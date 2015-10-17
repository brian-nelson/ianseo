<?php
/*
													- UpdateCtrlCode.php -
	Controlla il codice fiscale dei tizio in Partecipants.php
	Decide anche come gestire le tendine delle classi
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');
	require_once('Fun_Partecipants.local.inc.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['d_e_EnCtrlCode']) ||
		!isset($_REQUEST['d_e_EnSex']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$AgeClass = '';
	$Classes= '';
	$Divisions='';
	$ctrlCode='';

	$Age='';
	$Sex=intval($_REQUEST['d_e_EnSex']);
	$Div=(empty($_REQUEST['d_e_EnDiv']) ? '' : $_REQUEST['d_e_EnDiv']);
	$Clas=(empty($_REQUEST['d_e_EnAgeClass']) ? '' : $_REQUEST['d_e_EnAgeClass']);
	if(!empty($_REQUEST['d_e_EnCtrlCode']) and $ctrlCode=ConvertDateLoc($_REQUEST['d_e_EnCtrlCode'])) $Age=intval(substr($_SESSION['TourRealWhenTo'], 0, 4) - substr($ctrlCode, 0, 4));

	$divs=array();
	$clas=array();
	$vald=array();

	// Get the Divisions allowed based on Age (if any restriction applies)
	// Age check not done if not an athlete
	$Select1 = "select ClId, ClValidClass, DivId from Classes"
		. " inner join Divisions on DivTournament=ClTournament and DivAthlete=ClAthlete"
		. " where ClTournament={$_SESSION['TourId']}"
		. " AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))"
		. " AND ClSex in (-1, {$Sex})"
		. ($Age ? " and (ClAthlete!='1' or (ClAgeFrom<=$Age and ClAgeTo>=$Age))" : '')
		. " order by ClViewOrder, DivViewOrder ";

	$RsCl = safe_r_sql($Select1);
	while($MyRow=safe_fetch($RsCl)) {
		$divs[]=$MyRow->DivId;
	}
	$Divisions=implode(',', array_unique($divs));


	// get the classes based on the division selected
	$Select2 = "select ClId, ClValidClass, DivId from Classes"
		. " inner join Divisions on DivTournament=ClTournament and DivAthlete=ClAthlete"
		. ($Div ? " AND DivId='$Div'" : '')
		. " where ClTournament={$_SESSION['TourId']}"
		. " AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))"
		. " AND ClSex in (-1, {$Sex})"
		. ($Age ? " and (ClAthlete!='1' or (ClAgeFrom<=$Age and ClAgeTo>=$Age))" : '')
		. " order by ClViewOrder, DivViewOrder ";
	$RsCl = safe_r_sql($Select2);
	while($MyRow=safe_fetch($RsCl)) {
		$clas[]=$MyRow->ClId;
	}

	// get the VALID classes based on the division and class selected
	$Select3 = "select ClId, ClValidClass, DivId from Classes"
		. " inner join Divisions on DivTournament=ClTournament and DivAthlete=ClAthlete"
		. ($Div ? " AND DivId='$Div'" : '')
		. " where ClTournament={$_SESSION['TourId']}"
		. " AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))"
		. " AND ClSex in (-1, {$Sex})"
		. ($Clas ? " AND ClId='$Clas'" : '')
		. ($Age ? " and (ClAthlete!='1' or (ClAgeFrom<=$Age and ClAgeTo>=$Age))" : '')
		. " order by ClViewOrder, DivViewOrder ";
	$RsCl = safe_r_sql($Select3);
	while($MyRow=safe_fetch($RsCl)) {
		$vald=array_merge($vald, explode(',', $MyRow->ClValidClass));
	}

	if($Age or $Sex!==false or $Div) {
		$AgeClass=implode(',', array_unique($clas));
		$Classes=implode(',', array_unique($vald));
	}

	header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<dob><![CDATA[' . RevertDate($ctrlCode) . ']]></dob>' . "\n";
	print '<divisions><![CDATA[' . $Divisions . ']]></divisions>' . "\n";
	print '<ageclass><![CDATA[' . $AgeClass . ']]></ageclass>' . "\n";
	print '<classes><![CDATA[' . $Classes . ']]></classes>' . "\n";
	print '<sql><![CDATA[' . "$Select1\n$Select2\n$Select3" . ']]></sql>' . "\n";
	print '</response>' . "\n";
?>

