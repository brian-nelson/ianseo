<?php

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'DEN';
if(empty($SubRule)) {
    $SubRule='1';
}

function CreateStandardDivisions($TourId) {
	$optionDivs = array(
	    'R'=>'Recurve',
        'C'=>'Compound',
        'B'=>'Barbue',
        'L'=>'Langbue'
    );

    $i=1;
	foreach ($optionDivs as $k => $v){
		CreateDivision($TourId, $i++, $k, $v);
	}
}

function CreateStandardClasses($TourId, $SubRule) {
    $i=1;
    if($SubRule==1 OR $SubRule==4) {
        CreateClass($TourId, $i++, 1, 11, 1, 'DC', 'DC,DN,DA,DK,DJ,DS', 'Dame Micro');
        CreateClass($TourId, $i++, 1, 11, 0, 'HC', 'HC,HN,HA,HK,HJ,HS', 'Herre Micro');
        CreateClass($TourId, $i++, 12, 13, 1, 'DN', 'DN,DA,DK,DJ,DS', 'Dame Mini');
        CreateClass($TourId, $i++, 12, 13, 0, 'HN', 'HN,HA,HK,HJ,HS', 'Herre Mini');
        CreateClass($TourId, $i++, 14, 15, 1, 'DA', 'DA,DK,DJ,DS', 'Dame Aspirant');
        CreateClass($TourId, $i++, 14, 15, 0, 'HA', 'HA,HK,HJ,HS', 'Herre Aspirant');
        CreateClass($TourId, $i++, 16, 17, 1, 'DK', 'DK,DJ,DS', 'Dame Kadet');
        CreateClass($TourId, $i++, 16, 17, 0, 'HK', 'HK,HJ,HS', 'Herre Kadet');
        CreateClass($TourId, $i++, 18, 20, 1, 'DJ', 'DJ,DS', 'Dame Junior');
        CreateClass($TourId, $i++, 18, 20, 0, 'HJ', 'HJ,HS', 'Herre Junior');
    }
    if($SubRule==1 OR $SubRule==2) {
        CreateClass($TourId, $i++, 21, 49, 1, 'DS', 'DS', 'Dame Senior');
        CreateClass($TourId, $i++, 21, 49, 0, 'HS', 'HS', 'Herre Senior');
    }
    if($SubRule==1 OR $SubRule==3) {
        CreateClass($TourId, $i++, 50, 99, 1, 'DM', 'DM,DS', 'Dame Master');
        CreateClass($TourId, $i, 50, 99, 0, 'HM', 'HM,HS', 'Herre Master');
    }
}

function CreateStandardSubClasses($TourId) {
    $i=1;
    CreateSubClass($TourId, $i++, 'E', 'Elite');
    CreateSubClass($TourId, $i++, '1', 'Klasse 1');
    CreateSubClass($TourId, $i++, '2', 'Klasse 2');
    CreateSubClass($TourId, $i, '3', 'Uden for klasse');
}

function CreateStandardEvents($TourId, $SubRule, $TourType) {
    $Outdoor = ($TourType!=6 AND $TourType!=7);
	$TargetR=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetC=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_small10);
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetL=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetSizeR=($Outdoor ? 122 : ($TourType==6 ? 40 : 60));
	$TargetSizeC=($Outdoor ? 80 : ($TourType==6 ? 40 : 60));
    $TargetSizeB=($Outdoor ? 122 : ($TourType==6 ? 40 : 60));
    $TargetSizeL=($Outdoor ? 122 : ($TourType==6 ? 40 : 60));

	$FirstPhase = ($Outdoor ? 16 : 16);
	$TeamFirstPhase = ($Outdoor ? 8 : 8);

    $divNames = array(
        'R'=>'Recurve',
        'C'=>'Compound',
        'B'=>'Barbue',
        'L'=>'Langbue'
    );
    $classNames = array(
        'DC' => 'Dame Micro',
        'HC' => 'Herre Micro',
        'DN' => 'Dame Mini',
        'HN' => 'Herre Mini',
        'DA' => 'Dame Aspirant',
        'HA' => 'Herre Aspirant',
        'DK' => 'Dame Kadet',
        'HK' => 'Herre Kadet',
        'DJ' => 'Dame Junior',
        'HJ' => 'Herre Junior',
        'DS' => 'Dame Senior',
        'HS' => 'Herre Senior',
        'DM' => 'Dame Master',
        'HM' => 'Herre Master'
    );
    $arrIndividual = array(array(array()));
    $arrTeam = array(array(array()));
    $evNameInd = '';
    $evNameTeam = '';
    if($Outdoor) {
        switch ($SubRule) {
            case '1':
                $arrIndividual = array(
                    15 => array(
                        '15L'=> array('L' => array('HC', 'DC')),
                        '15B'=> array('B' => array('HC', 'DC')),
                        '15C'=> array('C' => array('HC', 'DC')),
                        '15R'=> array('R' => array('HC', 'DC'))
                    ),
                    30 => array(
                        '30L'=> array('L' => array('DN', 'HN', 'DA', 'HA', 'DK', 'HK')),
                        '30B'=> array('B' => array('DN', 'HN', 'DA', 'HA', 'DK', 'HK')),
                        '30C'=> array('C' => array('DN', 'HN', 'DA', 'HA')),
                        '30R'=> array('R' => array('DN', 'HN'))
                    ),
                    40 => array(
                        '40L'=> array('L' => array('DS', 'HS', 'DJ', 'HJ', 'DM', 'HM')),
                        '40B'=> array('B' => array('DS', 'HS', 'DJ', 'HJ', 'DM', 'HM')),
                        '40R'=> array('R' => array('DA', 'HA'))
                    ),
                    50 => array(
                        '50C'=> array('C' => array('HS', 'DS', 'HM', 'DM', 'HJ', 'DJ', 'HK', 'DK'))
                    ),
                    60 => array(
                        '60R'=> array('R' => array('HM', 'DM', 'HK', 'DK'))
                    ),
                    70 => array(
                        '70R'=> array('R' => array('HS', 'DS', 'HJ', 'DJ'))
                    )
                );
                $evNameInd = '$kDist.\'m \'.$divNames[$kDiv]';
                $arrTeam = $arrIndividual;
                $evNameTeam = $evNameInd;
                break;
            case 2:
                $arrIndividual = array(
                    40 => array(
                        'LD' => array('L' => array('DS')),
                        'LH' => array('L' => array('HS')),
                        'BD' => array('B' => array('DS')),
                        'BH' => array('B' => array('HS'))
                    ),
                    50 => array(
                        'CD' => array('C' => array('DS')),
                        'CH' => array('C' => array('HS'))
                    ),
                    70 => array(
                        'RD' => array('R' => array('DS')),
                        'RH' => array('R' => array('HS'))
                    )
                );
                $arrTeam = array(
                    40 => array(
                        'L' => array('L' => array('DS', 'HS')),
                        'B' => array('B' => array('DS', 'HS')),
                    ),
                    50 => array(
                        'C' => array('C' => array('DS', 'HS'))
                    ),
                    70 => array(
                        'R' => array('R' =>  array('DS', 'HS'))
                    )
                );
                $evNameInd = '$divNames[$kDiv] . (substr($kEv,1,1)==\'H\' ? \' herrer\':\' damer\')';
                $evNameTeam = '$divNames[$kDiv]';
                break;
            case 3:
                $arrIndividual = array(
                    40 => array(
                        'LD' => array('L' => array('DM')),
                        'LH' => array('L' => array('HM')),
                        'BD' => array('B' => array('DM')),
                        'BH' => array('B' => array('HM'))
                    ),
                    50 => array(
                        'CD' => array('C' => array('DM')),
                        'CH' => array('C' => array('HM'))
                    ),
                    60 => array(
                        'RD' => array('R' => array('DM')),
                        'RH' => array('R' => array('HM'))
                    )
                );
                $arrTeam = array(
                    40 => array(
                        'L' => array('L' => array('DM', 'HM')),
                        'B' => array('B' => array('DM', 'HM')),
                    ),
                    50 => array(
                        'C' => array('C' => array('DM', 'HM'))
                    ),
                    70 => array(
                        'R' => array('R' =>  array('DM', 'HM'))
                    )
                );
                $evNameInd = '$divNames[$kDiv] . (substr($kEv,1,1)==\'H\' ? \' herrer\':\' damer\')';
                $evNameTeam = '$divNames[$kDiv]';
                break;
            case 4:
                $arrIndividual = array(
                    15 => array(
                        'DCL'=> array('L' => array('DC')),
                        'HCL'=> array('L' => array('HC')),
                        'DCB'=> array('B' => array('DC')),
                        'HCB'=> array('B' => array('HC')),
                        'DCC'=> array('C' => array('DC')),
                        'HCC'=> array('C' => array('HC')),
                        'DCR'=> array('R' => array('DC')),
                        'HCR'=> array('R' => array('HC'))
                    ),
                    30 => array(
                        'DNL'=> array('L' => array('DN')),
                        'HNL'=> array('L' => array('HN')),
                        'DNB'=> array('B' => array('DN')),
                        'HNB'=> array('B' => array('HN')),
                        'DNC'=> array('C' => array('DN')),
                        'HNC'=> array('C' => array('HN')),
                        'DNR'=> array('R' => array('DN')),
                        'HNR'=> array('R' => array('HN')),
                        'DAL'=> array('L' => array('DA')),
                        'HAL'=> array('L' => array('HA')),
                        'DAB'=> array('B' => array('DA')),
                        'HAB'=> array('B' => array('HA')),
                        'DAC'=> array('C' => array('DA')),
                        'HAC'=> array('C' => array('HA')),
                        'DKL'=> array('L' => array('DK')),
                        'HKL'=> array('L' => array('HK')),
                        'DKB'=> array('B' => array('DK')),
                        'HKB'=> array('B' => array('HK')),
                    ),
                    40 => array(
                        'DAR'=> array('R' => array('DA')),
                        'HAR'=> array('R' => array('HA')),
                        'DJL'=> array('L' => array('DJ')),
                        'HJL'=> array('L' => array('HJ')),
                        'DJB'=> array('B' => array('DJ')),
                        'HJB'=> array('B' => array('HJ')),
                    ),
                    50 => array(
                        'DKC'=> array('C' => array('DK')),
                        'HKC'=> array('C' => array('HK')),
                        'DJC'=> array('C' => array('DJ')),
                        'HJC'=> array('C' => array('HJ')),
                    ),
                    60 => array(
                        'DKR'=> array('R' => array('DK')),
                        'HKR'=> array('R' => array('HK')),
                    ),
                    70 => array(
                        'DJR'=> array('R' => array('DJ')),
                        'HJR'=> array('R' => array('HJ')),
                    )
                );
                $arrTeam = array(
                    15 => array(
                        '15L'=> array('L' => array('HC', 'DC')),
                        '15B'=> array('B' => array('HC', 'DC')),
                        '15C'=> array('C' => array('HC', 'DC')),
                        '15R'=> array('R' => array('HC', 'DC'))
                    ),
                    30 => array(
                        '30L'=> array('L' => array('DN', 'HN', 'DA', 'HA', 'DK', 'HK')),
                        '30B'=> array('B' => array('DN', 'HN', 'DA', 'HA', 'DK', 'HK')),
                        '30C'=> array('C' => array('DN', 'HN', 'DA', 'HA')),
                        '30R'=> array('R' => array('DN', 'HN'))
                    ),
                    40 => array(
                        '40L'=> array('L' => array('DJ', 'HJ')),
                        '40B'=> array('B' => array('DJ', 'HJ')),
                        '40R'=> array('R' => array('DA', 'HA'))
                    ),
                    50 => array(
                        '50C'=> array('C' => array('HJ', 'DJ', 'HK', 'DK'))
                    ),
                    60 => array(
                        '60R'=> array('R' => array('HK', 'DK'))
                    ),
                    70 => array(
                        '70R'=> array('R' => array('HJ', 'DJ'))
                    )
                );
                $evNameInd = '$classNames[substr($kEv,0,2)] . \' \' . $divNames[$kDiv]';
                $evNameTeam = '$kDist.\'m \'.$divNames[$kDiv]';
                break;
        }
	} else if($TourType==7) {
        $arrIndividual = array(
            10 => array(
                '10L'=> array('L' => array('HC', 'DC')),
                '10B'=> array('B' => array('HC', 'DC')),
                '10C'=> array('C' => array('HC', 'DC')),
                '10R'=> array('R' => array('HC', 'DC'))
            ),
            15 => array(
                '15L'=> array('L' => array('DN', 'HN', 'DA', 'HA')),
                '15B'=> array('B' => array('DN', 'HN', 'DA', 'HA')),
                '15C'=> array('C' => array('DN', 'HN', 'DA', 'HA')),
                '15R'=> array('R' => array('DN', 'HN', 'DA', 'HA'))
            ),
            25 => array(
                '25L'=> array('L' => array('DS', 'HS', 'DJ', 'HJ', 'DM', 'HM', 'DK', 'HK')),
                '25B'=> array('B' => array('DS', 'HS', 'DJ', 'HJ', 'DM', 'HM', 'DK', 'HK')),
                '25C'=> array('C' => array('DS', 'HS', 'DJ', 'HJ', 'DM', 'HM', 'DK', 'HK')),
                '25R'=> array('R' => array('DS', 'HS', 'DJ', 'HJ', 'DM', 'HM', 'DK', 'HK')),
            )
        );
        $evNameInd = '$kDist.\'m \'.$divNames[$kDiv]';
        $arrTeam = $arrIndividual;
        $evNameTeam = $evNameInd;
    } else {
        switch ($SubRule) {
            case 1:
                $arrIndividual = array(
                    8 => array(
                        '08L'=> array('L' => array('HC', 'DC')),
                        '08B'=> array('B' => array('HC', 'DC')),
                        '08C'=> array('C' => array('HC', 'DC')),
                        '08R'=> array('R' => array('HC', 'DC'))
                    ),
                    12 => array(
                        '12L'=> array('L' => array('DN', 'HN', 'DA', 'HA')),
                        '12B'=> array('B' => array('DN', 'HN', 'DA', 'HA')),
                        '12C'=> array('C' => array('DN', 'HN', 'DA', 'HA')),
                        '12R'=> array('R' => array('DN', 'HN', 'DA', 'HA'))
                    ),
                    18 => array(
                        '18L'=> array('L' => array('DS', 'HS', 'DJ', 'HJ', 'DM', 'HM', 'DK', 'HK')),
                        '18B'=> array('B' => array('DS', 'HS', 'DJ', 'HJ', 'DM', 'HM', 'DK', 'HK')),
                        '18C'=> array('C' => array('DS', 'HS', 'DJ', 'HJ', 'DM', 'HM', 'DK', 'HK')),
                        '18R'=> array('R' => array('DS', 'HS', 'DJ', 'HJ', 'DM', 'HM', 'DK', 'HK')),
                    )
                );
                $evNameInd = '$kDist.\'m \'.$divNames[$kDiv]';
                $arrTeam = $arrIndividual;
                $evNameTeam = $evNameInd;
                break;
            case 2:
                $arrIndividual = array(
                    18 => array(
                        'LD' => array('L' => array('DS')),
                        'LH' => array('L' => array('HS')),
                        'BD' => array('B' => array('DS')),
                        'BH' => array('B' => array('HS')),
                        'CD' => array('C' => array('DS')),
                        'CH' => array('C' => array('HS')),
                        'RD' => array('R' => array('DS')),
                        'RH' => array('R' => array('HS'))
                    )
                );
                $arrTeam = array(
                    18 => array(
                        'L' => array('L' => array('DS', 'HS')),
                        'B' => array('B' => array('DS', 'HS')),
                        'C' => array('C' => array('DS', 'HS')),
                        'R' => array('R' => array('DS', 'HS'))
                    )
                );
                $evNameInd = '$divNames[$kDiv] . (substr($kEv,1,1)==\'H\' ? \' herrer\':\' damer\')';
                $evNameTeam = '$divNames[$kDiv]';
                break;
            case 3:
                $arrIndividual = array(
                    18 => array(
                        'LD' => array('L' => array('DM')),
                        'LH' => array('L' => array('HM')),
                        'BD' => array('B' => array('DM')),
                        'BH' => array('B' => array('HM')),
                        'CD' => array('C' => array('DM')),
                        'CH' => array('C' => array('HM')),
                        'RD' => array('R' => array('DM')),
                        'RH' => array('R' => array('HM'))
                    )
                );
                $arrTeam = array(
                    18 => array(
                        'L' => array('L' => array('DM', 'HM')),
                        'B' => array('B' => array('DM', 'HM')),
                        'C' => array('C' => array('DM', 'HM')),
                        'R' => array('R' => array('DM', 'HM'))
                    )
                );
                $evNameInd = '$divNames[$kDiv] . (substr($kEv,1,1)==\'H\' ? \' herrer\':\' damer\')';
                $evNameTeam = '$divNames[$kDiv]';
                break;
            case 4:
                $arrIndividual = array(
                    8 => array(
                        'DCL'=> array('L' => array('DC')),
                        'HCL'=> array('L' => array('HC')),
                        'DCB'=> array('B' => array('DC')),
                        'HCB'=> array('B' => array('HC')),
                        'DCC'=> array('C' => array('DC')),
                        'HCC'=> array('C' => array('HC')),
                        'DCR'=> array('R' => array('DC')),
                        'HCR'=> array('R' => array('HC'))
                    ),
                    12 => array(
                        'DNL'=> array('L' => array('DN')),
                        'HNL'=> array('L' => array('HN')),
                        'DNB'=> array('B' => array('DN')),
                        'HNB'=> array('B' => array('HN')),
                        'DNC'=> array('C' => array('DN')),
                        'HNC'=> array('C' => array('HN')),
                        'DNR'=> array('R' => array('DN')),
                        'HNR'=> array('R' => array('HN')),
                        'DAL'=> array('L' => array('DA')),
                        'HAL'=> array('L' => array('HA')),
                        'DAB'=> array('B' => array('DA')),
                        'HAB'=> array('B' => array('HA')),
                        'DAC'=> array('C' => array('DA')),
                        'HAC'=> array('C' => array('HA')),
                        'DAR'=> array('R' => array('DA')),
                        'HAR'=> array('R' => array('HA'))
                    ),
                    18 => array(
                        'DKL'=> array('L' => array('DK')),
                        'HKL'=> array('L' => array('HK')),
                        'DKB'=> array('B' => array('DK')),
                        'HKB'=> array('B' => array('HK')),
                        'DKC'=> array('C' => array('DK')),
                        'HKC'=> array('C' => array('HK')),
                        'DKR'=> array('R' => array('DK')),
                        'HKR'=> array('R' => array('HK')),
                        'DJL'=> array('L' => array('DJ')),
                        'HJL'=> array('L' => array('HJ')),
                        'DJB'=> array('B' => array('DJ')),
                        'HJB'=> array('B' => array('HJ')),
                        'DJC'=> array('C' => array('DJ')),
                        'HJC'=> array('C' => array('HJ')),
                        'DJR'=> array('R' => array('DJ')),
                        'HJR'=> array('R' => array('HJ'))
                    )
                );
                $arrTeam = array(
                    8 => array(
                        '08L'=> array('L' => array('HC', 'DC')),
                        '08B'=> array('B' => array('HC', 'DC')),
                        '08C'=> array('C' => array('HC', 'DC')),
                        '08R'=> array('R' => array('HC', 'DC'))
                    ),
                    12 => array(
                        '12L'=> array('L' => array('DN', 'HN', 'DA', 'HA')),
                        '12B'=> array('B' => array('DN', 'HN', 'DA', 'HA')),
                        '12C'=> array('C' => array('DN', 'HN', 'DA', 'HA')),
                        '12R'=> array('R' => array('DN', 'HN', 'DA', 'HA'))
                    ),
                    18 => array(
                        '18L'=> array('L' => array('DJ', 'HJ', 'DK', 'HK')),
                        '18B'=> array('B' => array('DJ', 'HJ', 'DK', 'HK')),
                        '18C'=> array('C' => array('DJ', 'HJ', 'DK', 'HK')),
                        '18R'=> array('R' => array('DJ', 'HJ', 'DK', 'HK'))
                    )
                );
                $evNameInd = '$classNames[substr($kEv,0,2)] . \' \' . $divNames[$kDiv]';
                $evNameTeam = '$kDist.\'m \'.$divNames[$kDiv]';
                break;
        }
    }

    //Reorder some events in Youth Outdoor
    $newPosition=array("DAR"=>23,"HAR"=>24,"DKL"=>25,"HKL"=>26,"DKB"=>27,"HKB"=>28,"DKC"=>29,"HKC"=>30,"DKR"=>31,"HKR"=>32,"DJL"=>33,"HJL"=>34,"DJB"=>35,"HJB"=>36,"DJC"=>37,"HJC"=>38);

    $i=1;
    foreach ($arrIndividual as $kDist=>$arrEvents) {
        foreach ($arrEvents as $kEv=>$arrDivs) {
            foreach ($arrDivs as $kDiv => $arrClasses) {
                $iName = '';
                $posOrder = $i++;
                if($Outdoor AND $SubRule==4 and array_key_exists($kEv,$newPosition)) {
                    $posOrder = $newPosition[$kEv];
                }
                eval('$iName = ' . $evNameInd . ';');
                CreateEvent($TourId, $posOrder, 0, 0, $FirstPhase, ${"Target" . $kDiv}, 5, 3, 1, 5, 3, 1, $kEv, $iName, ($kDiv == 'C' ? 0 : 1), FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, '', '', ${"TargetSize" . $kDiv}, $kDist);
                foreach ($arrClasses as $vClass) {
                    InsertClassEvent($TourId, 0, 1, $kEv, $kDiv, $vClass);
                }
            }
        }
    }
    $i=1;
    foreach ($arrTeam as $kDist=>$arrEvents) {
        foreach ($arrEvents as $kEv=>$arrDivs) {
            foreach ($arrDivs as $kDiv => $arrClasses) {
                $tName = '';
                eval('$tName = ' . $evNameTeam . ';');
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, ${"Target" . $kDiv}, 4, 6, 3, 4, 6, 3, $kEv, $tName, ($kDiv == 'C' ? 0 : 1), FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, '', '', ${"TargetSize" . $kDiv}, $kDist, '', 1);
                foreach ($arrClasses as $vClass) {
                    InsertClassEvent($TourId, 1, 3, $kEv, $kDiv, $vClass);
                }
            }
        }
    }

}

