<?php
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_FormatText.inc.php');

function rotAbss($TVsettings, $RULE) {
	global $CFG, $IsCode, $TourId, $SubBlock;
	$CSS=unserialize($TVsettings->TVPSettings);
	getPageDefaults($CSS);
	$Return=array(
		'CSS' => $CSS,
		'html' => '',
		'Block' => 'QualRow',
		'BlockCss' => 'height:2em; width:100%; padding-right:0.5rem; overflow:hidden; font-size:2em; display:flex; flex-direction:row; justify-content:space-between; align-items:center; box-sizing:border-box;',
		'NextSubBlock' => 1,
		'SubBlocks' => 1,
		);
	$ret=array();

	$TourType=getTournamentType($TourId);

	$TVsettings->EventFilter=MakeEventFilter($TVsettings->TVPEventInd);

	$options=array('tournament' => $RULE->TVRTournament);
	$options['dist'] = 0;
	$options['records'] = 1;
	$options['subFamily'] = 'Abs';

	if(isset($TVsettings->TVPEventInd) && !empty($TVsettings->TVPEventInd))
		$options['events'] = explode('|',$TVsettings->TVPEventInd);
	if(isset($TVsettings->TVPNumRows) && $TVsettings->TVPNumRows>0)
		$options['cutRank'] = $TVsettings->TVPNumRows;
	if(isset($TVsettings->TVPSession) && $TVsettings->TVPSession>0)
		$options['session'] = $TVsettings->TVPSession;

	$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
	$ViewTeams=(in_array('TEAM', $Columns) or in_array('ALL', $Columns));
	$ViewFlag=(in_array('FLAG', $Columns) or in_array('ALL', $Columns));
	$ViewCode=(in_array('CODE', $Columns) or in_array('ALL', $Columns));
	$ViewDists=((in_array('DIST', $Columns) or in_array('ALL', $Columns)) and $TVsettings->TVPViewPartials);
	$View10s=(in_array('10', $Columns) or in_array('ALL', $Columns));
	$ViewX9s=(in_array('X9', $Columns) or in_array('ALL', $Columns));
	$ViewArrows=(in_array('ARROWS', $Columns) or in_array('ALL', $Columns));
	$Title2Rows=(in_array('TIT2ROWS', $Columns) ? '<br/>' : ': ');
	$Title2Arrows=(in_array('TIT2ROWS', $Columns) ? '<br/>' : ' - ');
	$ViewTOT=(in_array('TOT', $Columns) or in_array('ALL', $Columns));

	$comparedTo=preg_grep('/^COMP:/', $Columns);
	if(!empty($comparedTo)) {
		list(,$comparedTo) = explode(":",reset($comparedTo));
		$options['comparedTo'] = $comparedTo;
	}

	$Fixed=preg_grep('/^FIXED:/', $Columns);
	$FixedDone=false;
	if(!empty($Fixed)) {
		list(,$Fixed) = explode(":",reset($Fixed));
	}

	$rank=Obj_RankFactory::create('Snapshot',$options);
	$rank->read();
	$rankData=$rank->getData();

	if(count($rankData['sections'])==0) return $Return;

	$Return['SubBlocks']=count($rankData['sections']);

	$Return['NextSubBlock']=($SubBlock+1);
	if($SubBlock>count($rankData['sections'])) $SubBlock=1;

	foreach($rankData['sections'] as $IdEvent => $data) {
		$SubBlock--;
		if(!$SubBlock) {
			break;
		}
	}

	$FieldForGold=($data['meta']['running'] ? 'completeScore' : 'gold');
	$FieldForXNine=($data['meta']['running'] ? 'hits' : 'xnine');

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
		$retToAdd='';

		$ExtraCSS='';
		$NumRecords=0;
		$Final=(max($data['meta']['arrowsShot'])==$data['meta']['numDist']*$data['meta']['maxArrows']);
		foreach($data['records'] as $r) {
			$RecTot[$r->RtRecCode]['tot']=$r->RtRecMaxScore-$r->RtRecTotal;
			$RecTot[$r->RtRecCode]['gap']=$r->TrGaps;
			$RecTot[$r->RtRecCode]['area']=$r->ReArBitLevel;
			$RecTot[$r->RtRecCode]['claim']=$r->ReArMaCode;
			if($r->TrGaps and !$Final) {
				$RecTitle.='&nbsp;<span class="piccolo" style="color:#'.$r->TrColor.'">'.get_text('RecordAverage', 'Records');
				if($r->RtRecExtra and $tmp=unserialize($r->RtRecExtra)) {
					$RecTitle.=' ('.$r->RtRecTotal .' - '. $tmp[0]->Archers[0]['Archer'] . ')';
				}
				$RecTitle.='</span>';
			}
			$RecCut=max($RecCut, $RecTot[$r->RtRecCode]['tot']);
			$rec=round($r->RtRecTotal*max($data['meta']['arrowsShot'])/($data['meta']['numDist']*$data['meta']['maxArrows']),1);
			if($r->TrBars) {
				$NumRecords++;
				$ExtraCSS.=".Rec_{$r->RtRecCode} {background-color:#{$r->TrColor}; color:white;".($r->TrFontFile ? 'font-family:'.$r->RtRecCode.';' : '')."}";
				$tmp='<div class="QualRow Rec_'. $r->RtRecCode.'">
						<div class="Record">'.($Final ? $r->TrHeader : get_text('RecordAverage', 'Records', $r->RtRecCode)).'</div>
					<div class="Score">' . ($data['meta']['running'] ? number_format($rec/($data['meta']['numDist']*$data['meta']['maxArrows']), 3) : number_format($rec, $Final ? 0 : 1)) . '</div>
					'.($View10s ?  '<div class="Gold">&nbsp;</div>' : '').'
					'.($ViewX9s ? '<div class="XNine">&nbsp;</div>' : '').'
					</div>';
				$RecordCut["$rec"][]=$tmp;
			}
		}
		if($ExtraCSS) $Return['BlockCss'].="} $ExtraCSS {";
	}

	// TITLE
	$tmp = '';

	$ret[]='<div class="Title">
				<div class="TitleImg" style="float:left;"><img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$IsCode.'-ToLeft.jpg"></div>
				<div class="TitleImg" style="float:right;"><img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$IsCode.'-ToRight.jpg"></div>
		'.$rankData['meta']['title'].$Title2Rows.$data['meta']['descr'].$Title2Rows.$RecTitle
	//.($ViewArrows ? $Title2Arrows.$data['meta']['printHeader'] : '')
	.'</div>';

	// Header header;
	$tmp ='<div class="QualRow Headers">';


	// al 4° devo interrompere la tabella e aprire il resto in un div separato
	$tmp.='<div class="Rank Headers">' . $data['meta']['fields']['rank'] . '</div>';
	if(!empty($comparedTo)) {
		$tmp.='<div class="RankOld">&nbsp;</div>';
	}

	$tmp.=str_repeat('<div class="RecBar"></div>', $NumRecords);

	if($ViewCode) {
		$tmp.='<div class="CountryCode Rotate">&nbsp;</div>';
	}
	if($ViewFlag) {
		$tmp.='<div class="FlagDiv">&nbsp;</div>';
	}

	$tmp.='<div class="Target Headers">' . $data['meta']['fields']['target'] . '</div>';
	$tmp.='<div class="Athlete Headers">'.$data['meta']['fields']['athlete'].'</div>';

	if($ViewTeams) {
		$tmp.='<div class="CountryDescr Headers">' . $data['meta']['fields']['countryName'] . '</div>';
	}

	if($ViewDists) {
		for ($i=1; $i<=$rankData['meta']['numDist']; ++$i) {
			if($data['meta']['fields']['dist_'.$i]=='-') continue;
			$tmp.='<div class="DistScore Headers">' . $data['meta']['fields']['dist_'.$i] . '</div>';
			$tmp.='<div class="DistPos">&nbsp;</div>';
		}
	}

	if($ViewArrows) $tmp.='<div class="Arrows Headers">' . $data['meta']['fields']['hits'] . '</div>';

	$tmp.='<div class="Score Headers">' . get_text('SnapShort', 'Tournament', $data['meta']['snapArrows']) . '</div>';
	if($ViewTOT) {
		$tmp.='<div class="Score Headers">' . $data['meta']['fields']['score'] . '</div>';
	}
	if($View10s) $tmp.='<div class="Gold Headers">' . $data['meta']['fields'][$FieldForGold] . '</div>';
	if($ViewX9s) $tmp.='<div class="XNine Headers">' . $data['meta']['fields'][$FieldForXNine] . '</div>';
	$tmp.='</div>';
	$ret[]=$tmp;

	$ret[]=$retToAdd;

	if(!$Fixed) {
		$ret[]='<div id="content" data-direction="up">';
		$FixedDone=true;
	}

	// Inserisci adesso le singole righe
	foreach($data['items'] as $key => $archer) {
		if($Fixed and $archer['rank']>$Fixed and !$FixedDone) {
			$ret[]='<div id="content" data-direction="up">';
			$FixedDone=true;
		}

		foreach ($RecordCut as $Record => $FormattedRows) {
			if($archer['scoreSnap'] <= $Record) {
				$ret[]=implode('', $FormattedRows);
				unset($RecordCut[$Record]);
			}
		}

		$Class=($key%2 == 0 ? 'e': 'o');
		$tmp ='<div class="QualRow Font1'.$Class.' Back1'.$Class.'">';


		$tmp.='<div class="Rank">' . $archer['rank'] . '</div>';
		if(!empty($comparedTo)) {
			$cl="RankNone";
			if($archer['oldRank']) {
				if($archer['rank']==$archer['oldRank']) {
					$cl='RankMinus';
				} elseif($archer['rank']<$archer['oldRank']) {
					$cl='RankUp';
				} else {
					$cl='RankDown';
				}
			}
			$tmp.='<div class="RankOld '.$cl.'">' . ($archer['oldRank']&& $archer['oldRank']!=$archer['rank'] ? $archer['oldRank']:'&nbsp;'). '</div>';
		}

		foreach ($RecTot as $RecCode => $Record) {
			if(!$Final and $Record['gap'] and $archer['recordGap'] < $Record['tot'] and
				(!$Record['claim']
					or $Record['claim']==$archer['contAssoc']
					or $Record['claim']==$archer['memberAssoc'])) {
				$tmp.='<div class="RecBar Rec_'.$RecCode.'"></div>';
			} else {
				$tmp.='<div class="RecBar">&nbsp;</div>';
			}
		}


		if($ViewCode) {
			$tmp.='<div class="CountryCode Rotate Rev1'.$Class.'">' . $archer['countryCode'] . '</div>';
		}
		if($ViewFlag) {
			$tmp.='<div class="FlagDiv">'.get_flag_ianseo($archer['countryCode'], '', '', $IsCode).'</div>';
		}

		$tmp.='<div class="Target">' . ltrim($archer['target'],'0') . '</div>';
		$tmp.='<div class="Athlete">'.$archer['familynameUpper'].' '.($TVsettings->TVPNameComplete==0 ? FirstLetters($archer['givenname']) : $archer['givenname']).'</div>';
		if($ViewTeams) {
			$tmp.='<div class="CountryDescr">' . $archer['countryName'] . '</div>';
		}
		if($ViewDists) {
			for ($i=1; $i<=$rankData['meta']['numDist']; ++$i) {
				if($data['meta']['fields']['dist_'.$i]=='-') continue;
				list($rank, $score, $gold, $xnine)=explode('|', $archer['dist_'.$i]);
				$tmp.='<div class="DistScore">' . $score . '</div>';
				$tmp.='<div class="DistPos">/'.(($TourType != 14 and $TourType != 32) ? $rank : $xnine).'</div>';
			}
		}
		if($ViewArrows) $tmp.='<div class="Arrows">' . $archer['arrowsShot'] . '</div>';
		$tmp.='<div class="Score">' . $archer['scoreSnap'] . '</div>';
		if($ViewTOT) $tmp.= '<div class="Gold">' . ($archer['scoreSnap'] != $archer['score'] ?  $archer['score'] : "&nbsp;") . '</div>';
		if($View10s) $tmp.='<div class="Gold">' . $archer[$FieldForGold] . '</div>';
		if($ViewX9s) $tmp.='<div class="XNine">' . $archer[$FieldForXNine] . '</div>';
		$tmp.='</div>';
		$ret[]=$tmp;
	}
	if($FixedDone) $ret[]='</div>';

	$Return['html']=implode('', $ret);

	return $Return;
}

function rotAbssSettings($Settings) {
	global $CFG;
	$ret='<br/>';
	$ret.= '<table class="Tabella Css3">';
	$ret.= '<tr><th colspan="3">'.get_text('TVCss3SpecificSettings','Tournament').'</th></tr>';

	// defaults for fonts, colors, size
	$RMain=array();
	if(!empty($Settings)) {
		$RMain=unserialize($Settings);
	}

	$PageDefaults=getPageDefaults($RMain);

	// 	if(!isset($RMain[''])) $RMain['']='';
	// if(!isset($RMain[''])) $RMain['']='';

	foreach($PageDefaults as $key => $Value) {
		$ret.= '<tr>
			<th nowrap="nowrap" class="Right">'.get_text('TVCss3'.$key,'Tournament').' <input type="button" value="reset" onclick="document.getElementById(\'P-Main['.$key.']\').value=\''.$Value.'\'"></th>
			<td width="100%"><input type="text" name="P-Main['.$key.']" id="P-Main['.$key.']" value="'.$RMain[$key].'"></td>
			</tr>';
	}
	return $ret;
}

function getPageDefaults(&$RMain) {
	global $CFG;
	$ret=array(
		'Title' => '',
		'RankOld' => 'background-repeat:no-repeat; background-size: contain; background-position:center;color:#FFFFFF; font-weight:bold; font-size:60%;',
		'RankNone' => '',
		'RankUp' => 'background: url(\'' . $CFG->ROOT_DIR . 'Common/Images/Up.png\');',
		'RankDown' => 'background: url(\'' . $CFG->ROOT_DIR . 'Common/Images/Down.png\');',
		'RankMinus' => 'background: url(\'' . $CFG->ROOT_DIR . 'Common/Images/Minus.png\');',
		'Rank' => 'flex: 0 0 4vw; text-align:right;',
		'CountryCode' => 'flex: 0 0 3.5vw; font-size:0.8vw; margin-left:-3.75ch',
		'FlagDiv' => 'flex: 0 0 4.35vw;',
		'Flag' => 'height:2.8vw; border:0.05vw solid #888;box-sizing:border-box;',
		'Target' => 'flex: 0 0 6vw; text-align:right;margin-right:0.5em;',
		'Athlete' => 'flex: 1 1 20vw;white-space:nowrap;overflow:hidden;',
		'CountryDescr' => 'flex: 0 1 20vw;white-space:nowrap;overflow:hidden;',
		'Arrows' => 'flex: 0 0 5vw; text-align:right; font-size:1em;margin-right:0.5rem;',
		'DistScore' => 'flex: 0 0 5vw; text-align:right; font-size:0.8em;',
		'DistPos' => 'flex: 0 0 3vw; text-align:left; font-size:0.7em;',
		'Score' => 'flex: 0 0 6vw; text-align:right; font-size:1.25em;margin-right:0.5rem;',
		'Gold' => 'flex: 0 0 3vw; text-align:right; font-size:1em;',
		'XNine' => 'flex: 0 0 3vw; text-align:right; font-size:1em;',
	);
	foreach($ret as $k=>$v) {
		if(!isset($RMain[$k])) $RMain[$k]=$v;
	}
	return $ret;
}

function d() {



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
			$sql="select TrHeaderCode, TrHeader, RtRecCode, RtRecDistance, RtRecTotal, RtRecXNine, TrColor, RtRecExtra,
					find_in_set('bar', TrFlags) TrBars,
					find_in_set('gap', TrFlags) TrGaps
				from RecTournament
				inner join TourRecords on TrTournament=RtTournament and TrRecCode=RtRecCode and TrRecTeam=RtRecTeam and TrRecPara=RtRecPara
				inner join Events on RtTournament=EvTournament and EvRecCategory=RtRecCategory and EvCode='{$IdEvent}' and EvTournament={$TourId} and RtRecTeam=EvTeamEvent and EvTeamEvent=0
				where locate('metres', RtRecDistance)>0
				order by RtRecTotal desc "; // for now we only do on totals
			$q=safe_r_sql($sql);
			while($r=safe_fetch($q)) {
				$RecTot[$r->RtRecCode]['tot']=$MaxScore-$r->RtRecTotal;
				$RecTot[$r->RtRecCode]['gap']=$r->TrGaps;
				// no X9 checks now...
				// $RecTot[$r->RtRecCode]['X9']=$MaxScore-$r->RtRecTotal;
				if($r->TrGaps) {

					$RecTitle.='&nbsp;<span class="piccolo" style="color:#'.$r->TrColor.'">'.get_text('RecordAverage', 'Tournament', $r->TrHeaderCode);
					$RecTitle.='</span>';
				}


				$RecCut=max($RecCut, $RecTot[$r->RtRecCode]['tot']);
				$rec=round($r->RtRecTotal*max($section['meta']['arrowsShot'])/($section['meta']['numDist']*$section['meta']['maxArrows']),1); // no X9 checks now...
				if($r->TrBars) {
					$RecordCut["$rec"][]='<tr class="Record_'. $r->RtRecCode.'"><th colspan="%s">'.get_text('RecordAverage', 'Records', $r->TrHeader).'</th>
						<td class="NumberAlign Grassetto">' . number_format($rec,1, '.', '') . '</td>
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
			foreach ($RecTot as $RecType => $RecCodes) {
				foreach($RecCodes as $RecCode => $Record) {
					if($Record['gap'] and $item['recordGap'] < $Record['tot'] and ($RecCode=='WA' or $RecCode==$item['contAssoc'])) {
//						$RecClass='Rec-'.$RecType;
						$RecColumns.='<td class="Rec-Bg-'.$RecType.'">&nbsp;</td>';
					} else {
						$RecColumns.='<td>&nbsp;</td>';
					}
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
					$tmp .= '<td class="NumberAlign">' . str_pad($score,3," ",STR_PAD_LEFT) . '<span class="piccolo">/' . str_pad(($TourType!=14 ? $rank : $xnine),2," ",STR_PAD_LEFT) . '</span></td>';
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

}
