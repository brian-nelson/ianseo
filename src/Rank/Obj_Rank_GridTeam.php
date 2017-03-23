<?php
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');

/**
 * Obj_Rank_FinalTeam
 *
 * Implementa l'algoritmo di default per il recupero delle griglie finali individuali.
 * E' in sola lettura
 *
 *
 * A seconda della fase che sto trattando avrò porzioni di colonna da gestire differenti e calcoli differenti.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		events => array(<ev_1>,<ev_2>,...,<ev_n>)
 * 		tournament => #
 * )
 *
 * con:
 * 	 events: l'array con le coppie evento@fase di cui voglio la griglia.
 *  tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *
 * Estende Obj_Rank
 */
	class Obj_Rank_GridTeam extends Obj_Rank
	{
	/**
	 * safeFilter()
	 * Protegge con gli apici gli elementi di $this->opts['events']
	 *
	 * @return mixed: false se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
		var $EnIdFound=array();
		var $TeamFound='';

		protected function safeFilter() {
			$ret=array();
			if (!empty($this->opts['events'])) {
				if(!is_array($this->opts['events'])) $this->opts['events']=array($this->opts['events']);

				$f=array();

				foreach ($this->opts['events'] as $e) {
					@list($event,$phase)=explode('@',$e);
					if($event and !is_null($phase)) $f[] = '(EvCode=' . StrSafe_DB($event) . ' AND GrPhase=' . $phase . ')';
					elseif($event) $f[] = '(EvCode=' . StrSafe_DB($event) . ')';
					elseif(!is_null($phase)) $f[] = '(GrPhase=' . $phase . ')';
				}

				if($f) $ret[]= '(' . implode(' OR ', $f) . ')';
			}
			if(!empty($this->opts['schedule'])) {
				$ret[]="CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($this->opts['schedule']) . "";
			}
			if($ret) return ' AND '.implode(' AND ', $ret);
			return '';
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

		public function read()
		{
			$filter=$this->safeFilter();

			error_reporting(E_ALL);
		/*
		 *  prima passata per costruire la struttura del vettore.
		 *  Tiro fuori i nomi delle squadre
		 */
			$MyQueryNames  = "SELECT TfcId, TfcEvent, TfcCoId, TfcSubTeam, TfcOrder, EnCode, EnSex, EnNameOrder, ucase(EnFirstName) EnUpperName, EnFirstName, EnName, concat(ucase(EnFirstName), ' ', EnName) Athlete, CONCAT(TeRank,CHAR(64+TfcOrder)) AS BackNo  ";
			$MyQueryNames .= "FROM TeamFinComponent ";
			$MyQueryNames .= "INNER JOIN Events ON TfcEvent=EvCode AND TfcTournament=EvTournament AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 ";
			$MyQueryNames .= "INNER JOIN Entries ON TfcId=EnId AND TfcTournament=EnTournament ";
			$MyQueryNames .= "INNER JOIN Teams ON TfcCoId=TeCoId AND TfcSubTeam=TeSubTeam AND TfcEvent=TeEvent AND TfcTournament=TeTournament AND TeFinEvent=1 ";
			$MyQueryNames .= "WHERE TfcTournament = " . $this->tournament . " ";
			if(!empty($this->opts['events'])) {
				$MyQueryNames .= CleanEvents($this->opts['events'], 'TfcEvent');
			}
			$MyQueryNames .= " ORDER BY EvProgr, TfcEvent, TfcCoId, TfcSubTeam, EnFirstName, TfcOrder ";

			$this->data['sections']=array();
			$q=safe_r_SQL($MyQueryNames);
			while($r=safe_fetch($q)) {
				$this->data['sections'][$r->TfcEvent]['athletes'][$r->TfcCoId][$r->TfcSubTeam][]=array(
					'athlete' => $r->Athlete,
					'backNo' => $r->BackNo,
					'id' => $r->TfcId,
					'code' => $r->EnCode,
					'familyName' => $r->EnFirstName,
					'familyUpperName' => $r->EnUpperName,
					'givenName' => $r->EnName,
					'nameOrder' => $r->EnNameOrder,
					'gender' => $r->EnSex,
					);
				if(!empty($this->opts['enid']) and $r->TfcId==$this->opts['enid']) {
					$this->EnIdFound[]=$r->TfcEvent;
					$this->TeamFound=$r->TfcCoId;
				}
			}

			if(!empty($this->opts['enid'])) {
				if( !$this->EnIdFound) return;
				foreach($this->data['sections'] as $ev => $data) if(!in_array($ev, $this->EnIdFound)) unset($this->data['sections'][$ev]);
			}

			$q = "SELECT f1.*, f2.*,
					ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
					date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
					ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes from ("
				. "select"
				. " EvCode Event,"
				. " EvEventName EventDescr,"
				. " EvFinalFirstPhase,"
				. " EvMaxTeamPerson,"
				. " EvFinalPrintHead,"
				. " EvMatchMode,"
				. " EvProgr,"
				. " EvShootOff,"
				. " GrPhase Phase,"
				. " IF(EvFinalFirstPhase=48 || EvFinalFirstPhase=24,GrPosition2, GrPosition) Position,"
				. " TfTournament Tournament,"
				. " TfTeam Team,"
				. " TfSubTeam SubTeam,"
				. " TfMatchNo MatchNo,"
				. " TeRank QualRank,"
				. " TeRankFinal FinRank,"
				. " TeScore QualScore, "
				. " TfWinLose Winner, "
				. " TfDateTime LastUpdated, "
				. " CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as CountryName,"
				. " CoCode as CountryCode,"
				. " TfScore AS Score,"
				. " TfSetScore as SetScore,"
				. " TfTie Tie,"
				. " TfTieBreak TieBreak,"
				. " TfStatus Status, "
				. " TfSetPoints SetPoints, "
				. " TfSetPointsByEnd SetPointsByEnd, "
				. " TfArrowstring arrowstring, TfLive LiveFlag,"
				. " FSTarget Target,"
				. " TfNotes Notes, TfShootFirst as ShootFirst, "
				. " TarId, TarDescr, EvDistance as Distance, EvTargetSize as TargetSize, "
				. "	EvFinEnds, EvFinArrows, EvFinSO, EvElimEnds, EvElimArrows, EvElimSO, "
				. " DATE_FORMAT(FSScheduledDate,'" . get_text('DateFmtDB') . "') as ScheduledDate,"
				. " DATE_FORMAT(FSScheduledTime,'" . get_text('TimeFmt') . "') AS ScheduledTime "
				. " FROM TeamFinals "
				. " INNER JOIN Events ON TfEvent=EvCode AND TfTournament=EvTournament AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 and EvTournament=$this->tournament "
				. " INNER JOIN Grids ON TfMatchNo=GrMatchNo "
				. " INNER JOIN Targets ON EvFinalTargetType=TarId "
				. " LEFT JOIN Teams ON TfTeam=TeCoId AND TfSubTeam=TeSubTeam AND TfEvent=TeEvent AND TfTournament=TeTournament AND TeFinEvent=1 and TeTournament=$this->tournament "
				. " LEFT JOIN Countries ON TfTeam=CoId AND TfTournament=CoTournament and CoTournament=$this->tournament "
				. " LEFT JOIN FinSchedule ON TfEvent=FSEvent AND TfMatchNo=FSMatchNo AND TfTournament=FSTournament AND FSTeamEvent='1' and FSTournament=$this->tournament "
				. " WHERE TfMatchNo%2=0 AND TfTournament = " . $this->tournament . " " . $filter
				. ") f1 inner join ("
				. "select"
				. " EvCode OppEvent,"
				. " IF(EvFinalFirstPhase=48 || EvFinalFirstPhase=24,GrPosition2, GrPosition) OppPosition,"
				. " TfTournament OppTournament,"
				. " TfTeam OppTeam,"
				. " TfSubTeam OppSubTeam,"
				. " TfMatchNo OppMatchNo,"
				. " TeRank OppQualRank,"
				. " TeRankFinal OppFinRank,"
				. " TeScore OppQualScore, "
				. " TfWinLose OppWinner, "
				. " TfDateTime OppLastUpdated, "
				. " CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as OppCountryName,"
				. " CoCode as OppCountryCode,"
				. " TfScore AS OppScore,"
				. " TfSetScore as OppSetScore,"
				. " TfTie OppTie,"
				. " TfTieBreak OppTieBreak,"
				. " TfStatus OppStatus, "
				. " TfSetPoints OppSetPoints, "
				. " TfSetPointsByEnd OppSetPointsByEnd, "
				. " TfArrowstring oppArrowstring, "
				. " FSTarget OppTarget, "
				. " TfNotes OppNotes, TfShootFirst as OppShootFirst "
				. " FROM TeamFinals "
				. " INNER JOIN Events ON TfEvent=EvCode AND TfTournament=EvTournament AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 and EvTournament=$this->tournament "
				. " INNER JOIN Grids ON TfMatchNo=GrMatchNo "
				. " LEFT JOIN Teams ON TfTeam=TeCoId AND TfSubTeam=TeSubTeam AND TfEvent=TeEvent AND TfTournament=TeTournament AND TeFinEvent=1 and TeTournament=$this->tournament "
				. " LEFT JOIN Countries ON TfTeam=CoId AND TfTournament=CoTournament and CoTournament=$this->tournament "
				. " LEFT JOIN FinSchedule ON TfEvent=FSEvent AND TfMatchNo=FSMatchNo AND TfTournament=FSTournament AND FSTeamEvent='1' and FSTournament=$this->tournament "
				. " WHERE TfMatchNo%2=1 AND TfTournament = " . $this->tournament . " " . $filter
				. ") f2 on Tournament=OppTournament and Event=OppEvent and MatchNo=OppMatchNo-1
				LEFT JOIN DocumentVersions DV1 on Tournament=DV1.DvTournament AND DV1.DvFile = 'B-TEAM' and DV1.DvEvent=''
				LEFT JOIN DocumentVersions DV2 on Tournament=DV2.DvTournament AND DV2.DvFile = 'B-TEAM' and DV2.DvEvent=Event "
				. ($this->EnIdFound ? ' where Event in ("'.implode('","', $this->EnIdFound).'") AND (Team='.StrSafe_DB($this->TeamFound).' or oppTeam='.StrSafe_DB($this->TeamFound).')' : '')
				. (empty($this->opts['coid']) ? '' : " where (Team=" . intval($this->opts['coid'])." or OppTeam=" . intval($this->opts['coid']).") ")
				. (isset($this->opts['matchno']) ? " where MatchNo=" . intval($this->opts['matchno']).' ' : '')
				. (isset($this->opts['liveFlag']) ? " where LiveFlag=1 " : '')
				. " ORDER BY EvProgr ASC, event, Phase DESC, MatchNo ASC ";

			$r=safe_r_sql($q);

			$this->data['meta']['title']=get_text('BracketsSq');
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
			$this->data['meta']['fields']=array(
				// qui ci sono le descrizioni dei campi
				'scheduledDate' => get_text('Date', 'Tournament'),
				'scheduledTime' => get_text('Time', 'Tournament'),
				'winner' => get_text('Winner'),
				'matchNo' => get_text('MatchNo'),
				'bye' => get_text('Bye'),
				'bib' => get_text('Code','Tournament'),
				'target' => get_text('Target'),
				'athlete' => get_text('Athlete'),
				'familyname' => get_text('FamilyName', 'Tournament'),
				'givenname' => get_text('Name', 'Tournament'),
				'gender' => get_text('Sex', 'Tournament'),
				'countryCode' => '',
				'countryName' => get_text('Country'),
				'countryIocCode'=>'',
				'qualRank' => get_text('RankScoreShort'),
				'finRank' => get_text('FinalRank','Tournament'),
				'qualscore'=>get_text('TotalShort','Tournament'),
				'score'=>get_text('TotalShort','Tournament'),
				'setScore'=>get_text('SetTotal','Tournament'),
			 	'setPoints'=>get_text('SetPoints','Tournament'),
				'tie'=>'S.O.',
				'arrowstring'=>get_text('Arrows','Tournament'),
			 	'tiebreak'=>get_text('TieArrows'),
				'status'=>get_text('Status', 'Tournament'),
				'shootFirst'=>get_text('ShootsFirst', 'Tournament'),

				'oppMatchNo' => get_text('MatchNo'),
				'oppBib' => get_text('Code','Tournament'),
				'oppTarget' => get_text('Target'),
				'oppAthlete' => get_text('Athlete'),
				'oppFamilyname' => get_text('FamilyName', 'Tournament'),
				'oppGivenname' => get_text('Name', 'Tournament'),
				'oppGender' => get_text('Sex', 'Tournament'),
				'oppCountryCode' => '',
				'oppCountryName' => get_text('Country'),
				'oppCountryIocCode'=>'',
				'oppQualRank' => get_text('RankScoreShort'),
				'oppFinRank' => get_text('FinalRank','Tournament'),
				'oppQualScore'=>get_text('TotalShort','Tournament'),
				'oppScore'=>get_text('TotalShort','Tournament'),
				'oppSetScore'=>get_text('SetTotal','Tournament'),
			 	'oppSetPoints'=>get_text('SetPoints','Tournament'),
				'oppTie'=>'S.O.',
				'oppArrowstring'=>get_text('Arrows','Tournament'),
			 	'oppTiebreak'=>get_text('TieArrows'),
				'oppStatus'=>get_text('Status', 'Tournament'),
				'oppShootFirst'=>get_text('ShootsFirst', 'Tournament')

				);

			while($myRow=safe_fetch($r)) {
				if($myRow->LastUpdated>$this->data['meta']['lastUpdate']) $this->data['meta']['lastUpdate']=$myRow->LastUpdated;
				if($myRow->OppLastUpdated>$this->data['meta']['lastUpdate']) $this->data['meta']['lastUpdate']=$myRow->OppLastUpdated;
				if(!isset($this->data['sections'][$myRow->Event]['meta'])) {
					$tmp=GetMaxScores($myRow->Event, 0, 1, $this->tournament);

					$this->data['sections'][$myRow->Event]['meta']=array(
						'phase' => get_text('Phase'),
						'eventName' => get_text($myRow->EventDescr,'','',true),
						'firstPhase' => $myRow->EvFinalFirstPhase,
						'printHead' => get_text($myRow->EvFinalPrintHead,'','',true),
						'maxTeamPerson'=>$myRow->EvMaxTeamPerson,
						'matchMode'=>$myRow->EvMatchMode,
						'order'=>$myRow->EvProgr,
						'shootOffSolved'=>$myRow->EvShootOff,
						'finEnds' => $myRow->EvFinEnds,
						'finArrows' => $myRow->EvFinArrows,
						'finSO' => $myRow->EvFinSO,
						'finMaxScore' => $myRow->EvFinArrows*10,
						'elimEnds' => $myRow->EvElimEnds,
						'elimArrows' => $myRow->EvElimArrows,
						'elimSO' => $myRow->EvElimSO,
						'elimMaxScore' => $myRow->EvElimArrows*10,
						'targetType' => $myRow->TarDescr,
						'targetTypeId' => $myRow->TarId,
						'targetSize' => $myRow->TargetSize,
						'distance' => $myRow->Distance,
						'version' => $myRow->DocVersion,
						'versionDate' => $myRow->DocVersionDate,
						'versionNotes' => $myRow->DocNotes,
						'maxPoint' => $tmp['MaxPoint'],
						'minPoint' => $tmp['MinPoint'],
						);
					$this->data['sections'][$myRow->Event]['phases']=array();
					if(!empty($this->opts['records'])) {
						$this->data['sections'][$myRow->Event]['records'] = $this->getRecords($myRow->Event, true, true);
					}
				}

				if(!isset($this->data['sections'][$myRow->Event]['phases'][$myRow->Phase])) {
					$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']=array(
						'phaseName' => get_text($myRow->Phase . "_Phase"),
						'matchName' => get_text('MatchName-'.$myRow->Phase, 'Tournament')
						);
					$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['items']=array();
				}

				$tmpArr=array();
				$oppArr=array();
				if($myRow->TieBreak) {
					for($countArr=0; $countArr<strlen(trim($myRow->TieBreak)); $countArr+=$myRow->EvMaxTeamPerson) {
						$tmp=ValutaArrowString(substr(trim($myRow->TieBreak),$countArr,$myRow->EvMaxTeamPerson));
						if(!ctype_upper(trim($myRow->TieBreak)))
							$tmp .=  "*";
						$tmpArr[] = $tmp;
					}
				}
				if($myRow->OppTieBreak) {
					for($countArr=0; $countArr<strlen(trim($myRow->OppTieBreak)); $countArr+=$myRow->EvMaxTeamPerson) {
						$tmp=ValutaArrowString(substr(trim($myRow->OppTieBreak),$countArr,$myRow->EvMaxTeamPerson));
						if(!ctype_upper(trim($myRow->OppTieBreak)))
							$tmp .=  "*";
						$oppArr[] = $tmp;
					}
				}

				$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['items'][]=array(
					// qui ci sono le descrizioni dei campi
					'liveFlag' => $myRow->LiveFlag,
					'scheduledDate' => $myRow->ScheduledDate,
					'scheduledTime' => $myRow->ScheduledTime,
					'matchNo' => $myRow->MatchNo,
					'target' => $myRow->Target,
					'countryCode' => $myRow->CountryCode,
					'countryName' => $myRow->CountryName,
					'qualRank' => $myRow->QualRank,
					'finRank' => $myRow->FinRank,
					'qualScore'=> $myRow->QualScore,
					'winner' => $myRow->Winner,
					'score'=> $myRow->Score,
					'setScore'=> $myRow->SetScore,
				 	'setPoints'=> $myRow->SetPoints,
				 	'setPointsByEnd'=> $myRow->SetPointsByEnd,
				 	'notes'=> $myRow->Notes,
				 	'arrowstring'=> $myRow->arrowstring,
					'tie'=> $myRow->Tie,
				 	'tiebreak'=> $myRow->TieBreak,
				 	'tiebreakDecoded'=> implode(',', $tmpArr),
					'status'=>$myRow->Status,
					'shootFirst'=>$myRow->ShootFirst,
				 	'position'=> $myRow->QualRank ? $myRow->QualRank : $myRow->Position,
				 	'teamId'=> $myRow->Team,
				 	'subTeam'=> $myRow->SubTeam,
//
					'oppMatchNo' => $myRow->OppMatchNo,
					'oppTarget' => $myRow->OppTarget,
					'oppCountryCode' => $myRow->OppCountryCode,
					'oppCountryName' => $myRow->OppCountryName,
					'oppQualRank' => $myRow->OppQualRank,
					'oppFinRank' => $myRow->OppFinRank,
					'oppQualScore'=> $myRow->OppQualScore,
					'oppWinner' => $myRow->OppWinner,
					'oppScore'=> $myRow->OppScore,
					'oppSetScore'=> $myRow->OppSetScore,
				 	'oppSetPoints'=> $myRow->OppSetPoints,
				 	'oppSetPointsByEnd'=> $myRow->OppSetPointsByEnd,
				 	'oppNotes'=> $myRow->OppNotes,
				 	'oppArrowstring'=> $myRow->oppArrowstring,
					'oppTie'=> $myRow->OppTie,
				 	'oppTiebreak'=> $myRow->OppTieBreak,
				 	'oppTiebreakDecoded'=> implode(',', $oppArr),
					'oppStatus'=>$myRow->OppStatus,
					'oppShootFirst'=>$myRow->OppShootFirst,
				 	'oppPosition'=> $myRow->OppQualRank ? $myRow->OppQualRank : $myRow->OppPosition,
				 	'oppTeamId'=> $myRow->OppTeam,
				 	'oppSubTeam'=> $myRow->OppSubTeam,
					);


			}
		}

		function getData() {
			if(!empty($this->opts['enid']) and !$this->EnIdFound) return;
			return parent::getData();
		}
	}
