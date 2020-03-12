<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/Fun_PrintOuts.php');
	require_once('Common/XmlCreationFunctions.php');

	CheckTourSession(true);

	$ToFit=(isset($_REQUEST['ToFitarco']) ? $_REQUEST['ToFitarco'] : null);

	$XmlDoc = XmlCreateQualTeam();

	if (is_null($ToFit))
	{
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Content-type: text/xml; charset=' . PageEncode);
		echo $XmlDoc->SaveXML();
	}
	else
		$XmlDoc->save($ToFit);