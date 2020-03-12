<?php
function rotList($TVsettings, $RULE) {
	global $CFG, $IsCode, $TourId, $SubBlock;
	$CSS=unserialize($TVsettings->TVPSettings);
	getPageDefaults($CSS);
	$Return=array(
		'CSS' => $CSS,
		'html' => '',
		'Block' => 'StartList',
		'BlockCss' => 'height:2em; width:100%; overflow:hidden; font-size:2em; display:flex; flex-direction:row; justify-content:space-between; align-items:center; box-sizing:border-box;',
		'NextSubBlock' => 1,
		'SubBlocks' => 1);
	$ret=array();

	$Select = "SELECT EnCode as Bib, EnName AS Name, SesName, DivDescription, ClDescription, upper(EnFirstName) AS FirstName, SUBSTRING(AtTargetNo,1,1) AS Session, SUBSTRING(AtTargetNo,2) AS TargetNo, CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode, EnDivision AS DivCode, EnAgeClass as AgeClass, EnSubClass as SubClass, EnStatus as Status "
		. "FROM AvailableTarget at "
		. "LEFT JOIN "
		. "(SELECT SesName, QuTargetNo, DivDescription, ClDescription, EnCode, EnName, EnFirstName, CoCode, CoName, EnClass, EnDivision, EnAgeClass, EnSubClass, EnStatus, EnIndClEvent, EnTeamClEvent, EnIndFEvent, EnTeamFEvent "
		. "FROM Qualifications AS q  "
		. "INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($TourId)  . " AND EnAthlete=1 "
		. "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
		. "LEFT JOIN Classes ON EnClass=ClId AND EnTournament=ClTournament "
		. "LEFT JOIN Session ON QuSession=SesOrder AND SesType='Q' AND EnTournament=SesTournament "
		. "LEFT JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament) as Sq ON at.AtTargetNo=Sq.QuTargetNo "
		. "WHERE AtTournament = " . StrSafe_DB($TourId) . " AND EnCode IS NOT NULL "
		. ($TVsettings->TVPSession ? " AND AtTargetNo LIKE " . StrSafe_DB($TVsettings->TVPSession . "%") : "") . " "
		. "ORDER BY AtTargetNo, CoCode, Name, CoName, FirstName ";
	$Rs=safe_r_sql($Select);

	$RowCounter = 0;
	$oldTarget='';
	$Class='';
	$OldSession='';
	$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
	$ViewTeams=(in_array('TEAM', $Columns) or in_array('ALL', $Columns));
	$ViewFlag=(in_array('FLAG', $Columns) or in_array('ALL', $Columns));
	$ViewCode=(in_array('CODE', $Columns) or in_array('ALL', $Columns));
	$ViewCat=(in_array('DIVCLAS', $Columns) or in_array('ALL', $Columns));
	$ViewCatCode=(in_array('CATCODE', $Columns) or in_array('ALL', $Columns));
	$Title2Rows=(in_array('TIT2ROWS', $Columns) ? '<br/>' : ': ');


	$ret[]='<div class="Title">
				<div class="TitleImg" style="float:left;"><img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$IsCode.'-ToLeft.jpg"></div>
				<div class="TitleImg" style="float:right;"><img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$IsCode.'-ToRight.jpg"></div>
			'.get_text('StartlistSession','Tournament').'</div>';
	$ret[]='<div class="StartList Headers">'
		. '<div class="Target Headers">' . get_text('Target') . '</div>'
		. ($ViewCode ? '<div class="CountryCode Rotate Headers">&nbsp;</div>' : '')
		. ($ViewFlag ? '<div class="FlagDiv Headers">&nbsp;&nbsp;&nbsp;</div>' : '')
		. '<div class="Athlete Headers">' . get_text('Athlete') . '</div>'
		. ($ViewTeams ? '<div class="CountryDescr Headers">' . get_text('Country') . '</div>' : '')
		. ($ViewCatCode ? '<div class="CategoryCode Headers">' . ($ViewCat ? '&nbsp;' : get_text('DivisionClass')) . '</div>' : '')
		. ($ViewCat ? '<div class="Category Headers">' . get_text('DivisionClass') . '</div>' : '')
		. '</div>';

	$ret[]='<div id="content" data-direction="up">';
	while($MyRow=safe_fetch($Rs)) {
		if($OldSession!=$MyRow->Session) {
			$OldSession=$MyRow->Session;

			$ret[]='<div class="SubTitle">'.($MyRow->SesName ? $MyRow->SesName : get_text('Session') . ' ' . $MyRow->Session).'</div>';
		}
		if($oldTarget!=intval($MyRow->TargetNo)) {
			$oldTarget=intval($MyRow->TargetNo);
			$RowCounter++;
		}
		$Class=($RowCounter%2 ? 'e' : 'o');
		$tmp= '<div class="StartList Font1'.$Class.' Back1'.$Class.'">';
		$tmp.='<div class="Target">' . ltrim($MyRow->TargetNo, '0') . '</div>';
		if($ViewCode) {
			$tmp.='<div class="CountryCode Rotate Rev1'.$Class.'">'.$MyRow->NationCode.'</div>';
		}
		if($ViewFlag) {
			$tmp.='<div class="FlagDiv">'.get_flag_ianseo($MyRow->NationCode, '', '', $IsCode).'</div>';
		}
		$tmp.='<div class="Athlete">' . $MyRow->FirstName . ' ' . ($TVsettings->TVPNameComplete==0 ? FirstLetters($MyRow->Name) : $MyRow->Name) . '</div>';
		if($ViewTeams) {
			$tmp.= '<div class="CountryDescr">' . $MyRow->Nation . '</div>';
		}
		if($ViewCatCode) {
			$tmp.= '<div class="CategoryCode">' . $MyRow->DivCode . $MyRow->ClassCode . '</div>';
		}
		if($ViewCat) {
			$tmp.= '<div class="Category">' . $MyRow->DivDescription . ' ' . $MyRow->ClDescription . '</div>';
		}
		$tmp.= '</div>';

		$ret[]=$tmp;
	}
	$ret[]='</div>';

	$Return['html']=implode('', $ret);
	return $Return;
}

function rotListSettings($Settings) {
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
		'SubTitle' => 'margin-top:1vh; padding:0.25em 0.5em; background: linear-gradient(#1E5799, #7DB9E8);font-size:2.5vw; text-align:center; width:100%; box-sizing:border-box;color: white;',
		'CountryCode' => 'flex: 0 0 3.5vw; font-size:0.8vw; margin-left:-3.75ch',
		'FlagDiv' => 'flex: 0 0 4.35vw;',
		'Flag' => 'height:2.8vw; border:0.05vw solid #888;box-sizing:border-box;',
		'Target' => 'flex: 0 0 6vw; text-align:right;margin-right:0.5em;',
		'Athlete' => 'flex: 1 1 20vw;white-space:nowrap;overflow:hidden;',
		'CountryDescr' => 'flex: 0 1 20vw;white-space:nowrap;overflow:hidden;',
		'Category' => 'flex: 1 1 10vw;white-space:nowrap;overflow:hidden;',
		'CategoryCode' => 'flex: 0 0 4vw; text-align:center;',
		);
	foreach($ret as $k=>$v) {
		if(!isset($RMain[$k])) $RMain[$k]=$v;
	}
	return $ret;
}

function b() {



}