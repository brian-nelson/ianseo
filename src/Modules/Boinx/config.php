<?php
require_once('../../config.php');
if(empty($_GET['Tour']) or !($TourId = getIdFromCode($_GET['Tour'], true))) {
	exit('No Tournament Selected. Usage (TourCode is the Tournament Code, not ID!): IanseoAwards.php?Tour=TourCode');
}
$TourCode=$_GET['Tour'];

$TourCodeSafe=preg_replace('/[^a-z0-9_.-]/sim', '', $TourCode);
