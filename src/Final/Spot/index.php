<?php
/*
 * ViewInd.php riceve 2 tipi di querystring: 1 con Live e l'altra senza.
 * Se è presente Live la pagina cerca l'evento live
 * se non è presente vengono analizzati Event e MatchNo che servono a scegliere lo scontro da vedere
 * (indipendentemente dal flag di live).
 *
 */
	define ("debug",false);		// true per l'ouput di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Final/Spot/Common/Config.inc.php');
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Fun_Modules.php');

/**********************
  Cerco l'evento LIVE
***********************/
	$MatchNo=(!isset($_REQUEST['d_Match']) ? -1 : intval($_REQUEST['d_Match']));
	$Event=(empty($_REQUEST['d_Event']) ? '' : preg_replace('/[^a-zA-Z0-9-]/','',$_REQUEST['d_Event'] ));
	$Phase=(!isset($_REQUEST['d_Phase']) ? -1 : intval($_REQUEST['d_Phase']));
	$Team=-1;
	if($Event) list($Team, $Event)=explode('-', $Event, 2);

	$Lock=($Event && $MatchNo>=0 && $Team>=0 ? '1' : '0');

	if(!$Lock and $x=FindLive()) {
		list($Event, $MatchNo, $Team)=$x;
	}

	$JS_SCRIPT=array(
		'<link href="Common/style.css" media="screen" rel="stylesheet" type="text/css">',
		'<script type="text/javascript" src="../../Common/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="../../Common/ajax/ObjXMLHttpRequest.js"></script>',
		"<script>var Event='$Event'; var MatchNo=$MatchNo; var Team=$Team; var Lock=$Lock; var WebDir='$CFG->ROOT_DIR'; var Phase=$Phase; </script>",
		'<script type="text/javascript" src="Fun_ViewChunk.js"></script>',
		);
	if(module_exists('DanageDisplay')) {
		$JS_SCRIPT[] = '<script type="text/javascript" src="../../Modules/DanageDisplay/Fun_Display.js"></script>';
	}

	$NOSTYLE='nostyle';
	$ONLOAD=' onload="CheckUpdate();"';
	include('Common/Templates/head-min.php');

?>
<div id="Content"></div>
<div id="OverAllMenu" onclick="this.firstElementChild.style.display='block'; this.style.width='99%'; this.style.height='200px'">
<?php
// combo degli eventi
	$comboEvents='<select name="d_Event" id="d_Event" onChange="javascript:ChangeEvent(0,null,true);">';

	$Select
		= "SELECT EvCode,EvEventName "
		. "FROM Events "
		. "WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . "	AND EvTeamEvent=0 AND EvFinalFirstPhase!=0 "
		. "ORDER BY EvProgr ASC ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0) {
		$comboEvents .='<option value="">----</option>' . "\n";
		while ($Row=safe_fetch($Rs)) {
			$comboEvents.='<option value="0-' . $Row->EvCode . '">' . $Row->EvCode . ' - ' . get_text($Row->EvEventName,'','',true) . '</option>' . "\n";
		}
	}

	$Select
		= "SELECT EvCode,EvEventName "
		. "FROM Events "
		. "WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . "	AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 "
		. "ORDER BY EvProgr ASC ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0) {
		$comboEvents .='<option value="">----</option>' . "\n";
		while ($Row=safe_fetch($Rs)) {
			$comboEvents.='<option value="1-' . $Row->EvCode . '">' . $Row->EvCode . ' - ' . get_text($Row->EvEventName,'','',true) . '</option>' . "\n";
		}
	}
	$comboEvents
		.='</select>' . "\n";

?>
<form name="FrmParam" method="GET" action="" style="display:none">
	<table class="Tabella">
		<tr><th class="Title" colspan="4"><?php print get_text('ScheduledMatches', 'Tournament');?></th></tr>
		<tr class="Divider"><td colspan="4"></td></tr>
		<tr>
			<th width="30%"><?php print get_text('Event');?></th>
			<th width="10%"><?php echo get_text('Phase') ?></th>
			<th width="50%"><?php echo get_text('MatchNo') ?></th>
			<th width="10%">&nbsp;</th>
		</tr>
		<tr>
			<td class="Center" width="30%"><?php print $comboEvents;?></td>
			<td class="Center" width="10%"><select name="d_Phase" id="d_Phase" onchange="ChangeEventPhase();"></select></td>
			<td class="Center" width="50%"><select name="d_Match" id="d_Match"></select></td>
			<td rowspan="3" class="Center" width="10%"><input type="submit" value="<?php print get_text('CmdOk');?>" ></td>
		</tr>
		<?php 
		if(module_exists('DanageDisplay')) {
			echo '<tr><td colspan="4">';
			require_once ('Modules/DanageDisplay/interface.php');
			echo '</td></tr>';
		}
		?>
	</table>
</form>
</div>
<?php
	include('Common/Templates/tail-min.php');
?>