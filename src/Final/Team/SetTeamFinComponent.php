<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);
    checkACL(AclTeams, AclReadWrite);
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');

	$team=isset($_REQUEST['team']) ? $_REQUEST['team'] : null;
	$subTeam=isset($_REQUEST['subTeam']) ? $_REQUEST['subTeam'] : null;
	$ev=isset($_REQUEST['ev']) ? $_REQUEST['ev'] : null;
	$id=isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

	if (is_null($team) || is_null($subTeam) || is_null($ev) || is_null($id))
		exit;

	$query
		= "SELECT "
			. "CoCode,CoName "
		. "FROM "
			. "Countries "
		. "WHERE "
			. "CoTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
			. "CoId=" . StrSafe_DB($team) . " ";
	$rs=safe_r_sql($query);

	$MyRow=safe_fetch($rs);

	$title=$MyRow->CoCode . ' - ' . stripslashes($MyRow->CoName);

	$athletes=array();

	$query = "SELECT EnId AS `id`,EnDivision AS `div`,EnClass AS `class`,EnAgeClass AS `ageClass`,EnCode AS `code`,CONCAT(EnFirstName,' ',EnName) AS `name`
		FROM Entries 
		INNER JOIN EventClass ON EcCode=" . StrSafe_DB($ev) . " AND EcTeamEvent!=0 AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EcClass=EnClass AND EcDivision=EnDivision
		inner join Events on EvCode=EcCode and EvTeamEvent=1 and EvTournament=EnTournament
		WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
		    AND IF(EvTeamCreationMode=3, EnCountry3, if(EvTeamCreationMode=2, EnCountry2, if((EvTeamCreationMode=0 and EnCountry2=0) or EvTeamCreationMode=1, EnCountry, EnCountry2))) =" . StrSafe_DB($team) . " AND EnAthlete=1 AND EnStatus<=1
		    AND EnId NOT IN(
		        SELECT TfcId
		        FROM TeamFinComponent 
		        WHERE TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfcEvent=" . StrSafe_DB($ev) . " 
		        )";
	//debug_svela($query);

	$rs=safe_r_sql($query);

	if (safe_num_rows($rs)>0)
	{
		while ($MyRow=safe_fetch($rs))
		{
			$athletes[]=$MyRow;
		}
	}

	/*print '<pre>';
	print_r($athletes);
	print '</pre>';exit;*/
	$JS_SCRIPT=array(
		'<script type="text/javascript">',
		'	function updateOpener(team,subTeam,ev,oldId,newId)',
		'	{',
		'		window.opener.location',
		'			= \'ChangeComponents2.php\'',
		'			+ \'?ev=\' + ev',
		'			+ \'&command=Set\'',
		'			+ \'&team=\' + team',
		'			+ \'&subTeam=\' + subTeam',
		'			+ \'&oldId=\' + oldId',
		'			+ \'&newId=\' + newId;',
		'		window.close();',
		'	}',
		'</script>',
		);

	include('Common/Templates/head-popup.php');
?>
<table class="Tabella">
	<tr><th class="Title" colspan="4"><?php print $title; ?></th></tr>
	<tr>
		<th><?php print get_text('Athlete'); ?></th>
		<th><?php print get_text('Div'); ?></th>
		<th><?php echo get_text('AgeCl') ?></th>
		<th><?php print get_text('Cl'); ?></th>
	</tr>
	<?php if (count($athletes)>0) { ?>
		<?php foreach ($athletes as $a) { ?>
			<tr ondblclick="updateOpener(<?php print $team; ?>,<?php print $subTeam; ?>,'<?php print $ev;?>',<?php print $id; ?>,<?php print $a->id; ?>);">
				<td><?php print stripslashes($a->name); ?></td>
				<td><?php print $a->div; ?></td>
				<td><?php print $a->ageClass; ?></td>
				<td><?php print $a->class; ?></td>
			</tr>
		<?php } ?>
	<?php } ?>
	<tr><td colspan="4" class="Center"><input type="button" value="<?php print get_text('Close'); ?>" onclick="window.close();"/></td></tr>
</table>
<?php include('Common/Templates/tail-popup.php'); ?>