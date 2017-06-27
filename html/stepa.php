<?php 
require_once 'includes/main.inc.php';

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

require_once('includes/header.inc.php'); 

?>
<h3>Start With...    </h3>
<h4>An Introduction</h4>
<p>Start here if you are new to the &quot;Genome Neighborhood Networks Tool&quot;.</p>
<h4 class='center'><a href='tutorial.php'><button class='css_btn_class'>GO</button></a></h4>

<hr>
<img src="images/quest_stages_a.jpg" width="990" height="119" alt="stage 1">
<hr>
<h4>Input<a href="#" class="question" target="_blank">?</a></h4>
<h4><strong  class="blue">Upload a Sequence Similarity Network (SSN). The acceptable format is
xgmml; the maximum size is <?php echo ini_get('post_max_size'); ?>.

<br>The SSN must be generated using either Option A or Option B of <a href='http://efi.igb.illinois.edu/efi-est'>EFI-EST</a>;
Cytoscape can be used to edit/modify the SSN.</strong></h4>
<form name="upload_form" id='upload_form' method="post" action="" enctype="multipart/form-data">
<input type="hidden" id='MAX_FILE_SIZE' name="MAX_FILE_SIZE" value="2147483648" />
<label for="fileToUpload">Select a File to Upload</label><br />
<input type="file" id='ssn_file' name="ssn_file" data-url='server/php/' class="blast_inputs email border"><br><br>
<p>Neighborhood Size (default: <?php echo $default_neighbor_size; ?>)
<select name='neighbor_size' id='neighbor_size'>
<?php echo $neighbor_size_html; ?>

</select>
<p>
<input type='checkbox' id='newneighbormethod' name='newneighbormethod' value='1' checked> Use new starting genome determination method
</p>
<p>
<label for='cooccurrence_input'>Input % Co-Occurrence Lower Limit (Default: <?php echo settings::get_default_cooccurrence(); ?>, Valid 1-100):</label>
<input type='text' id='cooccurrence' name='cooccurrence' maxlength='3'><br>
<p>
<input name='email' id='email' type="text" value="Enter your email address" class="blast_inputs email" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;"><br>
<span class="smalltext">Used for data retrieval only</span>
</p>

<div id='message'><?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?></div>
<hr>
<!--<h3>Currently Disabled for maintanance</h3>-->
<input type="button" id='submit' name="submit" value="GO" class="css_btn_class" onclick="uploadFile()">

<h4><br><progress id='progress_bar' max='100' value='0'></progress></h4>
<br><div id="progressNumber"></div> 
<!--<p>Test 1: <a href='<?php echo $test1_query; ?>'>Example 1: Full network for Peptidase S46 family (IPR019500)</a>-->
<!--<p>Test 2: <a href='<?php echo $test2_query; ?>'>Example 2: 100% Rep-node network for DUF386 (PF04074)</a>-->

<p>UniProt Version: <b><?php echo settings::get_uniprot_version(); ?></b></p>
<p>ENA Version: <b><?php echo settings::get_ena_version(); ?></b></p>
</form>
<?php require_once('includes/footer.inc.php'); ?>
