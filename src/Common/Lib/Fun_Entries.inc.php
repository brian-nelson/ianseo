<?php
function  calculateAgeClass($dateOfBirth, $gender, $division='', $shoot=false) {
	$allowedClass = array();

	$age = intval(substr($_SESSION['TourRealWhenTo'], 0, 4) - substr($dateOfBirth, 0, 4));

	// get the classes based on the division selected
	$Select = "SELECT DISTINCT ClId, ClValidClass
		FROM Classes 
		INNER JOIN Divisions on DivTournament=ClTournament and DivAthlete=ClAthlete 
		" . ($division ? "AND DivId='$division' " : '') . "
		WHERE ClTournament={$_SESSION['TourId']} 
			AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) 
			AND ClSex in (-1, {$gender}) 
			" . ($age ? "AND (ClAthlete!='1' or (ClAgeFrom<=$age and ClAgeTo>=$age)) " : '') . "
		ORDER BY ClViewOrder, DivViewOrder ";
	$RsCl = safe_r_sql($Select);

	while($MyRow=safe_fetch($RsCl)) {
		$allowedClass[]=$MyRow->ClId;
		if($shoot) {
			foreach(explode(',', $MyRow->ClValidClass) as $c) {
				$c=trim($c);
				if(!in_array($c, $allowedClass)) {
					$allowedClass[]=$c;
				}
			}
		}
	}

	return $allowedClass;
}

function  calculateShootClass($dateOfBirth, $gender, $division='') {
	$allowedClass = array();

	$age = intval(substr($_SESSION['TourRealWhenTo'], 0, 4) - substr($dateOfBirth, 0, 4));

	// get the classes based on the division selected
	$Select = "SELECT DISTINCT ClId, ClValidClass
		FROM Classes 
		INNER JOIN Divisions on DivTournament=ClTournament and DivAthlete=ClAthlete 
		" . ($division ? "AND DivId='$division' " : '') . "
		WHERE ClTournament={$_SESSION['TourId']} 
			AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) 
			AND ClSex in (-1, {$gender}) 
			" . ($age ? "AND (ClAthlete!='1' or (ClAgeFrom<=$age and ClAgeTo>=$age)) " : '') . "
		ORDER BY ClViewOrder, DivViewOrder ";
	$RsCl = safe_r_sql($Select);

	while($MyRow=safe_fetch($RsCl)) {
		$allowedClass[$MyRow->ClId]=$MyRow->ClId;
		foreach(explode(',', $MyRow->ClValidClass) as $c) {
			$c=trim($c);
			if(empty($allowedClass[$c])) {
				$allowedClass[$c]=$MyRow->ClId;
			}
		}
	}

	return $allowedClass;
}