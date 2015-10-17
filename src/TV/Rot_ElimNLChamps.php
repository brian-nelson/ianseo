<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Modules/DutchIndoorNationals/Fun_DutchIndoorNationals.local.php');

$Arr_Ev = array();
$Arr_Ph = array();
if($TVsettings->TVPEventInd) $Arr_Ev = explode('|', $TVsettings->TVPEventInd);
if(strlen($TVsettings->TVPPhasesInd)) $Arr_Ph = explode('|', $TVsettings->TVPPhasesInd);

$par['phases']=$Arr_Ph;
$par['events']=$Arr_Ev;

$dataRankGroup=getRankGroup($par['events'],$par['phases'],true, $RULE->TVRTournament);	// solo i top 2
$dataRankScore=getRankScore($par['events'],$par['phases'],false, $RULE->TVRTournament);
$dataRankFinal=getRankFinal($par['events'],$par['phases'], $RULE->TVRTournament);

$ret=array();

$html='';

//$html.='<table class="Tabella">';

foreach ($par['events'] as $e) {
	foreach ($par['phases'] as $p) {
		if (count($dataRankGroup[$e]['phases'][$p]['items'])==0) continue;
		if (count($dataRankScore[$e]['phases'][$p]['items'])==0) continue;

		$tmp ='<tr><th class="Title" colspan="6">' . $dataRankGroup[$e]['descr'] . ' ' . get_text('Phase'). ' ' . $p. '</th></tr>';

		$col=array();
		$tmp.='<tr>';
		$tmp.='<th>'.get_text('Rank').'</th>';
		$col[]=5;
		$tmp.='<th>'.get_text('Group','Tournament').'</th>';
		$col[]=5;
		$tmp.='<th>'.get_text('Athlete').'</th>';
		$col[]=40;
		$tmp.='<th>'.get_text('Country').'</th>';
		$col[]=40;
		$tmp.='<th>Points</th>';
		$col[]=5;
		$tmp.='<th>Score</th>';
		$col[]=5;
		$tmp.='</tr>';

		$SumCol=array_sum($col);
		$cols='';
		foreach($col as $w) $cols.='<col width="'.round(100*$w/$SumCol, 0).'%"></col>';

		$ret["$e - $p"]['head']=$tmp;
		$ret["$e - $p"]['cols']=$cols;
		$ret["$e - $p"]['fissi']='';
		$ret["$e - $p"]['basso']='';
		$ret["$e - $p"]['type']='DB';
		$ret["$e - $p"]['style']=$ST;
		$ret["$e - $p"]['js']=$JS;
		$ret["$e - $p"]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId.'&Event='.$e.'&Phase='.$p."';\n";

		$tmp='';
		foreach ($dataRankGroup[$e]['phases'][$p]['items'] as $item) {
			$tmp.='
				<tr>
					<td class="NumberAlign">'.$item['rank'].'</td>
					<td class="NumberAlign">'.$item['group'].'</td>
					<td>'.$item['athlete'].'</td>
					<td>'.$item['countryCode'].' '.$item['countryName'].'</td>
					<td class="NumberAlign">'.$item['points'].'</td>
					<td class="NumberAlign">'.$item['score'].'</td>
				</tr>
			';
		}

		foreach ($dataRankScore[$e]['phases'][$p]['items'] as $item) {
			$tmp.='
				<tr>
					<td class="NumberAlign">'.$item['rank']. ' (' . $item['rankGroup']. ')</td>
					<td class="NumberAlign">'.$item['group'].'</td>
					<td>'.$item['athlete'].'</td>
					<td>'.$item['countryCode'].' '.$item['countryName'].'</td>
					<td class="NumberAlign">'.$item['points'].'</td>
					<td class="NumberAlign">'.$item['score'].'</td>
				</tr>
			';
		}

		$tmp.='<tr><td colspan="6" class="Center">' . get_text('PassNextPhase','Tournament'). '</td></tr>';

		foreach ($dataRankFinal[$e]['phases'][$p]['items'] as $item) {
			$tmp.='
				<tr>
					<td class="NumberAlign">'.$item['rank'].'</td>
					<td class="NumberAlign">'.$item['group'].'</td>
					<td>'.$item['athlete'].'</td>
					<td>'.$item['countryCode'].' '.$item['countryName'].'</td>
					<td class="NumberAlign">'.$item['points'].'</td>
					<td class="NumberAlign">'.$item['score'].'</td>
				</tr>
			';
		}

		$ret["$e - $p"]['basso']=$tmp;
	}
}
