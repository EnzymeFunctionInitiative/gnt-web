<?php
include_once("../libs/settings.class.inc.php");
?>

<!doctype html>
<head>
<!--<script src="includes/jquery-2.1.1.min.js" type="text/javascript"></script>-->
<link rel="stylesheet" type="text/css" href="css/efi_tool.css">
<link rel="stylesheet" type="text/css" href="css/popup.css">
<link rel="stylesheet" type="text/css" href="css/loader.css">
<link rel="stylesheet" type="text/css" href="css/multi-select.css">
<link rel="stylesheet" type="text/css" href="/css/shared.css">
<!--<link rel='stylesheet' type='text/css' href='css/upload.css'>-->
<link rel="shortcut icon" href="images/favicon_efi.ico" type="image/x-icon">
<title>Genome Neighborhood Networks Tool</title>

<script src='includes/main2.inc.js' type='text/javascript'></script>
<script src='includes/upload2.inc.js' type='text/javascript'></script>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
</head>

<body>

<h1>Sequnce Similarity Networks Tool</h1>
<div id="container">

	<div class="header_area">
		<div class="efi_logo">
			<a href="http://efi.igb.illinois.edu/efi-gnt/">
			<img src="images/efi-gnn_logo.png" width="250" height="75" alt="Enzyme Function Initiative Logo"></a><a href="http://enzymefunction.org">
			<img src="images/efi_logo.png" class="efi_logo_small" width="80" height="24" alt="Enzyme Function Initiative Logo"></a><div class="clear">
		</div>
	</div>

	<div class="clear"></div>
	<div class="public_topnav">
		<ul class="menu">
			<li class="leaf first">&nbsp;</li>
		</ul>
	</div>
	<div class="clear"></div>
</div>

<div class="content_holder">
	<div class="bottom">
		<div class="content_wide">
			<div class="clear"></div>
			<div class="content_2ndlevel">
				<div class="content_widecontent">

<h2>Genome Neighborhood Networks</h2>
<?php if (settings::is_beta_release()) { ?>
<center><h4><b><span style="color: red">BETA</span></b></h4></center>
<?php } ?>

