<?php
require_once "../includes/main.inc.php";
require_once "inc/header.inc.php";

?>


<h2>Version 2.0 Release Notes</h2>

Several new features have been added to the GNT:

<ul>
    <li>To improve usability of the GNT, the various tools have been separated into separate tabs.</li>
<?php if (settings::is_recent_jobs_enabled()) { ?>
    <li>Recently-ran jobs are now listed on the first tab of the GNT. This feature is enabled
        the first time a new job is submitted.</li>
<?php } ?>
    <li>When a GNN is created, the user now has the option of downloading the genomic neighborhood
        diagram data file; this file can be uploaded and visualized using the new "View Saved Diagrams"
        feature.</li>
    <li>The user now has the option of downloading the displayed genomic neighborhood diagrams in SVG
        format.  SVG is a vector graphics format that can be edited using Adobe Illustrator or
        the free <a href="http://inkscape.org">InkScape editor</a>.  Those programs can be used to  
        export publication-quality figures.</li>
    <li>The ability to retrieve genomic neighborhood diagrams without creating and uploading
        an SSN is now available on the "Retrieve Neighbhorhoods" tab. It is now possible to 
        run a BLAST against an input sequence and retrieve neighborhoods for the sequences returned 
        by the BLAST.  In addition, it is possible to submit FASTA sequences and/or files to retrieve
        neighborhoods for the sequences that were detected in the FASTA headers.  Finally, lists
        of UniProt or NCBI IDs can be uploaded and neighborhoods will be retrieved for any
        recognized IDs.</li>
</ul>

<p></p>

<p class="center"><a href="index.php"><button class="dark">Run GNT</button></a></p>


<?php require_once("inc/footer.inc.php"); ?>


