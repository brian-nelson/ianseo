<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/pdf/ResultPDF.inc.php');
	require_once('Common/Fun_Phases.inc.php');

	$pdf = new ResultPDF((get_text('IndFinal')));
	$pdf->setlinewidth(0.1);
//	$pdf->SetAutoPageBreak(false, 10);

	$Score3D = false;
	//$MyQuery = "SELECT (TtElabTeam=2) as is3D FROM Tournament INNER JOIN Tournament*Type AS tt ON ToType=TtId WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
	$MyQuery = "SELECT (ToElabTeam=2) as is3D FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)==1) {
		$r=safe_fetch($Rs);
		$Score3D = $r->is3D;
	}

	//(1+1+1 gold)+(2+2+2 arrow)+(1+1 totalw)

	$Fasi = array(get_text('64_Phase'),get_text('32_Phase'), get_text('16_Phase'), get_text('8_Phase'), get_text('4_Phase'), get_text('2_Phase'), get_text('0_Phase'));
	$TgtNoFasi = array('s64','s32', 's16', 's8', 's4', 's2', 'sGo');
	//$NumFasi = array(64,48, 32,24, 16, 8, 4, 1);
	$NumFasi = array(64, 32, 16, 8, 4, 2, 1);
	$Start2FirstPhase = array(64=>0,48=>0,32=>1, 24=>1, 16=>2, 8=>3, 4=>4, 2=>5, 1=>6, 0=>6);



	$MyQuery="";
	if (isset($_REQUEST['Blank']))
	{
		$model= empty($_REQUEST['Model'])?'':$_REQUEST['Model'];
		$MyQuery = "SELECT '' AS EvCode, '' AS EvEventName, EvFinalFirstPhase, EvMatchMode, EvMatchArrowsNo, "
        . " '' AS GrPosition, '' AS Athlete, '' AS CoCode, '' AS CoName, 0 as isBye, "
        . " '' AS s64,'' AS s32, '' AS s16, '' AS s8, '' AS s4, '' AS s2, '' AS sBr, '' AS sGo, "
       	. " EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO"
        . " from Events where ".($model ? "EvCode='$model' and" : '')." EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 limit 1";
	}
	else
	{
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
        . ' EvCode, EvEventName, EvFinalFirstPhase, EvMatchMode, EvMatchArrowsNo, '
        . ' IF(EvFinalFirstPhase=48 || EvFinalFirstPhase=24,GrPosition2, GrPosition) as GrPosition, CONCAT(EnName, \' \', EnFirstName) as Athlete, (FinTie=2) as isBye, '
        . ' CoCode, CoName, '
        . ' NULLIF(s64.FSLetter,\'\') s64,NULLIF(s32.FSLetter,\'\') s32, NULLIF(s16.FSLetter,\'\') s16, NULLIF(s8.FSLetter,\'\') s8, NULLIF(s4.FSLetter,\'\') s4, NULLIF(s2.FSLetter,\'\') s2, NULLIF(sb.FSLetter,\'\') sBr, NULLIF(sg.FSLetter,\'\') sGo '
			. " , EvElimEnds"
			. " , EvElimArrows"
			. " , EvElimSO"
			. " , EvFinEnds"
			. " , EvFinArrows"
			. " , EvFinSO"
        . ' FROM Events'
        . ' INNER JOIN Finals ON EvCode=FinEvent AND EvTournament=FinTournament'
        . ' INNER JOIN Grids ON FinMatchNo=GrMatchNo AND GrPhase=(IF(EvFinalFirstPhase=24,32, IF(EvFinalFirstPhase=48,64,EvFinalFirstPhase )))'
        . ' ' . $TmpJoinType . ' JOIN Entries ON FinAthlete=EnId AND FinTournament=EnTournament'
        . ' ' . $TmpJoinType . ' JOIN Countries on EnCountry=CoId AND EnTournament=CoTournament'
        . ' LEFT JOIN FinSchedule s64 ON EvCode=s64.FSEvent AND EvTeamEvent=s64.FSTeamEvent AND EvTournament=s64.FSTournament AND IF(EvFinalFirstPhase=64 OR EvFinalFirstPhase=48,FinMatchNo,-256)=s64.FSMatchNo'
        . ' LEFT JOIN FinSchedule s32 ON EvCode=s32.FSEvent AND EvTeamEvent=s32.FSTeamEvent AND EvTournament=s32.FSTournament AND IF(EvFinalFirstPhase=32 OR EvFinalFirstPhase=24,FinMatchNo,FLOOR(s64.FSMatchNo/2))=s32.FSMatchNo'
		. ' LEFT JOIN FinSchedule s16 ON EvCode=s16.FSEvent AND EvTeamEvent=s16.FSTeamEvent AND EvTournament=s16.FSTournament AND IF(EvFinalFirstPhase=16,FinMatchNo,FLOOR(s32.FSMatchNo/2))=s16.FSMatchNo'
		. ' LEFT JOIN FinSchedule s8 ON EvCode=s8.FSEvent AND EvTeamEvent=s8.FSTeamEvent AND EvTournament=s8.FSTournament AND IF(EvFinalFirstPhase=8,FinMatchNo,FLOOR(s16.FSMatchNo/2))=s8.FSMatchNo'
		. ' LEFT JOIN FinSchedule s4 ON EvCode=s4.FSEvent AND EvTeamEvent=s4.FSTeamEvent AND EvTournament=s4.FSTournament AND IF(EvFinalFirstPhase=4,FinMatchNo,FLOOR(s8.FSMatchNo/2))=s4.FSMatchNo'
		. ' LEFT JOIN FinSchedule s2 ON EvCode=s2.FSEvent AND EvTeamEvent=s2.FSTeamEvent AND EvTournament=s2.FSTournament AND IF(EvFinalFirstPhase=2,FinMatchNo,FLOOR(s4.FSMatchNo/2))=s2.FSMatchNo'
		. ' LEFT JOIN FinSchedule sb ON EvCode=sb.FSEvent AND EvTeamEvent=sb.FSTeamEvent AND EvTournament=sb.FSTournament AND FLOOR(s2.FSMatchNo/2)=sb.FSMatchNo'
		. ' LEFT JOIN FinSchedule sg ON EvCode=sg.FSEvent AND EvTeamEvent=sg.FSTeamEvent AND EvTournament=sg.FSTournament AND FLOOR(s2.FSMatchNo/2)-2=sg.FSMatchNo'
        . ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=0 ';
		if($Events)
			$MyQuery.= "AND EvCode in (" . implode(',', $Events) . ") ";
        $MyQuery .= ' ORDER BY EvCode, FinMatchNo ';
	}
	//DEBUG_svela($MyQuery, true);
	$Rs=safe_r_sql($MyQuery);
// Se il Recordset è valido e contiene almeno una riga
	if (safe_num_rows($Rs)>0)
	{
		$defArrowTotW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(6/15);//12;
		$defTotalW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(3/15);//16;
		$defGoldW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(1/15);//7;
		$ScoreHeight=$pdf->GetPageHeight()*0.23;

		$WhereStartX=array($pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2,$pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2,$pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2);
		$WhereStartY=array($pdf->GetPageHeight()*0.20,$pdf->GetPageHeight()*0.20,$pdf->GetPageHeight()*0.44,$pdf->GetPageHeight()*0.44,$pdf->GetPageHeight()*0.68,$pdf->GetPageHeight()*0.68);
		$WhereX=NULL;
		$WhereY=NULL;
		$RowNo=0;
		while($MyRow=safe_fetch($Rs))
		{
//			if($MyRow->EvFinalFirstPhase==48 || $MyRow->EvFinalFirstPhase==24)
//				$Fasi[0]=get_text('24_Phase');
			if($MyRow->EvFinalFirstPhase==48) {
				$Fasi[0]=get_text('48_Phase');
				$Fasi[1]=get_text('24_Phase');
			} elseif ($MyRow->EvFinalFirstPhase==24) {
				$Fasi[1]=get_text('24_Phase');
			}

			if($RowNo++ != 0)
				$pdf->AddPage();
			$WhereX=$WhereStartX;
			$WhereY=$WhereStartY;
//Intestazione Atleta
			$pdf->SetY($pdf->GetPageHeight()*0.20-23);
		   	$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell($pdf->GetPageWidth()*0.1,6,(get_text('Athlete')) . ': ','TL',0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($pdf->GetPageWidth()*0.8-20,6, ($MyRow->Athlete),'T',1,'L',0);
		   	$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell($pdf->GetPageWidth()*0.1,6,(get_text('Country')) . ': ','L',0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($pdf->GetPageWidth()*0.8-20,6, ($MyRow->CoName . (strlen($MyRow->CoCode)>0 ?  ' (' . $MyRow->CoCode  . ')' : '')),0,1,'L',0);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell($pdf->GetPageWidth()*0.1,6,(get_text('DivisionClass')) . ': ','LB',0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($pdf->GetPageWidth()*0.8-20,6, get_text($MyRow->EvEventName,'','',true),'B',1,'L',0);

			$pdf->SetXY($pdf->GetPageWidth()*0.9-10,$pdf->GetY()-18);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($pdf->GetPageWidth()*0.1,6, (get_text('Rank')),'TLR',1,'C',1);
			$pdf->SetXY($pdf->GetPageWidth()*0.9-10,$pdf->GetY());
			$pdf->SetFont($pdf->FontStd,'B',25);
			$pdf->Cell($pdf->GetPageWidth()*0.1,12, ($MyRow->GrPosition),'BLR',1,'C',1);

			$WhichScoreEnd=7;
			if ($MyRow->EvFinalFirstPhase==64 || $MyRow->EvFinalFirstPhase==48)
				$WhichScoreEnd=6;
			for($WhichScore = $Start2FirstPhase[$MyRow->EvFinalFirstPhase];$WhichScore<$WhichScoreEnd;$WhichScore++) {
				DrawScore($pdf, $MyRow, $WhichScore, $WhereX[$WhichScore-$Start2FirstPhase[$MyRow->EvFinalFirstPhase]], $WhereY[$WhichScore- $Start2FirstPhase[$MyRow->EvFinalFirstPhase]], $WhichScore==$WhichScoreEnd-1);
			}
		}
		safe_free_result($Rs);
	}

$pdf->Output();


function DrawScore(&$pdf, $MyRow, $WhichScore, $WhereX, $WhereY, $FinalScore=false) {
	global $defTotalW, $defGoldW, $defArrowTotW, $ScoreHeight, $NumFasi, $Fasi, $TgtNoFasi, $Score3D, $Start2FirstPhase;
	
	$scoreStartX = $WhereX;
	$scoreStartY = $WhereY;
	
	$TotalW = $defTotalW;
	$GoldW = $defGoldW;
	$NumCol = 3;
	$NumRow=5;
	$CellH=6;
// OCIO al 2*: il motivo è che il bit meno significativo è la finale quindi abbiamo tutto traslato a sinistra di un bit (=moltiplicato per due)
	if($MyRow->EvMatchArrowsNo & 2*bitwisePhaseId($NumFasi[$WhichScore])) {
		// eliminatorie
		$NumRow=$MyRow->EvElimEnds;
		$NumCol=$MyRow->EvElimArrows;
		$NumSO=$MyRow->EvElimSO;
	} else {
		$NumRow=$MyRow->EvFinEnds;
		$NumCol=$MyRow->EvFinArrows;
		$NumSO=$MyRow->EvFinSO;
	}
//print $WhichScore.'<br>';

	$ArrowW=$defArrowTotW/$NumCol;
	$CellH=($ScoreHeight-6*5)/$NumRow;

	//Header
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->Cell($GoldW,6,'',0,0,'C',0);
	$pdf->Cell(2*$GoldW+2*$TotalW+$NumCol*$ArrowW,6,($Fasi[$WhichScore]),1,(is_null($MyRow->{$TgtNoFasi[$WhichScore]}) ? 1 : 0),'C',1);

	if(!is_null($MyRow->{$TgtNoFasi[$WhichScore]})) {
		$pdf->SetXY($pdf->GetX()-15,$pdf->GetY());
		if(!$FinalScore) {
			$pdf->SetFont($pdf->FontStd,'',6);
			$pdf->Cell(15,3,get_text('Target'),'LRT',0,'C',1);
			$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->SetXY($pdf->GetX()-15,$pdf->GetY()+3);
			$pdf->Cell(15,3, ltrim($MyRow->{$TgtNoFasi[$WhichScore]},0),'LRB',0,'C',1);
		} else {
			$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(15,3,get_text('MedalGold') . ' ' . ltrim($MyRow->sGo,'0'),'LRT',0,'C',1);
			$pdf->SetXY($pdf->GetX()-15,$pdf->GetY()+3);
			$pdf->Cell(15,3, get_text('MedalBronze') . ' ' . ltrim($MyRow->sBr,'0'),'LRB',0,'C',1);
		}
	}
	$pdf->Rect($WhereX+$GoldW+1,$WhereY+1,4,4,'DF',array(),array(255,255,255));
	$pdf->SetDefaultColor();
	$pdf->SetXY($WhereX+$GoldW+5,$WhereY+1);
	$pdf->SetFont($pdf->FontStd,'B',6);
	$pdf->Cell($NumCol*$ArrowW-5,4, get_text('Winner'),0,1,'L',0);

	$pdf->SetXY($pdf->GetX(),$WhereY+6);
	$WhereY=$pdf->GetY();

	//Intestazioni Colonne
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->Cell($GoldW,6,'',0,0,'C',0);
	if($Score3D)
	{
		$pdf->Cell($NumCol*$ArrowW,6, get_text('Arrow'), 1, 0, 'C', 1);
	}
	else
	{
		for($j=0; $j<$NumCol; $j++)
			$pdf->Cell($ArrowW,6, ($j+1), 1, 0, 'C', 1);
	}
	$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),6,get_text(($MyRow->EvMatchMode==0 ? 'TotalProg':'SetTotal'),'Tournament'),1,0,'C',1);
	$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),6,get_text(($MyRow->EvMatchMode==0 ? 'TotalShort':'RunningTotal'),'Tournament'),1,0,'C',1);
	if($MyRow->EvMatchMode==0)
	{
		$pdf->Cell($GoldW,6,($pdf->prnGolds),1,0,'C',1);
		$pdf->Cell($GoldW,6,($pdf->prnXNine),1,1,'C',1);
	}
	else
	{
		$pdf->Cell(2*$GoldW,6,get_text('SetPoints', 'Tournament'),1,0,'C',1);
		$pdf->Cell(2/5*$TotalW,6,get_text('TotalShort','Tournament'),1,1,'C',1);
	}
	$WhereY=$pdf->GetY();

	//Righe
	for($i=1; $i<=$NumRow; $i++)
	{
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->SetXY($WhereX,$WhereY);
		$pdf->Cell($GoldW,$CellH,$i,1,0,'C',1);
		if($Score3D)
		{
			$pdf->SetFont($pdf->FontStd,'',8);
			$pdf->Cell(1/5*$ArrowW,$CellH, '11', 1, 0, 'C', 0);
			$pdf->Cell(1/5*$ArrowW,$CellH, '10', 1, 0, 'C', 0);
			$pdf->Cell(1/5*$ArrowW,$CellH, '8', 1, 0, 'C', 0);
			$pdf->Cell(1/5*$ArrowW,$CellH, '5', 1, 0, 'C', 0);
			$pdf->Cell(1/5*$ArrowW,$CellH, 'M', 1, 0, 'C', 0);
		}
		else
		{
			for($j=0; $j<$NumCol; $j++)
				$pdf->Cell($ArrowW,$CellH,'',1,0,'C',0);
		}
		$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),$CellH,'',1,0,'C',0);
		$pdf->Cell($TotalW * ($MyRow->EvMatchMode==0 ? 1:4/5),$CellH,'',1,0,'C',0);
		if($MyRow->EvMatchMode==0)
		{
			$pdf->Cell($GoldW,$CellH,'',1,0,'C',0);
			$pdf->Cell($GoldW,$CellH,'',1,1,'C',0);
		}
		else
		{
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell((2*$GoldW)/3,$CellH,'2',1, 0,'C',0);
			$pdf->Cell((2*$GoldW)/3,$CellH,'1',1, 0,'C',0);
			$pdf->Cell((2*$GoldW)/3,$CellH,'0',1, 0,'C',0);
			$pdf->Cell($TotalW * 2/5,$CellH,'',1, 1,'C',0);
		}

		$WhereY=$pdf->GetY();
	}

	//Shoot Off
	$pdf->SetXY($WhereX,$WhereY+0.5);
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell($GoldW,6.5,(get_text('TB')),1,0,'C',1);
	$ShootOffW=($NumSO<=$NumCol ? $ArrowW : ($ArrowW*$NumCol)/$NumSO);
	for($j=0; $j<$NumSO; $j++)
	{
		$pdf->SetXY($pdf->GetX()+0.5,$pdf->GetY());
		$pdf->Cell($ShootOffW-0.5,4,'',1,0,'C',0);
	}
	$pdf->SetXY($WhereX+$GoldW+0.5,$WhereY+5);
	$pdf->SetFont($pdf->FontStd,'',1);
	$pdf->Cell(2,2,'',1,0,'R',0);
	$pdf->SetXY($WhereX+$GoldW+2.5,$WhereY+4.5);
	$pdf->SetFont($pdf->FontStd,'',6);
	$pdf->Cell($ArrowW*$NumCol-2.5,2.5,get_text('Close2Center','Tournament'),0,0,'L',0);


	//Totale
	$pdf->SetXY($WhereX+$GoldW+($ArrowW*$NumCol),$WhereY);
	$pdf->SetFont($pdf->FontStd,'B',10);
	if($MyRow->EvMatchMode==0)
	{
		$pdf->Cell($TotalW ,6,(get_text('Total')),0,0,'R',0);
		$pdf->Cell($TotalW ,6,'',1,0,'C',0);
		$pdf->Cell($GoldW,6,'',1,0,'C',0);
		$pdf->Cell($GoldW,6,'',1,1,'C',0);

	}
	else
	{
		$pdf->Cell($TotalW * 8/5,6,'',0,0,'R',0);
		$pdf->Cell(2*$GoldW,6,get_text('Total'),0,0,'R',0);
		$pdf->Cell(2/5*$TotalW,6,'',1,1,'C',0);
	}


	$WhereY=$pdf->GetY()+3;
//Firme
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->SetFont($pdf->FontStd,'I',7);
	$pdf->Cell(2*$defTotalW + $defArrowTotW + 3*$defGoldW,4,(get_text('Archer')),'B',0,'L',0);
	$WhereY=$pdf->GetY()+5;
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->Cell(2*$defTotalW + $defArrowTotW + 3*$defGoldW,4, (get_text('Scorer')),'B',0,'L',0);
	$WhereY=$pdf->GetY();
	
	if($MyRow->isBye && $WhichScore == $Start2FirstPhase[$MyRow->EvFinalFirstPhase]) {
		$pdf->SetLineWidth(0.75);
		$pdf->Line($scoreStartX,$scoreStartY,$scoreStartX+(2*$defTotalW + $defArrowTotW + 3*$defGoldW),$scoreStartY + $ScoreHeight);
		$pdf->SetLineWidth(0.1);
	}

}
?>