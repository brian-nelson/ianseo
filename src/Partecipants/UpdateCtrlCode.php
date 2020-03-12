<?php
/*
													- UpdateCtrlCode.php -
	Aggiorna il codice fiscale del tizio.
	Richiama poi le info della riga in modo da decidere come gestire le tendine delle classi
*/

define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$Errore=0;
	$AllClasses='';

	if (!isset($_REQUEST['d_e_EnCtrlCode']) || !isset($_REQUEST['EnId']) || !isset($_REQUEST['EnSex']))
		$Errore=1;

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		$ctrlCode=ConvertDateLoc($_REQUEST['d_e_EnCtrlCode']);
		//print $ctrlCode;exit;
		if ($ctrlCode!==false)
		{
			$dob='0000-00-00';
			$cl='';
			$agecl='';
			$dontTouchCl=false;
			if (!empty($ctrlCode))	// la data non è vuota
			{
				list($__yy,$mm,$dd)=explode('-',$ctrlCode);

			// Ultime 2 cifre dell'anno
				$__yy = substr($__yy,-2);

			// Prime 2 cifre dell'anno
				$yy__ = '19';

			/*
				Pivot per discriminare 19xx e 20xx
			*/
				if ($__yy >= '00' && $__yy<='20')
					$yy__='20';

				$yy=intval($yy__ . $__yy);

				$dob=$yy . '-' . $mm . '-' . $dd;

			// calcolo la classe anagrafica e setto l'altra uguale a lei
				$sql= "SELECT ToWhenFrom FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
				$rs=safe_r_sql($sql);
				$myRow=safe_fetch($rs);
				$ToWhenFrom=$myRow->ToWhenFrom;

				$year = substr($ToWhenFrom,0,4) - $yy;

				$sql
					= "SELECT ClId "
					. "FROM Classes "
					. "WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClAgeFrom<=" . StrSafe_DB($year) . " AND ClAgeTo>=" . StrSafe_DB($year) . " ";
				$cond=" AND ClSex=" . StrSafe_DB($_REQUEST['EnSex']) . " ";
				$condUnisex="AND ClSex = -1 ";
				$rs=safe_w_sql($sql.$cond);

				if (safe_num_rows($rs)==1)
				{
					$myRow=safe_fetch($rs);
					$agecl=$myRow->ClId;
					$cl=$myRow->ClId;
				}
				elseif (safe_num_rows($rs)==0)
				{
					$rs=safe_w_sql($sql.$condUnisex);
					if (safe_num_rows($rs)==1)
					{
						$myRow=safe_fetch($rs);
						$agecl=$myRow->ClId;
						$cl=$myRow->ClId;
					}
				}
			}
			else
			{
				$dontTouchCl=true;

			/*
			 * tiro fuori il vecchio sesso x decidere se resettare o no le classi
			 * se cambia il sesso faccio reset
			 */
				$q="SELECT EnSex FROM Entries WHERE EnId=". StrSafe_DB($_REQUEST['EnId']) . " ";
				$r=safe_r_sql($q);
				if ($r && safe_num_rows($r)==1)
				{
					$tmp=safe_fetch($r);
					if ($tmp->EnSex!=$_REQUEST['EnSex'])
						$dontTouchCl=false;
				}
			}

			$recalc=false;
			$indFEventOld=$teamFEventOld=$countryOld=$divOld=$clOld=$subClOld=$zeroOld=null;
			$indFEvent=$teamFEvent=$country=$div=$cl=$subCl=$zero=null;

			if (!$dontTouchCl)
			{
			// se la vecchia classe è diversa ricalcolo spareggi e squadre per la vecchia e la nuova
				$query= "SELECT EnClass FROM Entries WHERE EnId=" . StrSafe_DB($_REQUEST['EnId']) . " AND EnClass<>" . StrSafe_DB($cl) . " ";
				//print $query;exit;
				$rs=safe_r_sql($query);

				if ($rs && safe_num_rows($rs)==1)
				{
					$recalc=true;
				// prendo le vecchie impostazioni
					$x=Params4Recalc($_REQUEST['EnId']);
					if ($x!==false)
					{
						list($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld,$zeroOld)=$x;
					}
				}
			}

			$Update = "UPDATE Entries SET "
				. "EnDob=" . StrSafe_DB($dob). ","
				. "EnSex=" . StrSafe_DB($_REQUEST['EnSex']) . " ";
			if (!$dontTouchCl)
			{
				$Update.= ",EnClass='" . $cl ."',"
					. "EnAgeClass='" .$agecl ."' ";
			}

			$Update .= "WHERE EnId=" . StrSafe_DB($_REQUEST['EnId']) . " ";
			$RsUp=safe_w_sql($Update);

			if (!$RsUp)
				$Errore=1;
			{

				if ($recalc)
				{
					$x=Params4Recalc($_REQUEST['EnId']);
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

			// Ritorno l'elenco di tutte le classi
				$Select
					= "SELECT ClId FROM Classes WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ClViewOrder ASC ";
				$Rs=safe_r_sql($Select);
				if (safe_num_rows($Rs)>0)
				{
					while ($Row=safe_fetch($Rs))
					{
						$AllClasses.= '<cl_id>' . $Row->ClId . '</cl_id>';
					}
				}
			}
		}
		else
			$Errore=1;
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<PARAM>';
	print $AllClasses;
	print '</PARAM>';
	print GetRows($_REQUEST['EnId']);
	print '</response>';
?>