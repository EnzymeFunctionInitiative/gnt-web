<?php 
require_once '../includes/main.inc.php';

$message = "";
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    $gnn = new gnn($db,$_GET['id']);
    if ($gnn->get_key() != $_GET['key']) {
        prettyError404();
    }
    elseif (time() < $gnn->get_time_completed() + settings::get_retention_days()) {
        prettyError404("That job has expired and doesn't exist anymore.");
    }
}
else {
    prettyError404();
}


$isLegacy = is_null($gnn->get_pbs_number());

$baseUrl = settings::get_web_address();
$gnnId = $gnn->get_id();
$gnnKey = $gnn->get_key();

$ssnFile = $gnn->get_relative_color_ssn();
$ssnZipFile = $gnn->get_relative_color_ssn_zip_file();
$ssnFilesize = $gnn->get_color_ssn_filesize();
$gnnFile = $gnn->get_relative_gnn();
$gnnZipFile = $gnn->get_relative_gnn_zip_file();
$gnnFilesize = $gnn->get_gnn_filesize();
$pfamFile = $gnn->get_relative_pfam_hub();
$pfamZipFile = $gnn->get_relative_pfam_hub_zip_file();
$pfamFilesize = $gnn->get_pfam_hub_filesize();
$pfamZipFilesize = $gnn->get_pfam_hub_zip_filesize();
$idDataZip = $gnn->get_relative_cluster_data_zip_file();
$idDataZipFilesize = $gnn->get_cluster_data_zip_filesize();
$pfamDataZip = $gnn->get_relative_pfam_data_zip_file();
$pfamDataZipFilesize = $gnn->get_pfam_hub_zip_filesize();
$warningFile = $gnn->get_relative_warning_file();
$warningFilesize = $gnn->get_warning_filesize();
$idTableFile = $gnn->get_relative_id_table_file();
$idTableFilesize = $gnn->get_id_table_filesize();
$pfamNoneZip = $gnn->get_relative_pfam_none_zip_file();
$pfamNoneZipFilesize = $gnn->get_pfam_none_zip_filesize();
$fastaZip = $gnn->get_relative_fasta_zip_file();
$fastaZipFilesize = $gnn->get_fasta_zip_filesize();
$coocTableFile = $gnn->get_relative_cooc_table_file();
$coocTableFilesize = $gnn->get_cooc_table_filesize();
$hubCountFile = $gnn->get_relative_hub_count_file();
$hubCountFilesize = $gnn->get_hub_count_filesize();
$hasDiagrams = $gnn->does_job_have_arrows();
$diagramFile = $gnn->get_relative_diagram_data_file();
$diagramZipFile = $gnn->get_relative_diagram_zip_file();
$diagramFileSize = $gnn->get_diagram_data_filesize();
$diagramZipFileSize = $gnn->get_diagram_zip_filesize();

// Legacy jobs
$noMatchesFile = $gnn->get_relative_no_matches_file();
$noMatchesFilesize = $gnn->get_no_matches_filesize();
$noNeighborsFile = $gnn->get_relative_no_neighbors_file();
$noNeighborsFilesize = $gnn->get_no_neighbors_filesize();



require_once('inc/header.inc.php'); 

?>

<div class="update_message">
    The GNT database has been updated to use UniProt
    <?php echo settings::get_uniprot_version(); ?> and ENA
    <?php echo settings::get_ena_version(); ?>.
</div>

<!--<hr>
    <img src="images/quest_stages_c.jpg" width="990" height="119" alt="stage 1">
    <hr> -->
    <h2 class="bold">Results</h2>

    <h3>Network Information</h3>
    <table width="100%" border="1" style="margin-top: 10px" class="pretty">
        <tbody>
            <tr class="deeper">
                <td>Job Number:</td><td><?php echo $gnnId; ?></td>
            </tr>
            <tr class="deeper">
                <td>Uploaded Filename:</td><td><?php echo $gnn->get_filename(); ?></td>
            </tr>
            <tr class="deeper">
                 <td>Neighborhood Size</td><td><?php echo $gnn->get_size(); ?></td>
            </tr>
            <tr class="deeper">
                <td>Input % Co-Occurrence</td><td><?php echo $gnn->get_cooccurrence(); ?>%</td>
            </tr>
        </tbody>
    </table>

    <h3>Colored Sequence Similarity Network (SSN)</h3>
    <p>The nodes in the input SSN are assigned unique cluster numbers and colors.</p>

    <table width="100%" border="1" class="pretty">
        <thead>
            <th></th>
            <th># Nodes</th>
            <th># Edges</th>
            <th>File Size (MB)</th>
        </thead>
        <tbody>
            <tr style='text-align:center;'>
                <td class="button-col">
                    <a href="<?php echo "$baseUrl/$ssnFile" ?>"><button class="light small">Download</button></a>
<?php if ($ssnZipFile) { ?>
                    <a href="<?php echo "$baseUrl/$ssnZipFile"; ?>"><button class="light small">Download ZIP</button></a>
<?php } ?>
                </td>
                <td><?php echo number_format($gnn->get_ssn_nodes()); ?></td>
                <td><?php echo number_format($gnn->get_ssn_edges()); ?></td>
                <td><?php echo $ssnFilesize; ?>MB</td>
            </tr>
        </tbody>
    </table>

    <h3>SSN Cluster Hub-Nodes: Genome Neighborhood Network (GNN)</h3>
    <p>Each hub-node in the network represents an SSN cluster that identified neighbors, with spoke-nodes for Pfam family with neighbors.</p>

    <table width="100%" border="1" class="pretty">
        <thead>
            <th></th>
            <th>File Size (MB)</th>
        </thead>
        <tbody>
            <tr style='text-align:center;'>
                <td class="button-col">
                    <a href="<?php echo "$baseUrl/$gnnFile"; ?>"><button class="light small">Download</button></a>
<?php if ($gnnZipFile) { ?>
                    <a href="<?php echo "$baseUrl/$gnnZipFile"; ?>"><button class="light small">Download ZIP</button></a>
<?php } ?>
                </td>
                <td><?php echo $gnnFilesize; ?>MB</td>
            </tr>
        </tbody>
    </table>

    <h3>Pfam Family Hub-Nodes Genome Neighborhood Network (GNN)</h3>
    <p>Each hub-node in the network represents a Pfam family of neighbors, with spoke-nodes for each SSN cluster that identified the Pfam family.</p>

    <table width="100%" border="1" class="pretty">
        <thead>
            <th></th>
            <th>File Size (Unzipped/Zipped MB)</th>
        </thead>
        <tbody>
            <tr style='text-align:center;'>
                <td class="button-col">
                    <a href="<?php echo "$baseUrl/$pfamFile"; ?>"><button class="light small">Download</button></a>
<?php if ($pfamZipFile) { ?>
                    <a href="<?php echo "$baseUrl/$pfamZipFile"; ?>"><button class="light small">Download ZIP</button></a>
<?php } ?>
                </td>
                <td>
                    <?php echo $pfamFilesize; ?> MB
<?php if ($pfamZipFilesize) { echo "/ $pfamZipFilesize MB"; } ?>
                </td>
            </tr>
        </tbody>
    </table>

<?php if ($hasDiagrams) { ?>
    <div style="color:red">
        <h3 style="color:red">Genome Neighborhood Diagrams</h3> 
        <div class="new_feature"></div>
        Genome neighboorhoods can be visualized in an arrow digram format in a new window.
    
        <table width="100%" border="1" class="pretty">
            <thead>
                <th>Action</th>
                <th></th>
                <th>File Size (Unzipped/Zipped MB)</th>
            </thead>
            <tbody>
                <tr style='text-align:center;'>
                    <td class="button-col">
                        <a href="view_diagrams.php?id=<?php echo $gnnId; ?>&key=<?php echo $gnnKey; ?>" target="_blank"><button class="light small">View diagrams</button></a>
                    </td>
                    <td colspan="2">
                        Opens arrow diagram explorer in a new tab or window.
                    </td>
                </tr>
                <tr style="text-align:center;">
                    <td class="button-col">
                        <a href="<?php echo "$baseUrl/$diagramFile"; ?>" target="_blank"><button class="light small">Download</button></a>
<?php if ($diagramZipFileSize) { ?>
                        <a href="<?php echo "$baseUrl/$diagramZipFile"; ?>" target="_blank"><button class="light small">Download ZIP</button></a>
<?php } ?>
                    </td>
                    <td>
                        Diagram data for later review
                    </td>
                    <td>
                        <?php echo $diagramFileSize; ?> MB
<?php if ($diagramZipFileSize) { echo "/ $diagramZipFileSize MB"; } ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
<?php } else { ?>
    <div style="color:red">
        <br><br>
        Since this job was run before the neighborhood diagrams were supported, there are no
        genome neighborhood diagrams available.
    </div>
<?php } ?>

    <h3>Other Files</h3>
    <table width="100%" border="1" class="pretty">
        <thead>
            <th></th>
            <th>File</th>
            <th>File Size (MB)</th>
        </thead>
        <tbody>
<?php if ($idTableFile) { ?>
            <tr style='text-align:center;'>
                <td class="button-col">
                    <a href="<?php echo "$baseUrl/$idTableFile"; ?>"><button class="light small">Download</button></a>
                </td>
                <td>UniProt ID-Color-Cluster Number Mapping Table</td>
                <td><?php echo $idTableFilesize; ?> MB</td>
            </tr>
<?php } ?>
<?php if ($idDataZip) { ?>
            <tr style='text-align:center;'>
                <td>
                    <a href="<?php echo "$baseUrl/$idDataZip"; ?>"><button class="light small">Download All (ZIP)</button></a>
                </td>
                <td>UniProt ID Lists per Cluster</td>
                <td><?php echo $idDataZipFilesize; ?> MB</td>
            </tr>
<?php } ?>
<?php if ($fastaZip) { ?>
            <tr style='text-align:center;'>
                <td>
                    <a href="<?php echo "$baseUrl/$fastaZip"; ?>"><button class="light small">Download All (ZIP)</button></a>
                </td>
                <td>FASTA Files per Cluster</td>
                <td><?php echo $fastaZipFilesize; ?> MB</td>
            </tr>
<?php } ?>
<?php if ($pfamDataZip) { ?>
            <tr style='text-align:center;'>
                <td>
                    <a href="<?php echo "$baseUrl/$pfamDataZip"; ?>"><button class="light small">Download All (ZIP)</button></a>
                </td>
                <td>PFAM Neighbor Mapping Tables</td>
                <td><?php echo $pfamDataZipFilesize; ?> MB</td>
            </tr>
<?php } ?>
<?php if ($pfamNoneZip) { ?>
            <tr style='text-align:center;'>
                <td>
                    <a href="<?php echo "$baseUrl/$pfamNoneZip"; ?>"><button class="light small">Download All (ZIP)</button></a>
                </td>
                <td>Neighbors without PFAM assigned per Cluster</td>
                <td><?php echo $pfamNoneZipFilesize; ?> MB</td>
            </tr>
<?php } ?>
<?php if ($warningFile) { ?>
            <tr style='text-align:center;'>
                <td>
                    <a href="<?php echo "$baseUrl/$warningFile"; ?>"><button class="light small">Download</button></a>
                </td>
                <td>No Matches/No Neighbors File</td>
                <td><?php echo $warningFilesize; ?> MB</td>
            </tr>
<?php } ?>
<?php if ($noMatchesFile) { ?>
            <tr style='text-align:center;'>
                <td>
                    <a href="<?php echo "$baseUrl/$noMatchesFile"; ?>"><button class="light small">Download</button></a>
                </td>
                <td>No Matches</td>
                <td><?php echo $noMatchesFilesize; ?> MB</td>
            </tr>
<?php } ?>
<?php if ($noNeighborsFile) { ?>
            <tr style='text-align:center;'>
                <td>
                    <a href="<?php echo "$baseUrl/$noNeighborsFile"; ?>"><button class="light small">Download</button></a>
                </td>
                <td>No Neighbors File</td>
                <td><?php echo $noNeighborsFilesize; ?> MB</td>
            </tr>
<?php } ?>
<?php if ($coocTableFilesize) { ?>
            <tr style='text-align:center;'>
                <td>
                    <a href="<?php echo "$baseUrl/$coocTableFile"; ?>"><button class="light small">Download</button></a>
                </td>
                <td>Pfam Family/Cluster Cooccurrence Table File</td>
                <td><?php echo $coocTableFilesize; ?> MB</td>
            </tr>
<?php } ?>
<?php if ($hubCountFilesize) { ?>
            <tr style='text-align:center;'>
                <td>
                    <a href="<?php echo "$baseUrl/$hubCountFile"; ?>"><button class="light small">Download</button></a>
                </td>
                <td>GNN Hub Cluster Sequence Count File</td>
                <td><?php echo $hubCountFilesize; ?> MB</td>
            </tr>
<?php } ?>
        </tbody>
    </table>

    <?php if (isset($message)) { echo "<h3 class='center'>" . $message . "</h3>"; } ?>  


<?php if (settings::is_beta_release()) { ?>
    <div><center><h4><b><span style="color: red">BETA</span></b></h4></center></div>
<?php } ?>

<?php require_once('inc/footer.inc.php'); ?>

