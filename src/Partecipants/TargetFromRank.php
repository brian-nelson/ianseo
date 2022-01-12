<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclParticipants, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Various.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

$startSession=(isset($_REQUEST['startSession']) ? $_REQUEST['startSession'] : null);
$endSession=(isset($_REQUEST['endSession']) ? $_REQUEST['endSession'] : null);
$filter=((isset($_REQUEST['filter']) AND preg_match("/^[0-9A-Z%_]+$/i",$_REQUEST["filter"])) ? $_REQUEST['filter'] : null);
$isEvent=(isset($_REQUEST['isEvent']) && $_REQUEST['isEvent']==1 ? $_REQUEST['isEvent'] : 0);
$sourceRankFrom=(isset($_REQUEST['sourceRankFrom']) ? $_REQUEST['sourceRankFrom'] : 1);
$sourceRankTo=(isset($_REQUEST['sourceRankTo']) ? $_REQUEST['sourceRankTo'] : 99999);
$destFrom=(isset($_REQUEST['destFrom']) ? $_REQUEST['destFrom'] : null);
$destTo=(isset($_REQUEST['destTo']) ? $_REQUEST['destTo'] : null);

$NumSession=0;
$Row=null;

// sessioni
$sessions=GetSessions('Q');

	$comboStartSession
		= '<select name="startSession" id="startSession">'
		. '<option value="0">' . get_text('AllSessions','Tournament') . '</option>';

	$comboEndSession
		= '<select name="endSession" id="endSession">'
		. '<option value="0">--</option>';

	foreach ($sessions as $s)
	{
		$comboStartSession.='<option value="' . $s->SesOrder. '"' . (!is_null($startSession) && $s->SesOrder==$startSession ? ' selected' : '') . '>' . $s->SesOrder .': ' . $s->SesName . '</option>';
		$comboEndSession.='<option value="' . $s->SesOrder . '"' . (!is_null($endSession) && $s->SesOrder==$endSession ? ' selected' : '') . '>' . $s->SesOrder .': ' . $s->SesName . '</option>';
	}

	$comboStartSession.='</select>';
	$comboEndSession.='</select>';

$msg='';

if (isset($_REQUEST['command'])) {
    if (!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		/* verifico i campi passati */
		// sessione . bersagli di destinazione
        $start=str_pad($destFrom,TargetNoPadding,'0',STR_PAD_LEFT);
        $end=str_pad($destTo,TargetNoPadding,'0',STR_PAD_LEFT);
        if ($endSession>count($sessions)
            || $sessions[$endSession-1]->SesFirstTarget > $destFrom
            || $sessions[$endSession-1]->SesTar4Session+$sessions[$endSession-1]->SesFirstTarget-1 < ($destTo))
        {
            $msg=get_text('Error');
        } else {
	        // la rank iniziale deve essere un numero
            if (!preg_match('/^[0-9]+$/i',$sourceRankFrom)) {
               $msg=get_text('Error');
            } else {
	            $err=false;
                $TargetOrder='ASC';
				// e se è settata, la rank finale deve essere maggiore di quella iniziale
                if ($sourceRankTo!='') {
	                if (!preg_match('/^[0-9]+$/i',$sourceRankTo)) {
		                $msg=get_text('Error');
                        $err=true;
                    } else {
	                    if ($sourceRankFrom>$sourceRankTo) {
		                    $TargetOrder='DESC';
//								$msg=get_text('Error');
//								$err=true;
                        }
                    }
                }

                if (!$err) {
	                // il filtro non deve essere vuoto
                    if (trim($filter)=='') {
	                    $msg=get_text('Error');
                    } else {
                        /* qui dovrei avere i parametri a posto */
                        $data2up=array();

						// tiro fuori i bersagli di destinazione che mi servono
                        $targets=array();
                        $index=0;	// indice di $targets

                        $SubStrLen=strlen($endSession . $start);

                        $query = "SELECT AtTargetNo, substr(AtTargetNo,1,$SubStrLen) Target, substr(AtTargetNo,-1) Letter "
                            . "FROM "
                                . "AvailableTarget "
                            . "WHERE "
                                . "AtTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
                                . "AtTargetNo>='" . $endSession . $start . 'A' . "' AND AtTargetNo<='" . $endSession . $end . 'Z' . "' "
                            . "ORDER BY "
                                . "Target $TargetOrder, Letter ASC ";
                        $rs=safe_r_sql($query);
                        while ($row=safe_fetch($rs)) {
	                        $targets[]=$row->AtTargetNo;
                        }

                        /*print '<pre>';
                        print_r($targets);
                        print '</pre>';exit;*/

                        $rank1=$sourceRankFrom;
                        $rank2=$sourceRankTo!='' ? $sourceRankTo : 999999;

						/*
						 * ora faccio la query per tirar fuori la rank.
						 */
                        $query="";

                        if ($isEvent==0) {
	                        // individuali

							/*
							 * IMPORTANTE!!!
							 * Questa query non tiene conto del flag di partecipazione alla classifica di classe.
							 * Un eventuale confronto con la classifica di classe potrebbe mostrare discrepanze
							 * (l'elenco delle persone di questa query ha un numero di righe >= a quello della classifica di classe)
							 */
                            $query = "SELECT QuId, EnFirstName, EnName, EnWChair, EnDoubleSpace, CONCAT(EnDivision,EnClass) AS `Event`, QuSession, QuTargetNo, QuClRank as `Rank` 
                                FROM Tournament 
                                INNER JOIN Entries ON ToId=EnTournament 
                                INNER JOIN Qualifications ON EnId=QuId 
                                WHERE CONCAT(EnDivision,EnClass) LIKE " . StrSafe_DB($filter) . " AND EnAthlete=1  AND EnStatus<=1 AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuScore<>0 
                                ORDER BY QuClRank ASC, EnFirstName, EnName ";
                        } else {
                            // assoluti individuali
                            $query = "SELECT QuId, EnFirstName, EnName,EnWChair, EnDoubleSpace, CONCAT(EnDivision,EnClass) AS `Event`, QuSession, QuTargetNo, IndRank as `Rank` 
                                FROM Tournament 
                                INNER JOIN Entries ON ToId=EnTournament 
                                INNER JOIN Qualifications ON EnId=QuId 
                                INNER JOIN Individuals ON IndId=EnId AND IndTournament=EnTournament 
                                WHERE EnAthlete=1 AND EnIndFEvent=1 AND EnStatus <= 1  AND QuScore<>'0' AND ToId = " . StrSafe_DB($_SESSION['TourId']) . " AND IndEvent LIKE " . StrSafe_DB($filter) . " 
                                ORDER BY IndRank ASC, EnFirstName, EnName ";
                        }
                        $Rs=safe_r_sql($query);

                        $CurrentRow=-1;

                        while ($MyRow=safe_fetch($Rs)) {
                            /*
                             * Se la persona ha la rank tra quelle selezionate, la sessione è quella selezionata
                             * E HO ancora bersagli di destinazione aggiungo in $data2up
                             */
                            if ($index<count($targets)) {
                                if ($MyRow->Rank>=$rank1 && $MyRow->Rank<=$rank2 && ($startSession==0 || ($MyRow->QuSession!=0 && $MyRow->QuSession==$startSession))) {
                                    $trgt=$targets[0];
                                    $index=0;

                                    if($MyRow->EnWChair) {
                                        // cerca il target B successivo
                                        while($index < count($targets) and substr($targets[$index],-1)!='B') $index++;
                                        // se esiste sposta l'arciere su quel bersaglio ed elimina il 'D' relativo
                                        if($index < count($targets)) {
                                            $trgt=$targets[$index];
                                        } else {
                                            $index=0;
                                        }
                                        if(count($targets)>2) {
                                            array_splice($targets,$index+2,1);
                                        }
                                    }

                                    array_splice($targets,$index,1);

                                    $data2up[]=array
                                        (
                                        'id'=>$MyRow->QuId,
                                        'session'=>$endSession,
                                        'target'=> $trgt
                                        );
                                }
                            }
                        }

                        // faccio gli update
                        if (count($data2up)>0) {
                            foreach ($data2up as $d) {
                                $query = "UPDATE Qualifications
                                    SET QuSession='" . $d['session'] . "',
                                        QuTargetNo='" . $d['target'] . "',
                                        QuTarget='" . intval(substr($d['target'],1)) . "',
                                        QuLetter='" . substr($d['target'], -1) . "',
                                        QuTimestamp=QuTimestamp
                                    WHERE QuId='" . $d['id'] . "' ";
                                //print $query . '<br><br>';
                                $rs=safe_w_SQL($query);
                                if(safe_w_affected_rows()) {
                                    safe_w_sql("Update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where EnId={$d['id']}");
                                    safe_w_sql("UPDATE Qualifications SET QuBacknoPrinted=0, QuTimestamp=QuTimestamp WHERE QuId='{$d['id']}'");
                                }

                                $msg.=get_text('TargetAssigned', 'Tournament',substr($d['target'],1)) . '<br/>';

                            }
                        } else {
                            $msg.=get_text('NoTargetFound') . '<br/>';
                        }
                    }
                }
            }
        }
    } else {
        $msg=get_text('Error');
    }
}

$JS_SCRIPT=array(
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/adapter/ext/ext-base.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/ext-all-debug.js"></script>',
    phpVars2js(array(
        'StrError' => get_text('Error'),
    )),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants/Fun_TargetFromRank.js"></script>'
);

include('Common/Templates/head.php');
?>

<form id="frm" method="post" action="<?php print $_SERVER['PHP_SELF'];?>">
	<table class="Tabella">
		<tr><th class="Title"><?php print get_text('MenuLM_TargetFromRank');?></th></tr>
		<tr class="Divider"><TD></TD></tr>

		<tr><th><?php print get_text('Source');?></th></tr>

		<tr>
			<td class="Center">
				<?php print get_text('Session');?>: <?php print $comboStartSession;?>
				&nbsp;&nbsp;
				<?php print get_text('FilterOnDivCl','Tournament'); ?>: <input type="text" name="filter" id="filter" size="12" maxlength="10" value="<?php print (!is_null($filter) ? $filter : '');?>" />
				&nbsp;&nbsp;
				<input type="checkbox" name="isEvent" id="isEvent" value="1" <?php print ($isEvent==1 ? 'checked="yes"' : '');?>/>&nbsp;<?php print get_text('Event');?>
				&nbsp;&nbsp;
				<?php print get_text('Rank');?>
				<?php print get_text('From','Tournament');?>: <input type="text" name="sourceRankFrom" id="sourceRankFrom" size="4" maxlength="4" value="<?php print $sourceRankFrom;?>" />
				&nbsp;&nbsp;
				<?php print get_text('To','Tournament');?>: <input type="text" name="sourceRankTo" id="sourceRankTo" size="4" maxlength="4" value="<?php print $sourceRankTo;?>" />
			</td>
		</tr>

		<tr class="Divider"><TD></TD></tr>

		<tr><th><?php print get_text('Destination');?></th></tr>

		<tr>
			<td class="Center">
				<?php print get_text('Session');?>: <?php print $comboEndSession;?>
				&nbsp;&nbsp;
				<?php print get_text('From','Tournament'); ?>: <input type="text" name="destFrom" id="destFrom" size="5" maxlength="4" value="<?php print (!is_null($destFrom) ? $destFrom : '');?>" />
				&nbsp;&nbsp;
				<?php print get_text('To','Tournament'); ?>: <input type="text" name="destTo" id="destTo" size="5" maxlength="4" value="<?php print (!is_null($destTo) ? $destTo : '');?>" />
			</td>
		</tr>
		<tr>
			<td class="Center">
				<input type="hidden" name="command" value="OK"/>
				<input type="button" id="btnOk" value="<?php print get_text('CmdOk');?>" />
			</td>
		</tr>
		<?php if ($msg!='') { ?>
			<tr class="Divider"><TD></TD></tr>
			<tr><td><?php print $msg;?></td></tr>
		<?php }?>
	</table>
</form>

<?php

include('Common/Templates/tail.php');
