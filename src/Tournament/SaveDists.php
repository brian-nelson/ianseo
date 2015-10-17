<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['type']) ||
		!isset($_REQUEST['cl']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$tds=array();

	if (!IsBlocked(BIT_BLOCK_TOURDATA))
	{
		if ($_REQUEST['cl']!='')
		{

			$x=1;
			foreach($_REQUEST as $k=>$v)
			{
				if (substr($k,0,2)=='td')
					$tds[$k]=($v!='' ? $v : '.' . $x++ . '.');
			}

		// verifico se esiste una possibile div/cl per la regola
			$select
				= "SELECT "
					. "CONCAT(DivId,ClId) as Ev "
				. "FROM "
					. "Divisions INNER JOIN Classes ON DivTournament=ClTournament "
				. "WHERE "
					. "CONCAT(DivId,ClId) LIKE " . StrSafe_DB($_REQUEST['cl']) . " AND "
					. "DivTournament=" . StrSafe_DB($_SESSION['TourId']) .  " ";
			$rs=safe_r_sql($select);

			if (debug)
				print $select . '<br><br>';

			if (!($rs && safe_num_rows($rs)!=0))
			{
				$Errore=1;
			}
			else
			{
			// verifico che la regola non sia già inclusa in un'altra
				/*
				$select
					= "SELECT DISTINCT "
						. "TdClasses "
					. "FROM "
						. "Divisions INNER JOIN Classes ON DivTournament=ClTournament AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
						. "INNER JOIN TournamentDistances AS t ON TdType=" . StrSafe_DB($_REQUEST['type']) . " and TdTournament=DivTournament AND CONCAT(DivId,ClId) LIKE TdClasses "
					. "WHERE "
						. "TdClasses LIKE " . StrSafe_DB($_REQUEST['cl']) . " AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
				*/
				$select
					= "SELECT "
						. "TdClasses "
					. "FROM "
						. "TournamentDistances "
					. "WHERE "
						. "TdType=" . StrSafe_DB($_REQUEST['type']) . " AND "
						. "TdTournament={$_SESSION['TourId']} AND "
						. "(TdClasses LIKE " . StrSafe_DB($_REQUEST['cl']) . " OR " . StrSafe_DB($_REQUEST['cl']) . "LIKE TdClasses) ";
				$rs=safe_r_sql($select);

				if (debug)
					print $select . '<br><br>';
				//exit;
				if ($rs)
				{
					if (safe_num_rows($rs)!=0)
					{
						$Errore=2;
					}
					else
					{
						$distFields="";
						$distValues="";

						$replace
							= "REPLACE INTO TournamentDistances "
								. "(TdTournament, TdClasses,TdType,";
						foreach ($tds as $k => $v)
						{
							$distFields.=$k . ",";
							$distValues.=StrSafe_DB($v) . ",";
						}
						$distFields=substr($distFields,0,-1);
						$distValues=substr($distValues,0,-1);

						$replace
							.=$distFields . ") VALUES('{$_SESSION['TourId']}', " . StrSafe_DB($_REQUEST['cl']) . "," . StrSafe_DB($_REQUEST['type']) . "," . $distValues . ") ";

					//	print $replace;exit;
						if (!debug)
							$rs=safe_w_sql($replace);
						if (!$rs)
							$Errore=1;
					}
				}
				else
					$Errore=3;
			}
		}
		else
			$Errore=4;
	}
	else
		$Errore=5;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<cl>' . $_REQUEST['cl'] . '</cl>';
	print '<type>' . $_REQUEST['type'] . '</type>';
	print '<num_dist>' . $_REQUEST['numDist'] . '</num_dist>';
	foreach ($tds as $v)
	{
		print '<td>' . $v . '</td>' . "\n";
	}

	print '</response>' . "\n";
?>