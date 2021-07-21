<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php

echo '<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>';

if(!empty($PAGE_TITLE)) echo $PAGE_TITLE . ' - ';
print ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '');

echo '</title>';

$local_JS = array(
    '<link href="'.$CFG->ROOT_DIR.'Common/Styles/Blue_screen.css" media="screen" rel="stylesheet" type="text/css">',
    '<link href="'.$CFG->ROOT_DIR.'Common/Styles/Blue_screen-print.css" media="print" rel="stylesheet" type="text/css">',
    '<link href="'.$CFG->ROOT_DIR.'Common/Styles/Menu.css" rel="stylesheet" type="text/css">',
);

if(!empty($IncludeFA)) {
	$local_JS[]= '<link href="'.$CFG->ROOT_DIR.'Common/css/font-awesome.css" rel="stylesheet" type="text/css">';
}

require_once('Common/Menu.php');

if($_SESSION['debug']) {
	$local_JS[]= '<link href="'.$CFG->ROOT_DIR.'Common/Styles/Blue_screen_debug.css" rel="stylesheet" type="text/css">';
}

if(SelectLanguage()=='tlh'){
	$local_JS[]= '<link href="'.$CFG->ROOT_DIR.'Common/Styles/klingon.css" rel="stylesheet" type="text/css">';
}

if(empty($JS_SCRIPT)) {
    $JS_SCRIPT=array();
}
$JS_SCRIPT = array_merge($local_JS, $JS_SCRIPT);

foreach($JS_SCRIPT as $script) {
	if(preg_match('#(src|href)="(\.){0,1}/(.*?)(\.js|\.css)"#i',$script, $matches)) {
		$file = ($matches[2] == '.' ? getcwd() : $_SERVER["DOCUMENT_ROOT"] ) . '/'. $matches[3] . $matches[4];
		if(file_exists($file)){
			$mtime = filemtime($file);
			$script = str_replace($matches[4],  $matches[4].'?ts=' . $mtime, $script);
		}
	}
	echo "$script\n";
}

echo '</head>';
echo '<body'.(!empty($ONLOAD)?$ONLOAD:'').'>';
echo '<div id="TourInfo">';
echo '';


InfoTournament();

?>
</div>
<div id="navigation">
<?php

// $mid->printMenu('hormenu1');

PrintMenu();

?>
</div>
<div id="Content">
