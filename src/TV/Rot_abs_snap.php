<?php
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_FormatText.inc.php');

$TourType=getTournamentType($TourId);

$TVsettings->EventFilter=MakeEventFilter($TVsettings->TVPEventInd);

$options=array('tournament' => $RULE->TVRTournament, 'dist'=>0);
$options['subFamily'] = 'Abs';

$Arr_Ev = explode('|', $TVsettings->TVPEventInd);
if (count($Arr_Ev)>1) {
	sort($Arr_Ev);
	$options['events'] = array_values($Arr_Ev);
} elseif (!empty($TVsettings->TVPEventInd)) {
	$options['events'] = $TVsettings->TVPEventInd;
}

if(isset($TVsettings->TVPNumRows) && $TVsettings->TVPNumRows>0)
	$options['cutRank'] = $TVsettings->TVPNumRows;
if(isset($TVsettings->TVPSession) && $TVsettings->TVPSession>0)
	$options['session'] = $TVsettings->TVPSession;

$rank=Obj_RankFactory::create('Snapshot',$options);
$rank->read();
$rankData=$rank->getData();

if(count($rankData['sections'])==0) return '';

$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
$ViewTeams=(in_array('TEAM', $Columns) or in_array('ALL', $Columns));
$ViewDists=((in_array('DIST', $Columns) or in_array('ALL', $Columns)) and $TVsettings->TVPViewPartials);
$ViewTOT=(in_array('TOT', $Columns) or in_array('ALL', $Columns));

$NumColBase=3 + $ViewTeams + $ViewTOT;

foreach($rankData['sections'] as $IdEvent => $section) {
	$RecTot=array();
	$RecTitle='';
	$RecCut=999999;
	$NumColDist=($ViewDists ? $rankData['meta']['numDist'] : 0);
	$NumCol = $NumColBase + $NumColDist;
	if($section['meta']['arrowsShot']) {
		// Records handling
		$RecTot=array();
		$RecordCut=array();
		$RecTitle='';
		$RecCut=0;
		$RecXNine=0;
		$RecCols=array();
		if($section['meta']['arrowsShot']) {
			// Records handling
			$MaxScore=$section['meta']['numDist']*$section['meta']['maxScore'];
			$Final=(max($section['meta']['arrowsShot'])==$section['meta']['numDist']*$section['meta']['maxArrows']);
			$sql="select TrHeaderCode, TrHeader, RtRecCode, RtRecDistance, RtRecTotal, RtRecXNine, TrColor, RtRecExtra, ReArBitLevel, ReArMaCode,
					find_in_set('bar', TrFlags) TrBars,
					find_in_set('gap', TrFlags) TrGaps
				from RecTournament
				inner join TourRecords on TrTournament=RtTournament and TrRecCode=RtRecCode and TrRecTeam=RtRecTeam and TrRecPara=RtRecPara
				inner join RecAreas on ReArCode=RtRecCode
				inner join Events on RtTournament=EvTournament and EvRecCategory=RtRecCategory and EvCode='{$IdEvent}' and EvTournament={$TourId} and RtRecTeam=EvTeamEvent and EvTeamEvent=0
				where RtRecPhase=1
				order by RtRecTotal desc "; // for now we only do on totals
			$q=safe_r_sql($sql);
			while($r=safe_fetch($q)) {
				$RecTot[$r->RtRecCode]['tot']=$MaxScore-$r->RtRecTotal;
				$RecTot[$r->RtRecCode]['gap']=$r->TrGaps;
				$RecTot[$r->RtRecCode]['area']=$r->ReArBitLevel;
				$RecTot[$r->RtRecCode]['claim']=$r->ReArMaCode;
				// no X9 checks now...
				// $RecTot[$r->RtRecCode]['X9']=$MaxScore-$r->RtRecTotal;
				if($r->TrGaps and !$Final) {

					$RecTitle.='&nbsp;<span class="piccolo" style="color:#'.$r->TrColor.'">'.get_text('RecordAverage', 'Tournament', $r->TrHeaderCode);
					//if($r->RtRecExtra and $tmp=unserialize($r->RtRecExtra)) {
					//	$RecTitle.=' ('.$r->RtRecTotal .' - '. $tmp[0]->Archers[0]['Archer'] . ')';
					//}
					$RecTitle.='</span>';
				}


				$RecCut=max($RecCut, $RecTot[$r->RtRecCode]['tot']);
				$rec=round($r->RtRecTotal*max($section['meta']['arrowsShot'])/($section['meta']['numDist']*$section['meta']['maxArrows']),1); // no X9 checks now...
				if($r->TrBars) {
					$RecordCut["$rec"][]='<tr class="Record_'. $r->RtRecCode.'"><th colspan="%s">'.($Final ? $r->TrHeader : get_text('RecordAverage', 'Records', $r->RtRecCode)).'</th>
						<td class="NumberAlign Grassetto">' . ($section['meta']['running'] ? number_format($rec/($section['meta']['numDist']*$section['meta']['maxArrows']), 3) : number_format($rec, $Final ? 0 : 1)) . '</td>
						</tr>';
				}
			}
		}
	}

	// crea l'header della gara
	$tmp = '';

	$NumCol = $NumColBase + ($ViewDists ? $rankData['meta']['numDist'] : 0);

	$tmp.= '<tr><th class="Title" colspan="' . ($NumCol+count($RecTot)) . '">';
	$tmp.= $Arr_Pages[$TVsettings->TVPPage];
	$tmp.= '</th></tr>' . "\n";

	// Titolo della tabella
	$tmp.= '<tr><th class="Title" colspan="' . ($NumCol+count($RecTot)) . '">';
	$tmp.=  $section['meta']['descr']. $RecTitle;
	$tmp.= '</th></tr>' . "\n";

	// Header vero e proprio, incluse le larghezze delle colonne;
	$col=array();
	$tmp.= '<tr>';
	$tmp.= '<th colspan="'.(1 +count($RecTot)).'">' . $section['meta']['fields']['rank'] . '</th>';
	$col[]=7;
	if($RecTot) foreach($RecTot as $k) $col[]=0.5;
	$tmp.= '<th>' . $section['meta']['fields']['athlete'] . '</th>';
	$col[]=33;

	if($ViewTeams) {
		$tmp.= '<th>' . $section['meta']['fields']['countryName'] . '</th>';
		$col[]=($TVsettings->TVPViewNationName?27:11.5);
	}

	if($ViewDists) {
		for ($i=1;$i<=$rankData['meta']['numDist'];++$i) {
			$tmp.= '<th>' . $section['meta']['fields']['dist_' . $i]. '</th>';
			$col[]=6.5;
		}
	}

	$tmp.= '<th>' . $section['meta']['printHeader'] . '</th>';
	$col[]=7.5;

	if($ViewTOT) {
		$tmp.= '<th>' . $section['meta']['fields']['score'] . '</th>';
		$col[]=7.5;
	}
	$tmp.= '</tr>' . "\n";
	$ret[$IdEvent]['head']=$tmp;

	$SumCol=array_sum($col);
	$cols='';
	foreach($col as $w) $cols.='<col width="'.round(100*$w/$SumCol, 0).'%"></col>';

	$ret[$IdEvent]['cols']=$cols;
	$ret[$IdEvent]['fissi']='';
	$ret[$IdEvent]['basso']='';
	$ret[$IdEvent]['type']='DB';
	$ret[$IdEvent]['style']=$ST;
	$ret[$IdEvent]['js']=$JS;
	$ret[$IdEvent]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId.'&Event='.$IdEvent."';\n";

	$Cut=false;
	foreach($section['items'] as $key => $item)
	{
		// Dati della tabella aperta in (1)
		if(!$Cut and $item['rank']>$section['meta']['qualifiedNo']) {
			$ret[$IdEvent]['basso'].='<tr><td colspan="'.count($col).'">&nbsp;</td></tr>';
			$Cut=true;
		}
		$tmp='';
		foreach ($RecordCut as $Record => $FormattedRows) {
			if($item['scoreSnap'] <= $Record) {
				$tmp.=sprintf(implode('', $FormattedRows), count($col)-(1));
				unset($RecordCut[$Record]);
			}
		}

		$RecClass='';
		$RecColumns=str_repeat('<td>&nbsp;</td>',count($RecTot));
		if($item['recordGap'] < $RecCut) {
			$RecColumns='';
			foreach ($RecTot as $RecCode => $Record) {
				if($Record['gap'] and $item['recordGap'] < $Record['tot'] and
					(!$Record['claim']
						or $Record['claim']==$item['contAssoc']
						or $Record['claim']==$item['memberAssoc'])) {
					$RecColumns.='<td class="Rec-Bg-'.$RecCode.'">&nbsp;</td>';
				} else {
					$RecColumns.='<td>&nbsp;</td>';
				}
			}
		}

		//al 4° devo interrompere la tabella e aprire il resto in un div separato
		$tmp.= '<tr' . ($key % 2 == 0 ? '': ' class="Next"') . '>';
		$tmp.= '<th class="Title '.$RecClass.'">' . $item['rank'] . '</th>';
		$tmp.=$RecColumns;
		$tmp.= '<td><span class="piccolo">' . $item['target'] . '</span> ' . $item['familynameUpper'] . ' ' . ($TVsettings->TVPNameComplete==0 ? FirstLetters($item['givenname']) : $item['givenname']) . '</td>';

		if($ViewTeams) $tmp.= '<td style="font-size:80%">' . $item['countryCode'] . ' ' . ($TVsettings->TVPViewNationName==1 ? ($item['countryName']) : '') . '</td>';

		//NewDistanze
		if($ViewDists) {
			for ($i=1;$i<=$rankData['meta']['numDist'];++$i) {
				list($rank, $score, $gold, $xnine)=explode('|', $item['dist_'.$i]);
				if($section['meta']['snapDistance']==0)
					$tmp .= '<td class="NumberAlign">' . str_pad($score,3," ",STR_PAD_LEFT) . '<span class="piccolo">/' . str_pad((($TourType!=14 and $TourType != 32) ? $rank : $xnine),2," ",STR_PAD_LEFT) . '</span></td>';
				else if($i < $section['meta']['snapDistance'])
					$tmp .= '<td class="NumberAlign">' . str_pad($score,3," ",STR_PAD_LEFT) . '</td>';
				else if($i == $section['meta']['snapDistance'])
				{
					list($rank, $score)=explode('|', $item['dist_Snap']);
					$tmp .= '<td class="NumberAlign">' . str_pad($score,3," ",STR_PAD_LEFT) . '</td>';
				}
				else
					$tmp .= '<td class="NumberAlign">' . str_pad("0",3," ",STR_PAD_LEFT) . '</td>';
			}
		}
		$tmp.= '<td class="NumberAlign Grassetto '.$RecClass.'">' . $item['scoreSnap'] . '</td>';
		if($ViewTOT) $tmp.= '<td class="NumberAlign Grassetto">' . ($item['scoreSnap'] != $item['score'] ?  $item['score'] : "&nbsp;") . '</td>';

		$tmp.= '</tr>' . "\n";

		if($item['rank']<=3) {
			if(isset($section['items'][7]) && $section['items'][7]['rank']<=3) {
				$ret[$IdEvent]['basso'].=$tmp;
			} else {
				$ret[$IdEvent]['fissi'].=$tmp;
			}
		}
		else
			$ret[$IdEvent]['basso'].=$tmp;
	}
}

?>
