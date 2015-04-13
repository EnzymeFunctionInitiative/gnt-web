<?php
require_once 'includes/main.inc.php';
$id = 0;
$key = 0;
$message = "";
$valid = 0;
$path = "/var/www/efi-gnt/html/test/";
if (isset($_GET)) {

        $valid = 1;
	$file_type = "";
        //Sets default % Co-Occurrence value if nothing was inputted.

        $cooccurrence = settings::get_default_cooccurrence();
        if ($_GET['cooccurrence'] != "") {
                $cooccurrence = (int)$_GET['cooccurrence'];
        }
	if (isset($_GET['ssn_file'])) {
        	$file_type = strtolower(pathinfo($_GET['ssn_file'],PATHINFO_EXTENSION));
	}
	
	elseif ($file_type != settings::get_valid_file_type()) {
                $valid = 0;
                $message .= "<br><b>Invalid filetype.  The file has to be an " . settings::get_valid_file_type() . " filetype.</b>";
        }

	if (!functions::verify_email($_GET['email'])) {
                $valid = 0;
                $message .= "<br><b>Please verify your email address</b>";
        }

	if ((!is_int($cooccurrence)) || ($cooccurrence > 100) || ($cooccurrence < 0)) {
                $valid = 0;
                $message .= "<br><b>Invalid % Co-Occurrence.  It must be an integer between 0 and 100.</b>";
        }

        if ($valid) {
                $id = gnn::test_create($db,$_GET['email'],$_GET['neighbor_size'],$path . $_GET['ssn_file'],basename($_GET['ssn_file']),$cooccurrence);
		$gnn = new gnn($db,$id);
		$key = $gnn->get_key();
		header("Location: stepb.php?id=" . $id . "&key=" . $key); 
        }

	echo $message;
}


?>
