<?php
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');

/**
 * Obj_Rank_FinalInd
 *
 * Implementa l'algoritmo di default per il recupero delle griglie finali individuali.
 * E' in sola lettura
 *
 * La tabella in cui scrive è Individuals e popola la RankFinal "a pezzi". Solo alla fine della gara
 * avremo tutta la colonna valorizzata.
 *
 * A seconda della fase che sto trattando avrò porzioni di colonna da gestire differenti e calcoli differenti.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		events => array(<ev_1>@<calcPhase_1>,<ev_2>@<calcPhase_2>,...,<ev_n>@<calcPhase_n>)
 * 		tournament => #
 * )
 *
 * con:
 * 	 events: l'array con le coppie evento@fase di cui voglio la griglia.
 *  tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *
 * Estende Obj_Rank
 */
	class Obj_Rank_GridInd extends Obj_Rank
	{
	/**
	 * safeFilter()
	 * Protegge con gli apici gli elementi di $this->opts['events']
	 *
	 * @return mixed: false se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
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

		/*
		 *  prima passata per costruire la struttura del vettore.
		 *  Tiro fuori le qualifiche, le posizioni finali e le eliminatorie (se ci sono)
		 */
			$q="SELECT"
				. " * "
				. "FROM (select"
					. " FinEvent Event,"
					. " EvProgr,"
					. " EvEventName AS EventDescr,"
					. " EvMatchMode,"
					. " EvFinalFirstPhase, "
					. " EvFinalPrintHead, "
					. " GrPhase Phase,"
					. " EvShootOff,"
					. " IF(EvFinalFirstPhase=48 || EvFinalFirstPhase=24,GrPosition2, GrPosition) Position,"
					. " DATE_FORMAT(FSScheduledDate,'" . get_text('DateFmtDB') . "') as ScheduledDate,"
					. " DATE_FORMAT(FSScheduledTime,'" . get_text('TimeFmt') . "') AS ScheduledTime, "
					. " FinTournament Tournament,"
					. " FinDateTime LastUpdated,"
					. " FinMatchNo MatchNo,"
					. " EnCode Bib,"
					. " EnId,"
					. " FsTarget Target,"
					. " TarId, TarDescr, EvDistance as Distance, EvTargetSize as TargetSize, "
					. " concat(upper(EnFirstName), ' ', EnName) Athlete,"
					. " EnFirstName FamilyName,"
					. " upper(EnFirstName) FamilyNameUpper,"
					. " EnName GivenName,"
					. " CoId CountryId,"
					. " CoCode CountryCode,"
					. " CoName CountryName,"
					. " CoIocCode CountryIocCode,"
					. " IndRank QualRank,"
					. " IndRankFinal FinRank,"
					. " QuScore QualScore,"
					. "	EvFinEnds, EvFinArrows, EvFinSO, EvElimEnds, EvElimArrows, EvElimSO, "
					. " FinWinLose Winner,"
					. " FinScore Score,"
					. " FinSetScore SetScore,"
					. " FinSetPoints SetPoints,"
					. " FinTie AS Tie,"
					. " FinArrowstring ArrowString,"
					. " FinTiebreak TieBreak,"
					. " FinStatus Status, "
					. " FinLive LiveFlag "
					. "FROM "
					. " Finals "
					. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
					. "INNER JOIN Events ON FinEvent=EvCode AND FinTournament=EvTournament AND EvTeamEvent=0 "
					. "INNER JOIN Targets ON EvFinalTargetType=TarId "
					. "LEFT JOIN Individuals ON FinAthlete=IndId AND FinEvent=IndEvent AND FinTournament=IndTournament "
					. "LEFT JOIN Entries ON FinAthlete=EnId AND FinTournament=EnTournament "
					. "LEFT JOIN Qualifications ON QuId=EnId "
					. "LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
					. "LEFT JOIN FinSchedule ON FinEvent=FSEvent AND FinMatchNo=FSMatchNo AND FinTournament=FSTournament AND FSTeamEvent='0' "
					. "WHERE FinMatchNo%2=0 "
					. " AND FinTournament = " . $this->tournament . " " . $filter
					. ") f1 "
				. "INNER JOIN (select"
					. " FinEvent OppEvent,"
					. " FinTournament OppTournament,"
					. " FinDateTime OppLastUpdated,"
					. " FinMatchNo OppMatchNo,"
					. " EnCode OppBib,"
					. " EnId OppEnId,"
					. " FsTarget OppTarget,"
					. " concat(upper(EnFirstName), ' ', EnName) OppAthlete,"
					. " EnFirstName OppFamilyName,"
					. " upper(EnFirstName) OppFamilyNameUpper,"
					. " IF(EvFinalFirstPhase=48 || EvFinalFirstPhase=24,GrPosition2, GrPosition) OppPosition,"
					. " EnName OppGivenName,"
					. " CoId OppCountryId,"
					. " CoCode OppCountryCode,"
					. " CoName OppCountryName,"
					. " CoIocCode OppCountryIocCode,"
					. " IndRank OppQualRank,"
					. " IndRankFinal OppFinRank,"
					. " QuScore OppQualScore,"
					. " FinWinLose OppWinner,"
					. " FinScore OppScore,"
					. " FinSetScore OppSetScore,"
					. " FinSetPoints OppSetPoints,"
					. " FinTie AS OppTie,"
					. " FinArrowstring OppArrowString,"
					. " FinTiebreak OppTieBreak, "
					. " FinStatus OppStatus "
					. "FROM "
					. " Finals "
					. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
					. "INNER JOIN Events ON FinEvent=EvCode AND FinTournament=EvTournament AND EvTeamEvent=0 "
					. "LEFT JOIN Individuals ON FinAthlete=IndId AND FinEvent=IndEvent AND FinTournament=IndTournament "
					. "LEFT JOIN Entries ON FinAthlete=EnId AND FinTournament=EnTournament "
					. "LEFT JOIN Qualifications ON QuId=EnId "
					. "LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
					. "LEFT JOIN FinSchedule ON FinEvent=FSEvent AND FinMatchNo=FSMatchNo AND FinTournament=FSTournament AND FSTeamEvent='0' "
					. "WHERE FinMatchNo%2=1 "
					. " AND FinTournament = " . $this->tournament . " " . $filter
					. ") f2 on Tournament=OppTournament and Event=OppEvent and MatchNo=OppMatchNo-1 "
				. (empty($this->opts['enid']) ? '' : " where (EnId=" . intval($this->opts['enid'])." or OppEnId=" . intval($this->opts['enid']).") ")
				. (empty($this->opts['coid']) ? '' : " where (CountryId=" . intval($this->opts['coid'])." or OppCountryId=" . intval($this->opts['coid']).") ")
				. (isset($this->opts['matchno']) ? " where MatchNo=" . intval($this->opts['matchno']).' ' : '')
				. (isset($this->opts['liveFlag']) ? " where LiveFlag=1 " : '')
				. "ORDER BY EvProgr ASC, Event, Phase DESC, MatchNo ASC ";

			$r=safe_r_sql($q);

			$this->data['meta']['title']=get_text('BracketsInd');
			$this->data['meta']['saved']=get_text('Seeded16th');
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
			$this->data['meta']['fields']=array(
				// qui ci sono le descrizioni dei campi
				'scheduledDate' => get_text('Date', 'Tournament'),
				'scheduledTime' => get_text('Time', 'Tournament'),
				'winner' => get_text('Winner'),
				'matchNo' => get_text('MatchNo'),
				'bib' => get_text('Code','Tournament'),
				'target' => get_text('Target'),
				'athlete' => get_text('Athlete'),
				'familyname' => get_text('FamilyName', 'Tournament'),
				'givenname' => get_text('Name', 'Tournament'),
				'countryId' => '',
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

				'oppMatchNo' => get_text('MatchNo'),
				'oppBib' => get_text('Code','Tournament'),
				'oppTarget' => get_text('Target'),
				'oppAthlete' => get_text('Athlete'),
				'oppFamilyname' => get_text('FamilyName', 'Tournament'),
				'oppGivenname' => get_text('Name', 'Tournament'),
				'oppCountryId' => '',
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
				'oppStatus'=>get_text('Status', 'Tournament')
				);
			$this->data['sections']=array();

			while($myRow=safe_fetch($r)) {
				if($myRow->LastUpdated>$this->data['meta']['lastUpdate']) $this->data['meta']['lastUpdate']=$myRow->LastUpdated;
				if($myRow->OppLastUpdated>$this->data['meta']['lastUpdate']) $this->data['meta']['lastUpdate']=$myRow->OppLastUpdated;
				if(!isset($this->data['sections'][$myRow->Event])) {
					$this->data['sections'][$myRow->Event]['meta']=array(
						'phase' => get_text('Phase'),
						'eventName' => get_text($myRow->EventDescr,'','',true),
						'firstPhase' => $myRow->EvFinalFirstPhase,
						'printHead' => get_text($myRow->EvFinalPrintHead,'','',true),
						'matchMode'=>$myRow->EvMatchMode,
						'order'=>$myRow->EvProgr,
						'shootOffSolved'=>$myRow->EvShootOff,
						'finEnds' => $myRow->EvFinEnds,
						'finArrows' => $myRow->EvFinArrows,
						'finSO' => $myRow->EvFinSO,
						'elimEnds' => $myRow->EvElimEnds,
						'elimArrows' => $myRow->EvElimArrows,
						'elimSO' => $myRow->EvElimSO,
						'targetType' => $myRow->TarDescr,
						'targetTypeId' => $myRow->TarId,
						'targetSize' => $myRow->TargetSize,
						'distance' => $myRow->Distance,
						);
					$this->data['sections'][$myRow->Event]['phases']=array();
				}

				if(!isset($this->data['sections'][$myRow->Event]['phases'][$myRow->Phase])) {
					$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]=array('meta' => array(),'items' => array());
					$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']['phaseName'] = get_text(namePhase($myRow->EvFinalFirstPhase, $myRow->Phase) . "_Phase");
				}

				$tmpArr=array();
				$oppArr=array();
				if($myRow->TieBreak) {
					for($countArr=0; $countArr<strlen(trim($myRow->TieBreak)); $countArr++)
						$tmpArr[] = DecodeFromLetter(substr(trim($myRow->TieBreak),$countArr,1));
				}
				if($myRow->OppTieBreak) {
					for($countArr=0; $countArr<strlen(trim($myRow->OppTieBreak)); $countArr++)
						$oppArr[] = DecodeFromLetter(substr(trim($myRow->OppTieBreak),$countArr,1));
				}

				$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['items'][]=array(
					// qui ci sono le descrizioni dei campi
					'liveFlag' => $myRow->LiveFlag,
					'scheduledDate' => $myRow->ScheduledDate,
					'scheduledTime' => $myRow->ScheduledTime,
					'matchNo' => $myRow->MatchNo,
					'bib' => $myRow->Bib,
					'id' => $myRow->EnId,
					'target' => $myRow->Target,
					'athlete' => $myRow->Athlete,
					'familyName' => $myRow->FamilyName,
					'familyNameUpper' => $myRow->FamilyNameUpper,
					'givenName' => $myRow->GivenName,
					'countryId' => $myRow->CountryId,
					'countryCode' => $myRow->CountryCode,
					'countryName' => $myRow->CountryName,
					'countryIocCode'=> $myRow->CountryIocCode,
					'qualRank' => $myRow->QualRank,
					'finRank' => $myRow->FinRank,
					'qualScore'=> $myRow->QualScore,
					'winner' => $myRow->Winner,
					'score'=> $myRow->Score,
					'setScore'=> $myRow->SetScore,
				 	'setPoints'=> $myRow->SetPoints,
					'tie'=> $myRow->Tie,
					'arrowstring'=> $myRow->ArrowString,
				 	'tiebreak'=> $myRow->TieBreak,
				 	'tiebreakDecoded'=> implode(',', $tmpArr),
					'status'=>$myRow->Status,
				 	'position'=> $myRow->QualRank ? $myRow->QualRank : $myRow->Position,
				 	'saved'=> ($myRow->Position>0 and $myRow->Position<9 and $myRow->EvFinalFirstPhase!=valueFirstPhase($myRow->EvFinalFirstPhase)),
//
					'oppMatchNo' => $myRow->OppMatchNo,
					'oppBib' => $myRow->OppBib,
					'oppId' => $myRow->OppEnId,
					'oppTarget' => $myRow->OppTarget,
					'oppAthlete' => $myRow->OppAthlete,
					'oppFamilyName' => $myRow->OppFamilyName,
					'oppFamilyNameUpper' => $myRow->OppFamilyNameUpper,
					'oppGivenName' => $myRow->OppGivenName,
					'oppCountryId' => $myRow->OppCountryId,
					'oppCountryCode' => $myRow->OppCountryCode,
					'oppCountryName' => $myRow->OppCountryName,
					'oppCountryIocCode'=> $myRow->OppCountryIocCode,
					'oppQualRank' => $myRow->OppQualRank,
					'oppFinRank' => $myRow->OppFinRank,
					'oppQualScore'=> $myRow->OppQualScore,
					'oppWinner' => $myRow->OppWinner,
					'oppScore'=> $myRow->OppScore,
					'oppSetScore'=> $myRow->OppSetScore,
				 	'oppSetPoints'=> $myRow->OppSetPoints,
					'oppTie'=> $myRow->OppTie,
					'oppArrowstring'=> $myRow->OppArrowString,
				 	'oppTiebreak'=> $myRow->OppTieBreak,
				 	'oppTiebreakDecoded'=> implode(',', $oppArr),
					'oppStatus'=>$myRow->OppStatus,
				 	'oppPosition'=> $myRow->OppQualRank ? $myRow->OppQualRank : $myRow->OppPosition,
				 	'oppSaved'=> ($myRow->OppPosition>0 and $myRow->OppPosition<9 and $myRow->EvFinalFirstPhase!=valueFirstPhase($myRow->EvFinalFirstPhase)),
					);

					$curEvent='';
				$curPhase='';
				$section=null;

			}
		}
	}
