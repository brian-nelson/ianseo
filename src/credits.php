<?php
require_once('./config.php');
require_once('Common/Fun_Modules.php');

$PAGE_TITLE='Credits';

include('Common/Templates/head.php');

echo '<table class="Tabella">';

echo '<tr><th class="Title" colspan="3">' . get_text('Credits-IanseoTeam', 'Install') . '</th></tr>'
	. '<tr>'
		. '<th class="SubTitle" nowrap="nowrap" colspan="2">'
		. '<a href="https://www.ianseo.net/" target="_blank"><img src="Common/Images/ianseo-logo.png" width="150" hspace="10" alt="Ianseo Logo" border="0"/></a>'
		. '</th>'
		. '<td width="100%" style="font-size:120%">'
		. '<div><b>Version: </b>'.ProgramVersion.'</div>'
		. (defined('ProgramBuild') ? '<div><b>Build: </b>'.ProgramBuild.'</div>' : '')
		. '<div>' . get_text('Credits-FitarcoCredits', 'Install') . '</div>'
		. '<div>' . get_text('Credits-IanseoWorld', 'Install') . '</div>'
		. '</td>'
	. '</tr>';
/** Credits Beiter **/
if(module_exists("Barcodes")) {
	echo '<tr>'
			. '<th class="SubTitle" nowrap="nowrap" colspan="2">'
			. '<a href="http://www.wernerbeiter.com/" target="_blank"><img src="Modules/Barcodes/beiter.png" width="150" hspace="10" alt="Beiter Logo" border="0"/></a>'
			. '</th>'
			. '<td width="100%" style="font-size:120%">'
			. '<div>' . get_text('Credits-BeiterCredits', 'Install') . '</div>'
			. '</td>'
		. '</tr>';
}		

/**  get the contributed credits **/
// First the localised rules
$glob=glob($CFG->DOCUMENT_PATH . 'Modules/Sets/*/credits.php');
if($glob) {
	foreach($glob as $file) {
		if($credit = get_credit_details($file)) {
			echo '<tr>'
					. '<th class="SubTitle" nowrap="nowrap" colspan="2">'
					. $credit->img
					. '</th>'
					. '<td width="100%" style="font-size:120%">'
					. $credit->txt
					. '</td>'
				. '</tr>';
		}
	}
}

echo '<tr><th class="Title" colspan="3">' . get_text('Credits-Credits', 'Install') . '</th></tr>';

include('Common/Languages/credits.php');

echo '<tr><th class="SubTitle" nowrap="nowrap" colspan="2">' . get_text('Credits-Documentation', 'Install') . '</th>'
	. '<td class="Bold">Ardingo Scarzella</td>'
	. '</tr>'
	. '<tr><th class="SubTitle" nowrap="nowrap" colspan="2">' . get_text('Credits-HHT', 'Install') . '</th>'
	. '<td><b>Matteo Pisani</b> <a href="https://www.ianseo.net/" target="_blank">https://www.ianseo.net/</a>
			<br/><b>Christian "Doc" Deligant</b> <a href="https://www.ianseo.net/" target="_blank">https://www.ianseo.net/</a></td>'
	. '</tr>'
	. '<tr><th class="SubTitle" nowrap="nowrap" colspan="2">' . get_text('Credits-ISK', 'Install') . '</th>'
	. '<td><b>Ken Sentell</b> Head of project
			<br/><b>Ianseo</b> <a href="https://www.ianseo.net/" target="_blank">https://www.ianseo.net/</a></td>'
	. '</tr>'
	. '<tr><th class="SubTitle" nowrap="nowrap" colspan="2">' . get_text('Credits-Coordination', 'Install') . '</th>'
	. '<td><b>Ianseo Team</b> <a href="https://www.ianseo.net/" target="_blank">https://www.ianseo.net/</a></td>'
	. '</tr>'
	. '<tr class="Divider"><td colspan="3"></td></tr>'
	. '<tr><th class="SubTitle" colspan="3">' . get_text('Credits-License', 'Install') . '</th></tr>'
	. '<tr><td class="Center" colspan="3"><a href="http://www.gnu.org" target="_blank"><img src="Common/Images/gplv3.png" alt="GPLv3" border="0"></a></td></tr>'
	. '</table>';

include('Common/Templates/tail.php');

function get_credit_details($file) {
	include($file);
	if(!empty($credit)) {
		return $credit;
	}
}
?>