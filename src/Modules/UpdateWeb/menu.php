<?php
$version='2011-05-13 08:13:00';


if(!empty($on)) {
	$ret['COMP']['SEND'][] = MENU_DIVIDER;
	$ret['COMP']['SEND'][] = get_text('MenuLM_UpdateWeb') .'|'.$CFG->ROOT_DIR.'Modules/UpdateWeb/UpdateWeb.php';
}

?>