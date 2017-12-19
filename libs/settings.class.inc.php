<?php

class settings {

    public static function get_default_neighbor_size() {
        return __DEFAULT_NEIGHBOR_SIZE__;
    }

    public static function get_gnn_script() {
        return __GNN_SCRIPT__;
    }

    public static function get_process_diagram_script() {
        return __PROCESS_DIAGRAM_SCRIPT__;
    }

    public static function get_uploads_dir() {
        $dir = __UPLOAD_DIR__;
        if (is_dir($dir)) {
            return $dir;
        }
        return false;
    }

    public static function get_valid_file_type() {
        return __VALID_FILE_TYPE__;
    }

    public static function get_default_file_type($filetype) {
        $filetypes = explode(" ", __VALID_FILE_TYPE__);
        return $filetypes[0];
    }

    public static function get_output_dir() {
        if (is_dir(__OUTPUT_DIR__)) {
            return __OUTPUT_DIR__;
        }
        return false;
    }

    public static function get_diagram_output_dir() {
        if (is_dir(__DIAGRAM_OUTPUT_DIR__))
            return __DIAGRAM_OUTPUT_DIR__;
        return false;
    }

    public static function get_legacy_output_dir() {
        if (is_dir(__LEGACY_OUTPUT_DIR__)) {
            return __LEGACY_OUTPUT_DIR__;
        }
        return false;
    }

    public static function get_rel_output_dir() {
        return __RELATIVE_OUTPUT_DIR__;		
    }

    public static function get_legacy_rel_output_dir() {
        return __LEGACY_RELATIVE_OUTPUT_DIR__;		
    }

    public static function get_web_address() {
        return dirname($_SERVER['PHP_SELF']);
    }

    public static function get_web_root() {
        return __WEB_ROOT__;
    }

    public static function get_email_footer() {
        return __EMAIL_FOOTER__;

    }

    public static function get_admin_email() {
        return __ADMIN_EMAIL__;

    }

    public static function get_timeout() {
        return __MAX_TIMEOUT__;

    }

    public static function get_retention_days() {
        return __RETENTION_DAYS__;
    }

    public static function get_default_cooccurrence() {
        return __COOCCURRENCE__;
    }

    public static function get_cluster_user() {
        return __CLUSTER_USER__;
    }

    public static function get_gnn_module() {
        return __GNN_MODULE__;
    }
    public static function get_efidb_module() {
        return __EFIDB_MODULE__;
    }

    public static function get_uniprot_version() {
        return __UNIPROT_VERSION__;
    }

    public static function get_ena_version() {
        return __ENA_VERSION__;
    }

    public static function get_est_version() {
        return defined("__EST_VERSION__") && __EST_VERSION__ ? __EST_VERSION__ : "-";
    }

    public static function get_est_url() {
        return __EST_URL__ ? __EST_URL__ : "#";
    }

    public static function get_gnt_version() {
        return defined("__GNT_VERSION__") && __GNT_VERSION__ ? __GNT_VERSION__ : "-";
    }

    public static function get_release_status() {
        return defined("__BETA_RELEASE__") && __BETA_RELEASE__ ? __BETA_RELEASE__ . " " : "";
    }

    public static function is_beta_release() {
        return defined("__BETA_RELEASE__") && __BETA_RELEASE__ ? true : false;
    }

    public static function get_valid_diagram_file_types() {
        $filetypes = explode(" ", __VALID_DIAGRAM_FILE_TYPE__);
        return $filetypes[0];
    }

    public static function get_diagram_extension() {
        return "sqlite";
    }

    public static function get_diagram_upload_prefix() {
        return "diagram_";
    }

    public static function get_default_evalue() {
        return __DEFAULT_EVALUE__;
    }

    public static function get_max_blast_seq() {
        return __MAX_NUM_BLAST_SEQ__;
    }

    public static function get_default_blast_seq() {
        return __DEFAULT_NUM_BLAST_SEQ__;
    }

    public static function run_jobs_as_legacy() {
        return __ENABLE_LEGACY__;
    }

    public static function get_default_neighborhood_size() {
        return __DEFAULT_NEIGHBORHOOD_SIZE__;
    }

    public static function is_recent_jobs_enabled() {
        return __ENABLE_RECENT_JOBS__;
    }

    public static function get_cluster_scheduler() {
        return __CLUSTER_SCHEDULER__;
    }
}
?>
