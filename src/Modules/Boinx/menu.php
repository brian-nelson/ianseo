<?php
$version='2011-05-13 08:13:00';

if(!empty($on)) {
	if(end($ret['MEDI']) != MENU_DIVIDER) {
		$ret['MEDI'][] = MENU_DIVIDER;
	}
	$ret['MEDI'][] = 'ManageTvFlags|'.$CFG->ROOT_DIR.'Modules/Boinx/ManageTVFlags.php';
	$ret['MEDI'][] = 'ShowTvFlags|'.$CFG->ROOT_DIR.'Modules/Boinx/ShowTVFlags.php?Tour='.$_SESSION['TourCode'].'|||TV';

	if(end($ret['MEDI']) != MENU_DIVIDER) {
		$ret['MEDI'][] = MENU_DIVIDER;
	}
	$ret['MEDI'][] = get_text('MenuLM_Boinx') .'|'.$CFG->ROOT_DIR.'Modules/Boinx/';
//	$ret['MEDI'][] = get_text('MenuLM_BoinxMeteo') .'|'.$CFG->ROOT_DIR.'Modules/Boinx/IanseoMeteo.php';
	$ret['MEDI'][] = get_text('MenuLM_BoinxSchedule') .'|'. $CFG->ROOT_DIR.'Modules/Boinx/Schedule.php';
}
?>