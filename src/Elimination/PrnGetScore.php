<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/LabelPDF.inc.php');
require_once('Common/Fun_Sessions.inc.php');

$Indices=array('bis','ter','quat', 'quin', 'sex', 'sept', 'oct');

if(CheckTourSession())
{

	$pdf = new LabelPDF();

	$ath4target=0;
	$ses=GetSessions('Q');
	foreach ($ses as $s)
	{
		$ath4target=max($ath4target, $s->SesAth4Target);
	}

	$MyQuery = "SELECT EnName AS Name, EnFirstName AS FirstName, SUBSTRING(ElTargetNo,1,1) AS Session, SUBSTRING(ElTargetNo,2,2) AS TargetNo,  SUBSTRING(ElTargetNo,-1,1) AS BackNo "
		. " FROM Eliminations AS q  "
		. " INNER JOIN Entries AS e ON q.ElId=e.EnId AND EnAthlete=1 "
		. " INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
		. " LEFT JOIN Session ON ElSession=SesOrder AND ElTournament=SesTournament AND SesType='E' "
		. " WHERE EnTournament =  " . StrSafe_DB($_SESSION['TourId']) . ' '
		. (!(empty($_REQUEST['x_From']) && empty($_REQUEST['x_To'])) ? " AND ElTargetNo>='" . str_pad($_REQUEST['x_From'],'3','0',STR_PAD_LEFT) . "A' AND ElTargetNo<='" . str_pad($_REQUEST['x_To'],'3','0',STR_PAD_LEFT) . "Z' AND ElElimPhase= " . ($_REQUEST['x_Session']) . " " : "")
		. (!empty($_REQUEST['x_ElimSession']) ? " AND ElSession=". $_REQUEST['x_ElimSession'] . " " : "")
		. ' ORDER BY ElSession, SesOrder, ElElimPhase, ElTargetNo ASC, EnFirstName, EnName, CoCode';

	//echo $MyQuery;exit;

	$Rs=safe_r_sql($MyQuery);
	if($Rs)
	{
		$Etichetta=-1;
		$CurrentTarget=0;
		$NewTarget=true;
		$CurHeight=0;

		$BlockHeight=($pdf->getPageHeight()-30)/4;
		$CellHeight=$BlockHeight/($ath4target+1);

		while($MyRow=safe_fetch($Rs)) {

			$NumEnd=($MyRow->Session == 0 ? 12 : 8);
			$TargetToPrint=$MyRow->TargetNo;
			$NumTarget= intval($MyRow->TargetNo);
			if($NumTarget>$NumEnd) {
				$NumTarget = (($NumTarget-1) % ($NumEnd)) + 1;
			}
			if($MyRow->TargetNo and $NumTarget!=intval($MyRow->TargetNo)) {
				$TargetToPrint = $NumTarget . '-' . $Indices[ceil(intval($MyRow->TargetNo)/($NumEnd))-2];
			}

			if($CurrentTarget != $MyRow->Session . $MyRow->TargetNo) {
				$Etichetta = ++$Etichetta % 12;
				$NewTarget=true;
			}
			$CurrentTarget = $MyRow->Session . $MyRow->TargetNo;

			if($Etichetta==0 && $NewTarget)
				$pdf->AddPage();

			if($NewTarget) {
				$aths=$ath4target;
				$CurHeight = $CellHeight;

				$pdf->SetXY((($Etichetta % 3) * 70)+5,(intval($Etichetta / 3) * ($BlockHeight+5) +5));
				$pdf->SetFont($pdf->FontStd,'B',25);
				$pdf->Cell(60,$CurHeight,$TargetToPrint,1,0,'C',1);

				$pdf->SetFont($pdf->FontStd,'B',20);
				//for($i=1; $i<=$MyRow->{"ToAth4Target" . $MyRow->Session}; $i++)
				for($i=1; $i<=$aths; $i++) {
					$pdf->SetXY((($Etichetta % 3) * 70)+5,(intval($Etichetta / 3) * ($BlockHeight+5)+5)+($i*$CurHeight));
					$pdf->Cell(20, $CurHeight, chr(ord("A")-1+$i), '1', 0, 'C', 0, '', 1, '', '', 'T' );
					$pdf->Cell(40, $CurHeight, '', '1', 0, 'C');
				}
				$NewTarget=false;
			}
			$pdf->SetXY((($Etichetta % 3) * 70)+5,(intval($Etichetta / 3) * ($BlockHeight+5)+5)+((ord($MyRow->BackNo)-ord("A")+1)*$CurHeight)+0.6*$CurHeight);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell(20,0.4*$CurHeight,$MyRow->FirstName,0,0,'C',0);
		}
		safe_free_result($Rs);
	}
	$pdf->Output();
}
?>
