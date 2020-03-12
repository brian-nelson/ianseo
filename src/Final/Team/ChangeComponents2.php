<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');

	CheckTourSession(true);
    checkACL(AclTeams, AclReadWrite);

	$ev=isset($_REQUEST['ev']) ? $_REQUEST['ev'] : null;

	if (is_null($ev)) {
		header('Location: ChangeComponents1.php');
		exit;
	}

	$command=isset($_REQUEST['command']) ? $_REQUEST['command'] : null;

	if (!is_null($command))
	{
		if ($command=='Set' && !IsBlocked(BIT_BLOCK_TEAM))
		{
			$team=isset($_REQUEST['team']) ? $_REQUEST['team'] : null;
			$subTeam=isset($_REQUEST['subTeam']) ? $_REQUEST['subTeam'] : null;
			$oldId=isset($_REQUEST['oldId']) ? $_REQUEST['oldId'] : null;
			$newId=isset($_REQUEST['newId']) ? $_REQUEST['newId'] : null;

			//print $team . ' ' . $subTeam . ' ' . $oldId . ' ' . $newId;exit;

			if (!is_null($team) && !is_null($subTeam) && !is_null($oldId) && !is_null($newId))
			{
				/*$query
					= "UPDATE "
						. "TeamFinComponent "
					. "SET "
						. "TfcId=" . StrSafe_DB($newId) . " "
					. "WHERE "
						. "TfcCoId=" . StrSafe_DB($team) . " AND TfcSubTeam=" . StrSafe_DB($subTeam) . " AND "
						. "TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfcEvent=" . StrSafe_DB($ev) . " AND "
						. "TfcId=" . StrSafe_DB($oldId) . " ";
			//	print $query;exit;
				$rs=safe_w_sql($query);*/

				$oldOrder=1;
				$query
					= "SELECT TfcOrder FROM TeamFinComponent "
					. "WHERE "
						. "TfcCoId=" . StrSafe_DB($team) . " AND TfcSubTeam=" . StrSafe_DB($subTeam) . " AND "
						. "TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfcEvent=" . StrSafe_DB($ev) . " AND "
						. "TfcId=" . StrSafe_DB($oldId) . " ";
				$rs=safe_r_sql($query);

				if ($rs && safe_num_rows($rs)==1)
				{
					$row=safe_fetch($rs);
					$oldOrder=$row->TfcOrder;

					$query
						= "DELETE FROM TeamFinComponent "
						. "WHERE "
							. "TfcCoId=" . StrSafe_DB($team) . " AND TfcSubTeam=" . StrSafe_DB($subTeam) . " AND "
							. "TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfcEvent=" . StrSafe_DB($ev) . " AND "
							. "TfcId=" . StrSafe_DB($oldId) . " ";
					$rs=safe_w_sql($query);

					if ($rs)
					{
						$query
							= "INSERT INTO TeamFinComponent (TfcCoId,TfcSubTeam,TfcTournament,TfcEvent,TfcId,TfcOrder) "
							. "VALUES("
								. StrSafe_DB($team) . ","
								. StrSafe_DB($subTeam) . ","
								. StrSafe_DB($_SESSION['TourId']) . ","
								. StrSafe_DB($ev) . ","
								. StrSafe_DB($newId) . ","
								. $oldOrder . " "
							. ") ";
						$rs=safe_w_sql($query);
					}
				}


			}
		}

		header('Location: ChangeComponents2.php?ev=' . $ev);
		exit;
	}

	$teams=array();

	$query = "SELECT
		TfcCoId,	TfcSubTeam,	TfcTournament,	TfcEvent,TfcId,TfcOrder,
		CoCode, CoName,
		EnId,EnFirstName,EnName,EnDivision,EnAgeClass,EnClass
		FROM TeamFinComponent
		INNER JOIN Teams ON TfcTournament=TeTournament and TfcEvent=TeEvent and TfcSubTeam=TeSubTeam and TeFinEvent=1 and TfcCoId=TeCoId
		INNER JOIN Countries ON TfcCoId=CoId
		INNER JOIN Entries ON TfcId=EnId
		WHERE TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfcEvent=" . StrSafe_DB($ev) . "
		ORDER BY TfcEvent ASC,TfcCoId ASC,TfcSubTeam ASC,TfcOrder ASC ";
	//print $query;	exit;
	$rs=safe_r_sql($query);

	if (safe_num_rows($rs)>0)
	{
		$curTeam=-1;
		while ($MyRow=safe_fetch($rs))
		{
			if ($curTeam!=$MyRow->TfcCoId . $MyRow->TfcSubTeam)
			{
				$teams[]=array
				(
					'team'=>$MyRow->TfcCoId,
					'code'=>$MyRow->CoCode,
					'name'=>stripslashes($MyRow->CoName),
					'subTeam'=>$MyRow->TfcSubTeam,
					'components'=>array()
				);

				$curTeam=$MyRow->TfcCoId . $MyRow->TfcSubTeam;
			}

			$teams[count($teams)-1]['components'][]=array
			(
				'id'=>$MyRow->EnId,
				'name'=>stripslashes($MyRow->EnFirstName . ' ' . $MyRow->EnName),
				'div'=>$MyRow->EnDivision,
				'ageClass'=>$MyRow->EnAgeClass,
				'class'=>$MyRow->EnClass
			);
		}

	/*	print '<pre>';
		print_r($teams);
		print '</pre>';exit;*/
	}
	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript">',
		'	function openSet(id)',
		'	{',
		'		var splitted=id.split(\'_\');',
		'		OpenPopup(\'SetTeamFinComponent.php?team=\' + splitted[1] + \'&subTeam=\' + splitted[2] + \'&ev=\' + splitted[3] + \'&id=\' + splitted[4],\'SetComponent\',600,500)',
		'	}',
		'</script>',
		);

	$PAGE_TITLE=get_text('ChangeComponents');

	include('Common/Templates/head.php');
?>
<div align="center">
	<div class="medium">
		<table class="Tabella">
			<tr><th class="Title" colspan="6"><?php print get_text('ChangeComponents') . ' - ' . $ev; ?></th></tr>
			<tr>
				<th style="width: 30%;" colspan="2"><?php print get_text('Team'); ?></th>
				<th><?php print get_text('TeamComponents'); ?></th>
				<th><?php print get_text('Div'); ?></th>
				<th><?php echo get_text('AgeCl') ?></th>
				<th><?php print get_text('Cl'); ?></th>
			</tr>
			<?php if (count($teams)>0) {?>
				<?php foreach ($teams as $t) { ?>
					<tr>
						<td class="Left" rowspan="<?php print count($t['components']); ?>">
							<?php print $t['code']  . ($t['subTeam']!=0 ? ' (' . $t['subTeam'] . ') ' : '');?>
						</td>
						<td class="Left" rowspan="<?php print count($t['components']); ?>">
							<?php print $t['name']; ?>
						</td>
						<td class="Left" id="row_<?php print $t['team'] . '_' . $t['subTeam'] . '_' . $ev . '_' . $t['components'][0]['id']; ?>" ondblclick="openSet(this.id);"><?php print $t['components'][0]['name']; ?></td>
						<td class="Left"><?php print $t['components'][0]['div']; ?></td>
						<td class="Left"><?php print $t['components'][0]['ageClass']; ?></td>
						<td class="Left"><?php print $t['components'][0]['class']; ?></td>
					</tr>
					<?php for ($i=1;$i<count($t['components']);++$i) { ?>
						<tr>
							<td class="Left" id="row_<?php print $t['team'] . '_' . $t['subTeam'] . '_' . $ev . '_' .$t['components'][$i]['id']; ?>" ondblclick="openSet(this.id);"><?php print $t['components'][$i]['name']; ?></td>
							<td class="Left"><?php print $t['components'][$i]['div']; ?></td>
							<td class="Left"><?php print $t['components'][$i]['ageClass']; ?></td>
							<td class="Left"><?php print $t['components'][$i]['class']; ?></td>
						</tr>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</table>
	</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>