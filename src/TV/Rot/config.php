<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Modules.php');

if(empty($_REQUEST['Tour'])) {
	$IsCode=GetIsParameter('IsCode');
} else {
	$IsCode=$_REQUEST['Tour'];
	if(!getIdFromCode($IsCode)) {
		$IsCode=GetIsParameter('IsCode');
	}
}

$TourId=getIdFromCode($IsCode);

$Rule = empty($_REQUEST['Rule']) ? 1 : intval($_REQUEST['Rule']);
$Rule = empty($_REQUEST['rule']) ? $Rule : intval($_REQUEST['rule']);

// this is the live match
$SQL= "(Select '0' Team, FinEvent, FinMatchNo, MAX(FinDateTime) DateTime
		from Finals use index (FinLive)
		where FinLive='1' and FinTournament=$TourId
	) UNION (
		Select '1' team, TfEvent, TfMatchNo, MAX(TfDateTime) from TeamFinals
		where TfLive='1' and TfTournament=$TourId
	) ORDER BY DateTime DESC";



function MakeEventFilter($Event) {
	if(!$Event) return '';

	$Ret = "=" . StrSafe_DB($Event) . " ";

	$Arr_Ev = explode('|',$Event);
	if (count($Arr_Ev)>1) {
		sort($Arr_Ev);
		foreach ($Arr_Ev as $Key => $Value)
			$Arr_Ev[$Key]=StrSafe_DB($Value);

		$Ret = "IN (" . implode(',',$Arr_Ev) . ") ";
	}

	return $Ret;
}

function getCss($TourId, $Rule) {
	$ret='';
	$q=safe_r_SQL("select TVRSettings from TVRules WHERE TVRId=$Rule AND TVRTournament=$TourId");
	if($r=safe_fetch($q) and $Settings=unserialize($r->TVRSettings)) {
		$ret.="
		#body {".($Settings['name'] ? "font-family:{$Settings['name']};" : "" )."font-size:{$Settings['size']};{$Settings['content']}}
		#content {position:relative; box-sizing:border-box;height:100%;}
		.Font1e { color:{$Settings['col1e']}; }
		.Font1o { color:{$Settings['col1o']}; }
		.Font2e { color:{$Settings['col2e']}; }
		.Font2o { color:{$Settings['col2o']}; }
		.Rev1e { color:{$Settings['rev1e']}; }
		.Rev1o { color:{$Settings['rev1o']}; }
		.Rev2e { color:{$Settings['rev2e']}; }
		.Rev2o { color:{$Settings['rev2o']}; }
		.Back1e { background:{$Settings['bck1e']}; }
		.Back1o { background:{$Settings['bck1o']}; }
		.Back2e { background:{$Settings['bck2e']}; }
		.Back2o { background:{$Settings['bck2o']}; }
		.Title { {$Settings['title']}; }
		.Headers { {$Settings['Headers']}; }
		.TitleImg {display:inline-block;margin:-0.5vw;padding:0.1vw;background-color:white;}
		.TitleImg img {height:3.5vw; width:auto;}
		";
	}
	return '<style>'.$ret.'</style>';
}

function getCssPage($CSS, $Block, $BlockDefinition, $Rule) {
	$Headers='';
	if($RuleSettings=unserialize($Rule)) {
		$Headers=$RuleSettings['Headers']."; background:none; ";
	}
	$ret  = "<style>";
	$ret .= ".{$Block} {{$BlockDefinition}}";
	$ret .= ".{$Block} div {overflow: hidden; box-sizing:border-box; }";
	if($Block!='GridId') {
		$ret .= ".{$Block} div {margin-right:0.5rem; }";
	}
	if($CSS) {
		foreach($CSS as $k => $v) {
			switch($k) {
				case 'Title':
				case 'SubTitle':
				case 'TopRow':
				case 'BottomRow':
				case 'TgtBlock':
				case 'Loser':
				case 'Divider':
					$ret .= ".{$k} {{$v}}";
					break;
				case 'MainContent':
					$ret .= "#body {{$v}}";
					break;
				default:
					$ret .= ".{$Block} .{$k} {{$v}}";
					$ret .= ".TgtBlock .{$k} {{$v}}";
					if($Headers) $ret .= ".{$Block} .{$k}.Headers {{$Headers}}";
			}
		}
	}
	$ret .= "</style>";
	return $ret;
}