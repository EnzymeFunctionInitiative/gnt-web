<?php

$feedbackMessage = "Need help or have suggestions or comments?   Please click here.";

if ((isset($Is404Page) && $Is404Page) || (isset($IsExpiredPage) && $IsExpiredPage)) {
    echo "</div>";
    if (isset($Is404Page) && $Is404Page)
        $feedbackMessage = "Please click here to report this.";
}

?>

            <p class="suggestions">
                <a href="http://enzymefunction.org/content/sequence-similarity-networks-tool-feedback" target="_blank"><?php echo $feedbackMessage; ?></a>
            </p>
        </div> <!-- content_holder -->

        <div class="footer_container">
            <div class="footer">
                <div class="address inline">
                    Enzyme Function Initiative |
                    1206 W. Gregory Drive Urbana, IL 61801
                    &nbsp;|&nbsp; <a href="mailto:efi@enzymefunction.org">efi@enzymefunction.org</a>
                </div>
                <div class="footer_logo inline">
                    <a href="http://www.nigms.nih.gov/" target="_blank"><img alt="NIGMS" src="images/nighnew.png" style="width: 201px; height: 30px;"></a>
                </div>
            </div>
        </div>
    </div> <!-- container -->

    <!-- Bootstrap Core JavaScript -->
    <script src="/bs/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/bs/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>

