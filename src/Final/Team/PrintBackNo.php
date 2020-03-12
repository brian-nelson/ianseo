<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);
    checkACL(AclTeams, AclReadOnly);
	require_once('Common/Fun_FormatText.inc.php');

	if(!empty($_FILES) and !empty($_FILES['ImportBackNumbers']['tmp_name']) ) {
		$Bns=unserialize(gzuncompress(implode('',file($_FILES['ImportBackNumbers']['tmp_name']))));
		foreach($Bns as $Bn) {
			unset($Bn->BnTournament);
			unset($Bn->BnFinal);
			$sql="replace into BackNumber set BnTournament={$_SESSION['TourId']}";
			foreach($Bn as $field=>$value) $sql.=", $field=".StrSafe_DB($value);
			$sql.=", BnFinal=2";
			safe_w_sql($sql);
		}
		cd_redirect(basename(__FILE__));
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript">',
		'function DisableChkOther(NoDist, NumDist)',
		'{',
		'	if(NoDist)',
		'	{',
		'		if(document.getElementById(\'ChkDist0\').checked)',
		'		{',
		'			for(i=1; i<=NumDist; i++)',
		'				document.getElementById(\'ChkDist\'+i).checked=false;',
		'		}',
		'	}',
		'	else',
		'	{',
		'		for(i=1; i<=NumDist; i++)',
		'		{',
		'			if(document.getElementById(\'ChkDist\'+i).checked)',
		'				document.getElementById(\'ChkDist0\').checked=false;',
		'		}',
		'	}',
		'}',
		'</script>',
		);

	$PAGE_TITLE=get_text('PrintBackNo','BackNumbers');

	include('Common/Templates/head.php');

	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="2">' . get_text('PrintBackNo','BackNumbers') . '</th></tr>';
//Parametri
	echo '<tr>';
//Pettorali Personali
	echo '<td class="Center" width="60%"><br><a href="PDFBackNumber.php" class="Link" target="PrintOut">';
	echo '<img src="../../Common/Images/pdf.gif" alt="' . get_text('TeamFinal') . '" border="0"><br>';
	echo get_text('TeamFinal');
	echo '</a></td>';
	echo '<td class="Center" width="40%" rowspan="2">';
	$t=safe_r_sql("SELECT * FROM BackNumber WHERE BnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND BnFinal in (0,2) order by BnFinal desc");
	if(safe_num_rows($t)) echo '<img src="../../Tournament/ImgBackNumber.php?IdTpl=2"><br/><br/>';
	echo '<input type="button" value="' . get_text('BackNoEdit', 'BackNumbers') . '" onClick="document.location=\''.$CFG->ROOT_DIR.'Tournament/BackNumber.php?BackNo=2\'">'
	. '<br /><input type="button" value="' . get_text('BackNoExportLayout', 'BackNumbers') . '" onClick="document.location=\''.$CFG->ROOT_DIR.'Tournament/BackNumbersExport.php\'">'
	. '<form id="PrnParameters" action="" method="post" enctype="multipart/form-data"><br /><input type="file" name="ImportBackNumbers" />&nbsp;&nbsp;&nbsp;'
	. '<input name="Submit" type="submit" value="' . get_text('BackNoImportLayout', 'BackNumbers') . '"></form></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td class="Center" width="60%"><div align="center"><br>';
	echo '<table class="Tabella" style="width:80%">';
	echo '<tr>';
	//Eventi
	$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvFinalFirstPhase!=0 ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		$TmpCnt=0;
		while($MyRow=safe_fetch($Rs))
		{
			if($TmpCnt++ % 2 == 0 && $TmpCnt != 1)
				echo '</tr><tr>';
			echo '<td class="Center" width="50%"><a href="PDFBackNumber.php?Event=' .  $MyRow->EvCode . '" class="Link" target="PrintOut">';
			echo '<img src="../../Common/Images/pdf_small.gif" alt="' . $MyRow->EvCode . '" border="0"><br>';
			echo $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true);
			echo '</a></td>';
		}
		if($TmpCnt % 2 != 0)
			echo '<td>&nbsp;</td>';
		safe_free_result($Rs);
	}
	echo '</tr>';
	echo '</table>';
	echo '</div>&nbsp;</td>';
	echo '</tr>';
	echo '</table>';

	include('Common/Templates/tail.php');
?>