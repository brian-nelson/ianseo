<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
include_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Scheduler.php');

checkACL(AclCompetition, AclReadOnly);

if(!$FopLocations=Get_Tournament_Option('FopLocations')) {
	$FopLocations=array();
	Set_Tournament_Option('FopLocations', $FopLocations);
}

// defines the days
$DaysToPrint=array();
if(!empty($_REQUEST['Day'])) {
	foreach($_REQUEST['Day'] as $k => $v) {
		$DaysToPrint[]=date('Y-m-d', $_SESSION['ToWhenFromUTS'] + $k*86400);
	}
} else {
	foreach(range(0,  intval(($_SESSION['ToWhenToUTS']-$_SESSION['ToWhenFromUTS'])/86400)) as $n) {
		$DaysToPrint[]=date('Y-m-d', $_SESSION['ToWhenFromUTS'] + $n*86400);
	}
}


$pdf=NULL;

// defines the Locations (these will be printed on a single page)
$LocationsToPrint=array();
if(empty($_REQUEST['Print'])) {
	if(!$FopLocations) {
		// prints everything in a single location
		$tmp=new stdClass();
		$tmp->Loc='';
		$tmp->Tg1=1;
		$tmp->Tg2=9999;
		$LocationsToPrint[]=$tmp;
	} else {
		$LocationsToPrint=$FopLocations;
	}
} else {
	foreach($_REQUEST['Print'] as $k=>$v) {
		$LocationsToPrint[]=$FopLocations[$k];
	}
}

$Scheduler=new Scheduler();
$Scheduler->SplitLocations=true;
$Scheduler->DaysToPrint=$DaysToPrint;
$Scheduler->LocationsToPrint=$LocationsToPrint;

$pdf=$Scheduler->FOP();
$pdf->Output();

die();

define("ColName",18);
define("RowH",17);

$terne=array(
	array(0,255,0),
	array(255,153,255),
	array(255,255,204),
	array(153,153,255),
	array(255,153,0),
	array(204,255,204),
// 	array(102,0,51),
	array(51,204,204),
);

$ColorArray=$terne;

foreach($terne as $col) {
	$ColorArray[] = array($col[1],$col[2],$col[0]);
	if($col[0]!=$col[2]) $ColorArray[] = array($col[1],$col[0],$col[2]);
}
foreach($terne as $col) {
	$ColorArray[] = array($col[2],$col[0],$col[1]);
	if($col[0]!=$col[1]) $ColorArray[] = array($col[2],$col[1],$col[0]);
}
foreach($terne as $col) {
	$ColorArray[] = array($col[0],$col[2],$col[1]);
	if($col[2]!=$col[1]) $ColorArray[] = array($col[0],$col[1],$col[2]);
}

$ColorArray[] = array(198,198,198);

if(isset($_REQUEST["BlackAndWhite"])) {
	$ColorArray=array();
	for($i=0; $i<25; $i++) {
		$ColorArray[] = array(250,250,250);
		$ColorArray[] = array(170,170,170);
		$ColorArray[] = array(230,230,230);
		$ColorArray[] = array(190,190,190);
		$ColorArray[] = array(210,210,210);
	}
}

// Setting the font color to black or white based on the perception of darkness of the background
foreach($ColorArray as $k => $color) {
	$txt=($color[0]*0.21)+($color[1]*0.71)+($color[2]*0.08);
	$ColorArray[$k][]=($txt<=85 ? 255 : 0);
}

$ColorAssignment = array();

$First=true;

foreach($LocationsToPrint as $Location) {
	foreach($DaysToPrint as $Day) {
		$FirstTarget=1;
		$LastTarget=999;
		$MyQuery = "(SELECT FSScheduledDate, MIN(FSTarget*1) As A, MAX(FSTarget*1) As B
				FROM FinSchedule
				WHERE FsTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FSScheduledDate='$Day' and (FSTarget*1) between $Location->Tg1 and $Location->Tg2
				group by FSScheduledDate)
						UNION
				(SELECT
				FwDay, min(cast(SUBSTRING_INDEX(SUBSTRING_INDEX(FwTargets, ',', 1), '-', 1) as unsigned)), max(cast(SUBSTRING_INDEX(SUBSTRING_INDEX(FwTargets, ',', -1), '-', -1) as unsigned))
				FROM FinWarmup
				WHERE FwTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND date_format(FwDay, '%Y-%m-%d')='$Day' and FwTargets between $Location->Tg1 and $Location->Tg2
				GROUP by FwDay) order by FSScheduledDate";

		$q=safe_r_sql("select max(EvDistance) MaxDist from Events where EvTournament={$_SESSION['TourId']}");
		$r=safe_fetch($q);
		$MaxDist=$r->MaxDist;

		$DayFirstTargets=array();
		$DayLastTargets=array();
		$Rs = safe_r_sql($MyQuery);
		if(safe_num_rows($Rs)) {
			$FirstTarget=999;
			$LastTarget=1;

			while($r=safe_fetch($Rs)) {
				if(empty($DayFirstTargets[$r->FSScheduledDate])) $DayFirstTargets[$r->FSScheduledDate]=999;
				if(empty($DayLastTargets[$r->FSScheduledDate])) $DayLastTargets[$r->FSScheduledDate]=1;
				if($r->A) $DayFirstTargets[$r->FSScheduledDate]=min($r->A, $DayFirstTargets[$r->FSScheduledDate]);
				if($r->B) $DayLastTargets[$r->FSScheduledDate]=max($r->B, $DayLastTargets[$r->FSScheduledDate]);
			}

			$FirstTarget=min($DayFirstTargets);
			$LastTarget=max($DayLastTargets);
		}

		if(!$FirstTarget AND !$LastTarget) exit();

		$FirstTarget = current($DayFirstTargets);
		$LastTarget = current($DayLastTargets);
		// $FirstTarget=1; // che senso ha???



		$MyQuery = "(SELECT '' as Warmup, FSEvent,"
			. " FSTeamEvent,"
			. " GrPhase,"
			. " FsMatchNo,"
			. " FsTarget,"
			. " '' as TargetTo,"
			. " EvMatchArrowsNo, EvMatchMode, EvMixedTeam, EvTeamEvent, EvWinnerFinalRank, "
			. " UNIX_TIMESTAMP(FSScheduledDate) as SchDate,"
			. " DATE_FORMAT(FSScheduledTime,'" . get_text('TimeFmt') . "') as SchTime, "
			. " EvFinalFirstPhase,"
			. " @bit:=pow(2, ceil(log2(GrPhase))+1) & EvMatchArrowsNo,"
			. " IF(@bit=0,EvFinEnds,EvElimEnds) AS `ends`,"
			. " IF(@bit=0,EvFinArrows,EvElimArrows) AS `arrows`,"
			. " IF(@bit=0,EvFinSO,EvElimSO) AS `so`,"
			. " EvMaxTeamPerson,"
			. " FSScheduledDate,"
			. " FSScheduledTime, EvDistance "
			. " FROM FinSchedule"
			. " INNER JOIN Grids ON FSMatchNo=GrMatchNo"
			. " INNER JOIN Events ON FSEvent=EvCode AND FSTeamEvent=EvTeamEvent AND FSTournament=EvTournament "
			. " INNER JOIN Phases ON PhId=EvFinalFirstPhase and (PhIndTeam & pow(2, EvTeamEvent))>0 "
			. " WHERE FSTournament=" . StrSafe_DB($_SESSION['TourId']) . "
					AND FSScheduledDate='$Day'
					AND (FSTarget*1) between $Location->Tg1 and $Location->Tg2
					AND GrPhase<=greatest(PhId, PhLevel) "
			. ") UNION ("
			. "SELECT '1' as Warmup, '".get_text('WarmUp','Tournament')."',"
			. " '',"
			. " FwEvent ," // GrPhase
			. " '',"
			. " FwTargets, "
			. " FwOptions,"
			. " '--' as EvMatchArrowsNo, 0 as EvMatchMode, 0, 0, 0, "
			. " UNIX_TIMESTAMP(FwDay) as SchDate,"
			. " DATE_FORMAT(FwTime,'" . get_text('TimeFmt') . "') as SchTime,"
			. " 0,"
			. " 0,"
			. " 0 AS `ends`,"
			. " 0 AS `arrows`,"
			. " 0 AS `so`,"
			. " 0,"
			. " FwDay,"
			. " FwTime, EvDistance "
			. " FROM FinWarmup"
			. " INNER JOIN Events ON FwEvent=EvCode AND FwTeamEvent=EvTeamEvent AND FwTournament=EvTournament "
			. " WHERE FwTournament=" . StrSafe_DB($_SESSION['TourId']) . "
					AND date_format(FwDay, '%Y-%m-%d')='$Day' and FwTargets between $Location->Tg1 and $Location->Tg2"
			. " )"
			. " ORDER BY FSScheduledDate ASC, FSScheduledTime ASC, Warmup ASC, FSTarget ASC, FSMatchNo ASC";
		$Rs = safe_r_sql($MyQuery);
		if(safe_num_rows($Rs)>0) {
			if($First) {
				$pdf = new ResultPDF(get_text('FopSetup'), ($LastTarget-$FirstTarget)<21);
				error_reporting(E_ALL);
				$pdf->SetCellPadding(0.25);
			} else {
				$pdf->AddPage(($LastTarget-$FirstTarget)<21 ? 'P' : 'L');
			}

// 			$pdf->setPageOrientation(($LastTarget-$FirstTarget)<21 ? 'P' : 'L');

			$DimTarget = min(10,($pdf->getpagewidth()-20-ColName)/($LastTarget-$FirstTarget+1));

			$pdf->SetFont($pdf->FontStd,'B',16);
			$pdf->Cell(0, 0, ($Location->Loc ? $Location->Loc . ' - ' : '').$Day, 'B', true, 'C');
			$pdf->SetFont($pdf->FontStd,'',8);

			$OldSched = '';
			$OldDate = '';
			$OldEvent = '';
			$OldTarget = '';
			$TmpColor=array(255,255,255);
			$PrintEvent=true;
			$TgText='';
			$TgFirst=0;
			$TgNo=0;
			$TgTop=0;

			$TopPos=35-RowH;
			$MyRow=safe_fetch($Rs);

			$TimeEvents=array();
			$DistGap=0;

			while($MyRow) {
				// check distances!
				$CurDistGap=($MyRow->EvDistance ? 3 : 0)+(($MaxDist - $MyRow->EvDistance)/5);
				$DistGap=max($DistGap, $CurDistGap);

				//Cambio di Orario e/o data
				if($OldSched != $MyRow->SchDate . $MyRow->SchTime) {
					if(!empty($DayLastTargets[$MyRow->FSScheduledDate])) {
						$FirstTarget=$DayFirstTargets[$MyRow->FSScheduledDate];
						$LastTarget=$DayLastTargets[$MyRow->FSScheduledDate];
					}
					if(($OldDate != $MyRow->SchDate && $OldDate != '')
						|| !$pdf->SamePage(RowH + 12 + $DistGap)
						|| (!$pdf->SamePage((2*RowH) + 12 + $DistGap) and $MyRow->Warmup)) {
						if($LastTarget-$FirstTarget<21) {
							$pdf->AddPage('P');	//Al cambio di data aggiungo una pagina
						} else {
							$pdf->AddPage('L');	//Al cambio di data aggiungo una pagina
						}
						$TopPos = 35;
						$DimTarget = min(10,($pdf->getpagewidth()-20-ColName)/($LastTarget-$FirstTarget+1));
					} else {
						if($OldDate) {
							$pdf->SetLineStyle(array('width'=>0.5, 'color' => array(128)));
							$pdf->Line(10, $TopPos + RowH + $DistGap-1, $pdf->getPageWidth()-10, $TopPos + RowH + $DistGap-1);
							$pdf->SetLineStyle(array('width'=>.1, 'color' => array(0)));
						}
						$TopPos += RowH + $DistGap +2;
					}

					$DistGap=max(3, $CurDistGap);;

					$pdf->SetTextColor(0);
					$pdf->SetXY(10, $TopPos - 1);
					$pdf->SetFont($pdf->FontStd,'B',14);
					$pdf->Cell(ColName,3,(isset($_REQUEST["HideTime"]) ? '' : $MyRow->SchTime),0,0,"C");
					// data e ora
					$pdf->SetXY(10, $TopPos+($MyRow->EvMatchArrowsNo=='--'?7:4));
					$pdf->SetFont($pdf->FontStd,'I',8);
					//$pdf->Cell(ColName,5,date( get_text('DateFmt'), $MyRow->SchDate),0,0,"C");
					$pdf->Cell(ColName,5,dateRenderer($MyRow->FSScheduledDate,get_text('DateFmt')),0,0,"C");

					if($MyRow->EvMatchArrowsNo!='--') {
						// numero frecce
						$nARR = $MyRow->arrows . 'x' . $MyRow->ends;				// Numero di frecce
						if($MyRow->EvMaxTeamPerson>1)
							$nARR = "(" . $MyRow->EvMaxTeamPerson.'x'.($MyRow->arrows/$MyRow->EvMaxTeamPerson) . ")x" . $MyRow->ends;
						$pdf->SetXY(10, $TopPos+7);
						$pdf->SetFont($pdf->FontStd,'I',7);
						$pdf->Cell(ColName,5,get_text('Arrows4End','Tournament',$nARR), 0, 0, "C");
					}

					// arcieri per paglione
					$pdf->SetXY(10, $TopPos+10.5);
					$pdf->SetTextColor(127);
					$pdf->Cell(ColName,4,get_text('Ath4Target', 'Tournament'),0,0,"C",0);
					$pdf->SetTextColor(0);

					$pdf->SetXY(10+ColName, $TopPos);
					$pdf->SetFont($pdf->FontFix,'B',6);
					$pdf->SetFillColor(240);
					for($i=$FirstTarget; $i<=$LastTarget; $i++) {
						$pdf->Cell($DimTarget,3,$i,'LRB',0,"C",1);
					}

					$OldSched = $MyRow->SchDate . $MyRow->SchTime;
					$OldDate = $MyRow->SchDate;
					$OldEvent = '';
					$OldTarget = '';
					$PrintEvent=true;
					$TimeEvents=array();
				}

				$TgTop = $TopPos+3;

				if($MyRow->Warmup) {
					// set textcolor to black
					$pdf->SetTextColor(0);
					// scrive evento e fase

					foreach(explode(',', $MyRow->FsTarget) as $range) {
						$tmp=explode('-', $range);
						$tgFrom=$tmp[0];
						$tgTo=(!empty($tmp[1]) ? $tmp[1] : $tmp[0]);

						$celW=$DimTarget*($tgTo+1-$tgFrom);
						$X=10+ColName+($tgFrom-$FirstTarget)*$DimTarget;
						// Distance
						if($MyRow->EvDistance) {
							$pdf->SetFont($pdf->FontStd,'B',7);
							$pdf->SetXY($X, $TgTop);
							$pdf->Cell($celW, $CurDistGap, $MyRow->EvDistance, 'LR', 0, "C");
						}
						// box
						$pdf->SetXY($X, $TgTop + $CurDistGap);
						$pdf->setAlpha(0.5);
						$pdf->SetFillColor(150);
						$pdf->Cell($celW,10.5, '',1,0,"C",1);
						$pdf->setAlpha(1);
						// Events
						$pdf->SetFont($pdf->FontStd,'B',10);
						$pdf->SetXY($X, $TgTop + $CurDistGap);
						$pdf->Cell($celW, 4, array_intersect(explode('-', $MyRow->GrPhase), $TimeEvents) ? get_text('Bye') : $MyRow->FSEvent,0,0,"C");
						// Phase
						$pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->SetXY($X, $TgTop+4 + $CurDistGap);
						$pdf->Cell($celW,6.5, $MyRow->GrPhase,0,0,"C");
					}
					$MyRow=safe_fetch($Rs);
				} else {
					if(!in_array($MyRow->FSEvent, $TimeEvents)) $TimeEvents[]=$MyRow->FSEvent;
					// devo calcolare fin dove si estende l'evento corrente
					$OldEvent = $MyRow->FSEvent . $MyRow->FSTeamEvent . $MyRow->GrPhase;
					$TgNo=1;
					$actTarget = $MyRow->FsTarget;
					if(!in_array($MyRow->FSEvent, $ColorAssignment)) $ColorAssignment[] = $MyRow->FSEvent;
					$TmpColor=$ColorArray[array_search($MyRow->FSEvent, $ColorAssignment)];
					if($MyRow->GrPhase==0) {
						$TgText = $MyRow->FSEvent . "|" . ($MyRow->EvWinnerFinalRank==1 ? get_text('0_Phase') : $MyRow->EvWinnerFinalRank.' vs '.($MyRow->EvWinnerFinalRank+1));
					} elseif($MyRow->GrPhase==1) {
						$TgText = $MyRow->FSEvent . "|" . ($MyRow->EvWinnerFinalRank==1 ? get_text('1_Phase') : ($MyRow->EvWinnerFinalRank+2).' vs '.($MyRow->EvWinnerFinalRank+3));
					} else {
						$TgText = $MyRow->FSEvent . "|" . (get_text(namePhase($MyRow->EvFinalFirstPhase, $MyRow->GrPhase) . '_Phase'));
					}
					$TgFirst = $MyRow->FsTarget;
					$arcTarget=($MyRow->FSTeamEvent ? $MyRow->EvMaxTeamPerson : 1);
					$WU=$MyRow->Warmup;

					$Distance=$MyRow->EvDistance;

					while($MyRow and $OldEvent == $MyRow->FSEvent . $MyRow->FSTeamEvent . $MyRow->GrPhase and $MyRow->FsTarget==$actTarget and $OldSched == $MyRow->SchDate . $MyRow->SchTime) {
						if($MyRow=safe_fetch($Rs)) {
							if($OldEvent == $MyRow->FSEvent . $MyRow->FSTeamEvent . $MyRow->GrPhase) {
								if($actTarget==$MyRow->FsTarget and $WU==$MyRow->Warmup) {
									$arcTarget=-2;
								} elseif($actTarget==$MyRow->FsTarget-1) {
									$actTarget++;
									$TgNo++;
								}
							}
						}
					}

					// Distance
					if($Distance) {
						$pdf->SetTextColor(0);
						$pdf->SetFont($pdf->FontStd,'B',7);
						$pdf->SetXY(10+ColName+($TgFirst-$FirstTarget)*$DimTarget, $TopPos+3);
						$pdf->Cell($DimTarget*$TgNo, $CurDistGap, $Distance, 'LR', 0, "C");
					}

					// scrive il rettangolo colorato di sfondo
					$pdf->SetFillColor($TmpColor[0],$TmpColor[1],$TmpColor[2]);
					$pdf->SetTextColor($TmpColor[3]);
					$pdf->Rect(10+ColName+($TgFirst-$FirstTarget)*$DimTarget, $TopPos+3+$CurDistGap, $DimTarget*$TgNo, 8,"DF");

					// scrive evento e fase scontornato in bianco
					$pdf->SetDrawColor(255);
					$pdf->TextOutline=2;
					$pdf->TextOutlineWidth=1.5;
					$pdf->SetFont($pdf->FontStd,'B',10);
					$pdf->SetXY(10+ColName+($TgFirst-$FirstTarget)*$DimTarget, $TgTop+$CurDistGap);
					$pdf->Cell($DimTarget*$TgNo, 4, substr($TgText,0,strpos($TgText, "|")),"",0,"C",0);
					$pdf->SetFont($pdf->FontStd,'B',8);
					$pdf->SetXY(10+ColName+($TgFirst-$FirstTarget)*$DimTarget, $TgTop+4+$CurDistGap);
					$pdf->Cell($DimTarget*$TgNo,4, substr(strrchr($TgText, "|"), 1),"",0,"C",0);

					// riscrive il tutto ma in nero
					$pdf->SetDrawColor(0);
					$pdf->TextOutline=0;
					$pdf->SetFont($pdf->FontStd,'B',10);
					$pdf->SetXY(10+ColName+($TgFirst-$FirstTarget)*$DimTarget, $TgTop+$CurDistGap);
					$pdf->Cell($DimTarget*$TgNo, 4, substr($TgText,0,strpos($TgText, "|")),"",0,"C",0);
					$pdf->SetFont($pdf->FontStd,'B',8);
					$pdf->SetXY(10+ColName+($TgFirst-$FirstTarget)*$DimTarget, $TgTop+4+$CurDistGap);
					$pdf->Cell($DimTarget*$TgNo,4, substr(strrchr($TgText, "|"), 1),"",0,"C",0);

					$larCell=$DimTarget/5;
					for($n=0; $n<$TgNo; $n++) {
						$colX=10+ColName+($TgFirst+$n-$FirstTarget)*$DimTarget;
						$pdf->SetXY($colX, $TopPos+11+$CurDistGap);
						//$pdf->Cell($DimTarget,2,'',1,0,"C");
						$pdf->SetFillColor(255);
						$pdf->Rect($colX, $TopPos+11+$CurDistGap,$DimTarget,2.5,"DF");
						$pdf->SetFillColor(127);
						if($arcTarget & 1) {
							$pdf->Rect($colX + 2*$larCell, $TopPos+11.5+$CurDistGap,$larCell,1.5,"DF");
						}
						if($arcTarget & 2) {
							$pdf->Rect($colX + 1*$larCell, $TopPos+11.5+$CurDistGap,$larCell,1.5,"DF");
							$pdf->Rect($colX + 3*$larCell, $TopPos+11.5+$CurDistGap,$larCell,1.5,"DF");
						}
					}

					if(false and $OldTarget != $MyRow->FsTarget)
					{
						$TgNo++;
						$pdf->SetXY(10+ColName+($MyRow->FsTarget-$FirstTarget)*$DimTarget, $TopPos+15+$CurDistGap);
						$pdf->Cell($DimTarget,4,'',1,0,"C");
						$pdf->SetFillColor(127);
						if($MyRow->FSTeamEvent)
						{
							$pdf->Rect((10+ColName+($MyRow->FsTarget-$FirstTarget)*$DimTarget)+2*($DimTarget/5), $TopPos+16+$CurDistGap,$DimTarget/5,2,"DF");
							$pdf->Rect((10+ColName+($MyRow->FsTarget-$FirstTarget)*$DimTarget)+3*($DimTarget/5), $TopPos+16+$CurDistGap,$DimTarget/5,2,"DF");
						}
						else
						{
							$pdf->Rect((10+ColName+($MyRow->FsTarget-$FirstTarget)*$DimTarget)+2*($DimTarget/5), $TopPos+16+$CurDistGap,$DimTarget/5,2,"DF");
						}
			//		} else {
						$pdf->SetFillColor(255);
						$pdf->SetDrawColor(255);
						$pdf->Rect((10+ColName+($MyRow->FsTarget-$FirstTarget)*$DimTarget)+2*($DimTarget/5), $TopPos+16+$CurDistGap,$DimTarget/5,2,"DF");
						$pdf->SetFillColor(127);
						$pdf->SetDrawColor(0);
						$pdf->Rect((10+ColName+($MyRow->FsTarget-$FirstTarget)*$DimTarget)+($DimTarget/5), $TopPos+16+$CurDistGap,$DimTarget/5,2,"DF");
						$pdf->Rect((10+ColName+($MyRow->FsTarget-$FirstTarget)*$DimTarget)+3*($DimTarget/5), $TopPos+16+$CurDistGap,$DimTarget/5,2,"DF");
					}
				}
				//$OldTarget = $MyRow->FsTarget;
			}
			$First=false;
		}

	}
}


if(empty($pdf)) {
	$pdf=new ResultPDF(get_text('FopSetup'));
}

$pdf->Output();
