<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	$error=0;

	$query = "SELECT DivId, DivDescription FROM Divisions 
		where DivTournament={$_SESSION['TourId']}
		ORDER BY DivViewOrder";
	$rs=safe_r_SQL($query);
	$subclasses=array();

	while ($myRow=safe_fetch($rs)) {
		$subclasses[] = $myRow->DivId . ':::' . $myRow->DivDescription;
	}

	$xmlDoc=new DOMDocument('1.0',PageEncode);

	$xmlDoc->appendChild($xmlRoot=$xmlDoc->createElement('response'));

	$xmlRoot->appendChild($xmlDoc->createElement('error',$error));
	$xmlRoot->appendChild($xmlItems=$xmlDoc->createElement('items'));

	$xmlItems->appendChild($xmlDoc->createCDATASection(implode('---', $subclasses)));

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();