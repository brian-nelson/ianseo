<?php
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Rank/Obj_Rank_GridInd.php');

/**
 * Obj_Rank_GridInd_3_SetFRChampsD1DNAP
 *
 * Overrides only the phase names of the standard grid
 *
 */
	class Obj_Rank_GridInd_3_SetFRChampsD1DNAP extends Obj_Rank_GridInd {
		public function getQuery($OrderByTarget=false) {
			$filter=$this->safeFilter();

			$ExtraFilter=array('if(EvElimType=4, EnId>0 or OppEnId>0 or GrPhase<=4, true)');
			if(!empty($this->opts['enid'])) {
				$ExtraFilter[] = "(EnId=" . intval($this->opts['enid']) . " or OppEnId=" . intval($this->opts['enid']) . ")";
			}
			if(!empty($this->opts['coid'])) {
				$ExtraFilter[] = "(CountryId=" . intval($this->opts['coid']) . " or OppCountryId=" . intval($this->opts['coid']) . ") ";
			}
			if(isset($this->opts['matchno'])) {
				$ExtraFilter[] = "(MatchNo=" . intval($this->opts['matchno']) . ' or OppMatchNo =' . intval($this->opts['matchno']) . ')';
			}
			if(isset($this->opts['matchnoArray'])) {
				$ExtraFilter[] = "MatchNo in (" . implode(',', $this->opts['matchnoArray']) . ') ';
			}
			if(isset($this->opts['liveFlag'])) {
				$ExtraFilter[] = "LiveFlag=1";
			}
			if(isset($this->opts['noElim'])) {
				$ExtraFilter[] = "Phase<=FullPhase";
			}
			if($ExtraFilter) {
				$ExtraFilter = 'WHERE ' . implode(' AND ', $ExtraFilter);
			} else {
				$ExtraFilter = '';
			}

			/*
			 *  prima passata per costruire la struttura del vettore.
			 *  Tiro fuori le qualifiche, le posizioni finali e le eliminatorie (se ci sono)
			 */
			$q="SELECT"
				. " f1.*, f2.*,
					ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
					date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
					ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes "
				. "FROM (select"
				.(empty($this->opts['extended']) ? '' : " RevLanguage1 AS Review1, RevLanguage2 AS Review2, UNIX_TIMESTAMP(IFNULL(RevDateTime,0)) As ReviewUpdate, ")
				. " FinArrowPosition ArrowPosition, FinTiePosition TiePosition,"
				. " FinEvent Event,"
				. " GrPhase,"
				. " EvProgr,"
				. " EvEventName AS EventDescr,"
				. " EvMatchMode,"
				. " EvWinnerFinalRank, "
				. " EvFinalFirstPhase=EvNumQualified as NoRealPhase,"
				. " EvFinalFirstPhase, "
				. " EvFinalPrintHead, "
				. " 63 as Phase,"
				. " truncate((FinMatchNo-128)/16,0)+1 as GameNumber,"
				. " pow(2, ceil(log2(GrPhase))+1) & EvMatchArrowsNo!=0 as FinElimChooser,"
				. " greatest(PhId, PhLevel) as FullPhase,"
				. " EvShootOff,"
				. " EvCodeParent,"
				. " EvElimType,"
				. " EvNumQualified,"
				. " IF(EvFinalFirstPhase=48, GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition)) Position,"
				. " concat(FSScheduledDate,' ',FSScheduledTime) AS ScheduledKey, "
				. " DATE_FORMAT(FSScheduledDate,'" . get_text('DateFmtDB') . "') as ScheduledDate,"
				. " DATE_FORMAT(FSScheduledTime,'" . get_text('TimeFmt') . "') AS ScheduledTime, "
				. " FinTournament Tournament,"
				. " FinDateTime LastUpdated,"
				. " FinMatchNo MatchNo,"
				. " EnCode Bib,"
				. " ifnull(EdExtra,EnCode) LocalBib,"
				. " EnId, EnNameOrder NameOrder, EnSex Gender, EnDob BirthDate, "
				. " FsTarget Target,"
				. " TarId, TarDescr, EvDistance as Distance, EvTargetSize as TargetSize, "
				. " concat(upper(EnFirstName), ' ', EnName) Athlete,"
				. " EnFirstName FamilyName,"
				. " upper(EnFirstName) FamilyNameUpper,"
				. " EnName GivenName,"
				. " CoId CountryId,"
				. " CoCode CountryCode,"
				. " left(CoName, 20) ShortCountry,"
				. " CoName CountryName,"
				. " CoIocCode CountryIocCode,"
				. " IndRank QualRank,"
				. " IndRankFinal FinRank,"
				. " QuScore QualScore,"
				. " IndNotes QualNotes,"
				. "	EvFinEnds, EvFinArrows, EvFinSO, EvElimEnds, EvElimArrows, EvElimSO, "
				. " FinWinLose Winner,"
				. " FinScore Score,"
				. " FinSetScore SetScore,"
				. " FinSetPoints SetPoints,"
				. " FinSetPointsByEnd SetPointsByEnd,"
				. " FinTie AS Tie,"
				. " FinArrowstring ArrowString,"
				. " FinTiebreak TieBreak,"
				. " FinStatus Status, "
				. " FinConfirmed Confirmed, "
				. " FinLive LiveFlag, FinNotes Notes, FinShootFirst as ShootFirst, if(EvFinalFirstPhase%12=0, GrPosition2, GrPosition) as GridPosition "
				. "FROM "
				. " Finals "
				. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
				. "INNER JOIN Events ON FinEvent=EvCode AND FinTournament=EvTournament AND EvTeamEvent=0 "
				. "inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & 1)=1 "
				. "INNER JOIN Targets ON EvFinalTargetType=TarId "
				. "LEFT JOIN Individuals ON FinAthlete=IndId AND FinEvent=IndEvent AND FinTournament=IndTournament "
				. "LEFT JOIN Entries ON FinAthlete=EnId AND FinTournament=EnTournament "
				. "LEFT JOIN ExtraData ON EdId=EnId AND EdType='Z' "
				. "LEFT JOIN Qualifications ON QuId=EnId "
				. "LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
				. "LEFT JOIN FinSchedule ON FinEvent=FSEvent AND FinMatchNo=FSMatchNo AND FinTournament=FSTournament AND FSTeamEvent='0' "
				. (empty($this->opts['extended']) ? '' : "LEFT JOIN Reviews ON FinEvent=RevEvent AND FinMatchNo=RevMatchNo AND FinTournament=RevTournament AND RevTeamEvent=0 ")
				. "WHERE FinMatchNo%2=0 "
				. " AND FinTournament = " . $this->tournament . " " . $filter
				. ") f1 "
				. "INNER JOIN (select"
				.(empty($this->opts['extended']) ? '' : " RevLanguage1 AS OppReview1, RevLanguage2 AS OppReview2, UNIX_TIMESTAMP(IFNULL(RevDateTime,0)) As OppReviewUpdate, ")
				. " FinArrowPosition OppArrowPosition, FinTiePosition OppTiePosition,"
				. " FinEvent OppEvent,"
				. " FinTournament OppTournament,"
				. " FinDateTime OppLastUpdated,"
				. " FinMatchNo OppMatchNo,"
				. " EnCode OppBib,"
				. " ifnull(EdExtra,EnCode) OppLocalBib,"
				. " EnId OppEnId, EnNameOrder OppNameOrder, EnSex as OppGender, EnDob OppBirthDate,"
				. " FsTarget OppTarget,"
				. " concat(upper(EnFirstName), ' ', EnName) OppAthlete,"
				. " EnFirstName OppFamilyName,"
				. " upper(EnFirstName) OppFamilyNameUpper,"
				. " IF(EvFinalFirstPhase=48, GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition)) OppPosition,"
				. " EnName OppGivenName,"
				. " CoId OppCountryId,"
				. " CoCode OppCountryCode,"
				. " left(CoName, 20) OppShortCountry,"
				. " CoName OppCountryName,"
				. " CoIocCode OppCountryIocCode,"
				. " IndRank OppQualRank,"
				. " IndRankFinal OppFinRank,"
				. " QuScore OppQualScore,"
				. " IndNotes OppQualNotes,"
				. " FinWinLose OppWinner,"
				. " FinScore OppScore,"
				. " FinSetScore OppSetScore,"
				. " FinSetPoints OppSetPoints,"
				. " FinSetPointsByEnd OppSetPointsByEnd,"
				. " FinTie AS OppTie,"
				. " FinArrowstring OppArrowString,"
				. " FinTiebreak OppTieBreak, "
				. " FinConfirmed OppConfirmed, "
				. " FinStatus OppStatus, FinNotes OppNotes, FinShootFirst as OppShootFirst, if(EvFinalFirstPhase%12=0, GrPosition2, GrPosition) as OppGridPosition  "
				. "FROM "
				. " Finals "
				. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
				. "INNER JOIN Events ON FinEvent=EvCode AND FinTournament=EvTournament AND EvTeamEvent=0 "
				. "LEFT JOIN Individuals ON FinAthlete=IndId AND FinEvent=IndEvent AND FinTournament=IndTournament "
				. "LEFT JOIN Entries ON FinAthlete=EnId AND FinTournament=EnTournament "
				. "LEFT JOIN ExtraData ON EdId=EnId AND EdType='Z' "
				. "LEFT JOIN Qualifications ON QuId=EnId "
				. "LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
				. "LEFT JOIN FinSchedule ON FinEvent=FSEvent AND FinMatchNo=FSMatchNo AND FinTournament=FSTournament AND FSTeamEvent='0' "
				. (empty($this->opts['extended']) ? '' : "LEFT JOIN Reviews ON FinEvent=RevEvent AND FinMatchNo=RevMatchNo AND FinTournament=RevTournament AND RevTeamEvent=0 ")
				. "WHERE FinMatchNo%2=1 "
				. " AND FinTournament = " . $this->tournament . " " . $filter
				. ") f2 on Tournament=OppTournament and Event=OppEvent and MatchNo=OppMatchNo-1
					LEFT JOIN DocumentVersions DV1 on Tournament=DV1.DvTournament AND DV1.DvFile = 'B-IND' and DV1.DvEvent=''
					LEFT JOIN DocumentVersions DV2 on Tournament=DV2.DvTournament AND DV2.DvFile = 'B-IND' and DV2.DvEvent=Event "
				. " $ExtraFilter "
				. "ORDER BY ".($OrderByTarget ? 'Target, ' : '')."EvProgr ASC, Event, Phase DESC, MatchNo ASC ";

			return $q;
		}

	}
