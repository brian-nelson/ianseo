<?php
/**
 * Obj_Rank_DivClassTeam
 *
 * Implementa l'algoritmo di default per il calcolo della rank di qualifica di classe a squadre.
 *
 * La tabella in cui vengono scritti i valori è la Teams.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		events	=> array(<ev_1>,<ev_2>,...,<ev_n>) || string,		[calculate/read]
 * 		divs	=> array(<div_1>,<div_2>,...,<div_n>) || string		[calculate/read]
 * 		cls		=> array(<cl_1>,<cl_2>,...,<cl_n>) || string		[calculate/read]
 * 		cutScore=> #												[read,non influisce su calculate]
 * 		cutRank => #												[read,non influisce su calculate]
 * 		components => bool											[read, non influisce su calculate]
 * 		tournament => #												[calculate/read]
 * )
 *
 * con:
 * 	 events: l'array degli eventi dove in questo caso un evento è la concatenazione di div e cl oppure se scalare, una stringa usata in LIKE. Sovrascrieve divs e cls
 *	 divs: l'array delle divisioni oppure se scalare, una stringa usata in LIKE.
 *	 cls:  l'array delle classi oppure se scalare, una stringa usata in LIKE.
 *	 cutScore: punteggio (incluso) a cui tagliare. Se impostato durante una calculate() il metodo ignorerà l'opzione
 *	 cutRank: Posizione di classifica (inclusa) a cui tagliare. Se impostato durante una calculate() il metodo ignorerà l'opzione
 *   components: se impostato a false non ritorna i nomi dei componenti altrimenti sì
 *	 tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *
 *
 * $data ha la seguente forma
 *
 * array(
 * 		meta 		=> array(
 * 			title 		=> <titolo della classifica localizzato>
 * 			lastUpdate  => timestamp dell'ultima modifica (il max tra tutte le righe)
 *		),
 * 		sections 	=> array(
 * 			div_cl_1 => array(
 * 				meta => array(
 * 					event => <div_cl_1>, valore uguale alla chiave
 * 					descr => <descrizione evento localizzata>
 * 					fields(*1) => array(
 *						id 				=> <id della squadra>
 *                      countryCode 	=> <codice nazione>
 *                      countryName 	=> <nazione>
 *                      subteam 		=> <subteam>
 *                      athletes 		=> array(
 *                      	name		=> <nome>
 *                      	fields 		=> array(
 * 								id    => <id della persona>
 *								bib => <matricola della persona>
 *								athlete => <cognome e nome della persona>,
 *								familyname => <cognome>
 *								givenname => <nome>
 *								div => <divisione>
 *								class => <classe>
 *								ageclass => <classe anagrafica>
 *								subclass => <subclass>
 *								quscore => <score di qualifica>
 *							)
 *                      )
 *                      rank 			=> <rank>
 *                      score 			=> <punti>
 *                      gold 			=> <ori>
 *                      xnine 			=> <xnine>
 *                      hits			=> <frecce tirate>
 * 					)
 *				)
 * 				items => array(
 * 					array(
 * 						id=><valore>,countryCode=><valore>,
 * 						athletes=>array(
 *                      	array(id=><valore>,bib=><valore>,...,subclass=><valore>),
 *                      	)
 *
 *                      )
 * 						...,
 * 						hits=><valore>
 * 					),
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
	class Obj_Rank_DivClassTeam_calc extends Obj_Rank_DivClassTeam
	{
	/**
	 * calculate()
	 *
	 * @Override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#calculate()
	 */
		public function calculate()
		{
			$filter=$this->safeFilter();

			$orderBy= "TeEvent, TeScore DESC, TeGold DESC, TeXnine DESC, TeSubTeam ";

			$q="
				SELECT
					ToId,TeCoId,TeSubTeam,TeEvent,
					TeScore, TeGold, TeXnine
				FROM Tournament
				INNER JOIN Teams ON ToId=TeTournament AND TeFinEvent=0
			    inner join IrmTypes on IrmId=TeIrmType and IrmShowRank=1
				left JOIN
						(
							SELECT CONCAT(DivId, ClId) DivClass, Divisions.*, Classes.*
							FROM
								Divisions
									INNER JOIN Classes
								ON DivTournament=ClTournament
							WHERE
								DivAthlete and ClAthlete
						) AS DivClass
					ON TeEvent=DivClass AND TeTournament=DivTournament
				WHERE ToId={$this->tournament} AND TeScore<>0
					{$filter}
				ORDER BY
					{$orderBy}
			";
			$r=safe_r_sql($q);

			if (!$r)
				return false;

			$myEv='';
			$myTeam='';

			$rank=1;
			$pos=0;

			$scoreOld=0;
			$goldOld=0;
			$xNineOld=0;

			if (safe_num_rows($r)>0)
			{
				while ($row=safe_fetch($r))
				{
					if ($myEv!=$row->TeEvent)
					{
						$myEv=$row->TeEvent;

						$rank = 1;
						$pos = 0;
						$scoreOld = 0;
						$goldOld = 0;
						$xNineOld = 0;
						$myTeam = '';
					}

					if ($myTeam!=$row->TeCoId)
					{
						$myTeam=$row->TeCoId;

						++$pos;

					// Se non ho parimerito il ranking è uguale alla posizione
						if (!($row->TeScore==$scoreOld && $row->TeGold==$goldOld && $row->TeXnine==$xNineOld))
							$rank = $pos;

						$date=date('Y-m-d H:i:s');

						$q="
							UPDATE
								Teams
							SET
								TeRank={$rank},
								TeTimeStamp='{$date}'
							WHERE
								TeTournament={$this->tournament} AND TeCoId={$row->TeCoId} ANd TeSubTeam={$row->TeSubTeam} AND
								TeFinEvent=0 AND TeEvent='{$row->TeEvent}'

						";
						safe_w_sql($q);
					}


					$scoreOld = $row->TeScore;
					$goldOld = $row->TeGold;
					$xNineOld = $row->TeXnine;
				}

			}

			return true;
		}

	}