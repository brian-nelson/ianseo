<?php

function getGroupedTargets($TargetNo, $Session=0, $SesType='Q', $SesPhase='', $returnArray=false) {
	global $CompId;
	// get all targets associated/grouped together with the target requested
	if(empty($CompId)) {
		if(empty($_SESSION['TourId'])) return $TargetNo;

		$CompId=$_SESSION['TourId'];
	}

	$TargetToQuery=$TargetNo;
	if($returnArray and $SesType=='Q') {
		$TargetToQuery=$Session.str_pad($TargetNo, 3, '0', STR_PAD_LEFT);
	}

	$SubSelect="select TgGroup, TgSession, TgSesType
		from TargetGroups
		where TgTournament=$CompId
		and TgTargetNo='$TargetToQuery'";
	if($SesType!='Q') {
		$SubSelect.=" and TgSesType='{$SesType}{$SesPhase}'";
	}
	$Tmp=array();
	$q=safe_r_sql("Select TgTargetNo
		from TargetGroups
		where TgTournament=$CompId
		and (TgGroup, TgSession, TgSesType)=($SubSelect) order by TgTargetNo");
	while($r=safe_fetch($q)) {
		if($returnArray and $SesType=='Q') {
			$Tmp[]=intval(substr($r->TgTargetNo,1));
		} else {
			$Tmp[]=$r->TgTargetNo;
		}
	}
	if($Tmp and !$returnArray) {
		$TargetNo=implode("','", $Tmp);
	}

	if($returnArray) {
		if($Tmp) {
			return $Tmp;
		} else {
			return array($TargetNo);
		}
	}
	return $TargetNo;
}
