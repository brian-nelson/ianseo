<?php
/*
	Viene incluso il motore ajax di index per sfruttare UpdateField
*/
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
checkACL(AclParticipants, AclReadWrite);
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/CommonLib.php');

$IrmSelector='<select onchange="setIRM(this)">';
$q=safe_r_sql("select * from IrmTypes order by IrmId");
while($r=safe_fetch($q)) {
    $IrmSelector.='<option value="'.$r->IrmId.'">'.$r->IrmType.($r->IrmType ? ' - '.get_text('IRM-'.$r->IrmId, 'Tournament') : '').'</option>';
}
$IrmSelector.='</select>';

$ByeSelector='<select onchange="setBye(this)"><option value="0">' . get_text('NoTie', 'Tournament') . '</option><option value="1" disabled="disabled">' . get_text('TieWinner', 'Tournament') . '</option><option value="2">' . get_text('Bye') . '</option></select>';
$EditRankIcon='<i class="fa fa-pencil" onclick="editIRM(this)"></i>';

$JS_SCRIPT=array(
    phpVars2js(array(
	    'IrmSelector'=>$IrmSelector,
	    'ByeSelector'=>$ByeSelector,
        'EditRankIcon'=>$EditRankIcon,
	    'strOk'=>get_text('CmdOk'),
	    'strCancel'=>get_text('CmdCancel'),
	    'strConfirm'=>get_text('ConfirmIrmMsg', 'Tournament'),
	    'strConfirmBye'=>get_text('ConfirmByeMsg', 'Tournament'),
	    'QuDeranking' => $CFG->DERANKING,
	    'QuDisqualify' => $CFG->DISQUALIFIED,
	    'QuDidnotstart' => $CFG->DIDNOTSTART,
	    'QuDidnotfinish' => $CFG->DIDNOTFINISH,
	    'ReqTeam' => isset($_REQUEST['team']) ? intval($_REQUEST['team']) : 0,
	    'ReqEvent' => isset($_REQUEST['event']) ? $_REQUEST['event'] : '',
	    'ReqPhase' => isset($_REQUEST['phase']) ? $_REQUEST['phase'] : '',
    )),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-confirm.min.js"></script>',
	'<link href="'.$CFG->ROOT_DIR.'Common/css/jquery-confirm.min.css" media="screen" rel="stylesheet" type="text/css">',
    '<script type="text/javascript" src="./ManIrmStatus.js"></script>',
    '<style>
		.disabled {background-color: yellow;}
		.hide {display: none;}
		.BorderBottom td {border-bottom:1.5px solid;}
		.BorderTop td {border-top:1.5px solid;}
		.Bye-2 td {background-color:#ddd;}
		.p-2 {padding:0.5em!important;}
	</style>',
    );

$PAGE_TITLE=get_text('MenuLM_IrmManagement');
$IncludeFA=true;

$Order=empty($_REQUEST['Order']) ? '' : $_REQUEST['Order'];

include('Common/Templates/head.php');

echo '<table class="Tabella" style="width:auto;margin: auto;">';
echo '<tr><th class="Title" colspan="13">'. get_text('MenuLM_IrmManagement') .'</th></tr>';
echo '<tr class="Divider"><td colspan="13"></td></tr>';

// Event/Phase selector
echo '<tr><td colspan="13">
    <div style="display:flex;justify-content: space-around;flex-wrap: wrap">
        <select id="TeamSelector" onchange="selectEvent(this)"><option value="0">'.get_text('Individual').'</option><option value="1">'.get_text('Team').'</option></select>
        <select id="EventsSelector" onchange="SelectPhase(this)"></select>
        <select id="PhaseSelector" onchange="ShowItems(this)"></select>
        <div id="SearchBox">'.get_text('Search', 'Tournament').' <input type="text" id="SearchText" onchange="ShowItems()"></div>
    </div>
    </td></tr>';
echo '<tr class="Divider"><td colspan="13"></td></tr>';

echo '<tr>';
print '<td class="Title" active="" type="ASC" ord="Code" onclick="setOrder(this)">' . get_text('Code','Tournament') . '</td>'
	. '<td class="Title" active="" type="ASC" ord="Archer" onclick="setOrder(this)">' . get_text('Archer') . '</td>'
	. '<td class="Title" active="" type="ASC" ord="CountryCode" onclick="setOrder(this)">' . get_text('CountryCode') . '</td>'
	. '<td class="Title" active="" type="ASC" ord="Country" onclick="setOrder(this)">' . get_text('Country') . '</td>'
	. '<td class="Title" active="1" type="DESC" ord="Score" onclick="setOrder(this)">' . get_text('Score', 'Tournament') . '</td>'
	. '<td class="Title" active="" type="ASC" ord="Records">' . get_text('Notes', 'Tournament') . '&nbsp;<i class="fa fa-question-circle text-info fa-lg" onclick="showHelp(\'NotesHelp\')"></i></td>'
	. '<td class="Title" active="" type="ASC" ord="IrmStatus" onclick="setOrder(this)" ref="IrmSelector">' . get_text('IrmStatus', 'Tournament') . '&nbsp;<i class="fa fa-question-circle text-warning fa-lg" onclick="showHelp(\'IrmHelp\')"></i></td>'
	. '<td class="Title" active="" type="ASC" ord="Bye" onclick="setOrder(this)" ref="Bye">' . get_text('Bye') . '</td>'
	. '<td class="Title" active="" type="ASC" ord="QualRank" onclick="setOrder(this)" ref="QualRank">' . get_text('RankScoreShort') . '</td>'
	. '<td class="Title" active="" type="ASC" ord="SubClassRank" onclick="setOrder(this)" ref="SubClassRank">' . get_text('SubClassRank', 'Tournament') . '</td>'
	. '<td class="Title" active="" type="ASC" ord="FinRank" onclick="setOrder(this)" ref="FinRank">' . get_text('FinalRank', 'Tournament') . '</td>'
	. '<td class="Title"></td>';
print '</tr>';

// Search boxes
echo '<tbody id="ResultsTable"></tbody>';

$tmp=array();
foreach(array('OR','WR','CR','GR','NR','PB','SB') as $rec) {
	$tmp[]='<b>'.$rec.'</b> ('.get_text($rec.'-Record','Tournament').')';
}
echo '<tr><th colspan="11" class="Left p-2" id="NotesHelp"><i class="fa fa-question-circle text-info fa-lg"></i>&nbsp;'.get_text('NotesHelp','ODF', implode(', ', $tmp)).'</th></tr>';
echo '<tr><th colspan="11" class="Left p-2" id="IrmHelp"><i class="fa fa-question-circle text-warning fa-lg"></i>&nbsp;'.get_text('IrmHelp','ODF').'</th></tr>';

echo '</table>';

include('Common/Templates/tail.php');
