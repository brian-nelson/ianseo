<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');

CheckTourSession(true);
checkACL(AclQualification, AclReadOnly);

$ToCode = '';

$Select = "SELECT ToCode "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
$Rs=safe_r_sql($Select);

if ($row=safe_fetch($Rs)) {
	$ToCode=$row->ToCode;
}

$MyQuery = "SELECT *
		from Qualifications
		inner join Entries on EnId=QuId
		inner join Countries on EnCountry=CoId
		where EnTournament={$_SESSION['TourId']} and QuScore>0";
$Rs=safe_r_sql($MyQuery);

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-Disposition: attachment; filename=' . ($ToCode!='' ? $ToCode : 'export') . '.csv');
header('Content-type: text/tab-separated-values; charset=' . PageEncode);

echo get_text('FamilyName','Tournament') . "\t"
	. get_text('Name','Tournament') . "\t"
	. get_text('Country') . "\t"
	. get_text('Div') . "\t"
	. get_text('Cl') . "\t";
for ($i=1;$i<=2;++$i) {
	for($j=1; $j<=24; $j++) {
		echo get_text('Peg', 'Tournament') . ' ' . $i . "\t";
	}
	echo get_text('TotaleScore') . '-' . $i . "\t"
		. ("6") . '-' . $i . "\t"
		. ("5") . '-' . $i . "\t";
}

echo get_text('TotaleScore') . "\t"
	. ("Tot 6")  . "\t"
	. ("Tot 5") . "\n";

while ($MyRow=safe_fetch($Rs)) {
	echo $MyRow->EnFirstName . "\t"
		. $MyRow->EnName . "\t"
		. $MyRow->CoCode . "\t"
		. $MyRow->EnDivision . "\t"
		. $MyRow->EnClass . "\t";

	for ($i=1;$i<=2;++$i) {
		for($j=1; $j<=24; $j++) {
			echo ValutaArrowString(substr($MyRow->{"QuD{$i}Arrowstring"}, 3*($j-1), 3)) . "\t";
		}
		echo $MyRow->{"QuD{$i}Score"} . "\t"
			. $MyRow->{"QuD{$i}Gold"} . "\t"
			. $MyRow->{"QuD{$i}Xnine"} . "\t";
	}
	echo $MyRow->QuScore . "\t"
		. $MyRow->QuGold . "\t"
		. $MyRow->QuXnine . "\n";
}
