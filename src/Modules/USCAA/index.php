<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

CheckTourSession(true);
$importReport = array();

// Check if a file has been uploaded
if(!empty($_FILES["UploadedFile"]["name"]) and strlen($_FILES["UploadedFile"]["name"]) and $_FILES["UploadedFile"]["error"]==UPLOAD_ERR_OK) {
	$MaxArrows=0;
	$G = '';
	$X = '';

	$Select = "SELECT ToGoldsChars,ToXNineChars,(ToMaxDistScore/ToGolds) AS MaxArrows
		FROM Tournament
		WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1) {
		$MyRow=safe_fetch($Rs);
		$MaxArrows=$MyRow->MaxArrows;
		$G=$MyRow->ToGoldsChars;
		$X=$MyRow->ToXNineChars;
	}

	if (($handle = fopen($_FILES["UploadedFile"]["tmp_name"], "r")) !== FALSE) {
		$row = 0;
		while (($data = fgetcsv($handle)) !== FALSE) {
			if($row++) {
				$athCode = str_pad($data[0],4,'0',STR_PAD_LEFT);
				$athId = 0;
				$Select = "SELECT EnId FROM Entries WHERE EnCode='{$athCode}' AND EnTournament=" . $_SESSION["TourId"];
				$Rs=safe_r_sql($Select);
				if(safe_num_rows($Rs)==1) {
					$MyRow=safe_fetch($Rs);
					$athId = $MyRow->EnId;
					$dist = $data[6];
					$ArrowString = '';
					for($i=11; $i<count($data); $i++) {
						$ArrowString .= GetLetterFromPrint(($data[$i]=='0' ? 'M' : $data[$i]),$athId,$dist);
					}

					list($CurScore,$CurGold,$CurXNine) = ValutaArrowStringGX($ArrowString,$G,$X);
					$Update
						= "UPDATE Qualifications SET "
						. "QuD" . $dist . "ArrowString=" . StrSafe_DB($ArrowString) . ","
						. "QuD" . $dist . "Score=" . StrSafe_DB($CurScore) . ", "
						. "QuD" . $dist . "Gold=" . StrSafe_DB($CurGold) . ", "
						. "QuD" . $dist . "Xnine=" . StrSafe_DB($CurXNine) . ", "
						. "QuD" . $dist . "Hits=" . StrSafe_DB(strlen($ArrowString)) . ", "
						. "QuScore=QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score+QuD8Score,"
						. "QuGold=QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold,"
						. "QuXnine=QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine, "
						. "QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits, "
						. "QuTimestamp=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
						. "WHERE QuId=" . StrSafe_DB($athId);
					$RsUp=safe_w_sql($Update);
					CalcQualRank($dist,'%');
					CalcRank($dist);
				} else {
					$importReport[] = get_text('Error') . " - Line " . $row . " : " . get_text('Athlete') . $athCode;
				}
			}
		}
		fclose($handle);
		// rank totale
		CalcQualRank(0,'%');
		// rank abs totale
		CalcRank(0);
		// squadre
		MakeTeams(NULL, NULL);
		MakeTeamsAbs(NULL,null,null);
	}
	unlink($_FILES["UploadedFile"]["tmp_name"]);
}



$PAGE_TITLE=get_text('MenuLM_GetScoreUSCAA');
include('Common/Templates/head.php');

?>
<form name="Frm" method="POST" action="" enctype="multipart/form-data">
<table class="Tabella2">
	<tr>
		<th class="Title"><?php print get_text('MenuLM_GetScoreUSCAA');?></th>
	</tr>
	<tr>
	<td class="Center" colspan="2"><input name="UploadedFile" type="file" size="30">
	</tr>
	<tr>
		<td class="Center" colspan="2"><input type="submit" value="<?php print get_text('CmdGo','Tournament');?>" id="Vai"></td>
	</tr>

</table>
</form>
<?php
include('Common/Templates/tail.php');
