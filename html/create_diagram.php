<?php
require_once "../includes/main.inc.php";
require_once "../libs/diagram_jobs.class.inc.php";

$id = 0;
$key = 0;
$message = "";
$valid = 0;
$cookieInfo = "";

if (isset($_POST["option"])) {

    $opt = $_POST["option"];

    $valid = 1;

    if (!isset($_POST["email"]) || !functions::verify_email($_POST["email"])) {
        $valid = 0;
        $message .= "<br><b>Please verify your email address</b>";
    }

    if ($valid) {
        $email = $_POST["email"];
        $title = isset($_POST["title"]) ? $_POST["title"] : "";

        $retval = "";
        if ($opt == "a") {
            $retval = create_blast_job($db, $email, $title);
        } elseif ($opt == "c") {
            $retval = create_fasta_job($db, $email, $title);
        } elseif ($opt == "d") {
            $retval = create_lookup_job($db, $email, $title);
        }

        if ($retval["valid"] === false) {
            $valid = 0;
            $message .= "<br>" . $retval["message"];
            $id = "";
            $key = "";
        } else {
            $id = $retval["id"];
            $key = $retval["key"];
        }
        
        $userObj = new user_jobs();
        $userObj->save_user($db, $email);
        $cookieInfo = $userObj->get_cookie();
    }
}

echo json_encode(array(
    "valid" => $valid,
    "id" => $id,
    "key" => $key,
    "message" => $message,
    "cookieInfo" => $cookieInfo
));




function create_blast_job($db, $email, $title) {

    $retval = array("id" => 0, "key" => "", "valid" => false, "message" => "");

    if (!isset($_POST["evalue"]) || !functions::verify_evalue($_POST["evalue"])) {
    
        $retval["message"] = "The given e-value is invalid.";
    
    } elseif (!isset($_POST["max-seqs"]) || !functions::verify_max_seqs($_POST["max-seqs"])) {
    
        $retval["message"] = "The given maximum sequence value is invalid.";

    } elseif (!isset($_POST["sequence"]) || !functions::verify_blast_input($_POST["sequence"])) {

        $retval["message"] = "The BLAST sequence is not valid.";

    } else {

        $retval["valid"] = true;
        $jobInfo = diagram_jobs::create_blast_job($db, $email, $title, $_POST["evalue"], $_POST["max-seqs"], $_POST["sequence"]);
    
        if ($jobInfo === false) {
            $retval["message"] .= " The job was unable to be created.";
            $retval["valid"] = false;
        } else {
            $retval["id"] = $jobInfo["id"];
            $retval["key"] = $jobInfo["key"];
        }
    }

    return $retval;
}


function create_fasta_job($email, $title) {

    //TODO: Implement this
    
    return "";
}


function create_lookup_job($email, $title) {

    $retval = array("id" => 0, "key" => "", "valid" => false, "message" => "");

//    if (!isset($_POST["ids"]) || strlen($_POST["ids"]) == 0) {
//
//        $retval["message"] = "The given list of IDs is invalid. Please input some IDs.";
//
//    } else {
//
//        $retval["valid"] = true;
//        file_put_contents(

    return "";
}


?>
