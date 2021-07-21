<?php
/*
													- UpdateField.php -
	Aggiorna un campo alla volta.
	Non deve essere usata dai campi che riguardano la nazione perch� questi verranno gestiti da UpdateCountry.php
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once ('Partecipants/Fun_Partecipants.local.inc.php');
	require_once('Common/Fun_FormatText.inc.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$Arr_Tabelle = array
	(
		'e' => array('Entries','EnId'),
		'c' => array('Countries','CoId')
	);

	$Errore=0;
	$Which='#';
	$Value='';
	$passValue='';

	if (!debug)
		header('Content-Type: text/xml');

	foreach ($_REQUEST as $Key => $Value)
	{
		$passValue = $Value;
		if (substr($Key,0,2)=='d_')
		{
			$Campo = '';
			$Chiave = '';
			$Tabella = '';
			list(,$Tabella,$Campo,$Chiave) = explode('_',$Key);
			$Which=$Key;

			if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
			{
				/*$Update
					= "UPDATE Entries SET "
					. $Campo . "=" . StrSafe_DB($Value) . " "
					. "WHERE EnId=" . StrSafe_DB($Chiave) . " ";*/

			/*
			 * se aggiorno la div devo tirare fuori la vecchia e se cambia ricalcolo spareggi
			 * e squadre
			 */
				$recalc=false;
				$indFEventOld=$teamFEventOld=$countryOld=$divOld=$clOld=$subClOld=$zeroOld=null;
				$indFEvent=$teamFEvent=$country=$div=$cl=$subCl=$zero=null;

				switch($Campo) {
					case 'EnDivision':
						$query="SELECT EnDivision FROM Entries WHERE EnId=". StrSafe_DB($Chiave). " AND EnDivision<>" . StrSafe_DB($Value) . " " ;
						$rs=safe_r_sql($query);

						if ($rs && safe_num_rows($rs)==1)
						{
							$recalc=true;

						// prendo le vecchie impostazioni
							$x=Params4Recalc($Chiave);
							if ($x!==false)
							{
								list($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld,$zeroOld)=$x;
							}
						}
						break;
					case 'EnName':
					case 'EnFirstName':
						$Value=AdjustCaseTitle($Value);
					case 'CoName':
					case 'CoNameComplete':
						$passValue = $Value;
						break;
					case 'CoCaCode':
					case 'CoMaCode':
						$passValue = strtoupper($Value);
						break;
					case 'EnIndClEvent':
					case 'EnTeamClEvent':
					case 'EnIndFEvent':
					case 'EnTeamFEvent':
					case 'EnTeamMixEvent':
						$recalc=true;
						break;
					case 'CoParent1':
					case 'CoParent2':
						$searchSQL = "SELECT CoId FROM Countries WHERE CoCode=" . StrSafe_DB(stripslashes($Value)) . " AND CoTournament=".StrSafe_DB($_SESSION['TourId']);
						$rsSearch = safe_r_sql($searchSQL);
						if(safe_num_rows($rsSearch)==1 && $row = safe_fetch($rsSearch))
							$Value=$row->CoId;
						else
							$Value=0;
						break;
				}

				$Update
					= "UPDATE " . $Arr_Tabelle[$Tabella][0]  . " SET "
					. $Campo . "=" . StrSafe_DB(stripslashes($Value)) . " "
					. "WHERE " . $Arr_Tabelle[$Tabella][1] . "=" . StrSafe_DB($Chiave). " ";
				$RsUp=safe_w_sql($Update);
				if(safe_w_affected_rows()) {
					switch($Campo) {
						case 'EnName':
						case 'EnFirstName':
						case 'CoName':
						case 'CoNameComplete':
							safe_w_sql("update Qualifications set QuBacknoPrinted=0 where ". $Arr_Tabelle[$Tabella][1] . "=" . StrSafe_DB($Chiave));
							break;
					}
				}

				if (!$RsUp)
				{
					$Errore=1;
				}

				if (debug)
					print $Update .'<br><br>';

				$Select = "SELECT " . $Campo . " FROM " . $Arr_Tabelle[$Tabella][0] . " WHERE " . $Arr_Tabelle[$Tabella][1] . "=" . StrSafe_DB($Chiave). " ";
				if($Campo=='CoParent1' || $Campo=='CoParent2')
				{
					if($Value!=0)
						$Select = "SELECT CoCode as " . $Campo . " FROM " . $Arr_Tabelle[$Tabella][0] . " WHERE " . $Arr_Tabelle[$Tabella][1] . "=" . StrSafe_DB($Value). " ";
					else
						$Select = "SELECT '' as " . $Campo ;
				}

				$Rs=safe_r_sql($Select);

				if (debug)
					print $Select .'<br><br>';

				if (!$Rs || safe_num_rows($Rs)!=1)
				{
					$Errore=1;
				}
				else
				{
					$Row=safe_fetch($Rs);
					//print '..' . stripslashes($Value) ;
					if ($Row->{$Campo}!=stripslashes($passValue))
					{
						$Errore=1;
					}
					else
					{
						$Value = $Row->{$Campo};
						if ($recalc)
						{
							$x=Params4Recalc($Chiave);
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
							for ($i=0; $i<$tmpRow->ToNumDist;++$i)
							{
								CalcQualRank($i,$divOld.$clOld);
								CalcQualRank($i,$div.$cl);
							}

						// individuale abs
							MakeIndAbs();
						}
					}
				}
			}
			else
				$Errore=1;

			print '<response>';
			print '<error>' . $Errore . '</error>';
			print '<which>' . $Which . '</which>';
			print '<value>' . ((($Campo=='CoParent1' || $Campo=='CoParent2') && $passValue='') ? '' : $Value) . '</value>';
			print '</response>';
		}
	}


?>
