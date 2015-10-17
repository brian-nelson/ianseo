<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	if (BlockExperimental) printcrackerror(false,'Blocked');

	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Various.inc.php');



	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_AJAX_index.js.php"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_AJAX_WriteArrows.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/Fun_JS.inc.js"></script>',
		phpVars2js(array(
			'CmdPostUpdate'=>get_text('CmdPostUpdate'),
			'PostUpdating'=>get_text('PostUpdating'),
			'PostUpdateEnd'=>get_text('PostUpdateEnd'),
			'RootDir'=>$CFG->ROOT_DIR.'Qualification/',
		)),
	);

	$PAGE_TITLE=get_text('QualRound');

	include('Common/Templates/head.php');

	/*$Select
		= "SELECT ToId,ToNumSession,TtGolds,TtXNine,TtNumDist,(TtMaxDistScore/TtGolds) AS MaxArrows "
		. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/

	$Select
		= "SELECT ToId,ToNumSession,ToGolds AS TtGolds,ToXNine AS TtXNine,ToNumDist AS TtNumDist,(ToMaxDistScore/ToGolds) AS MaxArrows "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$RsTour=safe_r_sql($Select);

	$RowTour=NULL;
	$ComboSes='';
	$TxtFrom='';
	$TxtTo='';
	$ComboDist='';

	$TxtArrows = '';
	$TxtVolee = '';

	if (safe_num_rows($RsTour)==1)
	{
		$RowTour=safe_fetch($RsTour);

		$ComboSes = '<select name="x_Session" id="x_Session" onChange="javascript:SelectSession();">' . "\n";
		$ComboSes.= '<option value="-1">---</option>' . "\n";

		$ComboDist = '<select name="x_Dist" id="x_Dist">' . "\n";
		$ComboDist.= '<option value="-1">---</option>' . "\n";

		for ($i=1;$i<=$RowTour->ToNumSession;++$i)
			$ComboSes.= '<option value="' . $i . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$i ? ' selected' : '') . '>' . $i . '</option>' . "\n";
		$ComboSes.= '</select>' . "\n";

		for ($i=1;$i<=$RowTour->TtNumDist;++$i)
			$ComboDist.= '<option value="' . $i . '"' . (isset($_REQUEST['x_Dist']) && $_REQUEST['x_Dist']==$i ? ' selected' : '') . '>' . $i . '</option>' . "\n";
		$ComboDist.= '</select>' . "\n";


		$TxtFrom = '<input type="text" name="x_From" id="x_From" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_From']) ? $_REQUEST['x_From'] : '') . '">';
		$TxtTo = '<input type="text" name="x_To" id="x_To" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_To']) ? $_REQUEST['x_To'] : '') . '">';

		$TxtArrows = '<input type="text" name="x_Arrows" id="x_Arrows" size="3" maxlength="3" value="' . (isset($_REQUEST['x_Arrows']) ? $_REQUEST['x_Arrows'] : '') . '">';
		$TxtVolee = '<input type="text" name="x_Volee" id="x_Volee" size="3" maxlength="3" value="' . (isset($_REQUEST['x_Volee']) ? $_REQUEST['x_Volee'] : '') . '">';

?>
<?php print prepareModalMask('PostUpdateMask','<div align="center" style="font-size: 20px; font-weight: bold;"><br/><br/><br/><br/><br/>'.get_text('PostUpdating').'</div>');?>

<form name="FrmParam" method="POST" action="">
<input type="hidden" name="xxx" id="Command">
<input type="hidden" name="Command" value="OK">
<table class="Tabella">
<TR><TH class="Title" colspan="8"><?php print get_text('QualRound');?></TH></TR>
<TR><Th class="SubTitle" colspan="8"><?php print get_text('SingleArrow','Tournament');?></Th></TR>
<tr class="Divider"><TD colspan="8"></TD></tr>
<tr>
<th width="5%"><?php print get_text('Session');?></th>
<th width="8%"><?php echo get_text('From','Tournament') ?></th>
<th width="8%"><?php echo get_text('To','Tournament') ?></th>
<th width="5%"><?php echo get_text('Distance','Tournament') ?></th>
<th width="5%"><?php echo get_text('End (volee)') ?></th>
<th width="5%"><?php echo get_text('Arrows','Tournament') ?></th>
<!--<th width="5%">X</th>-->
<th width="5%">&nbsp;</th>
<th>&nbsp;</th>
</tr>
<tr>
<td class="Center"><?php print $ComboSes; ?></td>
<td class="Center"><?php print $TxtFrom; ?></td>
<td class="Center"><?php print $TxtTo; ?></td>
<td class="Center"><?php print $ComboDist; ?></td>
<td class="Center"><?php print $TxtVolee; ?></td>
<td class="Center"><?php print $TxtArrows; ?></td>
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
		onclick="ManagePostUpdateArrow(this.checked);"
	/><?php print get_text('CmdPostUpdate');?>
</td></tr>
<tr class="Divider"><td colspan="8">
	<span id="idPostUpdateMessage"></span>
</td></tr>
</table>
</form>
<br>
<?php
		if(empty($_REQUEST['x_To']) && !empty($_REQUEST['x_From']))
			$_REQUEST['x_To']=$_REQUEST['x_From'];
		if (isset($_REQUEST['Command']) && $_REQUEST['Command']=='OK' &&
			$_REQUEST['x_Session']!=-1 && $_REQUEST['x_Dist']!=-1 &&
			is_numeric($_REQUEST['x_Arrows']) && $_REQUEST['x_Arrows']>0 &&
			is_numeric($_REQUEST['x_Volee']) && $_REQUEST['x_Volee']>0)
		{

		/*
		 * Se Sforo dalla lunghezza massima non faccio nulla.
		 * La lunghezza max è $RowTour->MaxArrows; per decidere la porzione di arrowstring da gestire uso:
		 * Ip: V*(A-1)
		 * Fp: Ip+(A-1)
		 * con Ip,Fp rispettivamente Inizio Porzione e Fine Porzione e V e A rispettivamente
		 * la volee scelta e il numero di frecce.
		 * L'indice max è  $RowTour->MaxArrows-1 e sforo se Ip>max || Fp>max
		 * I conti sono con vettori 0-based (mysql è 1-based per le stringhe)
		 */
			$Ip = $_REQUEST['x_Arrows']*($_REQUEST['x_Volee']-1);
			$Fp = $Ip+($_REQUEST['x_Arrows']-1);

			// Get max arrows from Distances...
			$t=safe_r_sql("select DiEnds*DiArrows as MaxArrows from DistanceInformation where DiTournament={$_SESSION['TourId']} and DiSession={$_REQUEST['x_Session']} and DiDistance={$_REQUEST['x_Dist']} and DiType='Q'");
			if($u=safe_fetch($t)) $RowTour->MaxArrows=$u->MaxArrows;


		//	print $Ip . ' - ' . $Fp;

			if (($Ip<=$RowTour->MaxArrows-1) && ($Fp<=$RowTour->MaxArrows-1))
			{

				$TargetFilter = "AND QuTargetNo >='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_From'],TargetNoPadding,'0',STR_PAD_LEFT) . "A' AND QuTargetNo<='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_To'],TargetNoPadding,'0',STR_PAD_LEFT) . "Z' ";

				$ScoreTH = '';	// header dello score
				$ScoreTD = '';	// td dello score

				for ($i=$Ip; $i<=$Fp;++$i)
				{
					$ScoreTH.='<td class="Title">(' . ($i+1) . ')</td>';
					/*$ScoreTD
						.='<td class="Center">'
						. '<input type="text" id="" size="2" maxlength="2" value="">'
						. '</td>';*/
				}

				/*$Select
					= "SELECT EnId,EnCode,EnName,EnFirstName,EnTournament,EnDivision,EnClass,EnCountry,CoCode, (EnStatus <=1) AS EnValid,EnStatus, "
					. "QuTargetNo, SUBSTRING(QuTargetNo,2) AS Target,"
					. "QuD" . $_REQUEST['x_Dist'] . "Score AS SelScore,QuD" . $_REQUEST['x_Dist'] . "Hits AS SelHits,QuD" . $_REQUEST['x_Dist'] . "Gold AS SelGold,QuD" . $_REQUEST['x_Dist'] . "Xnine AS SelXNine, "
					. "QuScore, "
					. "QuD" . $_REQUEST['x_Dist'] . "ArrowString AS ArrowString,"
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
					= "SELECT EnId,EnCode,EnName,EnFirstName,EnTournament,EnDivision,EnClass,EnCountry,CoCode, (EnStatus <=1) AS EnValid,EnStatus, "
					. "QuTargetNo, SUBSTRING(QuTargetNo,2) AS Target,"
					. "QuD" . $_REQUEST['x_Dist'] . "Score AS SelScore,QuD" . $_REQUEST['x_Dist'] . "Hits AS SelHits,QuD" . $_REQUEST['x_Dist'] . "Gold AS SelGold,QuD" . $_REQUEST['x_Dist'] . "Xnine AS SelXNine, "
					. "QuScore, "
					. "QuD" . $_REQUEST['x_Dist'] . "ArrowString AS ArrowString,"
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
				if (safe_num_rows($Rs)>0)
				{

?>
<form name="Frm" method="POST" action="">
<table class="Tabella">
<tr>
<td class="Title"><?php print get_text('Target');?></td>
<td class="Title"><?php print get_text('Code','Tournament');?></td>
<td class="Title"><?php print get_text('Archer');?></td>
<td class="Title"><?php print get_text('Div');?></td>
<td class="Title"><?php print get_text('Cl');?></td>
<td class="Title"><?php print get_text('Country');?></td>
<?php print $ScoreTH; ?>
<td class="Title" width="5%">Score (<?php print $_REQUEST['x_Dist']; ?>)</td>
<td class="Title" width="5%"><?php print $RowTour->TtGolds . ' (' . $_REQUEST['x_Dist'] . ')'; ?></td>

<td class="Title" width="5%"><?php print $RowTour->TtXNine . ' (' . $_REQUEST['x_Dist'] . ')'; ?></td>
<td class="Title" width="5%">Score</td>
</tr>
<?php
					$CurTarget = 'xx';
					$RowStyle='';	// NoShoot oppure niente
					$TarStyle='';	// niene oppure warning se $RowStyle==''
				// elenco persone
					while ($MyRow=safe_fetch($Rs))
					{
						$ScoreTD = '';

						$RowStyle=($MyRow->EnValid ? '' : 'NoShoot');

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
<td><?php print $MyRow->Target; ?></td>
<td><?php print $MyRow->EnCode; ?></td>
<td><?php print $MyRow->EnFirstName . ' ' . $MyRow->EnName;?></td>
<td class="Center"><?php print $MyRow->EnDivision; ?></td>
<td class="Center"><?php print $MyRow->EnClass; ?></td>
<td><?php print $MyRow->CoCode; ?></td>
<?php
					// elaboro l'arrowstring

						$CurArrowString = str_pad($MyRow->ArrowString,$RowTour->MaxArrows,' ',STR_PAD_RIGHT);

						$SubArrowString = substr($CurArrowString,$Ip,$_REQUEST['x_Arrows']);

						//print '<td>...' . $CurArrowString . '...<br>...' . $SubArrowString . '...</td>';

						for ($i=0;$i<$_REQUEST['x_Arrows'];++$i)
						{
							$vv = DecodeFromLetter($SubArrowString[$i]);
							$FieldId = 'arr_' . $_REQUEST['x_Dist'] . '_' . ($Ip+$i) . '_' . $MyRow->EnId;
							$ScoreTD
								.='<td class="Center">'
								. '<input type="text" id="' . $FieldId . '" '
								. 'size="2" maxlength="2" value="' . $vv . '" '
								. 'onBlur="javascript:UpdateArrow(\'' . $FieldId . '\');">'
								. '</td>';
						}

						print $ScoreTD;
?>
<td class="Center Bold">
<div id="idScore_<?php print $_REQUEST['x_Dist'] . '_' . $MyRow->EnId; ?>"><?php print $MyRow->SelScore; ?></div></td>
<td class="Center Bold"><div id="idGold_<?php print $_REQUEST['x_Dist'] . '_' . $MyRow->EnId; ?>"><?php print $MyRow->SelGold; ?></div></td>
<td class="Center Bold"><div id="idXNine_<?php print $_REQUEST['x_Dist'] . '_' . $MyRow->EnId; ?>"><?php print $MyRow->SelXNine; ?></div></td>
<td class="Center Bold" onDblClick="javascript:window.open('WriteScoreCard.php?Command=OK&x_Session=<?php print $_REQUEST['x_Session']; ?>&x_Dist=<?php print $_REQUEST['x_Dist']; ?>&x_Target=<?php print $MyRow->Target; ?>',<?php print $MyRow->EnId; ?>);">
<div id="idScore_<?php print $MyRow->EnId; ?>"><?php print $MyRow->QuScore; ?></div>
</td>
<?php
						$CurTarget=	substr($MyRow->Target,0,-1);
					}	// fine elenco persone
?>
</tr>
</table>
</form>
<?php
				}
			}
			else
				print get_text('BadParams','Tournament');
		}
	}
?>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>