<?php
/*
													- UpdateTargetNo.php -
	La pagina aggiorna il TargetNo del tizio in Qualifications se la sessione è settata
*/

define('debug',false);

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_Sessions.inc.php');

$JSON=array('error'=>1,'val'=>'');

if (!CheckTourSession() or checkACL(AclEliminations, AclReadWrite, false)!=AclReadWrite or IsBlocked(BIT_BLOCK_ELIM)) {
	JsonOut($JSON);
}

foreach ($_REQUEST as $Key => $Value) {
	if (substr($Key,0,2)!='d_') {
		JsonOut($JSON);
	}

	list( , , , $Fase, $Evento) = explode('_',$Key);
	$Id=$Fase.'_'.$Evento;
	$Ses=$Value;

	$sessions=GetSessions('E');
	$trovato=($Ses==0);
	foreach ($sessions as $s) {
		if ($s->SesOrder==$Ses) {
			$trovato=true;
			break;
		}
	}

	if (!$trovato) {
		JsonOut($JSON);
	}

	$Update = "UPDATE Eliminations SET ElSession=" . StrSafe_DB($Ses) . " WHERE ElElimPhase=" . $Fase . " AND ElEventCode='".$Evento."' AND ElTournament=". $_SESSION['TourId'];
	$RsUp=safe_w_sql($Update);

	$JSON['val']=$Ses;
}

$JSON['error']=0;

JsonOut($JSON);
