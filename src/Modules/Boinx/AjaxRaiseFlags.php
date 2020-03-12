<?php
require_once('../../config.php');
checkACL(AclOutput,AclReadWrite, false);

$State=0;
safe_w_sql("update BoinxSchedule set BsExtra=3-BsExtra where BsTournament='{$_SESSION['TourId']}' and BsType like 'Awa_%'");

$q=safe_r_sql("select BsExtra from BoinxSchedule where BsTournament='{$_SESSION['TourId']}' and BsType like 'Awa_%'");
if($r=$q->fetch_object()) {
	$State=$r->BsExtra;
}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

echo '<response>';
echo '<error>0</error>';
echo '<status>'.$State.'</status>';
echo '</response>';
