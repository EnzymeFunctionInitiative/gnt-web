<?php
require_once 'includes/tutorial_header.inc.php'; ?>

 <div class="content_nav">
	<?php require_once('includes/tutorial_nav.php'); ?>
  
  </div>
  <div class="content_content">

<p><br><b>GNN Generation Process</b></p>
<p><br>The process of generating a GNN occurs in three general steps. The
input is a sequence similarity network (SSN) already partitioned into
clusters based on sequence similarity homology at a previously
indicated e-value cutoff. In the first step, the network file is
parsed for node and edge information. All clusters and their
corresponding sequence accession numbers, with the exception of
singletons which are excluded from analysis, are assigned a number and an unique color.</p>

<p><br>In the second step, after “cluster membership” information is
gathered, the European Nucleotide Archive (<a href="http://www.ebi.ac.uk/ena/">ENA</a>)
is queried using the UniProt accession number of each protein
sequence in the SSN. Upon locating the UniProt accession number of a query
sequence in the ENA files, the information for up to 10 entries
proceeding and succeeding the query in the genome is collected. These
20 entries represent the genome neighborhood. Each “neighbor”
entry with a protein-encoding gene (rRNAs and tRNAs are discarded but
still included in the +/- 10 count) is compared to the <a href="http://pfam.sanger.ac.uk/help">Pfam-A</a>
database to determine their Pfam family membership. Sequences that do
not match to any Pfam-A family are discarded. Concurrently, neighbor
accession numbers are used to query additional databases in order to
populate the node attributes with information that is useful to
functional discovery. The output is a report that details the
relationship between all query sequences and the newly retrieved
neighborhood information. 
</p>
<p><br>The third and final step entails writing the GNN network file and
coloring the original SSN network. The entire process is extremely
fast and computationally inexpensive enough to be carried out on the
same machine that hosts the Web server.</p>
        
</div>
<div>
  <div> </div>
      </div>
    </div>

    <div class="clear"></div>

	</div>
    
    
    
    
    
    
   <!-- END: class="content_wide" -->
</div>
<p style='text-align:center;'><a href='tutorial_format.php'><button class="css_btn_class">Continue Tutorial</button></a></p>


<?php require_once('includes/tutorial_footer.inc.php'); ?>
