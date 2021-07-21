<?php
/*
													- ManTarget.php -
	Aggiorna il target in FinSchedule
*/

$JSON=array('error'=>1, 'targets'=>array());

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('../LibFinals.php');

if (!CheckTourSession() or !hasACL(AclCompetition, AclReadWrite) or IsBlocked(BIT_BLOCK_TOURDATA) or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

switch($_REQUEST['act']) {
	case 'get':
		// get all the red targets
		if (empty($_REQUEST['event'])) {
			JsonOut($JSON);
		}
		if($JSON['targets']=getRedTargets($_REQUEST['event'], 0)) {
			$JSON['error']=0;
		}
		break;
	case 'set':
		if (empty($_REQUEST['fld']) or !isset($_REQUEST['val']) or !isset($_REQUEST['type'])) {
			JsonOut($JSON);
		}
		if($JSON['targets']=SetTargetDb($_REQUEST['fld'], $_REQUEST['val'], $_REQUEST['type'])) {
			$JSON['error']=0;
		}
		break;
}

JsonOut($JSON);

