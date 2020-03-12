<?php
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_FormatText.inc.php');

function rotRankt($TVsettings, $RULE) {
	global $CFG, $IsCode, $TourId, $SubBlock;
	$CSS=unserialize($TVsettings->TVPSettings);
	getPageDefaults($CSS);
	$Return=array(
		'CSS' => $CSS,
		'html' => '',
		'Block' => 'QualRow',
		'BlockCss' => 'height:2em; width:100%; padding-right:0.5rem; overflow:hidden; font-size:2em; display:flex; flex-direction:row; justify-content:space-between; align-items:center; box-sizing:border-box;',
		'NextSubBlock' => 1,
		'SubBlocks' => 1);
	$ret=array();

	$TourType=getTournamentType($TourId);

	$TVsettings->EventFilter=MakeEventFilter($TVsettings->TVPEventTeam);

	$options=array('tournament' => $RULE->TVRTournament);
	$options['dist'] = 0;

	if(isset($TVsettings->TVPEventTeam) && !empty($TVsettings->TVPEventTeam))
		$options['events'] = explode('|',$TVsettings->TVPEventTeam);
	if(isset($TVsettings->TVPNumRows) && $TVsettings->TVPNumRows>0)
		$options['cutRank'] = $TVsettings->TVPNumRows;
	if(isset($TVsettings->TVPSession) && $TVsettings->TVPSession>0)
		$options['session'] = $TVsettings->TVPSession;

	$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
	$ViewTeams=(in_array('TEAM', $Columns) or in_array('ALL', $Columns));
	$ViewFlag=(in_array('FLAG', $Columns) or in_array('ALL', $Columns));
	$ViewCode=(in_array('CODE', $Columns) or in_array('ALL', $Columns));
	$ViewAths=(in_array('ATHL', $Columns) or in_array('ALL', $Columns));
	$ViewByes=(in_array('BYE', $Columns) or in_array('ALL', $Columns));
	$Title2Rows=(in_array('TIT2ROWS', $Columns) ? '<br/>' : ': ');

	$Fixed=preg_grep('/^FIXED:/', $Columns);
	$FixedDone=false;
	if(!empty($Fixed)) {
		list(,$Fixed) = explode(":",reset($Fixed));
	}

	$rank=Obj_RankFactory::create('FinalTeam',$options);
	$rank->read();
	$rankData=$rank->getData();

	if(count($rankData['sections'])==0) return $Return;

	$Return['SubBlocks']=count($rankData['sections']);
	$Return['NextSubBlock']=$SubBlock+1;

	if($SubBlock>count($rankData['sections'])) $SubBlock=1;

	foreach($rankData['sections'] as $IdEvent => $data) {
		$SubBlock--;
		if(!$SubBlock) {
			break;
		}
	}

	$FieldForGold='gold';
	$FieldForXNine='xnine';

	$RecTot=array();
	$RecTitle='';
	$RecCut=999999;
	$retToAdd='';
	$NumRecords=0;
	$RecordCut=array();

	// TITLE
	$tmp = '';

	$ret[]='<div class="Title">
				<div class="TitleImg" style="float:left;"><img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$IsCode.'-ToLeft.jpg"></div>
				<div class="TitleImg" style="float:right;"><img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$IsCode.'-ToRight.jpg"></div>
		'.$rankData['meta']['title'].$Title2Rows.$data['meta']['descr']
	.'</div>';

	// Header header;
	$tmp ='<div class="QualRow Headers">';


	// al 4° devo interrompere la tabella e aprire il resto in un div separato
	$tmp.='<div class="Rank Headers">'.get_text('Rank').'</div>';

	if($ViewCode) {
		$tmp.='<div class="CountryCode Rotate">&nbsp;</div>';
	}
	if($ViewFlag) {
		$tmp.='<div class="FlagDiv">&nbsp;</div>';
	}

	$tmp.='<div class="CountryDescr Headers">'.get_text('Team').'</div>';

	if($ViewAths) {
		$tmp.='<div class="Athlete Headers">'.$data['meta']['fields']['athletes']['fields']['athlete'].'</div>';
	}

	$tmp.='</div>';
	$ret[]=$tmp;

	$ret[]=$retToAdd;

	if(!$Fixed) {
		$ret[]='<div id="content" data-direction="up">';
		$FixedDone=true;
	}

	// Inserisci adesso le singole righe
	$cnt=1;
	foreach($data['items'] as $key => $archer) {
		if($archer['rank'] ==0) {
			continue;

		}
		if($Fixed and $archer['rank']>$Fixed and !$FixedDone) {
			$ret[]='<div id="content" data-direction="up">';
			$FixedDone=true;
		}

		foreach ($RecordCut as $Record => $FormattedRows) {
			if($archer['completeScore'] <= $Record) {
				$ret[]=implode('', $FormattedRows);
				unset($RecordCut[$Record]);
			}
		}

		$Class=($cnt%2 == 0 ? 'e': 'o');
		$tmp ='<div class="QualRow Font1'.$Class.' Back1'.$Class.'">';


		$tmp.='<div class="Rank">' . $archer['rank'] . '</div>';

		if($ViewCode) {
			$tmp.='<div class="CountryCode Rotate Rev1'.$Class.'">' . $archer['countryCode'] . '</div>';
		}
		if($ViewFlag) {
			$tmp.='<div class="FlagDiv">'.get_flag_ianseo($archer['countryCode'], '', '', $IsCode).'</div>';
		}

		$tmp.='<div class="CountryDescr">' . $archer['countryName'] . '</div>';

		if($ViewAths) {
			$tmp .= '<div class="Athlete">';
			if($archer['countryCode']) {
				foreach($archer['athletes'] as $ath) {
					$tmp.=$ath['athlete'].'<br/>';
				}
			}
			$tmp .= '</div>';
		}

		$tmp.='</div>';
		$ret[]=$tmp;
		$cnt++;
	}
	if($FixedDone) $ret[]='</div>';

	$Return['html']=implode('', $ret);

	return $Return;
}

function rotRanktSettings($Settings) {
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
		'Rank' => 'flex: 0 0 4vw; text-align:right;',
		'CountryCode' => 'flex: 0 0 3.5vw; font-size:0.8vw; margin-left:-3.75ch',
		'FlagDiv' => 'flex: 0 0 4.35vw;',
		'Flag' => 'height:2.8vw; border:0.05vw solid #888;box-sizing:border-box;',
		'Target' => 'flex: 0 0 6vw; text-align:right;margin-right:0.5em;',
		'Athlete' => 'flex: 1 1 20vw;font-size:0.5em;white-space:nowrap;overflow:hidden;',
		'CountryDescr' => 'flex: 1 1 25vw;white-space:nowrap;overflow:hidden;',
	);
	foreach($ret as $k=>$v) {
		if(!isset($RMain[$k])) $RMain[$k]=$v;
	}
	return $ret;
}

