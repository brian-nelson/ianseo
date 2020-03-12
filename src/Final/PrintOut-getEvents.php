<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error' => 1);

if(!isset($_REQUEST['team']) or !isset($_REQUEST['showChildren'])) {
	JsonOut($JSON);
}

$MySql = "SELECT EvCode, EvEventName, EvCodeParent FROM Events 
	WHERE EvTeamEvent='".($_REQUEST['team'] ? 1 : 0)."' 
		AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
		AND EvFinalFirstPhase!=0 
		".($_REQUEST['showChildren'] ? '' : "and EvCodeParent=''")."
		ORDER BY EvProgr";
$Rs = safe_r_sql($MySql);

$JSON['options']=array();
$JSON['options'][]=array('v'=>'.', 't'=>get_text('AllEvents'));
while($MyRow=safe_fetch($Rs)) {
	$JSON['options'][]=array('v'=>$MyRow->EvCode, 't'=>$MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true));
}

$JSON['error']=0;
JsonOut($JSON);
