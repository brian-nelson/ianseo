<?php
	require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
	require_once('Final/Fun_Final.local.inc.php');
	require_once('HHT/serial.php');
	require_once('HHT/Fun_HHT.local.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_Phases.inc.php');
	
	$event = isset($_REQUEST['event']) ? $_REQUEST['event'] : null;
	$team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null;
	$match = isset($_REQUEST['match']) ? $_REQUEST['match'] : null;
	
	$line = isset($_REQUEST['line']) ? $_REQUEST['line'] : null;
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;
	
	$contrast = isset($_REQUEST['contrast']) ? $_REQUEST['contrast'] : 9;
	
	$Errore = 0;
	
	if(is_null($event) || is_null($team) || is_null($match) || is_null($line) || is_null($type))
		$Errore = 1;
	if($Errore==0)
	{
		
		$rs=GetFinMatches($event,null,$match,$team,false);
		$myRow=safe_fetch($rs);
		$obj=getEventArrowsParams($myRow->event,$myRow->phase,$myRow->teamEvent);
		
		/*
		echo "<pre>";
		print_r($myRow);
		print_r($obj);
		echo "</pre>";
		*/
		
		$lArr = "";
		$rArr = "";
		$lSum = ""; 
		$rSum = "";
		$lTot = ""; 
		$rTot = "";
		
		$dLeft = "";
		$dCenter = "";
		$dRight = "";
		
		if($myRow->matchMode==1)	//Gara a SET
		{
		
			$setPointL = explode("|",$myRow->setPoints1);
			$setPointR = explode("|",$myRow->setPoints2);
			$setRunning=0;

			//Decido quale set Pubblicare
			for($i=0; $i<max(count($setPointL),count($setPointR)); $i++ )
			{
				if($setPointL[$i]!=0 || $setPointR[$i]!=0)
					$setRunning=$i;
			}
			if(strlen(trim($myRow->tiebreak1)) > 0 || strlen(trim($myRow->tiebreak2)) > 0)
				$setRunning = (-1) * max(strlen(trim($myRow->tiebreak1)),strlen(trim($myRow->tiebreak2)));
				
			if($setRunning>=0)		//Gara
			{
				$tlArr=substr($myRow->arrowString1, $setRunning*$obj->arrows, $setRunning*$obj->arrows+$obj->arrows);
				$trArr=substr($myRow->arrowString2, $setRunning*$obj->arrows, $setRunning*$obj->arrows+$obj->arrows);
				for($j=0; $j<$obj->arrows; $j++)
				{
					if(substr($tlArr, $j, 1)<="a")
						$lArr .= (DecodeFromLetter(substr($tlArr, $j, 1))!="10" ? DecodeFromLetter(substr($tlArr, $j, 1)):chr(158));
					else
						$lArr .= chr(160+ValutaArrowString(substr($tlArr, $j, 1)));
						
					if(substr($trArr, $j, 1)<="a")
						$rArr .= (DecodeFromLetter(substr($trArr, $j, 1))!="10" ? DecodeFromLetter(substr($trArr, $j, 1)):chr(158));
					else
						$rArr .= chr(160+ValutaArrowString(substr($trArr, $j, 1)));
						
				}
				$lSum = $setPointL[$setRunning];
				$rSum = $setPointR[$setRunning];
			}
			else		//ShootOff
			{
				$tlArr=str_pad(substr($myRow->tiebreak1, 0, $obj->so),3," ");
				$trArr=str_pad(substr($myRow->tiebreak2, 0, $obj->so),3," ");
				
				for($j=0; $j<$obj->so; $j++)
				{
					if(substr($tlArr, $j, 1)<="a")
						$lArr .= (DecodeFromLetter(substr($tlArr, $j, 1))!="10" ? DecodeFromLetter(substr($tlArr, $j, 1)):chr(158));
					else
						$lArr .= (DecodeFromLetter(substr($tlArr, $j, 1))!="10*" ? chr(160+ValutaArrowString(substr($tlArr, $j, 1))):chr(158));
						
					if(substr($trArr, $j, 1)<="a")
						$rArr .= (DecodeFromLetter(substr($trArr, $j, 1))!="10" ? DecodeFromLetter(substr($trArr, $j, 1)):chr(158));
					else
						$rArr .= (DecodeFromLetter(substr($trArr, $j, 1))!="10*" ? chr(160+ValutaArrowString(substr($trArr, $j, 1))):chr(158));
						
				}
				$lSum = 'TB';
				$rSum = 'TB';
			}
			$lTot = $myRow->setScore1;
			$rTot = $myRow->setScore2;
				
			$dLeft = str_pad(substr($lArr,-3,3),4," ",STR_PAD_RIGHT);
			$dRight = str_pad(substr($rArr,-3,3),4," ",STR_PAD_RIGHT);
			
			$dLeft = $lSum;
			$dRight = $rSum;
				
			
			$dCenter = trim($lTot) . "-" . trim($rTot);
		}
		else 
		{
			if(strlen(trim($myRow->tiebreak1))==0 && strlen(trim($myRow->tiebreak2))==0)		//Gara
			{
				$dLeft = $myRow->score1;
				$dRight = $myRow->score2;
				$lTot = $dLeft;
				$rTot = $dRight;

				$endRunning=intval((max(strlen(trim($myRow->arrowString1)),strlen(trim($myRow->arrowString2)))-1)/($obj->arrows/($myRow->teamEvent? 2:1)));
//				echo $endRunning . "<br>";
				$tlArr=substr($myRow->arrowString1, $endRunning*($obj->arrows/($myRow->teamEvent? 2:1)), $endRunning*($obj->arrows/($myRow->teamEvent? 2:1))+($obj->arrows/($myRow->teamEvent? 2:1)));
				$trArr=substr($myRow->arrowString2, $endRunning*($obj->arrows/($myRow->teamEvent? 2:1)), $endRunning*($obj->arrows/($myRow->teamEvent? 2:1))+($obj->arrows/($myRow->teamEvent? 2:1)));
//				echo $tlArr.".".$trArr. "<br>";
				
				$endRunning=intval((max(strlen(trim($myRow->arrowString1)),strlen(trim($myRow->arrowString2)))-1)/$obj->arrows);
				$lSum = ValutaArrowString(substr($myRow->arrowString1, $endRunning*$obj->arrows, $endRunning*$obj->arrows+$obj->arrows));
				$rSum = ValutaArrowString(substr($myRow->arrowString2, $endRunning*$obj->arrows, $endRunning*$obj->arrows+$obj->arrows));
				
				for($j=0; $j<($obj->arrows/($myRow->teamEvent? 2:1)); $j++)
				{
					if(substr($tlArr, $j, 1)<="a")
						$lArr .= (DecodeFromLetter(substr($tlArr, $j, 1))!="10" ? DecodeFromLetter(substr($tlArr, $j, 1)):chr(158));
					else
						$lArr .= chr(160+ValutaArrowString(substr($tlArr, $j, 1)));
					
					if(substr($trArr, $j, 1)<="a")
						$rArr .= (DecodeFromLetter(substr($trArr, $j, 1))!="10" ? DecodeFromLetter(substr($trArr, $j, 1)):chr(158));
					else
						$rArr .= chr(160+ValutaArrowString(substr($trArr, $j, 1)));
				}
			}
			else 			//ShootOff
			{
				$dLeft = ValutaArrowString(substr($myRow->tiebreak1, 0, $obj->so));
				$dRight = ValutaArrowString(substr($myRow->tiebreak2, 0, $obj->so));
				$lTot = $dLeft;
				$rTot = $dRight;
				
				$tlArr=str_pad(substr($myRow->tiebreak1, 0, $obj->so),3," ");
				$trArr=str_pad(substr($myRow->tiebreak2, 0, $obj->so),3," ");
				for($j=0; $j<$obj->so; $j++)
				{
					if(substr($tlArr, $j, 1)<="a")
						$lArr .= (DecodeFromLetter(substr($tlArr, $j, 1))!="10" ? DecodeFromLetter(substr($tlArr, $j, 1)):chr(158));
					else
						$lArr .= (DecodeFromLetter(substr($tlArr, $j, 1))!="10*" ? chr(160+ValutaArrowString(substr($tlArr, $j, 1))):chr(158));
				
					if(substr($trArr, $j, 1)<="a")
						$rArr .= (DecodeFromLetter(substr($trArr, $j, 1))!="10" ? DecodeFromLetter(substr($trArr, $j, 1)):chr(158));
					else
						$rArr .= (DecodeFromLetter(substr($trArr, $j, 1))!="10*" ? chr(160+ValutaArrowString(substr($trArr, $j, 1))):chr(158));
				
				}
				
				$lSum = 'TB';
				$rSum = 'TB';
			}
			$dCenter = "";
		}

		/*
		echo "Left Arrows:" . $lArr . "<br>";
		echo "Right Arrows:" . $rArr . "<br>";
		echo "Left Sum:" . $lSum . "<br>";
		echo "Right Sum:" . $rSum . "<br>";
		echo "Left Total:" . $lTot . "<br>";
		echo "Right Total:" . $rTot . "<br><br>";
		echo "Left Display:" . $dLeft . "<br>";
		echo "Center Display:" . $dCenter . "<br>";
		echo "Right Display:" . $dRight . "<br><br>";
		echo "Display Type:" . $type . "<br>";
		*/
		
		if($type==2)
			SendHTT(HhtParam($line),PrepareDisplayFrame($dLeft,$dRight," "," ", $dCenter, $contrast));
		elseif($type==1)
			SendHTT(HhtParam($line),PrepareFinalDisplayFrame($lArr,$rArr,$lSum,$rSum,$lTot,$rTot));
	}
		

	//header('Content-Type: text/xml');

	print '<response>' . "\n";
		print '<error>' . $Errore . '</error>' . "\n";
	print '</response>' . "\n";


?>