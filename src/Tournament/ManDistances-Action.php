<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error' => 1);

if(!CheckTourSession() or !hasACL(AclCompetition, AclReadWrite) or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

switch($_REQUEST['act']) {
	case 'update':
		if(empty($_REQUEST['d']) or empty($_REQUEST['r']) or empty($_REQUEST['val']) or !($Dist=intval($_REQUEST['d']))) {
			JsonOut($JSON);
		}
		safe_w_sql("update TournamentDistances 
    		inner join Tournament on TdTournament=ToId and TdType=ToType 
			set Td{$Dist}=".StrSafe_DB($_REQUEST['val'])."
			where TdClasses=".StrSafe_DB($_REQUEST['r'])." and TdTournament={$_SESSION['TourId']}");
		$JSON['error']=0;
		break;
	default:
		JsonOut($JSON);
}

JsonOut($JSON);
