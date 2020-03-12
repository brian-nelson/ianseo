<?php

define('LANG_INCLUDE', $CFG->DOCUMENT_PATH  . 'Common/Languages');
define('MENU_INCLUDE', $CFG->DOCUMENT_PATH . 'Common/phplayersmenu');

// da cambiare in 'en' dopo
define('DEF_LANG', 'en');

// Nella dir LANG_INCLUDE cerco tutte le directory di 2 lettere
// corrispondenti al codice internazionale
// l'inglese è la lingua predefinita

$Moduli = array();
$Lingue = array();

// recupera i moduli inglesi, lingua di default
$dir = dir(LANG_INCLUDE . '/'.DEF_LANG.'/');
while($entry = $dir->read() ) {
	if(strstr( $entry, 'php' )) {
		$Moduli[] = $entry;
	}
}
sort($Moduli);
//array_unshift($Moduli, 'menu_on');
//array_unshift($Moduli, 'menu_off');

$Lin=glob(LANG_INCLUDE . '/??');
foreach($Lin as $path) {
	if(is_dir($path)) {
		$entry=basename($path);
		$Lingue[$entry]=strtoupper($entry);
		if(file_exists($l = LANG_INCLUDE . "/$entry/$entry.txt")) {
			$Lingue[$entry]=trim(implode('', file($l)));
		}
	}
}

$Lin=glob(LANG_INCLUDE . '/*/*.txt');
foreach($Lin as $path) {
	$entry=basename(dirname($path));
	if($path== LANG_INCLUDE . "/$entry/$entry.txt") {
		$Lingue[$entry]=trim(file_get_contents($path));
	}
}

unset($Lingue['en']);
ksort($Lingue);
//$Lingue = array_merge(array('it' => 'Italiano'), $Lingue);
$Lingue = array_merge(array('en' => 'English'), $Lingue);

/*
*********************************************
*/

function check_lang_file($l, $m) {
	static $EN;
	if(!isset($EN[$m])) {
		$EN[$m] = get_lang($m);
	}

	if($lang = get_lang($m, $l)) {
		foreach($EN[$m] as $key=>$val) {
			if(!isset($lang[$key])){
				return'Edit';
			}
		}
	} else {
		return 'Create';
	}
	return 'OK';
}

function get_lang($m, $l='en', $edit='') {
	$lang=array();
	if(strstr($m,'menu_')) {
		if(file_exists($file=MENU_INCLUDE . "/$m." . strtoupper($l) . ".txt")) {
			$f=file($file);
			foreach($f as $riga) {
				if($riga and $riga[0]=='.') {
					$pez=explode('|', $riga);
					if($edit) {
						$lang[]=$pez;
					} else {
						if(isset($pez[2])) $lang[trim($pez[2])]=$pez[1];
					}
				}
			}
		}
	} else {
		if(file_exists(LANG_INCLUDE . "/$l/$m")) {
			include( LANG_INCLUDE . "/$l/$m" );
		}
	}
	return($lang);
}

function elenca_lang($l, $m){
	// carica il file italiano
	$EN=get_lang($m, DEF_LANG, '1');
	// carica il file da tradurre
	$LANG=get_lang($m, $l, '1');

	echo "<tr>";
	echo "<td><B>".get_text('Variable', 'Languages')."</B></td>";
	echo "<td><B>".get_text('Original', 'Languages')."</B></td>";
	echo "<td><B>".get_text('Translation', 'Languages')."</B></td>";
	echo "</tr>";
	if(strstr($m,'menu_')) {
		// struttura cambia drasticamente...
		$key=0; // chiave per recuperare la sincronia con il file della lingua!
		foreach($EN as $riga => $pezzi) {
			$value='';
			if(isset($LANG[$key][1])) $value=str_replace('"','&quot;',$LANG[$key][1]);
			if(isset($pezzi[2]) and (!isset($LANG[$key][2]) or $LANG[$key][2]!=$pezzi[2])) {
				$value='';
			}
			$class=$value?'':' style="background-color:rgb(255,220,220)"';
			echo "<tr$class>";
			if(trim($pezzi[1])=='---') {
				// separatore!
				echo "<td colspan=\"3\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".get_text('Separatore', 'Languages')."</i></td>";
			} else {
				if(strlen($pezzi[0])==1) $pezzi[1]="<B>$pezzi[1]</B>";
				echo "<td nowrap>".str_repeat('&nbsp;»',strlen($pezzi[0])-1)."&nbsp;".$pezzi[1]."</td>";
				echo "<td>&nbsp;</td>";
				echo '<td width=100%><input type="text" name="valore['.$riga.']" value="'.$value.'" style="width:200px"></td>';
			}

			// controllo se andare avanti con l'indice nella trad
			if($LANG[$key][0]==$pezzi[0]) {
				if($pezzi[1]=='---') {
					if($LANG[$key][1]=='---') $key++;
				} elseif(isset($pezzi[2])) {
					if(isset($LANG[$key][2]) and $LANG[$key][2]==$pezzi[2]) $key++;
				} else {
					$key++;
				}
			}


			echo "</tr>";
		}
	} else {
		foreach($EN as $key=>$val) {
			$value='';
			$class=isset($LANG[$key])?'':' style="background-color:rgb(255,220,220)"';
			if(isset($LANG[$key])) $value=str_replace('"','&quot;',$LANG[$key]);
			echo "<tr$class>";
			echo "<td nowrap><B><i>$key</i></B></td>";
			echo "<td width=50%><b>$val</b></td>";
			if(strlen($value)>40) {
				echo '<td width=50%><textarea name="valore['.$key.']" style="width:200px">'.$value.'</textarea></td>';
			} else {
				echo '<td width=50%><input type="text" name="valore['.$key.']" value="'.$value.'" style="width:200px"></td>';
			}
			echo "</tr>";
		}
	}
	echo "<tr>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo '<td><input type=submit value="'.get_text('Aggiorna', 'Languages').'"></td>';
	echo "</tr>";
}

function usable_lang($Lingue) {
	$ret=array();
	foreach($Lingue as $key=>$val) {
		if(file_exists(MENU_INCLUDE . '/menu_on.'.strtoupper($key).'.txt')
			and file_exists(MENU_INCLUDE . '/menu_off.'.strtoupper($key).'.txt')) {
			$ret[$key] = $val ;
		}
	}
	return $ret;
}

function check_word($search) {
	global $Moduli;
	$ret=array();
	$snd=array();
	$inc=array();
	foreach($Moduli as $Module) {
		$lang=get_lang($Module);
		$Module=substr($Module,0,-4);
		foreach($lang as $key => $val) {
			$org=trim(strtolower($val));
			if(strtolower($key)==$search) {
				$ret[]=array($Module, $key, $val);
			} elseif($org==$search) {
				$ret[]=array($Module, $key, $val);
			} elseif(strstr($org, $search)) {
				$inc[]=array($Module, $key, $val);
			} elseif(soundex($org)==soundex($search)) {
				$snd[]=array($Module, $key, $val);
			}
		}
	}

	return array_merge($ret, $inc, $snd);
}
?>