<?php

/*

The name of this function in the other modules MUST BE
DrawQRCode_[Api Directory]

*/
function DrawQRCode_ISK_Lite(&$pdf, $X, $Y, $Session=0, $Distance=0, $Target='', $Phase='', $Stage='Q', $Individual=false, $DistName='') {
	DrawQRCode_ISK($pdf, $X, $Y, $Session, $Distance, $Target, $Phase, $Stage, $Individual, $DistName);
}

function DrawQRCode_ISK(TCPDF &$pdf, $X, $Y, $Session=0, $Distance=0, $Target='', $Phase='', $Stage='Q', $Individual=false, $DistName='') {
	global $CFG;
	static 	$OptsU, $OptsC;


	$Width=25;
	$Height=25;
	if(!$X) {
		$X=($pdf->getPageWidth()-$Width)/2;
	}
	if(!$Y) {
		$Y=($pdf->getPageHeight()-$Height-3)/2;
	}

	$pdf->SetFontSize(10);

	require_once('Common/Lib/Fun_Modules.php');

	if(is_null($OptsU)) {
		$OptsU=getModuleParameter('ISK', 'ServerUrl').$CFG->ROOT_DIR; // .'Api/ISK-Lite/';
		$OptsC=$_SESSION['TourCode'];
	    $tmpPin = getModuleParameter('ISK', 'ServerUrlPin');
	    if(!empty($tmpPin)) {
		    $OptsC .= '|'.$tmpPin;
	    }
	}

	$Opts=array('u' => $OptsU, 'c' => $OptsC);
	if($Session) $Opts['s']=$Session;
	if($Distance || ($Stage=="MI" || $Stage=="MT")) $Opts['d']=(int)$Distance;
    if($Stage=="MI" || $Stage=="MT") {
        $Opts['t']=$Target;
    } else if($Target) {
        $Opts['t'] = str_pad($Target, 3, '0', STR_PAD_LEFT);
    }
	if($Phase) $Opts['p']=$Phase;
	if($Stage) $Opts['st']=$Stage;

	$text=json_encode($Opts);

	$Oldx=$pdf->getX();
	$Oldy=$pdf->getY();

	require_once('Common/tcpdf/tcpdf_barcodes_2d.php');
	// create new barcode object
	$barcodeobj = new TCPDF2DBarcode($text, 'QRCODE,L');
	$img=$barcodeobj->getBarcodePngData();

	if($Individual and $Stage=='Q' and $Distance) {
		// draws a white background square
		$pdf->Rect($X, $Y, $Width, $Height, 'FD', array('all'=>array('color'=>100)), array(255));
		$pdf->Image('@'.$img, $X+2.5, $Y+2.5, $Width-5, $Height-5, 'PNG');

		$pdf->SetFont($pdf->FontStd,'',8);
		$pdf->StartTransform();
		// Rotate 20 degrees counter-clockwise centered by (70,110) which is the lower left corner of the rectangle
		$pdf->setXY($X+$Width, $Y+$Height);
		$pdf->Rotate(90, $X+$Width, $Y+$Height);
		$pdf->MultiCell($Width, $Height, get_text('FlashCodeDistance','Api', ($DistName? $DistName : get_text('DistanceNum','Api', $Distance))), '', 'C');
		// Stop Transformation
		$pdf->StopTransform();
		//$pdf->
		//$pdf->MultiCell($Text, 4, , '','L', false,1, '', '', true, 0, true);
	} else {
		// draws a white background square
		$pdf->Rect($X, $Y, $Width, $Height, 'FD', array('all'=>array('color'=>100)), array(255));
		$pdf->Image('@'.$img, $X+2.5, $Y+2.5, $Width-5, $Height-5, 'PNG');
	}

	$pdf->setXY($Oldx, $Oldy);
}

