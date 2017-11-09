<?php
require_once "inc/header.inc.php";
require "../libs/user_jobs.class.inc.php";
require_once "../libs/ui.class.inc.php";
require_once "../includes/main.inc.php";

$jobs = array();
if (user_jobs::has_token_cookie()) {
    $userJobs = new user_jobs();
    $userJobs->load_jobs($db, user_jobs::get_user_token());
    $jobs = $userJobs->get_jobs();
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


?>


<p></p>
<p>
The EFI-Genome Neighborhood Tool (EFI-GNT) allows the exploration of the physical association of genes on genomes, i.e. 
gene clustering. EFI-GNT enables a user to retrieve, display, and interact with genome neighborhood information for 
large datasets of sequences.
</p>
<!--
<p class="center"><a href='stepa.php'><button class="dark">Create a new genome neighborhood network</button></a></p>
<p class="center"><a href='start_diagram_upload.php'><button class="dark">Upload a saved GNN diagram data file for review</button></a></p>
-->

<div class="update_message">
    The GNT database has been updated to use UniProt
    <?php echo settings::get_uniprot_version(); ?> and ENA
    <?php echo settings::get_ena_version(); ?>.
</div>

<div id="accordion">
<?php if (count($jobs) > 0) { ?>
    <h3>Previous Jobs</h3>
    <div>
        <div>
            <table class="pretty">
                <thead>
                    <th>ID</th>
                    <th>Filename</th>
                    <th>Date Completed</th>
                </thead>
                <tbody>
<?php
for ($i = 0; $i < count($jobs); $i++) {
    $key = $jobs[$i]["key"];
    $id = $jobs[$i]["id"];
    $name = $jobs[$i]["filename"];
    $dateCompleted = $jobs[$i]["completed"];

    $linkStart = $dateCompleted == "RUNNING" ? "" : "<a href=\"stepc.php?id=$id&key=$key\">";
    $linkEnd = $dateCompleted == "RUNNING" ? "" : "</a>";

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
        </div>
    </div>
<?php } ?>

    <h3>Create Genomic Neighborhood Network</h3>
    <div>

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
            <p>
            <input name='ssn_email' id='ssn_email' type="text" value="Enter your email address" class="email" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;"><br>
            When the file has been uploaded and processed, you will receive an email containing a link
            to download the data.
            </p>

            <div id='ssn_message' style="color: red"><?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?></div>
            <center>
                <div><button type="button" id='ssn_submit' name="ssn_submit" class="dark"
                        onclick="uploadFile('ssn_file','upload_form','progress_number','progress_bar','ssn_message','ssn_email','ssn_submit',true)">
                            Generate GNN
                    </button></div>
                <div><progress id='progress_bar' max='100' value='0'></progress></div>
                <div id="progress_number"></div>
            </center>

<!--
            <div class="update_message">
                The GNT database has been updated to use UniProt
                <?php echo settings::get_uniprot_version(); ?> and ENA
                <?php echo settings::get_ena_version(); ?>.
            </div>
-->
        </form>

    </div>

    <h3>Upload Saved Genomic Neighborhood Diagram Data</h3>
    <div>
        <form name="upload_diagram_form" id='upload_diagram_form' method="post" action="" enctype="multipart/form-data">
            <p>
            <?php echo ui::make_upload_box("<b>Select a File to Upload:</b><br>", "diagram_file", "progress_bar_diagram", "progress_number_diagram", "The acceptable format is sqlite."); ?>
            </p>

            <p>
            <input name='email' id='diagram_email' type="text" value="Enter your email address" class="email" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;"><br>
            When the file has been uploaded and processed, you will receive an email containing a link
            to view the diagrams.
            </p>

            <div id='diagram_message' style="color: red"><?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?></div>
            <center>
                <div><button type="button" id="diagram_submit" name="submit" class="dark" onclick="uploadFile('diagram_file','upload_diagram_form','progress_number_diagram','progress_bar_diagram','diagram_message','diagram_email','diagram_submit',false)">Upload Diagram Data</button></div>
                <div><progress id="progress_bar_diagram" max="100" value="0"></progress></div>
                <div id="progress_number_diagram"></div>
            </center>
        </form> 
    </div>

    <h3>Tutorial</h3>
    <div>

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

</div>


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

<!--<p class="center"><a href='stepa.php'><button class="dark">Begin EFI-GNT</button></a></p>-->

<script>
$( function() {
    $( "#accordion" ).accordion({
        heightStyle: "content"
    });
} );
</script>
<script src="js/custom-file-input.js" type="text/javascript"></script>

<?php require_once('inc/footer.inc.php'); ?>


