<?php

if (!$message) {
    $message = "That page does not exist.";
}


if (!isset($IsPretty)) {
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">   
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>File Not Found</title>

        <style>
            .error-icon {
                font: 6em bold;
                float: left;
                margin-left: 5%;
                margin-right: 30px;
            }

            .error-title {
                font: 4em bold;
                padding-top: 20px;
                margin-top: 5%;
                
            }

            .error-text {
                font-size: 1.2em;
            }
        </style>
    </head>

    <body>
<?php
}
?>
        <div id="error-message" style="margin-bottom: 50px">
            <div style="font: 6em bold;float: left;margin-left: 5%;margin-right: 30px">:-(</div>
            <div style="font: 4em bold;padding-top: 20px;margin-top: 5%;">Does not compute...</div>
            <div style="font-size: 1.2em;"><?php echo $message; ?></div>
        </div>
<?php
if (!isset($IsPretty)) {
?>
    </body>
</html>
<?php
}
?>


