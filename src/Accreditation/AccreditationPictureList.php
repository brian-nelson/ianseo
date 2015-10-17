<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (!CheckTourSession()) {
	print get_text('CrackError');
	exit;
}

$srcCountry = !empty($_REQUEST["country"]);
$srcAthlete = !empty($_REQUEST["athlete"]);
$srcNoPhoto = !empty($_REQUEST["nophoto"]);

$srcString = (empty($_REQUEST["search"]) ? '' : $_REQUEST["search"]);

$Errore=0;
$Answer='';


$Sql = "SELECT EnId, CONCAT(EnDivision, '-',EnClass) as Category, CONCAT(CoName, ' (' ,CoCode,')') as Country, CONCAT(UPPER(EnFirstName),' ' ,EnName) as Athlete, (PhEnId IS NOT NULL) as hasPicture "
	. "FROM Entries "
	. "LEFT JOIN Countries ON EnCountry=CoId "
	. "LEFT JOIN Photos ON EnId=PhEnId "
	. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
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
	$Sql .= "AND " . assembleWhereCondition($fields,explode(" ",$srcString)) . " ";
}
if($srcNoPhoto) {
	$Sql .= "AND PhEnId IS NULL ";
}
$Sql .= "ORDER BY EnFirstName, EnName";
$Rs=safe_r_sql($Sql);
if(safe_num_rows($Rs)) {
	while ($row = safe_fetch($Rs)) {
		$Answer .= '<athlete>'
			. '<id>' . $row->EnId . '</id>'
			. '<ath><![CDATA[' . $row->Athlete . ']]></ath>'
			. '<team><![CDATA[' . $row->Country . ']]></team>'
			. '<cat><![CDATA[' . $row->Category . ']]></cat>'
			. '<pic><![CDATA[' . $row->hasPicture . ']]></pic>'
			. '</athlete>';
	}
}

header('Content-Type: text/xml');
echo '<response>';
echo '<error>' . $Errore . '</error>';
echo $Answer;
echo '</response>';


?>