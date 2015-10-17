<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	$nextTarget = +1;
	if(($_SESSION["TourType"] >=9 && $_SESSION["TourType"] <=13))
		$nextTarget = +2;

	$MaxSession=0;
	$sessions=GetSessions('Q');
	$MaxSession=count($sessions);

	if(!empty($_REQUEST["Erase"]) && isset($_REQUEST["Session"]) && $_REQUEST["Session"]>=1 && $_REQUEST['Session']<=$MaxSession)
	{
		$UpdateQry = "UPDATE Qualifications INNER JOIN Entries ON QuId=EnId SET QuTargetNo='', QuBacknoPrinted=0 WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuSession=" . StrSafe_DB($_REQUEST['Session'])
			. (isset($_REQUEST["Event"]) && strlen($_REQUEST["Event"])>0 ? " AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE " . StrSafe_DB($_REQUEST['Event']) : "");
		$RsUpd=safe_w_sql($UpdateQry);
		header("Location: " . $_SERVER['PHP_SELF']);
		exit();
	}
	$TgtArray=NULL;

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants/Fun_JS.js"></script>',
		);

	$PAGE_TITLE=get_text('AutoTargetAssignment','Tournament');

	include('Common/Templates/head.php');
?>
<form name="Frm" method="GET" action="">
<table class="Tabella">
<tr><th class="Title" colspan="8"><?php print get_text('AutoTargetAssignment','Tournament');?></th></tr>
<tr class="Divider"><td colspan="8"></td></tr>
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
//echo '<td>'..'</td>';
echo '</tr>';
echo '<tr>';
	echo '<td class="Center">';
		echo '<select name="Session" id="Session">' . "\n";
		echo '<option value="-1">---</option>' . "\n";
		foreach ($sessions as $s)
			echo '<option value="' . $s->SesOrder . '"' . (isset($_REQUEST['Session']) && $_REQUEST['Session']==$s->SesOrder ? ' selected' : '') . '>' . $s->Descr . '</option>';
		print '</select>';
	echo '</td>';
	echo '<td class="Center"><input type="text" maxlength="4" size="5" name="Event" id="Event" value="' . (isset($_REQUEST['Event']) ? $_REQUEST['Event'] : '') . '"></td>';
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
echo '<td class="Center" colspan="8"><input type="submit" value="'.get_text('CmdOk').'">&nbsp;&nbsp;&nbsp;';
echo '<input type="submit" name="Erase" value="'.get_text('TargetAssErase','Tournament').'"></td>';
echo '</tr>';

	if(isset($_REQUEST["Session"]) && $_REQUEST["Session"]>=1 && $_REQUEST['Session']<=$MaxSession)
	{
		$MySes=GetSessions(null,false,array($_REQUEST['Session'].'_'.'Q'));

		if($MySes[0]->SesAth4Target>=4) {
			$TgtArray=array('A','C','B','D');
			$StartLetter='E';
			if($MySes[0]->SesAth4Target>4) for($i=4;$i<$MySes[0]->SesAth4Target;$i++) $TgtArray[] = $StartLetter++;
		} else {
			$StartLetter='A';
			for($i=0;$i<$MySes[0]->SesAth4Target;$i++) $TgtArray[] = $StartLetter++;
		}
	}
	safe_free_result($Rs);
?>
<tr>
<td colspan="8">
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
	if(!( $EndPlace=array_search(substr(strtoupper($_REQUEST["TgtTo"]),-1,1),$TgtArray) )) $EndPlace=count($TgtArray)-1;
	$EndTarget=intval($_REQUEST["TgtTo"]);


	$TgtList = array();
	$TgtAvailable = array();

	for($i=$CurTarget; $i<=$EndTarget; $i++)
	{
		for($j=0; $j<count($TgtArray); $j++)
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

//	$MySql = "SELECT "
//		. "  COUNT(EnId)+sum(EnWChair)+sum(EnSitting)+sum(EnDoubleSpace)+sum(EnDivision='VI') as Archers "
//		. "  , EnDivision, EnClass, EnTargetFace, EnCountry "
//		. "  , CONCAT(TRIM(EnDivision),TRIM(Enclass)) Event "
//		. "  , concat_ws('|', concat_ws('-', TfW1, TfW2, TfW3, TfW4, TfW5, TfW6, TfW7, TfW8), concat_ws('-', TfT1, TfT2, TfT3, TfT4, TfT5, TfT6, TfT7, TfT8)) Target"
//		. " FROM Entries e "
//		. "  INNER JOIN Qualifications ON EnId=QuId "
//		. "  INNER JOIN Divisions ON EnDivision=DivId and EnTournament=DivTournament and DivAthlete=1 "
//		. "  INNER JOIN Classes ON EnClass=ClId and EnTournament=ClTournament and ClAthlete=1 "
//		. "  INNER JOIN TargetFaces ON EnTargetFace=TfId and EnTournament=TfTournament "
//		. " WHERE"
//		. "  EnTournament=" . StrSafe_DB($_SESSION['TourId'])
//		. "  AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE " . StrSafe_DB($_REQUEST['Event'])
//		. "  AND QuSession=" . StrSafe_DB($_REQUEST['Session'])
//		.    (isset($_REQUEST['Exclude']) && $_REQUEST['Exclude']=='1' ? " AND QuTargetNo='' " : "")
//		. " GROUP BY "
//		. "  Target desc, EnDivision, EnClass, EnCountry "
//		. " ORDER BY ".implode(',',$SortOrder);

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

	//debug_svela($Distances);

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

		if($r->EnWChair or $r->EnSitting or $r->EnDivision=='VI' ) {
			$Vspace[]=$r->EnId;
			$Groups[$index][$r->EnCountry]++;
			$GroupsTotals[$index]++;
		}
		if($r->EnDoubleSpace) {
			$Hspace[]=$r->EnId;
			$Groups[$index][$r->EnCountry]+=(in_array($r->EnId,$Vspace) ? 2 : 1);
			$GroupsTotals[$index]+=(in_array($r->EnId,$Vspace) ? 2 : 1);
		}
	}

	krsort($Groups);

	$NotAssigned=array();

	$firstLine='AB' . (count($TgtArray)>4?'CD':'');
	$LeftSide='AC' . (count($TgtArray)>4?'E':'');

	// foreach group reassigns the targets so that each group fits
	foreach($Groups as $Index => $Group) {
		// sorts group DESC by numerosity
		
//		$Group = sortGroup($Group); //Proposed procedure to ORIS commeetee
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
		foreach(array_slice(array_keys($TgtAvailable, 1), 0, $GroupsTotals[$Index]) as $key) {
			$GroupAvailable[$key] = $TgtAvailable[$key];
		}

		// get archers of that group
		$Archers=array();
		$CurIndex=0;
		
		foreach($Group as $Country => $Users) {
			$CurIndices=array_keys($GroupAvailable, 1);
			if($CurIndices) $CurIndex=array_search($CurIndices[0], array_keys($GroupAvailable));
			foreach($Entries[$Index][$Country] as $Archer) {
				// if no place at all skips the loop
				if(!$CurIndices) {
					$NotAssigned[]=$Archer;
					continue;
				}

				if(!($ThisTarget=key(array_slice($GroupAvailable, $CurIndex, 1, true))
				and $GroupAvailable[$ThisTarget])) {
					// target is not there or is not free, finds the next "same" free or the first free
					while(!$GroupAvailable[$ThisTarget] and $CurIndex < count($GroupAvailable)) {
						$CurIndex+=count($TgtArray);
						$ThisTarget=key(array_slice($GroupAvailable, $CurIndex, 1, true));
					}

					// gone out of scope, resets to the first slot available
					if($CurIndex>=count($GroupAvailable)) {
						$CurIndices=array_keys($GroupAvailable, 1);
						if($CurIndices) 
							$CurIndex=array_search($CurIndices[0], array_keys($GroupAvailable));
						$ThisTarget=key(array_slice($GroupAvailable, $CurIndex, 1, true));
					}
				}
				if(count($TgtArray)>=4 and in_array($Archer, $Vspace)) {
					// tries assigning the V archers: Wheelchair, sitting etc
					// only needed if archers per butt are 4 or more
					// implies archers shoot 2 per butt at the same time
					// search the first occurence of a "first line" place with a concurrent second row place
					$ind=0;
					$found=false;
					while(!empty($CurIndices[$ind]) and !$found) {
						$found=(strstr($firstLine, $let=substr($CurIndices[$ind],-1))
							and $targ2=str_replace($let, $TgtArray[1+array_search($let, $TgtArray)], $CurIndices[$ind])
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
						$Assignments[]=ArcherAssign($Archer, $CurIndices[$ind], $Country);
						// occupies the targets...
						$GroupAvailable[$CurIndices[$ind]]=0;
						$GroupAvailable[$targ2]=0;
						$CurIndex=array_search($CurIndices[$ind], array_keys($GroupAvailable));
					}
				} elseif(count($TgtArray)>=4 and in_array($Archer, $Hspace)) {
					// tries assigning the H archers: crossbows etc
					//
					$ind=0;
					$found=false;
					while(!empty($CurIndices[$ind]) and !$found) {
						$found=(strstr($LeftSide, $let=substr($CurIndices[$ind],-1))
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
						$Assignments[]=ArcherAssign($Archer, $CurIndices[$ind], $Country);
						// occupies the targets...
						$GroupAvailable[$CurIndices[$ind]]=0;
						$GroupAvailable[$targ2]=0;
						$CurIndex=array_search($CurIndices[$ind], array_keys($GroupAvailable));
					}
				} elseif(!empty($GroupAvailable[$ThisTarget])) {
					$Assignments[]=ArcherAssign($Archer, $ThisTarget, $Country);
					$GroupAvailable[$ThisTarget]=0;
				} else {
					$NotAssigned[]=$Archer;
				}

				// goes to following slot
				$CurIndex+=(count($TgtArray)*$nextTarget);
				// gone out of scope, resets to the first slot available
				if($CurIndex>=count($GroupAvailable)) {
					$CurIndices=array_keys($GroupAvailable, 1);
					if($CurIndices) 
						$CurIndex=array_search($CurIndices[0], array_keys($GroupAvailable));
				}
			}
		}

		sort($Assignments);
		echo implode('<br>', $Assignments). '<br>';

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
		$UpdateQry = "UPDATE Qualifications SET QuTargetNo=" . StrSafe_DB($_REQUEST['Session'].$Target)
			. " WHERE QuId = $Archer";
		$RsUpd=safe_w_sql($UpdateQry);
		if(safe_w_affected_rows()) safe_w_sql("UPDATE Qualifications SET QuBacknoPrinted=0 WHERE QuId = $Archer");
	}
	$q=safe_r_sql("select * from Entries where EnID=$Archer");
	$r=safe_fetch($q);
	return get_text('TargetAssigned', 'Tournament', $Target) . ' - ' . $Country . " - $r->EnDivision$r->EnClass ($r->EnName $r->EnFirstName)";
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