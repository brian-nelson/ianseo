<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Language/lib.php');

include('Common/Templates/head.php');


// nothing to do here without data
if(!in_array(ProgramRelease, array('STABLE','FITARCO')) and empty($_POST['Email'])) CD_redirect('/');

$URL='http://www.ianseo.net/Update.php';
//$URL='http://ianseonet.dellinux/Update.php';

include('FileList.php');

@ob_end_flush();
echo str_repeat(' ',1500);
flush();

// preparing list
do_flush('<div><br/>' .get_text('Prepare', 'Install') . ':... ');

$tmp = new FileList($CFG->INCLUDE_PATH);
$tmp->EscludeFiles('^(\.)');
$tmp->ShowSize(true);
$tmp->ShowMD5(true);
if(!$tmp->Load()) {
	echo '</div><div><br/>'. get_text('NotUpdatable','Install', $CFG->INCLUDE_PATH).'</div>';
	include('Common/Templates/tail.php');
	die();
}
do_flush(get_text('Done', 'Install').'</div>');

// sending request to ianseo
do_flush('<div><br/>' . get_text('Sending', 'Install'). ':... ');
$Old=$tmp->serialize();

$Query=array( 'Json' => gzcompress($Old) );
if(!empty($_POST['Email'])) $Query['Email']=trim($_POST['Email']);

$postdata = http_build_query( $Query, '', '&' );

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

$context = stream_context_create($opts);
$stream = fopen($URL, 'r', false, $context);
$tmp = null;

if($stream===false) {
	$tmpErr = error_get_last();
	echo "<br><b>" . $tmpErr["message"] . "</b><br>";
} else {
	do_flush(get_text('Done', 'Install').'</div>');

// header information as well as meta data
// about the stream
	$size=0;
	$Headers=stream_get_meta_data($stream);
	foreach($Headers['wrapper_data'] as $header) {
		if(stristr($header, 'Size-approx')) {
			list(,$size)=explode(': ', $header);
		}
	}

	$size=number_format($size/1024);

// retrieving data from ianseo
	do_flush('<div><br/>' . get_text('Retrieving', 'Install', $size). ':... ');
	$tmp=stream_get_contents($stream);
}

if(!($NewIanseo=unserialize(@gzuncompress($tmp)))) {
	if($tmp=='NothingToDo') {
		echo get_text('Done', 'Install');
		updateChkUp();
	} else {
		echo get_text('Failed', 'Install');
	}
	echo '</div>';
	echo '<div><br/>'.get_text($tmp,'Install').'</div>';
	include('Common/Templates/tail.php');
	die();
}
fclose($stream);
do_flush(get_text('Done', 'Install').'</div>');

// updating the distro
do_flush('<div><br/>' . get_text('Updating', 'Install'). ':... ');
foreach($NewIanseo->Files as $file=>$data) {
	if(!is_dir(dirname($CFG->INCLUDE_PATH . '/'. $file))) {
		mkdir(dirname($CFG->INCLUDE_PATH . '/'. $file),0777, true);
		chmod(dirname($CFG->INCLUDE_PATH . '/'. $file), 0777);
	}
	file_put_contents ($CFG->INCLUDE_PATH . '/'. $file , $data['f']);
	if(!is_writable($CFG->INCLUDE_PATH . '/'. $file)) chmod($CFG->INCLUDE_PATH . '/'. $file, 0666);

	do_flush('<br/>'.$file . " (" . $data['s'] . " bytes): " . $data['m'] );
}
do_flush(get_text('Done', 'Install').'</div>');

// updating the distro
do_flush('<div><br/>' . get_text('Deleting', 'Install'). ':... ');
foreach($NewIanseo->ToDelete as $file) {
	unlink($CFG->INCLUDE_PATH . '/'. $file);
	do_flush('<br/>'.$file );
}
rsort($NewIanseo->DirToDelete);
foreach($NewIanseo->DirToDelete as $file) {
	rmdir($CFG->INCLUDE_PATH . '/'. $file);
	do_flush('<br/>'.$file );
}
do_flush(get_text('Done', 'Install').'</div>');

// updating the languages
do_flush('<div><br/>' . get_text('UpdatingLanguages', 'Install'). ':... ');
foreach(glob($CFG->INCLUDE_PATH . '/Common/Languages/*') as $file) {
	if(!is_dir($file)) continue;
	// gets the content of the language pack from ianseo!
	if( $package=@file_get_contents("http://translations.ianseo.net/getpackage.php?lang=".strtoupper(basename($file)))) {
		if($files=@unserialize(gzinflate($package))) {
			do_flush('<br/>'.basename($file) );
			//debug_svela($files, true);
			$Lang=strtolower(basename($file));
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
}
do_flush(get_text('Done', 'Install').'</div>');

echo '<div><br/>' . get_text('UpgradeFinished', 'Install') . '</div>';
updateChkUp();
include('Common/Templates/tail.php');

function do_flush($msg) {
	echo $msg."\n";
	flush();
}

function updateChkUp()
{
	$q="UPDATE Parameters SET ParValue=NOW() WHERE ParId='ChkUp' ";
	$r=safe_w_sql($q);
}
?>