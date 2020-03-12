<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Modules/Bundesliga/Fun_Bundesliga.local.inc.php');

$Arr_Ev = array();
if($TVsettings->TVPEventTeam) {
	$Arr_Ev = explode('|', $TVsettings->TVPEventTeam);
}

$ret=array();

$html='';

//$html.='<table class="Tabella">';

foreach ($Arr_Ev as $e) {
	$Sql = "SELECT CaTeam,CaSubTeam,CaEventCode,CaMatchNo, CoCode,CoName,CGGroup,"
		. "SUM(CaSPoints) AS Points,SUM(CaSScore) AS Score,SUM(CaSSetScore) AS SetScore,CaTiebreak, CaRank "
		. "FROM CasTeam "
		. "INNER JOIN Countries ON CaTeam=CoId "
		. "INNER JOIN CasGrid ON CaPhase=CGPhase AND (CaMatchNo=CGMatchNo1 OR CaMatchNo=CGMatchNo2) "
		. "INNER JOIN CasScore ON CaTournament=CaSTournament AND CaPhase=CaSPhase AND CaMatchNo=CaSMatchNo AND  CaEventCode=CaSEventCode AND CGRound=CaSRound "
		. "WHERE CaEventCode=" . StrSafe_DB($e) . " AND CaTournament=" . $RULE->TVRTournament . " AND CaPhase=0 "
		. "GROUP BY CaPhase,CaEventCode,CGGroup,CaTeam,CaSubTeam,CaMatchNo,CoCode,CoName,CaTiebreak,CaRank "
		. "ORDER BY CaEventCode ASC, CGGroup ASC, SUM(CaSPoints) DESC, SUM(CaSScore) DESC, CaRank ASC ";
	$q=safe_r_sql($Sql);
	$OldGroup = '--';
	$myRank = 0;
	$myPos = 0;
	$OldPoints = 0;
	$ret[$e]['head']='';
	$col[]=5;
	$col[]=40;
	$col[]=10;
	$col[]=10;
	$SumCol=array_sum($col);
	$cols='';
	foreach($col as $w) $cols.='<col width="'.round(100*$w/$SumCol, 0).'%"></col>';
	$ret[$e]['cols']=$cols;
	$ret[$e]['fissi']='';
	$ret[$e]['basso']='';
	$ret[$e]['type']='DB';
	$ret[$e]['style']=$ST;
	$ret[$e]['js']=$JS;
	$ret[$e]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId.'&Event='.$e.'\';'."\n";
	while($r=safe_fetch($q)) {
		if($OldGroup != $r->CGGroup) {
			$tmp ='<tr><th class="Title" colspan="6">Group ' . chr(64+$r->CGGroup) . '</th></tr>';
			$tmp.='<tr>';
			$tmp.='<th>'.get_text('Rank').'</th>';
			$tmp.='<th>'.get_text('Team').'</th>';
			$tmp.='<th>'.get_text('Points', 'Tournament').'</th>';
			$tmp.='<th>'.get_text('Tie').'</th>';
			$tmp.='</tr>';

			$ret[$e]['fissi'].=$tmp;
			$OldGroup = $r->CGGroup;
			$tmp='';
			$myRank = 0;
			$myPos = 0;
			$OldPoints = 0;
		}
		//Calcolo della Rank;
		$myPos++;
		if(!($r->Points == $OldPoints && $r->CaTiebreak == $OldTie)) {
			$myRank=$myPos;
		}

		//Valuto il TieBreak
		$TmpTie = '';
		if(strlen(trim($r->CaTiebreak)) > 0)
		{
			for($countArr=0; $countArr<strlen(trim($r->CaTiebreak)); $countArr = $countArr+3)
				$TmpTie .= ValutaArrowString(substr(trim($r->CaTiebreak),$countArr,3)) . ",";
				$TmpTie = substr($TmpTie,0,-1);
		}
		$tmp='<tr>
			<td class="NumberAlign">'.$myRank.'</td>
			<td>'.$r->CoName.'</td>
			<td class="NumberAlign">'.$r->Points.'</td>
			<td class="NumberAlign">'.$TmpTie.'</td>
		</tr>';

		$OldPoints = $r->Points;
		$OldTie = $r->CaTiebreak;

		$ret[$e]['fissi'].=$tmp;
	}
}
