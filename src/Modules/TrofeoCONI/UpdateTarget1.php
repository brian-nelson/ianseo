<?php
	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Fun_CONI.local.inc.php');

	CheckTourSession(true);

	$error=0;
	$which=(isset($_REQUEST['which']) ? $_REQUEST['which'] : null);
	$value=(isset($_REQUEST['value']) ? $_REQUEST['value'] : null);

	if (is_null($which) || is_null($value))
		exit;

	if (!IsBlocked(BIT_BLOCK_TEAM))
	{
		/*
		 * Contiene le parti di $which.
		 * Avrò 3 elementi: T Event Round Match
		 */
			$parts=explode('_',$which);

			if (preg_match('/^[0-9]{1,3}$/i',$value) || trim($value)=='')
			{
				if (trim($value)!='')
					$value=str_pad($value,2,'0',STR_PAD_LEFT);

				$Query
					= "UPDATE "
						. "CasScore "
					. "SET "
						. "CaSTarget=" . StrSafe_DB($value) . " "
					. "WHERE "
						. "CaSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
						. "CaSPhase=1 AND  "
						. "CaSRound=" . $parts[2] . " AND "
						. "CaSMatchNo=" . $parts[3] . " AND "
						. "CaSEventCode=" . StrSafe_DB($parts[1]) . " ";

				$Rs=safe_w_sql($Query);

				if (!$Rs)
					$error=1;
			}
			else
				$error=1;
	}
	else
		$error=1;

	header('Content-Type: text/xml');

	print '<response>' . "\n";
		print '<error>' . $error . '</error>' . "\n";
		print '<which>' . $which . '</which>' . "\n";
		print '<value>' . $value . '</value>' . "\n";
	print '</response>' . "\n";
