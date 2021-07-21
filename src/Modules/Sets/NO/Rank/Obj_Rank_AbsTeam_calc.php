<?php
/**
 * Obj_Rank_AbsTeam
 * Implementa l'algoritmo di default per il calcolo della rank di qualificazione assoluta a squadre
 *
 * La tabella in cui vengono scritti i valori è la Teams.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		events	=> array(<ev_1>,<ev_2>...<ev_n>) || string,			[calculate/read]
 * 		tournament => #												[calculate/read]
 * 		cutRank => #												[read]
 * 		skipExisting => #											[calculate]
 * 		components => #												[read]
 *
 * )
 *
 * con:
 * 	 events: l'array degli eventi assoluti oppure se scalare, una stringa usata in LIKE
 *	 tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *	 skipExisting: Se 1 non sovrascrive posizione e frecce di SO dove sono già valorizzati - Solo per Distanza = 0
 *   components: se impostato a false non ritorna i nomi dei componenti altrimenti sì
 *
 * $data ha la seguente forma
 *
 * array(
 * 		meta 		=> array(
 * 			title 		=> <titolo della classifica localizzato>
 * 			lastUpdate  => timestamp dell'ultima modifica (il max tra tutte le righe)
 *		),
 * 		sections 	=> array(
 * 			event_1 => array(
 * 				meta => array(
 * 					event => <event_1>, valore uguale alla chiave
 * 					descr => <descrizione evento localizzata>
 * 					printHeader => <testa stampa>
 * 					qualifiedNo => <numero di squadre qualificate per l'evento>
 * 					fields(*1) => array(
 *						id 				=> <id della squadra>
 *                      countryCode 	=> <codice nazione>
 *                      countryName 	=> <nazione>
 *                      subteam 		=> <subteam>
 						athletes 		=> array(
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
 *                      tiebreak		=> <frecce di tie>					(distanza 0)
 *                      ct				=> <numero di cointoss (gialli)>	(distanza 0)
 *                      so				=> <1 se shootoff (rosso)>			(distanza 0)
 * 					)
 *				)
 * 				items => array(
 * 					array(
 * 						id=><valore>,
 * 						countryCode=><valore>,
 * 						athletes=>array(
 *                      	array(id=><valore>,bib=><valore>,...,subclass=><valore>),
 *
 *                      )
 * 						...,
 * 						so=><valore>
 * 					),
 * 					...
 * 				)
 * 			)
 * 			...
 * 			event_n = ...
 * 		)
 * )
 */
	class Obj_Rank_AbsTeam_calc extends Obj_Rank_AbsTeam
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
			$f=$this->safeFilter();
			$filter=($f!==false ? $f : "");

			$orderBy="TeEvent, TeScore DESC, TeGold DESC, TeXnine DESC, CoCode, TeSubTeam ";

			$q="
				SELECT
					TeTournament,TeCoId,TeSubTeam,CoCode,TeEvent,
					TeScore, TeGold, TeXnine,
					IF(EvFinalFirstPhase=0,99999,EvNumQualified) AS QualifiedNo,
					TeRank AS ActualRank
				 FROM
				 	Teams
				 	INNER JOIN
				 		Countries
				 	ON TeCoId=CoId AND CoTournament=TeTournament
				 	INNER JOIN
				 		Events
				 	ON TeEvent=EvCode AND TeTournament=EvTournament AND EvTeamEvent=1
				 WHERE
				 	TeTournament={$this->tournament} AND TeFinEvent=1 AND TeScore<>'0' and TeIrmTypeFinal<=10
				 	{$filter}
				 ORDER BY
				 	{$orderBy}
			";
			//print $q;exit;

			$r=safe_r_sql($q);

			if (!$r)
				return false;

			if (safe_num_rows($r)>0)
			{
				$curGroup = "";
				$myRank = 1;
				$myPos = 0;
				$endQualified = false;

				$myScoreOld = 0;
				$myGoldOld = 0;
				$myXNineOld = 0;
				$myEndScore=-1;
				$myGroupStartPos=0;
				$currentRow=-1;

				while($myRow=safe_fetch($r))
				{
					++$currentRow;

					if ($curGroup != $myRow->TeEvent)
					{
						$curGroup = $myRow->TeEvent;

						$myRank = 1;
						$myPos = 0;
						$myScoreOld = 0;
						$myGoldOld = 0;
						$myXNineOld = 0;
						$endQualified = false;
						$myGroupStartPos = $currentRow;

					/*
					 * Carico l'ultimo punteggio per entrare.
					 * Vado a brancare la riga con l'ultimo Score buono
					 */
						if(safe_num_rows($r) > ($myGroupStartPos + $myRow->QualifiedNo))
						{
							safe_data_seek($r,$myGroupStartPos + $myRow->QualifiedNo -1);
							$tmpMyRow = safe_fetch($r);
							if($curGroup == $tmpMyRow->TeEvent)
							{
								$myEndScore = $tmpMyRow->TeScore;
								$tmpMyRow = safe_fetch($r);
								//Controllo se c'è parimerito per entrare
								if ($myEndScore != $tmpMyRow->TeScore || $curGroup != $tmpMyRow->TeEvent)
								{
									$myEndScore *= -1;
								}
							}
							else
								$myEndScore = -1;

							$tmpMyRow = NULL;
						}
						else
						{
							safe_data_seek($r,safe_num_rows($r)-1);
							$tmpMyRow = safe_fetch($r);
							$myEndScore = -1;
						}
						safe_data_seek($r,$myGroupStartPos+1);
					}
					++$myPos;

					$so=-1;

				// Se non ho parimerito il ranking è uguale alla posizione
					if($myEndScore == $myRow->TeScore)  //so che c'è uno spareggio per come ho caricato $myEndScore
					{
						if ($myRow->TeScore!=$myScoreOld)
							$myRank = $myPos;

						$so=1;	// rosso

					}
					else	//tutti gli altri pareggi...
					{
						if (!($myRow->TeScore==$myScoreOld && $myRow->TeGold==$myGoldOld && $myRow->TeXnine==$myXNineOld)) {
							$myRank = $myPos;
						}
					}

					if($myRank>$myRow->QualifiedNo)
						$so=0;

					$myScoreOld = $myRow->TeScore;
					$myGoldOld = $myRow->TeGold;
					$myXNineOld = $myRow->TeXnine;

					$x = false;
					if($myRow->ActualRank!=0 && array_key_exists('skipExisting',$this->opts) && $this->opts['skipExisting']==1)
					{
						$x=$this->setRow(array(
							array(	// passo 1 item alla volta
								'team' 		=> $myRow->TeCoId,
								'subteam' 	=> $myRow->TeSubTeam,
								'event'		=> $myRow->TeEvent,
								'so'		=> ($so * $myRank)
							)
						));
					}
					else
					{
						$x=$this->setRow(array(
							array(	// passo 1 item alla volta
								'team' 		=> $myRow->TeCoId,
								'subteam' 	=> $myRow->TeSubTeam,
								'event'		=> $myRow->TeEvent,
								'so'		=> ($so * $myRank),
								'rank'		=> $myRank,
								'tiebreak'	=> '',
								'decoded'	=> '',
                                'closest'   => 0
							)
						));
					}

					//print '..'.$x.'<br>';
					if ($x===false)
						return false;
				}
			}

		/*
		 * Adesso faccio la stessa cosa che o fatto per la classifica DivClass a partire dalla parte due
		 * limitandomi agli eventi con fase iniziale a zero
		 */
			$q="
				SELECT
					TeTournament,TeCoId,TeSubTeam,TeEvent,
					TeScore, TeGold, TeXnine,
					TeRank,
					GROUP_CONCAT(CAST(QuScore AS CHAR(10)) ORDER BY QuScore DESC SEPARATOR '|') AS `_Tupla`
				 FROM
				 	Teams
				 	INNER JOIN
				 		Countries
				 	ON TeCoId=CoId AND CoTournament=TeTournament
				 	INNER JOIN
				 		Events
				 	ON TeEvent=EvCode AND TeTournament=EvTournament AND EvTeamEvent=1 AND TeFinEvent=1 AND EvFinalFirstPhase=0
				 	INNER JOIN
						TeamComponent AS tc
					ON Teams.TeCoId=tc.TcCoId AND Teams.TeSubTeam=tc.TcSubTeam AND  Teams.TeEvent=tc.TcEvent AND Teams.TeTournament=tc.TcTournament AND Teams.TeFinEvent=tc.TcFinEvent
					INNER JOIN
						Entries
					ON TcId=EnId
					INNER JOIN
						Qualifications
					ON EnId=QuId
				 WHERE
				 	TeTournament={$this->tournament} AND TeRank<=3
				 	{$filter}
				 GROUP BY
					TeTournament, TeCoId, TeSubTeam, TeEvent, TeScore, TeGold, TeXnine
				 ORDER BY
				 	{$orderBy}
			";

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
						TeFinEvent=1 AND TeEvent='{$v['TeEvent']}'

				";
				$r=safe_w_sql($q);

				$oldRank=$v['TeRank'];
				$oldPs=array($v['p1'],$v['p2'],$v['p3']);
			}

			return true;
		}

	/**
	 * setRow().
	 * Imposta le IndRank degli elementi passati.
	 *
	 * Questo è il metodo da chiamare quando si risolvono gli spareggi perchè chi non passa ha la rank a posto
	 * grazie a calculate() e gli altri (sia quelli a cavallo che i buoni di sicuro) vanno impostati a mano.
	 *
	 *
	 * @param mixed $items: array degli elementi da scrivere.
	 * 		La struttra è la seguente:
	 * 			array(
	 * 				array(
	 * 					team 		=> <id>		 (chiave)
	 * 					subteam		=> <subteam> (chiave)
	 * 					event 		=> <ev>		 (chiave)
	 * 					rank 		=> <rank>
 	 * 					tiebreak 	=> <arrowstring>
	 * 					so 			=> <so>
	 * 				)
	 * 			)
	 *		con <id> l'id della squadra,<subteam> il subteam, <ev> l'evento, <arrowstring> l'arrowstring delle frecce di tie (opzionale),
	 *		<rank> la rank da impostare (opzionale), e <so> prima degli spareggi vale come la rank se non ci sono spareggi; 0 per chi non passa e negativo come la rank in caso di gialli (opzionale).
	 *		L'arrowstring, e l'so comunque sono considerati solo se <dist>==0
	 *
	 *		Deve essere presente almeno un campo opzionale se no il metodo ritorna errore.
	 *
	 *
	 * @return mixed: ritorna le affected_rows oppure false se c'è qualche errore
	 * 		(non salva gli eventuali elementi successivi a quello che ha generato l'errore)
	 */
		public function setRow($items=array())
		{
		// campi mandatory per $item
			$params=array('team','subteam','event');

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
				foreach ($params as $p)
				{
					if (!array_key_exists($p,$item))
					{
						$paramsOk=false;
						$ret=false;
						break;
					}
				}

				if (!$paramsOk) continue;


				$date=date('Y-m-d H:i:s');

				$q
					= "UPDATE "
						. "Teams "
					. "SET "
						. "TeTimeStamp='{$date}' "
				;

			/* campi opzionali e basta */
				if (array_key_exists('rank',$item))
				{
					$canUp=true;
					$q.=",TeRank={$item['rank']}";
				}

				if (array_key_exists('tiebreak',$item)) {
					$canUp=true;
					$q.=",TeTiebreak='{$item['tiebreak']}'";
				}

				if (array_key_exists('decoded',$item)) {
					$canUp=true;
					$q.=",TeTbDecoded='{$item['decoded']}'";
				}

                if (array_key_exists('closest',$item)) {
                    $canUp=true;
                    $q.=",TeTbClosest='{$item['closest']}'";
                }

				if (array_key_exists('so',$item))
				{
					$canUp=true;
					$q.=",TeSO={$item['so']}";
				}


				$q
					.=" WHERE "
						. "TeCoId=" . $item['team'] . " AND TeSubTeam=" . $item['subteam']. " AND TeFinEvent=1 AND TeEvent='" . $item['event'] . "' AND TeTournament=" . $this->tournament . " ";
				;
				//print $q.'<br><br>';

				if (!$canUp)
				{
					return false;
				}
				$r=safe_w_sql($q);

				if (!$r)
				{
					$affected=false;
				}
				else
				{
					$affected+=safe_w_affected_rows();
				}

				if (!$canUp)
				{
					return false;
				}
				$r=safe_w_sql($q);

				if (!$r)
				{
					$affected=false;
				}
				else
				{
					$affected+=safe_w_affected_rows();
				}

			}

			return $affected;
		}

	}
