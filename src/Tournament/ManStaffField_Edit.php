<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
$JSON=array('error' => 1, 'msg' => get_text('AllFieldsMandatory','Errors'));

if(!CheckTourSession() or !hasACL(AclCompetition, AclReadOnly)) {
	JsonOut($JSON);
}

if(!empty($_REQUEST['act'])) {
	// needs to do something on the DB
	if(($_REQUEST['act']=='delete' and empty($_REQUEST['ID'])
		or ($_REQUEST['act']!='delete' and (empty($_REQUEST['act']) or
				empty($_REQUEST['Code']) or
				empty($_REQUEST['FamilyName']) or
				empty($_REQUEST['GivenName']) or
				!isset($_REQUEST['Gender']) or
				$_REQUEST['Gender']==='' or
				empty($_REQUEST['CountryCode']) or
				empty($_REQUEST['Type']) or
				empty($_REQUEST['ID']))))) {

		JsonOut($JSON);
	}
} else {
	$_REQUEST['act']='list';
}

$CanEdit=hasACL(AclCompetition, AclReadWrite);

$JSON['error']=0;

if($CanEdit) {

	switch($_REQUEST['act']) {
		case 'new':
		case 'edit':
			// check the country Id
			$q=safe_r_sql("select CoId, CoCode, CoName from Countries where CoTournament={$_SESSION['TourId']} and CoCode=".StrSafe_DB($_REQUEST['CountryCode']));
			if(!safe_num_rows($q) and ($_REQUEST['act']=='new' || $_REQUEST['act']=='edit')) {
				// add the new country
				safe_w_SQL("insert into Countries set 
				CoTournament={$_SESSION['TourId']},
				CoCode=".StrSafe_DB($_REQUEST['CountryCode']).",
				CoName=".StrSafe_DB($_REQUEST['CountryName']).",
				CoNameComplete=".StrSafe_DB($_REQUEST['CountryName']));
				$q=safe_r_sql("select CoId, CoCode, CoName from Countries where CoTournament={$_SESSION['TourId']} and CoCode=".StrSafe_DB($_REQUEST['CountryCode']));
			}
			$CoId=0;
			if($r=safe_fetch($q)) {
				$CoId=$r->CoId;
			}

			$SQL=array(
				"TiTournament={$_SESSION['TourId']}",
				"TiType=".intval($_REQUEST['Type']),
				"TiCode=".StrSafe_DB($_REQUEST['Code']),
				"TiName=".StrSafe_DB($_REQUEST['FamilyName']),
				"TiGivenName=".StrSafe_DB($_REQUEST['GivenName']),
				"TiCountry=$CoId",
				"TiGender=".intval($_REQUEST['Gender']),
			);

			if($_REQUEST['ID']=='new') {
				safe_w_sql("insert ignore into TournamentInvolved set ".implode(',', $SQL));
			} else {
				safe_w_sql("update TournamentInvolved set ".implode(',', $SQL)." where TiId=".intval($_REQUEST['ID']));
			}
			break;
		case 'delete':
			safe_w_sql("delete from TournamentInvolved where TiTournament={$_SESSION['TourId']} and TiId=".intval($_REQUEST['ID']));
			break;
	}
}

$Types=array();
$TypeOptions = '<option value="0">---</option>';
$RsSel = safe_r_sql("SELECT ItId, ItDescription, if(ItJudge>0,1,0) + if(ItDos>0,2,0) + if(ItJury>0,3,0) + if(ItOc>0,4,0) as TiGroup FROM InvolvedType ORDER BY ItJudge=0, ItJudge, ItDoS=0, ItDoS, ItJury=0, ItJury, ItOc");
$OldType=0;
while ($RowSel = safe_fetch($RsSel)) {
	if($OldType and $OldType!=$RowSel->TiGroup) {
		$TypeOptions .= '<option value="0" disabled>---</option>';
	}
	$TypeOptions.='<option value="'.$RowSel->ItId.'">'.get_text($RowSel->ItDescription,'Tournament').'</option>';
	$Types[$RowSel->ItId]=get_text($RowSel->ItDescription,'Tournament');
	$OldType=$RowSel->TiGroup;
}

$Genders ='<option value="">---</option>';
$Genders.='<option value="0">'.get_text('ShortMale', 'Tournament').'</option>';
$Genders.='<option value="1">'.get_text('ShortFemale', 'Tournament').'</option>';

$JSON['table']='';
$q=safe_r_sql("SELECT *
    FROM TournamentInvolved  
    LEFT JOIN InvolvedType ON TiType=ItId
    left join Countries on CoId=TiCountry and CoTournament=TiTournament
    WHERE TiTournament={$_SESSION['TourId']}
    ORDER BY ItId IS NOT NULL, ItJudge=0, ItJudge, ItDoS=0, ItDoS, ItJury=0, ItJury, ItOc, TiName, TiGivenName");

if($CanEdit) {
	while($r=safe_fetch($q)) {
		$JSON['table'].='<tr ref="'.$r->TiId.'" class="rowHover">'.
				'<td class="Center"><input type="text" style="width: 95%" maxlength="9" name="Code" value="' . $r->TiCode . '" onchange="editFieldStaff(this)"></td>'.
				'<td class="Center"><input type="text" style="width: 97%" maxlength="64" name="FamilyName" value="' . $r->TiName . '" onchange="editFieldStaff(this)"></td>'.
				'<td class="Center"><input type="text" style="width: 97%" maxlength="64"name="GivenName" value="' . $r->TiGivenName . '" onchange="editFieldStaff(this)"></td>'.
				'<td class="Center"><select name="Gender" style="width: 95%" onchange="editFieldStaff(this)">'.str_replace('value="'.$r->TiGender.'"','value="'.$r->TiGender.'" selected="selected"', $Genders).'</select></td>'.
				'<td class="Center"><input type="text" style="width: 95%" maxlength="10" name="CountryCode" value="' . $r->CoCode . '" ' . (empty($r->CoName) ? '' : 'onchange="editFieldStaff(this)"') . '></td>'.
				'<td class="Center"><input type="text" style="width: 95%" maxlength="20" name="CountryName" value="' . $r->CoName . '" ' . (empty($r->CoName) ? 'onchange="editFieldStaff(this)"' : 'readonly="readonly"') . '></td>'.
				'<td class="Center"><select name="Type" style="width: 95%" onchange="editFieldStaff(this)">'.str_replace('value="'.$r->TiType.'"','value="'.$r->TiType.'" selected="selected"', $TypeOptions).'</select></td>'.
				'<td class="Center"><input type="button" value="' . get_text('CmdDelete','Tournament') . '" onClick="deleteFieldStaff(this)"></td>'.
			'</tr>';
	}
} else {
	while($r=safe_fetch($q)) {
		$JSON['table'].='<tr>'.
				'<td class="Center">' . $r->TiCode . '</td>'.
				'<td class="Center">' . $r->TiName . '</td>'.
				'<td class="Center">' . $r->TiGivenName . '</td>'.
				'<td class="Center">'.($r->TiGender ? get_text('ShortFemale', 'Tournament') : get_text('ShortMale', 'Tournament')).'</td>'.
				'<td class="Center">' . $r->CoCode . '</td>'.
				'<td class="Center">' . $r->CoName . '</td>'.
				'<td class="Center">'.(isset($Types[$r->TiType]) ? $Types[$r->TiType] : '').'</td>'.
				'<td class="Center"></td>'.
			'</tr>';
	}
}

JsonOut($JSON);
