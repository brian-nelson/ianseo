<?php

require_once('Common/pdf/ScorePDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');

/**
 * @param $Session if 0 all sessions
 * @param $FromTgt first target
 * @param $ToTgt last target
 * @param array $Options one or more of the following toggles:<ul>
 *      <li><b>GetArcInfo</b> (bool): adds a line to get DoB and Email from archers</li>
 *      <li><b>noEmpty</b> (bool): do not print the empty scorecards</li>
 *      <li><b>PersonalScore</b> (bool): all selected distances on the same scorecard</li>
 *      <li><b>QRCode</b> (array of APIs): prints the ISK setup QrCodes</li>
 *      <li><b>ScoreBarcode</b> (bool): prints the entry's barcode for results verification</li>
 *      <li><b>ScoreDist</b> (array): distances to print, if no distances prints the first</li>
 *      <li><b>ScoreDraw</b> (string): type of scorecard: <ul>
 *          <li><em>Complete</em>: full standard scorecard</li>
 *          <li><em>CompleteTotals</em>: as <em>Complete</em> with a running grand total column</li>
 *          <li><em>Data</em>: only the data (scorecards have been pre-printed)</li>
 *          <li><em>TargetNo</em>: only the target number (scorecards have been pre-printed)</li>
 *          <li><em>Draw</em>: only the structure, no data</li>
 *          <li><em>FourScoresNFAA</em>: special layout Vegas Style</li>
 *          </ul></li>
 *      <li><b>ScoreFilled</b> (bool): fills in the scorecard with arrow values</li>
 *      <li><b>ScoreFlags</b> (bool): prints the country/club flags</li>
 *      <li><b>ScoreHeader</b> (bool): prints the competition info</li>
 *      <li><b>ScoreLogos</b> (bool): prints the competition images. If QrCode is not empty, no bottom image is printed</li>
 *      <li><b>TourField3D</b> (string): empty (target archery), 'FIELD' or '3D'</li>
 *      </ul>
 * @param $SaveDir Directory to save each scorecard as a file. Ignored if not Personal scorecards.
 * @return ScorePDF
 */
function CreateSessionScorecard($Session, $FromTgt=1, $ToTgt=999, $Options=array(), $SaveDir='', $File='') {
	$Files=array();
	if($Session=='ONLINE') {
		$FromTgt=1;
		$ToTgt=999;
		$Options['ScoreDraw'] = "Complete";
		$Options['ScoreHeader'] = "1";
		$Options['ScoreLogos'] = "1";
		$Options['ScoreFlags'] = "1";
		$Options['ScoreBarcode'] = "1";
		$Options['PersonalScore'] = "1";
		$Options['ScoreFilled'] = "1";
		$Options['ScoreDist'] = range(1,8);
	}
	$DistArray=array();
	if(!empty($Options["ScoreDist"]) and is_array($Options["ScoreDist"])) {
		foreach($Options["ScoreDist"] as $Cards) {
			if(is_numeric($Cards)) {
				$DistArray[]=$Cards;
			}
		}
	} else {
		$DistArray[]=0;
	}

	$ScoreDraw=(empty($Options["ScoreDraw"]) ? '' : $Options["ScoreDraw"]);
	$FillWithArrows = !empty($Options["ScoreFilled"]);

	$pdf = new ScorePDF(true);
	//error_reporting(E_ALL);
	$pdf->BottomImage=empty($Options['QRCode']);
	$pdf->FillWithArrows=$FillWithArrows;
	if(empty($Options["ScoreHeader"])) {
		$pdf->HideHeader();
	}
	if(empty($Options["ScoreLogos"])) {
		$pdf->HideLogo();
	}
	if(empty($Options["ScoreFlags"])) {
		$pdf->HideFlags();
	}
	if(!empty($Options["ScoreBarcode"])) {
		$pdf->PrintBarcode=true;
	}
	if(!empty($Options["GetArcInfo"])) {
		$pdf->GetArcInfo=true;
	}
	switch($ScoreDraw) {
		case 'Data': $pdf->NoDrawing(); break;
		case 'CompleteTotals': $pdf->PrintTotalColumns(); break;
		case 'FourScoresNFAA': $pdf->NoTensOnlyX(); break;
	}

	if(!isset($Options["TourField3D"])) {
		$Options["TourField3D"]=''; // target archery
	}

	$Data=QualificationScorecards($Session, $FromTgt, $ToTgt, $IncludeEmpty=(empty($Options["noEmpty"])), $ScoreDraw, $FillWithArrows, false, $PersonalScore=(!empty($Options['PersonalScore'])), $Options);
	if($SaveDir) {
		if($PersonalScore) {
			if(substr($SaveDir, -1)!='/') {
				$SaveDir.='/';
			}
		} else {
			$SaveDir = '';
		}
	}

	$pdf->LastUpdate=$Data->QuTimestamp;

	if($PersonalScore) {
		// this prints all distances of a single archer defined by distances on a same scorecard
		if (count($DistArray) > 1 or $DistArray[0] != 0) {
			$Data->Ath4Target = count($DistArray);
		}
	}

	$defScoreW = ($pdf->GetPageWidth()-$pdf->getSideMargin()*3)/2;
	$defScoreH = ($pdf->GetPageHeight() - $pdf->getSideMargin()*3 - ($pdf->NoTensOnlyX ? 7 : 0))/2;

	$defScoreX = $pdf->getSideMargin();
	$defScoreX2 = $defScoreX + $pdf->getSideMargin() + $defScoreW;
	$defScoreY = $pdf->getSideMargin();
	$defScoreY2 = $defScoreY + $pdf->getSideMargin() + $defScoreH;

	if(!$Options["TourField3D"]) {
		// target archery
		if($Data->Ath4Target<=2) {
			$defScoreX = $pdf->getSideMargin()*3;
			$defScoreH = ($pdf->GetPageWidth()-$pdf->getSideMargin()*2 - ($pdf->NoTensOnlyX ? 7 : 0));
			$defScoreW = ($pdf->GetPageHeight()-$defScoreX*3)/2;
		} elseif($Data->Ath4Target==3) {
			$defScoreH = ($pdf->GetPageWidth()-$pdf->getSideMargin()*2);
			$defScoreW = ($pdf->GetPageHeight()-$pdf->getSideMargin()*4)/3;
		}

		if(!empty($Options['QRCode'])) {
			$QRCodeX=0;
			$QRCodeY=0;
			switch($Data->Ath4Target) {
                case 1:
				case 2:
				case 3:
					// ATTENTION HERE: Landscape page!
					$defScoreH-=25;
					$quanti=count($Options['QRCode']);
					$QRCodeY=min($pdf->GetPageHeight(),$pdf->GetPageWidth()) - $pdf->getSideMargin() - 25;
					$QRCodeX=(max($pdf->GetPageWidth(), $pdf->GetPageHeight()) + 5 - (25*$quanti))/2;
					break;
				case 4:
					$defScoreH-=6;
					$defScoreY2+=6;
					if(count($Options['QRCode'])>1) {
						$quanti=count($Options['QRCode']);
						$QRCodeX=($pdf->GetPageWidth() + 5 - (25*$quanti))/2;
					}
					break;
			}
		}
	} else {
		// field and 3D all scorecards are portrait, but takes the whole width
		$defScoreW = ($pdf->GetPageWidth()-$pdf->getSideMargin()*2);
		if(!empty($_REQUEST['QRCode'])) {
			$QRCodeY=0;
			$defScoreH-=12;
			$defScoreY2+=12;
			$quanti=count($_REQUEST['QRCode']);
			$QRCodeX=($pdf->GetPageWidth() + 5 - (30*$quanti))/2;
		}
	}

	if($ScoreDraw=='Draw') {
		foreach($Data->Scores as $Target => $Cards) {
			if($Options["TourField3D"]) {
				foreach($Cards as $Value) {
					$Yscore = $defScoreY2;
					switch(substr($Value["tNo"],-1,1)) {
						case 'A':
						case 'C':
						case 'E':
						case 'G':
						case 'I':
						case 'K':
							$pdf->AddPage();
							$Yscore = $defScoreY;
					}

					$pdf->DrawScoreField($defScoreX, $Yscore, $defScoreW, $defScoreH, 0, $Value, $Data->SesTar4Session, $Data->SesFirstTarget);
					if($Yscore == $defScoreY2 and !empty($_REQUEST['QRCode'])) {
						foreach($_REQUEST['QRCode'] as $k => $Api) {
							require_once('Api/'.$Api.'/DrawQRCode.php');
							$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
							$Function($pdf, $QRCodeX + 30*$k, $QRCodeY, $_REQUEST['x_Session'], 0, substr($Value["tNo"],0,-1));
						}
					}
				}
			} else {
				// Target Archery
				switch ($Data->Ath4Target) {
					case 1:
						$pdf->AddPage('P');
						$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, 0, $Cards[0]);
						break;
					case 2:
						$pdf->AddPage('L');
						$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, 0, $Cards[0]);
						$pdf->DrawScoreNew(2 * $defScoreX + $defScoreW, $defScoreY, $defScoreW, $defScoreH, 0, $Cards[1]);
						break;
					case 3:
						$pdf->AddPage('L');
						$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, ($pdf->GetPageHeight() - $pdf->getSideMargin() * 2), 0, $Cards[0]);
						$pdf->DrawScoreNew(2 * $defScoreX + $defScoreW, $defScoreY, $defScoreW, ($pdf->GetPageHeight() - $pdf->getSideMargin() * 2), 0, $Cards[1]);
						$pdf->DrawScoreNew(3 * $defScoreX + 2 * $defScoreW, $defScoreY, $defScoreW, ($pdf->GetPageHeight() - $pdf->getSideMargin() * 2), 0, $Cards[2]);
						break;
					default:
						$pdf->AddPage();
						$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, 0, $Cards[0]);
						$pdf->DrawScoreNew($defScoreX2, $defScoreY, $defScoreW, $defScoreH, 0, $Cards[1]);
						$pdf->DrawScoreNew($defScoreX, $defScoreY2, $defScoreW, $defScoreH, 0, $Cards[2]);
						$pdf->DrawScoreNew($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, 0, $Cards[3]);

				}
				if (!empty($Options['QRCode'])) {
					foreach ($Options['QRCode'] as $k => $Api) {
						require_once('Api/' . $Api . '/DrawQRCode.php');
						$Function = 'DrawQRCode_' . preg_replace('/[^a-z0-9]/sim', '_', $Api);
						$Function($pdf, $QRCodeX + 30 * $k, $QRCodeY);
					}
				}
			}
		}
	} elseif($PersonalScore) {
		// this prints all distances of a single archer defined by distances on a same scorecard
		if(count($DistArray)>1 or $DistArray[0]!=0) {
			$Data->Ath4Target=count($DistArray);
		}

		$SecondScorer=false;

		$Origins=array();
		$Origins[]=array($defScoreX, $defScoreY);

		if($Options["TourField3D"]) {

		} else {
			if($Data->Ath4Target <= 3) {
				$pdf->setPageOrientation('L');
			}

			switch($Data->Ath4Target) {
				case 2:
					$Origins[]=array(2*$defScoreX+$defScoreW, $defScoreY);
					break;
				case 3:
					$Origins[]=array(2*$defScoreX+$defScoreW, $defScoreY);
					$Origins[]=array(3*$defScoreX+2*$defScoreW, $defScoreY);
					break;
				case 4:
					$Origins[]=array($defScoreX2, $defScoreY);
					$Origins[]=array($defScoreX, $defScoreY2);
					$Origins[]=array($defScoreX2, $defScoreY2);
					break;
				case 5:
					$Origins[]=array($defScoreX, $defScoreY);
					$Origins[]=array($defScoreX2, $defScoreY);
					$Origins[]=array($defScoreX, $defScoreY2);
					$Origins[]=array($defScoreX, $defScoreY);
					$Origins[]=array($defScoreX2, $defScoreY);
					break;
				case 6:
					$Origins[]=array($defScoreX, $defScoreY);
					$Origins[]=array($defScoreX2, $defScoreY);
					$Origins[]=array($defScoreX, $defScoreY2);
					$Origins[]=array($defScoreX2, $defScoreY2);
					$Origins[]=array($defScoreX, $defScoreY);
					$Origins[]=array($defScoreX2, $defScoreY);
					break;
				case 7:
					$Origins[]=array($defScoreX, $defScoreY);
					$Origins[]=array($defScoreX2, $defScoreY);
					$Origins[]=array($defScoreX, $defScoreY2);
					$Origins[]=array($defScoreX2, $defScoreY2);
					$Origins[]=array($defScoreX, $defScoreY);
					$Origins[]=array($defScoreX2, $defScoreY);
					$Origins[]=array($defScoreX, $defScoreY2);
					break;
				case 8:
					$Origins[]=array($defScoreX, $defScoreY);
					$Origins[]=array($defScoreX2, $defScoreY);
					$Origins[]=array($defScoreX, $defScoreY2);
					$Origins[]=array($defScoreX2, $defScoreY2);
					$Origins[]=array($defScoreX, $defScoreY);
					$Origins[]=array($defScoreX2, $defScoreY);
					$Origins[]=array($defScoreX, $defScoreY2);
					$Origins[]=array($defScoreX2, $defScoreY2);
					break;
			}

		}
		if(!empty($Options['QRCode'])) {
			$QRCodeX=0;
            $QRCodeX2=0;
			$QRCodeY=0;
			$quanti=count($Options['QRCode']);
			switch($Data->Ath4Target) {
                case 1:
				case 2:
					$QRCodeY=$pdf->GetPageHeight() - $pdf->getSideMargin() - 25;
					$QRCodeX=($defScoreW + 5 - (25*$quanti))/2;
					if($Options["TourField3D"]) {
						//$defScoreH-=5;
						//$defScoreY2-=6;
						$QRCodeY=0;
						$QRCodeX=($pdf->getPageWidth()+5-(30*$quanti))/2;
					}
					if(!$FillWithArrows) {
						$QRCodeX=5;
						$SecondScorer=true;
					}
					break;
				case 3:
					$defScoreH-=5;
					$quanti=count($Options['QRCode']);
					$QRCodeY=$pdf->GetPageHeight() - $pdf->getSideMargin() - 25;
					$QRCodeX=($defScoreW + 5 - (25*$quanti))/2;
					if(!$FillWithArrows) {
						$QRCodeX=5;
						$SecondScorer=true;
					}
					break;
				case 4:
					$defScoreH-=6;
					$defScoreY2+=6;
                    $Origins[2][1]+=1;
                    $Origins[3][1]+=1;
                    $QRCodeX +=5;
					$QRCodeX2 = $QRCodeX + ($defScoreW/2);
					if(count($Options['QRCode'])>1) {
						$quanti=count($Options['QRCode']);
						$QRCodeX = ($defScoreW + 5 - (25*$quanti))/2;
                        $QRCodeX2 = $QRCodeX + ($defScoreW + 5 - (25*$quanti))/4;
					}
					break;
			}
		} elseif($Data->Ath4Target<=3 and !$FillWithArrows) {
			// we still need to have the 2 scorers to sign!
			$defScoreH-=25;
			$SecondScorer=true;
		}

		$FileName='';
		if($SaveDir) {
			$OrgPdf=clone $pdf;
		}
		foreach($Data->Scores as $Target => $Cards) {
			set_time_limit(120);
			foreach($Cards as $Card) {
				$Card['SecondScorer']=$SecondScorer;
				if($SaveDir) {
					if($FileName) {
						// a scorecard is ready
						$pdf->Output($SaveDir.$FileName, 'F');
						$Files[]=$FileName;
						$pdf=clone $OrgPdf;
						if($File) {
							file_put_contents($SaveDir.$File, $FileName);
						}
					}
	                $FileName = $_SESSION["TourCode"] . '_' . $Card['Session'] . '_' . $Card['tNo'].'.pdf';
				}
				$pdf->AddPage();
				foreach($DistArray as $k => $CurDist) {
					if ($CurDist and (!$Card["NumEnds" . $CurDist] or $Card["D" . $CurDist] == '-')) {
						continue;
					}

					if($Options["TourField3D"]) {
						// Odd Distances on top
						$Yscore = $defScoreY2;
						if($k%2 == 0) {
							$Yscore = $defScoreY;
							if($k>1) {
								$pdf->AddPage();
							}
						}

						$pdf->DrawScoreField($defScoreX, $Yscore, $defScoreW, $defScoreH, $CurDist, $Card, $Data->SesTar4Session, $Data->SesFirstTarget);
						if(($Yscore == $defScoreY2 or count($DistArray)==1) and !empty($_REQUEST['QRCode'])) {
							foreach($_REQUEST['QRCode'] as $j => $Api) {
								require_once('Api/'.$Api.'/DrawQRCode.php');
								$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
								$Function($pdf, $QRCodeX + 30*$j, $QRCodeY, $Card['Session'], 0, substr($Card["tNo"],0,-1), '', 'Q', $PersonalScore);
							}
						}
					} else {
						// target archery
						if(($Data->Ath4Target==5 and $k==3) or ($Data->Ath4Target==6 and $k==3) or ($Data->Ath4Target>6 and $k==4)) {
							$pdf->AddPage();
						}
						$pdf->DrawScoreNew($Origins[$k][0], $Origins[$k][1], $defScoreW, $defScoreH, $CurDist, $Card);

						if(!empty($Options['QRCode'])) {
						    $qrOrigin = $Origins[$k][0];
						    if($Data->Ath4Target==4) {
						        switch ($k) {
                                    case 1:
						                $qrOrigin = $Origins[2][0]+$QRCodeX2;
						                break;
                                    case 2:
						                $qrOrigin = $Origins[1][0];
						                break;
                                    case 3:
                                        $qrOrigin += $QRCodeX2;
                                        break;
                                }
                            }
	                        $TmpTarget = substr($Cards[0]['tNo'],0,-1);
							foreach($Options['QRCode'] as $kQr => $Api) {
								require_once('Api/'.$Api.'/DrawQRCode.php');
								$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
								$Function($pdf, $qrOrigin + $QRCodeX + 30*$kQr, $QRCodeY, $Card['Session'], $CurDist, $TmpTarget, '', 'Q', $PersonalScore, $Card["D".$CurDist]);
							}
						}
					}
				}
			}
		}
        if($SaveDir) {
            if($FileName) {
                // a scorecard is ready
                $pdf->Output($SaveDir.$FileName, 'F');
                $Files[]=$FileName;
                $pdf=clone $OrgPdf;
	            if($File) {
		            file_put_contents($SaveDir.$File, $FileName);
	            }
            }
        }
	} else {
		foreach($Data->Scores as $Target => $Cards) {
			foreach($DistArray as $CurDist) {
				if ($CurDist and $Cards[0]["D" . $CurDist] == '-') {
					continue 2;
				}

				if($Options["TourField3D"]) {
					foreach($Cards as $Value) {
						$Yscore = $defScoreY2;
						switch(substr($Value["tNo"],-1,1)) {
							case 'A':
							case 'C':
							case 'E':
							case 'G':
							case 'I':
							case 'K':
								$pdf->AddPage();
								$Yscore = $defScoreY;
						}

						$pdf->DrawScoreField($defScoreX, $Yscore, $defScoreW, $defScoreH, $CurDist, $Value, $Data->SesTar4Session, $Data->SesFirstTarget);
						if($Yscore == $defScoreY2 and !empty($_REQUEST['QRCode'])) {
							foreach($_REQUEST['QRCode'] as $k => $Api) {
								require_once('Api/'.$Api.'/DrawQRCode.php');
								$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
								$Function($pdf, $QRCodeX + 30*$k, $QRCodeY, $Cards[0]['Session'], $CurDist, substr($Value["tNo"],0,-1), '', 'Q', $PersonalScore);
							}
						}
					}

				} else {

					$pdf->AddPage(($Data->Ath4Target <= 3) ? 'L' : 'P');
					switch($Data->Ath4Target) {
						case 1:
							if(empty($Cards[0]['Ath'])) {
								continue 2;
							}
							$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, $CurDist, $Cards[0]);
							break;
						case 2:
							$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, $CurDist, $Cards[0]);
							$pdf->DrawScoreNew(2*$defScoreX+$defScoreW, $defScoreY, $defScoreW, $defScoreH, $CurDist, $Cards[1]);
							break;
						case 3:
							$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, $CurDist, $Cards[0]);
							$pdf->DrawScoreNew(2*$defScoreX+$defScoreW, $defScoreY, $defScoreW, $defScoreH,$CurDist, $Cards[1]);
							$pdf->DrawScoreNew(3*$defScoreX+2*$defScoreW, $defScoreY, $defScoreW, $defScoreH,$CurDist,$Cards[2]);
							break;
						case 4:
							$pdf->DrawScoreNew( $defScoreX,  $defScoreY, $defScoreW, $defScoreH, $CurDist, $Cards[0]);
							$pdf->DrawScoreNew($defScoreX2,  $defScoreY, $defScoreW, $defScoreH, $CurDist, $Cards[1]);
							$pdf->DrawScoreNew( $defScoreX, $defScoreY2, $defScoreW, $defScoreH, $CurDist, $Cards[2]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $CurDist, $Cards[3]);
							break;
						case 5:
							$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[0]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[1]);
							$pdf->DrawScoreNew($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $CurDist,$Cards[2]);
							$pdf->AddPage('P');
							$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[3]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[4]);
							break;
						case 6:
							$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[0]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[1]);
							$pdf->DrawScoreNew($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $CurDist,$Cards[2]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $CurDist,$Cards[3]);
							$pdf->AddPage('P');
							$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[4]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[5]);
							break;
						case 7:
							$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[0]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[1]);
							$pdf->DrawScoreNew($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $CurDist,$Cards[2]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $CurDist,$Cards[3]);
							$pdf->AddPage('P');
							$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[4]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[5]);
							$pdf->DrawScoreNew($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $CurDist,$Cards[6]);
							break;
						case 8:
							$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[0]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[1]);
							$pdf->DrawScoreNew($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $CurDist,$Cards[2]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $CurDist,$Cards[3]);
							$pdf->AddPage('P');
							$pdf->DrawScoreNew($defScoreX, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[4]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $CurDist,$Cards[5]);
							$pdf->DrawScoreNew($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $CurDist,$Cards[6]);
							$pdf->DrawScoreNew($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $CurDist,$Cards[7]);
							break;
					}
					if(!empty($Options['QRCode'])) {
						$TmpTarget = substr($Cards[0]['tNo'],0,-1);
						foreach($Options['QRCode'] as $k => $Api) {
							require_once('Api/'.$Api.'/DrawQRCode.php');
							$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
							$Function($pdf, $QRCodeX + 30*$k, $QRCodeY, $Cards[0]['Session'], $CurDist, $TmpTarget, '', 'Q', $PersonalScore);
						}
					}
				}
			}
		}
	}
	if($SaveDir) {
		return($Files);
	}
	return $pdf;
}

function QualificationScorecards($Session, $FromTgt, $ToTgt, $IncludeEmpty=true, $ScoreDraw='', $FillWithArrows=false, $Category = false, $PersonalScore=false, $Options=array()) {
	$Data=new stdClass();
	$Data->QuTimestamp = '';
	$Data->Ath4Target = 4;
	$Data->NumEnds=12;
	$Coalesce=(!empty($Options['x_Coalesce']));

	if(!empty($Options['SessionType']) and $Options['SessionType']=='E') {
		if($Session>0) {
			$ses=GetSessions(null,false, array($Session.'_E'));
			$Data->Ath4Target = $ses[0]->SesAth4Target;
            $Data->SesTar4Session = $ses[0]->SesTar4Session;
            $Data->SesFirstTarget = $ses[0]->SesFirstTarget;
		} else {
	        $ses=GetSessions('E',false);
	        $Data->Ath4Target = $ses[0]->SesAth4Target;
            $Data->SesTar4Session = $ses[0]->SesTar4Session;
            $Data->SesFirstTarget = $ses[0]->SesFirstTarget;
	        foreach ($ses as $tmpSession) {
	            $Data->Ath4Target = max($Data->Ath4Target, $tmpSession->SesAth4Target);
                $Data->SesTar4Session =  max($Data->SesTar4Session, $tmpSession->SesTar4Session);
                $Data->SesFirstTarget =  max($Data->SesFirstTarget, $tmpSession->SesFirstTarget);
	        }
	    }
	} else {

		$MyQuery = "SELECT ToNumEnds AS TtNumEnds FROM Tournament  WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
		$Rs=safe_r_sql($MyQuery);
		if($r=safe_fetch($Rs)) {
			$Data->NumEnds=$r->TtNumEnds;
		}

		if($Session>0) {
			$ses=GetSessions(null,false, array($Session.'_Q'));
			$Data->Ath4Target = $ses[0]->SesAth4Target;
            $Data->SesTar4Session = $ses[0]->SesTar4Session;
            $Data->SesFirstTarget = $ses[0]->SesFirstTarget;
		} else {
	        $ses=GetSessions('Q',false);
	        $Data->Ath4Target = $ses[0]->SesAth4Target;
            $Data->SesTar4Session = $ses[0]->SesTar4Session;
            $Data->SesFirstTarget = $ses[0]->SesFirstTarget;
	        foreach ($ses as $tmpSession) {
	            $Data->Ath4Target = max($Data->Ath4Target, $tmpSession->SesAth4Target);
                $Data->SesTar4Session =  max($Data->SesTar4Session, $tmpSession->SesTar4Session);
                $Data->SesFirstTarget =  max($Data->SesFirstTarget, $tmpSession->SesFirstTarget);
	        }
	    }
	}

	if($Coalesce) {
	    $Data->Ath4Target*=2;
	}
	$Data->Scores=array();

	if($ScoreDraw=='Draw') {
		$DrawArray=array("Session"=>$Session, 'Arr0'=>'');
		// gets the correct distance/session
		if($ses[0]->SesType=='Q') {
			$tmp=getArrowEnds($ses[0]->SesOrder, 1);
			$DrawArray['NumEnds0']=$tmp[1]['ends'];
			$DrawArray['NumArrows0']=$tmp[1]['arrows'];
		}
        switch($Data->Session[$Session]) {
            case 1:
                $Data->Scores[]=array(
	                $DrawArray+array('tNo'=>'A'),
                );
                break;
            case 2:
                $Data->Scores[]=array(
	                $DrawArray+array('tNo'=>'A'),
	                $DrawArray+array('tNo'=>'B'),
                    );
                break;
            case 3:
                $Data->Scores[]=array(
	                $DrawArray+array('tNo'=>'A'),
	                $DrawArray+array('tNo'=>'B'),
	                $DrawArray+array('tNo'=>'C'),
                    );
                break;
            default:
                $Data->Scores[]=array(
	                $DrawArray+array('tNo'=>'A'),
	                $DrawArray+array('tNo'=>'B'),
	                $DrawArray+array('tNo'=>'C'),
	                $DrawArray+array('tNo'=>'D'),
                    );
        }
		return $Data;
    }

	$FillWithArrows=($FillWithArrows and $ScoreDraw!='TargetNo');

	if(!empty($Options['SessionType']) and $Options['SessionType']=='E') {
		$MyQuery=GetElimScoreBySessionQuery($Session, $Options['x_Phase'], $FromTgt, $ToTgt, $IncludeEmpty, $ScoreDraw, $PersonalScore);
	} else {
		if($Category) {
			$MyQuery=GetScoreByCategoryQuery();
		} else {
			$MyQuery=GetScoreBySessionQuery($Session, $FromTgt, $ToTgt, $IncludeEmpty, $ScoreDraw, $PersonalScore, empty($Options['Entry']) ? 0 : intval($Options['Entry']));
		}
	}
	$Rs=safe_r_sql($MyQuery);
	$BothTarget=array('','');
	$OldTarget='';
    while($MyRow=safe_fetch($Rs)) {
	    $Data->QuTimestamp=max($Data->QuTimestamp, $MyRow->QuTimestamp);
        //$MyRow->Session=$Session;
        if(!$FillWithArrows) {
            $MyRow->Arr1='';
            $MyRow->Arr2='';
            $MyRow->Arr3='';
            $MyRow->Arr4='';
            $MyRow->Arr5='';
            $MyRow->Arr6='';
            $MyRow->Arr7='';
            $MyRow->Arr8='';
            $MyRow->Tot1='';
            $MyRow->Tot2='';
            $MyRow->Tot3='';
            $MyRow->Tot4='';
            $MyRow->Tot5='';
            $MyRow->Tot6='';
            $MyRow->Tot7='';
            $MyRow->Tot8='';
        }
        if($ScoreDraw=='TargetNo') {
            $MyRow->Ath='';
            $MyRow->Noc='';
            $MyRow->CoCode='';
            $MyRow->CoName='';
        }
        $Target=($MyRow->AtTargetNo ? substr($MyRow->AtTargetNo,0,-1) : '-');
        $Data->Scores[$Target][]=(array) $MyRow;
	}
    if($Coalesce) {
    	$Targets=array_keys($Data->Scores);
    	$NewScores=array();
    	while($Targets) {
    		$tgt1=array_shift($Targets);
    		if($tgt2=@array_shift($Targets)) {
			    $Data->Scores["$tgt1"]=array_merge($Data->Scores["$tgt1"], $Data->Scores["$tgt2"]);
			    unset($Data->Scores[$tgt2]);
		    } else {
    			foreach($Data->Scores["$tgt1"] as $tmp) {
    				foreach($tmp as $k=>$v) {
    					if(!($k[0]=='D' and strlen($k)==2)) {
    						$tmp[$k]='';
					    }
				    }
    				//$tmp
				    $Data->Scores["$tgt1"][]=$tmp;
			    }
		    }
	    }
    }
	safe_free_result($Rs);
	return $Data;
}

function GetElimScoreBySessionQuery($Session, $Phase, $FromTgt, $ToTgt, $IncludeEmpty=true, $ScoreDraw, $PersonalScore) {
	$NoEmpty='';
	if(!$IncludeEmpty) {
		$NoEmpty = "INNER JOIN
			(SELECT DISTINCT EnTournament TgtTournament, QuTarget as TgtNo, QuSession as TgtSession
			FROM Qualifications
			INNER JOIN Entries On QuId=EnId
			WHERE EnTournament = {$_SESSION['TourId']} AND EnAthlete=1 " . ($Session!=-1 ? "AND QuSession=$Session and QuTarget between $FromTgt and $ToTgt " : "") .
			") as Tgt ON TgtTournament=AtTournament AND TgtNo=AtTarget and TgtSession=AtSession";
	}
	$MyQuery = "SELECT SUBSTRING(ElTargetNo,1,4) as tNo, ElTargetNo as AtTargetNo, ElTargetNo+0 as AtTarget, ElSession as Session, '' as Dist, EnCode, ElDateTime as QuTimestamp, EnDob as DoB, CoCode, CoName, ElTargetNo as QuTargetNo, CONCAT(EnFirstName,' ', EnName) AS Ath, CONCAT(CoCode, ' - ', CoName) as Noc, EnDivision as `Div`, EnClass as Cls, EdEmail as Email, 
            EvCode as Cat,
            '' as D0, '' as D1,
            '' as Arr0, ElArrowstring as Arr1,
            '' as QuD0, ElScore as QuD1,
            '' as Tot0, 0 as Tot1,
            '' as QuGD0, ElGold as QuGD1, 
            '' as QuXD0, ElXnine as QuXD1,
            '' as gxD0, ElGold+ElXnine as gxD1,
            length(trim(ElArrowstring)) as Arrows,
       		EvE".($Phase+1)."Ends as NumEnds0, EvE".($Phase+1)."Arrows as NumArrows0,
       		EvE".($Phase+1)."Ends as NumEnds1, EvE".($Phase+1)."Arrows as NumArrows1
		FROM Eliminations 
		inner JOIN Events ON EvCode=ElEventCode and EvTeamEvent=0 and EvTournament=ElTournament
		left JOIN Entries AS e ON ElId=e.EnId AND EnAthlete=1 
		left join ExtraData ON EdId=EnId and EdType='E'
		left JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament 
		LEFT JOIN Session ON ElSession=SesOrder AND ElTournament=SesTournament AND SesType='E' 
		WHERE ElTournament = {$_SESSION['TourId']} and ElElimPhase=" . intval($Phase)
		. (!(empty($FromTgt) && empty($ToTgt)) ? " AND ElTargetNo>='" . str_pad($FromTgt,'3','0',STR_PAD_LEFT) . "A' AND ElTargetNo<='" . str_pad($ToTgt,'3','0',STR_PAD_LEFT) . "Z' " : "")
		. (!empty($Session) ? " AND ElSession=". $Session : "")
		. ' ORDER BY ElSession, SesOrder, ElElimPhase, ElTargetNo ASC, EnFirstName, EnName, CoCode';

	return $MyQuery;
}

function GetScoreBySessionQuery($Session, $FromTgt, $ToTgt, $IncludeEmpty=true, $ScoreDraw, $PersonalScore, $EnId=0) {
	$NoEmpty='';
	if(!$IncludeEmpty) {
		$NoEmpty = "INNER JOIN
			(SELECT DISTINCT EnTournament TgtTournament, QuTarget as TgtNo, QuSession as TgtSession
			FROM Qualifications
			INNER JOIN Entries On QuId=EnId
			WHERE EnTournament = {$_SESSION['TourId']} AND EnAthlete=1 " . ($Session!=-1 ? "AND QuSession=$Session ".(($FromTgt+$ToTgt > 0) ? " and QuTarget between $FromTgt and $ToTgt " : "")  : "") .
			") as Tgt ON TgtTournament=AtTournament AND TgtNo=AtTarget and TgtSession=AtSession";
	}
	$MyQuery = "SELECT concat(AtTarget,AtLetter) as tNo, AtTargetNo, AtTarget, AtSession as Session, '' as Dist, EnCode, QuTimestamp, EnDob as DoB, CoCode, CoName, QuTargetNo, CONCAT(EnFirstName,' ', EnName) AS Ath, CONCAT(CoCode, ' - ', CoName) as Noc, EnDivision as `Div`, EnClass as Cls, EdEmail as Email, 
            concat(EnDivision, ' ', EnClass) as Cat,
            '' as D0, Td1 as D1, Td2 as D2, Td3 as D3, Td4 as D4, Td5 as D5, Td6 as D6, Td7 as D7, Td8 as D8,
            '' as Arr0, QuD1Arrowstring as Arr1, QuD2Arrowstring as Arr2, QuD3Arrowstring as Arr3, QuD4Arrowstring as Arr4, QuD5Arrowstring as Arr5, QuD6Arrowstring as Arr6, QuD7Arrowstring as Arr7, QuD8Arrowstring as Arr8,
            '' as QuD0, QuD1Score as QuD1, QuD2Score as QuD2, QuD3Score as QuD3, QuD4Score as QuD4, QuD5Score as QuD5, QuD6Score as QuD6, QuD7Score as QuD7, QuD8Score as QuD8,
            '' as Tot0, 0 as Tot1, QuD1Score as Tot2, QuD1Score+QuD2Score as Tot3, QuD1Score+QuD2Score+QuD3Score as Tot4, QuD1Score+QuD2Score+QuD3Score+QuD4Score as Tot5, QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score as Tot6, QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score as Tot7, QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score as Tot8,
            '' as QuGD0, QuD1Gold as QuGD1, QuD2Gold as QuGD2, QuD3Gold as QuGD3, QuD4Gold as QuGD4, QuD5Gold as QuGD5, QuD6Gold as QuGD6, QuD7Gold as QuGD7, QuD8Gold as QuGD8, 
            '' as QuXD0, QuD1XNine as QuXD1, QuD2XNine as QuXD2, QuD3XNine as QuXD3, QuD4XNine as QuXD4, QuD5XNine as QuXD5, QuD6XNine as QuXD6, QuD7XNine as QuXD7, QuD8XNine as QuXD8,
            '' as gxD0, QuD1Gold+QuD1XNine as gxD1, QuD2Gold+QuD2XNine as gxD2, QuD3Gold+QuD3XNine as gxD3, QuD4Gold+QuD4XNine as gxD4, QuD5Gold+QuD5XNine as gxD5, QuD6Gold+QuD6XNine as gxD6, QuD7Gold+QuD7XNine as gxD7, QuD8Gold+QuD8XNine as gxD8,
            length(trim(concat(QuD1Arrowstring, QuD2Arrowstring, QuD3Arrowstring, QuD4Arrowstring, QuD5Arrowstring, QuD6Arrowstring, QuD7Arrowstring, QuD8Arrowstring))) as Arrows,
       		d1.DiEnds as NumEnds0, d1.DiArrows as NumArrows0,
       		d1.DiEnds as NumEnds1, d1.DiArrows as NumArrows1, d2.DiEnds as NumEnds2, d2.DiArrows as NumArrows2, d3.DiEnds as NumEnds3, d3.DiArrows as NumArrows3, d4.DiEnds as NumEnds4, d4.DiArrows as NumArrows4,
       		d5.DiEnds as NumEnds5, d5.DiArrows as NumArrows5, d6.DiEnds as NumEnds6, d6.DiArrows as NumArrows6, d7.DiEnds as NumEnds7, d7.DiArrows as NumArrows7, d8.DiEnds as NumEnds8, d8.DiArrows as NumArrows8
		FROM AvailableTarget " .
        ((!$IncludeEmpty AND $PersonalScore) ? "INNER" : "LEFT") . " join (select * from Qualifications 
			inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']}
			inner join Countries on CoId=EnCountry and CoTournament=EnTournament
			inner join Tournament on ToId=EnTournament
			left join ExtraData ON EdId=EnId and EdType='E'
			left join TournamentDistances ON ToType=TdType and TdTournament=ToId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses) sqy on QuTargetNo=AtTargetNo
			left join DistanceInformation d1 on d1.DiTournament=AtTournament and d1.DiType='Q' and d1.DiSession=AtSession and d1.DiDistance=1
			left join DistanceInformation d2 on d2.DiTournament=AtTournament and d2.DiType='Q' and d2.DiSession=AtSession and d2.DiDistance=2
			left join DistanceInformation d3 on d3.DiTournament=AtTournament and d3.DiType='Q' and d3.DiSession=AtSession and d3.DiDistance=3
			left join DistanceInformation d4 on d4.DiTournament=AtTournament and d4.DiType='Q' and d4.DiSession=AtSession and d4.DiDistance=4
			left join DistanceInformation d5 on d5.DiTournament=AtTournament and d5.DiType='Q' and d5.DiSession=AtSession and d5.DiDistance=5
			left join DistanceInformation d6 on d6.DiTournament=AtTournament and d6.DiType='Q' and d6.DiSession=AtSession and d6.DiDistance=6
			left join DistanceInformation d7 on d7.DiTournament=AtTournament and d7.DiType='Q' and d7.DiSession=AtSession and d7.DiDistance=7
			left join DistanceInformation d8 on d8.DiTournament=AtTournament and d8.DiType='Q' and d8.DiSession=AtSession and d8.DiDistance=8
		$NoEmpty
		where AtTournament={$_SESSION['TourId']} ".
        ($Session!=-1 ? " AND AtSession=$Session ".(($FromTgt+$ToTgt > 0) ? " and AtTarget between $FromTgt and $ToTgt " : "") : "").
		($EnId ? " and EnId=$EnId " : "").
		"ORDER BY AtTargetNo ASC, Ath, Noc";
	return $MyQuery;
}

function GetScoreByCategoryQuery($Category='') {
	$MyQuery = "SELECT EnCode, EnDob as DoB, CoCode, CoName, QuSession as Session, QuTimestamp, QuTargetNo, QuTarget as AtTarget, '' as Dist, SUBSTRING(QuTargetNo,2) as tNo, CONCAT(EnFirstName,' ', EnName) AS Ath, CONCAT(CoCode, ' - ', CoName) as Noc, EnDivision as `Div`, EnClass as Cls, EdEmail as Email, 
           concat(EnDivision, ' ', EnClass) as Cat,
            '' as D0, Td1 as D1, Td2 as D2, Td3 as D3, Td4 as D4, Td5 as D5, Td6 as D6, Td7 as D7, Td8 as D8,
            '' as Arr0, QuD1Arrowstring as Arr1, QuD2Arrowstring as Arr2, QuD3Arrowstring as Arr3, QuD4Arrowstring as Arr4, QuD5Arrowstring as Arr5, QuD6Arrowstring as Arr6, QuD7Arrowstring as Arr7, QuD8Arrowstring as Arr8,
            '' as QuD0, QuD1Score as QuD1, QuD2Score as QuD2, QuD3Score as QuD3, QuD4Score as QuD4, QuD5Score as QuD5, QuD6Score as QuD6, QuD7Score as QuD7, QuD8Score as QuD8,
            '' as Tot0, 0 as Tot1, QuD1Score as Tot2, QuD1Score+QuD2Score as Tot3, QuD1Score+QuD2Score+QuD3Score as Tot4, QuD1Score+QuD2Score+QuD3Score+QuD4Score as Tot5, QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score as Tot6, QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score as Tot7, QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score as Tot8,
            '' as QuGD0, QuD1Gold as QuGD1, QuD2Gold as QuGD2, QuD3Gold as QuGD3, QuD4Gold as QuGD4, QuD5Gold as QuGD5, QuD6Gold as QuGD6, QuD7Gold as QuGD7, QuD8Gold as QuGD8, 
            '' as QuXD0, QuD1XNine as QuXD1, QuD2XNine as QuXD2, QuD3XNine as QuXD3, QuD4XNine as QuXD4, QuD5XNine as QuXD5, QuD6XNine as QuXD6, QuD7XNine as QuXD7, QuD8XNine as QuXD8,
            '' as gxD0, QuD1Gold+QuD1XNine as gxD1, QuD2Gold+QuD2XNine as gxD2, QuD3Gold+QuD3XNine as gxD3, QuD4Gold+QuD4XNine as gxD4, QuD5Gold+QuD5XNine as gxD5, QuD6Gold+QuD6XNine as gxD6, QuD7Gold+QuD7XNine as gxD7, QuD8Gold+QuD8XNine as gxD8,
            length(trim(concat(QuD1Arrowstring, QuD2Arrowstring, QuD3Arrowstring, QuD4Arrowstring, QuD5Arrowstring, QuD6Arrowstring, QuD7Arrowstring, QuD8Arrowstring))) as Arrows,
       		d1.DiEnds as NumEnds0, d1.DiArrows as NumArrows0,
       		d1.DiEnds as NumEnds1, d1.DiArrows as NumArrows1, d2.DiEnds as NumEnds2, d2.DiArrows as NumArrows2, d3.DiEnds as NumEnds3, d3.DiArrows as NumArrows3, d4.DiEnds as NumEnds4, d4.DiArrows as NumArrows4,
       		d5.DiEnds as NumEnds5, d5.DiArrows as NumArrows5, d6.DiEnds as NumEnds6, d6.DiArrows as NumArrows6, d7.DiEnds as NumEnds7, d7.DiArrows as NumArrows7, d8.DiEnds as NumEnds8, d8.DiArrows as NumArrows8
        FROM Entries
        INNER JOIN Qualifications ON EnId = QuId
        INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
        INNER JOIN Tournament ON EnTournament=ToId
        LEFT JOIN ExtraData ON EdId=EnId and EdType='E'
        LEFT JOIN TournamentDistances ON ToType=TdType and TdTournament=ToId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses
		left join DistanceInformation d1 on d1.DiTournament=AtTournament and d1.DiType and d1.DiSession=AtSession and d1.DiDistance=1
		left join DistanceInformation d2 on d2.DiTournament=AtTournament and d2.DiType and d2.DiSession=AtSession and d2.DiDistance=2
		left join DistanceInformation d3 on d3.DiTournament=AtTournament and d3.DiType and d3.DiSession=AtSession and d3.DiDistance=3
		left join DistanceInformation d4 on d4.DiTournament=AtTournament and d4.DiType and d4.DiSession=AtSession and d4.DiDistance=4
		left join DistanceInformation d5 on d5.DiTournament=AtTournament and d5.DiType and d5.DiSession=AtSession and d5.DiDistance=5
		left join DistanceInformation d6 on d6.DiTournament=AtTournament and d6.DiType and d6.DiSession=AtSession and d6.DiDistance=6
		left join DistanceInformation d7 on d7.DiTournament=AtTournament and d7.DiType and d7.DiSession=AtSession and d7.DiDistance=7
		left join DistanceInformation d8 on d8.DiTournament=AtTournament and d8.DiType and d8.DiSession=AtSession and d8.DiDistance=8
        WHERE EnTournament = {$_SESSION['TourId']} ".($Category ? "AND concat(EnDivision,EnClass)='$Category'" : '')."
        ORDER BY EnFirstName, EnName, CoCode";
	return $MyQuery;
}
