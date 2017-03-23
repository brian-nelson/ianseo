<?php
/*
 * Il vettore serve come riferimento per avere la corrispondenza lettera-punto nelle arrowstring.
 * La struttura è:
 * chiave => [stampa,valore]
 *
 * con la 'chiave' la lettera che rappresenta il punto per tutti i target, 'stampa' la stringa che viene visualizzata
 * nelle viste e 'valore' il valore numerico della lettera usato per i conti.
 */
	$LetterPoint=array
	(
		"A" => array("P" => "M", "N" => "0", 'W' => 0),
		"B" => array("P" => "1", "N" => "1", 'W' => 110),
		"C" => array("P" => "2", "N" => "2", 'W' => 120),
		"D" => array("P" => "3", "N" => "3", 'W' => 130),
		"E" => array("P" => "4", "N" => "4", 'W' => 140),
		"F" => array("P" => "5", "N" => "5", 'W' => 150),
		"G" => array("P" => "6", "N" => "6", 'W' => 160),
		"H" => array("P" => "7", "N" => "7", 'W' => 170),
		"I" => array("P" => "8", "N" => "8", 'W' => 180),
		"J" => array("P" => "9", "N" => "9", 'W' => 190),
		"K" => array("P" => "X", "N" => "10", 'W' => 205),
		"L" => array("P" => "10", "N" => "10", 'W' => 200),
		"M" => array("P" => "11", "N" => "11", 'W' => 210),
		"N" => array("P" => "12", "N" => "12", 'W' => 220),
//		"O" => array("P" => "13", "N" => "13", 'W' => 230),
//		"P" => array("P" => "14", "N" => "14", 'W' => 240),
		"Q" => array("P" => "X", "N" => "15", 'W' => 250),
//		"R" => array("P" => "16", "N" => "16", 'W' => 260),
//		"S" => array("P" => "17", "N" => "17", 'W' => 270),
//		"T" => array("P" => "18", "N" => "18", 'W' => 280),
//		"U" => array("P" => "19", "N" => "19", 'W' => 290),
		"V" => array("P" => "20", "N" => "20", 'W' => 290),
//		"W" => array("P" => "21", "N" => "17", 'W' => 300),
//		"X" => array("P" => "22", "N" => "18", 'W' => 310),
//		"Y" => array("P" => "23", "N" => "19", 'W' => 320),
		"Z" => array("P" => "X", "N" => "5", 'W' => 160),
	);

/*
 * Di seguito sono definiti i vettori che rappresentano i bersagli.
 * Il nome della variabile deve terminare con 'Small' per indicare un bersaglio
 * non completo.
 */
// Bersaglio OutDoor
//	$TrgOutdoor = array
//	(
//	// Punti non dubbi
//		"A" => array("P" => "M", "N" => "0", "R" => "M"),
//		"B" => array("P" => "1", "N" => "1", "R" => "1"),
//		"C" => array("P" => "2", "N" => "2", "R" => "2"),
//		"D" => array("P" => "3", "N" => "3", "R" => "3"),
//		"E" => array("P" => "4", "N" => "4", "R" => "4"),
//		"F" => array("P" => "5", "N" => "5", "R" => "5"),
//		"G" => array("P" => "6", "N" => "6", "R" => "6"),
//		"H" => array("P" => "7", "N" => "7", "R" => "7"),
//		"I" => array("P" => "8", "N" => "8", "R" => "8"),
//		"J" => array("P" => "9", "N" => "9", "R" => "9"),
//		"K" => array("P" => "X", "N" => "10", "R" => "X"),
//		"L" => array("P" => "10", "N" => "10", "R" => "10"),
//	// Punti dubbi
//		"a" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"b" => array("P" => "1*", "N" => "1", "R" => "1*"),
//		"c" => array("P" => "2*", "N" => "2", "R" => "2*"),
//		"d" => array("P" => "3*", "N" => "3", "R" => "3*"),
//		"e" => array("P" => "4*", "N" => "4", "R" => "4*"),
//		"f" => array("P" => "5*", "N" => "5", "R" => "5*"),
//		"g" => array("P" => "6*", "N" => "6", "R" => "6*"),
//		"h" => array("P" => "7*", "N" => "7", "R" => "7*"),
//		"i" => array("P" => "8*", "N" => "8", "R" => "8*"),
//		"j" => array("P" => "9*", "N" => "9", "R" => "9*"),
//		"k" => array("P" => "X*", "N" => "10", "R" => "X*"),
//		"l" => array("P" => "10*", "N" => "10", "R" => "10*")
//	);

// Bersaglio Campagna
//	$TrgField = array
//	(
//		// Punti non dubbi
//		"A" => array("P" => "M", "N" => "0", "R" => "M"),
//		"B" => array("P" => "M", "N" => "0", "R" => "1"),
//		"C" => array("P" => "M", "N" => "0", "R" => "2"),
//		"D" => array("P" => "M", "N" => "0", "R" => "3"),
//		"E" => array("P" => "M", "N" => "0", "R" => "4"),
//		"F" => array("P" => "1", "N" => "1", "R" => "5"),
//		"G" => array("P" => "2", "N" => "2", "R" => "6"),
//		"H" => array("P" => "3", "N" => "3", "R" => "7"),
//		"I" => array("P" => "4", "N" => "4", "R" => "8"),
//		"J" => array("P" => "5", "N" => "5", "R" => "9"),
//		"K" => array("P" => "6", "N" => "6", "R" => "X"),
//		"L" => array("P" => "6", "N" => "6", "R" => "10"),
//	// Punti dubbi
//		"a" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"b" => array("P" => "M*", "N" => "0", "R" => "1*"),
//		"c" => array("P" => "M*", "N" => "0", "R" => "2*"),
//		"d" => array("P" => "M*", "N" => "0", "R" => "3*"),
//		"e" => array("P" => "M*", "N" => "0", "R" => "4*"),
//		"f" => array("P" => "1*", "N" => "1", "R" => "5*"),
//		"g" => array("P" => "2*", "N" => "2", "R" => "6*"),
//		"h" => array("P" => "3*", "N" => "3", "R" => "7*"),
//		"i" => array("P" => "4*", "N" => "4", "R" => "8*"),
//		"j" => array("P" => "5*", "N" => "5", "R" => "9*"),
//		"k" => array("P" => "6*", "N" => "6", "R" => "X*"),
//		"l" => array("P" => "6*", "N" => "6", "R" => "10*")
//	);

// OLIMPICO INDOOR Completo
//	$TrgIndComplete = array
//	(
//	// Punti non dubbi
//		"A" => array("P" => "M", "N" => "0", "R" => "M"),
//		"B" => array("P" => "1", "N" => "1", "R" => "1"),
//		"C" => array("P" => "2", "N" => "2", "R" => "2"),
//		"D" => array("P" => "3", "N" => "3", "R" => "3"),
//		"E" => array("P" => "4", "N" => "4", "R" => "4"),
//		"F" => array("P" => "5", "N" => "5", "R" => "5"),
//		"G" => array("P" => "6", "N" => "6", "R" => "6"),
//		"H" => array("P" => "7", "N" => "7", "R" => "7"),
//		"I" => array("P" => "8", "N" => "8", "R" => "8"),
//		"J" => array("P" => "9", "N" => "9", "R" => "9"),
//		"K" => array("P" => "10", "N" => "10", "R" => "X"),
//		"L" => array("P" => "10", "N" => "10", "R" => "10"),
//	// Punti dubbi
//		"a" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"b" => array("P" => "1*", "N" => "1", "R" => "1*"),
//		"c" => array("P" => "2*", "N" => "2", "R" => "2*"),
//		"d" => array("P" => "3*", "N" => "3", "R" => "3*"),
//		"e" => array("P" => "4*", "N" => "4", "R" => "4*"),
//		"f" => array("P" => "5*", "N" => "5", "R" => "5*"),
//		"g" => array("P" => "6*", "N" => "6", "R" => "6*"),
//		"h" => array("P" => "7*", "N" => "7", "R" => "7*"),
//		"i" => array("P" => "8*", "N" => "8", "R" => "8*"),
//		"j" => array("P" => "9*", "N" => "9", "R" => "9*"),
//		"k" => array("P" => "10*", "N" => "10", "R" => "X*"),
//		"l" => array("P" => "10*", "N" => "10", "R" => "10*")
//	);

// OLIMPICO INDOOR ridotto
//	$TrgIndSmall = array
//		(
//	// Punti non dubbi
//		"A" => array("P" => "M", "N" => "0", "R" => "M"),
//		"B" => array("P" => "M", "N" => "0", "R" => "1"),
//		"C" => array("P" => "M", "N" => "0", "R" => "2"),
//		"D" => array("P" => "M", "N" => "0", "R" => "3"),
//		"E" => array("P" => "M", "N" => "0", "R" => "4"),
//		"F" => array("P" => "M", "N" => "0", "R" => "5"),
//		"G" => array("P" => "6", "N" => "6", "R" => "6"),
//		"H" => array("P" => "7", "N" => "7", "R" => "7"),
//		"I" => array("P" => "8", "N" => "8", "R" => "8"),
//		"J" => array("P" => "9", "N" => "9", "R" => "9"),
//		"K" => array("P" => "10", "N" => "10", "R" => "X"),
//		"L" => array("P" => "10", "N" => "10", "R" => "10"),
//	// Punti dubbi
//		"a" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"b" => array("P" => "M*", "N" => "0", "R" => "1*"),
//		"c" => array("P" => "M*", "N" => "0", "R" => "2*"),
//		"d" => array("P" => "M*", "N" => "0", "R" => "3*"),
//		"e" => array("P" => "M*", "N" => "0", "R" => "4*"),
//		"f" => array("P" => "M*", "N" => "0", "R" => "5*"),
//		"g" => array("P" => "6*", "N" => "6", "R" => "6*"),
//		"h" => array("P" => "7*", "N" => "7", "R" => "7*"),
//		"i" => array("P" => "8*", "N" => "8", "R" => "8*"),
//		"j" => array("P" => "9*", "N" => "9", "R" => "9*"),
//		"k" => array("P" => "10*", "N" => "10", "R" => "X*"),
//		"l" => array("P" => "10*", "N" => "10", "R" => "10*")
//	);
// COMPOUND Indoor Completo
//	$TrgCOIndComplete = array
//	(
//	// Punti non dubbi
//		"A" => array("P" => "M", "N" => "0", "R" => "M"),
//		"B" => array("P" => "1", "N" => "1", "R" => "1"),
//		"C" => array("P" => "2", "N" => "2", "R" => "2"),
//		"D" => array("P" => "3", "N" => "3", "R" => "3"),
//		"E" => array("P" => "4", "N" => "4", "R" => "4"),
//		"F" => array("P" => "5", "N" => "5", "R" => "5"),
//		"G" => array("P" => "6", "N" => "6", "R" => "6"),
//		"H" => array("P" => "7", "N" => "7", "R" => "7"),
//		"I" => array("P" => "8", "N" => "8", "R" => "8"),
//		"J" => array("P" => "9", "N" => "9", "R" => "9"),
//		"K" => array("P" => "10", "N" => "10", "R" => "X"),
//		"L" => array("P" => "9", "N" => "9", "R" => "10"),
//	// Punti dubbi
//		"a" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"b" => array("P" => "1*", "N" => "1", "R" => "1*"),
//		"c" => array("P" => "2*", "N" => "2", "R" => "2*"),
//		"d" => array("P" => "3*", "N" => "3", "R" => "3*"),
//		"e" => array("P" => "4*", "N" => "4", "R" => "4*"),
//		"f" => array("P" => "5*", "N" => "5", "R" => "5*"),
//		"g" => array("P" => "6*", "N" => "6", "R" => "6*"),
//		"h" => array("P" => "7*", "N" => "7", "R" => "7*"),
//		"i" => array("P" => "8*", "N" => "8", "R" => "8*"),
//		"j" => array("P" => "9*", "N" => "9", "R" => "9*"),
//		"k" => array("P" => "10*", "N" => "10", "R" => "X*"),
//		"l" => array("P" => "9*", "N" => "9", "R" => "10*")
//	);

// Compound Indoor
//	$TrgCOIndSmall = array
//		(
//	// Punti non dubbi
//		"A" => array("P" => "M", "N" => "0", "R" => "M"),
//		"B" => array("P" => "M", "N" => "0", "R" => "1"),
//		"C" => array("P" => "M", "N" => "0", "R" => "2"),
//		"D" => array("P" => "M", "N" => "0", "R" => "3"),
//		"E" => array("P" => "M", "N" => "0", "R" => "4"),
//		"F" => array("P" => "M", "N" => "0", "R" => "5"),
//		"G" => array("P" => "6", "N" => "6", "R" => "6"),
//		"H" => array("P" => "7", "N" => "7", "R" => "7"),
//		"I" => array("P" => "8", "N" => "8", "R" => "8"),
//		"J" => array("P" => "9", "N" => "9", "R" => "9"),
//		"K" => array("P" => "10", "N" => "10", "R" => "X"),
//		"L" => array("P" => "9", "N" => "9", "R" => "10"),
//	// Punti dubbi
//		"a" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"b" => array("P" => "M*", "N" => "0", "R" => "1*"),
//		"c" => array("P" => "M*", "N" => "0", "R" => "2*"),
//		"d" => array("P" => "M*", "N" => "0", "R" => "3*"),
//		"e" => array("P" => "M*", "N" => "0", "R" => "4*"),
//		"f" => array("P" => "M*", "N" => "0", "R" => "5*"),
//		"g" => array("P" => "6*", "N" => "6", "R" => "6*"),
//		"h" => array("P" => "7*", "N" => "7", "R" => "7*"),
//		"i" => array("P" => "8*", "N" => "8", "R" => "8*"),
//		"j" => array("P" => "9*", "N" => "9", "R" => "9*"),
//		"k" => array("P" => "10*", "N" => "10", "R" => "X*"),
//		"l" => array("P" => "9*", "N" => "9", "R" => "10*")
//	);
// Hit/Miss Outdoor
//	$TrgHMOutComplete = array
//	(
//	// Punti non dubbi
//		"A" => array("P" => "M", "N" => "0", "R" => "M"),
//		"B" => array("P" => "M", "N" => "0", "R" => "1"),
//		"C" => array("P" => "M", "N" => "0", "R" => "2"),
//		"D" => array("P" => "M", "N" => "0", "R" => "3"),
//		"E" => array("P" => "M", "N" => "0", "R" => "4"),
//		"F" => array("P" => "M", "N" => "0", "R" => "5"),
//		"G" => array("P" => "M", "N" => "0", "R" => "6"),
//		"H" => array("P" => "M", "N" => "0", "R" => "7"),
//		"I" => array("P" => "M", "N" => "0", "R" => "8"),
//		"J" => array("P" => "1", "N" => "1", "R" => "9"),
//		"K" => array("P" => "1", "N" => "1", "R" => "X"),
//		"L" => array("P" => "1", "N" => "1", "R" => "10"),
//	// Punti dubbi
//		"a" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"b" => array("P" => "M*", "N" => "0", "R" => "1*"),
//		"c" => array("P" => "M*", "N" => "0", "R" => "2*"),
//		"d" => array("P" => "M*", "N" => "0", "R" => "3*"),
//		"e" => array("P" => "M*", "N" => "0", "R" => "4*"),
//		"f" => array("P" => "M*", "N" => "0", "R" => "5*"),
//		"g" => array("P" => "M*", "N" => "0", "R" => "6*"),
//		"h" => array("P" => "M*", "N" => "0", "R" => "7*"),
//		"i" => array("P" => "M*", "N" => "0", "R" => "8*"),
//		"j" => array("P" => "1*", "N" => "1", "R" => "9*"),
//		"k" => array("P" => "1*", "N" => "1", "R" => "X*"),
//		"l" => array("P" => "1*", "N" => "1", "R" => "10*")
//	);
	// Hit/Miss Outdoor
//	$Trg3DComplete = array
//	(
//	// Punti non dubbi
//		"A" => array("P" => "M", "N" => "0", "R" => "M"),
//		"B" => array("P" => "M", "N" => "0", "R" => "1"),
//		"C" => array("P" => "M", "N" => "0", "R" => "2"),
//		"D" => array("P" => "M", "N" => "0", "R" => "3"),
//		"E" => array("P" => "M", "N" => "0", "R" => "4"),
//		"F" => array("P" => "5", "N" => "5", "R" => "5"),
//		"G" => array("P" => "M", "N" => "0", "R" => "6"),
//		"H" => array("P" => "M", "N" => "0", "R" => "7"),
//		"I" => array("P" => "8", "N" => "8", "R" => "8"),
//		"J" => array("P" => "M", "N" => "0", "R" => "9"),
//		"K" => array("P" => "11", "N" => "11", "R" => "11"),
//		"L" => array("P" => "10", "N" => "10", "R" => "10"),
//	// Punti dubbi
//		"a" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"b" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"c" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"d" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"e" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"f" => array("P" => "5*", "N" => "5", "R" => "5*"),
//		"g" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"h" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"i" => array("P" => "8*", "N" => "8", "R" => "8*"),
//		"j" => array("P" => "M*", "N" => "0", "R" => "M*"),
//		"k" => array("P" => "11*", "N" => "11", "R" => "11*"),
//		"l" => array("P" => "10*", "N" => "10", "R" => "10*")
//	);

/*
 * Sta roba qua serve perchè php è ignorante e non usa bene le var globali.
 * Queste globals verranno usate nelle classi di import/export arf
 */
	$GLOBALS['LetterPoint']=$LetterPoint;
//	$GLOBALS['TrgOutdoor']=$TrgOutdoor;
//	$GLOBALS['TrgField']=$TrgField;
//	$GLOBALS['TrgIndComplete']=$TrgIndComplete;
//	$GLOBALS['TrgIndSmall']=$TrgIndSmall;
//	$GLOBALS['TrgCOIndComplete']=$TrgCOIndComplete;
//	$GLOBALS['TrgCOIndSmall']=$TrgCOIndSmall;
//	$GLOBALS['TrgHMOutComplete']=$TrgHMOutComplete;
//	$GLOBALS['Trg3DComplete']=$Trg3DComplete;

/*
	- GetTargetType($EventCode,$TeamEvent=0)
	Ritorna la variabile che contiene il target.
	$EventCode � l'evento.
	$TeamEvent vale 1 se l'evento � a squadre oppure 0 se � individuale
	$TourId vale -1 se va usato $_SESSION['TourId'] altrimenti è l'id del torneo
*/
	function GetTargetType($EventCode,$TeamEvent=0,$TourId=-1)
	{
		$Target = 'TrgOutdoor';

		$ToId=($TourId!=-1 ? $TourId : StrSafe_DB($_SESSION['TourId']));

		$Select
			= "SELECT EvCode,EvFinalTargetType,TarArray "
			. "FROM Events INNER JOIN Targets ON EvFinalTargetType=TarId AND EvTeamEvent=" . StrSafe_DB($TeamEvent) . " "
			. "WHERE EvTournament=" . $ToId . " AND EvCode=" . StrSafe_DB($EventCode) . " ";
		$Rs=safe_r_sql($Select);
		//print $Select;exit;
		if (safe_num_rows($Rs)==1)
		{
			$MyRow=safe_fetch($Rs);
			$Target = $MyRow->TarArray ;
		}

		return $Target;
	}

/**
 * Ritorna true se il bersaglio $Target è completo
 *
 * @param String $Target: Nome della variabile che rappresenta il bersaglio
 *
 * @return boolean: true se il bersaglio è completo; false altrimenti
 */
	function TargetIsComplete($Target)
	{

		$Ret=true;
		//print substr($Target,-5,5);exit;
		if (substr($Target,-5,5)=='Small')
		{
			$Ret=false;
		}
		return $Ret;
	}

/**
 *  Nuova ver di ValutaArrowString.
 *  Valuta l'arrowstring usando $LetterPoint
 *
 *  @param string $Letter: lettera chiave di $LetterPoint
 *  @return string: stringa vuota in caso di problemi oppure la somma dei punti di $LetterPoint
 */
//XXX Tolto il parametro $MySym e sganciato dal sorgente
	function ValutaArrowString($MyStr)
	{
		global $LetterPoint;
		/*
		 * converto in maiuscolo perchè tanto il valore numerico
		 * del dubbio è uguale a quello del non dubbio
		 */
		$MyStr=strtoupper($MyStr);
		$Tot=0;
		for ($i=0;$i<strlen($MyStr);++$i)
		{
			$letter=$MyStr[$i];

			if(array_key_exists($letter,$LetterPoint))
				$Tot+=$LetterPoint[$letter]["N"];
		}

		return $Tot;
	}

/**
 *  Nuova(2) ver di ValutaArrowStringGX.
 *  Valuta l'arrowstring contanto ori e x usando $LetterPoint
 *
 *  @param string $MyStr: lettera chiave di $LetterPoint
 *  @param string $G: string di chiavi di $LetterPoint da usare come gold
 *  @param string $X: string di chiavi di $LetterPoint da usare come xnine
 *  @return int[]: Array di 3 elementi: [Score,Gold,XNine]
 */
	function ValutaArrowStringGX($MyStr,$G=null,$X=null)
	{
		global $LetterPoint;

		$TotScore=0;
		$TotGold=0;
		$TotXNine=0;

		if(is_null($G) or is_null($X)) {
			$q=safe_r_sql("select ToGoldsChars, ToXNineChars from Tournament where ToId={$_SESSION['TourId']}");
			$r=safe_fetch($q);
			if(is_null($G)) $G=$r->ToGoldsChars;
			if(is_null($X)) $X=$r->ToXNineChars;
		}
	// trasformo in array $G e $X per cercarli meglio
		$G = preg_split('//', $G, -1, PREG_SPLIT_NO_EMPTY);
		$X = preg_split('//', $X, -1, PREG_SPLIT_NO_EMPTY);
//print_r($G).'<br>';
//print_r($X).'<br>';
//exit;
		for ($i=0;$i<strlen($MyStr);++$i)
		{
		/*
		 * tutto in maiuscolo perchè tanto il valore numerico del punto dubbio è uguale
		 * al punto non dubbio
		 */
			$letter=strtoupper($MyStr[$i]);

		// se la lettera nell'arrowstring è una chiave buona
			if(array_key_exists($letter,$LetterPoint))
			{
			// score
				$TotScore+=$LetterPoint[$letter]["N"];

			/* gold e xnine */

			// gold
				if (in_array($letter,$G))
					++$TotGold;

//				foreach ($G as $g)
//				{
//					if (array_key_exists($g,$LetterPoint))
//						++$TotGold;
//				}

			// xnine
				if (in_array($letter,$X))
					++$TotXNine;

//				foreach ($X as $x)
//				{
//					if (array_key_exists($x,$LetterPoint))
//						++$TotXNine;
//				}
			}
		}

		return array($TotScore,$TotGold,$TotXNine);
	}

	/**
	 *  Nuova(2) ver di ValutaArrowStringGX.
	 *  Valuta l'arrowstring contanto ori e x usando $LetterPoint
	 *
	 *  @param string $MyStr: lettera chiave di $LetterPoint
	 *  @return int[]: Array di 3 elementi: [Points, Max Weight, Number of stars]
	 */
	function ValutaArrowStringSO($MyStr) {
		global $LetterPoint;

		$TotScore = 0;
		$MaxWeight= 0;
		$TotStars = 0;
		$TotX = 0;
		$Letters=array();

		for ($i=0;$i<strlen($MyStr);++$i) {
			/*
			 * tutto in maiuscolo perchè tanto il valore numerico del punto dubbio è uguale
			 * al punto non dubbio
			 */
			$letter=strtoupper($MyStr[$i]);
			if($letter != $MyStr[$i]) $TotStars++;

			// se la lettera nell'arrowstring è una chiave buona
			if(array_key_exists($letter, $LetterPoint)) {
				// score
				$TotScore += $LetterPoint[$letter]["N"];
				$MaxWeight=max($MaxWeight, $LetterPoint[$letter]["W"]);
				$Letters[]=$LetterPoint[$letter]["N"];
				if($LetterPoint[$letter]["P"]=='X') $TotX++;
			}
		}

		rsort($Letters);

		return array($TotScore, $MaxWeight, $TotStars, $TotX, $Letters);
	}

/**
 *  Nuova ver di DecodeFromLetter.
 *  Data la lettera ritorna il suo valore di stampa.
 *
 *  @param string $Letter: lettera chiave di $LetterPoint
 *  @return string: stringa vuota in caso di problemi oppure colonna "P" di $LetterPoint
 */
//XXX tolto il parametro $Target e sganciato dal sorgente
	function DecodeFromLetter($Letter='')
	{
		global $LetterPoint;

		IF(!$Letter) RETURN '';
		$maybe=false;

	/*
	 * Se non esiste la chiave nel vettore potrebbe essere un dubbio.
	 * Se una volta convertita in maiuscola non trovo la lettera
	 * allora ho un errore
	 */
		if (!array_key_exists($Letter,$LetterPoint))
		{
			$Letter=strtoupper($Letter);
			$maybe=true;
		}

		if (array_key_exists($Letter,$LetterPoint))
		{
			return $LetterPoint[$Letter]['P'] . ($maybe ? '*' : '');
		}
		else
			return '';

	}

	/**
	 *  DecodeFromString.
	 *  Data la Stringa ritorna un array del suo valore di stampa.
	 *
	 *  @param string $Letter: stringa di lettere chiave di $LetterPoint
	 *  @return string: stringa vuota in caso di problemi oppure colonna "P" di $LetterPoint
	 */
	//XXX tolto il parametro $Target e sganciato dal sorgente
	function DecodeFromString($Letter='',$sum=false)
	{
		global $LetterPoint;
		$SumMaybe='';
		$SumReturn=0;

		IF(!$Letter) RETURN array();
		$maybe=false;

		$ret=array();
		foreach(range(1,strlen($Letter)) as $n) {
			$maybe=false;
			/*
			 * Se non esiste la chiave nel vettore potrebbe essere un dubbio.
			* Se una volta convertita in maiuscola non trovo la lettera
			* allora ho un errore
			*/
			if (!array_key_exists($Letter[$n-1],$LetterPoint))
			{
				$Letter[$n-1]=strtoupper($Letter[$n-1]);
				$maybe=true;
				$SumMaybe=true;
			}

			if (array_key_exists($Letter[$n-1],$LetterPoint))
			{
				$ret[]= $LetterPoint[$Letter[$n-1]]['P'] . ($maybe ? '*' : '');
			}
			else
				$ret[]= ' ';

		}
		if($sum) return ValutaArrowString($Letter).($maybe ? '*' : '');
		if(strlen($Letter)==1) return $ret[0];
		return $ret;

	}


/**
 * Nuova ver di DecodeFromPrint
 * Dato il valore di stampa ritorna il valore numerico.
 *
 * @param string $Value: valore di stampa da cercare
 * @return int: valore numerico. Ritorna 0 se non trova nulla.
 */
//XXX tolto il parametro $Target e sganciato dal sorgente
	function DecodeFromPrint($Value)
	{
		global $LetterPoint;

	/*
	 * Tutto diventa maiuscolo e rimuovo '*' perchè tanto
	 * il valore numerico del dubbio è quello del non dubbio.
	 */
		$P = strtoupper($Value);
		$P = str_replace('*','',$P);

		foreach ($LetterPoint as $Key => $Value)
		{
			if ($P==$Value['P'])
			{
				return $Value['N'];
			}
		}

		return 0;
	}

/*
	- GetLetterFromSearch($Value,$Target)
	Dato $Value il valore di Ricerca sul bersaglio, ritorna la chiave di $Target
*/
	function GetLetterFromSearch($Value,$Target)
	{
		$R = strtoupper($Value);
		//print 'R->' . $R . '<br>';
		foreach ($Target as $Key => $Value)
		{
			/*print '<pre>';
			print_r($Value);
			print '</pre>';*/
			if ($R==$Value['R'])
			{
				return $Key;
			}
		}
	}
/**
 * Nuova ver di GetLetterFromPrint
 * Dato il valore di stampa ritorna la lettera di codifica (la chiave di $LetterPoint).
 * NOTA: se nel valore di stampa c'è l' "*" la funzione ritorna la chiave in minuscolo
 * che significa punto dubbio.
 *
 * @param string $Value: valore di stampa da cercare
 * @return string: chiave di $LetterPoint. Se c'è qualche problema ritorna ' '
 */
//XXX tolto il parametro $Target e sganciato dal sorgente
	function old_GetLetterFromPrint($Value)
	{
		global $LetterPoint;

		$maybe=false;

		$P = strtoupper($Value);
		if (strpos($P,'*')!==false)
			$maybe=true;

		$P = str_replace('*','',$P);

		foreach ($LetterPoint as $Key => $Value)
		{
			if ($P==$Value['P'])
			{
				return ($maybe ? strtolower($Key) : $Key);
			}
		}

		return ' ';
	}

/**
 * Nuova ver di GetLetterFromPrint
 * Dato il valore di stampa ritorna la lettera di codifica (la chiave di $LetterPoint)
 * e nel caso vengano passati gli ultimi due parametri, lavora solo sul subset di lettere per il tipo
 * di bersaglio (vedi GetGoodLettersFromDist).
 *
 * NOTA: se nel valore di stampa c'è l' "*" la funzione ritorna la chiave in minuscolo
 * che significa punto dubbio.
 *
 * NOTA2: se $entry è un array, rappresenta un bersaglio valido subset di $LetterPoint!!!
 *
 * NOTA3: se $entry = "T" il $dist è l'ID del bersaglio
 *
 * @param string $Value valore di stampa da cercare
 * @param int $entry id della persona
 * @param int $dist distanza tirata
 * @return string chiave di $LetterPoint. Se c'è qualche problema ritorna uno spazio
 */
//XXX tolto il parametro $Target e sganciato dal sorgente
	function GetLetterFromPrint($Value, $entry=null, $dist=null)
	{
		global $LetterPoint;
		static $TargetTypes=array();

		// le lettere sono quelle di LetterPoint se non specifico il subset
		if(is_array($entry)) {
			$Letters=array_keys($entry);
		} elseif($entry=='T') {
			if(empty($TargetTypes[$dist])) {
				$TargetTypes[$dist]=GetGoodLettersFromTgtId($dist);
			}
			$Letters=$TargetTypes[$dist];
		} elseif(!($entry and $dist and $Letters=GetGoodLettersFromDist($entry,$dist))) {
			$Letters=array_keys($LetterPoint);
		}

		$maybe=false;

		$P = strtoupper($Value);
		if (strpos($P,'*')!==false)
			$maybe=true;

		$P = str_replace('*','',$P);

		foreach ($Letters as $l)
		{
			if ($P==$LetterPoint[$l]['P'])
			{
				return ($maybe ? strtolower($l) : $l);
			}
		}

//		foreach ($LetterPoint as $Key => $Value)
//		{
//			if ($P==$Value['P'])
//			{
//				return ($maybe ? strtolower($Key) : $Key);
//			}
//		}

		return ' ';
	}
/**
 * GetHigherTargetValue
 * ritorna il valore numerico più alto per un dato bersaglio
 */
	function GetHigherTargetValue($Target) {
		global $LetterPoint;
		$ret=0;

		foreach(array_keys($LetterPoint) as $index) if($Target[$index]['N']>$ret) $ret=$Target[$index]['N'];

		return $ret;
	}

/*
	- GetMaxScores($EventCode, $MatchNo, $TeamEvent=0, $TourId=-1)
	Returns an array of Maxvalues for that match.
	$EventCode is the event.
	$MatchNo is the number of the match (refer to table Grid)
	$TeamEvent = 1 for team events, 0 for individual
	$TourId: if -1 will use $_SESSION['TourId']
	The return array is
	Arrows => array of accepted arrows:
		'key': same code as $LetterPoint
		val[0] = size of ring
		val[1] = fill color
		val[2] = border color
	MaxPoint = maximum arrow point value;
	MaxEnd   = maximum per end
	MaxMatch = maximum per match
	MaxSetPoints = 0 if no SET system, Setpoints to win if SET system
	MaxSO    = maximum TOTAL ShootOff arrows

*/
	function GetMaxScores($EventCode, $MatchNo=0, $TeamEvent=0, $TourId=-1)
	{
		global $LetterPoint;

		$ret = array();

		$ToId=($TourId!=-1 ? $TourId : StrSafe_DB($_SESSION['TourId']));

		$Select
			= "SELECT"
			. " Targets.* "
			. " , EvMatchMode, EvFinalTargetType, EvTargetSize"
			. " , @Phase:=ifnull(2*pow(2,truncate(log2($MatchNo/2),0)),1) Phase"
			. ' , @PhaseMatch:=(@Phase & EvMatchArrowsNo)'
			. ' , if(@PhaseMatch, EvElimEnds, EvFinEnds) CalcEnds'
			. ' , if(@PhaseMatch, EvElimArrows, EvFinArrows) CalcArrows'
			. ' , if(@PhaseMatch, EvElimSO, EvFinSO) CalcSO '
			. "FROM"
			. " Events"
			. " INNER JOIN Targets ON EvFinalTargetType=TarId "
			. "WHERE"
			. " EvTournament=" . $ToId . ""
			. " AND EvCode=" . StrSafe_DB($EventCode) . " "
			. " AND EvTeamEvent=" . StrSafe_DB($TeamEvent) . " ";
			$Rs=safe_r_sql($Select);

		if ($MyRow=safe_fetch($Rs))
		{
			$ret['Arrows']=array('A' => array(0, '', ''));
			$ret['MaxPoint']=0;
			$ret['MinPoint']=999;
			$oldcolor='';
			if(isset($GLOBALS['CurrentTarget'])) {
				$GLOBALS['CurrentTarget']['A'] = $LetterPoint['A'];
			}
			$size=0;
			foreach(range('A','Z') as $key) {
				if($MyRow->{$key.'_size'}) {
					if($size < $MyRow->{$key.'_size'})
						$size = $MyRow->{$key.'_size'};
					// fills the accepted arrows array
					$ret['Arrows'][$key]=array($MyRow->{$key.'_size'}, $MyRow->{$key.'_color'}, ($MyRow->{$key.'_color'}=='000000' && $oldcolor=='000000')?'FFFFFF':'000000');
					$oldcolor=$MyRow->{$key.'_color'};

					// check the maxpoint
					if($LetterPoint[$key]['N']>$ret['MaxPoint']) $ret['MaxPoint']=$LetterPoint[$key]['N'];

					// check the minpoint
					if($LetterPoint[$key]['N'] and $LetterPoint[$key]['N']<$ret['MinPoint']) $ret['MinPoint']=$LetterPoint[$key]['N'];

					if(isset($GLOBALS['CurrentTarget'])) {
						$GLOBALS['CurrentTarget'][$key] = $LetterPoint[$key];
					}
				}
			}
			$ret['MaxEnd']=$ret['MaxPoint']*$MyRow->CalcArrows;
			$ret['MaxMatch']=$ret['MaxEnd']*$MyRow->CalcEnds;
			$ret['MaxSetPoints']=($MyRow->EvMatchMode ? $MyRow->CalcEnds+2 : 0);
			$ret['MaxSO']=$ret['MaxPoint']*$MyRow->CalcSO;
			$ret['ArrowsPerEnd']=$MyRow->CalcArrows;
			$ret['Ends']=$MyRow->CalcEnds;
			$ret['SO']=$MyRow->CalcSO;
			$ret['Size']= ($MyRow->EvTargetSize ? $MyRow->EvTargetSize : 122) * 50 * ($size/100);
		}

		return $ret;
	}

function GetTarget($TourId, $TrgName='') {
	global $LetterPoint;
	if($TrgName) {
		$q=safe_r_SQL("select Targets.* from Targets where TarDescr='$TrgName'");
	} else {
		$q=safe_r_SQL("select Targets.* from Targets inner join TargetFaces on TfT1=TarId where TfTournament={$TourId} and TfDefault='1'");
	}

	if(!($MyRow=safe_fetch($q))) return false;

	$ret=array();

	foreach(range('Z','B') as $key) {
		if($MyRow->{$key.'_size'}) {
			// fills the accepted arrows array
			$ret[] = $LetterPoint[$key]['P'];
		}
	}
	$ret[] = $LetterPoint['A']['P'];

	return $ret;
}

function GetTargetColors($TourId, $TrgName='') {
	global $LetterPoint;
	if($TrgName) {
		$q=safe_r_SQL("select Targets.* from Targets where TarDescr='$TrgName'");
	} else {
		$q=safe_r_SQL("select Targets.* from Targets inner join TargetFaces on TfT1=TarId where TfTournament={$TourId} and TfDefault='1'");
	}

	if(!($MyRow=safe_fetch($q))) return false;

	$ret=array();

	$X='';
	foreach(range('Z','A') as $key) {
		if($MyRow->{$key.'_size'}) {
			// fills the accepted arrows array
			if($LetterPoint[$key]['P']=='X') $X=array($key=>$MyRow->{$key.'_color'});
			else $ret[$key] = $MyRow->{$key.'_color'};
		}
	}
	if($X) $ret=$X+$ret;

	return $ret;
}



/**
 * Ritorna le lettere che esistono nel target di qualifica data la persona
 * e la distanza a cui sta tirando
 * @param int $entry: persona
 * @param int $dist: distanza
 * @return chars[]: lettere presenti nel bersaglio. La 'A' (zero) ci sarà sempre
 */
	function GetGoodLettersFromDist($entry,$dist=1)
	{
		$ret=array();

		if (!preg_match('/[1-8]{1,1}/',$dist))
			return $ret;

		$safeEntry=StrSafe_DB($entry);

		$q="
			SELECT
				Targets.*
			FROM
				Entries
				INNER JOIN
					TargetFaces
				ON EnTargetFace=TfId AND EnTournament=TfTournament
				INNER JOIN
					Targets
				ON TfT{$dist}=TarId
			WHERE
				EnId={$safeEntry}
		";
		$r=safe_r_SQL($q);

		if ($r && safe_num_rows($r)==1)
		{
			$ret[]='A';	// lo zero lo metto sempre

			$row=safe_fetch($r);
			foreach (range('B','Z') as $letter)
			{
				if ($row->{$letter . '_size'}!=0)
					$ret[]=$letter;
			}
		}

		//print_r($ret);
		return $ret;
	}

/**
 * Ritorna le lettere che esistono nel target
 * @param int $target: target ID
 * @return chars[]: lettere presenti nel bersaglio. La 'A' (zero) ci sarà sempre
 */
	function GetGoodLettersFromTgtId($target) {
		$ret=array();

		$q="SELECT * FROM Targets where TarId=$target";
		$r=safe_r_SQL($q);

		if ($row=safe_fetch($r)) {
			$ret[]='A';	// lo zero lo metto sempre
			foreach (range('B','Z') as $letter) {
				if ($row->{$letter . '_size'}!=0) $ret[]=$letter;
			}
		}

		//print_r($ret);
		return $ret;
	}

	function GetTargetInfo($TrgId, $size=0) {
		global $LetterPoint;
		$q=safe_r_SQL("select Targets.* from Targets where TarId=$TrgId");

		if(!($MyRow=safe_fetch($q))) return false;

		$ret=array();

		foreach(range('Z','B') as $key) {
			if($MyRow->{$key.'_size'}) {
				$ret[]=array("value"=>$LetterPoint[$key]['N'], "display"=>$LetterPoint[$key]['P'], "color"=>$MyRow->{$key.'_color'}, "diameter"=> strval($MyRow->{$key.'_size'} * $size /10));
			}
		}
		$ret[] = array("value"=>$LetterPoint['A']['N'], "display"=>$LetterPoint['A']['P'], "color"=>"FFFFFF", "diameter"=>0);

		return $ret;
	}

function GetMaxTargetValue($TargetLetters) {
	global $LetterPoint;
	$ret=0;

	foreach($TargetLetters as $Letter) {
		if($LetterPoint[$Letter]['N']>$ret) $ret=$LetterPoint[$Letter]['N'];
	}

	return $ret;
}
/**
 * returns how many points to add to the original value if all stars are hit!
 * @param string $ArrowString the arrowstring to evaluate with all stars raised to the upper value
 * @param string $Regexp the class of callable stars (will be set if an empty string is passed)
 * @param string $Event Event where this funziont is needed
 * @param number $TeamEvent idem
 * @param number $TourId if none the current comp is used
 * @return number
 */
function RaiseStars($ArrowString, &$Regexp='', $Event='', $TeamEvent=0, $TourId=0) {
	if(!$Regexp) {
		if(!$TourId) $TourId=$_SESSION['TourId'];
		$q=safe_r_sql("select TarStars from Targets inner join Events on EvTournament={$TourId} and EvFinalTargetType=TarId and EvCode='$Event' and EvTeamEvent=$TeamEvent");
		if($r=safe_fetch($q)) $Regexp=$r->TarStars;
	}

	if(!$Regexp) return 0;

	return strlen($ArrowString) - strlen(preg_replace('/['.$Regexp.']/', '', $ArrowString));
}