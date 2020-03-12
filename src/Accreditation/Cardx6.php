<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclParticipants, AclReadOnly);
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

$cntPass=0;
$ImgSxSize= NULL;
$ImgDxSize= NULL;
$ImgLowSize= NULL;
if(!empty($pdf->ToPaths['ToLeft'])) $ImgSxSize=getimagesize($pdf->ToPaths['ToLeft']);
if(!empty($pdf->ToPaths['ToRight'])) $ImgDxSize=getimagesize($pdf->ToPaths['ToRight']);
if(!empty($pdf->ToPaths['ToBottom'])) $ImgLowSize=getimagesize($pdf->ToPaths['ToBottom']);

if(is_file($file=$CFG->DOCUMENT_PATH.'TV/Photos/TV-'.$_SESSION["TourCodeSafe"].'-IdCardFooter.jpg')) {
	$pdf->ToPaths['ToBottom']=$file;
	$ImgLowSize=getimagesize($pdf->ToPaths['ToBottom']);
}

$AccH = ($pdf->getPageHeight()/3)-10;
$AccW = ($pdf->getPageWidth()/2)-10;

while ($MyRow=safe_fetch($Rs)) {
	$pdf->SetDefaultColor();
	$PosX = ($cntPass % 2 == 0 ? 5:($pdf->getPageWidth()/2)+5);
	$PosY = ($cntPass % 6 < 2 ? 5:($cntPass % 6 < 4 ? ($pdf->getPageHeight()/3)+5 : ($pdf->getPageHeight()*2/3)+5));
	$AccColor = array(255,255,255);
	if (!is_null($MyRow->AcColor))
		$AccColor = array(base_convert(substr($MyRow->AcColor,0,2), 16, 10),base_convert(substr($MyRow->AcColor,2,2), 16, 10),base_convert(substr($MyRow->AcColor,4,2), 16, 10));

	//Every 4 Accreditation I change page
	if($cntPass % 6 == 0)
		$pdf->AddPage();

//PRIMA Area dell'accredito: Logo SX e Nome/Country + sfondo colore - Altezza 6/20 (ne restano 14/20))
	$pdf->SetXY($PosX,$PosY);
	$pdf->SetFont('','B',12);
	$pdf->Cell($AccW,$AccH*0.05,$pdf->Name,0,0,'C',0);
	$pdf->Rect($PosX,$PosY+($AccH*0.05),$AccW,$AccH*0.2,'F',array(),$AccColor);
	$tmpX = $PosX + 2;
	if(!is_null($ImgSxSize)) {
		//Immagine Sinistra
		if($ImgSxSize[0]/$ImgSxSize[1]<=(($AccW*0.4) - 4)/(($AccH*0.2) - 4))	//Immagine troppo larga
			$pdf->Image($pdf->ToPaths['ToLeft'], $tmpX, $PosY+($AccH*0.05)+ 2, 0, ($AccH*0.2) - 4);
		else
			$pdf->Image($pdf->ToPaths['ToLeft'], $tmpX, $PosY+($AccH*0.05)+(((((($AccW*0.4)-4)/$ImgSxSize[0])*$ImgSxSize[1])/2)/$pdf->getScaleFactor()), ($AccW*0.4) - 4, 0);
		$tmpX = ($pdf->getImageRBX()+2);
	}
	if($MyRow->AcTitleReverse)
		$pdf->SetTextColor(255);
	$pdf->SetFont('','B',16);	//Cognome e Nome
	$pdf->SetXY($tmpX,$PosY+($AccH*0.08));
	$pdf->Cell($AccW+2-$tmpX-$PosX,$AccH*0.07,$MyRow->FirstName,0,0,'C',0);
	$pdf->SetXY($tmpX,$PosY+($AccH*0.15));
	$pdf->Cell($AccW+2-$tmpX-$PosX,$AccH*0.07, $MyRow->Name,0,0,'C',0);
	$pdf->SetDefaultColor();

	$pdf->SetXY($PosX,$PosY+$AccH*0.25);	//Luogo
	$pdf->SetFont('','',7);
	$pdf->Cell($AccW,$AccH*0.02,$pdf->Where . ", " . TournamentDate2String($pdf->WhenF,$pdf->WhenT),0,0,'C',0);
//SPAZIO BIANCO DI RISPETTO: Altezza 2/20 (totale 8/20, ne restano 12/20)

//SECONDA Area dell'accredito: Logo DX e Nome/Country/Categoria/Photo - Altezza 7/20 (totale 15/20, ne restano 5/20)
	$tmpX = $PosX + 2;
	if(!is_null($ImgDxSize)) {
		//Immagine Organizzatore (DX)
		if($ImgDxSize[1]>=$ImgDxSize[0]) //Immagine + alta che larga, comanda l'altezza
			$pdf->Image($pdf->ToPaths['ToRight'], $tmpX, $PosY+($AccH*0.3)+ 2, 0, ($AccH*0.25) - 4);
		else
			$pdf->Image($pdf->ToPaths['ToRight'], $tmpX, $PosY+($AccH*0.3)+ 2, ($AccW*0.25) - 4, 0);
		$tmpX = $pdf->getImageRBX()+2;
	}

	$tmpImgW=0;
	$im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION["TourCodeSafe"].'-En-'.$MyRow->EnId.'.jpg';
	if($IncludePhoto and is_file($im))	//FOTO dell'accredito se Presente
	{
		$imSize=getimagesize($im);

		$tmpImgW = ((((($AccH*0.3)-4)/$imSize[1])*$imSize[0]));
		$pdf->Image($im, $PosX + $AccW - 2 - $tmpImgW, $PosY+($AccH*0.3)+ 2, 0, ($AccH*0.3) - 4, '','','',false,300,'',false,false,1);
	}

	$pdf->SetFont('','I',10);	//SE atleta --> Classe e Divisione
	$pdf->SetXY($tmpX,$PosY+($AccH*0.3)+2);
	$pdf->Cell($AccW-($tmpX-$PosX)-$tmpImgW-4,$AccH*0.05,($MyRow->AcIsAthlete==1 ? get_text($MyRow->DivDescription,'','',true) . ' ' . get_text($MyRow->ClDescription,'','',true):''),0,0,'R',0);
	$pdf->SetFont('','B',14);	//SE atleta --> Classe e Divisione
	$pdf->SetXY($tmpX,$PosY+($AccH*0.4)+2);
	$pdf->Cell($AccW-($tmpX-$PosX)-$tmpImgW-4,$AccH*0.05,($MyRow->AcIsAthlete==1 ? get_text('Athlete'):$MyRow->ClDescription),0,0,'C',0);

	$pdf->SetFont('','B',14);	//Cognome e Nome
	$pdf->SetXY($PosX + 2,$PosY+($AccH*0.525));
	$pdf->Cell($AccW-($AccH*0.225)-1,$AccH*0.05,$MyRow->FirstName,0,0,'L',0);
	$pdf->SetXY($PosX + 2,$PosY+($AccH*0.6));
	$pdf->Cell($AccW-($AccH*0.225)-1,$AccH*0.05, $MyRow->Name,0,0,'L',0);
	$pdf->SetXY($PosX + 1 + $AccW-($AccH*0.4),$PosY+($AccH*0.6));	//Country
	$pdf->Cell(($AccH*0.4)-3,$AccH*0.05, $MyRow->Nation,0,0,'C',0);

//TERZA Area dell'accredito: Transport/Accomodation/Meal + Areas + sfondo colore - Altezza 3/20 (totale 18/20, ne restano 2/20)
	$pdf->Rect($PosX,$PosY+($AccH*0.675),($AccW*0.4)-0.5,$AccH*0.15,'F',array(),$AccColor);
	$pdf->Rect($PosX+($AccW*0.4)+0.5,$PosY+($AccH*0.675),($AccW*0.6)-0.5,$AccH*0.15,'F',array(),$AccColor);
	//Trasporti, accomodation e pranzi
	if($MyRow->AcTransport != 0)
		$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/Ac' . ($MyRow->AcTransport==1 ? 'Car' : ($MyRow->AcTransport==2 ? 'Van' : 'Bus'))  . '.png', $PosX+2, $PosY+($AccH*0.71), $AccW*0.165,0, 'png');
	if($MyRow->AcAccomodation != 0)
		$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/AcAccomodation.png', $PosX+($AccW*0.165)+4, $PosY+($AccH*0.71), $AccW*0.0825, 0, 'png');
	if($MyRow->AcMeal != 0)
		$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/AcMeal.png', $PosX+($AccW*0.2475)+6, $PosY+($AccH*0.71), $AccW*0.0675, 0, 'png');

	//Aree di accesso
	if($MyRow->AcTitleReverse)
		$pdf->SetTextColor(255);
	$tmpText=' ';
	for($i=0; $i<=7;$i++)
	{
		$tmpText .= ($MyRow->{'AcArea' . $i} ? $i . ($i<=1 && $MyRow->AcAreaStar ? '*':'') . ' ':'');
	}
	$pdf->SetFont('','B',35);	//Cognome e Nome
	$pdf->SetXY($PosX+($AccW*0.4)+0.5,$PosY+($AccH*0.675));
	$pdf->Cell(($AccW*0.6)-0.5,$AccH*0.15,$tmpText,0,0,'C',0);
	$pdf->SetDefaultColor();

//QUARTA Area dell'accredito: Immagine Bottom degli sponsor - Altezza 2/20 (totale 20/20)
	if(!is_null($ImgLowSize))	//Immagine Sponsor
	{
		if($ImgLowSize[0]/$ImgLowSize[1]>=($AccW-2)/(($AccH*0.1)-2))	//Immagine troppo larga
			$pdf->Image($pdf->ToPaths['ToBottom'], $PosX+1, $PosY+($AccH*0.9)+($AccH*0.1-((($AccW-2)/$ImgLowSize[0])*$ImgLowSize[1]))/2, $AccW-2, 0);
		else
			$pdf->Image($pdf->ToPaths['ToBottom'], $PosX+($AccW-((((0.1*$AccH)-2)/$ImgLowSize[1])*$ImgLowSize[0]))/2, $PosY+($AccH*0.9)+1,0, ($AccH*0.1)-2);
	}

//RETTANGOLO CHE CONTIENE L'ACCREDITO
	$pdf->Rect($PosX,$PosY,$AccW,$AccH);
	$cntPass++;
}
safe_free_result($Rs);

$pdf->Output();

?>