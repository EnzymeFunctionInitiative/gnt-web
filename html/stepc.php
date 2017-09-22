<?php 
require_once '../includes/main.inc.php';
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
$idDataZip = $gnn->get_relative_cluster_data_zip_file();
$idDataZipFilesize = $gnn->get_cluster_data_zip_filesize();
$pfamDataZip = $gnn->get_relative_pfam_data_zip_file();
$pfamDataZipFilesize = $gnn->get_pfam_data_zip_filesize();
$warningFile = $gnn->get_relative_warning_file();
$warningFilesize = $gnn->get_warning_filesize();
$idTableFile = $gnn->get_relative_id_table_file();
$idTableFilesize = $gnn->get_id_table_filesize();
$pfamNoneZip = $gnn->get_relative_pfam_none_zip_file();
$pfamNoneZipFilesize = $gnn->get_pfam_none_zip_filesize();
$fastaZip = $gnn->get_relative_fasta_zip_file();
$fastaZipFilesize = $gnn->get_fasta_zip_filesize();

// Legacy jobs
$noMatchesFile = $gnn->get_relative_no_matches_file();
$noMatchesFilesize = $gnn->get_no_matches_filesize();
$noNeighborsFile = $gnn->get_relative_no_neighbors_file();
$noNeighborsFilesize = $gnn->get_no_neighbors_filesize();

?>

<hr>
    <img src="images/quest_stages_c.jpg" width="990" height="119" alt="stage 1">
    <hr>
    <h4>Network Information</h4>
    <table width="100%" border="1">
        <tr>
            <td>Job Number:</td><td><?php echo $gnnId; ?></td>
        </tr>
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
                <a href="<?php echo "$baseUrl/$ssnFile" ?>"><button>Download</button></a>
<?php if ($ssnZipFile) { ?>
                <a href="<?php echo "$baseUrl/$ssnZipFile"; ?>"><button>Download ZIP</button></a>
<?php } ?>
            </td>
            <td><?php echo number_format($gnn->get_ssn_nodes()); ?></td>
            <td><?php echo number_format($gnn->get_ssn_edges()); ?></td>
            <td><?php echo $ssnFilesize; ?>MB</td>
        </tr>
    </table>

    <p>&nbsp;</p>
    <div class="align_left">
    <h4>SSN Cluster Hub-Nodes: Genome Neighborhood Network (GNN)</h4>
    <p>Each hub-node in the network represents an SSN cluster that identified neighbors, with spoke-nodes for Pfam family with neighbors.</p>
    </div>

    <table width="100%" border="1">
        <th></th>
        <th>File Size (MB)</th>
        <tr style='text-align:center;'>
            <td>
                <a href="<?php echo "$baseUrl/$gnnFile"; ?>"><button>Download</button></a>
<?php if ($gnnZipFile) { ?>
                <a href="<?php echo "$baseUrl/$gnnZipFile"; ?>"><button>Download ZIP</button></a>
<?php } ?>
            </td>
            <td><?php echo $gnnFilesize; ?>MB</td>
        </tr>
    </table>
   
    <h4>Pfam Family Hub-Nodes Genome Neighborhood Network (GNN)</h4>
    <p>Each hub-node in the network represents a Pfam family of neighbors, with spoke-nodes for each SSN cluster that identified the Pfam family.</p>

    <table width="100%" border="1">
        <th></th>
        <th>File Size (MB)</th>
        <tr style='text-align:center;'>
            <td>
                <a href="<?php echo "$baseUrl/$pfamFile"; ?>"><button>Download</button></a>
<?php if ($pfamZipFile) { ?>
                <a href="<?php echo "$baseUrl/$pfamZipFile"; ?>"><button>Download ZIP</button></a>
<?php } ?>
            </td>
            <td><?php echo $pfamFilesize; ?> MB</td>
        </tr>
    </table>
 
    <h4>Other Files</h4>
    <table width="100%" border="1">
        <th></th>
        <th>File</th>
        <th>File Size (MB)</th>
<?php if ($idTableFile) { ?>
        <tr style='text-align:center;'>
            <td>
                <a href="<?php echo "$baseUrl/$idTableFile"; ?>"><button>Download</button></a>
            </td>
            <td>UniProt ID-Color-Cluster Number Mapping Table</td>
            <td><?php echo $idTableFilesize; ?> MB</td>
        </tr>
<?php } ?>
<?php if ($idDataZip) { ?>
        <tr style='text-align:center;'>
            <td>
                <a href="<?php echo "$baseUrl/$idDataZip"; ?>"><button>Download All (ZIP)</button></a>
            </td>
            <td>UniProt ID Lists per Cluster</td>
            <td><?php echo $idDataZipFilesize; ?> MB</td>
        </tr>
<?php } ?>
<?php if ($fastaZip) { ?>
        <tr style='text-align:center;'>
            <td>
                <a href="<?php echo "$baseUrl/$fastaZip"; ?>"><button>Download All (ZIP)</button></a>
            </td>
            <td>FASTA Files per Cluster</td>
            <td><?php echo $fastaZipFilesize; ?> MB</td>
        </tr>
<?php } ?>
<?php if ($pfamDataZip) { ?>
        <tr style='text-align:center;'>
            <td>
                <a href="<?php echo "$baseUrl/$pfamDataZip"; ?>"><button>Download All (ZIP)</button></a>
            </td>
            <td>PFAM Neighbor Mapping Tables</td>
            <td><?php echo $pfamDataZipFilesize; ?> MB</td>
        </tr>
<?php } ?>
<?php if ($pfamNoneZip) { ?>
        <tr style='text-align:center;'>
            <td>
                <a href="<?php echo "$baseUrl/$pfamNoneZip"; ?>"><button>Download All (ZIP)</button></a>
            </td>
            <td>Neighbors without PFAM assigned per Cluster</td>
            <td><?php echo $pfamNoneZipFilesize; ?> MB</td>
        </tr>
<?php } ?>
<?php if ($warningFile) { ?>
        <tr style='text-align:center;'>
            <td>
                <a href="<?php echo "$baseUrl/$warningFile"; ?>"><button>Download</button></a>
            </td>
            <td>No Matches/No Neighbors File</td>
            <td><?php echo $warningFilesize; ?> MB</td>
        </tr>
<?php } ?>
<?php if ($noMatchesFile) { ?>
        <tr style='text-align:center;'>
            <td>
                <a href="<?php echo "$baseUrl/$noMatchesFile"; ?>"><button>Download</button></a>
            </td>
            <td>No Matches</td>
            <td><?php echo $noMatchesFilesize; ?> MB</td>
        </tr>
<?php } ?>
<?php if ($noNeighborsFile) { ?>
        <tr style='text-align:center;'>
            <td>
                <a href="<?php echo "$baseUrl/$noNeighborsFile"; ?>"><button>Download</button></a>
            </td>
            <td>No Neighbors File</td>
            <td><?php echo $noNeighborsFilesize; ?> MB</td>
        </tr>
<?php } ?>
    </table>
    
    <hr>
    <h4 >Arrow Diagrams</h4>  

    Input a list of UniProt IDs or a cluster number from the GNN to generate arrow diagrams:<br>
    <div id="diagram-controls">
        <div><textarea rows=4 cols=20 id="id-input"></textarea></div>
        <div style="float:left"><button type="button" id="diagram-generate-button">Generate</button></div>
        <div id="progress-loader" style="display:inline-block;margin-left:15px;visibility:hidden" class="loader"></div>
        <div style="clear:both"><input type="checkbox" checked id="display-mode"> Align genes by coordinates</div>
    </div>

    <div id="arrow-container" style="width:900px;height:10px">
        <br>

        <svg id="arrow-canvas" style="width:100%;height:100%"></svg>
    </div>

    <hr>
    <?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?>  
    
    
  </div>

<?php if (settings::is_beta_release()) { ?>
    <div><center><h4><b><span style="color: red">BETA</span></b></h4></center></div>
<?php } ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/snap.svg/0.5.1/snap.svg-min.js" content-type="text/javascript"></script>
    <script src="js/arrows.js" content-type="text/javascript"></script>
    <script type="application/javascript">
        $(document).ready(function() {
            var popupIds = new PopupIds("info-popup", "info-popup-id", "info-popup-fam", "info-popup-coords",
                    "info-popup-seqlen", "info-popup-dir", "info-popup-num");
            var arrowDiagram = new ArrowDiagram("arrow-canvas", "display-mode", "arrow-container", popupIds, "id-input",
                                                "diagram-controls", "progress-loader");
            arrowDiagram.setJobInfo("<?php echo $gnnId; ?>", "<?php echo $gnnKey; ?>");

            $("#diagram-generate-button").click(function() {
                arrowDiagram.getArrowData();
            });

            $("#display-mode").click(function() {
                arrowDiagram.toggleDisplayMode();
            });
        });
    </script>

<div id="info-popup" style="position:absolute;padding:5px;background-color:#555;color:#fff;">
    <div id="info-popup-id">ID: <span class="popup-id"></span></div>
    <div id="info-popup-fam">Family: <span class="popup-pfam"></span></div>
    <div id="info-popup-coords">Coordinates: <span class="popup-pfam"></span></div>
    <div id="info-popup-seqlen">Sequence Length: <span class="popup-pfam"></span></div>
    <div id="info-popup-dir">Direction: <span class="popup-pfam"></span></div>
    <div id="info-popup-num">Gene Index: <span class="popup-pfam"></span></div>
</div>

<?php require_once('includes/footer.inc.php'); ?>

