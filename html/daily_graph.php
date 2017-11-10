<?php
if (file_exists("inc/stats_main.inc.php")) {
    require_once 'inc/stats_main.inc.php';
}


if (isset($_GET['year'])) {
    $year = $_GET['year'];
}
if (isset($_GET['month'])) {
    $month = $_GET['month'];

}

$user_id = 0;
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
}

$graph_type = "";
if (isset($_GET['graph_type'])) {
    $graph_type = $_GET['graph_type'];
}


//Jobs Per Month
if ($graph_type == 'daily_jobs') {
    $data = statistics::get_daily_jobs($db,$month,$year);
    $xaxis = "day";
    $yaxis = "count";
    $title = "Daily Jobs - " . date("F", mktime(0, 0, 0, $month, 10)) . " - " . $year;
    custom_graph::bar_graph($data,$xaxis,$yaxis,$title);
}


?>
