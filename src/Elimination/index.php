<?php
if(!defined('debug')) define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
checkACL(AclEliminations, AclReadWrite);
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Lib/CommonLib.php');

$JS_SCRIPT=array(
	phpVars2js(array('RootDir'=>$CFG->ROOT_DIR.'Elimination/')),
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Elimination/WriteArrows.js"></script>',
	);

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

while($MyRow=safe_fetch($Rs)) {
	if ($MyRow->EvElim1>0) {
		$CheckEvent1.='<input type="checkbox" name="Events[]" value="' . $MyRow->EvCode .'-0"' . (in_array($MyRow->EvCode . "-0",$Events) ? ' checked' : '') . '>' . $MyRow->EvCode.'&nbsp;&nbsp;';
	}
	if ($MyRow->EvElim2>0) {
		$CheckEvent2.='<input type="checkbox" name="Events[]" value="' . $MyRow->EvCode .'-1"' . (in_array($MyRow->EvCode . "-1",$Events) ? ' checked' : '') . '>' . $MyRow->EvCode.'&nbsp;&nbsp;';
	}
}

$ComboSes = '';
$q=safe_r_sql("select distinct group_concat(distinct concat(ElEventCode, '-', ElElimPhase) order by EvProgr) as SessionId, concat('".get_text('Session')." ', SesOrder, if(SesName!='', concat(': ', SesName), '')) SessionName
		from Eliminations
		inner join Events on EvTournament=ElTournament and EvTeamEvent=0
		inner join Session on SesTournament=ElTournament and ElSession=SesOrder and SesType='E'
		where ElTournament={$_SESSION['TourId']} and ElId>0
		group by SesOrder");
if(safe_num_rows($q)) {
	$ComboSes = '<select name="x_Session" id="x_Session" onChange="SelectSession(this);">' . "\n";
	$ComboSes.= '<option value="">---</option>' . "\n";

	while($r=safe_fetch($q)) {
		$ComboSes.= '<option value="' . $r->SessionId . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$r->SessionId ? ' selected' : '') . '>' . $r->SessionName . '</option>' . "\n";
	}
	$ComboSes.= '</select>' . "\n";
}

include('Common/Templates/head.php');

?>
<form name="FrmParam" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="Tabella">
<tr><th class="Title" colspan="5"><?php print get_text('Elimination');?></th></tr>
<tr class="Divider"><td colspan="5"></td></tr>
<tr>
	<th width="100"><?php print get_text('Session');?></th>
	<th width="100"><?php print get_text('Events', 'Tournament');?></th>
	<th></th>
</tr>
<tr>
	<td class="Center"><?php print $ComboSes; ?></td>
	<td nowrap="nowrap" id="EventSelector">
		<?php
			if ($CheckEvent1!='') {
				print '<div style="white-space:nobreak;" class="Right">' . get_text('Eliminations_1'). ': ' . $CheckEvent1.'</div>';
			}

			if ($CheckEvent2!='') {
				print '<div style="white-space:nobreak;" class="Right">' . get_text('Eliminations_2'). ': ' . $CheckEvent2.'</div>';
			}
			?>
	</td>
	<td><input type="submit" value="<?php echo get_text('CmdOk')?>"/></td>
</tr>
</table>
</form>

<br>
<?php
/*

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

*/
?>
<?php
$EventsFilter="";
if ($Events) {
	$EventsFilter.=" AND CONCAT(ElEventCode,'-', ElElimPhase) IN(" . implode(',', StrSafe_DB($Events)). ") ";
}

$Select = "SELECT EnId,EnCode,EnName,EnFirstName,EnTournament,EnDivision,EnClass,EnCountry,CoCode, (EnStatus <=1) AS EnValid, "
	. "ElTargetNo, SUBSTRING(ElTargetNo,1) AS Target,"
	. "ElEventCode, ElElimPhase, "
	. "ElScore as SelScore, ElHits as SelHits, ElGold as SelGold, ElXnine as SelXNine, ToGolds AS TtGolds, ToXNine AS TtXNine "
	. "FROM Entries INNER JOIN Countries ON EnCountry=CoId "
	. "INNER JOIN Eliminations ON EnId=ElId "
	. "INNER JOIN Tournament ON EnTournament=ToId AND ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
	. "WHERE EnAthlete=1 AND ElTargetNo<>'' " . $EventsFilter . " "
	. "ORDER BY ElElimPhase ASC, ElEventCode ASC, ElTargetNo ASC ";
$Rs=safe_r_sql($Select);

// form elenco persone
if (safe_num_rows($Rs)>0) {
?>
<form name="Frm" method="POST" action="">
<table class="Tabella">

<?php
	$CurEvent='';
	// elenco persone
	while ($MyRow=safe_fetch($Rs)) {
		if($CurEvent != $MyRow->ElElimPhase.$MyRow->ElEventCode) {
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

<tr <?php echo 'class="rowHover ' . ($MyRow->EnValid ? '' : 'NoShoot') . '"'; ?> id="Row_<?php echo $MyRow->EnId . '_' . $MyRow->ElEventCode . '_' . $MyRow->ElElimPhase ;?>">
<td><?php print get_text('Eliminations_' . ($MyRow->ElElimPhase+1)); ?></td>
<td><?php print $MyRow->ElEventCode; ?></td>
<td><?php print $MyRow->Target; ?></td>
<td><?php print $MyRow->EnCode; ?></td>
<td><?php print $MyRow->EnFirstName . ' ' . $MyRow->EnName; ?></td>
<td><?php print $MyRow->CoCode; ?></td>
<td class="Center"><?php print '<input type="text" size="4" maxlength="5" id="d_ElScore_' . $MyRow->EnId . '_' . $MyRow->ElEventCode . '_' . $MyRow->ElElimPhase . '" value="' . $MyRow->SelScore . '" onchange="javascript:UpdateElim(this);"' . ($MyRow->EnValid ? '' : 'disabled') .'>';?></td>
<td class="Center"><?php print '<input type="text" size="4" maxlength="5" id="d_ElGold_' . $MyRow->EnId . '_' . $MyRow->ElEventCode . '_' . $MyRow->ElElimPhase . '" value="' . $MyRow->SelGold . '" onchange="javascript:UpdateElim(this);"' . ($MyRow->EnValid ? '' : 'disabled') .'>';?></td>
<td class="Center"><?php print '<input type="text" size="4" maxlength="5" id="d_ElXnine_' . $MyRow->EnId . '_' . $MyRow->ElEventCode . '_' . $MyRow->ElElimPhase . '" value="' . $MyRow->SelXNine . '" onchange="javascript:UpdateElim(this);"' . ($MyRow->EnValid ? '' : 'disabled') .'>';?></td>
<td class="Center"><?php print '<input type="text" size="4" maxlength="5" id="d_ElHits_' . $MyRow->EnId . '_' . $MyRow->ElEventCode . '_' . $MyRow->ElElimPhase . '" value="' . $MyRow->SelHits . '" onchange="javascript:UpdateElim(this);"' . ($MyRow->EnValid ? '' : 'disabled') .'>';?></td>
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
