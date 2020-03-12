<?php
require_once('./config.php');

$NOSTYLE = true;
$ONLOAD = ' style="margin:0px; padding:0px;"';
$PAGE_TITLE = get_text('MenuLM_TV Output');
$JS_SCRIPT=array(
		'<script type="text/javascript" src="./Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="output.js"></script>',
);

include('Common/Templates/head-caspar.php');

echo '<iframe id="rot" width="100%" height="100%" frameborder="0" src="http://localhost/TV/Rot/?Rule=1&Tour=a"></iframe>'; 


include('Common/Templates/tail-min.php');
?>