<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');

/****** Controller ******/
	$id=(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);
	$countryCode=(isset($_REQUEST['country_code']) ? $_REQUEST['country_code'] : null);

	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : null);
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : null);

	if (is_null($id) || is_null($countryCode) || is_null($row) || is_null($col) ||  !CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$tourId=StrSafe_DB($_SESSION['TourId']);

	$error=0;

	$coId=0;
	$coName='';
	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		if (strlen($countryCode)>0)
		{
			$query
				= "SELECT "
					. "CoId,CoName "
				. "FROM "
					. "Countries "
				. "WHERE "
					. "CoCode=" . StrSafe_DB($countryCode) . " AND CoTournament=" . $tourId . " ";
			$rs=safe_r_sql($query);

			if ($rs)
			{
				$count=safe_num_rows($rs);

				$countryCode=mb_convert_case($countryCode, MB_CASE_UPPER, "UTF-8");
			// se non c'è lo aggiungo
				if ($count==0) {
					$query = "INSERT INTO Countries set CoCode=".StrSafe_DB($countryCode).", CoTournament=";
					$rs=safe_w_sql($query.$tourId);
					$coId=safe_w_last_id();
					LogAccBoothQuerry($query.'§TOCODETOID§');
				} elseif ($count==1) {
					$myRow=safe_fetch($rs);

					//$coName=$myRow->CountryName;
					$coName=AdjustCaseTitle($myRow->CoName);
					$coId=$myRow->CoId;
				}
				else
					$error=1;
			}
			else
				$error=1;
		}
	}
	else
		$error=1;

	if ($error==0) {
		$query = "UPDATE Entries SET EnCountry3=" . StrSafe_DB($coId) . " WHERE EnId=" . StrSafe_DB($id) . " ";
		$rs=safe_w_sql($query);
		if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
			LogAccBoothQuerry("UPDATE Entries SET EnCountry3=(select CoId from Countries were CoCode='$countryCode' and CoTournament=§TOCODETOID§) where $EnSelect");
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

			$node=$xmlDoc->createElement('country_id',$coId);
			$xmlCountry->appendChild($node);

			$xmlCoCode=$xmlDoc->createElement('country_code');
			$xmlCountry->appendChild($xmlCoCode);
				$cdata=$xmlDoc->createCDATASection($countryCode);
				$xmlCoCode->appendChild($cdata);

			$xmlCoName=$xmlDoc->createElement('country_name');
			$xmlCountry->appendChild($xmlCoName);
				$cdata=$xmlDoc->createCDATASection($coName);
				$xmlCoName->appendChild($cdata);

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();
/****** End OUtput ******/
?>