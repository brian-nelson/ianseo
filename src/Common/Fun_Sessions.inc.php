<?php
require_once('Common/Fun_Phases.inc.php');

/*
 * Funzioni per la gestione delle sessioni.
 * Ci sono cose per ritornare l'elenco delle sessioni; funzioni solo per le sessioni di
 * un certo tipo etc...
 */

/**
 * Ritorna un array con i tipi di sessione.
 * La chiave è il tipo di sessione e il valore la descrizione già localizzata
 *
 * @return mixed[]
 */
function GetSessionsTypes()
	{
		return array
		(
			'Q'=>get_text('QualRound'),
			'E'=>get_text('Elimination'),
			'F'=>get_text('MenuLM_Final Rounds')
		);
	}

/**
 * GetSessions().
 * Ritorna l'elenco di tutte le sessioni.
 * La chiave sarà SesOrder_SesType e la descrizione cambierà a seconda del valore di $extend
 *
 * @param char $type: tipo di sessione; null => tutte, Q qualifica E eliminatorie F final
 * @param bool $extend: true per la descrizione estesa: 'Nome (Tipo Ordine)' false per quella breve: Nome (Ordine)
 * @param string[] $ids: vettore di id. se specificato verranno ritornati quelli
 * @param int $tour: null considera il torneo aperto se no quello passato.
 *
 * @return mixed: array con l'elenco delle sessioni con la descrizione impostata in base a $extend e l'id SesOrder_SesType
 */
function GetSessions($type=null,$extend=false,$ids=null,$tour=null)
{
	$tour=(!is_null($tour) ? $tour : $_SESSION['TourId']);

	$sessionsTypes=GetSessionsTypes();

	$filter="SesTournament=" . StrSafe_DB($tour) . " ";

	if (is_null($ids))
	{
		if (!is_null($type) && array_key_exists($type,$sessionsTypes))
		{
			$filter.="AND SesType='" . $type . "' ";
		}
	}
	else
	{
		if (!is_array($ids)) $ids=array($ids);

		$in=array();
		for ($i=0;$i<count($ids);++$i)
		{
			$in[]=StrSafe_DB($ids[$i]);
		}

		$filter.="AND CONCAT(SesOrder,'_',SesType) IN (" . implode(',',$in) . ") ";
	}

	$ret=array();

	$q="SELECT * FROM Session WHERE {$filter} ORDER BY locate(SesType, 'QEF'), SesOrder ";
	$r=safe_r_sql($q);
	if ($r && safe_num_rows($r)>0)
	{
		while ($row=safe_fetch($r))
		{
			$tmp=$row;

			$tmp->Id=$row->SesOrder.'_'.$row->SesType;

			if ($row->SesName!='')
			{
				$tmp->Descr=$row->SesOrder.': ' . $row->SesName;
				if ($extend)
				{
					$tmp->Descr=$row->SesName . ' (' . $sessionsTypes[$row->SesType] . ' ' . $row->SesOrder .')';
				}
			}
			else
			{
				$tmp->Descr=$tmp->SesOrder;
			}
			$ret[]=$tmp;

		}
	}

	return $ret;
}

	function GetNumQualSessions()
	{
		$q="SELECT ToNumSession FROM Tournament WHERE ToId={$_SESSION['TourId']}";
		$r=safe_r_sql($q);

		return safe_fetch($r)->ToNumSession;
	}

/**
 * CreateElimRows().
 * Crea le righe per le eliminatorie di un evento per un girone
 * @param string $event: evento
 * @param int $elim: 1 o 2 e indica il girone
 * @return bool: true se ok false altrimenti
 */
	function CreateElimRows($event,$elim, $ignore=false, $TourId=0)
	{
		$max=0;
		$ret=true;

		if(!$TourId) $TourId=$_SESSION['TourId'];

	// numero di righe da creare
		$q="
			SELECT EvElim{$elim} AS `q`
			FROM
				Events
			WHERE
				EvCode='{$event}' AND EvTournament={$TourId} AND EvTeamEvent=0
		";
		$r=safe_r_sql($q);
	//print $q.'<br><br>';exit;
		if ($r && safe_num_rows($r)==1)
		{
			$max=safe_fetch($r)->q;
			if ($max>0)
			{
				$tuple=array();
				$e=$elim-1;

				for ($i=1;$i<=$max;++$i)
				{
				// id,girone,evento,torneo,rank di qualifica,score,hits,gold,x,rank,timestamp
					$tuple[]="(0,{$e},'{$event}',{$TourId},{$i},0,0,0,0,0,'0000-00-00 00:00:00')";
				}

				$q="
					INSERT ".($ignore ? 'ignore' : '')." INTO Eliminations
					(ElId,ElElimPhase,ElEventCode,ElTournament,ElQualRank,ElScore,ElHits,ElGold,ElXnine,ElRank,ElDateTime) VALUES
				" . implode(',',$tuple) . " ";
				$r=safe_w_sql($q);
				//print $q.'<br>';
				$ret=($r!==false);
			}

			//print '<br>'.$q;
		}
		else
		{
			$ret=false;
		}

		return $ret;
	}

/**
 * DeleteElimRows()
 * Cancella le righe eliminatorie di un evento di un certo girone
 * @param string $event: evento
 * @param int $elim: 1 o 2 e indica il girone
 * @return bool: true se ok false altrimenti
 */
	function DeleteElimRows($event,$elim)
	{
		$e=$elim-1;

		$q="DELETE FROM Eliminations WHERE ElTournament={$_SESSION['TourId']} AND ElEventCode='{$event}' AND ElElimPhase={$e} ";
		$r=safe_w_sql($q);
		//print $q.'<br><br>';
		return ($r!==false);
	}

	function ResetElimRows($event, $elim, $CompId='') {
		$e=$elim-1;
		if(!$CompId) {
		    $CompId=$_SESSION['TourId'];
        }

		CreateElimRows($event, $elim, true, $CompId);
		$q=safe_w_sql("UPDATE Eliminations SET ElId=0,ElScore=0,ElHits=0,ElGold=0,ElXnine=0,ElArrowString='',ElTiebreak='', ElTbDecoded='', ElTbClosest=0, ElRank=0,ElSO=0 WHERE ElElimPhase={$e} AND	ElEventCode='{$event}' AND 	ElTournament={$CompId}");
		return ($q !== false);
	}

/**
 * ResetShootoff().
 * reset Shootoof flags and IndRankFinal/TeamRankFinal
 *
 *
 * @param string $event: EventCode
 * @param int $team: 0 individual - 1 team
 * @param int $type: shootoff type
 * 		 0: reset all of then
 * 		 1: reset from 2nd elim
 * 		 2: reset only matches
 * 		 3: reset only matches and DO NOT reset rankfinal coming from pools - Teo NOTE: I am not sure it's used and it works....
 * @return bool: success or not
 */
	function ResetShootoff($event,$team=0,$type=0,$ToId=0) {
		if(empty($ToId)) {
		    $ToId=$_SESSION['TourId'];
        }

		if (empty($event) OR !preg_match("/^[01]$/",$team) OR !preg_match("/^[0123]$/",$type)){
			return false;
		} else {
            $event=preg_replace('#^\'(.*)\'$#','$1',$event);
            $q="UPDATE Events SET ";
            switch ($type) {
                case 0:
                    $q.="EvShootOff='0',EvE1ShootOff='0',EvE2ShootOff='0' ";
                    break;
                case 1:
                    $q.="EvShootOff='0',EvE2ShootOff='0' ";
                    break;
                case 2:
                case 3:
                    $q.="EvShootOff='0' ";
                    break;
            }
            $q.="WHERE EvTournament={$ToId} AND EvTeamEvent={$team} AND EvCode='{$event}'";
            safe_w_sql($q);
            //Delete Final Rank. $type: 0-all rank of the event, 1- elim round from first one and exists only ind, 2-elim round from second one and exists only ind
            $date=date('Y-m-d H:i:s');
            $q="";
            if ($type==0 || $type==3) {
                if ($team==0) {
                    $q = "UPDATE Individuals  SET IndRankFinal=0, IndTimestampFinal='{$date}' " .
                        "WHERE IndTournament={$ToId} AND IndEvent='{$event}'";
                } else {
                    $q = "UPDATE Teams SET TeFinal=0, TeRankFinal=0, TeTimeStampFinal='{$date}' " .
                        "WHERE TeTournament={$ToId} AND TeEvent='{$event}' AND TeFinEvent=1 ";
                }
            } elseif ($type==1 AND $team==0) {
                $q = "UPDATE Individuals INNER JOIN Eliminations ON IndTournament=ElTournament AND IndId=ElId AND IndEvent=ElEventCode AND ElElimPhase=0 SET IndRankFinal=0, IndTimestampFinal='{$date}' ".
                    "WHERE IndTournament={$ToId} AND IndEvent='{$event}'";
            } elseif ($type==2 AND $team==0) {
                $q = "UPDATE Individuals INNER JOIN Eliminations ON IndTournament=ElTournament AND IndId=ElId AND IndEvent=ElEventCode AND ElElimPhase=1 SET IndRankFinal=0, IndTimestampFinal='{$date}' ".
                    "WHERE IndTournament={$ToId} AND IndEvent='{$event}'";
            }
            safe_w_sql($q);
            set_qual_session_flags();
            return true;
		}
	}

function getArrowEnds($Session=1, $Dist=0, $TourId=0) {
	$TourId=(empty($TourId) ? $_SESSION['TourId'] : $TourId);
	$ret=array();
    $q = safe_r_sql("select * from DistanceInformation where DiTournament={$TourId} and DiSession=$Session  and DiType='Q'"
        . ($Dist ? " and DiDistance=$Dist " : '')
    );
    while ($r = safe_fetch($q)) {
        $ret[$r->DiDistance] = array('ends' => ($r->DiEnds ? $r->DiEnds : 12), 'arrows' => ($r->DiArrows ? $r->DiArrows : 3));
    }

	return $ret;
}
