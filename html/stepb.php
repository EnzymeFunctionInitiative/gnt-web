<?php 
require_once 'includes/main.inc.php';
require_once('includes/header.inc.php'); 

$message = "<br><b>No EFI-GNN Selected. Please go <a href='index.php'>back</a></b>";
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
        $gnn = new gnn($db,$_GET['id']);
        if ($gnn->get_key() == $_GET['key']) {
                $message = "";
        }

}

?>

<hr>
        <img src="images/quest_stages_b.jpg" width="990" height="119" alt="stage 1">
   <hr>
		<h4 class='center'><strong class='blue'>Your GNN is being generated.</strong></h4>
        	<h4 class="center"><strong class="blue">This page will refresh automatically.</strong></h4>
		<h4 class="center"><strong class="blue">Do not close this page or the processing will stop.</strong></h4>
	<h4 class='center'><progress id='progress_bar'></progress></h4>
	<div id='message'><?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?></div>


  <?php require_once('includes/footer.inc.php'); ?>

<script>
window.onload = computeGNN(<?php echo "'" . $_GET['id'] . "','" . $_GET['key'] . "'"; ?>);
</script>
