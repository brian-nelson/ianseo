<?php
/*
													- UpdateQuals.php -
	Aggiorna la tabella Qualifications
*/

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');

$JSON=array(
	'error' => 1,
	'msg' => '',
	'total' => '',
	'hits' => '',
	'golds' => '',
	'xnine' => '',
	'value' => '',
);

if (!CheckTourSession()) {
	$JSON['msg']= get_text('CrackError');
	JsonOut($JSON);
}
checkACL(AclEliminations, AclReadWrite, false);

foreach ($_REQUEST as $Key => $Value) {
	if (substr($Key,0,2)=='d_') {
		if (IsBlocked(BIT_BLOCK_ELIM)) {
			$JSON['msg']= 'Competition is Locked';
			JsonOut($JSON);
		}

		list(,$Field, $EnId, $Event, $Phase)=explode('_',$Key);
		$EnId=intval($EnId);
		$Phase=intval($Phase);
		$Event=preg_replace('/[^a-z0-9_.-]/sim', '', $Event);
		switch($Field) {
			case 'ElScore':
			case 'ElGold':
			case 'ElXnine':
			case 'ElHits':
				// here just not to fall into the default!
				break;
			default:
				$JSON['msg']= 'Wrong field';
				JsonOut($JSON);
		}

		$Sel = "SELECT $Field AS OldValue, ElScore, ElGold, ElXnine, ElHits, ToGolds, ToXNine, ToCategory, EvFinalTargetType, 
			if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) as Arrows, if(ElElimPhase=0, EvE1Ends, EvE2Ends) as Ends
			FROM Eliminations
			inner join Events on EvCode=ElEventCode and EvTeamEvent=0 and EvTournament=ElTournament
			inner join Tournament on ToId=ElTournament
			WHERE ELTournament={$_SESSION['TourId']} and ElId={$EnId} and ElEventCode='{$Event}' AND ElElimPhase={$Phase}";
		$RsSel =safe_r_sql($Sel);
		if(!($rr=safe_fetch($RsSel))) {
			$JSON['msg']= 'Bad Selection';
			JsonOut($JSON);
		}

		$JSON['total'] = $rr->ElScore;
		$JSON['hits' ] = $rr->ElHits;
		$JSON['golds'] = $rr->ElGold;
		$JSON['xnine'] = $rr->ElXnine;

		$OldValue=$rr->OldValue;
		$MaxArrowValue=GetMaxTargetValue(GetGoodLettersFromTgtId($rr->EvFinalTargetType));
		$MaxScore=$rr->Ends*$rr->Arrows*$MaxArrowValue;

		// Check if the given value is fittable with the competition
		$tmp=explode('+', $rr->ToGolds);
		$Gtmp=DecodeFromPrint(end($tmp));
		$tmp=explode('+', $rr->ToXNine);
		$Xtmp=DecodeFromPrint(end($tmp));
		switch($Field) {
			case 'ElScore':
				$Error = ($Value>$MaxScore);
				if(!$Error) {
					$JSON['total'] = $Value;
					$JSON['value'] = $Value;
				}
				break;
			case 'ElGold':
				$Error = ($Value>floor($rr->ElScore/$Gtmp));
				if(!$Error) {
					$JSON['golds'] = $Value;
					$JSON['value'] = $Value;
				}
				break;
			case 'ElXnine':
				$Error = ($Value>floor((($rr->ElScore - ($Gtmp*$rr->ElGold))/$Xtmp)));
				if(!$Error) {
					$JSON['xnine'] = $Value;
					$JSON['value'] = $Value;
				}
				break;
			case 'ElHits':
				$Error = ($rr->ElGold and $Value<floor($rr->ElScore/($Gtmp*$rr->ElGold )) OR $Value<$rr->ElGold or $Value<$rr->ElXnine);
				if(!$Error) {
					$JSON['hits' ] = $Value;
					$JSON['value'] = $Value;
				}
				break;
		}
		$JSON['error']=$Error;

		if(!$Error) {
			// write data
			$Update = "UPDATE Eliminations
				SET $Field = " . StrSafe_DB($Value) . ",
				ElDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
				WHERE ElId=$EnId and ElEventCode='$Event' AND ElElimPhase=$Phase";
			$RsUp=safe_w_sql($Update);
			if (safe_w_affected_rows() and $OldValue!=$Value) {
				/*
				 * Reset the 2nd phase if any
				 */
				if ($Phase==0) {
					ResetElimRows($Event, 2);
				}

				Obj_RankFactory::create('ElimInd',array('eventsC'=>array($Event.'@'.($Phase+1))))->calculate();

				// reset the Shoot Offs
				ResetShootoff($Event, 0, $Phase+1);
			}
		}
	}
}

JsonOut($JSON);
