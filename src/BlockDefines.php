<?php
/*
 * BIT_BLOCK_TOURDATA
 * - elimina torneo
 * - modifica dati gara
 * - modifica impostazioni finali (tranne running)
 * - idem per squadre
 *
 * BIT_BLOCK_PARTICIPANT
 * - elenchi partecipanti
 *
 * BIT_BLOCK_ACCREDITATION
 * - procedure di accreditamento
 *
 *
 * La chiave rappresenta il bit di cui si è chiesto il set.
 * Il vecchio valore nel db viene posto in OR con il valore corrispondente alla chiave
 */
//define ("BIT_BLOCK_TOUR",   0x1);	// Blocco info gara
define ("BIT_BLOCK_PARTICIPANT",   0x1);	// Blocco Elenco Partecipanti e modifiche alle persone
define ("BIT_BLOCK_QUAL",   0x2); // Blocco qualificazioni
define ("BIT_BLOCK_ELIM",   0x4); // Blocco eliminatorie (ind)
define ("BIT_BLOCK_IND",    0x8); // Blocco finali ind
define ("BIT_BLOCK_TEAM",  0x10); // Blocco finali team
define ("BIT_BLOCK_REPORT",0x20); // Blocco verbale arbitri
define ("BIT_BLOCK_TOURDATA",0x40); // Blocco modifiche Torneo
define ("BIT_BLOCK_MEDIA",0x80); // Blocco delle modalità Media (rot, etc)
define ("BIT_BLOCK_ACCREDITATION",0x100); // Blocco accreditamento
define ("BIT_BLOCK_PUBBLICATION",0x200); // Blocco pubblicazioni online
define ("BIT_BLOCK_FLIGHTS",0x400); // Blocco gestione Flights
define ('BIT_BLOCK_ALL', 0xFFFF);

/*
 * La chiave rappresenta il bit di cui si è chiesto l'unset
 * Il vecchio valore nel db viene posto in AND con il valore corrispondente alla chiave
 */
function getBlocksToUnset() {
	$ToUnset = array();
	$ToUnset['6'] = (BIT_BLOCK_PARTICIPANT | BIT_BLOCK_ACCREDITATION);
	$ToUnset['0'] = (BIT_BLOCK_TOURDATA | BIT_BLOCK_ACCREDITATION);
	$ToUnset['8'] = (BIT_BLOCK_PARTICIPANT | BIT_BLOCK_TOURDATA);
	$ToUnset['1'] = (BIT_BLOCK_PARTICIPANT | BIT_BLOCK_TOURDATA | BIT_BLOCK_ACCREDITATION);
	$ToUnset['10'] = (BIT_BLOCK_ALL & ~ BIT_BLOCK_FLIGHTS);
	$ToUnset['2'] = ($ToUnset['1'] | BIT_BLOCK_QUAL | BIT_BLOCK_TEAM);
	$ToUnset['3'] = ($ToUnset['2'] | BIT_BLOCK_ELIM);
	$ToUnset['4'] = ($ToUnset['1'] | BIT_BLOCK_QUAL | BIT_BLOCK_ELIM | BIT_BLOCK_IND);
	$ToUnset['7'] = (BIT_BLOCK_ALL & ~ (BIT_BLOCK_MEDIA | BIT_BLOCK_REPORT) );
	$ToUnset['9'] = (BIT_BLOCK_ALL & ~ BIT_BLOCK_PUBBLICATION );
	$ToUnset['5'] = (BIT_BLOCK_ALL & ~ BIT_BLOCK_REPORT);

	return $ToUnset;

}

function getBlocksToSet() {
	$ToSet = array ();
	$ToSet['6'] = BIT_BLOCK_TOURDATA;
	$ToSet['0'] = ($ToSet['6'] | BIT_BLOCK_PARTICIPANT);
	$ToSet['8'] = ($ToSet['0'] | BIT_BLOCK_ACCREDITATION);
	$ToSet['1'] = ($ToSet['8'] | BIT_BLOCK_QUAL);
	$ToSet['10'] = BIT_BLOCK_FLIGHTS;
	$ToSet['2'] = ($ToSet['1'] | BIT_BLOCK_ELIM);
	$ToSet['3'] = ($ToSet['2'] | BIT_BLOCK_IND);
	$ToSet['4'] = ($ToSet['2'] | BIT_BLOCK_TEAM);
	$ToSet['7'] = ($ToSet['2'] | BIT_BLOCK_IND | BIT_BLOCK_TEAM | BIT_BLOCK_MEDIA);
	$ToSet['9'] = ($ToSet['7'] | BIT_BLOCK_PUBBLICATION);
	$ToSet['5'] = (BIT_BLOCK_ALL & ~ BIT_BLOCK_PUBBLICATION);
	return $ToSet;
}

function getACLFeatureList() {
	$tmpList=array();
	$Sql = "SELECT AclFeId as Id, AclFeName as Name FROM AclFeatures ORDER BY AclFeId";
	$Rs = safe_r_sql($Sql);
	while($r=safe_fetch($Rs))
		$tmpList[$r->Id]=$r->Name;
	return $tmpList;
}

function getBlockList() {
	$tmpList=array();
	$Sql = "SELECT AclDtIP as Ip, AclDtFeature as Feature, AclDtLevel as Level FROM AclDetails WHERE AclDtTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY AclDtIP,AclDtFeature ";
	$Rs = safe_r_sql($Sql);
	while($r=safe_fetch($Rs)) {
		if(!array_key_exists($r->Ip,$tmpList))
			$tmpList[$r->Ip]=array();
		$tmpList[$r->Ip][$r->Feature] = $r->Level;
	}
	return $tmpList;
}

function nextLevel($IP,$Feature) {
	$level=getLevel($IP,$Feature);
	if(++$level>3)
		$level=0;
	$Sql = "REPLACE INTO AclDetails (AclDtTournament, AclDtIP, AclDtFeature, AclDtLevel)
			VALUES (" . StrSafe_DB($_SESSION['TourId']) . ", " . StrSafe_DB($IP) . "," . StrSafe_DB($Feature) . "," . $level . ")";
	if(!$level)
		$Sql = "DELETE FROM AclDetails WHERE AclDtTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AclDtIP=" . StrSafe_DB($IP) . " AND AclDtFeature=" . StrSafe_DB($Feature);
	$Rs = safe_w_sql($Sql);
	return getLevel($IP,$Feature);
}

function getLevel($IP,$Feature) {
	$level=0;
	$Sql = "SELECT AclDtLevel as Level
			FROM AclDetails
			WHERE AclDtTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AclDtIP=" . StrSafe_DB($IP) . " AND AclDtFeature=" . StrSafe_DB($Feature);

	$Rs = safe_r_sql($Sql);
	if(safe_num_rows($Rs)){
		$r=safe_fetch($Rs);
		$level = $r->Level;
	}
	return $level;
}

function toggleEnabled($IP) {
	$enabled=getEnabled($IP);
	if(++$enabled!=1)
		$enabled=0;
	$Sql = "UPDATE ACL SET AclEnabled={$enabled}
			WHERE AclTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AclIP=" . StrSafe_DB($IP);
	$Rs = safe_w_sql($Sql);
	return getEnabled($IP);
}


function getEnabled($IP) {
	$enabled=0;
	$Sql = "SELECT AclEnabled as Enabled
			FROM ACL
			WHERE AclTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AclIP=" . StrSafe_DB($IP);
	$Rs = safe_r_sql($Sql);
	if(safe_num_rows($Rs)){
		$r=safe_fetch($Rs);
		$enabled = $r->Enabled;
	}
	return $enabled;
}