<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    CheckTourSession(true);
    checkACL(AclCompetition, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Phases.inc.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_ManTarget.js"></script>',
		'<script type="text/javascript" src="../../Common/js/Fun_JS.inc.js"></script>',
		);

	include('Common/Templates/head.php');
?>
<form name="Frm" method="post" action="">
<input type="hidden" name="Command" value="OK">
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('TargetFinalInd'); ?></th></tr>
<tr><th colspan="2"><?php print get_text('FilterRules'); ?></th></tr>
<tr class="Divider"><td colspan="2"></td></tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('Event');?></th>
<td class="Bold">
<?php
	$StartPhase=-1;

	$JS_RedTarget = '';

	$Select = "SELECT EvCode,EvEventName 
		FROM Events 
		WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='0' AND EvFinalFirstPhase!=0 
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
&nbsp;<input type="submit" value="<?php print get_text('CmdOk');?>">
&nbsp;&nbsp;&nbsp;
<a href="<?php echo $CFG->ROOT_DIR ?>Final/FopSetup.php" target="PrintOut" clasS="Link"><?php echo get_text('FopSetup'); ?></a>
<div id="idOutput"></div>
</td>
</tr>
<tr class="Divider"><td colspan="2"></td></tr>
<tr><td colspan="2"><?php echo get_text('TargetAssignmentDescription', 'Tournament'); ?></td></tr>
</table>
</form>
<?php
	if (!empty($_REQUEST['Command'])) {
		$Status=0;	// 1 -> errore
		// Estraggo la fase da cui inizia l'EventCode scelto, e la sua descrizione
		$Select = "SELECT EvCode,EvEventName,if(EvElimType=3, 64, EvFinalFirstPhase) AS StartPhase 
            FROM Events 
            WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($_REQUEST['d_Event']) . " AND EvTeamEvent='0' ";
		$RsParam=safe_r_sql($Select);
		$RowPar = NULL;

		if (safe_num_rows($RsParam)==1) {
			$JS_RedTarget = '<script type="text/javascript">';
			$RowPar=safe_fetch($RsParam);
			$StartPhase=$RowPar->StartPhase;
			$GridRows = 2*(valueFirstPhase($StartPhase)) + 2 + 2*(valueFirstPhase($StartPhase));	// righe della griglia
			$alpha=ceil(log10(valueFirstPhase($StartPhase))/log10(2));
			$GridCols=2*$alpha+1;	// Colonne della griglia

		/*
			Griglia.
			In questo caso il concetto di riga equivale a quello di una tabella normale,
			mentre quello di colonna ha il seguente significato:
			una colonna � una fase oppure un cambio di fase.
			In realt� la fase � formata da 5 colonne nel senso classico del termine.
			La matrice $MyGrid avr� le seguenti dimensioni: $GridRows x $GridCols
			mentre la tabella html che risulter�, avr� le seguenti: $GridRows x (5*k + k-1) con k il numero di fasi da giocare.
		*/
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
			$MiddleLineRows = array (
				'Top Right' => 1,
				'Right' => 3,
				'Top' => 1,
				'' => 3
			);

			// indici di $MyGrid
			$Row=0;
			$Col=0;

			$Ultima = 0;		// Flag per disegnare l'ultima riga del passaggio di fase

		// Il ciclo gestisce fino alla semifinale
			$CurPhase = valueFirstPhase($StartPhase);

			$TabIndex = 1;
			$BitMask = 0;

			$Select = "SELECT EvFinalAthTarget 
				FROM Events 
				WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($_REQUEST['d_Event']) . " AND EvTeamEvent='0' ";
			$Rs=safe_r_sql($Select);
			//print $Select;exit;
			if (safe_num_rows($Rs)==1) {
				$Row=safe_fetch($Rs);
				$BitMask=$Row->EvFinalAthTarget;
			}

			while ($CurPhase>1 && $Status==0) {
                $JS_RedTarget .= "FindRedTarget('" . $_REQUEST['d_Event'] . "','" . $CurPhase . "','');";
                // Estraggo il bit corrispondete alla fase
                $Bit = ($CurPhase > 0 ? valueFirstPhase($CurPhase) * 2 : 1);
                $Ath4Tar = (($Bit & $BitMask) == $Bit ? 1 : 0);

                $AthPrinted = 0;    // Numero di arcieri stampati
                $TotAthPrinted = 0;    // numero totali di arcieri stampati per la fase
                $Row = 0;

                // Estraggo la griglia della fase $CurPhase
                $Select = "SELECT GrPhase,GrPosition2, GrPosition, GrMatchNo,FinEvent, FSTarget 
					FROM Finals JOIN Grids ON FinMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($CurPhase) . " 
					INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
					LEFT JOIN FinSchedule ON FinEvent=FSEvent AND FinMatchNo=FSMatchNo AND FinTournament=FSTournament AND (FSTeamEvent='0' OR FSTeamEvent IS NULL) 
					WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " 
					ORDER BY  GrPhase DESC , GrMatchNo ASC ";
                $Rs = safe_r_sql($Select);
                if (safe_num_rows($Rs) > 0) {
                    // righe di testa della fase
                    for ($i = 0; $i <= $HeadRows + 2; ++$i) {
                        // se sto stampando l'ultima riga di testa scrivo la fase
                        $Txt = '';
                        $Colspan = ($CurPhase == valueFirstPhase($StartPhase) ? '3' : '4');
                        if ($i == 1) {
                            $Txt = '<th nowrap class="Center" colspan="' . $Colspan . '">' . get_text(namePhase($StartPhase, $CurPhase) . '_Phase') . '</th>';
                        } else {
                            $Txt = '<td  nowrap class="Center" colspan="' . $Colspan . '">&nbsp;</td>';
                        }
                        $MyGrid[$Row++][$Col] .= $Txt;
                    }

                    while ($MyRow = safe_fetch($Rs)) {
                        if (!isFirstPhase($StartPhase, $MyRow->GrPhase)) {
                            $TipoBordo = ($Ultima % 2 == 0 ? 'Bottom' : '');
                            $MyGrid[$Row][$Col] .= '<td  nowrap class="Center ' . $TipoBordo . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                        }
                        // posizione
                        $MyGrid[$Row][$Col] .= '<td nowrap class="' . ($AthPrinted == 1 ? 'Bottom ' : '') . 'Top wRight Left Center">' . (useGrPostion2($StartPhase, $CurPhase) ? ($MyRow->GrPosition2 ? $MyRow->GrPosition2 : '&nbsp;') : $MyRow->GrPosition) . '</td>';
                        // target
                        $Target = (!is_null($MyRow->FSTarget) ? $MyRow->FSTarget : '');
                        if ($Ath4Tar == 0) {
                            $MyGrid[$Row][$Col] .= '<td  nowrap class="' . ($AthPrinted == 1 ? 'Bottom ' : '') . 'Top Right Left Center"><input type="text" tabindex="' . ($TabIndex++) . '" maxlength="7" size="3" name="d_FSTarget_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '_' . $Ath4Tar . '" id="d_FSTarget_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '_' . $Ath4Tar . '" value="' . $Target . '" onBlur="javascript:WriteTarget(\'d_FSTarget_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '_' . $Ath4Tar . '\');"></td>';
                        } else {
                            if ($AthPrinted == 0) {
                                $MyGrid[$Row][$Col] .= '<td  nowrap rowspan="2" class="Bottom Top Right Left Center"><input type="text" tabindex="' . ($TabIndex++) . '" maxlength="7" size="3" name="d_FSTarget_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '_' . $Ath4Tar . '" id="d_FSTarget_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '_' . $Ath4Tar . '" value="' . $Target . '" onBlur="javascript:WriteTarget(\'d_FSTarget_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '_' . $Ath4Tar . '\');"></td>';
                            }
                        }

                        // tie
                        $MyGrid[$Row][$Col] .= '<td nowrap class="' . ($AthPrinted == 1 ? 'wBottom Top' : 'wTop ') . ' wRight wLeft">';
                        $MyGrid[$Row][$Col] .= '&nbsp;';
                        $MyGrid[$Row][$Col] .= '</td>';

                        ++$AthPrinted;
                        ++$TotAthPrinted;

                        // ogni due arcieri stampo le righe mediane
                        if ($AthPrinted == 2 AND $TotAthPrinted != (valueFirstPhase($CurPhase) * 2)) {
                            for ($i = 1; $i <= $MiddleRows; ++$i) {
                                if (!array_key_exists($Col, $MyGrid[++$Row])) {
                                    $MyGrid[$Row][$Col] = null;
                                }
                                $MyGrid[$Row][$Col] .= '<td nowrap class="Center" colspan="' . $Colspan . '">&nbsp;</td>';
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
                        if (!array_key_exists($Row, $MyGrid)) {
                            $MyGrid[$Row] = array();
                        }
                        if (!array_key_exists($Col, $MyGrid[$Row])) {
                            $MyGrid[$Row][$Col] = NULL;
                        }
                        $MyGrid[$Row++][$Col] .= '<td nowrap class="Center" colspan="' . $Colspan . '">&nbsp;</td>';
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
                                if (++$kk > (-2 + (valueFirstPhase($StartPhase)) * 4 - $MiddleRows)) {
                                    break 3;
                                }
                            }
                        }
                    }

                    for ($i = 1; $i <= $HeadLineRows + 4; ++$i) {
                        if (!array_key_exists($Row, $MyGrid)) {
                            $MyGrid[$Row] = array();
                        }
                        if (!array_key_exists($Col, $MyGrid[$Row])) {
                            $MyGrid[$Row][$Col] = NULL;
                        }
                        $MyGrid[$Row++][$Col] .= '<td nowrap class="Center">&nbsp;</td>';
                    }

                    ++$Col;
                    $CurPhase = valueFirstPhase($CurPhase) / 2;    // dimezzo la fase

                    $HeadRows = 2 * $HeadRows + 1;
                    $MiddleRows = 2 * $MiddleRows + 2;
                    $MiddleLineRows['Right'] = 2 * $MiddleLineRows['Right'] + 1;
                    $MiddleLineRows[''] = 2 * $MiddleLineRows[''] + 1;
                    $HeadLineRows = 2 * $HeadLineRows;
                } else {
                    $Status = 1;
                }
			}  // end semifinali

		// oro/bronzo
			$Select = "SELECT GrPhase, GrPosition2, GrPosition, GrMatchNo, FinEvent, FSTarget 
				FROM Finals INNER JOIN Grids ON FinMatchNo=GrMatchNo 
				INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
				LEFT JOIN FinSchedule ON FinEvent=FSEvent AND FinMatchNo=FSMatchNo AND FinTournament=FSTournament AND (FSTeamEvent='0' OR FSTeamEvent IS NULL) 
				WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " AND GrPhase<='1' 
				ORDER BY GrPhase ASC , GrMatchNo ASC ";
			$Rs=safe_r_sql($Select);
			//print $Select;
			$Row=0;

			if (safe_num_rows($Rs)==4) {
				$JS_RedTarget
					.="FindRedTarget('" . $_REQUEST['d_Event'] . "','1','');"
					. "FindRedTarget('" . $_REQUEST['d_Event'] . "','0','');";

			// Estraggo il bit corrispondete alla fase oro e alla fase bronzo
				$Bit_0 = 1;
				$Ath4Tar_0 = (($Bit_0 & $BitMask)==$Bit_0 ? 1 : 0);

				$Bit_1 = 2;
				$Ath4Tar_1 = (($Bit_1 & $BitMask)==$Bit_1 ? 1 : 0);


				$AthPrinted=0;
				$Ultima=0;
				$MiddleRows=2;
			// righe di testa della fase
				for ($i=0;$i<=$HeadRows+2;++$i)	{
				// se sto stampando l'ultima riga di testa scrivo la fase
					//$MyGrid[$Row++][$Col].= '<td  nowrap class="Center" colspan="5">' . ($i==$HeadRows ? get_text('0_Phase') : '&nbsp;') . '</td>';
					//$MyGrid[$Row++][$Col].= '<td  nowrap class="Center" colspan="5">' . ($i==$HeadRows ? get_text('0_Phase') : ($i==0 ? $Bottone : '&nbsp;')) . '</td>';

					$Txt = '';
					if ($i==1) {
                        $Txt = '<th nowrap class="Center" colspan="3">' . get_text('0_Phase') . '/' . get_text('1_Phase') . '</th>';
                    } else {
                        $Txt = '<td  nowrap class="Center" colspan="3">&nbsp;</td>';
                    }
					$MyGrid[$Row++][$Col].= $Txt;
				}

				while ($MyRow=safe_fetch($Rs)) {

				// righe mediane ogni due arcieri
					if ($AthPrinted==2)	{
						for ($i=1;$i<=$MiddleRows;++$i)	{
							$MyGrid[$Row++][$Col].= '<td  nowrap class="Center" colspan="3">&nbsp;</td>';
						}
						$AthPrinted=0;
					}

					$TipoBordo=($Ultima%2==0 ? 'Bottom' : '');
					if(!array_key_exists($Col,$MyGrid[$Row])) {
                        $MyGrid[$Row][$Col] = NULL;
                    }
					$MyGrid[$Row][$Col].= '<td nowrap class="Center ' . $TipoBordo . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';

				// posizione
					$MyGrid[$Row][$Col].= '<td nowrap class="'. ($AthPrinted==1 ? 'Bottom ' : '') . 'Top wRight Left Center">' . (useGrPostion2($StartPhase, $CurPhase) ? ($MyRow->GrPosition2 ? $MyRow->GrPosition2 : '&nbsp;') : $MyRow->GrPosition) . '</td>';
				// target
					$Target = (!is_null($MyRow->FSTarget) ? $MyRow->FSTarget : '');

					$Ath4Tar = ${'Ath4Tar_' . $MyRow->GrPhase};

					if ($Ath4Tar==0) {
                        $MyGrid[$Row][$Col] .= '<td  nowrap class="' . ($AthPrinted == 1 ? 'Bottom ' : '') . 'Top Right Left Center"><input type="text" tabindex="' . ($TabIndex++) . '" maxlength="7" size="3" name="d_FSTarget_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '_' . $Ath4Tar . '" id="d_FSTarget_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '_' . $Ath4Tar . '" value="' . $Target . '" onBlur="javascript:WriteTarget(\'d_FSTarget_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '_' . $Ath4Tar . '\');"></td>';
                    } else {
                        if ($AthPrinted == 0) {
                            $MyGrid[$Row][$Col] .= '<td  nowrap rowspan="2" class="Bottom Top Right Left Center"><input type="text" tabindex="' . ($TabIndex++) . '" maxlength="7" size="3" name="d_FSTarget_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '_' . $Ath4Tar . '" id="d_FSTarget_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '_' . $Ath4Tar . '" value="' . $Target . '" onBlur="javascript:WriteTarget(\'d_FSTarget_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '_' . $Ath4Tar . '\');"></td>';
                        }
                    }
					++$AthPrinted;
					++$TotAthPrinted;
					++$Ultima;
					++$Row;
				}

				for ($i=1;$i<=$HeadRows+4;++$i) {
					//$MyGrid[$Row++][$Col].= '<td  class="Center" colspan="3">' . ($i==1 ? get_text('1_Phase') : '&nbsp;') . '</td>';
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
			$JS_RedTarget .= '</script>';
		}

		if ($Status==0)	{
			print '<table class="Griglia">';
			for ($i=0;$i<$GridRows+($StartPhase==2 ? 3 : 0);++$i) {
				print '<tr>';
				for ($j=0;$j<$GridCols;++$j)
				    if($MyGrid[$i][$j]) {
					    print $MyGrid[$i][$j];
                    } else {
					    echo '<td colspan="'.(3+($j>0)).'"></td>';
                    }
				print '</tr>';
			}
			print '</table>';
		}
	}

	$POST_TAIL = $JS_RedTarget;
	include('Common/Templates/tail.php');
?>