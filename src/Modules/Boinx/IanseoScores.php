<?php

require_once('./config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');

// check where is the live flag
$SQL= "(Select '0' Team, FinEvent, FinMatchNo from Finals
	where
		FinLive='1'
		and FinTournament=$TourId
	) UNION (
	Select '1' team, TfEvent, TfMatchNo from TeamFinals
	where
		TfLive='1'
		and TfTournament=$TourId)
	order by FinMatchNo";

$q=safe_r_sql($SQL);

include('Common/CheckPictures.php');
CheckPictures($TourCode);

if($r=safe_fetch($q)) {
	$opts=array('tournament' => $TourId, 'liveFlag'=>1);
	$FILTER=($r->Team ? "f.TfLive='1'" : "f.FinLive='1'");
	include("IanseoScores-$r->Team.php");
} else {
	exit('No Live flag selected. Go to Ianseo Final/Team Spotting page and activate a Live Event');
}

?>