<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once("Common/pdf/ResultPDF.inc.php");

	class BigNamesPDF extends IanseoPdf
	{
		protected $ResultStream;
		public $TargetAssignment=true;
		public $IncludeLogo=true;
		public $TeamLeaf=false;
		public $leftMargin = 10;
		public $rightMargin= 10;
		public $topMargin  = 10;
		public $Local = false;
		public $GoldBox='Gold';
		public $BronzeBox='Bronze';
		public $SemiBox='1/2';
		public $BoxWidth=15;
		public $BigNameLineWidth=1;
		public $BigNameColors=array(
			0 => array(128),
			1 => array(128),
			2 => array(251, 191, 21),
			4 => array(239, 46, 49),
			8 => array(64, 193, 230),
			16 => array(33, 81, 168),
			32 => array(79, 190, 55),
			64 => array(237, 43, 159),
			);


		public function init($Rs){
			$this->ResultStream=$Rs;
		}

		//Page Header
		function Header(){
			global $CFG;
			if(true or $this->print_header) {

				$LeftStart = $this->leftMargin;
				$RightStart = $this->rightMargin;

				$ImgSizeReq=40;

				if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg') and empty($_REQUEST['TestCountries']))
				{
					$im=getimagesize($IM);
					$this->Image($IM, $this->leftMargin, $this->topMargin-5, 0, $ImgSizeReq);
					$LeftStart += ($im[0] * $ImgSizeReq / $im[1]);
				}
				if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg') and empty($_REQUEST['TestCountries']))
				{
					$im=getimagesize($IM);
					$this->Image($IM, (($this->w-$this->rightMargin) - ($im[0] * $ImgSizeReq / $im[1])), $this->topMargin-5, 0, $ImgSizeReq);
					$RightStart += ($im[0] * $ImgSizeReq / $im[1]);
				}

		    	$this->SetFont($this->FontStd,'B',30);
				$this->SetXY($LeftStart,$this->topMargin+5);
				$this->Cell($this->w-$LeftStart-$RightStart, 4, ($this->Name), 0, 1, 'C', 0);
			}

		}

		public function footer() {
			global $CFG;
			$this->SetDefaultColor();

			$this->Line(IanseoPdf::sideMargin, $this->h - $this->bMargin, ($this->w-IanseoPdf::sideMargin), $this->h - $this->bMargin);

			//IMMAGINE DEGLI SPONSOR
			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg') and empty($_REQUEST['TestCountries']))
			{
				$im=getimagesize($IM);
				$imgwidth = $im[0] * (IanseoPdf::footerImageH) / $im[1];
				$this->Image($IM, ($this->w-$imgwidth)/2, $this->h - $this->bMargin + 5, 0, IanseoPdf::footerImageH);
			}
			//			$this->SetFont($this->FontStd,'',8);
//	    	$this->SetXY(IanseoPdf::sideMargin,$this->h - $this->bMargin);
//		    $this->MultiCell(($this->w-20), 5, $this->getGroupPageNo() . "/" . $this->getPageGroupAlias() ,0, "C", 0);    //Page number
//		    $this->SetXY(($this->w-80),$this->h - $this->bMargin + 1);    //Position at 1.5 cm from bottom
//			$this->MultiCell(70, 5, $this->Titolo . " - " . date('Ymd.Hi') ,0, "R", 0);    //Page number
		}
		public function Make() {
			if(empty($_REQUEST['ColouredPhases'])) {
				$this->BigNameColors=array(
					0 => array(0),
					1 => array(0),
					2 => array(0),
					4 => array(0),
					8 => array(0),
					16 => array(0),
					32 => array(0),
					64 => array(0),
					);
				$this->BigNameLineWidth=$this->GetLineWidth();
			}
			if($this->TeamLeaf) {
				$this->MakeTeamLeaf();
			} else {
				$this->MakeNormal();
			}
		}

		public function MakeNormal() {
			global $CFG;
			$TourCode=(empty($_REQUEST['TestCountries'])?$_SESSION['TourCodeSafe']:'All');
			$EnIds=array();
			while($MyRow=safe_fetch($this->ResultStream)) {
				if(isset($MyRow->EnId)) {
					if(in_array($MyRow->EnId, $EnIds)) {
						continue;
					}
					$EnIds[]=$MyRow->EnId;
				}
				if(!trim($MyRow->Athlete)) continue;

				$this->AddPage();
//				$this->Line(5,($this->getPageHeight()/2),15,($this->getPageHeight()/2));
//				$this->Line($this->getPageWidth()-15,($this->getPageHeight()/2),$this->getPageWidth()-5,($this->getPageHeight()/2));


				// Box del Rank
				$this->SetXY(10,55);
				$this->SetFontSize(180);
				$H=$this->getPageHeight()-80;
				$tmpPad=$this->getCellPaddings();
				$this->SetCellPadding(0);
				if($this->IncludeLogo) {
					if(file_exists($svg=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-FlSvg-'.$MyRow->CoCode.'.svg')) {
						$this->ImageSVG($svg, 10, 55, 60, 40);
					} elseif(file_exists($svg=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-Fl-'.$MyRow->CoCode.'.jpg')) {
						list($w, $h)=getimagesize($svg);
						$ratio=$w/$h;
						$h=38;
						$w=$h*$ratio;
						if($w>58) {
							$w=58;
							$h=$w/$ratio;
						}
						$this->Image($svg, 10+(60-$w)/2, 55+(40-$h)/2, $w, $h, '', '', '', 2);
					}
					$this->rect(10, 55, 60, 40);

					$this->y+=45;
					$H -= 45;
				}
				if($MyRow->Rank) {
					$this->Cell(60, $H, $MyRow->Rank, 1, 1, 'C', 0, null, 1);
				}
				$this->SetCellPaddings($tmpPad['L'], $tmpPad['T'], $tmpPad['R'], $tmpPad['B']);

				// box Atleta/Country
				$this->SetXY(70, 41.3);
				$this->SetFontSize(159);
				$this->Cell(($this->getPageWidth()-80), 60, $MyRow->Athlete, 0, 1, 'L', 0, null, 1);

				// box Country/Athletes
				$Names=explode('|', $MyRow->CoName);
				if(count($Names)>4) {
					$this->SetFontSize(30);
				} else if(count($Names)>1) {
					$this->SetFontSize(50);
				} else {
					$this->SetFontSize(100);
				}
				$StartY=110 + 8*(count($Names)==1);
				foreach($Names as $name) {
					$this->SetXY(70, $StartY);
					$this->Cell(($this->getPageWidth()-80), 10, $name, 0, 0, 'L', 0, 0, 1);
					$StartY+=(count($Names)>4 ? 12 : 18);
				}

				if($this->TargetAssignment) {

					// PArte di riconoscimento EVENTO e Paglione
					$OldLineWidth=$this->GetLineWidth();
					$OldColor=$this->DrawColor;
					$this->SetFont('','',20);
					$this->SetLineWidth($this->BigNameLineWidth);


					$this->SetXY($this->getPageWidth()-$this->BoxWidth-10, $this->getPageHeight()-35);
                    if(!empty($MyRow->sGo)) {
						$this->setColorArray('draw', $this->BigNameColors[0]);
                        $this->Cell($this->BoxWidth, 10, $this->GoldBox . ": " . ltrim($MyRow->sGo, '0'), 1, 0, 'C', 0);
                        $this->SetX($this->getX() - $this->BigNameLineWidth - $this->BoxWidth * 2);
                    }
                    if(!empty($MyRow->sBr)) {
						$this->setColorArray('draw', $this->BigNameColors[1]);
                        $this->Cell($this->BoxWidth, 10, $this->BronzeBox . ": " . ltrim($MyRow->sBr, '0'), 1, 0, 'C', 0);
                        $this->SetX($this->getX() - $this->BigNameLineWidth - $this->BoxWidth*2);
                    }
					for($i=2; $i<=valueFirstPhase($MyRow->GrPhase);$i=$i*2) {
						$this->setColorArray('draw', $this->BigNameColors[$i]);
						if(isset($MyRow->{'s' . $i})) {
							$this->Cell($this->BoxWidth, 10, '1/' . namePhase($MyRow->EvFinalFirstPhase,$i) . ': ' . ltrim($MyRow->{'s' . $i},'0'),1,0,'C',0);
						} else {
							$this->Cell($this->BoxWidth, 10, '',1,0,'C',0);
						}
                        if(empty($MyRow->{'s' . $i})) { //} OR is_null($MyRow->{'s' . $i})) {
						    $this->line($this->getX(), $this->getY(),$this->getX()-$this->BoxWidth, $this->GetY()+10);
                        }
                        $this->SetX($this->getX() - $this->BigNameLineWidth - $this->BoxWidth*2);
					}

					$this->DrawColor=$OldColor;

                    //$this->SetX($this->getX()-$this->BoxWidth-15);
					$this->SetFont('','B',20);
					$this->Cell(15,10, $MyRow->EvCode,1,0,'C',0);
					$this->SetFont('','',20);

					$this->SetLineWidth($OldLineWidth);
				}

			}
			$this->endPage();
			$this->Output();
		}

		public function MakeTeamLeaf()
		{
			global $CFG;
			$first=true;
			$this->print_header=true;
			$TourCode=(empty($_REQUEST['TestCountries'])?$_SESSION['TourCodeSafe']:'All');
			$this->topMargin=$this->h/2+10;
			while($MyRow=safe_fetch($this->ResultStream))
			{

				if(!trim($MyRow->Athlete)) continue;

				$this->AddPage();
//				$this->Line(5,($this->getPageHeight()/2),15,($this->getPageHeight()/2));
//				$this->Line($this->getPageWidth()-15,($this->getPageHeight()/2),$this->getPageWidth()-5,($this->getPageHeight()/2));


				// Bandiera
				$this->PrintFlag($MyRow, $TourCode);
				$this->Rotate(180, $this->w/2, $this->h/2);
				$this->Header();
				$this->PrintFlag($MyRow, $TourCode, true);
				$this->Footer();
				$this->Rotate(0);
			}
			$this->Output();
		}

		function PrintFlag($MyRow, $TourCode, $lang=false) {
			global $CFG;
			if($this->IncludeLogo) {
				if(file_exists($svg=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-FlSvg-'.$MyRow->CoCode.'.svg')) {
					$this->ImageSVG($svg, 5, $this->topMargin+40, 45, 30);
				} elseif(file_exists($svg=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-Fl-'.$MyRow->CoCode.'.jpg')) {
					list($w, $h)=getimagesize($svg);
					$ratio=$w/$h;
					$h=28;
					$w=$h*$ratio;
					if($w>43) {
						$w=43;
						$h=$w/$ratio;
					}
					$this->Image($svg, 5+(45-$w)/2, $this->topMargin+40+(30-$h)/2, $w, $h, '', '', '', 2);
				}
				$this->rect(5, $this->topMargin+40, 45, 30);
			}

			// box Atleta/Country
			$this->InFooter=true;
			$this->SetXY(55, $this->topMargin+31);
			$this->SetFont('','b',110);
			if(!$this->Local or !$lang or strstr($Athlete=get_text($MyRow->CoCode, 'IOC_Codes', '', false, true), '[')) {
				$Athlete=$MyRow->Athlete;
			}
			$this->Cell(($this->getPageWidth()-65), 30, $Athlete, 0, 1, 'L', 0, null, 1);
		}
	}
?>
