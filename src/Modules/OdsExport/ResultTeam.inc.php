<?php
// get the ranks
$options=array();

$rank=Obj_RankFactory::create('FinalTeam',$options);
$rank->read();
$rankData=$rank->getData();

// for each Event
foreach($rankData['sections'] as $Event) {
	$EvCode=$Event['meta']['event'].' T';
	$excel->setActiveSheet($EvCode);
//	$page=array();

	$Rounds=array('Rk','NOC','Name','Name FamilyName','FitaID','Score','Ranking','1=8','1=4','1=2','Final','SO');

	$excel->addRow($Rounds);//, 'header');
//	$page[]=implode("\t", $Rounds);

	foreach($Event['items'] as $ItemKey => $Item) {
		$cols=array(
			$Item['rank'],
			$Item['countryCode'],
			$Item['countryName'],
			$Item['athletes'][0]['familyname'] . ' ' . $Item['athletes'][0]['givenname'],
			$Item['athletes'][0]['bib'],
			$Item['qualScore'],
			$Item['qualRank'],
			);

		foreach(array(8,4,2,1,0) as $Phase) {
			if(empty($Item['finals'][$Phase])) {
				// no phase at all
				if($Phase>1) $cols[]='';
				continue;
			} elseif($Item['finals'][$Phase]['tie']==2) {
				$cols[]='bye';
			} else {
				$cols[]=$Item['finals'][$Phase][($Event['meta']['matchMode'] ? 'setScore' : 'score')];
				if($Phase<2 and $Item['finals'][$Phase]['tiebreak']) $cols[]=$Item['finals'][$Phase]['tiebreak'];
			}
		}

		$excel->addRow($cols);
//		$page[]=implode("\t", $cols);

		for($n=1; $n<count($Item['athletes']); $n++) {
			$cols=array(
			'',
			'',
			'',
			$Item['athletes'][$n]['familyname'] . ' ' . $Item['athletes'][$n]['givenname'],
			$Item['athletes'][$n]['bib'],
			);
			$excel->addRow($cols);
//			$page[]=implode("\t", $cols);
		}
	}
//	$TXT[$EvCode] = implode("\n",$page);
}
?>