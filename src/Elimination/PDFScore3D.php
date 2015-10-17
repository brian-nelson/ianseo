<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ScorePDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');

$pdf = new ScorePDF(true);

$NumEnd = 6;

if(!(isset($_REQUEST["ScoreHeader"]) && $_REQUEST["ScoreHeader"]==1))
	$pdf->HideHeader();

if(!(isset($_REQUEST["ScoreLogos"]) && $_REQUEST["ScoreLogos"]==1))
	$pdf->HideLogo();

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST["ScoreDraw"]=="Data")
	$pdf->NoDrawing();

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST['ScoreDraw']=="Draw")
{
	$pdf->AddPage();
	$pdf->DrawScore3D(8,8,194,132.5,$NumEnd,array("tNo"=>""),false);
	$pdf->DrawScore3D(8,156.5,194,132.5,$NumEnd,array("tNo"=>""),false);
}
else
{
	$MyQuery = 'SELECT SUBSTRING(ElTargetNo,1,4) as tNo, CONCAT(EnFirstName,\' \', EnName) AS Ath, CONCAT(CoCode, \' - \', CoName) as Noc, ElEventCode AS Cat , ElElimPhase, '
		. " EvElimEnds AS CalcEnds, EvElimArrows AS CalcArrows, EvElimSO AS CalcSO, SesName, IFNULL(SesFirstTarget,0) as FirstTarget "
		. " FROM Eliminations AS q  "
		. " INNER JOIN Entries AS e ON q.ElId=e.EnId AND EnAthlete=1 AND q.ElTournament=e.EnTournament "
		. " INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
		. " INNER JOIN Events AS ev ON ev.EvCode=q.ElEventCode AND ev.EvTournament=e.EnTournament AND ev.EvTeamEvent=0 "
		. " LEFT JOIN Session ON ElSession=SesOrder AND ElTournament=SesTournament AND SesType='E' "
		. " WHERE EnTournament =  " . StrSafe_DB($_SESSION['TourId']) . ' '
		. (!(empty($_REQUEST['x_From']) && empty($_REQUEST['x_To'])) ? " AND ElTargetNo>='" . str_pad($_REQUEST['x_From'],'3','0',STR_PAD_LEFT) . "A' AND ElTargetNo<='" . str_pad($_REQUEST['x_To'],'3','0',STR_PAD_LEFT) . "Z' AND ElElimPhase= " . ($_REQUEST['x_Session']) . " " : "")
		. (!empty($_REQUEST['x_ElimSession']) ? " AND ElSession=". $_REQUEST['x_ElimSession'] . " " : "")
		. ' ORDER BY SesOrder, ElElimPhase, /*ElEventCode,*/ ElTargetNo ASC, EnFirstName, EnName, CoCode';
	$Rs=safe_r_sql($MyQuery);

	//debug_svela($MyQuery);
	//print $MyQuery;exit;
	if(safe_num_rows($Rs)>0)
	{
		$first=true;
		while($MyRow=safe_fetch($Rs))
		{
				$Value = array(
					"tNo"=>$MyRow->tNo,
					"startTarget"=>(substr($MyRow->tNo,0,-1)*1),
					"firstTarget"=>$MyRow->FirstTarget,
					"Cat"=>$MyRow->Cat,
					"Dist"=>'',
					"Ath"=>$MyRow->Ath . ' - ' .  get_text('Eliminations_' . ($MyRow->ElElimPhase+1)) . ($MyRow->SesName=='' || is_null($MyRow->SesName) ? '' : ' (' . $MyRow->SesName . ')'),
					"Noc"=>$MyRow->Noc);
				if(substr($Value["tNo"],-1,1)=="A")
				{
					$pdf->AddPage();
					$pdf->DrawScore3D(8,8,194,132.5,$MyRow->CalcEnds/2,$Value,false);
					$first=false;
				}
				elseif(substr($Value["tNo"],-1,1)=="B")
				{
					if($first) $pdf->AddPage();
					$pdf->DrawScore3D(8,156.5,194,132.5,$MyRow->CalcEnds/2,$Value,false);
					$first=true;
				}
				elseif(substr($Value["tNo"],-1,1)=="C")
				{
					$pdf->AddPage();
					$pdf->DrawScore3D(8,8,194,132.5,$MyRow->CalcEnds/2,$Value,false);
					$first=false;
				}
				elseif(substr($Value["tNo"],-1,1)=="D")
				{
					if($first) $pdf->AddPage();
					$pdf->DrawScore3D(8,156.5,194,132.5,$MyRow->CalcEnds/2,$Value,false);
					$first=true;
				}
				elseif(substr($Value["tNo"],-1,1)=="E")
				{
					$pdf->AddPage();
					$pdf->DrawScore3D(8,8,194,132.5,$MyRow->CalcEnds/2,$Value,false);
					$first=false;
				}
				elseif(substr($Value["tNo"],-1,1)=="F")
				{
					if($first) $pdf->AddPage();
					$pdf->DrawScore3D(8,156.5,194,132.5,$MyRow->CalcEnds/2,$Value,false);
					$first=true;
				}
		}
		safe_free_result($Rs);
	}
}
$pdf->Output();
?>