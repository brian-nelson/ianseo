<?php
	require_once(dirname(__FILE__) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	$TourRow=NULL;
	$json_array=array();

	$Select	= "SELECT ToId,ToType,ToCode,ToName,ToCommitee,ToComDescr,ToWhere, "
		    . "       DATE_FORMAT(ToWhenFrom,'" . get_text('DateFmtDB') . "') AS DtFrom, "
		    . "       DATE_FORMAT(ToWhenTo,'" . get_text('DateFmtDB') . "') AS DtTo, "
		    . "       ToNumSession, ToTypeName, ToNumDist, ToNumEnds, ToCategory "
		    . "  FROM Tournament  "
		    . " WHERE ToId=$CompId";

	// Retrieve the Tournament info
	$Rs=safe_r_sql($Select);

	if($TourRow=safe_fetch($Rs)) {
		// Now load the json array with the info we need

		$json_array["compcode"] = $TourRow->ToCode;
		$json_array["compname"] = $TourRow->ToName;
		$json_array["compdesc1"] = $TourRow->ToCommitee . ' - ' . $TourRow->ToComDescr;
		$json_array["compdesc2"] = $TourRow->ToWhere . ", " . $TourRow->DtFrom . " - " . $TourRow->DtTo;
		$json_array["compdesc3"] = ManageHTML(get_text($TourRow->ToTypeName, 'Tournament')) . ", " . $TourRow->ToNumDist . " " . get_text($TourRow->ToNumDist==1?'Distance':'Distances','Tournament');
		$json_array["compcategory"] = (int)$TourRow->ToCategory;
		$json_array["numdist"] = (int)$TourRow->ToNumDist;
		$json_array["numsession"] = (int)$TourRow->ToNumSession;
		$json_array["distances"] = array();
		//Retrieve all the stages
		$tmp_stage = Array();
		$tmp_stage[] = "Q";
		$Select = "SELECT DISTINCTROW EvTeamEvent, EvElim1, EvElim2 FROM Events WHERE EvTournament=$CompId AND EvFinalFirstPhase!=0 order by EvTeamEvent, EvProgr";
		$Rs=safe_r_sql($Select);
		while($StageRow=safe_fetch($Rs)) {
			if($StageRow->EvElim1 > 0 && !in_array("E1",$tmp_stage))
				$tmp_stage[]="E1";
			if($StageRow->EvElim2 > 0 && !in_array("E2",$tmp_stage))
				$tmp_stage[]="E2";
			if($StageRow->EvTeamEvent==0 && !in_array("MI",$tmp_stage))
				$tmp_stage[]="MI";
			if($StageRow->EvTeamEvent==1 && !in_array("MT",$tmp_stage))
				$tmp_stage[]="MT";
		}
		$json_array["stages"] = $tmp_stage;

		// Retrieve all the sessions that have been defined for the tournament
		// and create a sessions array in the json data
		$sessions=GetSessions('Q',false,null,$TourRow->ToId);
		$session_array = Array();
		foreach ($sessions as $s) {
			$row_array=array();
			$row_array["sessdesc"] = $s->Descr;
			$row_array["sesstype"] = $s->SesType;
			$row_array["sessnumtargets"] = (int)$s->SesTar4Session;
			$row_array["sessfirsttarget"] = (int)$s->SesFirstTarget;
			$json_array["sessions"][]= $row_array;
		}
// 		$json_array["sessions"] = $session_array;

		// Retrieve all the distances for the tournament and create
		// an array to be appended to the json data
		$Select = "SELECT Td1, Td2, Td3, Td4, Td5, Td6, Td7, Td8 "
			    . "  FROM TournamentDistances "
			    . " WHERE TdTournament=" . $TourRow->ToId;

		$Rs=safe_r_sql($Select);

		if (safe_num_rows($Rs) == 1) {
			$distance_array = Array();
			$DistRow=safe_fetch($Rs);
			for($i=1; $i <= $TourRow->ToNumDist; $i++) {
				$dist_array["distnum"] = $i;
				$dist_array["distdesc"] = $DistRow->{'Td'.$i};
				$dist_array["arrowsperend"] = '3';
				$dist_array["numends"] = (int)$TourRow->ToNumEnds;
				array_push($distance_array, $dist_array);
			}
			$json_array["distances"] = $distance_array;
		}
	}
	// Return the json structure with the callback function that is needed by the app
	SendResult($json_array);

