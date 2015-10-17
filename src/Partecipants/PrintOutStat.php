<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');


	$PAGE_TITLE=get_text('PrintList','Tournament');

	include('Common/Templates/head.php');

	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="3">' . get_text('PrintList','Tournament')  . '</th></tr>';
	echo '<tr>';
	echo '<th class="SubTitle" width="33%">' . get_text('StatClasses','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="34%">' . get_text('StatEvents','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="33%">' . get_text('StatCountries','Tournament')  . '</th>';
	echo '</tr>';
//Statistiche
	echo '<tr>';
//Completa
	echo '<td class="Center"><br><a href="PrnStatClasses.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf.gif" alt="' . get_text('StatClasses','Tournament') . '" border="0"><br>';
	echo get_text('StatClasses','Tournament');
	echo '</a></td>';
//Completa
	echo '<td class="Center"><br><a href="PrnStatEvents.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf.gif" alt="' . get_text('StatEvents','Tournament') . '" border="0">&nbsp;&nbsp;&nbsp;';
	echo '<a href="OrisStatEvents.php" class="Link" target="ORISPrintOut">';
	echo '<img src="../Common/Images/pdfOris.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="'.($_SESSION['ISORIS'] ? 'OrisStatEvents.php' : 'PrnStatEvents.php').'" class="Link" target="PrintOut">' . get_text('StatEvents','Tournament') . '</a>';
	echo '</td>';
//Completa
	echo '<td class="Center"><br><a href="PrnStatCountry.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf.gif" alt="' . get_text('StatCountries','Tournament') . '" border="0"></a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="OrisStatCountry.php" class="Link" target="ORISPrintOut">';
	echo '<img src="../Common/Images/pdfOris.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="'.($_SESSION['ISORIS'] ? 'OrisStatCountry.php' : 'PrnStatCountry.php').'" class="Link" target="PrintOut">' . get_text('StatCountries','Tournament') . '</a>';
	echo '</td>';
	echo '</tr>';
	//Comleanni
	echo '<tr class="Divider"><td colspan="3"></td></tr>';
	echo '<tr>';
	//Completa
	echo '<th class="SubTitle" width="33%">' . get_text('Birthdays','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="34%">&nbsp;</th>';
	echo '<th class="SubTitle" width="33%">&nbsp;</th>';
	echo '</tr>';

	echo '<tr>';
	//Completa
	echo '<td class="Center"><br><a href="PrnBirthday.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf.gif" alt="' . get_text('Birthdays','Tournament') . '" border="0"><br>';
	echo get_text('Birthdays','Tournament');
	echo '</a></td>';
	//FREE
	echo '<td>&nbsp;</td>';
	echo '<td>&nbsp;</td>';
	echo '</tr>';
	echo '</table>';

	include('Common/Templates/tail.php');
?>