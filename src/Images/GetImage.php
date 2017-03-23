<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

$Types=array(
	'rot',
	'flag',
);
$Type='';
if(!empty($_GET['type']) and in_array($_GET['type'], $Types)) $Type = $_GET['type'];

$ID=0;
if(!empty($_GET['id'])) $ID = intval($_GET['id']);

$tour=0;
if(!empty($_GET['tour'])) $tour=intval($_GET['tour']);

$CoCode='';
if(!empty($_GET['country'])) $CoCode=intval($_GET['country']);

$im='';

switch($Type) {
	case 'rot':
		if($ID and $tour) {

			$q=safe_r_sql("select * from TVContents where TVCId=$ID and TVCTournament=$tour");
			if($r=safe_fetch($q)) $im=imagecreatefromstring($r->TVCContent);
		}
		break;
}

if(!$im) {
	// sends a blank picture
	$im=imagecreate(250,20);
	$background_color = imagecolorallocate($im, 255, 255, 255);
	$text_color = imagecolorallocate($im, 233, 14, 91);
	imagestring($im, 1, 5, 5,  "No Image", $text_color);
}

header('Content-type: image/png');
imagepng($im);

?>