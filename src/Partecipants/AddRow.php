<?php
/*
													- AddRow.php -
	Inserisce una riga nella tabella delle entries e aggiunge una riga bianca nella tabella HTML
	prepando i campi con gli id giusti
*/

define('debug',false);

require_once(dirname(dirname(__FILE__)) . '/config.php');

CheckTourSession(true);
checkACL(AclParticipants, AclReadWrite, false);

$Errore = 1;

if (!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
	$Errore=0;
	$t=safe_r_sql("select ToIocCode from Tournament WHERE ToId={$_SESSION['TourId']}");
	$u=safe_fetch($t);
	safe_w_sql("Insert into Entries set EnTournament='{$_SESSION['TourId']}', EnIocCode='{$u->ToIocCode}'");
	safe_w_sql("Insert into Qualifications set QuId=".safe_w_last_id());

	// le query che seguono mi servono per generare la tendine dinamiche
	$xml = '';
	$Select = "SELECT ToNumSession FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_r_sql($Select);
	$Row=safe_fetch($Rs);
	for ($i=1;$i<=$Row->ToNumSession; $i++) {
		$xml .= '<sessions>' . $i . '</sessions>';
	}

	$Select = "SELECT DivId FROM Divisions WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY DivViewOrder ASC ";
	$Rs=safe_r_sql($Select);
	while ($Row=safe_fetch($Rs)) {
		$xml .= '<divisions>' . $Row->DivId . '</divisions>';
	}

	$Arr_Cl = array();
	$Select = "SELECT ClId FROM Classes WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ClViewOrder ASC ";
	$Rs=safe_r_sql($Select);
	while ($Row=safe_fetch($Rs)) {
		$xml .= '<classes>' . $Row->ClId . '</classes>';
	}

	$Arr_SubCl = array();
	$Select = "SELECT ScId FROM SubClass WHERE ScTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ScViewOrder ASC ";
	$Rs=safe_r_sql($Select);
	while ($Row=safe_fetch($Rs)) {
		$xml .= '<sub_classes>' . $Row->ScId . '</sub_classes>';
	}
}

header('Content-Type: text/xml');

print '<response>';
print '<error>' . $Errore . '</error>';
print '<new_id>' . $NewId . '</new_id>';
print $xml;
print '<confirm_msg1><![CDATA[' . get_text('Archer') . ']]></confirm_msg1>';
print '<confirm_msg2><![CDATA['	. get_text('Country') . ']]></confirm_msg2>';
print '<confirm_msg3><![CDATA[' . get_text('OpDelete','Tournament') . ']]></confirm_msg3>';
print '<confirm_msg4><![CDATA[' . get_text('MsgAreYouSure') . ']]></confirm_msg4>';
print '</response>';

