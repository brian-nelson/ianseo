<?php
require_once('Common/Lib/ArrTargets.inc.php');
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
	class Obj_Rank_AbsTeam_3_SetFRChampsD1DNAP extends Obj_Rank
	{
		var $AllInOne=0;

		/**
	 * safeFilter()
	 * Protegge con gli apici gli elementi di $this->opts['events'] e genera il pezzo di query per filtrare
	 *
	 * @return string: vuota se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
		protected function safeFilter()
		{
			$filter="";

			if(!empty($this->opts['session'])) {
				// need to check which team events are being shot in those sessions...
				$QuFilter='';
				if(is_array($this->opts['session'])) {
					$QuFilter .= " QuSession in (".implode(', ', $this->opts['session']).") ";
				} else {
					if($ses=intval($this->opts['session'])) {
						$QuFilter .= " QuSession=$ses ";
					}
				}
				if($QuFilter) {
					if(empty($this->opts['events'])) {
						$this->opts['events']=array();
					} elseif(!is_array($this->opts['events'])) {
						$this->opts['events']=array($this->opts['events']);
					}
					$t=safe_r_sql("select distinct EvCode 
						from Events
						inner join TeamComponent on TcEvent=EvCode and TcTournament=EvTournament and EvTeamEvent=1
						inner join Qualifications on QuId=TcId and $QuFilter
						where EvTournament={$this->tournament}");
					while($u=safe_fetch($t)) {
						$this->opts['events'][]=$u->EvCode;
					}
				}
			}

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
				$filter.=" AND (TeCoId, TeSubTeam, TeEvent) IN (SELECT TcCoId, TcSubTeam, TcEvent FROM TeamComponent WHERE TcId=" . intval($this->opts['enid']). " AND TcFinEvent=1) " ;
			}

			return $filter;
		}

		public function __construct($opts)
		{
			parent::__construct($opts);
			$this->AllInOne=getModuleParameter('FFTA', 'D1AllInOne', 0);
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

			$TeamFilter='';
			if (array_key_exists('cutRank',$this->opts)) {
				if(is_numeric($this->opts['cutRank']) && $this->opts['cutRank']>0) {
					$TeamFilter.= " AND Teams.teRank<={$this->opts['cutRank']} ";
				} elseif (strtolower($this->opts['cutRank'])=='cut') {
					$TeamFilter.= " AND Teams.teRank<=EvNumQualified ";
				}
			}


			$q="
				SELECT
					TeTournament,CoId,TeSubTeam,CoCode,CoName, CoCaCode, CoMaCode, TeEvent,EvEventName,ToNumEnds,ToNumDist,ToMaxDistScore,FlContAssoc,
					EvMaxTeamPerson, EvProgr, EvFinalFirstPhase,EvOdfCode, QuConfirm, EvMixedTeam, 
					ClDescription, DivDescription,
					EnId,EnCode,ifnull(EdExtra,EnCode) as LocalBib, EnSex,EnNameOrder,EnFirstName,upper(EnFirstName) EnFirstNameUpper,EnName,EnClass,EnDivision,EnAgeClass,EnSubClass,EnCoCode,EnDob,
					EvNumQualified AS QualifiedNo, EvFirstQualified, EvQualPrintHead,
					SUBSTRING(QuTargetNo,1,1) AS Session, SUBSTRING(QuTargetNo,2) AS TargetNo,
					TeHits AS Arrows_Shot, QuScore, QuGold, QuXnine, TeScore,TeRank, TeGold, TeXnine, ToGolds, ToXNine,TeHits,
				    concat(rtrim(QuD1Arrowstring),rtrim(QuD2Arrowstring),rtrim(QuD3Arrowstring),rtrim(QuD4Arrowstring),rtrim(QuD5Arrowstring),rtrim(QuD6Arrowstring),rtrim(QuD7Arrowstring),rtrim(QuD8Arrowstring)) as DetailedArrows,
					TeRank, EvRunning, IF(EvRunning=1,IFNULL(ROUND(TeScore/TeHits,3),0),0) as RunningScore,
					ABS(TeSO) AS RankBeforeSO,
					tie.Quanti,
					TeTieBreak, TeTbClosest, TeTbDecoded, (TeSO>0) AS isSO,IFNULL(sqY.Quanti,1) AS `NumCT`,
					IFNULL(Td1,'.1.') as Td1, IFNULL(Td2,'.2.') as Td2, IFNULL(Td3,'.3.') as Td3, IFNULL(Td4,'.4.') as Td4, IFNULL(Td5,'.5.') as Td5, IFNULL(Td6,'.6.') as Td6, IFNULL(Td7,'.7.') as Td7, IFNULL(Td8,'.8.') as Td8,
					TeTimeStamp, DiEnds, DiArrows,
					ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
					date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
					ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes, TeNotes, (EvShootOff OR EvE1ShootOff OR EvE2ShootOff) as ShootOffSolved,
				    TeIrmType, IrmType, IrmShowRank, QuIrmType,
					TeRecordBitmap as RecBitLevel, EvIsPara, CoMaCode, CoCaCode
				FROM
					Tournament
					INNER JOIN Teams ON ToId=TeTournament AND TeFinEvent=1
				    inner join IrmTypes on IrmId=TeIrmType
					INNER JOIN Countries ON TeCoId=CoId AND TeTournament=CoTournament
					INNER JOIN Events ON TeEvent=EvCode AND ToId=EvTournament AND EvTeamEvent=1
					INNER JOIN (
							SELECT TeEvent as tieEvent, TeFinEvent as tieFinEvent, TeTournament as tieTournament, TeScore as tieScore, Count(*) as Quanti
							FROM Teams
							WHERE TeTournament = {$this->tournament}  {$filter}
							GROUP BY TeEvent, TeFinEvent, TeTournament, TeScore
						) AS tie ON Teams.TeEvent=tie.tieEvent AND Teams.TeTournament=tie.tieTournament AND Teams.TeFinEvent=tie.tieFinEvent AND Teams.TeScore=tie.tieScore
					INNER JOIN TeamComponent AS tc ON Teams.TeCoId=tc.TcCoId AND Teams.TeSubTeam=tc.TcSubTeam AND  Teams.TeEvent=tc.TcEvent AND Teams.TeTournament=tc.TcTournament AND Teams.TeFinEvent=tc.TcFinEvent
					INNER JOIN (select Entries.*, CoCode as EnCoCode from Entries inner join Countries on CoId=EnCountry where EnTournament=$this->tournament) Entries ON TcId=EnId
					INNER JOIN Qualifications ON EnId=QuId
					INNER JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament
					INNER JOIN Classes ON EnClass=ClId AND EnTournament=ClTournament
				    left join ExtraData on EdId=EnId and EdType='Z'
				/* Contatori per CT (gialli)*/
					LEFT JOIN (
						SELECT TeEvent as sqyEvent,Count(*) as Quanti, TeSO as sqyRank, TeTournament as sqyTournament
						FROM Teams
						inner join IrmTypes on IrmId=TeIrmType and IrmShowRank=1
						WHERE TeTournament = {$this->tournament} AND TeFinEvent=1 AND TeSO!=0 {$filter}
						GROUP BY TeTournament, TeFinEvent, TeEvent, TeSO
						) AS sqY
					ON sqY.sqyRank=TeSO AND sqY.sqyEvent=Teams.TeEvent AND Teams.TeFinEvent=1 AND sqY.sqyTournament=Teams.TeTournament
					LEFT JOIN
						TournamentDistances
					ON ToType=TdType AND TdTournament=ToId AND TeEvent like TdClasses
					LEFT JOIN
						Flags
						ON FlIocCode='FITA' and FlCode=CoCode and FlTournament=ToId
					left join DistanceInformation on EnTournament=DiTournament and DiSession=1 and DiDistance=1 and DiType='Q'
					LEFT JOIN DocumentVersions DV1 on EvTournament=DV1.DvTournament AND DV1.DvFile = 'QUAL-TEAM' and DV1.DvEvent=''
					LEFT JOIN DocumentVersions DV2 on EvTournament=DV2.DvTournament AND DV2.DvFile = 'QUAL-TEAM' and DV2.DvEvent=EvCode


					WHERE
					Teams.TeTournament={$this->tournament}
					{$filter}
					{$TeamFilter}
				ORDER BY
					EvProgr,TeEvent, RunningScore DESC, if(IrmShowRank=1, 0, TeIrmType), TeRank ASC, TeGold DESC, TeXnine DESC, CoCode, TeSubTeam, EnSex desc, EnFirstName, tc.TcOrder
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
							'session'		=> get_text('Session'),
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
									'gender' => get_text('Sex', 'Tournament'),
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
							'completeScore' => get_text('TotalShort','Tournament'),
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
						$ConfirmStatus=pow(2, $distValid+2)-2;

						$section=array(
							'meta' => array(
								'event' => $myEv,
								'odfcode' => $row->EvOdfCode,
								'firstPhase' => $row->EvFinalFirstPhase,
								'descr' => get_text($row->EvEventName,'','',true),
								'qualifiedNo'=>$row->QualifiedNo,
                                'firstQualified' => $row->EvFirstQualified,
								'printHeader'=>$row->EvQualPrintHead,
								'order' => $row->EvProgr,
								'numDist' => $distValid,
								'maxPersons' => $row->EvMaxTeamPerson,
								'maxScore' => $row->ToMaxDistScore*$row->EvMaxTeamPerson*$distValid,
								'maxArrows' => ($row->DiEnds ? $row->DiEnds*$row->DiArrows : $row->ToNumEnds*3)*$row->EvMaxTeamPerson*$distValid,
								'arrowsShot'=> array(),
								'sesArrows'=> array(),
								'running' => ($row->EvRunning==1 ? 1:0),
                                'finished' => ($row->ShootOffSolved==1 ? 1:0),
								'fields'=>$fields,
								'version' => $row->DocVersion,
								'versionDate' => $row->DocVersionDate,
								'versionNotes' => $row->DocNotes,
								'lastUpdate' => '0000-00-00 00:00:00',
								'hasShootOff' => '',
								),
							'records' => array(),
							'items' => array(),
						);
						if(!empty($this->opts['records'])) {
							$section['records'] = $this->getRecords($row->TeEvent, true);
						}
					}

					if ($myTeam!=$row->CoId . $row->TeSubTeam . $row->TeEvent)
					{
						$tmpArr=array();
						if(trim($row->TeTieBreak)) {
							for($countArr=0; $countArr<strlen(trim($row->TeTieBreak)); $countArr = $countArr+$row->EvMaxTeamPerson) {
								$tmpArr[] = ValutaArrowString(substr(trim($row->TeTieBreak),$countArr,$row->EvMaxTeamPerson)) ;
							}
							$section['meta']['hasShootOff']=max($section['meta']['hasShootOff'], ceil($countArr/$row->EvMaxTeamPerson));
						}

						if($row->TeRank==127) {
                            $tmpRank = 'DSQ';
                        } else if ($row->TeRank==126) {
                            $tmpRank = 'DNS';
                        } else {
                            $tmpRank= $row->TeRank;
                        }

						$item=array(
							'id' 			=> $row->CoId,
							'countryCode' 	=> $row->CoCode,
							'contAssoc'     => $row->CoCaCode,
							'memberAssoc'   => $row->CoMaCode,
							'countryName' 	=> $row->CoName,
							'subteam' 		=> $row->TeSubTeam,
							'athletes'		=> array(),
							'rank'			=> $row->IrmShowRank ? $tmpRank : '',
							'rankBeforeSO'	=> $row->RankBeforeSO,
							'score' 		=> $row->IrmShowRank ? ($row->EvRunning==1 ? $row->RunningScore : $row->TeScore) : $row->IrmType,
							'completeScore' => $row->TeScore,
							'gold' 			=> $row->TeGold,
							'xnine' 		=> $row->TeXnine,
							'hits'			=> $row->TeHits,
							'notes'			=> $row->TeNotes,
							'recordGap'		=> ($row->Arrows_Shot*10)-$row->TeScore,
							'tiebreak'		=> trim($row->TeTieBreak),
                            'tiebreakClosest'=> $row->TeTbClosest,
							'tiebreakDecoded'=> $row->TeTbDecoded,
 		                    'ct'			=> $row->NumCT,
							'tie'			=> ($row->Quanti>1),
        	                'so'			=> $row->isSO,
        	                'detailedArrows'=> '',
        	                'scoreToConfirm'=> $row->EvMaxTeamPerson,
        	                'scoreConfirmed'=> 0,
							'record'        => $this->ManageBitRecord($row->RecBitLevel, $row->CoCaCode, $row->CoMaCode, $row->EvIsPara),
        	                'irm'           => $row->TeIrmType,
        	                'irmText'       => $row->IrmType,
						);

						//Gestisco il numero di frecce tirate per sessione
						if(empty($section["meta"]["arrowsShot"][$row->Session]) || $section["meta"]["arrowsShot"][$row->Session]<=$row->TeHits)
							$section["meta"]["arrowsShot"][$row->Session] = $row->TeHits;

						$section['items'][]=$item;

						if ($row->TeTimeStamp>$this->data['meta']['lastUpdate']) {
							$this->data['meta']['lastUpdate']=$row->TeTimeStamp;
						}
						if ($row->TeTimeStamp>$section['meta']['lastUpdate']) {
							$section['meta']['lastUpdate']=$row->TeTimeStamp;
						}



						$myTeam=$row->CoId . $row->TeSubTeam . $row->TeEvent;
					}

					if (!array_key_exists('components',$this->opts) || $this->opts['components'])
					{
						$athlete=array(
							'id' => $row->EnId,
							'bib' => $row->EnCode,
							'localbib' => $row->LocalBib,
							'birthdate' => $row->EnDob,
							'countryCode' => $row->EnCoCode,
							'session' => $row->Session,
							'target' => $row->TargetNo,
							'athlete'=>$row->EnFirstNameUpper . ' ' . $row->EnName,
							'familyname' => $row->EnFirstName,
							'familynameUpper' => $row->EnFirstNameUpper,
							'givenname' => $row->EnName,
							'nameOrder' => $row->EnNameOrder,
							'gender' => $row->EnSex,
							'div' => $row->EnDivision,
							'class' => $row->EnClass,
							'ageclass' => $row->EnAgeClass,
							'subclass' => $row->EnSubClass,
							'quscore' => $row->QuIrmType ? $row->IrmType : $row->QuScore,
							'qugolds' => $row->QuIrmType ? '' : $row->QuGold,
							'quxnine' => $row->QuIrmType ? '' : $row->QuXnine,
							'scoreConfirmed' => $row->QuConfirm==$ConfirmStatus
						);
						$section['items'][count($section['items'])-1]['athletes'][]=$athlete;
						if(!empty($this->opts['detailedArrowstring'])) {
							$section['items'][count($section['items'])-1]['detailedArrows'].=$row->DetailedArrows;
						}
					}
					$section['items'][count($section['items'])-1]['scoreToConfirm']--;
					if($section['items'][count($section['items'])-1]['scoreToConfirm']==0) {
						$section['items'][count($section['items'])-1]['scoreConfirmed']=1;
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
