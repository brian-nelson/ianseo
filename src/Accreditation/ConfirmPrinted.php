<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclParticipants, AclReadOnly, false);

$CardType=(empty($_REQUEST['CardType']) ? 'A' : $_REQUEST['CardType']);
$CardNumber=(empty($_REQUEST['CardNumber']) ? 0 : intval($_REQUEST['CardNumber']));

$RemoteIP=($_SERVER['REMOTE_ADDR']!='::1' ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');
$Operation=($CardType=='A' ? 1 : (ord($CardType)*100)+$CardNumber);

$Now=date('Y-m-d H:i:s');
$TourId=$_SESSION['TourId'];

switch($CardType) {
	case 'A':
		$WHERE='';
		if(!empty($_REQUEST['Entries'])) {
			$tmp=array();
			foreach($_REQUEST['Entries'] as $v) {
				$tmp[]=intval($v);
			}
			$WHERE='('.implode(',', $tmp).')';
		}
		if($_SESSION['AccreditationTourIds']) $TourId=$_SESSION['AccreditationTourIds'];
		$SQL="update Entries set EnBadgePrinted='$Now', EnTimestamp=EnTimestamp where EnId in ".($WHERE ? $WHERE : "(select AEId from AccEntries where AEOperation=-{$Operation} and AETournament in ($TourId) and AEFromIp=INET_ATON('$RemoteIP'))");
		if($_SESSION['AccBooth']) {
			// we need to log every single Entry...
			$q=safe_r_sql("select EnCode, EnIocCode, EnDivision, ToCode from Entries inner join Tournament on EnTournament=ToId where EnId in ".($WHERE ? $WHERE : "(select AEId from AccEntries where AEOperation=-{$Operation} and AETournament in ($TourId) and AEFromIp=INET_ATON('$RemoteIP'))"));
			while($r=safe_fetch($q)) {
				LogAccBoothQuerry("update Entries
						set EnBadgePrinted='$Now', EnTimestamp=EnTimestamp
						where EnCode='$r->EnCode' and EnIocCode='$r->EnIocCode' and EnDivision='$r->EnDivision' and EnTournament=§TOCODETOID§", $r->ToCode);
			}
		}
		break;
	case 'Q':
		$SQL="update Qualifications set QuBacknoPrinted='$Now', QuTimestamp=QuTimestamp where QuId in (select AEId from AccEntries where AEOperation=-{$Operation} and AETournament={$_SESSION['TourId']} and AEFromIp=INET_ATON('$RemoteIP')) ";
		break;
	case 'E':
		$SQL="update Eliminations set ElBacknoPrinted='$Now', ElDateTime=ElDateTime where (ElId, concat(ElEventCode, ElElimPhase)) in (select AEId, AEExtra from AccEntries where AEOperation=-{$Operation} and AETournament={$_SESSION['TourId']} and AEFromIp=INET_ATON('$RemoteIP')) ";
		break;
	case 'I':
		$SQL="update Individuals set IndBacknoPrinted='$Now', IndTimestamp=IndTimestamp, IndTimestampFinal=IndTimestampFinal where (IndId, IndEvent) in (select AEId, AEExtra from AccEntries where AEOperation=-{$Operation} and AETournament={$_SESSION['TourId']} and AEFromIp=INET_ATON('$RemoteIP')) ";
		break;
	case 'T':
		$SQL="update Teams set TeBacknoPrinted='$Now', TeTimestamp=TeTimestamp, TeTimestampFinal=TeTimestampFinal where (TeCoId, concat(TeEvent, TeSubTeam)) in (select AEId, AEExtra from AccEntries where AEOperation=-{$Operation} and AETournament={$_SESSION['TourId']} and AEFromIp=INET_ATON('$RemoteIP')) ";
		break;
}

safe_w_sql($SQL);

safe_w_sql("delete from AccEntries where AEOperation=-{$Operation} and AEFromIp=INET_ATON('$RemoteIP') and AETournament={$_SESSION['TourId']}");

$xmlDoc=new DOMDocument('1.0','UTF-8');
$xmlRoot=$xmlDoc->createElement('response');
$xmlDoc->appendChild($xmlRoot);

$xmlRule=$xmlDoc->createElement('error', '0');
$xmlRoot->appendChild($xmlRule);

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

print $xmlDoc->saveXML();
