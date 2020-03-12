<?php
$version='2013-05-05 16:13:00';


if ($on and !defined('hideSpeaker') AND $acl[AclSpeaker] >= AclReadOnly) {
	$ret['PART'][] = MENU_DIVIDER;
	$ret['PART'][] = get_text('MenuLM_Speaker') . '|' . $CFG->ROOT_DIR.'Modules/Speaker/participants.php';

	$ret['QUAL'][] = MENU_DIVIDER;
	$ret['QUAL'][] = get_text('MenuLM_Speaker') . '|' . $CFG->ROOT_DIR.'Modules/Speaker/qualification.php';

	if(!empty($ret['ELIM'])) {
		$ret['ELIM'][] = MENU_DIVIDER;
		$ret['ELIM'][] = get_text('MenuLM_Speaker') . '|' . $CFG->ROOT_DIR.'Modules/Speaker/elimination.php';
	}

	if(!empty($_SESSION['MenuFinIOn'])) {
		$ret['FINI'][] = MENU_DIVIDER;
		$ret['FINI'][] = get_text('MenuLM_Speaker') . '|' . $CFG->ROOT_DIR.'Modules/Speaker/index.php';
	}

	if(!empty($_SESSION['MenuFinTOn'])) {
		$ret['FINT'][] = MENU_DIVIDER;
		$ret['FINT'][] = get_text('MenuLM_Speaker') . '|' . $CFG->ROOT_DIR.'Modules/Speaker/index.php';
	}
}

