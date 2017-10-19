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
$coocTableFile = $gnn->get_relative_cooc_table_file();
$coocTableFilesize = $gnn->get_cooc_table_filesize();
$hubCountFile = $gnn->get_relative_hub_count_file();
$hubCountFilesize = $gnn->get_hub_count_filesize();

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
<?php if ($coocTableFile) { ?>
        <tr style='text-align:center;'>
            <td>
                <a href="<?php echo "$baseUrl/$coocTableFile"; ?>"><button>Download</button></a>
            </td>
            <td>Pfam Family/Cluster Cooccurrence Table File</td>
            <td><?php echo $coocTableFilesize; ?> MB</td>
        </tr>
<?php } ?>
<?php if ($hubCountFile) { ?>
        <tr style='text-align:center;'>
            <td>
                <a href="<?php echo "$baseUrl/$hubCountFile"; ?>"><button>Download</button></a>
            </td>
            <td>GNN Hub Cluster Sequence Count File</td>
            <td><?php echo $hubCountFilesize; ?> MB</td>
        </tr>
<?php } ?>
    </table>
    
    <hr>
    <h4 >Arrow Diagrams</h4>  

    <a href="diagrams.php?id=<?php echo $gnnId; ?>&key=<?php echo $gnnKey; ?>" target="_blank">View arrow diagrams in a new window</a>
<!--
    <div id="diagram-controls">
        <div style="float:left">
            <div style="width:200px">
                <div style="font-size: 90%">Input a list of UniProt IDs or a cluster number from the GNN to generate arrow diagrams:</div>
                <textarea rows=4 cols=20 id="id-input"></textarea>
            </div>
            <div style="float:left"><button type="button" id="diagram-generate-button">Generate</button></div>
            <div id="progress-loader" style="display:inline-block;margin-left:15px;visibility:hidden" class="loader"></div>
        </div>
        <div style="float:left;margin-left:40px" id="pfam-filter-container">
            <div style="font-size:90%">Select one or more Pfam families to highlight:</div>
            <select multiple="multiple" id="pfam-filter" name="pfam-filter[]">
            </select>
        </div>
        <div style="float:left;margin-left:40px" id="display-control-container"><input type="checkbox" checked id="display-mode"> Align genes by coordinates</div>
        <div style="clear:both"></div>
    </div>

    <div id="arrow-container" style="width:900px;height:10px">
        <br>

        <svg id="arrow-canvas" style="width:100%;height:100%"></svg>
        <div style="text-align:right"><button type="button" id="arrow-all">Show All</button>  <button type="button" id="arrow-more">Show More</button></div>
    </div>
-->
    <hr>
    <?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?>  
    
    
  </div>

<?php if (settings::is_beta_release()) { ?>
    <div><center><h4><b><span style="color: red">BETA</span></b></h4></center></div>
<?php } ?>
<!--
    <script src="https://cdnjs.cloudflare.com/ajax/libs/snap.svg/0.5.1/snap.svg-min.js" content-type="text/javascript"></script>
    <script src="js/arrows.js" content-type="text/javascript"></script>
    <script type="application/javascript">
        $(document).ready(function() {
            var popupIds = {
                "ParentId": "info-popup",
                "IdId": "info-popup-id",
                "FamilyId": "info-popup-fam",
                "FamilyDescId": "info-popup-fam-desc",
                "SpTrId": "info-popup-sptr",
                "SeqLenId": "info-popup-seqlen",
                "DescId": "info-popup-desc",
            };

            $("#display-control-container").hide();
            $("#pfam-filter-container").hide();

            var inputObj = document.getElementById("id-input");
            var progressObj = $("#progress-loader");
            var controlObj = $("#diagram-controls  button,textarea,input");
            var arrows = new ArrowDiagram("arrow-canvas", "display-mode", "arrow-container", popupIds);
            arrows.setJobInfo("<?php echo $gnnId; ?>", "<?php echo $gnnKey; ?>");

            $("#pfam-filter").multiSelect({
                selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='enter Pfam'>",
                selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='enter Pfam'>",
                afterInit: function(ms){
                    var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';
                
                    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                        .on('keydown', function(e){
                             if (e.which === 40){
                                 that.$selectableUl.focus();
                                 return false;
                             }
                        });
                
                    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function(e){
                      if (e.which == 40){
                        that.$selectionUl.focus();
                        return false;
                      }
                    });
                }, 
                afterSelect: function(values) {
                    this.qs1.cache();
                    this.qs2.cache();
                    arrows.addPfamFilter(values);
                },
                afterDeselect: function(values) {
                    this.qs1.cache();
                    this.qs2.cache();
                    arrows.removePfamFilter(values);
                },
            });

            $("#diagram-generate-button").click(function() {
                if (arrows.hasFamilyData()) {
                    startProgressBar();
                    var idList = getIdList(); 
                    arrows.retrieveArrowData(idList, true, true, function(isEod) {
                        fillPfamSelectBox();
                        updateMoreButtonStatus(isEod);
                        stopProgressBar();
                    });
                } else {
                    startProgressBar();
                    arrows.retrieveFamilyData(function(fams) {
                        var idList = getIdList(); 
                        arrows.retrieveArrowData(idList, true, true, function(isEod) {
                            fillPfamSelectBox();
                            updateMoreButtonStatus(isEod);
                            stopProgressBar();
                            $("#display-control-container").show();
                            $("#pfam-filter-container").show();
                        });
                    });
                }
            });

            function fillPfamSelectBox() {
                var fams = arrows.getFamilies();
                var selObj = $("#pfam-filter");
                selObj.find('option').remove();
                for (var i = 0; i < fams.length; i++) {
                    selObj.multiSelect('addOption', { value: fams[i], text: fams[i], index: i });
                }
                selObj.multiSelect('updateCache');
            }

            function startProgressBar() {
                progressObj.css({visibility: "visible"});
            }

            function stopProgressBar() {
                progressObj.css({visibility: "hidden"});
            }

            function disableForm() {
                controlObj.prop("disabled", true);
            }

            function enableForm() {
                controlObj.prop("disabled", false);
            }

            function getIdList() {
                var idList = inputObj.value;
                return idList;
            }

            $("#display-mode").click(function() {
                arrows.toggleDisplayMode();
            });

            $("#arrow-more").click(function() {
                arrows.nextPage(nextPageCallback);
            }).hide();

            $("#arrow-all").click(function() {
                startProgressBar();
                arrows.retrieveArrowData(undefined, false, true, function(isEod) {
                    fillPfamSelectBox();
                    updateMoreButtonStatus(isEod);
                    stopProgressBar();
                });
            }).hide();

            function updateMoreButtonStatus(isEod) {
                if (isEod) {
                    $("#arrow-more").hide();
                    $("#arrow-all").hide();
                } else {
                    $("#arrow-more").show();
                    $("#arrow-all").show();
                }
            }

            function nextPageCallback(isEod) {
                updateMoreButtonStatus(isEod);
                $('html,body').animate({scrollTop: document.body.scrollHeight},"fast");
            }

        });
    </script>
    <script src="js/jquery.quicksearch.js" type="text/javascript"></script>
    <script src="js/jquery.multi-select.js" type="text/javascript"></script>
-->

<div id="info-popup" style="position:absolute;padding:5px;background-color:#555;color:#fff;">
    <div id="info-popup-id">UniProt ID: <span class="popup-id"></span></div>
    <div id="info-popup-desc">Description: <span class="popup-pfam"></span></div>
    <div id="info-popup-sptr">Annotation Status: <span class="popup-pfam"></span></div>
    <div id="info-popup-fam">Family: <span class="popup-pfam"></span></div>
    <div id="info-popup-fam-desc">Pfam Desc: <span class="popup-pfam"></span></div>
<!--    <div id="info-popup-coords">Coordinates: <span class="popup-pfam"></span></div>-->
    <div id="info-popup-seqlen">Sequence Length: <span class="popup-pfam"></span></div>
<!--    <div id="info-popup-dir">Direction: <span class="popup-pfam"></span></div>-->
<!--    <div id="info-popup-num">Gene Index: <span class="popup-pfam"></span></div>-->
</div>

<?php require_once('includes/footer.inc.php'); ?>

