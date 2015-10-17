<?php
/**
 * Obj_Rank_AbsTeam
 * Implementa l'algoritmo di default per il calcolo della rank di qualificazione assoluta a squadre
 *
 * La tabella in cui vengono scritti i valori è la Teams.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		events	=> array(<ev_1>,<ev_2>...<ev_n>) || string,			[calculate/read]
 * 		tournament => #												[calculate/read]
 * 		cutRank => #												[read]
 * 		skipExisting => #											[calculate]
 * 		components => #												[read]
 *
 * )
 *
 * con:
 * 	 events: l'array degli eventi assoluti oppure se scalare, una stringa usata in LIKE
 *	 tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *	 skipExisting: Se 1 non sovrascrive posizione e frecce di SO dove sono già valorizzati - Solo per Distanza = 0
 *   components: se impostato a false non ritorna i nomi dei componenti altrimenti sì
 *
 * $data ha la seguente forma
 *
 * array(
 * 		meta 		=> array(
 * 			title 		=> <titolo della classifica localizzato>
 * 			lastUpdate  => timestamp dell'ultima modifica (il max tra tutte le righe)
 *		),
 * 		sections 	=> array(
 * 			event_1 => array(
 * 				meta => array(
 * 					event => <event_1>, valore uguale alla chiave
 * 					descr => <descrizione evento localizzata>
 * 					printHeader => <testa stampa>
 * 					qualifiedNo => <numero di squadre qualificate per l'evento>
 * 					fields(*1) => array(
 *						id 				=> <id della squadra>
 *                      countryCode 	=> <codice nazione>
 *                      countryName 	=> <nazione>
 *                      subteam 		=> <subteam>
 						athletes 		=> array(
 *                      	name		=> <nome>
 *                      	fields 		=> array(
 * 								id    => <id della persona>
 *								bib => <matricola della persona>
 *								athlete => <cognome e nome della persona>,
 *								familyname => <cognome>
 *								givenname => <nome>
 *								div => <divisione>
 *								class => <classe>
 *								ageclass => <classe anagrafica>
 *								subclass => <subclass>
 *								quscore => <score di qualifica>
 *							)
 *                      )
 *                      rank 			=> <rank>
 *                      score 			=> <punti>
 *                      gold 			=> <ori>
 *                      xnine 			=> <xnine>
 *                      hits			=> <frecce tirate>
 *                      tiebreak		=> <frecce di tie>					(distanza 0)
 *                      ct				=> <numero di cointoss (gialli)>	(distanza 0)
 *                      so				=> <1 se shootoff (rosso)>			(distanza 0)
 * 					)
 *				)
 * 				items => array(
 * 					array(
 * 						id=><valore>,
 * 						countryCode=><valore>,
 * 						athletes=>array(
 *                      	array(id=><valore>,bib=><valore>,...,subclass=><valore>),
 *
 *                      )
 * 						...,
 * 						so=><valore>
 * 					),
 * 					...
 * 				)
 * 			)
 * 			...
 * 			event_n = ...
 * 		)
 * )
 */
	class Obj_Rank_AbsTeam extends Obj_Rank
	{
	/**
	 * safeFilter()
	 * Protegge con gli apici gli elementi di $this->opts['events'] e genera il pezzo di query per filtrare
	 *
	 * @return string: vuota se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
		protected function safeFilter()
		{
			$filter="";

			if (!empty($this->opts['events'])) {
				if (is_array($this->opts['events'])) {
					$tmp=array();
					foreach ($this->opts['events'] as $e) $tmp[]=StrSafe_DB($e);

					sort($tmp);
					$filter="AND TeEvent IN (" . implode(',',$tmp) . ")";
				}
				elseif(preg_match('/[%_]/', $this->opts['events'])) {
					$filter="AND TeEvent LIKE '" . $this->opts['events'] . "' ";
				} else {
					$filter="AND TeEvent = '" . $this->opts['events'] . "' ";
				}
			}

			if (!empty($this->opts['coid'])) {
				$filter.=" AND TeCoId=" . intval($this->opts['coid']). " " ;
			}

			if (!empty($this->opts['enid'])) {
				$filter.=" AND CONCAT(TeCoId,'|',TeSubTeam,'|',TeEvent) IN (SELECT CONCAT(TcCoId,'|',TcSubTeam,'|',TcEvent) FROM TeamComponent WHERE TcId=" . intval($this->opts['enid']). " AND TcFinEvent=1) " ;
			}

			return $filter;
		}

		public function __construct($opts)
		{
			parent::__construct($opts);
		}

	/**
	 * calculate()
	 *
	 * @Override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#calculate()
	 */
		public function calculate()
		{
			return true;
		}

	/**
	 * read()
	 *
	 * @Override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#calculate()
	 */
		public function read()
		{
			$f=$this->safeFilter();
			$filter=($f!==false ? $f : "");

			if (array_key_exists('cutRank',$this->opts) && is_numeric($this->opts['cutRank']) && $this->opts['cutRank']>0)
				$filter.= "AND Teams.teRank<={$this->opts['cutRank']} ";

			$q="
				SELECT
					TeTournament,CoId,TeSubTeam,CoCode,CoName, TeEvent,EvEventName,ToNumEnds,ToNumDist,ToMaxDistScore,FlContAssoc,
					EvMaxTeamPerson, EvProgr, EvFinalFirstPhase,
					ClDescription, DivDescription,
					EnId,EnCode,EnFirstName,upper(EnFirstName) EnFirstNameUpper,EnName,EnClass,EnDivision,EnAgeClass,EnSubClass,
					IF(EvFinalFirstPhase=48, 104, IF(EvFinalFirstPhase=24, 56, (EvFinalFirstPhase*2))) AS QualifiedNo,	EvQualPrintHead,
					SUBSTRING(QuTargetNo,1,1) AS Session, SUBSTRING(QuTargetNo,2) AS TargetNo,
					QuHits*EvMaxTeamPerson AS Arrows_Shot, QuScore, TeScore,TeRank, TeGold, TeXnine, ToGolds, ToXNine,TeHits,
					TeRank, EvRunning, IF(EvRunning=1,IFNULL(ROUND(TeScore/TeHits,3),0),0) as RunningScore,
					ABS(TeSO) AS RankBeforeSO,
					tie.Quanti,
					TeTieBreak,(TeSO>0) AS isSO,IFNULL(sqY.Quanti,1) AS `NumCT`,
					IFNULL(Td1,'.1.') as Td1, IFNULL(Td2,'.2.') as Td2, IFNULL(Td3,'.3.') as Td3, IFNULL(Td4,'.4.') as Td4, IFNULL(Td5,'.5.') as Td5, IFNULL(Td6,'.6.') as Td6, IFNULL(Td7,'.7.') as Td7, IFNULL(Td8,'.8.') as Td8,
					TeTimeStamp, DiEnds, DiArrows
				FROM
					Tournament
					INNER JOIN
						Teams
					ON ToId=TeTournament AND TeFinEvent=1
					INNER JOIN
						Countries
					ON TeCoId=CoId AND TeTournament=CoTournament
					INNER JOIN
						Events
					ON TeEvent=EvCode AND ToId=EvTournament AND EvTeamEvent=1
					INNER JOIN
						(
							SELECT
								TeEvent as tieEvent, TeFinEvent as tieFinEvent, TeTournament as tieTournament, TeScore as tieScore, Count(*) as Quanti
							FROM
								Teams
							WHERE
								TeTournament = {$this->tournament}  {$filter}
							GROUP BY
								TeEvent, TeFinEvent, TeTournament, TeScore
						) AS tie
					ON Teams.TeEvent=tie.tieEvent AND Teams.TeTournament=tie.tieTournament AND Teams.TeFinEvent=tie.tieFinEvent AND Teams.TeScore=tie.tieScore
					INNER JOIN
						TeamComponent AS tc
					ON Teams.TeCoId=tc.TcCoId AND Teams.TeSubTeam=tc.TcSubTeam AND  Teams.TeEvent=tc.TcEvent AND Teams.TeTournament=tc.TcTournament AND Teams.TeFinEvent=tc.TcFinEvent
					INNER JOIN
						Entries
					ON TcId=EnId
					INNER JOIN
						Qualifications
					ON EnId=QuId
					INNER JOIN
						Divisions
					ON EnDivision=DivId AND EnTournament=DivTournament
					INNER JOIN
						Classes
					ON EnClass=ClId AND EnTournament=ClTournament
				/* Contatori per CT (gialli)*/
					LEFT JOIN
						(
							SELECT
								TeEvent as sqyEvent,Count(*) as Quanti, TeSO as sqyRank, TeTournament as sqyTournament
							FROM
								Teams
							WHERE
								TeTournament = {$this->tournament} AND TeFinEvent=1 AND TeSO!=0 {$filter}
							GROUP BY
								TeSO, TeEvent, TeTournament
						) AS sqY
					ON sqY.sqyRank=TeSO AND sqY.sqyEvent=Teams.TeEvent AND Teams.TeFinEvent=1 AND sqY.sqyTournament=Teams.TeTournament
					LEFT JOIN
						TournamentDistances
					ON ToType=TdType AND TdTournament=ToId AND TeEvent like TdClasses
					LEFT JOIN
						Flags
						ON FlIocCode='FITA' and FlCode=CoCode and FlTournament=-1
					left join DistanceInformation on EnTournament=DiTournament and DiSession=1 and DiDistance=1 and DiType='Q'

					WHERE
					Teams.TeTournament={$this->tournament}
					{$filter}
				ORDER BY
					EvProgr,TeEvent, RunningScore DESC, TeRank ASC, TeGold DESC, TeXnine DESC, CoCode, TeSubTeam ,tc.TcOrder
			";

			$r=safe_r_sql($q);

			$this->data['meta']['title']=get_text('ResultSqAbs','Tournament');
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
			$this->data['sections']=array();

			$myEv='';
			$myTeam='';

			if (safe_num_rows($r)>0)
			{
				$section=null;


				while ($row=safe_fetch($r))
				{
					if ($myEv!=$row->TeEvent)
					{
						if ($myEv!='')
						{
							foreach($section["meta"]["arrowsShot"] as $k => $v) {
								if($v) $section["meta"]["sesArrows"][$k] = get_text('AfterXArrows', 'Common', $v);
							}
							$this->data['sections'][$myEv]=$section;
							$section=null;

						}

						$myEv=$row->TeEvent;

						$fields=array(
							'id' 			=> 'Id',
							'countryCode' 	=> get_text('CountryCode'),
							'countryName' 	=> get_text('Country'),
							'subteam' 		=> get_text('PartialTeam'),
							'athletes' 		=> array(
								'name' => get_text('Athletes'),
								'fields'=>array(
									'id'  => 'Id',
									'bib' => get_text('Code','Tournament'),
									'session' => get_text('Session'),
									'target' => get_text('Target'),
									'athlete' => get_text('Athlete'),
									'familyname' => get_text('FamilyName', 'Tournament'),
									'givenname' => get_text('Name', 'Tournament'),
									'div' => get_text('Division'),
									'class' => get_text('Cl'),
									'ageclass' => get_text('AgeCl'),
									'subclass' => get_text('SubCl','Tournament'),
									'quscore' => get_text('TotaleScore')
								)
							),
							'rank'			=> get_text('PositionShort'),
							'rankBeforeSO'	=> '',
							'score' 		=> ($row->EvRunning==1 ? get_text('ArrowAverage') : get_text('TotaleScore')),
							'gold' 			=> $row->ToGolds,
							'xnine' 		=> $row->ToXNine,
							'hits'			=> get_text('Arrows','Tournament'),
							'tiebreak' 		=> get_text('TieArrows'),
							'tie' 			=> get_text('Tie'),
							'ct' 			=> get_text('CoinTossShort','Tournament'),
							'so' 			=> get_text('ShotOffShort','Tournament')
						);

						$distFields=array();
						$distValid=$row->ToNumDist;
						foreach(range(1,8) as $n)
						{
							$distFields['dist_' . $n]=$row->{'Td' . $n};
							if($distFields['dist_' . $n]=='-')
								$distValid--;
						}

						$section=array(
							'meta' => array(
								'event' => $myEv,
								'firstPhase' => $row->EvFinalFirstPhase,
								'descr' => get_text($row->EvEventName,'','',true),
								'qualifiedNo'=>$row->QualifiedNo,
								'printHeader'=>$row->EvQualPrintHead,
								'order' => $row->EvProgr,
								'numDist' => $distValid,
								'maxScore' => $row->ToMaxDistScore*$row->EvMaxTeamPerson,
								'maxArrows' => ($row->DiEnds ? $row->DiEnds*$row->DiArrows : $row->ToNumEnds*3)*$row->EvMaxTeamPerson,
								'arrowsShot'=> array(),
								'sesArrows'=> array(),
								'running' => ($row->EvRunning==1 ? 1:0),
								'fields'=>$fields
								),
							'items' => array(),
						);
					}

					if ($myTeam!=$row->CoId . $row->TeSubTeam . $row->TeEvent)
					{
						$tmpArr=array();
						for($countArr=0; $countArr<strlen(trim($row->TeTieBreak)); $countArr = $countArr+$row->EvMaxTeamPerson)
							$tmpArr[]= ValutaArrowString(substr(trim($row->TeTieBreak),$countArr,$row->EvMaxTeamPerson)) . ",";
						$item=array(
							'id' 			=> $row->CoId,
							'countryCode' 	=> $row->CoCode,
							'contAssoc' 	=> $row->FlContAssoc,
							'countryName' 	=> $row->CoName,
							'subteam' 		=> $row->TeSubTeam,
							'athletes'		=> array(),
							'rank'			=> $row->TeRank,
							'rankBeforeSO'	=> $row->RankBeforeSO,
							'score' 		=> ($row->EvRunning==1 ? $row->RunningScore : $row->TeScore),
							'gold' 			=> $row->TeGold,
							'xnine' 		=> $row->TeXnine,
							'hits'			=> $row->TeHits,
							'recordGap'		=> ($row->Arrows_Shot*10)-$row->TeScore,
							'tiebreak'		=> $row->TeTieBreak,
							'tiebreakDecoded'=> $row->TeTieBreak ? 'T.'.implode(',', $tmpArr): '',
 		                    'ct'			=> $row->NumCT,
							'tie'			=> ($row->Quanti>1),
        	                'so'			=> $row->isSO
						);

						//Gestisco il numero di frecce tirate per sessione
						if(empty($section["meta"]["arrowsShot"][$row->Session]) || $section["meta"]["arrowsShot"][$row->Session]<=$row->Arrows_Shot)
							$section["meta"]["arrowsShot"][$row->Session] = $row->Arrows_Shot;

						$section['items'][]=$item;

						if ($row->TeTimeStamp>$this->data['meta']['lastUpdate'])
							$this->data['meta']['lastUpdate']=$row->TeTimeStamp;


						$myTeam=$row->CoId . $row->TeSubTeam . $row->TeEvent;
					}

					if (!array_key_exists('components',$this->opts) || $this->opts['components'])
					{
						$athlete=array(
							'id' => $row->EnId,
							'bib' => $row->EnCode,
							'session' => $row->Session,
							'target' => $row->TargetNo,
							'athlete'=>$row->EnFirstNameUpper . ' ' . $row->EnName,
							'familyname' => $row->EnFirstName,
							'familynameUpper' => $row->EnFirstNameUpper,
							'givenname' => $row->EnName,
							'div' => $row->EnDivision,
							'class' => $row->EnClass,
							'ageclass' => $row->EnAgeClass,
							'subclass' => $row->EnSubClass,
							'quscore' => $row->QuScore,
						);
						$section['items'][count($section['items'])-1]['athletes'][]=$athlete;
					}
				}

				foreach($section["meta"]["arrowsShot"] as $k => $v) {
					if($v) $section["meta"]["sesArrows"][$k] = str_replace("<br/>"," ",get_text('AfterXArrows', 'Common', $v));
				}

			// ultimo giro
				$this->data['sections'][$myEv]=$section;
			}
		}
	}