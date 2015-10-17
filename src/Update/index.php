<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
include('Common/Templates/head.php');

$f=@fopen($CFG->DOCUMENT_PATH.'check', 'w');
if($f) {
	echo '<div align="center">';
	echo '<form name="FrmParam" method="POST" action="UpdateIanseo.php">';
	echo '<table class="Tabella" style="width:50%">';
	echo '<tr>'
		. '<td colspan="2">'.get_text('UpdatePrepared', 'Install').'</td>'
		. '</tr>';

	if(!in_array(ProgramRelease, array('STABLE','FITARCO'))) {
		echo '<tr>'
			. '<th colspan="2">'.get_text('SpecialUpdate', 'Install').'</th>'
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
	echo '</form>';
	echo '</div>';
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

?>