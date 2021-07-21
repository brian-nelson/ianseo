<?php
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_FormatText.inc.php');

// Filtro eventi
$options=array('tournament' => $RULE->TVRTournament, 'dist'=>0);

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

$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
$ViewTeams=(in_array('TEAM', $Columns) or in_array('ALL', $Columns)) ;
$ViewDists=((in_array('DIST', $Columns) or in_array('ALL', $Columns)) and $TVsettings->TVPViewPartials);
$View10s=(in_array('10', $Columns) or in_array('ALL', $Columns)) ;
$ViewX9s=(in_array('X9', $Columns) or in_array('ALL', $Columns));

$comparedTo=preg_grep('/^COMP:/', $Columns);
if(!empty($comparedTo))
	list(,$comparedTo) = explode(":",reset($comparedTo));
$options['comparedTo'] = $comparedTo;

$family='Abs';

$rank=Obj_RankFactory::create($family,$options);
$rank->read();
$rankData=$rank->getData();

if(count($rankData['sections'])==0) return '';



// set the width of the totals if any
$TotalsWidth=7.5;
foreach($Columns as $opt) {
	$tmpW=explode(':', $opt);
	if($tmpW[0]=='WIDTH' and $tmpW[1]) {
		$TotalsWidth=$tmpW[1];
		break;
	}
}

$NumColBase=(empty($comparedTo) ? 3 : 4) + $ViewTeams + $View10s + $ViewX9s;

foreach($rankData['sections'] as $IdEvent => $section) {
//	$RecDist=array();
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
			$sql="select TrHeaderCode, TrHeader, RtRecCode, RtRecDistance, RtRecTotal, RtRecXNine, TrColor, ReArBitLevel, ReArMaCode,
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
					$RecTitle.='&nbsp;<span class="piccolo" style="color:#'.$r->TrColor.'">'.get_text('RecordAverage', 'Tournament', $r->TrHeaderCode).'</span>';
				}
				$RecCut=max($RecCut, $RecTot[$r->RtRecCode]['tot']);
				$rec=round($r->RtRecTotal*max($section['meta']['arrowsShot'])/($section['meta']['numDist']*$section['meta']['maxArrows']),1); // no X9 checks now...
				if($r->TrBars) {
					$RecordCut["$rec"][]='<tr class="Record_'.$r->RtRecCode.'"><th colspan="%1$s">'.($Final ? $r->TrHeader : get_text('RecordAverage', 'Records', $r->TrHeader)).'</th>
						<td class="NumberAlign Grassetto">' . ($section['meta']['running'] ? number_format($rec/($section['meta']['numDist']*$section['meta']['maxArrows']), 3, '.', '') : number_format($rec, $Final ? 0 : 1, '.', '')) . '</td>
						'.($View10s ? '<td>&nbsp;</td>' : '').'
						'.($ViewX9s ? '<td>&nbsp;</td>' : '').'
						</tr>';
				}
			}
		}
	}


	$tmp = '';


	// Titolo della tabella
	$tmp.= '<tr><th class="Title" colspan="' . ($NumCol+count($RecTot)) . '">';
	$tmp.= get_text($IdEvent,'','',true) . ' - ' . get_text($section['meta']['descr'],'','',true) . $RecTitle;
	$tmp.= '</th></tr>' . "\n";

	// Header vero e proprio
	$col=array();
	$tmp.= '<tr>';
	$tmp.= '<th colspan="'.((empty($comparedTo) ? 1 : 2) +count($RecTot)).'">' . $section['meta']['fields']['rank'] . '</th>';
	if(empty($comparedTo)) {
		$col[]=5;
	} else {
		$col[]=3;
		$col[]=2;
	}
	if($RecTot) foreach($RecTot as $k) $col[]=0.5;
	$tmp.= '<th>' . $section['meta']['fields']['athlete'] . '</th>';
	$col[]=33;

	if($ViewTeams) {
		$tmp.= '<th>' . $section['meta']['fields']['countryName'] . '</th>';
		$col[]=($TVsettings->TVPViewNationName ? 20 : 11.5);
	}

	if($ViewDists) {
		for ($i=1;$i<=$rankData['meta']['numDist'];++$i) {
			$tmp.= '<th>' . $section['meta']['fields']['dist_' . $i] . '</th>';
			$col[]=6.5;
		}
	}

	$tmp.= '<th>' . $section['meta']['fields']['completeScore']. '</th>';
	$col[]=$TotalsWidth;

	if($section['meta']['running']) {
		$Field10='score';
		$FieldX9='hits';
		$Class10=' Grassetto';
		if($View10s) {
			$tmp.= '<th>' . $section['meta']['fields']['score'] . '</th>';
			$col[]=$TotalsWidth;
		}
		if($ViewX9s) {
			$tmp.= '<th>' . $section['meta']['fields']['hits'] . '</th>';
			$col[]=4.5;
		}
	} else {
		$Field10='gold';
		$FieldX9='xnine';
		$Class10='';
		if($View10s) {
			$tmp.= '<th>' . $section['meta']['fields']['gold'] . '</th>';
			$col[]=4;
		}
		if($ViewX9s) {
			$tmp.= '<th>' . $section['meta']['fields']['xnine']. '</th>';
			$col[]=4;
		}
	}
	$tmp.= '</tr>' . "\n";

	$SumCol=array_sum($col);
	$cols='';
	foreach($col as $w) $cols.='<col width="'.round(100*$w/$SumCol, 0).'%"></col>';

	$ret[$IdEvent]['head']=$tmp;
	$ret[$IdEvent]['cols']=$cols;
	$ret[$IdEvent]['fissi']='';
	$ret[$IdEvent]['basso']='';
	$ret[$IdEvent]['type']='DB';
	$ret[$IdEvent]['style']=$ST;
	$ret[$IdEvent]['js']=$JS;
	$ret[$IdEvent]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId.'&Event='.$IdEvent."';\n";

	$Cut=false;
	foreach($section['items'] as $key => $item) {
		// Dati della tabella aperta in (1)
		if(!$Cut and $item['rank']>$section['meta']['qualifiedNo']) {
			$ret[$IdEvent]['basso'].='<tr><td colspan="'.count($col).'">&nbsp;</td></tr>';
			$Cut=true;
		}

		$tmp='';

		foreach ($RecordCut as $Record => $FormattedRows) {
			if($item['completeScore'] <= $Record) {
				$tmp.=sprintf(implode('', $FormattedRows), count($col)-(1+$View10s+$ViewX9s));
				unset($RecordCut[$Record]);
			}
		}

		$RecClass='';
		$RecColumns=str_repeat('<td>&nbsp;</td>',count($RecTot));
		if($item['recordGap'] < $RecCut) {
			$RecColumns='';
			foreach ($RecTot as $RecCode => $Record) {
				if(!$Final and $Record['gap'] and $item['recordGap'] < $Record['tot'] and
					(!$Record['claim']
						or $Record['claim']==$item['contAssoc']
						or $Record['claim']==$item['memberAssoc'])) {
					$RecColumns.='<td class="Rec-Bg-'.$RecCode.'">&nbsp;</td>';
				} else {
					$RecColumns.='<td>&nbsp;</td>';
				}
			}
		}

		$tmp.= '<tr' . ($key % 2 == 0 ? '': ' class="Next"') . '>';

		$tmp.= '<th class="Title '.$RecClass.'">' . $item['rank'] . '</th>';
		if(!empty($comparedTo))
			$tmp.= '<td class="Center '.$RecClass.'" style="' . ($item['oldRank'] ? 'background: url(\'' . $CFG->ROOT_DIR . 'Common/Images/' . ($item['rank']==$item['oldRank'] ? 'Minus' : ($item['rank']<$item['oldRank'] ? 'Up' : 'Down')) . '.png\'); background-repeat:no-repeat; background-size: contain; background-position:center;' : '') . 'color:#FFFFFF; font-weight:bold; font-size:60%; ">' . ($item['oldRank']&& $item['oldRank']!=$item['rank'] ? $item['oldRank']:'&nbsp;'). '</th>';
		$tmp.=$RecColumns;
		$tmp.= '<td><span class="piccolo">' . $item['target'] . '</span> ' . $item['familynameUpper'] . ' ' . ($TVsettings->TVPNameComplete==0 ? FirstLetters($item['givenname']) : $item['givenname']) . '</td>';
		if($ViewTeams) $tmp.= '<td style="font-size:80%">' . ($TVsettings->TVPViewNationName==1 ? ($item['countryName']) : $item['countryCode']) . '</td>';
		if($ViewDists) {
			for ($i=1;$i<=$rankData['meta']['numDist'];++$i)
			{
				list($rank,$score)=explode('|',$item['dist_' . $i]);
				$tmp.= '<td class="NumberAlign">' . str_pad($score,3," ",STR_PAD_LEFT) . '</td>';
			}
		}
		$tmp.= '<td class="NumberAlign Grassetto '.$RecClass.'">' . $item['completeScore'] . '</td>';
		if($View10s) $tmp.= '<td class="NumberAlign'.$Class10.'">' . $item[$Field10] . '</td>';
		if($ViewX9s) $tmp.= '<td class="NumberAlign">' . $item[$FieldX9] . '</td>';

		$tmp.= '</tr>' . "\n";

		if($item['rank']<=3)
		{
			if(isset($section['items'][5]) && $section['items'][5]['rank']==1 && $key>0)
				$ret[$IdEvent]['basso'].=$tmp;
			if(isset($section['items'][6]) && $section['items'][6]['rank']==2 && $key>1)
				$ret[$IdEvent]['basso'].=$tmp;
			if(isset($section['items'][7]) && $section['items'][7]['rank']==3 && $key>2)
				$ret[$IdEvent]['basso'].=$tmp;
			else
				$ret[$IdEvent]['fissi'].=$tmp;
		}
		else
			$ret[$IdEvent]['basso'].=$tmp;
	}

}




?>
