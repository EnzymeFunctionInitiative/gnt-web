<?php 
require_once '../includes/main.inc.php';

$email = "do-not-reply@enzymefunction.org";
$file1 = "TEST1-PeptidaseS46-55percent-full.xgmml";
$file2 = "TEST2-DUF386_repnode-1.00_52percent.xgmml";
$neighborhood = 10;
$cooccurrence = 20;

$test1_query = "test.php?" . http_build_query(array('ssn_file'=>$file1,
                                'neighbor_size'=>$neighborhood,
                                'cooccurrence'=>$cooccurrence,
                                'email'=>$email));

$test2_query = "test.php?" . http_build_query(array('ssn_file'=>$file2,
                                'neighbor_size'=>$neighborhood,
                                'cooccurrence'=>$cooccurrence,
                                'email'=>$email));

$neighbor_size_html = "";
$default_neighbor_size = settings::get_default_neighbor_size();
for ($i=3;$i<=20;$i++) {
	if ($i == $default_neighbor_size) {
		$neighbor_size_html .= "<option value='" . $i . "' selected='selected'>" . $i . "</option>";

	}
	else {
		$neighbor_size_html .= "<option value='" . $i . "'>" . $i . "</option>";
	}

}

function make_upload_box($title, $file_id, $progress_bar_id, $progress_num_id, $other) {
    global $maxFileSize;
    return <<<HTML
                <div>
                    $title
                    <input type='file' name='$file_id' id='$file_id' data-url='server/php/' class="input_file">
                    <label for="$file_id" class="file_upload"><img src="images/upload.svg" /> <span>Choose a file&hellip;</span></label>
                </div>
                $other
                Maximum size is $maxFileSize.
HTML;
}

$maxFileSize = ini_get('post_max_size');

require_once('includes/header.inc.php'); 

?>
<!--
<h3>Start With...    </h3>
<h4>An Introduction</h4>
<p>Start here if you are new to the &quot;Genome Neighborhood Networks Tool&quot;.</p>
<h4 class='center'><a href='tutorial.php'><button class='css_btn_class'>GO</button></a></h4>
-->
<h3>Stage 1</h3>

<hr>
<img src="images/quest_stages_a.jpg" width="990" height="119" alt="stage 1">
<hr>

<h4>Input <a href="#" class="question" target="_blank">?</a></h4>

<p>
    <strong class="blue">Upload the Sequence Similarity Network (SSN) for which you want to create a Genome Neighborhood Network (GNN)</strong>
</p>

<p>
    The submitted SSN must have been generated using Option A, B, C with reading FASTA headers on, or D
    of <a href='http://efi.igb.illinois.edu/efi-est-beta'>EFI-EST</a> to be interpretted.
    <br>An SSN that has been modified in Cytoscape can also be used.
</p>

<!--
<h4><strong  class="blue">Upload a Sequence Similarity Network (SSN). The acceptable format is
uncompressed or zipped xgmml.</strong></h4>

<h4><strong  class="blue">The SSN must be generated using Option A, Option B, Option C (reading FASTA headers), or
Option D of <a href='http://efi.igb.illinois.edu/efi-est'>EFI-EST</a>.</strong></h4>
<h4>Cytoscape can be used to edit/modify the SSN.</h4>
-->

<form name="upload_form" id='upload_form' method="post" action="" enctype="multipart/form-data">
<input type="hidden" id='MAX_FILE_SIZE' name="MAX_FILE_SIZE" value="2147483648" />

<p>
<?php echo make_upload_box("<b>Select a File to Upload:</b><br>", "ssn_file", "progress_bar", "progressNumber", "The acceptable format is uncompressed or zipped xgmml."); ?>
<!--
    <label for="fileToUpload"><b>Select a File to Upload</b></label><br />
    <input type="file" id='ssn_file' name="ssn_file" data-url='server/php/' class="blast_inputs email border"><br>
    The acceptable format is uncompressed or zipped xgmml. Maximum file size is <?php echo $maxFileSize; ?>b.
-->
    <!--(Maximum size is <?php echo ini_get('post_max_size'); ?>.)-->
</p>

<p>
    <b>Neighborhood Size:</b><!-- (default: <?php echo $default_neighbor_size; ?>)-->
    <select name='neighbor_size' id='neighbor_size'>
        <?php echo $neighbor_size_html; ?>
    </select>
    <br>
    With a value of <?php echo $default_neighbor_size; ?>, the PFAM families for <?php echo $default_neighbor_size; ?>
    genes located upstream and for <?php echo $default_neighbor_size; ?> genes <br>
    located downstream of sequences in the SNN will be collected and displayed.<br>
    The default value is  <?php echo $default_neighbor_size; ?>.
</p>

<p>
    <!--<label for='cooccurrence_input'>Input % Co-Occurrence Lower Limit (Default: <?php echo settings::get_default_cooccurrence(); ?>, Valid 1-100):</label>-->
    <label for='cooccurrence_input'><b>Co-occurrence percentage lower limit:</b></label>
    <input type='text' id='cooccurrence' name='cooccurrence' maxlength='3'><br>
     This option allows to filter the neighboring pFAMs with a co-occurrence <br>percentage lower than the set value. <br>
    The default value is  <?php echo settings::get_default_cooccurrence(); ?>, Valid values are 1-100.
<p>
<input name='email' id='email' type="text" value="Enter your email address" class="blast_inputs email" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;"><br>
Used for data retrieval only
</p>

<div id='message' style="color: red"><?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?></div>
<!--<h3>Currently Disabled for maintanance</h3>-->
<center><input type="button" id='submit' name="submit" value="Generate GNN" class="css_btn_class_recalc" onclick="uploadFile()"></center>

<hr>

<div align="center">

<h4><b><span style="color: red">BETA</span></b></h4>

<h4><br><progress id='progress_bar' max='100' value='0'></progress></h4>
<br><div id="progressNumber"></div> 
<!--<p>Test 1: <a href='<?php echo $test1_query; ?>'>Example 1: Full network for Peptidase S46 family (IPR019500)</a>-->
<!--<p>Test 2: <a href='<?php echo $test2_query; ?>'>Example 2: 100% Rep-node network for DUF386 (PF04074)</a>-->

<p>UniProt Version: <b><?php echo settings::get_uniprot_version(); ?></b></p>
<p>ENA Version: <b><?php echo settings::get_ena_version(); ?></b></p>
</div>
</form>

<script src="includes/custom-file-input.js" type="text/javascript"></script>

<?php require_once('includes/footer.inc.php'); ?>
