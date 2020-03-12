<?php

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/CommonLib.php');

	CheckTourSession(true);

	$d_Event=isset($_REQUEST['d_Event']) ? $_REQUEST['d_Event'] : null;
	$d_Phase=isset($_REQUEST['d_Phase']) ? $_REQUEST['d_Phase'] : null;
	$d_Match=isset($_REQUEST['d_Match']) ? $_REQUEST['d_Match'] : null;
	$TeamEvent=isset($_REQUEST['Team']) ? $_REQUEST['Team'] : 0;
	$ElimPool=isset($_REQUEST['ElimPool']) ? $_REQUEST['ElimPool'] : 0;

    checkACL(($TeamEvent ? AclTeams : AclIndividuals), AclReadWrite);

	if(!is_null($d_Match) and !is_null($d_Event)) $ONLOAD=' onload="makeScore('.$TeamEvent.')"';

// combo degli eventi
	$comboEvents='';

	$Select
		= "SELECT EvCode,EvEventName "
		. "FROM Events "
		. "WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . "	AND EvTeamEvent=" . StrSafe_DB($TeamEvent). " AND EvFinalFirstPhase!=0 "
		. "ORDER BY EvProgr ASC ";
	$Rs=safe_r_sql($Select);

	$comboEvents
		.='<select name="d_Event" id="d_Event" onChange="javascript:ChangeEvent(' . $TeamEvent . ',null,true);">' . "\n"
			. '<option value="">----</option>' . "\n";

			if (safe_num_rows($Rs)>0)
			{
				while ($Row=safe_fetch($Rs))
				{
					$comboEvents.='<option value="' . $Row->EvCode . '"' . ($Row->EvCode==$d_Event ? ' selected' : ''). '>' . $Row->EvCode . ' - ' . get_text($Row->EvEventName,'','',true) . '</option>' . "\n";
				}
			}
	$comboEvents
		.='</select>' . "\n";

	$comboModes
		= '<select name="d_Modes" id="d_Modes">' . "\n"
			. '<option selected value="0">' . get_text('MenuLM_Arrow by Arrow (Scorecards)') . '</option>' . "\n"
			. '<option value="1">' . get_text($TeamEvent ? 'SpottingTeam':'SpottingInd') . '</option>' . "\n"
			. '<option value="2">' . get_text('Review') . '</option>' . "\n"
		. '</select>' . "\n";

	$JS_SCRIPT=array(phpVars2js(array(
	            "WebDir" => $CFG->ROOT_DIR,
                'TeamEvent' => $TeamEvent,
                'ElimPool' => $ElimPool,
		        'MoveWinner2NextPhase' => get_text('MoveWinner2NextPhase','Tournament'),
		        'MoveWinner2PoolA' => get_text('MoveWinner2PoolA','Tournament'),
		        'MoveWinner2PoolB' => get_text('MoveWinner2PoolB','Tournament'),
		        'Select' => get_text('Select','Tournament'),
                )),
			'<style>.hidden {display:none;}</style>'
	);

	$PAGE_TITLE=get_text($TeamEvent ? 'TeamFinal':'IndFinal');

	include('Common/Templates/head' . (isset($_REQUEST["hideMenu"]) ? '-min': '') . '.php');

?>
<form name="FrmParam" method="POST" action="">
	<table class="Tabella">
		<tr onClick="showOptions();"><th class="Title" colspan="4"><?php print get_text($TeamEvent ? 'TeamFinal':'IndFinal'); ?></th></tr>
		<tr class="Divider"><td colspan="4"></td></tr>
		<tbody id="options">
		<tr>
			<th width="30%"><?php echo get_text('Event'); ?></th>
			<th width="10%"><?php echo get_text('Phase'); ?></th>
			<th width="50%"><?php echo get_text('MatchNo'); ?></th>
			<th width="10%">&nbsp;</th>
		</tr>
		<tr>
			<td class="Center" width="30%"><?php echo $comboEvents; ?></td>
			<td class="Center" width="10%"><select name="d_Phase" id="d_Phase" onchange="ChangeEventPhase(<?php echo $TeamEvent; ?>);"></select></td>
			<td class="Center" width="50%"><select name="d_Match" id="d_Match"><?php

			if(!is_null($d_Match)) echo '<option value="'.$d_Match.'" selected="selected"></option>';
			?></select></td>
			<td rowspan="3" class="Center" width="10%"><input type="button" value="<?php print get_text('CmdOk');?>" onclick="makeScore(<?php echo $TeamEvent;?>);"><br>
			<?php
				echo '<input type="checkbox" id="showMenu" ' . (isset($_REQUEST["hideMenu"]) ? '' : 'checked') .
					' onClick="document.location=\'' . $_SERVER["PHP_SELF"]. '?Team='  . $TeamEvent . (isset($_REQUEST["hideMenu"]) ? '' : '&hideMenu') . '\';"' .
					'>&nbsp;';
				echo get_text('ShowIanseoMenu', 'Tournament');
			?>
			</td>
		</tr>
		<tr class="Divider"><td colspan="3"></td></tr>
		<tr>
			<td colspan="2" class="Bold">
				<input type="button" id="buttonMove2Next" value="<?php print get_text('MoveWinner2NextPhase','Tournament');?>" onclick="move2nextPhase(document.getElementById('d_Event').value,document.getElementById('d_Match').value,<?php echo $TeamEvent;?>);"/>
			</td>
			<td class="Center"><?php print $comboModes;?></td>
		</tr>
		<tr><td colspan="4">
		<?php
			if(file_exists($CFG->DOCUMENT_PATH . 'Modules/DanageDisplay/interface.php'))
			{
				require_once ('Modules/DanageDisplay/interface.php');
			}
		?>
		</td></tr>

		<tr class="Divider"><td colspan="4"></td></tr>
		</tbody>
	</table>
</form>
<div id="outputChunk"></div>
<?php

if(!empty($GoBack)) {
	echo '<table class="Tabella2" width="50%"><tr><th style="background-color:red"><a href="'.$GoBack.'" style="color:white">'.get_text('BackBarCodeCheck','Tournament').'</a></th></tr></table>';
}

// JavaScripts at the end for IE!
?>
<div id="idOutput"></div>
<script type="text/javascript" src="<?php print $CFG->ROOT_DIR;?>Common/js/Fun_JS.inc.js"></script>
<script type="text/javascript" src="<?php print $CFG->ROOT_DIR;?>Common/ajax/ObjXMLHttpRequest.js"></script>
<script type="text/javascript" src="<?php print $CFG->ROOT_DIR;?>Final/Fun_AJAX.js"></script>
<script type="text/javascript" src="<?php print $CFG->ROOT_DIR;?>Final/Fun_AJAX_WriteScoreCard.js"></script>
<?php
	if(file_exists($CFG->DOCUMENT_PATH . 'Modules/DanageDisplay/Fun_Display.js'))
		echo '<script type="text/javascript" src="' . $CFG->ROOT_DIR . 'Modules/DanageDisplay/Fun_Display.js"></script>';
	include('Common/Templates/tail' . (isset($_REQUEST["hideMenu"]) ? '-min' : '') . '.php');
?>