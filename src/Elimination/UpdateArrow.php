<?php

define('debug',false);

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');

if (!CheckTourSession()) {
	$JSON['msg']= get_text('CrackError');
	JsonOut($JSON);
}
checkACL(AclEliminations, AclReadWrite, false);

$JSON=array('error' => 1, 'msg'=>'');

if(empty($_REQUEST['arr'])) {
	JsonOut($JSON);
}

if (IsBlocked(BIT_BLOCK_ELIM)) {
	$JSON['msg']='Eliminations locked';
	JsonOut($JSON);
}

list(, $Index, $Event, $Phase, $EnId)=explode('_', $_REQUEST['arr']);
$Arrow=strtoupper($_REQUEST['value']);

// get the arrowstring
$q = safe_r_sql("select distinct if(ElElimPhase=0, EvE1Ends, EvE2Ends) Ends, if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) Arrows, ElArrowstring, EvFinalTargetType, ElTargetNo-1 as TgtOffset, ToCategory
	from Eliminations
	inner join Events on EvCode=ElEventCode and EvTeamEvent=0 and EvTournament=ElTournament
	inner join Tournament on ToId=ElTournament
	where ElTournament={$_SESSION['TourId']} and ElEventCode='$Event' and ElElimPhase=$Phase and ElId=$EnId");

if(!($r=safe_fetch($q))) {
	$JSON['msg']='Wrong data';
	JsonOut($JSON);
}

$MaxArrows=$r->Arrows*$r->Ends;
$ArrowString=str_pad($r->ElArrowstring, $MaxArrows, ' ', STR_PAD_RIGHT);
$ArrowStringOrg=$ArrowString;

// puts an M if it was a 0
if(strlen($Arrow)) {
	if($Arrow[0]=='0') $Arrow[0]='M';
}

// gets the correct letter (or space if wrong)
$Letter=GetLetterFromPrint($Arrow, 'T', $r->EvFinalTargetType);

// assigns the arrow to the correct place
$ArrowString[$Index]=$Letter;

// check if the arrow inserted is the same as the arrow received
$JSON['error']='0';
if($Arrow!=($tmp=ValutaArrowString($Letter))) {
	$JSON['error']='1';
	$Arrow=$tmp;
} else {
}

$Hits=strlen(str_replace(' ', '', $ArrowString));
list($Score, $Golds, $XNine)=ValutaArrowStringGX($ArrowString);

$JSON['value']=$Arrow;
$JSON['score']=$Score;
$JSON['gold'] =$Golds;
$JSON['xnine']=$XNine;
$JSON['hits']=$Hits;

// update the DB
safe_w_sql("UPDATE Eliminations SET
	ElArrowstring='$ArrowString',
	ElScore=$Score,
	ElGold=$Golds,
	ElXNine=$XNine,
	ElHits=$Hits
	where ElTournament={$_SESSION['TourId']} and ElEventCode='$Event' and ElElimPhase=$Phase and ElId=$EnId");
if(safe_w_affected_rows()) {
	safe_w_sql("UPDATE Eliminations SET ElDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where ElTournament={$_SESSION['TourId']} and ElEventCode='$Event' and ElElimPhase=$Phase and ElId=$EnId");

	if ($Event) {
		if ($Phase==0) {
			ResetElimRows($Event,2);
		}

		Obj_RankFactory::create('ElimInd',array('eventsC'=>array($Event.'@'.($Phase+1))))->calculate();
	}

	// reset ShootOffs
	ResetShootoff($Event, 0, $Phase+1);
}

if($_REQUEST['type']=='score') {
	// make all the calculations end by end
	$TotRunning=0;
	$TotEndRun=0;

	$OffSet = ($r->ToCategory & 12) ? $r->TgtOffset : 0;

	for($i=0; $i<$r->Ends; $i++) {
		$RealI=(($i+$OffSet)%$r->Ends)+1;
		$ArrNo = (($i+$OffSet)%$r->Ends) * $r->Arrows;
		$TotEnd=ValutaArrowString(substr($ArrowString, $ArrNo, $r->Arrows));
		$TotEndRun += $TotEnd;
		$TotRunning += $TotEnd;

		$JSON['details'][$RealI]=array(
			'end' => $TotEnd,
			'endrun' => $TotEndRun,
			'score' => $TotRunning
		);
	}
}

JsonOut($JSON);

