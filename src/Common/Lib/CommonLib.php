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
		case '96': return 12; break;
		case '94': return 11; break;
		case '46': return 10; break;
		case '48': return  9; break;
		case '24': return  8; break;
		case '22': return  7; break;
		case '10': return  6; break;
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
		case '190': return 22; break;
		case '222': return 21; break;
		case '174': return 20; break;
		case '206': return 19; break;
		case '102': return 18; break;
		case '86':  return 17; break;
		case '110': return 16; break;
		case '94':  return 15; break;
		case '46':  return 14; break;
		case '54':  return 13; break;
		case '42':  return 12; break;
		case '50':  return 11; break;
		case '24':  return 10; break;
		case '20':  return  9; break;
		case '26':  return  8; break;
		case '22':  return  7; break;
		case '10':  return  6; break;
		case '12':  return  5; break;
	}

	return false;
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
function phpVars2js($vars)
{
	$out='';

	$out.='<script type="text/javascript">' . "\n";

	foreach ($vars as $k => $v)
	{

		if (is_array($v))		// array
		{

			$out.='var ' . $k . '=new Array();' . "\n";
			foreach ($v as $index => $value)
			{
				if (!is_numeric($index))
				{
					$index="'" . $index . "'";
				}
				$out.=$k . '[' . $index . ']="' . addslashes($value) . '";' . "\n";
			}
		}
		else		// var scalare
		{
			$out.='var ' . $k . '="' . addslashes($v) . '";' . "\n";
		}

	}

	$out.='</script>' . "\n";

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

	$q=safe_r_SQL("select ToPrintLang from Tournament where ToId=$TourId");
	$r=safe_fetch($q);
	@define('PRINTLANG', $r->ToPrintLang);
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