<?php
/**
 * Obj_Rank.
 * Classe base per calcolare le classifiche.
 * Rappresenta l'abstract product del factory pattern.
 * Da qui derivano tutte le classi che implementano gli algoritmi
 * di calcolo della rank.
 *
 *
 */
	abstract class Obj_Rank
	{
	/**
	 * Torneo su cui stiamo lavorando.
	 * Verrà impostato con l'opzione 'tournament' se presente oppure con
	 * il torneo in sessione.
	 *
	 * @var int
	 */
		protected $tournament=null;
		protected $tourcode=null;

	/**
	 * Struttura contenente la rank con tutte le metainformazioni
	 * importanti per il tipo.
	 * Questo parametro viene inizializzato dai figli di questa classe e ognuno la struttura a modo suo
	 *
	 * @var mixed[]
	 */
		protected $data=null;

	/**
	 * E' il vettore delle opzioni.
	 * A seconda della classifica richiesta avrà una determinata forma (commentata da ogni figlio)
	 *
	 * @var mixed[]
	 */
		protected $opts=null;

		/**
		 * Will contain the records available for this comeptition. Most usefull are WR (World Record), OR (Olympic Records), CR (Continental records)...
		 * The structure will be an array of arrays: $HasRecords=array(WR=>array(WA), OR=>array(WA), CR=>array(WAE, WAAm, WAAs, WAAf, WAO))
		 * @var array of records levels
		 */
		protected $HasRecords=array();

		protected $TourOptions=array();
		protected $TimeZone='UTC';

	/**
	 * __construct()
	 * Costruttore.
	 *
	 * @param mixed[] $opts: opzioni per la rank
	 * @return void
	 */
		public function __construct($opts=null)
		{
			$this->opts=$opts;

			if (array_key_exists('tournament',$this->opts)) {
				$this->tournament=$this->opts['tournament'];
			} else {
				$this->tournament=$_SESSION['TourId'];
			}

			$q=safe_r_sql("select ToTimeZone, ToOptions, ToCode from Tournament where ToId=$this->tournament");
			if($r=safe_fetch($q)) {
				$this->TourOptions = unserialize($r->ToOptions);
				$this->TimeZone = $r->ToTimeZone;
				$this->tourcode = $r->ToCode;
			}


			// defines a constant that overrides printing if not empty
			if(!empty($_SESSION['TourPrintLang'])) @define('PRINTLANG', $_SESSION['TourPrintLang']);

		}

	/**
	 * calculate()
	 * Calcola la rank e la scrive nel db.
	 * Viene implementato da ogni sotto classe.
	 *
	 * @abstract
	 *
	 * @return bool: true se non ci sono errori false altrimenti
	 */
		abstract public function calculate();

	/**
	 * read()
	 * Legge la rank dal db.
	 * Viene implementato da ogni sotto classe.
	 *
	 * @abstract
	 *
	 * @return void
	 */
		abstract public function read();

		public function getData()
		{
			return $this->data;
		}

		public function getOpts()
		{
			return $this->opts;
		}

		public function getRecords($Event='', $Team=false, $Match=false) {
			$ret=array();
			$sql="select distinct RtRecType, RtRecCode, RtRecDistance, RtRecTotal, RtRecXNine, RtRecDate, RtRecExtra, TrColor,
					find_in_set('bar', TrFlags) TrBars,
					find_in_set('gap', TrFlags) TrGaps
				from RecTournament
				inner join TourRecords on TrTournament=RtTournament and TrRecType=RtRecType and TrRecCode=RtRecCode and TrRecTeam=RtRecTeam and TrRecPara=RtRecPara
				inner join Events on RtTournament=EvTournament and EvRecCategory=RtRecCategory and EvTournament={$this->tournament}  and RtRecTeam=EvTeamEvent
				".($Event ? "and EvCode='{$Event}'" : '')."
				and EvTeamEvent=".($Team ? '1' : '0')."
				where ".($Match ? 'RtRecPhase in (0, 3)' : 'RtRecPhase=1')."
				order by RtRecTotal desc "; // for now we only do on totals

			$q=safe_r_sql($sql);
			while($r=safe_fetch($q)) {
				if($r->RtRecExtra) $r->RtRecExtra=unserialize($r->RtRecExtra);
				$r->RtRecType=get_text($r->RtRecType.'-Record', 'Tournament');
				$ret[]=$r;
			}
			return $ret;
		}

		function getHall($target) {
			$target=intval($target);
			if(!empty($this->TourOptions['FopLocations'])) {
				foreach($this->TourOptions['FopLocations'] as $hall) {
					if($target>=$hall->Tg1 and $target<=$hall->Tg2) {
						return $hall->Loc;
					}
				}
			}
			return '';
		}
	}