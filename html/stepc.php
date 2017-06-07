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
		<td>Uploaded Filename:</td><td><?php echo $gnn->get_filename(); ?></td>
	</tr>
        <tr>
		<td>Neighborhood Size</td><td><?php echo $gnn->get_size(); ?></td>
        </tr>
	<tr>
		<td>Input % Co-Occurrence</td><td><?php echo $gnn->get_cooccurrence(); ?>%</td>
	</tr>
	</table>

   <hr>
	<h4>Colored Sequence Similarity Network (SSN)</h4>
	<p>The nodes in the input SSN are assigned unique cluster numbers and colors.</p>

    <table width="100%" border="1">
    <th></th>
    <th># Nodes</th>
    <th># Edges</th>
    <th>File Size (MB)</th>
    <tr style='text-align:center;'>
    <td>
	<a href='<?php echo settings::get_web_address() . $gnn->get_relative_color_ssn(); ?>'>
	<button>Download</button></a></td>
    <td><?php echo number_format($gnn->get_ssn_nodes()); ?></td>
    <td><?php echo number_format($gnn->get_ssn_edges()); ?></td>
    <td><?php echo $gnn->get_color_ssn_filesize(); ?>MB</td>
    </tr>
    </table>

	<p>&nbsp;</p>
    <div class="align_left">
    <h4>Genome Neighborhood Network (GNN): SSN Cluster Hub-Nodes</h4>
	<p>Each hub-node in the network represents an SSN cluster that identified neighbors, with spoke-nodes for Pfam family with neighbors.</p>
    </div>
	    <table width="100%" border="1">
    <th></th>
    <th>File Size (MB)</th>
    <tr style='text-align:center;'>
	    <td>
        <a href='<?php echo settings::get_web_address() . $gnn->get_relative_gnn(); ?>'>
        <button>Download</button></a></td>
    <td><?php echo $gnn->get_gnn_filesize(); ?>MB</td>
    </tr>
    </table>
   
    <h4>Genome Neighborhood Network (GNN): Pfam Family Hub-Nodes</h4>
	<p>Each hub-node in the network represents a Pfam family of neighbors, with spoke-nodes for each SSN cluster that identified the Pfam family.</p>
    </div>
            <table width="100%" border="1">
    <th></th>
    <th>File Size (MB)</th>
    <tr style='text-align:center;'>
            <td>
        <a href='<?php echo settings::get_web_address() . $gnn->get_relative_pfam_hub(); ?>'>
        <button>Download</button></a></td>
    <td><?php echo $gnn->get_pfam_hub_filesize(); ?>MB</td>
    </tr>
    </table>
 
	<h4>Other Files</h4>
        <p>As described in the tutorial, additional files will soon be available</p>
    </div>
            <table width="100%" border="1">
    <th></th>
    <th>File</th>
    <th>File Size (MB)</th>
       <tr style='text-align:center;'>
        <td>
        <a href='<?php echo settings::get_web_address() . $gnn->get_relative_no_matches(); ?>'>
        <button>Download</button></a></td>
        <td>No Matches File</td>
    <td><?php echo $gnn->get_no_matches_filesize(); ?>MB</td>
    </tr>
    <tr style='text-align:center;'>
        <td>
        <a href='<?php echo settings::get_web_address() . $gnn->get_relative_no_neighbors(); ?>'>
        <button>Download</button></a></td>
        <td>No Neighbors File</td>
    <td><?php echo $gnn->get_no_neighbors_filesize(); ?>MB</td>
    </tr>


    </table>
    
    <hr>
	<?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?>  
    
    
  </div>
  

  <?php require_once('includes/footer.inc.php'); ?>

