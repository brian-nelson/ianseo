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

			$orderBy= "TeEvent,TeScore DESC, TeGold DESC, TeXnine DESC ";

		// Prima parte: come la normale
			$q="
				SELECT
					ToId,TeCoId,TeSubTeam,TeEvent,
					TeScore, TeGold, TeXnine
				FROM
					Tournament
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
				WHERE
					ToId={$this->tournament} AND
					TeScore<>0
					{$filter}
				ORDER BY
					{$orderBy}
			";

			//print $q;exit;
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

						if (!($row->TeScore==$scoreOld && $row->TeGold==$goldOld && $row->TeXnine==$xNineOld))
						{
							$rank = $pos;
						}


						$date=date('Y-m-d H:i:s');

						$q="
							UPDATE
								Teams
							SET
								TeRank={$rank},
								TeTimeStamp='{$date}'
							WHERE
								TeTournament={$this->tournament} AND TeCoId={$row->TeCoId} AND TeSubTeam={$row->TeSubTeam} AND
								TeFinEvent=0 AND TeEvent='{$row->TeEvent}'

						";
						safe_w_sql($q);
					}


					$scoreOld = $row->TeScore;
					$goldOld = $row->TeGold;
					$xNineOld = $row->TeXnine;
				}

			}

		/*
		 * Seconda parte:
		 * dalla rank calcolata prima tiro fuori le prime tre posizioni per poter risolvere con la regola della tupla.
		 * Dato che non posso fare un order by sulla tupla (perchè se il migliore di uno è 987 e l'altro 1239, verrebbe fuori che
		 * 987 è prima di 1239 dato che la stringa 987 è prima di 1239) tiro fuori le righe e le sbatto in un array esplodendo
		 * le tuple in tre elementi del record così poi ordino da lì.
		 */
			$q="

				SELECT
					ToId,TeCoId,TeSubTeam,TeEvent,TeFinEvent,TeScore,TeHits,TeGold,TeXnine,TeRank,
					GROUP_CONCAT(CAST(QuScore AS CHAR(10)) ORDER BY QuScore DESC SEPARATOR '|') AS `_Tupla`
				FROM
					Tournament
					INNER JOIN Teams ON ToId=TeTournament AND TeFinEvent=0
				    inner join IrmTypes on IrmId=TeIrmType and IrmShowRank=1
					INNER JOIN
						TeamComponent
					ON TeCoId=TcCoId AND TeSubTeam=TcSubTeam AND TeEvent=TcEvent AND TeTournament=TcTournament AND TeFinEvent=TcFinEvent
					INNER JOIN
						Qualifications
					ON TcId=QuId
					INNER JOIN
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
				WHERE
					ToId={$this->tournament} AND
					TeRank<=3
					{$filter}
				GROUP BY
					ToId, TeCoId, TeSubTeam, TeEvent, TeScore, TeGold, TeXnine
				ORDER BY
					ToId,TeEvent,TeRank


			";
			//print $q;exit;

			$rows=array();
			$r=safe_r_sql($q);

			if ($r && safe_num_rows($r)>0)
			{
				while ($row=safe_fetch_assoc($r))
				{
					$tmp=array();
					foreach ($row as $k=>$v)
					{
						if (substr($k,0,1)!='_')
						{
							$tmp[$k]=$v;
						}
					}
				// esplodo
					$parts=explode('|',$row['_Tupla']);
					$tmp['p1']=$parts[0];
					$tmp['p2']=$parts[1];
					$tmp['p3']=$parts[2];

					$rows[]=$tmp;
				}
			}

		/*
		 * Terza parte:
		 * con array_multisort ordino la struttura precedente sui campi TeEvent ASC,TeRank ASC,p1 DESC,p2 DESC e p3 DESC
		 * in modo da avere i dati ordinati per applicare l'algoritmo standard pos/rank
		 */
			$ranks=array();
			$p1s=array();
			$p2s=array();
			$p3s=array();

			foreach ($rows as $k=>$row)
			{
				$ranks[$k]=$row['TeRank'];
				$p1s[$k]=$row['p1'];
				$p2s[$k]=$row['p2'];
				$p3s[$k]=$row['p3'];
			}

//			print '<pre>';
//			print_r($ranks);
//			print_r($p1s);
//			print_r($p2s);
//			print_r($p3s);
//			print '</pre>';

			array_multisort(
				$ranks,SORT_ASC,
				$p1s,SORT_DESC,
				$p2s,SORT_DESC,
				$p3s,SORT_DESC,
				$rows
			);

//			print '<pre>';
//			print_r($rows);
//			print '</pre>';

		// algoritmo rank/pos
			$rank=1;
			$pos=0;
			$oldRank=0;		// ocio che qui rank sarebbe lo score quindi questo sarebbe oldScore, NON confondersi con $rank che è quella normale
			$oldPs=array(-1,-1,-1);
			$curEvent='';

			foreach ($rows as $k=>$v)
			{
				if ($v['TeEvent']!=$curEvent)
				{
					$rank=1;
					$pos=0;
					$oldRank=0;
					$oldPs=array(-1,-1,-1);
					$curEvent='';

					$curEvent=$v['TeEvent'];
				}

				++$pos;

			/*
			 * Una volta che ho assegnato rank=4, tutti quelli dopo saranno rank=4.
			 * Il motivo è il seguente:
			 * qui sto lavorando solo sulle prime 3 posizioni e potrebbe succedere di avere a cavallo del bronzo dei parimeriti.
			 * Se per la tupla non ho parimeriti al terzo posto con chi è a cavallo, chi resta fuori dal podio è quarto pari
			 * perchè se no la sua rank l'avrebbe sistemata la prima parte e qui non sarebbero venuti fuori.
			 * Esempio: ho 5 primi quindi le prime posizioni saranno 1,1,1,1,1,6,....
			 * La query della seconda parte mi tira fuori tutti gli 1.
			 * Ho pari i primi 2 quindi otterrò 1,1,3,x,x. Le x saranno 3 se c'è il parimerito se no saranno 4 e otterrò giustamente alla fine
			 * 1,1,3,4,4,6,...
			 *
			 */
				if ($rank<4)
				{
					if (!($v['TeRank']==$oldRank && $v['p1']==$oldPs[0] && $v['p2']==$oldPs[1] && $v['p3']==$oldPs[2]))
					{
						$rank=$pos;
					}
				}

			// non tocco il timestamp perchè tengo buono quello calcolato prima
				$q="
					UPDATE
						Teams
					SET
						TeRank={$rank}
					WHERE
						TeTournament={$this->tournament} AND TeCoId={$v['TeCoId']} AND TeSubTeam={$v['TeSubTeam']} AND
						TeFinEvent=0 AND TeEvent='{$v['TeEvent']}'

				";
				$r=safe_w_sql($q);

				$oldRank=$v['TeRank'];
				$oldPs=array($v['p1'],$v['p2'],$v['p3']);
			}

			return true;
		}

	}