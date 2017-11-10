<?php
require_once "../includes/main.inc.php";

$isError = false;
if (strpos($_SERVER['HTTP_REFERER'], "view_diagrams.php") === FALSE || !isset($_POST["svg"])) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    exit;
}


$type = "svg";
if (isset($_POST["type"]) && $_POST["type"] == "png")
    $type = "png";

$filename = "image";
if (isset($_POST["name"]) && strlen($_POST["name"]) > 3)
    $filename = $_POST["name"];

$filename .= "." . $type;

$svg = $_POST["svg"];
$svg = rawurldecode($svg);

if ($type == "svg") {
    header('Content-type: image/svg+xml');
    header('Content-Disposition: attachment; filename="' . $filename . '"'); 
    print $svg;
} elseif ($type == "png") {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}


?>

