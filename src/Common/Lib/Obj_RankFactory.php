<?php
require_once('Common/Lib/CommonLib.php');

/**
 * Obj_RankFactory.
 * E' la factory per istanziare la classe corretta per gestire la rank considerata.
 *
 * Vedi commenti a Obj_RankFactory::create() per spiegazione sul funzionamento
 *
 * @final
 */
	final class Obj_RankFactory
	{
	/**
	 * create().
	 * Metodo statico per istanziare la classe corretta.
	 * La classe corretta viene scelta in base al parametro $family e deve trovarsi
	 * nella directory /Common/Rank.
	 *
	 * Il nome del file e il nome della classe stessa deve essere nella seguente forma:
	 * Obj_Rank_<family>_<type>_<lang>
	 * con <family> la famiglia, <type> il tipo gara e <lang> la regola localizzata con <type> e <lang>
	 * opzionali.
	 *
	 * Il metodo cerca nella directory se esiste prima di tutto il file corretto nella sequenza:
	 * 1) Obj_Rank_<family>_<type>_<lang>
	 * 2) Obj_Rank_<family>_<lang>
	 * 3) Obj_Rank_<family>_<type>
	 * 4) Obj_Rank_<family>
	 *
	 * @static
	 *
	 * @param string $family: famiglia
	 * 		Attualmente per convenzione abbiamo:
	 * 			DivClass 		=> classifica di Divisione/Classe individuale
	 * 			SubClass 		=> classifica di Categoria/Classe di merito individuale
	 * 			Snapshot 		=> classifica di Categoria/Classe o di Qualificazione basata sul metodo dell "Snapshot"
	 * 			Abs 			=> classifica di qualificazione assoluta
	 * 			AbsTeam 		=> classifica di qualificazione assoluta a squadre
	 * 			ElimInd 		=> classifica fase eliminatoria individuale
	 * 			FinalInd 		=> classifica finale individuale
	 * 			DivClassTeam	=> classifica di Divisione/Classe s squadre
	 * 			AbsTeam			=> classifica di qualificazione a squadre
	 * 			FinalTeam 		=> classifica finale a squadre
	 *
	 * @param mixed[] $opts: opzioni usate dai vari metodi write() e read() dalle classi.
	 * 		A seconda della classe sarà inizializzato in un qualche modo.
	 * 		Ogni classe commenta la forma di $opts
	 *
	 * @see Obj_Rank_*
	 *
	 * @return mixed: istanza della classe corretta per gestire la rank
	 *
	 */
		static public function create($family,$opts=null)
		{
		/*
		 * In base a $family, al tipo di torneo e alla localizzazione della regola
		 * con cui il torneo è stato creato istanzio la classe giusta includendo quello che serve
		 */

			global $CFG;

		// torneo passato
			$tournament = (array_key_exists('tournament',$opts) ? $opts['tournament'] : $_SESSION['TourId']);

		// tipo, localizzazione e sottotipo
			$q=safe_r_sql("select ToType, ToLocRule, ToTypeSubRule from Tournament where ToId={$tournament}");
			$r=safe_fetch($q);
			$ToType=$r->ToType;
			$ToLocRule=$r->ToLocRule;
			$ToSubRule=str_replace('-','',$r->ToTypeSubRule);

		// sicuramente includo la classe base
			if (!file_exists($CFG->DOCUMENT_PATH . 'Common/Rank/Obj_Rank.php'))
				return null;

			require_once ('Common/Rank/Obj_Rank.php');

		/*
		 * Adesso in base alla famiglia, al tipo e alla localizzazione
		 * cerco il file in questa sequenza:
		 * 1) Obj_Rank_<family>_<type>_<subtype>_<lang>
		 * 2) Obj_Rank_<family>_<type>_<lang>
		 * 3) Obj_Rank_<family>_<lang>
		 * 4) Obj_Rank_<family>_<type>_<subtype>
		 * 5) Obj_Rank_<family>_<type>
		 * 6) Obj_Rank_<family>
		 * Se neppure (6) esiste termino con errore (null) perchè il default è la base della famiglia
		 * e voglio che esista!
		 */
			//print $family.'<br/>';

			$name='Obj_Rank_'. $family;

			$rootLoc = $CFG->DOCUMENT_PATH . "Modules/Sets/$ToLocRule/Rank/" . $name . "%s.php";
			$rootGlobal = $CFG->DOCUMENT_PATH . "Common/Rank/" . $name . "%s.php";

			if( file_exists($file=sprintf($rootLoc, "_{$ToType}_{$ToSubRule}")) or
				file_exists($file=sprintf($rootLoc, "_{$ToType}")) or
				file_exists($file=sprintf($rootLoc, "")) or
				file_exists($file=sprintf($rootGlobal, "_{$ToType}_{$ToSubRule}")) or
				file_exists($file=sprintf($rootGlobal, "_{$ToType}")) or
				file_exists($file=sprintf($rootGlobal, ""))
				) {
				// first step is to include base class!!!
				require_once ($file);
				$name=basename($file,".php");

				// check if the "calc" class exists
				if( file_exists($file=sprintf($rootLoc, "_{$ToType}_{$ToSubRule}_calc")) or
					file_exists($file=sprintf($rootLoc, "_{$ToType}_calc")) or
					file_exists($file=sprintf($rootLoc, "_calc")) or
					file_exists($file=sprintf($rootGlobal, "_{$ToType}_{$ToSubRule}_calc")) or
					file_exists($file=sprintf($rootGlobal, "_{$ToType}_calc")) or
					file_exists($file=sprintf($rootGlobal, "_calc"))
					) {
					require_once ($file);
					$name=basename($file,".php");
				}

				// returns the found class
				return new $name($opts);
			} else {
				// no class found!
				return null;
			}

		}
	}