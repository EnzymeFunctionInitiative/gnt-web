<?php
require_once 'includes/tutorial_header.inc.php'; ?>

 <div class="content_nav">
	<?php require_once('includes/tutorial_nav.php'); ?>
  
  </div>
  <div class="content_content">

<p><br><b>What is a Genome Neighborhood Network?</b></p>

<p><br>While sequence homology alone is capable of indicating protein
function in some cases, the combination of sequence homology and
genome neighborhood analysis increases the confidence of these
predictions and expands functional discovery to highly divergent
proteins. Genome neighborhood analysis can shed light on the function
of an unknown protein due to the way bacteria organize the genes
within their genome, for example in the simple case described below.</p>

<p><br>In order to reduce resources consumed in the turning on and off of
gene transcription, bacterial genes often are organized into operons.
A single operon may contain several genes under the transcriptional
regulation of a single promoter. These genes are often related in
that their gene products, often enzymes, form a biochemical pathway.
For example, the product of Enzyme A is then the substrate for Enzyme
B, which produces yet another molecule that is acted on by Enzyme C.
These pathways are most often metabolic in nature. If the functions
of Enzyme A and Enzyme C are known, but the function of Enzyme B is
unknown – the knowledge that Enzyme A and C are sequentially
located within the genome gives insight into the possible function of
Enzyme B. Enzyme B most likely executes a chemical reaction that
bridges the metabolites produced and consumed by Enzyme A and C,
respectively. 
</p>
<p><img src='images/Tutorial_Figure1.jpeg' alt='Figure 1' width="580"></p>
<p><i>Figure 1.</i> Genome organization is influenced by gene products that form a
metabolic pathway.</p>
<p><br>Sometimes genes that encode the proteins in biochemical pathways are organized in neighboring clusters of two or more transcriptional units that are controlled by the same regulator.  Their gene products may be similarly analyzed to deduce biochemical pathways and the functions of unknown proteins.</p>

<p><br>Using the family information in Sequence Similarity Networks (SSNs) as input, the Genome Neighborhood Network (GNN) organizes the proteins encoded by the genome neighborhoods for each query sequence according to Pfam family. Unlike traditional genome neighborhood analysis, which can be extremely time-consuming when conducted on more than a handful of genes at a time, the GNN acquires and organizes genome neighborhood information for thousands of query genes in a high throughput and rapid fashion. The resulting network allows a user to quickly identify the protein families (using Pfam-defined homology-based classifications) that are encoded by the genes within close proximity to genes that encode the proteins in the SSN dataset. From this GNN network, one can distinguish between commonly occurring protein classes and rarely occurring protein classes, as well as protein classes that are shared among SSN-clusters. One can also filter this network to examine only the neighbors of specific clusters from the original SSN, in order to quickly assign function to clusters of un-annotated protein sequences.</p>        

</div>
    </div>

    <div class="clear"></div>

	</div>
    
    
    
    
    
    
   <!-- END: class="content_wide" -->
</div>
<p style='text-align:center;'><a href='tutorial_generation.php'><button class="css_btn_class">Continue Tutorial</button></a></p>
  <div class="clear"></div>

<?php require_once('includes/tutorial_footer.inc.php'); ?>
