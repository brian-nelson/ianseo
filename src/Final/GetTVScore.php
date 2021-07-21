<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/CommonLib.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Fun_Final.local.inc.php');

	$Error='';

	if(empty($_REQUEST['TourCode']) and empty($_SESSION['TourId'])) {
		$Error = 'No Tournament Selected';
	} else {
		$TourId=(empty($_REQUEST['TourCode']) ? $_SESSION['TourId'] : getIdFromCode($_REQUEST['TourCode']));
	}
    checkACL(AclOutput,AclReadOnly, false, $TourId);

	$LastUpdated=(empty($_REQUEST['Time']) ? 0 : $_REQUEST['Time']);
	$WinWidth=(empty($_REQUEST['Width']) ? 800 : $_REQUEST['Width']);

	$XML='<r>'
		. '<e>'.intval(!empty($Error)).'</e>'
		. '<t>0</t>'
		. '<c><![CDATA['.$Error.']]></c>'
		. '</r>';
	if($Error) {
		header('Content-Type: text/xml');
		die($XML);
	}


	$q=safe_r_sql("(select '0' as Team, FinEvent as Event, FinMatchNo as MatchNo, UNIX_TIMESTAMP(FinDateTime) DateTime, UNIX_TIMESTAMP(FinDateTime)>$LastUpdated Updated from Finals where FinTournament=$TourId and FinLive='1')
		UNION
		(select '1' as Team, TfEvent as Event, TfMatchNo as MatchNo, UNIX_TIMESTAMP(TfDateTime) DateTime, UNIX_TIMESTAMP(TfDateTime)>$LastUpdated Updated from TeamFinals where TfTournament=$TourId and TfLive='1') order by MatchNo");

	if(!($r=safe_fetch($q))) $Error = 'no match now';

	$XML='<r>'
		. '<e>'.intval(!empty($Error)).'</e>'
		. '<t>'.($Error ? 0 : $r->DateTime).'</t>'
		. '<c><![CDATA['.$Error.']]></c>'
		. '</r>';

	// No changes so exits immediately
	if($Error or !$r->Updated ) {
		header('Content-Type: text/xml');
		die($XML);
	}

	$d_Event=$r->Event;
	$d_Match=$r->MatchNo;
	$team=$r->Team;

	// tiro fuori le info x lo scontro
	$rs=GetFinMatches($d_Event, null, $d_Match, $team, false, $TourId);
	//Carico il vettore dei dati validi
	$validData=GetMaxScores($d_Event, $d_Match, $team, $TourId);

	if (safe_num_rows($rs)!=1)
		exit;

	$EnCours=false;

	$myRow=safe_fetch($rs);

	// righe e colonne e so nel caso di individuali cumulativi

	$rows=4;
	$cols=3;
	$so=1;

	list($rows,$cols,$so)=CalcScoreRowsColsSO($myRow, $TourId);

	// i due score da stampare a video
	$scores=array(1=>array(),2=>array());

	for($archer=1; $archer<3; $archer++) {
		for($row=0; $row<$rows; $row++) {
			for($col=0; $col<$cols; $col++) {
				$idx=$row*$cols+$col;
				$ArValue=(empty($myRow->{'arrowString'.$archer}[$idx]) ? '' : DecodeFromLetter($myRow->{'arrowString'.$archer}[$idx]));
				if($ArValue=='') continue;
				$scores[$archer][$row][]=$ArValue;
			}
		}
	}

	// Get the last filled
	$LastEnd=max(count($scores[1]), count($scores[2]))-1;
	if(empty($scores[1][$LastEnd])) $scores[1][$LastEnd]=array();
	if(empty($scores[2][$LastEnd])) $scores[2][$LastEnd]=array();

	// Is it "en cours" or ended?
	if($myRow->arrowString1 or $myRow->arrowString2) $EnCours=(count($scores[1][$LastEnd])!=count($scores[2][$LastEnd]) or count($scores[1][$LastEnd])!=$cols or strstr(implode('', $scores[1][$LastEnd]).implode('', $scores[2][$LastEnd]), '*'));

	$Out='<table id="Tabella" style="width:100%;height:100%" cellpadding="10" cellspacing="0">';
	$OutString=0;
	$OutMainString=0;
	if($myRow->matchMode) {
		$OutMainString = strlen($myRow->setScore1)+strlen($myRow->setScore2);
		if($EnCours) {
			$Out.='<tr style="height:20%"><td id="name1" colspan="'.$cols.'">'.$myRow->name1.'</td><td id="name2" colspan="'.$cols.'">'.$myRow->name2.'</td></tr>';;
			$Out.='<tr style="height:50%"><td id="MiniScore1" colspan="'.$cols.'">'.$myRow->setScore1.'</td><td id="MiniScore2" colspan="'.$cols.'">'.$myRow->setScore2.'</td></tr>';
			$Out.='<tr style="height:30%">';
			foreach(range(1,2) as $m) {
				foreach(range(0, $cols-1) as $col) {
					$Out.='<td class="ScoreArrows'.$m.'">'.(empty($scores[$m][$LastEnd][$col]) ? '&nbsp;' : $scores[$m][$LastEnd][$col]).'</td>';
					$OutString+=(empty($scores[$m][$LastEnd][$col]) ? 1 : strlen($scores[$m][$LastEnd][$col]));
				}
			}
			$Out.='</tr>';
//			$OutMainString*=0.75;
		} else {
			$Out.='<tr style="height:20%"><td id="name1">'.$myRow->name1.'</td><td id="name2">'.$myRow->name2.'</td></tr>';;
			$Out.='<tr style="width:100%;height:80%"><td id="Score1">'.$myRow->setScore1.'</td><td id="Score2">'.$myRow->setScore2.'</td></tr>';
			$OutMainString*=0.75;
		}
	} else {
		// Only the simple table with total scores
		$OutMainString = strlen($myRow->score1)+strlen($myRow->score2);
		if($EnCours) {
			$Out.='<tr style="height:20%"><td id="name1" colspan="'.$cols.'">'.$myRow->name1.'</td><td id="name2" colspan="'.$cols.'">'.$myRow->name2.'</td></tr>';;
			$Out.='<tr style="height:50%"><td id="MiniScore1" colspan="'.$cols.'">'.$myRow->score1.'</td><td id="MiniScore2" colspan="'.$cols.'">'.$myRow->score2.'</td></tr>';
			$Out.='<tr style="height:30%">';
			foreach(range(1,2) as $m) {
				foreach(range(0, $cols-1) as $col) {
					$Out.='<td class="ScoreArrows'.$m.'">'.(empty($scores[$m][$LastEnd][$col]) ? '&nbsp;' : $scores[$m][$LastEnd][$col]).'</td>';
					$OutString+=(empty($scores[$m][$LastEnd][$col]) ? 1 : strlen($scores[$m][$LastEnd][$col]));
				}
			}
			$Out.='</tr>';
		} else {
			$Out.='<tr style="height:20%"><td id="name1" colspan="'.$cols.'">'.$myRow->name1.'</td><td id="name2" colspan="'.$cols.'">'.$myRow->name2.'</td></tr>';
			if(!$myRow->tiebreak1 and !$myRow->tiebreak2) {
				$Out.='<tr style="width:100%;height:80%"><td id="Score1" colspan="'.$cols.'">'.$myRow->score1.'</td><td id="Score2" colspan="'.$cols.'">'.$myRow->score2.'</td></tr>';
			} else {
				$Out.='<tr style="height:50%"><td id="MiniScore1" colspan="'.$cols.'" width="50%">'.$myRow->score1.'</td><td id="MiniScore2" colspan="'.$cols.'" width="50%">'.$myRow->score2.'</td></tr>';
				$Out.='<tr style="height:30%">';
				foreach(range(1,2) as $m) {
					$Out.='<td class="ScoreArrows'.$m.'" colspan="'.$cols.'">T.&nbsp;';
					if($myRow->{'tiebreak'.$m}) {
						foreach(range(0, strlen($myRow->{'tiebreak'.$m})-1) as $col) {
							$Out.='<div class="SingleScore">'.DecodeFromLetter($myRow->{'tiebreak'.$m}[$col]).'</div>';
							$OutString+=1;
						}
					}
					$Out.='</td>';
				}
			}
			$OutMainString*=0.75;
		}
	}
	$Out.='</table>';

	$ColWidth=100/($cols*2);

	$Scale=1.25;

	$Out.='<style>
	#Score1, #Score2, #MiniScore1, #MiniScore2, .ScoreArrows1, .ScoreArrows2 {font-size:'.($WinWidth/($OutMainString*$Scale)).'px;}
	.ScoreArrows1, .ScoreArrows2 {width:'.$ColWidth.'%; font-size:'.($WinWidth/($OutString ? $OutString*$Scale : 15)).'px}
	#name1, #name2 {width:'.$ColWidth.'%; font-size:'.($WinWidth/($OutString ? $OutString*$Scale*2 : 15)).'px}
	#MiniScore1, #MiniScore2 {font-size:'.($WinWidth/($OutMainString*$Scale)).'px; }
	</style>';



	header('Content-Type: text/xml');
	echo '<r>'
		. '<e>0</e>'
		. '<t>'.$r->DateTime.'</t>'
		. '<c><![CDATA['.$Out.']]></c>'
		. '</r>';

?>
