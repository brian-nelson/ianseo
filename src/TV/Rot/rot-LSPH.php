<?php
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');

function rotLsph($TVsettings, $RULE) {
	global $CFG, $IsCode, $TourId, $SubBlock;
	$CSS=unserialize($TVsettings->TVPSettings);
	getPageDefaults($CSS);
	$Return=array(
		'CSS' => $CSS,
		'html' => '',
		'Block' => 'StartList',
		'BlockCss' => ' width:100%; overflow:hidden; font-size:2em; display:flex; flex-direction:row; justify-content:space-around; align-items:center; box-sizing:border-box;',
		'NextSubBlock' => 1,
		'SubBlocks' => 1);
	$ret=array();

	$Select = "SELECT EnCode as Bib, EnName AS Name, SesName, "
		. " PhPhoto, EnId, "
		. " upper(EnFirstName) AS FirstName, SUBSTRING(AtTargetNo,1,1) AS Session,"
		. " SUBSTRING(AtTargetNo,2) AS TargetNo,"
		. " CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode,"
		. " EnDivision AS DivCode, EnAgeClass as AgeClass, DivDescription, ClDescription, "
		. " EnSubClass as SubClass, EnStatus as Status "
		. "FROM AvailableTarget at "
		. "LEFT JOIN (SELECT EnTournament, QuTargetNo, EnId, EnCode, EnName, EnFirstName, CoCode, CoName, "
			. "EnClass, EnDivision, EnAgeClass, EnSubClass, EnStatus, EnIndClEvent, EnTeamClEvent, EnIndFEvent, EnTeamFEvent "
			. "FROM Qualifications AS q  "
			. "INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($TourId)  . " AND EnAthlete=1 "
			. "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament"
			. ") as Sq ON at.AtTargetNo=Sq.QuTargetNo "
		. "LEFT JOIN Divisions on EnDivision=DivId and DivTournament=$TourId  "
		. "LEFT JOIN Classes on EnClass=ClId and ClTournament=$TourId "
		. "LEFT JOIN Session on SUBSTRING(AtTargetNo,1,1) = SesOrder and EnTournament=SesTournament and SesType='Q' "
		. "LEFT JOIN Photos on EnId=PhEnId "
		. "WHERE"
		. " AtTournament = " . StrSafe_DB($TourId)
		. " AND EnCode IS NOT NULL "
		. ($TVsettings->TVPSession ? " AND SUBSTRING(AtTargetNo,1,1)= " . StrSafe_DB($TVsettings->TVPSession) . " " : "")
		. "ORDER BY AtTargetNo, CoCode, Name, CoName, FirstName ";

	$Rs=safe_r_sql($Select);
	$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
	$ViewTeams=(in_array('TEAM', $Columns) or in_array('ALL', $Columns));
	$ViewFlag=(in_array('FLAG', $Columns) or in_array('ALL', $Columns));
	$ViewCode=(in_array('CODE', $Columns) or in_array('ALL', $Columns));
	$ViewCat=(in_array('DIVCLAS', $Columns) or in_array('ALL', $Columns));
	$ViewCatCode=(in_array('CATCODE', $Columns) or in_array('ALL', $Columns));
	$Title2Rows=(in_array('TIT2ROWS', $Columns) ? '<br/>' : ': ');

// 	include_once('Common/CheckPictures.php');
// 	CheckPictures($IsCode);

	$ret[]='<div class="Title">
				<div class="TitleImg" style="float:left;"><img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$IsCode.'-ToLeft.jpg"></div>
				<div class="TitleImg" style="float:right;"><img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$IsCode.'-ToRight.jpg"></div>
		'.get_text('StartlistSessionPicture','Tournament').'</div>';

	$ret[]='<div id="content" data-direction="up">';
	$RowCounter = 0;
	$OldTarget='';
	$Class='';
	$OldSession='';
	$tab = array();
	while($MyRow=safe_fetch($Rs)) {
		if($OldSession!=$MyRow->Session) {
			$OldSession=$MyRow->Session;
			$ses=GetSessions('Q',true,$MyRow->Session.'_Q', $TourId);
			$Ath4Target=$ses[0]->SesAth4Target;

			$ret[]='<div class="SubTitle">'.($ses[0]->SesName ? $ses[0]->SesName : get_text('Session') . ' ' . $MyRow->Session).'</div>';

			$oldTarget='';
		}
		if($OldTarget!=intval($MyRow->TargetNo)) {

			// creates the row with all items of that target
			if($tab) {
				$Class=($RowCounter++%2 ? 'e' : 'o');
				$tmp= '<div class="TgtBlock Font2'.$Class.' Back2'.$Class.'">';
				foreach($tab as $letter=>$r) {
					$tmp.='<div class="TgtAssign">';
					$tmp.='<div class="Letter">'.$OldTarget.' '.$letter.'</div>';
					if($r) {
						$tmp.='<div class="Picture">'.get_photo_ianseo($r->EnId, '', '', 'class="IdPictureImg"', true).'</div>';
						$tmp.='<div class="Category">'
							.($ViewCatCode ? $r->DivCode . $r->ClassCode : '')
							.($ViewCat ? ($ViewCatCode ? ' ' : ''). $r->DivDescription . ' ' . $r->ClDescription : '')
							.'</div>';
						$tmp.='<div class="Athlete">'.$r->FirstName . ' ' . ($TVsettings->TVPNameComplete==0 ? FirstLetters($r->Name) : $r->Name).'</div>';
						$tmp.='<div class="CountryBlock">'
							. ($ViewCode ? '<div class="CountryCode Rotate Rev2'.$Class.'">'.$r->NationCode.'</div>' : '')
							. ($ViewFlag ? '<div class="FlagDiv">'.get_flag_ianseo($r->NationCode, '', '', $IsCode).'</div>' : '')
							. ($ViewTeams ? '<div class="CountryDescr">' . $r->Nation . '</div>' : '')
							.'</div>';
					} else {
						$tmp.='<div class="Picture">'.get_photo_ianseo(0, '', '', 'class="IdPictureImg"', true).'</div>';
						$tmp.='<div class="Category">&nbsp;</div>';
						$tmp.='<div class="Athlete">&nbsp;</div>';
						$tmp.='<div class="CountryBlock">'
							. ($ViewCode ? '<div class="CountryCode Rotate Rev2'.$Class.'"></div>' : '')
							. ($ViewFlag ? '<div class="FlagDiv"></div>' : '')
							. ($ViewTeams ? '<div class="CountryDescr"></div>' : '')
							.'</div>';
					}
					$tmp.='</div>';
				}
				$tmp.='</div>';
				$ret[]=$tmp;
			}
			$OldTarget=intval($MyRow->TargetNo);
			$tab = array();
			for($n=0; $n<$Ath4Target; $n++) {
				$tab[sprintf('%c', $n+65)]='';
			}
		}

		// recupera se si tratta di A, B, eccetera
		$Num=substr($MyRow->TargetNo,-1);
		$tab[$Num] = $MyRow;

	}

	if($tab) {
		$tmp= '<div class="TgtBlock Font2'.$Class.' Back2'.$Class.'">';
		foreach($tab as $letter=>$r) {
			$tmp.='<div class="TgtAssign">';
			$tmp.='<div class="Letter">'.$OldTarget.' '.$letter.'</div>';
			if($r) {
				$tmp.='<div class="Picture">'.get_photo_ianseo($r->EnId, '', '', 'class="IdPictureImg"', true).'</div>';
				$tmp.='<div class="Category">'
					.($ViewCatCode ? $r->DivCode . $r->ClassCode : '')
					.($ViewCat ? ($ViewCatCode ? ' ' : ''). $r->DivDescription . ' ' . $r->ClDescription : '')
					.'</div>';
				$tmp.='<div class="Athlete">'.$r->FirstName . ' ' . ($TVsettings->TVPNameComplete==0 ? FirstLetters($r->Name) : $r->Name).'</div>';
				$tmp.='<div class="CountryBlock">'
					. ($ViewCode ? '<div class="CountryCode Rotate Rev2'.$Class.'">'.$r->NationCode.'</div>' : '')
					. ($ViewFlag ? '<div class="FlagDiv">'.get_flag_ianseo($r->NationCode, '', '', $IsCode).'</div>' : '')
					. ($ViewTeams ? '<div class="CountryDescr">' . $r->Nation . '</div>' : '')
					.'</div>';
			} else {
				$tmp.='<div class="Picture">'.get_photo_ianseo(0, '', '', 'class="IdPictureImg"', true).'</div>';
				$tmp.='<div class="Category">&nbsp;</div>';
				$tmp.='<div class="Athlete">&nbsp;</div>';
				$tmp.='<div class="CountryBlock">'
					. ($ViewCode ? '<div class="CountryCode Rotate Rev2'.$Class.'"></div>' : '')
					. ($ViewFlag ? '<div class="FlagDiv"></div>' : '')
					. ($ViewTeams ? '<div class="CountryDescr"></div>' : '')
					.'</div>';
			}
			$tmp.='</div>';
		}
		$tmp.='</div>';
		$ret[]=$tmp;
	}

	$ret[]='</div>';

	$Return['html']=implode('', $ret);
	return $Return;
}

function rotLsphSettings($Settings) {
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
		'Athlete' => 'white-space:nowrap;overflow:hidden;',
		'CountryDescr' => 'flex: 0 1 20vw;text-align:left;margin-left:0.1em;white-space:nowrap;overflow:hidden;',
		'Category' => 'font-size:0.8em; overflow:hidden; width:100%; white-space:nowrap;margin: 0 0.2em;',
		'CountryBlock' => 'margin-bottom:0.5rem;width:100%; overflow:hidden; display:flex; flex-direction:row; justify-content:center; align-items:center; box-sizing:border-box;',
		'TgtBlock' => 'margin-top:1em;width:100%; overflow:hidden; font-size:2em; display:flex; flex-direction:row; justify-content:space-around; align-items:flex-start; box-sizing:border-box;',
		'TgtAssign' => 'flex: 1 0 15rem; text-align:center;display:flex; flex-direction:column; justify-content:center; align-items:center;',
		'Letter' => 'width:80%;font-weight:bold;font-size:2em;',
		'IdPicture' => 'width:80%;',
		'IdPictureImg' => 'width:80%;',
	);
	foreach($ret as $k=>$v) {
		if(!isset($RMain[$k])) $RMain[$k]=$v;
	}
	return $ret;
}

