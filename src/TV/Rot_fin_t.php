<?php

global $CFG;

require_once('Common/Lib/Obj_RankFactory.php');

$options=array('tournament' => $RULE->TVRTournament);
$Arr_Ev = array();
$Arr_Ph = array();
if($TVsettings->TVPEventTeam) $Arr_Ev = explode('|', $TVsettings->TVPEventTeam);
if(strlen($TVsettings->TVPPhasesTeam)) {
	$Arr_Ph = explode('|', $TVsettings->TVPPhasesTeam);
}

if($Arr_Ev and count($Arr_Ph)) {
	$options['events']=array();
	foreach($Arr_Ph as $p) {
		foreach($Arr_Ev as $e) $options['events'][] = $e . '@' . $p;
	}
} elseif(count($Arr_Ph)) {
	$options['events']=array();
	if(strstr($Arr_Ph[0], '+')) {
		foreach($Arr_Ph as $p) {
			$t=explode('+',$p);
			$l=array_shift($t);
			foreach($t as $e) $options['events'][] = $l . '@' . $e;
		}
	} else {
		foreach($Arr_Ph as $p) {
			$options['events'][] = '@' . $p;
		}
	}
} elseif($Arr_Ev) {
	$options['events'] = $Arr_Ev;
}

$rank=Obj_RankFactory::create('GridTeam',$options);
$rank->read();
$rankData=$rank->getData();

if(count($rankData['sections'])==0) return '';

$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
$ViewAthls=(in_array('ATHL', $Columns) or in_array('ALL', $Columns)) ;
$ViewByes=(in_array('BYE', $Columns) or in_array('ALL', $Columns));
$ViewFlags=(in_array('FLAG', $Columns) or in_array('ALL', $Columns));
$TotalColWidth=7;
foreach($Columns as $sub) if(substr($sub,0,5)=='WIDTH') $TotalColWidth=substr($sub,6);
//$View10s=(in_array('10', $Columns) or in_array('ALL', $Columns));

if($TVsettings->TVPViewIdCard) {
	// opponents view with picture

	// check the pictures
	$fotow=min(200, intval($_SESSION['WINHEIGHT']/6)*4/3, intval($_SESSION['WINWIDTH']/7));
	include_once('Common/CheckPictures.php');
	CheckPictures($TourId);

	// do the job
	$NumCol=4;
	foreach($rankData['sections'] as $IdEvent => $section) {
		$colspan=$section['meta']['maxTeamPerson'];
		$SumCol=round(100/($colspan*2),0);
		$cols='';
		foreach(range(1,$colspan*2) as $w) $cols.='<col width="'.$SumCol.'%"></col>';

		foreach($section['phases'] as $IdPhase => $phase) {
			// Titolo della tabella
			$tmp = '<tr><th class="Title" colspan="' . ($colspan*2) . '">';
			$tmp.= $section['meta']['eventName'] . " - " . $phase['meta']['phaseName'];
			$tmp.= '</th></tr>' . "\n";

			$ret["$IdEvent - $IdPhase"]['head']=$tmp;
			$ret["$IdEvent - $IdPhase"]['cols']=$cols;
			$ret["$IdEvent - $IdPhase"]['fissi']='';
			$ret["$IdEvent - $IdPhase"]['basso']='';
			$ret["$IdEvent - $IdPhase"]['type']='DB';
			$ret["$IdEvent - $IdPhase"]['style']=$ST;
			$ret["$IdEvent - $IdPhase"]['js']=$JS;
			$ret["$IdEvent - $IdPhase"]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId.'&Event='.$IdEvent.'&Phase='.$IdPhase."';\n";

			foreach($phase['items'] as $key => $item) {
				if(($IsBye=($item['tie']==2 or $item['oppTie']==2)) and !$ViewByes) continue;

				// 1a riga, è il target
				$tgtSx=get_text('Target') . ' ' . ltrim($item['target'],'0');
				$tgtDx=get_text('Target') . ' ' . ltrim($item['oppTarget'],'0');
				if($item['tie']==2) {
					$tgtSx=get_text('Bye') . ($item['target'] ? ' (' . $tgtSx . ')' : '');
					$tgtDx='&nbsp;';
				} elseif($item['oppTie']==2) {
					$tgtSx='&nbsp;';
					$tgtDx=get_text('Bye') . ($item['oppTarget'] ? ' (' . $tgtDx . ')' : '');
				}
				$tmp = '<tr>'
					. '<th class="Title" colspan="'.$colspan.'" align="center" style="font-weight:bold;">' . $tgtSx . '</th>'
					. '<th class="Title" colspan="'.$colspan.'" align="center" style="font-weight:bold;">' . $tgtDx . '</th>'
					. '</tr>';

				// 2a riga, nazioni
				$natSx=$item['countryCode'] . ($TVsettings->TVPViewNationName ? ' ' . $item['countryName'] : '');
				$natDx=$item['oppCountryCode'] . ($TVsettings->TVPViewNationName ? ' ' . $item['oppCountryName'] : '');
				$tmp.= '<tr align="center" style="font-size:150%">'
					. '<td colspan="'.$colspan.'" width="50%"><b>' . $natSx . '</b></td>'
					. '<td colspan="'.$colspan.'" width="50%"><b>' . $natDx . '</b></td>'
					. '</tr>';

				// 3a riga, sono i nomi degli atleti
				$tmp.= '<tr align="center" style="font-size:50%">';

				// 4a riga, le fotografie
				$tmp4= '<tr align="center">';

				$tmpSx='';
				$tmpDx='';
				$tmp4Sx='';
				$tmp4Dx='';
				for($n=0; $n<$colspan; $n++) {
					if(!empty($section['athletes'][$item['teamId']][0][$n])) {
						$at=$section['athletes'][$item['teamId']][0][$n];
						$name=($TVsettings->TVPNameComplete ? $at['athlete'] : $at['familyName'] . ' ' . FirstLetters($at['givenName']));

						$fot=$CFG->DOCUMENT_PATH.'TV/'.($fotina='Photos/'.$TourCode.'-En-'.$at['id'].'.jpg');
						$tmpSx.= '<td><b>' . $name . '</b></td>';
						$tmp4Sx.= '<td>'.(file_exists($fot) ? '<img class="athletephoto" src="'.$fotina.'" width="'.$fotow.'" alternate=""/>':'&nbsp;').'</td>';
					} else {
						$tmpSx.= '<td>&nbsp;</td>';
						$tmp4Sx.= '<td>&nbsp;</td>';
					}

					if(!empty($section['athletes'][$item['oppTeamId']][0][$n])) {
						$at=$section['athletes'][$item['oppTeamId']][0][$n];
						$name=($TVsettings->TVPNameComplete ? $at['athlete'] : $at['familyName'] . ' ' . FirstLetters($at['givenName']));

						$fot=$CFG->DOCUMENT_PATH.'TV/'.($fotina='Photos/'.$TourCode.'-En-'.$at['id'].'.jpg');
						$tmpDx.= '<td><b>' . $name . '</b></td>';
						$tmp4Dx.= '<td>'.(file_exists($fot) ? '<img class="athletephoto" src="'.$fotina.'" width="'.$fotow.'" alternate=""/>':'&nbsp;').'</td>';
					} else {
						$tmpDx.= '<td>&nbsp;</td>';
						$tmp4Dx.= '<td>&nbsp;</td>';
					}
				}

				$tmp.= $tmpSx . $tmpDx . '</tr>';
				$tmp.= $tmp4 . $tmp4Sx . $tmp4Dx . '</tr>';

				// 5a riga, punteggio
				$SxFinScore='&nbsp;';
				$DxFinScore='&nbsp;';

				if($item['tie']==2) {
					// has a bye
					$SxFinScore = get_text('Bye') ;
				} elseif($item['oppTie']==2) {
					// has a bye
					$DxFinScore = get_text('Bye') ;
				} else {
// 					$SxFinScore = $item['score'];
// 					$DxFinScore = $item['oppScore'];
					$SxFinScore = trim($section['meta']['matchMode'] ? $item['setScore']    : $item['score']);
					$DxFinScore = trim($section['meta']['matchMode'] ? $item['oppSetScore'] : $item['oppScore']);
					if($item['tie']==1 or $item['oppTie']==1) {
						if($item['tiebreakDecoded'] or $item['oppTiebreakDecoded']) {
							$SxFinScore .= '<span style="font-size:50%">&nbsp;T.'.$item['tiebreakDecoded'].'</span>';
							$DxFinScore .= '<span style="font-size:50%">&nbsp;T.'.$item['oppTiebreakDecoded'].'</span>';
						} elseif($item['tie']==1) {
							$SxFinScore .= '*';
						} else {
							$DxFinScore .= '*';
						}
					}
				}

				$tmp.= '<tr align="center" style="font-size:250%">'
					. '<td colspan="'.$colspan.'" width="50%"><b>' . $SxFinScore . '</b></td>'
					. '<td colspan="'.$colspan.'" width="50%"><b>' . $DxFinScore . '</b></td>'
					. '</tr>';

				$ret["$IdEvent - $IdPhase"]['basso'].=$tmp;
			}
		}
	}
} else {
	// Grid view

	$NumColBase = 3 + $ViewAthls + $ViewFlags;
	foreach($rankData['sections'] as $IdEvent => $section) {
		foreach($section['phases'] as $IdPhase => $phase) {

			$NumCol = $NumColBase ;
			switch($IdPhase) {
				case 4: $NumCol+=2; break;
				case 8: $NumCol+=2; break;
				case 16: $NumCol+=3; break;
				case 32: case 64: $NumCol+=4; break;
			}

			// Titolo della tabella
			$tmp = '<tr><th class="Title" colspan="' . ($NumCol) . '">';
			$tmp.= $section['meta']['eventName'] . " - " . $phase['meta']['phaseName'];
			$tmp.= '</th></tr>' . "\n";

			$col = array();
			$tmp.= '<tr>' . "\n";
			$tmp.= '<th>' . get_text('Target') . '</th>' . "\n";
			$col[]=8;
			if($ViewFlags) {
				$tmp.= '<th colspan="2">'. get_text('Country') . '</th>' . "\n";
				$col[]=8;
				$col[]=0;
			} else {
				$tmp.= '<th>'. get_text('Country') . '</th>' . "\n";
				$col[]=0;
			}
			if($ViewAthls) {
				$tmp.= '<th>' . get_text('Archer') . '</th>' . "\n";
				$col[]=35;
			}
			$tmp.= '<th>' . get_text('TotaleScore') . '</th>' . "\n";
			$col[]=$TotalColWidth;
			$n=0;
			switch($IdPhase) {
				case '64':
					$tmp.='<th class="small">'.get_text($section['meta']['firstPhase']=='48' ? '24_Phase' : '32_Phase').'</th>';
					$col[]=3;
					$n++;
				case '32':
					$tmp.='<th class="small">'.get_text('16_Phase').'</th>';
					$col[]=3;
					$n++;
				case '16':
					$tmp.='<th class="small">'.get_text('8_Phase').'</th>';
					$col[]=3;
					$n++;
				case '8':
					$tmp.='<th class="small">'.get_text('4_Phase').'</th>';
					$col[]=3;
				case '4':
					if($n<3) {
						$tmp.='<th class="small">'.get_text('2_Phase').'</th>';
						$col[]=3;
						if($IdPhase=='4') {
							$tmp.='<th class="small">&nbsp;</th>';
							$col[]=3;
						}
					}
				break;
			}
			$tmp.= '</tr>' . "\n";

			$col[1+$ViewFlags]=100-array_sum($col);
			$cols='';
			foreach($col as $w) $cols.='<col width="'.$w.'%"></col>';

			$ret["$IdEvent - $IdPhase"]['head']=$tmp;
			$ret["$IdEvent - $IdPhase"]['cols']=$cols."\n";
			$ret["$IdEvent - $IdPhase"]['fissi']='';
			$ret["$IdEvent - $IdPhase"]['basso']='';
			$ret["$IdEvent - $IdPhase"]['type']='DB';
			$ret["$IdEvent - $IdPhase"]['style']=$ST;
			$ret["$IdEvent - $IdPhase"]['js']=$JS;
			$ret["$IdEvent - $IdPhase"]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId.'&Event='.$IdEvent."&Phase=$IdPhase';\n";

			$ii=0;
			$Style='';
			$colspan=$section['meta']['maxTeamPerson'];
			foreach($phase['items'] as $key => $item) {
				// Dati della tabella aperta in (1)
				$tmp = '<tr' . $Style . '>';

				$FotoSx=(file_exists($CFG->DOCUMENT_PATH.'TV/'.($FotinaSx='Photos/'.$TourCode.'-Fl-'.$item['countryCode'].'.jpg')) ? '<img src="'.$FotinaSx.'" width="100%" alternate=""/>' : '&nbsp;');
				$FotoDx=(file_exists($CFG->DOCUMENT_PATH.'TV/'.($FotinaDx='Photos/'.$TourCode.'-Fl-'.$item['oppCountryCode'].'.jpg')) ? '<img src="'.$FotinaDx.'" width="100%" alternate=""/>' : '&nbsp;');
				$TeamSx=$item['countryCode'] . '&nbsp;' . $item['countryName'];
				$TeamDx=$item['oppCountryCode'] . '&nbsp;' . $item['oppCountryName'];

				if(!$TVsettings->TVPViewNationName) {
					$TeamSx=$item['countryCode'];
					$TeamDx=$item['oppCountryCode'];
				}

				$NameSx=array();
				$NameDx=array();

				if($ViewAthls) {
					for($n=0; $n<$colspan; $n++) {
						if(!empty($section['athletes'][$item['teamId']][0][$n])) {
							$at=$section['athletes'][$item['teamId']][0][$n];
							$NameSx[]=($TVsettings->TVPNameComplete ? $at['athlete'] : $at['familyName'] . ' ' . FirstLetters($at['givenName']));
						} else {
							$NameSx[]='&nbsp;';
						}

						if(!empty($section['athletes'][$item['oppTeamId']][0][$n])) {
							$at=$section['athletes'][$item['oppTeamId']][0][$n];
							$NameDx[]=($TVsettings->TVPNameComplete ? $at['athlete'] : $at['familyName'] . ' ' . FirstLetters($at['givenName']));
						} else {
							$NameDx[]='&nbsp;';
						}
					}
				}

				if($item['tie']==2) {
					// it is a bye
					$tmp.= '<td>' . ltrim($item['target'], '0') . '</td>'
						. ($ViewFlags ? '<td class="flagphoto">' . $FotoSx . '</td>' : '')
						. '<td>' . $TeamSx . '</td>'
						. ($ViewAthls ? '<td class="bottom">' . implode('<br/>', $NameSx) . '</td>' : '')
						. '<td class="NumberAlign bottom"><span class="piccolo">'.get_text('Bye').'</span></td>' . "\n";
				} elseif($item['countryCode']) {
					// archer is there
					$tmp.= '<td>' . ltrim($item['target'], '0') . '</td>'
						. ($ViewFlags ? '<td class="flagphoto">' . $FotoSx . '</td>' : '')
						. '<td>' . $TeamSx . '</td>'
						. ($ViewAthls ? '<td class="bottom">' . implode('<br/>', $NameSx) . '</td>' : '')
						. '<td class="NumberAlign bottom">' . $item[$section['meta']['matchMode'] ? 'setScore' : 'score'] . ($item['tie']==1 ? '*' : ($item['oppTie']==1 ? '<tt>&nbsp;</tt>' : '')) . '</td>' . "\n";
				} else {
					// it is the bye's opponent?
					$tmp.= '<td>&nbsp;</td>'
						. ($ViewFlags ? '<td class="flagphoto">&nbsp;</td>' : '')
						. '<td>&nbsp;</td>'
						. ($ViewAthls ? '<td class="bottom">' . implode('<br/>', $NameSx) . '</td>' : '')
						. '<td class="NumberAlign bottom">&nbsp;</td>' . "\n";

				}

				// prints the fake grids (32th)
				$grid=array('','','');

				if($IdPhase>=4) {
					$grid=get_rot_grids($item['matchNo'], $IdPhase);
				}

				$tmp.=$grid[0].'</tr>'. "\n";

				if (++$ii==2) {
					$ii=0;
					$Style=($Style=='' ? ' class="Next"' : '');
					if($IdPhase>4 and $item['matchNo'] < $IdPhase*4-1) {
						$tmp.='<tr class="bg"><td rowspan="2" colspan="'.($NumColBase).'"></td>'.$grid[1].'</tr>';
						$tmp.='<tr class="bg">'.$grid[2].'</tr>';
					}
				}

				$ret["$IdEvent - $IdPhase"]['basso'].=$tmp;

				// OPPONENT DATA
				// Dati della tabella aperta in (1)
				$tmp = '<tr' . $Style . '>';

				if($item['oppTie']==2) {
					// it is a bye
					$tmp.= '<td>' . ltrim($item['oppTarget'], '0') . '</td>'
						. ($ViewFlags ? '<td class="flagphoto">' . $FotoDx . '</td>' : '')
						. '<td>' . $TeamDx . '</td>'
						. ($ViewAthls ? '<td>' . implode('<br/>', $NameDx) . '</td>' : '')
						. '<td class="NumberAlign"><span class="piccolo">' . get_text('Bye') . '</span></td>' . "\n";
				} elseif($item['oppCountryCode']) {
					// archer is there
					$tmp.= '<td>' . ltrim($item['oppTarget'], '0') . '</td>'
						. ($ViewFlags ? '<td class="flagphoto">' . $FotoDx . '</td>' : '')
						. '<td>' . $TeamDx . '</td>'
						. ($ViewAthls ? '<td>' . implode('<br/>', $NameDx) . '</td>' : '')
						. '<td class="NumberAlign">' . $item[$section['meta']['matchMode'] ? 'oppSetScore' : 'oppScore'] . ($item['oppTie']==1 ? '*' : ($item['tie']==1 ? '<tt>&nbsp;</tt>' : '')) . '</td>' . "\n";
				} else {
					// it is the bye's opponent?
					$tmp.= '<td>&nbsp;</td>'
						. ($ViewFlags ? '<td class="flagphoto">&nbsp;</td>' : '')
						. '<td>&nbsp;</td>'
						. ($ViewAthls ? '<td>' . implode('<br/>', $NameDx) . '</td>' : '')
						. '<td class="NumberAlign">&nbsp;</td>' . "\n";
				}

				// prints the fake grids (32th)
				$grid=array('','','');

				if($IdPhase>=4) {
					$grid=get_rot_grids($item['oppMatchNo'], $IdPhase);
				}

				$tmp.=$grid[0].'</tr>'. "\n";

				if (++$ii==2) {
					$ii=0;
					$Style=($Style=='' ? ' class="Next"' : '');
					if($item['oppMatchNo'] < $IdPhase*4-1) {
						$tmp.='<tr class="bg"><td rowspan="2" colspan="'.$NumColBase.'"></td>'.$grid[1].'</tr>';
						$tmp.='<tr class="bg">'.$grid[2].'</tr>';
					}
				}

				$ret["$IdEvent - $IdPhase"]['basso'].=$tmp;
			}
		}
	}
}

?>