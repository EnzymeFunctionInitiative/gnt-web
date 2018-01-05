<?php
require_once '../includes/main.inc.php';
$id = 0;
$key = 0;
$message = "";
$valid = 0;
$cookieInfo = "";

if (isset($_POST['submit'])) {

    $valid = 1;
    $file_type = "";
    //Sets default % Co-Occurrence value if nothing was inputted.

    $cooccurrence = settings::get_default_cooccurrence();
    if ($_POST['cooccurrence'] != "") {
        $cooccurrence = (int)$_POST['cooccurrence'];
    }
    if (isset($_FILES['file'])) {
        $file_type = strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
    }

    if (!isset($_FILES['file'])) {
        $valid = 0;
        $message .= "<br><b>Please select a file to upload</b>";
    }
    elseif (isset($_FILES['file']['error']) && ($_FILES['file']['error'] != 0)) {
        $valid = 0;
        $message .= "<br><b>Error uploading file: " . functions::get_upload_error($_FILES['file']['error']) . "</b>" . $_POST['MAX_FILE_SIZE'];
    }
    elseif (!functions::is_valid_file_type($file_type)) {
        $valid = 0;
        $message .= "<br><b>Invalid filetype ($file_type).  The file has to be an " . settings::get_valid_file_types() . " filetype.</b>";
    }

    if (!functions::verify_email($_POST['email'])) {
        $valid = 0;
        $message .= "<br><b>Please verify your email address</b>";
    }

    if ((!is_int($cooccurrence)) || ($cooccurrence > 100) || ($cooccurrence < 0)) {
        $valid = 0;
        $message .= "<br><b>Invalid % Co-Occurrence.  It must be an integer between 0 and 100.</b>";
    }

    $email = $_POST['email'];

    if ($valid) {
        $gnnInfo = gnn::create2($db, $email, $_POST['neighbor_size'], $cooccurrence, $_FILES['file']['tmp_name'], $_FILES['file']['name']);
        if ($gnnInfo === false) {
            $valid = false;
        } else {
            $id = $gnnInfo['id'];
            $key = $gnnInfo['key'];
        }
    }
}

// This resets the expiration date of the cookie so that frequent users don't have to login in every X days as long
// as they keep using the app.
if ($valid && settings::is_recent_jobs_enabled() && user_jobs::has_token_cookie()) {
    $cookieInfo = user_jobs::get_cookie_shared(user_jobs::get_user_token());
    $returnData["cookieInfo"] = $cookieInfo;
}

echo json_encode(array(
    'valid' => $valid,
    'id' => $id,
    'key' => $key,
    'message' => $message,
    'cookieInfo' => $cookieInfo
));

?>
