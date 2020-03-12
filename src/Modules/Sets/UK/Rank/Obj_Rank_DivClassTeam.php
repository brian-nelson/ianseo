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
	class Obj_Rank_DivClassTeam extends Obj_Rank
	{
	/**
	 * safeFilter()
	 * Protegge con gli apici gli elementi di $this->opts['events'] e genera il pezzo di query per filtrare
	 *
	 * @return string: vuota se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
		protected function safeFilter()
		{
			$filter="";

			if (!empty($this->opts['divs']))
			{
				if(is_array($this->opts['divs']))
				{
					$tmp=array();
					foreach ($this->opts['divs'] as $e)
					{
						$tmp[]=StrSafe_DB($e);
					}
					sort($tmp);
					$filter.=" AND DivId IN (" . implode(',',$tmp). ") ";
				}
				else
				{
					$filter.=" AND DivId LIKE " . StrSafe_DB($this->opts['divs']) ;
				}
			}
			if (!empty($this->opts['cls']))
			{
				if(is_array($this->opts['cls']))
				{
					$tmp=array();
					foreach ($this->opts['cls'] as $e)
					{
						$tmp[]=StrSafe_DB($e);
					}
					sort($tmp);
					$filter.=" AND ClId IN (" . implode(',',$tmp). ") ";
				}
				else
				{
					$filter.=" AND ClId LIKE " . StrSafe_DB($this->opts['cls']) ;
				}
			}

			if (!empty($this->opts['events']))
			{
				if (is_array($this->opts['events']))
				{
					$tmp=array();
					foreach ($this->opts['events'] as $e) $tmp[]=StrSafe_DB($e);

					sort($tmp);
					$filter="AND TeEvent IN (" . implode(',',$tmp) . ")";
				}
				else
				{
					$filter="AND TeEvent LIKE '" . $this->opts['events'] . "' ";
				}
			}

			return $filter;
		}

		public function __construct($opts)
		{
			parent::__construct($opts);
		}

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
			return true;
		}

	/**
	 * read()
	 *
	 * @Override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#calculate()
	 */
		public function read()
		{
			$filter=$this->safeFilter();

			if (array_key_exists('cutScore',$this->opts) && is_numeric($this->opts['cutScore']))
				$filter.= "AND TeScore>={$this->opts['cutScore']} ";

			if (array_key_exists('cutRank',$this->opts) && is_numeric($this->opts['cutRank']) && $this->opts['cutRank']>0)
				$filter.= "AND TeRank<={$this->opts['cutRank']} ";

			$orderBy= "DivViewOrder, TeEvent, TeRank ASC,CoCode ASC, TcOrder ";

			$q="
				SELECT
					ToId, TeRank, CoId, CoCode, CoName,	TeSubTeam, TeEvent, DivDescription,
					EnId, EnCode, EnFirstName, upper(EnFirstName) EnFirstNameUpper, EnName,Q,EnClass,EnDivision,EnAgeClass,EnSubClass,
					QuScore,QuGold,	QuXnine, TeScore, TeGold, TeXnine, TeHits,ToGolds, ToXNine ,TeTimeStamp
				FROM
					Tournament
					INNER JOIN
						Teams
					ON ToId=TeTournament AND TeFinEvent=0
					INNER JOIN
						Countries
					ON TeCoId=CoId AND TeTournament=CoTournament
					INNER JOIN
						(
							SELECT
								TcCoId, TcEvent, TcTournament, TcFinEvent, COUNT(TcId) as Q
							FROM
								TeamComponent
							GROUP BY
								TcCoId, TcEvent
						) AS sq
					ON TeCoId=sq.TcCoId AND TeEvent=sq.TcEvent AND TeTournament=sq.TcTournament AND TeFinEvent=sq.TcFinEvent
					INNER JOIN
						TeamComponent  AS tc
					ON TeCoId=tc.TcCoId AND TeEvent=tc.TcEvent AND TeTournament=tc.TcTournament AND TeFinEvent=tc.TcFinEvent
					INNER JOIN
						Entries
					ON TcId=EnId
					INNER JOIN
						Qualifications
					ON EnId=QuId
					INNER JOIN
						Divisions
					ON LEFT(TeEvent,1)=DivId AND TeTournament=DivTournament
				WHERE
					ToId={$this->tournament}
					{$filter}
				ORDER BY
					{$orderBy}
			";

			$r=safe_r_sql($q);

			$this->data['meta']['title']=get_text('ResultClass','Tournament') . ' - ' . get_text('Teams');
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
			$this->data['sections']=array();

			$myEv='';
			$myTeam='';

			if (safe_num_rows($r)>0)
			{
				$section=null;

				while ($row=safe_fetch($r))
				{
					if ($myEv!=$row->TeEvent)
					{
						if ($myEv!='')
						{
							$this->data['sections'][$myEv]=$section;
							$section=null;

						}

						$myEv=$row->TeEvent;

						$tmp='';
						if(substr($row->TeEvent,-1,1)=='0' || substr($row->TeEvent,-1,1)=='1' || substr($row->TeEvent,-1,1)=='2')
						{
							$tmp = (substr($row->TeEvent,-1,1)=='0' ? "Club " : (substr($row->TeEvent,-1,1)=='1' ? "County " : "Regional ")) . $row->DivDescription . " Team";
						}
						else if(!is_null($row->DivDescription))
						{
							$tmp = (get_text($row->DivDescription,'','',true));
						}
						else
						{
							$tmp = (get_text($row->TeEvent,'','',true));
						}

						$fields=array(
							'id' 			=> 'Id',
							'countryCode' 	=> '',
							'countryName' 	=> get_text('Country'),
							'subteam' 		=> get_text('PartialTeam'),
							'athletes' 		=> array(
								'name' => get_text('Name','Tournament'),
								'fields'=>array(
									'id'  => 'Id',
									'bib' => get_text('Code','Tournament'),
									'athlete' => get_text('Athlete'),
									'familyname' => get_text('FamilyName', 'Tournament'),
									'givenname' => get_text('Name', 'Tournament'),
									'div' => get_text('Division'),
									'class' => get_text('Cl'),
									'ageclass' => get_text('AgeCl'),
									'subclass' => get_text('SubCl','Tournament'),
									'quscore' => get_text('TotaleScore'),
									'qugold' => $row->ToGolds,
									'quxnine' => $row->ToXNine
								)
							),
							'rank'			=> get_text('PositionShort'),
							'score' 		=> get_text('TotaleScore'),
							'gold' 			=> $row->ToGolds,
							'xnine' 		=> $row->ToXNine,
							'hits'			=> get_text('Arrows','Tournament')
						);


						$section=array(
							'meta' => array(
								'event' => $myEv,
								'descr' => $tmp,
								'fields'=>$fields
							),
						);
					}

					if ($myTeam!=$row->CoId . $row->TeEvent)
					{
						$item=array(
							'id' 			=> $row->CoId,
							'countryCode' 	=> $row->CoCode,
							'countryName' 	=> $row->CoName,
							'subteam' 		=> $row->TeSubTeam,
							'athletes'		=> array(),
							/*'athletes' 		=> (!array_key_exists('components',$this->opts) || $this->opts['components']
								? array(
									'meta' => array(
										'fields' =>array(
											'id'  => 'Id',
											'bib' => get_text('Code','Tournament'),
											'athlete' => get_text('Athlete'),
											'familyname' => get_text('FamilyName', 'Tournament'),
											'givenname' => get_text('Name', 'Tournament'),
											'div' => get_text('Division'),
											'class' => get_text('Cl'),
											'ageclass' => get_text('AgeCl'),
											'subclass' => get_text('SubCl','Tournament'),
											'quscore' => get_text('TotaleScore')
										)
									)
								)
								: null),*/
							'rank'			=> $row->TeRank,
							'score' 		=> $row->TeScore,
							'gold' 			=> $row->TeGold,
							'xnine' 		=> $row->TeXnine,
							'hits'			=> $row->TeHits
						);

						$section['items'][]=$item;

						if ($row->TeTimeStamp>$this->data['meta']['lastUpdate'])
							$this->data['meta']['lastUpdate']=$row->TeTimeStamp;


						$myTeam=$row->CoId . $row->TeEvent;
					}

					if (!array_key_exists('components',$this->opts) || $this->opts['components'])
					{
						$athlete=array(
							'id' => $row->EnId,
							'bib' => $row->EnCode,
							'athlete'=>$row->EnFirstNameUpper . ' ' . $row->EnName,
							'familyname' => $row->EnFirstName,
							'familynameUpper' => $row->EnFirstNameUpper,
							'givenname' => $row->EnName,
							'div' => $row->EnDivision,
							'class' => $row->EnClass,
							'ageclass' => $row->EnAgeClass,
							'subclass' => $row->EnSubClass,
							'quscore' => $row->QuScore,
							'qugold' => $row->QuGold,
							'quxnine' => $row->QuXnine
						);

//						$i=0;
//						if (array_key_exists('items',$section))
//							$i=count($section['items'])-1;
//						$section['items'][$i]['athletes']['items'][]=$athlete;

						$section['items'][count($section['items'])-1]['athletes'][]=$athlete;
					}
				}

			// ultimo giro
				$this->data['sections'][$myEv]=$section;
			}
		}
	}