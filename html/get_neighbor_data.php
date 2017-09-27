<?php
include_once '../includes/main.inc.php';
include_once '../libs/gnn.class.inc.php';


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


$minBp = 999999999999;
$maxBp = -999999999999;
$maxQueryWidth = -1;

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
    $attr['rel_start_coord'] = $row['rel_start'];
    $attr['rel_stop_coord'] = $row['rel_stop'];
    $attr['strain'] = $row['strain'];
    $attr['direction'] = $row['direction'];
    $attr['type'] = $row['type'];
    $attr['seq_len'] = $row['seq_len'];
    $attr['cluster_num'] = $row['cluster_num'];
    $attr['organism'] = $row['organism'];
    $attr['taxon_id'] = $row['taxon_id'];
    $attr['anno_status'] = $row['anno_status'];
    $attr['family_desc'] = $row['family_desc'];
    $attr['desc'] = $row['desc'];

    if ($attr['rel_start_coord'] < $minBp)
        $minBp = $attr['rel_start_coord'];
    if ($attr['rel_stop_coord'] > $maxBp)
        $maxBp = $attr['rel_stop_coord'];
    $queryWidth = $attr['rel_stop_coord'] - $attr['rel_start_coord'];
    if ($queryWidth > $maxQueryWidth)
        $maxQueryWidth = $queryWidth;

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
        $nb['rel_start_coord'] = $row['rel_start'];
        $nb['rel_stop_coord'] = $row['rel_stop'];
        $nb['direction'] = $row['direction'];
        $nb['type'] = $row['type'];
        $nb['seq_len'] = $row['seq_len'];
        $nb['anno_status'] = $row['anno_status'];
        $nb['family_desc'] = $row['family_desc'];
        $nb['desc'] = $row['desc'];
        if ($nb['rel_start_coord'] < $minBp)
            $minBp = $nb['rel_start_coord'];
        if ($nb['rel_stop_coord'] > $maxBp)
            $maxBp = $nb['rel_stop_coord'];
        array_push($neighbors, $nb);
    }

    $output["data"][$id] = array('attributes' => $attr,
                                 'neighbors' => $neighbors);
}

$maxSide = (abs($maxBp) > abs($minBp)) ? abs($maxBp) : abs($minBp);
$maxWidth = $maxSide * 2 + $maxQueryWidth;
$minBp = -$maxSide;
$maxBp = $maxSide + $maxQueryWidth;

foreach ($output["data"] as $accId => $data) {
    $start = $output["data"][$accId]["attributes"]["rel_start_coord"];
    $stop = $output["data"][$accId]["attributes"]["rel_stop_coord"];
    $acStart = 0.5;
    $acWidth = ($stop - $start) / $maxWidth;
    $offset = 0.5 - ($start - $minBp) / $maxWidth;
    $output["data"][$accId]["attributes"]["rel_start"] = $acStart;
    $output["data"][$accId]["attributes"]["rel_width"] = $acWidth;

    foreach ($output["data"][$accId]["neighbors"] as $idx => $data2) {
        $nbStart = ($output["data"][$accId]["neighbors"][$idx]["rel_start_coord"] - $minBp) / $maxWidth;
        $nbWidth = ($output["data"][$accId]["neighbors"][$idx]["rel_stop_coord"] - $output["data"][$accId]["neighbors"][$idx]["rel_start_coord"]) / $maxWidth;
        $output["data"][$accId]["neighbors"][$idx]["rel_start"] = $nbStart + $offset;
        $output["data"][$accId]["neighbors"][$idx]["rel_width"] = $nbWidth;
    }
}


echo json_encode($output);





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
