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
    checkACL(AclQualification, AclReadOnly);

	if($_SESSION['ToPaper']) {
		$pdf = new LabelPDF(216, 280);
	} else {
		$pdf = new LabelPDF();
	}
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
		for($j=0; $j<8; $j++) {
			for($i=0; $i<3; $i++) {
				$Pos[]=array($pageMarginL+($lblW+$lblSpaceH)*$i, $pageMarginT+$j*$lblH);
			}
		}
	}
	else
	{
		$lblW= 66.68;
		$lblH= 25.35;
		$lblMarginH=3;
		$lblMarginV=2;
// 		$lblSpaceH=0.15625*25.4;
// 		$lblSpaceV=0;
		$pageMarginT=13;
		$pageMarginL=5;
		$lblSpaceH= 3.5;
		$Label4Page=30;
// 		$printBarcode=false;
		$Pos=array();
		for($j=0; $j<10; $j++) {
			for($i=0; $i<3; $i++) {
				$Pos[]=array($pageMarginL+($lblW+$lblSpaceH)*$i, $pageMarginT+$j*$lblH);
			}
		}
	}
	$PdfData=getStartList();

	$pdf->SetCellPadding(0);

	$Etichetta=0;
	if(!empty($PdfData->Data['Items'])) {
		$Etichetta=0;
		foreach($PdfData->Data['Items'] as $Entries) {
            foreach($Entries as $Entry) {
                if (empty($Entry->Athlete)) continue;
                if ($Etichetta == 0) $pdf->AddPage();
                //Cerchia Etichetta
//                $pdf->Rect($pageMarginL + (($Etichetta % $Label4Column) * ($lblW + $lblSpaceH)), $pageMarginT + (intval($Etichetta / $Label4Column) * ($lblH + $lblSpaceV)), $lblW, $lblH, "D");

                $pdf->SetXY($Pos[$Etichetta][0] + $lblMarginH, $Pos[$Etichetta][1] + $lblMarginV);

                //Piazzola, Turno & Classe.Divisione
                $pdf->SetFont($pdf->FontStd, 'B', 20);
                $pdf->Cell(17, 8, ltrim($Entry->TargetNo, '0'), 0, 0, 'C', 0);
                $pdf->SetFont($pdf->FontStd, 'B', 12);
                $pdf->SetX($pdf->GetX() + 1);
                $pdf->Cell(22, 8, $Entry->SesName, 0, 0, 'C', 0, '', '1', false, 'T', 'B');
                $pdf->SetX($pdf->GetX() + 1);
                $pdf->SetFont($pdf->FontStd, '', 10);
                $pdf->Cell($lblW - 41 - 2 * $lblMarginH, 4, $Entry->DivDescription, 0, 1, 'R', 0, '', '1', false, 'T', 'T');
                $pdf->SetXY($Pos[$Etichetta][0] + 41 + $lblMarginH, $Pos[$Etichetta][1] + 4 + $lblMarginV);
                $pdf->Cell($lblW - 41 - 2 * $lblMarginH, 4, $Entry->ClDescription . ' ' . intval($Entry->SubClass), 0, 1, 'R', 0, '', '1', false, 'T', 'B');


                //Arciere & Società
                $pdf->SetFont($pdf->FontStd, 'B', 12);
                $pdf->SetXY($Pos[$Etichetta][0] + $lblMarginH, $Pos[$Etichetta][1] + 8 + $lblMarginV);
                $pdf->Cell($lblW - 10 - 2 * $lblMarginH, 6, $Entry->Athlete, 0, 0, 'L', 0);
                $pdf->SetFont($pdf->FontStd, '', 10);
                $pdf->SetX($pdf->GetX() + 1);
                $pdf->Cell(9, 6, $Entry->NationCode, 0, 0, 'R', 0, '', '1', false, 'T', 'B');

                //Barcode
                $pdf->SetXY($Pos[$Etichetta][0] + $lblMarginH, $Pos[$Etichetta][1] + 15 + $lblMarginV);
                $pdf->SetFont('barcode', '', 23);
                if ($Entry->Bib[0] == '_') $Entry->Bib = 'UU' . substr($Entry->Bib, 1);
                $pdf->Cell($lblW - 2 * $lblMarginH, 10, mb_convert_encoding('*' . $Entry->Bib . '-' . $Entry->DivCode . '-' . $Entry->ClassCode, "UTF-8", "cp1252") . "*", 0, 0, 'C', 0, '', 1, false, 'T', 'T');

                $pdf->SetXY($Pos[$Etichetta][0] + $lblMarginH, $Pos[$Etichetta][1] + $lblH + $lblMarginV - 10);
                $pdf->SetFont($pdf->FontStd, '', 8);
                $pdf->Cell($lblW - 2 * $lblMarginH, 10, $Entry->Bib . '-' . $Entry->DivCode . '-' . $Entry->ClassCode, 0, 0, 'C', 0, '', 1, false, 'T', 'B');

                $Etichetta = ++$Etichetta % $Label4Page;
            }
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
