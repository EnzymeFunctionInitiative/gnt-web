<?php
require_once 'includes/main.inc.php';
$id = 0;
$key = 0;
$message = "";
$valid = 0;

if (isset($_POST['submit'])) {

    $valid = 1;
    $file_type = "";
    //Sets default % Co-Occurrence value if nothing was inputted.

    $cooccurrence = settings::get_default_cooccurrence();
    if ($_POST['cooccurrence'] != "") {
        $cooccurrence = (int)$_POST['cooccurrence'];
    }
    if (isset($_FILES['ssn_file'])) {
        $file_type = strtolower(pathinfo($_FILES['ssn_file']['name'],PATHINFO_EXTENSION));
    }

    if (!isset($_FILES['ssn_file'])) {
        $valid = 0;
        $message .= "<br><b>Please select a file to upload</b>";
    }
    elseif (isset($_FILES['ssn_file']['error']) && ($_FILES['ssn_file']['error'] != 0)) {
        $valid = 0;
        $message .= "<br><b>Error uploading file: " . functions::get_upload_error($_FILES['ssn_file']['error']) . "</b>";
    }
    elseif (!settings::is_valid_file_type($file_type)) {
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

    $useNewNeighborMethod = 0;
    if ($_POST['newneighbormethod'] == "true") {
        $useNewNeighborMethod = 1;
    }

    if ($valid) {
        error_log($useNewNeighborMethod);
        $id = gnn::create($db,$_POST['email'],$_POST['neighbor_size'],$_FILES['ssn_file']['tmp_name'],$_FILES['ssn_file']['name'],$cooccurrence,$useNewNeighborMethod);
        $gnn = new gnn($db,$id);
        $key = $gnn->get_key();
    }

}

echo json_encode(array('valid'=>$valid,
    'id'=>$id,
    'key'=>$key,
    'message'=>$message));

?>
