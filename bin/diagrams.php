<?php
chdir(dirname(__FILE__));
require_once '../includes/main.inc.php';
require_once '../libs/diagram_jobs.class.inc.php';


$sapi_type = php_sapi_name();

if ($sapi_type != 'cli') {
    echo "Error: This script can only be run from the command line.\n";
}
else {
    $jobManager = new diagram_jobs($db);
    $jobs = $jobManager->get_new_jobs();
    if (count($jobs)) {
        foreach ($jobs as $jobId) {
            sleep(1);
            $msg = false;
            print "Processing $jobId\n";
            $job = new diagram_job($db, $jobId);
            $msg = $job->process();
            if (!$msg) {
                print "Error processing job $jobId:\n";
                print $job->get_message();
            }
        }
    }
}

?>

