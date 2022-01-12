<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
include_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Scheduler.php');

checkACL(AclCompetition, AclReadOnly);

if(!$FopLocations=Get_Tournament_Option('FopLocations')) {
	$FopLocations=array();
	Set_Tournament_Option('FopLocations', $FopLocations);
}

// defines the days
$DaysToPrint=array();
if(!empty($_REQUEST['Day'])) {
	foreach($_REQUEST['Day'] as $k => $v) {
		$DaysToPrint[]=date('Y-m-d', $_SESSION['ToWhenFromUTS'] + $k*86400);
	}
} else {
	//foreach(range(0,  intval(($_SESSION['ToWhenToUTS']-$_SESSION['ToWhenFromUTS'])/86400)) as $n) {
	//	$DaysToPrint[]=date('Y-m-d', $_SESSION['ToWhenFromUTS'] + $n*86400);
	//}
}


$pdf=NULL;

// defines the Locations (these will be printed on a single page)
$LocationsToPrint=array();
if(empty($_REQUEST['Print'])) {
	if(!$FopLocations) {
		// prints everything in a single location
		$tmp=new stdClass();
		$tmp->Loc='';
		$tmp->Tg1=1;
		$tmp->Tg2=99999;
		$LocationsToPrint[]=$tmp;
	} else {
		$LocationsToPrint=$FopLocations;
	}
} else {
	foreach($_REQUEST['Print'] as $k=>$v) {
		$LocationsToPrint[]=$FopLocations[$k];
	}
}

$Scheduler=new Scheduler();
$Scheduler->SplitLocations=true;
$Scheduler->DaysToPrint=$DaysToPrint;
$Scheduler->LocationsToPrint=$LocationsToPrint;

$Scheduler->FOP();

