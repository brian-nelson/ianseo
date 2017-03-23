<?php
/*
													- Fun_Number.inc.php -
	File contenente le funzioni per la manipolazione i numeri
*/

/*
	- NumFormat($TheNum,$Dec)
	Riformatta il numero $TheNum con le impostazioni di lingua se $TheNum � un numero.
	Ritorna 0 se $TheNum non � un numero.
	Mette $Dec cifre decimali nella formattazione
*/
	function NumFormat($TheNum,$Dec=0)
	{
		if (is_numeric($TheNum))
		{
			return number_format($TheNum,$Dec, get_text('NumberDecimalSeparator'), get_text('NumberThousandsSeparator'));
		}
		else
		{
			return 0;
		}
	}
?>