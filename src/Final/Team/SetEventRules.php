<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	if (!CheckTourSession() || !isset($_REQUEST['EvCode'])) printCrackError();
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Fun_Various.inc.php');

	if (isset($_REQUEST['DelRow']) && !IsBlocked(BIT_BLOCK_TOURDATA))
	{
		list($EcClass,$EcDivision) = explode('~',$_REQUEST['DelRow']);
		//print $EcClass . ' - ' . $EcDivision . '<br>';exit;
		$Delete
			= "DELETE FROM EventClass "
			. "WHERE EcCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EcClass=" . StrSafe_DB($EcClass) . " AND EcDivision=" . StrSafe_DB($EcDivision) . " "
			. "AND EcTeamEvent!='0' AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) .  " ";
			//print $Delete;exit;
		$Rs=safe_w_sql($Delete);

	// calcolo il numero massimo di persone nel team
		calcMaxTeamPerson(array($_REQUEST['EvCode']));

	// cancello le righe di Team per l'evento passato
		$queries[]
			= "DELETE FROM Teams WHERE TeTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TeFinEvent=1 AND TeEvent=" . StrSafe_DB($_REQUEST['EvCode']) . " ";

	// cancello i nomi
		$queries[]
			= "DELETE FROM TeamComponent WHERE TcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TcFinEvent=1 AND TcEvent=". StrSafe_DB($_REQUEST['EvCode']) . " ";

	// cancello i nomi fin
		$queries[]
			= "DELETE FROM TeamFinComponent WHERE TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfcEvent=" .  StrSafe_DB($_REQUEST['EvCode']) . " ";

	// elimino le griglie
		$queries[]
			= "DELETE FROM TeamFinals "
			. "WHERE TfEvent=" . StrSafe_DB($_REQUEST['EvCode']) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_w_sql($Delete);

	// reset shootoff
		ResetShootoff($_REQUEST['EvCode'],1,0);

	// teamabs
		MakeTeamsAbs(null,null,null);

		header('Location: SetEventRules.php?EvCode=' . $_REQUEST['EvCode']);
		exit;
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Team/Fun_AJAX_SetEventRules.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Team/Fun_JS.js"></script>',
		);

	$PAGE_TITLE=get_text('TeamDefinition');

	include('Common/Templates/head.php');
?>
<div align="center">
<div class="medium">
<table class="Tabella" id="MyTable">
<tbody id="tbody">
<tr><th class="Title" colspan="5"><?php print get_text('TeamDefinition');?></th></tr>
<tr class="Divider"><td colspan="5"></td></tr>
<?php
	$Select
		= "SELECT EvCode,EvEventName,EvPartialTeam,EvMultiTeam,EvMixedTeam,EvTeamCreationMode,EvElimEnds,EvElimArrows,EvElimSO,EvFinEnds,EvFinArrows,EvFinSO "
		. "FROM Events "
		. "WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$RsEv = safe_r_sql($Select);

	$RowEv=null;

	if (safe_num_rows($RsEv)==1)
	{
		$RowEv=safe_fetch($RsEv);
?>
<tr><td class="Title" colspan="5"><?php print get_text($RowEv->EvEventName,'','',true);?></td></tr>
<tr>
<th width="25%"><?php print get_text('Number');?></th>
<th width="20%"><?php print get_text('Division');?></th>
<th width="20%"><?php print get_text('Class');?></th>
<th width="10%">&nbsp;</th>
<th width="25%">&nbsp;</th>
</tr>
<?php
		$Select
			= "SELECT ec.*,Quanti "
			. "FROM EventClass AS ec INNER JOIN("
			. "SELECT COUNT(*) AS Quanti,EcCode,EcTeamEvent,EcTournament "
			. "FROM EventClass "
			. "WHERE EcCode=" . StrSafe_DB($RowEv->EvCode) . " AND EcTeamEvent!='0' AND EcTournament= " . StrSafe_DB($_SESSION['TourId']) . " "
			. "GROUP BY EcCode,EcTeamEvent,EcTournament"
			. ") AS sq ON ec.EcCode=sq.EcCode AND ec.EcTeamEvent=sq.EcTeamEvent AND ec.EcTournament=sq.EcTournament "
			. "WHERE ec.EcCode=" . StrSafe_DB($RowEv->EvCode) . " AND ec.EcTeamEvent<>'0' AND ec.EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "ORDER BY EcTeamEvent ASC,EcDivision,EcClass ";
		$Rs=safe_r_sql($Select);
 		//print $Select;exit;
		if (safe_num_rows($Rs)>0)
		{
			$MyGroup = -1;

			while ($MyRow=safe_fetch($Rs))
			{
				if ($MyGroup!=$MyRow->EcTeamEvent && $MyGroup!=-1)
					print '<tr id="Div_' . $MyRow->EcCode . '_' . $MyRow->EcTeamEvent . '" class="Divider"><td colspan="5"></td></tr>' . "\n";

				print '<tr id="Row_' . $MyRow->EcCode . '_' . $MyRow->EcTeamEvent . '_' . $MyRow->EcDivision . '_' . $MyRow->EcClass . '">';
				if ($MyGroup!=$MyRow->EcTeamEvent)
					print '<td rowspan="' . $MyRow->Quanti . '" class="Center">' . $MyRow->EcNumber . '</td>';
				print '<td class="Center">' . $MyRow->EcDivision . '</td>';
				print '<td class="Center">' . $MyRow->EcClass . '</td>';
				$Row2Del = $MyRow->EcClass . '~' . $MyRow->EcDivision; // l'altro pezzo di chiave lo ricavo con gli altri parametri e la sessione
				print '<td class="Center"><a href="' . $_SERVER['PHP_SELF'] . '?EvCode=' . $MyRow->EcCode . '&amp;DelRow=' . $Row2Del . '"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#"></a></td>';
				if ($MyGroup!=$MyRow->EcTeamEvent)
				{
					print '<td rowspan="' . $MyRow->Quanti . '" class="Center">';
					print '<a href="javascript:DeleteEventRule(\'' . $MyRow->EcCode .'\',\'' . $MyRow->EcTeamEvent . '\');"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#"></a>';
					print '</td>';
				}
				print '</tr>' . "\n";



				$MyGroup=$MyRow->EcTeamEvent;
			}
		}
	}
?>
</tbody>
</table>
<br>

<table class="Tabella">
<tr><td class="Center">
<?php echo get_text('MixedTeamEvent') ?>&nbsp;
<select name="d_EvMixedTeam" id="d_EvMixedTeam" onChange="SetMixedTeam('<?php print $RowEv->EvCode; ?>');">
	<option value="0"<?php print ($RowEv!=null && $RowEv->EvMixedTeam==0 ? ' selected' : ''); ?>><?php print get_text('No'); ?></option>
	<option value="1"<?php print ($RowEv!=null && $RowEv->EvMixedTeam==1 ? ' selected' : ''); ?>><?php print get_text('Yes'); ?></option>
</select>
</td></tr>
<tr><td class="Center">
<?php echo get_text('AllowPartialTeams') ?>&nbsp;
<select name="d_EvPartialTeam" id="d_EvPartialTeam" onChange="SetPartialTeam('<?php print $RowEv->EvCode; ?>');">
	<option value="0"<?php print ($RowEv!=null && $RowEv->EvPartialTeam==0 ? ' selected' : ''); ?>><?php print get_text('No'); ?></option>
	<option value="1"<?php print ($RowEv!=null && $RowEv->EvPartialTeam==1 ? ' selected' : ''); ?>><?php print get_text('Yes'); ?></option>
</select>
</td></tr>
<tr><td class="Center">
<?php echo get_text('AllowMultiTeam') ?>&nbsp;
<select name="d_EvMultiTeam" id="d_EvMultiTeam" onChange="SetMultiTeam('<?php print $RowEv->EvCode; ?>');">
	<option value="0"<?php print ($RowEv!=null && $RowEv->EvMultiTeam==0 ? ' selected' : ''); ?>><?php print get_text('No'); ?></option>
	<option value="1"<?php print ($RowEv!=null && $RowEv->EvMultiTeam==1 ? ' selected' : ''); ?>><?php print get_text('Yes'); ?></option>
</select>
</td></tr>

<tr>
	<td class="Center">
		<?php print get_text('TeamCreationMode','Tournament');?>&nbsp;
		<?php
			$comboTeamCreationMode=ComboFromRs(
				array(
					array('id'=>0,'descr'=>get_text('TeamCreationMode_0','Tournament')),
					array('id'=>1,'descr'=>get_text('TeamCreationMode_1','Tournament')),
					array('id'=>2,'descr'=>get_text('TeamCreationMode_2','Tournament')),
					array('id'=>3,'descr'=>get_text('TeamCreationMode_3','Tournament')),
				),
				'id',
				'descr',
				1,
				$RowEv->EvTeamCreationMode,
				null,
				'd_EvTeamCreationMode',
				'd_EvTeamCreationMode',
				array(
					'onChange'=>'SetTeamCreationMode(\'' . $RowEv->EvCode. '\');'
				)
			);

			print $comboTeamCreationMode;
		?>
	</td>
</tr>

</table>
<br/>

<table class="Tabella">
<tr><td colspan="4" class="Center"><?php print get_text('PressCtrl2SelectAll'); ?></td></tr>
<tr>
<td width="25%" class="Center" valign="top">
<input type="text" name="New_EcNumber" id="New_EcNumber" size="3" maxlength="3" value="">
</td>
<td width="25%" class="Center" valign="top">
<?php
	$ComboDiv
		= '<select name="New_EcDivision" id="New_EcDivision" multiple="multiple">' . "\n";

	$Select
		= "SELECT * "
		. "FROM Divisions "
		. "WHERE DivTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND DivAthlete=1 "
		. "ORDER BY DivViewOrder ASC ";
	$RsSel = safe_r_sql($Select);

	if (safe_num_rows($RsSel)>0)
	{
		while ($Row=safe_fetch($RsSel))
		{
			//print '<input type="checkbox" name="New_EcDivision[]" id="New_EcDivision_' . $MyRow->DivId .'" value="1">' . $MyRow->DivId . '<br>';
			$ComboDiv.= '<option value="' . $Row->DivId . '">' . $Row->DivId . '</option>' . "\n";
		}
	}

	$ComboDiv.= '</select>' . "\n";
	print $ComboDiv;
	print '<br><br><a class="Link" href="javascript:SelectAllOpt(\'New_EcDivision\');">' . get_text('SelectAll') . '</a>';
?>
</td>
<td width="25%" class="Center" valign="top">
<?php
	$ComboCl
		= '<select name="New_EcClass" id="New_EcClass" multiple="multiple">' . "\n";

	$Select
		= "SELECT * "
		. "FROM Classes "
		. "WHERE ClTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND ClAthlete=1 "
		. "ORDER BY ClViewOrder ASC ";
	$RsSel = safe_r_sql($Select);

	if (safe_num_rows($RsSel)>0)
	{
		while ($Row=safe_fetch($RsSel))
		{
			//print '<input type="checkbox" name="New_EcClass[]" id="New_EcClass_' . $MyRow->ClId .'" value="1">' . $MyRow->ClId . '<br>';
			$ComboCl.= '<option value="' . $Row->ClId . '">' . $Row->ClId . '</option>' . "\n";
		}
	}

	$ComboCl.= '</select>' . "\n";
	print $ComboCl;
	print '<br><br><a class="Link" href="javascript:SelectAllOpt(\'New_EcClass\');">' . get_text('SelectAll') . '</a>';
?>
</td>
<td width="25%" class="Center" valign="top">
<input type="button" name="Command" id="Command" value="<?php print get_text('CmdSave');?>" onclick="javascript:AddEventRule('<?php print $RowEv->EvCode;?>');">
</td>
</tr>
</table>
<table class="Tabella">

<tr><td class="Center"><a class="Link" href="ListEvents.php"><?php echo get_text('Back') ?></a></td></tr>
</table>
<div id="idOutput"></div>
</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>