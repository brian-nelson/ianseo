<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/pdf/ResultPDF.inc.php');
	require_once('Common/Fun_Phases.inc.php');
    require_once('Common/Lib/ArrTargets.inc.php');
    checkACL(AclIndividuals, AclReadOnly);

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

	$Fasi = array(get_text('64_Phase'),get_text('32_Phase'), get_text('16_Phase'), get_text('8_Phase'), get_text('4_Phase'), get_text('2_Phase'), get_text('ScoreFinalMatch', 'Tournament'));
	$TgtNoFasi = array('s64','s32', 's16', 's8', 's4', 's2', 'sGo');
	$ArrowFasi = array('f64', 'f32', 'f16', 'f8', 'f4', 'f2', 'fGo');
	$ByeFasi = array('b64', 'b32', 'b16', 'b8', 'b4', 'b2', 'bBr');
	$ByeFasi = array('b64', 'b32', 'b16', 'b8', 'b4', 'b2', 'bBr');
	$OppArray = array('op64', 'op32', 'op16', 'op8', 'op4', 'op2', 'opB', 'opG');
	//$NumFasi = array(64,48, 32,24, 16, 8, 4, 1);
	$NumFasi = array(64, 32, 16, 8, 4, 2, 1);

	$Start2FirstPhase=array();
	$q=safe_r_sql("select PhId, greatest(PhId, PhLevel) as FullLevel from Phases where PhRuleSets in ('', '{$_SESSION['TourLocRule']}') order by PhId desc");
	while($r=safe_fetch($q)) {
		$Start2FirstPhase[$r->PhId]=(($place=array_search($r->FullLevel, $NumFasi))===false ? 6 : $place);
	}

	$MyQuery="";
	if (isset($_REQUEST['Blank']))
	{
		$model= empty($_REQUEST['Model'])?'':$_REQUEST['Model'];
		$MyQuery = "SELECT '' AS EvCode, '' AS EvEventName, EvFinalFirstPhase, EvMatchMode, EvMatchArrowsNo, "
        . " '' AS GrPosition, '' AS Athlete, '' AS CoCode, '' AS CoName, 0 as isBye, "
        . " '' AS s64,'' AS s32, '' AS s16, '' AS s8, '' AS s4, '' AS s2, '' AS sBr, '' AS sGo, "
        . " '' AS b64,'' AS b32, '' AS b16, '' AS b8, '' AS b4, '' AS b2, '' AS bBr, '' AS bGo, "
        . " '' ft64, '' ft32, '' ft16, '' ft8, '' ft4, '' ft2, '' ftBr, '' ftGo, "
        . " '' op64, '' op32, '' op16, '' op8, '' op4, '' op2, '' opB, '' opG, "
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
            . ' IF(EvFinalFirstPhase=48, GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition)) as GrPosition, if(EnNameOrder=1, concat(ucase(EnFirstName), " ", EnName), concat(EnName, " ", ucase(EnFirstName))) as Athlete, (f.FinTie=2) as isBye, '
        . ' CoCode, CoName, '
            . ' NULLIF(s64.FSLetter,\'\') s64,NULLIF(s32.FSLetter,\'\') s32, NULLIF(s16.FSLetter,\'\') s16, NULLIF(s8.FSLetter,\'\') s8, NULLIF(s4.FSLetter,\'\') s4, NULLIF(s2.FSLetter,\'\') s2, NULLIF(sb.FSLetter,\'\') sBr, NULLIF(sg.FSLetter,\'\') sGo, '
            . ' NULLIF(f64.FinArrowString,\'\') f64,NULLIF(f32.FinArrowString,\'\') f32, NULLIF(f16.FinArrowString,\'\') f16, NULLIF(f8.FinArrowString,\'\') f8, NULLIF(f4.FinArrowString,\'\') f4, NULLIF(f2.FinArrowString,\'\') f2, NULLIF(fb.FinArrowString,\'\') fBr, NULLIF(fg.FinArrowString,\'\') fGo, '
            . ' f64.FinTie=2 b64, f32.FinTie=2 b32, f16.FinTie=2 b16, f8.FinTie=2 b8, f4.FinTie=2 b4, f2.FinTie=2 b2, fb.FinTie=2 bBr, fg.FinTie=2 bGo, '
            . ' NULLIF(f64.FinTiebreak,\'\') ft64,NULLIF(f32.FinTiebreak,\'\') ft32, NULLIF(f16.FinTiebreak,\'\') ft16, NULLIF(f8.FinTiebreak,\'\') ft8, NULLIF(f4.FinTiebreak,\'\') ft4, NULLIF(f2.FinTiebreak,\'\') ft2, NULLIF(fb.FinTiebreak,\'\') ftBr, NULLIF(fg.FinTiebreak,\'\') ftGo, '
            . ' op64, op32, op16, op8, op4, op2, opB, opG, '
            . " EvElimEnds, "
            . " EvElimArrows, "
            . " EvElimSO, "
            . " EvFinEnds, "
            . " EvFinArrows, "
            . " EvFinSO"
        . ' FROM Events'
            . ' INNER JOIN Phases ON PhId=EvFinalFirstPhase and (PhIndTeam & 1)=1 '
            . ' INNER JOIN Finals f ON EvCode=f.FinEvent AND EvTournament=f.FinTournament'
            . ' INNER JOIN Grids ON f.FinMatchNo=GrMatchNo AND GrPhase=greatest(PhId, PhLevel)'
            . ' ' . $TmpJoinType . ' JOIN Entries ON f.FinAthlete=EnId AND f.FinTournament=EnTournament'
        . ' ' . $TmpJoinType . ' JOIN Countries on EnCountry=CoId AND EnTournament=CoTournament'
            . ' LEFT JOIN FinSchedule s64 ON EvCode=s64.FSEvent AND EvTeamEvent=s64.FSTeamEvent AND EvTournament=s64.FSTournament AND IF(GrPhase=64, f.FinMatchNo, -256)=s64.FSMatchNo'
            . ' LEFT JOIN FinSchedule s32 ON EvCode=s32.FSEvent AND EvTeamEvent=s32.FSTeamEvent AND EvTournament=s32.FSTournament AND IF(GrPhase=32,f.FinMatchNo,FLOOR(s64.FSMatchNo/2))=s32.FSMatchNo'
            . ' LEFT JOIN FinSchedule s16 ON EvCode=s16.FSEvent AND EvTeamEvent=s16.FSTeamEvent AND EvTournament=s16.FSTournament AND IF(GrPhase=16,f.FinMatchNo,FLOOR(s32.FSMatchNo/2))=s16.FSMatchNo'
            . ' LEFT JOIN FinSchedule s8 ON EvCode=s8.FSEvent AND EvTeamEvent=s8.FSTeamEvent AND EvTournament=s8.FSTournament AND IF(GrPhase=8,f.FinMatchNo,FLOOR(s16.FSMatchNo/2))=s8.FSMatchNo'
            . ' LEFT JOIN FinSchedule s4 ON EvCode=s4.FSEvent AND EvTeamEvent=s4.FSTeamEvent AND EvTournament=s4.FSTournament AND IF(GrPhase=4,f.FinMatchNo,FLOOR(s8.FSMatchNo/2))=s4.FSMatchNo'
            . ' LEFT JOIN FinSchedule s2 ON EvCode=s2.FSEvent AND EvTeamEvent=s2.FSTeamEvent AND EvTournament=s2.FSTournament AND IF(GrPhase=2,f.FinMatchNo,FLOOR(s4.FSMatchNo/2))=s2.FSMatchNo'
		. ' LEFT JOIN FinSchedule sb ON EvCode=sb.FSEvent AND EvTeamEvent=sb.FSTeamEvent AND EvTournament=sb.FSTournament AND FLOOR(s2.FSMatchNo/2)=sb.FSMatchNo'
		. ' LEFT JOIN FinSchedule sg ON EvCode=sg.FSEvent AND EvTeamEvent=sg.FSTeamEvent AND EvTournament=sg.FSTournament AND FLOOR(s2.FSMatchNo/2)-2=sg.FSMatchNo'


            . ' LEFT JOIN Finals f64 ON EvCode=f64.FinEvent AND EvTournament=f64.FinTournament AND IF(GrPhase=64,f.FinMatchNo,-256)=f64.FinMatchNo'
            . ' LEFT JOIN Finals f32 ON EvCode=f32.FinEvent AND EvTournament=f32.FinTournament AND IF(GrPhase=32,f.FinMatchNo,FLOOR(f64.FinMatchNo/2))=f32.FinMatchNo'
            . ' LEFT JOIN Finals f16 ON EvCode=f16.FinEvent AND EvTournament=f16.FinTournament AND IF(GrPhase=16,f.FinMatchNo,FLOOR(f32.FinMatchNo/2))=f16.FinMatchNo'
            . ' LEFT JOIN Finals f8 ON EvCode=f8.FinEvent AND EvTournament=f8.FinTournament AND IF(GrPhase=8,f.FinMatchNo,FLOOR(f16.FinMatchNo/2))=f8.FinMatchNo'
            . ' LEFT JOIN Finals f4 ON EvCode=f4.FinEvent AND EvTournament=f4.FinTournament AND IF(GrPhase=4,f.FinMatchNo,FLOOR(f8.FinMatchNo/2))=f4.FinMatchNo'
            . ' LEFT JOIN Finals f2 ON EvCode=f2.FinEvent AND EvTournament=f2.FinTournament AND IF(GrPhase=2,f.FinMatchNo,FLOOR(f4.FinMatchNo/2))=f2.FinMatchNo'
            . ' LEFT JOIN Finals fb ON EvCode=fb.FinEvent AND EvTournament=fb.FinTournament AND FLOOR(f2.FinMatchNo/2)=fb.FinMatchNo'
            . ' LEFT JOIN Finals fg ON EvCode=fg.FinEvent AND EvTournament=fg.FinTournament AND FLOOR(f2.FinMatchNo/2)-2=fg.FinMatchNo'

            . ' LEFT JOIN (select FinMatchNo, FinEvent, FinTournament, if(EnNameOrder=1, concat(ucase(EnFirstName), " ", EnName), concat(EnName, " ", ucase(EnFirstName))) as op64 from Finals inner join Entries on EnId=FinAthlete and EnTournament=FinTournament) opp64 ON opp64.FinEvent=EvCode AND opp64.FinTournament=EvTournament AND opp64.FinMatchno=if(f64.FinMatchNo%2=1, f64.FinMatchNo-1, f64.FinMatchNo+1)'
            . ' LEFT JOIN (select FinMatchNo, FinEvent, FinTournament, if(EnNameOrder=1, concat(ucase(EnFirstName), " ", EnName), concat(EnName, " ", ucase(EnFirstName))) as op32 from Finals inner join Entries on EnId=FinAthlete and EnTournament=FinTournament) opp32 ON opp32.FinEvent=EvCode AND opp32.FinTournament=EvTournament AND opp32.FinMatchno=if(f32.FinMatchNo%2=1, f32.FinMatchNo-1, f32.FinMatchNo+1)'
            . ' LEFT JOIN (select FinMatchNo, FinEvent, FinTournament, if(EnNameOrder=1, concat(ucase(EnFirstName), " ", EnName), concat(EnName, " ", ucase(EnFirstName))) as op16 from Finals inner join Entries on EnId=FinAthlete and EnTournament=FinTournament) opp16 ON opp16.FinEvent=EvCode AND opp16.FinTournament=EvTournament AND opp16.FinMatchno=if(f16.FinMatchNo%2=1, f16.FinMatchNo-1, f16.FinMatchNo+1)'
            . ' LEFT JOIN (select FinMatchNo, FinEvent, FinTournament, if(EnNameOrder=1, concat(ucase(EnFirstName), " ", EnName), concat(EnName, " ", ucase(EnFirstName))) as  op8 from Finals inner join Entries on EnId=FinAthlete and EnTournament=FinTournament) opp8  ON  opp8.FinEvent=EvCode AND  opp8.FinTournament=EvTournament AND  opp8.FinMatchno=if( f8.FinMatchNo%2=1,  f8.FinMatchNo-1,  f8.FinMatchNo+1)'
            . ' LEFT JOIN (select FinMatchNo, FinEvent, FinTournament, if(EnNameOrder=1, concat(ucase(EnFirstName), " ", EnName), concat(EnName, " ", ucase(EnFirstName))) as  op4 from Finals inner join Entries on EnId=FinAthlete and EnTournament=FinTournament) opp4  ON  opp4.FinEvent=EvCode AND  opp4.FinTournament=EvTournament AND  opp4.FinMatchno=if( f4.FinMatchNo%2=1,  f4.FinMatchNo-1,  f4.FinMatchNo+1)'
            . ' LEFT JOIN (select FinMatchNo, FinEvent, FinTournament, if(EnNameOrder=1, concat(ucase(EnFirstName), " ", EnName), concat(EnName, " ", ucase(EnFirstName))) as  op2 from Finals inner join Entries on EnId=FinAthlete and EnTournament=FinTournament) opp2  ON  opp2.FinEvent=EvCode AND  opp2.FinTournament=EvTournament AND  opp2.FinMatchno=if( f2.FinMatchNo%2=1,  f2.FinMatchNo-1,  f2.FinMatchNo+1)'
            . ' LEFT JOIN (select FinMatchNo, FinEvent, FinTournament, if(EnNameOrder=1, concat(ucase(EnFirstName), " ", EnName), concat(EnName, " ", ucase(EnFirstName))) as  opB from Finals inner join Entries on EnId=FinAthlete and EnTournament=FinTournament) oppb  ON  oppb.FinEvent=EvCode AND  oppb.FinTournament=EvTournament AND  oppb.FinMatchno=if( fb.FinMatchNo%2=1,  fb.FinMatchNo-1,  fb.FinMatchNo+1)'
            . ' LEFT JOIN (select FinMatchNo, FinEvent, FinTournament, if(EnNameOrder=1, concat(ucase(EnFirstName), " ", EnName), concat(EnName, " ", ucase(EnFirstName))) as  opG from Finals inner join Entries on EnId=FinAthlete and EnTournament=FinTournament) oppg  ON  oppg.FinEvent=EvCode AND  oppg.FinTournament=EvTournament AND  oppg.FinMatchno=if( fg.FinMatchNo%2=1,  fg.FinMatchNo-1,  fg.FinMatchNo+1)'


            . ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=0 ';
		if($Events)
			$MyQuery.= "AND EvCode in (" . implode(',', $Events) . ") ";
        $MyQuery .= ' ORDER BY EvCode, f.FinMatchNo ';
	}

	$Rs=safe_r_sql($MyQuery);
// Se il Recordset è valido e contiene almeno una riga
	if (safe_num_rows($Rs)>0)
	{
		$defArrowTotW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(6/13);// 1 time;
		$defTotalW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(3/13);// 2 times;
		$defGoldW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(1/13);// 1 time;
		$TopPage=50;
		$ScoreHeight=($pdf->GetPageHeight()-$TopPage-35)/3;

		$WhereStartX=array($pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2,$pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2,$pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2);
		$WhereStartY=array($TopPage, $TopPage, $TopPage+5+$ScoreHeight, $TopPage+5+$ScoreHeight, $TopPage+10+$ScoreHeight*2, $TopPage+10+$ScoreHeight*2);
		$WhereX=NULL;
		$WhereY=NULL;
		$RowNo=0;
		while($MyRow=safe_fetch($Rs)) {
			// sets the corrects headers based on the Events...
			$Fasi = array(get_text('64_Phase'),get_text('32_Phase'), get_text('16_Phase'), get_text('8_Phase'), get_text('4_Phase'), get_text('2_Phase'), get_text('ScoreFinalMatch', 'Tournament'));

			switch($MyRow->EvFinalFirstPhase) {
				case 64:
					$Fasi[0]=get_text('64_Phase');
					$Fasi[1]=get_text('32_Phase');
					break;
				case 48:
					$Fasi[0]=get_text('48_Phase');
					$Fasi[1]=get_text('24_Phase');
					break;
				default:
					$Fasi[$Start2FirstPhase[$MyRow->EvFinalFirstPhase]]=get_text($MyRow->EvFinalFirstPhase.'_Phase');
			}

			if($RowNo++ != 0)
				$pdf->AddPage();
			$WhereX=$WhereStartX;
			$WhereY=$WhereStartY;
//Intestazione Atleta
			$pdf->SetY(30);
		   	$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell($pdf->GetPageWidth()*0.1,5,(get_text('Athlete')) . ': ','TL',0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($pdf->GetPageWidth()*0.8-20,5, ($MyRow->Athlete),'T',1,'L',0);
		   	$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell($pdf->GetPageWidth()*0.1,5,(get_text('Country')) . ': ','L',0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($pdf->GetPageWidth()*0.8-20,5, ($MyRow->CoName . (strlen($MyRow->CoCode)>0 ?  ' (' . $MyRow->CoCode  . ')' : '')),0,1,'L',0);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell($pdf->GetPageWidth()*0.1,5,(get_text('DivisionClass')) . ': ','LB',0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($pdf->GetPageWidth()*0.8-20,5, get_text($MyRow->EvEventName,'','',true),'B',1,'L',0);

			$pdf->SetXY($pdf->GetPageWidth()*0.9-10,33);
			$pdf->SetFont($pdf->FontStd,'B',25);
			$pdf->Cell($pdf->GetPageWidth()*0.1,12, ($MyRow->GrPosition),'BLR',1,'C',1);
			$pdf->SetXY($pdf->GetPageWidth()*0.9-10,30);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($pdf->GetPageWidth()*0.1,5, (get_text('Rank')),'TLR',1,'C',1);

			$WhichScoreEnd=7;
			if ($MyRow->EvFinalFirstPhase==64 || $MyRow->EvFinalFirstPhase==48)
				$WhichScoreEnd=6;
			for($WhichScore = $Start2FirstPhase[$MyRow->EvFinalFirstPhase];$WhichScore<$WhichScoreEnd;$WhichScore++) {
				DrawScore($pdf, $MyRow, $WhichScore, $WhereX[$WhichScore-$Start2FirstPhase[$MyRow->EvFinalFirstPhase]], $WhereY[$WhichScore- $Start2FirstPhase[$MyRow->EvFinalFirstPhase]], $WhichScore==6);
			}
		}
		safe_free_result($Rs);
	}

$pdf->Output();


function DrawScore(&$pdf, $MyRow, $WhichScore, $WhereX, $WhereY, $FinalScore=false) {
    global $defTotalW, $defGoldW, $defArrowTotW, $ScoreHeight, $NumFasi, $Fasi, $TgtNoFasi, $ArrowFasi, $TieFasi, $ByeFasi, $Score3D, $OppArray, $Start2FirstPhase;

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
	$CellH=($ScoreHeight - 6*6)/$NumRow;

	//Header
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->Cell($GoldW,6,'',0,0,'C',0);
	$pdf->Cell(2*$TotalW+$defArrowTotW, 6, ($Fasi[$WhichScore]),1,(is_null($MyRow->{$TgtNoFasi[$WhichScore]}) ? 1 : 0),'C',1);

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

	if($MyRow->EvMatchMode) {
		$pdf->Cell($TotalW * 4/5, 6,get_text('SetTotal','Tournament'),1,0,'C',1);
		$pdf->Cell($TotalW * 4/5, 6,get_text('SetPoints', 'Tournament'),1,0,'C',1);
		$pdf->Cell($TotalW * 2/5, 6,get_text('TotalShort','Tournament'),1,0,'C',1);
	} else {

		$pdf->Cell($TotalW,6,get_text('TotalProg','Tournament'),1,0,'C',1);
		$pdf->Cell($TotalW,6,get_text('TotalShort','Tournament'),1,0,'C',1);
	}
	$pdf->ln();
	$WhereY=$pdf->GetY();

	//Righe
    $runTot = 0;
    for ($i = 1; $i <= $NumRow; $i++) {
        $runRow = 0;
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->SetXY($WhereX,$WhereY);
		$pdf->Cell($GoldW,$CellH,$i,1,0,'C',1);
        if ($Score3D) {
			$pdf->SetFont($pdf->FontStd,'',8);
			$pdf->Cell(1/5*$ArrowW,$CellH, '11', 1, 0, 'C', 0);
			$pdf->Cell(1/5*$ArrowW,$CellH, '10', 1, 0, 'C', 0);
			$pdf->Cell(1/5*$ArrowW,$CellH, '8', 1, 0, 'C', 0);
			$pdf->Cell(1/5*$ArrowW,$CellH, '5', 1, 0, 'C', 0);
			$pdf->Cell(1/5*$ArrowW,$CellH, 'M', 1, 0, 'C', 0);
        } else {
            $pdf->SetFont($pdf->FontStd, '', 10);
            for ($j = 0; $j < $NumCol; $j++) {
            	$tmp='';
            	if(isset($_REQUEST["ScoreFilled"]) AND isset($MyRow->{$ArrowFasi[$WhichScore]})) {
            		$Arrow=substr($MyRow->{$ArrowFasi[$WhichScore]}, ($i - 1) * $NumCol + $j, 1);
                    $tmp = DecodeFromLetter($Arrow);
                    $runRow += ValutaArrowString($Arrow);
	            }
                $pdf->Cell($ArrowW, $CellH, $tmp, 1, 0, 'C', 0);
            }
            $runTot += $runRow;
		}
        if ($MyRow->EvMatchMode == 0) {
	        $pdf->Cell($TotalW, $CellH, ((isset($_REQUEST["ScoreFilled"]) AND !empty($MyRow->{$ArrowFasi[$WhichScore]}) AND $MyRow->EvMatchMode == 0) ? $runRow : ''), 1, 0, 'C', 0);
	        $pdf->Cell($TotalW, $CellH, ((isset($_REQUEST["ScoreFilled"]) AND !empty($MyRow->{$ArrowFasi[$WhichScore]}) AND $MyRow->EvMatchMode == 0) ? $runTot : ''), 1, 0, 'C', 0);
        } else {
	        $pdf->Cell($TotalW * 4/5, $CellH, ((isset($_REQUEST["ScoreFilled"]) AND !empty($MyRow->{$ArrowFasi[$WhichScore]}) AND $MyRow->EvMatchMode == 0) ? $runRow : ''), 1, 0, 'C', 0);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($TotalW * 4/15,$CellH,'2',1, 0,'C',0);
			$pdf->Cell($TotalW * 4/15,$CellH,'1',1, 0,'C',0);
			$pdf->Cell($TotalW * 4/15,$CellH,'0',1, 0,'C',0);
			$pdf->Cell($TotalW * 2/5,$CellH,'',1, 0,'C',0);
		}
		$pdf->ln();

		$WhereY=$pdf->GetY();
	}

	//Shoot Off
	$pdf->SetXY($WhereX,$WhereY+0.5);
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell($GoldW,6.5,(get_text('TB')),1,0,'C',1);
	$ShootOffW=($NumSO<=$NumCol ? $ArrowW : ($ArrowW*$NumCol)/$NumSO);
    $hasClosest = false;
    for ($j = 0; $j < $NumSO; $j++) {
	    $tmp=array('', 0, 0);
	    if(isset($_REQUEST["ScoreFilled"]) AND isset($MyRow->{$TieFasi[$WhichScore]})) {
		    $tmp=substr(ValutaArrowStringSO($MyRow->{$TieFasi[$WhichScore]}), $j, 1);
	    }

		$pdf->SetXY($pdf->GetX()+0.5,$pdf->GetY());
        $pdf->Cell($ShootOffW - 0.5, 4, $tmp[0], 1, 0, 'C', 0);
        if ($tmp[2]) {
            $hasClosest = true;
        }
	}
	$pdf->SetXY($WhereX+$GoldW+0.5,$WhereY+5);
	$pdf->SetFont($pdf->FontStd,'',1);
    $pdf->Cell(2, 2, '', 1, 0, 'R', $hasClosest);
	$pdf->SetXY($WhereX+$GoldW+2.5,$WhereY+4.5);
	$pdf->SetFont($pdf->FontStd,'',6);
	$pdf->Cell($ArrowW*1.5, 2.5, get_text('Close2Center','Tournament'),0,0,'L',0);
	$pdf->Cell($ArrowW*($NumCol-2)+$TotalW, 2.5, get_text('ArcherSignature','Tournament'),0,0,'C',0);

	if($MyRow->{$ByeFasi[$WhichScore]}) {
		$Name='';
		$OppName='';
	} else {
		$Name=$MyRow->Athlete;
		$OppName=$MyRow->{$OppArray[$WhichScore]};
	}
	//Totale
	$pdf->SetXY($WhereX+$GoldW+$ArrowW, $WhereY);
	$pdf->SetFont($pdf->FontStd,'B',10);
    if ($MyRow->EvMatchMode == 0) {
		$pdf->Cell(($ArrowW*($NumCol-1))+$TotalW-9, 6, '', 0, 0, 'C', 0);
		$pdf->Cell(9, 6, get_text('Total'), 0,0,'R',0);
        $pdf->Cell($TotalW, 6, ((isset($_REQUEST["ScoreFilled"]) AND $runTot != 0) ? $runTot : ''), 1, 0, 'C', 0);
    } else {
		$pdf->Cell($defArrowTotW-$ArrowW+$TotalW*3/5, 6, '',0,0,'C',0);
		$pdf->Cell($TotalW, 6,get_text('Total'),0,0,'R',0);
		$pdf->Cell(2/5*$TotalW,6,'',1,0,'C',0);
	}
	$pdf->ln();

	// Opponent score summary
	$WhereY=$pdf->GetY()+4;
    if($MyRow->{$ByeFasi[$WhichScore]}) {
	    $OppName=get_text('Bye');
    }
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->Cell($GoldW + $ArrowW , 5, (get_text('Opponent', 'Tournament')),0,0,'L',0);
	$pdf->Cell(2*$defTotalW + ($defArrowTotW-$ArrowW-$GoldW) + $defGoldW, 5, $OppName, 'B', 1,'L',0);


	$WhereY=$pdf->GetY()+1;
    //$pdf->line($WhereX, $WhereY+0.5, $WhereX+$GoldW+$ArrowW*$NumCol+$TotalW*2+$defGoldW*2, $WhereY+0.5);
    //$pdf->line($WhereX, $WhereY+7, $WhereX+$GoldW+$ArrowW*$NumCol+$TotalW*2+$defGoldW*2, $WhereY+7);
	$pdf->SetXY($WhereX,$WhereY+0.5);
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell($GoldW, 6.5, (get_text('TB')),1,0,'C',1);
	$ShootOffW=($NumSO<=$NumCol ? $ArrowW : ($ArrowW*$NumCol)/$NumSO);
    $hasClosest = false;
    for ($j = 0; $j < $NumSO; $j++) {
	    $tmp=array('', 0, 0);
	    if(isset($_REQUEST["ScoreFilled"]) AND isset($MyRow->{$TieFasi[$WhichScore]})) {
		    $tmp=substr(ValutaArrowStringSO($MyRow->{$TieFasi[$WhichScore]}), $j, 1);
	    }

		$pdf->SetXY($pdf->GetX()+0.5,$pdf->GetY());
        $pdf->Cell($ShootOffW - 0.5, 4, $tmp[0], 1, 0, 'C', 0);
        if ($tmp[2]) {
            $hasClosest = true;
        }
	}
	$pdf->SetXY($WhereX+$GoldW+0.5,$WhereY+5);
	$pdf->SetFont($pdf->FontStd,'',1);
    $pdf->Cell(2, 2, '', 1, 0, 'R', $hasClosest);
	$pdf->SetXY($WhereX+$GoldW+2.5,$WhereY+4.5);
	$pdf->SetFont($pdf->FontStd,'',6);
	$pdf->Cell($ArrowW*1.5, 2.5,get_text('Close2Center','Tournament'),0,0,'L',0);
	$pdf->Cell($ArrowW*($NumCol-2)+$TotalW, 2.5,get_text('OpponentSignature','Tournament'),0,0,'C',0);


	//Totale
	$pdf->SetXY($WhereX+$GoldW+$ArrowW, $WhereY+0.5);
	$pdf->SetFont($pdf->FontStd,'B',10);
    if ($MyRow->EvMatchMode == 0) {
		$pdf->Cell($TotalW + ($ArrowW*($NumCol-1)) - 9, 6.5,'',0,0,'C',0);
		$pdf->Cell(9, 6.5,(get_text('Total')),0,0,'R',0);
        $pdf->Cell($TotalW, 6.5, ((isset($_REQUEST["ScoreFilled"]) AND $runTot != 0) ? $runTot : ''), 1, 0, 'C', 0);
    } else {
		$pdf->Cell($defArrowTotW+$TotalW*3/5 - $ArrowW, 6.5, '',0,0,'C',0);
		$pdf->Cell($TotalW, 6.5, get_text('Total'),0,0,'R',0);
		$pdf->Cell(2/5*$TotalW,6.5,'',1,0,'C',0);
	}
	$pdf->ln();

    // draw a separation line
	$pdf->SetLineWidth(0.75);
	//$pdf->line($scoreStartX-5, $scoreStartY + $ScoreHeight+3, $scoreStartX+(2*$defTotalW + $defArrowTotW + $defGoldW)+5, $scoreStartY + $ScoreHeight+3);

	// draws a line on unused scorecards
	if($MyRow->{$ByeFasi[$WhichScore]}) {
		$pdf->Line($scoreStartX,$scoreStartY,$scoreStartX+(2*$defTotalW + $defArrowTotW + $defGoldW),$scoreStartY + $ScoreHeight);
	}
	$pdf->SetLineWidth(0.1);

}
?>