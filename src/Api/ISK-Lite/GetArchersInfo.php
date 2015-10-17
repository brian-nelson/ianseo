<?php
	require_once(dirname(__FILE__) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Lib/Fun_Phases.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');

	$json_array = array();
	$json_array['distances']=array();
	$json_array['targettypes']=array();
	$json_array['archers']=array();

	if(!empty($_GET['matchid'])) {
		list($Event,$EventType,$MatchNo) = explode("|",$_GET['matchid']);
		$EventType=($EventType=='T' ? 1 : 0);
		$Phase=0;

		//Get the phase relatedto the matchno
		$SQL="select GrPhase from Grids where GrMatchNo=$MatchNo";
		$Rs=safe_r_sql($SQL);
		if($r=safe_fetch($Rs))
			$Phase = $r->GrPhase;

		// get the distances
		$objParam=getEventArrowsParams($Event,$Phase,$EventType,$CompId);
		$json_array['distances'][]=array('num' => 1, 'desc' => '', 'arrows' => (int)$objParam->arrows, 'ends' => (int)$objParam->ends, 'shootoff' => (int)$objParam->so);

		//get the archers
		$options['tournament']=$CompId;
		$options['events']=array();
		$options['events'][] =  $Event . '@' . $Phase;
		$rank=null;
		if($EventType)
			$rank=Obj_RankFactory::create('GridTeam',$options);
		else
			$rank=Obj_RankFactory::create('GridInd',$options);

		$rank->read();
		$Data=$rank->getData();
		//debug_svela($Data);
		foreach($Data['sections'] as $kSec=>$vSec) {
			$tmp=array('id' => $vSec['meta']['targetTypeId'], 'name' => get_text($vSec['meta']['targetType']));
			if(!in_array($tmp, $json_array['targettypes'])) $json_array['targettypes'][]=$tmp;
			foreach($vSec['phases'] as $kPh=>$vPh) {
				foreach($vPh['items'] as $kItem=>$vItem) {
					if($vItem['matchNo']==$MatchNo) {
						$row_array=array();
						$row_array["matchid"] = $kSec . "|" . ($EventType ? "T" : "I") . "|" . $vItem['matchNo'];
						$row_array["name"] = ($EventType ? $vItem['countryName'] : $vItem['athlete']);
						$row_array["placement"] = ltrim($vItem['target'],'0');
						$row_array["info1"] = $vItem['countryCode']. ', ' . $vItem['countryName'];
						$row_array["info2"] =$vSec['meta']['eventName'];
						$row_array["distances"] = array($vSec['meta']['targetTypeId']);
						$json_array['archers'][]=$row_array;

						$row_array=array();
						$row_array["matchid"] = $kSec . "|" . ($EventType ? "T" : "I") . "|" . $vItem['oppMatchNo'];
						$row_array["name"] = ($EventType ? $vItem['oppCountryName'] : $vItem['oppAthlete']);
						$row_array["placement"] = ltrim($vItem['oppTarget'],'0');
						$row_array["info1"] = $vItem['oppCountryCode']. ', ' . $vItem['oppCountryName'];
						$row_array["info2"] =$vSec['meta']['eventName'];
						$row_array["distances"] = array($vSec['meta']['targetTypeId']);
						$json_array['archers'][]=$row_array;
					}
				}
			}
		}

	} else {
		$json_array['targettypes']=array();
		$json_array['distances']=array();
		$json_array['archers'] = Array();

		// Check to see if we are in an Elimination stage (Field+3D)
		$tmp=explode('|', $_GET['sesstarget']);
		if(count($tmp)==3) {
			// THIS IS AN ELIMINATION...
			// need to get all archers
			$TargetNo=str_pad($tmp[2], 3, '0', STR_PAD_LEFT);

			// Prepare the select used to retrieve competitor information
			$Select = "SELECT EnId,EnCode,EnName,EnFirstName,EnSex,EnDivision,DivDescription,EnClass,ClDescription,
				CoCode,CoName, concat('{$tmp[0]}|{$tmp[1]}|',ElTargetNo) as QuTargetNo, ElTargetNo AS TargetNo,
				TfT1, TfT2, TfT3, TfT4, TfT5, TfT6, TfT7, TfT8, EvElimEnds, EvElimArrows, EvElimSO, TarId, TarDescr
				FROM Entries
				INNER JOIN Eliminations ON EnId=ElId and ElElimPhase=".($tmp[0]=='E1' ? 0 : 1)." and ElEventCode='".$tmp[1]."'
				inner join Events on EvCode=ElEventCode and EvTeamEvent=0 and EvTournament=ElTournament
				INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
				INNER JOIN Classes ON EnClass=ClId AND EnTournament=ClTournament
				INNER JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament
				INNER JOIN TargetFaces ON EnTargetFace=TfId AND EnTournament=TfTournament
				INNER JOIN Targets ON TfT1=TarId
				WHERE EnTournament=$CompId
				AND EnAthlete=1
				AND EnStatus <= 1
				AND left(ElTargetNo,3) in ('".$TargetNo."')
				ORDER BY ElTargetNo ";
			$q=safe_r_sql($Select);
			while($r=safe_fetch($q)) {
				$tmp=array('id' => $r->TarId, 'name' => get_text($r->TarDescr));
				if(!in_array($tmp, $json_array['targettypes'])) $json_array['targettypes'][]=$tmp;

				$tmp=array(
					'num' => 1,
					'desc' => '1',
					'arrows' => (int)$r->EvElimArrows,
					'ends' => (int)$r->EvElimEnds);
				if(!in_array($tmp, $json_array['distances'])) $json_array['distances'][]=$tmp;
			}

		} else {
			// THIS IS THE QUALIFICATION PART
			// Retrieve the parameters sent in the call
			$Session = (empty($_GET['session']) ? '' : $_GET['session']);
			$Target = sprintf("%03s", $_GET['sesstarget']);  // Add leading zeroes because the app doesn't send them
			$TargetNo=getGroupedTargets($Session.$Target, $Session);

			// get the targettypes
			$sql=array();
			for($n=1; $n<=8; $n++) {
				$sql[]="select TarId, TarDescr from Targets
					inner join TargetFaces on TfT{$n}=TarId and TfTournament=$CompId
					inner join Entries on EnTournament=$CompId and EnTargetFace=TfId
					inner join Qualifications on EnId=QuId and left(QuTargetNo,4) in ('".$TargetNo."') AND EnStatus <= 1 AND EnAthlete=1";
			}
			$SQL="(".implode(') UNION (', $sql).")";
			$q=safe_r_sql($SQL);
			while($r=safe_fetch($q)) {
				$tmp=array('id' => $r->TarId, 'name' => get_text($r->TarDescr));
				if(!in_array($tmp, $json_array['targettypes'])) $json_array['targettypes'][]=$tmp;
			}

			// get the distances
			$sql=array();
			for($n=1; $n<=8; $n++) {
				$sql[]="select group_concat(DISTINCT Td{$n} ORDER BY Td{$n} ASC SEPARATOR ',') DiName, DiEnds, DiArrows, DiDistance
					from Entries
					INNER JOIN Tournament ON ToId=$CompId
					inner join Qualifications on EnId=QuId and left(QuTargetNo,4) in ('".$TargetNo."')
					INNER JOIN DistanceInformation ON EnTournament=DiTournament and DiSession=QuSession and DiDistance=$n and DiType='Q'
					INNER JOIN TournamentDistances ON ToType=TdType and TdTournament=ToId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses
					where EnTournament=$CompId
						AND EnStatus <= 1
						AND EnAthlete=1
					group by DiEnds, DiArrows, DiDistance
						";
			}
			$SQL="(".implode(') UNION (', $sql).") order by DiDistance";

			$q=safe_r_sql($SQL);
			//debug_svela($SQL);
			while($r=safe_fetch($q)) {
				if(!$r->DiDistance)
					continue;
				$tmp=array(
					'num' => (int)$r->DiDistance,
					'desc' => $r->DiName,
					'arrows' => (int)$r->DiArrows,
					'ends' => (int)$r->DiEnds);
				if(!in_array($tmp, $json_array['distances'])) $json_array['distances'][]=$tmp;
			}

			// Prepare the select used to retrieve competitor information
			$Select = "SELECT EnId,EnCode,EnName,EnFirstName,EnSex,EnDivision,DivDescription,EnClass,ClDescription,
					CoCode,CoName, QuTargetNo, SUBSTRING(QuTargetNo,2) AS TargetNo,
		        	TfT1, TfT2, TfT3, TfT4, TfT5, TfT6, TfT7, TfT8
				FROM Entries
				INNER JOIN Qualifications ON EnId=QuId
				INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
				INNER JOIN Classes ON EnClass=ClId AND EnTournament=ClTournament
				INNER JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament
				INNER JOIN TargetFaces ON EnTargetFace=TfId AND EnTournament=TfTournament
				WHERE EnTournament=$CompId
					AND EnAthlete=1
					AND EnStatus <= 1
					AND left(QuTargetNo,4) in ('".$TargetNo."')
				ORDER BY QuTargetNo ";
		}

		// Retrieve the competitor info
		//debug_svela($Select);
		$Rs=safe_r_sql($Select);

		while ($MyRow=safe_fetch($Rs)) {
			// Now load the json array with the info we need
			$row_array=array();
			$row_array["id"] = $MyRow->EnId;
			$row_array["name"] = $MyRow->EnName . ' ' . $MyRow->EnFirstName;
			$row_array["placement"] = ltrim($MyRow->TargetNo, '0');
			$row_array["info1"] = $MyRow->CoCode . ', ' . $MyRow->CoName;
			$row_array["info2"] = $MyRow->EnDivision . $MyRow->EnClass . "-" . $MyRow->DivDescription . "," . $MyRow->ClDescription;
			$row_array["qutarget"] = $MyRow->QuTargetNo;

			if($json_array['distances']) {
				$distance_array = Array();

				foreach(range(1, count($json_array['distances'])) as $i) {
					$row_array["distances"][]=($json_array['distances'][$i-1]['desc']=='-' ? 0 : (int)$MyRow->{'TfT'.$i});
				}
			}
			$json_array['archers'][]=$row_array;
		}
	}
	// Return the json structure with the callback function that is needed by the app
	SendResult($json_array);

