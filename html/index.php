<?php
require_once 'includes/tutorial_header.inc.php'; ?>


<p></p>
<p>
The EFI-Genome Neighborhood Tool (EFI-GNT) allows the exploration of the physical association of genes on genomes, i.e. 
gene clustering. EFI-GNT enables a user to retrieve, display, and interact with genome neighborhood information for 
large datasets of sequences.
</p>
<p style='text-align:center;'><a href='stepa.php'><button class="css_btn_class_recalc">Begin EFI-GNT</button></a></p>

<h3>Tutorial</h3>
<h4>EFI-Genome Neighborhood Tool Overview</h4>

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

<h4>EFI-GNT acceptable input</h4>

<p>
The sequence datasets are generated from an SSN produced by the EFI-Enzyme Similarity Tool (EFI-EST). Acceptable 
SSNs are generated for an entire Pfam and/or InterPro protein family (from Option B of EFI-EST), a focused region of a 
family (from Option A of EFI-EST), a set of protein sequence that can be identified from FASTA headers (from option C of 
EFI-EST with header reading) or a list of recognizable UniProt and/or NCBI IDs (from option D of EFI-EST). A manually 
modified SSN within Cytoscape that originated from any of the EST options is also acceptable. SSNs that have been 
colored using the "Color SSN Utility" of EFI-EST and that originated from any of acceptable Options are also acceptable.
</p>

<h4>Principle of GNT analysis</h4>

<p>
Protein encoding genes that are neighbors of input queries (within a defined window on either side) are collected from 
sequence files for bacterial (prokaryotic and archaeal) and fungal genomes in the European Nucleotide Archive (ENA) 
database. The co-occurrence frequencies of the identified neighboring sequences with the input queries are calculated as 
well as the absolute values of the distances in open reading frames (orfs) between the queries and neighbors. The 
calculated information is provided as Genome Neighborhood Networks (GNNs), in addition to a colored version of the input 
SSN that aids analysis of the GNNs.
</p>

<h4>EFI-GNT output</h4>

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













<!--
<p>The EFI-Genome Neighborhood Tool (EFI-GNT) web tool enables a user to retrieve, display, and interact with genome neighborhood information for large datasets of protein sequences, including entire protein families.  The sequence datasets are generated using the EFI-Enzyme Similarity Tool (EFI-EST) web tool and can be either for 1) an entire Pfam and/or InterPro protein family (from Option B of EFI-EST) or 2) a focused region of a family (from Option A of EFI-EST).</p>

<p>EFI-EST generates Sequence Similarity Networks (SSNs) that are visualized and analyzed using Cytoscape.  An SSN (in the .xgmml file format) segregated into potential isofunctional families (by filtering with an appropriate alignment score) is the input for EFI-GNT.  The genome neighborhood proteins within an orf window on either side of the input queries (default &plusmn; 10 orfs; the user can change the window size) are collected from sequence files for bacterial (prokaryotic and archaeal) and fungal genomes in the European Nucleotide Archive (ENA) database.   EFI-GNT generates two formats of the Genome Neighborhood Network (GNN) as well as a colored version of the input SSN that aids analysis of the GNNs.</p>

<p>The UniProt accession IDs for the queries and the neighbors, the Pfam families for the neighbors, and both the query-neighbor distances (in orfs) and co-occurrence frequencies are provided in the GNNs.  The GNNs and colored SSN are downloaded, visualized, and analyzed using Cytoscape.</p>

<p>The user can filter the GNNs for a range of query-neighbor distances and/or co-occurrence frequencies to enable the identification of functionally related proteins/enzymes, with shorter distances and great co-occurrence frequencies suggesting functional linkage in a metabolic pathway.  With the identities of the Pfam families for the neighbors, the user may be able to infer the in vitro enzymatic activities of the queries and neighbors and predict the reactions in the metabolic pathway in which they participate.</p>
<p><img src='images/tutorial/intro_figure_1.jpg'></p>
<p><i>Figure 1:</i> Examples of colored SSN (left) and a hub-and-spoke cluster from a GNN (right).</p>
<p>Although other tools may allow comparison gene neighborhoods among multiple prokaryotic genomes to allow inference of phylogenetic relationships, e.g., IMG (<a href='https://img.jgi.doe.gov'>https://img.jgi.doe.gov</a> - EFI-GNT enables comparison of the genome neighborhoods for clusters of similar protein sequences in order to facilitate the assignment of function within protein families and superfamilies.</p>

<p><b>If you are new to this tool</b>, we recommend that you first read the tutorial sections.</p>
<p><b>When you are ready to generate a GNN</b>, follow the "Begin EFI-GNT" link at the bottom of the page to upload the xgmml file for your SSN.</p>
-->
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
