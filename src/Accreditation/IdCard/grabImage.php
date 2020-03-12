<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    checkACL(AclAccreditation, AclReadWrite, false);
/*

:sout=#transcode{vcodec=mjpg,fps=5,width=800,height=600}:standard{access=http,mux=mpjpeg,dst=0.0.0.0:8050/stream.mjpg}
:no-sout-audio
:input-repeat=10000

 */
$x=(empty($_REQUEST['x']) ? -1 : $_REQUEST['x']);
$y=(empty($_REQUEST['y']) ? -1 : $_REQUEST['y']);
$w=(empty($_REQUEST['w']) ? 300 : $_REQUEST['w']);
$h=$w*(4/3);

$athId=(empty($_REQUEST['AthId']) || $_REQUEST['AthId']==0 ? null : $_REQUEST['AthId']);

//$camurl=(empty($_REQUEST['url']) ? "http://localhost:8050/stream.mjpg" : urldecode($_REQUEST['url']));
//$camurl="http://192.168.0.249/jpg/image.jpg";
$camurl=(empty($_REQUEST['CamUrl']) ? "" : urldecode($_REQUEST['CamUrl']));
if(!$camurl) $camurl=$_COOKIE['CamUrl'];

$boundary="\n--";

$f = @fopen($camurl,"r") ;

if(!$camurl or !$f) {

	//**** cannot open
	$im=imagecreatetruecolor(640,480);
	$col=imagecolorallocate($im,128,128,128);

	imagefilledrectangle($im, 0, 0, 640, 480, $col);
	header("Content-type: image/png");
	imagepng($im);
	imagedestroy($im);

} else {
	$r="";
	$im = null;
	if(preg_match('/\.jpg$/i',$camurl) != 0) {
		while(!feof($f))
			$r .= fread($f,4096);
		$im=imagecreatefromstring($r);
	} else {
		while (substr_count($r,"Content-Length") != 2)
			$r.=fread($f,512);
		$start = strpos($r,chr(255));
		$end   = strpos($r,$boundary,$start)-1;

		$frame = substr("$r",$start,$end - $start);
		$im=imagecreatefromstring($frame);
	}

	if($x<0) $x=(imagesx($im)-$w-2)/2;
	if($y<0) $y=(imagesy($im)-$h)-2;


	if($x+$w > imagesx($im)) $x=(imagesx($im)-$w)-2;
	if($y+$h > imagesy($im)) $y=(imagesy($im)-$h)-2;

	$lineColor=imagecolorallocate($im,255,255,0);
	imagesetthickness($im,3);

	if(!empty($_REQUEST['get']) && $_REQUEST['get']==1 && !IsBlocked(BIT_BLOCK_ACCREDITATION)) {
		setcookie("getPhotoX",$x,time()+24*60);
		setcookie("getPhotoY",$y,time()+24*60);
		setcookie("getPhotoW",$w,time()+24*60);
		$tmpname=tempnam('/tmp', 'enphoto');
		$Booth='';
		if($_SESSION['AccBooth']) {
			// pictures will be recorded in a Database!
			$q=safe_r_sql("select EnCode, EnIocCode, ToCode from Entries inner join Tournament on EnTournament=ToId where EnId={$athId}");
			$Booth=safe_fetch($q);
		}

		if($athId and $img=photoresize($tmpname, true) and InsertPhoto($athId, $img, $Booth)) {
			$lineColor=imagecolorallocate($im,0,0,255);
			imagesetthickness($im,10);
		}
	}

	imagerectangle($im,$x,$y,$x+$w,$y+$h,$lineColor);

	header("Content-type: image/png");
	imagepng($im);
	imagedestroy($im);
	fclose($f);
}
