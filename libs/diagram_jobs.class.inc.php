<?php

require_once "../includes/main.inc.php";
require_once "functions.class.inc.php";
require_once "Mail.php";
require_once "Mail/mime.php";

class diagram_jobs {

    private $db;
    private $beta;

    public function __construct($db) {
        $this->db = $db;
        $this->beta = settings::get_release_status();
    }

    public function get_new_jobs() {
        $jobs = array();

        $sql = "SELECT * FROM diagram WHERE diagram_status = '" . __NEW__ . "'";
        $rows = $this->db->query($sql);

        foreach ($rows as $row) {
            array_push($jobs, $row['diagram_id']);
        }

        return $jobs;
    }

    public static function get_key($db, $id) {
        $sql = "SELECT diagram_key FROM diagram WHERE diagram_id = $id";
        $result = $db->query($sql);
        if (!$result)
            return "";
        else
            return $result[0]['diagram_key'];
    }
}


?>

