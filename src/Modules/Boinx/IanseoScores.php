<?php

require_once('./config.php');

$opts=array();

if(isset($_REQUEST['matchno']) and isset($_REQUEST['team']) and isset($_REQUEST['event'])) {
	// direct request goes with calculated
	$Team=($_REQUEST['team'] ? 1 : 0);
	require_once('Common/Lib/Obj_RankFactory.php');
	require_once('Common/Fun_FormatText.inc.php');
	$opts=array('tournament' => $TourId, 'matchno'=>intval($_REQUEST['matchno']), 'events'=>$_REQUEST['event']);
	include("IanseoScores-$Team.php");

	// outputs the file to the browser
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);
	header('Content-Length: '.mb_strlen($XML, PageEncode));
	header('Connection: Close');
	echo $XmlDoc->SaveXML();

} else {
	// check where is the live flag
	$SQL= "(Select '0' Team, FinEvent, FinMatchNo, MAX(FinDateTime) DateTime
		from Finals use index (FinLive)
		where FinLive='1' and FinTournament=$TourId
		) UNION (
		Select '1' team, TfEvent, TfMatchNo, MAX(TfDateTime) from TeamFinals
		where TfLive='1' and TfTournament=$TourId
		) ORDER BY DateTime DESC";

	$q=safe_r_sql($SQL);

	if($r=safe_fetch($q)) {
		if(file_exists($file=$CFG->DOCUMENT_PATH.'Modules/Boinx/XML/'.$TourCode.'-Scores.xml') && !is_null($r->DateTime) && date('Y-m-d H:i:s', filemtime($file))>$r->DateTime) {
			$XML=file_get_contents($file);
		} else {
			require_once('Common/Lib/Obj_RankFactory.php');
			require_once('Common/Fun_FormatText.inc.php');
	// 		require_once('Common/Lib/ArrTargets.inc.php');
	// 		require_once('Common/Fun_Phases.inc.php');
	// 		include('Common/CheckPictures.php');
	// 		CheckPictures($TourCode);

			$opts=array('tournament' => $TourId, 'liveFlag'=>1);
			$FILTER=($r->Team ? "f.TfLive='1'" : "f.FinLive='1'");
			$EXCLUDE_HEADER=true; // stops outputting the file!!!!
			include("IanseoScores-$r->Team.php");

			$XML= $XmlDoc->SaveXML();
			file_put_contents($file, $XML);
			chmod($file, 0666);
		}

		// outputs the file to the browser
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Content-type: text/xml; charset=' . PageEncode);
		header('Content-Length: '.mb_strlen($XML, PageEncode));
		header('Connection: Close');
		echo $XML;
	} else {
		exit('No Live flag selected. Go to Ianseo Final/Team Spotting page and activate a Live Event');
	}
}

