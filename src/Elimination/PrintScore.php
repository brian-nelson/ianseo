<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclEliminations, AclReadOnly);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Modules.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="../Qualification/Fun_AJAX_index.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
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
		'',
		'}',
		'</script>',
		);

	include('Common/Templates/head.php');

	$RowTour=NULL;
	/*$Select
		= "SELECT ToId,TtNumDist,TtElabTeam "
		. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/
	$Select
		= "SELECT ToId,ToNumDist AS TtNumDist,ToElabTeam AS TtElabTeam "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$RsTour=safe_r_sql($Select);
	if (safe_num_rows($RsTour)==1)
	{
		$RowTour=safe_fetch($RsTour);
		safe_free_result($RsTour);
	}

	echo '<form id="PrnParameters" action="../Qualification/PDFScore.php" method="post" target="PrintOut">';
    echo '<input name="SessionType" type="hidden" value="E" >';
	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="2">' . get_text('PrintScore','Tournament')  . '</th></tr>';
	echo '<tr><th class="SubTitle" colspan="2">' . get_text('ScorePrintMode','Tournament')  . '</th></tr>';
//Parametri
	echo '<tr>';
//Tipo di Score
	echo '<td width="50%"><br>';
	echo '<input name="ScoreDraw" type="radio" value="Complete" checked>&nbsp;' . get_text('ScoreComplete','Tournament') . '<br>';
	echo '<input name="ScoreDraw" type="radio" value="Data">&nbsp;' . get_text('ScoreData','Tournament') . '<br>';
	echo '<input name="ScoreDraw" type="radio" value="Draw">&nbsp;' . get_text('ScoreDrawing') . '<br>';
	echo '</td>';
//Header e Immagini
	echo '<td width="50%"><br>';
	echo '<input name="ScoreHeader" type="checkbox" value="1" checked>&nbsp;' . get_text('ScoreTournament','Tournament') . '<br>';
	echo '<input name="ScoreLogos" type="checkbox" value="1" checked>&nbsp;' . get_text('ScoreLogos','Tournament') . '<br>';
	if(module_exists("Barcodes")) {
		echo '<input name="ScoreBarcode" type="checkbox" checked value="1" >&nbsp;' . get_text('ScoreBarcode','Tournament') . '<br>';
	}
    foreach(AvailableApis() as $Api) {
        if(!($tmp=getModuleParameter($Api, 'Mode')) || $tmp=='live' ) {
            continue;
        }
        echo '<input name="QRCode[]" type="checkbox" '.($tmp=='pro' ? '' : 'checked="checked"').' value="'.$Api.'" >&nbsp;' . get_text($Api.'-QRCode','Api') . '<br>';
    }
	echo '</td>';
	echo '</tr>';

	$ComboPhase='';
	$ComboSes='';
	$TxtFrom='';
	$TxtTo='';
	$ComboDist='';
	$ChkG='';
	$ChkX='';
	if($RowTour != NULL)
	{
//fasi eliminatorie
		echo '<tr><th class="SubTitle" colspan="2">' . get_text('Phase')  . '</th></tr>';
		echo '<tr>';
		echo '<td colspan="2" align="Center"><br>';
		echo '<input type="hidden" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1">';
		echo get_text('Session') . '&nbsp;<select name="x_ElimSession" id="x_ElimSession">' . "\n";
		echo '<option value="0">---</option>' . "\n";
		$Select = "Select SesOrder, SesName FROM Session WHERE SesTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND SesType='E' ORDER BY SesOrder";
		$Rs=safe_r_sql($Select);
		while($row=safe_fetch($Rs))
			echo '<option value="' . $row->SesOrder . '"' . (isset($_REQUEST['x_ElimSession']) && $_REQUEST['x_ElimSession']==$row->SesOrder ? ' selected' : '') . '>' . $row->SesOrder . (!empty($row->SesName) ? " - " . $row->SesName : "") . '</option>' . "\n";
		echo '</select>' . "\n";

		echo get_text('Phase') . '&nbsp;<select name="x_Session" id="x_Session" onChange="javascript:SelectSession();">' . "\n";
		echo '<option value="-1">---</option>' . "\n";
		for ($i=0;$i<=1;++$i)
			echo '<option value="' . $i . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$i ? ' selected' : '') . '>' . ($i+1) . '</option>' . "\n";
		echo '</select>' . "\n";

		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo  get_text('From','Tournament') . '&nbsp;<input type="text" name="x_From" id="x_From" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_From']) ? $_REQUEST['x_From'] : '') . '">';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo  get_text('To','Tournament') . '&nbsp;<input type="text" name="x_To" id="x_To" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_To']) ? $_REQUEST['x_To'] : '') . '">';
		echo '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td colspan="2" align="Center"><br>';
		echo '<input id="ScoreFilled" name="ScoreFilled" type="checkbox" value="1" >' . get_text('ScoreFilled');
		echo '</td>';
		echo '</tr>';
	}
	echo '<tr>';
	echo '<td colspan="2" align="Center"><br>';
	echo '<input name="Submit" type="submit" value="' . get_text('PrintScore','Tournament') . '"><br>&nbsp;';
	echo '</td>';
	echo '</tr>';
	echo '<tr class="Divider"><td  colspan="3"></td></tr>';




	echo '</table>';
	echo '</form>';
	echo '<br/>';

	//Bigliettini
	echo '<form name="frmTick" method="post" action="PrnGetScore.php" target="PrintOut">' . "\n";
	echo '<input type="hidden" name ="x_ElimSession" id="xx_ElimSession" value="">';
	echo '<input type="hidden" name ="x_Session" id="xx_Session" value="">';
	echo '<input type="hidden" name="x_From" id="xx_From" value="">';
	echo '<input type="hidden" name="x_To" id="xx_To" value="">';
	echo '<table class="Tabella">' . "\n";
	echo '<tr><th class="SubTitle" colspan="2">' . get_text('TicketGetScore', 'Tournament')  . '</th></tr>';
		echo '<tr>';
				echo '<td colspan="2" align="Center"><br>';
				//echo '<a href="PrnGetScore.php" target="PrintOut" class="Link">' .  get_text('Print', 'Tournament') . '</a>&nbsp;';
				echo '<input type="button" onclick="submitTicket();" value="' . get_text('Print', 'Tournament') .'">';
			echo '</td>';
		echo '</tr>';
	echo '</table>' . "\n";
	echo '</form>' . "\n";
?>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>
