<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclEliminations, AclReadOnly);
	require_once('Common/Fun_FormatText.inc.php');

	if(!empty($_FILES) and !empty($_FILES['ImportBackNumbers']['tmp_name']) ) {
		$Bns=unserialize(gzuncompress(implode('',file($_FILES['ImportBackNumbers']['tmp_name']))));
		foreach($Bns as $Bn) {
			unset($Bn->BnTournament);
			$sql="replace into BackNumber set BnTournament={$_SESSION['TourId']}";
			foreach($Bn as $field=>$value) $sql.=", $field=".StrSafe_DB($value);
			safe_w_sql($sql);
		}
		cd_redirect(basename(__FILE__));
	}

	$Out='';
	$OutLeft='';
	$OutRight='';

	$MySql = "SELECT EvCode, EvEventName,EvElim1,EvElim2 FROM Events WHERE EvTeamEvent='0' AND (EvElim1>0 OR EvElim2>0) AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);

	$Left=array();
	$Right=array();

	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			if ($MyRow->EvElim1!=0)
				$Left[$MyRow->EvCode]=get_text($MyRow->EvEventName,'','',true);

			if ($MyRow->EvElim2!=0)
				$Right[$MyRow->EvCode]=get_text($MyRow->EvEventName,'','',true);
		}
	}

	if (count($Left)>0)
	{
		$OutLeft
			.='<td style="width:50%;">'
				. '<table class="Tabella">'
					. '<tr><th colspan="2">' . get_text('Eliminations_1') . '</th></tr>' . "\n"
					. '<tr>'
						. '<td class="Center" colspan="2">'
							. '<a class="Link" target="PrintOut" href="PDFBackNumber.php?Elim=0&amp;BackNo=3">'
								. '<img src="../Common/Images/pdf.gif" alt="' . get_text('PrintBackNo','BackNumbers') . '" border="0"><br/>' . get_text('PrintBackNo','BackNumbers')
							. '</a>'
						. '</td>'
					.'</tr>' . "\n";

		$OutLeft.= '<tr>';
		$TmpCnt=0;
		foreach ($Left as $ee=>$dd)
		{
			if($TmpCnt % 2 == 0 && $TmpCnt!=0)
				$OutLeft.='</tr><tr>';

			$OutLeft
				.='<td class="Center" width="50%">'
					. '<a href="PDFBackNumber.php?Elim=0&amp;BackNo=3&amp;Event=' .  $ee . '" class="Link" target="PrintOut">'
						. '<img src="../Common/Images/pdf_small.gif" alt="' . $ee . '" border="0"><br>'.  $ee . ' - ' . $dd
					. '</a>'
				. '</td>';
			++$TmpCnt;
		}
		if($TmpCnt % 2 != 0)
				$OutLeft.='<td>&nbsp;</td>';
			$OutLeft.='</tr>' . "\n";

		$t=safe_r_sql("SELECT * FROM BackNumber WHERE BnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND BnFinal in (0,3) order by BnFinal desc");
		$OutLeft.='<tr><td class="Center" colspan="2">'
						. (safe_num_rows($t) ? '<br/><img src="../Tournament/ImgBackNumber.php?IdTpl=3"><br/><br/>':'')
						. '<input type="button" value="' . get_text('BackNoEdit', 'BackNumbers') .'" onClick="document.location=\''.$CFG->ROOT_DIR.'Tournament/BackNumber.php?BackNo=3\'">'
						. '<br /><input type="button" value="' . get_text('BackNoExportLayout', 'BackNumbers') . '" onClick="document.location=\''.$CFG->ROOT_DIR.'Tournament/BackNumbersExport.php\'">'
						. '<form id="PrnParameters" action="" method="post" enctype="multipart/form-data"><br /><input type="file" name="ImportBackNumbers" />&nbsp;&nbsp;&nbsp;'
						. '<input type="submit" value="' . get_text('BackNoImportLayout', 'BackNumbers') . '"></form>'
					. '</td></tr>'
				. '</table>' . "\n"
			. '</td>';
	}

	if (count($Right)>0)
	{
		$OutRight
			.='<td style="width:50%;">'
				. '<table class="Tabella">'
					. '<tr><th colspan="2">' . get_text('Eliminations_2') . '</th></tr>' . "\n"
					. '<tr>'
						. '<td class="Center" colspan="2">'
							. '<a class="Link" target="PrintOut" href="PDFBackNumber.php?Elim=1&amp;BackNo=4">'
								. '<img src="../Common/Images/pdf.gif" alt="' . get_text('PrintBackNo','BackNumbers') . '" border="0"><br/>' . get_text('PrintBackNo','BackNumbers')
							. '</a>'
						. '</td>'
					. '</tr>' . "\n";

		$OutRight.= '<tr>';
		$TmpCnt=0;
		foreach ($Right as $ee=>$dd)
		{
			if($TmpCnt % 2 == 0 && $TmpCnt!=0)
				$OutRight.='</tr><tr>';

			$OutRight
				.='<td class="Center" width="50%">'
					. '<a href="PDFBackNumber.php?Elim=1&amp;BackNo=4&amp;Event=' .  $ee . '" class="Link" target="PrintOut">'
						. '<img src="../Common/Images/pdf_small.gif" alt="' . $ee . '" border="0"><br>'.  $ee . ' - ' . $dd
					. '</a>'
				. '</td>';
			++$TmpCnt;
		}
		if($TmpCnt % 2 != 0)
				$OutRight.='<td>&nbsp;</td>';
			$OutRight.='</tr>' . "\n";

		$t=safe_r_sql("SELECT * FROM BackNumber WHERE BnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND BnFinal in (0,4) order by BnFinal desc");
		$OutRight.='<tr><td class="Center" colspan="2">'
						. (safe_num_rows($t) ? '<br/><img src="../Tournament/ImgBackNumber.php?IdTpl=4"><br/><br/>':'')
						. '<input type="button" value="' . get_text('BackNoEdit', 'BackNumbers') . '" onClick="document.location=\''.$CFG->ROOT_DIR.'Tournament/BackNumber.php?BackNo=4\'">'
						. '<br /><input type="button" value="' . get_text('BackNoExportLayout', 'BackNumbers') . '" onClick="document.location=\''.$CFG->ROOT_DIR.'Tournament/BackNumbersExport.php\'">'
						. '<form id="PrnParameters" action="" method="post" enctype="multipart/form-data"><br /><input type="file" name="ImportBackNumbers" />&nbsp;&nbsp;&nbsp;'
						. '<input name="Submit" type="submit" value="' . get_text('BackNoImportLayout', 'BackNumbers') . '"></form>'
					. '</td></tr>' . "\n"
				. '</table>'
			. '</td>';
	}

	include('Common/Templates/head.php');
?>
<table class="Tabella">
	<tr><th class="Title" colspan="2"><?php print get_text('PrintBackNo','BackNumbers'); ?></th></tr>
	<?php
		print '<tr>';
			print $OutLeft;
			print $OutRight;
		print '</tr>' . "\n";
	?>
</table>
<?php
	include('Common/Templates/tail.php');
?>