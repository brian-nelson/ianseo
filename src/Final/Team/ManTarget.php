<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
CheckTourSession(true);
checkACL(AclCompetition, AclReadWrite);
require_once('Common/Lib/CommonLib.php');
require_once('Common/Fun_Phases.inc.php');

$JS_SCRIPT=array(
	'<script type="text/javascript" src="../../Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="./ManTarget.js"></script>',
);
if (!empty($_REQUEST['Command'])) {
	$JS_SCRIPT[]=phpVars2js(array('EventCode' => $_REQUEST['d_Event']));
}

$PAGE_TITLE=get_text('TargetFinalTeam');

include('Common/Templates/head.php');

// Event Selector
echo '<form name="Frm" method="post" action="">
    <input type="hidden" name="Command" value="OK">
    <table class="Tabella">
    <tr><th class="Title" colspan="2">'. get_text('TargetFinalTeam').'</th></tr>
    <tr><th colspan="2">'. get_text('FilterRules').'</th></tr>
    <tr class="Divider"><td colspan="2"></td></tr>
    <tr>
    <th class="TitleLeft" width="15%">'. get_text('Event').'</th>
    <td class="Bold">';

$StartPhase=-1;

$Select = "SELECT EvCode,EvEventName 
    FROM Events 
    WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' AND EvFinalFirstPhase!=0 
    ORDER BY EvProgr ASC ";
$Rs=safe_r_sql($Select);

print '<select name="d_Event" id="d_Event">';
while ($Row=safe_fetch($Rs)) {
    print '<option value="' . $Row->EvCode . '"' . (isset($_REQUEST['d_Event']) && $_REQUEST['d_Event']==$Row->EvCode ? ' selected' : '') . '>' . $Row->EvCode . ' - ' . get_text($Row->EvEventName,'','',true) . '</option>';
}
print '</select>&nbsp;<input type="submit" value="'.get_text('CmdOk').'">';
echo '&nbsp;&nbsp;&nbsp;<a href="'.$CFG->ROOT_DIR.'Final/FopSetup.php" target="PrintOut" clasS="Link">'.get_text('FopSetup').'</a>';
echo '<div id="idOutput"></div>';
echo '</td>';
echo '</tr>';
echo '<tr class="Divider"><td colspan="2"></td></tr>';
echo '<tr><td colspan="2">'.get_text('TargetAssignmentDescription', 'Tournament').'</td></tr>';
echo '</table>';
echo '</form>';

if (!empty($_REQUEST['Command'])) {
    $Status=0;	// 1 -> errore
    // Estraggo la fase da cui inizia l'EventCode scelto, e la sua descrizione
    $Select = "SELECT EvCode,EvEventName,EvFinalFirstPhase AS StartPhase 
        FROM Events 
        WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($_REQUEST['d_Event']) . " AND EvTeamEvent='1' ";
    $RsParam=safe_r_sql($Select);
    $RowPar = NULL;

    if (safe_num_rows($RsParam)==1) {

        $RowPar=safe_fetch($RsParam);
        $StartPhase=$RowPar->StartPhase;

        $GridRows = 2*(valueFirstPhase($StartPhase)) + 2 + 2*(valueFirstPhase($StartPhase));	// righe della griglia

        $alpha=ceil(log10(valueFirstPhase($StartPhase))/log10(2));
        $GridCols=2*$alpha+1;	// Colonne della griglia

        $MyGrid = array();
        for ($i=0;$i<$GridRows;++$i) {
            for ($j = 0; $j < $GridCols; ++$j) {
                $MyGrid[$i][$j] = '';
            }
        }

        $HeadRows = 1;		// righe di testa della fase
        $MiddleRows =  2;	// righe tra 2 scontri della fase

        $HeadLineRows = 2*$HeadRows;		// righe di testa del passaggio di fase

    /*
        Righe per disegnare il passaggio di fase.
        La chiave indica il tipo di cella, il valore il numero di celle di quel tipo
    */
        $MiddleLineRows = array
        (
            'Top Right' => 1,
            'Right' => 3,
            'Top' => 1,
            '' => 3
        );

        // indici di $MyGrid
        $Row=0;
        $Col=0;

        $Ultima = 0;		// Flag per disegnare l'ultima riga del passaggio di fase
        $BitMask = 0;
	    $MatchMask = 0;

        $Select = "SELECT EvFinalAthTarget, EvMatchMultipleMatches 
            FROM Events 
            WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($_REQUEST['d_Event']) . " AND EvTeamEvent='1'";
        $Rs=safe_r_sql($Select);
        if ($Row=safe_fetch($Rs)) {
            $BitMask=$Row->EvFinalAthTarget;
	        $MatchMask=$Row->EvMatchMultipleMatches;
        }

        // Il ciclo gestisce fino alla semifinale
        $CurPhase = valueFirstPhase($StartPhase);;

        $TabIndex = 1;

        while ($CurPhase>1 && $Status==0) {
            // Estraggo il bit corrispondete alla fase
            $Bit = ($CurPhase>0 ? valueFirstPhase($CurPhase)*2 : 1);
            $Ath4Tar = (($Bit & $BitMask)==$Bit ? 1 : 0);
	        $Match4Tar = (($Bit & $MatchMask) == $Bit ? 1 : 0);

            $AthPrinted = 0;	// Numero di arcieri stampati
            $TotAthPrinted =0; 	// numero totali di arcieri stampati per la fase
            $Row=0;

        // Estraggo la griglia della fase $CurPhase
            $Select = "SELECT GrPhase, GrPosition, GrPosition2, GrMatchNo, TfEvent, FSTarget, FsLetter, 1 as EvTeamEvent
                FROM TeamFinals 
                inner JOIN Grids ON TFMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($CurPhase) . " 
                LEFT JOIN FinSchedule ON TfEvent=FSEvent AND TFMatchNo=FSMatchNo  AND TFTournament=FSTournament AND (FSTeamEvent='1' OR FSTeamEvent IS NULL) 
                WHERE TFTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " 
                ORDER BY  GrPhase DESC , GrMatchNo ASC ";
            $Rs=safe_r_sql($Select);
            if (safe_num_rows($Rs)>0) {
            // righe di testa della fase
                for ($i=0;$i<=$HeadRows+2;++$i) {
                // se sto stampando l'ultima riga di testa scrivo la fase
                    $Txt = '';
                    $Colspan = ($CurPhase == valueFirstPhase($StartPhase) ? '4' : '5');
                    if ($i==1) {
	                    $subtxt='';
	                    if($Match4Tar) {
		                    $subtxt='<div><input type="radio" value="AB" name="Multiple-'.$CurPhase.'" checked="checked">AB - <input type="radio" value="CD" name="Multiple-'.$CurPhase.'">CD - <input type="radio" value="ABCD" name="Multiple-'.$CurPhase.'">ABCD</div>';
	                    }
	                    $Txt = '<th nowrap class="Center" colspan="' . $Colspan . '">' . get_text(namePhase($StartPhase, $CurPhase) . '_Phase') . $subtxt. '</th>';
                    } else {
                        $Txt = '<td  nowrap class="Center" colspan="' . $Colspan. '">&nbsp;</td>';
                    }
                    $MyGrid[$Row++][$Col].= $Txt;
                }

                while ($MyRow=safe_fetch($Rs)) {
                    if (!isFirstPhase($StartPhase, $MyRow->GrPhase)) {
                        $TipoBordo=($Ultima%2==0 ? 'Bottom' : '');
                        $MyGrid[$Row][$Col].= '<td  nowrap class="Center ' . $TipoBordo . '">&nbsp;</td>';
                    }
                    // posizione
                    $MyGrid[$Row][$Col].= '<td nowrap class="'. ($AthPrinted==1 ? 'Bottom ' : '') . 'Top wRight Left Center">' . (useGrPostion2($StartPhase, $CurPhase) ? ($MyRow->GrPosition2 ? $MyRow->GrPosition2 : '&nbsp;') : $MyRow->GrPosition) . '</td>';
	                $MyGrid[$Row][$Col] .= '<td nowrap class="' . ($AthPrinted == 1 ? 'Bottom ' : '') . 'Top wRight Left Center"><span phase="'.$MyRow->GrPhase.'" id="Letter-'.$MyRow->GrMatchNo.'">' . ltrim($MyRow->FsLetter, '0123456789') . '</span></td>';
                    // target
                    $Target = (!is_null($MyRow->FSTarget) ? $MyRow->FSTarget : '');
	                $id = $MyRow->TfEvent . '_' . $MyRow->EvTeamEvent . '_' . $MyRow->GrMatchNo;
                    if ($Ath4Tar==0) {
                        $MyGrid[$Row][$Col] .= '<td  nowrap class="' . ($AthPrinted == 1 ? 'Bottom ' : '') . 'Top Right Left Center"><input type="text" tabindex="' . ($TabIndex++) . '" maxlength="7" size="3" name="' . $id . '" id="' . $id . '" value="' . $Target . '" phase="'.$MyRow->GrPhase.'" onchange="WriteTarget(this)"></td>';
                    } else {
                        if ($AthPrinted == 0) {
                            $MyGrid[$Row][$Col] .= '<td  nowrap rowspan="2" class="Bottom Top Right Left Center"><input type="text" tabindex="' . ($TabIndex++) . '" maxlength="7" size="3" name="' . $id . '" id="' . $id . '" value="' . $Target . '" phase="'.$MyRow->GrPhase.'" onchange="WriteTarget(this)"></td>';
                        }
                    }
                    // tie
                    $MyGrid[$Row][$Col].= '<td nowrap class="' . ($AthPrinted==1 ? 'wBottom Top' : 'wTop ') . ' wRight wLeft">';
                    $MyGrid[$Row][$Col].=  '&nbsp;';
                    $MyGrid[$Row][$Col].=  '</td>';

                    ++$AthPrinted;
                    ++$TotAthPrinted;

                    // ogni due arcieri stampo le righe mediane
                    if ($AthPrinted==2 && $TotAthPrinted!=(valueFirstPhase($CurPhase)*2)) {
                        for ($i=1;$i<=$MiddleRows;++$i) {
                            $MyGrid[++$Row][$Col].= '<td nowrap class="Center" colspan="' . $Colspan. '">&nbsp;</td>';
                        }
                        $AthPrinted=0;
                    }
                    ++$Ultima;
                    ++$Row;
                }

            /*
                Scrivo le code.
                Ho lo stesso numero di celle che ho in testa
            */
                // righe di coda
                for ($i=1;$i<=$HeadRows+4;++$i)	{
                    if(!array_key_exists($Row,$MyGrid)) {
                        $MyGrid[$Row] = array();
                    }
                    if(!array_key_exists($Col,$MyGrid[$Row])) {
                        $MyGrid[$Row][$Col]=NULL;
                    }
                    $MyGrid[$Row++][$Col].= '<td nowrap class="Center" colspan="' . $Colspan . '">&nbsp;</td>';
                }

            /*
                disegno i passaggi di fase
            */
            // righe senza bordo in testa
                $Row=0;
                ++$Col;
                for ($i=0;$i<=$HeadLineRows+2;++$i) {
                    $MyGrid[$Row++][$Col].= '<td nowrap class="Center">&nbsp;</td>';
                }

            // righe con il disegno
                $kk=0;
                for(;;) {
                    foreach ($MiddleLineRows as $Key => $Value) {
                        for ($i=0;$i<$Value;++$i) {
                            $MyGrid[$Row++][$Col].= '<td class="Center ' . $Key . '">&nbsp;</td>';
                            if (++$kk>(-2+(valueFirstPhase($StartPhase))*4-$MiddleRows)) {
                                break 3;
                            }
                        }
                    }
                }

                for ($i=1;$i<=$HeadLineRows+4;++$i) {
                    if(!array_key_exists($Row,$MyGrid)) {
                        $MyGrid[$Row] = array();
                    }
                    if(!array_key_exists($Col,$MyGrid[$Row])) {
                        $MyGrid[$Row][$Col] = NULL;
                    }
                    $MyGrid[$Row++][$Col].= '<td nowrap class="Center">&nbsp;</td>';
                }

                ++$Col;
                $CurPhase = valueFirstPhase($CurPhase) / 2;    // dimezzo la fase

                $HeadRows=2*$HeadRows+1;
                $MiddleRows=2*$MiddleRows+2;
                $MiddleLineRows['Right']=2*$MiddleLineRows['Right']+1;
                $MiddleLineRows['']=2*$MiddleLineRows['']+1;
                $HeadLineRows=2*$HeadLineRows;
            } else {
                $Status = 1;
            }
        }  // end semifinali

        // oro/bronzo
        $Select = "SELECT GrPhase, GrPosition, GrPosition2, GrMatchNo,TfEvent, FSTarget, FsLetter, 1 as EvTeamEvent 
            FROM TeamFinals 
            INNER JOIN Grids ON TFMatchNo=GrMatchNo 
            LEFT JOIN FinSchedule ON TfEvent=FSEvent AND TFMatchNo=FSMatchNo  AND TFTournament=FSTournament AND (FSTeamEvent='1' OR FSTeamEvent IS NULL) 
            WHERE TFTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " AND GrPhase<='1' 
            ORDER BY GrPhase ASC, GrMatchNo ASC";
        $Rs=safe_r_sql($Select);
        $Row=0;

        if (safe_num_rows($Rs)==4) {
        // Estraggo il bit corrispondete alla fase oro e alla fase bronzo
	        $Bit_0 = 1;
	        $Ath4Tar_0 = (($Bit_0 & $BitMask)==$Bit_0 ? 1 : 0);
	        $Match4Tar_0 = (($Bit_0 & $MatchMask) == $Bit_0 ? 1 : 0);

	        $Bit_1 = 2;
	        $Ath4Tar_1 = (($Bit_1 & $BitMask)==$Bit_1 ? 1 : 0);
	        $Match4Tar_1 = (($Bit_1 & $MatchMask) == $Bit_1 ? 1 : 0);

            $AthPrinted=0;
            $Ultima=0;
            $MiddleRows=2;
        // righe di testa della fase
            for ($i=0;$i<=$HeadRows+2;++$i) {
            // se sto stampando l'ultima riga di testa scrivo la fase
                $Txt = '';
                if ($i==1) {
	                $subtxt_0='';
	                if($Match4Tar_0) {
		                $subtxt_0='<input type="radio" value="AB" name="Multiple-0" checked="checked">AB - <input type="radio" value="CD" name="Multiple-0">CD - <input type="radio" value="ABCD" name="Multiple-0">ABCD';
	                }
	                $subtxt_1='';
	                if($Match4Tar_1) {
		                $subtxt_1='<input type="radio" value="AB" name="Multiple-1" checked="checked">AB - <input type="radio" value="CD" name="Multiple-1">CD - <input type="radio" value="ABCD" name="Multiple-1">ABCD';
	                }
	                $Txt = '<th nowrap class="Center" colspan="4"><div>' . get_text('0_Phase') . $subtxt_0 . '</div><div>' . get_text('1_Phase') . $subtxt_1 . '</div></th>';
                } else {
                    $Txt = '<td  nowrap class="Center" colspan="4">&nbsp;</td>';
                }
                $MyGrid[$Row++][$Col].= $Txt;
            }

            while ($MyRow=safe_fetch($Rs)) {
            // righe mediane ogni due arcieri
                if ($AthPrinted==2) {
                    for ($i=1;$i<=$MiddleRows;++$i) {
                        $MyGrid[$Row++][$Col].= '<td  nowrap class="Center" colspan="4">&nbsp;</td>';
                    }
                    $AthPrinted=0;
                }

                $TipoBordo=($Ultima%2==0 ? 'Bottom' : '');
                if(!array_key_exists($Row,$MyGrid)) {
                    $MyGrid[$Row] = array();
                }
                if(!array_key_exists($Col,$MyGrid[$Row])) {
                    $MyGrid[$Row][$Col] = NULL;
                }
                $MyGrid[$Row][$Col].= '<td nowrap class="Center ' . $TipoBordo . '">&nbsp;</td>';

                // posizione
                $MyGrid[$Row][$Col].= '<td nowrap class="'. ($AthPrinted==1 ? 'Bottom ' : '') . 'Top wRight Left Center">' . (useGrPostion2($StartPhase, $CurPhase) ? ($MyRow->GrPosition2 ? $MyRow->GrPosition2 : '&nbsp;') : $MyRow->GrPosition) . '</td>';
	            $MyGrid[$Row][$Col] .= '<td nowrap class="' . ($AthPrinted == 1 ? 'Bottom ' : '') . 'Top wRight Left Center"><span phase="'.$MyRow->GrPhase.'" id="Letter-'.$MyRow->GrMatchNo.'">' . ltrim($MyRow->FsLetter, '0123456789') . '</span></td>';
                // target
                $Target = (!is_null($MyRow->FSTarget) ? $MyRow->FSTarget : '');

                $Ath4Tar = ${'Ath4Tar_' . $MyRow->GrPhase};
	            $id = $MyRow->TfEvent . '_' . $MyRow->EvTeamEvent . '_' . $MyRow->GrMatchNo;

                if ($Ath4Tar==0) {
                    $MyGrid[$Row][$Col] .= '<td  nowrap class="' . ($AthPrinted == 1 ? 'Bottom ' : '') . 'Top Right Left"><input type="text" tabindex="' . ($TabIndex++) . '" maxlength="7" size="3" name="' . $id . '" id="' . $id . '" value="' . $Target . '" phase="'.$MyRow->GrPhase.'" onchange="WriteTarget(this)"></td>';
                } else {
                    if ($AthPrinted == 0) {
                        $MyGrid[$Row][$Col] .= '<td  nowrap rowspan="2" class="Bottom Top Right Left"><input type="text" tabindex="' . ($TabIndex++) . '" maxlength="7" size="3" name="' . $id . '" id="' . $id . '" value="' . $Target . '" phase="'.$MyRow->GrPhase.'" onchange="WriteTarget(this)"></td>';
                    }
                }

                ++$AthPrinted;
                ++$TotAthPrinted;
                ++$Ultima;
                ++$Row;
            }

            for ($i=1;$i<=$HeadRows+4;++$i) {
                if(!array_key_exists($Row,$MyGrid)) {
                    $MyGrid[$Row] = array();
                }
                if(!array_key_exists($Col,$MyGrid[$Row])) {
                    $MyGrid[$Row][$Col] = NULL;
                }
                $MyGrid[$Row++][$Col].= '<td  class="Center" colspan="3">&nbsp;</td>';
            }
        } else {
            $Status = 1;
        }
    }

    if ($Status==0)	{
        print '<table class="Griglia">';
        for ($i=0;$i<$GridRows+($StartPhase==2 ? 3 : 0);++$i) {
            print '<tr>';
            for ($j=0;$j<$GridCols;++$j) {
                print $MyGrid[$i][$j];
            }
            print '</tr>';
        }
        print '</table>';
    }
}

include('Common/Templates/tail.php');

