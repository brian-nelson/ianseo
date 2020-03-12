<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
// if (!CheckTourSession() or !isset($_REQUEST['Id'])) printCrackerror('popup');

$JS_SCRIPT[]='<link href="'.$CFG->ROOT_DIR.'Common/Styles/Biographies.css" media="screen" rel="stylesheet" type="text/css">';
$JS_SCRIPT[]='<script src="./Spot.js"></script>';

// $ONLOAD=' onblur="window.close();"';

include('Common/Templates/head-min.php');

$rawData = file_get_contents($CFG->WaWrapper."?v=3&content=BIOTEAM&ID=".$_REQUEST["Id"]."&CAT={$_REQUEST['Cat']}");
if(($BioData=json_decode($rawData))!=null) {
	$BioData=$BioData->items[0];
	echo '<div id="BioContent">';
	echo '<table>
		<tr class="BioName">
			<td colspan="5">'. $BioData->FName.' ('.$BioData->NOC.') - ' . $BioData->GName.' '.'</td>
		</tr>
		<tr class="NoWrap odd">
			<td><b>Joined:</b> '.$BioData->Joined.'</td>
			<td><b>World Rank:</b> '.$BioData->Rnk.' '.$BioData->Cat.'</td>
			<td><div class="Variation">';
	$RankDiff=$BioData->Rnk-$BioData->RnkOld;
	if($RankDiff<0) {
		echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/Up.png">';
	} elseif($RankDiff>1) {
		echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/Down.png">';
	} else {
		echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/Minus.png">';
	}
	if($RankDiff) echo '<div>'.abs($RankDiff).'</div>';
	echo '</div></td>
		</tr>
		<tr>
			<td><b>Match won:</b> '.$BioData->MatchWin.'/'.$BioData->MatchTot.' ('.$BioData->MatchWinPercentage.'%)</td>
			<td><b>Ties won:</b> '.$BioData->TBWin.'/'.$BioData->TBTot.' ('.$BioData->TBWinPercentage.'%)</td>
		</tr>';

	// fetches the previous matches between the 2 opponents
	$rawData = file_get_contents($CFG->WaWrapper."?v=3&RBP=All&content=ATHMAT&CatCode=".$_REQUEST["Cat"]."&Noc=".$_REQUEST["Id"]."&Noc2=".$_REQUEST["Id2"]);
	if(($Matches=json_decode($rawData))!=null) {
		$TotalMatches=$Matches->pageInfo->totalResults;
		$Wins=0;
		$TB=0;
		$WinsTB=0;
		foreach($Matches->items as $Match) {
			if($Match->Competitor1->Athlete->NOC==$_REQUEST["Id"] and $Match->Competitor1->WinLose) $Wins++;
			if($Match->Competitor2->Athlete->NOC==$_REQUEST["Id"] and $Match->Competitor2->WinLose) $Wins++;
			if($Match->Competitor1->TB!=='') {
				$TB++;
				if($Match->Competitor1->Athlete->NOC==$_REQUEST["Id"] and $Match->Competitor1->WinLose) $WinsTB++;
				if($Match->Competitor2->Athlete->NOC==$_REQUEST["Id"] and $Match->Competitor2->WinLose) $WinsTB++;
			}
		}

		$WinPercent=0;
		$TiePercent=0;
		if($TotalMatches) $WinPercent=number_format(100*$Wins/$TotalMatches,0);
		if($TB) $TiePercent=number_format(100*$WinsTB/$TB,0);

		echo '<tr>
			<td><b>Match vs opponent:</b> '.$Wins.'/'.$TotalMatches.' ('.$WinPercent.'%)</td>
			<td><b>Ties vs opponent won:</b> '.$WinsTB.'/'.$TB.' ('.$TiePercent.'%)</td>
		</tr>';
	} else {
		debug_svela($CFG->WaWrapper."?v=3&RBP=All&content=ATHMAT&CatCode=".$_REQUEST["Cat"]."&Noc=".$_REQUEST["Id"]."&Noc2=".$_REQUEST["Id2"]);

	}

	echo '
		</table>';

	// Medals
	echo '<table id="BioMedals">
		';
		echo '<tr><td colspan="4">
				';
// 		foreach($BioData->CompetitionLevels as $Id => $Desc) {
// 			if($Id==5) echo '<br/>';
// 			echo '<input type="checkbox" onclick="UpdateRows(this)" value="Lev'.$Id.'" checked="checked">'.$Desc;
// 		}
		foreach($BioData->caps as $Id => $Item) {
// 			if($Item[1]==5) echo '<br/>';
			echo '<span style="white-space:nowrap"><input type="checkbox" onclick="UpdateRows(this)" value="Lev'.$Id.'" checked="checked">'.$Item[0].' x '.$Item[1].'</span> ';
		}
		echo '</td></tr>';
		echo '<tr><th colspan="4" class="Title">Medals</th></tr>';
	$cnt=0;
	$Level=0;
	foreach($BioData->Medals as $medal) {
		if($Level!=$medal->ComLevel) {
			$Level=$medal->ComLevel;
			echo '<tr class=" Lev'.$Level.'"><th colspan="4" class="Title">'.$BioData->CompetitionLevels->{$Level}.'</th></tr>';
		}
			$Class="IsTeam";
		if($cnt++ % 2 ==0) $Class.=" odd";
		$Class.=" Lev".$Level;
		echo '<tr class="'.$Class.'">
			<td class="Rank">'.$medal->Rnk.'</td>
			<td class="NoWrap">'.$medal->Cat.' Team</td>
			<td>'.$medal->ComName.' ('.$medal->ComNOC.')</td>
			<td class="NoWrap">'.date('j M Y', strtotime($medal->Date)).'</td>
			</tr>';
	}
	echo '</table></div>';

}

include('Common/Templates/tail-min.php');