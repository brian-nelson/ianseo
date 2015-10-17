<?php
	require_once(dirname(__FILE__) . '/config.php');
	require_once('Common/Lib/Obj_RankFactory.php');
	require_once('Common/Lib/Fun_Phases.inc.php');

	list($Event,$EventType,$MatchNo) = explode("|",(!empty($_GET['matchid']) ? $_GET['matchid'] : "0|0|0"));
	$EventType=($EventType=='T' ? 1 : 0);
	$Phase=0;

	//Get the phase relatedto the matchno
	$SQL="select GrPhase from Grids where GrMatchNo=$MatchNo";
	$Rs=safe_r_sql($SQL);
	if($r=safe_fetch($Rs))
		$Phase = $r->GrPhase;

	$JsonResult=array();

	$options['tournament']=$CompId;
	$options['events']=array();
	$options['events'][] =  $Event . '@' . $Phase;

	$rank=null;
	if($EventType)
		$rank=Obj_RankFactory::create('GridTeam',$options);
	else
		$rank=Obj_RankFactory::create('GridInd',$options);

	$rank->read();
	$Data=$rank->getData();
	//debug_svela($Data);
	foreach($Data['sections'] as $kSec=>$vSec) {
		$json_array=array();
		$json_array['matchtype'] = ($vSec['meta']['matchMode'] ? "S":"C");
		$json_array['matchover'] = false;
		foreach($vSec['phases'] as $kPh=>$vPh) {
			$objParam=getEventArrowsParams($Event,$kPh,$EventType,$CompId);
			foreach($vPh['items'] as $kItem=>$vItem) {
				if($vItem['matchNo']!=$MatchNo && $vItem['oppMatchNo']!=$MatchNo)
					continue;

				$json_array['matchover'] = ($vItem['winner'] or $vItem['oppWinner']);
				$end = array();
				$oppEnd = array();
				if($vSec['meta']['matchMode']) {
					$tmp0 = explode("|",$vItem['setPoints']);
					$tmp1 = explode("|",$vItem['oppSetPoints']);
					for($i=0; $i<$objParam->ends; $i++){
						if($tmp0[$i] || $tmp1[$i]) {
							$end[]=array('endnum'=>$i+1, 'endscore'=>$tmp0[$i], 'points'=>($tmp0[$i]>$tmp1[$i] ? 2 : ($tmp0[$i]==$tmp1[$i] ? 1 : 0)));
							$oppEnd[]=array('endnum'=>$i+1, 'endscore'=>$tmp1[$i], 'points'=>($tmp1[$i]>$tmp0[$i] ? 2 : ($tmp0[$i]==$tmp1[$i] ? 1 : 0)));
						}
					}
					if($vItem['tiebreakDecoded'] && $vItem['oppTiebreakDecoded']) {
						$end[]=array('endnum'=>'S.O.', 'endscore'=>$vItem['tiebreakDecoded'], 'points'=>($vItem['tie'] ? 1:0));
						$oppEnd[]=array('endnum'=>'S.O.', 'endscore'=>$vItem['oppTiebreakDecoded'], 'points'=>($vItem['oppTie'] ? 1:0));
					}
				} else {
					$running=array(0,0);
					for($i=0; $i<$objParam->ends; $i++){
						$tmp=array(ValutaArrowString(substr($vItem['arrowstring'],$i*$objParam->arrows, $objParam->arrows)), ValutaArrowString(substr($vItem['oppArrowstring'],$i*$objParam->arrows, $objParam->arrows)));
						$running[0]+=$tmp[0];
						$running[1]+=$tmp[1];
						if($tmp[0] || $tmp[1]) {
							$end[]=array('endnum'=>$i+1, 'endscore'=>$tmp[0], 'points'=>$running[0]);
							$oppEnd[]=array('endnum'=>$i+1, 'endscore'=>$tmp[1], 'points'=>$running[1]);
						}
					}
					if($vItem['tiebreakDecoded'] && $vItem['oppTiebreakDecoded']) {
						$end[]=array('endnum'=>'S.O.', 'endscore'=>$vItem['tiebreakDecoded'], 'points'=>$running[0]);
						$oppEnd[]=array('endnum'=>'S.O.', 'endscore'=>$vItem['oppTiebreakDecoded'], 'points'=>$running[1]);
					}
				}

				$json_array['competitors'] = Array();
				$json_array['competitors'][] = Array('winner'=>(int)$vItem['winner'], 'matchid'=>$kSec . "|" . ($EventType ? "T" : "I") . "|" . $vItem['matchNo'], 'score'=>($vSec['meta']['matchMode'] ? $vItem['setScore'] : $vItem['score']), 'ends'=>$end);
				$json_array['competitors'][] = Array('winner'=>(int)$vItem['oppWinner'], 'matchid'=>$kSec . "|" . ($EventType ? "T" : "I") . "|" . $vItem['oppMatchNo'], 'score'=>($vSec['meta']['matchMode'] ? $vItem['oppSetScore'] : $vItem['oppScore']), 'ends'=>$oppEnd);
				$JsonResult[] = $json_array;
			}
		}
	}
	// Return the json structure with the callback function that is needed by the app
	SendResult($JsonResult);

