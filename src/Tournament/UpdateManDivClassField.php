<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
    checkACL(AclCompetition, AclReadWrite, false);

	if (!CheckTourSession() || !isset($_REQUEST['Tab'])) {
		print get_text('CrackError');
		exit;
	}

/*
	- $Arr_Tables
	Array di lookup per le tabelle.
	Alla chiave corrisponde un vettore formato da:
		La tabella, i 2 campi di questa da usare come chiave per l'update
*/
	$Arr_Tables = array
	(
		'D' => array('Divisions','DivId','DivTournament','','DivTourRules'),
		'C' => array('Classes','ClId','ClTournament','','ClTourRules'),
		'SC'=> array('SubClass','ScId','ScTournament','','')
	);

	$Errore=0;
	$Which='#';

	if (!IsBlocked(BIT_BLOCK_TOURDATA) && !defined('dontEditClassDiv'))
	{
		if (!array_key_exists($_REQUEST['Tab'],$Arr_Tables))
			$Errore=1;
		else
		{
			$tt=$Arr_Tables[$_REQUEST['Tab']][0];	// tabella su cui fare l'update
			$kk=$Arr_Tables[$_REQUEST['Tab']][1];	// campo 1 da usare come chiave per l'update
			$kk2=$Arr_Tables[$_REQUEST['Tab']][2];	// campo 2 da usare come chiave per l'update
			$kk3=$Arr_Tables[$_REQUEST['Tab']][3];	// campo 3 da usare come chiave per l'update
			$kk4=$Arr_Tables[$_REQUEST['Tab']][4];	// campo 4 per resettare info del default
			
			foreach ($_REQUEST as $Key => $Value)
			{
				$Field='';
				$Id='';
				$ClDivAllowed='';
				if (substr($Key,0,2)=='d_') {
					$tmp=explode('_',$Key);
					$Field=$tmp[1];
					$Id=$tmp[2];
					if(!empty($tmp[3])) $ClDivAllowed=$tmp[3];

					$Which = $Key;

					$Update
						= "UPDATE " . $tt . " SET "
						. $Field . "=" . StrSafe_DB($Value) . " "
						. "WHERE " . $kk . "=" . StrSafe_DB($Id)
						. " AND " . $kk2 . "=" . StrSafe_DB($_SESSION['TourId']) . " "
						. ($kk3 ? " AND $kk3 = ".StrSafe_DB($ClDivAllowed) : '');
					$Rs=safe_w_sql($Update);
					if(safe_w_affected_rows() and (($Field=='ClAthlete' or $Field=='DivAthlete'))) {
						safe_w_sql("UPDATE {$tt} SET {$kk4}='' WHERE {$kk}=" . StrSafe_DB($Id) . " AND {$kk2}=" . StrSafe_DB($_SESSION['TourId']) . ($kk3 ? " AND $kk3 = ".StrSafe_DB($ClDivAllowed) : ''));
						// avvenuto un cambio di status di atleta!!!
						if($Value) {
							if($Field=='ClAthlete') safe_w_sql("Update Entries left join Divisions on EnTournament=DivTournament and EnDivision=DivId set EnAthlete=DivAthlete+0 where EnTournament={$_SESSION['TourId']} and EnClass='$Id'");
							elseif($Field=='DivAthlete') safe_w_sql("Update Entries left join Classes on EnTournament=ClTournament and EnClass=ClId set EnAthlete=ClAthlete+0 where EnTournament={$_SESSION['TourId']} and EnDivision='$Id'");
						} else {
							if($Field=='ClAthlete') safe_w_sql("Update Entries set EnAthlete='' where EnTournament={$_SESSION['TourId']} and EnClass='$Id'");
							elseif($Field=='DivAthlete') safe_w_sql("Update Entries set EnAthlete='' where EnTournament={$_SESSION['TourId']} and EnDivision='$Id'");
						}
					}
					

					if (debug) print $Update;

					if (!$Rs)
						$Errore=1;
				}
			}
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<which>' . $Which . '</which>' . "\n";
	print '</response>' . "\n";
?>