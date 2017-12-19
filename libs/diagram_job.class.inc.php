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
    private $title = "";
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
        $this->key = $result["diagram_key"];
        $this->email = $result["diagram_email"];
        $this->status = $result["diagram_status"];
        $this->type = $this->get_job_type($result["diagram_type"]);
        $this->title = $result["diagram_title"];
        $this->params = functions::decode_object($result["diagram_params"]);
    }

    public function process() {

        if ($this->type == DiagramJob::Uploaded) {
            return $this->process_direct_job();
        } elseif ($this->type == DiagramJob::UploadedZip) {
            return $this->process_direct_zip_job();
        } elseif ($this->type == DiagramJob::BLAST) {
            return $this->process_blast_job();
        } elseif ($this->type == DiagramJob::IdLookup || $this->type == DiagramJob::FastaLookup) {
            return $this->process_lookup_job();
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

        $args = " -blast \"" . $this->params["blast_seq"] . "\"";
        $args .= " -evalue " . $this->params["evalue"];
        $args .= " -max-seq " . $this->params["max_num_sequence"];

        return $this->execute_job($args);
    }

    private function process_lookup_job() {
        $jobId = $this->id;

        $outDir = settings::get_diagram_output_dir() . "/" . $this->id;
        $this->create_output_dir($outDir);

        $uploadDir = settings::get_uploads_dir();
        $uploadPrefix = settings::get_diagram_upload_prefix();
        $uploadSource = "$uploadDir/$uploadPrefix$jobId.txt";

        $source = "$outDir/$jobId.txt";
        copy($uploadSource, $source);

        $args = "";
        if ($this->type == DiagramJob::IdLookup) {
            $args = " -id-file \"$source\"";
        } elseif ($this->type == DiagramJob::FastaLookup) {
            $args = " -fasta-file \"$source\"";
        }

        return $this->execute_job($args);
    }

    private function execute_job($commandLine) {
        
        $target = $this->get_output_file();
        $sched = settings::get_cluster_scheduler();

        $binary = settings::get_process_diagram_script();
        $exec = "source /etc/profile\n";
        $exec .= "module load " . settings::get_gnn_module() . "\n";
        $exec .= "module load " . settings::get_efidb_module() . "\n";
        $exec .= $binary . " ";
        $exec .= $commandLine;
        $exec .= " -output \"$target\"";
        if (settings::run_jobs_as_legacy())
            $exec .= " -legacy";
        if ($this->title)
            $exec .= " -title \"" . $this->title . "\"";
        if ($this->type)
            $exec .= " -job-type \"" . $this->type . "\"";
        if (array_key_exists("neighborhood_size", $this->params) && $this->params["neighborhood_size"])
            $exec .= " -nb-size " . $this->params["neighborhood_size"];
        if ($sched)
            $exec .= " -scheduler $sched";

        //TODO: remove this debug message
        error_log("Job ID: " . $this->id);
        error_log("Exec: " . $exec);

        $exit_status = 1;
        $output_array = array();
        $output = exec($exec, $output_array, $exit_status);
        $output = trim(rtrim($output));

        if ($sched == "slurm")
            $pbs_job_number = $output;
        else
            $pbs_job_number = substr($output, 0, strpos($output, "."));

        if (!$exit_status) {
            error_log("Job running with job # $pbs_job_number");
            $this->db->non_select_query("UPDATE diagram SET diagram_status = '" . __RUNNING__ . "' WHERE diagram_id = " . $this->id);
        } else {
            $currentTime = date("Y-m-d H:i:s", time());
            $this->db->non_select_query("UPDATE diagram SET diagram_status = '" . __FAILED__ . "', " .
                                        "diagram_time_completed = '$currentTime' WHERE diagram_id = " . $this->id);
            $this->email_failed();
            error_log("Error: $output");
        }

        return $exit_status == 0;
    }

    public function check_if_job_is_done() {

        $outDir = $this->get_output_dir();
        $isDone = file_exists("$outDir/" . DiagramJob::JobCompleted) || file_exists("$outDir/unzip.completed");
        $isError = file_exists("$outDir/" . DiagramJob::JobError) || !file_exists($this->get_output_file());

        if ($isDone) {
            $currentTime = date("Y-m-d H:i:s", time());
            
            $status = __FINISH__;
            if ($isError)
                $status = __FAILED__;
            print $this->id . " has completed and has status = $status.\n";

            $this->db->non_select_query("UPDATE diagram SET diagram_status = '$status', " .
                                        "diagram_time_completed = '$currentTime' WHERE diagram_id = " . $this->id);

            if ($isError)
                $this->email_failed();
            else
                $this->email_complete();
        }
    }

    private function email_failed() {

        $emailTitleBit = "failed to be retrieved";
        $emailBody = "";
        $emailBody .= "There was an error retrieving the neighborhood data for the job (job #" . $this->id . ").";
        $emailBody .= $this->eol . $this->eol;

        $this->email_shared($emailTitleBit, $emailBody);
    }

    private function email_complete() {

        $emailTitleBit = "ready";
        $emailBody = "";
        $emailBody .= "The diagram data file you uploaded is ready to be viewed at ";
        $emailBody .= "THE_URL" . $this->eol . $this->eol;
        $emailBody .= "These data will only be retained for " . settings::get_retention_days() . " days." . $this->eol . $this->eol;

        $this->email_shared($emailTitleBit, $emailBody);

        //$queryType = $this->get_query_string_id_field();
        //$subject = $this->beta . "EFI-GNT - GNN diagrams ready";
        //$to = $this->email;
        //$from = "EFI GNT <" . settings::get_admin_email() . ">";
        //$url = settings::get_web_root() . "/view_diagrams.php";
        //$full_url = $url . "?" . http_build_query(array($queryType => $this->id, 'key' => $this->key));

        //$plain_email = "";

        //if ($this->beta) $plain_email = "Thank you for using the beta site of EFI-GNT." . $this->eol;

        ////plain text email
        //$plain_email .= "The diagram data file you uploaded is ready to be viewed at ";
        //$plain_email .= "THE_URL" . $this->eol . $this->eol;
        //$plain_email .= "These data will only be retained for " . settings::get_retention_days() . " days." . $this->eol . $this->eol;
        //$plain_email .= settings::get_email_footer();

        //$html_email = nl2br($plain_email, false);

        //$plain_email = str_replace("THE_URL", $full_url, $plain_email);
        //$html_email = str_replace("THE_URL", "<a href='" . htmlentities($full_url) . "'>" . $full_url . "</a>", $html_email);

        //$message = new Mail_mime(array("eol" => $this->eol));
        //$message->setTXTBody($plain_email);
        //$message->setHTMLBody($html_email);
        //$body = $message->get();
        //$extraheaders = array(
        //    "From" => $from,
        //    "Subject" => $subject
        //);
        //$headers = $message->headers($extraheaders);

        //$mail = Mail::factory("mail");
        //$mail->send($to, $headers, $body);
    }

    private function email_shared($titleBit, $message) {
        $queryType = $this->get_query_string_id_field();
        $subject = $this->beta . "EFI-GNT - GNN diagrams $titleBit";
        $to = $this->email;
        $from = "EFI GNT <" . settings::get_admin_email() . ">";
        $url = settings::get_web_root() . "/view_diagrams.php";
        $full_url = $url . "?" . http_build_query(array($queryType => $this->id, 'key' => $this->key));

        $plain_email = "";

        if ($this->beta) $plain_email = "Thank you for using the beta site of EFI-GNT." . $this->eol;

        //plain text email
        $plain_email .= $message;
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
        $type = strtoupper($type);
        if ($type == DiagramJob::Uploaded)
            return DiagramJob::Uploaded;
        elseif ($type == DiagramJob::UploadedZip)
            return DiagramJob::UploadedZip;
        elseif ($type == DiagramJob::BLAST)
            return DiagramJob::BLAST;
        elseif ($type == DiagramJob::IdLookup)
            return DiagramJob::IdLookup;
        elseif ($type == DiagramJob::FastaLookup)
            return DiagramJob::FastaLookup;
        elseif ($type == DiagramJob::GNN)
            return DiagramJob::GNN;
        else
            return DiagramJob::UNKNOWN;
    }

    private function get_query_string_id_field() {
        return functions::get_diagram_id_field($this->type);
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

