<?php

require_once "../includes/main.inc.php";
require_once "functions.class.inc.php";

class user_jobs {

    const USER_TOKEN_NAME = "token";
    const EXPIRATION_SECONDS = 2592000; // 30 days

    private $user_token;
    private $user_email = "";
    private $loaded;
    private $jobs;

    public static function has_token_cookie() {
        return isset($_COOKIE[user_jobs::USER_TOKEN_NAME]);
    }

    public static function get_user_token() {
        return $_COOKIE[user_jobs::USER_TOKEN_NAME];
    }

    public function __construct() {
        $this->jobs = array();
    }

    public function load_jobs($db, $token) {
        $this->user_token = $token;

        $sql = "SELECT user_email FROM user_token WHERE user_id='" . $this->user_token . "'";
        $row = $db->query($sql);
        if (!$row)
            return;

        $this->user_email = $row[0]["user_email"];
        if (!$this->user_email)
            return;

        $sql = "SELECT gnn_id, gnn_key, gnn_filename, gnn_time_completed FROM gnn WHERE gnn_email='" . $this->user_email . "' ORDER BY gnn_time_completed";
        $rows = $db->query($sql);

        foreach ($rows as $row) {
            array_push($this->jobs, array("id" => $row["gnn_id"], "gnn_key" => $row["gnn_key"], "gnn_filename" => $row["gnn_filename"],
                                          "completed" => $row["gnn_time_completed"]));
        }
    }

    public function save_user($db, $gnnId) {
        $sql = "SELECT gnn_email FROM gnn WHERE gnn_id=" . $gnnId;
        $rows = $db->query($sql);

        if (!$rows)
            return false;

        $this->user_email = $rows[0]["gnn_email"];

        $sql = "SELECT user_id, user_email FROM user_token WHERE user_email='" . $this->user_email . "'";
        $rows = $db->query($sql);

        $isUpdate = false;
        if ($rows && count($rows) > 0) {
            $isUpdate = true;
            $this->user_token = $rows[0]["user_id"];
        } else {
            $this->user_token = functions::generate_key();
        }

        $insert_array = array("user_id" => $this->user_token, "user_email" => $this->user_email);
        if (!$isUpdate) {
            $db->build_insert("user_token", $insert_array);
        }

        setcookie(user_jobs::USER_TOKEN_NAME, $this->user_token, time() + user_jobs::EXPIRATION_SECONDS);

        return true;
    }

    public function get_jobs() {
        return $this->jobs;
    }

    public function is_loaded() {
        return $this->loaded;
    }
}

?>

