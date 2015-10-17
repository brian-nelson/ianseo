<?php

function module_exists($name)
{
	global $CFG;
	return file_exists($CFG->DOCUMENT_PATH.'/Modules/'.$name);
}

function getModuleParameter($module, $param, $defaultValue='') {
	static $Parameters=array();
	if(empty($Parameters[$module])) {
		$TmpSql = "SELECT MpValue, MpParameter
			FROM ModulesParameters
			WHERE MpModule=" . StrSafe_DB($module) . "
			AND MpTournament=" .  StrSafe_DB($_SESSION['TourId']);
		$Rs=safe_r_sql($TmpSql);
		while($r=safe_fetch($Rs)) {
			$Parameters[$module][$r->MpParameter]=$r->MpValue;
		}
	}
	if(isset($Parameters[$module][$param])) {
		return $Parameters[$module][$param];
	} else {
		return $defaultValue;
	}
}

function setModuleParameter($module, $param, $value)
{
	$Query = "INSERT into ModulesParameters
		SET MpValue=" . StrSafe_DB($value) . ",
		MpModule=" . StrSafe_DB($module) . ",
		MpParameter=" . StrSafe_DB($param) . ",
		MpTournament=" .  StrSafe_DB($_SESSION['TourId']) . "
		ON DUPLICATE KEY UPDATE MpValue=" . StrSafe_DB($value) ;
	safe_w_SQL($Query);
}

function delModuleParameter($module, $param) {
	$Query = "delete from ModulesParameters
		where
		MpModule=" . StrSafe_DB($module) . "
		AND MpParameter=" . StrSafe_DB($param) . "
		AND MpTournament=" .  StrSafe_DB($_SESSION['TourId']);
	safe_w_SQL($Query);

}
