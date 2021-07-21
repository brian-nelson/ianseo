<?php

require_once(dirname(dirname(__FILE__)).'/config.php');
checkACL(AclRoot, AclReadWrite);

require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/CommonLib.php');
require_once('Accreditation/Lib.php');

$JSON=array(
	'error' => 1,
	'html' => '',
	);

if(empty($_REQUEST['toid']) or !($ToId=intval($_REQUEST['toid']))) {
	JsonOut($JSON);
}

// get all the categories involved
$Cats=array();
$q=safe_r_sql("select distinct if(EnAthlete=1, concat(EnDivision, EnClass), EnDivision) as Category 
	from Entries
	LEFT join Divisions on DivId=EnDivision and DivTournament=$ToId
	left JOIN Classes on ClId=EnClass and ClTournament=$ToId
	where EnTournament=$ToId
	group by EnDivision, EnClass
	order by DivViewOrder, ClViewOrder");
while($r=safe_fetch($q)) {
	$Cats[$r->Category]=array(0, 0);
}


// get all people in competition divided by function
$q=safe_r_sql("select CoCode, CoName, EnAthlete, if(EnAthlete=1, concat(EnDivision, EnClass), EnDivision) as Category, count(*) as Total, count(HowMany) as TotalIn  from Entries
	inner join Countries on CoId=EnCountry and CoTournament=EnTournament
	LEFT join Divisions on DivId=EnDivision and DivTournament=$ToId
	left JOIN Classes on ClId=EnClass and ClTournament=$ToId
	left join (select sum(GLDirection) as HowMany, GLEntry from GateLog where GLTournament=$ToId group by GlEntry having HowMany!=0) access on GlEntry=EnId
	where EnTournament=$ToId
	group by EnCountry, EnDivision, EnClass
	order by CoCode, DivViewOrder, ClViewOrder");

$Situation=array();
$Presence=array();
while($r=safe_fetch($q)) {
	$Situation[$r->CoCode.' - '.$r->CoName][$r->Category]=array($r->Total, $r->TotalIn);
	$Cats[$r->Category][0]+=$r->Total;
	$Cats[$r->Category][1]+=$r->TotalIn;
}

$JSON['html']='<table>';
$JSON['html'].='<tr>';
$JSON['html'].='<th>'.get_text('CountryCode').'</th>';
foreach($Cats as $Cat => $num) {
	if($num[0]) {
		$JSON['html'].='<th colspan="2">'.$Cat.'</th>';
	} else {
		unset($Cats[$Cat]);
	}
}
$JSON['html'].='</tr>';

foreach($Situation as $CoCode => $Categories) {
	$JSON['html'].='<tr>';

	$JSON['html'].='<td><b>'.$CoCode.'</b></td>';
	foreach($Cats as $CatCode => $num) {
		if(!empty($Categories[$CatCode])) {
			$JSON['html'].='<td>';
			for($i=0; $i<$Categories[$CatCode][1];$i++) {
				$JSON['html'].='<img src="'.$CFG->ROOT_DIR.'Common/Images/gate-in.png">';
			}
			for($i=$Categories[$CatCode][1]; $i<$Categories[$CatCode][0];$i++) {
				$JSON['html'].='<img src="'.$CFG->ROOT_DIR.'Common/Images/gate-out.png">';
			}
			$JSON['html'].='</td>';

			$JSON['html'].='<td>';
			$JSON['html'].=$Categories[$CatCode][1].'/'.$Categories[$CatCode][0];
			$JSON['html'].='</td>';
		} else {
			$JSON['html'].='<td colspan="2">';
			$JSON['html'].='</td>';
		}

	}
	$JSON['html'].='</tr>';
}
$JSON['html'].='</table>';

$JSON['error']=0;

JsonOut($JSON);