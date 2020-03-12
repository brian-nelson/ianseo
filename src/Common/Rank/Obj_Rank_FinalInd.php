<?php
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');

/**
 * Obj_Rank_FinalInd
 *
 * Implementa l'algoritmo di default per il calcolo della rank finale individuale.
 *
 * La tabella in cui scrive è Individuals e popola la RankFinal "a pezzi". Solo alla fine della gara
 * avremo tutta la colonna valorizzata.
 *
 * A seconda della fase che sto trattando avrò porzioni di colonna da gestire differenti e calcoli differenti.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		eventsC => array(<ev_1>@<calcPhase_1>,<ev_2>@<calcPhase_2>,...,<ev_n>@<calcPhase_n>)			[calculate,non influisce su read]
 * 		eventsR => array(<ev_1>,...,<ev_n>)																[read,non influisce su calculate]
 * 		cutRank => taglio della rank position
 * 		tournament => #																					[calculate/read]
 * )
 *
 * con:
 * 	 eventsC: l'array con le coppie evento@fase di cui voglio il calcolo.
 * 	 	I valori calcPhase_n servono a calculate() per scegliere da dove prelevare i dati e come gestirli per l'evento accoppiato perchè
 * 			a seconda che ci siano o no le eliminatorie, che si stia passando dagli assoluti alle finali o da un girone
 * 			eliminatorio all'altro avrò comportamenti diversi.
 * 			I valori sono:
 * 				-3: sto arrivando dalle qualifiche
 * 				-2: sto arrivando dall'ultimo girone eliminatorio
 * 				-1: sto arrivando dal primo girone eliminatorio
 * 			 	0: oro 				--> assegna le rank 1,2
 * 			 	1: bronzo				--> assegna le rank 3,4
 * 			 	2: semifinali			--> non assegna nulla
 * 			 	4: quarti				--> assegna le rank dalla 5 all'8 con la regola setpoints=>cumulativo=>tie
 * 			 	8: ottavi				--> assegna le rank dalla 9 alla 16 mettendo tutti a 9
 * 			 	16: sedicesimi			--> assegna le rank dalla 17 alla 32 mettendo tutti a 17
 * 			 	24: ventiquattresimi 	--> assegna le rank dalla 17 alla 32 mettendo tutti a 33
 * 			 	32: trentaduesimi 		--> assegna le rank dalla 17 alla 32 mettendo tutti a 33
 * 			 	48: quarantottesimi 	--> assegna le rank dalla 34 in su mettendo tutti a 49
 * 			IMPORTANTE!
 * 				il valore di calcPhase rappresenta la fase che è appena terminata e per la quale vogliamo calcolare
 * 				la rank. Ad esempio se abbiamo risolto gli spareggio delle quelifiche verso le finali dobbiamo usare -3
 * 				per calcolare la RankFinal di quelli che non sono passati.
 *	eventsR: l'array con gli eventi per cui si vuole la classifica.
 *  tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *
 * Estende Obj_Rank
 */
	class Obj_Rank_FinalInd extends Obj_Rank
	{
	/**
	 * safeFilterR()
	 * Protegge con gli apici gli elementi di $this->opts['eventsR']
	 *
	 * @return mixed: false se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
		protected function safeFilterR()
		{
			$filter=false;

			if (array_key_exists('eventsR',$this->opts))
			{
				if (is_array($this->opts['eventsR']) && count($this->opts['eventsR'])>0)
				{
					$filter=array();

					foreach ($this->opts['eventsR'] as $e)
					{
						$filter[]=StrSafe_DB($e);
					}

					$filter="AND EvCode IN(" . implode(',',$filter) . ")";
				}
				elseif (gettype($this->opts['eventsR'])=='string' && trim($this->opts['eventsR'])!='')
				{
					$filter="AND EvCode LIKE '" . $this->opts['eventsR'] . "' ";
				}
				else
					$filter=false;
			}
			else
				$filter=false;

			return $filter;
		}

		public function __construct($opts)
		{
			parent::__construct($opts);
		}

	/**
	 * calculate()
	 *
	 * Al primo errore termina con false!
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

		public function read($StraightRank=false)
		{
			$f=$this->safeFilterR();

			$filter="";
			if ($f!==false)
			{
				$filter=$f;
			}

			if (array_key_exists('cutRank',$this->opts) && is_numeric($this->opts['cutRank']) && $this->opts['cutRank']>0)
				$filter.= "AND IF(EvShootOff+EvE1ShootOff+EvE2ShootOff=0, IndRank, IndRankFinal)<={$this->opts['cutRank']} ";

			$EnFilter  = (empty($this->opts['enid']) ? '' : " AND EnId=" . intval($this->opts['enid'])) ;
			$EnFilter .= (empty($this->opts['coid']) ? '' : " AND EnCountry=" . intval($this->opts['coid'])) ;

			$phases=null;

		/*
		 *  prima passata per costruire la struttura del vettore.
		 *  Tiro fuori le qualifiche, le posizioni finali e le eliminatorie (se ci sono)
		 */
			$q="SELECT EnId,EnCode, EnSex, EnNameOrder, upper(EnIocCode) EnIocCode, EnName AS Name, EnFirstName AS FirstName, upper(EnFirstName) AS FirstNameUpper, CoId, CoCode, CoName, if(CoNameComplete>'', CoNameComplete, CoName) as CoNameComplete,
					EvCode,EvEventName,EvProgr,EvElimType, ifnull(EdExtra, EnCode) as LocalBib, EnDob,
					EvFinalPrintHead as PrintHeader,
					EvFinalFirstPhase,	EvElim1, 	EvElim2,EvMatchMode, EvMedals, EvCodeParent, 
					IndRank as QualRank, ".($StraightRank ? "IndRankFinal" : "IF(EvShootOff+EvE1ShootOff+EvE2ShootOff=0, IndRank, IndRankFinal)")." as FinalRank, QuScore AS QualScore,
					e1.ElRank AS E1Rank,e1.ElScore AS E1Score,
					e2.ElRank AS E2Rank,e2.ElScore AS E2Score,
					IndTimestamp,IndTimestampFinal,
					ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
					date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
					ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes, EvOdfCode, EvOdfGender
				FROM Tournament
				INNER JOIN Entries ON ToId=EnTournament
				INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament AND EnTournament={$this->tournament}
				INNER JOIN Qualifications ON EnId=QuId
				INNER JOIN Individuals ON EnTournament=IndTournament AND EnId=IndId
				INNER JOIN Events ON EvCode=IndEvent AND EvTeamEvent=0 AND EvTournament=IndTournament
				LEFT JOIN DocumentVersions DV1 on EvTournament=DV1.DvTournament AND DV1.DvFile = 'R-IND' and DV1.DvEvent=''
				LEFT JOIN DocumentVersions DV2 on EvTournament=DV2.DvTournament AND DV2.DvFile = 'R-IND' and DV2.DvEvent=EvCode
				LEFT JOIN Eliminations AS e1 ON IndId=e1.ElId AND IndTournament=e1.ElTournament AND IndEvent=e1.ElEventCode AND e1.ElElimPhase=0
				LEFT JOIN Eliminations AS e2 ON IndId=e2.ElId AND IndTournament=e2.ElTournament AND IndEvent=e2.ElEventCode AND e2.ElElimPhase=1
				left join ExtraData on EdId=EnId and EdType='Z'
				WHERE
					EnAthlete=1 AND EnIndFEvent=1 AND EnStatus <= 1  AND (QuScore != 0 OR IndRankFinal != 0) AND ToId = {$this->tournament}
					{$filter}
					{$EnFilter}
				ORDER BY
						EvProgr, EvCode, IF(EvShootOff+EvE1ShootOff+EvE2ShootOff=0, IndRank, IndRankFinal) ASC, EnFirstName, EnName
			";

			//print $q;exit;
			$r=safe_r_sql($q);

			$this->data['meta']['title']=get_text('IndFinEvent','Tournament');
//			$this->data['meta']['printHeader']='';
//			$this->data['meta']['firstPhase']=-1;
//			$this->data['meta']['elim1']=-1;
//			$this->data['meta']['elim2']=-1;
//			$this->data['meta']['matchMode']=-1;
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
			$this->data['sections']=array();

			if(safe_num_rows($r)>0)
			{
				$curEvent='';
				$section=null;

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
							$this->data['sections'][$curEvent]=$section;
							$section=null;
						}

					// al cambio creo una nuova sezione
						$curEvent=$myRow->EvCode;
						$phases=getPhasesId($myRow->EvFinalFirstPhase);

					// qui ci sono le descrizioni dei campi
						$fields=array(
							'id'  => 'Id',
							'bib' => get_text('Code','Tournament'),
							//'session' => get_text('Session'),
							//'target' => get_text('Target'),
							'athlete' => get_text('Athlete'),
							'familyname' => get_text('FamilyName', 'Tournament'),
							'givenname' => get_text('Name', 'Tournament'),
							'gender' => get_text('Sex', 'Tournament'),
//							'div' => get_text('Division'),
//							'class' => get_text('Class'),
//							'ageclass' => get_text('AgeCl'),
//							'subclass' => get_text('SubCl','Tournament'),
							'countryCode' => '',
							'countryName' => get_text('Country'),
							'countryIocCode'=>'',
							'qualRank' => get_text('RankScoreShort'),
							'qualScore' => get_text('PositionShort'),
							'rank' => get_text('PositionShort'),
							'elims'=>array(
								'e1'=>get_text('Eliminations_1'),
								'e2'=>get_text('Eliminations_2'),
								'fields'=>array(
									'score'=>get_text('Total'),
									'rank'=>get_text('PositionShort'),
								)
							),
							'finals'=>array()
						);

						foreach($phases as $k => $v) {
							if($v<=valueFirstPhase($myRow->EvFinalFirstPhase)) {
								$fields['finals'][$v]=get_text(namePhase($myRow->EvFinalFirstPhase,$v)  . "_Phase");
							}
						}

						$fields['finals']['fields']=array(
							'score'=>get_text('TotalShort','Tournament'),
							'setScore'=>get_text('SetTotal','Tournament'),
						 	'setPoints'=>get_text('SetPoints','Tournament'),
							'tie'=>'S.O.',
							'arrowstring'=>get_text('Arrows','Tournament'),
						 	'tiebreak'=>get_text('TieArrows')
						);



						$section=array(
							'meta' => array(
								'event' => $curEvent,
								'odfEvent' => $myRow->EvOdfCode,
								'odfGender' => $myRow->EvOdfGender,
								'descr' => get_text($myRow->EvEventName,'','',true),
								'printHeader'=>get_text($myRow->PrintHeader,'','',true),
								'firstPhase'=>$myRow->EvFinalFirstPhase,
								'elimType'=>$myRow->EvElimType,
								'elim1'=>($myRow->EvElim1!=0),
								'elim2'=>($myRow->EvElim2!=0),
                                'parent'=>$myRow->EvCodeParent,
								'matchMode'=>$myRow->EvMatchMode,
								'order'=>$myRow->EvProgr,
								'lastUpdate'=>'0000-00-00 00:00:00',
								'fields' => $fields,
								'medals' => $myRow->EvMedals,
								'version' => $myRow->DocVersion,
								'versionDate' => $myRow->DocVersionDate,
								'versionNotes' => $myRow->DocNotes
								),
							'items'=>array()
						);

					}

					$item=array(
						'id'  => $myRow->EnId,
						'bib' => $myRow->EnCode,
						'localBib' => $myRow->LocalBib,
						'athlete' => $myRow->FirstNameUpper . ' ' . $myRow->Name,
						'familyname' => $myRow->FirstName,
						'familynameUpper' => $myRow->FirstNameUpper,
						'givenname' => $myRow->Name,
						'nameOrder' => $myRow->EnNameOrder,
						'gender' => $myRow->EnSex,
						'birthdate' => $myRow->EnDob,
//						'div' => $myRow->EnDivision,
//						'class' => $myRow->EnClass,
//						'ageclass' => $myRow->EnAgeClass,
//						'subclass' => $myRow->EnSubClass,
						'countryId' => $myRow->CoId,
						'countryCode' => $myRow->CoCode,
						'countryIocCode' => $myRow->EnIocCode,
						'countryName' => $myRow->CoName,
						'countryNameLong' => $myRow->CoNameComplete,
						'qualScore'=>$myRow->QualScore,
						'qualRank'=>($myRow->QualRank == 9999 ? 'DSQ' : ($myRow->QualRank == 9998 ? 'DNS' : $myRow->QualRank)),
						'rank'=>($myRow->FinalRank == 9999 ? 'DSQ' : ($myRow->FinalRank == 9998 ? 'DNS' : $myRow->FinalRank)),
						'preseed'=>(($Saved=SavedInPhase($myRow->EvFinalFirstPhase)) and $myRow->QualRank<=$Saved) ? '1' : '',
						'elims'=>array(),
						'finals'=>array()
					);

					if ($myRow->E1Rank!==null && $myRow->E1Score!==null)
					{
						$item['elims']['e1']['score']=$myRow->E1Score;
						$item['elims']['e1']['rank']=$myRow->E1Rank;
					}

					if ($myRow->E2Rank!==null && $myRow->E2Score!==null)
					{
						$item['elims']['e2']['score']=$myRow->E2Score;
						$item['elims']['e2']['rank']=$myRow->E2Rank;
					}

					$section['items'][$myRow->EnId]=$item;

					if ($myRow->IndTimestampFinal>$section['meta']['lastUpdate'])
						$section['meta']['lastUpdate']=$myRow->IndTimestampFinal;
					if ($myRow->IndTimestampFinal>$this->data['meta']['lastUpdate'])
						$this->data['meta']['lastUpdate']=$myRow->IndTimestampFinal;

				}
			// ultimo giro
				$this->data['sections'][$curEvent]=$section;
			}

		/*
		 * A questo punto ho i nomi, le qualifiche,le eliminatorie (se ci sono)
		 * e punti+rank delle precedenti.
		 * Mi mancano le finali. Che prendo SOLO se lo shootoff è risolto
		 *
		 */

			$q="(
				SELECT
					EvElimType, f1.FinEvent AS `event`,f1.FinAthlete AS `athlete`,f1.FinMatchNo AS `matchNo`,f1.FinScore AS `score`,f1.FinSetScore AS `setScore`,f1.FinSetPoints AS `setPoints`,f1.FinSetPointsByEnd AS `setPointsByEnd`,f1.FinTie AS `tie`,f1.FinArrowstring AS `arrowstring`,f1.FinTiebreak AS `tiebreak`, f1.FinNotes as Notes,
					f2.FinAthlete AS `oppAthlete`,f2.FinMatchNo AS `oppMatchNo`,f2.FinScore AS `oppScore`,f2.FinSetScore AS `oppSetScore`,f2.FinSetPoints AS `oppSetPoints`,f2.FinSetPointsByEnd AS `oppSetPointsByEnd`,f2.FinTie AS `oppTie`,f2.FinArrowstring AS `oppArrowstring`,f2.FinTiebreak AS `oppTiebreak`, f2.FinNotes as OppNotes,
					GrPhase, EvProgr, IndRankFinal
					FROM Finals AS f1
					INNER JOIN Events
						ON EvTournament=f1.FinTournament AND EvCode=f1.FinEvent AND EvTeamEvent=0 AND EvShootOff=1
					INNER JOIN Finals AS f2
						ON f2.FinEvent=f1.FinEvent AND f2.FinTournament=f1.FinTournament AND f2.FinMatchNo=f1.FinMatchNo+1
					INNER JOIN Grids
						ON GrMatchNo=f1.FinMatchNo and if(EvElimType=3, GrPhase<=EvFinalFirstPhase, true)
					INNER JOIN Individuals
						ON IndTournament={$this->tournament} AND IndEvent=f1.FinEvent AND IndId=f1.FinAthlete
					WHERE
						f1.FinTournament={$this->tournament} and f1.FinMatchNo%2=0
						{$filter}
				) union (
				SELECT
					EvElimType, f1.FinEvent AS `event`,f1.FinAthlete AS `athlete`,f1.FinMatchNo AS `matchNo`,f1.FinScore AS `score`,f1.FinSetScore AS `setScore`,f1.FinSetPoints AS `setPoints`,f1.FinSetPointsByEnd AS `setPointsByEnd`,f1.FinTie AS `tie`,f1.FinArrowstring AS `arrowstring`,f1.FinTiebreak AS `tiebreak`, f1.FinNotes as Notes,
					f2.FinAthlete AS `oppAthlete`,f2.FinMatchNo AS `oppMatchNo`,f2.FinScore AS `oppScore`,f2.FinSetScore AS `oppSetScore`,f2.FinSetPoints AS `oppSetPoints`,f2.FinSetPointsByEnd AS `oppSetPointsByEnd`,f2.FinTie AS `oppTie`,f2.FinArrowstring AS `oppArrowstring`,f2.FinTiebreak AS `oppTiebreak`, f2.FinNotes as OppNotes,
					GrPhase, EvProgr, IndRankFinal
					FROM Finals AS f1
					INNER JOIN Events
						ON EvTournament=f1.FinTournament AND EvCode=f1.FinEvent AND EvTeamEvent=0 AND EvShootOff=1
					INNER JOIN Finals AS f2
						ON f2.FinEvent=f1.FinEvent AND f2.FinTournament=f1.FinTournament AND f2.FinMatchNo=f1.FinMatchNo-1
					INNER JOIN Grids
						ON GrMatchNo=f1.FinMatchNo and if(EvElimType=3, GrPhase<=EvFinalFirstPhase, true)
					INNER JOIN Individuals
						ON IndTournament={$this->tournament} AND IndEvent=f1.FinEvent AND IndId=f1.FinAthlete
					WHERE
						f1.FinTournament={$this->tournament} and f1.FinMatchNo%2=1
						{$filter}
  				)
  				ORDER BY
  					EvProgr ASC, IndRankFinal ASC, GrPhase DESC
			";
			//print $q;exit;
			//return;
			$rr=safe_r_sql($q);
			if (safe_num_rows($rr)>0)
			{
				while ($row=safe_fetch($rr))
				{
					$arrowstring=array();
					for ($i=0;$i<strlen($row->arrowstring);++$i)
					{
						if (trim($row->arrowstring[$i])!='')
						{
							$arrowstring[]=DecodeFromLetter($row->arrowstring[$i]);
						}
					}

					$tiebreak=array();
					for ($i=0;$i<strlen($row->tiebreak);++$i)
					{
						if (trim($row->tiebreak[$i])!='')
						{
							$tiebreak[]=DecodeFromLetter($row->tiebreak[$i]);
						}
					}

					$oppArrowstring=array();
					for ($i=0;$i<strlen($row->oppArrowstring);++$i)
					{
						if (trim($row->oppArrowstring[$i])!='')
						{
							$oppArrowstring[]=DecodeFromLetter($row->oppArrowstring[$i]);
						}
					}

					$oppTiebreak=array();
					for ($i=0;$i<strlen($row->oppTiebreak);++$i)
					{
						if (trim($row->oppTiebreak[$i])!='')
						{
							$oppTiebreak[]=DecodeFromLetter($row->oppTiebreak[$i]);
						}
					}

					if(!empty($this->data['sections'][$row->event]['items'][$row->athlete])) {
						if($row->GrPhase > $this->data['sections'][$row->event]['meta']['firstPhase']) {
							$phases=getPhasesId($row->GrPhase);
							$PhPools=getPoolMatchesHeadersWA();
							$Finals=array();
							foreach($phases as $k => $v) {
								if($v<=valueFirstPhase($row->GrPhase)) {
									if($row->EvElimType==4 and $v>=4) {
										$Finals[$v]=$PhPools[$v];
									} else {
										$Finals[$v]=get_text(namePhase($row->GrPhase,$v)  . "_Phase");
									}
								}
							}

							if($row->EvElimType==4) {
								$this->data['sections'][$row->event]['meta']['firstPhase']=$row->GrPhase;
								$this->data['sections'][$row->event]['meta']['fields']['finals']=$Finals;
							}

						}
						$this->data['sections'][$row->event]['items'][$row->athlete]['finals'][$row->GrPhase]=array(
							'score'=>$row->score,
							'setScore'=>$row->setScore,
						 	'setPoints'=>$row->setPoints,
						 	'setPointsByEnd'=>$row->setPointsByEnd,
						 	'notes'=>$row->Notes,
							'tie'=>$row->tie,
							'arrowstring'=>implode('|',$arrowstring),
						 	'tiebreak'=>implode('|',$tiebreak),

							'oppAthlete'=>$row->oppAthlete,
							'oppScore'=>$row->oppScore,
							'oppSetScore'=>$row->oppSetScore,
						 	'oppSetPoints'=>$row->oppSetPoints,
						 	'oppSetPointsByEnd'=>$row->oppSetPointsByEnd,
						 	'oppNotes'=>$row->OppNotes,
							'oppTie'=>$row->oppTie,
							'oppArrowstring'=>implode('|',$oppArrowstring),
						 	'oppTiebreak'=>implode('|',$oppTiebreak)
						);
					}
				}
			}
		}
	}
