<?php
	require_once(dirname(__FILE__) . '/config.php');
	require_once('Common/Lib/Obj_RankFactory.php');
	require_once('Common/Lib/Fun_Phases.inc.php');

	list($Event,$EventTypeLetter,$MatchNo) = explode("|",(!empty($_GET['matchid']) ? $_GET['matchid'] : "0|0|0"));
	$EventType=($EventTypeLetter=='T' ? 1 : 0);
	$Phase=getPhase($MatchNo);

	$JsonResult=array();

	$options['tournament']=$CompId;
	$options['events']=array();
	$options['matchno']=$MatchNo;
	$options['events'][] =  $Event . '@' . $Phase;

	$rank=null;
	if($EventType)
		$rank=Obj_RankFactory::create('GridTeam',$options);
	else
		$rank=Obj_RankFactory::create('GridInd',$options);

	$rank->read();
	$Data=$rank->getData();
	foreach($Data['sections'] as $kSec=>$vSec) {
		$json_array=array();
		$json_array['matchtype'] = ($vSec['meta']['matchMode'] ? "S":"C");
		$json_array['matchover'] = false;
		foreach($vSec['phases'] as $kPh=>$vPh) {
			$objParam=getEventArrowsParams($Event,$kPh,$EventType,$CompId);
			foreach($vPh['items'] as $kItem=>$vItem) {
				if($vItem['matchNo']!=$MatchNo && $vItem['oppMatchNo']!=$MatchNo)
					continue;

				// only for pro!
				if($iskModePro) {
					$firstTmpEnd = 0;
					$SQL = "SELECT *
						FROM IskData
						WHERE IskDtTournament={$CompId} AND IskDtMatchNo IN (".$vItem['matchNo'].",".$vItem['oppMatchNo'].") AND IskDtEvent='{$Event}' AND IskDtTeamInd={$EventType} AND IskDtType='{$EventTypeLetter}' AND IskDtTargetNo='' AND IskDtDistance=0
						ORDER BY IskDtEndNo ASC";
					$q=safe_r_SQL($SQL);
					while($r=safe_fetch($q)) {
						if($IsSO=($r->IskDtEndNo > $objParam->ends)) {
							// tie break
							$fld = ($r->IskDtMatchNo == $vItem['matchNo'] ? 'tiebreak' : 'oppTiebreak');
							$vItem[$fld]=str_repeat(' ', strlen($r->IskDtArrowstring));
							$idx = 0;
						} else {
							// normal scoring
							$fld = ($r->IskDtMatchNo == $vItem['matchNo'] ? 'arrowstring' : 'oppArrowstring');
							$idx = ($r->IskDtEndNo-1)*$objParam->arrows;
							$vItem[$fld]=str_pad($vItem[$fld], $idx+1, ' ', STR_PAD_RIGHT);
						}
						for($i=0; $i<strlen($r->IskDtArrowstring); $i++) {
							$vItem[$fld][$idx++] = $r->IskDtArrowstring[$i];
						}
					}
					if(strlen(trim($vItem['tiebreak']))==strlen(trim($vItem['oppTiebreak'])) and strlen(trim($vItem['tiebreak']))>0) {
						//we have a closest to center and a winner here!
						if($vItem['oppTiebreak']!=strtoupper($vItem['oppTiebreak'])) {
							$vItem['oppClosest']='1';
							$vItem['oppWinner']='1';
							$vItem['oppTiebreak']=strtoupper($vItem['oppTiebreak']);
						} elseif($vItem['tiebreak']!=strtoupper($vItem['tiebreak'])) {
							$vItem['closest']='1';
							$vItem['winner']='1';
							$vItem['tiebreak']=strtoupper($vItem['tiebreak']);
						}
					}
				}

				$end = array();
				$oppEnd = array();
				// rebuild points
				$chunks0=str_split(rtrim($vItem['arrowstring']), $objParam->arrows);
				$chunks1=str_split(rtrim($vItem['oppArrowstring']), $objParam->arrows);
				$Tot0=0;
				$Tot1=0;
				foreach($chunks0 as $End => $Arrows) {
					if(!$Arrows and !$chunks1[$End]) continue;
					$pts0=ValutaArrowString($Arrows);
					$pts1=ValutaArrowString($chunks1[$End]);

					if($vSec['meta']['matchMode']) {
						$endtot0 = ($pts0==$pts1 ? 1 : ($pts0>$pts1 ? 2 : 0));
						$endtot1 = ($pts0==$pts1 ? 1 : ($pts0<$pts1 ? 2 : 0));
                        $Tot0+=$endtot0;
                        $Tot1+=$endtot1;
					} else {
						$endtot0 += $pts0;
						$endtot1 += $pts1;
                        $Tot0+=$pts0;
                        $Tot1+=$pts1;
					}
					$end[]=array('endnum'=>$End+1, 'endscore'=>$pts0, 'points'=>$endtot0);
					$oppEnd[]=array('endnum'=>$End+1, 'endscore'=>$pts1, 'points'=>$endtot1);
				}

				// check tiebreak
				$SO=false;
				if(strlen(trim($vItem['tiebreak']))==strlen(trim($vItem['oppTiebreak'])) and strlen(trim($vItem['tiebreak']))>0) {
					$SO=true;
					// now we have a closest to centre but if the SO comes from the stored values of the device we need to hack the "*" into a closest!!
					$pts0=ValutaArrowString($vItem['tiebreak']);
					$pts1=ValutaArrowString($vItem['oppTiebreak']);

					if($vSec['meta']['matchMode']) {
						$endtot0=0;
						$endtot1=0;
						if($vItem['winner'] or $pts0>$pts1 or $vItem['closest']) {
							$endtot0=1;
							$Tot0+=1;
						}
						if($vItem['oppWinner'] or $pts0<$pts1 or $vItem['oppClosest']) {
							$endtot1=1;
							$Tot1+=1;
						}
					} else {
                        $endtot0 = '-';
                        $endtot1 = '-';
					}
					$end[]=array('endnum'=>'S.O.', 'endscore'=>$pts0, 'points'=>$endtot0);
					$oppEnd[]=array('endnum'=>'S.O.', 'endscore'=>$pts1, 'points'=>$endtot1);
				}

				// check winner if no winners and pro mode
				if($iskModePro and !$vItem['winner']
						and !$vItem['oppWinner']
						and ($objParam->EvMatchMode ? ($Tot0>$objParam->ends or $Tot1>$objParam->ends) : (strlen(rtrim($vItem['arrowstring']))==$objParam->ends*$objParam->arrows and strlen(rtrim($vItem['oppArrowstring']))==$objParam->ends*$objParam->arrows))) {
					if($objParam->EvMatchMode) {
						if($Tot0>$objParam->ends) {
							$vItem['winner']=1;
						} else {
							$vItem['oppWinner']=1;
						}
					} else {
						if($Tot0>$Tot1) {
							$vItem['winner']=1;
						} elseif($Tot0<$Tot1) {
							$vItem['oppWinner']=1;
						} elseif($SO) {
							if($pts0>$pts1) {
								$vItem['winner']=1;
							} elseif($pts0<$pts1) {
								$vItem['oppWinner']=1;
							} else {
								// check star?
							}

						}
					}
				}

				$json_array['matchover'] = ($vItem['winner'] or $vItem['oppWinner']);

				// sends summary
				$json_array['competitors'] = Array();
				$json_array['competitors'][] = Array('winner'=>(int)$vItem['winner'], 'matchid'=>$kSec . "|" . ($EventType ? "T" : "I") . "|" . $vItem['matchNo'], 'score'=>$Tot0, 'ends'=>$end);
				$json_array['competitors'][] = Array('winner'=>(int)$vItem['oppWinner'], 'matchid'=>$kSec . "|" . ($EventType ? "T" : "I") . "|" . $vItem['oppMatchNo'], 'score'=>$Tot1, 'ends'=>$oppEnd);

				$JsonResult[] = $json_array;
			}
		}
	}
	// Return the json structure with the callback function that is needed by the app
	SendResult($JsonResult);

