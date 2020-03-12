<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/GlobalsLanguage.inc.php');
require_once('Language/lib.php');

$JSON=array('error' => 1, 'date' => '', 'lang' => '');


if(!empty($_REQUEST['lang']) and preg_match('/^[A-Z-]+$/i', $_REQUEST['lang'])) {
    $LANG=strtoupper($_REQUEST['lang']);
    // gets the content of the language pack from ianseo!
    if( $package=@file_get_contents("https://translations.ianseo.net/getpackage.php?lang=$LANG")) {
        if($files=@unserialize(gzinflate($package))) {
            $Lang=strtolower($_REQUEST['lang']);
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
                foreach($files['lang'] as $Module => $File) {
                	save_lang_files($LangDir . $Module . '.php', "<?" . "php\n" . $File . "?>");
                }
	        }

            foreach(glob($LangDir.'*.old') as $file) {
                unlink($file);
            }

            $JSON['error']=0;
            $JSON['lang']=$Lang;
            $JSON['date']=date(get_text('DateFmt') . ' H:i:s', filemtime('../Common/Languages/' . $Lang . '/'.$Lang.'.txt'));
        }
    }
}

JsonOut($JSON);