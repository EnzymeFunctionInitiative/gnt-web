<?php
include_once '../includes/main.inc.php';
include_once '../libs/gnn.class.inc.php';


$output = array();

$dbFile = "";

$message = "";
if ((isset($_GET["gnn-id"])) && (is_numeric($_GET["gnn-id"]))) {
    $gnn = new gnn($db,$_GET["gnn-id"]);
    if ($gnn->get_key() != $_GET["key"]) {
        $message = "No GNN selected.";
        exit;
    }
    elseif (time() < $gnn->get_time_completed() + settings::get_retention_days()) {
        $message = "GNN results are expired.";
    }
    
    $dbFile = $gnn->get_diagram_data_file();
    if (!file_exists($dbFile))
        $dbFile = $gnn->get_diagram_data_file_legacy();
}
else if (isset($_GET['upload-id']) && functions::is_diagram_upload_id_valid($_GET['upload-id'])) {
    $arrows = new diagram_data_file($_GET['upload-id']);
    $dbFile = $arrows->get_diagram_data_file();
}
else if (isset($_GET['direct-id']) && functions::is_diagram_upload_id_valid($_GET['direct-id'])) {
    $arrows = new diagram_data_file($_GET['direct-id']);
    $dbFile = $arrows->get_diagram_data_file();
}
else {
    $message = "No GNN selected.";
}

$window = NULL;
if (isset($_GET["window"]) && is_numeric($_GET["window"])) {
    $window = intval($_GET["window"]);
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


if (array_key_exists("query", $_GET)) {
    $queryString = strtoupper($_GET["query"]);
    $queryString = str_replace("\n", ",", $queryString);
    $queryString = str_replace("\r", ",", $queryString);
    $queryString = str_replace(" ", ",", $queryString);
    $items = explode(",", $queryString);

    $blastId = getBlastId();
    //$orderData = getOrder($blastId, $items, $dbFile, $blastCacheDir, $gnn);
    $orderData = getDefaultOrder();
    $arrowData = getArrowData($items, $dbFile, $orderData, $window);
    $output["eod"] = $arrowData["eod"];
    $output["counts"] = $arrowData["counts"];
    $output["data"] = $arrowData["data"];
}
else if (array_key_exists("fams", $_GET)) {
    $output["families"] = getFamilies($dbFile);
}
else {
    $output["error"] = true;
    $output["message"] = "No query is selected.";
    echo json_encode($output);
    exit;
}



echo json_encode($output);






function getBlastId() {
    return "test";
}

function getFamilies($dbFile) {
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
                array_push($output, array("id" => $row["family"], "name" => "a name"));
        }
    }

    return $output;
}



function getArrowData($items, $dbFile, $orderDataStruct, $window) {

    $orderData = $orderDataStruct['order'];
    $output = array();
    
    $resultsDb = new SQLite3($dbFile);
    $orderByClause = getOrderByClause($resultsDb);

    $ids = parseIds($items, $orderDataStruct, $resultsDb, $orderByClause);

    $pageBounds = getPageLimits();
    $startCount = $pageBounds['start'];
    $maxCount = $pageBounds['end'];
    $output["eod"] = "$startCount $maxCount";
    $output["counts"] = array("max" => count($ids), "invalid" => array());
    
    $minBp = 999999999999;
    $maxBp = -999999999999;
    $maxQueryWidth = -1;
    
    $output["data"] = array();
    $idCount = 0;
    for ($i = 0; $i < count($ids); $i++) {
        $id = $ids[$i][0];
        $evalue = $ids[$i][1];

        $attrSql = "SELECT * FROM attributes WHERE accession = '$id' $orderByClause";
        $dbQuery = $resultsDb->query($attrSql);
        $row = $dbQuery->fetchArray(SQLITE3_ASSOC);
        if (!$row) {
            array_push($output["counts"]["invalid"], $id);
            continue;
        }
    
        if ($idCount++ < $startCount)
            continue;
    
        if (++$startCount > $maxCount)
            break;

        $attr = getQueryAttributes($row, $orderData);
        if ($attr['rel_start_coord'] < $minBp)
            $minBp = $attr['rel_start_coord'];
        if ($attr['rel_stop_coord'] > $maxBp)
            $maxBp = $attr['rel_stop_coord'];
        $queryWidth = $attr['rel_stop_coord'] - $attr['rel_start_coord'];
        if ($queryWidth > $maxQueryWidth)
            $maxQueryWidth = $queryWidth;


        $nbSql = "SELECT * FROM neighbors WHERE gene_key = '" . $row['sort_key'] . "'";
        if ($window !== NULL) {
            //TODO: handle circular case
            $numClause = "num >= " . ($attr['num'] - $window) . " AND num <= " . ($attr['num'] + $window);
            $nbSql .= " AND " . $numClause;
        }
        $dbQuery = $resultsDb->query($nbSql);
    
        $neighbors = array();
        while ($row = $dbQuery->fetchArray()) {
            $nb = getNeighborAttributes($row);
            if ($nb['rel_start_coord'] < $minBp)
                $minBp = $nb['rel_start_coord'];
            if ($nb['rel_stop_coord'] > $maxBp)
                $maxBp = $nb['rel_stop_coord'];
            array_push($neighbors, $nb);
        }

        array_push($output["data"],
            array(
                'attributes' => $attr,
                'neighbors' => $neighbors,
            ));
    }

    $resultsDb->close();

    $output["eod"] = $startCount < $maxCount;
    $output["counts"]["displayed"] = $startCount;
    if (!$output["eod"])
        $output["counts"]["displayed"]--;

    $output = computeRelativeCoordinates($output, $minBp, $maxBp, $maxQueryWidth);
    
    return $output;
}


function getOrderByClause($db) {
    $hasSortOrder = 0;

    #$cols = $db->fetchColumnTypes('attributes', SQLITE_ASSOC);
    #foreach ($cols as $col => $type) {
    #    print "$col\n";
    #    if ($col == "sort_order") {
    #        $hasSortOrder = 1;
    #        break;
    #    }
    #}

    $result = $db->query("PRAGMA table_info(attributes)");
    while ($row = $result->fetchArray()) {
        if ($row['name'] == "sort_order") {
            $hasSortOrder = 1;
            break;
        }
    }
    
    if ($hasSortOrder) {
        return "ORDER BY sort_order";
    } else {
        return "";
    }
}


function sortNodes($a, $b) {
    if ($a[1] == $b[1])
        return 0;
    return $a[1] < $b[1] ? -1 : 1;
}


function computeRelativeCoordinates($output, $minBp, $maxBp, $maxQueryWidth) {
    $maxSide = (abs($maxBp) > abs($minBp)) ? abs($maxBp) : abs($minBp);
    $maxWidth = $maxSide * 2 + $maxQueryWidth;
    $minBp = -$maxSide;
    $maxBp = $maxSide + $maxQueryWidth;
    
    for ($i = 0; $i < count($output["data"]); $i++) {
        $start = $output["data"][$i]["attributes"]["rel_start_coord"];
        $stop = $output["data"][$i]["attributes"]["rel_stop_coord"];
        $acStart = 0.5;
        $acWidth = ($stop - $start) / $maxWidth;
        $offset = 0.5 - ($start - $minBp) / $maxWidth;
        $output["data"][$i]["attributes"]["rel_start"] = $acStart;
        $output["data"][$i]["attributes"]["rel_width"] = $acWidth;
    
        foreach ($output["data"][$i]["neighbors"] as $idx => $data2) {
            $nbStart = ($output["data"][$i]["neighbors"][$idx]["rel_start_coord"] - $minBp) / $maxWidth;
            $nbWidth = ($output["data"][$i]["neighbors"][$idx]["rel_stop_coord"] - $output["data"][$i]["neighbors"][$idx]["rel_start_coord"]) / $maxWidth;
            $output["data"][$i]["neighbors"][$idx]["rel_start"] = $nbStart + $offset;
            $output["data"][$i]["neighbors"][$idx]["rel_width"] = $nbWidth;
        }
    }

    return $output;
}


function parseIds($items, $orderDataStruct, $resultsDb, $sortOrderClause) {

    $orderData = $orderDataStruct['order'];
    $centralId = $orderDataStruct['central_id'];

    $ids = array();

    foreach ($items as $item) {
        if (is_numeric($item)) {
            $clusterIds = getIdsFromDatabase($item, $resultsDb, $sortOrderClause);
            foreach ($clusterIds as $clusterId) {
                $evalue = array_key_exists($clusterId, $orderData) ? $orderData[$clusterId][0] : -1;
                $pctId = array_key_exists($clusterId, $orderData) ? $orderData[$clusterId][1] : -1;
                array_push($ids, array($clusterId, $evalue, $pctId));
            }
        }
        else if ($item) {
            $evalue = array_key_exists($item, $orderData) ? $orderData[$item][0] : -1;
            $pctId = array_key_exists($item, $orderData) ? $orderData[$clusterId][1] : -1;
            array_push($ids, array($item, $evalue, $pctId));
        }
    }

    // This will be useful when we start sorting/grouping
    //usort($ids, "sortNodes");
    //if ($centralId)
    //    array_unshift($ids, array($centralId, 0));

    return $ids;
}


function getPageLimits() {
    $pageSize = 20;
    $startCount = 0;
    $maxCount = 100000000;
    if (array_key_exists("page", $_GET)) {
        $parm = $_GET["page"];
        $dashPos = strpos($parm, "-");
        if ($dashPos !== FALSE) {
            $startPage = substr($parm, 0, $dashPos);
            $endPage = substr($parm, $dashPos + 1);
            $startCount = $startPage * $pageSize;
            $maxCount = $endPage * $pageSize + $pageSize;
        } else {
            $page = intval($parm);
            if ($page >= 0 && $page <= 10000) { // error check to limit to 10000 pages 
                $startCount = $page * $pageSize;
                $maxCount = $startCount + $pageSize;
            }
        }
    }

    return array('start' => $startCount, 'end' => $maxCount);
}


function getQueryAttributes($row, $orderData) {
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
    $attr['organism'] = rtrim($row['organism']);
    $attr['taxon_id'] = $row['taxon_id'];
    $attr['anno_status'] = $row['anno_status'];
    $attr['family_desc'] = $row['family_desc'];
    $attr['desc'] = $row['desc'];
    
    if (array_key_exists("color", $row))
        $attr['color'] = $row['color'];

    if (array_key_exists("sort_order", $row))
        $attr['sort_order'] = $row['sort_order'];
    else
        $attr['sort_order'] = -1;
    
    if (array_key_exists("is_bound", $row))
        $attr['is_bound'] = $row['is_bound'];
    else
        $attr['is_bound'] = 0;

    $evalue = array_key_exists($attr['accession'], $orderData) ? $orderData[$attr['accession']][0] : 100;
    $pid = array_key_exists($attr['accession'], $orderData) ? $orderData[$attr['accession']][1] : -1;
    $attr['evalue'] = $evalue;
    $attr['pid'] = $pid;

    if (strlen($attr['organism']) > 0 && substr_compare($attr['organism'], ".", -1) === 0)
        $attr['organism'] = substr($attr['organism'], 0, strlen($attr['organism'])-1);

    return $attr;
}


function getNeighborAttributes($row) {
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
    
    if (array_key_exists("color", $row))
        $nb['color'] = $row['color'];
    
    return $nb;
}


function getIdsFromDatabase($clusterId, $resultsDb, $sortOrderClause) {
    if (!is_numeric($clusterId))
        return array();

    $sql = "SELECT accession FROM attributes WHERE cluster_num = '$clusterId' $sortOrderClause";
    $dbQuery = $resultsDb->query($sql);

    $ids = array();
    while ($row = $dbQuery->fetchArray()) {
        array_push($ids, $row['accession']);
    }
    
    return $ids;
}


function getIdsFromClusterFile($clusterId, $dataDir) {
    if (!is_numeric($clusterId))
        return array();

    $filePath = "$dataDir/cluster-data/cluster_UniProt_IDs_" . $clusterId . ".txt";
    if (!file_exists($filePath))
        return array();

    $flags = FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES;
    $ids = file($filePath, $flags);
    return $ids;
}


function getDefaultOrder() {
    $result = array('order' => array(), 'central_id' => "");
    return $result;
}


//function getOrder($blastId, $items, $dbFile, $jobDataDir, $blastCacheDir, $gnn) {
//
//    $cwd = getcwd();
//
//    $resultsDb = new SQLite3($dbFile);
//
//    $centralId = "";
//    $blastIds = array();
//    foreach ($items as $item) {
//        if (is_numeric($item)) {
//            $ids = getIdsFromCluster($item, $jobDataDir);
//
//            if (!$centralId) {
//                $sql = "SELECT * FROM cluster_degree where cluster_num = '$item'";
//                $dbQuery = $resultsDb->query($sql);
//                $row = $dbQuery->fetchArray(SQLITE3_ASSOC);
//
//                if (!$row && count($ids) > 0)
//                    $centralId = $ids[0];
//                else
//                    $centralId = $row["accession"];
//            }
//
//            foreach ($ids as $id)
//                $blastIds[$id] = 1;
////            array_push($blastIds, $ids);
//        } else if ($item) {
//            if (!$centralId)
//                $centralId = $item;
//            $blastIds[$item] = 1;
////            array_push($blastIds, $item);
//        }
//    }
//
//    $resultsDb->close();
//
//    if (!$centralId)
//        return FALSE;
//
//    $index = array_search($centralId, $blastIds);
//    if ($index !== FALSE)
//        unset($blastIds[$index]);
//
//    $blastMod = settings::get_blast_module();
//    $blastDir = "$blastCacheDir/blast-$blastId";
//    if (!file_exists($blastDir))
//        mkdir($blastDir);
//
//    $blastInputFile = "$blastDir/blast.input";
//    $blastOutputFile = "$blastDir/blast.output";
//    $blasthits = 100000; //TODO: find this
//    $evalue = "1e-5"; //TODO: find this
//
//    $exec = "source /etc/profile.d/modules.sh; ";
//    $exec .= "module load $blastMod; ";
//    $exec .= "fastacmd -d $jobDataDir/blast/database -s $centralId > $blastInputFile; ";
//    $exec .= "blastall -p blastp -i $blastInputFile -d $jobDataDir/blast/database -m 8 -e $evalue -b $blasthits -o $blastOutputFile";
//
//    $exitStatus = 1;
//    $outputArray = array();
//    $cmdOutput = exec($exec, $outputArray, $exitStatus);
//    $cmdOutput = trim(rtrim($cmdOutput));
//    //TODO: handle errors
//
//    $order = getIdOrder($blastOutputFile, $blastIds);
//
//    $result = array('order' => $order, 'central_id' => $centralId);
//    return $result;
//}
//function getIdOrder($blastOutputFile, $blastIds) {
//    $order = array();
//
//    $data = file_get_contents($blastOutputFile);
//    $lines = preg_split("/(\r\n|\r|\n)/", $data);
//    foreach ($lines as $line) {
//        $line = rtrim($line);
//        $parts = explode("\t", $line);
//        if (count($parts) < 11)
//            continue;
//
//        if (array_key_exists($parts[1], $blastIds)) {
//            $order[$parts[1]] = array(floatval($parts[10]), floatval($parts[2]));
//        }
//    }
//
//    return $order;
//}

?>
