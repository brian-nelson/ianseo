<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclParticipants, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	$nextTarget = +1;
	if(!empty($_REQUEST["FieldSeed"]) and $_REQUEST["FieldSeed"]==1) {
		$nextTarget = +2;
	}

	$MaxSession=0;
	$sessions=GetSessions('Q');
	$MaxSession=count($sessions);

	if(!empty($_REQUEST["Erase"]) && isset($_REQUEST["Session"]) && $_REQUEST["Session"]>=1 && $_REQUEST['Session']<=$MaxSession) {
		$Where="EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuSession=" . StrSafe_DB($_REQUEST['Session'])
			. ((isset($_REQUEST["Event"]) and preg_match("/^[0-9A-Z%_]+$/i",$_REQUEST["Event"])) ? " AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE " . StrSafe_DB($_REQUEST['Event']) : "");
		safe_w_sql("update Entries inner join Qualifications on EnId=QuId
			set EnTimestamp='".date('Y-m-d H:i:s')."'
			where QuTargetNo!='' and $Where");
		safe_w_sql("UPDATE Qualifications INNER JOIN Entries ON QuId=EnId
			SET QuTargetNo='', QuTarget=0, QuLetter='', QuBacknoPrinted=0
			WHERE $Where");
		header("Location: " . $_SERVER['PHP_SELF']);
		exit();
	}
	$TgtArray=NULL;

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants/Fun_JS.js"></script>',
		);

	$PAGE_TITLE=get_text('AutoTargetAssignment','Tournament');

	include('Common/Templates/head.php');
?>
<form name="Frm" method="GET" action="">
<table class="Tabella">
<tr><th class="Title" colspan="9"><?php print get_text('AutoTargetAssignment','Tournament');?></th></tr>
<tr class="Divider"><td colspan="9"></td></tr>
<?php

// in realtà 'stq query può esser rancata (mettendo a posto la condizione sotto)? prima c'erano pure i Tar4Session e gli Ath4target
	$Select
		= "SELECT ToNumSession "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)!=1) exit();

	$MyRow=safe_fetch($Rs);

echo '<tr>';
echo '<td class="Center">'.get_text('SelectSession','Tournament').'</td>';
echo '<td class="Center">'.get_text('FilterOnDivCl','Tournament').'</td>';
echo '<td class="Center">'.get_text('DrawType', 'Tournament').'</td>';
echo '<td class="Center">'.get_text('SeparateDivisions','Tournament').'</td>';
echo '<td class="Center">'.get_text('SeparateClasses','Tournament').'</td>';
echo '<td class="Center">'.get_text('TargetAssExclude','Tournament').'</td>';
echo '<td class="Center">'.get_text('TargetDoAssignment','Tournament').'</td>';
echo '<td class="Center" colspan="2">'.get_text('Targets','Tournament').'</td>';
//echo '<td>'..'</td>';
//echo '<td>'..'</td>';
//echo '<td>'..'</td>';
//echo '<td>'..'</td>';
//echo '<td>'..'</td>';
//echo '<td>'..'</td>';
//echo '<td>'..'</td>';
//echo '<td>'..'</td>';
echo '</tr>';
echo '<tr>';
	echo '<td class="Center">';
		echo '<select name="Session" id="Session">';
		echo '<option value="-1">---</option>';
		foreach ($sessions as $s)
			echo '<option value="' . $s->SesOrder . '"' . (isset($_REQUEST['Session']) AND $_REQUEST['Session']==$s->SesOrder ? ' selected' : '') . '>' . $s->Descr . '</option>';
		print '</select>';
	echo '</td>';
	echo '<td class="Center"><input type="text" maxlength="10" size="12" name="Event" id="Event" value="' . (isset($_REQUEST['Event']) ? $_REQUEST['Event'] : '') . '"></td>';
	echo '<td class="Center"><select name="FieldSeed" id="FieldSeed">
				<option value="">==></option>
				<option value="0"'.((isset($_REQUEST['FieldSeed']) and $_REQUEST['FieldSeed']==0) ? ' selected="selected"' : '').'>'.get_text('DrawNormal', 'Tournament').'</option>
				<option value="1"'.(((isset($_REQUEST['FieldSeed']) and $_REQUEST['FieldSeed']==1) or (!isset($_REQUEST['FieldSeed']) and $_SESSION["TourType"] >=9 and $_SESSION["TourType"] <=13)) ? ' selected="selected"' : '').'>' . get_text('DrawField3D', 'Tournament') . '</option>
				<option value="2"'.(((isset($_REQUEST['FieldSeed']) and $_REQUEST['FieldSeed']==2) ) ? ' selected="selected"' : '').'>' . get_text('DrawOris', 'Tournament') . '</option>
				<option value="3"'.(((isset($_REQUEST['FieldSeed']) and $_REQUEST['FieldSeed']==3) ) ? ' selected="selected"' : '').'>' . get_text('DrawOris', 'Tournament') . ' 2</option>
				</select>
			</td>';
	echo '<td class="Center"><input type="checkbox" name="GroupByDiv" id="GroupByDiv" value="1" ' . (isset($_REQUEST['GroupByDiv']) && $_REQUEST['GroupByDiv']=='1' ? 'checked' : '') . '></td>';
	echo '<td class="Center"><input type="checkbox" name="GroupByClass" id="GroupByClass" value="1" ' . (isset($_REQUEST['GroupByClass']) && $_REQUEST['GroupByClass']=='1' ? 'checked' : '') . '></td>';
	echo '<td class="Center"><input type="checkbox" name="Exclude" id="Exclude" value="1" ' . (isset($_REQUEST['Exclude']) && $_REQUEST['Exclude']=='1' ? 'checked' : '') . '></td>';
	echo '<td class="Center"><input type="checkbox" name="DoAssign" id="DoAssign" value="1" ' . (isset($_REQUEST['DoAssign']) && $_REQUEST['DoAssign']=='1' ? 'checked' : '') . '></td>';
	echo '<td class="Center">' . get_text('From','Tournament') . '&nbsp;<input type="text" maxlength="4" size="5" name="TgtFrom" id="TgtFrom" value="' . (isset($_REQUEST['TgtFrom']) ? $_REQUEST['TgtFrom'] : '') . '"></td>';
	echo '<td class="Center">' . get_text('To','Tournament') . '&nbsp;<input type="text" maxlength="4" size="5" name="TgtTo" id="TgtTo" value="' . (isset($_REQUEST['TgtTo']) ? $_REQUEST['TgtTo'] : '') . '"></td>';
//	echo '<td></td>';
//	echo '<td></td>';
//	echo '<td></td>';
//	echo '<td></td>';
//	echo '<td></td>';
//	echo '<td></td>';
//	echo '<td></td>';
//	echo '<td></td>';
//	echo '<td></td>';
echo '</tr>';

echo '<tr>';
echo '<td class="Center" colspan="9"><input type="submit" value="'.get_text('CmdOk').'">&nbsp;&nbsp;&nbsp;';
echo '<input type="submit" name="Erase" value="'.get_text('TargetAssErase','Tournament').'"></td>';
echo '</tr>';

	if(isset($_REQUEST["Session"]) && $_REQUEST["Session"]>=1 && $_REQUEST['Session']<=$MaxSession)
	{
		$MySes=GetSessions(null,false,array($_REQUEST['Session'].'_'.'Q'));

		if($MySes[0]->SesAth4Target>=4) {
		    if($_REQUEST['FieldSeed']==3) {
                $TgtArray=array('A','B','C','D');
            } else {
                $TgtArray=array('A','C','B','D');
            }
			$StartLetter='E';
			if($MySes[0]->SesAth4Target>4) {
				for($i=4;$i<$MySes[0]->SesAth4Target;$i++) {
					$TgtArray[] = $StartLetter++;
				}
			}
		} else {
            $q = safe_r_SQL("SELECT DISTINCT AtLetter FROM `AvailableTarget` WHERE `AtTournament` = {$_SESSION['TourId']} AND `AtSession` = {$MySes[0]->SesOrder} ORDER BY AtLetter ");
			while($r=safe_fetch($q)) {
                $TgtArray[] = $r->AtLetter;
            }
		}
	}
	$ArcPerButt=(empty($TgtArray) ? 0 : count($TgtArray));
	safe_free_result($Rs);
?>
<tr>
<td colspan="9">
<?php
if(isset($_REQUEST["Event"]) && isset($_REQUEST["Session"]) && isset($_REQUEST["TgtFrom"]) && isset($_REQUEST["TgtTo"])
	&& $_REQUEST["Session"]>=1 && $_REQUEST['Session']<=$MaxSession && preg_match("/^[0-9]+[A-F]?$/i",$_REQUEST["TgtFrom"]) && preg_match("/^[0-9]+[A-F]?$/i",$_REQUEST["TgtTo"]))
{
	/*
	 FIRST and ALWAYS grouped by Distances, TargetType
	 SECOND and according to the flags
	 - GroupByDiv
	 - GroupByClass
	 THIRD and ALWAYS grouped by Country

	 THEN

	 Each Group will be assigned a set of consecutive targets to fit them

	2019 WA Coach Committee decided to have a team shooting some on one line and some on another

	*/

	/*===========
	 calculates available targets
	 ============ */
	$CurIndex=0;
	$CurTarget=0;
	$CurPlace=0;
	$EndTarget=0;
	$EndPlace=0;

	// search for first place assignment
	if(!( $CurPlace=array_search(substr(strtoupper($_REQUEST["TgtFrom"]),-1,1),$TgtArray) )) $CurPlace=0;
	$CurTarget=intval($_REQUEST["TgtFrom"]);

	// search last place to assign
	if(!( $EndPlace=array_search(substr(strtoupper($_REQUEST["TgtTo"]),-1,1),$TgtArray) )) $EndPlace=$ArcPerButt-1;
	$EndTarget=intval($_REQUEST["TgtTo"]);


	$TgtList = array();
	$TgtAvailable = array();

	for($i=$CurTarget; $i<=$EndTarget; $i++)
	{
		for($j=0; $j<$ArcPerButt; $j++)
		{
			if(($i==$CurTarget && $j>=$CurPlace) || ($i==$EndTarget && $j<=$EndPlace) || ($i>$CurTarget && $i<$EndTarget))
			{
				$TgtList[] = str_pad($i . $TgtArray[$j],(TargetNoPadding+1),'0',STR_PAD_LEFT);
				$MySql = "SELECT QuId FROM Qualifications INNER JOIN Entries ON QuId = EnId WHERE QuTargetNo=" . StrSafe_DB($_REQUEST['Session'].str_pad($i . $TgtArray[$j],(TargetNoPadding+1),'0',STR_PAD_LEFT)) . " AND EnTournament=" . StrSafe_DB($_SESSION['TourId']);
				$Rs = safe_r_sql($MySql);
				if(safe_num_rows($Rs)==1)
					$TgtAvailable[str_pad($i . $TgtArray[$j],(TargetNoPadding+1),'0',STR_PAD_LEFT)] = 0;
				else
					$TgtAvailable[str_pad($i . $TgtArray[$j],(TargetNoPadding+1),'0',STR_PAD_LEFT)] = 1;
			}
		}
	}

	// Defines the offset for the new 2019 Committee decision
    $Offset2019 = $_REQUEST['FieldSeed']==3 ? 1 : 0;

	echo $CurTarget . "." . $TgtArray[$CurPlace] . " -- " . $EndTarget . "." . $TgtArray[$EndPlace] . "<br>";

	$Distances=getDistFields();

	$GroupFields='EnTargetFace';
	$SortOrder=array();
	$SortOrder[]='DivViewOrder';
	$SortOrder[]='ClViewOrder';
	$SortOrder[]='rand()';

	$GroupIndex='%1$s|';
	if(!empty($_REQUEST['GroupByDiv'])) {
		$GroupFields.=', EnDivision';
		$GroupIndex.='%2$s';
	}

	if(!empty($_REQUEST['GroupByClass'])) {
		$GroupFields.=', EnClass';
		$GroupIndex.= '%3$s';
	}

	$GroupFields.=', EnCountry';
	$GroupIndex.='|%4$s';

	$MySql = "SELECT "
		. "  EnId, EnWChair, EnSitting, EnDoubleSpace "
		. "  , EnDivision, EnClass, EnTargetFace, CoCode EnCountry "
		. "  , CONCAT(TRIM(EnDivision),TRIM(Enclass)) Event "
		. "  , concat_ws('|', concat_ws('-', TfW1, TfW2, TfW3, TfW4, TfW5, TfW6, TfW7, TfW8), concat_ws('-', TfT1, TfT2, TfT3, TfT4, TfT5, TfT6, TfT7, TfT8)) Target"
		. " FROM Entries e "
		. "  INNER JOIN Countries ON EnCountry=CoId "
		. "  INNER JOIN Qualifications ON EnId=QuId "
		. "  INNER JOIN Divisions ON EnDivision=DivId and EnTournament=DivTournament and DivAthlete=1 "
		. "  INNER JOIN Classes ON EnClass=ClId and EnTournament=ClTournament and ClAthlete=1 "
		. "  INNER JOIN TargetFaces ON EnTargetFace=TfId and EnTournament=TfTournament "
		. " WHERE"
		. "  EnTournament=" . StrSafe_DB($_SESSION['TourId'])
		. "  AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE " . StrSafe_DB($_REQUEST['Event'])
		. "  AND QuSession=" . StrSafe_DB($_REQUEST['Session'])
		.    (isset($_REQUEST['Exclude']) && $_REQUEST['Exclude']=='1' ? " AND substr(QuTargetNo,2)='' " : "")
		. " ORDER BY ".implode(',',$SortOrder);
	$q=safe_r_SQL($MySql);

	// group index changes according to flags
	$Entries=array();
	$Groups=array();
	$GroupsTotals=array();
	$Hspace=array();
	$Vspace=array();
	while($r=safe_fetch($q)) {
		$index=sprintf($GroupIndex, $Distances[$r->Event], $r->EnDivision, $r->EnClass, $r->Target);
		$Entries[$index][$r->EnCountry][]=$r->EnId;

		if(empty($Groups[$index][$r->EnCountry])) $Groups[$index][$r->EnCountry]=0;
		$Groups[$index][$r->EnCountry]++;

		if(empty($GroupsTotals[$index])) $GroupsTotals[$index]=0;
		$GroupsTotals[$index]++;

		if($r->EnWChair or $r->EnSitting or $r->EnDivision=='VI') {
			if($ArcPerButt>=4) {
				$Vspace[]=$r->EnId;
				$Groups[$index][$r->EnCountry]++;
				$GroupsTotals[$index]++;
			} else {
				$Groups[$index][$r->EnCountry]+=0.3;
				$GroupsTotals[$index]+=0.3;
			}
		}
		if($r->EnDoubleSpace) {
			$Hspace[]=$r->EnId;
			$Groups[$index][$r->EnCountry]+=(in_array($r->EnId,$Vspace) ? 2 : 1);
			$GroupsTotals[$index]+=(in_array($r->EnId,$Vspace) ? 2 : 1);
		}
	}

	krsort($Groups);

	$NotAssigned=array();

	//$LastLetter=chr(ord('A')+$ArcPerButt-1);
    $LastLetter=$TgtArray[count($TgtArray)-1];

	$firstLine=array('AB' . ($ArcPerButt>4?'CD':''), 'CD');
	$LeftSide=array('AC' . ($ArcPerButt>4?'E':''), 'BD');

	$IndGap=0;

	// foreach group reassigns the targets so that each group fits
	foreach($Groups as $Index => $Group) {
		$ToggleZigZag=0;

		// sorts group DESC by numerosity

// 		$Group = sortGroup($Group); //Proposed procedure to ORIS commeetee
		arsort($Group); //Otherwise standard one
		$Assignments=array();
		echo '<br><br><b>'.$Index.'</b><br>';

		// each main group should start on 'A'
		// so "switch off" until the first "A"
		$tmpIndex=array_search(1, $TgtAvailable);
		while($tmpIndex and substr($tmpIndex,-1)!='A') {
			$TgtAvailable[$tmpIndex]=0;
			$tmpIndex=array_search(1, $TgtAvailable);
		}

		// needs that many slots to fit everybody
		$GroupAvailable=array();
		foreach(array_slice(array_keys($TgtAvailable, 1), 0, ($ArcPerButt*(1+intval($GroupsTotals[$Index])/$ArcPerButt)-1)) as $key) {
			$GroupAvailable[$key] = $TgtAvailable[$key];
		}

		// get archers of that group
		$Archers=array();
		$CurIndex=0;
		$CurLastIndex=count($GroupAvailable)-1;

		$CurIndices=array_keys($GroupAvailable, 1);

		$BottomCountry=false;

		while($Group) {
// 		foreach($Group as $Country => $Users) {
            $Country=key($Group);
            $Users=current($Group);
			if($BottomCountry) {
				end($Group);
                $Country=key($Group);
                $Users=current($Group);
				$Group=array_slice($Group, 0, -1, true);
			} else {
				$Group=array_slice($Group, 1, count($Group), true);
			}

			if(!$BottomCountry) {
// 				if($_REQUEST['FieldSeed']==2) {
// 					$ToggleZigZag=1-$ToggleZigZag;
	// 				if()
// 				} else {
					// if normal seed recreates the available indeices for every country!
					$CurIndices=array_keys($GroupAvailable, 1);
// 				}

				if($ToggleZigZag) {
					// if toggle zigzag then inverts the array to choose from!
					$CurIndices=array_reverse($CurIndices);
				}

				if($CurIndices) {
					if($ToggleZigZag) {
						$CurIndex=array_search($CurIndices[0], array_reverse(array_keys($GroupAvailable), true));
					} else {
						$CurIndex=array_search($CurIndices[0], array_keys($GroupAvailable));
					}
				}
			}

			if($_REQUEST['FieldSeed']==2 or $_REQUEST['FieldSeed']==3) {
				if($BottomCountry) {
					$IndGap++;
					if($IndGap==2) {
						$BottomCountry=(!$BottomCountry);
					}
				} else {
					$BottomCountry=(!$BottomCountry);
					$IndGap=0;
				}
			}

			if(!$Country) debug_svela($Group);
			foreach($Entries[$Index][$Country] as $Archer) {
				// if no place at all skips the loop
				if(!$CurIndices) {
					$NotAssigned[]=$Archer;
					continue;
				}

				if(!($ThisTarget=key(array_slice($GroupAvailable, $CurIndex, 1, true))
						and $GroupAvailable[$ThisTarget])) {
					// target is not there or is not free, finds the next "same" free or the first free
					while(($ToggleZigZag ? $CurIndex>0 : $CurIndex < count($GroupAvailable)) and !$GroupAvailable[$ThisTarget]) {
						if($ToggleZigZag) {
							$CurIndex-=($ArcPerButt*$nextTarget);
						} else {
							$CurIndex+=($ArcPerButt*$nextTarget);
						}
						$ThisTarget=key(array_slice($GroupAvailable, $CurIndex, 1, true));
					}

					// gone out of scope, resets to the first slot available
					if($CurIndex>=count($GroupAvailable)) {
						$CurIndices=array_keys($GroupAvailable, 1);
						if($ToggleZigZag) {
							// if toggle zigzag then inverts the array to choose from!
							$CurIndices=array_reverse($CurIndices);
						}
						if($CurIndices) {
							if($ToggleZigZag) {
								$CurIndex=array_search($CurIndices[0], array_reverse(array_keys($GroupAvailable), true));
							} else {
								$CurIndex=array_search($CurIndices[0], array_keys($GroupAvailable));
							}
						}
						$ThisTarget=key(array_slice($GroupAvailable, $CurIndex, 1, true));
					}
				}

				if($ArcPerButt>=4 and in_array($Archer, $Vspace)) {
					// tries assigning the V archers: Wheelchair, sitting etc
					// only needed if archers per butt are 4 or more
					// implies archers shoot 2 per butt at the same time
					// search the first occurence of a "first line" place with a concurrent second row place
					$ind=array_search($ThisTarget, $CurIndices);
					$found=false;
					while(!empty($CurIndices[$ind]) and !$found) {
						$found=(strstr($firstLine[$ToggleZigZag], $let=substr($CurIndices[$ind],-1))
							and $targ2=str_replace($let, $TgtArray[array_search($let, $TgtArray)+($ToggleZigZag ? -1 : 1)], $CurIndices[$ind])
							and in_array($targ2, $CurIndices)
							and $GroupAvailable[$targ2]
							and $GroupAvailable[$CurIndices[$ind]]
							);
						if(!$found) {

							$ind++;
						} else {
						}
					}

					// ecception thrown if no place!
					if(!$found) {
						$NotAssigned[]=$Archer;
					} else {
						$Assignments[$CurIndices[$ind]]=ArcherAssign($Archer, $CurIndices[$ind], $Country);
						// occupies the targets...
						$GroupAvailable[$CurIndices[$ind]]=0;
						$GroupAvailable[$targ2]=0;
						if($ToggleZigZag) {
							$CurIndex=array_search($CurIndices[$ind], array_reverse(array_keys($GroupAvailable), true));
						} else {
							$CurIndex=array_search($CurIndices[$ind], array_keys($GroupAvailable));
						}
					}
				} elseif($ArcPerButt>=4 and in_array($Archer, $Hspace)) {
					// tries assigning the H archers: crossbows etc
					//
					$ind=0;
					$found=false;
					while(!empty($CurIndices[$ind]) and !$found) {
						$found=(strstr($LeftSide[$ToggleZigZag], $let=substr($CurIndices[$ind],-1))
							and $targ2=str_replace($let, $TgtArray[2+array_search($let, $TgtArray)], $CurIndices[$ind])
							and in_array($targ2, $CurIndices)
							and $GroupAvailable[$targ2]
							and $GroupAvailable[$CurIndices[$ind]]
							);
						if(!$found) $ind++;
					}

					// ecception thrown if no place!
					if(!$found) {
						$NotAssigned[]=$Archer;
					} else {
						$Assignments[$CurIndices[$ind]]=ArcherAssign($Archer, $CurIndices[$ind], $Country);
						// occupies the targets...
						$GroupAvailable[$CurIndices[$ind]]=0;
						$GroupAvailable[$targ2]=0;
						if($ToggleZigZag) {
							$CurIndex=array_search($CurIndices[$ind], array_reverse(array_keys($GroupAvailable), true));
						} else {
							$CurIndex=array_search($CurIndices[$ind], array_keys($GroupAvailable));
						}
					}
				} elseif(!empty($GroupAvailable[$ThisTarget])) {
					$Assignments[$ThisTarget]=ArcherAssign($Archer, $ThisTarget, $Country);
					$GroupAvailable[$ThisTarget]=0;
				} else {
					$NotAssigned[]=$Archer;
				}

				// goes to following slot
                if($Offset2019) {
                    if(substr($ThisTarget, -1)==chr(64+$ArcPerButt)) {
                        if($ToggleZigZag) {
                            $CurIndex-=$Offset2019;
                        } else {
                            $CurIndex+=$Offset2019;
                        }
                    } else {
                        if($ToggleZigZag) {
                            $CurIndex-=($ArcPerButt*$nextTarget)-$Offset2019;
                        } else {
                            $CurIndex+=($ArcPerButt*$nextTarget)+$Offset2019;
                        }
                    }
                } else {
                    if($ToggleZigZag) {
                        $CurIndex-=($ArcPerButt*$nextTarget)-$Offset2019;
                    } else {
                        $CurIndex+=($ArcPerButt*$nextTarget)+$Offset2019;
                    }
                }
				// gone out of scope, resets to the first slot available
				if($CurIndex>=count($GroupAvailable) or $CurIndex<0) {
					$CurIndices=array_keys($GroupAvailable, 1);
					if($ToggleZigZag) {
						// if toggle zigzag then inverts the array to choose from!
						$CurIndices=array_reverse($CurIndices);
					}
					if($CurIndices) {
						if($ToggleZigZag) {
							$CurIndex=array_search($CurIndices[0], array_reverse(array_keys($GroupAvailable), true));
						} else {
							$CurIndex=array_search($CurIndices[0], array_keys($GroupAvailable));
						}
					}
				}
			}
		}

		ksort($Assignments);
		$OldAss='';
		foreach($Assignments as $k => $v) {
			if($OldAss!=intval($k)) {
				echo '<br/>';
			}
			echo $v. '<br/>';
			$OldAss=intval($k);
		}

		// occupies all the occupied slots in the main array
		foreach($GroupAvailable as $k => $v) {
			$TgtAvailable[$k]=$v;
		}
	}

	if($NotAssigned) {
		sort($NotAssigned);
		echo "<br><strong>".get_text('TargetAssError','Tournament')."</strong><br>";
		$q=safe_r_sql("select CoCode EnCountry, EnFirstName, EnName, EnDivision, EnClass from Entries inner join Countries on EnCountry=CoId where EnID in (".implode(',', $NotAssigned).")");
		while($r=safe_fetch($q)) {
			echo "$r->EnCountry - $r->EnFirstName $r->EnName ($r->EnDivision$r->EnClass)<br/>\n";
		}
		echo "<br><br>";
	}
}
?>
</td>
</tr>
</table>
</form>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');

function ArcherAssign($Archer, $Target, $Country) {

	if(!empty($_REQUEST['DoAssign'])) {
		$UpdateQry = "UPDATE Qualifications SET QuTimestamp=QuTimestamp, QuTarget=".intval($Target).", QuLetter='".substr($Target, -1)."', QuTargetNo=" . StrSafe_DB($_REQUEST['Session'].$Target)
			. " WHERE QuId = $Archer";
		$RsUpd=safe_w_sql($UpdateQry);
		if(safe_w_affected_rows()) {
			safe_w_sql("Update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where EnId=$Archer");
			safe_w_sql("UPDATE Qualifications SET QuBacknoPrinted=0, QuTimestamp=QuTimestamp WHERE QuId = $Archer");
		}
	}
	$q=safe_r_sql("select * from Entries where EnID=$Archer");
	$r=safe_fetch($q);
	return get_text('TargetAssigned', 'Tournament', $Target) . ' - ' . $Country . " - $r->EnDivision$r->EnClass ($r->EnName $r->EnFirstName)". ($r->EnWChair ? ' - ' . get_text('WheelChair', 'Tournament') :'') . ($r->EnSitting ? ' - Sitting ' :'');
}

function sortGroup($group) {
	arsort($group);
	$tmp = array();
	$ucnt = 0;
	$lcnt = count($group)-1;
	while($ucnt<$lcnt) {
		$tmp += array_slice($group,$ucnt++,1);
		if($ucnt<=$lcnt)
			$tmp += array_slice($group,$ucnt++,1);
		if($ucnt<=$lcnt)
			$tmp += array_slice($group,$lcnt--,1);
	}
	return $tmp;
}

function getDistFields() {
	$ret=array();

	$q=safe_r_SQL("select distinct EnDivision, EnClass from Entries where EnTournament={$_SESSION['TourId']}");
	while($r=safe_fetch($q)) {
		$cat=trim($r->EnDivision).trim($r->EnClass);

		$MySql="select"
			. " concat_ws('#', Td1, Td2, Td3, Td4, Td5, Td6, Td7, Td8) QryFields "
			. "from"
			. " TournamentDistances "
			. "WHERE"
			. " TdType={$_SESSION['TourType']} "
			. " AND TdTournament = {$_SESSION['TourId']} "
			. " AND '$cat' LIKE TdClasses "
			. "order by"
			. " TdTournament=0"
			. ", '$cat' = TdClasses desc"
			. ", left(TdClasses,1)!='_' and left(TdClasses,1)!='%' desc"
			. ", left(TdClasses,1)='_' desc"
			. ", TdClasses desc"
			. ", TdClasses='%' "
			. "LIMIT 1";

		$t=safe_r_sql($MySql);
		if($u=safe_fetch($t)) $ret[$cat]=str_replace(array('-','#'), array('','-'), $u->QryFields);
	}

	return $ret;
}

?>