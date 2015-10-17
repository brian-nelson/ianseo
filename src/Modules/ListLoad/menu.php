<?php
$version='2011-05-13 08:13:00';


if(!empty($on)) {
	$ret['PART']['SYNC'] = array(
		get_text('MenuLM_Athletes Sync.') .'|'.$CFG->ROOT_DIR.'Partecipants/LookupTableLoad.php',
		get_text('MenuLM_Athletes Sync.') .'|'.$CFG->ROOT_DIR.'Partecipants/LookupTableLoad.php',
		get_text('MenuLM_ListLoad') .'|'.$CFG->ROOT_DIR.'Modules/ListLoad/ListLoad.php'
	);
}

?>