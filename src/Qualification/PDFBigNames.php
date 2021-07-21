<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclQualification, AclReadOnly);
require_once('Common/pdf/IanseoPdf.php');

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
$pdf->setCellMargins(0, 0, 0, 0);
$pdf->SetCellPadding(0);

$Height=array(0, 0, 0);
$Height[1]=$pdf->getPageHeight()/3;
$Height[2]=$Height[1]*2;
$Width=$pdf->getPageWidth()-65;
$CellHeight=$Height[1]-10; // 5 top and bottom

$fontname=TCPDF_FONTS::addTTFfont($CFG->DOCUMENT_PATH.'Common/tcpdf/fonts/ariblk.ttf');
$pdf->SetFont($fontname);

$Filter='';
if(!empty($_REQUEST['x_Session'])) $Filter.=" and QuSession={$_REQUEST['x_Session']}";
if(!empty($_REQUEST['x_From'])) $Filter.=" and substr(QuTargetNo, 2, 3)>='".str_pad($_REQUEST['x_From'], 3, '0', STR_PAD_LEFT)."'";
if(!empty($_REQUEST['x_To'])) $Filter.=" and substr(QuTargetNo, 2, 3)<='".str_pad($_REQUEST['x_To'], 3, '0', STR_PAD_LEFT)."'";
$Rs=safe_r_SQL("select EnName, EnFirstName, QuTargetNo
	from Entries
	inner join Qualifications on EnId=QuId $Filter where EnTournament={$_SESSION['TourId']}
	order by QuTargetNo");

//error_reporting(E_ALL);

$n=0;
while($MyRow=safe_fetch($Rs)) {
	if($n==0) {
		$pdf->AddPage();
		$pdf->Line(5, $Height[1], 15, $Height[1]);
		$pdf->Line($pdf->getPageWidth()-15, $Height[1], $pdf->getPageWidth()-5, $Height[1]);
		$pdf->Line(5, $Height[2], 15, $Height[2]);
		$pdf->Line($pdf->getPageWidth()-15, $Height[2], $pdf->getPageWidth()-5, $Height[2]);
	}

	$pdf->SetXY(10, $Height[$n]+5);

	$pdf->setColor('text', 0);
	$pdf->SetFont('','',136);
	$pdf->Cell($Width, $CellHeight, $MyRow->EnFirstName, 0, 0, 'L');

	$pdf->setx($pdf->GetX()+5);
	$pdf->SetFont('','',20);
	$pdf->Cell(25, $CellHeight-8, $MyRow->EnName, 0, 0, 'L', 0, '', true, false, 'T', 'B');

	$pdf->setx($pdf->GetX()+5);
	$pdf->setColor('text', 128);
	$pdf->Cell(10, $CellHeight-8, ltrim(substr($MyRow->QuTargetNo, 1), '0'), 0, 0, 'L', 0, '', true, false, 'T', 'B');

	if(!empty($_REQUEST['TargetAssign'])) {
		// PArte di riconoscimento EVENTO e Paglione
		$tmpY = ($match2 ? $pdf->getPageHeight()/2 : $pdf->getPageHeight()) - 10;
		$pdf->SetFont('','',10);

		$pdf->SetXY($pdf->getPageWidth()-20,$tmpY);
		$pdf->Cell(10,5, "G." . $MyRow->sGo,1,0,'C',0);
		$pdf->SetX($pdf->getX()-20);
		$pdf->Cell(10,5, "B." . $MyRow->sBr,1,0,'C',0);
		for($i=2; $i<=$MyRow->EvFinalFirstPhase;$i=$i*2)
		{
			$pdf->SetX($pdf->getX()-20);
			$pdf->Cell(10,5, namePhase($MyRow->EvFinalFirstPhase,$i) . '.' . $MyRow->{'s' . $i},1,0,'C',0);
		}
		$pdf->SetX($pdf->getX()-20);
		$pdf->SetFont('','B',10);
		$pdf->Cell(10,5, $MyRow->EvCode,1,0,'C',0);
	}
	$n=(++$n % 3);
}
$pdf->Output();

exit();
