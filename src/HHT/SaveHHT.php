<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');

if (!CheckTourSession() || !isset($_REQUEST['Name']) || !isset($_REQUEST['IpAddress']) || !isset($_REQUEST['Port']))
{
	print get_text('CrackError');
	exit;
}

$Errore=0;
$newID=-1;

if (!(IsBlocked(BIT_BLOCK_QUAL) && IsBlocked(BIT_BLOCK_IND) && IsBlocked(BIT_BLOCK_TEAM)))
{
	if(strlen(trim($_REQUEST['Name']))==0 || strlen(trim($_REQUEST['IpAddress']))==0 || strlen(trim($_REQUEST['Port']))==0)
	{
		$Errore=1;
	}
	else
	{
		$select = "SELECT HsId FROM HhtSetup WHERE HsIpAddress=" . StrSafe_DB($_REQUEST['IpAddress']) . " AND HsTournament=" . StrSafe_DB($_SESSION['TourId']);
		$rs=safe_r_sql($select);
	//	echo $select;
		if (($rs && safe_num_rows($rs)!=0))
			$Errore=1;
		else
		{
			$q=safe_r_sql("SELECT IFNULL(MAX(HsId),0) AS CurID FROM HhtSetup WHERE HsTournament=" . StrSafe_DB($_SESSION['TourId']));
			$newID = (safe_fetch($q)->CurID)+1;
			safe_w_sql("INSERT INTO HhtSetup set HsId=$newID, HsTournament=" . StrSafe_DB($_SESSION['TourId']) . ", HsIpAddress=" . StrSafe_DB($_REQUEST['IpAddress']) . ", HsPort=" . StrSafe_DB($_REQUEST['Port']) . ", HsName=". StrSafe_DB($_REQUEST['Name']) . ", HsMode=3, HsFlags='NNNNNNNNNNNNNNNN', HsPhase='0', HsSequence='0103011006', HsDistance=1");
		}
	}	
}
else
	$Errore=1;
	
header('Content-Type: text/xml');

print '<response>' . "\n";
print '<error>' . $Errore . '</error>' . "\n";
print '<id>' . $newID . '</id>' . "\n";
print '<name>' . $_REQUEST['Name'] . '</name>' . "\n";
print '<ip>' . $_REQUEST['IpAddress'] . '</ip>' . "\n";
print '<port>' . $_REQUEST['Port'] . '</port>' . "\n";
print '</response>' . "\n";
	
?>