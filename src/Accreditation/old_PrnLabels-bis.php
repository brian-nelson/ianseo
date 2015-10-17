<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
require_once('Common/Fun_FormatText.inc.php');

$OpDetails = "Accreditation";
if(isset($_REQUEST["OperationType"]))
	$OpDetails = $_REQUEST["OperationType"];

if($_POST) {
	require_once('Common/pdf/LabelPDF.inc.php');
	$IncludeBarCode=true;
	$MarLeftRight=0;
	$MarTop=0;
	$MarBottom=0;
	$StickXRows=0;
	$StickXCols=0;
	$ColIntMargin=0;
	$RowIntMargin=0;
	$PaperHeight=0;
	$PaperWidth=0;




	$pdf = new LabelPDF();
	$lblW= $pdf->GetPageWidth()/3;
	$lblH= $pdf->GetPageHeight()/8;
	$lblSp=$lblH*0.05;

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
			//$pdf->Rect((($Etichetta % 3) * $lblW),(intval($Etichetta / 3) * $lblH),$lblW,$lblH,"D");

			//Status
			if($MyRow->Status>1)
				$pdf->Rect((($Etichetta % 3) * $lblW)+4,(intval($Etichetta / 3) * $lblH)+4,($lblW-8),($lblH-10*$lblSp),"FD");

			//Piazzola, Turno & Classe.Divisione
			$pdf->SetXY((($Etichetta % 3) * $lblW)+5,(intval($Etichetta / 3) * $lblH)+2*$lblSp);
			$pdf->SetFont($pdf->FontStd,'B',20);
			$pdf->Cell(15,8,$MyRow->TargetNo,0,0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->SetXY($pdf->GetX(),(intval($Etichetta / 3) * $lblH)+3*$lblSp);
			$pdf->Cell(25,6,get_text('Session') . ": " . $MyRow->Session,0,0,'C',0);
			$pdf->Cell($lblW-50,6,$MyRow->DivCode . ' ' . $MyRow->ClassCode,0,0,'R',0);

			//Arciere & Società
			$pdf->SetXY((($Etichetta % 3) * $lblW)+5,(intval($Etichetta / 3) * $lblH)+6*$lblSp);
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->Cell($lblW-10,6,$MyRow->FirstName . ' ' . $MyRow->Name,0,0,'L',0);
			$pdf->SetXY((($Etichetta % 3) * $lblW)+5,(intval($Etichetta / 3) * $lblH)+9*$lblSp);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell($lblW-10,5,$MyRow->NationCode . " - " . $MyRow->Nation,0,0,'L',0);

			//Barcode
			$pdf->SetXY((($Etichetta % 3) * $lblW)+5,(intval($Etichetta / 3) * $lblH)+12*$lblSp);
			$pdf->SetFont('barcode','',28);
			$pdf->Cell($lblW-10,10, mb_convert_encoding('*$' . $MyRow->EnId,"UTF-8","cp1252") . "*",0,0,'C',0);

			//$pdf->Rect((($Etichetta % 3) * 70)+0,(intval($Etichetta / 3) * 37)+0,70,37);
			$Etichetta = ++$Etichetta % 24;
		}
		safe_free_result($Rs);
	}
	$pdf->Output();
	exit;
}

	include('Common/Templates/head.php');

	$Sql = "SELECT ToPrintPaper, ToNumSession From Tournament WHERE ToId = " . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs = safe_r_sql($Sql);
	$SesNo=1;

	if(safe_num_rows($Rs)==1 && ($r=safe_fetch($Rs)) && $SesNo=$r->ToNumSession)
	{
		//$SmallCellW = ceil(100/$NumOp);
		$unit='mm';
		$width='210';
		$height='297';
		if($r->toPrintPaper) {
			// Letter
			$unit='inch';
			$width='11';
			$height='297';
		}

		print '<form action="" method="get" target="PrintOut"><table class="Tabella">'  . "\n";
		print '<tr><th class="Title" colspan="3">' . get_text('PrintList','Tournament')  . '</th></tr>' . "\n";
		echo '<tr valign="top">';
//Etichette
		echo '<td class="Right" nobreak="nobreak">' . get_text('Session') . '</td>';
		echo '<td class="Center" width="1%"><select name="Session">';
		echo '<option value="All">' . get_text('AllSessions','Tournament') . '</option>';
		for($i=1; $i<=$SesNo; $i++)
			echo '<option value="' . $i . '">' . $i . '</option>';
		echo '</select></td>';
		echo '<td>&nbsp;</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersBarcode','Tournament') . '</td>';
		echo '<td class="Center"><input type="checkbox" name="sticker_BarCode" size="3"/></td>';
		echo '<td>'.get_text('StickersBarcodeDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersUnit','Tournament') . '</td>';
		echo '<td class="Left">';
			echo '<input type="radio" name="sticker_Unit" value="mm" checked/>&nbsp;' . get_text('StickersUnitMm','Tournament') . '<br/>';
			echo '<input type="radio" name="sticker_Unit" value="mm"/>&nbsp;' . get_text('StickersUnitInches','Tournament');
		echo '</td>';
		echo '<td>'.get_text('StickersUnitDesc','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersPageWidth','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_PageWidth" size="3"/></td>';
		echo '<td>'.get_text('StickersPageWidthDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersPageHeight','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_PageHeight" size="3"/></td>';
		echo '<td>'.get_text('StickersPageHeightDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersTopMargin','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_Top" size="3"/></td>';
		echo '<td>'.get_text('StickersTopMarginDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersLeftRightMargin','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_Side" size="3"/></td>';
		echo '<td>'.get_text('StickersLeftRightMarginDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersBotMargin','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_Bot" size="3"/></td>';
		echo '<td>'.get_text('StickersBotMarginDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersRows','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_Y" size="3"/></td>';
		echo '<td>'.get_text('StickersRowsDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersInterRows','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_InterRows" size="3"/></td>';
		echo '<td>'.get_text('StickersInterRowsDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersCols','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_X" size="3"/></td>';
		echo '<td>'.get_text('StickersColsDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersInterCols','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_InterCols" size="3"/></td>';
		echo '<td>'.get_text('StickersInterColsDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr><td colspan="3" class="Center"><input type="submit" name="Submit" value="' . get_text('CmdOk') . '"></td></tr>';

		echo '</table></form>';
	}

	include('Common/Templates/tail.php');

?>