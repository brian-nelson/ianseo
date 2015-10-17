<?php
$BadgePerPage=4;

$format=array(210,297);
$q=safe_r_sql("select ToPrintPaper from Tournament where ToId='{$_SESSION['TourId']}'");
if($r=safe_fetch($q) and $r->ToPrintPaper=='1') $format=array(215.9,279.4);

$pdf->SetAutoPageBreak(false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$cntPass=0;

$AccH = ($pdf->getPageHeight()/2)-4;
$AccW = ($pdf->getPageWidth()/2)-4;

while ($MyRow=safe_fetch($Rs))
{
	$pdf->SetDefaultColor();
	$PosX = ($cntPass % 2 == 0 ? 2:($pdf->getPageWidth()/2)+2);
	$PosY = ($cntPass % 4 < 2 ? 2:($pdf->getPageHeight()/2)+2);
	$AccColor = array(255,255,255);
	if (!is_null($MyRow->AcColor))
		$AccColor = array(base_convert(substr($MyRow->AcColor,0,2), 16, 10),base_convert(substr($MyRow->AcColor,2,2), 16, 10),base_convert(substr($MyRow->AcColor,4,2), 16, 10));

	//Every 4 Accreditation I change page
	if($cntPass % $BadgePerPage == 0)
		$pdf->AddPage();

	//Put their image as background
	$pdf->SetXY($PosX,$PosY);
	$pdf->Image( $_SESSION["TourCode"] . ".jpg",$PosX,$PosY,$AccW,$AccH,'JPEG','','T',2,300);

	//Banda Colorata
	$pdf->Rect($PosX,$PosY+33,$AccW,40,'F',array(),$AccColor);

	//Foto della persona
	$tmpImgW=0;
	if($IncludePhoto and !is_null($MyRow->PhPhoto))	//FOTO dell'accredito se Presente
	{
		$im = imagecreatefromstring(base64_decode($MyRow->PhPhoto));
		if($im)
		{
			$tmpImgW = (((40/imagesy($im))*imagesx($im)));
			$pdf->Image('@'.base64_decode($MyRow->PhPhoto), $PosX + $AccW - $tmpImgW, $PosY+33, 0, 40, '','','',false,300,'',false,false,0);
			imagedestroy($im);
		}
	}
	//Name
	$nameSpace = $AccW - $tmpImgW -4;
	if($MyRow->AcTitleReverse)
		$pdf->SetTextColor(255);
	$pdf->SetFont('','B',20);
	$pdf->SetXY($PosX + 2,$PosY+35);
	$pdf->Cell($nameSpace,8,mb_convert_case($MyRow->FirstName, MB_CASE_UPPER),0,0,'C',0);
	$pdf->SetFont('','B',18);
	$pdf->SetXY($PosX + 2,$PosY+43);
	$pdf->Cell($nameSpace,8,mb_convert_case($MyRow->Name, MB_CASE_TITLE),0,0,'C',0);

	//Athlete or Official
	$pdf->SetFont('','I',15);
	$pdf->SetXY($PosX+2,$PosY+54);
	$pdf->Cell($nameSpace,7,($MyRow->AcIsAthlete!=1 ? get_text($MyRow->DivDescription,'','',true) : get_text('Athlete')),0,0,'C',0);
	$pdf->SetFont('','I',12);
	$pdf->SetXY($PosX+1,$PosY+65);
	$pdf->Cell($nameSpace,8,($MyRow->AcIsAthlete==1 ? get_text($MyRow->DivDescription,'','',true) . " " . get_text($MyRow->ClDescription,'','',true): get_text($MyRow->ClDescription,'','',true)),0,0,'L',0);

	//Country
	$pdf->SetTextColor(0);
	$tmpImgW=0;
	$pdf->SetXY($PosX+2,$PosY+75);
	if(file_exists("../TV/Photos/" . $_SESSION["TourCodeSafe"] . "-Fl-" . $MyRow->NationCode . ".jpg"))	//FOTO dell'accredito se Presente
	{
		$pdf->Image("../TV/Photos/" . $_SESSION["TourCodeSafe"] . "-Fl-" . $MyRow->NationCode . ".jpg", $PosX + 1, $PosY+75, 0, 15, 'JPEG','','T',false,300,'',false,false,0);
	}
	$pdf->SetFont('','B',20);
	$pdf->Cell($AccW-($pdf->GetX()-$PosX+2),15,"  " . $MyRow->Nation,0,0,'L',0);

	//Transport, Accomodation and Meals

	if($MyRow->AcTransport != 0 && $MyRow->NationCode!="HUN")
		$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/Ac' . ($MyRow->AcTransport==1 ? 'Car' : ($MyRow->AcTransport==2 ? 'Van' : 'Bus'))  . '.png', $PosX+1, $PosY+110, 0,10, 'png');
	if($MyRow->AcAccomodation != 0 && $MyRow->NationCode!="HUN")
		$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/AcAccomodation.png', $PosX+25, $PosY+110, 0,10, 'png');
	if($MyRow->AcMeal != 0 && $MyRow->NationCode!="HUN")
		$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/AcMeal.png', $PosX+40, $PosY+110, 0,10, 'png', '', 'T');

	$pdf->SetXY($PosX+50,$PosY+110);
	$pdf->SetFont('','',20);
	$tmpText=' ';
	for($i=0; $i<=7;$i++)
		$tmpText .= ($MyRow->{'AcArea' . $i} ? $i . ($i<=1 && $MyRow->AcAreaStar ? '*':'') . '  ':'');
	$pdf->Cell(52,10,substr($tmpText,0,-2),0,0,'C',0);

	$cntPass++;

}
?>