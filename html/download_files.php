<?php
require_once "../includes/main.inc.php";
require_once "../libs/gnn.class.inc.php";
require_once "../libs/diagram_data_file.class.inc.php";
require_once "../libs/diagram_jobs.class.inc.php";


$isError = false;
$dbFile = "";
$arrows = NULL;
$theId = "";

if (isset($_GET["gnn-id"]) && is_numeric($_GET["gnn-id"])) {
    $theId = $_GET["gnn-id"];
    $gnn = new gnn($db, $theId);

    if ($gnn->get_key() != $_GET["key"]) {
        $isError = true;
    }
    elseif (time() < $gnn->get_time_completed() + settings::get_retention_days()) {
        $isError = true;
    }

    $dbFile = $gnn->get_diagram_data_file();
    if (!file_exists($dbFile))
        $dbFile = $gnn->get_diagram_data_file_legacy();
}
elseif (isset($_GET["direct-id"]) && is_numeric($_GET["direct-id"])) {
    $theId = $_GET["direct-id"];
    $arrows = getArrowDb($db, $theId);
    $dbFile = $arrows->get_diagram_data_file();
}
else {
    $isError = true;
}

if ($isError) {
    error404();
}

if (isset($_GET["type"])) {
    $type = $_GET["type"];

    if ($type == "data-file") {
        $downloadFilename = pathinfo($dbFile, PATHINFO_FILENAME) . ".sqlite";
        $contentSize = filesize($dbFile);
        sendHeaders($downloadFilename, $contentSize);
        readfile($dbFile);
        exit(0);
    } elseif ($arrows === NULL) {
        $isError = true;
    } elseif ($type == "uniprot") {
        $gnnName = $arrows->get_name();
        $downloadFilename = "${theId}_${gnnName}_UniProt_IDs.txt";
        $ids = $arrows->get_uniprot_ids();
        $content = "UniProt ID\tQuery ID\n";
        foreach ($ids as $upId => $otherId) {
            $content .= "$upId\t$otherId\n";
        }
        #$content = implode("\n", $ids);
        sendHeaders($downloadFilename, strlen($content));
        print $content;
        exit(0);
    } elseif ($type == "unmatched") {
        $gnnName = $arrows->get_name();
        $downloadFilename = "${theId}_${gnnName}_Unmatched_IDs.txt";
        $ids = $arrows->get_unmatched_ids();
        $content = implode("\n", $ids);
        sendHeaders($downloadFilename, strlen($content));
        print $content;
        exit(0);
    } elseif ($type == "blast") {
        $gnnName = $arrows->get_name();
        $downloadFilename = "${theId}_${gnnName}_BLAST_Sequence.txt";
        $content = $arrows->get_blast_sequence();
        sendHeaders($downloadFilename, strlen($content));
        print $content;
        exit(0);
    } else {
        $isError = true;
    }
}

if ($isError) {
    error404();
}





function sendHeaders($downloadFilename, $contentSize) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Connection: Keep-Alive');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . $contentSize);
    ob_clean();
}







function getArrowDb($db, $theId) {
    $arrows = new diagram_data_file($theId);
    $key = diagram_jobs::get_key($db, $theId);
    $timeCompleted = diagram_jobs::get_time_completed($db, $theId);

    if ($key != $_GET["key"]) {
        $isError = true;
    }
    elseif (!$arrows->is_loaded()) {
        $isError = true;
    }
    elseif ($timeCompleted === false || time() < $timeCompleted + settings::get_retention_days()) {
        $isError = true;
    }

    return $arrows;
}

?>

