<?php
/*
													- Went2Home.php -
	Ritira una persona
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Fun_Qualification.local.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');

	if (!CheckTourSession() || !isset($_REQUEST['Id']))	{
		print get_text('CrackError');
		exit;
	}
    checkACL(AclQualification, AclReadWrite, false);

	$JSON=array(
		'error' => 1,
		'Id' => $_REQUEST['Id'],
		'Retired' => 1,
		'NewStatus' => 0,
		'Msg' => get_text('InvalidAction'),
		);

	$Select
		= "SELECT EnStatus, IFNULL(LueStatus,0) as NewStatus "
		. "FROM Entries LEFT JOIN LookUpEntries ON EnCode=LueCode AND EnClass=LueClass "
		. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnId=" . StrSafe_DB($_REQUEST['Id']) . " ";

	$Rs = safe_r_sql($Select);
	$Update="";

	if (!IsBlocked(BIT_BLOCK_QUAL)) {
		if(safe_num_rows($Rs)==1) {
			$Row=safe_fetch($Rs);
			if($Row->EnStatus==6) {
				$Update
					= "UPDATE Entries AS e "
					. "LEFT JOIN LookUpEntries AS l ON e.EnCode=l.LueCode AND e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
					. "SET "
					. "e.EnStatus=IFNULL(l.LueStatus,0) "
					. "WHERE e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnId=" . StrSafe_DB($_REQUEST['Id']) . " ";
				$JSON['NewStatus']=($Row->NewStatus<=1 ? 1 : 0);
				$JSON['Retired']=0;
			} else {
				$Update
					= "UPDATE Entries INNER JOIN Qualifications ON EnId=QuId SET "
					. "EnStatus='6',";
				for ($i=1;$i<=8;++$i)
					$Update
						.="QuD" . $i . "Score='0',"
						. "QuD" . $i . "Gold='0',"
						. "QuD" . $i . "Xnine='0',";
				$Update
					.="QuScore='0',"
					. "QuGold='0',"
					. "QuXnine='0' "
					. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuId=" . StrSafe_DB($_REQUEST['Id']) . " ";
			}
			if (debug)
				print $Update . '<br>';

			safe_w_sql($Update);

			$JSON['error'] = false;
			for ($i=0;$i<=8;++$i) {
				$JSON['error'] = ($JSON['error'] or CalcRank($i));
			} // recalculate distances and final rank
			$JSON['error']=($JSON['error'] or MakeTeams(NULL, NULL));
			$JSON['error']=($JSON['error'] or MakeTeamsAbs(NULL,null,null));

			$JSON['Msg']=$JSON['error'] ? get_text('ErrorIndTeamsRank') : get_text('RecalcIndTeamsRank');
		}
	}

	$JSON['error']=intval($JSON['error']);

	JsonOut($JSON);
