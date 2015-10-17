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
	 * __construct()
	 * Costruttore.
	 *
	 * @param mixed[] $opts: opzioni per la rank
	 * @return void
	 */
		public function __construct($opts=null)
		{
			$this->opts=$opts;

			if (array_key_exists('tournament',$this->opts))
			{
				$this->tournament=$this->opts['tournament'];
			}
			else
			{
				$this->tournament=$_SESSION['TourId'];
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
	}