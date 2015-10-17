<?php

require_once('Common/Lib/ArrTargets.inc.php');

function SetElimArrowValue($Phase, $Event, $Target, $ArIndex, $ArSymbol, $Output='XML', $CompId='') {
	require_once('Common/Lib/Obj_RankFactory.php');
	$JsonResult=array();
	$JsonResult['error']      = 1;
	$JsonResult['qutarget']   = $_REQUEST['qutarget'];
	$JsonResult['distnum']    = $_REQUEST['distnum'] ;
	$JsonResult['arrowindex'] = $_REQUEST['arrowindex'] ;
	$JsonResult['arrowsymbol']= '';
	$JsonResult['curscore']   = '';
	$JsonResult['curgold']    = '';
	$JsonResult['curxnine']   = '';
	$JsonResult['score']      = '';
	$JsonResult['gold']       = '';
	$JsonResult['xnine']      = '';


	if(empty($CompId)) $CompId=$_SESSION['TourId'];

	$q=safe_r_sql("select * from Eliminations
		where ElElimPhase=".($Phase[1]-1)."
		and ElEventCode='$Event'
		and ElTargetNo='$Target'
		and ElTournament=$CompId
		");
	if($r=safe_fetch($q)) {
		$Arrowstring=str_pad($r->ElArrowString, $ArIndex+1, ' ', STR_PAD_RIGHT);
		$xx=GetLetterFromPrint($ArSymbol);
		$Arrowstring[$ArIndex]=str_pad($xx, 1, ' ', STR_PAD_RIGHT);
		list($CurScore, $CurGold, $CurXNine)=ValutaArrowStringGX($Arrowstring);

		safe_w_sql("update Eliminations
			set ElArrowString='{$Arrowstring}',
			ElScore=$CurScore,
			ElGold=$CurGold,
			ElXnine=$CurXNine
			where ElElimPhase=".($Phase[1]-1)."
			and ElEventCode='$Event'
			and ElTargetNo='$Target'
			and ElTournament=$CompId");

		if(safe_w_affected_rows()) {
			if($Phase[1]==1) {
				require_once('Common/Fun_Sessions.inc.php');
				ResetElimRows($Event, 2);
			}
			Obj_RankFactory::create('ElimInd',array('tournament'=>$CompId, 'eventsC'=>array($Event.'@'.$Phase[1])))->calculate();
		}

		$JsonResult['error']      = 0;
		$JsonResult['arrowsymbol']= $xx ? strtoupper($ArSymbol) : '';
		$JsonResult['curscore']   = $CurScore;
		$JsonResult['curgold']    = $CurGold;
		$JsonResult['curxnine']   = $CurXNine;
		$JsonResult['score']      = $CurScore;
		$JsonResult['gold']       = $CurGold;
		$JsonResult['xnine']      = $CurXNine;
	}

	return $JsonResult;
}