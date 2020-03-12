<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclAccreditation, AclReadWrite);
	$dir=$CFG->DOCUMENT_PATH . 'Accreditation/IdCard/Photo/';

	$query
		= "SELECT "
			. "EnCode, EnDivision, EnClass, EnFirstName, EnName, PhPhoto "
		. "FROM "
			. "Entries "
			. "INNER JOIN "
				. "Photos "
			. "ON EnId=PhEnId "
		. "WHERE "
			. "EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY "
			. "EnCode ASC ";
		//	print $query;
	$rs=safe_r_sql($query);
	while ($myRow=safe_fetch($rs)) {
		$im=imagecreatefromstring(base64_decode($myRow->PhPhoto));
		imagejpeg($im, $dir . $myRow->EnDivision . $myRow->EnClass . "_" . $myRow->EnFirstName . "_" . $myRow->EnName . ".jpg" , 90);
		echo $myRow->EnDivision . $myRow->EnClass . "_" . $myRow->EnFirstName . "_" . $myRow->EnName . ".jpg<br>";
		imagejpeg($im, $dir . $myRow->EnCode . ".jpg" , 90);
		echo $myRow->EnCode . ".jpg<br>";
		imagedestroy($im);
	}

