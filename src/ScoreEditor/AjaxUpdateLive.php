<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Lib/Fun_Final.local.inc.php');

	$event = isset($_REQUEST['d_Event']) ? $_REQUEST['d_Event'] : null;
	$TeamEvent = isset($_REQUEST['d_Team']) ? $_REQUEST['d_Team'] : null;
	$match = isset($_REQUEST['d_Match']) ? $_REQUEST['d_Match'] : null;

	if($match%2!=0)
		$match--;

	$Errore = 0;
	$msg = '';
	$xml = '';

	$isBlocked=($TeamEvent==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM));
	if (is_null($event) || is_null($TeamEvent) || is_null($match) || $isBlocked)
	{
		$Errore=1;
		$msg = 'Blocked!';
	}
	else
	{
		$Rs=setLiveSession($TeamEvent, $event, $match, $_SESSION['TourId']);

		if (safe_num_rows($Rs)==1) {
			$myRow=safe_fetch($Rs);
			$xml = '<live>' . $myRow->Live . '</live>' ."\n";
			$xml .= '<livemsg>' . get_text(($myRow->Live ? 'LiveOff':'LiveOn')) . '</livemsg>' ."\n";
		} else {
			$Errore = 1;
			$msg=get_text('Error');
		}
	}

	if ($Errore==0)
	{

		$msg=get_text('CmdOk');
	}
	else
	{
		//$msg=get_text('Error');
	}

	header('Content-Type: text/xml');

	print '<response>' . "\n";
		print '<error>' . $Errore . '</error>' . "\n";
		print '<msg>' . $msg . '</msg>' . "\n";
		print $xml;
	print '</response>' . "\n";

?>