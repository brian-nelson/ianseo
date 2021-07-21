<?php
require_once(dirname(dirname(__FILE__)).'/config.php');
require_once('Common/Lib/Fun_Modules.php');

// Include the main TCPDF library (search for installation path).
require_once('Common/pdf/ResultPDF.inc.php');

CheckTourSession(true);
checkACL(AclISKServer, AclReadWrite);

// create new PDF document
$pdf = new ResultPDF('QrCode');//TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set style for barcode
$style = array(
		'border' => 2,
		'vpadding' => 'auto',
		'hpadding' => 'auto',
		'fgcolor' => array(0,0,0),
		'bgcolor' => false, //array(255,255,255)
		'module_width' => 1, // width of a single module in points
		'module_height' => 1 // height of a single module in points
);

$Opts=array();
$Opts['u']=getModuleParameter('ISK', 'ServerUrl').$CFG->ROOT_DIR;
$Opts['c']=$_SESSION['TourCode'];
$tmpPin = getModuleParameter('ISK', 'ServerUrlPin');
if(!empty($tmpPin)) {
	$Opts['c'] .= '|'.$tmpPin;
}

$Code=json_encode($Opts);

$Y=35;
$VBlock=($pdf->getPageHeight()-$Y-30);
$Size=min(60, $VBlock-12);
$X=($pdf->getPageWidth()-$Size)/2;

$ActY=$Y + ($VBlock - $Size)/2;
$pdf->SetFontSize(12);

$pdf->SetY($ActY-6);
$pdf->SetFont('', 'B', 18);
$pdf->Cell(0, 6, 'ISK-Pro Setup', 0, 1, 'C');
$pdf->SetFont('', '', 12);
$pdf->Cell(0, 6, $Code, 0, 1, 'C');
$pdf->write2DBarcode($Code, 'QRCODE,L', $X, $ActY+12, $Size, $Size, $style, 'N');
$ActY+= $VBlock;

// -------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('QrCode.pdf', 'I');

