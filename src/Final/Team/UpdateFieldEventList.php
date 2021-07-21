<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

$JSON=array('error' => 1, 'which' => '#');

if(!CheckTourSession()
		or checkACL(AclCompetition, AclReadWrite, false)!=AclReadWrite
		or IsBlocked(BIT_BLOCK_TOURDATA)
		or empty($_REQUEST['field'])
		or !isset($_REQUEST['value'])) {
    print get_text('CrackError');
    exit;
}

$Key=$_REQUEST['field'];
$Value=$_REQUEST['value'];

if (substr($Key,0,2)=='d_') {
    $cc = '';
    $ee = '';
    list (,$cc,$ee)=explode('_',$Key);

	if(!preg_match('/^[0-9a-z_]+$/i', $cc)) {
		JsonOut($JSON);
	}

    $JSON['which'] = $Key;
    $Update
        = "UPDATE Events SET "
        . $cc . "=" . StrSafe_DB($Value) . " "
        . "WHERE EvCode=" . StrSafe_DB($ee) . " AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
    $RsUp=safe_w_sql($Update);

    if ($cc == 'EvMatchMode' || $cc == 'EvFinalTargetType') {
        if(safe_w_affected_rows()!=0) {
            safe_w_sql("UPDATE Events SET EvTourRules='' where EvCode=" . StrSafe_DB($ee) . " AND EvTeamEvent='1' AND EvTournament = " . StrSafe_DB($_SESSION['TourId']));
        }
    }

    $JSON['error']=0;
}

JsonOut($JSON);

