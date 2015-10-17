<?php
	define ('debug',false);
// crea il manifest degli eventi

	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');
	
	//define ('URL_IANSEO_NET_DELETE','ianseo_net/Delete.php');
	define ('URL_IANSEO_NET_DELETE','http://www.ianseo.net/Delete.php');
	
	
	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}
	
	$Credential=CheckCredential();
	
// Errori dovuti alle credenziali o all'utilizzo non corretto della pagina
	$errorCode=0;
	$errorMsg=get_text('CmdOk');
	
// Errori durante l'esecuzione di questa pagina
	$Errore=0;
	
// Errori di curl
	$curlErrorCode=0;
	$curlErrorMsg=get_text('CmdOk');
	
	if ($Credential)
	{
	// sessione curl
		$ch = curl_init();
	// URL	
		curl_setopt($ch, CURLOPT_URL, URL_IANSEO_NET_DELETE);
	// POST	
		curl_setopt($ch, CURLOPT_POST, true );
	// vars da postare	
		$postVars['OnlineId']=$_SESSION['OnlineId'];
		$postVars['OnlineEventCode']=$_SESSION['OnlineEventCode'];
		$postVars['OnlineAuth']=$_SESSION['OnlineAuth'];
	
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
		
	/**
	 * specifico che la funzione curl_exec dovrà restituire l'output
	 * prodotto dall'URL contattato (destinatario.php)
	 * invece di inviarlo direttamente al browser
	 */
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$postResult = curl_exec($ch);
		
		if (curl_errno($ch)) 
		{
			$curlErrorCode=curl_errno($ch);
			$curlErrorMsg=curl_error($ch);
		}
		else
		{
			list($errorCode,$errorMsg)=explode('|',$postResult);
		}
		
	// Fine sessione curl
		curl_close($ch);
	}
	else
	{
		$Errore=1;
	}
	
	if (!debug)
		header('Content-Type: text/xml');
		
	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<error_code>' . $errorCode . '</error_code>' . "\n";
	print '<error_msg>' . $errorMsg . '</error_msg>' . "\n";
	print '<curl_error_code>' . $curlErrorCode . '</curl_error_code>' . "\n";
	print '<curl_error_msg>' . $curlErrorMsg . '</curl_error_msg>' . "\n";
	print '</response>' . "\n";
?>
