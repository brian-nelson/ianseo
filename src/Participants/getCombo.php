<?php

require_once(dirname(__FILE__) . '/cfg.php');

$JSON=array('error' => 1, 'msg' => get_text('WrongData', 'Install'));

$JSON['rows']=array(array('key' => '', 'value' => get_text('Select', 'Tournament')));

$ToId=(empty($_REQUEST['ToId']) ? 0 : intval($_REQUEST['ToId']));
$QuTargetNo=(empty($_REQUEST['QuTargetNo']) ? '' : (preg_match('/^[0-9a-z]+$/sim', $_REQUEST['QuTargetNo']) ? $_REQUEST['QuTargetNo'] : ''));
$EnId=(empty($_REQUEST['EnId']) ? 0 : intval($_REQUEST['EnId']));
$Field=(empty($_REQUEST['field']) ? '' : $_REQUEST['field']);
$q='';

switch($Field) {
	case 'status':
		$JSON['error']=0;
		$JSON['rows'][]=array('key' => 0, 'value' => get_text('CmdOk'));
		foreach (array(1,5,6,7,8,9) as $s) {
			$JSON['rows'][]=array('key' => $s, 'value' => get_text('Status_'.$s));
		}
		break;
	case 'tourcode':
		$q=safe_r_sql("select ToId rowKey, ToCode rowValue from Tournament where ToId in ($TourId)");
		break;
	case 'session':
		$q=safe_r_sql("select SesOrder rowKey, if(SesName!='', SesName, concat('Session ', SesOrder)) rowValue from Session where SesTournament=$ToId and SesType='Q'");
		break;
	case 'sex':
		$q=safe_r_sql("(select 0 rowKey, ".StrSafe_DB(get_text('ShortMale','Tournament'))." rowValue) union (select 1 rowKey, ".StrSafe_DB(get_text('ShortFemale','Tournament'))." rowValue)");
		break;
	case 'wc':
		$q=safe_r_sql("(select 0 rowKey, ".StrSafe_DB(get_text('No'))." rowValue) union (select 1 rowKey, ".StrSafe_DB(get_text('Yes'))." rowValue)");
		break;
	case 'targetface_name':
		if($EnId) {
			$q=safe_r_sql("select distinct TfId rowKey, TfName rowValue
				from Entries
				inner join TargetFaces on EnTournament=TfTournament and if(TfRegExp>'', concat(trim(EnDivision),trim(EnClass)) REGEXP TfRegExp, concat(trim(EnDivision),trim(EnClass)) like TfClasses)
				where EnId=$EnId
				order by TfDefault desc");
			$JSON['error']=0;
			while($r=safe_fetch($q)) {
				$JSON['rows'][]=array('key' => $r->rowKey, 'value' => get_text($r->rowValue, 'Tournament', '', true));
			}
			$q='';
		}
		break;
	case 'division':
	case 'class':
	case 'ageclass':
		$SQL="select ClId rowKey, ClDescription rowValue from Classes where ClTournament=$ToId order by ClViewOrder";
		if($Field=='division') {
			$SQL="select DivId rowKey, DivDescription rowValue from Divisions where DivTournament=$ToId order by DivViewOrder";
		}
		if($EnId) {
			$q=safe_r_sql("select if(EnDob=0, 0, year(ToWhenTo)-year(EnDob)) Age, EnDivision, EnSex, EnAgeClass from Entries inner join Tournament on EnTournament=ToId where EnId=$EnId");
			if($r=safe_fetch($q)) {
				switch($Field) {
					case 'division':
						$SQL="select distinct DivId rowKey, DivDescription rowValue
							from Divisions
							inner join Classes on ClTournament=DivTournament and (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))
							where DivTournament=$ToId
								".($r->Age ? "and $r->Age between ClAgeFrom and ClAgeTo" : "")."
								and ClSex in (-1, $r->EnSex)
							order by DivViewOrder";
						break;
					case 'ageclass':
						$SQL="select ClId rowKey, ClDescription rowValue
							from Classes
							where ClTournament=$ToId
								".($r->Age ? "and $r->Age between ClAgeFrom and ClAgeTo" : "")."
								and ClSex in (-1, $r->EnSex)
							".($r->EnDivision ? "and (ClDivisionsAllowed='' or find_in_set('$r->EnDivision', ClDivisionsAllowed))" : "")."
							order by ClViewOrder";
						break;
					case 'class':
						$SQL="select c1.ClId rowKey, c1.ClDescription rowValue
							from Classes c1
							inner join Classes c2 on find_in_set(c1.ClId, c2.ClValidClass) and c1.ClTournament=c2.ClTournament
							where c1.ClTournament=$ToId
								and c2.ClId='$r->EnAgeClass'
								and c2.ClSex in (-1, $r->EnSex)
							".($r->EnDivision ? "and (c2.ClDivisionsAllowed='' or find_in_set('$r->EnDivision', c2.ClDivisionsAllowed))" : "")."
							order by c1.ClViewOrder";
						break;
				}
			}
		}
		$q=safe_r_sql($SQL);
		break;
	case 'subclass':
		$q=safe_r_sql("select ScId rowKey, ScDescription rowValue from SubClass where ScTournament=$ToId order by ScViewOrder");
		break;
}

if($q) {
	$JSON['error']=0;
	while($r=safe_fetch($q)) {
		$JSON['rows'][]=array('key' => $r->rowKey, 'value' => $r->rowValue);
	}
}

JsonOut($JSON);
