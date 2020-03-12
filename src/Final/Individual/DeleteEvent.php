<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
checkACL(AclCompetition, AclReadWrite, false);

$JSON=array('error' => 1, 'events' => array());

if (!CheckTourSession() || !isset($_REQUEST['EvCode'])) {
	JsonOut($JSON);
}

if (!IsBlocked(BIT_BLOCK_TOURDATA)) {

	$JSON['events']=deleteEvent($_REQUEST['EvCode'], 0);

	if ($JSON['events']) {
		set_qual_session_flags();
	}
	$JSON['error'] = 0;
}

JsonOut($JSON);

