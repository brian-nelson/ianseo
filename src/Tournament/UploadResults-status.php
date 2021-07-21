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

$JSON=array();

CheckTourSession(true);
checkACL(AclInternetPublish, AclReadWrite);


$MSG='';
$ORIS=$_SESSION['ISORIS'];

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
$Select = "SELECT distinct EvCode, EvEventName, EvTeamEvent, EvElim1, EvElim2, EvFinalFirstPhase, EvElimType, EvMedals, ifnull(i2.IndId,i3.IndId) as HasMedal, (i2.IndId is NOT NULL) as HasGoldMedal, EvShootOff
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

$JSON['IndBra'] = $outputIndBra;
$JSON['TeamBra'] = $outputTeamBra;
$JSON['IndFin'] = $outputIndFin;
$JSON['TeamFin'] = $outputTeamFin;
$JSON['IndAbs'] = $outputIndAbs;
$JSON['TeamAbs'] = $outputTeamAbs;
$JSON['Elim'] = $outputElim;
$JSON['Medals'] = $ShowMedals;
$JSON['FinalBook'] = $ShowFinalBook;

JsonOut($JSON);