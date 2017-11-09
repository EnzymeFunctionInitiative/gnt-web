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
        $ext = settings::get_diagram_extension();
        $uploadPrefix = settings::get_diagram_upload_prefix();

        $source = "$uploadDir/$uploadPrefix$jobId.$ext";
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

        $this->db->non_select_query("UPDATE diagram SET diagram_status = '" . __FINISH__ . "' WHERE diagram_id = $jobId");

        $this->email_complete();

        return true;
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

