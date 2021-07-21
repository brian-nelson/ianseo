<?php
checkACL(array(AclIndividuals, AclTeams), AclReadOnly);

$pdf = new IanseoPdf('',false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(0,0,0);
$pdf->SetAutoPageBreak(false, 0);
$pdf->SetFont('','',220);
$pdf->SetAuthor('https://www.ianseo.net');
$pdf->SetCreator('Software Design by Ianseo');
$pdf->SetTitle('IANSEO - Integrated Result System');
$pdf->SetSubject('Final Athlete Name');
$pdf->SetTextColor(0x00, 0x00, 0x00);
$pdf->SetDrawColor(0x33, 0x33, 0x33);
$pdf->SetFillColor(0xE8,0xE8,0xE8);

$rep=array(
	'ASD' => '',
	'A.S.D.' => '',
	);

$n=0;
//error_reporting(E_ALL);
$EnIds=array();

while($MyRow=safe_fetch($Rs)) {
	if(isset($MyRow->EnId)) {
		if(in_array($MyRow->EnId, $EnIds)) {
			continue;
		}
		$EnIds[]=$MyRow->EnId;
	}
	$match2=($n%2 == 0);
	$n++;
	if($match2) {
		$pdf->AddPage();
		$pdf->Line(5,($pdf->getPageHeight()/2),15,($pdf->getPageHeight()/2));
		$pdf->Line($pdf->getPageWidth()-15,($pdf->getPageHeight()/2),$pdf->getPageWidth()-5,($pdf->getPageHeight()/2));
	}


	if($match2) {
        $pdf->SetXY(10, 10);
    } else {
        $pdf->SetXY(10, ($pdf->getPageHeight() / 2) + 10);
    }

	$pdf->SetFont('','',220);
	$pdf->Cell(($pdf->getPageWidth()-20),(($pdf->getPageHeight()/2)-20),str_replace(array_keys($rep), array_values($rep), $MyRow->Athlete),0,1,'L',0);

	if(!empty($_REQUEST['TargetAssign'])) {
		// PArte di riconoscimento EVENTO e Paglione
		$tmpY = ($match2 ? $pdf->getPageHeight()/2 : $pdf->getPageHeight()) - 10;
		$pdf->SetFont('','',10);

		$pdf->SetXY($pdf->getPageWidth()-20,$tmpY);
		$pdf->Cell(10,5, "G." . $MyRow->sGo,1,0,'C',0);
		$pdf->SetX($pdf->getX()-20);
		$pdf->Cell(10,5, "B." . $MyRow->sBr,1,0,'C',0);
		for($i=2; $i<=bitwisePhaseId($MyRow->EvFinalFirstPhase);$i=$i*2) {
			$pdf->SetX($pdf->getX()-20);
			$pdf->Cell(10,5, namePhase($MyRow->EvFinalFirstPhase,$i) . '.' . $MyRow->{'s' . $i},1,0,'C',0);
		}
		$pdf->SetX($pdf->getX()-20);
		$pdf->SetFont('','B',10);
		$pdf->Cell(10,5, $MyRow->EvCode,1,0,'C',0);
	}
}
$pdf->Output();

exit();
?>
