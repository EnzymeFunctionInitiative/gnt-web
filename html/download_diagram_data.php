<?php
include_once '../includes/main.inc.php';
include_once '../libs/gnn.class.inc.php';


$isError = false;
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    $gnn = new gnn($db,$_GET['id']);
    if ($gnn->get_key() != $_GET['key']) {
        $isError = true;
    }
    elseif (time() < $gnn->get_time_completed() + settings::get_retention_days()) {
        $isError = true;
    }
}
else {
    $isError = true;
}

if ($isError) {
    error404();
}



$dbFile = $gnn->get_arrow_data_file();
if (!file_exists($dbFile))
    $dbFile = $gnn->get_arrow_data_file_legacy();
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

