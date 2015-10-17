<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

/****** Controller ******/
	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$error = 0;

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{

		$tourId=StrSafe_DB($_SESSION['TourId']);

		$t=safe_r_sql("select NULLIF(ToIocCode,'') as nocCode from Tournament WHERE ToId={$_SESSION['TourId']}");
		$u=safe_fetch($t);
		$nocCode=$u->nocCode;
		
	// aggiungo la riga in Entries
		$query
			= "INSERT INTO Entries (EnTournament,EnIocCode,EnDivision,EnClass,EnSubClass,EnAgeClass,EnCountry,EnCtrlCode,EnCode,EnName,EnFirstName,EnAthlete,EnSex,EnWChair,EnSitting,EnIndClEvent,EnTeamClEvent,EnIndFEvent,EnTeamFEvent,EnStatus) "
			. "VALUES("
			. $tourId . ","		// EnTournament
			. "'$nocCode',"
			. "'--',"			// EnDivision
			. "'--',"			// EnClass
			. "'00',"			// EnSubClass
			. "'--',"			// EnAgeClass
			. "'0',"			// EnCountry
			. "'',"				// EnCtrlCode
			. "'',"				// EnCode
			. "'',"				// EnName
			. "'',"				// EnFirstName
			. "'1',"			// EnAthlete
			. "'0',"			// EnSex
			. "'0',"			// EnWChair
			. "'0',"			// EnSitting
			. "'1',"			// EnIndClEvent
			. "'1',"			// EnTeamClEvent
			. "'1',"			// EnIndFEvent
			. "'1',"			// EnTeamFEvent
			. "'0'"				// EnStatus
			. ") ";
		$rs=safe_w_sql($query);

		$newId=0;

		if (safe_w_affected_rows()==1)
		{
		// Aggiungo la riga a Qualifications
			$newId=safe_w_last_id();

			$query
				= "INSERT INTO Qualifications (QuId,QuSession) "
				. "VALUES("
				. StrSafe_DB($newId) . ","
				. "0"
				. ") ";
			$rs=safe_w_sql($query);
		}
		else
			$error=1;

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

		$node=$xmlDoc->createElement('new_id',$newId);
		$xmlRoot->appendChild($node);

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();
/****** End OUtput ******/
?>