
<?php 

require_once '../includes/main.inc.php';
require_once '../libs/settings.class.inc.php';



$gnnId = "";
$gnnKey = "";
$cooccurrence = 0;
$nbSize = 0;
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    $gnnKey = $_GET['key'];
    $gnnId = $_GET['id'];
    $gnn = new gnn($db, $gnnId);
    $cooccurrence = $gnn->get_cooccurrence();
    $nbSize = $gnn->get_size();
    $gnnName = $gnn->get_filename();
    $dotPos = strpos($gnnName, ".");
    $gnnName = substr($gnnName, 0, $dotPos);
    if ($gnn->get_key() != $_GET['key']) {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
        include("not_found.php");
        die();
    }
    elseif (time() < $gnn->get_time_completed() + settings::get_retention_days()) {
        prettyError404("That job has expired and doesn't exist anymore.");
    }
}
else {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    include("not_found.php");
    die();
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">   
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Genome Neighborhood Diagrams for Job #<?php echo $gnnId; ?></title>

        <!-- Bootstrap core CSS -->
        <link href="/bs/css/bootstrap.min.css" rel="stylesheet">
        <link href="/bs/css/menu-sidebar.css" rel="stylesheet">
        <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link rel="shortcut icon" href="images/favicon_efi.ico" type="image/x-icon">


        <!-- Custom styles for this template -->
        <link href="css/diagrams.css" rel="stylesheet">

        <script src="js/app.js" type="application/javascript"></script>
        <script src="js/arrows.js" type="application/javascript"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>

        <header class="header">
            <div class="span6 align-middle navbar-left">
                <span class="header-title">Genome Neighborhood Diagrams for Job #<?php echo $gnnId; ?></span>
            </div>
            <div class="span6">
                <div class="header-metadata pull-right align-middle">
                    <div>Co-occurrence: <?php echo $cooccurrence; ?></div>
                    <div>Neighborhood size: <?php echo $nbSize; ?></div>
                    <div>Input filename: <?php echo $gnnName; ?></div>
                </div>
            </div>
        </header>

        <!-- Begin page content -->
        <div id="wrapper" class="">
            <div id="sidebar-wrapper">
                <ul class="sidebar-nav">
                    <!--<li class="sidebar-brand">
                        <a href="#menu-toggle" id="menu-toggle" style="margin-top:20px;float:right;" >
                            <i class="fa fa-caret-square-o-right fa-toggle-size hidden" id="toggle-icon-right" aria-hidden="true"></i>
                            <i class="fa fa-caret-square-o-left fa-toggle-size" id="toggle-icon-left" aria-hidden="true"></i>
                        </a> 
                    </li>-->
                    <li>
                        <i class="fa fa-search" aria-hidden="true"> </i> <b><span style="margin-left:10px;">SEARCH</span></b>
                        <div id="advanced-search-panel">
                            <div style="font-size:0.9em">Input multiple clusters and/or individual UniProt IDs.</div>
                            <textarea id="advanced-search-input"></textarea>
                            <button type="button" class="btn btn-light" id="advanced-search-cluster-button">Query</button>
                        </div>
                    </li>
                    <li>
                        <i class="fa fa-filter" aria-hidden="true"> </i> <b><span style="margin-left:10px;">PFAM FILTERING</span></b>
                        <div class="initial-hidden">
                            <div class="filter-cb-div filter-cb-toggle-div" id="filter-container-toggle">
                                <input id="filter-cb-toggle" type="checkbox" />
                                <label for="filter-cb-toggle"><span id="filter-cb-toggle-text">Show Pfam Numbers</span></label>
                            </div>
                            <div style="width:100%;height:12em;" class="filter-container" id="filter-container">
                            </div>
                            <button type="button" id="filter-clear"><i class="fa fa-times" aria-hidden="true"></i> Clear Filter</button>
                            <!--<div>
                                <input id="filter-cb-toggle-dashes" type="checkbox" />
                                <label for="filter-cb-toggle-dashes"><span id="filter-cb-toggle-dashes-text">Dashed lines</span></label>
                            </div>-->
                            <div style="width:100%;height:12em;" class="active-filter-list" id="active-filter-list">
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="container">
                <div id="arrow-container" style="width:100%;height:10px">
                    <br>
                    <svg id="arrow-canvas" style="width:100%;height:100%"></svg>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div id="progress-loader" class="loader hidden"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="button-wrapper col-centered">
                            <a href="diagrams.php?id=<?php echo $gnnId; ?>&key=<?php echo $gnnKey; ?>" target="_blank" class="btn btn-default">New Window</a>
                            <button type="button" class="btn btn-default" id="save-canvas-button">Save To PNG</button>
                            <a id="download-data" href="download_diagram_data.php?id=<?php echo $gnnId; ?>&key=<?php echo $gnnKey; ?>" class="btn btn-default" id="download-data" title="Download the data to upload it for future analysis using this tool.">Download Data</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="button-wrapper pull-right">
                            <button type="button" class="btn btn-default" id="show-all-arrows-button">Show All</button>
                            <button type="button" class="btn btn-default" id="show-more-arrows-button">Show More</button>
                        </div>
                    </div>
                </div>
            </div>
        </footer>


        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->

        <script src="https://cdnjs.cloudflare.com/ajax/libs/snap.svg/0.5.1/snap.svg-min.js" content-type="text/javascript"></script>

        <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!--        <script src="/bs/js/jquery.js"></script>-->
        <!-- Bootstrap Core JavaScript -->
        <script src="/bs/js/bootstrap.min.js"></script>

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="/bs/js/ie10-viewport-bug-workaround.js"></script>
        <script type="application/javascript">
            $(document).ready(function() {
                var popupIds = new PopupIds();
                var arrowDiagrams = new ArrowDiagram("arrow-canvas", "", "arrow-container", popupIds);
                arrowDiagrams.setJobInfo("<?php echo $gnnId; ?>", "<?php echo $gnnKey; ?>");
                var arrowApp = new ArrowApp(arrowDiagrams);

                $("#menu-toggle").click(function(e) {
                    e.preventDefault();
                    $("#wrapper").toggleClass("toggled");
                    $("#filter-container").toggleClass("hidden");
                    $("#toggle-icon-left").toggleClass("hidden");
                    $("#toggle-icon-right").toggleClass("hidden");
                    $("#advanced-search-panel").toggleClass("hidden");
                });
                $("#filter-cb-toggle").click(function(e) {
                    arrowApp.togglePfamNamesNumbers(this.checked);
                });

            });
        </script>

        <div id="info-popup" class="info-popup hidden">
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

        <div id="start-info">
            <div><i class="fa fa-arrow-left" aria-hidden="true"></i></div>
            <div>Start by entering a cluster number</div>
        </div>
    </body>
</html>


