<?php
require_once 'includes/main.inc.php';
require_once '../libs/settings.class.inc.php';
require_once 'includes/tutorial_header.inc.php';
?>

 <div class="content_nav">
	<?php require_once('includes/tutorial_nav.php'); ?>
  
  </div>
  <div class="content_content">

<h3>Advice, Limitations, and Caveats</h3>

<h4>Deducing functions of neighbors: mapping of genome neighbors to their family SSNs</h4>

<p>
The assignment of genome neighbors of SSN query sequences to Pfam families 
should restrict their possible in vitro activities and in vivo metabolic 
functions. Approximately 80% of the 16,712 families in Pfam 31.0 have "curated" 
functions. The user can use the Pfam curated functions "broadly" to predict 
possible functions—many proteins are members of specificity diverse 
superfamilies (same reaction mechanism, different substrate specificity) or 
functionally diverse superfamilies (conserved partial reactions, different 
substrate specificities).
</p>

<p>
When the SSN for the neighbor Pfam family is colored using the SSN query 
cluster color, the user easily can locate the nodes associated with the 
neighbors identified in the GNN. If these are co-located in the SSN, this 
provides evidence that these have the same function and are reasonable 
candidates for partners functionally linked to the queries in a metabolic 
pathway.
</p>

<p>
The query-neighbor distance node attribute provides additional information 
about functional linkage: small distances suggest functional linkage; long 
distances suggest the lack of functional linkage. This analysis is especially 
useful when neighbors are members of large Pfam families, e.g., PF00106, short 
chain dehydrogenases, because these can occur twice in the same 
neighborhood—one close to the query that is functionally linked and one remote 
that occurs "by chance". 
</p>

<p>
Finally, when the nodes in the neighbor Pfam family SSN that have SwissProt 
annotations are identified (<b>UniProt Annotation Status</b> node attribute), the user 
can determine whether the query’s genome neighbors have these characterized 
functions (in clusters with SwissProt-annotated sequences) or novel functions 
(no SwissProt-annotated sequences, i.e., TrEMBL sequences).
</p>

<h4>Signal to noise: "Optimization" of co-occurrence frequency and proximity</h4>

<p>
The utility of a GNN is limited by its signal-to-noise ratio, i.e., proteins 
encoded by genes proximal to that encoding the query but irrelevant to the 
query’s function (role in a metabolic pathway). This "noise" can be detrimental 
to the functional discovery process by providing misleading genomic contexts. 
Thus, at least initially, the user should focus on those proteins encoded by 
genome neighbors that co-occur most frequently with the query and/or are in 
closer proximity to the query because these are less likely to be "noise".
</p>

<p>
As described in the previous section, the node attributes in both 
representations of the GNN provide information about the co-occurrence 
frequency of the query and neighbors (<b>Co-occurrence</b> and <b>Co-occurrence Ratio</b>) as 
well as the median (<b>Median Distance</b>) and mean (<b>Average Distance</b>) distances 
separating the genes encoding the query and neighbors.
</p>

<p>
The GNN scripts use a default window of &plusmn; 10 orfs from the query to collect the 
neighbors and a minimum co-occurrence frequency of 20% (the value of the 
<b>Co-occurrence</b> node attribute) to include the neighbors in the GNN. The user can 
either 1) select different values for the windows and co-occurrence frequencies 
and/or 2) filter the default GNN using the Select Control Panel to generate a 
"daughter" GNN that includes only the results for a smaller mean or median 
query-neighbor distance and/or larger co-occurrence frequency to focus on the 
most probable components of a metabolic pathway involving the query.
</p>

<h4>"No Pfam" Neighbors</h4>

<p>
EFI-GNT associates neighbors with Pfam families using assignments from the 
UniProt/InterPro databases and displays the results. Approximately 80% of the 
proteins in the UniProt database are assigned to a Pfam family, so pathway 
components may be located in the "none" Pfam hub-node in the Pfam hub-node 
representation of the GNN or in the "none" Pfam spoke-nodes in the cluster 
hub-node representation of the GNN. The node attributes for the sequences in 
the "none" nodes contain information about query-neighbor distance and 
co-occurrence frequencies that the user may find useful in evaluating the 
importance of "no Pfam" neighbors as components of metabolic pathways.
</p>

<p>
If "no Pfam" neighbors appear to be important for predicting a pathway, the 
user is advised to 1) generate the SSN for the "no Pfam" neighbors to assess 
whether neighbors are located in the same cluster/family using Option D and the 
list of accession IDs that is provided in the downloads and/or 2) use Option A 
of the EFI-EST web tool to collect the homologues of a "no Pfam" neighbor and 
generate SSNs so that "new" families of proteins might be identified for 
functional assignment. The node attributes for these SSNs will include InterPro 
family/domain assignments that may have annotation information from sources 
other than Pfam, so these SSNs and their node attributes may be useful in 
deducing the function of a "no Pfam" neighbor.
</p>

<h4>"Over-fractionation" of query SSNs</h4>

<p>
The GNNs are presented in the Pfam hub-node/cluster spoke-node formats to 
facilitate an evaluation of whether the input SSN is appropriately segregated 
into "isofunctional" clusters. Indeed, this segregation is the most difficult 
but, arguably, the most important step in using GNNs to facilitate the 
identification of metabolic pathways.
</p>

<p>
<b>The SSN cluster hub-node/Pfam spoke-node format</b> is intended to allow the user 
to quickly identify the likely components of pathways, with subsequent 
synergistic evaluation of the co-occurrence frequencies (<b>Co-occurrence</b> and 
<b>Co-occurrence Ratio</b> node attributes) and query-neighbor distances (<b>Average 
Distance</b> and <b>Median Distance</b> node attributes) allowing the user to choose the 
most likely enzyme families that contribute the components of the metabolic 
pathway in which the query participates.
</p>

<p>
<b>The Pfam hub-node/SSN cluster spoke-node format</b> is intended to help the user 
decide whether the input SSN is over-fractionated, i.e., orthologues (enzymes 
with the same in vitro activity and the same role in the same in vivo pathway) 
are located in multiple SSN clusters. This may occur as the result of 
phylogenetic divergence of sequence (retrospectively assessed using the 
phylogeny node attributes in the input SSN). If/when this occurs, multiple 
cluster spoke-nodes are present for Pfam hub-nodes—the user may find that using 
an input SSN generated with a smaller alignment score (lower sequence identity) 
may reduce/eliminate the over-fractionation by merging orthologues into a 
single SSN cluster (or smaller number of SSN clusters).
</p>

<p>
An iterative approach of using the GNN in the Pfam hub-node/cluster spoke-node 
formats to guide the selection of the alignment score for the input SSN likely 
will yield the most useful information. Please refer to the
<a href="<?php echo settings::get_est_url(); ?>">EFI-EST tutorial</a>
for information regarding the selection of a satisfactory alignment score 
threshold.
</p>

<h4>Less-than-expected number of neighbors</h4>

<p>
A query sequence may return less than the maximum number of neighbors (e.g., 20 
for the default &plusmn; 10 orfs, 2N for user-specified &plusmn; N orfs):
</p>

<p>
1. Genome context is obtained from nucleic acid sequence files from the ENA. 
Functionally relevant genome context can be obtained for prokaryotic (PRO) and 
fungal (FUN) genomes as well as environmental (ENV) metagenome projects. Three 
types of ENA files are used in decreasing order of annotation/assembly: STD, 
standard annotated assembled sequences (complete genomes); CON, high level 
constructed genome sequences; and WGS, whole genome shotgun sequences. Queries 
in the input SSN that do not have matches in these files will not return 
neighbors; for example, mammalian and plant proteins will not have matches in 
the PRO and FUN ENA files and, therefore, will have no genome neighbors. The 
UniProt accession IDs for these queries are included in the nomatch file that 
can be downloaded; these are also identified in the colored SSN with a node 
attribute.
</p>

<p>
2. For some queries, the ENA file includes only the sequence for the gene and 
no flanking sequences. Therefore, these queries also will have no genome 
neighbors. The UniProt accession IDs for these queries are included in the 
noneigh file that can be downloaded; these are also identified in the colored 
SSN with a node attribute.
</p>

<p>
3. For neighbors identified in STD ENA files (complete genomes), the query may 
be located near the beginning or end of the linear sequence of proteins in the 
file. In some cases, the chromosome may be linear; in other cases, the 
chromosome may be circular. For the former, it is impossible to collect the 
maximum number of neighbors in one direction from the query. For the latter, 
our neighbor identification algorithm gathers neighbors from the opposite end 
of the ENA file.
</p>

<p>
4. For neighbors identified in CON and WGS ENA (contigs of intact chromosomes), 
the queries again may be located near the beginning or end of the linear 
sequence, so it may not be possible to collect the maximum number of neighbors 
in one or both directions from query (depending on the length of the sequence).
</p>

<p>
5. One or more of the neighbors may be non-coding RNA. The presence of these 
RNAs is not reported.
</p>







<!--
<p><br><b><font color='red'>Advice, Limitations, and Caveats</font></b></p>

<p><b>Deducing functions of neighbors:  mapping of genome neighbors to their family SSNs</b></p>

<p>The assignment of genome neighbors of SSN query sequences to Pfam families should restrict their possible in vitro activities and in vivo metabolic functions.  Approximately 80% of the 16,306 families in Pfam 30.0 have "curated" functions; the remaining 20% are Domains (or proteins) of Unknown Function (DUFs).  The user can use the Pfam curated functions "broadly" to predict possible functions—many proteins are members of specificity diverse superfamilies (same reaction mechanism, different substrate specificity) or functionally diverse superfamilies (conserved partial reactions, different substrate specificities).</p>


<p>When the SSN for the neighbor Pfam family is colored using the SSN query cluster color, the user easily can locate the nodes associated with the neighbors identified in the GNN.  If these are co-located in the SSN, this provides evidence that these have the same function and are reasonable candidates for partners functionally linked to the queries in a metabolic pathway.</p>

<p>When the SSN for the neighbor Pfam family is colored using the SSN query cluster color, the user easily can locate the nodes associated with the neighbors identified in the GNN.  If these are co-located in the SSN, this provides evidence that these have the same function and are reasonable candidates for partners functionally linked to the queries in a metabolic pathway.</p>

<p>The query-neighbor distance node attribute provides additional information about functional linkage:  small distances suggest functional linkage; long distances suggest the lack of functional linkage.  This analysis is especially useful when neighbors are members of large Pfam families, e.g., PF00106, short chain dehydrogenases.  Also, for large neighbor Pfam families, a query may identify multiple members of the family; the neighbor with the shorter distance is more likely to be functionally linked.</p>

<p>Finally, when the nodes in the neighbor Pfam family SSN that have SwissProt annotations are identified (<b>Status</b> node attribute), the user can determine whether the query’s genome neighbors have these characterized functions (in clusters with SwissProt-annotated sequences) or novel functions (no SwissProt-annotated sequences).</p>

<p><b>Signal to noise:  "Optimization" of co-occurrence frequency and proximity</b></p>

<p>The utility of a GNN is limited by its signal-to-noise ratio, i.e., proteins encoded by genes proximal to that encoding the query but irrelevant to the query’s function (role in a metabolic pathway).  This "noise" can be detrimental to the functional discovery process by providing misleading genomic contexts. Thus, at least initially, the user should focus on those proteins encoded by genome neighbors that co-occur most frequently with the query and/or are in closer proximity to the query because these are less likely to be "noise".</p>

<p>As described in the previous section, the node attributes in both representations of the GNN provide information about the co-occurrence frequency of the query and neighbors (<b>Cluster Fraction</b> and <b>Num Ratio</b>) as well as the median (<b>Median Distance</b>) and mean (<b>Average Distance</b>) distances separating the genes encoding the query and neighbors.</p>

<p>The GNN scripts use a default window of &plusmn; 10 orfs from the query to collect the neighbors and a minimum co-occurrence frequency of 20% (the value of the <b>Co-occurrence</b> node attribute) to include the neighbors in the GNN.  The user can either 1) select different values for the windows and co-occurrence frequencies and/or 2) filter the default GNN using the Select Control Panel to generate a "daughter" GNN that includes only the results for a smaller mean or median query-neighbor distance and/or larger co-occurrence frequency to focus on the most probable components of a metabolic pathway involving the query.</p>

<p><b>"No Pfam" Neighbors</b></p>

<p>EFI-GNT associates neighbors with Pfam families using assignments from the UniProt/InterPro databases and displays the results.  Approximately 80% of the proteins in the UniProt database are assigned to a Pfam family, so pathway components may be located in the "none" Pfam hub-node in the Pfam hub-node representation of the GNN or in the "none" Pfam spoke-nodes in the cluster hub-node representation of the GNN.   The node attributes for the sequences in the "none" nodes contain information about query-neighbor distance and co-occurrence frequencies that the user may find useful in evaluating the importance of "no Pfam" neighbors as components of metabolic pathways.</p>

<p>If "no Pfam" neighbors appear to be important for predicting a pathway, the user is advised to 1) generate the SSN for the "no Pfam" neighbors to assess whether neighbors are located in the same cluster/family and/or 2) use Option A of the EFI-EST web tool to collect the homologues of a "no Pfam" neighbor and generate SSNs so that "new" families of proteins might be identified for functional assignment.   The node attributes for these SSNs will include InterPro family/domain assignments that may have annotation information from sources other than Pfam, so these SSNs and their node attributes may be useful in deducing the function of a "no Pfam" neighbor.</p>

<p><b>"Over-fractionation" of query SSNs</b></p>

<p>The GNNs are presented in the Pfam hub-node/cluster spoke-node formats to facilitate an evaluation of whether the input SSN is appropriately segregated into "isofunctional" clusters.  Indeed, this segregation is the most difficult but, arguably, the most important step in using GNNs to facilitate the identification of metabolic pathways.</p>

<p><b>The Pfam hub-node/cluster spoke-node format</b> is intended to help the user decide whether the input SSN is over-fractionated, i.e., orthologues (enzymes with the same in viro activity and the same role in the same in vivo pathway) are located in multiple SSN clusters.  This may occur as the result of phylogenetic divergence of sequence (retrospectively assessed using the phylogeny node attributes in the input SSN).  If/when this occurs, multiple cluster spoke-nodes are present for Pfam hub-nodes—the user may find that using an input SSN generated with a smaller alignment score (lower sequence identity) may reduce/eliminate the over-fractionation by merging orthologues into a single SSN cluster (or smaller number of SSN clusters).</p>

<p><b>The cluster hub-node/Pfam spoke-node format</b> is intended to allow the user to quickly identify the likely components of pathways, with subsequent synergistic evaluation of the co-occurrence frequencies (<b>Co-occurrence and Co-occurrence Ratio</b> node attribute) and query-neighbor distances (<b>Average Distance</b> and <b>Median Distance</b> node attributes) allowing the user to choose the most likely enzyme families that contribute the components of the metabolic pathway in which the query participates.</p>

<p>An iterative approach of using the GNN in the Pfam hub-node/cluster spoke-node formats to guide the selection of the alignment score for the input SSN likely will yield the most useful information. Please refer to the <a href='http://efi.igb.illinois.edu/efi-est/tutorial.php'>EFI-EST tutorial</a> for information regarding the selection of a satisfactory alignment score threshold.</p>

<p><b>Less-than-expected number of neighbors</b></p>

<p>A query sequence may return less than the maximum number of neighbors (e.g., 20 for the default &plusmn; 10 orfs, 2N for user-specified &plusmn; N orfs):</p>

	<p>1.	Genome context is obtained from nucleic acid sequence files from the ENA.  Functionally relevant genome context can be obtained for prokaryotic (PRO) and fungal (FUN) genomes.  Three types of ENA files are used in decreasing order of annotation/assembly:  STD, standard annotated assembled sequences (complete genomes); CON, high level constructed genome sequences; and WGS, whole genome shotgun sequences.  
Queries in the input SSN that do not have matches in these files will not return neighbors; for example, mammalian and plant proteins will not have matches in the PRO and FUN ENA files and, therefore, will have no genome neighbors.  The UniProt accession IDs for these queries are included in the nomatch file that can be downloaded.</p>

<p>2.	For some queries, the ENA file includes only the sequence for the gene and no flanking sequences.  Therefore, these queries also will have no genome neighbors.  The UniProt accession IDs for these queries are included in the noneigh file that can be downloaded.</p>

<p>3.	For neighbors identified in STD ENA files (complete genomes), the query may be located near the beginning or end of the linear sequence of proteins in the file.  In some cases, the chromosome may be linear; in other cases, the chromosome may be circular.  For the former, it is impossible to collect the maximum number of neighbors in one direction from the query.  For the latter, we have not yet "corrected" our neighbor identification algorithm to gather neighbors from the opposite end of the ENA file; we plan to correct this infrequently occurring "glitch" as soon as time permits.</p>

<p>4.	For neighbors identified in CON and WGS ENA (contigs of intact chromosomes), the queries again may be located near the beginning or end of the linear sequence, so it may not be possible to collect the maximum number of neighbors in one or both directions from query (depending on the length of the sequence).</p>

<p>5.	One or more of the neighbors may be non-coding RNA.  The presence of these RNAs is not reported.</p>

<p><b>Multiple ENA files for the same query UniProt accession ID</b></p>

<p>Although one might expect that each UniProt accession ID would be unique, i.e., associated with a specific protein in a specific organism, this is not always the case. UniProt reduced redundancy in April 2015 by archiving (in UniParc) the proteomes of closely related/redundant strains and including only the proteomes of "unique" strains in UniProt; however, we have encountered examples of a UniProt accession ID being associated with multiple ENA files, each associated with the genome sequence of a different strain.  In these "rare" cases, EFI-GNT uses only the first ENA genome sequence file in the UniProt "flat file" to identify neighbors.  The genome sequence in this file is not necessarily the longest sequence file for the closely related strains, so a situation may occur in which the UniProt ID for a query may not retrieve the maximum number of neighbors.  As time permits, we will correct this problem.</p>
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

<p style='text-align:center;'><a href='tutorial_example.php'><button class="css_btn_class">Continue Tutorial</button></a></p>

<?php require_once('includes/tutorial_footer.inc.php'); ?>
