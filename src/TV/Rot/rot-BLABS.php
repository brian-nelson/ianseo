<?php

require_once('Common/Lib/ArrTargets.inc.php');

function rotBlAbs($TVsettings, $RULE) {
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

	$TVsettings->EventFilter=MakeEventFilter($TVsettings->TVPEventInd);

	$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
	$ViewFlag=(in_array('FLAG', $Columns) or in_array('ALL', $Columns));
	$ViewCode=(in_array('CODE', $Columns) or in_array('ALL', $Columns));

	$Fixed=preg_grep('/^FIXED:/', $Columns);
	$FixedDone=false;
	if(!empty($Fixed)) {
		list(,$Fixed) = explode(":",reset($Fixed));
	}

	if(!$TVsettings->TVPEventTeam) return $Return;

	$Events=explode('|',$TVsettings->TVPEventTeam);

	$Return['SubBlocks']=count($Events);
	$Return['NextSubBlock']=$SubBlock+1;

	if($SubBlock>count($Events)) $SubBlock=1;

	$ret[]='<div id="content" data-direction="up">';

	$SubBlock--;
	$e=$Events[$SubBlock];

	$Sql = "SELECT CaTeam,CaSubTeam,CaEventCode,CaMatchNo, CoCode,CoName,CGGroup,"
		. "SUM(CaSPoints) AS Points,SUM(CaSScore) AS Score,SUM(CaSSetScore) AS SetScore,CaTiebreak, CaRank "
		. "FROM CasTeam "
		. "INNER JOIN Countries ON CaTeam=CoId "
		. "INNER JOIN CasGrid ON CaPhase=CGPhase AND (CaMatchNo=CGMatchNo1 OR CaMatchNo=CGMatchNo2) "
		. "INNER JOIN CasScore ON CaTournament=CaSTournament AND CaPhase=CaSPhase AND CaMatchNo=CaSMatchNo AND  CaEventCode=CaSEventCode AND CGRound=CaSRound "
		. "WHERE CaEventCode=" . StrSafe_DB($e) . " AND CaTournament=" . $RULE->TVRTournament . " AND CaPhase=0 "
		. "GROUP BY CaPhase,CaEventCode,CGGroup,CaTeam,CaSubTeam,CaMatchNo,CoCode,CoName,CaTiebreak,CaRank "
		. "ORDER BY CaEventCode ASC, CGGroup ASC, SUM(CaSPoints) DESC, SUM(CaSSetScore) DESC, CaRank ASC ";

	$q=safe_r_sql($Sql);

	$tmp = '';
	$OldGroup='';
	while($r=safe_fetch($q)) {
		if($OldGroup!=$r->CGGroup) {
			// TITLE
			$ret[]='<div class="Title">
				<div class="TitleImg" style="float:left;"><img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$IsCode.'-ToLeft.jpg"></div>
				<div class="TitleImg" style="float:right;"><img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$IsCode.'-ToRight.jpg"></div>
				'.get_text('Group', 'Tournament').' ' . chr(64+$r->CGGroup) . '</div>';

			// Header header;
			$tmp ='<div class="QualRow Headers">';
			$tmp.='<div class="Rank Headers">' . get_text('Rank') . '</div>';
			if($ViewCode) {
				$tmp.='<div class="CountryCode Rotate">&nbsp;</div>';
			}
			if($ViewFlag) {
				$tmp.='<div class="FlagDiv">&nbsp;</div>';
			}
			$tmp.='<div class="Athlete Headers">'.get_text('Team').'</div>';
			$tmp.='<div class="Score Headers">' . get_text('Points', 'Tournament') . '</div>';
            $tmp.='<div class="Score Headers">' . get_text('SetPoints', 'Tournament') . '</div>';
			$tmp.='<div class="XNine Headers">' . get_text('Tie') . '</div>';
			$tmp.='</div>';
			$ret[]=$tmp;
			$OldGroup=$r->CGGroup;

			$myRank = 0;
			$myPos = 0;
			$OldPoints = 0;
            $OldSetPoints = 0;
			$OldTie=0;

			$key=0;
		}

		$Class=($key%2 == 0 ? 'e': 'o');
		$tmp ='<div class="QualRow Font1'.$Class.' Back1'.$Class.'">';

		//Calcolo della Rank;
		$myPos++;
		if(!($r->Points == $OldPoints AND $r->SetScore == $OldSetPoints AND $r->CaTiebreak == $OldTie)) {
			$myRank=$myPos;
		}
		$tmp.='<div class="Rank">' . $myRank . '</div>';

		if($ViewCode) {
			$tmp.='<div class="CountryCode Rotate Rev1'.$Class.'">' . $r->CoCode . '</div>';
		}
		if($ViewFlag) {
			$tmp.='<div class="FlagDiv">'.get_flag_ianseo($r->CoCode, '', '', $IsCode).'</div>';
		}

		$tmp.='<div class="Athlete">'.$r->CoName.'</div>';

		$tmp.='<div class="Score">' . $r->Points . '</div>';
        $tmp.='<div class="Score">' . $r->SetScore . '</div>';

		//Valuto il TieBreak
		$TmpTie = '';
		if(strlen(trim($r->CaTiebreak)) > 0) {
			for($countArr=0; $countArr<strlen(trim($r->CaTiebreak)); $countArr = $countArr+3)
				$TmpTie .= ValutaArrowString(substr(trim($r->CaTiebreak),$countArr,3)) . ",";
				$TmpTie = substr($TmpTie,0,-1);
		}

		$tmp.='<div class="XNine">' . $TmpTie . '</div>';

		$OldPoints = $r->Points;
        $OldSetPoints = $r->SetScore;
		$OldTie = $r->CaTiebreak;

		$tmp.='</div>';
		$ret[]=$tmp;

		$key++;
	}

	$ret[]='</div>';

	$Return['html']=implode('', $ret);

	return $Return;
}

function rotBlAbsSettings($Settings) {
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
		'Title' => 'margin-top:1em;',
		'Rank' => 'flex: 0 0 4vw; text-align:right;',
		'Athlete' => 'flex: 1 1 20vw;white-space:nowrap;overflow:hidden;',
		'Score' => 'flex: 1 0 10vw; text-align:right; font-size:1.25em;margin-right:0.5rem;',
		'XNine' => 'flex: 1 0 4vw; text-align:right; font-size:1em;',
		'CountryCode' => 'flex: 0 0 3.5vw; font-size:0.8vw; margin-left:-3.75ch',
		'FlagDiv' => 'flex: 0 0 4.35vw;',
		'Flag' => 'height:2.8vw; border:0.05vw solid #888;box-sizing:border-box;',
	);
	foreach($ret as $k=>$v) {
		if(!isset($RMain[$k])) $RMain[$k]=$v;
	}
	return $ret;
}