<?php
require_once(dirname(dirname(__FILE__)).'/config.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
require_once(dirname(__FILE__).'/Res-ImportCommon.php');

if(!CheckTourSession()) {
	header('Content-Type: text/xml');
	die('<response error="1"/>');
}
checkACL(AclISKServer, AclReadWrite,false);

$Error=false;
$Calls=array();
if(isset($_REQUEST['data'])) {
	foreach($_REQUEST['data'] as $data) {
		$MatchNo=key($data);    $tmp=current($data);
		$Event=key($tmp); 	    $tmp=current($tmp);
		$Team=key($tmp);		$tmp=current($tmp);
		$Type=key($tmp);		$tmp=current($tmp);
		$Target=key($tmp);  	$tmp=current($tmp);
		$Distance=key($tmp);	$tmp=current($tmp);
		$End=key($tmp);
		$Arrowstring=current($tmp);
		//$tmp=each($data);
		//$MatchNo=$tmp['key'];	$tmp=each($tmp['value']);
		//$Event=$tmp['key']; 	$tmp=each($tmp['value']);
		//$Team=$tmp['key'];		$tmp=each($tmp['value']);
		//$Type=$tmp['key'];		$tmp=each($tmp['value']);
		//$Target=$tmp['key'];	$tmp=each($tmp['value']);
		//$Distance=$tmp['key'];	$tmp=each($tmp['value']);
		//$End=$tmp['key'];
		//$Arrowstring=$tmp['value'];

		if($Event==':::') $Event='';
		if($Target==':::') $Target='';

		$SQL='';
		switch($Type) {
			case 'Q' :
			case 'E' :
				$SQL="select DISTINCT CONCAT('$Type',ToNumDist,{$Target[0]}) as keyValue, substr(IskDtTargetNo, -4, 3)+0 as Target
					from IskData
					inner join Tournament on IskDtTournament=ToId
					where IskDtTournament={$_SESSION['TourId']}
						and IskDtMatchNo=$MatchNo
						and IskDtEvent='$Event'
						and IskDtTeamInd=$Team
						and IskDtType='$Type'
						and IskDtTargetNo='$Target'
						and IskDtDistance=$Distance
						and IskDtEndNo=$End
						and IskDtArrowstring='$Arrowstring'
						";
				$q=safe_r_sql($SQL);
				while($r=safe_fetch($q)) {
					$Calls[$r->Target]=array(
						'ses'  => $r->keyValue,
						'dist' => $Distance,
						'end'  => $End,
						'target' => $r->Target,
					);
				}
				break;
			case 'I':
			case 'T':
				$SQL="select DISTINCT CONCAT('$Type', FSScheduledDate, FSScheduledTime) as keyValue, FsMatchNo
					from IskData
					inner join FinSchedule on FsTournament=IskDtTournament and FsMatchNo=IskDtMatchNo and FsEvent=IskDtEvent and FsTeamEvent=IskDtTeamInd
					where IskDtTournament={$_SESSION['TourId']}
						and IskDtMatchNo=$MatchNo
						and IskDtEvent='$Event'
						and IskDtTeamInd=$Team
						and IskDtType='$Type'
						and IskDtTargetNo='$Target'
						and IskDtDistance=$Distance
						and IskDtEndNo=$End
						and IskDtArrowstring='$Arrowstring'
						";
				$q=safe_r_sql($SQL);
				while($r=safe_fetch($q)) {
					if(isset($Calls["$Event-$Team"])) {
						$Calls["$Event-$Team"]['matchno'].= ','.$MatchNo;
					} else {
						$Calls["$Event-$Team"]=array(
							'ses'     => $r->keyValue,
							'dist'    => $Distance,
							'end'     => $End,
							'event'   => $Event,
							'matchno' => $MatchNo,
						);
					}
				}
				break;
		}
	}
}

foreach ($Calls as $Call) {
	unset($_REQUEST['data']);
	foreach($Call as $k=>$v) {
		$_REQUEST[$k]=$v;
	}
	$Error=($Error or DoImportData());
}

header('Content-Type: text/xml');
die('<response error="'.intval($Error).'"/>');

