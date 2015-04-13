<?php

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


	public static function get_upload_error($value) {
		return self::$upload_errors[$value];
		
	}

	public static function get_gnn_module() {
		return __GNN_MODULE__;
	}

        public static function get_uniprot_version() {
                return __UNIPROT_VERSION__;
        }

	public static function get_ena_version() {
		return __ENA_VERSION__;
	}
}
?>
