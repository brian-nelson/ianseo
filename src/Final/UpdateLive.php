<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Lib/Fun_Final.local.inc.php');
	require_once('Common/Lib/Fun_Modules.php');

	$event = isset($_REQUEST['d_Event']) ? $_REQUEST['d_Event'] : null;
	$TeamEvent = isset($_REQUEST['d_Team']) ? $_REQUEST['d_Team'] : null;
	$match = isset($_REQUEST['d_Match']) ? $_REQUEST['d_Match'] : null;

	$JSON=array('error'=>1, 'isLive' => 0, 'msg'=>'');
	$isJSON=isset($_REQUEST['JSON']);

    checkACL(($TeamEvent ? AclTeams : AclIndividuals), AclReadWrite, false);

	if($match%2!=0)
		$match--;

	$Errore = 0;
	$msg = '';
	$xml = '';

	$isBlocked=($TeamEvent==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM));
	if (is_null($event) || is_null($TeamEvent) || is_null($match) || $isBlocked) {
		$Errore=1;
		$msg = 'Blocked!';
	} else {
		$Rs=setLiveSession($TeamEvent, $event, $match, $_SESSION['TourId']);

		if (safe_num_rows($Rs)==1) {
			$myRow=safe_fetch($Rs);
			$JSON['error']=0;
			$JSON['isLive']=($myRow->Live>0);
			$xml = '<live>' . $myRow->Live . '</live>' ."\n";
			$xml .= '<livemsg>' . get_text(($myRow->Live ? 'LiveOff':'LiveOn')) . '</livemsg>' ."\n";
		} else {
			$Errore = 1;
			$msg=get_text('Error');
		}
	}

	if ($Errore==0) {
		$msg=get_text('CmdOk');
	} else {
		//$msg=get_text('Error');
	}

	runJack("FinLiveUpdate", $_SESSION['TourId'], array("Event"=>$event ,"Team"=>$TeamEvent ,"MatchNo"=>$match ,"TourId"=>$_SESSION['TourId']));

	if($isJSON) {
		$JSON['msg']=$msg;
		JsonOut($JSON);
	}

	header('Content-Type: text/xml');

	print '<response>' ;
		print '<error>' . $Errore . '</error>' ;
		print '<msg>' . $msg . '</msg>' ;
		print $xml;
	print '</response>' ;

?>