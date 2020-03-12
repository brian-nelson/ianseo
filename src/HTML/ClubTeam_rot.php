<?php
// !!!!!!!!!!!!!!!Lo script non è agganciato al motore delle regole!!!!!!!

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Modules/ClubTeam/Fun_ClubTeam.local.inc.php');


	// Record di partenza
	$PartiDa=(isset($_REQUEST['PD']) ? $_REQUEST['PD'] : 0);
	$_REQUEST['Rule']=-1;

	$Tempo=10;	// secondi di reload

	$Righe=4;	// num righe da stampare. ($Righe/2 è il numero di match che verranno visualizzati)

	$NextURL='';

	$Events=array("'RM'","'RW'");	// filtro sugli eventi	(IN(...))
	$Round=array(1);		// filtro sui round		(IN(...))	<-- per ora uno solo xchè bisogna mettere a posto l'orderby
	$Phase=1;			// filtro sulle fase	(secco)
	$Primary=1;		// primary o secondary  (secco)

	$where
		= "Team1.CTEventCode IN(" . join(",",$Events) . ") AND Team2.CTEventCode=Team1.CTEventCode "
		. "AND CTG.CTGRound IN(" . join(",",$Round) . ") "
		. "AND Team1.CTPhase = " . $Phase . " "
		. "AND TeamScore1.CTSPrimary = " . $Primary . " ";

	$limit= $PartiDa . "," . $Righe;

	$Rs=sql_getMatchesPhase1($where,$limit);

/*	$limit= $PartiDa . "," . $Righe;

	$Events='OLMT';
	$Round=1;
	$Phase=1;
	$Primary=1;
	$Rs=getMatchesPhase1($Events,$Round,$Primary,$Phase,"",$limit);*/

	$html="";
	$curGroup='xxx';
	$curEvent='xxx';

	if ($Rs)
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
					.='<table>' . "\n";
				if($curEvent != $myRow['EventName'])
				{
					$html .= '<tr><th class="Title" colspan="4" style="font-size:25px;">'
						. $myRow['EventCode'] . ' - ' . $myRow['EventName'] . '<br/>'
						. '<span style="font-size:12px">(' . ('Ranking Matches ' . $myRow['Round']) . ')</span>'
						. '</th></tr>' . "\n";
				}
				$html .= '<tr><th colspan="4" style="font-size:20px;">- ' . get_text('Group#','Tournament',$myRow['Group']) . ' -</th></tr>' . "\n"
						. '<tr>'
							. '<th style="width: 5%;font-size:20px;">' . get_text('Target') . '</th>'
							. '<th style="width: 75%;font-size:20px;">' . get_text('Country') . '</th>'
							. '<th style="width: 10%;font-size:20px;">' . get_text('Total') . '</th>'
							. '<th style="width: 10%;font-size:20px;">Tie</td>'
						. '</tr>' . "\n";
			}

			++$PartiDa;

			for ($i=1;$i<=2;++$i)
			{
				$html
					.='<tr>'
						. '<td style="font-size:22px;">' . ($myRow['TargetNo' . $i]!='' ? $myRow['TargetNo' . $i] : '&nbsp;'). '</td>'
						. '<td style="font-size:22px;">' . $myRow['CountryCode' . $i] . ' - ' .  ($myRow['CountryName' . $i]!='' ? $myRow['CountryName' . $i] . (intval($myRow['SubTeamCode' . $i])<=1 ? '' : ' (' . $myRow['SubTeamCode' . $i] .')') : '&nbsp') . '</td>'
						. '<td style=" text-align:right;font-size:22px;">' . $myRow['Score' . $i] . '</td>'
						. '<td style=" text-align:right;font-size:22px;">' . ($myRow['Tie' . $i]==1 ? '*' : ($myRow['Tie' . $i]==2 ? 'Bye' : '&nbsp;')) . '</td>'
					. '</tr>' . "\n";
			}
			$html .= '<tr style="height: 3px;"><td colspan="4"></td></tr>';

			$curGroup=$myRow['Group'];
			$curEvent=$myRow['EventName'];
		}

		$html.='</table>' . "\n";
	}

	if (safe_num_rows($Rs)>=$Righe)
	{
		$NextURL = $_SERVER['PHP_SELF'] . '?PD=' . $PartiDa;
	}
	else
	{
		$NextURL = $_SERVER['PHP_SELF'];
	}

	if(safe_num_rows($Rs)==0)
	{
		header("Location: " . $NextURL);
		exit();
	}

	include('Common/Templates/head-html-rot.php');
	print $html;
	include('Common/Templates/tail-html-rot.php');

?>