<?php

if(empty($FIELDS)) $FIELDS='EnId, EnCode as Bib, EnName AS Name, upper(EnFirstName) AS FirstName, EnName as GivCamel, upper(EnName) as GivCaps, EnFirstName as FamCamel, upper(EnFirstName) AS FamCaps, QuSession AS Session, (EnBadgePrinted is not null and EnBadgePrinted!=\'0000-00-00 00:00:00\') as Printed, '
	. 'CoCode AS NationCode, CoName AS Nation, upper(CoName) as NationCaps, SesName, '
	. 'EnClass AS ClassCode, EnDivision AS DivCode, EnAgeClass as AgeClass, DivDescription,ClDescription,EnSubClass as SubClass, EnStatus as Status, EnIndClEvent AS `IC`, EnTeamClEvent AS `TC`, EnIndFEvent AS `IF`, EnTeamFEvent as `TF`, '
	. 'AcColor, AcTitleReverse,(ClAthlete*DivAthlete) AS  AcIsAthlete, PhPhoto, '
	. 'AcArea0, AcArea1, AcArea2, AcArea3, AcArea4, AcArea5, AcArea6, AcArea7, AcAreaStar,'
	. 'AcTransport, AcAccomodation, AcMeal ';

if(empty($SORT)) $SORT='Printed, FirstName, Name';
$Entries=array();
$Countries=array();
$Divisions=array();
$Classes=array();
$Sessions=array();

if(!empty($_REQUEST['Entries'])) {
	foreach($_REQUEST['Entries'] as $item) {
		if(intval($item)) $Entries[]=intval($item);
	}
} else {
	if(!empty($_REQUEST['Country'])) {
		foreach($_REQUEST['Country'] as $item) {
			if(intval($item)) $Countries[]=intval($item);
		}
	}

	if(!empty($_REQUEST['Division'])) {
		foreach($_REQUEST['Division'] as $item) {
			if($item) $Divisions[]=strsafe_DB($item);
		}
	}

	if(!empty($_REQUEST['Class'])) {
		foreach($_REQUEST['Class'] as $item) {
			if($item) $Classes[]=strsafe_DB($item);
		}
	}

	if(!empty($_REQUEST['Session'])) {
		foreach($_REQUEST['Session'] as $item) {
			if(intval($item)) $Sessions[]=intval($item);
		}
	}
}

sort($Entries);
sort($Countries);
sort($Divisions);
sort($Classes);
sort($Sessions);

$IncludePhoto    = (!empty($_REQUEST['IncludePhoto']));
$PrintPhoto      = '';
$PrintAccredited = '';
$PrintNotPrinted = '';

if(!empty($_REQUEST['PrintPhoto'])) {
	// recupera solo i badge che hanno una foto dentro
	$PrintPhoto = ' AND PhPhoto is not NULL AND PhPhoto>\'\' ';
}

if(!empty($_REQUEST['PrintAccredited'])) {
	// recupera solo quelli già accreditati
	$PrintAccredited = ' AND AEId is not NULL ';
}

if(!empty($_REQUEST['PrintNotPrinted'])) {
	// solo quelli non ancora stampati...
	// EnBadgePrinted contiene data e ora della stampa del badge...
	// quindi sono da recuperare quelli che hanno la data nulla oppure
	// oppure quelli la cui data di stampa è anteriore alla foto inserita
	$PrintNotPrinted = ' AND (EnBadgePrinted is NULL or EnBadgePrinted=0 or (EnBadgePrinted < PhPhotoEntered)) ';
}

$MyQuery = "SELECT $FIELDS ";
$MyQuery.= "FROM Entries AS e ";
$MyQuery.= "INNER JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$MyQuery.= "INNER JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
$MyQuery.= "INNER JOIN Qualifications AS q ON e.EnId=q.QuId ";
$MyQuery.= "LEFT JOIN AccEntries ON AEId=EnId and AETournament=EnTournament and AeOperation=1 ";
$MyQuery.= "LEFT JOIN Session ON SesTournament=EnTournament and SesOrder=QuSession ";
$MyQuery.= "LEFT JOIN AccColors AS ac ON ac.AcTournament=e.EnTournament AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE ac.AcDivClass ";
$MyQuery.= "LEFT JOIN Photos AS ph ON e.EnId=ph.PhEnId ";
$MyQuery.= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " "; // 2010-03-16 tolto EnAthlete=1 AND
if($Sessions)        $MyQuery .= "AND QuSession in (" . implode(',', $Sessions) . ") ";
if($Entries)         $MyQuery.= " and EnId in (".implode(',', $Entries).") ";
if($Countries)       $MyQuery.= " and EnCountry in (".implode(',', $Countries).") ";
if($Divisions)       $MyQuery.= " and EnDivision in (".implode(',', $Divisions).") ";
if($Classes)         $MyQuery.= " and EnClass in (".implode(',', $Classes).") ";
if($PrintPhoto)      $MyQuery.= $PrintPhoto;
if($PrintAccredited) $MyQuery.= $PrintAccredited;
if($PrintNotPrinted) $MyQuery.= $PrintNotPrinted;

$MyQuery.= " ORDER BY $SORT ";

?>