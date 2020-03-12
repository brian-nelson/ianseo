<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$error = 0;

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		$p=isset($_REQUEST['p']) ? $_REQUEST['p'] : null;
		$v=isset($_REQUEST['v']) ? $_REQUEST['v'] : null;

		if (!is_null($p) && !is_null($v))
		{
			list($field,$id)=explode('_',$p);

			$recalc=false;
			$indFEvent=$teamFEvent=$country=$div=$cl=$subCl=$zero=null;

			// se cambio status ricalcolo gli spareggi
			$query= "SELECT EnClass FROM Entries WHERE EnId=" . StrSafe_DB($id) . " AND " . $field . "<>" . StrSafe_DB($v) . " ";
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

			$query
				= "UPDATE "
					. "Entries "
				. "SET "
					. $field . "=" . StrSafe_DB($v) . " "
				. "WHERE "
					. "EnId=" . StrSafe_DB($id) . " ";
			$rs=safe_w_sql($query);

			if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
				LogAccBoothQuerry("UPDATE Entries SET $field=" . StrSafe_DB($v) . " WHERE  $EnSelect", $_SESSION['TourCode']);
			}


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

	}
	else
		$error=1;

	$xml ='<response>'
		. '<error>' . $error . '</error>'
		. '</response>';

	header('Content-type: text/xml; charset=' . PageEncode);

	print $xml;
?>