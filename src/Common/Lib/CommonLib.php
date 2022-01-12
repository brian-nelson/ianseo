<?php

/**
 * @return array of the pool phases as [phase] => [descriptionShort]
 */
function getPoolMatchesPhases() {
	$ret=array();
	$ret['64']='Show Match';
	$ret['32']='Match A1/B1';
	$ret['16']='Match A2/B2';
	$ret['8']='Match A3/B3';
	$ret['4']='Match A4/B4';
	return $ret;
}

/**
 * @return array of the pool phases as [phase] => [descriptionShort]
 */
function getPoolMatchesPhasesWA() {
	$ret=array();
	$ret['64']='Match 1 A/B/C/D';
	$ret['32']='Match 2 A/B/C/D';
	$ret['16']='Match 3 A/B/C/D';
	$ret['8']='Match 4 A/B/C/D';
	$ret['4']='Match AB/CD';
	return $ret;
}

/**
 * @return array of the pool phases as [phase] => [descriptionShort]
 */
function getPoolMatchesHeaders() {
	$ret=array();
	$ret['64']='Show Match';
	$ret['32']='Match 1';
	$ret['16']='Match 2';
	$ret['8']='Match 3';
	$ret['4']='Match 4';
	return $ret;
}

/**
 * @return array of the pool phases as [phase] => [descriptionShort]
 */
function getPoolMatchesHeadersWA() {
	$ret=array();
	$ret['64']='Match 1';
	$ret['32']='Match 2';
	$ret['16']='Match 3';
	$ret['8']='Match 4';
	$ret['4']='AB/CD';
	return $ret;
}

/**
 * @param int $Pool can be empty string [default], A or B to filter the selected pool
 * @return array of the pool matches as [lower matchno] => [descriptionShort]
 */
function getPoolMatchesShort($Pool='') {
	$ret=array();
	if(!$Pool) {
		$ret['128']='Show Match';
	}
	if(!$Pool or $Pool=='A') {
		$ret['94']='Match A1';
		$ret['46']='Match A2';
		$ret['22']='Match A3';
		$ret['10']='Match A4';
	}
	if(!$Pool or $Pool=='B') {
		$ret['96']='Match B1';
		$ret['48']='Match B2';
		$ret['24']='Match B3';
		$ret['12']='Match B4';
	}
	return $ret;
}

/**
 * @param int $Pool can be empty string [default], A or B to filter the selected pool
 * @return array of the pool matches as [lower matchno] => [descriptionShort]
 */
function getPoolMatchesShortWA($Pool='') {
	$ret=array();
	if(!$Pool or $Pool=='A') {
		$ret['206']='Match A1';
		$ret['102']='Match A2';
		$ret['50'] ='Match A3';
		$ret['24'] ='Match A4';
		//$ret['12'] ='Match AB';
	}
	if(!$Pool or $Pool=='B') {
		$ret['222']='Match B1';
		$ret['110']='Match B2';
		$ret['54'] ='Match B3';
		$ret['26'] ='Match B4';
		$ret['12'] ='Match AB';
	}
	if(!$Pool or $Pool=='C') {
		$ret['174']='Match C1';
		$ret['86'] ='Match C2';
		$ret['42'] ='Match C3';
		$ret['20'] ='Match C4';
		//$ret['10'] ='Match CD';
	}
	if(!$Pool or $Pool=='D') {
		$ret['190']='Match D1';
		$ret['94'] ='Match D2';
		$ret['46'] ='Match D3';
		$ret['22'] ='Match D4';
		$ret['10'] ='Match CD';
	}

	return $ret;
}

/**
 * @param int $Pool can be empty string [default], A or B to filter the selected pool
 * @return array of the pool matches as [Matchno] => [Winner Description]
 */
function getPoolMatchesWinners($Pool='') {
	$ret=array();
	if(!$Pool) {
		$ret['128']='1st Ranked';
		$ret['129']='2nd Ranked';
		$ret['4']='1st or 2nd Ranked';
		$ret['5']='Winner of Match A4';
		$ret['6']='Winner of Match B4';
		$ret['7']='1st or 2nd Ranked';
	}
	if(!$Pool or $Pool=='A') {
		$ret['94']='10th ranked';
		$ret['95']='11th ranked';
		$ret['46']='7th ranked';
		$ret['47']='Winner of Match A1';
		$ret['22']='6th ranked';
		$ret['23']='Winner of Match A2';
		$ret['10']='3rd ranked';
		$ret['11']='Winner of Match A3';
	}
	if(!$Pool or $Pool=='B') {
		$ret['96']='12th ranked';
		$ret['97']='9th ranked';
		$ret['48']='Winner of Match B1';
		$ret['49']='8th ranked';
		$ret['24']='Winner of Match B2';
		$ret['25']='5th ranked';
		$ret['12']='Winner of Match B3';
		$ret['13']='4th ranked';
	}
	return $ret;
}

/**
 * @param int $Pool can be empty string [default], A or B to filter the selected pool
 * @return array of the pool matches as [Matchno] => [Winner Description]
 */
function getPoolMatchesWinnersWA($Pool='') {
	$ret=array();
	if(!$Pool) {
		$ret['4']='1st Ranked';
		$ret['5']='Winner of Match CD';
		$ret['6']='Winner of Match AB';
		$ret['7']='2nd Ranked';
	}

	if(!$Pool or $Pool=='A') {
		$ret['206']='18th ranked';
		$ret['207']='19th ranked';
		$ret['102']='11th ranked';
		$ret['103']='Winner of Match A1';
		$ret['50'] ='10th ranked';
		$ret['51'] ='Winner of Match A2';
		$ret['24'] ='3rd ranked';
		$ret['25'] ='Winner of Match A3';
		//$ret['12'] ='Winner of Pool A';
		//$ret['13'] ='Winner of Pool B';
	}
	if(!$Pool or $Pool=='B') {
		$ret['222']='16th ranked';
		$ret['223']='21st ranked';
		$ret['110']='13th ranked)';
		$ret['111']='Winner of Match B1';
		$ret['54'] ='8th ranked';
		$ret['55'] ='Winner of Match B2';
		$ret['26'] ='5th ranked';
		$ret['27'] ='Winner of Match B3';
		$ret['12'] ='Winner of Pool A';
		$ret['13'] ='Winner of Pool B';
	}
	if(!$Pool or $Pool=='C') {
		$ret['174']='17th ranked';
		$ret['175']='20th ranked';
		$ret['86'] ='12th ranked';
		$ret['87'] ='Winner of Match C1';
		$ret['42'] ='9th ranked';
		$ret['43'] ='Winner of Match C2';
		$ret['20'] ='4th ranked';
		$ret['21'] ='Winner of Match C3';
		//$ret['10'] ='Winner of Pool C';
		//$ret['11'] ='Winner of Pool D';
	}
	if(!$Pool or $Pool=='D') {
		$ret['190']='15th ranked';
		$ret['191']='22nd ranked';
		$ret['94'] ='14th ranked';
		$ret['95'] ='Winner of Match D1';
		$ret['46'] ='7th ranked';
		$ret['47'] ='Winner of Match D2';
		$ret['22'] ='6th ranked';
		$ret['23'] ='Winner of Match D3';
		$ret['10'] ='Winner of Pool C';
		$ret['11'] ='Winner of Pool D';
	}

	return $ret;
}

/**
 * @param string $Pool can be empty string [default], A or B to filter the selected pool
 * @return array of the pool matches as [description] => [lower matchno]
 */
function getPoolMatches($Pool='') {
	$ret=array();
	if(!$Pool or $Pool=='C') {
		$ret['128']='Show Match 1 vs 2';
	}
	if(!$Pool or $Pool=='A') {
		$ret['94']='Pool A - Match 1 (10/11)';
		$ret['46']='Pool A - Match 2 (7/W MA-1)';
		$ret['22']='Pool A - Match 3 (6/W MA-2)';
		$ret['10']='Pool A - Match 4 (3/W MA-3)';
	}
	if(!$Pool or $Pool=='B') {
		$ret['96']='Pool B - Match 1 (9/12)';
		$ret['48']='Pool B - Match 2 (8/W MB-1)';
		$ret['24']='Pool B - Match 3 (5/W MB-2)';
		$ret['12']='Pool B - Match 4 (4/W MB-3)';
	}
	return $ret;
}

/**
 * @param string $Pool can be empty string [default], A or B to filter the selected pool
 * @return array of the pool matches as [description] => [lower matchno]
 */
function getPoolMatchesWA($Pool='', $WithExtra=true) {
	$ret=array();
	if(!$Pool or $Pool=='A') {
		$ret['206']='Pool A - Match 1 (18/19)';
		$ret['102']='Pool A - Match 2 (11/W MA-1)';
		$ret['50'] ='Pool A - Match 3 (10/W MA-2)';
		$ret['24'] ='Pool A - Match 4 (3/W MA-3)';
		if($Pool and $WithExtra) {
			$ret['12'] ='Pool A/B (W MA-4/W MB-4)';
		}
	}
	if(!$Pool or $Pool=='B') {
		$ret['222']='Pool B - Match 1 (16/21)';
		$ret['110']='Pool B - Match 2 (13/W MB-1)';
		$ret['54'] ='Pool B - Match 3 (8/W MB-2)';
		$ret['26'] ='Pool B - Match 4 (5/W MB-3)';
		$ret['12'] ='Pool A/B (W MA-4/W MB-4)';
	}
	if(!$Pool or $Pool=='C') {
		$ret['174']='Pool C - Match 1 (17/20)';
		$ret['86'] ='Pool C - Match 2 (12/W MC-1)';
		$ret['42'] ='Pool C - Match 3 (9/W MC-2)';
		$ret['20'] ='Pool C - Match 4 (4/W MC-3)';
		if($Pool and $WithExtra) {
			$ret['10'] = 'Pool C/D (W MC-4/W MD-4)';
		}
	}
	if(!$Pool or $Pool=='D') {
		$ret['190']='Pool D - Match 1 (15/22)';
		$ret['94'] ='Pool D - Match 2 (14/W MD-1)';
		$ret['46'] ='Pool D - Match 3 (7/W MD-2)';
		$ret['22'] ='Pool D - Match 4 (6/W MD-3)';
		$ret['10'] ='Pool C/D (W MC-4/W MD-4)';
	}
	return $ret;
}

/**
 * @param string $Pool
 * @return array of all the matchnos involved
 */
function getPoolMatchNos($Pool='') {
	$ret=array();
	foreach(getPoolMatches($Pool) as $k => $v) {
		$ret[]=$k;
		$ret[]=$k+1;
	}
	return $ret;
}

/**
 * @param string $Pool
 * @return array of all the matchnos involved
 */
function getPoolMatchNosWA($Pool='', $WithExtra=true) {
	$ret=array();
	foreach(getPoolMatchesWA($Pool, $WithExtra) as $k => $v) {
		$ret[]=$k;
		$ret[]=$k+1;
	}
	return $ret;
}

/**
 * @return array as a static grid of rank => matchno
 * @see getPoolMatches();
 */
function getPoolGrids() {
	$ret=array();
	$ret[1]='128';
	$ret[2]='129';
	$ret[3]='10';
	$ret[4]='13';
	$ret[5]='25';
	$ret[6]='22';
	$ret[7]='46';
	$ret[8]='49';
	$ret[9]='97';
	$ret[10]='94';
	$ret[11]='95';
	$ret[12]='96';

	return $ret;
}

/**
 * @return array as a static grid of rank => matchno
 * @see getPoolMatches();
 */
function getPoolGridsWA() {
	$ret=array();
	$ret[1] ='4';
	$ret[2] ='7';
	$ret[3] ='24';
	$ret[4] ='20';
	$ret[5] ='26';
	$ret[6] ='22';
	$ret[7] ='46';
	$ret[8] ='54';
	$ret[9] ='42';
	$ret[10]='50';
	$ret[11]='102';
	$ret[12]='86';
	$ret[13]='110';
	$ret[14]='94';
	$ret[15]='190';
	$ret[16]='222';
	$ret[17]='174';
	$ret[18]='206';
	$ret[19]='207';
	$ret[20]='175';
	$ret[21]='223';
	$ret[22]='191';

	return $ret;
}

/**
 * @return array as a static grid of rank => matchno
 * @see getPoolMatches();
 */
function getPoolLooserRank($MatchNo) {
	switch($MatchNo) {
		case '96': return 11; break;
		case '94': return 11; break;
		case '46': return  9; break;
		case '48': return  9; break;
		case '24': return  7; break;
		case '22': return  7; break;
		case '10': return  5; break;
		case '12': return  5; break;
	}

	return false;
}

/**
 * @return array as a static grid of rank => matchno
 * @see getPoolMatches();
 */
function getPoolLooserRankWA($MatchNo) {
	switch($MatchNo) {
		case '190':
		case '222':
		case '174':
		case '206': return 19; break;
		case '102':
		case '86':
		case '110':
		case '94':  return 15; break;
		case '46':
		case '54':
		case '42':
		case '50':  return 11; break;
		case '24':
		case '20':
		case '26':
		case '22':  return  7; break;
		case '10':
		case '12':  return  5; break;
	}

	return false;
}

function GetTournamentIocCode($TourId=0) {
    if(!$TourId and !empty($_SESSION['TourId'])) $TourId=$_SESSION['TourId'];
    if(empty($TourId)) return '';
    $q=safe_r_sql("select ToIocCode from Tournament where ToId={$TourId}");

    if($r=safe_fetch($q)) {
        return $r->ToIocCode;
    } else {
        return '';
    }
}

function Get_Tournament_Option($key, $Ret='', $TourId=0) {
	if(!$TourId and !empty($_SESSION['TourId'])) $TourId=$_SESSION['TourId'];
	if(empty($TourId)) return array();


	$q=safe_r_sql("select ToOptions from Tournament where ToId={$TourId}");
	$r=safe_fetch($q);
	if($r->ToOptions) $ToOptions=unserialize($r->ToOptions);
	if(!empty($ToOptions[$key])) {
		if(is_array($ToOptions[$key])) {
			if(empty($Ret)) $Ret=array();
			foreach($ToOptions[$key] as $k => $v) {
				$Ret[$k] = $v;
			}
		} elseif(is_object($ToOptions[$key])) {
			if(empty($Ret)) $Ret=new StdClass();
			foreach($ToOptions[$key] as $k => $v) {
				$Ret->$k = $v;
			}
		} else {
			$Ret=$ToOptions[$key];
		}
	}
	return $Ret;
}

function Get_Image($IocCode=null, $Section=null, $Reference=null, $Type=null, $Tourid=0) {
	if(empty($Tourid)) {
		$Tourid=$_SESSION['TourId'];
	}
	$SQL="select * from Images where ImTournament=$Tourid";
	if(!isnull($IocCode)) $SQL.=" and ImIocCode='$IocCode'";
	if(!isnull($Section)) $SQL.=" and ImSection='$Section'";
	if(!isnull($Reference)) $SQL.=" and ImReference='$Reference'";
	if(!isnull($Type)) $SQL.=" and ImType='$Type'";

	$q=safe_r_sql($SQL);
}

/**
 * Serve a collegare le var stringa definite in php a javascript.
 * Per ogni stringa in $vars viene generata una var javascript con lo stesso nome e lo stesso valore.
 * Se la var in php è un vettore (1-dimensionale) anche quello verrà convertito in js.
 *
 * @param string[] $vars: nomi delle var da generare
 *
 * @return string: script javascript che inizializza le variabili localizzate
 */
function phpVars2js($vars) {
	$out='<script type="text/javascript">';

	foreach ($vars as $k => $v) {
		$out.='var ' . $k . '='.json_encode($v).';';
	}

	$out.='</script>';

	return $out;
}


/**
 * @param mixed $r EnId of athlete or a row containing all the necessary info
 * @param number $h height of the image
 * @param string $side alignment
 * @param string $Extra extra html tags (title, etc)
 * @param string $force if true on empty picture creates an on the fly portrait
 * @param string $direct if true returns image (or empty string if not found on filesystem) without further checking
 * @return string
 */
function get_photo_ianseo($r, $h=0, $side=false, $Extra='', $force=false, $direct='') {
	global $CFG;
	if($direct) {
		if(is_file($f=$CFG->DOCUMENT_PATH.($img='TV/Photos/'.$direct.'-En-'.$r.'.jpg'))) {
			return '<img src="'.$CFG->ROOT_DIR.$img.'" '.($h?' height="'.$h.'"':''). ($side ? ' align="left"' : '') . ' '.$Extra.'/>';
		}
		return '&nbsp;';
	}

	if(is_numeric($r)) {
		$q=safe_r_SQL("Select PhPhoto, unix_timestamp(PhPhotoEntered) PhModded, PhEnId, ToCode from Photos
				inner join Entries on EnId=PhEnId
				inner join Tournament on ToId=EnTournament
				where PhEnId=".intval($r));
		$r=safe_fetch($q);
		if(!$r) {
			if($force) {
				$WorkH=($h ? $h : 400);
				// creates an embedded image on the fly!
				$w=ceil($WorkH*3/4);
				$im=imagecreatetruecolor($w, $WorkH);
				$white=imagecolorallocate($im, 255, 255, 255);
				$red=imagecolorallocate($im, 200, 0, 0);
				$blue=imagecolorallocate($im, 0, 0, 200);
				imagefilledrectangle($im, 0, 0, $w-1, $WorkH-1, $red);
 				imagefilledrectangle($im, 2, 2, $w-3, $WorkH-3, $white);
 				$texts=explode(' ', get_text('NoPictureAvailable', 'Tournament'));
 				$font=$CFG->DOCUMENT_PATH.'Common/tcpdf/fonts/arialbi.ttf';
 				$startY=(($WorkH - (count($texts)*$WorkH/5))/2)+$WorkH/7;
 				foreach($texts as $i=>$t) {
 					$dim=imagettfbbox($WorkH/10, 12, $font, $t);
 					$x=($w+$dim[0]-$dim[2])/2;
 					$y=$startY+$i*$WorkH/5;
	 				imagettftext($im, $WorkH/10, 12, $x, $y, $blue, $font, $t);
 				}
				ob_start();
				imagepng($im);
				$img = ob_get_contents();
				ob_end_clean();

				return '<img src="data:image/png;base64,'.base64_encode($img).'" '.($h?' height="'.$h.'"':''). ($side ? ' align="left"' : '') . ' '.$Extra.'/>';
			} else {
				return '';
			}
		}
	}
	if(!$r->PhPhoto) return '';
	if(!is_file($f=$CFG->DOCUMENT_PATH.($img='TV/Photos/'.$r->ToCode.'-En-'.$r->PhEnId.'.jpg')) or filemtime($f)<$r->PhModded) {
		if($im=@imagecreatefromstring(base64_decode($r->PhPhoto))) Imagejpeg($im, $f, 95);
	}
	return '<img src="'.$CFG->ROOT_DIR.$img.'" '.($h?' height="'.$h.'"':''). ($side ? ' align="left"' : '') . ' '.$Extra.'/>';
}

function get_flag_ianseo($r, $h=0, $align='', $direct='') {
	global $TourCode, $CFG;
	if($direct) {
		if(is_file($f=$CFG->DOCUMENT_PATH.($img='TV/Photos/'.$direct.'-Fl-'.$r.'.jpg'))) {
			return '<img src="'.$CFG->ROOT_DIR.$img.'" '.($h?' height="'.$h.'"':'').' class="Flag" '.($align ? 'align="'.$align.'"' : '').' />';
		}
		return '&nbsp;';
	}
	if(!is_object($r)) {
		if(!$r) return '&nbsp;';
		$ret=array();
		if(!is_array($r)) $r=array($r);
		foreach($r as $flag) {
			$q=safe_r_SQL("select FlJPG, FlCode, unix_timestamp(FlEntered) FlModded from Flags
				where FlCode=".StrSafe_DB($flag)." and FlTournament in (-1, ".getIdFromCode($TourCode).") order by FlTournament desc");
			$ret[]=get_flag_ianseo(safe_fetch($q), $h, $align);
		}
		return implode('<br>', $ret);
	}
	if(!$r->FlJPG) return '&nbsp;';
	if(!is_file($f=$CFG->DOCUMENT_PATH.($img='TV/Photos/'.$TourCode.'-Fl-'.$r->FlCode.'.jpg')) or filemtime($f)<$r->FlModded) {
		if($im=@imagecreatefromstring(base64_decode($r->FlJPG))) Imagejpeg($im, $f, 95);
	}
	return '<img src="'.$CFG->ROOT_DIR.$img.'" '.($h?' height="'.$h.'"':'').' class="Flag" '.($align ? 'align="'.$align.'"' : '').' />';
}

function CssToObject($css) {
	$ret=array();
	foreach(explode(';', $css) as $elem) {
		if(trim($elem)) {
			list($k, $v)=explode(':', $elem);
			$ret[trim($k)]=trim($v);
		}
	}
	return(json_decode(json_encode($ret)));
}

function ObjectToCss($css) {
	$ret=array();
	foreach($css as $k => $v) {
		$ret[]=$k . ':' . $v;
	}
	return implode('; ', $ret);
}

function DefineForcePrintouts($TourId, $Restore=false) {
//	static $OldLang=null;
//	if($OldLang==null) {
//		$OldLang=SelectLanguage();
//	}
    if(!defined('PRINTLANG')) {
        $q = safe_r_SQL("select ToPrintLang from Tournament where ToId=$TourId");
        $r = safe_fetch($q);
        @define('PRINTLANG', $r->ToPrintLang);
    }
}

function getScheduledSessions($return='API', $TourId=0, $OnlyToday=false, $Short=false) {
	require_once('Common/Lib/Fun_Phases.inc.php');
	if(!$TourId) $TourId=$_SESSION['TourId'];

	$ret=array();

	$SQL = "(SELECT DISTINCT CONCAT(SesType,ToNumDist,SesOrder) as keyValue, SesType as Type, 'Q' as txtkey,
				if(SesName='', SesOrder, SesName) as Description, '0' as FirstPhase,
				IFNULL(CONCAT(SchDay, ' ', SchStart), concat('0000-00-00 00:00:', SesOrder)) as dtOrder, group_concat(DiEnds order by DiDistance) MaxEnds, 0 as EvElimType
			FROM Session
			INNER JOIN Tournament ON SesTournament=ToId
			LEFT JOIN DistanceInformation on DiTournament=SesTournament and DiType=SesType and DiSession=SesOrder
			LEFT JOIN Scheduler ON SchTournament=SesTournament AND SchSesType=SesType AND SchSesOrder=SesOrder
			WHERE SesTournament=$TourId AND SesType='Q'
			" . ($OnlyToday ? " AND (SchDay=UTC_DATE() or DiDay=UTC_DATE())" : "") ."
			GROUP BY SesOrder, SesType
		) UNION ALL (
		SELECT DISTINCT CONCAT('E1',ElSession) as keyValue, 'E' as Type, 'E1' as txtkey,
				if(SesName is null or SesName='', ElSession, SesName) as Description, '0' as FirstPhase,
				IFNULL(CONCAT(SchDay, ' ', SchStart), concat('0000-00-00 00:00:', ElSession)) as dtOrder, EvElimEnds as MaxEnds, EvElimType
			FROM Events
			INNER JOIN Eliminations ON ElTournament=EvTournament and EvCode=ElEventCode and EvTeamEvent=0 and EvElim1>0 and ElElimPhase=0 and EvElimType<3
			left JOIN Session on EvTournament=SesTournament and ElSession=SesOrder AND SesType='E'
			LEFT JOIN Scheduler ON SchTournament=EvTournament AND SchSesType='E' AND SchSesOrder=ElSession
			WHERE EvTournament=$TourId
			" . ($OnlyToday ? "AND SchDay=UTC_DATE()" : "") ."
			GROUP BY EvCode
		) UNION ALL (
		SELECT DISTINCT CONCAT('E2',ElSession) as keyValue, 'E' as Type, 'E2' as txtkey,
				if(SesName is null or SesName='', ElSession, SesName) as Description, '0' as FirstPhase, 
				IFNULL(CONCAT(SchDay, ' ', SchStart), concat('0000-00-00 00:00:', ElSession)) as dtOrder, EvElim2 as MaxEnds, EvElimType
			FROM Events
			INNER JOIN Eliminations ON ElTournament=EvTournament and EvCode=ElEventCode and EvTeamEvent=0 and EvElim2>0 and ElElimPhase=1 and EvElimType<3
			left JOIN Session on SesTournament=ElTournament and ElSession=SesOrder AND SesType='E'
			LEFT JOIN Scheduler ON SchTournament=EvTournament AND SchSesType='E' AND SchSesOrder=ElSession
			WHERE EvTournament=$TourId
			" . ($OnlyToday ? "AND SchDay=UTC_DATE()" : "") ."
			GROUP BY EvCode
		) UNION ALL (
		SELECT DISTINCT CONCAT(IF(FSTeamEvent=0,'I','T'), FSScheduledDate, FSScheduledTime) AS keyValue, FSTeamEvent as Type, FSTeamEvent as txtkey,
				CONCAT(date_format(FSScheduledDate, '%e %b '),date_format(FSScheduledTime, '%H:%i'), ' ', group_concat(distinct concat('--', GrPhase, '-- ', FsEvent) separator '+')) AS Description, EvFinalFirstPhase as FirstPhase, 
				CONCAT(FSScheduledDate,' ',FSScheduledTime) as dtOrder, (max(if(GrPhase>4, EvElimEnds, EvFinEnds))+5) MaxEnds, EvElimType
			FROM FinSchedule
			inner join Grids on GrMatchNo=FsMatchNo
			inner join Events on EvCode=FsEvent and EvTournament=FsTournament and EvTeamEvent=FsTeamEvent
			WHERE FSTournament=$TourId and FSScheduledDate>0
			" . ($OnlyToday ? "AND FSScheduledDate=UTC_DATE()" : "") ."
			GROUP BY CONCAT(IF(FSTeamEvent=0,'I','T'), FSScheduledDate, FSScheduledTime)
		) ORDER BY Type='Q' desc, Type='E' desc, dtOrder ASC, Description ";

	$texts=array(
		'type-Q' => $Short ? 'Q' : get_text('QualSession', 'HTT'),
		'type-E1' => $Short ? 'E1' : get_text('EliminationShort', 'Tournament').' 1',
		'type-E2' => $Short ? 'E2' : get_text('EliminationShort', 'Tournament').' 2',
		'type-0' => $Short ? 'I' : get_text('FinInd', 'HTT'),
		'type-1' => $Short ? 'T' : get_text('FinTeam', 'HTT'),
	);

	$PoolMatches=getPoolMatchesPhases();
	$PoolMatchesWA=getPoolMatchesPhasesWA();

	$q=safe_r_SQL($SQL);
	while($r=safe_fetch($q)) {
		unset($m);
		preg_match_all('/--([0-9]+)--/', $r->Description, $m);
		$n=array_unique($m[1]);
		foreach($n as $v) {
			$tmp=get_text(namePhase($r->FirstPhase, $v).'_Phase');
			if($r->EvElimType==3 and isset($PoolMatches[$v])) {
				$tmp=$PoolMatches[$v];
			} elseif($r->EvElimType==4 and isset($PoolMatchesWA[$v])) {
				$tmp=$PoolMatchesWA[$v];
			}
			$r->Description=str_replace("--{$v}--", $tmp, $r->Description);
		}

		$r->Description=$texts['type-'.$r->txtkey].': '.$r->Description;

		$ret[$r->keyValue]=$r;
	}
	return $ret;
}

function getStatusFromEnds($Ends, $Group, &$JSON) {
	$TgtsStatus=array();
	foreach($Ends as $Tgt => $Let) {
		if(!empty($SpecificDevice) and !in_array($Tgt, $SpecificDevice)) {
			// a secific device has been asked for, so if not in those skip the target!
			continue;
		}
		$TgtsStatus[$Tgt]='';
		$isScoring=false;
		$noScores=false;
		$finished=true;
		foreach($Let as $k => $v) {
			$finished=($finished and ($v=='F' or $v=='G'));
			switch($v) {
				case 'G': // empty spot
				case 'R': // arrows in wrong session
				case 'C': // missing arrow in current end
				case 'O': // arrows in different end
					// do nothing
					break;
				case 'B': // arrows imported
				case 'F': // finished scoring
					$isScoring=true;
					if($TgtsStatus[$Tgt]!='Z' and $TgtsStatus[$Tgt]!='Y') {
						$TgtsStatus[$Tgt]='B';
					}
					break;
				case 'Z': // scoring in progress
					$isScoring=true;
					$TgtsStatus[$Tgt]='Z';
					break;
				case 'Y': // ready to import
					$isScoring=true;
					if($TgtsStatus[$Tgt]!='Y') {
						$TgtsStatus[$Tgt]='Y';
					}
					break;
				default:
					$noScores=true;
			}
			$JSON['a'][]=array(
				'id' => $Group.'-'.$Tgt.'-'.$k,
				'v' => $v,
				'a' => (!empty($Anomalies[$Tgt][$k]) ? '1' : '0'),
			);
		}
		if($finished) {
			$TgtsStatus[$Tgt]='F';
		}
		if($isScoring and $noScores) {
			$TgtsStatus[$Tgt]='Z';
		}
	}
	return $TgtsStatus;
}

function getMatchLive($TourId) {
	// gets the live match
	$q=safe_r_SQL("(Select '0' Team, FinEvent Event, FinMatchNo MatchNo, FinDateTime DateTime, EvMaxTeamPerson, EvEventName, EvFinalFirstPhase
		from Finals use index (FinLive)
		inner join Events on FinTournament=EvTournament and FinEvent=EvCode and EvTeamEvent=0
		where FinLive='1' and FinMatchNo%2=0 and FinTournament=$TourId
		) UNION (
		Select '1' Team, TfEvent Event, TfMatchNo MatchNo, TfDateTime DateTime, EvMaxTeamPerson, EvEventName, EvFinalFirstPhase
		from TeamFinals
		inner join Events on TfTournament=EvTournament and TfEvent=EvCode and EvTeamEvent=1
		where TfLive='1' and TfMatchNo%2=0 and TfTournament=$TourId
		)");
	if($r=safe_fetch($q)) {
		return (object) array("MatchNo"=>$r->MatchNo, "Event"=>$r->Event, "Team"=>$r->Team, 'Archers'=>$r->EvMaxTeamPerson, 'Name'=>$r->EvEventName, 'FirstPhase'=>$r->EvFinalFirstPhase);
	} else {
		return false;
	}
}

function decodeTie($TieString, $SoArrows, $Closest) {
	$Decoded=array();
	$idx=0;
	while($TbString=substr($TieString, $idx, $SoArrows)) {
		if($SoArrows==1) {
			$Decoded[]=DecodeFromLetter($TbString);
		} else {
			$Decoded[]=ValutaArrowString($TbString);
		}
		$idx+=$SoArrows;
	}
	return implode(',',$Decoded).($Closest ? '+' : '');
}


/**
 * @param bool $AllHHT selects which sets of sessions to return
 *              <ul><li>Individuals: only individual events</li><li>Teams: only team events</li><li>All: all events</li></ul>
 * @param null $ComboSesArray will contain the session items value
 * @return string
 * This function returns the string of options with all the timed sessions of the competition to use in a select
 */
function ComboSession($AllHHT=false, &$ComboSesArray=null) {
	$ComboArr=array();
	$ComboSes='';
	$numOptions=0;

	$MatchNames=getPoolMatchesPhases();
	$MatchNamesWA=getPoolMatchesPhasesWA();

	if((isset($_REQUEST["x_Hht"]) && $_REQUEST["x_Hht"]!=-1) or $AllHHT) {
		if(!$AllHHT) {
			$Select='SELECT HeEventCode FROM HhtEvents WHERE HeTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND HeHhtId=' . StrSafe_DB($_REQUEST["x_Hht"]);
			$Rs=safe_r_sql($Select);
		}
		if($AllHHT or (numHHT()==1 && safe_num_rows($Rs)==0 )) {

			if(!$AllHHT) {
				$sessions=GetSessions('Q');

				foreach ($sessions as $s) {
					if ($ComboSesArray!==null) {
						$ComboArr[]=$s->SesOrder;
					}
					$ComboSes.= '<option value="' . $s->SesOrder . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$s->SesOrder ? ' selected' : '') . '>' . get_text('QualSession','HTT') . ' ' . $s->Descr . '</option>' . "\n";
					$numOptions++;
				}
			}

			// Individual Finals
			if($AllHHT!='Teams') {
				$Select='SELECT
						@Phase:=ifnull(2*pow(2,truncate(log2(fsmatchno/2),0)),1) Phase,
						@RealPhase:=truncate(@Phase/2, 0) RealPhase,
						CONCAT(FSScheduledDate," ",FSScheduledTime) AS MyDate,
						DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDBshort') . '") AS Dt,
						DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDB') . '") AS Dat,
						FSTeamEvent,
						FSEvent,
						FSScheduledTime,
						EvFinalFirstPhase,
						EvElimType
					FROM FinSchedule fs
					inner join Events on FSEvent=EvCode and FSTeamEvent=EvTeamEvent and FsTournament=EvTournament
					where FsTournament=' . $_SESSION['TourId'] . ' and FsTeamEvent=0
						and fsscheduleddate >0
					group by FsScheduledDate, FsScheduledTime, FsEvent, Phase
					order by FsScheduledDate, FsScheduledTime, FsEvent, Phase';
				$tmp=array();
				$Rs=safe_r_sql($Select);
				while ($MyRow=safe_fetch($Rs)) {
					$val=$MyRow->FSTeamEvent . $MyRow->MyDate;
					$text=get_text('FinInd','HTT') . ': ' . $MyRow->MyDate ;
					if($MyRow->EvElimType==3 and isset($MatchNames[$MyRow->RealPhase])) {
						$idx=$MatchNames[$MyRow->RealPhase];
					} elseif($MyRow->EvElimType==4 and isset($MatchNamesWA[$MyRow->RealPhase])) {
						$idx=$MatchNamesWA[$MyRow->RealPhase];
					} else {
						$idx=get_text(namePhase($MyRow->EvFinalFirstPhase, $MyRow->RealPhase) . '_Phase');
					}
					$tmp[$val]['events'][$idx][]= $MyRow->FSEvent;
					$tmp[$val]['date']= $MyRow->Dt . ' '. substr($MyRow->FSScheduledTime,0,5) . ' ' . get_text('FinInd','HTT') ;
					$tmp[$val]['selected']= isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$val ? ' selected' : '';
					$numOptions++;
				}
				foreach($tmp as $k => $v) {
					$val=array();
					foreach($v['events'] as $ph => $ev) $val[]= $ph . ' ('.implode('+',$ev).')';
					$ComboSes.='<option value="'.$k.'"'.$v['selected'].'>'.$v['date']  . ' '. implode('; ',$val).'</option>';
					if ($ComboSesArray!==null)
					{
						$ComboArr[]=$k;
					}
				}
			}

			// Team Finals
			if($AllHHT!='Individuals') {
				$Select='SELECT  @Phase:=ifnull(2*pow(2,truncate(log2(fsmatchno/2),0)),1) Phase, @RealPhase:=truncate(@Phase/2, 0) RealPhase, 
					CONCAT(FSScheduledDate,\' \',FSScheduledTime) AS MyDate, DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDBshort') . '") AS Dt, DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDB') . '") AS Dat,
					FSTeamEvent, FSEvent, FSScheduledTime, EvFinalFirstPhase 
					FROM `FinSchedule` fs 
                    INNER JOIN Events on FSEvent=EvCode and FSTeamEvent=EvTeamEvent and FsTournament=EvTournament 
					WHERE FsTournament=' . $_SESSION['TourId'] . ' and fsscheduleddate >0 AND FSTeamEvent!=0 
					GROUP BY FsScheduledDate, FsScheduledTime, FsEvent, Phase 
					order BY FsScheduledDate, FsScheduledTime, FsEvent, Phase';
				$tmp=array();
				$Rs=safe_r_sql($Select);
				if (safe_num_rows($Rs)>0) {
					while ($MyRow=safe_fetch($Rs)) {
						$val=$MyRow->FSTeamEvent . $MyRow->MyDate;
						$text=get_text('FinTeam','HTT') . ': ' . $MyRow->MyDate;
						$tmp[$val]['events'][get_text(namePhase($MyRow->EvFinalFirstPhase, $MyRow->RealPhase) . '_Phase')][]= $MyRow->FSEvent;
						$tmp[$val]['date']= get_text('FinTeam','HTT') . ': ' . $MyRow->Dt.' '. substr($MyRow->FSScheduledTime,0,5);
						$tmp[$val]['selected']= isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$val ? ' selected' : '';
						$numOptions++;
					}
					foreach($tmp as $k => $v) {
						$val=array();
						foreach($v['events'] as $ph => $ev) $val[]= $ph . ' ('.implode('+',$ev).')';
						$ComboSes.='<option value="'.$k.'"'.$v['selected'].'>'.$v['date']  . ': '. implode('; ',$val).'</option>';
						if ($ComboSesArray!==null)
						{
							$ComboArr[]=$k;
						}
					}
				}
			}
		} else {
			//Solo le fasi di qualifica associate alla catena HHT
			$Select='SELECT HeSession FROM HhtEvents WHERE HeTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND HeHhtId=' . StrSafe_DB($_REQUEST["x_Hht"]) . " AND HeSession!=0 ORDER BY HeSession";
			$Rs=safe_r_sql($Select);
			while ($MyRow=safe_fetch($Rs)) {
				if ($ComboSesArray!==null) {
					$ComboArr[]=$MyRow->HeSession;
				}
				$ComboSes.= '<option value="' . $MyRow->HeSession . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$MyRow->HeSession ? ' selected' : '') . '>' . get_text('QualSession','HTT') . ' ' . $MyRow->HeSession . '</option>';
				$numOptions++;
			}

			//Solo le finali associate alla catena HHT
			$Select='SELECT
					@Phase:=ifnull(2*pow(2,truncate(log2(fsmatchno/2),0)),1) Phase,
					@RealPhase:=truncate(@Phase/2, 0) RealPhase,
					CONCAT(FSScheduledDate," ",FSScheduledTime) AS MyDate,
					DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDBshort') . '") AS Dt,
					DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDB') . '") AS Dat,
					FSTeamEvent,
					FSEvent,
					FSScheduledTime,
					EvFinalFirstPhase
				FROM FinSchedule fs
				INNER JOIN HhtEvents ON HeTournament=FSTournament AND HeSession=0 AND HeTeamEvent=FSTeamEvent AND HeFinSchedule = CONCAT(FSScheduledDate," ",FSScheduledTime)
				inner join Events on FSEvent=EvCode and FSTeamEvent=EvTeamEvent and FsTournament=EvTournament
				where FsTournament=' . $_SESSION['TourId'] . ' and fsscheduleddate>0 AND HeHhtId=' . StrSafe_DB($_REQUEST["x_Hht"]) . '
				group by FsScheduledDate, FsScheduledTime, FsEvent, Phase
				order by FsScheduledDate, FsScheduledTime, FsEvent, Phase';
			$tmp=array();
			$Rs=safe_r_sql($Select);
			if (safe_num_rows($Rs)>0)
			{
				while ($MyRow=safe_fetch($Rs))
				{
					$val=$MyRow->FSTeamEvent . $MyRow->MyDate;
					$text=($MyRow->FSTeamEvent==0 ? get_text('FinInd','HTT') . ': ' . $MyRow->MyDate : get_text('FinTeam','HTT') . ': ' . $MyRow->MyDate);
					$tmp[$val]['events'][get_text(namePhase($MyRow->EvFinalFirstPhase, $MyRow->RealPhase) . '_Phase')][]= $MyRow->FSEvent;
					$tmp[$val]['date']= $MyRow->Dt . ' '. substr($MyRow->FSScheduledTime,0,5) . ' ' . ($MyRow->FSTeamEvent==0 ? get_text('FinInd','HTT') : get_text('FinTeam','HTT'));
					$tmp[$val]['selected']= isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$val ? ' selected' : '';
					$numOptions++;
				}
				foreach($tmp as $k => $v) {
					$val=array();
					foreach($v['events'] as $ph => $ev) $val[]= $ph . ' ('.implode('+',$ev).')';
					$ComboSes.='<option value="'.$k.'"'.$v['selected'].'>'.$v['date']  . ' '. implode('; ',$val).'</option>';
					if ($ComboSesArray!==null)
					{
						$ComboArr[]=$k;
					}
				}
			}
		}
		$ComboSes = '<select name="x_Session" id="x_Session">'
			. ($numOptions>1 ? '<option value="-1">---</option>' : '')
			. $ComboSes
			. '</select>';
	}

	if ($ComboSesArray!==null) {
		$ComboSesArray=$ComboArr;
	}
	return $ComboSes;
}

function CheckCredentials($OnlineId, $OnlineAuth, $Return, $LicenseVoucher='') {
	global $CFG;

	// check basic info
	if(empty($_SESSION['TourCode'])) {
		return get_text('CrackError');
	}

	if(empty($OnlineId) or strlen($OnlineAuth)==0) {
		return get_text('ErrEmptyFields', 'Errors');
	}

	$postdata = http_build_query( array(
		"ToId" => intval($OnlineId),
		"Auth1" => stripslashes($OnlineAuth),
		"Version" => UploadVersion,
		"ToCode" => $_SESSION['TourCode'],
		"CheckImgs" => '1',
		'Voucher' => $LicenseVoucher,
	), '', '&' );

	$opts = array('http' =>
		array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata,
		)
	);

	$URL=$CFG->IanseoServer."TourCheckCodes.php";
	$context = stream_context_create($opts);
	$stream = fopen($URL, 'r', false, $context);
	$tmp = null;

	if($stream===false) {
		$tmpErr = error_get_last();
		return($tmpErr["message"]);
	}

	// retrieving data from ianseo
	$StreamContent=stream_get_contents($stream);
	$varResponse=@json_decode($StreamContent, true);
	if(!$varResponse) {
		return(get_text('ErrGenericError', 'Errors'));
	}

	if($varResponse['error']!=0) {
		return get_text($varResponse['msg'], 'Tournament');
	}

	$_SESSION['OnlineServices']=$varResponse['services'];
	$_SESSION['OnlineAuth']=stripslashes($OnlineAuth);
	$_SESSION['OnlineId']=intval($OnlineId);
	$_SESSION['OnlineEventCode']=$_SESSION['TourCode'];
	$_SESSION['OnlineFiles']=$varResponse['files'];
	$_SESSION['OnlineUrls']=$varResponse['urls'];

	// No header images for PDF...
	$_SESSION['SendOnlinePDFImages']=$varResponse['imgs'];

	// sets the online code inside the tournament...
	safe_w_SQL("update Tournament set ToOnlineId=".intval($OnlineId)." where ToId={$_SESSION['TourId']}");

	// check if the service is available
	switch($Return) {
		case 'Modules/UpdateWeb/UpdateWeb.php':
			// old arrow2arrow permission
			if(!($_SESSION['OnlineServices']&2)) {
				return get_text('ServiceNotAvailable', 'Tournament');
			}
			break;
		case 'Modules/SyncroWeb/index.php':
			// InfoSystem permission
			if(!($_SESSION['OnlineServices']&4)) {
				return get_text('ServiceNotAvailable', 'Tournament');
			}
			break;
		case 'Tournament/UploadResults.php':
			// InfoSystem permission
			if(!($_SESSION['OnlineServices']&1)) {
				return get_text('ServiceNotAvailable', 'Tournament');
			}
			break;
		default:
	}
}

function get_Countries() {
	// get list from WA Countries
	$Flags=array(
		'AFG' => 'Afghanistan',
		'ALB' => 'Albania',
		'ALG' => 'Algeria',
		'AND' => 'Andorra',
		'ARG' => 'Argentina',
		'ARM' => 'Armenia',
		'ARU' => 'Aruba',
		'ASA' => 'American Samoa',
		'AUS' => 'Australia',
		'AUT' => 'Austria',
		'AZE' => 'Azerbaijan',
		'BAH' => 'Bahamas',
		'BAN' => 'Bangladesh',
		'BAR' => 'Barbados',
		'BEL' => 'Belgium',
		'BEN' => 'Benin',
		'BER' => 'Bermuda',
		'BHU' => 'Bhutan',
		'BIH' => 'Bosnia and Herzegovina',
		'BLR' => 'Belarus',
		'BOL' => 'Bolivia',
		'BRA' => 'Brazil',
		'BUL' => 'Bulgaria',
		'BUR' => 'Burkina Faso',
		'CAF' => 'Central African Republic',
		'CAM' => 'Cambodia',
		'CAN' => 'Canada',
		'CHA' => 'Chad',
		'CHI' => 'Chile',
		'CHN' => 'PR China',
		'CIV' => 'Cote d Ivoire',
		'CMR' => 'Cameroon',
		'COD' => 'DR Congo',
		'COL' => 'Colombia',
		'COM' => 'Comoros',
		'CRC' => 'Costa Rica',
		'CRO' => 'Croatia',
		'CUB' => 'Cuba',
		'CYP' => 'Cyprus',
		'CZE' => 'Czech Republic',
		'DEN' => 'Denmark',
		'DJI' => 'Djibouti',
		'DMA' => 'Dominica',
		'DOM' => 'Dominican Republic',
		'ECU' => 'Ecuador',
		'EGY' => 'Egypt',
		'ERI' => 'Eritrea',
		'ESA' => 'El Salvador',
		'ESP' => 'Spain',
		'EST' => 'Estonia',
		'FIJ' => 'Fiji',
		'FIN' => 'Finland',
		'FLK' => 'Falkland Island',
		'FPO' => 'Tahiti',
		'FRA' => 'France',
		'FRO' => 'Faroe Islands',
		'GAB' => 'Gabon',
		'GBR' => 'Great Britain',
		'GEO' => 'Georgia',
		'GER' => 'Germany',
		'GHA' => 'Ghana',
		'GLP' => 'Guadalupe',
		'GRE' => 'Greece',
		'GUA' => 'Guatemala',
		'GUI' => 'Guinea',
		'GUM' => 'Guam',
		'GUY' => 'Guyana',
		'HAI' => 'Haiti',
		'HKG' => 'Hong Kong, China',
		'HON' => 'Honduras',
		'HUN' => 'Hungary',
		'INA' => 'Indonesia',
		'IND' => 'India',
		'IOA' => 'Int. Olympic Archer',
		'IPA' => 'Int. Paralympic Archer',
		'IRI' => 'IR Iran',
		'IRL' => 'Ireland',
		'IRQ' => 'Iraq',
		'ISL' => 'Iceland',
		'ISR' => 'Israel',
		'ISV' => 'Virgin Islands, US',
		'ITA' => 'Italy',
		'IVB' => 'British Virgin Islands',
		'JOR' => 'Jordan',
		'JPN' => 'Japan',
		'KAZ' => 'Kazakhstan',
		'KEN' => 'Kenya',
		'KGZ' => 'Kyrgyzstan',
		'KIR' => 'Kiribati',
		'KOR' => 'Korea',
		'KOS' => 'Kosovo',
		'KSA' => 'Saudi Arabia',
		'KUW' => 'Kuwait',
		'LAO' => 'Laos',
		'LAT' => 'Latvia',
		'LBA' => 'Libya',
		'LBR' => 'Liberia',
		'LES' => 'Lesotho',
		'LIB' => 'Lebanon',
		'LIE' => 'Liechtenstein',
		'LTU' => 'Lithuania',
		'LUX' => 'Luxembourg',
		'MAC' => 'Macau, China',
		'MAD' => 'Madagascar',
		'MAR' => 'Morocco',
		'MAS' => 'Malaysia',
		'MAW' => 'Malawi',
		'MDA' => 'Moldova',
		'MEX' => 'Mexico',
		'MGL' => 'Mongolia',
		'MKD' => 'North Macedonia',
		'MLI' => 'Mali',
		'MLT' => 'Malta',
		'MNE' => 'Montenegro',
		'MON' => 'Monaco',
		'MRI' => 'Mauritius',
		'MTN' => 'Mauritania',
		'MTQ' => 'Martinique',
		'MYA' => 'Myanmar',
		'NAM' => 'Namibia',
		'NCA' => 'Nicaragua',
		'NCL' => 'New Caledonia',
		'NED' => 'Netherlands',
		'NEP' => 'Nepal',
		'NFK' => 'Norfolk Island',
		'NGR' => 'Nigeria',
		'NIG' => 'Niger',
		'NIU' => 'Niue',
		'NMI' => 'Northern Mariana Islands',
		'NOR' => 'Norway',
		'NZL' => 'New Zealand',
		'PAK' => 'Pakistan',
		'PAN' => 'Panama',
		'PAR' => 'Paraguay',
		'PER' => 'Peru',
		'PHI' => 'Philippines',
		'PLW' => 'Palau',
		'PNG' => 'Papua New Guinea',
		'POL' => 'Poland',
		'POR' => 'Portugal',
		'PRK' => 'DPR Korea',
		'PUR' => 'Puerto Rico',
		'QAT' => 'Qatar',
		'ROU' => 'Romania',
		'RSA' => 'South Africa',
		'RUS' => 'Russia',
		'RWA' => 'Rwanda',
		'SAM' => 'Samoa',
		'SCG' => 'Serbia and Montenegro',
		'SEN' => 'Senegal',
		'SGP' => 'Singapore',
		'SKN' => 'Saint Kitts and Nevis',
		'SLE' => 'Sierra Leone',
		'SLO' => 'Slovenia',
		'SMR' => 'San Marino',
		'SOL' => 'Solomon Islands',
		'SOM' => 'Somalia',
		'SRB' => 'Serbia',
		'SRI' => 'Sri Lanka',
		'SUD' => 'Sudan',
		'SUI' => 'Switzerland',
		'SUN' => 'USSR',
		'SUR' => 'Suriname',
		'SVK' => 'Slovakia',
		'SWE' => 'Sweden',
		'SYR' => 'Syria',
		'TGA' => 'Tonga',
		'THA' => 'Thailand',
		'TJK' => 'Tajikistan',
		'TKM' => 'Turkmenistan',
		'TOG' => 'Togo',
		'TPE' => 'Chinese Taipei',
		'TTO' => 'Trinidad and Tobago',
		'TUN' => 'Tunisia',
		'TUR' => 'Turkey',
		'UAE' => 'UAE',
		'UGA' => 'Uganda',
		'UKR' => 'Ukraine',
		'URU' => 'Uruguay',
		'USA' => 'USA',
		'UZB' => 'Uzbekistan',
		'VAN' => 'Vanuatu',
		'VEN' => 'Venezuela',
		'VIE' => 'Vietnam',
		'VIN' => 'St Vincent and the Grenadines',
		'YEM' => 'Yemen',
		'YUG' => 'Yugoslavia',
		'ZAM' => 'Zambia',
		'ZIM' => 'Zimbabwe',
	);
	return $Flags;
}