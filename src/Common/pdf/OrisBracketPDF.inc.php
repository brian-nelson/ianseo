<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once("Common/pdf/OrisPDF.inc.php");

class OrisBracketPDF extends OrisPDF
{
	var $CellHSp = 0;
	var $CellVSp = 0;
	var $TeamMatchNoPosition = array(0,0,0,0);

	//Constructor
	function OrisBracketPDF($DocNumber, $DocTitle, $Headers='')
	{
		//$this->__construct('P','mm','A4');
		parent::__construct($DocNumber, $DocTitle, $Headers);
		$this->SetAutoPageBreak(false);
	}


	function FirstColumn($item, $TargetType='')
	{
		$this->SetLineWidth(0.1);
		$this->SetFont('','',8);
		$this->SetXY(OrisPDF::leftMargin,$this->lastY);
		$this->Cell(8,$this->CellVSp, is_null($item->FirstName) ? '' : $item->IndRank . '/',0,0,'R');
		$this->Cell(10,$this->CellVSp,$item->QuScore,0,0,'R');
		$this->Cell(9,$this->CellVSp, is_null($item->FirstName) ? '' : $item->GrPosition,0,0,'R');

		if(!is_null($item->FirstName))
			$this->Cell(40,$this->CellVSp,$item->FirstName . ' ' . $item->Name,0,0,'L');
		elseif($item->Saved) {
			$this->setfont('','i');
			$this->Cell(40,$this->CellVSp,$item->Saved,0,0,'L');
			$this->setfont('','');
		}
		elseif($item->OppTie==2)
			$this->Cell(40,$this->CellVSp,'-Bye-',0,0,'L');
		else
			$this->Cell(40,$this->CellVSp,'',0,0,'L');
		$this->Cell(13,$this->CellVSp,$item->Country,0,0,'L');
		//Punteggio
		$tmpScore = "";
		if($item->Score==0 && $item->OppScore==0)
		{
			if($item->OppTie==2 && !empty($item->FirstName))
				$tmpScore = '-Bye-';
			else if($item->FinTie==2 || $item->OppTie==2)
				$tmpScore = ' ';
			else
				$tmpScore = ($item->FSTarget != '' && $item->FSTarget != 0 ? 'T# ' . $item->FSTarget : '');
		}
		else
			$tmpScore = (!empty($item->FirstName) ? $item->Score : '');
		//Gestisco cosa scrivere nel tie
		if(strlen(trim($item->FinTiebreak)) > 0)
		{
			$tmpArr="";
			for($countArr=0; $countArr<strlen(trim($item->FinTiebreak)); $countArr++)
				$tmpArr .= DecodeFromLetter(substr(trim($item->FinTiebreak),$countArr,1)) . ",";
			$tmpScore .= " T." . substr($tmpArr,0,-1);
		}
		else if($item->FinTie==1)
			$tmpScore .= " *";
		//Punti dei Set, se presenti
		if(!empty($item->SetPoints))
		{

			$tmpSetPoint = "";
			$thisSetPoint=explode("|",$item->SetPoints);
			$otherSetPoint=explode("|",$item->oppSetPoints);

			for($cntSetPoint=0; $cntSetPoint<count($thisSetPoint); $cntSetPoint++)
			{
				if(@$thisSetPoint[$cntSetPoint]!=0 || @$otherSetPoint[$cntSetPoint]!=0)
					$tmpSetPoint .= @$thisSetPoint[$cntSetPoint].",";
			}

			if(strlen($tmpSetPoint)>0)
				$tmpScore .= ' (' . substr($tmpSetPoint,0,-1) . ')';
		}

		$this->Cell($this->CellHSp-1,$this->CellVSp,$tmpScore,0,0,'L');
		$this->setX($this->getX()+1);

		if($item->FinMatchNo % 2 == 0 and $item->DrawMatch)
		{
			//Linee Orizzontali
			//$this->Line($this->getX()+$this->CellHSp-20,$this->getY(),$this->getX()+$this->CellHSp-25,$this->getY());
			//$this->Line($this->getX()+$this->CellHSp-20,$this->getY()+2*$this->CellVSp,$this->getX()+$this->CellHSp-25,$this->getY()+2*$this->CellVSp);
			$this->Line($this->getX(),$this->getY(),$this->getX()-5,$this->getY());
			$this->Line($this->getX(),$this->getY()+2*$this->CellVSp,$this->getX()-5,$this->getY()+2*$this->CellVSp);
			//Linea Verticale
			//$this->Line($this->getX()+$this->CellHSp-20,$this->getY(),$this->getX()+$this->CellHSp-20,$this->getY()+2*$this->CellVSp);
			$this->Line($this->getX(),$this->getY(),$this->getX(),$this->getY()+2*$this->CellVSp);
			if($item->ScheduledDate != '' && $item->ScheduledTime != '')
			{
				$this->SetFont('','',6);
				$this->SetXY(OrisPDF::leftMargin+80,$this->getY()+0.4*$this->CellVSp);
				$this->Cell($this->CellHSp,0.6*$this->CellVSp, (($item->FinTie==0 && $item->OppTie==0 && $item->Score==0 && $item->OppScore==0) ? $item->ScheduledDate : ''), 0, 0, 'R', 0);
				$this->SetXY(OrisPDF::leftMargin+80,$this->getY()+0.6*$this->CellVSp);
				$this->Cell($this->CellHSp,0.6*$this->CellVSp, (($item->FinTie==0 && $item->OppTie==0 && $item->Score==0 && $item->OppScore==0) ? $item->ScheduledTime : ''), 0, 0, 'R', 0);
			}
		}

		$this->lastY += (($item->FinMatchNo % 2 == 0 ? 1:2) * $this->CellVSp);
	}

	function OtherColumns($PhaseCounter, $FinMatchNo, $FirstName, $Name, $Country, $FinScore, $FinTie, $FinTieBreak, $FinSetPoints, $OppScore, $OppTie, $TargetNo, $ScheduledDate, $ScheduledTime, $TargetType='')
	{
		$this->SetLineWidth(0.1);
		$this->SetFont('','',8);
		$this->SetXY(90+$PhaseCounter*$this->CellHSp,$this->lastY);
		mb_regex_encoding('UTF-8');
		if(!is_null($FirstName)) {
			$this->Cell($this->CellHSp,$this->CellVSp,$FirstName . ' ' . $this->FirstLetters($Name) . " (" . $Country . ")" ,0,0,'L');
		} elseif($OppTie==2)
			$this->Cell($this->CellHSp,$this->CellVSp,'-Bye-',0,0,'L');
		else
			$this->Cell($this->CellHSp,$this->CellVSp,'',0,0,'L');

		$this->lastY += $this->CellVSp;
		$this->SetXY(90+$PhaseCounter*$this->CellHSp,$this->lastY);

		//Punteggio
		$tmpScore = "";
		if($FinScore==0 && $OppScore==0)
		{
			if($OppTie==2 && !empty($FirstName))
				$tmpScore = '-Bye-';
			else if($FinTie==2 || $OppTie==2)
				$tmpScore = ' ';
			else
				$tmpScore = ($TargetNo != '' && $TargetNo != 0 ? 'T# ' . $TargetNo : '');
		}
		else
			$tmpScore = (!empty($FirstName) ? $FinScore : '');
//OLD	$tmpScore=($OppTie==2 || ($FinScore==0 && $OppScore==0)) ? ($TargetNo != '' && $TargetNo != 0 ? 'T# ' . $TargetNo : '') : (!empty($FirstName) ? $FinScore : '');
		//Gestisco cosa scrivere nel tie
		if(strlen(trim($FinTieBreak)) > 0)
		{
			$tmpArr="";
			for($countArr=0; $countArr<strlen(trim($FinTieBreak)); $countArr++)
				$tmpArr .= DecodeFromLetter(substr(trim($FinTieBreak),$countArr,1)) . ",";
			$tmpScore .= " T." . substr($tmpArr,0,-1);
		}
		else if($FinTie==1)
			$tmpScore .= " *";
		//Punti dei Set, se presenti
		if(!empty($FinSetPoints))
		{

			$tmpSetPoint = "";
			foreach(explode("|",$FinSetPoints) as $spValue)
			{
				if($spValue!=0)
					$tmpSetPoint .= $spValue.",";
			}

			if(strlen($tmpSetPoint)>0)
				$tmpScore .= ' (' . substr($tmpSetPoint,0,-1) . ')';
		}
		$this->Cell($this->CellHSp-1,$this->CellVSp,$tmpScore,0,0,'L');
		$this->setX($this->getX()+1);


		$this->Line(90+$PhaseCounter*$this->CellHSp,$this->lastY,90+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY);
		if($FinMatchNo % 2 == 0)
		{
			$this->Line(90+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY,90+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY+($PhaseCounter==1 ? 3: ($PhaseCounter==2 ? 6: ($PhaseCounter==3 ? 12: 24))) * $this->CellVSp);
			if($ScheduledDate != '' && $ScheduledTime != '')
			{
				$this->SetFont('','',6);
				$this->SetXY(90+$PhaseCounter*$this->CellHSp,$this->getY()+($PhaseCounter==1 ? 3: ($PhaseCounter==2 ? 6: ($PhaseCounter==3 ? 12: 24))) * $this->CellVSp/2 - 0.6 * $this->CellVSp);
				$this->Cell($this->CellHSp,0.6*$this->CellVSp, (($FinTie==0 && $OppTie==0 && $FinScore==0 && $OppScore==0) ? $ScheduledDate : ''), 0, 0, 'R', 0);
				$this->SetXY(90+$PhaseCounter*$this->CellHSp,$this->getY()+0.6*$this->CellVSp);
				$this->Cell($this->CellHSp,0.6*$this->CellVSp, (($FinTie==0 && $OppTie==0 && $FinScore==0 && $OppScore==0) ? $ScheduledTime : ''), 0, 0, 'R', 0);
			}
		}

		$this->lastY += ($PhaseCounter==1 ? 2: ($PhaseCounter==2 ? 5: ($PhaseCounter==3 ? 11: 23))) * $this->CellVSp;
	}

	function PrintMedals($PhaseCounter, $GoldFirstName, $GoldName, $GoldCountry, $SilverFirstName, $SilverName, $SilverCountry, $BronzeFirstName, $BronzeName, $BronzeCountry)
	{
		$this->SetLineWidth(0.1);
		$this->SetXY(95+$PhaseCounter*$this->CellHSp,$this->lastY);
		$this->SetFont('','B');
		$this->Cell($this->CellHSp-5,$this->CellVSp,"GOLD",0,1,'L');
		$this->SetXY(90+$PhaseCounter*$this->CellHSp,$this->GetY());
		$this->SetFont('','');
		if(!is_null($GoldFirstName) && strlen($GoldFirstName)>0)
			$this->Cell($this->CellHSp,$this->CellVSp,$GoldFirstName . ' ' . $this->FirstLetters($GoldName). " (" . $GoldCountry . ")" ,0,0,'L');
		$this->Line(90+$PhaseCounter*$this->CellHSp,$this->lastY+2*$this->CellVSp,90+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY+2*$this->CellVSp);

		$this->lastY += ($PhaseCounter==2 ? 3: 6) * $this->CellVSp;
		$this->SetXY(95+$PhaseCounter*$this->CellHSp,$this->lastY);
		$this->SetFont('','B');
		$this->Cell($this->CellHSp-5,$this->CellVSp,"SILVER",0,1,'L');
		$this->SetXY(90+$PhaseCounter*$this->CellHSp,$this->GetY());
		$this->SetFont('','');
		if(!is_null($SilverFirstName) && strlen($SilverFirstName)>0)
			$this->Cell($this->CellHSp,$this->CellVSp,$SilverFirstName . ' ' . $this->FirstLetters($SilverName). " (" . $SilverCountry . ")" ,0,0,'L');
		$this->Line(90+$PhaseCounter*$this->CellHSp,$this->lastY+2*$this->CellVSp,90+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY+2*$this->CellVSp);

		$this->lastY += ($PhaseCounter==2 ? 3: 6) * $this->CellVSp;
		$this->SetXY(95+$PhaseCounter*$this->CellHSp,$this->lastY);
		$this->SetFont('','B');
		$this->Cell($this->CellHSp-5,$this->CellVSp,"BRONZE",0,1,'L');
		$this->SetXY(90+$PhaseCounter*$this->CellHSp,$this->GetY());
		$this->SetFont('','');
		if(!is_null($BronzeFirstName) && strlen($BronzeFirstName)>0)
			$this->Cell($this->CellHSp,$this->CellVSp,$BronzeFirstName . ' ' . $this->FirstLetters($BronzeName). " (" . $BronzeCountry . ")" ,0,0,'L');
		$this->Line(90+$PhaseCounter*$this->CellHSp,$this->lastY+2*$this->CellVSp,90+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY+2*$this->CellVSp);

	}

	function FirstColumnTeam($FinMatchNo, $Country, $CountryName, $Rank, $QuScore, $Position, $FinScore, $FinTie, $FinTieBreak, $FinSetPoints, $OppScore, $OppTie, $TargetNo, $ScheduledDate, $ScheduledTime, $TargetType='', $Components, $NumComponents)
	{
		$this->SetLineWidth(0.1);
		$this->SetFont('','');
		$this->SetXY(OrisPDF::leftMargin,$this->lastY);

		$this->Cell($this->DataSize[0],1.5*$this->CellVSp,$OppTie==2 || is_null($Country) ? '' : $Rank . '/',0,0,'R');
		$this->Cell($this->DataSize[1],1.5*$this->CellVSp,$QuScore,0,0,'R');
		$this->SetFont('','B');
		if($OppTie==2)
			$this->Cell($this->DataSize[2]+$this->DataSize[3],1.5*$this->CellVSp,'-Bye-',0,0,'L');
		else if(is_null($Country))
			$this->Cell($this->DataSize[2]+$this->DataSize[3],1.5*$this->CellVSp,'',0,0,'L');
		else
		{
			$this->Cell($this->DataSize[2],1.5*$this->CellVSp, mb_convert_case($Country, MB_CASE_UPPER, "UTF-8"),0,0,'L');
			$this->Cell($this->DataSize[3],1.5*$this->CellVSp, $CountryName,0,0,'L');
		}
		$this->SetFont('','');

		//Punteggio
		$tmpScore=($OppTie==2 || ($FinScore==0 && $OppScore==0)) ? ($TargetNo != '' && $TargetNo != 0 && $OppTie!=2 && $FinTie!=2  ? 'T# ' . $TargetNo : '') : $FinScore;

		//Gestisco cosa scrivere nel tie
		if(strlen(trim($FinTieBreak)) > 0)
		{
			/*
			$tmpArr="";
			$tmpArr="";
			for($countArr=0; $countArr<strlen(trim($FinTieBreak)); $countArr = $countArr+$NumComponents)
			{
				$tmpArr .= ValutaArrowString(substr(trim($FinTieBreak),$countArr,$NumComponents));
				if(strpos(DecodeFromLetter(substr(trim($FinTieBreak),$countArr+$NumComponents-1,1)),"*")!==false)
					$tmpArr .=  "*";
				 $tmpArr .= ",";
			}*/

			$tmpScore.=" T." . $FinTieBreak;
		}
		else if($FinTie==1)
			$tmpScore.=" *";

		// setpoints
		if(!empty($FinSetPoints))
		{
			$tmpSetPoint = "";
			foreach(explode("|",$FinSetPoints) as $spValue)
			{
				if($spValue!=0)
					$tmpSetPoint .= $spValue.",";
			}

			if(strlen($tmpSetPoint)>0)
				$tmpScore .= ' (' . substr($tmpSetPoint,0,-1) . ')';
		}

		$TmpX=$this->getX()+20;
		$this->Cell($this->CellHSp-1, 1.5*$this->CellVSp, $tmpScore, 0, 0, 'L', 0);		//No Tie

		//Scrivo i componenti
		for($i=0; $i<count($Components); $i++)
		{
			$this->SetFont('','','6');
			$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1],$this->lastY+(1.5+$i)*$this->CellVSp);
			$this->Cell($this->DataSize[2],$this->CellVSp, $Components[$i][0],0,0,'R');
			$this->Cell(3,$this->CellVSp, '',0,0,'R');
			$this->Cell($this->DataSize[3]-3,$this->CellVSp, $Components[$i][1],0,0,'L');
			$this->SetFont('','','8');
		}

		if($FinMatchNo % 2 == 0)
		{
			$this->Line($TmpX-20+$this->DataSize[4],$this->lastY,$TmpX-20,$this->lastY);
			$this->Line($TmpX-20+$this->DataSize[4],$this->lastY+(3+2*count($Components))*$this->CellVSp,$TmpX-20,$this->lastY+(3+2*count($Components))*$this->CellVSp);
			$this->Line($TmpX+$this->CellHSp-20,$this->lastY,$TmpX+$this->CellHSp-20,$this->lastY+(3+2*count($Components))*$this->CellVSp);
			if($ScheduledDate != '' && $ScheduledTime != '')
			{
				$this->SetFont('','',6);
				$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3],$this->lastY+((3+2*count($Components))*$this->CellVSp)/2-$this->CellVSp);
				$this->Cell($this->CellHSp,$this->CellVSp, (($FinTie==0 && $OppTie==0 && $FinScore==0 && $OppScore==0) ? $ScheduledDate : ''), 0, 0, 'R', 0);
				$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3],$this->lastY+((3+2*count($Components))*$this->CellVSp)/2);
				$this->Cell($this->CellHSp,$this->CellVSp, (($FinTie==0 && $OppTie==0 && $FinScore==0 && $OppScore==0) ? $ScheduledTime : ''), 0, 0, 'R', 0);
				$this->SetFont('','','8');
			}
		}

		$this->lastY += ((count($Components)+1.5+($FinMatchNo % 2)) * $this->CellVSp);
	}

	function OtherColumnsTeam($PhaseCounter, $FinMatchNo, $CountryName, $FinScore, $FinTie, $FinTieBreak, $FinSetPoints, $OppScore, $OppTie, $TargetNo, $ScheduledDate, $ScheduledTime, $TargetType='', $NumComponenti, $NumComponents)
	{

		$this->SetLineWidth(0.1);
		$this->SetFont('','');
		$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY);
		if($OppTie==2)
			$this->Cell($this->CellHSp,1.5*$this->CellVSp,'-Bye-',0,0,'L');
		else if(is_null($CountryName))
			$this->Cell($this->CellHSp,1.5*$this->CellVSp,'',0,0,'L');
		else
			$this->Cell($this->CellHSp,1.5*$this->CellVSp,$CountryName,0,0,'L');

		$this->lastY += 1.5*$this->CellVSp;
		$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY);
		//Punteggio
		$tmpScore=($OppTie==2 || ($FinScore==0 && $OppScore==0)) ? ($TargetNo != '' && $TargetNo != 0 && $OppTie!=2 && $FinTie!=2 ? 'T# ' . $TargetNo : '') : $FinScore;

		//Gestisco cosa scrivere nel tie
		if(strlen(trim($FinTieBreak)) > 0)
		{
			/*
			$tmpArr="";
			for($countArr=0; $countArr<strlen(trim($FinTieBreak)); $countArr = $countArr+$NumComponents)
			{
				$tmpArr .= ValutaArrowString(substr(trim($FinTieBreak),$countArr,$NumComponents));
				if(strpos(DecodeFromLetter(substr(trim($FinTieBreak),$countArr+$NumComponents-1,1)),"*")!==false)
					$tmpArr .=  "*";
				$tmpArr .= ",";
			}*/
			$tmpScore.=" T." . $FinTieBreak;
		}
		else if($FinTie==1)
			$tmpScore.=" *";		//*

		// setpoints
		if(!empty($FinSetPoints))
		{
			$tmpSetPoint = "";
			foreach(explode("|",$FinSetPoints) as $spValue)
			{
				if($spValue!=0)
					$tmpSetPoint .= $spValue.",";
			}

			if(strlen($tmpSetPoint)>0)
				$tmpScore .= ' (' . substr($tmpSetPoint,0,-1) . ')';
		}
		$this->Cell($this->CellHSp-1, 1.5*$this->CellVSp, $tmpScore, 0, 0, 'L', 0);		//No Tie

		$this->Line(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY,OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY);

		if($FinMatchNo % 2 == 0)
		{
			if($FinMatchNo!=2)
			{
				$this->Line(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY,OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY+($PhaseCounter==1 ? (4+2*$NumComponenti): ($PhaseCounter==2 ? (8+4*$NumComponenti): (16+8*$NumComponenti))) * $this->CellVSp);
				if($ScheduledDate != '' && $ScheduledTime != '')
				{
					$this->SetFont('','',6);
					$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY+(($PhaseCounter==1 ? (4+2*$NumComponenti): ($PhaseCounter==2 ? (8+4*$NumComponenti): (16+8*$NumComponenti))) * $this->CellVSp)/2-$this->CellVSp);
					$this->Cell($this->CellHSp,$this->CellVSp, (($FinTie==0 && $OppTie==0 && $FinScore==0 && $OppScore==0) ? $ScheduledDate : ''), 0, 0, 'R', 0);
					$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY+(($PhaseCounter==1 ? (4+2*$NumComponenti): ($PhaseCounter==2 ? (8+4*$NumComponenti): (16+8*$NumComponenti))) * $this->CellVSp)/2);
					$this->Cell($this->CellHSp,$this->CellVSp, (($FinTie==0 && $OppTie==0 && $FinScore==0 && $OppScore==0) ? $ScheduledTime : ''), 0, 0, 'R', 0);
					$this->SetFont('','',8);
				}
			}
			else
			{
				$this->Line(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY,OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY+($PhaseCounter==3 ? 4.5 : (3.5+$NumComponenti)) *$this->CellVSp);
				if($ScheduledDate != '' && $ScheduledTime != '')
				{
					$this->SetFont('','',6);
					$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY+(($PhaseCounter==3 ? 4.5 : (3.5+$NumComponenti)) *$this->CellVSp)/2-$this->CellVSp);
					$this->Cell($this->CellHSp,$this->CellVSp, (($FinTie==0 && $OppTie==0 && $FinScore==0 && $OppScore==0) ? $ScheduledDate : ''), 0, 0, 'R', 0);
					$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY+(($PhaseCounter==3 ? 4.5 : (3.5+$NumComponenti)) *$this->CellVSp)/2);
					$this->Cell($this->CellHSp,$this->CellVSp, (($FinTie==0 && $OppTie==0 && $FinScore==0 && $OppScore==0) ? $ScheduledTime : ''), 0, 0, 'R', 0);
					$this->SetFont('','',8);
				}
			}
		}

		//Se sono nelle finali mi salvo le posiioni - mi serve per le medaglie
		if($FinMatchNo<4)
			$this->TeamMatchNoPosition[$FinMatchNo] = $this->lastY;

		//Calcolo la Posizione del Next Matchno
		if($FinMatchNo==1)
		{
			$this->lastY += ($PhaseCounter==3 ? (5.25+2*$NumComponenti) : (4+2*$NumComponenti)) *$this->CellVSp;
		}
		else if($FinMatchNo==2)
		{
			$this->lastY += ($PhaseCounter==3 ? 3 : (2+$NumComponenti)) *$this->CellVSp;
		}
		else
			$this->lastY += ($PhaseCounter==1 ? (2.5+2*$NumComponenti): ($PhaseCounter==2 ? (6.5+4*$NumComponenti): (14.5+8*$NumComponenti))) * $this->CellVSp;

	}

	function PrintMedalsTeam($PhaseCounter, $GoldCountry, $SilverCountry, $BronzeCountry, $NumComponenti)
	{
		$this->lastY = $this->TeamMatchNoPosition[0] + ($this->TeamMatchNoPosition[1]-$this->TeamMatchNoPosition[0]) /2;

		$this->SetLineWidth(0.1);
		$this->Line(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY,OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY);
		$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY-2.5*$this->CellVSp);
		$this->SetFont('','B');
		$this->Cell($this->CellHSp-5,$this->CellVSp,"GOLD",0,0,'L');
		$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY-1.5*$this->CellVSp);
		$this->SetFont('','');
		if(!is_null($GoldCountry))
			$this->Cell($this->CellHSp,1.5*$this->CellVSp,$GoldCountry,0,0,'L');


		$this->lastY = $this->TeamMatchNoPosition[1] + (1.5+($PhaseCounter>2 ? 0.5:0)+$NumComponenti)*$this->CellVSp;

		$this->Line(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY,OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY);
		$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY-2.5*$this->CellVSp);
		$this->SetFont('','B');
		$this->Cell($this->CellHSp-5,$this->CellVSp,"SILVER",0,0,'L');
		$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY-1.5*$this->CellVSp);
		$this->SetFont('','');
		if(!is_null($GoldCountry))
			$this->Cell($this->CellHSp,1.5*$this->CellVSp,$SilverCountry,0,0,'L');

		$this->lastY = $this->TeamMatchNoPosition[2] + ($this->TeamMatchNoPosition[3]-$this->TeamMatchNoPosition[2]) /2;

		$this->Line(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY,OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp+$this->CellHSp,$this->lastY);
		$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY-2.5*$this->CellVSp);
		$this->SetFont('','B');
		$this->Cell($this->CellHSp-5,$this->CellVSp,"BRONZE",0,0,'L');
		$this->SetXY(OrisPDF::leftMargin+$this->DataSize[0]+$this->DataSize[1]+$this->DataSize[2]+$this->DataSize[3]+$PhaseCounter*$this->CellHSp,$this->lastY-1.5*$this->CellVSp);
		$this->SetFont('','');
		if(!is_null($GoldCountry))
			$this->Cell($this->CellHSp,1.5*$this->CellVSp,$BronzeCountry,0,0,'L');

	}

}

?>