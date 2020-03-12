<?php

die();

require_once(dirname(dirname(__FILE__)) . '/config.php');

// gets the module list for this installation
$ret= new stdClass();
$ret->ProgVersion = ProgramVersion;
$ret->DbVersion = GetParameter('DBUpdate');
$ret->ProgRelease = ProgramRelease;
$ret->Action = 'list';
$ret->email = (empty($_POST['email']) ? '' : $_POST['email']);
$ret->Modules = get_modules();
$ret->Sets = get_sets();

$postdata = http_build_query( array( 'Json' => gzcompress(serialize($Old) ) ));

// get the online modules
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

$context = stream_context_create($opts);

$stream = fopen('https://ianseo.net/Ianseo/Modules.php', 'r', false, $context);

$tmp=stream_get_contents($stream);

if(!($NewIanseo=unserialize(gzuncompress($tmp)))) {
	if($tmp=='NothingToDo') {
		echo get_text('Done', 'Install');
	} else {
		echo get_text('Failed', 'Install');
	}
	echo '</div>';
	echo '<div><br/>'.get_text($tmp,'Install').'</div>';
	include('Common/Templates/tail.php');
	die();
}
fclose($stream);

include('Common/Templates/head.php');

echo '<div align="center"><form name="FrmParam" method="POST" action="">';
echo '<table class="Tabella" style="width:50%">';
echo '<tr>'
	. '<td colspan="2">'.get_text('ModuleSearch', 'Install').'</td>'
	. '</tr>';

if(!in_array(ProgramRelease, array('STABLE','FITARCO'))) {
	echo '<tr>'
		. '<th colspan="2">'.get_text('SpecialUpdate', 'Install', ProgramRelease).'</th>'
		. '</tr>';

	echo '<tr>'
		. '<th>' . get_text('Email','Install') . '</th>'
		. '<td><input type="text" name="Email"  style="width:100%"></td>'
		. '</tr>';
}



echo '<tr>'
	. '<td class="Center" colspan="2"><input type="submit" value="' . get_text('CmdOk') . '"></td>'
	. '</tr>';
echo '</table>';
echo '</form></div>';

include('Common/Templates/tail.php');

function get_modules() {
	$Modules=array();
	foreach(glob(dirname(__FILE__).'/*') as $file) {
		$dir=basename($file);
		if(!is_dir($file) or $dir=='Custom' or $dir=='Sets') continue;
		$version='';
		if(file_exists($file.'/menu.php')) @include($file.'/menu.php');
		$Modules[$dir]=$version;
	}
	return $Modules;
}

function get_sets() {
	$Modules=array();
	foreach(glob(dirname(__FILE__).'/Sets/*') as $file) {
		$dir=basename($file);
		if(!is_dir($file)) continue;
		$version='';
		if(file_exists($file.'/sets.php')) @include($file.'/sets.php');
		$Modules[$dir]=$version;
	}
	return $Modules;
}

?>