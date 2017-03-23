<?php
/**
 * Obj_Rank_ElimInd
 * Implementa l'algoritmo di default per il calcolo della rank dei gironi delle eliminatorie.
 *
 * La tabella in cui vengono scritti i valori è la Eliminations.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		eventsC => array(<ev_1>@<calcPhase_1>,<ev_2>@<calcPhase_2>,...,<ev_n>@<calcPhase_n>)	[calculate,non influisce su read]
 * 		eventsR => array(<ev_1>@<phase_1>,<ev_2>@<phase_2>,...,<ev_n>@<phase_n>)				[read,non influisce su calculate], se presente events verrà ignorato
 * 		events =>  array(<ev_1>,<ev_2>,...,<ev_n>)												[read,non influisce su calculate]
 * 		skipExisting => #																		[calculate]
 * 		tournament => #																			[calculate/read]
 * )
 *
 * con:
 * 	 eventsC: l'array con le coppie evento@fase di cui voglio il calcolo.
 * 	 	I valori calcPhase_n servono a calculate() per scegliere se stiamo parlando del primo girone oppure del secondo.
 * 			I valori sono:
 * 				2: voglio calcolare il secondo girone
 * 				1: voglio calcolare il primo
 *	eventsR: come eventsC ma per la lettura
 *  events: array di eventi.
 *	tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *  skipExisting: Se 1 non sovrascrive posizione e frecce di SO dove sono già valorizzati
 *
 *  NOTA BENE:
 *  	In questa classe ci sono 3 elementi per i filtri: uno per la scrittura e gli altri per la lettura.
 *  	eventsC serve perchè per scrivere occorre sapere di che evento e di che girone si sta parlando; eventsR e events vanno usato
 *  	in mutua esclusione (se presente eventsR events verrà ignorato).
 *  	events estrae per gli eventi passati tutti i gironi ed è comodo per stampare le classifiche.
 *  	eventsR invece ha lo scopo di eventsC e torna utile per estrarre le classifiche per gli spareggi.
 *
 * $data ha la seguente forma
 *
 * array(
 * 		meta 		=> array(
 * 			title 	=> <titolo della classifica localizzato>
 *		),
 * 		sections 	=> array(
 * 			event_1 => array(
 * 				meta => array(
 * 					event => <event_1>, valore uguale alla chiave
 * 					descr => <descrizione evento localizzata>
 * 					qualifiedNo => <numero di persone qualificate per l'evento>
 * 					fields(*1) => array(
 *						id 				=> <id della persona>
 *                      bib 			=> <codice della persona>
 *                      target 			=> <piazzola>
 *                      athlete 		=> <cognome e nome>
 *                      familyname 		=> <cognome>
 *						givenname 		=> <nome>
 *                      div				=> <codice divisione>
 *                      cl				=> <codice classe>
 *                      subclass 		=> <categoria>
 *                      countryCode 	=> <codice nazione>
 *                      countryName 	=> <nazione>
 *                      rank 			=> <rank in base alla distanza>
 *                      score 			=> <punti in base alla distanza>
 *                      gold 			=> <ori in base alla distanza>
 *                      xnine 			=> <xnine in base alla distanza>
 *                      ct				=> <numero di cointoss (gialli)>	(distanza 0)
 *                      so				=> <1 se shootoff (rosso)>			(distanza 0)
 * 					)
 *				)
 * 				items => array(
 * 					array(id=><valore>,bib=><valore>,...,dist_8=><valore>),
 * 					...
 * 				)
 * 			)
 * 			...
 * 			event_n = ...
 * 		)
 * )
 * Estende Obj_Rank
 */
	class Obj_Rank_ElimInd extends Obj_Rank
	{
		protected function safeFilterC()
		{
			$filter=false;

			if (array_key_exists('eventsC',$this->opts))
			{
				if (is_array($this->opts['eventsC']) && count($this->opts['eventsC'])>0)
				{
					$filter=array();

					foreach ($this->opts['eventsC'] as $e)
					{
						list($event,$phase)=explode('@',$e);

						$filter[]=StrSafe_DB($event . ($phase-1));
					}

					$filter=" AND CONCAT(ElEventCode,ElElimPhase) IN (" . implode(',',$filter).") ";
				}
				else
					$filter=false;
			}
			else
				$filter=false;

			return $filter;
		}

		protected function safeFilterR()
		{
			$filter=false;

			if (array_key_exists('eventsR',$this->opts))
			{
				if (is_array($this->opts['eventsR']) && count($this->opts['eventsR'])>0) {
					$f=array();

					foreach ($this->opts['eventsR'] as $e) {
						@list($event,$phase)=explode('@',$e);
						if($event and !is_null($phase)) $f[] = '(el.ElEventCode=' . StrSafe_DB($event) . ' AND el.ElElimPhase=' . ($phase-1) . ')';
						elseif($event) $f[] = '(el.ElEventCode=' . StrSafe_DB($event) . ')';
						elseif($phase) $f[] = '(el.ElElimPhase=' . ($phase-1) . ')';
					}

					if($f) $filter=" AND (" . implode(' OR ',$f) . ") ";
				}
				else
					$filter=false;
			}
			elseif (array_key_exists('events',$this->opts))
			{
				if (is_array($this->opts['events']) && count($this->opts['events'])>0)
				{
					$filter=array();

					foreach ($this->opts['events'] as $e)
					{
						$filter[]=StrSafe_DB($e);
					}

					$filter=" AND el.ElEventCode IN (" . implode(',',$filter).") ";
				}
				else
					$filter=false;
			}

			if (!empty($this->opts['enid'])) {
				$filter.=" AND ElId=" . intval($this->opts['enid']) . " ";
			}

			return $filter;
		}

	/**
	 * calculate()
	 * La classifica viene calcolata quando si cambia un punteggio oppure quando si risolvono gli
	 * spareggi per passare al girone dopo oppure alle finali.
	 * Nel primo caso si chiama direttamente questo metodo; nel secondo si userà setRow() utilizzata pure
	 * da questo metodo.
	 *
	 * @override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#calculate()
	 */
		public function calculate()
		{
			return true;
		}

		public function read()
		{
			$f=$this->safeFilterR();
			$filter=($f!==false ? $f : "");

			$q="
				SELECT
					EnId,EnCode, EnSex, EnNameOrder, EnName AS Name, EnFirstName AS FirstName, upper(EnFirstName) AS FirstNameUpper, el.ElTargetNo AS TargetNo,
					CoId, CoCode, CoName, EnClass, EnDivision,EnAgeClass,  EnSubClass,
					if(el.ElElimPhase=0, 'Eliminations_1', if(el.ElElimPhase=1 and EvElim1!=0 and EvElim2!=0, 'Eliminations_2', 'Eliminations')) as roundText,
					ElScore, ElRank, ElGold, ElXnine, ElHits, ElTiebreak, ToGolds AS GoldLabel, ToXNine AS XNineLabel,
					/*IF(EvElim1=0 && EvElim2=0, IF(EvFinalFirstPhase=48, 104, IF(EvFinalFirstPhase=24, 56, (EvFinalFirstPhase*2))) ,IF(EvElim1=0,EvElim2,EvElim1)) as QualifiedNo,*/
					IF(el.ElElimPhase=0,EvElim2,IF(EvFinalFirstPhase=48, 104, IF(EvFinalFirstPhase=24, 56, (EvFinalFirstPhase*2)))) AS QualifiedNo,
					EvProgr,EvCode,EvEventName,el.ElElimPhase, EvRunning, IF(EvRunning=(el.ElElimPhase+2),(IFNULL(ROUND(ElScore/ElHits,3),0)),0) as RunningScore,
					sqY.Quanti AS NumCT,
					(el.ElSO>0) AS isSO,
					ABS(ElSO) AS RankBeforeSO,
					el.ElDateTime,
					ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
					date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
					ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes
				FROM
					Eliminations as el
					INNER JOIN
						Entries
					ON el.ElId=EnId AND el.ElTournament=EnTournament
					INNER JOIN
						Countries
					ON EnCountry=CoId AND EnTournament=CoTournament
					INNER JOIN
						Tournament
					ON el.ElTournament=ToId
					INNER JOIN
						Events
					ON EvCode=el.ElEventCode AND EvTournament=el.ElTournament AND EvTeamEvent=0
					LEFT JOIN DocumentVersions DV1 on EvTournament=DV1.DvTournament AND DV1.DvFile = 'ELIM' and DV1.DvEvent=''
					LEFT JOIN DocumentVersions DV2 on EvTournament=DV2.DvTournament AND DV2.DvFile = 'ELIM' and DV2.DvEvent=EvCode
					/* contatore ct */
					LEFT JOIN
						(
							SELECT
								Count(*) as Quanti, ElEventCode, ElElimPhase, ElSO as sqyRank, ElTournament
							FROM
								Eliminations AS el
							WHERE
								ElTournament={$this->tournament} AND ElSO!=0 {$filter}
							GROUP BY
								ElSO,ElEventCode,ElElimPhase,ElTournament
						) AS sqY
					ON sqY.sqyRank=ElSO AND el.ElEventCode=sqY.ElEventCode AND el.ElElimPhase=sqY.ElElimPhase AND el.ElTournament=sqY.ElTournament
				WHERE
					ToId={$this->tournament} {$filter}
				" . (!empty($this->opts['coid']) ? " AND EnCountry=" . intval($this->opts['coid']) . " " : "" ) . "
				ORDER BY
					EvProgr ASC,EvCode, el.ElElimPhase ASC, RunningScore DESC, el.ElRank,EnFirstName,EnName
			";

			$r=safe_r_sql($q);

			$this->data['meta']['title']=get_text('Elimination');
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
			$this->data['sections']=array();

			if ($r && safe_num_rows($r)>0)
			{
				$curEvent='';

				$section=null;

				$runningOldScore=-1;
				$runningPos=0;
				$runningRank=0;

				while ($myRow=safe_fetch($r))
				{
					if ($curEvent!=$myRow->EvCode.$myRow->ElElimPhase)
					{
					/*
					 *  se non sono all'inizio, prima di iniziare una sezione devo prendere quella appena fatta
					 *  e accodarla alle altre
					 */
						if ($curEvent!='')
						{
							$this->data['sections'][$curEvent]=$section;
							$section=null;
						}
					// al cambio creo una nuova sezione
						$curEvent=$myRow->EvCode.$myRow->ElElimPhase;

					// qui ci sono le descrizioni dei campi
						$fields=array(
							'id'  => 'Id',
							'bib' => get_text('Code','Tournament'),
							'target' => get_text('Target'),
							'athlete' => get_text('Athlete'),
							'familyname' => get_text('FamilyName', 'Tournament'),
							'givenname' => get_text('Name', 'Tournament'),
							'gender' => get_text('Sex', 'Tournament'),
							'div' => get_text('Division'),
							'class' => get_text('Class'),
							'subclass' => get_text('SubCl','Tournament'),
							'countryId' => '',
							'countryCode' => '',
							'countryName' => get_text('Country'),
							'rank' => get_text('PositionShort'),
							'rankBeforeSO' => '',
							'score' => (($myRow->ElElimPhase==0 && $myRow->EvRunning==2) || ($myRow->ElElimPhase==1 && $myRow->EvRunning==3) ? get_text('ArrowAverage') : get_text('Total')),
							'completeScore' => get_text('Total'),
							'gold' => $myRow->GoldLabel,
							'xnine' => $myRow->XNineLabel,
							'tiebreak'=>get_text('TieArrows'),
							'hits' => get_text('Arrows','Tournament'),
							'so' => '',
							'ct' => ''
						);

						$section=array(
							'meta' => array(
								'event' => $curEvent,
								'session' => $myRow->ElElimPhase,
								'descr' => get_text($myRow->EvEventName, '', '', true),
								'round' => get_text($myRow->roundText),
								'roundText' => $myRow->roundText,
								'qualifiedNo' => $myRow->QualifiedNo,
								'running' => (($myRow->ElElimPhase==0 && $myRow->EvRunning==2) || ($myRow->ElElimPhase==1 && $myRow->EvRunning==3) ? 1 : 0),
								'lastUpdate'=>'0000-00-00 00:00:00',
								'fields' => $fields,
								'order' => $myRow->EvProgr,
								'version' => $myRow->DocVersion,
								'versionDate' => $myRow->DocVersionDate,
								'versionNotes' => $myRow->DocNotes,
							)
						);

						$runningOldScore=-1;
						$runningPos=0;
						$runningRank=0;
					}

					if($myRow->EvRunning)
					{
						$runningPos++;
						if($runningOldScore!=$myRow->RunningScore)
							$runningRank=$runningPos;
						$runningOldScore=$myRow->RunningScore;
					}
				// creo un elemento per la sezione
					$item=array(
						'id'  => $myRow->EnId,
						'bib' => $myRow->EnCode,
						'target' => $myRow->TargetNo,
						'athlete' => $myRow->FirstNameUpper . ' ' . $myRow->Name,
						'familyname' => $myRow->FirstName,
						'familynameUpper' => $myRow->FirstNameUpper,
						'givenname' => $myRow->Name,
						'nameOrder' => $myRow->EnNameOrder,
						'gender' => $myRow->EnSex,
						'div' => $myRow->EnDivision,
						'class' => $myRow->EnClass,
						'ageclass' => $myRow->EnAgeClass,
						'subclass' => $myRow->EnSubClass,
						'countryId' => $myRow->CoId,
						'countryCode' => $myRow->CoCode,
						'countryName' => $myRow->CoName,
						'rank' => (($myRow->ElElimPhase==0 && $myRow->EvRunning==2) || ($myRow->ElElimPhase==1 && $myRow->EvRunning==3) ? $runningRank: $myRow->ElRank),
						'rankBeforeSO' => $myRow->RankBeforeSO,
						'score' => (($myRow->ElElimPhase==0 && $myRow->EvRunning==2) || ($myRow->ElElimPhase==1 && $myRow->EvRunning==3) ? $myRow->RunningScore: $myRow->ElScore),
						'completeScore' => $myRow->ElScore,
						'gold' => $myRow->ElGold,
						'xnine' => $myRow->ElXnine,
						'tiebreak'=> $myRow->ElTiebreak,
						'hits' => $myRow->ElHits,
						'ct'=>$myRow->NumCT,
						//'so'=>$myRow->ElSO
						'so'=>$myRow->isSO
					);
				// e lo aggiungo alla sezione
					//print_r($item);
					$section['items'][]=$item;

					if ($myRow->ElDateTime>$section['meta']['lastUpdate'])
						$section['meta']['lastUpdate']=$myRow->ElDateTime;
					if ($myRow->ElDateTime>$this->data['meta']['lastUpdate'])
						$this->data['meta']['lastUpdate']=$myRow->ElDateTime;
				}
			// ultimo giro
				$this->data['sections'][$curEvent]=$section;
			}
		}
	}