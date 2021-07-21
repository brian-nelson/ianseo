<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');
	require_once('Common/Lib/Fun_PrintOuts.php');
	require_once('Common/Fun_Modules.php');

/*
													- Fun_Qualification.local.inc.php -
	Contiene le funzioni e le variabili globali per la sezione Qualification.
*/

/*
	- CalcQualRank($Dist,$Event)
	Calcola la Rank delle qualifiche.
	$Dist è la distanza su cui fare il calcolo, se vale 0 la Rank sarà quella totale.
	$Event è il filtro della LIKE sull'evento.
	Ritorna true se errore, false altrimenti
*/

function CalcQualRank($Dist, $Event, $TourId=0) {
	if(!$TourId) $TourId=$_SESSION['TourId'];
	return !Obj_RankFactory::create('DivClass',array('tournament' => $TourId, 'events'=>$Event,'dist'=>$Dist))->calculate();
}

/**
 * calcola la rank abs di distanza o totale
 * @param int $Dist
 * @return int: 0->ok, 1->errore
 */
function CalcRank($Dist=0, $IncludeNullPoints=false)
{
	$Errore=0;

	$affected=array();
	if (MakeIndividuals($affected))
	{
		$Errore=1;
	}
	else
	{
		if(CalcQualRank($Dist,'%'))		//SOLO classifica di classe & divisione
		{
			$Errore=1;
		}
		else
		{
		// eventi a cui azzerare gli spareggi (oltre agli $affected)
			$events=array();

		// se la dist non è zero faccio la rank di distanza su tutti
			if ($Dist!=0)
			{
				if (!Obj_RankFactory::create('Abs',array('events'=>'%','dist'=>$Dist))->calculate())
				{
					$Errore=1;
				}
			}
			else
			{
			// salvo la tabella
				if (CreateTmpIndAndSnap()==1)
				{
					$Errore=1;
				}
				else
				{
				// faccio la rank ma non tocco i valori già esistenti
					$Opts=array('events'=>'%','dist'=>$Dist,'skipExisting'=>1);
					if($IncludeNullPoints) {
					    $Opts['includeNullPoints']=true;
					}
					if (!Obj_RankFactory::create('Abs', $Opts)->calculate())
					{
						$Errore=1;
					}
				// confronto con la vecchia tabella
					$events=FindIndEventsWithSOChanged();

					if ($events===false)
					{
						$Errore=1;
					}
					else
					{
						if (count($events)>0)
						{
							$affected=array_merge($affected,$events);
						}

					// azzero gli shootoff
						foreach ($affected as $e)
						{
							$x=ResetShootoff($e,0,0);

							if (!$x)
								$Errore=1;
						}

						if ($Errore==0)
						{
						// adesso gli eventi senza spareggi (anche quelli che non provengono da questa elaborazione)
							$events=array();
							$q="
								SELECT EvCode FROM Events
								WHERE
									EvTournament={$_SESSION['TourId']} AND EvTeamEvent=0 AND
									EvShootOff=0 AND EvE1ShootOff=0 AND EvE2ShootOff=0
							";
							$r=safe_r_sql($q);

							if (!$r)
							{
								$Errore=1;
							}
							else
							{
								while ($row=safe_fetch($r))
								{
									$events[]=$row->EvCode;
								}

							// ricalcolo la loro rank
								if (count($events)>0)
								{
									if (!Obj_RankFactory::create('Abs',array('events'=>$events,'dist'=>$Dist))->calculate())
									{
										$Errore=1;
									}
								}
							}

						}
					}
				}
			}
		}
	}

	DropTmpInd();
	return $Errore;
}


// nuovo by simo
/**
 * MakeIndividuals()
 * Aggiunge o toglie le righe ad Individuals.
 *
 * @param string[] $affected: contiene gli eventi assoluti che hanno subito una modifica (aggiunte o cancellazioni)
 * @param int $tournament: contiene il torneo su cui lavorare. se null prende dalla sessione
 * @return bool: true in caso di errore, false altrimenti
 */
function MakeIndividuals(&$affected, $tournament=null) {
	/** REVIEWED AFTER INTRODUCTION OD SUBCLASS SELECTION (2017-04-23)! **/
	if(is_null($tournament))
		$tournament = $_SESSION['TourId'];

	// get the QT nations
	//Se esiste il quota tournament cancello i "non elegible"
	$dbValues=array();
	$QTNations=array();
	$QTDates=array();
	if(module_exists('QuotaTournament')) {
		$dbValues = getModuleParameter("QuotaTournament", "allowedIOC", array());
		if(count($dbValues)>0) {
            foreach ($dbValues as $k => $v) {
                if (substr($k, 0, 3) == 'ev_') {
                    $evCode = explode("_", $k);
                    $evCode = $evCode[2];
                    $QTNations[$evCode] = $v;
                }
            }
        }
	}

	// Select the people that needs to be inserted in Individuals
	$q="SELECT DISTINCT EcCode, CoCode
		FROM Entries
		INNER JOIN EventClass ON EnTournament=EcTournament AND EcTeamEvent=0 AND EnDivision=EcDivision AND EnClass=EcClass and if(EcSubClass='', true, EcSubClass=EnSubClass)
		INNER JOIN Countries ON CoId=EnCountry
		LEFT JOIN Individuals ON IndId=EnId AND IndTournament=EnTournament AND IndEvent=EcCode
		WHERE EnTournament = {$tournament} AND IndEvent IS NULL AND EnIndFEvent=1 AND EnStatus<=1";
	$r=safe_r_sql($q);
	if (safe_num_rows($r)>0) {
		while ($myRow=safe_fetch($r)) {
			if (!in_array($myRow->EcCode, $affected,true) and !(!empty($QTNations[$myRow->EcCode]) and !in_array($myRow->CoCode, $QTNations[$myRow->EcCode])))
				$affected[]=$myRow->EcCode;
		}

		// Adds missing people
		$q="INSERT INTO Individuals (IndId, IndEvent, IndTournament, IndTimestamp)
			SELECT EnId, EcCode, EnTournament, '".date('Y-m-d H:i:s')."'
			FROM Entries
			INNER JOIN EventClass ON EnTournament=EcTournament AND EcTeamEvent=0 AND EnDivision=EcDivision AND EnClass=EcClass and if(EcSubClass='', true, EcSubClass=EnSubClass)
			LEFT JOIN Individuals ON IndId=EnId AND IndTournament=EnTournament AND IndEvent=EcCode
			WHERE EnTournament = {$tournament} AND IndEvent IS NULL AND EnIndFEvent=1 AND EnStatus<=1";
		$r=safe_w_sql($q);
	}
	// Deletes entries from individuals
	$q="SELECT DISTINCT IndEvent, IndId
		FROM Individuals
		LEFT JOIN
			(SELECT EnId, EcCode
			FROM Entries
			INNER JOIN EventClass ON EnTournament = EcTournament AND EcTeamEvent =0 AND EnDivision = EcDivision AND EnClass = EcClass and if(EcSubClass='', true, EcSubClass=EnSubClass)
			WHERE EnTournament = {$tournament} AND EnIndFEvent=1 AND EnStatus<=1
			) AS sq ON IndId = EnId AND IndEvent = EcCode
		WHERE IndTournament = {$tournament} AND EnId IS NULL";
	$r=safe_r_sql($q);
	while ($myRow=safe_fetch($r)) {
		if (!in_array($myRow->IndEvent,$affected,true)) {
			$affected[]=$myRow->IndEvent;
		}
		safe_w_sql("delete from Individuals where IndId=$myRow->IndId and IndEvent='{$myRow->IndEvent}' and IndTournament={$tournament}");
	}

	// In case of Quota Tournament removes the non eligibles
	if(module_exists('QuotaTournament')) {
        $maxByCountry = getModuleParameter("QuotaTournament", "maxByCountry", array());
        if(count($maxByCountry)!=0) {
            foreach ($maxByCountry as $k => $v) {
                $sql = "SELECT i.IndId, i.IndEvent, COUNT(sqy.IndRank) as qty " .
                    "FROM Individuals i " .
                    "INNER JOIN Entries e on i.IndId=e.EnId " .
                    "LEFT JOIN (SELECT IndRank, IndEvent, IndTournament, EnCountry " .
                    "FROM Individuals " .
                    "INNER JOIN Entries on IndId=EnId " .
                    "WHERE EnTournament={$tournament} " .
                    ") sqy on i.IndTournament=sqy.IndTournament and i.IndEvent=sqy.IndEvent and e.EnCountry=sqy.EnCountry and i.IndRank>sqy.IndRank " .
                    "WHERE e.EnTournament={$tournament} and i.IndEvent = '" . $k . "' " .
                    "GROUP BY i.IndId, i.IndEvent, i.IndRank, e.EnCountry " .
                    "HAVING qty>=" . $v['Num'] . " " .
                    "ORDER BY i.IndEvent, i.IndRank";
                $q = safe_r_sql($sql);
                while($r = safe_fetch($q)) {
                    $sql = "DELETE FROM Individuals WHERE IndId={$r->IndId} AND IndEvent='" . $v['Ev'] . "' AND IndTournament={$tournament}";
                    safe_w_sql($sql);
                }
            }
        }

		$dbValues=getModuleParameter("QuotaTournament", "allowedIOC", array());
        if(count($dbValues)!=0) {
            foreach ($dbValues as $k => $v) {
                if (substr($k, 0, 3) == 'ev_') {
                    $evCode = explode("_", $k);
                    $evCode = $evCode[2];
                    $q = "DELETE FROM Individuals
                        USING Individuals
                        INNER JOIN Entries ON IndId = EnId AND IndTournament=EnTournament
                        INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
                        INNER JOIN EventClass ON EnTournament = EcTournament AND EcTeamEvent =0 AND EnDivision = EcDivision AND EnClass = EcClass and if(EcSubClass='', true, EcSubClass=EnSubClass) AND IndEvent = EcCode
                        WHERE EnTournament = {$tournament} AND EnIndFEvent=1 AND EnStatus<=1 AND EcCode='{$evCode}'
                        AND (CoCode NOT IN ('" . implode("', '", $v) . "') OR EnIndClEvent!=1)";
                    $r = safe_w_sql($q);
                    if (safe_w_affected_rows()) {
                        if (!in_array($k, $affected, true))
                            $affected[] = $k;
                    }
                } else if (substr($k, 0, 4) == 'dob_') {
                    $evCode = explode("_", $k);
                    $dobType = $evCode[1];
                    $evCode = $evCode[2];
                    $q = "DELETE FROM Individuals
                        USING Individuals
                        INNER JOIN Entries ON IndId = EnId AND IndTournament=EnTournament
                        INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
                        INNER JOIN EventClass ON EnTournament = EcTournament AND EcTeamEvent =0 AND EnDivision = EcDivision AND EnClass = EcClass and if(EcSubClass='', true, EcSubClass=EnSubClass) AND IndEvent = EcCode
                        WHERE EnTournament = {$tournament} AND EnIndFEvent=1 AND EnStatus<=1 AND EcCode='{$evCode}'
                        AND (EnDob " . ($dobType == 's' ? "<=" : ">=") . " '{$v}' OR EnIndClEvent!=1)";
                    $r = safe_w_sql($q);
                    if (safe_w_affected_rows()) {
                        if (!in_array($k, $affected, true))
                            $affected[] = $k;
                    }

                }
            }
        }
	}
	return false;
}

/**
 * MakeIndAbs()
 *
 * Crea una classifica assoluta (eventualemente mettendo a posto Individuals tramite MakeIndividuals())
 *
 * @param int[] $Dists: vettore delle distanze su cui operare. Se null la funzione lavora
 * 		su tutte le distanze della gara più sulla zero che significa abs totale
 * @return int: 0 nessun errore 1 altrimenti
 */
function MakeIndAbs($Dists=null)
{
	$Errore=0;

	if (is_null($Dists)) {
	// scopro quante distanze ha la gara
		$q="SELECT ToNumDist FROM Tournament WHERE ToId={$_SESSION['TourId']} ";
		$r=safe_r_sql($q);

		if (safe_num_rows($r)==1){
			$row=safe_fetch($r);

			for ($i=0;$i<=$row->ToNumDist;++$i) {
                $Dists[] = $i;
            }
		} else {
            return 1;
        }
	}

	//print_r($Dists);exit;

	$affected=array();

	$x=MakeIndividuals($affected);

	if (!$x) {
		if(count($affected)>0) {
			foreach ($Dists as $d) {
				$y=Obj_RankFactory::create('Abs',array('events'=>$affected,'dist'=>$d))->calculate();

				if (!$y) {
					$Errore=1;
					break;
				}
			}
		}
	} else {
		$Errore=1;
	}

	return $Errore;
}

/*
	- MakeTeams($Societa, $Category)
	Calcola le squadre delle qualifiche.
	$Societa limita sulla società su cui fare il calcolo, se vale NULL è calcolata su tutti.
	$Category è il filtro sulla classe/divisione, valida solo per il tipo "0", se NULL è calcolata su tutte.
	Ritorna true se tutto ok, false altrimenti
*/
function MakeTeams($Societa, $Category, $ToId=0) {
	global $CFG;

	if(!$ToId) $ToId=$_SESSION['TourId'];
	$Errore=0;
	// check if an overriding function exists
	static $ToType, $ToLocRule, $ToSubRule, $ToYear;
	if(!$ToType) {
		$q=safe_r_sql("select ToType, ToLocRule, ToTypeSubRule, year(ToWhenTo) as ToYear  from Tournament where ToId={$ToId}");
		$r=safe_fetch($q);
		$ToType=$r->ToType;
		$ToLocRule=$r->ToLocRule;
		$ToSubRule=$r->ToTypeSubRule;
		$ToYear=$r->ToYear;
	}

	$TipoElaborazione=0;

	$Common=$CFG->DOCUMENT_PATH . "Modules/Sets/$ToLocRule/Functions/MakeTeams%s.php";
	if(file_exists($file=sprintf($Common, "-$ToType-$ToSubRule-$ToYear"))
		or file_exists($file=sprintf($Common, "-$ToType-$ToSubRule"))
		or file_exists($file=sprintf($Common, "-$ToType"))
		or file_exists($file=sprintf($Common, "-$ToSubRule-$ToYear"))
		or file_exists($file=sprintf($Common, "-$ToSubRule"))
		or file_exists($file=sprintf($Common, "-$ToYear"))
		or file_exists($file=sprintf($Common, ""))
		) {
		// the function is overridden...
		require_once($file);
	} else {

	//Estraggo il tipo di elaborazione che devo Fare

		$MyQuery="SELECT ToElabTeam AS TtElabTeam " .
			"FROM Tournament  " .
			"WHERE ToId=" . StrSafe_DB($ToId);
		$Rs=safe_r_sql($MyQuery);
		if(safe_num_rows($Rs)==1)
		{
			$r=safe_fetch($Rs);
			$TipoElaborazione = $r->TtElabTeam;
		}

	// Elimino le squadre della qualifica
		$Delete
			= "DELETE Teams, TeamComponent FROM "
			. "Teams, TeamComponent  "
			. "WHERE TeCoId=TcCoId AND TeEvent=TcEvent AND TeTournament=TcTournament AND TeTournament=" . StrSafe_DB($ToId)
			. (!is_null($Societa) ? ' AND TeCoId=' . StrSafe_DB($Societa) . ' AND TcCoId=' . StrSafe_DB($Societa)  : '')
			. ((!is_null($Category) AND $TipoElaborazione==0) ? ' AND TeEvent=' . StrSafe_DB($Category) . ' AND TcEvent=' . StrSafe_DB($Category) : '')
			. " AND TeFinEvent='0' AND TcFinEvent='0' ";
		$Rs=safe_w_sql($Delete);

		if($TipoElaborazione==0)	//Gare Standard
		{
			// Get the people eligible, exept who got an IRM in qualification
			$Select = "SELECT EnTournament, EnId, IF(EnCountry2=0,EnCountry,EnCountry2) as EnCountry, CONCAT(EnDivision,EnClass) AS Event, QuScore, QuGold, QuXnine, QuHits
				FROM Entries 
			    INNER JOIN Qualifications ON EnId=QuId
				inner join IrmTypes on IrmId=QuIrmType and IrmShowRank=1
				WHERE EnAthlete=1 AND EnTeamClEvent=1 AND EnStatus <= 1 AND EnTournament = " . StrSafe_DB($ToId) . " AND QuScore>0 "
				. (!is_null($Societa) ? ' AND IF(EnCountry2=0,EnCountry,EnCountry2)=' . StrSafe_DB($Societa)   : '')
				. (!is_null($Category) ? ' AND CONCAT(EnDivision,EnClass)=' . StrSafe_DB($Category)  : '')
				."ORDER BY IF(EnCountry2=0, EnCountry, EnCountry2), CONCAT(EnDivision,EnClass), QuScore DESC, QuGold DESC, QuXnine DESC, EnId ASC ";
			$Rs=safe_r_sql($Select);
			if (!$Rs) {
				$Errore=1;
			}

			// Variabili di Servizio
			$Peoples = 0;	// Contatore delle persone
			$MyCountry = 0;
			$MyEvent = '';
			$Countries=array(0,0,0);
			$Event=array(0,0,0);
			$Aths = array(0,0,0);
			$Scores = array(0,0,0);
			$Golds = array(0,0,0);
			$XNines = array(0,0,0);
			$Hits = array(0,0,0);

			//Ciclo per Scorrere l'elenco partecipanti
			if (safe_num_rows($Rs)) {
				while ($MyRow=safe_fetch($Rs)) {
					//Cambio di società
					if ($MyCountry != $MyRow->EnCountry || $MyEvent != $MyRow->Event) {
						$Peoples=0;
						$Countries=array(0,0,0);
						$Event=array(0,0,0);
						$Aths = array(0,0,0);
						$Scores = array(0,0,0);
						$Golds = array(0,0,0);
						$XNines = array(0,0,0);
						$Hits = array(0,0,0);
						$MyCountry = $MyRow->EnCountry;
						$MyEvent = $MyRow->Event;
					} else {
					    ++$Peoples;
					}

					//Ho la Squadra----> Salvo
					if ($Peoples<=2)
					{
						$Aths[$Peoples]=$MyRow->EnId;
						$Countries[$Peoples]=$MyRow->EnCountry;
						$Events[$Peoples]=$MyRow->Event;
						$Scores[$Peoples]=$MyRow->QuScore;
						$Golds[$Peoples]=$MyRow->QuGold;
						$XNines[$Peoples]=$MyRow->QuXnine;
						$Hits[$Peoples]=$MyRow->QuHits;

						// se ho proprio 3 persone faccio la squadra
						if ($Peoples==2)
						{
							// Insert in Teams
							$InsertT
								= "INSERT INTO Teams (TeCoId,TeEvent,TeTournament,TeFinEvent,TeScore,TeGold,TeXNine,TeFinal,TeHits) "
								. "VALUES("
								. StrSafe_DB($Countries[0]) . ","
								. StrSafe_DB($Events[0]) . ","
								. StrSafe_DB($ToId) . ","
								. "'0',"
								. StrSafe_DB($Scores[0]+$Scores[1]+$Scores[2]) . ","
								. StrSafe_DB($Golds[0]+$Golds[1]+$Golds[2]) . ","
								. StrSafe_DB($XNines[0]+$XNines[1]+$XNines[2]) . ","
								. "'0',"
								. StrSafe_DB($Hits[0]+$Hits[1]+$Hits[2]) . ""
								. ") ";
							$RsT=safe_w_sql($InsertT);
							// Insert in TeamComponent
							$RsTC=array(NULL,NULL,NULL);
							for ($i=0;$i<=2;++$i)
							{
								$InsertTC
									= "INSERT INTO TeamComponent (TcCoId,TcTournament,TcEvent,TcFinEvent,TcId,TcOrder) "
									. "VALUES("
									. StrSafe_DB($Countries[$i]) . ","
									. StrSafe_DB($ToId) . ","
									. StrSafe_DB($Events[$i]) . ","
									. "'0',"
									. StrSafe_DB($Aths[$i]) . ","
									. StrSafe_DB(($i+1)) . ""
									. ") ";
								$RsTC[$i]=safe_w_sql($InsertTC);
							}
							if (!$RsT || !$RsTC[0] || !$RsTC[1] || !$RsTC[2])
								$Errore=1;
						}
					}
				}
			}
		}
		elseif($TipoElaborazione==1)	// gare di Campagna
		{
			$Select = "SELECT EnTournament,EnId,IF(EnCountry2=0,EnCountry,EnCountry2) AS EnCountry, EnDivision, RIGHT(EnClass,1) AS Sex,
					QuScore,QuGold,QuXnine,QuHits
				FROM Entries 
			    INNER JOIN Qualifications ON EnId=QuId and QuScore>0 
			    inner join IrmTypes on IrmId=QuIrmType and IrmShowRank=1
				inner join Individuals on IndId=EnId and IndTournament=EnTournament
				WHERE EnAthlete=1 AND EnTeamClEvent=1 AND EnStatus <= 1 AND EnTournament = " . StrSafe_DB($ToId) . " "
				. (!is_null($Societa) ? ' AND IF(EnCountry2=0,EnCountry,EnCountry2)=' . StrSafe_DB($Societa)   : '')
				. " AND EnClass NOT IN ('AF','AM','GF','GM','RF','RM') AND EnDivision IN ('OL','CO','AN')"
				. "ORDER BY IF(EnCountry2=0,EnCountry,EnCountry2), RIGHT(EnClass,1), EnDivision, QuScore DESC, QuGold DESC, QuXnine DESC,EnId ASC ";

			$Rs=safe_r_sql($Select);
			if (!$Rs)
				$Errore=1;

			// Variabili di Servizio
			$Peoples = -1;	// Contatore delle persone
			$MyCountry = 0;
			$MyEvent = '';
			$Countries=array(0,0,0);
			$Event=array(0,0,0);
			$Aths = array(0,0,0);
			$Scores = array(0,0,0);
			$Golds = array(0,0,0);
			$XNines = array(0,0,0);
			$Hits = array(0,0,0);
			$Divisions = array("OL" => false, "AN" => false, "CO" => false);

			//Ciclo per Scorrere l'elenco partecipanti
			if (safe_num_rows($Rs))
			{
				while ($MyRow=safe_fetch($Rs))
				{
					//Cambio di società
					if ($MyCountry != $MyRow->EnCountry || $MyEvent != "~" . $MyRow->Sex)
					{
						$Peoples=-1;			//N.B. inizializzo a -1 perchè qui si incrementa Sempre ad ogni persona, altrimenti sballa gli indici degli array di appoggio
						$Countries=array(0,0,0);
						$Event=array(0,0,0);
						$Aths = array(0,0,0);
						$Scores = array(0,0,0);
						$Golds = array(0,0,0);
						$XNines = array(0,0,0);
						$Hits = array(0,0,0);
						$Divisions = array("OL" => false, "AN" => false, "CO" => false);
						$MyCountry = $MyRow->EnCountry;
						$MyEvent = "~" . $MyRow->Sex;
					}
					if($Divisions[$MyRow->EnDivision] == false) {
						$Divisions[$MyRow->EnDivision] = true;
						++$Peoples;
						$Aths[$Peoples]=$MyRow->EnId;
						$Countries[$Peoples]=$MyRow->EnCountry;
						$Events[$Peoples]=$MyRow->EnDivision;
						$Scores[$Peoples]=$MyRow->QuScore;
						$Golds[$Peoples]=$MyRow->QuGold;
						$XNines[$Peoples]=$MyRow->QuXnine;
						$Hits[$Peoples]=$MyRow->QuHits;
						// se ho proprio 3 persone faccio la squadra
						if ($Peoples==2)
						{
							// Insert in Teams
							$InsertT
								= "INSERT INTO Teams (TeCoId,TeEvent,TeTournament,TeFinEvent,TeScore,TeGold,TeXNine,TeFinal,TeHits) "
								. "VALUES("
								. StrSafe_DB($Countries[0]) . ","
								. StrSafe_DB($MyEvent) . ","
								. StrSafe_DB($ToId) . ","
								. "'0',"
								. StrSafe_DB($Scores[0]+$Scores[1]+$Scores[2]) . ","
								. StrSafe_DB($Golds[0]+$Golds[1]+$Golds[2]) . ","
								. StrSafe_DB($XNines[0]+$XNines[1]+$XNines[2]) . ","
								. "'0',"
								. StrSafe_DB($Hits[0]+$Hits[1]+$Hits[2]) . ""
								. ") ";
							$RsT=safe_w_sql($InsertT);
							$RsTC=array(NULL,NULL,NULL);

							// Insert in TeamComponent
							for ($i=0;$i<=2;++$i)
							{
								$InsertTC
									= "INSERT INTO TeamComponent (TcCoId,TcTournament,TcEvent,TcFinEvent,TcId,TcOrder) "
									. "VALUES("
									. StrSafe_DB($Countries[$i]) . ","
									. StrSafe_DB($ToId) . ","
									. StrSafe_DB($MyEvent) . ","
									. "'0',"
									. StrSafe_DB($Aths[$i]) . ","
									. StrSafe_DB(($i+1)) . ""
									. ") ";
								$RsTC[$i]=safe_w_sql($InsertTC);
							}

							if (!$RsT || !$RsTC[0] || !$RsTC[1] || !$RsTC[2])
								$Errore=1;
						}
					}
				}
			}
		}
		elseif($TipoElaborazione==2)	// gare 3D
		{
			// Estraggo l'elenco delle persone
			$Select = "SELECT EnTournament,EnId,IF(EnCountry2=0,EnCountry,EnCountry2) AS EnCountry, if(EnDivision='AI' OR EnDivision='OL' OR EnDivision='AN','AN',EnDivision) as Division, EnClass AS Sex, QuScore,QuGold,QuXnine,QuHits
				FROM Entries 
			    INNER JOIN Qualifications ON EnId=QuId and QuScore>0 
				inner join IrmTypes on IrmId=QuIrmType and IrmShowRank=1
				WHERE EnAthlete=1 AND EnTeamClEvent=1 AND EnStatus <= 1 AND QuScore>0 AND EnTournament = " . StrSafe_DB($ToId) . " "
				. (!is_null($Societa) ? ' AND IF(EnCountry2=0,EnCountry,EnCountry2)=' . StrSafe_DB($Societa)   : '')
				. "AND EnDivision IN ('AI','OL','CO','AN','LB')"
				. "ORDER BY IF(EnCountry2=0,EnCountry,EnCountry2), EnClass, QuScore DESC, QuGold DESC, QuXnine DESC, if(EnDivision='AI' OR EnDivision='OL' OR EnDivision='AN','AN',EnDivision), EnId ASC ";

			$Rs=safe_r_sql($Select);
			if (!$Rs)
				$Errore=1;

			// Variabili di Servizio
			$Peoples = -1;	// Contatore delle persone
			$MyCountry = 0;
			$MyEvent = '';
			$Countries=array(0,0,0);
			$Event=array(0,0,0);
			$Aths = array(0,0,0);
			$Scores = array(0,0,0);
			$Golds = array(0,0,0);
			$XNines = array(0,0,0);
			$Hits = array(0,0,0);
			$Divisions = array("LB" => false, "AN" => false, "CO" => false);

			//Ciclo per Scorrere l'elenco partecipanti
			if (safe_num_rows($Rs)>0) {
				while ($MyRow=safe_fetch($Rs)) {
					//Cambio di società
					if ($MyCountry != $MyRow->EnCountry || $MyEvent != 'XX' . $MyRow->Sex) {
						$Peoples=-1;			//N.B. inizializzo a -1 perchè qui si incrementa Sempre ad ogni persona, altrimenti sballa gli indici degli array di appoggio
						$Countries=array(0,0,0);
						$Event=array(0,0,0);
						$Aths = array(0,0,0);
						$Scores = array(0,0,0);
						$Golds = array(0,0,0);
						$XNines = array(0,0,0);
						$Hits = array(0,0,0);
						$Divisions = array("LB" => false, "AN" => false, "CO" => false);
						$MyCountry = $MyRow->EnCountry;
						$MyEvent = 'XX' . $MyRow->Sex;
					}
					if($Divisions[$MyRow->Division] == false) {
						$Divisions[$MyRow->Division] = true;
						++$Peoples;
						$Aths[$Peoples]=$MyRow->EnId;
						$Countries[$Peoples]=$MyRow->EnCountry;
						$Events[$Peoples]=$MyRow->Division;
						$Scores[$Peoples]=$MyRow->QuScore;
						$Golds[$Peoples]=$MyRow->QuGold;
						$XNines[$Peoples]=$MyRow->QuXnine;
						$Hits[$Peoples]=$MyRow->QuHits;
						// se ho proprio 3 persone faccio la squadra
						if ($Peoples==2) {
							// Insert in Teams
							$InsertT
								= "INSERT INTO Teams (TeCoId,TeEvent,TeTournament,TeFinEvent,TeScore,TeGold,TeXNine,TeFinal,TeHits) "
								. "VALUES("
								. StrSafe_DB($Countries[0]) . ","
								. StrSafe_DB($MyEvent) . ","
								. StrSafe_DB($ToId) . ","
								. "'0',"
								. StrSafe_DB($Scores[0]+$Scores[1]+$Scores[2]) . ","
								. StrSafe_DB($Golds[0]+$Golds[1]+$Golds[2]) . ","
								. StrSafe_DB($XNines[0]+$XNines[1]+$XNines[2]) . ","
								. "'0',"
								. StrSafe_DB($Hits[0]+$Hits[1]+$Hits[2]) . ""
								. ") ";
							$RsT=safe_w_sql($InsertT);
							$RsTC=array(NULL,NULL,NULL);

							// Insert in TeamComponent
							for ($i=0;$i<=2;++$i) {
								$InsertTC
									= "INSERT INTO TeamComponent (TcCoId,TcTournament,TcEvent,TcFinEvent,TcId,TcOrder) "
									. "VALUES("
									. StrSafe_DB($Countries[$i]) . ","
									. StrSafe_DB($ToId) . ","
									. StrSafe_DB($MyEvent) . ","
									. "'0',"
									. StrSafe_DB($Aths[$i]) . ","
									. StrSafe_DB(($i+1)) . ""
									. ") ";
								$RsTC[$i]=safe_w_sql($InsertTC);
							}
							if (!$RsT || !$RsTC[0] || !$RsTC[1] || !$RsTC[2]) {
                                $Errore = 1;
                            }
						}
					}
				}
			}
		}
	}
	Obj_RankFactory::create('DivClassTeam',array(
		'tournament' => $ToId,
		'events'=>(($Category!==null AND $TipoElaborazione==0) ? array($Category)  : array())
	))->calculate();
	return $Errore;
}

/**
 * Crea lo snapshoot della Individuals su tmpIndividuals.
 * @return int: 0 no error 1 errore
 */
function CreateTmpIndAndSnap() {
	// tmp
	$query="
		CREATE TEMPORARY TABLE IF NOT EXISTS `tmpIndividuals` (
			`tIndId` int(10) unsigned NOT NULL,
			`tIndEvent` varchar(4) NOT NULL,
			`tIndTournament` int(11) NOT NULL,
			`tIndSO` smallint(6) NOT NULL default '0',
			PRIMARY KEY (`tIndId`,`tIndEvent`,`tIndTournament`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	";

	$Rs=safe_w_sql($query);

	if (!$Rs)
		return 1;

	// per sicurezza faccio una truncate
	$query="TRUNCATE TABLE tmpIndividuals";
	$Rs=safe_w_sql($query);

	if (!$Rs)
		return 1;

	// snapshoot
	$query="
		INSERT INTO `tmpIndividuals`
		(
			`tIndId`,
			`tIndEvent`,
			`tIndTournament`,
			`tIndSO`
		)
		SELECT
			IndId,
			IndEvent,
			IndTournament,
			IndSO
		FROM
			Individuals
		WHERE
			IndTournament={$_SESSION['TourId']}
	";

	//print $query;exit;
	$Rs=safe_w_sql($query);

	if (!$Rs)
		return 1;

	return 0;
}

/**
 * cerca gli eventi che hanno subito un cambiamento tra
 * la creazione di Individuals e il calcolo della abs rank sulla dist 0
 *
 * @return mixed[]: false in caso di errore, l'array con gli eventi che hanno subito un cambio
 * 		ai quali azzerare gli shootoff
 */
function FindIndEventsWithSOChanged()
{
	$events=array();

	$q="
		SELECT DISTINCT
			IndEvent AS ev
		FROM
			Individuals
			INNER JOIN
				tmpIndividuals
			ON IndId=tIndId AND IndEvent=tIndEvent AND IndTournament=tIndTournament AND IndSO<>tIndSO
		WHERE
			IndTournament={$_SESSION['TourId']}
	";

	$Rs=safe_w_sql($q);

	if (!$Rs)
		return false;

	while ($myRow=safe_fetch($Rs))
	{
		if (!in_array($myRow->ev,$events))
			$events[]=StrSafe_DB($myRow->ev);
	}

	return $events;
}

/**
 * Cancella la tabella temporanea tmpIndividuals
 * @return void
 */
function DropTmpInd()
{
	$query= "DROP TEMPORARY TABLE IF EXISTS tmpIndividuals ";
	$Rs=safe_w_sql($query);
}

/**
 * Crea lo snapshoot della Teams su tmpTeams.
 * Verrà usata quando la MakeTeamsAbs agisce su tutte le società
 * @return int: 0 no error 1 errore
 */
function CreateTmpTeamsAndSnap($ToId=0) {
	// tmp
	if(!$ToId) $ToId=$_SESSION['TourId'];
	$query="
		CREATE TEMPORARY TABLE IF NOT EXISTS `tmpTeams` (
		  `tTeCoId` int(11) NOT NULL,
		  `tTeSubTeam` tinyint(4) NOT NULL default '0',
		  `tTeEvent` varchar(4) NOT NULL,
		  `tTeTournament` int(11) NOT NULL,
		  `tTeFinEvent` tinyint(3) unsigned NOT NULL default '0',
		  `tTeScore` smallint(6) NOT NULL,
		  `tTeHits` smallint(6) NOT NULL,
		  `tTeGold` smallint(6) NOT NULL,
		  `tTeXnine` smallint(6) NOT NULL,
		  `tTeTie` tinyint(1) NOT NULL,
		  `tTeTieBreak` varchar(15) NOT NULL,
		  `tTeTbClosest` tinyint(4) NOT NULL,
		  `tTeTbDecoded` varchar(15) NOT NULL,
		  `tTeRank` tinyint(4) NOT NULL,
		  `tTeRankFinal` smallint(6) NOT NULL,
		  `tTeSO` smallint(6) NOT NULL,
		  `tTeTimeStamp` timestamp NULL default NULL,
		  `tTeTimeStampFinal` datetime NULL default NULL,
		  `tTeFinal` tinyint(3) unsigned NOT NULL default '0',
		  PRIMARY KEY  (`tTeCoId`,`tTeSubTeam`,`tTeEvent`,`tTeTournament`,`tTeFinEvent`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	";
	$Rs=safe_w_sql($query);

// per sicurezza faccio una truncate
	$query="TRUNCATE TABLE tmpTeams";
	$Rs=safe_w_sql($query);

// snapshoot
	$query="
		INSERT INTO tmpTeams
		(
			tTeCoId,
			tTeSubTeam,
			tTeEvent,
			tTeTournament,
			tTeFinEvent,
			tTeScore,
			tTeHits,
			tTeGold,
			tTeXnine,
			tTeTie,
			tTeTieBreak,
			tTeTbClosest,
		 	tTeTbDecoded,
			tTeRank,
			tTeRankFinal,
			tTeTimeStamp,
			tTeTimeStampFinal,
			tTeSO,
			tTeFinal
		)
		SELECT
			 TeCoId,
			 TeSubTeam,
			 TeEvent,
			 TeTournament,
			 TeFinEvent,
			 TeScore,
			 TeHits,
			 TeGold,
			 TeXnine,
			 TeTie,
			 TeTieBreak,
			 TeTbClosest,
			 TeTbDecoded,
			 TeRank,
			 TeRankFinal,
			 TeTimeStamp,
			 TeTimeStampFinal,
			 TeSO,
			 TeFinal
		FROM
			Teams
		WHERE
			TeTournament=" . StrSafe_DB($ToId) . " AND TeFinEvent=1 AND TeRank<>0
	";
	//print $query;exit;
	$Rs=safe_w_sql($query);

	if (!$Rs)
		return 1;

	return 0;
}

/**
 * Fa un'outer tra Teams e tmpTeam.
 * Quanto una delle due tabelle è NULL vuol dire che nel frattempo quell'evento è stato modificato quindi
 * occorre impostare lo shootoff a zero per essere rifatto.
 * La funzione non imposta il flag ma ritorna gli eventi a cui impostarlo
 * @return mixed: string[] elenco di eventi; false in caso di errore
 */
function FindTeamEventsWithNull($ToId=0) {
/*
 * A causa di un bug di mysql non si può usare più di una volta una tabella temporanea nella stessa query.
 * Allora la query di prima viene fatta in due tempi, prima sulla left e poi sulla right
 */
	if(!$ToId) $ToId=$_SESSION['TourId'];
	$events=array();

	$query="
		SELECT
			IF(TeEvent IS NULL AND tTeEvent IS NOT NULL,tTeEvent,IF(TeEvent  IS NOT NULL AND  tTeEvent IS NULL,TeEvent,'*') ) AS ev

		FROM
			Teams
			{@join_type@} JOIN
				tmpTeams
			ON TeCoId=tTeCoId AND TeSubTeam=tTeSubTeam AND TeEvent=tTeEvent AND TeTournament=tTeTournament AND
				TeFinEvent=tTeFinEvent AND TeScore=tTeScore AND TeHits=tTeHits AND TeGold=tTeGold AND TeXnine=tTeXnine
				AND TeTie=tTeTie AND TeSO=tTeSO
		WHERE
			 TeTournament=" . StrSafe_DB($ToId) . " AND TeFinEvent=1 AND
			 IF(TeEvent IS NULL AND tTeEvent IS NOT NULL,tTeEvent,IF(TeEvent  IS NOT NULL AND  tTeEvent IS NULL,TeEvent,'*') )<>'*'
	";

	$jointypes=array('LEFT','RIGHT');

	foreach ($jointypes as $jt)
	{
		$q=str_replace('{@join_type@}',$jt,$query);
		//print $q.'<br>';
		$Rs=safe_w_sql($q);

		if (!$Rs)
			return false;

		while ($myRow=safe_fetch($Rs))
		{
			if (!in_array($myRow->ev,$events))
				$events[]=StrSafe_DB($myRow->ev);

		}
	}
//print '<pre>';
//print_r($events);
//print '</pre>';
	return $events;
}

/**
 * Per gli eventi in $events imposta lo shootoff a zero
 * @param $string[] $events: eventi
 * @return int: 0 ok; 1 errore
 */
function SetupShootoff($events,$ToId=0)
{
	if(!$ToId) $ToId=$_SESSION['TourId'];
	if (count($events)>0)
	{
		/*$query
			= "UPDATE "
				. "Events "
			. "SET "
				. "EvShootOff=0 "
			. "WHERE "
				. "EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 "
				. "AND EvCode IN(" . implode(',',$events). ") ";
		$Rs=safe_w_sql($query);
		if (!$Rs)
			return 1;*/

		foreach ($events as $e)
		{
			$x=ResetShootoff($e,1,0,$ToId);

			if (!$x)
				return 1;
		}
	}

	return 0;
}

/**
 * Reimposta la rank prelevato con lo snapshoot per gli eventi non modificati.
 * Questa mi serve perchè la procedura MakeTeamsAbs cancella comunque le squadre anche se non sono modificate.
 * In questo modo posso riprendere la rank e non trovarmi nel caso in cui ho gli shootoff fatti ma le rank azzerate.
 * @param string[] $events: eventi da escludere; $events contiene gli eventi modificati quindi se ci sono la where
 * avrà come condizione una NOT IN
 * @return mixed: false se ci son problemi, un array con gli eventi rimessi a posto altrimenti
 */
function SetupTeamsRank($events, $ToId) {
	$ret=array();
	if(!$ToId) $ToId=$_SESSION['TourId'];

/*
 * Prelevo gli eventi che andranno ad essere aggiornate dalla query successiva a questa.
 * Mi servono perchè quando alla fine della MakeTeamsAbs andrò a calcolare la rank abs
 * con la libreria, non dovrò gestire con la classe questi eventi
 * dato che saranno già a posto per via dell'update qui sotto.
 * Questi eventi li faccio ritornare dalla funzione.
 */
	$query
		= "SELECT TeEvent FROM "
		. "Teams "
		. "INNER JOIN "
				. "tmpTeams "
			. "ON TeCoId=tTeCoId AND TeSubTeam=tTeSubTeam AND TeEvent=tTeEvent AND TeTournament=tTeTournament AND "
			. "TeFinEvent=tTeFinEvent "

		. "WHERE "
			. "TeTournament=" . StrSafe_DB($ToId) . " AND TeFinEvent=1 ";
	if (count($events)>0)
	{
		$query
			.="AND TeEvent NOT IN(" . implode(',',$events). ") ";
	}
	$Rs=safe_w_sql($query);
	if (safe_num_rows($Rs)>0)
	{
		while ($row=safe_fetch($Rs))
			$ret[]=$row->TeEvent;
	}

//	print '<pre>';
//	while ($r=safe_fetch($Rs))
//	{
//
//		print_r($r);
//
//	}
//	print '</pre>';
//			exit;
	$query
		= "UPDATE "
			. "Teams "
			. "INNER JOIN "
				. "tmpTeams "
			. "ON TeCoId=tTeCoId AND TeSubTeam=tTeSubTeam AND TeEvent=tTeEvent AND TeTournament=tTeTournament AND "
			. "TeFinEvent=tTeFinEvent "
		. "SET "
			. "TeRank=tTeRank, "
			. "TeRankFinal=tTeRankFinal, "
			. "TeSO=tTeSO "
		. "WHERE "
			. "TeTournament=" . StrSafe_DB($ToId) . " AND TeFinEvent=1 ";

	if (count($events)>0)
	{
		$query
			.="AND TeEvent NOT IN(" . implode(',',$events). ") ";
	}

	$Rs=safe_w_sql($query);
	//print $query;exit;
	if (!$Rs)
	{
		return false;
	}
	else
	{
		return $ret;
	}
}

/**
 * Cancella la tabella temporanea
 * @return void
 */
function DropTmpTeams()
{
	$query= "DROP TEMPORARY TABLE tmpTeams ";
	$Rs=safe_w_sql($query);
}

/*
	- MakeTeamsAbs($Societa)
	Calcola le squadre delle qualifiche.
	$Societa limita sulla società su cui fare il calcolo, se vale NULL è calcolata su tutti.
	$Div è la divisione su cui lavorare
	$Cl è la classe su cui lavorare
	$Div e $Cl non devono essere null. se $Società è null allora il loro valore verrà ignorato.
	Ritorna true se tutto ok, false altrimenti
*/
function MakeTeamsAbs($Societa=null, $Div=null, $Cl=null, $ToId=0)/*,$MoreTeam=true*/ {
	$Errore=0;

	if(!$ToId) $ToId=$_SESSION['TourId'];

	if (is_null($Societa)) {
		$Errore=CreateTmpTeamsAndSnap($ToId);

		if ($Errore==1)
			return 1;
	}

	$events4abs=array();

/* simo */
	$Delete
		= "DELETE FROM "
			. "te, tc "
		. "USING "
			. "Teams AS te INNER JOIN TeamComponent AS tc "
			. "ON te.TeCoId=tc.TcCoId AND te.TeEvent=tc.TcEvent AND te.TeTournament=tc.TcTournament AND te.TeFinEvent=tc.TcFinEvent  "
			. "INNER JOIN ("
				. "SELECT EcCode AS sqEcCode, EcTournament AS sqEcTournament "
				. "FROM EventClass "
				. "WHERE "
					. "EcTournament=" . StrSafe_DB($ToId) . " AND EcTeamEvent<>0 "
					. (!is_null($Societa) ? " AND EcClass=" . StrSafe_DB($Cl) . " AND EcDivision=" . StrSafe_DB($Div) . " " : "")
			. ") AS sq ON te.TeEvent=sqEcCode AND te.TeTournament=sqEcTournament "
			. "WHERE "
				. "te.TeFinEvent=1 AND te.TeTournament=" . StrSafe_DB($ToId) . " "
				. (!is_null($Societa) ? "AND te.TeCoId=". StrSafe_DB($Societa)  . " " : "");
/* end simo */
//print $Delete;exit;
	$Rs=safe_w_sql($Delete);
	//
	// Estraggo la lista di eventi per le finali a squadre
	$Select = "SELECT DISTINCT EcCode, EvPartialTeam, EvMultiTeam, EvMultiTeamNo, EvMixedTeam, EvTeamCreationMode, EvRunning 
		FROM EventClass 
	    INNER JOIN Events ON EcCode=EvCode AND EvTeamEvent=1 AND EcTeamEvent!='0' AND EcTournament=EvTournament 
		WHERE EcTournament=" . StrSafe_DB($ToId);
	if (!is_null($Societa)) {
		$Select
			.= " AND EcDivision=" . StrSafe_DB($Div) . " AND EcClass=" . StrSafe_DB($Cl) . " ";
	}
	$RsSel=safe_r_sql($Select);

	if (safe_num_rows($RsSel)>0) {
		$ToCode=getCodeFromId($ToId);

		while ($RowEv=safe_fetch($RsSel)) {
			$EventCode=$RowEv->EcCode;
			if (!in_array($EventCode,$events4abs)) {
			/*
			 * Da questo array verranno tolti gli eventi ritornati da
			 * SetupTeamRank()
			 */
				$events4abs[]=$EventCode;
			}

			$MyQuery = 'SELECT EcCode, EcTeamEvent, EcNumber, EcDivision, EcClass, EcSubClass '
				. ' FROM EventClass '
				. 'WHERE EcTournament = ' . StrSafe_DB($ToId) . ' AND EcTeamEvent!=0 and EcCode=' . StrSafe_DB($EventCode) . ' '
				. 'ORDER BY EcCode, EcTeamEvent, EcDivision, EcClass';
			$RsDef=safe_r_sql($MyQuery);
			if (!$RsDef) {
				$Errore=1;
			} else {
				$TeamDef=array();
				$TeamNum=array();
				while($MyRowDef=safe_fetch($RsDef)) {
					if(!array_key_exists($MyRowDef->EcTeamEvent, $TeamDef))
						$TeamDef[$MyRowDef->EcTeamEvent] = array();
					$TeamDef[$MyRowDef->EcTeamEvent][] =  ($MyRowDef->EcDivision . "|" . $MyRowDef->EcClass . "|" . $MyRowDef->EcSubClass);
					$TeamPar[$MyRowDef->EcCode]=$RowEv->EvPartialTeam;

					if(!array_key_exists($MyRowDef->EcTeamEvent, $TeamNum))
						$TeamNum[$MyRowDef->EcTeamEvent] = $MyRowDef->EcNumber;

				}
				$MyQuery = 'select * from (';
				foreach($TeamDef as $key=>$value) {
					$ifc=ifSqlForCountry($RowEv->EvTeamCreationMode);
					$MyQuery .= "(SELECT {$ifc} AS Country, " . $key . " as CheQuery, EnId, EnSubTeam,
						QuScore, QuGold, QuXnine, QuHits, " . ($RowEv->EvRunning==1 ? "(QuScore/QuHits)" : "QuScore") . " As ScoreCalc
						FROM Entries 
							INNER JOIN Qualifications ON EnId=QuId and (QuScore>0 or QuHits>0)
						    inner join IrmTypes on IrmId=QuIrmType and IrmShowRank=1 "
//*Taipei (TPE) 2017*/		. "INNER JOIN Countries ON {$ifc}=CoId AND EnTournament=CoTournament "
						. "WHERE EnAthlete=1 AND {$ifc}<>0 "
						. " AND " . ($RowEv->EvMixedTeam ? "EnTeamMixEvent" : "EnTeamFEvent") . "=1 "
						. " AND EnStatus <= 1 AND (QuScore!=0 OR QuHits!=0) AND EnTournament = " . StrSafe_DB($ToId) . " AND ";
//*Taipei (TPE) 2017*/		$MyQuery .=  (($EventCode=="CM" AND $ToCode=="17UNI") ? " (" : "");
                    $tmpQuery = array();
                    foreach ($value as $vDef) {
                        $tmpQuery[] = "CONCAT(EnDivision, '|', EnClass, '|'" . (substr($vDef,-1,1)=='|' ? "" : ", EnSubClass" ) . ") = '$vDef'";
                    }
                    $MyQuery .= '(' .implode(' OR ', $tmpQuery). ')';
//*Taipei (TPE) 2017*/		$MyQuery .= (($EventCode=="CM" AND $ToCode=="17UNI") ? " OR (CONCAT(EnDivision, EnClass)='CW' AND CoCode IN ('GBR', 'GER', 'HKG', 'INA'))) " : "");
					$MyQuery .= (!is_null($Societa) ? " AND {$ifc}=" . StrSafe_DB($Societa)   : '') . ') ';
					$MyQuery .= "UNION ALL ";
				}
				$MyQuery = substr($MyQuery,0,-1*strlen("UNION ALL "))
					. ") TeamEntries 
					group by EnId
					ORDER BY Country, EnSubTeam, CheQuery, ScoreCalc DESC, QuGold DESC, QuXnine DESC,EnId ASC ";
				//print $RowEv->EcCode . ":<br>" . $MyQuery. "<br><br>\n";//exit;
				$Rs=safe_r_sql($MyQuery);
				if (!$Rs) {
					$Errore = 1;
				} else {
					$CurTeam = 0;									//Codice team attuale
					$CurSubTeam = ($RowEv->EvMultiTeam ? 1 : 0);	//Codice Subteam attuale
					$CurSubQuery = 0;								//Codice team attuale
					$TeamCount= array();			//contatore degli elementi trovati
					$CurComponent = array();		//Componenti della Squadra
					$Scores = array();
					$Golds = array();
					$XNines = array();
					$Hits = array();
					while($MyRow=safe_fetch($Rs)) {
						// Change Team
						if($CurTeam != $MyRow->Country OR ($MyRow->EnSubTeam!=0 AND $CurSubTeam!=$MyRow->EnSubTeam)) {
                            //If Partial Team I have to scroll the complete array
						    if ($RowEv->EvPartialTeam==1) {
								foreach($TeamCount as $k=>$value) {
									if(array_sum($TeamCount[$k])>0 AND count($CurComponent[$k])>1 AND ($k==0 OR ($k>0 AND $RowEv->EvMultiTeam!=0 and ($RowEv->EvMultiTeamNo==0 OR $RowEv->EvMultiTeamNo>=$k))))	//Insert Partial Team if not complete yet
								 		WriteTeamAbs($CurTeam,$k,$CurComponent[$k],$EventCode,$Scores[$k],$Golds[$k],$XNines[$k],$Hits[$k], $ToId);
								}
							}
							$CurTeam = $MyRow->Country;
							$CurSubTeam = ($MyRow->EnSubTeam!=0 ? $MyRow->EnSubTeam : ($RowEv->EvMultiTeam ? 1 : 0));
							$CurSubQuery=$MyRow->CheQuery;
							$TeamCount= array();
							$CurComponent = array();
							$Scores = array();
							$Golds = array();
							$XNines = array();
							$Hits = array();
							$TeamCount[$CurSubTeam]=$TeamNum;
							$CurComponent[$CurSubTeam] = array();
							$Scores[$CurSubTeam] = array();
							$Golds[$CurSubTeam] = array();
							$XNines[$CurSubTeam] = array();
							$Hits[$CurSubTeam] = array();
						}
						//Multiteam Management
						if($MyRow->EnSubTeam==0 AND $RowEv->EvMultiTeam AND ($TeamCount[$CurSubTeam][$MyRow->CheQuery] == 0 || $CurSubQuery != $MyRow->CheQuery)) {
							if($CurSubQuery == $MyRow->CheQuery) {
                                $CurSubTeam++;
                            } else {
                                $CurSubTeam = 1;
                            }

							if(empty($TeamCount[$CurSubTeam])) {
								$TeamCount[$CurSubTeam]=$TeamNum;
								$CurComponent[$CurSubTeam] = array();
								$Scores[$CurSubTeam] = array();
								$Golds[$CurSubTeam] = array();
								$XNines[$CurSubTeam] = array();
								$Hits[$CurSubTeam] = array();
							}
							$CurSubQuery=$MyRow->CheQuery;		//Manage the subQueryCounter
						}
						//Looking for team components
						if(array_sum($TeamCount[$CurSubTeam])>0) {
							if($TeamCount[$CurSubTeam][$MyRow->CheQuery]>0) {
								if(!in_array($MyRow->EnId, $CurComponent[$CurSubTeam])) {
									$CurComponent[$CurSubTeam][] = $MyRow->EnId;
									$Scores[$CurSubTeam][] = $MyRow->QuScore;
									$Golds[$CurSubTeam][] = $MyRow->QuGold;
									$XNines[$CurSubTeam][] = $MyRow->QuXnine;
									$Hits[$CurSubTeam][] = $MyRow->QuHits;
									$TeamCount[$CurSubTeam][$MyRow->CheQuery]--;
									//If we have everybody, we save....
									if(array_sum($TeamCount[$CurSubTeam])==0 AND ($RowEv->EvMultiTeamNo==0 OR $RowEv->EvMultiTeamNo>=$CurSubTeam)) {
	                                    WriteTeamAbs($CurTeam, $CurSubTeam, $CurComponent[$CurSubTeam], $EventCode, $Scores[$CurSubTeam], $Golds[$CurSubTeam], $XNines[$CurSubTeam], $Hits[$CurSubTeam], $ToId);
	                                }
								}
							}
						}

					}
                    //If Partial Team I have to scroll the complete array
					if ($RowEv->EvPartialTeam==1) {
						foreach($TeamCount as $k=>$value) {
                            //Insert Partial Team if not complete yet
							if(array_sum($TeamCount[$k])>0 AND count($CurComponent[$k])>1 AND ($k==0 OR ($k>0 AND $RowEv->EvMultiTeam!=0 and ($RowEv->EvMultiTeamNo==0 OR $RowEv->EvMultiTeamNo>=$k)))) {
                                WriteTeamAbs($CurTeam, $k, $CurComponent[$k], $EventCode, $Scores[$k], $Golds[$k], $XNines[$k], $Hits[$k], $ToId);
                            }
						}
					}
				}
			}
		}
	}
	Obj_RankFactory::create('AbsTeam',array(
			'tournament'=> $ToId,
			'events'=>$events4abs
	))->calculate();

	if ($Errore==0 AND is_null($Societa)) {
		$tmp=FindTeamEventsWithNull($ToId);
		if ($tmp===false) {
			$Errore=1;
		} else {
			$Errore=SetupShootoff($tmp, $ToId);

			if ($Errore==0) {
				$toRemove=SetupTeamsRank($tmp, $ToId);

				if ($toRemove!==false) {
					foreach ($toRemove as $e) {
						if (($x=array_search($e,$events4abs))!==false) {
							unset($events4abs[$x]);
						}
					}
				}
			}
		}
		DropTmpTeams();
	}
	return $Errore;
}

function WriteTeamAbs($CurTeam,$CurSubTeam,$CurComponent,$EventCode,$Scores,$Golds,$XNines,$Hits=0, $ToId=0) {
	if(!$ToId) $ToId=$_SESSION['TourId'];
// Insert in Teams
	$InsertQuery
		= "REPLACE INTO Teams (TeCoId,TeSubTeam,TeEvent,TeTournament,TeFinEvent,TeScore,TeGold,TeXNine,TeFinal,TeHits) "
		. "VALUES("
		. StrSafe_DB($CurTeam) . ","
		. StrSafe_DB($CurSubTeam) . ","
		. StrSafe_DB($EventCode) . ","
		. StrSafe_DB($ToId) . ","
		. "'1',"
		. StrSafe_DB(array_sum($Scores)) . ","
		. StrSafe_DB(array_sum($Golds)) . ","
		. StrSafe_DB(array_sum($XNines)) . ","
		. "'0',"
		. StrSafe_DB(array_sum($Hits)) . ""
		. ") ";
	$RsT=safe_w_sql($InsertQuery);
//	if (debug)
//		print $InsertQuery . '<br>';

	// if there is no component of the team returns immediately
	if(empty($CurComponent)) return;

	$InsertQuery = "REPLACE INTO TeamComponent (TcCoId,TcSubTeam, TcTournament,TcEvent,TcFinEvent,TcId,TcOrder) VALUES ";
	for($i=0; $i<count($CurComponent); $i++)
	{
		$InsertQuery .= "("
			. StrSafe_DB($CurTeam) . ","
			. StrSafe_DB($CurSubTeam) . ","
			. StrSafe_DB($ToId) . ","
			. StrSafe_DB($EventCode) . ","
			. "'1',"
			. StrSafe_DB($CurComponent[$i]) . ","
			. StrSafe_DB($i+1)
			. '), ';
	}
	$InsertQuery = substr($InsertQuery,0,-2);
	$RsT=safe_w_sql($InsertQuery);
//	if (debug)
//		print $InsertQuery . '<br><br>';
}

	function ExportLSTInd()
	{
		$options=array('dist' => 0);

		$rank=Obj_RankFactory::create('DivClass',$options);
		$rank->read();
		$rankData=$rank->getData();

//		print '<pre>';
//		print_r($rankData);
//		print '</pre>';exit;

		$MyHeader = array
		(
			'Pos' => 4,
			'Num' => 6,
			'Cl' => 5,
			'Ct' => 3,
			'Tu' => 3,
			'Cognome-Nome' => 27,
			'Sigla' => 9,
			'Societa' => 26,
			'~Dist' => 11,
			'Totale' => 8,
			//'Z' => 4,
			'O' => 4,
			'X/9' => 4
		);

		$StrData = '';


		if (count($rankData['sections'])>0)
		{
		// gli eventi
			$StrData.="\n";
			foreach ($rankData['sections'] as $section)
			{
				$StrData.="\n".$section['meta']['descr']."\n\n";

				foreach ($MyHeader as $Key => $Value)
				{
					if ($Key!='~Dist')
					{
						$StrData.= str_pad($Key,$Value,' ',STR_PAD_RIGHT);
					}
					else
					{
						foreach ($section['meta']['fields'] as $k=>$f)
						{
							//print $k .' - ' . $f."\n";
							if (substr($k,0,5)=='dist_' AND $f!='')
							{
								$StrData.= str_pad($f,$Value,' ',STR_PAD_RIGHT);
							}
						}

					}
				}
				$StrData.="\n";

			// i tizi
				if (count($section['items'])>0)
				{
					foreach ($section['items'] as $item)
					{
						$StrData.= str_pad($item['rank'],$MyHeader['Pos'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad($item['target'],$MyHeader['Num'],' ',STR_PAD_RIGHT);
						$cc='';
						if ($item['ageclass']!=$item['class'])
							$cc=$item['ageclass'];
						$StrData.= str_pad($cc,$MyHeader['Cl'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad($item['subclass'],$MyHeader['Ct'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad($item['session'],$MyHeader['Tu'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad(stripslashes(substr($item['athlete'],0,$MyHeader['Cognome-Nome'])),$MyHeader['Cognome-Nome'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad(substr($item['countryCode'],0,2) . '/' . substr($item['countryCode'],2),$MyHeader['Sigla'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad(stripslashes(substr($item['countryName'],0,$MyHeader['Societa'])),$MyHeader['Societa'],' ',STR_PAD_RIGHT);
						foreach ($item as $k=>$f)
						{
							if (substr($k,0,5)=='dist_' AND $f!='0|0|0|0')
							{
								list($r,$s,$g,$x)=explode('|',$f);
								$StrData.= str_pad($s . '-' . str_pad($r,'2','0',STR_PAD_LEFT),$MyHeader['~Dist'],' ',STR_PAD_RIGHT);
							}
						}
						$StrData.= str_pad($item['score'],$MyHeader['Totale'],' ',STR_PAD_RIGHT);
						$tmp = str_pad($item['gold'],2,'0',STR_PAD_LEFT);
						$StrData.= str_pad($tmp,$MyHeader['O'],' ',STR_PAD_RIGHT);
						$tmp = str_pad($item['xnine'],2,'0',STR_PAD_LEFT);
						$StrData.= str_pad($tmp,$MyHeader['X/9'],' ',STR_PAD_RIGHT);
						$StrData.= "\n";
					}
				}
			}
		}

		return $StrData;
	}

	function ExportLSTTeam()
	{
		$options=array('dist'=>0);

		$rank=Obj_RankFactory::create('DivClassTeam',$options);
		$rank->read();
		$rankData=$rank->getData();

		$MyHeader = array
		(
			'Pos' => 8,
			'Sigla' => 11,
			'Societa' => 26,
			'Cod.Tessera' => 13,
			'Div' => 5,
			'Cl' => 5,
			'Clg' => 5,
			'Ct' => 3,
			'Cognome-Nome' => 27,
			'Totale' => 8,
			'O' => 6,
			'X/9' => 6
		);

		$TotPad = 0;
		foreach ($MyHeader as $Key => $Value)
		{
			if($Key=='Totale')
				break;

			$TotPad+=$Value;
		}

		$StrData = '';

		if (count($rankData['sections'])>0)
		{
		// gli eventi
			$StrData.="\n";
			foreach ($rankData['sections'] as $section)
			{
				$StrData.="\n".$section['meta']['descr']."\n\n";

				foreach ($MyHeader as $Key => $Value)
				{
					$StrData.= str_pad($Key,$Value,' ',STR_PAD_RIGHT);
				}
				$StrData.= "\n";

			// i team
				if (count($section['items'])>0)
				{
					foreach ($section['items'] as $item)
					{
						$StrData.= str_pad($item['rank'],$MyHeader['Pos'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad(substr($item['countryCode'],0,2) . '/' . substr($item['countryCode'],2),$MyHeader['Sigla'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad(stripslashes($item['countryName']),$MyHeader['Societa'],' ',STR_PAD_RIGHT);

					/*
					 * I membri del team.
					 * Il primo lo scrivo fuori dal ciclo, gli altri li faccio ciclare
					 */
						$StrData.= str_pad($item['athletes'][0]['bib'],$MyHeader['Cod.Tessera'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad($item['athletes'][0]['div'],$MyHeader['Div'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad($item['athletes'][0]['class'],$MyHeader['Cl'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad($item['athletes'][0]['ageclass'],$MyHeader['Clg'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad($item['athletes'][0]['subclass'],$MyHeader['Ct'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad(stripslashes(substr($item['athletes'][0]['athlete'],0,$MyHeader['Cognome-Nome'])) ,$MyHeader['Cognome-Nome'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad(str_pad($item['athletes'][0]['quscore'],4,' ',STR_PAD_LEFT),$MyHeader['Totale'],' ',STR_PAD_RIGHT);
					// per ora due *
						$StrData.= str_pad(str_pad($item['athletes'][0]['qugold'],3,' ',STR_PAD_LEFT),$MyHeader['O'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad(str_pad($item['athletes'][0]['quxnine'],3,' ',STR_PAD_LEFT),$MyHeader['X/9'],' ',STR_PAD_RIGHT);
						$StrData.= "\n";
					// gli altri membri del team
						for ($i=1;$i<count($item['athletes']);++$i)
						{
							$StrData.= str_repeat(' ',($MyHeader['Pos']+$MyHeader['Sigla']+$MyHeader['Societa']));
							$StrData.= str_pad($item['athletes'][$i]['bib'],$MyHeader['Cod.Tessera'],' ',STR_PAD_RIGHT);
							$StrData.= str_pad($item['athletes'][$i]['div'],$MyHeader['Div'],' ',STR_PAD_RIGHT);
							$StrData.= str_pad($item['athletes'][$i]['class'],$MyHeader['Cl'],' ',STR_PAD_RIGHT);
							$StrData.= str_pad($item['athletes'][$i]['ageclass'],$MyHeader['Clg'],' ',STR_PAD_RIGHT);
							$StrData.= str_pad($item['athletes'][$i]['subclass'],$MyHeader['Ct'],' ',STR_PAD_RIGHT);
							$StrData.= str_pad(stripslashes(substr($item['athletes'][$i]['athlete'],0,$MyHeader['Cognome-Nome'])) ,$MyHeader['Cognome-Nome'],' ',STR_PAD_RIGHT);
							$StrData.= str_pad(str_pad($item['athletes'][$i]['quscore'],4,' ',STR_PAD_LEFT),$MyHeader['Totale'],' ',STR_PAD_RIGHT);
						// per ora due *
							$StrData.= str_pad(str_pad($item['athletes'][$i]['qugold'],3,' ',STR_PAD_LEFT),$MyHeader['O'],' ',STR_PAD_RIGHT);
							$StrData.= str_pad(str_pad($item['athletes'][$i]['quxnine'],3,' ',STR_PAD_LEFT),$MyHeader['X/9'],' ',STR_PAD_RIGHT);
							$StrData.= "\n";
						}
					// il totale
						$StrData.= str_repeat(' ',$TotPad);
						$StrData.= str_pad(str_pad($item['score'],4,' ',STR_PAD_LEFT),$MyHeader['Totale'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad(str_pad($item['gold'],3,' ',STR_PAD_LEFT),$MyHeader['O'],' ',STR_PAD_RIGHT);
						$StrData.= str_pad(str_pad($item['xnine'],3,' ',STR_PAD_LEFT),$MyHeader['X/9'],' ',STR_PAD_RIGHT);
						$StrData.= "\n\n";
					}
				}
			}
		}

		return $StrData;
	}

	function recalSnapshot($Session, $Distance, $fromTarget, $toTarget)
	{
		CheckTourSession();
		$Select
			= "REPLACE INTO ElabQualifications SELECT EnId as ID, ";
		for($i=1; $i<$Distance; $i++)
			$Select .= "QuD" . $i . "Hits+";

		$Select
			.= "QuD" . $Distance . "Hits AS TotHits, " . $Distance . " AS Distance, "
			. "QuD" . $Distance . "Score AS SelScore,QuD" . $Distance . "Hits AS SelHits,QuD" . $Distance . "Gold AS SelGold,QuD" . $Distance . "Xnine AS SelXNine, "
			. "QuTimeStamp as Tstamp "
			. "FROM Entries "
			. "INNER JOIN Qualifications ON EnId=QuId "
			. "WHERE EnAthlete=1 AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuSession<>0 AND QuTargetNo<>'' AND QuScore>0 AND QuSession=" . StrSafe_DB($Session) . " "
			. "AND QuTargetNo >='" . $Session . str_pad($fromTarget,TargetNoPadding,'0',STR_PAD_LEFT) . "A' AND QuTargetNo<='" . $Session . str_pad($toTarget,TargetNoPadding,'0',STR_PAD_LEFT) . "Z' "
			. "ORDER BY QuTargetNo ASC ";
		$Rs=safe_w_sql($Select);
		return 0;
	}

	function useArrowsSnapshot($Session, $Distance, $fromTarget, $toTarget, $numArrows)
	{
		CheckTourSession();

		/*$Select
			= "SELECT EnId as ID, ". $Distance . " AS Distance, ";
		for($i=1; $i<$Distance; $i++)
			$Select .= "QuD" . $i . "Hits+";
		$Select .= $numArrows . " AS TotHits, ";

		$Select .= "SUBSTRING(QuD" . $Distance . "ArrowString,1," . $numArrows . ") AS TotArrowString, ";

		$Select
			.= "TtGolds, TtXNine, QuTimeStamp as Tstamp "
			. "FROM Entries "
			. "INNER JOIN Qualifications ON EnId=QuId "
			. "INNER JOIN Tournament ON EnTournament=ToId "
			. "INNER JOIN Tournament*Type ON ToType=TtId "
			. "WHERE EnAthlete=1 AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuTargetNo<>'' AND QuSession=" . StrSafe_DB($Session) . " "
			. "AND QuTargetNo >='" . $Session . str_pad($fromTarget,TargetNoPadding,'0',STR_PAD_LEFT) . "A' AND QuTargetNo<='" . $Session . str_pad($toTarget,TargetNoPadding,'0',STR_PAD_LEFT) . "Z' "
			. "ORDER BY QuTargetNo ASC ";*/

		$Select
			= "SELECT EnId as ID, ". $Distance . " AS Distance, ";
		for($i=1; $i<$Distance; $i++)
			$Select .= "QuD" . $i . "Hits+";
		$Select .= $numArrows . " AS TotHits, ";

		$Select .= "SUBSTRING(QuD" . $Distance . "ArrowString,1," . $numArrows . ") AS TotArrowString, ";

		$Select
			//.= "ToGolds AS TtGolds, ToXNine AS TtXNine, QuTimeStamp as Tstamp "
			.= "ToGoldsChars, ToXNineChars, QuTimeStamp as Tstamp "
			. "FROM Entries "
			. "INNER JOIN Qualifications ON EnId=QuId "
			. "INNER JOIN Tournament ON EnTournament=ToId "
			. "WHERE EnAthlete=1 AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuTargetNo<>'' AND QuSession=" . StrSafe_DB($Session) . " "
			. "AND QuTargetNo >='" . $Session . str_pad($fromTarget,TargetNoPadding,'0',STR_PAD_LEFT) . "A' AND QuTargetNo<='" . $Session . str_pad($toTarget,TargetNoPadding,'0',STR_PAD_LEFT) . "Z' "
			. "ORDER BY QuTargetNo ASC ";

		//echo $Select."<br><br>";exit;
		$Rs=safe_r_sql($Select);
		if (safe_num_rows($Rs)>0)
		{
			while ($myRow=safe_fetch($Rs))
			{
				list($Score,$Gold,$XNine)=ValutaArrowStringGX($myRow->TotArrowString,$myRow->ToGoldsChars,$myRow->ToXNineChars);
				$Select
					= "REPLACE INTO ElabQualifications VALUES("
					. StrSafe_DB($myRow->ID) . ", "
					. StrSafe_DB($myRow->TotHits) . ", "
					. StrSafe_DB($myRow->Distance) . ", "
					. StrSafe_DB($Score) . ", "
					. StrSafe_DB($numArrows) . ", "
					. StrSafe_DB($Gold) . ", "
					. StrSafe_DB($XNine) . ", "
					. StrSafe_DB($myRow->Tstamp) . ")";
				if(strlen(rtrim($myRow->TotArrowString))==$numArrows)
					$RsUpdate=safe_w_sql($Select);
			}
		}
		return $numArrows;
	}

function useArrowsSnapshotTarget($Distance, $Target, $numArrows) {
	CheckTourSession();

	$Select = "SELECT EnId as ID, ". $Distance . " AS Distance, ";
	for($i=1; $i<$Distance; $i++) {
		$Select .= "QuD" . $i . "Hits+";
	}
	$Select .= $numArrows . " AS TotHits, 
			SUBSTRING(QuD" . $Distance . "ArrowString,1," . $numArrows . ") AS TotArrowString,
			ToGoldsChars, ToXNineChars, QuTimeStamp as Tstamp 
		FROM Entries 
		INNER JOIN Qualifications ON EnId=QuId 
		INNER JOIN Tournament ON EnTournament=ToId 
		WHERE EnAthlete=1 
			AND EnTournament={$_SESSION['TourId']} 
			AND QuTargetNo ='$Target' 
		ORDER BY QuTargetNo ASC ";

	//echo $Select."<br><br>";exit;
	$Rs=safe_r_sql($Select);
	while ($myRow=safe_fetch($Rs)) {
		list($Score,$Gold,$XNine)=ValutaArrowStringGX($myRow->TotArrowString,$myRow->ToGoldsChars,$myRow->ToXNineChars);
		$Select = "REPLACE INTO ElabQualifications VALUES("
			. StrSafe_DB($myRow->ID) . ", "
			. StrSafe_DB($myRow->TotHits) . ", "
			. StrSafe_DB($myRow->Distance) . ", "
			. StrSafe_DB($Score) . ", "
			. StrSafe_DB($numArrows) . ", "
			. StrSafe_DB($Gold) . ", "
			. StrSafe_DB($XNine) . ", "
			. StrSafe_DB($myRow->Tstamp) . ")";
		if(strlen(rtrim($myRow->TotArrowString))==$numArrows) {
			safe_w_sql($Select);
		}
	}
}

/**
 * Ritorna la stringa che contiene la if sql da usare per stabilire qualche EnCountry usare.
 * C'è una funzione perchè la if è lunga da portare nelle varie query soprattutto se ci saranno cambi.
 * E' necessaria la presenza del campo EvTeamCreationMode quindi occorrerà avera la join corretta con Events
 *
 * @return string: if sql da includere nelle query
 */
	function ifSqlForCountryWithJoin()
	{
		return "
			IF(EvTeamCreationMode=0,IF(EnCountry2=0,EnCountry,EnCountry2),IF(EvTeamCreationMode=1,EnCountry,IF(EvTeamCreationMode=2,EnCountry2,EnCountry3)))
		";
	}

/**
 * Come quella con join ma riceve il parametro per decidere come il team viene creato
 * @param int $mode: modalità
 * @return string: if sql da includere nelle query
 */
	function ifSqlForCountry($mode=0)
	{
		$q="";

		switch ($mode)
		{
			case 0:
				$q=" IF(EnCountry2=0,EnCountry,EnCountry2) ";
				break;
			case 1:
				$q=" EnCountry ";
				break;
			case 2:
				$q=" EnCountry2 ";
				break;
			case 3:
				$q=" EnCountry3 ";
				break;
			default:
				$q=" IF(EnCountry2=0,EnCountry,EnCountry2) ";

		}

		return $q;
	}

function GetEvents($Type='I', $tour=0) {
	if($tour==0 AND $_SESSION['TourId']!=0) {
        $tour=$_SESSION['TourId'];
    }
	$ret=array();
	$TeamFilter='';
	if($Type=='I') {
		$TeamFilter='and EvTeamEvent=0';
	} elseif($Type=='T') {
		$TeamFilter='and EvTeamEvent=1';
	}

	$q=safe_r_sql("select * from Events where EvTournament={$tour} $TeamFilter order by EvProgr");
	while($r=safe_fetch($q)) $ret[$r->EvCode]=$r;

	return $ret;
}
