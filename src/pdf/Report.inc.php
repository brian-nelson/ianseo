<?php
require_once('Common/pdf/ResultPDF.inc.php');

class Report extends ResultPDF
{
	var $copy2='';
	var $validationCode='';
	
	public function __construct($DocTitolo, $Portrait=true)
	{
		parent::__construct($DocTitolo, $Portrait);
		$this->AddFont('barcode');
	}
	
	public function Footer()
	{
		parent::Footer();
		$this->SetXY(IanseoPdf::sideMargin,$this->h - $this->savedBottomMargin);
		$this->MultiCell(70, 5, $this->copy2 ,0, "L", 0);

		//$this->Line(195, $this->tMargin, 195, $this->tMargin+50);
		//$this->Line(195, $this->tMargin+60, 195, $this->tMargin+110);
		$this->StartTransform();
		$this->setXY(195, $this->tMargin+50);
		$this->Rotate(90);
		$this->Cell(45,5,get_text('OrgResponsible','Tournament'),'T',0,'C');
		$this->setXY(195, $this->tMargin+100);
		$this->Rotate(90);
		$this->Cell(45,5,get_text('Judge','Tournament'),'T',0,'C');
		$this->setXY(185, $this->tMargin+175);
		$this->Rotate(90);
		$this->SetFont('barcode','', 40);
		$this->Cell(65,15, (IsBlocked(BIT_BLOCK_REPORT) ? '*' . str_replace(".","",$this->validationCode) . '*' : ''),0,0,'C');
		$this->setXY(185, $this->tMargin+235);
		$this->Rotate(90);
		$this->SetFont($this->FontFix,'B', 20);
		$this->Cell(50,15, (IsBlocked(BIT_BLOCK_REPORT) ? $this->validationCode : get_text('TourNoBlock','Tournament')),0,0,'C');
		
		$this->StopTransform();
		
		if (!IsBlocked(BIT_BLOCK_REPORT))
			$this->WaterMark(get_text('TourNoBlock','Tournament'));
	}
	
	function WaterMark($txt)
	{
    //Put watermark
	    $this->SetFont($this->FontStd,'B',75);
    	$this->SetTextColor(0xC0,0xC0,0xC0);
    	$a=$this->h;
    	$b=$this->w;
    	$c = sqrt(($a*$a) + ($b*$b));
    	
    	$this->StartTransform();
    	$this->SetXY(0,$a-20);
    	$this->Rotate(rad2deg(asin($a/$c)));
    	$this->Cell($c-50,30,$txt,1,0,'C',0,'',2);
    	$this->Rotate(0);
    	$this->StopTransform();
	}

	public function setCopy2($v)
	{
		$this->copy2 = $v;
	}
	
	public function setValidationCode($v)
	{
		$this->validationCode = $v;
	}
}
?>