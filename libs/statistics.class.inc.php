<?php

class statistics 
{

    public static function num_per_month($db, $recentOnly = false) {
        $sql = "SELECT count(1) as count, ";
        $sql .= "MONTHNAME(gnn_time_created) as month, ";
        $sql .= "YEAR(gnn_time_created) as year ";
        $sql .= "FROM gnn ";
        if ($recentOnly)
            $sql .= "WHERE TIMESTAMPDIFF(MONTH,gnn_time_created,CURRENT_TIMESTAMP) <= 7 ";
        $sql .= "GROUP BY MONTH(gnn_time_created),YEAR(gnn_time_created) ORDER BY year,MONTH(gnn_time_created)";
        return $db->query($sql);
    }

    public static function get_unique_users($db) {
        $sql = "SELECT DISTINCT(gnn_email) as email, ";
        $sql .= "MAX(gnn_time_created) as last_job_time, ";
        $sql .= "COUNT(1) as num_jobs ";
        $sql .= "FROM gnn ";
        $sql .= "GROUP BY gnn_email ";
        $sql .= "ORDER BY gnn_email ASC";
        return $db->query($sql);
    }

    public static function num_unique_users($db) {
        $result = self::get_unique_users($db);
        return count($result);
    }

    public static function num_jobs($db) {
        $sql = "SELECT count(*) as count from gnn";
        $result = $db->query($sql);
        return $result[0]['count'];
    }

    public static function get_jobs($db,$month,$year) {
        $sql = "SELECT gnn.gnn_email as 'Email', ";
        $sql .= "gnn.gnn_id as 'GNT ID', ";
        $sql .= "gnn.gnn_key as 'Key', ";
        $sql .= "gnn.gnn_time_created as 'Time Created', ";
        $sql .= "gnn.gnn_time_started as 'Time Started', ";
        $sql .= "gnn.gnn_time_completed as 'Time Completed', ";
        $sql .= "gnn.gnn_size as 'Neighborhood Size', ";
        $sql .= "gnn.gnn_cooccurrence as 'Input Cooccurrance', ";
        $sql .= "gnn.gnn_filename as 'Filename' ";
        $sql .= "FROM gnn ";
        $sql .= "WHERE MONTH(gnn.gnn_time_created)='" . $month . "' ";
        $sql .= "AND YEAR(gnn.gnn_time_created)='" . $year . "' ";
        $sql .= "ORDER BY gnn.gnn_id ASC";
        return $db->query($sql);
    }

    public static function get_daily_jobs($db,$month,$year) {
        $sql = "SELECT count(1) as count, ";
        $sql .= "DATE(gnn.gnn_time_created) as day ";
        $sql .= "FROM gnn ";
        $sql .= "WHERE MONTH(gnn.gnn_time_created)='" . $month . "' ";
        $sql .= "AND YEAR(gnn.gnn_time_created)='" . $year . "' ";
        $sql .= "GROUP BY DATE(gnn.gnn_time_created) ";
        $sql .= "ORDER BY DATE(gnn.gnn_time_created) ASC";
        $result = $db->query($sql);
        return self::get_day_array($result,'day','count',$month,$year);
    }

    public static function get_day_array($data,$day_column,$data_column,$month,$year) {
        $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        $new_data = array();
        for($i=1;$i<=$days;$i++){
            $exists = false;
            if (count($data) > 0) {
                foreach($data as $row) {
                    $day = date("d",strtotime($row[$day_column]));
                    if ($day == $i) {
                        //array_push($new_data,array($day_column=>$i,
                        //                      $data_column=>$row[$data_column]));
                        array_push($new_data,$row);
                        $exists = true;
                        break(1);
                    }
                }
            }
            if (!$exists) {
                $day = $year . "-" . $month . "-" . $i;
                array_push($new_data,array($day_column=>$day,$data_column=>0));
            }
            $exists = false;
        }
        return $new_data;
    }

}


?>
