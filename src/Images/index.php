<?php

require_once('../config.php');

$q=safe_r_sql("select * from Flags where FlIocCode='FITA'");
while($r=safe_fetch($q)) {
	file_put_contents($r->FlCode.'.jpg', base64_decode($r->FlJPG));
	file_put_contents($r->FlCode.'.svg', gzinflate($r->FlSVG));
}

die('done');


	// flags in SVG
	$q=safe_w_sql("insert"
		. " ignore into Images (ImTournament, ImIocCode, ImSection, ImReference, ImType, ImContent, ImgLastUpdate, ImChecked) "
		. "select"
		. " FlTournament,"
		. " FlIocCode,"
		. " 'FLAGS',"
		. " FlCode,"
		. " 'svg',"
		. " FlSVG,"
		. " FlEntered,"
		. " FlChecked "
		. "from Flags");

	// Flags in PNG
	$q=safe_w_sql("insert"
		. " ignore into Images (ImTournament, ImIocCode, ImSection, ImReference, ImType, ImContent, ImgLastUpdate, ImChecked) "
		. "select"
		. " FlTournament,"
		. " FlIocCode,"
		. " 'FLAGS',"
		. " FlCode,"
		. " 'png',"
		. " FlJPG,"
		. " FlEntered,"
		. " FlChecked "
		. "from Flags");

	// Entries pictures
	$q=safe_w_sql("insert"
		. " ignore into Images (ImTournament, ImIocCode, ImSection, ImReference, ImType, ImContent, ImgLastUpdate, ImChecked) "
		. "select"
		. " EnTournament,"
		. " EnIocCode,"
		. " 'PHOTO',"
		. " EnCode,"
		. " 'png',"
		. " PhPhoto,"
		. " PhPhotoEntered,"
		. " '' "
		. "from Entries inner join Photos on EnId=PhEnId where EnIocCode>''");

	// Tournament Top Left image
	$q=safe_w_sql("insert"
		. " ignore into Images (ImTournament, ImSection, ImReference, ImType, ImContent, ImgLastUpdate, ImChecked) "
		. "select"
		. " ToId,"
		. " 'TOUR',"
		. " 'TOP LEFT',"
		. " 'png',"
		. " ToImgL,"
		. " ToWhenFrom,"
		. " '' "
		. "from Tournament where ToImgL>''");

	// Tournament Top Right image
	$q=safe_w_sql("insert"
		. " ignore into Images (ImTournament, ImSection, ImReference, ImType, ImContent, ImgLastUpdate, ImChecked) "
		. "select"
		. " ToId,"
		. " 'TOUR',"
		. " 'TOP RIGHT',"
		. " 'png',"
		. " ToImgR,"
		. " ToWhenFrom,"
		. " '' "
		. "from Tournament where ToImgR>''");

	// Tournament Bottom image
		$q=safe_w_sql("insert"
		. " ignore into Images (ImTournament, ImSection, ImReference, ImType, ImContent, ImgLastUpdate, ImChecked) "
		. "select"
		. " ToId,"
		. " 'TOUR',"
		. " 'BOTTOM',"
		. " 'png',"
		. " ToImgB,"
		. " ToWhenFrom,"
		. " '' "
		. "from Tournament where ToImgB>''");

	// Tournament Bottom 2 image
		$q=safe_w_sql("insert"
		. " ignore into Images (ImTournament, ImSection, ImReference, ImType, ImContent, ImgLastUpdate, ImChecked) "
		. "select"
		. " ToId,"
		. " 'TOUR',"
		. " 'BOTTOM 2',"
		. " 'png',"
		. " ToImgB2,"
		. " ToWhenFrom,"
		. " '' "
		. "from Tournament where ToImgB2>''");

	// Backnumber image
		$q=safe_w_sql("insert"
		. " ignore into Images (ImTournament, ImSection, ImReference, ImType, ImContent, ImgLastUpdate, ImChecked) "
		. "select"
		. " BnTournament,"
		. " 'BACK',"
		. " BnFinal,"
		. " 'png',"
		. " BnBackground,"
		. " now(),"
		. " '' "
		. "from BackNumber where BnBackground>''");


?>