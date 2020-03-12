<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');

$NOSTYLE=true;

include('Common/Templates/head-caspar.php');
echo '
    <div style="text-align: center;">
    <img src="'.$CFG->ROOT_DIR.'Common/Images/ianseo-logo.png" border="0">
    </div>

';

include('Common/Templates/tail-min.php');
