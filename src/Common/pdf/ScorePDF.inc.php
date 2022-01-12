<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once("Common/pdf/IanseoPdf.php");
require_once('Common/Lib/Fun_DateTime.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Sessions.inc.php');

class ScorePDF extends IanseoPdf {
	var $PrintLogo, $PrintHeader, $PrintDrawing, $PrintFlags, $PrintBarcode, $FillWithArrows=false, $PrintLineNo = true;

	/**
	 * If set to true adds a row with EnCode, Date of Birth and Email.
	 * @var bool [default: false]
	 */
	var $GetArcInfo=false;
	var $PrintTotalCols;
	var $Indices=array('bis','ter','quat', 'quin', 'sex', 'sept', 'oct');
	var $BottomImage=true;
    var $NoTensOnlyX = false;

	//Constructor
	function __construct($Portrait=true) {
		parent::__construct('Scorecard', $Portrait);
		$this->setPrintHeader(false);
		$this->setPrintFooter(false);
		$this->SetMargins(10,10,10);
		$this->SetAutoPageBreak(false, 10);
	    $this->PrintLogo = true;
	    $this->PrintHeader = true;
	    $this->PrintDrawing = true;
	    $this->PrintFlags = true;
		$this->PrintTotalCols = false;
		$this->PrintBarcode=false;
        $this->PrintLineNo = true;
		$this->SetSubject('Scorecard');
		$this->SetColors();
	}

	function Footer() {
		$this->SetDefaultColor();
		$this->SetFont($this->FontStd,'B',7);
		$this->SetXY(IanseoPdf::sideMargin,$this->h - 16);
		$this->multicell(0, 0, get_text('ScoreSingleWarning', 'Tournament'), 1, 'C', '', 1);
	}

	function SetColors($Datum=false, $Light=false) {

		if($this->PrintDrawing) {
			$this->SetTextColor(0x00, 0x00, 0x00);
			$this->SetDrawColor(0x33, 0x33, 0x33);
			if($Light)
				$this->SetFillColor(0xF8,0xF8,0xF8);
			else
				$this->SetFillColor(0xE8,0xE8,0xE8);
		} else {
			$this->SetDrawColor(0xFF, 0xFF, 0xFF);
			$this->SetFillColor(0xFF, 0xFF, 0xFF);
			if($Datum)
				$this->SetTextColor(0x00, 0x00, 0x00);
			else
				$this->SetTextColor(0xFF, 0xFF, 0xFF);
		}
	}


	function HideLogo() {
	    $this->PrintLogo = false;
	}

	function HideFlags() {
	    $this->PrintFlags = false;
	}

	function HideHeader() {
	    $this->PrintHeader = false;
	}

	function NoDrawing() {
	    $this->PrintDrawing = false;
	}

	function PrintTotalColumns() {
		$this->PrintTotalCols = true;
	}

	function NoLineNumbers() {
        $this->PrintLineNo = false;
    }

	function NoTensOnlyX() {
		$this->NoTensOnlyX = true;
		$this->setPrintFooter(true);
	}

//DRAW SCORE
	function DrawScore($TopX, $TopY, $Width, $Height, $NumEnd=0, $NumArrow=0, $Data=array(), $ArrValue="", $TournamentTotal="", $printGX=true) {
		global $CFG;
		static $ArrowEnds=array();

		// $ArrowEnds will contain the ends per arrows of each event and distance
		$Event=(empty($Data["Cat"]) || !trim($Data["Cat"]) ? '--' : $Data["Cat"]);
		$Session=(empty($Data["Session"]) ? '1' : $Data["Session"]);
		if(empty($ArrowEnds[$Event])) {
			$ArrowEnds[$Event]=getArrowEnds($Session);
		}

		$CurDist=(empty($Data["CurDist"]) ? 1 : $Data["CurDist"]);

		$NumEnd=$ArrowEnds[$Event][$CurDist]['ends'];
		$NumArrow=$ArrowEnds[$Event][$CurDist]['arrows'];
		if($NumArrow==6 and $NumEnd==6) {
			$NumArrow=3;
			$NumEnd=12;
		}

		//PARAMETRI CALCOLATI
		$TopOffset=30;
		$BottomImage=0;

		//HEADER LOGO SX & Dx
		$TmpLeft = 0;
		$TmpRight = 0;
		if($this->PrintLogo) {
			if(file_exists($IM=$this->ToPaths['ToLeft']) ) {
				$im=getimagesize($IM);
				$this->Image($IM, $TopX, $TopY, 0, ($TopOffset/2));
				$TmpLeft = (1 + ($im[0] * ($TopOffset/2) / $im[1]));
			}
			if(file_exists($IM=$this->ToPaths['ToRight']) ) {
				$im=getimagesize($IM);
				$TmpRight = ($im[0] * 15 / $im[1]);
				$this->Image($IM, ($TopX+$Width-$TmpRight), $TopY, 0, 15);
				$TmpRight++;
			}
			//IMMAGINE DEGLI SPONSOR
			// Sponsors disabled if QRCodes are to be printed!!!
			if($this->BottomImage and file_exists($IM=$this->ToPaths['ToBottom'])) {
				$BottomImage=7.5;
				$im=getimagesize($IM);
				$imgW = $Width;
				$imgH = $imgW * $im[1] / $im[0] ;
				if($imgH > $BottomImage) {
					$imgH = $BottomImage;
					$imgW = $imgH * $im[0] / $im[1] ;
				}
				$this->Image($IM, ($TopX+($Width-$imgW)/2), ($TopY+$Height-$imgH), $imgW, $imgH);
			}
		}

		$CellW = ($Width / ($NumArrow+5));
		$ExtraRows=3;
		if($this->NoTensOnlyX) {
			$ExtraRows=(2+($Data['CurDist'] ? $Data['CurDist'] : 1)) ;
		}

		$CellH = min(10,($Height-41-$BottomImage-4*intval($this->GetArcInfo))/($NumEnd + $ExtraRows));
        $NumDist = 0;

		//TESTATA GARA
		if($this->PrintHeader) {
			$tmpPad=$this->getCellPaddings();
			$this->SetCellPadding(0);
			$this->SetColors(true);
	    	$this->SetFont($this->FontStd,'B',9);
			$this->SetXY($TopX+$TmpLeft,$TopY);
			$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Name, 0, 'L', 0);
    		$this->SetFont($this->FontStd,'',7);
			$this->SetXY($TopX+$TmpLeft, $this->GetY());
			if($this->GetStringWidth($this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT))>=$Width-$TmpLeft-$TmpRight) {
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Where, 0, 'L', 0);
				$this->SetXY($TopX+$TmpLeft, $this->GetY());
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			} else {
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			}
			$this->SetCellPaddings($tmpPad['L'], $tmpPad['T'], $tmpPad['R'], $tmpPad['B']);
		}

		//DATI ATLETA
		$FlagOffset=0.2*$CellW;
		$this->SetXY($FlagOffset+$TopX+0.2*$CellW, $TopY+($TopOffset*7/12));
		if($this->PrintFlags and !empty($Data['CoCode'])) {
			if(is_file($file= $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$Data['CoCode'].'.jpg')) {
				$H=$TopOffset*3/8;
				$W=$H*3/2;
				$OrgY=$this->gety();
				@$this->Image($file, $TopX, $this->gety(), $W, $H, 'JPG', '', '', true, 300, '', false, false, 1, true);
				$FlagOffset=$W+1;
			}
		}

		$this->SetXY($FlagOffset+$TopX, $TopY+($TopOffset*7/12));
		$this->SetFont($this->FontStd,'',8);
		$this->SetColors(false);
		$ArcherStringLength=$this->GetStringWidth((get_text('Archer') . ": "));
		$this->Cell($ArcherStringLength,$TopOffset/6, (get_text('Archer') . ": "),'B',0,'L',0);
		$this->SetY($this->gety()-2, false);
		$this->SetFont($this->FontStd,'B',13);
		$this->SetColors(true);
		$this->Cell($Width-(($this->PrintTotalCols && empty($Data["FirstDist"])) ? 2.7*$CellW : 1.6*$CellW)-$ArcherStringLength - $FlagOffset,2+($TopOffset/6), (array_key_exists("Ath",$Data) ? $Data["Ath"] : ' '),'B',0,'L',0);
		$this->SetXY($FlagOffset+$TopX, $TopY+($TopOffset*19/24));
		$this->SetFont($this->FontStd,'',8);

		// Country
		$this->SetColors(false);
		$CountryWidth=$this->GetStringWidth((get_text('Country') . ": "));
		$this->Cell($CountryWidth, $TopOffset/6, (get_text('Country') . ": "),'B',0,'L',0);
		$this->SetFont($this->FontStd,'B',8);
		$this->SetColors(true);
		$CellTmpWidth=$Width-(($this->PrintTotalCols && empty($Data["FirstDist"])) ? 2.7*$CellW : 1.6*$CellW)-$CountryWidth - $FlagOffset;
		if(array_key_exists("Noc",$Data)) {
			$str=$Data['CoCode'].' -';
			$strW=$this->GetStringWidth($str);
			$this->Cell($strW, $TopOffset/6, $str,'B',0,'L',0);
			$this->Cell($CellTmpWidth-$strW, $TopOffset/6, $Data['CoName'],'B',0,'L',0);
		} else {
			$this->Cell($CellTmpWidth,$TopOffset/6, ' ','B',0,'L',0);
		}

		//PAGLIONE
		$this->SetXY($TopX+$Width-(1.4*$CellW), $TopY+($TopOffset*13/24));
		$this->SetFont($this->FontStd,'B',20);
		$this->SetColors(true);
		$this->Cell((1.4*$CellW),$TopOffset*7/24,(array_key_exists("tNo",$Data) ? ltrim($Data["tNo"],'0') : ' '),0,0,'R',1);
		$this->SetXY($TopX+$Width-(1.4*$CellW), $TopY+($TopOffset*10/12));
		$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(true);
		$this->Cell((1.4*$CellW),$TopOffset*2/12,(array_key_exists("Cat",$Data) ? $Data["Cat"] : ' '),'T',0,'C',1);
		if($this->PrintTotalCols && empty($Data["FirstDist"]))
		{
			$this->SetFont($this->FontStd,'B',8);
			$this->SetFillColor(0xFF,0xE8,0xE8);
			$this->SetXY($TopX+$Width-(2.5*$CellW), $TopY+($TopOffset*16/24));
			$this->Cell((1.1*$CellW),$TopOffset*4/24,get_text('Total'),1,0,'C',1);
			$this->SetXY($TopX+$Width-(2.5*$CellW), $TopY+($TopOffset*20/24));
			$this->Cell((1.1*$CellW),$TopOffset*4/24,($ArrValue == "" ? '' : $TournamentTotal),1,0,'C',1);
		}

		//HEADER DELLO SCORE
		$ArCellW=($this->PrintTotalCols ? 0.9 : 1)*$CellW;
		$EndNumCellW=0.8*$CellW;
		$TotalCellW=($this->PrintTotalCols ? 1 : 1.4)*$CellW;
		$XNineW=(($this->NoTensOnlyX or $this->prnGolds==$this->prnXNine) ? 1.4 : 0.7)*$CellW;

		if($this->NoTensOnlyX) {
			$TopOffset+=$CellH/2;
			if(!empty($Data['CurDist']) and $Data['CurDist']>1) {
				// prints previous distances
				for($i=1; $i<$Data['CurDist']; $i++) {
					$this->SetXY($TopX + $ArCellW*$NumArrow, $TopY+$TopOffset);
					$this->Cell($TotalCellW + $EndNumCellW, $CellH, get_text('FlightsDistTotal', 'Tournament', $Data['D'.$i]),0,0,'R',0);
					$this->Cell($TotalCellW, $CellH,$Data['QuD'.$i],1,0,'C',0);
					$this->Cell($XNineW, $CellH,$Data['QuXD'.$i],1,1,'C',0);
					$TopOffset+=$CellH;
				}
			}
		}

		$this->SetXY($TopX, $TopY+$TopOffset);
	   	$this->SetFont($this->FontStd,'I',8);
		$this->SetFillColor(0xF8,0xF8,0xF8);
		$this->SetColors(true,true);
        $this->Cell($EndNumCellW, $CellH, (array_key_exists("D1", $Data) ? $Data["D1"] : ' '), 0, 0, 'C', (array_key_exists("D1", $Data) ? 1 : 0));
        $this->Cell($EndNumCellW, $CellH, (array_key_exists("Dist", $Data) ? $Data["Dist"] : ' '), 0, 0, 'C', (array_key_exists("Dist", $Data) ? 1 : 0));
		$this->SetFillColor(0xE8,0xE8,0xE8);
	   	$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(false);
		for($j=1; $j<=$NumArrow; $j++) {
			$this->Cell($ArCellW, $CellH, $j, 1, 0, 'C', 1);
		}
	   	$this->SetFont($this->FontStd,'B',8);
		$this->Cell($TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1), $CellH, (get_text('TotalProg','Tournament')),1,0,'C',1);
		$this->Cell($TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 5/4 : 1), $CellH, (get_text('TotalShort','Tournament')),1,0,'C',1);
		if($this->PrintTotalCols) {
			$this->SetFillColor(0xFF,0xE8,0xE8);
			$this->Cell($CellW*1.1,$CellH, (get_text('Total')),1,0,'C',1);
			$this->SetFillColor(0xE8,0xE8,0xE8);
		}
		if(!($this->NoTensOnlyX or $this->prnGolds==$this->prnXNine)) {
			$this->Cell($XNineW,$CellH, ($this->prnGolds),1,0,'C',1);
		}
		$this->Cell($XNineW,$CellH, ($this->prnXNine),1,1,'C',1);

// 		DISTANZA => $Data["CurDist"];
		//RIGHE DELLO SCORE
		$ScoreMultiLineTotal = 0;
		$ScoreTotal = 0;
		$ScoreGold = 0;
		$ScoreXnine = 0;
		$StartCell=true;
		$End=1;
		$HeighEndCell=$CellH*($NumEnd/$ArrowEnds[$Event][$CurDist]['ends']);
		for($i=1; $i<=$NumEnd; $i++) {
		   	$this->SetFont($this->FontStd,'B',10);
			$this->SetXY($TopX, $TopY+$TopOffset+$CellH*$i);
			if($StartCell) {
				$this->Cell($EndNumCellW, $HeighEndCell, $End++, 1, 0, 'C', 1);
			} else {
				$this->SetX($TopX+$EndNumCellW);
			}
			$this->SetFont($this->FontStd,'',9);
			for($j=0; $j<$NumArrow; $j++) {
				$this->Cell($ArCellW,$CellH,($ArrValue == "" ? '' : DecodeFromLetter(substr($ArrValue,($i-1)*$NumArrow+$j,1))), 1, 0, 'C', 0);
			}
			list($ScoreEndTotal,$ScoreEndGold, $ScoreEndXnine) = ValutaArrowStringGX(substr($ArrValue,($i-1)*$NumArrow,$NumArrow),$this->goldsChars,$this->xNineChars);
			$ScoreMultiLineTotal += $ScoreEndTotal;
			$ScoreTotal += $ScoreEndTotal;
			$ScoreGold += $ScoreEndGold;
			$ScoreXnine += $ScoreEndXnine;
			$this->Cell($TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1), $CellH,($ArrValue == "" ? '' : $ScoreEndTotal),1,0,'C',0);
			$this->SetFont($this->FontStd,'',10);
			if(($NumArrow*$i)%$ArrowEnds[$Event][$CurDist]['arrows']) {
				$this->Cell($TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 5/4 : 1), $CellH,'',1,0,'C',0);
				$this->Line($x1=$this->getX(), $y1=$this->getY(), $x1-$TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 5/4 : 1), $y1+$CellH);
				$this->Line($x1-$TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 5/4 : 1), $y1, $x1, $y1+$CellH);
				$StartCell=false;
			} else {
				if($ArrowEnds[$Event][$CurDist]['arrows']>5) {
					$this->Cell($TotalCellW*2/4, $CellH,($ArrValue == "" ? '' : $ScoreMultiLineTotal),1,0,'C',0);
					$this->Cell($TotalCellW*3/4, $CellH,($ArrValue == "" ? '' : $ScoreTotal),1,0,'C',0);
					$ScoreMultiLineTotal = 0;
				} else {
					$this->Cell($TotalCellW, $CellH,($ArrValue == "" ? '' : $ScoreTotal),1,0,'C',0);
				}
				$StartCell=true;
			}

			if($this->PrintTotalCols) {
				$this->SetFillColor(0xFF,0xE8,0xE8);
				$this->SetFont($this->FontStd,'',9);
				if(($NumArrow*$i)%$ArrowEnds[$Event][$CurDist]['arrows']) {
					$this->Cell(1.1*$CellW,$CellH,'',1,0,'C',1);
					$this->Line($x1=$this->getX(), $y1=$this->getY(), $x1-(1.1*$CellW), $y1+$CellH);
					$this->Line($x1-(1.1*$CellW), $y1, $x1, $y1+$CellH);
				} else {
					$this->Cell(1.1*$CellW,$CellH,($ArrValue == "" ? '' : $ScoreTotal + $TournamentTotal),1,0,'C',1);
					if(!empty($Data["FirstDist"]) && $ArrValue == "") {
						$this->Line($this->GetX(),$this->GetY(),$this->GetX()-1.1*$CellW,$this->GetY()+$CellH);
						$this->Line($this->GetX(),$this->GetY()+$CellH,$this->GetX()-1.1*$CellW,$this->GetY());
					}
				}
				$this->SetFillColor(0xE8,0xE8,0xE8);
			}
			$this->SetFont($this->FontStd,'',8);
			if(!($this->NoTensOnlyX or $this->prnGolds==$this->prnXNine)) {
				$this->Cell($XNineW, $CellH,($ArrValue == "" || !$ScoreEndGold ? '' : $ScoreEndGold),1,0,'C',0);
			}
			$this->Cell($XNineW, $CellH,($ArrValue == "" || !$ScoreEndXnine ? '' : $ScoreEndXnine),1,1,'C',0);
		}

		// CODICE A BARRE
		$BCode=0;
		if($this->PrintBarcode and !empty($Data['EnCode'])) {
			$this->SetXY($TopX-2, $TopY+$TopOffset+$CellH*($NumEnd +1)-1);
			$this->SetFont('barcode','',22);
			$BCode=($NumArrow+($this->PrintTotalCols ? 0.6 : 1.3))*$CellW;
			if($Data['EnCode'][0]=='_') $Data['EnCode']='UU'.substr($Data['EnCode'], 1);
			$this->Cell($BCode +3, $CellH, mb_convert_encoding('*' . $Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'] . (array_key_exists("CurDist",$Data) ? '-'.$Data["CurDist"] : ''), "UTF-8","cp1252") . "*",0,0,'C',0);
// 			$this->write1DBarcode(mb_convert_encoding('*' . $Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'] . (array_key_exists("CurDist",$Data) ? '-'.$Data["CurDist"] : ''), "UTF-8","cp1252") . "*",
// 					'C39E', $TopX, $TopY+$TopOffset+$CellH*($NumEnd+1)+1, $BCode, $CellH-1);//, (float) $xres, (array) $style, (string) $align);

			$this->SetFont($this->FontStd,'',7);
			$this->SetXY($TopX-2, $TopY+$TopOffset+$CellH*($NumEnd +2));
			$this->Cell($BCode +3, $CellH, mb_convert_encoding($Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'] . (array_key_exists("CurDist",$Data) ? '-'.$Data["CurDist"] : ''), "UTF-8","cp1252"),0,0,'C',0);
		}


		//TOTALE DELLO SCORE
		$ErScoreTotal = empty($Data['CurDist']) ? '' : ($ArrValue and $Data["QuD{$Data['CurDist']}"]!=$ScoreTotal);
		$ErScoreGold  = empty($Data['CurDist']) ? '' : ($Data["QuGD{$Data['CurDist']}"]!=$ScoreGold);
		$ErScoreXNine = empty($Data['CurDist']) ? '' : ($Data["QuXD{$Data['CurDist']}"]!=$ScoreXnine);

		$this->SetXY($TopX + $BCode, $TopY+$TopOffset+$CellH*($NumEnd +1));
	   	$this->SetFont($this->FontStd,'B',11);
		$this->Cell(($NumArrow+($this->PrintTotalCols ? 1.5 : 2.2))*$CellW - $BCode + ($ArrowEnds[$Event][$CurDist]['arrows']>5 ? $TotalCellW/4 : 0),$CellH, (get_text('Total') . " "),0,0,'R',0);
		$this->Cell($TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1),$CellH,($ArrValue == "" ? '' : $ScoreTotal),1,0,'C',0);
		if($this->FillWithArrows && $ErScoreTotal)
			$this->Line($x1 = $this->getx() - (($this->PrintTotalCols ? 1 : 1.4)*$CellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1)), $y1=$this->gety()+$CellH, $x1+($this->PrintTotalCols ? 1 : 1.4)*$CellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1), $y1-$CellH);
		if($this->PrintTotalCols) {
			$this->SetFillColor(0xFF,0xE8,0xE8);
			$this->Cell(1.1*$CellW,$CellH,($ArrValue == "" ? '' : $ScoreTotal + $TournamentTotal),1,0,'C',1);
			$this->SetFillColor(0xE8,0xE8,0xE8);
		}
		$this->SetFont($this->FontStd,'B',9);
		if(!($this->NoTensOnlyX or $this->prnGolds==$this->prnXNine)) {
			$this->Cell($XNineW,$CellH,($ArrValue == "" ? '' : $ScoreGold),1,0,'C',0);
			if($this->FillWithArrows && $ErScoreGold) {
				$this->Line($x1 = $this->getx() - 0.7*$CellW, $y1=$this->gety()+$CellH, $x1+0.7*$CellW, $y1-$CellH);
			}
		}
		$this->Cell($XNineW,$CellH,($ArrValue == "" ? '' : $ScoreXnine),1,0,'C',0);
		if($this->FillWithArrows && $ErScoreXNine) {
			$this->Line($x1 = $this->getx() - 0.7*$CellW, $y1=$this->gety()+$CellH, $x1+0.7*$CellW, $y1-$CellH);
		}

		if(($this->NoTensOnlyX) and !empty($Data['CurDist']) and $Data['CurDist']>1) {
			$this->ln();
			// prints Grand Total
			$Tot=0;
			$TotX=0;
			for($i=1; $i<=$Data['CurDist']; $i++) {
				$Tot+=$Data['QuD'.$i];
				$TotX+=$Data['QuXD'.$i];
			}
			$this->SetX($TopX + $ArCellW*$NumArrow);
			$this->Cell($TotalCellW + $EndNumCellW, $CellH, get_text('RunningTotal', 'Tournament', $Data['D'.$i]),0,0,'R',0);
			$this->Cell($TotalCellW, $CellH, $this->FillWithArrows ? $Tot : '',1,0,'C',0);
			$this->Cell($XNineW, $CellH, $this->FillWithArrows ? $TotX : '',1,1,'C',0);
		}

		$this->ln($CellH/2);

		if($this->FillWithArrows && ($ErScoreTotal or $ErScoreGold or $ErScoreXNine)) {
            $this->ln($CellH/2);
			$this->SetX($TopX + $BCode);
		   	$this->SetFont($this->FontStd,'B',11);
			$this->Cell(($NumArrow+($this->PrintTotalCols ? 1.5 : 2.2))*$CellW - $BCode + ($ArrowEnds[$Event][$CurDist]['arrows']>5 ? $TotalCellW/4 : 0),$CellH, (get_text('SignedTotal', 'Tournament') . " "),0,0,'R',0);
			$this->Cell($TotalCellW * ($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1),$CellH, $Data["QuD{$Data['CurDist']}"] ,1,0,'C',0);
			if($this->PrintTotalCols)
			{
				$this->Cell(1.1*$CellW,$CellH,'',0,0,'C',0);
			}
			$this->SetFont($this->FontStd,'B',9);
			if(!($this->NoTensOnlyX or $this->prnGolds==$this->prnXNine)) {
				$this->Cell($XNineW,$CellH, $Data["QuGD{$Data['CurDist']}"] ,1,0,'C',0);
			}
			$this->Cell($XNineW,$CellH, $Data["QuXD{$Data['CurDist']}"] ,1,0,'C',0);
		}

		// Collect Dob and Email
		if($this->GetArcInfo) {
			$this->SetXY($TopX, $this->GetY()+10);
			$this->SetFont($this->FontFix,'BI',6);

			$this->Cell($CellW*0.75, 3, $Data['EnCode'], 0, 0, 'L', 0, '', 1, false, 'T', 'B');
			$this->Cell($CellW*1.5, 3, get_text('DOB', 'Tournament'), 0, 0, 'R', 0, '', 1, false, 'T', 'B');
			$this->Cell($CellW*1.5, 3, $Data['DoB'], 'B', 0, 'L', 0, '', 1, false, 'T', 'B');
			$this->Cell($CellW, 3, get_text('Email', 'Tournament'), 0, 0, 'R', 0, '', 1, false, 'T', 'B');
			$this->Cell($Width-4.75*$CellW, 3, $Data['Email'], 'B', 0, 'L', 0, '', 1, false, 'T', 'B');
			$this->ln($CellH/2);
		}

		//FIRME
		if(!$this->FillWithArrows) {
			$SignY=$TopY+$Height-($BottomImage+3);
			$this->Line($TopX+4, $SignY, $TopX+($Width/2)-3 , $SignY);
			$this->Line($TopX+($Width/2)+3, $SignY, $TopX+$Width-4 , $SignY);
			$this->SetFont($this->FontFix,'BI',6);
			$this->SetXY($TopX, $SignY);
			$this->Cell(4,3,'',0,0,'C',0);
			$this->Cell(($Width/2)-7,3,(get_text('Archer')),0,0,'C',0);
			$this->Cell(6,3,'',0,0,'C',0);
			$this->Cell(($Width/2)-7,3,(get_text('Scorer')),0,0,'C',0);
			$this->Cell(4,3,'',0,0,'C',0);
		}
		//$this->Rect($TopX, $TopY, $Width, $Height);
	}

// NEW DRAW SCORE
	function DrawScoreNew($TopX, $TopY, $Width, $Height, $Distance=0, $Data=array('Session'=>1)) {
		global $CFG;
		static $ArrowEnds=array(), $StdFont, $StdFontSmall, $StdFontMedium;

		// $ArrowEnds will contain the ends per arrows of each event and distance
		$Event=(empty($Data["Cat"]) || !trim($Data["Cat"]) ? '--' : $Data["Cat"]);
		$Session=(empty($Data["Session"]) ? '1' : $Data["Session"]);
		if(empty($ArrowEnds[$Event])) {
			$ArrowEnds[$Event]=getArrowEnds($Session);
		}

		$CurDist=(empty($Distance) ? 1 : $Distance);
		$FirstDist=($Distance==1);

		$NumEnd=$ArrowEnds[$Event][$CurDist]['ends'];
		$NumArrow=$ArrowEnds[$Event][$CurDist]['arrows'];
		if($NumArrow==6 and $NumEnd==6) {
			$NumArrow=3;
			$NumEnd=12;
		} elseif($NumArrow==6 and $NumEnd==5) {
			$NumArrow=3;
			$NumEnd=10;
		}
		if($NumArrow==1 AND ($NumEnd%3)==0) {
			$NumArrow=3;
			$NumEnd=$NumEnd/3;
		} else if($NumArrow==1 AND ($NumEnd%5)==0) {
			$NumArrow=5;
			$NumEnd=$NumEnd/5;
		}

		//PARAMETRI CALCOLATI
		$TopOffset=30;
		$BottomImage=0;

		//HEADER LOGO SX & Dx
		$TmpLeft = 0;
		$TmpRight = 0;
		if($this->PrintLogo) {
			if(file_exists($IM=$this->ToPaths['ToLeft']) ) {
				$im=getimagesize($IM);
				$this->Image($IM, $TopX, $TopY, 0, ($TopOffset/2));
				$TmpLeft = (1 + ($im[0] * ($TopOffset/2) / $im[1]));
			}
			if(file_exists($IM=$this->ToPaths['ToRight']) ) {
				$im=getimagesize($IM);
				$TmpRight = ($im[0] * 15 / $im[1]);
				$this->Image($IM, ($TopX+$Width-$TmpRight), $TopY, 0, 15);
				$TmpRight++;
			}
			//IMMAGINE DEGLI SPONSOR
			// Sponsors disabled if QRCodes are to be printed!!!
			if($this->BottomImage and file_exists($IM=$this->ToPaths['ToBottom'])) {
				$BottomImage=7.5;
				$im=getimagesize($IM);
				$imgW = $Width;
				$imgH = $imgW * $im[1] / $im[0] ;
				if($imgH > $BottomImage) {
					$imgH = $BottomImage;
					$imgW = $imgH * $im[0] / $im[1] ;
				}
				$this->Image($IM, ($TopX+($Width-$imgW)/2), ($TopY+(empty($Data['SecondScorer']) ? 0 : 25)+$Height-$imgH), $imgW, $imgH);
			}
		}

		$CellW = ($Width / ($NumArrow+5));
		$ExtraRows=3;
		if($this->NoTensOnlyX) {
			$ExtraRows=(2+($Distance ? $Distance : 1)) ;
		}

		$CellH = min(10,($Height-41-$BottomImage-4*intval($this->GetArcInfo))/($NumEnd + $ExtraRows));
		if(empty($StdFont)) {
			$StdFont=min(10, $CellH*2);
			$StdFontSmall=$StdFont*.8;
			$StdFontMedium=$StdFont*.9;
		}
        $NumDist = 0;

		//TESTATA GARA
		if($this->PrintHeader) {
			$tmpPad=$this->getCellPaddings();
			$this->SetCellPadding(0);
			$this->SetColors(true);
	    	$this->SetFont($this->FontStd,'B',9);
			$this->SetXY($TopX+$TmpLeft,$TopY);
			$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Name, 0, 'L', 0);
    		$this->SetFont($this->FontStd,'',7);
			$this->SetXY($TopX+$TmpLeft, $this->GetY());
			if($this->GetStringWidth($this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT))>=$Width-$TmpLeft-$TmpRight) {
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Where, 0, 'L', 0);
				$this->SetXY($TopX+$TmpLeft, $this->GetY());
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			} else {
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			}
			$this->SetCellPaddings($tmpPad['L'], $tmpPad['T'], $tmpPad['R'], $tmpPad['B']);
		}

		//DATI ATLETA
		$FlagOffset=0.2*$CellW;
		$this->SetXY($FlagOffset+$TopX+0.2*$CellW, $TopY+($TopOffset*7/12));
		if($this->PrintFlags and !empty($Data['CoCode'])) {
			if(is_file($file= $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$Data['CoCode'].'.jpg')) {
				$H=$TopOffset*3/8;
				$W=$H*3/2;
				$OrgY=$this->gety();
				@$this->Image($file, $TopX, $this->gety(), $W, $H, 'JPG', '', '', true, 300, '', false, false, 1, true);
				$FlagOffset=$W+1;
			}
		}

		$this->SetXY($FlagOffset+$TopX, $TopY+($TopOffset*7/12));
		$this->SetFont($this->FontStd,'',8);
		$this->SetColors(false);
		$ArcherStringLength=$this->GetStringWidth((get_text('Archer') . ": "));
		$this->Cell($ArcherStringLength,$TopOffset/6, (get_text('Archer') . ": "),'B',0,'L',0);
		$this->SetY($this->gety()-2, false);
		$this->SetFont($this->FontStd,'B',13);
		$this->SetColors(true);
		$this->Cell($Width-(($this->PrintTotalCols && empty($FirstDist)) ? 2.7*$CellW : 1.6*$CellW)-$ArcherStringLength - $FlagOffset,2+($TopOffset/6), (array_key_exists("Ath",$Data) ? $Data["Ath"] : ' '),'B',0,'L',0);
		$this->SetXY($FlagOffset+$TopX, $TopY+($TopOffset*19/24));
		$this->SetFont($this->FontStd,'',8);

		// Country
		$this->SetColors(false);
		$CountryWidth=$this->GetStringWidth((get_text('Country') . ": "));
		$this->Cell($CountryWidth, $TopOffset/6, (get_text('Country') . ": "),'B',0,'L',0);
		$this->SetFont($this->FontStd,'B',8);
		$this->SetColors(true);
		$CellTmpWidth=$Width-(($this->PrintTotalCols && empty($FirstDist)) ? 2.7*$CellW : 1.6*$CellW)-$CountryWidth - $FlagOffset;
		if(array_key_exists("Noc",$Data)) {
			$str=$Data['CoCode'].' -';
			$strW=$this->GetStringWidth($str);
			$this->Cell($strW, $TopOffset/6, $str,'B',0,'L',0);
			$this->Cell($CellTmpWidth-$strW, $TopOffset/6, $Data['CoName'],'B',0,'L',0);
		} else {
			$this->Cell($CellTmpWidth,$TopOffset/6, ' ','B',0,'L',0);
		}

		//PAGLIONE
		$this->SetXY($TopX+$Width-(1.4*$CellW), $TopY+($TopOffset*13/24));
		$this->SetFont($this->FontStd,'B',20);
		$this->SetColors(true);
		$this->Cell((1.4*$CellW),$TopOffset*7/24,(array_key_exists("tNo",$Data) ? ltrim($Data["tNo"],'0') : ' '),0,0,'R',1);
		$this->SetXY($TopX+$Width-(1.4*$CellW), $TopY+($TopOffset*10/12));
		$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(true);
		$this->Cell((1.4*$CellW),$TopOffset*2/12,(array_key_exists("Cat",$Data) ? $Data["Cat"] : ' '),'T',0,'C',1);
		if($this->PrintTotalCols && empty($FirstDist))
		{
			$this->SetFont($this->FontStd,'B',8);
			$this->SetFillColor(0xFF,0xE8,0xE8);
			$this->SetXY($TopX+$Width-(2.5*$CellW), $TopY+($TopOffset*16/24));
			$this->Cell((1.1*$CellW),$TopOffset*4/24,get_text('Total'),1,0,'C',1);
			$this->SetXY($TopX+$Width-(2.5*$CellW), $TopY+($TopOffset*20/24));
			$this->Cell((1.1*$CellW),$TopOffset*4/24,($Data['Arr'.$Distance] == "" ? '' : $Data['Tot'.$Distance]),1,0,'C',1);
			$this->SetFont($this->FontStd,'B',10);
		}

		//HEADER DELLO SCORE
		$ArCellW=($this->PrintTotalCols ? 0.9 : 1)*$CellW;
		$EndNumCellW=0.8*$CellW;
		$TotalCellW=($this->PrintTotalCols ? 1 : 1.4)*$CellW;
		$XNineW=(($this->NoTensOnlyX or $this->prnGolds==$this->prnXNine) ? 1.4 : 0.7)*$CellW;

		if($this->NoTensOnlyX) {
			$TopOffset+=$CellH/2;
			if(!empty($Distance) and $Distance>1) {
				// prints previous distances
				for($i=1; $i<$Distance; $i++) {
					$this->SetXY($TopX + $ArCellW*$NumArrow, $TopY+$TopOffset);
					$this->Cell($TotalCellW + $EndNumCellW, $CellH, get_text('FlightsDistTotal', 'Tournament', $Data['D'.$i]),0,0,'R',0);
					$this->Cell($TotalCellW, $CellH,$Data['QuD'.$i],1,0,'C',0);
					$this->Cell($XNineW, $CellH,$Data['QuXD'.$i],1,1,'C',0);
					$TopOffset+=$CellH;
				}
			}
		}

		$this->SetXY($TopX, $TopY+$TopOffset);
	   	$this->SetFont($this->FontStd,'I',8);
		$this->SetFillColor(0xF8,0xF8,0xF8);
		$this->SetColors(true,true);
        $this->Cell($EndNumCellW, $CellH, $Data["D".$Distance], 0, 0, 'C', 1);
		$this->SetFillColor(0xE8,0xE8,0xE8);
	   	$this->SetFont($this->FontStd,'B',$StdFont);
		$this->SetColors(false);
		for($j=1; $j<=$NumArrow; $j++) {
			$this->Cell($ArCellW, $CellH, $j, 1, 0, 'C', 1);
		}
	   	$this->SetFont($this->FontStd,'B', $StdFontSmall);
		$this->Cell($TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1), $CellH, (get_text('TotalProg','Tournament')),1,0,'C',1);
		$this->Cell($TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 5/4 : 1), $CellH, (get_text('TotalShort','Tournament')),1,0,'C',1);
		if($this->PrintTotalCols) {
			$this->SetFillColor(0xFF,0xE8,0xE8);
			$this->Cell($CellW*1.1,$CellH, (get_text('Total')),1,0,'C',1);
			$this->SetFillColor(0xE8,0xE8,0xE8);
		}
		if(!($this->NoTensOnlyX or $this->prnGolds==$this->prnXNine)) {
			$this->Cell($XNineW,$CellH, ($this->prnGolds),1,0,'C',1);
		}
		$this->Cell($XNineW,$CellH, ($this->prnXNine),1,1,'C',1);

// 		DISTANZA => $Data["CurDist"];
		//RIGHE DELLO SCORE
		$ScoreMultiLineTotal = 0;
		$ScoreTotal = 0;
		$ScoreGold = 0;
		$ScoreXnine = 0;
		$StartCell=true;
		$End=1;
		$HeighEndCell=$CellH*($NumEnd/$ArrowEnds[$Event][$CurDist]['ends']);
		for($i=1; $i<=$NumEnd; $i++) {
		   	$this->SetFont($this->FontStd,'B',$StdFont);
			$this->SetXY($TopX, $TopY+$TopOffset+$CellH*$i);
			if($StartCell) {
				$this->Cell($EndNumCellW, $HeighEndCell, $End++, 1, 0, 'C', 1);
			} else {
				$this->SetX($TopX+$EndNumCellW);
			}
			$this->SetFont($this->FontStd,'',$StdFontMedium);
			if($Data['Arr'.$Distance]) {
				for($j=0; $j<$NumArrow; $j++) {
					$this->Cell($ArCellW,$CellH,DecodeFromLetter(substr($Data['Arr'.$Distance],($i-1)*$NumArrow+$j,1)), 1, 0, 'C', 0);
				}
			} else {
				for($j=0; $j<$NumArrow; $j++) {
					$this->Cell($ArCellW,$CellH,'', 1, 0, 'C', 0);
				}
			}
			list($ScoreEndTotal,$ScoreEndGold, $ScoreEndXnine) = ValutaArrowStringGX(substr($Data['Arr'.$Distance],($i-1)*$NumArrow,$NumArrow),$this->goldsChars,$this->xNineChars);
			$ScoreMultiLineTotal += $ScoreEndTotal;
			$ScoreTotal += $ScoreEndTotal;
			$ScoreGold += $ScoreEndGold;
			$ScoreXnine += $ScoreEndXnine;
			$this->Cell($TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1), $CellH,($Data['Arr'.$Distance] == "" ? '' : $ScoreEndTotal),1,0,'C',0);
			$this->SetFont($this->FontStd,'',$StdFont);
			if(($NumArrow*$i)%$ArrowEnds[$Event][$CurDist]['arrows']) {
				$this->Cell($TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 5/4 : 1), $CellH,'',1,0,'C',0);
				$this->Line($x1=$this->getX(), $y1=$this->getY(), $x1-$TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 5/4 : 1), $y1+$CellH);
				$this->Line($x1-$TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 5/4 : 1), $y1, $x1, $y1+$CellH);
				$StartCell=false;
			} else {
				if($ArrowEnds[$Event][$CurDist]['arrows']>5) {
					$this->Cell($TotalCellW*2/4, $CellH,($Data['Arr'.$Distance] == "" ? '' : $ScoreMultiLineTotal),1,0,'C',0);
					$this->Cell($TotalCellW*3/4, $CellH,($Data['Arr'.$Distance] == "" ? '' : $ScoreTotal),1,0,'C',0);
					$ScoreMultiLineTotal = 0;
				} else {
					$this->Cell($TotalCellW, $CellH,($Data['Arr'.$Distance] == "" ? '' : $ScoreTotal),1,0,'C',0);
				}
				$StartCell=true;
			}

			if($this->PrintTotalCols) {
				$this->SetFillColor(0xFF,0xE8,0xE8);
				$this->SetFont($this->FontStd,'',$StdFontMedium);
				if(($NumArrow*$i)%$ArrowEnds[$Event][$CurDist]['arrows']) {
					$this->Cell(1.1*$CellW,$CellH,'',1,0,'C',1);
					$this->Line($x1=$this->getX(), $y1=$this->getY(), $x1-(1.1*$CellW), $y1+$CellH);
					$this->Line($x1-(1.1*$CellW), $y1, $x1, $y1+$CellH);
				} else {
					$this->Cell(1.1*$CellW,$CellH,($Data['Arr'.$Distance] == "" ? '' : $ScoreTotal + $Data['Tot'.$Distance]),1,0,'C',1);
					if(!empty($FirstDist) && $Data['Arr'.$Distance] == "") {
						$this->Line($this->GetX(),$this->GetY(),$this->GetX()-1.1*$CellW,$this->GetY()+$CellH);
						$this->Line($this->GetX(),$this->GetY()+$CellH,$this->GetX()-1.1*$CellW,$this->GetY());
					}
				}
				$this->SetFillColor(0xE8,0xE8,0xE8);
			}
			$this->SetFont($this->FontStd,'',$StdFontSmall);
			if(!($this->NoTensOnlyX or $this->prnGolds==$this->prnXNine)) {
				$this->Cell($XNineW, $CellH,($Data['Arr'.$Distance] == "" || !$ScoreEndGold ? '' : $ScoreEndGold),1,0,'C',0);
			}
			$this->Cell($XNineW, $CellH,($Data['Arr'.$Distance] == "" || !$ScoreEndXnine ? '' : $ScoreEndXnine),1,1,'C',0);
		}

		// CODICE A BARRE
		$BCode=0;
		if($this->PrintBarcode and !empty($Data['EnCode'])) {
			$this->SetXY($TopX-2, $TopY+$TopOffset+$CellH*($NumEnd+1)-1);
			$this->SetFont('barcode','',22);
			$BCode=($NumArrow+($this->PrintTotalCols ? 0.6 : 1.3))*$CellW;
			if($Data['EnCode'][0]=='_') $Data['EnCode']='UU'.substr($Data['EnCode'], 1);
			$this->Cell($BCode +3, $CellH, mb_convert_encoding('*' . $Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'] . ($Distance ? '-'.$Distance : ''), "UTF-8","cp1252") . "*",0,0,'C',0);
// 			$this->write1DBarcode(mb_convert_encoding('*' . $Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'] . (array_key_exists("CurDist",$Data) ? '-'.$Data["CurDist"] : ''), "UTF-8","cp1252") . "*",
// 					'C39E', $TopX, $TopY+$TopOffset+$CellH*($NumEnd+1)+1, $BCode, $CellH-1);//, (float) $xres, (array) $style, (string) $align);

			$this->SetFont($this->FontStd,'',7);
			$this->ln();
			$this->SetXY($TopX-2, $this->GetY()-2);
			$this->Cell($BCode +3, $CellH, mb_convert_encoding($Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'] . ($Distance ? '-'.$Distance : ''), "UTF-8","cp1252"),0,0,'C',0, '', 1, false, 'T', 'T');
		}


		//TOTALE DELLO SCORE
		$ErScoreTotal = empty($Distance) ? '' : ($Data['Arr'.$Distance] and $Data["QuD{$Distance}"]!=$ScoreTotal);
		$ErScoreGold  = empty($Distance) ? '' : ($Data["QuGD{$Distance}"]!=$ScoreGold);
		$ErScoreXNine = empty($Distance) ? '' : ($Data["QuXD{$Distance}"]!=$ScoreXnine);

		$this->SetXY($TopX + $BCode, $TopY+$TopOffset+$CellH*($NumEnd +1));
	   	$this->SetFont($this->FontStd,'B',11);
		$this->Cell(($NumArrow+($this->PrintTotalCols ? 1.5 : 2.2))*$CellW - $BCode + ($ArrowEnds[$Event][$CurDist]['arrows']>5 ? $TotalCellW/4 : 0),$CellH, (get_text('Total') . " "),0,0,'R',0);
		$this->Cell($TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1),$CellH,($Data['Arr'.$Distance] == "" ? '' : $ScoreTotal),1,0,'C',0);
		if($this->FillWithArrows && $ErScoreTotal)
			$this->Line($x1 = $this->getx() - (($this->PrintTotalCols ? 1 : 1.4)*$CellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1)), $y1=$this->gety()+$CellH, $x1+($this->PrintTotalCols ? 1 : 1.4)*$CellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1), $y1-$CellH);
		if($this->PrintTotalCols) {
			$this->SetFillColor(0xFF,0xE8,0xE8);
			$this->Cell(1.1*$CellW,$CellH,($Data['Arr'.$Distance] == "" ? '' : $ScoreTotal + $Data['Tot'.$Distance]),1,0,'C',1);
			$this->SetFillColor(0xE8,0xE8,0xE8);
		}
		$this->SetFont($this->FontStd,'B',9);
		if(!($this->NoTensOnlyX or $this->prnGolds==$this->prnXNine)) {
			$this->Cell($XNineW,$CellH,($Data['Arr'.$Distance] == "" ? '' : $ScoreGold),1,0,'C',0);
			if($this->FillWithArrows && $ErScoreGold) {
				$this->Line($x1 = $this->getx() - 0.7*$CellW, $y1=$this->gety()+$CellH, $x1+0.7*$CellW, $y1-$CellH);
			}
		}
		$this->Cell($XNineW,$CellH,($Data['Arr'.$Distance] == "" ? '' : $ScoreXnine),1,0,'C',0);
		if($this->FillWithArrows && $ErScoreXNine) {
			$this->Line($x1 = $this->getx() - 0.7*$CellW, $y1=$this->gety()+$CellH, $x1+0.7*$CellW, $y1-$CellH);
		}

		if(($this->NoTensOnlyX) and !empty($Distance) and $Distance>1) {
			$this->ln();
			// prints Grand Total
			$Tot=0;
			$TotX=0;
			for($i=1; $i<=$Distance; $i++) {
				$Tot+=$Data['QuD'.$i];
				$TotX+=$Data['QuXD'.$i];
			}
			$this->SetX($TopX + $ArCellW*$NumArrow);
			$this->Cell($TotalCellW + $EndNumCellW, $CellH, get_text('RunningTotal', 'Tournament', $Data['D'.$i]),0,0,'R',0);
			$this->Cell($TotalCellW, $CellH, $this->FillWithArrows ? $Tot : '',1,0,'C',0);
			$this->Cell($XNineW, $CellH, $this->FillWithArrows ? $TotX : '',1,1,'C',0);
		}

		$this->ln($CellH/2);

		if($this->FillWithArrows && ($ErScoreTotal or $ErScoreGold or $ErScoreXNine)) {
            $this->ln($CellH/2);
			$this->SetX($TopX + $BCode);
		   	$this->SetFont($this->FontStd,'B',11);
			$this->Cell(($NumArrow+($this->PrintTotalCols ? 1.5 : 2.2))*$CellW - $BCode + ($ArrowEnds[$Event][$CurDist]['arrows']>5 ? $TotalCellW/4 : 0),$CellH, (get_text('SignedTotal', 'Tournament') . " "),0,0,'R',0);
			$this->Cell($TotalCellW * ($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1),$CellH, $Data["QuD{$Distance}"] ,1,0,'C',0);
			if($this->PrintTotalCols)
			{
				$this->Cell(1.1*$CellW,$CellH,'',0,0,'C',0);
			}
			$this->SetFont($this->FontStd,'B',9);
			if(!($this->NoTensOnlyX or $this->prnGolds==$this->prnXNine)) {
				$this->Cell($XNineW,$CellH, $Data["QuGD{$Distance}"] ,1,0,'C',0);
			}
			$this->Cell($XNineW,$CellH, $Data["QuXD{$Distance}"] ,1,0,'C',0);
		}

		// Collect Dob and Email
		if($this->GetArcInfo) {
			$this->SetXY($TopX, $this->GetY()+10);
			$this->SetFont($this->FontFix,'BI',6);

			$this->Cell($CellW*0.75, 3, $Data['EnCode'], 0, 0, 'L', 0, '', 1, false, 'T', 'B');
			$this->Cell($CellW*1.5, 3, get_text('DOB', 'Tournament'), 0, 0, 'R', 0, '', 1, false, 'T', 'B');
			$this->Cell($CellW*1.5, 3, $Data['DoB'], 'B', 0, 'L', 0, '', 1, false, 'T', 'B');
			$this->Cell($CellW, 3, get_text('Email', 'Tournament'), 0, 0, 'R', 0, '', 1, false, 'T', 'B');
			$this->Cell($Width-4.75*$CellW, 3, $Data['Email'], 'B', 0, 'L', 0, '', 1, false, 'T', 'B');
			$this->ln($CellH/2);
		}

		//FIRME
		if(!$this->FillWithArrows) {
			$SignY=$TopY+$Height-($BottomImage+3);
			//$this->Line($TopX+4, $SignY, $TopX+($Width/2)-3 , $SignY);
			//$this->Line($TopX+($Width/2)+3, $SignY, $TopX+$Width-4 , $SignY);
			$this->SetFont($this->FontFix,'BI',6);
			$this->SetXY($TopX+4, $SignY);
			$this->Cell(($Width/2)-7,3,(get_text('Archer')),'T',0,'C',0);
			$this->SetX($this->GetX()+6);
			$this->Cell(($Width/2)-7,3,empty($Data['SecondScorer']) ? get_text('Scorer') : get_text('ScorerNum', 'Api', 1),'T',0,'C',0);
			if(!empty($Data['SecondScorer'])) {
				$this->SetXY($TopX+3+($Width/2), $SignY+12);
				$this->Cell(($Width/2)-7,3,get_text('ScorerNum', 'Api', 2),'T',0,'C',0);
			}
		}
		//$this->Rect($TopX, $TopY, $Width, $Height);
	}

//DRAW SCORE - ArrowCollector
	function DrawCollector($TopX, $TopY, $Width, $Height, $End, $NumArrow, $Archers, $Target='', $Distance='') {
		global $CFG;
		//PARAMETRI CALCOLATI
		$TopOffset=12;
		$TgtWidth = 12;
		$CellW = ($Width / (2*$NumArrow));
		$CellH = ($Height-$TopOffset)/(count($Archers)+1);
		//HEADER LOGO SX & Dx
		$TmpLeft = 0;
		if($this->PrintLogo && $this->PrintDrawing) {
			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) {
				$im=getimagesize($IM);
				$imW=0; $imH=$TopOffset-2;
				if($im[0]/$im[1] > 1.5) { $imH=0; $imW=($TopOffset-2)*1.5; }
				$this->Image($IM, $TopX, $TopY, $imW, $imH);
				$TmpLeft = 1 + ($imW ? $imW : ($TopOffset-2)*$im[0]/$im[1]);
			}
//			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) {
//				$im=getimagesize($IM);
//				$TmpRight = ($im[0] * 15 / $im[1]);
//				$this->Image($IM, ($TopX+$Width-$TmpRight), $TopY, 0, 15);
//				$TmpRight++;
//			}
//			//IMMAGINE DEGLI SPONSOR
//			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) {
//				$im=getimagesize($IM);
//				$imgW = $Width;
//				$imgH = $imgW * $im[1] /$im[0] ;
//				if($imgH > $BottomImage) {
//					$imgH = $BottomImage;
//					$imgW = $imgH * $im[0] /$im[1] ;
//				}
//				$this->Image($IM, ($TopX+($Width-$imgW)/2), ($TopY+$Height-$imgH), $imgW, $imgH);
//			}
		}

		//TESTATA GARA
//		if($this->PrintHeader) {
			$tmpPad=$this->getCellPaddings();
			$this->SetCellPadding(0);
			$this->SetColors(false);
	    	$this->SetFont($this->FontStd,'B',8);
			$this->SetXY($TopX+$TmpLeft,$TopY);
			$this->Cell($Width-$TmpLeft-$TgtWidth, 5, substr($this->Name, 0, 40), 0, 0, 'L', 0);
    		$this->SetFont($this->FontStd,'',7);
			$this->SetXY($TopX+$TmpLeft, $TopY+5);
			$this->Cell($Width-$TmpLeft-$TgtWidth, 5, substr($this->Where, 0, 40), 0, 0, 'L', 0);
			$this->SetCellPaddings($tmpPad['L'], $tmpPad['T'], $tmpPad['R'], $tmpPad['B']);
//		}

		//PAGLIONE
		$this->SetXY($TopX+$Width-$TgtWidth, $TopY);
		$this->SetFont($this->FontStd,'B',14);
		$this->SetColors(true);
		$this->Cell($TgtWidth, 6, $Target, 0, 0, 'R', 1);
		$this->SetXY($TopX+$Width-$TgtWidth, $TopY+6);
		$this->SetFont($this->FontStd,'',9);
		$this->SetColors(true);
		$this->Cell($TgtWidth, 4, 'End: '.$End, 0, 0, 'L', 1);

		//HEADER DELLO SCORE
		$this->SetXY($TopX, $TopY+$TopOffset);
	   	$this->SetFont($this->FontStd,'I',8);
		$this->SetFillColor(0xE8,0xE8,0xE8);
//		$this->SetColors(true,true);
		$this->Cell($Width/2, $CellH, $Distance, 1, 0, 'L', 1);
		$this->SetFillColor(0xE8,0xE8,0xE8);
	   	$this->SetFont($this->FontStd, 'B', 8);
//		$this->SetColors(false);
		foreach(range(1, $NumArrow) as $j) $this->Cell($CellW, $CellH, $j, 1, 0, 'C', 1);

		//DATI ATLETI
		foreach($Archers as $k => $Archer) {
			$this->SetXY($TopX, $TopY+12+$CellH+($k*$CellH));
			$this->Cell(4, $CellH, chr(65+$k), 1, 0, 'L', 0);
			$this->Cell($Width/2 - 4, $CellH, $Archer, 1, 0, 'L', 0);
			foreach(range(1, $NumArrow) as $j) $this->Cell($CellW, $CellH, '', 1, 0, 'C', 0);
		}
	}

//DRAW SCORE - FIELD VERSION
	function DrawScoreField($TopX, $TopY, $Width, $Height, $CurDist=0, $Data=array(), $SesTar4Session=0, $SesFirstTarget=1) {
		global $CFG;
		static $ArrowEnds=array();

		$prnAppInfo=($SesFirstTarget!=1);

		// $ArrowEnds will contain the ends per arrows of each event and distance
        //$CurDist=(empty($CurDist) ? 1 : $CurDist);
		$NumEnd=$Data['NumEnds'.$CurDist]/2;
		if($SesTar4Session==0 || $SesTar4Session!=$Data['NumEnds'.$CurDist]) {
			$SesTar4Session = $Data['NumEnds'.$CurDist];
		}

		$NumArrow=$Data['NumArrows'.$CurDist];
		if($NumArrow==6) {
			$NumArrow=3;
			$NumEnd*=2;
		}
		$isNfaa = false;
		if($_SESSION['TourLocRule']=='NFAA' AND $SesTar4Session==51) {
			$isNfaa = true;
			$NumEnd=17;
		}

		//PARAMETRI CALCOLATI
		$TopOffset=30;
		$BottomImage=0;
		$TargetNo=(!empty($Data["AtTarget"]) ? intval($Data["AtTarget"]) : 1);
		$TargetNoApp=(!empty($Data["AtTarget"]) ? intval($Data["AtTarget"]) : 1);

		if($TargetNo-($SesFirstTarget-1)>$SesTar4Session) {
			$TargetNo = (($TargetNo-1) % $SesTar4Session) + $SesFirstTarget;
		}
		if($TargetNoApp>$SesTar4Session) {
			$TargetNoApp = (($TargetNoApp-1) % $SesTar4Session) + 1;
		}
		//HEADER LOGO SX & Dx
		$TmpLeft = 0;
		$TmpRight = 0;
		if($this->PrintLogo) {
			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) {
				$im=getimagesize($IM);
				$this->Image($IM, $TopX, $TopY, 0, ($TopOffset/2));
				$TmpLeft = (1 + ($im[0] * ($TopOffset/2) / $im[1]));
			}
			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) {
				$im=getimagesize($IM);
				$TmpRight = ($im[0] * 15 / $im[1]);
				$this->Image($IM, ($TopX+$Width-$TmpRight), $TopY, 0, 15);
				$TmpRight++;
			}
			//IMMAGINE DEGLI SPONSOR
			if($this->BottomImage and file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) {
				$BottomImage=7.5;
				$im=getimagesize($IM);
				$imgW = $Width;
				$imgH = $imgW * $im[1] /$im[0] ;
				if($imgH > $BottomImage) {
					$imgH = $BottomImage;
					$imgW = $imgH * $im[0] /$im[1] ;
				}
				$this->Image($IM, ($TopX+($Width-$imgW)/2), ($TopY+$Height-$imgH), $imgW, $imgH);
			}
		}

		$CellW = ((($Width-5)/2) / ($NumArrow+5));
		if($isNfaa) {
			$CellW = ((($Width-5)/3) / ($NumArrow+5)) ;
		}
		$CellH = ($Height-41-$BottomImage)/(ceil($NumEnd)+3);

		// CODICE A BARRE
		$BCode=0;
		if($this->PrintBarcode and !empty($Data['EnCode'])) {
			if($Data['EnCode'][0]=='_') $Data['EnCode']='UU'.substr($Data['EnCode'], 1);
			$txt=$Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'];
			if(!empty($Data['ElCode'])) {
				$txt=$Data['EnCode'].'-'.$Data['ElPhase'].'-'.$Data['ElCode'];
			}
			$BCode=60;
			$this->SetXY(10+$Width-$TmpRight-$BCode, $TopY);
			$this->SetFont('barcode','',28);
			$this->Cell($BCode-5, $CellH, mb_convert_encoding('*' . $txt, "UTF-8","cp1252") . "*",0,0,'C',0);
			$this->SetFont($this->FontStd,'',7);
			$this->SetXY(10+$Width-$TmpRight-$BCode, $TopY+9);
			$this->Cell($BCode-5, $CellH, mb_convert_encoding($txt, "UTF-8","cp1252"),0,0,'C',0);
		}

		//TESATATA GARA
		if($this->PrintHeader)
		{
			$this->SetColors(true);
	    	$this->SetFont($this->FontStd,'B',9);
			$this->SetXY($TopX+$TmpLeft,$TopY);
			$this->MultiCell($Width-$TmpLeft-$TmpRight-$BCode, 4, $this->Name, 0, 'L', 0);
    		$this->SetFont($this->FontStd,'',7);
			$this->SetXY($TopX+$TmpLeft, $this->GetY());
			if($this->GetStringWidth($this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT))>=$Width-$TmpLeft-$TmpRight)
			{
				$this->MultiCell($Width-$TmpLeft-$TmpRight-$BCode, 4, $this->Where, 0, 'L', 0);
				$this->SetXY($TopX+$TmpLeft, $this->GetY());
				$this->MultiCell($Width-$TmpLeft-$TmpRight-$BCode, 4, TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			}
			else
				$this->MultiCell($Width-$TmpLeft-$TmpRight-$BCode, 4, $this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
		}
		//DATI ATLETA
		$FlagOffset=0.2*$CellW;
		$this->SetXY($TopX+0.2*$CellW, $TopY+($TopOffset*7/12));
		if($this->PrintFlags and !empty($Data['CoCode'])) {
			if(is_file($file= $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$Data['CoCode'].'.jpg')) {
				$H=$TopOffset*3/8;
				$W=$H*3/2;
				$OrgY=$this->gety();
				$this->Image($file, $TopX, $this->gety(), $W, $H, 'JPG', '', '', true, 300, '', false, false, 1, true);
				$FlagOffset=$W+1;
			}
		}

		$this->SetXY($FlagOffset+$TopX, $TopY+($TopOffset*7/12));
		$this->SetFont($this->FontStd,'',8);
		$this->SetColors(false);
		$this->Cell($this->GetStringWidth((get_text('Archer') . ": ")),$TopOffset/6, (get_text('Archer') . ": "),'B',0,'L',0);
		$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(true);
		$this->Cell($Width-(($prnAppInfo ? 3.4 : 1.9) * $CellW)-$this->GetStringWidth((get_text('Archer') . ": "))-$FlagOffset,$TopOffset/6, (array_key_exists("Ath",$Data) ? $Data["Ath"] : ' '),'B',0,'L',0);
		$this->SetXY($FlagOffset+$TopX, $TopY+($TopOffset*19/24));
		$this->SetFont($this->FontStd,'',8);
		$this->SetColors(false);
		$this->Cell($this->GetStringWidth((get_text('Country') . ": ")),$TopOffset/6, (get_text('Country') . ": "),'B',0,'L',0);
		$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(true);

		$CellTmpWidth=$Width-(($prnAppInfo ? 3.4 : 1.9) * $CellW)-$this->GetStringWidth((get_text('Country') . ": "))-$FlagOffset;
		if(array_key_exists("Noc",$Data)) {
			$str=$Data['CoCode'].' -';
			$strW=$this->GetStringWidth($str);
			$this->Cell($strW, $TopOffset/6, $str,'B',0,'L',0);
			$this->Cell($CellTmpWidth-$strW, $TopOffset/6, $Data['CoName'],'B',0,'L',0);
		} else {
			$this->Cell($CellTmpWidth,$TopOffset/6, ' ','B',0,'L',0);
		}
		//APP INFO
		if($prnAppInfo) {
			$this->SetXY($TopX+$Width-(3.4*$CellW), $TopY+($TopOffset*13/24));
			$this->SetFont($this->FontStd,'I',16);
			$this->SetColors(true);
			$HeaderTarget = ' ';
			if(array_key_exists("tNo",$Data)) {
				$HeaderTarget = trim($Data["tNo"],'0');
				if(!empty($Data["AtTarget"]) and $TargetNoApp!=intval($Data["AtTarget"])) {
					$HeaderTarget = $TargetNoApp . substr($Data["tNo"],-1,1) . '-' . $this->Indices[ceil($Data["AtTarget"]/$SesTar4Session)-2];
				}
			}
			$this->Cell((1.9*$CellW),$TopOffset*7/24, $HeaderTarget,'TLR',0,'R',1);
			$this->SetXY($TopX+$Width-(3.4*$CellW), $TopY+($TopOffset*10/12));
			$this->SetFont($this->FontStd,'I',8);
			$this->SetColors(true);
			$this->Cell((1.9*$CellW),($TopOffset*2/12), 'ISK App','BLR',0,'C',1);

		}
		//PAGLIONE
		$this->SetXY($TopX+$Width-(1.4*$CellW), $TopY+($TopOffset*13/24));
		$this->SetFont($this->FontStd,'B',20);
		$this->SetColors(true);
		$HeaderTarget = ' ';
		if(array_key_exists("tNo",$Data)) {
			$HeaderTarget = trim($Data["tNo"],'0');
			if(!empty($Data["AtTarget"]) and $TargetNo!=intval($Data["AtTarget"])) {
				$HeaderTarget = $TargetNo . substr($Data["tNo"],-1,1) . '-' . $this->Indices[ceil($Data["AtTarget"]/(2*$NumEnd))-2];
			}
		}


		$this->Cell((1.4*$CellW),$TopOffset*7/24, $HeaderTarget,0,0,'R',1);
		$this->SetXY($TopX+$Width-(1.4*$CellW), $TopY+($TopOffset*10/12));
		$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(true);
		$this->Cell((1.4*$CellW),$TopOffset*2/12,(array_key_exists("Cat",$Data) ? $Data["Cat"] : ' '),'T',0,'C',1);
//####SCORE 1 ####////
		//NFAA
		$prnAppInfo=false;
		$TargetNo=$TargetNoApp;
		//HEADER DELLO SCORE 1
		$this->SetXY($TopX, $TopY+$TopOffset);
	   	$this->SetFont($this->FontStd,'I',($isNfaa ? 8 : 10));
		$this->SetFillColor(0xF8,0xF8,0xF8);
		$this->SetColors(true,true);
		$this->Cell(($prnAppInfo ? 1.6 : 0.8)*$CellW,$CellH, (array_key_exists("D".$CurDist,$Data) ? $Data["D".$CurDist] : ' '),0,0,'C',(array_key_exists("D".$CurDist,$Data) ? 1 : 0));
		$this->SetFillColor(0xE8,0xE8,0xE8);
	   	$this->SetFont($this->FontStd,'B',($isNfaa ? 8 : 10));
		$this->SetColors(false);
		for($j=0; $j<$NumArrow; $j++) {
			$this->Cell($CellW*($NumArrow>1 ? 1 : ($prnAppInfo ? 1.6 : 2.4)),$CellH, ($j+1), 1, 0, 'C', 1);
		}
	   	$this->SetFont($this->FontStd,'B',($isNfaa ? 8 : 10));
		if($NumArrow>1) {
			$this->Cell(1.4*$CellW,$CellH, (get_text('TotalProg','Tournament')),1,0,'C',1);
		}
		$this->Cell(1.4*$CellW,$CellH, (get_text('TotalShort','Tournament')),1,0,'C',1);
		if(empty($this->prnXNine)) {
			$this->Cell(1.4*$CellW,$CellH, ($this->prnGolds),1,0,'C',1);
		} else {
			$this->Cell(0.7*$CellW,$CellH, ($this->prnGolds),1,0,'C',1);
			$this->Cell(0.7 * $CellW, $CellH, ($this->prnXNine), 1, 0, 'C', 1);
		}
		$this->ln();
		$ScoreTotal = 0;
		$ScoreGold = 0;
		$ScoreXnine = 0;
		//RIGHE DELLO SCORE 1
		for($i=1; $i<=ceil($NumEnd); $i++) {
			$this->SetXY($TopX, $TopY+$TopOffset+$CellH*$i);
			if($prnAppInfo) {
				$this->SetFont($this->FontStd,'I',7);
				$this->Cell(0.8 * $CellW, $CellH, ($this->PrintLineNo ? '# '.$TargetNoApp : ''), 1, 0, 'C', 0);
			}
			$this->SetFont($this->FontStd,'B',($isNfaa ? 8 : 10));
			$this->Cell(0.8*$CellW,$CellH, ($this->PrintLineNo ? $TargetNo : ''),1,0,'C',1);
			$this->SetFont($this->FontStd,'',($isNfaa ? 8 : 10));
			for($j=0; $j<$NumArrow; $j++) {
				$this->Cell($CellW*($NumArrow>1 ? 1 : ($prnAppInfo ? 1.6 : 2.4)),$CellH, $this->FillWithArrows ?  DecodeFromLetter(substr($Data["Arr".$CurDist], (($TargetNo-1)%(2*$NumEnd))*$NumArrow+$j, 1)) : '', 1, 0, 'C', 0);
			}
			list($ScoreEndTotal,$ScoreEndGold, $ScoreEndXnine) = ValutaArrowStringGX(substr($Data["Arr".$CurDist],(($TargetNo-1)%(2*$NumEnd))*$NumArrow,$NumArrow),$this->goldsChars,$this->xNineChars);
			$ScoreTotal += $ScoreEndTotal;
			$ScoreGold += $ScoreEndGold;
			$ScoreXnine += $ScoreEndXnine;
			if(!strlen(trim(substr($Data["Arr".$CurDist],(($TargetNo-1)%(2*$NumEnd))*$NumArrow,$NumArrow)))) {
				$ScoreEndTotal='';
				$ScoreEndGold='';
				$ScoreEndXnine='';
			}
			if($NumArrow>1) {
				$this->Cell(1.4*$CellW,$CellH,($this->FillWithArrows ? $ScoreEndTotal : ''),1,0,'C',0);
			}
			$this->Cell(1.4*$CellW,$CellH,($this->FillWithArrows ? $ScoreTotal : ''),1,0,'C',0);
			if(empty($this->prnXNine)) {
				$this->Cell(1.4*$CellW,$CellH,($this->FillWithArrows ? $ScoreEndGold : ''),1,0,'C',0);
			} else {
				$this->Cell(0.7*$CellW,$CellH,($this->FillWithArrows ? $ScoreEndGold : ''),1,0,'C',0);
				$this->Cell(0.7 * $CellW, $CellH, ($this->FillWithArrows ? $ScoreEndXnine : ''), 1, 0, 'C', 0);
			}
			$this->ln();
			if(++$TargetNo-($SesFirstTarget-1)>$SesTar4Session) {
				$TargetNo = $SesFirstTarget;
			}
			if(++$TargetNoApp>$SesTar4Session) {
				$TargetNoApp= 1;
			}
			//NFAA
			$TargetNo=$TargetNoApp;
		}

//#### SCORE 2 ####////
		//HEADER DELLO SCORE 2
		$this->SetXY($TopX+($Width-5)/2+5, $TopY+$TopOffset);
		if($isNfaa) {
			$this->SetXY($TopX+($Width-5)/3+2.5+(empty($this->prnXNine) ? 0.35*$CellW:0), $TopY+$TopOffset);
		}
	   	$this->SetFont($this->FontStd,'I',($isNfaa ? 8 : 10));
		$this->SetFillColor(0xF8,0xF8,0xF8);
		$this->SetColors(true,true);
		$this->Cell(($prnAppInfo ? 1.6 : 0.8)*$CellW,$CellH,(!empty($Data["D".$CurDist]) && $this->FillWithArrows ? $Data["D".$CurDist] : (array_key_exists("Dist",$Data) ? $Data["Dist"] : ' ')),0,0,'C',(array_key_exists("Dist",$Data) ? 1 : 0));
		$this->SetFillColor(0xE8,0xE8,0xE8);
	   	$this->SetFont($this->FontStd,'B',($isNfaa ? 8 : 10));
		$this->SetColors(false);
		for($j=0; $j<$NumArrow; $j++) {
			$this->Cell($CellW*($NumArrow>1 ? 1 : ($prnAppInfo ? 1.6 : 2.4)),$CellH, ($j+1), 1, 0, 'C', 1);
		}
	   	$this->SetFont($this->FontStd,'B',($isNfaa ? 8 : 10));
		if($NumArrow>1) {
			$this->Cell(1.4*$CellW,$CellH, (get_text('TotalProg','Tournament')),1,0,'C',1);
		}
		$this->Cell(1.4*$CellW,$CellH, (get_text('TotalShort','Tournament')),1,0,'C',1);
		$this->Cell(0.7*$CellW,$CellH, ($this->prnGolds),1,empty($this->prnXNine),'C',1);
		if(!empty($this->prnXNine)) {
			$this->Cell(0.7 * $CellW, $CellH, ($this->prnXNine), 1, 1, 'C', 1);
		}
		//RIGHE DELLO SCORE 2
		for($i=1; $i<=$NumEnd; $i++) {
			$this->SetXY($TopX+($Width-5)/2+5, $TopY+$TopOffset+$CellH*$i);
			if($isNfaa) {
				$this->SetXY($TopX+($Width-5)/3+2.5+(empty($this->prnXNine) ? 0.35*$CellW:0), $TopY+$TopOffset+$CellH*$i);
			}
			if($prnAppInfo) {
				$this->SetFont($this->FontStd,'I',7);
				$this->Cell(0.8 * $CellW, $CellH, ($this->PrintLineNo ? '# '.$TargetNoApp : ''), 1, 0, 'C', 0);
			}
			$this->SetFont($this->FontStd,'B',($isNfaa ? 8 : 10));
			$this->Cell(0.8*$CellW,$CellH,($this->PrintLineNo ? $TargetNo : ''),1,0,'C',1);
			$this->SetFont($this->FontStd,'',($isNfaa ? 8 : 10));
			for($j=0; $j<$NumArrow; $j++) {
				$this->Cell($CellW*($NumArrow>1 ? 1 : ($prnAppInfo ? 1.6 : 2.4)),$CellH, ($this->FillWithArrows ?  DecodeFromLetter(substr($Data["Arr".$CurDist], (($TargetNo-1)%(2*$NumEnd))*$NumArrow+$j, 1)) : ''), 1, 0, 'C', 0);
			}
			list($ScoreEndTotal,$ScoreEndGold, $ScoreEndXnine) = ValutaArrowStringGX(substr($Data["Arr".$CurDist],(($TargetNo-1)%(2*$NumEnd))*$NumArrow,$NumArrow),$this->goldsChars,$this->xNineChars);
			$ScoreTotal += $ScoreEndTotal;
			$ScoreGold += $ScoreEndGold;
			$ScoreXnine += $ScoreEndXnine;
			if(!strlen(trim(substr($Data["Arr".$CurDist],(($TargetNo-1)%(2*$NumEnd))*$NumArrow,$NumArrow)))) {
				$ScoreEndTotal='';
				$ScoreEndGold='';
				$ScoreEndXnine='';
			}
			if($NumArrow>1) {
				$this->Cell(1.4 * $CellW, $CellH, ($this->FillWithArrows ? $ScoreEndTotal : ''), 1, 0, 'C', 0);
			}
			$this->Cell(1.4*$CellW,$CellH,($this->FillWithArrows ? $ScoreTotal : ''),1,0,'C',0);
			$this->Cell(0.7*$CellW,$CellH,($this->FillWithArrows ? $ScoreEndGold : ''),1,empty($this->prnXNine),'C',0);
			if(!empty($this->prnXNine)) {
				$this->Cell(0.7 * $CellW, $CellH, ($this->FillWithArrows ? $ScoreEndXnine : ''), 1, 1, 'C', 0);
			}
			if(++$TargetNo-($SesFirstTarget-1)>$SesTar4Session) {
				$TargetNo = $SesFirstTarget;
			}
			if(++$TargetNoApp>$SesTar4Session) {
				$TargetNoApp= 1;
			}
			//NFAA
			$TargetNo=$TargetNoApp;
		}
//#### SCORE 3 - SOLO NFAA ####////
		//HEADER DELLO SCORE 3
		if($isNfaa) {
			$this->SetXY($TopX + ($Width - 5) * 2/3 + 5 + (empty($this->prnXNine) ? 0.7*$CellW:0), $TopY + $TopOffset);

			$this->SetFont($this->FontStd, 'I', 8);
			$this->SetFillColor(0xF8, 0xF8, 0xF8);
			$this->SetColors(true, true);
			$this->Cell(($prnAppInfo ? 1.6 : 0.8) * $CellW, $CellH, (!empty($Data["D" . $CurDist]) && $this->FillWithArrows ? $Data["D" . $CurDist] : (array_key_exists("Dist", $Data) ? $Data["Dist"] : ' ')), 0, 0, 'C', (array_key_exists("Dist", $Data) ? 1 : 0));
			$this->SetFillColor(0xE8, 0xE8, 0xE8);
			$this->SetFont($this->FontStd, 'B', 8);
			$this->SetColors(false);
			for ($j = 0; $j < $NumArrow; $j++) {
				$this->Cell($CellW * ($NumArrow > 1 ? 1 : ($prnAppInfo ? 1.6 : 2.4)), $CellH, ($j + 1), 1, 0, 'C', 1);
			}
			$this->SetFont($this->FontStd, 'B', 8);
			if ($NumArrow > 1) {
				$this->Cell(1.4 * $CellW, $CellH, (get_text('TotalProg', 'Tournament')), 1, 0, 'C', 1);
			}
			$this->Cell(1.4 * $CellW, $CellH, (get_text('TotalShort', 'Tournament')), 1, 0, 'C', 1);
			$this->Cell(0.7 * $CellW, $CellH, ($this->prnGolds), 1, empty($this->prnXNine), 'C', 1);
			if(!empty($this->prnXNine)) {
				$this->Cell(0.7 * $CellW, $CellH, ($this->prnXNine), 1, 1, 'C', 1);
			}
			//RIGHE DELLO SCORE 3
			for ($i = 1; $i <= $NumEnd; $i++) {
				$this->SetXY($TopX + ($Width - 5) *2/3 + 5 + (empty($this->prnXNine) ? 0.7*$CellW:0), $TopY + $TopOffset + $CellH * $i);
				if($prnAppInfo) {
					$this->SetFont($this->FontStd,'I',7);
					$this->Cell(0.8 * $CellW, $CellH, ($this->PrintLineNo ? '# '.$TargetNoApp : ''), 1, 0, 'C', 0);
				}
				$this->SetFont($this->FontStd, 'B', 8);
				$this->Cell(0.8 * $CellW, $CellH, ($this->PrintLineNo ? $TargetNo : ''), 1, 0, 'C', 1);
				$this->SetFont($this->FontStd, '', 8);
				for ($j = 0; $j < $NumArrow; $j++) {
					$this->Cell($CellW * ($NumArrow > 1 ? 1 : ($prnAppInfo ? 1.6 : 2.4)), $CellH, ($this->FillWithArrows ? DecodeFromLetter(substr($Data["Arr" . $CurDist], (($TargetNo - 1) % (2 * $NumEnd)) * $NumArrow + $j, 1)) : ''), 1, 0, 'C', 0);
				}
				list($ScoreEndTotal, $ScoreEndGold, $ScoreEndXnine) = ValutaArrowStringGX(substr($Data["Arr" . $CurDist], (($TargetNo - 1) % (2 * $NumEnd)) * $NumArrow, $NumArrow), $this->goldsChars, $this->xNineChars);
				$ScoreTotal += $ScoreEndTotal;
				$ScoreGold += $ScoreEndGold;
				$ScoreXnine += $ScoreEndXnine;
				if (!strlen(trim(substr($Data["Arr" . $CurDist], (($TargetNo - 1) % (2 * $NumEnd)) * $NumArrow, $NumArrow)))) {
					$ScoreEndTotal = '';
					$ScoreEndGold = '';
					$ScoreEndXnine = '';
				}
				if ($NumArrow > 1) {
					$this->Cell(1.4 * $CellW, $CellH, ($this->FillWithArrows ? $ScoreEndTotal : ''), 1, 0, 'C', 0);
				}
				$this->Cell(1.4 * $CellW, $CellH, ($this->FillWithArrows ? $ScoreTotal : ''), 1, 0, 'C', 0);
				$this->Cell(0.7 * $CellW, $CellH, ($this->FillWithArrows ? $ScoreEndGold : ''), 1, empty($this->prnXNine), 'C', 0);
				if(!empty($this->prnXNine)) {
					$this->Cell(0.7 * $CellW, $CellH, ($this->FillWithArrows ? $ScoreEndXnine : ''), 1, 1, 'C', 0);
				}
				if(++$TargetNo-($SesFirstTarget-1)>$SesTar4Session) {
					$TargetNo = $SesFirstTarget;
				}
				if(++$TargetNoApp>$SesTar4Session) {
					$TargetNoApp= 1;
				}
				//NFAA
				$TargetNo=$TargetNoApp;
			}
		}

		//TOTALE DELLO SCORE
        $ErScoreTotal = false;
        $ErScoreGold = false;
        $ErScoreXNine = false;
        if(isset($Data["QuD"]) AND isset($Data["QuGD"]) AND isset($Data["QuXD"])) {
            $ErScoreTotal = ($Data["QuD"] != $ScoreTotal);
            $ErScoreGold = ($Data["QuGD"] != $ScoreGold);
            $ErScoreXNine = ($Data["QuXD"] != $ScoreXnine);
        }

		//TOTALE GENERALE
		$OldLine=$this->GetLineWidth();
		$this->SetLineWidth(0.5);
		$this->SetXY($TopX+($Width-5)/2+5, $TopY+$TopOffset+$CellH*($NumEnd+1)+1);
		if($isNfaa){
			$this->SetXY($TopX+($Width-5)*2/3+5+(empty($this->prnXNine) ? 0.7*$CellW:0), $TopY+$TopOffset+$CellH*($NumEnd+1)+1);
		}
	   	$this->SetFont($this->FontStd,'B',10);
		$this->Cell(($NumArrow+2.2)*$CellW,$CellH, (get_text('Total') . " "),0,0,'R',0);
		$this->Cell(1.4*$CellW,$CellH,($this->FillWithArrows ? $ScoreTotal : ''),1,0,'C',0);
		if($this->FillWithArrows && $ErScoreTotal) {
			$this->Line($x1 = $this->getx(), $y1=$this->gety()+$CellH, $x1-(1.4*$CellW), $y1-$CellH);
		}
		$this->Cell(0.7*$CellW,$CellH,($this->FillWithArrows ? $ScoreGold : ''),1,empty($this->prnXNine),'C',0);
		if($this->FillWithArrows && $ErScoreGold) {
			$this->Line($x1 = $this->getx(), $y1=$this->gety()+$CellH, $x1-(0.7*$CellW), $y1-$CellH);
		}
		if($this->FillWithArrows && $ErScoreXNine) {
			$this->Line($x1 = $this->getx(), $y1=$this->gety(), $x1+(0.7*$CellW), $y1+$CellH);
		}
		if(!empty($this->prnXNine)) {
			$this->Cell(0.7 * $CellW, $CellH, ($this->FillWithArrows ? $ScoreXnine : ''), 1, 1, 'C', 0);
		}
		if($this->FillWithArrows and ($ErScoreTotal or $ErScoreGold or $ErScoreXNine)) {
			$this->SetXY($TopX+($Width-5)/2+5, $TopY+$TopOffset+$CellH*($NumEnd+2)+1);
			$this->Cell(($NumArrow+2.2)*$CellW,$CellH, (get_text('SignedTotal', 'Tournament') . " "),0,0,'R',0);

			$this->Cell(1.4*$CellW,$CellH, $Data["QuD"],1,0,'C',0);
			$this->Cell(0.7*$CellW,$CellH, $Data["QuGD"],1,empty($this->prnXNine),'C',0);
			if(!empty($this->prnXNine)) {
				$this->Cell(0.7 * $CellW, $CellH, $Data["QuXD"], 1, 1, 'C', 0);
			}
		} else {
			$this->ln($CellH);
		}

		$this->SetLineWidth(0.2);

		//FIRME
		$this->SetFont($this->FontFix,'BI',6);
		$this->Cell(4, 3, '', 0, 0, 'C', 0);
		$this->Cell($Width/2-7, 3, (get_text('Archer')), 'B', 0, 'L', 0);
		$this->Cell(6, 3, '', 0, 0, 'C', 0);
		$this->Cell($Width/2-7,3,(get_text('Scorer')),'B',1,'L',0);
		$this->SetLineWidth($OldLine);
	}

//DRAW SCORE - 3D VERSION
	function DrawScore3D($TopX, $TopY, $Width, $Height, $NumEndTotal, $Data=array(), $OnlyLeftScore=false, $Target='')
	{
		global $CFG;
		if(!$Target) {
			$Target=array(11, 10, 8, 5, 'M');
		}

        $NumEnd = $NumEndTotal/2;
		//PARAMETRI CALCOLATI
		$TopOffset=30;
		$BottomImage=0;
		$TargetNo=(!empty($Data["AtTarget"]) ? intval($Data["AtTarget"]) : 1);
		if($TargetNo>2*$NumEnd) {
			$TargetNo = (($TargetNo-1) % (2*$NumEnd)) + 1;
		}


		//HEADER LOGO SX & Dx
		$TmpLeft = 0;
		$TmpRight = 0;
		if($this->PrintLogo && $this->PrintDrawing) {
			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) {
				$im=getimagesize($IM);
				$this->Image($IM, $TopX, $TopY, 0, ($TopOffset/2));
				$TmpLeft = (1 + ($im[0] * ($TopOffset/2) / $im[1]));
			}
			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) {
				$im=getimagesize($IM);
				$TmpRight = ($im[0] * 15 / $im[1]);
				$this->Image($IM, ($TopX+$Width-$TmpRight), $TopY, 0, 15);
				$TmpRight++;
			}
			//IMMAGINE DEGLI SPONSOR
			if($this->BottomImage and file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) {
				$BottomImage=7.5;
				$im=getimagesize($IM);
				$imgW = $Width;
				$imgH = $imgW * $im[1] /$im[0] ;
				if($imgH > $BottomImage)
				{
					$imgH = $BottomImage;
					$imgW = $imgH * $im[0] /$im[1] ;
				}
				$this->Image($IM, ($TopX+($Width-$imgW)/2), ($TopY+$Height-$imgH), $imgW, $imgH);
			}
		}
		$CellW = ((($Width-5)/2) / (count($Target)+3));
		$CellH = ($Height-41-$BottomImage)/($NumEnd+2);

		// CODICE A BARRE
		$BCode=0;
		if($this->PrintBarcode and !empty($Data['EnCode'])) {
			$BCode=60;
			$this->SetXY(10+$Width-$TmpRight-$BCode, $TopY);
			$this->SetFont('barcode','',28);
			if($Data['EnCode'][0]=='_') $Data['EnCode']='UU'.substr($Data['EnCode'], 1);
			$this->Cell($BCode-5, $CellH, mb_convert_encoding('*' . $Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'], "UTF-8","cp1252") . "*",0,0,'C',0);
			$this->SetFont($this->FontStd,'',7);
			$this->SetXY(10+$Width-$TmpRight-$BCode, $TopY+9);
			$this->Cell($BCode-5, $CellH, mb_convert_encoding($Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'], "UTF-8","cp1252"),0,0,'C',0);
		}

		//TESTATA GARA
		if($this->PrintHeader)
		{
			$this->SetColors(true);
	    	$this->SetFont($this->FontStd,'B',9);
			$this->SetXY($TopX+$TmpLeft,$TopY);
			$this->MultiCell($Width-$TmpLeft-$TmpRight-$BCode, 4, $this->Name, 0, 'L', 0);
    		$this->SetFont($this->FontStd,'',7);
			$this->SetXY($TopX+$TmpLeft, $this->GetY());
			if($this->GetStringWidth($this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT))>=$Width-$TmpLeft-$TmpRight)
			{
				$this->MultiCell($Width-$TmpLeft-$TmpRight-$BCode, 4, $this->Where, 0, 'L', 0);
				$this->SetXY($TopX+$TmpLeft, $this->GetY());
				$this->MultiCell($Width-$TmpLeft-$TmpRight-$BCode, 4, TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			}
			else
				$this->MultiCell($Width-$TmpLeft-$TmpRight-$BCode, 4, $this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
		}


		//DATI ATLETA
		$this->SetXY($TopX+0.2*$CellW, $TopY+($TopOffset*7/12));
		$this->SetFont($this->FontStd,'',8);
		$this->SetColors(false);
		$this->Cell($this->GetStringWidth((get_text('Archer') . ": ")),$TopOffset/6, (get_text('Archer') . ": "),'B',0,'L',0);
		$this->SetFont($this->FontStd,'B',8);
		$this->SetColors(true);
		$this->Cell($Width-(1.9*$CellW)-$this->GetStringWidth((get_text('Archer') . ": ")),$TopOffset/6, (array_key_exists("Ath",$Data) ? $Data["Ath"] : ' '),'B',0,'L',0);
		$this->SetXY($TopX+0.2*$CellW, $TopY+($TopOffset*19/24));
		$this->SetFont($this->FontStd,'',8);
		$this->SetColors(false);
		$this->Cell($this->GetStringWidth((get_text('Country') . ": ")),$TopOffset/6, (get_text('Country') . ": "),'B',0,'L',0);
		$this->SetFont($this->FontStd,'B',8);
		$this->SetColors(true);
		$CellTmpWidth=$Width-(1.9*$CellW)-$this->GetStringWidth((get_text('Country') . ": "));
		if(array_key_exists("Noc",$Data)) {
			$str=$Data['CoCode'].' -';
			$strW=$this->GetStringWidth($str);
			$this->Cell($strW, $TopOffset/6, $str,'B',0,'L',0);
			$this->Cell($CellTmpWidth-$strW, $TopOffset/6, $Data['CoName'],'B',0,'L',0);
		} else {
			$this->Cell($CellTmpWidth,$TopOffset/6, ' ','B',0,'L',0);
		}
		//PAGLIONE
		$this->SetXY($TopX+$Width-(1.4*$CellW), $TopY+($TopOffset*13/24));
		$this->SetFont($this->FontStd,'B',20);
		$this->SetColors(true);
		$HeaderTarget = ' ';
		if(array_key_exists("tNo",$Data)) {
			$HeaderTarget = trim($Data["tNo"],'0');
			if(!empty($Data["AtTarget"]) and $TargetNo!=intval($Data["AtTarget"])) {
				$HeaderTarget = $TargetNo . substr($Data["tNo"],-1,1) . '-' . $this->Indices[ceil($Data["AtTarget"]/(2*$NumEnd))-2];
			}
		}

		$this->Cell((1.4*$CellW),$TopOffset*7/24,$HeaderTarget,0,0,'R',1);
		$this->SetXY($TopX+$Width-(1.4*$CellW), $TopY+($TopOffset*10/12));
		$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(true);
		$this->Cell((1.4*$CellW),$TopOffset*2/12,(array_key_exists("Cat",$Data) ? $Data["Cat"] : ' '),'T',0,'C',1);
//####SCORE 1 ####////
		//HEADER DELLO SCORE 1
		$this->SetXY($TopX, $TopY+$TopOffset);
	   	$this->SetFont($this->FontStd,'I',8);
		$this->SetFillColor(0xF8,0xF8,0xF8);
		$this->SetColors(true,true);
		$this->Cell(0.8*$CellW,$CellH,(array_key_exists("Dist",$Data) ?
			$Data["Dist"] : ' '),0,0,'C',(array_key_exists("Dist",$Data) ?
			1 : 0));
		$this->SetFillColor(0xFF,0xFF,0xFF);
		if ($this->PrintDrawing)
			$this->SetFillColor(0xE8,0xE8,0xE8);

//	   	$this->SetFont($this->FontStd,'',6);
//		$this->Cell(0.9*$CellW,$CellH,($this->PrintDrawing ? get_text('Target') : ''),1,0,'C',1);
	   	$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(false);
		$this->Cell(0.9*(count($Target))*$CellW,$CellH, get_text('Arrow'), 1, 0, 'C', 1);
	   	$this->SetFont($this->FontStd,'B',8);
		$this->Cell(1.4*$CellW,$CellH, (get_text('TotalShort','Tournament')),1,0,'C',1);
		$this->Cell(0.7*$CellW,$CellH, ($this->prnGolds),1,0,'C',1);
		$this->Cell(0.7*$CellW,$CellH, ($this->prnXNine),1,1,'C',1);
		//RIGHE DELLO SCORE 1
		for($i=1; $i<=ceil($NumEnd); $i++)
		{
		   	$this->SetFont($this->FontStd,'B',10);
			$this->SetXY($TopX, $TopY+$TopOffset+$CellH*$i);
			$this->Cell(0.8*$CellW,$CellH,$TargetNo,1,0,'C',1);
		   	$this->SetFont($this->FontStd,'',10);
//			$this->Cell(0.9*$CellW,$CellH,'',1,0,'C',0);
			foreach($Target as $point) {
				$this->Cell(0.9*$CellW,$CellH, $point, 1, 0, 'C', 0);
			}
// 			$this->Cell(0.9*$CellW,$CellH, 'M', 1, 0, 'C', 0);
			$this->Cell(1.4*$CellW,$CellH,'',1,0,'C',0);
			$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
			$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
			if(++$TargetNo>($OnlyLeftScore ? $NumEnd : 2*$NumEnd))
				$TargetNo=1;
		}
/*
		//TOTALE DELLO SCORE 1
		$this->SetXY($TopX, $TopY+$TopOffset+$CellH*($NumEnd+1));
	   	$this->SetFont($this->FontStd,'B',10);
		$this->Cell(6.2*$CellW,$CellH, (get_text('Total1','Tournament') . " "),0,0,'R',0);
		$this->Cell(1.4*$CellW,$CellH,'',1,0,'C',0);
		$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
		$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
*/
//#### SCORE 2 ####////
		//HEADER DELLO SCORE 2
		$this->SetXY($TopX+($Width-5)/2+5, $TopY+$TopOffset);
	   	$this->SetFont($this->FontStd,'I',8);
		$this->SetFillColor(0xF8,0xF8,0xF8);
		$this->SetColors(true,true);
		$this->Cell(0.8*$CellW,$CellH,(array_key_exists("Dist",$Data) ?
$Data["Dist"] : ' '),0,0,'C',(array_key_exists("Dist",$Data) ?
1 : 0));
		$this->SetFillColor(0xFF,0xFF,0xFF);
		if ($this->PrintDrawing)
			$this->SetFillColor(0xE8,0xE8,0xE8);
//	   	$this->SetFont($this->FontStd,'',6);
//		$this->Cell(0.9*$CellW,$CellH,($this->PrintDrawing ? get_text('Target') : ''),1,0,'C',1);
	   	$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(false);
		$this->Cell(0.9*(count($Target))*$CellW,$CellH, get_text('Arrow'), 1, 0, 'C', 1);
	   	$this->SetFont($this->FontStd,'B',8);
		$this->Cell(1.4*$CellW,$CellH, (get_text('TotalShort','Tournament')),1,0,'C',1);
		$this->Cell(0.7*$CellW,$CellH, ($this->prnGolds),1,0,'C',1);
		$this->Cell(0.7*$CellW,$CellH, ($this->prnXNine),1,1,'C',1);
		//RIGHE DELLO SCORE 2
		for($i=1; $i<=floor($NumEnd); $i++)
		{
		   	$this->SetFont($this->FontStd,'B',10);
			$this->SetXY($TopX+($Width-5)/2+5, $TopY+$TopOffset+$CellH*$i);
			$this->Cell(0.8*$CellW,$CellH,$TargetNo,1,0,'C',1);
		   	$this->SetFont($this->FontStd,'',10);
//			$this->Cell(0.9*$CellW,$CellH,'',1,0,'C',0);
			foreach($Target as $point) {
				$this->Cell(0.9*$CellW,$CellH, $point, 1, 0, 'C', 0);
			}
// 			$this->Cell(0.9*$CellW,$CellH, 'M', 1, 0, 'C', 0);
			$this->Cell(1.4*$CellW,$CellH,'',1,0,'C',0);
			$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
			$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
			if(++$TargetNo>($OnlyLeftScore ? $NumEnd : 2*$NumEnd))
				$TargetNo=1;
		}
/*
		//TOTALE DELLO SCORE 2
		$this->SetXY($TopX+($Width-5)/2+5, $TopY+$TopOffset+$CellH*($NumEnd+1));
	   	$this->SetFont($this->FontStd,'B',10);
		$this->Cell(6.2*$CellW,$CellH, (get_text('Total2','Tournament') . " "),0,0,'R',0);
		$this->Cell(1.4*$CellW,$CellH,'',1,0,'C',0);
		$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
		$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
*/
		//TOTALE GENERALE
		$OldLine=$this->GetLineWidth();
		$this->SetLineWidth(0.5);
		$this->SetXY($TopX+($Width-5)/2+5, $TopY+$TopOffset+$CellH*($NumEnd+1)+1);
	   	$this->SetFont($this->FontStd,'B',10);
		$this->Cell(((0.9*(count($Target)-1)) + 1.7)*$CellW,$CellH, (get_text('Total') . " "),0,0,'R',0);
		$this->Cell(1.4*$CellW,$CellH,'',1,0,'C',0);
		$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
		$this->Cell(0.7*$CellW,$CellH,'',1,1,'C',0);
		$this->SetLineWidth(0.2);
		//Se solo score di SINISTRA
		if($OnlyLeftScore)
		{
			$this->SetLineWidth(0.5);
			$this->Line($TopX+($Width-5)/2+5, $TopY+$TopOffset, $TopX+$Width, $TopY+$TopOffset+$CellH*($NumEnd+2));
			$this->SetLineWidth(0.2);
		}
		//FIRME
		$this->SetFont($this->FontFix,'BI',6);
		$this->Cell(4, 3, '', 0, 0, 'C', 0);
		$this->Cell($Width/2-7, 3, (get_text('Archer')), 'B', 0, 'L', 0);
		$this->Cell(6, 3, '', 0, 0, 'C', 0);
		$this->Cell($Width/2-7,3,(get_text('Scorer')),'B',1,'L',0);
		$this->Cell(4,3,'',0,1,'C',0);
		$this->Cell(4,3,'',0,0,'C',0);
		$this->Cell($Width-8,3,(get_text('JudgeNotes')),'B',0,'L',0);

		$this->SetLineWidth($OldLine);
		//$this->Rect($TopX, $TopY, $Width, $Height);
	}

}
?>
