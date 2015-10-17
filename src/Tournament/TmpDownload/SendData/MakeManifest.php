<?php
	define ('debug',false);
// crea il manifest degli eventi

	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
	require_once('Common/PclZip/pclzip.lib.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	if (!isset($_REQUEST['ev']) || trim($_REQUEST['ev']==''))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$Credential=CheckCredential();

	if ($Credential)
	{
	// esplodo gli eventi
		$events=explode('|',$_REQUEST['ev']);

		$fileName=$CFG->DOCUMENT_PATH . 'Tournament/TmpDownload/SendData/' . $_SESSION['OnlineId'] . '_info.manifest';
		$f=fopen($fileName,"w");

	// Cerco il file modificato più recentemente
		$lastUpdate=0;
		$dir=$CFG->DOCUMENT_PATH . 'Tournament/TmpDownload/SendData/';

		foreach ($events as $v)
		{
			//print $dir . $_SESSION['OnlineId'] . '_' . $v . '.pdf' . '<br>';
			$x=filemtime($dir . $_SESSION['OnlineId'] . '_' . $v . '.pdf');
			if ($x>$lastUpdate)
				$lastUpdate=$x;
		}

		fputs($f,date('Y-m-d H:i:s',$lastUpdate) . "\n");

		$select = "";

		$EvCode='';
		$EvTeam='';
	/**
	 * per ogni evento a meno di quelli complessivi e non divisi per div/cl estraggo il codice e decido se è team o no.
	 * Aggrego la condizione della where e faccio la select.
	 *
	 * Aggiunta:
	 * tiro fuori anche l'impostazione della lingua di stampa per usare lei nella generazione delle descrizioni dei link che non sono
	 * nel default dello switch
	 */
		define('PRINTLANG', $_SESSION['TourPrintLang']);

		foreach ($events as $v)
		{
			switch ($v)
			{
			// Codici da gestire a mano
				case 'ENS':
					fputs($f,$v.'.pdf|' .  get_text('StartlistSession','Tournament') . '|' . date('Y-m-d H:i:s',filemtime($dir . $_SESSION['OnlineId'] . '_' . $v . '.pdf')) . "\n");
					break;
				case 'ENC':
					fputs($f,$v.'.pdf|' .  get_text('StartlistCountry','Tournament') . '|' . date('Y-m-d H:i:s',filemtime($dir . $_SESSION['OnlineId'] . '_' . $v . '.pdf')) . "\n");
					break;
				case 'ENA':
					fputs($f,$v.'.pdf|' .  get_text('StartlistAlpha','Tournament') . '|' . date('Y-m-d H:i:s',filemtime($dir . $_SESSION['OnlineId'] . '_' . $v . '.pdf')) . "\n");
					break;
				case 'IC':
					fputs($f,$v.'.pdf|' . get_text('ResultClass','Tournament') . ' - ' . get_text('Individual') . '|' . date('Y-m-d H:i:s',filemtime($dir . $_SESSION['OnlineId'] . '_' . $v . '.pdf')) . "\n");
					break;
				case 'TC':
					fputs($f,$v.'.pdf|' . get_text('ResultClass','Tournament') . ' - ' . get_text('Team') . '|' . date('Y-m-d H:i:s',filemtime($dir . $_SESSION['OnlineId'] . '_' . $v . '.pdf')) . "\n");
					break;
				case 'MEDSTD':
					fputs($f,$v.'.pdf|' . get_text('MedalStanding') . '|' . date('Y-m-d H:i:s',filemtime($dir . $_SESSION['OnlineId'] . '_' . $v . '.pdf')) . "\n");
					break;
				case 'MEDLST':
					fputs($f,$v.'.pdf|' . get_text('MedalList') . '|' . date('Y-m-d H:i:s',filemtime($dir . $_SESSION['OnlineId'] . '_' . $v . '.pdf')) . "\n");
					break;
			// Codici da gestire da db
				default:
					$EvCode=substr($v,2);
					$EvTeam=(substr($v,0,1)=='I' ? 0 : 1);

					$select
						.="(SELECT '" . $v . ".pdf' AS FileName,EvEventName "
						. "FROM Events "
						. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
						. "AND (EvCode=" . StrSafe_DB($EvCode) . " AND EvTeamEvent=" . $EvTeam . ")) "
						. "UNION ALL ";
			}
		// Codici da gestire a mano
		/*	if ($v=='IC' || $v=='TC' )
			{
			// qui scrivo a manina nel file
				fputs($f,$v.'.pdf|' . get_text('ResultClass','Tournament') . ' - ' . ($v=='IC' ? get_text('Individual') : get_text('Team')) . "\n");
			}
			else
			{
				$EvCode=substr($v,2);
				$EvTeam=(substr($v,0,1)=='I' ? 0 : 1);

				$select
					.="(SELECT '" . $v . ".pdf' AS FileName,EvEventName "
					. "FROM Events "
					. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
					. "AND (EvCode=" . StrSafe_DB($EvCode) . " AND EvTeamEvent=" . $EvTeam . ")) "
					. "UNION ALL ";

			}*/
		}

	// Descrizioni di IC e TC (no db)
/*		$IC_Descr=get_text('ResultClass','Tournament') . ' - ' . get_text('Individual');
		$TC_Descr=get_text('ResultClass','Tournament') . ' - ' . get_text('Team');*/

	// Descrizioni degli altri eventi

		if ($select!='')
		{
			$select=substr($select,0,-10);
			$Rs=safe_r_sql($select);

			if ($Rs)
			{
				while ($MyRow=safe_fetch($Rs))
				{
					fputs($f,$MyRow->FileName.'|'.$MyRow->EvEventName . '|' . date('Y-m-d H:i:s',filemtime($dir . $_SESSION['OnlineId'] . '_' . $MyRow->FileName)) . "\n");
				}
			}
			else
			{
				$Errore=1;
			}
		}


		fclose($f);
	}
	else
	{
		$Errore=1;
	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '</response>' . "\n";
?>