<?php
if (!defined('IN_PHP')) CD_redirect();

require_once('Common/Lib/Fun_Phases.inc.php');

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
			$StartPhase=valueFirstPhase($Row->Phase);
		}
	}
	else {
	    $StartPhase=TeamStartPhase;
	}

	for ($i=0,$pp=$StartPhase; $pp>=1; $pp/=2,++$i) {
	    $p2=namePhase($StartPhase, $pp);
	    $pfull=valueFirstPhase($pp);
		$Arr_PhaseTeam[$i][0]=valueFirstPhase($pp);
		$Arr_PhaseTeam[$i][1]=get_text( $p2 . '_Phase');
		if($p2!=$pfull) $Arr_PhaseTeam[$i][1].='/'.get_text( $pfull . '_Phase');
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
<td><input type="text" name="d_TVTimeStop" id="d_TVTimeStop" size="2" maxlength="5" value="<?php print $MyRow->TVPTimeStop;?>"></td>
<td><?php print get_text('TVTimeStopDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVTimeScroll','Tournament');?></th>
<td><input type="text" name="d_TVTimeScroll" id="d_TVTimeScroll" size="2" maxlength="4" value="<?php print $MyRow->TVPTimeScroll;?>"></td>
<td><?php print get_text('TVTimeScrollDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVNumRows','Tournament');?></th>
<td><input type="text" name="d_TVNumRows" id="d_TVNumRows" size="2" maxlength="4" value="<?php print $MyRow->TVPNumRows;?>"></td>
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
		print '<option value="' . $s->SesOrder. '"' . (isset($MyRow->TVPSession) && $MyRow->TVPSession==$s->SesOrder ? ' selected' : '') . '>' . $s->Descr . '</option>' . "\n";
	}
	echo '</select>';
	echo '</td>';
	echo '</tr>';
}

echo '</table>';

echo '<div id="EventPhaseSel"></div>';

?>




<br/>
<table class="Tabella">
<tr><th class="Title" colspan="10"><?php print get_text('TVColorsFonts','Tournament');
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

</table>
<br/>
<style>
.Css3 textarea {width:100%; box-sizing:border-box; height:3em;}
.Css3 input[type=text] {width:100%; box-sizing:border-box; }
</style>
<table class="Tabella Css3">
<tr><th colspan="3" class="Title"><?php print get_text('TVCss3Management','Tournament');?></th></tr>
<tr><th colspan="3"><?php echo get_text('TVCss3Defaults','Tournament'); ?></th></tr>
<?php
// defaults for fonts, colors, size
$RMain=array();
if(!empty($RULE->TVRSettings)) {
	$RMain=unserialize($RULE->TVRSettings);
}

$MainDefaults=getDefaults($RMain);

echo '<tr><td colspan="3">
	<table width="100%">';
echo '<tr>
		<th>'.get_text('TVCss3FontName','Tournament').       ' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[name]\').value=\''.$MainDefaults['name'].'\'"></th>
		<th>'.get_text('TVCss3FontSize','Tournament').       ' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[size]\').value=\''.$MainDefaults['size'].'\'"></th>
		<th>'.get_text('TVCss3FontColor1-even','Tournament').' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[col1e]\').value=\''.$MainDefaults['col1e'].'\'"></th>
		<th>'.get_text('TVCss3FontRev1-even','Tournament').  ' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[rev1e]\').value=\''.$MainDefaults['rev1e'].'\'"></th>
		<th>'.get_text('TVCss3BgColor1-even','Tournament').  ' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[bck1e]\').value=\''.$MainDefaults['bck1e'].'\'"></th>
		<th>'.get_text('TVCss3FontColor1-odd','Tournament'). ' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[col1o]\').value=\''.$MainDefaults['col1o'].'\'"></th>
		<th>'.get_text('TVCss3FontRev1-odd','Tournament').   ' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[rev1o]\').value=\''.$MainDefaults['rev1o'].'\'"></th>
		<th>'.get_text('TVCss3BgColor1-odd','Tournament').   ' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[bck1o]\').value=\''.$MainDefaults['bck1o'].'\'"></th>
		<th>'.get_text('TVCss3FontColor2-even','Tournament').' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[col2e]\').value=\''.$MainDefaults['col2e'].'\'"></th>
		<th>'.get_text('TVCss3FontRev2-even','Tournament').  ' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[rev2e]\').value=\''.$MainDefaults['rev2e'].'\'"></th>
		<th>'.get_text('TVCss3BgColor2-even','Tournament').  ' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[bck2e]\').value=\''.$MainDefaults['bck2e'].'\'"></th>
		<th>'.get_text('TVCss3FontColor2-odd','Tournament'). ' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[col2o]\').value=\''.$MainDefaults['col2o'].'\'"></th>
		<th>'.get_text('TVCss3FontRev2-odd','Tournament').   ' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[rev2o]\').value=\''.$MainDefaults['rev2o'].'\'"></th>
		<th>'.get_text('TVCss3BgColor2-odd','Tournament').   ' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[bck2o]\').value=\''.$MainDefaults['bck2o'].'\'"></th>
		</tr>';
echo '<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td style="color:'.$RMain['col1e'].';background:'.$RMain['bck1e'].'" colspan="3">'.$RMain['col1e'].'<br/><span style="color:'.$RMain['rev1e'].'">'.$RMain['rev1e'].'</span><br/>'.$RMain['bck1e'].'</td>
		<td style="color:'.$RMain['col1o'].';background:'.$RMain['bck1o'].'" colspan="3">'.$RMain['col1o'].'<br/><span style="color:'.$RMain['rev1o'].'">'.$RMain['rev1o'].'</span><br/>'.$RMain['bck1o'].'</td>
		<td style="color:'.$RMain['col2e'].';background:'.$RMain['bck2e'].'" colspan="3">'.$RMain['col2e'].'<br/><span style="color:'.$RMain['rev2e'].'">'.$RMain['rev2e'].'</span><br/>'.$RMain['bck2e'].'</td>
		<td style="color:'.$RMain['col2o'].';background:'.$RMain['bck2o'].'" colspan="3">'.$RMain['col2o'].'<br/><span style="color:'.$RMain['rev2o'].'">'.$RMain['rev2o'].'</span><br/>'.$RMain['bck2o'].'</td>
		</tr>';
echo '<tr>
		<td><input type="text" id="R-Main[name]"  name="R-Main[name]" value="'.$RMain['name'].'"></td>
		<td><input type="text" id="R-Main[size]"  name="R-Main[size]" value="'.$RMain['size'].'"></td>
		<td><input type="text" id="R-Main[col1e]" name="R-Main[col1e]" value="'.$RMain['col1e'].'"></td>
		<td><input type="text" id="R-Main[rev1e]" name="R-Main[rev1e]" value="'.$RMain['rev1e'].'"></td>
		<td><input type="text" id="R-Main[bck1e]" name="R-Main[bck1e]" value="'.$RMain['bck1e'].'"></td>
		<td><input type="text" id="R-Main[col1o]" name="R-Main[col1o]" value="'.$RMain['col1o'].'"></td>
		<td><input type="text" id="R-Main[rev1o]" name="R-Main[rev1o]" value="'.$RMain['rev1o'].'"></td>
		<td><input type="text" id="R-Main[bck1o]" name="R-Main[bck1o]" value="'.$RMain['bck1o'].'"></td>
		<td><input type="text" id="R-Main[col2e]" name="R-Main[col2e]" value="'.$RMain['col2e'].'"></td>
		<td><input type="text" id="R-Main[rev2e]" name="R-Main[rev2e]" value="'.$RMain['rev2e'].'"></td>
		<td><input type="text" id="R-Main[bck2e]" name="R-Main[bck2e]" value="'.$RMain['bck2e'].'"></td>
		<td><input type="text" id="R-Main[col2o]" name="R-Main[col2o]" value="'.$RMain['col2o'].'"></td>
		<td><input type="text" id="R-Main[rev2o]" name="R-Main[rev2o]" value="'.$RMain['rev2o'].'"></td>
		<td><input type="text" id="R-Main[bck2o]" name="R-Main[bck2o]" value="'.$RMain['bck2o'].'"></td>
		</tr>';
echo '</table>
	</td></tr>';

// defaults for structures
echo '<tr>
	<th nowrap="nowrap" class="Right">'.get_text('TVCss3MainContent','Tournament').' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[content]\').value=\''.$MainDefaults['content'].'\'"></th>
	<td></td>
	<td width="100%"><input type="text"  id="R-Main[content]" name="R-Main[content]" value="'.$RMain['content'].'"></td>
	</tr>';

echo '<tr>
	<th nowrap="nowrap" class="Right">'.get_text('TVCss3Title','Tournament').' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[title]\').value=\''.$MainDefaults['title'].'\'"></th>
	<td style="'.preg_replace('/\bwidth:[^;]+/sim', '', $RMain['title']).'">'.get_text('TVCss3Title','Tournament').'</td>
	<td width="100%"><input type="text" id="R-Main[title]" name="R-Main[title]" value="'.$RMain['title'].'"></td>
	</tr>';

echo '<tr>
	<th nowrap="nowrap" class="Right">'.get_text('TVCss3Headers','Tournament').' <input type="button" value="reset" onclick="document.getElementById(\'R-Main[Headers]\').value=\''.$MainDefaults['Headers'].'\'"></th>
	<td style="'.preg_replace('/\bwidth:[^;]+/sim', '', $RMain['Headers']).'">'.get_text('TVCss3Headers','Tournament').'</td>
	<td width="100%"><input type="text" id="R-Main[Headers]" name="R-Main[Headers]" value="'.$RMain['Headers'].'"></td>
	</tr>';


function getDefaults(&$Rmain) {
	$ret=array(
		'name' => '',
		'size' => '1em',
		'col1e' => 'black',
		'col1o' => 'black',
		'col2e' => 'black',
		'col2o' => 'black',
		'rev1e' => 'black',
		'rev1o' => 'black',
		'rev2e' => 'black',
		'rev2o' => 'black',
		'bck1e' => 'linear-gradient(to right, #FFFFFF, #DDDDDD)',
		'bck1o' => 'linear-gradient(to right, #EDDE5D, #F09819)',
		'bck2e' => 'linear-gradient(#FFFFFF, #DDDDDD)',
		'bck2o' => 'linear-gradient(#EDDE5D, #F09819)',
		'title' => 'background: linear-gradient(#1E5799, #7DB9E8);font-size:2.4vw; text-align:center; width:100%; padding:0.5em; box-sizing:border-box;color: white;',
		'Headers' => 'background: linear-gradient(#1E5799, #7DB9E8);font-size:1.25vw;box-sizing:border-box;color: white;white-space:nowrap;',
		'content' => 'height:100%; left:2vw; width:96vw;font-size:1vw;',
	);
	foreach($ret as $k=>$v) {
		if(!isset($Rmain[$k])) $Rmain[$k]=$v;
	}
	return $ret;
}