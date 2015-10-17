<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	$Events=isset($_REQUEST['Events']) ? $_REQUEST['Events'] : array();

	$Select
		= "SELECT EvCode,EvTournament,	EvEventName, EvElim1, EvElim2 "
		. "FROM Events "
		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='0' "
		. "AND (EvElim1>0 OR EvElim2>0) "
		. "ORDER BY EvProgr ASC ";
	$Rs=safe_r_sql($Select);

	$CheckEvent1='';
	$CheckEvent2='';

	if (safe_num_rows($Rs)>0)
	{
		while($MyRow=safe_fetch($Rs))
		{
			if ($MyRow->EvElim1>0)
				$CheckEvent1.='<input type="checkbox" name="Events[]" value="' . $MyRow->EvCode .'#0"' . (in_array($MyRow->EvCode . "#0",$Events) ? ' checked' : '') . '>' . $MyRow->EvCode.'&nbsp;&nbsp;';
			if ($MyRow->EvElim2>0)
				$CheckEvent2.='<input type="checkbox" name="Events[]" value="' . $MyRow->EvCode .'#1"' . (in_array($MyRow->EvCode . "#1",$Events) ? ' checked' : '') . '>' . $MyRow->EvCode.'&nbsp;&nbsp;';
		}
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_index.js"></script>',
		'<script type="text/javascript" src="../Common/Fun_JS.inc.js"></script>',
		);

	include('Common/Templates/head.php');

	$sessions=GetSessions('E');

?>
<input type="hidden" name="xxx" id="Command">
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('Elimination');?></th></tr>
<tr class="Divider"><td colspan="2"></td></tr>
<tr>
<td class="Bold" width="20%"><input type="checkbox" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1"><?php echo get_text('CmdBlocAutoSave') ?></td>
<td width="80%">
<form name="FrmParam" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="Tabella">
<tr><td style="width:90%;"><table class="Tabella">
<?php
if ($CheckEvent1!='')
	print '<tr><td style="width:25%;" class="Right">' . get_text('Eliminations_1'). '</td><td>' . $CheckEvent1.'</td></tr>';

if ($CheckEvent2!='')
	print '<tr><td  style="width:25%;" class="Right">' . get_text('Eliminations_2'). '</td><td>' . $CheckEvent2.'</td></tr>';
?>
</table>
</td>
<td><input type="submit" value="<?php echo get_text('CmdOk')?>"/></td>
</table></form>
</td>
</tr>


</table>
<br>
<?php
	$EventsFilter="";
	$in=array();
	if (count($Events)>0)
	{
		foreach ($Events as $e)
		{
			list($ev,$phase)=explode('#',$e);
			$in[]=StrSafe_DB(str_replace('#','',$e));
		}

		$EventsFilter.=" AND CONCAT(ElEventCode,ElElimPhase) IN(" . implode(',',$in). ") ";
	}

	$Select
		= "SELECT EnId,EnCode,EnName,EnFirstName,EnTournament,EnDivision,EnClass,EnCountry,CoCode, (EnStatus <=1) AS EnValid, "
		. "ElTargetNo, SUBSTRING(ElTargetNo,1) AS Target,"
		. "ElEventCode, ElElimPhase, "
		. "ElScore as SelScore, ElHits as SelHits, ElGold as SelGold, ElXnine as SelXNine, ToGolds AS TtGolds, ToXNine AS TtXNine "
		. "FROM Entries INNER JOIN Countries ON EnCountry=CoId "
		. "INNER JOIN Eliminations ON EnId=ElId "
		. "INNER JOIN Tournament ON EnTournament=ToId AND ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "WHERE EnAthlete=1 AND ElTargetNo<>'' " . $EventsFilter . " "
		. "ORDER BY ElElimPhase ASC, ElEventCode ASC, ElTargetNo ASC ";
		//print $Select;exit;
	if (debug)
		print $Select . '<br>';
	$Rs=safe_r_sql($Select);

// form elenco persone
	if (safe_num_rows($Rs)>0)
	{
?>
<form name="Frm" method="POST" action="">
<table class="Tabella">

<?php
		$CurEvent='';
		// elenco persone
		while ($MyRow=safe_fetch($Rs))
		{
			if($CurEvent != $MyRow->ElElimPhase.$MyRow->ElEventCode)
			{
?>
<tr class="Divider"><td colspan="10"></td></tr>
<tr>
<td class="Title" width="10%"><?php print get_text('Elimination');?></td>
<td class="Title" width="5%"><?php print get_text('Event');?></td>
<td class="Title" width="5%"><?php print get_text('Target');?></td>
<td class="Title" width="5%"><?php print get_text('Code','Tournament');?></td>
<td class="Title" width="20%"><?php print get_text('Archer');?></td>
<td class="Title" width="10%"><?php print get_text('Country');?></td>
<td class="Title" width="5%"><?php print get_text('TotaleScore');?></td>
<td class="Title" width="5%"><?php print $MyRow->TtGolds; ?></td>
<td class="Title" width="5%"><?php print $MyRow->TtXNine; ?></td>
<td class="Title" width="5%"><?php print get_text('Arrows','Tournament');?></td>
</tr>
<?php
				$CurEvent = $MyRow->ElElimPhase.$MyRow->ElEventCode;
			}

?>

<tr <?php echo 'class="' . ($MyRow->EnValid ? '' : 'NoShoot') . '"'; ?>>
<td><?php print get_text('Eliminations_' . ($MyRow->ElElimPhase+1)); ?></td>
<td><?php print $MyRow->ElEventCode; ?></td>
<td><?php print $MyRow->Target; ?></td>
<td><?php print $MyRow->EnCode; ?></td>
<td><?php print $MyRow->EnFirstName . ' ' . $MyRow->EnName; ?></td>
<td><?php print $MyRow->CoCode; ?></td>
<td class="Center"><?php print '<input type="text" size="4" maxlength="5" name="d_ElScore_' . $MyRow->EnId . '_' . $MyRow->ElElimPhase . '" id="d_ElScore_' . $MyRow->EnId . '_' . $MyRow->ElElimPhase . '" value="' . $MyRow->SelScore . '" onBlur="javascript:UpdateElim(\'d_ElScore_' . $MyRow->EnId . '_' . $MyRow->ElElimPhase . '\');"' . ($MyRow->EnValid ? '' : 'disabled') .'>';?></td>
<td class="Center"><?print '<input type="text" size="4" maxlength="5" name="d_ElGold_' . $MyRow->EnId . '_' . $MyRow->ElElimPhase . '" id="d_ElGold_' . $MyRow->EnId . '_' . $MyRow->ElElimPhase . '" value="' . $MyRow->SelGold . '" onBlur="javascript:UpdateElim(\'d_ElGold_' . $MyRow->EnId . '_' . $MyRow->ElElimPhase . '\');"' . ($MyRow->EnValid ? '' : 'disabled') .'>';?></td>
<td class="Center"><?print '<input type="text" size="4" maxlength="5" name="d_ElXnine_' . $MyRow->EnId . '_' . $MyRow->ElElimPhase . '" id="d_ElXnine_' . $MyRow->EnId . '_' . $MyRow->ElElimPhase . '" value="' . $MyRow->SelXNine . '" onBlur="javascript:UpdateElim(\'d_ElXnine_' . $MyRow->EnId . '_' . $MyRow->ElElimPhase . '\');"' . ($MyRow->EnValid ? '' : 'disabled') .'>';?></td>
<td class="Center"><?print '<input type="text" size="4" maxlength="5" name="d_ElHits_' . $MyRow->EnId . '_' . $MyRow->ElElimPhase . '" id="d_ElHits_' . $MyRow->EnId . '_' . $MyRow->ElElimPhase . '" value="' . $MyRow->SelHits . '" onBlur="javascript:UpdateElim(\'d_ElHits_' . $MyRow->EnId . '_' . $MyRow->ElElimPhase . '\');"' . ($MyRow->EnValid ? '' : 'disabled') .'>';?></td>
</tr>
<?php
		}	// fine elenco persone
?>
</table>
</form>
<?php
	}

?>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>