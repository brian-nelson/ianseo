<?php
	require_once('Common/ARF/ARF.class.php');
	require_once('Common/MySql2XML.class.php');
	define('debug',false);

	class ARFOutput extends ARF
	{
		private $totalEnds=null;
		private $maxTotal=null;

		private $options=null;

	/**
	 * Contiene il recordset della query eseguita dal costruttore.
	 * @var safe_r_Result (procedurale)
	 */
		private $rs=null;

	/**
	 * Inizializza il documento di output
	 *
	 * @param <b>int $tourId</b>: id torneo
	 * @param <b>string $phase</b>: fase presa in considerazione
	 * @param <b>mixed array $options</b>: opzioni
	 *
	 * <br/><br/>
	 * <b>$options</b> assume le seguenti forme:
	 * <ul>
	 *   <li>
	 *     array($session,$dist) se $phase vale QUALIFICATION.<br/>
	 *   </li>
	 *   <li>
	 *     array($round) se $phase vale ELIMINATION.<br/>
	 *   </li>
	 *   <li>
	 *     array($scheduling) se $phase vale INDIVIDUAL_FINAL.<br/>
	 *   </li>
	 *   <li>
	 *     array($scheduling) se $phase vale TEAM_FINAL
	 *   </li>
	 * </ul>
	 * <br/><br/>
	 * <b>$session</b> indica la sessione da esportare,<b>$dist</b> la distanza.<br/>
	 * Se <b>$session</b> vale -1 significa tutte.<br/>
	 * Se <b>$dist</b> vale -1 significa tutte, 0 nessuna.<br/>
	 * <b>$round</b> indica il girone da esportare.<br/>
	 * Se <b>$round</b> vale -1 significa tutti.<br/>
	 * <b>$scheduling</b> è la programmazione dell'orario. Sono ammessi i caratteri % e _
	 * <br/><br/>
	 * <b>Nessun'altra combinazione è ammessa.</b>
	 *
	 * @todo: gestire il controllo sulla forma di $options
	 */
		public function __construct($tourId,$phase,$options)
		{
		// Inizializzo il doc xml con la root
			$this->xmlDoc=new DOMDocument('1.0','UTF-8');
			$this->xmlRoot=$this->xmlDoc->createElement('response');
			$this->xmlDoc->appendChild($this->xmlRoot);

		// version
			$xmlVer=$this->xmlDoc->createElement('version',ARF::ARF_OUTPUT_VERSION);
			$this->xmlRoot->appendChild($xmlVer);

		// chiamo il costruttore padre per esplicitare l'init
			parent::__construct($tourId,$phase);

			$this->makeHeader();

		// Inizializzo gli altri valori
			$this->setDirection('Output');

			$totalEndsField='';
			$maxTotalField='';

			$makeRS='';

		// In base a $phase decido i campi da estrarre
			/*switch ($phase)
			{
				case ARF::QUALIFICATION:
					$totalEndsField='TtNumEnds';
					$maxTotalField='TtMaxDistScore';
					$makeRS='rsQualification';
					break;
				case ARF::ELIMINATION:
					$totalEndsField='\'#\'';
					$maxTotalField='TtMaxDistScore';
					$makeRS='rsElimination';
					break;
				case ARF::INDIVIDUAL_FINAL:
					$totalEndsField='\'#\'';
					$maxTotalField='TtMaxFinIndScore';
					$makeRS='rsIndividualFinal';
					break;
				case ARF::TEAM_FINAL:
					$totalEndsField='\'#\'';
					$maxTotalField='TtMaxFinTeamScore';
					$makeRS='rsTeamFinal';
					break;
			}

			$query
				= "SELECT "
					. $totalEndsField . " AS TotalEnds," . $maxTotalField . " AS MaxTotal "
				. "FROM "
					. "Tournament*Type INNER JOIN Tournament ON TtId=ToType "
				. "WHERE "
					. "ToId=" . $this->getTourId() . " ";*/
			
			switch ($phase)
			{
				case ARF::QUALIFICATION:
					$totalEndsField='ToNumEnds';
					$maxTotalField='ToMaxDistScore';
					$makeRS='rsQualification';
					break;
				case ARF::ELIMINATION:
					$totalEndsField='\'#\'';
					$maxTotalField='ToMaxDistScore';
					$makeRS='rsElimination';
					break;
				case ARF::INDIVIDUAL_FINAL:
					$totalEndsField='\'#\'';
					$maxTotalField='ToMaxFinIndScore';
					$makeRS='rsIndividualFinal';
					break;
				case ARF::TEAM_FINAL:
					$totalEndsField='\'#\'';
					$maxTotalField='ToMaxFinTeamScore';
					$makeRS='rsTeamFinal';
					break;
			}

			$query
				= "SELECT "
					. $totalEndsField . " AS TotalEnds," . $maxTotalField . " AS MaxTotal "
				. "FROM "
					. "Tournament "
				. "WHERE "
					. "ToId=" . $this->getTourId() . " ";
			$rs=safe_r_sql($query);

			if (safe_num_rows($rs)==1)
			{
				$myRow=safe_fetch($rs);

				$this->setTotalEnds($myRow->TotalEnds);
				$this->setMaxTotal($myRow->MaxTotal);

				$this->options=$options;

				$this->add2header();
				call_user_func(array($this,$makeRS),array());
				//$this->addData();

				$x=new MySql2XML($this->rs,$this->xmlDoc,$this->xmlRoot);

				// Non serve perchè gli oggetti vengono assegnati per referenza
				//$this->xmlDoc=$x->getXmlDoc();
			}
			else
				$this->setError(1);
		}

	/**
	 * Crea l'header del documento xml
	 */
		private function makeHeader()
		{
			$xmlHeader=$this->xmlDoc->createElement('header');

		// tourCode
			$tmp=$this->xmlDoc->createElement('tour_code',$this->getTourCode());
			$xmlHeader->appendChild($tmp);
		// tourname
			$tmp=$this->xmlDoc->createElement('tour_name',$this->getTourName());
			$xmlHeader->appendChild($tmp);
		// phase
			$tmp=$this->xmlDoc->createElement('phase',$this->getPhase());
			$xmlHeader->appendChild($tmp);
		// type
			$tmp=$this->xmlDoc->createElement('type',$this->getType());
			$xmlHeader->appendChild($tmp);

			$this->xmlRoot->appendChild($xmlHeader);
		}

		private function setTotalEnds($v)
		{
			$this->totalEnds=$v;
		}

		private function getTotalEnds()
		{
			return $this->totalEnds;
		}

		private function setMaxTotal($v)
		{
			$this->maxTotal=$v;
		}

		private function getMaxTotal()
		{
			return $this->maxTotal;
		}

	/**
	 * Aggiunge all'header le info di output
	 *
	 */
		private function add2header()
		{
			$headers=$this->xmlDoc->getElementsByTagName('header');

		// Direzione
			$tmp=$this->xmlDoc->createElement('direction',$this->getDirection());
			$headers->item(0)->appendChild($tmp);
		// Numero di volee
			if ($this->getPhase()==ARF::QUALIFICATION)
			{
				$tmp=$this->xmlDoc->createElement('total_ends',$this->getTotalEnds());
				$headers->item(0)->appendChild($tmp);
			}
		// Totale massimo
			$tmp=$this->xmlDoc->createElement('max_total',$this->getMaxTotal());
			$headers->item(0)->appendChild($tmp);
		}

	/**
	 * Genera il recordset delle qualificazioni
	 *
	 */
		private function rsQualification()
		{
		// filtro sulla sessione
			$sessionFilter=($this->options[0]!=-1 ? "AND QuSession=" . StrSafe_DB($this->options[0]) . " " : "");

		// distanza estratta
			$dists="";
			$from=0;
			$to=0;
			if ($this->options[1]==-1)
			{
				$from=1;
				$to=8;
			}
			elseif ($this->options[1]==0)
			{
				$from=0;
				$to=-1;
			}
			else
			{
				$from=$this->options[1];
				$to=$from;
			}

			for ($i=$from;$i<=$to;++$i)
			{
				$dists
					.=",QuD" . $i . "Score AS `entry->prev_totals->s" . $i . "`"
					. ",QuD" . $i . "Gold AS `entry->prev_totals->g" . $i . "`"
					. ",QuD" . $i . "Xnine AS `entry->prev_totals->x" . $i . "`"
					. ",QuD" . $i . "Hits AS `entry->prev_totals->h" . $i . "`";
			}

			$query
				= "SELECT "
					. "QuSession AS `entry->session`, SUBSTRING(QuTargetNo,2) AS `entry->target`,  "
					. "CONCAT(EnFirstName,' ',EnName) AS `entry->name`, CONCAT(EnDivision,EnClass) AS `entry->category`, CoCode AS `entry->noc`,CoName AS `entry->team_name`, "
					. "EnIndClEvent AS `entry->event_partecipation->ind_class`,"
					. "EnTeamClEvent AS `entry->event_partecipation->team_class`,"
					. "EnIndFEvent AS `entry->event_partecipation->ind_final`,"
					. "EnTeamFEvent AS `entry->event_partecipation->team_final`,"
					. "IF(EnStatus<=1,1,0) AS `entry->valid` "
					. $dists . ", "
					. "QuScore AS `entry->prev_totals->s`,QuGold AS `entry->totals->g`,QuXnine AS `entry->totals->x`,QuHits AS `entry->totals->h` "
				. "FROM "
					. "Entries INNER JOIN Qualifications ON EnId=QuId "
					. "INNER JOIN Countries ON EnCountry=CoId "
				. "WHERE "
					. "EnTournament=" . $this->getTourId() . " "
					. $sessionFilter
				. "ORDER BY QuSession ASC,QuTargetNo ASC ";
			$this->rs=safe_r_sql($query);
		//	print $query;exit;
		}

	/**
	 * Genera il recordset delle eliminatorie
	 *
	 */
		private function rsElimination()
		{
		// filtro sul girone
			$roundFilter=($this->options[0]!=-1 ? "AND ElElimPhase=" . StrSafe_DB($this->options[0]) . " " : "");

			$query
				= "SELECT "
					. "(ElElimPhase+1) AS `entry->session`,ElTargetNo AS `entry->target`,"
					. "CONCAT(EnFirstName,' ',EnName) AS `entry->name`, CONCAT(EnDivision,EnClass) AS `entry->category`, CoCode AS `entry->noc`,CoName AS `entry->team_name`, "
					. "EnIndClEvent AS `entry->event_partecipation->ind_class`,"
					. "EnTeamClEvent AS `entry->event_partecipation->team_class`,"
					. "EnIndFEvent AS `entry->event_partecipation->ind_final`,"
					. "EnTeamFEvent AS `entry->event_partecipation->team_final`,"
					. "IF(EnStatus<=1,1,0) AS `entry->valid`, "
					. "ElScore AS `entry->totals->s`,ElHits AS `entry->totals->h`,ElGold AS `entry->totals->g`,ElXnine AS `entry->totals->x` "
				. "FROM "
					. "Entries INNER JOIN Eliminations ON EnId=ElId "
					. "INNER JOIN Countries ON EnCountry=CoId "
				. "WHERE "
					. "EnTournament=" . $this->getTourId() . " "
					. $roundFilter . " "
				. "ORDER BY ElElimPhase ASC,ElTargetNo ASC ";

			$this->rs=safe_r_sqlm($query);
		}

	/**
	 * Genera il recordset delle finali individuali
	 */
		private function rsIndividualFinal()
		{
		// filtro
		 	$schedulingFilter="AND (CONCAT(FSScheduledDate,' ',FSScheduledTime) LIKE " . StrSafe_DB($this->options[0]) . " OR  CONCAT(FSScheduledDate,' ',FSScheduledTime) IS NULL) ";

		 	$query
		 		= "SELECT "
		 			. "GrPhase AS `entry->session`,"
		 			. "CONCAT(FSTarget,IF(MOD(FinMatchNo,2)=0,'A','B')) AS `entry->target`,"
		 			. "CONCAT(FSScheduledDate,' ',FSScheduledTime) AS `entry->scheduling`,"
		 			. "GrPosition AS `entry->position`,"
		 			. "CONCAT(EnFirstName,' ',EnName) AS `entry->name`,"
		 			. "FinEvent AS `entry->category`,"
		 			. "CoCode AS `entry->noc`,CoName AS `entry->team_name`, "
		 			. "EnIndClEvent AS `entry->event_partecipation->ind_class`,"
					. "EnTeamClEvent AS `entry->event_partecipation->team_class`,"
					. "EnIndFEvent AS `entry->event_partecipation->ind_final`,"
					. "EnTeamFEvent AS `entry->event_partecipation->team_final`,"
					. "IF(EnStatus<=1,1,0) AS `entry->valid`, "
					. "FinScore AS `entry->totals->s` "
		 		. "FROM "
		 			. "Finals INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=" . $this->getTourId() . " "
		 			. "INNER JOIN Entries ON  FinAthlete=EnId INNER JOIN Grids ON FinMatchNo=GrMatchNo "
		 			. "LEFT JOIN Countries ON EnCountry=CoId LEFT JOIN FinSchedule ON FinTournament=FSTournament "
		 			. "AND FinMatchNo=FSMatchNo AND FinEvent=FSEvent AND FSTeamEvent='0' "
		 		. "WHERE "
		 			. "EnTournament=" . $this->getTourId() . " "
		 			. $schedulingFilter . " "
		 		. "ORDER BY "
		 			. "FSTarget ASC ";
		 	$this->rs=safe_r_sql($query);
		 	//print $query;exit;
		}

	/**
	 * Genera il recordset delle finali a squadre
	 */
		private function rsTeamFinal()
		{
		// filtro
		 	$schedulingFilter="AND (CONCAT(FSScheduledDate,' ',FSScheduledTime) LIKE " . StrSafe_DB($this->options[0]) . " OR CONCAT(FSScheduledDate,' ',FSScheduledTime) IS NULL) ";

		 	$query
		 		= "SELECT "
		 			. "GrPhase AS `entry->session`,"
		 			. "CONCAT(FSTarget,'A') AS `entry->target`,"
		 			. "CONCAT(FSScheduledDate,' ',FSScheduledTime) AS `entry->scheduling`,"
		 			. "GrPosition AS `entry->position`,"
		 			. "TfEvent AS `entry->category`,"
		 			. "CoCode AS `entry->noc`,CoName AS `entry->team_name`, "
					. "TfScore AS `entry->totals->s` "
		 		. "FROM "
		 			. "TeamFinals INNER JOIN Countries ON TfTeam=CoId AND TfTournament=CoTournament "
		 			. "INNER JOIN Grids ON TfMatchNo=GrMatchNo "
		 			. "LEFT JOIN FinSchedule ON TfTournament=FSTournament AND TfMatchNo=FSMatchNo AND TfEvent=FSEvent AND FSTeamEvent='1' "
		 		. "WHERE "
		 			. "TfTournament=" . $this->getTourId() . "  "
		 			. $schedulingFilter . " "
		 		. "ORDER BY "
		 			. "FSTarget ASC ";
		 	$this->rs=safe_r_sql($query);
		 	//print $query;exit;
		}

	/**
	 * Renderizza al browser
	 *
	 */
		public function render2browser()
		{
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Content-type: text/xml; charset=' . PageEncode);

			print $this->xmlDoc->SaveXML();
		}

	/**
	 * Renderizza al browser per il download
	 *
	 */
		public function render2download()
		{
			header('Content-Disposition: attachment; filename="' . $this->getTourCode() . '.' . $this->getPhase() . '.export.' . date('YmdHis') .'.xml"');

			$this->render2browser();
		}
	}
?>