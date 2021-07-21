<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/CommonLib.php');
if (!CheckTourSession()) {
    exit;
}
checkACL(AclISKServer, AclReadOnly,false);

$xml='';
$error=0;

$IskSequence=getModuleParameter('ISK', 'Sequence', array('type' => '', 'session'=>'', 'distance'=>0, 'maxdist'=>0, 'end'=>0));

foreach(getScheduledSessions('API', $_SESSION['TourId'], !empty($_REQUEST["onlyToday"])) as $myRow) {
    // skip elimination for now
    //if($myRow->keyValue[0]=='E') continue;
    $MaxEnds=$myRow->MaxEnds;
    $desc='';
    $selected=0;
    if(array_key_exists('type',$IskSequence) and array_key_exists('maxdist',$IskSequence) and array_key_exists('session',$IskSequence)) {
        $selected=($myRow->keyValue==$IskSequence['type'].$IskSequence['maxdist'].$IskSequence['session'] ? '1' : '0');
    }
    $active='0';
    $xml.='<schedule>
            <val selected="'.$selected.'" active="'.$active.'" maxends="'.($MaxEnds).'">' . $myRow->keyValue . '</val>
            <display><![CDATA[' . $myRow->Description . ']]></display>
        </schedule>';
}

header('Content-Type: text/xml');
print '<response error="' . $error . '" distance="'.(array_key_exists('distance',$IskSequence) ? $IskSequence['distance']:'0').'" end="'.(array_key_exists('end',$IskSequence) ? $IskSequence['end']:'0').'">';
print $xml;
print '</response>';
