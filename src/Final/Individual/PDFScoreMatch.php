<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/pdf/ResultPDF.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Lib/Fun_PrintOuts.php');

	$pdf = new ResultPDF((get_text('IndFinal')),false);
	$pdf->setBarcodeHeader(70);

	$Score3D = false;
	//$MyQuery = "SELECT (TtElabTeam=2) as is3D FROM Tournament INNER JOIN Tournament*Type AS tt ON ToType=TtId WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
	$MyQuery = "SELECT (ToElabTeam=2) as is3D FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)==1) {
		$r=safe_fetch($Rs);
		$Score3D=$r->is3D;
	}

	if (!$Score3D)
		define("CellH",9);
	else
		define("CellH",6);

	error_reporting(E_ALL);

	$FillWithArrows=false;
	if((isset($_REQUEST["ScoreFilled"]) && $_REQUEST["ScoreFilled"]==1))
		$FillWithArrows=true;

	$pdf->PrintFlags=(!empty($_REQUEST["ScoreFlags"]));

	$MyQuery="";
	if (isset($_REQUEST['Blank']))
	{
		$rows= empty($_REQUEST['Rows'])?5:intval($_REQUEST['Rows']);
		$cols= empty($_REQUEST['Cols'])?3:intval($_REQUEST['Cols']);
		$sots= empty($_REQUEST['SO'])?1:intval($_REQUEST['SO']);
		 $MyQuery = "(SELECT DISTINCT "
			. " '' AS EvCode, '' AS EvEventName, '' AS EvFinalFirstPhase, '0' AS GrPhase,"
			. " 0 AS GrMatchNo, '' AS Athlete,"
			. " '' AS CoCode, '' AS CoName, '' AS IndRank, '' AS FSTarget,"
			. " '' AS FinArrowString, '' AS FinTieBreak, EvMatchMode, IF(EvMatchArrowsNo=0,0,1) AS EvMatchArrowsNo, "
			. " 0 as Score, 0 as FinTie "
			. " , '$rows' CalcEnds "
			. " , '$cols' CalcArrows "
			. " , '$sots' CalcSO "
			. "FROM Events "
			. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0) "
			. "UNION ALL "
			. "(SELECT DISTINCT "
			. " '' AS EvCode, '' AS EvEventName, '' AS EvFinalFirstPhase, '0' AS GrPhase,"
			. " 1 AS GrMatchNo, '' AS Athlete,"
			. " '' AS CoCode, '' AS CoName, '' AS IndRank, '' AS FSTarget,"
			. " '' AS FinArrowString, '' AS FinTieBreak, EvMatchMode, IF(EvMatchArrowsNo=0,0,1) AS EvMatchArrowsNo, "
			. " 0 as Score, 0 as FinTie "
			. " , '$rows' CalcEnds "
			. " , '$cols' CalcArrows "
			. " , '$sots' CalcSO "
			. "FROM Events "
			. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0) "
			. "ORDER BY  EvMatchMode, EvMatchArrowsNo, GrMatchNo";
	}
	else
	{

		$MyQuery = 'SELECT '
			. ' EvCode, EvEventName, EvFinalFirstPhase, GrPhase, '
			. ' GrMatchNo, CONCAT(EnName, \' \', EnFirstName) as Athlete, FinAthlete,'
			. ' CoCode, CoName, IF(EvELim2>0, ElRank, IndRank) AS  IndRank, FSTarget, '
			. ' FinArrowString, FinTieBreak, FinSetScore, EvMatchMode, EvMatchArrowsNo, '
			. ' IF(EvMatchMode=0,FinScore,FinSetScore) AS Score, FinTie, @elimination:=pow(2, ceil(log2(GrPhase))+1) & EvMatchArrowsNo '
			. " , if(@elimination, EvElimEnds, EvFinEnds) CalcEnds "
			. " , if(@elimination, EvElimArrows, EvFinArrows) CalcArrows "
			. " , if(@elimination, EvElimSO, EvFinSO) CalcSO "
			. ' FROM Events'
			. ' INNER JOIN Finals ON EvCode=FinEvent AND EvTournament=FinTournament'
			. ' INNER JOIN Grids ON FinMatchNo=GrMAtchNo AND GrPhase<=(IF(EvFinalFirstPhase=24,32, IF(EvFinalFirstPhase=48,64,EvFinalFirstPhase )))'
			. ' LEFT JOIN Individuals ON FinAthlete=IndId AND FinEvent=IndEvent AND FinTournament=IndTournament'
			. ' LEFT JOIN Entries ON FinAthlete=EnId AND FinTournament=EnTournament'
			. ' LEFT JOIN Qualifications ON QuId=EnId'
			. ' LEFT JOIN Eliminations ON ElId=EnId AND ElElimPhase=1'
			. ' LEFT JOIN Countries on EnCountry=CoId AND EnTournament=CoTournament'
			. ' LEFT JOIN FinSchedule ON FinEvent=FSEvent AND FinMatchNo=FSMatchNo AND FinTournament=FSTournament AND FSTeamEvent=\'0\''
			. ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=0 ';
			if (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']!=-1) {
				$MyQuery .= "AND concat(FSTeamEvent, FSScheduledDate, ' ', FSScheduledTime)=" . strSafe_DB($_REQUEST['x_Session']) . " ";
				$OrderBy='FsTarget, ';
			} else {
				$OrderBy='';
				if (!empty($_REQUEST['Event'])) $MyQuery.= CleanEvents($_REQUEST['Event'], "EvCode") . " ";
				if (isset($_REQUEST['Phase']) && preg_match("/^[0-9]{1,2}$/i",$_REQUEST["Phase"]))
				{
					$p=$_REQUEST['Phase'];
				//	print $p;exit;
					if ($p==24)
					{
						$p=32;
					}
					elseif ($p==48)
					{
						$p=64;
					}
					$MyQuery.= "AND GrPhase = {$p} " ;//. StrSafe_DB($_REQUEST['Phase']) . " ";
					//$MyQuery.= "AND GrPhase = " . StrSafe_DB($_REQUEST['Phase']) . " ";
				}
			}
			$MyQuery .= ' ORDER BY '.$OrderBy.' EvCode, GrPhase DESC, FinMatchNo ASC';
	 }
	//*DEBUG*/echo $MyQuery;exit();

	$Rs=safe_r_sql($MyQuery);
// Se il Recordset è valido e contiene almeno una riga
	if (safe_num_rows($Rs)>0)
	{
		$defGoldW  = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(1/15);
		$defTotalW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(3/15);
		$defArrowTotW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(6/15);

		$WhereStartX=array($pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2);
		$WhereStartY=array(60,60);
		$WhereX=NULL;
		$WhereY=NULL;
		$AtlheteName=NULL;
		$FollowingRows=false;

//DrawScore
		while($MyRow=safe_fetch($Rs))
		{
			$MyRowOpp=safe_fetch($Rs);
			if(empty($_REQUEST["Blank"]) &&  empty($_REQUEST["IncEmpty"]) && ($MyRow->FinAthlete==0 || $MyRowOpp->FinAthlete==0)) {
				// se è vuoto uno dei due arcieri e non è selezionata l'inclusione
				// salta al prossimo record
				continue;
			}

			// disegna lo score di sinistra
			DrawScore($pdf, $MyRow, $MyRowOpp);

			// Disegna lo score di destra
			DrawScore($pdf, $MyRowOpp, $MyRow);

			// print barcode if any
			if(!empty($_REQUEST['Barcode'])) {
				$pdf->setxy($pdf->BarcodeHeaderX, 5);
				$pdf->SetFont('barcode','',25);
				$pdf->Cell($pdf->BarcodeHeader, 15, '*' . mb_convert_encoding($MyRow->GrMatchNo.'-0-'.$MyRow->EvCode, "UTF-8","cp1252") . "*",0,1,'C',0);
				$pdf->SetFont($pdf->FontStd,'',10);
				$pdf->setxy($pdf->BarcodeHeaderX, 16);
				$pdf->Cell($pdf->BarcodeHeader, 4, mb_convert_encoding($MyRow->GrMatchNo.'-0-'.$MyRow->EvCode, "UTF-8","cp1252"),0,1,'C',0);
			} else {
				$pdf->setBarcodeHeader(10);
			}

			if(!empty($_REQUEST['QRCode'])) {
				foreach($_REQUEST['QRCode'] as $k => $Api) {
					require_once('Api/'.$Api.'/DrawQRCode.php');
					$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
					$Function($pdf, $pdf->BarcodeHeaderX -(15 * ($k+1)), 5, $MyRow->EvCode, $MyRow->GrMatchNo, $MyRow->GrPhase, 0, "MI");
				}
			}
		}

//END OF DrawScore
		safe_free_result($Rs);
	}

$pdf->Output();

function DrawScore(&$pdf, $MyRow, $MyRowOpp) {
	global $CFG, $defTotalW, $defGoldW, $defArrowTotW, $FollowingRows, $TrgOutdoor, $WhereStartX, $WhereStartY, $Score3D, $FillWithArrows;

	$NumRow=$MyRow->CalcEnds;
	$NumCol=$MyRow->CalcArrows;
	$ArrowW = $defArrowTotW/$NumCol;
	$TotalW=$defTotalW;
	$GoldW=$defGoldW;

//		echo $MyRow->EvMatchArrowsNo . "." . $MyRow->GrPhase ."." . ($MyRow->EvMatchArrowsNo & ($MyRow->GrPhase>0 ? $MyRow->GrPhase*2:1)) . "/" . $NumRow . "--<br>";
	if($MyRow->GrMatchNo%2 == 0 && $FollowingRows)
		$pdf->AddPage();

	$FollowingRows=true;
	$WhichScore=($MyRow->GrMatchNo%2);
	$WhereX=$WhereStartX;
	$WhereY=$WhereStartY;
//Intestazione Atleta
	$pdf->SetLeftMargin($WhereStartX[$WhichScore]);
	$pdf->SetY(35);
// Flag of Country/Club
	if($pdf->PrintFlags) {
		if(is_file($file= $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$MyRow->CoCode.'.jpg')) {
			$H=12;
			$W=18;
			$OrgY=$pdf->gety();
			$OrgX=$NumCol*$ArrowW+$TotalW+$GoldW+$TotalW-18;
			$pdf->Image($file, $pdf->getx()+$OrgX, $OrgY, $W, $H, 'JPG', '', '', true, 300, '', false, false, 1, true);
			$FlagOffset=$W+1;
		}
	}

	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,6,(get_text('Athlete')) . ': ', 'TL', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($NumCol*$ArrowW+2*$TotalW+$GoldW-20-($pdf->PrintFlags?18:0),6,($MyRow->Athlete), 'T', 1, 'L', 0);
   	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,6,(get_text('Country')) . ': ', 'L', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($NumCol*$ArrowW+2*$TotalW+$GoldW-20,6, ($MyRow->CoName . (strlen($MyRow->CoCode)>0 ?  ' (' . $MyRow->CoCode  . ')' : '')), 0, 1, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,6,(get_text('DivisionClass')) . ': ', 'LB', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($NumCol*$ArrowW+$TotalW+$GoldW-20,6, get_text($MyRow->EvEventName,'','',true), 'B', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($TotalW,6, (get_text('Target')) . ' ' . $MyRow->FSTarget, '1', 1, 'C', 1);


	$pdf->SetXY($NumCol*$ArrowW+2*$TotalW+$GoldW+$WhereStartX[$WhichScore],35);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(2*$GoldW,6, (get_text('Rank')),'TLR',1,'C',1);
	$pdf->SetXY($NumCol*$ArrowW+2*$TotalW+$GoldW+$WhereStartX[$WhichScore],$pdf->GetY());
	$pdf->SetFont($pdf->FontStd,'B',25);
	$pdf->Cell(2*$GoldW,12, $MyRow->IndRank,'BLR',1,'C',1);

//Header
   	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell($GoldW,CellH,'',0,0,'C',0);
	$pdf->Cell(2*$GoldW+2*$TotalW+$NumCol*$ArrowW,CellH, get_text(namePhase($MyRow->EvFinalFirstPhase,$MyRow->GrPhase). '_Phase'),1,0,'C',1);
//Winner Checkbox
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell(2*$GoldW,CellH,'',0,0,'C',0);
	//$pdf->Rect($WhereX[$WhichScore]+$GoldW+2,$WhereY[$WhichScore]+2,$GoldW-4,CellH-4,'DF',array(),array(255,255,255));
	$pdf->Rect($WhereX[$WhichScore]+$GoldW+2,$WhereY[$WhichScore]+2,CellH-4,CellH-4,'DF',array(),array(255,255,255));
	if($FillWithArrows && ($MyRow->Score > $MyRowOpp->Score || ($MyRow->Score == $MyRowOpp->Score && $MyRow->FinTie > $MyRowOpp->FinTie )))
	{
		$tmpWidth=$pdf->GetLineWidth();
		$pdf->SetLineWidth($tmpWidth*5);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+1,$WhereY[$WhichScore]+1,$WhereX[$WhichScore]+2*$GoldW-1,$WhereY[$WhichScore]+CellH-1);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+1,$WhereY[$WhichScore]+CellH-1,$WhereX[$WhichScore]+2*$GoldW-1,$WhereY[$WhichScore]+1);
		$pdf->SetLineWidth($tmpWidth);
	}
	$pdf->SetDefaultColor();
	$pdf->Cell($GoldW+2*$TotalW+$NumCol*$ArrowW,CellH, get_text('Winner'),0,1,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY();

	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell($GoldW,CellH,'',0,0,'C',0);
	if($Score3D)
	{
		$pdf->Cell($NumCol*$ArrowW,CellH, get_text('Arrow'), 1, 0, 'C', 1);
	}
	else
	{
		for($j=0; $j<$NumCol; $j++)
			$pdf->Cell($ArrowW,CellH, ($j+1), 1, 0, 'C', 1);
	}
	$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),CellH, get_text(($MyRow->EvMatchMode==0 ? 'TotalProg':'SetTotal'),'Tournament'),1,0,'C',1);
	$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),CellH, get_text('RunningTotal','Tournament'),1,0,'C',1);

	if($MyRow->EvMatchMode==0)
	{
		$pdf->Cell($GoldW,CellH,($pdf->prnGolds),1,0,'C',1);
		$pdf->Cell($GoldW,CellH,($pdf->prnXNine),1,1,'C',1);
	}
	else
	{
		$pdf->Cell(2*$GoldW,CellH,get_text('SetPoints', 'Tournament'),1,0,'C',1);
		$pdf->Cell(2/5*$TotalW,CellH,get_text('TotalShort','Tournament'),1,1,'C',1);
	}
	$WhereY[$WhichScore]=$pdf->GetY();
//Righe
	$ScoreTotal = 0;
	$ScoreGold = 0;
	$ScoreXnine = 0;
	$SetTotal = '';
	for($i=1; $i<=$NumRow; $i++)
	{
		$ScoreEndTotal = 0;
		$ScoreEndGold = 0;
		$ScoreEndXnine = 0;
	   	$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
		$pdf->Cell($GoldW,CellH,$i,1,0,'C',1);
		$pdf->SetFont($pdf->FontStd,'',10);

		if($Score3D)
		{
			$pdf->Cell($ArrowW/5,CellH, '11', 1, 0, 'C', 0);
			$pdf->Cell($ArrowW/5,CellH, '10', 1, 0, 'C', 0);
			$pdf->Cell($ArrowW/5,CellH, '8', 1, 0, 'C', 0);
			$pdf->Cell($ArrowW/5,CellH, '5', 1, 0, 'C', 0);
			$pdf->Cell($ArrowW/5,CellH, 'M', 1, 0, 'C', 0);
		}
		else
		{
			for($j=0; $j<$NumCol; $j++)
				$pdf->Cell($ArrowW,CellH,($FillWithArrows ? DecodeFromLetter(substr($MyRow->FinArrowString,($i-1)*$NumCol+$j,1)) : ''),1,0,'C',0);

			$IsEndScore= trim(substr($MyRow->FinArrowString, ($i-1)*$NumCol, $NumCol));
			list($ScoreEndTotal,$ScoreEndGold, $ScoreEndXnine) = ValutaArrowStringGX(substr($MyRow->FinArrowString,($i-1)*$NumCol,$NumCol),$pdf->goldsChars,$pdf->xNineChars);
			$ScoreTotal += $ScoreEndTotal;
			$ScoreGold += $ScoreEndGold;
			$ScoreXnine += $ScoreEndXnine;
		}
		$pdf->SetFont($pdf->FontStd,'', ($MyRow->EvMatchMode==0 ? 10 : 12));
		$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),CellH,($FillWithArrows && $IsEndScore ? $ScoreEndTotal : ''),1,0,'C',0);
		$pdf->SetFont($pdf->FontStd,'', ($MyRow->EvMatchMode==0 ? 12 : 10));
		$pdf->Cell($TotalW* ($MyRow->EvMatchMode==0 ? 1:4/5),CellH,($FillWithArrows && $IsEndScore ? $ScoreTotal : ''),1,0,'C',0);
		if($MyRow->EvMatchMode==0)
		{
			$pdf->SetFont($pdf->FontStd,'',9);
			$pdf->Cell($GoldW,CellH,($FillWithArrows && $IsEndScore ? $ScoreEndGold : ''),1,0,'C',0);
			$pdf->Cell($GoldW,CellH,($FillWithArrows && $IsEndScore ? $ScoreEndXnine : ''),1,1,'C',0);
		}
		else
		{
			$SetTotSx = '';
			if($IsEndScore && $FillWithArrows) {
				$SetPointSx= ValutaArrowString(substr($MyRow->FinArrowString, ($i-1)*$NumCol, $NumCol));
				$SetPointDx= ValutaArrowString(substr($MyRowOpp->FinArrowString, ($i-1)*$NumCol, $NumCol));

				if($SetPointSx > $SetPointDx) {
					$SetTotSx= 2;
				} elseif($SetPointSx < $SetPointDx) {
					$SetTotSx= 0;
				} else {
					$SetTotSx= 1;
				}
				$SetTotal = intval($SetTotal) + $SetTotSx;
			}

			$pdf->SetFont($pdf->FontStd,'B',11);
			if($SetTotSx==2 && $FillWithArrows)
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+CellH/2, $GoldW/3, 0, 360, 'FD');
			$pdf->Cell((2*$GoldW)/3,CellH,'2',1, 0,'C',0);
			if($SetTotSx==1 && $FillWithArrows)
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+CellH/2, $GoldW/3, 0, 360, 'FD');
			$pdf->Cell((2*$GoldW)/3,CellH,'1',1, 0,'C',0);
			if($SetTotSx==0 && $IsEndScore && $FillWithArrows)
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+CellH/2, $GoldW/3, 0, 360, 'FD');
			$pdf->Cell((2*$GoldW)/3,CellH,'0',1, 0,'C',0);
			$pdf->Cell($TotalW * 2/5,CellH,($IsEndScore && $FillWithArrows ? $SetTotal : ''),1, 1,'C',0);


		}
		$WhereY[$WhichScore]=$pdf->GetY();
	}
//Shoot Off
	$closeToCenter=false;
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]+(CellH/4));
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell($GoldW,CellH*11/8,(get_text('TB')),1,0,'C',1);
	$ShootOffW=($MyRow->CalcSO<=$NumCol ? $ArrowW : ($ArrowW*$NumCol)/$MyRow->CalcSO);
	for($j=0; $j<$MyRow->CalcSO; $j++)
	{
		$pdf->SetXY($pdf->GetX()+0.5,$pdf->GetY());
		$pdf->SetFont($pdf->FontStd,'',10);
		$pdf->Cell($ShootOffW-0.5,CellH*3/4,($FillWithArrows ? DecodeFromLetter(substr($MyRow->FinTieBreak,$j,1)) : ''),1,0,'C',0);
		if(substr(($FillWithArrows ? DecodeFromLetter(substr($MyRow->FinTieBreak,$j,1)) : ''),-1,1)=="*")
			$closeToCenter=true;
	}
	if($NumCol>$j)
		$pdf->Cell($ArrowW*($NumCol-$j),CellH*3/4,'',0,0,'L',0);
//Totale
	$pdf->SetXY($pdf->GetX(),$WhereY[$WhichScore]);
	$pdf->SetFont($pdf->FontStd,'B',10);
	if($MyRow->EvMatchMode==0)
	{
		$pdf->Cell($TotalW,CellH,get_text('Total'),0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',11);
		$pdf->Cell($TotalW,CellH,($FillWithArrows ? $ScoreTotal : ''),1,0,'C',0);
		$pdf->SetFont($pdf->FontStd,'',10);
		$pdf->Cell($GoldW,CellH,($FillWithArrows ? $ScoreGold : ''),1,0,'C',0);
		$pdf->Cell($GoldW,CellH,($FillWithArrows ? $ScoreXnine : ''),1,1,'C',0);
	}
	else
	{
		$pdf->Cell($TotalW * 8/5,CellH,'',0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->Cell(2*$GoldW,CellH,get_text('Total'),0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',14);
		$pdf->Cell(2/5*$TotalW,CellH,($FillWithArrows ? $MyRow->FinSetScore : ''),1,1,'C',0);
	}
	$WhereY[$WhichScore]=$pdf->GetY();
//Closet to the center
	$pdf->SetFont($pdf->FontStd,'',9);
	$pdf->SetXY($WhereX[$WhichScore]+$GoldW+$ShootOffW/2,$WhereY[$WhichScore]+CellH/8);
	$pdf->Cell($ShootOffW/2,CellH/2,'',1,0,'R',0);
	if($closeToCenter)
	{
		$tmpWidth=$pdf->GetLineWidth();
		$pdf->SetLineWidth($tmpWidth*5);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+$ShootOffW/2-1,$WhereY[$WhichScore]+CellH/8-1,$WhereX[$WhichScore]+$GoldW+$ShootOffW+1,$WhereY[$WhichScore]+CellH*5/8+1);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+$ShootOffW/2-1,$WhereY[$WhichScore]+CellH*5/8+1,$WhereX[$WhichScore]+$GoldW+$ShootOffW+1,$WhereY[$WhichScore]+CellH/8-1);
		$pdf->SetLineWidth($tmpWidth);
	}
	$pdf->Cell($ArrowW*($NumCol-1),CellH*2/4,get_text('Close2Center','Tournament'),0,0,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY()+10;
//Firme
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
   	$pdf->SetFont($pdf->FontStd,'I',7);
	$pdf->Cell(3*$GoldW+2*$TotalW+$NumCol*$ArrowW,4,(get_text('Archer')),'B',0,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY()+10;
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell(3*$GoldW+2*$TotalW+$NumCol*$ArrowW,4,(get_text('Scorer')),'B',0,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY()+15;
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell(3*$GoldW+2*$TotalW+$NumCol*$ArrowW,4,(get_text('JudgeNotes')),'B',0,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY();
}
?>
