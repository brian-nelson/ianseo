<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = '';
if(empty($SubRule)) $SubRule='1';

global $ArlefDivisions, $ArlefClasses;

$ArlefDivisions=array(
	'R' => 'Recurve',
	'C' => 'Compound',
	'NL'=> 'Nu Longbow',
	'NR'=> 'Nu Recurve/Barebow',
	);

$ArlefClasses=array(
	'R' => array(
		'HB',
		'DB',
		'HC',
		'DC',
		'HJ',
		'DJ',
		'H1',
		'D1',
		'H2',
		'D2',
		'HM',
		'DM',
		'HV',
		'DV',
		),
	'C' => array(
		'HB',
		'DB',
		'HC',
		'DC',
		'HJ',
		'DJ',
		'H1',
		'D1',
		'H2',
		'D2',
		'HM',
		'DM',
		'HV',
		'DV',
		),
	'NL' => array(
		'J',
		'S',
		'M',
		),
	'NR' => array(
		'J',
		'S',
		'M',
		),
);


function CreateStandardDivisions($TourId, $Type, $SubRule=1) {
	global $ArlefDivisions, $ArlefClasses;
	$i=1;
	switch($Type) {
		case '1':
		case '3':
			CreateDivision($TourId, $i++, 'R', 'Recurve',1,'R','R');
			CreateDivision($TourId, $i++, 'C', 'Compound',1,'C','C');
			CreateDivision($TourId, $i++, 'CO', 'Compound Open',1,'C','C',1);
			CreateDivision($TourId, $i++, 'C1', 'Compound W1',1,'W1','W1',1);
			CreateDivision($TourId, $i++, 'RS', 'Recurve Standing',1,'R','R',1);
			CreateDivision($TourId, $i++, 'R2', 'Recurve W2',1,'W2','W2',1);
			CreateDivision($TourId, $i++, 'VI', 'Visually Impaired',1,'VI','VI',1);
			$ArlefDivisions=array(
				'R' => 'Recurve',
				'C' => 'Compound',
				);
			break;
		case '6':
		case '7':
			CreateDivision($TourId, $i++, 'R', 'Recurve',1,'R','R');
			CreateDivision($TourId, $i++, 'C', 'Compound',1,'C','C');
			if($SubRule==1) {
				CreateDivision($TourId, $i++, 'NL', 'Nu Longbow',1,'L','L');
				CreateDivision($TourId, $i++, 'NR', 'Nu Recurve/Barebow',1,'B','B');
				CreateDivision($TourId, $i++, 'BR', 'Brevet Recurve');
				CreateDivision($TourId, $i++, 'BC', 'Brevet Compound');
				CreateDivision($TourId, $i++, 'BN', 'Brevet Nu Longbow');
				CreateDivision($TourId, $i++, 'BB', 'Brevet Recurve/Barebow');
				CreateDivision($TourId, $i++, 'B', 'Brevet 10m');
				CreateDivision($TourId, $i++, 'CO', 'Compound Open',1,'C','C',1);
				CreateDivision($TourId, $i++, 'C1', 'Compound W1',1,'W1','W1',1);
				CreateDivision($TourId, $i++, 'RS', 'Recurve Standing',1,'R','R',1);
				CreateDivision($TourId, $i++, 'R2', 'Recurve W2',1,'W2','W2',1);
				CreateDivision($TourId, $i++, 'VI', 'Visually Impaired',1,'VI','VI',1);
			}
			break;
		case '23':
		case '24':
		case '25':
			CreateDivision($TourId, $i++, 'R', 'Recurve',1,'R','R');
			CreateDivision($TourId, $i++, 'C', 'Compound',1,'C','C');
			CreateDivision($TourId, $i++, 'NL', 'Nu Longbow',1,'L','L');
			CreateDivision($TourId, $i++, 'NR', 'Nu Recurve/Barebow',1,'B','B');
			CreateDivision($TourId, $i++, 'CO', 'Compound Open',1,'C','C',1);
			CreateDivision($TourId, $i++, 'C1', 'Compound W1',1,'W1','W1',1);
			CreateDivision($TourId, $i++, 'RS', 'Recurve Standing',1,'R','R',1);
			CreateDivision($TourId, $i++, 'R2', 'Recurve W2',1,'W2','W2',1);
			CreateDivision($TourId, $i++, 'VI', 'Visually Impaired',1,'VI','VI',1);
			break;
		case '26': // Brevet 10m outdoor
			CreateDivision($TourId, $i++, 'BR', 'Brevet 10m Recurve');
			CreateDivision($TourId, $i++, 'BC', 'Brevet 10m Compound');
			CreateDivision($TourId, $i++, 'BN', 'Brevet 10m Nu');
			break;
		case '27': // Brevet 15m outdoor
			CreateDivision($TourId, $i++, 'RB', 'Brevet 15m Recurve');
			CreateDivision($TourId, $i++, 'CB', 'Brevet 15m Compound');
			CreateDivision($TourId, $i++, 'NB', 'Brevet 15m Nu');
			break;
		case '28': // Brevet 25m outdoor
		case '29': // Brevet 30/20 outdoor
			CreateDivision($TourId, $i++, 'RB', 'Brevet Recurve');
			CreateDivision($TourId, $i++, 'CB', 'Brevet Compound');
			CreateDivision($TourId, $i++, 'NB', 'Brevet Nu Barebow');
			CreateDivision($TourId, $i++, 'LB', 'Brevet Longbow');
			break;
		case '30': // Brevet FITA outdoor
			CreateDivision($TourId, $i++, 'RB', 'Brevet Recurve');
			CreateDivision($TourId, $i++, 'CB', 'Brevet Compound');
			break;
	}
}

function CreateStandardClasses($TourId, $Type, $SubRule) {
	global $ArlefDivisions, $ArlefClasses;
	$i=1;
	switch($Type) {
		case '1':
			CreateClass($TourId, $i++,  1, 14, -1, 'MB', 'MB', 'Benjamin', '1', 'C,R');
			CreateClass($TourId, $i++, 15, 17,  0, 'HC', 'HC', 'Homme Cadet', '1', 'C,R');
			CreateClass($TourId, $i++, 15, 17,  1, 'DC', 'DC', 'Dame Cadet', '1', 'C,R');
			CreateClass($TourId, $i++, 18, 20,  0, 'HJ', 'HJ', 'Homme Junior', '1', 'C,R');
			CreateClass($TourId, $i++, 18, 20,  1, 'DJ', 'DJ', 'Dame Junior', '1', 'C,R');
			CreateClass($TourId, $i++, 21, 34,  0, 'H1', 'H1', 'Homme Senior 1', '1', 'C,R');
			CreateClass($TourId, $i++, 21, 34,  1, 'D1', 'D1', 'Dame Senior 1', '1', 'C,R');
			CreateClass($TourId, $i++, 35, 49,  0, 'H2', 'H2', 'Homme Senior 2', '1', 'C,R');
			CreateClass($TourId, $i++, 35, 49,  1, 'D2', 'D2', 'Dame Senior 2', '1', 'C,R');
			CreateClass($TourId, $i++, 50, 59,  0, 'HM', 'HM', 'Homme Master', '1', 'C,R');
			CreateClass($TourId, $i++, 50, 59,  1, 'DM', 'DM', 'Dame Master', '1', 'C,R');
			CreateClass($TourId, $i++, 60,100,  0, 'HV', 'HV', 'Homme Vétéran', '1', 'C,R');
			CreateClass($TourId, $i++, 60,100,  1, 'DV', 'DV', 'Dame Vétéran', '1', 'C,R');
			CreateClass($TourId, $i++, 1, 100,  0, 'H', 'H', 'Homme', '1', 'CO,C1,RS,R2,VI','','',1);
			CreateClass($TourId, $i++, 1, 100,  1, 'D', 'D', 'Dame', '1', 'CO,C1,RS,R2,VI','','',1);
			$ArlefClasses=array(
				'R' => array('MB','HC','DC','HJ','DJ','H1','D1','H2','D2','HM','DM','HV','DV'),
				'C' => array('MB','HC','DC','HJ','DJ','H1','D1','H2','D2','HM','DM','HV','DV'),
				);
			break;
		case '3':
			CreateClass($TourId, $i++,  1, 12, -1, 'MP', 'MP', 'Pupille', '1', 'C,R');
			CreateClass($TourId, $i++, 13, 14,  0, 'HB', 'HB', 'Homme Benjamin', '1', 'C,R');
			CreateClass($TourId, $i++, 13, 14,  1, 'DB', 'DB', 'Dame Benjamin', '1', 'C,R');
			CreateClass($TourId, $i++, 15, 17,  0, 'HC', 'HC', 'Homme Cadet', '1', 'C,R');
			CreateClass($TourId, $i++, 15, 17,  1, 'DC', 'DC', 'Dame Cadet', '1', 'C,R');
			CreateClass($TourId, $i++, 18, 20,  0, 'HJ', 'HJ', 'Homme Junior', '1', 'C,R');
			CreateClass($TourId, $i++, 18, 20,  1, 'DJ', 'DJ', 'Dame Junior', '1', 'C,R');
			CreateClass($TourId, $i++, 21, 34,  0, 'H1', 'H1', 'Homme Senior 1', '1', 'C,R');
			CreateClass($TourId, $i++, 21, 34,  1, 'D1', 'D1', 'Dame Senior 1', '1', 'C,R');
			CreateClass($TourId, $i++, 35, 49,  0, 'H2', 'H2', 'Homme Senior 2', '1', 'C,R');
			CreateClass($TourId, $i++, 35, 49,  1, 'D2', 'D2', 'Dame Senior 2', '1', 'C,R');
			CreateClass($TourId, $i++, 50, 59,  0, 'HM', 'HM', 'Homme Master', '1', 'C,R');
			CreateClass($TourId, $i++, 50, 59,  1, 'DM', 'DM', 'Dame Master', '1', 'C,R');
			CreateClass($TourId, $i++, 60,100,  0, 'HV', 'HV', 'Homme Vétéran', '1', 'C,R');
			CreateClass($TourId, $i++, 60,100,  1, 'DV', 'DV', 'Dame Vétéran', '1', 'C,R');
			CreateClass($TourId, $i++, 1, 100,  0, 'H', 'H', 'Homme', '1', 'CO,C1,RS,R2,VI','','',1);
			CreateClass($TourId, $i++, 1, 100,  1, 'D', 'D', 'Dame', '1', 'CO,C1,RS,R2,VI','','',1);
			$ArlefClasses=array(
				'R' => array('MP','HB','DB','HC','DC','HJ','DJ','H1','D1','H2','D2','HM','DM','HV','DV'),
				'C' => array('MP','HB','DB','HC','DC','HJ','DJ','H1','D1','H2','D2','HM','DM','HV','DV'),
				);
			break;
		case '6':
		case '7':
			if($SubRule==1) {
				CreateClass($TourId, $i++,  1,  6, -1, 'MP', 'MP', 'Pupille', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++,  7, 14, 0, 'HB', 'HB', 'Homme Benjamin', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++,  7, 14, 1, 'DB', 'DB', 'Dame Benjamin', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++, 15, 17, 0, 'HC', 'HC', 'Homme Cadet', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++, 15, 17, 1, 'DC', 'DC', 'Dame Cadet', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++, 18, 20, 0, 'HJ', 'HJ', 'Homme Junior', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++, 18, 20, 1, 'DJ', 'DJ', 'Dame Junior', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++, 21, 34, 0, 'H1', 'H1', 'Homme Senior 1', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++, 21, 34, 1, 'D1', 'D1', 'Dame Senior 1', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++, 35, 49, 0, 'H2', 'H2', 'Homme Senior 2', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++, 35, 49, 1, 'D2', 'D2', 'Dame Senior 2', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++, 50, 59, 0, 'HM', 'HM', 'Homme Master', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++, 50, 59, 1, 'DM', 'DM', 'Dame Master', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++, 60, 100, 0, 'HV', 'HV', 'Homme Vétéran', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++, 60, 100, 1, 'DV', 'DV', 'Dame Vétéran', '1', 'BC,BR,C,R');
				CreateClass($TourId, $i++,  1, 17, -1, 'J', 'J', 'Jeune', '1', 'BB,BN,NL,NR');
				CreateClass($TourId, $i++, 18, 49, -1, 'S', 'S', 'Senior', '1', 'BB,BN,NL,NR');
				CreateClass($TourId, $i++, 50, 100, -1, 'M', 'M', 'Master', '1', 'BB,BN,NL,NR');
				CreateClass($TourId, $i++, 1, 100, -1, 'A', 'A', 'Apache', '1', 'B');
				CreateClass($TourId, $i++, 1, 100, -1, 'V', 'V', 'Viseur', '1', 'B');
				CreateClass($TourId, $i++, 1, 100, 0, 'H', 'H', 'Homme', '1', 'CO,C1,RS,R2,VI','','',1);
				CreateClass($TourId, $i++, 1, 100, 1, 'D', 'D', 'Dame', '1', 'CO,C1,RS,R2,VI','','',1);
				$ArlefClasses['R'][]='MP';
				$ArlefClasses['C'][]='MP';
			} else {
				CreateClass($TourId, $i++,  1, 14, -1, 'J', 'J', 'Jeune', '1', 'C,R');
				CreateClass($TourId, $i++, 14,100, -1, 'S', 'S', 'Senior', '1', 'C,R');
			}
			break;
		case '23':
		case '24':
		case '25':
			if($Type==25) {
				CreateClass($TourId, $i++,  1, 12, -1, 'MP', 'MP', 'Pupille', '1', 'C,R');
				CreateClass($TourId, $i++,  13, 14, -1, 'MB', 'MB', 'Benjamin', '1', 'C,R');
			} else {
				CreateClass($TourId, $i++,  1, 14, -1, 'MB', 'MB', 'Benjamin', '1', 'C,R');
			}
			CreateClass($TourId, $i++, 15, 17, 0, 'HC', 'HC', 'Homme Cadet', '1', 'C,R');
			CreateClass($TourId, $i++, 15, 17, 1, 'DC', 'DC', 'Dame Cadet', '1', 'C,R');
			CreateClass($TourId, $i++, 18, 20, 0, 'HJ', 'HJ', 'Homme Junior', '1', 'C,R');
			CreateClass($TourId, $i++, 18, 20, 1, 'DJ', 'DJ', 'Dame Junior', '1', 'C,R');
			CreateClass($TourId, $i++, 21, 34, 0, 'H1', 'H1', 'Homme Senior 1', '1', 'C,R');
			CreateClass($TourId, $i++, 21, 34, 1, 'D1', 'D1', 'Dame Senior 1', '1', 'C,R');
			CreateClass($TourId, $i++, 35, 49, 0, 'H2', 'H2', 'Homme Senior 2', '1', 'C,R');
			CreateClass($TourId, $i++, 35, 49, 1, 'D2', 'D2', 'Dame Senior 2', '1', 'C,R');
			CreateClass($TourId, $i++, 50, 59, 0, 'HM', 'HM,H2', 'Homme Master', '1', 'C,R');
			CreateClass($TourId, $i++, 50, 59, 1, 'DM', 'DM,D2', 'Dame Master', '1', 'C,R');
			CreateClass($TourId, $i++, 60, 100, 0, 'HV', 'HV,HM,H2', 'Homme Vétéran', '1', 'C,R');
			CreateClass($TourId, $i++, 60, 100, 1, 'DV', 'DV,DM,D2', 'Dame Vétéran', '1', 'C,R');
			CreateClass($TourId, $i++, 1, 100, 0, 'H', 'H', 'Homme', '1', 'CO,C1,RS,R2,VI','','',1);
			CreateClass($TourId, $i++, 1, 100, 1, 'D', 'D', 'Dame', '1', 'CO,C1,RS,R2,VI','','',1);
			CreateClass($TourId, $i++,  1, 17, -1, 'J', 'J', 'Jeune', '1', 'NL,NR');
			CreateClass($TourId, $i++, 18, 49, -1, 'S', 'S', 'Senior', '1', 'NL,NR');
			CreateClass($TourId, $i++, 50, 100, -1, 'M', 'M,S', 'Master', '1', 'NL,NR');
			$ArlefClasses=array(
				'R' => array('MB','HC','DC','HJ','DJ','H1','D1','H2','D2','HM','DM','HV','DV'),
				'C' => array('MB','HC','DC','HJ','DJ','H1','D1','H2','D2','HM','DM','HV','DV'),
				'NL' => array('J','S','M'),
				'NR' => array('J','S','M'),
				);
			break;
		case '26':
			CreateClass($TourId, $i++,  1, 17, -1, 'A', 'A', 'Apache');
			CreateClass($TourId, $i++,  1, 17, -1, 'V', 'V', 'Viseur');
		case '27':
			CreateClass($TourId, $i++,  1,  6, -1, 'P', 'P', 'Pupille', '1', 'RB,CB');
			CreateClass($TourId, $i++,  7, 14, -1, 'B', 'B', 'Benjamin', '1', 'RB,CB');
			CreateClass($TourId, $i++, 15, 17, -1, 'C', 'C', 'Cadet', '1', 'RB,CB');
			CreateClass($TourId, $i++, 18, 20, -1, 'J', 'J', 'Junior', '1', 'RB,CB');
			CreateClass($TourId, $i++, 21, 34, -1, 'S', 'S', 'Senior', '1', 'RB,CB');
			CreateClass($TourId, $i++, 50, 59, -1, 'M', 'M', 'Master', '1', 'RB,CB');
			CreateClass($TourId, $i++, 60,100, -1, 'V', 'V', 'Vétéran', '1', 'RB,CB');
			CreateClass($TourId, $i++,  1, 17, -1, 'Jn', 'Jn', 'Jeune', '1', 'NB');
			CreateClass($TourId, $i++, 18, 49, -1, 'Sn', 'Sn', 'Senior', '1', 'NB');
			CreateClass($TourId, $i++, 50,100, -1, 'Mn', 'Mn', 'Master', '1', 'NB');
			break;
		case '28':
		case '29':
			CreateClass($TourId, $i++,  1,  6, -1, 'P', 'P', 'Pupille', '1', 'RB,CB');
			CreateClass($TourId, $i++,  7, 14, -1, 'B', 'B', 'Benjamin', '1', 'RB,CB');
			CreateClass($TourId, $i++, 15, 17, -1, 'C', 'C', 'Cadet', '1', 'RB,CB');
			CreateClass($TourId, $i++, 18, 20, -1, 'J', 'J', 'Junior', '1', 'RB,CB');
			CreateClass($TourId, $i++, 21, 34, -1, 'S', 'S', 'Senior', '1', 'RB,CB');
			CreateClass($TourId, $i++, 50, 59, -1, 'M', 'M', 'Master', '1', 'RB,CB');
			CreateClass($TourId, $i++, 60,100, -1, 'V', 'V', 'Vétéran', '1', 'RB,CB');
			CreateClass($TourId, $i++,  1, 17, -1, 'Jn', 'Jn', 'Jeune', '1', 'NB,LB');
			CreateClass($TourId, $i++, 18, 49, -1, 'Sn', 'Sn', 'Senior', '1', 'NB,LB');
			CreateClass($TourId, $i++, 50,100, -1, 'Mn', 'Mn', 'Master', '1', 'NB,LB');
			break;
		case '30':
			CreateClass($TourId, $i++,  1, 17, -1, 'C', 'C', 'Cadet', '1', 'RB,CB');
			CreateClass($TourId, $i++, 18, 20, -1, 'J', 'J', 'Junior', '1', 'RB,CB');
			CreateClass($TourId, $i++, 21, 34, -1, 'S', 'S', 'Senior', '1', 'RB,CB');
			CreateClass($TourId, $i++, 50, 59, -1, 'M', 'M', 'Master', '1', 'RB,CB');
			CreateClass($TourId, $i++, 60,100, -1, 'V', 'V', 'Vétéran', '1', 'RB,CB');
			break;
	}
}

function CreateStandardEvents($TourId, $Type) {
	global $ArlefDivisions;
	$i=1;
	switch($Type) {
		case 1:
		case 3:
		case 6:
		case 7:
		case 23:
		case 24:
		case 25:
			foreach($ArlefDivisions as $k => $v) {
				CreateEvent($TourId, $i++, 1, 0, 0, 1, 4, 6, 3, 4, 6, 3, $k.'T',  'Equipe '.$v);
			}
			break;
	}
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	global $ArlefClasses;
	foreach($ArlefClasses as $cD => $D) {
		foreach($D as $cC ) {
			InsertClassEvent($TourId, 1, 3, $cD.'T', $cD, $cC);
		}
	}
	InsertClassEvent($TourId, 1, 3, 'CT', 'CO', 'H');
	InsertClassEvent($TourId, 1, 3, 'CT', 'CO', 'D');
	InsertClassEvent($TourId, 1, 3, 'CT', 'C1', 'H');
	InsertClassEvent($TourId, 1, 3, 'CT', 'C1', 'D');
	InsertClassEvent($TourId, 1, 3, 'RT', 'RS', 'H');
	InsertClassEvent($TourId, 1, 3, 'RT', 'RS', 'D');
	InsertClassEvent($TourId, 1, 3, 'RT', 'R2', 'H');
	InsertClassEvent($TourId, 1, 3, 'RT', 'R2', 'D');
}

/*

FIELD DEFINITIONS (Target Tournaments)

*/

require_once(dirname(dirname(__FILE__)).'/FITA/lib-Field.php');

/*

3D DEFINITIONS (Target Tournaments)

*/

require_once(dirname(dirname(__FILE__)).'/FITA/lib-3D.php');

