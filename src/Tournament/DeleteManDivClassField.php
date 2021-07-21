<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

define('debug',false);

require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error'=>1,'which'=>'#','tab'=>'');

/*
	- $Arr_Tables
	Array di lookup per le tabelle.
	Alla chiave corrisponde un vettore formato da:
		La tabella, i 2 campi di questa da usare come chiave per la delete
*/
$Arr_Tables = array(
	'D' => array('Divisions','DivId','DivTournament', 'Div_'),
	'C' => array('Classes','ClId','ClTournament', 'Cl_'),
	'SC'=> array('SubClass','ScId','ScTournament', 'SubClass_')
	);

if(checkACL(AclCompetition, AclReadWrite, false)!=AclReadWrite
		or !CheckTourSession()
		or empty($_REQUEST['Tab'])
		or empty($_REQUEST['Id'])
		or empty($Arr_Tables[$_REQUEST['Tab']])
		or IsBlocked(BIT_BLOCK_TOURDATA)
		or defined('dontEditClassDiv')
		) {
	JsonOut($JSON);
}

$tt=$Arr_Tables[$_REQUEST['Tab']][0];	// tabella su cui fare l'update
$kk=$Arr_Tables[$_REQUEST['Tab']][1];	// campo 1 da usare come chiave per l'update
$kk2=$Arr_Tables[$_REQUEST['Tab']][2];	// campo 2 da usare come chiave per l'update

$Delete
	= "DELETE FROM " . $tt . " "
	. "WHERE " . $kk . "="  .StrSafe_DB($_REQUEST['Id']) . " AND " . $kk2 . "=" . StrSafe_DB($_SESSION['TourId']) . " ";
$Rs=safe_w_sql($Delete);

$JSON['which']=$Arr_Tables[$_REQUEST['Tab']][3].$_REQUEST['Id'];
$JSON['error']=0;
$JSON['tab']=$_REQUEST['Tab'];

JsonOut($JSON);
