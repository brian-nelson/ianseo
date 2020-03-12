<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

/****** Controller ******/
	$id=(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);
	$ath=(isset($_REQUEST['ath']) ? $_REQUEST['ath'] : null);

	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : null);
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : null);


	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	if (is_null($id) || is_null($ath) || is_null($row) || is_null($col))
	{
		print get_text('CrackError');
		exit;
	}


	$error = 0;

	$tourId=StrSafe_DB($_SESSION['TourId']);

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		$query = "UPDATE Entries SET EnAthlete=" . StrSafe_DB($ath) . ", EnClass='', EnAgeClass='' ";
		$rs=safe_w_sql($query. ' where EnId='. StrSafe_DB($id));
		if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
			LogAccBoothQuerry($query . " where $EnSelect");
		}
	}
	else
		$error=1;
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



			$xmlRoot->appendChild($node);

	header('Content-type: text/xml; charset=' . PageEncode);
	header('Cache-Control: no-store, no-cache, must-revalidate');
	print $xmlDoc->saveXML();
/****** End OUtput ******/
?>