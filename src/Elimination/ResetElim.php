<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');
	
	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$error=0;
	$xml='';

	$event=isset($_REQUEST['event']) ? $_REQUEST['event'] : null;

	if (is_null($event))
	{
		$error=1;
	}
	else
	{
	// elimino le rige di Eliminations
		$query
			= "DELETE "
			. "FROM "
				. "Eliminations "
			. "USING "
				. "Eliminations "
				. "INNER JOIN "
					. "Entries "
				. "ON ElId=EnId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
					. "AND ElEventCode=" . StrSafe_DB($event) .  " ";

		//print $query . '<br><br>';
		$rs=safe_w_sql($query);

		
		$x=DeleteElimRows($event,1);
		$y=DeleteElimRows($event,2);
		
		if (!$x || !$y)
		{
			$error=1;
		}
		else
		{
		// distruggo la griglia delle finali
			$query
				= "DELETE FROM Finals "
				. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent = " . StrSafe_DB($event) . " ";

			//print $query . '<br><br>';
			$rs=safe_w_sql($query);

			if (!$rs)
			{
				$error=1;
			}
			else
			{
			// ricreo le griglie eliminatorie
				$x=CreateElimRows($event,1);
				$y=CreateElimRows($event,2);
			
				if ($x && $y)
				{
				// ricreo la griglia distrutta
					$query
						= "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) "
						. "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i')) . " "
						. "FROM Events INNER JOIN Grids ON GrPhase<=EvFinalFirstPhase AND EvTeamEvent='0' "
						. "AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
						. "WHERE EvCode = " . StrSafe_DB($event);
	
					//print $query . '<br><br>';
					$rs=safe_w_sql($query);
	
					if (!$rs)
					{
						$error=1;
					}
					else
					{
					// azzero i flags di shootoff
						/*$query
							= "UPDATE "
								. "Events "
							. "SET "
								. "EvShootOff=0,EvE1ShootOff=0,EvE2ShootOff=0 "
							. "WHERE "
								. "EvCode=" . StrSafe_DB($event) . " AND EvTeamEvent=0 AND 	EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
						$rs=safe_w_sql($query);
						set_qual_session_flags();*/
						
						if (!ResetShootoff($event,0,0))
							$error=1;
						
					}
				}
				else
				{
					$error=1;
				}
			}

		}
	}

	$xml
		.='<response>' . "\n"
			. '<error>' . $error . '</error>' . "\n"
			. '<event>' . $event . '</event>' . "\n"
		. '</response>' . "\n";


	
	header('Content-Type: text/xml; charset=UTF-8');
	print $xml;
?>