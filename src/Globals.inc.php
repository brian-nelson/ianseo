<?php
/*
													- Globals.inc.php -
	Contiene le variabili,le costanti simboliche e le funzioni globali a tutto il progetto e si preoccupa di eseguire
	uno script di inizializzazione.

	Questo SCript viene chiamato anche come svn:externals per il manuale
	perciò la variabile $newversion è stata spostata nel file ROOT/config.php


	---------------------------------------- Variabili e Funzioni Globali ---------------------------------------------
*/

// $newversion moved to file ROOT/config.php


define ("ProgramName","Ianseo");	// Nome del programma
define ("ProgramVersion","2017.01.01"); // "Don't just wish for a great 2017, MAKE IT SO!"

define ("TargetNoPadding",3);		// Padding del targetno

define ("TieBreakArrows_Ind",3);	// Numero di frecce per il tiebreak dell'olympic round IND
define ("TieBreakArrowsSet_Ind",1);	// Numero di frecce per il tiebreak dell'olympic round IND a SET
define ("TieBreakArrows_Team",9);	// Numero di frecce per il tiebreak dell'olympic round TEAM
define ("TieBreakArrows_MixedTeam",6);	// Numero di frecce per il tiebreak dell'olympic round MIXED TEAM
define ("TieBreakArrowsSet_Team",3);// Numero di frecce per il tiebreak dell'olympic round TEAM a SET

define ("SetPoints6Arrows", 4);		// Numero di punti per vincere il match se si tirano 6 frecce
define ("SetPoints3Arrows", 6);		// Numero di punti per vincere il match se si tirano 3 frecce

define ("TeamStartPhase",8);		// Fase iniziale delle finali a squadre

define ("BlockExperimental",false);	// settare a true per impedire l'uso delle pagine sperimentali

define ("MaxFinIndArrows",12);		// Num massimo di frecce x le finali individuali - Cumulative
define ("MaxFinIndArrowsSet_3",15);	// Num massimo di frecce x le finali individuali - 5 Set x 3 Frecce
define ("MaxFinIndArrowsSet_6",18);	// Num massimo di frecce x le finali individuali - 3 Set x 6 Frecce

define ("MaxFinTeamArrows",24);		// Num massimo di frecce x le finali team

define("verbose",false);			// Serve per visualizzare o meno gli errori nella lignua
									// settare a false per avere come fallback l'inglese

define("PageEncode", "UTF-8");		// spostato qui invece che come stringa "linguistica"

require_once('Common/BlockDefines.php');

// vincoli per le foto
define('MAX_WIDTH',300);
define('MAX_HEIGHT',400);
define('PROPORTION',400/300);
define('MAX_SIZE',50);	// kilobytes


/*****************************

Inserimento nuove funzioni di Chris per la gestione degli errori di DB

Definizione delle variabili e delle costanti utilizzate

******************************/
$safe_SQL=array(
	'w_connect'=>0,
	'r_connect'=>0,
	'w_querries'=>array(),
	'r_querries'=>array(),
	);

$WRIT_CON='';
$READ_CON='';
$GLOBALS['tempo']=getmicrotime();

/*

setta la variabile di debug che servirà anche per l'error_reporting
vedi anche dopo nello script di init, nella parte inizio di sessione
se è a false non farà MAI vedere il debug
se è a true farà vedere il debug solo se anche la sessione è true...

il trigger per far vedere il debug è chiamare la pagina aggiungendo ?ianseo-debug-session

*/
$ERROR_REPORT = true;

/*
	-------------------------------------------------- Script di init -------------------------------------------------
*/

session_start();

if(empty($_SESSION['WINHEIGHT'])) $_SESSION['WINHEIGHT'] = '';
if(empty($_SESSION['WINWIDTH']))  $_SESSION['WINWIDTH']  = '';
if(empty($_SESSION['COLLATION'])) set_collation();

/*
	Controllo della cache
*/
	header("Expires: Fri, 10 Jun 2005 19:26:00 GMT");    // Data passata
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");


//Impostazione del Coockie per il linguaggio
	if(isset($_REQUEST["SetLanguage"]) && preg_match("/^[A-Z]{2}(-[A-Z]{2})*$/i",$_REQUEST["SetLanguage"]))
	{
		$TmpLang=strtolower($_REQUEST["SetLanguage"]);
		if (!file_exists($CFG->LANGUAGE_PATH . $TmpLang . '/'.$TmpLang.'.txt')) {
			$TmpLang="en";
		}

		setcookie ("UseLanguage", strtoupper($TmpLang), time()+604800,'/');
		$_COOKIE["UseLanguage"]=strtoupper($TmpLang);
		set_collation(get_text('MySqlCollation'));
		header("Location: " . $_SERVER['PHP_SELF'] . go_get('SetLanguage','',true));
	}

// funzione per l'internazionalizzazione
function get_text($text, $module='Common', $a='', $translate=false, $force=false, $ForceLang='', $Verbose=true) {
	static $_LANG;
	global $Arr_StrStatus, $CFG;

	if($module=='ReturnLangArray') return $_LANG;

	if($ForceLang) {
		$lingua=strtolower($ForceLang);
	} else {
		$lingua=strtolower(SelectLanguage($force));
	}
	if(!$module) $module='Common';

	// ingloba anche la funzione langtr!
	if($translate){
		switch (substr($text,0,1))
		{
			case '~':	// Devo valutare la variabile contenuta nel nome
				$text=substr($text,1);
				if(substr($text,0,3)=="Str") $text=substr($text,3);
				if(substr($b=get_text($text, $module, $a), 0, 5)=='<b>[[') {
					return $text;
				} else {
					return $b;
				}
			case '|':	// Devo processare i pezzi di stringa
				$Tmp="";
				foreach (explode('|',substr($text,1)) as $Value)
				{
					$Tmp.= get_text($Value, $module, $a, true, $force); // rimanda al processore il pezzo estratto
				}
				return $Tmp;
			default:	// Devo stampare la stringa dura e pura!
				return $text;
		}
	}

	// per prima cosa carica i testi del modulo se non � presente
	if(!isset($_LANG[$lingua][$module])) {
		$_LANG[$lingua][$module]=array();
		if(!verbose){
			// carica il fallback (inglese), se esistente!
			if(file_exists($file=$CFG->LANGUAGE_PATH . "en/$module.php")) {
				include($file);
				$_LANG[$lingua][$module]=$lang;
			}
		}

		if(file_exists($file=$CFG->LANGUAGE_PATH . "$lingua/$module.php")) {
			include($file);
			$_LANG[$lingua][$module]=array_merge($_LANG[$lingua][$module], $lang);
		}

		// se il modulo è Common deve ricreare l'array degli status
		if($module=='Common') {
			$Arr_StrStatus = array
			(
				0 => '',
				1 => get_text('Status_1'),
				5 => get_text('Status_5'),
				6 => get_text('Status_6'),
				7 => get_text('Status_7'),
				8 => get_text('Status_8'),
				9 => get_text('Status_9')
			);
		}
	}

	if(isset($_LANG[$lingua][$module][$text])) {
		// se esiste il testo manda il testo con i parametri
		eval("\$result".' = "' . str_replace('"', '\"', $_LANG[$lingua][$module][$text]) . '";');
		return($result);
	} elseif($Verbose) {
		// oppure un avviso che manca il testo e il modulo
		return("<b>[[$text]@[$lingua]@[$module]]</b>");
	}
	return '';
}

function get_text_eval($text, $a='') {
	eval("\$result".' = "' . str_replace('"', '\"', $text) . '";');
	return($result);
}

/**
QUESTA FUNZIONE PROCEDE AD ASSEGNARE IN SESSIONE
gli eventi che ancora non sono stati spareggiati

Le variabili di sessione saranno usate poi in Menu php per creare il menu dinamico

**/
function set_qual_session_flags() {
	// se non c'è il tour... ritorna
	if(empty($_SESSION['TourId']) || $_SESSION['TourId']<=0) return;

	$ConstToStore=array();

	$q=safe_r_sql("select ToUseHHT, ToElimination, ToOptions from Tournament where ToId={$_SESSION['TourId']}");
	$r=safe_fetch($q);
	if(!empty($r->ToOptions)) $ConstToStore=unserialize($r->ToOptions);
	$ConstToStore['MenuHHT']=$r->ToUseHHT;
	$ConstToStore['MenuElimDo']=$r->ToElimination;
	$ConstToStore['MenuFinIDo']=false;
	$ConstToStore['MenuFinTDo']=false;
	$ConstToStore['MenuElimOn']=false;
	$ConstToStore['MenuFinIOn']=false;
	$ConstToStore['MenuFinTOn']=false;
	$ConstToStore['MenuElim1']=array();
	$ConstToStore['MenuElim2']=array();
	$ConstToStore['MenuFinI']=array();
	$ConstToStore['MenuFinT']=array();

	$q=safe_r_sql("select EvCode, EvTeamEvent, EvFinalFirstPhase, EvShootOff, EvE1ShootOff, EvE2ShootOff, EvElim1, EvElim2 from Events where EvTournament={$_SESSION['TourId']} AND EvFinalFirstPhase!=0 AND EvCodeParent=''");
	while($r=safe_fetch($q)) {
		if($ConstToStore['MenuElimDo']) {
			$ConstToStore['MenuElimOn']=($ConstToStore['MenuElimOn'] or $r->EvE1ShootOff or $r->EvE2ShootOff);
			if((!$r->EvE1ShootOff and $r->EvElim1>0)) $ConstToStore['MenuElim1'][]=$r->EvCode;
			if((!$r->EvE2ShootOff and $r->EvElim2>0)) $ConstToStore['MenuElim2'][]=$r->EvCode;
		}
		if($r->EvTeamEvent && $r->EvFinalFirstPhase) {
			$ConstToStore['MenuFinTDo']=true;
			$ConstToStore['MenuFinTOn']=($ConstToStore['MenuFinTOn'] or $r->EvShootOff);
			if(!$r->EvShootOff) $ConstToStore['MenuFinT'][]=$r->EvCode;
		} elseif(!$r->EvTeamEvent && $r->EvFinalFirstPhase) {
			$ConstToStore['MenuFinIDo']=true;
			$ConstToStore['MenuFinIOn']=($ConstToStore['MenuFinIOn'] or $r->EvShootOff);
			if(!$r->EvShootOff) $ConstToStore['MenuFinI'][]=$r->EvCode;
		}
	}

	if(count($ConstToStore['MenuElim1'])==0 && count($ConstToStore['MenuElim2'])==0 && $ConstToStore['MenuElimOn']==false)
		$ConstToStore['MenuElimDo']=false;

	safe_w_sql("update Tournament set ToOptions=".StrSafe_DB(serialize($ConstToStore))." where ToId={$_SESSION['TourId']}");

	define_session_flags($ConstToStore);
	return;
}

/**
 *
 * Questa funzione fa un define dei parametri settati dalla funzione precedente
 */
function define_session_flags($ConstToStore=array()) {
	if(empty($_SESSION['TourId']) || $_SESSION['TourId']<=0) return;
	if(!$ConstToStore) {
		$q=safe_r_sql("select ToId, ToOptions from Tournament where ToCode='{$_SESSION['TourCode']}'");
		if($r=safe_fetch($q)) {
			$_SESSION['TourId']=$r->ToId; // security check for tour upload while another session is open
			if(empty($r->ToOptions)) return;
			$ConstToStore=unserialize($r->ToOptions);
		}
	}
	foreach($ConstToStore as $k => $v) {
		$_SESSION[$k]=$v;
	}
}

/**
 *
 * Questa funzione aggiune un'opzione nel torneo => diventa variabile di sessione
 */
function Set_Tournament_Option($key, $value, $unset=false, $TourId=0) {
	if(!$TourId) {
		if(empty($_SESSION['TourId']) || $_SESSION['TourId']<=0) return;
		$TourId=$_SESSION['TourId'];
	}
	$ConstToStore=array();
	$q=safe_r_sql("select ToOptions from Tournament where ToId=$TourId");
	$r=safe_fetch($q);
	if(!empty($r->ToOptions)) $ConstToStore=unserialize($r->ToOptions);
	if($unset) {
		unset($ConstToStore[$key]);
	} else {
		$ConstToStore[$key]=$value;
	}
	safe_w_sql("update Tournament set ToOptions=".StrSafe_DB(serialize($ConstToStore))." where ToId=$TourId");
	define_session_flags($ConstToStore);
}



/*
	- CheckToutSession()
	Ritorna true se c'� una sessione di tornao attiva; false altrimenti
*/
function CheckTourSession($PrintCrack=false, $popup=false)
{
	global $CFG;
// E' selezionato un torneo
	if (isset($_SESSION['TourId']) && $_SESSION['TourId']>0 &&
		isset($_SESSION['TourName']) && strlen($_SESSION['TourName'])>0 &&
		isset($_SESSION['TourWhere']) && strlen($_SESSION['TourWhere'])>0 &&
		isset($_SESSION['TourWhenFrom']) && $_SESSION['TourWhenFrom']!='0000-00-00' &&
		isset($_SESSION['TourWhenTo']) && $_SESSION['TourWhenTo']!='0000-00-00')
		{

		if(!defined("TargetNoPadding")) define ("TargetNoPadding",3);

		return true;
	}
	elseif($PrintCrack) {
		PrintCrackError($popup);
	}
	else
		return false;
}

function PrintCrackError($popup=false, $errore='CrackError', $Module='Common', $a='') {
	global $CFG;
	include('Common/Templates/head'.($popup?'-popup':'').'.php');
	echo get_text($errore, $Module, $a);
	include('Common/Templates/tail'.($popup?'-popup':'').'.php');
	exit;
}
/*
	- InfoTournament()
	Ritorna le info del torneo attivo prelevandole dalla sessione
*/
function InfoTournament()
{
	global $CFG;
// E' selezionato un torneo
	print '<table class="Tabella">';
	print '<tr style="height:34px">';
	print '<td>';
	if (CheckTourSession())
	{
		print get_text('SelTour') . ': ' . $_SESSION['TourName'] . ' (' . $_SESSION['TourWhere'] . ' ' . get_text('From','Tournament') . ' ' . $_SESSION['TourWhenFrom'] . ' ' . get_text('To','Tournament') . ' ' . $_SESSION['TourWhenTo'] . ')';
	}
	else	// Non � selezionato nessun torneo
	{
		print get_text('NoTour','Tournament');
	}
	print '</td>';
	if($file=CheckHelp()) {
		print '<td width="5%" class="Center">';
		print '<a href="javascript:OpenPopup(\''.$CFG->ROOT_DIR.'Help.php?help='.$file.'\',\'Esegui\',800,500);">';
		print '<img onMouseOver="resizeImg(this, 150, \''.$CFG->ROOT_DIR.'Common/Images/help-30.png\')" onMouseOut="resizeImg(this)" border="0" src="'.$CFG->ROOT_DIR.'Common/Images/help.png" alt="Help" title="Help">';
		print '</a>';
		print '</td>';
	}
	print '<td width="5%" class="Center">';
	print '<a href="'.$CFG->ROOT_DIR.'credits.php">';
	print '<img onMouseOver="resizeImg(this, 150, \''.$CFG->ROOT_DIR.'Common/Images/ianseo_dot-30.png\')" onMouseOut="resizeImg(this)" border="0" src="'.$CFG->ROOT_DIR.'Common/Images/ianseo_dot.png" alt="Credits" title="Credits">';
	print '</a>';
	print '</td>';
	print '</tr>' . "\n";
	print '</table>' . "\n";
}

/**
 * Crea una sessione del torneo
 * @param $TourId: id del torneo
 * @return true se ci riesce e false altrimenti
 */
function CreateTourSession($TourId) {
	require_once('Common/CheckPictures.php');
	$Select
		= "SELECT"
		. " Tournament.*"
		. ", UNIX_TIMESTAMP(ToWhenFrom) AS ToWhenFromUTS"
		. ", DATE_FORMAT(ToWhenFrom,'" . get_text('DateFmtDB') . "') AS DtFrom"
		. ", UNIX_TIMESTAMP(ToWhenTo) AS ToWhenToUTS"
		. ", DATE_FORMAT(ToWhenTo,'" . get_text('DateFmtDB') . "') AS DtTo"
		. ", ToTypeName AS TtName"
		. ", ToElimination AS TtElimination "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($TourId);
	//print $Select;
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1)
	{
		$debug = $_SESSION['debug'];
		$debmode= (!empty($_SESSION['debug-mode']) ? $_SESSION['debug-mode'] : '');

		$_SESSION=Array();

		$_SESSION['debug'] = $debug;
		$_SESSION['debug-mode'] = $debmode;

		$MyRow=safe_fetch($Rs);
		$_SESSION['TourId']=$MyRow->ToId;
		$_SESSION['TourType']=$MyRow->ToType;
		$_SESSION['TourPrintLang']=$MyRow->ToPrintLang;
		$_SESSION['TourLocRule']=$MyRow->ToLocRule;
		$_SESSION['TourCode']=$MyRow->ToCode;
		$_SESSION['TourCodeSafe']=preg_replace('/[^a-z0-9_.-]+/sim', '', $MyRow->ToCode);
		$_SESSION['TourCollation']=$MyRow->ToCollation;
		$_SESSION['TourName']=$MyRow->ToName;
		$_SESSION['TourWhere']=$MyRow->ToWhere;
		$_SESSION['TourRealWhenFrom']=$MyRow->ToWhenFrom;
		$_SESSION['TourRealWhenTo']=$MyRow->ToWhenTo;
		$_SESSION['TourWhenFrom']=$MyRow->DtFrom;
		$_SESSION['TourWhenTo']=$MyRow->DtTo;
		$_SESSION['ToWhenFromUTS']=$MyRow->ToWhenFromUTS;
		$_SESSION['ToWhenToUTS']=$MyRow->ToWhenToUTS;
		$_SESSION['ToPaper']=$MyRow->ToPrintPaper;
	// parametri per le credenziali di upload verso ianseo.net
		$_SESSION['OnlineId']=0;
		$_SESSION['OnlineEventCode']=0;
		$_SESSION['OnlineAuth']=0;
	//Parametro per il Padding dei paglioni
		$_SESSION['TargetPadding']=2;

		// sets the collation for the tournament
		set_collation($MyRow->ToCollation);
		// if a collation is set for a tournament, this will be the default whatever language is chosen
		$_SESSION['COLLATION-LOCK']=($MyRow->ToCollation!='');

		// Defines if a tournament is ORIS compliant or not
		$_SESSION['ISORIS']=$MyRow->ToIsORIS;

		// check if the accreditation is for multiple competitions...
		$_SESSION['AccreditationTourIds']='';
		if(GetParameter('AccActive')) {
			$ids=array();
			foreach(explode(',', GetParameter('AccCompetitions')) as $tour) {
				$ids[]=getIdFromCode($tour);
			}
			// only if the opened competition is one of the ones to be multi-accredited!
			if(in_array($MyRow->ToId, $ids)) $_SESSION['AccreditationTourIds']=implode(',', $ids);
		}

		// check if it is an Accreditation Booth...
		$_SESSION['AccBooth']='';
		if(GetParameter('AccBoothActive') and in_array($MyRow->ToCode, $codes=explode(', ', GetParameter('AccBoothCodes')))) {
			$_SESSION['AccBooth']='1';
			$ids=array();
			foreach($codes as $tour) {
				$ids[]=getIdFromCode($tour);
			}
			$_SESSION['AccreditationTourIds']=implode(',', $ids);
		}



		$q="
			SELECT IFNULL(MAX(SesTar4Session),0) AS max_session
			FROM
				Session
			WHERE
				SesTournament={$MyRow->ToId}
		";
		$t=safe_r_sql($q);

		if($u=safe_fetch($t)) {
			if($u->max_session>=100) {
				$_SESSION['TargetPadding']=3;
			} else {
				$_SESSION['TargetPadding']=2;
			}
		}

		$_SESSION['ClickMenu']=GetParameter('OnClickMenu');

		// parametri per gli spareggi... e i menu
		set_qual_session_flags();

		RedrawPictures();
		return $MyRow;
	}

	return false;

}

/*
	- EraseTourSession()
	Distrugge la sessione per il torneo attivo
*/
function EraseTourSession() {
	$_SESSION=array();
	$_SESSION['TourId']=-1;
	$_SESSION['TourCode']='';
	$_SESSION['TourName']='';
	$_SESSION['TourWhere']='';
	$_SESSION['TourWhenFrom']='0000-00-00';
	$_SESSION['TourWhenTo']='0000-00-00';

	$_SESSION['OnlineId']=0;
	$_SESSION['OnlineEventCode']=0;
	$_SESSION['OnlineAuth']=0;

	if (isset($_SESSION['AccOp']))
		unset($_SESSION['AccOp']);
	if (isset($_SESSION['SetRap']))
		unset($_SESSION['SetRap']);
	if (isset($_SESSION['ToWhenFromUTS']))
		unset($_SESSION['ToWhenFromUTS']);
	if (isset($_SESSION['ToWhenToUTS']))
		unset($_SESSION['ToWhenToUTS']);
}

/*
	- VerificaDati(&$DataArray)
	Riceve un vettore nella forma
	array
	(
		<Chiave> => array('Func' => <nome funzione>,'Error => <true/false> [,'Value' => '<valore da analizzare>])
	)

	Serve a verificare la correttezza dei parametri passati ad una pagina quando questi vengono scritti nel db.
	Se <Chiave> inizia con 'd_' allora l'elemento deve essere nella forma array('Func' => <nome funzione>,'Error => <true/false>).
	Il campo 'Func' contiene il nome della funzione usata per analizzare il dato: questa ricever� il valore di $_REQUEST[<Chiave>] e
	avr� un parametro di ritorno che conterr� true o false a seconda se c'� o no l'errore.
	Se  <Chiave> inizia con 'x_' allora l'elemento sar� nella forma array('Func' => <nome funzione>,'Error => <true/false>,'Value' => '<valore da analizzare>)
	doev l'elemento 'Value' conterr� il valore da analizzare.

	Ritorna il numero di errori riscontrati.
*/
function VerificaDati(&$DataArray)
{
	$NumErr=0;	// Numero di errori

	foreach ($DataArray as $Key => $Value)
	{
		$TipoPar = substr($Key,0,2);

		if ($TipoPar=='d_' || $TipoPar=='x_')
		{
			$R = false;		// Stato dell'errore

			if ($TipoPar=='d_')
			{
			// Verifico che la var sia settata
				$R=(isset($_REQUEST[$Key])!=true);
			}

			$DataArray[$Key]['Error']=$R;

			if ($DataArray[$Key]['Error']==false)
			{
				$R = call_user_func($DataArray[$Key]['Func'], ($TipoPar=='d_' ? $_REQUEST[$Key] : $DataArray[$Key]['Value']));
				$DataArray[$Key]['Error']=(!$R);
			}

			if ($DataArray[$Key]['Error'])
				++$NumErr;
		}
	}

	return $NumErr;
}

/*
	- CheckBlocked($Bit)
	Verifica se la sezione è stata bloccata oppure no.
	Riceve $Bit il bit che rappresenta la sezione
	Ritorna true se la sezione è bloccata oppure false altrimenti
*/
function IsBlocked($Bit) {
	if(!isset($_SESSION['TourId'])) return false;

	$Query = "SELECT (ToBlock & $Bit) Blocked "
			. "FROM "
				. "Tournament "
			. "WHERE "
				. "ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_r_sql($Query);

	if ($r=safe_fetch($Rs) and $r->Blocked) return true; // tournament is blocked!

	return false;

	// tournament is open... check if the user has the permission
/*
	// users on localhost are ALWAYS granted
	if($_SERVER['REMOTE_ADDR']=='127.0.0.1') return false;

	$q=safe_r_sql("select (AclAccess & $Bit) granted from ACL where AclIp=".StrSafe_DB($_SERVER['REMOTE_ADDR'])." and AclTournament={$_SESSION['TourId']}");
	if(!($r=safe_fetch($q)) or $r->granted) return false; // no lines in DB or user has access

	// USER IS BLOCKED!
	return true;
	*/
}

/**
 * Ritorna l'id del torneo dato il codice gara $code
 *
 * @param string $code: codice gara
 *
 * @return int: id torneo se esiste; 0 altrimenti
 */
function getIdFromCode($code, $ForceLang=false)
{
	$ret=0;

	$query
		= "SELECT ToId, ToPrintLang FROM Tournament WHERE ToCode=" . StrSafe_DB($code) . " ";
	$rs=safe_r_sql($query);
	if (safe_num_rows($rs)==1)
	{
		$row=safe_fetch($rs);
		$ret=$row->ToId;
		if($row->ToPrintLang and !defined('PRINTLANG')) define('PRINTLANG', $row->ToPrintLang);
	}

	return $ret;
}

/**
 * Returns Tournament Code knowing the ID
 *
 * @param string $code: codice gara
 *
 * @return int: id torneo se esiste; 0 altrimenti
 */
function getCodeFromId($id)
{
	if($id==-1) return 'BaseIanseo';
	$ret=0;

	$query
		= "SELECT ToCode FROM Tournament WHERE ToId=" . StrSafe_DB($id) . " ";
	$rs=safe_r_sql($query);
	if (safe_num_rows($rs)==1)
	{
		$row=safe_fetch($rs);
		$ret=$row->ToCode;
	}

	return $ret;
}

/**
 * Ritorna il tipo del torneo selezionato
 *
 * @return int: tipo del torneo se esiste; 0 altrimenti
 */
function getTournamentType($TourId='')
{
	$ret=0;

	$query
		= "SELECT ToType FROM Tournament WHERE ToId=" . StrSafe_DB($TourId?$TourId:$_SESSION['TourId']) . " ";
	$rs=safe_r_sql($query);
	if (safe_num_rows($rs)==1)
	{
		$row=safe_fetch($rs);
		$ret=$row->ToType;
	}

	return $ret;
}

/**
 * Ritorna la categoria del torneo oppure -1 in caso di errori
 * @param int $Id: id del torneo
 * @return int
 */
	function GetCategory($Id)
	{
		global $CFG;
		/*$Query
			= "SELECT TtCategory "
			. "FROM "
				. "Tournament*Type "
				. "INNER JOIN "
					. "Tournament "
				. "ON TtId=ToType "
			. "WHERE "
				. "ToId=" . StrSafe_DB($Id) . " ";*/
		$Query
			= "SELECT ToCategory AS TtCategory "
			. "FROM "
				. "Tournament "
			. "WHERE "
				. "ToId=" . StrSafe_DB($Id) . " ";
		$Rs=safe_r_sql($Query);
		$Cat=-1;

		if ($Rs && safe_num_rows($Rs)==1)
		{
			$Row=safe_fetch($Rs);
			$Cat=$Row->TtCategory;
		}
		return $Cat;

	}

/*
	- SelectLanguage()
	La funzione serve a settare il linguaggio giusto.
	Verifica la presenza del cookie e se c'� usa quelle impostazioni;
	se � settato il flag di lingua usa quello;
	se il file del linguaggio esiste lo usa.
	se non trova nulla usa l'italiano
*/
function SelectLanguage($force=false)
{
	global $CFG;
	$TmpLang='en';

	//for printouts, checks if the constant is defined
	if(!$force and defined('PRINTLANG') and PRINTLANG > '' ) {
		$TmpLang = PRINTLANG;

	// if not, check if a lang has been sent
	} elseif(isset($_REQUEST["Lang"])
		&& preg_match("/^[a-z-]+$/i",$_REQUEST["Lang"])
		&& file_exists($CFG->LANGUAGE_PATH . $_REQUEST["Lang"] . '/'.$_REQUEST["Lang"].'.txt')) {
		$TmpLang=strtolower($_REQUEST["Lang"]);

	//Check Cookie
	} elseif(isset($_COOKIE["UseLanguage"])) {
		$TmpLang=strtolower($_COOKIE["UseLanguage"]);

	// else check the browser for a hint
	} elseif(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		// it-it,it;q=0.8,en-us;q=0.5,en;q=0.3
		$langs=explode(',', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
		foreach($langs as $lang) {
			$l=explode(';', $lang);
			if(file_exists($CFG->LANGUAGE_PATH . $l[0] . '/'.$l[0].'.txt')) {
				$TmpLang=$l[0];
				break;
			}
		}
	}
	//Chech if file exists and include it
	if (!file_exists($CFG->LANGUAGE_PATH . $TmpLang . '/'.$TmpLang.'.txt')) {
		$TmpLang="en";
	}

	return $TmpLang;
}

function CheckHelp() {
	global $CFG;
	$helpfile=str_replace('/','-',substr($_SERVER['SCRIPT_NAME'], strlen($CFG->ROOT_DIR)));
	if(file_exists($CFG->DOCUMENT_PATH . 'Common/Help/' . $helpfile)) {
		return $helpfile.($_SERVER['QUERY_STRING'] ? '&'.$_SERVER['QUERY_STRING'] : '');
	} else {
		return '';
	}
}

function set_collation($Col='') {
	static $Collations=array(
		'czech',
		'danish',
		'esperanto',
		'estonian',
		'general',
		'hungarian',
		'icelandic',
		'latvian',
		'lithuanian',
		'persian',
		'polish',
		'roman',
		'romanian',
		'slovak',
		'slovenian',
		'spanish2',
		'spanish',
		'swedish',
		'turkish'
		);

	if(!$Col) $Col=get_text('MySqlCollation');
	if(empty($_SESSION['COLLATION'])) $_SESSION['COLLATION'] = 'utf8_general_ci';
	if(empty($_SESSION['COLLATION-LOCK'])) $_SESSION['COLLATION-LOCK'] = false;
	if($Col and !$_SESSION['COLLATION-LOCK'] and in_array($Col, $Collations)) $_SESSION['COLLATION'] = "utf8_{$Col}_ci";
}

/**
 * CheckLastSWUpdate()
 * Controlla se è il caso di controllare gli aggiornamenti.
 *
 * @param int $days: intervallo di tempo in giorni
 * @return bool: true se è passato più tempo di $days e false altrimenti.
 */
function CheckLastSWUpdate($days=10)
{
	$ret=false;

/*
 * Se oggi meno l'intervallo scelto è una data maggiore di quella di ultimo controllo
 * degli update, allora sarebbe il caso di fare un altro controllo
 */
	$q="SELECT IF(DATE_SUB('".date('Y-m-d H:i:s')."', INTERVAL {$days} DAY)>ParValue,1,0) AS MustCheck FROM Parameters WHERE ParId='ChkUp' ";
	//print $q;exit;
	$r=safe_r_sql($q);

	if ($r && safe_num_rows($r)==1)
	{
		$row=safe_fetch($r);
		$ret=$row->MustCheck==1;
	}

	return $ret;
}


/**
 *
 *
 * NEEDS TO BE the file
 *
 * **/
function GetWebDirectory($file) {
	global $CFG;
	return $CFG->ROOT_DIR.dirname(str_replace(array($CFG->DOCUMENT_PATH, DIRECTORY_SEPARATOR), array('','/'), $file));
}

/**
 * AvailableApis()
 * Check all availables APIS.
 *
 * @return array: an array of available Apis, false otherwise.
*/
function AvailableApis() {
	global $CFG;
	$ret=array();
	if(ProgramRelease=='FITARCO') return $ret;
	foreach(glob($CFG->DOCUMENT_PATH.'Api/*/index.php') as $dir) {
		if(basename(dirname($dir))!='ISK-Lite') {
			$ret[]=basename(dirname($dir));
		}
	}
	return $ret;
}

/**
 * @param array $color
 * @return boolean
 * given an array(R,G,B), returns if the background needs white color print or black color print!
 */
function IsDarkBackground($color=array(0,0,0)) {
	// Setting the font color to black or white based on the perception of darkness of the background
	$txt=($color[0]*0.21)+($color[1]*0.71)+($color[2]*0.08);

	return $txt<=85;
}

function GetIsParameter($ParameterName) {
	$TmpSql = "SELECT IsValue FROM InfoSystem WHERE IsId=" . StrSafe_DB($ParameterName);
	$Rs=safe_r_sql($TmpSql, false, true);
	if($Rs and $TmpRow = safe_fetch($Rs) and $TmpRow->IsValue) return unserialize($TmpRow->IsValue);
	return '';
}

function JsonOut($JSON) {
	header('Content-type: application/javascript');
	echo json_encode($JSON);
	die();
}