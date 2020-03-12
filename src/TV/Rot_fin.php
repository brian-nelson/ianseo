<?php

global $CFG;

require_once('Common/Lib/Obj_RankFactory.php');

$options=array('tournament' => $RULE->TVRTournament);
$Arr_Ev = array();
$Arr_Ph = array();
if($TVsettings->TVPEventInd) $Arr_Ev = explode('|', $TVsettings->TVPEventInd);
if(strlen($TVsettings->TVPPhasesInd)) {
	$Arr_Ph = explode('|', $TVsettings->TVPPhasesInd);
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

$rank=Obj_RankFactory::create('GridInd',$options);
$rank->read();
$rankData=$rank->getData();

if(count($rankData['sections'])==0) return '';

$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
$ViewTeams=(in_array('TEAM', $Columns) or in_array('ALL', $Columns)) ;
$ViewByes=(in_array('BYE', $Columns) or in_array('ALL', $Columns));
$View10s=(in_array('10', $Columns) or in_array('ALL', $Columns));
$TotalColWidth=7;
foreach($Columns as $sub) if(substr($sub,0,5)=='WIDTH') $TotalColWidth=substr($sub,6);

$NumCol=3 + $ViewTeams ;

if($TVsettings->TVPViewIdCard) {
	// opponents view with picture

	// check the pictures
	$fotow=min(200,intval($_SESSION['WINHEIGHT']/6)*4/3);
	include_once('Common/CheckPictures.php');
	CheckPictures($TourCode);

	// do the job
	$NumCol=4;
	foreach($rankData['sections'] as $IdEvent => $section) {
		$col=array();
		$col[]=25;
		$col[]=25;
		$col[]=25;
		$col[]=25;
		$cols='';
		foreach($col as $w) $cols.='<col width="'.$w.'%"></col>';
		foreach($section['phases'] as $IdPhase => $phase) {
			// Titolo della tabella
			$tmp = '<tr><th class="Title" colspan="' . ($NumCol) . '">';
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

				if(($IsBye=($item['tie']==2 or $item['oppTie']==2) or (!$item['athlete'] or !$item['oppAthlete'])) and !$ViewByes) continue;

				// 1a riga, è il target
				$Tgt=get_text('Target') . ' ' . ltrim($item['target'], '0');
				if($IsBye) {
					$Tgt=get_text('Bye');
					if($item['tie']==2 and $item['target']) $Tgt.= ' (' . get_text('Target') . ' ' . ltrim($item['target'], '0') . ')';
					elseif($item['oppTarget']) $Tgt.= ' (' . get_text('Target') . ' ' . ltrim($item['oppTarget'], '0') . ')';
				} elseif($item['target']!=$item['oppTarget']) {
					$Tgt=get_text('Targets','Tournament') . ' ' . ltrim($item['target'], '0') . '-' . ltrim($item['oppTarget'], '0');
				}
				$tmp = '<tr><th class="Title" colspan="4" align="center" style="font-weight:bold;">' . $Tgt . '</th></tr>';

				$NamSx=$item['athlete'];
				$NamDx=$item['oppAthlete'];

				if(!$TVsettings->TVPNameComplete) {
					$NamSx=$item['familyName'] . ' ' . FirstLetters($item['givenName']);
					$NamDx=$item['oppFamilyName'] . ' ' . FirstLetters($item['oppGivenName']);
				}

				if($TVsettings->TVPViewNationName) {
					// 2a riga, sono i nomi degli atleti
					$tmp.= '<tr align="center" style="font-size:150%">'
						. '<td width="50%" colspan="2"><b>' . $NamSx . '</b></td>'
						. '<td width="50%" colspan="2"><b>' . $NamDx . '</b></td>'
						. '</tr>';

					// 3a riga, nazioni
					$tmp.= '<tr align="center">'
						. '<td width="50%" colspan="2"><b>' . $item['countryName'] . '</b></td>'
						. '<td width="50%" colspan="2"><b>' . $item['oppCountryName'] . '</b></td>'
						. '</tr>';

				} else {
					// 2a riga, sono i nomi degli atleti E delle nazioni
					$NamSx .= ' (<span class="piccolo">'.$item['countryCode'].'</span>)';
					$NamDx .= ' (<span class="piccolo">'.$item['oppCountryCode'].'</span>)';
					if($item['tie']==2) $NamDx='&nbsp;';
					if($item['oppTie']==2) $NamSx='&nbsp;';
					$tmp.= '<tr align="center" style="font-size:150%">'
						. '<td width="50%" colspan="2"><b>' . $NamSx .'</b></td>'
						. '<td width="50%" colspan="2"><b>' . $NamDx . '</b></td>'
						. '</tr>';
				}

				$Score='&nbsp;';
				if($IsBye) {
					$Score = '<i style="font-size:75%">'.get_text('Bye').'</i>';
				} else {
					$SxFinScore = trim($section['meta']['matchMode'] ? $item['setScore']    : $item['score']);
					$DxFinScore = trim($section['meta']['matchMode'] ? $item['oppSetScore'] : $item['oppScore']);
					$TieBreak='';
					if($item['tie']==1 or $item['oppTie']==1) {
						if($item['tiebreakDecoded'] or $item['oppTiebreakDecoded']) {
							$TieBreak = '<div style="font-size:60%">&nbsp;T.&nbsp;'
								. $item['tiebreakDecoded']
								. '-'
								. $item['oppTiebreakDecoded']
								. '</div>';
						} elseif($item['tie']==1) {
							$SxFinScore .= '*';
						} else {
							$DxFinScore .= '*';
						}
					}
					$Score= $SxFinScore . '-' . $DxFinScore;
					if($TieBreak) $Score .= $TieBreak;
				}

				// 4a riga, le fotografie

				$fotSx=$CFG->DOCUMENT_PATH.'TV/'.($fotinaSx='Photos/'.$TourCode.'-En-'.$item['id'].'.jpg');
				$fotDx=$CFG->DOCUMENT_PATH.'TV/'.($fotinaDx='Photos/'.$TourCode.'-En-'.$item['oppId'].'.jpg');
				$fotNoPhoto=$CFG->DOCUMENT_PATH.'TV/'.($fotinaNophoto='Photos/'.$TourCode.'-nophoto.jpg');
				if($item['athlete'] and (file_exists($fotSx) or file_exists($fotNoPhoto)) ) {
					$fotinaSx = '<img class="athletephoto" src="'.(file_exists($fotSx) ? $fotinaSx : $fotinaNophoto).'" width="'.$fotow.'" alternate=""/>&nbsp;';
				} else {
					$fotinaSx = '&nbsp;';
				}
				if($item['oppAthlete'] and (file_exists($fotDx) or file_exists($fotNoPhoto)) ) {
					$fotinaDx = '<img class="athletephoto" src="'.(file_exists($fotDx) ? $fotinaDx : $fotinaNophoto).'" width="'.$fotow.'" alternate=""/>&nbsp;';
				} else {
					$fotinaDx = '&nbsp;';
				}
				$tmp.= '<tr align="center">'
					. '<td width="25%">'.$fotinaSx.'</td>'
					. '<td width="50%" colspan="2" style="font-size:200%; " id="match_'.$IdEvent.'_'.$item['matchNo'].'"><b>' . $Score . '</b></td>'
					. '<td width="25%">'.$fotinaDx.'</td>'
					. '</tr>';

				$ret["$IdEvent - $IdPhase"]['basso'].=$tmp;
			}
		}
	}
} else {
	// Grid view

	$NumColBase = 3 + $ViewTeams;
	foreach($rankData['sections'] as $IdEvent => $section) {
		foreach($section['phases'] as $IdPhase => $phase) {

			$NumCol = $NumColBase ;
			switch($IdPhase) {
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
			$tmp.= '<th>'. get_text('Archer') . '</th>' . "\n";
			$col[]=33;
			if($ViewTeams) {
				$tmp.= '<th>' . get_text('Country') . '</th>' . "\n";
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
					if($n<3) {
						$tmp.='<th class="small">'.get_text('2_Phase').'</th>';
						$col[]=3;
					}
				break;
			}
			$tmp.= '</tr>' . "\n";

			$SumCol=array_sum($col);
			$cols='';
			foreach($col as $w) $cols.='<col width="'.round(100*$w/$SumCol, 0).'%"></col>';

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
			foreach($phase['items'] as $key => $item) {
				// Dati della tabella aperta in (1)
				$tmp = '<tr' . $Style . '>';

				$NamSx=$item['athlete'];
				$NamDx=$item['oppAthlete'];
				$TeamSx=$item['countryCode'] . '&nbsp;' . $item['countryName'];
				$TeamDx=$item['oppCountryCode'] . '&nbsp;' . $item['oppCountryName'];

				if(!$TVsettings->TVPNameComplete) {
					$NamSx=$item['familyName'] . ' ' . FirstLetters($item['givenName']);
					$NamDx=$item['oppFamilyName'] . ' ' . FirstLetters($item['oppGivenName']);
				}

				if(!$TVsettings->TVPViewNationName) {
					$TeamSx=$item['countryCode'];
					$TeamDx=$item['oppCountryCode'];
				}

				if($item['tie']==2) {
					// it is a bye
					$tmp.= '<td>' . get_text('Bye') . '</td>'
						. '<td>' . $NamSx . '</td>'
						. ($ViewTeams ? '<td>' . $TeamSx . '</td>' : '')
						. '<td class="NumberAlign">&nbsp;</td>' . "\n";
				} elseif($item['athlete']) {
					// archer is there
					$tmp.= '<td>' . ltrim($item['target'], '0') . '</td>'
						. '<td>' . $NamSx . '</td>'
						. ($ViewTeams ? '<td>' . $TeamSx . '</td>' : '')
						. '<td class="NumberAlign" id="match_'.$IdEvent.'_'.$item['matchNo'].'">' . $item[$section['meta']['matchMode'] ? 'setScore' : 'score'] . ($item['tie']==1 ? '*' : ($item['oppTie']==1 ? '<tt>&nbsp;</tt>' : '')) . '</td>' . "\n";
				} else {
					// it is the bye's opponent?
					$tmp.= '<td>&nbsp;</td>'
						. '<td>&nbsp;</td>'
						. ($ViewTeams ? '<td>&nbsp;</td>' : '')
						. '<td class="NumberAlign">&nbsp;</td>' . "\n";

				}

				// prints the fake grids (32th)
				$grid=array('','','');

				if($IdPhase>=8) {
					$grid=get_rot_grids($item['matchNo'], $IdPhase);
				}

				$tmp.=$grid[0].'</tr>'. "\n";

				if (++$ii==2) {
					$ii=0;
					$Style=($Style=='' ? ' class="Next"' : '');
					if($item['matchNo'] < $IdPhase*4-1) {
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
					$tmp.= '<td>' . get_text('Bye') . '</td>'
						. '<td>' . $NamDx . '</td>'
						. ($ViewTeams ? '<td>' . $TeamDx . '</td>' : '')
						. '<td class="NumberAlign">&nbsp;</td>' . "\n";
				} elseif($item['oppAthlete']) {
					// archer is there
					$tmp.= '<td>' . ltrim($item['oppTarget'], '0') . '</td>'
						. '<td>' . $NamDx . '</td>'
						. ($ViewTeams ? '<td>' . $TeamDx . '</td>' : '')
						. '<td class="NumberAlign" id="match_'.$IdEvent.'_'.$item['oppMatchNo'].'">' . $item[$section['meta']['matchMode'] ? 'oppSetScore' : 'oppScore'] . ($item['oppTie']==1 ? '*' : ($item['tie']==1 ? '<tt>&nbsp;</tt>' : '')) . '</td>' . "\n";
				} else {
					// it is the bye's opponent?
					$tmp.= '<td>&nbsp;</td>'
						. '<td>&nbsp;</td>'
						. ($ViewTeams ? '<td>&nbsp;</td>' : '')
						. '<td class="NumberAlign">&nbsp;</td>' . "\n";
				}

				// prints the fake grids (32th)
				$grid=array('','','');

				if($IdPhase>=8) {
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