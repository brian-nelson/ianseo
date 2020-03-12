<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/pdf/ResultPDF.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Lib/Fun_PrintOuts.php');
	require_once('Common/Lib/Obj_RankFactory.php');
	checkACL(AclIndividuals, AclReadOnly);

	$pdf = new ResultPDF((get_text('IndFinal')),false);
	$pdf->setBarcodeHeader(70);

	$Score3D = false;
	//$MyQuery = "SELECT (TtElabTeam=2) as is3D FROM Tournament INNER JOIN Tournament*Type AS tt ON ToType=TtId WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
	$MyQuery = "SELECT (ToElabTeam=2) as is3D FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)==1) {
		$r=safe_fetch($Rs);
		$Score3D=$r->is3D;
	}

	if (!$Score3D)
		define("CellH",9);
	else
		define("CellH",6);

	error_reporting(E_ALL);

	$FillWithArrows=false;
	if((isset($_REQUEST["ScoreFilled"]) && $_REQUEST["ScoreFilled"]==1))
		$FillWithArrows=true;

	$pdf->PrintFlags=(!empty($_REQUEST["ScoreFlags"]));

	$MyQuery="";
	if (isset($_REQUEST['Blank']))
	{
		$MyQuery = "SELECT DISTINCT
		 	'' AS Event, '' as CountryName, '' as Athlete, '' as CountryCode, '' as EventDescr, '' as Target, '' as Position, -1 as Phase, EvMatchMode as EvMatchMode, '' as ArrowString, '' as Tie,
		 	'' as OppAthlete, '' as OppCountryCode, '' as OppCountryName, '' as OppTarget, '' as OppPosition, '' as OppArrowString, '' as OppTie, '' as QualRank, '' as OppQualRank
			FROM Events
			WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0";
	}
	else
	{

		$options=array('dist'=>0);
		$family='GridInd';
		$options['tournament']=$_SESSION['TourId'];

		if (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']!=-1) {
			$options['schedule']=substr($_REQUEST['x_Session'], 1);
			$OrderBy=true;
		} else {
			$OrderBy=false;
			$Events=array();
			if (!empty($_REQUEST['Event'])) {
				if(is_array($_REQUEST['Event'])) {
					foreach($_REQUEST['Event'] as $Ev) {
						if(isset($_REQUEST['Phase'])) {
							if(is_array($_REQUEST['Phase'])) {
								foreach($_REQUEST['Phase'] as $Ph) {
									$Ph=intval($Ph);
									if ($Ph==24) {
										$Ph=32;
									} elseif ($Ph==48) {
										$Ph=64;
									}
									$Events[]="$Ev@".$Ph;
								}
							} else {
								$Ph=intval($_REQUEST['Phase']);
								if ($Ph==24) {
									$Ph=32;
								} elseif ($Ph==48) {
									$Ph=64;
								}
								$Events[]="$Ev@".$Ph;
							}
						}
					}
				} else {
					$Ev=$_REQUEST['Event'];
					if(isset($_REQUEST['Phase'])) {
						if(is_array($_REQUEST['Phase'])) {
							foreach($_REQUEST['Phase'] as $Ph) {
								$Ph=intval($Ph);
								if ($Ph==24) {
									$Ph=32;
								} elseif ($Ph==48) {
									$Ph=64;
								}
								$Events[]="$Ev@".$Ph;
							}
						} else {
							$Ph=intval($_REQUEST['Phase']);
							if ($Ph==24) {
								$Ph=32;
							} elseif ($Ph==48) {
								$Ph=64;
							}
							$Events[]="$Ev@".$Ph;
						}
					}
				}
			} elseif (isset($_REQUEST['Phase']) && preg_match("/^[0-9]{1,2}$/i",$_REQUEST["Phase"])) {
				$Events='@'.intval($_REQUEST['Phase']);
			}
			if($Events) $options['events']=$Events;
		}
		$rank=Obj_RankFactory::create($family,$options);

		$MyQuery = $rank->getQuery($OrderBy);
	 }
	$Rs=safe_r_sql($MyQuery);
// Se il Recordset è valido e contiene almeno una riga
	if (safe_num_rows($Rs)>0) {
		$defGoldW  = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(1/15);
		$defTotalW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(3/15);
		$defArrowTotW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(6/15);

		$WhereStartX=array($pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2);
		$WhereStartY=array(60,60);
		$WhereX=NULL;
		$WhereY=NULL;
		$AtlheteName=NULL;
		$FollowingRows=false;

//DrawScore
		while($MyRow=safe_fetch($Rs)) {
			set_time_limit(30);
			if(empty($_REQUEST["Blank"]) &&  empty($_REQUEST["IncEmpty"]) && (empty($MyRow->EnId) || empty($MyRow->OppEnId))) {
				// se è vuoto uno dei due arcieri e non è selezionata l'inclusione
				// salta al prossimo record
				continue;
			}

			// disegna lo score di sinistra
			DrawScore($pdf, $MyRow, 'L');

			// Disegna lo score di destra
			DrawScore($pdf, $MyRow, 'R');

			// print barcode if any
			if(!empty($_REQUEST['Barcode'])) {
				$pdf->setxy($pdf->BarcodeHeaderX, 5);
				$pdf->SetFont('barcode','',25);
				$pdf->Cell($pdf->BarcodeHeader, 15, '*' . mb_convert_encoding($MyRow->MatchNo.'-0-'.$MyRow->Event, "UTF-8","cp1252") . "*",0,1,'C',0);
				$pdf->SetFont($pdf->FontStd,'',10);
				$pdf->setxy($pdf->BarcodeHeaderX, 16);
				$pdf->Cell($pdf->BarcodeHeader, 4, mb_convert_encoding($MyRow->MatchNo.'-0-'.$MyRow->Event, "UTF-8","cp1252"),0,1,'C',0);
			} else {
				$pdf->setBarcodeHeader(10);
			}

			if(!empty($_REQUEST['QRCode'])) {
				foreach($_REQUEST['QRCode'] as $k => $Api) {
					require_once('Api/'.$Api.'/DrawQRCode.php');
					$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
					$Function($pdf, $pdf->BarcodeHeaderX -(25 * ($k+1)), 5, $MyRow->Event, $MyRow->MatchNo, $MyRow->Phase, 0, "MI");
				}
			}
		}

//END OF DrawScore
		safe_free_result($Rs);
	}

$pdf->Output();

function DrawScore(&$pdf, $MyRow, $Side='L') {
	global $CFG, $defTotalW, $defGoldW, $defArrowTotW, $FollowingRows, $TrgOutdoor, $WhereStartX, $WhereStartY, $Score3D, $FillWithArrows;
	if(isset($_REQUEST['Blank'])) {
		$tmp=new stdClass();
		$tmp->ends=empty($_REQUEST['Rows'])?5:intval($_REQUEST['Rows']);
		$tmp->arrows = empty($_REQUEST['Cols'])?3:intval($_REQUEST['Cols']);
		$tmp->so = empty($_REQUEST['SO'])?1:intval($_REQUEST['SO']);
	} else {
		$tmp=getEventArrowsParams($MyRow->Event, $MyRow->Phase, 0);
	}
	$NumRow=$tmp->ends;
	$NumCol=$tmp->arrows;
	$ArrowW = $defArrowTotW/$NumCol;
	$TotalW=$defTotalW;
	$GoldW=$defGoldW;

	$Prefix='Opp';
	$Opponent='';
	$ScorePrefix='';
	if($MyRow->EvMatchMode) {
		$ScorePrefix='Set';
	}

//		echo $MyRow->EvMatchArrowsNo . "." . $MyRow->GrPhase ."." . ($MyRow->EvMatchArrowsNo & ($MyRow->GrPhase>0 ? $MyRow->GrPhase*2:1)) . "/" . $NumRow . "--<br>";
	if($Side=='L') {
		if($FollowingRows) $pdf->AddPage();
		$Prefix='';
		$Opponent='Opp';
	}

	$FollowingRows=true;
	$WhichScore=($Side=='R');
	$WhereX=$WhereStartX;
	$WhereY=$WhereStartY;
//Intestazione Atleta
	$pdf->SetLeftMargin($WhereStartX[$WhichScore]);
	$pdf->SetY(35);
// Flag of Country/Club
	if($pdf->PrintFlags) {
		if(is_file($file= $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$MyRow->{$Prefix.'CountryCode'}.'.jpg')) {
			$H=12;
			$W=18;
			$OrgY=$pdf->gety();
			$OrgX=$NumCol*$ArrowW+$TotalW+$GoldW+$TotalW-18;
			$pdf->Image($file, $pdf->getx()+$OrgX, $OrgY, $W, $H, 'JPG', '', '', true, 300, '', false, false, 1, true);
			$FlagOffset=$W+1;
		}
	}

	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,6,(get_text('Athlete')) . ': ', 'TL', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($NumCol*$ArrowW+2*$TotalW+$GoldW-20-($pdf->PrintFlags?18:0),6,($MyRow->{$Prefix.'Athlete'}), 'T', 1, 'L', 0);
   	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,6,(get_text('Country')) . ': ', 'L', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($NumCol*$ArrowW+2*$TotalW+$GoldW-20,6, ($MyRow->{$Prefix.'CountryName'} . (strlen($MyRow->{$Prefix.'CountryCode'})>0 ?  ' (' . $MyRow->{$Prefix.'CountryCode'}  . ')' : '')), 0, 1, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,6,(get_text('DivisionClass')) . ': ', 'LB', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($NumCol*$ArrowW+$TotalW+$GoldW-20,6, get_text($MyRow->EventDescr,'','',true), 'B', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($TotalW,6, (get_text('Target')) . ' ' . ltrim($MyRow->{$Prefix.'Target'},'0'), '1', 1, 'C', 1);


	$pdf->SetXY($NumCol*$ArrowW+2*$TotalW+$GoldW+$WhereStartX[$WhichScore],35);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(2*$GoldW,6, (get_text('Rank')),'TLR',1,'C',1);
	$pdf->SetXY($NumCol*$ArrowW+2*$TotalW+$GoldW+$WhereStartX[$WhichScore],$pdf->GetY());
	$pdf->SetFont($pdf->FontStd,'B',25);
	$pdf->Cell(2*$GoldW,12, $MyRow->{$Prefix.'QualRank'},'BLR',1,'C',1);

//Header
	$PhaseName='';
	if($MyRow->{'Phase'}>=0) {
		$PhaseName=get_text(namePhase($MyRow->EvFinalFirstPhase, $MyRow->Phase). '_Phase');
		if($MyRow->EvElimType==3 and isset($pdf->PoolMatches[$MyRow->MatchNo])) {
			$PhaseName=$pdf->PoolMatches[$MyRow->MatchNo];
		} elseif($MyRow->EvElimType==4 and isset($pdf->PoolMatchesWA[$MyRow->MatchNo])) {
			$PhaseName=$pdf->PoolMatchesWA[$MyRow->MatchNo];
		}
	}
	if(!empty($MyRow->GameNumber)) {
		$PhaseName.=' - '.get_text('GameNumber', 'Tournament', $MyRow->GameNumber);
	}
   	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell($GoldW,CellH,'',0,0,'C',0);
	$pdf->Cell(2*$GoldW+2*$TotalW+$NumCol*$ArrowW,CellH, $PhaseName,1,0,'C',1);
//Winner Checkbox
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell(2*$GoldW,CellH,'',0,0,'C',0);
	//$pdf->Rect($WhereX[$WhichScore]+$GoldW+2,$WhereY[$WhichScore]+2,$GoldW-4,CellH-4,'DF',array(),array(255,255,255));
	$pdf->Rect($WhereX[$WhichScore]+$GoldW+2,$WhereY[$WhichScore]+2,CellH-4,CellH-4,'DF',array(),array(255,255,255));
	if($FillWithArrows && ($MyRow->{$Prefix.$ScorePrefix.'Score'} > $MyRow->{$Opponent.$ScorePrefix.'Score'} || ($MyRow->{$Prefix.$ScorePrefix.'Score'} == $MyRow->{$Opponent.$ScorePrefix.'Score'} && $MyRow->{$Prefix.'Tie'} > $MyRow->{$Opponent.'Tie'} ))) {
		$tmpWidth=$pdf->GetLineWidth();
		$pdf->SetLineWidth($tmpWidth*5);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+1,$WhereY[$WhichScore]+1,$WhereX[$WhichScore]+2*$GoldW-1,$WhereY[$WhichScore]+CellH-1);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+1,$WhereY[$WhichScore]+CellH-1,$WhereX[$WhichScore]+2*$GoldW-1,$WhereY[$WhichScore]+1);
		$pdf->SetLineWidth($tmpWidth);
	}
	$pdf->SetDefaultColor();
	$pdf->Cell($GoldW+2*$TotalW+$NumCol*$ArrowW,CellH, get_text('Winner'),0,1,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY();

	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell($GoldW,CellH,'',0,0,'C',0);
	if($Score3D)
	{
		$pdf->Cell($NumCol*$ArrowW,CellH, get_text('Arrow'), 1, 0, 'C', 1);
	}
	else
	{
		for($j=0; $j<$NumCol; $j++)
			$pdf->Cell($ArrowW,CellH, ($j+1), 1, 0, 'C', 1);
	}
	$pdf->Cell($TotalW * ($MyRow->{'EvMatchMode'}==0 ? 1:4/5),CellH, get_text(($MyRow->{'EvMatchMode'}==0 ? 'TotalProg':'SetTotal'),'Tournament'),1,0,'C',1);
	$pdf->Cell($TotalW * ($MyRow->{'EvMatchMode'}==0 ? 1:4/5),CellH, get_text('RunningTotal','Tournament'),1,0,'C',1);

	if($MyRow->{'EvMatchMode'}==0)
	{
		$pdf->Cell($GoldW,CellH,($pdf->prnGolds),1,0,'C',1);
		$pdf->Cell($GoldW,CellH,($pdf->prnXNine),1,1,'C',1);
	}
	else
	{
		$pdf->Cell(2*$GoldW,CellH,get_text('SetPoints', 'Tournament'),1,0,'C',1);
		$pdf->Cell(2/5*$TotalW,CellH,get_text('TotalShort','Tournament'),1,1,'C',1);
	}
	$WhereY[$WhichScore]=$pdf->GetY();
//Righe
	$ScoreTotal = 0;
	$ScoreGold = 0;
	$ScoreXnine = 0;
	$SetTotal = '';
	for($i=1; $i<=$NumRow; $i++)
	{
		$ScoreEndTotal = 0;
		$ScoreEndGold = 0;
		$ScoreEndXnine = 0;
	   	$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
		$pdf->Cell($GoldW,CellH,$i,1,0,'C',1);
		$pdf->SetFont($pdf->FontStd,'',10);

		if($Score3D)
		{
			$pdf->Cell($ArrowW/5,CellH, '11', 1, 0, 'C', 0);
			$pdf->Cell($ArrowW/5,CellH, '10', 1, 0, 'C', 0);
			$pdf->Cell($ArrowW/5,CellH, '8', 1, 0, 'C', 0);
			$pdf->Cell($ArrowW/5,CellH, '5', 1, 0, 'C', 0);
			$pdf->Cell($ArrowW/5,CellH, 'M', 1, 0, 'C', 0);
		}
		else
		{
			for($j=0; $j<$NumCol; $j++)
				$pdf->Cell($ArrowW,CellH,($FillWithArrows ? DecodeFromLetter(substr($MyRow->{$Prefix.'ArrowString'},($i-1)*$NumCol+$j,1)) : ''),1,0,'C',0);

			$IsEndScore= trim(substr($MyRow->{$Prefix.'ArrowString'}, ($i-1)*$NumCol, $NumCol));
			list($ScoreEndTotal,$ScoreEndGold, $ScoreEndXnine) = ValutaArrowStringGX(substr($MyRow->{$Prefix.'ArrowString'},($i-1)*$NumCol,$NumCol),$pdf->goldsChars,$pdf->xNineChars);
			$ScoreTotal += $ScoreEndTotal;
			$ScoreGold += $ScoreEndGold;
			$ScoreXnine += $ScoreEndXnine;
		}
		$pdf->SetFont($pdf->FontStd,'', ($MyRow->{'EvMatchMode'}==0 ? 10 : 12));
		$pdf->Cell($TotalW * ($MyRow->{'EvMatchMode'}==0 ? 1:4/5),CellH,($FillWithArrows && $IsEndScore ? $ScoreEndTotal : ''),1,0,'C',0);
		$pdf->SetFont($pdf->FontStd,'', ($MyRow->{'EvMatchMode'}==0 ? 12 : 10));
		$pdf->Cell($TotalW* ($MyRow->{'EvMatchMode'}==0 ? 1:4/5),CellH,($FillWithArrows && $IsEndScore ? $ScoreTotal : ''),1,0,'C',0);
		if($MyRow->{'EvMatchMode'}==0)
		{
			$pdf->SetFont($pdf->FontStd,'',9);
			$pdf->Cell($GoldW,CellH,($FillWithArrows && $IsEndScore ? $ScoreEndGold : ''),1,0,'C',0);
			$pdf->Cell($GoldW,CellH,($FillWithArrows && $IsEndScore ? $ScoreEndXnine : ''),1,1,'C',0);
		}
		else
		{
			$SetTotSx = '';
			if($IsEndScore && $FillWithArrows) {
				$SetPointSx= ValutaArrowString(substr($MyRow->{$Prefix.'ArrowString'}, ($i-1)*$NumCol, $NumCol));
				$SetPointDx= ValutaArrowString(substr($MyRow->{$Opponent.'ArrowString'}, ($i-1)*$NumCol, $NumCol));

				if($SetPointSx > $SetPointDx) {
					$SetTotSx= 2;
				} elseif($SetPointSx < $SetPointDx) {
					$SetTotSx= 0;
				} else {
					$SetTotSx= 1;
				}
				$SetTotal = intval($SetTotal) + $SetTotSx;
			}

			$pdf->SetFont($pdf->FontStd,'B',11);
			if($SetTotSx==2 && $FillWithArrows)
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+CellH/2, $GoldW/3, 0, 360, 'FD');
			$pdf->Cell((2*$GoldW)/3,CellH,'2',1, 0,'C',0);
			if($SetTotSx==1 && $FillWithArrows)
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+CellH/2, $GoldW/3, 0, 360, 'FD');
			$pdf->Cell((2*$GoldW)/3,CellH,'1',1, 0,'C',0);
			if($SetTotSx==0 && $IsEndScore && $FillWithArrows)
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+CellH/2, $GoldW/3, 0, 360, 'FD');
			$pdf->Cell((2*$GoldW)/3,CellH,'0',1, 0,'C',0);
			$pdf->Cell($TotalW * 2/5,CellH,($IsEndScore && $FillWithArrows ? $SetTotal : ''),1, 1,'C',0);


		}
		$WhereY[$WhichScore]=$pdf->GetY();
	}

//Shoot Off
	$closeToCenter=false;
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]+(CellH/4));
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell($GoldW,CellH*($MyRow->EvElimType ? 11 : 23)/8,(get_text('TB')),1,0,'C',1);
	$ShootOffW=($tmp->so<=$NumCol ? $ArrowW : ($ArrowW*$NumCol)/$tmp->so);
	$StartX=$pdf->getX();
	for($i=0; $i<($MyRow->EvElimType ? 1 : 3); $i++) {

		$pdf->SetX($StartX);
		for($j=0; $j<$tmp->so; $j++) {
			$pdf->SetXY($pdf->GetX()+0.5,$pdf->GetY());
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell($ShootOffW-0.5,CellH*3/4,($FillWithArrows ? DecodeFromLetter(substr($MyRow->{$Prefix.'TieBreak'}, $i*$tmp->so + $j ,1)) : ''),1,0,'C',0);
			if(substr(($FillWithArrows ? DecodeFromLetter(substr($MyRow->{$Prefix.'TieBreak'},$i*$tmp->so + $j,1)) : ''),-1,1)=="*") {
				$closeToCenter=true;
			}
			$pdf->ln();
		}
	}
	if($MyRow->{$Prefix.'Tie'}==1) $SetTotal++;
	//if($NumCol>$j) {
	//	$pdf->Cell($ArrowW*($NumCol-$j),CellH*3/4,'',0,0,'L',0);
	//}

//Totale
	$Errore=($FillWithArrows and (strlen($MyRow->{$Prefix.'ArrowString'}) and ($MyRow->{'EvMatchMode'} ? $MyRow->{$Prefix.'SetScore'}!=$SetTotal : $MyRow->{$Prefix.$ScorePrefix.'Score'}!=$ScoreTotal)));
	$pdf->SetXY($TopX=$StartX+$ArrowW*$NumCol,$WhereY[$WhichScore]);
	$pdf->SetFont($pdf->FontStd,'B',10);
	if($MyRow->{'EvMatchMode'}==0)
	{
		$pdf->Cell($TotalW,CellH,get_text('Total'),0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',11);
		$pdf->Cell($TotalW,CellH,($FillWithArrows ? $ScoreTotal : ''),1,0,'C',0);
		if($Errore) {
			$pdf->Line($x1 = $pdf->getx() - $TotalW, $y1=$pdf->gety()+CellH, $x1+$TotalW, $y1-CellH);
		}
		$pdf->SetFont($pdf->FontStd,'',10);
		$pdf->Cell($GoldW,CellH,($FillWithArrows ? $ScoreGold : ''),1,0,'C',0);
		$pdf->Cell($GoldW,CellH,($FillWithArrows ? $ScoreXnine : ''),1,1,'C',0);
	}
	else
	{
		$pdf->Cell($TotalW * 8/5,CellH,'',0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->Cell(2*$GoldW,CellH,get_text('Total'),0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',14);
		$pdf->Cell(2/5*$TotalW,CellH,($FillWithArrows ? $MyRow->{$Prefix.'SetScore'} : ''),1,1,'C',0);
		if($Errore) {
			$pdf->Line($x1 = $pdf->getx() - 2/5*$TotalW, $y1=$pdf->gety()+CellH, $x1 + 2/5*$TotalW, $y1-CellH);
		}
	}

	if($Errore) {
		$pdf->SetX($TopX);
		$pdf->SetFont($pdf->FontStd,'B',11);
		$pdf->Cell($MyRow->{'EvMatchMode'} ? 2*$GoldW + $TotalW * 8/5 : $TotalW, CellH, (get_text('SignedTotal', 'Tournament') . " "), 0,0,'R',0);
		$pdf->Cell($MyRow->{'EvMatchMode'} ? 2/5*$TotalW : $TotalW, CellH, $MyRow->{$Prefix.$ScorePrefix.'Score'}, 1, 0, 'C', 0);
		$pdf->ln();
	}

	$WhereY[$WhichScore]=$pdf->GetY();
//Closet to the center
	$pdf->SetFont($pdf->FontStd,'',9);
	$pdf->SetXY($WhereX[$WhichScore]+$GoldW+$ShootOffW/2, $WhereY[$WhichScore]+CellH*($MyRow->EvElimType ? 1 : 13)/8);
	$pdf->Cell($ShootOffW/2,CellH/2,'',1,0,'R',0);
	if($closeToCenter)
	{
		$tmpWidth=$pdf->GetLineWidth();
		$pdf->SetLineWidth($tmpWidth*5);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+$ShootOffW/2-1,$WhereY[$WhichScore]+CellH/8-1,$WhereX[$WhichScore]+$GoldW+$ShootOffW+1,$WhereY[$WhichScore]+CellH*5/8+1);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+$ShootOffW/2-1,$WhereY[$WhichScore]+CellH*5/8+1,$WhereX[$WhichScore]+$GoldW+$ShootOffW+1,$WhereY[$WhichScore]+CellH/8-1);
		$pdf->SetLineWidth($tmpWidth);
	}
	$pdf->Cell($ArrowW*($NumCol-1),CellH*2/4,get_text('Close2Center','Tournament'),0,0,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY()+10;
//Firme
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
   	$pdf->SetFont($pdf->FontStd,'I',7);
	$pdf->Cell(3*$GoldW+2*$TotalW+$NumCol*$ArrowW,4,(get_text('Archer')),'B',0,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY()+10;
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell(3*$GoldW+2*$TotalW+$NumCol*$ArrowW,4,(get_text('Scorer')),'B',0,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY()+15;
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell(3*$GoldW+2*$TotalW+$NumCol*$ArrowW,4,(get_text('JudgeNotes')),'B',0,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY();
}
?>
