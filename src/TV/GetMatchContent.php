<?php
define('debug',false);	// settare a true per l'output di debug

define('IN_PHP', true);

require_once(dirname(dirname(__FILE__)) . '/config.php');
if (empty($_REQUEST['RuleId'])) printCrackError();
require_once('Common/Fun_FormatText.inc.php');
require_once('TV/Fun_HTML.local.inc.php');

$RuleId=intval($_REQUEST['RuleId']);
$TourId=intval($_REQUEST['TourId']);
checkACL(AclOutput,AclReadOnly, false, $TourId);


// get the defaults of the rule
$q=safe_r_sql("select * from TVRules where TVRId=$RuleId AND TVRTournament=$TourId");
if(!($RULE=safe_fetch($q))) printCrackError();

// set the correct defines for the torunament
set_defines($RULE->TVRTournament);

// Estraggo gli spezzoni di regola
$Select
	= "SELECT * FROM TVSequence "
	. "INNER JOIN TVParams on TVPId=TVSContent AND TVPTournament=TVSTournament "
	. "WHERE TVSRule=$RuleId AND TVSTournament=$TourId AND TVSTable='DB' AND TVPPage='FIN' "
	. "ORDER BY TVSOrder ";

$Rs=safe_r_sql($Select);
if(!safe_num_rows($Rs)) printcrackerror();

$ret='';
$Arr_Ev = array();
$Arr_Ph = array();
$ViewIdCard=false;

while($TVsettings=safe_fetch($Rs)) {
	if($TVsettings->TVPEventInd) foreach(explode('|', $TVsettings->TVPEventInd) as $Ev) $Arr_Ev[] = $Ev;
	if(strlen($TVsettings->TVPPhasesInd)) foreach(explode('|', $TVsettings->TVPPhasesInd) as $Ph) $Arr_Ph[] = $Ph;
	$ViewIdCard=($ViewIdCard or $TVsettings->TVPViewIdCard);
}

require_once('Common/Lib/Obj_RankFactory.php');
$options=array('tournament' => $TourId);

if($Arr_Ev and count($Arr_Ph)) {
	$options['events']=array();
	foreach($Arr_Ph as $p) {
		foreach($Arr_Ev as $e) $options['events'][] = $e . '@' . $p;
	}
} elseif(count($Arr_Ph)) {
	$options['events']=array();
	if(strstr($Arr_Ph[0], '+')) {
		foreach($Arr_Ph as $p) {
			$t=explode('+',$p);
			$l=array_shift($t);
			foreach($t as $e) $options['events'][] = $l . '@' . $e;
		}
	} else {
		foreach($Arr_Ph as $p) {
			$options['events'][] = '@' . $p;
		}
	}
} elseif($Arr_Ev) {
	$options['events'] = $Arr_Ev;
}

$rank=Obj_RankFactory::create('GridInd',$options);
$rank->read();
$rankData=$rank->getData();

if(count($rankData['sections'])==0) return '';

if($ViewIdCard) {
	foreach($rankData['sections'] as $IdEvent => $section) {
		foreach($section['phases'] as $IdPhase => $phase) {
			foreach($phase['items'] as $key => $item) {
				$IsBye=($item['tie']==2 or $item['oppTie']==2);

				$Score='&nbsp;';

				if($IsBye) {
					$Score = '<i style="font-size:75%">'.get_text('Bye').'</i>';
				} else {
					$SxFinScore = trim($section['meta']['matchMode'] ? $item['setScore']    : $item['score']);
					$DxFinScore = trim($section['meta']['matchMode'] ? $item['oppSetScore'] : $item['oppScore']);
					$TieBreak='';
					if($item['tie']==1 or $item['oppTie']==1) {
						if($item['tiebreakDecoded'] or $item['oppTiebreakDecoded']) {
							$TieBreak = '<div style="font-size:60%">T.&nbsp;'
								. $item['tiebreakDecoded']
								. '-'
								. $item['oppTiebreakDecoded']
								. '<tt>&nbsp;&nbsp;</tt></div>';
						} elseif($item['tie']==1) {
							$SxFinScore .= '*';
						} else {
							$DxFinScore .= '*';
						}
					}
					$Score= $SxFinScore . '-' . $DxFinScore;
					if($TieBreak) $Score .= $TieBreak;
				}

				$ret.= '<matchid><![CDATA[match_'.$IdEvent.'_'.$item['matchNo'].']]></matchid>';
				$ret.= '<score><![CDATA[' . $Score . ']]></score>';
			}
		}
	}
} else {
	foreach($rankData['sections'] as $IdEvent => $section) {
		foreach($section['phases'] as $IdPhase => $phase) {
			foreach($phase['items'] as $key => $item) {
				$Score='&nbsp;';
				if($item['tie']==2) {
					// it is a bye
					$Score = get_text('Bye') ;
				} elseif($item['athlete']) {
					// archer is there
					$Score = ( $item[$section['meta']['matchMode'] ? 'setScore' : 'score'] . ($item['tie']==1 ? '*' : '') );
				}

				$ret.= '<matchid><![CDATA[match_'.$IdEvent.'_'.$item['matchNo'].']]></matchid>';
				$ret.= '<score><![CDATA[' . $Score . ']]></score>';

				$Score='&nbsp;';
				if($item['oppTie']==2) {
					// it is a bye
					$Score = get_text('Bye') ;
				} elseif($item['oppAthlete']) {
					// archer is there
					$Score = ( $item[$section['meta']['matchMode'] ? 'oppSetScore' : 'oppScore'] . ($item['oppTie']==1 ? '*' : '') );
				}

				$ret.= '<matchid><![CDATA[match_'.$IdEvent.'_'.$item['oppMatchNo'].']]></matchid>';
				$ret.= '<score><![CDATA[' . $Score . ']]></score>';
			}
		}
	}
}

header('Content-type: text/xml');

echo '<response>';
echo $ret;
echo '</response>';
