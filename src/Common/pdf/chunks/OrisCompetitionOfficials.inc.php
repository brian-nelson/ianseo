<?php

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase('Competition Officials');

$Version='';
if($PdfData->DocVersion) {
	$Version=trim('Vers. '.$PdfData->DocVersion . " ($PdfData->DocVersionDate) $PdfData->DocVersionNotes");
}
$pdf->setComment($Version);
$pdf->AddPage();
$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$pdf->Bookmark($PdfData->IndexName, 0);


foreach($PdfData->Data['Items'] as $Group=>$Names) {
	$pdf->SamePage(count($Names), 3.5, $pdf->lastY);
	$pdf->lastY += 3.5;
    $first=true;
	foreach($Names as $name) {
        $tmp=array(
            ($first ? "~".get_text($name->ItDescription, 'Tournament') : ''),
            mb_strtoupper($name->TiName, 'UTF-8') . ' ' . $name->TiGivenName,
            mb_strtoupper($name->CoCode, 'UTF-8'), $name->CoNameComplete,
            "§".($name->TiGender==0 ? 'M' : 'W')
        );
        $first=false;
        $pdf->printDataRow($tmp);
	}
}
