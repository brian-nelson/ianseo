<?php
/*
													- WriteScore_Bra.php -
	Scrive in Finals
*/

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Final/Fun_ChangePhase.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');

$JSON=array('error'=>1, 'which'=>'', 'field_error'=>0, 'ath'=>array());

if (!CheckTourSession() or checkACL(AclTeams, AclReadWrite,false)!=AclReadWrite or IsBlocked(BIT_BLOCK_TEAM)) {
	JsonOut($JSON);
}

$Continue=true;

foreach ($_REQUEST as $Key => $Value) {
	$Items=explode('_', $Key);
	if(count($Items)<4) {
		continue;
	}
	$ee=$Items[2];
	$mm=$Items[3];
	$opp = ($mm % 2) ? $mm - 1 : $mm + 1;

	switch($Items[1]) {
		case 'N':
			// A note to put into the match
			$Update = "UPDATE TeamFinals SET TfNotes=" . StrSafe_DB($Value) . " WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($ee) . " AND TfMatchNo=" . StrSafe_DB($mm);
			$Rs=safe_w_sql($Update);
			$JSON['error']=0;
			JsonOut($JSON);
			break;
		case 'S':
			// Max Score is dependent on Event Ends, Arrows by end and Target type
			$MaxScores=GetMaxScores($ee, $mm, '1'); // team
			if (!is_numeric($Value) || $Value > ($MaxScores['MaxSetPoints'] ? $MaxScores['MaxSetPoints'] : $MaxScores['MaxMatch'])) {
				$JSON['field_error']=1;
				JsonOut($JSON);
			} else {
				$Update = "UPDATE TeamFinals
					INNER JOIN Events ON TfEvent=EvCode AND EvTeamEvent='1' AND EvTournament=TfTournament
					SET TfScore=IF(EvMatchMode=0," . StrSafe_DB($Value) . ",TfScore), 
						TfSetScore=IF(EvMatchMode=0,TfSetScore," . StrSafe_DB($Value) . "),
						TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
					WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($ee) . " AND TfMatchNo=" . StrSafe_DB($mm) . " ";
				$Rs=safe_w_sql($Update);
			}
			break;
		case 'T':
			if (substr($Value, 0, 4) == 'irm-') {
				// needs to reset the tie status also
				$Value=intval(substr($Value, 4));
				safe_w_SQL("update TeamFinals set TfTie=0, TfWinLose=0, TfIrmType=" . $Value . " where TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($ee) . " AND TfMatchNo=" . StrSafe_DB($mm) . " ");
				if($Value) {
					safe_w_sql("UPDATE TeamFinals SET TfTie=2, TfWinLose=1, TfTbClosest=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo=$opp and TfIrmType=0");
				}
			} else {
				if (!is_numeric($Value) or $Value < 0 or $Value > 2) {
					$JSON['field_error']=1;
					JsonOut($JSON);
				}
				$WinLose=min(1, $Value);

				// setting or removing the bye also "kills" the winlose status of both opponents
				setTieWinner($ee, $mm, $opp, $WinLose, $Value);

				// if tie==1 then check the "bonus point" if a set event
				if($Value==1) {
					$q=safe_r_sql("select EvMatchMode, f1.TfSetScore
						from Events
						inner join TeamFinals f1 on f1.TfTournament=EvTournament and f1.TfEvent=EvCode and f1.TfMatchNo=$mm
						inner join TeamFinals f2 on f2.TfTournament=EvTournament and f2.TfEvent=EvCode and f2.TfMatchNo=$opp
						where f1.TfSetScore = f2.TfSetScore and EvMatchMode=1 and EvTournament={$_SESSION['TourId']} and EvCode='$ee' and EvTeamEvent=1");
					if($r=safe_fetch($q)) {
						safe_w_SQL("update TeamFinals set TfSetScore=".($r->TfSetScore+1)." where TfTournament={$_SESSION['TourId']} and TfEvent='$ee' and TfMatchNo=$mm");
					}
				}
			}
			break;
		case 'cl':
		case 't':
			// if at Sets Check if the set score is compatible with a SO
			$Continue=false;
			$SQL="select EvMatchMode, TfScore, TfSetScore, TfTiebreak, TfTbClosest, GrPhase 
					from TeamFinals
					inner join Events on EvTournament=TfTournament and EvCode=TfEvent and EvTeamEvent=1
					inner join Grids on GrMatchNo=TfMatchNo
					WHERE TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo in ($mm, $opp)
					order by TfMatchNo=$mm";
			$q=safe_w_sql($SQL);
			if($r1=safe_fetch($q)) {
				$r2=safe_fetch($q); // this will be the record of the current matchno
				$obj=getEventArrowsParams($ee, $r2->GrPhase,1);
				if(($r2->EvMatchMode and $r1->TfSetScore >= $obj->ends and $r2->TfSetScore >= $obj->ends) or (!$r2->EvMatchMode and $r1->TfScore==$r2->TfScore)) {
					if($Items[1]=='t') {
						$ArrowNum = $Items[4];
						$tiebreak = GetLetterFromPrint($Value);
						$r2->TfTiebreak = str_pad($r2->TfTiebreak, $ArrowNum + 1, ' ', STR_PAD_RIGHT);
						$r2->TfTiebreak[$ArrowNum] = GetLetterFromPrint($Value);
						safe_w_sql("UPDATE TeamFinals SET TfTiebreak=" . StrSafe_DB($r2->TfTiebreak) . ", TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo=$mm");
					}

					// calculate the TbDecoded
					$TbDecoded=array();
					$tiebreak=rtrim($r2->TfTiebreak);
					$idx=0;
					while($SoEnd=substr($tiebreak, $idx, $obj->so)) {
						if($obj->so>1) {
							$TbDecoded[]=ValutaArrowString($SoEnd);
						} else {
							$TbDecoded[]=DecodeFromLetter($SoEnd);
						}
						$idx+=$obj->so;
					}
					safe_w_sql("update TeamFinals set TfTbDecoded='" . ($TbDecoded ? implode(",",$TbDecoded).($r2->TfTbClosest ? '+' : '') : '') . "' where TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo = $mm");

					// if the 2 tiestrings are same length we can eventually set the winner
					if(strlen(trim($r1->TfTiebreak)) == strlen(trim($r2->TfTiebreak))) {
						$Tie1=ValutaArrowString($r1->TfTiebreak);
						$Tie2=ValutaArrowString($r2->TfTiebreak);
						if($Tie1>$Tie2) {
							// opponent won the tie
							setTieWinner($ee, $opp, $mm);
							// adjust the winner match in case of sets
							if($r2->EvMatchMode) {
								safe_w_sql("update TeamFinals set TfSetScore=if(TfMatchNo=$opp, $obj->winAt, $obj->ends) where TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo in ($mm, $opp)");
							}
						} elseif($Tie1<$Tie2) {
							// current matchno won the tie
							setTieWinner($ee, $mm, $opp);
							// adjust the winner match in case of sets
							if($r2->EvMatchMode) {
								safe_w_sql("update TeamFinals set TfSetScore=if(TfMatchNo=$mm, $obj->winAt, $obj->ends) where TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo in ($mm, $opp)");
							}
						} else {
							if($Items[1]=='cl') {
								// sets the closest
								if($Value) {
									setTieWinner($ee, $mm, $opp, 1, 1, 1);
									if($r2->EvMatchMode) {
										safe_w_sql("update TeamFinals set TfSetScore=if(TfMatchNo=$mm, $obj->winAt, $obj->ends) where TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo in ($mm, $opp)");
									}
								} else {
									setTieWinner($ee, $mm, $opp, 0, 0, 0);
									if($r2->EvMatchMode) {
										safe_w_sql("update TeamFinals set TfSetScore=$obj->ends where TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo in ($mm, $opp)");
									}
								}
							} else {
								if($r1->TfTbClosest) {
									// opponent won the tie
									setTieWinner($ee, $opp, $mm, 1, 1, 1);
									// adjust the winner match in case of sets
									if($r2->EvMatchMode) {
										safe_w_sql("update TeamFinals set TfSetScore=if(TfMatchNo=$opp, $obj->winAt, $obj->ends) where TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo in ($mm, $opp)");
									}
								} elseif($r2->TfTbClosest) {
									// current matchno won the tie
									setTieWinner($ee, $mm, $opp, 1, 1, 1);
									// adjust the winner match in case of sets
									if($r2->EvMatchMode) {
										safe_w_sql("update TeamFinals set TfSetScore=if(TfMatchNo=$mm, $obj->winAt, $obj->ends) where TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo in ($mm, $opp)");
									}
								} else {
									// still tie, removes ties and winner from current match
									setTieWinner($ee, $mm, $opp, 0, 0);
									if($r2->EvMatchMode) {
										safe_w_sql("update TeamFinals set TfSetScore=$obj->ends where TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo in ($mm, $opp)");
									}
								}
							}
						}
						$Continue=true;
					}
				} elseif($Items[1]=='t') {
					// this is not a tie situation so we do not accept arrows except to remove values
					$ArrowNum = $Items[4];
					$r2->TfTiebreak = str_pad($r2->TfTiebreak, $ArrowNum + 1, ' ', STR_PAD_RIGHT);
					$r2->TfTiebreak[$ArrowNum] = ' ';
					safe_w_sql("UPDATE TeamFinals SET TfTiebreak=" . StrSafe_DB(rtrim($r2->TfTiebreak)) . ", TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo=$mm");
				}

			}
			break;
		default:
			JsonOut($JSON);
	}

	$JSON['which'] = $Key;
	$JSON['error']=0;
	$JSON['event']=$ee;

	if($Continue) {
		// move to next phase as we do not know which moment this is called
		$updateTS = move2NextPhaseTeam(NULL, $ee, $mm, 0, true);

		if (!is_null($updateTS)) {
			$Select = "SELECT 
					TfMatchNo, TfEvent,  TfTeam, TfSubTeam, IF(EvMatchMode=0,TfScore,TfSetScore) AS Score, TfTie, TfIrmType, TfTbClosest,
					CoCode, CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) AS TeamName
				FROM TeamFinals
				INNER JOIN Events ON TfEvent=EvCode AND EvTeamEvent='1' AND EvTournament=TfTournament
				LEFT JOIN Countries ON TfTeam=CoId 
				WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($ee) . " AND TfDateTime=" . StrSafe_DB($updateTS) . "
				ORDER BY TfEvent, TfMatchNo";
			$Rs = safe_w_sql($Select);
			while ($MyRow = safe_fetch($Rs)) {
				$JSON['ath'][] = array(
					'matchno' => $MyRow->TfMatchNo,
					'tie' => $MyRow->TfIrmType ? 'irm-' . $MyRow->TfIrmType : $MyRow->TfTie,
					'name' => $MyRow->TeamName,
					'cty' => $MyRow->CoCode,
					'closest' => $MyRow->TfTbClosest,
					'score' => $MyRow->Score,
				);
			}
		}
	}
}

JsonOut($JSON);

// ======================================

function setTieWinner($ee, $winner, $loser, $WinLose=1, $TieValue=1, $Closest=0) {
	safe_w_sql("UPDATE TeamFinals SET TfTie=0, TfWinLose=0, TfTbClosest=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo in ($winner, $loser)");
	safe_w_sql("UPDATE TeamFinals SET TfTie=$TieValue, TfWinLose=$WinLose, TfTbClosest=$Closest, TfIrmType=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfTournament={$_SESSION['TourId']} AND TfEvent='$ee' AND TfMatchNo=$winner");
}
