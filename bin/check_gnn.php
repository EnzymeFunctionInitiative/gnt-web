<?php
chdir(dirname(__FILE__));
require_once '../includes/main.inc.php';
require_once '../libs/functions.class.inc.php';


$sapi_type = php_sapi_name();

if ($sapi_type != 'cli') {
    echo "Error: This script can only be run from the command line.\n";
}
else {
    $running_jobs = functions::get_gnn_jobs($db,__RUNNING__);
    if (count($running_jobs)) {
        foreach ($running_jobs as $job) {
            $gnn = new gnn($db,$job['gnn_id']);

            $finish_file_exists = $gnn->check_finish_file();
            $job_running = $gnn->check_pbs_running();
            if ((!$job_running) && ($finish_file_exists)) {
                $gnn->complete_gnn();
                $msg = "GNN ID: " . $job['gnn_id'] . " - Job Completed Successfully";
                functions::log_message($msg);
            } else if (!$finish_file_exists && !$job_running) {
                $gnn->error_gnn();
                $msg = "GNN ID: " . $job['gnn_id'] . " - Job Failed";
                functions::log_message($msg);
            }
        }
    }
}

?>

