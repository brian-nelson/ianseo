<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Common/Fun_Various.inc.php');

/****** Controller ******/
	$id=(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);
	$status=(isset($_REQUEST['status']) ? $_REQUEST['status'] : null);

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$error = 0;

	$tourId=StrSafe_DB($_SESSION['TourId']);

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		$recalc=false;
		$indFEvent=$teamFEvent=$country=$div=$cl=$subCl=$zero=null;

		// se cambio status ricalcolo gli spareggi
		$query= "SELECT EnClass FROM Entries WHERE EnId=" . StrSafe_DB($id) . " AND EnStatus<>" . StrSafe_DB($status) . " ";
		//print $query;exit;
		$rs=safe_r_sql($query);
		if ($rs && safe_num_rows($rs)==1)
		{
			$recalc=true;
			$x=Params4Recalc($id);
			if ($x!==false)
			{
				list($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero)=$x;
			}
		}

		//Adesso aggiorno lo status
		$query = "UPDATE Entries SET EnStatus=" . StrSafe_DB($status) . " WHERE EnId=" . StrSafe_DB($id);
		$rs=safe_w_sql($query);
		if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
			LogAccBoothQuerry("UPDATE Entries SET EnStatus=" . StrSafe_DB($status) . " WHERE  $EnSelect", $_SESSION['TourCode']);
		}

		checkAgainstLUE($id);
		if (!$rs)
			$error=1;

		if ($recalc)
		{
			// ricalcolo il vecchio e il nuovo
			if (!is_null($indFEvent))
			RecalculateShootoffAndTeams($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero);

			// rank di classe x tutte le distanze
			$q="SELECT ToNumDist FROM Tournament WHERE ToId={$_SESSION['TourId']}";
			$r=safe_r_sql($q);
			$tmpRow=safe_fetch($r);
			for ($i=0; $i<$tmpRow->ToNumDist;++$i)
			{
				if (!is_null($indFEvent))
				CalcQualRank($i,$div.$cl);
			}
			MakeIndAbs();
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

			$node=$xmlDoc->createElement('new_status',$status);
			$xmlRoot->appendChild($node);

	header('Content-type: text/xml; charset=' . PageEncode);
	header('Cache-Control: no-store, no-cache, must-revalidate');
	print $xmlDoc->saveXML();
/****** End OUtput ******/
?>