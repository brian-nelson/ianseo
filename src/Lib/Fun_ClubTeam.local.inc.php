<?php
define('EndPos',16);
define('MaxRound',3);

require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/Fun_Phases.inc.php');

function makeComboEvent($OnlyPrimary=false)
{
	$Query
		= "SELECT EvCode,EvTournament,	EvEventName "
		. "FROM Events "
		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' "
		.($OnlyPrimary ? ' AND EvFinalFirstPhase>0 ' : '')
		. "ORDER BY EvProgr ASC ";
	$Rs=safe_r_sql($Query);

	$opts='';
	//print $Query;exit;
	if ($Rs && safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			$opts.='<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true) . '</option>' . "\n";
		}
	}

	$comboEvent
		= '<select name="EventCode">' . "\n"
			. $opts
		. '</select>' . "\n";

	return $comboEvent;
}

function makeComboRound()
{
	$comboRound= '<select name="Round">' . "\n";
		for ($i=1;$i<=MaxRound;++$i)
		{
			$comboRound.='<option value="' . $i .'">' . get_text('Round#','Tournament',$i) . '</option>' . "\n";
		}
	$comboRound.='</select>' . "\n";

	return $comboRound;
}

// function makeComboPrimary()
// {
// 	$comboPrimary
// 		= '<select name="Primary">' . "\n"
// 			. '<option value="1">' . get_text('Primary','Tournament') . '</option>' . "\n"
// 			. '<option value="0">' . get_text('Secondary','Tournament') . '</option>' . "\n"
// 		. '</select>' . "\n";

// 	return $comboPrimary;
// }

function roundPager($round,$qs)
{
	$prev='<a class="Link" href="' . $_SERVER['PHP_SELF'] . '?Round=' . ($round-1) . '&' . $qs . '">&lt;&lt;</a>';
	$next='<a class="Link" href="' . $_SERVER['PHP_SELF'] . '?Round=' . ($round+1) . '&' . $qs . '">&gt;&gt;</a>';

	if ($round==MaxRound)
	{
		$next='';
	}
	elseif ($round==1)
	{
		$prev='';
	}

	return $prev . '&nbsp;&nbsp;' . get_text('Round','Tournament') . '&nbsp;&nbsp;' . $next;
}

function getMatchesPhase1($event, $round, $primary='1', $phase=1, $otherWhere="", $limit="", $TourId=0) {
	//
	$CtgPhase=($primary ? 'CTGPhase' : 'CTGPhase+1');
	if(empty($TourId)) $TourId=$_SESSION['TourId'];
	$Query = "SELECT "
			. "IFNULL(TeamScore1.CTSTarget,'') AS TargetNo1,"
			. "IFNULL(TeamScore2.CTSTarget,'') AS TargetNo2,"
			. "TeamScore1.CTSSetPoints AS SetPoints1,"
			. "TeamScore2.CTSSetPoints AS SetPoints2,"
			. "TeamScore1.CTSSetEnds AS SetEnds1,"
			. "TeamScore2.CTSSetEnds AS SetEnds2,"
			. "if(TeamScore1.CTSDateTime=0, '', TeamScore1.CTSDateTime) AS DateTime1,"
			. "if(TeamScore2.CTSDateTime=0, '', TeamScore2.CTSDateTime) AS DateTime2,"
			. "Team1.CTQualRank AS QualRank1,"
			. "Team2.CTQualRank AS QualRank2,"
			. "EvFinArrows, EvFinEnds, EvFinSO, "
			. "CTG.CTGMatchNo1 AS Match1, CTG.CTGMatchNo2 AS Match2,CTG.CTGGroup AS `Group`,CTG.CTGRound AS Round, Ev.EvCode AS EventCode, Ev.EvEventName AS EventName, "
			. "IFNULL(Team1.CTTeam,'') AS TeamCode1,IFNULL(Team1.CTSubTeam,'') AS SubTeamCode1,"
			. "IFNULL(Country1.CoCode,'') AS CountryCode1,IFNULL(Country1.CoName,'') AS CountryName1, "
			. "IFNULL(TeamScore1.CTSScore,'0') AS Score1, IFNULL(TeamScore1.CTSTie,'0') AS Tie1, IFNULL(TeamScore1.CTSTiebreak,'') AS Tiebreak1, "
			. "IFNULL(TeamScore1.CTSArrowString,'0') AS ArrowString1, IFNULL(TeamScore1.CTSPoints,'0') AS Point1,"

			. "IFNULL(Team2.CTTeam,'') AS TeamCode2,IFNULL(Team2.CTSubTeam,'') AS SubTeamCode2,"
			. "IFNULL(Country2.CoCode,'') AS CountryCode2,IFNULL(Country2.CoName,'') AS CountryName2, "
			. "IFNULL(TeamScore2.CTSScore,'0') AS Score2 , IFNULL(TeamScore2.CTSTie,'0') AS Tie2, IFNULL(TeamScore2.CTSTiebreak,'') AS Tiebreak2, "
			. "IFNULL(TeamScore2.CTSArrowString,'0') AS ArrowString2, IFNULL(TeamScore2.CTSPoints,'0') AS Point2, "
			. "TeamScore1.CTSPrimary AS `Primary` "
			//. "TeamScore1.*,TeamScore2.*,CTG.*,Country1.*,Country2.*,Ev.* "

		. "FROM "
			. "ClubTeamGrid AS CTG "

			. "LEFT JOIN "
				. "ClubTeam AS Team1 "
			. "ON CTG.$CtgPhase=Team1.CTPhase AND CTG.CTGMatchNo1=Team1.CTMatchNo AND Team1.CTTournament=$TourId AND Team1.CTEventCode=" . StrSafe_DB($event) . " AND Team1.CTPrimary=$primary "

			. "LEFT JOIN "
				. "Countries AS Country1 "
			. "ON Team1.CTTeam=Country1.CoId AND Team1.CTTournament=Country1.CoTournament AND Team1.CTTournament=$TourId "

			. "LEFT JOIN "
				. "ClubTeamScore AS TeamScore1 "
			. "ON CTG.CTGMatchNo1=TeamScore1.CTSMatchNo AND CTG.CTGRound=TeamScore1.CTSRound AND TeamScore1.CTSEventCode=Team1.CTeventCode AND  TeamScore1.CTSPhase=CTG.$CtgPhase AND Team1.CTTournament=TeamScore1.CTSTournament AND Team1.CTTournament=$TourId AND Team1.CTPrimary=TeamScore1.CTSPrimary AND Team1.CTPrimary=" . StrSafe_DB($primary) . " "

			. "LEFT JOIN "
				. "ClubTeam AS Team2 "
			. "ON CTG.$CtgPhase=Team2.CTPhase AND  CTG.CTGMatchNo2=Team2.CTMatchNo AND Team2.CTTournament=$TourId AND Team2.CTEventCode=" . StrSafe_DB($event) . " AND Team2.CTPrimary=" . StrSafe_DB($primary) . " "

			. "LEFT JOIN "
				. "Countries AS Country2 "
			. "ON Team2.CTTeam=Country2.CoId AND Team2.CTTournament=Country2.CoTournament AND Team2.CTTournament=$TourId "

			. "LEFT JOIN "
				. "ClubTeamScore AS TeamScore2 "
			. "ON CTG.CTGMatchNo2=TeamScore2.CTSMatchNo AND CTG.CTGRound=TeamScore2.CTSRound AND TeamScore2.CTSEventCode=Team2.CTeventCode AND TeamScore2.CTSPhase=CTG.$CtgPhase AND Team2.CTTournament=TeamScore2.CTSTournament AND Team2.CTTournament=$TourId AND Team2.CTPrimary=TeamScore2.CTSPrimary AND Team2.CTPrimary=" . StrSafe_DB($primary) . " "

			. "LEFT JOIN "
				. "Events AS Ev "
			. "ON Team1.CTEventCode=Ev.EvCode AND Team1.CTTournament=Ev.EvTournament AND Ev.EvTeamEvent=1 "


		. "WHERE "
			. "CTG.$CtgPhase=" . StrSafe_DB($phase) . " and EvCode is not null ";
	if($round!= 0 )
		$Query .= "AND CTGRound=" . StrSafe_DB($round) . " ";
	$Query .= $otherWhere . " ";
	$Query .= "ORDER BY CTGGroup ASC, CTGRound ASC, CTGMatchNo1 ASC ";
	if ($limit!="")
		$Query.="LIMIT " . $limit;

	$Rs=safe_r_sql($Query);

	return $Rs;
}

function sql_getMatchesPhase1($where="",$limit="") {
// 	$Query = "SELECT "
// 		//	. "Team1.CTPhase,TeamScore1.CTSPhase, "
// 			. "IFNULL(TeamScore1.CTSTarget,'') AS TargetNo1,"
// 			. "IFNULL(TeamScore2.CTSTarget,'') AS TargetNo2,"
// 			. "TeamScore1.CTSSetPoints AS SetPoints1,"
// 			. "TeamScore2.CTSSetPoints AS SetPoints2,"
// 			. "TeamScore1.CTSSetEnds AS SetEnds1,"
// 			. "TeamScore2.CTSSetEnds AS SetEnds2,"
// 			. "EvFinArrows, EvFinEnds, EvFinSO, "
// 			. "CTG.CTGMatchNo1 AS Match1,"
// 			. "CTG.CTGMatchNo2 AS Match2,"
// 			. "CTG.CTGGroup AS `Group`,CTG.CTGRound AS Round, Ev.EvCode AS EventCode, Ev.EvEventName AS EventName, "

// 			. "IFNULL(Team1.CTTeam,'') AS TeamCode1,IFNULL(Team1.CTSubTeam,'') AS SubTeamCode1,IFNULL(Country1.CoCode,'') AS CountryCode1,IFNULL(Country1.CoName,'') AS CountryName1, "
// 			. "IFNULL(TeamScore1.CTSScore,'0') AS Score1, IFNULL(TeamScore1.CTSTie,'0') AS Tie1, IFNULL(TeamScore1.CTSTiebreak,'') AS Tiebreak1, "
// 			. "IFNULL(TeamScore1.CTSArrowString,'0') AS ArrowString1, IFNULL(TeamScore1.CTSPoints,'0') AS Point1,"

// 			. "IFNULL(Team2.CTTeam,'') AS TeamCode2,IFNULL(Team2.CTSubTeam,'') AS SubTeamCode2,IFNULL(Country2.CoCode,'') AS CountryCode2,IFNULL(Country2.CoName,'') AS CountryName2, "
// 			. "IFNULL(TeamScore2.CTSScore,'0') AS Score2 , IFNULL(TeamScore2.CTSTie,'0') AS Tie2, IFNULL(TeamScore2.CTSTiebreak,'') AS Tiebreak2, "
// 			. "IFNULL(TeamScore2.CTSArrowString,'0') AS ArrowString2, IFNULL(TeamScore2.CTSPoints,'0') AS Point2, "
// 			. "TeamScore1.CTSPrimary AS `Primary` "

// 		. "FROM "
// 			. "ClubTeamGrid AS CTG "

// 			. "INNER JOIN "
// 				. "ClubTeam AS Team1 "
// 			. "ON Team1.CTPhase=CTGPhase+if(EvFinalFirstPhase>0, 0, 1) AND CTG.CTGMatchNo1=Team1.CTMatchNo AND Team1.CTTournament=" . StrSafe_DB($_SESSION['TourId']) .  " "

// 			. "INNER JOIN "
// 				. "ClubTeamScore AS TeamScore1 "
// 			. "ON CTG.CTGMatchNo1=TeamScore1.CTSMatchNo AND CTG.CTGRound=TeamScore1.CTSRound AND TeamScore1.CTSEventCode=Team1.CTeventCode AND  TeamScore1.CTSPhase=CTG.CTGPhase AND Team1.CTTournament=TeamScore1.CTSTournament AND Team1.CTTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND Team1.CTPrimary=TeamScore1.CTSPrimary "

// 			. "INNER JOIN "
// 				. "ClubTeam AS Team2 "
// 			. "ON Team2.CTPhase=CTGPhase+if(EvFinalFirstPhase>0, 0, 1) AND  CTG.CTGMatchNo2=Team2.CTMatchNo AND Team2.CTTournament=" . StrSafe_DB($_SESSION['TourId']) .  " AND Team2.CTPrimary=Team1.CTPrimary "

// 			. "INNER JOIN "
// 				. "ClubTeamScore AS TeamScore2 "
// 			. "ON CTG.CTGMatchNo2=TeamScore2.CTSMatchNo AND CTG.CTGRound=TeamScore2.CTSRound AND TeamScore2.CTSEventCode=Team2.CTeventCode AND TeamScore2.CTSPhase=CTG.CTGPhase AND Team2.CTTournament=TeamScore2.CTSTournament AND Team2.CTTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND Team2.CTPrimary=TeamScore2.CTSPrimary "

// 			. "INNER JOIN "
// 				. "Events AS Ev "
// 			. "ON Team1.CTEventCode=Ev.EvCode AND Team1.CTTournament=Ev.EvTournament AND Ev.EvTeamEvent=1 "

// 			. "LEFT JOIN "
// 				. "Countries AS Country1 "
// 			. "ON Team1.CTTeam=Country1.CoId AND Team1.CTTournament=Country1.CoTournament AND Team1.CTTournament=" . StrSafe_DB($_SESSION['TourId']) . " "

// 			. "LEFT JOIN "
// 				. "Countries AS Country2 "
// 			. "ON Team2.CTTeam=Country2.CoId AND Team2.CTTournament=Country2.CoTournament AND Team2.CTTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";

	$Query="Select CTSTarget as TargetNo, CTSScore Score, CoName CountryName, CoCode CountryCode, CTSubTeam SubTeamCode
		from ClubTeamScore
		inner join ClubTeam
			on CTTournament={$_SESSION['TourId']}
			and CTMatchNo=CTSMatchNo
			AND CTSEventCode=CTeventCode
			AND CTSPhase=CTPhase
			AND CTPrimary=CTSPrimary
		inner join Countries on CoId=CTTeam
		where CTSTournament={$_SESSION['TourId']} ";
	if ($where) {
		$Query.=" AND " . $where ;
	}

	$Query .= " ORDER BY  CTSTarget";

	if ($limit) {
		$Query.=" LIMIT " . $limit;
	}

	$Rs=safe_r_sql($Query);

	return $Rs;
}

//TODO se si testasse bene potrebbe sostituire il passaggio di fase in UpdateScore2.php
function WinnerFrom2to3($myRow, $execute=true) {
	$ret=array();
	$AssignPositions=true;

	/*
	 * $myRow ha due matchno.
	 * $match4winner sarà il matchno più basso e ci andrà il vincente;
	 * $match4loser sarà il matchno più alto e ci andrà il perdente
	 */
	$match4winner=0;
	$match4loser=0;

	if ($myRow->Match1<$myRow->Match2)
	{
		$match4winner=$myRow->Match1;
		$match4loser=$myRow->Match2;
	}
	else
	{
		$match4winner=$myRow->Match2;
		$match4loser=$myRow->Match1;
	}

	$ret[$match4winner]='';
	$ret[$match4loser]='';

	$winner
		= "UPDATE "
			. "ClubTeam "
		. "SET "
			. "CTTeam=#team#,"
			. "CTSubTeam=#subteam# "
		. "WHERE "
			. "CTTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CTPhase=3 AND CTMatchNo=" . $match4winner . " "
			. "AND CTEventCode=" . StrSafe_DB($myRow->EventCode)  . " AND CTPrimary=" . StrSafe_DB($myRow->Primary) . " AND "
			. "CTTeam=0 AND CTSubTeam=0 ";

	$loser
		= "UPDATE "
			. "ClubTeam "
		. "SET "
			. "CTTeam=#team#,"
			. "CTSubTeam=#subteam# "
		. "WHERE "
			. "CTTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CTPhase=3 AND CTMatchNo=" . $match4loser .  " "
			. "AND CTEventCode=" . StrSafe_DB($myRow->EventCode)  . " AND CTPrimary=" . StrSafe_DB($myRow->Primary) . " AND "
			. "CTTeam=0 AND CTSubTeam=0 ";

	$insert=true;

	$Teams=array(
		'winner' => array(0, 0),
		'loser'  => array(0, 0),
		);
	if ($myRow->Score1>$myRow->Score2) {
		// vince 1
		$Teams['winner'][0]=$myRow->TeamCode1;
		$Teams['winner'][1]=$myRow->SubTeamCode1;
		$Teams['loser'][0]=$myRow->TeamCode2;
		$Teams['loser'][1]=$myRow->SubTeamCode2;
		$ret[$myRow->Match1]='W';
		$ret[$myRow->Match2]='L';
	} elseif ($myRow->Score1<$myRow->Score2) {
		// vince 2
		$Teams['winner'][0]=$myRow->TeamCode2;
		$Teams['winner'][1]=$myRow->SubTeamCode2;
		$Teams['loser'][0]=$myRow->TeamCode1;
		$Teams['loser'][1]=$myRow->SubTeamCode1;
		$ret[$myRow->Match1]='L';
		$ret[$myRow->Match2]='W';
	} elseif ($myRow->Tie1 > $myRow->Tie2) {
		// Pareggio, vince 1
		$Teams['winner'][0]=$myRow->TeamCode1;
		$Teams['winner'][1]=$myRow->SubTeamCode1;
		$Teams['loser'][0]=$myRow->TeamCode2;
		$Teams['loser'][1]=$myRow->SubTeamCode2;
		$ret[$myRow->Match1]='W';
		$ret[$myRow->Match2]='L';
	} elseif ($myRow->Tie1<$myRow->Tie2) {
		// Pareggio, vince 2
		$Teams['winner'][0]=$myRow->TeamCode2;
		$Teams['winner'][1]=$myRow->SubTeamCode2;
		$Teams['loser'][0]=$myRow->TeamCode1;
		$Teams['loser'][1]=$myRow->SubTeamCode1;
		$ret[$myRow->Match1]='L';
		$ret[$myRow->Match2]='W';
	} else {
		// pareggio non ancora gestito
		$ret[$myRow->Match1]='';
		$ret[$myRow->Match2]='';
		$AssignPositions=false;
	}

	if($execute) {
		// azzero le righe di clubteam
		$Query = "UPDATE ClubTeam "
			. "SET "
			. "CTTeam=0,CTSubTeam=0,CTBonus=0,CTRank=0,CTTiebreak='' "
			. "WHERE "
			. "CTTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CTPhase=3 "
			. "AND CTMatchNo IN (" . $myRow->Match1 . "," . $myRow->Match2 . ") "
			. "AND CTEventCode=" . StrSafe_DB($myRow->EventCode) . " AND CTPrimary=" . StrSafe_DB($myRow->Primary) . " ";
		safe_w_sql($Query);

		// Poi resetto gli score
		$Query = "UPDATE "
			. "ClubTeamScore "
			. "SET "
			. "CTSScore=0,CTSTie=0,CTSArrowString='',CTSArrowPosition='',CTSTiebreak='',CTSTiePosition='',CTSPoints=0 "
			. "WHERE "
			. "CTSTournament=" . StrSafe_DB($_SESSION['TourId']) .  " AND CTSPhase=3 AND CTSRound=1 "
			. "AND CTSMatchNo IN (" . $myRow->Match1 . "," . $myRow->Match2 . ") "
			. "AND CTSEventCode=" . StrSafe_DB($myRow->EventCode) . " AND CTSPrimary=" . StrSafe_DB($myRow->Primary) . " ";
		safe_w_sql($Query);

		if($myRow->Match1<5) {
			// Updates the semifinals
			$FromFinToSemi=array(1=>4, 2=>7, 3=>6, 4=>5);
			safe_w_sql("update TeamFinals set
					TfTeam={$myRow->TeamCode1},
					TfSubTeam={$myRow->SubTeamCode1},
					TfScore=$myRow->Score1,
					TfSetPoints='$myRow->SetEnds1',
					TfSetScore='$myRow->SetPoints1',
					TfArrowstring='$myRow->ArrowString1',
					TfDateTime='".date('Y-m-d H:i:s')."'
				where TfEvent='$myRow->EventCode'
					AND TfMatchNo=".($FromFinToSemi[$myRow->Match1])."
					AND TfTournament={$_SESSION['TourId']}
					");
			safe_w_sql("update TeamFinals set
					TfTeam={$myRow->TeamCode2},
					TfSubTeam={$myRow->SubTeamCode2},
					TfScore=$myRow->Score2,
					TfSetPoints='$myRow->SetEnds2',
					TfSetScore=$myRow->SetPoints2,
					TfArrowstring='$myRow->ArrowString2',
					TfDateTime='".date('Y-m-d H:i:s')."'
				where TfEvent='$myRow->EventCode'
					AND TfMatchNo=".($FromFinToSemi[$myRow->Match2])."
					AND TfTournament={$_SESSION['TourId']}
					");
			$q=safe_w_sql("delete from TeamFinComponent where TfcTournament={$_SESSION['TourId']} and TfcEvent='$myRow->EventCode'");
			$q=safe_r_sql("select tfc.* from TeamComponent tfc
				inner join Teams on TcCoId=TeCoId and TcTournament=TeTournament and TcEvent=TeEvent and TcSubTeam=TeSubTeam and TeFinEvent=1 and TcFinEvent=1
				where TeRank<100 and TeTournament={$_SESSION['TourId']} and TcEvent='$myRow->EventCode'");
			while($r=safe_fetch_assoc($q)) {
				$r['TcSubTeam']='0';
				$r['TcEvent']=$myRow->EventCode;
				$sql=array();
				foreach($r as $k=>$v) {
					if($k!='TcFinEvent') $sql[]=str_replace('Tc', 'Tfc', $k)."='$v'";
				}
				safe_w_sql("insert ignore into TeamFinComponent set ".implode(',', $sql));
			}
		}

		if($AssignPositions) {
			$winner=str_replace(array('#team#', '#subteam#'), $Teams['winner'],$winner);
			$loser=str_replace(array('#team#', '#subteam#'), $Teams['loser'],$loser);
			safe_w_sql($winner);
			safe_w_sql($loser);

			if($myRow->Match1<5) {
				// FINALS => insert into the real finals bronze and gold!!!

				// Updates the Medal matches
				safe_w_sql("update TeamFinals set
						TfTeam={$Teams['winner'][0]},
						TfSubTeam={$Teams['winner'][1]}
					where TfEvent='$myRow->EventCode'
						AND TfMatchNo=".($match4winner-1)."
						AND TfTournament={$_SESSION['TourId']}
						");
				safe_w_sql("update TeamFinals set
						TfTeam={$Teams['loser'][0]},
						TfSubTeam={$Teams['loser'][1]}
					where TfEvent='$myRow->EventCode'
						AND TfMatchNo=".($match4loser-1)."
						AND TfTournament={$_SESSION['TourId']}
						");
			}
		}
	}

	return $ret;
}

function ClubTeamUpdateArrowString($HhtRow, $Filter='') {
	static $MatchMode=null;
	if(is_null($MatchMode)) $MatchMode=getModuleParameter('ClubTeam', 'MatchModeScoring');

	$Select = "SELECT
			CTSEventCode as EvCode,
			CTSMatchNo as MatchNo,
			CTSArrowString as ArString,
			CTSTieBreak as TbString,
			CTSPhase,
			CTSRound,
			CTSPrimary
		FROM ClubTeamScore
		WHERE CTSTournament={$_SESSION['TourId']}
			$Filter";

	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1) {
		$MatchUpdated=false; // server per aggiornare il timestamp

		$MyRow=safe_fetch($Rs);

		$obj=getEventArrowsParams($MyRow->EvCode, 2, 1);
		$maxArrows=$obj->ends*$obj->arrows;
		$maxSoArrows=$obj->so;

		$ArrowStart=$HhtRow->HdArrowStart;
		$ArrowEnd=$HhtRow->HdArrowEnd;
		$ArrowString=$HhtRow->HdArrowString;

		$ArrowStart--;
		$Len=$ArrowEnd-$ArrowStart;
		$Offset=($ArrowStart<$maxArrows ? 0 : $maxArrows);

		$SubArrowString=substr($ArrowString,0,$Len);
		$tmpArrowString=str_pad(($Offset==0 ? $MyRow->ArString : $MyRow->TbString),($Offset==0 ? $maxArrows : $maxSoArrows)," ",STR_PAD_RIGHT);
		$tmpArrowString=substr_replace($tmpArrowString,$SubArrowString,$ArrowStart-$Offset,$Len);
		$ExtraField='';

		if($Offset==0) {
			$Score=ValutaArrowString($tmpArrowString);
			$ExtraField.=", CTsScore='$Score'";
		}

		// assigns SetValues
		if($Offset==0 and $MatchMode) {
			$Ends=array();
			for($i=0; $i<$maxArrows; $i+=$obj->arrows) {
				$end=substr($tmpArrowString, $i, $obj->arrows);
				if(trim($end)=='') {
					$Ends[]='';
				} else {
					$Ends[]=ValutaArrowString($end);
				}
			}
			$ExtraField.=", CTSSetEnds='".implode('|', $Ends)."'";
		}

		$query="UPDATE ClubTeamScore
			SET " . ($Offset==0 ? "CTSArrowString" : "CTSTiebreak") . "=" . StrSafe_DB($tmpArrowString) . ",
				CTSTimeStamp=CTSTimeStamp
				$ExtraField
			WHERE
				CTSTie!=2
				AND CTSTournament={$_SESSION['TourId']}
				$Filter";

		safe_w_sql($query);
		$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());

		if($MatchUpdated) {
			$query="UPDATE ClubTeamScore
				SET CTSTimeStamp='".date('Y-m-d H:i:s')."'
				WHERE
					CTSTournament={$_SESSION['TourId']}
					$Filter";
			safe_w_sql($query);
		}
		return ClubTeamMatchTotal($MyRow->MatchNo, $MyRow->EvCode, $MyRow->CTSPhase, $MyRow->CTSRound, $MyRow->CTSPrimary);
	}
}

function ClubTeamMatchTotal($MatchNo, $EvCode, $Phase, $Round, $Primary) {
	$filter=" AND (CTG.CTGMatchNo1=$MatchNo OR CTG.CTGMatchNo2=$MatchNo) ";
	$Rs=getMatchesPhase1($EvCode, $Round, $Primary, $Phase, $filter);

	if (safe_num_rows($Rs)==1) {
		$myRow=safe_fetch($Rs);

		// azzero i Points di entrambi i team
		$Query = "UPDATE ClubTeamScore
			SET CTSPoints=0
			WHERE CTSTournament={$_SESSION['TourId']}
				AND CTSPhase=$Phase
				AND CTSRound=$Round
				AND CTSEventCode=" . StrSafe_DB($EvCode) . "
				AND CTSPrimary=$Primary
				AND CTSMatchNo IN({$myRow->Match1},{$myRow->Match2}) ";
		$Rs=safe_w_sql($Query);

		$Score1=$myRow->Score1;
		$Score2=$myRow->Score2;

		if(getModuleParameter('ClubTeam', 'MatchModeScoring')) {
			// recalculate sets!
			$Sets1=explode('|',$myRow->SetEnds1);
			$Sets2=explode('|',$myRow->SetEnds2);
			$SetPoints1=0;
			$SetPoints2=0;
			foreach($Sets1 as $k => $v) {
				$Arr1=str_replace(' ', '', substr($myRow->ArrowString1, $k*$myRow->EvFinArrows, $myRow->EvFinArrows));
				$Arr2=str_replace(' ', '', substr($myRow->ArrowString2, $k*$myRow->EvFinArrows, $myRow->EvFinArrows));

				if($v!='' and isset($Sets2[$k]) and $Sets2[$k]!='' and $Arr1==strtoupper($Arr1) and $Arr2==strtoupper($Arr2)) {
					if($v>$Sets2[$k]) {
						$SetPoints1+=2;
					} elseif($v<$Sets2[$k]) {
						$SetPoints2+=2;
					} else {
						$SetPoints1+=1;
						$SetPoints2+=1;
					}
				}
			}

			$SetTie1='';
			$SetTie2='';
			if($Phase>1) {
				if($myRow->Tie1==1) {
					$SetPoints1+=1;
				} elseif($myRow->Tie2==1) {
					$SetPoints2+=1;
				} else {
					// check if there are tiebreak arrows...
					if($myRow->Tiebreak1 and $myRow->Tiebreak2) {
						$t1=ValutaArrowString($myRow->Tiebreak1);
						$t2=ValutaArrowString($myRow->Tiebreak2);
						if($t1>$t2 or strtoupper($myRow->Tiebreak1)!=$myRow->Tiebreak1) {
							$SetPoints1+=1;
							$SetTie1=', CTSTie=1';
							$SetTie2=', CTSTie=0';
						} elseif($t2>$t1 or strtoupper($myRow->Tiebreak2)!=$myRow->Tiebreak2) {
							$SetPoints2+=1;
							$SetTie1=', CTSTie=0';
							$SetTie2=', CTSTie=1';
						} else {
							// check if one of the arrows is closer to center by value
							$t1=array();
							$t2=array();
							for($n=0; $n<strlen($myRow->Tiebreak1); $n++) {
								$t1[]=ValutaArrowString($myRow->Tiebreak1[$n]);
							}
							for($n=0; $n<strlen($myRow->Tiebreak2); $n++) {
								$t2[]=ValutaArrowString($myRow->Tiebreak2[$n]);
							}
							rsort($t1);
							rsort($t2);
							foreach($t1 as $k => $v) {
								if($t1[$k]>$t2[$k]) {
									$SetPoints1+=1;
									$SetTie1=', CTSTie=1';
									$SetTie2=', CTSTie=0';
									break; // exits the foreach
								} elseif($t2[$k]>$t1[$k]) {
									$SetPoints2+=1;
									$SetTie1=', CTSTie=0';
									$SetTie2=', CTSTie=1';
									break; // exits the foreach
								}
							}
						}
					}
				}
			}

			safe_w_sql("UPDATE ClubTeamScore
				SET CTSSetPoints=$SetPoints1 $SetTie1
				WHERE CTSTournament={$_SESSION['TourId']}
					AND CTSPhase=$Phase
					AND CTSRound=$Round
					AND CTSEventCode=" . StrSafe_DB($EvCode) . "
					AND CTSPrimary=$Primary
					AND CTSMatchNo={$myRow->Match1}");
			safe_w_sql("UPDATE ClubTeamScore
				SET CTSSetPoints=$SetPoints2 $SetTie2
				WHERE CTSTournament={$_SESSION['TourId']}
					AND CTSPhase=$Phase
					AND CTSRound=$Round
					AND CTSEventCode=" . StrSafe_DB($EvCode) . "
					AND CTSPrimary=$Primary
					AND CTSMatchNo={$myRow->Match2}");
			$Score1=$SetPoints1;
			$Score2=$SetPoints2;
		}

		if($Phase==1) {
			if($Score1 or $Score2) {
				$match2up=0;
				if ($Score1 > $Score2) {
					// vince 1
					$match2up=$myRow->Match1;
				} elseif ($Score1 < $Score2) {
					// vince 2
					$match2up=$myRow->Match2;
				}

				if ($match2up>0) {
					$Query = "UPDATE ClubTeamScore
						SET CTSPoints=2
						WHERE CTSTournament={$_SESSION['TourId']}
							AND CTSPhase=1
							AND CTSRound=$Round
							AND CTSEventCode=" . StrSafe_DB($EvCode) . "
							AND CTSPrimary=$Primary
							AND CTSMatchNo=" . $match2up. " ";
					$Rs=safe_w_sql($Query);
				} else {
					$Query = "UPDATE ClubTeamScore
						SET CTSPoints=1
						WHERE CTSTournament={$_SESSION['TourId']}
							AND CTSPhase=1
							AND CTSRound=$Round
							AND CTSEventCode=" . StrSafe_DB($EvCode) . "
							AND CTSPrimary=$Primary
							AND CTSMatchNo in($myRow->Match1,$myRow->Match2) ";
					$Rs=safe_w_sql($Query);
				}
			}
			return; // end phase 1
		}

		if($Phase==2) {
			$myRow->Score1=$Score1;
			$myRow->Score2=$Score2;
			WinnerFrom2to3($myRow);
		}
	}
}

function isPrimary($event) {
	$q=safe_r_sql("select EvFinalFirstPhase from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=1 and EvCode='$event'");
	$r=safe_fetch($q);
	return ($r->EvFinalFirstPhase>0 ? 1 : 0);
}