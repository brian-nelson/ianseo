<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclQualification, AclReadOnly);
	require_once('Common/Fun_FormatText.inc.php');


	$Code=(isset($_REQUEST['Code']) ? $_REQUEST['Code'] : '');
	$FirstName=(isset($_REQUEST['FirstName']) ? $_REQUEST['FirstName'] : '');
	$Name=(isset($_REQUEST['Name']) ? $_REQUEST['Name'] : '');

	$Command=(isset($_REQUEST['Command']) ? $_REQUEST['Command'] : null);

	$SelArcher='';

	if (!is_null($Command))
	{
		if ($Command=='OK')
		{
		/*
		 * Cerco l'id del tizio.
		 */
			$Filter='';
		// se c'è il codice faccio la query su quello altrimenti sul cognome e nome
			if ($Code!='')
			{
				$Filter= "AND EnCode=" . StrSafe_DB($Code) . " ";
			}
			else
			{
				if ($FirstName!='')
					$Filter.= "AND EnFirstName LIKE " . Strsafe_DB("%" . $FirstName . "%") . " ";

				if ($Name!='')
					$Filter.= "AND EnName LIKE " . Strsafe_DB("%" . $Name . "%") . " ";
			}

			if ($Filter!='')
			{
				$Query
					= "SELECT "
						. "EnId,EnCode,EnFirstName,EnName,"
						. "EnDivision,EnClass,"
						. "CoCode,CoName "
					. "FROM "
						. "Entries "
						. "INNER JOIN "
							. "Countries "
						. "ON EnCountry=CoId AND EnTournament=CoTournament "
					. "WHERE "
						. "EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
						. $Filter . " "
					. "ORDER BY "
						. "EnFirstName,EnName ";
	//print $Query;exit;
				$Rs=safe_r_sql($Query);

			/*
			 * Se c'è una riga sola tiro fuori l'id e ridireziono subito verso l'altra pagina
			 * altrimenti creo l'output per selezionare la persona giusta
			 */
				$x=safe_num_rows($Rs);

				if ($x==1)
				{
					$MyRow=safe_fetch($Rs);
					header('Location: RankPersonal2.php?Id=' . $MyRow->EnId);
					exit;
				}
				elseif ($x>1)
				{
					$SelArcher
						= '<table class="Tabella">' . "\n"
							. '<tr>'
								. '<th>' . get_text('Code','Tournament') . '</th>'
								. '<th>' . get_text('Archer') . '</th>'
								. '<th>' . get_text('Country') . '</th>'
								. '<th>' . get_text('Division') . '</th>'
								. '<th>' . get_text('Class') . '</th>'
							. '</tr>';
					while ($MyRow=safe_fetch($Rs))
					{
						$SelArcher
							.='<tr>'
								. '<td>' . $MyRow->EnCode . '</td>'
								. '<td>'
									. '<a class="Link" href="RankPersonal2.php?Id=' . $MyRow->EnId . '">'
										. $MyRow->EnFirstName . ' ' . $MyRow->EnName
									. '</a>'
								. '</td>'
								. '<td>' . $MyRow->CoCode . ' - ' . $MyRow->CoName . '</td>'
								. '<td>' . $MyRow->EnDivision . '</td>'
								. '<td>' . $MyRow->EnClass . '</td>'
							. '</tr>';
					}
					$SelArcher.='</table>' . "\n";
				}
				else
				{
					$SelArcher
						= '<table class="Tabella">'
							. '<tr>'
								. '<th>' . get_text('ArcherNotFound','Tournament') . '</th>'
							. '</tr>'
						. '</table>' . "\n";
				}
			}
		}
	}


	$PAGE_TITLE=get_text('PersonalRank', 'Tournament');

	include('Common/Templates/head.php');
?>
<form name="Frm" method="post" action="<?php print $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="Command" value="OK">
	<table class="Tabella">
		<tr><th class="Title" colspan="3"><?php print get_text('PersonalRank','Tournament'); ?></th></tr>
		<tr class="Spacer"><td class="Divider" colspan="3"></td></tr>
		<tr>
			<th><?php print get_text('Code','Tournament'); ?></th>
			<th><?php print get_text('FamilyName','Tournament'); ?></th>
			<th><?php print get_text('Name','Tournament'); ?></th>
		</tr>
		<tr>
			<td class="Center"><input type="text" name="Code" value="<?php print $Code; ?>"></td>
			<td class="Center"><input type="text" name="FirstName" value="<?php print $FirstName; ?>"></td>
			<td class="Center"><input type="text" name="Name" value="<?php print $Name; ?>"></td>
		</tr>
		<tr>
			<td class="Center" colspan="3">
				<input type="submit" value="<?php print get_text('CmdOk'); ?>">
				&nbsp;&nbsp;
				<input type="reset" value="<?php print get_text('CmdCancel'); ?>">
			</td>
		</tr>
	</table>
</form>
<br/>
<?php
	print $SelArcher;
?>

<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>