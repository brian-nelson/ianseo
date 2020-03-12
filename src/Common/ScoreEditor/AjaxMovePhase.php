<?php
	define ('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Fun_Final.local.inc.php');
	require_once('Fun_ChangePhase.inc.php');

	CheckTourSession(true);

	$event=isset($_REQUEST['event']) ? $_REQUEST['event'] : null;
	$team=isset($_REQUEST['team']) ? $_REQUEST['team'] : null;
	$match=isset($_REQUEST['match']) ? $_REQUEST['match'] : null;

	$Errore=0;
	$msg=get_text('CmdOk');

	$ok=false;

	$isBlocked=($team==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM));

	if (is_null($event) || is_null($team) || is_null($match) || $isBlocked )
	{
		$Errore=1;
	}
	else
	{
		if ($team==0)
		{
			$ok=move2NextPhase(null,$event,$match);
		}
		else
		{
			$ok=move2NextPhaseTeam(null,$event,$match);
		}

		if ($ok===false)
			$Errore=1;
	}

	if ($Errore==1)
		$msg=get_text('Error');

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
		print '<error>' . $Errore . '</error>' . "\n";
		print '<msg>' . $msg . '</msg>' . "\n";
	print '</response>' . "\n";

