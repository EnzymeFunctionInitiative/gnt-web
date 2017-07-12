<?php
chdir(dirname(__FILE__));
require_once '../includes/main.inc.php';


$sapi_type = php_sapi_name();

if ($sapi_type != 'cli') {
    echo "Error: This script can only be run from the command line.\n";
}
else {
    $new_gnns = functions::get_gnn_jobs($db, __NEW__);
    if (count($new_gnns)) {
        foreach ($new_gnns as $data) {

            sleep(1);			
            $obj = new gnn($db, $data['gnn_id']);
            $result = $obj->run_gnn_async(functions::get_is_debug());
            if ($result['RESULT']) {
                $msg = "GNN Job ID: " . $data['gnn_id'] .  " - PBS Number: " . $result['PBS_NUMBER'] . " - " . $result['MESSAGE'];
            }
            else {
                $msg = "GNN Job ID: " . $data['gnn_id'] .  " - Error: " . $result['MESSAGE'];
            }
            print "MESSAGE: $msg\n";
            functions::log_message($msg);
        }
    }
}

?>

