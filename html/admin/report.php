<?php
include_once 'includes/main.inc.php';

if (isset($_POST['create_user_report'])) {

	$type = $_POST['report_type'];
	$data = statistics::get_unique_users($db);
	$filename = "unique_users." . $type; 
}

if (isset($_POST['create_job_report'])) {

	$type = $_POST['report_type'];
	$month = $_POST['month'];
	$year = $_POST['year'];
	$data =	statistics::get_jobs($db,$month,$year);
	$filename = "job_report-" . $year . "-" . $month . "." .$type;
}


switch ($type) {
	case 'csv':
		report::create_csv_report($data,$filename);
		break;
	case 'xls':
		report::create_excel_2003_report($data,$filename);
		break;
	case 'xlsx':
		report::create_excel_2007_report($data,$filename);
		break;
}

?>
