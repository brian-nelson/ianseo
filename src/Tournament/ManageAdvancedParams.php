<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_Various.inc.php');
    checkACL(AclCompetition, AclReadWrite);

	CheckTourSession(true);

	if (isset($_REQUEST['command'])) {
		if ($_REQUEST['command']=='SAVE') {
            $_REQUEST['d_ToGoldsChars']=getLettersFromPrintList($_REQUEST['d_ToGoldsChars']);
            $_REQUEST['d_ToXNineChars']=getLettersFromPrintList($_REQUEST['d_ToXNineChars']);
			$up=array();
			foreach ($_REQUEST as $k=>$v) {
				if (substr($k,0,2)=='d_') {
					list(,$field)=explode('_',$k);
					$up[]="{$field}=".StrSafe_DB($v);
					if($field=='ToNumDist' and intval($v)>0) {
					    $v=intval($v);
					    // removes the distanceinformation records and clean the names of the distances!
                        safe_w_sql("delete from DistanceInformation where DiTournament={$_SESSION['TourId']} and DiDistance>$v");
                        if($v<8) {
                            $sql=array();
                            for($n=$v+1;$n<=8;$n++) {
                                $sql[]="Td{$n}=''";
                            }
                            safe_w_sql("update TournamentDistances set ".implode(',',$sql)." where TdTournament={$_SESSION['TourId']}");
                        }
                    }
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
	for($i=0;$i<strlen($tour->ToGoldsChars);++$i) {
		$goldsChars[]=DecodeFromLetter($tour->ToGoldsChars[$i]);
	}
	$goldsChars=implode(',',array_unique($goldsChars));

	$xNineChars=array();
	for($i=0;$i<strlen($tour->ToXNineChars);++$i) {
		$xNineChars[]=DecodeFromLetter($tour->ToXNineChars[$i]);
	}
	$xNineChars=implode(',',array_unique($xNineChars));

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
