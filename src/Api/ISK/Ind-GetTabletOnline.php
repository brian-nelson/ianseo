<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$Img=$CFG->DOCUMENT_PATH.'Common/Images/status-couldshoot.gif';

if (CheckTourSession()) {
	$Img=$CFG->DOCUMENT_PATH.'Common/Images/status-noshoot.gif';
	$Select
		= "SELECT *
			FROM IskDevices where IskDvCode=".StrSafe_DB($_REQUEST['device']);
	$Rs=safe_r_sql($Select);
	if ($myRow=safe_fetch($Rs) and checkOnline($myRow->IskDvIpAddress)) {
		$Img=$CFG->DOCUMENT_PATH.'Common/Images/status-ok.gif';
	}

}



header('Content-Type: image/gif');
readfile($Img);

function checkOnline($IP) {
	unset($Output);
	unset($RetVal);
	$fp = @stream_socket_client("tcp://{$IP}:80", $errno, $errstr, 0.5);
	if (!$fp) {
		if($errno==111) {
			// connection REFUSED, so device is online
			return 1;
		}
	} else {
		fclose($fp);
		return 1;
	}
	return 0;
}