<?php

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once("Common/Obj_Target.php");
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Fun_MatchTotal.inc.php');
	require_once('Fun_Final.local.inc.php');


	CheckTourSession(true);

	$event = isset($_REQUEST['event']) ? $_REQUEST['event'] : null;
	$matchfirst = isset($_REQUEST['matchfirst']) ? $_REQUEST['matchfirst'] : null;
	$TeamEvent = isset($_REQUEST['team']) ? $_REQUEST['team'] : null;
	$match = isset($_REQUEST['match']) ? $_REQUEST['match'] : null;
	$what = isset($_REQUEST['what']) ? $_REQUEST['what'] : null;
	$arrow = isset($_REQUEST['arrow']) ? $_REQUEST['arrow'] : null;
	$index = isset($_REQUEST['index']) ? $_REQUEST['index'] : null;
	$size = isset($_REQUEST['size']) ? $_REQUEST['size'] : null;
	$xpos = isset($_REQUEST['x']) ? $_REQUEST['x'] : null;
	$ypos = isset($_REQUEST['y']) ? $_REQUEST['y'] : null;
	$Stat1=0;
	$Stat2=0;
	$Match1=-1;
	$Match2=-1;
	$IsFinished=0;

    checkACL(($TeamEvent ? AclTeams : AclIndividuals), AclReadWrite, false);


	$OrgMatch=$match;

	$spotLimit = array(-1,-1,-1);

	$Errore = 0;
	$msg = '';

	$xml = '';
	$xmlArrows = '';
	$isBlocked=($TeamEvent==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM));

	if (is_null($event) || is_null($TeamEvent) || is_null($match) || $isBlocked ) {
		$Errore=1;
	} else {
		//Scelgo il match su cui lavorare
		$match2edit=-1;
		$queryMatch=-1;

		$m2edit=-1;	// 1 o 2 a seconda di quale matchno è stato passato
		$m=-1;

		if ($match%2==0) {
			$queryMatch=$match;
			$match2edit=$match;
			$match=$match2edit+1;
			$m2edit=1;	// 1 o 2 a seconda di quale matchno è stato passato
			$m=2;
		} else {
			$queryMatch=$match-1;
			$match2edit=$match;
			$match=$match2edit-1;
			$m2edit=2;
			$m=1;
		}

		//Carico il vettore dei dati validi
		$CurrentTarget=array();
		$validData=GetMaxScores($event, $match, $TeamEvent);
		//Se ho la posizione, salvo la posizione e ri-setto per i punti
		//if(!(is_null($size) || is_null($xpos) || is_null($ypos))) {
		//	$target = new Obj_Target($validData["Arrows"],$validData["Size"]);
		//	$arrowHit = $target->getHitValue($size, $xpos, $ypos);
		//	$arrUpdate = UpdateArrowPosition($match2edit, $event, $TeamEvent, $arrowHit["X"], $arrowHit["Y"], 0);
		//	if(!is_null($arrUpdate))
		//	{
		//		list($what,$index)=preg_split("/[|]/",$arrUpdate);
		//		$what = ($what == 1 ? 't' : 's');
		//		$arrow = DecodeFromLetter($arrowHit["V"]);
		//	}
		//
		//}
// 		if($arrow==='0') $arrow='M';
// 		elseif($arrow==='0*') $arrow='M*';
		//Se ho i valori di freccia, indice della textbox e tipo textbox (std o so) procedo con il salvataggio dei punti
		if(!(is_null($what) || is_null($arrow) || is_null($index) || !preg_match('/^[st]{1}$/i',$what))) {

			// Verifico la arrow se il valore è valido per il target selezionato
			if(array_key_exists(strtoupper(GetLetterFromPrint($arrow, $CurrentTarget)) , $validData["Arrows"])) {
				$arrow = GetLetterFromPrint($arrow, $CurrentTarget);
			} else {
				$arrow = ' ';
			}

			// conti
			$ArrowStart=$index+1;
			// se però ho mandato il tie devo rifare i conti
			if ($what=='t') {
				$rs=GetFinMatches($event,null,$queryMatch,$TeamEvent,false);

				$myRow=safe_fetch($rs);

				list($rows,$cols,$so)=CalcScoreRowsColsSO($myRow);

				$ArrowStart=($rows*$cols)+1+$index;
			}
			$IsFinished=UpdateArrowString($match2edit,$event,$TeamEvent,$arrow,$ArrowStart,$ArrowStart);
//TEO x FE			if($arrow == ' ') {
//TEO				$IsFinished=UpdateArrowPosition($match2edit, $event, $TeamEvent, '', '', 0, ($what == 's' ? '0':'1') . "|" . $index);
//TEO			}

			$spotLimit[0]=($what == 's' ? 0:1);
		}


		$Select ='';

		if($TeamEvent==0) {
			$Select
				= "SELECT "
				. "f.FinEvent as EvCode, f.FinMatchNo as MatchNo, f2.FinMatchNo as OppMatchNo, EvMatchMode, EvMatchArrowsNo, f.FinStatus Status1, f2.FinStatus Status2, "
				. "IF(f.FinDateTime>=f2.FinDateTime, f.FinDateTime, f2.FinDateTime) AS DateTime,"
				. "f.FinWinLose Winner,f.FinSetPoints SetPoints, f.FinSetPointsByEnd SetPointsByEnds, f.FinScore AS Score, f.FinSetScore AS SetScore, f.FinTie as Tie, f.FinArrowString as ArString, f.FinTieBreak as TbString, "
				. "f2.FinWinLose OppWinner,f2.FinSetPoints OppSetPoints, f2.FinSetPointsByEnd OppSetPointsByEnds, f2.FinScore AS OppScore, f2.FinSetScore AS OppSetScore, f2.FinTie as OppTie, f2.FinArrowString as OppArString, f2.FinTieBreak as OppTbString, "

				. "GrPhase, EvMaxTeamPerson "
				. "FROM Finals AS f "
				. "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament "
				. "INNER JOIN Events ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0 "
				. "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo "
				. "WHERE f.FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (f.FinMatchNo % 2)=0 AND GrMatchNo=" . StrSafe_DB($queryMatch) . " AND f.FinEvent=" . StrSafe_DB($event) . " "
				. "ORDER BY f.FinEvent, f.FinMatchNo ";
		} else {
			$Select
				= "SELECT "
				. "f.TfEvent as EvCode, f.TfMatchNo as MatchNo, f2.TfMatchNo as OppMatchNo, EvMatchMode, EvMatchArrowsNo, f.TfStatus Status1, f2.TfStatus Status2, "
				. "IF(f.TfDateTime>=f2.TfDateTime, f.TfDateTime, f2.TfDateTime) AS DateTime,"
				. "f.TfWinLose Winner,f.TfSetPoints SetPoints, f.TfSetPointsByEnd SetPointsByEnds, f.TfScore AS Score, f.TfSetScore AS SetScore, f.TfTie as Tie, f.TfArrowString as ArString, f.TfTieBreak as TbString, "
				. "f2.TfWinLose OppWinner,f2.TfSetPoints OppSetPoints, f2.TfSetPointsByEnd OppSetPointsByEnds, f2.TfScore AS OppScore, f2.TfSetScore AS OppSetScore, f2.TfTie as OppTie, f2.TfArrowString as OppArString, f2.TfTieBreak as OppTbString, "
				. "GrPhase, EvMaxTeamPerson "
				. "FROM TeamFinals AS f "
				. "INNER JOIN TeamFinals AS f2 ON f.TfEvent=f2.TfEvent AND f.TfMatchNo=IF((f.TfMatchNo % 2)=0,f2.TfMatchNo-1,f2.TfMatchNo+1) AND f.TfTournament=f2.TfTournament "
				. "INNER JOIN Events ON f.TfEvent=EvCode AND f.TfTournament=EvTournament AND EvTeamEvent=1 "
				. "INNER JOIN Grids ON f.TfMatchNo=GrMatchNo "
				. "WHERE f.TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (f.TfMatchNo % 2)=0 AND GrMatchNo=" . StrSafe_DB($queryMatch) . " AND f.TfEvent=" . StrSafe_DB($event) . " "
				. "ORDER BY f.TfEvent, f.TfMatchNo ";
		}
		$Rs=safe_r_sql($Select);

		//print $Select;
		if (safe_num_rows($Rs)==1) {
			$MyRow=safe_fetch($Rs);
			$Stat1=$MyRow->Status1;
			$Stat2=$MyRow->Status2;
			$Match1=$MyRow->MatchNo;
			$Match2=$MyRow->OppMatchNo;

			// winner status
			$xml.='<winner arc1="'.$MyRow->Winner.'" arc2="'.$MyRow->OppWinner.'" />';


			$obj=getEventArrowsParams($MyRow->EvCode,$MyRow->GrPhase,$TeamEvent);
		// tiro fuori subito i vari totali visto che sono calcolati nel db
		// scores
			$xml.='<tot_' . $queryMatch . '>' . $MyRow->Score . '</tot_' . ($queryMatch) . '>';
			$xml.='<tot_' . ($queryMatch+1) . '>' . $MyRow->OppScore . '</tot_' . ($queryMatch+1) . '>';


		// setscores
			if($MyRow->EvMatchMode==1) {
				$xml.='<totsets_' . $queryMatch . '>' . $MyRow->SetScore . '</totsets_' . $queryMatch . '>';
				$xml.='<totsets_' . ($queryMatch+1) . '>' . $MyRow->OppSetScore . '</totsets_' . ($queryMatch+1) . '>';
			}

			// i progressivi
			$maxArrows=$obj->ends*$obj->arrows;
			$stepArrow=$obj->arrows;

			$SetPointsAth=array();
			$SetPointsOpp=array();
			$SetAthArr=explode('|', $MyRow->SetPointsByEnds);
			$SetOppArr=explode('|', $MyRow->OppSetPointsByEnds);
			for($i=0; $i<$maxArrows; $i=$i+$stepArrow) {
			//Cicla per tutte le volee dell'incontro
				//In caso di spot, verifico in che volee sto inserendo
				if($spotLimit[0]==0) {
					if($index >= $i && $index < $i+$stepArrow) {
						$spotLimit[1] = $i;
						$spotLimit[2] = $i+$stepArrow-1;
					}
				} elseif ($spotLimit[0]==1) {
					$SOi=intval($index/$obj->so)*$obj->so;
					$spotLimit[1] = $SOi;
					$spotLimit[2] = $SOi+$obj->so-1;
				}

				$SetAth=0;
				$SetOpp=0;

				$SetPointsAth[] = ValutaArrowString(substr($MyRow->ArString,$i,$stepArrow));
				$SetPointsOpp[] = ValutaArrowString(substr($MyRow->OppArString,$i,$stepArrow));

			// i progressivi
				$xml.='<pr_' . $queryMatch . '_' . $i . '_' . ($i+$stepArrow-1) . '>' . $SetPointsAth[$i/$stepArrow] . '</pr_' . $queryMatch . '_' . $i . '_' . ($i+$stepArrow-1) . '>';
				$xml.='<pr_' . ($queryMatch+1) . '_' . $i . '_' . ($i+$stepArrow-1) . '>' . $SetPointsOpp[$i/$stepArrow] . '</pr_' . ($queryMatch+1) . '_' . $i . '_' . ($i+$stepArrow-1) . '>';

			// i set progressivi dei set
				if($MyRow->EvMatchMode==1) {
					$xml.='<sp_' . $queryMatch . '_' . $i . '_' . ($i+$stepArrow-1) . '><![CDATA[' . (isset($SetAthArr[$i/$stepArrow]) ? $SetAthArr[$i/$stepArrow] : '') . ']]></sp_' . $queryMatch . '_' . $i . '_' . ($i+$stepArrow-1) . '>';
					$xml.='<sp_' . ($queryMatch+1) . '_' . $i . '_' . ($i+$stepArrow-1) . '><![CDATA[' . (isset($SetOppArr[$i/$stepArrow]) ? $SetOppArr[$i/$stepArrow] : '') . ']]></sp_' . ($queryMatch+1) . '_' . $i . '_' . ($i+$stepArrow-1) . '>';
				}
			}

			// Set Next Arrow
			$Arrows=0;
			$tota='';
			$totb='';
			// estraggo tutte le frecce, inclusi gli so
			for($i=0; $i<$obj->ends*$obj->arrows; $i++)		//Cicla per tutte le frecce
			{
				$a=substr($MyRow->ArString,$i,1);
				$b=substr($MyRow->OppArString,$i,1);
				$av=DecodeFromLetter($a);
				$bv=DecodeFromLetter($b);
				$tota.=trim($a);
				$totb.=trim($b);
				$xmlArrows .= '<s_' . $MyRow->MatchNo . '_' . $i . '>'  . $av . '</s_' . $MyRow->MatchNo . '_' . $i . '>';
				$xmlArrows .= '<s_' . $MyRow->OppMatchNo . '_' . $i . '>'  . $bv . '</s_' . $MyRow->OppMatchNo . '_' . $i . '>';
				$Arrows++;
				if($Arrows==$MyRow->EvMaxTeamPerson) {
					$Arrows=0;
					$tota='';
					$totb='';
				}
			}

			$Arrows=0;
			$tota='';
			$totb='';
			for($i=0; $i<max(strlen(trim($MyRow->TbString)), strlen(trim($MyRow->OppTbString))); $i++)		//Cicla per tutte le frecce
			{
				$a=substr($MyRow->TbString,$i,1);
				$b=substr($MyRow->OppTbString,$i,1);
				$av=DecodeFromLetter($a);
				$bv=DecodeFromLetter($b);
				$tota.=trim($a);
				$totb.=trim($b);
				$xmlArrows .= '<t_' . $MyRow->MatchNo . '_' . $i . '>'  . $av . '</t_' . $MyRow->MatchNo . '_' . $i . '>';
				$xmlArrows .= '<t_' . $MyRow->OppMatchNo . '_' . $i . '>'  . $bv . '</t_' . $MyRow->OppMatchNo . '_' . $i . '>';
				$Arrows++;
				if($Arrows==$MyRow->EvMaxTeamPerson) {
					$Arrows=0;
					$tota='';
					$totb='';
				}
			}


			// i cumulativi
			for($i=1; $i<count($SetPointsAth); ++$i) {
				$SetPointsAth[$i]+=$SetPointsAth[$i-1];
				$SetPointsOpp[$i]+=$SetPointsOpp[$i-1];

				if($MyRow->EvMatchMode==1) {
					if(empty($SetAthArr[$i])) $SetAthArr[$i]=0;
					if(empty($SetOppArr[$i])) $SetOppArr[$i]=0;
					$SetAthArr[$i]+=intval($SetAthArr[$i-1]);
					$SetOppArr[$i]+=intval($SetOppArr[$i-1]);
				}
			}

			$ProgSet=0;
			$OppProgSet=0;

			for($i=0,$k=0; $i<$maxArrows; $i=$i+$stepArrow,++$k)		//Cicla per tutte le volee dell'incontro
			{
				$xml.='<totcum_' . $queryMatch . '_' . $i . '_' . ($i+$stepArrow-1) . '>' . $SetPointsAth[$k] . '</totcum_' .  $queryMatch . '_' . $i . '_' . ($i+$stepArrow-1) . '>';
				$xml.='<totcum_' . ($queryMatch+1) . '_' . $i . '_' . ($i+$stepArrow-1) . '>' . $SetPointsOpp[$k] . '</totcum_' .  ($queryMatch+1) . '_' . $i . '_' . ($i+$stepArrow-1) . '>';

				if($MyRow->EvMatchMode==1) {
					$xml.='<st_' . $queryMatch . '_' . $i . '_' . ($i+$stepArrow-1) . '><![CDATA[' . $SetAthArr[$k] . ']]></st_' .  $queryMatch . '_' . $i . '_' . ($i+$stepArrow-1) . '>';
					$xml.='<st_' . ($queryMatch+1) . '_' . $i . '_' . ($i+$stepArrow-1) . '><![CDATA[' . $SetOppArr[$k] . ']]></st_' .  ($queryMatch+1) . '_' . $i . '_' . ($i+$stepArrow-1) . '>';
				}
			}
		}
	}

	if ($Errore==0)
	{
		$msg=get_text('CmdOk');
	}
	else
	{
		$msg=get_text('Error');
	}

	/*
	if(getModuleParameter('Agile', 'Enabled') and file_exists($CFG->DOCUMENT_PATH.'Modules/Agile/fileMatchGenerator.php')) {
		require_once('Modules/Agile/fileMatchGenerator.php');
		updateMatchFiles($_REQUEST['event'], $_REQUEST['team'], $_REQUEST['match'], $_SESSION['TourId']);
	}*/

	header('Content-Type: text/xml');

	print '<response final="'.($IsFinished ? 1 : 0).'" match1="'.$Match1.'" match2="'.$Match2.'" stat1="'.$Stat1.'" stat2="'.$Stat2.'" arrow="'.trim(DecodeFromLetter($arrow)).'">';
		print '<error>' . $Errore . '</error>';
		print '<msg>' . $msg . '</msg>';

		print '<spot_enable>' . $spotLimit[0] . '</spot_enable>';
		print '<spot_start>' . $spotLimit[1] . '</spot_start>';
		print '<spot_end>' . $spotLimit[2] . '</spot_end>';


		print '<arrows>';
			print $xmlArrows;
		print '</arrows>';

		print '<results>';
			print $xml;
		print '</results>';

	print '</response>';
?>