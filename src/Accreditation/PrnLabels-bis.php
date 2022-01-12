<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');

$OpDetails = "Accreditation";
if(isset($_REQUEST["OperationType"]))
	$OpDetails = $_REQUEST["OperationType"];

if($_POST) {
	require_once('Common/pdf/LabelPDF.inc.php');
	$IncludePicture=(!empty($_REQUEST['sticker_Picture']));
	$IncludeBarCode=(!empty($_REQUEST['sticker_BarCode']));
	$Unit=$_REQUEST['sticker_Unit'];
	$MarTop=$_REQUEST['sticker_Top'];
	$MarLeftRight=$_REQUEST['sticker_Side'];
	$MarBottom=$_REQUEST['sticker_Bot'];
	$StickRows=$_REQUEST['sticker_Y'];
	$StickCols=$_REQUEST['sticker_X'];
	$ColIntMargin=$_REQUEST['sticker_InterCols'];
	$RowIntMargin=$_REQUEST['sticker_InterRows'];
	$PaperHeight=$_REQUEST['sticker_PageHeight'];
	$PaperWidth=$_REQUEST['sticker_PageWidth'];
	$EnCodeFilter=($_REQUEST['sticker_EnCodes'] ? preg_split('/[ ,]+/', $_REQUEST['sticker_EnCodes']) : '');

	$pdf = new LabelPDF();

	if($IncludePicture) {
		include_once('Common/CheckPictures.php');
		CheckPictures('', true, false, true); // cancella le foto più vecchie di 1 giorno

		// prints all the pictures in the size specified...

		$MySQL="select"
			. " PhEnId, concat(CoCode, ' (', EnCode, '-', EnFirstName, ')') code  "
			. "from"
			. " Photos"
			. " inner join Entries on EnId=PhEnId "
			. " inner join Countries on EnCountry=CoId "
			. " left join Qualifications on EnId=QuId "
			. "where"
			. " EnTournament={$_SESSION['TourId']} "
			. (isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]) ? " AND SUBSTRING(QuTargetNo,1,1) = " . StrSafe_DB($_REQUEST["Session"]) . " " : '')
			. ($EnCodeFilter ? "and EnCode in ('".implode("','", $EnCodeFilter)."')" : "")
			. " and PhPhoto>'' order by code";

		$q=safe_r_sql($MySQL);

		$pdf->addpage();


		$NumCols=floor(($PaperWidth-2*$MarLeftRight)/max(1,$StickCols));
		$HGap=($PaperWidth-2*$MarLeftRight-$NumCols*$StickCols)/($NumCols-1);

		$NumRows=floor(($PaperHeight-$MarTop-$MarTop)/max(1,$StickRows+3));
		$VGap=($PaperHeight-$MarTop-$MarTop-$NumRows*($StickRows+3))/($NumRows-1);

		$X=$MarLeftRight;
		$Y=$MarTop;

		$pdf->SetFontSize(7);

		//error_reporting(E_ALL);

		while($r=safe_fetch($q)) {
			if(!is_file($file= $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-En-'.$r->PhEnId.'.jpg')) {
				continue;
			}
			$pdf->Image($file, $X, $Y, $StickCols, $StickRows, 'JPG', '', '', true, 300, '', false, false, 1, true);
			$pdf->setXY($X, $Y+$StickRows);
			$pdf->cell($StickCols, 3, $r->code, '', '', 'C', '', '', 1);
			$X+=$StickCols+$HGap;

			if($X+$StickCols > $PaperWidth) {
				$X=$MarLeftRight;
				$Y+=$StickRows+$VGap+3;
				if($Y+$StickRows > $PaperHeight) {
					$Y=$MarTop;
					$pdf->addpage();
				}
			}
		}
	} else {
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
		$MyQuery.= "WHERE AtTournament = " . StrSafe_DB($_SESSION['TourId']) . " "
			. ($EnCodeFilter ? "and EnCode in ('".implode("','", $EnCodeFilter)."')" : "");
		if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"])) {
			$MyQuery .= "AND SUBSTRING(AtTargetNo,1,1) = " . StrSafe_DB($_REQUEST["Session"]) . " ";
		}
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
				if($MyRow->EnId[0]=='_') $MyRow->EnId='UU'.substr($MyRow->EnId, 1);
				$pdf->Cell($lblW-10,10, mb_convert_encoding('*$' . $MyRow->EnId,"UTF-8","cp1252") . "*",0,0,'C',0);

				//$pdf->Rect((($Etichetta % 3) * 70)+0,(intval($Etichetta / 3) * 37)+0,70,37);
				$Etichetta = ++$Etichetta % 24;
			}
			safe_free_result($Rs);
		}
	}
	$pdf->Output();
	exit;
}

	include('Common/Templates/head.php');

	$Sql = "SELECT ToPrintPaper  From Tournament WHERE ToId = " . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs = safe_r_sql($Sql);
	$SesNo=0;

	if(safe_num_rows($Rs)==1)
	{
		$r=safe_fetch($Rs);

		$sessions=GetSessions('Q');

		$unit='mm';
		$width='210';
		$height='297';
		if($r->ToPrintPaper) {
			// Letter
			$unit='inch';
			$width='11';
			$height='297';
		}

		print '<form action="" method="POST" target="_blank"><table class="Tabella">'  . "\n";
		print '<tr><th class="Title" colspan="3">' . get_text('PrintList','Tournament')  . '</th></tr>' . "\n";
		echo '<tr valign="top">';
//Etichette
		echo '<td class="Right" nobreak="nobreak">' . get_text('Session') . '</td>';
		echo '<td class="Center" width="1%"><select name="Session">';
		echo '<option value="All">' . get_text('AllSessions','Tournament') . '</option>';
		//for($i=1; $i<=$SesNo; $i++)
			//echo '<option value="' . $i . '">' . $i . '</option>';
		foreach ($sessions as $s)
		{
			echo '<option value="' . $s->SesOrder . '">' . $s->Descr . '</option>';
		}
		echo '</select></td>';
		echo '<td>&nbsp;</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersBarcode','Tournament') . '</td>';
		echo '<td class="Center"><input type="checkbox" name="sticker_BarCode" size="3" /></td>';
		echo '<td>'.get_text('StickersBarcodeDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersPicture','Tournament') . '</td>';
		echo '<td class="Left">';
			echo '<input type="radio" name="sticker_Picture" value="1"/>&nbsp;' . get_text('Yes') . '<br/>';
			echo '<input type="radio" name="sticker_Picture" value="0" checked/>&nbsp;' . get_text('No');
		echo '</td>';
		echo '<td>'.get_text('StickersPictureDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersUnit','Tournament') . '</td>';
		echo '<td class="Left">';
			echo '<input type="radio" name="sticker_Unit" value="mm" '.($unit=='mm' ? 'checked' : '').'/>&nbsp;' . get_text('StickersUnitMm','Tournament') . '<br/>';
			echo '<input type="radio" name="sticker_Unit" value="inch" '.($unit=='inch' ? 'checked' : '').'/>&nbsp;' . get_text('StickersUnitInches','Tournament');
		echo '</td>';
		echo '<td>'.get_text('StickersUnitDesc','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersPageWidth','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_PageWidth" size="3" value="'.$width.'"/></td>';
		echo '<td>'.get_text('StickersPageWidthDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersPageHeight','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_PageHeight" size="3" value="'.$height.'"/></td>';
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
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersCols','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_X" size="3"/></td>';
		echo '<td>'.get_text('StickersColsDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersInterRows','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_InterRows" size="3"/></td>';
		echo '<td>'.get_text('StickersInterRowsDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersInterCols','Tournament') . '</td>';
		echo '<td class="Center"><input name="sticker_InterCols" size="3"/></td>';
		echo '<td>'.get_text('StickersInterColsDescr','Tournament').'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="Right" nobreak="nobreak">' . get_text('StickersCodes','Tournament') . '<div>'.get_text('StickersCodesDescr','Tournament').'</div></td>';
		echo '<td colspan="2" class="Left"><textarea name="sticker_EnCodes" style="width:100%;box-sizing: border-box;height:5em"></textarea></td>';
		echo '</tr>';

		echo '<tr><td colspan="3" class="Center"><input type="submit" value="' . get_text('CmdOk') . '"></td></tr>';

		echo '</table></form>';
	}

	include('Common/Templates/tail.php');

?>
