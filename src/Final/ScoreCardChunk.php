<?php
//xx
// ATTENZIONE: Questo è solo un chunk!!!!!

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Fun_Final.local.inc.php');

	CheckTourSession(true);

	$d_Event=isset($_REQUEST['d_Event']) ? $_REQUEST['d_Event'] : null;
	$d_Match=isset($_REQUEST['d_Match']) ? $_REQUEST['d_Match'] : null;
	$team=isset($_REQUEST['d_Team']) ? $_REQUEST['d_Team'] : 0;
	$chunkMode=isset($_REQUEST['d_Mode']) ? $_REQUEST['d_Mode'] : 0;

	if (is_null($d_Event) || is_null($d_Match) || !CheckTourSession())
		exit;

	if ($d_Match=='' || $d_Event=='')
		exit;

// tiro fuori le info x lo scontro
	$rs=GetFinMatches($d_Event,null,$d_Match,$team,false);
//Carico il vettore dei dati validi
	$validData=GetMaxScores($d_Event, $d_Match, $team);

	if (safe_num_rows($rs)!=1)
		exit;

	$myRow=safe_fetch($rs);

// righe e colonne e so nel caso di individuali cumulativi
	$rows=4;
	$cols=3;
	$so=1;

	list($rows,$cols,$so)=CalcScoreRowsColsSO($myRow);

// i due score da stampare a video
	$scores=array(1=>'','');

/*
 * $setPoints contiene contiene i progressivi dei set.
 * Quando entrambi gli score sono riempiti, si calcolerà il vincitore di ogni set
 * e si aggiornerà la cella sostituendo il riferimento nella stringa.
 * I riferimenti sono nella forma {sp_$m_$set} con $m il match (1 o 2) e $set il set
 */
	$setPoints=array(1=>array_fill(0,$rows,0),array_fill(0,$rows,0));
	$setLoaded=array(1=>array_fill(0,$rows,false),array_fill(0,$rows,false));

/*
 * Come $setPoints ma con i totali del set. I riferimento sono nella forma {st_$m_$set}
 */
	$setTotals=array(1=>array_fill(0,$rows,0),array_fill(0,$rows,0));

	for($m=1;$m<=2;++$m)
	{
		$out='';
		if ($myRow->{'name' . $m}!='***')
		{
			$out.='<table class="Tabella">' . "\n";
			$out.='<tr><th colspan="' . (5+$cols). '">' . $myRow->{'name' .$m} . '</th></tr>' . "\n";

			if($chunkMode == 0 || $chunkMode == 1)
			{

				if($chunkMode == 1)
				{
					$i=0;
					$size = round($_SESSION["WINHEIGHT"]*0.45,-1)-1;
					$out .= '<tr style="height:' . floor($size/$cols). 'px;">';
					$out .= '<td class="Bold Center" onClick="clickStar(\'spot_' . $myRow->{'match'.$m} . '_0\', false)">' . ($i+1) . '</td>';
					$out .= '<td id="spot_' . $myRow->{'match'.$m} . '_0" class="Right FontMedium" onClick="clickStar(this.id, true)">&nbsp;</td>';
					$out .= '<td rowspan="' . $cols . '" colspan="' . (4+$cols). '" class="Center">';
					$out .= '<img id="tgtImage_'. $myRow->{'match'.$m} . '" style="position:relative; cursor:crosshair;" src="' . $CFG->ROOT_DIR .'Common/target.php?Event=' . $d_Event . '&Match=' . $d_Match . '&Team=' . $team . '&Size=' . $size . '&ts=' . date('U'). '" onclick="javascript:targetClick(' . $myRow->{'match'.$m} . ', ' . $size . ', event);"  height="' . $size . '" width="' . $size . '"/>';
					$out .= '</td>';
					$out .= '</tr>' . "\n";
					for($i=1;$i<$cols;$i++)
						$out .= '<tr style="height:' . floor($size/$cols). 'px;"><td class="Bold Center" onClick="clickStar(\'spot_' . $myRow->{'match'.$m} . '_' . $i . '\', false)">' . ($i+1) . '</td><td id="spot_' . $myRow->{'match'.$m} . '_' . $i . '" class="Right FontMedium" onClick="clickStar(this.id, true)">&nbsp;</td></tr>' . "\n";

				}
			// header score
				$out.='<tr>';
					$out.='<th></th>';
					for ($i=0;$i<$cols;++$i)
					{
						$out.='<th>' . ($i+1) . '</th>';
					}
					$out.='<th>' . (get_text(($myRow->matchMode==0 ? 'TotalProg' : 'SetTotal'),'Tournament')) . '</th>';
					$out.='<th>' . get_text('RunningTotal','Tournament'). '</th>';

					if ($myRow->matchMode==1)
					{
						$out.='<th>' . get_text('SetPoints','Tournament'). '</th>';
						$out.='<th>' . get_text('TotalShort','Tournament'). '</th>';
					}
				$out.='</tr>';

			// righe
				$totCum=0;
				for ($i=0;$i<$rows;++$i)
				{
					$pr=0;		// totale progressivo
					$prValues=true;	//Vede se ci sono valori
					$out.='<tr>';
						$out.='<th>' . ($i+1) . '</th>';

					// celle di input
						$idx=-1;
						for ($j=0;$j<$cols;++$j)
						{
							$idx=$i*$cols+$j;
							$name='s_' . $myRow->{'match'.$m} . '_' . $idx;
						/*
						 * il nome della textbox indica il gruppo ed è nella forma
						 * s_match_row
						 *
						 * mentre l'id è nella forma
						 * s_match_arrowIndex
						 */
							if(!empty($myRow->{'arrowString'.$m}) && array_key_exists($myRow->{'arrowString'.$m}[$idx] , $validData["Arrows"]))
								$pr+=ValutaArrowString($myRow->{'arrowString'.$m}[$idx]);
							else
								$prValues=false;

							$out.='<td class="Center"><input type="text" id="' . $name . '" size="2" maxlength="3" onclick="this.select();"
									value="' . (!empty($myRow->{'arrowString'.$m})  ? DecodeFromLetter($myRow->{'arrowString'.$m}[$idx]) :'') . '"'
									. ($j==0 ? 'onfocus="setStarter(this.id);"' : '')
									.' /></td>';
						}

					// aggiungo i progressivi in $setPoints
						$setPoints[$m][$i]=$pr;
						$setLoaded[$m][$i]=$prValues;
						$totCum+=$pr;	// il totale cumulativo è la somma dei progressivi

					/*
					 * questi id sono nella forma
					 *
					 * cosa_$match_$from_$idx
					 *
					 * con cosa un riferimento al tipo di valore,
					 * $from il primo indice dell'arrowstring parziale e $idx l'ultimo
					 */
						$from=$idx-$cols+1;
						$coords=$myRow->{'match'.$m} . '_' . $from . '_' . $idx;

						$out.='<td class="Right Bold"><span id="pr_' . $coords . '">' . $pr . '</span></td>';
						$out.='<td class="Right Bold"><span id="totcum_' . $coords . '">' . $totCum . '</span></td>';

					// progr set e tot set
						if ($myRow->matchMode==1)
						{
						// i riferimenti verranno sostituiti dopo
							$out.='<td class="Right Bold"><span id="sp_' . $coords . '">{sp_' . $m . '_' . $i . '}</span></td>';
							$out.='<td class="Right Bold"><span id="st_' . $coords . '">{st_' . $m . '_' . $i . '}</span></td>';
						}
					$out.='</tr>' . "\n";

				}

			// riga degli so e dei totali
				$out.='<tr>';
					$out.='<th>S.O.</th>';
					$out.='<td class="Center" colspan="' . $cols . '">';
						for ($i=0;$i<$so;++$i)
						{
						/*
						 * stesse considerazioni per le textbox dei punti
						 */
							$name='t_' . $myRow->{'match'.$m} . '_' . $i;

							$out.='<input type="text" id="' . $name . '" size="2" maxlength="3" value="'
							. (!empty($myRow->{'tiebreak'.$m})  ?
							 DecodeFromLetter(substr($myRow->{'tiebreak'.$m},$i,1)) :'') . '"  />&nbsp;';
						}
					$out.='</td>';

					$out.='<td class="Right Bold">' . get_text('Total'). '</td>';
					$out.='<td class="Right Bold"><span id="tot_' . $myRow->{'match'.$m} . '">' . $totCum . '</span></td>';

					if ($myRow->matchMode==1)
					{
						$out.='<td class="Right Bold">' . get_text('Total'). '</td>';
						$out.='<td class="Right Bold"><span id="totsets_' . $myRow->{'match'.$m} . '">{totsets_' . $m . '}</span></td>';
					}

				$out.='</tr>' . "\n";
			}
			else if($chunkMode == 2)
			{
				$sql = "SELECT RevLanguage1, RevLanguage2 "
					. "FROM Reviews "
					. "WHERE RevEvent=" . StrSafe_DB($myRow->event) . " AND "
					. "RevMatchNo =" . $myRow->match1 . " AND "
					. "RevTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
					. "RevTeamEvent=" . StrSafe_DB($team);

				$rs=safe_r_sql($sql);
				if (safe_num_rows($rs)>1)
					exit;

				$myReview=safe_fetch($rs);

				$out.='<tr><td colspan="' . (5+$cols). '" class="Center">' . get_text($m==1 ? 'RevMainLang' : 'RevSecLang') . '</td></tr>' . "\n";
				$out.='<tr><td colspan="' . (5+$cols). '" class="Center"><textarea style="width:' . ($_SESSION["WINWIDTH"]*0.45). 'px; height:' . ($_SESSION["WINHEIGHT"]*0.45). 'px;"  id="Lang' . $m . '">' . ($myReview ? $myReview->{'RevLanguage'.$m} : '') . '</textarea></td></tr>' . "\n";
			}
			$out.='</table>' . "\n";
			$scores[$m]=$out;
		}
	}

// ora devo calcolare i punti dei set e i totali dei set (se il tipo di gara è a set)

	if ($myRow->matchMode==1)
	{
		$points=array(1=>array_fill(0,$rows,0),array_fill(0,$rows,0));	// punti set
		$tots=array(1=>array_fill(0,$rows,0),array_fill(0,$rows,0));	// tot cum dei set
		//print_r($setLoaded);
		for ($i=0;$i<$rows;++$i)
		{
			$winner=1;
			$loser=2;
			$pointWinner=2;
			$pointLoser=0;

			if ($setLoaded[1][$i]==true && $setLoaded[2][$i]==true)
			{
				if ($setPoints[1][$i]>$setPoints[2][$i])
				{
					$winner=1;
					$loser=2;
				}
				elseif ($setPoints[1][$i]<$setPoints[2][$i])
				{
					$winner=2;
					$loser=1;
				}
				else	// pari
				{
					$pointWinner=1;
					$pointLoser=1;
				}
			}
			else
			{
				$pointWinner=0;
				$pointLoser=0;
			}


			//print $i . ': ' . $winner . ' ' . $loser . ' - ' . $pointWinner . ' ' . $pointLoser . '<br>';

			//$scores[$winner]=str_replace('{sp_' . $winner . '_' . $i . '}',$pointWinner,$scores[$winner]);
			//$scores[$loser]=str_replace('{sp_' . $loser . '_' . $i . '}',$pointLoser,$scores[$loser]);

			$points[$winner][$i]=$pointWinner;
			$points[$loser][$i]=$pointLoser;
		}

		/*print '<pre>';
		print_r($points);
		print_r($tots);
		print '</pre>';*/

	// i totali cumulativi dei sets
		$tots=$points;

		for ($m=1;$m<=2;++$m)
		{
			for ($i=1;$i<$rows;++$i)
			{
				$tots[$m][$i]+=$tots[$m][$i-1];
			}
		}

	// faccio le sostituzioni
		for ($m=1;$m<=2;++$m)
		{
			for ($i=0;$i<$rows;++$i)
			{
				$scores[$m]=str_replace('{sp_' . $m . '_' . $i . '}',$points[$m][$i],$scores[$m]);
				$scores[$m]=str_replace('{st_' . $m . '_' . $i . '}',$tots[$m][$i],$scores[$m]);
			}

		// il tot cumulativo del set
			//$scores[$m]=str_replace('{totsets_' . $m . '}',$tots[$m][$rows-1],$scores[$m]);
			$scores[$m]=str_replace('{totsets_' . $m . '}',$myRow->{'setScore' . $m},$scores[$m]);
		}
	}
?>
<input type="hidden" id="team" value="<?php print $team;?>" />
<input type="hidden" id="event" value="<?php print $myRow->event;?>" />
<input type="hidden" id="phase" value="<?php print $myRow->phase;?>" />
<input type="hidden" id="matchMode" value="<?php print $myRow->matchMode;?>" />
<input type="hidden" id="rows" value="<?php print $rows;?>" />
<input type="hidden" id="cols" value="<?php print $cols;?>" />
<input type="hidden" id="match1" value="<?php print $myRow->match1;?>" />
<input type="hidden" id="match2" value="<?php print $myRow->match2;?>" />
<input type="hidden" id="spotEnable" value="-1" />
<input type="hidden" id="spotStart" value="-1" />
<input type="hidden" id="spotEnd" value="-1" />

<table class="Tabella">
	<tr>
		<td style="width:50%;"><?php print $scores[1];?></td>
		<td><?php print $scores[2];?></td>
	</tr>
<?php
echo '<tr><td colspan="2" align="center">';
if($chunkMode == 0 || $chunkMode == 1) {
	echo '<input type="button" id="liveButton" ' . ($myRow->live ? 'class="error"' : '') . ' value="' . get_text(($myRow->live ? 'LiveOff':'LiveOn')) . '" onclick="setLive('. $team . ');"/>';
	echo '&nbsp;&nbsp;&nbsp;<input type="checkbox" id="alternate" />'.get_text('AlternateMatch', 'Tournament');
} else {
	echo '<input type="button" id="saveCommentary" value="' . get_text('CmdSave') . '" onclick="saveCommentary('. $team . ');"/>';
}
echo '</td></tr>';
?>
</table>
<div id="msg"></div>
