<?php
	define ("debug",false);		// true per l'ouput di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Final/Spot/Common/Config.inc.php');
	require_once('Common/Fun_Phases.inc.php');

/**********************
  Cerco l'evento LIVE
***********************/

	$MatchNo=(isset($_REQUEST['MatchNo']) ? intval($_REQUEST['MatchNo']/2)*2 : -1);
	$Event=(empty($_REQUEST['Event']) ? '<![CDATA[]]>' : $_REQUEST['Event']);
	$Team=(isset($_REQUEST['Team']) ? intval($_REQUEST['Team']) : -1);
	$Lock=(isset($_REQUEST['Lock']) ? intval($_REQUEST['Lock']) : 0);
	$LiveExists=false;

	$Live=false;

	if ($x=FindLive()) {
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
	$HiddenDisplay = '<input type="hidden" id="d_Match" value="' . $MatchNo . '">
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

		$MyTargetComplete = TargetIsComplete(GetTargetType($Event));
		$MyTargetHitMiss = strstr(GetTargetType($Event,0), 'TrgHM');
		$MyTargetField = strstr(GetTargetType($Event,0), 'TrgField');
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
		$Select
			= "SELECT "
			. "e.EnId AS Athlete, e.EnCode as Code, e.EnName AS Name, upper(e.EnFirstName) AS FirstName, c.CoCode AS NationCode, c.CoName AS Nation, "
			. "f.FinMatchNo AS MatchNo, f.FinWinLose as Winner, IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS FinalScore, f.FinScore AS Score, f.FinSetScore as SetScore, f.FinSetPoints as SetPoints, f.FinTie as Tie, f.FinArrowString AS ArrowString, f.FinArrowPosition AS ArrPos, f.FinTieBreak AS TieBreak, f.FinTiePosition AS TiePos, "
			. "r.RevLanguage1 AS Review1, r.RevLanguage2 AS Review2, UNIX_TIMESTAMP(IFNULL(r.RevDateTime,0)) As ReviewUpdate, "
			. "q.QuScore as QualScore, i.IndRank as QualRank, "
			. "e2.EnId AS OppAthlete, e.EnCode as OppCode, e2.EnName AS OppName, upper(e2.EnFirstName) AS OppFirstName, c2.CoCode AS OppNationCode, c2.CoName AS OppNation, "
			. "f2.FinMatchNo AS OppMatchNo, f2.FinWinLose as OppWinner, IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) AS OppFinalScore, f2.FinScore AS OppScore, f2.FinSetScore as OppSetScore, f2.FinSetPoints as OppSetPoints, f2.FinTie as OppTie, f2.FinArrowString AS OppArrowString, f2.FinArrowPosition AS OppArrPos, f2.FinTieBreak AS OppTieBreak, f2.FinTiePosition AS OppTiePos, "
			. "r2.RevLanguage1 AS OppReview1, r2.RevLanguage2 AS OppReview2, UNIX_TIMESTAMP(IFNULL(r2.RevDateTime,0)) As OppReviewUpdate, "
			. "q2.QuScore as OppQualScore, i2.IndRank as OppQualRank, "
			. "EvCode AS Event, EvEventName AS EventName, GrPhase as Phase, EvMatchMode as MatchMode, @elimination:=pow(2, ceil(log2(GrPhase))+1) & EvMatchArrowsNo "
			. " , if(@elimination, EvElimEnds, EvFinEnds) CalcEnds "
			. " , if(@elimination, EvElimArrows, EvFinArrows) CalcArrows "
			. " , if(@elimination, EvElimSO, EvFinSO) CalcSO "
			. "FROM Events "
			. "INNER JOIN Finals AS f ON EvTournament=f.FinTournament AND EvCode=f.FinEvent AND EvTeamEvent='0' "
			. 'INNER JOIN Individuals AS i ON f.FinAthlete=i.IndId AND f.FinEvent=i.IndEvent AND f.FinTournament=i.IndTournament '
			. "LEFT JOIN Entries AS e ON f.FinAthlete=e.EnId AND f.FinTournament=e.EnTournament "
			. "LEFT JOIN Qualifications AS q ON e.EnId=q.QuId "
			. "LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
			. "LEFT JOIN Reviews AS r ON f.FinEvent=r.RevEvent AND f.FinMatchNo=r.RevMatchNo AND f.FinTournament=r.RevTournament AND r.RevTeamEvent=0 "
			. "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament "
			. 'INNER JOIN Individuals AS i2 ON f2.FinAthlete=i2.IndId AND f2.FinEvent=i2.IndEvent AND f2.FinTournament=i2.IndTournament '
			. "LEFT JOIN Entries AS e2 ON f2.FinAthlete=e2.EnId AND f2.FinTournament=e2.EnTournament "
			. "LEFT JOIN Qualifications AS q2 ON e2.EnId=q2.QuId "
			. "LEFT JOIN Countries AS c2 ON e2.EnCountry=c2.CoId AND e2.EnTournament=c2.CoTournament "
			. "LEFT JOIN Reviews AS r2 ON f2.FinEvent=r2.RevEvent AND f2.FinMatchNo=r2.RevMatchNo AND f2.FinTournament=r2.RevTournament AND r2.RevTeamEvent=0 "

			. "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo "
			. "WHERE EvCode=" . StrSafe_DB($Event) . " AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "AND f.FinMatchNo =" . 	StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) . " ";

		//print $Select . '<br>';exit;
		if (debug) print $Select . '<br>';
		//exit;

		$Rs=safe_r_sql($Select);

		$ArrSx = '';
		$ArrDx = '';

		$TieSx = '';
		$TieDx = '';

		$PosArrSx = '';
		$PosArrDx = '';

		$PosTieSx = '';
		$PosTieDx = '';

		$ReviewLang1 = '';
		$ReviewLang2 = '';

		$StorySx = '';
		$StoryDx = '';

		if (safe_num_rows($Rs)==1) {
			$MyRow=safe_fetch($Rs);

			//Sistemiamo i numeri di  frecce
			$nEND=$MyRow->CalcEnds;				// Numero di Volee
			$nARR=$MyRow->CalcArrows;				// Numero di frecce

			$maxArrows=$nEND*$nARR;
			$nTieBreak=$MyRow->CalcSO;
/**********
  Scontri
***********/
			$SetTieSx=0;
			$SetTieDx=0;
			/* Preparo le variabili per i due scontri */
			$ArrSx =str_pad($MyRow->ArrowString,$maxArrows);
			$TieSx=str_pad($MyRow->TieBreak,$nTieBreak);
			if(trim($MyRow->ArrPos,'|') or trim($MyRow->OppArrPos,'|')) {
				$PosArrSx=explode('|',str_pad($MyRow->ArrPos,$maxArrows-substr_count($MyRow->ArrPos, "|"),"|"));
				$PosTieSx=explode('|',str_pad($MyRow->TiePos,$nTieBreak-substr_count($MyRow->TiePos, "|"),"|"));
				$PosArrDx=explode('|',str_pad($MyRow->OppArrPos,$maxArrows-substr_count($MyRow->OppArrPos, "|"),"|"));
				$PosTieDx=explode('|',str_pad($MyRow->OppTiePos,$nTieBreak-substr_count($MyRow->OppTiePos, "|"),"|"));
			}

			$ArrDx=str_pad($MyRow->OppArrowString,$maxArrows);
			$TieDx=str_pad($MyRow->OppTieBreak,$nTieBreak);

			$ReviewLang1 = $MyRow->Review1;
			$ReviewLang2 = $MyRow->Review2;
			$HiddenReview = '<input type="hidden" id="Review" value="' . $MyRow->ReviewUpdate . '">';

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
/*************************
Conteggio i punti di Set
*************************/
			$SetPointSx=array();
			$SetPointDx=array();
			$SetTotSx=array();
			$SetTotDx=array();
			if($MyRow->MatchMode!=0)
			{
				for($i=0; $i<$maxArrows; $i=$i+$nARR)		//Cicla per tutte le volee dell'incontro
				{
					$SetPointSx[] = ValutaArrowString(substr($MyRow->ArrowString,$i,$nARR));
					$SetPointDx[] = ValutaArrowString(substr($MyRow->OppArrowString,$i,$nARR));
					if(strlen(trim(substr($MyRow->ArrowString,$i,$nARR)))==$nARR && strlen(trim(substr($MyRow->OppArrowString,$i,$nARR)))==$nARR && ctype_upper(substr($MyRow->ArrowString,$i,$nARR)) && ctype_upper(substr($MyRow->OppArrowString,$i,$nARR)))
					{
						if($SetPointSx[$i/$nARR]>$SetPointDx[$i/$nARR])
						{
							$SetTotSx[]= 2;
							$SetTotDx[]= 0;
						}
						else if($SetPointSx[$i/$nARR]<$SetPointDx[$i/$nARR])
						{
							$SetTotSx[]= 0;
							$SetTotDx[]= 2;
						}
						else
						{
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
			$StoryQuery = "SELECT IF(f.FinAthlete=" . StrSafe_DB($MyRow->Athlete) . ", 0, 1) AS SxDx, f.FinMatchNo, GrPhase as Phase, "
				. "IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS Score, f.FinTie, "
				. "EnFirstName as OppFirstName, EnName as OppName, CoCode as Country, IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) as OppScore, f2.FinTie as OppTie "
			. "FROM Finals AS f "
				. "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament "
				. "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo "
				. "INNER JOIN Events AS e ON EvTournament=f.FinTournament AND EvCode=f.FinEvent AND EvTeamEvent='0' "
				. "LEFT JOIN Entries  ON f2.FinAthlete=EnId AND f2.FinTournament=EnTournament "
				. "LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
			. "WHERE f.FinTournament = " . StrSafe_DB($_SESSION['TourId']) . " "
				. "AND (f.FinAthlete=" . StrSafe_DB($MyRow->Athlete) . " OR f.FinAthlete=" . StrSafe_DB($MyRow->OppAthlete) . ") "
				. "AND f.FinEvent=" . StrSafe_DB($Event) . " AND GrPhase > " . StrSafe_DB($MyRow->Phase). " "
			. "ORDER BY IF(f.FinAthlete=" . StrSafe_DB($MyRow->Athlete) . ", 0, 1),f.FinMatchNo";
			//echo $StoryQuery;exit;
			$RsStory=safe_r_sql($StoryQuery);
			if(safe_num_rows($RsStory)>0)
			{
				while($MyRowStory=safe_fetch($RsStory))
				{
					if($MyRowStory->SxDx==0)
						$StorySx .= '<b>' . get_text($MyRowStory->Phase . '_Phase') . "</b>: "
							. '<em>' . ($MyRowStory->Score==0 && $MyRowStory->FinTie==2 ?  '' :  ' ' .$MyRowStory->Score) . ($MyRowStory->FinTie==1 ? '*' : '')
							. "</em> - "
							. ($MyRowStory->FinTie==2 ? 'Bye -' : ($MyRowStory->OppScore==0 && $MyRowStory->FinTie==2 ? '' : ' ' . $MyRowStory->OppScore) . ($MyRowStory->OppTie==1 ? '*' : '')) . " " . $MyRowStory->OppFirstName . " " . $MyRowStory->OppName . "\n";
					if($MyRowStory->SxDx==1)
						$StoryDx .= '<b>' . get_text($MyRowStory->Phase . '_Phase') . "</b>: "
							. '<em>' . ($MyRowStory->Score==0 && $MyRowStory->FinTie==2 ? '' : ' ' .$MyRowStory->Score) . ($MyRowStory->FinTie==1 ? '*' : '')
							. "</em> - "
							. ($MyRowStory->FinTie==2 ? 'Bye -' : ($MyRowStory->OppScore==0 && $MyRowStory->FinTie==2 ? '' : ' ' . $MyRowStory->OppScore) . ($MyRowStory->OppTie==1 ? '*' : '')) . " " . $MyRowStory->OppFirstName . " " . $MyRowStory->OppName . "\n";
				}
			}
			$StorySx .= '<b>' . get_text('QualRound') . "</b>: " . $MyRow->QualScore . " (" .  $MyRow->QualRank .")\n";
			$StoryDx .= '<b>' . get_text('QualRound') . "</b>: " . $MyRow->OppQualScore . " (" .  $MyRow->OppQualRank .")\n";

/********************************
  Atleta di Sinistra --> Athlete
 ********************************/
			//if (!is_null($MyRow["Athlete"]))
			{
				$PhotoSx = $CFG->ROOT_DIR.'Partecipants-exp/common/photo.php?mode=y&val=150&id=' . $MyRow->Athlete;

				$HiddenArrSx = '<input type="hidden" id="ArrSx" value="' . trim($ArrSx) . '">';
				$HiddenTieSx = '<input type="hidden" id="TieSx" value="' . trim($TieSx) . '">';

				$ScoreSx
					= '<table class="Tabella">' . "\n"
					. '<tr>'
					. '<th rowspan="2"  style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('End (volee)') . '</th>'
					. '<th colspan="' . $nARR . '"   style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('Arrow') . '</th>'
					. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text(($MyRow->MatchMode==0 ? 'TotalProg':'SetTotal'),'Tournament') . '</th>';
				if($MyRow->MatchMode==0) {
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
					if($MyRow->MatchMode==0) {
						$ScoreSx.='<td style="font-size:180%; ' . ($MyRow->MatchMode==0 ? 'font-weight:bold; ' : '') . 'text-align:right;">' . $Tot . '</td>';
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
				$ScoreSx.='<tr>';
				for ($i=0;$i<$nTieBreak;++$i)
					$ScoreSx .= '<td style="font-size:180%; font-weight:bold; text-align:center;">' . DecodeFromLetter($TieSx[$i]) . '&nbsp</td>';
				$ScoreSx.='</tr>' . "\n";
				$ScoreSx.='</table>' . "\n";
				$ScoreSx.='</td>';
				$ScoreSx.='<td>&nbsp;</td>';
				if($MyRow->MatchMode==0) {
					$ScoreSx.='<td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot . '</td>';
				} else {
					$ScoreSx .= '<td colspan="2" style="font-size:180%; font-weight:bold; text-align:right;">' . $MyRow->FinalScore . '</td>';
				}
				$ScoreSx.='</tr>' . "\n";
				$ScoreSx.='</table>' . "\n";

/***********************
  Semaforo di Sinistra
***********************/
				if($PosArrSx OR $PosArrDx) {
				// semaforo sx
					$SemaforoSx
						= '<table class="Tabella">' . "\n";
				// bersagli delle volee
					for ($i=0;$i<$nEND;++$i)
					{
						$Tmp = '';

						for ($j=0;$j<$nARR;++$j)
						{
							if(@array_key_exists (($i*$nARR+$j), $PosArrSx))
									$Tmp .= "&amp;Arrows[]=" .  $PosArrSx[$i*$nARR+$j];
						}

						if($MyTargetHitMiss)
							$Tmp .='&amp;HMOUT=1';
						else if($MyTargetField)
							$Tmp .='&amp;FIELD=1';

						$SemaforoSx
							.='<tr>'
							. '<td class="FontMedium Bold Right">' . ($i+1) . '</td>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Match='.$MatchSx.'&Team=0&Event='.$Event.'&Size=120' . $Tmp . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target" border="0">'
							. '</td>'
							. '</tr>' . "\n";

					}
				// bersaglio del tiebreak
					// bersaglio del tiebreak (solo se ho almeno una freccia in uno dei due)
					if (trim($TieSx . $TieDx)!='')
					{
						$Tmp = '';

						for($j=0;$j<$nTieBreak;$j++)
						{
							if(@array_key_exists($j,$PosTieSx))
								$Tmp .= "&amp;Arrows[]=" . $PosTieSx[$j];
						}

						if($MyTargetHitMiss)
							$Tmp .='&amp;HMOUT=1';
						else if($MyTargetField)
							$Tmp .='&amp;FIELD=1';

						$SemaforoSx
							.='<tr>'
							. '<td class="FontMedium Bold Right">T.B.</td>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Match='.$MatchSx.'&Team=0&Event='.$Event.'&Size=120' . $Tmp . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target" border="0">'
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
				$PhotoDx = $CFG->ROOT_DIR.'Partecipants-exp/common/photo.php?mode=y&val=150&id=' . $MyRow->OppAthlete;

				$HiddenArrDx = '<input type="hidden" id="ArrDx" value="' . trim($ArrDx) . '">';
				$HiddenTieDx = '<input type="hidden" id="TieDx" value="' . trim($TieDx) . '">';

				$ScoreDx
					= '<table class="Tabella">' . "\n"
					. '<tr>'
					. '<th rowspan="2"  style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('End (volee)') . '</th>'
					. '<th colspan="' . $nARR . '"   style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('Arrow') . '</th>'
					. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text(($MyRow->MatchMode==0 ? 'TotalProg':'SetTotal'),'Tournament') . '</th>';
				if($MyRow->MatchMode==0) {
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
					if($MyRow->MatchMode==0) {
						$ScoreDx.='<td style="font-size:180%; ' . ($MyRow->MatchMode==0 ? 'font-weight:bold; ' : '') . 'text-align:right;">' . $Tot . '</td>';
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
				$ScoreDx.='<tr>';
				for ($i=0;$i<$nTieBreak;++$i)
					$ScoreDx.='<td style="font-size:180%; font-weight:bold; text-align:center;">' . DecodeFromLetter($TieDx[$i]) . '&nbsp</td>';
				$ScoreDx.='</tr>' . "\n";
				$ScoreDx.='</table>' . "\n";
				$ScoreDx.='</td>';
				$ScoreDx.='<td>&nbsp;</td>';
				if($MyRow->MatchMode==0) {
					$ScoreDx.='<td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot . '</td>';
				} else {
					$ScoreDx .= '<td colspan="2" style="font-size:180%; font-weight:bold; text-align:right;">' . $MyRow->OppFinalScore . '</td>';
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

						for ($j=0;$j<$nARR;++$j)
						{
							if(@array_key_exists (($i*$nARR+$j), $PosArrDx))
									$Tmp .= "&amp;Arrows[]=" .  $PosArrDx[$i*$nARR+$j];
						}

						if($MyTargetHitMiss)
							$Tmp .='&amp;HMOUT=1';
						else if($MyTargetField)
							$Tmp .='&amp;FIELD=1';

						$SemaforoDx
							.='<tr>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Match='.$MatchDx.'&Team=0&Event='.$Event.'&Size=120' . $Tmp . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target" border="0">'
							. '<td class="FontMedium Bold">' . ($i+1) . '</td>'
							. '</tr>' . "\n";

					}
				// bersaglio del tiebreak (solo se ho almeno una freccia in uno dei due)
					if (trim($TieSx . $TieDx)!='')
					{
						$Tmp = '';

						for($j=0;$j<$nTieBreak;$j++)
						{
							if(@array_key_exists($j,$PosTieDx))
								$Tmp .= "&amp;Arrows[]=" . $PosTieDx[$j];
						}

						if($MyTargetHitMiss)
							$Tmp .='&amp;HMOUT=1';
						else if($MyTargetField)
							$Tmp .='&amp;FIELD=1';

						$SemaforoDx
							.='<tr>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Match='.$MatchDx.'&Team=0&Event='.$Event.'&Size=120' . $Tmp . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target" border="0">'
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
			if($MyRow->Winner or $MyRow->OppWinner) {
				$WinnerRow='<tr>'
				. ($PosArrDx || $PosArrSx ? '<td>&nbsp;</td>' : '')
				. '<td'.($MyRow->Winner ? ' class="Winner"' : '').'>'.($MyRow->Winner ? get_text('Winner') : '&nbsp;').'</td>'
				. '<td'.($MyRow->OppWinner ? ' class="Winner"' : '').'>'.($MyRow->OppWinner ? get_text('Winner') : '&nbsp;').'</td>'
				. ($PosArrDx || $PosArrSx ? '<td>&nbsp;</td>' : '')
				. '</tr>';
			}

			$Output
				= '<table class="Tabella">' . "\n"
				. '<tr height="1%">'
				. '<th colspan="4" class="Title" style="font-size:200%; font-weight:bold; text-align:center;">'
				. ($Live?'<img src="'.$CFG->ROOT_DIR.'Common/Images/greendot_anim.gif" align="absmiddle"> ':'')
				.  get_text($MyRow->EventName,'','',true)
				. ($Live?' - '.get_text('LiveUpdate','Tournament').' <img src="'.$CFG->ROOT_DIR.'Common/Images/greendot_anim.gif" align="absmiddle">':'')
				. ($LiveExists ? ' - <a href="./">'.get_text('GoLive','Tournament').'</a>': '')
				. '</th>'
				. '</tr>' . "\n"
				. '<tr height="1%">'
				. '<th colspan="4" class="Title">' .  get_text($MyRow->Phase . '_Phase') . '</th>'
				. '</tr>' . "\n"
				. '<tr height="1%">'
				. ($PosArrSx || $PosArrDx ? '<td style="font-size:150%; font-weight:bold; text-align:center; width:17%;">' . $MyRow->Nation . '</td>' : '')
				. '<td style="font-size:180%; font-weight:bold; text-align:center; width:32%;">' . $MyRow->FirstName . ' ' . $MyRow->Name . '<br>(' . $MyRow->Nation. ')</td>'
				. '<td style="font-size:180%; font-weight:bold; text-align:center; width:32%;">' . $MyRow->OppFirstName . ' ' . $MyRow->OppName  . '<br>(' . $MyRow->OppNation. ')</td>'
				. ($PosArrSx || $PosArrDx ? '<td style="font-size:150%; font-weight:bold; text-align:center; width:17%;">' . $MyRow->OppNation . '</td>' : '')
				. '</tr>' . "\n"
				. $WinnerRow
				. '<tr>'
				. ($PosArrSx || $PosArrDx ? '<td valign="top" rowspan="3">' . $SemaforoSx . '</td>' : '')
				. '<td valign="top" class="Center" nowrap>' . $ScoreSx . '<br>' . ($PhotoSx!='' ? '<img src="' . $PhotoSx . '" border="0" width="150px">' : '') . '<br>'
				. '<div style="font-size:180%; text-align:justify; margin:10px;">' . nl2br($StorySx) . '</div>'
				. '</td>'
				. '<td valign="top" class="Center" nowrap>' . $ScoreDx . '<br>' . ($PhotoDx!='' ? '<img src="' . $PhotoDx . '" border="0" width="150px">' : '') . '<br>'
				. '<div style="font-size:180%; text-align:justify; margin:10px;">' . nl2br($StoryDx) . '</div>'
				. '</td>'
				. ($PosArrSx || $PosArrDx ? '<td valign="top" rowspan="3">' . $SemaforoDx . '</td>' : '')
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
		$Select="select distinct count(*) TeamMates, Events.* from Events inner join TeamComponent on EvTournament=TcTournament and EvTeamEvent=1 and EvCode=TcEvent where EvTournament={$_SESSION['TourId']} and EvCode='$Event' group by TcCoId, TcSubTeam";
		$t=safe_r_sql($Select);
		$EVENT=safe_fetch($t);

		$Select
			= "SELECT TfMatchNo AS MatchNo, TfWinLose as Winner, TfEvent AS Event, EvEventName AS EventName, GrPhase as Phase, CONCAT(TfTeam,'|',TfSubTeam) AS Athlete, EvMatchMode as MatchMode, "
			. "RevLanguage1, RevLanguage2, UNIX_TIMESTAMP(RevDateTime) as ReviewUpdate, "
			. "IF(EvMatchMode=0,TfScore,TfSetScore) AS FinalScore, TfScore AS Score, TfSetScore as SetScore, TfSetPoints as SetPoints, TfArrowString AS ArrowString,TfArrowPosition AS ArrPos,"
			. "TeScore, TeRank, GROUP_CONCAT(CONCAT(EnName, ' ', UPPER(EnFirstName)) SEPARATOR ', ') as Archers, "
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
			. "AND TfEvent=" . StrSafe_DB($Event) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
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
			if(trim($MyRowSx->ArrPos,'|')) {
				//foreach(explode('|',str_pad($MyRowSx->ArrPos,$nMaxArrows-substr_count($MyRowSx->ArrPos, "|"),"|")) as $pos) if($pos) $PosArrSx=$pos;
				//foreach(explode('|',str_pad($MyRowSx->TiePos,$nSO-substr_count($MyRowSx->TiePos, "|"),"|")) as $pos) if($pos) $PosTieSx=$pos;
				$PosArrSx = explode('|',str_pad($MyRowSx->ArrPos,$nMaxArrows-substr_count($MyRowSx->ArrPos, "|"),"|"));
				$PosTieSx = explode('|',str_pad($MyRowSx->TiePos,$nSO-substr_count($MyRowSx->TiePos, "|"),"|"));

			}

			$ReviewLang1 = $MyRowSx->RevLanguage1;
			$ReviewLang2 = $MyRowSx->RevLanguage2;
			$HiddenReview = '<input type="hidden" id="Review" value="' . $MyRowSx->ReviewUpdate . '">';

			if ($MyRowDx=safe_fetch($Rs))
			{
				$ArrDx=str_pad($MyRowDx->ArrowString,MaxFinTeamArrows);
				$TieDx=str_pad($MyRowDx->TieBreak,$nSO);
				if(trim($MyRowDx->ArrPos,'|') or trim($MyRowSx->ArrPos,'|')) {
					$PosArrDx = explode('|',str_pad($MyRowDx->ArrPos,$nMaxArrows-substr_count($MyRowDx->ArrPos, "|"),"|"));
					$PosTieDx = explode('|',str_pad($MyRowDx->TiePos,3+$nSO-substr_count($MyRowDx->TiePos, "|"),"|"));
				}
			}
			elseif($MyRowSx->MatchNo%2==1)
			{
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

				for($i=0; $i<$maxArrows; $i=$i+$nARR)		//Cicla per tutte le volee dell'incontro
				{
					$SetPointSx[] = ValutaArrowString(substr($MyRowSx->ArrowString,$i,$nARR));
					$SetPointDx[] = ValutaArrowString(substr($MyRowDx->ArrowString,$i,$nARR));
					if(strlen(trim(substr($MyRowSx->ArrowString,$i,$nARR)))==$nARR && strlen(trim(substr($MyRowDx->ArrowString,$i,$nARR)))==$nARR && ctype_upper(substr($MyRowSx->ArrowString,$i,$nARR)) && ctype_upper(substr($MyRowDx->ArrowString,$i,$nARR)))
					{
						if($SetPointSx[$i/$nARR]>$SetPointDx[$i/$nARR])
						{
							$SetTotSx[]= 2;
							$SetTotDx[]= 0;
						}
						else if($SetPointSx[$i/$nARR]<$SetPointDx[$i/$nARR])
						{
							$SetTotSx[]= 0;
							$SetTotDx[]= 2;
						}
						else
						{
							$SetTotSx[]= 1;
							$SetTotDx[]= 1;
						}
					}
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
			. "WHERE f.TfTournament = " . StrSafe_DB($_SESSION['TourId']) . " "
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
				$StorySx .= '<b>' . get_text('QualRound') . "</b>: " . $MyRowSx->TeScore . ($MyRowSx->TeRank >0 ? " (" .  $MyRowSx->TeRank .")" : '' ) . "\n";
				$StoryDx .= '<b>' . get_text('QualRound') . "</b>: " . $MyRowDx->TeScore . ($MyRowDx->TeRank >0 ? " (" .  $MyRowDx->TeRank .")" : '' ) . "\n";
			}

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

			if ($MyRowSx)
			{
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
				for ($i=0;$i<$nEND;++$i)
				{
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
				$ScoreSx.='<tr>';
				for ($i=0;$i<$nSO;++$i)
					$ScoreSx .= '<td style="font-size:180%; font-weight:bold; text-align:center;">' . DecodeFromLetter($TieSx[$i]) . '&nbsp</td>';
				$ScoreSx.='</tr>' . "\n";
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
						$Tmp = '';
						for ($j=0;$j<$nARR;++$j)
						{
							if(@array_key_exists (($i*$nARR+$j), $PosArrSx))
									$Tmp .= "&amp;Arrows[]=" .  $PosArrSx[$i*$nARR+$j];
						}
						$SemaforoSx
							.='<tr>'
							. '<td class="FontMedium Bold Right">' . ($i+1) . '</td>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Match='.$MatchSx.'&Team=1&Event='.$Event.'&Size=120' . $Tmp . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target" border="0">'
							. '</td>'
							. '</tr>' . "\n";
					}
				// bersaglio del tiebreak
					// bersaglio del tiebreak (solo se ho almeno una freccia in uno dei due)
					if (trim($TieSx . $TieDx)!='')
					{

						$Tmp = '';

						for($j=0;$j<$nSO;$j++)
						{
							if(@array_key_exists($j,$PosTieSx) && $PosTieSx[$j]!='')
								$Tmp .= "&amp;Arrows[]=" . $PosTieSx[$j];
						}

						$SemaforoSx
							.='<tr>'
							. '<td class="FontMedium Bold Right">T.B.</td>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Match='.$MatchSx.'&Team=1&Event='.$Event.'&Size=120' . $Tmp . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target" border="0">'
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
				for ($i=0;$i<$nEND;++$i)
				{
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
				$ScoreDx.='<tr>';
				for ($i=0;$i<$nSO;++$i)
					$ScoreDx .= '<td style="font-size:180%; font-weight:bold; text-align:center;">' . DecodeFromLetter($TieDx[$i]) . '&nbsp</td>';
				$ScoreDx.='</tr>' . "\n";
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
					for ($i=0;$i<$nEND;++$i)
					{
						$Tmp = '';

						for ($j=0;$j<$nARR;++$j)
						{
							if(@array_key_exists (($i*$nARR+$j), $PosArrDx))
									$Tmp .= "&amp;Arrows[]=" .  $PosArrDx[$i*$nARR+$j];
						}

						$SemaforoDx
							.='<tr>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Match='.$MatchDx.'&Team=1&Event='.$Event.'&Size=120' . $Tmp . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target" border="0">'
							. '<td class="FontMedium Bold">' . ($i+1) . '</td>'
							. '</tr>' . "\n";

					}
				// bersaglio del tiebreak (solo se ho almeno una freccia in uno dei due)
					if (trim($TieSx . $TieDx)!='')
					{
						$Tmp = '';

						for($j=0;$j<$nSO;$j++)
						{
							if(@array_key_exists($j,$PosTieDx) && $PosTieDx[$j]!='')
								$Tmp .= "&amp;Arrows[]=" . $PosTieDx[$j];
						}

						$SemaforoDx
							.='<tr>'
							. '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Match='.$MatchDx.'&Team=1&Event='.$Event.'&Size=120' . $Tmp . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target" border="0">'
							. '<td class="FontMedium Bold">T.B.</td>'
							. '</tr>' . "\n";
					}
					$SemaforoDx.='</table>' . "\n";
				}
			}
			$WinnerRow='';
			if($MyRowSx->Winner or $MyRowDx->Winner) {
				$WinnerRow='<tr>'
				. ($PosArrDx || $PosArrSx ? '<td>&nbsp;</td>' : '')
				. '<td'.($MyRowSx->Winner ? ' class="Winner"' : '').'>'.($MyRowSx->Winner ? get_text('Winner') : '&nbsp;').'</td>'
				. '<td'.($MyRowDx->Winner ? ' class="Winner"' : '').'>'.($MyRowDx->Winner ? get_text('Winner') : '&nbsp;').'</td>'
				. ($PosArrDx || $PosArrSx ? '<td>&nbsp;</td>' : '')
				. '</tr>';
			}

			$Output
				= '<table class="Tabella">' . "\n"
				. '<tr height="1%">'
				. '<th colspan="4" class="Title" style="font-size:200%; font-weight:bold; text-align:center;">'
				. ($Live?'<img src="'.$CFG->ROOT_DIR.'Common/Images/greendot_anim.gif" align="absmiddle"> ':'')
				.  get_text($EventName,'','',true)
				. ($Live?' - '.get_text('LiveUpdate','Tournament').' <img src="'.$CFG->ROOT_DIR.'Common/Images/greendot_anim.gif" align="absmiddle">':'')
				. ($LiveExists ? ' - <a href="./">Go Life</a>': '')
				. '</th>'
				. '</tr>' . "\n"
				. '<tr height="1%">'
				. '<th colspan="4" class="Title">' .  get_text($PhaseName . '_Phase') . '</th>'
				. '</tr>' . "\n"

				. '<tr>'
				. ($PosArrDx || $PosArrSx ? '<td style="font-size:150%; font-weight:bold; text-align:center; width:17%;">&nbsp;</td>' : '')
				. '<td style="font-size:180%; font-weight:bold; text-align:center; width:32%;"><span style="font-size:80%">'.$MyRowSx->NationCode.'</span>&nbsp;-&nbsp;' . strtoupper($MyRowSx->Nation) . '<br><span style="font-size:80%">' . $MyRowSx->Archers .  '</span></td>'
				. '<td style="font-size:180%; font-weight:bold; text-align:center; width:32%;"><span style="font-size:80%">'.$MyRowDx->NationCode.'</span>&nbsp;-&nbsp;' . strtoupper($MyRowDx->Nation) . '<br><span style="font-size:80%">' . $MyRowDx->Archers. '</span></td>'
				. ($PosArrDx || $PosArrSx ? '<td style="font-size:150%; font-weight:bold; text-align:center; width:17%;">&nbsp;</td>' : '')
				. '</tr>' . "\n"
				. $WinnerRow
				. '<tr>'
				. ($PosArrDx || $PosArrSx ? '<td valign="top" rowspan="3">' . $SemaforoSx . '</td>' : '')
				. '<td valign="top" class="Center">' . $ScoreSx . '<br>'
				. '<div style="font-size:180%; text-align:justify; margin:10px;">' . nl2br($StorySx) . '</div>'
				. '</td>'
				. '<td valign="top" class="Center">' . $ScoreDx . '<br>'
				. '<div style="font-size:180%; text-align:justify; margin:10px;">' . nl2br($StoryDx) . '</div>'
				. '</td>'
				. ($PosArrDx || $PosArrSx ? '<td valign="top" rowspan="3">' . $SemaforoDx . '</td>' : '')
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