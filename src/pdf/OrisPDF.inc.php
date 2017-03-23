<?php

define('PRINTLANG', 'EN');
require_once('Common/pdf/IanseoPdf.php');
require_once('Common/Lib/Fun_DateTime.inc.php');


class OrisPDF extends IanseoPdf
{
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

	const leftMargin=10;
	const topMargin=15;
	const bottomMargin=16;
	const topStart=45;


	//Constructor
	function __construct($DocNumber, $DocTitle, $headers='')
	{
		parent::__construct($DocTitle, true, $headers);
// 		$this->AliasNbPages('{}');
		$this->Title=$DocTitle;
		$this->Number=$DocNumber;
		$this->Event='';
		$this->EvPhase='';
		if(isset($_REQUEST["ReportCreated"]) && preg_match("/^[0-9]{12}$/i", $_REQUEST["ReportCreated"]))
			$this->utsReportCreated = mktime(substr($_REQUEST["ReportCreated"],8,2),substr($_REQUEST["ReportCreated"],10,2),0,substr($_REQUEST["ReportCreated"],4,2),substr($_REQUEST["ReportCreated"],6,2),substr($_REQUEST["ReportCreated"],0,4));
		else
			$this->utsReportCreated = strtotime("now");

		$this->SetSubject($DocNumber . ' - ' . $DocTitle);
		$this->SetDefaultColor();

		$this->SetMargins(OrisPDF::leftMargin,OrisPDF::topMargin,OrisPDF::leftMargin);
		$this->SetAutoPageBreak(true,OrisPDF::bottomMargin);
	}

	public function setDocUpdate($newDate)
	{
		$this->utsReportCreated = mktime(substr($newDate,11,2),substr($newDate,14,2),0,substr($newDate,5,2),substr($newDate,8,2),substr($newDate,0,4));
	}

	function SetDefaultColor()
	{
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


	function Header()
	{
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
		if($this->Event != '')
		{
			$this->SetXY($LeftStart+40,12.5);
			$this->SetFont($this->FontStd,'B',11);
			$this->Cell($this->w-$LeftStart-$RightStart-40, 5, $this->Event,0,0,'L');
		}

		//Event Phase if available
		if($this->EvPhase != '')
		{
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
		if($this->EvPhase != '')
		{
			$this->SetXY(145,30);
			$this->SetFont($this->FontStd,'B',8);
			$this->Cell(60,7,$this->EvComment,0,1,'R');
		}

		$this->SetFont($this->FontStd,'',8);

		$this->lastY = OrisPDF::topStart-4;

		$this->SetXY(OrisPDF::leftMargin, $this->lastY);

		// Prints Records if available...
		foreach($this->Records as $Record) {
			$Rows=0;
			foreach($Record->RtRecExtra as $Extra) {
				$Rows+=$this->RecCelHeight;
			}
			// what
			$this->SetFont('', 'B');
			$this->cell(40, $Rows, ' '.$Record->RtRecType.' '.$Record->RtRecDistance.':', 'LTB', 0);
			$this->SetFont('', '');
			// how much
			$this->cell(10, $Rows, $Record->RtRecTotal.($Record->RtRecXNine ? '/'.$Record->RtRecXNine : '').' ', 'TB', 0, 'R');
			$X=$this->getX();
			$Y=$this->getY();
			foreach($Record->RtRecExtra as $k=>$Extra) {
				$this->SetX($X, $Y+$k*$this->RecCelHeight);
				$arc=array();
				foreach($Extra->Archers as $t => $Archer) {
					$arc[]=$Archer['Archer'];
				}
				// who
				$this->cell(80, $this->RecCelHeight, ' '.implode('/', $arc). ' ', 'TB', 0, 'R');
				// NOC
				$this->cell(10, $this->RecCelHeight, " $Extra->NOC ", 'TB', 0);
				// where (NOC)
				$this->cell(30, $this->RecCelHeight, " $Extra->EventNOC ", 'TB', 0, 'R');
			}
			// date
			$this->SetX($X+120, $Y);
			$this->cell(20, $Rows, " $Record->RtRecDate ", 'TBR', 1, 'R');

			$this->lastY+=$Rows;
		}
		$this->lastY+=3;


		//Report Table Header
		if(!$this->StopHeader and count($this->HeaderSize)>0) {
			$this->printHeader(OrisPDF::leftMargin, $this->lastY);
		}
	}


	function Footer()
	{
		global $CFG;
		$TopStart = ($this->h-15);

		$this->SetLineWidth(0.3);
		$this->Line(5, $TopStart, ($this->w-5), $TopStart);
		$this->SetLineWidth(0.1);

		$this->SetFont($this->FontStd,'',8);
		$this->SetXY(10,$TopStart);
		$this->Cell(60,3,mb_convert_case("AR_" . $this->Number, MB_CASE_UPPER, "UTF-8"),0,0,'L');
		$this->SetXY($this->w-70,$TopStart);
		$this->Cell(60,3,'Page '.$this->PageNo().'/'.$this->getAliasNbPages(),0,0,'R');
		$this->SetXY($this->w/2-30,$TopStart);
		$this->Cell(60,3,'Report Created: ' . date('d M Y H:i',$this->utsReportCreated).' @ UTC'.$this->TzOffset,0,0,'C');

		$im=NULL;
		if($this->ToPaths['ToBottom']) {
			$im=getimagesize($this->ToPaths['ToBottom']);
			$imgwidth = $im[0] * 7 / $im[1];
			$this->Image($this->ToPaths['ToBottom'], ($this->w-$imgwidth)/2, $this->h-11, 0, 7);
		}
		$this->SetFont($this->FontStd,'',8);

	}

	function setDataHeader($FieldNames, $FieldSizes)
	{
		$this->HeaderName=array();
		$this->HeaderSize=array();
		$this->DataSize=array();
		if(end($FieldSizes)==0) {
			$FieldSizes[count($FieldSizes)-1]=$this->getPageWidth()-$this->lMargin-$this->rMargin-array_sum($FieldSizes);
		}

		if(!is_array($FieldNames))
			$this->HeaderName[] = $FieldNames;
		else
			$this->HeaderName = $FieldNames;

		if(!is_array($FieldSizes))
		{
			$this->HeaderSize[] = $FieldSizes;
			$this->DataSize[] = $FieldSizes;
		}
		else
		{
			foreach($FieldSizes as $fs)
			{
				if(!is_array($fs))
				{
					$this->HeaderSize[] = $fs;
					$this->DataSize[] = $fs;
				}
				else
				{
					$this->HeaderSize[] = array_sum($fs);
					$this->DataSize = array_merge($this->DataSize,$fs);
				}
			}
		}

	}

	function printHeader($xPosition, $yPosition)
	{
		$maxCell= 0;
		$this->SetLineWidth(0.1);
		$this->SetFont($this->FontStd,'B',8);
		$this->SetXY($xPosition, $yPosition);
		for($i=0; $i<count($this->HeaderName); $i++)
		{
			if(strpos($this->HeaderName[$i],"@")===0)
			{
				$this->HeaderName[$i] = substr($this->HeaderName[$i],1);
				$this->Cell($this->HeaderSize[min($i,count($this->HeaderSize)-1)],3.5,str_replace(array("#",'§'),"",$this->HeaderName[$i]),0,0,(strpos($this->HeaderName[$i],"#")===false ? (strpos($this->HeaderName[$i],"§")===false ? 'L':'C'):'R'));
			}
			else
				$maxCell = max($maxCell,$this->MultiCell($this->HeaderSize[min($i,count($this->HeaderSize)-1)],3.5,str_replace(array("#",'§'),"",$this->HeaderName[$i]),0,(strpos($this->HeaderName[$i],"#")===false ? (strpos($this->HeaderName[$i],"§")===false ? 'L':'C'):'R'),0,0));
		}
		$this->Rect($xPosition, $yPosition-1, array_sum($this->HeaderSize),3.5*$maxCell+2);
		$this->SetFont($this->FontStd,'',8);
		$this->lastY = $yPosition+(3.5*$maxCell)+1+1;
	}

	function addSpacer($size=2)
	{
		$this->lastY += $size;
	}

	function printDataRow($data) {
		$maxCell= 1;
		$this->SetFont($this->FontStd,'',8);
		$this->SetXY(OrisPDF::leftMargin, $this->lastY);
		$this->Cell(0.1,3.5,'',0,0,'L');
		$this->SetXY(OrisPDF::leftMargin, $this->lastY);
		for($i=0; $i<count($data); $i++) {
			$Align='L';
			if(strstr($data[$i],"#")) {
				$Align='R';
			} elseif(strstr($data[$i],"§")) {
				$Align='C';
			}
			if(strstr($data[$i], "\n")) {
				$maxCell = max($maxCell, $this->MultiCell($this->DataSize[min($i,count($this->DataSize)-1)],3.5,str_replace(array("#","§"),"",$data[$i]),0, $Align, 0, 0));
			} else {
				$this->Cell($this->DataSize[min($i,count($this->DataSize)-1)],3.5,str_replace(array("#","§"),"",$data[$i]),0,0, $Align);
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

	function setEvent($name)
	{
		$this->Event = $name;
	}

	function setPhase($name)
	{
		$this->EvPhase = $name;
	}

	function setComment($comment)
	{
		$this->EvComment = $comment;
	}

	function samePage($rowNo, $rowHeight=3.5, $y='', $addPage=true)
	{
		return !$this->checkPageBreak($rowNo * $rowHeight, $y, $addPage);
	}

	function setOrisCode($newCode='', $newTitle='')
	{
		if($newCode != '')
			$this->Number=$newCode;
		if($newTitle != '')
			$this->Title=$newTitle;
	}
}

?>