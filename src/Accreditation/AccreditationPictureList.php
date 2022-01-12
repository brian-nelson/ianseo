<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (!CheckTourSession()) {
	print get_text('CrackError');
	exit;
}
checkACL(AclAccreditation, AclReadWrite, false);

$srcCountry = !empty($_REQUEST["country"]);
$srcAthlete = !empty($_REQUEST["athlete"]);
$srcNoPhoto = !empty($_REQUEST["nophoto"]);
$srcNoPrint = !empty($_REQUEST["noprint"]);
$srcAccPhoto = !empty($_REQUEST["noacc"]);

$srcString = (empty($_REQUEST["search"]) ? '' : $_REQUEST["search"]);

$Errore=0;
$Answer='';

if($_SESSION['AccreditationTourIds']) {
	if(empty($_REQUEST['x_Tour'])) {
		$Where="EnTournament in ({$_SESSION['AccreditationTourIds']}) ";
	} else {
		$tmp=array();
		foreach($_REQUEST['x_Tour'] as $k => $v) {
			$tmp[]=$k;
		}
		$Where="EnTournament in (".implode(',', $tmp).") ";
	}
} else {
	$Where="EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
}

$Sql = "SELECT EnId,
		CONCAT(EnDivision, '-',EnClass) as Category,
		CONCAT(CoName, ' (' ,CoCode,')') as Country,
		CONCAT(UPPER(EnFirstName),' ' ,EnName) as Athlete,
		(PhEnId IS NOT NULL and PhToRetake=0 ) as hasPicture,
		(EnBadgePrinted+0 and PhEnId IS NULL) or PhToRetake=1 as NoPrintout
	FROM Entries
	INNER JOIN Qualifications ON EnId=QuId
	LEFT JOIN Countries ON EnCountry=CoId
	LEFT JOIN Photos ON EnId=PhEnId
	WHERE  ";
if(!empty($srcString) && ($srcAthlete || $srcCountry)){
	$fields = array();
	if($srcAthlete){
		$fields[]='EnName';
		$fields[]='EnFirstName';
	}
	if($srcCountry) {
		$fields[]='CoCode';
		$fields[]='CoName';
	}
	$Where .= " AND " . assembleWhereCondition($fields,explode(" ",$srcString)) . " ";
}
if($srcNoPhoto) {
	$Where .= " AND (PhEnId IS NULL or PhToRetake=1) ";
}
if($srcNoPrint) {
	$Where .= " AND ((EnBadgePrinted+0 and PhEnId IS NULL) or PhToRetake=1) ";
}
if($srcAccPhoto) {
	$Where .= " AND PhEnId IS NULL ";
}

if(!empty($_REQUEST['x_Sessions'])) {
	$tmp=array();
	foreach($_REQUEST['x_Sessions'] as $k => $v) {
		$tmp[]=$k;
	}
	$Where .= " AND QuSession in (".implode(',', $tmp).") ";
}

$Sql .= $Where . " ORDER BY EnFirstName, EnName";
$Rs=safe_r_sql($Sql);
if(safe_num_rows($Rs)) {
	while ($row = safe_fetch($Rs)) {
		$Answer .= '<athlete id="' . $row->EnId
				. '" ath="' . htmlspecialchars($row->Athlete . ($row->NoPrintout ? ' - ONLY PHOTO' : ''))
				. '" team="' . htmlspecialchars($row->Country)
				. '" cat="' . htmlspecialchars($row->Category)
				. '" pic="' . ($row->hasPicture ? 1 : 0)
				. '" prn="' . ($row->NoPrintout ? 1 : 0) . '">'
			//. '<id>' . $row->EnId . '</id>'
			//. '<ath><![CDATA[' . $row->Athlete . ($row->NoPrintout ? ' - ONLY PHOTO' : '') . ']]></ath>'
			//. '<team><![CDATA[' . $row->Country . ']]></team>'
			//. '<cat><![CDATA[' . $row->Category . ']]></cat>'
			//. '<pic><![CDATA[' . $row->hasPicture . ']]></pic>'
			//. '<prn><![CDATA[' . intval($row->NoPrintout) . ']]></prn>'
			. '</athlete>';
	}
}

$q=safe_r_SQL("select count(*) as Missing
		from Entries
		INNER JOIN Qualifications ON EnId=QuId
		LEFT JOIN Countries ON EnCountry=CoId
		left join ExtraData on EdId=EnId and EdType='A'
		left join Photos on EnId = PhEnId
		where $Where");
$r=safe_fetch($q);

header('Content-Type: text/xml');
echo '<response missing="'.$r->Missing.'">';
echo '<error>' . $Errore . '</error>';
echo $Answer;
echo '</response>';


?>
