<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');

/****** Controller ******/
	$id=(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);

	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : null);
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : null);

	if (!CheckTourSession() || is_null($id) || is_null($row) || is_null($col)) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$tourId=$_SESSION['TourId'];

	$error=0;

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
	// Verifico che l'id esista per la gara aperta
		$query
			= "SELECT "
				. "EnId "
			. "FROM "
				. "Entries "
			. "WHERE EnId=" . StrSafe_DB($id) . " AND EnTournament=" . $tourId . " ";
		$rs=safe_r_sql($query);

		if (safe_num_rows($rs)==1)
		{
			$indFEvent=$teamFEvent=$country=$div=$cl=$subCl=$zero=null;
			$recalc=Params4Recalc($id);
			if ($recalc!==false)
			{
				$recalc=true;
				list($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero)=$recalc;
			}

			// Removes Entries
			deleteArcher($id, true, true);

			if ($recalc)
			{
				RecalculateShootoffAndTeams($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero);

			// rank di classe x tutte le distanze
				$q="SELECT ToNumDist FROM Tournament WHERE ToId={$_SESSION['TourId']}";
				$r=safe_r_sql($q);
				$tmpRow=safe_fetch($r);
				for ($i=0; $i<$tmpRow->ToNumDist;++$i)
				{
					CalcQualRank($i,$div.$cl);
				}

			// rifaccio gli assoluti
				$error=MakeIndAbs();
			}
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

	header('Content-type: text/xml; charset=' . PageEncode);
	header('Cache-Control: no-store, no-cache, must-revalidate');
	print $xmlDoc->saveXML();
/****** End OUtput ******/
?>