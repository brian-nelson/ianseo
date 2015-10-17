<?php
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Fun_ChangePhase.inc.php');

function UpdateArrowPosition($MatchNo, $EvCode, $TeamEvent, $ArrowPosX, $ArrowPosY, $ArrowPos=null)
{
	$retValue = null;
	$Select = '';

	$TablePrefix = "Fin";
	$Select
		= "SELECT "
		. "FinEvent as EvCode, FinMatchNo as MatchNo, FinArrowString as ArString, FinTieBreak as TbString, FinArrowPosition as ArPos, FinTiePosition as TbPos, GrPhase "
		. "FROM Finals "
		. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
		. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinMatchNo=" . StrSafe_DB($MatchNo) . " AND FinEvent=" . StrSafe_DB($EvCode);
	if($TeamEvent) {
		$TablePrefix = "Tf";
		$Select
			= "SELECT "
			. "TfEvent as EvCode, TfMatchNo as MatchNo, TfArrowString as ArString, TfTieBreak as TbString, TfArrowPosition as ArPos, TfTiePosition as TbPos, GrPhase "
			. "FROM TeamFinals "
			. "INNER JOIN Grids ON TfMatchNo=GrMatchNo "
			. "WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfMatchNo=" . StrSafe_DB($MatchNo) . " AND TfEvent=" . StrSafe_DB($EvCode);
	}
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1)
	{
		$MatchUpdated=false; // server per aggiornare il timestamp

		$MyRow=safe_fetch($Rs);

		$obj=getEventArrowsParams($MyRow->EvCode,$MyRow->GrPhase,$TeamEvent);
		$maxArrows=$obj->ends*$obj->arrows;
		$maxSoArrows=$obj->so;

		$isShootOff=-1;
		$ArrowNumber = 0;

		if(is_null($ArrowPos)) {
			$isShootOff=0;
			$ArrowNumber = strpos(str_pad($MyRow->ArString,$maxArrows," ",STR_PAD_RIGHT)," ");
			if($ArrowNumber===false || $ArrowNumber>=$maxArrows) {
				$isShootOff = 1;
				$ArrowNumber = strpos(str_pad($MyRow->TbString,$maxSoArrows," ",STR_PAD_RIGHT)," ");
				if($ArrowNumber===false || $ArrowNumber >= $maxSoArrows)
					$isShootOff = -1;
			}
		} elseif(preg_match('/^([01])[|]([0-9]*)$/i',$ArrowPos,$found)) {
			$isShootOff=$found[1];
			$ArrowNumber = $found[2];
		} elseif(preg_match('/^([0-9]*)$/i',$ArrowPos)) {
			if($ArrowPos>$maxArrows) {
				$isShootOff = 1;
				$ArrowNumber = $ArrowPos-$maxArrows-1;
			} else {
				$isShootOff=0;
				$ArrowNumber = $ArrowPos-1;
			}
		}

		if($isShootOff != -1)
		{
			$retValue = $isShootOff . "|" . $ArrowNumber;
			//Carico le posizioni esistente e "aggiusto" le dimensioni dell'array
			$ArrowPos = explode("|",($isShootOff==0 ? $MyRow->ArPos : $MyRow->TbPos));
			for($i=count($ArrowPos); $i<($isShootOff==0 ? $maxArrows : $maxSoArrows); $i++)
				$ArrowPos[$i] = '';
			if(count($ArrowPos)>($isShootOff==0 ? $maxArrows : $maxSoArrows))
				$ArrowPos = array_slice($ArrowPos, 0, ($isShootOff==0 ? $maxArrows : $maxSoArrows));
			if($ArrowPosX != '' && $ArrowPosY != '')
				$ArrowPos[$ArrowNumber]=$ArrowPosX . "," . $ArrowPosY;
			else
				$ArrowPos[$ArrowNumber]='';

			$query="UPDATE "
				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
				. "SET "
				. $TablePrefix . ($isShootOff==0 ? "ArrowPosition" : "TiePosition") . "=" . StrSafe_DB(implode("|",$ArrowPos)) . ", "
				. "{$TablePrefix}DateTime={$TablePrefix}DateTime "
				. "WHERE "
				. "{$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
				. "AND {$TablePrefix}MatchNo=". StrSafe_DB($MyRow->MatchNo) . " "
				. "AND {$TablePrefix}Tournament=". StrSafe_DB($_SESSION['TourId']);

			safe_w_sql($query);

			$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());

			if($MatchUpdated)
			{
				$query="UPDATE "
					. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
					. "SET "
					. "{$TablePrefix}DateTime=now() "
					. "WHERE "
					. " {$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
					. "AND {$TablePrefix}MatchNo = ". StrSafe_DB($MyRow->MatchNo). " "
					. "AND {$TablePrefix}Tournament=". StrSafe_DB($_SESSION['TourId']);
				safe_w_sql($query);
			}
		}
	}
	return $retValue;
}

function UpdateArrowString($MatchNo, $EvCode, $TeamEvent, $ArrowString, $ArrowStart, $ArrowEnd)
{
	$Select ='';

	$TablePrefix = "Fin";
	$Select
		= "SELECT "
		. "FinEvent as EvCode, FinMatchNo as MatchNo, FinArrowString as ArString, FinTieBreak as TbString, "
		. "EvMatchMode, EvMatchArrowsNo, GrPhase "
		. "FROM Finals "
		. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
		. "INNER JOIN Events ON FinEvent=EvCode AND FinTournament=EvTournament AND EvTeamEvent=0 "
		. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinMatchNo=" . StrSafe_DB($MatchNo) . " AND FinEvent=" . StrSafe_DB($EvCode);
	if($TeamEvent) {
		$TablePrefix = "Tf";
		$Select
			= "SELECT "
			. "TfEvent as EvCode, TfMatchNo as MatchNo, TfArrowString as ArString, TfTieBreak as TbString, "
			. "EvMatchMode, EvMatchArrowsNo, GrPhase "
			. "FROM TeamFinals "
			. "INNER JOIN Grids ON TfMatchNo=GrMatchNo "
			. "INNER JOIN Events ON TfEvent=EvCode AND TfTournament=EvTournament AND EvTeamEvent=1 "
			. "WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfMatchNo=" . StrSafe_DB($MatchNo) . " AND TfEvent=" . StrSafe_DB($EvCode);
	}

	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1)
	{
		$MatchUpdated=false; // server per aggiornare il timestamp

		$MyRow=safe_fetch($Rs);

		$obj=getEventArrowsParams($MyRow->EvCode,$MyRow->GrPhase,$TeamEvent);
		$maxArrows=$obj->ends*$obj->arrows;
		$maxSoArrows=$obj->so;

		$ArrowStart--;
		$Len=$ArrowEnd-$ArrowStart;
		$Offset=($ArrowStart<$maxArrows ? 0 : $maxArrows);

		$SubArrowString=substr($ArrowString,0,$Len);
		$tmpArrowString=str_pad(($Offset==0 ? $MyRow->ArString : $MyRow->TbString),($Offset==0 ? $maxArrows : $maxSoArrows)," ",STR_PAD_RIGHT);
		$tmpArrowString=substr_replace($tmpArrowString,$SubArrowString,$ArrowStart-$Offset,$Len);

		$query="UPDATE "
			. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
			. "SET "
			. $TablePrefix . ($Offset==0 ? "ArrowString" : "Tiebreak") . "=" . StrSafe_DB($tmpArrowString) . ", "
			. "{$TablePrefix}DateTime={$TablePrefix}DateTime "
			. "WHERE "
			. "{$TablePrefix}Tie!=2 "
			. "AND {$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
			. "AND {$TablePrefix}MatchNo=". StrSafe_DB($MyRow->MatchNo) . " "
			. "AND {$TablePrefix}Tournament=". StrSafe_DB($_SESSION['TourId']);

		safe_w_sql($query);
		$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());

		if($MatchUpdated)
		{
			$query="UPDATE "
				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
				. "SET "
				. "{$TablePrefix}DateTime=now() "
				. "WHERE "
				. " {$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
				. "AND {$TablePrefix}MatchNo = ". StrSafe_DB($MyRow->MatchNo). " "
				. "AND {$TablePrefix}Tournament=". StrSafe_DB($_SESSION['TourId']);
			safe_w_sql($query);
		}
		//print $query;
		return MatchTotal($MatchNo, $EvCode, $TeamEvent);
	}
}

function MatchTotal($MatchNo, $EvCode, $TeamEvent=0)
{
	if(is_null($MatchNo) || is_null($EvCode))	//Devono esistere sia il MatchNo che l'evento
		return;

	$MatchFinished=false; // serve per vedere se il match è finito
	$TablePrefix = "Fin";
	$Select
		= "SELECT "
		. "f.FinEvent as EvCode, f.FinMatchNo as MatchNo, f2.FinMatchNo as OppMatchNo, EvMatchMode, EvMatchArrowsNo, "
		. "IF(f.FinDateTime>=f2.FinDateTime, f.FinDateTime, f2.FinDateTime) AS DateTime,"
		. "f.FinScore AS Score, f.FinSetScore AS SetScore, f.FinTie as Tie, IFNULL(f.FinArrowString,'') as ArString, IFNULL(f.FinTieBreak,'') as TbString, "
		. "f2.FinScore AS OppScore, f2.FinSetScore AS OppSetScore, f2.FinTie as OppTie, IFNULL(f2.FinArrowString,'') as OppArString, IFNULL(f2.FinTieBreak,'') as OppTbString, "
		. "GrPhase "
		. "FROM Finals AS f "
		. "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament "
		. "INNER JOIN Events ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0 "
		. "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo "
		. "WHERE f.FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (f.FinMatchNo % 2)=0 AND GrMatchNo=" . StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) . " AND f.FinEvent=" . StrSafe_DB($EvCode) . " "
		. "ORDER BY f.FinEvent, f.FinMatchNo ";

	if($TeamEvent) {
		$TablePrefix = "Tf";
		$Select
			= "SELECT "
			. "f.TfEvent as EvCode, f.TfMatchNo as MatchNo, f2.TfMatchNo as OppMatchNo, EvMatchMode, EvMatchArrowsNo, "
			. "IF(f.TfDateTime>=f2.TfDateTime, f.TfDateTime, f2.TfDateTime) AS DateTime,"
			. "f.TfScore AS Score, f.TfSetScore AS SetScore, f.TfTie as Tie, IFNULL(f.TfArrowString,'') as ArString, IFNULL(f.TfTieBreak,'') as TbString, "
			. "f2.TfScore AS OppScore, f2.TfSetScore AS OppSetScore, f2.TfTie as OppTie, IFNULL(f2.TfArrowString,'') as OppArString, IFNULL(f2.TfTieBreak,'') as OppTbString, "
			. "GrPhase "
			. "FROM TeamFinals AS f "
			. "INNER JOIN TeamFinals AS f2 ON f.TfEvent=f2.TfEvent AND f.TfMatchNo=IF((f.TfMatchNo % 2)=0,f2.TfMatchNo-1,f2.TfMatchNo+1) AND f.TfTournament=f2.TfTournament "
			. "INNER JOIN Events ON f.TfEvent=EvCode AND f.TfTournament=EvTournament AND EvTeamEvent=1 "
			. "INNER JOIN Grids ON f.TfMatchNo=GrMatchNo "
			. "WHERE f.TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (f.TfMatchNo % 2)=0 AND GrMatchNo=" . StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) . " AND f.TfEvent=" . StrSafe_DB($EvCode) . " "
			. "ORDER BY f.TfEvent, f.TfMatchNo ";
	}

	//print $Select . "<br>";exit;
	$MatchUpdated=false; // serve per aggiornare il timestamp
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1) {
		$MyRow=safe_fetch($Rs);
		$obj=getEventArrowsParams($MyRow->EvCode,$MyRow->GrPhase,$TeamEvent);
		$TotArrows=$obj->ends*$obj->arrows;
		$Winner=-1;

		// set winner... of Ties
		if($MyRow->Tie) {
			$Winner=$MyRow->MatchNo;
			$MatchFinished=true;
		} elseif ($MyRow->OppTie) {
			$Winner=$MyRow->OppMatchNo;
			$MatchFinished=true;
		}

		$Score=ValutaArrowString(substr($MyRow->ArString, 0, $TotArrows));
		$OppScore=ValutaArrowString(substr($MyRow->OppArString, 0, $TotArrows));

		if($MyRow->EvMatchMode==0) {
			//Sistema Cumulativo
			if(strlen(str_replace(' ', '', $MyRow->ArString))==$TotArrows
				and strlen(str_replace(' ', '', $MyRow->OppArString))==$TotArrows
				and strlen(str_replace(' ', '', $MyRow->TbString))==strlen(str_replace(' ', '', $MyRow->OppTbString))) {
				$MatchFinished=true;
				// if match is over establish the winner
				// only if not already decided by the tie
				// and if there are no doubts
				// and no SO are going on
				if($Winner==-1
						and $MyRow->ArString==strtoupper($MyRow->ArString)
						and $MyRow->OppArString==strtoupper($MyRow->OppArString)
						and strlen(trim($MyRow->TbString))==strlen(trim($MyRow->OppTbString))) {
					if($Score>$OppScore) {
						$Winner=$MyRow->MatchNo;
					} elseif($Score<$OppScore) {
						$Winner=$MyRow->OppMatchNo;
					}
				}
			}

			$query="UPDATE "
				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
				. "SET "
				. "{$TablePrefix}WinLose=" . ($Winner==$MyRow->MatchNo ? '1' : '0') . ", "
				. "{$TablePrefix}Score=" . $Score . ", "
				. "{$TablePrefix}SetScore=0, "
				. "{$TablePrefix}SetPoints='', "
				. "{$TablePrefix}DateTime={$TablePrefix}DateTime "
				. "WHERE "
				. " {$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
				. " AND {$TablePrefix}MatchNo=". StrSafe_DB($MyRow->MatchNo) . " "
				. " AND {$TablePrefix}Tournament=". StrSafe_DB($_SESSION['TourId']);

			safe_w_sql($query);
			$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());

			//print $query.'<br><br>';

			$query="UPDATE "
				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
				. "SET "
				. "{$TablePrefix}WinLose=" . ($Winner==$MyRow->OppMatchNo ? '1' : '0') . ", "
				. "{$TablePrefix}Score=" . $OppScore . ", "
				. "{$TablePrefix}SetScore=0, "
				. "{$TablePrefix}SetPoints='', "
				. "{$TablePrefix}DateTime={$TablePrefix}DateTime "
				. "WHERE "
				. " {$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
				. " AND {$TablePrefix}MatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " "
				. " AND {$TablePrefix}Tournament=". StrSafe_DB($_SESSION['TourId']);

			safe_w_sql($query);
			$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());

			//print $query.'<br><br>';
		} else {
			//Sistema a Set
			$SetPointsAth=array();
			$SetPointsOpp=array();
			$SetAth=0;
			$SetOpp=0;
			$SetAthWin=0;
			$SetOppWin=0;
			$WinnerId=-1;
			for($i=0; $i<$TotArrows; $i=$i+$obj->arrows) {
				//Cicla per tutte le volee dell'incontro
				$AthEndString=substr($MyRow->ArString, $i, $obj->arrows);
				$OppEndString=substr($MyRow->OppArString, $i, $obj->arrows);
				$MatchString=$AthEndString.$OppEndString;
				$AthSetPoints=ValutaArrowString($AthEndString);
				$OppSetPoints=ValutaArrowString($OppEndString);
				$SetPointsAth[] = $AthSetPoints;
				$SetPointsOpp[] = $OppSetPoints;

				if(strpos($MatchString, ' ')===false and ctype_upper($MatchString)) {
					if($AthSetPoints>$OppSetPoints) {
						$SetAth += 2;
						$SetAthWin++;
					} elseif($AthSetPoints<$OppSetPoints) {
						$SetOpp += 2;
						$SetOppWin++;
					} else {
						$SetAth++;
						$SetOpp++;
					}
				}
			}

			if($SetAth==$SetOpp && !empty($MyRow->TbString) && !empty($MyRow->OppTbString) && strlen(trim($MyRow->TbString))==strlen(trim($MyRow->OppTbString))) {
				//Verifico le stringhe CASE INSENSITIVE - in questo momento me ne frego degli "*"
				$AthTbValue=ValutaArrowString($MyRow->TbString);
				$OppTbValue=ValutaArrowString($MyRow->OppTbString);
				if($AthTbValue < $OppTbValue) {
					 $Winner = $MyRow->OppMatchNo;	//OppTbString è maggiore di TbString --> il secondo vince
					 $WinnerId = $MyRow->OppMatchNo;	//OppTbString è maggiore di TbString --> il secondo vince
					 $SetOpp++;
				} elseif($AthTbValue > $OppTbValue) {
					$Winner = $MyRow->MatchNo;	//TbString è maggiore di OppTbString --> il primo vince
					$WinnerId = $MyRow->MatchNo;	//TbString è maggiore di OppTbString --> il primo vince
					$SetAth++;
				} elseif ($AthTbValue>0 and $OppTbValue>0) {
					//le stringhe CASE INSENSITIVE sono uguali
					if(!ctype_upper(trim($MyRow->OppTbString)) and ctype_upper(trim($MyRow->TbString))) {
						// Verifico gli "*" e lo star è nella stringa del secondo (è maggiore)
						$Winner = $MyRow->OppMatchNo;
						$WinnerId = $MyRow->OppMatchNo;
						$SetOpp++;
					} elseif(ctype_upper(trim($MyRow->OppTbString)) and !ctype_upper(trim($MyRow->TbString))) {
						// Verifico gli "*" e lo star è nella stringa del primo (è maggiore)
						$Winner = $MyRow->MatchNo;
						$WinnerId = $MyRow->MatchNo;
						$SetAth++;
					}
				}
			} elseif($SetAth>=$obj->winAt) {
				$Winner = $MyRow->MatchNo;
			} elseif($SetOpp>=$obj->winAt) {
				$Winner = $MyRow->OppMatchNo;
			}

			$query="UPDATE "
				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
				. "SET "
				. "{$TablePrefix}WinLose=" . ($Winner==$MyRow->MatchNo ? '1' : '0') . ", "
				. "{$TablePrefix}Score=" . $Score . ", "
				. "{$TablePrefix}SetScore=" . $SetAth . ", "
				. "{$TablePrefix}SetPoints=" . StrSafe_DB(implode($SetPointsAth, '|')) . ", "
				. "{$TablePrefix}WinnerSet=" . $SetAthWin . ", "
				. "{$TablePrefix}Tie=" . ($WinnerId == $MyRow->MatchNo ? '1':'0') . ", "
				. "{$TablePrefix}DateTime={$TablePrefix}DateTime "
				. "WHERE "
				. "{$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
				. "AND {$TablePrefix}MatchNo=". StrSafe_DB($MyRow->MatchNo) . " "
				. "AND {$TablePrefix}Tournament=". StrSafe_DB($_SESSION['TourId']);
// 			debug_svela($Winner==$MyRow->MatchNo, true);
			safe_w_sql($query);
			$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());

			$query="UPDATE "
				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
				. "SET "
				. "{$TablePrefix}WinLose=" . ($Winner==$MyRow->OppMatchNo ? '1' : '0') . ", "
				. "{$TablePrefix}Score=" . $OppScore . ", "
				. "{$TablePrefix}SetScore=" . $SetOpp . ", "
				. "{$TablePrefix}SetPoints=" . StrSafe_DB(implode($SetPointsOpp, '|')) . ", "
				. "{$TablePrefix}WinnerSet=" . $SetOppWin . ", "
				. "{$TablePrefix}Tie=" . ($WinnerId == $MyRow->OppMatchNo ? '1':'0') . ", "
				. "{$TablePrefix}DateTime={$TablePrefix}DateTime "
				. "WHERE "
				. "{$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
				. "AND {$TablePrefix}MatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " "
				. "AND {$TablePrefix}Tournament=". StrSafe_DB($_SESSION['TourId']);
			safe_w_sql($query);
			$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());

			if($SetAth >= $obj->winAt || $SetOpp >= $obj->winAt) {
				$MatchFinished=true;
			}
		}

		if($MatchUpdated) {
			$query="UPDATE "
				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
				. "SET "
				. "{$TablePrefix}DateTime=now() "
				. "WHERE "
				. " {$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
				. "AND {$TablePrefix}MatchNo in (". StrSafe_DB($MyRow->MatchNo) . ',' . StrSafe_DB($MyRow->OppMatchNo) . ") "
				. "AND {$TablePrefix}Tournament=". StrSafe_DB($_SESSION['TourId']);
			safe_w_sql($query);
		}
		//Serve per ricalcolare le ranking, solo medaglie

		if($MatchNo < 4 and $MatchFinished) {
			if($TeamEvent) {
				move2NextPhaseTeam(NULL, $EvCode, $MatchNo);
			} else {
				move2NextPhase(NULL, $EvCode, $MatchNo);
			}
		}
	}
	return $MatchFinished;
}
?>