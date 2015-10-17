<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');

	$PAGE_TITLE=get_text('PrintList', 'Tournament');

	$JS_SCRIPT=array('
	<script>
	function changevisibility(box, who, optdiv, optdiv2) {
		if(box.checked) {
			if(who!=undefined) document.getElementById(who).style.display=\'block\';
			if(optdiv!=undefined) document.getElementById(optdiv).style.display=\'none\';
			if(optdiv2!=undefined) document.getElementById(optdiv2).style.display=\'none\';
		} else {
			if(who!=undefined) document.getElementById(who).style.display=\'none\';
			if(optdiv!=undefined) document.getElementById(optdiv).style.display=\'block\';
			if(optdiv2!=undefined) document.getElementById(optdiv2).style.display=\'block\';
		}
	}
	</script>');

	include('Common/Templates/head.php');

	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="3">' . get_text('PrintList','Tournament')  . '</th></tr>';
	echo '<tr><th class="SubTitle" width="30%">' . get_text('ResultIndClass','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="40%">' . get_text('ResultClass','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="30%">' . get_text('ResultSqClass','Tournament')  . '</th></tr>';
//Classifica
	echo '<tr>';
//Individuale
	echo '<td class="Center"><br><a href="PrnIndividual.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf_small.gif" alt="' . get_text('ResultIndClass','Tournament') . '" border="0"><br>';
	echo get_text('ResultIndClass','Tournament');
	echo '</a></td>';
//Completa
	echo '<td class="Center"><br><a href="PrnComplete.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf.gif" alt="' . get_text('ResultClass','Tournament') . '" border="0"><br>';
	echo get_text('ResultClass','Tournament');
	echo '</a></td>';
//Squadre
	echo '<td class="Center"><br><a href="PrnTeam.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf_small.gif" alt="' . get_text('ResultSqClass','Tournament') . '" border="0"><br>';
	echo get_text('ResultSqClass','Tournament');
	echo '</a></td>';
	echo '</tr>';
//Medaglie
	echo '<tr>';
//Individuale
	echo '<td class="Center"><br><a href="PrnMedalInd.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf_small.gif" alt="' . get_text('MedalIndClass','Tournament') . '" border="0"><br>';
	echo get_text('MedalIndClass','Tournament');
	echo '</a></td>';
//Completa
	echo '<td class="Center"><br><a href="PrnMedalComp.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf_small.gif" alt="' . get_text('MedalClass','Tournament') . '" border="0"><br>';
	echo get_text('MedalClass','Tournament');
	echo '</a></td>';
//Squadre
	echo '<td class="Center"><br><a href="PrnMedalTeam.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf_small.gif" alt="' . get_text('MedalSqClass','Tournament') . '" border="0"><br>';
	echo get_text('MedalSqClass','Tournament');
	echo '</a></td>';
	echo '</tr>';

	echo '<tr class="Divider"><td  colspan="3"></td></tr>';
	echo '<tr>';
//Filtri per l'Individuale
	echo '<td class="Center" colspan="3"><div align="center"><br><form id="PrnParameters" action="" method="get" target="PrintOut">';
	echo '<table class="Tabella" style="width:80%">';
	echo '<tr>';
	//Divisioni
	echo '<td class="Center" width="33%">';
	$MySql = "SELECT DivId, DivDescription FROM Divisions WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . "  AND DivAthlete=1 ORDER BY DivViewOrder";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		echo get_text('Division') . '<br><select id="Divisions" name="Divisions[]" multiple="multiple">';
//		echo '<option value="All" >' . get_text('AllsM','Tournament')  . '</option>';
		while($MyRow=safe_fetch($Rs))
			echo '<option value="' . $MyRow->DivId . '">' . $MyRow->DivId . ' - ' . get_text($MyRow->DivDescription,'','',true)  . '</option>';
		echo '</select>';
		echo '<br><br><a class="Link" href="javascript:SelectAllOpt(\'Divisions\');">' . get_text('SelectAll') . '</a>';
		safe_free_result($Rs);
	}
	//Classi
	echo '</td><td class="Center" width="34%">';
	$MySql = "SELECT ClId, ClDescription FROM Classes WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClAthlete=1 ORDER BY ClViewOrder";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		echo get_text('Class') . '<br><select id="Classes" name="Classes[]" multiple="multiple">';
//		echo '<option value="All">' . get_text('AllsM','Tournament')  . '</option>';
		while($MyRow=safe_fetch($Rs))
			echo '<option value="' . $MyRow->ClId . '">' . $MyRow->ClId . ' - ' . get_text($MyRow->ClDescription,'','',true)  . '</option>';
		echo '</select>';
		echo '<br><br><a class="Link" href="javascript:SelectAllOpt(\'Classes\');">' . get_text('SelectAll') . '</a>';
		safe_free_result($Rs);
	}

	$TypeVisible='';
	$TypeHidden=' style="display:none"';
	if($_SESSION['TourType']==14) {
		$TypeVisible=' style="display:none"';
// 		$TypeHidden='';
	}
	echo '</td><td class="Center" width="33%">';
	echo '<div>' . get_text('NumResult','Tournament') . '<br><input name="MaxNum" type="text" size="10" maxlength="3"></div>';
	echo '<div>' . get_text('ScoreCutoff','Tournament') . '<br><input name="ScoreCutoff" type="text" size="10" maxlength="5"></div>';
	echo '<br>';
	echo '<div id="SnapShot"'.$TypeVisible.'><input name="Snapshot" type="checkbox" onclick="changevisibility(this, null, \'SubClassRankCommand\', \'DistanceRankings\')">' . get_text('Snapshot','Tournament') . '</div>';

	// Subclass ranking
	echo '<br><div id="SubClassRankCommand">';
	echo '<div><input name="SubClassRank" type="checkbox" onclick="changevisibility(this,\'SubClassRank\', \'SnapShot\', \'DistanceRankings\')"'.($_SESSION['TourType']==14 ? ' checked="checked"' : '').'>' . get_text('SubClassRank','Tournament') . '</div>';
	if($_SESSION['TourType']==14) echo '<div><input name="ShowAwards" type="checkbox">' . get_text('PrintFlightsAwards','Tournament') . '</div>';
	echo '<div id="SubClassRank"'.$TypeHidden.'>';
	echo '<div><input name="OnlySubClass" type="checkbox">' . get_text('SubClassOnly','Tournament') . '</div>';
	echo '<div><input name="SubClassDivRank" type="checkbox">' . get_text('SubClassDivJoinRank','Tournament') . '</div>';
	echo '<div><input name="SubClassClassRank" type="checkbox">' . get_text('SubClassClassJoinRank','Tournament') . '</div>';
	echo '</div>';
	echo '</div>';

	// RankByDistance
	$t=safe_r_sql("select ToNumDist from Tournament where ToId='{$_SESSION['TourId']}'");
	$u=safe_fetch($t);
	echo '<br><div id="DistanceRankings"'.$TypeVisible.'>';
	if($u->ToNumDist>1) {
		echo '<div><input type="checkbox" name="distEnable" onclick="changevisibility(this,\'AfterDistance\', \'SnapShot\', \'SubClassRankCommand\')">' . get_text('RankByDistance','Tournament') . '</div>';
		echo '<div id="AfterDistance" style="display:none">';
		echo '<div>' . get_text('AfterDistance','Tournament') . ' <select name="runningDist">'
			. '<option value="">' . get_text('Last','Tournament') . '</option>'
			;
			for($n=1; $n<=$u->ToNumDist; $n++) {
				echo '<option value="'.$n.'">' . $n . '</option>';
			}
			echo '</select></div>';
		echo '<div>' . get_text('AtDistance','Tournament') . ' <select name="atDist">'
			. '<option value="">--</option>'
			;
			for($n=1; $n<=$u->ToNumDist; $n++) {
				echo '<option value="'.$n.'">' . $n . '</option>';
			}
			echo '</select></div>';
		echo '</div>';
	}
	echo "</div>";
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '</form></div><br></td>';
	echo '</tr>';
//Bottoni
	echo '<tr>';
	echo '<td class="Center"><br><input type="submit" name="Button" onClick="javascript:document.getElementById(\'PrnParameters\').action=\'PrnIndividual.php\';document.getElementById(\'PrnParameters\').submit();" value="' . get_text('ResultIndClass','Tournament') . '"><br>&nbsp;</td>';
	echo '<td class="Center"><br><input type="submit" name="Button" onClick="javascript:document.getElementById(\'PrnParameters\').action=\'PrnComplete.php\';document.getElementById(\'PrnParameters\').submit();" value="' . get_text('ResultClass','Tournament') . '"><br>&nbsp;</td>';
	echo '<td class="Center"><br><input type="submit" name="Button" onClick="javascript:document.getElementById(\'PrnParameters\').action=\'PrnTeam.php\';document.getElementById(\'PrnParameters\').submit();" value="' . get_text('ResultSqClass','Tournament') . '"><br>&nbsp;</td>';
	echo '</tr>';

	echo '</table>';

	include('Common/Templates/tail.php');
?>