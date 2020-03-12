<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Fun_CONI.local.inc.php');

if(!isset($isCompleteResultBook))
	$pdf = new OrisPDF('C73A', get_text('Rankings') . ' - ' . get_text('SecondPhase','Tournament'));
else
	$pdf->setOrisCode('', get_text('Rankings') . ' - ' . get_text('SecondPhase','Tournament'));

$MyQuery = "SELECT EvCode AS EventCode, EvEventName AS EventName "
	. "FROM Events "
	. "WHERE EvTeamEvent != 0 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
	. "ORDER BY EvProgr ASC, EvEventName ASC";
$RsEv=safe_r_sql($MyQuery);

if(safe_num_rows($RsEv)>0)
{
	while($MyRowEv=safe_fetch($RsEv))
	{
		$rows=rankPhase2($MyRowEv->EventCode,array(1,2,3,4));

		if (count($rows)>0)
		{
			$arrTitles=array("Rank", "Team",  "Punti totali#", "Score#", " ");
			$arrSizes=array(15,125,20,20);
			$pdf->setEvent(get_text($MyRowEv->EventName,'','',true));
			$pdf->setPhase(get_text('Rankings') . ' - ' . get_text('SecondPhase','Tournament'));

			$pdf->SetDataHeader(array(), array());
			$pdf->AddPage();
			$pdf->SetDataHeader($arrTitles, $arrSizes);
			$OldGroup = '--';

			foreach ($rows as $r)
			{
				if($OldGroup != $r->Group && $OldGroup != '--')
					$pdf->lastY += 5;
				if($OldGroup != $r->Group)
				{
					$pdf->SetY($pdf->lastY);
					$pdf->SetFont('', 'B', '12');
					$pdf->Cell(35,5,"Girone " . chr(68+$r->Group),0,1,'L');
					$pdf->lastY += 7;
					$pdf->printHeader(OrisPDF::leftMargin, $pdf->lastY);
					$OldGroup = $r->Group;
				}

				$pdf->printDataRow(array(
					$r->Rank,
					$r->Country,
					$r->Points,
					$r->Score,
					$r->Tie
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