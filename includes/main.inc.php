<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

set_include_path(get_include_path() . ':../libs:../includes/pear');
function __autoload($class_name) {
    if(file_exists("../libs/" . $class_name . ".class.inc.php")) {
        require_once $class_name . '.class.inc.php';
    }
}


include_once '../conf/settings.inc.php';

date_default_timezone_set(__TIMEZONE__);
$db = new db(__MYSQL_HOST__,__MYSQL_DATABASE__,__MYSQL_USER__,__MYSQL_PASSWORD__);


function error404($message = "") {
    errorBase($message, false, true, false);
}

function errorBase($message, $IsPretty, $Is404Page, $IsExpiredPage) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    if ($IsPretty)
        include("inc/header.inc.php");
    include("not_found.php");
    if ($IsPretty)
        include("inc/footer.inc.php");
    die();
}

function prettyError404($message = "") {
    errorBase($message, true, true, false);
}

function prettyErrorExpired($message = "") {
    errorBase($message, true, false, true);
}

?>

