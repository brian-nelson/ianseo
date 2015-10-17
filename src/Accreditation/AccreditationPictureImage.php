<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (!CheckTourSession()) {
	print get_text('CrackError');
	exit;
}

$Errore=0;
$Answer='';
$AthId = 0;
$image=null;
if(!empty($_REQUEST["Id"]) && $EnId=intval($_REQUEST["Id"])) {
	if(!empty($_REQUEST["picDelete"])) {
		safe_w_sql("DELETE FROM Photos WHERE PhEnId=$EnId");
	}
	if(!empty($_REQUEST["picEncoded"])) {
		$picEncoded=str_replace(array('data:image/png;base64,',' '),array('','+'),$_REQUEST["picEncoded"]);
		$im = new Imagick();
		$im->setFormat('jpg');
		if($im->readImageBlob(base64_decode($picEncoded))) {
			$w=$im->getImageWidth();
			$h=$im->getimageheight();
// 			echo $w . "." . $h;exit;
			if($w!=MAX_WIDTH or $h!=MAX_HEIGHT) {
				// resize image
				$im->scaleimage($w/$h<(MAX_WIDTH/MAX_HEIGHT) ? MAX_WIDTH : 0, $w/$h<(MAX_WIDTH/MAX_HEIGHT) ? 0 : MAX_HEIGHT);
				$w=$im->getImageWidth();
				$h=$im->getimageheight();
				$im->cropimage(MAX_WIDTH, MAX_HEIGHT, ($w-MAX_WIDTH)/2, ($h-MAX_HEIGHT)/2);
			}
			$imgtoSave=StrSafe_DB(base64_encode($im->getImageBlob()));
			safe_w_sql("insert into Photos set PhEnId=$EnId, PhPhoto={$imgtoSave} on duplicate key update PhPhoto={$imgtoSave}");
			require_once('Common/CheckPictures.php');
			updatePhoto($EnId);
		}
	}

	$Sql = "SELECT EnId, CONCAT(EnDivision, '-',EnClass) as Category, CONCAT(CoName, ' (' ,CoCode,')') as Country, CONCAT(UPPER(EnFirstName),' ' ,EnName) as Athlete, PhPhoto as Photo "
		. "FROM Entries "
		. "LEFT JOIN Countries ON EnCountry=CoId "
		. "LEFT JOIN Photos ON EnId=PhEnId "
		. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnId=" . StrSafe_DB($_REQUEST['Id']);
	$Rs=safe_r_sql($Sql);
	if(safe_num_rows($Rs)) {
		$row = safe_fetch($Rs);
		$Answer .= '<athlete>'
			. '<id>' . $row->EnId . '</id>'
			. '<ath><![CDATA[' . $row->Athlete . ']]></ath>'
			. '<team><![CDATA[' . $row->Country . ']]></team>'
			. '<cat><![CDATA[' . $row->Category . ']]></cat>'
			. '<pic><![CDATA[' . ($row->Photo ? "data:image/jpeg;base64,".$row->Photo:'') . ']]></pic>'
			. '</athlete>';
	}

} else {
	$Errore = 1;
}

header('Content-Type: text/xml');
echo '<response>';
echo '<error>' . $Errore . '</error>';
echo $Answer;
echo '</response>';

?>