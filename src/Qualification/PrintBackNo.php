<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_Sessions.inc.php');
CheckTourSession(true);
$lvl = checkACL(AclQualification, AclReadOnly);
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/CommonLib.php');

if($_POST) {
	if(!empty($_FILES) and !empty($_FILES['ImportBackNumbers']['tmp_name']) ) {
		$Bns=unserialize(gzuncompress(implode('',file($_FILES['ImportBackNumbers']['tmp_name']))));
		foreach($Bns as $Bn) {
			unset($Bn->BnTournament);
			unset($Bn->BnFinal);
			$sql="replace into BackNumber set BnTournament={$_SESSION['TourId']}";
			foreach($Bn as $field=>$value) $sql.=", $field=".StrSafe_DB($value);
			$sql.=", BnFinal=0";
			safe_w_sql($sql);
		}
		cd_redirect(basename(__FILE__));
	} elseif(!empty($_REQUEST['x_Session']) and $_REQUEST['x_Session']!='-1') {
		if(empty($_REQUEST['ConfirmPrint'])) {
			require_once('./PDFBackNumber.php');
			die();
		} elseif(empty($_REQUEST["BackNoDraw"]) or $_REQUEST['BackNoDraw']!="Test") {
			// confirming the printouts
			$From=str_pad(intval($_REQUEST['x_From']), 3, '0', STR_PAD_LEFT);
			$To=str_pad(intval($_REQUEST['x_To']), 3, '0', STR_PAD_LEFT);
			$MyQuery = "update Qualifications
				INNER JOIN Entries ON QuId=EnId AND EnAthlete=1
				set QuBacknoPrinted='".date('Y-m-d H:i:s')."'
				WHERE EnTournament =  {$_SESSION['TourId']}
				AND QuTargetNo>='" . $_REQUEST['x_Session'] . $From . "A' AND QuTargetNo<='" . $_REQUEST['x_Session'] . $To . "Z' ";
			safe_w_sql($MyQuery);
			cd_redirect(basename(__FILE__));
		}
	}
}



	$JS_SCRIPT=array(
		phpVars2js(array('MsgAreYouSure' => get_text('MsgAreYouSure'))),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_AJAX_index.js"></script>',
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

	$PAGE_TITLE=get_text('PrintBackNo','BackNumbers');

	include('Common/Templates/head.php');

	$RowTour=NULL;
	/*$Select
		= "SELECT ToId,TtNumDist,TtElabTeam "
		. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/

	$Select
		= "SELECT ToId,ToNumDist AS TtNumDist,ToElabTeam AS TtElabTeam "
		. "FROM Tournament  "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

	$RsTour=safe_r_sql($Select);
	if (safe_num_rows($RsTour)==1)
	{
		$RowTour=safe_fetch($RsTour);
		safe_free_result($RsTour);
	}

	echo '<form id="PrnParameters" action="" method="post" target="PrintOut" enctype="multipart/form-data">';
	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="2">' . get_text('PrintBackNo','BackNumbers') . '</th></tr>';
//Parametri
	echo '<tr>';
//Tipo di Score
	echo '<td width="50%"><br>';
	echo '<input name="BackNoDraw" type="radio" value="Complete" checked>&nbsp;' . get_text('BackNoComplete', 'BackNumbers') . '<br>';
	echo '<input name="BackNoDraw" type="radio" value="Test">&nbsp;' . get_text('BackNoTest', 'BackNumbers') . '<br><br>';
	echo '<input type="checkbox" value="1" name="PrintEmpty">' . get_text('BackNoPrintEmpty', 'BackNumbers') . '<br>';
	echo '<input type="checkbox" value="1" name="SkipPrinted">' . get_text('SkipPrinted', 'BackNumbers') . '<br>';
	echo '</td>';
//Header e Immagini
// immagine fittizia del pettorale
	echo '<td width="50%" align="center"><br/>';
	echo '<img src="../Tournament/ImgBackNumber.php"><br/><br/>';
	if($lvl == AclReadWrite) {
        echo '<input type="button" value="' . get_text('BackNoEdit', 'BackNumbers') . '" onClick="document.location=\'' . $CFG->ROOT_DIR . 'Tournament/BackNumber.php?BackNo=0\'">';
        echo '<br />';
        echo '<input type="button" value="' . get_text('BackNoExportLayout', 'BackNumbers') . '" onClick="document.location=\'' . $CFG->ROOT_DIR . 'Tournament/BackNumbersExport.php\'">';
        echo '<br />';
        echo '<input type="file" name="ImportBackNumbers" />&nbsp;&nbsp;&nbsp;';
        echo '<input type="submit" value="' . get_text('BackNoImportLayout', 'BackNumbers') . '" onClick="document.location=\'' . $CFG->ROOT_DIR . 'Tournament/BackNumber.php?BackNo=0\'">';
    }
	echo '</td>';
	echo '</tr>';


	$ComboSes='';
	$TxtFrom='';
	$TxtTo='';
	$ComboDist='';
	$ChkG='';
	$ChkX='';
	if($RowTour != NULL)
	{
//Sessioni
		$sessions=GetSessions('Q');
		echo '<tr><th class="SubTitle" colspan="2">' . get_text('Session')  . '</th></tr>';
		echo '<tr>';
		echo '<td colspan="2" align="Center"><br>';
		echo '<input type="hidden" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1">';
		echo get_text('Session') . '&nbsp;<select name="x_Session" id="x_Session" onChange="javascript:SelectSession();hide_confirm();">' . "\n";
		echo '<option value="-1">---</option>' . "\n";
		foreach ($sessions as $s)
		{
			echo '<option value="' . $s->SesOrder . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$s->SesOrder ? ' selected' : '') . '>' . $s->Descr . '</option>' . "\n";
		}
		echo '</select>' . "\n";

		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo  get_text('From','Tournament') . '&nbsp;<input type="text" name="x_From" id="x_From" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_From']) ? $_REQUEST['x_From'] : '') . '" onChange="javascript:hide_confirm();">';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo  get_text('To','Tournament') . '&nbsp;<input type="text" name="x_To" id="x_To" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_To']) ? $_REQUEST['x_To'] : '') . '" onChange="javascript:hide_confirm();">';
		echo '</td>';
		echo '</tr>';
	}
	echo '<tr>';
	echo '<td colspan="2" align="Center">';
	echo '<input name="ConfirmPrint" type="hidden" value="">';

	echo '<br/><input name="Submit" type="submit" value="' . get_text('PrintBackNo','BackNumbers') . '" onclick="if(this.form.ImportBackNumbers.value>\'\') this.form.target=\'\';activate_confirm();"><br/>&nbsp;';
	echo '<br/><input name="Submit" type="submit"  style="display:none;" id="confirm_button" value="' . get_text('BackNoConfirmPrinted','BackNumbers') . '" onclick="this.form.target=\'\'; this.form.ConfirmPrint.value=1;"><br/>&nbsp;';
	echo '</td>';
	echo '</tr>';
	echo '<tr class="Divider"><td  colspan="3"></td></tr>';




	echo '</table>';
	echo '</form>';

	echo '<script>
		function activate_confirm() {
			document.getElementById(\'confirm_button\').style.display=\'inline\';
		}

		function hide_confirm() {
			document.getElementById(\'confirm_button\').style.display=\'none\';
		}
		</script>';

	include('Common/Templates/tail.php');
?>