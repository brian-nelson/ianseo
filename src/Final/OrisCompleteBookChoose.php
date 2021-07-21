<?php

/**
 *
 * I codici dei file sono:
 * IMG --> le immagini della gara
 * ENS --> Start list per piazzola
 * ENC --> Start list per società
 * ENA --> Start list per ordine alfabetico
 * IC --> Classifica di classe individuale
 * TC --> Classifica di classe a squadre
 * IQ(evento) --> Qualificazione individuale dell'evento (evento)
 * TQ(evento) --> Qualificazione a squadre dell'evento (evento)
 * IE(evento) --> Eliminatorie individuali dell'evento (evento)
 * IF(evento) --> Finale individuale dell'evento (evento) (Rank)
 * TF(evento) --> Finale a squadre dell'evento	(Rank)
 * IB(evento) --> Finale individuale dell'evento (evento) (Bracket)
 * TB(evento) --> Finale a squadre dell'evento	(evento) (Bracket)
 *
 * MEDSTD --> Medal standing
 * MEDLST --> Medal list
 */
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
CheckTourSession(true);
checkACL(array(AclIndividuals, AclTeams),AclReadOnly);

$MSG='';
$ORIS=$_SESSION['ISORIS'];

if($_POST) {
	//error_reporting(0);
// 	require_once('Common/OrisFunctions.php');
	require_once('Common/pdf/OrisPDF.inc.php');
	require_once('Common/pdf/OrisBracketPDF.inc.php');
	require_once('Common/Fun_FormatText.inc.php');

	// WE ONLY SEND ORIS STUFF
	$ORIS=!empty($_POST['oris']);
	//$ORIS=true;

// 	if(!defined('PRINTLANG')) {
// 		if($ORIS) {
// 			define('PRINTLANG', 'EN');
// 		} else {
// 			define('PRINTLANG', $_SESSION['TourPrintLang']);
// 		}
// 	}

	$isCompleteResultBook = true;

	$pdf = new OrisBracketPDF('', 'Complete Result Book');
	$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin);


	// Medal standing
	if(!empty($_POST['MEDSTD'])) {
		include 'OrisMedalStanding.php';
	}

	// Medallists
	if(!empty($_POST['MEDLST'])) {
		$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin);
		include 'OrisMedalList.php';
	}

	// List by Countries
	if(!empty($_POST['ENC'])) {
		include 'Partecipants/OrisCountry.php';
	}

	// List by targets
	if(!empty($_POST['ENS'])) {
		include 'Partecipants/OrisStartList.php';
	}

	// List by Entries
	if(!empty($_POST['ENA'])) {
		include 'Partecipants/OrisAlphabetical.php';
	}

	// Final Rank, Individual
	if(!empty($_POST['FinalInd'])) {
		$EventRequested=array();
		foreach($_POST['FinalInd'] as $Event) $EventRequested[]=substr($Event,2);
		include 'Final/Individual/OrisRanking.php';
	}

	// Brackets, Individual
	if(!empty($_POST['BracketsInd'])) {
		$EventRequested=array();
		foreach($_POST['BracketsInd'] as $Event) $EventRequested[]=substr($Event,2);
		include 'Final/Individual/OrisBracket.php';
		$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin);
	}

	// Elimination, Individual
	if(!empty($_POST['EliminationInd'])) {
		$EventRequested=array();
		foreach($_POST['EliminationInd'] as $Event) $EventRequested[]=substr($Event,2);
		include 'Elimination/OrisIndividual.php';
	}

	// Qualification, Individual
	if(!empty($_POST['QualificationInd'])) {
		$EventRequested=array();
		foreach($_POST['QualificationInd'] as $Event) $EventRequested[]=substr($Event,2);
		include 'Qualification/OrisIndividual.php';
	}

	// Ranking by Category, Individual (local rules apply)
	if(!empty($_POST['IC'])) {
		$pdf->AddPage();
		include 'Qualification/PrnIndividual.php';
	}

	// Ranking, Team
	if(!empty($_POST['FinalTeam'])) {
		$EventRequested=array();
		foreach($_POST['FinalTeam'] as $Event) $EventRequested[]=substr($Event,2);
		include 'Final/Team/OrisRanking.php';
	}

	// Brackets, Team
	if(!empty($_POST['BracketsTeam'])) {
		$EventRequested=array();
		foreach($_POST['BracketsTeam'] as $Event) $EventRequested[]=substr($Event,2);
		include 'Final/Team/OrisBracket.php';
		$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin);
	}

	// Qualification, Team
	if(!empty($_POST['QualificationTeam'])) {
		$EventRequested=array();
		foreach($_POST['QualificationTeam'] as $Event) $EventRequested[]=substr($Event,2);
		include 'Qualification/OrisTeam.php';
	}

	// Ranking by Category, Teams (local rules apply)
	if(!empty($_POST['TC'])) {
		include 'Qualification/PrnTeam.php';
	}

	$pdf->startPageGroup();
	// add a new page for TOC

	$pdf->setPrintFooter(false);
	$pdf->SetDataHeader(array(), array());
	$pdf->setEvent('Summary');
	$pdf->setComment('');
	$pdf->setOrisCode('', 'Summary');
	$pdf->setPhase('');

	$pdf->addTOCPage();

	if($_SESSION['ISORIS']) {
		$pdf->setY(40);
	}
	// write the TOC title
	$pdf->SetFont('times', 'B', 16);
	// $pdf->MultiCell(0, 0, 'Index', 0, 'C', 0, 1, '', '', true, 0);
	// 			$pdf->Ln();

	// disable existing columns
	$pdf->resetColumns();
	// set columns
	$pdf->setEqualColumns(2, ($pdf->getPageWidth()-25)/2);

	$pdf->SetFont('freesans', '', 9.5);

	// add a simple Table Of Content at first page
	// (check the example n. 59 for the HTML version)
	$pdf->addTOC(1, 'courier', '.', 'INDEX', 'B');

	// end of TOC page
	$pdf->endTOCPage();


	if(isset($_REQUEST['ToFitarco']))
	{
		$Dest='D';
		if (isset($_REQUEST['Dest']))
			$Dest=$_REQUEST['Dest'];
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
	}
	else
		$pdf->Output();
	die();
}

require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Various.inc.php');


// Seleziono la lista degli eventi
$outputIndAbs='';
$outputTeamAbs='';
$outputElim='';
$outputIndFin='';
$outputTeamFin='';
$outputIndBra='';
$outputTeamBra='';


// select the ACTUAL Individual Events
$Select = "SELECT distinct EvCode,EvEventName,EvTeamEvent,EvElim1,EvElim2,EvFinalFirstPhase 
    FROM Events 
    inner join Individuals on IndEvent=EvCode and IndTournament=EvTournament
    WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 
    ORDER BY EvProgr ";

$Rs=safe_r_sql($Select);
while ($MyRow=safe_fetch($Rs)) {
	$QualCode='IQ' . $MyRow->EvCode;
	$FinCode='IF' . $MyRow->EvCode;
	$BraCode='IB' . $MyRow->EvCode;

	if ($MyRow->EvElim1>0 || $MyRow->EvElim2>0) {
		$ElimCode='IE' . $MyRow->EvCode;
		$outputElim
			.='<input type="checkbox" name="EliminationInd[]" value="'.$ElimCode.'" id="' . $ElimCode . '"'
				.(empty($_POST['EliminationInd']) || !in_array($ElimCode, $_POST['EliminationInd']) ? '' : 'checked="checked"')
				. '>' . $MyRow->EvEventName . '<br/>' . "\n";
	}

	$outputIndAbs
		.='<input type="checkbox" name="QualificationInd[]" value="'.$QualCode.'" id="' . $QualCode . '"'
			.(empty($_POST['QualificationInd']) || !in_array($QualCode, $_POST['QualificationInd']) ? '' : 'checked="checked"')
			.'>' . $MyRow->EvEventName . '<br/>' . "\n";

	// solo chi ha la fase > 0 va avanti
	if(!$MyRow->EvFinalFirstPhase
		or in_array($MyRow->EvCode, $_SESSION['MenuElim1'])
		or in_array($MyRow->EvCode, $_SESSION['MenuElim2'])
		or in_array($MyRow->EvCode, $_SESSION['MenuFinI'])
		) continue;
	$outputIndFin
		.='<input type="checkbox" name="FinalInd[]" value="'.$FinCode.'" id="' . $FinCode . '"'
			.(empty($_POST['FinalInd']) || !in_array($FinCode, $_POST['FinalInd']) ? '' : 'checked="checked"')
			.'>' . $MyRow->EvEventName . '<br/>' . "\n";
	$outputIndBra
		.='<input type="checkbox" name="BracketsInd[]" value="'.$BraCode.'" id="' . $BraCode . '"'
			.(empty($_POST['BracketsInd']) || !in_array($BraCode, $_POST['BracketsInd']) ? '' : 'checked="checked"')
			.'>' . $MyRow->EvEventName . '<br/>' . "\n";
}

// select the ACTUAL Team Events
$Sql = "SELECT distinct EvCode, EvEventName, EvMixedTeam, EvTeamCreationMode,EvFinalFirstPhase 
  FROM Events 
  WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 
  ORDER BY EvProgr";
$RsEv=safe_r_sql($Sql);
while($MyRowEv=safe_fetch($RsEv)) {
	$Sql = "SELECT DISTINCT EcCode, EcTeamEvent, EcNumber 
        FROM EventClass 
        WHERE EcCode=" . StrSafe_DB($MyRowEv->EvCode) . " 
            AND EcTeamEvent!=0 
            AND EcTournament=" . StrSafe_DB($_SESSION['TourId']);
	$RsEc=safe_r_sql($Sql);
	if(safe_num_rows($RsEc)>0) {
		$RuleCnt=0;
		$Sql = "Select * ";
		while($MyRowEc=safe_fetch($RsEc)) {
			$ifc=ifSqlForCountry($MyRowEv->EvTeamCreationMode);
			$Sql .= (++$RuleCnt == 1 ? "FROM ": "INNER JOIN ");
			$Sql .= "(SELECT {$ifc} as C" . $RuleCnt . ", SUM(IF(EnSubTeam=0,1,0)) AS QuantiMulti
				  FROM Entries
				  INNER JOIN EventClass ON EnClass=EcClass AND EnDivision=EcDivision AND if(EcSubClass='', true, EnSubClass=EcSubClass) and EnTournament=EcTournament AND EcTeamEvent=" . $MyRowEc->EcTeamEvent . " AND EcCode=" . StrSafe_DB($MyRowEc->EcCode) . "
				  WHERE {$ifc}<>0 AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnTeam" . ($MyRowEv->EvMixedTeam ? 'Mix' : 'F') ."Event=1
				  group by {$ifc}, EnSubTeam
				  HAVING COUNT(EnId)>=" . $MyRowEc->EcNumber . ") as sqy";
			$Sql .= ($RuleCnt == 1 ? " ": $RuleCnt . " ON C1=C". $RuleCnt . " ");
		}
		$Sql .= " limit 1";

		$Rs=safe_r_sql($Sql);
		if(safe_num_rows($Rs)) {
			$QualCode='TQ' . $MyRowEv->EvCode;
			$FinCode='TF' . $MyRowEv->EvCode;
			$BraCode='TB' . $MyRowEv->EvCode;

			$outputTeamAbs
				.='<input type="checkbox" name="QualificationTeam[]" value="' . $QualCode . '" id="' . $QualCode . '"'
					.(empty($_POST['QualificationTeam']) || !in_array($QualCode, $_POST['QualificationTeam']) ? '' : 'checked="checked"')
					.'>' . $MyRowEv->EvEventName . '<br/>' . "\n";

			// solo chi ha la fase > 0 va avanti
			if(!$MyRowEv->EvFinalFirstPhase or in_array($MyRowEv->EvCode, $_SESSION['MenuFinT'])) continue;
			$outputTeamFin
				.='<input type="checkbox" name="FinalTeam[]" value="' . $FinCode . '" id="' . $FinCode . '"'
					.(empty($_POST['FinalTeam']) || !in_array($FinCode, $_POST['FinalTeam']) ? '' : 'checked="checked"')
					.'>' . $MyRowEv->EvEventName . '<br/>' . "\n";
			$outputTeamBra
				.='<input type="checkbox" name="BracketsTeam[]" value="' . $BraCode . '" id="' . $BraCode . '"'
					.(empty($_POST['BracketsTeam']) || !in_array($BraCode, $_POST['BracketsTeam']) ? '' : 'checked="checked"')
					.'>' . $MyRowEv->EvEventName . '<br/>' . "\n";
		}
	}
}

$JS_SCRIPT=array(
	phpVars2js(array(
		'StrInitProcess' => get_text('InitProcess', 'Tournament'),
		'StrOk' => get_text('CmdOk'),
		'StrError' => get_text('Error'),
		'StrCreateFiles' => get_text('CreateFiles', 'Tournament'),
		'StrMakingZip' => get_text('MakingZip', 'Tournament'),
		'StrMakingManifest' => get_text('MakingManifest', 'Tournament'),
		'StrNoCredential' => get_text('NoCredential', 'Tournament'),
		'StrSendData' => get_text('SendData', 'Tournament'),
		'StrErrorCode' => get_text('ErrorCode', 'Tournament'),
		'StrDeleting' => get_text('Deleting', 'Tournament'),
		'OnlineId' => (isset($_SESSION['OnlineId']) ? $_SESSION['OnlineId'] : 0),
		'RootDir' => $CFG->DOCUMENT_PATH,
		'WebDir' => $CFG->ROOT_DIR,
		'StrMsgAreYouSure' => get_text('MsgAreYouSure'),
		)),
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_JS.js"></script>',
	);

$PAGE_TITLE=get_text('CompleteResultBook');

include('Common/Templates/head.php');
?>
<form method="POST" target="PrintOut">
<div align="center">
	<div class="medium">
		<table class="Tabella">
			<tr>
				<th colspan="4"><?php print get_text('CompleteResultBook'); ?></th>
			</tr>

<!-- StartList -->
			<tr>
				<td class="Bold Left"><input name="oris" type="checkbox" id="oris" <?php echo $ORIS ? 'checked="checked"' : ''; ?> />&nbsp;<?php print get_text('StdORIS','Tournament') ?></td>
				<td class="Bold Center"><input type="checkbox" value="ENS" name="ENS" id="ENS" />&nbsp;<?php print get_text('StartlistSession','Tournament') ?></td>
				<td class="Bold Center"><input type="checkbox" value="ENC" name="ENC" id="ENC" />&nbsp;<?php echo get_text('StartlistCountry','Tournament') ?></td>
				<td class="Bold Right"><input type="checkbox" value="ENA"  name="ENA" id="ENA" />&nbsp;<?php echo get_text('StartlistAlpha','Tournament') ?></td>
			</tr>

			<tr>
				<th colspan="2" width="50%"><?php echo get_text('Individual'); ?></th>
				<th colspan="2" width="50%"><?php echo get_text('Team'); ?></th>
			</tr>

<!-- Classifica di classe -->
			<tr>
				<td colspan="2" class="Bold Center"><input type="checkbox" name="IC" id="IC" <?php echo empty($_POST['IC']) ? '' : 'checked="checked"'; ?>/>&nbsp;<?php print get_text('ResultClass','Tournament'); ?> - <?php print get_text('Individual'); ?></td>
				<td colspan="2" class="Bold Center"><input type="checkbox" name="TC" id="TC" <?php echo empty($_POST['TC']) ? '' : 'checked="checked"'; ?>/>&nbsp;<?php print get_text('ResultClass','Tournament'); ?> - <?php print get_text('Team'); ?></td>
			</tr>

<?php

if($outputIndAbs or $outputTeamAbs) {
	// divider
	echo '<tr class="Divider"><th colspan="4"></th></tr>';


	echo '<tr>';

	// Individual Qualifications
	echo '<td class="Bold Left">';
	echo $outputIndAbs ? '<input type="checkbox" id="allResultIndAbs" onclick="setAllCheck(\'QualificationInd[]\',this.id);">&nbsp;'.get_text('ResultIndAbs','Tournament') : '&nbsp;';
	echo '</td>';

	echo '<td class="Left">';
	echo $outputIndAbs ? $outputIndAbs : '&nbsp;';
	echo '</td>';

	// Team Qualifications
	echo '<td class="Bold Left">';
	echo $outputTeamAbs ? '<input type="checkbox" id="allResultTeamAbs" onclick="setAllCheck(\'QualificationTeam[]\',this.id);">&nbsp;'.get_text('ResultSqAbs','Tournament') : '&nbsp;';
	echo '</td>';

	echo '<td class="Left">';
	echo $outputTeamAbs ? $outputTeamAbs : '&nbsp;';
	echo '</td>';

	echo '</tr>';
}

// Eliminations (HF & 3D)
if($outputElim) {
	echo '<tr class="Divider"><th colspan="4"></th></tr>';
	echo '<tr>';
	echo '<td class="Bold Left">';
	echo '<input type="checkbox" id="allResultElim" onclick="setAllCheck(\'EliminationInd[]\',this.id);">&nbsp;';
	echo get_text('Elimination');
	echo '</td>';
	echo '<td class="Left">';
	echo $outputElim;
	echo '</td>';
	echo '<td colspan="2" class="Left">&nbsp;</td>';
	echo '</tr>';
}

// Brackets
if($outputIndBra or $outputTeamBra) {
	echo '<tr class="Divider"><th colspan="4"></th></tr>';
	echo '<tr>';

	// Individual brackets
	if($outputIndBra) {
		echo '<td class="Bold Left">'
			. '<input type="checkbox" id="allIndBra" onclick="setAllCheck(\'BracketsInd[]\',this.id);">&nbsp;'
			. get_text('Brackets') . ' - ' . get_text('Individual')
			. '</td>'
			. '<td class="Left">'
			. $outputIndBra
			. '</td>';
	} else {
		echo '<td colspan="2">&nbsp;</td>';
	}

	// Team brackets
	if($outputTeamBra) {
		echo '<td class="Bold Left">'
			. '<input type="checkbox" id="allTeamBra" onclick="setAllCheck(\'BracketsTeam[]\',this.id);">&nbsp;'
			. get_text('Brackets') . ' - ' . get_text('Team')
			. '</td>'
			. '<td class="Left">'
			. $outputTeamBra
			. '</td>';
	} else {
		echo '<td colspan="2">&nbsp;</td>';
	}

	echo '</tr>';
}

if($outputIndFin or $outputTeamFin) {
	// Final Rankings
	echo '<tr class="Divider"><th colspan="4"></th></tr>';
	echo '<tr>';

	// Individual rank
	if($outputIndFin) {
		echo '<td class="Bold Left">'
			. '<input type="checkbox" id="allIndFin" onclick="setAllCheck(\'FinalInd[]\',this.id);">&nbsp;'
			. get_text('Rankings') . ' - ' . get_text('Individual')
			. '</td>'
			. '<td class="Left">'
			. $outputIndFin
			. '</td>';
	} else {
		echo '<td colspan="2">&nbsp;</td>';
	}

	// Team Rank
	if($outputTeamFin) {
		echo '<td class="Bold Left">'
			. '<input type="checkbox" id="allTeamFin" onclick="setAllCheck(\'FinalTeam[]\',this.id);">&nbsp;'
			. get_text('Rankings') . ' - ' . get_text('Team')
			. '</td>'
			. '<td class="Left">'
			. $outputTeamFin
			. '</td>';
	} else {
		echo '<td colspan="2">&nbsp;</td>';
	}

	echo '</tr>';
}

?>

<!-- medal -->
			<tr class="Divider"><th colspan="4"></th></tr>
			<tr>
				<td colspan="2" class="Bold Center"><input type="checkbox" name="MEDSTD" id="MEDSTD">&nbsp;<?php print get_text('MedalStanding'); ?></td>
				<td colspan="2" class="Bold Center"><input type="checkbox" name="MEDLST" id="MEDLST">&nbsp;<?php print get_text('MedalList'); ?></td>
			</tr>

			<tr class="Divider"><th colspan="4"></th></tr>
			<tr><td class="Left" id="idStatus" colspan="4">&nbsp;</td></tr>
<?php
if (!IsBlocked(BIT_BLOCK_PUBBLICATION)) {
	echo '<tr><td colspan="4" class="Center"><input type="submit" id="btnOk" value="' . get_text('CmdOk') . '" onclick="document.getElementById(\'msg\').innerHTML=\'&nbsp;\'"></td></tr>';
}
?>
		</table>
	</div>
</div>
</form>
<?php
	include('Common/Templates/tail.php');


function getQueryResult($SQL, $BT='') {
	$ret=array();
	$q=safe_r_sql($SQL);
	while($r=safe_fetch($q)) $ret[]=$r;
	return $ret;
}

function getRankResult($type, $Event='') {
	$options=array('dist'=>0);
	if($Event) $options['events'] = $Event;
	if($type=='IC') {
		$rank=Obj_RankFactory::create('DivClass',$options);
		$rank->read();
		$rankData=$rank->getData();
	} elseif($type=='TC') {
		$rank=Obj_RankFactory::create('DivClassTeam',$options);
		$rank->read();
		$rankData=$rank->getData();
	} elseif($type=='IQ') {
		$rank=Obj_RankFactory::create('Abs',$options);
		$rank->read();
		$rankData=$rank->getData();
	} elseif($type=='TQ') {
		$rank=Obj_RankFactory::create('AbsTeam',$options);
		$rank->read();
		$rankData=$rank->getData();
	} elseif($type=='IF') {
		if($Event) $options['eventsR'] = $Event;
		$rank=Obj_RankFactory::create('FinalInd',$options);
		$rank->read();
		$rankData=$rank->getData();
	} elseif($type=='TF') {
		if($Event) $options['eventsR'] = $Event;
		$rank=Obj_RankFactory::create('FinalTeam',$options);
		$rank->read();
		$rankData=$rank->getData();
	}

	return $rankData;
}
?>
