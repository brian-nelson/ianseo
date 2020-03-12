<?php
/*
													- UpdateStatus.php -
	Modifica lo status di un tizio
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Fun_Partecipants.local.inc.php');
	require_once('Common/Fun_Various.inc.php');


	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$Errore = 0;

	$EnId=0;
	$NewStatus=0;

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		foreach ($_REQUEST as $Key => $Value)
		{
			if (substr($Key,0,13)=='d_e_EnStatus_')
			{
				$Campo='';
				$Id='';

				list(,,$Campo,$Id)=explode('_',$Key);
				$EnId=$Id;

				// se cambio status ricalcolo gli spareggi
				$recalc=false;
				$indFEvent=$teamFEvent=$country=$div=$cl=$subCl=$zero=null;
				$query= "SELECT EnClass FROM Entries WHERE EnId=" . StrSafe_DB($Id) . " AND EnStatus<>" . StrSafe_DB($Value) . " ";
				//print $query;exit;
				$rs=safe_r_sql($query);
				if ($rs && safe_num_rows($rs)==1)
				{
					$recalc=true;
					$x=Params4Recalc($Id);
					if ($x!==false)
					{
						list($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero)=$x;
					}
				}

				$Update
					= "UPDATE Entries SET "
					. "ENStatus=" .StrSafe_DB($Value) . " "
					. "WHERE EnId=" . StrSafe_DB($Id) . " ";
				$RsUp=safe_w_sql($Update);
				if (debug)
					print $Update . '<br><br>';

				checkAgainstLUE($Id);
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

				$Select
					= "SELECT EnStatus FROM Entries WHERE EnId=" . StrSafe_DB($Id) . " ";
				$Rs=safe_r_sql($Select);

				if (debug)
					print $Select . '<br><br>';
				if (safe_num_rows($Rs)==1)
				{
					$Row=safe_fetch($Rs);
					$NewStatus=$Row->EnStatus;
				}
				else
					$Errore=1;
			}
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<id>' . $EnId . '</id>';
	print '<new_status>' . $NewStatus . '</new_status>';
	print '</response>';
?>