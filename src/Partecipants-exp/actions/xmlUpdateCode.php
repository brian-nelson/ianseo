<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

/****** Controller ******/
	$code=(isset($_REQUEST['code']) ? $_REQUEST['code'] : null);	// matricola del tizio
	$id=(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);	// id del tizio (mi serve per l'update di Entries)

	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : null);
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : null);

	if (!CheckTourSession() || is_null($code) || is_null($id) || is_null($row) || is_null($col)) {
		print get_text('CrackError');
		exit;
	}
	checkACL(AclParticipants, AclReadWrite, false);

	$tourId=$_SESSION['TourId'];

	$error=0;
	if (!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		$query = "UPDATE Entries SET EnCode=" . StrSafe_DB($code) . " WHERE EnId=" . StrSafe_DB($id) . " ";
		$rs=safe_w_sql($query);
		if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
			LogAccBoothQuerry("UPDATE Entries SET EnCode=" . StrSafe_DB($code) . " where $EnSelect");
		}
	} else {
		$error=1;
	}
/****** End Controller ******/

/****** Output ******/
	$xmlDoc=new DOMDocument('1.0',PageEncode);
		$xmlRoot=$xmlDoc->createElement('response');
		$xmlDoc->appendChild($xmlRoot);

		// Header
			$xmlHeader=$xmlDoc->createElement('header');
			$xmlRoot->appendChild($xmlHeader);

				$node=$xmlDoc->createElement('error',$error);
				$xmlHeader->appendChild($node);

				$node=$xmlDoc->createElement('row',intval($row));
				$xmlHeader->appendChild($node);

				$node=$xmlDoc->createElement('col',intval($col));
				$xmlHeader->appendChild($node);

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();
/****** End OUtput ******/
?>
