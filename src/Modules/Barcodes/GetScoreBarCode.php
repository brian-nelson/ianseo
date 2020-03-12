<?php
define('debug',false);	// settare a true per l'output di debug
define('IN_PHP', true);

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_Number.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');

CheckTourSession(true);
checkACL(AclQualification,AclReadWrite);
$EnBib='-';
$archers=array();

// Check the correct separator (as barcode reader may interpret «-» as a «'» !)
//
if(empty($_SESSION['BarCodeSeparator'])) {
	require_once('./GetBarCodeSeparator.php');
	die();
}

$ShowMiss=(!empty($_GET['ShowMiss']));
$D=0;
$T=0;
$Turno='';

if($_GET) {
	if(!empty($_GET['BARCODESEPARATOR'])) {
		unset($_SESSION['BarCodeSeparator']);
		CD_redirect($_SERVER['PHP_SELF']);
	}

	if(!empty($_GET['T'])) $Turno='&T='.($T=$_GET['T']);

	// try to guess from input field both the distance and the selected archer
	if(!empty($_GET['B'])) {
		$tmpB=explode($_SESSION['BarCodeSeparator'], $_GET['B']);
		if(count($tmpB)==5) {
			$tmpB[0]="{$tmpB[0]}-{$tmpB[1]}";
			$tmpB[1]=$tmpB[2];
			$tmpB[2]=$tmpB[3];
			$tmpB[3]=$tmpB[4];
			unset($tmpB[4]);
		}
		if(!empty($tmpB[3])) {
			if(empty($_GET['D'])) $_GET['D']=intval($tmpB[3]);
			$EnBib=$tmpB[0];
		}
	}


	// sets the distance
	if(!empty($_GET['D'])) $D=intval($_GET['D']);

	// sets the autoedit feature
	if(!empty($_GET['AutoEdit']) and empty($_GET['return']) and empty($_GET['C'])) $_GET['C']='EDIT2';
	unset($_GET['return']);

	// we can carry on ONLY if a distance is set (explicitly or through the barcode) -- Changed: No Distaxo, so Total!
	if(!empty($_GET['B'])) {
		// gets all the archers through the input:
		// @STTT (S=Session, T=0-padded target)
		// #Name/Surname
		// _GET['target']
		$archers=getScore($D, $_GET['B']);
		if($EnBib=='-') {
			$EnBib=key($archers);
		}
		// if we have a "C" input (beware of autoedit!) then do the action
		if(!empty($_GET['C'])) {
			$C=$_GET['C'];
			unset($_GET['C']);
			if(!empty($archers[$EnBib]) and !IsBlocked(BIT_BLOCK_QUAL)) {
				$archer=$archers[$EnBib];
				$NeedsRecalc=false;
				switch(strtoupper($C)) {
					case 'EDIT':
						if($D) {
							$GoBack=$_SERVER['SCRIPT_NAME'].go_get();
								// edit the scorecard
							$_REQUEST['Command']='OK';
							$_REQUEST['x_Session']=$archer->QuTargetNo[0];
							$_REQUEST['x_Dist']=$D;
							$_REQUEST['x_Target']=substr($archer->QuTargetNo, 1);
							require_once('Qualification/WriteScoreCard.php');
							die();
						}
						break;
					case 'EDIT2':
						if($D) {
							$GoBack=$_SERVER['SCRIPT_NAME'].go_get().'&return=1';
								// edit the scorecard
							$_REQUEST['Command']='OK';
							$_REQUEST['x_Session']=$archer->QuTargetNo[0];
							$_REQUEST['x_Dist']=$D;
							$_REQUEST['x_From']=substr($archer->QuTargetNo, 1, -1);
							$_REQUEST['x_To']=substr($archer->QuTargetNo, 1, -1);
							if(count($archers)==1) $_REQUEST['x_Target']=$archer->QuTargetNo;
							$_REQUEST['x_Gold']=1;
							require_once('Qualification/index.php');
							die();
						}
						break;
					case 'REM10':
						if($D) {
							$SQL="update Qualifications set QuD{$D}Gold='0',
								QuGold=(QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold)
								where QuId={$archer->EnId}";
							safe_w_sql($SQL);
							updateArcher($archer, $D);
							$NeedsRecalc=true;
						}
						break;
					case 'REMXNINE':
						if($D) {
							$SQL="update Qualifications set QuD{$D}Xnine='0',
								QuXnine=(QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine)
								where QuId={$archer->EnId}";
							safe_w_sql($SQL);
							updateArcher($archer, $D);
							$NeedsRecalc=true;
						}
						break;
					case 'REMALL':
						if($D) {
							$SQL="update Qualifications set QuD{$D}Xnine='0', QuD{$D}Gold='0',
								QuXnine=(QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine),
								QuGold=(QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold)
								where QuId={$archer->EnId}";
							safe_w_sql($SQL);
							updateArcher($archer, $D);
							$NeedsRecalc=true;
						}
						break;
					case 'RESET':
						if($D) {
							$Select = "SELECT QuD{$D}Arrowstring ArrowString, ToGoldsChars,ToXNineChars
								FROM Qualifications
								inner join Entries on EnId=QuId
								inner join Tournament on EnTournament=ToId
								WHERE ToId={$_SESSION['TourId']} and EnId={$archer->EnId}";

							$Rs=safe_r_sql($Select, false, true);
							if($Rs and $MyRow=safe_fetch($Rs)) {
								require_once('Common/Lib/ArrTargets.inc.php');
								list($CurScore,$CurGold,$CurXNine) = ValutaArrowStringGX($MyRow->ArrowString,$MyRow->ToGoldsChars,$MyRow->ToXNineChars);

								$SQL="update Qualifications set QuD{$D}Xnine='$CurXNine', QuD{$D}Gold='$CurGold',
									QuXnine=(QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine),
									QuGold=(QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold)
									where QuId={$archer->EnId}";
								safe_w_sql($SQL);
								updateArcher($archer, $D);
								$NeedsRecalc=true;
							}
						}
						break;
					case strtoupper($_GET['B']):
						foreach($archers as $arc) {
						    updateArcher($arc, $D);
						}
						unset($_GET['C']);
						unset($_GET['B']);
						cd_redirect(basename(__FILE__).go_get());
						break;
					default:
						// reads another barcode
						$_GET['B']=$C;
				}
				if($NeedsRecalc) {
					require_once('Qualification/Fun_Qualification.local.inc.php');
					// needs to recalculate distance and total rank, reset SO etc...
					// reset SOfs
					$SQL=" SELECT DISTINCT EvCode,EvTeamEvent
						FROM Events
						INNER JOIN EventClass ON EvCode=EcCode AND if(EvTeamEvent='0', EcTeamEvent=0, EcTeamEvent>0) AND EcTournament={$_SESSION['TourId']}
						INNER JOIN Entries ON TRIM(EcDivision)=TRIM(EnDivision) AND TRIM(EcClass)=TRIM(EnClass) AND if(EcSubClass='', true, EcSubClass=EnSubClass) AND EnId={$archer->EnId}
					WHERE (EvTeamEvent='0' AND EnIndFEvent='1') OR (EvTeamEvent='1' AND EnTeamFEvent+EnTeamMixEvent>0) AND EvTournament={$_SESSION['TourId']} ";
					$Rs=safe_r_sql($SQL);

					while ($row=safe_fetch($Rs)) {
						ResetShootoff($row->EvCode, $row->EvTeamEvent, 0);
					}

					// recalculate ranks
					$Select = "SELECT QuScore, QuGold, QuXnine FROM Qualifications WHERE QuId={$archer->EnId}";
					$Rs=safe_r_sql($Select);
					if ($MyRow = safe_fetch($Rs)) {
						$Score = $MyRow->QuScore;
						$Gold = $MyRow->QuGold;
						$Xnine = $MyRow->QuXnine;

						// distance Rank
						$Event = '*#*#';

						$Select = "SELECT CONCAT(EnDivision,EnClass) AS MyEvent, EnCountry as MyTeam, EnDivision, EnClass, EnIndClEvent, EnIndFEvent, EnTeamClEvent, EnTeamFEvent+EnTeamMixEvent as AbsTeam
							FROM Entries
							WHERE EnId={$archer->EnId} AND EnTournament={$_SESSION['TourId']}";
						$Rs=safe_r_sql($Select);

						if ($rr=safe_fetch($Rs)) {
							$Event = $rr->MyEvent;
							$Category = $rr->MyEvent;
							$Club = $rr->MyTeam;
							$Div = $rr->EnDivision;
							$Cl = $rr->EnClass;

							if($rr->EnIndClEvent) {
                                CalcQualRank($D, $Event);
                                CalcQualRank(0, $Event);
                            }

                            // regular teams
                            if($rr->EnTeamClEvent) {
                                MakeTeams($Club, $Category);
                            }

                            // recalc AbsTeams
                            if($rr->AbsTeam) {
                                MakeTeamsAbs($Club, $Div, $Cl);
                            }

                            if($rr->EnIndFEvent) {
                                // recalc Individuals
                                $events4abs=array();
                                $Rs=safe_r_sql("select distinct IndEvent from Individuals where IndId={$archer->EnId} AND IndTournament={$_SESSION['TourId']}");
                                while($rr=safe_fetch($Rs)) {
                                    $events4abs[] = $tmp->EcCode;
                                }
                                if ($events4abs) {
                                    Obj_RankFactory::create('Abs', array('events' => $events4abs, 'dist' => $D))->calculate();
                                    Obj_RankFactory::create('Abs', array('events' => $events4abs, 'dist' => 0))->calculate();
                                }
                            }
						}
					}
				}
				cd_redirect(basename(__FILE__).go_get());
			} elseif(getScore($D, $C)) {
				// reads another barcode
				$_GET['B']=$C;
				cd_redirect(basename(__FILE__).go_get());
			}
		}
	} else {
// 		cd_redirect(basename(__FILE__));
	}
}

$ONLOAD=' onLoad="javascript:document.Frm.bib.focus()"';
$JS_SCRIPT=array('<style>');
if($ShowMiss) {
	$JS_SCRIPT[]='
		form.ShowMiss {position:absolute;left:0;right:170px;}
		div.ShowMiss {position:absolute;width:170px;top:0;right:0;bottom:0;overflow-x:hidden;}
		';
}
$JS_SCRIPT[]='
	.selected td {background-color:#d0d0d0;font-weight:bold}
	';
$JS_SCRIPT[]='</style>';

include('Common/Templates/head.php');

?>
<form name="Frm" method="get" action="" class="ShowMiss">
<table class="Tabella2 half">
	<tr>
		<th class="Title" colspan="6"><?php print get_text('CheckScorecards','Tournament');?></th>

	</tr>
	<?php
	echo '<tr>';
	echo '<th colspan="5">' . get_text('BarcodeSeparator','BackNumbers') . ': <span style="font-size:150%">' . $_SESSION['BarCodeSeparator'] . '</span>' . '</th>';
	echo '<th colspan="1"><a href="' . $_SERVER["PHP_SELF"]. '?BARCODESEPARATOR=1">' . get_text('ResetBarcodeSeparator','BackNumbers') . '</a></th>';
	echo '</tr>';
	?>
	<tr>
		<th><?php print get_text('Targets','Tournament');?></th>
		<th><?php print get_text('AutoEdits','Tournament');?></th>
		<th><?php print get_text('ShowMissing','Tournament');?></th>
		<th><?php print get_text('Distance','Tournament');?></th>
		<th><?php print get_text('Barcode','BackNumbers');?></th>
		<th><?php print get_text('Session');?></th>
	</tr>
	<tr>
		<td class="Center"><input type="checkbox" onclick="document.Frm.bib.focus()" name="Targets" <?php echo ((empty($_GET) or !empty($_GET['Targets'])) ? ' checked="checked"' : ''); ?>></td>
		<td class="Center"><input type="checkbox" onclick="document.Frm.bib.focus()" name="AutoEdit"  <?php echo (!empty($_GET['AutoEdit']) ? ' checked="checked"' : ''); ?>></td>
		<td class="Center"><input type="checkbox" onclick="document.Frm.bib.focus()" name="ShowMiss"  <?php echo ((empty($_GET) or !empty($_GET['ShowMiss'])) ? ' checked="checked"' : ''); ?>></td>
		<td class="Center"><select id="Distance" name="D"  onchange="document.Frm.bib.focus()"><option value="0"></option><?php
$q=safe_r_sql("Select ToNumDist, ToGolds, ToXNine from Tournament where ToId={$_SESSION['TourId']}");
$TOUR=safe_fetch($q);
foreach(range(1,$TOUR->ToNumDist) as $d) echo '<option value="'.$d.'"'.(!empty($D) && $D==$d ? ' selected="selected"' : '').'>'.$d.'</option>';
?></select></td>
		<td class="Center"><?php
if(!empty($_GET['B'])) {
	echo '<input type="hidden" name="B" value="'.$_GET['B'].'">';
	echo '<input type="text" name="C" id="bib" tabindex="1">';
} else {
	echo '<input type="text" name="B" id="bib" tabindex="1">';
}
?></td>
		<td class="Center"><select id="Session" name="T"  onchange="document.Frm.bib.focus()"><option value="0"></option><?php
$q=safe_r_sql("Select distinct SesOrder, SesName from Session where SesType='Q' and SesTournament={$_SESSION['TourId']} order by SesOrder");
while($r=safe_fetch($q)) echo '<option value="'.$r->SesOrder.'" '.(!empty($_GET['T']) && $_GET['T']==$r->SesOrder ? ' selected="selected"' : '').'>'.($r->SesName ? $r->SesName : $r->SesOrder).'</option>';
?></select></td>
</tr>
	<tr>
		<td class="Center" colspan="2"><input type="submit" value="<?php print get_text('CmdGo','Tournament');?>" id="Vai" onClick="javascript:SendBib();"></td>
		<td class="Center"><input type="button" value="<?php print get_text('BarcodeMissing','Tournament');?>" onClick="window.open('./GetScoreBarCodeMissing.php?S=Q&D='+document.getElementById('Distance').value+'&T='+document.getElementById('Session').value);"></td>
	</tr>

	<tr>
	<td colspan="6"><?php echo get_text('ScoreBarCodeShortcuts', 'Help'); ?></td>
	</tr>
	<?php

	if(!$archers){
		echo '<tr class="divider"><td colspan="6"></td></tr>
		<tr><th colspan="6"><img src="beiter.png" width="80" hspace="10" alt="Beiter Logo" border="0"/><br>' . get_text('Credits-BeiterCredits', 'Install') . '</th></tr>';
	}
	?>
</table>
<?php

if($archers) {
	echo '<table class="Tabella2" style="font-size:150%">';
	echo '<tr><th class="Title" colspan="16">'.get_text('Archer').'</th></tr>';
	echo '<tr>';
		echo '<th>'.get_text('TargetShort', 'Tournament').'</th>';
		echo '<th>'.get_text('DistanceShort','Tournament').'</th>';
		echo '<th colspan="2">'.get_text('Name','Tournament').'</th>';
		echo '<th>'.get_text('ClassDiv', 'InfoSystem').'</th>';
		echo '<th>'.get_text('Total').'</th>';
		echo '<th>'.$TOUR->ToGolds.'</th>';
		echo '<th>'.$TOUR->ToXNine.'</th>';
		echo '<th>'.get_text('Total').'</th>';
		echo '<th>'.$TOUR->ToGolds.'</th>';
		echo '<th>'.$TOUR->ToXNine.'</th>';
		echo '<th colspan="4"></th>';
		echo '</tr>';
	foreach($archers as $archer) {
		$T=$archer->QuTargetNo[0];
		echo '<tr'.($archer->EnBib==$EnBib ? ' class="selected"' : '').'>';
			echo '<td>'.ltrim(substr($archer->QuTargetNo, 1), '0').'</td>';
			echo '<td>'.intval($D).'</td>';
			echo '<td>'.$archer->Firstname.'</td>';
			echo '<td>'.$archer->EnName.'</td>';
			echo '<td align="center">'.$archer->EnDivision.' '.$archer->EnClass.'</td>';
			echo '<td align="right" style="font-size:150%"><b>'.$archer->Score.'</b></td>';
			echo '<td align="right" style="font-size:150%;padding:0 10px;"><b>'.$archer->Gold.'</b></td>';
			echo '<td align="right" style="font-size:150%;padding:0 10px;"><b>'.$archer->Xnine.'</b></td>';
			echo '<td align="right" style="font-size:100%">'.$archer->tScore.'</td>';
			echo '<td align="right" style="font-size:100%;padding:0 10px;">'.$archer->tGold.'</td>';
			echo '<td align="right" style="font-size:100%;padding:0 10px;">'.$archer->tXnine.'</td>';
			echo '<td align="center" style="font-size:80%"><b><a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->EnDivision.$_SESSION['BarCodeSeparator'].$archer->EnClass, 'C' => $archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->EnDivision.$_SESSION['BarCodeSeparator'].$archer->EnClass)).'">CONFIRM</a></b></td>';
			if($D) {
				echo '<td align="center" style="font-size:80%"><b><a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->EnDivision.$_SESSION['BarCodeSeparator'].$archer->EnClass, 'C'=> 'EDIT')).'">Edit arrows</a>
					<br/><a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->EnDivision.$_SESSION['BarCodeSeparator'].$archer->EnClass, 'C' => 'EDIT2')).'">Edit totals</a></b>
					</td>';
				echo '<td align="center" style="font-size:80%"><b><a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->EnDivision.$_SESSION['BarCodeSeparator'].$archer->EnClass, 'C'=> 'REM10')).'">Remove 10</a>
					<br/><a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->EnDivision.$_SESSION['BarCodeSeparator'].$archer->EnClass, 'C'=> 'REMXNINE')).'">Remove X/Nine</a>
					<br/><a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->EnDivision.$_SESSION['BarCodeSeparator'].$archer->EnClass, 'C'=> 'REMALL')).'">Remove both</a></b>
					</td>';
				echo '<td align="center" style="font-size:80%"><b><a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->EnDivision.$_SESSION['BarCodeSeparator'].$archer->EnClass, 'C'=> 'RESET')).'">Reset both</a></b>
					</td>';
			} else {
				echo '<td align="center" style="font-size:80%" colspan="3">&nbsp;</td>';
			}
			echo '</tr>';
	}
	echo '</table>';
}


?>
</form>
<?php
if($ShowMiss) {
	echo '<div class="ShowMiss"><table>';
	$cnt = 0;
	$tgt = 0;
	$tmpRow = '';
	$MyQuery = "SELECT EnCode as Bib
			, EnName AS Name
			, upper(EnFirstName) AS FirstName
			, QuSession AS Session
			, SUBSTRING(QuTargetNo,2) AS TargetNo
			, CoCode AS NationCode, CoName AS Nation
			, EnClass AS ClassCode, ClDescription
			, EnDivision AS DivCode, DivDescription
			, EnSubClass as SubClass
			, SesName
		FROM Entries
		inner JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
		inner JOIN Qualifications ON EnId=QuId and QuSession=$T
		inner JOIN Divisions ON EnTournament=DivTournament AND EnDivision=DivId
		inner JOIN Classes ON EnTournament=ClTournament AND EnClass=ClId
		inner join Session on SesOrder=$T and SesTournament=EnTournament and SesType='Q'
		WHERE EnAthlete=1
			AND EnTournament = {$_SESSION['TourId']} AND EnStatus<=1
			AND QuConfirm & ".pow(2, $D)." = 0
		ORDER BY QuTargetNo ";
	$Q=safe_r_sql($MyQuery);
	while($r=safe_fetch($Q)) {
		if(empty($_GET['Targets']) or $tgt!=intval($r->TargetNo)) {
			$tgt=intval($r->TargetNo);
			$cnt++;
		}
//		$lnk=' onclick="location.href=\''.go_get('B', $r->match1.$_SESSION['BarCodeSeparator'].$r->teamEvent.$_SESSION['BarCodeSeparator'].$r->event).'\'"';

		$tmpRow .= '<tr><td>'.$r->TargetNo.'</td><td>'.$r->DivCode.$r->ClassCode.'</td><td nowrap="nowrap">'.$r->FirstName.' '.$r->Name.'</td></tr>';
	}
	echo '<tr><th colspan="3">' . get_text('TotalMissingScorecars','Tournament',$cnt) . '</th></tr>';
	echo $tmpRow;
	echo '</table></div>';
}
?>
<div id="idOutput"></div>
<?php
include('Common/Templates/tail.php');


function getScore($dist, $barcode, $strict=false) {
	global $EnBib;
	$ret=array();
	$div='';
	$cls='';
	if($barcode[0]=='@') {
		$barcode=substr($barcode,1);

		// left-pad with 0
		if(strlen($barcode)<4) $barcode=str_pad($barcode, 3, '0', STR_PAD_LEFT);

		// insert jolly session if session not defined or not set
		if(strlen($barcode)<4) $barcode=(empty($_GET['T']) ? '_' : $_GET['T']).$barcode;

		$filter=" QuTargetNo like '".$barcode."%'";
	} elseif($barcode[0]=='#') {
		$filter=" (EnFirstname like ".StrSafe_DB(substr($barcode,1).'%')." or EnName like ".StrSafe_DB(substr($barcode,1).'%').")";
	} else {
		$tmp=@explode($_SESSION['BarCodeSeparator'], $barcode);
		if(count($tmp)>4) {
			$bib=$tmp[0].'-'.$tmp[1];
			$div=$tmp[2];
			$cls=$tmp[3];
		} else {
			//$bib=ltrim($tmp[0], '0'); // why??? Breaks all the regular bibs that start with 0!
			$bib=$tmp[0];
			$div=$tmp[1];
			$cls=$tmp[2];
		}
		if(substr($bib, 0, 2)=='UU') $bib='_'.substr($bib, 2);
		$filter="EnCode='$bib' and EnDivision='$div' and EnClass='$cls'";
		$filter2="EnCode='$bib'";
		$EnBib=$bib;

		if(!$strict and !empty($_GET['Targets'])) {
			$filter="left(QuTargetNo,4)=(select left(QuTargetNo,4) from Qualifications inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} where $filter)";
		}
		if(empty($bib) or empty($div) or empty($cls)) return;
	}
	$SQL="select QuTargetNo, EnCode EnBib, EnId, EnName, upper(EnFirstname) Firstname, EnDivision, EnClass, QuScore tScore, QuGold tGold, QuXnine tXnine, " .
		($dist ? "QuD{$dist}Score Score, QuD{$dist}Gold Gold, QuD{$dist}Xnine Xnine" : "QuScore Score, QuGold Gold, QuXnine Xnine") . "
		from Qualifications inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} where $filter
		order by QuTargetNo, EnDivision='$div' desc, EnClass='$cls' desc ";
	$q=safe_r_sql($SQL, false, true);
	while($r=safe_fetch($q)) $ret["$r->EnBib"]=$r;
	if(!$ret) {
		$SQL="select QuTargetNo, EnCode EnBib, EnId, EnName, upper(EnFirstname) Firstname, EnDivision, EnClass, QuScore tScore, QuGold tGold, QuXnine tXnine, " .
				($dist ? "QuD{$dist}Score Score, QuD{$dist}Gold Gold, QuD{$dist}Xnine Xnine" : "QuScore Score, QuGold Gold, QuXnine Xnine") . "
				from Qualifications inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} where $filter2
				order by QuTargetNo, EnDivision='$div' desc, EnClass='$cls' desc ";
		$q=safe_r_sql($SQL, false, true);
		while($r=safe_fetch($q)) $ret["$r->EnBib"]=$r;
		if(count($ret)>1) $ret=array();
	}
	if(!$ret) {
		$filter="EdExtra='$bib' and EnDivision='$div' and EnClass='$cls'";
		$filter2="EdExtra='$bib'";
		$EnBib=$bib;

		if(!$strict and !empty($_GET['Targets'])) {
			$filter="left(QuTargetNo,4)=(select left(QuTargetNo,4) from Qualifications inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} where $filter)";
		}
		if(empty($bib) or empty($div) or empty($cls)) return;

		$SQL="select QuTargetNo, EdExtra EnBib, EnId, EnName, upper(EnFirstname) Firstname, EnDivision, EnClass, QuScore tScore, QuGold tGold, QuXnine tXnine, " .
			($dist ? "QuD{$dist}Score Score, QuD{$dist}Gold Gold, QuD{$dist}Xnine Xnine" : "QuScore Score, QuGold Gold, QuXnine Xnine") . "
            from Qualifications 
            inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} 
            inner JOIN ExtraData ON EdType='Z' and EdId=EnId
            where $filter
            order by QuTargetNo, EnDivision='$div' desc, EnClass='$cls' desc ";
		$q=safe_r_sql($SQL, false, true);
		while($r=safe_fetch($q)) $ret["$r->EnBib"]=$r;
		if(!$ret) {
			$SQL="select QuTargetNo, EdExtra EnBib, EnId, EnName, upper(EnFirstname) Firstname, EnDivision, EnClass, QuScore tScore, QuGold tGold, QuXnine tXnine, " .
				($dist ? "QuD{$dist}Score Score, QuD{$dist}Gold Gold, QuD{$dist}Xnine Xnine" : "QuScore Score, QuGold Gold, QuXnine Xnine") . "
				from Qualifications 
				inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} 
                inner JOIN ExtraData ON EdType='Z' and EdId=EnId
				where $filter2
				order by QuTargetNo, EnDivision='$div' desc, EnClass='$cls' desc ";
			$q=safe_r_sql($SQL, false, true);
			while($r=safe_fetch($q)) $ret["$r->EnBib"]=$r;
			if(count($ret)>1) $ret=array();
		}
	}
	return $ret;
}

function updateArcher($archer, $D) {
	$SQL= "update Qualifications
	    set QuConfirm = QuConfirm | ".pow(2, $D) ."
	    where QuId=$archer->EnId";
	safe_w_sql($SQL);
}