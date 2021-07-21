<?php
/*
													- WriteScore_Bra.php -
	Scrive in Finals
*/

$JSON=array('error'=>1, 'which'=>'', 'field_error'=>0, 'ath'=>array());

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Final/Fun_ChangePhase.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');

if (!CheckTourSession() or checkACL(AclIndividuals, AclReadWrite, false)!=AclReadWrite or IsBlocked(BIT_BLOCK_IND)) {
	JsonOut($JSON);
}

$Continue=true;

foreach ($_REQUEST as $Key => $Value) {
	// ho qualcosa da scrivere (considero l'attuale matchno)
	$Items=explode('_', $Key);
	if(count($Items)<4) {
		continue;
	}
	$ee=$Items[2];
	$mm=$Items[3];
	$opp = ($mm % 2) ? $mm - 1 : $mm + 1;
	$obj=getEventArrowsParams($ee, getPhase($mm),0);

	switch($Items[1]) {
		case 'N':
			// A note to put into the match
			$Update = "UPDATE Finals SET FinNotes=" . StrSafe_DB($Value) . " WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($ee) . " AND FinMatchNo=" . StrSafe_DB($mm) . " ";
			$Rs = safe_w_sql($Update);
			$JSON['error']=0;
			JsonOut($JSON);
			break;
		case 'S':
			// Max Score is dependent on Event Ends, Arrows by end and Target type
			$MaxScores = GetMaxScores($ee, $mm);
			if ($Value !== '' and (!is_numeric($Value) or $Value > ($MaxScores['MaxSetPoints'] ? $MaxScores['MaxSetPoints'] : $MaxScores['MaxMatch']))) {
				$JSON['field_error']=1;
				JsonOut($JSON);
			}
			$Update = "UPDATE Finals 
				INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=FinTournament
				SET FinScore=IF(EvMatchMode=0," . StrSafe_DB($Value) . ",FinScore), 
					FinSetScore=IF(EvMatchMode=0,FinSetScore," . StrSafe_DB($Value) . "), 
					FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
				WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($ee) . " AND FinMatchNo=" . StrSafe_DB($mm) . " ";
			$Rs = safe_w_sql($Update);
			break;
		case 'T':
			if (substr($Value, 0, 4) == 'irm-') {
				// needs to reset the tie status also
				$Value=intval(substr($Value, 4));
				safe_w_SQL("update Finals set FinTie=0, FinWinLose=0, FinIrmType=" . $Value . " where FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($ee) . " AND FinMatchNo=" . StrSafe_DB($mm) . " ");
				if($Value) {
					safe_w_sql("UPDATE Finals SET FinTie=2, FinWinLose=1, FinTbClosest=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo=$opp and FinIrmType=0");
				}
			} else {
				if (!is_numeric($Value) or $Value < 0 or $Value > 2) {
					$JSON['field_error']=1;
					JsonOut($JSON);
				}
				$WinLose=min(1, $Value);

				// setting or removing the bye also "kills" the winlose status of both opponents
				setTieWinner($ee, $mm, $opp, $obj->so, $WinLose, $Value);

				// if tie==1 then check the "bonus point" if a set event
				if($Value==1) {
					$q=safe_r_sql("select EvMatchMode, f1.FinSetScore
						from Events
						inner join Finals f1 on f1.FinTournament=EvTournament and f1.FinEvent=EvCode and f1.FinMatchNo=$mm
						inner join Finals f2 on f2.FinTournament=EvTournament and f2.FinEvent=EvCode and f2.FinMatchNo=$opp
						where f1.FinSetScore = f2.FinSetScore and EvMatchMode=1 and EvTournament={$_SESSION['TourId']} and EvCode='$ee' and EvTeamEvent=0");
					if($r=safe_fetch($q)) {
						safe_w_SQL("update Finals set FinSetScore=".($r->FinSetScore+1)." where FinTournament={$_SESSION['TourId']} and FinEvent='$ee' and FinMatchNo=$mm");
					}
				}
			}
			break;
		case 'cl':
		case 't':
			// if at Sets Check if the set score is compatible with a SO
			$Continue=false;
			$q=safe_w_sql("select EvMatchMode, FinScore, FinSetScore, FinTiebreak, FinTbClosest, GrPhase from Finals
				inner join Events on EvTournament=FinTournament and EvCode=FinEvent and EvTeamEvent=0
				inner join Grids on GrMatchNo=FinMatchNo
				WHERE FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo in ($mm, $opp)
				order by FinMatchNo=$mm");
			if($r1=safe_fetch($q)) {
				$r2=safe_fetch($q); // this will be the record of the current matchno

				// this should remove or add the SO arrows
				if(($r2->EvMatchMode and $r1->FinSetScore >= $obj->ends and $r2->FinSetScore >= $obj->ends) or (!$r2->EvMatchMode and $r1->FinScore==$r2->FinScore)) {
					if($Items[1]=='t') {
						$ArrowNum = $Items[4];
						$tiebreak = GetLetterFromPrint($Value);
						$r2->FinTiebreak = str_pad($r2->FinTiebreak, $ArrowNum + 1, ' ', STR_PAD_RIGHT);
						$r2->FinTiebreak[$ArrowNum] = GetLetterFromPrint($Value);

						safe_w_sql("UPDATE Finals SET FinTiebreak=" . StrSafe_DB($r2->FinTiebreak) . ", FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo=$mm");
					}

					// if the 2 tiestrings are same length we can eventually set the winner
					if(strlen(trim($r1->FinTiebreak)) == strlen(trim($r2->FinTiebreak))) {
						$Tie1=ValutaArrowString($r1->FinTiebreak);
						$Tie2=ValutaArrowString($r2->FinTiebreak);
						if($Tie1>$Tie2) {
							// opponent won the tie
							setTieWinner($ee, $opp, $mm, $obj->so);
							// adjust the winner match in case of sets
							if($r2->EvMatchMode) {
								safe_w_sql("update Finals set FinSetScore=if(FinMatchNo=$opp, $obj->winAt, $obj->ends) where FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo in ($mm, $opp)");
							}
						} elseif($Tie1<$Tie2) {
							// current matchno won the tie
							setTieWinner($ee, $mm, $opp, $obj->so);
							// adjust the winner match in case of sets
							if($r2->EvMatchMode) {
								safe_w_sql("update Finals set FinSetScore=if(FinMatchNo=$mm, $obj->winAt, $obj->ends) where FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo in ($mm, $opp)");
							}
						} else {
							if($Items[1]=='cl') {
								// sets the closest
								if($Value) {
									setTieWinner($ee, $mm, $opp, $obj->so, 1, 1, 1);
									if($r2->EvMatchMode) {
										safe_w_sql("update Finals set FinSetScore=if(FinMatchNo=$mm, $obj->winAt, $obj->ends) where FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo in ($mm, $opp)");
									}
								} else {
									setTieWinner($ee, $mm, $opp, $obj->so, 0, 0, 0);
									if($r2->EvMatchMode) {
										safe_w_sql("update Finals set FinSetScore=$obj->ends where FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo in ($mm, $opp)");
									}
								}
							} else {
								if($r1->FinTbClosest) {
									// opponent won the tie
									setTieWinner($ee, $opp, $mm, $obj->so, 1, 1, 1);
									// adjust the winner match in case of sets
									if($r2->EvMatchMode) {
										safe_w_sql("update Finals set FinSetScore=if(FinMatchNo=$opp, $obj->winAt, $obj->ends) where FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo in ($mm, $opp)");
									}
								} elseif($r2->FinTbClosest) {
									// current matchno won the tie
									setTieWinner($ee, $mm, $opp, $obj->so, 1, 1, 1);
									// adjust the winner match in case of sets
									if($r2->EvMatchMode) {
										safe_w_sql("update Finals set FinSetScore=if(FinMatchNo=$mm, $obj->winAt, $obj->ends) where FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo in ($mm, $opp)");
									}
								} else {
									// still tie, removes ties and winner from current match
									setTieWinner($ee, $mm, $opp, $obj->so, 0, 0);
									if($r2->EvMatchMode) {
										safe_w_sql("update Finals set FinSetScore=$obj->ends where FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo in ($mm, $opp)");
									}
								}
							}
						}
						$Continue=true;
					}
				} else {
					// this is not a tie situation so we remove values and closest to center for both opponents
					safe_w_sql("UPDATE Finals SET FinTiebreak='', FinTbClosest=0, FinTbDecoded='', FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo in ($mm, $opp)");

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
		$updateTS = move2NextPhase(NULL, $ee, $mm, 0, true);

		if (!is_null($updateTS)) {
			$Select = "SELECT 
					FinMatchNo, FinEvent,  FinAthlete, IF(EvMatchMode=0,FinScore,FinSetScore) AS Score, FinTie, FinIrmType, FinTbClosest,
					IFNULL(CONCAT(EnFirstName,' ',SUBSTRING(EnName,1,1),'.'),'') AS Atleta,
					IFNULL(CoCode,'') AS Country
				FROM Finals
				INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=FinTournament
				LEFT JOIN Entries ON FinAthlete=EnId
				LEFT JOIN Countries ON EnCountry=CoId
				WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($ee) . " AND FinDateTime=" . StrSafe_DB($updateTS) . "
				ORDER BY FinEvent, FinMatchNo";
			$Rs = safe_w_sql($Select);
			while ($MyRow = safe_fetch($Rs)) {
				$JSON['ath'][] = array(
					'matchno' => $MyRow->FinMatchNo,
					'tie' => $MyRow->FinIrmType ? 'irm-' . $MyRow->FinIrmType : $MyRow->FinTie,
					'name' => $MyRow->Atleta,
					'cty' => $MyRow->Country,
					'closest' => $MyRow->FinTbClosest,
					'score' => $MyRow->Score,
				);
			}
		}
	}
}

JsonOut($JSON);

// ======================================

function setTieWinner($ee, $winner, $loser, $SoArrows, $WinLose=1, $TieValue=1, $Closest=0) {
	safe_w_sql("UPDATE Finals SET FinTie=0, FinWinLose=0, FinTbClosest=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo in ($winner, $loser)");
	safe_w_sql("UPDATE Finals SET FinTie=$TieValue, FinWinLose=$WinLose, FinTbClosest=$Closest, FinIrmType=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo=$winner");

	// calculate the TbDecoded
	$q=safe_r_sql("select FinMatchNo, FinTiebreak, FinTbClosest from Finals WHERE FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo in ($winner, $loser)");
	while($r=safe_fetch($q)) {
		$TbDecoded=array();
		$tiebreak=rtrim($r->FinTiebreak);
		$idx=0;
		while($SoEnd=substr($tiebreak, $idx, $SoArrows)) {
			if($SoArrows>1) {
				$TbDecoded[]=ValutaArrowString($SoEnd);
			} else {
				$TbDecoded[]=DecodeFromLetter($SoEnd);
			}
			$idx+=$SoArrows;
		}

		safe_w_sql("update Finals set FinTbDecoded='" . ($TbDecoded ? implode(",",$TbDecoded).($r->FinTbClosest ? '+' : '') : '') . "' where FinTournament={$_SESSION['TourId']} AND FinEvent='$ee' AND FinMatchNo = $r->FinMatchNo");
	}
}
