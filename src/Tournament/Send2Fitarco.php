<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
    checkACL(AclCompetition, AclReadOnly);

	define('debug',true);

	if (!CheckTourSession() || !isset($_REQUEST['Code']) || !isset($_REQUEST['From']) || !isset($_REQUEST['Message'])) {
		print get_text('CrackError');
		exit;
	}

//	$MyPOST = array();  // Conterrà il post al sito

	$URL = '';

	if (!debug)
		$URL = GetParameter('ResPath');
	else
		$URL = 'http://ianseo/Tournament/prova/ricevi.php';

	$MyPOST['Subject'] = $_REQUEST['Code'];
	$MyPOST['From'] = $_REQUEST['From'];
	$MyPOST['Message'] = $_REQUEST['Message'];

// template dei files
	$Arr_Templates = array
	(
		'.asc',
		'.lst',
		'_team.lst',
		'_rank.lst',
		'_rank_team.lst',
		'.pdf',
		'_team.pdf',
		'_rank.pdf',
		'_grid.pdf',
		'_rank_team.pdf',
		'_grid_team.pdf'
	);

	$Arr_Files2Send = array();


	foreach ($Arr_Templates as $Value)
	{
		if(file_exists($CFG->DOCUMENT_PATH . 'Tournament/TmpDownload/' . $_REQUEST['Code'] . $Value))
		{
			$Arr_Files2Send[]='@' . $CFG->DOCUMENT_PATH . 'Tournament/TmpDownload/' . $_REQUEST['Code'] . $Value;

		}
	}

	/*print '<pre>';
	print_r($Arr_Files2Send);
	print '</pre>';exit;*/

	$StrCurlError = '';
	$PostResult = '';

// Se ci sono dei files li spedisco
	if (count($Arr_Files2Send)>0)
	{
		foreach ($Arr_Files2Send as $Value)
		{
			$Send = false;	// true se il file va spedito

		/*
		 * Se dentro alla dir md5 non c'è l'md5 di $Value, lo creo e spedisco online.
		 * Se dentro alla dir md5 c'è l'md5 di $Value ma è uguale all'attuale, non faccio nulla
		 * Se dentro alla dir md5 c'è l'md5 di $Value ed è diverso, creo il nuovo md5 e spedisco
		 */
			$bs = basename(substr($Value,1));

		// 1) L'md5 non c'è
			if (!file_exists($CFG->DOCUMENT_PATH . 'Tournament/TmpDownload/md5/' . $bs . '.md5'))
			{
				$fp = fopen($CFG->DOCUMENT_PATH . 'Tournament/TmpDownload/md5/' . $bs . '.md5','w');

				fputs($fp,md5_file(substr($Value,1)));

				fclose($fp);

				$Send=true;
			}
			else	// l'md5 c'è
			{
				$fp = fopen($CFG->DOCUMENT_PATH . 'Tournament/TmpDownload/md5/' . $bs . '.md5','r');

				$md5=fgets($fp,1024);

			// 2) diverso
				if ($md5!=md5_file(substr($Value,1)))
				{

				}

			}

			if ($Send)
			{
				$MyPOST['File']=$Value;

			// inizializzo la sessione CURL
				$ch = curl_init();

			// imposto l'URL dello script destinatario
				curl_setopt($ch, CURLOPT_URL, $URL );

			// indico il tipo di comunicazione da effettuare (POST)
				curl_setopt($ch, CURLOPT_POST, true );

			// indico i dati da inviare attraverso POST
				curl_setopt($ch, CURLOPT_POSTFIELDS, $MyPOST);

			// specifico che la funzione curl_exec dovrà restituire l'output
			// prodotto dall'URL contattato
			// invece di inviarlo direttamente al browser
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			// eseguo la connessione e l'invio dei dati e salvo in
			// $postResult l'output prodotto dall'URL contattato
				$PostResult = curl_exec($ch);

			// se ci sono stati degli errori mostro un messaggio esplicativo
				if (curl_errno($ch))
				{
					$StrCurlError .= curl_error($ch);
				}

				// chiudo la sessione CURL
				curl_close($ch);
			}
		}
	}
	print $StrCurlError . '<br>';
	print $PostResult;

?>