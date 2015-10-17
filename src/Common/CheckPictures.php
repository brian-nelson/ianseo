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
		$Images=glob($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-*.jpg');
		if($Images) {
			$OnlyNewer = filemtime($Images[0]); // create filter
			// speeds the process for the next time the function is called, as it will skip this file :)
			touch($Images[0]);
		}
		$Images=glob($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-*.jpg');
		if($Images) {
			$OnlyNewer = max($OnlyNewer,filemtime($Images[0])); // create filter
			// speeds the process for the next time the function is called, as it will skip this file :)
			touch($Images[0]);
		}
	}

	if($all) {
		// updates the multimedia content as well...
		$q=safe_r_sql("select * from TVContents where TVCMimeType in ('image/gif','image/jpeg','image/png') and TVCTournament in (-1, $TourId)");
		while($r=safe_fetch($q)) {
			$im=imagecreatefromstring($r->TVCContent);
			imagejpeg($im, $CFG->DOCUMENT_PATH.'TV/Photos/TV-'.($r->TVCTournament==-1?'BaseIanseo':$TourCodeSafe).'-'.($r->TVCName=='IdCardFooter' ? $r->TVCName : $r->TVCId).'.jpg', 90);
		}

		// AND the Tour images as well...
		$q=safe_r_sql("select * from Tournament where ToId = $TourId");
		if($r=safe_fetch($q)) {
			@unlink($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'ToLeft.jpg');
			@unlink($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'ToRight.jpg');
			@unlink($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'ToBottom.jpg');
			if($r->ToImgL and $im=@imagecreatefromstring($r->ToImgL)) imagejpeg($im, $CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToLeft.jpg', 90);
			if($r->ToImgR and $im=@imagecreatefromstring($r->ToImgR)) imagejpeg($im, $CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToRight.jpg', 90);
			if($r->ToImgB and $im=@imagecreatefromstring($r->ToImgB)) imagejpeg($im, $CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToBottom.jpg', 90);
		}

		// AND the Tour Backnumbers as well...
		$q=safe_r_sql("select BnFinal, BnBackground from BackNumber where BnTournament = $TourId");
		if(!safe_num_rows($q)) {
			if(!$all)
				createBackno($TourCodeSafe);
		} else {
			while($r=safe_fetch($q)) {
				if($r->BnBackground and $im=@imagecreatefromstring($r->BnBackground)) imagejpeg($im, $CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-BackNo-'.$r->BnFinal.'.jpg', 90);
			}
		}

		// and the accreditation pictures too...
		$q=safe_r_sql("select IcBackground from IdCards where IcTournament = $TourId");
		if($r=safe_fetch($q)) {
			if($r->IcBackground and $im=@imagecreatefromstring($r->IcBackground)) imagejpeg($im, $CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Accreditation.jpg', 90);
		}
		$q=safe_r_sql("select IceContent, IceOrder from IdCardElements where IceTournament = $TourId and IceType='Image'");
		while($r=safe_fetch($q)) {
			if($r->IceContent and $im=@imagecreatefromstring($r->IceContent)) imagejpeg($im, $CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Image-'.$r->IceOrder.'.jpg', 90);
		}

		// and the Images too...
		$q=safe_r_sql("select * from Images where ImTournament = $TourId");
		while($r=safe_fetch($q)) {
			if($r->ImContent and $im=@imagecreatefromstring($r->ImContent)) {
				imagejpeg($im, $CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe
					.'-'.$r->ImIocCode
					.'-'.$r->ImSection
					.'-'.$r->ImReference
					.'-'.$r->ImType.'.jpg', 90);
			}
		}

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
			. " 'Fl-svg', "
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
		$Sql = "(select"
			. " 'En' PictureType, "
			. " PhEnId PictureCode,"
			. " PhPhoto Picture,"
			. " unix_timestamp(PhPhotoEntered) PictureTime "
			. "from"
			. " Photos"
			. " left join Entries on EnId=PhEnId "
			. "where"
//			. " EnAthlete='1' and "
			. " EnTournament=$TourId "
			. ($OnlyNewer ? " and unix_timestamp(PhPhotoEntered)>$OnlyNewer " : '')
			. " and PhPhoto>'') "
			. "UNION "
			. "(select"
			. " 'Fl', "
			. " FlCode,"
			. " FlJPG,"
			. " unix_timestamp(FlEntered) UnixTime "
			. "from"
			. " Flags "
			. " inner join Countries on FlCode=CoCode and CoTournament=$TourId "
			. "where"
			. " FlTournament in (-1, $TourId)"
			. ($OnlyNewer ? " and unix_timestamp(FlEntered)>$OnlyNewer ":'')
			. " and FlJPG>'') "
			. "UNION "
			. "(select"
			. " 'Fl-svg', "
			. " FlCode,"
			. " FlSVG,"
			. " unix_timestamp(FlEntered) UnixTime "
			. "from"
			. " Flags "
			. " inner join Countries on FlCode=CoCode and CoTournament=$TourId "
			. "where"
			. " FlTournament in (-1, $TourId)"
			. ($OnlyNewer ? " and unix_timestamp(FlEntered)>$OnlyNewer ":'')
			. " and FlSVG>'') "
			;
	}

	$q=safe_r_sql($Sql);
	while($r=safe_fetch($q)) {
		if($r->PictureType=='Fl-svg') {
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
				} elseif($r->PictureType=='En') {
					safe_w_SQL("delete from Photos where PhEnId=$r->PictureCode");
				}
			}
		}
	}
}

function createBackno($TourCodeSafe) {
// 	return;
	global $CFG;
	include('Tournament/BackNumberEmpty.php');
	$RowBn=emptyBackNumber();


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
	if($_SESSION['ISORIS']) define('PRINTLANG', 'en');
	elseif($_SESSION['TourPrintLang']) define('PRINTLANG', $_SESSION['TourPrintLang']);

	$q=safe_r_sql("select ToWhere, ToName, ToWhenFrom, ToWhenTo from Tournament where ToId={$_SESSION['TourId']}");
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

function updatePhoto($EnId) {
	global $CFG;
	$q=safe_r_sql("select PhPhoto, ToCode from Photos
			inner join Entries on EnId=PhEnId
			inner join Tournament on EnTournament=ToId
			where PhEnId=$EnId");
	if($r=safe_fetch($q)) {
		$ImName = $CFG->DOCUMENT_PATH.'TV/Photos/'.preg_replace('/[^a-z0-9_.-]/sim', '', $r->ToCode).'-En-'.$EnId.'.jpg';
		if($im=@imagecreatefromstring(base64_decode($r->PhPhoto))) {
			Imagejpeg($im, $ImName, 95);
		}
	}
}