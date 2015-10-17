<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Fun_HHT.local.inc.php');
	require_once('Common/Fun_Phases.inc.php');

	CheckTourSession(true);

	$myrow=NULL;

	$select =
		"SELECT HsId, HsIpAddress, HsPort FROM HhtSetup WHERE HsId=". StrSafe_DB($_REQUEST['Id']) . " AND HsTournament=" . StrSafe_DB($_SESSION['TourId']);
	$rsHht=safe_r_sql($select);
	if ($rsHht!=null)
		$myRow=safe_fetch($rsHht);

	$JS_SCRIPT = array(
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_Configuration.js"></script>',
		'<script type="text/javascript">var StrConfirm="' . get_text('MsgAreYouSure') . '";</script>',
		);
	$PAGE_TITLE=get_text('HTTSocket','HTT');

	include('Common/Templates/head.php');
?>
<div align="center">
	<div class="half">
		<form name="myform" action="#">
		<table class="Tabella">
			<tr>
				<th class="Title" colspan="3"><?php print get_text('HTTSocket','HTT'); ?></th>
			</tr>
			<tr>
				<th class="TitleLeft" colspan="2"><?php print get_text('Host','HTT'); ?></th>
				<td style="width:80%;"><?php print $myRow->HsIpAddress; ?><input type="hidden" id="HhtId" value="<?php  echo $myRow->HsId; ?>"></td>
			</tr>
			<tr>
				<th class="TitleLeft" colspan="2"><?php print get_text('Port','HTT'); ?></th>
				<td style="width:80%;"><?php print $myRow->HsPort; ?></td>
			</tr>
			<tr class="Spacer"><td colspan="3"></td></tr>
			<tr>
				<th style="width:15%;"><a class="Link" href="javascript:selectAllEvents();"><?php echo get_text('SelectAll'); ?></a></th>
				<th colspan="2"><?php print get_text('Session'); ?></th>

			</tr>
			<?php
			$EventArray=array();
			$listCodes=array();
			//$Select = "SELECT HeEventCode FROM HhtEvents WHERE HeHhtId=". StrSafe_DB($_REQUEST['Id']) . " AND HeTournament=" . StrSafe_DB($_SESSION['TourId']);
			$Select = "SELECT HeEventCode,HeHhtId FROM HhtEvents WHERE HeTournament=" . StrSafe_DB($_SESSION['TourId']);
			$Rs=safe_r_sql($Select);
			while($MyRow=safe_fetch($Rs))
				//$EventArray[] = $MyRow->HeEventCode;
				$EventArray[$MyRow->HeEventCode] = $MyRow->HeHhtId;

			// Get the Session Names
			$SesNames=array();
			$q=safe_r_sql("select * from Session where Sestype='Q' and SesTournament={$_SESSION['TourId']}");
			while($r=safe_fetch($q)) {
				$SesNames[$r->SesOrder]=($r->SesName ? $r->SesName : get_text('QualSession','HTT') . ' ' . $r->SesOrder);
			}

			$RowTour=RowTour();
			for ($i=1; $i<=$RowTour->ToNumSession; $i++)
			{
				$tmpCode = phaseEncode(-1,$i,1);
				echo '<tr id="row_' . $tmpCode . '">';
				echo '<td class="Center">';
				if(array_key_exists($tmpCode,$EventArray) &&($EventArray[$tmpCode]!=$_REQUEST['Id']))
					echo "&nbsp;";
				else
					echo '<input type="checkbox" name="eventList" value="' . $tmpCode . '" id="chk_' . $tmpCode . '" ' .  (array_key_exists($tmpCode,$EventArray) ? 'checked': '') . ' onClick="saveHhtEvent(\'' . $tmpCode . '\')"></td>';
				echo '</td>';
				echo '<td colspan="2">' . $SesNames[$i] . '</td>';
				echo '</tr>';
				$listCodes[]= StrSafe_DB($tmpCode);
			}

			// Finali
			$Select
				= "SELECT DISTINCT CONCAT(FSScheduledDate,' ',FSScheduledTime) AS MyDate,FSTeamEvent "
				. "FROM FinSchedule "
				. "WHERE FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 "
				. "ORDER BY FSTeamEvent ASC,CONCAT(FSScheduledDate,FSScheduledTime) ASC ";

			$Select='SELECT'
				. ' @Phase:=ifnull(2*pow(2,truncate(log2(fsmatchno/2),0)),1) Phase'
				. ' , @RealPhase:=truncate(@Phase/2, 0) RealPhase'
				. ' , CONCAT(FSScheduledDate,\' \',FSScheduledTime) AS MyDate'
				. ' , DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDBshort') . '") AS Dt '
				. ' , DATE_FORMAT(FSScheduledDate,"' . get_text('DateFmtDB') . '") AS Dat '
				. ' , FSTeamEvent '
				. ' , FSEvent '
				. ' , EvFinalFirstPhase '
				. ' , FSScheduledTime '
				. 'FROM'
				. ' `FinSchedule` fs '
				. " inner join Events on FSEvent=EvCode and FSTeamEvent=EvTeamEvent and FsTournament=EvTournament "
				. 'where'
				. ' FsTournament=' . $_SESSION['TourId']
				. ' and fsscheduleddate >0 '
				. 'group by '
				. ' FsScheduledDate, '
				. ' FsScheduledTime, '
				. ' FsEvent, '
				. ' Phase';
			$tmp=array();
			$Rs=safe_r_sql($Select);

			if (safe_num_rows($Rs)>0)
			{
				while ($MyRow=safe_fetch($Rs))
				{
					if($tmpCode = phaseEncode($MyRow->FSTeamEvent, $MyRow->MyDate, 0)) {
						if(!in_array(StrSafe_DB($tmpCode), $listCodes)) $listCodes[] = StrSafe_DB($tmpCode);
						$tmp[$tmpCode]['events'][get_text(namePhase($MyRow->EvFinalFirstPhase, $MyRow->RealPhase) . '_Phase')][]= $MyRow->FSEvent;
						$tmp[$tmpCode]['date']= $MyRow->Dt . ' '. substr($MyRow->FSScheduledTime,0,5) . ' ' . ($MyRow->FSTeamEvent==0 ? get_text('FinInd','HTT') : get_text('FinTeam','HTT'));
						//$tmp[$tmpCode]['selected']= isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$val ? ' selected' : '';
					}
				}
				foreach($tmp as $k => $v) {
					$val=array();
					foreach($v['events'] as $ph => $ev) $val[]= $ph . ' ('.implode('+',$ev).')';
//					$ComboSes.='<option value="'.$k.'"'.$v['selected'].'>'.$v['date']  . ' '. implode('; ',$val).'</option>';

					echo '<tr id="row_' . $k . '">';
					echo '<td class="Center">';
					if($k && array_key_exists($k, $EventArray) && ($EventArray[$k]!=$_REQUEST['Id']))
						echo "&nbsp;";
					else
						echo '<input type="checkbox" name="eventList" value="' . $k . '" id="chk_' . $k . '" ' .  (array_key_exists($k, $EventArray) ? 'checked': '') . ' onClick="saveHhtEvent(\'' . $k . '\')">';
					echo '</td>';
					echo '<td colspan="2">' . $v['date']  . ' '. implode('; ',$val) . '</td>';
					echo '</tr>' . "\n";
				}
			}
			sort($listCodes);
			$delete
				= "DELETE FROM HhtEvents "
				. "WHERE HeEventCode NOT IN (" . implode(',', $listCodes) . ") AND HeTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$rs=safe_w_sql($delete);

			?>
		</table>
		</form>
		<br>
		<a class="Link" href="Configuration.php"><?php echo get_text('Back') ?></a>
	</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>