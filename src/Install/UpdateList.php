<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	@include('Modules/IanseoTeam/IanseoFeatures/isIanseoTeam.php');
	
	include('FileList.php');
	$tmp = new FileList("/");
	$tmp->EscludeFiles('^(\.)');
//	$tmp->ApplyFilter('([.]php)$');
	
	$tmp->Load();
	
	$tmp->ShowSize(true);
	$tmp->ShowMD5(true);
//	echo "<pre>\n";
//	echo $tmp->TextList();
//	echo "</pre>\n";

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);
	echo $tmp->XML();
?>