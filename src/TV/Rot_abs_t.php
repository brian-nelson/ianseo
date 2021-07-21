<?php

require_once('Common/Lib/Obj_RankFactory.php');

$TVsettings->EventFilter=MakeEventFilter($TVsettings->TVPEventTeam);

$options=array('tournament' => $RULE->TVRTournament);
$options['dist'] = 0;

if(isset($TVsettings->TVPEventTeam) && !empty($TVsettings->TVPEventTeam))
	$options['events'] = explode('|',$TVsettings->TVPEventTeam);
if(isset($TVsettings->TVPNumRows) && $TVsettings->TVPNumRows>0)
	$options['cutRank'] = $TVsettings->TVPNumRows;
if(isset($TVsettings->TVPSession) && $TVsettings->TVPSession>0)
	$options['session'] = $TVsettings->TVPSession;

$rank=Obj_RankFactory::create('AbsTeam',$options);
$rank->read();
$rankData=$rank->getData();

if(count($rankData['sections'])==0) return '';


$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
$ViewAths=(in_array('ATHL', $Columns) or in_array('ALL', $Columns));
$View10s=(in_array('10', $Columns) or in_array('ALL', $Columns));
$ViewX9s=(in_array('X9', $Columns) or in_array('ALL', $Columns));

// set the width of the totals if any
$TotalsWidth=7.5;
foreach($Columns as $opt) {
	$tmpW=explode(':', $opt);
	if($tmpW[0]=='WIDTH' and $tmpW[1]) {
		$TotalsWidth=$tmpW[1];
		break;
	}
}

$NumCol = 3 + $ViewAths + $View10s + $ViewX9s;

foreach($rankData['sections'] as $IdEvent => $data) {
	$RecTot=array();
	$RecTitle='';
	$RecCut=999999;
	if($data['meta']['arrowsShot']) {
		// Records handling
		$RecTot=array();
		$RecordCut=array();
		$RecTitle='';
		$RecCut=0;
		$RecXNine=0;
		$RecCols=array();
		if($data['meta']['arrowsShot']) {
			// Records handling
			$MaxScore=$data['meta']['maxScore'];
			$Final=(max($data['meta']['arrowsShot'])==$data['meta']['maxArrows']);
			$sql="select TrHeaderCode, TrHeader, RtRecCode, RtRecDistance, RtRecTotal, RtRecXNine, TrColor, ReArBitLevel, ReArMaCode,
					find_in_set('bar', TrFlags) TrBars,
					find_in_set('gap', TrFlags) TrGaps
				from RecTournament
				inner join TourRecords on TrTournament=RtTournament and TrRecCode=RtRecCode and TrRecTeam=RtRecTeam and TrRecPara=RtRecPara
				inner join RecAreas on ReArCode=RtRecCode
				inner join Events on RtTournament=EvTournament and EvRecCategory=RtRecCategory and EvCode='{$IdEvent}' and EvTournament={$TourId} and RtRecTeam=EvTeamEvent and EvTeamEvent=1
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
					$RecTitle.='&nbsp;<span class="piccolo" style="color:#'.$r->TrColor.'">'.($Final ? $r->TrHeader : get_text('RecordAverage', 'Records', $r->TrHeader)).'</span>';
				}
				$RecCut=max($RecCut, $RecTot[$r->RtRecCode]['tot']);
				$rec=round($r->RtRecTotal*array_sum($data['meta']['arrowsShot'])/$data['meta']['maxArrows'],1); // no X9 checks now...
				if($r->TrBars) {
					$RecordCut["$rec"][]='<tr class="Record_'.$r->RtRecCode.'"><th colspan="%s">'.get_text('RecordAverage', 'Records', $r->TrHeader).'</th>
						<td class="NumberAlign Grassetto">' . ($data['meta']['running'] ? number_format($rec/$data['meta']['maxArrows'], 3, '.', '') : number_format($rec, $Final ? 0 : 1, '.', '')) . '</td>
						'.($View10s ? '<td>&nbsp;</td>' : '').'
						'.($ViewX9s ? '<td>&nbsp;</td>' : '').'
						</tr>';
				}
			}
		}
	}

	// Titolo della tabella
	$tmp = '<tr><th class="Title" colspan="' . ($NumCol+count($RecTot)) . '">';
	$tmp.= $Arr_Pages[$TVsettings->TVPPage];
	$tmp.= '</th></tr>' . "\n";

	$tmp.= '<tr><th class="Title" colspan="' . ($NumCol+count($RecTot)) . '">';
	$tmp.= $data['meta']['descr'];
	$tmp.= '</th></tr>' . "\n";

	// Header vero e proprio
	$col=array();
	$tmp.= '<tr>';
	$tmp.= '<th colspan="'.(1+count($RecTot)).'">' . $data['meta']['fields']['rank'] . '</th>';
	$col[]=7;
	if($RecTot) foreach($RecTot as $k) $col[]=0.5;
	$tmp.= '<th>' . $data['meta']['fields']['countryName'] . '</th>';
	$col[]=33;
	if($ViewAths) {
		$tmp.= '<th>' . get_text('Athlete') . '</th>';
		$col[]=41;
	}

	$tmp.= '<th>' . $data['meta']['fields']['completeScore'] . '</th>';
	$col[]=$TotalsWidth;

	if($data['meta']['running']) {
		$Field10='score';
		$FieldX9='hits';
		$Class10=' Grassetto';
		if($View10s) {
			$tmp.= '<th>' . $data['meta']['fields']['score'] . '</th>';
			$col[]=$TotalsWidth;
		}
		if($ViewX9s) {
			$tmp.= '<th>' . $data['meta']['fields']['hits'] . '</th>';
			$col[]=4.5;
		}
	} else {
		$Field10='gold';
		$FieldX9='xnine';
		$Class10='';
		if($View10s) {
			$tmp.= '<th>' . $data['meta']['fields']['gold'] . '</th>';
			$col[]=4;
		}
		if($ViewX9s) {
			$tmp.= '<th>' . $data['meta']['fields']['xnine']. '</th>';
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

	// Inserisci adesso le singole righe
	$Cut=false;
	foreach($data['items'] as $key => $archer) {
		$NumNomi = count($archer['athletes']);

		if(!$Cut and $archer['rank']>$data['meta']['qualifiedNo']) {
			$ret[$IdEvent]['basso'].='<tr><td colspan="'.count($col).'">&nbsp;</td></tr>';
			$Cut=true;
		}

		$tmp='';

		foreach ($RecordCut as $Record => $FormattedRows) {
			if($archer['score'] <= $Record) {
				$tmp.=sprintf(implode('', $FormattedRows), count($col)-(1+$View10s+$ViewX9s));
				unset($RecordCut[$Record]);
			}
		}

		$RecClass='';
		$RecColumns=str_repeat('<td>&nbsp;</td>',count($RecTot));
		if($archer['recordGap'] < $RecCut) {
			$RecColumns='';
			foreach ($RecTot as $RecCode => $Record) {
				if(!$Final and $Record['gap'] and $archer['recordGap'] < $Record['tot'] and
					(!$Record['claim']
						or $Record['claim']==$archer['contAssoc']
						or $Record['claim']==$archer['memberAssoc'])) {
					$RecColumns.='<td class="Rec-Bg-'.$RecCode.'">&nbsp;</td>';
				} else {
					$RecColumns.='<td>&nbsp;</td>';
				}
			}
		}

		// al 4° devo interrompere la tabella e aprire il resto in un div separato
		$tmp.='<tr' . ($key%2 == 0 ? '': ' class="Next"') . '>';
		$tmp.= '<th class="Title">' . $archer['rank'] . '</th>';
		$tmp.=$RecColumns;
		$tmp.= '<td>' . $archer['countryCode'] . ' ' . ($archer['countryName']) . '</td>';
	    if($ViewAths) {
		    $tmp.= '<td>' . $archer['athletes'][0]['athlete'];
		    for ($i=1; $i<$NumNomi;++$i)
		    	$tmp.= '<br/>' . $archer['athletes'][$i]['athlete'] ;
		    $tmp.= '</td>';
	    }
		$tmp.= '<td class="NumberAlign Grassetto">' . $archer['completeScore'] . '</td>';
		if($View10s) $tmp.= '<td class="NumberAlign'.$Class10.'">' . $archer[$Field10] . '</td>';
		if($ViewX9s) $tmp.= '<td class="NumberAlign">' . $archer[$FieldX9] . '</td>';
	    $tmp.= '</tr>' . "\n";

		if($archer['rank']<=3)
		{
			if(isset($data['items'][5]) && $data['items'][5]['rank']==1 && $key>0)
				$ret[$IdEvent]['basso'].=$tmp;
			if(isset($data['items'][6]) && $data['items'][6]['rank']==2 && $key>1)
				$ret[$IdEvent]['basso'].=$tmp;
			if(isset($data['items'][7]) && $data['items'][7]['rank']==3 && $key>2)
				$ret[$IdEvent]['basso'].=$tmp;
			else
				$ret[$IdEvent]['fissi'].=$tmp;
		}
		else
			$ret[$IdEvent]['basso'].=$tmp;

	}

}

?>
