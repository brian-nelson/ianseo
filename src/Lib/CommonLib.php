<?php

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
	static $OldLang=null;
	if($OldLang==null) {
		$OldLang=SelectLanguage();
	}

	$q=safe_r_SQL("select ToPrintLang from Tournament where ToId=$TourId");
	$r=safe_fetch($q);
	@define('PRINTLANG', $r->ToPrintLang);
}

