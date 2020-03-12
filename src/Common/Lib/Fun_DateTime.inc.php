<?php
/*
													- Fun_DateTime.inc.php -
	File contenente le funzione per la manipolazione della data e dell'ora
*/

	function dateRenderer($date,$format='d-m-Y H:i')
	{
	// if date is 0000-00-00 00:00:00 will be convert to empty string
		if (preg_match('/0000-00-00( 00:00:00)*/',$date))
			return '';

		if (!(preg_match('/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/',$date) || preg_match('/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/',$date)))
			return '';

		$day=0;
		$month=0;
		$year=0;
		$hour=0;
		$min=0;
		$sec=0;

		$d='';
		$t='';

		if (!preg_match('/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/',$date))
			$date.= ' 00:00:00';

		list($d,$t)=explode(' ',$date);
		$d=explode('-',$d);

		$day=$d[2];
		$month=$d[1];
		$year=$d[0];

		if ($t!='')
		{
			$t=explode(':',$t);
			$hour=$t[0];
			$min=$t[1];
			$sec=$t[2];
		}

		//print $day . ' ' . $month . ' ' . $year . ' ' . $hour . ' ' . $min . ' ' . $sec . ' ';

		$mktime=mktime($hour,$min,$sec,$month,$day,$year);

		return date($format,$mktime);
	}

/*
 * Se $Code è una data buona nel formato della localizzazione
 * ritorna la data nella forma yyyy-mm-dd altrimenti false;
 * Se $Code è una stringa vuota ritorna una stringa vuota.
 *
 * I formati considerati buoni sono quelli nella switch.
 * Tutto il resto NON va neppure considerato
 *
 * Si suppone che il formato divida le parti della data
 * con un separatore che rispetta la regex [\./-]{1}.
 * Se format non è null verrà usato quello come formato di origine se no
 * quello della localizzazione.
 */
	function ConvertDateLoc($Code='') {
		// no code returns
		if (empty($Code)) return '';

		$splitCode=preg_split('#[./-]#',$Code);
		// devo avere 3 parti
		if (count($splitCode)!=3) return '';

		if(strlen($splitCode[0])==4) {
			// date is YYYY-mm-dd
			if(checkdate(intval($splitCode[1]),intval($splitCode[2]),intval($splitCode[0]))) return sprintf('%04d-%02d-%02d', $splitCode[0], $splitCode[1], $splitCode[2]);
			return ''; //date in invalid format or not a valid date
		}

		// check local format
		$splitFormat=preg_split('#[./-]#', get_text('DateFmtDB'));
		// fallback if some strange format has been used in DB
		if(!in_array('%d', $splitFormat) or !in_array('%m', $splitFormat) or !in_array('%Y', $splitFormat)) $splitFormat=array('%d', '%m', '%Y');

		$d=$splitCode[array_search('%d', $splitFormat)];
		$m=$splitCode[array_search('%m', $splitFormat)];
		$y=$splitCode[array_search('%Y', $splitFormat)];

		// check the year and adjust in case
		if(strlen($y)==2) {
			$y+=2000;
			if($y>date('Y')+2) $y-=100; // date in the future more than 2 years was a year of last century
		}

		if (checkdate(intval($m),intval($d),intval($y))) return sprintf('%04d-%02d-%02d', $y, $m, $d);
		return ''; //date in invalid format or not a valid date
	}

/*
	- ConvertDate($TheDate)
	Converte la data $TheDate (che deve essere nella forma d-m-yyyy) nella forma %Y-%m-%d per MySql.
	(Un tempo.... Ora piglia una data e ne ricava il formato dalla localizzazione)

	Se $TheDate non é una data valida verrà ritornato false altrimenti la data riformattata.
*/
	function ConvertDate($TheDate)
	{
		if (!preg_match('#^[0-9]{1,2}[ /.-][0-9]{1,2}[ /.-][0-9]{4}$#',$TheDate))
		{
			return false;
		}

		$mm=""; $dd=""; $yy="";
		list($dd,$mm,$yy)=preg_split('#[ /.-]#',$TheDate);

		$bits=preg_split("#[ /.-]#",get_text('DateFmt'));
		if(in_array($bits[0],array('d','j'))) $d=$dd;
		elseif(in_array($bits[0],array('m','n'))) $m=$dd;
		elseif(in_array($bits[0],array('y','Y'))) $y=$dd;

		if(in_array($bits[1],array('d','j'))) $d=$mm;
		elseif(in_array($bits[1],array('m','n'))) $m=$mm;
		elseif(in_array($bits[1],array('y','Y'))) $y=$mm;

		if(in_array($bits[2],array('d','j'))) $d=$yy;
		elseif(in_array($bits[2],array('m','n'))) $m=$yy;
		elseif(in_array($bits[2],array('y','Y'))) $y=$yy;

		if($d and $m and $y) {
			$dd=$d; $mm=$m; $yy=$y;
		}

		if (strlen($mm)<2) $mm='0'.$mm;
		if (strlen($dd)<2) $dd='0'.$dd;
		if (strlen($yy)<4) $yy= 2000+$yy;


		if (checkdate($mm,$dd,$yy))
		{
			return $yy . '-' . $mm . '-' . $dd;
		}
		else
		{
			return false;
		}
	}

/*
	- RevertDate($TheDate)
	Converte la data $TheDate (che deve essere nella forma Y-m-d) nella forma d-m-Y.
	Se $TheDate non � una data valida verr� ritornato false altrimenti la data riformattata.
*/
	function RevertDate($TheDate)
	{
		if (!preg_match('/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}/',$TheDate))
		{
			return false;
		}

		$mm=""; $dd=""; $yy="";
		list($yy,$mm,$dd)=explode('-',$TheDate);

		if (strlen($mm)<2) $mm='0'.$mm;
		if (strlen($dd)<2) $dd='0'.$dd;

		if (checkdate($mm,$dd,$yy))
		{
			return date(get_text('DateFmt'), mktime(0, 0, 0, $mm, $dd, $yy));
		}
		else
		{
			return false;
		}
	}

/*
	- Convert24Time($TheTime)
	Converte l'ora $TheTime (che deve essere nel formato h:m) nella forma a 24 ore.
	Se $TheTime non è un'ora valida verrà ritornato false altrimenti l'ora riformattata.
*/
	function Convert24Time($TheTime)
	{
		if (!preg_match('/^[0-9]{1,2}(:[0-9]{1,2}){0,1}$/',$TheTime)) {
			return false;
		}

		$tmp = explode(':',$TheTime);
		$hh=$tmp[0];
		if(count($tmp)==1) {
			$mm=0;
		} else {
			$mm=$tmp[1];
		}

		if ($hh<0 or $hh>23 or $mm<0 or $mm>59) {
			return false;
		}

		return sprintf('%02d:%02d', $hh, $mm);
	}


/*
	- TournamentDate2String($DateFrom, $DateTo)
	Converte in stringa secondo le impostazioni di traduzione la data di inizio e fine del torneo
	$DateFrom e $DateTo sono Unix Timestamp
*/

	function TournamentDate2String($DateFrom, $DateTo)
	{
		$TmpData="";
		if(is_numeric($DateFrom)) {
			if($DateFrom == $DateTo)			//Inizio e Fine Coincidono
			{
				$TmpData=date( get_text('DateFmt'), $DateFrom);
			}
			else
			{
				$TmpData = get_text('DateFmtMoreDays', 'Common', array( date( get_text('DateFmt'),$DateFrom), date(get_text('DateFmt'),$DateTo)));
			}
		} else {
			if($DateFrom == $DateTo)			//Inizio e Fine Coincidono
			{
				$TmpData=$DateFrom;
			}
			else
			{
				$TmpData = get_text('DateFmtMoreDays', 'Common', array( $DateFrom, $DateTo));
			}
		}
		return $TmpData;
	}

	// usata SOLO nello standard ORIS, quindi nessuna localizzazione!
	function TournamentDate2StringShort($DateFrom, $DateTo)
	{
		$TmpData="";

		list($y,$m,$d)=explode('-',$DateFrom);
		$DateFrom=mktime(0,0,0,$m,$d,$y);

		list($y,$m,$d)=explode('-',$DateTo);
		$DateTo=mktime(0,0,0,$m,$d,$y);

		if($DateFrom == $DateTo)			//Inizio e Fine Coincidono
			$TmpData=date('j M Y',$DateFrom);
		else if(date('m',$DateFrom)==date('m',$DateTo))
			$TmpData=date('j',$DateFrom) . '-' . date('j',$DateTo) . date(' M Y',$DateFrom);
		else if(date('Y',$DateFrom)==date('Y',$DateTo))
			$TmpData=date('j M',$DateFrom) . ' - ' . date('j M',$DateTo) . date(' Y',$DateFrom);
		else
			$TmpData=date('j M Y',$DateFrom) . ' - ' . date('j M Y',$DateTo);

		return $TmpData;
	}

// differenza in anni tra due date
	function dateYearDiff( $endDate, $beginDate)
	{
	           $date_parts1=explode('-', $beginDate);
	           $date_parts2=explode('-', $endDate);
	          /* print'<pre>';
	           print_r($date_parts1);
	           print_r($date_parts2);
	           print '</pre>';*/
	           $start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
	           $end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
	           return round(($end_date - $start_date)/365,0);
	}

// date DEVE essere in formato Y-m-d
function formatTextDate($date, $AddWeekDay=false) {
	$tmp=explode('-', $date);
	$ret = ltrim($tmp[2], '0').' '.get_text($tmp[1].'_Short','DateTime').' '.$tmp[0];
	if($AddWeekDay) {
		$ret .= ", " . formatWeekDayLong($date);
	}
	return $ret;
}

// date DEVE essere in formato Y-m-d
function formatWeekDayLong($date) {
	return get_text("DayOfWeekLong_".date("w", strtotime($date)));
}

// BEST GUESS of the date
function CleanDate($Date) {
	$Y=0;
	$m=0;
	$d=0;

	$bits=preg_split('#[ ./-]#sim', $Date);
	foreach($bits as $k=>$bit) $bits[$k]=intval($bit);

	// if only one, presume it is the day, the rest is added based on now
	if(count($bits)==1) {
		return date('Y-m-').str_pad($bits[0], 2, '0', STR_PAD_LEFT);
	}
	if(count($bits)==2) {
		// day+month, so add year and convert!
		$bits[]=date('Y');
	}

	if(strlen($bits[0])==4) {
		// it is a Y-m-d already
		return sprintf('%04d-%02d-%02d', $bits[0], $bits[1], $bits[2]);
	}

	return ConvertDate(implode('-', $bits));
}

/*
  170122 Ken
  Converts a DateTime value from one timezone to another
  Params:
  $source: a DateTime object containing our source date
  $source_timezone: an integer that refers to the timezone of our source data. For example, if the
                    timezone of our source data is ‘Asia/Ho_Chi_Minh’ (UTC+7), $source_timezone is 7.
  $dest_timezone: an integer that refers to the timezone of our target data
*/
function convertToOtherTz($source, $source_timezone, $dest_timezone){
    $offset = $dest_timezone - $source_timezone;
    $offsetMins = $offset * 60;
    if($offset == 0)
        return $source->format('Y-m-d H:i:s');
    $source->modify($offsetMins.' minutes');
    return $source->format('Y-m-d H:i:s');
}

/*
  180511 Ken
  Formats the competition date(s) for display
*/
function formatCompDate($startDate, $endDate, $IncludeYear = false) {
	$compDates = '';
	$Y = ($IncludeYear ? ' Y' : '');

	$dtStart = DateTime::createFromFormat('d-m-Y', $startDate);
	$dtEnd = DateTime::createFromFormat('d-m-Y', $endDate);

	if($startDate == $endDate) {
		$compDates = $dtEnd->format('j M'.$Y);

	} else {
		$d1=explode('-', $startDate);
		$d2=explode('-', $endDate);

		if($d1[2] != $d2[2]) {  // years different
			$compDates = $dtStart->format('j M Y').' - '.$dtEnd->format('j M Y');

		} elseif($d1[1] != $d2[1]) {  // months different
			$compDates = $dtStart->format('j M').' - '.$dtEnd->format('j M'.$Y);

		} elseif($d1[0] != $d2[0]) {  // days different
			$compDates = $dtStart->format('j').'-'.$dtEnd->format('j M'.$Y);

		} else {
			$compDates = $dtStart->format('j M'.$Y);
		}
	}
	return $compDates;
}

/*
  180520 Ken
  Formats the last update datetime for display
  $timestamp should be a string in the format YYYY-MM-DD HH:MM:SS
  $showYear should be a boolean
*/
function formatUpdateDatetime($timestamp, $showYear) {
	$displayLastUp = new DateTime($timestamp);

	$lastUp = clone $displayLastUp;
	$lastUp->setTime( 0, 0, 0 ); // reset time part, to prevent partial comparison

	$today = new DateTime(); // This object represents current date/time
	$today->setTime( 0, 0, 0 ); // reset time part, to prevent partial comparison

	$diff = $today->diff( $lastUp );
	$diffDays = (integer)$diff->format( "%R%a" ); // Extract days count in interval

	if ($today->format('Y') == $displayLastUp->format('Y')) {
		// Special format for updates that recently occurred
		$outputLastUp = '';
		switch( $diffDays ) {
			case 0:
				$outputLastUp = "Today".$displayLastUp->format(' H:i');
				break;
			case -1:
				$outputLastUp = "Yesterday".$displayLastUp->format(' H:i');
				break;
			default:
				$outputLastUp = $displayLastUp->format('j M H:i');
		}
	} else {
		$outputLastUp = $showYear ? $displayLastUp->format('j M Y H:i') : $displayLastUp->format('j M H:i');;
	}
	return $outputLastUp;
}
