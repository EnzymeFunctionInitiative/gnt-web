<?php
include_once '../includes/main.inc.php';
include_once '../libs/gnn.class.inc.php';

//$result = array();
//$rawData = file("proto/raw2.txt");
//
//$ND = array();
//
//foreach ($rawData as $row) {
//    $parts = explode("\t", trim($row));
//    $accId = $parts[0];
//    $ND[$accId] = array('pfam' => $parts[5], 'start' => $parts[2], 'dir' => $parts[1], 'width' => $parts[3],
//       'strain' => $parts[4], 'neighbors' => array());
//    if (count($parts) > 6) {
//        $neighbors = explode("|", $parts[6]);
//        foreach ($neighbors as $neighborData) {
//            $parts = explode(";", $neighborData);
//            array_push($ND[$accId]['neighbors'], $parts);
//        }
//    }
//}

$output = array();

$message = "";
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    $gnn = new gnn($db,$_GET['id']);
    if ($gnn->get_key() != $_GET['key']) {
        $message = "No GNN selected.";
        exit;
    }
    elseif (time() < $gnn->get_time_completed() + settings::get_retention_days()) {
        $message = "GNN results are expired.";
    }
    else {
    }
}
else {
    $message = "No GNN selected.";
}

$output["message"] = "";
$output["error"] = false;

if ($message) {
    $output["message"] = $message;
    $output["error"] = true;
    echo json_encode($output);
    exit;
}

$data_dir = $gnn->get_output_dir();

$ids = array();

if (array_key_exists("query", $_GET)) {
    $queryString = str_replace("\n", ",", $_GET["query"]);
    $queryString = str_replace("\r", ",", $queryString);
    $queryString = str_replace(" ", ",", $queryString);
    $items = explode(",", $queryString);

    foreach ($items as $item) {
        if (is_numeric($item)) {
            $ids = array_merge($ids, getIdsFromCluster($item, $data_dir));
        }
        else if ($item) {
            array_push($ids, $item);
        }
    }
}
else {
    $output["error"] = true;
    $output["message"] = "No query is selected.";
    echo json_encode($output);
    exit;
}

$resultsDb = new SQLite3($gnn->get_arrow_data_file());
//$resultsDb->open($gnn->get_arrow_data_file());

//$data_file_contents = file_get_contents($gnn->get_arrow_data_file());
//$all_data = json_decode($data_file_contents, true);
//var_dump($all_data);

$output["data"] = array();
foreach ($ids as $id) {
    $attrSql = "SELECT * FROM attributes WHERE accession = '$id'";
    $dbQuery = $resultsDb->query($attrSql);
    $row = $dbQuery->fetchArray(SQLITE3_ASSOC);
    if (!$row)
        continue;

    $attr = array();
    $attr['accession'] = $row['accession'];
    $attr['id'] = $row['id'];
    $attr['num'] = $row['num'];
    $attr['family'] = $row['family'];
    $attr['start'] = $row['start'];
    $attr['stop'] = $row['stop'];
    $attr['rel_start'] = $row['rel_start'];
    $attr['rel_width'] = $row['rel_width'];
    $attr['strain'] = $row['strain'];
    $attr['direction'] = $row['direction'];
    $attr['type'] = $row['type'];
    $attr['seq_len'] = $row['seq_len'];
    $attr['cluster_num'] = $row['cluster_num'];

    $nbSql = "SELECT * FROM neighbors WHERE gene_key = '" . $row['sort_key'] . "'";
    $dbQuery = $resultsDb->query($nbSql);

    $neighbors = array();
    while ($row = $dbQuery->fetchArray()) {
        $nb = array();
        $nb['accession'] = $row['accession'];
        $nb['id'] = $row['id'];
        $nb['num'] = $row['num'];
        $nb['family'] = $row['family'];
        $nb['start'] = $row['start'];
        $nb['stop'] = $row['stop'];
        $nb['rel_start'] = $row['rel_start'];
        $nb['rel_width'] = $row['rel_width'];
        $nb['strain'] = $row['strain'];
        $nb['direction'] = $row['direction'];
        $nb['type'] = $row['type'];
        $nb['seq_len'] = $row['seq_len'];
        array_push($neighbors, $nb);
    }

    $output["data"][$id] = array('attributes' => $attr,
                                 'neighbors' => $neighbors);
}

echo json_encode($output);


//else if (array_key_exists("cluster-id", $_GET)) {
//    $queryString = str_replace("\n", ",", $_GET["cluster-id"]);
//    $queryString = str_replace("\r", ",", $queryString);
//    $queryString = str_replace(" ", ",", $queryString);
//    $clusters = explode(",", $queryString);
//
//    foreach ($clusters as $clusterId) {
//        $ids = array_merge($ids, getIdsFromCluster($clusterId));
//    }
//}
//else if (array_key_exists("ids", $_GET)) {
//    $queryString = str_replace("\n", ",", $_GET["ids"]);
//    $queryString = str_replace("\r", ",", $queryString);
//    $queryString = str_replace(" ", ",", $queryString);
//    $ids = explode(",", $queryString);
//}
//
//foreach ($ids as $id) {
//    $id = trim($id);
//    if (array_key_exists($id, $ND)) {
//        array_push($result, array($id, $ND[$id]));
//    }
//}
//echo json_encode($result);



function getIdsFromCluster($clusterId, $data_dir) {
    if (!is_numeric($clusterId))
        continue;

    $filePath = "$data_dir/cluster-data/cluster_UniProt_IDs_" . $clusterId . ".txt";
    if (!file_exists($filePath))
        continue;

    $flags = FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES;
    $ids = file($filePath, $flags);
    return $ids;
}

?>
