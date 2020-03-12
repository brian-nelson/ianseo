<?php
require_once('./config.php');

$PAGE_TITLE='NO ACCESS';

include('Common/Templates/head-min.php');

echo '<div id="NoAccess" align="center">';
echo  get_text('NoAccess');
echo '<br><img style="padding-top: 10px;" src="Common/Images/ianseo-logo.png" alt="IANSEO">';
echo '</div>';


include('Common/Templates/tail-min.php'); 
?>