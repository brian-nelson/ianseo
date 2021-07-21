<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	//$competitions = array("S2006045");
    $competitions = array($_SESSION["TourCode"]);

	$BonusDecode = array("T");

	$BonusComplete = array("%");

	$Bonus = array(
		"T" => array(0, 250, 200, 185, 170, 155, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	);

//	$allowedTeam = array('01000', '04000', '08000', '09000');

	$allowedTeam = array(	'01000', '02000', '03000', '04000', '05000', '06000', '07000',
							'08000', '09000', '10000', '11000', '12000', '13000', '14000',
							'15000', '16000', '17000', '18000', '19000', '20000', '21000',
					);

	$LastPhasePossibility = array(0=>2, 1=>4, 2=>4, 4=>8, 8=>16, 16=>32);

	$headerCompetition = "S2006045";

	define('DoniEvent', 'REG');

	define('DoniField', 'ToWhere'); // ToWhere for Doni - ToName for CdR

	define('DoniLimit', '0'); // set to 0 for no limit

	define('DoniSperateRank', true); // adds the rank of the teams for each tournament

?>