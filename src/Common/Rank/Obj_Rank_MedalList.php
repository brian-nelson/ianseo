<?php

class Obj_Rank_MedalList extends Obj_Rank
{
	public function calculate()
	{
		return true;
	}

	public function read()
	{
		$tourId=StrSafe_DB($this->tournament);

		$this->data=array(
			'title'=>get_text('MedalList'),
			'lastUpdate'=>'0000-00-00 00:00:00',
			'fields'=>array(
				'evCode'=>get_text('EvCode'),
				'evName'=>get_text('EvName'),
				'divCode'=>get_text('Division'),
				'divName'=>get_text('Division'),
				'clCode'=>get_text('Class'),
				'clName'=>get_text('Class'),
				'date'=>get_text('Date','Tournament'),
				'gold'=>get_text('MedalGold'),
				'silver'=>get_text('MedalSilver'),
				'bronze'=>get_text('MedalBronze'),
				'qualScore'=>get_text('QualRound'),
			),
			//'fields'=>array(),
			'events'=>array()
		);

	/*
	 * Qui dentro ci saranno le queries che comporranno la union.
	 * Ocio che per divcl avremo che ci sarà o no la query in questione mentre per le finali, distinguiamo solo
	 * finale ind da finale team perchè le righe dei senza match vengono messe insieme a quelle con i match e
	 * solo quando viene popolato l'array $this->data skippo i senza match a meno di non averli richiesti.
	 */
		$queries=array();

	// divcl ind
		if (isset($this->opts['divcl']['i']) && $this->opts['divcl']['i']==true)
		{
			$queries[]="
				(
					SELECT CONCAT_WS('|',DivId,ClId) as EvCode, CONCAT_WS('|',DivDescription,ClDescription) as EvName, 1 as indEvent, 0 as finEvent, 0 as hasFinals,
					QuClRank as Rank, QuScore, CoCode, CoName, CONCAT_WS(',',EnCode,EnFirstName,EnName) as Athlete, ToWhenTo as Date,
					QuTimestamp as lastUpdate, 3 AS myOrder,(DivViewOrder+ClViewOrder*1000) AS Progr,
					DivId, DivDescription, ClId, ClDescription
					FROM Tournament
					INNER JOIN Entries ON ToId=EnTournament
					INNER JOIN Qualifications ON EnId=QuId
					INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
					INNER JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament
					INNER JOIN Classes ON EnClass=ClId AND EnTournament=ClTournament
					WHERE EnTournament={$tourId} AND EnStatus<=1 AND QuClRank BETWEEN 1 AND 3
					ORDER BY Progr ASC, EvCode ASC, Rank ASC, EnFirstName ASC, EnName ASC
				)
			";
		}

	// divcl team
		if (isset($this->opts['divcl']['t']) && $this->opts['divcl']['t']==true)
		{
			$queries[]="
				(
					SELECT EvCode, EvName, 0 as indEvent, 0 as finEvent, 0 AS hasFinals,
					TeRank as Rank, TeScore QuScore, CoCode, CoName,
					GROUP_CONCAT(CONCAT_WS(',',EnCode,EnFirstName,EnName) ORDER BY TcOrder SEPARATOR '|') as Athlete, ToWhenTo as Date,
					TeTimeStamp as lastUpdate, 4 AS myOrder,(DivViewOrder+ClViewOrder*1000) AS Progr,
					DivId, DivDescription, ClId, ClDescription
					FROM Tournament
					INNER JOIN Teams ON ToId=TeTournament
					INNER JOIN Countries ON TeCoId=CoId AND TeTournament=CoTournament
					INNER JOIN TeamComponent ON TcCoId=TeCoId AND TcSubTeam=TeSubTeam AND TcTournament=TeTournament AND TcEvent=TeEvent AND TcFinEvent=TeFinEvent
					INNER JOIN Entries ON EnId=TcId AND EnTournament=TcTournament
					INNER JOIN (
					  SELECT CONCAT(DivId, ClId) DivClass, CONCAT_WS('|',DivId,ClId) as EvCode, CONCAT_WS('|',DivDescription,ClDescription) as EvName, DivViewOrder, ClViewOrder,
						DivId, DivDescription, ClId, ClDescription
					  FROM Divisions
					  INNER JOIN Classes
					  ON DivTournament=ClTournament
					  WHERE DivTournament={$tourId} AND DivAthlete AND ClAthlete
					) as sq ON TeEvent=DivClass
					WHERE TeTournament={$tourId} AND TeFinEvent=0 AND TeRank BETWEEN 1 AND 3
					GROUP BY EvCode, EvName, indEvent, finEvent, hasFinals, Rank, CoCode, CoName
					ORDER BY Progr ASC, EvCode ASC, Rank ASC, CoCode ASC, CoName ASC
				)
			";
		}

	// finali ind (con o senza scontri)
		if (!isset($this->opts['final']['i']) || $this->opts['final']['i']==true)
		{
			$queries[]="
				(
					SELECT EvCode, EvEventName as EvName, 1 as indEvent, 1 as finEvent, (EvFinalFirstPhase!=0) AS hasFinals,
					IF(EvFinalFirstPhase!=0, IndRankFinal, IndRank) as Rank, QuScore, CoCode, CoName, CONCAT_WS(',',EnCode,EnFirstName,EnName) as Athlete,
					IFNULL(FSScheduledDate,ToWhenTo) as Date, IF(EvFinalFirstPhase!=0, IndTimestampFinal, IndTimestamp) as lastUpdate, 1 AS myOrder,EvProgr AS Progr,
					DivId, DivDescription, ClId, ClDescription
					FROM Tournament
					INNER JOIN Events ON EvTournament=ToId
					LEFT JOIN Individuals ON EvCode=IndEvent AND EvTournament=IndTournament AND IF(EvFinalFirstPhase!=0, IndRankFinal, IndRank) BETWEEN 1 AND 3
					LEFT JOIN (select * from Entries
						INNER JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament
						INNER JOIN Classes ON EnClass=ClId AND EnTournament=ClTournament) Entry ON IndId=EnId AND IndTournament=EnTournament
					LEFT JOIN Qualifications ON EnId=QuId
					LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
					LEFT JOIN FinSchedule ON EvCode=FSEvent AND EvTeamEvent=FSTeamEvent AND EvTournament=FSTournament AND FSMatchNo=0
					WHERE EvTournament={$tourId} AND EvTeamEvent=0 AND EvMedals!=0
					ORDER BY Progr ASC, EvCode ASC, Rank ASC, EnFirstName ASC, EnName ASC
				)
			";
		}

	// finali team (con o senza scontri)
		if (!isset($this->opts['final']['t']) || $this->opts['final']['t']==true)
		{
			$queries[]="
				(
					SELECT EvCode, EvEventName as EvName, 0 as indEvent, 1 as finEvent, (EvFinalFirstPhase!=0) AS hasFinals,
					IF(EvFinalFirstPhase!=0, TeRankFinal, TeRank) as Rank, 0 QuScore, CoCode, CoName,
					GROUP_CONCAT(IF(EvFinalFirstPhase!=0, CONCAT_WS(',',ef.EnCode,ef.EnFirstName,ef.EnName),CONCAT_WS(',',eq.EnCode,eq.EnFirstName,eq.EnName)) ORDER BY IF(EvFinalFirstPhase!=0, TfcOrder,TcOrder) SEPARATOR '|') as Athlete,
					IFNULL(FSScheduledDate,ToWhenTo) as Date, IF(EvFinalFirstPhase!=0, TeTimeStampFinal, TeTimeStamp) as lastUpdate, 2 AS myOrder,EvProgr AS Progr,
					DivId, DivDescription, ClId, ClDescription
					FROM Tournament
					INNER JOIN Events ON ToId=EvTournament
					LEFT JOIN Teams ON EvCode=TeEvent AND EvTournament=TeTournament AND TeFinEvent!=0 AND IF(EvFinalFirstPhase!=0, TeRankFinal, TeRank) BETWEEN 1 AND 3
					LEFT JOIN Countries ON TeCoId=CoId AND TeTournament=CoTournament
					LEFT JOIN TeamFinComponent ON TfcCoId=TeCoId AND TfcSubTeam=TeSubTeam AND TfcTournament=TeTournament AND TfcEvent=TeEvent AND IF(EvFinalFirstPhase=0, 0, 1)=1
					LEFT JOIN (select * from Entries
						INNER JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament
						INNER JOIN Classes ON EnClass=ClId AND EnTournament=ClTournament) as ef ON ef.EnId=TfcId AND ef.EnTournament=TfcTournament
					LEFT JOIN TeamComponent ON TcCoId=TeCoId AND TcSubTeam=TeSubTeam AND TcTournament=TeTournament AND TcEvent=TeEvent AND TcFinEvent=TeFinEvent AND IF(EvFinalFirstPhase!=0, 0, 1)=1
					LEFT JOIN Entries as eq ON eq.EnId=TcId AND eq.EnTournament=TcTournament
					LEFT JOIN FinSchedule ON EvCode=FSEvent AND EvTeamEvent=FSTeamEvent AND EvTournament=FSTournament AND FSMatchNo=0
					WHERE EvTournament={$tourId} AND EvTeamEvent=1 AND EvMedals!=0
					GROUP BY EvCode, EvName, indEvent, finEvent, hasFinals, Rank, CoCode, CoName
					ORDER BY Progr ASC, EvCode ASC, Rank ASC, CoCode ASC, CoName ASC
				)
			";
		}

		if (count($queries)>0)
		{
			$mapMedal=array(
				1 => 'gold',
				2 => 'silver',
				3 => 'bronze'
			);

			$q=implode(' UNION ALL ',$queries) . " ORDER BY Date ASC, myOrder ASC,Progr ASC, EvCode ASC, Rank ASC";
			//print $q.'<br><br>';
			$r=safe_r_sql($q);

			if ($r && safe_num_rows($r)>0)
			{
				while ($row=safe_fetch($r))
				{
					$type=$row->indEvent . $row->finEvent . $row->hasFinals;
				// skippo le rank nulle
					if ($row->Rank===null)
					{
						continue;
					}

				// se ho detto di non volere le finali ind senza match skippo (se la riga è una finale ind senza match!)
					if ($type == '110' && (!isset($this->opts['noMatch']['i']) || $this->opts['noMatch']['i']==false))
					{
						continue;
					}

				// se ho detto di non volere le finali team senza match skippo (se la riga è una finale team senza match!)
					if ($type == '010' && (!isset($this->opts['noMatch']['t']) || $this->opts['noMatch']['t']==false))
					{
						continue;
					}

				// verifico e sistemo il lastupdate e necessario
					if($this->data['lastUpdate']<$row->lastUpdate)
						$this->data['lastUpdate']=$row->lastUpdate;

					$evCode=$row->EvCode;
					$evName=$row->EvName;
				// queste 4 solo per le divcl
					$divCode='';
					$divName='';
					$clCode='';
					$clName='';

					if ($type[1]=='0')	// divcl
					{
						$evCode=str_replace('|','',$row->EvCode);
						$evName=str_replace('|',' - ',$row->EvName);

						list($divCode,$clCode)=explode('|',$row->EvCode);
						list($divName,$clName)=explode('|',$row->EvName);
					}

				// chiave per l'evento
					$evKey=$evCode . '_' . ($row->indEvent==1 ? 'I' : 'T') . '_' . $row->finEvent;

				// se non c'è l'evento con i flag uguali lo creo
					if (!isset($this->data['events'][$evKey]))
					{
						$this->data['events'][$evKey]=array(
							'evCode'=>$evCode,
							'evName'=>$evName,

							'divCode'=>$row->DivId,
							'divName'=>$divName,

							'clCode'=>$row->ClId,
							'clName'=>$clName,

							'date'=>$row->Date,

						// flags non localizzati!
							'indEvent'=>$row->indEvent,
						 	'finEvent'=>$row->finEvent,
							'hasFinals'=>$row->hasFinals,
						);
					}

				// se non ho ancora una medaglia del tipo in questione creo l'array prima di usarlo
					$medal=$mapMedal[$row->Rank];
					if (!isset($this->data['events'][$evKey][$medal]))
					{
						$this->data['events'][$evKey][$medal]=array();
					}

				/*
				 * Ora aggiungo la persona.
				 * Prima genero la lista delle persone.
				 * Nel campo del recordset le virgole separano gli attributi della persona mentre le pipe separano
				 * le persone
				 */
					$athletes=array();

					$tmp=explode('|',$row->Athlete);
					foreach ($tmp as $ath)
					{
						$tmp1=explode(',',$ath);
						$athletes[]=array(
							'bib'=>$tmp1[0],
							'familyName'=>$tmp1[1],
							'name'=>$tmp1[2],
							'athlete'=> mb_convert_case($tmp1[1],MB_CASE_UPPER,'UTF-8') . ' ' . mb_convert_case($tmp1[2],MB_CASE_TITLE,'UTF-8')
						);
					}

				// aggiungo la nazione e le persone
					$this->data['events'][$evKey][$medal][]=array(
						'countryCode'=>$row->CoCode,
						'countryName'=>$row->CoName,
						'qualScore'=>$row->QuScore,
						'athletes'=>$athletes
					);
				}
			}

		}
	}
}