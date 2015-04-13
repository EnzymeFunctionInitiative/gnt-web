<?php
require_once 'includes/tutorial_header.inc.php'; ?>

 <div class="content_nav">
	<?php require_once('includes/tutorial_nav.php'); ?>
  
  </div>
  <div class="content_content">
<p>
<p><br><b>EFI-GNT Input and Output</b></p>
<p><br>As mentioned previously in the tutorial, the GNN-building process has
been consolidated into a program that can be executed via a web
portal by the user and will run on a server housed within the
Institute for Genomic Biology at the University of Illinois at
Urbana-Champaign. Acceptable input is an xgmml file resulting from
the SSN-building web tool, <a href="http://efi.igb.illinois.edu/efi-est/">EFI-EST</a>,
or a network that has been manipulated and exported from <a href="http://www.cytoscape.org/download.html">Cytoscape</a>
(maximum size = 500 MB). This SSN may be a full network or a rep-node
network. The user then designates the number of neighbors to retrieve
from upstream and downstream of each query using the pull-down menu.
As with EFI-EST, the user also inputs an e-mail address to which an
email containing a link to the results will be sent.</p>

<p><br><img src='images/Tutorial_Figure3.jpeg' alt='Figure 3' width='580'></p>

<p><br>The EFI-GNT output is a pair of .xgmml files. The genome neighbor
network (GNN) and an updated version of the original SSN that is now
colored by cluster to correspond to the spoke nodes within the GNN. A
link from which both networks can be downloaded for a period of seven
days will be sent to the e-mail address provided. The networks will
be stored on the server for up to seven days.</p>

<p><br><img src='images/Tutorial_Figure4.jpeg' alt='Figure 4' width='580'></p>
        </div>
<div>
  <div> </div>
      </div>
    </div>

    <div class="clear"></div>

	</div>
    
    
    
    
    
    
   <!-- END: class="content_wide" -->
</div>

<p style='text-align:center;'><a href='tutorial_gnn_manipulation.php'><button class="css_btn_class">Continue Tutorial</button></a></p>

<?php require_once('includes/tutorial_footer.inc.php'); ?>
