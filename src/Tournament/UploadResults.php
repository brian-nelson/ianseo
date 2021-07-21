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
require_once('Common/Lib/Fun_Phases.inc.php');

if(!CheckTourSession() or IsBlocked(BIT_BLOCK_PUBBLICATION)) {
	PrintCrackError(false,'LockedProcedure', 'Errors');
}

checkACL(AclInternetPublish, AclReadWrite);
$URL=$CFG->IanseoServer.'Upload-Competition.php';

if(empty($_SESSION['OnlineId']) or empty($_SESSION['OnlineAuth']) or empty($_SESSION['OnlineServices']) or !($_SESSION['OnlineServices']&1) or empty($_SESSION['OnlineEventCode'])) {
    // check if the credentials have been entered already
	$return='Tournament/'.basename(__FILE__);
	if($Credentials=getModuleParameter('SendToIanseo', 'Credentials', (object) array('OnlineId' => '', 'OnlineAuth' => '')) AND !empty($Credentials->OnlineId)) {
		require_once('Common/Lib/CommonLib.php');
		if($ErrorMessage=CheckCredentials($Credentials->OnlineId, $Credentials->OnlineAuth, $return)) {
			safe_error($ErrorMessage);
		} else {
			cd_redirect($CFG->ROOT_DIR . $return);
		}
	} else {
		cd_redirect('SetCredentials.php?return='.$return);
	}
}

$MSG='';
$ORIS=$_SESSION['ISORIS'];
$q=safe_r_SQL("SELECT * FROM `TourRecords` WHERE `TrTournament` = " . StrSafe_DB($_SESSION['TourId']));
$RECORDS = (safe_num_rows($q) > 0);

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
$Scores='';
$Elim1=0;
$Elim2=0;
$Elim3=0;
$Elim4=0;
$ShowMedals=false;

// Scorecards of qualifications
$Scores.='<div><input type="checkbox" name="ScoQual" class="removeAfterUpload"/>'.get_text('ScorecardsQual','Tournament').'</div>';

// select the ACTUAL Individual Events
$Select = "SELECT distinct EvCode, EvEventName, EvTeamEvent, EvElim1, EvElim2, EvFinalFirstPhase, EvElimType, EvMedals, ifnull(i2.IndId,i3.IndId) as HasMedal,  (i2.IndId is NOT NULL) as HasGoldMedal, EvShootOff
    FROM Events
    inner join Individuals i1 on i1.IndTournament=EvTournament and i1.IndEvent=EvCode
    left join Individuals i2 on i2.IndTournament=EvTournament and i2.IndEvent=EvCode and i2.IndRankFinal=1
    left join Individuals i3 on i3.IndTournament=EvTournament and i3.IndEvent=EvCode and i3.IndRankFinal=3
    WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0
    ORDER BY EvProgr ";

$Rs=safe_r_sql($Select);

// Results book is showable only if it is an ORIS event and there is at least one event.
$ShowFinalBook=($_SESSION['ISORIS'] and safe_num_rows($Rs));

while ($MyRow=safe_fetch($Rs)) {
	if($MyRow->EvMedals and $MyRow->HasMedal) {
	    $ShowMedals=true;
    }
	if($MyRow->EvFinalFirstPhase and !$MyRow->HasGoldMedal) {
	    $ShowFinalBook=false;
	}

    $QualCode='IQ' . $MyRow->EvCode;
    // qualifications is for all...
    $outputIndAbs .='<input type="checkbox" name="QualificationInd[]" value="'.$QualCode.'">' . $MyRow->EvCode . '&nbsp;-&nbsp;' . $MyRow->EvEventName . '<br/>';

    // Field/3D eliminations and Pools...
    switch($MyRow->EvElimType) {
        case 0:
            // do nothing
            break;
        case 3:
        case 4:
            // Pools
			if($MyRow->EvShootOff) {
	            ${'Elim'.$MyRow->EvElimType}=1;
	            $ElimCode='IP' . $MyRow->EvCode.$MyRow->EvElimType;
	            $outputElim .='<input type="checkbox" name="EliminationInd[]" value="'.$ElimCode.'">' . $MyRow->EvCode . '&nbsp;-&nbsp;' . $MyRow->EvEventName . '<br/>';
			}
            break;
        default:
            if ($MyRow->EvElim1>0 || $MyRow->EvElim2>0) {
                if(!$Elim1) {
                    $Elim1=1;
                }
                if($MyRow->EvElim2) {
                    $Elim2=1;
                }
                $ElimCode='IE' . $MyRow->EvCode;
                $outputElim .='<input type="checkbox" name="EliminationInd[]" value="'.$ElimCode.'">' . $MyRow->EvCode . '&nbsp;-&nbsp;' . $MyRow->EvEventName . '<br/>';
            }
    }

	// based on the SO status we build brackets and Final Ranks
    if($MyRow->EvShootOff) {
        $BraCode='IB' . $MyRow->EvCode;
        $FinCode='IF' . $MyRow->EvCode;
        $outputIndFin .='<input type="checkbox" name="FinalInd[]" value="'.$FinCode.'">' . $MyRow->EvCode . '&nbsp;-&nbsp;' . $MyRow->EvEventName . '<br/>';
        $outputIndBra .='<input type="checkbox" name="BracketsInd[]" value="'.$BraCode.'">' . $MyRow->EvCode . '&nbsp;-&nbsp;' . $MyRow->EvEventName . '<br/>';
    }
}

// select the ACTUAL Team Events
$Sql = "SELECT distinct EvCode, EvEventName, EvFinalFirstPhase, EvMedals, ifnull(t2.TeCoId,t3.TeCoId) as HasMedal, (t2.TeCoId is NOT NULL) as HasGoldMedal, EvShootOff
    FROM Events 
    inner join Teams t1 on t1.TeEvent=EvCode and t1.TeTournament=EvTournament
    left join Teams t2 on t2.TeEvent=EvCode and t2.TeTournament=EvTournament and t2.TeRankFinal=1
    left join Teams t3 on t3.TeEvent=EvCode and t3.TeTournament=EvTournament and t3.TeRankFinal=3
    WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 ORDER BY EvProgr";

$RsEv=safe_r_sql($Sql);
while($MyRowEv=safe_fetch($RsEv)) {
	if($MyRowEv->EvMedals and $MyRowEv->HasMedal) {
		$ShowMedals=true;
	}
	if($MyRowEv->EvFinalFirstPhase and !$MyRowEv->HasGoldMedal) {
		$ShowFinalBook=false;
	}

    $QualCode='TQ' . $MyRowEv->EvCode;
    $FinCode='TF' . $MyRowEv->EvCode;
    $BraCode='TB' . $MyRowEv->EvCode;

    $outputTeamAbs .='<input type="checkbox" name="QualificationTeam[]" value="' . $QualCode . '">' . $MyRowEv->EvCode . '&nbsp;-&nbsp;' . $MyRowEv->EvEventName . '<br/>';

    // solo chi ha la fase > 0 va avanti
    if(!$MyRowEv->EvFinalFirstPhase or in_array($MyRowEv->EvCode, $_SESSION['MenuFinT'])) {
        continue;
    }
    $outputTeamFin .='<input type="checkbox" name="FinalTeam[]" value="' . $FinCode . '">' . $MyRowEv->EvCode . '&nbsp;-&nbsp;' . $MyRowEv->EvEventName . '<br/>';
    $outputTeamBra .='<input type="checkbox" name="BracketsTeam[]" value="' . $BraCode . '">' . $MyRowEv->EvCode . '&nbsp;-&nbsp;' . $MyRowEv->EvEventName . '<br/>';
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
        'StrOrder' => get_text('Order', 'Tournament'),
        'StrDescription' => get_text('Descr','Tournament'),
        'StrDelete' => get_text('CmdDelete','Tournament'),
        'StrUrl' => get_text('URL','Tournament'),
        )),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-confirm.min.js"></script>',
    '<script type="text/javascript" src="./UploadResults.js"></script>',
    '<link type="text/css" rel="stylesheet" href="./UploadResults.css" />',
    '<link type="text/css" rel="stylesheet" href="'.$CFG->ROOT_DIR.'Common/css/font-awesome.css" />',
    );

$PAGE_TITLE=get_text('Send2Ianseo','Tournament');

include('Common/Templates/head.php');

//echo '<div align="center">';
//echo '<div class="medium">';
echo '<form id="uploads" enctype="multipart/form-data">';
echo '<table class="Tabella freeWidth">';
echo '<tr><th class="Title" colspan="4">'.get_text('Send2Ianseo','Tournament').'</th></tr>';

// Delete/Show records/Oris line
echo '<tr>
    <td class="Left Deletable"><input type="checkbox" onclick="toggleDeleteColor(this)" name="btnDelOnline" id="btnDelOnline" class="removeAfterUpload">'.get_text('CmdDeleteOnline','Tournament').'</td>
    <td colspan="2" class="Center"><b id="msg"></b></td>
    <td class="Right">
        <span>'.get_text('showRecords','Tournament').'<input name="showRecords" type="checkbox" id="showRecords" '.($RECORDS ? 'checked="checked"' : '').' /></span>
        <span style="margin-left:2em;">'.get_text('StdORIS','Tournament').'<input name="oris" type="checkbox" id="oris" '.($ORIS ? 'checked="checked"' : '').' onclick="toggleOris()"/></span>
    </td>
    </tr>';
echo '<tr class="Divider"><th colspan="4"></th></tr>';

// Stats
// first run one must send the headings
echo '<tr>
    <td class="Bold Deletable"><input type="checkbox" value="STE" name="STE" class="removeAfterUpload" />'.get_text('StatEvents','Tournament').'</td>
    <td class="Bold Center" colspan="2"><input type="checkbox" value="IMG" name="IMG" class="removeAfterUpload" '.(empty($_SESSION['SendOnlinePDFImages']) ? '' : 'checked="checked" onclick="this.checked=true"').' />'.get_text('SendLogos','Tournament').'</td>
    <td class="Bold Deletable"><input type="checkbox" value="STC" name="STC" class="removeAfterUpload" />'.get_text('StatCountries','Tournament').'</td>
    </tr>';

// startlists
echo '<tr>
    <td class="Bold Deletable"><input type="checkbox" value="ENE" name="ENE"  class="removeAfterUpload" />'.get_text('StartlistTeam','Tournament').'</td>
    <td class="Bold Deletable"><input type="checkbox" value="ENS" name="ENS"  class="removeAfterUpload" />'.get_text('StartlistSession','Tournament').'</td>
    <td class="Bold Deletable InBook"><input type="checkbox" value="ENC" name="ENC"  class="removeAfterUpload" />'.get_text('StartlistCountry','Tournament').'</td>
    <td class="Bold Deletable"><input type="checkbox" value="ENA"  name="ENA"  class="removeAfterUpload" />'.get_text('StartlistAlpha','Tournament').'</td>
    </tr>';

echo '<tr class="OrisHide">
    <th colspan="2" width="50%">'.get_text('Individual').'</th>
    <th colspan="2" width="50%">'.get_text('Team').'</th>
    </tr>';

// Division and Class
echo '<tr class="OrisHide">'.
        '<td colspan="2" class="Bold Deletable"><input type="checkbox" name="IC" id="IC"/>'.get_text('ResultClass','Tournament').
        (getModuleParameter('ISK','CalcClDivInd',0, 0, true) == 1 ? '<br><span class="text-danger">'.get_text('RkCalcOffWarning', 'ISK').'</span>' : '').
        '</td>'.
        '<td colspan="2" class="Bold Deletable"><input type="checkbox" name="TC" id="TC"/>'. get_text('ResultClass','Tournament').
        (getModuleParameter('ISK','CalcClDivTeam',0, 0, true) == 1 ? '<br><span class="text-danger">'.get_text('RkCalcOffWarning', 'ISK').'</span>' : '').
        '</td>'.
    '</tr>';
echo '<tr class="Divider"><td colspan="4" class="Title"></td></tr>';

//echo '<tr class="Divider"><th colspan="4"></th></tr>';

// Qualifications (Events)
if(isset($_REQUEST['QUAL'])) {
    $cl='';
    $st='on';
    $ic='down';
} else {
    $cl='hidden';
    $st='off';
    $ic='right';
}
echo '<tr class="tit_Abs'.(($outputIndAbs or $outputTeamAbs) ? '' : ' hidden').'"><th colspan="4" class="AccordionToggle" ref="QUAL" status="'.$st.'" onclick="toggleAccordion(this)"><i class="fa fa-lg fa-caret-'.$ic.' mr-2"></i>'.get_text('Q-Session', 'Tournament').'</th></tr>';
echo '<tbody id="Tbody-QUAL" class="'.$cl.'">';
echo '<tr class="tit_Abs'.(($outputIndAbs or $outputTeamAbs) ? '' : ' hidden').'">';

// Individual Qualifications
echo '<td class="Bold Left Deletable"><div id="tit_IndAbs" '.($outputIndAbs ? '' : 'class="hidden"').'>';
echo '<input type="checkbox" id="allResultIndAbs" onclick="setAllCheck(\'QualificationInd[]\',this.id);">&nbsp;'.get_text('ResultIndAbs','Tournament');
echo (getModuleParameter('ISK','CalcFinInd',0, 0, true) == 1 ? '<br><span class="text-danger">'.get_text('RkCalcOffWarning', 'ISK').'</span>' : '');
echo '</div></td>';

echo '<td class="Left Deletable InBook" id="sel_IndAbs">';
echo $outputIndAbs ? $outputIndAbs : '&nbsp;';
echo '</td>';

// Team Qualifications
echo '<td class="Bold Left Deletable"><div id="tit_TeamAbs" '.($outputTeamAbs ? '' : 'class="hidden"').'>';
echo '<input type="checkbox" id="allResultTeamAbs" onclick="setAllCheck(\'QualificationTeam[]\',this.id);">&nbsp;' . get_text('ResultSqAbs', 'Tournament');
echo (getModuleParameter('ISK','CalcFinTeam',0, 0, true) == 1 ? '<br><span class="text-danger">'.get_text('RkCalcOffWarning', 'ISK').'</span>' : '');
echo '</div></td>';

echo '<td class="Left Deletable InBook" id="sel_TeamAbs">';
echo $outputTeamAbs ? $outputTeamAbs : '&nbsp;';
echo '</td>';

echo '</tr>';

// divider
//echo '<tr class="Divider"><th colspan="4"></th></tr>';
echo '</tbody>';

if($outputIndAbs) {
    $Scores.='<div><input type="checkbox" name="ScoBra" class="removeAfterUpload"/>'.get_text('ScorecardsInd','Tournament').'</div>';
}
if($outputTeamAbs) {
    $Scores.='<div><input type="checkbox" name="ScoBraTeam" class="removeAfterUpload"/>'.get_text('ScorecardsTeams','Tournament').'</div>';
}


// Eliminations (HF & 3D)
if(isset($_REQUEST['ELIM'])) {
    $cl='';
    $st='on';
    $ic='down';
} else {
    $cl='hidden';
    $st='off';
    $ic='right';
}
echo '<tr class="tit_Elim'.($outputElim ? '' : ' hidden').'"><th colspan="4" class="AccordionToggle" ref="ELIM" status="'.$st.'" onclick="toggleAccordion(this)"><i class="fa fa-lg fa-caret-'.$ic.' mr-2"></i>'.get_text('E-Session', 'Tournament').'</th></tr>';
echo '<tbody id="Tbody-ELIM" class="'.$cl.'">';
if($Elim4) {
    $ElimCode='EL4';
    $outputElim='<input type="checkbox" name="EliminationStartlist[]" value="'.$ElimCode.'"  class="removeAfterUpload">' . get_text('StartList', 'Tournament') . ' '.get_text('WA_Pool4').'<br/>' . $outputElim;
}
if($Elim3) {
    $ElimCode='EL3';
    $outputElim='<input type="checkbox" name="EliminationStartlist[]" value="'.$ElimCode.'"  class="removeAfterUpload">' . get_text('StartList', 'Tournament') . ' '.get_text('WG_Pool2').'<br/>' . $outputElim;
}
if($Elim2) {
    $ElimCode='EL2';
    $outputElim='<input type="checkbox" name="EliminationStartlist[]" value="'.$ElimCode.'"  class="removeAfterUpload">' . get_text('StartlistSession', 'Tournament') . ' '.get_text('Eliminations').' 2<br/>' . $outputElim;
}
if($Elim1) {
    $ElimCode='EL1';
    $outputElim='<input type="checkbox" name="EliminationStartlist[]" value="'.$ElimCode.'"  class="removeAfterUpload">' . get_text('StartlistSession', 'Tournament') . ' '.get_text('Eliminations'). ' 1<br/>' . $outputElim;
}
echo '<tr class="tit_Elim'.($outputElim ? '' : ' hidden').'">';
echo '<td class="Bold Left Deletable">';
echo '<input type="checkbox" id="allResultElim" onclick="setAllCheck(\'EliminationInd[]\',this.id);">&nbsp;';
echo get_text('Elimination');
echo '</td>';
echo '<td class="Left Deletable InBook">';
echo $outputElim ? $outputElim : '&nbsp;';
echo '</td>';
echo '<td colspan="2" class="Left">&nbsp;</td>';
echo '</tr>';

echo '</tbody>';

$Scores.='<div><input type="checkbox" name="ScoElim" class="removeAfterUpload"/>'.get_text('ScorecardsElim','Tournament').'</div>';


// Brackets
if(isset($_REQUEST['EVENTS'])) {
    $cl='';
    $st='on';
    $ic='down';
} else {
    $cl='hidden';
    $st='off';
    $ic='right';
}
echo '<tr class="tit_Bra'.(($outputIndBra or $outputTeamBra) ? '' : ' hidden').'"><th colspan="4" class="AccordionToggle" ref="EVENTS" status="'.$st.'" onclick="toggleAccordion(this)"><i class="fa fa-lg fa-caret-'.$ic.' mr-2"></i>'.get_text('Events', 'Tournament').'</th></tr>';
echo '<tbody id="Tbody-EVENTS" class="'.$cl.'">';
echo '<tr class="tit_Bra'.(($outputIndBra or $outputTeamBra) ? '' : ' hidden').'">';

// Individual brackets
echo '<td class="Bold Left Deletabl"><div id="tit_IndBra" '.($outputIndBra ? '' : 'class="hidden"').'>'
    . '<input type="checkbox" id="allIndBra" onclick="setAllCheck(\'BracketsInd[]\',this.id);">&nbsp;'
    . get_text('Brackets') . ' - ' . get_text('Individual')
    . '</div></td>'
    . '<td class="Left Deletable InBook" id="sel_IndBra">'
    . ($outputIndBra ? $outputIndBra : '&nbsp;')
    . '</td>';


// Team brackets
echo '<td class="Bold Left Deletable><div id="tit_TeamBra" '.($outputTeamBra ? '' : 'class="hidden"').'>'
    . '<input type="checkbox" id="allTeamBra" onclick="setAllCheck(\'BracketsTeam[]\',this.id);">&nbsp;'
    . get_text('Brackets') . ' - ' . get_text('Team')
    . '</div></td>'
    . '<td class="Left Deletable InBook" id="sel_TeamBra">'
    . ($outputTeamBra ? $outputTeamBra : '&nbsp;')
    . '</td>';

echo '</tr>';

// divider
echo '<tr class="Divider"><th colspan="4"></th></tr>';

// Final Rankings
echo '<tr class="tit_Bra'.(($outputIndFin or $outputTeamFin) ? '' : ' hidden').'">';
// Individual rank
echo '<td class="Bold Left Deletable"><div id="tit_IndFin" '.($outputIndFin ? '' : 'class="hidden"').'>'
    . '<input type="checkbox" id="allIndFin" onclick="setAllCheck(\'FinalInd[]\',this.id);">&nbsp;'
    . get_text('Rankings') . ' - ' . get_text('Individual')
    . '</div></td>'
    . '<td class="Left Deletable InBook" id="sel_IndFin">'
    . ($outputIndFin ? $outputIndFin : '&nbsp;')
    . '</td>';


	// Team Rank
echo '<td class="Bold Left Deletable"><div id="tit_TeamFin" '.($outputTeamFin ? '' : 'class="hidden"').'>'
    . '<input type="checkbox" id="allTeamFin" onclick="setAllCheck(\'FinalTeam[]\',this.id);">&nbsp;'
    . get_text('Rankings') . ' - ' . get_text('Team')
    . '</div></td>'
    . '<td class="Left Deletable InBook" id="sel_TeamFin">'
    . ($outputTeamFin ? $outputTeamFin : '&nbsp;')
    . '</td>';


echo '</tr>';
echo '</tbody>';


// ORIS SPECIFIC FILES
echo '<tr class="Divider OrisShow tit_MedBook'.(($ShowMedals or $ShowFinalBook) ? '' : ' hidden').'"><th colspan="4"></th></tr>';
echo '<tr class="OrisShow tit_MedBook'.(($ShowMedals or $ShowFinalBook) ? '' : ' hidden').'">
    <td class="Bold Center Deletable InBook"><div class="tit_Med'.($ShowMedals ? '' : ' hidden').'"><input type="checkbox" name="MEDSTD">'.get_text('MedalStanding').'</div></td>
    <td colspan="2" class="Bold Center Deletable"><div class="tit_Book'.($ShowFinalBook ? '' : ' hidden').'"><input type="checkbox" name="BOOK" class="removeAfterUpload" onclick="SelectBook(this)">'.get_text('CompleteResultBook').'</div></td>
    <td class="Bold Center Deletable InBook"><div class="tit_Med'.($ShowMedals ? '' : ' hidden').'"><input type="checkbox" name="MEDLST">'.get_text('MedalList').'</div></td>
    </tr>';


// Scorecards and Generic PDFs
if(isset($_REQUEST['PDFS'])) {
	$cl='';
	$st='on';
	$ic='down';
} else {
	$cl='hidden';
	$st='off';
	$ic='right';
}
$Scores='';
echo '<tr><th colspan="4" class="AccordionToggle" ref="PDFS" status="'.$st.'" onclick="toggleAccordion(this)"><i class="fa fa-lg fa-caret-'.$ic.' mr-2"></i>'.get_text('UploadFile', 'Tournament').'</th></tr>';
echo '<tbody id="Tbody-PDFS" class="'.$cl.'">';
echo '<tr>
    <th class="Bold" colspan="2">Files</th>
    <th class="Bold" colspan="2">Links</th>
    </tr>
    <tr>
    <td class="Bold" colspan="2">
    	<div class="flexLines">
	        <div><input type="checkbox" value="SCH" name="SCH" class="removeAfterUpload"/>'.get_text('CompleteSchedule','Tournament').'</div>
	        <div>'.get_text('Order', 'Tournament').': <input type="number" name="SCHorder" id="SCHorder" size="3"></div>
	        <div>'.get_text('Descr','Tournament').': <input type="text" class="description"  name="SCHname" id="SCHname" value=""/></div>
	        <div></div>
		</div>
    	<div class="flexLines">
	        <div><input type="checkbox" value="FOP" name="FOP" class="removeAfterUpload"/>'.get_text('PrintFOP','Tournament').'</div>
	        <div>'.get_text('Order', 'Tournament').': <input type="number" name="FOPorder" id="FOPorder" size="3"></div>
	        <div>'.get_text('Descr','Tournament').': <input type="text" class="description" name="FOPname" id="FOPname" /></div>
	        <div></div>
		</div>';
foreach($_SESSION['OnlineFiles'] as $Files) {
	echo '<div class="flexLines" id="Files-'.$Files['IFName'].'">
	        <div>'.$Files['IFName'].'</div>
	        <div>'.get_text('Order', 'Tournament').': <input type="number" name="FilesOrder['.$Files['IFName'].']" size="3" value="'.$Files['IFOrder'].'"></div>
	        <div>'.get_text('Descr','Tournament').': <input type="text" name="FilesDescr['.$Files['IFName'].']" value="'.$Files['IFDescr'].'" /></div>
	        <div><input type="checkbox" value="'.$Files['IFName'].'" name="FilesRemove[]" class="removeAfterUpload"/>'.get_text('CmdDelete','Tournament').'</div>
		</div>';
}
echo '<div class="flexLines">
	        <div><input type="file" name="FIL" id="FIL" class="removeAfterUpload"/></div>
	        <div>'.get_text('Order','Tournament').': <input type="number" name="FILorder" id="FILorder" size="3" class="removeAfterUpload"/></div>
	        <div>'.get_text('Descr','Tournament').': <input type="text" name="FILname" id="FILname" class="removeAfterUpload description"/></div>
	        <div></div>
		</div>
    </td>
    <td class="Bold" colspan="2">';
foreach($_SESSION['OnlineUrls'] as $Urls) {
	echo '<div class="flexLines" id="Urls-'.$Urls['ILId'].'">
	        <div>'.get_text('URL','Tournament').': <input type="text" name="UrlsUrl['.$Urls['ILId'].']" value="'.$Urls['ILUrl'].'"/></div>
	        <div>'.get_text('Order', 'Tournament').': <input type="number" name="UrlsOrder['.$Urls['ILId'].']" size="3" value="'.$Urls['ILOrder'].'"></div>
	        <div>'.get_text('Descr','Tournament').': <input type="text"  name="UrlsDescr['.$Urls['ILId'].']" value="'.$Urls['ILDescr'].'"/></div>
	        <div><input type="checkbox" value="'.$Urls['ILId'].'" name="UrlsRemove[]" class="removeAfterUpload description"/>'.get_text('CmdDelete','Tournament').'</div>
		</div>';
}
echo	'<div class="flexLines">
	        <div>'.get_text('URL','Tournament').': <input type="text" name="URL" id="URL" class="removeAfterUpload"/></div>
	        <div>'.get_text('Order','Tournament').': <input type="number" name="URLorder" id="URLorder" size="3" class="removeAfterUpload"/></div>
	        <div>'.get_text('Descr','Tournament').': <input type="text" name="URLname" id="URLname" class="removeAfterUpload description"/></div>
	        <div></div>
		</div>
		</td>
    </tr>';
echo '</tbody>';



// final button
if (!IsBlocked(BIT_BLOCK_PUBBLICATION)) {
	echo '<tr class="Divider"><th colspan="4" class="Title"></th></tr>';
	echo '<tr><td colspan="4" class="Center"><input type="button" value="' . get_text('CmdOk') . '" onclick="doUpload()"></td></tr>';
}

//Autorefresh Timer
if(getModuleParameter('ISK', 'Mode', false) !== false ) {
    echo '<tr class="Divider"><th colspan="4" class="Title"></th></tr>';
    echo '<tr>'.
        '<th colspan="4" class="Left"><input type="checkbox" id="AutoUploadToggle" onchange="AutoUpload()">'.get_text('AutoUploadToggle','Tournament').'</div>&nbsp;&nbsp;&nbsp;<select id="AutoUploadTimer"><option value="2">2\'</option><option value="5">5\'</option><option value="10">10\'</option><option value="15">15\'</option><option value="30">30\'</option><option value="45">45\'</option><option value="60">60\'</option></select>'.
        '&nbsp;&nbsp;&nbsp;<span id="toCountDown"></span>';
        '</tr>';
}


echo '</table>';
echo '</form>';
//echo '</div>';
//echo '</div>';

include('Common/Templates/tail.php');

