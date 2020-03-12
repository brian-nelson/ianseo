<?php
/*
													- UpdateField.php -
	Aggiorna un campo alla volta.
	Non deve essere usata dai campi che riguardano la nazione perch� questi verranno gestiti da UpdateCountry.php
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclCompetition, AclReadWrite, false);

	$Arr_Tabelle = array
	(
		'e' => array('Entries','EnId')
	);

	$Errore=0;
	$Which='#';


		foreach ($_REQUEST as $Key => $Value)
		{
			if (substr($Key,0,2)=='d_')
			{
				$Campo = '';
				$Chiave = '';
				$Tabella = '';
				list(,$Tabella,$Campo,$Chiave) = explode('_',$Key);
				$Which=$Key;
				if (!IsBlocked(BIT_BLOCK_ACCREDITATION))
				{
					/*$Update
						= "UPDATE Entries SET "
						. $Campo . "=" . StrSafe_DB($Value) . " "
						. "WHERE EnId=" . StrSafe_DB($Chiave) . " ";*/
					$Update
						= "UPDATE " . $Arr_Tabelle[$Tabella][0]  . " SET "
						. $Campo . "=" . StrSafe_DB(stripslashes($Value)) . " "
						. "WHERE " . $Arr_Tabelle[$Tabella][1] . "=" . StrSafe_DB($Chiave). " ";
					$RsUp=safe_w_sql($Update);

					if (!$RsUp)
						$Errore=1;

					if (debug)
						print $Update .'<br><br>';

					$Select
						= "SELECT " . $Campo . " FROM " . $Arr_Tabelle[$Tabella][0] . " WHERE " . $Arr_Tabelle[$Tabella][1] . "=" . StrSafe_DB($Chiave). " ";
					$Rs=safe_r_sql($Select);

					if (!$Rs || safe_num_rows($Rs)!=1)
					{
						$Errore=1;
					}
					else
					{
						$Row=safe_fetch($Rs);
						if ($Row->{$Campo}!=$Value)
							$Errore=1;
					}
				}
				else
					$Errore=1;
			}
		}


	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<which>' . $Which . '</which>' . "\n";
	print '</response>' . "\n";
?>