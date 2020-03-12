<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/pdf/ResultPDF.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Phases.inc.php');
    checkACL(AclTeams, AclReadOnly);

	$pdf = new ResultPDF((get_text('TeamFinal')),true);
	$pdf->setlinewidth(0.1);

	$GoldW  = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(1/18);
	$ArrowTotW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(9/18);
	$TotalW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(3/18);
	$GridHeight = ($pdf->GetPageHeight()*0.22);

	$StdCols=1;
	$NumRow=4;
	$CellH=7;
	/*$Select
		= "SELECT (TtElabTeam=0) as StdTournament, (TtElabTeam=2) as ThreeDTournament "
		. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/
	$Select
		= "SELECT (ToElabTeam=0) as StdTournament, (ToElabTeam=2) as ThreeDTournament "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$RsTour=safe_r_sql($Select);
	if (safe_num_rows($RsTour)==1){
		$r=safe_fetch($RsTour);
		$StdCols = $r->StdTournament;
		$NumRow  = $r->ThreeDTournament;
		safe_free_result($RsTour);
	}
	$NumRow = ($NumRow ==1 ? 8 : 4);

	$Fasi = array(16, 8, 4, 2, 0);
	$TgtNoFasi = array('s16','s8', 's4', 's2', 'sGo');
	$ByeFasi = array('b16', 'b8', 'b4', 'b2', 'bBr');
	$Start2FirstPhase=array();
	$q=safe_r_sql("select PhId, greatest(PhId, PhLevel) as FullLevel from Phases where greatest(PhId, PhLevel)<=16 and PhRuleSets in ('', '{$_SESSION['TourLocRule']}') order by PhId desc");
	while($r=safe_fetch($q)) {
		$Start2FirstPhase[$r->PhId]=(($place=array_search($r->FullLevel, $Fasi))===false ? 4 : $place);
	}

	if (isset($_REQUEST['Blank'])) {
		$model= empty($_REQUEST['Model'])?'':$_REQUEST['Model'];
		$MyQuery = "SELECT EvCode, '' AS EvEventName, EvMixedTeam,  EvFinalFirstPhase, EvMatchMode, EvMatchArrowsNo, "
        . " '' AS GrPosition, '' AS CoCode, '' AS TeamName, EvMaxTeamPerson, 0 as isBye, "
        . " '' AS s16, '' AS s8, '' AS s4, '' AS s2, '' AS sBr, '' AS sGo, "
        . " '' AS b16, '' AS b8, '' AS b4, '' AS b2, '' AS bBr, '' AS bGo "
        . " from Events where ".($model ? "EvCode='$model' and" : '')." EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent!=0 limit 1";
	} else {
		$Events=array();
		if(!empty($_REQUEST['Event'])) {
			if(!is_array($_REQUEST['Event'])) $_REQUEST['Event']=array($_REQUEST['Event']);
			foreach($_REQUEST['Event'] as $Event) {
				if(preg_match('//', $Event)) $Events[]=strSafe_DB($Event);
			}
			sort($Events);
		}

		$TmpJoinType='INNER';
		if(isset($_REQUEST["IncEmpty"]) && $_REQUEST["IncEmpty"]==1)
			$TmpJoinType='LEFT';
		$MyQuery = 'SELECT '
			. ' EvCode, EvMatchMode, EvMatchArrowsNo, EvEventName, EvMixedTeam, EvFinalFirstPhase, GrPosition, '
			. " CoCode, CONCAT(CoName, IF(f.TfSubTeam>'1',CONCAT(' (',f.TfSubTeam,')'),'')) AS TeamName, EvMaxTeamPerson, (f.TfTie=2) as isBye, "
			. ' f16.TfTie=2 b16, f8.TfTie=2 b8, f4.TfTie=2 b4, f2.TfTie=2 b2, fb.TfTie=2 bBr, fg.TfTie=2 bGo, '
			. ' IFNULL(s16.FSTarget,\'\') s16, IFNULL(s8.FSTarget,\'\') s8, IFNULL(s4.FSTarget,\'\') s4, IFNULL(s2.FSTarget,\'\') s2, IFNULL(sb.FSTarget,\'\') sBr, IFNULL(sg.FSTarget,\'\') sGo '
			. ' FROM Events '
			. ' INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0 '
			. ' INNER JOIN TeamFinals as f ON EvCode=TfEvent AND EvTournament=f.TfTournament '
			. ' INNER JOIN Grids ON TfMatchNo=GrMatchNo AND GrPhase=greatest(PhId, PhLevel) '
			. ' ' . $TmpJoinType . ' JOIN Countries on TfTeam=CoId AND TfTournament=CoTournament '
            . ' LEFT JOIN FinSchedule s16 ON EvCode=s16.FSEvent AND EvTeamEvent=s16.FSTeamEvent AND EvTournament=s16.FSTournament AND IF(GrPhase=16,TfMatchNo,-256)=s16.FSMatchNo'
            . ' LEFT JOIN FinSchedule s8 ON EvCode=s8.FSEvent AND EvTeamEvent=s8.FSTeamEvent AND EvTournament=s8.FSTournament AND IF(GrPhase=8,TfMatchNo,FLOOR(s16.FSMatchNo/2))=s8.FSMatchNo'
            . ' LEFT JOIN FinSchedule s4 ON EvCode=s4.FSEvent AND EvTeamEvent=s4.FSTeamEvent AND EvTournament=s4.FSTournament AND IF(GrPhase=4,TfMatchNo,FLOOR(s8.FSMatchNo/2))=s4.FSMatchNo'
			. ' LEFT JOIN FinSchedule s2 ON EvCode=s2.FSEvent AND EvTeamEvent=s2.FSTeamEvent AND EvTournament=s2.FSTournament AND IF(GrPhase=2,TfMatchNo,FLOOR(s4.FSMatchNo/2))=s2.FSMatchNo'
			. ' LEFT JOIN FinSchedule sb ON EvCode=sb.FSEvent AND EvTeamEvent=sb.FSTeamEvent AND EvTournament=sb.FSTournament AND FLOOR(s2.FSMatchNo/2)=sb.FSMatchNo'
			. ' LEFT JOIN FinSchedule sg ON EvCode=sg.FSEvent AND EvTeamEvent=sg.FSTeamEvent AND EvTournament=sg.FSTournament AND FLOOR(s2.FSMatchNo/2)-2=sg.FSMatchNo'

			. ' LEFT JOIN TeamFinals f16 ON EvCode=f16.TfEvent AND EvTournament=f16.TfTournament AND IF(GrPhase=16,f.TfMatchNo, -256)=f16.TfMatchNo'
			. ' LEFT JOIN TeamFinals  f8 ON EvCode= f8.TfEvent AND EvTournament= f8.TfTournament AND IF(GrPhase= 8,f.TfMatchNo, FLOOR(f16.TfMatchNo/2))=f8.TfMatchNo'
			. ' LEFT JOIN TeamFinals  f4 ON EvCode= f4.TfEvent AND EvTournament= f4.TfTournament AND IF(GrPhase= 4,f.TfMatchNo, FLOOR( f8.TfMatchNo/2))=f4.TfMatchNo'
			. ' LEFT JOIN TeamFinals  f2 ON EvCode= f2.TfEvent AND EvTournament= f2.TfTournament AND IF(GrPhase= 2,f.TfMatchNo, FLOOR( f4.TfMatchNo/2))=f2.TfMatchNo'
			. ' LEFT JOIN TeamFinals  fb ON EvCode= fb.TfEvent AND EvTournament= fb.TfTournament AND FLOOR(f2.TfMatchNo/2)=fb.TfMatchNo'
			. ' LEFT JOIN TeamFinals  fg ON EvCode= fg.TfEvent AND EvTournament= fg.TfTournament AND FLOOR(f2.TfMatchNo/2)-2=fg.TfMatchNo'

			. ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=1 ';
			if($Events)
				$MyQuery.= "AND EvCode in (" . implode(',', $Events) . ") ";
		$MyQuery .= ' ORDER BY EvCode, f.TfMatchNo';
		//echo $MyQuery;exit;
	}
	$Rs=safe_r_sql($MyQuery);
// Se il Recordset è valido e contiene almeno una riga
	if (safe_num_rows($Rs)>0) {
//		$WhereStartX=array($pdf->getSideMargin(),($pdf->GetPageWidth()+$pdf->getSideMargin())/2,$pdf->getSideMargin(),($pdf->GetPageWidth()+$pdf->getSideMargin())/2);
//		$WhereStartY=array(55, 55, 55+($pdf->GetPageHeight()-75)/2,55+($pdf->GetPageHeight()-75)/2);
        $WhereStartX=array($pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2,$pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2,$pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2);
        $WhereStartY=array($pdf->GetPageHeight()*0.20,$pdf->GetPageHeight()*0.20,$pdf->GetPageHeight()*0.44,$pdf->GetPageHeight()*0.44,$pdf->GetPageHeight()*0.68,$pdf->GetPageHeight()*0.68);

        $WhereX=NULL;
		$WhereY=NULL;
		//$NumRow=4;
		$RowNo=0;
//DrawScore
		while($MyRow=safe_fetch($Rs)) {
			if($RowNo++ != 0)
				$pdf->AddPage();
			$WhereX=$WhereStartX;
			$WhereY=$WhereStartY;
//Intestazione Squadra
			$pdf->SetY(35);
		   	$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell($pdf->GetPageWidth()*0.1 , 7, (get_text('Country')) . ': ','TL',0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($pdf->GetPageWidth()*0.8-20,7, ($MyRow->TeamName . (strlen($MyRow->CoCode)>0 ?  ' (' . $MyRow->CoCode  . ')' : '')),'T',1,'L',0);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell($pdf->GetPageWidth()*0.1,7,(get_text('DivisionClass')) . ': ','LB',0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($pdf->GetPageWidth()*0.8-20,7, get_text($MyRow->EvEventName,'','',true),'B',1,'L',0);

			$pdf->SetXY($pdf->GetPageWidth()*0.9-10,$pdf->GetY()-14);
			$pdf->SetFont($pdf->FontStd,'B',8);
			$pdf->Cell($pdf->GetPageWidth()*0.1,5, (get_text('Rank')),'TLR',1,'C',1);
			$pdf->SetXY($pdf->GetPageWidth()*0.9-10,$pdf->GetY());
			$pdf->SetFont($pdf->FontStd,'B',20);
			$pdf->Cell($pdf->GetPageWidth()*0.1,9, ($MyRow->GrPosition),'BLR',1,'C',1);

            for($WhichScore = $Start2FirstPhase[$MyRow->EvFinalFirstPhase];$WhichScore<=4;$WhichScore++) {
				DrawScore($pdf, $MyRow, $WhichScore, $WhereX[$WhichScore-$Start2FirstPhase[$MyRow->EvFinalFirstPhase]], $WhereY[$WhichScore-$Start2FirstPhase[$MyRow->EvFinalFirstPhase]]);
			}
		}

//END OF DrawScore
		$pdf->Output('Score.pdf','I');
	}

function DrawScore(&$pdf, $MyRow, $WhichScore, $WhereX, $WhereY) {

	global $ArrowTotW, $GridHeight, $GoldW, $TotalW, $CellH, $Fasi, $ByeFasi, $TgtNoFasi, $Start2FirstPhase;

	$scoreStartX = $WhereX;
	$scoreStartY = $WhereY;

	$EventSpecs=getEventArrowsParams($MyRow->EvCode, pow(2, 4-$WhichScore), '1');
	// 4 fixed height rows and $numCol + 1 variable rows
	$NumCol = $EventSpecs->arrows;
	$ColWidth = $ArrowTotW / $NumCol;

	$NumRows = $EventSpecs->ends;
	$TmpCellH = $GridHeight/($NumRows+4);

//Header
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->Cell($GoldW,$TmpCellH,'',0,0,'C',0);
	$pdf->Cell(2*$GoldW+2*$TotalW+$NumCol*$ColWidth,$CellH,(get_text(namePhase($MyRow->EvFinalFirstPhase,$Fasi[$WhichScore]).'_Phase')),1,(is_null($MyRow->{$TgtNoFasi[$WhichScore]}) ? 1 : 0),'C',1);

	if(!is_null($MyRow->{$TgtNoFasi[$WhichScore]}))
	{
		$pdf->SetXY($pdf->GetX()-20,$pdf->GetY());
 		if($WhichScore!=4)
		{
			$pdf->SetFont($pdf->FontStd,'',7);
			$pdf->Cell(20,$CellH*0.5,get_text('Target'),'LRT',0,'C',1);
			$pdf->SetFont($pdf->FontStd,'B',8);
			$pdf->SetXY($pdf->GetX()-20,$pdf->GetY()+$CellH*0.5);
			$pdf->Cell(20,$CellH*0.5, ltrim($MyRow->{$TgtNoFasi[$WhichScore]}, '0'),'LRB',0,'C',1);
		}
		else
		{
			$pdf->SetFont($pdf->FontStd,'B',8);
			$pdf->Cell(20,$CellH*0.5,get_text('MedalGold') . ' ' . ltrim($MyRow->sGo, '0'),'LRT',0,'C',1);
			$pdf->SetXY($pdf->GetX()-20,$pdf->GetY()+$CellH*0.5);
			$pdf->Cell(20,$CellH*0.5, get_text('MedalBronze') . ' ' . ltrim($MyRow->sBr, '0'),'LRB',0,'C',1);
		}

	}
	$pdf->Rect($WhereX+$GoldW+1,$WhereY+1,$CellH-2,$CellH-2,'DF',array(),array(255,255,255));
	$pdf->SetDefaultColor();
	$pdf->SetXY($WhereX+$GoldW+$CellH-1,$WhereY+1);
	$pdf->SetFont($pdf->FontStd,'B',7);
	$pdf->Cell($ArrowTotW-$CellH+1,$CellH-2, get_text('Winner'),0,1,'L',0);

	$pdf->SetXY($pdf->GetX(),$WhereY+$CellH);
	$WhereY=$pdf->GetY();

	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->Cell($GoldW,$TmpCellH,'',0,0,'C',0);
	for($j=0; $j<$NumCol; $j++)
		$pdf->Cell($ColWidth,$TmpCellH, ($j+1), 1, 0, 'C', 1);
	$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),$TmpCellH,get_text(($MyRow->EvMatchMode==0 ? 'TotalProg':'SetTotal'),'Tournament'),1,0,'C',1);
	$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),$TmpCellH,get_text(($MyRow->EvMatchMode==0 ? 'TotalShort':'RunningTotal'),'Tournament'),1,0,'C',1);
	if($MyRow->EvMatchMode==0)
	{
		$pdf->Cell($GoldW,$TmpCellH,($pdf->prnGolds),1,0,'C',1);
		$pdf->Cell($GoldW,$TmpCellH,($pdf->prnXNine),1,1,'C',1);
	}
	else
	{
		$pdf->Cell(2*$GoldW,$TmpCellH,get_text('SetPoints', 'Tournament'),1,0,'C',1);
		$pdf->Cell(2/5*$TotalW,$TmpCellH,get_text('TotalShort','Tournament'),1,1,'C',1);
	}
	$WhereY=$pdf->GetY();
//Righe
	for($i=1; $i<=$NumRows; $i++)
	{
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->SetXY($WhereX,$WhereY);
		$pdf->Cell($GoldW,$TmpCellH,$i,1,0,'C',1);
		for($j=0; $j<$NumCol; $j++)
			$pdf->Cell($ColWidth,$TmpCellH,'',1,0,'C',0);
		$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),$TmpCellH,'',1,0,'C',0);
		$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),$TmpCellH,'',1,0,'C',0);
		if($MyRow->EvMatchMode==0)
		{
			$pdf->Cell($GoldW,$TmpCellH,'',1,0,'C',0);
			$pdf->Cell($GoldW,$TmpCellH,'',1,1,'C',0);
		}
		else
		{
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell((2*$GoldW)/3,$TmpCellH,'2',1, 0,'C',0);
			$pdf->Cell((2*$GoldW)/3,$TmpCellH,'1',1, 0,'C',0);
			$pdf->Cell((2*$GoldW)/3,$TmpCellH,'0',1, 0,'C',0);
			$pdf->Cell($TotalW * 2/5,$TmpCellH,'',1, 1,'C',0);
		}
		$WhereY=$pdf->GetY();
	}
//Tie Break
	$pdf->SetXY($WhereX,$WhereY+0.5);
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell($GoldW,$TmpCellH-0.5,(get_text('TB')),1,0,'C',1);
	for($j=0; $j<$EventSpecs->so; $j++)
	{
		if(($j % $MyRow->EvMaxTeamPerson)==0)
			$pdf->SetX($pdf->GetX()+0.5);
		$pdf->Cell(($ArrowTotW-(0.5*$EventSpecs->so/$MyRow->EvMaxTeamPerson))/$EventSpecs->so, $TmpCellH-4, '', 1, 0, 'C', 0);
	}
	$pdf->SetXY($WhereX+$GoldW+0.5,$WhereY+$TmpCellH-3);
	$pdf->SetFont($pdf->FontStd,'',1);
	$pdf->Cell(3,3,'',1,0,'R',0);
	$pdf->SetXY($WhereX+$GoldW+3.5,$WhereY+$TmpCellH-3);
	$pdf->SetFont($pdf->FontStd,'',6);
	$pdf->Cell($ArrowTotW-3.5,3,get_text('Close2Center','Tournament'),0,0,'L',0);

//Totale
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->SetXY($WhereX+$GoldW+$ArrowTotW,$WhereY);
	$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),$TmpCellH,(get_text('Total')),0,0,'R',0);
	$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),$TmpCellH,'',1,0,'C',0);
	if($MyRow->EvMatchMode==0)
	{
		$pdf->Cell($GoldW,$TmpCellH,'',1,0,'C',0);
		$pdf->Cell($GoldW,$TmpCellH,'',1,1,'C',0);
	}
	else
	{
		$pdf->Cell(2*$GoldW,$TmpCellH,get_text('Total'),0,0,'R',0);
		$pdf->Cell(2/5*$TotalW,$TmpCellH,'',1,1,'C',0);
	}
	$WhereY=$pdf->GetY()+2;
//Firme
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->SetFont($pdf->FontStd,'I',7);
	$pdf->Cell($NumCol*$ColWidth + 2*$TotalW + 3*$GoldW, 4, (get_text('Scorer')),'B',0,'L',0);
	$WhereY=$pdf->GetY()+6;
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->Cell($NumCol*$ColWidth + 2*$TotalW + 3*$GoldW, 4,(get_text('Archers')),'B',0,'L',0);
	$WhereY=$pdf->GetY();


	// draws a line on scoecards that are byes
	if($MyRow->{$ByeFasi[$WhichScore]}) {
		$pdf->SetLineWidth(0.75);
		$pdf->Line($scoreStartX,$scoreStartY,$scoreStartX+(2*$TotalW + $NumCol*$ColWidth + 3* $GoldW), $WhereY+4);
		$pdf->SetLineWidth(0.1);
	}
}

?>