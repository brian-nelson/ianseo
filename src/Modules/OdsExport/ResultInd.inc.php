<?php
// get the ranks
$options=array();

$rank=Obj_RankFactory::create('FinalInd',$options);
$rank->read();
$rankData=$rank->getData();

// for each Event
foreach($rankData['sections'] as $Event) {
	$EvCode=$Event['meta']['event'].' I';
	$excel->setActiveSheet($EvCode);
//	$page=array();

	$Rounds=array('Rk','Archer','NOC','Fita ID','R/','RRScore','','RRRank','1=64/48','1=32/24','1=16','1=8','1=4','1=2','Final','SO');

	$excel->addRow($Rounds);//, 'header');



//	$page[]=implode("\t", $Rounds);

	foreach($Event['items'] as $Item) {

		$cols = array(
			$Item['rank'],
			$Item['familyname'] . ' ' . $Item['givenname'],
			$Item['countryCode'],
			$Item['bib'],
			'',
			$Item['qualScore'],
			'',
			$Item['qualRank'],
			);
		foreach(array(64,32,16,8,4,2,1,0) as $Phase) {
			if(empty($Item['finals'][$Phase])) {
				// no phase at all
				if($Phase>1) $cols[]='';
				continue;
			} elseif($Item['preseed'] and $Phase>=32) {
				$cols[]='preseed';
			} elseif($Item['finals'][$Phase]['tie']==2) {
				$cols[]='bye';
			} else {
				$cols[]=$Item['finals'][$Phase][($Event['meta']['matchMode'] ? 'setScore' : 'score')];
				if($Phase<2 and $Item['finals'][$Phase]['tiebreak']) $cols[]=$Item['finals'][$Phase]['tiebreak'];
			}
		}

		$excel->addRow($cols);
//		$page[]=implode("\t", $cols);
	}

//	$TXT[$EvCode] = implode("\n",$page);
}
?>