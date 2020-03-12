<?php
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
require_once('Common/Rank/Obj_Rank_GridTeam.php');

/**
 * Obj_Rank_GridTeam_3_SetFRChampsD1DNAP
 *
 * Overrides named phases!
 */

class Obj_Rank_GridTeam_3_SetFRChampsD1DNAP extends Obj_Rank_GridTeam{
	public function getQuery($OrderByTarget=false) {
		$filter=$this->safeFilter();

		$ExtraFilter=array();
		if(!empty($this->EnIdFound)) {
			$ExtraFilter[] = 'Event in ("'.implode('","', $this->EnIdFound).'") AND (Team='.StrSafe_DB($this->TeamFound).' or oppTeam='.StrSafe_DB($this->TeamFound).')';
		}
		if(!empty($this->opts['coid'])) {
			$ExtraFilter[] = "(Team=" . intval($this->opts['coid']) . " or OppTeam=" . intval($this->opts['coid']) . ") ";
		}
		if(isset($this->opts['matchno'])) {
			$ExtraFilter[] = "(MatchNo=" . intval($this->opts['matchno']) . ' or OppMatchNo =' . intval($this->opts['matchno']) . ')';
		}
		if(isset($this->opts['matchnoArray'])) {
			$ExtraFilter[] = "(MatchNo in (" . implode(',', $this->opts['matchnoArray']) . ')';
		}
		if(isset($this->opts['liveFlag'])) {
			$ExtraFilter[] = "LiveFlag=1";
		}
		if($ExtraFilter) {
			$ExtraFilter = 'WHERE ' . implode(' AND ', $ExtraFilter);
		} else {
			$ExtraFilter = '';
		}

		$SQL = "SELECT f1.*, f2.*,
					ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
					date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
					ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes from ("
			. "select"
			. " TfArrowPosition ArrowPosition, TfTiePosition TiePosition,"
			. " EvCode Event,"
			. " EvOdfCode OdfCode,"
			. " EvEventName EventDescr,"
			. " EvFinalFirstPhase, EvNumQualified, "
			. " EvMaxTeamPerson,"
			. " EvFinalPrintHead,"
			. " EvMatchMode,"
			. " EvWinnerFinalRank,"
			. " EvFinalFirstPhase=EvNumQualified as NoRealPhase,"
			. " EvProgr,"
			. " EvShootOff,"
			. " EvCodeParent,"
			. " 63 Phase,"
			. " truncate((FsMatchNo-128)/16,0)+1 as GameNumber,"
			. " pow(2, ceil(log2(GrPhase))+1) & EvMatchArrowsNo!=0 as FinElimChooser,"
			. " GrPosition Position,"
			. " GrPosition2 Position2,"
			. " TfTournament Tournament,"
			. " TfTeam Team,"
			. " TfSubTeam SubTeam,"
			. " TfMatchNo MatchNo,"
			. " TeRank QualRank,"
			. " TeRankFinal FinRank,"
			. " TeScore QualScore, "
			. " TeNotes QualNotes, "
			. " TfWinLose Winner, "
			. " TfDateTime LastUpdated, "
			. " CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as CountryName,"
			. " CoCode as CountryCode,"
			. " TfScore AS Score,"
			. " TfSetScore as SetScore,"
			. " TfTie Tie,"
			. " TfTieBreak TieBreak,"
			. " TfStatus Status, "
			. " TfConfirmed Confirmed, "
			. " TfSetPoints SetPoints, "
			. " TfSetPointsByEnd SetPointsByEnd, "
			. " TfArrowstring Arrowstring, TfLive LiveFlag,"
			. " FSTarget Target,"
			. " TfNotes Notes, TfShootFirst as ShootFirst, "
			. " TarId, TarDescr, EvDistance as Distance, EvTargetSize as TargetSize, "
			. "	EvFinEnds, EvFinArrows, EvFinSO, EvElimEnds, EvElimArrows, EvElimSO, "
			. " concat(FSScheduledDate,' ',FSScheduledTime) AS ScheduledKey, "
			. " DATE_FORMAT(FSScheduledDate,'" . get_text('DateFmtDB') . "') as ScheduledDate,"
			. " DATE_FORMAT(FSScheduledTime,'" . get_text('TimeFmt') . "') AS ScheduledTime, if(EvFinalFirstPhase%12=0, GrPosition2, GrPosition) as GridPosition  "
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
			. " TfArrowPosition OppArrowPosition, TfTiePosition OppTiePosition, "
			. " EvCode OppEvent,"
			. " GrPosition OppPosition,"
			. " GrPosition2 OppPosition2,"
			. " TfTournament OppTournament,"
			. " TfTeam OppTeam,"
			. " TfSubTeam OppSubTeam,"
			. " TfMatchNo OppMatchNo,"
			. " TeRank OppQualRank,"
			. " TeRankFinal OppFinRank,"
			. " TeScore OppQualScore, "
			. " TeNotes OppQualNotes, "
			. " TfWinLose OppWinner, "
			. " TfDateTime OppLastUpdated, "
			. " CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as OppCountryName,"
			. " CoCode as OppCountryCode,"
			. " TfScore AS OppScore,"
			. " TfSetScore as OppSetScore,"
			. " TfTie OppTie,"
			. " TfTieBreak OppTieBreak,"
			. " TfStatus OppStatus, "
			. " TfConfirmed OppConfirmed, "
			. " TfSetPoints OppSetPoints, "
			. " TfSetPointsByEnd OppSetPointsByEnd, "
			. " TfArrowstring OppArrowstring, "
			. " FSTarget OppTarget, "
			. " TfNotes OppNotes, TfShootFirst as OppShootFirst, if(EvFinalFirstPhase%12=0, GrPosition2, GrPosition) as OppGridPosition "
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
			. " $ExtraFilter "
			. " ORDER BY ".($OrderByTarget ? 'Target, ' : '')."EvProgr ASC, event, Phase DESC, MatchNo ASC ";
		return $SQL;
	}
}
