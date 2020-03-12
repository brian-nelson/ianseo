<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Fun_CONI.local.inc.php');


if(!isset($isCompleteResultBook))
	$pdf = new OrisPDF('C73A', get_text('Rankings') . ' - ' . get_text('FirstPhase','Tournament'));
else
	$pdf->setOrisCode('', get_text('Rankings') . ' - ' . get_text('FirstPhase','Tournament'));


$MyQuery = "SELECT EvCode AS EventCode, EvEventName AS EventName "
	. "FROM Events "
	. "WHERE EvTeamEvent != 0 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
	. "ORDER BY EvProgr ASC, EvEventName ASC";
$RsEv=safe_r_sql($MyQuery);

if(safe_num_rows($RsEv)>0)
{
	while($MyRowEv=safe_fetch($RsEv))
	{
		$MyQuery = "SELECT CaTeam,CaSubTeam,CaEventCode,CaMatchNo, CoCode,CoName,CGGroup,"
			. "SUM(CaSPoints) AS Points,SUM(CaSScore) AS Score,SUM(CaSSetScore) AS SetScore,CaTiebreak, CaRank "
			. "FROM CasTeam "
			. "INNER JOIN Countries ON CaTeam=CoId "
			. "INNER JOIN CasGrid ON CaPhase=CGPhase AND (CaMatchNo=CGMatchNo1 OR CaMatchNo=CGMatchNo2) "
			. "INNER JOIN CasScore ON CaTournament=CaSTournament AND CaPhase=CaSPhase AND CaMatchNo=CaSMatchNo AND  CaEventCode=CaSEventCode AND CGRound=CaSRound "
			. "WHERE CaEventCode=" . StrSafe_DB($MyRowEv->EventCode) . " AND CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaPhase=1 "
			. "GROUP BY CaPhase,CaEventCode,CGGroup,CaTeam,CaSubTeam,CaMatchNo,CoCode,CoName,CaTiebreak,CaRank "
			. "ORDER BY CaEventCode ASC, CGGroup ASC, SUM(CaSPoints) DESC, SUM(CaSScore) DESC, CaRank ASC ";
		$Rs=safe_r_sql($MyQuery);

		if(safe_num_rows($Rs)>0)
		{
			$arrTitles=array("Rank", "Team",  "Punti totali#", "Score#", " ");
			$arrSizes=array(15,125,20,20);
			$pdf->setEvent(get_text($MyRowEv->EventName,'','',true));
			$pdf->setPhase(get_text('Rankings') . ' - ' . get_text('FirstPhase','Tournament'));
			
			$pdf->SetDataHeader(array(), array());
			$pdf->AddPage();
			$pdf->SetDataHeader($arrTitles, $arrSizes);
			$OldGroup = '--';
			$myRank = 0;
			$myPos = 0;
			$OldPoints = 0;
			$OldScore = 0;
			$OldSetScore=0;
			$OldTie = 0;

			while($MyRow=safe_fetch($Rs))
			{
				if($OldGroup != $MyRow->CGGroup && $OldGroup != '--')
					$pdf->lastY += 5;
				if($OldGroup != $MyRow->CGGroup)
				{
					$pdf->SetY($pdf->lastY);
					$pdf->SetFont('', 'B', '12');
					$pdf->Cell(35,5,"Girone " . chr(64+$MyRow->CGGroup),0,1,'L');
					$pdf->lastY += 7;
					$pdf->printHeader(OrisPDF::leftMargin, $pdf->lastY);
					$OldGroup = $MyRow->CGGroup;
					$myRank = 0;
					$myPos = 0;
					$OldPoints = 0;
					$OldScore = 0;
					$OldSetScore = 0;
					$OldTie = 0;
				}

				//Calcolo della Rank;
				$myPos++;
				if(! ($MyRow->Points == $OldPoints && $MyRow->Score == $OldScore && $MyRow->SetScore==$OldSetScore && $MyRow->CaTiebreak == $OldTie))
					$myRank=$myPos;

				//Valuto il TieBreak
				$TmpTie = '';
				if(strlen(trim($MyRow->CaTiebreak)) > 0)
				{
					for($countArr=0; $countArr<strlen(trim($MyRow->CaTiebreak)); $countArr = $countArr+3)
						$TmpTie .= ValutaArrowString(substr(trim($MyRow->CaTiebreak),$countArr,3)) . ",";
					$TmpTie = substr($TmpTie,0,-1);
				}

				$pdf->printDataRow(array(
					($MyRow->CaRank == 0 ? $myRank : $MyRow->CaRank),

					$MyRow->CoName . ($MyRow->CaSubTeam>1 ? " (" . $MyRow->CaSubTeam . ")" : ""),

					$MyRow->Points . "#",
					$MyRow->Score . "#",
					$TmpTie
				));
				$pdf->lastY += 2;
				$OldPoints = $MyRow->Points;
				$OldScore = $MyRow->Score;
				$OldSetScore = $MyRow->SetScore;
				$OldTie = $MyRow->CaTiebreak;
			}
		}
	}
}

if(!isset($isCompleteResultBook))
{
	if(isset($_REQUEST['ToFitarco']))
	{
		$Dest='D';
		if (isset($_REQUEST['Dest']))
			$Dest=$_REQUEST['Dest'];
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
	}
	else
		$pdf->Output();
}
?>