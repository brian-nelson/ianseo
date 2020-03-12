<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');

/****** Controller ******/
	$countryCode=(isset($_REQUEST['country_code']) ? $_REQUEST['country_code'] : null);

	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : null);
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : null);

	if (is_null($countryCode) || is_null($row) || is_null($col) ||  !CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	$tourId=StrSafe_DB($_SESSION['TourId']);

	$error=0;

	$coId=0;
	$coName='#';

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		if (strlen($countryCode)>0)
		{
			$query
				= "SELECT "
					. "CoId, CoName "
				. "FROM "
					. "Countries "
				. "WHERE "
					. "CoCode=" . StrSafe_DB($countryCode) . " AND CoTournament=" . $tourId . " ";
			$rs=safe_r_sql($query);
			//print $query;exit;
			if ($rs)
			{
			// se ce ne sono di più, piglio la prima che trovo...
				if (safe_num_rows($rs)>0)
				{
					$myRow=safe_fetch($rs);

					$coId=$myRow->CoId;
					$coName=AdjustCaseTitle((trim($myRow->CoName)!='' ? trim($myRow->CoName) : '#'));
				}
			}
			else
			{
				$error=1;
			}
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

	// Country
		$xmlCountry=$xmlDoc->createElement('country');
		$xmlRoot->appendChild($xmlCountry);

			$node=$xmlDoc->createElement('country_id',$coId);
			$xmlCountry->appendChild($node);

			$xmlCoName=$xmlDoc->createElement('country_name');
			$xmlCountry->appendChild($xmlCoName);
				$cdata=$xmlDoc->createCDATASection($coName);
				$xmlCoName->appendChild($cdata);

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();

/****** End OUtput ******/
?>