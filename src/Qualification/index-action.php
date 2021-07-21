<?php

$JSON=array('error' => 1);

require_once(dirname(dirname(__FILE__)) . '/config.php');
//require_once('Common/Lib/CommonLib.php');
//require_once('Common/Fun_FormatText.inc.php');
//require_once('Common/Fun_Various.inc.php');
//require_once('Common/Fun_Sessions.inc.php');

if(!CheckTourSession() or checkACL(AclQualification, AclReadWrite, false)!=AclReadWrite or empty($_REQUEST['act']) or empty($_REQUEST['id'])) {
    JsonOut($JSON);
}

$EnId=intval($_REQUEST['id']);
$q=safe_r_sql("select * from Entries where EnId=$EnId and EnTournament={$_SESSION['TourId']}");
if(!safe_num_rows($q)) {
    JsonOut($JSON);
}

switch($_REQUEST['act']) {
    case 'dnf':
        $JSON['class']='Irm-5';
        $JSON['btn']=get_text('CmdUnset', 'Tournament', 'DNF');
        safe_w_sql("update Qualifications set QuIrmType=5 where QuId=$EnId");
        safe_w_sql("update Individuals set IndIrmType=5 where IndId=$EnId");
        break;
    case 'dns':
        $JSON['class']='Irm-10';
        $JSON['btn']=get_text('CmdUnset', 'Tournament', 'DNS');
        safe_w_sql("update Qualifications set QuIrmType=10 where QuId=$EnId");
        safe_w_sql("update Individuals set IndIrmType=10 where IndId=$EnId");
        break;
    case 'unset':
        $JSON['class']='Irm-0';
        $JSON['btn']=get_text('CmdSet', 'Tournament');
        safe_w_sql("update Qualifications set QuIrmType=0 where QuId=$EnId");
        safe_w_sql("update Individuals set IndIrmType=0 where IndId=$EnId");
        break;
    default:
        JsonOut($JSON);
}

$JSON['error']=0;

JsonOut($JSON);
