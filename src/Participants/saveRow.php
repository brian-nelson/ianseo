<?php
require_once(dirname(__FILE__) . '/cfg.php');

$JSON=array('error' => 1, 'msg' => get_text('WrongData', 'Install'));

$ToId=(empty($_REQUEST['ToId']) ? 0 : intval($_REQUEST['ToId']));
$Field=(empty($_REQUEST['field']) ? '' : $_REQUEST['field']);

if(IsBlocked(BIT_BLOCK_PARTICIPANT) or !$ToId or !$Field) {
	if(IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		$JSON['msg']=get_text('Blocked');
	}
	JsonOut($JSON);
}

require_once('./lib.php');

// start creating a new Entry, only if the correct value for ToId is set!
$ToId=$TourId;
if($_SESSION['AccreditationTourIds']) {
	$ToId=0;
	if(!empty($_REQUEST['field']['tourid'])) {
		$ToId=intval($_REQUEST['field']['tourid']);
	}
}

if(!$ToId) {
	JsonOut($JSON);
}

debug_svela($_REQUEST);

// gets the standard LookupTable for this individual
$LueTable='';
$q=safe_r_sql("select ToIocCode from Tournament where ToId=$ToId");
if($r=safe_fetch($q) and $r->ToIocCode) {
	$LueTable=$r->ToIocCode;
}

safe_w_sql("insert into Entries set EnIocTable=".StrSafe_DB($LueTable).", EnTournament=$ToId");
$EnId=safe_w_last_id();
safe_w_sql("insert into Qualifications set QuId=$EnId");

if(isset($_REQUEST['field']['tourid'])) {
	unset($_REQUEST['field']['tourid']);
}
foreach(array(
        'country_code',
        'country_name',
        'country_code2',
        'country_name2',
        'country_code3',
        'country_name3',
        'name',
        'firstname',
        'code',
        'targetno',
        'locCode',
        'email',
        'caption',
        'dob',
        'session',
        'sex',
        'wc',
        'division',
        'ageclass',
        'class',
        'subclass',
		) as $Field) {
	if(!isset($_REQUEST['field'][$Field])) {
		continue;
	}

	$JSON['field'][$Field] = saveField($Field, $_REQUEST['field'][$Field], $EnId, $ToId);
}

/*

    )




*/