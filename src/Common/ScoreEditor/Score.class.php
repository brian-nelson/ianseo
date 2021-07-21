<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Fun_FormatText.inc.php');

class Score {
	var $SelectQuery='';
	var $Filter='';
	var $TeamEvent=0;
	var $ONLOAD='';
	var $TourId=0;
	var $AllEvents=false;
	var $ViewComboModes=0;
	var $PAGE_TITLE='';
	var $Event='';
	var $Match=0;
	var $Javascripts=array('Common/ScoreEditor/WriteScoreCard.js');
	var $PhpVars2JS=array();
	var $SpottingModes=array();
	var $EnableDanageDisplay=true;
	var $Ajax=array(
			'PhpChangeEvent'=>'Common/ScoreEditor/AjaxGetPhase.php',
			'PhpChangePhase'=>'Common/ScoreEditor/AjaxGetMatch.php',
			'PhpChangeMatch'=>'Common/ScoreEditor/AjaxGetScore.php',
			'PhpUpdateCard' =>'Common/ScoreEditor/AjaxUpdateCard.php',
			'PhpUpdateLive' =>'Common/ScoreEditor/AjaxUpdateLive.php',
			'PhpUpdateComm' =>'Common/ScoreEditor/AjaxUpdateComm.php',
			'PhpMovePhase'  =>'Common/ScoreEditor/AjaxMovePhase.php',
			);

	function __construct() {
		global $CFG;
		CheckTourSession(true);
		$this->TourId=$_SESSION['TourId'];
		$this->PAGE_TITLE=get_text('IndFinal');
		$this->SpottingModes[0]=get_text('MenuLM_Arrow by Arrow (Scorecards)');
		$this->SpottingModes[1]=get_text('MenuLM_Spotting');
		$this->SpottingModes[2]=get_text('Review');
	}

	function MatchScore() {
		global $CFG;

		foreach($this->Ajax as $k => $f) {
			$this->PhpVars2JS[$k]=$CFG->ROOT_DIR.$f;
		}

		// combo degli eventi
		$comboEvents ='<select name="d_Event" id="d_Event" onChange="ChangeEvent(' . $this->TeamEvent . ',null,true);">' . "\n"
				. '<option value="">----</option>' . "\n";
		$Select = "SELECT EvCode,EvEventName
			FROM Events
			WHERE EvTournament=$this->TourId
				AND EvTeamEvent=$this->TeamEvent
				".($this->AllEvents ? "" : "AND EvFinalFirstPhase!=0")."
			ORDER BY EvProgr ASC ";
		$Rs=safe_r_sql($Select);
		while ($Row=safe_fetch($Rs)) {
			$comboEvents.='<option value="' . $Row->EvCode . '"' . ($Row->EvCode==$this->Event ? ' selected' : ''). '>' . $Row->EvCode . ' - ' . get_text($Row->EvEventName,'','',true) . '</option>' . "\n";
		}
		$comboEvents.='</select>' . "\n";

		$comboModes = '<select name="d_Modes" id="d_Modes">';
		foreach($this->SpottingModes as $k => $v) {
			$comboModes .= '<option selected value="'.$k.'">' . $v . '</option>';
		}
		$comboModes .= '</select>';

		$PAGE_TITLE=$this->PAGE_TITLE;
		$ONLOAD=$this->ONLOAD;
		$JS_SCRIPT=array(phpVars2js($this->PhpVars2JS));

		include('Common/Templates/head' . (isset($_REQUEST["hideMenu"]) ? '-min': '') . '.php');

		echo '<form name="FrmParam" method="POST" action="">';
		echo '<table class="Tabella">';
		echo '<tr onClick="showOptions();"><th class="Title" colspan="4">'.$this->PAGE_TITLE.'</th></tr>';
		echo '<tr class="Divider"><td colspan="4"></td></tr>';
		echo '<tbody id="options">';
		echo '<tr>
				<th width="30%">'.get_text('Event').'</th>
				<th width="10%">'.get_text('Phase').'</th>
				<th width="50%">'.get_text('MatchNo').'</th>
				<th width="10%">&nbsp;</th>
			</tr>';
		echo '<tr>
				<td class="Center" width="30%">'.$comboEvents.'</td>
				<td class="Center" width="10%"><select name="d_Phase" id="d_Phase" onchange="ChangePhase('.$this->TeamEvent.');"></select></td>
				<td class="Center" width="50%"><select name="d_Match" id="d_Match">'.($this->Match ? '<option value="'.$this->Match.'" selected="selected"></option>' : '').'></select></td>
				<td rowspan="3" class="Center" width="10%"><input type="button" value="'.get_text('CmdOk').'" onclick="makeScore('.$this->TeamEvent.');">
					<br><input type="checkbox" id="showMenu" ' . (isset($_REQUEST["hideMenu"]) ? '' : 'checked') .
						' onClick="document.location=\'' . $_SERVER["PHP_SELF"]. '?Team=' . $this->TeamEvent . (isset($_REQUEST["hideMenu"]) ? '' : '&hideMenu') . '\';"' .
						'>&nbsp;' . get_text('ShowIanseoMenu', 'Tournament') .'</td>
			</tr>';
		echo '<tr class="Divider"><td colspan="3"></td></tr>';
		echo '<tr>
				<td colspan="2" class="Bold">';
		if($this->Ajax['PhpMovePhase']) echo '<input type="button" id="buttonMove2Next" value="'.get_text('MoveWinner2NextPhase','Tournament').'" onclick="move2nextPhase(document.getElementById(\'d_Event\').value,document.getElementById(\'d_Match\').value,'.$this->TeamEvent.');"/>';
		echo '</td>
				<td class="Center">'.$comboModes.'</td>
			</tr>';
		echo '<tr><td colspan="4">';
		if($this->EnableDanageDisplay and file_exists($CFG->DOCUMENT_PATH . 'Modules/DanageDisplay/interface.php')) {
			require_once ('Modules/DanageDisplay/interface.php');
		}
		echo '</td></tr>';
		echo '<tr class="Divider"><td colspan="4"></td></tr>';
		echo '</tbody>';
		echo '</table>';
		echo '</form>';
		echo '<div id="outputChunk"></div>';

		if(!empty($GoBack)) {
			echo '<table class="Tabella2" width="50%"><tr><th style="background-color:red"><a href="'.$GoBack.'" style="color:white">'.get_text('BackBarCodeCheck','Tournament').'</a></th></tr></table>';
		}

		echo '<div id="idOutput"></div>';

		echo '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>';
		echo '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>';
		foreach($this->Javascripts as $file) {
			echo '<script type="text/javascript" src="'.$CFG->ROOT_DIR.$file.'"></script>';
		}

		if($this->EnableDanageDisplay and file_exists($CFG->DOCUMENT_PATH . 'Modules/DanageDisplay/Fun_Display.js')) {
			echo '<script type="text/javascript" src="' . $CFG->ROOT_DIR . 'Modules/DanageDisplay/Fun_Display.js"></script>';
		}

		include('Common/Templates/tail' . (isset($_REQUEST["hideMenu"]) ? '-min' : '') . '.php');
	}
}
