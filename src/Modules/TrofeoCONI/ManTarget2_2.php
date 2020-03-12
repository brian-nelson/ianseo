<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Fun_CONI.local.inc.php');

	CheckTourSession(true);

	$round=(isset($_REQUEST['Round']) ? $_REQUEST['Round'] : null);
	$event=(isset($_REQUEST['EventCode']) ? $_REQUEST['EventCode'] : null);


	if (is_null($round) || is_null($event))
		exit;

	// verifico se lo spareggio per l'evento è stato fatto
	/* Ma anchee NO!!!
	$Select
		= "SELECT EvE2ShootOff "
		. "FROM Events "
		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvE2ShootOff='1' AND EvTeamEvent='1' AND EvCode=" . StrSafe_DB($event) . " ";
	$Rs = safe_r_sql($Select);
	//print $Select;exit;
	if (!$Rs || safe_num_rows($Rs)!=1)
	{
		header('Location: AbsTeam1_2.php?EventCode=' . $event);
		exit;
	}
	*/

	$html='';

	$Rs=getMatchesPhase1($event,$round,2);

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
						. '<tr><th colspan="3">' . get_text('Group#','Tournament',$myRow['Group']) . '</th></tr>' . "\n"
						. '<tr>'
							. '<td class="Title" style="width: 5%;">' . get_text('MatchNo') . '</td>'
							. '<td class="Title" style="width: 5%;">' . get_text('Target') . '</td>'
							. '<td class="Title" style="width: 20%;">' . get_text('Country') . '</td>'
						. '</tr>' . "\n";
			}

			$rowStyle=(++$match%2==0 ? '' : ' class="warning"');

			for ($i=1;$i<=2;++$i)
			{
				$code=$event . '_' . $round . '_' . $myRow['Match' . $i] ;

				$html
					.='<tr' . $rowStyle. '>'
						. '<td>' . $myRow['Match' . $i] . '</td>'
						. '<td class="Center">'
							. '<input type="text"
									maxlength="2" size="3"
									id="T_' . $code . '"
									name="T_' . $code . '"
									value="' . $myRow['TargetNo' . $i] . '"
									onblur="updateTarget2(this.id);"
								/>'
						. '</td>'
						. '<td>' . $myRow['CountryCode' . $i] . ' - ' .  ($myRow['CountryName' . $i]!='' ? $myRow['CountryName' . $i] . (intval($myRow['SubTeamCode' . $i])<=1 ? '' : ' (' . $myRow['SubTeamCode' . $i] .')') : '&nbsp') . '</td>'
					. '</tr>' . "\n";
			}

			$curGroup=$myRow['Group'];

		}

		$html.='</table>' . "\n";
	}

	$JS_SCRIPT=array
	(
		'<script type="text/javascript" src="../../Common/ajax/ObjXMLHttpRequest.js"></script>'
		,'<script type="text/javascript" src="Fun_AJAX_Phase2.js"></script>'
	);

	include('Common/Templates/head.php');
?>
<div align="center">
	<div class="half">
		<form name="frm" method="post" action="<?php print $_SERVER['PHP_SELF'];?>">

			<table class="Tabella">
				<tr><th class="Title"><?php print $event; ?> - <?php print get_text('Round#','Tournament',$round); ?></th></tr>
				<tr><td class="Center"><?php print roundPager($round,'EventCode=' . $event); ?></td></tr>
			</table>

			<br/>

			<?php print $html;?>
		</form>
	</div>
</div>
<?php include('Common/Templates/tail.php'); ?>