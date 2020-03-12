<?php

require_once(dirname(__FILE__) . '/config.php');

CheckTourSession(true);

$JS_SCRIPT[] ='<script src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>';
$JS_SCRIPT[]='<script src="./configure.js"></script>';

include('Common/Templates/head.php');


// foreach category sets the winnings of previous year
$Winners=array();
$Ranking=array();
$Teams=array();

$q=safe_r_sql("select EvCode, EvNumQualified
	from Events 
	where EvTeamEvent=1 and EvTournament={$_SESSION['TourId']} 
	order by EvProgr");
while($r=safe_fetch($q)) {
	foreach(range(1,$r->EvNumQualified) as $k) {
		$Winners[$r->EvCode][$k]='';
		$Bonus[$r->EvCode][$k]=0;
		$Teams[$r->EvCode]=$r->EvNumQualified;
	}
}

$SavedBonus=getModuleParameter('FFTA', 'D1Bonus', $Winners);
$SavedWinners=getModuleParameter('FFTA', 'D1Winners', $Winners);
setModuleParameter('FFTA', 'D1Bonus', $SavedBonus);
setModuleParameter('FFTA', 'D1Winners', $SavedWinners);

echo '<table class="Tabella" style="margin:auto;width:auto;margin-bottom:1em">';

// default match duration
echo '<tr><th class="Title">'.get_text('ConnectedCodes','Tournament').'</th><td><input type="text" pos="" cat="" item="CONNECTED" onblur="confUpdate(this)" size="30" value="'.implode(', ', getModuleParameter('FFTA', 'ConnectedCompetitions', array($_SESSION['TourCode']))).'"></td></tr>';
echo '<tr><th class="Title">'.get_text('StdIndMatchLength','Tournament').'</th><td><input type="text" pos="" cat="" item="DEFIND" onblur="confUpdate(this)" size="10" value="'.getModuleParameter('FFTA', 'DefaultMatchIndividual', 40).'"></td></tr>';
echo '<tr><th class="Title">'.get_text('StdTeamMatchLength','Tournament').'</th><td><input type="text" pos="" cat="" item="DEFTEAM" onblur="confUpdate(this)" size="10" value="'.getModuleParameter('FFTA', 'DefaultMatchTeam', 30).'"></td></tr>';
echo '</table>';

echo '<table class="Tabella" style="margin:auto;width:auto">';

// default match duration
echo '<tr></tr>';

$Heading2='<tr>';
echo '<tr>';
echo '<th class="Title" rowspan="2"></th>';
foreach($Teams as $Cat => $Rank) {
	echo '<th class="Title" colspan="2">' . $Cat . '</th>';
	$Heading2.='<th class="Title">'.get_text('RankYear', 'Tournament', substr($_SESSION['TourRealWhenFrom'], 0, 4)-1).'</th><th class="Title">'.get_text('Bonus', 'Tournament').'</th>';
}
echo '</tr>';
echo $Heading2.'</tr>';

foreach(range(1, max($Teams)) as $pos) {
	echo '<tr><th class="Title">'.$pos.'</th>';
	foreach($Teams as $Cat => $MaxRows) {
		if($pos<=$MaxRows) {
			echo '<td><input type="text" pos="'.$pos.'" cat="'.$Cat.'" item="CLUB" onblur="confUpdate(this)" value="'.(isset($SavedWinners[$Cat][$pos]) ? $SavedWinners[$Cat][$pos] : '').'" size="10"></td>';
			echo '<td><input type="text" pos="'.$pos.'" cat="'.$Cat.'" item="BONUS" onblur="confUpdate(this)" value="'.(isset($SavedBonus[$Cat][$pos]) ? intval($SavedBonus[$Cat][$pos]) : 0).'" size="3"></td>';
		} else {
			echo '<td></td>';
			echo '<td></td>';
		}
	}
	echo '</tr>';
}
echo '</table>';

include('Common/Templates/tail.php');