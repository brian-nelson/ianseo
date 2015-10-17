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
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Elimination/Fun_AJAX_SetTarget.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/Fun_JS.inc.js"></script>',
		);

	include('Common/Templates/head.php');
?>
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('ManualTargetAssignment','Tournament');?></th></tr>
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
		$sessions=GetSessions('E');
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
			= "SELECT "
				. "ElElimPhase,ElEventCode,EvEventName,ElQualRank,ElTournament,ElTargetNo AS TargetNo,ElSession,CoCode, CoName,"
				. "EnCode,EnName,EnFirstName,EnDivision,EnClass,EnCountry "
			. "FROM "
				. "Eliminations "
				. "INNER JOIN Events ON ElEventCode=EvCode AND ElTournament=EvTournament AND EvTeamEvent=0 "
				. "LEFT JOIN "
					. "Entries "
				. "ON ElId=EnId AND ElTournament=EnTournament "
				. "LEFT JOIN "
					. "Countries "
				. "ON EnCountry=CoId AND EnTournament=CoTournament "
			. "WHERE "
				. "ElTournament=". StrSafe_DB($_SESSION['TourId']) . "  " . $EventsFilter . " ";

			$Select.= "ORDER BY ElElimPhase ASC, ElEventCode ASC,ElQualRank ASC ";


			//debug_svela($Select, true);
		$Rs=safe_r_sql($Select);
	//print $Select;

		$curEvent='';

		if (safe_num_rows($Rs)>0)
		{
			while ($MyRow=safe_fetch($Rs))
			{
				if ($curEvent!=$MyRow->ElElimPhase.'_'.$MyRow->ElEventCode)
				{
					if ($curEvent!='')
					{
						print '</table>';
					}
					print '<table class="Tabella">' . "\n";
						print '<tr>';
							print '<th class="Title "colspan="6">'.get_text('Eliminations_' . ($MyRow->ElElimPhase+1) ) . " - " . $MyRow->ElEventCode.'</th>';
							print '<th colspan="2">';
								print '<div align="left">';
									$id=$MyRow->ElElimPhase.'_'.$MyRow->ElEventCode.'_'.$MyRow->ElTournament;
									print get_text('Session').'&nbsp;&nbsp;<select id="d_q_ElSession_' . $id.'" onChange="UpdateSession(\'d_q_ElSession_' .$id .'\');">';
										print '<option value="0">---</option>';
										foreach ($sessions as $s)
										{
											print '<option value="' . $s->SesOrder. '"' . ($s->SesOrder==$MyRow->ElSession ? ' selected' : '') . '>'.$s->Descr.'</option>';
										}
									print '</select>';
								print '</div>';
							print '</th>';
						print '</tr>';
						print '<tr>';
							//print '<th class="SubTitle" width="10%">' . get_text('Event') . '</a></th>';
							print '<th class="SubTitle" width="5%">' . get_text('Rank') . '</a></th>';
							print '<th class="SubTitle" width="10%">' . get_text('Target') . '</a></th>';
							print '<th class="SubTitle" width="5%">' . get_text('Code','Tournament') . '</a></th>';
							print '<th class="SubTitle" width="15%">' . get_text('Athlete') . '</a></th>';
							print '<th class="SubTitle" colspan="2" width="25%">' . get_text('Country') . '</a></th>';
							print '<th class="SubTitle" width="10%">' . get_text('Division') . '</a></th>';
							print '<th class="SubTitle" width="10%">' . get_text('Class') . '</a></th>';
						print '</tr>' . "\n";
				}

				$id=$MyRow->ElElimPhase.'_'.$MyRow->ElEventCode.'_'.$MyRow->ElQualRank.'_'.$MyRow->ElTournament;

				print '<tr>';

			/*	print '<td class="Center">';
				print get_text('Eliminations_' . ($MyRow->ElElimPhase+1) ) . " - " . $MyRow->ElEventCode;
				print '</td>';*/

				print '<td class="Center">';
				print $MyRow->ElQualRank;
				print '</td>';

				print '<td class="Center">';
				print '<input type="text" size="4"  name="d_q_ElTargetNo_' . $id. '" id="d_q_ElTargetNo_' . $id . '" value="' . $MyRow->TargetNo . '"' . ' onBlur="javascript:UpdateTargetNo(\'d_q_ElTargetNo_' . $id . '\');">';
				//print $MyRow->TargetNo;
				print '</td>';

				print '<td class="Center">';
				print ($MyRow->EnCode!='' ? $MyRow->EnCode : '&nbsp;');
				print '</td>';

				print '<td>';
				print ($MyRow->EnFirstName . ' ' . $MyRow->EnName!=' ' ? $MyRow->EnFirstName . ' ' . $MyRow->EnName : '&nbsp;');
				print '</td>';

				print '<td class="Center" width="4%">';
				print ($MyRow->CoCode!='' ? $MyRow->CoCode : '&nbsp;');
				print '</td>';

				print '<td width="16%">';
				print ($MyRow->CoName!='' ? $MyRow->CoName : '&nbsp;');
				print '</td>';

				print '<td class="Center">';
				print (trim($MyRow->EnDivision)!='' ? $MyRow->EnDivision : '&nbsp;');
				print '</td>';

				print '<td class="Center">';
				print (trim($MyRow->EnClass)!='' ? $MyRow->EnClass : '&nbsp;');
				print '</td>';

				print '</tr>';

				$curEvent=$MyRow->ElElimPhase.'_'.$MyRow->ElEventCode;
			}
		}
		print '</table>' . "\n";

?>
<div id="idOutput"></div>
<script type="text/javascript">FindRedTarget();</script>
<?php
	include('Common/Templates/tail.php');
?>