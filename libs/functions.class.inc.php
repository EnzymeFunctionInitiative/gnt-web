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

}
?>
