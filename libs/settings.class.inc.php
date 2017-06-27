<?php

class settings {

	public static function get_default_neighbor_size() {
		return __DEFAULT_NEIGHBOR_SIZE__;


	}

	public static function get_gnn_script() {
		return __GNN_SCRIPT__;


	}

	public static function get_uploads_dir() {
		$dir =__UPLOAD_DIR__;
		if (is_dir($dir)) {
			return $dir;
		}
		return false;

	}

    public static function get_valid_file_type() {
        return __VALID_FILE_TYPE__;
    }

    public static function is_valid_file_type($filetype) {
        $filetypes = explode(" ", __VALID_FILE_TYPE__);
        return in_array($filetype, $filetypes);
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

	public static function get_rel_output_dir() {
		return __RELATIVE_OUTPUT_DIR__;		

	}

	public static function get_web_address() {
		return dirname($_SERVER['PHP_SELF']);

	}

        public static function get_web_root() {
                $url = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
		return $url;
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

}
?>
