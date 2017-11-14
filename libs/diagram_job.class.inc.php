<?php

require_once "../includes/main.inc.php";
require_once "functions.class.inc.php";
require_once "Mail.php";
require_once "Mail/mime.php";

class diagram_job {

    private $id;
    private $db;
    private $beta;
    private $status;
    private $key;
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
        $this->key = $result[0]['diagram_key'];
        $this->email = $result[0]['diagram_email'];
        $this->status = $result[0]['diagram_status'];
    }

    public function process() {

        $jobId = $this->id;

        $uploadDir = settings::get_uploads_dir();
        $outDir = settings::get_diagram_output_dir() . "/" . $jobId;
        //$ext = settings::get_diagram_extension();
        $uploadPrefix = settings::get_diagram_upload_prefix();
        
        $source = "$uploadDir/$uploadPrefix$jobId";
        $isZipFile = file_exists("$source.zip");
        $ext = $isZipFile ? "zip" : settings::get_diagram_extension();

        $source = "$source.$ext";
        $target = "$outDir/$jobId.$ext";

        if (!file_exists($source))
            return false;

        //TODO:
        //if (@file_exists($outDir))
        //    functions::rrmdir($outDir);
        if (!file_exists($outDir))
            mkdir($outDir);
        chdir($outDir);
        copy($source, $target);

        if ($isZipFile) {
            $binary = settings::get_unzip_diagram_script();
            $exec = "source /etc/profile.d/modules.sh; ";
            $exec .= "module load " . settings::get_gnn_module() . "; ";
            $exec .= $binary . " ";
            $exec .= " -diagram-file \"$target\"";

            //TODO: remove this debug message
            error_log("Job ID: " . $this->id);
            error_log("Exec: " . $exec);

            $exit_status = 1;
            $output_array = array();
            $output = exec($exec, $output_array, $exit_status);
            $output = trim(rtrim($output));

            $pbs_job_number = substr($output, 0, strpos($output, "."));
            if (!$exit_status)
                $this->db->non_select_query("UPDATE diagram SET diagram_status = '" . __RUNNING__ . "' WHERE diagram_id = $jobId");
            else
                error_log("Error: $output");
        } else {
            $this->db->non_select_query("UPDATE diagram SET diagram_status = '" . __FINISH__ . "' WHERE diagram_id = $jobId");
            $this->email_complete();
        }

        return true;
    }

    public function check_if_job_is_done() {
        $jobId = $this->id;

        $outDir = settings::get_diagram_output_dir() . "/" . $jobId;
        $isDone = file_exists("$outDir/unzip.completed");

        if ($isDone) {
            print "$jobId is done.\n";
            $this->db->non_select_query("UPDATE diagram SET diagram_status = '" . __FINISH__ . "' WHERE diagram_id = $jobId");
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
}

?>

