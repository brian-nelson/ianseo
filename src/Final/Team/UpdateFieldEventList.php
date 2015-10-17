<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	$Which='#';

	foreach ($_REQUEST as $Key => $Value)
	{
		if (substr($Key,0,2)=='d_')
		{
			$Which = $Key;
			$cc = '';
			$ee = '';
			list (,$cc,$ee)=explode('_',$Key);

			if (!IsBlocked(BIT_BLOCK_TOURDATA))
			{
				$Update
					= "UPDATE Events SET "
					. $cc . "=" . StrSafe_DB($Value) . " "
					. "WHERE EvCode=" . StrSafe_DB($ee) . " AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
				$RsUp=safe_w_sql($Update);
				if (debug)
					print $Update . '<br>';
				if (!$RsUp)
					$Errore=1;
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