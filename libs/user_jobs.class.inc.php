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

        $expDate = $this->get_start_date_window();
        $sql = "SELECT gnn_id, gnn_key, gnn_filename, gnn_time_completed FROM gnn " .
            "WHERE gnn_email='" . $this->user_email . "' AND " .
            "(gnn_time_completed >= '$expDate' OR gnn_status = 'RUNNING')" .
            "ORDER BY gnn_status, gnn_time_completed DESC";
        $rows = $db->query($sql);

        foreach ($rows as $row) {
            $comp = $row["gnn_time_completed"];
            if (substr($comp, 0, 4) == "0000")
                $comp = "RUNNING";
            array_push($this->jobs, array("id" => $row["gnn_id"], "key" => $row["gnn_key"], "filename" => $row["gnn_filename"],
                                          "completed" => $comp));
        }
    }

    public function save_user($db, $email) {
        $this->user_email = $email;

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

        //setcookie(user_jobs::USER_TOKEN_NAME, $this->user_token, time() + user_jobs::EXPIRATION_SECONDS);

        return true;
    }

    public function get_cookie() {
        $dom = parse_url(settings::get_web_root(), PHP_URL_HOST);
        $maxAge = 30 * 86400; // 30 days
        $tokenField = user_jobs::USER_TOKEN_NAME;
        $token = $this->user_token;
        return "$tokenField=$token;max-age=$maxAge";
    }

    public function get_start_date_window() {
        $numDays = settings::get_retention_days();
        $dt = new DateTime();
        $pastDt = $dt->sub(new DateInterval("P${numDays}D"));
        $mysqlDate = $pastDt->format("Y-m-d");
        return $mysqlDate;
    }

    public function get_jobs() {
        return $this->jobs;
    }

    public function is_loaded() {
        return $this->loaded;
    }
}

?>

