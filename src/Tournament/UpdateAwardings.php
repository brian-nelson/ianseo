<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');

if (!CheckTourSession()) {
	print get_text('CrackError');
	exit;
}

$Errore=0;
$Answer='';

if(preg_match('/^[A-Z0-9]{1,4}\|[0-1]{1}\|[0-1]{1}$/',$_REQUEST["id"]) && in_array($_REQUEST["field"],array('AwOrder','AwPositions','AwDescription','AwAwarders'))) {
	if (!IsBlocked(BIT_BLOCK_PARTICIPANT)){
		list($Event,$isFinal,$isTeam) = explode('|',$_REQUEST["id"]);
		$Sql = "UPDATE Awards SET " . $_REQUEST["field"] . "=" . StrSafe_DB($_REQUEST["value"]) .
			"WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AwEvent=" . StrSafe_DB($Event) . " AND AwFinEvent=" . StrSafe_DB($isFinal). " AND AwTeam=" . StrSafe_DB($isTeam); 
	
		$RsUp=safe_w_sql($Sql);
		if (!$RsUp)
			$Errore=1;
		
		$Answer = '<row>' . $_REQUEST["id"] . '</row>'
			. '<field><![CDATA[' . $_REQUEST["field"] . ']]></field>'
			. '<value><![CDATA[' . ManageHTML($_REQUEST["value"]) . ']]></value>';
	} else {
		$Errore = 1;
	}
} else {
	$Errore = 1;
}

header('Content-Type: text/xml');
echo '<response>';
echo '<error>' . $Errore . '</error>';
echo $Answer;
echo '</response>';
?>