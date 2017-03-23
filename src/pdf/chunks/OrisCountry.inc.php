<?php

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase('Entries');

$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$Version='';
if($PdfData->DocVersion) {
	$Version=trim('Vers. '.$PdfData->DocVersion . " ($PdfData->DocVersionDate) $PdfData->DocVersionNotes");
}
$pdf->setComment($Version);
$pdf->AddPage();
$pdf->Bookmark($PdfData->IndexName, 0);

$ONLINE=isset($PdfData->HTML);

$ACCREDITATION = (!empty($_REQUEST['SinglePage']) and empty($_REQUEST['Athletes']));
$SinglePage=!empty($_REQUEST['SinglePage']);

$lstPictures = array();
$lstDoB = array();

$AddPage=false;

foreach($PdfData->Data['Items'] as $Rows) {
	if($AddPage and $SinglePage) $pdf->addpage();
	$AddPage=true;
	$pdf->SamePage(count($Rows) + 2);
	$pdf->lastY += 3.5;
	$first=true;
	$lstPictures = array();
	$lstDoB = array();
	foreach($Rows as $MyRow) {
// 		if($ONLINE and !$MyRow->IsAthlete) continue;
		$Tgt=ltrim($MyRow->IsAthlete ? (!empty($PdfData->BisTarget) && (intval(substr($MyRow->TargetNo,1)) > $PdfData->NumEnd) ? str_pad((substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd),3,"0",STR_PAD_LEFT) . substr($MyRow->TargetNo,-1,1) . ' bis'  : $MyRow->TargetNo) : '', '0');
		$tmp=array(
			$MyRow->NationCode,
			$MyRow->Nation,
			$MyRow->Athlete,
			$MyRow->IsAthlete && $PdfData->IsRanked ? ($MyRow->Ranking ? $MyRow->Ranking : '-') . '    #' : '',
			$MyRow->DOB . "   #",
			$Tgt.'  #',
			$MyRow->EventName
			);

		if(!$first) {
			$tmp[0]='';
			$tmp[1]='';
		}

		$pdf->printDataRow($tmp);

		$first=false;

		if($ACCREDITATION) {
			if(empty($MyRow->DOB)) {
				$lstDoB[] = array('','',$MyRow->Athlete,str_repeat('_',5),str_repeat('_',25),$Tgt.'  #',$MyRow->EventName);
			}
			if($MyRow->HasPhoto==0) {
				$lstPictures[] = array('', '', $tmp[2], $tmp[3], $tmp[4], $tmp[5], $tmp[6]);
			}
		}

// 		if(!$ONLINE or !$MyRow->IsAthlete) continue;

		$PdfData->HTML['Countries'][$MyRow->NationCode]['Description']=$MyRow->Nation;
		$PdfData->HTML['Countries'][$MyRow->NationCode]['Archers'][]=array(
			$MyRow->Athlete,
			(!empty($PdfData->BisTarget) && (intval(substr($MyRow->TargetNo,1)) > $PdfData->NumEnd) ? 'bis ' . (substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd) . substr($MyRow->TargetNo,-1,1)  : $MyRow->TargetNo),
			$MyRow->EvCode ? $MyRow->EventName : ($MyRow->IsAthlete ? $MyRow->DivDescription . ' ' : '') . $MyRow->ClDescription,
			$MyRow->SesName,
			);
	}

	if(!$ONLINE) {
		//Print Missing DOB
		if(count($lstDoB)) {
			$pdf->addSpacer(10);
			$pdf->printSectionTitle("Missing Date".(count($lstDoB)>1 ? "s" : "")." of Birth");
			foreach($lstDoB as $dobDetail) {
				$pdf->addSpacer();
				$pdf->printDataRow($dobDetail);
			}
		}

		//Print Missing Pictures List
		if(count($lstPictures)) {
			$pdf->addSpacer(10);
			$pdf->printSectionTitle("Missing Picture".(count($lstPictures)>1 ? "s" : ""));
			foreach($lstPictures as $picDetail) {
				$pdf->printDataRow($picDetail);
			}
		}

		if($ACCREDITATION) {
			if(!$pdf->SamePage(6, 4)) {
				$pdf->sety(50);
			}
			// prints some infos to collect
			$LeftOffset=10+$PdfData->HeaderWidth[0]+$PdfData->HeaderWidth[1];
			$Length=$PdfData->HeaderWidth[2]+$PdfData->HeaderWidth[3];
	// 		debug_svela($PdfData->HeaderWidth);
			$pdf->ln();
			$pdf->dy(10);
			$pdf->SetFont('', 'b');
			$pdf->cell(20, 4, 'Email', 0, 0, 'R');
			$pdf->SetX(32);
			$pdf->SetFont('', '');
			$pdf->cell($Length, 4, str_repeat('. ', 60), '', 1);

			$pdf->dy(4);
			$pdf->SetFont('', 'b');
			$pdf->cell(20, 4, 'Local mobile', 0, 0, 'R');
			$pdf->SetX(32);
			$pdf->SetFont('', '');
			$pdf->cell($Length, 4, str_repeat('. ', 60), '', 0);

			$pdf->SetFont('', 'b');
			$pdf->cell(35, 4, 'Home phone contact', 0, 0, 'R');
			$pdf->SetX($Length+69);
			$pdf->SetFont('', '');
			$pdf->cell($Length, 4, str_repeat('. ', 60), '', 1);

			$pdf->dy(4);
			$pdf->SetFont('', 'b');
			$pdf->cell(20, 4, 'Date', 0, 0, 'R');
			$pdf->SetX(32);
			$pdf->SetFont('', '');
			$pdf->cell($Length, 4, str_repeat('. ', 60), '', 0);

// 			$pdf->dy(4);
			$pdf->SetFont('', 'b');
			$pdf->cell(35, 4, 'Team Manager signature', 0, 0, 'R');
			$pdf->SetX($Length+69);
			$pdf->SetFont('', '');
			$pdf->cell($Length, 4, str_repeat('. ', 60), '', 1);

		}
	}
}

?>