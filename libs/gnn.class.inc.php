<?php

require_once('Mail.php');
require_once('Mail/mime.php');

class gnn {

	////////////////Private Variables//////////

        protected $db; //mysql database object
        protected $id;
        protected $email;
        protected $key;
	protected $filename;
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
        ///////////////Public Functions///////////

	 public function __construct($db,$id = 0) {
                $this->db = $db;

                if ($id) {
                        $this->load_gnn($id);

                }
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
		return $uploads_dir . "/" . $this->get_id() . "." . settings::get_valid_file_type(); 

	}

	public static function create($db,$email,$size,$tmp_filename,$filename,$cooccurrence) {
		$result = false;
		$insert_array = array('gnn_email'=>$email,
			'gnn_size'=>$size,
			'gnn_key'=>self::generate_key(),
			'gnn_filename'=>$filename,
			'gnn_cooccurrence'=>$cooccurrence);
		$result = $db->build_insert('gnn',$insert_array);
		if ($result) {	
			self::copy_to_uploads_dir($tmp_filename,$result);
		}
		return $result;

	}

	public static function copy_to_uploads_dir($tmp_file,$id) {
                $uploads_dir = settings::get_uploads_dir();
                $file_type = settings::get_valid_file_type();
                $filename = $id . "." . $file_type;
                $full_path = $uploads_dir . "/" . $filename;
		if (is_uploaded_file($tmp_file)) {
			if (move_uploaded_file($tmp_file,$full_path)) { return $filename; }
	
		}
		else {
			if (copy($tmp_file,$full_path)) { return $filename; }
		}
                return false;

        }


	public function run_gnn() {
		$this->delete_outputs();
		$this->set_time_started();
		$binary = settings::get_gnn_script();
		mkdir(settings::get_output_dir() . "/" . $this->get_id());
		$exec = "source /etc/profile.d/modules.sh; module load " . functions::get_gnn_module() . "; ";
		$exec .= $binary . " ";
		$exec .= "-ssnin " . $this->get_full_path() . " ";
		$exec .= "-n " . $this->get_size() . " ";
		$exec .= "-nomatch " . $this->get_no_matches() . " ";
		$exec .= "-gnn " . $this->get_gnn() . " ";
		$exec .= "-ssnout " . $this->get_color_ssn() . " ";
		$exec .= "-incfrac " . $this->get_cooccurrence() . " ";
		$exec .= "-stats " . $this->get_stats();

		error_log("Job ID: " . $this->get_id());
		error_log("Exec: " . $exec);
		$output_array = array();
		$output = exec($exec,$output_array,$exit_status);
		$output = trim(rtrim($output));
		error_log("Jog ID: " . $this->get_id() . ", Exit Status: " . $exit_status);
		$this->set_time_completed();
		$formatted_output = implode("\n",$output_array);
		file_put_contents($this->get_log_file(), $formatted_output);
		if (($exit_status == 0) && (strpos($output,'makegnn.pl finished') !== false)) {
			$this->set_gnn_stats();
			$this->set_ssn_stats();
			return array('RESULT'=>true,'OUTPUT'=>$formatted_output);
		}
		else {
			$this->email_error($formatted_output); 
			return array('RESULT'=>false,'OUTPUT'=>$formatted_output);
		}

	}

	public function get_log_file() {
                $filename = $this->log_file;
                $output_dir = settings::get_output_dir();
                $full_path = $output_dir . "/" . $this->get_id() . "/" . $filename;
                return $full_path;


	}
	public function get_color_ssn() {
		$filename = $this->get_id() . "_color_co" . $this->get_cooccurrence() . "_ns" . $this->get_size() . ".xgmml";
		$output_dir = settings::get_output_dir();
		$full_path = $output_dir . "/" . $this->get_id() . "/" . $filename;
		return $full_path;

	}
	public function get_relative_color_ssn() {
		$filename = $this->get_id() . "_color_co" . $this->get_cooccurrence() . "_ns" . $this->get_size() . ".xgmml";
		$output_dir = settings::get_rel_output_dir();
                $full_path = $output_dir . "/" . $this->get_id() . "/" . $filename;
                return $full_path;

	}
	public function get_gnn() {
		$filename = $this->get_id() . "_gnn_co" . $this->get_cooccurrence() . "_ns" . $this->get_size() . ".xgmml";
		$output_dir = settings::get_output_dir();
		$full_path = $output_dir . "/" . $this->get_id() . "/" . $filename;
		return $full_path;
	
	}
        public function get_relative_gnn() {
		$filename = $this->get_id() . "_gnn_co" . $this->get_cooccurrence() . "_ns" . $this->get_size() . ".xgmml";
                $output_dir = settings::get_rel_output_dir();
                $full_path = $output_dir . "/" . $this->get_id() . "/" . $filename;
                return $full_path;

        }

	public function get_no_matches() {
		$filename = $this->get_id() . "_no_matches_co" . $this->get_cooccurrence() . "_ns" . $this->get_size() . ".xgmml";
                $output_dir = settings::get_output_dir();
                $full_path = $output_dir . "/" . $this->get_id() . "/" . $filename;
                return $full_path;

	}
        public function get_relative_no_matches() {
		$filename = $this->get_id() . "_no_matches_co" . $this->get_cooccurrence() . "_ns" . $this->get_size() . ".xgmml";
                $output_dir = settings::get_rel_output_dir();
                $full_path = $output_dir . "/" . $this->get_id() . "/" . $filename;
                return $full_path;

        }

	public function get_stats() {
		$filename = $this->get_id() . "_stats_co" . $this->get_cooccurrence() . "_ns" . $this->get_size() . ".tab";
		$output_dir = settings::get_output_dir();
		$full_path = $output_dir . "/" . $this->get_id() . "/" . $filename;
		return $full_path;
	}
	public function get_relative_stats() {
		$filename = $this->get_id() . "_stats_co" . $this->get_cooccurrence() . "_ns" . $this->get_size() . ".tab";
		$output_dir = settings::get_rel_output_dir();
		$full_path = $output_dir . "/" . $this->get_id() . "/".  $filename;
		return $full_path;

	}
	public function get_color_ssn_filesize() {
		return round(filesize($this->get_color_ssn()) / 1048576,2);
	}
	public function get_gnn_filesize() {
		return round(filesize($this->get_gnn()) / 1048576,2);
	}
	public function get_no_matches_filesize() {
		return round(filesize($this->get_no_matches()) / 1048576,2);
	}

	public function get_stats_filesize() {
		return round(filesize($this->get_stats()) / 1048576,2);
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

		}	

	}

	protected function generate_key() {
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
		if (file_exists($this->get_no_matches())) {
			unlink($this->get_no_matches());
		}
	
	}

	public function email_user() {

                $subject = "EFI-GNN Complete";
                $from = settings::get_admin_email();
                $to = $this->get_email();
                $url = settings::get_web_root() . "/stepc.php";
                $full_url = $url . "?" . http_build_query(array('id'=>$this->get_id(),
                                'key'=>$this->get_key()));

		//html email
		$html_email = "<br>Your EFI-GNN is Complete" . $this->eol;
                $html_email .= "<br>To view results, please go to <a href='" . htmlentities($full_url) . "'>" . $full_url . "</a>" . $this->eol;
                $html_email .= "<br>EFI-GNN ID: " . $this->get_id() . $this->eol;
		$html_email .= "<br>Uploaded Filename: " . $this->get_filename() . $this->eol;	
                $html_email .= "<br>Neighborhood Size: " . $this->get_size() . $this->eol;
		$html_email .= "<br>% Co-Occurrence Lower Limit (Default: " . settings::get_default_cooccurrence() . "%): " . $this->get_cooccurrence() . "%" . $this->eol;
                $html_email .= "<br>Time Submitted: " . $this->get_time_created() . $this->eol;
		$html_email .= "<br>Time Completed: " . $this->get_time_completed() . $this->eol;
		$html_email .= "<br><br>This data will only be retained for " . settings::get_retention_days() . " days." . $this->eol;
		$html_email .= "<br>";
                $html_email .= nl2br(settings::get_email_footer(),false) . $this->eol;

		//plain text email
		$plain_email = "Your EFI-GNN is Complete" . $this->eol;
                $plain_email .= "To view results, please go to " . $full_url . $this->eol;
                $plain_email .= "EFI-GNN ID: " . $this->get_id() . $this->eol;
                $plain_email .= "Uploaded Filename: " . $this->get_filename() . $this->eol;
                $plain_email .= "Neighborhood Size: " . $this->get_size() . $this->eol;
                $plain_email .= "% Co-Occurrence Lower Limit (Default: " . settings::get_default_cooccurrence() . "%): " . $this->get_cooccurrence() . "%" . $this->eol;
                $plain_email .= "Time Submitted: " . $this->get_time_created() . $this->eol;
                $plain_email .= "Time Completed: " . $this->get_time_completed() . $this->eol . $this->eol;
                $plain_email .= "This data will only be retained for " . settings::get_retention_days() . " days." . $this->eol . $this->eol;
                $plain_email .= settings::get_email_footer() . $this->eol;

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

	private function email_error($error_message) {
                $subject = "EFI-GNN Failed";
                $from = settings::get_admin_email();
                //$to = $this->get_admin_email();
		$to = "dslater@igb.illinois.edu";

		//html email
                $html_email = "<br>Your EFI-GNN failed" . $this->eol;
                $html_email .= "<br>EFI-GNN ID: " . $this->get_id() . $this->eol;
                $html_email .= "<br>Uploaded Filename: " . $this->get_filename() . $this->eol;
                $html_email .= "<br>Neighborhood Size: " . $this->get_size() . $this->eol;
                $html_email .= "<br>% Co-Occurrence Lower Limit (Default: " . settings::get_default_cooccurrence() . "%): " . $this->get_cooccurrence() . "%" . $this->eol;
                $html_email .= "<br>Time Submitted: " . $this->get_time_created() . $this->eol;
                $html_email .= "<br>Time Completed: " . $this->get_time_completed() . $this->eol;
		$html_email .= "<br>Error: " . $error_message . $this->eol;
                $html_email .= "<br>";
                $html_email .= "<br>";
                $html_email .= nl2br(settings::get_email_footer(),false) . $this->eol;

		//text email
		$plain_email = "Your EFI-GNN failed" . $this->eol;
                $plain_email .= "EFI-GNN ID: " . $this->get_id() . $this->eol;
                $plain_email .= "Uploaded Filename: " . $this->get_filename() . $this->eol;
                $plain_email .= "Neighborhood Size: " . $this->get_size() . $this->eol;
                $plain_email .= "% Co-Occurrence Lower Limit (Default: " . settings::get_default_cooccurrence() . "%): " . $this->get_cooccurrence() . "%" . $this->eol;
                $plain_email .= "Time Submitted: " . $this->get_time_created() . $this->eol;
                $plain_email .= "Time Completed: " . $this->get_time_completed() . $this->eol;
                $plain_email .= "Error: " . $error_message . $this->eol;
                $plain_email .= $this->eol . $this->eol;
                $plain_email .= settings::get_email_footer() . $this->eol;

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
}
?>
