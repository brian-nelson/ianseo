<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('config.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Globals.inc.php');

function getAllowedTeam() {
    global $allowedTeam;
    $tmpSelect = "";
    if(count($allowedTeam)) {
        $tmpSelect .= "AND CoCode IN (";
        foreach($allowedTeam as $k=>$v) {
            $tmpSelect .= StrSafe_DB($v) . ",";
        }
        $tmpSelect = substr($tmpSelect,0,-1) .  ") ";
    }
    return $tmpSelect;
}

function getTeamList() {
    global $competitions;

    $tmpId=null;
    foreach ($competitions as $gara) {
        $tmpId[] = StrSafe_DB(getIdFromCode($gara));
    }

    $TeamList = null;
    $MyQuery = "SELECT DISTINCT CoCode AS NationCode, CoName AS Nation ";
        $MyQuery.= "FROM Countries ";
        $MyQuery.= "WHERE CoTournament IN (" . implode(",",$tmpId) . ") ";
        $MyQuery.= getAllowedTeam();
        $MyQuery.= "ORDER BY CoCode";

    $Rs=safe_r_sql($MyQuery);
    if($Rs)	{
        while($MyRow = safe_fetch($Rs))	{
            $TeamList[$MyRow->NationCode]=$MyRow->Nation;
        }
    }
    return $TeamList;
}

function getTeamsValue() {
    global $competitions;

    $TeamResults = null;

    foreach ($competitions as $key=>$gara) {
        $currentId = getIdFromCode($gara);
        $TeamResults[$key]= null;

        $MyQuery = "SELECT CoCode AS NationCode, SUM(TeScore) as TeScore ";
            $MyQuery.= "FROM Teams AS te ";
            $MyQuery.= "INNER JOIN Countries AS c ON te.TeCoId=c.CoId AND te.TeTournament=c.CoTournament ";
            $MyQuery.= "WHERE te.TeTournament = " . StrSafe_DB($currentId) . " AND te.TeFinEvent=1 ";
            $MyQuery.= getAllowedTeam();
            $MyQuery.= "GROUP BY CoCode ";
            $MyQuery.= "ORDER BY TeScore DESC, NationCode ";

        $Rs=safe_r_sql($MyQuery);
        if($Rs)	{
            while($MyRow = safe_fetch($Rs))	{
                $TeamResults[$key][$MyRow->NationCode] = $MyRow;
            }
        }
    }
    return $TeamResults;

}

function getIndMatchesValue() {
    global $competitions,$LastPhasePossibility,$BonusComplete;
    $myPhases=getPhaseArray();

    $IndResult=array();
    $cntEvent=0;

    foreach ($competitions as $key=>$gara) 	{
        $currentId = getIdFromCode($gara);
        $IndResult[$key]= array();


        //Genero la query che mi ritorna tutti gli eventi individuali
        $MyQuery = "SELECT concat(EnId,'-',EvCode) as CheckAthlete, IndRankFinal as `Rank`, CONCAT_WS(' ', EnFirstName, EnName) as Athlete, CoCode, EvCode, lastPhase, IF(EvCode LIKE '" . $BonusComplete[$cntEvent] . "',IFNULL(FinScore,0),0) as orderScore ";
        $MyQuery.= "FROM Individuals ";
        $MyQuery .= "INNER JOIN Events ON IndEvent=EvCode AND IndTournament=EvTournament AND EvTeamEvent=0 ";
        $MyQuery .= "INNER JOIN Entries ON IndId=EnId AND IndTournament=EnTournament ";
        $MyQuery .= "INNER JOIN Countries ON IF(EnCountry2=0, EnCountry, EnCountry2)=CoId AND EnTournament=CoTournament ";
        $MyQuery .= "LEFT JOIN (SELECT FinAthlete, FinEvent, FinTournament, MIN(GrPhase) as lastPhase FROM Finals INNER JOIN Grids ON FinMatchNo=GrMatchNo WHERE FinTournament=" . StrSafe_DB($currentId) . " GROUP BY FinAthlete, FinEvent, FinTournament) as sqy ON IndId=sqy.FinAthlete AND IndEvent=sqy.FinEvent AND IndTournament=sqy.FinTournament ";
        $MyQuery .= "LEFT JOIN Finals as f ON IndId=f.FinAthlete AND IndEvent=f.FinEvent AND IndTournament=f.FinTournament AND lastPhase=8 ";
        $MyQuery.= "WHERE IndTournament = " . StrSafe_DB($currentId) . " AND EnTeamFEvent=1 AND IndRankFinal<=16 ";
        $MyQuery.= getAllowedTeam();
        $MyQuery.= "ORDER BY EvCode, `Rank` ASC, orderScore DESC, Athlete ";
        $Rs=safe_r_sql($MyQuery);
        if (safe_num_rows($Rs)>0) {
            $tmpEvent = "-----";
            $tmpOldScore = 0;
            $curRank = 9;
            $posRank=8;
            $Athletes=array();
            while ($MyRow=safe_fetch($Rs)) {
                if(in_array($MyRow->CheckAthlete, $Athletes)) continue;
                $Athletes[]=$MyRow->CheckAthlete;
                if($tmpEvent != $MyRow->EvCode) {
                    $tmpOldScore = -1;
                    $curRank = 9;
                    $posRank=8;
                    $tmpEvent = $MyRow->EvCode;
                }
                if($MyRow->Rank==9) {
                    $posRank++;
                    if($tmpOldScore!=$MyRow->orderScore && $tmpOldScore>0)
                        $curRank=$posRank;
                    $MyRow->Rank = $curRank;
                    $tmpOldScore = $MyRow->orderScore;
                }

                $IndResult[$key][$MyRow->EvCode][]=array(($MyRow->Rank ? $MyRow->Rank : -1 * $LastPhasePossibility[$MyRow->lastPhase]),$MyRow->Athlete, $MyRow->CoCode);
            }
        }
        $cntEvent++;
    }
    return $IndResult;
}

function getTeamMatchesValue() {
    global $competitions,$LastPhasePossibility,$BonusComplete;
    $myPhases=getPhaseArray();

    $TeamResult=array();
    $cntEvent=0;

    foreach ($competitions as $key=>$gara) 	{
        $currentId = getIdFromCode($gara);
        $TeamResult[$key]= array();


        //Genero la query che mi ritorna tutti gli eventi individuali
        $MyQuery = "SELECT concat(TeCoId,'-',TeSubTeam,'-',EvCode) as CheckAthlete, TeScore, TeRankFinal as `Rank`, TeamNames as Athlete, CoCode, EvCode, lastPhase, IF(EvCode LIKE '" . $BonusComplete[$cntEvent] . "',IFNULL(TfScore,0),0) as orderScore ";
        $MyQuery.= "FROM Teams ";
        $MyQuery .= "INNER JOIN Events ON TeEvent=EvCode AND TeTournament=EvTournament AND EvTeamEvent=1 ";
        $MyQuery .= "INNER JOIN Countries ON TeCoId=CoId AND TeTournament=CoTournament ";
        $MyQuery .= "LEFT JOIN (SELECT TfcCoId, TfcSubTeam, TfcEvent, GROUP_CONCAT(EnFirstName order by TfcOrder separator ', ') as TeamNames FROM TeamFinComponent INNER JOIN Entries ON TfcId=EnId WHERE TfcTournament =" . StrSafe_DB($currentId) . " GROUP BY TfcCoId, TfcSubTeam, TfcEvent) as ath ON TeCoId=ath.TfcCoId AND TeSubTeam=ath.TfcSubTeam AND TeEvent=ath.TfcEvent ";
        $MyQuery .= "LEFT JOIN (SELECT TfTeam, TfSubTeam, TfEvent, MIN(GrPhase) as lastPhase FROM TeamFinals INNER JOIN Grids ON TfMatchNo=GrMatchNo WHERE TfTournament=" . StrSafe_DB($currentId) . " GROUP BY TfTeam, TfSubTeam, TfEvent) as sqy ON TeCoId=sqy.TfTeam AND TeSubTeam=sqy.TfSubTeam AND TeEvent=sqy.TfEvent ";
        $MyQuery .= "LEFT JOIN TeamFinals as f ON TeCoId=f.TfTeam AND TeSubTeam=f.TfSubTeam  AND TeEvent=f.TfEvent AND TeTournament=f.TfTournament AND lastPhase=4 ";
        $MyQuery.= "WHERE TeTournament = " . StrSafe_DB($currentId) . " AND TeFinEvent=1 AND TeRankFinal<=8 ";
        $MyQuery.= getAllowedTeam();
        $MyQuery.= "ORDER BY EvCode, `Rank` ASC, orderScore DESC, Athlete ";
        $Rs=safe_r_sql($MyQuery);
        if (safe_num_rows($Rs)>0) {
            $tmpEvent = "-----";
            $tmpOldScore = 0;
            $curRank = 9;
            $posRank=8;
            $Athletes=array();
            while ($MyRow=safe_fetch($Rs)) {
                if(in_array($MyRow->CheckAthlete, $Athletes)) continue;
                $Athletes[]=$MyRow->CheckAthlete;
                if($tmpEvent != $MyRow->EvCode) {
                    $tmpOldScore = -1;
                    $curRank = 9;
                    $posRank=8;
                    $tmpEvent = $MyRow->EvCode;
                }
                if($MyRow->Rank==9) {
                    $posRank++;
                    if($tmpOldScore!=$MyRow->orderScore && $tmpOldScore>0)
                        $curRank=$posRank;
                    $MyRow->Rank = $curRank;
                    $tmpOldScore = $MyRow->orderScore;
                }

                $TeamResult[$key][$MyRow->EvCode][]=array(($MyRow->Rank ? $MyRow->Rank : -1 * $LastPhasePossibility[$MyRow->lastPhase]),$MyRow->Athlete, $MyRow->CoCode, $MyRow->TeScore);
            }
        }
        $cntEvent++;
    }
    return $TeamResult;
}
