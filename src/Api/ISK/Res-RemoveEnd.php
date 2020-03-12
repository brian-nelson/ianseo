<?php

require_once(dirname(dirname(__FILE__)).'/config.php');
$Error=1;

if(!CheckTourSession()) {
	header('Content-Type: text/xml');
	die('<response error="'.$Error.'"/>');
}
checkACL(AclISKServer, AclReadWrite,false);

// data[23][RW][0][I][010B][0][6]=J
// $popId="data[$r->IskDtMatchNo][$r->IskDtEvent][$r->IskDtTeamInd][$r->IskDtType][$r->IskDtTargetNo][$r->IskDtDistance][$r->IskDtEndNo]=$r->IskDtArrowstring";

if(isset($_REQUEST['data'])) {
	foreach($_REQUEST['data'] as $data) {
		//$tmp=each($data);
		//$MatchNo=$tmp['key'];	$tmp=each($tmp['value']);
		//$Event=$tmp['key']; 	$tmp=each($tmp['value']);
		//$Team=$tmp['key'];		$tmp=each($tmp['value']);
		//$Type=$tmp['key'];		$tmp=each($tmp['value']);
		//$Target=$tmp['key'];	$tmp=each($tmp['value']);
		//$Distance=$tmp['key'];	$tmp=each($tmp['value']);
		//$End=$tmp['key'];
		//$Arrowstring=$tmp['value'];
		$MatchNo=key($data);    $tmp=current($data);
		$Event=key($tmp); 	    $tmp=current($tmp);
		$Team=key($tmp);		$tmp=current($tmp);
		$Type=key($tmp);		$tmp=current($tmp);
		$Target=key($tmp);  	$tmp=current($tmp);
		$Distance=key($tmp);	$tmp=current($tmp);
		$End=key($tmp);
		$Arrowstring=current($tmp);

		if($Event==':::') $Event='';
		if($Target==':::') $Target='';

		safe_w_sql("delete from IskData
			where IskDtTournament={$_SESSION['TourId']}
				and IskDtMatchNo=$MatchNo
				and IskDtEvent='$Event'
				and IskDtTeamInd=$Team
				and IskDtType='$Type'
				and IskDtTargetNo='$Target'
				and IskDtDistance=$Distance
				and IskDtEndNo=$End
				and IskDtArrowstring='$Arrowstring'");
	}
	$Error=0;
}

header('Content-Type: text/xml');
echo '<response error="'.$Error.'">';
echo '</response>';
