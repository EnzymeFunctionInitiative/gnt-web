<?php
require_once("const.class.inc.php");

class functions {

    //Possible errors when you upload a file
    private static $upload_errors = array(
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        3 => 'The uploaded file was only partially uploaded.',
        4 => 'No file was uploaded.',
        6 => 'Missing a temporary folder.',
        7 => 'Failed to write file to disk.',
        8 => 'File upload stopped by extension.',
    );

    public static function verify_email($email) {
        $email = strtolower($email);
        $hostname = "";
        if (strpos($email,"@")) {
            list($prefix,$hostname) = explode("@",$email);
        }

        $valid = 1;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $valid = 0;
        }
        elseif (($hostname != "") && (!checkdnsrr($hostname,"MX"))) {
            $valid = 0;
        }
        return $valid;

    }

    public static function verify_neighborhood_size($nbSize) {
        $max_nbSize = 100;
        $valid = 1;
        if ($nbSize == "") {
            $valid = 0;
        }
        if (!preg_match("/^\d+$/",$nbSize)) {
            $valid = 0;
        }
        if ($nbSize < 1) {
            $valid = 0;
        }
        return $valid;
    }

    public static function verify_evalue($evalue) {
        $max_evalue = 100;
        $valid = 1;
        if ($evalue == "") {
            $valid = 0;
        }
        if (!preg_match("/^\d+$/",$evalue)) {
            $valid = 0;
        }
        if ($evalue > $max_evalue) {
            $valid = 0;
        }
        return $valid;
    }

    public static function verify_blast_input($blast_input) {
        $blast_input = strtolower($blast_input);
        $valid = 1;
        if (!strlen($blast_input)) {
            $valid = 0;
        }
        if (strlen($blast_input) > 65534) {
            $valid = 0;
        }
        if (preg_match('/[^a-z-* \n\t\r]/',$blast_input)) {
            $valid = 0;
        }
        return $valid;
    }

    public static function verify_max_seqs($max_seqs) {
        $valid = 0;
        if ($max_seqs == "") {
            $valid = 0;
        }
        elseif (!preg_match("/^[1-9][0-9]*$/",$max_seqs)) {
            $valid = 0;
        }
        elseif ($max_seqs > settings::get_max_blast_seq()) {
            $valid = 0;
        }
        else {
            $valid = 1;
        }
        return $valid;
    }

    public static function get_upload_error($value) {
        return self::$upload_errors[$value];

    }

    public static function log_message($message) {
        $current_time = date('Y-m-d H:i:s');
        $full_msg = $current_time . ": " . $message . "\n";
        if (self::log_enabled()) {
            file_put_contents(self::get_log_file(),$full_msg,FILE_APPEND | LOCK_EX);
        }
        echo $full_msg;

    }

    public static function get_log_file() {
        $log_file = __LOG_FILE__;
        if (!$log_file) {
            touch($log_file);
        }
        return $log_file;

    }

    public static function log_enabled() {
        return __ENABLE_LOG__;
    }

    public static function get_gnn_jobs($db, $status = 'NEW') {
        $sql = "SELECT * ";
        $sql .= "FROM gnn ";
        $sql .= "WHERE gnn_status='" . $status . "' ";
        $sql .= "ORDER BY gnn_time_created ASC ";
        $result = $db->query($sql);
        return $result;
    }

    public static function get_is_debug() {
        return getenv('EFI_DEBUG') ? true : false;
    }

    # recursively remove a directory
    public static function rrmdir($dir) {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file))
                self::rrmdir($file);
            else
                unlink($file);
        }
        rmdir($dir);
    }

    public static function generate_key() {
        $key = uniqid(rand(), true);
        $hash = sha1($key);
        return $hash;
    }

    public static function is_diagram_upload_id_valid($id) {
        // Make sure the ID only contains numbers and letters to prevent attacks.
        $hasInvalidChars = preg_match('/[^A-Za-z0-9]/', $id);
        if ($hasInvalidChars === 1)
            return false;

        return file_exists(self::get_diagram_file_path($id));
    }

    public static function get_diagram_file_name($id) {
        return "$id." . settings::get_diagram_extension();
    }

    public static function get_diagram_file_path($id) {
        $filePath = settings::get_diagram_output_dir() . "/$id/" . self::get_diagram_file_name($id);
        return $filePath;
    }

    public static function copy_to_uploads_dir($tmp_file, $uploaded_filename, $id, $prefix = "", $forceExtension = "") {
        $uploads_dir = settings::get_uploads_dir();

        // By this time we have verified that the uploaded file is valid. Now we need to retain the
        // extension in case the file is a zipped file.
        if ($forceExtension)
            $file_type = $forceExtension;
        else
            $file_type = strtolower(pathinfo($uploaded_filename, PATHINFO_EXTENSION));
        $filename = $prefix . $id . "." . $file_type;
        $full_path = $uploads_dir . "/" . $filename;
        if (is_uploaded_file($tmp_file)) {
            if (move_uploaded_file($tmp_file,$full_path)) { return $filename; }
        }
        else {
            if (copy($tmp_file,$full_path)) { return $filename; }
        }
        return false;
    }

    public static function sqlite_table_exists($sqliteDb, $tableName) {
        // Check if the table exists
        $checkSql = "SELECT name FROM sqlite_master WHERE type='table' AND name='$tableName'";
        $dbQuery = $sqliteDb->query($checkSql);
        if ($dbQuery->fetchArray()) {
            return true;
        } else {
            return false;
        }
    }

    public static function decode_object($json) {
        $data = json_decode($json, true);
        if (!$data)
            return array();
        else
            return $data;
    }

    public static function encode_object($obj) {
        return json_encode($obj);
    }

    public static function update_results_object_tmpl($db, $prefix, $table, $column, $id, $data) {
        $theCol = "${prefix}_${column}";

        $sql = "SELECT $theCol FROM $table WHERE ${prefix}_id='$id'";
        $result = $db->query($sql);
        if (!$result)
            return NULL;
        $result = $result[0];
        $results_obj = self::decode_object($result[$theCol]);

        foreach ($data as $key => $value)
            $results_obj[$key] = $value;
        
        $json = self::encode_object($results_obj);
        
        $sql = "UPDATE $table SET $theCol = '" . $db->escape_string($json) . "'";
        $sql .= " WHERE ${prefix}_id='$id' LIMIT 1";
        $result = $db->non_select_query($sql);

        return $result;
    }

    public static function get_verbose_job_type($diagramType) {
        $title = "";
        if ($diagramType == DiagramJob::Uploaded || $diagramType == DiagramJob::UploadedZip)
            $title = "Uploaded diagram data file";
        elseif ($diagramType == DiagramJob::BLAST)
            $title = "Sequence BLAST";
        elseif ($diagramType == DiagramJob::IdLookup || $diagramType == "LOOKUP") // "lookup" is for legacy"
            $title = "Sequence ID lookup";
        elseif ($diagramType == DiagramJob::FastaLookup)
            $title = "FASTA header ID lookup";
        return $title;
    }

    public static function get_diagram_id_field($type) {
        switch ($type) {
            case DiagramJob::BLAST:
            case DiagramJob::IdLookup:
            case DiagramJob::FastaLookup:
                return "direct-id";
            case DiagramJob::Uploaded:
            case DiagramJob::UploadedZip:
                return "upload-id";
            default:
                return "gnn-id";
        }
    }

    public static function is_valid_file_type($filetype) {
        $filetypes = explode(" ", __VALID_FILE_TYPE__);
        return in_array($filetype, $filetypes);
    }

    public static function is_valid_diagram_file_type($filetype) {
        $filetypes = explode(" ", __VALID_DIAGRAM_FILE_TYPE__);
        return in_array($filetype, $filetypes);
    }

    public static function is_valid_id_file_type($filetype) {
        $filetypes = explode(" ", __VALID_ID_FILE_TYPE__);
        return in_array($filetype, $filetypes);
    }

    public static function get_update_message() {
        return "The GNT database has been updated to use UniProt " . 
            settings::get_uniprot_version() . " and ENA " . settings::get_ena_version() . ".";
    }

}
?>
