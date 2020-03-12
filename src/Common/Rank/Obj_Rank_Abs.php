<?php
	require_once('Common/Lib/ArrTargets.inc.php');
/**
 * Obj_Rank_Abs
 * Implementa l'algoritmo di default per il calcolo della rank di qualificazione assoluta individuale
 *
 * La tabella in cui vengono scritti i valori è la Individuals.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		events	=> array(<ev_1>,<ev_2>...<ev_n>) || string,			[calculate/read]
 * 		dist	=> #												[calculate/read]
 * 		runningDist	=> #											[read]
 * 		tournament => #												[calculate/read]
 * 		cutRank => #												[read]
 * 		session => #												[read,non influisce su calculate]
 * 		comparedTo => #												[read]
 * 		skipExisting => #											[calculate]
 * )
 *
 * con:
 * 	 events: l'array degli eventi assoluti oppure se scalare, una stringa usata in LIKE
 * 	 dist: la distanza con 0 per indicare la rank assoluta totale totale.
 * 	 runningDist: Restituisce la classifica dopo "X" distanze a non della distanza "x" (e rimuove le impostazioni di "dist" se presenti)
 *	 tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *	 session: Se impostato ritorna la classifica di quella sessione, con la rank globale. Chi chiama se vuole ricalcolerà la rank in quella sessione
 *	 skipExisting: Se 1 non sovrascrive posizione e frecce di SO dove sono già valorizzati - Solo per Distanza = 0
 *
 * $data ha la seguente forma
 *
 * array(
 * 		meta 		=> array(
 * 			title 	=> <titolo della classifica localizzato>
 * 			numDist	=> <numero distanze>, inizializzato solo se c'è almeno una sezione
 * 			double	=> <1 se gara doppia 0 altrimenti>, inizializzato solo se c'è almeno una sezione
 * 			lastUpdate => timestamp dell'ultima modifica (il max tra tutte le righe)
 *		),
 * 		sections 	=> array(
 * 			event_1 => array(
 * 				meta => array(
 * 					event => <event_1>, valore uguale alla chiave
 * 					descr => <descrizione evento localizzata>
 * 					qualifiedNo => <numero di persone qualificate per l'evento>
 * 					printHeader => <testa stampa>
 * 					fields(*1) => array(
 *						id 				=> <id della persona>
 *                      bib 			=> <codice della persona>
 *                      session 		=> <sessione>
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
 *                      rankBeforeSO	=> <rank prima degli shootoff (ha senso sulla dist 0)>
 *                      score 			=> <punti in base alla distanza>
 *                      gold 			=> <ori in base alla distanza>
 *                      xnine 			=> <xnine in base alla distanza>
 *                      tiebreak		=> <frecce di tie>					(distanza 0)
 *                      ct				=> <numero di cointoss (gialli)>	(distanza 0)
 *                      so				=> <1 se shootoff (rosso)>			(distanza 0)
 *                      dist_1 			=> <rank|punti|ori|xnine della distanza 1>
 *                      dist_2 			=> <rank|punti|ori|xnine della distanza 2>
 *                      dist_3 			=> <rank|punti|ori|xnine della distanza 3>
 *                      dist_4 			=> <rank|punti|ori|xnine della distanza 4>
 *                      dist_5 			=> <rank|punti|ori|xnine della distanza 5>
 *                      dist_6 			=> <rank|punti|ori|xnine della distanza 6>
 *                      dist_7	 		=> <rank|punti|ori|xnine della distanza 7>
 *                      dist_8 			=> <rank|punti|ori|xnine della distanza 8>
 *                      hits			=> <frecce tirate (tutte se la distanza è zero oppure solo quelle della distanza passata)>
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
 *
 * Estende Obj_Rank
 */
	class Obj_Rank_Abs extends Obj_Rank
	{
	/**
	 * safeFilter()
	 * Protegge con gli apici gli elementi di $this->opts['events']
	 *
	 * @return mixed: false se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
		protected function safeFilter()
		{
			$ret=array();

			if (array_key_exists('events',$this->opts)) {
				if (is_array($this->opts['events']) && count($this->opts['events'])>0) {
					$f=array();

					foreach ($this->opts['events'] as $e) {
						$f[]=StrSafe_DB($e);
					}

					$ret[]="EvCode IN(" . implode(',',$f) . ")";
				} elseif (gettype($this->opts['events'])=='string' && trim($this->opts['events'])!='') {
					$ret[]="EvCode LIKE '" . $this->opts['events'] . "'";
				}
			}

			if($ret) return " AND " . implode(' AND ', $ret);
			return false;

		}

		public function __construct($opts)
		{
			parent::__construct($opts);
		}

	/**
	 * calculate().
	 * La classifica abs viene calcolata quando si calcola quella di classe e l'evento
	 * prevede la div/cl della persona coinvolta
	 * e quando si fanno gli spareggi per passare alle eliminatorie o alle finali.
	 * Nel primo caso questo è il metodo da chiamare perchè calcolerà l'IndRank o l'IndD[1-8]Rank lavorando su tutto l'evento
	 * (utilizza setRow()) altrimenti occorre usare setRow() direttamente.
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

	/**
	 * read()
	 *
	 * @override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#read()
	 */
		public function read(){
			$ConfirmStatus=0;
			$dd='';
			if(!empty($this->opts['runningDist']) && $this->opts['runningDist']>0) {
				$this->opts['dist'] = 0;
			}
			if($this->opts['dist']>0) {
				$dd = 'D' . $this->opts['dist'];
				$ConfirmStatus=pow(2,$this->opts['dist']);
			}

			$f=$this->safeFilter();

			$filter="";
			if ($f!==false)
			{
				$filter=$f;
			}

			$EnFilter  = (empty($this->opts['enid']) ? '' : " AND EnId=" . intval($this->opts['enid'])) ;
			$EnFilter .= (empty($this->opts['coid']) ? '' : " AND EnCountry=" . intval($this->opts['coid'])) ;

			if (array_key_exists('cutRank',$this->opts)) {
				if(is_numeric($this->opts['cutRank']) && $this->opts['cutRank']>0) {
					$EnFilter.= "AND Ind{$dd}Rank<={$this->opts['cutRank']} ";
				} elseif (strtolower($this->opts['cutRank'])=='cut') {
					$EnFilter.= "AND Ind{$dd}Rank<=EvNumQualified ";
				}
			}

			$comparedTo=0;
			if(!empty($this->opts["comparedTo"]) && is_numeric($this->opts["comparedTo"]))
				$comparedTo=$this->opts["comparedTo"];

			if(!empty($this->opts['session'])) {
				if(is_array($this->opts['session'])) {
					$EnFilter .= " AND QuSession in (".implode(', ', $this->opts['session']).") ";
				} else {
					if($ses=intval($this->opts['session'])) {
						$EnFilter .= " AND QuSession=$ses ";
					}
				}
			}

			$tmp=null;
			if (empty($this->opts['runningDist']) || $this->opts['runningDist']>0) {
				$tmp=array();
				foreach(range(1,(empty($this->opts['runningDist']) ? 8 : $this->opts['runningDist'])) as $n)
					$tmp[]='QuD'.$n.'Hits';
				$tmp=implode('+', $tmp);
			}
			elseif($this->opts['dist'])	{
				$tmp='QuD'.$this->opts['dist'].'Hits';
			} else {
				$tmp='QuHits';
			}

			$MyRank="Ind{$dd}Rank";

			$only4zero="";
			if ($this->opts['dist']==0 && empty($this->opts['runningDist']))
				$only4zero=",IndTiebreak,(IndSO>0) as isSO, IFNULL(sqY.Quanti,1) AS `NumCT`,ABS(IndSO) AS RankBeforeSO ";

			$q="
				SELECT
					EnId, EnCode, ifnull(EdExtra, EnCode) as LocalId, if(EnDob=0, '', EnDob) as BirthDate, EnOdfShortname, EnSex, EnNameOrder, upper(EnIocCode) EnIocCode, EnName AS Name, EnFirstName AS FirstName, upper(EnFirstName) AS FirstNameUpper, SUBSTRING(QuTargetNo,1,1) AS Session,
					SUBSTRING(QuTargetNo,2) AS TargetNo, FlContAssoc,
					EvProgr, ToNumEnds,ToNumDist,ToMaxDistScore,
					CoId, CoCode, CoName, EnClass, EnDivision,EnAgeClass,  EnSubClass,
					IFNULL(Td1,'.1.') as Td1, IFNULL(Td2,'.2.') as Td2, IFNULL(Td3,'.3.') as Td3, IFNULL(Td4,'.4.') as Td4, IFNULL(Td5,'.5.') as Td5, IFNULL(Td6,'.6.') as Td6, IFNULL(Td7,'.7.') as Td7, IFNULL(Td8,'.8.') as Td8,
					QuD1Score, IndD1Rank, QuD2Score, IndD2Rank, QuD3Score, IndD3Rank, QuD4Score, IndD4Rank,
					QuD5Score, IndD5Rank, QuD6Score, IndD6Rank, QuD7Score, IndD7Rank, QuD8Score, IndD8Rank,
					QuD1Gold, QuD2Gold, QuD3Gold, QuD4Gold, QuD5Gold, QuD6Gold, QuD7Gold, QuD8Gold,
					QuD1Xnine, QuD2Xnine, QuD3Xnine, QuD4Xnine, QuD5Xnine, QuD6Xnine, QuD7Xnine, QuD8Xnine,
					QuD1Arrowstring,QuD2Arrowstring,QuD3Arrowstring,QuD4Arrowstring,QuD5Arrowstring,QuD6Arrowstring,QuD7Arrowstring,QuD8Arrowstring,
					QuScore, QuNotes, QuConfirm, IndNotes, (EvShootOff OR EvE1ShootOff OR EvE2ShootOff) as ShootOffSolved,
					IF(EvRunning=1,IFNULL(ROUND(QuScore/QuHits,3),0),0) as RunningScore,
					EvCode,EvEventName,EvRunning, EvFinalFirstPhase, EvElim1, EvElim2,
					{$tmp} AS Arrows_Shot,
					IF(EvElim1=0 && EvElim2=0, EvNumQualified ,IF(EvElim1=0,EvElim2,EvElim1)) as QualifiedNo, EvQualPrintHead as PrintHeader,
					{$MyRank} AS Rank, " . (!empty($comparedTo) ? 'IFNULL(IopRank,0)' : '0') . " as OldRank, Qu{$dd}Score AS Score, Qu{$dd}Gold AS Gold,Qu{$dd}Xnine AS XNine, Qu{$dd}Hits AS Hits, ";

			if(!empty($this->opts['runningDist']) && $this->opts['runningDist']>0) {
				$q1='';
				$q2='';
				$q3='';
				for($i=1; $i<=$this->opts['runningDist']; $i++) {
					$q1 .= "QuD" . $i . "Score+";
					$q2 .= "QuD" . $i . "Gold+";
					$q3 .= "QuD" . $i . "XNine+";
				}
				$q .= substr($q1, 0, -1) . " AS OrderScore, ";
				$q .= substr($q2, 0, -1) . " AS OrderGold, ";
				$q .= substr($q3, 0, -1) . " AS OrderXnine, ";
			}
			else {
				$q .= "0 AS OrderScore, 0 AS OrderGold, 0 AS OrderXnine, ";
			}

			$q .= "IndTimestamp,
					ToGolds AS GoldLabel, ToXNine AS XNineLabel,
					ToDouble, DiEnds, DiArrows,
					ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
					date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
					ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes
					{$only4zero}
				FROM Tournament
				INNER JOIN Entries ON ToId=EnTournament
				INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament AND EnTournament={$this->tournament}
				INNER JOIN Qualifications ON EnId=QuId
				INNER JOIN Individuals ON IndTournament=EnTournament AND EnId=IndId
				INNER JOIN Events ON EvCode=IndEvent AND EvTeamEvent=0 AND EvTournament=EnTournament
				left join ExtraData on EdId=EnId and EdType='Z'
				LEFT JOIN DocumentVersions DV1 on EvTournament=DV1.DvTournament AND DV1.DvFile = 'QUAL-IND' and DV1.DvEvent=''
				LEFT JOIN DocumentVersions DV2 on EvTournament=DV2.DvTournament AND DV2.DvFile = 'QUAL-IND' and DV2.DvEvent=EvCode
				LEFT JOIN TournamentDistances ON ToType=TdType AND TdTournament=ToId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses
				left join DistanceInformation on EnTournament=DiTournament and DiSession=1 and DiDistance=1 and DiType='Q' ";
			if(!empty($comparedTo))
				$q .= "LEFT JOIN IndOldPositions ON IopId=EnId AND IopEvent=EvCode AND IopTournament=EnTournament AND IopHits=" . ($comparedTo>0 ? $comparedTo :  "(SELECT MAX(IopHits) FROM IndOldPositions WHERE IopId=EnId AND IopEvent=EvCode AND IopTournament=EnTournament AND IopHits!=QuHits) ") . " ";
			$q .= "LEFT JOIN Flags ON FlIocCode='FITA' and FlCode=CoCode and FlTournament=-1

					/* Contatori per CT (gialli)*/
					LEFT JOIN
						(
							SELECT
								IndEvent,Count(*) as Quanti, IndSO as sqyRank, IndTournament
							FROM
								Individuals INNER JOIN Events ON IndEvent=EvCode AND IndTournament=EvTournament AND EvTeamEvent=0
							WHERE
								IndTournament = {$this->tournament} AND IndSO!=0 {$filter}
							GROUP BY
								IndSO, IndEvent,IndTournament
						) AS sqY
					ON sqY.sqyRank=IndSO AND sqY.IndEvent=Individuals.IndEvent AND sqY.IndTournament=Individuals.IndTournament

				WHERE
					EnAthlete=1 AND EnIndFEvent=1 AND EnStatus <= 1  "
					. (empty($this->opts['includeNullPoints'])? " AND (QuScore != 0 OR IndRank != 0) " : "")
					." AND ToId = {$this->tournament}
					{$filter}
					AND (IndRank!=9999 OR IndRank!=9998 OR EvRunning=0)
					{$EnFilter}
				ORDER BY
						EvProgr, EvCode, ";
			if(!empty($this->opts['runningDist']) && $this->opts['runningDist']>0)
				$q .= "OrderScore DESC, OrderGold DESC, OrderXnine DESC, FirstName, Name ";
			else
				$q .= "RunningScore DESC, Ind{$dd}Rank ASC, FirstName, Name ";

			$r=safe_r_sql($q);

			$this->data['meta']['title']=get_text('ResultIndAbs','Tournament');
			$this->data['meta']['distance']=$this->opts['dist'];
			$this->data['meta']['numDist']=-1;
			$this->data['meta']['double']=-1;
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
			$this->data['sections']=array();

			if (safe_num_rows($r)>0) {
				$curEvent='';

				$section=null;

				$runningOldScore=-1;
				$runningPos=0;
				$runningRank=0;

				$oldScore=-1;
				$oldGold=-1;
				$oldXnine=-1;
				$myPos=0;
				$myRank=0;

				while ($myRow=safe_fetch($r))
				{
					if ($curEvent!=$myRow->EvCode)
					{
					/*
					 *  se non sono all'inizio, prima di iniziare una sezione devo prendere quella appena fatta
					 *  e accodarla alle altre
					 */
						if ($curEvent!='')
						{
							foreach($section["meta"]["arrowsShot"] as $k => $v) {
								if($v) $section["meta"]["sesArrows"][$k] = get_text('AfterXArrows', 'Common', $v);
							}
							$this->data['sections'][$curEvent]=$section;
							$section=null;
						}

					// al cambio creo una nuova sezione
						$curEvent=$myRow->EvCode;

					// inizializzo i meta che son comuni a tutta la classifica
						if ($this->data['meta']['numDist']==-1)
						{
							$this->data['meta']['numDist']=$myRow->ToNumDist;
							$this->data['meta']['double']=$myRow->ToDouble;
						}

					// qui ci sono le descrizioni dei campi
						$distFields=array();
						$distValid=$myRow->ToNumDist;
						foreach(range(1,8) as $n) {
							$distFields['dist_' . $n]=$myRow->{'Td' . $n};
							if($distFields['dist_' . $n]=='-') {
								$distValid--;
							}
						}
						if(!$dd) {
							$ConfirmStatus=pow(2, $distValid+1)-2;
						}

						$fields=array(
							'id'  => 'Id',
							'bib' => get_text('Code','Tournament'),
							'session' => get_text('Session'),
							'target' => get_text('Target'),
							'athlete' => get_text('Athlete'),
							'familyname' => get_text('FamilyName', 'Tournament'),
							'givenname' => get_text('Name', 'Tournament'),
							'gender' => get_text('Sex', 'Tournament'),
							'div' => get_text('Division'),
							'class' => get_text('Class'),
							'ageclass' => get_text('AgeCl'),
							'subclass' => get_text('SubCl','Tournament'),
							'countryId'  => 'CoId',
							'countryCode' => get_text('CountryCode'),
							'countryName' => get_text('Country'),
							'rank' => get_text('PositionShort'),
							'oldRank' => '',
							'rankBeforeSO' => '',
							'score' => ($myRow->EvRunning==1 ? get_text('ArrowAverage') : get_text('TotalShort','Tournament')),
							'completeScore' => get_text('TotalShort','Tournament'),
							'gold' => $myRow->GoldLabel,
							'xnine' => $myRow->XNineLabel,
							'hits' => get_text('Arrows','Tournament')
						);

						if ($this->opts['dist']==0 && empty($this->opts['runningDist']))
						{
							$fields=$fields+array(
								'tiebreak' => get_text('TieArrows'),
								'ct' => get_text('CoinTossShort','Tournament'),
								'so' => get_text('ShotOffShort','Tournament')
							);
						}

						$fields=$fields+$distFields;

						$section=array(
							'meta' => array(
								'event' => $curEvent,
								'firstPhase' => $myRow->EvFinalFirstPhase,
								'elimination1' => $myRow->EvElim1,
								'elimination2' => $myRow->EvElim2,
								'descr' => get_text($myRow->EvEventName,'','',true),
								'numDist' => $distValid,
								'qualifiedNo' => $myRow->QualifiedNo,
								'printHeader' => (!empty($this->opts['runningDist']) && $this->opts['runningDist']>0 ? get_text('AfterXDistance','Tournament',$this->opts['runningDist']) : ($this->opts['dist']>0 ? get_text('AtXDistance','Tournament',$this->opts['dist']): $myRow->PrintHeader)),
								'arrowsShot'=> array(),
								'maxPersons' => 1,
								'maxScore' => $myRow->ToMaxDistScore,
								'maxArrows' => ($myRow->DiEnds ? $myRow->DiEnds*$myRow->DiArrows : $myRow->ToNumEnds*3),
								'sesArrows'=> array(),
								'running' => ($myRow->EvRunning==1 ? 1:0),
								'finished' => ($myRow->ShootOffSolved ? 1:0),
								'order' => $myRow->EvProgr,
								'fields' => $fields,
								'version' => $myRow->DocVersion,
								'versionDate' => $myRow->DocVersionDate,
								'versionNotes' => $myRow->DocNotes,
								'lastUpdate' => '0000-00-00 00:00:00',
								'hasShootOff' => '',
							),
							'records' => array(),
						);
						if(!empty($this->opts['records'])) {
							$section['records'] = $this->getRecords($myRow->EvCode);
						}

						$oldScore=-1;
						$oldGold=-1;
						$oldXnine=-1;
						$myPos=0;
						$myRank=0;

						$runningOldScore=-1;
						$runningPos=0;
						$runningRank=0;
					}

					if($myRow->EvRunning==1)
					{
						$runningPos++;
						if($runningOldScore!=$myRow->RunningScore)
							$runningRank=$runningPos;
						$runningOldScore=$myRow->RunningScore;
					}

					$myPos++;
					if(!($oldScore==$myRow->OrderScore && $oldGold==$myRow->OrderGold && $oldXnine==$myRow->OrderXnine))
						$myRank = $myPos;
					$oldScore = $myRow->OrderScore;
					$oldGold = $myRow->OrderGold;
					$oldXnine = $myRow->OrderXnine;


				// creo un elemento per la sezione
					if($myRow->Rank==9999) {
                        $tmpRank = 'DSQ';
                    } else if ($myRow->Rank==9998) {
                        $tmpRank = 'DNS';
					} else {
						$tmpRank= (!empty($this->opts['runningDist']) && $this->opts['runningDist']>0 ? $myRank : ($myRow->EvRunning==1 ? $runningRank: $myRow->Rank));
					}

					$item=array(
						'id'  => $myRow->EnId,
						'bib' => $myRow->EnCode,
						'localbib' => $myRow->LocalId,
						'tvname' => $myRow->EnOdfShortname,
						'birthdate' => $myRow->BirthDate,
						'session' => $myRow->Session,
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
						'contAssoc' => $myRow->FlContAssoc,
						'countryIocCode' => $myRow->EnIocCode,
						'countryName' => $myRow->CoName,
						'rank' => $tmpRank,
						'oldRank' => $myRow->OldRank,
						'rankBeforeSO'=>(isset($myRow->RankBeforeSO) ? $myRow->RankBeforeSO:0),
						'score' => (!empty($this->opts['runningDist']) && $this->opts['runningDist']>0 ? $myRow->OrderScore : ($myRow->EvRunning==1 ? $myRow->RunningScore: $myRow->Score)),
						'completeScore' => $myRow->Score,
						'scoreConfirmed' => $myRow->QuConfirm==$ConfirmStatus,
						'gold' => (!empty($this->opts['runningDist']) && $this->opts['runningDist']>0 ? $myRow->OrderGold : $myRow->Gold),
						'xnine' => (!empty($this->opts['runningDist']) && $this->opts['runningDist']>0 ? $myRow->OrderXnine : $myRow->XNine),
						'hits' => $myRow->Hits,
						'notes' => trim($myRow->QuNotes. ' ' . $myRow->IndNotes),
						'recordGap' => ($myRow->Arrows_Shot*10)-$myRow->Score,
					);

					if ($this->opts['dist']==0 && empty($this->opts['runningDist']))
					{
						$tmpArr="";
						if(trim($myRow->IndTiebreak)) {
							$tmpArr="T.";
							for($countArr=0; $countArr<strlen(trim($myRow->IndTiebreak)); $countArr++) {
								$tmpArr .= DecodeFromLetter(substr(trim($myRow->IndTiebreak),$countArr,1)) . ",";
							}
							$tmpArr = substr($tmpArr,0,-1);
							$section['meta']['hasShootOff']=max($section['meta']['hasShootOff'], $countArr);
						}
						$item=$item+array(
							'tiebreak' => trim($myRow->IndTiebreak),
							'tiebreakDecoded' => $tmpArr,
							'ct' => $myRow->NumCT,
							'so' => $myRow->isSO
						);
					}

					$distFields=array();
					foreach(range(1,8) as $n)
					{
						if((!empty($this->opts['runningDist']) && $this->opts['runningDist']>0 && $n>$this->opts['runningDist']) || ($this->opts['dist']>0 && $n!=$this->opts['dist']))
							$distFields['dist_' . $n]='0|0|0|0';
						else
							$distFields['dist_' . $n]=$myRow->{'IndD' . $n . 'Rank'} . '|' . $myRow->{'QuD' . $n . 'Score'} . '|' . $myRow->{'QuD' . $n . 'Gold'} . '|' . $myRow->{'QuD' . $n . 'Xnine'};
						$item["D{$n}Arrowstring"]=$myRow->{"QuD{$n}Arrowstring"};
					}

					$item=$item+$distFields;

					//Gestisco il numero di frecce tirate per sessione
					if(empty($section["meta"]["arrowsShot"][$myRow->Session]) || $section["meta"]["arrowsShot"][$myRow->Session]<=$myRow->Arrows_Shot)
						$section["meta"]["arrowsShot"][$myRow->Session] = $myRow->Arrows_Shot;


				// e lo aggiungo alla sezione
					//print_r($item);
					$section['items'][]=$item;

					if ($myRow->IndTimestamp>$this->data['meta']['lastUpdate']) {
						$this->data['meta']['lastUpdate']=$myRow->IndTimestamp;
					}
					if ($myRow->IndTimestamp>$section['meta']['lastUpdate']) {
						$section['meta']['lastUpdate']=$myRow->IndTimestamp;
					}
				}

				foreach($section["meta"]["arrowsShot"] as $k => $v) {
					if($v) $section["meta"]["sesArrows"][$k] = get_text('AfterXArrows', 'Common', $v);
				}

			// ultimo giro
				$this->data['sections'][$curEvent]=$section;
			}
		}
	}
