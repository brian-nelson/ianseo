<?php
	require_once('Common/ARF/ARF.class.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	define('debug',false);

	class ARFInput extends ARF
	{
		private $session=null;	// sessione
		private $end=null;		// volee
		private $distance=null;	// distanza (QUALIFICATION)
		private $arrows=null;	// numero di frecce da importare

	// num max di frecce
		private $maxArrows=null;

		private $G=null;	// cosa valutare come gold
		private $X=null;	// cosa valutare come xnine

	/**
	 * Costruttore.
	 * Inizializza $this->xmlDoc data la stringa xml $xml
	 *
	 * @param string $xml: stringa xml
	 */
		public function __construct($xml)
		{
		// Creo un DOMDocument e gli carico la stringa
			$this->xmlDoc=new DOMDocument();

			$x=$this->xmlDoc->loadXML($xml);

			if ($x===false)
			{
				$this->setError(1);
				return;
			}

		// Verifico che la root si chiami 'response'
			$root=$this->xmlDoc->documentElement;

			if ($root->nodeName!='response')
			{
				$this->setError(1);
				return;
			}

		// Verifico che <response> abbia dei figli
			if (!$root->hasChildNodes())
			{
				$this->setError(1);
				return;
			}

		// <reponse> è buona
			$this->xmlRoot=$root;

		// verifico la versione
			$versions=$this->xmlRoot->getElementsByTagName('version');
			if($versions->length!=1)
			{
				$this->setError(1);
				return;
			}
			if ($versions->item(0)->textContent!=ARF::ARF_INPUT_VERSION)
			{
				$this->setError(1);
				return;
			}

		// Verifico che ci sia una SOLA sezione <header>
			$headers=$this->xmlDoc->getElementsByTagName('header');
			if ($headers->length!=1)
			{
				$this->setError(1);
				return;
			}

		// e che sia figlia di <response>
			if ($headers->item(0)->parentNode!=$this->xmlRoot)
			{
				$this->setError(1);
				return;
			}

		// <header> è buono
			$this->xmlHeader=$headers->item(0);

		// Verifico se l'header ha le info giuste
			$x=$this->makeHeader();
			if ($x==0)
			{
				$this->setError($x);
				return;
			}
			list($toId,$phase,$session,$distance,$end,$arrows)=$x;
		// chiamo il costruttore padre
			parent::__construct($toId,$phase);

		// setto gli altri valori
			$this->setSession($session);
			$this->setphase($phase);
			$this->setEnd($end);
			$this->setDistance($distance);
			$this->setArrows($arrows);

		// controllo se la fase è bloccata
			if ($this->isBlocked())
			{
				$this->setError(999);
				return;
			}

		// max arrows ori e x
			if ($this->getPhase()==ARF::QUALIFICATION || $this->getPhase()==ARF::ELIMINATION)
			{
				/*$query
					= "SELECT TtGolds,TtXNine,(TtMaxDistScore/TtGolds) AS MaxArrows "
					. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
					. "WHERE ToId=" . $this->getTourId() . " ";*/
				$query
					= "SELECT ToGoldsChars AS TtGolds,ToXNineChars AS TtXNine,(ToMaxDistScore/ToGolds) AS MaxArrows "
					. "FROM Tournament "
					. "WHERE ToId=" . $this->getTourId() . " ";
				$rs=safe_r_sql($query);

				if (safe_num_rows($rs)==1)
				{
					$myRow=safe_fetch($rs);

					$this->setMaxArrows($myRow->MaxArrows);
					$this->G=$myRow->TtGolds;
					$this->X=$myRow->TtXNine;
				}
			}
			elseif ($this->getPhase()==ARF::INDIVIDUAL_FINAL || $this->getPhase()==ARF::TEAM_FINAL)
			{
				$this->setMaxArrows(($this->getPhase()==ARF::INDIVIDUAL_FINAL ? MaxFinIndArrows : MaxFinTeamArrows));
			}

		}

		private function isBlocked()
		{
			$ret=false;
			switch ($this->getPhase())
			{
				case ARF::QUALIFICATION:
					$ret=IsBlocked(BIT_BLOCK_QUAL);
					break;
				case ARF::ELIMINATION:
					$ret=IsBlocked(BIT_BLOCK_ELIM);
					break;
				case ARF::INDIVIDUAL_FINAL:
					$ret=IsBlocked(BIT_BLOCK_IND);
					break;
				case ARF::TEAM_FINAL:
					$ret=IsBlocked(BIT_BLOCK_TEAM);
					break;
			}

			return $ret;
		}

	/**
	 * Verifica se è già stata importata l'arrowstring
	 *
	 */
		public function checkExist()
		{
			$query="";

			$Ip = $this->getArrows()*($this->getEnd()-1)+1;
			$Fp = $Ip+($this->getArrows()-1);

			switch ($this->getPhase())
			{
				case ARF::QUALIFICATION:
					$query
						= "SELECT QuId "
							. "FROM Qualifications "
						. "WHERE "
							. "QuId IN (SELECT EnId FROM Entries WHERE EnTournament=" . $this->getTourId() . ") "
							. "AND QuSession=" . StrSafe_DB($this->getSession()) . " "
							. "AND TRIM(SUBSTRING(RPAD(QuD" . $this->getDistance() . "ArrowString," . $this->getMaxArrows(). ",' ')," . $Ip . "," . $Fp . "))<>''";
					break;
				case ARF::ELIMINATION:
					$query
						= "SELECT ElId "
							. "FROM Eliminations "
						. "WHERE "
							. "ElId IN (SELECT EnId FROM Entries WHERE EnTournament=" . $this->getTourId() . ") "
							. "AND 	ElElimPhase=" . StrSafe_DB($this->getSession()-1) . " "
							. "AND TRIM(SUBSTRING(RPAD(ElArrowString," . $this->getMaxArrows(). ",' ')," . $Ip . "," . $Fp . "))<>''";
					break;
				case ARF::INDIVIDUAL_FINAL:
					$query
						= "SELECT FinAthlete,CONCAT(FSScheduledDate,' ',FSScheduledTime),FinArrowString "
						. "FROM Finals INNER JOIN FinSchedule ON FSEvent=FinEvent AND FSTeamEvent=0 AND FSMatchNo=FinMatchNo AND FSTournament=FinTournament "
						. "WHERE FinTournament=" . $this->getTourId() . " "
						. "AND CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($this->getSession()) . " "
						. "AND TRIM(SUBSTRING(RPAD(FinArrowString," . $this->getMaxArrows() . ",' ')," . $Ip . "," . $Fp . "))<>'' ";
					break;
				case ARF::TEAM_FINAL:
					$query
						= "SELECT TfTeam,CONCAT(FSScheduledDate,' ',FSScheduledTime),TfArrowString "
						. "FROM TeamFinals INNER JOIN FinSchedule ON FSEvent=TfEvent AND FSTeamEvent=1 AND TfMatchNo=FSMatchNo AND FSTournament=TfTournament "
						. "WHERE TfTournament=" . $this->getTourId() . " "
						. "AND CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($this->getSession()) . " "
						. "AND TRIM(SUBSTRING(RPAD(TfArrowString," . $this->getMaxArrows() . ",' ')," . $Ip . "," . $Fp . "))<>'' ";
					break;
			}

			$rs=safe_r_sql($query);

			if ($rs)
			{
				if (safe_num_rows($rs)==0)
					return false;
				else
					return true;
			}
			else
			{
				$this->setError(1);
				return true;
			}

		}

	/**
	 * Setta le info dell'header o ritorna un errore
	 *
	 * @return mixed: codice errore in caso di errore, array(tourId,phase,session,distance,end,arrows) in caso di successo
	 */
		private function makeHeader()
		{
		/*
		 * Devono essere presenti i figli:
		 * - direction: 'Input'
		 * - tour_code: codice della gara da importare
		 * - phase: nome della fase da importare
		 * - session: sessione da importare
		 * - end: volee da importare
		 * - arrows: numero di frecce
		 */

		/*
		 * <direction>
		 */
			$directions=$this->xmlHeader->getElementsByTagName('direction');

			if ($directions->length!=1)
			{
				return 1;
			}

			$dirNode=$directions->item(0);
			/*if ($dirNode->parentNode!=$this->xmlHeader)
			{
				return 1;
			}*/

			$direction=$dirNode->textContent;
			if ($direction!='Input')
			{
				return 1;
			}

		/*
		 * <tour_code>
		 */
			$tourCodes=$this->xmlHeader->getElementsByTagName('tour_code');

			if ($tourCodes->length!=1)
			{
				return 1;
			}
			$tourCodeNode=$tourCodes->item(0);
			/*if ($tourCodeNode->parentNode!=$this->xmlHeader)
			{
				return 1;
			}*/

			$tour_code=$tourCodeNode->textContent;

		// cerco l'id del torneo in base al codice gara
			$toId=getIdFromCode($tour_code);
			if ($toId==0)
			{
				return 1;
			}

		/*
		 * <phase>
		 */
			$phases=$this->xmlHeader->getElementsByTagName('phase');

			if ($phases->length!=1)
			{
				return 1;
			}

			$phaseNode=$phases->item(0);
			/*if ($phaseNode->parentNode!=$this->xmlHeader)
			{
				return 1;
			}*/

			$phase=$phaseNode->textContent;

		/*
		 * <session>
		 */
			$sessions=$this->xmlHeader->getElementsByTagName('session');

			if ($sessions->length!=1)
			{
				return 1;
			}

			$sessionNode=$sessions->item(0);
			/*if ($sessionNode->parentNode!=$this->xmlHeader)
			{
				return 1;
			}*/

			$session=$sessionNode->textContent;
		/*
		 * <end>
		 */
			$ends=$this->xmlHeader->getElementsByTagName('end');

			if ($ends->length!=1)
			{
				return 1;
			}

			$endNode=$ends->item(0);
			/*if ($endNode->parentNode!=$this->xmlHeader)
			{
				return 1;
			}*/

			$end=$endNode->textContent;


		/*
		 * <distance>
		 */

			$distances=$this->xmlHeader->getElementsByTagName('distance');
			if ($distances->length!=1)
			{
				return 1;
			}
			$distanceNode=$distances->item(0);
			$distance=$distanceNode->textContent;

		/*
		 * <arrows>
		 */
		// NOTA: fa schifo il nome ma è per coerenza
			$arrowss=$this->xmlHeader->getElementsByTagName('arrows');
			if ($arrowss->length!=1)
			{
				return 1;
			}

			$arrowsNode=$arrowss->item(0);
			/*if ($arrowsNode->parentNode!=$this->xmlHeader)
			{
				return 1;
			}*/

			$arrows=$arrowsNode->textContent;

			return array($toId,$phase,$session,$distance,$end,$arrows);
		}

		private function setSession($v)
		{
			$this->session=$v;
		}

		private function getSession()
		{
			return $this->session;
		}

		private function setEnd($v)
		{
			$this->end=$v;
		}

		private function getEnd()
		{
			return $this->end;
		}

		private function setDistance($v)
		{
			$this->distance=$v;
		}

		private function getDistance()
		{
			return $this->distance;
		}

		private function setArrows($v)
		{
			$this->arrows=$v;
		}

		private function getArrows()
		{
			return $this->arrows;
		}

		private function setMaxArrows($v)
		{
			$this->maxArrows=$v;
		}

		private function getMaxArrows()
		{
			return $this->maxArrows;
		}

	/**
	 * Verifica che  l'entry $e sia corretto
	 *
	 * @param DOMNode $e: nodo entry da verificare
	 *
	 * @return boolean: true se ok, false altrimenti
	 */
		private function verifyEntry($e)
		{
		// $e deve avere figli
			if (!$e->hasChildNodes())
				return false;
		/*
		 * Devono essere presenti i figli:
		 * - target (oppure match)
		 * - valid
		 * - $this->arrows nodi arrow (durante l'import verifico che ci sia l'attributo num)
		 * - end_total
		 */
			if ($this->getSession()!='MATCH')
			{
				if ($e->getElementsByTagName('target')->length!=1)
					return false;

			}
			else
			{
				$matches=$e->getElementsByTagName('match');
				if ($matches->length!=1)
					return false;

				$x=$matches->item(0)->getElementsByTagName('event');
				if ($x->length!=1)
					return false;

				$x=$matches->item(0)->getElementsByTagName('position');
				if ($x->length!=1)
					return false;

				$x=$matches->item(0)->getElementsByTagName('cur_phase');
				if ($x->length!=1)
					return false;
			}

			if ($e->getElementsByTagName('valid')->length!=1)
				return false;
			if ($e->getElementsByTagName('end_total')->length!=1)
				return false;

			if ($e->getElementsByTagName('arrow')->length!=$this->getArrows())
				return false;

			return true;
		}

	/**
	 * Importa le sezioni <entry> verificando che siano nella forma giusta
	 *
	 */
		public function import()
		{
		// il vettore contiene gli indici dei nodi entry non importati
			$badEntries=array();

		// contatore dei nodi importati
			$importedEntries=0;

			$G=$this->G;
			$X=$this->X;

			if (debug)
			{
				print 'Phase: ' . $this->getPhase() . '<br>';
				print 'Session: ' . $this->getSession() . '<br>';
				print 'Distance: ' . $this->getDistance() . '<br>';
				print 'End: ' . $this->getEnd() . '<br>';
				print 'Arrows: ' . $this->getArrows() . '<br>';
				print 'MaxArrows: ' . $this->getMaxArrows() . '<br>';
			}

		// Metodo da chimare dopo tutti gli import (in base alla fase importata)
			$postImportMethod='';

		// parametro per il metodo $postImportMethod
			$postParam=null;

			switch ($this->getPhase())
			{
				case ARF::QUALIFICATION:
					$postImportMethod='resetQualsShootOff';
					break;
				case ARF::ELIMINATION:
					$postImportMethod='resetElimShootOff';
					break;
				case ARF::INDIVIDUAL_FINAL:
				case ARF::TEAM_FINAL:
					$postImportMethod='nextPhase';
					break;
			}

		// collezione di entry
			$entries=$this->xmlDoc->getElementsByTagName('entry');

			$c=$entries->length;
			if ($c==0)
			{
				$this->setError(1);
				return false;
			}

		// il vettore contiene gli id delle persone a cui occorre azzerrare lo shootoff (QUALIFICATION e ELIMINATION)
			$shootOffIds=array();

		// Vettore delle chiavi per il passaggio di fase (INDIVIDULAL_FINAL TEAM_FINAL)
			$keysNextPhase=array();

			for ($i=0;$i<$c;++$i)
			{
				$arrowString = '##@@##';

				$e=$entries->item($i);

			// verifico la forma del nodo
				if (!$this->verifyEntry($e))
				{
					$badEntries[]=$i;
					continue;
				}

				$targetNo=null;
				$position=null;
				$matchNo=null;
				$curPhase=null;

				if ($this->getSession()!='MATCH')
					$targetNo=str_pad($e->getElementsByTagName('target')->item(0)->textContent,(TargetNoPadding+1),'0',STR_PAD_LEFT);
				else
				{
					$position=$e->getElementsByTagName('position')->item(0)->textContent;
					$event=$e->getElementsByTagName('event')->item(0)->textContent;
					$curPhase=$e->getElementsByTagName('cur_phase')->item(0)->textContent;
				}


				$valid=$e->getElementsByTagName('valid')->item(0)->textContent;
				$endScore=$e->getElementsByTagName('end_total')->item(0)->textContent;


				$points=array();
				$arrows=$e->getElementsByTagName('arrow');
				foreach ($arrows as $a)
				{
				/*
				 * Per ora se non c'è l'attributo num, la freccia viene ignorata
				 */
					/*$index=$a->getAttribute('num');
					if (is_numeric($index))
						$points[$index]=$a->textContent;*/
				/*
				 * Edit del precedente commento:
				 * per ora anche se non c'è l'attributo num la freccia viene importata
				 */
					$points[]=$a->textContent;
				}

			/*
			 * Sicuramente ho il numero corretto di frecce (lo ha verificato $this->verifyNode)
			 * quindi posso procedere
			 */

			// Cerco la riga nella tabella appropriata
				$query="";

				switch ($this->getPhase())
				{
					case ARF::QUALIFICATION:
						$query
							= "SELECT "
								. "QuId AS AthId,EnStatus AS AthStatus,QuD" . $this->getDistance() . "ArrowString AS ArrowString "
							. "FROM "
								. "Qualifications INNER JOIN Entries ON QuId=EnId AND EnTournament=" . $this->getTourId() . " "
							. "WHERE "
								. "QuSession=" . StrSafe_DB($this->getSession()) . " AND QuTargetNo=" . StrSafe_DB($this->getSession() . $targetNo) . " ";
						break;
					case ARF::ELIMINATION:
						$query
							= "SELECT "
								. "ElId AS AthId,EnStatus AS AthStatus,ElArrowString AS ArrowString "
							. "FROM "
								. "Eliminations INNER JOIN Entries ON ElId=EnId AND EnTournament=" . $this->getTourId() . " "
							. "WHERE "
								. "ElElimPhase=" . StrSafe_DB($this->getSession()-1) .  " AND ElTargetNo=" . StrSafe_DB($targetNo) . " ";
						break;
					case ARF::INDIVIDUAL_FINAL:
						if ($this->getSession()!='MATCH')
						{
							$query
								= "SELECT "
									. "FinAthlete AS AthId,IF(FinAthlete!=0,0,9) AS AthStatus,FinMatchNo AS MatchNo,GrPhase,FinEvent AS Event,FSTarget,FinArrowString AS ArrowString, CONCAT(FSScheduledDate,' ',FSScheduledTime) AS Scheduled "
								. "FROM "
									. "Finals INNER JOIN FinSchedule ON FinMatchNo=FSMatchNo AND FinTournament=FSTournament "
									. "AND FinEvent=FSEvent AND FSTeamEvent=0 "
									. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
								. "WHERE "
									. "FinTournament=" . $this->getTourId() . " AND "
									. "FSTarget='" . substr($targetNo,0,-1) . "' AND "
									. "CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($this->getSession()) . " "
								. "ORDER BY "
									. "FinEvent,FinMatchNo ASC "
								. "LIMIT "
									. (substr($targetNo,-1)=='A' ? 0 : 1) . ",1 ";
							//print $query;exit;
						}
						else
						{
							$query
								= "SELECT "
									. "FinAthlete AS AthId,IF(FinAthlete!=0,0,9) AS AthStatus,FinMatchNo AS MatchNo,GrPhase,FinEvent AS Event,FSTarget,FinArrowString AS ArrowString "
								. "FROM "
									. "Finals INNER JOIN FinSchedule ON FinMatchNo=FSMatchNo AND FinTournament=FSTournament "
									. "AND FinEvent=FSEvent AND FSTeamEvent=0 "
									. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
								. "WHERE "
									. "FinTournament=" . $this->getTourId() . " AND "
									. "GrPosition=" . StrSafe_DB($position) . " AND "
									. "GrPhase=" . StrSafe_DB($curPhase) . " AND "
									. "FinEvent=" . StrSafe_DB($event) . " "
								. "ORDER BY "
									. "FinEvent,FinMatchNo ASC ";
						}
						break;
					case ARF::TEAM_FINAL:
						if ($this->getSession()!='MATCH')
						{
							$query
								= "SELECT TfTeam AS AthId,IF(TfTeam!=0,0,9) AS AthStatus,TfMatchNo AS MatchNo,GrPhase,TfEvent AS Event,FSTarget,TfArrowString AS ArrowString "
								. "FROM TeamFinals INNER JOIN FinSchedule ON TfMatchNo=FSMatchNo AND TfTournament=FSTournament "
								. "AND TfEvent=FSEvent AND FSTeamEvent=1 "
								. "INNER JOIN Grids ON TfMatchNo=GrMatchNo "
								. "WHERE TfTournament=" . $this->getTourId() . " AND "
								. "FSTarget='" . substr($targetNo,0,-1) . "' AND "
								. "CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($this->getSession()) . " "
								. "ORDER BY TfEvent,TfMatchNo ASC "
								. "LIMIT " . (substr($targetNo,-1)=='A' ? 0 : 1) . ",1";
						}
						else
						{
							$query
								= "SELECT TfTeam AS AthId,IF(TfTeam!=0,0,9) AS AthStatus,TfMatchNo AS MatchNo,GrPhase,TfEvent AS Event,FSTarget,TfArrowString AS ArrowString "
								. "FROM TeamFinals INNER JOIN FinSchedule ON TfMatchNo=FSMatchNo AND TfTournament=FSTournament "
								. "AND TfEvent=FSEvent AND FSTeamEvent=1 "
								. "INNER JOIN Grids ON TfMatchNo=GrMatchNo "
								. "WHERE TfTournament=" . $this->getTourId() . " AND "
								. "GrPosition=" . StrSafe_DB($position) . " AND "
								. "GrPhase=" . StrSafe_DB($curPhase) . " AND "
								. "TfEvent=" . StrSafe_DB($event) . " "
								. "ORDER BY TfEvent,TfMatchNo ASC ";
						}
						break;
				}

				if (debug) print $query . '<br><br>';

				$rs=safe_r_sql($query);

				$myRow=null;
				$good=false;	// true se il nodo è ok
				if ($rs)
				{
				// non trovo il bersaglio in db
					if (safe_num_rows($rs)==0)
					{
						if ($valid==1)	// il file dice che qualcuno ha tirato
						{
							$badEntries[]=$i;
							continue;
						}
						else	// anche il file dice che non c'è nessuno
						{
							continue;
						}
					}
					elseif (safe_num_rows($rs)==1)	// il bersaglio è unico
					{
						$myRow=safe_fetch($rs);

					// la tabella dice che tira
						if ($myRow->AthStatus <= 1)
						{
						// ma il file dice no
							if ($valid==0)
							{
								$badEntries[]=$i;
								continue;
							}
							elseif ($valid==1)	// anche il file dice si
							{
								$arrowString=str_pad($myRow->ArrowString,$this->getMaxArrows(),' ',STR_PAD_RIGHT);
							}
						}
						else	// la tabella dice che non tira
						{
							// il file è d'accordo
							if ($valid==0)
							{
								continue;
							}
							else	// il file dice che c'è qualcuno
							{
								$badEntries[]=$i;
								continue;
							}
						}
					}
				}
				else
				{
					$badEntries[]=$i;
					continue;
				}

			

			// Creo la nuova arrowstring e verifico il totale della volee
				$subArrowString='';
				$totEnd=0;

				foreach ($points as $value)
				{
					$value2write=' ';	// inizializzato a blank

					if ($value!='#')
					{
						$totEnd+=($value!='M' && $value!='m' ? $value : 0);
						$value2write=GetLetterFromPrint($value);
					}

				// Questa condizione in realtà sarebbe un errore
					if ($value2write=='')
					{
						$value2write=' ';
					}

					$subArrowString.=$value2write;
				}

			// se il totale dichiarato in end_total non è coerente con i punti passati, il nodo non viene importato
				if ($endScore!=$totEnd)
				{
					$badEntries[]=$i;
					continue;
				}

				if (debug) print '..'.$subArrowString.'..(' . strlen($subArrowString) . ')<br>';

				$Ip = $this->getArrows()*($this->getEnd()-1);
				$Fp = $Ip+($this->getArrows()-1);

				$arrowString = substr_replace($arrowString,$subArrowString,$Ip,strlen($subArrowString));
				//print $arrowString . '<br><br>';
				if (debug) print '..'.$arrowString.'..<br><br>';

			// Posso calcolare i punti
				$score=0;
				$gold=0;
				$xnine=0;
				if ($this->getPhase()==ARF::QUALIFICATION || $this->getPhase()==ARF::ELIMINATION)
					list($score,$gold,$xnine)=ValutaArrowStringGX($arrowString,$G,$X);
				else
					$score=ValutaArrowString($arrowString);

				if (debug)
				{
					print 'Score: ' . $score . '<br>';
					print 'Gold: ' . $gold . '<br>';
					print 'Xnine: ' . $xnine . '<br><br>';
				}


			// posso fare l'update
				$query="";
				switch ($this->getPhase())
				{
					case ARF::QUALIFICATION:
						$query
							= "UPDATE "
								. "Qualifications "
							. "SET "
								. "QuD" . $this->getDistance() . "Score=" . StrSafe_DB($score) . ","
								. "QuD" . $this->getDistance() . "Gold=" . StrSafe_DB($gold) . ","
								. "QuD" . $this->getDistance() . "Xnine=" . StrSafe_DB($xnine) . ", "
								. "QuD" . $this->getDistance() . "ArrowString=" . StrSafe_DB($arrowString) . ", "
								. "QuScore=QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score+QuD8Score,"
								. "QuGold=QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold,"
								. "QuXnine=QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine, "
								. "QuTimestamp=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
							. "WHERE "
								. "QuId=" . StrSafe_DB($myRow->AthId) . " ";
						if (debug) print $query . '<br><br>';
						$rs=safe_w_sql($query);

						if ($rs)
						{
							++$importedEntries;
							$shootOffIds[]=StrSafe_DB($myRow->AthId);
						}
						else
						{
							$badEntries[]=$i;
							continue;
						}
						break;
					case ARF::ELIMINATION:
						$query
							= "UPDATE "
								. "Eliminations "
							. "SET "
								. "ElScore=" . Strsafe_DB($score) . ","
								. "ElGold=" . Strsafe_DB($gold) . ","
								. "ElXnine=" . Strsafe_DB($xnine) . ", "
								. "ElArrowString=" . StrSafe_DB($arrowString) . " "
							. "WHERE "
								. "ElId=" . StrSafe_DB($myRow->AthId) . " AND ElElimPhase=" . StrSafe_DB($this->getSession()-1) . " ";
						//print $query . '<br><br>';
						$rs=safe_w_sql($query);
						if ($rs)
						{
							++$importedEntries;
							$shootOffIds[]=StrSafe_DB($myRow->AthId);
						}
						else
						{
							$badEntries[]=$i;
							continue;
						}
						break;
					case ARF::INDIVIDUAL_FINAL:
						$query
							= "UPDATE Finals SET "
							. "FinScore=" . StrSafe_DB($score) . ","
							. "FinArrowString=" . StrSafe_DB($arrowString) . ","
							. "FinTie=0,"
							. "FinTiebreak=NULL,"
							. "FinTiePosition=NULL "
							. "WHERE FinMatchNo=" . StrSafe_DB($myRow->MatchNo) . " AND "
							. "FinEvent=" . StrSafe_DB($myRow->Event) . " AND FinTournament=" . $this->getTourId() . " ";
						$rs=safe_w_sql($query);
						//print $query . '<br><br>';
						if ($rs)
						{
							++$importedEntries;
							$keysNextPhase[$myRow->GrPhase]["'" . $myRow->Event . "'"]=1;
						}
						else
						{
							$badEntries[]=$i;
							continue;
						}
						break;
					case ARF::TEAM_FINAL:
						$query
							= "UPDATE TeamFinals SET "
							. "TfScore=" . StrSafe_DB($score) . ","
							. "TfArrowString=" . StrSafe_DB($arrowString) . ","
							. "TfTie=0,"
							. "TfTiebreak=NULL,"
							. "TfTiePosition=NULL "
							. "WHERE TfMatchNo=" . StrSafe_DB($myRow->MatchNo) . " AND "
							. "TfEvent=" . StrSafe_DB($myRow->Event) . " AND TfTournament=" . $this->getTourId() . " ";
						$rs=safe_w_sql($query);
						//print $query . '<br><br>';
						if ($rs)
						{
							++$importedEntries;
							$keysNextPhase[$myRow->GrPhase]["'" . $myRow->Event . "'"]=1;
						}
						else
						{
							$badEntries[]=$i;
							continue;
						}
						break;
				}
			}
			//exit;
		// Provo a post-processare i dati importati
			switch ($this->getPhase())
			{
				case ARF::QUALIFICATION:
				case ARF::ELIMINATION:
					$postParam=$shootOffIds;
					break;
				case ARF::INDIVIDUAL_FINAL:
				case ARF::TEAM_FINAL:
					$postParam=$keysNextPhase;
					break;
			}

			$postError=false;	// true se la prossima chiamata ritorna un errore
			if ($this->getPhase()==ARF::QUALIFICATION || $this->getPhase()==ARF::ELIMINATION)
			{
				call_user_func(array($this,$postImportMethod),$postParam,&$postError);
			}
			elseif ($this->getPhase()==ARF::INDIVIDUAL_FINAL || $this->getPhase()==ARF::TEAM_FINAL)
			{
				$team=($this->getPhase()==ARF::INDIVIDUAL_FINAL ? 0 : 1);
				call_user_func(array($this,$postImportMethod),$postParam,$team,&$postError);
			}

			return array($this->getTourCode(),$importedEntries,$badEntries,$postError);
		}

	/**
	 * Viene chiamata per azzerare gli shootoff delle qualifiche
	 *
	 * @param array int $ids: vettore degli id di cui bisogna azzerare lo shootoof
	 * @param boolean &$error: true se il post processo genera un errore
	 */
		private function resetQualsShootOff($ids,&$error)
		{
		/*
		 * Se non ci sono ids buoni non ho shootoff, non ho la generazione di nulla
		 * e il post-processing fallisce
		 */
			if (count($ids)==0)
			{
				$error=true;
				return;
			}

		// shootoff
			$query
				= "UPDATE Events INNER JOIN EventClass ON EvCode=EcCode AND (EvTeamEvent='0' OR EvTeamEvent='1') AND EcTournament=" . $this->getTourId() . " "
				. "INNER JOIN Entries ON EcDivision=EnDivision AND EcClass=EnClass  AND EnId IN(" . join(',',$ids)  . ") "
				. "SET EvShootOff='0' "
				. "WHERE (EvTeamEvent='0' OR EvTeamEvent='1') AND EvTournament=" . $this->getTourId() . " ";

			$rs=safe_w_sql($query);
			set_qual_session_flags();

			if (debug) print $query . '<br><br>';

		// rank e squadre
			$query
				= "SELECT CONCAT(EnDivision,EnClass) AS MyEvent, EnCountry as MyTeam,EnDivision,EnClass "
				. "FROM Entries "
				. "WHERE EnId IN(" . join(',',$ids) . ") AND EnTournament=" . $this->getTourId() . " ";
			$rs=safe_r_sql($query);

			if (debug) print $query . '<br><br>';

			if (safe_num_rows($rs)>0)
			{
				while ($rr=safe_fetch($rs))
				{
					$Evento=$rr->MyEvent;
					$Category = $rr->MyEvent;
					$Societa = $rr->MyTeam;
					$Div = $rr->EnDivision;
					$Cl = $rr->EnClass;

				// rank distanza
					if (CalcQualRank($this->getDistance(),$Evento))
					{
						$error=true;
					}
					else
					{
					// rank totale
						if (CalcQualRank(0,$Evento))
							$error=true;
						else
						{
						// squadre
							if (MakeTeams($Societa, $Category))
								$error=true;
							else
							{
								if (MakeTeamsAbs($Societa,$Div,$Cl))
									$error=true;
							}
						}
					}
				}
			}
			else
				$error=true;
		}

		private function resetElimShootOff($ids,&$error)
		{
		/*
		 * Se non ci sono ids buoni non ho shootoff, non ho la generazione di nulla
		 * e il post-processing fallisce
		 */
			if (count($ids)==0)
			{
				$error=true;
				return;
			}

		// shootoff
			$query
				= "UPDATE Events INNER JOIN EventClass ON EvCode=EcCode AND (EvElim1!='0' OR EvElim2!='0') AND EcTournament=" . $this->getTourId() . " "
				. "INNER JOIN Entries ON EcDivision=EnDivision AND EcClass=EnClass  AND EnId IN(" . join(',',$ids)  . ") "
				. "SET EvE" . $this->getSession() . "ShootOff='0' "
				. "WHERE (EvElim1!='0' OR EvElim2!='0') AND EvTournament=" . $this->getTourId() . " ";
			//print $query . '<br>';
			$rs=safe_w_sql($query);

			if (!$rs)
				$error=true;
		}

	/**
	 * Viene chiamata per fare i passaggi di fase delle finali
	 *
	 * @param array int $keys: vettore delle chiavi per il passaggio di fase
	 * @param array int $team: 0 individuali 1 squadre
	 * @param boolean &$error: true se il post processo genera un errore
	 */
		private function nextPhase($keys,$team,&$error)
		{
		/*
		 * Se non ci sono $keys buone non ho passaggi di fase e il post-processing fallisce
		 */
			if (count($keys)==0)
			{
				$error=true;
				return;
			}


			foreach ($keys as $key => $value)
			{
				$events = implode(',',array_keys($value));

				$query="";
				if ($team==0)
				{
					$query
						= "SELECT GrPhase AS Phase,GrPosition AS Position,	/* Grids*/ "
						. "FinMatchNo AS MatchNo,FinEvent AS Event, FinAthlete AS Id, FinScore AS Score,FinTie AS Tie, "
						. "IF(GrPhase>2, FLOOR(FinMatchNo/2),FLOOR(FinMatchNo/2)-2) AS NextMatchNo "
						. "FROM Finals INNER JOIN Grids ON FinMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($key) . " "
						. "LEFT JOIN Entries ON FinAthlete=EnId AND FinEvent IN(" . $events . ') '
						. "WHERE FinTournament=" . $this->getTourId() . " "
						. "ORDER BY FinEvent, NextMatchNo ASC, FinScore DESC, FinTie DESC ";
				}
				else
				{
					$query
						= "SELECT GrPhase AS Phase,GrPosition AS Position,	/* Grids*/ "
						. "TfTeam AS Id,TfMatchNo AS MatchNo,TfEvent AS Event, TfScore AS Score,TfTie AS Tie, "
						. "IF(GrPhase>2, FLOOR(TfMatchNo/2),FLOOR(TfMatchNo/2)-2) AS NextMatchNo "
						. "FROM TeamFinals INNER JOIN Grids ON TfMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($key) . " "
						. "WHERE TfTournament=" . $this->getTourId() . " AND TfEvent IN (" . $events . ") "
						. "ORDER BY TfEvent, NextMatchNo ASC, TfScore DESC, TfTie DESC ";
				}
				//print $query . '<br>';exit;
				$rs=safe_r_SQL($query);

				$myNextMatchNo = 'xx';
				$row=array();

				if (safe_num_rows($rs)>0)
				{
					while ($row0=safe_fetch($rs))
					{
						$row1=safe_fetch($rs);
						$athProp = 0;
						$myUpQuery = "";

						if ($row0->Phase>=2)
						{
							if (($row0->Score>0 || $row0->Tie>0) &&
								($row0->Score!=$row1->Score || $row0->Tie!=$row1->Tie))
							{
								if ($team==0)
								{
									$myUpQuery = "UPDATE Finals SET ";
									$myUpQuery.= "FinAthlete =" . StrSafe_DB($row0->Id) . ", ";
									$myUpQuery.= "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
									$myUpQuery.= "WHERE FinEvent=" . StrSafe_DB($row0->Event) . " AND FinMatchNo=" . StrSafe_DB($row0->NextMatchNo) . " AND FinTournament=" . $this->getTourId() . " ";

								}
								else
								{
									$myUpQuery = "UPDATE TeamFinals SET ";
									$myUpQuery.= "TfTeam =" . StrSafe_DB($row0->Id) . ", ";
									$myUpQuery.= "TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
									$myUpQuery.= "WHERE TfEvent=" . StrSafe_DB($row0->Event) . " AND TfMatchNo=" . StrSafe_DB($row0->NextMatchNo) . " AND TfTournament=" . $this->getTourId() . " ";

								}
								//print $myUpQuery . '<br><br>';
								$rsUp=safe_w_sql($myUpQuery);
								$athProp=$row0->Id;
							}
							else
							{
								if ($team==0)
								{
									$myUpQuery = "UPDATE Finals SET ";
									$myUpQuery.= "FinAthlete ='0', ";
									$myUpQuery.= "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
									$myUpQuery.= "WHERE FinEvent=" . StrSafe_DB($row0->Event) . " AND FinMatchNo=" . StrSafe_DB($row0->NextMatchNo) . " AND FinTournament=" . $this->getTourId() . " ";

								}
								else
								{
									$myUpQuery = "UPDATE TeamFinals SET ";
									$myUpQuery.= "TfTeam ='0', ";
									$myUpQuery.= "TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
									$myUpQuery.= "WHERE TfEvent=" . StrSafe_DB($row0->Event) . " AND TfMatchNo=" . StrSafe_DB($row0->NextMatchNo) . " AND TfTournament=" . $this->getTourId() . " ";

								}

								$rsUp=safe_w_sql($myUpQuery);
							}
						}

						if ($row1->Phase==2)
						{
							if (($row1->Score>0 || $row1->Tie>0) &&
								($row0->Score!=$row1->Score || $row0->Tie!=$row1->Tie))
							{
								if ($team==0)
								{
									$myUpQuery = "UPDATE Finals SET ";
									$myUpQuery.= "FinAthlete =" . StrSafe_DB($row1->Id) . ", ";
									$myUpQuery.= "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
									$myUpQuery.= "WHERE FinEvent=" . StrSafe_DB($row1->Event) . " AND FinMatchNo=" . StrSafe_DB(($row1->NextMatchNo+2)) . " AND FinTournament=" . $this->getTourId() . " ";
								}
								else
								{
									$myUpQuery = "UPDATE TeamFinals SET ";
									$myUpQuery.= "TfTeam =" . StrSafe_DB($row1->Id) . ", ";
									$myUpQuery.= "TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
									$myUpQuery.= "WHERE TfEvent=" . StrSafe_DB($row1->Event) . " AND TfMatchNo=" . StrSafe_DB(($row1->NextMatchNo+2)) . " AND TfTournament=" . $this->getTourId() . " ";

								}
								$rsUp=safe_w_sql($myUpQuery);
								$athProp=$Row1->FinAthlete;
							}
							else
							{
								if ($team==0)
								{
									$myUpQuery = "UPDATE Finals SET ";
									$myUpQuery.= "FinAthlete =" . StrSafe_DB($row1->Id) . ", "; // 0
									$myUpQuery.= "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
									$myUpQuery.= "WHERE FinEvent=" . StrSafe_DB($row1->Event) . " AND FinMatchNo=" . StrSafe_DB(($row1->NextMatchNo+2)) . " AND FinTournament=" . $this->getTourId() . " ";

								}
								else
								{
									$myUpQuery = "UPDATE TeamFinals SET ";
									$myUpQuery.= "TfTeam =" . StrSafe_DB($row1->Id) . ", "; // 0
									$myUpQuery.= "TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
									$myUpQuery.= "WHERE TfEvent=" . StrSafe_DB($row1->Event) . " AND TfMatchNo=" . StrSafe_DB(($row1->NextMatchNo+2)) . " AND TfTournament=" . $this->getTourId() . " ";

								}

								$rsUp=safe_w_sql($myUpQuery);
							}

						// Devo fare la propagazione del nome se il matchno>7 (fase>2)
							$oldId=($athProp!=0 ? StrSafe_DB($row1->Id) : StrSafe_DB($row0->Id) . ',' . StrSafe_DB($row1->Id));

							$update = "";

							if ($team==0)
							{
								$update
									= "UPDATE Finals SET "
									. "FinAthlete=" . StrSafe_DB($athProp) . " "
									. "WHERE FinAthlete IN (" . $oldId . ") AND FinTournament=" . $this->getTourId() . " AND FinEvent=" . StrSafe_DB($row0->Event) . " AND FinMatchNo<" . StrSafe_DB($row0->NextMatchNo) . " AND (FinScore<>0 OR FinTie<>0) ";
							}
							else
							{
								$update
									= "UPDATE TeamFinals SET "
									. "TfTeam=" . StrSafe_DB($athProp) . " "
									. "WHERE TfTeam IN (" . $oldId . ") AND TfTournament=" . $this->getTourId() . " AND TfEvent=" . StrSafe_DB($row0->Event) . " AND TfMatchNo<" . StrSafe_DB($row0->NextMatchNo) . " AND (TfScore<>0 OR TfTie<>0) ";

							}
							//print $update . '<br><br>';
							$rsProp = safe_w_sql($update);


							if (safe_w_affected_rows()>0)
							{
								$error=false;
							}
						}
					}
				}
			}
		}
	}
?>