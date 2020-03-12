<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
include('Common/Fun_Export.php');
ini_set('max_execution_time','240');
checkACL(AclCompetition, AclReadOnly);

EraseTourSession();

$Sql = "SELECT ToId, ToCode from Tournament";
$q=safe_r_SQL($Sql);
while($r=safe_fetch($q)) {
    $Gara = export_tournament($r->ToId, true);
    $ToSave = gzcompress(serialize($Gara),9);
    file_put_contents($CFG->DOCUMENT_PATH."Tournament/TmpDownload/".$r->ToCode.".ianseo", $ToSave);
}

header('location: '.$CFG->ROOT_DIR );
