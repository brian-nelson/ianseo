<?php
if (!defined('IN_PHP')) CD_redirect();

// Show the already made Chain
$RuleChain='<table class="Tabella">';
$q=safe_r_sql("SELECT TVSequence.*,TVSOrder=(SELECT COUNT(*) FROM TVSequence WHERE TVSRule=$RuleId AND TVSTournament=$TourId) as last FROM TVSequence WHERE TVSRule=$RuleId AND TVSTournament=$TourId ORDER BY TVSOrder");
if(safe_num_rows($q)) {
	$RuleChain.='<tr>';
	$RuleChain.='<th class="TitleCenter">'.get_text('Order','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('Type','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('Name','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('TVFilter-Display','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('TVDefault-Scroll','Tournament').'</th>';
	$RuleChain.='</tr>';
	while($r=safe_fetch($q)) $RuleChain.=decode_chain($r, false);
} else {
	$RuleChain.='<tr><td>'.get_text('TVOutNoRules','Tournament').'</td></tr>';
}
$RuleChain.='</table>';

?>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVDefinedChain','Tournament');?></th>
<td><?php echo $RuleChain; ?></td>
</tr>
</table>
<br/>
<table class="Tabella">
<?php

/********************************************
 *
 * edits the Tournament Content
 *
 * *********************************************/

// Add a new piece to the chain

$Arr_EventIndRule = array();	// Eventi Ind presenti nella regola
$Arr_EventTeamRule = array();	// Eventi Team presenti nella regola

$Arr_EventInd = array();	// Eventi Ind del torneo
$Arr_EventTeam = array();	// Eventi Team del torneo

$Arr_PhaseInd = array();	// fasi fattibili ind
$Arr_PhaseTeam = array();	// fasi fattibili team

$Arr_PhaseIndRule = array();	// fasi Ind presenti nella regola
$Arr_PhaseTeamRule = array();	// fasi Team presenti nella regola

$NumSession = 0;

// Eventi nel torneo (finali)
$Select
	= "SELECT EvCode,EvEventName,EvTeamEvent "
	. "FROM Events "
	. "WHERE EvTournament=" . StrSafe_DB($TourId) . " "
	. "ORDER BY EvTeamEvent ASC, EvProgr ";
$Rs=safe_r_sql($Select);
$i=0;
$t=0;
if (safe_num_rows($Rs))
{
	while ($MyRow=safe_fetch($Rs))
	{
		if ($MyRow->EvTeamEvent==0)
		{
			$Arr_EventInd['QUAL'][$i][0]=$MyRow->EvCode;
			$Arr_EventInd['QUAL'][$i][1]=$MyRow->EvEventName;
			++$i;
		}
		elseif ($MyRow->EvTeamEvent==1)
		{
			$Arr_EventTeam['QUAL'][$t][0]=$MyRow->EvCode;
			$Arr_EventTeam['QUAL'][$t][1]=$MyRow->EvEventName;
			++$t;
		}
	}
}

// Eventi nel torneo (classi ind)
$Select
	= "SELECT DISTINCT EnDivision,EnClass,DivDescription,ClDescription "
	. "FROM Entries "
	. "INNER JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament AND DivAthlete=1 "
	. "INNER JOIN Classes ON EnClass=ClId AND EnTournament=ClTournament AND ClAthlete=1 "
	. "WHERE EnTournament=" . StrSafe_DB($TourId) . " "
	. "ORDER BY DivViewOrder, ClViewOrder ";
$Rs=safe_r_sql($Select);

if (safe_num_rows($Rs))
{
	while ($MyRow=safe_fetch($Rs))
	{
		$Arr_EventInd['CLAS'][$i][0]=$MyRow->EnDivision . $MyRow->EnClass;
		$Arr_EventInd['CLAS'][$i][1]=get_text($MyRow->DivDescription,'','',true) . ' - ' . get_text($MyRow->ClDescription,'','',true);
		++$i;
	}
}

// Eventi nel torneo (classi team)
	$Select="SELECT distinct EnDivision MyDiv, EnClass MyClass, DivDescription, ClDescription
FROM Entries
inner join Divisions on EnDivision=DivId AND EnTournament=DivTournament AND DivAthlete=1
inner join Classes on EnClass=ClId AND EnTournament=ClTournament AND ClAthlete=1
WHERE EnTournament=" . StrSafe_DB($TourId) . "
GROUP BY EnDivision, EnClass, EnCountry
HAVING COUNT(EnId)>=3
ORDER BY DivViewOrder, ClViewOrder	";
	$Rs=safe_r_sql($Select);
//	debug_svela($Select);
	if (safe_num_rows($Rs))
	{
		while ($MyRow=safe_fetch($Rs))
		{
			$Arr_EventTeam['CLAS'][$t][0]=$MyRow->MyDiv . $MyRow->MyClass;
			$Arr_EventTeam['CLAS'][$t][1]=get_text($MyRow->DivDescription,'','',true) . ' - ' . get_text($MyRow->ClDescription,'','',true);
			++$t;
		}
	}

// fasi fattibili ind
	$Select
		= "SELECT MAX(EvFinalFirstPhase) AS Phase FROM Events where EvTeamEvent=0 and EvTournament=$TourId";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs))
	{
		$MyRow=safe_fetch($Rs);
		$i2=64;
		$pp=$MyRow->Phase;
		$i=0;
		while ($pp>=1)
		{
			$Arr_PhaseInd[$i][0]=$pp;
			$Arr_PhaseInd[$i][1]= get_text($pp . '_Phase');
			if($pp==48) $Arr_PhaseInd[$i][1].= ' ('.get_text('64_Phase').')';
			if($pp==24) {
				$Arr_PhaseInd[$i][1].= ' ('.get_text('32_Phase').')';
				$pp=32;
			}
			$Arr_PhaseInd[$i][0]=$pp;
			$pp/=2;
			++$i;
		}

		$Arr_PhaseInd[$i][0]=0;
		$Arr_PhaseInd[$i][1]=get_text('0_Phase');
	}

// fasi fattibili team
	$StartPhase=-1;
	$Select
		= "SELECT MAX(EvFinalFirstPhase) AS Phase FROM Events where EvTeamEvent=1 and EvTournament=$TourId";
	$RsPh=safe_r_sql($Select);

	// Se la fase iniziale esiste in griglia allora uso quella altrimenti cerco la massima disponibile
	if (safe_num_rows($RsPh)!=1)
	{
		$Select
			= "SELECT MAX(GrPhase) AS Phase FROM Grids ";
		$RsPh=safe_r_sql($Select);

		if (safe_num_rows($RsPh)==1)
		{
			$Row=safe_fetch($RsPh);
			$StartPhase=$Row->Phase;
		}
	}
	else
		$StartPhase=TeamStartPhase;

	for ($i=0,$pp=$StartPhase;$pp>=1;$pp/=2,++$i)
	{
		$Arr_PhaseTeam[$i][0]=$pp;
		$Arr_PhaseTeam[$i][1]=get_text( $pp . '_Phase');
	}

	$Arr_PhaseTeam[$i][0]=0;
	$Arr_PhaseTeam[$i][1]=get_text('0_Phase');

// Numero di sessioni
	$sessions=GetSessions('Q');
	$NumSession=count($sessions);


	$Select
		= "SELECT * FROM TVParams "
		. "WHERE TVPId=$DBId "
		. "AND TVPTournament=$TourId";
	$Rs=safe_r_sql($Select);

	// the default object...
	$MyRow=new StdClass();
	$MyRow->TVPTimeStop=3; // seconds of stop before starting scrolling: default to 3 seconds
	$MyRow->TVPTimeScroll=10; // tenths of seconds for scrolling one row: default to 1 second
	$MyRow->TVPNumRows=0; // rows of result to show: default to 0 (all)
	$MyRow->TVPViewNationName=1; // show nation complete names: default to yes (1)
	$MyRow->TVPNameComplete=1; // show athletes complete names: default to yes (1)
	$MyRow->TVPViewTeamComponents=1; // show team components: default to yes (1)
	$MyRow->TVPViewPartials=1; // show Distance scores: default to yes (1)
	$MyRow->TVPViewDetails=1; // show 10s and X9s: default to yes (1)
	$MyRow->TVPViewIdCard=''; // show Id Cards in the match rounds: default to (1)
	$MyRow->TVPPage=''; // type of page for Tournament contents
	$MyRow->TVP_TR_BGColor='#FFFFFF'; // row background color
	$MyRow->TVP_TRNext_BGColor='#FFFFCC'; // alternate row background color
	$MyRow->TVP_TR_Color='#000000'; // row text color
	$MyRow->TVP_TRNext_Color='#000000'; // alternate row text color
	$MyRow->TVP_Content_BGColor='#FEFEFE'; // table background color
	$MyRow->TVP_Page_BGColor='#FFFFFF'; // page background color
	$MyRow->TVP_TH_BGColor='#CCCCCC'; // column header background color
	$MyRow->TVP_TH_Color='#000000'; // column header text color
	$MyRow->TVP_THTitle_BGColor='#585858'; // Table header background color
	$MyRow->TVP_THTitle_Color='#F4F4F4'; // table header text color
	$MyRow->TVP_Carattere='20'; // font dimention
	$MyRow->TVPDefault=true; // Default

	if($r=safe_fetch($Rs)) {
		// select the correct piece of chain to edit

		if ($r->TVPEventInd!='')
			$Arr_EventIndRule = explode('|',$r->TVPEventInd);

		if ($r->TVPEventTeam!='')
			$Arr_EventTeamRule = explode('|',$r->TVPEventTeam);

		if ($r->TVPPhasesInd!='')
			$Arr_PhaseIndRule = explode('|',$r->TVPPhasesInd);

		if ($r->TVPPhasesTeam!='')
			$Arr_PhaseTeamRule = explode('|',$r->TVPPhasesTeam);

		$MyRow=$r;
	}

	if($RULE->TV_Carattere and (!$r or !$MyRow->TVP_Carattere)) {
		$MyRow->TVP_TR_BGColor=$RULE->TV_TR_BGColor; // row background color
		$MyRow->TVP_TRNext_BGColor=$RULE->TV_TRNext_BGColor; // alternate row background color
		$MyRow->TVP_TR_Color=$RULE->TV_TR_Color; // row text color
		$MyRow->TVP_TRNext_Color=$RULE->TV_TRNext_Color; // alternate row text color
		$MyRow->TVP_Content_BGColor=$RULE->TV_Content_BGColor; // table background color
		$MyRow->TVP_Page_BGColor=$RULE->TV_Page_BGColor; // page background color
		$MyRow->TVP_TH_BGColor=$RULE->TV_TH_BGColor; // column header background color
		$MyRow->TVP_TH_Color=$RULE->TV_TH_Color; // column header text color
		$MyRow->TVP_THTitle_BGColor=$RULE->TV_THTitle_BGColor; // Table header background color
		$MyRow->TVP_THTitle_Color=$RULE->TV_THTitle_Color; // table header text color
		$MyRow->TVP_Carattere=$RULE->TV_Carattere; // font dimention
	}

?>
<br/>

<table class="Tabella">
<tr><th class="Title" colspan="3"><?php print get_text('TVTournamentContents','Tournament');?></th></tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVPageSelect','Tournament');?></th>
<td>
<select name="d_TVPage" id="d_TVPage" onchange="GetRuleSel(this.value, <?php echo $DBId; ?>);">
<option value=""><?php echo get_text('TVSelectPage','Tournament'); ?></option>
<?php
foreach($Arr_Pages as $key => $descr) {
	echo "<option value=\"$key\"".($key==$MyRow->TVPPage?' selected':'').">$descr</option>";
}
?>
</select>
</td>
<td><?php print get_text('TVSelectPageDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVCharSize','Tournament');?></th>
<td><input type="text" name="d_TV_Carattere" size="3" maxlength="3" value="<?php print $MyRow->TVP_Carattere;?>">&nbsp;px</td>
<td><?php print get_text('TVCharSizeDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVTimeStop','Tournament');?></th>
<td><input type="text" name="d_TVTimeStop" id="d_TVTimeStop" size="2" maxlength="2" value="<?php print $MyRow->TVPTimeStop;?>"></td>
<td><?php print get_text('TVTimeStopDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVTimeScroll','Tournament');?></th>
<td><input type="text" name="d_TVTimeScroll" id="d_TVTimeScroll" size="2" maxlength="2" value="<?php print $MyRow->TVPTimeScroll;?>"></td>
<td><?php print get_text('TVTimeScrollDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVNumRows','Tournament');?></th>
<td><input type="text" name="d_TVNumRows" id="d_TVNumRows" size="2" maxlength="3" value="<?php print $MyRow->TVPNumRows;?>"></td>
<td><?php print get_text('TVNumRowsDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVViewNationName','Tournament');?></th>
<td><input type="checkbox" name="d_TVViewNationName" id="d_TVViewNationName"<?php print ($MyRow->TVPViewNationName==1 ? ' checked' : '');?>></td>
<td><?php print get_text('TVViewNationNameDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVNameComplete','Tournament');?></th>
<td><input type="checkbox" name="d_TVNameComplete" id="d_TVNameComplete"<?php print ($MyRow->TVPNameComplete==1 ? ' checked' : '');?>></td>
<td><?php print get_text('TVNameCompleteDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVViewPartials','Tournament');?></th>
<td><input type="checkbox" name="d_TVViewPartials" id="d_TVViewPartials"<?php print ($MyRow->TVPViewPartials==1 ? ' checked' : '');?>></td>
<td><?php print get_text('TVViewPartialsDescr','Tournament');?></td>
</tr>

<!-- tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVViewDetails','Tournament');?></th>
<td><input type="checkbox" name="d_TVViewDetails" id="d_TVViewDetails"<?php print ($MyRow->TVPViewDetails==1 ? ' checked' : '');?>></td>
<td><?php print get_text('TVViewDetailsDescr','Tournament');?></td>
</tr -->

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVViewIdCard','BackNumbers');?></th>
<td><input type="checkbox" name="d_TVViewIdCard" id="d_TVViewIdCard"<?php print ($MyRow->TVPViewIdCard==1 ? ' checked' : '');?>></td>
<td><?php print get_text('TVViewIdCardDescr','BackNumbers');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('ViewTeamComponents','Tournament');?></th>
<td><input type="checkbox" name="d_TVViewTeamComponents" id="d_TVViewTeamComponents"<?php print ($MyRow->TVPViewTeamComponents==1 ? ' checked' : '');?>></td>
<td><?php print get_text('ViewTeamComponentsDescr','Tournament');?></td>
</tr>

<?php

if($NumSession>1) {
	echo '<tr>';
	echo '<th class="TitleLeft" width="15%">'. get_text('Session').'</th>';
	echo '<td colspan="2">';
	echo '<select name="d_TVSession" id="d_TVSession">';
	echo '<option value="0">' . get_text('AllSessions','Tournament') . '</option>';
	//for ($i=1;$i<=$NumSession;++$i)
		//print '<option value="' . $i. '"' . ($MyRow->TVPSession==$i ? ' selected' : '') . '>' . $i . '</option>' . "\n";
	foreach ($sessions as $s)
	{
		print '<option value="' . $s->SesOrder. '"' . ($MyRow->TVPSession==$s->SesOrder ? ' selected' : '') . '>' . $s->Descr . '</option>' . "\n";
	}
	echo '</select>';
	echo '</td>';
	echo '</tr>';
}

?>
</table>

<div id="EventPhaseSel"></div>

<table class="Tabella">
<tr><th class="TitleCenter" colspan="10"><?php print get_text('TVColorsFonts','Tournament');
?>&nbsp;-&nbsp;<input type="checkbox" name="d_TV_ColDefault" <?php
if($MyRow->TVPDefault) echo " checked"; ?>/>&nbsp;<?php
echo get_text('TVDefault','Tournament') ?>&nbsp;-&nbsp;<input type="checkbox" name=d_TV_SetColDefault />&nbsp;<?php
echo get_text('TVSetAsDefault','Tournament') ?></th></tr>

<tr>
<th><?php print get_text('TVContentBGColor','Tournament');?></th>
<th><?php print get_text('TVPageBGColor','Tournament');?></th>
<th><?php print get_text('TVTRBGColor','Tournament');?></th>
<th><?php print get_text('TVTRColor','Tournament');?></th>
<th><?php print get_text('TVTRNextBGColor','Tournament');?></th>
<th><?php print get_text('TVTRNextColor','Tournament');?></th>

<th><?php print get_text('TVTHBGColor','Tournament');?></th>
<th><?php print get_text('TVTHColor','Tournament');?></th>
<th><?php print get_text('TVTHTitleBGColor','Tournament');?></th>
<th><?php print get_text('TVTHTitleColor','Tournament');?></th>
</tr>

<tr>
<td class="Center">
<input type="text" name="d_TV_Content_BGColor" id="d_TV_Content_BGColor" size="6" maxlength="7" value="<?php print $MyRow->TVP_Content_BGColor?>">
&nbsp;<input type="text" id="Ex_Content_BGColor" size="1" style="background-color:<?php print $MyRow->TVP_Content_BGColor?>;" readonly>
<br>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302('d_TV_Content_BGColor','Ex_Content_BGColor');">
</td>

<td class="Center">
<input type="text" name="d_TV_Page_BGColor" id="d_TV_Page_BGColor" size="6" maxlength="7" value="<?php print $MyRow->TVP_Page_BGColor?>">
&nbsp;<input type="text" id="Ex_Page_BGColor" size="1" style="background-color:<?php print $MyRow->TVP_Page_BGColor?>;" readonly>
<br>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302('d_TV_Page_BGColor','Ex_Page_BGColor');">
</td>

<td class="Center">
<input type="text" name="d_TV_TR_BGColor" id="d_TV_TR_BGColor" size="6" maxlength="7" value="<?php print $MyRow->TVP_TR_BGColor?>">
&nbsp;<input type="text" id="Ex_TR_BGColor" size="1" style="background-color:<?php print $MyRow->TVP_TR_BGColor?>;" readonly>
<br>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302('d_TV_TR_BGColor','Ex_TR_BGColor');">
</td>

<td class="Center">
<input type="text" name="d_TV_TR_Color" id="d_TV_TR_Color" size="6" maxlength="7" value="<?php print $MyRow->TVP_TR_Color?>">
&nbsp;<input type="text" id="Ex_TR_Color" size="1" style="background-color:<?php print $MyRow->TVP_TR_Color?>;" readonly>
<br>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302('d_TV_TR_Color','Ex_TR_Color');">
</td>

<td class="Center">
<input type="text" name="d_TV_TRNext_BGColor" id="d_TV_TRNext_BGColor" size="6" maxlength="7" value="<?php print $MyRow->TVP_TRNext_BGColor?>">
&nbsp;<input type="text" id="Ex_TRNext_BGColor" size="1" style="background-color:<?php print $MyRow->TVP_TRNext_BGColor?>;" readonly>
<br>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302('d_TV_TRNext_BGColor','Ex_TRNext_BGColor');">
</td>

<td class="Center">
<input type="text" name="d_TV_TRNext_Color" id="d_TV_TRNext_Color" size="6" maxlength="7" value="<?php print $MyRow->TVP_TRNext_Color?>">
&nbsp;<input type="text" id="Ex_TRNext_Color" size="1" style="background-color:<?php print $MyRow->TVP_TRNext_Color?>;" readonly>
<br>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302('d_TV_TRNext_Color','Ex_TRNext_Color');">
</td>

<td class="Center">
<input type="text" name="d_TV_TH_BGColor" id="d_TV_TH_BGColor" size="6" maxlength="7" value="<?php print $MyRow->TVP_TH_BGColor?>">
&nbsp;<input type="text" id="Ex_TH_BGColor" size="1" style="background-color:<?php print $MyRow->TVP_TH_BGColor?>;" readonly>
<br>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302('d_TV_TH_BGColor','Ex_TH_BGColor');">
</td>

<td class="Center">
<input type="text" name="d_TV_TH_Color" id="d_TV_TH_Color" size="6" maxlength="7" value="<?php print $MyRow->TVP_TH_Color?>">
&nbsp;<input type="text" id="Ex_TH_Color" size="1" style="background-color:<?php print $MyRow->TVP_TH_Color?>;" readonly>
<br>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302('d_TV_TH_Color','Ex_TH_Color');">
</td>

<td class="Center">
<input type="text" name="d_TV_THTitle_BGColor" id="d_TV_THTitle_BGColor" size="6" maxlength="7" value="<?php print $MyRow->TVP_THTitle_BGColor?>">
&nbsp;<input type="text" id="Ex_THTitle_BGColor" size="1" style="background-color:<?php print $MyRow->TVP_THTitle_BGColor?>;" readonly>
<br>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302('d_TV_THTitle_BGColor','Ex_THTitle_BGColor');">
</td>

<td class="Center">
<input type="text" name="d_TV_THTitle_Color" id="d_TV_THTitle_Color" size="6" maxlength="7" value="<?php print $MyRow->TVP_THTitle_Color?>">
&nbsp;<input type="text" id="Ex_THTitle_Color" size="1" style="background-color:<?php print $MyRow->TVP_THTitle_Color?>;" readonly>
<br>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302('d_TV_THTitle_Color','Ex_THTitle_Color');">
</td>
</tr>
