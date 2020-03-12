<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');
CheckTourSession(true);
checkACL(AclCompetition, AclReadWrite, false);


$Value=array('error' => 1);

if($FopLocations=Get_Tournament_Option('FopLocations')) {
	if(!empty($_GET['Location']) and is_array($_GET['Location'])) {
		$LocK=key($_GET['Location']);
		$LocV=current($_GET['Location']);
		$FopLocations[$LocK]->Loc=$LocV;
		Set_Tournament_Option('FopLocations', $FopLocations);
		$Value['error']=0;
	} elseif(!empty($_GET['Start']) and is_array($_GET['Start'])) {
		$LocK=key($_GET['Start']);
		$LocV=current($_GET['Start']);
		$FopLocations[$LocK]->Tg1=$LocV;
		Set_Tournament_Option('FopLocations', $FopLocations);
		$Value['error']=0;
	} elseif(!empty($_GET['End']) and is_array($_GET['End'])) {
		$LocK=key($_GET['End']);
		$LocV=current($_GET['End']);
		$FopLocations[$LocK]->Tg2=$LocV;
		Set_Tournament_Option('FopLocations', $FopLocations);
		$Value['error']=0;
	}
}



header('Content-Type: text/xml');

echo '<response>';
foreach($Value as $fld => $data) {
	echo "<$fld><![CDATA[$data]]></$fld>";
}
echo '</response>';
