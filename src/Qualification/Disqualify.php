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
		'Error' => true,
		'NewStatus' => 0,
		'Id' => $_REQUEST['Id'],
		'Msg' => get_text('InvalidAction'),
	);

	$Select = "SELECT QuClRank, IndRank from Qualifications inner join Individuals on QuId=IndId where IndTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuId=" . StrSafe_DB($_REQUEST['Id']) . " ";

	$Rs = safe_r_sql($Select);
	$Update="";

	if (!IsBlocked(BIT_BLOCK_QUAL)) {
		$JSON['Error']=false;
		while($Row=safe_fetch($Rs)) {
			if($Row->QuClRank==9999) {
				// reverts from the disqualification
				$Update = "UPDATE Qualifications Inner Join Individuals on QuId=IndId set
						QuClRank=0,
						IndRank=0,
						IndRankFinal=0

					WHERE IndTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuId=" . StrSafe_DB($_REQUEST['Id']);
				$JSON['NewStatus']=1;
			} else {
				$Update = "UPDATE Qualifications Inner Join Individuals on QuId=IndId set ";
// 				for($i=1; $i<9; $i++) $Update.= "QuD{$i}Rank=0, IndD{$i}Rank=0, ";
				$Update.= "IndSO=0,
						QuClRank=9999,
						IndRank=9999,
						IndRankFinal=9999
					WHERE IndTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuId=" . StrSafe_DB($_REQUEST['Id']);
			}
			if (debug)
				print $Update . '<br>';

			safe_w_sql($Update);
			$JSON['Error']=($JSON['Error'] or CalcRank(0)); // recalculate final rank
			$JSON['Error']=($JSON['Error'] or MakeTeams(NULL, NULL));
			$JSON['Error']=($JSON['Error'] or MakeTeamsAbs(NULL,null,null));
		}

		$JSON['Msg']=($JSON['Error'] ? get_text('ErrorIndTeamsRank') : get_text('RecalcIndTeamsRank'));
	}

	JsonOut($JSON);