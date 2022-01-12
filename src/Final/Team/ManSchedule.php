<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    CheckTourSession(true);
    checkACL(AclCompetition, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Phases.inc.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_ManSchedule.js"></script>',
		'<script type="text/javascript" src="../../Common/js/Fun_JS.inc.js"></script>',
		);
	$PAGE_TITLE=get_text('ManFinScheduleTeam');
	include('Common/Templates/head.php');
?>
<script>
function insert_schedule_from_select(event, phase, schedule) {
	if(schedule) {
		bits=schedule.split('§');
		document.getElementById('d_FSScheduledDateAll_' + event + '_' + phase).value=bits[0];
		document.getElementById('d_FSScheduledTimeAll_' + event + '_' + phase).value=bits[1];
		if(document.getElementById('d_FSScheduledLenAll_' + event + '_' + phase))
			document.getElementById('d_FSScheduledLenAll_' + event + '_' + phase).value=bits[2];
	} else {
		document.getElementById('d_FSScheduledDateAll_' + event + '_' + phase).value='';
		document.getElementById('d_FSScheduledTimeAll_' + event + '_' + phase).value='';
		if(document.getElementById('d_FSScheduledLenAll_' + event + '_' + phase))
			document.getElementById('d_FSScheduledLenAll_' + event + '_' + phase).value='';
	}
}
</script>
<form name="Frm" method="post" action="">
<input type="hidden" name="Command" value="OK">
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('ManFinScheduleTeam'); ?></th></tr>
<tr><th colspan="2"><?php print get_text('FilterRules'); ?></th></tr>
<tr class="Divider"><td colspan="2"></td></tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('Event');?></th>
<td class="Bold">
<?php
	$StartPhase=-1;

	$Select = "SELECT EvCode,EvEventName 
		FROM Events 
		WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' AND EvFinalFirstPhase!=0 
		ORDER BY EvProgr ASC ";
	$Rs=safe_r_sql($Select);

	print '<select name="d_Event" id="d_Event">';
	if (safe_num_rows($Rs)>0) {
		while ($Row=safe_fetch($Rs)) {
			print '<option value="' . $Row->EvCode . '"' . (isset($_REQUEST['d_Event']) && $_REQUEST['d_Event']==$Row->EvCode ? ' selected' : '') . '>' . $Row->EvCode . ' - ' . get_text($Row->EvEventName,'','',true) . '</option>' . "\n";
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
<tr><td colspan="2"><?php echo get_text('PhaseTimeDescription', 'Tournament'); ?></td></tr>
</table>
</form>
<?php
	if (isset($_REQUEST['Command']) && $_REQUEST['Command']=='OK')	{
		$Status=0;	// 1 -> errore
		// Estraggo la fase da cui inizia l'EventCode scelto, e la sua descrizione
		$Select	= "SELECT EvCode,EvEventName,EvFinalFirstPhase AS StartPhase 
			FROM Events 
			WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($_REQUEST['d_Event']) . " AND EvTeamEvent=1";
		$RsParam=safe_r_sql($Select);
		$RowPar = NULL;

		if (safe_num_rows($RsParam)==1) {
			$RowPar=safe_fetch($RsParam);
			$StartPhase=$RowPar->StartPhase;

			$GridRows = 2* (valueFirstPhase($StartPhase)) + 2 + 2* (valueFirstPhase($StartPhase));	// righe della griglia
			$alpha=ceil(log10(valueFirstPhase($StartPhase))/log10(2));

			$GridCols=2*$alpha+1;	// Colonne della griglia

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

		// Il ciclo gestisce fino alla semifinale
			$CurPhase = valueFirstPhase($StartPhase);

			$TabIndex = 1;

			while ($CurPhase>1 && $Status==0)
			{
				$AthPrinted = 0;	// Numero di arcieri stampati
				$TotAthPrinted =0; 	// numero totali di arcieri stampati per la fase
				$Row=0;

			// Estraggo la griglia della fase $CurPhase
				$Select = "SELECT GrPhase, GrPosition, GrPosition2, GrMatchNo, TFEvent, 
					DATE_FORMAT(FSScheduledDate,'".get_text('DateFmtDB')."') AS Dt,DATE_FORMAT(FSScheduledTime,'".get_text('TimeFmt')."') AS Hr, FSScheduledLen AS MatchLen 
					FROM TeamFinals 
					JOIN Grids ON TFMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($CurPhase) . " 
					LEFT JOIN FinSchedule ON TFEvent=FSEvent AND TFMatchNo=FSMatchNo AND (FSTeamEvent=1 OR FSTeamEvent IS NULL) AND TFTournament=FSTournament 
					WHERE TFTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TFEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " 
					ORDER BY  GrPhase DESC , GrMatchNo ASC ";
				$Rs=safe_r_sql($Select);
				//print $Select . '<br><br>';
				if (safe_num_rows($Rs)>0) {
				// righe di testa della fase
					for ($i=0;$i<=$HeadRows+2;++$i) {
						$Txt = '';

                        $Colspan = ($CurPhase==valueFirstPhase($StartPhase) ? '3' : '4');
                        $Input
							= '<input type="text" maxlength="10" size="10" id="d_FSScheduledDateAll_' . $_REQUEST['d_Event'] . '_' . $CurPhase . '" value="">@'
							. '<input type="text" maxlength="5" size="5" id="d_FSScheduledTimeAll_' . $_REQUEST['d_Event'] . '_' . $CurPhase . '" value="">'
							. (!defined('hideSchedulerAndAdvancedSession') ? '&nbsp;/&nbsp;<input type="text" maxlength="5" size="5" id="d_FSScheduledLenAll_' . $_REQUEST['d_Event'] . '_' . $CurPhase . '" value="">' : '')
							. '<br>'
							. get_text('DateTimeViewFmt') . (!defined('hideSchedulerAndAdvancedSession') ? ' / ' . get_text('MatchMins','Tournament'):''). '<br>'
							. '<a class="Link" href="javascript:WriteScheduleAll(\'' . $_REQUEST['d_Event'] . '\',\'' . $CurPhase . '\');">' . get_text('CmdSet2All') . '</a>';

						if ($i==1) {
							$Txt = '<th nowrap class="Center" colspan="' . $Colspan. '">' . get_text(namePhase($StartPhase,$CurPhase) . '_Phase') . '</th>';
						} elseif ($i==2) {
							$Txt = '<td>&nbsp;</td><td nowrap colspan="' . ($Colspan-1) . '">'
								. $Input
								. get_already_scheduled_events($CurPhase, $_REQUEST['d_Event'], 1)
								. '</td>';
						} else {
							$Txt = '<td  nowrap class="Center" colspan="' . $Colspan. '">&nbsp;</td>';
						}

						$MyGrid[$Row++][$Col].= $Txt;
					}

					while ($MyRow=safe_fetch($Rs)) {
						if (!isFirstPhase($StartPhase,$MyRow->GrPhase)) {
							$TipoBordo=($Ultima%2==0 ? 'Bottom' : '');
							$MyGrid[$Row][$Col].= '<td  nowrap class="Center ' . $TipoBordo . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
						}
					// posizione
						$MyGrid[$Row][$Col].= '<td  nowrap class="' . ($AthPrinted==1 ? 'Bottom ' : '') . 'Top wRight Left Center">' . (useGrPostion2($StartPhase, $CurPhase) ? ($MyRow->GrPosition2 ? $MyRow->GrPosition2 : '&nbsp;') : $MyRow->GrPosition) . '</td>';
					// data/ora
						if ($AthPrinted==0) {
							$Dt = (!is_null($MyRow->Dt) /*&& $MyRow->Dt!='00-00-0000'*/ ? $MyRow->Dt : '');
							$Hr = (!is_null($MyRow->Hr) ? $MyRow->Hr : '');
							$mLn = (!is_null($MyRow->MatchLen) ? $MyRow->MatchLen : '');
							$MyGrid[$Row][$Col]
								.='<td nowrap rowspan="2" class="Bottom Top Right Left">'
								. '<input type="text" tabindex="' . ($TabIndex++) . '" maxlength="10" size="10" name="d_FSScheduledDate_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '" id="d_FSScheduledDate_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '" value="' . $Dt . '" onBlur="javascript:WriteSchedule(\'d_FSScheduledDate_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '\');">@'
								. '<input type="text" tabindex="' . ($TabIndex++) . '" maxlength="5" size="5" name="d_FSScheduledTime_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '" id="d_FSScheduledTime_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '" value="' . $Hr . '"  onBlur="javascript:WriteSchedule(\'d_FSScheduledTime_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '\');">'
								. (!defined('hideSchedulerAndAdvancedSession') ? '&nbsp;/&nbsp;<input type="text" tabindex="' . ($TabIndex++) . '" maxlength="5" size="5" name="d_FSScheduledLen_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '" id="d_FSScheduledLen_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '" value="' . $mLn . '"  onBlur="javascript:WriteSchedule(\'d_FSScheduledLen_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '\');">' : '')
								. '</td>';
						}

					// tie
						$MyGrid[$Row][$Col].= '<td nowrap class="' . ($AthPrinted==1 ? 'wBottom Top' : 'wTop ') . ' wRight wLeft">';
						$MyGrid[$Row][$Col].=  '&nbsp;';
						$MyGrid[$Row][$Col].=  '</td>';

						++$AthPrinted;
						++$TotAthPrinted;

					// ogni due arcieri stampo le righe mediane
						if ($AthPrinted==2 && $TotAthPrinted!=valueFirstPhase($CurPhase)*2) {
							for ($i=1;$i<=$MiddleRows;++$i) {
								if(!array_key_exists($Col,$MyGrid[++$Row]))
									$MyGrid[$Row][$Col]=null;
								$MyGrid[$Row][$Col].= '<td nowrap class="Center" colspan="' . $Colspan. '">&nbsp;</td>';
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
					for ($i=1;$i<=$HeadRows+4;++$i) {
						if(!array_key_exists($Row,$MyGrid)) {
                            $MyGrid[$Row] = array();
                        }
						if(!array_key_exists($Col,$MyGrid[$Row])) {
                            $MyGrid[$Row][$Col] = NULL;
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
								++$kk;
								if ($kk>(-2+(valueFirstPhase($StartPhase))*4-$MiddleRows)) {
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
					$CurPhase/=2;	// dimezzo la fase

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
			$Select
				= "SELECT GrPhase,GrPosition, GrPosition2, GrMatchNo, TFEvent,
				DATE_FORMAT(FSScheduledDate,'".get_text('DateFmtDB')."') AS Dt,DATE_FORMAT(FSScheduledTime,'".get_text('TimeFmt')."') AS Hr, FSScheduledLen AS MatchLen 
				FROM TeamFinals INNER JOIN Grids ON TFMatchNo=GrMatchNo 
				LEFT JOIN FinSchedule ON TFEvent=FSEvent AND TFMatchNo=FSMatchNo AND (FSTeamEvent='1' OR FSTeamEvent IS NULL) AND TFTournament=FSTournament 
				WHERE TFTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TFEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " AND GrPhase<='1' 
				ORDER BY GrPhase ASC , GrMatchNo ASC ";
			$Rs=safe_r_sql($Select);
			//print $Select;
			$Row=0;

			if (safe_num_rows($Rs)==4) {
				$AthPrinted=0;
				$Ultima=0;
				$MiddleRows=2;
			// righe di testa della fase
				for ($i=0;$i<=$HeadRows+2;++$i) {
				// se sto stampando l'ultima riga di testa scrivo la fase
					//$MyGrid[$Row++][$Col].= '<td  nowrap class="Center" colspan="5">' . ($i==$HeadRows ? get_text('0_Phase') : '&nbsp;') . '</td>';
					//$MyGrid[$Row++][$Col].= '<td  nowrap class="Center" colspan="5">' . ($i==$HeadRows ? get_text('0_Phase') : ($i==0 ? $Bottone : '&nbsp;')) . '</td>';

					$Txt = '';

					$Input
						= '<input type="text" maxlength="10" size="10" id="d_FSScheduledDateAll_' . $_REQUEST['d_Event'] . '_1" value="">@'
						. '<input type="text" maxlength="5" size="5" id="d_FSScheduledTimeAll_' . $_REQUEST['d_Event'] . '_1" value="">'
						. (!defined('hideSchedulerAndAdvancedSession') ? '&nbsp;/&nbsp;<input type="text" maxlength="5" size="5" id="d_FSScheduledLenAll_' . $_REQUEST['d_Event'] . '_1" value="">' : '')
						. '<br>'
						. get_text('DateTimeViewFmt') . (!defined('hideSchedulerAndAdvancedSession') ? ' / ' . get_text('MatchMins','Tournament'):''). '<br>'

						. '<a class="Link" href="javascript:WriteScheduleAll(\'' . $_REQUEST['d_Event'] . '\',\'1\');">' . get_text('CmdSet2All') . '</a>';

					if ($i==1) {
                        $Txt = '<th nowrap class="Center" colspan="3">' . get_text('0_Phase') . '/' . get_text('1_Phase') . '</th>';
                    } elseif ($i==2) {
                        $Txt = '<td colspan="2">&nbsp;</td><td nowrap class="" colspan="' . ($Colspan - 2) . '">'
                            . $Input
                            . get_already_scheduled_events(1, $_REQUEST['d_Event'], 1)
                            . '</td>';
                    } else {
                        $Txt = '<td  nowrap class="Center" colspan="3">&nbsp;</td>';
                    }
					$MyGrid[$Row++][$Col].= $Txt;
				}

				while ($MyRow=safe_fetch($Rs)) {
				// righe mediane ogni due arcieri
					if ($AthPrinted==2) {
						for ($i=1;$i<=$MiddleRows;++$i) {
							$MyGrid[$Row++][$Col].= '<td  nowrap class="Center" colspan="3">&nbsp;</td>';
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
					$MyGrid[$Row][$Col].= '<td nowrap class="Center ' . $TipoBordo . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';

				// posizione
					$MyGrid[$Row][$Col].= '<td  nowrap class="' . ($AthPrinted==1 ? 'Bottom ' : '') . 'Top wRight Left">' . (useGrPostion2($StartPhase, $CurPhase) ? ($MyRow->GrPosition2 ? $MyRow->GrPosition2 : '&nbsp;') : $MyRow->GrPosition) . '</td>';
					if ($AthPrinted==0) {
				// data/ora
						$Dt = (!is_null($MyRow->Dt) /*&& $MyRow->Dt!='00-00-0000'*/ ? $MyRow->Dt : '');
						$Hr = (!is_null($MyRow->Hr) ? $MyRow->Hr : '');
						$mLn = (!is_null($MyRow->MatchLen) ? $MyRow->MatchLen : '');
						$MyGrid[$Row][$Col]
							.='<td nowrap rowspan="2" class="Bottom Top Right Left">'
							. '<input type="text" tabindex="' . ($TabIndex++) . '" maxlength="10" size="10"  id="d_FSScheduledDate_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '" value="' . $Dt . '" onBlur="javascript:WriteSchedule(\'d_FSScheduledDate_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '\');">@'
							. '<input type="text" tabindex="' . ($TabIndex++) . '" maxlength="5" size="5" id="d_FSScheduledTime_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '" value="' . $Hr . '" onBlur="javascript:WriteSchedule(\'d_FSScheduledTime_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '\');">'
							. (!defined('hideSchedulerAndAdvancedSession') ? '&nbsp;/&nbsp;<input type="text" tabindex="' . ($TabIndex++) . '" maxlength="5" size="5" name="d_FSScheduledLen_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '" id="d_FSScheduledLen_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '" value="' . $mLn . '"  onBlur="javascript:WriteSchedule(\'d_FSScheduledLen_' . $MyRow->TFEvent . '_' . $MyRow->GrMatchNo . '\');">' : '')
							. '</td>';
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
		}

		if ($Status==0) {
			print '<table class="Griglia">';
			for ($i=0;$i<$GridRows+($StartPhase==2 ? 3 : 0);++$i) {
				print '<tr>';
				for ($j=0;$j<$GridCols;++$j)
					print $MyGrid[$i][$j];
				print '</tr>';
			}
			print '</table>';
		}
	}

	include('Common/Templates/tail.php');
?>
