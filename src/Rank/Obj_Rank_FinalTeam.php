<?php
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');

/**
 * Obj_Rank_FinalTeam
 *
 * Implementa l'algoritmo di default per il calcolo della rank finale a squadre.
 *
 * La tabella in cui scrive è Teams e popola la RankFinal "a pezzi". Solo alla fine della gara
 * avremo tutta la colonna valorizzata.
 *
 * A seconda della fase che sto trattando avrò porzioni di colonna da gestire differenti e calcoli differenti.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		eventsC => array(<ev_1>@<calcPhase_1>,<ev_2>@<calcPhase_2>,...,<ev_n>@<calcPhase_n>)			[calculate,non influisce su read]
 * 		eventsR => array(<ev_1>,...,<ev_n>)																[read,non influisce su calculate]
 * 		tournament => #																					[calculate/read]
 * )
 */
	class Obj_Rank_FinalTeam extends Obj_Rank
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

	/**
	 * read()
	 *
	 * @Override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#read()
	 */
		public function read()
		{
			$f=$this->safeFilterR();

			$filter="";
			if ($f!==false)
			{
				$filter=$f;
			}

			if (array_key_exists('cutRank',$this->opts) && is_numeric($this->opts['cutRank']) && $this->opts['cutRank']>0)
				$filter.= "AND IF(EvFinalFirstPhase=0, TeRank, TeRankFinal)<={$this->opts['cutRank']} ";

			$EnFilter  = (empty($this->opts['enid']) ? '' : " AND EnId=" . intval($this->opts['enid'])) ;
			$EnFilter .= (empty($this->opts['coid']) ? '' : " AND EnCountry=" . intval($this->opts['coid'])) ;
			$EnFilter .= (empty($this->opts['cutRank']) ? '' : " AND IF(EvFinalFirstPhase=0, TeRank, TeRankFinal)<=" . intval($this->opts['cutRank'])) ;

			$Order='';
// 			if(empty($this->opts['alpha'])) $Order='personOrder ASC, ';

			$phases=null;

		/*
		 * prima passata per costruire la struttura del vettore.
		 * Tiro fuori le posizioni di qualifica e le posizioni finali con i nomi.
		 * La query è divisa in due: la prima parte tira fuori le squadre che sono andate in finale
		 * e la seconda quelle che si son fermate prima.
		 * Devo far così perchè i nomi dei membri dei team provengono da due tabelle diverse.

		 */
			/* parte delle finali */
			$q="
				(
					SELECT 1,
						CoId, TeSubTeam, CoCode, CoName, if(CoNameComplete>'', CoNameComplete, CoName) as CoNameComplete,
						EvProgr, TeEvent, EvEventName, EvMaxTeamPerson,
						EvFinalPrintHead as PrintHeader,
						EvFinalFirstPhase, EvMatchMode, EvMedals,
						EnId,EnCode, EnSex, EnNameOrder,EnFirstName,upper(EnFirstName) EnFirstNameUpper,EnName,tc.TfcOrder AS personOrder,
						TeRank as QualRank, IF(EvFinalFirstPhase=0, TeRank, TeRankFinal) as FinalRank, TeScore,
						TeTimestamp,TeTimestampFinal,
						ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
						date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
						ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes
					FROM
						Tournament

						INNER JOIN
							Teams
						ON ToId=TeTournament AND TeFinEvent=1

						INNER JOIN
							Countries
						ON TeCoId=CoId AND TeTournament=CoTournament

						INNER JOIN
							TeamFinComponent AS tc
						ON Teams.TeCoId=tc.TfcCoId AND Teams.TeSubTeam=tc.TfcSubTeam AND  Teams.TeEvent=tc.TfcEvent AND Teams.TeTournament=tc.TfcTournament AND Teams.TeFinEvent=1

						INNER JOIN
							Entries
						ON TfcId=EnId

						INNER JOIN
							Events
						ON TeEvent=EvCode AND ToId=EvTournament AND EvTeamEvent=1
						LEFT JOIN DocumentVersions DV1 on EvTournament=DV1.DvTournament AND DV1.DvFile = 'R-TEAM' and DV1.DvEvent=''
						LEFT JOIN DocumentVersions DV2 on EvTournament=DV2.DvTournament AND DV2.DvFile = 'R-TEAM' and DV2.DvEvent=EvCode

					WHERE
						IF(EvFinalFirstPhase=0, TeRank, TeRankFinal)<=(EvFinalFirstPhase*2) AND TeScore != 0 AND ToId = {$this->tournament}
						{$filter}
						{$EnFilter}

				)
				UNION ALL
				(
					SELECT 2,
						CoId,TeSubTeam,CoCode,CoName, if(CoNameComplete>'', CoNameComplete, CoName) as CoNameComplete,
						EvProgr,TeEvent,EvEventName,EvMaxTeamPerson,
						EvFinalPrintHead as PrintHeader,
						EvFinalFirstPhase,EvMatchMode,EvMedals,
						EnId,EnCode, EnSex, EnNameOrder,EnFirstName,upper(EnFirstName) EnFirstNameUpper,EnName,tc.TcOrder,
						TeRank as QualRank, IF(EvFinalFirstPhase=0, TeRank, TeRankFinal) as FinalRank, TeScore,
						TeTimestamp,TeTimestampFinal,
						ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
						date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
						ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes
					FROM
						Tournament

						INNER JOIN
							Teams
						ON ToId=TeTournament AND TeFinEvent=1

						INNER JOIN
							Countries
						ON TeCoId=CoId AND TeTournament=CoTournament

						INNER JOIN
							TeamComponent AS tc
						ON Teams.TeCoId=tc.TcCoId AND Teams.TeSubTeam=tc.TcSubTeam AND  Teams.TeEvent=tc.TcEvent AND Teams.TeTournament=tc.TcTournament AND Teams.TeFinEvent=tc.TcFinEvent AND Teams.TeFinEvent=1

						INNER JOIN
							Entries
						ON TcId=EnId

						INNER JOIN
							Events
						ON TeEvent=EvCode AND ToId=EvTournament AND EvTeamEvent=1
						LEFT JOIN DocumentVersions DV1 on EvTournament=DV1.DvTournament AND DV1.DvFile = 'R-TEAM' and DV1.DvEvent=''
						LEFT JOIN DocumentVersions DV2 on EvTournament=DV2.DvTournament AND DV2.DvFile = 'R-TEAM' and DV2.DvEvent=EvCode
					WHERE
						IF(EvFinalFirstPhase=0, TeRank, TeRankFinal)>(EvFinalFirstPhase*2)  AND TeScore != 0 AND ToId = {$this->tournament}
						/*AND CONCAT(TeCoId,'_',TeSubTeam) NOT IN (SELECT DISTINCT CONCAT(TfTeam,'_',TfSubTeam) FROM TeamFinals WHERE TfTournament={$this->tournament})*/
						{$filter}
						{$EnFilter}
				)
				ORDER BY
					EvProgr, TeEvent,/*rowType ASC,*/ FinalRank ASC, CoCode ASC, TeSubTeam, {$Order} EnFirstName, EnName
			";

// 						debug_svela($q);
			$r=safe_r_sql($q);

			$this->data['meta']['title']=get_text('TeamFinEvent','Tournament');
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
			$this->data['sections']=array();

			$myEv='';
			$myTeam='';

			if(safe_num_rows($r)>0)
			{
				$section=null;

				while ($myRow=safe_fetch($r))
				{
					if ($myEv!=$myRow->TeEvent)
					{
						if ($myEv!='')
						{
							$this->data['sections'][$myEv]=$section;
							$section=null;

						}

						$myEv=$myRow->TeEvent;
						$phases=getPhasesId($myRow->EvFinalFirstPhase);

						$fields=array(
							'id' 			=> 'Id',
							'countryCode' 	=> '',
							'countryName' 	=> get_text('Country'),
							'subteam' 		=> get_text('PartialTeam'),
							'athletes' 		=> array(
								'name' => get_text('Name','Tournament'),
								'fields'=>array(
									'id'  => 'Id',
									'bib' => get_text('Code','Tournament'),
									'athlete' => get_text('Athlete'),
									'familyname' => get_text('FamilyName', 'Tournament'),
									'givenname' => get_text('Name', 'Tournament'),
									'gender' => get_text('Sex', 'Tournament')
								)
							),
							'qualRank' => get_text('RankScoreShort'),
							'qualScore' => get_text('PositionShort'),
							'rank'			=> get_text('PositionShort'),
							'finals'=>array()
						);

						foreach($phases as $k => $v)
						{
							if($v<=valueFirstPhase($myRow->EvFinalFirstPhase))
							{
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
								'event' => $myEv,
								'descr' => get_text($myRow->EvEventName, '', '', true),
								'printHeader'=>get_text($myRow->PrintHeader, '', '', true),
								'firstPhase'=>$myRow->EvFinalFirstPhase,
								'matchMode'=>$myRow->EvMatchMode,
								'maxTeamPerson'=>$myRow->EvMaxTeamPerson,
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



					if ($myTeam!=$myRow->CoId . $myRow->TeSubTeam . $myRow->TeEvent)
					{
						//print $myRow->CoId . '.'.$myRow->TeSubTeam . '.'.$myRow->TeEvent.'<br>';
					//	if ($myRow->rowType==1 && array_key_exists($myRow->CoId .'_'. $myRow->TeSubTeam,$section['items']))
					//	{
					//		continue;
					//	}

						//if ($myRow->rowType==0 || ($myRow->rowType==1 && !array_key_exists($myRow->CoId.'_'.$myRow->TeSubTeam,$section['items'])))
						{
							$item=array(
								'id' 			=> $myRow->CoId,
								'countryCode' 	=> $myRow->CoCode,
								'countryName' 	=> $myRow->CoName,
								'countryNameLong' 	=> $myRow->CoNameComplete,
								'subteam' 		=> $myRow->TeSubTeam,
								'athletes'		=> array(),
								'qualScore'		=> $myRow->TeScore,
								'qualRank'		=> $myRow->QualRank,
								'rank'			=> ($myRow->FinalRank == 9999 ? 'DSQ' : $myRow->FinalRank),
								'finals'		=> array()
							);

							$section['items'][$myRow->CoId.'_'.$myRow->TeSubTeam]=$item;

							if ($myRow->TeTimestampFinal>$section['meta']['lastUpdate'])
								$section['meta']['lastUpdate']=$myRow->TeTimestampFinal;
							if ($myRow->TeTimestampFinal>$this->data['meta']['lastUpdate'])
								$this->data['meta']['lastUpdate']=$myRow->TeTimestampFinal;

						}

						$myTeam=$myRow->CoId . $myRow->TeSubTeam . $myRow->TeEvent;
					}

					if (!array_key_exists('components',$this->opts) || $this->opts['components'])
					{
						//if ($myRow->rowType==0 || ($myRow->rowType==1 && !array_key_exists($myRow->CoId.'_'.$myRow->TeSubTeam,$section['items'])))
						{
							$athlete=array(
								'id' => $myRow->EnId,
								'bib' => $myRow->EnCode,
								'athlete'=>$myRow->EnFirstNameUpper . ' ' . $myRow->EnName,
								'familyname' => $myRow->EnFirstName,
								'familynameUpper' => $myRow->EnFirstNameUpper,
								'givenname' => $myRow->EnName,
								'nameOrder' => $myRow->EnNameOrder,
								'gender' => $myRow->EnSex,
							);

							//$section['items'][count($section['items'])-1]['athletes'][]=$athlete;
							$section['items'][$myRow->CoId.'_'.$myRow->TeSubTeam]['athletes'][]=$athlete;
						}
					}
				}

			// ultimo giro
				$this->data['sections'][$myEv]=$section;
			}

		//	print count($this->data['sections']['OLMT']['items']);exit;

		/*
		 * A questo punto ho i nomi e le qualifiche
		 * e punti+rank delle precedenti.
		 * Mi mancano le finali.
		 *
		 */

			$q="
				SELECT
					f1.TfEvent AS `event`,CONCAT(f1.TfTeam,'_',f1.TfSubTeam) AS `athlete`,f1.TfMatchNo AS `matchNo`,f1.TfScore AS `score`,f1.TfSetScore AS `setScore`,f1.TfSetPoints AS `setPoints`,f1.TfSetPointsByEnd AS `setPointsByEnd`,f1.TfTie AS `tie`,f1.TfArrowstring AS `arrowstring`,f1.TfTiebreak AS `tiebreak`,
					CONCAT(f2.TfTeam,'_',f2.TfSubTeam) AS `oppAthlete`,f2.TfMatchNo AS `oppMatchNo`,f2.TfScore AS `oppScore`,f2.TfSetScore AS `oppSetScore`,f2.TfSetPoints AS `oppSetPoints`,f2.TfSetPointsByEnd AS `oppSetPointsByEnd`,f2.TfTie AS `oppTie`,f2.TfArrowstring AS `oppArrowstring`,f2.TfTiebreak AS `oppTiebreak`,
					GrPhase, EvMaxTeamPerson, f1.TfNotes as Notes, f2.TfNotes as oppNotes
				FROM
					Teams
					INNER JOIN
						TeamFinals AS f1
					ON TeTournament=f1.TfTournament AND TeEvent=f1.TfEvent AND CONCAT(TeCoId,'_',TeSubTeam)=CONCAT(f1.TfTeam,'_',f1.TfSubTeam)
					INNER JOIN
						TeamFinals AS f2
					ON f1.TfEvent=f2.TfEvent AND f1.TfMatchNo=IF((f1.TfMatchNo % 2)=0,f2.TfMatchNo-1,f2.TfMatchNo+1) AND f1.TfTournament=f2.TfTournament

					INNER JOIN
						Grids
					ON f1.TfMatchNo=GrMatchNo
					INNER JOIN
						Events
					ON f1.TfTournament=EvTournament AND f1.TfEvent=EvCode AND EvTeamEvent=1
				WHERE
					f1.TfTournament={$this->tournament}
					{$filter}
				ORDER BY
					EvProgr ASC,EvCode,TeRankFinal ASC,GrPhase DESC
			";

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

					$tmpArr=array();
					$oppArr=array();
					if($row->tiebreak) {
						for($countArr=0; $countArr<strlen(trim($row->tiebreak)); $countArr+=$row->EvMaxTeamPerson) {
							$tmp=ValutaArrowString(substr(trim($row->tiebreak),$countArr,$row->EvMaxTeamPerson));
							if(!ctype_upper(trim($row->tiebreak)))
								$tmp .=  "*";
							$tmpArr[] = $tmp;
						}
					}
					if($row->oppTiebreak) {
						for($countArr=0; $countArr<strlen(trim($row->oppTiebreak)); $countArr+=$row->EvMaxTeamPerson) {
							$tmp=ValutaArrowString(substr(trim($row->oppTiebreak),$countArr,$row->EvMaxTeamPerson));
							if(!ctype_upper(trim($row->oppTiebreak)))
								$tmp .=  "*";
							$oppArr[] = $tmp;
						}
					}

					if(isset($this->data['sections'][$row->event]['items'][$row->athlete]['finals'])) {
						$this->data['sections'][$row->event]['items'][$row->athlete]['finals'][$row->GrPhase]=array(
							'score'=>$row->score,
							'setScore'=>$row->setScore,
						 	'setPoints'=>$row->setPoints,
						 	'setPointsByEnd'=>$row->setPointsByEnd,
							'tie'=>$row->tie,
							'arrowstring'=>implode('|',$arrowstring),
						 	'tiebreak'=>implode('|',$tiebreak),
						 	'tiebreakDecoded'=>implode(',',$tmpArr),
						 	'notes'=>$row->Notes,

							'oppAthlete'=>$row->oppAthlete,
							'oppScore'=>$row->oppScore,
							'oppSetScore'=>$row->oppSetScore,
						 	'oppSetPoints'=>$row->oppSetPoints,
						 	'oppSetPointsByEnd'=>$row->oppSetPointsByEnd,
							'oppTie'=>$row->oppTie,
							'oppArrowstring'=>implode('|',$oppArrowstring),
						 	'oppTiebreak'=>implode('|',$oppTiebreak),
						 	'oppTiebreakDecoded'=>implode(',',$oppArr),
						 	'oppNotes'=>$row->oppNotes,
						);
					}
				}
			}
		}
	}