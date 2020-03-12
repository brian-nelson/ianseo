<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

/****** Controller ******/
CheckTourSession(true);
checkACL(AclParticipants, AclReadWrite, false);

$error = 1;

if (!IsBlocked(BIT_BLOCK_PARTICIPANT)) {

	$t=safe_r_sql("select ToIocCode from Tournament WHERE ToId={$_SESSION['TourId']}");
	$u=safe_fetch($t);

	// aggiungo la riga in Entries
	$query = "INSERT INTO Entries
		set EnTournament = {$_SESSION['TourId']},
		EnIocCode='{$u->ToIocCode}'";
	$rs=safe_w_sql($query);
	$EnId=safe_w_last_id();

	$query = "INSERT INTO Qualifications set QuId=".$EnId;
	$rs=safe_w_sql($query);

	if($EnSelect=GetAccBoothEnWhere($EnId, true, true)) {
		LogAccBoothQuerry("insert into Entries set EnIocCode='{$u->ToIocCode}', EnTournament=§TOCODETOID§");
		LogAccBoothQuerry("insert into Qualifications set QuId=(select EnId from Entries where $EnSelect)");
	}

	$error=0;
}
/****** End Controller ******/

/****** Output ******/
$xmlDoc=new DOMDocument('1.0',PageEncode);
	$xmlRoot=$xmlDoc->createElement('response');
	$xmlDoc->appendChild($xmlRoot);

// Header
	$xmlHeader=$xmlDoc->createElement('header');
	$xmlRoot->appendChild($xmlHeader);

	$node=$xmlDoc->createElement('error',$error);
	$xmlHeader->appendChild($node);

	$node=$xmlDoc->createElement('new_id', $EnId);
	$xmlRoot->appendChild($node);

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

print $xmlDoc->saveXML();
/****** End OUtput ******/
