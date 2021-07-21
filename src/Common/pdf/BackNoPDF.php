<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/pdf/IanseoPdf.php');

ini_set('memory_limit', '256M');

class BackNoPDF extends IanseoPdf {
	var $RowBn = NULL;
	var $angle;
	var $RotX = -1;
	var $RotY = -1;
	var $Rotation=false;
	var $TargetNoFont='';
	var $TargetNoStyle='';
	var $TargetNoColor='';
	var $TargetNoAlign='';

	var $AthleteFont='';
	var $AthleteStyle='';
	var $AthleteColor='';
	var $AthleteAlign='';
	var $FirstNameAllCaps='';

	var $CountryFont='';
	var $CountryStyle='';
	var $CountryColor='';
	var $CountryAlign='';

	var $BackGroundFile='';

	function __construct($TemplateID=0) {
		parent::__construct('BackNumber');
		$Select = "SELECT BackNumber.*, LENGTH(BnBackground) as ImgSize  "
			. "FROM BackNumber  "
			. "WHERE BnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND BnFinal in (0," . $TemplateID . ") order by BnFinal desc limit 1";
			//print $Select;exit;
		$Rs=safe_r_sql($Select);
		if (safe_num_rows($Rs)==1)
		{
			$this->RowBn=safe_fetch($Rs);
		} else {
			// fall back if no Backnumber creates from template!
			include_once('Tournament/BackNumberEmpty.php');
			$this->RowBn = emptyBackNumber();
		}

		$this->Rotation=($this->RowBn->BnOffsetX != 0 || $this->RowBn->BnOffsetY != 0);
		$this->RotX = $this->RowBn->BnWidth/2;
		$this->RotY = $this->RowBn->BnOffsetY/2;

		// TargetNo Specs
		$this->TargetNoFont = (($this->RowBn->BnTargetNo & 4) == 4 ? $this->FontFix : (($this->RowBn->BnTargetNo & 2) ==2 ? 'dejavuserif' : $this->FontStd));
		$this->TargetNoStyle= ($this->RowBn->BnTargetNo & 8 ? 'B' : '').($this->RowBn->BnTargetNo & 16 ? 'I' : '');
		$this->TargetNoColor= array(base_convert(substr($this->RowBn->BnTnoColor,0,2), 16, 10),base_convert(substr($this->RowBn->BnTnoColor,2,2), 16, 10),base_convert(substr($this->RowBn->BnTnoColor,4,2), 16, 10));
		$this->TargetNoAlign= (($this->RowBn->BnTargetNo & 96) == 64 ? 'L' : (($this->RowBn->BnTargetNo & 96) == 32 ? 'R' : 'C'));

		// Athlete Specs
		$this->AthleteFont = (($this->RowBn->BnAthlete & 4) == 4 ? $this->FontFix : (($this->RowBn->BnAthlete & 2) ==2 ? 'dejavuserif' : $this->FontStd));
		$this->AthleteStyle= ($this->RowBn->BnAthlete & 8 ? 'B' : '').($this->RowBn->BnAthlete & 16 ? 'I' : '');
		$this->AthleteColor= array(base_convert(substr($this->RowBn->BnAthColor,0,2), 16, 10),base_convert(substr($this->RowBn->BnAthColor,2,2), 16, 10),base_convert(substr($this->RowBn->BnAthColor,4,2), 16, 10));
		$this->AthleteAlign= (($this->RowBn->BnAthlete & 96) == 64 ? 'L' : (($this->RowBn->BnAthlete & 96) == 32 ? 'R' : 'C'));
		if($this->RowBn->BnCapitalFirstName) $this->FirstNameAllCaps=true;

		// Country Specs
		$this->CountryFont = (($this->RowBn->BnCountry & 4) == 4 ? $this->FontFix : (($this->RowBn->BnCountry & 2) ==2 ? 'dejavuserif' : $this->FontStd));
		$this->CountryStyle= ($this->RowBn->BnCountry & 8 ? 'B' : '').($this->RowBn->BnCountry & 16 ? 'I' : '');
		$this->CountryColor= array(base_convert(substr($this->RowBn->BnCoColor,0,2), 16, 10),base_convert(substr($this->RowBn->BnCoColor,2,2), 16, 10),base_convert(substr($this->RowBn->BnCoColor,4,2), 16, 10));
		$this->CountryAlign= (($this->RowBn->BnCountry & 96) == 64 ? 'L' : (($this->RowBn->BnCountry & 96) == 32 ? 'R' : 'C'));

		// background temp creation
		if($this->RowBn->ImgSize) {
			$this->BackGroundFile = tempnam('/tmp', 'bgf');
			$img=imagecreatefromstring($this->RowBn->BnBackground);
			if(!imagepng($img, $this->BackGroundFile)) die('could not create image');
		}
		$Orientation=($this->RowBn->BnWidth>$this->RowBn->BnHeight ? 'L' : 'P');
		$this->setPageOrientation($Orientation);
		$this->setPageFormat(array($this->RowBn->BnWidth, $this->RowBn->BnHeight), $Orientation);
		$this->SetFont($this->FontStd,'',10);
		$this->setPrintHeader(false);
		$this->setPrintFooter(false);
		$this->SetMargins(10,10,10);
		$this->SetAutoPageBreak(false, 10);
		$this->SetAuthor('https://www.ianseo.net');
		$this->SetCreator('Software Design by Ianseo');
		$this->SetTitle('IANSEO - Integrated Result System (release ' . ProgramVersion . ')');
		$this->SetSubject('BackNo');
	}

	function DrawElements($TargetNo = '', $MyRow = '', $Offset = 0, $BisValue='')
	{
		global $CFG;
		$OffsetX=0;
		$OffsetY=0;
		if(empty($MyRow)) {
			$MyRow = StdClass();
			$MyRow->EnFirstName='';
			$MyRow->EnFirstNameUpper='';
			$MyRow->EnName='';
			$MyRow->CoName='';
			$MyRow->CoCode='';
		}
		if(!$Offset or !$this->Rotation) {
			$this->AddPage();
		} else {
			$OffsetX=$this->RowBn->BnOffsetX*$Offset;
			$OffsetY=$this->RowBn->BnOffsetY*$Offset;
		}

//Immagine di Sfondo
		if($this->RowBn->ImgSize > 0) {
			$this->Image($this->BackGroundFile, $this->RowBn->BnBgX+$OffsetX, $this->RowBn->BnBgY+$OffsetY, $this->RowBn->BnBgW, $this->RowBn->BnBgH, 'png');
		}
//BackNumber
		if($this->RowBn->BnTargetNo & 1)
		{
			$this->SetFont($this->TargetNoFont, $this->TargetNoStyle, $this->RowBn->BnTnoSize);
			$this->SetTextColor($this->TargetNoColor[0], $this->TargetNoColor[1], $this->TargetNoColor[2]);
			$this->SetXY($this->RowBn->BnTnoX+$OffsetX, $this->RowBn->BnTnoY+$OffsetY);
			$this->Cell($this->RowBn->BnTnoW, $this->RowBn->BnTnoH, ($this->RowBn->BnIncludeSession==1 ? $MyRow->Session . " - " : ($this->RowBn->BnIncludeSession==2 ? $MyRow->SesName . " - " : '')) .  ltrim($TargetNo,'0'), 0, 0, $this->TargetNoAlign);

			// aggiunta eventuale del bis
			$this->SetXY($this->RowBn->BnTnoX+$OffsetX+($this->RowBn->BnTnoW/4), $this->RowBn->BnTnoY+$OffsetY+$this->RowBn->BnTnoH-10);
			$this->SetFont($this->TargetNoFont, $this->TargetNoStyle, $this->RowBn->BnTnoSize/3);
			$this->Cell($this->RowBn->BnTnoW/2, $this->RowBn->BnTnoH/5, $BisValue, 0, 0, $this->TargetNoAlign, null, null, 2);
		}
//Atleta
		if($this->RowBn->BnAthlete & 1)
		{
			$FinalName='';
			if($MyRow->EnFirstName) {
				$FinalName = $this->RowBn->BnCapitalFirstName ? $MyRow->EnFirstNameUpper : $MyRow->EnFirstName;
				$FinalName.= ' ' . ($this->RowBn->BnGivenNameInitial ? $this->FirstLetters($MyRow->EnName)."." : $MyRow->EnName);
			}

			$this->SetFont($this->AthleteFont, $this->AthleteStyle, $this->RowBn->BnAthSize);
			$this->SetTextColor($this->AthleteColor[0], $this->AthleteColor[1], $this->AthleteColor[2]);
			$this->SetXY($this->RowBn->BnAthX+$OffsetX, $this->RowBn->BnAthY+$OffsetY);
			$this->Cell($this->RowBn->BnAthW, $this->RowBn->BnAthH, $FinalName, 0, 0, $this->AthleteAlign);
		}

//Societa
		if($this->RowBn->BnCountry & 1)
		{
			$FinCountry='';
			$ImgOffset=0;
			$file='';
			$type='';
			if($MyRow->CoCode) {
				$FinCountry=$MyRow->CoCode;
				switch($this->RowBn->BnCountryCodeOnly) {
					case '3':
						if(is_file($file=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-FlSvg-'.$MyRow->CoCode.'.svg')) {
							$type='SVG';
							$ImgOffset=$this->RowBn->BnCoH*3/2;
						} elseif(is_file($file=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$MyRow->CoCode.'.jpg')) {
							$type='JPG';
							$ImgOffset=$this->RowBn->BnCoH*3/2;
						}
					case '1':
						$FinCountry=$MyRow->CoName;
						break;
					case '2':
						$FinCountry.= ' - ' . $MyRow->CoName;
						break;
					case '4':
						if(is_file($file=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-FlSvg-'.$MyRow->CoCode.'.svg')) {
							$type='SVG';
							$ImgOffset=$this->RowBn->BnCoH*3/2;
						} elseif(is_file($file=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$MyRow->CoCode.'.jpg')) {
							$type='JPG';
							$ImgOffset=$this->RowBn->BnCoH*3/2;
						}
						break;
				}
			}

			$this->SetFont($this->CountryFont, $this->CountryStyle, $this->RowBn->BnCoSize);
			$this->SetTextColor($this->CountryColor[0], $this->CountryColor[1], $this->CountryColor[2]);
			$this->SetXY($this->RowBn->BnCoX+$OffsetX+$ImgOffset, $this->RowBn->BnCoY+$OffsetY);
			if($type=='SVG')
				$this->ImageSVG($file, $this->RowBn->BnCoX+$OffsetX, $this->RowBn->BnCoY+$OffsetY, $ImgOffset, $this->RowBn->BnCoH, '', '', '', 1);
			elseif($type)
				$this->Image($file, $this->RowBn->BnCoX+$OffsetX, $this->RowBn->BnCoY+$OffsetY, $ImgOffset, $this->RowBn->BnCoH, $type, '', '', true, 300, '', false, false, 1, true);
			if($this->RowBn->BnCountryCodeOnly!=4) $this->Cell($this->RowBn->BnCoW-$ImgOffset, $this->RowBn->BnCoH, $FinCountry, 0, 0, $type ? 'L' : $this->CountryAlign);
		}
	}

	function _endpage()
	{
		if($this->angle!=0)
		{
			$this->angle=0;
			$this->_out('Q');
		}
		parent::_endpage();
	}

	function AthletesPerPage() {
		$pageH = 1;
		$pageW = 1;
		if($this->RowBn->BnOffsetY)
			$pageH = floor($this->RowBn->BnHeight/$this->RowBn->BnOffsetY);
		if($this->RowBn->BnOffsetX)
			$pageW = floor($this->RowBn->BnWidth/$this->RowBn->BnOffsetX);
		return max($pageH,$pageW);
	}

}

?>