<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');
	require_once('Partecipants-exp/common/config.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Various.inc.php');

/****** Controller ******/
	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$tourId=StrSafe_DB($_SESSION['TourId']);

	$error=0;

	$field=(isset($_REQUEST['field']) ? $_REQUEST['field'] : null);
	$value=(isset($_REQUEST['value']) ? $_REQUEST['value'] : null);
	$id=(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);

	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : null);
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : null);

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		if (is_null($field) || is_null($value) || is_null($id) || !array_key_exists($field,$entriesMapping) || is_null($row) || is_null($col)) {
			$error=1;
		} else {
		/*
		 * se aggiorno la div devo tirare fuori la vecchia e se cambia ricalcolo spareggi
		 * e squadre
		 */
			$recalc=false;
			$resetPrintBadge=false;
			$indFEventOld=$teamFEventOld=$countryOld=$divOld=$clOld=$subClOld=$zeroOld=null;
			$indFEvent=$teamFEvent=$country=$div=$cl=$subCl=$zero=null;

			// Timestampupdate
			$TimestampUpdate=true;

			switch($field) {
				case 'division':
					$resetPrintBadge=true;
					$query="SELECT EnDivision FROM Entries WHERE EnId=". StrSafe_DB($id). " AND EnDivision<>" . StrSafe_DB($value) . " " ;
					$rs=safe_r_sql($query);

					if ($rs && safe_num_rows($rs)==1) {
						$recalc=true;

					// prendo le vecchie impostazioni
						$x=Params4Recalc($id);
						if ($x!==false) {
							list($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld, $zeroOld)=$x;
						}
					}
					break;
				case 'first_name':
				case 'name':
					$value=AdjustCaseTitle($value);
					$resetPrintBadge=true;
					break;
				case 'EnOnlineId':
				case 'EnIocCode':
				case 'EnBadgePrinted':
				case 'EnWChair':
				case 'EnSitting':
				case 'EnDoubleSpace':
				case 'EnPays':
					$TimestampUpdate=false;
					break;
			}

			$query = "UPDATE Entries
				SET "
					. $entriesMapping[$field] . "=" . StrSafe_DB($value) . " "
					. ($resetPrintBadge ? ", EnBadgePrinted=0 " : "")
					. ($TimestampUpdate ? '' : ", EnTimestamp=EnTimestamp ")
				. " WHERE EnId=" . StrSafe_DB($id) . " ";
			$rs=safe_w_sql($query);
			if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
				LogAccBoothQuerry("UPDATE Entries
					SET "
						. $entriesMapping[$field] . "=" . StrSafe_DB($value) . " "
						. ($resetPrintBadge ? ", EnBadgePrinted=0 " : "")
						. ($TimestampUpdate ? '' : ", EnTimestamp=EnTimestamp ")
					. " WHERE $EnSelect");
			}

			//print $query;exit;
			if (!$rs) {
				$error=1;
			} else {
			// dopo
				checkAgainstLUE($id);
				if ($recalc)
				{
					$x=Params4Recalc($id);
					if ($x!==false)
					{
						list($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero)=$x;
					}

				// ricalcolo il vecchio e il nuovo
					RecalculateShootoffAndTeams($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld,$zeroOld);
					RecalculateShootoffAndTeams($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero);

				// rank di classe x tutte le distanze
					$q="SELECT ToNumDist FROM Tournament WHERE ToId={$_SESSION['TourId']}";
					$r=safe_r_sql($q);
					$tmpRow=safe_fetch($r);
					for ($i=0; $i<$tmpRow->ToNumDist;++$i) {
						CalcQualRank($i,$divOld.$clOld);
						CalcQualRank($i,$div.$cl);
					}

				// individuali assoluti
					MakeIndAbs();
				}
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

		// Sezione header
			$xmlHeader=$xmlDoc->createElement('header');
			$xmlRoot->appendChild($xmlHeader);

				$node=$xmlDoc->createElement('error',$error);
				$xmlHeader->appendChild($node);

				$node=$xmlDoc->createElement('row',intval($row));
				$xmlHeader->appendChild($node);

				$node=$xmlDoc->createElement('col',intval($col));
				$xmlHeader->appendChild($node);

				$node=$xmlDoc->createElement('field',$field);
				$xmlHeader->appendChild($node);

				$xmlHeader->appendChild($xmlDoc->createElement('value',$value));

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();

/****** End OUtput ******/
?>