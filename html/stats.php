<?php

include_once 'inc/stats_main.inc.php';
include_once 'inc/stats_header.inc.php';

$month = date('n');
if (isset($_POST['month'])) {
	$month = $_POST['month'];
}
$year = date('Y');
if (isset($_POST['year'])) {
	$year = $_POST['year'];
}

$graph_type = "daily_jobs";
$get_array  = array('graph_type'=>$graph_type,
                'month'=>$month,
                'year'=>$year);
$graph_image = "<img src='daily_graph.php?" . http_build_query($get_array) . "'>";

$generate_per_month = statistics::num_per_month($db);
$generate_per_month_html = "";
foreach ($generate_per_month as $value) {
	$generate_per_month_html .= "<tr><td>" . $value['month'] . "</td>";
	$generate_per_month_html .= "<td>" . $value['year'] . "</td>";
	$generate_per_month_html .= "<td>" . $value['count'] . "</td>";
	$generate_per_month_html .= "</tr>";

}


$month_html = "<select class='form-control' name='month'>";
for ($i=1;$i<=12;$i++) {
	if ($month == $i) {
		$month_html .= "<option value='" . $i . "' selected='selected'>" . date("F", mktime(0, 0, 0, $i, 10)) . "</option>\n";
	}
	else {
		$month_html .= "<option value='" . $i . "'>" . date("F", mktime(0, 0, 0, $i, 10)) . "</option>\n";
	}
}
$month_html .= "</select>";

$year_html = "<select class='form-control' name='year'>";
for ($i=2014;$i<=date('Y');$i++) {
	if ($year = $i) {
		$year_html .= "<option selected='selected' value='" . $i . "'>". $i . "</option>\n";
	}
	else {
		$year_html .= "<option value='" . $i . "'>". $i . "</option>\n";
	}

}
$year_html .= "</select>";
?>



<h3>EFI-GNT Statistics</h3>

<h4>Statistics</h4>
<table class='table table-condensed table-bordered span8'>
<tr>
	<th>Month</th>
	<th>Year</th>
	<th>Total Jobs</th>
</tr>
<?php echo $generate_per_month_html; ?>
</table>
<hr>
<h4>Running Total of Unique Users</h4>
<p>Number of Unique Users: <?php echo statistics::num_unique_users($db); ?>
<hr>
<h4>Running Total of Jobs</h4>
<p>Total Number of Jobs: <?php echo statistics::num_jobs($db); ?>
<hr>

<hr>
<h4>Daily Graph</h4>
<form class='form-inline' method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
        <?php echo $month_html; ?>
        <?php echo $year_html; ?>

<input class='btn btn-primary' type='submit'
                name='create_user_report' value='Get Daily Graph'>

<br>
<hr>
<?php echo $graph_image; ?>


<?php include_once 'inc/stats_footer.inc.php'; ?>

