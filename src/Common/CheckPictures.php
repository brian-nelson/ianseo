<?php
/**
 * This function is called on opening of a tournament and checks
 * if all flags and pictures are on disk (for TV output and Boinx Output)
 * and up to date
 *
 * It is also called on each TV Output or Boinx XML generation and only checks
 * if recent changes have been done getting the first file of the considered type
 * and checking its timestamp against the database.
 *
 * If a change has been done BUT the image seems to be wrong, the safest way is open the tournament
 * with another browser or from a different computer, to activate the first check!
 *
 */
function CheckPictures($TourCode='', $open=false, $all=false, $force=false) {
	global $CFG;
	if($all) {
		$TourCode='All';
		$TourId= -1;
	} else {
		if(!$TourCode) $TourCode=$_SESSION['TourCodeSafe'];
		$TourId= getIdFromCode($TourCode);
	}
	$TourCodeSafe=preg_replace('/[^a-z0-9_.-]/sim', '', $TourCode);

	$OnlyNewer=''; // starts with no filters at all

	if($open) {
		$now=strtotime('-10 days');

		// on opening of the tournament erase ALL the pictures older than 10 days;
		$Images=glob($CFG->DOCUMENT_PATH.'TV/Photos/*.jpg');
		foreach($Images as $img) {
			if(filemtime($img)<$now) unlink($img);
		}
	} else {
		// in all other cases, sets the filter to one of the images of the tournament
		// NO GUARANTEE that it is the oldest or the most recent
		// but it should be enough because of the 1st step
// 		$Images=glob($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-*.jpg');
// 		if($Images) {
// 			$OnlyNewer = filemtime($Images[0]); // create filter
// 			// speeds the process for the next time the function is called, as it will skip this file :)
// 			touch($Images[0]);
// 		}
// 		$Images=glob($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-*.jpg');
// 		if($Images) {
// 			$OnlyNewer = max($OnlyNewer,filemtime($Images[0])); // create filter
// 			// speeds the process for the next time the function is called, as it will skip this file :)
// 			touch($Images[0]);
// 		}
	}

	if($all) {
		$Sql = "(select"
			. " 'Fl' PictureType, "
			. " FlCode PictureCode,"
			. " FlJPG Picture,"
			. " unix_timestamp(FlEntered) PictureTime "
			. "from"
			. " Flags "
			. "where"
			. " FlTournament = -1 "
// 			. " and FlChecked!='1'"
			. " and FlJPG>'') "
			. "UNION "
			. "(select"
			. " 'FlSvg', "
			. " FlCode,"
			. " FlSVG,"
			. " unix_timestamp(FlEntered) UnixTime "
			. "from"
			. " Flags "
			. "where"
			. " FlTournament = -1"
// 			. " and FlChecked!='1'"
			. " and FlSVG>'') "
			;
	} else {
		$Sql = "(select 'En' PictureType, PhEnId PictureCode, PhPhoto Picture, unix_timestamp(PhPhotoEntered) PictureTime
			from Entries
			inner join Photos on EnId=PhEnId and PhPhoto!=''
			where EnTournament=$TourId "
// 			. ($OnlyNewer ? " and unix_timestamp(PhPhotoEntered)>$OnlyNewer " : '')
			. " )
			UNION (select 'Fl', FlCode, FlJPG, unix_timestamp(FlEntered) UnixTime
			from Flags
			inner join Countries on FlCode=CoCode and CoTournament=$TourId
			where FlTournament in (-1, $TourId)"
// 			. ($OnlyNewer ? " and unix_timestamp(FlEntered)>$OnlyNewer ":'')
			. " and FlJPG!='')
			UNION (select 'FlSvg', FlCode, FlSVG, unix_timestamp(FlEntered) UnixTime
			from Flags
			inner join Countries on FlCode=CoCode and CoTournament=$TourId
			where FlTournament in (-1, $TourId)"
// 			. ($OnlyNewer ? " and unix_timestamp(FlEntered)>$OnlyNewer ":'')
			. " and FlSVG!='') "
			;
	}

	$q=safe_r_sql($Sql);
	while($r=safe_fetch($q)) {
		if($r->PictureType=='FlSvg') {
			$ImName = $CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-'.$r->PictureType.'-'.$r->PictureCode.'.svg';
			if($force or !file_exists($ImName) or filemtime($ImName) < $r->PictureTime) {
				$f=fopen($ImName, 'w');
				fwrite($f, gzinflate($r->Picture));
				fclose($f);
			}
		} else {
			$ImName = $CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-'.$r->PictureType.'-'.$r->PictureCode.'.jpg';
			if($force or !file_exists($ImName) or filemtime($ImName) < $r->PictureTime) {
				if($im=@imagecreatefromstring(base64_decode($r->Picture))) {
					Imagejpeg($im, $ImName,95);
					if(class_exists('Imagick')) {
						$im=new Imagick($ImName);
						$im->opaquePaintImage('#000000', '#151515', 14, false);
						$im->writeImage($ImName);
					}
				} elseif($r->PictureType=='En') {
					safe_w_SQL("delete from Photos where PhEnId=$r->PictureCode");
				}
			}
		}
	}

	// updates the multimedia content as well...
	$q=safe_r_sql("select * from TVContents where TVCMimeType in ('image/gif','image/jpeg','image/png') and TVCTournament in (-1, $TourId)");
	while($r=safe_fetch($q)) {
		$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/TV-'.($r->TVCTournament==-1?'BaseIanseo':$TourCodeSafe).'-'.($r->TVCName=='IdCardFooter' ? $r->TVCName : $r->TVCId).'.jpg';
		if($force or !file_exists($ImName) or filemtime($ImName) < $r->TVCTimestamp) {
			$im=imagecreatefromstring($r->TVCContent);
			imagejpeg($im, $ImName, 90);
		}
	}

	// AND the Tour images as well...
	$q=safe_r_sql("select * from Tournament where ToId = $TourId");
	if($r=safe_fetch($q)) {
		$imgLname=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToLeft.jpg';
		$imgRname=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToRight.jpg';
		$imgBname=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToBottom.jpg';
		if($r->ToImgL and $im=@imagecreatefromstring($r->ToImgL)) {
			if($force or !file_exists($imgLname)) imagejpeg($im, $imgLname, 90);
		} else {
			@unlink($imgLname);
		}
		if($r->ToImgR and $im=@imagecreatefromstring($r->ToImgR)) {
			if($force or !file_exists($imgRname)) imagejpeg($im, $imgRname, 90);
		} else {
			@unlink($imgRname);
		}
		if($r->ToImgB and $im=@imagecreatefromstring($r->ToImgB)) {
			if($force or !file_exists($imgBname)) imagejpeg($im, $imgBname, 90);
		} else {
			@unlink($imgBname);
		}
	}

	// AND the Tour Backnumbers as well...
	$q=safe_r_sql("select BnFinal, BnBackground from BackNumber where BnTournament = $TourId");
	if(!safe_num_rows($q)) {
		if(!$all)
			createBackno($TourCodeSafe);
	} else {
		while($r=safe_fetch($q)) {
			$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-BackNo-'.$r->BnFinal.'.jpg';
			if($r->BnBackground and $im=@imagecreatefromstring($r->BnBackground)) {
				if($force or !file_exists($ImName)) imagejpeg($im, $ImName, 90);
			} else {
				@unlink($ImName);
			}
		}
	}

	// and the accreditation pictures too...
	$q=safe_r_sql("select IcBackground, IcType, IcNumber from IdCards where IcTournament = $TourId");
	while($r=safe_fetch($q)) {
		$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-'.$r->IcType.'-'.$r->IcNumber.'-Accreditation.jpg';
		if($r->IcBackground and $im=@imagecreatefromstring($r->IcBackground)) {
			if($force or !file_exists($ImName)) imagejpeg($im, $ImName, 90);
		} else {
			@unlink($ImName);
		}
	}
	$q=safe_r_sql("select IceContent, IceOrder, IceType, IceCardType, IceCardNumber from IdCardElements where IceTournament = $TourId and IceType in ('Image', 'RandomImage')");
	while($r=safe_fetch($q)) {
		$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-'.$r->IceType.'-'.$r->IceCardType.'-'.$r->IceCardNumber.'-'.$r->IceOrder.'.jpg';
		if($r->IceContent and $im=@imagecreatefromstring($r->IceContent)) {
			if($force or !file_exists($ImName)) imagejpeg($im, $ImName, 90);
		} else {
			@unlink($ImName);
		}
	}

	// and the Images too...
	$q=safe_r_sql("select * from Images where ImTournament = $TourId");
	while($r=safe_fetch($q)) {
		$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe
			.'-'.$r->ImIocCode
			.'-'.$r->ImSection
			.'-'.$r->ImReference
			.'-'.$r->ImType.'.jpg';
		if($r->ImContent and $im=@imagecreatefromstring($r->ImContent)) {
			if($force or !file_exists($ImName) or filemtime($ImName) < $r->ImgLastUpdate) imagejpeg($im, $ImName, 90);
		} else {
			@unlink($ImName);
		}
	}
}

/**
 * This function redraws all the most recent images type by type based on the last mtime image in the filesystem;
 * furthermore, it inserts into the competition all the default country images
 * @param string $TourCode The code of the competition
 * @param boolean $Force force the deletion and thus the recreation of all the images
 */
function RedrawPictures($TourCode='', $Force=false) {
	global $CFG;

	if(!$TourCode) $TourCode=$_SESSION['TourCodeSafe'];
	$TourId= getIdFromCode($TourCode);

	$TourCodeSafe=$TourCode;

	if($Force) {
		// removes all the media, thus forcing the redraw of everything
		RemoveMedia($TourCode);
	}

	// Inserts the default flags inside the Competition Countries
	safe_w_sql("insert ignore into Flags (FlCode, FlTournament, FlJPG, FlSVG)
	select CoCode, $TourId, FlJPG, FlSVG
	from Countries
	inner join Flags on FlCode=CoCode and FlTournament=-1
	where CoTournament=$TourId and CoCode not in (select FlCode from Flags where FlTournament=$TourId)");

	// ===================
	//       ENTRIES
	// ===================
	$MaxTime=0;
	$Objects=array();

	foreach(glob($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-En-*') as $file) {
		// gets the most recent item
		if(($time=filemtime($file))> $MaxTime) $MaxTime=$time;
		// fetches all the items there
		$Objects[]=substr(basename($file), strlen($TourCode.'-En-'),-4);
	}

	// selects all the more recent items in DB and all the missing items
	$filter=(count($Objects)<200 ? " or EnId not in ('".implode("','", $Objects)."')" : '');
	$SQL="select EnId, PhPhoto, '$TourCode' ToCode
			from Entries
			inner join Photos on EnId=PhEnId and PhPhoto!=''
			where EnTournament=$TourId
				and (unix_timestamp(PhPhotoEntered)>{$MaxTime} $filter)";
	$q=safe_r_sql($SQL);
	updatePhoto('', $q);

	// ===================
	//      Flags JPG
	// ===================
	$MaxTime=0;
	$Objects=array();

	foreach(glob($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-Fl-*') as $file) {
		// gets the most recent item
		if(($time=filemtime($file)) > $MaxTime) $MaxTime=$time;
		// fetches all the items there
		$Objects[]=substr(basename($file), strlen($TourCode.'-Fl-'),-4);
	}

	// selects all the more recent items in DB and all the missing items
	$filter=(count($Objects)<30 ? " or FlCode not in ('".implode("','", $Objects)."')" : '');
	$SQL="Select FlCode, FlJPG, '$TourCode' ToCode, FlTournament ToId
			from Flags
			where FlTournament = $TourId and FlJPG!=''
				and (unix_timestamp(FlEntered)>{$MaxTime} $filter)";
	$q=safe_r_sql($SQL);
	updateFlag('', 'JPG', $q);

	// ===================
	//      Flags SVG
	// ===================
	$MaxTime=0;
	$Objects=array();

	foreach(glob($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-FlSvg-*') as $file) {
		// gets the most recent item
		if(($time=filemtime($file)) > $MaxTime) $MaxTime=$time;
		// fetches all the items there
		$Objects[]=substr(basename($file), strlen($TourCode.'-FlSvg-'),-4);
	}

	// selects all the more recent items in DB and all the missing items
	$filter=(count($Objects)<30 ? " or FlCode not in ('".implode("','", $Objects)."')" : '');
	$SQL="Select FlCode, FlSVG, '$TourCode' ToCode, FlTournament ToId
			from Flags
			where FlTournament = $TourId and FlJPG!=''
				and (unix_timestamp(FlEntered)>{$MaxTime} $filter)";
	$q=safe_r_sql($SQL);
	updateFlag('', 'SVG', $q);

	// ===================
	//      Multimedia
	// ===================
	$q=safe_r_sql("select * from TVContents where TVCMimeType in ('image/gif','image/jpeg','image/png') and TVCTournament in (-1, $TourId)");
	while($r=safe_fetch($q)) {
		$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/TV-'.($r->TVCTournament==-1?'BaseIanseo':$TourCodeSafe).'-'.($r->TVCName=='IdCardFooter' ? $r->TVCName : $r->TVCId).'.jpg';
		if($Force or !file_exists($ImName) or filemtime($ImName) < $r->TVCTimestamp) {
			$im=imagecreatefromstring($r->TVCContent);
			imagejpeg($im, $ImName, 90);
		}
	}

	// AND the Tour images as well...
	$q=safe_r_sql("select * from Tournament where ToId = $TourId");
	if($r=safe_fetch($q)) {
		$imgLname=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToLeft.jpg';
		$imgRname=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToRight.jpg';
		$imgBname=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToBottom.jpg';
		if($r->ToImgL and $im=@imagecreatefromstring($r->ToImgL)) {
			if($Force or !file_exists($imgLname)) imagejpeg($im, $imgLname, 90);
		} else {
			@unlink($imgLname);
		}
		if($r->ToImgR and $im=@imagecreatefromstring($r->ToImgR)) {
			if($Force or !file_exists($imgRname)) imagejpeg($im, $imgRname, 90);
		} else {
			@unlink($imgRname);
		}
		if($r->ToImgB and $im=@imagecreatefromstring($r->ToImgB)) {
			if($Force or !file_exists($imgBname)) imagejpeg($im, $imgBname, 90);
		} else {
			@unlink($imgBname);
		}
	}

	// AND the Tour Backnumbers as well...
	$q=safe_r_sql("select BnFinal, BnBackground from BackNumber where BnTournament = $TourId");
	if(!safe_num_rows($q)) {
		createBackno($TourCodeSafe);
	} else {
		while($r=safe_fetch($q)) {
			$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-BackNo-'.$r->BnFinal.'.jpg';
			if($r->BnBackground and $im=@imagecreatefromstring($r->BnBackground)) {
				if($Force or !file_exists($ImName)) imagejpeg($im, $ImName, 90);
			} else {
				@unlink($ImName);
			}
		}
	}

	// and the accreditation pictures too...
	$q=safe_r_sql("select IcBackground from IdCards where IcTournament = $TourId");
	if($r=safe_fetch($q)) {
		$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Accreditation.jpg';
		if($r->IcBackground and $im=@imagecreatefromstring($r->IcBackground)) {
			if($Force or !file_exists($ImName)) imagejpeg($im, $ImName, 90);
		} else {
			@unlink($ImName);
		}
	}
	$q=safe_r_sql("select IceContent, IceType, IceOrder, IceCardType, IceCardNumber from IdCardElements where IceContent>'' and IceTournament = $TourId and IceType in ('Image', 'ImageSvg', 'RandomImage')");
	while($r=safe_fetch($q)) {
		if($r->IceType=='ImageSvg') {
			$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-'.$r->IceType.'-'.$r->IceCardType.'-'.$r->IceCardNumber.'-'.$r->IceOrder.'.svg';
			if($im=@gzinflate($r->IceContent)) {
				if($Force or !file_exists($ImName)) file_put_contents($ImName, $im);
			} else {
				@unlink($ImName);
			}
		} else {
			$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-'.$r->IceType.'-'.$r->IceCardType.'-'.$r->IceCardNumber.'-'.$r->IceOrder.'.jpg';
			if($im=@imagecreatefromstring($r->IceContent)) {
				if($Force or !file_exists($ImName)) imagejpeg($im, $ImName, 90);
			} else {
				@unlink($ImName);
			}
		}
	}

	// and the Images too...
	$q=safe_r_sql("select * from Images where ImTournament = $TourId");
	while($r=safe_fetch($q)) {
		$ImName=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe
			.'-'.$r->ImIocCode
			.'-'.$r->ImSection
			.'-'.$r->ImReference
			.'-'.$r->ImType.'.jpg';
		if($r->ImContent and $im=@imagecreatefromstring($r->ImContent)) {
			if($Force or !file_exists($ImName) or filemtime($ImName) < $r->ImgLastUpdate) imagejpeg($im, $ImName, 90);
		} else {
			@unlink($ImName);
		}
	}
}

function createBackno($TourCodeSafe) {
// 	return;
	global $CFG;
	include('Tournament/BackNumberEmpty.php');
	$RowBn=emptyBackNumber($ID=getIdFromCode($TourCodeSafe));


	$W=$RowBn->BnBgW;
	$H=$RowBn->BnBgH;

	$img=imagecreatetruecolor($W*12, $H*12);
	$ColWhi=imagecolorallocate($img, 255, 255, 255); // bianco
	imagefilledrectangle($img, 0, 0, $W*12, $H*12, $ColWhi);

	$ColBlue=imagecolorallocate($img, 0, 83, 166); // azzurro

	if(file_exists($file=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToLeft.jpg')) {
		$fmt=75/65; // max rectangle is 75 w x 65 h
		$im2=imagecreatefromjpeg($file);
		$w2=imagesx($im2);
		$h2=imagesy($im2);
		if($w2/$h2 > $fmt) {
			// more landscape than available rectangle
			$newW=75*12;
			$newH=$newW*$h2/$w2;
		} else {
			// more portrait than available rectangle
			$newH=65*12;
			$newW=$newH*$w2/$h2;
		}
		imagecopyresampled($img, $im2, 0, 0, 0, 0, $newW, $newH, $w2, $h2);
	}

	if(file_exists($file=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToRight.jpg')) {
		$fmt=($W-51)/34; // max rectangle is 75 w x 65 h
		$im2=imagecreatefromjpeg($file);
		$w2=imagesx($im2);
		$h2=imagesy($im2);
		if($w2/$h2 > $fmt) {
			// more landscape than available rectangle
			$newW=($W-51)*12;
			$newH=$newW*$h2/$w2;
		} else {
			// more portrait than available rectangle
			$newH=34*12;
			$newW=$newH*$w2/$h2;
		}
		imagecopyresampled($img, $im2, 0, ($H*12)-$newH, 0, 0, $newW, $newH, $w2, $h2);
	}

	// text
	putenv('GDFONTPATH=' . dirname(__FILE__).'/tcpdf/fonts');
	if($_SESSION['ISORIS']) {
		if(!defined('PRINTLANG')) define('PRINTLANG', 'en');
	} elseif($_SESSION['TourPrintLang']) {
		if(!defined('PRINTLANG')) define('PRINTLANG', $_SESSION['TourPrintLang']);
	}

	$q=safe_r_sql("select ToWhere, ToName, ToWhenFrom, ToWhenTo from Tournament where ToId={$ID}");
	$font = 'ariblk.ttf';
	$r=safe_fetch($q);

	$text='';
	$date1=explode('-', $r->ToWhenFrom);
	$date2=explode('-', $r->ToWhenTo);
	if($date1[0]!=$date2[0]) {
		// 2 years
		$text=intval($date1[2]).' '.get_text($date1[1].'_Short', 'DateTime').' '.$date1[0] . '/' . intval($date2[2]).' '.get_text($date2[1].'_Short', 'DateTime').' '.$date2[0];
	} elseif($date1[1]!=$date2[1]) {
		// 2 months
		$text=intval($date1[2]).' '.get_text($date1[1].'_Short', 'DateTime') . '/' . intval($date2[2]).' '.get_text($date2[1].'_Short', 'DateTime').' '.$date2[0];
	} elseif($date1[0]!=$date2[0]) {
		// 2 days
		$text=intval($date1[2]) . '/' . intval($date2[2]).' '.get_text($date2[1].'_Short', 'DateTime').' '.$date2[0];
	} else {
		$text=intval($date2[2]).' '.get_text($date2[1].'_Short', 'DateTime').' '.$date2[0];
	}

	$text .= ' - '.preg_replace("#[\n\r]+#", ' - ', $r->ToWhere) . ' - '.preg_replace("#[\n\r]+#", ' - ', $r->ToName);
	$FontSize=50;

	$dim=imagettfbbox($FontSize, 0, $font, $text);
	while($dim[2]>$W*12) {
		$FontSize--;
		$dim=imagettfbbox($FontSize, 0, $font, $text);
	}

	$textX=($W*12 - $dim[2])/2;
	$textY=65*12 + 5 - $dim[7];
	imagettftext($img, $FontSize, 0, $textX, $textY, $ColBlue, $font, $text);
    imagesetthickness($img, 10);
    imageline($img, $textX, $textY + $dim[1]+8, $textX+$dim[2], $textY + $dim[1]+8, $ColBlue);

	imagejpeg($img, $FILE=$CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-BackNo-0.jpg', 90);

	$TmpUpdate = '';
	$RowBn->BnTournament=$_SESSION['TourId'];
	$RowBn->BnFinal=0;
	foreach($RowBn as $Key => $Value) {
		if(substr($Key,0,2)=='Bn') {
			if(is_array($Value)) {
				$Tmp = 0;
				foreach($Value as $SubValue)
					$Tmp += $SubValue;
				$TmpUpdate .= $Key . " = " . StrSafe_DB($Tmp) . ', ';
			} else {
				$TmpUpdate .= $Key . " = " . StrSafe_DB(str_replace('#','',$Value)) . ', ';
			}
		}
	}

	$TmpUpdate.="BnBackground='".addslashes(file_get_contents($FILE))."'";

	safe_w_sql("INSERT INTO BackNumber SET $TmpUpdate on duplicate key update $TmpUpdate");
}

/**
 * Writes on filesystem the specified image
 * @param number $EnId the ID of the athlete
 * @param resource $q [optional] a dataset resource
 * @return boolean true on success, false otherwise.
 */
function updatePhoto($EnId, $q='') {
	global $CFG;
	static $SafeToCode;
	if(!$q) {
		$q=safe_r_sql("select EnId, PhPhoto, ToCode from Photos
				inner join Entries on EnId=PhEnId
				inner join Tournament on EnTournament=ToId
				where PhEnId=$EnId");
	}
	$ret=(safe_num_rows($q) ? true : false);
	while($r=safe_fetch($q)) {
		if(empty($SafeToCode)) $SafeToCode=preg_replace('/[^a-z0-9_.-]/sim', '', $r->ToCode);
		$ImName = $CFG->DOCUMENT_PATH.'TV/Photos/'.$SafeToCode.'-En-'.$r->EnId.'.jpg';
		if($im=@imagecreatefromstring(base64_decode($r->PhPhoto))) {
			@Imagejpeg($im, $ImName, 95);
			$ret=($ret and true);
		} else {
			safe_w_SQL("delete from Photos where PhEnId=$r->EnId");
			@unlink($ImName);
			$ret=false;
		}
	}
	return $ret;
}

/**
 * Writes on filesystem the specified image
 * @param string $FlCode the ID of the athlete
 * @param string $Type the type (can be JPG, SVG or ALL
 * @param resource $q [optional] a dataset resource
 * @return boolean true on success, false otherwise.
 */
function updateFlag($FlCode, $Type='JPG', $q='') {
	global $CFG;
	static $SafeToCode;
	if(!$q) {
		$q=safe_r_sql("select FlCode, FlJPG, FlSVG, ToCode, ToId from Flags
				inner join Tournament on FlTournament=ToId
				where FlCode='$FlCode'");
	}
	$ret=(safe_num_rows($q) ? true : false);
	$Type=strtoupper($Type);

	$Delete=0;

	while($r=safe_fetch($q)) {
		if(empty($SafeToCode)) $SafeToCode=preg_replace('/[^a-z0-9_.-]/sim', '', $r->ToCode);

		if($Type=='JPG' or $Type=='ALL') {
			$ImName = $CFG->DOCUMENT_PATH.'TV/Photos/'.$SafeToCode.'-Fl-'.$r->FlCode.'.jpg';
			if($r->FlJPG and $im=@imagecreatefromstring(base64_decode($r->FlJPG))) {
				@Imagejpeg($im, $ImName, 95);
				$ret=($ret and true);
			} else {
				safe_w_SQL("update Flags set FlJPG='' where FlCode='$r->FlCode' and FlTournament=$r->ToId");
				@unlink($ImName);
				$ret=false;
				$Delete+=safe_w_affected_rows();
			}
		}
		if($Type=='SVG' or $Type=='ALL') {
			$ImName = $CFG->DOCUMENT_PATH.'TV/Photos/'.$SafeToCode.'-FlSvg-'.$r->FlCode.'.svg';
			if($r->FlSVG and $im=gzinflate($r->FlSVG)) {
				$f=fopen($ImName, 'w');
				fwrite($f, $im);
				fclose($f);
				$ret=($ret and true);
			} else {
				safe_w_SQL("update Flags set FlSVG='' where FlCode='$r->FlCode' and FlTournament=$r->ToId");
				@unlink($ImName);
				$ret=false;
				$Delete+=safe_w_affected_rows();
			}
		}
	}
	// deletes the empty records!
	if($Delete) {
		safe_w_sql("delete from Flags where FlSVG='' and FlJPG=''");
	}
	return $ret;
}


function RemoveMedia($TourCode='') {
	global $CFG;
	if(empty($TourCode)) $TourCode=$_SESSION['TourCodeSafe'];
	foreach(glob($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'*') as $file) {
		unlink($file);
	}
}

/**
 * Inserts a picture in the database and redraws the content
 * @param integer $EnId the EnId of the archer
 * @param string $Photo the escaped, base64 encoded stringified image
 * @return boolean true on success, false otherwise
 */
function InsertPhoto($EnId, $Photo, $Booth=false, $Date='', $EnBadgePrinted=0, $ToRetake=false) {
	if(!$Date) $Date=date('Y-m-d H:i:s');
	$ToRetake=intval($ToRetake);
	$sql = "PhEnId=" . Strsafe_DB($EnId) . ", PhPhoto='" . $Photo ."', PhToRetake=$ToRetake";
	safe_w_sql("insert into Photos set $sql, PhPhotoEntered='$Date' on duplicate key update $sql");
	if(safe_w_affected_rows()) {
		safe_w_sql("update Photos set PhPhotoEntered='$Date' where PhEnId=" . Strsafe_DB($EnId));
		if(!$ToRetake) {
			safe_w_sql("update Entries set EnBadgePrinted=".($EnBadgePrinted ? Strsafe_DB($EnBadgePrinted) : 0)." where EnId=".Strsafe_DB($EnId));
		}
	}
	if($Booth) {
		safe_w_sql("insert into ianseo_Accreditation.Photos
				set PhJpegSmall='".$Photo."', PhEnCode='{$Booth->EnCode}', PhIocCode='$Booth->EnIocCode', PhToCode='$Booth->ToCode'
				on duplicate key update PhJpegSmall='".$Photo."'");
	}
	return updatePhoto($EnId);
}