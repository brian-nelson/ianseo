<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession())
	{
		exit;
	}

	$id=isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

/*
 * $mode
 * -- max: ridimensiona usando come riferimento la dimensione più grande
 * -- x  : ridimensiona usando come riferimento la larghezza
 * -- y  : ridimensiona usando come riferimento l'altezza
 *
 * $altnophoto: fornisce un riquadro bianco con croce rossa
 * -- $w e $h sono le sue dimensioni, se fornita una sola il rapporto è 3:4 se nessuna quadrato 50x50
 */
	$mode=isset($_REQUEST['mode']) ? $_REQUEST['mode'] : null;
	$val=isset($_REQUEST['val']) ? $_REQUEST['val']: null;
	$altnophoto=!empty($_REQUEST['altnophoto']);

	if (is_null($id))
		exit;

	$query = "SELECT PhPhoto FROM Photos WHERE PhEnId=" . StrSafe_DB($id);
	//print $query;Exit;
	$rs=safe_r_sql($query);

	$im=null;

	if (safe_num_rows($rs)==1)
	{
		$r=safe_fetch($rs);
		$image=$r->PhPhoto;

	// immagine così com'è nel db
		$im=imagecreatefromstring(base64_decode($image));
		//$im=imagecreatefromstring(($image));

		if (!is_null($mode) && !is_null($val) && is_numeric($val) && $val>0)
		{

			$oldW=imagesx($im);
			$oldH=imagesy($im);

			$newW=0;
			$newH=0;

			switch ($mode)
			{
				case 'max':
					if ($oldW>=$oldH)
					{
						$newW=$val;
						$newH=$newW*($oldH/$oldW);
					}
					else
					{
						$newH=$val;
						$newW=$newH*($oldW/$oldH);
					}
					break;

				case 'x':
					$newW=$val;
					$newH=$newW*($oldH/$oldW);
					break;

				case 'y':
				//	print 'qui';exit;
					$newH=$val;
					$newW=$newH*($oldW/$oldH);
					break;
			}

			$newImage=imagecreatetruecolor($newW,$newH);
			imagecopyresampled($newImage,$im,0,0,0,0,$newW,$newH,$oldW,$oldH);
			$im=$newImage;
		}
	}
	elseif($altnophoto)
	{
		$w=0;
		$h=0;
		if(!empty($_REQUEST['w'])) $w=intval($_REQUEST['w']);
		if(!empty($_REQUEST['h'])) $w=intval($_REQUEST['h']);
		if($w+$h==0) {
			$h=50;
			$w=50;
		} elseif($w) {
			$h=intval($w*4/3);
		} else {
			$h=intval($h*3/4);
		}
		$im=imagecreatetruecolor($w,$h);
		$w--;
		$h--;
		$sfondo=imagecolorallocate($im,255,255,255); // sfondo bianco
		$bordo =imagecolorallocate($im,196,196,196); // bordo grigino
		$croce =imagecolorallocate($im,196,0,0); // rosso scuro
		imagefilledrectangle($im, 0,0,$w,$h,$sfondo);
		imagesetthickness($im, 5);
		imageline($im,0,0,$w,$h,$croce);
		imageline($im,0,$h,$w,0,$croce);
		imagesetthickness($im, 1);
		imagerectangle($im, 0,0,$w,$h,$bordo);

	}
	else
	{
		$im=imagecreatefromjpeg($CFG->DOCUMENT_PATH . 'Common/Images/nophoto.jpeg');
	}



	header('Content-Type: image/png');
    imagepng($im);
	imagedestroy($im);
	imagedestroy($newImage);
?>