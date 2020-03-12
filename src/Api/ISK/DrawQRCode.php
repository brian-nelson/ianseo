<?php

/*

The name of this function in the other modules MUST BE
DrawQRCode_[Api Directory]

*/
function DrawQRCode_ISK_Lite(&$pdf, $X, $Y, $Session=0, $Distance=0, $Target='', $Phase='', $Stage='Q') {
	DrawQRCode_ISK($pdf, $X, $Y, $Session, $Distance, $Target, $Phase, $Stage);
}

function DrawQRCode_ISK(&$pdf, $X, $Y, $Session=0, $Distance=0, $Target='', $Phase='', $Stage='Q') {
	global $CFG;
	$style = array(
			'border' => 2,
			'vpadding' => 'auto',
			'hpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => array(255,255,255), //array(255,255,255)
			'module_width' => 1, // width of a single module in points
			'module_height' => 1 // height of a single module in points
	);

	$Width=25;
	$Height=25;
	if(!$X) $X=($pdf->getPageWidth()-$Width)/2;
	if(!$Y) $Y=($pdf->getPageHeight()-$Height-3)/2;

	$pdf->SetFontSize(10);

	require_once('Common/Lib/Fun_Modules.php');

	$Opts=array();
	$Opts['u']=getModuleParameter('ISK', 'ServerUrl').$CFG->ROOT_DIR; // .'Api/ISK-Lite/';
	$Opts['c']=$_SESSION['TourCode'];
	if($Session) $Opts['s']=$Session;
	if($Distance || ($Stage=="MI" || $Stage=="MT")) $Opts['d']=(int)$Distance;
	if($Target || ($Stage=="MI" || $Stage=="MT")) $Opts['t']=$Target;
	if($Phase) $Opts['p']=$Phase;
	if($Stage) $Opts['st']=$Stage;



	$text=json_encode($Opts);

	$Oldx=$pdf->getX();
	$Oldy=$pdf->getY();
	$pdf->write2DBarcode($text, 'QRCODE,L', $X, $Y, $Width, $Height, $style, 'N');
//	$pdf->setXY($X, $Y+$Height);
//	$pdf->Cell($Width, 0, 'ISK-Lite', '', '', 'C');
	$pdf->setXY($Oldx, $Oldy);
}

