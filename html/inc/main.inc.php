<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

set_include_path(get_include_path() . ':../libs:includes/pear/');
include_once '../conf/settings.inc.php';
function __autoload($class_name) {
    if(file_exists("../libs/" . $class_name . ".class.inc.php")) {
        require_once $class_name . '.class.inc.php';
    }
}
date_default_timezone_set(__TIMEZONE__);
$db = new db(__MYSQL_HOST__,__MYSQL_DATABASE__,__MYSQL_USER__,__MYSQL_PASSWORD__);
?>

