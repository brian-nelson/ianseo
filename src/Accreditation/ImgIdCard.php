<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/BackNoPDF.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('IdCardEmpty.php');
require_once('Common/Lib/Fun_DateTime.inc.php');

if(!CheckTourSession()) {
	// spedisci una immagine vuota
	$img=createimagetruecolor(100,100);
	// Content type
	header('Content-type: image/png');

	imagepng($img);
	die();
}

$CardType=(empty($_REQUEST['CardType']) ? 'A' : $_REQUEST['CardType']);
$CardNumber=(empty($_REQUEST['CardNumber']) ? 0 : intval($_REQUEST['CardNumber']));

$Select
	= "SELECT IdCards.*, LENGTH(IcBackground) as ImgSize "
	. "FROM IdCards  "
	. "WHERE IcTournament={$_SESSION['TourId']} and IcType='{$CardType}' and IcNumber={$CardNumber}";
$Rs=safe_r_sql($Select);

$RowBn=emptyIdCard($OrgRow=safe_fetch($Rs));


$img=imagecreatetruecolor($RowBn->Settings["Width"]*2, $RowBn->Settings["Height"]*2);
$ColWhi=imagecolorallocate($img, 255, 255, 255); // bianco
$ColBlk=imagecolorallocate($img, 192, 192, 192); // black e sfondo
imagefilledrectangle($img, 0, 0, $RowBn->Settings["Width"]*2-1, $RowBn->Settings["Height"]*2-1, $ColWhi);

//Immagine di Sfondo
if(strlen( $RowBn->Background) > 0) {
	// inserisci immagine di sfondo
	$sf=imagecreatefromstring($RowBn->Background);
	imagecopyresampled ($img , $sf , $RowBn->Settings["IdBgX"]*2 , $RowBn->Settings["IdBgY"]*2 , 0 , 0 , $RowBn->Settings["IdBgW"]*2 , $RowBn->Settings["IdBgH"]*2 , imagesx($sf) , imagesy($sf) );
} else {
	// filetto grigino di contorno
	imagerectangle($img, 0, 0, $RowBn->Settings["Width"]*2-1, $RowBn->Settings["Height"]*2-1, $ColBlk);
}


$q=safe_r_sql("(select * from IdCardElements where IceTournament={$_SESSION['TourId']} and IceCardType='{$CardType}' and IceCardNumber={$CardNumber} and IceType!='RandomImage' order by IceOrder)
	union
	(select * from IdCardElements where IceTournament={$_SESSION['TourId']} and IceCardType='{$CardType}' and IceCardNumber={$CardNumber} and IceType='RandomImage' order by rand() limit 1)");

while($r=safe_fetch($q)) {
	draw_pip($r);
}

// Content type
header('Content-type: image/png');

imagepng($img);
die();

function draw_pip($r) {
	global $img, $ColBlk, $CFG;
	static $Fonts=array('arial','times','cour');

	$Options=unserialize($r->IceOptions);
	$CardFile="{$r->IceCardType}-{$r->IceCardNumber}-{$r->IceOrder}";

 	//error_reporting(E_ALL);

	switch($r->IceType) {
		case 'ToLeft':
		case 'ToRight':
		case 'ToBottom':
			$im=$CFG->DOCUMENT_PATH."TV/Photos/{$_SESSION['TourCodeSafe']}-{$r->IceType}.jpg";
		case 'Picture':
			if(!isset($im)) $im=$CFG->DOCUMENT_PATH."Common/Images/Photo.gif";
		case 'RandomImage':
			if(!isset($im)) {
				$im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-RandomImage-'.$CardFile.'.jpg';
			}
		case 'TgtSequence':
			if(!isset($im)) {
				$im=$CFG->DOCUMENT_PATH.'Common/Images/TgtSequence'.($r->IceContent=='Coloured' ? 'Col' : 'BW').'.png';
			}
		case 'Image':
			if(!isset($im)) {
				$im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Image-'.$CardFile.'.jpg';
			}

			if(file_exists($im)) {
				if(strtolower(substr($im, -4)) =='.jpg') {
					$im2=imagecreatefromjpeg($im);
				} elseif(strtolower(substr($im, -4)) =='.png') {
					$im2=imagecreatefrompng($im);
				} else {
					$im2=imagecreatefromgif($im);
				}
				if($Options['H'] or $Options['W']) {
					$h=$Options['H']*2;
					$w=$Options['W']*2;
					if(!$h) {
						$h=$w*imagesy($im2)/imagesx($im2);
					} elseif(!$w) {
						$w=$h*imagesx($im2)/imagesy($im2);
					}
					imagecopyresampled($img, $im2, $Options['X']*2, $Options['Y']*2, 0, 0, $w, $h, imagesx($im2), imagesy($im2));
				}
			}
			break;
		case 'ImageSvg':
			if(class_exists('Imagick') and file_exists($im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ImageSvg-'.$CardFile.'.svg')) {
				;
				$tmpname=tempnam($CFG->DOCUMENT_PATH.'TV/Photos/', 'tmp');
				$im2=new Imagick();
				$im2->setBackgroundColor(new ImagickPixel('transparent'));
				$im2->readImage($im);
				$im2->setImageFormat("png32");
				$im2->writeImage($tmpname);/*(or .jpg)*/
				$im2->clear();
				$im2->destroy();

				$im2=imagecreatefrompng($tmpname);
				if($Options['H'] or $Options['W']) {
					$h=$Options['H']*2;
					$w=$Options['W']*2;
					if(!$h) {
						$h=$w*imagesy($im2)/imagesx($im2);
					} elseif(!$w) {
						$w=$h*imagesx($im2)/imagesy($im2);
					}
					imagecopyresampled($img, $im2, $Options['X']*2, $Options['Y']*2, 0, 0, $w, $h, imagesx($im2), imagesy($im2));
				}
				unlink($tmpname);
			}
			break;
		case 'ColoredArea':
			$Text=trim($r->IceContent);
		case 'CompName':
			if(!isset($Text)) $Text=$_SESSION['TourName'];
		case 'CompDetails':
			if(!isset($Text)) $Text=$_SESSION['TourWhere'].' - '.TournamentDate2StringShort($_SESSION['TourRealWhenFrom'], $_SESSION['TourRealWhenTo']);
		case 'Athlete':
			if(!isset($Text)) {
				switch($r->IceContent) {
					case 'FamCaps': $Text=get_text('FamCaps', 'BackNumbers'); break;
					case 'FamCaps-GAlone': $Text=get_text('FamCaps-GAlone', 'BackNumbers'); break;
					case 'FamCaps-GivCamel': $Text=get_text('FamCaps-GivCamel', 'BackNumbers'); break;
					case 'FamCaps-GivCaps': $Text=get_text('FamCaps-GivCaps', 'BackNumbers'); break;
					case 'FamCamel': $Text=get_text('FamCamel', 'BackNumbers'); break;
					case 'FamCamel-GAlone': $Text=get_text('FamCamel-GAlone', 'BackNumbers'); break;
					case 'FamCamel-GivCamel': $Text=get_text('FamCamel-GivCamel', 'BackNumbers'); break;
					case 'GivCamel':    $Text=get_text('GivCamel',    'BackNumbers'); break;
					case 'GivCamel-FamCamel': $Text=get_text('GivCamel-FamCamel', 'BackNumbers'); break;
					case 'GivCamel-FamCaps': $Text=get_text('GivCamel-FamCaps', 'BackNumbers'); break;
					case 'GivCaps': $Text=get_text('GivCaps', 'BackNumbers'); break;
					case 'GivCaps-FamCaps': $Text=get_text('GivCaps-FamCaps', 'BackNumbers'); break;
					case 'GAlone-FamCaps':   $Text=get_text('GAlone-FamCaps',   'BackNumbers'); break;
					case 'GAlone-FamCamel':   $Text=get_text('GAlone-FamCamel',   'BackNumbers'); break;
				}
			}
		case 'Club':
			if(!isset($Text)) {
				switch($r->IceContent) {
					case 'NocCaps-ClubCamel':$Text=get_text('NocCaps-ClubCamel','BackNumbers'); break;
					case 'NocCaps-ClubCaps':$Text=get_text('NocCaps-ClubCaps','BackNumbers'); break;
					case 'NocCaps':$Text=get_text('NocCaps','BackNumbers'); break;
					case 'ClubCamel':$Text=get_text('ClubCamel','BackNumbers'); break;
					case 'ClubCaps':$Text=get_text('ClubCaps','BackNumbers'); break;
				}
			}
		case 'AthCode':
			if(!isset($Text)) $Text='test123';
		case 'TeamComponents':
			if(!isset($Text)) {
				switch($r->IceContent) {
					case 'OneLine':$Text=get_text('FamCaps-GivCamel', 'BackNumbers')." - ".get_text('FamCaps-GivCamel', 'BackNumbers')." - ".get_text('FamCaps-GivCamel', 'BackNumbers'); break;
					case 'MultiLine':$Text=get_text('FamCaps-GivCamel', 'BackNumbers')."\n".get_text('FamCaps-GivCamel', 'BackNumbers')."\n".get_text('FamCaps-GivCamel', 'BackNumbers'); break;
				}
			}
		case 'Category':
			if(!isset($Text)) {
				switch($r->IceContent) {
					case 'CatCode':$Text='CODE'; break;
					case 'CatCode-EvDescr':$Text='CODE-Category'; break;
					case 'CatDescr':$Text='Category'; break;
				}
			}
		case 'Session':
			if(!isset($Text)) $Text=get_text('Session');
		case 'Ranking':
			if(!isset($Text)) $Text=get_text('Rank');
		case 'Event':
			if(!isset($Text)) {
				switch($r->IceContent) {
					case 'EvCode':$Text=get_text('EvCode','BackNumbers'); break;
					case 'EvCode-EvDescr':$Text=get_text('EvCode-EvDescr','BackNumbers'); break;
					case 'EvDescr':$Text=get_text('EvDescr','BackNumbers'); break;
				}
			}
		case 'Target':
			if(!isset($Text)) $Text=get_text('Target');
		case 'SessionTarget':
			if(!isset($Text)) $Text=get_text('SessionTarget','BackNumbers');
		case 'Access':
			if(!isset($Text)) $Text='0/9*';

			if(isset($Text)) {
				// Calculate the dimensions of the box containing the text
				$font=dirname(dirname(__FILE__))."/Common/tcpdf/fonts/{$Options['Font']}.ttf";
				$size=$Options['Size']*0.35278*2;
				$pos=imagettfbbox($size, 0, $font, $Text);
				$width=$pos[4]-$pos[0];
				$height=$pos[1]-$pos[5];

				$y=($Options['H']*2-$height)/2;
				switch($Options['Just']) {
					case 1: $x=($Options['W']*2-$width)/2; break; // centered
					case 2: $x=$Options['W']*2-$width; break; // left
					default: $x=0; break; // right
				}

				$txt1=imagecreatetruecolor($Options['W']*2, $Options['H']*2);
				$Back=imagecolorallocate($txt1, 250, 250, 250); // background
				imagefill($txt1, 0, 0, $Back);
				if($Options['Col']) {
					$color=imagecolorallocate($txt1, hexdec(substr($Options['Col'], 1, 2)), hexdec(substr($Options['Col'], 3, 2)), hexdec(substr($Options['Col'], 5, 2)));
				} else {
					$color=imagecolorallocate($txt1, 0, 0, 0); // black
				}
				if($Options['BackCol']) {
					$colb=imagecolorallocate($txt1, hexdec(substr($Options['BackCol'], 1, 2)), hexdec(substr($Options['BackCol'], 3, 2)), hexdec(substr($Options['BackCol'], 5, 2)));
					imagefill($txt1, 0, 0, $colb);
				} elseif(!empty($Options['BackCat'])) {
					for($i=0; $i<imagesx($txt1); $i+=10) {
						for($j=0; $j<imagesy($txt1); $j+=5) {
							$Offset=($j%2==0);
							imagefilledrectangle($txt1, $i+5*$Offset, $j, $i+4+5*$Offset, $j+4, $ColBlk);
						}
					}
				} else {
					imagecolortransparent($txt1, $Back);
				}

				imagettftext ($txt1, $size, 0, $x, $y+$size, $color, $font, $Text);
				imagecopymerge ($img, $txt1, $Options['X']*2, $Options['Y']*2, 0, 0, $Options['W']*2, $Options['H']*2, 100);
				imagerectangle($img, $Options['X']*2, $Options['Y']*2, $Options['X']*2+$Options['W']*2-1, $Options['Y']*2+$Options['H']*2-1, $ColBlk);
			} else {
				if($Options['BackCol']) {
					$color=imagecolorallocate($img, hexdec(substr($Options['BackCol'], 1, 2)), hexdec(substr($Options['BackCol'], 3, 2)), hexdec(substr($Options['BackCol'], 5, 2)));
					imagefilledrectangle($img, $Options['X']*2, $Options['Y']*2, $Options['X']*2+$Options['W']*2-1, $Options['Y']*2+$Options['H']*2-1, $color);
				}
			}
			break;
		case 'AthBarCode':
			$txt1=imagecreatefrompng($CFG->DOCUMENT_PATH.'Common/Images/edit-barcode.png');
		case 'AthQrCode':
			if(!isset($txt1)) $txt1=imagecreatefromjpeg($CFG->DOCUMENT_PATH.'Common/Images/qrcode.jpg');
		case 'Flag':
			if(!isset($txt1)) $txt1=imagecreatefromjpeg($CFG->DOCUMENT_PATH.'Common/Images/Flag.jpg');
		case 'AccessGraphics':
			if(!isset($txt1)) $txt1=imagecreatefrompng($CFG->DOCUMENT_PATH.'Common/Images/AccessCodes.png');
		case 'Accomodation':
			if(!isset($txt1)) $txt1=imagecreatefrompng($CFG->DOCUMENT_PATH.'Common/Images/Accomodations.png');
			imagecopyresampled($img, $txt1, $Options['X']*2, $Options['Y']*2, 0, 0, $Options['W']*2, $Options['H']*2, imagesx($txt1), imagesy($txt1));
			break;
		case 'HLine':
			$color=imagecolorallocate($img, hexdec(substr($Options['Col'], 1, 2)), hexdec(substr($Options['Col'], 3, 2)), hexdec(substr($Options['Col'], 5, 2)));
			imagefilledrectangle($img, $Options['X']*2, $Options['Y']*2, $Options['X']*2+$Options['W']*2, $Options['Y']*2+$Options['H']*2, $color);
			break;
	}

	return;
	//
	// Calculate the dimensions of the box containing the text
	$font=dirname(dirname(__FILE__))."/Common/tcpdf/fonts/"
		.$Fonts[$RowBn->Settings[$Field.'_Font']]
		.($RowBn->Settings[$Field.'_Bold'] ? ($RowBn->Settings[$Field.'_Italic'] ? 'b' : 'bd') : '')
		.($RowBn->Settings[$Field.'_Italic'] ? 'i' : '')
		.".ttf";
	$size=$RowBn->Settings[$Field.'_Size']*0.35278;
	$pos=imagettfbbox($size, 0, $font, trim($Name));

	$width=$pos[2]-$pos[0];
	$height=$pos[1]-$pos[5];

	$txt1=imagecreatetruecolor($RowBn->Settings[$Field.'_W'], $RowBn->Settings[$Field.'_H']);

	$col0=imagecolorallocate($txt1, 0, 0, 0); // black e sfondo
	$col2=imagecolorallocate($txt1, 255, 255, 255); // white
	imagefilledrectangle($txt1, 0, 0, imagesx($txt1), imagesy($txt1), $col2); // background is white

	$col1=imagecolorallocate($txt1, base_convert(substr($RowBn->Settings[$Field.'_Col'],1,2), 16, 10),base_convert(substr($RowBn->Settings[$Field.'_Col'],3,2), 16, 10),base_convert(substr($RowBn->Settings[$Field.'_Col'],5,2), 16, 10));
	switch($RowBn->Settings[$Field.'_Just']) {
		case 1: $x=($RowBn->Settings[$Field.'_W']-$width)/2; break; // centered
		case 2: $x=$RowBn->Settings[$Field.'_W']-$width; break; // left
		default: $x=0; break; // right
	}

	imagettftext ($txt1, $size, 0, $x, $RowBn->Settings[$Field.'_H']-2, $col1, $font, $Name );

	imagecopy ($img, $txt1, $RowBn->Settings[$Field.'_X'], $RowBn->Settings[$Field.'_Y'], 0, 0, $RowBn->Settings[$Field.'_W'], $RowBn->Settings[$Field.'_H'] );
	imagerectangle($img, $RowBn->Settings[$Field.'_X'], $RowBn->Settings[$Field.'_Y'], $RowBn->Settings[$Field.'_X']+$RowBn->Settings[$Field.'_W']-1, $RowBn->Settings[$Field.'_Y']+$RowBn->Settings[$Field.'_H']-1, $ColBlk);
}

?>