<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['rowid']) ||
		!isset($_REQUEST['cl']) ||
		!isset($_REQUEST['ath']) ||
		!isset($_REQUEST['col']) ||
		!isset($_REQUEST['titlereverse']) ||
		!isset($_REQUEST['area0']) ||
		!isset($_REQUEST['area1']) ||
		!isset($_REQUEST['area2']) ||
		!isset($_REQUEST['area3']) ||
		!isset($_REQUEST['area4']) ||
		!isset($_REQUEST['area5']) ||
		!isset($_REQUEST['area6']) ||
		!isset($_REQUEST['area7']) ||
		!isset($_REQUEST['areastar']) ||
		!isset($_REQUEST['transport']) ||
		!isset($_REQUEST['accomodation']) ||
		!isset($_REQUEST['meal']))
	{
		print get_text('CrackError');
		exit;
	}
	checkACL(AclCompetition, AclReadWrite, false);

	$Errore=0;

	if (!IsBlocked(BIT_BLOCK_ACCREDITATION))
	{
		if ($_REQUEST['cl']!='' &&
		preg_match('/^[01]{1,1}$/i',$_REQUEST['ath']) &&
		preg_match('/^#[0-9A-Za-z]{6}$/i',$_REQUEST['col']))
		{
			$query
				= "REPLACE INTO AccColors (AcTournament,AcDivClass,AcColor,AcIsAthlete,AcTitleReverse, "
				. "AcArea0,AcArea1,AcArea2,AcArea3,AcArea4,AcArea5,AcArea6,AcArea7,AcAreaStar, "
				. "AcTransport, AcAccomodation, AcMeal) "
				. "VALUES("
					. StrSafe_DB($_SESSION['TourId']) . ","
					. StrSafe_DB($_REQUEST['cl']) . ","
					. StrSafe_DB(substr($_REQUEST['col'],1)) . ","
					. StrSafe_DB($_REQUEST['ath']) . ", "
					. StrSafe_DB($_REQUEST['titlereverse']) . ", "
					. StrSafe_DB($_REQUEST['area0']) . ", "
					. StrSafe_DB($_REQUEST['area1']) . ", "
					. StrSafe_DB($_REQUEST['area2']) . ", "
					. StrSafe_DB($_REQUEST['area3']) . ", "
					. StrSafe_DB($_REQUEST['area4']) . ", "
					. StrSafe_DB($_REQUEST['area5']) . ", "
					. StrSafe_DB($_REQUEST['area6']) . ", "
					. StrSafe_DB($_REQUEST['area7']) . ", "
					. StrSafe_DB($_REQUEST['areastar']) . ", "
					. StrSafe_DB($_REQUEST['transport']) . ", "
					. StrSafe_DB($_REQUEST['accomodation']) . ", "
					. StrSafe_DB($_REQUEST['meal']) . ") ";
			//print $query;exit;
			$rs=safe_w_sql($query);
			if (!$rs)
				$Errore=1;
		}
		else
			$Errore=1;
	}
	else
		$Errore=1;

	header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<rowid>' . $_REQUEST['rowid'] . '</rowid>';
	print '<cl>' . $_REQUEST['cl'] . '</cl>';
	print '<ath>' . ($_REQUEST['ath']==1 ? get_text('Yes') : get_text('No')) . '</ath>' . "\n";
	print '<titlereverse>' . ($_REQUEST['titlereverse']==1 ? get_text('Yes') : get_text('No')) . '</titlereverse>' . "\n";
	print '<col>' . substr($_REQUEST['col'],1) . '</col>' . "\n";
	print '<area><![CDATA[';
	$tmp=array();
	for($i=0;$i<=7;$i++) {
		if($_REQUEST['area' . $i] == 1) {
			$tmp[]= $i . (($i<2 and $_REQUEST['areastar'] == 1) ? '*' : '');
		}
	}
	print implode('&nbsp;&nbsp;&nbsp', $tmp);
	print ']]></area>' . "\n";
	print '<transport>' . ($_REQUEST['transport']!=0 ? get_text('Transport_' . $_REQUEST['transport'], 'Tournament') : get_text('No')) . '</transport>' . "\n";
	print '<accomodation>' . ($_REQUEST['accomodation']==1 ? get_text('Yes') : get_text('No')) . '</accomodation>' . "\n";
	print '<meal>' . ($_REQUEST['meal']==1 ? get_text('Yes') : get_text('No')) . '</meal>' . "\n";
	print '</response>' . "\n";
?>
