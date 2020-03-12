<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

CheckTourSession(true);

$edit='';
if(!empty($_GET['edit'])) $edit=$_GET['edit'];
if(!empty($_GET['delete'])) {
	safe_w_SQL("delete from Flags where FlCode=".StrSafe_DB($_GET['delete'])." and FlTournament={$_SESSION['TourId']}");
	@unlink( $CFG->DOCUMENT_PATH . 'TV/Photos/' . $_SESSION['TourCodeSafe'] . '-Fl-' . $_GET['delete'] . '.jpg') ;
	@unlink( $CFG->DOCUMENT_PATH . 'TV/Photos/' . $_SESSION['TourCodeSafe'] . '-FlSvg-' . $_GET['delete'] . '.svg') ;
	CD_redirect($_SERVER['PHP_SELF'].go_get('delete', '', true));
}

if(isset($_REQUEST['Export'])) {
	$q=safe_r_sql("select distinct FlCode, FlJPG
		from Flags 
		where FlTournament in (-1, {$_SESSION['TourId']})
			and FlJPG!='' ");
	if(!safe_num_rows($q)) {
		cd_redirect('./Countries.php');
	}

	$zip = new ZipArchive();
	$filename = $tmpfname = tempnam("/tmp", $_SESSION['TourCodeSafe']);

	if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
		exit("cannot open <$filename>\n");
	}

	while($r=safe_fetch($q)) {
		if(!($img=imagecreatefromstring(base64_decode($r->FlJPG)))) {
			continue; // skip and goes to the next picture
		}
		$img2=imagecreatetruecolor(90, 60);
		$BgCol=imagecolorallocate($img2, 255, 255, 255);
		imagefill($img2, 0, 0, $BgCol);
		$ratio=imagesx($img)/imagesy($img);

		$DestX=0;
		$DestY=0;
		$DestW=90;
		$DestH=60;

		if($ratio<1.5) {
			// image is too squarish
			$DestX=intval((90-(60*$ratio))/2);
			$DestW=60*$ratio;
		} elseif($ratio>1.5) {
			// image is too long
			$DestY=intval((60-(90/$ratio))/2);
			$DestH=90/$ratio;
		}

		imagecopyresampled($img2, $img, $DestX, $DestY, 0, 0, $DestW, $DestH, imagesx($img), imagesy($img));
		ob_start();
		imagepng($img2);
		$stringdata = ob_get_contents(); // read from buffer
		ob_end_clean(); // delete buffer

		$zip->addFromString($r->FlCode.'.png', $stringdata);
	}

	$zip->close();

	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename="'.$_SESSION['TourCodeSafe'].'.zip"');
	readfile($filename);
	unlink($filename);
}

if($_FILES) {
	if(!empty($_FILES['SVG']['name'][$edit]) and $_FILES['SVG']['type'][$edit] == 'image/svg+xml') {
		$img=tempnam('/tmp', 'flag');
		$imgSVG=$img.'.svg';
		$imgJPG=$img.'.jpg';
		copy($_FILES['SVG']['tmp_name'][$edit], $imgSVG);

		//exec("inkscape -e $imgPNG -w 200 -z $imgSVG ");
		// Imagick does not work with ALL the images :(
		exec("convert -render $img.svg -scale 200x200 -quality 95 $img.jpg");

		$ImageSVG=addslashes(gzdeflate(file_get_contents($imgSVG), 9));
		$ImageJPG='';
		if(file_exists($imgJPG)) {
			$ImageJPG=addslashes(base64_encode(file_get_contents($imgJPG)));
		}

		// inserts SVG graphic and PNG ONLY if it has been updated
		safe_w_sql("insert into Flags set"
			. " FlCode='$edit',"
			. " FlSVG='$ImageSVG',"
			. (!empty($_POST['UpdateJPG']) && $ImageJPG?" FlJPG='$ImageJPG',":'')
			. " FlTournament={$_SESSION['TourId']} "
			. " on duplicate key update"
			. " FlSVG='$ImageSVG'"
			. (!empty($_POST['UpdateJPG']) && $ImageJPG?", FlJPG='$ImageJPG'":'')
			);
	}

	if(!empty($_FILES['JPG']['name'][$edit])) {
		$imgJPG=$_FILES['JPG']['tmp_name'][$edit].'.jpg';
		switch($_FILES['JPG']['type'][$edit]) {
			case 'image/png':
				$img=imagecreatefrompng($_FILES['JPG']['tmp_name'][$edit]);
				if($img) imagejpeg($img, $imgJPG, 95);
				break;
			case 'image/gif':
				$img=imagecreatefromgif($_FILES['JPG']['tmp_name'][$edit]);
				if($img) imagejpeg($img, $imgJPG, 95);
				break;
			case 'image/jpg':
			case 'image/jpeg':
				$imgJPG=$_FILES['JPG']['tmp_name'][$edit];
				break;
			default:
				cd_redirect('Countries.php#'.$edit);
		}
		$Image=base64_encode(file_get_contents($imgJPG));
		$ImageEsc=StrSafe_DB($Image);
		safe_w_sql("insert into Flags set FlCode='$edit', FlJPG=$ImageEsc, FlTournament={$_SESSION['TourId']} "
			. " on duplicate key update FlJPG=$ImageEsc");
		$ImName = $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$edit.'.jpg';
		if($im=@imagecreatefromstring(base64_decode($Image))) {
			Imagejpeg($im, $ImName,95);
		}
		unlink($imgJPG);
	}

	cd_redirect('Countries.php#'.$edit);
}

require_once('Common/CheckPictures.php');
CheckPictures();

$PAGE_TITLE=get_text('TourCountries', 'Tournament');

include('Common/Templates/head.php');

echo '<form method="POST" ENCTYPE="multipart/form-data">';
echo '<table class="Tabella">';
echo '<tr>';
echo '<td colspan="5">';
echo '<a href="../Final/Team/PrnName.php?TeamLabel=1" target="_blank" class="Button">'.get_text('TeamPlace', 'Tournament').'</a>';
echo '<a href="../Final/Team/PrnName.php?TeamLabel=1&local=1" target="_blank" class="Button">'.get_text('TeamPlaceLocal', 'Tournament').'</a>';
echo '<a href="?Export" class="Button">'.get_text('CmdExport', 'Tournament').'</a>';
echo '</td>';
echo '</tr>';
echo '<tr>';
echo '<th class="Title" width="15%" nowrap="nowrap">'.get_text('Country').'</th>';
echo '<th class="Title">'.get_text('Nation').'</th>';
echo '<th class="Title">'.get_text('SVGFile', 'Tournament').'</th>';
echo '<th class="Title">'.get_text('Image', 'Tournament').'</th>';
echo '<th class="Title">'.get_text('CmdDelete', 'Tournament').'</th>';
echo '</tr>';

$q=safe_r_sql("select distinct "
	. " CoCode, "
	. " CoName, "
	. " fl.* "
	. "from"
	. " Countries"
	. " INNER JOIN Entries on CoId in (EnCountry, EnCountry2, EnCountry3)"
	. " left join (select distinct FlCode, FlJPG, FlSVG, FlTournament from Flags where FlTournament in (-1, {$_SESSION['TourId']}) order by FlCode, FlTournament desc) fl on FlCode=CoCode "
	. "where"
	. " CoTournament = {$_SESSION['TourId']}"
	. ($edit?" and CoCode='$edit' ":" and CoCode>'' ")
	. "order by"
	. " FlSVG>'', FlJPG>'', CoCode, FlTournament desc");

$OldCode='';
while($r=safe_fetch($q)) {
	if($OldCode==$r->CoCode) continue;
	if($edit) {
		echo '<tr>';
		echo '<td>'.$r->CoCode.'</td>';
		echo '<td>'.$r->CoName.'</td>';
		echo '<td>'.get_text($r->FlSVG?'Yes':'No').' <input type="file" name="SVG['.$r->CoCode.']" size="5"> <input type="checkbox" name="UpdateJPG" checked="checked">' . get_text('UpdateJPG', 'Tournament').' <a href="http://en.wikipedia.org/wiki/File:Flag_of_'.$r->CoName.'.svg" target="_blank">Wikipedia</a></td>';
		echo '<td>'.($r->FlJPG?'<img height="30" src="' . $CFG->ROOT_DIR . 'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$r->FlCode . '.jpg">':'&nbsp;').' <input type="file" name="JPG['.$r->CoCode.']" size="5"></td>';
		echo '<td>&nbsp;</td>';
		echo '</tr>';
	} else {
		echo '<tr>';
		echo '<td><a name="'.$r->CoCode.'" href="?edit='.$r->CoCode.'">'.$r->CoCode.'</a></td>';
		echo '<td><a name="'.$r->CoCode.'" href="?edit='.$r->CoCode.'">'.$r->CoName.'</a></td>';
		echo '<td>'.get_text($r->FlSVG ? 'Yes' : 'No').'</td>';
		if($r->FlJPG) {
			$size=getimagesize($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$r->FlCode . '.jpg');
			$Ratio=round($size[0]/$size[1],2);
			echo '<td><img height="30" src="' . $CFG->ROOT_DIR . 'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$r->FlCode . '.jpg">&nbsp;'.$Ratio.'</td>';
		} else {
			echo '<td>&nbsp;</td>';
		}
		if($r->FlSVG or $r->FlJPG) {
			echo '<td><img src="' . $CFG->ROOT_DIR . 'Common/Images/drop.png" onclick="location.href=\'?delete='.$r->FlCode.'\'"></td>';
		} else {
			echo '<td>&nbsp;</td>';
		}
		echo '</tr>';
	}

	$OldCode=$r->CoCode;
}

echo '<tr>';
echo '<td colspan="5" align="Center"><input type="submit" value="'.get_text('CmdUpdate').'"></td>';
echo '</tr>';

echo '</table>';
echo '</form>';

include('Common/Templates/tail.php');

?>