<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');

	include('Common/Templates/head.php');

	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="2">' . get_text('PrintList','Tournament')  . '</th></tr>';
	print '<tr><th colspan="2">' . get_text('GroupMatches','Tournament') . ' - ' . get_text('FirstPhase','Tournament') . '</th></tr>' . "\n";
	print '<tr>';

		print '<td class="Center" style="width:50%;"><br>';
		print '<a class="Link" target="PrintOutOris" href="OrisGroupMatch.php?Phase=1"><img src="../../Common/Images/pdfOris.gif" alt="' . get_text('AllRound','Tournament') . '" border="0"></a>';
		print '<br/><a class="Link" target="PrintOutOris" href="OrisGroupMatch.php?Phase=1">' . get_text('AllRound','Tournament') . '</a>';
		print '</td>';

		print '<td class="Center"><br>';
		print '<a class="Link" target="PrintOutOris" href="OrisGroupRank.php"><img src="../../Common/Images/pdfOris.gif" alt="' . get_text('AllRound','Tournament') . '" border="0"></a>';
		print '<br/><a class="Link" target="PrintOutOris" href="OrisGroupRank.php">' . get_text('Rankings') . '</a>';
		print '</td>';



	print '<tr><th colspan="2">' . get_text('GroupMatches','Tournament') . ' - ' . get_text('SecondPhase','Tournament') . '</th></tr>' . "\n";
	print '</tr>' . "\n";

	print '<tr>';
		print '<td class="Center"><br>';
		print '<a class="Link" target="PrintOutOris" href="OrisGroupMatch.php?Phase=2"><img src="../../Common/Images/pdfOris.gif" alt="' . get_text('AllRound','Tournament') . '" border="0"></a>';
		print '<br/><a class="Link" target="PrintOutOris" href="OrisGroupMatch.php?Phase=2">' . get_text('AllRound','Tournament') . '</a>';
		print '</td>';

		print '<td class="Center"><br>';
		print '<a class="Link" target="PrintOutOris" href="OrisGroupRank2.php"><img src="../../Common/Images/pdfOris.gif" alt="' . get_text('AllRound','Tournament') . '" border="0"></a>';
		print '<br/><a class="Link" target="PrintOutOris" href="OrisGroupRank2.php">' . get_text('Rankings') . '</a>';
		print '</td>';

	print '</tr>' . "\n";

	print '<tr><th colspan="2">' . get_text('Rankings') . '</th></tr>' . "\n";
	print '<tr>';
		print '<td class="Center" colspan="2"><br>';
		print '<a class="Link" target="PrintOutOris" href="OrisFinalRank.php"><img src="../../Common/Images/pdfOris.gif" alt="' . get_text('AllRound','Tournament') . '" border="0"></a>';
		print '<br/><a class="Link" target="PrintOutOris" href="OrisFinalRank.php">' . get_text('Rankings') . '</a>';
		print '</td>';



	print '</tr>' . "\n";

	echo '</table>' . "\n";


	include('Common/Templates/tail.php');
?>