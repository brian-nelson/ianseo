<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
include_once('Common/pdf/ResultPDF.inc.php');
checkACL(AclQualification, AclReadOnly);

$pdf = new ResultPDF(get_text('FopSetup'),false);

define("ColName",22);
define("RowH",18);

$terne=array(
	array(0,255,0),
	array(255,153,255),
	array(255,255,204),
	array(153,153,255),
	array(255,153,0),
	array(204,255,204),
	//array(102,0,51),
	array(51,204,204),
);

$ColorArray=$terne;
$ColorArray[] = array(198,198,198);

foreach($terne as $col) {
	$ColorArray[] = array($col[1],$col[2],$col[0]);
}
foreach($terne as $col) {
	$ColorArray[] = array($col[2],$col[0],$col[1]);
}

$ColorAssignment = array();

$FirstTarget = 1;
$LastTarget = 99;
$NumDistances = 1;

$MyQuery = "SELECT MIN(SUBSTRING(AtTargetNo,2,".TargetNoPadding.")*1) AS A, MAX(SUBSTRING(AtTargetNo,2,".TargetNoPadding.")*1) AS B, MAX(ToNumDist) as NumDistanze"
	. " FROM Tournament"
	. " INNER JOIN AvailableTarget On ToId = AtTournament"
	. " WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
//echo $MyQuery;exit();
$Rs = safe_r_sql($MyQuery);

if(safe_num_rows($Rs)==1)
{
	$r=safe_fetch($Rs);
	$FirstTarget = $r->A;
	$LastTarget = $r->B;
	$NumDistances = $r->NumDistanze;
	//safe_free_result($Rs);
}
$DimTarget = ($pdf->GetPageWidth()-42)/($LastTarget-$FirstTarget+1);

$MyQuery = "";
for($i=1; $i<=$NumDistances; $i++ )
{
	if(strlen($MyQuery)!= 0)
		$MyQuery .= " UNION ";
	$MyQuery .= "(SELECT DISTINCT QuSession as Session, SUBSTRING(QuTargetNo,2,".TargetNoPadding.") as Target, "
		. " IFNULL(Td" . $i . ",'." . $i . ".') as Dist, IFNULL(Td1,'.1.') as Main, " . $i . " as CheDist"
		. " FROM Tournament AS t"
		. " INNER JOIN Entries AS e ON t.ToId=e.EnTournament"
		. " INNER JOIN Qualifications AS q ON e.EnId=q.QuId"
		. " LEFT JOIN TournamentDistances AS td ON t.ToType=td.TdType and TdTournament=ToId AND CONCAT(TRIM(e.EnDivision),TRIM(e.EnClass)) LIKE TdClasses"
		. " WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " AND QuSession > 0 AND QuTargetNo <> '' )";
}
$MyQuery .= " ORDER BY Session, Dist+0 DESC, CheDist ASC, Target, Main DESC";

$Rs = safe_r_sql($MyQuery);
if(safe_num_rows($Rs)>0)
{
	$OldSession = '';
	$OldDist = '';
	$OldTarget = '';
	$TmpColor=array(255,255,255);
	$SecondaryDistance=0;
	$TgText='';
	$TgFirst=0;
	$TgNo=0;
	$TgTop=0;
	$OldTrueSession='';

	$TopPos=35-RowH;
	$MyRow=safe_fetch($Rs);
	while($MyRow) {
//Cambio di Sessione e/o Distanza
		if($MyRow->Dist=='-') {
			$MyRow=safe_fetch($Rs);
			continue;
		}
		if($OldSession != $MyRow->Session.$MyRow->Dist)
		{
			if(!$pdf->SamePage(RowH+5) or ($OldTrueSession!=$MyRow->Session and !$pdf->SamePage(2*RowH+15))) {
				$pdf->AddPage();	//Al cambio di data aggiungo una pagina
				$TopPos = 35;
			} else {
				$TopPos += RowH;
			}
			$pdf->SetXY(10, $TopPos+3);
			$pdf->SetFont($pdf->FontStd,'B',14);
			$pdf->Cell(ColName,8,$MyRow->Dist,0,0,"C");
			$pdf->SetXY(10, $TopPos+9);
			$pdf->SetFont($pdf->FontStd,'I',8);
			$pdf->Cell(ColName,5, get_text('Session') . ": " . $MyRow->Session,0,0,"C");

			$pdf->SetTextColor(0);

			$pdf->SetXY(10+ColName, $TopPos+2);
			$pdf->SetFont($pdf->FontFix,'B',7);
			$pdf->SetFillColor(240);
			for($i=$FirstTarget; $i<=$LastTarget; $i++)
				$pdf->Cell($DimTarget,4,$i,'LRB',0,"C",1);

			$OldSession = $MyRow->Session.$MyRow->Dist;
			$OldDist = '';
			$OldTarget = '';
		}

		$TgTop=$TopPos+6;

		// devo calcolare fin dove si estende l'evento corrente
		$OldEvent = $MyRow->Session . $MyRow->Main;
		$TgNo=0;
		$actTarget = $MyRow->Target;
		if(!in_array($MyRow->Session . $MyRow->Main, $ColorAssignment))
			$ColorAssignment[] = $MyRow->Session . $MyRow->Main;
		$TmpColor=$ColorArray[array_search($MyRow->Session.$MyRow->Main, $ColorAssignment)];
		$TgText = $MyRow->Dist;
		$SecondaryDistance = $MyRow->CheDist;
		$TgFirst = $MyRow->Target;
		while($MyRow and $OldEvent == $MyRow->Session . $MyRow->Main and $MyRow->Target==$actTarget and $MyRow->CheDist==$SecondaryDistance) {
			if($MyRow=safe_fetch($Rs)) {
				if($actTarget!=$MyRow->Target or $MyRow->Dist!=$TgText) {
					$actTarget++;
					$TgNo++;
				}
			} else {
				$TgNo++;
			}
		}
		if(!$TgNo) $TgNo=1;

		$pdf->SetFillColor(128);
		if($SecondaryDistance == 1)
		{
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->Rect(10+ColName+($TgFirst-1)*$DimTarget, $TgTop, $DimTarget*$TgNo, 10,'DF');
			$pdf->SetFillColor($TmpColor[0],$TmpColor[1],$TmpColor[2]);
			$pdf->SetXY(10+ColName+($TgFirst-1)*$DimTarget+1, $TgTop+1);
			$pdf->Cell($DimTarget*$TgNo-2, 8, $SecondaryDistance,1,0,"C",1);
		}
		else
		{
			$pdf->SetFont($pdf->FontStd,'BI',8);
			$pdf->SetFillColor($TmpColor[0],$TmpColor[1],$TmpColor[2]);
			for($i=0; $i<$TgNo; $i++)
			{
				$pdf->Rect(10+ColName+($TgFirst+$i-1)*$DimTarget, $TgTop, $DimTarget, 6,'D');
				$pdf->Circle(10+ColName+($TgFirst+$i-0.5)*$DimTarget,$TgTop+3,1.5,0,360,'DF');
			}
			$pdf->SetXY(10+ColName+($TgFirst-1)*$DimTarget, $TgTop+6);
			$pdf->SetFillColor(255);
			$pdf->Cell($DimTarget*$TgNo, 4, $SecondaryDistance,1,0,"C",1);
		}
	}
}


$pdf->Output();

?>