<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/tcpdf/tcpdf.php');
require_once('Common/Lib/Fun_DateTime.inc.php');

define('EURO', chr(128));

error_reporting(0);
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
	var $Code, $Name, $Oc, $Where, $WhenF, $WhenT, $prnGolds, $prnXNine, $goldsChars ,$xNineChars;
	var $imgR = false, $imgL = false, $imgB = false;
	var $Judge, $Dos, $Resp, $Jury;
	var $ShowAwards=false;
	var $ShowStaff=true;
	var $StaffCategories=array();

	var $PageSize='A4', $FontStd='helvetica', $FontFix='courier', $Currency='';
	var $docUpdate;

	// patch
	var $DtFrom;
	var $DtTo;

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

	var $ToPaths=array('ToLeft' => '', 'ToRight' => '', 'ToBottom' => '');

	function __construct($DocTitolo, $Portrait=true, $Headers='', $StaffVisibility=true) {
		global $CFG;
		$this->ShowStaff = $StaffVisibility;
		if($Headers and is_file($Headers)) {
			$tmp=unserialize(file_get_contents($Headers));
			foreach($tmp as $k => $v) $this->{$k}=$v;
			if(!defined('ProgramVersion')) define('ProgramVersion', $tmp->ProgramVersion);
			if(!defined('ProgramBuild')) define('ProgramBuild', $tmp->ProgramBuild);
			if(!defined('ProgramRelease')) define('ProgramRelease', $tmp->ProgramRelease);
			if(file_exists($this->TourPath.'/topleft.png')) $this->ToPaths['ToLeft']=$this->TourPath.'/topleft.png';
			if(file_exists($this->TourPath.'/topright.png')) $this->ToPaths['ToRight']=$this->TourPath.'/topright.png';
			if(file_exists($this->TourPath.'/bottom.png')) $this->ToPaths['ToBottom']=$this->TourPath.'/bottom.png';
		}
		elseif(CheckTourSession())
		{
			$Sql = "SELECT ToCode, ToName, ToComDescr, ToWhere, ".
				"date_format(ToWhenFrom, '".get_text('DateFmtDB')."') as ToWhenFrom, date_format(ToWhenTo, '".get_text('DateFmtDB')."') as ToWhenTo," .
			// riga di patch
				"ToWhenFrom AS DtFrom,ToWhenTo AS DtTo," .
				"(ToImgL) as ImgL, (ToImgR) as ImgR, (ToImgB) as ImgB, ToGolds AS TtGolds, ToXNine AS TtXNine,ToGoldsChars,ToXNineChars, " .
				"ToPrintPaper, ToPrintChars, ToCurrency, ToPrintLang " .
				"FROM Tournament   WHERE ToId = " . StrSafe_DB($_SESSION['TourId']);
			$Rs=safe_r_sql($Sql);
			//print $Sql;exit;
			if(safe_num_rows($Rs)==1)
			{
				$r=safe_fetch($Rs);
				$this->Code		= $r->ToCode;
				$this->Name		= $r->ToName;
				$this->Oc		= $r->ToComDescr;
				$this->Where	= $r->ToWhere;
				$this->WhenF	= $r->ToWhenFrom;
				$this->WhenT	= $r->ToWhenTo;
				$this->imgL		= $r->ImgL;
				$this->imgR		= $r->ImgR;
				$this->imgB		= $r->ImgB;
				$this->prnGolds = $r->TtGolds;
				$this->prnXNine = $r->TtXNine;
				$this->goldsChars = $r->ToGoldsChars;
				$this->xNineChars = $r->ToXNineChars;
				$this->docUpdate=date('Ymd.His');

			// patch
				$this->DtFrom=$r->DtFrom;
				$this->DtTo=$r->DtTo;
				$this->TournamentDate2String = TournamentDate2String($this->WhenF, $this->WhenT);

				// texts
				$this->Continue=get_text('Continue');
				$this->LegendSO=get_text('LegendSO','Tournament');
				$this->CoinTossShort=get_text('CoinTossShort','Tournament');
				$this->CoinToss=get_text('CoinToss','Tournament');
				$this->ShotOffShort=get_text('ShotOffShort','Tournament');
				$this->ShotOff=get_text('ShotOff','Tournament');
				$this->LegendStatus=get_text('LegendStatus','Tournament');
				$this->Partecipation=get_text('Partecipation');
				$this->IndQual=get_text('IndQual', 'Tournament');
				$this->IndFin=get_text('IndFin', 'Tournament');
				$this->TeamQual=get_text('TeamQual', 'Tournament');
				$this->TeamFin=get_text('TeamFin', 'Tournament');
				$this->MixedTeamFinEvent=get_text('MixedTeamFinEvent', 'Tournament');
				$this->Yes=get_text('Yes');
				$this->No=get_text('No');
				// ---

				if($r->ToPrintPaper)
					$this->PageSize = 'LETTER';
				switch($r->ToPrintChars)
				{
					case 0:		 // helvetica & standard european fonts
						$this->FontStd='helvetica';
						break;
					case 1:
						$this->FontStd='dejavusans';
						$this->FontFix='freemono';
						break;
					case 2:
// 						This font is more chinese friendly -- by uian2000@gmail.com
						$this->FontStd='droidsansfallback';
						$this->FontFix='droidsansfallback';
						break;
				}

				if(is_null($r->ToCurrency))
					$this->Currency = '€';
				else
					$this->Currency = $r->ToCurrency;

				// defines a constant that overrides printing if not empty
				@define('PRINTLANG', $r->ToPrintLang);

				safe_free_result($Rs);

				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) $this->ToPaths['ToLeft']=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg';
				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) $this->ToPaths['ToRight']=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg';
				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) $this->ToPaths['ToBottom']=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg';
			}

			$Ses=StrSafe_DB($_SESSION['TourId']);
			$Select="
				SELECT ti.*, it.*,IF(ItJudge!=0,'CatJudge',IF(ItDoS!=0,'CatDos',IF(ItJury!=0,'CatJury','CatOC'))) AS `Category`
				FROM TournamentInvolved AS ti LEFT JOIN InvolvedType AS it ON ti.TiType=it.ItId
				WHERE ti.TiTournament={$Ses} AND it.ItId IS NOT NULL
				ORDER BY IF(ItJudge!=0,1,IF(ItDoS!=0,2,IF(ItJury!=0,3,4))) ASC, IF(ItJudge!=0,ItJudge,IF(ItDoS!=0,ItDoS,IF(ItJury!=0,ItJury,ItOC))) ASC,ti.TiName ASC
			";
			$Rs=safe_r_sql($Select);

			$CurCategory='';

			if(safe_num_rows($Rs)>0)
			{
				while($MyRow = safe_fetch($Rs))
				{
					if ($CurCategory!=$MyRow->Category)
					{
						$this->StaffCategories[get_text($MyRow->Category,'Tournament')]=array();
						$CurCategory=$MyRow->Category;
						$tmp=array();
					}

					$this->StaffCategories[get_text($MyRow->Category,'Tournament')][] = $MyRow->TiName;
				}
				foreach($this->StaffCategories as $cat => $members) $this->StaffCategories[$cat] = implode(', ', $members);
			}
		}

		parent::__construct(($Portrait ? 'P' : 'L'),'mm',$this->PageSize);
		$this->setJPEGQuality(100);
		$this->AliasNbPages();
		$this->SetSubject($DocTitolo);
		$this->Titolo=$DocTitolo;
		$this->SetDefaultColor();
		$this->SetMargins(IanseoPdf::sideMargin,IanseoPdf::topMargin + 3.0*count($this->StaffCategories) ,IanseoPdf::sideMargin);
		$this->SetAutoPageBreak(true, ($this->ToPaths['ToBottom'] ? IanseoPdf::footerImageH:0) + IanseoPdf::bottomMargin);
		$this->SetAuthor('http://www.ianseo.net');
		$this->SetCreator('Software Design by Ianseo');
		$this->SetTitle('IANSEO - Integrated Result System - Version ' . ProgramVersion . (defined('ProgramBuild') ? ' (' . ProgramBuild . ')' : '') . ' - Release '.ProgramRelease);
		$this->SetFont($this->FontStd,'',8);
		$this->SetLineWidth(0.1);
		$this->pushMargins();
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

		if($this->BarcodeHeader) {
			$RightStart += $this->BarcodeHeader + 10;
		}

    	$this->SetFont($this->FontStd,'B',13);
		$this->SetXY($LeftStart,5);
		$this->Cell($this->w-$LeftStart-$RightStart, 4, ($this->Name), 0, 1, 'L', 0);
		$this->SetXY($LeftStart,$this->GetY()+1);
    	$this->SetFont($this->FontStd,'',10);
		$this->SetX($LeftStart);
		$this->Cell($this->w-$LeftStart-$RightStart, 4, ($this->Oc . (strlen($this->Code) > 0 ? ' (' . $this->Code . ')' : '')) , 0, 1, 'L', 0);
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
			$this->MultiCell(95, 5, $this->Titolo . " - " . $this->docUpdate ,0, "R", 0);    //Page number
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
}

