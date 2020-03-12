<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/pdf/ResultPDF.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Fun_CONI.local.inc.php');

	$p=$_REQUEST['Phase']==1 ? get_text('FirstPhase','Tournament') : get_text('SecondPhase','Tournament');

	$pdf = new ResultPDF((get_text('GroupMatches','Tournament') . ' - ' . $p),false);

	define("ArrowW",11);
	define("TotalW",20);
	define("GoldW",7);
	define("CellH",12);
	define("NumColField",3);

	$MyQuery="";
	$Rs = null;
	if (isset($_REQUEST['Blank']))
	{
		$MyQuery = "SELECT '' AS TargetNo1, '' AS TargetNo2, '' AS EventName, "
				. "'' AS Match1, '' AS Match2, '' AS `Group`, '' AS Round, '' AS EventCode, "
				. "'' AS TeamCode1, '' AS SubTeamCode1, '' AS CountryCode1, '' AS CountryName1, "
				. "'' AS Score1, '' AS Tie1, '' AS Tiebreak1, '' AS ArrowString1, "
				. "'' AS TeamCode2, '' AS SubTeamCode2, '' AS CountryCode2, '' AS CountryName2, "
				. "'' AS Score2, '' AS Tie2, '' AS Tiebreak2, '' AS ArrowString2 ";
		$Rs=safe_r_sql($MyQuery);
	}
	else
		$Rs=getMatchesPhase1($_REQUEST["Event"],$_REQUEST["Round"],$_REQUEST["Phase"],"");
// Se il Recordset � valido e contiene almeno una riga
	if (safe_num_rows($Rs)>0)
	{
		$WhereStartX=array(10,160);
		$WhereStartY=array(60,60);
		$WhereX=NULL;
		$WhereY=NULL;
		$AtlheteName=NULL;
		//$NumRow=4;
		$FollowingRows=false;
//DrawScore
		while($MyRow=safe_fetch($Rs))
		{
			if($FollowingRows)
				$pdf->AddPage();
			$FollowingRows=true;

			$NumCol = 4;
			$ColWidth = (ArrowW*6) / $NumCol;
			$NumRows = 4;
			$TmpCellH = CellH;

			for($WhichScore=0; $WhichScore<=1; $WhichScore++)
			{
				$WhereX=$WhereStartX;
				$WhereY=$WhereStartY;
	//Intestazione Atleta
				$pdf->SetLeftMargin($WhereStartX[$WhichScore]);
				$pdf->SetY(35);
			   	$pdf->SetFont($pdf->FontStd,'',10);
				$pdf->Cell(20,7,(get_text('Country')) . ': ', 'LT', 0, 'L', 0);
				$pdf->SetFont($pdf->FontStd,'B',10);
				$pdf->Cell($NumCol*$ColWidth+2*TotalW+GoldW-20,7, ($MyRow->{"CountryName" . ($WhichScore+1)} . ($MyRow->{"SubTeamCode" . ($WhichScore+1)}>1 ?  ' ' . $MyRow->{"SubTeamCode" . ($WhichScore+1)}  : '' ) . (strlen($MyRow->{"CountryCode" . ($WhichScore+1)})>0 ?  ' (' . $MyRow->{"CountryCode" . ($WhichScore+1)}  . ')' : '')), 'T', 1, 'L', 0);
				$pdf->SetFont($pdf->FontStd,'',10);
				$pdf->Cell(20,7,(get_text('DivisionClass')) . ': ', 'LB', 0, 'L', 0);
				$pdf->SetFont($pdf->FontStd,'B',10);
				$pdf->Cell($NumCol*$ColWidth+TotalW+GoldW-20,7, get_text($MyRow->EventName,'','',true), 'B', 0, 'L', 0);
				$pdf->SetFont($pdf->FontStd,'B',8);
				$pdf->Cell(TotalW,7, (get_text('Target')) . ' ' . $MyRow->{"TargetNo" . ($WhichScore+1)}, '1', 1, 'C', 1);

				$pdf->SetXY($NumCol*$ColWidth+2*TotalW+GoldW+$WhereStartX[$WhichScore],35);
				$pdf->SetFont($pdf->FontStd,'B',8);
				$pdf->Cell(2*GoldW,5, (get_text('Rank')),'TLR',1,'C',1);
				$pdf->SetXY($NumCol*$ColWidth+2*TotalW+GoldW+$WhereStartX[$WhichScore],$pdf->GetY());
				$pdf->SetFont($pdf->FontStd,'B',20);
				$pdf->Cell(2*GoldW,9, $MyRow->{"Match" . ($WhichScore+1)},'BLR',1,'C',1);
	//Header
			   	$pdf->SetFont($pdf->FontStd,'B',10);
				$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
				$pdf->Cell(GoldW,CellH,'',0,0,'C',0);
				$pdf->Cell(2*GoldW+2*TotalW+$NumCol*$ColWidth,CellH,($MyRow->Round>0 ? get_text('Group#','Tournament',chr(64+($_REQUEST["Phase"]==1?0:4)+$MyRow->Group)) . ' - ' . get_text('Round#','Tournament',$MyRow->Round)  : ''),1,1,'C',1);
				$WhereY[$WhichScore]=$pdf->GetY();

				$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
				$pdf->Cell(GoldW,CellH,'',0,0,'C',0);
				for($j=0; $j<$NumCol; $j++)
					$pdf->Cell($ColWidth,CellH, ($j+1), 1, 0, 'C', 1);
				$pdf->Cell(TotalW,CellH,(get_text('TotalProg','Tournament')),1,0,'C',1);
				$pdf->Cell(TotalW,CellH,(get_text('TotalShort','Tournament')),1,0,'C',1);
				$pdf->Cell(GoldW*2,CellH,(get_text('SetPoints', 'Tournament')),1,1,'C',1);
				$WhereY[$WhichScore]=$pdf->GetY();

	//Righe
				$ScoreTotal = 0;
				$ScoreGold = 0;
				$ScoreXnine = 0;
				for($i=1; $i<=$NumRows; $i++)
				{
					$pdf->SetFont($pdf->FontStd,'B',10);
					$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
					$pdf->Cell(GoldW,$TmpCellH,$i,1,0,'C',1);
					$pdf->SetFont($pdf->FontStd,'',10);
					for($j=0; $j<$NumCol; $j++) {
						$pdf->Cell($ColWidth,$TmpCellH,'',1,0,'C',0);
					}
					$pdf->Cell(TotalW,$TmpCellH,'',1,0,'C',0);
					$pdf->SetFont($pdf->FontStd,'',11);
					$pdf->Cell(TotalW,$TmpCellH,'',1,0,'C',0);
					$pdf->SetFont($pdf->FontStd,'',9);
					$pdf->Cell(GoldW*2,$TmpCellH,'',1,1,'C',0);
					$WhereY[$WhichScore]=$pdf->GetY();
				}
	//Tie Break
				$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]+(CellH/4));
				$pdf->SetFont($pdf->FontStd,'B',8);
				//$pdf->Cell(GoldW,CellH*3/4,(get_text('TB')),1,0,'C',1);
				$pdf->Cell(GoldW,CellH*3/4,"",0,0,'C',0);
				for($j=0; $j<2; $j++) {
					$pdf->Cell(($NumCol*$ColWidth/2),CellH*3/4,'',0,0,'C',0);
				}
	//Totale
				$pdf->SetXY($pdf->GetX(),$WhereY[$WhichScore]);
				$pdf->SetFont($pdf->FontStd,'B',10);
				$pdf->Cell(TotalW,CellH,(get_text('Total')),0,0,'R',0);
				$pdf->SetFont($pdf->FontStd,'B',11);
				$pdf->Cell(TotalW,CellH,'',1,0,'C',0);
				$pdf->SetFont($pdf->FontStd,'',10);
				$pdf->Cell(GoldW*2,CellH,'',1,1,'C',0);
				$WhereY[$WhichScore]=$pdf->GetY()+8;
	//Firme
				$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
			   	$pdf->SetFont($pdf->FontStd,'I',7);
				$pdf->Cell($ColWidth + GoldW ,4,(get_text('Archer')),'B',0,'L',0);
				$pdf->Cell(($NumCol-1)*$ColWidth + 2*(TotalW + GoldW),4,'','B',1,'L',0);
				$WhereY[$WhichScore]=$pdf->GetY()+8;
				$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
				$pdf->Cell($ColWidth + GoldW ,4,(get_text('Scorer')),'B',0,'L',0);
				$pdf->Cell(($NumCol-1)*$ColWidth + 2*(TotalW + GoldW),4,'','B',1,'L',0);
				$WhereY[$WhichScore]=$pdf->GetY()+8;
				$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
				$pdf->Cell($ColWidth + GoldW ,4,(get_text('JudgeNotes')),'B',0,'L',0);
				$pdf->Cell(($NumCol-1)*$ColWidth + 2*(TotalW + GoldW),4,'','B',1,'L',0);
				$WhereY[$WhichScore]=$pdf->GetY();
			}
		}

//END OF DrawScore
		safe_free_result($Rs);
	}

$pdf->Output();


?>
