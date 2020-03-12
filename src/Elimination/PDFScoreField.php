<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ScorePDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
checkACL(AclEliminations, AclReadOnly);

$pdf = new ScorePDF(true);
$pdf->BottomImage=empty($_REQUEST['QRCode']);

$NumEnd = 8;
//$MyQuery = "SELECT TtNumEnds FROM Tournament INNER JOIN Tournament*Type AS tt ON ToType=TtId WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$MyQuery = "SELECT ToNumEnds AS TtNumEnds FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$Rs=safe_r_sql($MyQuery);
if(safe_num_rows($Rs)==1) {
	$r=safe_fetch($Rs);
	$NumEnd=$r->TtNumEnds;
}

$FillWithArrows = (isset($_REQUEST["ScoreFilled"]) && $_REQUEST["ScoreFilled"]==1);
$pdf->FillWithArrows=$FillWithArrows;

if(!empty($_REQUEST["ScoreBarcode"])) $pdf->PrintBarcode=true;

$NumEnd = (!empty($_REQUEST['x_Session']) && $_REQUEST['x_Session']==1 ? 8 : 12);

if(!(isset($_REQUEST["ScoreHeader"]) && $_REQUEST["ScoreHeader"]==1))
	$pdf->HideHeader();

if(!(isset($_REQUEST["ScoreLogos"]) && $_REQUEST["ScoreLogos"]==1))
	$pdf->HideLogo();

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST["ScoreDraw"]=="Data")
	$pdf->NoDrawing();

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST['ScoreDraw']=="Draw") {
	$Value=array(
		"EnCode"=>'',
		"Div"=>'',
		"Cls"=>'',
		"tNo"=>'',
		"startTarget"=>'',
		"firstTarget"=>'',
		"Cat"=>'',
		"Dist"=>'',
		"Ath"=>'',
		"Noc"=>'',
		'CoCode' => '',
		'CoName' => '',
		'ElPhase' => '',
		'ElCode' => '',
		"D"=>'',
		"gxD"=>'',
		"Arr"=>'',
		"QuD"=>'',
		"QuGD"=>'',
		"QuXD"=>'',
	);
	$q=safe_r_sql("(select '0' as Phase, EvProgr, EvCode, EvE1Arrows EvArrows, EvE1Ends EvEnds from Events where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " and EvElimType in (1,2) and EvElim1>0)
		union
		(select '1' as Phase, EvProgr, EvCode, EvE2Arrows EvArrows, EvE2Ends EvEnds from Events where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " and EvElimType in (1,2) and EvElim2>0)
		order by EvProgr, Phase");
	while($r=safe_fetch($q)) {
		$pdf->AddPage();
		$Value['ElCode']=$r->EvCode;
		$Value['Cat']=$r->EvCode;
		$pdf->DrawScoreField(8,8,194,132.5,$r->EvEnds/2, $r->EvArrows, $Value);
		$pdf->DrawScoreField(8,156.5,194,132.5,$r->EvEnds/2,$r->EvArrows, $Value);
	}
} else {
	$MyQuery = "SELECT EnCode, EnClass, EnDivision, ElEventCode, ElElimPhase, SUBSTRING(ElTargetNo,1,4) as tNo, SesName, CONCAT(EnFirstName,' ', EnName) AS Ath, CONCAT(CoCode, ' - ', CoName) as Noc, ElEventCode AS Cat , ElElimPhase, IFNULL(SesFirstTarget,0) as FirstTarget,  
		CoCode, CoName, (ElGold+ElXNine)  as printDXgx, ElArrowstring, ElScore, ElGold, ElXNine, if(ElElimPhase=0, EvE1Ends, EvE2Ends) EvEnds, if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) EvArrows  
		FROM Eliminations AS q  
		inner JOIN Events ON EvCode=ElEventCode and EvTeamEvent=0 and EvTournament=ElTournament
		left JOIN Entries AS e ON q.ElId=e.EnId AND EnAthlete=1 
		left JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament 
		LEFT JOIN Session ON ElSession=SesOrder AND ElTournament=SesTournament AND SesType='E' 
		WHERE ElTournament =  " . StrSafe_DB($_SESSION['TourId']) . ' '
		. (!(empty($_REQUEST['x_From']) && empty($_REQUEST['x_To'])) ? " AND ElTargetNo>='" . str_pad($_REQUEST['x_From'],'3','0',STR_PAD_LEFT) . "A' AND ElTargetNo<='" . str_pad($_REQUEST['x_To'],'3','0',STR_PAD_LEFT) . "Z' AND ElElimPhase= " . ($_REQUEST['x_Session']) . " " : "")
		. (!empty($_REQUEST['x_ElimSession']) ? " AND ElSession=". $_REQUEST['x_ElimSession'] . " " : "")
		. ' ORDER BY ElSession, SesOrder, ElElimPhase, ElTargetNo ASC, EnFirstName, EnName, CoCode';
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)>0) {
		while($MyRow=safe_fetch($Rs)) {
				$Value = array(
					"EnCode"=>$MyRow->EnCode,
					"Div"=>$MyRow->EnDivision,
					"Cls"=>$MyRow->EnClass,
					"tNo"=>$MyRow->tNo,
					"startTarget"=>(substr($MyRow->tNo,0,-1)*1),
					"firstTarget"=>$MyRow->FirstTarget,
					"Cat"=>$MyRow->Cat,
					"Dist"=>'E'.($MyRow->ElElimPhase+1),
                    "CurDist"=>'E'.($MyRow->ElElimPhase+1),
					"Ath"=>$MyRow->Ath . ' - ' .  get_text('Eliminations_' . ($MyRow->ElElimPhase+1)) . ($MyRow->SesName?" ($MyRow->SesName)":''),
					"Noc"=>$MyRow->Noc,
					'CoCode' => $MyRow->CoCode,
					'CoName' => $MyRow->CoName,
					'ElPhase' => $MyRow->ElElimPhase,
					'ElCode' => $MyRow->ElEventCode,
					"D"=>'',
					"gxD"=>$MyRow->printDXgx,
					"Arr"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->ElArrowstring : ''),
					"QuD"=>$MyRow->ElScore,
					"QuGD"=>$MyRow->ElGold,
					"QuXD"=>$MyRow->ElXNine,
						);
				if(substr($Value["tNo"],-1,1)=="A")
				{
					$pdf->AddPage();
					$pdf->DrawScoreField(8,8,194,132.5,$MyRow->EvEnds, $MyRow->EvArrows, $Value);
				}
				elseif(substr($Value["tNo"],-1,1)=="B")
				{
					$pdf->DrawScoreField(8,156.5,194,132.5,$MyRow->EvEnds, $MyRow->EvArrows, $Value);
				}
				elseif(substr($Value["tNo"],-1,1)=="C")
				{
					$pdf->AddPage();
					$pdf->DrawScoreField(8,8,194,132.5,$MyRow->EvEnds, $MyRow->EvArrows, $Value);
				}
				elseif(substr($Value["tNo"],-1,1)=="D")
				{
					$pdf->DrawScoreField(8,156.5,194,132.5,$MyRow->EvEnds, $MyRow->EvArrows, $Value);
				}
				elseif(substr($Value["tNo"],-1,1)=="E")
				{
					$pdf->AddPage();
					$pdf->DrawScoreField(8,8,194,132.5,$MyRow->EvEnds, $MyRow->EvArrows, $Value);
				}
				elseif(substr($Value["tNo"],-1,1)=="F")
				{
					$pdf->DrawScoreField(8,156.5,194,132.5,$MyRow->EvEnds, $MyRow->EvArrows, $Value);
				}
		}
		safe_free_result($Rs);
	}
}
$pdf->Output();
?>