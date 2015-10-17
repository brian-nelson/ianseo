<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/LabelPDF.inc.php');
require_once('Common/OrisFunctions.php');
/*
 *
 * Page / Label Dimensions  	Report Builder Fields  	Avery 5160 (inches)
---Page Width 	Report – Page Width 	8.5
---Page Height 	Report – Page Height 	11
---Label Width 	* 	2.5935
---Label Height 	Detail – Height 	1
Top Margin 	Report – Top Margin 	0.5
Right Margin 	Report – Right Margin 	0.5
Bottom Margin 	Report – Bottom Margin 	0.1875
Left Margin 	Report – Left Margin 	0.1875
Columns 	Report – Column Count 	3
Horizontal Spacing 	Report – Column Spacing 	0.15625
 *
 */

if(CheckTourSession()) {

	$pdf = new LabelPDF();
	//Predefinita per etichette A4
	$lblW= $pdf->GetPageWidth()/3;
	$lblH= $pdf->GetPageHeight()/8;
	$lblMarginH=2;
	$lblMarginV=2;
	$lblSpaceV=0;
	$lblSpaceH=0;
	$pageMarginT=0;
	$pageMarginL=0;
	$Label4Column=3;
	$Label4Page=24;
	$printBarcode=true;

	if(intval($pdf->GetPageWidth())==210 && intval($pdf->GetPageHeight())==297)	//Etichette A4
	{
		$lblMarginH=4;
		$lblMarginV=4;
	}
	else
	{
		$lblW= 66;
		$lblH= 25.5;
		$lblMarginH=3;
		$lblMarginV=4;
// 		$lblSpaceH=0.15625*25.4;
// 		$lblSpaceV=0;
		$pageMarginT=11;
		$pageMarginL=5;
		$lblSpaceH= 3;
		$Label4Page=30;
// 		$printBarcode=false;
	}

	$PdfData=getStartList();

	$pdf->SetCellPadding(0);

// 	debug_svela($PdfData->Data['Items']);

	$Etichetta=0;
	if(!empty($PdfData->Data['Items'])) {
		foreach($PdfData->Data['Items'] as $Entry) {
			if(!$Entry->Athlete) continue;
			if($Etichetta==0) $pdf->AddPage();

			//Cerchia Etichetta
// 			$pdf->Rect($pageMarginL+(($Etichetta % $Label4Column) * ($lblW+$lblSpaceH)),$pageMarginT+(intval($Etichetta / $Label4Column) * ($lblH+$lblSpaceV)),$lblW,$lblH,"D");

			$pdf->SetXY(0,0);
			$pdf->SetLeftMargin($pageMarginL+$lblMarginH+(($Etichetta % $Label4Column) * ($lblW+$lblSpaceH)));
			$pdf->SetTopMargin($pageMarginT+$lblMarginV+(intval($Etichetta / $Label4Column) * ($lblH+$lblSpaceV)));

			//Piazzola, Turno & Classe.Divisione
			$pdf->SetFont($pdf->FontStd,'B',20);
			$pdf->Cell(17, 8, ltrim($Entry->TargetNo, '0'), 0, 0, 'C', 0);
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->SetX($pdf->GetX()+1);
			$pdf->Cell(22, 8, $Entry->SesName,0,0,'C',0, '', '1', false, 'T', 'B');
			$pdf->SetX($pdf->GetX()+1);
			$pdf->SetFont($pdf->FontStd,'',10);
			$X=$pdf->GetX();
			$Y=$pdf->GetY();
			$pdf->Cell($lblW-(2*$lblMarginH)-41, 4, $Entry->DivDescription ,0,1,'R',0, '', '1', false, 'T', 'T');
			$pdf->SetXY($X, $Y+4);
			$pdf->Cell($lblW-(2*$lblMarginH)-41, 4, $Entry->ClDescription . ' ' . intval($Entry->SubClass),0,1,'R',0, '', '1', false, 'T', 'B');


			//Arciere & Società
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->Cell($lblW-(2*$lblMarginH)-10, 6, $Entry->Athlete,0,0,'L',0);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->SetX($pdf->GetX()+1);
			$pdf->Cell(9, 6, $Entry->NationCode, 0, 0, 'R', 0, '', '1', false, 'T', 'B');

// 			//Barcode
			$pdf->SetY($pdf->GetY()+5);
			$pdf->SetFont('barcode','',25);
			$pdf->Cell($lblW-(2*$lblMarginH),10, mb_convert_encoding('*' . $Entry->Bib.'-'.$Entry->DivCode.'-'.$Entry->ClassCode, "UTF-8","cp1252") . "*",0,0,'C',0);

			$Etichetta = ++$Etichetta % $Label4Page;

		}
	}

	$pdf->Output();
}

/*
 [SesName] => Line A
 [Bib] => 1750
 [Athlete] => ANDREWS Shannon
 [Session] => 1
 [TargetNo] => 044A
 [NationCode] => USA
 [Nation] => UNITED STATES OF AMERICA
 [ClassCode] => FX
 [DivCode] => FU
 [DivDescription] => Freestyle Unlimited
 [ClDescription] => Flights
 [SubClass] => 13
 [Status] => 0

*/
