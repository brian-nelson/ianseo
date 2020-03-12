<?php
$version='2013-03-24 14:13:00';

if($on AND $_SESSION["TourLocRule"]=='IT') {
	$ret['PRNT'][] = MENU_DIVIDER;
	$ret['PRNT'][] = get_text('MenuLM_SelectionRank') .'|'.$CFG->ROOT_DIR.'Modules/Average/';
}
