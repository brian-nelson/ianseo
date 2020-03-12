<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

/****** Controller ******/
	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$error = 1;

	$tourId=$_SESSION['TourId'];

	$id=(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);
	$session=(isset($_REQUEST['session']) ? $_REQUEST['session'] : null);
	$target=(isset($_REQUEST['target']) ? $_REQUEST['target'] : null);
	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : null);
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : null);

	$targetNo=$target;

	if (is_null($id) || is_null($session) || is_null($target) || is_null($row) || is_null($col))
		exit;

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		if (trim($target)=='') {
			$query = "UPDATE Qualifications
				SET QuTargetNo=" . StrSafe_DB($target) . ", QuTarget=SUBSTRING(QuTargetNo,2)+0, QuLetter=right(QuTargetNo, 1), QuTimestamp=QuTimestamp
				WHERE QuId=" . StrSafe_DB($id) . " ";

			if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
				LogAccBoothQuerry("UPDATE Qualifications
					SET QuTargetNo=" . StrSafe_DB($target) . ", QuTarget=SUBSTRING(QuTargetNo,2)+0, QuLetter=right(QuTargetNo, 1), QuTimestamp=QuTimestamp
					WHERE QuId=(select EnId from Entries where $EnSelect)", $_SESSION['TourCode']);
			}
			$rs=safe_w_sql($query);
			if(safe_w_affected_rows()) {
				safe_w_sql("update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where EnId='{$id}'");
				safe_w_sql("UPDATE Qualifications SET QuBacknoPrinted=0, QuTimestamp=QuTimestamp WHERE QuId='{$id}'");
				LogAccBoothQuerry("update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where $EnSelect", $_SESSION['TourCode']);
				LogAccBoothQuerry("UPDATE Qualifications SET QuBacknoPrinted=0, QuTimestamp=QuTimestamp WHERE QuId=(select EnId from Entries where $EnSelect)", $_SESSION['TourCode']);
			}
			$error = 0;
		} else {
			if (preg_match('/^[0-9]{1,' . TargetNoPadding . '}[a-z]{1}$/i',$target)) {
				// verifico che la persona in db abbia la sessione uguale a quella passata e diversa da zero
				$query = "SELECT QuId FROM Qualifications WHERE QuId=" . StrSafe_DB($id) . " AND QuSession<>0 AND QuSession=" . StrSafe_DB($session);
				$rs=safe_r_sql($query);

				if (safe_num_rows($rs)==1) {
					$targetNo=str_pad(strtoupper($target),(TargetNoPadding+1),'0',STR_PAD_LEFT);

					$target=$session . $targetNo;
				// verifico che il bersaglio esista

					$query = "SELECT AtTargetNo FROM AvailableTarget WHERE AtTournament=" . StrSafe_DB($tourId) . " AND AtTargetNo=" . StrSafe_DB($target);
					$rs=safe_r_sql($query);

					if (safe_num_rows($rs)) {
						$error = 0;
						// verifico se è già in uso (e se si salvo ma segno l'errore)
						$query = "SELECT QuId
							FROM Qualifications
							INNER JOIN Entries ON QuId=EnId AND EnTournament=" . StrSafe_DB($tourId) . "
							WHERE QuTargetNo=" . StrSafe_DB($target);

						$rs=safe_r_sql($query);
						if (safe_num_rows($rs)>0) {
							$error=1;
						}

						$query = "UPDATE Qualifications
							SET QuTargetNo=" . StrSafe_DB($target) . ", QuTarget=SUBSTRING(QuTargetNo,2)+0, QuLetter=right(QuTargetNo, 1), QuTimestamp=QuTimestamp
							WHERE QuId=" . StrSafe_DB($id) . " ";

						if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
							LogAccBoothQuerry("UPDATE Qualifications
								SET QuTargetNo=" . StrSafe_DB($target) . ", QuTarget=SUBSTRING(QuTargetNo,2)+0, QuLetter=right(QuTargetNo, 1), QuTimestamp=QuTimestamp
								WHERE QuId=(select EnId from Entries where $EnSelect)", $_SESSION['TourCode']);
						}
						$rs=safe_w_sql($query);
						if(safe_w_affected_rows()) {
							safe_w_sql("update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where EnId='{$id}'");
							safe_w_sql("UPDATE Qualifications SET QuBacknoPrinted=0, QuTimestamp=QuTimestamp WHERE QuId='{$id}'");
							LogAccBoothQuerry("update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where $EnSelect", $_SESSION['TourCode']);
							LogAccBoothQuerry("UPDATE Qualifications SET QuBacknoPrinted=0, QuTimestamp=QuTimestamp WHERE QuId=(select EnId from Entries where $EnSelect)", $_SESSION['TourCode']);
						}
					}
				}
			}
		}
	}

/****** Output ******/
	$xmlDoc=new DOMDocument('1.0',PageEncode);
		$xmlRoot=$xmlDoc->createElement('response');
		$xmlDoc->appendChild($xmlRoot);

		// Header
			$xmlHeader=$xmlDoc->createElement('header');
			$xmlRoot->appendChild($xmlHeader);

				$node=$xmlDoc->createElement('error',$error);
				$xmlHeader->appendChild($node);

				$node=$xmlDoc->createElement('row',$row);
				$xmlHeader->appendChild($node);

				$node=$xmlDoc->createElement('col',$col);
				$xmlHeader->appendChild($node);

			$node=$xmlDoc->createElement('target_no',$targetNo);
			$xmlRoot->appendChild($node);

			$node=$xmlDoc->createElement('session',$session);
			$xmlRoot->appendChild($node);



	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();

/****** End OUtput ******/
?>