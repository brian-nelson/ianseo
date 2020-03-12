<?php

/**

* STEP 2: check della configurazione mysql
  - se esiste già una connessione lo script fa un dump di
    tutte le gare + un mysqldump --opt del DB di scrittura
  - visualizzare i campi per la connession ai 2 DB (lettura e scrittura),
    e relative password di root qualora dovessero essere creati gli utenti


*/

if(!empty($_POST['W_HOST']) and !empty($_POST['W_USER']) and !empty($_POST['W_PASS']) and !empty($_POST['DB_NAME'])) {
	/**
		se è stata fornita la password di root, bisogna creare l'utente e il database
		prima però si fa un'esportazione delle gare eventualmente presenti nel "vecchio" database
		nonché il dump del DB stesso via mysqldump...
		questi files verranno salvati per sicurezza in una dir unica all'interno di Install
		al termine dell'installazione verrà presentata all'utente la pagina con l'elenco dei file salvati
	**/

	// sanitizza il nome dell'host
	$W_HOST = trim(preg_replace("/[^a-z0-9_.-]/sim", '', $_REQUEST['W_HOST']));
	$R_HOST = trim(preg_replace("/[^a-z0-9_.-]/sim", '', $_REQUEST['R_HOST']));
	$W_USER = trim(preg_replace("/[^a-z0-9_.-]/sim", '', $_REQUEST['W_USER']));
	$R_USER = trim(preg_replace("/[^a-z0-9_.-]/sim", '', $_REQUEST['R_USER']));
	$W_PASS = stripslashes(trim($_REQUEST['W_PASS']));
	$R_PASS = stripslashes(trim($_REQUEST['R_PASS']));
	$DB_NAME= trim(preg_replace("/[^a-z0-9_.-]/sim", '', $_REQUEST['DB_NAME']));

	if(empty($R_HOST)) $R_HOST=$W_HOST;
	if(empty($R_USER)) $R_USER=$W_USER;
	if(empty($R_PASS)) $R_PASS=$W_PASS;
	if($R_HOST==$W_HOST) {
		$R_USER=$W_USER;
		$R_PASS=$W_PASS;
	}

	// i dati vengono messi in sessione
	$_SESSION['INSTALL']['CFG']= array(
		'W_HOST'=> $W_HOST,
		'R_HOST'=> $R_HOST,
		'W_USER'=> $W_USER,
		'R_USER'=> $R_USER,
		'W_PASS'=> $W_PASS,
		'R_PASS'=> $R_PASS,
		'DB_NAME'=>$DB_NAME,
		'ERROR'=>'',
		);

	// per proseguire TUTTI i dati devono a questo punto essere completi
	if($W_HOST and $R_HOST and $W_USER and $R_USER and $W_PASS and $R_PASS and $DB_NAME) {
		// tenta la connessione al DB
		$CFG->W_HOST =$W_HOST;
		$CFG->R_HOST =$R_HOST;
		$CFG->W_USER =$W_USER;
		$CFG->R_USER =$R_USER;
		$CFG->W_PASS =$W_PASS;
		$CFG->R_PASS =$R_PASS;
		$CFG->DB_NAME=$DB_NAME;


		// Inizia con il DB di scrittura
		if(check_write_DB('W', $W_HOST, $W_USER, $W_PASS)) {
			// il DB è già esistente, quindi si deve fare il dump delle cose
			// va fatto SOLO per il writ, dato che il read se c'è è una replica e quindi
			// contiene gli stessi dati!
			// per ora faremo solo un dump completo del DB
			// che verrà salvato come data-ora/ianseodump.sql
			$working_dir = dirname($_SERVER['SCRIPT_FILENAME']) . "/dbdumps/" . date('Ymd-His');
			mkdir($working_dir, 0777);
			$SQLfilename = $working_dir . "/ianseodump.sql";
			exec("mysqldump -Q -h'$W_HOST' -u'".str_replace("'", "\'", $W_USER)."' -p'".str_replace("'", "\'", $W_PASS)."' --opt $DB_NAME > $SQLfilename", $error_lines);

			// per ogni gara fanne l'esportazione nei dump e la cancella dal DB
			include('Common/Fun_Export.php');
			include('Common/Fun_TourDelete.php');

			$CompToReload=array();
			//controlla che ci sia la tabella Tournament
			$q=safe_r_sql("show tables like 'Tournament'");
			if(safe_num_rows($q)) {
				$q=safe_w_sql("select * from Tournament");
				while($r=safe_fetch($q)) {
					$Gara=export_tournament($r->ToId);
					$filename=$working_dir . "/{$Gara['Tournament']['ToCode']}.ianseo";
					$f=fopen($filename,'w');
					fwrite($f,gzcompress(serialize($Gara),9));
					fclose($f);

					$CompToReload[]=$filename;
					tour_delete($r->ToId);
				}

				// per sicurezza fai un secondo dump del DB svuotato...
				$SQLfilename = $working_dir . "/ianseodump-vuoto.sql";
				exec("mysqldump -Q -h'$W_HOST' -u'".str_replace("'", "\'", $W_USER)."' -p'".str_replace("'", "\'", $W_PASS)."' --opt $DB_NAME > $SQLfilename", $error_lines);

				// zappo la tabella LookUpEntries
				safe_w_sql("truncate table LookUpEntries");
				$SQLfilename = $working_dir . "/ianseodump-base.sql";
				exec("mysqldump -Q -h'$W_HOST' -u'".str_replace("'", "\'", $W_USER)."' -p'".str_replace("'", "\'", $W_PASS)."' --opt $DB_NAME > $SQLfilename", $error_lines);
			}

			// svuota il database
			$q=safe_w_sql("show tables");
			while($r=safe_fetch($q)) {
				safe_w_sql("drop table if exists ".$r->{'Tables_in_' . $DB_NAME});
			}

			// carica il database "sano"
			install_blank_db();

			// reimporta tutte le gare salvate precedentemente
			foreach($CompToReload as $reload) {
				tour_import($reload);
			}
		}

		// esegue il check anche sul DB di lettura solo se sono diversi gli host
		if($W_HOST!=$R_HOST) {
			$CFG->W_HOST =$R_HOST;
			$CFG->W_USER =$R_USER;
			$CFG->W_PASS =$R_PASS;
			check_write_DB('R', $R_HOST, $R_USER, $R_PASS);
		}

		// a questo punto non resta che scrivere il nuovo file Common/config.inc.php
		/**

		Questi 2 parametri invece NON possono essere modificati via script ma solo per directory (.htaccess ? )

		ini_set('post_max_size','32M');
		ini_set('upload_max_filesize','23K');

		**/
		$config = '';
		$config.= '<?php'."\n";
		$config.= '// settings for the READ server'."\n";
		$config.= '$CFG->R_HOST = \''.str_replace("'", "\'", $R_HOST).'\';'."\n";
		$config.= '$CFG->R_USER = \''.str_replace("'", "\'", $R_USER).'\';'."\n";
		$config.= '$CFG->R_PASS = \''.str_replace("'", "\'", $R_PASS).'\';'."\n";
		$config.= ''."\n";
		$config.= '// settings for the WRITE Server'."\n";
		$config.= '$CFG->W_HOST = \''.str_replace("'", "\'", $W_HOST).'\';'."\n";
		$config.= '$CFG->W_USER = \''.str_replace("'", "\'", $W_USER).'\';'."\n";
		$config.= '$CFG->W_PASS = \''.str_replace("'", "\'", $W_PASS).'\';'."\n";
		$config.= ''."\n";
		$config.= '/* DB Name */'."\n";
		$config.= '$CFG->DB_NAME = \''.str_replace("'", "\'", $DB_NAME).'\';'."\n";
		$config.= ''."\n";
		$config.= '// set the root directory'."\n";
		$config.= '$CFG->ROOT_DIR = \'' . $CFG->ROOT_DIR . '\';'."\n";
		$config.= ''."\n";
		if(!empty($_SESSION['INSTALL']['CFG']['EXEC_TIME'])) {
			$config.= '// set the default execution time'."\n";
			$config.= $_SESSION['INSTALL']['CFG']['EXEC_TIME'] . "\n";
			$config.= ''."\n";
		}
		if(!empty($_SESSION['INSTALL']['CFG']['MEMORY'])) {
			$config.= '// riase the default memory limit'."\n";
			$config.= $_SESSION['INSTALL']['CFG']['MEMORY'] . "\n";
			$config.= ''."\n";
		}
// 		$config.= '// Check if the DB is up to date'."\n";
// 		$config.= '$version = GetParameter(\'DBUpdate\');'."\n";
// 		$config.= 'if($version < $newversion) {'."\n";
// 		$config.= '	@include_once(\'Common/UpdateDb.inc.php\');'."\n";
// 		$config.= '}'."\n";
// 		$config.= ''."\n";
		$config.= ''."\n";
		$config.= '?>';

		if($f=fopen($CFG->DOCUMENT_PATH . 'Common/config.inc.php', 'w')) {
			fwrite($f, $config);
			fclose($f);

			// In caso di successo di tutta la procedura, si dirotta l'utente allo step 3
			unset($_SESSION['INSTALL']);
			cd_redirect('?step=3');
		} else {
			$_SESSION['INSTALL']['CFG']['ERROR']=get_text('Config write failed','Install');
			cd_redirect('?step=2');
		}
	}
}

function check_write_DB($tipo, $W_HOST, $W_USER, $W_PASS) {
	global $CFG, $WRIT_CON;
	$a=safe_w_con(true); // ritorna con l'errore di collegamento
	$testi=($tipo=='W'?'Write':'Read');
	if($a=='CONNECTION_FAILED' or is_array($a)) {
		// se è stata fornita la password di root, proviamo a collegarci come root
		if(isset($_POST[$tipo.'_ROOT'])) {
			$CFG->W_USER ='root';
			$CFG->W_PASS =stripslashes(trim($_POST[$tipo.'_ROOT']));
			$a=safe_w_con(true);
			if($a=='CONNECTION_FAILED') {
				$_SESSION['INSTALL']['CFG']['ERROR']=get_text($testi.' connection failed','Install');
				cd_redirect('?step=2');
			}

			if(is_array($a) and $a[1]=='NO_DATABASE') {
				// beh... bisogna creare il DB :)
				$WRIT_CON=$a[0];
				safe_w_sql("CREATE DATABASE `$CFG->DB_NAME` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");

				// poi garantire l'accesso all'utente
				safe_w_sql("grant all privileges on `".$CFG->DB_NAME."`.* to '$W_USER'@'$W_HOST' identified by '".addslashes($W_PASS)."';");

				// infine inserire nel DB la struttura di base
				// ma prima rimuovere la connessione per forzarne una nuova
				$WRIT_CON=null;
				$CFG->W_USER = $W_USER;
				$CFG->W_PASS = $W_PASS;
				install_blank_db();

				// e ritorna falso (creato ex novo)
				return(false);
			}
		} elseif(is_array($a) and $a[1]=='NO_DATABASE') {
			$_SESSION['INSTALL']['CFG']['ERROR']=get_text($testi.' Database not present','Install');
			cd_redirect('?step=2');
		}

		// in ogni caso ritorna sulla pagina via un get per eliminare i POST
		$_SESSION['INSTALL']['CFG']['ERROR']=get_text($testi.' connection failed','Install');
		cd_redirect('?step=2');
	}

	// se la connessione è stata stabilita senza problemi bisogna fare il dump
	// quindi ritorna true!
	return(true);
}

?>