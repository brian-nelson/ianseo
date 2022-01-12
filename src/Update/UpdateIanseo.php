<?php

global $JSON;
$PROCEED=false;
$UpdateFile=dirname(DIRNAME).'/TV/Photos/updating.json';
if(file_exists($UpdateFile)) {
    if(is_writable($UpdateFile) and $f=file_get_contents($UpdateFile) and $DATA=@json_decode($f)) {
        // we have a regular situation file... can go on
        $PROCEED=true;
    }
}

if(!$PROCEED) {
    $DATA->error=1;
    $DATA->status.='Status file missing, corrupted or not writeable. Please check all files, folders and subfolders in '.($_SERVER['DOCUMENT_ROOT'].$CFG->ROOT_DIR).' are world-writeable, remove the file '.$UpdateFile.' and try again.';
    $DATA->msg='Status file missing, corrupted or not writeable. Please check all files, folders and subfolders in '.($_SERVER['DOCUMENT_ROOT'].$CFG->ROOT_DIR).' are world-writeable, remove the file '.$UpdateFile.' and try again.';
    $DATA->finished=1;
    file_put_contents($UpdateFile, json_encode($DATA));
    $JSON['msg']='Status file missing, corrupted or not writeable. Please check all files, folders and subfolders in '.($_SERVER['DOCUMENT_ROOT'].$CFG->ROOT_DIR).' are world-writeable, remove the file '.$UpdateFile.' and try again.';
    JsonOut($JSON);
}


if(empty($IN_PHP)) {
    $DATA->error=1;
    $DATA->status='<div>' .get_text('NotUpdatable', 'Install', $CFG->INCLUDE_PATH).'</div>';
    $DATA->msg=get_text('NotUpdatable', 'Install', $CFG->INCLUDE_PATH).'<div>' . $CFG->INCLUDE_PATH.'</div>';
    $DATA->finished=1;
    file_put_contents($UpdateFile, json_encode($DATA));
    JsonOut($JSON);
}

require_once('Language/lib.php');
ini_set('memory_limit', '512M');

// nothing to do here without data
if(!in_array(ProgramRelease, array('STABLE','FITARCO')) and empty($_REQUEST['user'])) {
    $DATA->error=1;
    $DATA->status='<div>' .get_text('NotUpdatable', 'Install', $CFG->INCLUDE_PATH).'</div>';
    $DATA->msg=get_text('NotUpdatable', 'Install', $CFG->INCLUDE_PATH).'<div>' . $CFG->INCLUDE_PATH.'</div>';
    $DATA->finished=1;
    file_put_contents($UpdateFile, json_encode($DATA));
    JsonOut($JSON);
}

$URL=$CFG->IanseoServer.'Update.php';

include('FileList.php');



$JSON['error']=0;
$DATA->error=0;
$DATA->status='<div>' .get_text('Prepare', 'Install') . ':... ';
file_put_contents($UpdateFile, json_encode($DATA));

$tmp = new FileList($CFG->INCLUDE_PATH);
$tmp->EscludeFiles('^(\.)');
$tmp->ShowSize(true);
$tmp->ShowMD5(true);
if(!$tmp->Load()) {
    $DATA->error=1;
    $DATA->status.='</div><div>' .get_text('NotUpdatable', 'Install', $CFG->INCLUDE_PATH).'</div>';
    $DATA->msg=get_text('NotUpdatable', 'Install', $CFG->INCLUDE_PATH).'<div>' . $CFG->INCLUDE_PATH.'</div>';
    $DATA->finished=1;
    file_put_contents($UpdateFile, json_encode($DATA));
    $JSON['error']=1;
    $JSON['msg']=get_text('NotUpdatable', 'Install', $CFG->INCLUDE_PATH);
    JsonOut($JSON);
}

$DATA->status.=get_text('Done', 'Install').'</div>';
file_put_contents($UpdateFile, json_encode($DATA));

// sending request to ianseo
$DATA->status.= '<div>' . get_text('Sending', 'Install'). ':... ';
file_put_contents($UpdateFile, json_encode($DATA));
$Old=$tmp->serialize();
$Query=array( 'Json' => gzcompress($Old) );
if(!empty($_REQUEST['user'])) {
    $Query['Email']=trim($_REQUEST['user']);
}
if(!empty($_REQUEST['pwd'])) {
    $Query['Password']=trim($_REQUEST['pwd']);
}
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
    $DATA->error=1;
    $DATA->status.='</div><div>' .$tmpErr["message"] . '</div>';
    $DATA->msg=$tmpErr["message"];
    $DATA->finished=1;
    $JSON['error']=1;
    $JSON['msg']=$tmpErr["message"];
    file_put_contents($UpdateFile, json_encode($DATA));
    JsonOut($JSON);
}

$DATA->status.=get_text('Done', 'Install').'</div>';
file_put_contents($UpdateFile, json_encode($DATA));

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
$DATA->status.='<div>' . get_text('Retrieving', 'Install', $size). ':... ';
file_put_contents($UpdateFile, json_encode($DATA));
$tmp=stream_get_contents($stream);

if(!($NewIanseo=unserialize(@gzuncompress($tmp)))) {
	if($tmp=='NothingToDo') {
        $DATA->status.=get_text('Done', 'Install');
		updateChkUp();
	} else {
        $DATA->status.=get_text('Failed', 'Install');
	}
    $DATA->status.= '</div>';
    $DATA->status.= '<div><br/>'.get_text($tmp,'Install').'</div>';

    $DATA->error=1;
    $DATA->msg=get_text($tmp,'Install');
    $DATA->finished=1;
    $JSON['error']=1;
    $JSON['msg']=get_text($tmp,'Install');
    file_put_contents($UpdateFile, json_encode($DATA));
    JsonOut($JSON);
}
fclose($stream);
$DATA->status.='<br/>'.get_text('Done', 'Install').'</div>';

$STATUS=$DATA->status;

// updating the distro, New Files and dirs
$DATA->status.='<div>' . get_text('Updating', 'Install'). ':... ';
file_put_contents($UpdateFile, json_encode($DATA));

foreach($NewIanseo->Files as $file=>$data) {
	if(!is_dir(dirname($CFG->INCLUDE_PATH . '/'. $file))) {
		mkdir(dirname($CFG->INCLUDE_PATH . '/'. $file),0777, true);
		chmod(dirname($CFG->INCLUDE_PATH . '/'. $file), 0777);
	}
	file_put_contents ($CFG->INCLUDE_PATH . '/'. $file , $data['f']);
	if(!is_writable($CFG->INCLUDE_PATH . '/'. $file)) {
        chmod($CFG->INCLUDE_PATH . '/'. $file, 0666);
    }

    $DATA->status.='<br/>'.$file . " (" . $data['s'] . " bytes): " . $data['m'];
    file_put_contents($UpdateFile, json_encode($DATA));
}
$DATA->status.='<br/>'.get_text('Done', 'Install').'</div>';
file_put_contents($UpdateFile, json_encode($DATA));

// deleting spurious files...
$DATA->status.='<div>' . get_text('Deleting', 'Install'). ':... ';
file_put_contents($UpdateFile, json_encode($DATA));

foreach($NewIanseo->ToDelete as $file) {
    unlink($CFG->INCLUDE_PATH . '/'. $file);
    $DATA->status.='<br/>'.$file;
    file_put_contents($UpdateFile, json_encode($DATA));
}
rsort($NewIanseo->DirToDelete);
foreach($NewIanseo->DirToDelete as $file) {
    rmdir($CFG->INCLUDE_PATH . '/'. $file);
    $DATA->status.='<br/>'.$file;
    file_put_contents($UpdateFile, json_encode($DATA));
}
$DATA->status.='<br>'.get_text('Done', 'Install').'</div>';
file_put_contents($UpdateFile, json_encode($DATA));

// updating the languages
$DATA->status.='<div>' . get_text('UpdatingLanguages', 'Install'). ':... ';
foreach(glob($CFG->INCLUDE_PATH . '/Common/Languages/*') as $file) {
	if(!is_dir($file)) continue;
	// gets the content of the language pack from ianseo!
	if( $package=@file_get_contents("https://translations.ianseo.net/getpackage.php?lang=".strtoupper(basename($file)))) {
		if($files=@unserialize(gzinflate($package))) {
            $DATA->status.=' '.basename($file);
            file_put_contents($UpdateFile, json_encode($DATA));

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
			if(!empty($files['lang'])) {
				foreach($files['lang'] as $Module => $File) {
					save_lang_files($LangDir . $Module . '.php', "<?" . "php\n" . $File . "?>");
				}
			}

			foreach(glob($LangDir.'*.old') as $file) {
				unlink($file);
			}

		}
	}
}
$DATA->status.='<br>'.get_text('Done', 'Install').'</div>';
file_put_contents($UpdateFile, json_encode($DATA));

$DATA->status.='<div><b style="font-size:larger">' . get_text('UpgradeFinished', 'Install') . '</b></div>';
$DATA->finished=1;
file_put_contents($UpdateFile, json_encode($DATA));
updateChkUp();

$JSON['msg']='';
JsonOut($JSON);

function updateChkUp() {
	$q="UPDATE Parameters SET ParValue='".date('Y-m-d H:i:s')."' WHERE ParId='ChkUp' ";
	$r=safe_w_sql($q);

    // force a check on updatedb, just in case
    require_once('Common/UpdateDb-check.php');
}
