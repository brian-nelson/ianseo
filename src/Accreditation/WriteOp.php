<?php
//XXX Sarebbe il caso di avvisare nel caso non siano settate div o cl o clg della persona
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');


	if (!isset($_REQUEST['Id']) && !isset($_REQUEST['bib']))
	{
		printcrackerror();
	}

	if (!(isset($_SESSION['chk_Turni']) && is_array($_SESSION['chk_Turni']) &&
		isset($_SESSION['AccOp']) && is_numeric($_SESSION['AccOp'])))
	{
		header('Location: index.php');
		exit;
	}

	$OpDescr = '';
	$Select
		= "SELECT AOTDescr "
		. "FROM AccOperationType "
		. "WHERE AOTId=" . StrSafe_DB($_SESSION['AccOp']) . " ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)==1)
	{
		$Row=safe_fetch($Rs);
		$OpDescr=get_text($Row->AOTDescr,'Tournament');
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

	$Id = -1;
	$bib = '';
	$NoArcher=false;
	$BibIsId = false;

	if (isset($_REQUEST['Id']))
	{
		$Id = $_REQUEST['Id'];
	}
	else	// analizzo il bib
	{
	// se inizia con $ tento di interpretare la matricola come id
		if (substr($_REQUEST['bib'],0,1)=='$')
		{
			$Id=substr($_REQUEST['bib'],1);
			$BibIsId=true;
		}
		elseif (preg_match('/^[0-9]{5}.{1}[0-9]{3}$/i',stripslashes($_REQUEST['bib'])))	// proviene dal fitarco pass
		{
			$tmp=substr(stripslashes($_REQUEST['bib']),0,5);
			$bib='';
			for ($i=0;$i<strlen($tmp);++$i)
			{
				if ($tmp[$i]!='0') break;

			}
			$bib.=substr($tmp,$i);

		}
		else	// colpo secco
		{
			$tmp=stripslashes($_REQUEST['bib']);

			if (is_numeric($tmp))
			{
				$bib=ltrim($tmp,'0');
			}
			else
			{
				$bib=$tmp;
			}
			//print $bib;exit;
		}

		if (!$BibIsId)
		{
		// se ho una sola matricola, setto il suo Id, altrimenti mando alla pagina di scelta
			$Select
				= "SELECT EnId FROM Entries INNER JOIN Qualifications ON EnId=QuId "
				. "WHERE EnCode=" . StrSafe_DB($bib) . " AND QuSession IN (" . $Turni . ") AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			//print $Select;exit;
			$RsSel =safe_r_sql($Select);

			if ($RsSel)
			{
				if (safe_num_rows($RsSel)==1)	// ok
				{
					$row=safe_fetch($RsSel);
					$Id=$row->EnId;
				}
				elseif (safe_num_rows($RsSel)>1)
				{
					header('Location: SelArcher.php?bib=' . $bib);
					exit;
				}
				/*else
					$NoArcher=true;*/
			}
			else
				exit;
		}
	}

// vale 1 se il conto è aperto
	$SetRap = (isset($_SESSION['SetRap']) ? $_SESSION['SetRap'] : 0);

	$RicaricaOpener=false;
	if (!IsBlocked(BIT_BLOCK_ACCREDITATION))
	{
		if (isset($_REQUEST['Command']))
		{
			if ($_REQUEST['Command']=='EXEC')
			{
				require_once('./Lib.php');
				$RicaricaOpener=SetAccreditation($Id, $SetRap);

			}
		}
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		'<script type="text/javascript" src="../Common/Fun_JS.inc.js"></script>',
		'',
		'',
		);
	$ONLOAD=($RicaricaOpener ? ' onLoad="javascript:ReloadOpener(true);"' : ' onLoad="javascript:document.Frm.Submit.focus();"');
	include('Common/Templates/head-popup.php');

?>
<form name="Frm" method="POST" action="">
<input type="hidden" name="Id" id="Id" value="<?php print $Id;?>">
<input type="hidden" name="Command" value="">
<?php
	$Select
		= "Select EnId,EnTournament,EnDivision,EnClass,EnCountry,CoCode,CoName,EnCode,EnName,EnFirstName,EnStatus,"
		. "EnIndClEvent,EnTeamClEvent,EnIndFEvent,EnTeamFEvent,EnTeamMixEvent,QuSession,SUBSTRING(QuTargetNo,2) As TargetNo, "
		. "AEOperation "
		. "FROM Entries LEFT JOIN Countries ON EnCountry=CoId "
		. "left JOIN Qualifications ON EnId=QuId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "LEFT JOIN AccEntries ON EnId=AEId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AEOperation=" . StrSafe_DB($_SESSION['AccOp']) . " "
		. "LEFT JOIN AccOperationType ON AEOperation=AOTId "
		. "WHERE /* EnAthlete='1' AND */ QuSession IN (" . $Turni . ") AND EnId=" . StrSafe_DB($Id) . " "
		. "ORDER BY QuSession ASC, TargetNo ASC, EnFirstName ASC , EnName ASC , CoCode ASC ";
	$Rs=safe_r_sql($Select);

	//print $Select;
	$MyRow=NULL;
	$NoAtleta = false;

	if (safe_num_rows($Rs)==1)
	{
		$MyRow=safe_fetch($Rs);
?>
<table class="Tabella">
<tr><th class="Title" colspan="12"><?php print $OpDescr;?></th></tr>
<tr>
<th width="7%"><?php print get_text('Code','Tournament');?></th>
<th width="3%"><?php print get_text('Session');?></th>
<th width="5%"><?php print get_text('Target');?></th>
<th width="28%"><?php print get_text('Archer');?></th>
<th width="22%"><?php print get_text('Country');?></th>
<th width="5%"><?php print get_text('Division');?></th>
<th width="5%"><?php print get_text('Class');?></th>
<th width="5%"><?php print get_text('IndClEvent', 'Tournament');?></th>
<th width="5%"><?php print get_text('TeamClEvent', 'Tournament');?></th>
<th width="5%"><?php print get_text('IndFinEvent', 'Tournament');?></th>
<th width="5%"><?php print get_text('TeamFinEvent', 'Tournament');?></th>
<th width="5%"><?php print get_text('MixedTeamFinEvent', 'Tournament');?></th>
<?php
		$RowStyle='';
		switch ($MyRow->EnStatus)
		{
			case '0':
				$RowStyle = '';
				break;
			case '1':
				$RowStyle = 'CanShoot';
				break;
			case '8':
				$RowStyle = 'CouldShoot';
				break;
			case '9':
				$RowStyle = 'NoShoot';
				break;
		}
		print '<tr class="' . $RowStyle . '">';
		print '<td>' . $MyRow->EnCode . '</td>';
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
		print '<td class="Center">' . ($MyRow->EnTeamMixEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
		print '</tr>' . "\n";
		print '<tr>';
		print '<td colspan="12" class="Center Bold">' . (!is_null($MyRow->AEOperation) ? get_text('Credited','Tournament') : ($MyRow->EnStatus!=7 ? '&nbsp;' : get_text('NoAcc','Tournament'))) . '</td>';
		print '</tr>' . "\n";
		if (is_null($MyRow->AEOperation) && $MyRow->EnStatus!=7)
		{
			if ($MyRow->EnStatus<7)
				print '<tr><td colspan="12" class="Center"><input type="submit" name="Submit" value="' . get_text('CmdExec','Tournament') . '" onclick="document.Frm.Command.value=\'EXEC\'"></td></tr>' . "\n";
			else
			{
				print '<tr><td colspan="12" class="Bold">' . get_text('Status_'.$MyRow->EnStatus) . '</td></tr>' . "\n";
				print '<tr><td colspan="12" class="Center"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?Command=EXEC&Id=' . $Id . '">' . get_text('CmdExec','Tournament') . '</a></td></tr>' . "\n";
			}
		}
		else
			print '<tr><td colspan="12" class="Center"><input type="button" name="Submit" value="' . get_text('Close') . '" onclick="javascript:window.close();"></td></tr>' . "\n";
?>
</table>
<?php
	}
	else
	{
		print '<table class="Tabella">' . "\n";
		print '<tr>';
		print '<td colspan="12" class="Center Bold">' . get_text('ArcherNotFound','Tournament') . '</td>';
		print '</tr>' . "\n";
		print '<tr><td colspan="12" class="Center"><input type="button" name="Submit" value="' . get_text('Close') . '" onClick="javascript:window.close();"></td></tr>' . "\n";
		print '</table>' . "\n";
	}
?>

</form>
<?php
	include('Common/Templates/tail-popup.php');
?>