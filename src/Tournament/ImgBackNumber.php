<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/BackNoPDF.php');
require_once('Common/Fun_FormatText.inc.php');

$TemplateID=0;
if(!empty($_REQUEST['IdTpl'])) {
	$TemplateID=intval($_REQUEST['IdTpl']);
}

if(!CheckTourSession()) {
	// spedisci una immagine vuota
	$img=createimagetruecolor(100,100);
	// Content type
	header('Content-type: image/png');

	imagepng($img);
	die();
}

$Select = "SELECT BackNumber.* "
	. "FROM BackNumber  "
	. "WHERE BnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND BnFinal in (0," . $TemplateID . ") "
	. "ORDER BY BnFinal DESC";

$Rs=safe_r_sql($Select);

if (!($RowBn=safe_fetch($Rs))) {
	include('Tournament/BackNumberEmpty.php');
	$RowBn=emptyBackNumber();
} else {

}

$H=$RowBn->BnHeight;
if($RowBn->BnOffsetX or $RowBn->BnOffsetY) $H = $H/2;

$img=imagecreatetruecolor($RowBn->BnWidth-1, $H-1);

DrawElements(get_text('Target'), get_text('Athlete') , get_text('Country'), 0);

// Content type
header('Content-type: image/png');

imagepng($img);
die();

function DrawElements($TargetNo = ' ', $Name = ' ', $Country = ' ', $Offset = 0)
{
	global $RowBn, $img, $CFG, $TemplateID;

	$ColBlk=imagecolorallocate($img, 192, 192, 192); // black e sfondo
	$ColWhi=imagecolorallocate($img, 255, 255, 255); // bianco

	imagefilledrectangle($img, $RowBn->BnBgX, $RowBn->BnBgY, $RowBn->BnBgW+$RowBn->BnBgX-1, $RowBn->BnBgH+$RowBn->BnBgY-1, $ColWhi);

	//Immagine di Sfondo
	if(file_exists($file=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-BackNo-'.$TemplateID.'.jpg')
			or file_exists($file=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-BackNo-0.jpg')) {
		// inserisci immagine di sfondo
		$sf=imagecreatefromjpeg($file);

		imagecopyresampled ($img , $sf , $RowBn->BnBgX , $RowBn->BnBgY , 0 , 0 , $RowBn->BnBgW , $RowBn->BnBgH , imagesx($sf) , imagesy($sf) );

	} else {
		// inserisci un riquadro bianco
	}

//BackNumber
	if($RowBn->BnTargetNo & 1)
	{
		draw_pip($RowBn->BnTargetNo, $RowBn->BnTnoSize, "99D", $RowBn->BnTnoX , $RowBn->BnTnoY, $RowBn->BnTnoW, $RowBn->BnTnoH, $RowBn->BnTnoColor);
	}

	//Atleta
	if($RowBn->BnAthlete & 1)
	{
		$NameToSend=($RowBn->BnCapitalFirstName?strtoupper($Name):$Name).' '.$Name;
		draw_pip($RowBn->BnAthlete, $RowBn->BnAthSize, $NameToSend, $RowBn->BnAthX , $RowBn->BnAthY, $RowBn->BnAthW, $RowBn->BnAthH, $RowBn->BnAthColor);
	}

	//Societa
	if($RowBn->BnCountry & 1)
	{
		draw_pip($RowBn->BnCountry, $RowBn->BnCoSize, $Country, $RowBn->BnCoX , $RowBn->BnCoY, $RowBn->BnCoW, $RowBn->BnCoH, $RowBn->BnCoColor);
	}

}

function DecodeFont($Font) {
	$ret='';
	// returning family (Arial, Courier, Times)
	switch($Font & 6) {
		case '2': $ret='times'; break;
		case '4': $ret='cour'; break;
		default: $ret='arial';
	}

	// return style (bold, italic, normal)
	if($Font & 8) $ret.='b';
	if($Font & 16) $ret.='i';
	if($Font & 8 and !($Font & 16)) $ret.='d';

	return dirname(dirname(__FILE__))."/Common/tcpdf/fonts/$ret.ttf";
}

function draw_pip($Font, $Size, $Name, $OrgX, $OrgY, $OrgW, $OrgH, $OrgColor) {
	global $img, $ColBlk;
		// Calculate the dimensions of the box containing the text
	$font=DecodeFont($Font);
	$size=$Size*0.35277;
	$pos=imagettfbbox($size, 0, $font, trim($Name));

	$w=$width=$pos[2]-$pos[0];
	$h=$height=$pos[1]-$pos[5];
	$prop=$OrgW/$OrgH;
	if($width/$height < $prop) $width=$height*$prop;

	$txt1=imagecreatetruecolor($width, $height);

	$col0=imagecolorallocate($txt1, 0, 0, 0); // black e sfondo
	$col2=imagecolorallocate($txt1, 255, 255, 255); // white
	imagefilledrectangle($txt1, 0, 0, imagesx($txt1), imagesy($txt1), $col2); // background is white

	$col1=imagecolorallocate($txt1, base_convert(substr($OrgColor,0,2), 16, 10),base_convert(substr($OrgColor,2,2), 16, 10),base_convert(substr($OrgColor,4,2), 16, 10));
	switch($Font & 96) {
		case 96: $x=($width-$w)/2 + abs($pos[0]); break; // centered
		case 64: $x=abs($pos[0]); break; // left
		default: $x=abs($pos[0]) + $width-$w; break; // right
	}

	imagettftext ($txt1, $size, 0, $x, (($height-$h)/2) + abs($pos[5])-1 , $col1, $font, $Name );

	imagecopyresampled ($img, $txt1, $OrgX, $OrgY, 0, 0, $OrgW, $OrgH, $width, $height );
	imagerectangle($img, $OrgX, $OrgY, $OrgX+$OrgW, $OrgY+$OrgH, $ColBlk);
}

?>