<?php
/*
													- CheckTargetNo_Par.php -
	Cerifica il targetno in Partecipants.php
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession() || !isset($_REQUEST['d_q_QuSession']) || !isset($_REQUEST['d_q_QuTargetNo'])) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	$Errore=0;

	$TargetNo = '#';


	if (trim($_REQUEST['d_q_QuTargetNo'])!='')
	{
		if (!preg_match('/^[0-9]{1,' . TargetNoPadding . '}[a-z]{1}$/i',$_REQUEST['d_q_QuTargetNo']))
		{
			$Errore=1;
		}
		else
		{
			$TargetNo = str_pad(mb_convert_case($_REQUEST['d_q_QuTargetNo'], MB_CASE_UPPER, "UTF-8"),(TargetNoPadding+1),'0',STR_PAD_LEFT);

			$Select
				= "SELECT * FROM AvailableTarget "
				. "WHERE AtTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "AND AtTargetNo=" . StrSafe_DB($_REQUEST['d_q_QuSession'] . $TargetNo) . " ";
			$Rs=safe_r_sql($Select);
			if (debug)
				print $Select . '<br>';
			if (!$Rs || safe_num_rows($Rs)!=1)
			{
				$TargetNo = $_REQUEST['d_q_QuTargetNo'];
				$Errore=1;
			}
		}
	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<targetno>' . $TargetNo . '</targetno>';
	print '</response>';
?>