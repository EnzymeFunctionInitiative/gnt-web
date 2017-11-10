<?php
$TUTORIAL = true;
require_once 'inc/main.inc.php';
require_once 'inc/header.inc.php';
?>

<div class="tutorial_nav">
	<?php require_once('inc/tutorial_nav.php'); ?>
</div>
  

<div class="tutorial_body">

<h2>Result page and file download </h2>

<h3>EFI-GNT Files and Node Attributes</h3>

<p>
As described in the previous section, EFI-GNT generates a colored version of 
the input SSN as well as two formats of genome neighbor networks (GNNs) for 
download (all three in the xgmml format) and subsequent analysis with 
Cytoscape.
</p>

<p>
This section provides a detailed description of the colored SSN and both GNN 
formats, with emphasis on the node attributes that are provided for the GNNs; 
these include the query-neighbor distances, co-occurrence frequencies, and the 
identities of the neighbor’s Pfam family that are used for pathway predictions.
</p>

<h3>Colored SSN</h3>

<p>
The colored SSN assists the user in analyzing the GNNs by allowing color-guided 
association of the cluster nodes in the GNNs with the clusters in the input SSN 
(Figure 1).
</p>

<p><img src='images/tutorial/attributes_figure_1.jpg' width='600'></p>

<p><i>Figure 1.</i> Example of a colored SSN</p>

<p>
EFI-GNT assigns a unique number and color to each multi-node cluster in the 
input SSN. Node attributes are added for the cluster number (<b>Cluster 
Number</b>) and color (<b>node.fillColor</b>).   The clusters are ordered in 
order of decreasing number of sequences in the clusters (<b>Cluster Sequence 
Count</b>).
</p>

<p>
The colored SSN also includes node attributes to indicate whether the sequence 
has a match in the ENA files (<b>Present in ENA Database?</b>), neighbors in 
the ENA files (<b>Genome Neighbors in ENA Database?</b>), and the name of the 
ENA file (<b>ENA Database Genome ID</b>).
</p>

<p>
In full networks, singletons in the input SSN are excluded from the GNN 
analysis, while in Rep node networks, singletons with 1 sequence are excluded. 
Singletons excluded from the GNN analysis will appear in the colored SSN with 
the Cytoscape default color (cyan). All singletons will not have a 
cluster number assigned.
</p>

<h3>Two formats for GNN are generated</h3>

<p>
EFI-GNT generates the GNN in two formats that provide different query-neighbor 
perspectives to assist predictions of pathways. The formats differ in the 
identities of the cluster hub-node (neighbor Pfam family or query SSN cluster) 
and spoke-nodes (query SSN cluster or Pfam family, respectively).
</p>

<p>
A GNN contains clusters (hub-node and &ge; 1 spoke‑node) that provide genome 
neighborhood information (query-neighbor co-occurrence frequencies and 
distances) for the sequences in the clusters in the input SSN.
</p>

<p>
In the first GNN format (Figure 2, below), a cluster is present for each query 
SSN cluster (hub-node, center) that was used to identify genome neighbors 
(spoke-nodes). <b>This format allows the user to identify functionally linked 
enzymes, as deduced from genome proximity, that constitute the metabolic 
pathway in which the sequences in the query SSN cluster participate.</b>
</p>

<p><img src='images/tutorial/attributes_figure_2.jpg' width='600'></p>

<p><i>Figure 2.</i> SSN Cluster Hub-Nodes and Pfam Family Spoke-Nodes GNN: The 
hub-node for each cluster, a hexagon, is the cluster number. The hub-nodes are 
colored with the unique color assigned for the Colored SSN and labeled with the 
unique cluster number that was assigned.</p>

<p>
In the second GNN format (Figure 3, below), a cluster is present for each Pfam 
family (hub-node, center) that was identified as a neighbor to queries in the 
SSN clusters (spoke-nodes). <b>This format allows the user to assess whether 
queries in multiple SSN clusters are neighbors to members of the same Pfam 
family and, therefore, may have the same in vitro activities and in vivo 
metabolic functions.</b>
</p>

<p><img src='images/tutorial/attributes_figure_3.jpg' width='600'></p>
<p><i>Figure 3.</i> Pfam Family Hub-Nodes and SSN Cluster Spoke-Nodes GNN:
The hub-node, a hexagon colored grey, represents the neighbor Pfam family. 
These are labeled with the Pfam short name for the family (e.g., DAO for 
PF01266, FAD-dependent oxidoreductase). For multidomain proteins, the label is 
a composite of the Pfam short names for the component domains (e.g., FGGY_N- 
FGGY_C for PF00370-PF02782, the N- and C-terminal domains in the FGGY family of 
carbohydrate kinases).
</p>

<p>
In both formats, the node attributes for the Pfam family and SSN cluster nodes 
contain the same information; these provide information about the 
query-neighbor relationships that can be used to infer functional relationships 
(co-occurrence frequency and distance) that enable the prediction of in vitro 
activities and in vivo metabolic pathways.
</p>

<p>
If the nodes are not automatically colored when the SSN is opened in Cytoscape, 
the Style Control Panel can be used to color the nodes in the SSN: In the Fill 
Color property, select "node:node.fillColor" for the Column value and 
"Passthrough Mapping" for the Mapping Type value.
</p>

<h3>Additional Files for Download</h3>

<p>
In addition to the colored SSN, several text files/folders of text files are 
available for download.  These include:
</p>

<ol>
<li>"UniProt ID-Color-Cluster Number Mapping Table" is a tab-delimited text file with three columns with headers, 
"UniProt ID", "Cluster Number", and "Cluster Color"; a description of this file and its use was provided in the Color 
SSN Utility section of the <a href="<?php echo settings::get_est_url(); ?>/tutorial.php">EST Tutorial</a>.</li>

<li>"UniProt IDs per Cluster" is a folder of files for each cluster that list the UniProt IDs for the sequences in the 
cluster; a description of the files in this folder and their use was provided in the Color SSN Utility section
of the <a href="<?php echo settings::get_est_url(); ?>/tutorial.php">EST Tutorial</a>.</li>

<li>"FASTA Files per Cluster" is a folder of files for each cluster that contain the FASTA-formatted sequences for the 
sequences in the cluster; a description of the files in this folder and their use was provided in the Color SSN Utility 
section of the <a href="<?php echo settings::get_est_url(); ?>/tutorial.php">EST Tutorial</a>.</li>

<li>"Pfam Neighbor Mapping Tables" is a folder of tab-delimited text files for each neighbor Pfam family that includes 
the following columns:  "Query ID", "Neighbor ID", "Neighbor Pfam", "SSN Query Cluster #", "SSN Query Cluster Color", 
"Query-Neighbor Distance" (absolute value of distances in ORFs), and "Query-Neighbor Directions" (relative directions of 
transcription).  These files can be used with the Cytoscape BridgeDB App to add these columns as node attributes to the 
SSN for the neighbor Pfam family so that the neighbors can be located/analyzed in the sequence-function context of the 
family.  The "SSN Query Cluster Color" node attributes can be used with "pass-through mapping" in the Cytoscape Style 
Color panel to color the nodes in the neighbor Pfam family with the colors in the colored SSN generated by EFI-GNT 1.0, 
thereby facilitating the determination of whether the neighbors identified in the genome neighborhoods are orthologous.  
A concatenated file that contains all of the information is also available and may be more convenient for adding node 
attributes to SSNs for multiple Pfam families.</li>

<li>"Neighbors without PFAM assigned per Cluster" is a folder of tab-delimited text files for each SSN cluster that 
lists the accession IDs of neighbors not assigned to Pfam families.  These files allow SSNs to be generated with Option 
D of EFI-EST 2.0 so that protein families not curated by Pfam can be identified.  The user should use an alignment score 
of ~20 to filter the SSN; in most cases, this alignment score will segregate the SSN into protein families.</li>

<li>"No Matches/No Neighbors File" is a tab-delimited text file with two columns:  "UniProt ID" and "No Match/No 
Neighbor" ("nomatch" or "neighbor").  The same information is included in the colored SSN generated by EFI-GNT 1.0 (vide 
infra).</li>
</ol>

<p>Description of node attributes for the various output files is available below
and is provided in two formats: a verbose description and a tabular summary.</p>

<ul>
<li><a href="#fmt1verbose">Verbose description of node attributes for GNN format #1</a></li>
<li><a href="#fmt1tabular">Tabular summary of node attributes for GNN format #1</a></li>
<li><a href="#fmt2verbose">Verbose description of node attributes for GNN format #2</a></li>
<li><a href="#fmt2tabular">Tabular summary of node attributes for GNN format #2</a></li>
<li><a href="#coloredssn">Tabular summary of node attributes in colored SSN</a></li>
</ul>


<a name="fmt1verbose"></a>
<h2>Details for format GNN 1: SSN Cluster Hub-Nodes and Pfam Family Spoke-Nodes</h2>

<p>
In the first GNN format, each hub-node corresponds a cluster present in the SSN 
(center). The cluster hub-node color and number are identical to those in the 
colored SSN. Sequences belonging to that cluster were used to retrieve genome 
neighbors (spoke-nodes; Figure 2). This format allows the user to identify 
functionally linked enzymes, as deduced from genome proximity, that constitute 
the metabolic pathway in which the query participates.
</p>

<p>
A spoke-node, colored grey and represented as a hexagon, is present for each 
Pfam family that was identified as a neighbor of a query sequence in the 
cluster represented by the hub-node. These are labeled with the short name for 
the Pfam family (e.g., Aldedh for PF00171, aldehyde dehydrogenase). For 
multidomain proteins, the hub-node name is a composite of the short names for 
the component domains (e.g., HTH_1-LysR for PF00126-PF03466, the N-terminal HTH 
DNA-binding and C-terminal ligand binding domains of LysR transcriptional 
regulators). A spoke-node ("none") will be present for neighbors that have 
not been assigned to a Pfam family.
</p>

<p>
The size of the spoke-node (<b>node.size</b>) is determined by the value of the 
<b>Co-occurrence</b> node attribute [decimal value of the ratio of the number of 
queries (sequences with neighbors) in the cluster that found neighbors in the 
Pfam family to the number of queriable sequences in the cluster (<b>Queries 
with Pfam Neighbors/Queriable SSN Sequences</b>); see below]—the larger the 
co-occurrence frequency of the SSN cluster queries and their genome neighbors, 
the larger the spoke-node. The value of <b>node.size</b> [calculated as 
(<b>Co-occurrence</b> * 100)] is used by Cytoscape to draw the node.
</p>

<p>
The shape of spoke-node (<b>node.shape</b>) is determined by the values of two 
node attributes for the neighbors that were identified  (in the <b>Query-Neighbor 
Accession</b> node attribute): a triangle if a SwissProt description is available, 
a square if a Protein Data Bank (PDB) code is available; a diamond if both an 
EC number and a PDB are available; or a circle if neither an EC number or a PDB 
code is available. The availability of a SwissProt description and/or a PDB 
code suggests that the function of the neighbor may be known. The node shape in 
the node shape node attribute (<b>node.shape</b>) is used by Cytoscape to draw 
the node. The network can be filtered with the Select Panel to select specific 
shapes, i.e. different levels of confidence about the functions of the 
neighbors.
</p>

<p>
The identities of the neighbors not associated with a Pfam family (no Pfam or 
"none" family) also are provided in the Neighbor Accession and Query-Neighbor 
Arrangement node attributes (described in detailed in the descriptions of the 
node attributes).
</p>

<h3>Node attributes for each SSN cluster hub-node</h3>

<div class="indent">
    <b>shared name</b> (generated by Cytoscape): the unique number assigned to each 
cluster in the input SSN (singletons are not included) This node attribute is 
text—strings of characters can be selected with the Select Control Panel.
</div>

<div class="indent">
    <b>name</b> (generated by Cytoscape): the unique number assigned to each cluster in 
the input SSN (singletons are not included) This node attribute is text—strings 
of characters can be selected with the Select Control Panel.
</div>

<div class="indent">
    <b>Cluster Number</b>: the unique number assigned to each cluster in the input SSN 
(singletons are not included) This node attribute has a numerical value— a 
specific number of sequences or a range of sequences can be selected with the 
Select Control Panel.
</div>

<div class="indent">
    <b># of Sequences in SSN Cluster</b>: The total number of sequences in the SSN 
cluster. This node attribute has a numerical value—a specific Cluster Number or 
a range of Cluster Numbers can be selected with the Select Control Panel.
</div>

<div class="indent">
    <b># of Sequences in SSN Cluster with Neighbors</b>: The number of sequences in the 
SSN cluster that have genome neighbors in the bacterial and fungal ENA sequence 
files (queriable sequences). The value of this node attribute is calculated by: 
<p>
<div class="indent">Total number of sequences in the SSN cluster (<b># of Sequences in
SSN Cluster</b>) –</div>
<div class="indent">number of sequences that did not have a match in the ENA
sequence files (list provided in the nomatch file that can be downloaded) –</div>
<div class="indent">number of sequences for which the ENA sequence files did not provide genome
neighborhoods (list provided in the noneighbor file that can be
downloaded).</div>
</p>
This node attribute has a 
numerical value—a specific number of sequences or a range of sequences can be 
selected with the Select Control Panel.
</div>

<div class="indent">
    <b>Hub Queries with Pfam Neighbors</b>: A summary for all spoke-node Pfam families 
    of the number of queriable sequences in the hub-node SSN cluster (<b># of Sequences 
    in SSN Cluster with Neighbors</b>) for which a neighbor in the Pfam family was found 
    in the following format:
    
    <p class="indentall">cluster#:Pfam#:#Queries with Pfam Neighbors</p>
        
    where
    <ul>
        <li>"cluster#" is the cluster# for the query</li>
        <li>"Pfam#" is the spoke-node Pfam family 
            number</li>
        <li>"#Queries with Pfam Neighbors" is the number of queriable sequences 
            in the SSN cluster (<b># of Sequences in SSN Cluster with Neighbors</b>) for which a 
            neighbor in the spoke-node Pfam family was found.</li>
    </ul>

    A query may find multiple 
    members of the Pfam family, but this node attribute reports only the number of 
    queries that found any neighbor in the Pfam family. The neighbors in the Pfam 
    family need not be orthologues (share the same function). This node attribute is 
    text—strings of characters can be selected with the Select Control Panel. By 
    right clicking on the node attribute, the entries can be copied and pasted into 
    Excel or a text file for further analyses. In Excel, the colon-delimited entries 
    can be easily separated into separate columns.
</div>

<div class="indent">
    <b>Hub Pfam Neighbors</b>: A summary for all spoke-node Pfam families of the number 
of neighbors in the Pfam family that were found by the queries in the hub-node 
SSN cluster in the following format:

<p class="indentall">cluster#:Pfam#:#Pfam Neighbors</p>

where

<ul>
<li>"cluster#" is the cluster# for the query</li>
<li>"Pfam#" is the  spoke-node Pfam family</li>
<li>"#Pfam Neighbors" is the number of neighbors in the 
spoke-node Pfam family found by the queries in hub-node SSN cluster.</li>
</ul>

The value 
of "#Pfam Neighbors" will be greater than the value of the <b>Hub Queries with PFAM 
Neighbors</b> (previous) node attribute if a query found more than one neighbor in 
the Pfam family. Again, the neighbors in the Pfam family need not be orthologues 
(share the same function). This node attribute is text—strings of characters can 
be selected with the Select Control Panel. By right clicking on the node 
attribute, the entries can be copied and pasted into Excel or a text file for 
further analyses. In Excel, the colon-delimited entries can be easily separated 
into separate columns.
</div>

<div class="indent">
    <b>Hub Average and Median Distances</b>: A summary for all spoke-node Pfam families 
of the values of the <b>Average Distance</b> and <b>Median Distance</b> node attributes in the 
following format:

<p class="indentall">cluster#:"Pfam#:average absolute value of distances:median 
absolute value of distances</p>

where

<ul>
<li>"cluster#" is the cluster# for the 
query</li>
<li>"Pfam#" is the Pfam# for the neighbor</li>
<li>"average absolute value of 
distances" is the average of the absolute values of distances between the 
hub-node queries and spoke-node neighbors</li>
<li>"median absolute value of 
distances" is the median absolute value of distances between the hub-node 
queries and spoke-node neighbors.</li>
</ul>

This node attribute is text—strings of 
characters can be selected with the Select Control Panel. By right clicking on 
the node attribute, the entries can be copied and pasted into Excel or a text 
file for further analyses. In Excel, the colon-delimited entries can be easily 
separated into separate columns.
</div>

<div class="indent">
    <b>Hub Co-occurrence and Ratio</b>: A summary for all spoke-node Pfam families of 
the values for <b>Co-occurrence</b> and <b>Co-occurrence Ratio</b> node attributes in the 
following format:

<p>cluster#:Pfam#:Co-occurrence:Co-occurrence Ratio</p>

where

<ul>
<li>"cluster#" is the cluster# for the query</li>
<li>"Pfam#" is the Pfam# for 
the neighbor</li>
<li>"Co-occurrence" is the decimal value of the ratio of the 
number queries that found neighbors in the Pfam family to the number of 
queriable sequences in the hub-node SSN query cluster (<b># of Queries with Pfam 
Neighbors</b>/<b># of Sequences in SSN Cluster with Neighbors</b>)</li>
<li>"Co-occurrence 
Ratio" is the numerical ratio of the number of queries that found neighbors in 
the Pfam family to the number of queriable sequences in the hub-node SSN 
cluster.</li>
</ul>

This node attribute is text—strings of characters can be selected with 
the Select Control Panel. By right clicking on the node attribute, the entries 
can be copied and pasted into Excel or a text file for further analyses. In 
Excel, the colon-delimited entries can be easily separated into separate 
columns.
</div>

<div class="indent">
    <b>Node.fillColor</b>: the hexadecimal number of the unique color assigned to each 
cluster in the input SSN (singletons are not included). This number is used by 
the pass-through mapping "Fill Color" style of Cytoscape to color the nodes in 
the network.  
</div>

<p>
If the nodes are not automatically colored when the SSN is opened in Cytoscape, 
the Style Control Panel can be used to color the nodes in the SSN: In the Fill 
Color property, select "node:node.fillColor" for the Column value and 
"Passthrough Mapping" for the Mapping Type value.
</p>

<div class="indentall">
    <b>Node.shape</b>: hexagon (used by Cytoscape but can be used in searches to select hub-nodes)<br>
    <b>Node.size</b>: 70.0 (used by Cytoscape)<br>
    <b>Pfam</b>: empty (a spoke-node attribute)<br>
    <b>Pfam description</b>: empty (a spoke-node attribute)<br>
    <b># of Queries with Pfam Neighbors</b>: empty (a spoke-node attribute)<br>
    <b># of Pfam Neighbors</b>: empty (a spoke-node attribute)<br>
    <b>Query-Accessions</b>: empty (a spoke-node attribute)<br>
    <b>Query-Neighbor Accessions</b>: empty (a spoke-node attribute)<br>
    <b>Query-Neighbor Arrangement</b>: empty (a spoke-node attribute)<br>
    <b>Average Distance</b>: empty (a spoke-node attribute)<br>
    <b>Median Distance</b>: empty (a spoke-node attribute)<br>
    <b>Co-occurrence</b>: empty (a spoke-node attribute)<br>
    <b>Co-occurrence Ratio</b>: empty (a spoke-node attribute)
</div>

<h3>Node attributes for each Pfam family spoke-node</h3>

<div class="indent">
<b>shared name</b>: the Pfam family short name or hyphen-separated short names
for multidomain proteins) This node attribute is text—strings of characters can
be selected with the Select Control Panel.
</div>

<div class="indent">
<b>name</b>: the Pfam family short name (hyphen-separated names for multidomain
proteins) This node attribute is text—strings of characters can be selected
with the Select Control Panel [The <b>shared name</b>
and <b>name</b> node attributes are redundant and required by Cytoscape.]
</div>

<div class="indent">
<b>Cluster Number</b>: the hub-node SSN cluster that found neighbors in the
Pfam family spoke-node.
</div>

<div class="indent">
<b>Pfam</b>: the Pfam family number (PFxxxxx) (or hyphen-separated numbers for
multidomain proteins) This node attribute is text—strings of characters can be
selected with the Select Control Panel.
</div>

<div class="indent">
<b>Pfam description</b>: the Pfam family long name (or hyphen-separated names
for multidomain proteins) This node attribute is text—strings of characters can
be selected with the Select Control Panel.
</div>

<div class="indent">
<b># of Sequences in SSN Cluster with Neighbors</b>: The number of sequences in
the SSN cluster that have genome neighbors in the bacterial and fungal ENA
sequence files (queriable sequences). The value of this node attribute is
calculated by:
<p>
<div class="indent">Total number of sequences in the SSN cluster (<b># of Sequences in
SSN Cluster</b>) –</div>
<div class="indent">number of sequences that did not have a match in the ENA
sequence files (list provided in the nomatch file that can be downloaded) –</div>
<div class="indent">number of sequences for which the ENA sequence files did not provide genome
neighborhoods (list provided in the noneighbor file that can be
downloaded).</div>
</p>
This node attribute has a numerical value—a specific number of
sequences or a range of sequences can be selected with the Select Control
Panel.
</div>

<div class="indent">
<b># of Queries with Pfam Neighbors</b>: The total number of queriable
sequences in the hub-node SSN cluster (<b># of Sequences in SSN Cluster with
Neighbors</b> in SSN cluster hub-node) for which any neighbor in this Pfam family
was found. A query may find multiple members of the Pfam family, but this node
attribute reports only the number of queries that found any neighbor.
</div>

<div class="indent">
<b># of Pfam Neighbors</b>: The total number of neighbors in the Pfam family
found by the queries in the spoke-node SSN cluster. This value of this node
attribute will be greater than the value of the <b>Queries with PFAM Neighbors</b>
(previous) node attribute if a query found more than one neighbor in the Pfam
family. The neighbors in the Pfam family need not be orthologues (share the
same function)—this can be evaluated by mapping the neighbors to the SSN for
the Pfam family using the spreadsheet/custom node attribute files that can be
downloaded. This node attribute has a numerical value—a specific number of
sequences or a range of sequences can be selected with the Select Control
Panel.
</div>

<div class="indent">
<b>Query-Accessions</b>: A list of the query accession IDs in the SSN cluster
that found neighbors.
</div>

<div class="indent">
    <b>Query-Neighbor Accessions</b>: Information about the query-neighbor pairs in
    the Pfam family in the following format:
    
    <p class="indentall">Pfam#:Query ID:Neighbor ID:EC#:NeighborPDB:ClosestPDB:PDB-E-value:Status</p>
    
    where:
    <ul>
        <li>"Query ID" is the query accession ID,</li>
        <li>"Pfam#" is the Pfam# for the neighbor,</li>
        <li>"Neighbor ID" is the neighbor accession ID,</li>
        <li>"EC#" is the E.C. number, if any, assigned to the neighbor in the UniProt database,</li>
        <li>"NeighborPDB is the Protein Databank (PDB) identifier for the neighbor is one is available,</li>
        <li>"ClosestPDB" is the Protein Databank (PDB) identifier for the most similar
            sequence to the neighbor with a structure in the PDB database,</li>
        <li>"PDB-E-value" is the BLAST e-value for the neighbor-ClosestPDB pair, and</li>
        <li>"Status" (SwissProt/TrEMBL) reports if the in vitro activity of the neighbor
            has been reviewed by SwissProt.</li>
    </ul>
    
    This node
    attribute is text—strings of characters can be selected with the Select Control
    Panel. By right clicking on the node attribute, the entries can be copied and
    pasted into Excel or a text file for further analyses. In Excel, the
    colon-delimited entries can be easily separated into separate columns.
</div>

<div class="indent">
<i>[ClosestPDB:PDB-E-value is a novel Node Attribute that indicates whether a
sequence shares significant (e-value &lt; e-30) homology with a protein for
which an X-ray crystal structure has been deposited in the PDB. The format of
this information is "PDB code:e‑value". This information is valuable to
computational chemists wanting to construct a structure model using a known
structure as a template from a protein similar in sequence. Ideally, the
neighbor sequence itself would have a deposited X-ray crystal structure
(previous field in this node attribute), but this is most often not the case.
Nonetheless, confident structure models have been employed successfully in
pathway docking to determine the substrates of unknown enzymes.]</i>
</div>

<div class="indent">
<b>Query-Neighbor Arrangement</b>: Genome context information for neighbors in
the Pfam family in the following format:

<p class="indentall">Pfam#:Query ID:normal/complement:Neighbor ID: noncomplement/complement:Distance</p>

where 

<ul>
<li>"Pfam#" is the Pfam# for the neighbor,</li>
<li>"Query ID" is the query accession ID,</li>
<li>"normal/complement" is the direction of transcription of the gene encoding the query (from the ENA sequence file),</li>
<li>"Neighbor ID" is the neighbor accession ID,</li>
<li>"normal/complement" is the direction of transcription of the gene encoding the query (from the ENA sequence file), and</li>
<li>"Distance" is the distance in orfs between the genes encoding the query and neighbor.</li>
</ul>

This node attribute is text—strings of
characters can be selected with the Select Control Panel. By right clicking on
the node attribute, the entries can be copied and pasted into Excel or a text
file for further analyses. In Excel, the colon-delimited entries can be easily
separated into separate columns.
</div>

<div class="indent">
<b>Average Distance</b>: The average of the absolute values of the distances
between the queries in the hub-node SSN cluster and their neighbors in the Pfam
family. This node attribute has a numerical value—a specific number of
sequences or a range of sequences can be selected with the Select Control
Panel.
</div>

<div class="indent">
<b>Median Distance</b>: The median value of the absolute values of the
distances between the queries in the hub-node SSN cluster and their neighbors
in the Pfam family. This node attribute has a numerical value—a specific number
of sequences or a range of sequences can be selected with the Select Control
Panel.
</div>

<div class="indent">
<b>Co-occurrence</b>: The decimal value of the ratio of the number of queries
in the hub-node SSN cluster that found neighbors in the Pfam family to the
number of queriable sequences in the SSN cluster (<b># of Queries with Pfam
Neighbors</b>/<b># of Sequences in SSN Cluster with Neighbors</b>). This node attribute
has a numerical value—a specific number of sequences or a range of sequences
can be selected with the Select Control Panel.
</div>

<div class="indent">
<b>Co-occurrence Ratio</b>: The numerical ratio of the number of queries in the
hub- node SSN cluster that found neighbors in the Pfam family to the number of
queriable sequences in the SSN cluster (<b>Hub Queries with Pfam Neighbors</b>/<b># of
Sequences in SSN Cluster with Neighbors</b>). This node attribute is text-strings of
characters can be selected with the Select Control Panel.
</div>

<div class="indent">
<b>Node.fillColor</b>: #EEEEEE (grey in hexadecimal); used by Cytoscape. This
node attribute is text—strings of characters can be selected with the Select
Control Panel.
</div>

<div class="indent">
<b>Node.shape</b>: ellipse, diamond, or square (explained above); used by
Cytoscape but can be used in searches. This node attribute is text—strings of
characters can be selected with the Select Control Panel.
</div>

<div class="indent">
<b>Node.size</b>: calculated as (Co-occurrence * 100); used by Cytoscape. This
node attribute is text—strings of characters can be selected with the Select
Control Panel.
</div>

<div class="indentall">
    <b># of Sequences in SSN Cluster</b>: empty (a hub-node attribute)<br>
    <b># of Sequences in SSN Cluster with Neighbors</b>: empty (a hub-node attribute)<br>
    <b>Hub Queries with Pfam Neighbors</b>: empty (a hub-node attribute)<br>
    <b>Hub Pfam Neighbors</b>: empty (a hub-node attribute)<br>
    <b>Hub Average and Median Distances</b>: empty (a hub-node attribute)<br>
    <b>Hub Co-occurrence and Ratio</b>: empty (a hub-node attribute)
</div>

<a name="fmt2verbose"></a>
<h2>Details for GNN format 2: Pfam Family Hub-Nodes and SSN Cluster Spoke-Nodes</h2>

<p>
In the second GNN format, a cluster is present for each Pfam family (hub-node)
that was identified as a neighbor to queries in the SSN clusters (spoke-nodes;
Figure 3). <b>This format allows the user to assess whether queries in multiple
SSN clusters are neighbors to members of the same Pfam family and, therefore,
may have the same in vitro activities and in vivo metabolic functions.</b>
</p>

<p>A spoke-node is present for each SSN cluster that identified a member of 
the Pfam family. The color of the spoke-node is the unique color assigned in
the colored SSN; its label is the unique cluster number.
</p>

<p>
The size of the spoke-node (<b>node.size</b>) is determined by the value of the
<b>Co-occurrence</b> node attribute [decimal value of the ratio of the number of
queries in the cluster that found neighbors in the Pfam family to the number of
queriable sequences in the cluster (<b># of Queries with Pfam Neighbors</b>/<b># of
Queriable SSN Sequences</b>); see below]—the larger the co-occurrence frequency of
the SSN cluster queries and their genome neighbors, the larger the spoke-node.
The value of <b>node.size</b> [calculated as (<b>Co-occurrence</b> * 100)] is used by
Cytoscape to draw the node.
</p>

<p>
The shape of spoke-node (<b>node.shape</b>) is determined by the values of two node
attributes for the neighbors that were identified (in the Query-Neighbor
Accession node attribute): a triangle if a SwissProt description is available,
a square if a Protein Data Bank (PDB) code is available; a diamond if both an
EC number and a PDB are available; or a circle if neither an EC number or a PDB
code is available. The availability of a SwissProt description and/or a PDB
code suggests that the function of the neighbor may be known. The node shape in
the node shape node attribute (<b>node.shape</b>) is used by Cytoscape to draw the
node. The network can be filtered with the Select Panel to select specific
shapes, i.e. different levels of confidence about the functions of the
neighbors.
</p>

<p>
The network can be filtered with the Control Select Panel to select specific
node shapes, i.e., different levels of confidence about the functions of
neighbors.
</p>

<p>
The identities of the neighbors not associated with a Pfam family are provided
in the <b>Neighbor Accession</b> and <b>Query-Neighbor Arrangement</b> node attributes for
the Pfam family nodes (described in detailed in the descriptions of the node
attributes).
</p>

<h3>Node attributes for Pfam family hub-nodes</h3>

<div class="indent">
<b>shared name</b>: the Pfam family short name (or hyphen-separated short names
for multidomain proteins) This node attribute is text—strings of characters can
be selected with the Select Control Panel.
</div>

<div class="indent">
<b>name</b>: the Pfam family short name (hyphen-separated names for multidomain
proteins) This node attribute is text—strings of characters can be selected
with the Select Control Panel. [The shared name and name node attributes are
redundant and required by Cytoscape.]
</div>

<div class="indent">
<b>Pfam</b>: the Pfam family number (PFxxxxx) (or hyphen-separated numbers for
multidomain proteins) This node attribute is text—strings of characters can be
selected with the Select Control Panel.
</div>

<div class="indent">
<b>Pfam description</b>: the Pfam family long name (or hyphen-separated names
for multidomain proteins) This node attribute is text—strings of characters can
be selected with the Select Control Panel.
</div>

<div class="indent">
<b># of Sequences in SSN Cluster</b>: The total number of sequences in the
spoke-node SSN clusters. This node attribute has a numerical value—a specific
number of sequences or a range of sequences can be selected with the Select
Control Panel.
</div>

<div class="indent">
<b># of Sequences in SSN Cluster with Neighbors</b>: The total number of
sequences in the spoke-node SSN clusters that have genome neighbors in the
bacterial and fungal ENA sequence files (queriable sequences). The value of
this node attribute is calculated by:
<p>
<div class="indent">Total number of sequences in the spoke-node SSN clusters (<b># of Sequences in SSN Cluster</b>) –</div>
<div class="indent">number of sequences that did not have a match in the ENA sequence files (list provided in the nomatch file that can be downloaded) –</div>
<div class="indent">number of sequences for which the ENA sequence files did not provide genome neighborhoods (list provided in the noneighbor file that can be downloaded).</div>
</p>
This node attribute has a numerical
value—a specific number of sequences or a range of sequences can be selected
with the Select Control Panel.
</div>

<div class="indent">
<b># of Queries with Pfam Neighbors</b>: The total number of queriable
sequences in the spoke-node SSN clusters for which a neighbor in the hub-node
Pfam family was found. A query may find multiple members of the Pfam family,
but this node attribute reports only the number of queries that found any
neighbor in the Pfam family. The neighbors in the Pfam family need not be
orthologues (share the same function)—this can be evaluated by mapping the
neighbors to the SSN for the Pfam family using the spreadsheet/custom node
attribute files that can be downloaded. This node attribute has a numerical
value—a specific number of sequences or a range of sequences can be selected
with the Select Control Panel.
</div>

<div class="indent">
<b># of Pfam Neighbors</b>: The total number of neighbors in the Pfam family
found by the queries in the spoke-node SSN clusters. This value of this node
attribute will be greater than the value of the <b># of Queries with Pfam
Neighbors</b> (previous) node attribute if a query found more than one neighbor in
the Pfam family. Again, the neighbors in the Pfam family need not be
orthologues (share the same function)—this can be evaluated by mapping the
neighbors to the SSN for the Pfam family using the spreadsheet/custom node
attribute files that can be downloaded. This node attribute has a numerical
value—a specific number of neighbors or a range of neighbors can be selected
with the Select Control Panel.
</div>

<div class="indent">
<b>Query Accessions</b>: A summary of queries for all spoke-node SSN clusters
in the following format:

<p class="indentall">cluster#:Query ID</p>

where

<ul>
<li>"cluster#" is the cluster# for the query, and</li>
<li>"Query ID" is the query accession ID.</li>
</ul>

This node attribute is
text—strings of characters can be selected with the Select Control Panel. By
right clicking on the node attribute, the entries can be copied and pasted into
Excel or a text file for further analyses. In Excel, the colon-delimited
entries can be easily separated into separate columns.
</div>

<div class="indent">
<b>Query-Neighbor Accessions</b>: A summary for all spoke-node SSN clusters of
information about the query-neighbor pairs in the Pfam family in the following
format:

<p class="indentall">cluster#:Query ID:Neighbor
ID:EC#:NeighborPDB:ClosestPDB:PDB-E-value:Status</p>

where

<ul>
<li>"cluster#" is the cluster# for the query,</li>
<li>"Query ID" is the query accession ID,</li>
<li>"Neighbor ID" is the neighbor accession ID,</li>
<li>"EC# "is the E.C. number, if any, assigned to the neighbor in the UniProt database,</li>
<li>"NeighborPDB" is the Protein Databank (PDB) identifier for the neighbor is one is available,</li>
<li>"ClosestPDB" is the Protein Databank (PDB) identifier for the most similar sequence to the neighbor with a structure in the PDB database,</li>
<li>"PDB-E-value" is the BLAST e-value for the neighbor-ClosestPDB pair, and</li>
<li>"Status" (SwissProt/TrEMBL) reports if the in vitro activity of the neighbor has been reviewed by SwissProt.</li>
</ul>

This node
attribute is text—strings of characters can be selected with the Select Control
Panel. By right clicking on the node attribute, the entries can be copied and
pasted into Excel or a text file for further analyses. In Excel, the
colon-delimited entries can be easily separated into separate columns.
</div>

<div class="indent">
<i>[ClosestPDB:PDB-E-value is a novel Node Attribute that indicates whether a
sequence shares significant (e-value &lt; e-30) homology with a protein for
which an X-ray crystal structure has been deposited in the PDB. The format of
this information is "PDB code:e-value". This information is valuable to
computational chemists wanting to construct a structure model using a known
structure as a template from a protein similar in sequence. Ideally, the
neighbor sequence itself would have a deposited X-ray crystal structure
(previous field in this node attribute), but this is most often not the case.
Nonetheless, confident structure models have been employed successfully in
pathway docking to determine the substrates of unknown enzymes.]</i>
</div>

<div class="indent">
<b>Query-Neighbor Arrangement</b>: A summary for all spoke-node SSN clusters of
genome context information for the neighbors in the Pfam family in the
following format:

<p class="indentall">cluster#:Query ID:normal/complement:Neighbor ID: normal/
complement:Distance</p>

where

<ul>
<li>"cluster#" is the cluster# for the query,</li>
<li>"Query ID" is the query accession ID,</li>
<li>"normal/complement" is the direction of transcription of the gene encoding the query (from the ENA sequence file),</li>
<li>"Neighbor ID" is the neighbor accession ID,</li>
<li>"normal/complement" is the direction of transcription of the gene encoding the query (from the ENA sequence file), and</li>
<li>"Distance" is the distance in orfs between the genes encoding the query and neighbor.</li>
</ul>

This node attribute is text—strings of
characters can be selected with the Select Control Panel. By right clicking on
the node attribute, the entries can be copied and pasted into Excel or a text
file for further analyses. In Excel, the colon-delimited entries can be easily
separated into separate columns.
</div>

<div class="indent">
<b>Hub Average and Median Distances</b>: A summary for all spoke-node SSN
clusters of the values of the <b>Average Distance</b> and <b>Median Distance</b> node
attributes in the following format:

<p class="indentall">cluster#:<b>Average Distance</b>:<b>Median
Distance</b></p>

where

<ul>
<li>"cluster#" is the cluster# for the query,</li>
<li>"average absolute value of distances" is the average distance between the queries and neighbors in the cluster, and</li>
<li>"median absolute value of distances" is the median distance between the queries and neighbors in the cluster.</li>
</ul>

This node attribute is text—strings of characters can
be selected with the Select Control Panel. By right clicking on the node
attribute, the entries can be copied and pasted into Excel or a text file for
further analyses. In Excel, the colon-delimited entries can be easily separated
into separate columns.
</div>

<div class="indent">
<b>Hub Co-occurrence and Ratio</b>: A summary for all spoke-node SSN clusters
of the values for Co-occurrence and Co-occurrence Rationode attributes in the
following format:

<p class="indentall">cluster#:Co-occurrence:Co-occurrence Ratio</p>

where

<ul>
<li>"cluster#" is the cluster# for the query,</li>
<li>"Co-occurrence" is the decimal value of the ratio of the number of queries that found neighbors in the Pfam family to the number of queriable sequences in the cluster (<b># of Queries with Pfam Neighbors</b>/<b># of Sequences in SSN Cluster with Neighbors</b>), and</li>
<li>"Co-occurrence Ratio" is the ratio of the number of queries that found neighbors in the Pfam family to the number of queriable sequences in the cluster.</li>
</ul>

This node
attribute is text—strings of characters can be selected with the Select Control
Panel. By right clicking on the node attribute, the entries can be copied and
pasted into Excel or a text file for further analyses. In Excel, the
colon-delimited entries can be easily separated into separate columns.
</div>

<div class="indent">
<b>Node.fillColor</b>: #FFFFFF (white in hexadecimal; used by Cytoscape) This
node attribute is text—strings of characters can be selected with the Select
Control Panel.
</div>

<div class="indent">
<b>Node.shape</b>: hexagon (used by Cytoscape but can be used in searches to
select types of nodes) This node attribute is text—strings of characters can be
selected with the Select Control Panel.
</div>

<div class="indent">
<b>Node.size</b>: 70.0 (used by Cytoscape) This node attribute is text—strings
of characters can be selected with the Select Control Panel.
</div>

<div class="indentall">
    <b>Cluster Number</b>: empty (a spoke-node attribute)<br>
    <b>Average Distance</b>: empty (a spoke-node attribute)<br>
    <b>Median Distance</b>: empty (a spoke-node attribute)<br>
    <b>Co-occurrence</b>: empty (a spoke-node attribute)<br>
    <b>Co-occurrence Ratio</b>: empty (a spoke-node attribute)
</div>

<h3>Node attributes for SSN cluster spoke-nodes</h3>

<div class="indent">
<b>shared name</b>: the unique number assigned to each cluster in the input SSN
(singletons are not included) This node attribute is text—strings of characters
can be selected with the Select Control Panel.
</div>

<div class="indent">
<b>name</b>: the unique number assigned to each cluster in the input SSN
(singletons are not included) This node attribute is text—strings of characters
can be selected with the Select Control Panel.
</div>

<div class="indent">
<b>Cluster Number</b>: the unique number assigned to each cluster in the input
SSN (singletons are not included) This node attribute has a numerical value—a
specific Cluster Number or a range of Cluster Numbers can be selected with the
Select Control Panel.
</div>

<div class="indent">
<b># of Sequences in SSN Cluster</b>: The total number of sequences in the SSN
cluster. This node attribute has a numerical value—a specific number of
sequences or a range of sequences can be selected with the Select Control
Panel.
</div>

<div class="indent">
<b># of Sequences in SSN Cluster with Neighbors</b>: The number of sequences in
the SSN cluster that have genome neighbors in the bacterial and fungal ENA
sequence files. The value of this node attribute is calculated by:
<p>
<div class="indent">Total number of sequences in the cluster (<b># of Total SSN Sequences</b>) –</div>
<div class="indent">number of sequences that did not have a match in the ENA 
    sequence files (list provided in the nomatch file that can be downloaded) –</div>
<div class="indent">number of sequences for which the ENA sequence files did not provide 
    genome neighborhoods (list provided in the noneighbor file that can be 
    downloaded).</div>
</p>

This node attribute has a numerical
value—a specific number of sequences or a range of sequences can be selected
with the Select Control Panel.
</div>

<div class="indent">
<b># of Queries with Pfam Neighbors</b>: The total number of queriable
sequences (value of <b># Queriable SSN Sequences</b>) in the SSN cluster for which a
neighbor in the hub- node Pfam family was found. A query may find multiple
members of the Pfam family, but this node attribute reports only the number of
queries that found any neighbor in the Pfam family. The neighbors in the Pfam
family need not be orthologues (share the same function)—this can be evaluated
by mapping the neighbors to the SSN for the Pfam family using the
spreadsheet/custom node attribute files that can be downloaded. This node
attribute has a numerical value—a specific number of sequences or a range of
sequences can be selected with the Select Control Panel.
</div>

<div class="indent">
<b># of Pfam Neighbors</b>: The total number of neighbors in the Pfam family
that were found by the sequences in the SSN cluster. This value of
this node attribute will be greater than the value of the <b># Queries with PFAM
Neighbors</b> (previous) node attribute if a query found more than one neighbor in
the Pfam family. Again, the neighbors in the Pfam family need not be
orthologues (share the same function)— this can be evaluated by mapping the
neighbors to the SSN for the Pfam family using the spreadsheet/custom node
attribute files that can be downloaded. This node attribute has a numerical
value—a specific number of neighbors or a range of neighbors can be selected
with the Select Control Panel.
</div>

<div class="indent">
<b>Query-Neighbor Accessions</b>: A list of information about the
query-neighbor pais in the Pfam family in the following format:

<p class="indentall">Query ID:Neighbor ID:EC#:Neighbor PDB:Closest PDB:PDB-E-value:Status</p>

where

<ul>
<li>"Query ID" is the query UniProt accession ID,</li>
<li>"Neighbor ID" is the neighbor UniProt accession ID,</li>
<li>"EC#" is the E.C. number, if any, assigned to the neighbor in the UniProt database,</li>
<li>"Neighbor PDB" is the Protein Databank (PDB) identifier for the neighbor if one is available,</li>
<li>"ClosestPDB" is the Protein Databank (PDB) identifier for the most similar sequence to the neighbor with a structure in the PDB database,</li>
<li>"PDB-E-value" is the BLAST e-value for the neighbor-ClosestPDB pair, and</li>
<li>"Status" (Reviewed/Unreviewed) reports if the in-vitro activity of the neighbor has been reviewed by SwissProt.</li>
</ul>

This node
attribute is text—strings of characters can be selected with the Select Control
Panel. By right clicking on the node attribute, the entries can be copied and
pasted into Excel or a text file for further analyses. In Excel, the
colon-delimited entries can be easily separated into separate columns.
</div>

<div class="indent">
<b>Query-Neighbor Arrangement</b>: A list of genome context information for the
neighbors in the Pfam family in the following format:

<p class="indentall">Query ID:normal/complement:Neighbor ID:normal/complement:Distance</p>

 where 

<ul>
<li>"Query ID" is the query UniProt accession ID,</li>
<li>"normal/complement" is the direction of transcription of the gene encoding the query (from the ENA sequence file),</li>
<li>"Neighbor ID" is the neighbor UniProt accession ID,</li>
<li>"normal/complement" is the direction of transcription of the gene encoding the query (from the ENA sequence file), and</li>
<li>"Distance" is the distance in orfs between the genes encoding the query and neighbor.</li>
</ul>

This node attribute is text—strings of
characters can be selected with the Select Control Panel. By right clicking on
the node attribute, the entries can be copied and pasted into Excel or a text
file for further analyses. In Excel, the colon-delimited entries can be easily
separated into separate columns.
</div>

<div class="indent">
<b>Average Distance</b>: The average of the absolute values of the distances
between the queries in the cluster and their neighbors in the Pfam family. This
node attribute has a numerical value—a specific distance or a range of
distances can be selected with the Select Control Panel.
</div>

<div class="indent">
<b>Median Distance</b>: The median value of the absolute values of the
distances between the queries in the cluster and their neighbors in the Pfam
family This node attribute has a numerical value—a specific distance or a range
of distances can be selected with the Select Control Panel.
</div>

<div class="indent">
<b>Co-occurrence</b>: The decimal value of the ratio of the number of queries
that found neighbors in the Pfam family to the number of queriable sequences in
the cluster. This node attribute has a numerical value—a specific co-occurrence
or a range of co-occurrences can be selected with the Select Control Panel.
</div>

<div class="indent">
<b>Co-occurrence Ratio</b>: The ratio of the number of queries that found
neighbors in the Pfam family to the number of queriable sequences in the
cluster. This node attribute is text—strings of characters can be selected with
the Select Control Panel.
</div>

<div class="indent">
<b>Node.fillColor</b>: the hexadecimal number of the unique color assigned to
each cluster in the input SSN (singletons are not included). This number is
used by the pass-through mapping "Fill Color" style of Cytoscape to color the
nodes in the network. This node attribute is text—strings of characters can be
selected with the Select Control Panel.
</div>

<div class="indent">
<b>Node.shape</b>: ellipse, diamond, triangle, or square (explained above; used by
Cytoscape but can be used in searches to select hub-nodes) This node attribute
is text—strings of characters can be selected with the Select Control Panel.
</div>

<div class="indent">
<b>Node.size</b>: calculated as (<b>Co-occurrence</b> * 100) is used by Cytoscape to
draw the node. This node attribute is text—strings of characters can be
selected with the Select Control Panel.
</div>

<div class="indentall">
    <b>Pfam</b>: empty (a hub-node attribute)<br>
    <b>Pfam description</b>: empty (a hub-node attribute)<br>
    <b>Hub Average and Median Distance</b>: empty (a hub-node attribute)<br>
    <b>Hub Co-occurrence and Co-occurrence Ratio</b>: empty (a hub-node attribute)
</div>


<h2>Tabular Summary of Node Attributes</h2>

<a name="coloredssn"></a>
<h3>Colored SSN</h3>

<table class="pretty">
<thead>
<th>Node Attribute</th>
<th>Description - Options A, B, C with FASTA header reading, D</th>
</thead>
<tbody>
<tr>
    <td>Name</td>
    <td>Full network - UniProt accession; Rep Node network - UniProt accession for the longest sequence in the representative node (seed sequence for CD-Hit)</td>
</tr>
<tr>
    <td>Shared name</td>
    <td>Full network - UniProt accession; Rep Node network - UniProt accession for the longest sequence in the representative node (seed sequence for CD-Hit)</td>
</tr>
<tr>
    <td>Number of IDs in Rep Node1</td>
    <td>Number of UniProt IDs in the representative node</td>
</tr>
<tr>
    <td>List of IDs in Rep Node1</td>
    <td>List of UniProt IDs in the representative node</td>
</tr>
<tr>
    <td>Sequence Source</td>
    <td>Options B, C, and D, “USER” if from user-supplied file, “FAMILY” if from user-specified Pfam/InterPro family, “USER+FAMILY” if from both</td>
</tr>
<tr>
    <td>Query IDs</td>
    <td>Option C with FASTA header reading and Option D, Input Query ID(s) that identified a UniProt match </td>
</tr>
<tr>
    <td>Other IDs</td>
    <td>Option C with FASTA header reading, additional IDs in headers for a FASTA sequence that did not identify a UniProt match (NCBI BLAST files) </td>
</tr>
<tr>
    <td>Cluster Number</td>
    <td>Number assigned to cluster, in order of decreasing number of sequences in the clusters (“999999” for singletons)</td>
</tr>
<tr>
    <td>Cluster Sequence Count</td>
    <td>Number of sequences in the cluster</td>
</tr>
<tr>
    <td>Node.fillColor</td>
    <td>Unique color assigned to cluster, in hexadecimal</td>
</tr>
<tr>
    <td>Present in ENA Database?</td>
    <td>“true” if UniProt ID was found in an ENA file (see ENA Database Genome ID); otherwise “false”</td>
</tr>
<tr>
    <td>Genome Neighbors in ENA Database?</td>
    <td>“true” if ENA file has sequences for query plus neighbors; “false” if ENA file has no neighbors; “n/a” if not present in ENA database</td>
</tr>
<tr>
    <td>ENA Database Genome ID</td>
    <td>ENA file used to obtain genome neighbors</td>
</tr>
<tr>
    <td>Organism</td>
    <td>organism genus/genera and species, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Taxonomy ID</td>
    <td>NCBI taxonomy identifier(s), from UniProt </td>
</tr>
<tr>
    <td>UniProt Annotation Status</td>
    <td>SwissProt - manually annotated; TrEMBL - automatically annotated; from UniProt</td>
</tr>
<tr>
    <td>Description</td>
    <td>protein name(s)/annotation(s), from UniProtKB</td>
</tr>
<tr>
    <td>SwissProt Description</td>
    <td>protein name(s)/annotation(s), from UniProtKB for SwissProt reviewed entries</td>
</tr>
<tr>
    <td>Sequence Length</td>
    <td>number(s) of amino acid residues, from UniProt</td>
</tr>
<tr>
    <td>Gene name</td>
    <td>gene name(s)</td>
</tr>
<tr>
    <td>NCBI IDs</td>
    <td>RefSeq/GenBank IDs and GI numbers, from UniProt idmapping</td>
</tr>
<tr>
    <td>Superkingdom</td>
    <td>domain of life of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Kingdom</td>
    <td>kingdom of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Phylum</td>
    <td>Phylogenetic phylum of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Class</td>
    <td>Phylogenetic class of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Order</td>
    <td>Phylogenetic order of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Family</td>
    <td>Phylogenetic family of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Genus</td>
    <td>Phylogenetic genus of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Species</td>
    <td>Phylogenetic species of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>EC</td>
    <td>EC number, from UniProt</td>
</tr>
<tr>
    <td>PFAM</td>
    <td>Pfam family, from UniProt</td>
</tr>
<tr>
    <td>IPRO</td>
    <td>InterPro family, from UniProt</td>
</tr>
<tr>
    <td>PDB</td>
    <td>Protein Data Bank entry, from UniProt</td>
</tr>
<tr>
    <td>BRENDA ID</td>
    <td>BRENDA Database ID, from UniProt</td>
</tr>
<tr>
    <td>CAZY Name</td>
    <td>Carbohydrate-Active enZYmes (CAZy) family name(s), from UniProt</td>
</tr>
<tr>
    <td>GO Term</td>
    <td>Gene Ontology classification(s), from UniProt</td>
</tr>
<tr>
    <td>KEGG ID</td>
    <td>KEGG Database ID, from UniProt</td>
</tr>
<tr>
    <td>PATRIC ID</td>
    <td>PATRIC Database ID, from UniProt</td>
</tr>
<tr>
    <td>STRING ID</td>
    <td>STRING Database ID, from UniProt</td>
</tr>
<tr>
    <td>HMP Body Site</td>
    <td>location(s) of organism(s) in/on the body, if human microbiome organism, spreadsheet from HMP </td>
</tr>
<tr>
    <td>HMP Oxygen</td>
    <td>oxygen requirement(s), if human microbiome organism, spreadsheet from HMP</td>
</tr>
<tr>
    <td>P01 gDNA</td>
    <td>availability of gDNA(s) at EFI Protein Core, in-house</td>
</tr>
</tbody>
</table>
<div>1 - These attributes are present only in Rep Node Networks </div>


<br><br>
<table class="pretty">
<thead>
<th>Node Attribute</th>
<th>Description - Option C without FASTA header reading</th>
</thead>
<tbody>
<tr>
    <td>Name</td>
    <td>zzznnn, where nnn = number of the sequence in FASTA file</td>
</tr>
<tr>
    <td>Shared Name</td>
    <td>zzznnn, where nnn = number of the sequence in FASTA file</td>
</tr>
<tr>
    <td>Description</td>
    <td>FASTA Header </td>
</tr>
<tr>
    <td>Sequence Length</td>
    <td>Length of sequence in FASTA entry</td>
</tr>
<tr>
    <td>Sequence Source</td>
    <td>“USER” if from user-supplied file, “FAMILY” if from user-specified Pfam/InterPro family, “USER+FAMILY” if from both</td>
</tr>
<tr>
    <td>Cluster Number</td>
    <td>Number assigned to cluster, in order of decreasing number of sequences in the clusters (“999999” for singletons)</td>
</tr>
<tr>
    <td>Cluster Sequence Count</td>
    <td>Number of sequences in the cluster</td>
</tr>
<tr>
    <td>Node.fillColor</td>
    <td>Unique color assigned to cluster, in hexadecimal</td>
</tr>
<tr>
    <td>Present in ENA Database?</td>
    <td>“false”</td>
</tr>
<tr>
    <td>Genome Neighbors in ENA Database?</td>
    <td>“n/a” </td>
</tr>
<tr>
    <td>ENA Database Genome ID</td>
    <td>none</td>
</tr>
</tbody>
</table>



<a name="fmt1tabular"></a>
<h3>SSN Cluster Hub-Nodes and Pfam Family Spoke-Nodes</h3>

<table class="pretty">
<thead>
<th>Node Attribute</th>
<th>Description - SSN cluster hub-nodes</th>
</thead>
<tbody>
<tr>
    <td>Shared name</td>
    <td>Input SSN cluster number</td>
</tr>
<tr>
    <td>Name</td>
    <td>Input SSN cluster number</td>
</tr>
<tr>
    <td>Cluster Number</td>
    <td>Input SSN cluster number</td>
</tr>
<tr>
    <td># of Sequences in SSN Cluster</td>
    <td>Total number of sequences in SSN cluster</td>
</tr>
<tr>
    <td># of Sequences in SSN Cluster with Neighbors</td>
    <td>Number of sequences in SSN cluster with neighbors (queriable sequences)</td>
</tr>
<tr>
    <td>Hub Queries with Pfam Neighbors</td>
    <td>Summary of number of queriable sequences with a neighbor in the Pfam family</td>
</tr>
<tr>
    <td>Hub Pfam Neighbors</td>
    <td>Summary of the total # of Pfam neighbors found by the queriable sequences</td>
</tr>
<tr>
    <td>Hub Average and Median Distances</td>
    <td>Summary of average and median distances between the query and neighbors in each Pfam family</td>
</tr>
<tr>
    <td>Hub Co-occurrence and Ratio</td>
    <td>Summary of the query-neighbor co-occurrence (decimal value) and ratio (fraction) for each Pfam family</td>
</tr>
<tr>
    <td>Node.fillColor</td>
    <td>Hexadecimal color for the SSN cluster in the colored SSN, used by Cytoscape</td>
</tr>
<tr>
    <td>Node.shape</td>
    <td>"hexagon", used by Cytoscape</td>
</tr>
<tr>
    <td>Node Size</td>
    <td>"70.0", used by Cytoscape</td>
</tr>
</tbody>
</table>

<br><br>

<table class="pretty">
<thead>
<th>Node Attribute</th>
<th>Description - Pfam family spoke-nodes</th>
</thead>
<tbody>
<tr>
    <td>Shared name</td>
    <td>Pfam family short name</td>
</tr>
<tr>
    <td>Name</td>
    <td>Pfam family short name</td>
</tr>
<tr>
    <td>SSN Cluster Number</td>
    <td>SSN Cluster that found neighbors in the Pfam family</td>
</tr>
<tr>
    <td>Pfam</td>
    <td>Pfam family number (PFnnnnn)</td>
</tr>
<tr>
    <td>Pfam description</td>
    <td>Pfam family description</td>
</tr>
<tr>
    <td># of Queries with Pfam Neighbors</td>
    <td>Number of queriable sequences with a neighbor in the Pfam family</td>
</tr>
<tr>
    <td># of Pfam Neighbors</td>
    <td>Number of Pfam neighbors found by the queriable sequences</td>
</tr>
<tr>
    <td>Query-Accessions</td>
    <td>List of SSN cluster queries that found neighbors in the Pfam family</td>
</tr>
<tr>
    <td>Query-Neighbor Accessions</td>
    <td>Information about query-neighbor pairs in the Pfam family</td>
</tr>
<tr>
    <td>Query-Neighbor Arrangement</td>
    <td>Genome context information for the query-neighbor pairs in the Pfam family</td>
</tr>
<tr>
    <td>Average Distance</td>
    <td>Average distance (in ORFs) between the SSN cluster queries and Pfam neighbors</td>
</tr>
<tr>
    <td>Median Distance</td>
    <td>Median distance (in ORFs) between the SSN cluster queries and Pfam neighbors</td>
</tr>
<tr>
    <td>Co-occurrence</td>
    <td>Decimal value of ratio of queries that found neighbors to queriable sequences</td>
</tr>
<tr>
    <td>Co-occurrence Ratio</td>
    <td>Ratio of queries that found neighbors to queriable sequences</td>
</tr>
<tr>
    <td>Node.fillColor</td>
    <td>#EEEEEE, grey in hexadecimal, used by Cytoscape</td>
</tr>
<tr>
    <td>Node.shape</td>
    <td>"ellipse", "diamond", or "square"; explained in on-line tutorial, used by Cytoscape</td>
</tr>
<tr>
    <td>Node.size</td>
    <td>Co-occurrence * 100, used by Cytoscape </td>
</tr>
</tbody>
</table>




<a name="fmt2tabular"></a>
<h3>Pfam Family Hub-Nodes and SSN Cluster Spoke-Nodes</h3>

<table class="pretty">
<thead>
<th>Node Attribute</th>
<th>Description - Pfam family hub-nodes</th>
</thead>
<tbody>
<tr>
    <td>Shared name</td>
    <td>Pfam family short name</td>
</tr>
<tr>
    <td>Name</td>
    <td>Pfam family short name</td>
</tr>
<tr>
    <td>Pfam</td>
    <td>Pfam family number (PFnnnnn)</td>
</tr>
<tr>
    <td>Pfam description</td>
    <td>Pfam family description</td>
</tr>
<tr>
    <td># of Sequences in SSN Cluster</td>
    <td>Total number of sequences in SSN cluster</td>
</tr>
<tr>
    <td># of Sequences in SSN Cluster with Neighbors</td>
    <td>Number of sequences in SSN cluster with neighbors (queriable sequences)</td>
</tr>
<tr>
    <td># of Queries with Pfam Neighbors</td>
    <td>Number of queriable sequences with a neighbor in the Pfam family</td>
</tr>
<tr>
    <td># of Pfam Neighbors</td>
    <td>Number of Pfam neighbors found by the queriable sequences</td>
</tr>
<tr>
    <td>Query-Neighbor Accessions</td>
    <td>Information about query-neighbor pairs in the Pfam family</td>
</tr>
<tr>
    <td>Query-Neighbor Arrangement</td>
    <td>Genome context information for the query-neighbor pairs in the Pfam family</td>
</tr>
<tr>
    <td>Hub Average and Median Distances</td>
    <td>Summary of average and median distances between the query and neighbors </td>
</tr>
<tr>
    <td>Hub Co-occurrence and Ratio</td>
    <td>Summary of the query-Pfam family co-occurrence (decimal value) and ratio (fraction)</td>
</tr>
<tr>
    <td>Node.fillColor</td>
    <td>“#FFFFFF”, white in hexadecimal, used by Cytoscape</td>
</tr>
<tr>
    <td>Node.shape</td>
    <td>“hexagon”, used by Cytoscape</td>
</tr>
<tr>
    <td>Node.size</td>
    <td>“70.0”, used by Cytoscape</td>
</tr>
</tbody>
</table>

<br><br>

<table class="pretty">
<thead>
<th>Node Attribute</th>
<th>Description - SSN cluster spoke-nodes</th>
</thead>
<tbody>
<tr>
    <td>Shared name</td>
    <td>Input SSN cluster number</td>
</tr>
<tr>
    <td>Name</td>
    <td>Input SSN cluster number</td>
</tr>
<tr>
    <td>Cluster Number</td>
    <td>Input SSN cluster number</td>
</tr>
<tr>
    <td># of Sequences in SSN Cluster</td>
    <td>Total number of sequences in SSN cluster</td>
</tr>
<tr>
    <td># of Sequences in SSN Cluster with Neighbors</td>
    <td>Number of sequences in SSN cluster with neighbors (queriable sequences)</td>
</tr>
<tr>
    <td># of Queries with Pfam Neighbors</td>
    <td>Number of queriable sequences with a neighbor in the Pfam family</td>
</tr>
<tr>
    <td># of Pfam Neighbors</td>
    <td>Number of Pfam neighbors found by the queriable sequences</td>
</tr>
<tr>
    <td>Query-Accessions</td>
    <td>List of queries in each SSN cluster that found neigbhors in the Pfam family</td>
</tr>
<tr>
    <td>Query-Neighbor Accessions</td>
    <td>Information about query-neighbor pairs in the Pfam family</td>
</tr>
<tr>
    <td>Query-Neighbor Arrangement</td>
    <td>Genome context information for the query-neighbor pairs in the Pfam family</td>
</tr>
<tr>
    <td>Average Distance</td>
    <td>Average distance (in ORFs) between the SSN cluster queries and Pfam neighbors</td>
</tr>
<tr>
    <td>Median Distance</td>
    <td>Median distance (in ORFs) between the SSN cluster queries and Pfam neighbors</td>
</tr>
<tr>
    <td>Co-occurrence</td>
    <td>Decimal value of ratio of queries that found neighbors to queriable sequences</td>
</tr>
<tr>
    <td>Co-occurrence Ratio</td>
    <td>Ratio of queries that found neighbors to queriable sequences</td>
</tr>
<tr>
    <td>Node.fillColor</td>
    <td>Hexadecimal color for the SSN cluster in the colored SSN, used by Cytoscape</td>
</tr>
<tr>
    <td>Node.shape</td>
    <td>“ellipse”, “diamond”, or “square”; explained in on-line tutorial, used by Cytoscape</td>
</tr>
<tr>
    <td>Node.size</td>
    <td>Co-occurrence * 100, used by Cytoscape</td>
</tr>
</tbody>
</table>

</div>


<div class="tutorial_next">
<a href='tutorial_input.php'><button class="light">Continue Tutorial</button></a>
</div>


<?php require_once('inc/footer.inc.php'); ?>

