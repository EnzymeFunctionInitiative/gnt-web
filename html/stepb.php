<?php 

require_once "../includes/main.inc.php";
require_once "../libs/diagram_jobs.class.inc.php";


if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['key'])) {
    error404();
} else if (!isset($_GET['diagram'])) {
    $gnn = new gnn($db,$_GET['id']);
    if ($gnn->get_key() != $_GET['key']) {
        error404();
    }
} else {
    $key = diagram_jobs::get_key($db, $_GET['id']);
    if ($key != $_GET['key']) {
        error404();
    }
}

$isDiagram = isset($_GET['diagram']) && $_GET['diagram'];

require_once('inc/header.inc.php'); 

?>


<!--<img src="images/quest_stages_b.jpg" width="990" height="119" alt="stage B">-->

<hr>

<?php if (!$isDiagram) { ?>
<h2 class="bold">Completing Generation of GNN </h2>
<p>&nbsp;</p>
<p>An email will be sent when your GNN generation is complete.</p>
<?php } else { ?>
<h2 class="bold">Diagram is Being Processed</h2>
<p>&nbsp;</p>
<p>An email will be sent when your diagram is ready to view.</p>
<?php } ?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p></p>
<p>&nbsp;</p>


<?php include_once 'inc/footer.inc.php'; ?>


