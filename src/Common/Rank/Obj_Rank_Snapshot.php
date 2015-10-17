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
 *      subFamily=> "Abs" || "DivClass"								[read]
 * 		events	=> array(<ev_1>,<ev_2>,...,<ev_n>) || string,		[read]
 * 		divs	=> array(<div_1>,<div_2>,...,<div_n>) || string		[read]
 * 		cls		=> array(<cl_1>,<cl_2>,...,<cl_n>) || string		[read]
 * 		arrNo	=> #												[read]
 * 		cutRank => #												[read]
 * 		session => #												[read]
 * 		tournament => #												[read]
 * )
 *
 * con:
 *   subFamily: stringa che identifica se estrarre per classifica Assoluta ("Abs") o di Divisione/Classe ("DivClass")
 * 	 events: l'array degli eventi oppure, se scalare, una stringa usata in LIKE.
 *	 divs: l'array delle divisioni oppure, se scalare, una stringa usata in LIKE. Ignorato se subFamily="Abs"
 *	 cls:  l'array delle classi oppure, se scalare, una stringa usata in LIKE. Ignorato se subFamily="Abs"
 * 	 arrNo: Numero di frecce a cui calcolare lo snapshot. 0 significa calcolato automaticamente
 *	 cutRank: Posizione di classifica (inclusa) a cui tagliare. Se impostato durante una calculate() il metodo ignorerà l'opzione
 *	 session: Se impostato ritorna la classifica di quella sessione, con la rank globale. Chi chiama se vuole ricalcolerà la rank in quella sessione
 *	 tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *
 * $data ha la seguente forma
 *
 * array(
 * 		meta 		=> array(
 * 			title 	=> <titolo della classifica localizzato>
 * 			numDist	=> <numero distanze>, inizializzato solo se c'è almeno una sezione
 * 			double	=> <1 se gara doppia 0 altrimenti>, inizializzato solo se c'è almeno una sezione
 *		),
 * 		sections 	=> array(
 * 			div_cl_1 => array(
 * 				meta => array(
 * 					event => <div_cl_1>, valore uguale alla chiave
 * 					descr => <descrizione evento localizzata>
 * 					arrNo => Numero di frecce a cui è calcolato lo snapshot
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
	class Obj_Rank_Snapshot extends Obj_Rank
	{

		public function __construct($opts)
		{
			parent::__construct($opts);
		}

	/**
	 * safeFilter()
	 * Protegge con gli apici gli elementi di $this->opts['events'] e genera il pezzo di query per filtrare
	 *
	 * @return mixed: false se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
		protected function safeFilter()
		{
			$filter="";

			if (!empty($this->opts['divs']) && !empty($this->opts['subFamily']) && $this->opts['subFamily']=="DivClass") {
				if(is_array($this->opts['divs'])) {
					$tmp=array();
					foreach ($this->opts['divs'] as $e) $tmp[]=StrSafe_DB($e);
					sort($tmp);
					$filter.=" AND EnDivision IN (" . implode(',',$tmp). ") ";
				} else {
					$filter.=" AND EnDivision LIKE " . StrSafe_DB($this->opts['divs']) ;
				}
			}
			if (!empty($this->opts['cls']) && !empty($this->opts['subFamily']) && $this->opts['subFamily']=="DivClass") {
				if(is_array($this->opts['cls'])) {
					$tmp=array();
					foreach ($this->opts['cls'] as $e) $tmp[]=StrSafe_DB($e);
					sort($tmp);
					$filter.=" AND EnClass IN (" . implode(',',$tmp). ") ";
				} else {
					$filter.=" AND EnClass LIKE " . StrSafe_DB($this->opts['cls']) ;
				}
			}

			if (!empty($this->opts['events']) && !empty($this->opts['subFamily']) && $this->opts['subFamily']=="DivClass") {
				// events overrides Div/Class selection
				if (is_array($this->opts['events'])) {
					$tmp=array();
					foreach ($this->opts['events'] as $e) $tmp[]=StrSafe_DB($e);
					sort($tmp);
					$filter="AND CONCAT(EnDivision,EnClass) IN (" . implode(',', $tmp) . ")";
				} else {
					$filter="AND CONCAT(EnDivision,EnClass) LIKE '" . $this->opts['events'] . "' ";
				}
			}


			if (!empty($this->opts['events']) && !empty($this->opts['subFamily']) && $this->opts['subFamily']=="Abs") {
				// events overrides Div/Class selection
				if (is_array($this->opts['events'])) {
					$tmp=array();
					foreach ($this->opts['events'] as $e) $tmp[]=StrSafe_DB($e);
					sort($tmp);
					$filter="AND IndEvent IN (" . implode(',',$tmp) . ")";
				} else {
					$filter="AND IndEvent LIKE '" . $this->opts['events'] . "' ";
				}
			}

			return $filter;
		}


		public function calculate()
		{
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
			$isAbs=(!empty($this->opts['subFamily']) && $this->opts['subFamily']=="Abs");

			$f=$this->safeFilter();
			$filter=($f!==false ? $f : "");

			if(!empty($this->opts['session']) and $ses=intval($this->opts['session'])) {
				$filter .= " AND QuSession=$ses ";
			}

			$cutRank=0;
			if (array_key_exists('cutRank',$this->opts) && is_numeric($this->opts['cutRank']) && $this->opts['cutRank']>0)
				$cutRank = $this->opts['cutRank'];

			$ArrowNo=0;
			$SnapDistance=0;
			if(!empty($this->opts['arrNo'])  && is_numeric($this->opts['arrNo']))
				$ArrowNo = $this->opts['arrNo'];
			else
			{
				$q = "SELECT MAX(EqArrowNo) as ArrowNo
					FROM Entries
					INNER JOIN Qualifications ON EnId=QuId
					INNER JOIN ElabQualifications ON EnId=EqId
					" .	($isAbs ? "INNER JOIN Individuals ON EnId=IndId AND EnTournament=IndTournament " : "")	."
					WHERE EnAthlete=1 AND " . ($isAbs ? "EnIndFEvent" : "EnIndClEvent") ."=1 AND EnStatus <= 1 AND EnTournament = '{$this->tournament}' {$filter}
					GROUP BY QuSession
					ORDER BY ArrowNo ASC";
				$Rs=safe_r_sql($q);
				if(safe_num_rows($Rs)>0)
					$ArrowNo = safe_fetch($Rs)->ArrowNo;
			}

			if($ArrowNo != 0)
			{
				$q = "SELECT MIN(EqDistance) as Distance
					FROM Entries
					INNER JOIN Qualifications ON EnId=QuId
					INNER JOIN ElabQualifications ON EnId=EqId
					" .	($isAbs ? "INNER JOIN Individuals ON EnId=IndId AND EnTournament=IndTournament " : "")	."
					WHERE EnAthlete=1 AND EnTournament='{$this->tournament}' AND EqArrowNo='$ArrowNo' {$filter}";
				$Rs=safe_r_sql($q);
				if($Rs)
				$SnapDistance=safe_fetch($Rs)->Distance;
			}

			$q="
				SELECT distinct
					EnId,EnCode, EnName AS Name, upper(EnFirstName) AS FirstNameUpper, EnFirstName AS FirstName, SUBSTRING(QuTargetNo,1,1) AS Session,
					SUBSTRING(QuTargetNo,2) AS TargetNo,
					CoCode, CoName, EnClass, EnDivision,EnAgeClass, EnSubClass, ClDescription, DivDescription,
					IFNULL(Td1,'.1.') as Td1, IFNULL(Td2,'.2.') as Td2, IFNULL(Td3,'.3.') as Td3, IFNULL(Td4,'.4.') as Td4, IFNULL(Td5,'.5.') as Td5, IFNULL(Td6,'.6.') as Td6, IFNULL(Td7,'.7.') as Td7, IFNULL(Td8,'.8.') as Td8,
					QuD1Score, QuD2Score, QuD3Score, QuD4Score, QuD5Score, QuD6Score, QuD7Score, QuD8Score,
					QuD1Rank, QuD2Rank, QuD3Rank, QuD4Rank, QuD5Rank, QuD6Rank, QuD7Rank, QuD8Rank,
					QuD1Gold, QuD2Gold, QuD3Gold, QuD4Gold, QuD5Gold, QuD6Gold, QuD7Gold, QuD8Gold,
					QuD1Xnine, QuD2Xnine, QuD3Xnine, QuD4Xnine, QuD5Xnine, QuD6Xnine, QuD7Xnine, QuD8Xnine, ";
			if($isAbs)
				$q .= "IndD1Rank, IndD2Rank, IndD3Rank, IndD4Rank, IndD5Rank, IndD6Rank, IndD7Rank, IndD8Rank, EvCode,EvEventName, ";
			if($SnapDistance==0)
			{
				$q .= "QuScore as OrderScore, QuGold as OrderGold, QuXnine as OrderXnine, '0' as EqDistance, '0' as EqScore, '0' as EqGold, '0' as EqXNine, ";
			}
			else
			{
				for($i=1; $i<$SnapDistance; $i++)
					$q .= "QuD" . $i . "Score+";
				$q .="IFNULL(EqScore,0) AS OrderScore, ";
				for($i=1; $i<$SnapDistance; $i++)
					$q .= "QuD" . $i . "Gold+";
				$q .="IFNULL(EqGold,0) AS OrderGold, ";
				for($i=1; $i<$SnapDistance; $i++)
					$q .= "QuD" . $i . "XNine+";
				$q .="IFNULL(EqXNine,0) AS OrderXnine, ";
				$q .= "EqDistance, IFNULL(EqScore,0) as EqScore, IFNULL(EqGold,0) as EqGold, IFNULL(EqXNine,0) as EqXNine, ";
			}
			$q .= "
					QuScore AS Score, QuGold AS Gold, QuXnine AS XNine, QuHits as Hits,
					QuTimestamp,
					ToGolds AS GoldLabel, ToXNine AS XNineLabel,
					ToNumDist,ToDouble "
					. ', ' . ($isAbs ? "IF(EvElim1=0 && EvElim2=0, IF(EvFinalFirstPhase=48, 104, IF(EvFinalFirstPhase=24, 56, (EvFinalFirstPhase*2))),IF(EvElim1=0,EvElim2,EvElim1))" : "''") . ' as QualifiedNo '
					. "FROM
					Tournament

					INNER JOIN
						Entries
					ON ToId=EnTournament

					INNER JOIN
						Countries
					ON EnCountry=CoId AND EnTournament=CoTournament AND EnTournament={$this->tournament}

					INNER JOIN
						Qualifications
					ON EnId=QuId ";
			if($SnapDistance!=0)
				$q .= "LEFT JOIN ElabQualifications ON EnId=EqId AND EqArrowNo='{$ArrowNo}' ";
			if($isAbs)
			{
				$q .= "
					INNER JOIN
						EventClass
					ON EnClass=EcClass AND EnDivision=EcDivision AND EnTournament=EcTournament AND EcTeamEvent=0
					INNER JOIN
						Events
					ON EvCode=EcCode AND EvTeamEvent=EcTeamEvent AND EvTournament=EcTournament
					INNER JOIN
						Individuals
					ON EnId=IndId AND EnTournament=IndTournament AND IndEvent=EvCode
					";
			}
			$q .= "	INNER JOIN
						Classes
					ON EnClass=ClId AND ClTournament={$this->tournament}

					INNER JOIN
						Divisions
					ON EnDivision=DivId AND DivTournament={$this->tournament}

					LEFT JOIN
						TournamentDistances
					ON ToType=TdType AND TdTournament=ToId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses

					WHERE
						EnAthlete=1 AND QuScore!=0 AND " . ($isAbs ? "EnIndFEvent" : "EnIndClEvent") ."=1 AND EnStatus <= 1 AND ToId={$this->tournament}
						{$filter}

					ORDER BY ";
			if($isAbs)
				$q .= "EvProgr, EvCode, ";
			else
				$q .= "DivViewOrder, EnDivision, ClViewOrder, EnClass,  ";
			$q .= "OrderScore DESC, OrderGold DESC, OrderXNine DESC, FirstName, Name";

			//print $q.'<br>';
// 			debug_svela($q);
			$r=safe_r_sql($q);

			$this->data['meta']['title']=get_text(($isAbs ? 'ResultIndAbsSnap':'ResultIndClassSnap'), 'Tournament');
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
					if (($isAbs && $curEvent!=$myRow->EvCode) || (!$isAbs && $curEvent!=$myRow->EnDivision . $myRow->EnClass))
					{
					/*
					 *  se non sono all'inizio, prima di iniziare una sezione devo prendere quella appena fatta
					 *  e accodarla alle altre
					 */
						if ($curEvent!='')
						{
							$this->data['sections'][$curEvent]=$section;
							$section=null;
						}

					// al cambio creo una nuova sezione
						if($isAbs)
							$curEvent=$myRow->EvCode;
						else
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
							'div' => get_text('Division'),
							'class' => get_text('Class'),
							'ageclass' => get_text('AgeCl'),
							'subclass' => get_text('SubCl','Tournament'),
							'countryCode' => '',
							'countryName' => get_text('Country'),
							'rank' => get_text('PositionShort'),
							'score' => get_text('TotaleScore'),
							'gold' => $myRow->GoldLabel,
							'xnine' => $myRow->XNineLabel,
							'hits' => get_text('Arrows','Tournament')
						);

						$fields = $fields + $distFields;

						$section=array(
							'meta' => array(
								'event' => $curEvent,
								'descr' => ($isAbs ? $myRow->EvEventName : get_text($myRow->DivDescription,'','',true) . " - " . get_text($myRow->ClDescription,'','',true)),
								'printHeader' => get_text('AfterXArrows', 'Common', $ArrowNo),
								'snapArrows' => $ArrowNo,
								'numDist' => $distValid,
								'qualifiedNo' => $myRow->QualifiedNo,
								'snapDistance' => $SnapDistance,
								'sesArrows'=> array(),
								'running' => 0,
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
					if(!($oldScore==$myRow->OrderScore && $oldGold==$myRow->OrderGold && $oldXnine==$myRow->OrderXnine))
						$myRank = $myPos;
					$oldScore = $myRow->OrderScore;
					$oldGold = $myRow->OrderGold;
					$oldXnine = $myRow->OrderXnine;
				// creo un elemento per la sezione
					$item=array(
						'id'  => $myRow->EnId,
						'bib' => $myRow->EnCode,
						'session' => $myRow->Session,
						'target' => $myRow->TargetNo,
						'athlete' => $myRow->FirstNameUpper . ' ' . $myRow->Name,
						'familyname' => $myRow->FirstName,
						'familynameUpper' => $myRow->FirstNameUpper,
						'givenname' => $myRow->Name,
						'div' => $myRow->EnDivision,
						'class' => $myRow->EnClass,
						'ageclass' => $myRow->EnAgeClass,
						'subclass' => $myRow->EnSubClass,
						'countryCode' => $myRow->CoCode,
						'countryName' => $myRow->CoName,
						'rank' => $myRank,
						'score' => $myRow->Score,
						'gold' => $myRow->Gold,
						'xnine' => $myRow->XNine,
						'arrowsShot' => $myRow->Hits,
						'scoreSnap' => $myRow->OrderScore,
						'goldSnap' => $myRow->OrderGold,
						'xnineSnap' => $myRow->OrderXnine,
						'dist_Snap' => '0' . '|' . $myRow->{'EqScore'} . '|' . $myRow->{'EqGold'} . '|' . $myRow->{'EqXNine'}
					);

					$distFields=array();
					foreach(range(1,8) as $n)
					{
						$distFields['dist_' . $n]= ($isAbs? $myRow->{'IndD' . $n . 'Rank'} : $myRow->{'QuD' . $n . 'Rank'}) . '|' . $myRow->{'QuD' . $n . 'Score'} . '|' . $myRow->{'QuD' . $n . 'Gold'} . '|' . $myRow->{'QuD' . $n . 'Xnine'};
					}

					$item = $item + $distFields;

				// e lo aggiungo alla sezione
					//print_r($item);

					if(!$cutRank || $myRank<=$cutRank)
					{
						$section['items'][]=$item;
						if ($myRow->QuTimestamp>$this->data['meta']['lastUpdate'])
							$this->data['meta']['lastUpdate']=$myRow->QuTimestamp;
					}
				}

			// ultimo giro
				$this->data['sections'][$curEvent]=$section;
			}
		}

	}
?>