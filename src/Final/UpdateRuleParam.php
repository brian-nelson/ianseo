<?php
/*
	Aggiorna il campo di Events passato in querystring.
*/

require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error' => 1, 'msg' => 'Error');

if(checkACL(AclCompetition, AclNoAccess) != AclReadWrite
		or !CheckTourSession()
		or empty($_REQUEST['event'])
		or empty($_REQUEST['fld'])
		or !isset($_REQUEST['val'])
		or !isset($_REQUEST['team'])
		) {
	JsonOut($JSON);
}

if(IsBlocked(BIT_BLOCK_TOURDATA)) {
	$JSON['msg']=get_text('LockedProcedure', 'Errors');
	JsonOut($JSON);
}

require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

$Team=intval($_REQUEST['team']);
$Where=" where EvTeamEvent=$Team and EvCode=" . StrSafe_DB($_REQUEST['event']) . " and EvTournament={$_SESSION['TourId']}";

$RedoBrackets=false;
$ResetSO=false;
switch($_REQUEST['fld']) {
	case 'persons':
		$SQL="update Events set EvMaxTeamPerson=" . intval($_REQUEST['val']) . $Where;
		$RedoBrackets=true;
		break;
	case 'wacat':
		$SQL="update Events set EvWaCategory=" . StrSafe_DB($_REQUEST['val']) . $Where;
		break;
	case 'odfcode':
		$SQL="update Events set EvOdfCode=" . StrSafe_DB(str_pad(rtrim($_REQUEST['val'],' -'),22, '-', STR_PAD_RIGHT)) . $Where;
		break;
	case 'reccat':
		$SQL="update Events set EvRecCategory=" . StrSafe_DB($_REQUEST['val']) . $Where;
		break;
	case 'num':
		$SQL="update Events set EvNumQualified=" . intval($_REQUEST['val']) . $Where;
		$RedoBrackets=true;
		break;
	case 'final':
		$SQL="update Events set EvWinnerFinalRank=" . intval($_REQUEST['val']) . $Where;
		break;
	case 'first':
		$SQL="update Events set EvFirstQualified=" . intval($_REQUEST['val']) . $Where;
		$RedoBrackets=true;
		break;
	case 'medal':
		$SQL="update Events set EvMedals=" . ($_REQUEST['val'] ? '1' : '0') . $Where;
		break;
	case 'parent':
		// check the parent code really exists
		$q=safe_r_sql("select * from Events where EvTeamEvent=$Team and EvCode=" . StrSafe_DB($_REQUEST['val']) . " and EvTournament={$_SESSION['TourId']}");
		if(!safe_num_rows($q)) {
			JsonOut($JSON);
		}
		$SQL="update Events set EvCodeParent=" . StrSafe_DB($_REQUEST['val']) . $Where;
		$RedoBrackets=true;
		break;
	default:
		JsonOut($JSON);
}

$RsIns=safe_w_sql($SQL);

if (safe_w_affected_rows()) {
	safe_w_sql("UPDATE Events SET EvTourRules=''" . $Where );
}

if($RedoBrackets) {
	// TODO: need to destroy and recreate the brackets
	ResetShootoff($_REQUEST['event'], $Team,0);
} elseif($ResetSO) {
	// reset of the Event's SO
	ResetShootoff($_REQUEST['event'], $Team,0);
}

// rebuild Teams/Individuals
if($Team) {
	MakeTeamsAbs(null, null, null);
} else {
	MakeIndAbs();
}

$JSON['error']=0;

JsonOut($JSON);
