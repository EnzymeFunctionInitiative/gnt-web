<?php
require_once '../includes/main.inc.php';
$id = 0;
$key = 0;
$message = "";
$valid = 0;

if (isset($_FILES['diagram_file'])) {
    $valid = 1;
    $file_type = "";
    
    $file_type = strtolower(pathinfo($_FILES['ssn_file']['name'],PATHINFO_EXTENSION));
    if (isset($_FILES['ssn_file']['error']) && ($_FILES['ssn_file']['error'] != 0)) {
        $valid = 0;
        $message .= "<br><b>Error uploading file: " . functions::get_upload_error($_FILES['ssn_file']['error']) . "</b>";
    }
    elseif (!settings::is_valid_diagram_file_type($file_type)) {
        $valid = 0;
        $message .= "<br><b>Invalid filetype ($file_type).  The file has to be an " . settings::get_valid_diagram_file_types() . " filetype.</b>";
    }
    
    if ($valid) {
        $id = gnn::create($db,$_POST['email'],$_POST['neighbor_size'],$_FILES['ssn_file']['tmp_name'],$_FILES['ssn_file']['name'],$cooccurrence);
        $gnn = new gnn($db,$id);
        $key = $gnn->get_key();
    }
} else {
    $valid = 0;
    $message = "No file provided.";
}


echo json_encode(array('valid'=>$valid,
    'id'=>$id,
    'key'=>$key,
    'message'=>$message));

?>
