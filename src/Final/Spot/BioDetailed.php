<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');
// if (!CheckTourSession() or !isset($_REQUEST['Id'])) printCrackerror('popup');

$JS_SCRIPT[]='<link href="'.$CFG->ROOT_DIR.'Common/Styles/Biographies.css" media="screen" rel="stylesheet" type="text/css">';
$JS_SCRIPT[]='<script src="./Spot.js"></script>';

include('Common/Templates/head-min.php');

// if(!empty($_REQUEST['CompType']))

$rawData = file_get_contents($CFG->WaWrapper."?v=3&content=BIODET&ID=".$_REQUEST["Id"]);
if(($BioData=json_decode($rawData))!=null) {

	$BioData=$BioData->items[0];
	echo '<div id="BioContent">';
	echo '<table>
		<tr class="BioName">
			<td colspan="5">'.($BioData->NameOrd ? $BioData->FName.' '.$BioData->GName : $BioData->GName.' '.$BioData->FName).' ('.$BioData->NOC.')</td>
		</tr>
		<tr class="NoWrap odd">
			<td><b>Age:</b> '.$BioData->Age.'</td>
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
	if(!empty($_REQUEST["Id2"])) {
		$rawData = file_get_contents($CFG->WaWrapper."?v=3&RBP=All&content=ATHMAT&Id=".$_REQUEST["Id"]."&Id2=".$_REQUEST["Id2"]);
		if(($Matches=json_decode($rawData))!=null) {
			$TotalMatches=$Matches->pageInfo->totalResults;
			$Wins=0;
			$TB=0;
			$WinsTB=0;
			foreach($Matches->items as $Match) {
				if($Match->Competitor1->Athlete->Id==$_REQUEST["Id"] and $Match->Competitor1->WinLose) $Wins++;
				if($Match->Competitor2->Athlete->Id==$_REQUEST["Id"] and $Match->Competitor2->WinLose) $Wins++;
				if($Match->Competitor1->TB!=='') {
					$TB++;
					if($Match->Competitor1->Athlete->Id==$_REQUEST["Id"] and $Match->Competitor1->WinLose) $WinsTB++;
					if($Match->Competitor2->Athlete->Id==$_REQUEST["Id"] and $Match->Competitor2->WinLose) $WinsTB++;
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
		}
	}

	echo '<tr class="NoWrap odd">
			<td><b>Personal best:</b> '.$BioData->QCareer.'</td>
			<td><b>Season\'s best:</b> '.$BioData->QSeason.'</td>
		</tr>';

	// get this competition's average
	$avg=0;
	$q=safe_r_SQL("select QuScore, QuHits, group_concat(trim(FinArrowstring) separator '') as FinArrowstring
		from Qualifications
		inner join Finals on FinAthlete=QuId
		inner join Entries on EnId=QuId
		where EnCode=".StrSafe_DB($_REQUEST['Id']));
	if($r=safe_fetch($q)) {
		$avg=round(($r->QuScore+ValutaArrowString($r->FinArrowstring))/($r->QuHits+strlen($r->FinArrowstring)), 3);
	}

	if($avg) {
		echo '<tr class="NoWrap odd">
				<td><b>overall avg:</b> '.$BioData->AverageArr.'</td>
				<td><b>comp. avg:</b> '.$avg.'</td>
			</tr>';
	}
	echo '</table>';

	// Medals
	echo '<table id="BioMedals">
		';
		echo '<tr><td colspan="4">
				<input type="checkbox" onclick="UpdateRows(this)" value="IsInd" checked="checked">Individual
				<input type="checkbox" onclick="UpdateRows(this)" value="IsTeam" checked="checked">Team<br/>';
// 		foreach($BioData->CompetitionLevels as $Id => $Desc) {
// 			if($Id==5) echo '<br/>';
// 			echo '<input type="checkbox" onclick="UpdateRows(this)" value="Lev'.$Id.'" checked="checked">'.$Desc;
// 		}
		foreach($BioData->caps as $Id => $Item) {
			echo '<span style="white-space:nowrap"><input type="checkbox" onclick="UpdateRows(this)" value="Lev'.$Id.'" checked="checked">'.$Item[0].' x '.$Item[1].'</span> ';
		}
		echo '</td></tr>';
		echo '<tr><th colspan="4" class="Title">Medals</th></tr>';
	$cnt=0;
	$Level=0;
	$IndWins=0;
	$IndPodiums=0;
	$TeamWins=0;
	$TeamPodiums=0;
	$COMPS='';
	foreach($BioData->Medals as $medal) {
		if($Level!=$medal->ComLevel) {
			$COMPS = str_replace(array('^^^','$$$','+++','°°°'), array($IndWins, $IndPodiums, $TeamWins, $TeamPodiums), $COMPS);
			$Level=$medal->ComLevel;
			$Wins=0;
			$Podiums=0;
			$COMPS.= '<tr class=" Lev'.$Level.'"><th colspan="4" class="Title">'.$BioData->CompetitionLevels->{$Level}.'<span style="font-weight:normal; font-size:80%"> - Ind. Wins ^^^ / Podiums $$$ - Team Wins +++ / Podiums °°°</span></th></tr>';
		}
		if($medal->IsTeam) {
			$Class="IsTeam";
			$TeamPodiums++;
			if($medal->Rnk==1) {
				$TeamWins++;
			}
		} else {
			$Class="IsInd";
			$IndPodiums++;
			if($medal->Rnk==1) {
				$IndWins++;
			}
		}
		if($cnt++ % 2 ==0) $Class.=" odd";
		$Class.=" Lev".$Level;
		$COMPS.= '<tr class="'.$Class.'">
			<td class="Rank">'.$medal->Rnk.'</td>
			<td class="NoWrap">'.$medal->Cat.($medal->IsTeam ? ' Team' : '').'</td>
			<td>'.$medal->ComName.' ('.$medal->ComNOC.')</td>
			<td class="NoWrap">'.date('j M Y', strtotime($medal->Date)).'</td>
			</tr>';
	}
	$COMPS = str_replace(array('^^^','$$$','+++','°°°'), array($IndWins, $IndPodiums, $TeamWins, $TeamPodiums), $COMPS);
	echo $COMPS;
	echo '</table>';

	// Biographic data
	echo '<table>
		<tr><th colspan="2" class="Title">Biography</th></tr>';
	$cnt=0;
	foreach($BioData->BioFields as $bio) {
		echo '<tr '.($cnt++ % 2 ==0 ? 'class="odd"' : '').'><th class="NoWrap">'.$bio->Title.'</th><td width="100%">'.$bio->Value.'</td></tr>';
	}
	echo '</table>';
	echo '</div>';
}


include('Common/Templates/tail-min.php');