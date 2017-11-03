<?php

class arrow_database {

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

    private function load_data() {
        $this->db_file = functions::get_uploaded_diagram_file($this->id);
        $db = new SQLite3($this->db_file);

        $sql = "SELECT * FROM metadata";
        $dbQuery = $db->query($sql);

        $row = $dbQuery->fetchArray();
        if (!$row)
        {
            $db->close();
            return false;
        }

        $this->cooccurrence = $row['cooccurrence'];
        $this->nb_size = $row['neighborhood_size'];
        $this->gnn_name = $row['name'];

        $db->close();

        return true;
    }

    public function get_arrow_data_file() {
        return $this->db_file;
    }

    public function get_name() {
        return $this->db_name;
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

