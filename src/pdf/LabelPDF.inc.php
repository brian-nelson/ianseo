<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once("Common/pdf/IanseoPdf.php");

class LabelPDF extends IanseoPdf {

	//Constructor
	function __construct($Width=0, $Height=0) {
		parent::__construct('');
		if($Width and $Height) {
			$this->setPageFormat(array($Width, $Height), $Width>$Height ? 'L' : 'P');
		}
		$this->setPrintHeader(false);
		$this->setPrintFooter(false);
		$this->SetMargins(0,0,0);
		$this->SetAutoPageBreak(false, 0);
		$this->SetColors();
		$this->AddFont('barcode');
	}

	function SetColors()
	{
		$this->SetTextColor(0x00, 0x00, 0x00);
		$this->SetDrawColor(0x33, 0x33, 0x33);
		$this->SetFillColor(0xE8,0xE8,0xE8);
	}
}
?>