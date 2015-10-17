<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);

require_once('./LibScheduler.php');

if(empty($_REQUEST['Fld'])) out();

$Field=key($_REQUEST['Fld']);

switch($Field) {
	case 'Q':
	case 'E':
	case 'I':
	case 'T':
	case 'Z':
		$function="Do{$Field}Schedule";
		$function(current($_REQUEST['Fld']));
}

// always outputs error...
out();

function DoESchedule($Item) {
	DoQSchedule($Item, $Type='E');
}

function DoQSchedule($Item, $Type='Q') {
	$Field=key($Item);
	switch($Field) {
		case 'Day':
			$ret=InsertSchedDate(current($Item), $Type);
			break;
		case 'WarmTime':
			$ret=InsertSchedTime(current($Item), 'Warm', $Type);
			break;
		case 'WarmDuration':
			$ret=InsertSchedDuration(current($Item), 'Warm', $Type);
			break;
		case 'Start':
			$ret=InsertSchedTime(current($Item), '', $Type);
			break;
		case 'Duration':
			$ret=InsertSchedDuration(current($Item), '', $Type);
			break;
		case 'Shift':
			$ret=InsertSchedShift(current($Item), $Type);
			break;
		case 'Options':
			$ret=InsertSchedComment(current($Item), $Type);
			break;
		default:
			debug_svela($Field);
	}
	out($ret);
}

function DoTSchedule($Item) {
	DoISchedule($Item, '1');
}

function DoISchedule($Item, $Team='0') {
	$Field=key($Item);
	switch($Field) {
		case 'Day':
			$ret=ChangeFinSchedDate(current($Item), $Team);
			break;
		case 'Start':
			$ret=ChangeFinSchedTime(current($Item), $Team);
			break;
		case 'Duration':
			$ret=ChangeFinSchedDuration(current($Item), $Team);
			break;
		case 'Shift':
			$ret=ChangeFinShift(current($Item), $Team);
			break;
		case 'WarmTime':
			$ret=ChangeFinSchedWarmTime(current($Item), $Team);
			break;
		case 'WarmDuration':
			$ret=ChangeFinSchedWarmDuration(current($Item), $Team);
			break;
		case 'Options':
			$ret=ChangeFinComment(current($Item), $Team);
			break;
		default:
			debug_svela($Field);
	}
	out($ret);
}

function DoZSchedule($Item) {
	$Field=key($Item);
	switch($Field) {
		case 'Day':
			$ret=InsertTextDate(current($Item));
			break;
		case 'Start':
			$ret=InsertTextTime(current($Item));
			break;
		case 'Order':
			$ret=InsertTextDuration(current($Item), true);
			break;
		case 'Duration':
			$ret=InsertTextDuration(current($Item));
			break;
		case 'Shift':
			$ret=InsertTextShift(current($Item));
			break;
		case 'Title':
			$ret=InsertText(current($Item), 'Title');
			break;
		case 'SubTitle':
			$ret=InsertText(current($Item), 'SubTitle');
			break;
		case 'Text':
			$ret=InsertText(current($Item), 'Text');
			break;
		default:
			debug_svela($Field);
	}
	out($ret);

}