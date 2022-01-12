<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Lib/CommonLib.php');

if(!empty($_SESSION['TourId']) AND $_SESSION['TourId']!=-1) {
	$TourId=$_SESSION['TourId'];
} else {
	$TourId=getIdFromCode(GetIsParameter('IsCode'));
    CreateTourSession($TourId);
}
checkACL(AclOutput,AclReadOnly, true, $TourId);

$MatchNo=(!isset($_REQUEST['d_Match']) ? -1 : intval($_REQUEST['d_Match']));
$Event=(empty($_REQUEST['d_Event']) ? '' : preg_replace('/[^a-z0-9_-]/sim','', $_REQUEST['d_Event'] ));
$Phase=(!isset($_REQUEST['d_Phase']) ? -1 : intval($_REQUEST['d_Phase']));
$Team=-1;
if($Event) list($Team, $Event)=explode('-', $Event, 2);

$Lock = ($Event && $MatchNo>=0 && $Team>=0) ? '1' : '0';

if(!$Lock and $x=getMatchLive($TourId)) {
	$Event=$x->Event;
	$MatchNo=$x->MatchNo;
	$Team=$x->Team;
}

$JS_SCRIPT=array(
	phpVars2js(array('Event'=>$Event, 'MatchNo'=>$MatchNo, 'Team'=>$Team,'Lock'=>$Lock,'WebDir'=>$CFG->ROOT_DIR, 'Phase'=>$Phase, 'TourId'=>$TourId)).
	'<link href="index.css" rel="stylesheet" type="text/css">',
	'<script type="text/javascript" src="index.js"></script>',
);

include('Common/Templates/head-BS.php');
?>

<div class="card">
	<div class="card-header text-center">
        <div class="d-flex justify-content-center align-items-center">
            <div id="OppLeftContainer">
                <div id="OppLeft">Opponent Left</div>
            </div>
            <div id="EventContainer">
                <div id="Event">Event<br/>Phase</div>
            </div>
            <div id="OppRightContainer">
                <div id="OppRight">Opponent Right</div>
            </div>
        </div>
	</div>
	<div class="card-body p-0">
		<table id="MainTable" class="w-100">
			<tr class="text-center align-top" >
                <td id="TgtLeft" class="w-45"><svg class="SVGTarget"></svg></td>
                <td rowspan="2" class="w-10" id="spotMenu">
                    <div class="btn-group-vertical btn-group mt-2" role="group">
                        <button type="button" class="btn btn-info btnViewMenu" id="btnPresentation" onclick="setView('Presentation')">[Presentation]</button>
                        <button type="button" class="btn btn-info btnViewMenu" id="btnBiography" onclick="setView('Biography')">[Biography]</button>
                        <button type="button" class="btn btn-info btnViewMenu" id="btnScorecard" onclick="setView('Scorecard')">[Scorecard]</button>
                        <button type="button" class="btn btn-info btnViewMenu" id="btnTarget" onclick="setView('Target')">[Target]</button>
                    </div>
                    <div class="btn-group-vertical btn-group mt-2 hidden" id="btnCeremony" role="group">
                        <button type="button" class="btn btn-info btnViewMenu" onclick="setView('Ceremony')">[Ceremony]</button>
                    </div>
                    <div class="btn-group-vertical btn-group mt-3" role="group">
                        <button type="button" class="btn btn-info btnViewMenu" id="bntSelectMatch" onclick="selectMatch()">[Select Match]</button>
                        <button type="button" class="btn btn-danger btnViewMenu" id="bntGoToLive" onclick="goToLive()">[Live Match]</button>
                    </div>

                    <div class="btn-group-vertical btn-group mt-3" role="group" id="EndSelector"></div>

                </td>
				<td id="TgtRight" class="w-45"><svg class="SVGTarget"></svg></td>
			</tr>
			<tr class="text-center">
				<td id="ScoreLeft"><div class="badge badge-info">ScoreLeft</div></td>
				<td id="ScoreRight"><div class="badge badge-info">ScoreRight</div></td>
			</tr>
		</table>
	</div>
</div>

<?php
echo '<div class="modal" tabindex="-1" role="dialog" id="SelectMatch">'.
    '<div class="modal-dialog" role="document">'.
        '<div class="modal-content">'.
            '<div class="modal-header">'.
                '<h5 class="modal-title">'.get_text('ScheduledMatches', 'Tournament').'</h5>.'.
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">'.
                    '<span aria-hidden="true">&times;</span>'.
                '</button>'.
            '</div>'.
            '<div class="modal-body">'.


                '<div class="form-group">'.
                    '<label for="selectSession">' . get_text('SelectSession', 'Tournament') . '</label>'.
                    '<select class="form-control" id="selectSession" onchange="updateComboSessionMatches();"></select>'.
                '</div>'.
                '<div class="list-group" id="selectSessionMatch">'.
                '</div>'.
                '<hr>'.
                '<div class="form-group">'.
                    '<label for="selectEvent">' . get_text('Event') . '</label>'.
                    '<select class="form-control" id="selectEvent" onchange="updateComboPhases();"></select>'.
                '</div>'.
                '<div class="form-group">'.
                    '<label for="selectPhase">' . get_text('Phase') . '</label>'.
                    '<select class="form-control" id="selectPhase" onchange="updateComboMatches();"></select>'.
                '</div>'.
                '<div class="form-group">'.
                    '<label for="selectMatch">' . get_text('Match', 'Tournament') . '</label>'.
                    '<select class="form-control" id="selectMatch"></select>'.
                '</div>'.
            '</div>'.
            '<div class="modal-footer">'.
                '<button type="button" class="btn btn-primary" onclick="goToMatch()">'.get_text('CmdGo', 'Tournament').'</button>'.
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">'.get_text('Close').'</button>'.
            '</div>'.
        '</div>'.
    '</div>'.
'</div>';

include('Common/Templates/tail-html.php');
