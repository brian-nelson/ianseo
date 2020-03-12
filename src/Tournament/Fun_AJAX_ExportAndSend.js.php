<?php
/*
													- Fun_AJAX_ExportAndSend.js.php -
	Contiene le funzioni ajax usate da ExportAndSend.php
*/
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/ajax/ObjXMLHttpRequest.js');

	print "var Msg_CheckRefMail ='" . get_text('CheckRefMail','Tournament') . "';";
	print "var Msg_MakingFile ='" . get_text('MakingFile','Tournament') . "';";
	print "var Msg_Ok='" . get_text('CmdOk') . "';";
	print "var Msg_Error='" . get_text('Error') . "';";
	print "var WebDir='" . $CFG->ROOT_DIR . "';";

	require_once('Tournament/Fun_AJAX_ExportAndSend.js');
?>