<?php
$version='2013-03-24 14:13:00';

if($on AND $acl[AclQualification] == AclReadWrite) {
	$ret['QUAL']['SCOR'][] = MENU_DIVIDER;
	$ret['QUAL']['SCOR'][] = get_text('MenuLM_GetScoreUSCAA') .'|'.GetWebDirectory(__FILE__).'/index.php';
    $ret['QUAL']['SCOR'][] = get_text('MenuLM_ExportScoreUSCAA') . '|' . GetWebDirectory(__FILE__) . '/export.php';
}
