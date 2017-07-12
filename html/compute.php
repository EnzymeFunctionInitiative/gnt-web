<?php
require_once '../includes/main.inc.php';
$message = "";
$valid = 0;
$id = 0;
$key = 0;
if ((isset($_POST['id'])) && (is_numeric($_POST['id']))) {
        $gnn = new gnn($db,$_POST['id']);
        if ($gnn->get_key() != $_POST['key']) {
                $message .= "<br><b>No EFI-GNN Selected. Please go <a href='index.php'>back</a></b>";
		$valid = 0;
        }
        else {
		$id = $_POST['id'];
		$key = $_POST['key'];
                //have timeout.  if web browser crashes and you open it up an hour later, it won't try to run the job again.
                if ($gnn->get_time_created() + settings::get_timeout() < time()) {
                        $result = $gnn->run_gnn();
                        if ($result['RESULT']) {
                                $gnn->email_complete();
				$message = "<b>EFI-GNN successfully created</b>";
				$valid = 1;

                        }
			else {
				$message = "<h4 class='center'>Error: " . $result['OUTPUT'];
				$message .= "<p>Please go <a href='index.php'>back</a></h4>";
				$valid = 0;
			}
                }
		else {
                	$message .= "<b>Error running GNN</b>";
			$valid = 0;
		}

        }

}
else {
                $message .= "<br><b>No EFI-GNN Selected. Please go <a href='index.php'>back</a></b>";
		$valid = 0;
}

error_log("Valid: " . $valid);
error_log("Message: " . $message);
echo json_encode(array('valid'=>$valid,
                        'id'=>$id,
                        'key'=>$key,
                        'message'=>$message));

?>
