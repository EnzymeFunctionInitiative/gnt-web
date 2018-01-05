<?php
include_once("../libs/settings.class.inc.php");

if (!isset($TopLevelUrl))
    $TopLevelUrl = "http://efi.igb.illinois.edu/efi-gnt/";

$title = "Genome Neighborhood Networks Tool";
if (isset($GnnId))
    $title .= ": Job #$GnnId";

if (isset($Is404Page) && $Is404Page)
    $title = "Page Not Found";

if (isset($IsExpiredPage) && $IsExpiredPage)
    $title = "Expired Job";

$IsLoggedIn = false;
if (settings::is_recent_jobs_enabled() && user_jobs::has_token_cookie())
    $IsLoggedIn = user_jobs::get_email_from_token($db, user_jobs::get_user_token());

include("../../main/html/inc/global_login_button.inc.php");

?>

<!doctype html>
<head>
    <link rel="stylesheet" type="text/css" href="jquery-ui-1.12.1/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="/css/shared.css">
    <link rel="stylesheet" type="text/css" href="/css/global.css">
    <link rel="stylesheet" type="text/css" href="css/gnt.css">
    <link rel="stylesheet" type="text/css" href="css/tabs.css">
    <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<?php if (isset($TUTORIAL) && $TUTORIAL) { ?>
    <link rel="stylesheet" type="text/css" href="css/tutorial.css">
<?php } ?>
    <link rel="shortcut icon" href="images/favicon_efi.ico" type="image/x-icon">
    <title><?php echo $title; ?></title>

    <script src="/js/login.js" type="text/javascript"></script>
    <script src="js/main.js" type="text/javascript"></script>
    <script src="js/upload.js" type="text/javascript"></script>
    <script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="jquery-ui-1.12.1/jquery-ui.js" type="text/javascript"></script>
</head>

<body>
<?php
include("../../main/html/inc/global_nav.inc.php");
?>
    <div id="container">
        <div class="header">
            <div class="header_logo">
                <a href="<?php echo $TopLevelUrl; ?>"><img src="images/efi-gnn_logo.png" width="250" height="75" alt="Enzyme Function Initiative Logo"></a>
                <a href="http://enzymefunction.org"><img src="images/efi_logo.png" class="efi_logo_small" width="80" height="24" alt="Enzyme Function Initiative Logo"></a>
            </div>
        </div>

        <div class="content_holder">
            <h1 class="ruled">Genome Neighborhood Network Tool</h1>
<?php if (settings::is_beta_release()) { ?>
            <div class="beta"><h4>BETA</h4></div>
<?php } ?>

