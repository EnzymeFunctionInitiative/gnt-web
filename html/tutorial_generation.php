<?php
require_once 'includes/tutorial_header.inc.php'; ?>

 <div class="content_nav">
	<?php require_once('includes/tutorial_nav.php'); ?>
  
  </div>
  <div class="content_content">

<p><br><b>GNN Generation Process</b></p>

<p>A genome neighborhood network (GNN) is generated in three steps.</p>

<p>1.  A sequence similarity network (SSN) from Option A or option B of <a href='http://efi.igb.illinois.edu/efi-est'>EFI-EST</a> (or a file from Option A or Option B of EFI-EST modified/edited by Cytoscape) is partitioned into “isofunctional” clusters with an appropriate alignment score (to separate the SSN into isofunctional clusters) using either the Analyze step of EFI-EST or by filtering with Cytoscape.  The xgmml file for the SSN is the input for EFI-GNT and is uploaded by the user on the Start page.</p>

<p>EFI-GNT recognizes the clusters in the SSN and extracts the UniProt accession IDs for the sequences in each cluster.   Each cluster is assigned a unique cluster number, and the nodes for the sequences in each cluster are assigned a unique color.  Singletons in the SSN are excluded from the analysis, although they will be present in the colored SSN that is provided by EFI-GNT.</p>

<p>EFI-GNT provides a numbered and colored version of the SSN (as an xgmml file) to assist the user in analyzing the GNNs.</p>
<p>2.  After the members of each cluster are identified, EFI-GNT queries the STD (annotated assembled sequences), CON (high level constructed sequences), and WGS (whole genome shotgun sequencing with intermediate level of assembly) sequence files for bacterial (prokaryotic and archaeal) and fungal proteins in the European Nucleotide Archive (ENA) database for the genome neighbors for each sequence in the cluster.</p>

<p>The default window for identifying neighbors is ± 10 orfs from the query sequence—the user can select a smaller/larger window on the Start page (from ±3 to ± 20 orfs).  As the size of the window decreases, the signal-to-noise in the GNN increases, although smaller windows may miss functionally linked neighbors.</p>
<p><img src='new_images/generation_figure_1.jpg' width='600'></p>

<p>The 20 neighbors collected with the ± 10 orf default (or 2N neighbors collected with a user-specified ±N orf window) constitute the genome neighborhood for the query.  If a neighbor is an RNA (rRNA or tRNA), it is discarded although its “place” is included in the ± 10 orf count.  Each of the protein neighbors is then associated with a Pfam family using annotations provided by the UniProt and InterPro databases.</p>

<p>For each query-neighbor pair, the EFI-GNT collects the distance (in orfs), genome start/stop coordinates for the query and neighbor, and the direction of transcription for the query and neighbor (complement/noncomplement).</p>

<p><b>Multidomain proteins:</b>  If a neighbor is a multidomain protein, i.e., containing multiple domains defined by Pfam, EFI-GNT reports that the neighbor is multidomain by providing a hyphenated list of the Pfam family names for the domains as the name of the GNN node (hub or spoke) for the neighbor Pfam family; EFI-GNT also provides hyphenated lists of all of the Pfam family names and numbers as node attributes for the Pfam family node (<b>name</b>, <b>shared name</b>, <b>Pfam</b>, and <b>Pfam description</b>).</p>


<p><b>Neighbors not in Pfam:</b>  If a neighbor is not associated with a Pfam family (~20% of the proteins in UniProt are not assigned to a Pfam family), it is assigned to the “no Pfam” family.  The “no Pfam” family is included in the GNNs (as clusters labeled "none").  A file containing the UniProt IDs of the “no Pfam” neighbors is available for download so that a SSN can be generated using Option D of EFI-EST, thereby allowing these to be placed into families that have not (yet) been curated by Pfam.</p>

<p><b>Neighbors not in ENA files:</b>  Bacterial (prokaryotes and archaea) and fungal genomes often are organized in operons and/or gene clusters that encode pathways, so these are mined for genome neighborhoods.  Because EFI-GNT queries ENA files for only bacteria and fungi, some queries in the input SSN, e.g., encoded by plant and mammalian genomes, will not find matches in these files.  In addition, because of the nature of the release schedule of the UniProt protein sequence files and ENA nucleotide sequence files, some bacterial and fungal entries in UniProt may not have entries in the ENA database used by EFI-GNT so no matches will be found.</p>

<p>A file containing the UniProt IDs for queries with no matches in the ENA files is available for download.</p>

<p><b>Queries with no or incomplete genome context:</b>  Not all sequences in the query SSN will identify 20 neighbors with the ± 10 orf default window, excluding the RNAs, (or 2N neighbors with a user-specified ±N orf window).  Depending on the organism and/or the type of sequencing project that contributed the ENA file in which the query sequence is located, a smaller number of neighbors, sometimes no neighbors, may be found if the query is close to/at the end of a contig or linear chromosome.</p>

<p>A file containing the UniProt IDs for queries with no neighbors in the ENA files is available for download.</p>

<p>3.  Several files are generated for download, including the colored version of the input SSN (details in the next section), the two formats of the GNN (details in the next section), and various text/spreadsheet files that can be used for multiple sequence alignments and custom node attributes for analyses of the neighbors in the SSNs for their families.</p>


</div>
<div>
  <div> </div>
      </div>
    </div>

    <div class="clear"></div>

	</div>
    
    
    
    
    
    
   <!-- END: class="content_wide" -->
</div>
<p style='text-align:center;'><a href='tutorial_attributes.php'><button class="css_btn_class">Continue Tutorial</button></a></p>


<?php require_once('includes/tutorial_footer.inc.php'); ?>
