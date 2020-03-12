<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');

/****** Controller ******/
	$id=(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);
	$session=(isset($_REQUEST['session']) ? $_REQUEST['session'] : null);

	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : null);
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : null);

	if (!CheckTourSession() || is_null($session) || is_null($id) || is_null($row) || is_null($col)) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$tourId=StrSafe_DB($_SESSION['TourId']);

	$error=0;

	$tooMany=0;
	$num4session=999;
	$msg=get_text('CmdOk');
	$resetTarget=0;
	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
	// Calcolo il numero di bersagli disponibili per la sessione scelta
		if ($session>0)
		{
			$ses=GetSessions('Q',false,array($session.'_Q'));
			$num4session=$ses[0]->SesTar4Session*$ses[0]->SesAth4Target;
		}

	// Mi serve la sessione attuale del tizio
		$query
			= "SELECT "
				. "QuSession "
			. "FROM "
				. "Qualifications "
			. "WHERE QuId=" . StrSafe_DB($id) . " ";
		$rs=safe_r_sql($query);

		$old=0;

		if ($rs)
		{
			if (safe_num_rows($rs)==1)
			{
				$myRow=safe_fetch($rs);
				$old=$myRow->QuSession;

				if ($session!=0 && $session!=$old)
				{
				// conto gli arcieri di $session sessione
					$query
						= "SELECT "
							. "COUNT(QuId) AS HowMany "
						. "FROM "
							. "Qualifications INNER JOIN Entries ON QuId=EnId AND EnTournament=" . $tourId . " "
						. "WHERE QuSession=" . StrSafe_DB($session) . " ";
					$rs=safe_r_sql($query);

					if ($rs)
					{
						if (safe_num_rows($rs)==1)
						{
							$myRow=safe_fetch($rs);

							if ($num4session<$myRow->HowMany+1)
							{
								$tooMany=1;
								$msg=get_text('NoMoreAth4Session','Tournament');
							}
						}
					}
				}

				if ($tooMany==0) {
					$query = "UPDATE Qualifications
						SET QuSession=" . StrSafe_DB($session) .  ", QuTimestamp=QuTimestamp
						WHERE QuId=" . StrSafe_DB($id) . " ";
					if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
						LogAccBoothQuerry("UPDATE Qualifications
							SET QuSession=" . StrSafe_DB($session) .  ", QuTimestamp=QuTimestamp
							WHERE QuId=(select EnId from Entries where $EnSelect)", $_SESSION['TourCode']);
					}

					$rs=safe_w_sql($query);

				// se la riga è stata aggiornata significa che la session è cambiata quindi annullo il target
					if (safe_w_affected_rows()) {
						safe_w_sql("update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where EnId='{$id}'");
						$query = "UPDATE Qualifications
							SET QuTargetNo='0', QuTarget=0, QuLetter='', QuBacknoPrinted=0, QuTimestamp=QuTimestamp
							WHERE QuId=" . StrSafe_DB($id) . " ";
						$rs=safe_w_sql($query);
						$resetTarget=1;
						LogAccBoothQuerry("UPDATE Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where $EnSelect", $_SESSION['TourCode']);
						LogAccBoothQuerry("UPDATE Qualifications
							SET QuTargetNo='0', QuTarget=0, QuLetter='', QuBacknoPrinted=0, QuTimestamp=QuTimestamp
							WHERE QuId=(select EnId from Entries where $EnSelect)", $_SESSION['TourCode']);
					}
				}
			}
			else
				$error=1;
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

				$node=$xmlDoc->createElement('row',intval($row));
				$xmlHeader->appendChild($node);

				$node=$xmlDoc->createElement('col',intval($col));
				$xmlHeader->appendChild($node);

		// dati
			$node=$xmlDoc->createElement('too_many',$tooMany);
			$xmlRoot->appendChild($node);

			$node=$xmlDoc->createElement('session',$session);
			$xmlRoot->appendChild($node);

			$node=$xmlDoc->createElement('old_session',$old);
			$xmlRoot->appendChild($node);

			$node=$xmlDoc->createElement('msg');
			$xmlRoot->appendChild($node);
				$cdata=$xmlDoc->createCDATASection($msg);
				$node->appendChild($cdata);

			$node=$xmlDoc->createElement('reset_target',$resetTarget);
			$xmlRoot->appendChild($node);

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();

/****** End OUtput ******/
?>