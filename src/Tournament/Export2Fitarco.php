<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
    checkACL(AclCompetition, AclReadOnly);

	define('hide_ExportAndSend',true);	// true per nascondere il botton ExportAndSend

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Code='';

// Tiro fuori il codice gara
	$Select
		= "SELECT ToCode FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)==1)
	{
		$MyRow=safe_fetch($Rs);
		$Code=$MyRow->ToCode;
	}

// Cerco gli eventi delle finali
	$FinEventInd=0;
	$FinEventTeam=0;
	$Select
		= "SELECT COUNT(EvCode) AS Quanti,EvTeamEvent FROM Events "

		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . "  AND EvShootOff=1 "
		. "GROUP BY EvTeamEvent ";
	$Rs=safe_r_sql($Select);

	if ($Rs)
	{
		while ($RowEv=safe_fetch($Rs))
		{
			if ($RowEv->EvTeamEvent=='0')
				$FinEventInd=$RowEv->Quanti;
			elseif($RowEv->EvTeamEvent=='1')
				$FinEventTeam=$RowEv->Quanti;
		}
	}

// Cerco gli eventi delle eliminatorie
	$ElimEvent=array(1=>0,2=>0);

	for ($i=1;$i<=2;++$i)
	{
		$Select
			= "SELECT EvCode FROM Events "
			. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvElim" . $i . ">0 AND EvE" . $i . "ShootOff=1 ";
		$Rs=safe_r_sql($Select);
		//print $Select . '<br>';
		if ($Rs && safe_num_rows($Rs)>0)
		{
			$ElimEvent[$i]=safe_num_rows($Rs);
		}
	}


	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		);

	$PAGE_TITLE=get_text('Export2Fitarco', 'Tournament');

	include('Common/Templates/head.php');

?>
<div align="center">
<div class="medium">
<table class="Tabella">
<tr><th class="Title"><?php print get_text('Export2Fitarco','Tournament'); ?></th></tr>
<tr class="Spacer"><td></td></tr>

<tr><th class="SubTitle">EXP</th></tr>
<tr><td><a class="Link" href="ExportExp.php"><?php print $Code . '.exp'; ?></a></td></tr>
<tr class="Spacer"><td></td></tr>
<tr><th class="Title"><?php print get_text('Export2Fitarco','Tournament'); ?></th></tr>
<tr><th class="SubTitle">ASC</th></tr>
<tr><td><a class="Link" href="../Tournament/ExportASC.php"><?php print $Code . '.asc'; ?></a></td></tr>
<tr><th class="SubTitle">LST</th></tr>
<tr><td><a class="Link" href="../Qualification/LST_Individual.php"><?php print $Code . '.lst'; ?></a></td></tr>
<tr><td><a class="Link" href="../Qualification/LST_Team.php"><?php print $Code . '_team.lst'; ?></a></td></tr>
<?php
	if ($FinEventInd>0)
	{
?>
<tr><td><a class="Link" href="../Final/Individual/LST_Individual.php"><?php print $Code . '_rank.lst'; ?></a></td></tr>
<?php
	}
	if ($FinEventTeam>0)
	{
?>
<tr><td><a class="Link" href="../Final/Team/LST_Team.php"><?php print $Code . '_rank_team.lst'; ?></a></td></tr>
<?php
	}
?>
<tr><th class="SubTitle">PDF</th></tr>
<tr><td><a class="Link" href="../Qualification/PrnIndividual.php?ToFitarco=<?php print $Code . '.pdf'; ?>"><?php print $Code . '.pdf'; ?></a></td></tr>
<tr><td><a class="Link" href="../Qualification/PrnTeam.php?ToFitarco=<?php print $Code . '_team.pdf'; ?>"><?php print $Code . '_team.pdf'; ?></a></td></tr>

<?php
	if ($FinEventInd>0)
	{
?>
<tr><td><a class="Link" href="../Qualification/PrnIndividualAbs.php?ToFitarco=<?php print $Code . '_abs.pdf'; ?>"><?php print $Code . '_abs.pdf'; ?></a></td></tr>
<?php
	}
?>
<?php
	if ($FinEventTeam>0)
	{
?>
<tr><td><a class="Link" href="../Qualification/PrnTeamAbs.php?ToFitarco=<?php print $Code . '_abs_team.pdf'; ?>"><?php print $Code . '_abs_team.pdf'; ?></a></td></tr>
<?php
	}
?>
<?php
	if ($ElimEvent[1]>0 || $ElimEvent[2]>0)
	{
?>
<tr><td><a class="Link" href="/Elimination/PrnIndividual.php"><?php print $Code . '_elim.pdf'; ?></a></td></tr>
<?php
	}
?>

<?php
	if ($FinEventInd>0)
	{
?>
<tr><td><a class="Link" href="../Final/Individual/PrnRanking.php?ToFitarco=<?php print $Code . '_rank.pdf'; ?>"><?php print $Code . '_rank.pdf'; ?></a></td></tr>
<tr><td><a class="Link" href="../Final/Individual/PrnBracket.php?ToFitarco=<?php print $Code . '_grid.pdf'; ?>"><?php print $Code . '_grid.pdf'; ?></a></td></tr>
<?php
	}
	if ($FinEventTeam>0)
	{
?>
<tr><td><a class="Link" href="../Final/Team/PrnRanking.php?ToFitarco=<?php print $Code . '_rank_team.pdf'; ?>"><?php print $Code . '_rank_team.pdf'; ?></a></td></tr>
<tr><td><a class="Link" href="../Final/Team/PrnBracket.php?ToFitarco=<?php print $Code . '_grid_team.pdf'; ?>"><?php print $Code . '_grid_team.pdf'; ?></a></td></tr>
<?php
	}

	if(hide_ExportAndSend)
		print '<!--';
?>
<tr class="Spacer"><td></td></tr>
<tr><td class="Center"><input type="button" value="<?php print get_text('ExportAndSend','Tournament'); ?>" onClick="javascript:OpenPopup('ExportAndSend.php?Code=<?php print $Code; ?>&Ind=<?php print $FinEventInd; ?>&Team=<?php print $FinEventTeam; ?>','ExportAndSend',800,600);"></td></tr>
<?php
	if(hide_ExportAndSend)
		print '-->';
?>
</table>
</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>