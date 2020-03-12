<?php
/*
- UpdateField.php -
Updates Entry fields
*/

require_once(dirname(__FILE__) . '/cfg.php');

$JSON=array('error' => 1, 'msg' => get_text('WrongData', 'Install'));

$ToId=(empty($_REQUEST['ToId']) ? 0 : intval($_REQUEST['ToId']));
$QuTargetNo=(empty($_REQUEST['QuTargetNo']) ? '' : (preg_match('/^[0-9a-z]+$/sim', $_REQUEST['QuTargetNo']) ? $_REQUEST['QuTargetNo'] : ''));
$EnId=(empty($_REQUEST['EnId']) ? 0 : intval($_REQUEST['EnId']));
$Field=(empty($_REQUEST['field']) ? '' : $_REQUEST['field']);
$Value=(empty($_REQUEST['value']) ? '' : $_REQUEST['value']);

if(IsBlocked(BIT_BLOCK_PARTICIPANT) or !$ToId or !$Field or !$EnId) {
	if(IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		$JSON['msg']=get_text('Blocked');
	}
	JsonOut($JSON);
}

require_once('./lib.php');

saveField($Field, $Value, $EnId, $ToId);

JsonOut($JSON);
