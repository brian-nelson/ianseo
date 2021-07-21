<?php
/**
 * Obj_Rank_DivClass
 * Implementa l'algoritmo di default per il calcolo della rank di qualifica di classe individuale.
 *
 * La tabella in cui vengono scritti i valori è la Qualifications.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		events	=> array(<ev_1>,<ev_2>,...,<ev_n>) || string,		[calculate/read]
 * 		divs	=> array(<div_1>,<div_2>,...,<div_n>) || string		[calculate/read]
 * 		cls		=> array(<cl_1>,<cl_2>,...,<cl_n>) || string		[calculate/read]
 * 		dist	=> #												[calculate]
 * 		runningDist	=> #											[read]
 * 		cutScore=> #												[read,non influisce su calculate]
 * 		cutRank => #												[read,non influisce su calculate]
 * 		session => #												[read,non influisce su calculate]
 * 		tournament => #												[calculate/read]
 * )
 *
 * con:
 * 	 events: l'array degli eventi dove in questo caso un evento è la concatenazione di div e cl oppure se scalare, una stringa usata in LIKE. Sovrascrive il filtro divs e cls
 *	 divs: l'array delle divisioni oppure se scalare, una stringa usata in LIKE.
 *	 cls:  l'array delle classi oppure se scalare, una stringa usata in LIKE.
 * 	 dist: la distanza con 0 per indicare la rank di qualifica totale
 * 	 runningDist: Restituisce la classifica dopo "X" distanze a non della distanza "x" (e rimuove le impostazioni di "dist" se presenti)
 *	 cutScore: punteggio (incluso) a cui tagliare. Se impostato durante una calculate() il metodo ignorerà l'opzione
 *	 cutRank: Posizione di classifica (inclusa) a cui tagliare. Se impostato durante una calculate() il metodo ignorerà l'opzione
 *	 session: Se impostato ritorna la classifica di quella sessione, con la rank globale. Chi chiama se vuole ricalcolerà la rank in quella sessione
 *	 tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *
 * $data ha la seguente forma
 *
 * array(
 * 		meta 		=> array(
 * 			title 		=> <titolo della classifica localizzato>
 * 			numDist		=> <numero distanze>, inizializzato solo se c'è almeno una sezione
 * 			double		=> <1 se gara doppia 0 altrimenti>, inizializzato solo se c'è almeno una sezione
 * 			lastUpdate => timestamp dell'ultima modifica (il max tra tutte le righe)
 *		),
 * 		sections 	=> array(
 * 			div_cl_1 => array(
 * 				meta => array(
 * 					event => <div_cl_1>, valore uguale alla chiave
 * 					descr => <descrizione evento localizzata>
 * 					fields(*1) => array(
 *						id 				=> <id della persona>
 *                      bib 			=> <codice della persona>
 *                      session 		=> <sessione>
 *                      target 			=> <piazzola>
 *                      athlete 		=> <cognome e nome>
 *                      familyname 		=> <cognome>
 *						givenname 		=> <nome>
 *						class			=> <classe gara>
 *						ageclass		=> <classe anagrafica>
 *						subclass 		=> <categoria>
 *                      countryCode 	=> <codice nazione>
 *                      countryName 	=> <nazione>
 *                      rank 			=> <rank in base alla distanza>
 *                      score 			=> <punti in base alla distanza>
 *                      gold 			=> <ori in base alla distanza>
 *                      xnine 			=> <xnine in base alla distanza>
 *                      hits			=> <frecce tirate (tutte se la distanza è zero oppure solo quelle della distanza passata)>
 *                      dist_1 			=> <rank|punti|ori|xnine della distanza 1>
 *                      dist_2 			=> <rank|punti|ori|xnine della distanza 2>
 *                      dist_3 			=> <rank|punti|ori|xnine della distanza 3>
 *                      dist_4 			=> <rank|punti|ori|xnine della distanza 4>
 *                      dist_5 			=> <rank|punti|ori|xnine della distanza 5>
 *                      dist_6 			=> <rank|punti|ori|xnine della distanza 6>
 *                      dist_7	 		=> <rank|punti|ori|xnine della distanza 7>
 *                      dist_8 			=> <rank|punti|ori|xnine della distanza 8>
 * 					)
 *				)
 * 				items => array(
 * 					array(id=><valore>,bib=><valore>,...,dist_8=><valore>),
 * 					...
 * 				)
 * 			)
 * 			...
 * 			div_cl_n = ...
 * 		)
 * )
 *
 * (*1) i campi contengono la localizzazione per l'etichetta di quel campo
 *
 * Estende Obj_Rank
 */
	class Obj_Rank_DivClass_calc extends Obj_Rank_DivClass
	{
	/**
	 * calculate
	 *
	 * @override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#calculate()
	 */
		public function calculate()
		{
			$dd = ($this->opts['dist']>0 ? 'D' . $this->opts['dist'] : '');

//			$f=$this->safeFilter();
//			$filter=($f!==false ? $f : "");

			$filter=$this->safeFilter();

			$orderBy="CONCAT(EnDivision,EnClass), Qu{$dd}Score DESC,Qu{$dd}Gold DESC, Qu{$dd}Xnine DESC ";

			$q="
				SELECT
					EnTournament,EnId,EnCountry,CONCAT(EnDivision,EnClass) AS MyEvent,ToType,
					Qu{$dd}Score AS Score,Qu{$dd}Gold AS Gold ,Qu{$dd}Xnine AS XNine, Qu{$dd}Hits AS Hits
				FROM Entries
				inner JOIN Tournament ON EnTournament=ToId
				INNER JOIN Qualifications ON EnId=QuId
			    inner join IrmTypes on IrmId=QuIrmType and IrmShowRank=1
				WHERE
					EnTournament={$this->tournament} AND
					EnAthlete=1 AND
					EnStatus <=1  AND
					EnIndClEvent='1' AND
					(Qu{$dd}Score>0 or Qu{$dd}Hits>0) 
					{$filter}
				ORDER BY
					{$orderBy}
			";
			//print $q;exit;
			$r=safe_r_sql($q);

			$myEv='';

			$rank=1;
			$pos=0;

			$scoreOld=0;
			$goldOld=0;
			$xNineOld=0;

			while ($myRow=safe_fetch($r)) {
				if ($myRow->MyEvent!=$myEv) {
					$rank=1;
					$pos=0;

					$scoreOld=0;
					$goldOld=0;
					$xNineOld=0;
				}

				++$pos;

				if (!($myRow->Score==$scoreOld && $myRow->Gold==$goldOld  && $myRow->XNine==$xNineOld))
					$rank = $pos;


				$date=date('Y-m-d H:i:s');

				$q 	= "UPDATE Qualifications "
					. "SET Qu" . ($dd=='' ? 'Cl' : '') . $dd . "Rank=" . StrSafe_DB($rank) . ", "
					. "QuTimestamp='{$date}' "
					. "WHERE QuId=" . $myRow->EnId . " "
				;
				//print $q.'<br><br>';
				safe_w_sql($q);

				if(empty($dd) and $myRow->Hits%3 == 0) {
					$q = "INSERT INTO QualOldPositions (QopId, QopHits, QopClRank) "
						. "VALUES(" . $myRow->EnId . "," . $myRow->Hits . "," . $rank . ") "
						. "ON DUPLICATE KEY UPDATE QopClRank=" . $rank;
					safe_w_sql($q);
					$q = "DELETE FROM QualOldPositions WHERE QopId=" . $myRow->EnId . " AND QopHits>" . $myRow->Hits;
					safe_w_sql($q);
				}

				$myEv=$myRow->MyEvent;
				$scoreOld=$myRow->Score;
				$goldOld=$myRow->Gold;
				$xNineOld=$myRow->XNine;
			}

			if(!$dd) $this->calculateSubClass();
			return true;
		}

		function calculateSubClass() {
			$filter=$this->safeFilter();

			$orderBy="CONCAT(EnDivision,EnClass,EnSubClass), QuScore DESC,QuGold DESC, QuXnine DESC ";

			$q="
				SELECT
					EnTournament,EnId,EnSubClass,EnCountry,CONCAT(EnDivision,EnClass,EnSubClass) AS MyEvent,ToType,
					QuScore AS Score,QuGold AS Gold ,QuXnine AS XNine, QuHits as Hits
				FROM
					Entries
					LEFT JOIN
						Tournament
					ON EnTournament=ToId
					INNER JOIN
						Qualifications
					ON EnId=QuId
				WHERE
					EnTournament={$this->tournament} AND
					EnAthlete=1 AND
					EnStatus <=1  AND
					EnIndClEvent='1' AND
					EnSubClass!='' AND
					QuScore<>0
					{$filter}
				ORDER BY
					{$orderBy}
			";
			//print $q;exit;
			$r=safe_r_sql($q);

			$myEv='';

			$rank=1;
			$pos=0;

			$scoreOld=0;
			$goldOld=0;
			$xNineOld=0;

			while ($myRow=safe_fetch($r))
			{
				if ($myRow->MyEvent!=$myEv)
				{
					$rank=1;
					$pos=0;

					$scoreOld=0;
					$goldOld=0;
					$xNineOld=0;
				}

				++$pos;

				if (!($myRow->Score==$scoreOld && $myRow->Gold==$goldOld  && $myRow->XNine==$xNineOld))
				{
					$rank = $pos;
				}

				$date=date('Y-m-d H:i:s');

				$q
					= "UPDATE Qualifications "
					. "SET "
						. "QuSubClassRank=" . StrSafe_DB($rank) . ", "
						. "QuTimestamp='{$date}' "
					. "WHERE "
						. "QuId=" . $myRow->EnId . " "
				;

				//print $q.'<br><br>';
				safe_w_sql($q);

				if($myRow->Hits%3 == 0) {
					$q = "UPDATE QualOldPositions "
						. "SET QopSubClassRank=" . $rank . " "
						. "WHERE QopId=" . $myRow->EnId . " AND QopHits=" . $myRow->Hits;
					safe_w_sql($q);
				}
				$myEv=$myRow->MyEvent;
				$scoreOld=$myRow->Score;
				$goldOld=$myRow->Gold;
				$xNineOld=$myRow->XNine;
			}

			return true;
		}
	}
