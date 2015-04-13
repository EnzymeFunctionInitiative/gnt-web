<?php
require_once 'includes/tutorial_header.inc.php'; ?>

 <div class="content_nav">
	<?php require_once('includes/tutorial_nav.php'); ?>
  
  </div>
  <div class="content_content">

<p><br><b>Introduction to EFI-Genome Neighborhood Tool</b></p>
<p><br>The EFI has developed an easy to use program that enables a user to
retrieve, display, and interact with genome neighborhood information
for large datasets of pre-organized protein sequences. These sequence
datasets are generally Pfam or InterPro families, or BLAST results.
The web-based EFI-Genome Neighborhood Tool (GNT) is a companion to
the EFI-Enzyme Similarity Tool (EST) and accepts a Sequence
Similarity Network (SSN) as input. The program generates a Genome
Neighborhood Network (GNN) file and a colored version of the input
SSN. The process is extremely efficient and retrieves information
(Pfam family, distance from query, etc.) for up to 20 “neighbor”
protein sequences per query sequence and renders a network file, that
can be imported and viewed in Cytoscape, in less than a minute for
most SSN sizes. <i>The only constraint applied to the retrieved
neighborhood information is the distance from the query gene.</i>
Neighborhood information is then organized based on Pfam family
classification thereby allowing one to establish functional
relationships between Pfam families and SSN data. 
</p>
<p><br>Many existing genome neighborhood tools compare gene neighborhoods among
multiple prokaryotic genomes in order to infer phylogenetic
relationships. EFI-GNT is different in that it allows the comparison
of genome neighborhoods between clusters of similar protein sequences
in order to facilitate the assignment of function within protein
families and superfamilies. 
</p>
<p><br>If you are new to this tool, it is highly recommended that you continue
reading the subsequent tutorial pages. If you are ready to generate a
GNN, follow the “Begin EFI-GNT” link to the input upload page.</p>

</div>
<div>
  <div> </div>
      </div>
    </div>

    <div class="clear"></div>

	</div>
    
    
    
    
    
    
   <!-- END: class="content_wide" -->
</div>
<p style='text-align:center;'><a href='tutorial.php'><button class="css_btn_class">Continue Tutorial</button></a></p>
  <div class="clear"></div>

<?php require_once('includes/tutorial_footer.inc.php'); ?>
