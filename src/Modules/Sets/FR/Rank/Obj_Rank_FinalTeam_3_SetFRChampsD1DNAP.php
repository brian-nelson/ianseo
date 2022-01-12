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
require_once('Common/Rank/Obj_Rank_FinalTeam.php');
class Obj_Rank_FinalTeam_3_SetFRChampsD1DNAP extends Obj_Rank_FinalTeam
{
	var $Competitions=array();
	var $Bonus=array();
	var $Winners=array();
	var $AllInOne=0;
	var $UseParent=false;

	/**
	 * safeFilterR()
	 * Protegge con gli apici gli elementi di $this->opts['eventsR']
	 *
	 * @return mixed: false se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
	protected function safeFilterR($Field = 'TeDaEvent', $ind=false)
	{
		if($this->UseParent) {
			return parent::safeFilterR();
		}

		$filter = '';

		if (array_key_exists('eventsR', $this->opts)) {
			if (is_array($this->opts['eventsR']) && count($this->opts['eventsR']) > 0) {
				$filter = array();

				foreach ($this->opts['eventsR'] as $e) {
					$filter[] = StrSafe_DB($e);
				}

				$filter = "AND $Field IN (" . implode(',', $filter) . ")";
			} elseif (gettype($this->opts['eventsR']) == 'string' && trim($this->opts['eventsR']) != '') {
				$filter = "AND $Field LIKE '" . $this->opts['eventsR'] . "' ";
			}
		}

		return $filter;
	}

	public function __construct($opts)
	{
		$this->UseParent=(isset($opts) and !empty($opts['eventsR']) and strlen(is_array($opts['eventsR']) ? $opts['eventsR'][0] : $opts['eventsR'])==4);
		parent::__construct($opts);

		if(!$this->UseParent) {
			$this->Bonus=getModuleParameter('FFTA', 'D1Bonus', array(), $this->tournament);
			$this->Winners=getModuleParameter('FFTA', 'D1Winners', array(), $this->tournament);
			if($comps=getModuleParameter('FFTA', 'ConnectedCompetitions', array(), $this->tournament)) {
				$SQL=safe_r_sql("select ToId, if(length(ToWhere)>20, ToCode, ToWhere) as City from Tournament where ToCode in (".implode(',', StrSafe_DB($comps)).")");
				while($r=safe_fetch($SQL)) {
					$this->Competitions[$r->ToId]=$r->City;
				}
			};
			$this->AllInOne=getModuleParameter('FFTA', 'D1AllInOne', 0);
		}
	}

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
		if($this->UseParent) {
			parent::read();
			return;
		}
		$filter = $this->safeFilterR();

		$this->data['meta']['title'] = get_text('TeamFinEvent', 'Tournament');
		$this->data['meta']['Year'] = $this->touryear;
		$this->data['meta']['lastUpdate'] = '0000-00-00 00:00:00';
		$this->data['meta']['target'] = get_text('Target');
		$this->data['meta']['score'] = get_text('Score', 'Tournament');
		$this->data['meta']['points'] = get_text('Points', 'Tournament');
		$this->data['meta']['gameTotal'] = get_text('GameTotal', 'Tournament');
		for($n=1;$n<=3;$n++) {
			$this->data['meta']['Run'.$n] = get_text('RunNumber', 'Tournament', $n);
		}
		for($n=1;$n<=15;$n++) {
			$this->data['meta']['Game'.$n] = get_text('GameNumber', 'Tournament', $n);
		}

		/*
		 * Select general items
		 *
		 * */

		$this->data['sections'] = array();
		$this->data['competitions']=$this->Competitions;
		$Comps=array_keys($this->Competitions);

		// get all the details
		$Bonus = getModuleParameter('FFTA', 'D1Bonus');
		$Events = $this->safeFilterR('TeDaEvent');
		$Details = array();
		$SQL = "select 
				TeDaEvent,
				TeDaTeam,
				TeDaSubTeam,
				TeDaBonusPoints,
				ToCode,
       			ToId,
                TeRank,
                TeScore
			from TeamDavis
			inner join Countries on CoCode=TeDaTeam and CoTournament=TeDaTournament
			inner join Teams on TeTournament=TeDaTournament and TeEvent=TeDaEvent and TeCoId=CoId and TeSubTeam=TeDaSubTeam and TeFinEvent=1
			inner join Tournament on ToId=TeDaTournament
			where TeDaTournament in (" . implode(',', $Comps) . ") $Events
			order by ToCode, TeDaEvent, TeRank";

		$q = safe_r_sql($SQL);
		while ($r = safe_fetch($q)) {
			$Details[$r->TeDaEvent][$r->TeDaTeam][$r->TeDaSubTeam][$r->ToId] = array('bon' => $r->TeDaBonusPoints, 'qual' => $r->TeScore, 'rank' => $r->TeRank);
		}

		$MatchDetails = array();
		$Events = $this->safeFilterR('tf1.TfEvent');
		// get the Team matches
		$SQL = "select 
       			truncate((tf1.TfMatchNo-128)/16,0) as Game,
       			tf1.TfMatchNo as MatchNo,
       			tf1.TfEvent,
				tf1.TfTournament,
                c1.CoCode as Team1,
                c2.CoCode as Team2,
                tf1.TfSubTeam as SubTeam1,
                tf2.TfSubTeam as SubTeam2,
                c1.CoName as TeamName1,
                c2.CoName as TeamName2,
                if(EvMatchMode=0, tf1.TfScore, tf1.TfSetScore) as Score1,
                if(EvMatchMode=0, tf2.TfScore, tf2.TfSetScore) as Score2,
                tf1.TfWinLose as Winner1,
                tf2.TfWinLose as Winner2,
       			fs1.FsTarget+0 as Target1,
       			fs2.FsTarget+0 as Target2
			from TeamFinals tf1
			left join Countries c1 on c1.CoId=tf1.TfTeam and c1.CoTournament=tf1.TfTournament
			inner join FinSchedule fs1 on fs1.FsEvent=tf1.TfEvent and fs1.FsTournament=tf1.TfTournament and fs1.FsMatchNo=tf1.TfMatchNo and fs1.FSTeamEvent=1
			inner join TeamFinals tf2 on tf2.TfEvent=tf1.TfEvent and tf2.TfTournament=tf1.TfTournament and tf2.TfMatchNo=tf1.TfMatchNo+1
			left join Countries c2 on c2.CoId=tf2.TfTeam and c2.CoTournament=tf2.TfTournament
			inner join FinSchedule fs2 on fs2.FsEvent=tf2.TfEvent and fs2.FsTournament=tf2.TfTournament and fs2.FsMatchNo=tf2.TfMatchNo and fs2.FSTeamEvent=1
			inner join Events on EvCode=tf1.TfEvent and EvTeamEvent=1 and EvTournament=tf1.TfTournament
			inner join Tournament on ToId=tf1.TfTournament
			where tf1.TfTournament in (" . implode(',', $Comps) . ") and tf1.TfMatchNo%2=0 and (tf1.TfTeam!=0 or tf2.TfTeam!=0) $Events
			order by EvProgr, ToWhenFrom, ToCode, tf1.TfMatchno";
		$q = safe_r_sql($SQL);
		while ($r = safe_fetch($q)) {
			$MatchDetails[$r->TfEvent][$r->TfTournament][$r->Game+1][$r->MatchNo]=array(
				'tgt1'=>$r->Target1,
				'tgt2'=>$r->Target2,
				'team1'=>$r->Team1.'_'.$r->SubTeam1,
				'team2'=>$r->Team2.'_'.$r->SubTeam2,
				'score1'=>$r->Winner1*2,
				'score2'=>$r->Winner2*2,
				'winner1'=>$r->Winner1,
				'winner2'=>$r->Winner2,
				'matchpoints1'=>0,
				'matchpoints2'=>0,
				'details'=>array(
					'E' => array(
						'Name1' => $r->TeamName1,
						'Name2' => $r->TeamName2,
						'score1' => $r->Score1,
						'score2' => $r->Score2,
						'points1' => $r->Winner1*2,
						'points2' => $r->Winner2*2,
					),
				),
			);
		}

		// get the individual matches
		if(!$this->AllInOne) {
			$Events = $this->safeFilterR('left(f1.FinEvent,3)');
			$SQL = "select 
	                truncate((f1.FinMatchNo-128)/16,0) as Game,
	                f1.FinMatchNo as MatchNo,
	                concat('I',right(f1.FinEvent,1)) as MatchType,
	                left(f1.FinEvent,3) as TeamEvent,
	                f1.FinEvent,
					f1.FinTournament,
	                c1.CoCode as Team1,
	                c2.CoCode as Team2,
	                0 as SubTeam1,
	                0 as SubTeam2,
	                concat(e1.EnName, ' ', e1.EnFirstName) as TeamName1,
	                concat(e2.EnName, ' ', e2.EnFirstName) as TeamName2,
	                if(EvMatchMode=0, f1.FinScore, f1.FinSetScore) as Score1,
	                if(EvMatchMode=0, f2.FinScore, f2.FinSetScore) as Score2,
	                f1.FinWinLose as Winner1,
	                f2.FinWinLose as Winner2
				from Finals f1
				left join Entries e1 on e1.EnId=f1.FinAthlete and e1.EnTournament=f1.FinTournament
				left join Countries c1 on c1.CoId=e1.EnCountry and c1.CoTournament=f1.FinTournament
				inner join Finals f2 on f2.FinEvent=f1.FinEvent and f2.FinTournament=f1.FinTournament and f2.FinMatchNo=f1.FinMatchNo+1
				left join Entries e2 on e2.EnId=f2.FinAthlete and e2.EnTournament=f2.FinTournament
				left join Countries c2 on c2.CoId=e2.EnCountry and c2.CoTournament=f2.FinTournament
				inner join Events on EvCode=f1.FinEvent and EvTeamEvent=0 and EvTournament=f1.FinTournament
				inner join Tournament on ToId=f1.FinTournament
				where f1.FinTournament in (" . implode(',', $Comps) . ") and f1.FinMatchNo%2=0 and (f1.FinAthlete!=0 or f2.FinAthlete!=0) $Events
				order by EvProgr, ToWhenFrom, ToCode, f1.FinMatchno";
			$q = safe_r_sql($SQL);
			while ($r = safe_fetch($q)) {
				if(!isset($MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo])) {
					continue;
				}
				$MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['score1']+=$r->Winner1;
				$MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['score2']+=$r->Winner2;

				// sets the matchpoints
				if($MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['score1'] + $MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['score2']>=5) {
					if($MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['score1']>$MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['score2']) {
						$MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['matchpoints1']=3;
					} elseif($MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['score1']<$MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['score2']) {
						$MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['matchpoints2']=3;
					} else {
						$MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['matchpoints1']=1;
						$MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['matchpoints2']=1;
					}
				}

				$MatchDetails[$r->TeamEvent][$r->FinTournament][$r->Game+1][$r->MatchNo]['details'][$r->MatchType]=array(
					'Name1' => $r->TeamName1,
					'Name2' => $r->TeamName2,
					'score1' => $r->Score1,
					'score2' => $r->Score2,
					'points1' => $r->Winner1,
					'points2' => $r->Winner2,
				);
			}
		}

		//debug_svela($MatchDetails);
		$this->data['details']=$MatchDetails;

		//At this point gets all the points each team has...
		$GeneralRank = array();
		$SQL = "select D1.*, Events.*,
       			CoId, CoName, if(CoNameComplete!='', CoNameComplete, CoName) as CoNameComplete, CoCode,
				ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
				date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
				ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes
			from (select max(TeDaDateTime) as TeDaDateTime, TeDaEvent, TeDaTeam, TeDaSubTeam, sum(TeDaBonusPoints) as BonusPoints, sum(TeDaMainPoints) as MainPoints, sum(TeDaWinPoints) as WinPoints, sum(TeDaLoosePoints) as LoosePoints, sum(TeScore) as Qualification
				from TeamDavis
				inner join Countries on CoCode=TeDaTeam and CoTournament=TeDaTournament
				inner join Teams on TeTournament=TeDaTournament and TeEvent=TeDaEvent and TeCoId=CoId and TeSubTeam=TeDaSubTeam and TeFinEvent=1
				where TeDaTournament in (" . implode(',', $Comps) . ") $filter
				group by TeDaEvent, TeDaTeam, TeDaSubTeam) D1
			inner join Events on EvCode=TeDaEvent and EvTeamEvent=1 and EvTournament={$this->tournament}
			INNER JOIN Countries ON TeDaTeam=CoCode AND CoTournament=EvTournament
			LEFT JOIN DocumentVersions DV1 on EvTournament=DV1.DvTournament AND DV1.DvFile = 'R-TEAM' and DV1.DvEvent=''
			LEFT JOIN DocumentVersions DV2 on EvTournament=DV2.DvTournament AND DV2.DvFile = 'R-TEAM' and DV2.DvEvent=EvCode
			order by EvProgr, MainPoints+BonusPoints desc, WinPoints-LoosePoints desc, WinPoints desc, Qualification desc";

		$r = safe_r_sql($SQL);
		$myEv = '';

		if (safe_num_rows($r) > 0) {
			$section = null;

			while ($myRow = safe_fetch($r)) {
				if ($myEv != $myRow->TeDaEvent) {
					if ($myEv != '') {
						$this->data['sections'][$myEv] = $section;
						$section = null;

					}

					$myEv = $myRow->TeDaEvent;

					$fields = array(
						'id' => 'Id',
						'countryCode' => '',
						'countryName' => get_text('Country'),
						'subteam' => get_text('PartialTeam'),
						'athletes' => array(
							'name' => get_text('Name', 'Tournament'),
							'fields' => array(
								'id' => 'Id',
								'bib' => get_text('Code', 'Tournament'),
								'athlete' => get_text('Athlete'),
								'familyname' => get_text('FamilyName', 'Tournament'),
								'givenname' => get_text('Name', 'Tournament'),
								'gender' => get_text('Sex', 'Tournament')
							)
						),
						'qualRank' => get_text('RankScoreShort'),
						'qualScore' => get_text('PositionShort'),
						'rank' => get_text('PositionShort'),
						'finals' => array()
					);

					$fields['finals'][64] = get_text("63_Phase");

					$fields['finals']['fields'] = array(
						'score' => get_text('TotalShort', 'Tournament'),
						'setScore' => get_text('SetTotal', 'Tournament'),
						'setPoints' => get_text('SetPoints', 'Tournament'),
						'tie' => 'S.O.',
						'arrowstring' => get_text('Arrows', 'Tournament'),
						'tiebreak' => get_text('TieArrows')
					);

					$section = array(
						'meta' => array(
							'event' => $myEv,
							'descr' => get_text($myRow->EvEventName, '', '', true),
							'printHeader' => get_text($myRow->EvFinalPrintHead, '', '', true),
							'firstPhase' => $myRow->EvFinalFirstPhase,
							'matchMode' => $myRow->EvMatchMode,
							'parent' => $myRow->EvCodeParent,
							'hasChildren' => getChildrenEvents($myEv, 1, $this->tournament),
							'maxTeamPerson' => $myRow->EvMaxTeamPerson,
							'order' => $myRow->EvProgr,
							'lastUpdate' => '0000-00-00 00:00:00',
							'fields' => $fields,
							'medals' => $myRow->EvMedals,
							'version' => $myRow->DocVersion,
							'versionDate' => $myRow->DocVersionDate,
							'versionNotes' => $myRow->DocNotes,
						),
						'items' => array()
					);
					$rnk = 1;
					$i = 1;
					$OldPoints = 0;
					$OldDiff = 0;
					$OldWins = 0;
					$OldQual = 0;
				}

				if (!$rnk or $OldPoints > $myRow->MainPoints+$myRow->BonusPoints or $OldDiff > $myRow->WinPoints - $myRow->LoosePoints or (!$this->AllInOne and $OldWins > $myRow->WinPoints) or $OldQual > $myRow->Qualification) {
					$rnk = $i;
				} else {
					// check the direct match between old team and this team
					// TODO: how to solve the ex-aequo?
				}
				$i++;
				$OldPoints = $myRow->MainPoints+$myRow->BonusPoints;
				$OldDiff = $myRow->WinPoints - $myRow->LoosePoints;
				$OldWins = $myRow->WinPoints;
				$OldQual = $myRow->Qualification;

				// NO athletes!
				$item = array(
					'id' => $myRow->CoId,
					'countryCode' => $myRow->CoCode,
					'countryName' => $myRow->CoName,
					'countryNameLong' => $myRow->CoNameComplete,
					'subteam' => $myRow->TeDaSubTeam,
					'athletes' => array(),
					'qualScore' => $myRow->Qualification,
					'bonusPoints' => $myRow->BonusPoints,
					'mainPoints' => $myRow->MainPoints,
					'diff' => $myRow->WinPoints - $myRow->LoosePoints,
					'winPoints' => $myRow->WinPoints,
					'loosePoints' => $myRow->LoosePoints,
					'qualRank' => 0,
					'rank' => $rnk,
					'finals' => $Details[$myRow->TeDaEvent][$myRow->TeDaTeam][$myRow->TeDaSubTeam],
				);

				$section['items'][$myRow->CoCode . '_' . $myRow->TeDaSubTeam] = $item;

				if ($myRow->TeDaDateTime > $section['meta']['lastUpdate']) {
					$section['meta']['lastUpdate'] = $myRow->TeDaDateTime;
				}
				if ($myRow->TeDaDateTime > $this->data['meta']['lastUpdate']) {
					$this->data['meta']['lastUpdate'] = $myRow->TeDaDateTime;
				}

			}

			// ultimo giro
			$this->data['sections'][$myEv] = $section;
		}
	}
}
