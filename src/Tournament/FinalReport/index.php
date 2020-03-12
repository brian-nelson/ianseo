<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    checkACL(AclCompetition, AclReadWrite);

	if (!IsBlocked(BIT_BLOCK_REPORT))
	{
		foreach($_REQUEST AS $Key => $Value)
		{
			if(preg_match("/^[Rep_]/",$Key))
			{
				$Tmp = "";
				if(is_array($Value))
				{
					foreach($Value as $subValue)
						$Tmp .= (strlen($Tmp)>0 ? "|" : "") . stripslashes($subValue);
				}
				else
				{
					$Tmp = stripslashes($Value);
				}

				$Sql = "REPLACE INTO FinalReportA (FraQuestion, FraTournament, FraAnswer) "
					. "VALUES(" . StrSafe_DB(substr($Key,4)) . ', ' . StrSafe_DB($_SESSION['TourId']) . ', ' . StrSafe_DB($Tmp) . ")";
				safe_w_sql($Sql);
			}
		}
	}

	$PAGE_TITLE=get_text('FinalReportTitle','Tournament');

	include('Common/Templates/head.php');

?>
<form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
<table class="Tabella">
<tr><th class="Title" colspan="3"><?php print get_text('FinalReportTitle','Tournament'); ?></th></tr>

<?php
/*
 * Type: 0->Text Box, 1->Text Area, 2->Yes/No, 3->List, 4->Check Box
 */
	/*$MySql = "SELECT FrqId, FrqStatus, FrqQuestion, FrqTip, FrqType, FrqOptions, FraAnswer "
		. "FROM FinalReportQ "
		. "INNER JOIN Tournament ON ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "INNER JOIN Tournament*Type ON TtId=ToType "
		. "LEFT JOIN FinalReportA ON FrqId=FraQuestion AND FraTournament=ToId "
		. "WHERE (FrqStatus & TtCategory) > 0 "
		. "ORDER BY FrqId";*/
	$MySql = "SELECT FrqId, FrqStatus, FrqQuestion, FrqTip, FrqType, FrqOptions, FraAnswer "
		. "FROM FinalReportQ "
		. "INNER JOIN Tournament ON ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "LEFT JOIN FinalReportA ON FrqId=FraQuestion AND FraTournament=ToId "
		. "WHERE (FrqStatus & ToCategory) > 0 "
		. "ORDER BY FrqId";
	$Rs=safe_r_sql($MySql);

	if(safe_num_rows($Rs)>0)
	{
		while($MyRow = safe_fetch($Rs))
		{
			echo '<tr>'. "\n\t";
			if($MyRow->FrqType==-1)
			{
				echo '<tr class="Divider"><td colspan="3"></td></tr>' . "\n";
				echo '<tr>';
				echo '<th class="Title" colspan="3">' . $MyRow->FrqId . ' - ' . $MyRow->FrqQuestion . '</th>';
				echo '</tr>';
			}
			else
			{
				echo '<td width="5%" class="Caption">' . $MyRow->FrqId . "</td>\n\t";
				echo '<td width="35%" class="Medium Bold"><span title="' . $MyRow->FrqTip . '">' . $MyRow->FrqQuestion . "</span></td>\n\t";
				echo '<td width="60%">';
				switch($MyRow->FrqType)
				{
					case 0:
						$Tmp = ' maxlen="200" size="70" ';
						if(preg_match("/^[0-9]+$/i",$MyRow->FrqOptions))
							$Tmp = ' maxlen="' . $MyRow->FrqOptions . '" size="'. $MyRow->FrqOptions . '" ';
						echo '<input type="text" name="Rep_'. $MyRow->FrqId .'" id="' . $MyRow->FrqId . '"'. $Tmp . (strlen($MyRow->FraAnswer)>0 ? ' value="' . $MyRow->FraAnswer . '"' : '') .' title="' . $MyRow->FrqQuestion . (!empty($MyRow->FrqTip) ? ": " . $MyRow->FrqTip : "") . '">';
						break;
					case 1:
						$Tmp = ' cols="70" rows="5" ';
						$ResArray = array();
						if(preg_match("/^([0-9]+)\|([0-9]+)$/i", $MyRow->FrqOptions, $ResArray))
							$Tmp = ' cols="' . $ResArray[1] . '" rows="'. $ResArray[2] . '" ';
						echo '<textarea name="Rep_'. $MyRow->FrqId .'" id="' . $MyRow->FrqId . '"'. $Tmp . ' title="' . $MyRow->FrqQuestion . (!empty($MyRow->FrqTip) ? ": " . $MyRow->FrqTip : "") . '">' . (strlen($MyRow->FraAnswer)>0 ?  $MyRow->FraAnswer: '') . '</textarea>';
						break;
					case 2:
						echo '<select name="Rep_'. $MyRow->FrqId .'" id="' . $MyRow->FrqId . '" title="' . $MyRow->FrqQuestion . (!empty($MyRow->FrqTip) ? ": " . $MyRow->FrqTip : "") . '">';
						echo '<option value="-">---</option>';
						echo '<option value="0"' . ($MyRow->FraAnswer=='0' || (strlen($MyRow->FraAnswer)==0 && $MyRow->FrqOptions=='0') ? ' selected' : '') . '>' . get_text('No') . '</option>';
						echo '<option value="1"' . ($MyRow->FraAnswer=='1' || (strlen($MyRow->FraAnswer)==0 && $MyRow->FrqOptions=='1') ? ' selected' : '') . '>' . get_text('Yes') . '</option>';
						echo '</select>';
						break;
					case 3:
						$Tmp = explode("|",$MyRow->FrqOptions);
						if(count($Tmp)>0)
						{
							echo '<select name="Rep_'. $MyRow->FrqId .'" id="' . $MyRow->FrqId . '"' . ' title="' . $MyRow->FrqQuestion . (!empty($MyRow->FrqTip) ? ": " . $MyRow->FrqTip : "") . '">';
							echo '<option value="-">---</option>';
							foreach($Tmp as $Value)
								echo '<option value="' . $Value . '"' . ($MyRow->FraAnswer==$Value ? ' selected' : '') . '>' . $Value . '</option>';
							echo '</select>';
						}
						break;
					case 4:
						$Tmp = explode("|",$MyRow->FrqOptions);
						if(count($Tmp)>0)
						{
							echo '<select name="Rep_'. $MyRow->FrqId .'[]" id="' . $MyRow->FrqId . '" multiple="multiple" title="' . $MyRow->FrqQuestion . (!empty($MyRow->FrqTip) ? ": " . $MyRow->FrqTip : "") . '">';
							foreach($Tmp as $Value)
								echo '<option value="' . $Value . '"' . (strpos($MyRow->FraAnswer,$Value)!==false ? ' selected' : '') . '>' . $Value . '</option>';
							echo '</select>';
						}
						break;
				}
				echo "</td>\n\t";
			}
			echo '</tr>'. "\n";

		}

		echo '<tr>';
			echo '<td colspan="3" class="Center">';
				echo '<input type="submit" value="' . get_text('CmdSave') . '" />&nbsp;&nbsp;';
				echo '<input type="button" value="' . get_text('Print', 'Tournament') . '" onclick="window.open(\'PDFReport.php\',\'PrintOut\');">';
			echo '</td>';
		echo '</tr>' . "\n";
		//echo '<tr><td colspan="3" class="Center"><a href="PDFReport.php" target="_blank">' . get_text('Print', 'Tournament') . '</a></td></tr>';
		safe_free_result($Rs);
	}


?>

</table>
</form>
<?php
	include('Common/Templates/tail.php');
?>