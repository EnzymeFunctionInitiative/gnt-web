<?php
require_once "../includes/main.inc.php";
require_once "../libs/gnn.class.inc.php";
require_once "../libs/diagram_data_file.class.inc.php";
require_once "../libs/diagram_jobs.class.inc.php";


$isError = false;
$dbFile = "";

if ((isset($_GET["gnn-id"])) && (is_numeric($_GET["gnn-id"]))) {
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
elseif ((isset($_GET["direct-id"])) && (is_numeric($_GET["direct-id"]))) {
    $theId = $_GET["direct-id"];
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

    $dbFile = $arrows->get_diagram_data_file();
}
else {
    $isError = true;
}

if ($isError) {
    error404();
}



$downloadFilename = pathinfo($dbFile, PATHINFO_FILENAME) . ".sqlite";


header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
header('Content-Transfer-Encoding: binary');
header('Connection: Keep-Alive');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($dbFile));
ob_clean();
readfile($dbFile);

?>

