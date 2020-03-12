<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/GlobalsLanguage.inc.php');

	$l='';
	$m='';
	if(isset($_REQUEST['l']) and isset($Lingue[$_REQUEST['l']])) $l=$_REQUEST['l'];
	if(isset($_REQUEST['m']) and in_array($_REQUEST['m'], $Moduli)) $m=$_REQUEST['m'];

	if($_POST and $l and $m and is_array($_POST['valore'])){

		if(strstr($m, 'menu_')) {
/*			$IT=get_lang($m, DEF_LANG, '1');
			$menu=array();
			foreach($IT as $key => $pezzi) {
				if(trim($pezzi[1])!='---') $pezzi[1]=$_POST['valore'][$key];
				$menu[]=implode('|', $pezzi);
			}

			// salva il menu
			$f=fopen( $file=MENU_INCLUDE . "/$m." . strtoupper($l) . ".txt" , 'w');
			fwrite($f,implode("\n", $menu));
			fclose($f);
			chmod( $file , 0666);*/
		} else {
			// controlla che ci sia un post nuovo... DEVE avere un titolo e un testo

			$modulo="<?"."php\n";
			foreach($_POST['valore'] as $key=>$val) {
				$val=trim($val);
				if($val) $modulo.="\$lang['" . $key . "']='" . str_replace("'","\'", stripslashes($val)) . "';\n";
			}
			$modulo.='?'.'>';

			// salvaguardia dell'ultimo originale IT!!!
			if($l==DEF_LANG) {
				if(!copy(LANG_INCLUDE . "/it/$m" , LANG_INCLUDE . "/".DEF_LANG."/$m.back")) die('ARGHHH! Could not backup the original file');
			}

			// ordina per chiave e salva
			$f=fopen( $file= LANG_INCLUDE . "/$l/$m" , 'w');
			fwrite($f,$modulo);
			fclose($f);
			chmod( $file , 0666);
		}

	}

	$ColSpan = count($Moduli) + 2;


	if($l and $m) $ColSpan='3';

	include('Common/Templates/head.php');
?>
<div align="center">
<div class="medium">

<form method="POST">
<input type="hidden" name="m" value="<?php echo $m ?>">
<input type="hidden" name="l" value="<?php echo $l ?>">

<table class="Tabella">
<tr><th class="Title" colspan="<?php echo $ColSpan ?>"><?php print ($l and $m)?get_text('Lang', 'Languages', array($l, $m)):get_text('Languages', 'Languages');?></th></tr>
<tr class="Spacer"><td colspan="<?php echo $ColSpan ?>"></td></tr>
<?php

if($l and $m) {
	// edit del modulo
	// elenca le variabili del file in italiano, la colonna con il testo originale e la traduzione
	elenca_lang($l, $m);
} else {
	// no edit!
	// elenca le varie lingue e lo stato della stessa:
	//    - OK: la lingua ha le stesse chiavi rispetto all'italiano
	//    - edit: la lingua ha chiavi diverse rispetto all'italiano
	//    - crea: manca la lingua... va caricata anche una bandiera...
	// da controllare lingua e modulo!


	// inizia con l'header
	print '<tr>';
	print '<th class="Title">&nbsp;</th>'; // flag
	print '<th class="Title">&nbsp;</th>'; // lang
	foreach ($Moduli as $Modulo) {
		print '<th class="Title">'.$Modulo.'</th>';
	}
	print '</tr>' . "\n";

	$styles=array('OK' => 'TargetOk',
				'Edit'=> 'TargetNoComplete',
				'Create' => 'TargetKo'
				);

	foreach ($Lingue as $Codice => $Lingua) {
		print '<tr>';
		print '<td width="4%" class="Center"><img src="'.$CFG->ROOT_DIR.'Common/Languages/' . $Codice . '/'.$Codice.'.png" border="0"></td>';
		print '<td>'.$Lingua.'</td>';
		foreach($Moduli as $Modulo) {
			$lang_status=check_lang_file($Codice, $Modulo);
			print '<td class="'.$styles[$lang_status].'"><a href="?l='.$Codice.'&m='.$Modulo.'" title="'.get_text($lang_status.'Descr', 'Languages').'">'.get_text($lang_status, 'Languages').'</a></td>';
		}
		print '</tr>' . "\n";
	}
}
?>
</table>
</form>
</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>