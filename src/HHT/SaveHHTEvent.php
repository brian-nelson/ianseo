<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Fun_HHT.local.inc.php');

if (!CheckTourSession() || !isset($_REQUEST['Id']) || !isset($_REQUEST['Event']) || !isset($_REQUEST['Value']))
{
	print get_text('CrackError');
	exit;
}

$Errore=0;

if (!(IsBlocked(BIT_BLOCK_QUAL) && IsBlocked(BIT_BLOCK_IND) && IsBlocked(BIT_BLOCK_TEAM)))
{
	$select = "SELECT HeEventCode FROM HhtEvents WHERE HeEventCode=" . StrSafe_DB($_REQUEST['Event'])
		. " AND HeTournament=" . StrSafe_DB($_SESSION['TourId'])
		. " AND HeHhtId=" . StrSafe_DB($_REQUEST['Id']);
	$rs=safe_r_sql($select);
	if (safe_num_rows($rs)==0 && $_REQUEST['Value']=="true")
	{
		list($what,$ses,$dist)=phaseDecode($_REQUEST['Event']);
		safe_w_sql("INSERT INTO HhtEvents set HeTournament=" . StrSafe_DB($_SESSION['TourId'])
			. ", HeEventCode=" . StrSafe_DB($_REQUEST['Event'])
			. ", HeHhtId=" . StrSafe_DB($_REQUEST['Id'])
			. ", HeSession=" . ($what==-1? $ses : 0)
			. ", HeFinSchedule=" . ($what!=-1? strSafe_DB($ses) : '0')
			. ", HeTeamEvent=" . ($what==-1 ? 0 : $what));
	}
	else if(safe_num_rows($rs)==1 && $_REQUEST['Value']=="false")
		safe_w_sql("Delete From HhtEvents WHERE HeTournament="
		. StrSafe_DB($_SESSION['TourId']) . " AND HeEventCode="
		. StrSafe_DB($_REQUEST['Event']) . " AND HeHhtId=" . StrSafe_DB($_REQUEST['Id']));
	else
		$Errore=1;
}
else
	$Errore=1;

header('Content-Type: text/xml');

print '<response>' . "\n";
print '<error>' . $Errore . '</error>' . "\n";
print '<id>' . $_REQUEST['Id'] . '</id>' . "\n";
print '<event>' . $_REQUEST['Event'] . '</event>' . "\n";
print '<enabled>' . $_REQUEST['Value'] . '</enabled>' . "\n";
print '</response>' . "\n";

?>