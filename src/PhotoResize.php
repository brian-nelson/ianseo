<?php

/**
 * Resizes a picture based on the constants MAX_WIDTH and MAX_HEIGHT
 * @param mixed $file  can be an image file, an upload variable or a string
 * @param boolean $direct  if true, $file is an image file
 * @param boolean $String if true $file is a string image
 * @return the resized image as an escaped base64 encoded string on success
 */
function photoresize($file, $direct=false, $String=false) {
	global $errMsg;
	$errMsg='';
	if($String) {
		// $file is actually a string of the image
		$direct=true;
		$tmpname=tempnam('/tmp', 'snp');
		if($im=imagecreatefromstring($file) and imagejpeg($im, $tmpname, 95)) {
			$file=$tmpname;
		}
	}
	if ($direct or $file['error']==UPLOAD_ERR_OK) {
		// check del tipo: ammessi JPG e PNG
		list($width, $height, $type, $attr) = getimagesize(($direct ? $file : $file["tmp_name"]));
		if ($type!=IMAGETYPE_JPEG && $type!=IMAGETYPE_PNG) {
			$errMsg=get_text('PhotoBadTypeError','Tournament');
		} else {
			$im='';
			if($type==IMAGETYPE_JPEG) {
				$im=imagecreatefromjpeg(($direct ? $file : $file["tmp_name"]));
			} elseif($type==IMAGETYPE_PNG) {
				$im=imagecreatefrompng(($direct ? $file : $file["tmp_name"]));
			}

			if(!$im) {
				$errMsg=get_text('PhotoBadTypeError','Tournament');
			} else {
				if ($width!=MAX_WIDTH || $height!=MAX_HEIGHT ) {
					// we have to crop or resize image
					$ratio=min($width/MAX_WIDTH, $height/MAX_HEIGHT);

					// we resize the image to fit the greater value
					$new_width=$width/$ratio;
					$new_height=$height/$ratio;
					$im2=imagecreatetruecolor( MAX_WIDTH, MAX_HEIGHT);
					$bgcolor=imagecolorallocate($im2, 255, 255, 255);
					imagefill($im2, 0,0,$bgcolor);

					if(!imagecopyresampled( $im2, $im, (MAX_WIDTH-$new_width)/2, (MAX_HEIGHT-$new_height)/2,0,0, $new_width, $new_height, $width, $height)) {
						$errMsg=get_text('PhotoDimError','Tournament',array(MAX_WIDTH,MAX_HEIGHT));
					} else {
						// check now the proportions
						$im = $im2;
					}
				}

				if (!$errMsg) {
					$savedImage=tempnam('/tmp', 'snp');
					imagejpeg($im, $savedImage, 70);
					$ResizedImage = addslashes(base64_encode(file_get_contents($savedImage)));
					unlink($savedImage);
					return $ResizedImage;
				}
			}
		}
	}
	elseif ($file['error']!=UPLOAD_ERR_NO_FILE)
	{
		$errMsg=get_text('PhotoUpError','Tournament');
	}

}
?>