<?php
require_once 'includes/tutorial_header.inc.php'; ?>

 <div class="content_nav">
	<?php require_once('includes/tutorial_nav.php'); ?>
  
  </div>
  <div class="content_content">

<p><br><b>Limitations and Caveats</b></p>
<p><br>The utility of the GNN is limited primarily by its signal-to-noise;
the noise being genes that are proximal to your query but irrelevant
to its function <i>in vivo.</i> GNN noise can be detrimental to the
functional discovery process by providing misleading genomic
contexts. Thus, the GNN user should have a critical eye for gene
neighbor consistency. Neighbor genes that occur more often and in
closer proximity are less likely to be "noise". 
</p>
<p><br>The consolidation of an SSN-cluster into a single representative
spoke node necessitates that the degree of SSN fractionation be
optimized (ideally) prior to GNN generation. Thus, the more confident
the user is in the SSN alignment score cutoff, the more useful they will find
the GNN information. An iterative approach likely will yield the most
useful information. Please see the <a href="http://efi.igb.illinois.edu/efi-est/tutorial.php">EFI-EST
tutorial</a> for information regarding the selection of a
satisfactory alignment score cutoff.</p>
<p><br>Additionally, there are several reasons why a query sequence may return less than
the user-designated number of neighbors. Some of these caveats will
be addressed in future iterations of EFI-GNT, while others require
solutions beyond the scope of this work or are biological in nature.</p>
<ol>
	<li>The query sequence does not match to the current ENA sub-databases.
	Currently, all prokaryotic sub-databases are queried – additional
	sub-databases (e.g., those from environmental samples) will be added
	in the future.</li>
	<li>One or more of the ENA entries proximal to the query are non-coding
	RNA.</li>
	<li>The query is located near the beginning or end of the ENA file,
	e.g., the query sequence is the third entry in the file, thus even
	if +/-3 or more is designated only the -1 and -2 genes will be
	returned. The current database files are split into many records,
	and currently the program retrieves neighbors from a single record
	(i.e., it does not search the record before or after the record in
	which the query is located, despite the fact that contiguous records
	may (however may not) contain information from a contiguous portion
	of the genome).</li>
	<li>The neighbor entry <u>does not</u> have an associated EMBL accession
	number and thus cannot be used to compare to the Pfam database.</li>
	<li>The neighbor entry <u>does</u> have an associated EMBL accession
	number, but that sequence has not been incorporated into a current
	Pfam family.</li>
</ol>
<p><br>Finally, gene neighbor annotations depend predominantly on HMM-based
assignment to a Pfam family, which is a highly automated process.
Accordingly, neighbor genes of interest should be investigated
rigorously via orthogonal bioinformatics methods in order to confirm
function. For instance, one next step is to generate an SSN for a few
of the most commonly occurring Pfam families in the genome
neighborhood of the query of interest. Doing so would help to
determine whether or not the neighbor clusters with protein sequences
whose substrates are known and/or how diverse the Pfam family is on
the whole. Knowing to what Pfam family a neighbor belongs will inform
the user as to the class of the enzyme, and may indicate a specific
substrate, but further exploiting the use of SSNs can provide family
or superfamily context to the function of the neighbor beyond that of
BLASTing the sequence against the publically available databases, not
to mention the additional benefit of being able to interact with the
data in Cytoscape. <u>In general, the GNN produced by EFI-GNT provides a
wealth of information, and this information can be leveraged best
through trial and error.</u></p>

<p><br>We are developing enhancements to EFI-GNT that will provide statistical analyses so that user will be able to more easily identify the most abundant Pfam families identified by each query cluster.  This analysis quickly allows the signal to be segregated from the noise.  When implemented, these analyses will be available for download on the EFI-GNT results page.</p>

        </div>
<div>
  <div> </div>
      </div>
    </div>

    <div class="clear"></div>

	</div>
    
    
    
    
    
    
   <!-- END: class="content_wide" -->
</div>
<p style='text-align:center;'><a href='tutorial_input.php'><button class="css_btn_class">Continue Tutorial</button></a></p>

<?php require_once('includes/tutorial_footer.inc.php'); ?>
