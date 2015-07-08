<?php
require_once 'includes/tutorial_header.inc.php'; ?>

 <div class="content_nav">
	<?php require_once('includes/tutorial_nav.php'); ?>
  
  </div>
  <div class="content_content">
<p><br><b>Viewing and
Manipulating a GNN</b></p>
<p><br>GNN files most be viewed in Cytoscape 3.0 (or more recent) and are
best laid out using an Organic or Prefuse Force Directed layout.
These layouts place the most connected, and thus most commonly
occurring, GNN-clusters at the top of the layout. Opening both the
GNN and colored SSN in a single instance of Cytoscape allows fast
comparison between the two networks.</p>
<p><br><b>NOTE</b>
– in Cytoscape the automatic rendering and coloring of the
colorized SSN is size dependent. Cytoscape settings include a
“Threshold View” that needs to be adjusted in the following
manner in order to automatically view your colored SSN:</p>
<ol>
	<li>In any version 3.X, go to Edit -> Preferences -> Properties
	<li>With “cytoscape 3” selected in the pull-down menu at the top,
	scroll to the bottom of the Property list and select “viewThreshold”
	<li>Click “Modify” and insert 5 zeros to the end of the displayed
	number
	<li>Click “OK”
</ol>
<p><br>Restart Cytoscape (this should only need to be done once per version of
Cytoscape installed on your machine)</p>
<p><br>Generally, the full +/-10 neighbor GNN presents an overwhelming amount of
information. We found that it is substantially more useful to filter
GNN networks by some criteria, such as Node Cluster Number, before
further analysis. The Node Cluster Number refers to the numbers
assigned to each cluster of the original SSN input (SSN clusters are
also numbered in addition to being color coded). You can compare your
filtered GNN to the colored SNN output to determine at which cluster
you are looking. The following video shows how to filter by cluster
number (important notes appear at the bottom of the video; expand the video to full-screen for best resolution). 
</p>
<p><br>
<video width='640' height='352' controls>
<source type='video/mp4' src='http://enzymefunction.org/system/files/upload/Filtering_A_GNN_Video_Tutorial_v3.0.2.mp4'>
<source type='video/webm' src='http://enzymefunction.org/system/files/upload/Filtering_A_GNN_Video_Tutorial_v3.0.2.webm'>
</video>
</p>
<p><br>The result of filtering is a simplified network now containing only hubs
connected to the designated SSN cluster (in this example, the cyan
cluster 5). One can now analyze the genome neighborhood specific to
this SSN-cluster. Additional tutorials regarding the manipulation of
networks in Cytoscape can be found <a href="http://enzymefunction.org/resources/tutorials">here</a>.</p>
<p><br><img src='images/Tutorial_Figure5.jpeg' alt='Figure 3' width='580'>

<p><br><i>Figure 3. </i>A full GNN prepared with EFI-GNT for the Radical SAM family
[left] and a GNN that has been filtered for SSN-cluster 93
[right].</p>

<p><br>Additionally, the spoke length is arbitrary. For crowded GNN-clusters, feel free to
click+drag+drop overlapping spoke nodes until all are visible. 
</p>
<p><br><img src='images/Tutorial_Figure6.jpeg' alt='Figure 4' width='580'></p>
<p>
        <br>Figure 4. A crowded GNN-cluster can be manipulated to
remove overlapping nodes.
        </div>
<div>
  <div> </div>
      </div>
    </div>

    <div class="clear"></div>

	</div>
    
    
    
    
    
    
   <!-- END: class="content_wide" -->
</div>


<?php require_once('includes/tutorial_footer.inc.php'); ?>
