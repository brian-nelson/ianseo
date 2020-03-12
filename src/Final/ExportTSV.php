<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');


CheckTourSession(true);
checkACL(array(AclIndividuals, AclTeams), AclReadOnly);

$rank=null;
$options['tournament'] = $_SESSION['TourId'];
//    $rank=Obj_RankFactory::create('GridTeam',$options);
$rank=Obj_RankFactory::create('GridInd',$options);
$rank->read();
$Data=$rank->getData();

$output = array();
$output[] = array('Event', 'Phase', 'MatchId',
    'Ath1 WaID', 'Ath1 FamilyName','Ath1 GivenName','Ath1 Noc','Ath1 Country', 'Ath1 Winner',
    'Ath2 WaID', 'Ath2 FamilyName','Ath2 GivenName','Ath2 Noc','Ath2 Country', 'Ath2 Winner',
    'Ath1 Score', 'Ath1 Ends','Ath1 So','Ath1 Arrows','Ath1 So Arrows',
    'Ath2 Score', 'Ath2 Ends','Ath2 So','Ath2 Arrows','Ath2 So Arrows',
);

foreach ($Data['sections'] as $EvCode=>$Phases) {
    if (!empty($Phases['phases'])) {
        foreach ($Phases['phases'] as $PhId => $Phase) {
            foreach ($Phase['items'] as $k => $v) {
                if (!(empty($v['id']) OR empty($v['oppId']))) {
                    $output[] = array(
                        $EvCode,
                        namePhase($Phases['meta']['firstPhase'], $PhId),
                        $v['matchNo'],
                        $v['bib'],
                        $v['familyName'],
                        $v['givenName'],
                        $v['countryCode'],
                        $v['countryName'],
                        $v['winner'],
                        $v['oppBib'],
                        $v['oppFamilyName'],
                        $v['oppGivenName'],
                        $v['oppCountryCode'],
                        $v['oppCountryName'],
                        $v['oppWinner'],
                        $v[($Phases['meta']['matchMode']==1 ? 'setScore' : 'score')],
                        str_replace('|',',',$v['setPoints']),
                        $v['tiebreakDecoded'],
                        implode(',', DecodeFromString(rtrim($v['arrowstring']), false, true)),
                        implode(',', DecodeFromString(rtrim($v['tiebreak']), false, true)),
                        $v[($Phases['meta']['matchMode']==1 ? 'oppSetScore' : 'oppScore')],
                        str_replace('|',',',$v['oppSetPoints']),
                        $v['oppTiebreakDecoded'],
                        implode(',', DecodeFromString(rtrim($v['oppArrowstring']), false, true)),
                        implode(',', DecodeFromString(rtrim($v['oppTiebreak']), false, true)),

                    );
                }
            }
        }
    }
}

$rank=Obj_RankFactory::create('GridTeam',$options);
$rank->read();
$Data=$rank->getData();

foreach ($Data['sections'] as $EvCode=>$Phases) {
    if (!empty($Phases['phases'])) {
        foreach ($Phases['phases'] as $PhId => $Phase) {
            foreach ($Phase['items'] as $k => $v) {
                if (!(empty($v['teamId']) OR empty($v['oppTeamId']))) {
                    $output[] = array(
                        $EvCode . '-T',
                        namePhase($Phases['meta']['firstPhase'], $PhId),
                        $v['matchNo'],
                        '',
                        '',
                        '',
                        $v['countryCode'],
                        $v['countryName'],
                        $v['winner'],
                        '',
                        '',
                        '',
                        $v['oppCountryCode'],
                        $v['oppCountryName'],
                        $v['oppWinner'],
                        $v[($Phases['meta']['matchMode']==1 ? 'setScore' : 'score')],
                        str_replace('|',',',$v['setPoints']),
                        $v['tiebreakDecoded'],
                        implode(',', DecodeFromString(rtrim($v['arrowstring']), false, true)),
                        implode(',', DecodeFromString(rtrim($v['tiebreak']), false, true)),
                        $v[($Phases['meta']['matchMode']==1 ? 'oppSetScore' : 'oppScore')],
                        str_replace('|',',',$v['oppSetPoints']),
                        $v['oppTiebreakDecoded'],
                        implode(',', DecodeFromString(rtrim($v['oppArrowstring']), false, true)),
                        implode(',', DecodeFromString(rtrim($v['oppTiebreak']), false, true)),

                    );
                }
            }
        }
    }
}


header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-Disposition: attachment; filename=' . $_SESSION["TourCode"] . '_Matches.csv');
header('Content-type: text/tab-separated-values; charset=' . PageEncode);

foreach ($output as $row) {
    echo implode(';',$row) . "\r\n";
}
