<?php

include_once '../inc/stats_main.inc.php';
include_once '../inc/stats_admin_header.inc.php';

$month = date('n');
if (isset($_GET['month'])) {
        $month = $_GET['month'];
}
$year = date('Y');
if (isset($_GET['year'])) {
        $year = $_GET['year'];
}
$stepc_page = "../stepc.php";
$jobs = statistics::get_jobs($db,$month,$year);

$gnn_html = "";
foreach ($jobs as $job) {
	$get_array = array('id'=>$job['GNT ID'],'key'=>$job['Key']);
	$url = $stepc_page . "?" . http_build_query($get_array);
	$gnn_html .= "<tr>";
	if (time() < $job['Time Completed'] + __RETENTION_DAYS__) {
		$gnn_html .= "<td>&nbsp</td>";
	}
	else {
		$gnn_html .= "<td><a href='" . $url ."'><span class='glyphicon glyphicon-share'></span></a></td>";
	}
	$gnn_html .= "<td>" . $job['GNT ID'] . "</td>\n";
	$gnn_html .= "<td>" . $job['Email'] . "</td>\n";
	$gnn_html .= "<td>" . $job['Filename'] . "</td>\n";
	$gnn_html .= "<td>" . $job['Neighborhood Size'] . "</td>\n";
	$gnn_html .= "<td>" . $job['Input Cooccurrance'] . "</td>\n";
	$gnn_html .= "<td>" . $job['Time Created'] . "</td>\n";
	$gnn_html .= "<td>" . $job['Time Started'] . "</td>\n";
	$gnn_html .= "<td>" . $job['Time Completed'] . "</td>\n";
	$gnn_html .= "</tr>";

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

$monthName = date("F", mktime(0, 0, 0, $month, 10));
?>
<h3>EFI-GNT Jobs - <?php echo $monthName . " - " . $year; ?></h3>

<form class='form-inline' method='get' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
<?php echo $month_html; ?>
<?php echo $year_html; ?>
<input class='btn btn-primary' type='submit'
                name='get_jobs' value='Submit'>

</form>
<h4>Jobs</h4>
<table class='table table-condensed table-bordered table-striped'>
<tr>
	<th>&nbsp</th>
	<th>EFI-GNT ID</th>
	<th>Email</th>
	<th>Filename</th>
	<th>Neighborhood Size</th>
	<th>Input Cooccurrance</th>
	<th>Time Submitted</th>
	<th>Time Started</th>
	<th>Time Finished</th>
</tr>
<?php echo $gnn_html; ?>
</table>



<?php include_once 'inc/footer.inc.php' ?>
