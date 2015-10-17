<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (CheckTourSession() && isset($_REQUEST["Type"]) && preg_match("/^[RLB2]{1,2}$/i",$_REQUEST["Type"])==true)
	{

		$Sql = "SELECT ToImg" . $_REQUEST["Type"] . " as ToImg FROM Tournament WHERE ToId = " . StrSafe_DB($_SESSION['TourId']);
		$Rs  = safe_r_sql($Sql);
		$myrow = safe_fetch($Rs);
		$myContent = $myrow->ToImg;

		if(!empty($_REQUEST["SaveFile"])) {
			header('Content-Disposition: attachment; filename=Logo-' . $_REQUEST["Type"]);
			header('Content-Transfer-Encoding: escaped-8bit');
			header('Content-Length: ' . strlen($myContent));
			print $myContent;


		}


		$im = imagecreatefromstring($myContent);
		if ($im !== false) {
			$width = imagesx($im);
			$height = imagesy($im);
			$scala = 1;
//Ridimensiono se troppo larga
			if(isset($_REQUEST["W"]) && is_numeric($_REQUEST["W"]) && $width>$_REQUEST["W"])
				$scala = $_REQUEST["W"] / $width;
//Ridimensiono se troppo alta
			if(isset($_REQUEST["H"]) && is_numeric($_REQUEST["H"]) && $height>$_REQUEST["H"])
				$scala = (($_REQUEST["H"]/$height) < $scala ? ($_REQUEST["H"]/$height) : $scala);
//Faccio il ridimensionamento
			if($scala<1)
			{
				$new_width = $width * $scala;
				$new_height = $height * $scala;
				$new_image = imagecreatetruecolor($new_width, $new_height);
				imagecopyresampled($new_image, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				$im=$new_image;
			}

		    header('Content-Type: image/png');
		    imagepng($im);
			imagedestroy($im);
		}
	}

?>