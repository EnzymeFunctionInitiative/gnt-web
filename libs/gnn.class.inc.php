<?php

require_once('../includes/main.inc.php');
require_once('Mail.php');
require_once('Mail/mime.php');

class gnn {

    ////////////////Private Variables//////////

    protected $db; //mysql database object
    protected $id;
    protected $email;
    protected $key;
    protected $filename;
    protected $basefilename;
    protected $size;
    protected $cooccurrence;
    protected $time_created;
    protected $time_started;
    protected $time_completed;
    protected $ssn_nodes;
    protected $ssn_edges;
    protected $gnn_nodes;
    protected $gnn_edges;
    protected $gnn_pfams;
    protected $log_file = "log.txt";
    protected $eol = PHP_EOL;
    protected $finish_file = "gnn.completed";
    protected $pbs_number;
    protected $status;
    protected $beta;
    protected $is_legacy = false;

    ///////////////Public Functions///////////

    public function __construct($db,$id = 0) {
        $this->db = $db;

        if ($id) {
            $this->load_gnn($id);
        }

        $this->beta = settings::get_release_status();
    }

    public function __destruct() {
    }

    public function get_id() { return $this->id; }
    public function get_email() { return $this->email; }
    public function get_key() { return $this->key; }
    public function get_size() { return $this->size; }
    public function get_cooccurrence() { return $this->cooccurrence; }
    public function get_filename() { return $this->filename; }
    public function get_time_created() { return $this->time_created; }
    public function get_time_started() { return $this->time_started; }
    public function get_time_completed() { return $this->time_completed; }
    public function get_ssn_nodes() { return $this->ssn_nodes; }
    public function get_ssn_edges() { return $this->ssn_edges; }
    public function get_gnn_pfams() { return $this->gnn_pfams; }
    public function get_gnn_nodes() { return $this->gnn_nodes; }
    public function get_gnn_edges() { return $this->gnn_edges; }

    public function get_full_path() {
        $uploads_dir = settings::get_uploads_dir();
        return $uploads_dir . "/" . $this->get_id() . "." . pathinfo($this->filename, PATHINFO_EXTENSION);
    }

    public static function create2($db, $email, $size, $cooccurrence, $tmp_filename, $filename) {
        return self::create_shared($db, $email, $size, $cooccurrence, $tmp_filename, $filename);
    }

    private static function create_shared($db, $email, $size, $cooccurrence, $tmp_filename, $filename) {
        $result = false;
        $key = self::generate_key();
        $insert_array = array(
            'gnn_email' => $email,
            'gnn_size' => $size,
            'gnn_key' => $key,
            'gnn_filename' => $filename,
            'gnn_cooccurrence' => $cooccurrence,
            'gnn_status' => __NEW__);
        $result = $db->build_insert('gnn',$insert_array);
        if ($result) {	
            functions::copy_to_uploads_dir($tmp_filename, $filename, $result);
        } else {
            return false;
        }
        $info = array('id' => $result, 'key' => $key);
        return $info;
    }

    public static function create($db, $email, $size, $tmp_filename, $filename, $cooccurrence) {
        $info = create_shared($db, $email, $size, $cooccurrence, $tmp_filename, $filename);
        if ($info === false)
            return 0;
        else
            return $info['id'];
    }

    public function unzip_file() { 
        $file = $this->get_full_path();
        $parts = pathinfo($file);
        $ext = strtolower($parts['extension']);
        $dir = $parts['dirname'];
        $name = $parts['filename'];

        $outfile = $file;

        if ($ext == "zip") {
            $outfile = "$dir/$name.xgmml";
            $exec = "unzip -p $file > $outfile";
            $output_array = array();
            $output = exec($exec, $output_array, $exit_status);
        } else if ($ext == "gz") {
            $outfile = "$dir/$name.xgmml";
            $exec = "gunzip $file > $outfile";
            $output_array = array();
            $output = exec($exec, $output_array, $exit_status);
        }

        error_log($outfile);

        return $outfile; 
    }


    public function run_gnn() {
        $this->delete_outputs();
        $this->set_time_started();

        $ssnin = $this->unzip_file();

        $binary = settings::get_gnn_script();

        mkdir(settings::get_output_dir() . "/" . $this->get_id());

        $exec = "source /etc/profile\n";
        $exec .= "module load " . settings::get_efidb_module() . "\n";
        $exec .= "module load " . settings::get_gnn_module() . "\n";
        $exec .= $binary . " ";
        $exec .= " -ssnin " . $ssnin;
        $exec .= " -n " . $this->get_size();
        $exec .= " -gnn " . $this->get_gnn();
        $exec .= " -ssnout " . $this->get_color_ssn();
        $exec .= " -incfrac " . $this->get_cooccurrence();
        $exec .= " -stats " . $this->get_stats();
        $exec .= " -warning-file " . $this->get_warning_file();
        $exec .= " -pfam " . $this->get_pfam_hub();
        $exec .= " -pfam-dir " . $this->get_pfam_data_dir() ;
        $exec .= " -pfam-zip " . $this->get_pfam_data_zip_file();
        $exec .= " -id-dir " . $this->get_cluster_data_dir() ;
        $exec .= " -id-zip " . $this->get_cluster_data_zip_file();
        $exec .= " -id-out " . $this->get_id_table_file();
        $exec .= " -none-dir " . $this->get_pfam_none_dir();
        $exec .= " -none-zip " . $this->get_pfam_none_zip_file();
        $exec .= " -arrow-file " . $this->get_diagram_data_file();
        $exec .= " -cooc-table " . $this->get_cooc_table_file();
        $exec .= " -hub-count-file \"" . $this->get_hub_count_file() . "\"";
        if (settings::get_cluster_scheduler())
            $exec .= " -scheduler " . settings::get_cluster_scheduler();

        error_log("Job ID: " . $this->get_id());
        error_log("Exec: " . $exec);

        $output_array = array();
        $output = exec($exec,$output_array,$exit_status);
        $output = trim(rtrim($output));

        error_log("Job ID: " . $this->get_id() . ", Exit Status: " . $exit_status);

        $this->set_time_completed();
        $formatted_output = implode("\n",$output_array);

        file_put_contents($this->get_log_file(), $exec . "\n");
        file_put_contents($this->get_log_file(), $formatted_output, FILE_APPEND);

        if (($exit_status == 0) && (strpos($output,'clustergnn.pl finished') !== false)) {
            $this->set_gnn_stats();
            $this->set_ssn_stats();
            return array('RESULT'=>true,'OUTPUT'=>$formatted_output);
        }
        else {
            $this->email_error($formatted_output); 
            return array('RESULT'=>false,'OUTPUT'=>$formatted_output);
        }

    }

    public function run_gnn_async($is_debug = false) {

        $ssnin = $this->get_full_path();

        $binary = settings::get_gnn_script();

        $out_dir = settings::get_output_dir() . "/" . $this->get_id();
        $target_ssnin = $out_dir . "/" . $this->get_id() . "." . pathinfo($ssnin, PATHINFO_EXTENSION);

        if (@file_exists($out_dir))
            functions::rrmdir($out_dir);
        if (!$is_debug && !file_exists($out_dir))
            mkdir($out_dir);
        chdir($out_dir);
        copy($ssnin, $target_ssnin);

        $sched = settings::get_cluster_scheduler();

        $exec = "source /etc/profile\n";
        $exec .= "module load " . settings::get_efidb_module() . "\n";
        $exec .= "module load " . settings::get_gnn_module() . "\n";
        $exec .= $binary . " ";
        $exec .= " -ssnin \"" . $target_ssnin . "\"";
        $exec .= " -nb-size " . $this->get_size();
        $exec .= " -cooc " . $this->get_cooccurrence();
        $exec .= " -gnn \"" . $this->get_gnn() . "\"";
        $exec .= " -ssnout \"" . $this->get_color_ssn() . "\"";
        $exec .= " -stats \"" . $this->get_stats() . "\"";
        $exec .= " -warning-file \"" . $this->get_warning_file() . "\"";
        $exec .= " -pfam \"" . $this->get_pfam_hub() . "\"";
        $exec .= " -pfam-dir \"" . $this->get_pfam_data_dir()  . "\"";
        $exec .= " -pfam-zip \"" . $this->get_pfam_data_zip_file() . "\"";
        $exec .= " -id-dir \"" . $this->get_cluster_data_dir()  . "\"";
        $exec .= " -id-zip \"" . $this->get_cluster_data_zip_file() . "\"";
        $exec .= " -id-out \"" . $this->get_id_table_file() . "\"";
        $exec .= " -none-dir \"" . $this->get_pfam_none_dir() . "\"";
        $exec .= " -none-zip \"" . $this->get_pfam_none_zip_file() . "\"";
        $exec .= " -fasta-dir \"" . $this->get_fasta_dir() . "\"";
        $exec .= " -fasta-zip \"" . $this->get_fasta_zip_file() . "\"";
        $exec .= " -arrow-file \"" . $this->get_diagram_data_file() . "\"";
        $exec .= " -cooc-table \"" . $this->get_cooc_table_file() . "\"";
        $exec .= " -hub-count-file \"" . $this->get_hub_count_file() . "\"";
        if ($sched)
            $exec .= " -scheduler $sched";

        //TODO: remove this debug message
        error_log("Job ID: " . $this->get_id());
        error_log("Exec: " . $exec);

        $exit_status = 1;

        file_put_contents($this->get_log_file(), $exec . "\n");

        $output_array = array();
        $output = exec($exec, $output_array, $exit_status);
        $output = trim(rtrim($output));

        if ($sched == "slurm")
            $pbs_job_number = $output;
        else
            $pbs_job_number = substr($output, 0, strpos($output, "."));

        if ($pbs_job_number && !$exit_status) {
            if (!$is_debug) {
                $this->set_pbs_number($pbs_job_number);
                $this->set_time_started();
                $this->email_started();
                $this->set_status(__RUNNING__);
            }

            //TODO: remove this debug message
            error_log("Job ID: " . $this->get_id() . ", Exit Status: " . $exit_status);

            return array('RESULT' => true, 'PBS_NUMBER' => $pbs_job_number, 'EXIT_STATUS' => $exit_status, 'MESSAGE' => 'Job Successfully Submitted');
        }
        else {
            error_log("There was an error submitting the GNN job: $output  // exit status: $exit_status  " . join(',', $output_array));
            return array('RESULT' => false, 'EXIT_STATUS' => $exit_status, 'MESSAGE' => $output_array[18]);
        }
    }

    public function complete_gnn() {
        $this->set_gnn_stats();
        $this->set_ssn_stats();

        $this->set_status(__FINISH__); 
        $this->set_time_completed();
        $this->email_complete();
    }

    public function error_gnn() {
        $this->set_status(__FAILED__);
        $this->email_error();
    }

    public function get_file_prefix() {
        if ($this->is_legacy)
            return $this->get_id();
        else
            return $this->get_id() . "_" . $this->basefilename;
    }

    public function get_log_file() {
        $filename = $this->log_file;
        $output_dir = settings::get_output_dir();
        $full_path = $output_dir . "/" . $this->get_id() . "/" . $filename;
        return $full_path;
    }

    public function get_color_ssn() {
        $name = $this->is_legacy ? "color" : "coloredssn";
        return $this->shared_get_full_file_path("_${name}", ".xgmml");
    }
    public function get_relative_color_ssn() {
        $name = $this->is_legacy ? "color" : "coloredssn";
        return $this->shared_get_relative_file_path("_${name}", ".xgmml");
    }

    public function get_gnn() {
        $name = $this->is_legacy ? "gnn" : "ssn_cluster_gnn";
        return $this->shared_get_full_file_path("_${name}", ".xgmml");
    }
    public function get_relative_gnn() {
        $name = $this->is_legacy ? "gnn" : "ssn_cluster_gnn";
        return $this->shared_get_relative_file_path("_${name}", ".xgmml");
    }

    public function get_pfam_hub() {
        $name = $this->is_legacy ? "pfam" : "pfam_family_gnn";
        return $this->shared_get_full_file_path("_${name}", ".xgmml");
    }
    public function get_relative_pfam_hub() {
        $name = $this->is_legacy ? "pfam" : "pfam_family_gnn";
        return $this->shared_get_relative_file_path("_${name}", ".xgmml");
    }
    public function get_pfam_hub_zipfile() {
        $name = $this->is_legacy ? "pfam" : "pfam_family_gnn";
        return $this->shared_get_full_file_path("_${name}", ".zip");
    }

    public function get_warning_file() {
        if ($this->is_legacy)
            return "";
        return $this->shared_get_full_file_path("_nomatches_noneighbors", ".txt");
    }
    public function get_relative_warning_file() {
        if ($this->is_legacy)
            return "";
        return $this->shared_get_relative_file_path("_nomatches_noneighbors", ".txt");
    }

    public function get_cluster_data_dir() {
        return $this->shared_get_dir("cluster-data");
    }
    public function get_pfam_data_dir() {
        return $this->shared_get_dir("pfam-data");
    }
    public function get_pfam_none_dir() {
        return $this->shared_get_dir("pfam-none");
    }
    public function get_fasta_dir() {
        return $this->shared_get_dir("fasta");
    }
    private function shared_get_dir($name) {
        $output_dir = $this->get_output_dir();
        $full_path = $output_dir . "/" . $name;
        return $full_path;
    }

    public function get_relative_color_ssn_zip_file() {
        if ($this->is_legacy)
            return "";
        $ssnFile = $this->get_relative_color_ssn();
        return preg_replace("/\.xgmml$/", ".zip", $ssnFile);
    }
    public function get_relative_gnn_zip_file() {
        if ($this->is_legacy)
            return "";
        $gnnFile = $this->get_relative_gnn();
        return preg_replace("/\.xgmml$/", ".zip", $gnnFile);
    }
    public function get_relative_pfam_hub_zip_file() {
        if ($this->is_legacy)
            return "";
        $pfamFile = $this->get_relative_pfam_hub();
        return preg_replace("/\.xgmml$/", ".zip", $pfamFile);
    }
    public function get_cluster_data_zip_file() {
        return $this->shared_get_full_file_path("_UniProt_IDs", ".zip");
    }
    public function get_relative_cluster_data_zip_file() {
        if ($this->is_legacy)
            return "";
        return $this->shared_get_relative_file_path("_UniProt_IDs", ".zip");
    }

    public function get_fasta_zip_file() {
        if ($this->is_legacy)
            return "";
        return $this->shared_get_full_file_path("_FASTA", ".zip");
    }
    public function get_relative_fasta_zip_file() {
        if ($this->is_legacy)
            return "";
        return $this->shared_get_relative_file_path("_FASTA", ".zip");
    }

    public function get_pfam_none_zip_file() {
        return $this->shared_get_full_file_path("_no_pfam_neighbors", ".zip");
    }
    public function get_relative_pfam_none_zip_file() {
        if ($this->is_legacy)
            return "";
        return $this->shared_get_relative_file_path("_no_pfam_neighbors", ".zip");
    }

    public function get_pfam_data_zip_file() {
        return $this->shared_get_full_file_path("_pfam_mapping", ".zip");
    }
    public function get_relative_pfam_data_zip_file() {
        if ($this->is_legacy)
            return "";
        return $this->shared_get_relative_file_path("_pfam_mapping", ".zip");
    }

    public function get_id_table_file() {
        return $this->shared_get_full_file_path("_mapping_table", ".txt");
    }
    public function get_relative_id_table_file() {
        if ($this->is_legacy)
            return "";
        return $this->shared_get_relative_file_path("_mapping_table", ".txt");
    }

    public function get_stats() {
        return $this->shared_get_full_file_path("_stats", ".txt");
    }
    public function get_relative_stats() {
        if ($this->is_legacy)
            return "";
        return $this->shared_get_relative_file_path("_stats", ".txt");
    }

    public function get_diagram_data_file() {
        return $this->shared_get_full_file_path("_arrow_data", ".sqlite");
    }
    public function get_diagram_zip_file() {
        return $this->shared_get_full_file_path("_arrow_data", ".zip");
    }
    public function get_diagram_data_file_legacy() {
        return $this->shared_get_full_file_path("_arrow_data", ".txt");
    }
    public function get_relative_diagram_data_file() {
        return $this->shared_get_relative_file_path("_arrow_data", ".sqlite");
    }
    public function get_relative_diagram_zip_file() {
        return $this->shared_get_relative_file_path("_arrow_data", ".zip");
    }
    public function does_job_have_arrows() {
        return file_exists($this->get_diagram_data_file());
    }

    public function get_cooc_table_file() {
        return $this->shared_get_full_file_path("_cooc_table", ".txt");
    }
    public function get_relative_cooc_table_file() {
        return $this->shared_get_relative_file_path("_cooc_table", ".txt");
    }

    public function get_hub_count_file() {
        return $this->shared_get_full_file_path("_hub_count", ".txt");
    }
    public function get_relative_hub_count_file() {
        return $this->shared_get_relative_file_path("_hub_count", ".txt");
    }

    public function shared_get_full_file_path($infix_type, $ext) {
        $filename = $this->get_file_prefix() . $infix_type . "_co" . $this->get_cooccurrence() . "_ns" . $this->get_size() . $ext;
        $output_dir = $this->get_output_dir();
        $full_path = $output_dir . "/" . $filename;
        return $full_path;
    }

    public function shared_get_relative_file_path($infix_type, $ext) {
        $filename = $this->get_file_prefix() . $infix_type . "_co" . $this->get_cooccurrence() . "_ns" . $this->get_size() . $ext;
        $output_dir = $this->get_rel_output_dir();
        $full_path = $output_dir . "/" . $this->get_id() . "/".  $filename;
        return $full_path;
    }

    public function get_color_ssn_filesize() {
        $file = $this->get_color_ssn();
        return $this->get_shared_file_size($file);
    }
    public function get_gnn_filesize() {
        $file = $this->get_gnn();
        return $this->get_shared_file_size($file);
    }
    public function get_warning_filesize() {
        if ($this->is_legacy)
            return 0;
        $file = $this->get_warning_file();
        return $this->get_shared_file_size($file);
    }
    public function get_cluster_data_zip_filesize() {
        if ($this->is_legacy)
            return 0;
        $file = $this->get_cluster_data_zip_file();
        return $this->get_shared_file_size($file);
    }
    public function get_fasta_zip_filesize() {
        if ($this->is_legacy)
            return 0;
        $file = $this->get_fasta_zip_file();
        return $this->get_shared_file_size($file);
    }
    public function get_pfam_none_zip_filesize() {
        if ($this->is_legacy)
            return 0;
        $file = $this->get_pfam_none_zip_file();
        return $this->get_shared_file_size($file);
    }
    public function get_pfam_data_zip_filesize() {
        if ($this->is_legacy)
            return 0;
        $file = $this->get_pfam_data_zip_file();
        return $this->get_shared_file_size($file);
    }
    public function get_stats_filesize() {
        $file = $this->get_stats();
        return $this->get_shared_file_size($file);
    }
    public function get_pfam_hub_filesize() {
        $file = $this->get_pfam_hub();
        return $this->get_shared_file_size($file);
    }
    public function get_pfam_hub_zip_filesize() {
        $file = $this->get_pfam_hub_zipfile();
        return $this->get_shared_file_size($file);
    }
    public function get_id_table_filesize() {
        if ($this->is_legacy)
            return 0;
        $file = $this->get_id_table_file();
        return $this->get_shared_file_size($file);
    }
    public function get_cooc_table_filesize() {
        $file = $this->get_cooc_table_file();
        return $this->get_shared_file_size($file);
    }
    public function get_hub_count_filesize() {
        $file = $this->get_hub_count_file();
        return $this->get_shared_file_size($file);
    }
    public function get_diagram_data_filesize() {
        $file = $this->get_diagram_data_file();
        return $this->get_shared_file_size($file);
    }
    public function get_diagram_zip_filesize() {
        $file = $this->get_diagram_zip_file();
        return $this->get_shared_file_size($file);
    }

    private function get_shared_file_size($file) {
        if (file_exists($file))
            return round(filesize($file) / 1048576, 2);
        else
            return 0;
    }

    // Legacy Jobs
    public function get_no_matches_file() {
        return $this->shared_get_full_file_path("_no_matches", ".xgmml");
    }
    public function get_relative_no_matches_file() {
        if (!$this->is_legacy)
            return "";
        return $this->shared_get_relative_file_path("_no_matches", ".xgmml");
    }
    public function get_no_matches_filesize() {
        if (!$this->is_legacy)
            return 0;
        return round(filesize($this->get_no_matches_file()) / 1048576,2);
    }
    public function get_no_neighbors_file() {
        return $this->shared_get_full_file_path("_no_neighbors", ".xgmml");
    }
    public function get_relative_no_neighbors_file() {
        if (!$this->is_legacy)
            return "";
        return $this->shared_get_relative_file_path("_no_neighbors", ".xgmml");
    }
    public function get_no_neighbors_filesize() {
        if (!$this->is_legacy)
            return 0;
        return round(filesize($this->get_no_neighbors_file()) / 1048576,2);
    }





    public function get_rel_output_dir() {
        if ($this->is_legacy)
            return settings::get_legacy_rel_output_dir();
        else
            return settings::get_rel_output_dir();
    }

    public function set_time_started() {
        $current_time = date("Y-m-d H:i:s",time());
        $sql = "UPDATE gnn SET gnn_time_started='" . $current_time . "' ";
        $sql .= "WHERE gnn_id='" . $this->get_id() . "' LIMIT 1";
        $result = $this->db->non_select_query($sql);
        if ($result) {
            $this->time_started = $current_time;
        } 
    }

    public function set_time_completed() {
        $current_time = date("Y-m-d H:i:s",time());
        $sql = "UPDATE gnn SET gnn_time_completed='" . $current_time . "' ";
        $sql .= "WHERE gnn_id='" . $this->get_id() . "' LIMIT 1";
        $result = $this->db->non_select_query($sql);
        if ($result) {
            $this->time_completed = $current_time;
        }


    }

    public function set_gnn_stats() {
        $result = $this->count_nodes_edges($this->get_gnn());
        $sql = "UPDATE gnn SET gnn_gnn_edges='" . $result['edges'] . "', ";
        $sql .= "gnn_gnn_nodes='" . $result['nodes'] . "', ";
        $sql .= "gnn_gnn_pfams='" . $result['pfams'] . "' ";
        $sql .= "WHERE gnn_id='" . $this->get_id() . "' LIMIT 1";
        $result = $this->db->non_select_query($sql);
        if ($result) {
            $this->gnn_nodes = $result['nodes'];
            $this->gnn_edges = $result['edges'];
            $this->gnn_pfams = $result['pfams'];
        }

    }

    public function set_ssn_stats() {
        $result = $this->count_nodes_edges($this->get_color_ssn());
        $sql = "UPDATE gnn SET gnn_ssn_edges='" . $result['edges'] . "', ";
        $sql .= "gnn_ssn_nodes='" . $result['nodes'] . "' ";
        $sql .= "WHERE gnn_id='" . $this->get_id() . "' LIMIT 1";
        $result = $this->db->non_select_query($sql);
        if ($result) {
            $this->ssn_nodes = $result['nodes'];
            $this->ssn_edges = $result['edges'];
        }

    }

    //////////////////Private Functions////////////


    private function load_gnn($id) {
        $sql = "SELECT * FROM gnn WHERE gnn_id='" . $id . "' LIMIT 1";
        $result = $this->db->query($sql);
        if ($result) {
            $this->id = $result[0]['gnn_id'];
            $this->email = $result[0]['gnn_email'];
            $this->key = $result[0]['gnn_key'];
            $this->size = $result[0]['gnn_size'];
            $this->cooccurrence = $result[0]['gnn_cooccurrence'];
            $this->filename = $result[0]['gnn_filename'];
            $this->time_created = $result[0]['gnn_time_created'];
            $this->time_started = $result[0]['gnn_time_started'];
            $this->time_completed = $result[0]['gnn_time_completed'];
            $this->ssn_nodes = $result[0]['gnn_ssn_nodes'];
            $this->ssn_edges = $result[0]['gnn_ssn_edges'];
            $this->gnn_nodes = $result[0]['gnn_gnn_nodes'];
            $this->gnn_edges = $result[0]['gnn_gnn_edges'];
            $this->gnn_pfams = $result[0]['gnn_gnn_pfams'];
            $this->pbs_number = $result[0]['gnn_pbs_number'];
            $this->status = $result[0]['gnn_status'];
            $this->is_legacy = is_null($this->status);

            // Sanitize the filename
            $this->filename = preg_replace("([\._]{2,})", '', preg_replace("([^a-zA-Z0-9\-_\.])", '', $this->filename));

            $fname = strtolower($this->filename);
            $ext_pos = strpos($fname, ".xgmml");
            if ($ext_pos === false)
                $ext_pos = strpos($fname, ".zip");
            if ($ext_pos !== false)
                $this->basefilename = substr($this->filename, 0, $ext_pos);
            else
                $this->basefilename = $this->filename;
        }	
    }

    protected static function generate_key() {
        $key = uniqid (rand (),true);
        $hash = sha1($key);
        return $hash;
    }

    private function delete_outputs() {
        if (file_exists($this->get_color_ssn())) {
            unlink($this->get_color_ssn());
        }
        if (file_exists($this->get_gnn())) {
            unlink($this->get_gnn());
        }
        if (file_exists($this->get_warning_file())) {
            unlink($this->get_warning_file());
        }

    }


    public function count_nodes_edges($xgmml_file) {
        $result = array('nodes'=>0,
            'edges'=>0,
            'pfams'=>0);
        if (file_exists($xgmml_file)) {
            $xml = simplexml_load_file($xgmml_file);
            foreach ($xml->edge as $edge) {
                $result['edges']++;
            }
            foreach($xml->node as $node) {
                $result['nodes']++;
                foreach ($node->att as $att) {
                    if ($att->attributes()->name == 'pfam') {
                        $result['pfams']++;
                    }
                }
            }	
        }
        return $result;

    }

    private function email_error() {
        $subject = $this->beta . "EFI-GNT - GNN computation failed";
        $to = $this->get_email();
        $from = "EFI GNT <" . settings::get_admin_email() . ">";

        $plain_email = "";

        if ($this->beta) $plain_email = "Thank you for using the beta site of EFI-GNT." . $this->eol;

        //plain text email
        $plain_email .= "The GNN computation for " . $this->get_id() . " failed. Please contact us ";
        $plain_email .= "to get further assistance." . $this->eol . $this->eol;
        $plain_email .= "Submission Summary:" . $this->eol . $this->eol;
        $plain_email .= $this->get_job_info() . $this->eol . $this->eol;
        $plain_email .= settings::get_email_footer();

        $html_email = nl2br($plain_email, false);

        $message = new Mail_mime(array("eol"=>$this->eol));
        $message->setTXTBody($plain_email);
        $message->setHTMLBody($html_email);
        $body = $message->get();
        $extraheaders = array("From"=>$from,
            "Subject"=>$subject
        );
        $headers = $message->headers($extraheaders);

        $mail = Mail::factory("mail");
        $mail->send($to,$headers,$body);
    }

    private function email_started() {
        $subject = $this->beta . "EFI-GNT - SSN submission received";
        $to = $this->get_email();
        $from = "EFI GNT <" . settings::get_admin_email() . ">";

        $plain_email = "";

        if ($this->beta) $plain_email = "Thank you for using the beta site of EFI-GNT." . $this->eol;

        //plain text email
        $plain_email .= "The SSN that is the input needed to generate a GNN has been received and is being processed." . $this->eol . $this->eol;
        $plain_email .= "You will receive an email once the job has been completed." . $this->eol . $this->eol;
        $plain_email .= "Submission Summary:" . $this->eol . $this->eol;
        $plain_email .= $this->get_job_info() . $this->eol . $this->eol;
        $plain_email .= settings::get_email_footer();

        $html_email = nl2br($plain_email, false);

        $message = new Mail_mime(array("eol"=>$this->eol));
        $message->setTXTBody($plain_email);
        $message->setHTMLBody($html_email);
        $body = $message->get();
        $extraheaders = array("From"=>$from,
            "Subject"=>$subject
        );
        $headers = $message->headers($extraheaders);

        $mail = Mail::factory("mail");
        $mail->send($to,$headers,$body);
    }

    public function email_complete() {
        $subject = $this->beta . "EFI-GNT - GNN computation completed";
        $to = $this->get_email();
        $from = "EFI GNT <" . settings::get_admin_email() . ">";
        $url = settings::get_web_root() . "/stepc.php";
        $full_url = $url . "?" . http_build_query(array('id'=>$this->get_id(), 'key'=>$this->get_key()));

        $plain_email = "";

        if ($this->beta) $plain_email = "Thank you for using the beta site of EFI-GNT." . $this->eol;

        //plain text email
        $plain_email .= "The GNN computation has completed." . $this->eol . $this->eol;
        $plain_email .= "To view results, go to THE_URL" . $this->eol . $this->eol;
        $plain_email .= "Submission Summary:" . $this->eol . $this->eol;
        $plain_email .= $this->get_job_info() . $this->eol . $this->eol;
        $plain_email .= "These data will only be retained for " . settings::get_retention_days() . " days." . $this->eol . $this->eol;
        $plain_email .= settings::get_email_footer();

        $html_email = nl2br($plain_email, false);

        $plain_email = str_replace("THE_URL", $full_url, $plain_email);
        $html_email = str_replace("THE_URL", "<a href='" . htmlentities($full_url) . "'>" . $full_url . "</a>", $html_email);

        $message = new Mail_mime(array("eol"=>$this->eol));
        $message->setTXTBody($plain_email);
        $message->setHTMLBody($html_email);
        $body = $message->get();
        $extraheaders = array("From"=>$from,
            "Subject"=>$subject
        );
        $headers = $message->headers($extraheaders);

        $mail = Mail::factory("mail");
        $mail->send($to,$headers,$body);
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    // Code to support asynchronous, cluster queued execution

    public function check_finish_file() {
        return file_exists($this->get_finish_file());
    }

    public function get_finish_file() {
        return $this->get_output_dir() . "/" . $this->finish_file;
    }

    public function get_output_dir() {
        if ($this->is_legacy)
            return settings::get_legacy_output_dir() . "/" . $this->get_id();
        else
            return settings::get_output_dir() . "/" . $this->get_id();
    }

    public function get_pbs_number() { return $this->pbs_number; }

        public function set_pbs_number($pbs_number) {
            $sql = "UPDATE gnn SET gnn_pbs_number='" . $pbs_number . "' ";
            $sql .= "WHERE gnn_id='" . $this->get_id() . "' LIMIT 1";
            $this->db->non_select_query($sql);
            $this->pbs_number = $pbs_number;
        }

    public function check_pbs_running() {
        $sched = strtolower(settings::get_cluster_scheduler());
        $jobNum = $this->get_pbs_number();
        $output = "";
        $exit_status = "";
        $exec = "";
        if ($sched == "slurm")
            $exec = "squeue --job $jobNum 2> /dev/null | grep $jobNum";
        else
            $exec = "qstat $jobNum 2> /dev/null | grep $jobNum";
        exec($exec,$output,$exit_status);
        if (count($output) == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public function set_status($status) {
        $sql = "UPDATE gnn ";
        $sql .= "SET gnn_status='" . $status . "' ";
        $sql .= "WHERE gnn_id='" . $this->get_id() . "' LIMIT 1";
        $result = $this->db->non_select_query($sql);
        if ($result) {
            $this->status = $status;
        }
    }

    public function get_job_info($eol = "\r\n") {
        $message = "EFI-GNT Job ID: " . $this->get_id() . $eol;
        $message .= "Uploaded Filename: " . $this->get_filename() . $this->eol;
        $message .= "Neighborhood Size: " . $this->get_size() . $this->eol;
        $message .= "% Co-Occurrence Lower Limit (Default: " . settings::get_default_cooccurrence() . "%): " . $this->get_cooccurrence() . "%" . $this->eol;
        $message .= "Time Submitted: " . $this->get_time_created() . $this->eol;
        //$message .= "Time Completed: " . $this->get_time_completed() . $this->eol . $this->eol;
        return $message;
    }
}
?>
