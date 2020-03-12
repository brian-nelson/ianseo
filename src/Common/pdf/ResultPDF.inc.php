<?php
require_once('Common/pdf/IanseoPdf.php');
require_once('Common/Lib/Fun_DateTime.inc.php');
require_once('Common/Lib/CommonLib.php');

class ResultPDF extends IanseoPdf {

	var $angle=0;
	var $HideCols=false;
	var $FontSizeTitle=10;
	var $FontSizeHead=7;
	var $FontSizeHeadSmall=6;
	var $FontSizeLines=8;
	var $RealCellHeight=4;
	var $PoolMatches=array();
	var $PoolMatchesWA=array();
	var $PoolWinners=array();

	//Constructor
	function __construct($DocTitolo, $Portrait=true, $Headers='', $StaffVisibility=true) {
		parent::__construct($DocTitolo, $Portrait, $Headers, $StaffVisibility);

		$this->PoolMatches=getPoolMatchesShort();
		$this->PoolMatchesWA=getPoolMatchesShortWA();
		$this->PoolWinners=getPoolMatchesWinners();
		$this->PoolWinnersWA=getPoolMatchesWinnersWA();

		$this->startPageGroup();
		$this->AddPage();
		$this->setlinewidth(0.1);
	}



	//Page Header

	function SetAccreditedColor()
	{
		$this->SetFillColor(0xF4,0xF4,0xF4);
		$this->SetTextColor(0x90, 0x90, 0x90);
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


	function Print_Group_Header($Rows, $Groups, $HeadersAndFirst, $CellDouble, $first, $DistSize, $AddSize, $CellWidth, $Segue=false) {
		$add_space=(!$first);

		// headers won't fit in the rest of the page
		if(!$Segue and !$this->SamePage($HeadersAndFirst)) {
			$this->AddPage();
			$add_space=false;
		}

		// extra space is called only between groups if not a new page
		if($add_space) $this->SetY($this->GetY()+5);

		$this->SetFont($this->FontStd,'B',$this->FontSizeTitle);
		$this->Cell($Rows->PageWidth, 6,  $Groups->Description, 1, 1, 'C', 1);

		if($Segue) {
			$this->SetXY(170, $this->GetY()-6);
		   	$this->SetFont($this->FontStd,'',6);
			$this->Cell(30, 6,  $this->Continue, 0, 1, 'R', 0);
		}

		$this->SetFont($this->FontStd,'B',$this->FontSizeHead);

		foreach($Rows->HeaderWidth as $Column => $Data) {
			if(is_array($Column)) {
				$TmpX=$this->GetX();
				$TmpY=$this->GetY();

				$offset=0;
				foreach($Column as $SubRow) {
					$this->setXY($TmpX,$TmpY+$offset);
					foreach($SubRow as $SubColumn=>$SubData) {
						$this->Cell($SubData->Width, $SubData->Height,  $Rows->Header->$SubColumn, $SubData->Border, $SubData->NewLine, $SubData->Align, 1);
						$offset = $SubData->Height;
					}
				}
				$this->setY($TmpY, false); // leaves X where it was, only moves the Y
			} else {
				$this->Cell($Data->Width, $Data->Height,  $Rows->Header->$Column, $Data->Border, $Data->NewLine, $Data->Align, 1);
			}
		}

		$this->SetFont($this->FontStd,'',1);
		$this->Cell($this->getPageWidth()-array_sum($this->getOriginalMargins()), 0.5,  '', 1, 1, 'C', 0);
	}

	function Print_Cell($MyRow, $Column, $Data ) {
		switch($Column) {
			case 'TotaleScoreLeft':
			   	if($MyRow->TotaleScore==$MyRow->TotaleSnapScore || empty($MyRow->EqDistance)) {
					$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
					$this->Cell($Data->Width, $Data->Height,  $MyRow->TotaleScore, $Data->Border, $Data->NewLine, $Data->Align, 0);
			   	} else {
					$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
					$this->Cell($Data->Width, $Data->Height,  $MyRow->TotaleSnapScore, $Data->Border, $Data->NewLine, $Data->Align, 0);
			   	}
				break;
			case 'TotaleScoreRight':
			   	if($MyRow->TotaleScore==$MyRow->TotaleSnapScore || empty($MyRow->EqDistance)) {
					$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
					$this->Cell($Data->Width, $Data->Height,  '', $Data->Border, $Data->NewLine, $Data->Align, 0);
			   	} else {
					$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
					$this->Cell($Data->Width, $Data->Height,  $MyRow->TotaleScore, $Data->Border, $Data->NewLine, $Data->Align, 0);
			   	}
				break;
			case 'GoldsLeft':
			   	if($MyRow->Golds==$MyRow->SnapGolds || empty($MyRow->EqDistance)) {
					$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
					$this->Cell($Data->Width, $Data->Height,  $MyRow->Golds, $Data->Border, $Data->NewLine, $Data->Align, 0);
			   	} else {
					$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
					$this->Cell($Data->Width, $Data->Height,  $MyRow->SnapGolds, $Data->Border, $Data->NewLine, $Data->Align, 0);
			   	}
				break;
			case 'GoldsRight':
			   	if($MyRow->Golds==$MyRow->SnapGolds || empty($MyRow->EqDistance)) {
					$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
					$this->Cell($Data->Width, $Data->Height,  '', $Data->Border, $Data->NewLine, $Data->Align, 0);
			   	} else {
					$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
					$this->Cell($Data->Width, $Data->Height,  $MyRow->Golds, $Data->Border, $Data->NewLine, $Data->Align, 0);
			   	}
				break;
			case 'XNineLeft':
			   	if($MyRow->XNine==$MyRow->SnapXNine || empty($MyRow->EqDistance)) {
					$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
					$this->Cell($Data->Width, $Data->Height,  $MyRow->XNine, $Data->Border, $Data->NewLine, $Data->Align, 0);
			   	} else {
					$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
					$this->Cell($Data->Width, $Data->Height,  $MyRow->SnapXNine, $Data->Border, $Data->NewLine, $Data->Align, 0);
			   	}
				break;
			case 'XNineRight':
			   	if($MyRow->XNine==$MyRow->SnapXNine || empty($MyRow->EqDistance)) {
					$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
					$this->Cell($Data->Width, $Data->Height,  '', $Data->Border, $Data->NewLine, $Data->Align, 0);
			   	} else {
					$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
					$this->Cell($Data->Width, $Data->Height,  $MyRow->XNine, $Data->Border, $Data->NewLine, $Data->Align, 0);
			   	}
				break;
			default:
		  		$this->SetFont($this->{$Data->FontType}, $Data->FontWeight, $Data->FontSize);
				$this->Cell($Data->Width, $Data->Height,  $MyRow->$Column, $Data->Border, $Data->NewLine, $Data->Align, 0);
		}
	}


	function DrawShootOffLegend()
	{
//Globals
//Legenda per la possibilit� di partecipazione (STATUS)
		$this->SetXY(10,$this->GetY()+5);
		if(!$this->SamePage(10))
			$this->AddPage();
	   	$this->SetFont($this->FontStd,'B',$this->FontSizeLines);
		$this->Cell(190, 5, $this->LegendSO, 1, 1, 'C', 1);
	   	$this->SetFont($this->FontStd,'B',$this->FontSizeHead);
		$this->Cell(10, 4, $this->CoinTossShort, 'TBL', 0, 'C', 1);
	   	$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(85, 4, $this->CoinToss, 'TBR', 0, 'L', 0);
	   	$this->SetFont($this->FontStd,'B',$this->FontSizeHead);
		$this->Cell(10, 4, $this->ShotOffShort, 'TBL', 0, 'C', 1);
	   	$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(85, 4, $this->ShotOff, 'TBR', 1, 'L', 0);
	}

	function DrawStatusLegend()
	{
	//Globals
		global $Arr_StrStatus;
	//Legenda per la possibilit� di partecipazione (STATUS)
		$this->SetXY(10,$this->GetY()+10);
		if(!$this->SamePage(5+ceil(count($Arr_StrStatus)/2)*4))
			$this->AddPage();
	   	$this->SetFont($this->FontStd,'B',$this->FontSizeLines);
		$this->Cell(190, 5, $this->LegendStatus, 1, 1, 'C', 1);
		$TmpCnt=0;
		if(!empty($Arr_StrStatus)) {
			foreach($Arr_StrStatus as $Key => $Value)
			{
				if($Key!=0)
				{
				    $this->SetFont($this->FontStd,'B',$this->FontSizeHead);
					$this->Cell(10, 4,  $Key, 1, 0, 'C', 1);
				    $this->SetFont($this->FontStd,'',$this->FontSizeHead);
					$this->Cell(85, 4,  $Value, 1, ($TmpCnt++ % 2), 'L', 0);
				}
			}
		}
		if($TmpCnt++ % 2) {
			$this->Cell(95, 4, '' , 1, 0, 'L', 0);
		}
	}

	function DrawParticipantHeader()
	{
		$this->SetFont($this->FontFix,'B',$this->FontSizeHeadSmall);
		$this->Cell(14, 4, '', 1, 0, 'C', 1);
		$this->SetX($this->GetX()-13);
		$this->Cell(2, 4, '1', 0, 0, 'C', 0, '', 0);
		$this->SetX($this->GetX()+0.5);
		$this->Cell(2, 4, '2', 0, 0, 'C', 0, '', 0);
		$this->SetX($this->GetX()+0.5);
		$this->Cell(2, 4, '3', 0, 0, 'C', 0, '', 0);
		$this->SetX($this->GetX()+0.5);
		$this->Cell(2, 4, '4', 0, 0, 'C', 0, '', 0);
		$this->SetX($this->GetX()+0.5);
		$this->Cell(2, 4, '5', 0, 0, 'C', 0, '', 0);
		$this->SetX($this->GetX()+1);
	}

	function DrawParticipantDetails($IndC='0', $IndF='0', $TeamC='0', $TeamF='0', $TeamMix='0', $bgColor=0)
	{
		$this->SetFont($this->FontStd, '', 4);
		$this->Cell(14, $this->RealCellHeight,  '', 1, 0, 'C', $bgColor);
		$draw=($IndC != -1);
		$this->SetFillColor(0x33,0x33,0x33);
		$this->SetXY($this->GetX()-13, $this->GetY()+1);
		$this->Cell(2, 2,  '', $draw ? 1-$IndC : 0, 0, 'C', $IndC);
		$this->SetX($this->GetX()+0.5);
		$this->Cell(2, 2,  '', $draw ? 1-$IndF : 0, 0, 'C', $IndF);
		$this->SetX($this->GetX()+0.5);
		$this->Cell(2, 2,  '', $draw ? 1-$TeamC : 0, 0, 'C', $TeamC);
		$this->SetX($this->GetX()+0.5);
		$this->Cell(2, 2,  '', $draw ? 1-$TeamF : 0, 0, 'C', $TeamF);
		$this->SetX($this->GetX()+0.5);
		$this->Cell(2, 2,  '', $draw ? 1-$TeamMix : 0, 0, 'C', $TeamMix);
		$this->SetXY($this->GetX()+1, $this->GetY()-1);
		$this->SetDefaultColor();
	}

	function DrawPartecipantLegend()
	{
	//Legenda per la Partecipazione a Gare
		$this->SetXY(10,$this->GetY()+5);
		if(!$this->SamePage(15))
			$this->AddPage();
	   	$this->SetFont($this->FontStd,'B',$this->FontSizeLines);
		$this->Cell(190, 5, $this->Partecipation, 1, 1, 'C', 1);
		$this->Cell(22, 10,  '', 1, 0, 'C', 0);
	//Disegna i PAllini per la partecipazione
		$this->SetXY($this->GetX()-20, $this->GetY()+2);
		$this->SetFont($this->FontFix,'B',$this->FontSizeHeadSmall);
		$this->Cell(2, 2,  '1', 0, 0, 'C', 0, '', 0);
		$this->SetX($this->GetX()+2);
		$this->Cell(2, 2,  '2', 0, 0, 'C', 0, '', 0);
		$this->SetX($this->GetX()+2);
		$this->Cell(2, 2,  '3', 0, 0, 'C', 0, '', 0);
		$this->SetX($this->GetX()+2);
		$this->Cell(2, 2,  '4', 0, 0, 'C', 0, '', 0);
		$this->SetX($this->GetX()+2);
		$this->Cell(2, 2,  '5', 0, 0, 'C', 0, '', 0);

		$this->SetXY($this->GetX()-18, $this->GetY()+3.5);
		$this->Cell(2, 2,  '', 1, 0, 'C', 1);
		$this->SetX($this->GetX()+2);
		$this->Cell(2, 2,  '', 1, 0, 'C', 1);
		$this->SetX($this->GetX()+2);
		$this->Cell(2, 2,  '', 1, 0, 'C', 1);
		$this->SetX($this->GetX()+2);
		$this->Cell(2, 2,  '', 1, 0, 'C', 1);
		$this->SetX($this->GetX()+2);
		$this->Cell(2, 2,  '', 1, 0, 'C', 1);
		$this->SetXY($this->GetX()+2, $this->GetY()-5.5);

	// Spiegazione dei 4 Pallini
		$this->SetFont($this->FontFix,'B',$this->FontSizeHeadSmall);
		$this->Cell(5, 5,  '1', 'LT', 0, 'C', 0);
		$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(38, 5, $this->IndQual, 'T', 0, 'L', 0);
		$this->SetFont($this->FontFix,'B',$this->FontSizeHeadSmall);
		$this->Cell(5, 5,  '2', 'T', 0, 'C', 0);
		$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(38, 5, $this->IndFin, 'T', 0, 'L', 0);
		$this->Cell(44, 5, '', 'RT', 1, 'L', 0);
		$this->SetX($this->GetX()+22);
		$this->SetFont($this->FontFix,'B',$this->FontSizeHeadSmall);
		$this->Cell(5, 5,  '3', 'LB', 0, 'C', 0);
		$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(38, 5, $this->TeamQual, 'B', 0, 'L', 0);
		$this->SetFont($this->FontFix,'B',$this->FontSizeHeadSmall);
		$this->Cell(5, 5,  '4', 'B', 0, 'C', 0);
		$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(38, 5, $this->TeamFin, 'B', 0, 'L', 0);
		$this->SetFont($this->FontFix,'B',$this->FontSizeHeadSmall);
		$this->Cell(5, 5,  '5', 'B', 0, 'C', 0);
		$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(39, 5, $this->MixedTeamFinEvent, 'RB', 0, 'L', 0);

		$this->SetXY($this->GetX(), $this->GetY()-5);
	//Legenda del S� e del No
		$this->SetFont($this->FontStd,'B',$this->FontSizeHead);
		$this->Cell(38, 10,  '', 1, 0, 'C', 0);
		$this->SetXY($this->GetX()-36, $this->GetY()+4);
		$this->SetFillColor(0x33,0x33,0x33);
		$this->Cell(2, 2,  '', 1, 0, 'C', 1);
		$this->SetDefaultColor();
		$this->Cell(2, 2,  '', 0, 0, 'C', 0);
		$this->Cell(12, 2, $this->Yes, 0, 0, 'L', 0);
		$this->Cell(2, 2,  '', 0, 0, 'C', 0);
		$this->Cell(2, 2,  '', 1, 0, 'C', 0);
		$this->Cell(2, 2,  '', 0, 0, 'C', 0);
		$this->Cell(12, 2, $this->No, 0, 1, 'L', 0);
		$this->SetXY($this->GetX(), $this->GetY()+5);
	}

	function PrintComponents($OldTarget, $Components, $force=false,$TargetFace=false) {
		if($OldTarget and ($Components['ok'] or $force)) {
			// prints the header
			$head=true;
			$this->SetFont($this->FontStd,'',1);
			$this->Cell(190, 0.5,  '', 0, 1, 'C', 0);
			$this->SetFont($this->FontStd,'B',$this->FontSizeLines);
	//		if(!$this->samepage(4*(count($Components['players'])+1))) $this->addpage();
			if(!empty($Components['header'][0])) { // BisValue
				$X=$this->GetX();
				$Y=$this->GetY();
				$this->setXY($X, $Y+4.5);
				$this->Cell(7, $this->RealCellHeight, $Components['header'][0], 'LB', 0, 'R', 0);

				$this->setXY($X, $Y);
			}
			$this->Cell(7, $this->RealCellHeight, !empty($Components['header'][1]) ? $Components['header'][1] : '', 'LTB', 0, 'R', 0);

			// print all components
			foreach($Components['players'] as $row) {
				$this->SetFont($this->FontStd,'B',$this->FontSizeLines);
				if($head) {
					$this->Cell(4, $this->RealCellHeight,  $row[0], 'RTB', 0, 'R', 0);
				} else {
					$this->Cell(7, $this->RealCellHeight,  '', 0, 0, 'R', 0);
					$this->Cell(4, $this->RealCellHeight,  $row[0], 1, 0, 'R', 0);
				}
				$this->SetFont($this->FontStd,'',$this->FontSizeHead);
				$this->Cell(10, $this->RealCellHeight,  $row[1], 1, 0, 'R', 0);
				$this->Cell(44, $this->RealCellHeight,  $row[2], 1, 0, 'L', 0);
				$this->Cell( 8, $this->RealCellHeight,  $row[3], 'LTB', 0, 'C', 0);
				$this->Cell(48, $this->RealCellHeight,  $row[4] . ($row[11]==null ? "" : " (" . $row[11] . " " . $row[12]. ")") . ($row[14]==null ? "" : " (" . $row[14]." ". $row[15].")"), 'RTB', 0, 'L', 0);
				if(!$this->HideCols && !$TargetFace)
				{
					$this->Cell(12, $this->RealCellHeight,  $row[5], 1, 0, 'C', 0);
					$this->Cell( 9, $this->RealCellHeight,  $row[6], 1, 0, 'C', 0);
				}
				$this->Cell(12 + ($this->HideCols==true ? ($TargetFace ? 12 : 23) : 0), $this->RealCellHeight, $row[7], 1, 0, 'C', 0);
				$this->Cell(12 + ($this->HideCols==true ? ($TargetFace ? 12 : 22) : 0), $this->RealCellHeight,  $row[8], 1, 0, 'C', 0);

				if ($TargetFace)
				{
					$this->Cell(21, $this->RealCellHeight,  get_text($row[13], 'Tournament', '', true), 1, 0, 'C', 0);
				}

		//Disegna i Pallini per la partecipazione
				if(!$this->HideCols)
				{
					$this->DrawParticipantDetails($row[9][0], $row[9][1], $row[9][2], $row[9][3], $row[9][4]);
					$this->SetDefaultColor();
					$this->SetFont($this->FontStd,'',$this->FontSizeHead);
					$this->Cell(10, $this->RealCellHeight,  $row[10] , 1, 0, 'C', 0);
				}
				$this->Cell(1, $this->RealCellHeight,  '' , 0, 1, 'C', 0);
				$head=false;
			}
		}
	}

	function writeDataRowPrnIndividualAbs($item, $distSize, $addSize, $running, $distances, $double, $snapDistance, $border='TB') {
		$this->SetFont($this->FontStd,'B',$this->FontSizeLines);
		$this->Cell(8, 4 * ($double ? 2 : 1),  $item['rank'], $border.'LR', 0, 'R', 0);
		//Atleta
		$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(7, 4 * ($double ? 2 : 1),  ($item['session'] . "- " . $item['target']), $border. 'L', 0, 'R', 0);
		$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(38+ $addSize, 4 * ($double ? 2 : 1),  $item['athlete'], $border. 'R', 0, 'L', 0);
		//Classe
		$this->SetFont($this->FontStd,'',$this->FontSizeHeadSmall);
		$this->Cell(5, 4 * ($double ? 2 : 1), ($item['class']), $border.'L', 0, 'C', 0);
		$this->SetFont($this->FontStd,'',5);
		$this->Cell(5, 4 * ($double ? 2 : 1), ($item['class']!=$item['ageclass'] ?  ' ' . ( $item['ageclass']) : ''), $border.'R', 0, 'C', 0);
		//Nazione
		$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(8, 4 * ($double ? 2 : 1),  $item['countryCode'], $border.'L', 0, 'L', 0);
		$this->Cell(43 + $addSize, 4 * ($double ? 2 : 1),  $item['countryName'], $border.'R', 0, 'L', 0);
		$this->SetFont($this->FontFix,'',$this->FontSizeHead);
		if(!$double)
		{
			for($i=1; $i<=$distances;$i++)
			{
				list($rank,$score)=explode('|',$item['dist_' . $i]);
				if($snapDistance==0)
				{
					$cellContent=str_pad($score,3," ",STR_PAD_LEFT);
					if($rank) $cellContent.="/" . str_pad($rank,2," ",STR_PAD_LEFT);
					$this->Cell($distSize, 4,  $cellContent, $border.'LR', 0, 'R', 0);
				}
				elseif($i<$snapDistance)
				{
					$this->Cell($distSize/2, 4, str_pad($score,3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
					$this->Cell($distSize/2, 4, "", $border.'R', 0, 'R', 0);
				}
				else if($i==$snapDistance)
				{
					list($rankS,$scoreS)=explode('|',$item['dist_Snap']);
					$this->Cell($distSize/2, 4, str_pad($scoreS,3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
					$this->Cell($distSize/2, 4, ($scoreS != $score ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), $border.'R', 0, 'R', 0);
				}
				else
				{
					$this->Cell($distSize/2, 4, str_pad("0",3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
					$this->Cell($distSize/2, 4, ($score!=0 ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), $border.'R', 0, 'R', 0);
				}
			}
		}
		else
		{
			$TmpX=$this->GetX();
			$TmpY=$this->GetY();
			$RunningTotal=0;
			for($i=1; $i<=$distances/2;$i++)
			{
				list($rank,$score)=explode('|',$item['dist_' . $i]);
				if($snapDistance==0)
				{
					$cellContent=str_pad($score,3," ",STR_PAD_LEFT);
					if($rank) $cellContent.="/" . str_pad($rank,2," ",STR_PAD_LEFT);
					$this->Cell($distSize, 4,  $cellContent, $border.'LR', 0, 'R', 0);
				}
				elseif($i<$snapDistance)
				{
					$this->Cell($distSize/2, 4, str_pad($score,3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
					$this->Cell($distSize/2, 4, "", $border.'R', 0, 'R', 0);
				}
				else if($i==$snapDistance)
				{
					list($rankS,$scoreS)=explode('|',$item['dist_Snap']);
					$this->Cell($distSize/2, 4, str_pad($scoreS,3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
					$this->Cell($distSize/2, 4, ($scoreS != $score ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), $border.'R', 0, 'R', 0);
				}
				else
				{
					$this->Cell($distSize/2, 4, str_pad("0",3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
					$this->Cell($distSize/2, 4, ($score!=0 ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), $border.'R', 0, 'R', 0);
				}
				$RunningTotal += $score;
			}
			$this->Cell($distSize, 4, number_format($RunningTotal,0,'',$this->NumberThousandsSeparator), 1, 0, 'R', 0);
			$this->setXY($TmpX,$TmpY+4);
			$RunningTotal=0;
			for($i; $i<=$distances;$i++)
			{
				list($rank,$score)=explode('|',$item['dist_' . $i]);
				if($snapDistance==0)
				{
					$cellContent=str_pad($score,3," ",STR_PAD_LEFT);
					if($rank) $cellContent.="/" . str_pad($rank,2," ",STR_PAD_LEFT);
					$this->Cell($distSize, 4,  $cellContent, $border.'LR', 0, 'R', 0);
				}
				elseif($i<$snapDistance)
				{
					$this->Cell($distSize/2, 4, str_pad($score,3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
					$this->Cell($distSize/2, 4, "", $border.'R', 0, 'R', 0);
				}
				else if($i==$snapDistance)
				{
					list($rankS,$scoreS)=explode('|',$item['dist_Snap']);
					$this->Cell($distSize/2, 4, str_pad($scoreS,3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
					$this->Cell($distSize/2, 4, ($scoreS != $score ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), $border.'R', 0, 'R', 0);
				}
				else
				{
					$this->Cell($distSize/2, 4, str_pad("0",3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
					$this->Cell($distSize/2, 4, ($score!=0 ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), $border.'R', 0, 'R', 0);
				}
				$RunningTotal += $score;
			}
			$this->Cell($distSize, 4, number_format($RunningTotal,0,'',$this->NumberThousandsSeparator), $border.'LR', 0, 'R', 0);
			$this->setXY($this->GetX(),$TmpY);
		}
	  	$this->SetFont($this->FontFix,'B',$this->FontSizeLines);
	  	if(!$running)
	  	{
	  	  	if($snapDistance==0)
				$this->Cell(12, 4 * ($double ? 2 : 1), number_format($item['score'],0,'',$this->NumberThousandsSeparator), $border.'LR', 0, 'R', 0);
			else
			{
				$this->Cell(6, 4 * ($double ? 2 : 1), number_format($item['scoreSnap'],0,'',$this->NumberThousandsSeparator), $border.'L', 0, 'R', 0);
				$this->SetFont($this->FontFix,'',$this->FontSizeHead);
				$this->Cell(6, 4 * ($double ? 2 : 1), ($item['score']==$item['scoreSnap'] ? '' : '(' . number_format($item['score'],0,'',$this->NumberThousandsSeparator) . ')'), $border.'R', 0, 'R', 0);
			}
	  	}
		$this->SetFont($this->FontFix,'',$this->FontSizeLines);
		if($this->ShowTens) {
		  	if($snapDistance==0) {
		  		$this->SetFont($this->FontFix,'',$this->FontSizeLines);
				$this->Cell(6, 4 * ($double ? 2 : 1), $item['gold'], $border.'LR', 0, 'R', 0);
		  	} else {
				$this->SetFont($this->FontFix,'',$this->FontSizeHeadSmall);
				$this->Cell(6, 4 * ($double ? 2 : 1), str_pad($item['goldSnap'],2," ", STR_PAD_LEFT) . ($item['gold']==$item['goldSnap'] ? "": "(". str_pad($item['gold'],2," ", STR_PAD_LEFT). ")"), $border.'LR', 0, 'R', 0);
			}
		}
		$this->SetFont($this->FontFix,'',$this->FontSizeLines);
	  	if($snapDistance==0) {
	  		$this->SetFont($this->FontFix,'',$this->FontSizeLines);
	  		$this->Cell(6 * ($this->ShowTens ? 1:2), 4 * ($double ? 2 : 1), $item['xnine'],$border.'LR', 0, 'R', 0);
	  	} else {
			$this->SetFont($this->FontFix,'',$this->FontSizeHeadSmall);
			$this->Cell(6 * ($this->ShowTens ? 1:2), 4 * ($double ? 2 : 1), str_pad($item['xnineSnap'],2," ", STR_PAD_LEFT) . ($item['xnine']==$item['xnineSnap'] ? "": "(". str_pad($item['xnine'],2," ", STR_PAD_LEFT). ")"), $border.'LR', 0, 'R', 0);
		}
		if($running)
		{
			$this->Cell(8, 4 * ($double ? 2 : 1),  $item['hits'], $border.'LR', 0, 'R', 0);
			$this->SetFont($this->FontFix,'B',$this->FontSizeLines);
			$this->Cell(12, 4 * ($double ? 2 : 1),  number_format($item['score'],3,$this->NumberDecimalSeparator,$this->NumberThousandsSeparator), $border.'LR', 1, 'R', 0);
		} else {
		//Definizione dello spareggio/Sorteggio
			$this->SetFont($this->FontStd,'I',5);
			if($this->ShowCTSO) {
				if(!empty($item['so']) &&  $item['so']>0) {
					$tmpArr="";
					if(strlen(trim($item['tiebreak']))) {
						$tmpArr=".";
						for($countArr=0; $countArr<strlen(trim($item['tiebreak'])); $countArr++) {
							$tmpArr .= DecodeFromLetter(substr(trim($item['tiebreak']),$countArr,1)) . ",";
						}
						$tmpArr = substr($tmpArr,0,-1);

					}
					$this->Cell(8, 4 * ($double ? 2 : 1),  ($this->ShotOffShort . $tmpArr), $border.'LR', 1, 'L', 1);
				} elseif(!empty($item['ct']) &&  $item['ct']>1) {
					$this->Cell(8, 4 * ($double ? 2 : 1),  $this->CoinTossShort, $border.'LR', 1, 'L', 0);
				} else {
					$this->Cell(8, 4 * ($double ? 2 : 1),  '', $border.'LR', 1, 'R', 0);
				}
			} else {
				$this->Cell(8, 4 * ($double ? 2 : 1),  '', $border.'LR', 1, 'R', 0);
			}
		}
	}

	function writeGroupHeaderPrnIndividualAbs($section, $distSize, $addSize, $running, $distances, $double, $follows=false)
	{
		$tmpHeader="";
		$this->SetFont($this->FontStd,'B',$this->FontSizeTitle);
		if (!empty($section['sesArrows']))
		{
			foreach($section['sesArrows'] as $k=>$v)
			{
				if($v)
				{
					if(strlen($tmpHeader)!=0)
						$tmpHeader .= " - ";
					$tmpHeader .= $v;
					if(count($section['sesArrows'])!=1)
						$tmpHeader .= " (" . $section['fields']['session'] . ": " . $k  . ")";
				}

			}
		}
		// testastampa
		if (strlen($section['printHeader']))
			$this->Cell(0, 7.5, $section['printHeader'], 0, 1, 'R', 0);
		else if(strlen($tmpHeader)!=0 && !$section['running'])
			$this->Cell(0, 7.5, $tmpHeader, 0, 1, 'R', 0);


		$this->SetFont($this->FontStd,'B',$this->FontSizeTitle);
		$this->Cell(0, 6,  $section['descr'], 1, 1, 'C', 1);
		if($follows)
		{
			$this->SetXY(170,$this->GetY()-6);
		   	$this->SetFont($this->FontStd,'',6);
			$this->Cell(0, 6, $this->Continue, 0, 1, 'R', 0);
		}
	   	$this->SetFont($this->FontStd,'B',$this->FontSizeHead);
		$this->Cell(8, 4 * ($double ? 2 : 1),  $section['fields']['rank'], 1, 0, 'C', 1);

		$this->Cell(45 + $addSize, 4 * ($double ? 2 : 1),  $section['fields']['athlete'], 1, 0, 'L', 1);
		$this->Cell(10, 4 * ($double ? 2 : 1),  $section['fields']['class'], 1, 0, 'C', 1);

		$this->Cell(51 + $addSize, 4 * ($double ? 2 : 1),  $section['fields']['countryName'], 1, 0, 'L', 1);
		if(!$double)
		{
			for($i=1; $i<=$distances;$i++)
			$this->Cell($distSize, 4,  $section['fields']['dist_'. $i], 1, 0, 'C', 1);
		}
		else
		{
			$TmpX=$this->GetX();
			$TmpY=$this->GetY();
			for($i=1; $i<=$distances/2;$i++)
				$this->Cell($distSize, 4, $section['fields']['dist_'. $i], 1, 0, 'C', 1);
			$this->Cell($distSize, 4, $this->TotalShort, 1, 0, 'C', 1);
			$this->setXY($TmpX,$TmpY+4);
			for($i; $i<=$distances;$i++)
				$this->Cell($distSize, 4, $section['fields']['dist_'. $i], 1, 0, 'C', 1);
			$this->Cell($distSize, 4, $this->TotalShort, 1, 0, 'C', 1);
			$this->setXY($this->GetX(),$TmpY);
		}
		if(!$running)
			$this->Cell(12, 4 * ($double ? 2 : 1),  $section['fields']['score'], 1, 0, 'C', 1);
		if($this->ShowTens)
			$this->Cell(6, 4 * ($double ? 2 : 1),  $section['fields']['gold'], 1, 0, 'C', 1);
		$this->Cell(6 * ($this->ShowTens?1:2), 4 * ($double ? 2 : 1),  $section['fields']['xnine'], 1, 0, 'C', 1);
		if($running)
		{
			$this->Cell(8, 4 * ($double ? 2 : 1),  $section['fields']['hits'], 1, 0, 'C', 1);
			$this->Cell(12, 4 * ($double ? 2 : 1),  $section['fields']['score'], 1, 1, 'C', 1);
		}
		else
			$this->Cell(8, 4 * ($double ? 2 : 1),  '', 1, 1, 'C', 1);
		$this->SetFont($this->FontStd,'',1);
		$this->Cell(0, 0.5,  '', 1, 1, 'C', 0);
	}

	function writeGroupHeaderPrnTeamAbs($section,$follows=false)
	{
		$tmpHeader="";
		$this->SetFont($this->FontStd,'B',$this->FontSizeTitle);
		if (!empty($section['sesArrows']))
		{
			foreach($section['sesArrows'] as $k=>$v)
			{
				if($v)
				{
					if(strlen($tmpHeader)!=0)
						$tmpHeader .= " - ";
					$tmpHeader .= $v;
					if(count($section['sesArrows'])!=1)
						$tmpHeader .= " (" . $section['fields']['session'] . ": " . $k  . ")";
				}

			}
		}
		// testastampa
		if (strlen($section['printHeader']))
			$this->Cell(190, 7.5, $section['printHeader'], 0, 1, 'R', 0);
		else if(strlen($tmpHeader)!=0 && !$section['running'])
			$this->Cell(190, 7.5, $tmpHeader, 0, 1, 'R', 0);

		$this->SetFont($this->FontStd,'B',$this->FontSizeTitle);
		$this->Cell(190, 6,  $section['descr'], 1, 1, 'C', 1);
		if($follows)
		{
			$this->SetXY(170,$this->GetY()-6);
		   	$this->SetFont($this->FontStd,'',6);
			$this->Cell(30, 6,  $this->Continue, 0, 1, 'R', 0);
		}

		$this->SetFont($this->FontStd,'B',$this->FontSizeHead);
		$this->Cell(9, 4,  $section['fields']['rank'], 1, 0, 'C', 1);
		$this->Cell(53 - ($section['running'] ? 5 : 0), 4, $section['fields']['countryName'], 1, 0, 'L', 1);
		$this->Cell(43 - ($section['running'] ? 5 : 0), 4, $section['fields']['athletes']['name'], 1, 0, 'L', 1);
		$this->Cell(12, 4, $section['fields']['athletes']['fields']['div'], 1, 0, 'C', 1);
		$this->Cell(11, 4, $section['fields']['athletes']['fields']['ageclass'], 1, 0, 'C', 1);
		$this->Cell(11, 4, $section['fields']['athletes']['fields']['class'], 1, 0, 'C', 1);
		$this->Cell(8, 4,  $section['fields']['athletes']['fields']['subclass'], 1, 0, 'C', 1);
		if($section['running'])
			$this->Cell(15, 4, $section['fields']['hits'], 1, 0, 'C', 1);
		$this->Cell(20 - ($section['running'] ? 5 : 0), 4, $section['fields']['score'], 1, 0, 'C', 1);
		$this->Cell(9, 4, $section['fields']['gold'], 1, 0, 'C', 1);
		$this->Cell(9, 4, $section['fields']['xnine'], 1, 0, 'C', 1);
		$this->Cell(5, 4, '', 1, 1, 'C', 1);
		$this->SetFont($this->FontStd,'',1);
		$this->Cell(190, 0.5,  '', 1, 1, 'C', 0);
	}

	function writeGroupHeaderPrnShooOffTeamAbs($section,$follows=false)
	{
		// testastampa
		if (strlen($section['printHeader']))
		{
			$this->SetFont($this->FontStd,'B',$this->FontSizeTitle);
			$this->Cell(190, 7.5,  $section['printHeader'], 0, 1, 'R', 0);
		}

		$this->SetFont($this->FontStd,'B',$this->FontSizeTitle);
		$this->Cell(190, 6,  $section['descr'], 1, 1, 'C', 1);
		if($follows)
		{
			$this->SetXY(170,$this->GetY()-6);
			$this->SetFont($this->FontStd,'',6);
			$this->Cell(30, 6,  $this->Continue, 0, 1, 'R', 0);
		}

		$this->SetFont($this->FontStd,'B',$this->FontSizeHead);
		$this->Cell(9, 4,  $section['fields']['rank'], 1, 0, 'C', 1);
		$this->Cell(55, 4, $section['fields']['countryName'], 1, 0, 'L', 1);
		$this->Cell(94, 4, $section['fields']['athletes']['name'], 1, 0, 'L', 1);
		$this->Cell(12, 4, $section['fields']['score'], 1, 0, 'C', 1);
		$this->Cell(6, 4, $section['fields']['gold'], 1, 0, 'C', 1);
		$this->Cell(6, 4, $section['fields']['xnine'], 1, 0, 'C', 1);
		$this->Cell(8, 4, '', 1, 1, 'C', 1);
		$this->SetFont($this->FontStd,'',1);
		$this->Cell(190, 0.5,  '', 1, 1, 'C', 0);
	}

	function writeDataRowPrnTeamAbs($item, $endQualified, $running)
	{
		if($endQualified)
		{
			$this->SetFont($this->FontStd,'',1);
			$this->Cell(190, 1,  '', 1, 1, 'C', 1);
		}
		$this->SetFont($this->FontStd,'B',$this->FontSizeLines);
		$height=4*count($item['athletes']);

		$this->SetFont($this->FontStd,'B',$this->FontSizeLines);
		$this->Cell(9, $height,  $item['rank'], 1, 0, 'R', 0);

		$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(8, $height,  $item['countryCode'], 'LTB', 0, 'C', 0);
		$this->Cell(45 - ($running ? 5 : 0), $height,  $item['countryName'] . (intval($item['subteam'])<=1 ? '' : ' (' . $item['subteam'] .')'), 'RTB', 0, 'L', 0);

		$X=$this->GetX();
		$Y=$this->GetY();

		$this->SetX(166 - ($running ? 19 : 0));
		if($running)
			$this->Cell(15, $height, $item['hits'], 1, 0, 'R', 0);
		$this->SetFont($this->FontFix,'B',$this->FontSizeLines);
		$this->Cell(11 + ($running ? 4 : 0), $height,  number_format($item['score'],($running ? 3:0),$this->NumberDecimalSeparator,$this->NumberThousandsSeparator), 1, 0, 'R', 0);

		$this->SetFont($this->FontFix,'',$this->FontSizeLines);
		$this->Cell(9, $height,  $item['gold'], 1, 0, 'R', 0);
		$this->Cell(9, $height,  $item['xnine'], 1, 0, 'R', 0);

		$txt='';
		$fill=0;
		if($item['so']>0)  //Spareggio
		{
			$txt=$this->ShotOffShort;
			$fill=1;
		}
		elseif ($item['ct']>1)
		{
			$txt=$this->CoinTossShort;
			$fill=1;
		}
		$this->SetFont($this->FontStd,'',5);
		$this->Cell(5, $height,  $txt, 1, 0, 'L', $fill);

		$this->SetXY($X,$Y);


		foreach ($item['athletes'] as $a)
		{
			$this->SetFont($this->FontStd,'',$this->FontSizeHead);
			$this->Cell(43 - ($running ? 5 : 0), 4,  $a['athlete'], 1, 0, 'L', 0);
			$this->Cell(12, 4,  $a['div'], 1, 0, 'C', 0);
			$this->Cell(11, 4,  $a['ageclass'], 1, 0, 'C', 0);
			$this->Cell(11, 4,  $a['class'], 1, 0, 'C', 0);
			$this->Cell(8, 4,  $a['subclass'], 1, ($running ? 1 : 0), 'C', 0);
			$this->SetFont($this->FontFix,'',$this->FontSizeHead);
			if(!$running)
				$this->Cell(9, 4,  number_format($a['quscore'],0,'',$this->NumberThousandsSeparator), 1, 1, 'R', 0);
			$this->SetX(72 - ($running ? 5 : 0));
		}

		$this->SetX(10);
	}

	function writeDataRowPrnShootOffTeamAbs($item, $border='TB')
	{
		$this->SetFont($this->FontStd,'B',$this->FontSizeLines);

		$this->SetFont($this->FontStd,'B',$this->FontSizeLines);
		$this->Cell(9, 4,  $item['rank'], $border.'LR', 0, 'R', 0);

		$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(8, 4,  $item['countryCode'],$border.'L', 0, 'C', 0);
		$this->Cell(47, 4,  $item['countryName'] . (intval($item['subteam'])<=1 ? '' : ' (' . $item['subteam'] .')'), $border.'R', 0, 'L', 0);

		$tmpNames="";
		foreach ($item['athletes'] as $a)
			$tmpNames .= $a['athlete'] . "(" . ($a['session'] . "-" . $a['target']) . ")" . ", ";
		$this->Cell(94, 4, substr($tmpNames,0,-2), $border.'LR', 0, 'L', 0);

		$this->SetFont($this->FontFix,'B',$this->FontSizeLines);
		$this->Cell(12, 4,  number_format($item['score'],0,'',$this->NumberThousandsSeparator), $border.'LR', 0, 'R', 0);

		$this->SetFont($this->FontFix,'',$this->FontSizeLines);
		$this->Cell(6, 4,  $item['gold'], $border.'LR', 0, 'R', 0);
		$this->Cell(6, 4,  $item['xnine'], $border.'LR', 0, 'R', 0);

		$txt='';
		$fill=0;
		if($item['so']>0)  //Spareggio
		{
			$txt=$this->ShotOffShort;
			$fill=1;
		}
		elseif ($item['ct']>1)
		{
			$txt=$this->CoinTossShort;
			$fill=0;
		}
		$this->SetFont($this->FontStd,'I',5);
		$this->Cell(8, 4,  $txt, $border.'LR', 1, 'L', $fill);
	}

	function writeGroupHeaderElimInd($section, $distSize, $addSize, $running, $follows=false) {
		// testastampa
	//	if (strlen($section['printHeader']))
	//	{
	//		$pdf->SetFont($pdf->FontStd,'B',$this->FontSizeTitle);
	//		$pdf->Cell(190, 7.5,  (get_text($section['printHeader'],'','',true)), 0, 1, 'R', 0);
	//	}
		$this->SetFont($this->FontStd,'B',$this->FontSizeTitle);
		$this->Cell(190, 6,  $section['descr'] . " - " . $section['round'], 1, 1, 'C', 1);
		if($follows)
		{
			$this->SetXY(170,$this->GetY()-6);
		   	$this->SetFont($this->FontStd,'',6);
			$this->Cell(30, 6, $this->Continue, 0, 1, 'R', 0);
		}
	//Calcolo Le Misure per i Campi
	   	$this->SetFont($this->FontStd,'B',$this->FontSizeHead);
		$this->Cell(10, 4,  $section['fields']['rank'], 1, 0, 'C', 1);
		$this->Cell(55, 4,  $section['fields']['athlete'], 1, 0, 'L', 1);
		$this->Cell(10, 4,  $section['fields']['class'], 1, 0, 'C', 1);
		$this->Cell(55, 4,  $section['fields']['countryName'], 1, 0, 'L', 1);
		$this->Cell(14, 4,  $section['round'], 1, 0, 'C', 1);
		$this->Cell(10, 4,  $section['fields']['gold'], 1, 0, 'C', 1);
		$this->Cell(10, 4,  $section['fields']['xnine'], 1, 0, 'C', 1);
		if($running)
		{
			$this->Cell(14, 4,  $section['fields']['hits'], 1, 0, 'C', 1);
			$this->Cell(12, 4,  $section['fields']['score'], 1, 1, 'C', 1);
		}
		else
		{
			$this->Cell(14, 4,  $section['fields']['completeScore'], 1, 0, 'C', 1);
			$this->Cell(12, 4,  '', 1, 1, 'C', 1);
		}
		$this->SetFont($this->FontStd,'',1);
		$this->Cell(190, 0.5,  '', 1, 1, 'C', 0);
	}

	function writeDataRowElimInd($item, $distSize, $addSize, $running, $endQualified) {
		if($endQualified)
		{
			$this->SetFont($this->FontStd,'',1);
			$this->Cell(190, 1,  '', 1, 1, 'C', 1);
		}
		$this->SetFont($this->FontStd,'B',$this->FontSizeLines);
		$this->Cell(10, 4, $item['rank'], 1, 0, 'R', 0);
		$this->SetFont($this->FontStd,'',$this->FontSizeHeadSmall);
		$this->Cell(8, 4, $item['target'], 'TLB', 0, 'R', 0);
		$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(47, 4, $item['athlete'], 'TRB', 0, 'L', 0);
		$this->SetFont($this->FontStd,'',$this->FontSizeHeadSmall);
		$this->Cell(5, 4, $item['class'], 'TBL', 0, 'C', 0);
		$this->SetFont($this->FontStd,'',5);
		$this->Cell(5, 4, ($item['class']!=$item['ageclass'] ?  ' ' . ( $item['ageclass']) : ''), 'TBR', 0, 'C', 0);

		$this->SetFont($this->FontStd,'',$this->FontSizeHead);
		$this->Cell(10, 4, $item['countryCode'], 'LTB', 0, 'C', 0);
		$this->Cell(45, 4, $item['countryName'], 'RTB', 0, 'L', 0);
		$this->SetFont($this->FontFix,'B',$this->FontSizeLines);
		$this->Cell(14, 4,  number_format($item['completeScore'],0,'',$this->NumberThousandsSeparator), 1, 0, 'R', 0);
		$this->SetFont($this->FontFix,'',$this->FontSizeLines);
		$this->Cell(10, 4, $item['gold'], 1, 0, 'R', 0);
		$this->Cell(10, 4, $item['xnine'], 1, 0, 'R', 0);
		if($running)
		{
			$this->Cell(14, 4, $item['hits'], 1, 0, 'R', 0);
			$this->Cell(12, 4, number_format($item['score'],3,$this->NumberDecimalSeparator,$this->NumberThousandsSeparator), 1, 1, 'R', 0);
		}
		else
		{
			$this->Cell(14, 4, number_format($item['score'],0,'',$this->NumberThousandsSeparator), 1, 0, 'R', 0);
			//Definizione dello spareggio/Sorteggio
			$this->SetFont($this->FontStd,'I',5);
			if($item['so']>0)  //Spareggio
			{
				$tmpArr="";
				if(strlen(trim($item['tiebreak'])))
				{
					$tmpArr=".";
					for($countArr=0; $countArr<strlen(trim($item['tiebreak'])); $countArr++)
						$tmpArr .= DecodeFromLetter(substr(trim($item['tiebreak']),$countArr,1)) . ",";
					$tmpArr = substr($tmpArr,0,-1);
				}
				//$pdf->Cell(12, 4 * ($double ? 2 : 1),  (get_text('ShotOffShort','Tournament') . $tmpArr), 1, 1, 'L', 1);
				$this->Cell(12, 4 ,  ($this->ShotOffShort . $tmpArr), 1, 1, 'L', 1);
			}
			elseif($item['ct']>1)
				//$pdf->Cell(12, 4 * ($double ? 2 : 1),  (get_text('CoinTossShort','Tournament')), 1, 1, 'L', 0);
				$this->Cell(12, 4,  ($this->CoinTossShort), 1, 1, 'L', 0);
			else
				//$pdf->Cell(12, 4 * ($double ? 2 : 1),  '', 1, 1, 'R', 0);
				$this->Cell(12, 4,  '', 1, 1, 'R', 0);
		}
	}

}
?>