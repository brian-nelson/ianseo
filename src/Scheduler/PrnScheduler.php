<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Scheduler.php');
require_once('Common/pdf/IanseoPdf.php');

if (defined('hideSchedulerAndAdvancedSession'))
{
	exit;
}
CheckTourSession(true);

$Schedule = new Scheduler();

if(isset($_REQUEST['Daily'])) $Schedule->DayByDay=true;
if(isset($_REQUEST['Finalists'])) $Schedule->Finalists=true;

if(isset($_REQUEST['Today'])) {
	$Schedule->SingleDay=date('Y-m-d');
	if(!empty($_REQUEST['FromDayDay'])) {
		if(strtolower(substr($_REQUEST['FromDayDay'], 0, 1))=='d') {
			$Date=date('Y-m-d', strtotime(sprintf('%+d days', substr($_REQUEST['FromDayDay'], 1) -1), $_SESSION['ToWhenFromUTS']));
		} else {
			$Date=CleanDate($_REQUEST['FromDayDay']);
		}
		if($Date) $Schedule->SingleDay=$Date;
	}
}

if(isset($_REQUEST['FromDay'])) {
	$Schedule->FromDay=date('Y-m-d');
	if(!empty($_REQUEST['FromDayDay'])) {
		if(strtolower(substr($_REQUEST['FromDayDay'], 0, 1))=='d') {
			$Date=date('Y-m-d', strtotime(sprintf('%+d days', substr($_REQUEST['FromDayDay'], 1) -1), $_SESSION['ToWhenFromUTS']));
		} else {
			$Date=CleanDate($_REQUEST['FromDayDay']);
		}
		if($Date) $Schedule->FromDay=$Date;
	}
}

$pdf = $Schedule->getSchedulePDF();
$pdf->Output();

