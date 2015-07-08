<?php
require_once 'includes/tutorial_header.inc.php'; ?>

 <div class="content_nav">
	<?php require_once('includes/tutorial_nav.php'); ?>
  
  </div>
  <div class="content_content">

<p><br><b>GNN Format</b></p>
<p>The GNN appears dramatically different from an SSN. The GNN visually
organizes genome neighborhood information into multiple hub-and-spoke
clusters. 
</p>
<p><img src='images/Tutorial_Figure2.jpeg' alt='Figure 2' width='580'>
<p><i>Figure 2:</i> Example colored SSN (left) and hub-and-spoke cluster from GNN (right).
<p><br><b>Hub Node</b></p>

<p><br>Each hub node represents a distinct Pfam family that was
retrieved as a neighbor to a query. Hub nodes are white and
labeled with the Pfam abbreviated name (e.g., Pro_racemase). Every
instance of retrieved neighbor sequences that are members of this
Pfam family regardless of query relationship, are consolidated into
this single hub node. The UniProt accession number for each retrieved
sequence is maintained within the hub node as a Node Attribute
(Neighbor_Accessions) with the following additional information: 
</p>
<ul>
	<li>EC number (if extant)</li>
	<li>PDB code (if extant)</li>
	<li>PDB-hit (described below)</li>
	<li>Swiss-Prot annotation status (reviewed/unreviewed)</li>
</ul>
<p><br>Additional Node Attributes that are specific to the hub node:</p>

<ul>
	<li>Num_neighbors = the number of neighbor sequences belonging to this
	Pfam family</li>
	<li>pfam = Pfam number, e.g., PF13365</li>
	<li>Pfam description = a short description of the family, e.g.,
	Trypsin-like peptidase domain</li>
</ul>
<p><br>PDB-hit is a novel Node Attribute that indicates whether a sequence
shares significant (e-value &lt; e<sup>-30</sup>) homology with a
protein for which an X-ray crystal structure has been deposited in the PDB.
The format of this information is “PDB code:e-value”. This
information is valuable to computational chemists wanting to
construct a structure model using a known structure as a template
from a protein similar in sequence. Ideally, the neighbor sequence
itself would have a deposited X-ray crystal structure, but this is
most often not the case. Nonetheless, confident structure models have
been employed successfully in pathway docking to determine the
substrates of unknown enzymes. For users that are new to homology
modeling, we suggest investigating the many useful modeling tools
being developed by the Andrej Sali lab at the University of
California at San Francisco <a href='http://salilab.org/our_resources.html'>here</a>.</p>

<p><br><b>Spoke Node</b></p>
<p><br>
The &quot;spokes&quot; then radiate out of the central hub node to
&quot;spoke nodes&quot;. Spoke nodes represent a single cluster from
the original SSN that contains a query sequence that retrieved a gene
in its neighborhood belonging to this particular Pfam family. Each
spoke node is assigned a unique color and numbered according to the
origin SSN-cluster. Additionally, spoke node size is dependent on the
percent representation of that Pfam in the neighborhood of that SSN
cluster - specifically, the ratio of the number of queries proximal
to the Pfam family compared to the number of total queries in the
origin SSN-cluster from which they came. This percent representation
may indicate distinct situations:</p>
<table width="638" cellpadding="7" cellspacing="0">
	<col width="136">
	<col width="472">
	<tr valign="top">
		<td width="136" style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
			<p align="center"><b>% Representation</b></p>
		</td>
		<td width="472" style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
			<p align="justify"><b>Indicative Situation</b></p>
		</td>
	</tr>
	<tr>
		<td width="136" style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
			<p align="center">&lt; 100%</p>
		</td>
		<td width="472" valign="top" style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
			<p align="justify"><font size="2" style="font-size: 11pt">The
			neighbor gene is not well-conserved and potentially unimportant to
			the physiological pathway of the query gene.</font></p>
		</td>
	</tr>
	<tr>
		<td width="136" style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
			<p align="center">&lt; 100%</p>
		</td>
		<td width="472" valign="top" style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
			<p align="justify"><font size="2" style="font-size: 11pt">This
			particular SSN-cluster is not isofunctional, containing multiple
			neighborhood contexts.</font></p>
		</td>
	</tr>
	<tr>
		<td width="136" style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
			<p align="center"><font color="#000000">≈</font> 100%</p>
		</td>
		<td width="472" valign="top" style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
			<p align="justify"><font size="2" style="font-size: 11pt">The
			neighbor gene is a well-conserved member of the genome
			neighborhood.</font></p>
		</td>
	</tr>
	<tr>
		<td width="136" style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
			<p align="center">&gt; 100%</p>
		</td>
		<td width="472" valign="top" style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
			<p>Two or
			more instances of neighbors from this particular Pfam family exist
			in the genome neighborhood.</font>
		</td>
	</tr>
</table>
<p><br>
The information contained within a single spoke node is now limited
only to the sequences with the connected Pfam family in their
neighborhood. The Node Attributes specific to spokes nodes are:</p>
<ul>
	<li>Cluster Number = number assigned to SSN-cluster during parsing step
	of GNN generation</li>
	<li>Query_Accessions = a list of UniProt accession numbers for the query
	sequences</li>
	<li>Distance = a list of which query sequence retrieved which neighbor
	sequences and the distance between the two. This is formatted
	“UniprotID-query:UniprotID-neighbor: (-)N”, where query = 0,
	next gene = 1, etc., and a negative N value indicates an upstream
	position.</li>
	<li>SSNClusterSize = the size of the origin SSN-cluster</li>
	<li>Num_neighbors = the number of neighbor sequences retrieved by this
	SSN-cluster</li>
	<li>Num_queries = the number of query sequences from the SSN-cluster
	that retrieved a neighbor from this connected Pfam</li>
	<li>Num_ratio = the “percent co-occurrence” as a ratio,
	specifically num_neighbors/SSNclusterSize</li>
	<li>ClusterFraction = the "percent co-occurrence" as decimal</li>
</ul>
<p><br>This hub-and-spoke clustering puts the emphasis on unique Pfam
families that are encoded by the genome neighborhood that includes the sequences from the
user’s SSN. It becomes immediately apparent in these networks which
Pfam families are highly populated, lowly populated, universal, or
situational. When analyzing the GNN of a particular sequence or
SSN-cluster (protocol described below), the user can immediately
identify the general classes of enzymes present. As described above
in the example where enzymes in a pathway are encoded by genes in an
operon, the presence of kinases and isomerases, may indicate that the
proteins of this particular SSN-cluster may carry out an
aldolase-type reaction for a catabolic pathway. </p>


<p><br><a name="_GoBack"></a>For the sake of completion, EFI-GNT can collect up to 10 ENA entries
both upstream and downstream of the query. This “neighborhood”
size is quite substantial and is, in the majority of cases, going to
contain more information than is functionally relevant. The average
operon length, from an analysis of 42 bacterial species, is 3-4 genes
(Zheng <i>et al</i>. 2002, <b>Genome Research</b> 12, 1221). Thus,
users may designate the number of ENA entries (from +/- 3 to 10) that
will be collected to compose the neighborhood network. Retrieving +/-
10 neighbors is recommended as a starting point if no other
information is known regarding the genome organization. The end-user
may analyze multiple networks in order to determine the optimal
signal-to-noise ratio; where “signal” is biologically relevant
gene neighbors and “noise” is unrelated, or coincidental, gene
neighbors. 
</p>
<p>*Note - by necessity, both up - and downstream neighbors must be collected</p>
        </div>
<div>
  <div> </div>
      </div>
    </div>

    <div class="clear"></div>

	</div>
    
    
    
    
    
    
   <!-- END: class="content_wide" -->
</div>

<p style='text-align:center;'><a href='tutorial_caveats.php'><button class="css_btn_class">Continue Tutorial</button></a></p>

<?php require_once('includes/tutorial_footer.inc.php'); ?>
