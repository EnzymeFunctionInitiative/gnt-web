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
$output["eod"] = false;

if ($message) {
    $output["message"] = $message;
    $output["error"] = true;
    echo json_encode($output);
    exit;
}

$dataDir = $gnn->get_output_dir();
$dbFile = $gnn->get_arrow_data_file();

if (array_key_exists("query", $_GET)) {
    $queryString = str_replace("\n", ",", $_GET["query"]);
    $queryString = str_replace("\r", ",", $queryString);
    $queryString = str_replace(" ", ",", $queryString);
    $items = explode(",", $queryString);

    $arrowData = getArrowData($items, $dataDir, $dbFile);
    $output["eod"] = $arrowData["eod"];
    $output["data"] = $arrowData["data"];
}
else if (array_key_exists("fams", $_GET)) {
    $output["families"] = getFamilies($dataDir, $dbFile);
}
else {
    $output["error"] = true;
    $output["message"] = "No query is selected.";
    echo json_encode($output);
    exit;
}



echo json_encode($output);








function getFamilies($dataDir, $dbFile) {
    $output = array();

    $resultsDb = new SQLite3($dbFile);

    // Check if the table exists
    $checkSql = "SELECT name FROM sqlite_master WHERE type='table' AND name='families'";
    $dbQuery = $resultsDb->query($checkSql);
    if ($dbQuery->fetchArray()) {
        $famSql = "SELECT * FROM families";
        $dbQuery = $resultsDb->query($famSql);
        while ($row = $dbQuery->fetchArray()) {
            if (strlen($row["family"]) > 0)
                array_push($output, $row["family"]);
        }
    }

    return $output;
}



function getArrowData($items, $dataDir, $dbFile) {

    $output = array();

    $ids = array();

    foreach ($items as $item) {
        if (is_numeric($item)) {
            $ids = array_merge($ids, getIdsFromCluster($item, $dataDir));
        }
        else if ($item) {
            array_push($ids, $item);
        }
    }

    $pageSize = 20;
    $startCount = 0;
    $maxCount = 100000000;
    if (array_key_exists("page", $_GET)) {
        $page = intval($_GET["page"]);
        if ($page >= 0 && $page <= 10000) { // error check to limit to 10000 pages 
            $startCount = $page * $pageSize;
            $maxCount = $startCount + $pageSize;
        }
    }
    
    $output["eod"] = "$startCount $maxCount";
    
    $resultsDb = new SQLite3($dbFile);
    
    
    $minBp = 999999999999;
    $maxBp = -999999999999;
    $maxQueryWidth = -1;
    
    
    $output["data"] = array();
    $idCount = 0;
    foreach ($ids as $id) {
        $attrSql = "SELECT * FROM attributes WHERE accession = '$id'";
        $dbQuery = $resultsDb->query($attrSql);
        $row = $dbQuery->fetchArray(SQLITE3_ASSOC);
        if (!$row)
            continue;
    
        if ($idCount++ < $startCount)
            continue;
    
        if (++$startCount > $maxCount)
            break;
    
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
        if (array_key_exists("is_bound", $row))
            $attr['is_bound'] = $row['is_bound'];
        else
            $attr['is_bound'] = 0;
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
    
    $output["eod"] = $startCount < $maxCount;
    
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

    return $output;
}






function getIdsFromCluster($clusterId, $dataDir) {
    if (!is_numeric($clusterId))
        continue;

    $filePath = "$dataDir/cluster-data/cluster_UniProt_IDs_" . $clusterId . ".txt";
    if (!file_exists($filePath))
        continue;

    $flags = FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES;
    $ids = file($filePath, $flags);
    return $ids;
}

?>
