<?php
require_once '../includes/main.inc.php';
require_once '../libs/diagram_jobs.class.inc.php';

$id = 0;
$key = 0;
$message = "";
$valid = 0;
$cookieInfo = "";

if (isset($_POST['submit'])) {

    $valid = 1;
    $file_type = "";

    if (isset($_FILES['file'])) {
        $file_type = strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
    }

    if (!isset($_FILES['file'])) {
        $valid = 0;
        $message .= "<br><b>Please select a file to upload</b>";
    }
    elseif (isset($_FILES['file']['error']) && ($_FILES['file']['error'] != 0)) {
        $valid = 0;
        $message .= "<br><b>Error uploading file: " . functions::get_upload_error($_FILES['file']['error']) . "</b>";
    }
    elseif (!functions::is_valid_diagram_file_type($file_type)) {
        $valid = 0;
        $message .= "<br><b>Invalid filetype ($file_type).  The file has to be an " . settings::get_valid_diagram_file_types() . " filetype.</b>";
    }

    if (!functions::verify_email($_POST['email'])) {
        $valid = 0;
        $message .= "<br><b>Please verify your email address</b>";
    }

    $email = $_POST['email'];

    if ($valid) {
        $arrowInfo = diagram_jobs::create_file($db, $email, $_FILES['file']['tmp_name'], $_FILES['file']['name']);
        if ($arrowInfo === false) {
            $valid = false;
        } else {
            $id = $arrowInfo['id'];
            $key = $arrowInfo['key'];

            $userObj = new user_jobs();
            $userObj->save_user($db, $email);
            $cookieInfo = $userObj->get_cookie();
        }
    }
}

echo json_encode(array(
    'valid' => $valid,
    'id' => $id,
    'key' => $key,
    'message' => $message,
    'cookieInfo' => $cookieInfo
));

?>
