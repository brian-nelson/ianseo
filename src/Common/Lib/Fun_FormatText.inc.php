<?php
/*
													- Fun_FormatText.inc.php -
	File contenente le funzioni per la manipolazione dei testi
*/

/*
	- ManageHTML($MyString)
	Sistema i caratteri in $MyString per essere stampati a web
*/
	function ManageHTML($MyString)
	{
		$MyString=htmlentities($MyString,ENT_QUOTES,PageEncode);
		$MyString=nl2br($MyString);
		$MyString = str_replace (htmlentities("<"), "<", $MyString);
		$MyString = str_replace (htmlentities(">"), ">", $MyString);
		return $MyString;
	}

/*
	- LangTr($TrString)
	Sceglie come visualizzare $TrString.
	Se la stringa inizia con '~' vuol dire che la stringa rappresenta il nome di una variabile
	Se la stringa inizia con '|' significa che il testo � separato da pipe e le parti verranno analizzate con
	il precedente algoritmo;
	Se la stringa inizia con un carattere diverso da '~' e da '|' allora viene ritornata subito
*/
	function LangTr($TrString)
	{
		switch (substr($TrString,0,1))
		{
			case '~':	// Devo valutare la variabile contenuta nel nome
				return $GLOBALS[substr($TrString,1)];
			case '|':	// Devo processare i pezzi di stringa
				$Tmp="";
				foreach (explode('|',substr($TrString,1)) as $Value)
				{
					$Tmp.=(strpos($Value,'~')===0 ? $GLOBALS[substr($Value,1)] : $Value);
				}
				return $Tmp;
			default:	// Devo stampare la stringa
				return $TrString;
		}
	}

	function AdjustCaseTitle($string) {
		$string=trim($string);
		if(strstr($string, '.')) return $string;
		if($string==mb_convert_case($string, MB_CASE_UPPER, "UTF-8") or $string==mb_convert_case($string, MB_CASE_LOWER, "UTF-8")) $string=mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
		return $string;
	}

	function FirstLetters($string) {
		$n=preg_split('/[ -]/', $string);
		foreach($n as &$v) $v= mb_eregi_replace('^(.).+', '\\1', $v);
		return implode('',$n);
	}

function GetPaddedNames($Ath1, $len) {
	if($len > ($l2=strlen($Ath1))) return $len + abs($len-$l2)*0.9;
	return $l2;
}