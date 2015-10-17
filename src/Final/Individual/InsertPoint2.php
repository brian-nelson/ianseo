<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Final/Fun_ChangePhase.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_Phases.inc.php');
	require_once('HHT/Fun_HHT.local.inc.php');

	if (!CheckTourSession() || !isset($_REQUEST['d_Phase'])) PrintCrackError();

	$Cols2Remove = (isset($_REQUEST['d_Tie']) && $_REQUEST['d_Tie']==1 ? 0 : 2);

	$Error=false;

	$Tie_Error = array();	// Contiene gl'indici delle tendine Tie con errore
	$Score_Error = array(); // contiene gl'indici degli score che superano il max
	$Set_Error = array(); // contiene gl'indici dei set che superano il max

	if (isset($_REQUEST['Command']) && $_REQUEST['Command']=='SAVE' && !IsBlocked(BIT_BLOCK_IND))
	{

	// Update dei punti e dei tie
		$AllowedEvents=array();
		foreach ($_REQUEST as $Key => $Value)
		{
			if (strpos($Key,'S_')===0)
			{
				if (!(is_numeric($Value) && $Value>=0))
				{
					$Value=0;
				}

				$ee=""; $mm="";	// evento e matchno estratti dal nome campo
				list($ee,$mm)=explode('_',substr($Key,2));

				// get the match maximum values
				$MaxScores=GetMaxScores($ee, $mm);

				if(empty($AllowedEvents[$ee])) {
					// dal matchno recupero la fase
					$t=safe_r_sql("select GrPhase from Grids where GrMatchNo=".intval($mm));
					if($u=safe_fetch($t)) $AllowedEvents[$ee]=$u->GrPhase;
				}

				if ($Value > ($MaxScores['MaxSetPoints'] ? $MaxScores['MaxSetPoints'] : $MaxScores['MaxMatch']) )
				{
					$Score_Error[$ee . '_' . $mm]=true;
				}
				else
				{

					if (debug) print $mm . ' - ';

					$Update
						= "UPDATE Finals SET "
						. (isset($_REQUEST['X_' . $ee . '_' . $mm]) && $_REQUEST['X_' . $ee . '_' . $mm]==0 ? "FinScore" : "FinSetScore") . "=" . StrSafe_DB($Value) . " ";

					//Cerco i punti dei set, SE gara a set
					if(isset($_REQUEST['X_' . $ee . '_' . $mm]) && $_REQUEST['X_' . $ee . '_' . $mm]!=0)
					{
						if(isset($_REQUEST['P_' . $ee . '_' . $mm]) && is_array($_REQUEST['P_' . $ee . '_' . $mm]))
						{
							//Valido i punti set POI decido se mi vanno bene o no
							foreach($_REQUEST['P_' . $ee . '_' . $mm] as $setPoint)
							{
								if((!is_numeric($setPoint) && $setPoint!='') or $setPoint > $MaxScores['MaxEnd'])
									$Set_Error[$ee . '_' . $mm]=true;
							}
							$Update.= ",FinSetPoints='" . implode("|",$_REQUEST['P_' . $ee . '_' . $mm]) . "' ";
							$Update.= ",FinScore='" . array_sum($_REQUEST['P_' . $ee . '_' . $mm]) . "' ";
						}
					}

					//Cerco i Tie
					if (isset($_REQUEST['T_' . $ee . '_' . $mm]))
					{
					// devo scrivere i punti di tie
						$tiebreak = '';

						if (isset($_REQUEST['t_' . $ee . '_' . $mm]) && is_array($_REQUEST['t_' . $ee . '_' . $mm]))
						{
							foreach ($_REQUEST['t_' . $ee . '_' . $mm] as $TieKey => $TieValue)
							{
								$tiebreak.=(GetLetterFromPrint($TieValue)!=' ' ? GetLetterFromPrint($TieValue) : ' ');
							}
						}
						$Update.= ",FinTieBreak=" . StrSafe_DB($tiebreak) . " ";

					// devo settare la tendina del tie
						$t="";

						if (!(is_numeric($_REQUEST['T_' . $ee .'_' . $mm]) && $_REQUEST['T_' . $ee .'_' . $mm]>=0))
						{
							$t=0;
						}
						else
						{
							$t=$_REQUEST['T_' . $ee .'_' . $mm];
						}
					/*
						Nel caso $t=1 la verifica che sia assegnato a chi ha l'ultima freccia di tiebreak più alta è fatta dai passaggi di classe.
						Segnalo se entrambe le persone hanno il bye
					*/
						$TieOk=true;
						if ($t==2)
						{
							$IndexOfOne = $ee .'_' . $mm;
							$First = '';
							$Last = '';

							if ($mm%2==0)
							{
								$First = $ee .'_' . $mm;
								$Last = $ee .'_' . ($mm+1);
							}
							else
							{
								$First = $ee .'_' . ($mm-1);
								$Last = $ee .'_' . $mm;
							}

							if ($_REQUEST['T_' . $First]==2 && $_REQUEST['T_' . $Last]==2)
								$TieOk=false;
							else
								$TieOk=true;
						}

						if ($TieOk)
							$Update.= ",FinTie=" . StrSafe_DB($t) . " ";
						else
							$Tie_Error[$ee .'_' . $mm]=true;
					}

					$Update
						.=",FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
						. "WHERE FinEvent='" . $ee . "' AND FinMatchNo='" . $mm . "' AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$RsUp=safe_w_sql($Update);
					if (debug)
						print $Update . '<br><br>';
				}
			}
		}

	// Faccio i passaggi di fase
		foreach($AllowedEvents as $event => $phase) move2NextPhase($phase, $event);
	}

	$PAGE_TITLE=get_text('MenuLM_Data insert (Table view)');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Individual/Fun_JS.js"></script>',
		);

	include('Common/Templates/head.php');
?>
<form name="Frm" method="post" action="InsertPoint2.php">
<input type="hidden" name="Command" value="">
<?php

$useSession=false;

$PrecPhase = '';//($_REQUEST['d_Phase']==0 ? 1 : ($_REQUEST['d_Phase']==32 ? 48 :$_REQUEST['d_Phase']*2));
$NextPhase = '';//($_REQUEST['d_Phase']>1 ? ($_REQUEST['d_Phase']==48 ? 32 : ($_REQUEST['d_Phase']==24 ? 16 : $_REQUEST['d_Phase']/2)) : 0);

$PP=$PrecPhase;
$NP=$NextPhase;
$Sch=0;

if(!empty($_REQUEST['d_Event'])) {
	foreach($_REQUEST['d_Event'] as $key => $val) {
		echo '<input type="hidden" name="d_Event[]" id="d_Event_'.$key.'" value="'.$val.'">' . "\n";
	}
}

if(!empty($_REQUEST['x_Session']) and $_REQUEST['x_Session']!=-1)
{
	$useSession=true;

	echo '<input type="hidden" name="x_Session" id="x_Session" value="'.$_REQUEST['x_Session'].'">' . "\n";

	$ComboArr=array();
	ComboSes(RowTour(), 'Individuals',$ComboArr);

// schedule attuale
	$actual=array_search($_REQUEST['x_Session'],$ComboArr);

/*
 * La precedente deve esser per forza diversa da false ma nel caso non faccio nulla
 */
	if ($actual!==false)
	{
	// prima imposto l'inizio e la fine uguale all'attuale
		$PP=$ComboArr[$actual];
		$NP=$ComboArr[$actual];

	// poi metto a posto

		if ($actual!=0)
		{
			$PP=$ComboArr[$actual-1];
		}

		if ($actual!=(count($ComboArr)-1))
		{
			$NP=$ComboArr[$actual+1];
		}
	}

	$Sch=1;
}
?>
<input type="hidden" name="d_Phase" id="d_Phase" value="<?php print $_REQUEST['d_Phase'];?>">
<input type="hidden" name="d_Tie" id="d_Tie" value="<?php print $_REQUEST['d_Tie'];?>">
<input type="hidden" name="d_SetPoint" id="d_SetPoint" value="<?php print $_REQUEST['d_SetPoint'];?>">
<table class="Tabella">
<tr><th class="Title" colspan="<?php print (8-$Cols2Remove);?>"><?php print get_text('IndFinal'); ?></th></tr>
<tr class="Divider"><td colspan="<?php print (8-$Cols2Remove);?>"></td></tr>
<?php

	$QueryFilter = '';

	// if a scheduled round has been sent, it superseeds everything
	if(!empty($_REQUEST['x_Session']) and $_REQUEST['x_Session']!=-1) {
		// get all the pairs event<=>phase for that scheduled time
		$QueryFilter.= "AND concat(FSTeamEvent, FSScheduledDate, ' ', FSScheduledTime)=" . strSafe_DB($_REQUEST['x_Session']) . " ";
	} elseif(!empty($_REQUEST['d_Event'])) {
		// creates the filter on the matching events and phase
		$tmp=array();
		foreach($_REQUEST['d_Event'] as $event) $tmp[]=StrSafe_DB($event);
		sort($tmp);
		if($tmp) $QueryFilter.= "AND FinEvent in (" . implode(',', $tmp) . ") ";
		$QueryFilter.= "AND GrPhase=" . StrSafe_DB($_REQUEST['d_Phase']) . " ";
	}

//	Tiro fuori l'elenco degli eventi non spareggiati

	$Select
		= "SELECT distinct EvCode, EvEventName, EvMatchMode "
		. "FROM Finals "
		. "left JOIN FinSchedule ON FinEvent=FSEvent AND FsMatchNo=FinMatchNo AND FsTeamEvent='0' AND FsTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
		. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvShootOff='0' " . $QueryFilter
		. "ORDER BY EvProgr ASC,GrMatchNo ASC ";
	$Rs=safe_r_sql($Select);

	$Elenco = '';
	while ($Row=safe_fetch($Rs))
	{
		$Elenco.= get_text($Row->EvEventName,'','',true) . ' (' . $Row->EvCode . ')<br>';
	}

	if ($Elenco!='')
	{
		print '<tr><td class="Bold" colspan="' . (8-$Cols2Remove) . '">' . get_text('IndFinEventWithoutShootOff') . '</td></tr>' . "\n";
		print '<tr><td colspan="' . (8-$Cols2Remove) . '">';
		print $Elenco;
		print '</td>';
		print '</tr>' . "\n";
		print '<tr class="Divider"><td colspan="' . (8-$Cols2Remove) . '"></td></tr>' . "\n";
	}

// tiro fuori solo gli eventi spareggiati
	$Select
		= "SELECT FinEvent,FinMatchNo,FinTournament,FinAthlete, IF(EvMatchMode=0,FinScore,FinSetScore) AS Score, FinTie, FinTiebreak, FinSetPoints,  /* Finals*/ "
		. "EvProgr,EvFinalFirstPhase,EvEventName, EvMatchMode, EvMatchArrowsNo, FsTarget,	/* Events*/ "
		. "GrMatchNo,GrPhase,IF(EvFinalFirstPhase=48 || EvFinalFirstPhase=24,GrPosition2, GrPosition) as GrPosition,	/* Grids */ "
		. "CONCAT(EnFirstName,' ',EnName) AS Athlete,EnCountry,	/* Entries*/ "
		. "CoCode,CoName	/* Countries */ "
		. "FROM Finals "
		. "left JOIN FinSchedule ON FinEvent=FSEvent AND FsMatchNo=FinMatchNo AND FsTeamEvent='0' AND FsTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
		. "LEFT JOIN Entries ON FinAthlete=EnId "
		. "LEFT JOIN Countries ON EnCountry=CoId "
		. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvShootOff='1' " . $QueryFilter
		. "ORDER BY EvProgr ASC,GrMatchNo ASC ";
	$Rs=safe_r_sql($Select);
	//print $Select;

	if (safe_num_rows($Rs)>0)
	{
		$MyEvent = '-----';
		while ($MyRow=safe_fetch($Rs))
		{
			$obj=getEventArrowsParams($MyRow->FinEvent,$MyRow->GrPhase,0);
			if ($MyEvent!=$MyRow->FinEvent)
			{
				$ii=0;
				$StileRiga="";
				if ($MyEvent!='-----')
				{
					print '<tr><td colspan="' . (8-$Cols2Remove) . '" class="Center"><input type="submit" value="' . get_text('CmdSave') . '" onclick="document.Frm.Command.value=\'SAVE\'"></td></tr>' . "\n";
					print '<tr class="Divider"><td></td></tr>' . "\n";
				}
				print '<tr><th colspan="' . (8-$Cols2Remove) . '" class="Title">' . get_text($MyRow->EvEventName,'','',true) . ' (' . $MyRow->FinEvent . ') - ' . get_text('Phase') . ' ' . get_text(namePhase($MyRow->EvFinalFirstPhase,$MyRow->GrPhase) . '_Phase') . '</th></tr>' . "\n";
				print '<tr>';
				print '<td colspan="' . (8-$Cols2Remove) . '">';
				//$PrecPhase = ($_REQUEST['d_Phase']==0 ? 1 : ($_REQUEST['d_Phase']==32 ? 48 :$_REQUEST['d_Phase']*2));
				//$NextPhase = ($_REQUEST['d_Phase']>1 ? ($_REQUEST['d_Phase']==48 ? 32 : ($_REQUEST['d_Phase']==24 ? 16 : $_REQUEST['d_Phase']/2)) : 0);
				//print '<a class="Link" href="javascript:ChangePhase(' . $PrecPhase . ');">' . get_text('PrecPhase') . '</a>'

				if (!$useSession)
				{
					list($PP,$NP)=PrecNextPhaseForButton();
				// queste due solo per i nomi e gli score del turno successivo (e volendo precedente)
					$PrecPhase=$PP;
					$NextPhase=$NP;
				}

				print '<a class="Link" href="javascript:ChangePhase(\'' . $PP . '\',' .$Sch .');">' . get_text('PrecPhase') . '</a>'
					//. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="Link" href="javascript:ChangePhase(' . $NextPhase . ');">' . get_text('NextPhase') . '</a>'
					. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="Link" href="javascript:ChangePhase(\'' . $NP . '\',' .$Sch .');">' . get_text('NextPhase') . '</a>'
					. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="Link" href="PrnIndividual.php?Event='.$MyRow->FinEvent.'&IncBrackets=1&ShowTargetNo=1" target="Griglie">' . get_text('Brackets') . '</a>';
				if($_REQUEST['d_Phase']>0) {
					print ''
						. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="Link" href="PDFScoreMatch.php?Event='.$MyRow->FinEvent.'&Phase='.$NextPhase.'" target="Scores">' . get_text('NextMatchScores') . '</a>'
						. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="Link" href="PrnName.php?Event='.$MyRow->FinEvent.'&Phase='.$NextPhase.'" target="Names">' . get_text('NextMatchNames') . '</a>'
						;
				}
				print '</td>';
				print '</tr>' . "\n";
				print '<tr>';
				print '<th width="5%">' . get_text('Target') . '</th>';
				print '<th width="5%">' . get_text('Rank') . '</th>';
				print '<th width="30%">' . get_text('Athlete') . '</th>';
				print '<th width="5%">' . get_text('Country') . '</th>';

				if($MyRow->EvMatchMode!=0)
					print '<th width="10%">' . get_text('SetPoints', 'Tournament') . '</th>';
				print '<th width="20%" colspan="' . ($MyRow->EvMatchMode==0 ? '2':'1') . '">' . get_text('Score', 'Tournament') . '</th>';
				if (isset($_REQUEST['d_Tie']) && $_REQUEST['d_Tie']==1)
				{
					print '<th width="5%">' . get_text('ShotOff', 'Tournament') . '</th>';
					print '<th width="10%">' . get_text('TieArrows') . '</th>';
				}
				print '</tr>' . "\n";
			}
			print '<tr class="' . $StileRiga . '">';
			print '<td class="Center">' . $MyRow->FsTarget . '</td>';
			print '<td class="Center">' . $MyRow->GrPosition . '</td>';
			print '<td>' . (strlen($MyRow->Athlete)>0 ? $MyRow->Athlete : '&nbsp;') . /*' ' . $MyRow->FinAthlete .*/ '</td>';
			print '<td>' . (!is_null($MyRow->CoCode) ? $MyRow->CoCode : '&nbsp;') . '</td>';
			$TextStyle = '';
			if (array_search($MyRow->FinEvent . '_' . $MyRow->GrMatchNo,array_keys($Score_Error))!==false)
					$TextStyle='error';
			//Carico i punti
			print '<td class="Center" colspan="' . ($MyRow->EvMatchMode==0 ? '2':'1') . '">';
			print '<input type="text" class="' . $TextStyle . '" size="3" maxlength="3" name="S_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '" value="' . ($TextStyle=='' ? $MyRow->Score : $_REQUEST['S_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo]) . '">';
			print '<input type="hidden" name="X_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '" value="' . $MyRow->EvMatchMode . '">';
			print '</td>';
			//Carico i set point in math di tipo set
			if($MyRow->EvMatchMode!=0)
			{
				$TextStyle = '';
				if (array_search($MyRow->FinEvent . '_' . $MyRow->GrMatchNo,array_keys($Set_Error))!==false)
					$TextStyle='error';
				$setPointsArray=explode("|",$MyRow->FinSetPoints);
				print '<td width="10%">';
				for($setNo=0; $setNo<$obj->ends; $setNo++)
				{
					if (isset($_REQUEST['d_SetPoint']) && $_REQUEST['d_SetPoint']==1)
						print '<input type="text" class="' . $TextStyle . '" size="2" maxlength="2" name="P_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '[' . $setNo . ']" value="' . ($TextStyle=='' ? (isset($setPointsArray[$setNo]) ? $setPointsArray[$setNo]:'') : $_REQUEST['P_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo][$setNo]) . '">&nbsp;';
					else
						print (isset($setPointsArray[$setNo]) ? $setPointsArray[$setNo]:'') . '&nbsp;&nbsp;&nbsp;';
				}
				print '</td>';
			}

			if (isset($_REQUEST['d_Tie']) && $_REQUEST['d_Tie']==1)
			{
				//print '<td class="Center"><input type="text" size="4" name="T_' . $MyRow->Event . '_' . $MyRow->MatchNo . '" value="' . $MyRow->tie . '"></td>';
				print '<td class="Center">';
				$ComboStyle = '';
				if (array_search($MyRow->FinEvent . '_' . $MyRow->GrMatchNo,array_keys($Tie_Error))!==false)
					$ComboStyle='error';

				print '<select class="' . $ComboStyle . '" name="T_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '" id="T_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '">' . "\n";
				print '<option value="0"' . (($ComboStyle=='' && $MyRow->FinTie==0) || ($ComboStyle!='' && $_REQUEST['T_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo]==0) ? ' selected' : '') . '>0 - ' .	get_text('NoTie', 'Tournament') . '</option>' . "\n";
				print '<option value="1"' . (($ComboStyle=='' && $MyRow->FinTie==1) || ($ComboStyle!='' && $_REQUEST['T_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo]==1) ? ' selected' : '') . '>1 - ' .	get_text('TieWinner', 'Tournament') . '</option>' . "\n";
				print '<option value="2"' . (($ComboStyle=='' && $MyRow->FinTie==2) || ($ComboStyle!='' && $_REQUEST['T_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo]==2) ? ' selected' : '') . '>2 - ' .	get_text('Bye') . '</option>' . "\n";
				print '</select>' . "\n";
				print '</td>';

				print '<td id="Tie_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '">';
				$TieBreak = str_pad($MyRow->FinTiebreak,$obj->so,' ',STR_PAD_RIGHT);
				for ($i=0;$i<$obj->so;++$i)
					print '<input type="text" name="t_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '[]" size="2" maxlength="3" value="' . DecodeFromLetter($TieBreak[$i]) . '">&nbsp;';
				print '</td>';
			}
			print '</tr>' . "\n";
			if (++$ii==2)
			{
				$StileRiga=($StileRiga=="" ? $StileRiga="warning" : "");
				$ii=0;
			}

			$MyEvent=$MyRow->FinEvent;
		}
		print '<tr><td colspan="' . (8-$Cols2Remove) . '" class="Center"><input type="submit" value="' . get_text('CmdSave') . '" onclick="document.Frm.Command.value=\'SAVE\'"></td></tr>' . "\n";
	}
	else
	{
		print '<tr>';
		print '<td colspan="' . (8-$Cols2Remove) . '">';
		//$PrecPhase = ($_REQUEST['d_Phase']==0 ? 1 : ($_REQUEST['d_Phase']==32 ? 48 :$_REQUEST['d_Phase']*2));
		//$NextPhase = ($_REQUEST['d_Phase']>1 ? ($_REQUEST['d_Phase']==48 ? 32 : ($_REQUEST['d_Phase']==24 ? 16 : $_REQUEST['d_Phase']/2)) : 0);
		//print '<a class="Link" href="javascript:ChangePhase(' . $PrecPhase . ');">' . get_text('PrecPhase') . '</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="Link" href="javascript:ChangePhase(' . $NextPhase . ');">' . get_text('NextPhase') . '</a>';

		list($PP,$NP)=PrecNextPhaseForButton();
		print '<a class="Link" href="javascript:ChangePhase(\'' . $PP . '\',' . $Sch.');">' . get_text('PrecPhase') . '</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="Link" href="javascript:ChangePhase(\'' . $NP . '\','.$Sch.');">' . get_text('NextPhase') . '</a>';
		print '</td>';
		print '</tr>' . "\n";
	}

?>
</table>
</form>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>