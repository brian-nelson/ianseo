<?php
/*
													- Fun_DB.inc.php -
	File contenente le funzioni per la gestione del db
*/

/*
	- StrSafe_DB($TheString)
	Quota mysql_real_escape_string($TheString) per prevenire mysql_injection
	come default prende la connessione del server di scrittura

*/

function StrSafe_DB($TheString, $RemoveQuotes=false) {
	global $WRIT_CON, $ERROR_REPORT;
	if(!$WRIT_CON) $WRIT_CON=safe_w_con();
	$ret='';
	if(is_array($TheString)) {
		$ret=array();
		foreach($TheString as $st) {
			$ret[]=StrSafe_DB($st, $RemoveQuotes);
		}
		return $ret;
	} else {
		$TheString=mysqli_real_escape_string($WRIT_CON, $TheString);
		if($RemoveQuotes) {
			return $TheString;
		}
		return "'" . $TheString . "'";
	}
// 	return "'" . str_replace(array("'", '\\'), array("''",'\\\\'), $TheString) . "'";
}

function GetParameter($ParameterName, $InfoSystem=false) {
	$TmpSql = "SELECT ParValue FROM Parameters WHERE ParId=" . StrSafe_DB($ParameterName);
	if($InfoSystem) $TmpSql = "SELECT IsValue as ParValue FROM InfoSystem WHERE IsId=" . StrSafe_DB($ParameterName);
	$Rs=safe_r_sql($TmpSql, false, true);
	if($Rs and $TmpRow = safe_fetch($Rs))return ($InfoSystem ? unserialize($TmpRow->ParValue) : $TmpRow->ParValue);
	return '';
}

function SetParameter($ParId, $ParValue) {
	$Query = "INSERT into Parameters SET ParValue=" . StrSafe_DB($ParValue) . ", ParId="  .StrSafe_DB($ParId) . " ON DUPLICATE KEY UPDATE ParValue=" . StrSafe_DB($ParValue) ;
	safe_w_SQL($Query);
}

function safe_w_con($error=false) {
	global $ERROR_REPORT, $CFG, $Collations;
	if($ERROR_REPORT) $GLOBALS['safe_SQL']['w_connect']++;
	$a=mysqli_connect($CFG->W_HOST, $CFG->W_USER, $CFG->W_PASS);
	if(!$a) {
		if($error) {
			return 'CONNECTION_FAILED';
		} else {
			safe_error("Write Server not reachable");
		}
	}
	$b=mysqli_select_db($a, $CFG->DB_NAME);

	if(!$b and !$error)safe_error(mysqli_error($a));

	mysqli_set_charset($a, "utf8");
// 	mysqli_query($a, "SET NAMES 'utf8' COLLATE '{$_SESSION['COLLATION']}'") or safe_error(mysqli_error($a));
// 	mysqli_query($a, "SET CHARACTER SET 'utf8'") or safe_error(mysqli_error($a));
	mysqli_query($a, "SET time_zone = '".date('P')."'") or safe_error(mysqli_error($a));
	mysqli_query($a, "SET sql_mode = 'NO_UNSIGNED_SUBTRACTION'") or safe_error(mysqli_error($a));

	if($error and !$b) return array($a, 'NO_DATABASE');
	Return $a;
}

function safe_r_con() {
	global $ERROR_REPORT, $CFG;
	if($ERROR_REPORT) $GLOBALS['safe_SQL']['r_connect']++;
	$a=mysqli_connect($CFG->R_HOST, $CFG->R_USER, $CFG->R_PASS) or safe_error("Read Server not reachable");
	mysqli_select_db($a, $CFG->DB_NAME) or safe_error(mysqli_error($a));
	mysqli_set_charset($a, "utf8");
// 	mysqli_query($a, "SET NAMES 'utf8' COLLATE '{$_SESSION['COLLATION']}'") or safe_error(mysqli_error($a));
// 	mysqli_query($a, "SET CHARACTER SET 'utf8'") or safe_error(mysqli_error($a));
	mysqli_query($a, "SET time_zone = '".date('P')."'") or safe_error(mysqli_error($a));
	mysqli_query($a, "SET sql_mode = 'NO_UNSIGNED_SUBTRACTION'") or safe_error(mysqli_error($a));
	Return $a;
}

function safe_w_SQL($SQL, $use=false, $acc_error=array(0)) {
	global $WRIT_CON, $ERROR_REPORT, $CFG;
	if($ERROR_REPORT and $CFG->TRACE_QUERRIES) {
		$a=debug_backtrace();
		$GLOBALS['safe_SQL']['w_querries'][]=$a[0];
	}
	if(!$WRIT_CON) $WRIT_CON=safe_w_con();
	if($use) {
		$a=mysqli_query($WRIT_CON, $SQL, MYSQLI_USE_RESULT ) or in_array(mysqli_errno($WRIT_CON), $acc_error) or safe_error('Error ' . mysqli_errno($WRIT_CON) . ': ' . mysqli_error($WRIT_CON));
	} else {
		$a=mysqli_query($WRIT_CON, $SQL) or in_array(mysqli_errno($WRIT_CON), $acc_error) or safe_error('Error ' . mysqli_errno($WRIT_CON) . ': ' . mysqli_error($WRIT_CON));
	}
	Return $a;
}

function safe_w_MultiSql($SQL, $free=false) {
	global $WRIT_CON, $ERROR_REPORT, $CFG;
	if($ERROR_REPORT and $CFG->TRACE_QUERRIES) {
		$a=debug_backtrace();
		$GLOBALS['safe_SQL']['w_querries'][]=$a[0];
	}
	if(!$WRIT_CON) $WRIT_CON=safe_w_con();
	$a=mysqli_multi_query($WRIT_CON, $SQL);
	if($free) {
		do {
			/* store first result set */
			if ($result = mysqli_store_result($WRIT_CON)) {
				mysqli_free_result($result);
			}
		} while (mysqli_next_result($WRIT_CON));
	}
	Return $a;
}

function safe_w_error() {
	global $WRIT_CON;
	$ret=new StdClass;
	$ret->errno=mysqli_errno($WRIT_CON);
	$ret->error=mysqli_error($WRIT_CON);
	return $ret;
}
function safe_r_SQL($SQL, $use=false, $force=false) {
	global $READ_CON, $ERROR_REPORT, $CFG;
	if($ERROR_REPORT and $CFG->TRACE_QUERRIES) {
		$a=debug_backtrace();
		$GLOBALS['safe_SQL']['r_querries'][]=$a[0];
	}
	if(!$READ_CON) $READ_CON=safe_r_con();
	if($use) {
		$a=mysqli_query($READ_CON, $SQL, MYSQLI_USE_RESULT ) or $force or safe_error('Error ' . mysqli_errno($READ_CON) . ': ' . mysqli_error($READ_CON));
	} else {
		$a=mysqli_query($READ_CON, $SQL) or $force or safe_error('Error ' . mysqli_errno($READ_CON) . ': ' . mysqli_error($READ_CON));
	}
	Return $a;
}
function safe_fetch($r) {
	Return mysqli_fetch_object($r);
}
function safe_fetch_assoc($r) {
	Return mysqli_fetch_assoc($r);
}
function safe_data_seek($r, $num=0) {
	mysqli_data_seek($r, $num);
}
function safe_num_rows($r) {
	Return mysqli_num_rows($r);
}
function safe_w_affected_rows() {
	global $WRIT_CON;
	Return mysqli_affected_rows($WRIT_CON);
}
function safe_r_affected_rows() {
	global $READ_CON;
	Return mysqli_affected_rows($READ_CON);
}
function safe_w_last_id() {
	global $WRIT_CON;
	Return mysqli_insert_id($WRIT_CON);
}
function safe_r_last_id() {
	global $READ_CON;
	Return mysqli_insert_id($READ_CON);
}

function safe_fetch_field($q) {
	return mysqli_fetch_field($q);
}

function safe_free_result($q) {
	return mysqli_free_result($q);
}

function safe_error($ERR='') {
global $ERROR_REPORT;
	header('HTTP/1.0 404 Not Found');
	echo '<meta http-equiv="Content-Type" content="text/html; charset='.PageEncode.'">
	<p><b>'.get_text('TecError')."</b></p>";
	if($ERR) echo "<div>$ERR</div>";
	if($ERROR_REPORT) {
		echo "<pre>";
		print_r(debug_backtrace());
		echo "</pre>";
	}
	exit;
}

function deb_rec($key,$var,$lev=0) {
	$ret='';
	if(is_object($var) or is_array($var)) {
		$ret.=str_repeat('&nbsp;',$lev*2).htmlentities($key)."=[array/object]<br>\n";
		foreach($var as $k=>$v) {
			$ret.=deb_rec($k,$v,$lev+1);
		}
	} else {
		$ret=str_repeat('&nbsp;',$lev*2).htmlentities($key)."=".nl2br(htmlentities($var))."<br>\n";
	}
	return($ret);
}

/**
 * Outputs a debug screen. If the second parameter is false and the global variable $ERROR_REPORT is false then the function silently returns.
 * @param mixed $query is the variable to show (print_r() like)
 * @param boolean $force if true the output is forced regardless the setup of $ERROR_REPORT
 */
function debug_svela($query=array(), $force=false) {
	global $ERROR_REPORT;
	if(!$ERROR_REPORT and $force=='TCPDF') {
		die($query);
	}
	if($force or !empty($_SESSION['OWNER']) or $ERROR_REPORT) {
		echo "<pre>";
		print_r(debug_backtrace());
		echo "\$_POST = ";
		echo htmlentities(print_r($_POST,1));
		if(!is_array($query)) $query=array($query);
		foreach($query as $key=>$val) {
			echo "\n\$$key = ";
			echo htmlentities(print_r($val,1));
		}
		die();
	}
}

function CD_redirect($home='') {
	if(substr(strtolower($home),0,4) != 'http') {
		if(!$home or $home[0]!='/') $home=preg_replace("#[/\\\\]+#","/","/".dirname($_SERVER['PHP_SELF']).'/'.$home);
		$port='';
		if($_SERVER['SERVER_PORT']!=80) $port=':'.$_SERVER['SERVER_PORT'];
		$home = "http://" . $_SERVER['SERVER_NAME'] . $port . $home;
	}
	header("Location: $home");
	exit();
}

function getmicrotime() {
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

function go_get($item='',$value='',$togli='') {
	$get=$_GET;
	if(!is_array($item)) {
		if($item) $item=array($item=>$value);
	} else {
		$togli=$value;
	}

	if($togli) {
		foreach($item as $key=>$val) {
			if(isset($get[$key])) unset($get[$key]);
		}
	} elseif($item) {
		foreach($item as $key=>$val) {
			$get[$key]=$val;
		}
	}

	$tmp=array();
	foreach($get as $key=>$val) {
		if(is_array($val)) {
			foreach($val as $key2=>$val2) {
				$tmp[]="$key".'['.$key2.']'."=$val2";
			}
		} else {
			$tmp[]="$key=$val";
		}
	}
	if($tmp) {
		return("?".implode("&",$tmp));
	} else {
		return("");
	}
}

function assembleWhereCondition($fields, $values) {
	$tmp = array();
	foreach($values as $v) {
		$tmp1 = array();
		foreach($fields as $f) {
			$tmp1[] = $f . " LIKE " . StrSafe_DB('%'.$v.'%');
		}
		$tmp[]='(' . implode(' OR ', $tmp1) . ')';
	}
	return '(' . implode(' AND ', $tmp) . ')';
}

function LogAccBoothQuerry($SQL, $ToCode='', $Type='QUERY') {
	if(empty($_SESSION['AccBooth'])) return;

	if(empty($ToCode)) $ToCode=$_SESSION['TourCode'];
	safe_w_sql("insert into ianseo_Accreditation.QueryLog set QlToCode='{$ToCode}', QlType='$Type', QlQuery=".StrSafe_DB($SQL));
}

function GetAccBoothEnWhere($EnId, $Division=false, $Limit=false) {
	if(empty($_SESSION['AccBooth'])) return '';

	$q=safe_r_sql("select EnCode, EnIocCode, EnDivision from Entries where EnId=$EnId");
	if($r=safe_fetch($q)) {
		$ret="EnCode='$r->EnCode' and EnIocCode='$r->EnIocCode' and EnTournament=§TOCODETOID§";
		if($Division) $ret.=" and EnDivision='$r->EnDivision'";
		if($Limit) $ret.=" limit 1";
		return $ret;
	}
	return '';
}