<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Fun_CONI.local.inc.php');

	CheckTourSession(true);

	$round=(isset($_REQUEST['Round']) ? $_REQUEST['Round'] : null);
	$event=(isset($_REQUEST['EventCode']) ? $_REQUEST['EventCode'] : null);

	$manTie=(isset($_REQUEST['ManTie']) ? $_REQUEST['ManTie'] : null);

	if (is_null($round) || is_null($event) || is_null($manTie))
		exit;

	// verifico se lo spareggio per l'evento è stato fatto
	$Select
		= "SELECT EvE1ShootOff  "
		. "FROM Events "
		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (EvE1ShootOff='1') AND EvTeamEvent='1' AND EvCode=" . StrSafe_DB($event) . " ";
	$Rs = safe_r_sql($Select);
	//print $Select;exit;
	if (!$Rs || safe_num_rows($Rs)!=1)
	{
		header('Location: AbsTeam2_1.php?EventCode=' . $event);
		exit;
	}


	$Rs=getMatchesPhase1($event,$round);

	$html='';

	$curGroup='xxx';
	$match=0;
	if ($Rs && safe_num_rows($Rs)>0)
	{
		while ($myRow=safe_fetch_assoc($Rs))
		{
			if ($curGroup!=$myRow['Group'])
			{
				if ($curGroup!='xxx')
				{
					$html.='</table><br/>' . "\n";
				}

				$html
					.='<table class="Tabella">' . "\n"
						. '<tr><th colspan="7">' . get_text('Group#','Tournament',$myRow['Group']) . '</th></tr>' . "\n"
						. '<tr>'
							. '<td class="Title" style="width: 5%;">' . get_text('MatchNo') . '</td>'
							. '<td class="Title" style="width: 5%;">' . get_text('Target') . '</td>'
							. '<td class="Title" style="width: 20%;">' . get_text('Country') . '</td>'
							. '<td class="Title" style="width: 10%;">' . get_text('SetPoints','Tournament') . '</td>'
							. '<td class="Title" style="width: 10%;">' . get_text('Total') . '</td>'
							. '<td class="Title" style="width: 10%;">Tie</td>'
							. '<td class="Title">' . get_text('TieArrows') . '</td>'
						. '</tr>' . "\n";
			}

			$rowStyle=(++$match%2==0 ? '' : ' class="warning"');

			for ($i=1;$i<=2;++$i)
			{
				$code=$event . '_' . $round . '_' . $myRow['Match' . $i];

			// freece di tiebreak
				$tieString='';
				if (false && $manTie==1)
				{
					$TieBreak = str_pad($myRow['Tiebreak' . $i],TieBreakArrows_Team,' ',STR_PAD_RIGHT);

					for ($k=0;$k<TieBreakArrows_Team;++$k)
					{
						$tieString.='<input type="text"
							id="t_' . $code . '_' . $k . '"
							name="t_' . $code . '[]"
							size="2" maxlength="2"
							value="' . DecodeFromLetter($TieBreak[$k]) . '"
							onblur="updateScore1(this.id);"
						/>&nbsp;';
					}
				}

				$html
					.='<tr' . $rowStyle. '>'
						. '<td>' . $myRow['Match' . $i] . '</td>'
						. '<td>' . $myRow['TargetNo' . $i]. '</td>'
						. '<td>' . $myRow['CountryCode' . $i] . ' - ' .  ($myRow['CountryName' . $i]!='' ? $myRow['CountryName' . $i] . (intval($myRow['SubTeamCode' . $i])<=1 ? '' : ' (' . $myRow['SubTeamCode' . $i] .')') : '&nbsp') . '</td>'
						. '<td class="Center">'
							. '<input type="text"
									maxlength="3" size="4"
									id="P_' . $code . '"
									name="P_' . $code . '"
									value="' . $myRow['SetScore' . $i] . '"
									onblur="updateScore1(this.id);"
								/>'
						. '</td>'
						. '<td class="Center">'
							. '<input type="text"
									maxlength="3" size="4"
									id="S_' . $code . '"
									name="S_' . $code . '"
									value="' . $myRow['Score' . $i] . '"
									onblur="updateScore1(this.id);"
								/>'
						. '</td>';
					if ($manTie==1)
					{
						$html
							.='<td class="Center">'
								. '<select id="T_' . $code . '" name="T_' . $code . '" onchange="updateScore1(this.id);">' . "\n"
									. '<option value="0"' . ($myRow['Tie' . $i]==0 ? ' selected' : '') . '>0 - No Tie</option>' . "\n"
									//. '<option value="1"' . ($myRow['Tie' . $i]==1 ? ' selected' : '') . '>1 - Tie</option>' . "\n"
									. '<option value="2"' . ($myRow['Tie' . $i]==2 ? ' selected' : '') . '>2 - Bye</option>' . "\n"
								. '</select>' . "\n"
							. '</td>';
					}
					else
					{
						$html.='<td></td>';
					}
				$html
						.='<td>'
							. $tieString
						.  '</td>'
					. '</tr>' . "\n";
			}

			$curGroup=$myRow['Group'];

		}

		$html.='</table>' . "\n";
	}

	$JS_SCRIPT=array
	(
		'<script type="text/javascript" src="../../Common/ajax/ObjXMLHttpRequest.js"></script>'
		,'<script type="text/javascript" src="Fun_AJAX_Phase1.js"></script>'
	);

	include('Common/Templates/head.php');
?>
<form name="frm" method="post" action="<?php print $_SERVER['PHP_SELF'];?>">

	<table class="Tabella">
		<tr><th class="Title"><?php print $event; ?> - <?php print get_text('Round#','Tournament',$round); ?></th></tr>
		<tr><td class="Center"><?php print roundPager($round,'EventCode=' . $event . '&ManTie=' . $manTie ); ?></td></tr>
	</table>

	<br/>

	<?php print $html;?>
</form>

<?php include('Common/Templates/tail.php'); ?>