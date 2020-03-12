<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclParticipants, AclReadOnly);
	require_once('Common/Fun_FormatText.inc.php');


	$PAGE_TITLE=get_text('PrintList','Tournament');

	include('Common/Templates/head.php');

	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="5">' . get_text('PrintList','Tournament')  . '</th></tr>';
	echo '<tr>';
	echo '<th class="SubTitle" width="20%">' . get_text('StatClasses','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="20%">' . get_text('StatEvents','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="20%">' . get_text('StatCountries','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="20%">' . get_text('StatRecordsStanding','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="20%">' . get_text('StatRecordsBroken','Tournament')  . '</th>';
	echo '</tr>';

//Stats
	echo '<tr>';
// Divisions and Classes
	echo '<td class="Center"><br><a href="PrnStatClasses.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf.gif" alt="' . get_text('StatClasses','Tournament') . '" border="0"><br>';
	echo get_text('StatClasses','Tournament');
	echo '</a></td>';

// Events
	echo '<td class="Center"><br><a href="PrnStatEvents.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf.gif" alt="' . get_text('StatEvents','Tournament') . '" border="0">&nbsp;&nbsp;&nbsp;';
	echo '<a href="OrisStatEvents.php" class="Link" target="ORISPrintOut">';
	echo '<img src="../Common/Images/pdfOris.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="'.($_SESSION['ISORIS'] ? 'OrisStatEvents.php' : 'PrnStatEvents.php').'" class="Link" target="PrintOut">' . get_text('StatEvents','Tournament') . '</a>';
	echo '</td>';

// Country/Clubs
	echo '<td class="Center"><br><a href="PrnStatCountry.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf.gif" alt="' . get_text('StatCountries','Tournament') . '" border="0"></a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="OrisStatCountry.php" class="Link" target="ORISPrintOut">';
	echo '<img src="../Common/Images/pdfOris.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="'.($_SESSION['ISORIS'] ? 'OrisStatCountry.php' : 'PrnStatCountry.php').'" class="Link" target="PrintOut">' . get_text('StatCountries','Tournament') . '</a>';
	echo '</td>';

// Standing records
	echo '<td class="Center"><br>';
	echo '<a href="OrisStatRecStanding.php" class="Link" target="ORISPrintOut">';
	echo '<img src="../Common/Images/pdfOris.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="OrisStatRecStanding.php" class="Link" target="PrintOut">' . get_text('StatRecordsStanding','Tournament') . '</a>';
	echo '</td>';

// Broken Records
	echo '<td class="Center"><br>';
	echo '<a href="OrisStatRecBroken.php" class="Link" target="ORISPrintOut">';
	echo '<img src="../Common/Images/pdfOris.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="OrisStatRecBroken.php" class="Link" target="PrintOut">' . get_text('StatRecordsBroken','Tournament') . '</a>';
	echo '</td>';

	echo '</tr>';

//Compleanni
	echo '<tr class="Divider"><td colspan="5"></td></tr>';
	echo '<tr>';
//Completa
	echo '<th class="SubTitle" colspan="5">' . get_text('Birthdays','Tournament')  . '</th>';
	echo '</tr>';

	echo '<tr>';
	//Completa
	echo '<td class="Center" colspan="5"><br><a href="PrnBirthday.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf.gif" alt="' . get_text('Birthdays','Tournament') . '" border="0"><br>';
	echo get_text('Birthdays','Tournament');
	echo '</a></td>';
	//FREE
	echo '</tr>';
	echo '</table>';

	include('Common/Templates/tail.php');
?>