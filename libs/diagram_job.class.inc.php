<?php

require_once "../includes/main.inc.php";
require_once "functions.class.inc.php";
require_once "Mail.php";
require_once "Mail/mime.php";
require_once "const.class.inc.php";

class diagram_job {

    private $id;
    private $db;
    private $beta;
    private $status;
    private $key;
    private $type;
    private $params;
    private $message = "";
    private $eol = PHP_EOL;

    public function __construct($db, $id) {
        $this->db = $db;
        $this->id = $id;
        $this->beta = settings::get_release_status();
        $this->load_job();
    }

    private function load_job() {
        $sql = "SELECT * FROM diagram WHERE diagram_id = " . $this->id;
        $result = $this->db->query($sql);
        $result = $result[0];
        $this->key = $result['diagram_key'];
        $this->email = $result['diagram_email'];
        $this->status = $result['diagram_status'];
        $this->type = $this->get_job_type($result['diagram_type']);
        $this->params = functions::decode_object($result['diagram_params']);
    }

    public function process() {

        if ($this->type == DiagramJob::DIRECT) {
            return $this->process_direct_job();
        } elseif ($this->type == DiagramJob::DIRECT_ZIP) {
            return $this->process_direct_zip_job();
        } elseif ($this->type == DiagramJob::BLAST) {
            return $this->process_blast_job();
        } elseif ($this->type == DiagramJob::LOOKUP) {
            return $this->process_lookup_job();
        } elseif ($this->type == DiagramJob::FASTA) {
            return $this->process_fasta_job();
        } else {
            return false;
        }
    }

    private function handle_upload($isUploadedDatabase = true) {
        $jobId = $this->id;

        $uploadDir = settings::get_uploads_dir();
        $outDir = settings::get_diagram_output_dir() . "/$jobId";
        //$ext = settings::get_diagram_extension();
        $uploadPrefix = settings::get_diagram_upload_prefix();
        
        $source = "$uploadDir/$uploadPrefix$jobId";
        $isZipFile = file_exists("$source.zip");
        $ext = $isUploadedDatabase ? "." . ($isZipFile ? "zip" : settings::get_diagram_extension()) : "";

        $source = "$source$ext";
        $target = "$outDir/$jobId$ext";

        if (!file_exists($source))
            return false;

        $this->create_output_dir($outDir);
        copy($source, $target);

        return $target;
    }

    private function create_output_dir($outDir) {
        if (@file_exists($outDir))
            functions::rrmdir($outDir);
        if (!file_exists($outDir))
            mkdir($outDir);
        chdir($outDir);
    }

    private function process_direct_zip_job() {
        $isUploaded = true;
        $target = $this->handle_upload($isUploaded);

        $args = " -zip-file \"$target\"";

        return $this->execute_job($args);
    }

    private function process_direct_job() {
        $isUploaded = true;
        $this->handle_upload($isUploaded);

        $this->db->non_select_query("UPDATE diagram SET diagram_status = '" . __FINISH__ . "' WHERE diagram_id = " . $this->id);
        $this->email_complete();

        return true;
    }

    private function process_blast_job() {
        if (!array_key_exists("blast_seq", $this->params) || strlen($this->params["blast_seq"]) == 0) {
            $this->message = "BLAST sequence is invalid.";
            return false;
        }

        $outDir = settings::get_diagram_output_dir() . "/" . $this->id;
        $this->create_output_dir($outDir);
        $target = $this->get_output_file();

        $args = " -blast \"" . $this->params["blast_seq"] . "\"";
        $args .= " -output \"$target\"";
        $args .= " -evalue " . $this->params["evalue"];
        $args .= " -max-seq " . $this->params["max_num_sequence"];

        return $this->execute_job($args);
    }

    private function process_lookup_job() {
        $isUploaded = false;
        return false;
    }

    private function process_fasta_job() {
        $isUploaded = false;
        return false;
    }

    private function execute_job($commandLine) {
        
        $binary = settings::get_process_diagram_script();
        $exec = "source /etc/profile.d/modules.sh; ";
        $exec .= "module load " . settings::get_gnn_module() . "; ";
        $exec .= "module load " . settings::get_efidb_module() . "; ";
        $exec .= $binary . " ";
        $exec .= $commandLine;
        if (settings::run_jobs_as_legacy())
            $exec .= " -legacy";

        //TODO: remove this debug message
        error_log("Job ID: " . $this->id);
        error_log("Exec: " . $exec);

        $exit_status = 1;
        $output_array = array();
        $output = exec($exec, $output_array, $exit_status);
        $output = trim(rtrim($output));

        $pbs_job_number = substr($output, 0, strpos($output, "."));
        if (!$exit_status) {
            error_log("Job running with job # $pbs_job_number");
            $this->db->non_select_query("UPDATE diagram SET diagram_status = '" . __RUNNING__ . "' WHERE diagram_id = " . $this->id);
        } else {
            error_log("Error: $output");
        }

        return $exit_status == 0;
    }

    public function check_if_job_is_done() {

        $outDir = $this->get_output_dir();
        $isDone = file_exists("$outDir/" . DiagramJob::JobCompleted) || file_exists("$outDir/unzip.completed");

        if ($isDone) {
            print $this->id . " is done.\n";
            $this->db->non_select_query("UPDATE diagram SET diagram_status = '" . __FINISH__ . "' WHERE diagram_id = " . $this->id);
            $this->email_complete();
        }
    }

    private function email_complete() {
        $subject = $this->beta . "EFI-GNT - GNN diagrams ready";
        $to = $this->email;
        $from = "EFI GNT <" . settings::get_admin_email() . ">";
        $url = settings::get_web_root() . "/view_diagrams.php";
        $full_url = $url . "?" . http_build_query(array('upload-id' => $this->id, 'key' => $this->key));

        $plain_email = "";

        if ($this->beta) $plain_email = "Thank you for using the beta site of EFI-GNT." . $this->eol;

        //plain text email
        $plain_email .= "The diagram data file you uploaded is ready to be viewed at ";
        $plain_email .= "THE_URL" . $this->eol . $this->eol;
        $plain_email .= "These data will only be retained for " . settings::get_retention_days() . " days." . $this->eol . $this->eol;
        $plain_email .= settings::get_email_footer();

        $html_email = nl2br($plain_email, false);

        $plain_email = str_replace("THE_URL", $full_url, $plain_email);
        $html_email = str_replace("THE_URL", "<a href='" . htmlentities($full_url) . "'>" . $full_url . "</a>", $html_email);

        $message = new Mail_mime(array("eol" => $this->eol));
        $message->setTXTBody($plain_email);
        $message->setHTMLBody($html_email);
        $body = $message->get();
        $extraheaders = array(
            "From" => $from,
            "Subject" => $subject
        );
        $headers = $message->headers($extraheaders);

        $mail = Mail::factory("mail");
        $mail->send($to, $headers, $body);
    }

    private function get_job_type($type) {
        if ($type == DiagramJob::DIRECT)
            return DiagramJob::DIRECT;
        elseif ($type == DiagramJob::DIRECT_ZIP)
            return DiagramJob::DIRECT_ZIP;
        elseif ($type == DiagramJob::BLAST)
            return DiagramJob::BLAST;
        elseif ($type == DiagramJob::LOOKUP)
            return DiagramJob::LOOKUP;
        elseif ($type == DiagramJob::FASTA)
            return DiagramJob::FASTA;
        else
            return DiagramJob::UNKNOWN;
    }

    private function get_output_file() {
        $outDir = $this->get_output_dir();
        $target = "$outDir/" . $this->id . "." . settings::get_diagram_extension();
        return $target;
    }

    private function get_output_dir() {
        $outDir = settings::get_diagram_output_dir() . "/" . $this->id;
        return $outDir;
    }

    public function get_message() {
        return $this->message;
    }
}

?>

