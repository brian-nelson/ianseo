<?php

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = '';
if(empty($SubRule)) {
    $SubRule='1';
}

function CreateStandardDivisions($TourId, $SubRule, $Type='FITA') {
	$i=1;
	if($SubRule=='1') {
        CreateDivision($TourId, $i++, 'R', 'Recurve Open', '1', 'R', 'R', 1);
        CreateDivision($TourId, $i++, 'C', 'Compound Open', '1', 'C', 'C', 1);
    } elseif($SubRule=='2') {
        CreateDivision($TourId, $i++, 'RO', 'Recurve Open', '1', 'R', 'R', 1);
        CreateDivision($TourId, $i++, 'CO', 'Compound Open', '1', 'C', 'C', 1);
        CreateDivision($TourId, $i++, 'R', 'Recurve', '1', 'R', 'R', 0);
        CreateDivision($TourId, $i++, 'C', 'Compound', '1', 'C', 'C', 0);
    }
	CreateDivision($TourId, $i++, 'W1', 'W1 Open (Rec/Comp)','1','W1','W1',1);
	CreateDivision($TourId, $i++, 'VI', 'Visually Impaired','1','VI','VI',1);
}

function CreateStandardClasses($TourId, $SubRule) {
	CreateClass($TourId, 1, 1,100, 0, 'M', 'M', 'Men', 1, ($SubRule==2 ? 'RO,CO,' : '') . 'C,R,W1','M','M',1);
	CreateClass($TourId, 2, 1,100, 1, 'W', 'W', 'Women', 1, ($SubRule==2 ? 'RO,CO,' : '') . 'C,R,W1','W','W',1);
	CreateClass($TourId, 3, 1,100, -1, '1', '1', '1', 1, 'VI','1','1',1);
	CreateClass($TourId, 4, 1,100, -1, '23', '23', '2/3', 1, 'VI','23','23',1);
}

function CreateStandardEvents($TourId, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
    $TargetW1=($Outdoor?5:4);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$TargetSizeV=($Outdoor ? 80 : 60);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
	$DistanceV=($Outdoor ? 30 : 18);

	$Settings=array(
		'EvElimEnds'=>5,
		'EvElimArrows'=>3,
		'EvElimSO'=>1,
		'EvFinEnds'=>5,
		'EvFinArrows'=>3,
		'EvFinSO'=>1,
		'EvFinalAthTarget'=>240,
		'EvMatchArrowsNo'=>240,
		'EvIsPara'=>1,
		'EvMatchMode'=>1,
		'EvFinalFirstPhase' => 16,
		'EvFinalTargetType'=>$TargetR,
		'EvTargetSize'=>$TargetSizeR,
		'EvDistance'=>$DistanceR,
	);


	$i=1;
	CreateEventNew($TourId, 'RMO', 'Recurve Men Open', $i++, $Settings);
	CreateEventNew($TourId, 'RWO', 'Recurve Women Open', $i++, $Settings);
	if($SubRule=='2') {
        $Settings['EvIsPara']=0;
        CreateEventNew($TourId, 'RM', 'Recurve Men', $i++, $Settings);
        CreateEventNew($TourId, 'RW', 'Recurve Women', $i++, $Settings);
        $Settings['EvIsPara']=1;
    }
	$Settings['EvMatchMode']=0;
	$Settings['EvFinalFirstPhase']=32;
	$Settings['EvFinalTargetType']=$TargetC;
	$Settings['EvTargetSize']=$TargetSizeC;
	$Settings['EvDistance']=$DistanceC;
	CreateEventNew($TourId, 'CMO', 'Compound Men Open', $i++, $Settings);
	$Settings['EvFinalFirstPhase']=16;
	CreateEventNew($TourId, 'CWO', 'Compound Women Open', $i++, $Settings);
    if($SubRule=='2') {
        $Settings['EvIsPara']=0;
        $Settings['EvFinalFirstPhase']=32;
        CreateEventNew($TourId, 'CM', 'Compound Men', $i++, $Settings);
        $Settings['EvFinalFirstPhase']=16;
        CreateEventNew($TourId, 'CW', 'Compound Women', $i++, $Settings);
        $Settings['EvIsPara']=1;
    }
	$Settings['EvFinalFirstPhase']=8;
	$Settings['EvFinalTargetType']=$TargetW1;
	CreateEventNew($TourId, 'MW1', 'Men W1 Open (Rec/Comp)', $i++, $Settings);
	CreateEventNew($TourId, 'WW1', 'Women W1 Open (Rec/Comp)', $i++, $Settings);
	$Settings['EvMatchMode']=1;
	$Settings['EvFinalFirstPhase']=2;
	$Settings['EvFinalTargetType']=$TargetR;
	$Settings['EvTargetSize']=$TargetSizeV;
	$Settings['EvDistance']=$DistanceV;
	CreateEventNew($TourId, 'VI1', 'Visually Impaired 1', $i++, $Settings);
	CreateEventNew($TourId, 'VI23', 'Visually Impaired 2/3', $i++, $Settings);
	$i=1;
	$Settings['EvTeamEvent']=1;
	$Settings['EvFinalAthTarget']=0;
	$Settings['EvMatchArrowsNo']=0;
	$Settings['EvElimEnds']=4;
	$Settings['EvElimArrows']=6;
	$Settings['EvElimSO']=3;
	$Settings['EvFinEnds']=4;
	$Settings['EvFinArrows']=6;
	$Settings['EvFinSO']=3;
	$Settings['EvMatchArrowsNo']=0;
	$Settings['EvMatchMode']=1;
	$Settings['EvFinalFirstPhase']=8;
	$Settings['EvFinalTargetType']=$TargetR;
	$Settings['EvTargetSize']=$TargetSizeR;
	$Settings['EvDistance']=$DistanceR;
	CreateEventNew($TourId, 'RMO', 'Recurve Men Open Team', $i++, $Settings);
	CreateEventNew($TourId, 'RWO', 'Recurve Women Open Team', $i++, $Settings);
    if($SubRule=='2') {
        $Settings['EvIsPara']=0;
        CreateEventNew($TourId, 'RM', 'Recurve Men Team', $i++, $Settings);
        CreateEventNew($TourId, 'RW', 'Recurve Women Team', $i++, $Settings);
        $Settings['EvIsPara']=1;
    }
	if($Outdoor) {
		$Settings['EvMixedTeam']=1;
		$Settings['EvElimArrows']=4;
		$Settings['EvElimSO']=2;
		$Settings['EvFinArrows']=4;
		$Settings['EvFinSO']=2;
		CreateEventNew($TourId, 'RXO', 'Recurve Open Mixed Team', $i++, $Settings);
        if($SubRule=='2') {
            $Settings['EvIsPara']=0;
            CreateEventNew($TourId, 'RX', 'Recurve Mixed Team', $i++, $Settings);
            $Settings['EvIsPara']=1;
        }
        $Settings['EvMixedTeam']=0;
		$Settings['EvElimArrows']=6;
		$Settings['EvElimSO']=3;
		$Settings['EvFinArrows']=6;
		$Settings['EvFinSO']=3;
	}
	$Settings['EvFinalTargetType']=$TargetW1;
	$Settings['EvTargetSize']=$TargetSizeC;
	$Settings['EvDistance']=$DistanceC;
	CreateEventNew($TourId, 'MW1', 'Men W1 Open (Rec/Comp) Team', $i++, $Settings);
	CreateEventNew($TourId, 'WW1', 'Women W1 Open (Rec/Comp) Team', $i++, $Settings);
	if($Outdoor) {
		$Settings['EvMixedTeam']=1;
		$Settings['EvElimArrows']=4;
		$Settings['EvElimSO']=2;
		$Settings['EvFinArrows']=4;
		$Settings['EvFinSO']=2;
		CreateEventNew($TourId, 'W1X', 'W1 Open (Rec/Comp) Mixed Team', $i++, $Settings);
		$Settings['EvMixedTeam']=0;
		$Settings['EvElimArrows']=6;
		$Settings['EvElimSO']=3;
		$Settings['EvFinArrows']=6;
		$Settings['EvFinSO']=3;
	}
	$Settings['EvFinalTargetType']=$TargetC;
	CreateEventNew($TourId, 'CMO', 'Compound Men Open Team', $i++, $Settings);
	CreateEventNew($TourId, 'CWO', 'Compound Women Open Team', $i++, $Settings);
    if($SubRule=='2') {
        $Settings['EvIsPara']=0;
        CreateEventNew($TourId, 'CM', 'Compound Men Team', $i++, $Settings);
        CreateEventNew($TourId, 'CW', 'Compound Women Team', $i++, $Settings);
        $Settings['EvIsPara']=1;
    }
	if($Outdoor) {
		$Settings['EvMixedTeam']=1;
		$Settings['EvElimArrows']=4;
		$Settings['EvElimSO']=2;
		$Settings['EvFinArrows']=4;
		$Settings['EvFinSO']=2;
		CreateEventNew($TourId, 'CXO', 'Compound Open Mixed Team', $i++, $Settings);
        if($SubRule=='2') {
            $Settings['EvIsPara']=0;
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $Settings);
            $Settings['EvIsPara']=1;
        }
		$Settings['EvMixedTeam']=0;
		$Settings['EvElimArrows']=6;
		$Settings['EvElimSO']=3;
		$Settings['EvFinArrows']=6;
		$Settings['EvFinSO']=3;
	}
}

function InsertStandardEvents($TourId, $SubRule, $Outdoor=true) {
    if($SubRule=='1') {
	    InsertClassEvent($TourId, 0, 1, 'RMO', 'R', 'M');
	    InsertClassEvent($TourId, 0, 1, 'RWO', 'R', 'W');
        InsertClassEvent($TourId, 0, 1, 'CMO', 'C', 'M');
        InsertClassEvent($TourId, 0, 1, 'CWO', 'C', 'W');
	} else if($SubRule=='2') {
        InsertClassEvent($TourId, 0, 1, 'RMO', 'RO', 'M');
        InsertClassEvent($TourId, 0, 1, 'RWO', 'RO', 'W');
        InsertClassEvent($TourId, 0, 1, 'CMO', 'CO', 'M');
        InsertClassEvent($TourId, 0, 1, 'CWO', 'CO', 'W');
        InsertClassEvent($TourId, 0, 1, 'RM', 'R', 'M');
        InsertClassEvent($TourId, 0, 1, 'RW', 'R', 'W');
        InsertClassEvent($TourId, 0, 1, 'CM', 'C', 'M');
        InsertClassEvent($TourId, 0, 1, 'CW', 'C', 'W');
    }
	InsertClassEvent($TourId, 0, 1, 'MW1', 'W1', 'M');
	InsertClassEvent($TourId, 0, 1, 'WW1', 'W1', 'W');
	InsertClassEvent($TourId, 0, 1, 'VI1', 'VI', '1');
	InsertClassEvent($TourId, 0, 1, 'VI23', 'VI', '23');
    if($SubRule=='1') {
        InsertClassEvent($TourId, 1, 3, 'RMO', 'R', 'M');
        InsertClassEvent($TourId, 1, 3, 'RWO', 'R', 'W');
        InsertClassEvent($TourId, 1, 3, 'CMO', 'C', 'M');
        InsertClassEvent($TourId, 1, 3, 'CWO', 'C', 'W');
    } elseif($SubRule=='2') {
        InsertClassEvent($TourId, 1, 3, 'RMO', 'RO', 'M');
        InsertClassEvent($TourId, 1, 3, 'RWO', 'RO', 'W');
        InsertClassEvent($TourId, 1, 3, 'CMO', 'CO', 'M');
        InsertClassEvent($TourId, 1, 3, 'CWO', 'CO', 'W');
        InsertClassEvent($TourId, 1, 3, 'RM', 'R', 'M');
        InsertClassEvent($TourId, 1, 3, 'RW', 'R', 'W');
        InsertClassEvent($TourId, 1, 3, 'CM', 'C', 'M');
        InsertClassEvent($TourId, 1, 3, 'CW', 'C', 'W');
    }
	InsertClassEvent($TourId, 1, 3, 'MW1', 'W1', 'M');
	InsertClassEvent($TourId, 1, 3, 'WW1', 'W1', 'W');
	if($Outdoor) {
        if($SubRule=='1') {
            InsertClassEvent($TourId, 1, 1, 'RXO', 'R', 'W');
            InsertClassEvent($TourId, 2, 1, 'RXO', 'R', 'M');
            InsertClassEvent($TourId, 1, 1, 'CXO', 'C', 'W');
            InsertClassEvent($TourId, 2, 1, 'CXO', 'C', 'M');
        } elseif($SubRule=='2') {
            InsertClassEvent($TourId, 1, 1, 'RX', 'R', 'W');
            InsertClassEvent($TourId, 2, 1, 'RX', 'R', 'M');
            InsertClassEvent($TourId, 1, 1, 'CX', 'C', 'W');
            InsertClassEvent($TourId, 2, 1, 'CX', 'C', 'M');
            InsertClassEvent($TourId, 1, 1, 'RXO', 'RO', 'M');
            InsertClassEvent($TourId, 2, 1, 'RXO', 'RO', 'W');
            InsertClassEvent($TourId, 1, 1, 'CXO', 'CO', 'W');
            InsertClassEvent($TourId, 2, 1, 'CXO', 'CO', 'M');
        }
		InsertClassEvent($TourId, 1, 1, 'W1X', 'W1', 'W');
		InsertClassEvent($TourId, 2, 1, 'W1X', 'W1', 'M');
	}
}

