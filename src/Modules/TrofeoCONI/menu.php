<?php
$version='2011-05-13 08:13:00';

if(!empty($on) AND $_SESSION["TourLocRule"]=='IT') {
	/** CONI MENU **/
	if($_SESSION['MenuFinTDo'] && $_SESSION['TourType']==33)
	{
		$ret['MODS'][] = MENU_DIVIDER;
		$ret['MODS']['CONI'][] = 'Trofeo CONI';
		$ret['MODS']['CONI'][] = get_text('MenuLM_ShootOf4Cas1') . '|' . $CFG->ROOT_DIR . 'Modules/TrofeoCONI/AbsTeam1_1.php';
		$ret['MODS']['CONI'][] = get_text('MenuLM_TargetAssignmentFirst') . '|' . $CFG->ROOT_DIR . 'Modules/TrofeoCONI/ManTarget1_1.php';
		$ret['MODS']['CONI'][] = get_text('MenuLM_InsertPointFirst') .'|' . $CFG->ROOT_DIR . 'Modules/TrofeoCONI/Score1_1.php';
		$ret['MODS']['CONI'][] = MENU_DIVIDER;
		$ret['MODS']['CONI'][] = get_text('MenuLM_ShootOf4Cas2') . '|' . $CFG->ROOT_DIR . 'Modules/TrofeoCONI/AbsTeam1_2.php';
		$ret['MODS']['CONI'][] = get_text('MenuLM_TargetAssignmentSecond') . '|' . $CFG->ROOT_DIR . 'Modules/TrofeoCONI/ManTarget1_2.php';
		$ret['MODS']['CONI'][] = get_text('MenuLM_InsertPointSecond') . '|' . $CFG->ROOT_DIR . 'Modules/TrofeoCONI/Score1_2.php';
		$ret['MODS']['CONI'][] = MENU_DIVIDER;
		$ret['MODS']['CONI'][] = get_text('MenuLM_LastShootoff') . '|' . $CFG->ROOT_DIR . 'Modules/TrofeoCONI/AbsTeam1_3.php';
		$ret['MODS']['CONI'][] = MENU_DIVIDER;
		$ret['MODS']['CONI'][] = get_text('MenuLM_Scorecard Printout') .'|'.$CFG->ROOT_DIR.'Modules/TrofeoCONI/PrintScore.php';
		$ret['MODS']['CONI'][] = MENU_DIVIDER;
		$ret['MODS']['CONI'][] = get_text('MenuLM_Printout') .'|'.$CFG->ROOT_DIR.'Modules/TrofeoCONI/PrintOut.php';
	}

}
?>