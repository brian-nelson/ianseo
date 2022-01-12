<?php
/*
													- Fun_ScriptsOnNewTour.inc.php -
	Il file contiene delle funzioni che vengono chiamate al momento della creazione di una nuova gara
*/

//define('NEWTOUR_PATH', $CFG->DOCUMENT_PATH . 'Common/Setup/' );

/**
 * Processa il file di setup $File2Proc
 *
 * @param String $File2Proc: file da processare (solo il basename!)
 * @param Int $Id: id del torneo
 *
 * @return void
 */
	function ProcessSetupFile($Id, $File2Proc= 'Setup.set')
	{
		$ff = $File2Proc;
		$fh = @file($ff);

		if ($fh)
		{
			foreach($fh as $Buffer)
			{
				if (trim($Buffer) and substr($Buffer,0,1)!='#' )
				{
					$Query = str_replace('##TourId##',StrSafe_DB($Id),$Buffer);
					$Rs=safe_w_sql($Query);
				}
			}
		}
	}

/**
 * Serve per intefacciarsi a ProcessSetupFile(...)
 *
 * @param Int $Id: id del torneo
 *
 * @return void
 *
 */
	function GetSetupFile($TourId, $ToType=0, $Lang='', $SubRule='1', $subRuleName='')
	{
		static $ciclo=0;
		$typeName='';
		global $CFG;
		global 	$tourDetGolds, $tourDetXNine, $tourDetGoldsChars, $tourDetXNineChars;

		if(!$ciclo) {
			// reset data if any
			$ToDelete=array(
				'Divisions' => 'Div',
				'Classes' => 'Cl',
				'SubClass' => 'Sc',
				'TournamentDistances' => 'Td',
				'DistanceInformation' => 'Di',
				'Eliminations' => 'El',
				'Events' => 'Ev',
				'EventClass' => 'Ec',
				'Finals' => 'Fin',
                'FinSchedule' => 'FS',
				'TeamFinals' => 'Tf',
				'TargetFaces' => 'Tf',
				);
			foreach($ToDelete as $table => $pre) {
				safe_w_sql("delete from $table where {$pre}Tournament = $TourId");
			}
			/** search for $ToType file, and for localisation $Lang **/
		// cerco il file per il tipo  localizzato per $Lang
			if (file_exists($file = $CFG->DOCUMENT_PATH . 'Modules/Sets/' . $Lang . '/Setup_' . $ToType . '_' . $Lang . '.php')
				or file_exists($file=$CFG->DOCUMENT_PATH . 'Modules/Sets/FITA/Setup_' . $ToType . '.php')
				) {
					if(empty($SubRule)) $SubRule=1;
					require_once($file);
					$typeName=$tourDetTypeName;
			} else {
				die('No Setup File!');
			}

			// reset entries fields if any
			safe_w_sql("update Entries set EnDivision='' where EnTournament=$TourId and EnDivision not in (select DivId from Divisions where DivTournament=$TourId)");
			safe_w_sql("update Entries set EnClass='' where EnTournament=$TourId and EnClass not in (select ClId from Classes where ClTournament=$TourId)");
			safe_w_sql("update Entries set EnAgeClass='' where EnTournament=$TourId and EnAgeClass not in (select ClId from Classes where ClTournament=$TourId)");
			safe_w_sql("update Entries set EnSubClass='' where EnTournament=$TourId and EnSubClass not in (select ScId from SubClass where ScTournament=$TourId)");
			safe_w_sql("update Entries set EnTargetFace='' where EnTournament=$TourId and EnTargetFace not in (select TfId from TargetFaces where TfTournament=$TourId)");

			//update Rule/subrule fields in the different tables
			$ConstToStore=array();
			$q=safe_r_sql("select ToOptions from Tournament where ToId={$TourId}");
			$r=safe_fetch($q);
			if(!empty($r->ToOptions)) {
				$ConstToStore=unserialize($r->ToOptions);
			}
			$ConstToStore["TourRulesCount"]=array();
			$ToUpdate=array(
					'Divisions' => 'Div',
					'Classes' => 'Cl',
					'TournamentDistances' => 'Td',
					'DistanceInformation' => 'Di',
					'Events' => 'Ev',
					'EventClass' => 'Ec',
					'TargetFaces' => 'Tf',
			);
			foreach($ToUpdate as $table => $pre) {
				safe_w_sql("UPDATE $table SET {$pre}TourRules='{$Lang}|{$typeName}|{$subRuleName}' where {$pre}Tournament = $TourId");
				$ConstToStore["TourRulesCount"][$table]=safe_w_affected_rows();
			}
			safe_w_sql("update Tournament set ToOptions=".StrSafe_DB(serialize($ConstToStore))." where ToId={$TourId}");
		}
		$ciclo++;
	}

// Come ProcessSetupFile() ma con gli eventuali file di update da eseguire dopo le regole di setup
	function GetUpdateFile($Id,$Lang='')
	{
		static $ciclo=0;
		global $CFG;
		if(!$ciclo) {
		// cerco il file per il tipo $ToType localizzato per $Lang
			if (file_exists($file = $CFG->DOCUMENT_PATH . 'Modules/Sets/' . $Lang . '/Update_' . $Lang . '.set')
				or file_exists($file=$CFG->DOCUMENT_PATH . 'Modules/Sets/FITA/Update.set')
				)
			{
				ProcessSetupFile($Id, $file);
			}
		}
		$ciclo++;
	}

?>