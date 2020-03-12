<?php
	define ("debug",false);		// true per l'ouput di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Final/Spot/Common/Config.inc.php');
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');
	require_once('Common/Lib/CommonLib.php');

/**********************
  Cerco l'evento LIVE
***********************/

	$MatchNo=(isset($_REQUEST['MatchNo']) ? intval($_REQUEST['MatchNo']/2)*2 : -1);
	$Event=(empty($_REQUEST['Event']) ? '<![CDATA[]]>' : $_REQUEST['Event']);
	$Team=(isset($_REQUEST['Team']) ? intval($_REQUEST['Team']) : -1);
	$Lock=(isset($_REQUEST['Lock']) ? intval($_REQUEST['Lock']) : 0);
	$TourId=(isset($_REQUEST['TourId']) ? intval($_REQUEST['TourId']) : $_SESSION['TourId']);
	$LiveExists=false;

    checkACL(AclOutput,AclReadOnly, false, $TourId);

	$fotodir='http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . getCodeFromId($TourId) . '-%s-%s-%s.jpg';

	$Live=false;

	if ($x=FindLive($TourId)) {
		if(!$Lock) list($Event, $MatchNo, $Team)=$x;
		$Live=($Event==$x[0] and $MatchNo==$x[1] and $Team==$x[2]);
		$LiveExists=(!$Live and $Lock);
	}

	// Vettore dei punti (bersaglio)
	$MyTargetComplete = false;
	$MyTargetHitMiss = false;
	$MyTargetField = false;

	$MyTargetSize = 0;

	$MatchSx=-1;
	$MatchDx=-1;

	$HiddenArrSx = '';
	$HiddenArrDx = '';
	$HiddenTieSx = '';
	$HiddenTieDx = '';
	$HiddenEvent = '';
	$HiddenMatchNo = '';
	$HiddenReview = '';
	$HiddenDisplay = '<input type="hidden" id="match" value="' . $MatchNo . '">
			<input type="hidden" id="event" value="' . $Event . '">
			<input type="hidden" id="team" value="' . $Team . '">';

	$Output='';

	if(!$Event or $MatchNo<0 or $Team<0) {
		$Output
			= '<table class="Tabella">' . "\n"
			. '<tr><td class="FontBig Bold Center">' . get_text('NoLiveEvent') . '</td></tr>' . "\n"
			. '</table>' . "\n";
	} elseif($Team==0) {


/***********************************************
  Verifico se ho le condizioni per proseguire
************************************************/
		$MatchSx=$MatchNo;
		$MatchDx=$MatchNo+1;

		$MyTarget=GetTargetType($Event, $Team==1, $TourId);
		$MyTargetComplete = TargetIsComplete($MyTarget);
		$MyTargetHitMiss = strstr($MyTarget, 'TrgHM');
		$MyTargetField = strstr($MyTarget, 'TrgField');
		//print $MyTargetComplete;exit;

		$MyTargetSize = 0;
		$MyTargetSize = ($MyTargetComplete ? 100 : 200);


	// Contatori per la distribuzione di freccie
		$CountersSx=array_fill(1,10,0);
		$CountersSx["X"]=0;
		$CountersSx["M"]=0;
		//foreach ($Counters1 as $key => $value) print $key . ' --> ' . $value . "<br>";
		$CountersDx=array_fill(1,10,0);
		$CountersDx["X"]=0;
		$CountersDx["M"]=0;

/*********************
  Dati dello Scontro
**********************/
		$options=array(
			'tournament' => $TourId,
			'matchno' => intval($MatchNo/2)*2,
			'events' => $Event,
			'extended' => true,
			);
		$rank=Obj_RankFactory::create('GridInd',$options);
		$rank->read();
		$rankData=$rank->getData();
		$ArrSx = '';
		$ArrDx = '';

		$TieSx = '';
		$TieDx = '';

		$PosArrSx = array();
		$PosArrDx = array();

		$PosTieSx = array();
		$PosTieDx = array();

		$ReviewLang1 = '';
		$ReviewLang2 = '';

		$StorySx = '';
		$StoryDx = '';

		if($tmpSection=current($rankData['sections']) and $tmpPhase=current($tmpSection['phases']) and $MyRow=current($tmpPhase['items'])) {
			$Phase=key($tmpSection['phases']);
			$PhaseName=$tmpPhase['meta']['phaseName'];
			$MatchMode=$tmpSection['meta']['matchMode'];

			$objParam=getEventArrowsParams($Event, $Phase, $Team, $TourId);
			//Sistemiamo i numeri di  frecce
            $nEND=$objParam->ends; // Numero di Volee
            $nARR=$objParam->arrows;				// Numero di frecce
            $nTieBreak=$objParam->so;

			$maxArrows=$nEND*$nARR;
/**********
  Scontri
***********/
			$SetTieSx=0;
			$SetTieDx=0;

			/* Preparo le variabili per i due scontri */
			$ArrSx =str_pad($MyRow['arrowstring'], $maxArrows);
			$TieSx=str_pad($MyRow['tiebreak'], $nTieBreak);
			$ArrDx=str_pad($MyRow['oppArrowstring'],$maxArrows);
			$TieDx=str_pad($MyRow['oppTiebreak'],$nTieBreak);

			if($MyRow['arrowPosition']) $PosArrSx = ($MyRow['arrowPosition']);
			if($MyRow['tiePosition']) $PosTieSx = ($MyRow['tiePosition']);
			if($MyRow['oppArrowPosition']) $PosArrDx = ($MyRow['oppArrowPosition']);
			if($MyRow['oppTiePosition']) $PosTieDx = ($MyRow['oppTiePosition']);

			$ReviewLang1 = $MyRow['review1'];
			$ReviewLang2 = $MyRow['review2'];
			$HiddenReview = '<input type="hidden" id="Review" value="' . $MyRow['reviewUpdate'] . '">';

			$SemaforoSx = '&nbsp;';
			$SemaforoDx = '&nbsp;';
			$ScoreSx = '';
			$ScoreDx = '';
			$PhotoSx = '';
			$PhotoDx = '';

			$HiddenArrSx = '<input type="hidden" id="ArrSx" value="">';
			$HiddenArrDx = '<input type="hidden" id="ArrDx" value="">';

			$HiddenTieSx = '<input type="hidden" id="TieSx" value="">';
			$HiddenTieDx = '<input type="hidden" id="TieDx" value="">';

/*************************
Conteggio i punti di Set
*************************/

			$SetPointSx=explode('|', $MyRow['setPoints']);
			$SetPointDx=explode('|', $MyRow['oppSetPoints']);
			$SetTotSx=array();
			$SetTotDx=array();
			if($MatchMode) {
				for($i=0; $i<$nEND; $i++) {
					// Cicla per tutte le volee dell'incontro
					if(array_sum($SetTotSx)<$MyRow['setScore'] or array_sum($SetTotDx)<$MyRow['oppSetScore']) {
						if($SetPointSx[$i]>$SetPointDx[$i]) {
							$SetTotSx[]= 2;
							$SetTotDx[]= 0;
						} elseif($SetPointSx[$i]<$SetPointDx[$i]) {
							$SetTotSx[]= 0;
							$SetTotDx[]= 2;
						} else {
							$SetTotSx[]= 1;
							$SetTotDx[]= 1;
						}
					}
				}
			}
/*******************************
  Storico dei match precedenti
********************************/
			//Carico la storia dei Matches precedenti
			$StoryQuery = "SELECT IF(f.FinAthlete=" . StrSafe_DB($MyRow['id']) . ", 0, 1) AS SxDx, f.FinMatchNo, GrPhase as Phase, EvFinalFirstPhase, "
				. "IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS Score, f.FinTie, "
				. "EnFirstName as OppFirstName, EnName as OppName, CoCode as Country, IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) as OppScore, f2.FinTie as OppTie "
			. "FROM Finals AS f "
				. "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament "
				. "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo "
				. "INNER JOIN Events AS e ON EvTournament=f.FinTournament AND EvCode=f.FinEvent AND EvTeamEvent='0' "
				. "LEFT JOIN Entries  ON f2.FinAthlete=EnId AND f2.FinTournament=EnTournament "
				. "LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
			. "WHERE f.FinTournament = $TourId "
				. "AND (f.FinAthlete=" . StrSafe_DB($MyRow['id']) . " OR f.FinAthlete=" . StrSafe_DB($MyRow['oppId']) . ") "
				. "AND f.FinEvent=" . StrSafe_DB($Event) . " AND GrPhase > " . StrSafe_DB($Phase). " "
			. "ORDER BY IF(f.FinAthlete=" . StrSafe_DB($MyRow['id']) . ", 0, 1),f.FinMatchNo";
			//echo $StoryQuery;exit;
			$RsStory=safe_r_sql($StoryQuery);
			if(safe_num_rows($RsStory)>0)
			{
				while($MyRowStory=safe_fetch($RsStory))
				{
					if($MyRowStory->SxDx==0)
						$StorySx .= '<b>' . get_text(namePhase($MyRowStory->EvFinalFirstPhase, $MyRowStory->Phase) . '_Phase') . "</b>: "
							. '<em>' . ($MyRowStory->Score==0 && $MyRowStory->FinTie==2 ?  '' :  ' ' .$MyRowStory->Score) . ($MyRowStory->FinTie==1 ? '*' : '')
							. "</em> - "
							. ($MyRowStory->FinTie==2 ? 'Bye -' : ($MyRowStory->OppScore==0 && $MyRowStory->FinTie==2 ? '' : ' ' . $MyRowStory->OppScore) . ($MyRowStory->OppTie==1 ? '*' : '')) . " " . $MyRowStory->OppFirstName . " " . $MyRowStory->OppName . "\n";
					if($MyRowStory->SxDx==1)
						$StoryDx .= '<b>' . get_text(namePhase($MyRowStory->EvFinalFirstPhase, $MyRowStory->Phase) . '_Phase') . "</b>: "
							. '<em>' . ($MyRowStory->Score==0 && $MyRowStory->FinTie==2 ? '' : ' ' .$MyRowStory->Score) . ($MyRowStory->FinTie==1 ? '*' : '')
							. "</em> - "
							. ($MyRowStory->FinTie==2 ? 'Bye -' : ($MyRowStory->OppScore==0 && $MyRowStory->FinTie==2 ? '' : ' ' . $MyRowStory->OppScore) . ($MyRowStory->OppTie==1 ? '*' : '')) . " " . $MyRowStory->OppFirstName . " " . $MyRowStory->OppName . "\n";
				}
			}
			$StorySx .= '<b>' . get_text('QualRound') . "</b>: " . $MyRow['qualScore'] . " (" .  $MyRow['qualRank'] .")\n";
			$StoryDx .= '<b>' . get_text('QualRound') . "</b>: " . $MyRow['oppQualScore'] . " (" .  $MyRow['oppQualRank'] .")\n";

/********************************
  Atleta di Sinistra --> Athlete
 ********************************/
			//if (!is_null($MyRow["Athlete"]))
			{
				$BioLinkSx='';
				if($MyRow['bib'] and is_numeric($MyRow['bib'])) $BioLinkSx = ' onClick="javascript:OpenPopup(\'BioDetailed.php?Id='.$MyRow['bib'].'&Id2='.$MyRow['oppBib'].'\',\'Biography\',800,600)"';

				$HiddenArrSx = '<input type="hidden" id="ArrSx" value="' . trim($ArrSx) . '">';
				$HiddenTieSx = '<input type="hidden" id="TieSx" value="' . trim($TieSx) . '">';

				$ScoreSx
					= '<table class="Tabella">' . "\n"
					. '<tr>'
					. '<th rowspan="2"  style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('End (volee)') . '</th>'
					. '<th colspan="' . $nARR . '"   style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('Arrow') . '</th>'
					. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text(($MatchMode==0 ? 'TotalProg':'SetTotal'),'Tournament') . '</th>';
				if($MatchMode==0) {
					$ScoreSx .= '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('TotaleScore') . '</th>';
				} else {
					$ScoreSx .= '<th rowspan="2" colspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('SetPoints', 'Tournament') . '</th>';
				}
				$ScoreSx .= '</tr>' . "\n";

				$ScoreSx.='<tr>';
				for ($j=0;$j<$nARR;++$j)
					$ScoreSx.='<th  style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . ($j+1) . '</th>';
					//$ScoreSx.='<th width="10%">' . ($j+1) . '</th>';
				$ScoreSx.='</tr>' . "\n";

				$Tot=0;
				$TotSet=0;
			// ArrowString
				for ($i=0;$i<$nEND;++$i)
				{
					$TotSerie=0;
					$ScoreSx.='<tr>';
					//$ScoreSx.='<th><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $_REQUEST['MatchNo'] . '&amp;Volee=' . ($i+1) . '&amp;Event=' . $_REQUEST['Event'] . '">' .  ($i+1) . '</a></th>';
					$ScoreSx.='<th style="font-size:180%; font-weight:bold; text-align:center;">'.  ($i+1) . '</th>';
					for ($j=0;$j<$nARR;++$j)
					{
						$ScoreSx .= '<td style="font-size:180%; font-weight:bold; text-align:center;">' . DecodeFromLetter($ArrSx[$i*$nARR+$j])  . '</td>';
						$TotSerie += ValutaArrowString($ArrSx[$i*$nARR+$j]);
					}
					$Tot+=$TotSerie;
					$ScoreSx.='<td style="font-size:160%; font-weight:bold; text-align:right;">' . $TotSerie. '</td>';
					if($MatchMode==0) {
						$ScoreSx.='<td style="font-size:180%; ' . ($MatchMode==0 ? 'font-weight:bold; ' : '') . 'text-align:right;">' . $Tot . '</td>';
					} else {
						$TotSet += 	(empty($SetTotSx[$i]) ? 0 : $SetTotSx[$i]);
						$ScoreSx .= '<td style="font-size:160%; font-weight:bold; text-align:right;">' . (empty($SetTotSx[$i]) ? '0' : $SetTotSx[$i]) . '</td>';
						$ScoreSx .= '<td style="font-size:180%; font-weight:bold; text-align:right;">' . $TotSet . '</td>';
					}
					$ScoreSx.='</tr>' . "\n";

				}
			// tiebreak
				$ScoreSx.='<tr>';
				$ScoreSx.='<th style="font-size:180%; font-weight:bold; text-align:center;">T.B</th>';
				$ScoreSx.='<td colspan="' . $nARR . '">';
				$ScoreSx.='<table class="Tabella">' . "\n";
                for($endSo=0; $endSo<ceil(max(strlen(trim($TieDx)),strlen(trim($TieSx)))/$nTieBreak); $endSo++) {
                    $ScoreSx.='<tr>';
                    for ($i = 0; $i < $nTieBreak; ++$i) {
                        $ScoreSx .= '<td style="font-size:180%; font-weight:bold; text-align:center;">' . (!empty($TieSx[($endSo*$nTieBreak)+$i]) ? DecodeFromLetter($TieSx[($endSo*$nTieBreak)+$i]) : '') . '&nbsp</td>';
                    }
                    $ScoreSx.='</tr>';
                }
				$ScoreSx.='</table>' . "\n";
				$ScoreSx.='</td>';
				$ScoreSx.='<td>&nbsp;</td>';
				if($MatchMode==0) {
					$ScoreSx.='<td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot . '</td>';
				} else {
					$ScoreSx .= '<td colspan="2" style="font-size:180%; font-weight:bold; text-align:right;">' . ($MatchMode ? $MyRow['setScore'] : $MyRow['score']) . '</td>';
				}
				$ScoreSx.='</tr>' . "\n";
				$ScoreSx.='</table>' . "\n";

/***********************
  Semaforo di Sinistra
***********************/
				if(count($PosArrSx) || count($PosArrDx)) {
				// semaforo sx
					$SemaforoSx
						= '<table class="Tabella">' . "\n";
				// bersagli delle volee
					for ($i=0;$i<$nEND;++$i)
					{
						$Tmp = '';

/*@Doc, to fix with new target
						for ($j=0;$j<$nARR;++$j)
						{
							if(@array_key_exists (($i*$nARR+$j), $PosArrSx))
								$Tmp .= "&amp;Arrows[]=" .  $PosArrSx[$i*$nARR+$j][0] . ',' . $PosArrSx[$i*$nARR+$j][1];
						}
*/
						if($MyTargetHitMiss)
							$Tmp .='&amp;HMOUT=1';
						else if($MyTargetField)
							$Tmp .='&amp;FIELD=1';

						$SemaforoSx
							.='<tr>'
							. '<td class="FontMedium Bold Right">' . ($i+1) . '</td>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Matchno='.$MatchSx.'&Team=0&Event='.$Event.'&End=' . ($i+1) . '" class="Target" border="0">'
							. '</td>'
							. '</tr>' . "\n";

					}
				// bersaglio del tiebreak
					// bersaglio del tiebreak (solo se ho almeno una freccia in uno dei due)
					if (trim($TieSx . $TieDx)!='')
					{
						$Tmp = '';

/*@Doc, to fix with new target
						for($j=0;$j<$nTieBreak;$j++)
						{
							if(@array_key_exists($j,$PosTieSx))
								$Tmp .= "&amp;Arrows[]=" . $PosTieSx[$j];
						}
*/
						if($MyTargetHitMiss)
							$Tmp .='&amp;HMOUT=1';
						else if($MyTargetField)
							$Tmp .='&amp;FIELD=1';

						$SemaforoSx
							.='<tr>'
							. '<td class="FontMedium Bold Right">T.B.</td>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Matchno='.$MatchSx.'&Team=0&Event='.$Event.'&End=-1" class="Target" border="0">'
							. '</td>'
							. '</tr>' . "\n";
					}
					$SemaforoSx.='</table>' . "\n";
				}

			}

/**********************************
  Atleta di Destra --> OppAthlete
***********************************/
			//if (!is_null($MyRow["OppAthlete"]))
			{
				$BioLinkDx='';
				if($MyRow['oppBib'] and is_numeric($MyRow['oppBib'])) $BioLinkDx = ' onClick="javascript:OpenPopup(\'BioDetailed.php?Id='.$MyRow['oppBib'].'&Id2='.$MyRow['bib'].'\',\'Biography\',800,600, 800)"';
				$PhotoDx = $CFG->ROOT_DIR.'Partecipants-exp/common/photo.php?mode=y&val=150&id=' . $MyRow['oppId'];

				$HiddenArrDx = '<input type="hidden" id="ArrDx" value="' . trim($ArrDx) . '">';
				$HiddenTieDx = '<input type="hidden" id="TieDx" value="' . trim($TieDx) . '">';

				$ScoreDx
					= '<table class="Tabella">' . "\n"
					. '<tr>'
					. '<th rowspan="2"  style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('End (volee)') . '</th>'
					. '<th colspan="' . $nARR . '"   style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('Arrow') . '</th>'
					. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text(($MatchMode==0 ? 'TotalProg':'SetTotal'),'Tournament') . '</th>';
				if($MatchMode==0) {
					$ScoreDx .= '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('TotaleScore') . '</th>';
				} else {
					$ScoreDx .= '<th rowspan="2" colspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('SetPoints', 'Tournament') . '</th>';
				}
				$ScoreDx .= '</tr>' . "\n";

				$ScoreDx.='<tr>';
				for ($j=0;$j<$nARR;++$j)
					$ScoreDx.='<th style="font-size:180%; font-weight:bold; text-align:center; width:15%; width:10%">' . ($j+1) . '</th>';
				$ScoreDx.='</tr>' . "\n";

				$Tot=0;
				$TotSet=0;
			// ArrowString
				for ($i=0;$i<$nEND;++$i)
				{
					$TotSerie=0;
					$ScoreDx.='<tr>';
					$ScoreDx.='<th style="font-size:180%; font-weight:bold; text-align:center;">' .  ($i+1) . '</th>';
					for ($j=0;$j<$nARR;++$j)
					{
						$ScoreDx  .= '<td style="font-size:180%; font-weight:bold; text-align:center;">' .  DecodeFromLetter($ArrDx[$i*$nARR+$j])   . '</td>';
						$TotSerie += ValutaArrowString($ArrDx[$i*$nARR+$j]);;
					}
					$Tot+=$TotSerie;
					$ScoreDx.='<td style="font-size:160%; font-weight:bold; text-align:right;">' . $TotSerie. '</td>';
					if($MatchMode==0) {
						$ScoreDx.='<td style="font-size:180%; ' . ($MatchMode==0 ? 'font-weight:bold; ' : '') . 'text-align:right;">' . $Tot . '</td>';
					} else {
						$TotSet += 	(empty($SetTotDx[$i]) ? 0 : $SetTotDx[$i]);
						$ScoreDx .= '<td style="font-size:160%; font-weight:bold; text-align:right;">' . (empty($SetTotDx[$i]) ? '0' : $SetTotDx[$i]) . '</td>';
						$ScoreDx .= '<td style="font-size:180%; font-weight:bold; text-align:right;">' . $TotSet . '</td>';
					}
					$ScoreDx.='</tr>' . "\n";
				}
			// Tiebreak
				$ScoreDx.='<tr>';
				$ScoreDx.='<th style="font-size:180%; font-weight:bold; text-align:center;">T.B.</th>';
				$ScoreDx.='<td colspan="' . $nARR . '">';
				$ScoreDx.='<table class="Tabella">' . "\n";
                for($endSo=0; $endSo<ceil(max(strlen(trim($TieDx)),strlen(trim($TieSx)))/$nTieBreak); $endSo++) {
                    $ScoreDx.='<tr>';
                    for ($i = 0; $i < $nTieBreak; ++$i) {
                        $ScoreDx .= '<td style="font-size:180%; font-weight:bold; text-align:center;">' . (!empty($TieDx[($endSo*$nTieBreak)+$i]) ? DecodeFromLetter($TieDx[($endSo*$nTieBreak)+$i]) : '') . '&nbsp</td>';
                    }
                    $ScoreDx.='</tr>';
                }
				$ScoreDx.='</table>' . "\n";
				$ScoreDx.='</td>';
				$ScoreDx.='<td>&nbsp;</td>';
				if($MatchMode==0) {
					$ScoreDx.='<td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot . '</td>';
				} else {
					$ScoreDx .= '<td colspan="2" style="font-size:180%; font-weight:bold; text-align:right;">' . ($MatchMode ? $MyRow['oppSetScore'] : $MyRow['oppScore']) . '</td>';
				}

				$ScoreDx.='</tr>' . "\n";
				$ScoreDx.='</table>' . "\n";

/***********************
  Semaforo di Destra
***********************/
				if($PosArrSx OR $PosArrDx) {
					$SemaforoDx
						= '<table class="Tabella">' . "\n";
				// bersagli delle volee
					for ($i=0;$i<$nEND;++$i)
					{
						$Tmp = '';
/*@Doc, to fix with new target
                        for ($j=0;$j<$nARR;++$j)
                        {
                            if(@array_key_exists (($i*$nARR+$j), $PosArrDx))
                                    $Tmp .= "&amp;Arrows[]=" .  $PosArrDx[$i*$nARR+$j][0] . "," . $PosArrDx[$i*$nARR+$j][1];
                        }
                        */
						if($MyTargetHitMiss)
							$Tmp .='&amp;HMOUT=1';
						else if($MyTargetField)
							$Tmp .='&amp;FIELD=1';

						$SemaforoDx
							.='<tr>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Matchno='.$MatchDx.'&Team=0&Event='.$Event.'&End=' . ($i+1) .  '" class="Target" border="0">'
							. '<td class="FontMedium Bold">' . ($i+1) . '</td>'
							. '</tr>' . "\n";

					}
				// bersaglio del tiebreak (solo se ho almeno una freccia in uno dei due)
					if (trim($TieSx . $TieDx)!='')
					{
						$Tmp = '';
/*@Doc, to fix with new target
						for($j=0;$j<$nTieBreak;$j++)
						{
							if(@array_key_exists($j,$PosTieDx))
								$Tmp .= "&amp;Arrows[]=" . $PosTieDx[$j];
						}
*/
						if($MyTargetHitMiss)
							$Tmp .='&amp;HMOUT=1';
						else if($MyTargetField)
							$Tmp .='&amp;FIELD=1';

						$SemaforoDx
							.='<tr>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Matchno='.$MatchDx.'&Team=0&Event='.$Event.'&End=-1" class="Target" border="0">'
							. '<td class="FontMedium Bold">T.B.</td>'
							. '</tr>' . "\n";
					}
					$SemaforoDx.='</table>' . "\n";
				}
			}

/**************************
  Contenitore dell'output
***************************/
			$WinnerRow='';
			if($MyRow['winner'] or $MyRow['oppWinner']) {
				$WinnerRow='<tr>'
				. (count($PosArrDx) || count($PosArrSx) ? '<td>&nbsp;</td>' : '')
				. '<td'.($MyRow['winner'] ? ' class="Winner"' : '').'>'.($MyRow['winner'] ? get_text('Winner') : '&nbsp;').'</td>'
				. '<td'.($MyRow['oppWinner'] ? ' class="Winner"' : '').'>'.($MyRow['oppWinner'] ? get_text('Winner') : '&nbsp;').'</td>'
				. (count($PosArrDx) || count($PosArrSx) ? '<td>&nbsp;</td>' : '')
				. '</tr>';
			}

			$Output
				= '<table class="Tabella">' . "\n"
				. '<tr height="1%">'
				. '<th colspan="4" class="Title" style="font-size:200%; font-weight:bold; text-align:center;">'
				. ($Live?'<img src="'.$CFG->ROOT_DIR.'Common/Images/greendot_anim.gif" align="absmiddle"> ':'')
				.  $tmpSection['meta']['eventName']
				. ($Live?' - '.get_text('LiveUpdate','Tournament').' <img src="'.$CFG->ROOT_DIR.'Common/Images/greendot_anim.gif" align="absmiddle">':'')
				. ($LiveExists ? ' - <a href="./">'.get_text('GoLive','Tournament').'</a>': '')
				. '</th>'
				. '</tr>' . "\n"
				. '<tr height="1%">'
				. '<th colspan="4" class="Title">' .  $PhaseName . '</th>'
				. '</tr>' . "\n"
				. '<tr height="1%">'
				. (count($PosArrSx) || count($PosArrDx) ? '<td style="font-size:150%; font-weight:bold; text-align:center; width:17%;">' . $MyRow['countryName'] . '</td>' : '')
				. '<td style="font-size:180%; font-weight:bold; text-align:center; width:32%;">' . $MyRow['athlete'] . '<br>(' . $MyRow['countryName']. ')</td>'
				. '<td style="font-size:180%; font-weight:bold; text-align:center; width:32%;">' . $MyRow['oppAthlete']  . '<br>(' . $MyRow['oppCountryName']. ')</td>'
				. (count($PosArrSx) || count($PosArrDx) ? '<td style="font-size:150%; font-weight:bold; text-align:center; width:17%;">' . $MyRow['oppCountryName'] . '</td>' : '')
				. '</tr>' . "\n"
				. $WinnerRow
				. '<tr>'
				. (count($PosArrSx) || count($PosArrDx) ? '<td valign="top" rowspan="3">' . $SemaforoSx . '</td>' : '')
				. '<td valign="top" class="Center" nowrap>' . $ScoreSx . '<br>' . get_photo_ianseo($MyRow['id'], 150, '', $BioLinkSx, true) . '<br>'
				. '<div style="font-size:180%; text-align:justify; margin:10px;">' . nl2br($StorySx) . '</div>'
				. '</td>'
				. '<td valign="top" class="Center" nowrap>' . $ScoreDx . '<br>' . get_photo_ianseo($MyRow['oppId'], 150, '', $BioLinkDx, true) . '<br>'
				. '<div style="font-size:180%; text-align:justify; margin:10px;">' . nl2br($StoryDx) . '</div>'
				. '</td>'
				. (count($PosArrSx) || count($PosArrDx) ? '<td valign="top" rowspan="3">' . $SemaforoDx . '</td>' : '')
				. '</tr>' . "\n"
				. '<tr>'
				. '<td valign="top" colspan="2" style="font-size:150%; text-align:justify; margin:10px;">' . nl2br(htmlentities($ReviewLang1, ENT_NOQUOTES, "UTF-8"))  . '<spacer></spacer></td>'
				. '</tr>' . "\n"
				. '<tr>'
				. '<td valign="top" colspan="2" style="font-size:150%; text-align:justify; margin:10px;">' . nl2br(htmlentities($ReviewLang2, ENT_NOQUOTES, "UTF-8"))  . '<spacer></spacer></td>'
				. '</tr>' . "\n";
			$Output.='</table>' . "\n";
		}
	} else {
		//////// VIEW TEAM
		$MatchSx=$MatchNo;
		$MatchDx=$MatchNo+1;

		$MyTargetComplete = TargetIsComplete(GetTargetType($Event,1));
		//print $MyTargetComplete;exit;

		$MyTargetSize = 0;
		$MyTargetSize = ($MyTargetComplete ? 100 : 200);

		// Contatori per la distribuzione di freccie
		$CountersSx=array_fill(1,10,0);
		$CountersSx["X"]=0;
		$CountersSx["M"]=0;
		//foreach ($Counters1 as $key => $value) print $key . ' --> ' . $value . "<br>";
		$CountersDx=array_fill(1,10,0);
		$CountersDx["X"]=0;
		$CountersDx["M"]=0;

		// check event material!
		$Select="select distinct count(*) TeamMates, Events.* from Events inner join TeamComponent on EvTournament=TcTournament and EvTeamEvent=1 and EvCode=TcEvent where EvTournament={$TourId} and EvCode='$Event' group by TcCoId, TcSubTeam";
		$t=safe_r_sql($Select);
		$EVENT=safe_fetch($t);

		$Select
			= "SELECT TfMatchNo AS MatchNo, TfWinLose as Winner, TfEvent AS Event, EvEventName AS EventName, GrPhase as Phase, CONCAT(TfTeam,'|',TfSubTeam) AS Athlete, EvMatchMode as MatchMode, "
			. "RevLanguage1, RevLanguage2, UNIX_TIMESTAMP(RevDateTime) as ReviewUpdate, "
			. "IF(EvMatchMode=0,TfScore,TfSetScore) AS FinalScore, TfScore AS Score, TfSetPointsByEnd as SetPointsByEnds, TfSetScore as SetScore, TfSetPoints as SetPoints, TfArrowString AS ArrowString,TfArrowPosition AS ArrPos,"
			. "TeScore, TeRank, GROUP_CONCAT(if(EnNameOrder, CONCAT(UPPER(EnFirstName), ' ', EnName), CONCAT(EnName, ' ', UPPER(EnFirstName))) order by EnSex desc, EnFirstName, EnName SEPARATOR ', ' ) as Archers, group_concat(EnId order by EnSex desc, EnFirstName, EnName) ArcEnIds, group_concat(EnCode order by EnSex desc, EnFirstName, EnName) ArcEnCodes, "
			. "TfTieBreak AS TieBreak,TfTiePosition AS TiePos, "
			. "TfWinLose AS WinLose, "
			. "CoCode AS NationCode,CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) AS Nation, @elimination:=pow(2, ceil(log2(GrPhase))+1) & EvMatchArrowsNo "
			. " , if(@elimination, EvElimEnds, EvFinEnds) CalcEnds "
			. " , if(@elimination, EvElimArrows, EvFinArrows) CalcArrows "
			. " , if(@elimination, EvElimSO, EvFinSO) CalcSO "
			. "FROM TeamFinals "
			. "INNER JOIN Events ON TfEvent=EvCode AND EvTeamEvent=1 AND TfTournament=EvTournament "
			. "INNER JOIN Countries ON TfTeam=CoId "
			. "INNER JOIN Grids ON TfMatchNo=GrMatchNo "
			. "INNER JOIN Teams ON TfTeam=TeCoId AND TfSubTeam=TeSubTeam AND TfEvent=TeEvent AND TfTournament=TeTournament AND TeFinEvent='1' "
			. "INNER JOIN TeamFinComponent ON TeCoId=TfcCoId AND TeSubTeam=TfcSubTeam AND TeTournament=TfcTournament AND TeEvent=TfcEvent "
			. "INNER JOIN Entries ON TfcId=EnId "
			. "LEFT JOIN Reviews ON TfEvent=RevEvent AND TfMatchNo=RevMatchNo AND TfTournament=RevTournament AND RevTeamEvent=1 "
			. "WHERE TfMatchNo IN(" . StrSafe_DB($MatchSx) . "," . StrSafe_DB($MatchDx) . ") "
			. "AND TfEvent=" . StrSafe_DB($Event) . " AND TfTournament=" . StrSafe_DB($TourId) . " "
			. "GROUP BY TfMatchNo "
			. "ORDER BY TfMatchNo ASC ";

		if (debug) print $Select . '<br>';
		$Rs=safe_r_sql($Select);
		$MyRowSx=NULL;
		$MyRowDx=NULL;

		$ArrSx = '';
		$ArrDx = '';

		$TieSx = '';
		$TieDx = '';

		$PosArrSx = array();
		$PosArrDx = array();

		$PosTieSx = '';
		$PosTieDx = '';

		$ReviewLang1 = '';
		$ReviewLang2 = '';

		$StorySx = '';
		$StoryDx = '';

		if (safe_num_rows($Rs)>0 && safe_num_rows($Rs)<=2)
		{
			$MyRowSx=safe_fetch($Rs);
		/*
			Qui sicuramente Sx non è false ma forse Dx si.
			Se Dx è false gli copio dentro l'Sx e poi annullo
			Dx Se $MyRowSx->MatchNo è pari altrimenti annullo Sx.
		*/
			$nEND=$MyRowSx->CalcEnds;	// Numero di Volee
			$nARR=$MyRowSx->CalcArrows;	// Numero di frecce
			$nSO=$MyRowSx->CalcSO; //Numero di frecce di SO
			$nMaxArrows=$nEND*$nARR;
			$EventName=$MyRowSx->EventName;
			$PhaseName=$MyRowSx->Phase;

			$ArrSx =str_pad($MyRowSx->ArrowString,MaxFinTeamArrows);
			$TieSx=str_pad($MyRowSx->TieBreak,$nSO);

            //if($MyRowSx->ArrPos) $PosArrSx = json_decode($MyRowSx->ArrPos);
            //if($MyRowSx->TiePos) $PosTieSx = json_decode($MyRowSx->TiePos);

			$ReviewLang1 = $MyRowSx->RevLanguage1;
			$ReviewLang2 = $MyRowSx->RevLanguage2;
			$HiddenReview = '<input type="hidden" id="Review" value="' . $MyRowSx->ReviewUpdate . '">';

			$PhotosSx='';
			$PhotosDx='';

			if ($MyRowDx=safe_fetch($Rs)) {
				$EnCodesSx=explode(',', $MyRowSx->ArcEnCodes);
				$EnCodesDx=explode(',', $MyRowDx->ArcEnCodes);
				$ArchersSx=explode(',', $MyRowSx->Archers);
				$ArchersDx=explode(',', $MyRowDx->Archers);
				foreach(explode(',', $MyRowSx->ArcEnIds) as $k => $EnId) {
					$PhotosSx.='<div style="display:inline-block;margin-right:0.5rem">'.get_photo_ianseo($EnId, 150, '', ' onClick="javascript:OpenPopup(\'BioDetailed.php?Id='.$EnCodesSx[$k].'\',\'Biography\',800,600)"', true).'<br/><b>'.$ArchersSx[$k].'</b></div>';
				}
				foreach(explode(',', $MyRowDx->ArcEnIds) as $k => $EnId) {
					$PhotosDx.='<div style="display:inline-block;margin-right:0.5rem">'.get_photo_ianseo($EnId, 150, '', ' onClick="javascript:OpenPopup(\'BioDetailed.php?Id='.$EnCodesDx[$k].'\',\'Biography\',800,600,800)"', true).'<br/><b>'.$ArchersDx[$k].'</b></div>';
				}

				$ArrDx=str_pad($MyRowDx->ArrowString,MaxFinTeamArrows);
				$TieDx=str_pad($MyRowDx->TieBreak,$nSO);
                //if($MyRowDx->ArrPos) $PosArrDx = json_decode($MyRowDx->ArrPos);
                //if($MyRowDx->TiePos) $PosTieDx = json_decode($MyRowDx->TiePos);
            } elseif($MyRowSx->MatchNo%2==1) {
				$MyRowDx=$MyRowSx;
				$MyRowSx=false;
			}

/*************************
 Conteggio i punti di Set
*************************/
			$SetPointSx=array();
			$SetPointDx=array();
			$SetTotSx=array();
			$SetTotDx=array();
			if($MyRowSx->MatchMode!=0 || $MyRowDx->MatchMode!=0)
			{
				//Sistemiamo i numeri di  frecce
				$nEND=$MyRowSx->CalcEnds;				// Numero di Volee
				$nARR=$MyRowSx->CalcArrows;				// Numero di frecce

				$maxArrows=$nEND*$nARR;
				$nTieBreak=$MyRowSx->CalcSO;

				$SetTotSx= explode('|', $MyRowSx->SetPointsByEnds);
				$SetTotDx= explode('|', $MyRowDx->SetPointsByEnds);

				for($i=0; $i<$maxArrows; $i=$i+$nARR)		//Cicla per tutte le volee dell'incontro
				{
					$SetPointSx[] = ValutaArrowString(substr($MyRowSx->ArrowString,$i,$nARR));
					$SetPointDx[] = ValutaArrowString(substr($MyRowDx->ArrowString,$i,$nARR));
				}
			}
/*******************************
 Storico dei match precedenti
********************************/
			$StoryQuery = "SELECT f.TfTiebreak, f2.TfTiebreak OppTiebreak, IF(CONCAT(f.TfTeam,'|',f.TfSubTeam)=" . ($MyRowSx===false ? '0' : StrSafe_DB($MyRowSx->Athlete)) . ", 0, 1) AS SxDx, f.TfMatchNo, GrPhase as Phase, "
				. "IF(EvMatchMode=0,f.TfScore,f.TfSetScore) AS Score, f.TfTie, "
				. "CONCAT(CoName, IF(f2.TfSubTeam>'1',CONCAT(' (',f2.TfSubTeam,')'),'')) as OppName, CoCode as OppCode, IF(EvMatchMode=0,f2.TfScore,f2.TfSetScore) as OppScore, f2.TfTie as OppTie "
			. "FROM TeamFinals AS f "
				. "INNER JOIN TeamFinals AS f2 ON f.TfEvent=f2.TfEvent AND f.TfMatchNo=IF((f.TfMatchNo % 2)=0,f2.TfMatchNo-1,f2.TfMatchNo+1) AND f.TfTournament=f2.TfTournament "
				. "INNER JOIN Grids ON f.TfMatchNo=GrMatchNo "
				. "INNER JOIN Events ON f.TfEvent=EvCode AND EvTeamEvent=1 AND f.TfTournament=EvTournament "
				. "LEFT JOIN Countries ON f2.TfTeam=CoId AND f2.TfTournament=CoTournament "
			. "WHERE f.TfTournament = " . StrSafe_DB($TourId) . " "
				. "AND (CONCAT(f.TfTeam,'|',f.TfSubTeam)=" . ($MyRowSx===false ? '0' : StrSafe_DB($MyRowSx->Athlete)) . " OR CONCAT(f.TfTeam,'|',f.TfSubTeam)=" . ($MyRowDx===false ? '0' : StrSafe_DB($MyRowDx->Athlete)) . ") "
				. "AND f.TfEvent=" . StrSafe_DB($Event) . " AND GrPhase > " . ($MyRowSx===false ? StrSafe_DB($MyRowDx->Phase) : StrSafe_DB($MyRowSx->Phase)) . " "
			. "ORDER BY IF(CONCAT(f.TfTeam,'|',f.TfSubTeam)=" . ($MyRowSx===false ? '0' : StrSafe_DB($MyRowSx->Athlete)) . ", 0, 1),f.TfMatchNo";

			$RsStory=safe_r_sql($StoryQuery);
			if(safe_num_rows($RsStory)>0)
			{
				while($MyRowStory=safe_fetch($RsStory))
				{
					$TieSxArrows='';
					$TieDxArrows='';
					if($MyRowStory->TfTie==1 or $MyRowStory->OppTie==1) {
						for($n=0; $n<strlen(rtrim($MyRowStory->TfTiebreak)); $n+=$EVENT->TeamMates) {
							$TieSxArrows.= ValutaArrowString(substr(trim($MyRowStory->TfTiebreak), $n, $EVENT->TeamMates)) . ",";
						}
						$TieSxArrows=' (T '.substr($TieSxArrows,0,-1).')';
						for($n=0; $n<strlen(rtrim($MyRowStory->OppTiebreak)); $n+=$EVENT->TeamMates) {
							$TieDxArrows.= ValutaArrowString(substr(trim($MyRowStory->OppTiebreak), $n, $EVENT->TeamMates)) . ",";
						}
						$TieDxArrows=' (T '.substr($TieDxArrows,0,-1).')';
					}
					if($MyRowStory->SxDx==0) {
						$StorySx .= '<b>' . get_text($MyRowStory->Phase . '_Phase') . "</b>: "
							. '<em>' . ($MyRowStory->Score==0 && $MyRowStory->TfTie==2 ? '' : ' ' .$MyRowStory->Score) . $TieSxArrows . "</em> - "
							. ($MyRowStory->TfTie==2 ? 'Bye -' : ($MyRowStory->OppScore==0 && $MyRowStory->TfTie==2 ? '' : ' ' . $MyRowStory->OppScore) . $TieDxArrows) . " " . $MyRowStory->OppName . "\n";
					} elseif($MyRowStory->SxDx==1) {
						$StoryDx .= '<b>' . get_text($MyRowStory->Phase . '_Phase') . "</b>: "
							. '<em>' . ($MyRowStory->Score==0 && $MyRowStory->TfTie==2 ? '' : ' ' .$MyRowStory->Score) . $TieSxArrows
							. "</em> - "
							. ($MyRowStory->TfTie==2 ? 'Bye -' : ($MyRowStory->OppScore==0 && $MyRowStory->TfTie==2 ? '' : ' ' . $MyRowStory->OppScore) . $TieDxArrows) . " " . $MyRowStory->OppName . "\n";
					}
				}
			}
			$StorySx .= '<b>' . get_text('QualRound') . "</b>: " . $MyRowSx->TeScore . ($MyRowSx->TeRank >0 ? " (" .  $MyRowSx->TeRank .")" : '' ) . "\n";
			$StoryDx .= '<b>' . get_text('QualRound') . "</b>: " . $MyRowDx->TeScore . ($MyRowDx->TeRank >0 ? " (" .  $MyRowDx->TeRank .")" : '' ) . "\n";

			$SemaforoSx = '&nbsp;';
			$SemaforoDx = '&nbsp;';
			$ScoreSx = '';
			$ScoreDx = '';
			$PhotoSx = $CFG->ROOT_DIR.'Accreditation/IdCard/Photo/00.png';
			$PhotoDx = $CFG->ROOT_DIR.'Accreditation/IdCard/Photo/00.png';

			$HiddenArrSx = '<input type="hidden" id="ArrSx" value="">';
			$HiddenArrDx = '<input type="hidden" id="ArrDx" value="">';

			$HiddenTieSx = '<input type="hidden" id="TieSx" value="">';
			$HiddenTieDx = '<input type="hidden" id="TieDx" value="">';

			if (debug)
			{
				print '<pre>ArrowString Sx --> ...' . $ArrSx . '...</pre>';
				print '<pre>ArrowString Dx --> ...' . $ArrDx . '...</pre>';

				print '<pre>Pos Sx --> '; print_r($PosArrSx); print  '</pre>';
				print '<pre>Pos Dx --> '; print_r($PosArrDx); print  '</pre>';

				print '<pre>Tie Sx --> ...' . $TieSx . '...</pre>';
				print '<pre>Tie Dx --> ...' . $TieDx . '...</pre>';

				print '<pre>PosTie Sx --> '; print_r($PosTieSx); print  '</pre>';
				print '<pre>PosTie Dx --> '; print_r($PosTieDx); print  '</pre>';
			}

			if ($MyRowSx) {
				$HiddenArrSx = '<input type="hidden" id="ArrSx" value="' . trim($ArrSx) . '">';
				$HiddenTieSx = '<input type="hidden" id="TieSx" value="' . trim($TieSx) . '">';

				$ScoreSx
					= '<table class="Tabella">' . "\n"
					. '<tr>'
					. '<th rowspan="2"  style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('End (volee)') . '</th>'
					. '<th colspan="' . $nARR . '"   style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('Arrow') . '</th>'
					. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text(($MyRowSx->MatchMode==0 ? 'TotalProg':'SetTotal'),'Tournament') . '</th>';
				if($MyRowSx->MatchMode==0) {
					$ScoreSx .= '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('TotaleScore') . '</th>';
				} else {
					$ScoreSx .= '<th rowspan="2" colspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('SetPoints', 'Tournament') . '</th>';
				}
				$ScoreSx .= '</tr>' . "\n";

				$ScoreSx.='<tr>';
				for ($j=0;$j<$nARR;++$j)
					$ScoreSx.='<th  style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . ($j+1) . '</th>';
					//$ScoreSx.='<th width="10%">' . ($j+1) . '</th>';
				$ScoreSx.='</tr>' . "\n";

				$Tot=0;
				$TotSet=0;
			// ArrowString
				for ($i=0;$i<$nEND;++$i) {
					$TotSerie=0;
					$ScoreSx.='<tr>';
					//$ScoreSx.='<th><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $_REQUEST['MatchNo'] . '&amp;Volee=' . ($i+1) . '&amp;Event=' . $_REQUEST['Event'] . '">' .  ($i+1) . '</a></th>';
					$ScoreSx.='<th style="font-size:180%; font-weight:bold; text-align:center;">'.  ($i+1) . '</th>';
					for ($j=0;$j<$nARR;++$j)
					{
						$ScoreSx  .= '<td style="font-size:180%; font-weight:bold; text-align:center;">' . $MyPValue=DecodeFromLetter($ArrSx[$i*$nARR+$j])  . '</td>';
						$TotSerie += ValutaArrowString($ArrSx[$i*$nARR+$j]);
					}
					$Tot+=$TotSerie;
					$ScoreSx.='<td style="font-size:160%; font-weight:bold; text-align:right;">' . $TotSerie. '</td>';
					if($MyRowSx->MatchMode==0) {
						$ScoreSx.='<td style="font-size:180%; ' . ($MyRowSx->MatchMode==0 ? 'font-weight:bold; ' : '') . 'text-align:right;">' . $Tot . '</td>';
					} else {
						$TotSet += 	(empty($SetTotSx[$i]) ? 0 : $SetTotSx[$i]);
						$ScoreSx .= '<td style="font-size:160%; font-weight:bold; text-align:right;">' . (empty($SetTotSx[$i]) ? '0' : $SetTotSx[$i]) . '</td>';
						$ScoreSx .= '<td style="font-size:180%; font-weight:bold; text-align:right;">' . $TotSet . '</td>';
					}
					$ScoreSx.='</tr>' . "\n";
				}
			// tiebreak
				$ScoreSx.='<tr>';
				$ScoreSx.='<th style="font-size:180%; font-weight:bold; text-align:center;">T.B</th>';
				$ScoreSx.='<td colspan="' . $nARR . '">';
				$ScoreSx.='<table class="Tabella">' . "\n";
                for($endSo=0; $endSo<ceil(max(strlen(trim($TieDx)),strlen(trim($TieSx)))/$nSO); $endSo++) {
                    $ScoreSx.='<tr>';
                    for ($i = 0; $i < $nSO; ++$i) {
                        $ScoreSx .= '<td style="font-size:180%; font-weight:bold; text-align:center;">' . (!empty($TieSx[($endSo*$nSO)+$i]) ? DecodeFromLetter($TieSx[($endSo*$nSO)+$i]):'') . '&nbsp</td>';
                    }
                    $ScoreSx.='</tr>';
                }

				$ScoreSx.='</table>' . "\n";
				$ScoreSx.='</td>';
				$ScoreSx.='<td>&nbsp;</td>';
				if($MyRowSx->MatchMode==0) {
					$ScoreSx.='<td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot . '</td>';
				} else {
					$ScoreSx .= '<td colspan="2" style="font-size:180%; font-weight:bold; text-align:right;">' . $MyRowSx->FinalScore . '</td>';
				}
				$ScoreSx.='</tr>' . "\n";
				$ScoreSx.='</table>' . "\n";
				if($PosArrDx or $PosArrSx) {
				// semaforo sx
					$SemaforoSx
						= '<table class="Tabella">' . "\n";
				// bersagli delle volee
					for ($i=0;$i<$nEND;++$i)
					{
						//$Tmp = '';
						//for ($j=0;$j<$nARR;++$j)
						//{
						//	if(@array_key_exists (($i*$nARR+$j), $PosArrSx))
						//			$Tmp .= "&amp;Arrows[]=" .  $PosArrSx[$i*$nARR+$j]["X"];
						//}
						$SemaforoSx
							.='<tr>'
							. '<td class="FontMedium Bold Right">' . ($i+1) . '</td>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Matchno='.$MatchSx.'&Team=1&Event='.$Event.'&End=' . ($i+1) . '" class="Target" border="0">'
							. '</td>'
							. '</tr>' . "\n";
					}
				// bersaglio del tiebreak
					// bersaglio del tiebreak (solo se ho almeno una freccia in uno dei due)
					if (trim($TieSx . $TieDx)!='')
					{

						//$Tmp = '';
						//
						//for($j=0;$j<$nSO;$j++)
						//{
						//	if(@array_key_exists($j,$PosTieSx) && $PosTieSx[$j]!='')
						//		$Tmp .= "&amp;Arrows[]=" . $PosTieSx[$j];
						//}

						$SemaforoSx
							.='<tr>'
							. '<td class="FontMedium Bold Right">T.B.</td>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Matchno='.$MatchSx.'&Team=1&Event='.$Event.'&End=-1" class="Target" border="0">'
							. '</td>'
							. '</tr>' . "\n";
					}
					$SemaforoSx.='</table>' . "\n";
				}
			}

			if ($MyRowDx)
			{
				$HiddenArrDx = '<input type="hidden" id="ArrDx" value="' . trim($ArrDx) . '">';
				$HiddenTieDx = '<input type="hidden" id="TieDx" value="' . trim($TieDx) . '">';

				$ScoreDx
					= '<table class="Tabella">' . "\n"
					. '<tr>'
					. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('End (volee)') . '</th>'
					. '<th colspan="' . $nARR . '" style="font-size:180%; font-weight:bold; text-align:center;">' . get_text('Arrow') . '</th>'
					. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text(($MyRowDx->MatchMode==0 ? 'TotalProg':'SetTotal'),'Tournament') . '</th>';
				if($MyRowDx->MatchMode==0) {
					$ScoreDx .= '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('TotaleScore') . '</th>';
				} else {
					$ScoreDx .= '<th rowspan="2" colspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('SetPoints', 'Tournament') . '</th>';
				}
				$ScoreDx .= '</tr>' . "\n";

				$ScoreDx.='<tr>';
				for ($j=0;$j<$nARR;++$j)
					$ScoreDx.='<th style="font-size:180%; font-weight:bold; text-align:center; width:15%; width:10%">' . ($j+1) . '</th>';
				$ScoreDx.='</tr>' . "\n";

				$Tot=0;
				$TotSet=0;
			// ArrowString
				for ($i=0;$i<$nEND;++$i) {
					$TotSerie=0;
					$ScoreDx.='<tr>';
					$ScoreDx.='<th style="font-size:180%; font-weight:bold; text-align:center;">' .  ($i+1) . '</th>';
					for ($j=0;$j<$nARR;++$j)
					{
						$ScoreDx  .= '<td style="font-size:180%; font-weight:bold; text-align:center;">' . $MyPValue=DecodeFromLetter($ArrDx[$i*$nARR+$j])  . '</td>';
						$TotSerie += ValutaArrowString($ArrDx[$i*$nARR+$j]);
					}
					$Tot+=$TotSerie;
					$ScoreDx.='<td style="font-size:180%; font-weight:bold; text-align:right;">' . $TotSerie. '</td>';

					if($MyRowDx->MatchMode==0) {
						$ScoreDx.='<td style="font-size:180%; ' . ($MyRowDx->MatchMode==0 ? 'font-weight:bold; ' : '') . 'text-align:right;">' . $Tot . '</td>';
					} else {
						$TotSet += 	(empty($SetTotDx[$i]) ? 0 : $SetTotDx[$i]);
						$ScoreDx .= '<td style="font-size:160%; font-weight:bold; text-align:right;">' . (empty($SetTotDx[$i]) ? '0' : $SetTotDx[$i]) . '</td>';
						$ScoreDx .= '<td style="font-size:180%; font-weight:bold; text-align:right;">' . $TotSet . '</td>';
					}
					$ScoreDx.='</tr>' . "\n";
				}
			// Tiebreak
				$ScoreDx.='<tr>';
				$ScoreDx.='<th style="font-size:180%; font-weight:bold; text-align:center;">T.B.</th>';
				$ScoreDx.='<td colspan="' . $nARR . '">';
				$ScoreDx.='<table class="Tabella">' . "\n";

                for($endSo=0; $endSo<ceil(max(strlen(trim($TieDx)),strlen(trim($TieSx)))/$nSO); $endSo++) {
                    $ScoreDx.='<tr>';
                    for ($i = 0; $i < $nSO; ++$i) {
                        $ScoreDx .= '<td style="font-size:180%; font-weight:bold; text-align:center;">' . (!empty($TieDx[($endSo*$nSO)+$i]) ? DecodeFromLetter($TieDx[($endSo*$nSO)+$i]):'') . '&nbsp</td>';
                    }
                    $ScoreDx.='</tr>';
                }
				$ScoreDx.='</table>' . "\n";
				$ScoreDx.='</td>';
				$ScoreDx.='<td>&nbsp;</td>';
				if($MyRowDx->MatchMode==0) {
					$ScoreDx.='<td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot . '</td>';
				} else {
					$ScoreDx .= '<td colspan="2" style="font-size:180%; font-weight:bold; text-align:right;">' . $MyRowDx->FinalScore . '</td>';
				}
				$ScoreDx.='</tr>' . "\n";
				$ScoreDx.='</table>' . "\n";

				if($PosArrDx or $PosArrSx) {
				// semaforo dx
					$SemaforoDx
						= '<table class="Tabella">' . "\n";
				// bersagli delle volee
					for ($i=0;$i<$nEND;++$i) {
						$SemaforoDx
							.='<tr>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Matchno='.$MatchDx.'&Team=1&Event='.$Event.'&End='.($i+1).'" class="Target" border="0">'
							. '<td class="FontMedium Bold">' . ($i+1) . '</td>'
							. '</tr>' . "\n";

					}
				// bersaglio del tiebreak (solo se ho almeno una freccia in uno dei due)
					if (trim($TieSx . $TieDx)!='') {
						$SemaforoDx
							.='<tr>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Matchno='.$MatchDx.'&Team=1&Event='.$Event.'&End=-1" class="Target" border="0">'
							. '<td class="FontMedium Bold">T.B.</td>'
							. '</tr>' . "\n";
					}
					$SemaforoDx.='</table>' . "\n";
				}
			}
			$WinnerRow='';
			if($MyRowSx->Winner or $MyRowDx->Winner) {
				$WinnerRow='<tr>'
				. (count($PosArrSx) || count($PosArrDx) ? '<td>&nbsp;</td>' : '')
				. '<td'.($MyRowSx->Winner ? ' class="Winner"' : '').'>'.($MyRowSx->Winner ? get_text('Winner') : '&nbsp;').'</td>'
				. '<td'.($MyRowDx->Winner ? ' class="Winner"' : '').'>'.($MyRowDx->Winner ? get_text('Winner') : '&nbsp;').'</td>'
				. (count($PosArrSx) || count($PosArrDx) ? '<td>&nbsp;</td>' : '')
				. '</tr>';
			}

			$Output
				= '<table class="Tabella">' . "\n"
				. '<tr height="1%">'
				. '<th colspan="4" class="Title" style="font-size:200%; font-weight:bold; text-align:center;">'
				. ($Live?'<img src="'.$CFG->ROOT_DIR.'Common/Images/greendot_anim.gif" align="absmiddle"> ':'')
				.  get_text($EventName,'','',true)
				. ($Live?' - '.get_text('LiveUpdate','Tournament').' <img src="'.$CFG->ROOT_DIR.'Common/Images/greendot_anim.gif" align="absmiddle">':'')
				. ($LiveExists ? ' - <a href="./">'.get_text('GoLive','Tournament').'</a>': '')
				. '</th>'
				. '</tr>' . "\n"
				. '<tr height="1%">'
				. '<th colspan="4" class="Title">' .  get_text($PhaseName . '_Phase') . '</th>'
				. '</tr>' . "\n"

				. '<tr>'
				. (count($PosArrSx) || count($PosArrDx) ? '<td style="font-size:150%; font-weight:bold; text-align:center; width:17%;">&nbsp;</td>' : '')
				. '<td style="font-size:180%; font-weight:bold; text-align:center; width:32%;" onClick="javascript:OpenPopup(\'BioDetailedTeams.php?Id='.$MyRowSx->NationCode.'&Id2='.$MyRowDx->NationCode.'&Cat='.$MyRowSx->Event.'\',\'Biography\',800,600)"><span style="font-size:80%">'.$MyRowSx->NationCode.'</span>&nbsp;-&nbsp;' . strtoupper($MyRowSx->Nation) . '<br><span style="font-size:80%">' . $MyRowSx->Archers .  '</span></td>'
				. '<td style="font-size:180%; font-weight:bold; text-align:center; width:32%;" onClick="javascript:OpenPopup(\'BioDetailedTeams.php?Id='.$MyRowDx->NationCode.'&Id2='.$MyRowSx->NationCode.'&Cat='.$MyRowSx->Event.'\',\'Biography\',800,600)"><span style="font-size:80%">'.$MyRowDx->NationCode.'</span>&nbsp;-&nbsp;' . strtoupper($MyRowDx->Nation) . '<br><span style="font-size:80%">' . $MyRowDx->Archers. '</span></td>'
				. (count($PosArrSx) || count($PosArrDx) ? '<td style="font-size:150%; font-weight:bold; text-align:center; width:17%;">&nbsp;</td>' : '')
				. '</tr>' . "\n"
				. $WinnerRow
				. '<tr>'
				. (count($PosArrSx) || count($PosArrDx) ? '<td valign="top" rowspan="3"><div class="Semafori">' . $SemaforoSx . '</div></td>' : '')
				. '<td valign="top" class="Center" nowrap>' . $ScoreSx . '<br>' . $PhotosSx . '<br>'
				. '<div style="font-size:180%; text-align:justify; margin:10px;">' . nl2br($StorySx) . '</div>'
				. '</td>'
				. '<td valign="top" class="Center" nowrap>' . $ScoreDx . '<br>' . $PhotosDx . '<br>'
				. '<div style="font-size:180%; text-align:justify; margin:10px;">' . nl2br($StoryDx) . '</div>'
				. '</td>'
				. (count($PosArrSx) || count($PosArrDx) ? '<td valign="top" rowspan="3"><div class="Semafori">' . $SemaforoDx . '</div></td>' : '')
				. '</tr>' . "\n"
				. '<tr>'
				. '<td valign="top" colspan="2" style="font-size:150%; text-align:justify; margin:10px;">' . nl2br(htmlentities($ReviewLang1, ENT_NOQUOTES, "UTF-8"))  . '<spacer></spacer></td>'
				. '</tr>' . "\n"
				. '<tr>'
				. '<td valign="top" colspan="2" style="font-size:150%; text-align:justify; margin:10px;">' . nl2br(htmlentities($ReviewLang2, ENT_NOQUOTES, "UTF-8"))  . '<spacer></spacer></td>'
				. '</tr>' . "\n";
			$Output.='</table>' . "\n";
		}

	}

	//exit;

$Errore=0;

header('Content-Type: text/xml');

print '<response>' . "\n";
print '<error>' . $Errore . '</error>' ;
print '<event>' . $Event . '</event>' ;
print '<matchno>' . $MatchNo . '</matchno>';
print '<team>' . $Team . '</team>';
print '<table><![CDATA[';

?>
<form name="Frm" method="get" action="">
<?php
	print $Output;
	print $HiddenArrSx . '<br>';
	print $HiddenTieSx . '<br>';
	print $HiddenArrDx . '<br>';
	print $HiddenTieDx . '<br>';

	print $HiddenEvent . '<br>';
	print $HiddenMatchNo . '<br>';
	print $HiddenReview . '<br>';
	print $HiddenDisplay . '<br>';
?>
</form>
<?php
print ']]></table>' ;
print '</response>' ;
?>