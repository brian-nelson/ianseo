<?php

/**
 * MakeTeams per la generazione delle suqadre dei Giochi Studenteschi Italiani - Tipo 19
 */

// Elimino le squadre della qualifica
	$Delete
		= "DELETE Teams, TeamComponent FROM "
		. "Teams, TeamComponent  "
		. "WHERE TeCoId=TcCoId AND TeEvent=TcEvent AND TeTournament=TcTournament AND TeTournament=" . StrSafe_DB($ToId)
		. (!is_null($Societa) ? ' AND TeCoId=' . StrSafe_DB($Societa) . ' AND TcCoId=' . StrSafe_DB($Societa)  : '')
		. (!is_null($Category) ? ' AND TeEvent=' . StrSafe_DB($Category) . ' AND TcEvent=' . StrSafe_DB($Category) : '')
		. " AND TeFinEvent='0' AND TcFinEvent='0' ";
//	if (debug)
		//print $Delete . '<br><br>';exit;
	$Rs=safe_w_sql($Delete);


	// TODO: bisogna approfondire e comunque rifare le squadre (firmato Doc)
	$Select = "SELECT EnTournament,EnId,IF(EnCountry2=0,EnCountry,EnCountry2) as EnCountry, CONCAT(EnDivision,EnClass) AS Event, 
			QuClRank, QuScore, QuGold, QuXnine
		FROM Entries 
	    INNER JOIN Qualifications ON EnId=QuId
		inner join IrmTypes on IrmId=QuIrmType and IrmShowRank=1
	    WHERE EnAthlete=1 AND EnTeamClEvent=1 AND EnStatus <= 1 AND EnTournament = " . StrSafe_DB($ToId) . " AND QuScore>0 "
		. (!is_null($Societa) ? ' AND IF(EnCountry2=0,EnCountry,EnCountry2)=' . StrSafe_DB($Societa)   : '')
		. (!is_null($Category) ? ' AND CONCAT(EnDivision,EnClass)=' . StrSafe_DB($Category)  : '')
		. " ORDER BY IF(EnCountry2=0,EnCountry,EnCountry2),CONCAT(EnDivision,EnClass), QuClRank ASC, QuScore DESC,QuGold DESC, QuXnine DESC,EnId ASC ";

	$Rs=safe_r_sql($Select);


	// Variabili di Servizio
	$Peoples = 0;	// Contatore delle persone
	$MyCountry = 0;
	$MyEvent = '';
	$Countries=array(0,0,0);
	$Event=array(0,0,0);
	$Aths = array(0,0,0);
	$Scores = array(0,0,0);
	$Points = array(0,0,0);
	$Golds = array(0,0,0);
	$XNines = array(0,0,0);

	//Ciclo per Scorrere l'elenco partecipanti
	if (safe_num_rows($Rs))
	{
		while ($MyRow=safe_fetch($Rs))
		{
			//Cambio di società
			if ($MyCountry != $MyRow->EnCountry || $MyEvent != $MyRow->Event)
			{
				$Peoples=0;

				$Countries=array(0,0,0);
				$Event=array(0,0,0);
				$Aths = array(0,0,0);
				$Scores = array(0,0,0);
				$Points = array(0,0,0);
				$Golds = array(0,0,0);
				$XNines = array(0,0,0);

				$MyCountry = $MyRow->EnCountry;
				$MyEvent = $MyRow->Event;
			}
			else
				++$Peoples;

			//Ho la Squadra----> Salvo
			if ($Peoples<=2)
			{
				$Aths[$Peoples]=$MyRow->EnId;
				$Countries[$Peoples]=$MyRow->EnCountry;
				$Events[$Peoples]=$MyRow->Event;
				$Scores[$Peoples]=$MyRow->QuClRank;
				$Points[$Peoples]=$MyRow->QuScore;
				$Golds[$Peoples]=$MyRow->QuGold;
				$XNines[$Peoples]=$MyRow->QuXnine;

			// se ho proprio 3 persone faccio la squadra
				if ($Peoples==2)
				{

				// Insert in Teams
					$InsertT
						= "INSERT INTO Teams (TeCoId,TeEvent,TeTournament,TeFinEvent,TeScore,TeHits,TeGold,TeXNine,TeFinal) "
						. "VALUES("
						. StrSafe_DB($Countries[0]) . ","
						. StrSafe_DB($Events[0]) . ","
						. StrSafe_DB($ToId) . ","
						. "'0',"
						. StrSafe_DB($Scores[0]+$Scores[1]+$Scores[2]) . ","
						. StrSafe_DB($Points[0]+$Points[1]+$Points[2]) . ","
						. StrSafe_DB($Golds[0]+$Golds[1]+$Golds[2]) . ","
						. StrSafe_DB($XNines[0]+$XNines[1]+$XNines[2]) . ","
						. "'0'"
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
							. StrSafe_DB($Events[$i]) . ","
							. "'0',"
							. StrSafe_DB($Aths[$i]) . ","
							. StrSafe_DB(($i+1)) . ""
							. ") ";
						$RsTC[$i]=safe_w_sql($InsertTC);
					}

					if (!$RsT || !$RsTC[0] || !$RsTC[1] || !$RsTC[2])
					{
						$Errore=1;
					}
				}
			}
		}
	}

?>