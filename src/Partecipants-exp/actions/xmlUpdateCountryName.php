<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');

/****** Controller ******/
	$countryCode=(isset($_REQUEST['country_code']) ? $_REQUEST['country_code'] : null);
	$countryName=(isset($_REQUEST['country_name']) ? AdjustCaseTitle(stripslashes($_REQUEST['country_name'])) : null);

	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : null);
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : null);

	if (is_null($countryCode) || is_null($countryName) || is_null($row) || is_null($col) ||  !CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$tourId=StrSafe_DB($_SESSION['TourId']);

	$error=1;

	$newName=0;
	if (IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		if($countryCode) {
			$query = "SELECT CoId, CoName FROM Countries WHERE CoCode=" . StrSafe_DB($countryCode) . " AND CoTournament=" . $tourId ;
			$rs=safe_r_sql($query);
			if($r=safe_fetch($rs)) $countryName=$r->CoName;
		}
	} else {
		if ($countryName and $countryCode) {
			$query
				= "SELECT "
					. "CoId "
				. "FROM "
					. "Countries "
				. "WHERE "
					. "CoCode=" . StrSafe_DB($countryCode) . " AND CoTournament=" . $tourId . " ";
			$rs=safe_r_sql($query);
//print $query;exit;
			if (safe_num_rows($rs)==1) {
				$error=0;
				$myRow=safe_fetch($rs);

				$query
					= "UPDATE "
						. "Countries "
					. "SET "
						. "CoName=" . StrSafe_DB(AdjustCaseTitle($countryName)) . " "
					. "WHERE "
						. "CoCode=" . StrSafe_DB($countryCode);
				$rs=safe_w_sql($query . " AND CoTournament=" . $tourId );
				//print $query;exit;
				if (safe_w_affected_rows()) $newName=1;
				LogAccBoothQuerry($query . " AND CoTournament=§TOCODETOID§", $_SESSION['TourCode']);
			}
		}
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

	// Country
		$xmlCountry=$xmlDoc->createElement('country');
		$xmlRoot->appendChild($xmlCountry);

			$node=$xmlDoc->createElement('new_name',$newName);
			$xmlCountry->appendChild($node);
			$xmlCountry->appendChild($xmlDoc->createElement('value',$countryName));

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();
/****** End OUtput ******/

?>