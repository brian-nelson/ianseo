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
		protected $touryear=null;

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
		 * Will contain the records available for this competition.
		 * The structure will be an array of arrays:
		 * $HasRecords=array(
		 *  [BitLevel|Claimer]=>object(RecAreas.*),
		 *  ...)
		 * @var array
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

			if (is_array($this->opts) and array_key_exists('tournament',$this->opts)) {
				$this->tournament=$this->opts['tournament'];
			} else {
				$this->tournament=$_SESSION['TourId'];
			}

			$q=safe_r_sql("select ToTimeZone, ToOptions, ToCode, year(ToWhenTo) as ToYear, RecAreas.* 
				from Tournament 
			    left join (select distinct RecAreas.*, TrTournament from RecAreas inner join TourRecords on TrRecCode=ReArCode and TrTournament=$this->tournament) RecAreas on TrTournament=ToId 
				where ToId=$this->tournament");
			while($r=safe_fetch($q)) {
				if(empty($this->tourcode)) {
					$this->TourOptions = unserialize($r->ToOptions);
					$this->TimeZone = $r->ToTimeZone;
					$this->tourcode = $r->ToCode;
					$this->touryear = $r->ToYear;
				}
				unset($r->ToOptions, $r->ToTimeZone, $r->ToCode, $r->ToYear, $r->TrTournament);
				if(!empty($r->ReArCode)) {
					$this->HasRecords["$r->ReArBitLevel|$r->ReArMaCode"]=$r;
				}
			}


			// defines a constant that overrides printing if not empty
			if(!empty($_SESSION['TourPrintLang']) AND !defined('PRINTLANG')) {
			    @define('PRINTLANG', $_SESSION['TourPrintLang']);
            }

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

		public function getRecords($Event='', $Team=false, $Match=false, $IsEvent=true) {
			$ret=array();
			if($IsEvent) {
				$sql="select distinct TrHeader, TrHeaderCode, RtRecCode, RtRecDistance, RtRecMaxScore, ReArBitLevel, ReArMaCode, TrFontFile, RtRecTotal, RtRecXNine, RtRecDate, RtRecExtra, TrColor,
						find_in_set('bar', TrFlags) TrBars,
						find_in_set('gap', TrFlags) TrGaps
					from RecTournament
					inner join RecAreas on ReArCode=RtRecCode
					inner join TourRecords on TrTournament=RtTournament and TrRecCode=RtRecCode and TrRecTeam=RtRecTeam and TrRecPara=RtRecPara
					inner join Events on RtTournament=EvTournament and RtRecCategory=if(ReArWaMaintenance=1, EvRecCategory, EvCode) and EvTournament={$this->tournament}  and RtRecTeam=EvTeamEvent
					".($Event ? "and EvCode='{$Event}'" : '')."
					and EvTeamEvent=".($Team ? '1' : '0')."
					where RtRecTotal>0 and RtRecPhase=".($Match ? 3 : 1)."
					order by ReArBitLevel desc, ReArCode, RtRecTotal desc "; // for now we only do on totals
			} else {
				$sql="select distinct TrHeader, TrHeaderCode, RtRecCode, RtRecDistance, RtRecMaxScore, ReArBitLevel, ReArMaCode, TrFontFile, RtRecTotal, RtRecXNine, RtRecDate, RtRecExtra, TrColor,
						find_in_set('bar', TrFlags) TrBars,
						find_in_set('gap', TrFlags) TrGaps
					from RecTournament
					inner join RecAreas on ReArCode=RtRecCode
					inner join TourRecords on TrTournament=RtTournament and TrRecCode=RtRecCode and TrRecTeam=RtRecTeam and TrRecPara=RtRecPara
					where RtTournament={$this->tournament} and RtRecTeam=".($Team ? '1' : '0')." and RtRecTotal>0 and RtRecPhase=".($Match ? 3 : 1).($Event ? " and RtRecLocalCategory='{$Event}'" : '')."
					order by ReArBitLevel desc, ReArCode, RtRecTotal desc "; // for now we only do on totals
			}

			$q=safe_r_sql($sql);
			while($r=safe_fetch($q)) {
				if($r->RtRecExtra) $r->RtRecExtra=unserialize($r->RtRecExtra);
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

		public function ManageBitRecord($BitLevel, $CaCode, $MaCode, $EvIsPara=0) {
			if(!$BitLevel) {
				return '';
			}

			$Field=$EvIsPara ? 'ReArOdfParaCode' : 'ReArOdfCode';

			foreach(array(128,64,32,16,8,4,2,1) as $Level) {
				if($BitLevel&$Level) {
					if(isset($this->HasRecords[$Level.'|'])) {
						return $this->HasRecords[$Level.'|']->{$Field};
					} elseif(isset($this->HasRecords[$Level.'|'.$CaCode])) {
						return $this->HasRecords[$Level.'|'.$CaCode]->{$Field};
					} elseif(isset($this->HasRecords[$Level.'|'.$MaCode])) {
						return $this->HasRecords[$Level.'|'.$MaCode]->{$Field};
					}
				}
			}
			// fall back to empty string
			return '';
		}
	}
