<?php

function getTargets($ByDiv=true, $Prefix='') {
	$ar=array();

	$MySql="select"
		. " DivId"
		. ", ClId"
		. ", TfId "
		. ", TfName "
		. ", TfDefault "
		. "from"
		. " Divisions"
		. " inner join Classes on DivTournament=ClTournament and DivAthlete=ClAthlete"
		. " inner join TargetFaces Tf on DivTournament=TfTournament and if(TfRegExp>'', concat(trim(DivId),trim(ClId)) REGEXP TfRegExp, concat(trim(DivId),trim(ClId)) like TfClasses) "
		. "WHERE"
		. " DivTournament={$_SESSION['TourId']} "
		. " AND DivAthlete='1' "
		. " AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))"
		. "order by"
		. " DivViewOrder"
		. ", ClViewOrder"
		. ", TfDefault desc"
		. ", TfRegExp>'' desc"
		. ", concat(trim(DivId),trim(ClId)) = TfClasses desc"
		. ", left(TfClasses,1)!='_' and left(TfClasses,1)!='%' desc"
		. ", left(TfClasses,1)='_' desc"
		. ", TfClasses desc"
		. ", TfClasses='%' ";

	$q=safe_r_sql($MySql);
	if($ByDiv) {
		while($r=safe_fetch($q)) {
			if(!$r->TfDefault or empty($ar[$r->DivId][$r->ClId])) {
				$ar[$r->DivId][$r->ClId][$Prefix.$r->TfId] = get_text($r->TfName, 'Tournament', '', true);
			}
		}
	} else {
		$divs=array();
		while($r=safe_fetch($q)) {
			if(!$r->TfDefault or empty($divs[$r->DivId][$r->ClId])) {
				$ar[$r->TfId][$r->DivId][$Prefix.$r->ClId] = $r->TfDefault;
				$divs[$r->DivId][$r->ClId]='done';
			}
		}
	}

	return $ar;
}

function getTargetsScript() {
	return "<script>var TargetFaces = ".json_encode(getTargets(true, 'p')).'</script>';
}
?>
