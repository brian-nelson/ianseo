<?php

require_once(dirname(dirname(__FILE__)).'/config.php');

CheckTourSession(true);

$Group=(empty($_REQUEST['Group'])? '-' : $_REQUEST['Group']);

list($SesType, $Session, $Groups)=explode('-', $Group, 3);

// debug_svela("delete from TargetGroups
// 	where TgTournament={$_SESSION['TourId']}
// 	and TgSession=$Session
// 	and TgSesType='$SesType'
// 	and TgGroup=".StrSafe_DB($Groups));

safe_w_sql("delete from TargetGroups
	where TgTournament={$_SESSION['TourId']}
	and TgSession=$Session
	and TgSesType='$SesType'
	and TgGroup=".StrSafe_DB($Groups));

header('Content-Type: text/xml');

print '<response>';
print '<error>0</error>';
echo '</response>';