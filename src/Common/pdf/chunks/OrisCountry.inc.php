<?php

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase('Entries');

$Version='';
if($PdfData->DocVersion) {
	$Version=trim('Vers. '.$PdfData->DocVersion . " ($PdfData->DocVersionDate) $PdfData->DocVersionNotes");
}
$pdf->setComment($Version);
$pdf->AddPage();
$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
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
	$pdf->SamePage(count($Rows), 3.5, $pdf->lastY);
	$pdf->lastY += 3.5;
	$first=true;
	$lstPictures = array();
	$lstRetakes = array();
	$lstDoB = array();
	$NationCode='';
	foreach($Rows as $MyRow) {
// 		if($ONLINE and !$MyRow->IsAthlete) continue;
		$Tgt=ltrim($MyRow->IsAthlete ? (!empty($PdfData->BisTarget) && (intval(substr($MyRow->TargetNo,1)) > $PdfData->NumEnd) ? str_pad((substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd),3,"0",STR_PAD_LEFT) . substr($MyRow->TargetNo,-1,1) . ' bis'  : $MyRow->TargetNo) : '', '0');
		$NationCode=$MyRow->NationCode;
		$tmp=array(
			$MyRow->NationCode,
			$MyRow->NationComplete,
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
				if($MyRow->HasAccreditation) {
					$lstRetakes[] = array('', '', $tmp[2], $tmp[3], $tmp[4], $tmp[5], $tmp[6]);
				} else {
					$lstPictures[] = array('', '', $tmp[2], $tmp[3], $tmp[4], $tmp[5], $tmp[6]);
				}
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

		//Print Missing Pictures List
		if(count($lstRetakes)) {
			$pdf->addSpacer(10);
			$pdf->printSectionTitle("Picture".(count($lstRetakes)>1 ? "s" : "").' to Retake');
			foreach($lstRetakes as $picDetail) {
				$pdf->printDataRow($picDetail);
			}
		}

		if($ACCREDITATION) {
			if(!$pdf->SamePage(6, 4)) {
				$pdf->sety(50);
			} else {
				$pdf->dy(10);
			}

			// prints some infos to collect
			$pdf->printSectionTitle("Contacts");
			$pdf->ln(8);

			$Offset=32;
			$Width=($pdf->getPageWidth()-55-$Offset)/3;
			// header:
			$pdf->SetFont('', 'b');
			$pdf->SetX($Offset);
			$pdf->cell(15, 4, 'Preferred', 0, 0, 'C');
			$pdf->cell(10, 4, '', 0, 0, 'C');
			$pdf->cell($Width, 4, 'Contact Person', 0, 0, 'C');
			$pdf->cell(10, 4, '', 0, 0, 'C');
			$pdf->cell($Width, 4, 'Contact Email', 0, 0, 'C');
			$pdf->cell(10, 4, '', 0, 0, 'C');
			$pdf->cell($Width, 4, 'Contact Phone', 0, 1, 'C');
			$pdf->SetFont('', '');

			// get the contact person
			$BottomLine=array('B' => array('dash' => '1,2'));
			$Sql="select ExtraDataCountries.* 
				from ExtraDataCountries 
				inner join Countries on CoId=EdcId
				where EdcType='E' and CoTournament={$_SESSION['TourId']}  and CoCode=".StrSafe_DB($NationCode)."
				order by EdcEvent='P' desc";
			$q=safe_r_sql($Sql);
			while($r=safe_fetch($q)) {
				foreach(unserialize($r->EdcExtra) as $Data) {
					$pdf->SetX($Offset);
					$pdf->cell(15, 6, $r->EdcEvent=='P' ? 'X' : '', $BottomLine, 0, 'C', 0, '', 1, false, 'T', 'B');
					$pdf->cell(10, 6, '', 0, 0, 'C');
					$pdf->cell($Width, 6, $Data['FamilyName'] . ' ' . $Data['GivenName'], $BottomLine, 0, 'L', 0, '', 1, false, 'T', 'B');
					$pdf->cell(10, 6, '', 0, 0, 'C');
					$pdf->cell($Width, 6, $Data['Email'], $BottomLine, 0, 'L', 0, '', 1, false, 'T', 'B');
					$pdf->cell(10, 6, '', 0, 0, 'C');
					$pdf->cell($Width, 6, $Data['Phone'], $BottomLine, 1, 'L', 0, '', 1, false, 'T', 'B');
				}
			}

			$pdf->SetX($Offset);
			$pdf->cell(15, 10, '', $BottomLine, 0, 'C');
			$pdf->cell(10, 10, '', 0, 0, 'C');
			$pdf->cell($Width, 10, '', $BottomLine, 0, 'L');
			$pdf->cell(10, 10, '', 0, 0, 'C');
			$pdf->cell($Width, 10, '', $BottomLine, 0, 'L');
			$pdf->cell(10, 10, '', 0, 0, 'C');
			$pdf->cell($Width, 10, '', $BottomLine, 1, 'L');

			$Length=$PdfData->HeaderWidth[2]+$PdfData->HeaderWidth[3];

			$pdf->dy(10);
			$pdf->SetFont('', 'b');
			$pdf->SetX($Offset);
			$pdf->cell(25, 4, 'Date', 0, 0, 'R');
			$pdf->cell($Width, 4, '', $BottomLine, 0);
			$pdf->cell(10, 4, '', 0, 0, 'C');

			$pdf->cell($Width+10, 4, 'Team Manager signature', 0, 0, 'R');
			$pdf->cell($Width, 4, '', $BottomLine, 1);

		}
	}
}

?>
