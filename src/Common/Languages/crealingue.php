<?php

/*

$StrHTTFlags=array
(
''=>'"X" can be used',
''=>'"M" to write value 0',
''=>'Enable "E" to mean error',
''=>'Y: View Distance Total <br/> N: View Tour Total',
''=>'A - Min. score 6',
''=>'B - Min. score 6',
''=>'C - Min. score 6',
''=>'D - Min. score 6',
'' => 'View Target Letter',
''=>'Don\'t view Game Info',
''=>'Data Reset',
''=>'ID Reset'
);


*/

$LANG_DIR=dirname(__FILE__);
define('MENU_DIR', realpath($LANG_DIR.'/../phplayersmenu'));

$MODULES = glob($LANG_DIR . '/en/*.php');
$LANGS = array();
$MENUS = array();
$BACKS = array();

// recupera le variabili in un array delle lingue
foreach( glob($LANG_DIR . '/??.inc.php') as $file) {
	$LanCode=strtolower(substr(basename($file),0,2));
	$LANGS[$LanCode] = get_vars($file);
	$MENUS[$LanCode] = get_menu(strtoupper($LanCode));
}

$BACKS = $LANGS;

unset($LANGS['en']);
unset($MENUS['en']);

$HTTFlags=array(
	'HTTFlags_SelectX' => 'HTTSelectX',
	'HTTFlags_SelectM' => 'HTTSelectM',
	'HTTFlags_SelectE' => 'HTTSelectE',
	'HTTFlags_3Arrows' => 'HTT3Arrows',
	'HTTFlags_LasVegasMode' => 'HTTLasVegasMode',
	'HTTFlags_DistanceTotal' => 'HTTDistanceTotal',
	'HTTFlags_Min6_A' => 'HTTMin6_A',
	'HTTFlags_Min6_B' => 'HTTMin6_B',
	'HTTFlags_Min6_C' => 'HTTMin6_C',
	'HTTFlags_Min6_D' => 'HTTMin6_D',
	'HTTFlags_TargetLetter' => 'HTTTargetLetter',
	'HTTFlags_GameInfo' => 'HTTGameInfo',
	'HTTFlags_ResetInfo' => 'ResetInfo',
	'HTTFlags_ResetID' => 'HTTResetID',
);

foreach($MODULES as $Module) {
	$lang=array();
	include($Module);

	// prepara i files vuoti per ogni lingua
	$Files=array();
	foreach(array_keys($LANGS) as $LanCode) {
		$Files[$LanCode] = get_module("$LANG_DIR/$LanCode/" . basename($Module));
	}

	// inizia il lavoro: per ogni chiave del modulo cerco la corrispondente variabile nei files di lingua
	// la inserisco nel file di lingua corrispondente
	foreach($lang as $key => $val) {
		foreach($LANGS as $LanCode => $vars) {
			$val2='';
			if(strstr($key, 'DayOfWeek_')==$key) {
				$val2=$vars['StrDayOfWeek'][substr($key,-1)];
				//				unset($BACKS[$LanCode]['StrDayOfWeek'][substr($key,-1)]);
				//				unset($BACKS['en']['StrDayOfWeek'][substr($key,-1)]);

			} elseif(strstr($key, 'HTTFlags_')==$key) {
				$val2=$vars['StrHTTFlags'][$HTTFlags[$key]];
				//				unset($BACKS[$LanCode]['StrHTTFlags'][$HTTFlags[$key]]);
				//				unset($BACKS['en']['StrHTTFlags'][$HTTFlags[$key]]);

			} elseif(strstr($key, 'Eliminations_')==$key) {
				$val2=$vars['Arr_StrEliminations'][substr($key,-1)];
				//				unset($BACKS[$LanCode]['Arr_StrEliminations'][substr($key,-1)]);
				//				unset($BACKS['en']['Arr_StrEliminations'][substr($key,-1)]);

			} elseif(strstr($key, 'Status_')==$key) {
				$val2=$vars['Arr_StrStatus'][substr($key,-1)];
				//				unset($BACKS[$LanCode]['Arr_StrStatus'][substr($key,-1)]);
				//				unset($BACKS[$LanCode]['Arr_StrStatus'][substr($key,-1)]);

			} elseif(strstr($key, 'Month_')==$key) {
				$val2=$vars['StrMonth'][substr($key,6)];
				//				unset($BACKS[$LanCode]['StrMonth'][substr($key,6)]);
				//				unset($BACKS['en']['StrMonth'][substr($key,6)]);

			} elseif($key=='DateFmtDB') {
				$val2 = $vars['StrDateFmt'];
				//				unset($BACKS[$LanCode]['StrDateFmt']);
				//				unset($BACKS['en']['StrDateFmt']);

			} elseif($key=='DateFmt') {
				$val2 = str_replace('%','',$vars['StrDateFmt']);
				//				unset($BACKS[$LanCode]['StrDateFmt']);
				//				unset($BACKS['en']['StrDateFmt']);

			} elseif($key=='Row-1') {
				$val2 = str_replace('%','',$vars['StrFirstRow']);
				//				unset($BACKS[$LanCode]['StrFirstRow']);
				//				unset($BACKS['en']['StrFirstRow']);

			} elseif($key=='Row-2') {
				$val2 = str_replace('%','',$vars['StrSecondRow']);
				//				unset($BACKS[$LanCode]['StrSecondRow']);
				//				unset($BACKS['en']['StrSecondRow']);

			} elseif($key=='Dos') {
				$val2 = $vars['StrDos'];
				//				unset($BACKS[$LanCode]['StrDos']);
				//				unset($BACKS[$LanCode]['StrDoS']);
				//				unset($BACKS['en']['StrDos']);
				//				unset($BACKS['en']['StrDoS']);

			} elseif($key=='MakingFile') {
				$val2 = str_replace(' ~FileName~...','',$vars['StrMakingFile']);
				//				unset($BACKS[$LanCode]['StrMakingFile']);
				//				unset($BACKS['en']['StrMakingFile']);

			} elseif($key=='DateFmtMoreDays') {
				$val2 = str_replace(array('#s#','#e#'),array('$a[0]','$a[1]'),$vars['StrDateFmtMoreDays']);
				//				unset($BACKS[$LanCode]['StrDateFmtMoreDays']);
				//				unset($BACKS['en']['StrDateFmtMoreDays']);

			} elseif($key=='RowNotImported') {
				$val2 = str_replace('~##~','$a',$vars['StrRowNotImported']);
				//				unset($BACKS[$LanCode]['StrRowNotImported']);
				//				unset($BACKS['en']['StrRowNotImported']);

			} elseif($key=='RowImported') {
				$val2 = str_replace('~##~','$a',$vars['StrRowImported']);
				//				unset($BACKS[$LanCode]['StrRowImported']);
				//				unset($BACKS['en']['StrRowImported']);

			} elseif($key=='TargetAssigned') {
				$val2 = str_replace('~##~','$a',$vars['StrTargetAssigned']);
				//				unset($BACKS[$LanCode]['StrTargetAssigned']);
				//				unset($BACKS['en']['StrTargetAssigned']);

			} elseif($key=='PhotoSizeError') {
				$val2 = str_replace('~##~','$a ',$vars['StrPhotoSizeError']);
				//				unset($BACKS[$LanCode]['StrPhotoSizeError']);
				//				unset($BACKS['en']['StrPhotoSizeError']);

			} elseif($key=='PhotoProportionError') {
				$val2 = str_replace('~##~','$a ',$vars['StrPhotoProportionError']);
				//				unset($BACKS[$LanCode]['StrPhotoProportionError']);
				//				unset($BACKS['en']['StrPhotoProportionError']);

			} elseif($key=='Importing') {
				$val2 = str_replace('~##~','$a',$vars['StrImporting']);
				//				unset($BACKS[$LanCode]['StrImporting']);
				//				unset($BACKS['en']['StrImporting']);

			} elseif($key=='PhotoDimError') {
				$val2 = str_replace(array('~ww~','~hh~'),array('$a[0] ','$a[1] '),$vars['StrPhotoDimError']);
				//				unset($BACKS[$LanCode]['StrPhotoDimError']);
				//				unset($BACKS['en']['StrPhotoDimError']);

			} elseif($key=='LogoL') {
				$val2 = $vars['StrLLogo'];
				//				unset($BACKS[$LanCode]['StrLLogo']);
				//				unset($BACKS['en']['StrLLogo']);

			} elseif($key=='LogoR') {
				$val2 = $vars['StrRLogo'];
				//				unset($BACKS[$LanCode]['StrRLogo']);
				//				unset($BACKS['en']['StrRLogo']);

			} elseif($key=='LogoB') {
				$val2 = $vars['StrBLogo'];
				//				unset($BACKS[$LanCode]['StrBLogo']);
				//				unset($BACKS['en']['StrBLogo']);

			} elseif($key=='LogoManagement') {
				$val2 = $vars['StrManLogos'];
				//				unset($BACKS[$LanCode]['StrManLogos']);
				//				unset($BACKS['en']['StrManLogos']);

			} elseif($key=='BlockUnset') {
				$val2 = $vars['StrUnsetBlock'];
				//				unset($BACKS[$LanCode]['StrUnsetBlock']);
				//				unset($BACKS['en']['StrUnsetBlock']);

			} elseif($key=='BlockSet') {
				$val2 = $vars['StrSetBlock'];
				//				unset($BACKS[$LanCode]['StrSetBlock']);
				//				unset($BACKS['en']['StrSetBlock']);

			} elseif($key=='Sign/guide-board') {
				$val2 = $vars['StrSign'];
				//				unset($BACKS[$LanCode]['StrSign']);
				//				unset($BACKS['en']['StrSign']);

			} elseif($key=='MedalIndClass') {
				$val2 = $vars['StrMedalndClass'];
				//				unset($BACKS[$LanCode]['StrMedalndClass']);
				//				unset($BACKS['en']['StrMedalndClass']);

			} elseif(isset($vars['Str'.$key])) {
				$val2 = $vars['Str'.$key];
				//				unset($BACKS[$LanCode]['Str'.$key]);
				//				unset($BACKS['en']['Str'.$key]);

			} elseif(isset($vars['Str_'.$key])) {
				$val2 = $vars['Str_'.$key];
				//				unset($BACKS[$LanCode]['Str_'.$key]);
				//				unset($BACKS['en']['Str_'.$key]);

			} elseif($key=='MenuLM_Close') {
				$val2 = $vars['StrClose'];
				//				unset($BACKS[$LanCode]['StrClose']);
				//				unset($BACKS['en']['StrClose']);

			} elseif($key=='MenuLM_Delete') {
				$val2 = $vars['StrDeleteTournament'];
				//				unset($BACKS[$LanCode]['StrDeleteTournament']);
				//				unset($BACKS['en']['StrDeleteTournament']);

			} elseif(strstr($key, 'MenuLM_')==$key and !empty($MENUS[$LanCode][substr($key, 7)])) {
				$val2 = $MENUS[$LanCode][substr($key, 7)];
				if(empty($val2)) $val2 = $vars['Str'.substr($key, 7)];
				//				unset($MENUS[$LanCode][substr($key, 7)]);

			} elseif(isset($vars[$key])) {
				$val2 = $vars[$key];
				//				unset($BACKS[$LanCode][$key]);
				//				unset($BACKS['en'][$key]);
			}
				
			if($val2 and $val2!=$val and (!isset($Files[$LanCode][$key]) or $Files[$LanCode][$key]!=$val2)) $Files[$LanCode][$key]=$val2;
		}
	}

	// a questo punto, scarico ogni modulo nella rispettiva lingua...
	foreach($Files as $LanCode => $vars) {
		if($vars) {
			$f=fopen($LANG_DIR . '/' . $LanCode . '/' . basename($Module), 'w');
			fwrite($f, '<?php' . "\n");
			fwrite($f, '// DO NOT EDIT' . "\n");
			fwrite($f, '// USE THE TRANSLATION PAGE INSTEAD' . "\n");
			fwrite($f, '// http://localhost/Language/traduzione.php' . "\n");
			foreach($vars as $key => $val) {
				fwrite($f, '$lang[\''.str_replace("'","\\'",$key).'\']=\''.str_replace("'","\\'",$val).'\';'."\n");
			}
			fwrite($f, '?>' . "\n");
			fclose($f);
		}
	}
}

//print_r($BACKS);

function get_vars($file) {
	$ret=array();
	include($file);
	$fp=file($file);
	foreach($fp as $riga) {
		if(substr($riga,0,1)=='$') {
			$m=array();
			preg_match('/^\$([a-z0-9_]+)/i', $riga, $m);
			$ret[$m[1]]=${$m[1]};
		}
	}
	ksort($ret);
	return $ret;
}

function get_menu($LanCode) {
	static $EN=array();
	static $REN=array();
	$ret=array();
	if(!$EN) {
		$fp=file(MENU_DIR . "/menu_off.EN.txt");
		$fp+=file(MENU_DIR . "/menu_on.EN.txt");
		foreach($fp as $riga) {
			if($riga and $riga[0]=='.' and !strstr($riga,'---')) {
				$riga=explode('|', trim($riga));
				$EN[]=$riga;
				if(!empty($riga[2]) and empty($REN[$riga[2]])) $REN[$riga[2]]=$riga[1];
			}
		}
	}

	if($LanCode=='EN') return;

	$LANG=array();
	$RLANG=array();
	$fp=file(MENU_DIR . "/menu_off.$LanCode.txt");
	$fp+=file(MENU_DIR . "/menu_on.$LanCode.txt");
	foreach($fp as $riga) {
		if($riga and $riga[0]=='.' and !strstr($riga,'---')) {
			$riga=explode('|', trim($riga));
			$LANG[]=$riga;
			if(!empty($riga[2]) and empty($RLANG[$riga[2]])) $RLANG[$riga[2]]=$riga[1];
		}
	}

	$key=0; // chiave per recuperare la sincronia con il file della lingua!
	foreach($EN as $riga => $pezzi) {
		$value='';
		if(isset($LANG[$key][1])) $value=$LANG[$key][1];
		if(isset($pezzi[2]) and (!isset($LANG[$key][2]) or $LANG[$key][2]!=$pezzi[2])) {
			$value='';
		}
		if($value and $pezzi[1]!=$value and !isset($ret[$pezzi[1]])) {
			$ret[$pezzi[1]]=$value;
		}

		// controllo se andare avanti con l'indice nella trad
		if($LANG[$key][0]==$pezzi[0]) {
			if(isset($pezzi[2])) {
				if(isset($LANG[$key][2]) and $LANG[$key][2]==$pezzi[2]) $key++;
			} else {
				$key++;
			}
		}
	}

	// inserisce i dati sicuramente corretti...
	foreach($REN as $val => $key) {
		if(isset($RLANG[$val]) and $key!=$RLANG[$val]) $ret[$key]=$RLANG[$val];
	}
	return $ret;
}

function get_module($Module) {
	global $LANG_DIR;
	$lang=array();
	if(file_exists($Module)) {
		include($Module);
	}
	ksort($lang);
	return $lang;
}

?>