<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Fun_CONI.local.inc.php');


$p=$_REQUEST['Phase']==1 ? get_text('FirstPhase','Tournament') : get_text('SecondPhase','Tournament');

if(!isset($isCompleteResultBook))
	$pdf = new OrisPDF('C73A', get_text('GroupMatches','Tournament') . ' - ' . $p);
else
	$pdf->setOrisCode('', 'Group Matches');


$MyQuery = "SELECT EvCode AS EventCode, EvEventName AS EventName "
	. "FROM Events "
	. "WHERE EvTeamEvent != 0 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
	. "ORDER BY EvProgr ASC, EvEventName ASC";
$RsEv=safe_r_sql($MyQuery);


if(safe_num_rows($RsEv)>0)
{
	while($MyRowEv=safe_fetch($RsEv))
	{
		$Rs=getMatchesPhase1($MyRowEv->EventCode,0,$_REQUEST['Phase']);
		if(safe_num_rows($Rs)>0)
		{
			$arrTitles=array("Girone","Match","Piazzola", "Team", "Score#","", "Punti#", " ");
			$arrSizes=array(15,15,15,90,15,15,15,10);

			$pdf->SetDataHeader($arrTitles, $arrSizes);
			$pdf->setEvent(get_text($MyRowEv->EventName,'','',true));
			$pdf->setPhase(get_text('GroupMatches','Tournament') . ' - ' . $p);
			
			$pdf->AddPage();
			$OldGroup = '--';
			while($MyRow=safe_fetch($Rs))
			{
				if($OldGroup != $MyRow->Group && $OldGroup != '--')
				{
					$pdf->SetDrawColor(0x80,0x80,0x80);
					$pdf->Line(10,$pdf->lastY,200,$pdf->lastY);
					$pdf->SetDefaultColor();
					$pdf->lastY += 3;
				}
				if($OldGroup != $MyRow->Group)
				{
					$pdf->SetY($pdf->lastY);
					$pdf->SetFont('', 'B', '14');
					$pdf->Cell(15,5," " . chr( ($_REQUEST['Phase']==1 ? 64 : 68)+$MyRow->Group),0,1,'L');
					$OldGroup = $MyRow->Group;
				}

				//Valuto il TieBreak
				$TmpTie1 = '';
				if(strlen(trim($MyRow->Tiebreak1)) > 0)
				{
					for($countArr=0; $countArr<strlen(trim($MyRow->Tiebreak1)); $countArr = $countArr+3)
						$TmpTie1 .= ValutaArrowString(substr(trim($MyRow->Tiebreak1),$countArr,3)) . ",";
					$TmpTie1 = substr($TmpTie1,0,-1);
				}
				else if($MyRow->Tie1==1)
					$TmpTie1 = '*';
				else if($MyRow->Tie1==2)
					$TmpTie1 = '-Bye-';
				$TmpTie2 = '';
				if(strlen(trim($MyRow->Tiebreak2)) > 0)
				{
					for($countArr=0; $countArr<strlen(trim($MyRow->Tiebreak2)); $countArr = $countArr+3)
						$TmpTie2 .= ValutaArrowString(substr(trim($MyRow->Tiebreak2),$countArr,3)) . ",";
					$TmpTie2 = substr($TmpTie2,0,-1);
				}
				else if($MyRow->Tie2==1)
					$TmpTie2 = '*';
				else if($MyRow->Tie2==2)
					$TmpTie2 = '-Bye-';

				$pdf->printDataRow(array(
					'',
					$MyRow->Round,
					$MyRow->TargetNo1,
					($TmpTie2 == '-Bye-' ? $TmpTie2 : $MyRow->CountryName1 . ($MyRow->SubTeamCode1>1 ? " (" . $MyRow->SubTeamCode1 . ")" : "")),
					($MyRow->Score1>0 ? $MyRow->SetScore1 . ' (' .$MyRow->Score1 . ")#" : ''),
					($TmpTie1 != '-Bye-' ? $TmpTie1 : ''),
					($MyRow->Point1>0 ? $MyRow->Point1 . "#" : '-#')
				));
				$pdf->printDataRow(array(
					'',
					'',
					$MyRow->TargetNo2,
					($TmpTie1 == '-Bye-' ? $TmpTie1 : $MyRow->CountryName2 . ($MyRow->SubTeamCode2>1 ? " (" . $MyRow->SubTeamCode2 . ")" : "")),
					($MyRow->Score2>0 ? $MyRow->SetScore2 . ' (' .$MyRow->Score2 . ")#" : ''),
					($TmpTie2 != '-Bye-' ? $TmpTie2 : ''),
					($MyRow->Point2>0 ? $MyRow->Point2 . "#" : '-#')
				));
				$pdf->lastY += 2;
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