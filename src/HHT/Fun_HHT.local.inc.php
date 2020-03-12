<?php
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Lib/CommonLib.php');

	$Base62=array
	(
		0 => '0',
		1 => '1',
		2 => '2',
		3 => '3',
		4 => '4',
		5 => '5',
		6 => '6',
		7 => '7',
		8 => '8',
		9 => '9',
		10 => 'A',
		11 => 'B',
		12 => 'C',
		13 => 'D',
		14 => 'E',
		15 => 'F',
		16 => 'G',
		17 => 'H',
		18 => 'I',
		19 => 'J',
		20 => 'K',
		21 => 'L',
		22 => 'M',
		23 => 'N',
		24 => 'O',
		25 => 'P',
		26 => 'Q',
		27 => 'R',
		28 => 'S',
		29 => 'T',
		30 => 'U',
		31 => 'V',
		32 => 'W',
		33 => 'X',
		34 => 'Y',
		35 => 'Z',

		36 => 'a',
		37 => 'b',
		38 => 'c',
		39 => 'd',
		40 => 'e',
		41 => 'f',
		42 => 'g',
		43 => 'h',
		44 => 'i',
		45 => 'j',
		46 => 'k',
		47 => 'l',
		48 => 'm',
		49 => 'n',
		50 => 'o',
		51 => 'p',
		52 => 'q',
		53 => 'r',
		54 => 's',
		55 => 't',
		56 => 'u',
		57 => 'v',
		58 => 'w',
		59 => 'x',
		60 => 'y',
		61 => 'z'
	);

	function toBase62($v)
	{
		global $Base62;

		if ($v>=0 && $v<=61)
			return $Base62[$v];

		return false;
	}

	function fromBase62($v)
	{
		global $Base62;

		if (($v>='0' && $v<='9') ||
			($v>='A' && $v<='Z') ||
			($v>='a' && $v<='z'))
		{
			return array_search($v,$Base62);
		}

		return false;
	}

	function numDay($date)
	{
		$q=safe_r_sql("select unix_timestamp('$date') as UnixTime");
		$r=safe_fetch($q);
		$uts=$r->UnixTime;
//		list($y,$m,$d)=explode('-',$date);
//		$uts=mktime(0,0,0,$m,$d,$y);
		//print $uts .  ' - ' . $_SESSION['ToWhenFromUTS'] . '<br>';

		$diff = floor(($uts-$_SESSION['ToWhenFromUTS']) / (3600 * 24));

		return $diff;
	}

	/*function phaseEncode($what,$session,$dist)
	{
		$phase=false;

		if ($what==-1 || $what==0 || $what==1)

		switch ($what)
		{
			case -1:	// qual
				if (is_numeric($session) && $session>=1 && $session<=9 &&
					$dist>=1 && $dist<=8)
				{
					$phase='0' . $session . $dist;
				}
				break;

			case 0:		// ind
			case 1:		// team
				if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:00/i',$session))
				{
					list($date,$time)=explode(' ',$session);
					list($h,$m)=explode(':',$time);

					$day=numDay($date);

					if ($day>=0 && $day<=26)
					{
						$start=($what==0 ? 65 : 97);	// 'A' oppure 'a'
						$d= chr($start+$day);

						$phase=$d . toBase62(intval($h)) . toBase62(intval($m));
					}
				}
				break;

			default:
				break;
		}

		return $phase;
	}*/

	function phaseEncode($what,$session,$dist)
	{
		$phase=false;

		if ($what==-1 || $what==0 || $what==1)

		switch ($what)
		{
			case -1:	// qual
				if (is_numeric($session) &&	$dist>=1 && $dist<=8)
				{
					$phase='0' . toBase62($session) . $dist;
				}
				break;

			case 0:		// ind
			case 1:		// team
				if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:00/i',$session))
				{
					list($date,$time)=explode(' ',$session);
					list($h,$m)=explode(':',$time);

					$day=numDay($date);

					if ($day>=0 && $day<=26)
					{
						$start=($what==0 ? 65 : 97);	// 'A' oppure 'a'
						$d= chr($start+$day);

						$phase=$d . toBase62(intval($h)) . toBase62(intval($m));
					}
				}
				break;

			default:
				break;
		}

		return $phase;
	}

	/*function phaseDecode($phase)
	{
		$what=false;
		$ses=false;
		$dist=false;

		if (substr($phase,0,1)=='0')	// qual
		{
			$what=-1;
			$ses=substr($phase,1,1);
			$dist=substr($phase,2,1);
		}
		else	// final
		{
			$remove=-1;
			$what=(substr($phase,0,1)>='A' && substr($phase,0,1)<='Z' ? 0 : 1);

			$remove=($what==0 ? 65 : 97);
		//	print $remove . '<br>';
		//	print ord(substr($phase,0,1)) . '<br>';
			$day=ord(substr($phase,0,1))-$remove;

		//	print '..'.$day . '..<br>';

			$q=safe_r_sql("select date_add(ToWhenFrom, interval $day DAY) as new_date from Tournament where ToId={$_SESSION['TourId']}");
			$r=safe_fetch($q);

			//$date=mktime(0,0,0,date('m',$_SESSION['ToWhenFromUTS']),date('d',$_SESSION['ToWhenFromUTS'])+$day,date('Y',$_SESSION['ToWhenFromUTS']));

			//$ses=date('Y-m-d',$date) . ' ' . str_pad(fromBase62(substr($phase,1,1)),2,'0',STR_PAD_LEFT) . ':' . str_pad(fromBase62(substr($phase,2,1)),2,'0',STR_PAD_LEFT) . ':00';
			$ses=$r->new_date . ' ' . str_pad(fromBase62(substr($phase,1,1)),2,'0',STR_PAD_LEFT) . ':' . str_pad(fromBase62(substr($phase,2,1)),2,'0',STR_PAD_LEFT) . ':00';
			$dist=0;
		}

		return array($what,$ses,$dist);
	}*/

	function phaseDecode($phase)
	{
		$what=false;
		$ses=false;
		$dist=false;

		if (substr($phase,0,1)=='0')	// qual
		{
			$what=-1;
			$ses=fromBase62(substr($phase,1,1));
			$dist=substr($phase,2,1);
		}
		else	// final
		{
			$remove=-1;
			$what=(substr($phase,0,1)>='A' && substr($phase,0,1)<='Z' ? 0 : 1);

			$remove=($what==0 ? 65 : 97);
		//	print $remove . '<br>';
		//	print ord(substr($phase,0,1)) . '<br>';
			$day=ord(substr($phase,0,1))-$remove;

		//	print '..'.$day . '..<br>';

			$q=safe_r_sql("select date_add(ToWhenFrom, interval $day DAY) as new_date from Tournament where ToId={$_SESSION['TourId']}");
			$r=safe_fetch($q);

			//$date=mktime(0,0,0,date('m',$_SESSION['ToWhenFromUTS']),date('d',$_SESSION['ToWhenFromUTS'])+$day,date('Y',$_SESSION['ToWhenFromUTS']));

			//$ses=date('Y-m-d',$date) . ' ' . str_pad(fromBase62(substr($phase,1,1)),2,'0',STR_PAD_LEFT) . ':' . str_pad(fromBase62(substr($phase,2,1)),2,'0',STR_PAD_LEFT) . ':00';
			$ses=$r->new_date . ' ' . str_pad(fromBase62(substr($phase,1,1)),2,'0',STR_PAD_LEFT) . ':' . str_pad(fromBase62(substr($phase,2,1)),2,'0',STR_PAD_LEFT) . ':00';
			$dist=0;
		}

		return array($what,$ses,$dist);
	}

/**
 * Genera la tabella dei terminali
 *
 * @param int $RowSize: terminali per riga
 * @param string $FormName: Nome della form in cui vengono generati i terminali.
 * 							Serve quando $Broadcast=false
 * @param bool $Broadcast: true se il tutti rappresenta il broadcast effettivo oppure false se la check è
 * 							una seleziona tutti (jsvascript)
 * @param string[] $Oks: vettore con i valori dei terminali aggiornati
 * @param string[] $FromDB: vettore con i valori dei terminali che sono già stati scaricati (vengono da db)
 * @param string[] $Disable: vettore con i valori dei terminali non considerati
 * @return string: output html
 */
	function TableHTT($RowSize,$FormName,$Broadcast,$Oks,$FromDB,$Disable,$NotUsed=false, $Status=array())
	{
		$out='';
		$jsSelectAll='';

		if (!$Broadcast)
		{
			$jsSelectAll='if (this.checked==true) SelectAllChecks(\'' . $FormName . '\',\'htt\'); else UnselectAllChecks(\'' . $FormName . '\',\'htt\');';
		}


/*		$Select
			= "SELECT DISTINCT "
				. "SUBSTRING(AtTargetNo,2,LENGTH(AtTargetNo)-2) AS TargetNo, "
				. "SUBSTRING(AtTargetNo,1,1) AS Session, "
				. " ToAth4Target1, "
				. " ToAth4Target2, "
				. " ToAth4Target3, "
				. " ToAth4Target4, "
				. " ToAth4Target5, "
				. " ToAth4Target6, "
				. " ToAth4Target7, "
				. " ToAth4Target8, "
				. " ToAth4Target9 "
			. "FROM "
				. "AvailableTarget "
				. " left join Tournament on AtTournament=ToId "
			. "WHERE "
				. "AtTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
				. "AtTargetNo LIKE " . StrSafe_DB($_REQUEST['x_Session'] . '%') . " "
			. "ORDER BY "
				. "SUBSTRING(AtTargetNo,2,LENGTH(AtTargetNo)-2) ASC ";*/
		//print $Select;

		$sessions=GetSessions('Q');
		$ath4target=array();

		foreach ($sessions as $s)
		{
			$ath4target[$s->SesOrder]=$s->SesAth4Target;
		}

		$Select
			= "SELECT DISTINCT "
				. "SUBSTRING(AtTargetNo,2,LENGTH(AtTargetNo)-2) AS TargetNo, "
				. "SUBSTRING(AtTargetNo,1,1) AS Session "
			. "FROM "
				. "AvailableTarget "
			. "WHERE "
				. "AtTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
				. "AtTargetNo LIKE " . StrSafe_DB($_REQUEST['x_Session'] . '%') . " "
			. "ORDER BY "
				. "SUBSTRING(AtTargetNo,2,LENGTH(AtTargetNo)-2) ASC ";

		$Rs=safe_r_sql($Select);

		if ($Rs)
		{
			$out.='<table class="Tabella">' . "\n";
			if (safe_num_rows($Rs))
			{
				$out
					.='<tr>' . "\n"
						. '<td colspan="' . $RowSize .'" class="Center">'
							. '<input type="checkbox" '
								. 'name="HTT[0]" '
								. 'value="0" '
								. 'onclick="' . $jsSelectAll . '"'
								. (isset($_REQUEST['HTT']['0']) ? ' checked ' : '')
							. '/>&nbsp;' . get_text('Broadcast','HTT')
						. '</td>'
					. '</tr>';
				$k=0;
				$out.='<tr>' . "\n";	// apro il primo tr
				while ($MyRow=safe_fetch($Rs))
				{
					if (($k%$RowSize)==0 && $k!=0)
					{
						$out.='</tr>' . "\n";
						$out.='<tr>' . "\n";
					}
					$num=intval($MyRow->TargetNo);
					$digits='';
					$disable=0;
					for ($i='A';$i<='D';++$i)
					{
						$style='';
						if(!empty($Status[$num . $i])) {
							switch($Status[$num . $i]) {
								case 'Red':
									$style=' hhtred ';
									$disable=0;
									break;
								case 'Green':
									$style=' hhtgreen';
									$disable=0;
									break;
								case 'Orange':
									$style=' hhtorange';
									$disable=0;
									break;
								case 'Disabled':
									$style=' hhtdisabledDark';
									break;
								default:
									if($i=='D' and ($ath4target[$MyRow->Session]<4)) {
										$style=' hhtdisabledDark';
									} else {
										$disable=0;
									}
									if(isset($_REQUEST['Command']) and !$style) $style=' hhtyellow';
							}
						} elseif (in_array($num . $i,$FromDB,false)) {
							$style=' hhtred ';
							$disable=0;
						} elseif (in_array($num . $i,$Oks,false)) {
							$style=' hhtgreen';
							$disable=0;
						} elseif (in_array($num . $i,$Disable,false)) {
							$style=' hhtdisabledDark';
						} else {
							if(($i=='D' and ($ath4target[$MyRow->Session]<4)) or ($i=='C' and ($ath4target[$MyRow->Session]<3))) {
								$style=' hhtdisabledDark';
							} else {
								$disable=0;
							}
							if(isset($_REQUEST['Command']) and !$style) $style=' hhtyellow';
						}

						// controllo per la disabilitazione del checkbox
						if (in_array($num . $i,$Disable,false)) $disable++;

						/*
						$out
							.='<div class="htt_letter' . (in_array($num . $i,$Oks,false) ? ' green' : '') . '">' . $i . '</div>';
						*/
						$digits
							.='<div class="htt_letter' . $style . '">' . $i . '</div>';

					}
					$out
						.='<td>'// . (array_search($num,$Oks)!==false ? ' class="green"' : '') . '>'
							. '<div class="htt_number">'
								. '<input type="checkbox" '
									. 'class="htt" '
									. 'id="HTT_' . $num . '" '
									. 'name="HTT[' . $num . ']" '
									. 'value="' . $num . '"'
									. (isset($_REQUEST['HTT'][$num]) ? ' checked ' : '')
									//. ($disable==$MyRow->{'ToAth4Target'.$MyRow->Session} ? ' disabled="disabled" ' : '')
									. ($disable>=$ath4target[$MyRow->Session] ? ' disabled="disabled" ' : '')
								. '/>&nbsp;'  . $MyRow->TargetNo
							. '</div>';

					$out.=$digits;

					$out.='</td>';

					++$k;
				}
				$out.='</tr>' . "\n";	// chudo l'ultimo tr
			}
			$out.='</table>' . "\n";
		}

		return $out;
	}

	function TableFinalHTT($RowSize,$FormName,$Broadcast,$Oks,$FromDB,$Disable, $ABCD=false, $Status=array())
	{
		$out='';
		$jsSelectAll='';

		if (!$Broadcast)
		{
			$jsSelectAll='if (this.checked==true) SelectAllChecks(\'' . $FormName . '\',\'htt\'); else UnselectAllChecks(\'' . $FormName . '\',\'htt\');';
		}

		$team=substr($_REQUEST['x_Session'],0,1);
		$when=substr($_REQUEST['x_Session'],1);

		$Select
			= "SELECT DISTINCT "
				. "CONCAT(FSScheduledDate,' ',FSScheduledTime), "
				. "FSTarget AS TargetNo,"
				. "GrPhase,"
				/*. "FSMatchNo, "
				. "GrMatchNo,"
				. "EvCode,"*/
				. "EvFinalAthTarget AS BitMask "
			. "FROM "
				. "FinSchedule "
				. "INNER JOIN "
					. "Grids "
				. "ON FSMatchNo=GrMatchNo "
				. "INNER JOIN "
					. "Events "
				. "ON FSEvent=EvCode AND FSTeamEvent=EvTeamEvent AND FSTournament=EvTournament "
			. "WHERE "
				. "FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "AND CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($when) . " "
				. "AND FSTeamEvent=" . StrSafe_DB($team) . " "
				. "AND FSTarget<>'' "
			. "ORDER BY "
				. "FSTarget ASC ";
		//print $Select;
		$Rs=safe_r_sql($Select);

		if ($Rs)
		{
			$out.='<table class="Tabella">' . "\n";
			if (safe_num_rows($Rs)>0)
			{
				$out
					.='<tr>' . "\n"
						. '<td colspan="' . $RowSize .'" class="Center">'
							. '<input type="checkbox" '
								. 'name="HTT[0]" '
								. 'value="0" '
								. 'onclick="' . $jsSelectAll . '"'
								. (isset($_REQUEST['HTT']['0']) ? ' checked ' : '')
							. '/>&nbsp;' . get_text('Broadcast','HTT')
						. '</td>'
					. '</tr>';
				$k=0;
				$out.='<tr>' . "\n";	// apro il primo tr
				while ($MyRow=safe_fetch($Rs))
				{
					if (($k%$RowSize)==0 && $k!=0)
					{
						$out.='</tr>' . "\n";
						$out.='<tr>' . "\n";
					}

					$digits='';
					$disable=true;
					$num=intval($MyRow->TargetNo);
					$bit=($MyRow->GrPhase>0 ? $MyRow->GrPhase*2 : 1);
					$value=(($bit & $MyRow->BitMask)==$bit ? 1 : 0);

					for ($i='A';$i<=($ABCD ? 'D' : ($value==0 ? 'A' : 'B'));++$i)
					{
						$style='';
						if(!empty($Status[$num . $i])) {
							switch($Status[$num . $i]) {
								case 'Red':
									$style=' hhtred ';
									$disable=0;
									break;
								case 'Green':
									$style=' hhtgreen';
									$disable=0;
									break;
								case 'Orange':
									$style=' hhtorange';
									$disable=0;
									break;
								case 'Disabled':
									$style=' hhtdisabledDark';
									break;
								default:
									if($i=='D' and ($ath4target[$MyRow->Session]<4)) {
										$style=' hhtdisabledDark';
									} else {
										$disable=0;
									}
									if(isset($_REQUEST['Command']) and !$style) $style=' hhtyellow';
							}
						} elseif (in_array($num . $i,$FromDB,false)) {
							$style=' hhtred ';
							$disable=false;
						} elseif (in_array($num . $i,$Oks,false)) {
							$style=' hhtgreen';
							$disable=false;
						} elseif (in_array($num . $i,$Disable,false)) {
							$style=' hhtdisabledDark';
						} else {
							if(isset($_REQUEST['Command'])) $style=' hhtyellow';
							$disable=false;
						}

						// controllo per la disabilitazione del checkbox
						if (in_array($num . $i,$Disable,false)) $disable++;


						/*
						$out
							.='<div class="htt_letter' . (in_array($num . $i,$Oks,false) ? ' green' : '') . '">' . $i . '</div>';
						*/
						$digits
							.='<div class="htt_letter' . $style . '">' . $i . '</div>';
					}

					$out
						.='<td '.($disable ? ' style="background-color:rgb(220,220,220);"' : '').'>'// . (array_search($num,$Oks)!==false ? ' class="green"' : '') . '>'
							. '<div class="htt_number">'
								. '<input type="checkbox" '
									. 'class="htt" '
									. 'id="HTT_' . $num . '" '
									. 'name="HTT[' . $num . ']" '
									. 'value="' . $num . '"'
									. (isset($_REQUEST['HTT'][$num]) ? ' checked ' : '')
									. ($disable ? ' disabled="disabled"' : '')
									. ' onclick="alert(\'\');"'
									. '/>&nbsp;<a href="#" onclick="document.getElementById(\'HTT_' . $num . '\').disabled=false;">'  . $MyRow->TargetNo . '</a>'
							. '</div>'
							. $digits
							. '</td>';

					++$k;
				}
				$out.='</tr>' . "\n";	// chiudo l'ultimo tr
			}
			$out.='</table>' . "\n";
		}

		return $out;
	}

	function RowTour()
	{
		/*$Select
			= "SELECT ToId,ToNumSession,TtNumDist, "
			. "ToTar4Session1, ToTar4Session2, ToTar4Session3, ToTar4Session4, ToTar4Session5, ToTar4Session6, ToTar4Session7, ToTar4Session8, ToTar4Session9 "
			. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
			. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/
		$Select
			= "SELECT ToId,ToNumSession,ToNumDist AS TtNumDist "
			. "FROM Tournament "
			. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$RsTour=safe_r_sql($Select);

		$RowTour=null;

		if (safe_num_rows($RsTour)==1)
		{
			$RowTour=safe_fetch($RsTour);
		}

		return $RowTour;
	}

	function numHHT()
	{
		$Select = "SELECT HsId FROM HhtSetup WHERE HsTournament=" . StrSafe_DB($_SESSION['TourId']);
		$Rs=safe_r_sql($Select);
		return safe_num_rows($Rs);
	}

	function ComboHHT()
	{
		$ComboHHT='';

		$Select = "SELECT HsId, HsName, HsIpAddress, HsPort FROM HhtSetup WHERE HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY HsId";
		$Rs=safe_r_sql($Select);
		//print $Select;exit;
		if(safe_num_rows($Rs)==1)
		{
			$MyRow=safe_fetch($Rs);
			$ComboHHT .= '<input type="hidden" name="x_Hht" id="x_Hht" value="' . $MyRow->HsId . '">'. $MyRow->HsName . " (" . $MyRow->HsIpAddress . ")";
			$_REQUEST["x_Hht"]= $MyRow->HsId;
		}
		else
		{
			$ComboHHT .= '<select name="x_Hht" id="x_Hht" onChange="resetCmbSession();">' . "\n";
			$ComboHHT .= '<option value="-1">---</option>' . "\n";
			while ($MyRow=safe_fetch($Rs))
				$ComboHHT .='<option value="' . $MyRow->HsId . '" ' . (isset($_REQUEST['x_Hht']) && $_REQUEST['x_Hht']==$MyRow->HsId ? ' selected' : '') . '>' . $MyRow->HsName . ' (' . $MyRow->HsIpAddress . ')</option>' . "\n";
			$ComboHHT .= '</select>' . "\n";
		}

		return $ComboHHT;
	}

	function ComboSes($RowTour, $AllHHT=false, &$ComboSesArray=null)
	{
		$ComboArr=array();
		$ComboSes='';
		$numOptions=0;

		$MatchNames=getPoolMatchesPhases();
		$MatchNamesWA=getPoolMatchesPhasesWA();

		if((isset($_REQUEST["x_Hht"]) && $_REQUEST["x_Hht"]!=-1) or $AllHHT) {
			if(!$AllHHT) {
				$Select='SELECT HeEventCode FROM HhtEvents WHERE HeTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND HeHhtId=' . StrSafe_DB($_REQUEST["x_Hht"]);
				$Rs=safe_r_sql($Select);
			}
			if($AllHHT or (numHHT()==1 && safe_num_rows($Rs)==0 )) {

				if(!$AllHHT) {
					$sessions=GetSessions('Q');


					//Tutte le fasi di qualifica
					/*for ($i=1;$i<=$RowTour->ToNumSession;++$i)
					{
						$ComboSes.= '<option value="' . $i . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$i ? ' selected' : '') . '>' . get_text('QualSession','HTT') . ' ' . $i . '</option>' . "\n";
						$numOptions++;
					}*/
					foreach ($sessions as $s) {
						if ($ComboSesArray!==null) {
							$ComboArr[]=$s->SesOrder;
						}
						$ComboSes.= '<option value="' . $s->SesOrder . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$s->SesOrder ? ' selected' : '') . '>' . get_text('QualSession','HTT') . ' ' . $s->Descr . '</option>' . "\n";
						$numOptions++;
					}
				}

				//Tutte le finali individuali
				if($AllHHT!='Teams') {
					$Select='SELECT'
						. ' @Phase:=ifnull(2*pow(2,truncate(log2(fsmatchno/2),0)),1) Phase'
						. ' , @RealPhase:=truncate(@Phase/2, 0) RealPhase'
						. ' , CONCAT(FSScheduledDate,\' \',FSScheduledTime) AS MyDate'
						. ' , DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDBshort') . '") AS Dt '
						. ' , DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDB') . '") AS Dat '
						. ' , FSTeamEvent '
						. ' , FSEvent '
						. ' , FSScheduledTime '
						. ' , EvFinalFirstPhase '
						. ' , EvElimType '
						. 'FROM'
						. ' `FinSchedule` fs '
						. ' inner join Events on FSEvent=EvCode and FSTeamEvent=EvTeamEvent and FsTournament=EvTournament '
						. 'where'
						. ' FsTournament=' . $_SESSION['TourId']
						. ' and FsTeamEvent=0'
						. ' and fsscheduleddate >0 '
						. 'group by '
						. ' FsScheduledDate, '
						. ' FsScheduledTime, '
						. ' FsEvent, '
						. ' Phase';
					$tmp=array();
					$Rs=safe_r_sql($Select);
					while ($MyRow=safe_fetch($Rs))
					{
						$val=$MyRow->FSTeamEvent . $MyRow->MyDate;
						$text=get_text('FinInd','HTT') . ': ' . $MyRow->MyDate ;
						if($MyRow->EvElimType==3 and isset($MatchNames[$MyRow->RealPhase])) {
							$idx=$MatchNames[$MyRow->RealPhase];
						} elseif($MyRow->EvElimType==4 and isset($MatchNamesWA[$MyRow->RealPhase])) {
							$idx=$MatchNamesWA[$MyRow->RealPhase];
						} else {
							$idx=get_text(namePhase($MyRow->EvFinalFirstPhase, $MyRow->RealPhase) . '_Phase');
						}
						$tmp[$val]['events'][$idx][]= $MyRow->FSEvent;
						$tmp[$val]['date']= $MyRow->Dt . ' '. substr($MyRow->FSScheduledTime,0,5) . ' ' . get_text('FinInd','HTT') ;
						$tmp[$val]['selected']= isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$val ? ' selected' : '';
						$numOptions++;
					}
					foreach($tmp as $k => $v) {
						$val=array();
						foreach($v['events'] as $ph => $ev) $val[]= $ph . ' ('.implode('+',$ev).')';
						$ComboSes.='<option value="'.$k.'"'.$v['selected'].'>'.$v['date']  . ' '. implode('; ',$val).'</option>';
						if ($ComboSesArray!==null)
						{
							$ComboArr[]=$k;
						}
					}
				}

				//Tutte le finali a squadre
				if($AllHHT!='Individuals') {
					$Select='SELECT  @Phase:=ifnull(2*pow(2,truncate(log2(fsmatchno/2),0)),1) Phase, @RealPhase:=truncate(@Phase/2, 0) RealPhase, 
						CONCAT(FSScheduledDate,\' \',FSScheduledTime) AS MyDate, DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDBshort') . '") AS Dt, DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDB') . '") AS Dat,
						FSTeamEvent, FSEvent, FSScheduledTime, EvFinalFirstPhase 
						FROM `FinSchedule` fs 
                        INNER JOIN Events on FSEvent=EvCode and FSTeamEvent=EvTeamEvent and FsTournament=EvTournament 
						WHERE FsTournament=' . $_SESSION['TourId'] . ' and fsscheduleddate >0 AND FSTeamEvent!=0 
						GROUP BY FsScheduledDate, FsScheduledTime, FsEvent, Phase';
					$tmp=array();
					$Rs=safe_r_sql($Select);
					if (safe_num_rows($Rs)>0) {
						while ($MyRow=safe_fetch($Rs)) {
							$val=$MyRow->FSTeamEvent . $MyRow->MyDate;
							$text=get_text('FinTeam','HTT') . ': ' . $MyRow->MyDate;
                            $tmp[$val]['events'][get_text(namePhase($MyRow->EvFinalFirstPhase, $MyRow->RealPhase) . '_Phase')][]= $MyRow->FSEvent;
							$tmp[$val]['date']= get_text('FinTeam','HTT') . ': ' . $MyRow->Dt.' '. substr($MyRow->FSScheduledTime,0,5);
							$tmp[$val]['selected']= isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$val ? ' selected' : '';
							$numOptions++;
						}
						foreach($tmp as $k => $v) {
							$val=array();
							foreach($v['events'] as $ph => $ev) $val[]= $ph . ' ('.implode('+',$ev).')';
							$ComboSes.='<option value="'.$k.'"'.$v['selected'].'>'.$v['date']  . ': '. implode('; ',$val).'</option>';
							if ($ComboSesArray!==null)
							{
								$ComboArr[]=$k;
							}
						}
					}
				}
			} else {
				//Solo le fasi di qualifica associate alla catena HHT
				$Select='SELECT HeSession FROM HhtEvents WHERE HeTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND HeHhtId=' . StrSafe_DB($_REQUEST["x_Hht"]) . " AND HeSession!=0 ORDER BY HeSession";
				$Rs=safe_r_sql($Select);
				while ($MyRow=safe_fetch($Rs)) {
					if ($ComboSesArray!==null) {
						$ComboArr[]=$MyRow->HeSession;
					}
					$ComboSes.= '<option value="' . $MyRow->HeSession . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$MyRow->HeSession ? ' selected' : '') . '>' . get_text('QualSession','HTT') . ' ' . $MyRow->HeSession . '</option>';
					$numOptions++;
				}

				//Solo le finali associate alla catena HHT
				$Select='SELECT'
					. ' @Phase:=ifnull(2*pow(2,truncate(log2(fsmatchno/2),0)),1) Phase'
					. ' , @RealPhase:=truncate(@Phase/2, 0) RealPhase'
					. ' , CONCAT(FSScheduledDate,\' \',FSScheduledTime) AS MyDate'
					. ' , DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDBshort') . '") AS Dt '
					. ' , DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDB') . '") AS Dat '
					. ' , FSTeamEvent '
					. ' , FSEvent '
					. ' , FSScheduledTime '
					. ' , EvFinalFirstPhase '
					. 'FROM'
					. ' `FinSchedule` fs '
					. "	INNER JOIN HhtEvents ON HeTournament=FSTournament AND HeSession=0 AND HeTeamEvent=FSTeamEvent AND HeFinSchedule = CONCAT(FSScheduledDate,' ',FSScheduledTime) "
					. " inner join Events on FSEvent=EvCode and FSTeamEvent=EvTeamEvent and FsTournament=EvTournament "
					. 'where'
					. ' FsTournament=' . $_SESSION['TourId']
					. ' and fsscheduleddate >0 '
					. ' AND HeHhtId=' . StrSafe_DB($_REQUEST["x_Hht"]) . ' '
					. 'group by '
					. ' FsScheduledDate, '
					. ' FsScheduledTime, '
					. ' FsEvent, '
					. ' Phase';
				$tmp=array();
				$Rs=safe_r_sql($Select);
				if (safe_num_rows($Rs)>0)
				{
					while ($MyRow=safe_fetch($Rs))
					{
						$val=$MyRow->FSTeamEvent . $MyRow->MyDate;
						$text=($MyRow->FSTeamEvent==0 ? get_text('FinInd','HTT') . ': ' . $MyRow->MyDate : get_text('FinTeam','HTT') . ': ' . $MyRow->MyDate);
						$tmp[$val]['events'][get_text(namePhase($MyRow->EvFinalFirstPhase, $MyRow->RealPhase) . '_Phase')][]= $MyRow->FSEvent;
						$tmp[$val]['date']= $MyRow->Dt . ' '. substr($MyRow->FSScheduledTime,0,5) . ' ' . ($MyRow->FSTeamEvent==0 ? get_text('FinInd','HTT') : get_text('FinTeam','HTT'));
						$tmp[$val]['selected']= isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$val ? ' selected' : '';
						$numOptions++;
					}
					foreach($tmp as $k => $v) {
						$val=array();
						foreach($v['events'] as $ph => $ev) $val[]= $ph . ' ('.implode('+',$ev).')';
						$ComboSes.='<option value="'.$k.'"'.$v['selected'].'>'.$v['date']  . ' '. implode('; ',$val).'</option>';
						if ($ComboSesArray!==null)
						{
							$ComboArr[]=$k;
						}
					}
				}
			}
			$ComboSes = '<select name="x_Session" id="x_Session">'
				. ($numOptions>1 ? '<option value="-1">---</option>' : '')
				. $ComboSes
				. '</select>';
		}

		if ($ComboSesArray!==null) {
			$ComboSesArray=$ComboArr;
		}
		return $ComboSes;
	}

	function SelectTableHTT($RowSize,$FormName,$Broadcast,$Oks,$FromDB,$Disable, $ABCD=false, $Status=array())
	{
		if (is_numeric($_REQUEST['x_Session']))
			return TableHTT($RowSize,$FormName,$Broadcast,$Oks,$FromDB,$Disable,$ABCD, $Status);
		else
			return TableFinalHTT($RowSize,$FormName,$Broadcast,$Oks,$FromDB,$Disable,$ABCD, $Status);
	}

	function HhtParam($Id)
	{
		$Select = "SELECT HsIpAddress, HsPort FROM HhtSetup WHERE HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HsId=" . StrSafe_DB($Id);
		$Rs=safe_r_sql($Select);
		if(safe_num_rows($Rs)==1)
		{
			$MyRow=safe_fetch($Rs);
			return array($MyRow->HsIpAddress,$MyRow->HsPort);
		}
		else
			return array('127.0.0.1','9001');
	}
?>