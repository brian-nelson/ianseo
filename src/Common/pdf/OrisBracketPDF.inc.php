<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once("Common/pdf/OrisPDF.inc.php");

class OrisBracketPDF extends OrisPDF
{
	var $CellHSp = 0;
	var $CellVSp = 0;
	var $TeamMatchNoPosition = array(0,0,0,0);
	var $Finalists=array();
	var $MaxFinalists=0;
	var $FinalRank='';
	var $OrisPages='AB';

	//Constructor
	function __construct($DocNumber, $DocTitle, $Headers='')
	{
		//$this->__construct('P','mm','A4');
		parent::__construct($DocNumber, $DocTitle, $Headers);
		$this->SetAutoPageBreak(false);
	}


	function FirstColumn($item) {
		$this->SetLineWidth(0.1);

		$this->SetFont('','',8);
		$this->SetXY(OrisPDF::leftMargin,$this->lastY);

		$this->linethrough = $item->strike;
		$this->Cell($this->DataSize[0], $this->CellVSp, is_null($item->FirstName) ? '' : $item->IndRank . '/',0,0,'R');
		$this->Cell($this->DataSize[1],$this->CellVSp,$item->QuScore,0,0,'R');
		$this->Cell($this->DataSize[2],$this->CellVSp, $item->QuNotes,0,0,'R');

		if(!is_null($item->FirstName)) {
			if($item->Bold) {
				$this->SetFont('','b');
			}
			$this->linethrough = $item->strike;
			$this->Cell($this->DataSize[3],$this->CellVSp,$item->FirstName . ' ' . $item->Name,0,0,'L');
			$this->SetFont('','');
			$this->linethrough = $item->strike;
		} elseif($item->Saved) {
			$this->setfont('','i');
			$this->linethrough = $item->strike;
			$this->Cell($this->DataSize[3],$this->CellVSp,$item->Saved,0,0,'L');
			$this->setfont('','');
			$this->linethrough = $item->strike;
		} elseif($item->OppTie==2) {
			$this->Cell($this->DataSize[3],$this->CellVSp,'-Bye-',0,0,'L');
		} else {
			$this->Cell($this->DataSize[3],$this->CellVSp,'',0,0,'L');
		}
		$this->Cell($this->DataSize[4],$this->CellVSp,$item->Country,0,0,'L');

		if($item->Bold) {
			$this->SetFont('','b');
		}
		$this->linethrough = $item->strike;
		$m=$this->getCellPaddings();
		$this->setCellPaddings('','',0,'');
		$this->Cell($item->ScoreCell,$this->CellVSp, $item->Score, 0,0,'L');
		$this->SetFont('','');
		$this->linethrough = $item->strike;
		$this->setCellPaddings(0,'',0,'');
		$this->Cell($this->CellHSp-$item->ScoreCell-1,$this->CellVSp, $item->ScoreDetails, 0,0,'L');
		$this->setCellPaddings($m['L'],'',$m['R'],'');

		$this->setX($this->getX()+1);

		if($item->FinMatchNo % 2 == 0 and $item->DrawMatch) {
			//Linee Orizzontali
			$this->Line($this->getX(),$this->getY(),$this->getX()-5,$this->getY());
			$this->Line($this->getX(),$this->getY()+2*$this->CellVSp,$this->getX()-5,$this->getY()+2*$this->CellVSp);
			//Linea Verticale
			$this->Line($this->getX(),$this->getY(),$this->getX(),$this->getY()+2*$this->CellVSp);
			if($item->ScheduledDate != '' && $item->ScheduledTime != '') {
				//debug_svela($item);
				$ShowSchedule= ($item->FinTie==0 && $item->OppTie==0 && $item->Score==0 && $item->OppScore==0 && $item->FinRank==0 && $item->OppFinRank==0);
				$this->SetFont('','',6);
				$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$this->DataSize[4],$this->getY()+0.4*$this->CellVSp);
				$this->Cell($this->CellHSp,0.6*$this->CellVSp, ($ShowSchedule ? $item->ScheduledDate : ''), 0, 0, 'R', 0);
				$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$this->DataSize[4],$this->getY()+0.6*$this->CellVSp);
				$this->Cell($this->CellHSp,0.6*$this->CellVSp, ($ShowSchedule ? $item->ScheduledTime : ''), 0, 0, 'R', 0);
			}
		}

		$this->lastY += (($item->FinMatchNo % 2 == 0 ? 1:2) * $this->CellVSp);
	}

	function OtherColumns($PhaseCounter, $item) {
		$this->SetLineWidth(0.1);
		$this->SetFont('','',8);
		$this->SetXY(83+$PhaseCounter*$this->CellHSp,$this->lastY);
		mb_regex_encoding('UTF-8');

		if($item->Bold) {
			$this->SetFont('','b');
		}
		$this->linethrough = $item->strike;

		if(!is_null($item->FirstName)) {
			$this->Cell($this->CellHSp,$this->CellVSp,$item->FirstName . ' ' . $this->FirstLetters($item->Name) . " (" . $item->Country . ")" ,0,0,'L');
		} elseif($item->OppTie==2) {
			$this->Cell($this->CellHSp,$this->CellVSp,'-Bye-',0,0,'L');
		} else {
			$this->Cell($this->CellHSp,$this->CellVSp,'',0,0,'L');
		}

		$this->lastY += $this->CellVSp;
		$this->SetXY(83+$PhaseCounter*$this->CellHSp,$this->lastY);

		//Punteggio
		$m=$this->getCellPaddings();
		$this->setCellPaddings('','',0,'');
		$this->Cell($item->ScoreCell,$this->CellVSp,$item->Score,0,0,'L');
		$this->SetFont('','');
		$this->linethrough = $item->strike;
		$this->setCellPaddings(0,'',0,'');
		$this->Cell($this->CellHSp-$item->ScoreCell-1,$this->CellVSp,$item->ScoreDetails,0,0,'L');
		$this->setCellPaddings($m['L'],'',$m['R'],'');
		$this->setX($this->getX()+1);


		$this->Line(83+$PhaseCounter*$this->CellHSp,$this->lastY,83+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY);
		if($item->FinMatchNo % 2 == 0)
		{
			$this->Line(83+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY,83+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY+($PhaseCounter==1 ? 3: ($PhaseCounter==2 ? 6: ($PhaseCounter==3 ? 12: 24))) * $this->CellVSp);
			if($item->ScheduledDate != '' && $item->ScheduledTime != '') {
				$ShowSchedule= ($item->FinTie==0 && $item->OppTie==0 && empty($item->Score) && empty($item->OppScore));
				$this->SetFont('','',6);
				$this->SetXY(83+$PhaseCounter*$this->CellHSp,$this->getY()+($PhaseCounter==1 ? 3: ($PhaseCounter==2 ? 6: ($PhaseCounter==3 ? 12: 24))) * $this->CellVSp/2 - 0.6 * $this->CellVSp);
				$this->Cell($this->CellHSp,0.6*$this->CellVSp, ($ShowSchedule ? $item->ScheduledDate : ''), 0, 0, 'R', 0);
				$this->SetXY(83+$PhaseCounter*$this->CellHSp,$this->getY()+0.6*$this->CellVSp);
				$this->Cell($this->CellHSp,0.6*$this->CellVSp, ($ShowSchedule ? $item->ScheduledTime : ''), 0, 0, 'R', 0);
			}
		}

		$this->lastY += ($PhaseCounter==1 ? 2: ($PhaseCounter==2 ? 5: ($PhaseCounter==3 ? 11: 23))) * $this->CellVSp;
	}

	function PrintFinalists() {
		if(count($this->Finalists)==0) {
			return;
		}
		$this->SetXY(110, 170);
		$this->SetFont('','b');
		$this->Cell(0,0, $this->FinalRank);
		$this->SetFont('','');
		$this->Rect(109,169,$this->getPageWidth()-119, min(8, $this->MaxFinalists)*4 + 6);
		$Striked=false;
		for($n=1;$n<=min(8,$this->MaxFinalists);$n++) {
			if(isset($this->Finalists[$n])) {
				$Striked=true;

				foreach($this->Finalists[$n] as $Fin) {
					$this->SetxY(110, 170+$n*4);
					$this->Cell(10,4, $Fin->ShowRank ? $Fin->FinRank : $Fin->IrmText, '','','R');
					$this->Cell(45,4, $Fin->FirstName.' '.$Fin->Name);
					$this->Cell(10,4, $Fin->Country);
					if($n>4) {
						$this->Cell(8,4, $Fin->Score, '','','R');
						if($Fin->ScoreMatch and $Fin->ScoreMatch!=$Fin->Score) {
							$this->Cell(10,4, '('.$Fin->ScoreMatch.')');
						}
					}
					$n++;
				}
				$n--;
			} else {
				$this->SetxY(110, 170+$n*4);
				$this->Cell(10,4, $n, '','','R');
				$this->Cell(45,4, $Striked ? $this->NotAwarded : '');
			}
		}
	}

	function PrintFinalistsTeam($Top) {
		if(count($this->Finalists)==0) {
			return;
		}
		if($Top) {
		    $extraH = 0;
		    if(count($this->Records)) {
		        foreach ($this->Records as $rec) {
                    $extraH += count($rec->RtRecExtra);
                }
            }
			$StartY=OrisPDF::topStart+12+($extraH*4);
			$StartX=155;
		} else {
			$StartY=$this->getPageHeight()-100;
			$StartX=155;
		}
		$StartX=max(155, $this->getPageWidth()+1-$this->getSideMargin()-$this->CellHSp*2);
		$W=$this->getPageWidth()-$StartX-$this->getSideMargin();
		$W1=$W*0.10;
		$W2=$W*0.16;
		$W4=$W*0.11;
		$W5=$W*0.20;
		$W3=$W-$W1-$W2-$W4-$W5;
		$this->SetXY($StartX, $StartY);
		$this->SetFont('','b');
		$this->Cell(0,0, $this->FinalRank);
		$this->SetFont('','');
		$this->Rect($StartX,$StartY,$W, min(8, $this->MaxFinalists)*4 + 6);
		$Striked=false;
		for($n=1;$n<=min(8,$this->MaxFinalists);$n++) {
			if(isset($this->Finalists[$n])) {
				$Striked=true;

				foreach($this->Finalists[$n] as $Fin) {
					$this->SetxY($StartX, $StartY+$n*4);
					$this->Cell($W1,4, $Fin->ShowRank ? $Fin->FinRank : $Fin->IrmText, '','','R');
					$this->Cell($W2,4, $Fin->Country);
					$this->Cell($W3,4, $Fin->Team);
					if($n>4) {
						if($Fin->ScoreMatch and $Fin->ScoreMatch!=$Fin->Score) {
							$this->Cell($W4,4, $Fin->Score, '','','R');
							$this->Cell($W5,4, '('.$Fin->ScoreMatch.')');
						} else {
							$this->Cell($W4+$W5,4, $Fin->Score, '','','R');
						}
					}
					$n++;
				}
				$n--;
			} else {
				$this->SetxY($StartX, $StartY+$n*4);
				$this->Cell(5,4, $n, '','','R');
				$this->Cell(41,4, $Striked ? $this->NotAwarded : '');
			}
		}
	}

	function PrintMedals($PhaseCounter, $GoldFirstName, $GoldName, $GoldCountry, $SilverFirstName, $SilverName, $SilverCountry, $BronzeFirstName, $BronzeName, $BronzeCountry)
	{
		$this->SetLineWidth(0.1);
		$this->SetXY(95+$PhaseCounter*$this->CellHSp,$this->lastY);
		$this->SetFont('','B');
		$this->Cell($this->CellHSp-5,$this->CellVSp,"GOLD",0,1,'L');
		$this->SetXY(83+$PhaseCounter*$this->CellHSp,$this->GetY());
		$this->SetFont('','');
		if(!is_null($GoldFirstName) && strlen($GoldFirstName)>0)
			$this->Cell($this->CellHSp,$this->CellVSp,$GoldFirstName . ' ' . $this->FirstLetters($GoldName). " (" . $GoldCountry . ")" ,0,0,'L');
		$this->Line(83+$PhaseCounter*$this->CellHSp,$this->lastY+2*$this->CellVSp,83+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY+2*$this->CellVSp);

		$this->lastY += ($PhaseCounter==2 ? 3: 6) * $this->CellVSp;
		$this->SetXY(95+$PhaseCounter*$this->CellHSp,$this->lastY);
		$this->SetFont('','B');
		$this->Cell($this->CellHSp-5,$this->CellVSp,"SILVER",0,1,'L');
		$this->SetXY(83+$PhaseCounter*$this->CellHSp,$this->GetY());
		$this->SetFont('','');
		if(!is_null($SilverFirstName) && strlen($SilverFirstName)>0)
			$this->Cell($this->CellHSp,$this->CellVSp,$SilverFirstName . ' ' . $this->FirstLetters($SilverName). " (" . $SilverCountry . ")" ,0,0,'L');
		$this->Line(83+$PhaseCounter*$this->CellHSp,$this->lastY+2*$this->CellVSp,83+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY+2*$this->CellVSp);

		$this->lastY += ($PhaseCounter==2 ? 3: 6) * $this->CellVSp;
		$this->SetXY(95+$PhaseCounter*$this->CellHSp,$this->lastY);
		$this->SetFont('','B');
		$this->Cell($this->CellHSp-5,$this->CellVSp,"BRONZE",0,1,'L');
		$this->SetXY(83+$PhaseCounter*$this->CellHSp,$this->GetY());
		$this->SetFont('','');
		if(!is_null($BronzeFirstName) && strlen($BronzeFirstName)>0)
			$this->Cell($this->CellHSp,$this->CellVSp,$BronzeFirstName . ' ' . $this->FirstLetters($BronzeName). " (" . $BronzeCountry . ")" ,0,0,'L');
		$this->Line(83+$PhaseCounter*$this->CellHSp,$this->lastY+2*$this->CellVSp,83+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY+2*$this->CellVSp);

	}

	function FirstColumnTeam($item) {
		$this->SetLineWidth(0.1);
		$this->SetFont('','', 8);
		$this->SetXY(OrisPDF::leftMargin,$this->lastY);

		$this->linethrough = $item->strike;
		$this->Cell($this->DataSize[0],1.5*$this->CellVSp,$item->OppTie==2 || is_null($item->Country) ? '' : $item->TeRank . '/',0,0,'R');
		$this->Cell($this->DataSize[1],1.5*$this->CellVSp, $item->TeScore,0,0,'R');
		$this->Cell($this->DataSize[2],1.5*$this->CellVSp, $item->TeNotes,0,0,'R');

		if($item->Bold) {
			$this->SetFont('','b');
		}
		$this->linethrough = $item->strike;
        if($item->Saved) {
            $this->setfont('', 'i');
			$this->linethrough = $item->strike;
            $this->Cell($this->DataSize[3] + $this->DataSize[4], 1.5 * $this->CellVSp, $item->Saved, 0, 0, 'L');
            $this->setfont('', '');
			$this->linethrough = $item->strike;
        } elseif($item->OppTie==2 and !$item->Country) {
            $this->setfont('', '');
			$this->linethrough = $item->strike;
            $this->Cell($this->DataSize[3] + $this->DataSize[4], 1.5 * $this->CellVSp, '-Bye-', 0, 0, 'L');
        } elseif(is_null($item->Country)) {
            $this->Cell($this->DataSize[3] + $this->DataSize[4], 1.5 * $this->CellVSp, '', 0, 0, 'L');
        } else {
			$this->Cell($this->DataSize[3],1.5*$this->CellVSp, mb_convert_case($item->Country, MB_CASE_UPPER, "UTF-8"),0,0,'L');
			$this->Cell($this->DataSize[4],1.5*$this->CellVSp, $item->Team,0,0,'L');
		}

        // score
		$m=$this->getCellPaddings();
		$this->setCellPaddings('',$m['T']+0.5,0,$m['B']+0.5);
		$this->Cell($item->ScoreCell,(1.5 + count($item->Componenti))*$this->CellVSp, $item->Score, 0,0,'L', '','',1,false,'T',$item->TfMatchNo % 2 ? 'B' : 'T');
		$this->SetFont('','');
		$this->linethrough = $item->strike;
		$this->setCellPaddings(0,'',0,'');
		$this->Cell($this->CellHSp-$item->ScoreCell-1,(1.5 + count($item->Componenti))*$this->CellVSp, $item->ScoreDetails, 0,0,'L', '','',1,false,'T',$item->TfMatchNo % 2 ? 'B' : 'T');
		$this->setCellPaddings($m['L'],$m['T'],$m['R'],$m['B']);

		$this->SetFont('','');
		$this->linethrough = $item->strike;


		//Scrivo i componenti
		for($i=0; $i<count($item->Componenti); $i++) {
			$this->SetFont('','','6');
			$this->linethrough = $item->strike;
			$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1],$this->lastY+(1.5+$i)*$this->CellVSp);
			$this->Cell($this->DataSize[2],$this->CellVSp, '',0,0,'R');
			$this->Cell($this->DataSize[3],$this->CellVSp, '',0,0,'R');
			$this->Cell(1,$this->CellVSp, '',0,0,'R');
			$this->Cell($this->DataSize[4]-1,$this->CellVSp, $item->Componenti[$i][1],0,0,'L');
			$this->SetFont('','','8');
		}

		if($item->TfMatchNo % 2 == 0) {
			$TmpX=$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+10;
			$this->Line($TmpX, $this->lastY,$TmpX+$this->DataSize[4]+$this->CellHSp, $this->lastY);
			$this->Line($TmpX,$this->lastY+(3+2*count($item->Componenti))*$this->CellVSp,$TmpX+$this->DataSize[4]+$this->CellHSp,$this->lastY+(3+2*count($item->Componenti))*$this->CellVSp);
			$this->Line($TmpX+$this->DataSize[4]+$this->CellHSp,$this->lastY,$TmpX+$this->DataSize[4]+$this->CellHSp,$this->lastY+(3+2*count($item->Componenti))*$this->CellVSp);
			if($item->ScheduledDate != '' && $item->ScheduledTime != '')
			{
				$this->SetFont('','',6);
				$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$this->DataSize[4],$this->lastY+((3+2*count($item->Componenti))*$this->CellVSp)/2-$this->CellVSp);
				$this->Cell($this->CellHSp,$this->CellVSp, (($item->TfTie==0 && $item->OppTie==0 && $item->Score==0 && $item->OppScore==0) ? $item->ScheduledDate : ''), 0, 0, 'R', 0);
				$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$this->DataSize[4],$this->lastY+((3+2*count($item->Componenti))*$this->CellVSp)/2);
				$this->Cell($this->CellHSp,$this->CellVSp, (($item->TfTie==0 && $item->OppTie==0 && $item->Score==0 && $item->OppScore==0) ? $item->ScheduledTime : ''), 0, 0, 'R', 0);
				$this->SetFont('','','8');
			}
		}

		$this->lastY += ((count($item->Componenti)+1.5+($item->TfMatchNo % 2)) * $this->CellVSp);
	}

	function OtherColumnsTeam($PhaseCounter, $item,  $NumComponenti) {
		$StartX = OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$this->DataSize[4]+$PhaseCounter*$this->CellHSp;
		switch($PhaseCounter) {
			case 0:
				$StartY=0;
				break;
			case 1:
				$StartY=4+2*$NumComponenti;
				break;
			case 2:
				$StartY=8+4*$NumComponenti;
				break;
			case 3:
				$StartY=16+8*$NumComponenti;
				break;
			default:
				$StartY=32+16*$NumComponenti;
		}
		$this->SetLineWidth(0.1);
		$this->SetFont('','');
		$this->SetXY($StartX,$this->lastY);

		$this->linethrough = $item->strike;
		if($item->OppTie==2 and !$item->Team) {
            $this->Cell($this->CellHSp, 1.5 * $this->CellVSp, '-Bye-', 0, 0, 'L');
        } else if(is_null($item->Team)) {
            $this->Cell($this->CellHSp, 1.5 * $this->CellVSp, '', 0, 0, 'L');
        } else {
			if($item->Bold) {
				$this->SetFont('','b');
			}
			$this->linethrough = $item->strike;
            $this->Cell($this->CellHSp, 1.5 * $this->CellVSp, $item->Team, 0, 0, 'L');
        }

		$this->lastY += 1.5*$this->CellVSp;
		$this->SetXY($StartX,$this->lastY);

		//Punteggio
		$m=$this->getCellPaddings();
		$this->setCellPaddings('','',0,'');
		$this->Cell($item->ScoreCell, 1.5*$this->CellVSp, $item->Score, 0, 0, 'L', 0);		//No Tie
		$this->SetFont('','');
		$this->linethrough = $item->strike;
		$this->setCellPaddings(0,'',0,'');
		$this->Cell($this->CellHSp-$item->ScoreCell-1, 1.5*$this->CellVSp, $item->ScoreDetails, 0, 0, 'L', 0);		//No Tie
		$this->setCellPaddings($m['L'],'',$m['R'],'');

		$this->Line($StartX,$this->lastY,$StartX+$this->CellHSp,$this->lastY);

		if($item->TfMatchNo % 2 == 0) {
			if($item->TfMatchNo!=2) {
				$this->Line($StartX+$this->CellHSp,$this->lastY,$StartX+$this->CellHSp,$this->lastY + $StartY*$this->CellVSp);
				if($item->ScheduledDate != '' && $item->ScheduledTime != '') {
					$this->SetFont('','',6);
					$this->SetXY($StartX,$this->lastY+($StartY * $this->CellVSp)/2-$this->CellVSp);
					$this->Cell($this->CellHSp,$this->CellVSp, (($item->TfTie==0 && $item->OppTie==0 && $item->Score==0 && $item->OppScore==0) ? $item->ScheduledDate : ''), 0, 0, 'R', 0);
					$this->SetXY($StartX,$this->lastY+($StartY * $this->CellVSp)/2);
					$this->Cell($this->CellHSp,$this->CellVSp, (($item->TfTie==0 && $item->OppTie==0 && $item->Score==0 && $item->OppScore==0) ? $item->ScheduledTime : ''), 0, 0, 'R', 0);
					$this->SetFont('','',8);
				}
			} else {
				$this->Line($StartX+$this->CellHSp,$this->lastY,$StartX+$this->CellHSp,$this->lastY+($PhaseCounter==3 ? 4.5 : (3.5+$NumComponenti)) *$this->CellVSp);
				if($item->ScheduledDate != '' && $item->ScheduledTime != '') {
					$this->SetFont('','',6);
					$this->SetXY($StartX,$this->lastY+(($PhaseCounter==3 ? 4.5 : (3.5+$NumComponenti)) *$this->CellVSp)/2-$this->CellVSp);
					$this->Cell($this->CellHSp,$this->CellVSp, (($item->TfTie==0 && $item->OppTie==0 && $item->Score==0 && $item->OppScore==0) ? $item->ScheduledDate : ''), 0, 0, 'R', 0);
					$this->SetXY($StartX,$this->lastY+(($PhaseCounter==3 ? 4.5 : (3.5+$NumComponenti)) *$this->CellVSp)/2);
					$this->Cell($this->CellHSp,$this->CellVSp, (($item->TfTie==0 && $item->OppTie==0 && $item->Score==0 && $item->OppScore==0) ? $item->ScheduledTime : ''), 0, 0, 'R', 0);
					$this->SetFont('','',8);
				}
			}
		}

		//Se sono nelle finali mi salvo le posiioni - mi serve per le medaglie
		if($item->TfMatchNo<4) {
            $this->TeamMatchNoPosition[$item->TfMatchNo] = $this->lastY;
        }

		//Calcolo la Posizione del Next Matchno
		if($item->TfMatchNo==1) {
			$this->lastY += ($PhaseCounter==4 ? (14+2*$NumComponenti) : ($PhaseCounter==3 ? (5.25+2*$NumComponenti) : (4+2*$NumComponenti))) *$this->CellVSp;
		} else if($item->TfMatchNo==2) {
			$this->lastY += ($PhaseCounter>=3 ? 3 : (2+$NumComponenti)) *$this->CellVSp;
		} else {
            $this->lastY += ($PhaseCounter == 1 ? (2.5 + 2 * $NumComponenti) : ($PhaseCounter == 2 ? (6.5 + 4 * $NumComponenti) : ($PhaseCounter == 3 ? (14.5 + 8 * $NumComponenti) : (30.5 + 16 * $NumComponenti)))) * $this->CellVSp;
        }
	}

	function PrintMedalsTeam($PhaseCounter, $GoldCountry, $SilverCountry, $BronzeCountry, $NumComponenti) {
		$StartX=OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$this->DataSize[4]+$PhaseCounter*$this->CellHSp;
		$this->lastY = $this->TeamMatchNoPosition[0] + ($this->TeamMatchNoPosition[1]-$this->TeamMatchNoPosition[0]) /2;

		$this->SetLineWidth(0.1);
		$this->Line($StartX,$this->lastY,$StartX+$this->CellHSp,$this->lastY);
		$this->SetXY($StartX,$this->lastY-(2.5+($PhaseCounter==5 ? 0.5:0))*$this->CellVSp);
		$this->SetFont('','B');
		$this->Cell($this->CellHSp-5,$this->CellVSp,"GOLD",0,0,'L');
		$this->SetXY($StartX,$this->lastY-1.5*$this->CellVSp);
		$this->SetFont('','');
		if(!is_null($GoldCountry))
			$this->Cell($this->CellHSp,1.5*$this->CellVSp,$GoldCountry,0,0,'L');


		$this->lastY = $this->TeamMatchNoPosition[1] + (1.5+($PhaseCounter>2 ? ($PhaseCounter==5 ? 4.5:0.5):0)+$NumComponenti)*$this->CellVSp;

		$this->Line($StartX,$this->lastY,$StartX+$this->CellHSp,$this->lastY);
		$this->SetXY($StartX,$this->lastY-(2.5+($PhaseCounter==5 ? 0.5:0))*$this->CellVSp);
		$this->SetFont('','B');
		$this->Cell($this->CellHSp-5,$this->CellVSp,"SILVER",0,0,'L');
		$this->SetXY($StartX,$this->lastY-1.5*$this->CellVSp);
		$this->SetFont('','');
		if(!is_null($GoldCountry))
			$this->Cell($this->CellHSp,1.5*$this->CellVSp,$SilverCountry,0,0,'L');

		$this->lastY = $this->TeamMatchNoPosition[2] + ($this->TeamMatchNoPosition[3]-$this->TeamMatchNoPosition[2]) /2;

		$this->Line($StartX,$this->lastY,$StartX+$this->CellHSp,$this->lastY);
		$this->SetXY($StartX,$this->lastY-(2.5+($PhaseCounter==5 ? 0.5:0))*$this->CellVSp);
		$this->SetFont('','B');
		$this->Cell($this->CellHSp-5,$this->CellVSp,"BRONZE",0,0,'L');
		$this->SetXY($StartX,$this->lastY-1.5*$this->CellVSp);
		$this->SetFont('','');
		if(!is_null($GoldCountry))
			$this->Cell($this->CellHSp,1.5*$this->CellVSp,$BronzeCountry,0,0,'L');

	}

}