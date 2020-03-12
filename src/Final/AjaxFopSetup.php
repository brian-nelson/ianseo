<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');
CheckTourSession(true);
checkACL(AclCompetition, AclReadWrite, false);

$Value=array('error' => 1);

if(!$FopLocations=Get_Tournament_Option('FopLocations')) {
	$FopLocations=array();
}


if(!empty($_GET['Location']) and $Loc=trim($_GET['Location'])
	and !empty($_GET['Target_1']) and $Tg1=intval($_GET['Target_1'])
	and !empty($_GET['Target_2']) and $Tg2=intval($_GET['Target_2'])) {

	$tmp=new stdClass();
	$tmp->Loc=$Loc;
	$tmp->Tg1=$Tg1;
	$tmp->Tg2=$Tg2;
	$FopLocations[]=$tmp;
	Set_Tournament_Option('FopLocations', $FopLocations);
	$Value['error']=0;
	$Value['loc']=$Loc;
	$Value['tg1']=$Tg1;
	$Value['tg2']=$Tg2;
	$Value['num']=count($FopLocations)-1;
}

header('Content-Type: text/xml');

echo '<response>';
foreach($Value as $fld => $data) {
	echo "<$fld><![CDATA[$data]]></$fld>";
}
echo '</response>';
