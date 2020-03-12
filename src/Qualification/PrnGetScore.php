<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/LabelPDF.inc.php');
require_once('Common/Fun_Sessions.inc.php');

if(CheckTourSession()) {
    checkACL(AclQualification, AclReadOnly);

	$pdf = new LabelPDF();

	$ath4target=0;
	$RowsPerPage=4;
	$ses=GetSessions('Q');
	foreach ($ses as $s)
	{
		$ath4target=max($ath4target, $s->SesAth4Target);
		if($ath4target>8) {
			$RowsPerPage=2;
		} elseif($ath4target>4) {
			$RowsPerPage=3;
		}
	}



	$MyQuery = "SELECT ToElabTeam, ToNumEnds, EnName AS Name, EnFirstName AS FirstName, SUBSTRING(AtTargetNo,1,1) AS Session, SUBSTRING(AtTargetNo,2," . TargetNoPadding . ") AS TargetNo,  SUBSTRING(AtTargetNo,-1,1) AS BackNo ";
	$MyQuery.= "FROM AvailableTarget at ";
	$MyQuery.= "INNER JOIN Tournament AS t ON t.ToId=at.AtTournament ";
	if((isset($_REQUEST["noEmpty"]) && $_REQUEST["noEmpty"]==1))
	{
		$MyQuery .= "INNER JOIN
				(SELECT DISTINCT EnTournament, SUBSTRING(QuTargetNo,1,4) as TgtNo
				FROM Qualifications
				INNER JOIN Entries On QuId=EnId
				WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 AND QuTargetNo>='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_From'],TargetNoPadding,"0",STR_PAD_LEFT) . "A' AND QuTargetNo<='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_To'],TargetNoPadding,"0",STR_PAD_LEFT) . "Z'
				) as Tgt ON at.AtTournament=Tgt.EnTournament AND SUBSTRING(at.AtTargetNo,1,4)=Tgt.TgtNo	";
	}
	$MyQuery.= "LEFT JOIN ";
	$MyQuery.= "(SELECT EnName, EnFirstName, EnTournament, QuTargetNo ";
	$MyQuery.= "FROM Entries AS e INNER JOIN Qualifications AS q ON e.EnId=q.QuId AND e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1) sq ";
	$MyQuery.= "ON at.AtTournament=sq.EnTournament AND at.AtTargetNo=sq.QuTargetNo ";
	$MyQuery.= "WHERE AtTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
	if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
		$MyQuery .= "AND SUBSTRING(AtTargetNo,1,1) = " . StrSafe_DB($_REQUEST["Session"]) . " ";
	elseif ($_REQUEST['x_Session']>0)
		$MyQuery.= " AND at.AtTargetNo>='" . $_REQUEST['x_Session'] . (strlen($_REQUEST['x_From'])<2 ? '0' : '') . $_REQUEST['x_From'] . "A' AND at.AtTargetNo<='" . $_REQUEST['x_Session'] . (strlen($_REQUEST['x_To'])<2 ? '0' : '') . $_REQUEST['x_To'] . "Z' ";



	$MyQuery.= "ORDER BY if(ToElabTeam=2, (substr(AtTargetNo,2 , 3)-1)%ToNumEnds, 1), AtTargetNo, Name, FirstName ";

	$Rs=safe_r_sql($MyQuery);
	if($Rs)
	{
		$Etichetta=-1;
		$CurrentTarget=0;
		$NewTarget=true;
		$CurHeight=0;

		$BlockHeight=($pdf->getPageHeight()-30)/$RowsPerPage;
		$CellHeight=$BlockHeight/($ath4target+1);
		$BlocksPerPage=$RowsPerPage*3;

		while($MyRow=safe_fetch($Rs)) {

			if($CurrentTarget != $MyRow->Session . $MyRow->TargetNo)
			{
				$Etichetta = ++$Etichetta % $BlocksPerPage;
				$NewTarget=true;
			}
			$CurrentTarget = $MyRow->Session . $MyRow->TargetNo;

			if($Etichetta==0 && $NewTarget)
				$pdf->AddPage();

			if($NewTarget)
			{
				$aths=$ath4target;

				$CurHeight = $CellHeight;

				$pdf->SetXY((($Etichetta % 3) * 70)+5,(intval($Etichetta / 3) * ($BlockHeight+5) +5));
				$pdf->SetFont($pdf->FontStd,'B',25);
				$tgt=(int)$MyRow->TargetNo;
				if($MyRow->ToElabTeam) {
					$Indices=array('bis','ter','quat', 'quin', 'sex', 'sept', 'oct');
					if($MyRow->TargetNo > $MyRow->ToNumEnds) {
						$tgt = ((($tgt-1)%$MyRow->ToNumEnds)+1) . '-' . $Indices[floor($MyRow->TargetNo/$MyRow->ToNumEnds)-1];
					}
				}
				$pdf->Cell(60,$CurHeight, $tgt,1,0,'C',1);


				$pdf->SetFont($pdf->FontStd,'B',20);
				//for($i=1; $i<=$MyRow->{"ToAth4Target" . $MyRow->Session}; $i++)
				for($i=1; $i<=$aths; $i++) {
					$pdf->SetXY((($Etichetta % 3) * 70)+5,(intval($Etichetta / 3) * ($BlockHeight+5)+5)+($i*$CurHeight));
					$pdf->Cell(20, $CurHeight, chr(ord("A")-1+$i), '1', 0, 'C', 0, '', 1, '', '', 'T' );
					$pdf->Cell(40, $CurHeight, '', '1', 0, 'C');
				}
				$NewTarget=false;
			}
			$pdf->SetXY((($Etichetta % 3) * 70)+5,(intval($Etichetta / 3) * ($BlockHeight+5)+5)+((ord($MyRow->BackNo)-ord("A")+1)*$CurHeight)+0.6*$CurHeight);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell(20,0.4*$CurHeight,$MyRow->FirstName,0,0,'C',0);
		}
		safe_free_result($Rs);
	}
	$pdf->Output();
}
