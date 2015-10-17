<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_Various.inc.php');

	CheckTourSession(true);

	if (isset($_REQUEST['command']))
	{
		if ($_REQUEST['command']=='SAVE')
		{
		// maneggio i simboli degli ori e delle x
			$golds=explode(',',$_REQUEST['d_ToGoldsChars']);
			$_REQUEST['d_ToGoldsChars']='';
			foreach ($golds as $g)
			{
				$_REQUEST['d_ToGoldsChars'].=GetLetterFromPrint(trim($g));
			}

			$xnine=explode(',',$_REQUEST['d_ToXNineChars']);
			$_REQUEST['d_ToXNineChars']='';
			foreach ($xnine as $x)
			{
				$_REQUEST['d_ToXNineChars'].=GetLetterFromPrint(trim($x));
			}

			$up=array();
			foreach ($_REQUEST as $k=>$v)
			{
				if (substr($k,0,2)=='d_')
				{
					$v=StrSafe_DB($v);

					list(,$field)=explode('_',$k);
					$up[]="{$field}={$v} ";
				}
			}

			$up=implode(',',$up);
			$q="
				UPDATE
					Tournament
				SET
					{$up}
				WHERE
					ToId={$_SESSION['TourId']}
			";
					//print $q;Exit;
			$r=safe_r_sql($q);

			// Get the "extra" options
			Set_Tournament_Option('OlympicFont', $font=preg_replace('/[^a-z0-9_ -]+/sim','',$_REQUEST['OlympicFont']));
			Set_Tournament_Option('OlympicFont-use', !empty($_REQUEST['OlympicFont-use']) and $font);

			// Set the records in which this tournament is going to work
			// Start deleting the records type attributions!

//			debug_svela($_REQUEST);

			safe_w_sql("delete from TourRecords where TrTournament={$_SESSION['TourId']}");
			if(!empty($_REQUEST['Records'])) {
				// recreates the tabl
				foreach($_REQUEST['Records'] as $Val) {
					$Flags=array();
					if(!empty($_REQUEST['RecBar'][$Val])) $Flags[]='bar';
					if(!empty($_REQUEST['RecGap'][$Val])) $Flags[]='gap';
					safe_w_sql("insert into TourRecords
						select distinct '{$_SESSION['TourId']}'
							, ReType
							, ReCode
							, ReTeam
							, RePara
							, '".substr($_REQUEST['RecColor'][$Val], 1)."'
							, '".implode(',', $Flags)."'
						from Records where ReType='$Val' and RePara=".($_SESSION['TourLocRule']=='PAR'?'1':'0') );
				}
			}

			// removes the records on the tour that are non to follow anymore
			safe_w_sql("delete from RecTournament where RtTournament={$_SESSION['TourId']} and RtRecType not in (select TrRecType from TourRecords where TrTournament={$_SESSION['TourId']})");
			// inserts/updates into the RecTournament the situation of records updated BEFORE the Tour Ends
			safe_w_sql("insert into RecTournament (
					select '{$_SESSION['TourId']}',
						ReType,
						ReCode,
						ReTeam,
						RePara,
						ReCategory,
						ReDistance,
						ReTotal,
						ReXNine,
						ReDate,
						ReExtra,
						ReLastUpdated
					from Records where ReDate<='{$_SESSION['TourRealWhenFrom']}'
						and ReTourType='{$_SESSION['TourType']}'
						and RePara=".($_SESSION['TourLocRule']=='PAR'?'1':'0')."
						and ReType in (select TrRecType from TourRecords where TrTournament={$_SESSION['TourId']}) )
				on duplicate key update
					RtRecTotal = ReTotal,
					RtRecXNine = ReXNine,
					RtRecDate  = ReDate ,
					RtRecExtra = ReExtra,
					RtRecLastUpdated=ReLastUpdated
				");
		}
	}

	$q="
		SELECT
			ToTypeName,
			ToNumDist,
			ToNumEnds,
			ToMaxDistScore,
			ToMaxFinIndScore,
			ToMaxFinTeamScore,
			ToCategory,
			ToElabTeam,
			ToElimination,
			ToGolds,
			ToXNine,
			ToGoldsChars,
			ToXNineChars,
			ToDouble
		FROM
			Tournament
		WHERE
			ToId={$_SESSION['TourId']}
	";
	$r=safe_r_sql($q);

	$tour=null;

	if (safe_num_rows($r)==1)
	{
		$tour=safe_fetch($r);
	}

	if ($tour===null)
		exit;

	$goldsChars=array();
	for($i=0;$i<strlen($tour->ToGoldsChars);++$i)
	{
		$goldsChars[]=DecodeFromLetter($tour->ToGoldsChars[$i]);
	}
	$goldsChars=implode(',',$goldsChars);

	$xNineChars=array();
	for($i=0;$i<strlen($tour->ToXNineChars);++$i)
	{
		$xNineChars[]=DecodeFromLetter($tour->ToXNineChars[$i]);
	}
	$xNineChars=implode(',',$xNineChars);

	$categories=getTournamentCategories();

	$comboCategory=comboFromRs(
		$categories,
		'key',
		'descr',
		1,
		$tour->ToCategory,
		null,
		'd_ToCategory',
		'd_ToCategory'
	);

	$elabMode=getElabTeamMode();

	$comboElabTeam=comboFromRs(
		$elabMode,
		'key',
		'descr',
		1,
		$tour->ToElabTeam,
		null,
		'd_ToElabTeam',
		'd_ToElabTeam'
	);

	$comboElimination=comboFromRs(
		array(
			array('key'=>'0','descr'=>get_text('No')),
			array('key'=>'1','descr'=>get_text('Yes')),
		),
		'key',
		'descr',
		1,
		$tour->ToElimination,
		null,
		'd_ToElimination',
		'd_ToElimination'
	);

	$comboDouble=comboFromRs(
		array(
			array('key'=>'0','descr'=>get_text('No')),
			array('key'=>'1','descr'=>get_text('Yes')),
		),
		'key',
		'descr',
		1,
		$tour->ToDouble,
		null,
		'd_ToDouble',
		'd_ToDouble'
	);

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ColorPicker/302pop.js"></script>',
		);
	include('Common/Templates/head.php');
?>
<div align="center">
	<div class="">
		<form name="frm" id="frm" method="post" action="<?php print $_SERVER['PHP_SELF'];?>">
			<table class="Tabella">
				<tr><th class="Main" colspan="2"><?php print get_text('AdvancedParams','Tournament');?></th></tr>
				<tr><td style="width:25%;"></td><td></td></tr>
				<tr>
					<th class="Title" colspan="2"><?php print $tour->ToTypeName;?></th>

				</tr>
				<tr>
					<th class="TitleLeft"><?php print '# '.get_text('Distances','Tournament');?></th>
					<td><input type="text" name="d_ToNumDist" id="d_ToNumDist" size="5" maxlength="2" value="<?php print $tour->ToNumDist;?>"/></td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print '# '.get_text('Ends','Tournament');?></th>
					<td><input type="text" name="d_ToNumEnds" id="d_ToNumEnds" size="5" maxlength="2" value="<?php print $tour->ToNumEnds;?>"/></td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('MaxDistScore','Tournament');?></th>
					<td><input type="text" name="d_ToMaxDistScore" id="d_ToMaxDistScore" size="5" maxlength="4" value="<?php print $tour->ToMaxDistScore;?>"/></td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('MaxFinIndScore','Tournament');?></th>
					<td><input type="text" name="d_ToMaxFinIndScore" id="d_ToMaxFinIndScore" size="5" maxlength="4" value="<?php print $tour->ToMaxFinIndScore;?>"/></td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('MaxFinTeamScore','Tournament');?></th>
					<td><input type="text" name="d_ToMaxFinTeamScore" id="d_ToMaxFinTeamScore" size="5" maxlength="4" value="<?php print $tour->ToMaxFinTeamScore;?>"/></td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('TourCategory','Tournament');?></th>
					<td><?php print $comboCategory;?></td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('ElabTeamMode','Tournament');?></th>
					<td><?php print $comboElabTeam;?></td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('Elimination');?></th>
					<td><?php print $comboElimination;?></td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('GoldLabel','Tournament');?></th>
					<td><input type="text" name="d_ToGolds" id="d_ToGolds" size="5" maxlength="5" value="<?php print $tour->ToGolds;?>"/></td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('XNineLabel','Tournament');?></th>
					<td><input type="text" name="d_ToXNine" id="d_ToXNine" size="5" maxlength="5" value="<?php print $tour->ToXNine;?>"/></td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('PointsAsGold','Tournament');?></th>
					<td>
						<input type="text" name="d_ToGoldsChars" id="d_ToGoldsChars" size="40" maxlength="31" value="<?php print $goldsChars;?>"/><br/>
						<?php print get_text('CommaSeparatedValues');?>
					</td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('PointsAsXNine','Tournament');?></th>
					<td>
						<input type="text" name="d_ToXNineChars" id="d_ToXNineChars" size="40" maxlength="31" value="<?php print $xNineChars;?>"/><br/>
						<?php print get_text('CommaSeparatedValues');?>
					</td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('TourDouble','Tournament');?></th>
					<td><?php print $comboDouble;?></td>
				</tr>
				<tr>
					<th colspan="2" class="Title"><?php echo get_text('TourRecordSetup','Tournament'); ?></th>
				</tr>

				<tr>
					<th class="TitleLeft"><?php print get_text('OlympicFont','InfoSystem');?></th>
					<td><input type="text" name="OlympicFont" value="<?php echo (empty($_SESSION['OlympicFont']) ? '' : $_SESSION['OlympicFont']); ?>"></td>
				</tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('OlympicFont-use','InfoSystem');?></th>
					<td><input type="checkbox" name="OlympicFont-use"<?php echo (empty($_SESSION['OlympicFont-use']) ? '' : ' checked="checked"') ?>></td>
				</tr>

				<?php

// get the records type in the DB
$q=safe_r_sql("select distinct
		ReType,
		ReCode,
		TrColor,
		TrTournament is not null as ReInserted ,
		find_in_set('bar', TrFlags) TrBars,
		find_in_set('gap', TrFlags) TrGaps from Records
	left join TourRecords
		on ReType=TrRecType
		and ReCode=TrRecCode
		and ReTeam=TrRecTeam
		and RePara=TrRecPara
		and TrTournament={$_SESSION['TourId']}
	WHERE RePara=".($_SESSION['TourLocRule']=='PAR'?'1':'0')."
		");
while($r=safe_fetch($q)) {
	echo '<tr>
		<th class="TitleLeft">'. get_text($r->ReType.'-Record','Tournament').'</th>
		<td><input type="checkbox" name="Records[]" value="'.$r->ReType.'"'.($r->ReInserted ? ' checked="checked"' : '').'>
		<input type="text" name="RecColor['.$r->ReType.']" id="BnTnoColor_'.$r->ReType.'" size="6" maxlength="7" value="#' . $r->TrColor . '">&nbsp;<input type="text" id="Ex_BnTnoColor_'.$r->ReType.'" size="1" style="background-color:#' . $r->TrColor . '" readonly>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302(\'BnTnoColor_'.$r->ReType.'\',\'Ex_BnTnoColor_'.$r->ReType.'\');">
		-&nbsp;'.get_text('BarRecord', 'InfoSystem').'<input type="checkbox" name="RecBar['.$r->ReType.']"'.($r->TrBars ? ' checked="checked"' : '').'>
		-&nbsp;'.get_text('GapRecord', 'InfoSystem').'<input type="checkbox" name="RecGap['.$r->ReType.']"'.($r->TrGaps ? ' checked="checked"' : '').'>
		</td>
		</tr>';
}
				?>
				<tr>
					<td class="Center" colspan="2">
						<input type="hidden" name="command" value="SAVE"/>
						<input type="button" value="<?php print get_text('CmdSave');?>" onclick="document.frm.submit();"/>
						&nbsp;&nbsp;
						<input type="reset" value="<?php print get_text('CmdCancel');?>"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>


<?php
	include('Common/Templates/tail.php');
?>
