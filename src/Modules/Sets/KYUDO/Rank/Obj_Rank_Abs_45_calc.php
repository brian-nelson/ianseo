<?php
require_once(dirname(__FILE__).'/Obj_Rank_Abs_45.php');

class Obj_Rank_Abs_45_calc extends Obj_Rank_Abs_45 {
    public function calculate() {
        if($this->opts['dist']>0) {
            return true;
        }

        // assign rank=0 if distance or total points=0, to prevent ranking to be retained when making tests
        safe_w_sql("update Individuals
                inner join Qualifications ON IndId=QuId
                inner join IrmTypes on IrmId=IndIrmType
                set IndD1Rank=0, IndD2Rank=0, IndRank=if(QuScore=0, 0, IndRank), IndRankFinal=if(QuScore=0, 0, IndRankFinal), IndSO=if((QuScore=0 and QuHits=0) or IrmShowRank=0, 0, IndSO)
                where IndTournament={$this->tournament}");

        $filter=$this->safeFilter();

        $q="
            SELECT
                IndId AS `athId`,IndEvent AS `EventCode`,EnDivision,
                QuD1Score AS Score, QuD2Score as ShootOff, IndRank as actualRank,
                EvFinalFirstPhase, EvElim1, EvElim2,
                IF((EvFinalFirstPhase=0 && EvElim1=0 && EvElim2=0),99999,IF(EvElim1=0 && EvElim2=0, EvNumQualified ,IF(EvElim1=0,EvElim2,EvElim1))) as QualifiedNo
            FROM Events
            INNER JOIN Individuals ON EvCode=IndEvent AND EvTournament=IndTournament AND EvTeamEvent=0
            INNER JOIN Qualifications ON IndId=QuId
            INNER JOIN Entries ON EnId=QuId
            inner join IrmTypes on IrmId=IndIrmType
            WHERE
                IndTournament={$this->tournament}
                 AND (QuScore != 0 OR QuHits !=0) 
                {$filter}
            ORDER BY
                IndEvent,QuD1Score DESC, QuD2Score DESC";

        $r=safe_r_sql($q);
        if (safe_num_rows($r)>0) {
            $curGroup = "";
            $myRank = 1;
            $myPos = 0;

            $myScoreOld = 0;
            $myShootoffOld = 0;
            $mySoScore=array();
            $currentRow=-1;

            while($myRow=safe_fetch($r)) {
                $currentRow++;

                if ($curGroup != $myRow->EventCode) {
                    $curGroup = $myRow->EventCode;

                    $myRank = 1;
                    $myPos = 0;
                    $myScoreOld = 0;
                    $mySoScore=array();
                    $myGroupStartPos = $currentRow;


                    /* Load last score for cut */
                    if(safe_num_rows($r) > ($myGroupStartPos + $myRow->QualifiedNo)) {
                        safe_data_seek($r,$myGroupStartPos + $myRow->QualifiedNo -1);
                        $tmpMyRow = safe_fetch($r);
                        if($curGroup == $tmpMyRow->EventCode) {
                            $tmpScore = $tmpMyRow->Score;
                            $tmpMyRow = safe_fetch($r);
                            //check if there is a tie situation
                            if ($tmpScore == $tmpMyRow->Score and $curGroup == $tmpMyRow->EventCode) {
                                $mySoScore[] = $tmpScore;
                            }
                        }
                        $tmpMyRow = NULL;
                    }
                    safe_data_seek($r,$myGroupStartPos+1);
                }
                $myPos++;

                $so=-1;

                // If not tie --> rank is the same as position

                if(in_array($myRow->Score,$mySoScore)) {
                    // specic case, there is a tie due to the way $mySoScore is prepared
                    if ($myRow->Score!=$myScoreOld) {
                        $myRank = $myPos;
                    }
                    $so=1;	//Shoot off
                } else {
                    //All Other tie situation
                    if(!($myRow->Score==$myScoreOld AND $myRow->ShootOff==$myShootoffOld)) {
                        $myRank = $myPos;
                    }
                }
                if($myRank>$myRow->QualifiedNo) {
                    $so = 0;
                }

                $myScoreOld = $myRow->Score;
                $myShootoffOld = $myRow->ShootOff;

                $x = false;
                if($this->opts['dist']==0 and $myRow->actualRank!=0 and array_key_exists('skipExisting',$this->opts) and $this->opts['skipExisting']==1) {
                    $x=$this->setRow(array(
                        array(	// passo 1 item alla volta
                            'ath' 		=> $myRow->athId,
                            'event'		=> $myRow->EventCode,
                            'dist'		=> $this->opts['dist'],
                            'so'		=> ($so * $myRank)
                        )
                    ));
                } else {
                    $x=$this->setRow(array(
                        array(	// passo 1 item alla volta
                            'ath' 		=> $myRow->athId,
                            'event'		=> $myRow->EventCode,
                            'dist'		=> $this->opts['dist'],
                            'rank'		=> $myRank,
                            'tiebreak'	=> '',
                            'decoded'	=> '',
                            'closest'   => 0,
                            'so'		=> ($so * $myRank)
                        )
                    ));
                }

                if ($x===false) {
                    return false;
                }
            }
            $q=safe_r_SQL("SELECT IndEvent, IndSO FROM Individuals WHERE IndTournament={$this->tournament} GROUP BY IndEvent, IndSO Having COUNT(*)>1");
            while($r=safe_fetch($q)) {
                safe_w_sql("UPDATE Individuals SET IndSO=ABS(IndSO) WHERE IndTournament={$this->tournament} AND IndEvent='{$r->IndEvent}' AND IndSO={$r->IndSO}");
            }
        }
        return true;
    }

    public function setRow($items=array())
    {
        // campi mandatory per $item
        $params=array('ath','event','dist');

        $affected=0;

        foreach ($items as $item)
        {
            /*print '<pre>';
            print_r($item);
            print '</pre>';*/

            $paramsOk=true;

            $canUp=false;

            /*
             *  controllo che ci siano i campi mandatory
             */
            foreach ($params as $p) {
                if (!array_key_exists($p,$item)) {
                    $paramsOk=false;
                    $ret=false;
                    break;
                }
            }

            if (!$paramsOk) continue;

            $dd = ($item['dist'] ? 'D' . $item['dist'] : '');

            $date=date('Y-m-d H:i:s');

            $q
                = "UPDATE "
                . "Individuals "
                . "SET "
                . "IndTimestamp='{$date}' "
            ;

            /* campi opzionali e basta */
            if (array_key_exists('rank',$item))
            {
                $canUp=true;
                $q.=",Ind{$dd}Rank={$item['rank']}";
            }

            /*
             *  campi opzionali (se dist==0).
             *  In ogni caso i valori vengono scritti se e solo se la rank nuova è diversa dalla vecchia!
             */
            if ($item['dist']==0) {
                if (array_key_exists('tiebreak',$item)) {
                    $canUp=true;
                    $q.=",IndTiebreak='{$item['tiebreak']}'";
                }

                if (array_key_exists('decoded',$item)) {
                    $canUp=true;
                    $q.=",IndTbDecoded='{$item['decoded']}'";
                }

                if (array_key_exists('closest',$item)) {
                    $canUp=true;
                    $q.=",IndTbClosest='{$item['closest']}'";
                }


                if (array_key_exists('so',$item)) {
                    $canUp=true;
                    $q.=",IndSO={$item['so']}";
                }
            }

            $q
                .=" WHERE "
                . "IndId=" . $item['ath'] . " AND IndEvent='" . $item['event'] . "' AND IndTournament=" . $this->tournament . " ";
            ;

            if (!$canUp) {
                return false;
            }
            $r=safe_w_sql($q);

            if (!$r) {
                $affected=false;
            } else {
                $affected+=safe_w_affected_rows();
            }

        }

        return $affected;
    }
}
