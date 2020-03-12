<?php
require_once(dirname(__FILE__) . '/config.php');

$TourId = 0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
}

$json_array=array();

$Sql = "(SELECT '0' as Team, FinDateTime DateTime, FinEvent AS Event, FinMatchNo AS MatchNo
	FROM Finals
	WHERE FinTournament=$TourId AND FinLive='1')
	UNION
	(SELECT '1' Team, TfDateTime DateTime, TfEvent AS Event, TfMatchNo AS MatchNo
	FROM TeamFinals
	WHERE TfTournament=$TourId AND TfLive='1')
	ORDER BY Team, DateTime DESC, Event ASC, MatchNo ASC ";

$Rs=safe_r_sql($Sql);
if (safe_num_rows($Rs)!=2) {
	$json_array["LiveMatch"] = false;
} else {
	$r=safe_fetch($Rs);
	$json_array["LiveMatch"] = true;
	$json_array["Event"] = $r->Event;
	$json_array["Type"] = $r->Team;
	$json_array["MatchId"] = (($r->MatchNo % 2) == 0 ? $r->MatchNo : ($r->MatchNo-1));
}

// Return the json structure with the callback function that is needed by the app
SendResult($json_array);