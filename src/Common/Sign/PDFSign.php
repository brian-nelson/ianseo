<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/pdf/SignPDF.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	
	$pdf = new SignPDF(get_text('Sign/guide-board','Tournament'),false);
	$pdf->init($_REQUEST['First'],$_REQUEST['Second']);
	
	$pdf->Make();
	
?>