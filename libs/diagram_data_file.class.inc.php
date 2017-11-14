<?php

class diagram_data_file {

    private $id;
    private $gnn_name;
    private $db_file;
    private $loaded;
    private $nb_size;
    private $cooccurrence;

    public function __construct($id) {
        $this->id = $id;
        if ($id)
            $this->loaded = $this->load_data();
        else
            $this->loaded = false;
    }

    public static function create($db, $email, $tmp_filename, $filename) {
        $result = false;

        $key = functions::generate_key();
        $title = self::get_diagram_title_from_file($filename);

        $insert_array = array(
            'diagram_key' => $key,
            'diagram_email' => $email,
            'diagram_title' => $title,
        );

        $uploadPrefix = settings::get_diagram_upload_prefix();
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $result = $db->build_insert('diagram', $insert_array);
        if ($result) {
            functions::copy_to_uploads_dir($tmp_filename, $filename, $result, $uploadPrefix, $ext);
        } else {
            return false;
        }

        $info = array('id' => $result, 'key' => $key);
        return $info;
    }

    private function load_data() {
        $this->db_file = functions::get_diagram_file_path($this->id);

        if (!file_exists($this->db_file))
            return false;

        $db = new SQLite3($this->db_file);

        if (functions::sqlite_table_exists($db, "metadata")) {
            $sql = "SELECT * FROM metadata";
            $dbQuery = $db->query($sql);

            $row = $dbQuery->fetchArray();
            if (!$row)
            {
                $db->close();
                return false;
            }

            if (array_key_exists("cooccurrence", $row))
                $this->cooccurrence = $row['cooccurrence'];
            else
                $this->cooccurrence = $row['coocurrence']; //TODO: remove this in production; there was a typo earlier.
            $this->nb_size = $row['neighborhood_size'];
            $this->gnn_name = $row['name'];
        } else {
            $this->cooccurrence = "";
            $this->nb_size = "";
            $this->gnn_name = "";
        }

        $db->close();

        return true;
    }

    private static function get_diagram_title_from_file($file) {
        $file = preg_replace("/\.sqlite$/", "", $file);
        $file = preg_replace("/_arrow_data/", "", $file);
        return $file;
        /*
        try {
            $db = new SQLite3($file);

            $sql = "SELECT * FROM metadata";
            $dbQuery = $db->query($sql);

            $row = $dbQuery->fetchArray();
            if (!$row)
            {
                $db->close();
                return "";
            }
            $gnn_name = $row['name'];

            $db->close();

            return $gnn_name;
        } catch (Exception $e) {
            return "";
        }
         */
    }

    public function get_diagram_data_file() {
        return $this->db_file;
    }

    public function get_name() {
        return $this->gnn_name;
    }

    public function get_neighborhood_size() {
        return $this->nb_size;
    }

    public function get_cooccurrence() {
        return $this->cooccurrence;
    }

    public function is_loaded() {
        return $this->loaded;
    }
}

?>

