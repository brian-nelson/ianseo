<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ScorePDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Lib/ScorecardsLib.php');
checkACL(AclQualification, AclReadOnly);

// switch to decide which scorecard type to print
$_REQUEST['TourField3D']=$_SESSION['TourField3D'];

$Session=intval($_REQUEST['x_Session']);

if(!empty($_REQUEST['SessionType']) and $_REQUEST['SessionType']=='E') {
	$Session=$_REQUEST['x_ElimSession'];
	$_REQUEST['x_Phase']=intval($_REQUEST['x_Session']);
	$_REQUEST['ScoreDist']=array(1);
}


$pdf=CreateSessionScorecard($Session, $_REQUEST['x_From'], $_REQUEST['x_To'], $_REQUEST);
$pdf->output();
