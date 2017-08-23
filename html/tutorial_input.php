<?php
require_once 'includes/main.inc.php';
require_once 'includes/tutorial_header.inc.php';
?>

 <div class="content_nav">
	<?php require_once('includes/tutorial_nav.php'); ?>
  
  </div>
  <div class="content_content">

<h3>EFI-GNT Input and Output Pages</h3>

<h4>Start Page/Input</h4>

<p>
Acceptable SSNs are generated for an entire Pfam and/or InterPro protein family 
(from Option B of EFI-EST), a focused region of a family (from Option A of 
EFI-EST), a set of protein sequence that can be identified from FASTA headers 
(from option C of EFI-EST with header reading) or a list of recognizable 
UniProt and/or NCBI IDs (from option D of EFI-EST). An SSN manually modified 
within Cytoscape that originated from any of acceptable EFI-EST Options is also 
acceptable. SSNs that have been colored using the "Color SSN Utility" of 
EFI-EST and that originated from any of the available options are also acceptable.
</p>

<p>
<b>SSNs generated with Option C of the EFI-EST without the header reading option 
selected will not work—the process for generating the GNN requires that the 
sequences have UniProt IDs.</b>
</p>

<p>
The maximum size of the xgmml file is 2048 MB. The SSN may be either a full SSN 
(a node for each sequence) or a representative-node (rep-node) SSN (sequences 
sharing greater than a user-selected sequence identity are located in the same 
metanode).  The xgmml file may be either uncompressed or zipped.
</p>

<p>
EFI-GNT uses a default &plusmn; 10 orf window to collect the genome neighbors; the 
user can select a smaller window (from &plusmn; 3 – &plusmn; 20 orfs) in the "Neighborhood 
Size" pull-down menu.
</p>

<p>
EFI-GNT collects all genome neighbors within the specified window. However, it 
will display a spoke node only if the query-neighbor co-occurrence frequency is 
greater than a specified value. The default value is 20%. A smaller value, 
e.g., 5%, should be used to find neighbors that co-occur with low frequency, 
often as the result of phylogenetically diverse genome arrangements of 
functionally linked pathway enzymes. As the co-occurrence frequency is 
decreased, a larger number of neighbors and Pfam families will be reported in 
the GNN and the signal-to-noise ratio will decrease.
</p>

<p>
As with EFI-EST, the user also inputs an e-mail address to which an email 
containing a link to the results will be sent.
</p>

<p><img src="images/tutorial/submission_form.png" width="100%" /></p>

<h4>Download Page/Output</h4>

<p>
When the results are available (typically a few minutes, although the time 
required for the analysis increases with the number of query sequences in the 
input SSN), an e-mail with a link to the output is sent to the address provided 
by the user on the Start page. The link will be active for seven days.
</p>

<p>
A summary of the submission is available.
</p>

<p><img src="images/tutorial/net_info.png" width="100%" /></p>

<p>
The EFI-GNT output is three xgmml files and several text/spreadsheet files.
</p>

<p><img src="images/tutorial/gnt_results.png" width="100%" /></p>

<p>
Colored SSN: The colored version of the SSN described in the previous section 
is available for download as an xgmml file for viewing in Cytoscape. This SSN 
allows the user to quickly associate SSN cluster spoke nodes in the GNNs with 
clusters in the input query SSN.
</p>

<p>
Two formats of the GNN: The two formats of the GNN described in the previous 
section are available for download as xgmml files and viewing/analysis in 
Cytoscape:
</p>

<ol>
<li>A cluster is present for each Pfam family (hub-node) that was identified as 
a neighbor to queries in the SSN clusters (spoke-nodes). This format allows the 
user to assess whether queries in multiple SSN clusters are neighbors to 
members of the same Pfam family and, therefore, may have the same in vitro 
activities and in vivo metabolic functions.</li>

<li>A cluster is present for each query SSN cluster (hub-node) that was used to 
identify genome neighbors (spoke-nodes). This format allows the user to 
identify functionally linked enzymes, as deduced from genome proximity, that 
constitute the metabolic pathway in which the sequences in the query SSN 
cluster participate.</li>
</ol>

<p>
In addition to the colored SSN and GNNs, several text files/folders of text 
files are available for download:  
</p>

<ol>
<li>"UniProt ID-Color-Cluster Number Mapping Table" is a tab-delimited text 
file with three columns with headers, "UniProt ID", "Cluster Number", and 
"Cluster Color"; a description of this file and its use is provided in the 
Color SSN Utility section of the
<a href="<?php echo settings::get_est_url(); ?>/tutorial.php">EFI-EST tutorial</a>.</li>

<li>"UniProt IDs per Cluster" is a folder of files for each cluster that list 
the UniProt IDs for the sequences in the cluster; a description of the files in 
this folder and their use was provided in the Color SSN Utility section of the
<a href="<?php echo settings::get_est_url(); ?>/tutorial.php">EFI-EST tutorial</a>.</li>

<li>"FASTA Files per Cluster" is a folder of files for each cluster that 
contain the FASTA-formatted sequences for the sequences in the cluster; a 
description of the files in this folder and their use was provided in the Color 
SSN Utility section of the
<a href="<?php echo settings::get_est_url(); ?>/tutorial.php">EFI-EST tutorial</a>.</li>

<li>"Pfam Neighbor Mapping Tables" is a folder of tab-delimited text files for 
each neighbor Pfam family that includes the following columns:  "Query ID", 
"Neighbor ID", "Neighbor Pfam", "SSN Query Cluster #", "SSN Query Cluster 
Color", "Query-Neighbor Distance" (absolute value of distances in ORFs), and 
"Query-Neighbor Directions" (relative directions of transcription).  These 
files can be used with the Cytoscape BridgeDB App to add these columns as node 
attributes to the SSN for the neighbor Pfam family so that the neighbors can be 
located/analyzed in the sequence-function context of the family.  The "SSN 
Query Cluster Color" node attributes can be used with "pass-through mapping" in 
the Cytoscape Style Color panel to color the nodes in the neighbor Pfam family 
with the colors in the colored SSN generated by EFI-GNT 1.0, thereby 
facilitating the determination of whether the neighbors identified in the 
genome neighborhoods are orthologous.  A concatenated file that contains all of 
the information is also available and may be more convenient for adding node 
attributes to SSNs for multiple Pfam families.</li>

<li>"Neighbors without PFAM assigned per Cluster" is a folder of tab-delimited 
text files for each SSN cluster that lists the accession IDs of neighbors not 
assigned to Pfam families.  These files allow SSNs to be generated with Option 
D of EFI-EST 2.0 so that protein families not curated by Pfam can be 
identified.  The user should use an alignment score of ~20 to filter the SSN; 
in most cases, this alignment score will segregate the SSN into protein 
families.</li>

<li>"No Matches/No Neighbors File" is a tab-delimited text file with two 
columns:  "UniProt ID" and "No Match/No Neighbor" ("nomatch" or "neighbor").  
The same information is included in the colored SSN generated by EFI-GNT 1.0 
(<i>vide infra</i>).</li>
</ol>






<!--
<p>The EFI-GNT web tool is a user-friendly interface to the software that accepts the query input SSN, collects the genome neighbors, and generates the colored version of the input SSN, the GNN in the two formats described in the previous section, and text files for download.  The software is run on a server housed in the Institute for Genomic Biology (IGB) at the University of Illinois at Urbana-Champaign.</p>

<p><b><font color='red'>Start Page/Input</font></b></p>

<p><b>The input SSN must be in the form of an xgmml file for 1) a SSN generated by either Option A or Option B of the EFI-EST web tool or 2) a SSN generated by either Option A or Option B from the EFI-EST web too and manipulated and exported from <a href='http://www.cytoscape.org/download.html'>Cytoscape</a>.</b></p>

<p><b>SSNs generated with Option C of the EFI-EST will not work—the process for generating the GNN requires that the sequences have UniProt IDs.</b></p>

<p>The maximum size of the xgmml file is 2048 MB.  The SSN may be either a full SSN (a node for each sequence) or a representative-node (rep-node) SSN (sequences sharing greater than a user-selected sequence identity are located in the same metanode).</p>

<p>EFI-GNT uses a default &plusmn; 10 orf window to collect the genome neighbors; the user can select a smaller window (from &plusmn; 3 – &plusmn; 20 orfs) in the "Neighborhood Size" pull-down menu</p>

<p>EFI-GNT collects all genome neighbors within the specified window.  However, it will display a spoke node only if the query-neighbor co-occurrence frequency is greater than a specified value.  The default value is 20%.  A smaller value, e.g., 5%, should also be used to find neighbors that co-occur with low frequency, often as the result of phylogenetically diverse genome arrangements of functionally linked pathway enzymes.  As the co-occurrence frequency is decreased, a larger number of neighbors and Pfam families will be reported in the GNN.</p>

<p>As with EFI-EST, the user also inputs an e-mail address to which an email containing a link to the results will be sent.</p>

<p><br><img src='images/tutorial/input_figure_1.jpg' alt='Figure 1' width='600'></p>

<p><b><font color='red'>Download Page/Output</font></b></p>

<p>When the results are available (typically a few minutes, although the time required for the analysis increases with the number of query sequences in the input SSN), an e-mail with a link to the output is sent to the address provided by the user on the Start page.  The link will be active for seven days.</p>

<p><br><img src='images/tutorial/input_figure_2.jpg' alt='Figure 2' width='600'></p>

<p>The EFI-GNT output is three xgmml files and several text/spreadsheet files.</p>

<p><b>Colored SSN:</b>  The colored version of the SSN described in the previous section is available for download as an xgmml file for viewing in Cytoscape.  This SSN allows the user to quickly associated SSN cluster spoke nodes in the GNNs with clusters in the input query SSN.</p>

<p><b>Two formats of the GNN:</b>  The two formats of the GNN described in the previous section are available for download as xgmml files and viewing/analysis in Cytoscape:</p>

<p>1.  A cluster is present for each Pfam family (hub-node) that was identified as a neighbor to queries in the SSN clusters (spoke-nodes).  This format allows the user to assess whether queries in multiple SSN clusters are neighbors to members of the same Pfam family and, therefore, may have the same in vitro activities and in vivo metabolic functions.</p>

<p>2.  A cluster is present for each query SSN cluster (hub-node) that was used to identify genome neighbors (spoke-nodes).   This format allows the user to identify functionally linked enzymes, as deduced from genome proximity, that constitute the metabolic pathway in which the sequences in the query SSN cluster participate.</p>

<p><b>Text/Spreadsheet files:</b>  Additional files are available to allow the user to perform additional analyses.  At present these are:</p>

<p>1.  Text file with list of query accession IDs not found in the bacterial and fungal ENA files (nomatch.tab), i.e., not in the STD (annotated assembled sequences), CON (high level constructed sequences), and WGS (whole genome shotgun sequencing with intermediate level of assembly) files for bacterial and fungal proteins.</p>

<p>This can be used to generate custom node attribute to identify sequences with no matches in the query SSN.</p>

<p>2.  Text file with list of query accession IDs that do not have genome neighbors (noneighb.tab), in the bacterial and fungal ENA files, i.e., the ENA files contain single orfs.<p>

<p>This can be used to generate custom node attribute to identify sequences with no neighbors in the query SSN.</p>

<p>In the near future, we will provide additional files to facilitate downstream analyses, including the mapping of neighbors to the SSNs for their Pfam families.</p>
-->



        </div>
<div>
  <div> </div>
      </div>
    </div>

    <div class="clear"></div>

	</div>
    
    
    
    
    
    
   <!-- END: class="content_wide" -->
</div>

<p style='text-align:center;'><a href='tutorial_gnn_manipulation.php'><button class="css_btn_class">Continue Tutorial</button></a></p>

<?php require_once('includes/tutorial_footer.inc.php'); ?>
