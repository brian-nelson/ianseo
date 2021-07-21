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
define ("ProgramVersion","2020-11-10"); // "Team Work"

define ("TargetNoPadding",3);		// Padding del targetno

define ("TieBreakArrows_Ind",3);	// Numero di frecce per il tiebreak dell'olympic round IND
define ("TieBreakArrowsSet_Ind",1);	// Numero di frecce per il tiebreak dell'olympic round IND a SET
define ("TieBreakArrows_Team",9);	// Numero di frecce per il tiebreak dell'olympic round TEAM
define ("TieBreakArrows_MixedTeam",6);	// Numero di frecce per il tiebreak dell'olympic round MIXED TEAM
define ("TieBreakArrowsSet_Team",3);// Numero di frecce per il tiebreak dell'olympic round TEAM a SET

define ("SetPoints6Arrows", 4);		// Numero di punti per vincere il match se si tirano 6 frecce
define ("SetPoints3Arrows", 6);		// Numero di punti per vincere il match se si tirano 3 frecce

define ("TeamStartPhase",16);		// Fase iniziale delle finali a squadre

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

// Upload System Version
define('UploadVersion', 3);

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
	if(isset($_REQUEST["SetLanguage"]) && preg_match("/^[A-Z-]{2,5}$/i",$_REQUEST["SetLanguage"]))
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
function get_text($text, $module='Common', $a=null, $translate=false, $force=false, $ForceLang='', $Verbose=true) {
	static $_LANG;
	global $Arr_StrStatus, $CFG;

	if(strlen($text)==0) {
		return '';
	}

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
		$result=$_LANG[$lingua][$module][$text];
		if(!is_null($a)) {
			if(is_scalar($a)) {
				$result=str_replace(array('{$a}','$a'), $a , $result);
			} elseif(is_object($a)) {
				foreach($a as $k=>$v) {
					$result=str_replace(array('{$a->'.$k.'}','$a->'.$k), $v , $result);
				}
			} elseif(is_array($a)) {
				foreach($a as $k=>$v) {
					$result=str_replace(array('{$a['.$k.']}','$a['.$k.']'), $v , $result);
				}
			}
		}
		return $result;
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
	$ConstToStore['MenuElimDo']=false;
	$ConstToStore['MenuElimPoolDo']=false;
	$ConstToStore['MenuFinIDo']=false;
	$ConstToStore['MenuFinTDo']=false;
	$ConstToStore['MenuElimOn']=false;
	$ConstToStore['MenuFinIOn']=false;
	$ConstToStore['MenuFinTOn']=false;
	$ConstToStore['MenuElim1']=array();
	$ConstToStore['MenuElim2']=array();
    $ConstToStore['MenuElimPool']=array();
	$ConstToStore['MenuFinI']=array();
	$ConstToStore['MenuFinT']=array();

    $q = safe_r_sql("select EvCode, EvTeamEvent, EvFinalFirstPhase,  EvShootOff, EvE1ShootOff, EvE2ShootOff, EvElimType, EvElim1, EvElim2
		from Events 
		where EvTournament={$_SESSION['TourId']} AND EvCodeParent=''");
	while($r=safe_fetch($q)) {
		$ConstToStore['MenuElimOn']=($ConstToStore['MenuElimOn'] or $r->EvE1ShootOff or $r->EvE2ShootOff);
        switch($r->EvElimType) {
            case 1:
            case 2:
				if((!$r->EvE1ShootOff and $r->EvElim1>0)) $ConstToStore['MenuElim1'][]=$r->EvCode;
				if((!$r->EvE2ShootOff and $r->EvElim2>0)) $ConstToStore['MenuElim2'][]=$r->EvCode;
				$ConstToStore['MenuElimDo']=true;
                break;
            case 3:
            case 4:
	            if((!$r->EvE2ShootOff and $r->EvElim2>0)) $ConstToStore['MenuElimPool'][]=$r->EvCode;
				$ConstToStore['MenuElimPoolDo']=true;
                break;
        }
        if ($r->EvTeamEvent == 1 and $r->EvFinalFirstPhase!=0) {
			$ConstToStore['MenuFinTDo']=true;
			$ConstToStore['MenuFinTOn']=($ConstToStore['MenuFinTOn'] or $r->EvShootOff);
			if(!$r->EvShootOff) $ConstToStore['MenuFinT'][]=$r->EvCode;
        } elseif ($r->EvTeamEvent == 0 and $r->EvFinalFirstPhase!=0) {
			$ConstToStore['MenuFinIDo']=true;
			$ConstToStore['MenuFinIOn']=($ConstToStore['MenuFinIOn'] or $r->EvShootOff);
			if(!$r->EvShootOff) $ConstToStore['MenuFinI'][]=$r->EvCode;
		}
	}

	if(count($ConstToStore['MenuElim1'])==0 && count($ConstToStore['MenuElim2'])==0 && count($ConstToStore['MenuElimPool'])==0 && $ConstToStore['MenuElimOn']==false)
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



/**
 * @param bool $PrintCrack if true prints a standard message
 * @param bool $popup
 * @return bool true if a competition is open
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

function OutputError($errore) {
	global $CFG;
	include('Common/Templates/head.php');
	echo '<div class="alert alert-warning">'.$errore.'</div>';
	include('Common/Templates/tail.php');
	exit;
}
/*
	- InfoTournament()
	Ritorna le info del torneo attivo prelevandole dalla sessione
*/
function InfoTournament()
{
	global $CFG, $INFO, $listACL;
// E' selezionato un torneo
	print '<table class="Tabella">';
	print '<tr style="height:34px">';
	print '<td>';
	if (CheckTourSession()) {
		print get_text('SelTour') . ': ' . $_SESSION['TourName'] . ' (' . $_SESSION['TourWhere'] . ' ' . get_text('From','Tournament') . ' ' . $_SESSION['TourWhenFrom'] . ' ' . get_text('To','Tournament') . ' ' . $_SESSION['TourWhenTo'] . ') - ' . $_SESSION['TourCode'] ;
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
	if(!empty($INFO->ACLReqfeatures)) {
        print '<td width="10%" id="securityBox">';
        print get_text('MenuLM_Lock manage').": <b>".($INFO->ACLEnabled ? get_text('CmdOn') : get_text('CmdOff')).'</b><br>';
        if($INFO->ACLReqlevel!=0) {
            foreach ($INFO->ACLReqfeatures as $feature) {
                print get_text($listACL[$feature], 'Tournament');
            }
            print '&nbsp;<b>' . ($INFO->ACLReqlevel == 1 ? 'r/o' : 'R/W'). '</b>';
        }
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
		$_SESSION['TourPrintLang']=$MyRow->ToPrintLang;
		$_SESSION['TourType']=$MyRow->ToType;
		$_SESSION['TourLocRule']=$MyRow->ToLocRule;
		$_SESSION['TourLocSubRule']=$MyRow->ToTypeSubRule;
		$_SESSION['TourField3D']=($MyRow->ToElabTeam==1 ? 'FIELD' : ($MyRow->ToElabTeam==2 ? '3D' : ''));
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
		$_SESSION['OnlineServices']=0;
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
	$_SESSION['OnlineServices']=0;

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
	foreach(glob($CFG->DOCUMENT_PATH.'Api/*/ApiConfig.php') as $dir) {
		if(basename(dirname($dir))!='ISK-Lite') {
			$ret[]=basename(dirname($dir));
		}
	}
	sort($ret);
	return $ret;
}

/**
 * @param array $color
 * @return boolean
 * given an array(R,G,B), returns if the background needs white color print or black color print!
 */
function IsDarkBackground($color=array(0,0,0)) {
	//////////// hexColor RGB
	$R1 = $color[0];
	$G1 = $color[1];
	$B1 = $color[2];

	//////////// Calc contrast ratio
	$L1 = 0.2126 * pow($R1 / 255, 2.2) +
		0.7152 * pow($G1 / 255, 2.2) +
		0.0722 * pow($B1 / 255, 2.2);

	$contrastRatio = (int)(($L1 + 0.05) / 0.05);

	return $contrastRatio <= 5;

	////////////// If contrast is more than 5, return black color
	//if ($contrastRatio > 5) {
	//	return 'black';
	//} else { //////////// if not, return white color.
	//	return 'white';
	//}
	//
	//// Setting the font color to black or white based on the perception of darkness of the background
	//$txt=($color[0]*0.21)+($color[1]*0.71)+($color[2]*0.08);
	//
	//return $txt<=85;
}

function GetIsParameter($ParameterName) {
	$TmpSql = "SELECT IsValue FROM InfoSystem WHERE IsId=" . StrSafe_DB($ParameterName);
	$Rs=safe_r_sql($TmpSql, false, true);
	if($Rs and $TmpRow = safe_fetch($Rs) and $TmpRow->IsValue) return unserialize($TmpRow->IsValue);
	return '';
}


/**
 * Outputs a JSON object/array
 * @param mixed $JSON data to be send
 * @param string $JsonP if false (default) sends the mime type "application/json" otherwise the name of the callback function and mime type is set to "application/javascript"
 * @param mixed $ExtraHeaders a single or an array of extra headers to send.
 * @param bool $Straight if true the $JSON object is already stringyfied
 */
function JsonOut($JSON, $JsonP=false, $ExtraHeaders=array(), $Straight=false) {
	if(!is_array($ExtraHeaders)) $ExtraHeaders=array($ExtraHeaders);

	$Answer=($Straight ? $JSON : json_encode($JSON));
    $Mime='application/json';

    if($JsonP and isset($_REQUEST[$JsonP]) and preg_match('/^[$A-Z_][0-9A-Z_.]*$/i',$_REQUEST[$JsonP])) {
	    $Answer = $_REQUEST[$JsonP] . '(' . $Answer . ");";
	    $Mime='application/javascript';
    }

    header('Access-Control-Allow-Origin: *');
	header('Cache-Control: no-store, no-cache, must-revalidate');
    foreach($ExtraHeaders as $h) {
    	header($h);
    }
	header('Content-type: '.$Mime .'; charset=UTF-8');
    header('Content-Length: '.strlen($Answer));
	echo $Answer;
	die();
}

function deleteArcher($EnId=0, $Division=false, $Limit=false) {
	$EnId=intval($EnId);
	if(!$EnId) return;

	if($Where=GetAccBoothEnWhere($EnId, $Division, $Limit)) {
		LogAccBoothQuerry("delete from Qualifications where QuId=(select EnId from Entries where $Where)");
		LogAccBoothQuerry("delete from AccEntries where AEId=(select EnId from Entries where $Where)");
		LogAccBoothQuerry("delete from Photos where PhEnId=(select EnId from Entries where $Where)");
		LogAccBoothQuerry("DELETE FROM ElabQualifications WHERE EqId=(select EnId from Entries where $Where)");
		LogAccBoothQuerry("DELETE FROM ExtraData WHERE EdId=(select EnId from Entries where $Where)");
		LogAccBoothQuerry("delete from Entries where $Where");
	}

	safe_w_sql("delete from Entries where EnTournament={$_SESSION['TourId']} and EnId=$EnId");
	if(safe_w_affected_rows()) {
		safe_w_sql("delete from AccEntries where AEId=$EnId");
		safe_w_sql("delete from Photos where PhEnId=$EnId");
		safe_w_sql("DELETE FROM Qualifications WHERE QuId=$EnId");
		safe_w_sql("DELETE FROM ElabQualifications WHERE EqId=$EnId");
		safe_w_sql("DELETE FROM ExtraData WHERE EdId=$EnId");
	}
}

/**
 * @param string $Type the type of log... Email, PDF, etc
 * @param string $Message the message to log
 * @param string $Title the title of the type (eg email title for type Email)
 * @param int $Entry the entry related to the log entry
 * @param int $TourId tour ID, defaults to the open session
 */
function insertLog($Type, $Message, $Title='', $Entry=0, $TourId=0) {
	safe_w_sql("insert ignore into Logs set 
		LogTournament=".(empty($TourId) ? $_SESSION['TourId'] : $TourId).",
		LogType=".StrSafe_DB($Type).",
		LogTitle=".StrSafe_DB($Title).",
		LogEntry=".StrSafe_DB($Entry).",
		LogMessage=".StrSafe_DB($Message).",
		LogTimestamp='".date('Y-m-d H:i:s.u')."',
		LogIP='".$_SERVER['REMOTE_ADDR']."'");
}
