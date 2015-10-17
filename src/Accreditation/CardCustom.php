<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/LabelPDF.inc.php');

$SORT='Printed, NationCode, FirstName, Name';

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

$Badges=array();
$AcTransport=array();
$AcTransport['img']=array('', 'Car', 'Van', 'Bus');
$AcTransport['size']=array();
foreach($AcTransport['img'] as $k=>$transport) {
	if($transport) $AcTransport['size'][$k]=getimagesize($CFG->DOCUMENT_PATH . 'Common/Images/Ac'.$transport.'.png');
}
$AcMeal=getimagesize($CFG->DOCUMENT_PATH . 'Common/Images/AcMeal.png');
$AcAccomodation=getimagesize($CFG->DOCUMENT_PATH . 'Common/Images/AcAccomodation.png');

$AcHeight=$AcAccomodation[1];

$OffsetX=explode(';', $_REQUEST['IdCardsSettings']['OffsetX']);
$OffsetY=explode(';', $_REQUEST['IdCardsSettings']['OffsetY']);

$PageWidth=$_REQUEST['IdCardsSettings']['PaperWidth'];
$PageHeight=$_REQUEST['IdCardsSettings']['PaperHeight'];

foreach($OffsetY as $y) {
	foreach($OffsetX as $x) {
		$Badges[]=array($x, $y);
	}
}

$BadgePerPage=count($Badges);
$Just=array('L', 'C', 'R');

$cntPass=0;
$pdf=new LabelPDF($PageWidth, $PageHeight);

// get the background of the card
$q=safe_r_SQL("select * from IdCards where IcTournament={$_SESSION['TourId']}");
$BackGround=safe_fetch($q) or debug_svela('Error in Accreditation!');
$BackGround->Options=unserialize($BackGround->IcSettings);

$Elements=array();
$q=safe_r_SQL("select * from IdCardElements where IceTournament={$_SESSION['TourId']} order by IceOrder");
while($r=safe_fetch($q)) {
	$r->Options=unserialize($r->IceOptions);
	if(!empty($r->Options['Font'])) {
		$r->Options['FontFamily']=$pdf->addTTFfont(K_PATH_FONTS.$r->Options['Font'].'.ttf');
		$r->Options['FontStyle']=(substr($r->Options['Font'], -2, 1)=='b' ? 'B' : '')
			.(substr($r->Options['Font'], -1, 1)=='i' ? 'I' : '');
	}
	if($r->IceType=='Accomodation') {
		// calculate the final dimentions regard the space we have....
		$OrgAcScaleFactor=($r->Options['H']-4)/$AcHeight;
	}
	$Elements[]=$r;
}

while ($MyRow=safe_fetch($Rs)) {
	$pdf->SetDefaultColor();

	if($cntPass==0) {
		$tmp=$pdf->addPage();
	}

	$StartX=$Badges[$cntPass][0];
	$StartY=$Badges[$cntPass][1];

	if($BackGround->IcBackground and file_exists($Back=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Accreditation.jpg')) {
// 		unset($BackGround->IcBackground);
// 		debug_svela($BackGround);
		$ElX=$StartX+$BackGround->Options['IdBgX'];
		$ElY=$StartY+$BackGround->Options['IdBgY'];
		$pdf->Image($Back, $ElX, $ElY, $BackGround->Options['IdBgW'], $BackGround->Options['IdBgH']);
	}

	foreach($Elements as $Element) {
		unset($Text);
		$ElX=$StartX+$Element->Options['X'];
		$ElY=$StartY+$Element->Options['Y'];
		switch($Element->IceType) {
			case 'ToLeft':
			case 'ToRight':
			case 'ToBottom':
				$pdf->Image($pdf->ToPaths[$Element->IceType], $ElX, $ElY, $Element->Options['W'], $Element->Options['H']);
				break;
			case 'Picture':
				if(file_exists($im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-En-'.$MyRow->EnId.'.jpg')) {
					$pdf->Image($im, $ElX, $ElY, $Element->Options['W'], $Element->Options['H']);
				}
				break;
			case 'Image':
				if(file_exists($im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Image-'.$Element->IceOrder.'.jpg')) {
					$pdf->Image($im, $ElX, $ElY, $Element->Options['W'], $Element->Options['H']);
				}
				break;
			case 'Accomodation':
				$Fill=false;
				if(!empty($Element->Options['BackCat'])) {
					$pdf->SetFillColor(hexdec(substr($MyRow->AcColor, 0, 2)), hexdec(substr($MyRow->AcColor, 2, 2)), hexdec(substr($MyRow->AcColor, 4, 2)));
					$Fill=true;
				} elseif($Element->Options['BackCol']) {
					$pdf->SetFillColor(hexdec(substr($Element->Options['BackCol'], 1, 2)), hexdec(substr($Element->Options['BackCol'], 3, 2)), hexdec(substr($Element->Options['BackCol'], 5, 2)));
					$Fill=true;
				}
				if(!empty($Element->Options['BackCat']) and $MyRow->AcTitleReverse) {
					$pdf->setColor('text', 255, 255, 255);
				} elseif($Element->Options['Col']) {
					$pdf->setColor('text', hexdec(substr($Element->Options['Col'], 1, 2)), hexdec(substr($Element->Options['Col'], 3, 2)), hexdec(substr($Element->Options['Col'], 5, 2)));
				} else {
					$pdf->setColor('text', 0, 0, 0);
				}
				$pdf->SetXY($ElX, $ElY + ($k*$Element->Options['H']));
				$pdf->Cell($Element->Options['W'], $Element->Options['H'], '', '', true,
						'', $Fill);

				$TotW=0;
				$TotAc=0;
				if($MyRow->AcTransport) {
					$TotW+=$AcTransport['size'][$MyRow->AcTransport][0];
					$TotAc++;
				}
				if($MyRow->AcAccomodation) {
					$TotW+=$AcAccomodation[0];
					$TotAc++;
				}
				if($MyRow->AcMeal) {
					$TotW+=$AcMeal[0];
					$TotAc++;
				}
				// now we know the total width of the original images...
				// Reducing factor
				if($TotW and ($Element->Options['W']-4)/$TotW < $OrgAcScaleFactor) {
					$ScaleFactor=($Element->Options['W']-4)/$TotW;
				} else {
					$ScaleFactor=$OrgAcScaleFactor;
				}

				$AcHorGap=($Element->Options['W']-($TotW*$ScaleFactor))/($TotAc+1);
				$AcX=$ElX+$AcHorGap;
				$AcY=$ElY+(($Element->Options['H']-($AcHeight*$ScaleFactor))/2);
				if($MyRow->AcTransport) {
					$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/Ac' . $AcTransport['img'][$MyRow->AcTransport] . '.png',
						$AcX, $AcY, $tmp=$AcTransport['size'][$MyRow->AcTransport][0]*$ScaleFactor, 0, 'png');
					$AcX+=$tmp+$AcHorGap;
				}
				if($MyRow->AcAccomodation) {
					$TotW+=$AcAccomodation[0];
					$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/AcAccomodation.png',
						$AcX, $AcY, $tmp=$AcAccomodation[0]*$ScaleFactor, 0, 'png');
					$AcX+=$tmp+$AcHorGap;
				}
				if($MyRow->AcMeal) {
					$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/AcMeal.png',
						$AcX, $AcY, $tmp=$AcMeal[0]*$ScaleFactor, 0, 'png');
				}
				break;
// 		if($MyRow->AcAccomodation != 0)
// 			$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/AcAccomodation.png', $PosX+($AccW*0.165)+4, $PosY+($AccH*0.81), $AccW*0.0825, 0, 'png');
// 		if($MyRow->AcMeal != 0)
// 			$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/AcMeal.png', $PosX+($AccW*0.2475)+6, $PosY+($AccH*0.81), $AccW*0.0675, 0, 'png');
				// 				[AcTransport] => 3
// 				[AcAccomodation] => 1
// 				[AcMeal] => 1
			case 'Flag':
				if(file_exists($im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$MyRow->NationCode.'.jpg')) {
					$pdf->Image($im, $ElX, $ElY, $Element->Options['W'], $Element->Options['H']);
					$pdf->SetDrawColor(128);
					$pdf->Rect($ElX, $ElY, $Element->Options['W'], $Element->Options['H']);
					$pdf->SetDrawColor(0);
				}
				break;
			case 'ColoredArea':
				$Text=explode("\n", $Element->IceContent);
				$Element->Options['H']=$Element->Options['H']/count($Text);
			case 'CompName':
				if(!isset($Text)) $Text=array($_SESSION['TourName']);
			case 'CompDetails':
				if(!isset($Text)) $Text=array($_SESSION['TourWhere'].' - '.TournamentDate2StringShort($_SESSION['TourWhenFrom'], $_SESSION['TourWhenTo']));
			case 'AthCode':
				if(!isset($Text)) $Text=array($MyRow->Bib);
			case 'Athlete':
				if(!isset($Text)) {
					switch($Element->IceContent) {
						case 'FamCaps': $Text=array($MyRow->FamCaps); break;
						case 'FamCaps-GAlone': $Text=array($MyRow->FamCaps.' '.substr($MyRow->GivCaps, 0, 1)); break;
						case 'FamCaps-GivCamel': $Text=array($MyRow->FamCaps.' '.$MyRow->GivCamel); break;
						case 'FamCaps-GivCaps': $Text=array($MyRow->FamCaps.' '.$MyRow->GivCaps); break;
						case 'FamCamel': $Text=array($MyRow->FamCamel); break;
						case 'FamCamel-GAlone': $Text=array($MyRow->FamCamel.' '.substr($MyRow->GivCaps, 0, 1)); break;
						case 'FamCamel-GivCamel': $Text=array($MyRow->FamCamel.' '.$MyRow->GivCamel); break;
						case 'GivCamel': $Text=array($MyRow->GivCamel); break;
						case 'GivCamel-FamCamel': $Text=array($MyRow->GivCamel.' '.$Text=$MyRow->FamCamel); break;
						case 'GivCamel-FamCaps': $Text=array($MyRow->GivCamel.' '.$MyRow->FamCaps); break;
						case 'GivCaps': $Text=array($MyRow->GivCaps); break;
						case 'GivCaps-FamCaps': $Text=array($MyRow->GivCaps.' '.$MyRow->FamCaps); break;
						case 'GAlone-FamCaps': $Text=array(substr($MyRow->GivCaps, 0, 1).' '.$MyRow->FamCaps); break;
						case 'GAlone-FamCamel': $Text=array(substr($MyRow->GivCaps, 0, 1)); break;
					}
				}
			case 'Club':
				if(!isset($Text)) {
					switch($Element->IceContent) {
						case 'NocCaps-ClubCamel':$Text=array($MyRow->NationCode.' '.$MyRow->Nation); break;
						case 'NocCaps-ClubCaps':$Text=array($MyRow->NationCode.' '.$MyRow->NationCaps); break;
						case 'NocCaps':$Text=array($MyRow->NationCode); break;
						case 'ClubCamel':$Text=array($MyRow->Nation); break;
						case 'ClubCaps':$Text=array($MyRow->NationCaps); break;
					}
				}
			case 'Category':
				if(!isset($Text)) $Text=array($MyRow->DivDescription. ' '.$MyRow->ClDescription);
			case 'Session':
				if(!isset($Text)) {
					if($MyRow->SesName) {
						$Text=array($MyRow->SesName);
					} elseif($MyRow->Session) {
						$Text=array(get_text('Session') . ' ' . $MyRow->Session);
					} else {
						$Text=array('');
					}
				}
			case 'Access':
				if(!isset($Text)) {
					$txt='';
					for($i=0; $i<8; $i++) {
						if($MyRow->{'AcArea'.$i}) {
							$txt.=$i;
							if($i<2 and $MyRow->AcAreaStar) $txt.='*';
							$txt.=' ';
						}
					}
					$Text=array(trim($txt));
				}

				$Fill=false;
				$pdf->SetFont($Element->Options['FontFamily'], $Element->Options['FontStyle'], $Element->Options['Size']);
				if(!empty($Element->Options['BackCat'])) {
					$pdf->SetFillColor(hexdec(substr($MyRow->AcColor, 0, 2)), hexdec(substr($MyRow->AcColor, 2, 2)), hexdec(substr($MyRow->AcColor, 4, 2)));
					$Fill=true;
				} elseif($Element->Options['BackCol']) {
					$pdf->SetFillColor(hexdec(substr($Element->Options['BackCol'], 1, 2)), hexdec(substr($Element->Options['BackCol'], 3, 2)), hexdec(substr($Element->Options['BackCol'], 5, 2)));
					$Fill=true;
				}
				if(!empty($Element->Options['BackCat']) and $MyRow->AcTitleReverse) {
					$pdf->setColor('text', 255, 255, 255);
				} elseif($Element->Options['Col']) {
					$pdf->setColor('text', hexdec(substr($Element->Options['Col'], 1, 2)), hexdec(substr($Element->Options['Col'], 3, 2)), hexdec(substr($Element->Options['Col'], 5, 2)));
				} else {
					$pdf->setColor('text', 0, 0, 0);
				}
				foreach($Text as $k => $txt) {
					$pdf->SetXY($ElX, $ElY + ($k*$Element->Options['H']));
					$pdf->Cell($Element->Options['W'], $Element->Options['H'], $txt, '', true,
							$Just[$Element->Options['Just']], $Fill);
				}
				break;
			case 'AthBarCode':
				if(!empty($Element->Options['BackCat']) and $MyRow->AcTitleReverse) {
					$pdf->setColor('text', 255, 255, 255);
				} elseif($Element->Options['Col']) {
					$pdf->setColor('text', hexdec(substr($Element->Options['Col'], 1, 2)), hexdec(substr($Element->Options['Col'], 3, 2)), hexdec(substr($Element->Options['Col'], 5, 2)));
				} else {
					$pdf->setColor('text', 0, 0, 0);
				}
				$pdf->SetXY($ElX, $ElY);
				$pdf->SetFont('barcode','','22');
				$pdf->Cell($Element->Options['W'], $Element->Options['H'], mb_convert_encoding('*' . $MyRow->Bib.'-'.$MyRow->DivCode.'-'.$MyRow->ClassCode, "UTF-8","cp1252") . "*",0,0,'C',0);
				break;
			case 'AthQrCode':
				if(!empty($Element->Options['BackCat']) and $MyRow->AcTitleReverse) {
					$pdf->setColor('text', 255, 255, 255);
				} elseif($Element->Options['Col']) {
					$pdf->setColor('text', hexdec(substr($Element->Options['Col'], 1, 2)), hexdec(substr($Element->Options['Col'], 3, 2)), hexdec(substr($Element->Options['Col'], 5, 2)));
				} else {
					$pdf->setColor('text', 0, 0, 0);
				}
				$style = array(
					'border' => 2,
					'vpadding' => 'auto',
					'hpadding' => 'auto',
					'fgcolor' => array(0,0,0),
					'bgcolor' => array(255,255,255), //array(255,255,255)
					'module_width' => 1, // width of a single module in points
					'module_height' => 1 // height of a single module in points
					);
				if($Element->Options['BackCol']) {
					$style['bgcolor']=array(hexdec(substr($Element->Options['BackCol'], 1, 2)), hexdec(substr($Element->Options['BackCol'], 3, 2)), hexdec(substr($Element->Options['BackCol'], 5, 2)));
				}
				if($Element->Options['Col']) {
					$style['fgcolor']=array(hexdec(substr($Element->Options['Col'], 1, 2)), hexdec(substr($Element->Options['Col'], 3, 2)), hexdec(substr($Element->Options['Col'], 5, 2)));
				}
				$pdf->write2DBarcode($MyRow->Bib.'-'.$MyRow->DivCode.'-'.$MyRow->ClassCode, 'QRCODE,L', $ElX, $ElY, $Element->Options['W'], $Element->Options['H'], $style, 'N');
				break;
			default:
				debug_svela($Element, $MyRow);
		}
	}

// 	$ImgSxSize= NULL;
// 	$ImgDxSize= NULL;
// 	$ImgLowSize= NULL;
// 	if(!empty($pdf->ToPaths['ToLeft'])) $ImgSxSize=getimagesize($pdf->ToPaths['ToLeft']);
// 	if(!empty($pdf->ToPaths['ToRight'])) $ImgDxSize=getimagesize($pdf->ToPaths['ToRight']);
// 	if(!empty($pdf->ToPaths['ToBottom'])) $ImgLowSize=getimagesize($pdf->ToPaths['ToBottom']);

// 	if(is_file($file=$CFG->DOCUMENT_PATH.'TV/Photos/TV-'.$_SESSION["TourCodeSafe"].'-IdCardFooter.jpg')) {
// 		$pdf->ToPaths['ToBottom']=$file;
// 		$ImgLowSize=getimagesize($pdf->ToPaths['ToBottom']);
// 	}

// 	switch($BadgePerPage) {
// 		case 1:
// 			$AccH = $pdf->getPageHeight()-10;
// 			$AccW = $pdf->getPageWidth()-10;
// 			break;
// 		case 2:
// 			$AccH = $pdf->getPageHeight()-10;
// 			$AccW = ($pdf->getPageWidth()/2)-10;
// 			break;
// 		default:
// 			$AccH = ($pdf->getPageHeight()/2)-10;
// 			$AccW = ($pdf->getPageWidth()/2)-10;
// 			break;
// 	}

// 		switch($BadgePerPage) {
// 			case 1:
// 				$PosX = 5;
// 				$PosY = 5;
// 				break;
// 			case 2:
// 				$PosX = ($cntPass % 2 == 0 ? 5: ($pdf->getPageWidth()/2)+5);
// 				$PosY = 5;
// 				break;
// 			default:
// 				$PosX = ($cntPass % 2 == 0 ? 5: ($pdf->getPageWidth()/2)+5);
// 				$PosY = ($cntPass % 4 < 2 ? 5: ($pdf->getPageHeight()/2)+5);
// 				break;
// 		}
// 		$AccColor = array(255,255,255);
// 		if (!is_null($MyRow->AcColor))
// 			$AccColor = array(base_convert(substr($MyRow->AcColor,0,2), 16, 10),base_convert(substr($MyRow->AcColor,2,2), 16, 10),base_convert(substr($MyRow->AcColor,4,2), 16, 10));

// 		//Every 4 Accreditation I change page
// 		if($cntPass % $BadgePerPage == 0)
// 			$pdf->AddPage();

// 	//PRIMA Area dell'accredito: Logo SX e Nome/Country + sfondo colore - Altezza 6/20 (ne restano 14/20))
// 		$pdf->SetXY($PosX,$PosY);
// 		$pdf->SetFont('','B',15);
// 		$pdf->Cell($AccW,$AccH*0.05,$pdf->Name,0,0,'C',0);
// 		$pdf->Rect($PosX,$PosY+($AccH*0.05),$AccW,$AccH*0.2,'F',array(),$AccColor);
// 		$tmpX = $PosX + 2;
// 		if(!is_null($ImgSxSize))	//Immagine Organizzatore (DX)
// 		{
// 			if($ImgSxSize[0]/$ImgSxSize[1]<=(($AccW*0.4) - 4)/(($AccH*0.2) - 4))	//Immagine troppo larga
// 				$pdf->Image($pdf->ToPaths['ToLeft'], $tmpX, $PosY+($AccH*0.05)+ 2, 0, ($AccH*0.2) - 4);
// 			else
// 				$pdf->Image($pdf->ToPaths['ToLeft'], $tmpX, $PosY+($AccH*0.05)+(((((($AccW*0.4)-4)/$ImgSxSize[0])*$ImgSxSize[1])/2)/$pdf->getScaleFactor()), ($AccW*0.4) - 4, 0);
// 			$tmpX = ($pdf->getImageRBX()+2);
// 		}
// 		if($MyRow->AcTitleReverse)
// 			$pdf->SetTextColor(255);

// 		$pdf->SetFont('','B',20);	//Cognome e Nome
// 		$pdf->SetXY($tmpX,$PosY+($AccH*0.08));
// 		$pdf->Cell($AccW-($tmpX-$PosX)-2, $AccH*0.07,$MyRow->FirstName,0,0,'C',0);
// 		$pdf->SetXY($tmpX,$PosY+($AccH*0.15));
// 		$pdf->Cell($AccW-($tmpX-$PosX)-2, $AccH*0.07, $MyRow->Name,0,0,'C',0);
// 		$pdf->SetDefaultColor();

// 		$pdf->SetXY($PosX,$PosY+$AccH*0.25);	//Luogo
// 		$pdf->SetFont('','',8);
// 		$pdf->Cell($AccW,$AccH*0.02,$pdf->Where . ", " . TournamentDate2String($pdf->WhenF,$pdf->WhenT),0,0,'C',0);
// 	//SPAZIO BIANCO DI RISPETTO: Altezza 2/20 (totale 8/20, ne restano 12/20)

// 	//SECONDA Area dell'accredito: Logo DX e Nome/Country/Categoria/Photo - Altezza 7/20 (totale 15/20, ne restano 5/20)
// 		$tmpX = $PosX + 2;
// 		if(!is_null($ImgDxSize))	//Immagine Organizzatore (DX)
// 		{
// 			if($ImgDxSize[1]>=$ImgDxSize[0]) //Immagine + alta che larga, comanda l'altezza
// 				$pdf->Image($pdf->ToPaths['ToRight'], $tmpX, $PosY+($AccH*0.4)+ 2, 0, ($AccH*0.2) - 4);
// 			else
// 				$pdf->Image($pdf->ToPaths['ToRight'], $tmpX, $PosY+($AccH*0.4)+ 2, ($AccW*0.35) - 4, 0);
// 			$tmpX = $pdf->getImageRBX()+2;
// 		}

// 		$tmpImgW=0;
// 		$im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION["TourCodeSafe"].'-En-'.$MyRow->EnId.'.jpg';
// 		if($IncludePhoto and is_file($im))	//FOTO dell'accredito se Presente
// 		{
// 			$imSize=getimagesize($im);

// 			$tmpImgW = ((((($AccH*0.3)-4)/$imSize[1])*$imSize[0]));
// 			$pdf->Image($im, $PosX + $AccW - 2 - $tmpImgW, $PosY+($AccH*0.4)+ 2, 0, ($AccH*0.3) - 4, '','','',false,300,'',false,false,1);
// 		}

// 		$pdf->SetFont('','I',6);	//SE atleta --> Classe e Divisione
// 		$pdf->SetXY($tmpX,$PosY+($AccH*0.4)+2);
// 		$pdf->Cell($AccW-($tmpX-$PosX)-$tmpImgW-4,$AccH*0.05,($MyRow->AcIsAthlete==1 ? get_text($MyRow->DivDescription,'','',true) . ' ' . get_text($MyRow->ClDescription,'','',true):''),0,0,'R',0);
// 		$pdf->SetFont('','B',12);	//SE atleta --> Classe e Divisione
// 		$pdf->SetXY($tmpX,$PosY+($AccH*0.5)+2);
// 		$pdf->Cell($AccW-($tmpX-$PosX)-$tmpImgW-4,$AccH*0.05,($MyRow->AcIsAthlete==1 ? get_text('Athlete'):$MyRow->ClDescription),0,0,'C',0);

// 		$pdf->SetFont('','B',14);	//Cognome e Nome
// 		$pdf->SetXY($PosX + 2,$PosY+($AccH*0.65));
// 		$pdf->Cell($AccW-($AccH*0.225)-1,$AccH*0.05,$MyRow->FirstName,0,0,'L',0);
// 		$pdf->SetXY($PosX + 2,$PosY+($AccH*0.70));
// 		$pdf->Cell($AccW-($AccH*0.225)-1,$AccH*0.05, $MyRow->Name,0,0,'L',0);
// 		$pdf->SetXY($PosX + 1 + $AccW-($AccH*0.225),$PosY+($AccH*0.7));	//Country
// 		$pdf->Cell(($AccH*0.225)-3,$AccH*0.05, $MyRow->Nation,0,0,'C',0);

// 	//TERZA Area dell'accredito: Transport/Accomodation/Meal + Areas + sfondo colore - Altezza 3/20 (totale 18/20, ne restano 2/20)
// 		$pdf->Rect($PosX,$PosY+($AccH*0.775),($AccW*0.4)-0.5,$AccH*0.125,'F',array(),$AccColor);
// 		$pdf->Rect($PosX+($AccW*0.4)+0.5,$PosY+($AccH*0.775),($AccW*0.6)-0.5,$AccH*0.125,'F',array(),$AccColor);
// 		//Trasporti, accomodation e pranzi
// 		if($MyRow->AcTransport != 0)
// 			$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/Ac' . ($MyRow->AcTransport==1 ? 'Car' : ($MyRow->AcTransport==2 ? 'Van' : 'Bus'))  . '.png', $PosX+2, $PosY+($AccH*0.81), $AccW*0.165,0, 'png');
// 		if($MyRow->AcAccomodation != 0)
// 			$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/AcAccomodation.png', $PosX+($AccW*0.165)+4, $PosY+($AccH*0.81), $AccW*0.0825, 0, 'png');
// 		if($MyRow->AcMeal != 0)
// 			$pdf->Image($CFG->DOCUMENT_PATH . 'Common/Images/AcMeal.png', $PosX+($AccW*0.2475)+6, $PosY+($AccH*0.81), $AccW*0.0675, 0, 'png');

// 		//Aree di accesso
// 		if($MyRow->AcTitleReverse)
// 			$pdf->SetTextColor(255);
// 		$tmpText=' ';
// 		for($i=0; $i<=7;$i++)
// 		{
// 			$tmpText .= ($MyRow->{'AcArea' . $i} ? $i . ($i<=1 && $MyRow->AcAreaStar ? '*':'') . ' ':'');
// 		}
// 		$pdf->SetFont('','B',40);	//Cognome e Nome
// 		$pdf->SetXY($PosX+($AccW*0.4)+0.5,$PosY+($AccH*0.775));
// 		$pdf->Cell(($AccW*0.6)-0.5,$AccH*0.125,$tmpText,0,0,'C',0);
// 		$pdf->SetDefaultColor();

// 	//QUARTA Area dell'accredito: Immagine Bottom degli sponsor - Altezza 2/20 (totale 20/20)
// 		if(!is_null($ImgLowSize))	//Immagine Sponsor
// 		{
// 			if($ImgLowSize[0]/$ImgLowSize[1]>=($AccW-2)/(($AccH*0.1)-2))	//Immagine troppo larga
// 				$pdf->Image($pdf->ToPaths['ToBottom'], $PosX+1, $PosY+($AccH*0.9)+($AccH*0.1-((($AccW-2)/$ImgLowSize[0])*$ImgLowSize[1]))/2, $AccW-2, 0);
// 			else
// 				$pdf->Image($pdf->ToPaths['ToBottom'], $PosX+($AccW-((((0.1*$AccH)-2)/$ImgLowSize[1])*$ImgLowSize[0]))/2, $PosY+($AccH*0.9)+1,0, ($AccH*0.1)-2);
// 		}

// 	//RETTANGOLO CHE CONTIENE L'ACCREDITO
		$pdf->Rect($StartX, $StartY, $_REQUEST['IdCardsSettings']['Width'], $_REQUEST['IdCardsSettings']['Height']);
		$cntPass++;
		if($cntPass>=count($Badges)) $cntPass=0;
	}
// 	$pdf->deletePage(1);

safe_free_result($Rs);
$pdf->Output();

?>