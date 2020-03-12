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
 * 		comparedTo => #												[read]
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
	class Obj_Rank_DivClass extends Obj_Rank
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

			if (!empty($this->opts['divs'])) {
				if(is_array($this->opts['divs'])) {
					$tmp=array();
					foreach ($this->opts['divs'] as $e) $tmp[]=StrSafe_DB($e);
					sort($tmp);
					$filter.=" AND EnDivision IN (" . implode(',',$tmp). ") ";
				} else {
					$filter.=" AND EnDivision LIKE " . StrSafe_DB($this->opts['divs']) ;
				}
			}
			if (!empty($this->opts['cls'])) {
				if(is_array($this->opts['cls'])) {
					$tmp=array();
					foreach ($this->opts['cls'] as $e) $tmp[]=StrSafe_DB($e);
					sort($tmp);
					$filter.=" AND EnClass IN (" . implode(',',$tmp). ") ";
				} else {
					$filter.=" AND EnClass LIKE " . StrSafe_DB($this->opts['cls']) ;
				}
			}

			if (!empty($this->opts['events'])) {
				// events overrides Div/Class selection
				if (is_array($this->opts['events'])) {
					$tmp=array();
					foreach ($this->opts['events'] as $e) $tmp[]=StrSafe_DB($e);
					sort($tmp);
					$filter="AND CONCAT(EnDivision,EnClass) IN (" . implode(',',$tmp) . ")";
				} else {
					$filter="AND CONCAT(EnDivision,EnClass) LIKE '" . $this->opts['events'] . "' ";
				}
			}

			if (!empty($this->opts['enid'])) {
				$filter.=" AND EnId=" . intval($this->opts['enid']) . " ";
			}

			if (!empty($this->opts['encode'])) {
				if (is_array($this->opts['encode'])) {
					$tmp=array();
					foreach ($this->opts['encode'] as $e) $tmp[]=StrSafe_DB($e);
					sort($tmp);
					$filter="AND EnCode IN (" . implode(',',$tmp) . ")";
				} else {
					$filter="AND EnCode = " . StrSafe_DB($this->opts['encode']) . " ";
				}
			}

			if (!empty($this->opts['coid'])) {
				$filter.=" AND EnCountry=" . intval($this->opts['coid'])  . " " ;
			}

			if (!empty($this->opts['sessions'])) {
				$filter.=" AND QuSession in (" . implode($this->opts['sessions'])  . ") " ;
			}

			return $filter;
		}

		public function __construct($opts=null)
		{
			parent::__construct($opts);
		}


		function calculate() {
			return true;
		}
	/**
	 * read()
	 *
	 * @override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#read()
	 *
	 */
		public function read()
		{
			if(!empty($this->opts['runningDist']) && $this->opts['runningDist']>0)
				$this->opts['dist'] = 0;
			$dd = (isset($this->opts['dist']) && $this->opts['dist']>0  ? 'D' . $this->opts['dist'] : '');

			$f=$this->safeFilter();
			$filter=($f!==false ? $f : "");

			if (array_key_exists('cutScore',$this->opts) && is_numeric($this->opts['cutScore']))
				$filter.= "AND Qu{$dd}Score>={$this->opts['cutScore']} ";

			if (array_key_exists('cutRank',$this->opts) && is_numeric($this->opts['cutRank']) && $this->opts['cutRank']>0)
				$filter.= "AND Qu" . ($this->opts['dist']>0 ? 'D' . $this->opts['dist'] : 'Cl') . "Rank<={$this->opts['cutRank']} ";

			if(!empty($this->opts['session']) and $ses=intval($this->opts['session'])) {
				$filter .= " AND QuSession=$ses ";
			}

			$comparedTo=0;
			if(!empty($this->opts["comparedTo"]) && is_numeric($this->opts["comparedTo"]))
				$comparedTo=$this->opts["comparedTo"];

			$tmp=null;
			if (empty($this->opts['runningDist']) || $this->opts['runningDist']>0)
			{
				$tmp=array();
				foreach(range(1,(empty($this->opts['runningDist']) ? 8 : $this->opts['runningDist'])) as $n)
				{
					$tmp[]='QuD'.$n.'Hits';
				}
				$tmp=implode('+', $tmp);
			}
			else
			{
				$tmp='QuD'.$this->opts['dist'].'Hits';
			}

			$MyRank="Qu" . ($dd=='' ? 'Cl' : '') . $dd . "Rank";

			$q="
				SELECT
					EnId, EnCode, EnSex, EnNameOrder, upper(EnIocCode) EnIocCode, EnName AS Name, upper(EnFirstName) AS FirstNameUpper, EnFirstName AS FirstName, SUBSTRING(QuTargetNo,1,1) AS Session,
					SUBSTRING(QuTargetNo,2) AS TargetNo, FlContAssoc,
					CoId, CoCode, CoName, EnClass, EnDivision,EnAgeClass, EnSubClass, ClDescription, DivDescription,
					IFNULL(Td1,'.1.') as Td1, IFNULL(Td2,'.2.') as Td2, IFNULL(Td3,'.3.') as Td3, IFNULL(Td4,'.4.') as Td4, IFNULL(Td5,'.5.') as Td5, IFNULL(Td6,'.6.') as Td6, IFNULL(Td7,'.7.') as Td7, IFNULL(Td8,'.8.') as Td8,
					QuD1Score, QuD1Rank, QuD2Score, QuD2Rank, QuD3Score, QuD3Rank, QuD4Score, QuD4Rank,
					QuD5Score, QuD5Rank, QuD6Score, QuD6Rank, QuD7Score, QuD7Rank, QuD8Score, QuD8Rank,
					QuD1Gold, QuD2Gold, QuD3Gold, QuD4Gold, QuD5Gold, QuD6Gold, QuD7Gold, QuD8Gold,
					QuD1Xnine, QuD2Xnine, QuD3Xnine, QuD4Xnine, QuD5Xnine, QuD6Xnine, QuD7Xnine, QuD8Xnine,
					QuD1ArrowString, QuD2ArrowString, QuD3ArrowString, QuD4ArrowString, QuD5ArrowString, QuD6ArrowString, QuD7ArrowString, QuD8ArrowString,
					{$tmp} AS Arrows_Shot, ToNumEnds, DiEnds, DiArrows,
					{$MyRank} AS Rank, " . (!empty($comparedTo) ? 'IFNULL(QopClRank,0)' : '0') . " as OldRank, Qu{$dd}Score AS Score, Qu{$dd}Gold AS Gold,Qu{$dd}Xnine AS XNine, Qu{$dd}Hits AS Hits, ";

			if(!empty($this->opts['runningDist']) && $this->opts['runningDist']>0)
			{
				for($i=1; $i<=$this->opts['runningDist']; $i++)
					$q .= "QuD" . $i . "Score+";
				$q = substr($q,0,-1) . " AS OrderScore, ";
				for($i=1; $i<=$this->opts['runningDist']; $i++)
					$q .= "QuD" . $i . "Gold+";
				$q = substr($q,0,-1) . " AS OrderGold, ";
				for($i=1; $i<=$this->opts['runningDist']; $i++)
					$q .= "QuD" . $i . "XNine+";
				$q = substr($q,0,-1) . " AS OrderXnine, ";
			}
			else {
				$q .= "0 AS OrderScore, 0 AS OrderGold, 0 AS OrderXnine, ";
			}
			$q .= "	QuTimestamp, ToGolds AS GoldLabel, ToXNine AS XNineLabel, ToNumDist,ToDouble
				FROM Tournament
				INNER JOIN Entries ON ToId=EnTournament
				INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament AND EnTournament={$this->tournament}
				INNER JOIN Qualifications ON EnId=QuId
				INNER JOIN Classes ON EnClass=ClId AND ClTournament=EnTournament AND ClAthlete=1
				INNER JOIN Divisions ON EnDivision=DivId AND DivTournament=EnTournament AND DivAthlete=1
				LEFT JOIN TournamentDistances ON ToType=TdType AND TdTournament=ToId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses ";
			if(!empty($comparedTo))
				$q .= "LEFT JOIN QualOldPositions ON EnId=QopId AND QopHits=" . ($comparedTo>0 ? $comparedTo :  "(SELECT MAX(QopHits) FROM QualOldPositions WHERE QopId=EnId AND QopHits!=QuHits) ") . " ";
			$q .= "	LEFT JOIN Flags ON FlIocCode='FITA' and FlCode=CoCode and FlTournament=-1
				left join DistanceInformation on EnTournament=DiTournament and DiSession=1 and DiDistance=1 and DiType='Q'
				WHERE EnAthlete=1 AND EnIndClEvent=1 AND EnStatus <= 1 AND QuScore != 0 AND ToId={$this->tournament}
					{$filter}
				ORDER BY DivViewOrder, EnDivision, ClViewOrder, EnClass, ";
			if(!empty($this->opts['runningDist']) && $this->opts['runningDist']>0)
				$q .= "OrderScore DESC, OrderGold DESC, OrderXnine DESC, FirstName, Name ";
			else
				$q .= "{$MyRank} ASC, FirstName, Name ";

			//print $q.'<br>';

			$r=safe_r_sql($q);
			//print '<pre>';

			$this->data['meta']['title']=get_text('ResultClass','Tournament');
			$this->data['meta']['distance']=(isset($this->opts['dist']) ? $this->opts['dist'] : 0);
			$this->data['meta']['numDist']=-1;
			$this->data['meta']['double']=-1;
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
			$this->data['sections']=array();

			if ($r && safe_num_rows($r)>0)
			{
				$curEvent='';

				$section=null;

				$oldScore=-1;
				$oldGold=-1;
				$oldXnine=-1;
				$myPos=0;
				$myRank=0;

				while ($myRow=safe_fetch($r))
				{
					if ($curEvent!=$myRow->EnDivision . $myRow->EnClass)
					{
					/*
					 *  se non sono all'inizio, prima di iniziare una sezione devo prendere quella appena fatta
					 *  e accodarla alle altre
					 */
						if ($curEvent!='')
						{
							foreach($section["meta"]["arrowsShot"] as $k => $v) {
								if($v) $section["meta"]["sesArrows"][$k] = get_text('AfterXArrows', 'Common', $v);
							}
							$this->data['sections'][$curEvent]=$section;
							$section=null;
						}

					// al cambio creo una nuova sezione
						$curEvent=$myRow->EnDivision . $myRow->EnClass;

					// inizializzo i meta che son comuni a tutta la classifica
						if ($this->data['meta']['numDist']==-1)
						{
							$this->data['meta']['numDist']=$myRow->ToNumDist;
							$this->data['meta']['double']=$myRow->ToDouble;
						}

					// qui ci sono le descrizioni dei campi
						$distFields=array();
						$distValid=$myRow->ToNumDist;
						foreach(range(1,8) as $n)
						{
							$distFields['dist_' . $n]=$myRow->{'Td' . $n};
							if($distFields['dist_' . $n]=='-')
								$distValid--;
						}

						$fields=array(
							'id'  => 'Id',
							'bib' => get_text('Code','Tournament'),
							'session' => get_text('Session'),
							'target' => get_text('Target'),
							'athlete' => get_text('Athlete'),
							'familyname' => get_text('FamilyName', 'Tournament'),
							'givenname' => get_text('Name', 'Tournament'),
							'gender' => get_text('Sex', 'Tournament'),
							'div' => get_text('Division'),
							'class' => get_text('Class'),
							'ageclass' => get_text('AgeCl'),
							'subclass' => get_text('SubCl','Tournament'),
							'countryId'  => 'CoId',
							'countryCode' => '',
							'countryName' => get_text('Country'),
							'rank' => get_text('PositionShort'),
							'oldRank' => '',
							'score' => get_text('TotaleScore'),
							'gold' => $myRow->GoldLabel,
							'xnine' => $myRow->XNineLabel,
							'hits' => get_text('Arrows','Tournament')
						);

						$fields=$fields+$distFields;

						$section=array(
							'meta' => array(
								'event' => $curEvent,
								'descr' => get_text($myRow->DivDescription,'','',true) . " - " . get_text($myRow->ClDescription,'','',true),
								'numDist' => $distValid,
								'arrowsShot'=> array(),
								'maxArrows' => ($myRow->DiEnds ? $myRow->DiEnds*$myRow->DiArrows : $myRow->ToNumEnds*3),
								'sesArrows'=> array(),
								'printHeader' => "",
								'fields' => $fields
							)
						);

						$oldScore=-1;
						$oldGold=-1;
						$oldXnine=-1;
						$myPos=0;
						$myRank=0;

					}

					$myPos++;
					if(!($oldScore==$myRow->OrderScore && $oldGold==$myRow->OrderGold && $oldXnine==$myRow->OrderXnine)) {
						$myRank = $myPos;
					}
					$oldScore = $myRow->OrderScore;
					$oldGold = $myRow->OrderGold;
					$oldXnine = $myRow->OrderXnine;
				// creo un elemento per la sezione
                    if($myRow->Rank==9999) {
                        $tmpRank = 'DSQ';
                    } else if ($myRow->Rank==9998) {
                        $tmpRank = 'DNS';
                    } else {
                        $tmpRank= (!empty($this->opts['runningDist']) && $this->opts['runningDist']>0 ? $myRank : $myRow->Rank);
                    }


					$item=array(
						'id'  => $myRow->EnId,
						'bib' => $myRow->EnCode,
						'session' => $myRow->Session,
						'target' => $myRow->TargetNo,
						'athlete' => $myRow->FirstNameUpper . ' ' . $myRow->Name,
						'familyname' => $myRow->FirstName,
						'familynameUpper' => $myRow->FirstNameUpper,
						'givenname' => $myRow->Name,
						'nameOrder' => $myRow->EnNameOrder,
						'gender' => $myRow->EnSex,
						'div' => $myRow->EnDivision,
						'class' => $myRow->EnClass,
						'ageclass' => $myRow->EnAgeClass,
						'subclass' => $myRow->EnSubClass,
						'countryId' => $myRow->CoId,
						'countryCode' => $myRow->CoCode,
						'contAssoc' => $myRow->FlContAssoc,
						'countryIocCode' => $myRow->EnIocCode,
						'countryName' => $myRow->CoName,
						'rank' => $tmpRank,
						'oldRank' => $myRow->OldRank,
						'score' => (!empty($this->opts['runningDist']) && $this->opts['runningDist']>0 ? $myRow->OrderScore : $myRow->Score),
						'gold' => (!empty($this->opts['runningDist']) && $this->opts['runningDist']>0 ? $myRow->OrderGold : $myRow->Gold),
						'xnine' => (!empty($this->opts['runningDist']) && $this->opts['runningDist']>0 ? $myRow->OrderXnine : $myRow->XNine),
						'hits' => $myRow->Hits,
						'arrowsShot' => $myRow->Arrows_Shot
					);

					$distFields=array();
					foreach(range(1,8) as $n)
					{
						if((!empty($this->opts['runningDist']) && $this->opts['runningDist']>0 && $n>$this->opts['runningDist']) || (isset($this->opts['dist']) && $this->opts['dist']>0 && $n!=$this->opts['dist']))
							$distFields['dist_' . $n]='0|0|0|0';
						else
							$distFields['dist_' . $n]=$myRow->{'QuD' . $n . 'Rank'} . '|' . $myRow->{'QuD' . $n . 'Score'} . '|' . $myRow->{'QuD' . $n . 'Gold'} . '|' . $myRow->{'QuD' . $n . 'Xnine'};

						$distFields["D{$n}Arrowstring"]=$myRow->{'QuD' . $n . 'ArrowString'} ;

					}

					$item=$item+$distFields;

					//Gestisco il numero di frecce tirate per sessione
					if(empty($section["meta"]["arrowsShot"][$myRow->Session]) || $section["meta"]["arrowsShot"][$myRow->Session]<=$myRow->Arrows_Shot)
						$section["meta"]["arrowsShot"][$myRow->Session] = $myRow->Arrows_Shot;

				// e lo aggiungo alla sezione
					//print_r($item);
					$section['items'][]=$item;

					if ($myRow->QuTimestamp>$this->data['meta']['lastUpdate'])
						$this->data['meta']['lastUpdate']=$myRow->QuTimestamp;

				}

				foreach($section["meta"]["arrowsShot"] as $k => $v) {
					if($v) $section["meta"]["sesArrows"][$k] = str_replace("<br/>"," ",get_text('AfterXArrows', 'Common', $v));
				}

			// ultimo giro
				$this->data['sections'][$curEvent]=$section;
			}
			//print '</pre>';
			/*print '<pre>';
			print_r($this->data);
			print '</pre>';*/
		}
	}