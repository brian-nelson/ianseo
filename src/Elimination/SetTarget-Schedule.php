<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
$JSON=array('error'=>1, 'msg' => get_text('CrackError'), 'items' => array());

if(!CheckTourSession()) {
	JsonOut($JSON);
}

checkACL(AclEliminations, AclReadWrite);

// remove old schedule
safe_w_sql("delete from FinSchedule where FSEvent=".StrSafe_DB($_REQUEST['dest'])." and FSTeamEvent=0 and FSTournament={$_SESSION['TourId']}");
$q=safe_r_sql("select ".StrSafe_DB($_REQUEST['dest'])." as FSEvent, FSTeamEvent, FSMatchNo, FSTournament, FSScheduledDate, FSScheduledTime, FSScheduledLen, FSLetter 
	from FinSchedule
	where FSEvent=".StrSafe_DB($_REQUEST['org'])." and FSTeamEvent=0 and FSTournament={$_SESSION['TourId']} and FSScheduledDate>0");
while($r=safe_fetch($q)) {
	$SQL=array();
	foreach($r as $k => $v) {
		$SQL[]=$k."=".StrSafe_DB($v);
	}
	$r->FSScheduledTime=substr($r->FSScheduledTime,0,5);
	$JSON['items'][]=$r;
	safe_w_sql("insert into FinSchedule set ".implode(',', $SQL)." on duplicate key update ".implode(',', $SQL));
}

$JSON['error']=0;
JsonOut($JSON);
