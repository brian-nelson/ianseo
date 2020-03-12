<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclParticipants, AclReadOnly);
require_once('Common/pdf/LabelPDF.inc.php');
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


$OpDetails = "Accreditation";
if(isset($_REQUEST["OperationType"]))
	$OpDetails = $_REQUEST["OperationType"];

if(CheckTourSession())
{

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
		$lblW= 2.5935*25.4;
		$lblH= 25.4;
		$lblMarginH=2;
		$lblMarginV=2;
		$lblSpaceH=0.15625*25.4;
		$lblSpaceV=0;
		$pageMarginT=0.5*25.4;
		$pageMarginL=0.1875*25.4;
		$Label4Page=30;
		$printBarcode=false;
	}

	$MyQuery = "SELECT EnId, EnName AS Name, upper(EnFirstName) AS FirstName, SUBSTRING(AtTargetNo,1,1) AS Session, SUBSTRING(AtTargetNo,2," . (TargetNoPadding+1) . ") AS TargetNo, CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode, EnDivision AS DivCode, EnStatus as Status, EnIndClEvent AS `IC`, EnTeamClEvent AS `TC`, EnIndFEvent AS `IF`, EnTeamFEvent as `TF`, if(AEId IS NULL, 0, 1) as OpDone ";
	$MyQuery.= "FROM AvailableTarget at ";
	$MyQuery.= "INNER JOIN Qualifications AS q ON at.AtTargetNo=q.QuTargetNo ";
	$MyQuery.= "INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament=at.AtTournament AND EnAthlete=1 ";
	$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
	$MyQuery.= "LEFT JOIN AccEntries AS ae ON e.EnId=ae.AEId AND e.EnTournament=ae.AETournament ";
	$MyQuery.= "AND ae.AEOperation=(SELECT AOTId FROM AccOperationType WHERE AOTDescr=" . StrSafe_DB($OpDetails) . ") ";
	$MyQuery.= "WHERE AtTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
	if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
		$MyQuery .= "AND SUBSTRING(AtTargetNo,1,1) = " . StrSafe_DB($_REQUEST["Session"]) . " ";
		//$MyQuery .= "AND AtTargetNo IN ('145B','145D','146D','149D','151C','152A') ";
	$MyQuery.= "ORDER BY AtTargetNo, CoCode, Name, CoName, FirstName ";

	$Rs=safe_r_sql($MyQuery);
	if($Rs)
	{
		$Etichetta=0;
		while($MyRow=safe_fetch($Rs)) {
			if($Etichetta==0)
				$pdf->AddPage();

			//Cerchia Etichetta
			//$pdf->Rect($pageMarginL+(($Etichetta % $Label4Column) * ($lblW+$lblSpaceH)),$pageMarginT+(intval($Etichetta / $Label4Column) * ($lblH+$lblSpaceV)),$lblW,$lblH,"D");

			$pdf->SetXY(0,0);
			$pdf->SetLeftMargin($pageMarginL+$lblMarginH+(($Etichetta % $Label4Column) * ($lblW+$lblSpaceH)));
			$pdf->SetTopMargin($pageMarginT+$lblMarginV+(intval($Etichetta / $Label4Column) * ($lblH+$lblSpaceV)));

			//Piazzola, Turno & Classe.Divisione
			$pdf->SetFont($pdf->FontStd,'B',20);
			$pdf->Cell(0.25*($lblW-2*($lblMarginH)),8,$MyRow->TargetNo,0,0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->SetXY($pdf->GetX(),$pdf->GetY()+2);
			$pdf->Cell(0.35*($lblW-2*($lblMarginH)),6,get_text('Session') . ": " . $MyRow->Session,0,0,'C',0);
			$pdf->Cell(0.40*($lblW-2*($lblMarginH)),6,$MyRow->DivCode . ' ' . $MyRow->ClassCode,0,1,'R',0);


			//Arciere & Società
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->Cell($lblW-2*($lblMarginH),6,$MyRow->FirstName . ' ' . $MyRow->Name,0,1,'L',0);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell($lblW-2*($lblMarginH),5,$MyRow->NationCode . " - " . $MyRow->Nation,0,1,'L',0);

			//Barcode
			if($printBarcode)
			{
				$pdf->SetFont('barcode','',28);
				if($MyRow->EnId[0]=='_') $MyRow->EnId='UU'.substr($MyRow->EnId, 1);
				$pdf->Cell($lblW-2*($lblMarginH),10, mb_convert_encoding('*$' . $MyRow->EnId,"UTF-8","cp1252") . "*",0,0,'C',0);
			}

/*
			//Status
			if($MyRow->Status>1)
				$pdf->Rect((($Etichetta % 3) * $lblW)+4,(intval($Etichetta / 3) * $lblH)+4,($lblW-8),($lblH-10*$lblSp),"FD");

			//Barcode
			$pdf->SetXY((($Etichetta % 3) * $lblW)+5,(intval($Etichetta / 3) * $lblH)+12*$lblSp);
			$pdf->SetFont('barcode','',28);
			$pdf->Cell($lblW-10,10, mb_convert_encoding('*$' . $MyRow->EnId,"UTF-8","cp1252") . "*",0,0,'C',0);

			//$pdf->Rect((($Etichetta % 3) * 70)+0,(intval($Etichetta / 3) * 37)+0,70,37);

*/
			$Etichetta = ++$Etichetta % $Label4Page;
		}
		safe_free_result($Rs);
	}
	$pdf->Output();
}
?>
