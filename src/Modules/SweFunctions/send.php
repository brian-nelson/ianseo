<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

CheckTourSession(true);

require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');

require_once(dirname(__FILE__).'/config.php');
$url = $swe_url_result;

// Read post_body for json data
$request_body = file_get_contents('php://input');
$data = json_decode($request_body,true);

//
$my_json_body = array();
$have_data_to_send = false;
// Send qvalification round

if ($data['option']['ind_kval'] == 1) {
    $data['qualification'] = getIndQualification();
    $have_data_to_send = true;
}
// Send finL round

if ($data['option']['ind_final'] == 1) {
    $data['final'] = getIndFinals();
    $have_data_to_send = true;
}
unset($data['option']);
//print_r($data);

if ($have_data_to_send) {
    echo sendInformation($url, json_encode($data));
} else {
    $rdata = array('status'=>'Failed','message'=>$swelang_noinformation_sent);
    echo json_encode($rdata);
}



function getIndQualification() {
    //.
    $options=array();
    $rank=Obj_RankFactory::create('DivClass',$options);
    $rank->read();
    return prepareIndQualification($rank->getData());
}

function getIndFinals() {
    //.
    $options=array();
    $rank=Obj_RankFactory::create('FinalInd',$options);
    $rank->read();
    $rankData=$rank->getData();
    if (count($rankData['sections']) > 0)
    {
        return prepareIndFinals($rankData);
    }
    return "";
}

function prepareIndQualification($data) {
    $sektions = array();
    foreach ($data['sections'] as $key => $value) {
        $item = array();
        foreach($value['items'] as $ikey => $ivalue) {
            $item[] = array(
                'bib'=> $ivalue['bib'],
                'familyname' => $ivalue['familyname'],
                'givenname' => $ivalue['givenname'],
                'gender' => $ivalue['gender'],
                'div' => $ivalue['div'],
                'class' => $ivalue['class'],
                'ageclass' => $ivalue['ageclass'],
                'subclass' => $ivalue['subclass'],
                'clubId' => $ivalue['countryCode'],
                'clubName' => $ivalue['countryName'],
                'rank' => $ivalue['rank'],
                'score' => $ivalue['score'],
                'gold' => $ivalue['gold'],
                'xnine' => $ivalue['xnine'],
                'dist_1' => $ivalue['dist_1'],
                'dist_2' => $ivalue['dist_2'],
                'dist_3' => $ivalue['dist_3'],
                'dist_4' => $ivalue['dist_4'],
                'dist_5' => $ivalue['dist_5'],
                'dist_6' => $ivalue['dist_6'],
                'dist_7' => $ivalue['dist_7'],
                'dist_8' => $ivalue['dist_8'],
                'hits' => $ivalue['arrowsShot'],
                'id' => $ivalue['id']
            );
        }

        $sektions[] = array('q_class' => $key, 'q_results' => array(
            'q_meta' => array(
                'descr' => $value['meta']['descr'],
                'numdist' => $value['meta']['numDist'],
                'maxArrows' => $value['meta']['maxArrows'],
                'score' => $value['meta']['fields']['score'],
                'gold' => $value['meta']['fields']['gold'],
                'xnine' => $value['meta']['fields']['xnine'],
                'dist_1' => $value['meta']['fields']['dist_1'],
                'dist_2' => $value['meta']['fields']['dist_2'],
                'dist_3' => $value['meta']['fields']['dist_3'],
                'dist_4' => $value['meta']['fields']['dist_4'],
                'dist_5' => $value['meta']['fields']['dist_5'],
                'dist_6' => $value['meta']['fields']['dist_6'],
                'dist_7' => $value['meta']['fields']['dist_7'],
                'dist_8' => $value['meta']['fields']['dist_8']
            ),
            'q_items' => $item
        ));
    }
    return $sektions;
}

function prepareIndFinals($data) {
    $returnData = array();
    foreach ($data['sections'] as $key => $value) {
        $item = array();
        foreach($value['items'] as $ikey => $ivalue) {
            $currentFinals = array();
            foreach($ivalue['finals'] as $fkey => $fvalue) {
                $currentFinals[$fkey] = array(
                    'athleteId' => $ivalue['id'],
                    'score' => $fvalue['score'],
                    'setScore' => $fvalue['setScore'],
                    'tie' => $fvalue['tie'],
                    'tiebreak' => $fvalue['tiebreak'],
                    'oppAthleteId' => $fvalue['oppAthlete'],
                    'oppAthlete' => $value['items'][$fvalue['oppAthlete']]['bib'],
                    'oppScore' => $fvalue['oppScore'],
                    'oppSetScore' => $fvalue['oppSetScore'],
                    'oppTie' => $fvalue['oppTie'],
                    'oppTiebreak' => $fvalue['oppTiebreak']
                );
            }

            $item[] = array(
                'id' => $ivalue['id'],
                'bib'=> $ivalue['bib'],
                'familyname' => $ivalue['familyname'],
                'givenname' => $ivalue['givenname'],
                'gender' => $ivalue['gender'],
                'clubId' => $ivalue['countryCode'],
                'clubName' => $ivalue['countryName'],
                'rank' => $ivalue['rank'],
                'elims' => $ivalue['elims'],
                'finals' => $currentFinals
            );
        }
        unset($value['meta']['fields']['finals']['fields']);
        $returnData[] = array('f_class' => $key, 'f_results' => array(
            'f_meta' => array(
                'descr' => $value['meta']['descr'],
                'firstPhase' => $value['meta']['firstPhase'],
                'elim1' => $value['meta']['fields']['elim1'],
                'elim2' => $value['meta']['fields']['elim2'],
                'finals' => $value['meta']['fields']['finals']
            ),
            'f_items' => $item
        ));
    }
    return $returnData;
}

function sendInformation($url, $data) {
    $hand = curl_init($url);
    curl_setopt($hand, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($hand, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($hand, CURLOPT_POSTFIELDS, $data);
    curl_setopt($hand, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($hand, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($hand, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
    $result = curl_exec($hand);
    curl_close($hand);
    return $result;
}
?>
