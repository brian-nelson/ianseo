<?php
define('debug',false);	// settare a true per l'output di debug
define('IN_PHP', true);

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_Number.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');

CheckTourSession(true);
checkACL(AclEliminations,AclReadWrite);
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
		$tmpB=explode($_SESSION['BarCodeSeparator'], $_GET['B'], 3);
		if(isset($tmpB[1])) {
			if(empty($_GET['D'])) $_GET['D']=intval($tmpB[1]);
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
		// $D is now the real phase number (0 or 1)
		if($EnBib=='-') {
			$EnBib=key($archers);
		}
		// if we have a "C" input (beware of autoedit!) then do the action
		if(!empty($_GET['C'])) {
			$C=$_GET['C'];
			unset($_GET['C']);
			if(!empty($archers[$EnBib]) and !IsBlocked(BIT_BLOCK_ELIM)) {
				$archer=$archers[$EnBib];
				switch(strtoupper($C)) {
					case 'EDIT':
						if($D) {
							$GoBack=$_SERVER['SCRIPT_NAME'].go_get();
								// edit the scorecard
							$_REQUEST['Events']=array($archer->ElEventCode.'-'. $archer->ElElimPhase);
							$_REQUEST['x_Target']=$archer->ElTargetNo;
							require_once('Elimination/WriteScoreCard.php');
							die();
						}
						break;
					case 'EDIT2':
						if($D) {
							$GoBack=$_SERVER['SCRIPT_NAME'].go_get().'&return=1';
								// edit the scorecard
							$ElimFilter=" AND ElElimPhase={$archer->ElElimPhase} AND ElEventCode='{$archer->ElEventCode}'";
							if(count($archers)==1) {
								$ElimFilter.=" AND ElTargetNo='{$archer->ElTargetNo}' ";
							} else {
								$ElimFilter.=" AND left(ElTargetNo, 3)='".substr($archer->ElTargetNo, 0, -1)."' ";
							}
							require_once('Elimination/index.php');
							die();
						}
						break;
// 					case 'REM10':
// 						if($D) {
// 							$SQL="update Qualifications set QuD{$D}Gold='' where QuId={$archer->EnId}";
// 							safe_w_sql($SQL);
// 							updateArcher($archer, $D);
// 							cd_redirect(basename(__FILE__).go_get());
// 						}
// 						break;
// 					case 'REMXNINE':
// 						if($D) {
// 							$SQL="update Qualifications set QuD{$D}Xnine='' where QuId={$archer->EnId}";
// 							safe_w_sql($SQL);
// 							updateArcher($archer, $D);
// 							cd_redirect(basename(__FILE__).go_get());
// 						}
// 						break;
// 					case 'REMALL':
// 						if($D) {
// 							$SQL="update Qualifications set QuD{$D}Xnine='', QuD{$D}Gold='' where QuId={$archer->EnId}";
// 							safe_w_sql($SQL);
// 							updateArcher($archer, $D);
// 							cd_redirect(basename(__FILE__).go_get());
// 						}
// 						break;
// 					case 'RESET':
// 						if($D) {
// 							$Select = "SELECT QuD{$D}Arrowstring ArrowString, ToGoldsChars,ToXNineChars
// 								FROM Qualifications
// 								inner join Entries on EnId=QuId
// 								inner join Tournament on EnTournament=ToId
// 								WHERE ToId={$_SESSION['TourId']} and EnId={$archer->EnId}";
//
// 							$Rs=safe_r_sql($Select, false, true);
// 							if($Rs and $MyRow=safe_fetch($Rs)) {
// 								require_once('Common/Lib/ArrTargets.inc.php');
// 								list($CurScore,$CurGold,$CurXNine) = ValutaArrowStringGX($MyRow->ArrowString,$MyRow->ToGoldsChars,$MyRow->ToXNineChars);
//
// 								$SQL="update Qualifications set QuD{$D}Xnine='$CurXNine', QuD{$D}Gold='$CurGold' where QuId={$archer->EnId}";
// 								safe_w_sql($SQL);
// 								updateArcher($archer, $D);
// 							}
// 							cd_redirect(basename(__FILE__).go_get());
// 						}
// 						break;
					case strtoupper($_GET['B']):
						foreach($archers as $arc) updateArcher($arc, $D);
						unset($_GET['C']);
						unset($_GET['B']);
						cd_redirect(basename(__FILE__).go_get());
						break;
					default:
						// reads another barcode
						$_GET['B']=$C;
						cd_redirect(basename(__FILE__).go_get());
				}
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
		form.ShowMiss {position:absolute;left:0;right:160px;}
		div.ShowMiss {position:absolute;width:150px;top:0;right:0;bottom:0;overflow:hide;}
		';
}
$JS_SCRIPT[]='
	.selected td {background-color:#d0d0d0;font-weight:bold}
	';
$JS_SCRIPT[]='</style>';

include('Common/Templates/head.php');

$q=safe_r_sql("Select ToNumDist, ToGolds, ToXNine from Tournament where ToId={$_SESSION['TourId']}");
$TOUR=safe_fetch($q);
if(!empty($archers)) {
	$arctmp=current($archers);
	$D=$arctmp->ElElimPhase+1;
	$_GET['T']=$arctmp->ElSession;
}

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
		<th><?php print get_text('Phase');?></th>
		<th><?php print get_text('Barcode','BackNumbers');?></th>
		<th><?php print get_text('Session');?></th>
	</tr>
	<tr>
		<td class="Center"><input type="checkbox" onclick="document.Frm.bib.focus()" name="Targets" <?php echo ((empty($_GET) or !empty($_GET['Targets'])) ? ' checked="checked"' : ''); ?>></td>
		<td class="Center"><input type="checkbox" onclick="document.Frm.bib.focus()" name="AutoEdit"  <?php echo (!empty($_GET['AutoEdit']) ? ' checked="checked"' : ''); ?>></td>
		<td class="Center"><input type="checkbox" onclick="document.Frm.bib.focus()" name="ShowMiss"  <?php echo ((empty($_GET) or !empty($_GET['ShowMiss'])) ? ' checked="checked"' : ''); ?>></td>
		<td class="Center"><select id="Distance" name="D"  onchange="document.Frm.bib.focus()"><option value="0"></option><?php

foreach(range(1,2) as $d) {
	echo '<option value="'.$d.'"'.(!empty($D) && $D==$d ? ' selected="selected"' : '').'>Elim '.$d.'</option>';
}
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
$q=safe_r_sql("Select distinct SesOrder, SesName from Session where SesType='E' and SesTournament={$_SESSION['TourId']} order by SesOrder");
while($r=safe_fetch($q)) echo '<option value="'.$r->SesOrder.'" '.(!empty($_GET['T']) && $_GET['T']==$r->SesOrder ? ' selected="selected"' : '').'>'.($r->SesName ? $r->SesName : $r->SesOrder).'</option>';
?></select></td>
</tr>
	<tr>
		<td class="Center" colspan="2"><input type="submit" value="<?php print get_text('CmdGo','Tournament');?>" id="Vai" onClick="javascript:SendBib();"></td>
		<td class="Center"><input type="button" value="<?php print get_text('BarcodeMissing','Tournament');?>" onClick="window.open('./GetScoreBarCodeMissing.php?S=E&D='+document.getElementById('Distance').value+'&T='+document.getElementById('Session').value);"></td>
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
		echo '<th colspan="4"></th>';
		echo '</tr>';
	foreach($archers as $archer) {
		$T=$archer->ElTargetNo[0];
		echo '<tr'.($archer->EnBib==$EnBib ? ' class="selected"' : '').'>';
			echo '<td>'.ltrim(substr($archer->ElTargetNo, 1), '0').'</td>';
			echo '<td>'.intval($D).'</td>';
			echo '<td>'.$archer->Firstname.'</td>';
			echo '<td>'.$archer->EnName.'</td>';
			echo '<td align="center">'.$archer->ElEventCode.'</td>';
			echo '<td align="right" style="font-size:150%"><b>'.$archer->Score.'</b></td>';
			echo '<td align="right" style="font-size:150%;padding:0 10px;"><b>'.$archer->Gold.'</b></td>';
			echo '<td align="right" style="font-size:150%;padding:0 10px;"><b>'.$archer->Xnine.'</b></td>';
			echo '<td align="center" style="font-size:80%"><b><a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->ElElimPhase.$_SESSION['BarCodeSeparator'].$archer->ElEventCode, 'C' => $archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->ElElimPhase.$_SESSION['BarCodeSeparator'].$archer->ElEventCode)).'">CONFIRM</a></b></td>';
			if($D) {
				echo '<td align="center" style="font-size:80%"><b>';
				echo '<a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->ElElimPhase.$_SESSION['BarCodeSeparator'].$archer->ElEventCode, 'C'=> 'EDIT')).'">Edit arrows</a><br/>';
				echo '<a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->ElElimPhase.$_SESSION['BarCodeSeparator'].$archer->ElEventCode, 'C' => 'EDIT2')).'">Edit totals</a></b>
					</td>';
// 				echo '<td align="center" style="font-size:80%"><b><a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->ElElimPhase.$_SESSION['BarCodeSeparator'].$archer->ElEventCode, 'C'=> 'REM10')).'">Remove 10</a>
// 					<br/><a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->ElElimPhase.$_SESSION['BarCodeSeparator'].$archer->ElEventCode, 'C'=> 'REMXNINE')).'">Remove X/Nine</a>
// 					<br/><a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->ElElimPhase.$_SESSION['BarCodeSeparator'].$archer->ElEventCode, 'C'=> 'REMALL')).'">Remove both</a></b>
// 					</td>';
// 				echo '<td align="center" style="font-size:80%"><b><a href="'.go_get(array('B'=>$archer->EnBib.$_SESSION['BarCodeSeparator'].$archer->ElElimPhase.$_SESSION['BarCodeSeparator'].$archer->ElEventCode, 'C'=> 'RESET')).'">Reset both</a></b>
// 					</td>';
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
	$MyQuery = "SELECT EnCode as Bib
			, EnName AS Name
			, upper(EnFirstName) AS FirstName
			, ElElimPhase AS Session
			, ElTargetNo AS TargetNo
			, CoCode AS NationCode, CoName AS Nation
			, ElEventCode
			, EnSubClass as SubClass
			, SesName
		FROM Entries
		inner JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
		inner JOIN Eliminations ON EnId=ElId and ElElimPhase=".($D-1)." and ElTournament=EnTournament and ElSession={$_GET['T']}
		inner JOIN Events ON EvTournament=ElTournament AND EvTeamEvent=0 and EvCode=ElEventCode
		left join Session on SesOrder=ElSession and SesTournament=EnTournament and SesType='E'
		WHERE EnAthlete=1
			AND EnTournament = {$_SESSION['TourId']} AND EnStatus<=1
			AND ElConfirm=0
		ORDER BY ElTargetNo ";
	$Q=safe_r_sql($MyQuery);
	while($r=safe_fetch($Q)) {
		echo '<tr><td>'.$r->TargetNo.'</td><td>'.$r->ElEventCode.'</td><td nowrap="nowrap">'.$r->FirstName.' '.$r->Name.'</td></tr>';
	}
	echo '</table></div>';
}
?>
<div id="idOutput"></div>
<?php
include('Common/Templates/tail.php');


function getScore(&$dist, $barcode, $strict=false) {
	global $EnBib, $T;
	$ret=array();
	$div='';
	$cls='';
	$filter=array();
// 	if(strlen($T)) {
// 		$filter[]="ElSession=$T";
// 	}
	if($barcode[0]=='@') {
		$barcode=substr($barcode,1);

		// left-pad with 0
		if(strlen($barcode)<4) $barcode=str_pad($barcode, 3, '0', STR_PAD_LEFT);

		$filter[]=" ElTargetNo like '".$barcode."%'";
	} elseif($barcode[0]=='#') {
		$filter[]=" (EnFirstname like ".StrSafe_DB(substr($barcode,1).'%')." or EnName like ".StrSafe_DB(substr($barcode,1).'%').")";
	} else {
		$tmp=@explode($_SESSION['BarCodeSeparator'], $barcode, 3);
		$bib=$tmp[0];
		$pha=$tmp[1];
		$dist=$tmp[1];
		$evt=$tmp[2];

		if(substr($bib, 0, 2)=='UU') $bib='_'.substr($bib, 2);

		$tmp="EnCode='$bib' and ElEventCode='$evt' and ElElimPhase='$pha'";
		$filter2="EnCode='$bib' and ElElimPhase='$pha'";
		$EnBib=$bib;
		if(!$strict and !empty($_GET['Targets'])) {
			$tmp="(left(ElTargetNo,3), ElSession)=(select left(ElTargetNo,3), ElSession from Eliminations inner join Entries on EnId=ElId and EnTournament={$_SESSION['TourId']} where $tmp)";
		}
		$filter[]=$tmp;
		if(empty($bib) or empty($evt)) return;
	}

	$filter=implode(' and ', $filter);

	$SQL="select ElTargetNo, EnCode EnBib, EnId, EnName, upper(EnFirstname) Firstname, ElEventCode, ElElimPhase, ElScore tScore, ElGold tGold, ElXnine tXnine, ElScore Score, ElGold Gold, ElXnine Xnine, ElSession
		from Eliminations
		inner join Entries on EnId=ElId and EnTournament={$_SESSION['TourId']} where $filter
		order by ElTargetNo, ElEventCode ";
	$q=safe_r_sql($SQL, false, true);
	while($r=safe_fetch($q)) $ret[$r->EnBib]=$r;
	if(!$ret) {
		$SQL="select ElTargetNo, EnCode EnBib, EnId, EnName, upper(EnFirstname) Firstname, ElEventCode, ElElimPhase, ElScore tScore, ElGold tGold, ElXnine tXnine, ElScore Score, ElGold Gold, ElXnine Xnine, ElSession
			from Eliminations inner join Entries on EnId=ElId and EnTournament={$_SESSION['TourId']} where $filter2
			order by ElTargetNo, EnDivision='$div' desc, EnClass='$cls' desc ";
		if($q=safe_r_sql($SQL, false, true)) {
			while($r=safe_fetch($q)) $ret[$r->EnBib]=$r;
		}
		if(count($ret)>1) $ret=array();
	}
	return $ret;
}

function updateArcher($archer, $D) {
    $SQL= "update Eliminations set ElConfirm=1 
        where ElId=$archer->EnId
            and ElElimPhase=$D
            and ElEventCode='$archer->ElEventCode'
            and ElTournament={$_SESSION['TourId']}";
    safe_w_sql($SQL);
}