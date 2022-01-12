<?php

if(!defined('PRINTLANG')) {
    define('PRINTLANG', 'EN');
}
require_once('Common/pdf/IanseoPdf.php');
require_once('Common/Lib/Fun_DateTime.inc.php');


class OrisPDF extends IanseoPdf {
	var $Number, $Title, $Event, $EvPhase, $EvComment;
	var $Name, $Where, $WhenF, $WhenT, $imgR, $imgL, $imgB, $imgB2, $prnGolds, $prnXNine;
	var $HeaderName = array();
	var $HeaderSize = array();
	var $DataSize = array();
	var $lastY=0;
	var $utsReportCreated=0;
	var $Records=array();
	var $RecCelHeight=4.5;
	var $StopHeader=false;
	var $FooterPrefix='AR_';
	var $Version='';

	const leftMargin=10;
	const topMargin=15;
    const bottomMargin=16;
	const topStart=45;
	var $extraBottomMargin=0;
	var $printPageNo=true;


	//Constructor
	function __construct($DocNumber, $DocTitle, $headers='') {
		parent::__construct($DocTitle, true, $headers);
		if($this->ToPaths['ToBottom']) {
            $im=getimagesize($this->ToPaths['ToBottom']);
            if($im[0]/$im[1] < 17) {
                $this->extraBottomMargin = 8;
            }
        //} else {
        //    $this->extraBottomMargin = -5;
        }
		$this->Title=$DocTitle;
		$this->Number=$DocNumber;
		$this->Event='';
		$this->EvPhase='';
		if(isset($_REQUEST["ReportCreated"]) && preg_match("/^[0-9]{12}$/i", $_REQUEST["ReportCreated"])) {
            $this->utsReportCreated = mktime(substr($_REQUEST["ReportCreated"], 8, 2), substr($_REQUEST["ReportCreated"], 10, 2), 0, substr($_REQUEST["ReportCreated"], 4, 2), substr($_REQUEST["ReportCreated"], 6, 2), substr($_REQUEST["ReportCreated"], 0, 4));
        } else {
            $this->utsReportCreated = strtotime("now");
        }

		$this->SetSubject($DocNumber . ' - ' . $DocTitle);
		$this->SetDefaultColor();

		$this->SetMargins(OrisPDF::leftMargin,OrisPDF::topMargin,OrisPDF::leftMargin);

		$this->SetAutoPageBreak(true,OrisPDF::bottomMargin+$this->extraBottomMargin);
	}

	public function setDocUpdate($newDate) {
		$this->utsReportCreated = mktime(substr($newDate,11,2),substr($newDate,14,2),0,substr($newDate,5,2),substr($newDate,8,2),substr($newDate,0,4));
	}

	function SetDefaultColor() {
		$this->SetDrawColor(0x00, 0x00, 0x00);
		$this->SetFillColor(0xE0,0xE0,0xE0);
		$this->SetTextColor(0x00, 0x00, 0x00);
	}

	function SetTextRed() {
		$this->SetTextColor(0xFF, 0x00, 0x00);
	}

	function SetTextOrange() {
		$this->SetTextColor(0xEE, 0x76, 0x00);
	}

	function SetTextGreen() {
		$this->SetTextColor(0x4C, 0xC4, 0x17);
	}


	function Header() {
		global $CFG;
		$LeftStart = 10;
		$RightStart = 10;
		$ImgSizeReq=20;

		//Immagini
		if($this->ToPaths['ToLeft']) {
			$im=getimagesize($this->ToPaths['ToLeft']);
			$this->Image($this->ToPaths['ToLeft'], 10, 5, 0, $ImgSizeReq);
			$LeftStart += ($im[0] * $ImgSizeReq / $im[1]);
		}
		if($this->ToPaths['ToRight']) {
			$im=getimagesize($this->ToPaths['ToRight']);
			$this->Image($this->ToPaths['ToRight'], (($this->w-10) - ($im[0] * $ImgSizeReq / $im[1])), 5, 0, $ImgSizeReq);
			$RightStart += ($im[0] * $ImgSizeReq / $im[1]);
		}

		//Where & When
		$this->SetXY($LeftStart,5);
		$this->SetFont($this->FontStd,'',10);
		$this->MultiCell(40, 5, $this->Where,0,'L');
		$this->SetXY($LeftStart,20);
	// patch
		$this->MultiCell(40, 5, TournamentDate2StringShort($this->DtFrom,$this->DtTo),0,'L');

		//Competition Name
		$this->SetXY($LeftStart+40,5);
		$this->SetFont($this->FontStd,'B',11);
		$this->Cell($this->w-$LeftStart-$RightStart-40, 5, preg_replace("/[\r\n]+/sim", ' ', $this->Name),0,0,'L');

		//Event Name if available
		if($this->Event != '') {
			$this->SetXY($LeftStart+40,12.5);
			$this->SetFont($this->FontStd,'B',11);
			$this->Cell($this->w-$LeftStart-$RightStart-40, 5, $this->Event,0,0,'L');
		}

		//Event Phase if available
		if($this->EvPhase != '') {
			$this->SetXY($LeftStart+40,19.5);
			$this->SetFont($this->FontStd,'B',11);
			$this->Cell($this->w-$LeftStart-$RightStart-40, 5, $this->EvPhase,0,0,'L');
		}


		//Linea di divisione
		$this->SetLineWidth(0.3);
		$this->Line(5,30,$this->w-5,30);
		$this->SetLineWidth(0.1);

		//Report Title
		$this->SetXY(10,30);
		$this->SetFont($this->FontStd,'B',12);
		$this->Cell(190,7,mb_convert_case($this->Title, MB_CASE_UPPER, "UTF-8"),0,1,'C');

		//Comment if available
		if($this->EvPhase != '') {
			$this->SetXY(145,30);
			$this->SetFont($this->FontStd,'B',8);
			$this->Cell(60,7,$this->EvComment,0,1,'R');
		}

		$this->SetFont($this->FontStd,'',8);

		$this->lastY = OrisPDF::topStart-4;

		$this->SetXY(OrisPDF::leftMargin, $this->lastY);

		// Prints Records if available...
		// set defaults cell padding ;)
		$OldPadding=$this->cell_padding;
		$this->setCellPaddings(1,0,1,0);
		foreach($this->Records as $Record) {
			$Rows=0;
			foreach($Record->RtRecExtra as $Extra) {
				$Rows+=$this->RecCelHeight;
			}
			// what
			$this->SetFont('', 'B');
			$this->cell(45, $Rows, $Record->TrHeader.' '.$Record->RtRecDistance.':', 'LTB', 0);
			$this->SetFont('', '');
			// how much
			$this->cell(10, $Rows, $Record->RtRecTotal.($Record->RtRecXNine ? '/'.$Record->RtRecXNine : ''), 'TB', 0, 'R');
			$X=$this->getX();
			$Y=$this->getY();
			foreach($Record->RtRecExtra as $k=>$Extra) {
				$this->SetXY($X, $Y+$k*$this->RecCelHeight);
				$arc=array();
				foreach($Extra->Archers as $t => $Archer) {
					$arc[]=$Archer['Archer'];
				}
				// who
				$this->cell(80, $this->RecCelHeight, implode('/', $arc), 'TB', 0, 'R');
				// NOC
				$this->cell(10, $this->RecCelHeight, $Extra->NOC, 'TB', 0);
				// where (NOC)
				$this->cell(30, $this->RecCelHeight, $Extra->EventNOC, 'TB', 0, 'R');
			}
			// date
			$this->SetXY($X+120, $Y);
			$this->cell(0, $Rows, $Record->RtRecDate, 'TBR', 1, 'R');

			$this->lastY+=$Rows;
		}
		$this->lastY+=3;


		//Report Table Header
		if(!$this->StopHeader and count($this->HeaderName)>0) {
			$this->printHeader(OrisPDF::leftMargin, $this->lastY);
		}
		$this->cell_padding=$OldPadding;
	}


	function Footer() {
		global $CFG;

        $TopStart = ($this->h-(15 + $this->extraBottomMargin));

		$this->SetLineWidth(0.3);
		$this->Line(5, $TopStart, ($this->w-5), $TopStart);
		$this->SetLineWidth(0.1);

		$this->SetFont($this->FontStd,'',8);
		$this->SetXY(10,$TopStart);
		$this->Cell(60,3,mb_convert_case($this->FooterPrefix . $this->Number, MB_CASE_UPPER, "UTF-8"),0,0,'L');
        if($this->printPageNo) {
		    $this->SetXY($this->w-60,$TopStart);
            $this->Cell(60, 3, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
        }
		$this->SetXY($this->w/2-30,$TopStart);
		$this->Cell(60,3,'Report Created: ' . date('d M Y H:i',$this->utsReportCreated).' @ UTC'.$this->TzOffset.($this->Version ? ' (v. '.$this->Version.')' : ''),0,0,'C');

		$im=NULL;
		if($this->ToPaths['ToBottom']) {
			$im=getimagesize($this->ToPaths['ToBottom']);
            $imgwidth = $im[0] * (7 + $this->extraBottomMargin) / $im[1];
            $this->Image($this->ToPaths['ToBottom'], ($this->w-$imgwidth)/2, $this->h-(11+$this->extraBottomMargin), 0, (7+$this->extraBottomMargin));
		}
		$this->SetFont($this->FontStd,'',8);

	}

	function setDataHeader($FieldNames, $FieldSizes) {
		$this->HeaderName=array();
		$this->HeaderSize=array();
		$this->DataSize=array();
		if(end($FieldSizes)==0) {
			$FieldSizes[count($FieldSizes)-1]=$this->getPageWidth()-$this->lMargin-$this->rMargin-array_sum($FieldSizes);
		}

		if(!is_array($FieldNames)) {
            $this->HeaderName[] = $FieldNames;
        } else {
            $this->HeaderName = $FieldNames;
        }

		if(!is_array($FieldSizes)) {
			$this->HeaderSize[] = $FieldSizes;
			$this->DataSize[] = $FieldSizes;
		} else {
			foreach($FieldSizes as $fs) {
				if(!is_array($fs)) {
					$this->HeaderSize[] = $fs;
					$this->DataSize[] = $fs;
				} else {
					$this->HeaderSize[] = array_sum($fs);
					$this->DataSize = array_merge($this->DataSize,$fs);
				}
			}
		}
	}

	function printHeader($xPosition, $yPosition) {
		$maxCell= 0;
		$this->SetLineWidth(0.1);
		$this->SetFont($this->FontStd,'B',8);
		$this->SetXY($xPosition, $yPosition);
		$Rows=3.5;
		if(strstr(implode('', $this->HeaderName), "\n")) {
			$Rows=7;
		}
		foreach($this->HeaderName as $i => $Header) {
			if($Header and $Header[0]=='@') {
				$Header=substr($Header, 1);
			}

			$Align='L';
			if(strstr($Header, '#')) {
				$Align='R';
			} elseif(strstr($Header, '§')) {
				$Align='C';
			}

			if(strstr($Header, "\n")) {
				$Header=explode("\n", $Header);
				$cHeight=3.5;
			} else {
				$Header=array($Header);
				$cHeight=$Rows;
			}

			$OrgX=$this->getx();

			foreach($Header as $j => $Head) {
				if(strstr($Head, '#')) {
					$Align='R';
				} elseif(strstr($Head, '§')) {
					$Align='C';
				}
				$Head=str_replace(array("#",'§'),"", $Head);
				$this->SetXY($OrgX, $yPosition+$j*3.5);
				$this->Cell($this->HeaderSize[min($i,count($this->HeaderSize)-1)], $cHeight, $Head,0,0, $Align);
			}
		}
		$this->Rect($xPosition, $yPosition-1, $this->getPageWidth()-20,$Rows+2);
		$this->SetFont($this->FontStd,'',8);
		$this->lastY = $yPosition+$Rows+2;
	}

	function addSpacer($size=2) {
		$this->lastY += $size;
	}

	function printDataRow($data) {
		$maxCell= 1;
		$this->SetFont($this->FontStd,'',8);
        $this->samePage(2, 3.5, $this->lastY); // check if there is enough space from the future location
		$this->SetXY(OrisPDF::leftMargin, $this->lastY); // sets the correct location after eventually the reset of lastY made by the setheader in case of a new page
		for($i=0; $i<count($data); $i++) {
			$Align='L';
			if(strstr($data[$i],"#")) {
				$Align='R';
			} elseif(strstr($data[$i],"§")) {
				$Align='C';
			}
            if(strstr($data[$i],"~")) {
                $this->SetFont('', 'B');
            }
			if(strstr($data[$i], "\n")) {
				$CellData=explode("\n", $data[$i]);
				$OrgX=$this->GetX();
				$OrgY=$this->GetY();
				$maxCell=count($CellData);
				foreach($CellData as $k=>$v) {
					$this->SetXY($OrgX, $OrgY + ($k*3.5));
					if(!$k) {
						// first line bold
						$this->SetFont('', 'B');
					}
					$this->Cell($this->DataSize[min($i,count($this->DataSize)-1)],3.5, str_replace(array("#","§"),"", $v),0,0, $Align);
					$this->SetFont('', '');
				}
				$this->SetXY($this->GetX(), $OrgY);
				//$maxCell = max($maxCell, $this->MultiCell($this->DataSize[min($i,count($this->DataSize)-1)],3.5,str_replace(array("#","§"),"",$data[$i]),0, $Align, 0, 0));
			} else {
				$this->Cell($this->DataSize[min($i,count($this->DataSize)-1)],3.5,str_replace(array("#","§","~"),"",$data[$i]),0,0, $Align);
			}
            if(strstr($data[$i],"~")) {
                $this->SetFont('', '');
            }
		}
		$this->lastY += 3.5*$maxCell;
	}

	function printSectionTitle($text, $y=null) {
		$this->SetFont($this->FontStd,'B',10);
		$this->SetXY(OrisPDF::leftMargin, is_null($y) ? $this->lastY : max(OrisPDF::topStart, $y));
		$this->Cell(0.1,5,'',0,0,'L');
		$this->SetXY(OrisPDF::leftMargin, is_null($y) ? $this->lastY : max(OrisPDF::topStart, $y));
		$this->Cell(array_sum($this->DataSize),5,str_replace(array("#",'§'),"",$text),0,0,(strpos($text,"#")===false ? (strpos($text,"§")===false ? 'L':'C'):'R'));
		$this->lastY += 5;
	}

	function setEvent($name) {
		$this->Event = $name;
	}

	function setPhase($name) {
		$this->EvPhase = $name;
	}

	function setComment($comment) {
		$this->EvComment = $comment;
	}

	function samePage($rowNo, $rowHeight=3.5, $y='', $addPage=true) {
		return !$this->checkPageBreak($rowNo * $rowHeight, $y, $addPage);
	}

	function setOrisCode($newCode='', $newTitle='', $force=false) {
		if($newCode != '' or $force) {
			$this->Number=$newCode;
		}
		if($newTitle != '' or $force) {
			$this->Title=$newTitle;
		}
	}

	function setPrintPageNo($doPrint) {
	    $this->printPageNo = $doPrint;
    }

    function OrisScorecard($Data, $Bottom=0, $Phase, $Section, $Meta, $Team=0) {
	    if($Phase['FinElimChooser']) {
	    	$Ends=$Section['elimEnds'];
	    	$Arrows=$Section['elimArrows'];
	    	$SO=$Section['elimSO'];
	    } else {
	    	$Ends=$Section['finEnds'];
	    	$Arrows=$Section['finArrows'];
	    	$SO=$Section['finSO'];
	    }

		$CellHeight=5;
		$ScoreWidth=($this->getPageWidth()-30)/2;
		$CellWidthShort=8+($Arrows==3 ? 4 : 0);
		$CellWidthLong=($ScoreWidth-3-$CellWidthShort*(1 + $Arrows))/2;
		$SoWidth=min($CellWidthShort, ($CellWidthShort*$Arrows)/$SO);
		$SoGap=$CellWidthShort*$Arrows - $SoWidth*$SO;
	    $HeadWidthTitle=20;
	    $HeadWidthData=$ScoreWidth-$HeadWidthTitle;

		$Offset=35+($Bottom ? $this::topMargin+($this->getPageHeight()-35-$this->extraBottomMargin-$this::bottomMargin-$this::topMargin)/2 : 0);
		$this->SetY($Offset, true);
		$this->SetFont('','b',12);
		$this->Cell(0,0, $Phase['matchName'], '', '1','C');
		$this->SetFont('','',10);
		$this->Cell(0,0, date('D j M Y', strtotime($Data['scheduledKey'])).' '.$Meta['fields']['scheduledTime'].': '.$Data['scheduledTime'], '', '1','C');
        if($Data['odfMatchName']!=0) {
            $this->Cell(0,0, 'Match Number ' . $Data['odfMatchName'], '', '1','C');
        }
		$this->ln();

		// line 1: targets
	    $this->Cell($HeadWidthTitle, 0, $Meta['fields']['target'].':');
	    $this->Cell($HeadWidthData, 0, ltrim($Data['target'],'0'));
	    $this->Cell(10,0, '');
	    $this->Cell($HeadWidthTitle, 0, $Meta['fields']['target'].':');
	    $this->Cell($HeadWidthData, 0, ltrim($Data['oppTarget'],'0'));
	    $this->ln();

	    //line 2: NOC / athlete
	    if($Team) {
		    $this->Cell($HeadWidthTitle, 0, $Meta['fields']['countryCode'] .':');
		    $this->SetFont('','b');
		    $this->Cell($HeadWidthData,0, $Data['countryCode']);
		    $this->SetFont('','');
		    $this->Cell(10,0, '');
		    $this->Cell($HeadWidthTitle, 0, $Meta['fields']['countryCode'].':');
		    $this->SetFont('','b');
		    $this->Cell($HeadWidthData,0, $Data['oppCountryCode']);
		    $this->SetFont('','');
	    } else {
		    $this->Cell($HeadWidthTitle, 0, $Meta['fields']['fullName'].':');
		    $this->SetFont('','b');
		    $this->Cell($HeadWidthData,0, $Data['athlete'].' ('.$Data['countryCode'].' - '.$Data['countryName'].')');
		    $this->SetFont('','');
		    $this->Cell(10,0, '');
		    $this->Cell($HeadWidthTitle, 0, $Meta['fields']['fullName'].':');
		    $this->SetFont('','b');
		    $this->Cell($HeadWidthData,0, $Data['oppAthlete'].' ('.$Data['oppCountryCode'].' - '.$Data['oppCountryName'].')');
		    $this->SetFont('','');
	    }
	    $this->ln();

	    //line 3: Bib / Country
	    if($Team) {
		    $this->Cell($HeadWidthTitle, 0, $Meta['fields']['countryName'] . ':');
		    $this->SetFont('', 'b');
		    $this->Cell($HeadWidthData, 0, $Data['countryName']);
		    $this->SetFont('', '');
		    $this->Cell(10, 0, '');
		    $this->Cell($HeadWidthTitle, 0, $Meta['fields']['countryName'] . ':');
		    $this->SetFont('', 'b');
		    $this->Cell($HeadWidthData, 0, $Data['oppCountryName']);
		    $this->SetFont('', '');
	    } else {
		    $this->Cell($HeadWidthTitle, 0, $Meta['fields']['bib'].':');
		    $this->Cell($HeadWidthData,0, $Data['bib']);
		    $this->Cell(10,0, '');
		    $this->Cell($HeadWidthTitle, 0, $Meta['fields']['bib'].':');
		    $this->Cell($HeadWidthData,0, $Data['oppBib']);
	    }
	    $this->ln();

	    //line 4: Coach?
        if(!empty($Data['coach']) OR !empty($Data['oppCoach'])) {
            $this->Cell($HeadWidthTitle, 0, $Meta['fields']['coach'] . ':');
            $this->Cell($HeadWidthData, 0, $Data['coach']);
            $this->Cell(10, 0, '');
            $this->Cell($HeadWidthTitle, 0, $Meta['fields']['coach'] . ':');
            $this->Cell($HeadWidthData, 0, $Data['oppCoach']);
            $this->ln();
        }
	    // empty line
	    $this->ln(2);

	    // Winner / IRM status, BOXED
	    $this->SetFontSize(13);
	    $Txt='';
	    if($Data['irm']) {
	    	$Txt=$Data['irmText'];
	    } elseif($Data['winner']) {
	    	$Txt=$Meta['fields']['winner'];
	    }
	    $this->Cell($ScoreWidth,0, $Txt, $Txt ? 1 : 0, 0, 'C');
	    $this->Cell(10,0, '');
	    $Txt='';
	    if($Data['oppIrm']) {
	    	$Txt=$Data['oppIrmText'];
	    } elseif($Data['oppWinner']) {
	    	$Txt=$Meta['fields']['winner'];
	    }
	    $this->Cell($ScoreWidth,6, $Txt, $Txt ? 1 : 0, 0, 'C');
	    $this->ln();
	    $this->SetFontSize(10);

	    // empty line
	    $this->ln(2);

	    // score drawing
	    // head
	    $this->Cell($CellWidthShort, $CellHeight,'',1);
        $this->Cell($CellWidthShort*$Arrows, $CellHeight, $Meta['fields']['arrowstring'],1, '0', 'C');
        $this->Cell($CellWidthLong, $CellHeight, $Meta['fields']['score'],1, 0, 'C');
	    $this->Cell(3, $CellHeight, '');
        $this->Cell($CellWidthLong, $CellHeight, $Section['matchMode'] ? $Meta['fields']['setPoints'] : $Meta['fields']['scoreLong'],1, 0, 'C');

	    $this->Cell(10, $CellHeight, '');

        $this->Cell($CellWidthLong, $CellHeight, $Section['matchMode'] ? $Meta['fields']['setPoints'] : $Meta['fields']['scoreLong'],1, 0, 'C');
	    $this->Cell(3, $CellHeight, '');
	    $this->Cell($CellWidthShort, $CellHeight,'',1);
        $this->Cell($CellWidthShort*$Arrows, $CellHeight, $Meta['fields']['arrowstring'],1, 0, 'C');
        $this->Cell($CellWidthLong, $CellHeight, $Meta['fields']['score'],1, 0, 'C');
	    $this->ln();

        $endTot=explode('|', $Data['setPoints']);
        $endPts=explode('|', $Data['setPointsByEnd']);
        $oppEndTot=explode('|', $Data['oppSetPoints']);
        $oppEndPts=explode('|', $Data['oppSetPointsByEnd']);
        $Tot=0;
        $OppTot=0;
        for($i=0;$i<$Ends;$i++) {
	        $this->Cell($CellWidthShort, $CellHeight,$i+1,1, 0, 'C');
	        $pts='';
        	for($j=0;$j<$Arrows;$j++) {
        		$pts=substr($Data['arrowstring'], $i*$Arrows + $j,1);
	            $this->Cell($CellWidthShort, $CellHeight, DecodeFromLetter($pts),1, 0, 'C');
	        }
			if(trim($pts)) {
				$Tot+=$endTot[$i];
		        $this->Cell($CellWidthLong, $CellHeight, $endTot[$i],1, 0, 'C');
	            $this->Cell(3, $CellHeight, '');
		        $this->Cell($CellWidthLong, $CellHeight, $Section['matchMode'] ? $endPts[$i] : $Tot,1, 0, 'C');
			} else {
		        $this->Cell($CellWidthLong, $CellHeight, '',1, 0, 'C');
	            $this->Cell(3, $CellHeight, '');
		        $this->Cell($CellWidthLong, $CellHeight, '',1, 0, 'C');
			}

			$this->Cell(10, $CellHeight, '');

            $pts=substr($Data['oppArrowstring'], $i*$Arrows,1);
			if(trim($pts)) {
				$OppTot+=$oppEndTot[$i];
		        $this->Cell($CellWidthLong, $CellHeight, $Section['matchMode'] ? $oppEndPts[$i] : $OppTot,1, 0, 'C');
			} else {
		        $this->Cell($CellWidthLong, $CellHeight, '',1, 0, 'C');
			}
            $this->Cell(3, $CellHeight, '');
	        $this->Cell($CellWidthShort, $CellHeight,$i+1,1, 0, 'C');
        	for($j=0;$j<$Arrows;$j++) {
        		$pts=substr($Data['oppArrowstring'], $i*$Arrows + $j,1);
	            $this->Cell($CellWidthShort, $CellHeight, DecodeFromLetter($pts),1, 0, 'C');
	        }
			if(trim($pts)) {
		        $this->Cell($CellWidthLong, $CellHeight, $oppEndTot[$i],1, 0, 'C');
			} else {
		        $this->Cell($CellWidthLong, $CellHeight, '',1, 0, 'C');
			}
	        $this->ln();
        }

        $this->ln(2);

		// SO
	    if($Data['tie'] or $Data['oppTie']) {
	    	$Rows=ceil(strlen(trim($Data['tiebreak']))/$SO);
	    	$Ties=explode(',', $Data['tiebreakDecoded']);
	    	$OppTies=explode(',', $Data['oppTiebreakDecoded']);
	        for($i=0; $i<$Rows; $i++) {
		        $this->Cell($CellWidthShort, $CellHeight,$Meta['fields']['tie'].' '.($i+1),1,0,'C');
		        $pts='';
		        for($j=0;$j<$SO;$j++) {
		            $pts=substr($Data['tiebreak'], $i*$SO + $j,1);
		            $this->Cell($SoWidth, $CellHeight, DecodeFromLetter($pts),1,0,'C');
		        }

		        if($SoGap) {
			        $this->Cell($SoGap, $CellHeight, '');
		        }

		        if($SO>1) {
			        $this->Cell($CellWidthLong, $CellHeight, $Ties[$i],1,0,'C');
		        } else {
			        $this->Cell($CellWidthLong, $CellHeight, '');
		        }

		        // closest to center goes only on last row
		        if($i==$Rows-1) {
			        //$this->Cell(1,$CellHeight,'');
			        //$this->Cell($CellHeight, $CellHeight,$Data['closest'] ? '+' : '', 1);
			        //$this->Cell(1,$CellHeight,'');
			        //$this->Cell($ClosestWidth, $CellHeight, $Meta['fields']['closestShort']);
			        $this->Cell(3,$CellHeight,'');
			        if($Section['matchMode']) {
				        $this->Cell($CellWidthLong, $CellHeight, $Data['tie'], 1,0,'C');
			        } else {
				        $this->Cell($CellWidthLong, $CellHeight, '', 0,0,'C');
			        }
		        } else {
			        $this->Cell(3+$CellWidthLong,$CellHeight,'');
		        }

				$this->Cell(10, $CellHeight, '');

		        if($i==$Rows-1) {
			        if($Section['matchMode']) {
				        $this->Cell($CellWidthLong, $CellHeight, $Data['oppTie'], 1,0,'C');
			        } else {
				        $this->Cell($CellWidthLong, $CellHeight, '', 0,0,'C');
			        }
			        $this->Cell(3,$CellHeight,'');
		        } else {
			        $this->Cell(3+$CellWidthLong, $CellHeight,'');
		        }
		        $this->Cell($CellWidthShort, $CellHeight,$Meta['fields']['tie'].' '.($i+1),1,0,'C');
		        $pts='';
		        for($j=0;$j<$SO;$j++) {
		            $pts=substr($Data['oppTiebreak'], $i*$SO + $j,1);
		            $this->Cell($SoWidth, $CellHeight, DecodeFromLetter($pts),1,0,'C');
		        }

		        if($SoGap) {
			        $this->Cell($SoGap, $CellHeight, '');
		        }

		        if($SO>1) {
			        $this->Cell($CellWidthLong, $CellHeight, $OppTies[$i],1,0,'C');
		        } else {
			        $this->Cell($CellWidthLong, $CellHeight, '',0,0,'C');
		        }

		        $this->ln();
	        }
	        $this->ln(2);
	    }

	    // Closest+TOTALS
        $this->Cell($CellWidthShort*(1+$Arrows)+$CellWidthLong+3, $CellHeight, $Data['closest'] ? $Meta['fields']['closest'] : '');
	    $this->SetFont('', 'b');
        $this->Cell($CellWidthLong, $CellHeight, $Section['matchMode'] ? $Data['setScore'] : $Data['score'], 1, 0, 'C');
		$this->Cell(10, $CellHeight, '');
        $this->Cell($CellWidthLong, $CellHeight, $Section['matchMode'] ? $Data['oppSetScore'] : $Data['oppScore'], 1, 0, 'C');
	    $this->SetFont('', '');
        $this->Cell(3,$CellHeight, '');
        $this->Cell($CellWidthShort*(1+$Arrows), $CellHeight, $Data['oppClosest'] ? $Meta['fields']['closest'] : '');

	    //last line: Judges?
        if(!empty($Data['lineJudge']) OR !empty($Data['targetJudge'])) {
            $this->ln(8);
            $this->Cell($CellWidthShort*(1+$Arrows)+$CellWidthLong+3,0,'');
            $this->Cell($CellWidthLong, 0, $Meta['fields']['lineJudge'] . ':');
            $this->Cell(0, 0, $Data['lineJudge'],0,1);
            $this->Cell($CellWidthShort*(1+$Arrows)+$CellWidthLong+3,0,'');
            $this->Cell($CellWidthLong, 0, $Meta['fields']['targetJudge'] . ':');
            $this->Cell(0, 0, $Data['targetJudge'],0,1);
        }
	    return;
    }
}
