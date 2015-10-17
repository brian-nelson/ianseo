<?php
	define ('debug',false);
/*
 * Zippa i files in ./TmpDownload/SendData per inviarli
 * Deve essere settato il torneo e devono essere settate le credenziali
 */

	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
	require_once('Common/PclZip/pclzip.lib.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');
	
/**
 * Funzione di callback chiamata dopo ogni aggiunta
 * di file all'archivio zip.
 * Elimina dal filesystem il file aggiunto se l'operazione avviene correttamente
 *
 * @param int $p_event: tipo di operazione di callback
 * @param mixed array $p_header: descrittore del file
 */
	function postAdd($p_event, &$p_header)
	{
	global $CFG;
		if ($p_header['status']=='ok')
		{
			unlink($p_header['filename']);
		}
	}
	
	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}
	
	$Errore=0;
	$Credential=CheckCredential();
	
	if ($Credential)
	{		
	// Se trovo un archivio zip con lo stesso nome, lo elimino
		if (file_exists($_SESSION['OnlineId'] . '_data.zip'))
			unlink($_SESSION['OnlineId'] . '_data.zip');
			
	// creo l'archivio zip
		$archivio = new PclZip($_SESSION['OnlineId'] . '_data.zip');

	// prefisso dei files
		$prefix = $_SESSION['OnlineId'] . '_';
		
	//Creo la lista di file pdf che hanno il nome che inzia per $prefix_
		$list='';
		if ($handle=opendir($CFG->DOCUMENT_PATH . 'Tournament/TmpDownload/SendData/'))
		{
			while (($file=readdir($handle))!==false)
			{
				if (substr($file,0,strlen($prefix))==$prefix)
				{
					$list.=$CFG->DOCUMENT_PATH . 'Tournament/TmpDownload/SendData/' . $file . ',';
				}
			}
			$list=substr($list,0,-1);
			//print $list;exit;
		}
	
	// $x==0 se ci sono errori
	/**
	 * PCLZIP_OPT_REMOVE_ALL_PATH serve ad eliminare il path dai files
	 * PCLZIP_CB_POST_ADD imposta la chiamata callback a postAdd dopo ogni aggiunta di un file
	 */ 
		$x=$archivio->add($list,PCLZIP_OPT_REMOVE_ALL_PATH,PCLZIP_CB_POST_ADD, 'postAdd');
		if ($x==0)
			$Errore=1;
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