<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');

	CheckTourSession(true,'popup');
    checkACL(AclAccreditation, AclReadWrite);

	if (!(isset($_SESSION['chk_Turni']) && is_array($_SESSION['chk_Turni']) &&
		isset($_SESSION['AccOp']) && is_numeric($_SESSION['AccOp'])))
	{
		header('Location: ./');
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
		$OpDescr=get_text($Row->AOTDescr, 'Tournament');
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Accreditation/Fun_JS.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		);

	$ONLOAD=' onload="setTimeout(function() {
		window.print();
		}, 50/*ms*/)"';

	include('Common/Templates/head-popup.php');

	$Select
		= "SELECT EnId,EnTournament,EnDivision,EnClass,EnCountry,CoCode,CoName,EnCode,EnName,EnFirstName,EnPays,APPrice,ToCurrency "
		. "FROM "
		. "Entries INNER JOIN AccEntries ON EnId=AEId AND EnTournament=AETournament AND EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " "
		. "INNER JOIN AccPrice ON CONCAT(EnDivision,EnClass) LIKE APDivClass AND APTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND AERapp='1' "
		. "LEFT JOIN Countries ON EnCountry=CoId "
		. "LEFT JOIN Tournament ON ToId=EnTournament "
		. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "  AND AEOperation=" . StrSafe_DB($_SESSION['AccOp']) . " AND AEFromIP=INET_ATON(" . StrSafe_DB(($_SERVER['REMOTE_ADDR']=="::1" ? "127.0.0.1": $_SERVER['REMOTE_ADDR'])) .") "
		. "ORDER BY EnFirstName ASC , EnName ASC, CoCode ASC ";
	//print $Select;
	$Rs=safe_r_sql($Select);


	?>
<table class="Tabella">
<tr>
<th valign="top" align="left" colspan="6" class="Bold"><table width="100%">
<tr>
<td><?php if(file_exists($CFG->DOCUMENT_PATH.($img='TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg'))) echo '<img src="'.$CFG->ROOT_DIR.$img.'" height="120">'; ?></td>
<td class="Center" style="width:100%;font-size:150%;text-align:center;"><?php echo $_SESSION['TourName'] . '<br/>' . $_SESSION['TourWhere'] . '<br/>' . get_text('From','Tournament') . ' ' . $_SESSION['TourWhenFrom'] . ' ' . get_text('To','Tournament') . ' ' . $_SESSION['TourWhenTo'] . ''; ?></td>
<td><?php if(file_exists($CFG->DOCUMENT_PATH.($img='TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg'))) echo '<img src="'.$CFG->ROOT_DIR.$img.'" height="120">'; ?></td>
</tr>
</table>
</th>
</tr>
<tr><th class="Title" colspan="6"><?php print get_text('CmdDetailsBill','Tournament');?></th></tr>
<tr>
<th ><?php print get_text('Code','Tournament');?></th>
<th><?php print get_text('Archer');?></th>
<th ><?php print get_text('Country');?></th>
<th ><?php print get_text('Division');?></th>
<th ><?php print get_text('Class');?></th>
<th ><?php print get_text('Price','Tournament');?></th>
</tr>
<?php
	if (safe_num_rows($Rs))
	{
		$cur='';
		$Tot=0;
		while ($MyRow=safe_fetch($Rs))
		{
			if ($cur=='')
				$cur= $MyRow->ToCurrency;
			print '<tr>';
			print '<td>' . $MyRow->EnCode . '</td>';
			print '<td>' . $MyRow->EnFirstName . ' ' . $MyRow->EnName . '</td>';
			print '<td>' . $MyRow->CoCode . ' - ' . $MyRow->CoName . '</td>';
			print '<td class="Center">' . $MyRow->EnDivision . '</td>';
			print '<td class="Center">' . $MyRow->EnClass . '</td>';
			print '<td class="Right">' . ($MyRow->EnPays==1 ? NumFormat($MyRow->APPrice,2) : NumFormat(0,2)) . '&nbsp;' . $MyRow->ToCurrency . '</td>';
			print '</tr>' . "\n";
			$Tot+=($MyRow->EnPays==1 ? $MyRow->APPrice : 0);
		}
		print '<tr>';
		print '<td class="Bold" colspan="2">' . $_SESSION['TourWhere'] . ', ' . date( get_text('DateFmt')) . '</td>';
		print '<td class="Bold Right" colspan="3">' . get_text('Total') . '</td>';
		print '<td class="Bold Right">' . NumFormat($Tot,2) . '&nbsp;' . $cur . '</td>';
		print '</tr>' . "\n";
	}
?>
<tr class="Divider"><td colspan="11"></td></tr>
<tr><td class="Center" colspan="11"><input class="noPrint" type="button" value="<?php echo get_text('Close') ?>" onClick="javascript:window.close();"></td></tr>
<tr><td class="Center" colspan="11"><input class="noPrint" type="button" value="<?php echo get_text('CmdPrintBill', 'Tournament') ?>" onClick="javascript:window.print();"></td></tr>
</table>
<?php include('Common/Templates/tail-popup.php'); ?>