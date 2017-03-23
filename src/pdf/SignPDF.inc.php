<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once("Common/pdf/ResultPDF.inc.php");

	class SignPDF extends ResultPDF {
		protected $First;
		protected $Second;
		protected $Third;

		protected $FirstSize;
		protected $SecondSize;
		protected $ThirdSize;
		protected $LnSize;

		public function init($First, $Second='', $Third='') {
			$this->First=$First;
			$this->Second=$Second;
			$this->Third=nl2br(trim($Third));

			$this->FirstSize=220;
			$this->SecondSize=95;
			$this->ThirdSize=14;
			$this->LnSize=16;

			if ($this->Second) {
				$this->FirstSize=200;
				$this->LnSize/=2;
			}

			if($this->Third) {
				$this->FirstSize=30;
				$this->SecondSize=14;
				$this->ThirdSize=12;
				$this->LnSize=16;
			}
		}

		//Page Header
		function Header()
		{
			global $CFG;
			$LeftStart = 10;
			$RightStart = 10;
			$ImgSizeReq=30;

			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg'))
			{
				$im=getimagesize($IM);
				$this->Image($IM, 10, 5, 0, $ImgSizeReq);
				$LeftStart += ($im[0] * $ImgSizeReq / $im[1]);
			}
			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg'))
			{
				$im=getimagesize($IM);
				$this->Image($IM, ($this->w-10) - ($im[0] * $ImgSizeReq / $im[1]), 5, 0, $ImgSizeReq);
				$RightStart += ($im[0] * $ImgSizeReq / $im[1]);
			}
	    	$this->SetFont($this->FontStd,'B',16);
			$this->SetXY($LeftStart,15);
			$this->Cell($this->w-$LeftStart-$RightStart, 4, ($this->Name), 0, 1, 'C', 0);

		}

		public function Make()
		{
			$this->ln($this->LnSize);

			$this->SetFont($this->FontStd,'B',$this->FirstSize);
			$this->cell(0,0,$this->First,0,1,'C');

			if ($this->Second!='')
			{
				$tmpWidth = $this->GetLineWidth();
				switch ($this->Second)
				{
					case '>':
						$this->SetLineWidth(15);
						$this->Line($this->w-40,$this->y+25+7.5*sqrt(2)/2,$this->w-75,$this->y-10+7.5*sqrt(2)/2);
						$this->Line($this->w-40,$this->y+25-7.5*sqrt(2)/2,$this->w-75,$this->y+60-7.5*sqrt(2)/2);
						$this->Line(40,$this->y+25,$this->w-47.5,$this->y+25);
						break;
					case '<':
						$this->SetLineWidth(15);
						$this->Line(40,$this->y+25+7.5*sqrt(2)/2,75,$this->y-10+7.5*sqrt(2)/2);
						$this->Line(40,$this->y+25-7.5*sqrt(2)/2,75,$this->y+60-7.5*sqrt(2)/2);
						$this->Line(47.5,$this->y+25,$this->w-40,$this->y+25);
						break;
					case '^':
						$this->SetLineWidth(15);
						$this->Line(($this->w/3),$this->y+5,($this->w/3),$this->y+60);
						$this->Line(($this->w/3)-7.5*sqrt(2)/2,$this->y,($this->w/3)+35-7.5*sqrt(2)/2,$this->y+35);
						$this->Line(($this->w/3)+7.5*sqrt(2)/2,$this->y,($this->w/3)-35+7.5*sqrt(2)/2,$this->y+35);
						$this->Line(($this->w*2/3),$this->y+5,($this->w*2/3),$this->y+60);
						$this->Line(($this->w*2/3)-7.5*sqrt(2)/2,$this->y,($this->w*2/3)+35-7.5*sqrt(2)/2,$this->y+35);
						$this->Line(($this->w*2/3)+7.5*sqrt(2)/2,$this->y,($this->w*2/3)-35+7.5*sqrt(2)/2,$this->y+35);
						break;
					default:
						$this->SetFont($this->FontStd,'B',$this->SecondSize);
						$this->cell(0,0,$this->Second,0,1,'C');
				}
				$this->SetLineWidth($tmpWidth);
			}

			$this->Output();
		}

		public function MakeDocument() {
			$this->SetFont($this->FontStd, 'B', $this->FirstSize);
			$this->cell(0,0,$this->First,0,1,'C');

			if($this->Second) {
				$this->dy(6);

				$this->SetFont($this->FontStd, 'B', $this->SecondSize);
				$this->cell(0,0,$this->Second,0,1,'L');
			}

			if($this->Third) {
				$this->dy(3);

				$this->SetFont($this->FontStd, '', $this->ThirdSize);
				$this->MultiCell(0, 0, $this->Third, 0, 'J', false, 1, '', '', true, 0, true);
			}

			$this->Output();
		}
	}
?>