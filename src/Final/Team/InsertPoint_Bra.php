<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);
    checkACL(AclTeams, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_Phases.inc.php');

	$StartPhase=-1;

	if (isset($_REQUEST['Command']) && $_REQUEST['Command']=='OK') {
		// verifico se lo spareggio per l'evento è stato fatto
		$Select = "SELECT EvShootOff 
			FROM Events 
			WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvShootOff='1' AND EvTeamEvent='1' AND EvCode=" . StrSafe_DB($_REQUEST['d_Event']);
		$Rs = safe_r_sql($Select);
		//print $Select;exit;
		if (safe_num_rows($Rs)!=1) {
			header('Location: AbsTeam2.php?EventCode=' . $_REQUEST['d_Event']);
			exit;
        }
	}

	$PAGE_TITLE=get_text('MenuLM_Data insert (Bracket view)');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="../../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_InsertPoint_Bra.js"></script>',
		);

	include('Common/Templates/head.php');
?>
<form name="Frm" method="post" action="">
<input type="hidden" name="Command" value="OK">
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('TeamFinal'); ?></th></tr>
<tr><th colspan="2"><?php print get_text('FilterRules'); ?></th></tr>
<tr class="Divider"><td colspan="2"></td></tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('Event');?></th>
<td>
<?php
	$Select = "SELECT EvCode,EvEventName 
		FROM Events 
		WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' AND EvFinalFirstPhase!=0 
		ORDER BY EvProgr ASC ";
	$Rs=safe_r_sql($Select);

	print '<select name="d_Event" id="d_Event">';
	if (safe_num_rows($Rs)>0) {
		while ($Row=safe_fetch($Rs)) {
			print '<option value="' . $Row->EvCode . '"' . (isset($_REQUEST['d_Event']) && $_REQUEST['d_Event']==$Row->EvCode ? ' selected' : '') . '>' . $Row->EvCode . ' - ' . get_text($Row->EvEventName,'','',true) . '</option>';
		}
	}
	print '</select>';
?>
&nbsp;
<select name="d_Tie" id="d_Tie">
<option value="0"<?php print (isset($_REQUEST['d_Tie']) && $_REQUEST['d_Tie']==0 ? ' selected' : '');?>><?php print get_text('NoManTie');?></option>
<option value="1"<?php print (isset($_REQUEST['d_Tie']) && $_REQUEST['d_Tie']==1 ? ' selected' : '');?>><?php print get_text('ManTie');?></option>
</select>
&nbsp;<input type="submit" value="<?php print get_text('CmdOk');?>">
<div id="idOutput"></div>
</td>
</tr>
</table>
</form>
<?php // griglia
if(!empty($_REQUEST['d_Event'])) {
    $Status = 0;    // 1 -> errore
    // Estraggo la fase da cui inizia l'EventCode scelto, e la sua descrizione
    $Select = "SELECT EvCode,EvEventName,EvFinalFirstPhase AS StartPhase 
        FROM Events 
        WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($_REQUEST['d_Event']) . " AND EvTeamEvent='1' ";
    $RsParam = safe_r_sql($Select);
    $RowPar = NULL;


    if (safe_num_rows($RsParam) == 1) {
        $RowPar = safe_fetch($RsParam);
        $StartPhase = $RowPar->StartPhase;

        $GridRows = 4 * valueFirstPhase($StartPhase) + 2 ;    // righe della griglia

        $alpha = log(valueFirstPhase($StartPhase), 2);
        $GridCols = 2 * $alpha + 1;    // Colonne della griglia


        /*
        Griglia.
        In questo caso il concetto di riga equivale a quello di una tabella normale,
        mentre quello di colonna ha il seguente significato:
        una colonna è una fase oppure un cambio di fase.
        In realtà la fase è formata da 5 colonne nel senso classico del termine.
        La matrice $MyGrid avrà le seguenti dimensioni: $GridRows x $GridCols
        mentre la tabella html che risulterà, avrà le seguenti: $GridRows x (5*k + k-1) con k il numero di fasi da giocare.
    */
        $MyGrid = array();
        for ($i = 0; $i < $GridRows; ++$i) {
            for ($j = 0; $j < $GridCols; ++$j) {
                $MyGrid[$i][$j] = '';
            }
        }

        $HeadRows = 1;        // righe di testa della fase
        $MiddleRows = 2;    // righe tra 2 scontri della fase

        $HeadLineRows = 2 * $HeadRows;        // righe di testa del passaggio di fase

        /*
            Righe per disegnare il passaggio di fase.
            La chiave indica il tipo di cella, il valore il numero di celle di quel tipo
        */
        $MiddleLineRows = array(
            'Top Right' => 1,
            'Right' => 3,
            'Top' => 1,
            '' => 3
        );

        // indici di $MyGrid
        $Row = 0;
        $Col = 0;

        $Ultima = 0;        // Flag per disegnare l'ultima riga del passaggio di fase

        // Il ciclo gestisce fino alla semifinale
        $CurPhase = valueFirstPhase($StartPhase);

        $TabIndex = 1;

        // data list for the Notes field
        echo '<datalist id="NoteList">';
        echo '<option value="DNS">' . get_text('DNS', 'Tournament') . '</option>';
        echo '<option value="DNF">' . get_text('DNF', 'Tournament') . '</option>';
        echo '<option value="DSQ">' . get_text('DSQ', 'Tournament') . '</option>';
        echo '<option value="WR">' . get_text('WR-Record', 'Tournament') . '</option>';
        echo '<option value="OR">' . get_text('OR-Record', 'Tournament') . '</option>';
        echo '</datalist>';


        while ($CurPhase > 1 && $Status == 0) {
            $AthPrinted = 0;    // Numero di arcieri stampati
            $TotAthPrinted = 0;    // numero totali di arcieri stampati per la fase
            $Row = 0;

            // Estraggo la griglia della fase $CurPhase
            $Select = "SELECT GrPhase, IF(EvFinalFirstPhase=48, GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition)) as GrPosition, GrMatchNo, EvMatchMode, TfNotes,TfTeam,TfMatchNo,TfEvent, IF(EvMatchMode=0,TfScore,TfSetScore) AS Score, TfTie, TfTiebreak, 
                CoCode,CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) AS TeamName,
                IF(GrPhase>2, FLOOR(TfMatchNo/2),FLOOR(TfMatchNo/2)-2) AS NextMatchNo 
                FROM TeamFinals 
                INNER JOIN Grids ON TfMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($CurPhase) . " 
                INNER JOIN Events ON TfEvent=EvCode AND EvTeamEvent='1' AND EvTournament=TfTournament 
                LEFT JOIN Countries ON TfTeam=CoId 
                WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " 
                ORDER BY TfEvent, GrPhase DESC , GrMatchNo ASC ";

            $Rs = safe_r_sql($Select);

            $StrValue = '';

            if (safe_num_rows($Rs) > 0) {
                $obj = getEventArrowsParams($_REQUEST['d_Event'], $CurPhase, 1);

                $Bottone = '<input type="button" name="CmdBlockPhase_' . $CurPhase . '" id="CmdBlockPhase_' . $CurPhase . '" value="' . get_text('CmdEnable') . '" onClick="javascript:BlockPhase(' . $CurPhase . ',\'' . get_text('CmdEnable') . '\',\'' . get_text('CmdDisable') . '\',' . $obj->so . ');">';
                // righe di testa della fase
                for ($i = 0; $i <= $HeadRows + 2; ++$i) {
                    // se sto stampando l'ultima riga di testa scrivo la fase
                    $Txt = '';
                    if ($i == 1) {
                        $Txt = '<th nowrap class="Center" colspan="5">' . get_text(namePhase($StartPhase, $CurPhase) . '_Phase') . '</th>';
                    } elseif ($i == 2) {
                        $Txt = '<td  nowrap class="Center" colspan="5">' . $Bottone . '</td>';
                    } elseif ($i == $HeadRows + 2) {
                        $Txt = '<td  nowrap class="Center" colspan="5">' . get_text(namePhase($StartPhase, $CurPhase) . '_Phase') . '</td>';
                    } else {
                        $Txt = '<td  nowrap class="Center" colspan="5">&nbsp;</td>';
                    }
                    $MyGrid[$Row++][$Col] .= $Txt;
                }

                while ($MyRow = safe_fetch($Rs)) {
                    $obj = getEventArrowsParams($MyRow->TfEvent, $MyRow->GrPhase, 1);

                    if (!isFirstPhase($StartPhase, $MyRow->GrPhase)) {
                        $TipoBordo = ($Ultima % 2 == 0 ? 'Bottom' : '');
                        $MyGrid[$Row][$Col] .= '<td  nowrap class="Center ' . $TipoBordo . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                    } else {
                        // Posizione
                        $MyGrid[$Row][$Col] .= '<td nowrap class="' . ($AthPrinted == 1 ? 'Bottom ' : '') . 'Top wRight Left Center">' . ($MyRow->GrPosition ? $MyRow->GrPosition : '&nbsp;') . '</td>';
                    }
                    // Atleta
                    $MyGrid[$Row][$Col] .= '<td  nowrap class="' . ($AthPrinted == 1 ? 'Bottom ' : '') . 'Top wRight Left"><div id="idAth_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '">' . (!is_null($MyRow->TeamName) ? $MyRow->TeamName : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') /*. ' - ' . $MyRow->GrMatchNo*/ . '</div></td>';
                    // Codice Nazione (o bandiera)
                    $MyGrid[$Row][$Col] .= '<td nowrap class="' . ($AthPrinted == 1 ? 'Bottom ' : '') . 'Top wRight Left"><div id="idCty_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '">' . (!is_null($MyRow->CoCode) ? $MyRow->CoCode : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') . '</div></td>';
                    // Punteggio
                    $MyGrid[$Row][$Col] .= '<td  nowrap class="' . ($AthPrinted == 1 ? 'Bottom ' : '') . 'Top Right Left TextRight"><input type="text" class="disabled" tabindex="' . ($TabIndex++) . '" size="3" name="d_S_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '" id="d_S_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '" value="' . $MyRow->Score . '" onBlur="javascript:SendToServer(\'d_S_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '\');" disabled="disabled"></td>';
                    // tie
                    $MyGrid[$Row][$Col] .= '<td nowrap class="Center ' . ($AthPrinted == 1 ? 'wBottom Top' : 'wTop') . ' wRight wLeft">';

                    if (isset($_REQUEST['d_Tie']) && $_REQUEST['d_Tie'] == 1) {
                        $MyGrid[$Row][$Col] .= '<select  class="disabled" tabindex="' . ($TabIndex++) . '" name="d_T_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '" id="d_T_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '" onChange="javascript:SendToServer(\'d_T_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '\');" disabled>';
                        $MyGrid[$Row][$Col] .= '<option value="0"' . ($MyRow->TfTie == 0 ? ' selected' : '') . '>' . get_text('NoTie', 'Tournament') . '</option>';
                        $MyGrid[$Row][$Col] .= '<option value="1"' . ($MyRow->TfTie == 1 ? ' selected' : '') . '>' . get_text('TieWinner', 'Tournament') . '</option>';
                        $MyGrid[$Row][$Col] .= '<option value="2"' . ($MyRow->TfTie == 2 ? ' selected' : '') . '>' . get_text('Bye') . '</option>';
                        $MyGrid[$Row][$Col] .= '</select>&nbsp;';

                        $TieBreak = str_pad($MyRow->TfTiebreak, $obj->so, ' ', STR_PAD_RIGHT);
                        for($pSo=0; $pSo<3; $pSo++ ) {
                            for ($i = 0; $i < $obj->so; ++$i) {
                                $ArrI = $i+($pSo*$obj->so);
                                $MyGrid[$Row][$Col] .= '<input  class="disabled" tabindex="' . ($TabIndex++) . '" type="text" size="1" maxlength="3" name="d_t_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '_' . $ArrI . '" id="d_t_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '_' . $ArrI . '" value="' . (!empty($TieBreak[$ArrI]) ? DecodeFromLetter($TieBreak[$ArrI]):'') . '" onBlur="javascript:SendTieBreak(\'d_t_' . $MyRow->TfEvent . '_' . $MyRow->TfMatchNo . '\',' . (3*$obj->so) . ');" disabled>';
                            }
                            $MyGrid[$Row][$Col] .= '&nbsp;';
                        }
                        $MyGrid[$Row][$Col] .= '<br/><input list="NoteList" value="' . $MyRow->TfNotes . '" tabindex="' . ($TabIndex++) . '" name="d_N_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '" id="d_N_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '" onChange="javascript:SendToServer(\'d_N_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '\');">';
                    } else {
                        $MyGrid[$Row][$Col] .= '&nbsp;';
                    }
                    $MyGrid[$Row][$Col] .= '</td>';
                    //$c=-1;

                    //print $c;
                    ++$AthPrinted;
                    ++$TotAthPrinted;

                    // ogni due arcieri stampo le righe mediane
                    if ($AthPrinted == 2 && $TotAthPrinted != valueFirstPhase($CurPhase) * 2) {
                        for ($i = 1; $i <= $MiddleRows; ++$i) {
                            if (!array_key_exists($Col, $MyGrid[++$Row])) {
                                $MyGrid[$Row][$Col] = null;
                            }
                            $MyGrid[$Row][$Col] .= '<td nowrap class="Center" colspan="5">&nbsp;</td>';
                        }
                        $AthPrinted = 0;
                    }
                    ++$Ultima;
                    ++$Row;
                }

                /*
                    Scrivo le code.
                    Ho lo stesso numero di celle che ho in testa
                */
                // righe di coda
                for ($i = 1; $i <= $HeadRows + 4; ++$i) {
                    $MyGrid[$Row][$Col] = '';
                    $MyGrid[$Row++][$Col] .= '<td nowrap class="Center" colspan="5">&nbsp;</td>';

                }

                /*
                    disegno i passaggi di fase
                */
                // righe senza bordo in testa
                $Row = 0;
                ++$Col;
                for ($i = 0; $i <= $HeadLineRows + 2; ++$i) {
                    $MyGrid[$Row++][$Col] .= '<td nowrap class="Center">&nbsp;</td>';
                }

                // righe con il disegno
                $kk = 0;
                for (; ;) {
                    foreach ($MiddleLineRows as $Key => $Value) {
                        for ($i = 0; $i < $Value; ++$i) {
                            $MyGrid[$Row++][$Col] .= '<td class="Center ' . $Key . '">&nbsp;</td>';
                            ++$kk;
                            if ($kk > (-2 + (valueFirstPhase($StartPhase)) * 4 - $MiddleRows)) {
                                break 3;
                            }
                        }
                    }
                }

                for ($i = 1; $i <= $HeadLineRows + 4; ++$i) {
                    $MyGrid[$Row][$Col] = '';
                    $MyGrid[$Row++][$Col] .= '<td nowrap class="Center">&nbsp;</td>';
                }

                ++$Col;
                $CurPhase /= 2;    // dimezzo la fase

                $HeadRows = 2 * $HeadRows + 1;
                $MiddleRows = 2 * $MiddleRows + 2;
                $MiddleLineRows['Right'] = 2 * $MiddleLineRows['Right'] + 1;
                $MiddleLineRows[''] = 2 * $MiddleLineRows[''] + 1;
                $HeadLineRows = 2 * $HeadLineRows;
            } else {
                $Status = 1;
            }
        }

        // Adesso gestisco l'oro e il bronzo
        $Select = "SELECT GrPhase, GrMatchNo, EvMatchMode, TfNotes,TfTeam,TfMatchNo,TfEvent, IF(EvMatchMode=0,TfScore,TfSetScore) AS Score,TfTie, TfTiebreak, 
            CoCode,CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) AS TeamName, 
            IF(GrPhase>2, FLOOR(TfMatchNo/2),FLOOR(TfMatchNo/2)-2) AS NextMatchNo 
            FROM TeamFinals INNER JOIN Grids ON TfMatchNo=GrMatchNo 
            INNER JOIN Events ON TfEvent=EvCode AND EvTeamEvent='1' AND EvTournament=TfTournament 
            LEFT JOIN Countries ON TfTeam=CoId 
            WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " AND GrPhase<='1' 
            ORDER BY TfEvent, GrPhase ASC , GrMatchNo ASC ";

        //print $Select;
        $Rs = safe_r_sql($Select);
        $Row = 0;

        if (safe_num_rows($Rs) == 4) {
            $obj = getEventArrowsParams($_REQUEST['d_Event'], 0, 1);
            $AthPrinted = 0;
            $Ultima = 0;
            $MiddleRows = 2;
            // righe di testa della fase
            $Bottone = '<input type="button" name="CmdBlockPhase_0" id="CmdBlockPhase_0" value="' . get_text('CmdEnable') . '" onClick="javascript:BlockPhase(0,\'' . get_text('CmdEnable') . '\',\'' . get_text('CmdDisable') . '\',' . $obj->so . ');">';
            for ($i = 0; $i <= $HeadRows + 2; ++$i) {
                $Txt = '';
                if ($i == 1) {
                    $Txt = '<th nowrap class="Center" colspan="5">' . get_text('0_Phase') . '/' . get_text('1_Phase') . '</th>';
                } elseif ($i == 2) {
                    $Txt = '<td  nowrap class="Center" colspan="5">' . $Bottone . '</td>';
                } elseif ($i == $HeadRows + 2) {
                    $Txt = '<td  nowrap class="Center" colspan="5">' . get_text('0_Phase') . '</td>';
                } else {
                    $Txt = '<td  nowrap class="Center" colspan="5">&nbsp;</td>';
                }
                $MyGrid[$Row++][$Col] .= $Txt;
            }

            while ($MyRow = safe_fetch($Rs)) {
                $obj = getEventArrowsParams($MyRow->TfEvent, $MyRow->GrPhase, 1);
                // righe mediane ogni due arcieri
                if ($AthPrinted == 2) {
                    for ($i = 1; $i <= $MiddleRows; ++$i) {
                        $MyGrid[$Row++][$Col] .= '<td  nowrap class="Center" colspan="5">&nbsp;</td>';
                    }
                    $AthPrinted = 0;
                }

                $TipoBordo = ($Ultima % 2 == 0 ? 'Bottom' : '');
                if (!array_key_exists($Col, $MyGrid[$Row]))
                    $MyGrid[$Row][$Col] = '';
                $MyGrid[$Row][$Col] .= '<td nowrap class="Center ' . $TipoBordo . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                // Atleta
                $MyGrid[$Row][$Col] .= '<td nowrap class="Bottom Top wRight Left"><div id="idAth_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '">' . (!is_null($MyRow->TeamName) ? $MyRow->TeamName : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') /*. ' ' . $MyRow->GrMatchNo*/ . '</div></td>';
                // Codice Nazione (o bandiera)
                $MyGrid[$Row][$Col] .= '<td nowrap class="Bottom Top wRight Left"><div id="idCty_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '">' . (!is_null($MyRow->CoCode) ? $MyRow->CoCode : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') . '</div></td>';
                // Punteggio
                $MyGrid[$Row][$Col] .= '<td nowrap class="Bottom Top Right Left TextRight"><input type="text"  class="disabled" tabindex="' . ($TabIndex++) . '" size="3" name="d_S_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '" id="d_S_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '" value="' . $MyRow->Score . '" onBlur="javascript:SendToServer(\'d_S_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '\');" disabled></td>';
                // tie
                $MyGrid[$Row][$Col] .= '<td nowrap class="Center">';

                if (isset($_REQUEST['d_Tie']) && $_REQUEST['d_Tie'] == 1) {
                    $MyGrid[$Row][$Col] .= '<select  class="disabled" tabindex="' . ($TabIndex++) . '" name="d_T_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '" id="d_T_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '" onChange="javascript:SendToServer(\'d_T_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '\');" disabled>';
                    $MyGrid[$Row][$Col] .= '<option value="0"' . ($MyRow->TfTie == 0 ? ' selected' : '') . '>' . get_text('NoTie', 'Tournament') . '</option>';
                    $MyGrid[$Row][$Col] .= '<option value="1"' . ($MyRow->TfTie == 1 ? ' selected' : '') . '>' . get_text('TieWinner', 'Tournament') . '</option>';
                    $MyGrid[$Row][$Col] .= '<option value="2"' . ($MyRow->TfTie == 2 ? ' selected' : '') . '>' . get_text('Bye') . '</option>';
                    $MyGrid[$Row][$Col] .= '</select>&nbsp;';

                    $TieBreak = str_pad($MyRow->TfTiebreak, $obj->so, ' ', STR_PAD_RIGHT);
                    for($pSo=0; $pSo<3; $pSo++ ) {
                        for ($i = 0; $i < $obj->so; ++$i) {
                            $ArrI = $i+($pSo*$obj->so);
                            $MyGrid[$Row][$Col] .= '<input  class="disabled" tabindex="' . ($TabIndex++) . '" type="text" size="1" maxlength="3" name="d_t_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '_' . $ArrI . '" id="d_t_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '_' . $ArrI . '" value="' . (!empty($TieBreak[$ArrI]) ? DecodeFromLetter($TieBreak[$ArrI]):'') . '" onBlur="javascript:SendTieBreak(\'d_t_' . $MyRow->TfEvent . '_' . $MyRow->TfMatchNo . '\',' . (3*$obj->so) . ');" disabled>';
                        }
                        $MyGrid[$Row][$Col] .= '&nbsp;';
                    }
                    $MyGrid[$Row][$Col] .= '<br/><input list="NoteList" value="' . $MyRow->TfNotes . '" tabindex="' . ($TabIndex++) . '" name="d_N_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '" id="d_N_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '" onChange="javascript:SendToServer(\'d_N_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '\');">';
                } else {
                    $MyGrid[$Row][$Col] .= '&nbsp;';
                }
                $MyGrid[$Row][$Col] .= '</td>';
                ++$Row;
                ++$AthPrinted;
                ++$Ultima;
            }

            for ($i = 1; $i <= $HeadRows + 4; ++$i) {
                $MyGrid[$Row][$Col] = '';
                $MyGrid[$Row++][$Col] .= '<td  class="Center" colspan="5">' . ($i == 1 ? get_text('1_Phase') : '&nbsp;') . '</td>';
            }
        } else {
            $Status = 1;
        }
    }

    if ($Status == 0) {
        print '<table class="Griglia">';
        for ($i = 0; $i < $GridRows + ($StartPhase == 2 ? 3 : 0); ++$i) {
            print '<tr>';
            for ($j = 0; $j < $GridCols; ++$j) {
                print $MyGrid[$i][$j];
            }
            print '</tr>';
        }
        print '</table>';
    }
}

	include('Common/Templates/tail.php');
?>