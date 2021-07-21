<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/tcpdf/tcpdf.php');
require_once('Common/Lib/Fun_DateTime.inc.php');

define('EURO', chr(128));

//error_reporting(0);
//error_reporting(E_ALL);

class IanseoPdf extends TCPDF {
	const sideMargin=10;
	const topMargin=22;
	const bottomMargin=10;
	const footerImageH=10;

	var $savedTopMargin, $savedSideMargin, $savedBottomMargin;
	var $TextOutline = 0;	// per scrivere solo il contorno del font
							// 0= fill (normal)
							// 1= outline
							// 2= fill+outline
	var $TextOutlineWidth = 2; // spessore della riga di contorno

	var $Titolo;
	var $ColorDocument;
	var $Code, $Name, $Oc, $Where, $WhenF, $WhenT, $prnGolds, $prnXNine, $goldsChars ,$xNineChars, $IsOris;
	var $imgR = false, $imgL = false, $imgB = false;
	var $Judge, $Dos, $Resp, $Jury;
	var $ShowAwards=false;
	var $ShowTens=true;
	var $ShowCTSO=true;
	var $ShowStaff=true;
	var $StaffCategories=array();

	var $PageSize='A4', $FontStd='helvetica', $FontFix='courier', $Currency='';
	var $docUpdate;

	// patch
	var $DtFrom;
	var $DtTo;
	var $TzOffset='';

	// Texts
	var $TournamentDate2String='';
	var $Continue='';
	var $LegendSO='';
	var $CoinTossShort='';
	var $CoinToss='';
	var $ShotOffShort='';
	var $ShotOff='';
	var $LegendStatus='';
	var $Partecipation='';
	var $IndQual='';
	var $IndFin='';
	var $TeamQual='';
	var $TeamFin='';
	var $MixedTeamFinEvent='';
	var $Yes='';
	var $No='';
	var $PrintFooterSerialNumber=true;
	var $angle=0;
	var $BarcodeHeader=0;
	var $BarcodeHeaderX=0;
	var $IsField3D=false;

	var $Version='';

	var $ToPaths=array('ToLeft' => '', 'ToRight' => '', 'ToBottom' => '');

	function __construct($DocTitolo, $Portrait=true, $Headers='', $StaffVisibility=true) {
		global $CFG;
		$this->ShowStaff = $StaffVisibility;
		$isOnline=false;
		if($Headers and is_file($Headers)) {
			$isOnline=true;
			$tmp=unserialize(file_get_contents($Headers));
		} elseif(CheckTourSession()) {
			require_once('Common/OrisFunctions.php');
			$tmp=getPdfHeader(false);
		} else {
			CD_redirect($CFG->ROOT_DIR);
		}

		foreach($tmp as $k => $v) $this->{$k}=$v;
		if(!defined('ProgramVersion')) define('ProgramVersion', $tmp->ProgramVersion);
		if(!defined('ProgramBuild')) define('ProgramBuild', $tmp->ProgramBuild);
		if(!defined('ProgramRelease')) define('ProgramRelease', $tmp->ProgramRelease);
		if($isOnline) {
			if(file_exists($this->TourPath.'/topleft.png')) $this->ToPaths['ToLeft']=$this->TourPath.'/topleft.png';
			if(file_exists($this->TourPath.'/topright.png')) $this->ToPaths['ToRight']=$this->TourPath.'/topright.png';
			if(file_exists($this->TourPath.'/bottom.png')) $this->ToPaths['ToBottom']=$this->TourPath.'/bottom.png';
		} else {
			if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) $this->ToPaths['ToLeft']=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg';
			if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) $this->ToPaths['ToRight']=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg';
			if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) $this->ToPaths['ToBottom']=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg';
		}

		parent::__construct(($Portrait ? 'P' : 'L'),'mm',$this->PageSize);

		$this->setJPEGQuality(100);
		$this->SetSubject($DocTitolo);
		$this->Title=$DocTitolo;
		$this->Titolo=$DocTitolo;
		$this->SetDefaultColor();
		$this->SetMargins(IanseoPdf::sideMargin,IanseoPdf::topMargin + 3.0*count($this->StaffCategories) ,IanseoPdf::sideMargin);
		$this->SetAutoPageBreak(true, ($this->ToPaths['ToBottom'] ? IanseoPdf::footerImageH:0) + IanseoPdf::bottomMargin);
		$this->SetAuthor('https://www.ianseo.net');
		$this->SetCreator('Software Design by Ianseo');
		$this->SetTitle('IANSEO - Integrated Result System - Version ' . ProgramVersion . (defined('ProgramBuild') ? ' (' . ProgramBuild . ')' : '') . ' - Release '.ProgramRelease);
		$this->SetFont($this->FontStd,'',8);
		$this->SetLineWidth(0.1);
		$this->pushMargins();
		$this->setFontSubsetting(empty($r) ? false : ($r->ToPrintChars >= 2));
		$this->setViewerPreferences(array('PrintScaling' => 'none'));
	}

	public function getDrawColor() {
		return $this->DrawColor;
	}

	public function setDocUpdate($newDate)
	{
		$this->docUpdate=$newDate;
		$this->docUpdate=str_replace("-","",$this->docUpdate);
		$this->docUpdate=str_replace(":","",$this->docUpdate);
		$this->docUpdate=str_replace(" ",".",$this->docUpdate);
	}

	public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=1, $ignore_min_height=false, $calign='T', $valign='M') {
		parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link, $stretch, $ignore_min_height, $calign, $valign);
	}

	function SetDefaultColor()
	{
		$this->SetDrawColor(0x33, 0x33, 0x33);
		$this->SetFillColor(0xE0,0xE0,0xE0);
		$this->SetTextColor(0x00, 0x00, 0x00);
	}

	function getSideMargin()
	{
		return IanseoPdf::sideMargin;
	}

	function getTopMargin()
	{
		return IanseoPdf::topMargin;
	}

	function dY($y=0, $resetX=true) {
		$this->setY($this->getY()+$y, $resetX);
	}

	function writeCurrency()
	{
		return $this->Currency;
	}

	function Header()
	{
		global $CFG;
		$this->SetDefaultColor();
		$LeftStart = IanseoPdf::sideMargin;
		$RightStart = IanseoPdf::sideMargin;
		$ImgSizeReq=15;
		//if(strlen($this->Judge . $this->Resp . $this->Dos . $this->Jury))
		if (count($this->StaffCategories)>0) $ImgSizeReq+=5;
		if($this->ToPaths['ToLeft']) {
			$this->Image($this->ToPaths['ToLeft'], IanseoPdf::sideMargin, 5, 0, $ImgSizeReq);
			$LeftStart = $this->getImageRBX()+2;
		}
		if($this->ToPaths['ToRight']) {
			$im=getimagesize($this->ToPaths['ToRight']);
			$this->Image($this->ToPaths['ToRight'], (($this->w-IanseoPdf::sideMargin) - ($im[0] * $ImgSizeReq / $im[1])), 5, 0, $ImgSizeReq);
			$RightStart += ($im[0] * $ImgSizeReq / $im[1]);
		}

		//if($this->BarcodeHeader) {
			//$RightStart += $this->BarcodeHeader + 10;
		//}

    	$this->SetFont($this->FontStd,'B',13);
		$this->SetXY($LeftStart,5);
		$this->Cell($this->w-$LeftStart-$RightStart, 4, preg_replace("/[\r\n]+/sim", ' ', $this->Name), 0, 1, 'L', 0);
		$this->SetXY($LeftStart,$this->GetY()+1);
    	$this->SetFont($this->FontStd,'',10);
		$this->SetX($LeftStart);
		$this->Cell($this->w-$LeftStart-$RightStart, 4, (preg_replace("/[\r\n]+/sim", ' ', $this->Oc) . (strlen($this->Code) > 0 ? ' (' . $this->Code . ')' : '')) , 0, 1, 'L', 0);
    	$this->SetFont($this->FontStd,'',10);
		$this->SetX($LeftStart);
		$this->Cell($this->w-$LeftStart-$RightStart, 4,  ($this->Where . ", " . $this->TournamentDate2String ), 0, 1, 'L', 0);
    	$this->SetFont($this->FontStd,'',6);

    	if ($this->ShowStaff && count($this->StaffCategories)>0)
    	{
    		foreach ($this->StaffCategories as $c=>$v)
    		{
    			$this->SetX($LeftStart);
    			$this->Cell($this->w-$LeftStart-$RightStart, 3,  $c .': ' . $v, 0, 1, 'L', 0);
    		}
    	}
	}

	//Page footer
	function Footer() {
		global $CFG;
		$this->SetDefaultColor();
		$this->Line(IanseoPdf::sideMargin, $this->h - $this->savedBottomMargin, ($this->w-IanseoPdf::sideMargin), $this->h - $this->savedBottomMargin);

		if($this->ToPaths['ToBottom']) {
			$im=getimagesize($this->ToPaths['ToBottom']);
			$imgwidth = $im[0] * (IanseoPdf::footerImageH) / $im[1];
			$imgheight = IanseoPdf::footerImageH;
			if($imgwidth>($this->w - $this->rMargin - $this->lMargin)) {
				$imgwidth=$this->w - $this->rMargin - $this->lMargin;
				$imgheight=$im[1]*$imgwidth/$im[0];
			}
			$this->Image($this->ToPaths['ToBottom'], ($this->w-$imgwidth)/2, $this->h - $this->savedBottomMargin + 5, $imgwidth, $imgheight);
		}

		if($this->PrintFooterSerialNumber) {
			$this->SetFont($this->FontStd,'',8);
	    	$this->SetXY(IanseoPdf::sideMargin,$this->h - $this->savedBottomMargin);
		    $this->MultiCell(($this->w-20), 5, $this->getGroupPageNo() . "/" . $this->getPageGroupAlias() ,0, "C", 0);    //Page number
		    $this->SetXY(($this->w-105),$this->h - $this->savedBottomMargin + 1);    //Position at 1.5 cm from bottom
			$this->MultiCell(95, 5, $this->Titolo . " - " . $this->docUpdate . ($this->Version ? " (v. $this->Version)" : ''),0, "R", 0);    //Page number
		}
	}

	function SamePage($HowLong) {
		return !($this->checkPageBreak($HowLong,'',false));
	}

	function pushMargins()
	{
		$this->savedTopMargin = $this->tMargin;
		$this->savedSideMargin = $this->lMargin;
		$this->savedBottomMargin = $this->bMargin;
	}

	function popMargins()
	{
		$this->tMargin = $this->savedTopMargin;
		$this->lMargin = $this->savedSideMargin;
		$this->bMargin = $this->savedBottomMargin;
	}

	function Rotate($angle,$x=-1,$y=-1)
	{
	    if($x==-1)
    	    $x=$this->x;
	    if($y==-1)
    	    $y=$this->y;
	    if($this->angle!=0)
        	$this->_out('Q');
    	$this->angle=$angle;
	    if($angle!=0)
    	{
	        $angle*=M_PI/180;
    	    $c=cos($angle);
	        $s=sin($angle);
        	$cx=$x*$this->k;
    	    $cy=($this->h-$y)*$this->k;
	        $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
    	}
	}

	function FirstLetters($string) {
		$n=preg_split('/[ -]/', $string);
		foreach($n as &$v) $v= mb_eregi_replace('^(.).+', '\\1', $v);
		return implode('',$n);
	}

	public function GetLineStyle() {
		static $ca = array('butt', 'round', 'square');
		static $ja = array('miter', 'round', 'bevel');
		$ret=array();

		// width, cap, join
		$ret['width']=floatval($this->linestyleWidth)/$this->k;
		$ret['cap']=intval($this->linestyleCap);
		$ret['join']=intval($this->linestyleJoin);

		// dash
		$dash=explode(' ',$this->linestyleDash);
		array_pop($dash);
		$ret['phase']=array_pop($dash);
		$ret['dash']=implode(',', $dash);

		// no color get at the moment
//		if (isset($color)) {
//			$this->SetDrawColorArray($color);
//		}
		return $ret;
	}

	public function setBarcodeHeader($num=0) {
		$this->BarcodeHeader=$num;
		$RightStart = IanseoPdf::sideMargin;
		$ImgSizeReq=15;
		if (count($this->StaffCategories)>0) $ImgSizeReq+=5;
		if($this->ToPaths['ToRight']) {
			$im=getimagesize($this->ToPaths['ToRight']);
			$RightStart += ($im[0] * $ImgSizeReq / $im[1]);
		}
		$this->BarcodeHeaderX = $this->w - 5 - $RightStart - $num;
	}

	public function setStaffVisibility($visible=true) {
		$this->ShowStaff = false;
	}

	public function Error($msg) {
		// unset all class variables
		$this->_destroy(true);
		// exit program and print error
		debug_svela('<strong>TCPDF ERROR: </strong>' . $msg, 'TCPDF');
	}
}

