<?php
/*
													- ChangeEvent.php -
	Ritorna la fase da cui parte l'evento Ev.
	Se l'evento � '' (Tutti) viene ritornata la fase max in Grids
*/

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');

	CheckTourSession(true);

	$Errore=0;
	$Team = (isset($_REQUEST['TeamEvent']) ? intval($_REQUEST['TeamEvent']) : 0);
	$Event= (isset($_REQUEST['Ev']) ? trim($_REQUEST['Ev']) : '');
	$StartPhase = -1;
	$SetPoints = 0;

// se ho un event faccio la query
	if ($Event) {
		$Select = "SELECT EvFinalFirstPhase AS StartPhase, EvMatchMode as MatchMode
			FROM Events
			WHERE EvTournament={$_SESSION['TourId']}
				AND EvCode=" . StrSafe_DB($Event) . "
				AND EvTeamEvent=$Team" ;
		$Rs=safe_r_sql($Select);

		if (safe_num_rows($Rs)==1) {
			$Row=safe_fetch($Rs);
			$StartPhase = $Row->StartPhase;
			$SetPoints = ($Row->MatchMode!=0);
		} else {
			$Errore=1;
		}
	} else {
		// tutti gli eventi
		if (!$Team) {
			$Select = "SELECT MAX(EvFinalFirstPhase) AS Phase, MAX(EvMatchMode) AS MatchMode
				FROM Events
				WHERE EvTournament={$_SESSION['TourId']}
			 		AND EvTeamEvent=$Team";

			$Rs=safe_r_sql($Select);

			if (safe_num_rows($Rs)==1) {
				$Row=safe_fetch($Rs);
				$StartPhase=$Row->Phase;
				$SetPoints = ($Row->MatchMode!=0);
			} else {
				$Errore=1;
			}
		} else {
			$Select = "SELECT GrPhase FROM Grids WHERE GrPhase=" . StrSafe_DB(TeamStartPhase) . " AND GrPosition='1' ";
			$RsPh=safe_r_sql($Select);

			// Se la fase iniziale esiste in griglia allora uso quella altrimenti cerco la massima disponibile

			if (safe_num_rows($RsPh)!=1) {
				$Select = "SELECT MAX(GrPhase) AS Phase FROM Grids ";
				$RsPh=safe_r_sql($Select);

				if (safe_num_rows($RsPh)==1) {
					$Row=safe_fetch($RsPh);
					$StartPhase=$Row->Phase;
				} else {
					$Errore=1;
				}
			} else {
				$StartPhase=TeamStartPhase;
			}
		}
	}



	header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<team>' . $Team . '</team>' . "\n";
	print '<start_phase>' . $StartPhase . '</start_phase>' . "\n";
	print '<set_points>' . ($SetPoints ? '1':'0') . '</set_points>' . "\n";
	for ($i=$StartPhase;$i>=1;$i/=2) {
		if($i==12) {
			$i = (trim($_REQUEST['Ev'])!='' ? 16 : 32);
		}
		print '<good_phase>';
		print '<code>' . ($i==48 ? 64 : ($i==24 ? 32 : $i)) . '</code>' . "\n";
		print '<name><![CDATA[' . get_text( $i . '_Phase') . ']]></name>';
		print '</good_phase>' . "\n";
	}
	print '<good_phase>';
	print '<code>0</code>' . "\n";
	print '<name>' . get_text('0_Phase') . '</name>';
	print '</good_phase>' . "\n";
	print '</response>' . "\n";
?>