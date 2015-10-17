<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ScorePDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');

$pdf = new ScorePDF(true);

$NumEnd = 8;
//$MyQuery = "SELECT TtNumEnds FROM Tournament INNER JOIN Tournament*Type AS tt ON ToType=TtId WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$MyQuery = "SELECT ToNumEnds AS TtNumEnds FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$Rs=safe_r_sql($MyQuery);
if(safe_num_rows($Rs)==1) {
	$r=safe_fetch($Rs);
	$NumEnd=$r->TtNumEnds;
}

$NumEnd = (!empty($_REQUEST['x_Session']) && $_REQUEST['x_Session']==1 ? 8 : 12);

if(!(isset($_REQUEST["ScoreHeader"]) && $_REQUEST["ScoreHeader"]==1))
	$pdf->HideHeader();

if(!(isset($_REQUEST["ScoreLogos"]) && $_REQUEST["ScoreLogos"]==1))
	$pdf->HideLogo();

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST["ScoreDraw"]=="Data")
	$pdf->NoDrawing();

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST['ScoreDraw']=="Draw")
{
	$pdf->AddPage();
	$pdf->DrawScoreField(8,8,194,132.5,$NumEnd/2,3,array("tNo"=>'',"startTarget"=>''));
	$pdf->DrawScoreField(8,156.5,194,132.5,$NumEnd/2,3,array("tNo"=>'',"startTarget"=>''));
}
else
{
	$MyQuery = 'SELECT SUBSTRING(ElTargetNo,1,4) as tNo, SesName, CONCAT(EnFirstName,\' \', EnName) AS Ath, CONCAT(CoCode, \' - \', CoName) as Noc, ElEventCode AS Cat , ElElimPhase, IFNULL(SesFirstTarget,0) as FirstTarget  '
		. ", CoCode, CoName  "
		. " FROM Eliminations AS q  "
		. " INNER JOIN Entries AS e ON q.ElId=e.EnId AND EnAthlete=1 "
		. " INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
		. " LEFT JOIN Session ON ElSession=SesOrder AND ElTournament=SesTournament AND SesType='E' "
		. " WHERE EnTournament =  " . StrSafe_DB($_SESSION['TourId']) . ' '
		. (!(empty($_REQUEST['x_From']) && empty($_REQUEST['x_To'])) ? " AND ElTargetNo>='" . str_pad($_REQUEST['x_From'],'3','0',STR_PAD_LEFT) . "A' AND ElTargetNo<='" . str_pad($_REQUEST['x_To'],'3','0',STR_PAD_LEFT) . "Z' AND ElElimPhase= " . ($_REQUEST['x_Session']) . " " : "")
		. (!empty($_REQUEST['x_ElimSession']) ? " AND ElSession=". $_REQUEST['x_ElimSession'] . " " : "")
		. ' ORDER BY ElSession, SesOrder, ElElimPhase, ElTargetNo ASC, EnFirstName, EnName, CoCode';
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)>0)
	{
		while($MyRow=safe_fetch($Rs))
		{
				$Value = array(
					"tNo"=>$MyRow->tNo,
					"startTarget"=>(substr($MyRow->tNo,0,-1)*1),
					"firstTarget"=>$MyRow->FirstTarget,
					"Cat"=>$MyRow->Cat,
					"Dist"=>'',
					"Ath"=>$MyRow->Ath . ' - ' .  get_text('Eliminations_' . ($MyRow->ElElimPhase+1)) . ($MyRow->SesName?" ($MyRow->SesName)":''),
					"Noc"=>$MyRow->Noc,
					'CoCode' => $MyRow->CoCode,
					'CoName' => $MyRow->CoName,
						);
				if(substr($Value["tNo"],-1,1)=="A")
				{
					$pdf->AddPage();
					$pdf->DrawScoreField(8,8,194,132.5,$NumEnd/2,3,$Value);
				}
				elseif(substr($Value["tNo"],-1,1)=="B")
				{
					$pdf->DrawScoreField(8,156.5,194,132.5,$NumEnd/2,3,$Value);
				}
				elseif(substr($Value["tNo"],-1,1)=="C")
				{
					$pdf->AddPage();
					$pdf->DrawScoreField(8,8,194,132.5,$NumEnd/2,3,$Value);
				}
				elseif(substr($Value["tNo"],-1,1)=="D")
				{
					$pdf->DrawScoreField(8,156.5,194,132.5,$NumEnd/2,3,$Value);
				}
				elseif(substr($Value["tNo"],-1,1)=="E")
				{
					$pdf->AddPage();
					$pdf->DrawScoreField(8,8,194,132.5,$NumEnd/2,3,$Value);
				}
				elseif(substr($Value["tNo"],-1,1)=="F")
				{
					$pdf->DrawScoreField(8,156.5,194,132.5,$NumEnd/2,3,$Value);
				}
		}
		safe_free_result($Rs);
	}
}
$pdf->Output();
?>