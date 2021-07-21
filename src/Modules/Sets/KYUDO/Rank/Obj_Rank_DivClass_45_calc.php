<?php
require_once(dirname(__FILE__).'/Obj_Rank_DivClass_45.php');

    class Obj_Rank_DivClass_45_calc extends Obj_Rank_DivClass_45 {
		public function calculate() {
		    if($this->opts['dist']>0) {
		        return true;
            }
            $filter=$this->safeFilter();

            $q="
				SELECT
					EnTournament,EnId,EnCountry,CONCAT(EnDivision,EnClass) AS MyEvent,ToType,
					QuD1Score AS Score, QuD2Score as ShootOff
				FROM
					Entries
					INNER JOIN Tournament ON EnTournament=ToId
					INNER JOIN Qualifications ON EnId=QuId
				WHERE
					EnTournament={$this->tournament} AND
					EnAthlete=1 AND
					EnStatus <=1  AND
					EnIndClEvent='1' AND
					QuD1Score<>0
					{$filter}
				ORDER BY
					CONCAT(EnDivision,EnClass), QuD1Score DESC, QuD2Score DESC ";

            $r=safe_r_sql($q);

            $myEv='';

            $rank=1;
            $pos=0;

            $scoreOld=0;
            $ShootOffOld=0;

            while ($myRow=safe_fetch($r)) {
                if ($myRow->MyEvent!=$myEv) {
                    $rank=1;
                    $pos=0;

                    $scoreOld=0;
                    $ShootOffOld=0;
                }

                $pos++;

                if (!($myRow->Score==$scoreOld AND $myRow->ShootOff==$ShootOffOld)) {
                    $rank = $pos;
                }

                $date=date('Y-m-d H:i:s');

                $q = "UPDATE Qualifications SET QuD1Rank='', QuD2Rank='', QuClRank=" . StrSafe_DB($rank) . ", QuTimestamp='{$date}' WHERE QuId=" . $myRow->EnId;
                safe_w_sql($q);

                $myEv=$myRow->MyEvent;
                $scoreOld=$myRow->Score;
                $ShootOffOld=$myRow->ShootOff;
            }
            return true;
        }
	}