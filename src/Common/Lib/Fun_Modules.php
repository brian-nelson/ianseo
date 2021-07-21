<?php

function module_exists($name)
{
	global $CFG;
	return file_exists($CFG->DOCUMENT_PATH.'/Modules/'.$name);
}

function getModuleParameter($module, $param, $defaultValue='', $TourId=0, $clear=false) {
	static $Parameters=array();
	if($clear and isset($Parameters[$module][$param])) unset($Parameters[$module][$param]);
	if(empty($TourId) and !empty($_SESSION['TourId'])) {
		$TourId=$_SESSION['TourId'];
	} else {
		unset($Parameters[$module][$param]);
	}
	if(empty($Parameters[$module][$param])) {
		$TmpSql = "SELECT MpValue, MpParameter
			FROM ModulesParameters
			WHERE MpModule=" . StrSafe_DB($module) . "
			AND MpTournament=" .  StrSafe_DB($TourId);
		$Rs=safe_r_sql($TmpSql);
		while($r=safe_fetch($Rs)) {
			if($r->MpValue) {
				if(($tmp=@unserialize($r->MpValue))===false) {
					$tmp=$r->MpValue;
				}
			} else {
				$tmp=$r->MpValue;
			}
			$Parameters[$module][$r->MpParameter]=$tmp;
		}
	}
	if(isset($Parameters[$module][$param])) {
		return $Parameters[$module][$param];
	} else {
		return $defaultValue;
	}
}

function getModuleParameterLike($module, $param, $TourId=0) {
	$Ret=array();
	if(empty($TourId) and !empty($_SESSION['TourId'])) {
		$TourId=$_SESSION['TourId'];
	}
	$TmpSql = "SELECT MpValue, MpParameter
		FROM ModulesParameters
		WHERE MpModule=" . StrSafe_DB($module) . "
		and MpParameter like ".StrSafe_DB($param)."
		AND MpTournament=" .  StrSafe_DB($TourId);
	$Rs=safe_r_sql($TmpSql);
	while($r=safe_fetch($Rs)) {
		if($r->MpValue) {
			if(($tmp=@unserialize($r->MpValue))===false) {
				$tmp=$r->MpValue;
			}
		} else {
			$tmp=$r->MpValue;
		}
		$Ret[$r->MpParameter]=$tmp;
	}

	return $Ret;
}

function getModule($module, $like='', $TourId=0) {
	$ret=array();
	if(empty($TourId) and !empty($_SESSION['TourId'])) {
		$TourId=$_SESSION['TourId'];
	}
	$TmpSql = "SELECT MpValue, MpParameter
		FROM ModulesParameters
		WHERE MpModule=" . StrSafe_DB($module) ."
		AND MpTournament=" .  StrSafe_DB($TourId)
		. ($like ? " AND MpParameter like '$like'" : '');

	$Rs=safe_r_sql($TmpSql);
	while($r=safe_fetch($Rs)) {
		if($r->MpValue) {
			if(($tmp=@unserialize($r->MpValue))===false) {
				$tmp=$r->MpValue;
			}
		} else {
			$tmp=$r->MpValue;
		}
		$ret[$r->MpParameter]=$tmp;
	}

	return $ret;
}

function setModuleParameter($module, $param, $value, $TourId=0) {
	if(empty($TourId)) $TourId=$_SESSION['TourId'];
	$Query = "INSERT into ModulesParameters
		SET MpValue=" . StrSafe_DB(serialize($value)) . ",
		MpModule=" . StrSafe_DB($module) . ",
		MpParameter=" . StrSafe_DB($param) . ",
		MpTournament=" .  StrSafe_DB($TourId) . "
		ON DUPLICATE KEY UPDATE MpValue=" . StrSafe_DB(serialize($value)) ;
	safe_w_SQL($Query);
	return getModuleParameter($module, $param, $value, $TourId, true);
}

function delModuleParameter($module, $param, $TourId=0) {
	if(empty($TourId)) $TourId=$_SESSION['TourId'];
	$Query = "delete from ModulesParameters
		where
		MpModule=" . StrSafe_DB($module) . "
		AND MpParameter=" . StrSafe_DB($param) . "
		AND MpTournament=" .  StrSafe_DB($TourId);
	safe_w_SQL($Query);
	return getModuleParameter($module, $param, '', $TourId, true);
}

function registerJack($JackEvent, $notifiedModule, $include, $callback, $TourId=0, $extraParams=array()) {
	$jackArray = getModuleParameter("Jack", $JackEvent, array(), $TourId);
	if(!array_key_exists($notifiedModule, $jackArray)) {
		$jackArray[$notifiedModule] = array('include'=>'','callback'=>'','extraparams'=>'');
	}
	$jackArray[$notifiedModule]['include'] = $include;
	$jackArray[$notifiedModule]['callback'] = $callback;
	$jackArray[$notifiedModule]['extraparams'] = $extraParams;

	return setModuleParameter("Jack", $JackEvent, $jackArray, $TourId);
}

function removeJack($JackEvent, $notifiedModule, $TourId=0) {
	$jackArray = getModuleParameter("Jack", $JackEvent, array(), $TourId);
	if(array_key_exists($notifiedModule, $jackArray)) {
		unset($jackArray[$notifiedModule]);
	}
	if(count($jackArray)!=0) {
		return setModuleParameter("Jack", $JackEvent, $jackArray, $TourId);
	} else {
		return delModuleParameter("Jack", $JackEvent, $TourId);
	}
}

function runJack($JackEvent, $TourId=0, $param=array()) {
	//IMPORTANT: Don't change name of $Targets Variable!!!! Used in API-JSON call
	$Targets = getModuleParameter('Jack', $JackEvent, array(), $TourId, true);
	//if($Targets) {
//		error_log("Event $JackEvent triggered ".json_encode($param));
	//}
	foreach($Targets as $Target => $Command) {
		if(is_file($Command['include'])) {
			require_once($Command['include']);
			foreach($param as $k=>$v) {
				$Command['callback'] = str_replace("@".$k, $v, $Command['callback']);
			}
			try {
				//SISTEMAREDOPO NIMES - PROC RSI chiude su errore
				eval($Command['callback']);
			} catch (Exception $e) {
				debug_svela($e);
			}
		}
	}
}
