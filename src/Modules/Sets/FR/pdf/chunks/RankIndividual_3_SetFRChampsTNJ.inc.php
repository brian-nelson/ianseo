<?php

$parentList = array();
$PdfData->LastUpdate=$PdfData->rankData['meta']['lastUpdate'];
$pdf->setDocUpdate($PdfData->LastUpdate);
$LastCell=$pdf->getPageWidth()-88;

$FirstPage=true;
foreach($PdfData->rankData['sections'] as $Event => $section) {
    if($section['meta']['parent']=='' OR !array_key_exists($section['meta']['parent'],$parentList)) {
        //Se Esistono righe caricate....
        if (count($section['items'])) {
            if (!$FirstPage) $pdf->AddPage();
            $FirstPage = false;

            $NeedTitle = true;
            foreach ($section['items'] as $item) {
                if (!$pdf->SamePage(4)) $NeedTitle = true;

                //Valuto Se è necessario il titolo
                if ($NeedTitle) {
                    // testastampa
                    if ($section['meta']['printHeader']) {
                        $pdf->SetFont($pdf->FontStd, 'B', 10);
                        $pdf->Cell(0, 7.5, $section['meta']['printHeader'], 0, 1, 'R', 0);
                    }
                    // Titolo della tabella
                    $pdf->SetFont($pdf->FontStd, 'B', 10);
                    $pdf->Cell(0, 7.5, $section['meta']['descr'], 1, 1, 'C', 1);
                    // Header vero e proprio
                    $pdf->SetFont($pdf->FontStd, 'B', 7);
                    $pdf->Cell(8, 5, $section['meta']['fields']['rank'], 1, 0, 'C', 1);
                    $pdf->Cell(60, 5, $section['meta']['fields']['athlete'], 1, 0, 'C', 1);
                    $pdf->Cell($LastCell, 5, $section['meta']['fields']['countryName'], 1, 1, 'C', 1);
                    $NeedTitle = false;
                }


                $pdf->SetFont($pdf->FontStd, 'B', 8);
                $pdf->Cell(8, 4, ($item['rank'] ? $item['rank'] : ''), 1, 0, 'C', 0);
                $pdf->SetFont($pdf->FontStd, '', 8);
                $pdf->Cell(60, 4, $item['athlete'], 'RBT', 0, 'L', 0);
                $pdf->Cell(10, 4, $item['countryCode'], 'LTB', 0, 'C', 0);
                $pdf->Cell($LastCell - 10, 4, $item['countryName'], 'RTB', 1, 'L', 0);
            }
        }
    }
    $parentList[$Event] = $Event;
}
