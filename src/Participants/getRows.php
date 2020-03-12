<?php
require_once(dirname(__FILE__) . '/cfg.php');

$Headers='';
$Filter='';
$Columns=array();
$ExtraWhere='';

// columns
$Filter.='<td>&nbsp;</td>';
$Headers.='<td class="Title">&nbsp;</td>';
$Columns[]='edit';

if($ShowTourCode) {
	$f='';
	if(isset($FilterRequest['Tour'])) {
		$f=$FilterRequest['Tour'];
		$ExtraWhere.=' AND ToCode like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
	}
	$Filter.='<td><input type="text" class="filter medium" id="Tour" value="'.$f.'" onchange="getRows()"></td>';
	$Headers.='<td class="Title" id="sortTour" status="'.($Sort=='sortTour' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Tournament','Tournament') . '</td>';
	$Columns[]='tourcode';
}

$f='';
if(isset($FilterRequest['Status'])) {
	$f=$FilterRequest['Status'];
	$ExtraWhere.=' AND ToCode like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
}
$Filter.='<td></td>';
$Headers.='<td class="Title" id="sortStatus" status="'.($Sort=='sortStatus' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Status','Tournament') . '</td>';
$Columns[]='status';

if($ShowPicture) {
	$f='';
	$Filter.='<td></td>';
	$Headers.='<td class="Title" id="sortPicture" status="'.($Sort=='sortPicture' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Photo','Tournament') . '</td>';
	$Columns[]='picture';
}

$f='';
if(isset($FilterRequest['Session'])) {
	$f=$FilterRequest['Session'];
	$ExtraWhere.=' AND QuSession like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
}
$Filter.='<td><input type="text" class="filter short" id="Session" value="'.$f.'" onchange="getRows()"></td>';
$Headers.='<td class="Title" id="sortTarget" status="'.($Sort=='sortTarget' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Session') . '</td>';
$Columns[]='session';

$f='';
if(isset($FilterRequest['Target'])) {
	$f=$FilterRequest['Target'];
	$ExtraWhere.=' AND QuTarget = '.intval($f);
}
$Filter.='<td><input type="text" class="filter short" id="Target" value="'.$f.'" onchange="getRows()"></td>';
$Headers.='<td class="Title" id="sortTarget" status="'.($Sort=='sortTarget' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Target') . '</td>';
$Columns[]='targetno';

$f='';
if(isset($FilterRequest['Bib'])) {
	$f=$FilterRequest['Bib'];
	$ExtraWhere.=' AND EnCode like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
}
$Filter.='<td><input type="text" class="filter medium" id="Bib" value="'.$f.'" onchange="getRows()"></td>';
$Headers.='<td class="Title" id="sortBib" status="'.($Sort=='sortBib' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Code','Tournament') . '</td>';
$Columns[]='code';

if($ShowLocalBib) {
	$f='';
	if(isset($FilterRequest['LocalBib'])) {
		$f=$FilterRequest['LocalBib'];
		$ExtraWhere.=' AND zextra.EdExtra like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
	}
	$Filter.='<td><input type="text" class="filter medium" id="LocalBib" value="'.$f.'" onchange="getRows()"></td>';
	$Headers.='<td class="Title" id="sortLocalBib" status="'.($Sort=='sortLocalBib' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('LocalCode','Tournament') . '</td>';
	$Columns[]='locCode';
}

$f='';
if(isset($FilterRequest['FamName'])) {
	$f=$FilterRequest['FamName'];
	$ExtraWhere.=' AND EnFirstName like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
}
$Filter.='<td><input type="text" class="filter" id="FamName" value="'.$f.'" onchange="getRows()"></td>';
$Headers.='<td class="Title" id="sortFamName" status="'.($Sort=='sortFamName' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('FamilyName','Tournament') . '</td>';
$Columns[]='firstname';

$f='';
if(isset($FilterRequest['GivName'])) {
	$f=$FilterRequest['GivName'];
	$ExtraWhere.=' AND EnName like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
}
$Filter.='<td><input type="text" class="filter" id="GivName" value="'.$f.'" onchange="getRows()"></td>';
$Headers.='<td class="Title" id="sortGivName" status="'.($Sort=='sortGivName' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Name','Tournament') . '</td>';
$Columns[]='name';

if($ShowEmail) {
	$f='';
	if(isset($FilterRequest['Email'])) {
		$f=$FilterRequest['Email'];
		$ExtraWhere.=' AND eextra.EdEmail like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
	}
	$Filter.='<td><input type="text" class="filter" id="Email" value="'.$f.'" onchange="getRows()"></td>';
	$Headers.='<td class="Title" id="sortEmail" status="'.($Sort=='sortEmail' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Email','Tournament') . '</td>';
	$Columns[]='email';
}

if($ShowCaption) {
	$f='';
	if(isset($FilterRequest['Caption'])) {
		$f=$FilterRequest['Caption'];
		$ExtraWhere.=' AND cextra.EdExtra like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
	}
	$Filter.='<td><input type="text" class="filter" id="Caption" value="'.$f.'" onchange="getRows()"></td>';
	$Headers.='<td class="Title" id="sortCaption" status="'.($Sort=='sortCaption' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('AccrCaption','Tournament') . '</td>';
	$Columns[]='caption';
}

$f='';
if(isset($FilterRequest['DOB'])) {
	$f=$FilterRequest['DOB'];
	$ExtraWhere.=' AND EnDob like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
}
$Filter.='<td><input type="text" class="filter" id="DOB" value="'.$f.'" onchange="getRows()"></td>';
$Headers.='<td class="Title" id="sortDob" status="'.($Sort=='sortDob' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('DOB','Tournament') . '</td>';
$Columns[]='dob';

$f='';
if(isset($FilterRequest['Sex'])) {
	$f=$FilterRequest['Sex'];
	$ExtraWhere.=' AND EnSex like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
}
$Filter.='<td><input type="text" class="filter short" id="Sex" value="'.$f.'" onchange="getRows()"></td>';
$Headers.='<td class="Title" id="sortSex" status="'.($Sort=='sortSex' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Sex','Tournament') . '</td>';
$Columns[]='sex';

$f='';
if(isset($FilterRequest['CoCode'])) {
	$f=$FilterRequest['CoCode'];
	$ExtraWhere.=' AND c.CoCode like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
}
$Filter.='<td><input type="text" class="filter short" id="CoCode" value="'.$f.'" onchange="getRows()"></td>';
$Headers.='<td class="Title" id="sortCoCode" status="'.($Sort=='sortCoCode' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Country') . '</td>';
$Columns[]='country_code';

$f='';
if(isset($FilterRequest['CoName'])) {
	$f=$FilterRequest['CoName'];
	$ExtraWhere.=' AND c.CoName like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
}
$Filter.='<td><input type="text" class="filter medium" id="CoName" value="'.$f.'" onchange="getRows()"></td>';
$Headers.='<td class="Title" id="sortCoName" status="'.($Sort=='sortCoName' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('NationShort','Tournament') . '</td>';
$Columns[]='country_name';

if($ShowCountry2) {
	$f='';
	if(isset($FilterRequest['CoCode2'])) {
		$f=$FilterRequest['CoCode2'];
		$ExtraWhere.=' AND c2.CoCode like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
	}
	$Filter.='<td><input type="text" class="filter short" id="CoCode2" value="'.$f.'" onchange="getRows()"></td>';
	$Headers.='<td class="Title" id="sortCoCode2" status="'.($Sort=='sortCoCode2' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Country') . ' 2</td>';
	$Columns[]='country_code2';

	$f='';
	if(isset($FilterRequest['CoName2'])) {
		$f=$FilterRequest['CoName2'];
		$ExtraWhere.=' AND c2.CoName like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
	}
	$Filter.='<td><input type="text" class="filter medium" id="CoName2" value="'.$f.'" onchange="getRows()"></td>';
	$Headers.='<td class="Title" id="sortCoName2" status="'.($Sort=='sortCoName2' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('NationShort','Tournament') . ' 2</td>';
	$Columns[]='country_name2';
}

if($ShowCountry3) {
	$f='';
	if(isset($FilterRequest['CoCode3'])) {
		$f=$FilterRequest['CoCode3'];
		$ExtraWhere.=' AND c3.CoCode like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
	}
	$Filter.='<td><input type="text" class="filter short" id="CoCode3" value="'.$f.'" onchange="getRows()"></td>';
	$Headers.='<td class="Title" id="sortCoCode3" status="'.($Sort=='sortCoCode3' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Country') . ' 3</td>';
	$Columns[]='country_code3';

	$f='';
	if(isset($FilterRequest['CoName3'])) {
		$f=$FilterRequest['CoName3'];
		$ExtraWhere.=' AND c3.CoName like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
	}
	$Filter.='<td><input type="text" class="filter medium" id="CoName3" value="'.$f.'" onchange="getRows()"></td>';
	$Headers.='<td class="Title" id="sortCoName3" status="'.($Sort=='sortCoName3' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('NationShort','Tournament') . ' 3</td>';
	$Columns[]='country_name3';
}

if($ShowDisable) {
	$f='';
	if(isset($FilterRequest['Disable'])) {
		$f=$FilterRequest['Disable'];
		$ExtraWhere.=' AND EnWChair like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
	}
	$Filter.='<td><input type="text" class="filter short" id="Disable" value="'.$f.'" onchange="getRows()"></td>';
	$Headers.='<td class="Title" id="sortDisable" status="'.($Sort=='sortDisable' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('WheelChair','Tournament') . '</td>';
	$Columns[]='wc';
}

$f='';
if(isset($FilterRequest['Div'])) {
	$f=$FilterRequest['Div'];
	$ExtraWhere.=' AND EnDivision like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
}
$Filter.='<td><input type="text" class="filter short" id="Div" value="'.$f.'" onchange="getRows()"></td>';
$Headers.='<td class="Title" id="sortDiv" status="'.($Sort=='sortDiv' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Div') . '</td>';
$Columns[]='division';

if($ShowAgeClass) {
	$f='';
	if(isset($FilterRequest['AgeClass'])) {
		$f=$FilterRequest['AgeClass'];
		$ExtraWhere.=' AND EnAgeClass like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
	}
	$Filter.='<td><input type="text" class="filter short" id="AgeClass" value="'.$f.'" onchange="getRows()"></td>';
	$Headers.='<td class="Title" id="sortAgeClass" status="'.($Sort=='sortAgeClass' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('AgeCl') . '</td>';
	$Columns[]='ageclass';
}

$f='';
if(isset($FilterRequest['Class'])) {
	$f=$FilterRequest['Class'];
	$ExtraWhere.=' AND EnClass like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
}
$Filter.='<td><input type="text" class="filter short" id="Class" value="'.$f.'" onchange="getRows()"></td>';
$Headers.='<td class="Title" id="sortClass" status="'.($Sort=='sortClass' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('Cl') . '</td>';
$Columns[]='class';

if($ShowSubClass) {
	$f='';
	if(isset($FilterRequest['SubClass'])) {
		$f=$FilterRequest['SubClass'];
		$ExtraWhere.=' AND EnSubClass like '.StrSafe_DB('%'.str_replace('_', '\\_', $f).'%');
	}
	$Filter.='<td><input type="text" class="filter short" id="SubClass" value="'.$f.'" onchange="getRows()"></td>';
	$Headers.='<td class="Title" id="sortSubClass" status="'.($Sort=='sortSubClass' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('SubCl','Tournament') . '</td>';
	$Columns[]='subclass';
}

$Filter.='<td>&nbsp;</td>';
$Headers.='<td class="Title" id="sortTgtType" status="'.($Sort=='sortTgtType' ? $NegSort[$SortOrder] : 'asc').'" onclick="getRows(this)">' . get_text('TargetType') . '</td>';
$Columns[]='targetface_name';

$Filter.='<td>&nbsp;</td>';
$Headers.='<td class="Title">&nbsp;</td>';
$Columns[]='delete';

$Rows=GetRows($RowKey, $TourId, $OrderBy, $AllTargets, $ExtraWhere);

$JSON=array('html'=>'');

$JSON['html'] = '<table class="Tabella">';
$JSON['html'].= '<tr>'.$Headers.'</tr>';
$JSON['html'].= '<tr id="RowFilter">'.$Filter.'</tr>';

if (count($Rows)>0) {
	$ref=0;
	$styles=array('', 'warning');
	$oldstyle='';
	foreach ($Rows as $IDrow => $r) {
		if(isset($_REQUEST['diffs']) and $r['code']!=$r['locCode']) continue;
		if($oldstyle!=$r['key']) {
			$ref=1-$ref;
			$oldstyle=$r['key'];
		}

		$JSON['html'] .= '<tr id="ToId='.$r['tourid'].'&QuTargetNo='.$r['qutargetno'].'&EnId='.$r['id'].'" enid="'.$r['id'].'" class="EntryRow '.$styles[$ref].'">';
		foreach($Columns as $col) {
			switch($col) {
				case 'edit':
					$JSON['html'] .= '<td edit="'.get_text('Edit', 'Tournament').'" close="'.get_text('Close').'" abort="'.get_text('CmdCancel').'" save="'.get_text('CmdSave').'" class="edit"><input type="button" value="'.get_text('Edit', 'Tournament').'"></td>';
					break;
				case 'status':
					$JSON['html'] .= '<td field="'.$col.'" val="'.$r['status'].'" class="Center">';
					$img='';
					switch ($r['status']) {
						case '0': $img='status-ok.gif'; $title=get_text('CmdOk'); break;
						case '1': $img='status-canshoot.gif'; $title=get_text('Status_1'); break;
						case '5': $img='status-unknown.gif'; $title=get_text('Status_5'); break;
						case '6': $img='status-gohome.gif'; $title=get_text('Status_6'); break;
						case '7': $img='status-notaccredited.gif'; $title=get_text('Status_7'); break;
						case '8': $img='status-couldshoot.gif'; $title=get_text('Status_8'); break;
						case '9': $img='status-noshoot.gif'; $title=get_text('Status_9'); break;
					}
					if ($r['status']!==null) {
						$JSON['html'] .= '<img src="'.$CFG->ROOT_DIR.'Common/Images/'.$img.'"  title="'.$title.'"/>';
					}
					$JSON['html'] .= '</td>';
					break;
				case 'tourcode':
					$JSON['html'] .= '<td field="'.$col.'" val="'.$r['tourid'].'">'.$r[$col].'</td>';
					break;
				case 'picture':
					$JSON['html'] .= '<td field="'.$col.'" val="'.$r['picture'].'"><img src="'.$CFG->ROOT_DIR.'Common/Images/photo-'.($r[$col] ? 'yes' : 'no').'.gif"></td>';
					break;
				case 'session':
					$JSON['html'] .= '<td field="'.$col.'" val="'.$r['session'].'">'.$r[$col].'</td>';
					break;
				case 'sex':
					$JSON['html'] .= '<td field="'.$col.'" val="'.$r['sex_id'].'">'.$r[$col].'</td>';
					break;
				case 'division':
				case 'class':
				case 'ageclass':
				case 'subclass':
					$JSON['html'] .= '<td field="'.$col.'" val="'.$r[$col].'">'.$r[$col].'</td>';
					break;
				case 'country_code':
					$JSON['html'] .= '<td field="'.$col.'">';
					$JSON['html'] .= $r['country_code'].($r['country_id2']!=0?'&nbsp;<img src="'.$CFG->ROOT_DIR.'Common/Images/info.gif" style="width:12px;height:12px;" title="'.get_text('Country') . ' (2): ' . $r['country_code2'] .' - ' . $r['country_name2'].'" />':'');
					$JSON['html'] .= ($r['country_id3']!=0?'&nbsp;<img src="'.$CFG->ROOT_DIR.'Common/Images/info.gif" style="width:12px;height:12px;" title="'.get_text('Country') . ' (3): ' . $r['country_code3'] .' - ' . $r['country_name3'].'" />':'');
					$JSON['html'] .= '</td>';
					break;
				case 'targetface_name':
					$JSON['html'] .= '<td field="'.$col.'" val="'.$r['targetface'].'">'.get_text($r['targetface_name'], 'Tournament', '', true).'</td>';
					break;
				case 'wc':
					$JSON['html'] .= '<td field="'.$col.'" val="'.$r[$col].'">'.($r[$col] ? 'X' : '') .'</td>';
					break;
				case 'delete':
					$JSON['html'] .= '<td class="Center Delete">'.($r['id']!==null?'<img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" onclick="deleteRow('.$r['id'] . ');"/>':'').'</td>';
					break;
				default:
					$JSON['html'] .= '<td field="'.$col.'">'.$r[$col].'</td>';
			}
		}


		$JSON['html'] .= '</tr>';
	}
}

$JSON['html'] .= '</table>';

JsonOut($JSON);


function GetRows($RowKey, $TourId, $OrderBy=null, $AllTargets=false, $ExtraWhere='') {
	$ret=array();

	$DefTargets=getTargets($TourId);

	if ($OrderBy===null) {
		$OrderBy= "QuSession ASC,QuTargetNo ASC ";
	}

	$Errore = 0;

	$Select="";
	if (!$AllTargets) {
		$Select = "SELECT $RowKey, e.*,IF(EnDob=0,'',EnDob) AS Dob,c.CoCode,c.CoName,c2.CoCode AS CoCode2,c2.CoName AS CoName2, c3.CoCode AS CoCode3, c3.CoName AS CoName3,
			q.QuSession AS `Session`,SUBSTRING(q.QuTargetNo,2) AS TargetNo,ToWhenFrom,TfName, ToCode,ToId,QuTargetNo,
			eextra.EdEmail, zextra.EdExtra locBib, cextra.EdExtra AccrCaption, PhPhoto is not null as HasPhoto
			FROM Entries AS e 
			INNER JOIN Qualifications AS q ON e.EnId=q.QuId 
			INNER JOIN Tournament ON EnTournament=ToId 
			LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament 
			LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament 
			LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament 
			LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId 
			LEFT JOIN ExtraData eextra ON eextra.EdType='E' and eextra.EdId=EnId 
			LEFT JOIN ExtraData zextra ON zextra.EdType='Z' and zextra.EdId=EnId 
			LEFT JOIN ExtraData cextra ON cextra.EdType='C' and cextra.EdId=EnId 
			LEFT JOIN Photos ON PhEnId=EnId 
			WHERE e.EnTournament in ({$TourId}) $ExtraWhere 
			ORDER BY $OrderBy";
	} else {
		$Select = "(SELECT RowKey, EnId, EnIocCode, EnTournament, EnDivision, EnClass, EnSubClass, EnAgeClass, ToCode, ToId,
				EnCountry, EnSubTeam, EnCountry2, EnCountry3, EnCtrlCode, Dob,
				EnCode, EnName, EnFirstName, EnBadgePrinted, EnAthlete, EnSex, EnWChair, EnSitting, EnIndClEvent, EnTeamClEvent, EnIndFEvent, EnTeamFEvent, EnTeamMixEvent, EnDoubleSpace, 
				EnPays, EnStatus, EnTargetFace, EnTimestamp, TfName, CoCode, CoName, CoCode2, CoName2, CoCode3, CoName3, 
				SUBSTRING(AtTargetNo,1,1) AS `Session`, SUBSTRING(AtTargetNo,2) AS TargetNo, AtTargetNo QuTargetNo, sq.ToWhenFrom, EdEmail, EdExtra locBib, AccrCaption, HasPhoto  
			FROM  AvailableTarget 
			INNER JOIN Tournament ON AtTournament=ToId 
			LEFT JOIN (SELECT $RowKey, EnId, EnIocCode, EnTournament, EnDivision, EnClass, EnSubClass, EnAgeClass, eextra.EdEmail, zextra.EdExtra, cextra.EdExtra AccrCaption, 
					EnCountry, EnSubTeam, EnCountry2, EnCountry3, EnCtrlCode, IF(EnDob!='0000-00-00',EnDob,'0000-00-00 00:00:00') AS Dob, 
					EnCode, EnName, EnFirstName, EnBadgePrinted, EnAthlete, EnSex, EnWChair, EnSitting, EnIndClEvent, EnTeamClEvent, EnIndFEvent, EnTeamFEvent, EnTeamMixEvent, EnDoubleSpace,
					EnPays, EnStatus, EnTargetFace, EnTimestamp, TfName, c.CoCode AS CoCode, c.CoName AS CoName, c2.CoCode AS CoCode2, c2.CoName AS CoName2, c3.CoCode AS CoCode3, c3.CoName AS CoName3, 
					q.QuSession AS `Session`, SUBSTRING(q.QuTargetNo,2) AS TargetNo, q.QuTargetNo AS QuTargetNo, ToWhenFrom, PhPhoto is not null as HasPhoto  
				FROM Entries AS e 
				INNER JOIN Qualifications AS q ON e.EnId=q.QuId  
				INNER JOIN Tournament ON EnTournament=ToId  
				LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament  
				LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament  
				LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament  
				LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId  
				LEFT JOIN ExtraData eextra ON eextra.EdType='E' and eextra.EdId=EnId  
				LEFT JOIN ExtraData zextra ON zextra.EdType='Z' and zextra.EdId=EnId  
				LEFT JOIN ExtraData cextra ON cextra.EdType='C' and cextra.EdId=EnId  
				WHERE e.EnTournament in ({$TourId}) $ExtraWhere  
				ORDER BY " . $OrderBy . "  ) AS sq ON AtTournament=EnTournament AND AtTargetNo=QuTargetNo  
			WHERE AtTournament in ({$TourId}))  
			UNION ALL  
			(SELECT $RowKey, EnId, EnIocCode, EnTournament, EnDivision, EnClass, EnSubClass, EnAgeClass, ToCode, ToId, 
				EnCountry, EnSubTeam, EnCountry2, EnCountry3, EnCtrlCode, IF(EnDob!='0000-00-00',EnDob,'0000-00-00 00:00:00') AS Dob, 
				EnCode, EnName, EnFirstName, EnBadgePrinted, EnAthlete, EnSex, EnWChair, EnSitting, EnIndClEvent, EnTeamClEvent, EnIndFEvent, EnTeamFEvent, EnTeamMixEvent, EnDoubleSpace,
				EnPays, EnStatus, EnTargetFace, EnTimestamp, TfName, c.CoCode AS CoCode, c.CoName AS CoName, c2.CoCode AS CoCode2, c2.CoName AS CoName2, c3.CoCode AS CoCode3, c3.CoName AS CoName3, 
				q.QuSession AS `Session`, SUBSTRING(q.QuTargetNo,2) AS TargetNo, QuTargetNo, ToWhenFrom, eextra.EdEmail, zextra.EdExtra locBib, cextra.EdExtra AccrCaption, PhPhoto is not null as HasPhoto  
			FROM  Entries 
			INNER JOIN Qualifications AS q ON EnId=q.QuId  
			INNER JOIN Tournament ON EnTournament=ToId  
			LEFT JOIN Countries AS c ON EnCountry=c.CoId AND EnTournament=c.CoTournament  
			LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId  
			LEFT JOIN Countries AS c2 ON EnCountry2=c2.CoId AND EnTournament=c2.CoTournament  
			LEFT JOIN Countries AS c3 ON EnCountry3=c3.CoId AND EnTournament=c3.CoTournament  
			LEFT JOIN ExtraData eextra ON eextra.EdType='E' and eextra.EdId=EnId  
			LEFT JOIN ExtraData zextra ON zextra.EdType='Z' and zextra.EdId=EnId  
			LEFT JOIN ExtraData cextra ON cextra.EdType='C' and cextra.EdId=EnId  
			WHERE EnTournament in ({$TourId}) AND length(QuTargetNo)<4 $ExtraWhere )  
			ORDER BY " . $OrderBy . " ";
	}

	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0) {
		while ($MyRow=safe_fetch($Rs)) {
			if ($MyRow->EnId) {
				if(empty($DefTargets[$MyRow->EnTournament][$MyRow->EnDivision][$MyRow->EnClass])) {
					// the target is missing for this entry... so sets the EnTargetFace to 0
					safe_w_sql("update Entries set EnTargetFace=0 where EnId=$MyRow->EnId");
					$MyRow->EnTargetFace=0;
				} elseif(empty($DefTargets[$MyRow->EnTournament][$MyRow->EnDivision][$MyRow->EnClass][$MyRow->EnTargetFace])) {
					// the assigned target face doesn't exists so resets to the first one (default)
					reset($DefTargets[$MyRow->EnTournament][$MyRow->EnDivision][$MyRow->EnClass]);
					$TfId = key($DefTargets[$MyRow->EnTournament][$MyRow->EnDivision][$MyRow->EnClass]);
					safe_w_sql("update Entries set EnTargetFace=$TfId where EnId=$MyRow->EnId");
					$MyRow->EnTargetFace=$TfId;
				}
			}

			$ret[]=array(
					'tourcode' => $MyRow->ToCode,
					'picture' => $MyRow->HasPhoto,
					'tourid' => $MyRow->ToId,
					'qutargetno' => $MyRow->QuTargetNo,
					'caption' => $MyRow->AccrCaption,
					'key' => $MyRow->RowKey,
					'id' => $MyRow->EnId,
					'ioccode' => $MyRow->EnIocCode,
					'code' => $MyRow->EnCode,
					'locCode' => $MyRow->locBib,
					'status' => $MyRow->EnStatus,
					'session' => $MyRow->Session!=0 ? $MyRow->Session : '',
					'targetno' => $MyRow->TargetNo,
					'firstname' => stripslashes($MyRow->EnFirstName),
					'name' => stripslashes($MyRow->EnName),
					'email' => stripslashes($MyRow->EdEmail),
					'sex_id' => $MyRow->EnSex,
					'sex' =>  $MyRow->EnId!==null ? $MyRow->EnSex==0 ? get_text('ShortMale','Tournament') : get_text('ShortFemale','Tournament') : '',
					'ctrl_code' => $MyRow->EnCtrlCode,
					'dob' => $MyRow->Dob,
					'country_id' => $MyRow->EnCountry,
					'country_code' => $MyRow->CoCode,
					'country_name' => stripslashes($MyRow->CoName),
					'sub_team' => $MyRow->EnSubTeam,
					'country_id2' => $MyRow->EnCountry2,
					'country_code2' => $MyRow->CoCode2,
					'country_name2' => stripslashes($MyRow->CoName2),
					'country_id3' => $MyRow->EnCountry3,
					'country_code3' => $MyRow->CoCode3,
					'country_name3' => stripslashes($MyRow->CoName3),
					'division' => $MyRow->EnDivision,
					'class' => $MyRow->EnClass,
					'ageclass' => $MyRow->EnAgeClass,
					'subclass' => $MyRow->EnSubClass,
					'targetface' => $MyRow->EnTargetFace,
					'targetface_name' => $MyRow->TfName,
					'indcl'=>$MyRow->EnIndClEvent,
					'teamcl'=>$MyRow->EnTeamClEvent,
					'indfin'=>$MyRow->EnIndFEvent,
					'teamfin'=>$MyRow->EnTeamFEvent,
					'mixteamfin'=>$MyRow->EnTeamMixEvent,
					'wc'=>$MyRow->EnWChair,
					'double'=>$MyRow->EnDoubleSpace,
			);
		}
	}

	return $ret;
}

function getTargets($TourId) {
	$ar=array();

	$MySql="select DivTournament, "
		. " DivId"
		. ", ClId"
		. ", TfId "
		. ", TfName "
		. ", TfDefault "
		. "from"
		. " Divisions"
		. " inner join Classes on DivTournament=ClTournament and DivAthlete=ClAthlete"
		. " inner join TargetFaces Tf on DivTournament=TfTournament and if(TfRegExp>'', concat(trim(DivId),trim(ClId)) REGEXP TfRegExp, concat(trim(DivId),trim(ClId)) like TfClasses) "
		. "WHERE"
		. " DivTournament in ($TourId) "
		. " AND DivAthlete='1' "
		. " AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))"
		. "order by"
		. " DivViewOrder"
		. ", ClViewOrder"
		. ", TfDefault desc"
		. ", TfRegExp>'' desc"
		. ", concat(trim(DivId),trim(ClId)) = TfClasses desc"
		. ", left(TfClasses,1)!='_' and left(TfClasses,1)!='%' desc"
		. ", left(TfClasses,1)='_' desc"
		. ", TfClasses desc"
		. ", TfClasses='%' ";

	$q=safe_r_sql($MySql);
// 	if($ByDiv) {
		while($r=safe_fetch($q)) {
			if(!$r->TfDefault or empty($ar[$r->DivTournament][$r->DivId][$r->ClId])) {
				$ar[$r->DivTournament][$r->DivId][$r->ClId][$r->TfId] = get_text($r->TfName, 'Tournament', '', true);
			}
		}
// 	} else {
// 		$divs=array();
// 		while($r=safe_fetch($q)) {
// 			if(!$r->TfDefault or empty($divs[$r->DivId][$r->ClId])) {
// 				$ar[$r->TfId][$r->DivId][$r->ClId] = $r->TfDefault;
// 				$divs[$r->DivId][$r->ClId]='done';
// 			}
// 		}
// 	}

	return $ar;
}
