<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');

$CardType=(empty($_REQUEST['CardType']) ? 'A' : $_REQUEST['CardType']);
$CardNumber=(empty($_REQUEST['CardNumber']) ? 0 : intval($_REQUEST['CardNumber']));

if(isset($JSON)) {
	if(!CheckTourSession()) {
		JsonOut($JSON);
	}

	$lvl=0;
	if($CardType=='A') {
		$lvl = checkACL(AclAccreditation, AclReadOnly);
	} else if($CardType=='Q') {
		$lvl = checkACL(AclQualification, AclReadOnly);
	} else if($CardType=='E') {
		$lvl = checkACL(AclEliminations, AclReadOnly);
	} else if($CardType=='I') {
		$lvl = checkACL(AclIndividuals, AclReadOnly);
	} else if($CardType=='T') {
		$lvl = checkACL(AclTeams, AclReadOnly);
	} else if($CardType=='Y') {
		$lvl = checkACL(AclCompetition, AclReadOnly);
	} else if($CardType=='Z') {
		$lvl = checkACL(AclCompetition, AclReadOnly);
	}

	if($lvl!=AclReadWrite) {
		JsonOut($JSON);
	}
} else {
	CheckTourSession(true);
	if($CardType=='A') {
		checkACL(AclAccreditation, AclReadWrite);
	} else if($CardType=='Q') {
		checkACL(AclQualification, AclReadWrite);
	} else if($CardType=='E') {
		checkACL(AclEliminations, AclReadWrite);
	} else if($CardType=='I') {
		checkACL(AclIndividuals, AclReadWrite);
	} else if($CardType=='T') {
		checkACL(AclTeams, AclReadWrite);
	} else if($CardType=='Y') {
		checkACL(AclCompetition, AclReadWrite);
	} else if($CardType=='Z') {
		checkACL(AclCompetition, AclReadWrite);
	}
}



function switchOrder($Old, $New, $CardType, $CardNumber) {
	global $CFG;
	if($New==$Old or !$New) return;
	$min=min($New, $Old);
	$max=max($New, $Old);
	safe_w_sql("update IdCardElements set IceNewOrder=IceOrder where IceCardType='$CardType' and IceCardNumber='$CardNumber' and IceTournament={$_SESSION['TourId']}");
	if($New<$Old) {
		safe_w_sql("update IdCardElements set IceNewOrder=IceOrder+1 where IceCardType='$CardType' and IceCardNumber='$CardNumber' and IceTournament={$_SESSION['TourId']} and IceOrder between $min and $max");
	} else {
		safe_w_sql("update IdCardElements set IceNewOrder=IceOrder-1 where IceCardType='$CardType' and IceCardNumber='$CardNumber' and IceTournament={$_SESSION['TourId']} and IceOrder between $min and $max");
	}
	safe_w_sql("update IdCardElements set IceNewOrder=$New where IceCardType='$CardType' and IceCardNumber='$CardNumber' and IceTournament={$_SESSION['TourId']} and IceOrder=$Old");
	safe_w_sql("update IdCardElements set IceOrder=IceNewOrder where IceCardType='$CardType' and IceCardNumber='$CardNumber' and IceTournament={$_SESSION['TourId']}");

	// removes all pictures
	$Images=array('Image','ImageSvg','RandomImage');
	foreach($Images as $type) {

		foreach(glob($CFG->DOCUMENT_PATH . 'TV/Photos/' . $_SESSION['TourCodeSafe'] . '-' . $type . '-' . $CardType . '-'. $CardNumber . '-*') as $file) {
			unlink($file);
		}
	}

	// redraws all pictures
	$SQL="select * from IdCardElements where IceContent>'' and IceType in (".implode(',', StrSafe_DB($Images)).") and IceCardType='$CardType' and IceCardNumber='$CardNumber' and IceTournament={$_SESSION['TourId']}";
	$q=safe_r_sql($SQL);
	while($r=safe_fetch($q)) {
		if($r->IceType=='ImageSvg') {
			$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-'.$r->IceType.'-'.$r->IceCardType.'-'.$r->IceCardNumber.'-'.$r->IceOrder.'.svg';
			if($im=@gzinflate($r->IceContent)) {
				file_put_contents($ImName, $im);
			}
		} else {
			$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-'.$r->IceType.'-'.$r->IceCardType.'-'.$r->IceCardNumber.'-'.$r->IceOrder.'.jpg';
			if($im=@imagecreatefromstring($r->IceContent)) {
				imagejpeg($im, $ImName, 90);
			}
		}
	}
}
