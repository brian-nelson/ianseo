<?php

require_once('./config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');

// check the medal matches

include('Common/CheckPictures.php');
CheckPictures($TourCode);

$EXCLUDE_HEADER=true;
$FILTER="GrPhase in (0,1)";
$opts=array('tournament' => $TourId, 'events'=>array('@0', '@1'));
include("IanseoScores-0.php");
include("IanseoScores-1.php");

exit();

