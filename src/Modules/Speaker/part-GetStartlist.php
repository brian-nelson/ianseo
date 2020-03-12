<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error' => 1,
		'rows' => array(),
		'col1'=>'',
		'col1head'=>'',
		'col2'=>'',
		'col2head'=>'',
		'col3'=>'',
		'col3head'=>'',
		'col4'=>'',
		'col4head'=>'',
		'col5'=>'',
		'col5head'=>'',
	);

if (empty($_SESSION['TourId'])) {
	JsonOut($JSON);
}
checkACL(AclSpeaker, AclReadOnly, false);

$SQL='';
$Sessions='';
$Fields='QuTarget Target, QuLetter Letter';
$filter=array('Q'=>array(), 'E'=>array());
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

if($filter['Q']) {
	$Sessions.=" inner join Qualifications on EnId=QuId inner join Session on QuSession=SesOrder and SesType='Q' and SesTournament={$_SESSION['TourId']} and SesOrder in (".implode(',', $filter['Q']).") ";
} elseif(!$filter['E']) {
	$Sessions.=" inner join Qualifications on EnId=QuId left join Session on QuSession=SesOrder and SesType='Q' and SesTournament={$_SESSION['TourId']} ";
}

switch($_REQUEST['type']) {
	case 'country':
		$SQL="select distinct CoCode ListKey, CoName ListValue
			from Entries
			inner join Countries on EnCountry=CoId and EnTournament=CoTournament
			inner join Divisions on EnDivision=DivId and DivTournament={$_SESSION['TourId']}
			inner join Classes on EnClass=ClId and ClTournament={$_SESSION['TourId']}
			$Sessions
			where DivAthlete=1 and ClAthlete=1 and EnTournament={$_SESSION['TourId']}
			order by CoCode";
		$JSON['col1head']=get_text('Country');
		$JSON['col2head']=get_text('DivisionClass');
		$JSON['col3head']=get_text('Target');
		$JSON['col4head']=get_text('Athlete');
		$JSON['col5head']=get_text('Session');
		$JSON['col1']='10%';
		$JSON['col2']='5%';
		$JSON['col3']='5%';
		$JSON['col4']='45%';
		$JSON['col5']='35%';
		break;
	case 'target':
		$SQL="select distinct concat(QuSession,QuTarget) ListKey, concat(QuSession, ' - ', QuTarget) ListValue
			from Entries
			inner join Divisions on EnDivision=DivId and DivTournament={$_SESSION['TourId']}
			inner join Classes on EnClass=ClId and ClTournament={$_SESSION['TourId']}
			$Sessions
			where QuSession>0 and DivAthlete=1 and ClAthlete=1 and EnTournament={$_SESSION['TourId']}
			order by QuSession, QuTarget";
		$JSON['col1head']=get_text('Session');
		$JSON['col2head']=get_text('Target');
		$JSON['col3head']=get_text('DivisionClass');
		$JSON['col4head']=get_text('Country');
		$JSON['col5head']=get_text('Athlete');
		$JSON['col1']='35%';
		$JSON['col2']='5%';
		$JSON['col3']='5%';
		$JSON['col4']='10%';
		$JSON['col5']='45%';
		break;
}

if($SQL) {
	$JSON['error']=0;
	$rs=safe_r_sql($SQL);

	while ($myRow=safe_fetch($rs)) {
		$JSON['rows'][]=array(
				'val' => $myRow->ListKey,
				'txt' => $myRow->ListValue,
				'sel' => '0',
		);
	}

}

JsonOut($JSON);