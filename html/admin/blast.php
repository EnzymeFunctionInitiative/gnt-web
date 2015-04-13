<?php

if (isset($_GET['blast'])) {
	echo wordwrap($_GET['blast'],75,"\n",true);
}
