<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error' => 1, 'rows' => array());

if (empty($_SESSION['TourId'])) {
	JsonOut($JSON);
}
checkACL(AclSpeaker, AclReadOnly, false);

$SQL='';
$Sessions='';
$Field='concat(QuTarget, QuLetter)';
$filter=array('Q'=>array(), 'E'=>array());
$Where=array();
if(!empty($_REQUEST['Session'])) {
	foreach($_REQUEST['Session'] as $Ses) {
		switch($Ses[0]) {
			case 'Q':
				$filter['Q'][]=substr($Ses, 1);
				break;
			case 'E':
				break;
		}
	}

}

if(!empty($_REQUEST['detail'])) {
	$Where=implode(',', StrSafe_DB($_REQUEST['detail']));
}

if($filter['Q']) {
	$Sessions.=" inner join Qualifications on EnId=QuId inner join Session on QuSession=SesOrder and SesType='Q' and SesTournament={$_SESSION['TourId']} and SesOrder in (".implode(',', $filter['Q']).") ";
} elseif(!$filter['E']) {
	$Sessions.=" inner join Qualifications on EnId=QuId left join Session on QuSession=SesOrder and SesType='Q' and SesTournament={$_SESSION['TourId']} ";
}

switch($_REQUEST['type']) {
	case 'country':
		$SQL="select
				concat(CoCode, ' - ', CoName) Col1,
				concat(DivId, ClId) Col2,
				$Field Col3,
				concat(ucase(EnFirstname), ' ', EnName) Col4,
				if(SesName!='', SesName, concat('Session ',SesOrder)) Col5
			from Entries
			inner join Countries on EnCountry=CoId and EnTournament=CoTournament
			inner join Divisions on EnDivision=DivId and DivTournament={$_SESSION['TourId']}
			inner join Classes on EnClass=ClId and ClTournament={$_SESSION['TourId']}
			$Sessions
			where DivAthlete=1 and ClAthlete=1 and EnTournament={$_SESSION['TourId']}
			".($Where ? "and CoCode in ($Where)" : '')."
			order by CoCode, EnFirstName, SesOrder ";
		break;
	case 'target':
		$SQL="select
			if(SesName!='', SesName, concat('Session ',SesOrder)) Col1,
			$Field Col2,
			concat(DivId, ClId) Col3,
			concat(CoCode, ' - ', CoName) Col4,
			concat(ucase(EnFirstname), ' ', EnName) Col5
			from Entries
			inner join Countries on EnCountry=CoId and EnTournament=CoTournament
			inner join Divisions on EnDivision=DivId and DivTournament={$_SESSION['TourId']}
			inner join Classes on EnClass=ClId and ClTournament={$_SESSION['TourId']}
			$Sessions
			where QuSession>0 and DivAthlete=1 and ClAthlete=1 and EnTournament={$_SESSION['TourId']}
			".($Where ? "and concat(QuSession,QuTarget) in ($Where)" : '')."
			order by QuSession, QuTargetNo";

		break;
}

if($SQL) {
	$JSON['error']=0;
	$rs=safe_r_sql($SQL);

	$id=1;

	while ($myRow=safe_fetch($rs)) {
		$JSON['rows'][]=array(
				'id' => 'row-'.$id++,
				'col1' => $myRow->Col1,
				'col2' => $myRow->Col2,
				'col3' => $myRow->Col3,
				'col4' => $myRow->Col4,
				'col5' => $myRow->Col5,
		);
	}

}


JsonOut($JSON);