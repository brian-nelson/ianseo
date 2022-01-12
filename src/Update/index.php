<?php

require_once(dirname(dirname($_SERVER['SCRIPT_FILENAME'])) . '/config.php');
checkACL(AclRoot, AclReadWrite);
checkGPL(true);

if(!empty($_SESSION['AUTH_ENABLE']) AND empty($_SESSION['AUTH_ROOT'])) {
    CD_redirect($CFG->ROOT_DIR.'noAccess.php');
}

require_once('Common/Lib/CommonLib.php');

$JS_SCRIPT=array(
    phpVars2js(array(
        'cmdClose' => get_text('Close'),
        'cmdForceUpdate' => get_text('cmdForceUpdate','Install'),
    )),
    '<script src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
    '<script src="'.$CFG->ROOT_DIR.'Common/js/jquery-confirm.min.js"></script>',
    '<link rel="stylesheet" href="'.$CFG->ROOT_DIR.'Common/css/jquery-confirm.min.css">',
    '<link rel="stylesheet" href="index.css">',
    '<script src="./index.js"></script>',
);

include('Common/Templates/head.php');

$f=@fopen($CFG->DOCUMENT_PATH.'check', 'w');
if($f) {
	echo '<div align="center">';

	echo '<table class="Tabella" style="width:50%">';
	echo '<tr>'
		. '<td colspan="2">'.get_text('UpdatePrepared', 'Install').'</td>'
		. '</tr>';

	if(!in_array(ProgramRelease, array('STABLE','FITARCO')) or isset($_GET['testing'])) {
		@include('Modules/IanseoTeam/IanseoFeatures/isIanseoTeam.php');
		echo '<tr>'
			. '<th colspan="2">'.get_text('SpecialUpdate', 'Install').'</th>'
			. '</tr>';

		echo '<tr>'
			. '<th>' . get_text('Email','Install') . '</th>'
			. '<td><input type="text" name="Email" id="Email"  style="width:100%"></td>'
			. '</tr>';

//		echo '<tr>'
//			. '<th>' . get_text('Password','Install') . '</th>'
//			. '<td><input type="password" name="Password" id="Password"  style="width:100%"></td>'
//			. '</tr>';
	}

	echo '<tr>'
		. '<td class="Center" colspan="2"><input type="button" value="' . get_text('CmdOk') . '" onclick="doUpdate()"></td>'
		. '</tr>';
	echo '</table>';
	echo '</form>';

	echo '</div>';
	fclose($f);
	unlink($CFG->DOCUMENT_PATH.'check');
} else {
	echo '<div align="center">';
	echo '<table class="Tabella" style="width:50%">';
	echo '<tr>'
		. '<td colspan="2">'.get_text('NotUpdatable', 'Install').'</td>'
		. '</tr>';
	echo '</table>';
	echo '</div>';

}


include('Common/Templates/tail.php');
