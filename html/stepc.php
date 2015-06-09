<?php 
require_once 'includes/main.inc.php';
require_once('includes/header.inc.php'); 

$message = "";
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
        $gnn = new gnn($db,$_GET['id']);
        if ($gnn->get_key() != $_GET['key']) {
                echo "<br><b><h4 class='center'>No EFI-GNN Selected. Please go <a href='index.php'>back</a></h4></b>";
		exit;
        }
	elseif (time() < $gnn->get_time_completed() + settings::get_retention_days()) {
                echo "Your job results are only retained for a period of " . settings::get_retention_days();
                echo "<br>Please go back to the <a href='" . settings::get_web_root() . "'>homepage</a>";
                exit;
        }

        else {

        }

}
else {
                echo "<br><b><h4 class='center'>No EFI-GNN Selected. Please go <a href='index.php'>back</a></h4></b>";
		exit;
}

?>

<hr>
	<img src="images/quest_stages_c.jpg" width="990" height="119" alt="stage 1">
	<hr>
	<h4>Network Information</h4>
	<table width="100%" border="1">
        <tr>
		<td>Neighborhood Size</td><td><?php echo $gnn->get_size(); ?></td>
        </tr>
	<tr>
		<td>Input % Co-Occurrence</td><td><?php echo $gnn->get_cooccurrence(); ?>%</td>
	</tr>
	</table>

   <hr>
	<h4>Colored Sequence Similarity Network (SSN)</h4>
	<p>Each node in the network is a single protein from the data set. Large files (&gt;500MB) may not open.</p>

    <table width="100%" border="1">
    <th></th>
    <th># Nodes</th>
    <th># Edges</th>
    <th>File Size (MB)</th>
    <tr style='text-align:center;'>
    <td>
	<a href='<?php echo settings::get_web_address() . $gnn->get_relative_color_ssn(); ?>'>
	<button>Download</button></a></td>
    <td><?php echo $gnn->get_ssn_nodes(); ?></td>
    <td><?php echo $gnn->get_ssn_edges(); ?></td>
    <td><?php echo $gnn->get_color_ssn_filesize(); ?>MB</td>
    </tr>
    </table>

	<p>&nbsp;</p>
    <div class="align_left">
    <h4>Genome Neighborhood Network (GNN)</h4>
	<p>Each hub node in the network represents a protein family (PFAM) of neighbors and each spoke node represents a collection of sequences from the original SSN.</p>
    </div>
	    <table width="100%" border="1">
    <th></th>
    <th># Pfams</th>
    <th># Nodes</th>
    <th># Edges</th>
    <th>File Size (MB)</th>
    <tr style='text-align:center;'>
	    <td>
        <a href='<?php echo settings::get_web_address() . $gnn->get_relative_gnn(); ?>'>
        <button>Download</button></a></td>
    <td><?php echo $gnn->get_gnn_pfams(); ?></td>
    <td><?php echo $gnn->get_gnn_nodes(); ?></td>
    <td><?php echo $gnn->get_gnn_edges(); ?></td>
    <td><?php echo $gnn->get_gnn_filesize(); ?>MB</td>
    </tr>
    </table>
    
	<h4>Stats</h4>
        <p>Download Stats File: Tabular output of (1) Cluster Number, (2) Neighbor Pfam ID, (3) Neighbor Pfam Name, (4) Cluster Fraction, and (5) Average Gene Distance</p>
    </div>
            <table width="100%" border="1">
    <th></th>
    <th>File Size (MB)</th>
    <tr style='text-align:center;'>
            <td>
        <a href='<?php echo settings::get_web_address() . $gnn->get_relative_stats(); ?>'>
        <button>Download</button></a></td>
    <td><?php echo $gnn->get_stats_filesize(); ?>MB</td>
    </tr>
    </table>
    
    <hr>
	<?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?>  
    
    
  </div>
  

  <?php require_once('includes/footer.inc.php'); ?>

