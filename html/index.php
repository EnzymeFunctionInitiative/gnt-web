<?php
require_once "inc/header.inc.php";
require "../libs/user_jobs.class.inc.php";
require_once "../libs/ui.class.inc.php";
require_once "../includes/main.inc.php";

$userEmail = "Enter your email address";

$showPreviousJobs = false;
$gnnJobs = array();
$diagramJobs = array();
if (settings::is_recent_jobs_enabled() && user_jobs::has_token_cookie()) {
    $userJobs = new user_jobs();
    $userJobs->load_jobs($db, user_jobs::get_user_token());
    $gnnJobs = $userJobs->get_jobs();
    $diagramJobs = $userJobs->get_diagram_jobs();
    $userEmail = $userJobs->get_email();
    $showPreviousJobs = count($gnnJobs) > 0 || count($diagramJobs) > 0;
}


$neighborhood = 10;
$cooccurrence = 20;
$neighbor_size_html = "";
$default_neighbor_size = settings::get_default_neighbor_size();
for ($i=3;$i<=20;$i++) {
    if ($i == $default_neighbor_size)
        $neighbor_size_html .= "<option value='" . $i . "' selected='selected'>" . $i . "</option>";
    else
        $neighbor_size_html .= "<option value='" . $i . "'>" . $i . "</option>";
}

$updateMessage = functions::get_update_message();

?>


<p></p>
<p>
The EFI-Genome Neighborhood Tool (EFI-GNT) allows the exploration of the physical association of genes on genomes, i.e. 
gene clustering. EFI-GNT enables a user to retrieve, display, and interact with genome neighborhood information for 
large datasets of sequences.
</p>

<div id="update-message" class="update_message initial-hidden">
<?php if (isset($updateMessage)) echo $updateMessage; ?>
</div>

A listing of new features and other information pertaining to EST is available on the <a href="notes.php">release notes page</a>. 

<div class="tabs">
    <ul class="tab-headers">
<?php if ($showPreviousJobs) { ?>
        <li class="active"><a href="#jobs">Previous Jobs</a></li>
<?php } ?>
        <li><a href="#create">Create GNN</a></li>
        <li><a href="#diagrams">View Saved Diagrams</a></li>
        <li><a href="#create-diagrams">Retrieve Neighborhoods</a></li>
        <li <?php if (! $showPreviousJobs) echo "class=\"active\""; ?>><a href="#tutorial">Tutorial</a></li>
    </ul>

    <div class="tab-content">
<?php if ($showPreviousJobs) { ?>
        <div id="jobs" class="tab active">
<?php } ?>
<?php if (count($gnnJobs) > 0) { ?>
            <h4>GNN Jobs</h4>
            <table class="pretty">
                <thead>
                    <th class="id-col">ID</th>
                    <th>Filename</th>
                    <th class="date-col">Date Completed</th>
                </thead>
                <tbody>
<?php
for ($i = 0; $i < count($gnnJobs); $i++) {
    $key = $gnnJobs[$i]["key"];
    $id = $gnnJobs[$i]["id"];
    $name = $gnnJobs[$i]["filename"];
    $dateCompleted = $gnnJobs[$i]["completed"];

    $linkStart = $dateCompleted == "RUNNING" ? "" : "<a href=\"stepc.php?id=$id&key=$key\">";
    $linkEnd = $dateCompleted == "RUNNING" ? "" : "</a>";

    if (array_key_exists("diagram", $gnnJobs[$i]))
        $linkStart = "<a href=\"view_diagrams.php?upload-id=$id&key=$key\">";

    echo <<<HTML
                    <tr>
                        <td>$linkStart${id}$linkEnd</td>
                        <td>$linkStart${name}$linkEnd</td>
                        <td>$dateCompleted</td>
                    </tr>
HTML;
}
?>
                </tbody>
            </table>
<?php } ?>
            
<?php if (count($diagramJobs) > 0) { ?>
            <h4>Diagram Jobs</h4>
            <table class="pretty">
                <thead>
                    <th class="id-col">ID</th>
                    <th class="name-col">Job Name</th>
                    <th class="type-col">Job Type</th>
                    <th class="date-col">Date Completed</th>
                </thead>
                <tbody>
<?php
for ($i = 0; $i < count($diagramJobs); $i++) {
    $key = $diagramJobs[$i]["key"];
    $id = $diagramJobs[$i]["id"];
    $name = $diagramJobs[$i]["filename"];
    $dateCompleted = $diagramJobs[$i]["completed"];

    $linkStart = $dateCompleted == "RUNNING" ? "" : "<a href=\"stepc.php?id=$id&key=$key\">";
    $linkEnd = $dateCompleted == "RUNNING" ? "" : "</a>";
    $idField = $diagramJobs[$i]["id_field"];
    $jobType = $diagramJobs[$i]["verbose_type"];

    $linkStart = "<a href=\"view_diagrams.php?$idField=$id&key=$key\">";

    echo <<<HTML
                    <tr>
                        <td>$linkStart${id}$linkEnd</td>
                        <td>$linkStart${name}$linkEnd</td>
                        <td>$linkStart${jobType}$linkEnd</td>
                        <td>$dateCompleted</td>
                    </tr>
HTML;
}
?>
                </tbody>
            </table>
<?php } ?>
<?php if ($showPreviousJobs) { ?>
        </div>
<?php } ?>

        <div id="create" class="tab <?php echo (!$showPreviousJobs ? "active" : "") ?>">
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
                <?php echo ui::make_upload_box("<b>Select a File to Upload:</b><br>", "ssn_file", "progress_bar", "progress_number", "The acceptable format is uncompressed or zipped xgmml."); ?>
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
                </p>
                <p>
                    <input name='ssn_email' id='ssn_email' type="text" value="<?php echo $userEmail; ?>" class="email" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;"><br>
                    When the file has been uploaded and processed, you will receive an email containing a link
                    to download the data.
                </p>
    
                <div id='ssn_message' style="color: red">
                    <?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?>
                </div>
                <center>
                    <div><button type="button" id='ssn_submit' name="ssn_submit" class="dark"
                            onclick="uploadFile('ssn_file','upload_form','progress_number','progress_bar','ssn_message','ssn_email','ssn_submit',true)">
                                Generate GNN
                        </button></div>
                    <div><progress id='progress_bar' max='100' value='0'></progress></div>
                    <div id="progress_number"></div>
                </center>
            </form>
        </div>

        <div id="diagrams" class="tab">
            <form name="upload_diagram_form" id='upload_diagram_form' method="post" action="" enctype="multipart/form-data">
                <p>
                    <?php echo ui::make_upload_box("<b>Select a File to Upload:</b><br>", "diagram_file", "progress_bar_diagram", "progress_number_diagram", "The acceptable format is sqlite."); ?>
                </p>
    
                <p>
                    <input name='email' id='diagram_email' type="text" value="<?php echo $userEmail; ?>" class="email" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;"><br>
                    When the file has been uploaded and processed, you will receive an email containing a link
                    to view the diagrams.
                </p>
    
                <div id='diagram_message' style="color: red">
                    <?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?>
                </div>
                <center>
                    <div><button type="button" id="diagram_submit" name="submit" class="dark"
                                onclick="uploadFile('diagram_file','upload_diagram_form','progress_number_diagram','progress_bar_diagram','diagram_message','diagram_email','diagram_submit',false)">
                            Upload Diagram Data</button>
                    </div>
                    <div><progress id="progress_bar_diagram" max="100" value="0"></progress></div>
                    <div id="progress_number_diagram"></div>
                </center>
            </form> 
        </div>

        <div id="create-diagrams" class="tab">
            <div style="margin-bottom: 10px;">Clicking on the headers below provides access to various ways of generating genomic network diagrams.</div>
            <div id="create-accordion">
                <h3>Single Sequence BLAST</h3>
                <div>
                    <p>
                    The provided sequence is used as the query for a BLAST search of the UniProt database.
                    The retrieved sequences are used to generate genomic neighborhood diagrams. 
                    </p>

                    <form name="create_diagrams" id="create_diagram_form" method="post" action="">
                        <input type="hidden" id="option-a-option" name="option" value="a">
                        <textarea class="options" id="option-a-input" name="option-a-input"><?php
                            if (isset($_POST['option-a-input'])) { echo $_POST['option-a-input']; }
                            ?></textarea>

                        <div class="create-job-options">
                            <table>
                                <tr>
                                    <td>Optional job title:</td>
                                    <td>
                                        <input type="text" class="small" id="option-a-title" name="title" value='<?php
                                                    if (isset($_POST["title"]))
                                                        echo $_POST["title"];
                                                    else
                                                        echo "";
                                            ?>'>
                                    </td>
                                    <td></td>
                                </tr>

    
                                <!--
                                <div class="advanced-toggle">
                                    Advanced Options <i class="fa fa-plus-square" aria-hidden="true"></i>
                                </div>
                                <div class="advanced-options">
                                </div>
                                -->
                                <tr>
                                    <td><label for="max-seqs">Maximum Blast Sequences:</label></td>
                                    <td>
                                        <input type="text" id="option-a-max-seqs" class="small" name="max-seqs" value='<?php
                                                if (isset($_POST["max-seqs"])) {
                                                    echo $_POST["max-seqs"];
                                                } else {
                                                    echo settings::get_default_blast_seq(); }
                                                    ?>'>
                                   </td>
                                   <td>
                                        Maximum number of sequences retrieved (&le; <?php echo settings::get_max_blast_seq(); ?>;
                                        default: <?php echo settings::get_default_blast_seq(); ?>)
                                    </td>
                                </tr>
                                <tr>
                                    <td>Neighborhood window size:</td>
                                    <td>
                                        <input type="text" id="option-a-nb-size" class="small" name="nb-size" value='<?php
                                                if (isset($_POST["nb-size"])) {
                                                    echo $_POST["nb-size"];
                                                } else {
                                                    echo settings::get_default_neighborhood_size(); }
                                            ?>'>
                                    </td>
                                    <td>
                                        Number of neighbors to retrieve on either side of the query sequence for each BLAST result
                                        (default: <?php echo settings::get_default_neighborhood_size(); ?>)
                                    </td>
                                </tr>
                                <tr>
                                    <td>E-Value:</td>
                                    <td>
                                        <input type="text" class="small" id="option-a-evalue" name="evalue" value='<?php
                                                if (isset($_POST["evalue"])) {
                                                    echo $_POST["evalue"];
                                                } else {
                                                    echo settings::get_default_evalue(); }
                                            ?>'>
                                    </td>
                                    <td>
                                        Negative log of e-value for all-by-all BLAST (&ge; 1; default:
                                        <?php echo settings::get_default_evalue(); ?>)
                                    </td>
                                </tr>
                            </table>
                            <div>
                                Email address:
                                <input name='email' id='option-a-email' type="text" value="<?php echo $userEmail; ?>" class="email" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;">
                            </div>
                            <div>
                                When the file has been uploaded and processed, you will receive an email containing a link
                                to view the diagrams.
                            </div>
                        </div>
    
                        <div id='option-a-message' style="color: red">
                            <?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?>
                        </div>

                        <center>
                            <button type="button" class="dark"
                                            onclick="submitOptionAForm('create_diagram.php', 'option-a-option', 'option-a-input',
                                                                       'option-a-title', 'option-a-evalue', 'option-a-max-seqs',
                                                                       'option-a-email', 'option-a-nb-size', 'option-a-message');"
                                >Submit</button>
                        </center>
                    </form>
                </div>

                <h3>Sequence ID Lookup</h3>
                <div>
                    <p>
                    The genomic neighborhoods are retreived for the UniProt, NCBI, EMBL-EBI ENA, and PDB identifiers
                    that are provided in the input box below.  Not all identifiers may exist in the EFI-GNT database so
                    the results will only include diagrams for sequences that were identified.
                    </p>

                    <form name="create_diagrams" id="create_diagram_form" method="post" action="create_diagram.php">
                        <input type="hidden" id="option-d-option" name="option" value="d">
                        <textarea class="options" id="option-d-input" name="input"><?php
                            if (isset($_POST['input'])) { echo $_POST['input']; }
                            ?></textarea>

                        <div style="margin-bottom: 20px">
                            <?php echo ui::make_upload_box(
                                        "Alternatively, a file containing a list of IDs can be uploaded:<br>",
                                        "option-d-file", "option-d-progress-bar", "option-d-progress-number",
                                        "The acceptable format is text."); ?>
                        </div>

                        <div class="create-job-options">
                            <table>
                                <tr>
                                    <td>Optional job title:</td>
                                    <td>
                                        <input type="text" class="small" id="option-d-title" name="title" value='<?php
                                                    if (isset($_POST["title"]))
                                                        echo $_POST["title"];
                                                    else
                                                        echo "";
                                            ?>'>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Neighborhood window size:</td>
                                    <td>
                                        <input type="text" id="option-d-nb-size" class="small" name="nb-size" value='<?php
                                                if (isset($_POST["nb-size"])) {
                                                    echo $_POST["nb-size"];
                                                } else {
                                                    echo settings::get_default_neighborhood_size(); }
                                            ?>'>
                                    </td>
                                    <td>
                                        Number of neighbors to retrieve on either side of the query sequence for each BLAST result
                                        (default: <?php echo settings::get_default_neighborhood_size(); ?>)
                                    </td>
                                </tr>
                            </table>

                            <div>
                                Email address:
                                <input name='email' id='option-d-email' type="text" value="<?php echo $userEmail; ?>" class="email" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;">
                            </div>
                            <div>
                                When the file has been uploaded and processed, you will receive an email containing a link
                                to view the diagrams.
                            </div>
                        </div>
    
                        <div id="option-d-message" style="color: red">
                            <?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?>
                        </div>

                        <center>
                            <button type="button" class="dark"
                                            onclick="submitOptionDForm('create_diagram.php', 'option-d-option', 'option-d-input',
                                                'option-d-title', 'option-d-email', 'option-d-nb-size', 'option-d-file',
                                                'option-d-progress-number', 'option-d-progress-bar', 'option-d-message');"
                                >Submit</button>
                            <div><progress id="option-d-progress-bar" max="100" value="0"></progress></div>
                            <div id="option-d-progress-number"></div>
                        </center>
                    </form>
                </div>

                <h3>FASTA Sequence Lookup</h3>
                <div>
                    <p>
                    The genomic neighborhoods are retreived for the UniProt, NCBI, EMBL-EBI ENA, and PDB identifiers
                    that are identified in the FASTA <b>headers</b>.  Not all identifiers may exist in the EFI-GNT database so
                    the results will only include diagrams for sequences that were identified.
                    </p>

                    <form name="create_diagrams" id="create_diagram_form" method="post" action="create_diagram.php">
                        <input type="hidden" id="option-c-option" name="option" value="c">
                        <textarea class="options" id="option-c-input" name="input"><?php
                            if (isset($_POST['input'])) { echo $_POST['input']; }
                            ?></textarea>

                        <div style="margin-bottom: 20px">
                            <?php echo ui::make_upload_box(
                                        "Alternatively, a file containing FASTA headers and sequences can be uploaded:<br>",
                                        "option-c-file", "option-c-progress-bar", "option-c-progress-number",
                                        "The acceptable format is text."); ?>
                        </div>

                        <div class="create-job-options">
                            <table>
                                <tr>
                                    <td>Optional job title:</td>
                                    <td>
                                        <input type="text" class="small" id="option-c-title" name="title" value='<?php
                                                    if (isset($_POST["title"]))
                                                        echo $_POST["title"];
                                                    else
                                                        echo "";
                                            ?>'>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Neighborhood window size:</td>
                                    <td>
                                        <input type="text" id="option-c-nb-size" class="small" name="nb-size" value='<?php
                                                if (isset($_POST["nb-size"])) {
                                                    echo $_POST["nb-size"];
                                                } else {
                                                    echo settings::get_default_neighborhood_size(); }
                                            ?>'>
                                    </td>
                                    <td>
                                        Number of neighbors to retrieve on either side of the query sequence for each BLAST result
                                        (default: <?php echo settings::get_default_neighborhood_size(); ?>)
                                    </td>
                                </tr>
                            </table>

                            <div>
                                Email address:
                                <input name='email' id='option-c-email' type="text" value="<?php echo $userEmail; ?>" class="email" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;">
                            </div>
                            <div>
                                When the file has been uploaded and processed, you will receive an email containing a link
                                to view the diagrams.
                            </div>
                        </div>
    
                        <div id="option-c-message" style="color: red">
                            <?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?>
                        </div>

                        <center>
                            <button type="button" class="dark"
                                            onclick="submitOptionCForm('create_diagram.php', 'option-c-option', 'option-c-input',
                                                'option-c-title', 'option-c-email', 'option-c-nb-size', 'option-c-file',
                                                'option-c-progress-number', 'option-c-progress-bar', 'option-c-message');"
                                >Submit</button>
                            <div><progress id="option-c-progress-bar" max="100" value="0"></progress></div>
                            <div id="option-c-progress-number"></div>
                        </center>
                    </form>
                </div>
            </div>
        </div>

        <div id="tutorial" class="tab <?php if (!$showPreviousJobs) echo "active"; ?>">
            <h3>EFI-Genome Neighborhood Tool Overview</h3>
    
            <p>
            Although other tools allow comparison of gene neighborhoods among multiple prokaryotic genomes to allow inference of 
            phylogenetic relationships, e.g., IMG (<a href="https://img.jgi.doe.gov" target="_blank">https://img.jgi.doe.gov</a>)
            and  PATRIC (<a href="https://www.patricbrc.org" target="_blank">https://www.patricbrc.org</a>), EFI-GNT enables 
            comparison of the genome neighborhoods for clusters of similar protein sequences in order to facilitate the assignment 
            of function within protein families and superfamilies.
            </p>
    
            <p>
            EFI-GNT is focused on placing protein families and superfamilies into a context. A sequence similarity network (SSN) 
            with defined protein clusters is used as an input. Each sequence within a SSN is used as a query for interrogation of 
            its genome neighborhood.
            </p>
    
            <h3>EFI-GNT acceptable input</h3>
    
            <p>
            The sequence datasets are generated from an SSN produced by the EFI-Enzyme Similarity Tool (EFI-EST). Acceptable 
            SSNs are generated for an entire Pfam and/or InterPro protein family (from Option B of EFI-EST), a focused region of a 
            family (from Option A of EFI-EST), a set of protein sequence that can be identified from FASTA headers (from option C of 
            EFI-EST with header reading) or a list of recognizable UniProt and/or NCBI IDs (from option D of EFI-EST). A manually 
            modified SSN within Cytoscape that originated from any of the EST options is also acceptable. SSNs that have been 
            colored using the "Color SSN Utility" of EFI-EST and that originated from any of acceptable Options are also acceptable.
            </p>
    
            <h3>Principle of GNT analysis</h3>
    
            <p>
            Protein encoding genes that are neighbors of input queries (within a defined window on either side) are collected from 
            sequence files for bacterial (prokaryotic and archaeal) and fungal genomes in the European Nucleotide Archive (ENA) 
            database. The co-occurrence frequencies of the identified neighboring sequences with the input queries are calculated as 
            well as the absolute values of the distances in open reading frames (orfs) between the queries and neighbors. The 
            calculated information is provided as Genome Neighborhood Networks (GNNs), in addition to a colored version of the input 
            SSN that aids analysis of the GNNs.
            </p>
    
            <h3>EFI-GNT output</h3>
    
            <p>
            EFI-GNT generates two formats of the Genome Neighborhood Network (GNN) as well as a colored version of the input SSN 
            that aids analysis of the GNNs.
            </p>
    
            <p>
            The UniProt accession IDs for the queries and the neighbors, the Pfam families for the neighbors, and both the 
            query-neighbor distances (in orfs) and co-occurrence frequencies are provided in the GNNs. The GNNs and colored SSN are 
            downloaded, visualized, and analyzed using Cytoscape.
            </p>
    
            <p>
            The user can use Cytoscape to filter the GNNs for a range of query-neighbor distances and/or co-occurrence frequencies 
            to enable the identification of functionally related proteins/enzymes, with shorter distances and great co-occurrence 
            frequencies suggesting functional linkage in a metabolic pathway. With the identities of the Pfam families for the 
            neighbors, the user may be able to infer the in vitro enzymatic activities of the queries and neighbors and predict the 
            reactions in the metabolic pathway in which they participate.
            </p>
    
            <center>
                <p><img src='images/tutorial/intro_figure_1.jpg'></p>
                <p><i>Figure 1:</i> Examples of colored SSN (left) and a hub-and-spoke cluster from a GNN (right).</p>
            </center>
    
    
    
            <p class="center"><a href='tutorial.php'><button class="light">Continue Tutorial</button></a></p>
    
        </div>
    </div> <!-- tab-content -->
</div> <!-- tabs -->


<div align="center">
    <?php if (settings::is_beta_release()) { ?>
    <h4><b><span style="color: red">BETA</span></b></h4>
    <?php } ?>

    <p>
    UniProt Version: <b><?php echo settings::get_uniprot_version(); ?></b><br>
    ENA Version: <b><?php echo settings::get_ena_version(); ?></b><br>
    EFI-GNT Version: <b><?php echo settings::get_gnt_version(); ?></b>
    </p>
</div>

<script>
    $(document).ready(function() {
        $(".tabs .tab-headers a").on("click", function(e) {
            var curAttrValue = $(this).attr("href");
            $(".tabs " + curAttrValue).fadeIn(300).show().siblings().hide();
            $(this).parent("li").addClass("active").siblings().removeClass("active");
            e.preventDefault();
        });

        $(".advanced-toggle").click(function () {
            $header = $(this);
            //getting the next element
            $content = $header.next();
            //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
            $content.slideToggle(100, function () {
                if ($content.is(":visible")) {
                    $header.find("i.fa").addClass("fa-minus-square");
                    $header.find("i.fa").removeClass("fa-plus-square");
                } else {
                    $header.find("i.fa").removeClass("fa-minus-square");
                    $header.find("i.fa").addClass("fa-plus-square");
                }
            });
        
        });

        $("#create-accordion" ).accordion({
            icons: { "header": "ui-icon-plus", "activeHeader": "ui-icon-minus" },
            heightStyle: "content"
        });
    });
</script>
<script src="js/custom-file-input.js" type="text/javascript"></script>

<?php require_once('inc/footer.inc.php'); ?>


