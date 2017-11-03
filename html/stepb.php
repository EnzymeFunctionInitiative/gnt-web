<?php 
require_once '../includes/main.inc.php';
require '../libs/user_jobs.class.inc.php';

$message = "<br><b>No EFI-GNN Selected. Please go <a href='index.php'>back</a></b>";
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    $gnn = new gnn($db,$_GET['id']);
    if ($gnn->get_key() == $_GET['key']) {
        $message = "";

//        $userObj = new user_jobs();
//        $userObj->save_user($db, $_GET['id']);
    }
}

require_once('includes/header.inc.php'); 

?>


<img src="images/quest_stages_b.jpg" width="990" height="119" alt="stage B">
   <hr>

    <h3>Completing Generation of GNN </h3>
    <p>&nbsp;</p>
    <p>An email will be sent when your GNN generation is complete.</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p></p>
    <p>&nbsp;</p>
  </div>

  <div class="clear"></div>
</div>


<?php include_once 'includes/footer.inc.php'; ?>


