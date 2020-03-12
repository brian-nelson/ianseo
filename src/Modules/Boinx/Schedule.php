<?php
require_once('../../config.php');
require_once('Common/Fun_FormatText.inc.php');
checkACL(AclOutput,AclReadWrite);

$PAGE_TITLE=get_text('BoinxSchedule', 'Boinx');

$JS_SCRIPT=array(
	'<script type="text/javascript" src="../../Common/ajax/ObjXMLHttpRequest.js"></script>',
	'<script type="text/javascript" src="Fun_AJAX_Schedule.js"></script>',
	'<script type="text/javascript" >var StrConfirm="' . get_text('MsgAreYouSure') . '";</script>'
	);

include('Common/Templates/head.php');
?>
<style>
.button_off, .button_on {
	margin:3px 0 0 0;
	padding:0 5px 1px 5px;
	border:2px outset #CC0000;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
	background-color:#ffd0d0;
	cursor: pointer;
	}
.button_on {
	border:2px inset blue;
	background-color:#d0d0ff;
	padding:1px 5px 0 5px;
	}

div.subtitle {
	margin-top:1em;
	font-size:small;
	font-weight:bold;
	}

div.Title {
	margin-top:1em;
	font-size:small;
	font-weight:bold;
	}

#RssDiv td {padding:0 2px;}


</style>

<div id="RssDiv">
<div class="Title" style="cursor:pointer" onclick="this.nextElementSibling.style.display=(this.nextElementSibling.style.display=='none'?'table':'none')"><?php print get_text('ScheduleFeed','Boinx');?></div>
<table class="Tabella">
<?php

// Extra
echo getExtra($_SESSION['TourId']);

echo '<tr><td colspan="4">&nbsp;</td></tr>';

// Rss
echo getRss($_SESSION['TourId']);

echo '<tr><td colspan="4">&nbsp;</td></tr>';

// Feed
echo getFeeds($_SESSION['TourId']);

?>
</table>
</div>

<div id="GrdDiv">
<div class="Title" style="cursor:pointer" onclick="this.nextElementSibling.style.display=(this.nextElementSibling.style.display=='none'?'table':'none')"><?php print get_text('ScheduleGrids','Boinx');?></div>
<table class="Tabella">
<?php

// Grids
echo getGrids($_SESSION['TourId']);

?>
</table>
</div>

<div id="AwaDiv">
<div class="Title" style="cursor:pointer" onclick="this.nextElementSibling.style.display=(this.nextElementSibling.style.display=='none'?'table':'none')"><?php print get_text('ScheduleAwards','Boinx');?></div>
<table class="Tabella">
<?php

// Awards
echo getAwards($_SESSION['TourId']);

?>
</table>
</div>

<?php
include('Common/Templates/tail.php');

function getExtra($TourId) {
	$QuaSes=0;
	$ret='<tr valign="top">';

	// select all the possible feeds...
	$ret.='<td colspan="4"><table cellpadding="0" cellspacing="0" border="0" width="100%">';

	// Qualifications IND (Div and Class)
	$SQL="select distinct QuSession, BsType, left(BsExtra, 1) as BsSession, substr(BsExtra, 2) as BsTargetNo
			from Qualifications
			inner join Entries on EnId=QuId and EnTournament=$TourId
			left join BoinxSchedule on EnTournament=BsTournament and BsType='Qua_Ind'
			where QuSession!='0'
			order by QuSession";
	$q=safe_r_sql($SQL);
	$ret.='<tr><th colspan="'.(safe_num_rows($q)+2).'">'.get_text('Session')."</th></tr>\n";
	$ret.='<tr><td>';
	$StartLst='';
	while($r=safe_fetch($q)) {
		if($r->BsSession==$r->QuSession) $QuaSes=$r->QuSession;
		$ret.= '<div class="button_'.($r->BsSession==$r->QuSession ? 'on' : 'off').'" id="Qua_Ind_'.$r->QuSession.'" onclick="toggle(this)">'.get_text('Session'). ' ' . $r->QuSession .'</div>';
	}
	$ret.='</td><td><input type="text" id="Qua_Ind_Bib" onblur="toggle(this)"></td>';
	$SQL="select distinct QuSession, BsType, right(BsType, 1) as BsSession
			from Qualifications
			inner join Entries on EnId=QuId and EnTournament=$TourId
			left join BoinxSchedule on EnTournament=BsTournament and BsType like 'Rss_Lst_Q%' and right(BsType, 1)=QuSession
			where QuSession!='0'
			order by QuSession";
	$q=safe_r_sql($SQL);
	while($r=safe_fetch($q)) {
		if($r->BsSession==$r->QuSession) $QuaSes=$r->QuSession;
		$StartLst.='<div class="button_'.($r->BsSession==$r->QuSession ? 'on' : 'off').'" id="Rss_Lst_Q'.$r->QuSession.'" onclick="toggle(this)">'.get_text('Session'). ' ' . $r->QuSession .' - '.get_text('Targets','Tournament').'</div>';
	}
	$ret.='<td>'.$StartLst.'</td>';
	$ret.= '</tr>';

	$ret.='</table></td>';
	echo '<script type="text/javascript">var QuaSes="'.$QuaSes.'";</script>'; // outputs directly to the browser!
	return $ret;
}

function getAwards($TourId) {
	$ret='<tr><th>'.get_text('ResultIndClass','Tournament').'</th><th>'.get_text('ResultSqClass','Tournament').'</th><th>'.get_text('IndFinal').'</th><th>'.get_text('TeamFinal').'</th></tr>';
	$ret.='<tr valign="top">';

	// select all the possible awards...
	// Qualifications IND (Div and Class)
	$ret.="<td>\n";
	$q=safe_r_sql("select distinct DivId, ClId, BsType, DivDescription, ClDescription from Entries inner join (select DivId, ClId, DivTournament, DivDescription, ClDescription, DivViewOrder,ClViewOrder from Divisions inner join Classes on DivTournament=ClTournament where ClAthlete and DivAthlete) DivClass on EnTournament=DivTournament and EnDivision=DivId and EnClass=ClId left join BoinxSchedule on DivTournament=BsTournament and BsType=concat_ws('_', 'Awa', 'Ind', DivId, ClId) where DivTournament=$TourId order by DivViewOrder,ClViewOrder");
	while($r=safe_fetch($q)) {
		$ret.='<div name="Awa" class="button_'.($r->BsType ? 'on' : 'off').'" id="Awa_Ind_'.$r->DivId.'_'.$r->ClId.'" onclick="toggle(this)">'.get_text($r->DivDescription, '', '', true)." ".get_text($r->ClDescription, '', '', true)."</div>\n";
	}

	$ret.="</td><td>\n";
	// Qualifications Team (Div and Class)
	$q=safe_r_sql("select distinct DivId, ClId, BsType, DivDescription, ClDescription from Teams inner join (select concat(DivId, ClId) DivCl, DivId, ClId, DivTournament, DivDescription, ClDescription, DivViewOrder,ClViewOrder from Divisions inner join Classes on DivTournament=ClTournament where ClAthlete and DivAthlete) DivClass on TeTournament=DivTournament and TeEvent=DivCl left join BoinxSchedule on DivTournament=BsTournament and BsType=concat_ws('_', 'Awa', 'Team', DivId, ClId) where DivTournament=$TourId order by DivViewOrder,ClViewOrder");
	while($r=safe_fetch($q)) {
		$ret.='<div name="Awa" class="button_'.($r->BsType ? 'on' : 'off').'" id="Awa_Team_'.$r->DivId.'_'.$r->ClId.'" onclick="toggle(this)">'.get_text($r->DivDescription, '', '', true)." ".get_text($r->ClDescription, '', '', true)."</div>\n";
	}

	$ret.="</td><td>\n";
	// Finals Individual
	$q=safe_r_sql("Select distinct BsType, EvCode, EvEventName from Finals inner join Events on FinTournament=EvTournament and EvCode=FinEvent and EvTeamEvent=0 left join BoinxSchedule on FinTournament=BsTournament and BsType=concat_ws('_', 'Awa', 'Abs', EvCode) where FinTournament=$TourId order by EvProgr");
	while($r=safe_fetch($q)) {
		$ret.='<div name="Awa" class="button_'.($r->BsType ? 'on' : 'off').'" id="Awa_Abs_'.$r->EvCode.'" onclick="toggle(this)">'.get_text($r->EvEventName, '', '', true)."</div>\n";
	}

	$ret.="</td><td>\n";
	// Finals Teams
	$q=safe_r_sql("Select distinct BsType, EvCode, EvEventName from TeamFinals inner join Events on TfTournament=EvTournament and EvCode=TfEvent and EvTeamEvent=1 left join BoinxSchedule on TfTournament=BsTournament and BsType=concat_ws('_', 'Awa', 'AbsTeam', EvCode) where TfTournament=$TourId order by EvProgr");
	while($r=safe_fetch($q)) {
		$ret.='<div name="Awa" class="button_'.($r->BsType ? 'on' : 'off').'" id="Awa_AbsTeam_'.$r->EvCode.'" onclick="toggle(this)">'.get_text($r->EvEventName, '', '', true)."</div>\n";
	}

	$ret.="</td></tr>";
	return $ret;
}

function getGrids($TourId) {
	$ret='<tr><th>'.get_text('BracketsInd').'</th><th>'.get_text('BracketsSq').'</th></tr>';
	$ret.='<tr valign="top"><td>';

	// Grids start from 8th or less anyway
	// select all the phases available in this tournament...
	// Individual
	$q=safe_r_sql("SELECT distinct"
		. " FinEvent"
		. ", EvEventName"
		. ", BsType "
		. "FROM"
		. " Finals"
		. " inner join Events on FinTournament=EvTournament and EvCode=FinEvent and EvTeamEvent=0"
		. " left join BoinxSchedule on FinTournament=BsTournament and BsType=concat_ws('_', 'Grd', 'Ind', FinEvent) "
		. "where"
		. " FinTournament=$TourId "
		. "order by"
		. " EvProgr");
	$OldEvent='';
	$grid=array();
	$cols=0;
	while($r=safe_fetch($q)) {
		$ret.='<div name="Grd" class="button_'.($r->BsType ? 'on' : 'off').'" id="Grd_Ind_'.$r->FinEvent.'" onclick="toggle(this)">'.get_text($r->EvEventName, null, null, true)."</div>\n";
	}

	// Teams
	$ret.="</td><td>\n";
	$q=safe_r_sql("SELECT distinct"
		. " TfEvent FinEvent"
		. ", EvEventName"
		. ", BsType "
		. "FROM"
		. " TeamFinals"
		. " inner join Events on TfTournament=EvTournament and EvCode=TfEvent and EvTeamEvent=1"
		. " left join BoinxSchedule on TfTournament=BsTournament and BsType=concat_ws('_', 'Grd', 'Team', TfEvent) "
		. "where"
		. " TfTournament=$TourId "
		. "order by"
		. " EvProgr");
	$OldEvent='';
	$grid=array();
	$cols=0;
	while($r=safe_fetch($q)) {
		$ret.='<div name="Grd" class="button_'.($r->BsType ? 'on' : 'off').'" id="Grd_Team_'.$r->FinEvent.'" onclick="toggle(this)">'.get_text($r->EvEventName, null, null, true)."</div>\n";
	}

	$ret.="</td></tr>";
	return $ret;
}

function getFeeds($TourId) {
	$ret='<tr valign="top">';
	$ret.='<td colspan="2"><table cellpadding="0" cellspacing="0" border="0" width="100%">';

	// select all the phases available in this tournament...
	// Individual
	$q=safe_r_sql("SELECT distinct"
		. " FinEvent"
		. ", GrPhase"
		. ", EvEventName"
		. ", BsType "
		. "FROM"
		. " Finals"
		. " inner join Events on FinTournament=EvTournament and EvCode=FinEvent and EvTeamEvent=0"
		. " inner join Grids on FinMatchNo=GrMatchNo"
		. " left join BoinxSchedule on FinTournament=BsTournament and BsType=concat_ws('_', 'Fee', 'Ind', FinEvent, GrPhase) "
		. "where"
		. " FinTournament=$TourId "
		. "order by"
		. " EvProgr, GrPhase");
	$OldEvent='';
	$grid=array();
	$cols=0;
	while($r=safe_fetch($q)) {
		if($OldEvent!=$r->FinEvent) {
			$grid[$r->FinEvent]['title']=get_text($r->EvEventName, null, null, true);
			$grid[$r->FinEvent]['data']=array();
			$OldEvent=$r->FinEvent;
		}
		$grid[$r->FinEvent]['data'][]='<div class="button_'.($r->BsType ? 'on' : 'off').'" id="Fee_Ind_'.$r->FinEvent.'_'.$r->GrPhase.'" onclick="toggle(this)">'.get_text($r->GrPhase.'_Phase')."</div>\n";
		$cols=max($cols, count($grid[$r->FinEvent]['data']));
	}

	$ret.='<tr><th colspan="'.$cols.'">'.get_text('BracketsInd')."</th></tr>\n";
	$width='100%';
	if($cols) $width=round(100/$cols).'%';
	foreach($grid as $event => $item) {
		//$ret.='<tr><td colspan="'.$cols.'"><b>'.$item['title'].'</b></td></tr>';
		$ret.='<tr>' . str_repeat('<td width="'.$width.'">&nbsp;</td>', $cols-count($item['data']));
		$s=0;
		foreach(array_reverse($item['data']) as $v) {
			if(!$s++) $v=str_replace('onclick="toggle(this)">' , 'onclick="toggle(this)"><b>'.$item['title'].'</b>&nbsp;&nbsp;&nbsp;', $v);
			$ret.= '<td width="'.$width.'">' . $v . '</td>';
		}
		$ret.='</tr>';
	}
	$ret.='</table>';

	// Teams
	$q=safe_r_sql("SELECT distinct"
		. " TfEvent FinEvent"
		. ", GrPhase"
		. ", EvEventName"
		. ", BsType "
		. "FROM"
		. " TeamFinals"
		. " inner join Events on TfTournament=EvTournament and EvCode=TfEvent and EvTeamEvent=1"
		. " inner join Grids on TfMatchNo=GrMatchNo"
		. " left join BoinxSchedule on TfTournament=BsTournament and BsType=concat_ws('_', 'Fee', 'Team', TfEvent, GrPhase) "
		. "where"
		. " TfTournament=$TourId "
		. "order by"
		. " EvProgr, GrPhase");
	$OldEvent='';
	$grid=array();
	$cols=0;
	while($r=safe_fetch($q)) {
		if($OldEvent!=$r->FinEvent) {
			$grid[$r->FinEvent]['title']=get_text($r->EvEventName, null, null, true);
			$grid[$r->FinEvent]['data']=array();
			$OldEvent=$r->FinEvent;
		}
		$grid[$r->FinEvent]['data'][]='<div class="button_'.($r->BsType ? 'on' : 'off').'" id="Fee_Team_'.$r->FinEvent.'_'.$r->GrPhase.'" onclick="toggle(this)">'.get_text($r->GrPhase.'_Phase')."</div>\n";
		$cols=max($cols, count($grid[$r->FinEvent]['data']));
	}
	$ret.='</td><td colspan="2">';
	$ret.='<table cellspacing="0" cellpadding="2" border="0" width="100%">';
	$ret.='<tr><th colspan="'.$cols.'"><div class="title">'.get_text('BracketsSq')."</div></th></tr>\n";
	$width='100%';
	if($cols) $width=round(100/$cols).'%';
	foreach($grid as $event => $item) {
//		$ret.='<tr><td colspan="'.$cols.'"><b>'.$item['title'].'</b></td></tr>';
		$s=0;
		$ret.='<tr>'.str_repeat('<td width="'.$width.'">&nbsp;</td>', $cols-count($item['data']));
		foreach(array_reverse($item['data']) as $v) {
			if(!$s++) $v=str_replace('onclick="toggle(this)">' , 'onclick="toggle(this)"><b>'.$item['title'].'</b>&nbsp;&nbsp;&nbsp;', $v);
			$ret.= '<td width="'.$width.'">' . $v . '</td>';
		}
		$ret.='</tr>';
	}
	$ret.='</table>';

	$ret.="</td></tr>";
	return $ret;

}

function getRss($TourId) {
	$ret='<tr valign="top">';

	// select all the possible feeds...
	$ret.='<td><table cellpadding="0" cellspacing="0" border="0" width="100%">';
	// Qualifications IND (Div and Class)
	$ret.='<tr><th colspan="5">'.get_text('ResultIndClass','Tournament')."</th></tr>\n";
	$q=safe_r_sql("select distinct DivId, ClId, BsType, BsExtra, DivDescription, ClDescription from Entries inner join (select DivId, ClId, DivTournament, DivDescription, ClDescription, DivViewOrder,ClViewOrder from Divisions inner join Classes on DivTournament=ClTournament where ClAthlete and DivAthlete) DivClass on EnTournament=DivTournament and EnDivision=DivId and EnClass=ClId left join BoinxSchedule on DivTournament=BsTournament and BsType=concat_ws('_', 'Rss', 'Ind', DivId, ClId) where DivTournament=$TourId order by DivViewOrder,ClViewOrder");
	while($r=safe_fetch($q)) {
		$ret.='<tr>'
			. '<td><div class="button_'.($r->BsType ? 'on' : 'off').'" id="Rss_Ind_'.$r->DivId.'_'.$r->ClId.'" onclick="toggle(this)">'.get_text($r->DivDescription, '', '', true)." ".get_text($r->ClDescription, '', '', true)."</div></td>"
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='3' ? 'on' : 'off').'" id="Rss_Ind_'.$r->DivId.'_'.$r->ClId.'_$03$" onclick="toggle(this)">3</div></td>'
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='10' ? 'on' : 'off').'" id="Rss_Ind_'.$r->DivId.'_'.$r->ClId.'_$10$" onclick="toggle(this)">10</div></td>'
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='20' ? 'on' : 'off').'" id="Rss_Ind_'.$r->DivId.'_'.$r->ClId.'_$20$" onclick="toggle(this)">20</div></td>'
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='al' ? 'on' : 'off').'" id="Rss_Ind_'.$r->DivId.'_'.$r->ClId.'_$al$" onclick="toggle(this)">all</div></td>'
			. '</tr>';
	}

	$ret.='</table></td>';
	$ret.='<td><table cellpadding="0" cellspacing="0" border="0" width="100%">';
	$ret.='<tr><th colspan="5">'.get_text('ResultSqClass','Tournament')."</th></tr>\n";
	// Qualifications Team (Div and Class)
	$q=safe_r_sql("select distinct DivId, ClId, BsType, BsExtra, DivDescription, ClDescription from Teams inner join (select concat(DivId, ClId) DivCl, DivId, ClId, DivTournament, DivDescription, ClDescription, DivViewOrder,ClViewOrder from Divisions inner join Classes on DivTournament=ClTournament where ClAthlete and DivAthlete) DivClass on TeTournament=DivTournament and TeEvent=DivCl left join BoinxSchedule on DivTournament=BsTournament and BsType=concat_ws('_', 'Rss', 'Team', DivId, ClId) where DivTournament=$TourId order by DivViewOrder,ClViewOrder");
	while($r=safe_fetch($q)) {
		$ret.='<tr>'
			. '<td><div class="button_'.($r->BsType ? 'on' : 'off').'" id="Rss_Team_'.$r->DivId.'_'.$r->ClId.'" onclick="toggle(this)">'.get_text($r->DivDescription, '', '', true)." ".get_text($r->ClDescription, '', '', true)."</div>"
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='3' ? 'on' : 'off').'" id="Rss_Team_'.$r->DivId.'_'.$r->ClId.'_$03$" onclick="toggle(this)">3</div></td>'
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='10' ? 'on' : 'off').'" id="Rss_Team_'.$r->DivId.'_'.$r->ClId.'_$10$" onclick="toggle(this)">10</div></td>'
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='20' ? 'on' : 'off').'" id="Rss_Team_'.$r->DivId.'_'.$r->ClId.'_$20$" onclick="toggle(this)">20</div></td>'
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='al' ? 'on' : 'off').'" id="Rss_Team_'.$r->DivId.'_'.$r->ClId.'_$al$" onclick="toggle(this)">all</div></td>'
			. '</tr>';
	}

	$ret.='</table></td>';
	$ret.='<td><table cellpadding="0" cellspacing="0" border="0" width="100%">';
	$ret.='<tr><th colspan="5">'.get_text('IndFinal')."</th></tr>\n";
	// Finals Individual
	$q=safe_r_sql("Select distinct BsType, BsExtra, EvCode, EvEventName from Finals inner join Events on FinTournament=EvTournament and EvCode=FinEvent and EvTeamEvent=0 left join BoinxSchedule on FinTournament=BsTournament and BsType=concat_ws('_', 'Rss', 'Abs', EvCode) where FinTournament=$TourId order by EvProgr");
	while($r=safe_fetch($q)) {
		$ret.='<tr>'
			. '<td><div class="button_'.($r->BsType ? 'on' : 'off').'" id="Rss_Abs_'.$r->EvCode.'" onclick="toggle(this)">'.get_text($r->EvEventName, '', '', true)."</div>\n"
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='3' ? 'on' : 'off').'" id="Rss_Abs_'.$r->EvCode.'_$03$" onclick="toggle(this)">3</div></td>'
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='10' ? 'on' : 'off').'" id="Rss_Abs_'.$r->EvCode.'_$10$" onclick="toggle(this)">10</div></td>'
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='20' ? 'on' : 'off').'" id="Rss_Abs_'.$r->EvCode.'_$20$" onclick="toggle(this)">20</div></td>'
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='al' ? 'on' : 'off').'" id="Rss_Abs_'.$r->EvCode.'_$al$" onclick="toggle(this)">all</div></td>'
			. '</tr>';
	}

	$ret.='</table></td>';
	$ret.='<td><table cellpadding="0" cellspacing="0" border="0" width="100%">';
	$ret.='<tr><th colspan="5">'.get_text('TeamFinal')."</th></tr>\n";
	// Finals Teams
	$q=safe_r_sql("Select distinct BsType, BsExtra, EvCode, EvEventName from TeamFinals inner join Events on TfTournament=EvTournament and EvCode=TfEvent and EvTeamEvent=1 left join BoinxSchedule on TfTournament=BsTournament and BsType=concat_ws('_', 'Rss', 'AbsTeam', EvCode) where TfTournament=$TourId order by EvProgr");
	while($r=safe_fetch($q)) {
		$ret.='<tr>'
			. '<td><div class="button_'.($r->BsType ? 'on' : 'off').'" id="Rss_AbsTeam_'.$r->EvCode.'" onclick="toggle(this)">'.get_text($r->EvEventName, '', '', true)."</div>\n"
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='3' ? 'on' : 'off').'" id="Rss_AbsTeam_'.$r->EvCode.'_$03$" onclick="toggle(this)">3</div></td>'
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='10' ? 'on' : 'off').'" id="Rss_AbsTeam_'.$r->EvCode.'_$10$" onclick="toggle(this)">10</div></td>'
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='20' ? 'on' : 'off').'" id="Rss_AbsTeam_'.$r->EvCode.'_$20$" onclick="toggle(this)">20</div></td>'
			. '<td><div class="button_'.($r->BsType && $r->BsExtra=='al' ? 'on' : 'off').'" id="Rss_AbsTeam_'.$r->EvCode.'_$al$" onclick="toggle(this)">all</div></td>'
			. '</tr>';
	}
	$ret.='</table>';

	$ret.="</td></tr>";
	return $ret;

}


?>