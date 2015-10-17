<?php
	define('INSTALL', true);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	// non si sa mai, ma il $CFG potrebbe essere vuoto!!!
	// quindi ricostruisco il $CFG
	$CFG->ROOT_DIR = substr($_SERVER['SCRIPT_NAME'], 0, strlen(dirname(dirname(__FILE__))) + strlen($_SERVER['SCRIPT_NAME']) - strlen(realpath($_SERVER['SCRIPT_FILENAME']))) . '/';


/**

La procdura di installazione è suddivisa in numerosi STEPS

* STEP 1: check della configurazione corrente del PHP
  - memoria assegnata
  - tempo di esecuzione
  - peso dei file del post
  - peso massimo del singolo file
  - modulo mysqli
  - moduli CURL
  - moduli gd
  - moduli imagemagick
  - presentazione dello snippet ianseo.ini da mettere nella dir di
    configurazione del php con i dati che servono (memory, eccetera)

* STEP 2: check della configurazione mysql
  - se esiste già una connessione lo script fa un dump di
    tutte le gare + un mysqldump --opt del DB di scrittura
  - visualizzare i campi per la connession ai 2 DB (lettura e scrittura),
    e relative password di root qualora dovessero essere creati gli utenti

* STEP 3: Creazione DB "sano"
  - import del DB di release tramite una SQL import
  - aggiornamento del DB via "update_db.php" alla patch odierna (tramite un reload della pagina)

* STEP 4: scrittura impostazioni
  - scrivere il file "Common/config.inc.php"

**/

$STEP=1;
if(!empty($_REQUEST['step'])) $STEP=max(0,min(4,intval($_REQUEST['step'])));

// prepara i dati da richiedere
if(empty($CFG->W_HOST)) $CFG->W_HOST='localhost';
if(empty($CFG->R_HOST)) $CFG->R_HOST='';
if(empty($CFG->W_USER)) $CFG->W_USER='ianseo';
if(empty($CFG->R_USER)) $CFG->R_USER='';
if(empty($CFG->W_PASS)) $CFG->W_PASS='ianseo';
if(empty($CFG->R_PASS)) $CFG->R_PASS='';
if(empty($CFG->DB_NAME)) $CFG->DB_NAME= 'ianseo';
if(empty($_SESSION['INSTALL']['CFG'])) $_SESSION['INSTALL']['CFG']=array(
		'W_HOST'=>$CFG->W_HOST,
		'R_HOST'=>$CFG->R_HOST,
		'W_USER'=>$CFG->W_USER,
		'R_USER'=>$CFG->R_USER,
		'W_PASS'=>$CFG->W_PASS,
		'R_PASS'=>$CFG->R_PASS,
		'DB_NAME'=>$CFG->DB_NAME,
		'ERROR'=>'',
	);


if($_POST) @include('install-'.$STEP.'-post.php');

// devo fare il check iniziale se posso creare/scrivere nella directory dei DUMP!!!
if(!file_exists('./dbdumps')) {
	if(!mkdir(dirname(__FILE__).'/dbdumps',0777)) {
		$_SESSION['INSTALL']['CFG']['ERROR']=get_text('File permission error','Install');
		$STEP=0;
	}
}


	include('Common/Templates/head.php');
?>
<center>
<form method="POST">
<h2><?php echo get_text('Install-'.$STEP.' Title', 'Install') ?></h2>
<table class="Tabella" style="width:auto">
<?php

if(!empty($_SESSION['INSTALL']['CFG']['ERROR'])) {
	echo '<tr><td colspan="3" class="Warning red">'.$_SESSION['INSTALL']['CFG']['ERROR'].'</td></tr>';
	echo '<tr class="Divider"><td colspan="3"></td></tr>';
}

if($STEP) include('install-'.$STEP.'.php');

?>
</table>
</form>
</center>
<?php
	include('Common/Templates/tail.php');

/** ********* *************   ******************* */

function install_blank_db() {
	$SQL=file('./install.sql');
	$query='';
	foreach($SQL as $riga) {
		if(substr($riga,0,2)=='--') continue;
		if(trim($riga)) $query .= trim($riga);
		if($query AND substr($query, -1)==';') {
			safe_w_sql($query);
			$query='';
		}
	}
	if($query AND substr($query, -1)==';') safe_w_sql($query);
}


?>