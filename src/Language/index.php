<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/GlobalsLanguage.inc.php');
require_once('Language/lib.php');

if(!empty($_REQUEST['UpdateLanguage']) and preg_match('/^[A-Z-]+$/', $_REQUEST['UpdateLanguage'])) {
	// gets the content of the language pack from ianseo!
	if( $package=@file_get_contents("https://translations.ianseo.net/getpackage.php?lang={$_REQUEST['UpdateLanguage']}")) {
		if($files=@unserialize(gzinflate($package))) {
			$Lang=strtolower($_REQUEST['UpdateLanguage']);
			$LangCommon = $CFG->DOCUMENT_PATH.'Common/Languages/';
			$LangDir = $LangCommon . $Lang . '/';
			if(!file_exists($LangDir)) {
				mkdir($LangDir, 0777);
				chmod($LangDir, 0777);
			}

			// salva il credit aggiornato
			save_lang_files($LangCommon . "credits.php", $files['credits']);

			// salva le immaginine
			save_lang_files($LangDir . $Lang . '.png', $files['flag-png']);
			save_lang_files($LangDir . $Lang . '.svg', $files['flag-svg']);

			// salva il testuale
			save_lang_files($LangDir . $Lang . '.txt', $files['testuale']);

			// salva i moduli
			foreach($files['lang'] as $Module => $File) save_lang_files($LangDir . $Module . '.php', "<?" . "php\n" . $File . "?>");

			foreach(glob($LangDir.'*.old') as $file) {
				unlink($file);
			}
		}
	}
	cd_redirect();
}

if(isset($_REQUEST['UpdateAll'])) {
    foreach ($Lingue as $Lang=>$langtext) {
        if( $package=@file_get_contents("https://translations.ianseo.net/getpackage.php?lang=".strtoupper($Lang))) {
            if($files=@unserialize(gzinflate($package))) {
                //$Lang=strtolower($_REQUEST['UpdateLanguage']);
                $LangCommon = $CFG->DOCUMENT_PATH.'Common/Languages/';
                $LangDir = $LangCommon . $Lang . '/';
                if(!file_exists($LangDir)) {
                    mkdir($LangDir, 0777);
                    chmod($LangDir, 0777);
                }

                // salva il credit aggiornato
                save_lang_files($LangCommon . "credits.php", $files['credits']);

                // salva le immaginine
                save_lang_files($LangDir . $Lang . '.png', $files['flag-png']);
                save_lang_files($LangDir . $Lang . '.svg', $files['flag-svg']);

                // salva il testuale
                save_lang_files($LangDir . $Lang . '.txt', $files['testuale']);

                // salva i moduli
                if(!empty($files['lang'])) {
                    foreach($files['lang'] as $Module => $File) save_lang_files($LangDir . $Module . '.php', "<?" . "php\n" . $File . "?>");
                }

                foreach(glob($LangDir.'*.old') as $file) {
                    unlink($file);
                }
            }
        }
    }
    cd_redirect();
}

$Lingue_esterne=array();
if(!empty($_REQUEST['FindLanguage'])) {
    if( $package=@file_get_contents("https://translations.ianseo.net/getlanguages.php")) {
        $Lingue_esterne=@unserialize(gzinflate($package)) ;
    }
}

$JS_SCRIPT[] = '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>';
$JS_SCRIPT[] = '<script type="text/javascript" src="./lang.js"></script>';
$JS_SCRIPT[] = '<style>.updated {background-color:#e0ffe0}</style>';

include('Common/Templates/head.php');
?>
<div align="center">
<div class="medium">
<table class="Tabella">
<tr><th class="Title" colspan="4"><?php print get_text('Languages','Languages');?><input style="float:right" type="button" value="Update All" onclick="updateAllLanguages()"></th></tr>
<tr class="Spacer"><td colspan="4"></td></tr>
<?php

	foreach ($Lingue as $lang=>$langtext)
	{
		print '<tr>' . "\n";
		print '	<td width="4%" class="Center">'."\n";
		print '		<a class="Link" href="index.php?SetLanguage=' . strtoupper($lang) . '"><img src="../Common/Languages/' . $lang . '/'.$lang.'.png" border="0"></a>'."\n";
		print '	</td>'."\n";
		print '	<td>'."\n";
		print '		<a class="Link" href="index.php?SetLanguage=' . strtoupper($lang) . '">' . $langtext . '</a>'."\n";
		print '	</td>'."\n";

		print '	<td id="date-'.$lang.'">'."\n";
		print '		' . date(get_text('DateFmt') . ' H:i:s', filemtime('../Common/Languages/' . $lang . '/'.$lang.'.txt')) . "\n";
		print '	</td>'."\n";
		print '	<td>'."\n";
		print '		<input id="lang='.$lang.'" type="button" value="'.htmlspecialchars(get_text('Aggiorna', 'Languages')).'" onclick="updateLanguage(this)">'."\n";
		print '	</td>'."\n";
		print '</tr>'."\n";
	}

	echo '<tr class="Spacer"><td colspan="4"></td></tr>'."\n";

	if($Lingue_esterne) {
		echo ''."\n";
		echo ''."\n";
		echo ''."\n";
		$new_langs=array_diff(array_keys($Lingue_esterne), array_keys($Lingue));
		$select='';
		if($new_langs) {
			$select='<form method="GET"><select name="UpdateLanguage">';
			foreach($new_langs as $lang) {
				$select .= '<option value="'.strtoupper($lang).'">'.$Lingue_esterne[$lang]->language.'-'.$Lingue_esterne[$lang]->translation.'</option>';
			}
			$select .= '</select>&nbsp;&nbsp;<input type="submit" value="'.get_text('InstallNewLanguage', 'Languages').'"/></form>';
		}
		$select.='<div>'.get_text('NoNewLanguage', 'Languages').'</div>';



		echo '<tr>'."\n";
		echo '	<td colspan="4" align="center">'.$select.'</td>'."\n";
		echo '</tr>'."\n";
	} else {
		echo '<tr>'."\n";
		echo '	<td colspan="4" align="center"><a class="Link" href="?FindLanguage=1">' . get_text('FindLanguage', 'Languages') .'</a></td>'."\n";
		echo '</tr>'."\n";
	}

?>
</table>
</div>
</div>
<?php
	include('Common/Templates/tail.php');

?>
