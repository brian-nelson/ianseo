<?php
include_once("config.inc.php");

function PrepareTxFrame($devices, $sentence)
{
	$FrameTX = array();
	$deviceList = array();
	if(is_array($sentence))
		return array();
	if(!is_array($devices))
		$deviceList[] = $devices;
	else
		$deviceList=$devices;

	foreach($deviceList as $value)
	{
		if($value>= 0 && $value <= 221)
		{
			$tmp  = STX . chr(strlen($sentence)+5) . ENQ . chr(AllModule+$value) . ModuleName . $sentence;
			$tmp .= CalculateChecksum($tmp) . EOT;
			$FrameTX[] = $tmp;
		}
	}
	return $FrameTX;
}

function SendHTT($HhtParams, $sentences, $sendResultArray = false, $overTO = 0)
{
	$request = array();
	$answers = array();
	if(!is_array($sentences))
		$request[] = $sentences;
	else
		$request = $sentences;
	if(count($request) > 0)
	{
		$fp = @fsockopen ($HhtParams[0], $HhtParams[1], $errno, $errstr, 1);
		if ($fp)
		{
			$SecondTry=false;
			stream_set_blocking($fp,0);
			for($i=0; $i<count($request); $i++)
			{
				$tmp = "";
				$EotCounter=0;
				$TimerTo=getmicrotime();

    			fputs ($fp, $request[$i]);

				while (!feof($fp))  {

					$char = fgetc($fp);
					if($char != null)
						$TimerTo=getmicrotime();

					$tmp .= $char;
   					if($char == EOT)
   					{
						$EotCounter++;
   						if(ord(substr($request[$i],3,1))==AllModule)
   							break;
   						if($EotCounter >=2 )
   							break;
   					}
   					if((getmicrotime()-$TimerTo) >0.5+$overTO)
   					{
//  						echo "TIMEOUTTED";
   						if(!$SecondTry && strlen($request[$i])< strlen($tmp))
   						{
   							$SecondTry = true;
   							$i--;
   						}
   						break;
   					}
  				}
//			   	echo "BACK" . OutText($tmp);

			   	//Elimino completamente l'echo del cavo.'
				$tmp = str_replace($request[$i],"",$tmp);
//				echo "PULITO" . OutText($tmp);
//			exit();
/**
 *  Se avevo fatto qualunque altra richiesta E NON AVEVO FATTO BROADCAST mi aspetto indietro:
 *  1) la domanda (Eliminata precedentemente e quindi non più in $tmp
 *  2) i bytes della risposta di tipo "Collect" relativamente ai soli pettorali Abilitati (A,B,C,D) sono presenti se ci sono dentro i nomi)
 *
 *  Prima di procedere verifico che:
 *  a) La richiesta non sia un BROADCAST
 *  b) Ci sia STX, ACK & EOT nei posti "giusti"
 *  c) Il checksum quadri con il teorico
 */
				if(ord(substr($request[$i],3,1)) != AllModule
					&& strlen($tmp)==ord(substr($tmp,1,1))+2
					&& substr($tmp,0,1)  == STX
					&& substr($tmp,2,1)  == ACK
					&& substr($tmp,-1,1) == EOT)
				{
//Gestione della "Collect" e della "STORE"
/**
  * Se ho fatto una richiesta di "Collect" o di "store and Collect" prendo il valore che torna e creo un array così formato:
  * array[0]= Numero di piazzolea
  * array[1]= Valore delle frecce
  */
					if(strlen($request[$i])==7 || (strlen($request[$i])==12 && substr($request[$i],5,5)=="sTORE"))
					{
						$tmpTarget   = intval(substr($tmp,6,3));

						$tmpFirstArr = intval(substr($tmp,9,2));
						$tmpLastArr  = intval(substr($tmp,11,2));
						$tmpPhase    = substr($tmp,15,3);


						$curPos=18;		//Posizione dove iniziano ad esserci i risultati (Forse)
						//Nota Bene: $curPos + "2" + $tmpLastArr - $tmpFirstArr
						//il "2" perche è: 1 per la piazzola (A,B,C,D,) + 1 per il numero frecce ($tmpLastArr - $tmpFirstArr + 1)
						while((strlen($tmp)-2)>= ($curPos + 2 + $tmpLastArr - $tmpFirstArr))
						{
							//echo substr($tmp,$curPos,2 + $tmpLastArr - $tmpFirstArr);
							$tmpAnswers  = array("TargetNo"=>0, "ArrowString"=>0, "Dist"=>0, "FirstArr"=>0, "LastArr"=>0, "Session"=>0, "FlagWhat"=>"-1");
							$tmpAnswers["TargetNo"] = $tmpTarget . substr($tmp,$curPos,1);
							$tmpAnswers["ArrowString"] = substr($tmp,$curPos+1,1+$tmpLastArr-$tmpFirstArr);
							$tmpAnswers["FirstArr"] = $tmpFirstArr;
							$tmpAnswers["LastArr"] = $tmpLastArr;
							$tmpAnswers["RawPhase"] = $tmpPhase;
							list($tmpAnswers["FlagWhat"],$tmpAnswers["Session"],$tmpAnswers["Dist"]) = phaseDecode($tmpPhase);

							//Salvo solo se esiste almeno un valore di freccia.
							if(strlen(str_replace("-","",$tmpAnswers["ArrowString"]))!=0)
								$answers[] = $tmpAnswers;

							$curPos += 2 + $tmpLastArr - $tmpFirstArr;
						}

					}
//It's a firmware Update
					else if(substr($request[$i],5,1)== ':')
					{
						if(substr($request[$i],12,2)=='01')
							echo "End Of File: " . substr($tmp,5,-2) . "\n";
						else
							echo substr($request[$i],8,4). ": " . substr($tmp,5,-2) . "\n";
						flush();
					}
					else
					{
//Preparo il numero di paglione
						$TmpPaglione = intval(substr($tmp,6,3)); 
//Gestisco l'init dell'HTT - Individual Mode
						if($TmpPaglione!=0 && strpos($request[$i],chr(211))!== false)
							$answers[] = $TmpPaglione;
//Mette A-B-C-D sull'init dei nomi della piazzola. Verifica quali dei Valori sono stati spediti
						if($TmpPaglione!=0 && strpos($request[$i],chr(217))!== false)
							$answers[] = $TmpPaglione . "A";
						if($TmpPaglione!=0 && strpos($request[$i],chr(218))!== false)
							$answers[] = $TmpPaglione . "B";
						if($TmpPaglione!=0 && strpos($request[$i],chr(219))!== false)
							$answers[] = $TmpPaglione . "C";
						if($TmpPaglione!=0 && strpos($request[$i],chr(220))!== false)
							$answers[] = $TmpPaglione . "D";
//Mette A-B-C-D sull'init dei totali della piazzola. Verifica quali dei Valori sono stati spediti
						if($TmpPaglione!=0 && strpos($request[$i],chr(216))!== false)
						{
							if(strpos(substr($request[$i],5,-2),"A")!== false)
								$answers[] = $TmpPaglione . "A";
							if(strpos(substr($request[$i],5,-2),"B")!== false)
								$answers[] = $TmpPaglione . "B";
							if(strpos(substr($request[$i],5,-2),"C")!== false)
								$answers[] = $TmpPaglione . "C";
							if(strpos(substr($request[$i],5,-2),"D")!== false)
								$answers[] = $TmpPaglione . "D";
						}
//Mette il flag ad "A" se ho mandato il "Game Info"
						if($TmpPaglione!=0 && strpos($request[$i],chr(221))!== false)
							$answers[] = $TmpPaglione . "A";
//Mette il flag ad "C" se ho mandato il "Commercial"
						if($TmpPaglione!=0 && strpos($request[$i],chr(222))!== false)
							$answers[] = $TmpPaglione . "B";
//Mette il flag ad "D" se ho mandato la "Sequence"
						if($TmpPaglione!=0 && strpos($request[$i],chr(223))!== false)
							$answers[] = $TmpPaglione . "D";
//Verifico se Chiedo la release Software
						if(substr($tmp,5,3) == "SW-")
						{
							$tmpAnswer = array("Target"=>0, "Terminal" => '');
							$tmpAnswer["Target"] = ord(substr($tmp,3,1))-32;
							$tmpAnswer["Terminal"] = substr($tmp,5,-2);
							$answers[] = $tmpAnswer;
						}
//Verifico se Chiedo i Dati di AB o di CE
						if(substr($request[$i],5,4) == "READ")
						{
							$curPos = strpos(substr($tmp,0,-2),"@",5);
							while($curPos !== false)
							{
								$tmpAnswer = array("Target"=>0, "Name" => '', "Country" => '', "TotaleScore" => 0, "Distance" => 0, "Volee" => 0, "Arrows" => '');
								$tmpAnswer["Target"] = ord(substr($tmp,3,1))-32;
								$tmpAnswer["Name"] = substr($tmp,$curPos+1,13);
								$tmpAnswer["Country"] = substr($tmp,$curPos+14,3);
								$tmpAnswer["TotaleScore"] = substr($tmp,$curPos+17,4);
								$tmpAnswer["Distance"] = substr($tmp,$curPos+21,3);
								$tmpAnswer["Volee"] = substr($tmp,$curPos+24,2);
								$tmpAnswer["Arrows"] = substr($tmp,$curPos+26,6);
								$answers[] = $tmpAnswer;
								$curPos = strpos(substr($tmp,0,-2),"@",$curPos+1);
							}
						}
					}
				}
				usleep(5000 + $overTO);
			}
    		fclose ($fp);
		}
		else
		{
			return NULL;
		}
	}
	return $answers;
}


function PrepareDisplayFrame($lText='',  $rText='', $lLight=' ', $rLight=' ', $abcd='    ', $contrast=9)
{
	if($contrast < 0 || $contrast > 9)
	{
		$contrast =  9;
	}

	$tmpTx = STX . '%' . ENQ . 'G$' .
		substr(str_pad($lLight,1),0,1) . ' ' .
		substr(str_pad($lText,5,' ',STR_PAD_LEFT),0,5) . ' ' .
		substr(str_pad($rLight,1),0,1) . ' ' .
		substr(str_pad($rText,5,' ',STR_PAD_LEFT),0,5) . ' ' .
		'23' . NUL . '::' . $contrast . '9 ' . substr(str_pad($abcd,4,' ',STR_PAD_LEFT),0,4) .' ' . '01' . NUL;
	$tmpTx .= CalculateDisplayChecksum($tmpTx) . EOT;
	return $tmpTx;
}

function PrepareFinalDisplayFrame($Larr,$Rarr,$Lsum,$Rsum,$Ltot,$Rtot)
{
	$totalString='';
	$tmpTx = ENQ . chr(1) . chr(16) . substr($Larr. '   ',0,3) . substr('  ' . $Lsum,-2) . substr('   ' . $Ltot,-3);
	$tmpTx = STX . chr(strlen($tmpTx)+2) . $tmpTx;
	$tmpTx .= CalculateDisplayChecksum($tmpTx) . EOT;
	$totalString  =  $tmpTx;
	$tmpTx = ENQ . chr(2) . chr(16) . substr($Rarr. '   ',0,3) . substr('  ' . $Rsum,-2) . substr('   ' . $Rtot,-3);
	$tmpTx = STX . chr(strlen($tmpTx)+2) . $tmpTx;
	$tmpTx .= CalculateDisplayChecksum($tmpTx) . EOT;
	$totalString .=  $tmpTx;
	return $totalString;
}

?>