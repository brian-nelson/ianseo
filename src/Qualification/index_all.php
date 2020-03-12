<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclQualification, AclReadWrite);
	require_once('Common/Lib/CommonLib.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_Various.inc.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_AJAX_index.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_JS.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
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
		. "FROM Tournament  "
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

		$ComboSes = '<select name="x_Session" id="x_Session" onChange="javascript:SelectSession();">' . "\n";
		$ComboSes.= '<option value="-1">---</option>' . "\n";

		$ComboDist = '<select name="x_Dist" id="x_Dist">' . "\n";
		$ComboDist.= '<option value="-1">' . get_text('AllDistances','Tournament') . '</option>' . "\n";

		for ($i=1;$i<=$RowTour->ToNumSession;++$i)
			$ComboSes.= '<option value="' . $i . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$i ? ' selected' : '') . '>' . $i . '</option>' . "\n";
		$ComboSes.= '</select>' . "\n";

		for ($i=1;$i<=$RowTour->TtNumDist;++$i)
			$ComboDist.= '<option value="' . $i . '"' . (isset($_REQUEST['x_Dist']) && $_REQUEST['x_Dist']==$i ? ' selected' : '') . '>' . $i . '</option>' . "\n";
		$ComboDist.= '</select>' . "\n";


		$TxtFrom = '<input type="text" name="x_From" id="x_From" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_From']) ? $_REQUEST['x_From'] : '') . '">';
		$TxtTo = '<input type="text" name="x_To" id="x_To" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_To']) ? $_REQUEST['x_To'] : '') . '">';
		$ChkG = '<input type="checkbox" name="x_Gold" id="x_Gold" value="1"' . (isset($_REQUEST['x_Gold']) && $_REQUEST['x_Gold']==1 ? ' checked' : '') . '>';
		//$ChkX = '<input type="checkbox" name="x_XNine" id="x_XNine" value="1"' . (isset($_REQUEST['x_XNine']) && $_REQUEST['x_XNine']==1 ? ' checked' : '') . '>';
?>
<?php print prepareModalMask('PostUpdateMask','<div align="center" style="font-size: 20px; font-weight: bold;"><br/><br/><br/><br/><br/>'.get_text('PostUpdating').'</div>');?>

<form name="FrmParam" method="POST" action="">
<input type="hidden" name="Command" value="OK">
<input type="hidden" name="xxx" id="Command">
<table class="Tabella">
<TR><TH class="Title" colspan="7"><?php print get_text('QualRound');?></TH></TR>
<TR><Th class="SubTitle" colspan="7"><?php print get_text('LongTable','Tournament');?></Th></TR>
<tr class="Divider"><TD colspan="7"></TD></tr>
<tr>
<th width="5%"><?php print get_text('Session');?></th>
<th width="8%"><?php print get_text('From','Tournament');?></th>
<th width="8%"><?php print get_text('To','Tournament');?></th>
<th width="5%"><?php print get_text('Distance','Tournament');?></th>
<th width="5%">G/X</th>
<!--<th width="5%">X</th>-->
<th width="5%">&nbsp;</th>
<th>&nbsp;</th>
</tr>
<tr>
<td class="Center"><?php print $ComboSes; ?></td>
<td class="Center"><?php print $TxtFrom; ?></td>
<td class="Center"><?php print $TxtTo; ?></td>
<td class="Center"><?php print $ComboDist; ?></td>
<td class="Center"><?php print $ChkG; ?></td>
<!--<td class="Center"><?php //print $ChkX; ?></td>-->
<td><input type="submit" value="<?php print get_text('CmdOk');?>"></td>
<td>
<a class="Link" href="javascript:MakeTeams();"><?php print get_text('MakeTeams','Tournament'); ?></a>&nbsp;-&nbsp;
<a class="Link" href="javascript:CalcRank(true);"><?php print get_text('CalcRankDist','Tournament'); ?></a>&nbsp;-&nbsp;
<a class="Link" href="javascript:CalcRank(false);"><?php print get_text('CalcRank','Tournament'); ?></a>
</td>
</tr>
<tr class="Divider"><td colspan="7"></td></tr>
<tr><td colspan="7" class="Bold">
	<input type="checkbox" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1"<?php print (isset($_REQUEST['chk_BlockAutoSave']) && $_REQUEST['chk_BlockAutoSave']==1 ? ' checked' : '');?>><?php echo get_text('CmdBlocAutoSave') ?>
	&nbsp;&nbsp;
	<input type="checkbox" name="chk_PostUpdate" id="chk_PostUpdate" value="1"
		<?php print (isset($_REQUEST['chk_PostUpdate']) && $_REQUEST['chk_PostUpdate']==1 ? ' checked' : '');?>
		onclick="ManagePostUpdate(this.checked);"
	/><?php print get_text('CmdPostUpdate');?>
</td></tr>
<tr class="Divider"><td colspan="7">
	<span id="idPostUpdateMessage"></span>
</td></tr>
</table>
</form>
<br>
<?php
		if (isset($_REQUEST['Command']) && $_REQUEST['Command']=='OK' && $_REQUEST['x_Session']!=-1)
		{
			if(empty($_REQUEST['x_To']) && !empty($_REQUEST['x_From']))
				$_REQUEST['x_To']=$_REQUEST['x_From'];
			$TargetFilter = "AND QuTargetNo >='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_From'],TargetNoPadding,'0',STR_PAD_LEFT) . "A' AND QuTargetNo<='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_To'],TargetNoPadding,'0',STR_PAD_LEFT) . "Z' ";

			/*$Select
				= "SELECT EnId,EnCode,EnName,EnFirstName,EnTournament,EnDivision,EnClass,EnCountry,CoCode, (EnStatus <=1) AS EnValid, "
				. "QuTargetNo, SUBSTRING(QuTargetNo,2) AS Target, ";
			for ($i=1;$i<=$RowTour->TtNumDist;++$i)
				$Select
					.= "QuD" . $i . "Score, "
					 . "QuD" . $i . "Hits, "
					 . "QuD" . $i . "Gold, "
					 . "QuD" . $i . "XNine, " ;
			$Select
				.= "QuScore,QuHits,	QuGold,	QuXnine, "
				 . "ToId,ToType,TtNumDist "
				 . "FROM Entries INNER JOIN Countries ON EnCountry=CoId "
				 . "INNER JOIN Qualifications ON EnId=QuId "
				 . "RIGHT JOIN AvailableTarget ON QuTargetNo=AtTargetNo AND AtTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				 . "INNER JOIN Tournament ON EnTournament=ToId AND ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
				 . "INNER JOIN Tournament*Type ON ToType=TtId "
				 . "WHERE EnAthlete=1 AND QuSession<>0 AND QuTargetNo<>'' AND QuSession=" . StrSafe_DB($_REQUEST['x_Session']) . " "
				 . $TargetFilter . " "
				 . "ORDER BY QuTargetNo ASC ";*/

			$Select
				= "SELECT EnId,EnCode,EnName,EnFirstName,EnTournament,EnDivision,EnClass,EnCountry,CoCode, (EnStatus <=1) AS EnValid, "
				. "QuTargetNo, SUBSTRING(QuTargetNo,2) AS Target, QuClRank, ";
			for ($i=1;$i<=$RowTour->TtNumDist;++$i)
				$Select
					.= "QuD" . $i . "Score, "
					 . "QuD" . $i . "Hits, "
					 . "QuD" . $i . "Gold, "
					 . "QuD" . $i . "XNine, " ;
			$Select
				.= "QuScore,QuHits,	QuGold,	QuXnine, "
				 . "ToId,ToType,ToNumDist AS TtNumDist "
				 . "FROM Entries INNER JOIN Countries ON EnCountry=CoId "
				 . "INNER JOIN Qualifications ON EnId=QuId "
				 . "RIGHT JOIN AvailableTarget ON QuTargetNo=AtTargetNo AND AtTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				 . "INNER JOIN Tournament ON EnTournament=ToId AND ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
				 . "WHERE EnAthlete=1 AND QuSession<>0 AND QuTargetNo<>'' AND QuSession=" . StrSafe_DB($_REQUEST['x_Session']) . " "
				 . $TargetFilter . " "
				 . "ORDER BY QuTargetNo ASC ";

			if (debug)
				print $Select . '<br>';
			$Rs=safe_r_sql($Select);

		// form elenco persone
			if (safe_num_rows($Rs))
			{
?>
<form name="Frm" method="POST" action="">
<table class="Tabella">
<tr>
<td class="Title" width="5%"><?php echo get_text('Went2Home', 'Tournament') ?></td>
<td class="Title" width="5%"><?php print get_text('Target');?></td>
<td class="Title" width="5%"><?php print get_text('Code','Tournament');?></td>
<td class="Title" width="15%"><?php print get_text('Archer');?></td>
<td class="Title" width="5%"><?php print get_text('Div');?></td>
<td class="Title" width="5%"><?php print get_text('Cl');?></td>
<td class="Title" width="5%"><?php print get_text('Country');?></td>
<?php
				$PercPunti = NumFormat(45/($RowTour->TtNumDist+5));

				for ($i=1;$i<=$RowTour->TtNumDist;++$i)
				{
					print '<td class="Title" width="' . $PercPunti . '%"><a class="LinkRevert" href="javascript:ChangeDist(' . $i . ',\'OK\');">Score (' . $i . ')</a></td>';
					if ($i==$_REQUEST['x_Dist'] || $_REQUEST['x_Dist']==-1)
					{
						print '<td class="Title" width="' . $PercPunti . '%"><a class="LinkRevert" href="javascript:ChangeGoldXNine(\'OK\');">' . $RowTour->TtGolds . ' (' . $i . ')</a></td>';
						print '<td class="Title" width="' . $PercPunti . '%"><a class="LinkRevert" href="javascript:ChangeGoldXNine(\'OK\');">' . $RowTour->TtXNine . ' (' . $i . ')</a></td>';
					}
				}
?>
<td class="Title" width="<?php print $PercPunti;?>%">Score</td>
<td class="Title" width="<?php print $PercPunti;?>%"><?php print $RowTour->TtGolds; ?></td>
<td class="Title" width="<?php print $PercPunti;?>%"><?php print $RowTour->TtXNine; ?></td>
</tr>
<?php
				$CurTarget = 'xx';
				$TarStyle='';	// niene oppure warning se $RowStyle==''
			// elenco persone
				while ($MyRow=safe_fetch($Rs))
				{
                    $RowStyle='';	// NoShoot oppure niente
					if($MyRow->QuClRank=='9999') {
						$RowStyle='Dsq';
					} elseif(!$MyRow->EnValid) {
						$RowStyle='NoShoot';
					}

					//if ($RowStyle=='')
					//{
						if ($CurTarget!='xx')
						{
							if ($CurTarget!=substr($MyRow->Target,0,-1) )
							{
								if ($TarStyle=='')
									$TarStyle='warning';
								elseif($TarStyle=='warning')
									$TarStyle='';
							}
						}
					//}
?>
<tr id="Row_<?php print $MyRow->EnId; ?>" <?php echo 'class="' . ($RowStyle!='' ? $RowStyle : $TarStyle) . '"'; ?>>
<td class="Center" id="TD_<?php print $MyRow->EnId; ?>">
<?php

if ($MyRow->QuClRank != '9999') {
	echo '<div onClick="Went2Home(' . $MyRow->EnId . ')" id="Went2Home_' . $MyRow->EnId . '">';
	if ($MyRow->EnValid) {
		echo get_text('Went2Home', 'Tournament');
	} else {
		echo get_text('BackFromHome', 'Tournament');
	}
	echo '</div>';

	echo '<div onClick="Disqualify(' . $MyRow->EnId . ')" id="Disqualify_' . $MyRow->EnId . '">';
	print get_text('Set-DSQ', 'Tournament');
	echo '</div>';
} else {
	echo '<div onClick="Disqualify(' . $MyRow->EnId . ')" id="Disqualify_' . $MyRow->EnId . '">';
	print get_text('Unset-DSQ', 'Tournament');
	echo '</div>';
}

?>
</td>
<td><?php print $MyRow->Target; ?></td>
<td><?php print $MyRow->EnCode; ?></td>
<td><?php print $MyRow->EnFirstName . ' ' . $MyRow->EnName; ?></td>
<td class="Center"><?php print $MyRow->EnDivision; ?></td>
<td class="Center"><?php print $MyRow->EnClass; ?></td>
<td><?php print $MyRow->CoCode; ?></td>
<?php
					for ($i=1;$i<=$RowTour->TtNumDist;++$i)
					{
						print '<td class="Center">';
						if ($i==$_REQUEST['x_Dist'] || $_REQUEST['x_Dist']==-1)
						{
							print '<input type="text" size="4" maxlength="4" name="d_QuD' . $i . 'Score_' . $MyRow->EnId . '" id="d_QuD' . $i . 'Score_' . $MyRow->EnId . '" value="' . $MyRow->{'QuD' . $i . 'Score'} . '" onBlur="javascript:UpdateQuals(\'d_QuD' . $i . 'Score_' . $MyRow->EnId . '\');"' . ($MyRow->EnValid ? '' : 'disabled') .'>';
						}
						else
						{
							print $MyRow->{'QuD' . $i .'Score'};
						}
						print '</td>';

						if ($i==$_REQUEST['x_Dist'] || $_REQUEST['x_Dist']==-1)
						{
							print '<td class="Center">';
							if (isset($_REQUEST['x_Gold']) && $_REQUEST['x_Gold']==1)
							{
								print '<input type="text" size="4" maxlength="4" name="d_QuD' . $i . 'Gold_' . $MyRow->EnId . '" id="d_QuD' . $i . 'Gold_' . $MyRow->EnId . '" value="' . $MyRow->{'QuD' . $i . 'Gold'} . '" onBlur="javascript:UpdateQuals(\'d_QuD' . $i . 'Gold_' . $MyRow->EnId . '\');"' . ($MyRow->EnValid ? '' : 'disabled') .'>';
							}
							else
							{
								print $MyRow->{'QuD' . $i . 'Gold'};
							}
							print '</td>';

							print '<td class="Center">';
							if (isset($_REQUEST['x_Gold']) && $_REQUEST['x_Gold']==1)
							{
								print '<input type="text" size="4" maxlength="4" name="d_QuD' . $i . 'Xnine_' . $MyRow->EnId . '" id="d_QuD' . $i . 'Xnine_' . $MyRow->EnId . '" value="' . $MyRow->{'QuD' . $i . 'XNine'} . '" onBlur="javascript:UpdateQuals(\'d_QuD' . $i . 'Xnine_' . $MyRow->EnId . '\');"' . ($MyRow->EnValid ? '' : 'disabled') .'>';
							}
							else
							{
								print $MyRow->{'QuD' . $i . 'XNine'};
							}
							print '</td>';
						}
					}
?>
<td class="Center Bold">
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
?>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>