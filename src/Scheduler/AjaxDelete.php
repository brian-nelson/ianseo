<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclCompetition, AclReadWrite,false);
CheckTourSession(true);

require_once('./LibScheduler.php');

if(!empty($_REQUEST['Fld'])) {
	$Field=key($_REQUEST['Fld']);

	foreach($_REQUEST['Fld'] as $Date => $Times) {
		foreach($Times as $Time => $Orders) {
			foreach($Orders as $Order => $Arg) {
				if($Arg!='del') out();
				safe_w_sql("delete from Scheduler where SchTournament={$_SESSION['TourId']} and SchDay='$Date' and SchStart='$Time' and SchOrder='$Order'");
			}
		}

		$ret=DistanceInfoData(true);
		out($ret);
	}
}

if(!empty($_REQUEST['WarmDelete'])) {
	list($TeamEvent, $Phase, $Day, $MatchTime, $Time)=explode('|', $_REQUEST['WarmDelete']);
	safe_w_sql("delete from FinWarmup
		where FwTournament={$_SESSION['TourId']}
			and FwTeamEvent='".($TeamEvent=='T' ? 1 : 0)."'
			and FwDay='$Day'
			and FwMatchTime='$MatchTime'
			and FwTime='$Time:00'");

	$ret=DistanceInfoData(true);
	out($ret);
}

out();
