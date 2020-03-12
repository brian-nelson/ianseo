<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclAccreditation, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');

	if (!isset($_REQUEST['bib'])) {
		printcrackerror();
	}

	$Turni = "";
/*
	Elenco dei turni per la query
*/
	foreach ($_SESSION['chk_Turni'] as $Value)
	{
		$Turni .= StrSafe_DB($Value) . ",";
	}

	$Turni=substr($Turni,0,-1);

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		'',
		'',
		);

	include('Common/Templates/head-popup.php');

?>
<table class="Tabella">
<tr>
<th width="7%"><?php print get_text('Code','Tournament');?></th>
<th width="3%"><?php print get_text('Session');?></th>
<th width="5%"><?php print get_text('Target');?></th>
<th width="30%"><?php print get_text('Archer');?></th>
<th width="25%"><?php print get_text('Country');?></th>
<th width="5%"><?php print get_text('Division');?></th>
<th width="5%"><?php print get_text('Class');?></th>
<th width="5%"><?php print get_text('IndClEvent', 'Tournament');?></th>
<th width="5%"><?php print get_text('TeamClEvent', 'Tournament');?></th>
<th width="5%"><?php print get_text('IndFinEvent', 'Tournament');?></th>
<th width="5%"><?php print get_text('TeamFinEvent', 'Tournament');?></th>
</tr>
<?php
	$Select
		= "Select EnId,EnTournament,EnDivision,EnClass,EnCountry,CoCode,CoName,EnCode,EnName,EnFirstName,"
		. "EnIndClEvent,EnTeamClEvent,EnIndFEvent,EnTeamFEvent,QuSession,SUBSTRING(QuTargetNo,2) As TargetNo, "
		. "AEOperation "
		. "FROM Entries LEFT JOIN Countries ON EnCountry=CoId "
		. "INNER JOIN Qualifications ON EnId=QuId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "LEFT JOIN AccEntries ON EnId=AEId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AEOperation=" . StrSafe_DB($_SESSION['AccOp']) . " "
		. "LEFT JOIN AccOperationType ON AEOperation=AOTId "
		. "WHERE EnAthlete='1' AND QuSession IN (" . $Turni . ") AND EnCode=" . StrSafe_DB($_REQUEST['bib']) . " "
		. "ORDER BY QuSession ASC, TargetNo ASC, EnFirstName ASC , EnName ASC , CoCode ASC ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			print '<tr' . (is_null($MyRow->AEOperation) ? ' class="warning"' : '') . '>';
			print '<td><a class="Link" href="WriteOp.php?Id=' . $MyRow->EnId . '">' . $MyRow->EnCode . '</a></td>';
			print '<td class="Center">' . $MyRow->QuSession . '</td>';
			print '<td class="Center">' . $MyRow->TargetNo . '</td>';
			print '<td>' . $MyRow->EnFirstName . ' ' . $MyRow->EnName . '</td>';
			print '<td>' . $MyRow->CoCode . ' - ' . $MyRow->CoName . '</td>';
			print '<td class="Center">' . $MyRow->EnDivision . '</td>';
			print '<td class="Center">' . $MyRow->EnClass . '</td>';
			print '<td class="Center">' . ($MyRow->EnIndClEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
			print '<td class="Center">' . ($MyRow->EnTeamClEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
			print '<td class="Center">' . ($MyRow->EnIndFEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
			print '<td class="Center">' . ($MyRow->EnTeamFEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
			print '</tr>' . "\n";
		}
	}
?>
</table>
<?php
	include('Common/Templates/tail-popup.php');
?>