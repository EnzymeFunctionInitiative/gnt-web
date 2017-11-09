<?php 
require_once '../includes/main.inc.php';

$email = "do-not-reply@enzymefunction.org";
$file1 = "TEST1-PeptidaseS46-55percent-full.xgmml";
$file2 = "TEST2-DUF386_repnode-1.00_52percent.xgmml";
$neighborhood = 10;
$cooccurrence = 20;


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
<h3>Stage 1</h3>

<hr>
<img src="images/quest_stages_a.jpg" width="990" height="119" alt="stage 1">
<hr>

<h4>Option 1: Input SSN <a href="#" class="question" target="_blank">?</a></h4>

<p>
    <strong class="blue">Upload the Sequence Similarity Network (SSN) for which you want to create a Genome Neighborhood Network (GNN)</strong>
</p>

<p>
    The submitted SSN must have been generated using Option A, B, C with reading FASTA headers on, or D
    of <a href='http://efi.igb.illinois.edu/efi-est'>EFI-EST <?php echo settings::get_est_version(); ?></a> (released 8/16/2017) to be interpreted.
    <br>The SSNs generated with these Options can be modified in Cytoscape.
</p>

<form name="upload_form" id='upload_form' method="post" action="" enctype="multipart/form-data">
<input type="hidden" id='MAX_FILE_SIZE' name="MAX_FILE_SIZE" value="2147483648" />

<p>
<?php echo make_upload_box("<b>Select a File to Upload:</b><br>", "ssn_file", "progress_bar", "progressNumber", "The acceptable format is uncompressed or zipped xgmml."); ?>
</p>

<p>
    <b>Neighborhood Size:</b>
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
    <label for='cooccurrence_input'><b>Co-occurrence percentage lower limit:</b></label>
    <input type='text' id='cooccurrence' name='cooccurrence' maxlength='3'><br>
     This option allows to filter the neighboring pFAMs with a co-occurrence <br>percentage lower than the set value. <br>
    The default value is  <?php echo settings::get_default_cooccurrence(); ?>, Valid values are 1-100.
<p>
<input name='email' id='email' type="text" value="Enter your email address" class="blast_inputs email" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;"><br>
Used for data retrieval only
</p>

<div id='message' style="color: red"><?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?></div>
<center><input type="button" id='submit' name="submit" value="Generate GNN" class="css_btn_class_recalc" onclick="uploadFile()"></center>

<div align="center">

<?php if (settings::is_beta_release()) { ?>
<h4><b><span style="color: red">BETA</span></b></h4>
<?php } ?>

<h4><br><progress id='progress_bar' max='100' value='0'></progress></h4>
<br><div id="progressNumber"></div> 

<p>
UniProt Version: <b><?php echo settings::get_uniprot_version(); ?></b><br>
ENA Version: <b><?php echo settings::get_ena_version(); ?></b><br>
EFI-GNT Version: <b><?php echo settings::get_gnt_version(); ?></b>
</p>
</div>
</form>

<script src="includes/custom-file-input.js" type="text/javascript"></script>

<div id="upload-arrow-data">
<hr>

<h4>Option 2: Upload Saved GNN Diagram Data <a href="#" class="question" target="_blank">?</a></h4>

<p>
    <strong class="blue">Upload a diagram file from a saved previous GNN job.</strong>
</p>

<form name="diagram_form" id='diagram_form' method="post" enctype="multipart/form-data">
<input type="hidden" id='MAX_FILE_SIZE' name="MAX_FILE_SIZE" value="2147483648" />
<input type="hidden" name="upload_type" value="diagram" />

<p>
<?php echo make_upload_box("<b>Select a File to Upload:</b><br>", "diagram_file", "progress_bar2", "progressNumber2", "The acceptable format is uncompressed sqlite."); ?>
</p>

<center><input type="button" id='submit-diagram' name="submit" value="Upload Diagram Data" class="css_btn_class_recalc" onclick="uploadDiagramFile()"></center>

<div align="center">

<h4><br><progress id='progress_bar2' max='100' value='0'></progress></h4>
<br><div id="progressNumber2"></div> 

</div>
</form>

</div>
</div>

</div>

<?php require_once('includes/footer.inc.php'); ?>