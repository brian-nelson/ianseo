<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/LabelPDF.inc.php');

require_once('CommonCard.php');

$Rs=safe_r_sql($MyQuery);
if (!safe_num_rows($Rs)) {
	include('Common/Templates/head-popup.php');
	echo '<table height="'.($_SESSION['WINHEIGHT']-50).'" width="100%"><tr><td>';
	echo '<div align="center">' . get_text('BadgeNoData', 'Tournament') . '';
	echo '<br/><br/><input type="button" onclick="window.close();" value="' . get_text('Close') . '">';
	echo '</td></tr></table>';
	include('Common/Templates/tail-popup.php');
	die();
}

$pdf=new LabelPDF();
$pdf->SetAutoPageBreak(false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$AccW=(($pdf->getPageWidth()-13)/2)-10;
$AccH=(($pdf->getPageHeight()-50)/3)-10;
$XOffset=array(0, $AccW+10);
$YOffset=array(0, 0, $AccH+10, $AccH+10, $AccH*2 + 20, $AccH*2 +20);
$CardsPerWidth=2;
$CardsPerPage=6;
$FlagHeight=12;


$cntPass=0;
$ImgSxSize= NULL;
$ImgDxSize= NULL;
$ImgLowSize= NULL;
$ImgLWidth=0;
$ImgLWidth=0;
$ImgRHeight=0;
$ImgRWidth=0;

if($pdf->ToPaths['ToLeft']) {
	$tmp=getimagesize($pdf->ToPaths['ToLeft']);
// 	$ImgLWidth=(($tmp[0]/$tmp[1]<= 2*$AccW/$AccH) ? $AccH*0.2 : 0.4*$AccW);
	$ImgLHeight=12;
	$ImgLWidth=$ImgLHeight*$tmp[0]/$tmp[1];
}

if($pdf->ToPaths['ToRight']) {
	$tmp=getimagesize($pdf->ToPaths['ToRight']);
// 	$ImgRWidth=(($tmp[0]/$tmp[1]<= 2*$AccW/$AccH) ? $AccH*0.2 : 0.4*$AccW);
	$ImgRHeight=12;
	$ImgRWidth=$ImgRHeight*$tmp[0]/$tmp[1];
}

if($pdf->ToPaths['ToBottom']) {
// if(strlen($pdf->imgB) > 0) {
// 	$im = imagecreatefromstring($pdf->imgB);
// 	if ($im !== false)
// 	{
// 		$ImgLowSize = array(imagesx($im),imagesy($im));
// 		imagedestroy($im);
// 	}
	$tmp=getimagesize($pdf->ToPaths['ToBottom']);
	$ImgBWidth=(($tmp[0]/$tmp[1]<= 2*$AccW/$AccH) ? $AccH*0.2 : 0.4*$AccW);
}

while ($MyRow=safe_fetch($Rs)) {
	$pdf->SetDefaultColor();
	$PosX = $XOffset[$cntPass % $CardsPerWidth]+5;
	$PosY = $YOffset[$cntPass % $CardsPerPage]+5;
	$AccColor = array(255,255,255);
	if (!is_null($MyRow->AcColor))
		$AccColor = array(base_convert(substr($MyRow->AcColor,0,2), 16, 10),base_convert(substr($MyRow->AcColor,2,2), 16, 10),base_convert(substr($MyRow->AcColor,4,2), 16, 10));

	//Every X Accreditation I change page
	if($cntPass % $CardsPerPage == 0) $pdf->AddPage();

	// Image Left
	if($pdf->ToPaths['ToLeft'])	{
		$pdf->Image($pdf->ToPaths['ToLeft'], $PosX+2, $PosY + 2, 0, $ImgLHeight);
	}

	// Image Right
	if($pdf->ToPaths['ToRight'])	{
		$pdf->Image($pdf->ToPaths['ToRight'], $PosX + $AccW - $ImgRWidth -2, $PosY+2, 0, $ImgRHeight);
	}

	// Competition
	$pdf->SetXY($PosX + $ImgLWidth + 4, $PosY + 2);
	$pdf->SetFont('','B',12);
	$pdf->Cell($AccW - $ImgLWidth - $ImgRWidth - 8, 7, $pdf->Name, 0, 0, 'C', 0);

	$pdf->SetXY($PosX + $ImgLWidth +4, $PosY + 9);	//Luogo
	$pdf->SetFont('','',7);
	$pdf->Cell($AccW - $ImgLWidth - $ImgRWidth - 8, 5, $pdf->Where . ", " . TournamentDate2String($pdf->WhenF,$pdf->WhenT),0,0,'C',0);

	// Category
	$pdf->SetFont('','BI',16);	//SE atleta --> Classe e Divisione
	$pdf->SetXY($PosX + 2, $PosY + 19);
	$pdf->Cell($AccW-4, 0, get_text($MyRow->DivDescription,'','',true), 0,0,'C',0);
	$pdf->SetXY($PosX + 2, $PosY + 27);
 	$pdf->Cell($AccW-4, 0, get_text($MyRow->ClDescription,'','',true),0,0,'C',0);



	$pdf->SetFont('','B',20);	//Cognome e Nome
	$pdf->SetXY($PosX + 2, $PosY + 39);
	$pdf->Cell($AccW-4, 0, $MyRow->FirstName . ' ' . $MyRow->Name,0,0,'C',0);
	$pdf->SetDefaultColor();

// 	if($IncludePhoto and !is_null($MyRow->PhPhoto))	//FOTO dell'accredito se Presente
// 	{
// 		$im = imagecreatefromstring(base64_decode($MyRow->PhPhoto));
// 		$tmpImgW = ((((($AccH*0.3)-4)/imagesy($im))*imagesx($im)));
// 		$pdf->Image('@'.base64_decode($MyRow->PhPhoto), $PosX + $AccW - 2 - $tmpImgW, $PosY+($AccH*0.3)+ 2, 0, ($AccH*0.3) - 4,'','','',false,300,'',false,false,1);
// 		imagedestroy($im);
// 	}

	// Nation Picture
	if(file_exists($tmp=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-svg-'.$MyRow->NationCode.'.svg')) {
		$pdf->ImageSVG($tmp, $PosX+2, $PosY + $AccH - $FlagHeight - 2, 0, $FlagHeight, '', '', '', 1);
	} elseif(file_exists($tmp=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$MyRow->NationCode.'.jpg')) {
		$pdf->Image($tmp, $PosX+2, $PosY + $AccH - $FlagHeight - 2, 0, $FlagHeight, '', '', '', 1);
	}
	$pdf->SetXY($PosX + 2 + $FlagHeight/2*3, $PosY + $AccH - $FlagHeight - 2);	//Country
	$pdf->SetFont('','B',25);	//Cognome e Nome
	$pdf->Cell($AccW - 2 - $FlagHeight/2*3, $FlagHeight, $MyRow->Nation,0,0,'C',0);

//RETTANGOLO CHE CONTIENE L'ACCREDITO
	$pdf->Rect($PosX,$PosY,$AccW,$AccH);
	$cntPass++;
}
safe_free_result($Rs);

$pdf->Output();

?>