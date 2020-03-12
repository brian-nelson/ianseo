<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Fun_Various.inc.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');
	require_once('Tournament/Fun_ManSessions.inc.php');

    checkACL(AclCompetition, AclReadWrite);

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}

	$q=safe_r_sql("select * from Session where SesTournament={$_SESSION['TourId']}");
// 	if(safe_num_rows($q)>9) header('Location: ./ManSessions.php');

	$maxSessions=9;

	$sesOrders=array();
	for($i=0;$i<=$maxSessions;++$i)
	{
		$sesOrders[]=$i;
	}

/* qui scrivo */
	$NumErr=0;
	$Arr_Values2Check_ManSessions=array();
	foreach ($sesOrders as $o)
	{
		if ($o==0) continue;
		$Arr_Values2Check_ManSessions['d_ToTar_'.$o]=array('Func' => 'GoodNumTarget', 'Error' => false);
		$Arr_Values2Check_ManSessions['d_ToAth_'.$o]=array('Func' => 'GoodNumAth', 'Error' => false);
	}

	if (isset($_REQUEST['Command'])) {
		if ($_REQUEST['Command']=='SAVE') {
			if (!IsBlocked(BIT_BLOCK_TOURDATA)) {
				$NumErr = VerificaDati($Arr_Values2Check_ManSessions);

				if ($NumErr==0) {
				/*
				 * Prendo i dati delle Vecchie sessioni e me li metto in un array per usi futuri
				 */
					$oldSession = array();
					$q = "SELECT * 
					    FROM Session 
					    WHERE SesTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND SesType='Q'
					    ORDER BY SesOrder";

					$rs=safe_r_sql($q);
					while($MyRow=safe_fetch($rs)) {
						$oldSession[$MyRow->SesOrder] = $MyRow;
					}

				/*
				 *  Lavoro le sessioni
				 *  1) Aggiorno quelle che "restano"
				 *  2) Cancello quelle "di troppo"
				 *  3) Inserisco le nuove
				 */
					foreach ($sesOrders as $o) {
						if ($o==0) continue;	//Salto la "vuota"

						if($o <= $_REQUEST['d_ToNumSession'] && array_key_exists ($o, $oldSession)) 	// La sessione è nel numero delle valide, ed esisteva precedentemente
						{
							$x = updateSession(
								$_SESSION['TourId'],
								$o,
								'Q',
								$oldSession[$o]->SesName,
								$_REQUEST['d_ToTar_'.$o],
								$_REQUEST['d_ToAth_'.$o],
								$oldSession[$o]->SesFirstTarget,
								0,
								$oldSession[$o]->SesDtStart,
								$oldSession[$o]->SesDtEnd,
								$oldSession[$o]->SesOdfCode,
								$oldSession[$o]->SesOdfPeriod,
								$oldSession[$o]->SesOdfVenue,
								$oldSession[$o]->SesOdfLocation,
								false
							);
						}
						else if($o <= $_REQUEST['d_ToNumSession'] && !array_key_exists ($o, $oldSession)) 	// La sessione è nel numero delle valide, ma non esisteva precedentemente
						{
							if (isset($_REQUEST['d_ToTar_'.$o]) && isset($_REQUEST['d_ToAth_'.$o]))
							{
								$x=insertSession(
									$_SESSION['TourId'],
									$o,
									'Q',
									'',
									$_REQUEST['d_ToTar_'.$o],
									$_REQUEST['d_ToAth_'.$o],
									1,
									0
								);
							}
						}
						else
						{
							deleteSession($_SESSION['TourId'], $o, 'Q');
						}
					}
				}

			}
			else
			{
				foreach ($sesOrders as $o)
				{
					if ($o==0) continue;
					$Arr_Values2Check_ManSessions['d_ToTar_'.$o]=array('Func' => 'GoodNumTarget', 'Error' => true);
					$Arr_Values2Check_ManSessions['d_ToAth_'.$o]=array('Func' => 'GoodNumAth', 'Error' => true);
				}
			}
		}
	}
/* fine qui scrivo */


	$numSessions=GetNumQualSessions();

	$allQSessions=GetSessions('Q');

	$sessions=array();

	foreach ($allQSessions as $s)
	{
		if (in_array($s->SesOrder,$sesOrders))
		{
			$sessions[$s->SesOrder]=$s;
		}
	}

/*	print '<pre>';
	print_r($sessions);
	print '</pre>';exit;
*/
	$JS_SCRIPT = array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_JS.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_AJAX_ManDistances.js"></script>',
	);
	$PAGE_TITLE=get_text('ManSession', 'Tournament');

	include('Common/Templates/head.php');
?>
	<form name="Frm" method="post" action="">
		<input type="hidden" name="Command" value="SAVE">
		<table class="Tabella">
			<tr><th class="Title" colspan="3"><?php print get_text('ManSession', 'Tournament'); ?></th></tr>
			<tr class="Divider"><td colspan="3"></td></tr>
			<tr>
				<th class="TitleLeft" width="15%"><?php echo get_text('NumSession', 'Tournament') ?></th>
				<td width="5%">
					<select name="d_ToNumSession" onChange="javascript:ChangeNumSession();">
					<?php foreach ($sesOrders as $o) { ?>
						<option value="<?php print $o;?>"<?php print ($o==$numSessions ? ' selected' : '');?>><?php print $o;?></option>
					<?php }?>
					</select>
				</td>
				<td>
					<?php if (!defined('hideSchedulerAndAdvancedSession')){?>
						<a  class="Link"  href="ManSessions.php">:<?php print get_text('Advanced');?>:</a>
					<?php } else { ?>
						&nbsp;
					<?php } ?>
				</td>
			</tr>
		</table>
		<br/>
		<table class="Tabella">
			<tr><th class="Title" colspan="<?php print $maxSessions;?>"><?php echo get_text('Tar4Session', 'Tournament') ?></th></tr>
			<tr class="Divider"><td colspan="<?php print $maxSessions;?>"></td></tr>
			<?php
				$StrHeader = '<tr>';
				$StrValue = '<tr>';
				foreach ($sesOrders as $o)
				{
					if ($o==0) continue;
					$StrHeader.= '<td class="Title" width="11%">' . get_text('Session') . ' ' . $o . '</td>';
					$StrValue.='
						<td>
							<input size="5" ' . ($o>$numSessions ? ' readonly' : '') . '
								maxlength="3"
								class="number' . ($o>$numSessions ? ' disabled' : ($Arr_Values2Check_ManSessions['d_ToTar_' . $o]['Error'] ? ' error' : '')) .'"
								id="d_ToTar_' .$o .'"
								name="d_ToTar_' . $o.'"

								value="' . (array_key_exists($o,$sessions) ? ($Arr_Values2Check_ManSessions['d_ToTar_' . $o]['Error'] ? $_REQUEST['d_ToTar_' . $o] : $sessions[$o]->SesTar4Session) : 0).'"
							/>
						</td>
					';
				}
				$StrHeader.= '</tr>' . "\n";
				$StrValue.= '</tr>' . "\n";

				print $StrHeader;
				print $StrValue;
			?>

			<tr><th class="Title" colspan="<?php print $maxSessions;?>"><?php echo get_text('Ath4Target', 'Tournament') ?></th></tr>
			<tr class="Divider"><td colspan="<?php print $maxSessions;?>"></td></tr>
			<?php
				$StrHeader = '<tr>';
				$StrValue = '<tr>';
				foreach ($sesOrders as $o)
				{
					if ($o==0) continue;
					$StrHeader.= '<td class="Title" width="11%">' . get_text('Session') . ' ' . $o . '</td>';
					$StrValue.='
						<td>
							<input size="5" ' . ($o>$numSessions ? ' readonly' : '') . '
								maxlength="2"
								class="number' . ($o>$numSessions ? ' disabled' : ($Arr_Values2Check_ManSessions['d_ToAth_' . $o]['Error'] ? ' error' : '')) .'"
								id="d_ToAth_' .$o .'"
								name="d_ToAth_' . $o.'"

								value="' . (array_key_exists($o,$sessions) ? ($Arr_Values2Check_ManSessions['d_ToAth_' . $o]['Error'] ? $_REQUEST['d_ToAth_' . $o] : $sessions[$o]->SesAth4Target) : 0).'"
							/>
						</td>
					';

				}
				$StrHeader.= '</tr>' . "\n";
				$StrValue.= '</tr>' . "\n";

				print $StrHeader;
				print $StrValue;
			?>
		</table>
		<br/>
		<table class="Tabella">
			<tr><td class="Center">
				<input type="submit" value="<?php print get_text('CmdSave');?>">&nbsp;&nbsp;
				<input type="button" value="<?php print get_text('CmdCancel');?>" onClick="javascript:FormCancel();">
			</td></tr>
		</table>
<?php

// DISTANCE INFORMATION MANAGEMENT
// Based on SESSIONS!!!!
require_once('./ManDistancesSessions.php');

?>
	</form>
<?php
	include('Common/Templates/tail.php');
?>