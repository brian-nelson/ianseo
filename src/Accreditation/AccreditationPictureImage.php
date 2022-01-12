<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_Modules.php');

if(file_exists($paramFile=dirname(dirname(__FILE__))."/Modules/Accreditation/includeAccreditationPicture.php")) {
	require_once($paramFile);
}

if (!CheckTourSession()) {
	print get_text('CrackError');
	exit;
}
checkACL(AclAccreditation, AclReadWrite,false);

$Errore = 1;
$Answer='';
$AthId = 0;
$image=null;
if(!empty($_REQUEST["Id"]) && $EnId=intval($_REQUEST["Id"])) {
	if(!empty($_REQUEST["picDelete"])) {
		safe_w_sql("DELETE FROM Photos WHERE PhEnId=$EnId");
		if($_SESSION['AccBooth']) {
			$q=safe_r_sql("select EnCode, EnIocCode, ToCode from Entries inner join Tournament on EnTournament=ToId where EnId={$EnId}");
			if($Booth=safe_fetch($q)) {
				safe_w_sql("delete from ianseo_Accreditation.Photos where PhEnCode='{$Booth->EnCode}' and PhIocCode='$Booth->EnIocCode' and PhToCode='$Booth->ToCode'");
				// this need to be logged as it will NOT be passed with the transfer of the photos!
				LogAccBoothQuerry("delete from Photos Where PhEnId=(select EnId from Entries where EnCode='{$Booth->EnCode}' and EnIocCode='$Booth->EnIocCode' and EnTournament=§TOCODETOID§", $Booth->ToCode);
				LogAccBoothQuerry("update Entries set EnBadgePrinted=0 where EnCode='{$Booth->EnCode}' and EnIocCode='$Booth->EnIocCode' and EnTournament=§TOCODETOID§", $Booth->ToCode);
			}
		}
	}

	// gets the tourcode and TourId from the EnId (because of multiple codes!)
	$q=safe_r_sql("select ToId, ToCode, EnBadgePrinted, (EnBadgePrinted+0 and PhEnId IS NULL) or PhToRetake=1 as NoPrintout 
		from Tournament 
		inner join Entries on EnTournament=ToId and EnId=$EnId
		LEFT JOIN Photos ON PhEnId=EnId");
	$r=safe_fetch($q);

	$TourCode=$r->ToCode;
	$TourId=$r->ToId;
	$NoPrintout=$r->NoPrintout;
	$DatePrinted=$r->EnBadgePrinted;

	$im=false;
	if(!empty($_REQUEST["picEncoded"])) {
		$picEncoded=str_replace(array('data:image/png;base64,',' '),array('','+'),$_REQUEST["picEncoded"]);
		$im = imagecreatefromstring(base64_decode($picEncoded));
	}
	if(!empty($_REQUEST["picJpgEncoded"])) {
		$JSON=array('error' => 1, 'pic' => '');
		$picEncoded=str_replace(array('data:image/jpeg;base64,',' '),array('','+'),$_REQUEST["picJpgEncoded"]);
		$im = imagecreatefromstring(base64_decode($picEncoded));
	}
	if(!empty($_REQUEST["picURL"])) {
		$im = imagecreatefromjpeg($_REQUEST["picURL"]);
	}
	if($im !== false) {
		$im2 = imagecreatetruecolor(MAX_WIDTH, MAX_HEIGHT);
		if($im !== false) {
			//Save Source

			// if set as save pictures but folder not writeable... blocking error!
			$Continue=true;
			if($_SESSION['AccreditationTourIds'] or getModuleParameter('AccPics','EnableSave',0)==1) {
				$Continue=false;
				$folder = getModuleParameter('AccPics','FolderSave',"");
				if($folder and is_dir($folder) and is_writable($folder)) {
					$MainDir=$folder.$TourCode.'/';
					$hasDir=true;
					if(!is_dir($MainDir)) {
						$hasDir = (mkdir($MainDir, 0777, true) and chmod($MainDir, 0777));
					}
					if($hasDir and imagejpeg($im, $MainDir.$TourCode."_".$EnId.".jpg", 98)) {
						$q=safe_r_sql("select EnCode, EnDivision from Entries where EnId={$EnId}");
						if($r=safe_fetch($q) and $r->EnCode) {
							$hasDir=true;
							$CodeDir=$MainDir.'Encodes/'.($r->EnDivision ? $r->EnDivision.'/' : '');
							if(!is_dir($CodeDir)) {
								$hasDir = (mkdir($CodeDir, 0777, true) and chmod($CodeDir, 0777));
							}
							if($hasDir and imagejpeg($im, $CodeDir.$TourCode."_".$r->EnCode.".jpg", 98)) {
								$Continue=true;
							}
						}
					}
				}
			}

			$Booth='';
			if($_SESSION['AccBooth']) {
				// pictures will be recorded in a Database!
				ob_start();
				imagejpeg($im, null, 98);
				$imageString = ob_get_clean();
				$q=safe_r_sql("select EnCode, EnIocCode, ToCode from Entries inner join Tournament on EnTournament=ToId where EnId={$EnId}");
				if($Booth=safe_fetch($q)) {
					$SQL="PhEnCode='{$Booth->EnCode}', PhIocCode='$Booth->EnIocCode', PhToCode='$Booth->ToCode', PhJpeg=".StrSafe_DB($imageString);
					safe_w_sql("insert into ianseo_Accreditation.Photos set $SQL on duplicate key update $SQL");
					$Continue=true;
				}
			}

			if($Continue) {
				$w=imagesx($im);
				$h=imagesy($im);

				$ratio= min($w/MAX_WIDTH, $h/MAX_HEIGHT)*(empty($param["frame"]) ? 1 : $param["frame"]/100);
				$srcW = MAX_WIDTH * $ratio;
				$srcH = MAX_HEIGHT * $ratio;

				$srcY = intval(($h-$srcH)/2);
				$srcX = intval(($w-$srcW)/2);

				$alfa = imagecopyresampled($im2, $im, 0, 0, $srcX, $srcY, MAX_WIDTH, MAX_HEIGHT, $srcW, $srcH);
				$w=imagesx($im2);
				$h=imagesy($im2);


				ob_start();
				imagepng($im2);
				$image_data = ob_get_contents();
				ob_end_clean ();
				require_once('Common/CheckPictures.php');
				require_once('Common/PhotoResize.php');
				if($imgtoSave=photoresize($image_data, true, true)) {
					if($NoPrintout) {
						InsertPhoto($EnId, $imgtoSave, $Booth, $DatePrinted, $DatePrinted);
					} else {
						InsertPhoto($EnId, $imgtoSave, $Booth);
					}
				}
			}
		}
	}

	$Sql = "SELECT EnId, EnCode, CONCAT(EnDivision, '-',EnClass) as Category, CONCAT(CoName, ' (' ,CoCode,')') as Country, CONCAT(UPPER(EnFirstName),' ' ,EnName) as Athlete, PhPhoto as Photo "
		. "FROM Entries "
		. "LEFT JOIN Countries ON EnCountry=CoId "
		. "LEFT JOIN Photos ON EnId=PhEnId "
		. "WHERE EnTournament=$TourId AND EnId=$EnId";
	$Rs=safe_r_sql($Sql);
	if($row = safe_fetch($Rs)) {
		$Errore=intval($row->Photo=='' and (!empty($_REQUEST["picEncoded"]) or !empty($_REQUEST["picURL"])));
		$Answer .= '<athlete id="' . $row->EnId . '" ath="' . htmlspecialchars($row->Athlete) . '" team="' . htmlspecialchars($row->Country) . '" cat="' . $row->Category . '">'
			. '<id>' . $row->EnId . '</id>'
			. '<ath><![CDATA[' . $row->Athlete . ']]></ath>'
			. '<team><![CDATA[' . $row->Country . ']]></team>'
			. '<cat><![CDATA[' . $row->Category . ']]></cat>'
			. '<pic><![CDATA[' . ($row->Photo ? "data:image/jpeg;base64,".$row->Photo:'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==') . ']]></pic>'
			. '</athlete>';

		if(!empty($JSON)) {
			$JSON['error']=$Errore;
			$JSON['pic']=($row->Photo ? "data:image/jpeg;base64,".$row->Photo : 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==');
			$JSON['ok']=($row->Photo ? 1 : 0);
			JsonOut($JSON);
		}
	}
}

header('Content-Type: text/xml');
echo '<response>';
echo '<error>' . $Errore . '</error>';
echo $Answer;
echo '</response>';

?>