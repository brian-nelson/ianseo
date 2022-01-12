<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Final.local.inc.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('./var.inc.php');

$JSON=array('error' => 1, 'rows' => array(), 'serverdate' => '', 'newdata' => '');

$schedule=(isset($_REQUEST['schedule']) && preg_match('/^[01]{1}[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}(:[0-9]{2})*$/',$_REQUEST['schedule']) ? substr($_REQUEST['schedule'],1) : null);
$team=(isset($_REQUEST['schedule']) && preg_match('/^[01]{1}[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}(:[0-9]{2})*$/',$_REQUEST['schedule']) ? substr($_REQUEST['schedule'],0,1) : null);
$events=(isset($_REQUEST['events']) && is_array($_REQUEST['events'])  ? $_REQUEST['events'] : array());
$serverDate=(isset($_REQUEST['serverDate']) ? $_REQUEST['serverDate'] : 0);
$parameters=isset($_REQUEST['parameters']) ? $_REQUEST['parameters'] : null;

if(strlen($schedule)<19) $schedule.=':00';

$query="SELECT UNIX_TIMESTAMP('".date('Y-m-d H:i:s')."') AS serverDate ";
$rs=safe_r_sql($query);
$row=safe_fetch($rs);
$JSON['serverdate']=$row->serverDate;
if($IskSequence=getModuleParameter('ISK', 'Sequence')) {
	if(!isset($IskSequence['session'])) {
		$IskSequence=current($IskSequence);
	}
	$tmp=str_replace(' ', '', $schedule);
	// get the running sequence
	$JSON['newdata']=($IskSequence['session']==$tmp ? '' : 'newdata');
}

if (empty($_SESSION['TourId']) && (is_null($schedule) || is_null($team))) {
	JsonOut($JSON);
}
checkACL(AclSpeaker, AclReadOnly, false);

$otherWhere= "
	AND fs1.FSTeamEvent=" . StrSafe_DB($team) . "
	AND (CONCAT(fs1.FSScheduledDate,' ',fs1.FSScheduledTime)=" . StrSafe_DB($schedule) . " OR CONCAT(fs2.FSScheduledDate,' ',fs2.FSScheduledTime)=" . StrSafe_DB($schedule) . ")
";

if (count($events)>0 && $events[0]!='') {
	//array_walk($events,'safe');
	$otherWhere .= " AND (fs1.FSEvent IN(" . implode(',',StrSafe_DB($events)) . ") OR fs2.FSEvent IN(". implode(',', StrSafe_DB($events)) .") )";
}

/*
 * cerco se ci sono scontri aggiornati rispetto alla serverDate passata, se sì ritorno tutto altrimenti nulla
 */
$countSql='';
if ($team==0) {
	$countSql= "SELECT FinMatchNo
		 FROM Finals
		 	LEFT JOIN FinSchedule ON FinMatchNo=FSMatchNo AND FinEvent=FSEvent AND FSTeamEvent=0 AND FinTournament=FSTournament
		 WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($schedule) . "
			AND UNIX_TIMESTAMP(FinDateTime)>" . StrSafe_DB($serverDate) . " ";
} else {
	$countSql= "SELECT TfMatchNo
		 FROM TeamFinals
		 	LEFT JOIN FinSchedule ON TfMatchNo=FSMatchNo AND TfEvent=FSEvent AND FSTeamEvent=1 AND TfTournament=FSTournament
		 WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($schedule) . "
			AND UNIX_TIMESTAMP(TfDateTime)>" . StrSafe_DB($serverDate) . " ";
}

$orderBy=" concat(fs1.FSTarget,fs2.FSTarget) ASC ";


$rs=safe_r_sql($countSql);
if (safe_num_rows($rs)>0) {
	$rs=GetFinMatches_sql($otherWhere,$team,$orderBy,false);

	if (safe_num_rows($rs)>0) {
		$points4win=array();
		$arrow4Match=array();
		$obj=null;
		$max=0;
		$stdArrowShot=0;
		$tieArrowShot=0;

	// primo giro x inizializzare i vettori accessori
		while ($myRow=safe_fetch($rs)) {
			$obj=getEventArrowsParams($myRow->event,$myRow->phase,$team);
			$points4win[$myRow->event]=$obj->winAt;
			$arrow4Match[$myRow->event]=$obj->ends*$obj->arrows;
			// massimo delle somme dei punti win + punti loser
			$sum=$myRow->setScore1+$myRow->setScore2;
			if ($sum>$max) {
				$max=$sum;
			}
			if($myRow->matchMode==0) {
				$stdArrowShot = max($stdArrowShot,strlen(trim($myRow->arrowString1)),strlen(trim($myRow->arrowString2)));
				$tieArrowShot = max($tieArrowShot,strlen(trim($myRow->tiebreak1)),strlen(trim($myRow->tiebreak2)));
			}
		}

		safe_data_seek($rs,0);	// resetto il puntatore

		$id=0;	// id fittizio
		while ($myRow=safe_fetch($rs)) {
			$target=$myRow->target1;
            $target2='';
			if ($myRow->target2!=$myRow->target1)
				$target2 = $myRow->target2;

			$score1=$myRow->score1;
			$score2=$myRow->score2;

			if ($myRow->matchMode==1) {
				$score1=$myRow->setScore1;
				$score2=$myRow->setScore2;
			}

			$score=$score1 . ' - ' . $score2;
			$setPoints1='';
			$setPoints2='';

			if ($myRow->tie1==2 && $myRow->tie2!=2) {
				$setPoints1=get_text('Bye');
			} elseif ($myRow->tie1!=2 && $myRow->tie2==2) {
				$setPoints2=get_text('Bye');
			} elseif ($myRow->matchMode==1) {
				list($setPoints1,$setPoints2)=purgeSetPoints($myRow->setPoints1,$myRow->setPoints2);
			} else {
				for($cEnd=0; $cEnd<strlen($myRow->arrowString1); $cEnd+=$obj->arrows) {
					$setPoints1 = $setPoints1 . ValutaArrowString(substr($myRow->arrowString1,$cEnd,$obj->arrows)) . " ";
				}
				for($cEnd=0; $cEnd<strlen($myRow->arrowString2); $cEnd+=$obj->arrows) {
					$setPoints2 = $setPoints2 . ValutaArrowString(substr($myRow->arrowString2,$cEnd,$obj->arrows)) . " ";
				}
			}

			// le frecce di tiebreak
			for ($index=1;$index<=2;++$index) {
				$arrowstring=$myRow->{'tiebreak'.$index};
				if (trim($arrowstring)!='') {
					//print 'pp';
					$tmp=array();
					for ($i=0;$i<strlen($arrowstring);++$i) {
						$tmp[]=DecodeFromLetter($arrowstring[$i]);
					}
                    if($myRow->{'tieclosest'.$index} != 0) {
                        $tmp[] = '+';
                    }
					${'setPoints'.$index}.=' ' . implode(' ',$tmp);
				}
			}

			/*
			 * 0 => il match no è finito
			 * 1 => il match è finito prima
			 * 2 => il match è finito ora
			 * 3 => shootoff
			 */
			$finished=0;

			/*
			 * <r> stabilisce lo stato di lettura della riga.
			 * Normalmente è zero però il suo valore diventa 1 se:
			 * 1) il match è finito in una volee precedente all'attuale check.
			 * 2) esiste nella request la var corrispondente e vale 1
			 * Questo mi serve per inizializzare la colonna read dello store.
			 *
			 */

			$r=0;
			if ($myRow->matchMode==1) {
				$finished=isFinished($myRow,$points4win,$max);
			} elseif($myRow->tie1==2 || $myRow->tie2==2) {
				$finished = 1;
			} elseif(strlen(trim($myRow->arrowString1))==$arrow4Match[$myRow->event] && strlen(trim($myRow->arrowString2))==$arrow4Match[$myRow->event]) {
				if($myRow->score1 != $myRow->score2 || ($myRow->tie1==1 || $myRow->tie2==1)) {
					if(strlen(trim($myRow->arrowString1))==$stdArrowShot && strlen(trim($myRow->arrowString2))==$stdArrowShot && strlen(trim($myRow->tiebreak1))==$tieArrowShot && strlen(trim($myRow->tiebreak2))==$tieArrowShot) {
						$finished = 2;
					} else {
						$finished = 1;
					}
				} elseif($myRow->score1 == $myRow->score2) {
					$finished = 3;
				}
			}

			if ($finished==1) {
				$r=1;
			}

			// controllo la request
			if (isset($_REQUEST['r_' . $id]) && preg_match('/^[0-1]{1}$/',$_REQUEST['r_' . $id]) && $myRow->lastUpdate<$serverDate) {
				$r=$_REQUEST['r_' . $id];
			}
			if($target or $target2) {
				$JSON['rows'][]=array(
					'id'	=> $id,
					'f'		=> $finished,
					'r'		=> $r,
					'ev'	=> $myRow->event,
					'ph'	=> get_text(namePhase($myRow->firstPhase,$myRow->phase). '_Phase'),
					'evn'	=> $myRow->eventName,
					't'	    => $target,
                    't2'	=> $target2,
					'n1'	=> $myRow->name1 . ' (#' . $myRow->rank1 . ')',
					'cn1'	=> $myRow->countryName1,
					'ar1'	=> strlen(str_replace(' ','',$myRow->arrowString1)),
					'sar1'	=> strlen(str_replace(' ','',$myRow->tiebreak1)),
					'n2'	=> $myRow->name2. ' (#' . $myRow->rank2 . ')',
					'cn2'	=> $myRow->countryName2,
					'ar2'	=> strlen(str_replace(' ','',$myRow->arrowString2)),
					'sar2'	=> strlen(str_replace(' ','',$myRow->tiebreak2)),
					'sp1'	=> $setPoints1,
					'sp2'	=> $setPoints2,
					's'		=> $score,
					'lu'	=> $myRow->lastUpdate,
				);
				++$id;
			}
		}
	}
}

$JSON['error']=0;

JsonOut($JSON);
