<?php
$version='2013-03-10 14:00:00';

// $h=array_shift($ret['MODS']);
// $ret['MODS'] = array_merge(array('HELP' => array(), MENU_DIVIDER), $ret['MODS']);
// array_unshift($ret['MODS'], $h);

// array_splice($ret['MODS'], 1, 0, array('HELP' => '', MENU_DIVIDER));

$ret['HELP'] = array(
	get_text('HELP') ,
	get_text('HELP-Manual-ENG') .'|https://www.ianseo.net/Release/Manual_ENG.pdf|||Help',
	get_text('HELP-Tutorial-ENG') .'|http://www.youtube.com/playlist?list=PL65C62724683F2724|||Help',
	MENU_DIVIDER,
	get_text('HELP-Manual-ITA') .'|https://www.ianseo.net/Release/Manual_ITA.pdf|||Help',
	get_text('HELP-Tutorial-ITA') .'|http://www.youtube.com/playlist?list=PL0D973F92D9942E81|||Help',
	MENU_DIVIDER,
	get_text('HELP-RepairTables') .'|'.$CFG->ROOT_DIR.'Modules/Help/RepairTables.php',
// 	MENU_DIVIDER,
// 	get_text('HELP-SendDebug') .'|'.$CFG->ROOT_DIR.'Modules/Help/',
// 	get_text('HELP-LoadDebug') .'|'.$CFG->ROOT_DIR.'Modules/Help/LoadDebug.php',
	);

