<?php
/*
 *
 * TODO: reorder bis targets in the flow for Fields and 3D
 *
 *
 * */
	@define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/CommonLib.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Various.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	CheckTourSession(true);
    checkACL(AclQualification, AclReadWrite);


	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_AJAX_index.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_JS.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-confirm.min.js"></script>',
		'<script type="text/javascript" src="./index.js"></script>',
		'<link href="'.$CFG->ROOT_DIR.'Common/css/jquery-confirm.min.css" media="screen" rel="stylesheet" type="text/css" />',
		'<link href="./index.css" media="screen" rel="stylesheet" type="text/css" />',
		phpVars2js(array(
			'CmdPostUpdate'=>get_text('CmdPostUpdate'),
			'PostUpdating'=>get_text('PostUpdating'),
			'PostUpdateEnd'=>get_text('PostUpdateEnd'),
			'RootDir'=>$CFG->ROOT_DIR.'Qualification/',
			'MsgAreYouSure' => get_text('MsgAreYouSure'),
			'MsgWent2Home' => get_text('Went2Home', 'Tournament'),
			'MsgBackFromHome' => get_text('BackFromHome', 'Tournament'),
			'MsgSetDSQ' => get_text('Set-DSQ', 'Tournament'),
            'MsgUnsetDSQ' => get_text('Unset-DSQ', 'Tournament'),
            'TxtIrmDns' => get_text('DNS', 'Tournament'),
            'TxtIrmDnf' => get_text('DNF', 'Tournament'),
            'TxtIrmUnset' => get_text('CmdUnset', 'Tournament', ''),
            'TxtCancel' => get_text('CmdCancel'),
		)),
	);

	$PAGE_TITLE=get_text('QualRound');

	include('Common/Templates/head.php');

	/*$Select
		= "SELECT ToId,ToNumSession,TtNumDist,TtGolds,TtXNine "
		. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/

	$Select
		= "SELECT ToId,ToNumSession,ToNumDist AS TtNumDist,ToGolds AS TtGolds,ToXNine AS TtXNine "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$RsTour=safe_r_sql($Select);

	$RowTour=NULL;
	$ComboSes='';
	$TxtFrom='';
	$TxtTo='';
	$ComboDist='';
	$ChkG='';
	$ChkX='';
	if (safe_num_rows($RsTour)==1)
	{
		$RowTour=safe_fetch($RsTour);

		$ComboSes = '<select name="x_Session" id="x_Session" onChange="SelectSession();">' . "\n";
		$ComboSes.= '<option value="-1">---</option>' . "\n";

		$ComboDist = '<select name="x_Dist" id="x_Dist">' . "\n";
		$ComboDist.= '<option value="-1">---</option>' . "\n";

		foreach (GetSessions('Q') as $ses)
			$ComboSes.= '<option value="' . $ses->SesOrder . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$ses->SesOrder ? ' selected' : '') . '>' . $ses->Descr. '</option>' . "\n";
		$ComboSes.= '</select>' . "\n";

		for ($i=1;$i<=$RowTour->TtNumDist;++$i)
			$ComboDist.= '<option value="' . $i . '"' . (isset($_REQUEST['x_Dist']) && $_REQUEST['x_Dist']==$i ? ' selected' : '') . '>' . $i . '</option>' . "\n";
		$ComboDist.= '</select>' . "\n";


		$TxtFrom = '<input type="text" name="x_From" id="x_From" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_From']) ? $_REQUEST['x_From'] : '') . '">';
		$TxtTo = '<input type="text" name="x_To" id="x_To" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_To']) ? $_REQUEST['x_To'] : '') . '">';
		$ChkG = '<input type="checkbox" name="x_Gold" id="x_Gold" value="1"' . (isset($_REQUEST['x_Gold']) && $_REQUEST['x_Gold']==1 ? ' checked' : '') . '>';

		//$ChkA = '<input type="checkbox" name="x_Arrows" id="x_Arrows" value="1"' . (isset($_REQUEST['x_Arrows']) && $_REQUEST['x_Arrows']==1 ? ' checked' : '') . '>';

		if(empty($_REQUEST['x_To']) && !empty($_REQUEST['x_From']))
			$_REQUEST['x_To']=$_REQUEST['x_From'];

		if (isset($_REQUEST['x_Arrows']) &&
			$_REQUEST['x_Arrows']==2 &&
			isset($_REQUEST['Command']) &&
			$_REQUEST['Command']=='OK' &&
			$_REQUEST['x_Session']>0 &&
			$_REQUEST['x_Dist']>0 && !IsBlocked(BIT_BLOCK_QUAL))
		{
			$v=0;
			if (isset($_REQUEST['x_AllArrows']) && preg_match('/^[0-9]{1,4}$/i',$_REQUEST['x_AllArrows']))
			{
				$v=	$_REQUEST['x_AllArrows'];

				$TargetFilter = "AND QuTargetNo >='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_From'],TargetNoPadding,'0',STR_PAD_LEFT) . "A' AND QuTargetNo<='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_To'],TargetNoPadding,'0',STR_PAD_LEFT) . "Z' ";

				$Where
					= "WHERE "
						. "EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuSession=" . StrSafe_DB($_REQUEST['x_Session']) . " AND EnStatus<=1 "
						. $TargetFilter . " ";

				$query
					= "UPDATE "
						. "Qualifications INNER JOIN Entries ON EnId=QuId "
					. "SET "
						. "QuD" . $_REQUEST['x_Dist'] ."Hits=" . StrSafe_DB($v) . " "
					. $Where;
				$rs=safe_w_sql($query);

			// somma
				$query
					= "UPDATE "
						. "Qualifications INNER JOIN Entries ON EnId=QuId "
					. "SET "
						. "QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits "
					. $Where;
				//	print $query;
				$rs=safe_w_sql($query);

			// per evitare puttanate riporto a zero la combo delle frecce
				unset($_REQUEST['x_Arrows']);
			}


		}

		$ComboA
			= '<select name="x_Arrows" id="x_Arrows" onChange="CreateArrowsText();">' . "\n"
				. '<option value="0"' . (isset($_REQUEST['x_Arrows']) && $_REQUEST['x_Arrows']==0 ? ' selected' : '') . '>' . get_text('No'). '</option>' . "\n"
				. '<option value="1"' . (isset($_REQUEST['x_Arrows']) && $_REQUEST['x_Arrows']==1 ? ' selected' : '') . '>' . get_text('RowByRow').'</option>' . "\n"
				. '<option value="2">' . get_text('ToAll') . '</option>' . "\n"
			. '</select>' . "\n";
?>
<?php print prepareModalMask('PostUpdateMask','<div align="center" style="font-size: 20px; font-weight: bold;"><br/><br/><br/><br/><br/>'.get_text('PostUpdating').'</div>');?>

<form name="FrmParam" method="POST" action="">
<input type="hidden" name="Command" value="OK">
<input type="hidden" name="xxx" id="Command">
<table class="Tabella">
<TR><TH class="Title" colspan="8"><?php print get_text('QualRound');?></TH></TR>
<TR><Th class="SubTitle" colspan="8"><?php print get_text('ShortTable','Tournament');?></Th></TR>
<tr class="Divider"><TD colspan="8"></TD></tr>
<tr>
<th width="5%"><?php print get_text('Session');?></th>
<th width="8%"><?php print get_text('From','Tournament');?></th>
<th width="8%"><?php print get_text('To','Tournament');?></th>
<th width="5%"><?php print get_text('Distance','Tournament');?></th>
<th width="5%">G/X</th>
<th width="15%"><?php print get_text('Arrows','Tournament');?></th>
<th width="5%">&nbsp;</th>
<th>&nbsp;</th>
</tr>
<tr>
<td class="Center"><?php print $ComboSes; ?></td>
<td class="Center"><?php print $TxtFrom; ?></td>
<td class="Center"><?php print $TxtTo; ?></td>
<td class="Center"><?php print $ComboDist; ?></td>
<td class="Center"><?php print $ChkG; ?></td>
<td class="Center"><?php print $ComboA; ?>&nbsp;&nbsp;<span id="ArrowsToAllText"></span></td>
<td><input type="submit" value="<?php print get_text('CmdOk');?>"></td>
<td>
<a class="Link" href="javascript:MakeTeams();"><?php print get_text('MakeTeams','Tournament'); ?></a>&nbsp;-&nbsp;
<a class="Link" href="javascript:CalcRank(true);"><?php print get_text('CalcRankDist','Tournament'); ?></a>&nbsp;-&nbsp;
<a class="Link" href="javascript:CalcRank(false);"><?php print get_text('CalcRank','Tournament'); ?></a>&nbsp;-&nbsp;
<a class="Link" href="javascript:saveSnapshotImage();"><?php print get_text('CalcSnapshot','Tournament'); ?></a>
</td>
</tr>
<tr class="Divider"><td colspan="8"></td></tr>
<tr><td colspan="8" class="Bold">
	<input type="checkbox" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1"<?php print (isset($_REQUEST['chk_BlockAutoSave']) && $_REQUEST['chk_BlockAutoSave']==1 ? ' checked' : '');?>><?php echo get_text('CmdBlocAutoSave') ?>
	&nbsp;&nbsp;
	<input type="checkbox" name="chk_PostUpdate" id="chk_PostUpdate" value="1"
		<?php print (isset($_REQUEST['chk_PostUpdate']) && $_REQUEST['chk_PostUpdate']==1 ? ' checked' : '');?>
		onclick="ManagePostUpdate(this.checked);"
	/><?php print get_text('CmdPostUpdate');?>
</td></tr>
<tr class="Divider"><td colspan="8" class="Bold">
	<span id="idPostUpdateMessage"></span>
</td></tr>
</table>
</form>
<br>
<?php
		if (isset($_REQUEST['Command']) && $_REQUEST['Command']=='OK' && $_REQUEST['x_Session']!=-1 && $_REQUEST['x_Dist']!=-1)
		{
			if(!empty($_REQUEST['x_Target'])) {
				$TargetFilter = "AND QuTargetNo ='" . $_REQUEST['x_Target'] . "' ";
			} else {
				$TargetFilter = "AND QuTargetNo >='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_From'],TargetNoPadding,'0',STR_PAD_LEFT) . "A' AND QuTargetNo<='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_To'],TargetNoPadding,'0',STR_PAD_LEFT) . "Z' ";
			}

			/*$Select
				= "SELECT EnId,EnCode,EnName,EnFirstName,EnTournament,EnDivision,EnClass,EnCountry,CoCode, (EnStatus <=1) AS EnValid,EnStatus, "
				. "QuTargetNo, SUBSTRING(QuTargetNo,2) AS Target,"
				. "QuD" . $_REQUEST['x_Dist'] . "Score AS SelScore,QuD" . $_REQUEST['x_Dist'] . "Hits AS SelHits,QuD" . $_REQUEST['x_Dist'] . "Gold AS SelGold,QuD" . $_REQUEST['x_Dist'] . "Xnine AS SelXNine, "
				. "QuScore, QuHits,	QuGold,	QuXnine, "
				. "ToId,ToType,TtNumDist "
				. "FROM Entries INNER JOIN Countries ON EnCountry=CoId "
				. "INNER JOIN Qualifications ON EnId=QuId "
				. "RIGHT JOIN AvailableTarget ON QuTargetNo=AtTargetNo AND AtTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "INNER JOIN Tournament ON EnTournament=ToId AND ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "INNER JOIN Tournament*Type ON ToType=TtId "
				. "WHERE EnAthlete=1 AND QuSession<>0 AND QuTargetNo<>'' AND QuSession=" . StrSafe_DB($_REQUEST['x_Session']) . " "
				. $TargetFilter . " "
				. "ORDER BY QuTargetNo ASC ";*/

			$Select = "SELECT EnId,EnCode,EnName,EnFirstName,EnTournament,EnDivision,EnClass,EnCountry,CoCode, (EnStatus <=1) AS EnValid,EnStatus,
                    QuTargetNo, SUBSTRING(QuTargetNo,2) AS Target,
                    QuD" . $_REQUEST['x_Dist'] . "Score AS SelScore,QuD" . $_REQUEST['x_Dist'] . "Hits AS SelHits,QuD" . $_REQUEST['x_Dist'] . "Gold AS SelGold,QuD" . $_REQUEST['x_Dist'] . "Xnine AS SelXNine,
                    QuScore, QuHits,	QuGold,	QuXnine, QuClRank, QuIrmType, IrmType,
                    ToId,ToType,ToNumDist AS TtNumDist
			    FROM Entries
			    INNER JOIN Countries ON EnCountry=CoId
			    INNER JOIN Qualifications ON EnId=QuId
			    INNER JOIN IrmTypes ON IrmId=QuIrmType
			    RIGHT JOIN AvailableTarget ON QuTargetNo=AtTargetNo AND AtTournament=" . StrSafe_DB($_SESSION['TourId']) . "
			    INNER JOIN Tournament ON EnTournament=ToId AND ToId=" . StrSafe_DB($_SESSION['TourId']) . "
			    WHERE EnAthlete=1 AND QuSession<>0 AND QuTargetNo<>'' AND QuSession=" . StrSafe_DB($_REQUEST['x_Session']) . " "
				. $TargetFilter . "
				ORDER BY QuTargetNo ASC ";

			$Rs=safe_r_sql($Select);
			//print $Select;
		// form elenco persone
			if (safe_num_rows($Rs)>0)
			{
?>
<form name="Frm" method="POST" action="">
<table class="Tabella">
<tr>
<td class="Title" width="5%" nowrap="nowrap"><?php echo get_text('IrmStatus', 'Tournament') ?></td>
<td class="Title" width="5%"><?php print get_text('Target');?></td>
<td class="Title" width="5%"><?php print get_text('Code','Tournament');?></td>
<td class="Title" width="20%"><?php print get_text('Archer');?></td>
<td class="Title" width="5%"><?php print get_text('Div');?></td>
<td class="Title" width="5%"><?php print get_text('Cl');?></td>
<td class="Title" width="5%"><?php print get_text('Country');?></td>
<td class="Title" width="5%">Score (<?php print $_REQUEST['x_Dist']; ?>)</td>
<td class="Title" width="5%"><a class="LinkRevert" href="javascript:ChangeGoldXNine('OK');"><?php print $RowTour->TtGolds . ' (' . $_REQUEST['x_Dist'] . ')'; ?></a></td>
<td class="Title" width="5%"><a class="LinkRevert" href="javascript:ChangeGoldXNine('OK');"><?php print $RowTour->TtXNine . ' (' . $_REQUEST['x_Dist'] . ')'; ?></a></td>
<td class="Title" width="5%"><a class="LinkRevert" href="javascript:ChangeArrows('OK');"><?php print get_text('Arrows','Tournament') . ' (' . $_REQUEST['x_Dist'] . ')'; ?></a></td>
<td class="Title" width="5%">Score</td>
<td class="Title" width="5%"><?php print $RowTour->TtGolds; ?></td>
<td class="Title" width="5%"><?php print $RowTour->TtXNine; ?></td>
</tr>
<?php
$CurTarget = 'xx';
$TarStyle='';	// niene oppure warning se $RowStyle==''
// elenco persone
while ($MyRow=safe_fetch($Rs)) {
    $RowStyle='';	// NoShoot oppure niente
    if(!$MyRow->EnValid) {
        $RowStyle='NoShoot';
    }

    if ($CurTarget!='xx') {
        if ($CurTarget!=substr($MyRow->Target,0,-1) ) {
            if ($TarStyle=='') {
                $TarStyle='warning';
            } elseif($TarStyle=='warning') {
                $TarStyle='';
            }
        }
    }

    echo '<tr id="Row_'.$MyRow->EnId.'" class="'.$TarStyle.' '.$RowStyle.' Irm-'.$MyRow->QuIrmType.'">';
    echo '<td class="Center" nowrap="nowrap" id="TD_'.$MyRow->EnId.'">';
    if($MyRow->QuIrmType) {
        echo '<div class="btn" onclick="IrmSet(this)" ref="'.$MyRow->QuIrmType.'">'.get_text('CmdUnset', 'Tournament', $MyRow->IrmType).'</div>';
    } else {
        echo '<div class="btn" onclick="IrmSet(this)" ref="'.$MyRow->QuIrmType.'">'.get_text('CmdSet', 'Tournament').'</div>';
    }

?>
</td>
<td><?php print $MyRow->Target; ?></td>
<td><?php print $MyRow->EnCode; ?></td>
<td><?php print $MyRow->EnFirstName . ' ' . $MyRow->EnName;?></td>
<td class="Center"><?php print $MyRow->EnDivision; ?></td>
<td class="Center"><?php print $MyRow->EnClass; ?></td>
<td><?php print $MyRow->CoCode; ?></td>
<td class="Center"><?php print '<input type="text" size="4" maxlength="5" name="d_QuD' . $_REQUEST['x_Dist'] . 'Score_' . $MyRow->EnId . '" id="d_QuD' . $_REQUEST['x_Dist'] . 'Score_' . $MyRow->EnId . '" value="' . $MyRow->SelScore . '" onBlur="javascript:UpdateQuals(\'d_QuD' . $_REQUEST['x_Dist'] . 'Score_' . $MyRow->EnId . '\');"' . ($MyRow->EnValid ? '' : 'disabled') .'>';?></td>
<td class="Center">
<?php
	if (isset($_REQUEST['x_Gold']) && $_REQUEST['x_Gold']==1)
		print '<input type="text" size="4" maxlength="5" name="d_QuD' . $_REQUEST['x_Dist'] . 'Gold_' . $MyRow->EnId . '" id="d_QuD' . $_REQUEST['x_Dist'] . 'Gold_' . $MyRow->EnId . '" value="' . $MyRow->SelGold . '" onBlur="javascript:UpdateQuals(\'d_QuD' . $_REQUEST['x_Dist'] . 'Gold_' . $MyRow->EnId . '\');"' . ($MyRow->EnValid ? '' : 'disabled') .'>';
	else
		print $MyRow->SelGold;
?>
</td>
<td class="Center">
<?php
	if (isset($_REQUEST['x_Gold']) && $_REQUEST['x_Gold']==1)
		print '<input type="text" size="4" maxlength="5" name="d_QuD' . $_REQUEST['x_Dist'] . 'Xnine_' . $MyRow->EnId . '" id="d_QuD' . $_REQUEST['x_Dist'] . 'Xnine_' . $MyRow->EnId . '" value="' . $MyRow->SelXNine . '" onBlur="javascript:UpdateQuals(\'d_QuD' . $_REQUEST['x_Dist'] . 'Xnine_' . $MyRow->EnId . '\');"' . ($MyRow->EnValid ? '' : 'disabled') .'>';
	else
		print $MyRow->SelXNine;
?>
</td>
<td class="Center">
<?php
	if (isset($_REQUEST['x_Arrows']) && $_REQUEST['x_Arrows']==1)
		print '<input type="text" size="4" maxlength="5" name="d_QuD' . $_REQUEST['x_Dist'] . 'Hits_' . $MyRow->EnId . '" id="d_QuD' . $_REQUEST['x_Dist'] . 'Hits_' . $MyRow->EnId . '" value="' . $MyRow->SelHits . '" onBlur="javascript:UpdateQuals(\'d_QuD' . $_REQUEST['x_Dist'] . 'Hits_' . $MyRow->EnId . '\');"' . ($MyRow->EnValid ? '' : 'disabled') .'>';
	else
		print $MyRow->SelHits;
?>
</td>
<td class="Center Bold" onDblClick="javascript:window.open('WriteScoreCard.php?Command=OK&x_Session=<?php print $_REQUEST['x_Session']; ?>&x_Dist=<?php print $_REQUEST['x_Dist']; ?>&x_Target=<?php print $MyRow->Target; ?>',<?php print $MyRow->EnId; ?>);">
<div id="idScore_<?php print $MyRow->EnId; ?>"><?php print $MyRow->QuScore; ?></div>
</td>
<td class="Center Bold">
<div id="idGold_<?php print $MyRow->EnId; ?>"><?php print $MyRow->QuGold; ?></div>
</td>
<td class="Center Bold">
<div id="idXNine_<?php print $MyRow->EnId; ?>"><?php print $MyRow->QuXnine; ?></div>
</td>
</tr>
<?php
					$CurTarget=	substr($MyRow->Target,0,-1);
				}	// fine elenco persone
?>
</table>
</form>
<?php
			}	// fine form elenco persone
		}
	}
	if(!empty($GoBack)) {
		echo '<table class="Tabella2" width="50%"><tr><th style="background-color:red">
			<a href="'.$GoBack.'" style="color:white">'.get_text('BackBarCodeCheck','Tournament').'</a>
			&nbsp;&nbsp;-&nbsp;&nbsp;
			<a href="'.$GoBack.'&C='.$_GET['B'].'" style="color:white">'.get_text('Confirm','Tournament').'</a>
			</th></tr></table>';
	}
	?>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>
