
<?php 

require_once '../includes/main.inc.php';




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
        echo "<br><b><h4 class='center'>No EFI-GNN Selected.</h4></b>";
        exit;
    }
}
else {
    echo "<br><b><h4 class='center'>No EFI-GNN Selected. Please go <a href='index.php'>back</a></h4></b>";
    exit;
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

        <title>Arrow Diagrams for Job #<?php echo $gnnId; ?></title>

        <!-- Bootstrap core CSS -->
        <link href="/bs/css/bootstrap.min.css" rel="stylesheet">
        <link href="/bs/css/menu-sidebar.css" rel="stylesheet">
        <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet">


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
<!--            <div class="container">-->
                <div class="span6 align-middle">
                    <form class="navbar-form navbar-left header-form"> 
                        <div class="form-group"> 
                            <div class="search-wrapper">
                                <span class="header-title">Arrow Diagrams for Job #<?php echo $gnnId; ?></span>
                                <input class="form-control" placeholder="Search for cluster(s)" id="search-input"> 
                                <button type="button" class="btn btn-default" id="search-cluster-button">Query</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="span6">
                    <div class="header-metadata pull-right align-middle">
                        <div>Co-occurrence: <?php echo $cooccurrence; ?></div>
                        <div>Neighborhood size: <?php echo $nbSize; ?></div>
                        <div>Input filename: <?php echo $gnnName; ?></div>
                    </div>
                </div>
<!--            </div>-->
        </header>

        <!-- Begin page content -->
        <div id="wrapper" class="">
            <div id="sidebar-wrapper">
                <ul class="sidebar-nav">
                    <li class="sidebar-brand">
                    <a href="#menu-toggle" id="menu-toggle" style="margin-top:20px;float:right;" >
                        <i class="fa fa-caret-square-o-right fa-toggle-size hidden" id="toggle-icon-right" aria-hidden="true"></i>
                        <i class="fa fa-caret-square-o-left fa-toggle-size" id="toggle-icon-left" aria-hidden="true"></i>
                        <!--<i class="fa fa-bars " style="font-size:20px !Important;" aria-hidden="true" aria-hidden="true"></i>--></a> 
                    </li>
                    <li>
                        <i class="fa fa-filter" aria-hidden="true"> </i> <b><span style="margin-left:10px;">PFAM FILTERING</span></b><br>
                        <div class="filter-cb-div filter-cb-toggle-div initial-hidden" id="filter-container-toggle">
                            <input id="filter-cb-toggle" type="checkbox" />
                            <label for="filter-cb-toggle"><span id="filter-cb-toggle-text">Show Pfam Numbers</span></label>
                        </div>
                        <div style="width:100%;height:12em;" class="filter-container initial-hidden" id="filter-container">
                        </div>
                        <button type="button" id="filter-clear" class="initial-hidden"><i class="fa fa-times" aria-hidden="true"></i> Clear Filter</button>
                    </li>
                    <li>
                        <i class="fa fa-search" aria-hidden="true"> </i> <b><span style="margin-left:10px;">ADVANCED SEARCH</span></b><br>
                        <div id="advanced-search-panel">
                            <div style="font-size:0.9em">Input multiple clusters and/or individual UniProt IDs.</div>
                            <textarea id="advanced-search-input"></textarea>
                            <button type="button" class="btn btn-light" id="advanced-search-cluster-button">Query</button>
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
                    <div class="col-md-6">
                        <div id="progress-loader" class="loader hidden"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="button-wrapper pull-right">
                            <button type="button" id="show-all-arrows-button">Show All</button>
                            <button type="button" id="show-more-arrows-button">Show More</button>
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
        <script src="/bs/js/jquery.js"></script>
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

                $('[data-toggle="tooltip"]').tooltip();
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
            <div><i class="fa fa-arrow-up" aria-hidden="true"></i></div>
            <div>Start by entering a cluster number</div>
        </div>
    </body>
</html>


