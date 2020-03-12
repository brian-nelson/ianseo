<?php
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
/*
													- Fun_Tournament.local.inc.php -
	Contiene le funzioni e le variabili globali per la sezione Tournament.
*/

/*
	- $Arr_Values2Check_*
	Vettori dei valori da verificare.
	La parte che sostituisce * indica il file al quale � associato
	La chiave � il nome del campo; a questa � associato un vettore di 2 elementi:
	array('Func' => '<Nome funzione>', 'Error' => <true/false>).
	La voce 'Func' indica la funzione da richiamare per il check: essa dovr� ritornare true se c'� errore e false altrimenti.
	La voce 'Error' contiene lo stato dell'errore (true/false).

	I campi che iniziano con 'd_' sono quelli che arrivano da QueryString; quelli che iniziano con 'x_' vengono prima aggregati a mano
	e poi analizzati
*/
	$Arr_Values2Check_Index = array
	(
		'd_ToCode' 			=> array('Func' => 'StrNotEmpty', 'Error' => false),
		'd_ToName' 			=> array('Func' => 'StrNotEmpty', 'Error' => false),
		'd_ToCommitee' 		=> array('Func' => 'StrNotEmpty', 'Error' => false),
		'd_ToComDescr' 		=> array('Func' => 'StrNotEmpty', 'Error' => false),
		'd_ToWhere' 		=> array('Func' => 'StrNotEmpty', 'Error' => false),
		'd_ToTimeZone'		=> array('Func' => 'GoodTimezone', 'Error' => false),
		'x_ToWhenFrom' 		=> array('Func' => 'GoodDate', 'Error' => false, 'Value' => (isset($_REQUEST['xx_ToWhenFromYear']) ? $_REQUEST['xx_ToWhenFromYear'] : '0000') . '-' . (isset($_REQUEST['xx_ToWhenFromMonth']) ? $_REQUEST['xx_ToWhenFromMonth'] : '00') . '-' . (isset($_REQUEST['xx_ToWhenFromDay']) ?  $_REQUEST['xx_ToWhenFromDay'] : '00')),
		'x_ToWhenTo'		=> array('Func' => 'GoodDate', 'Error' => false, 'Value' => (isset($_REQUEST['xx_ToWhenToYear']) ? $_REQUEST['xx_ToWhenToYear'] : '0000') . '-' . (isset($_REQUEST['xx_ToWhenToMonth']) ? $_REQUEST['xx_ToWhenToMonth'] : '00') . '-' . (isset($_REQUEST['xx_ToWhenToDay']) ? $_REQUEST['xx_ToWhenToDay'] : '00')),
		'd_ToType' 			=> array('Func' => 'StrNotEmpty', 'Error' => false),
		'd_Rule' 			=> array('Func' => 'StrNotEmpty', 'Error' => false)
	);

	/*$Arr_Values2Check_ManSessions = array
	(
		'd_ToTar_1'	=> array('Func' => 'GoodNumTarget', 'Error' => false),
		'd_ToTar_2'	=> array('Func' => 'GoodNumTarget', 'Error' => false),
		'd_ToTar_3'	=> array('Func' => 'GoodNumTarget', 'Error' => false),
		'd_ToTar_4'	=> array('Func' => 'GoodNumTarget', 'Error' => false),
		'd_ToTar_5'	=> array('Func' => 'GoodNumTarget', 'Error' => false),
		'd_ToTar_6'	=> array('Func' => 'GoodNumTarget', 'Error' => false),
		'd_ToTar_7'	=> array('Func' => 'GoodNumTarget', 'Error' => false),
		'd_ToTar_8'	=> array('Func' => 'GoodNumTarget', 'Error' => false),
		'd_ToTar_9'	=> array('Func' => 'GoodNumTarget', 'Error' => false),
		'd_ToAth_1'	=> array('Func' => 'GoodNumAth', 'Error' => false),
		'd_ToAth_2'	=> array('Func' => 'GoodNumAth', 'Error' => false),
		'd_ToAth_3'	=> array('Func' => 'GoodNumAth', 'Error' => false),
		'd_ToAth_4'	=> array('Func' => 'GoodNumAth', 'Error' => false),
		'd_ToAth_5'	=> array('Func' => 'GoodNumAth', 'Error' => false),
		'd_ToAth_6'	=> array('Func' => 'GoodNumAth', 'Error' => false),
		'd_ToAth_7'	=> array('Func' => 'GoodNumAth', 'Error' => false),
		'd_ToAth_8'	=> array('Func' => 'GoodNumAth', 'Error' => false),
		'd_ToAth_9'	=> array('Func' => 'GoodNumAth', 'Error' => false)
	);*/

	function GoodTimezone($TheString) {
		return (preg_match('/^[+-]{0,1}[0-9]{2}:[0-9]{2}$/', $TheString));
	}

/*
	- StrNotEmpty($TheString)
	In $Ret ritorna false se la stringa $TheString � vuota (lunghezza 0); true altrimenti.
*/
	function StrNotEmpty($TheString)
	{
		return (strlen($TheString)==0 ? false : true);
	}

/*
	- GoodDate($TheDate,&$Ret)
	Se ConvertDate(...) segnala l'errore sulla data $TheDate, in $Ret ci sar� false; altrimenti true
*/
	function GoodDate($TheDate)
	{
		return (RevertDate($TheDate)===false ? false : true);
	}

/*
	- GoodNumTarget($TheNum,&$Ret)
	In $Ret ci sar� true se $TheNum � un numero compreso nell'intervallo [0 ; 999]; false altrimenti.
*/
	function GoodNumTarget($TheNum)
	{

		return (preg_match('/^[0-9]{1,' . TargetNoPadding . '}$/i',$TheNum));
	}

/*
	- GoodNumAth($TheNum,&$Ret)
	In $Ret ci sarà true se $TheNum è un numero compreso nell'intervallo [0 ; 26]; false altrimenti.
*/
	//function GoodNumAth($TheNum, &$Ret)
	function GoodNumAth($TheNum)
	{
		return (is_numeric($TheNum) && $TheNum>=0 && $TheNum<=26);
	}

/*
	- CheckClassAge($ClId,$Age,$FromTo)
	Verifica che il parametro $Age per la classe $ClId sia corretto.
	Se $FromTo vale 'From' allora $Age è un AgeFrom altrimenti se vale 'To' è un AgeTo
	Ritorna true se il check è ok altrimenti false

	******************
	Occorre fare il controllo di non sofrapposione degli anni
	******************
*/
	function CheckClassAge($ClId, $Age, $FromTo='From', $ClDivAllowed = '')
	{
		$Start=0;
		$End=0;
		$Sex=-1;
		/*
		$Select
			= "SELECT ClId FROM Classes "
			. "WHERE ClId=" . StrSafe_DB($_REQUEST['ClId']) . " AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";

		if ($FromTo=='From')
			$Select.= "AND ClAgeTo<" . StrSafe_DB($Age) . " ";
		else
			$Select.= "AND ClAgeFrom>" . StrSafe_DB($Age) . " ";
		*/

		$Select = "";

		if ($FromTo=='From') {
			$Start=$Age;
			$Select
				= "SELECT ClAgeTo,ClSex  FROM Classes WHERE ClId=" . StrSafe_DB($ClId)
				. " AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. ($ClDivAllowed ? " AND ClDivisionsAllowed=" . StrSafe_DB($ClDivAllowed) . " " : '');
			$Rs=safe_r_sql($Select);

			//print $Select . '<br>';
			if (safe_num_rows($Rs)==1)
			{
				$Row=safe_fetch($Rs);
				$End=$Row->ClAgeTo;
				$Sex=$Row->ClSex;
			}
		} elseif ($FromTo=='To') {
			$End=$Age;
			$Select
				= "SELECT ClAgeFrom,ClSex FROM Classes WHERE ClId=" . StrSafe_DB($ClId)
				. " AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. ($ClDivAllowed ? " AND ClDivisionsAllowed=" . StrSafe_DB($ClDivAllowed) . " " : '');
			//print $Select . '<br>';
			$Rs=safe_r_sql($Select);
			if (safe_num_rows($Rs)==1)
			{
				$Row=safe_fetch($Rs);
				$Start=$Row->ClAgeFrom;
				$Sex=$Row->ClSex;
			}
		}

		$Select
			= "SELECT ClId FROM Classes "
			. "WHERE (ClId<>" . StrSafe_DB($ClId)
				. ($ClDivAllowed ? " AND ClDivisionsAllowed=" . StrSafe_DB($ClDivAllowed) : '')
				. " AND ClSex=" . StrSafe_DB($Sex)
				. " AND ClTournament=" . StrSafe_DB($_SESSION['TourId'])
				. " AND ClAthlete=1 "
				. ") "
			. " AND ((ClAgeFrom<=" . StrSafe_DB($Start) . " AND ClAgeTo>=" . StrSafe_DB($End) . ") OR "
			. "(ClAgeFrom<=" . StrSafe_DB($End) . " AND ClAgeTo>=" . StrSafe_DB($End) . ")) ";
		$Rs=safe_r_sql($Select);

		return (safe_num_rows($Rs)==0);
	}

/*
	- CreateValidClass($ClId,$ClList)
	Crea dal parametro $ClList la lista delle classi valide per $ClId
	Ritorna la stringa

*/
	function CreateValidClass($ClId,$ClList)
	{
		// mi assicuro che ci sia anche la classe id --NO, tolto per mettere i GM forzati a RM
		//$StrList=(strpos($ClList,$ClId)===false ? $ClId . ',' : '') . $ClList;

		//mi assicuro che se vuoto, ci sia almeno la ClId
		$StrList = (strlen(trim($ClList)) ? $ClList : $ClId);


		$Arr_List = explode(',',$StrList);

		if (debug)
		{
			print '<pre>';
			print_r($Arr_List);
			print '</pre>';
		}
	// paddo a due le eventuali classi con una lettera
		//foreach ($Arr_List as $Key => $Value)
		for ($i=0;$i<count($Arr_List);++$i)
			if (strlen(trim($Arr_List[$i]))<=2 && trim($Arr_List[$i])!='')
				$Arr_List[$i]=$Arr_List[$i];
			else	// se l'elemento non va bene, lo tolgo
				array_splice($Arr_List,$i,1);

		if (debug)
		{
			print '<pre>';
			print_r($Arr_List);
			print '</pre>';
		}

	// rigenero la lista
		$StrList = implode(',',$Arr_List);
		if (debug) print $StrList . '<br>';
		return $StrList;
	}


/*
	- CreateValidDivision($ClId,$ClList)
	Crea dal parametro $ClList la lista delle classi valide per $ClId
	Ritorna la stringa

*/
	function CreateValidDivision($ClList) {
		if(empty($ClList)) return '';

		$ClList=explode(',', $ClList);
		sort($ClList);
		$Arr_List = array();
		$q=safe_r_sql("select distinct DivId from Divisions where DivTournament={$_SESSION['TourId']} and DivId in ('".implode("','", $ClList)."') order by DivId");
		while($r=safe_fetch($q)) $Arr_List[]=$r->DivId;

		return implode(',',$Arr_List);
	}


/*
 * - CheckCredential()
 * Verifica che le credenziali x mandare i dati su ianseo.net siano settate
 * Attenzione: non controlla che siano quelle corrette ma solo se sono settate
 *
 * @return int: 1 se tutte le credenziali sono settate 0 altrimenti
 */
	function CheckCredential()
	{
		if (isset($_SESSION['OnlineId']) && $_SESSION['OnlineId']!='0' &&
			isset($_SESSION['OnlineEventCode']) && $_SESSION['OnlineEventCode']!='0' &&
			isset($_SESSION['OnlineAuth']) && $_SESSION['OnlineAuth']!='0' &&
			isset($_SESSION['OnlineServices']) && $_SESSION['OnlineServices']!='0')
		{
			return 1;
		}
		return 0;
	}

/**
 * piglia un arrowstring e la riconverte in punti usando i seguenti separatori:
 * "," dopo ogni punto
 * "-" dopo ogni set
 *
 * Utilizza lo stesso criterio di CalcScoreRowsColsSO e purtroppo non avendo qui la possiblità
 * di tirare fuori gli scontri con GetFinMatches() sono costretto a copiare la logica per stabilire le righe e le colonne
 *
 * @param int $rows: numero di righe (volee)
 * @param int $cols: numero di colonne (frecce)
 * @param string $arrowstring
 * @return string: stringa decodificata
 */
	function DecodeArrowstring($rows,$cols,$arrowstring)
	{
		$r=array();

		for ($i=0;$i<$rows;++$i)
		{
			$c=array();

			for ($j=0;$j<$cols;++$j)
			{
				$idx=$i*$cols+$j;
				if (!isset($arrowstring[$idx]))
					$arrowstring[$idx]=' ';
				$c[]=DecodeFromLetter($arrowstring[$idx]);
			}

			$r[]=implode(',',$c);
//			print '<pre>';
//			print_r($r);
//			print '</pre>';
		}

		return implode('-',$r);
	}

	function old_DecodeArrowstring($matchMode,$matchArrowsNo,$team,$mixedTeam,$phase,$arrowstring,$target)
	{
		$ret='';

	// individuale cumulativo
		$rows=4;
		$cols=3;

		if ($team==1)	// team cumulativi
		{
			$rows=4;
			if($mixedTeam==0)
			{
				$cols=6;

			}
			else
			{
				$cols=4;
			}
		}

		if ($matchMode==1)
		{
			$bit=($phase>0 ? 2*bitwisePhaseId($phase) : 1);
			$value = (($bit & $matchArrowsNo)==$bit ? 1 : 0);

			if ($team==0)		// per l'individuale
			{
				if ($value==0)
				{
					$rows=5;
					$cols=3;

				}
				else
				{
					$rows=3;
					$cols=6;

				}
			}
			else		// per i team
			{
				if ($value==0)
				{
					$rows=4;
					$cols=6;

				}
				else
				{
					$rows=4;
					$cols=6;
				}
			}
		}

		$r=array();

		for ($i=0;$i<$rows;++$i)
		{
			$c=array();

			for ($j=0;$j<$cols;++$j)
			{
				$idx=$i*$cols+$j;
				if (!isset($arrowstring[$idx]))
					$arrowstring[$idx]=' ';
				$c[]=DecodeFromLetter($arrowstring[$idx]);
			}

			$r[]=implode(',',$c);
		}

		return implode('-',$r);
	}

	function DecodeTieArrowString($arrowString)
	{
		$r=array();
		for ($i=0;$i<strlen($arrowString);++$i)
		{
			$r[]=DecodeFromLetter($arrowString);
		}

		return implode(',',$r);
	}

	function old_DecodeTieArrowString($arrowString,$target)
	{
		$r=array();
		for ($i=0;$i<strlen($arrowString);++$i)
		{
			$r[]=DecodeFromLetter($arrowString);
		}

		return implode(',',$r);
	}

	function ExportASC($Event=null,$IncludeZeroInfo=true)
	{
		$ToCode = '';
		$ToType = 0;
		$NumDist=0;
		$IocCode='';

		/*$Select
			= "SELECT ToCode, TtNumDist, ToType "
			. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
			. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/
		$Select
			= "SELECT ToCode, ToNumDist AS TtNumDist, ToType, ToIocCode "
			. "FROM Tournament  "
			. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

		$Rs=safe_r_sql($Select);

		if (safe_num_rows($Rs)==1)
		{
			$row=safe_fetch($Rs);
			$ToCode  = $row->ToCode;
			$ToType  = $row->ToType;
			$NumDist = $row->TtNumDist;
			$IocCode = $row->ToIocCode;
		}

		if ($ToCode=='')
			return (array('',''));

		$StrData = '';

	/*
	 * Tipo 0: Informazioni varie sulla ver di ianseo usata
	 * Versione - data aggiornamento archivio nomi
	 */
		if($IncludeZeroInfo)
		{
			$r=safe_r_sql("
				SELECT
					group_concat( CONCAT( if( LupIocCode = '', '___', LupIocCode ) , '-', date_format( LupLastUpdate, '%Y%m%d%H%i%s' ) ) SEPARATOR ',' ) AS up
				FROM
					LookUpPaths
			");
			$rowUp=safe_fetch($r);
			//$StrData.='0;' . ProgramVersion . ';' . ProgramRelease . ';' . (defined('ProgramBuild') ? ProgramBuild : '') .  ';'. GetParameter('SwUpdate') . ';' . GetParameter('LueUpdat') . "\n";
			$StrData.='0;' . ProgramVersion . ';' . ProgramRelease . ';' . (defined('ProgramBuild') ? ProgramBuild : '') .  ';'. GetParameter('SwUpdate') . ';' . $rowUp->up . "\n";
		}

	/*
	 * Tipo 1: Classifica di classe - Individuale
	 * Matricola-Divisione-Classe-CognomeNome-Societa-AgeClass-Totale1-ori1-X1-Totale2-Ori2-X2-CodiceDiControllo-PosizioneClassificaIndividuale(999 se nn partecipa)-Status-Singole distanze
	 * N.B. NON USO l'oggettone poichè ho bisogno di avere anche i non pertecipanti cl/div individuali
	 */
		$Query
			= "SELECT "
				. "'1' AS RowType,EnCode as Bib, EnDivision, EnClass,"
				. "CONCAT(EnFirstName,' ',EnName) AS Name, CoCode,EnAgeClass, ";
		if ($ToType==8)
		{
			$Query .= "(QuD1Score+QuD2Score) AS Score1, "
				. "(QuD1Gold+QuD2Gold) AS Gold1, "
				. "(QuD1Xnine+QuD2Xnine) AS Xnine1, "
				. "(QuD3Score+QuD4Score) AS Score2, "
				. "(QuD3Gold+QuD4Gold) AS Gold2, "
				. "(QuD3Xnine+QuD4Xnine) AS Xnine2, ";
		}
		else if ($ToType==10 || $ToType==12 || $ToType==13)
		{
			$Query .= "(QuD1Score) AS Score1, "
				. "(QuD1Gold) AS Gold1, "
				. "(QuD1Xnine) AS Xnine1, "
				. "(QuD2Score) AS Score2, "
				. "(QuD2Gold) AS Gold2, "
				. "(QuD2Xnine) AS Xnine2, ";
		}
		else
		{
			$Query .= "(QuD1Score+QuD2Score+QuD3Score+QuD4Score) AS Score1, "
				. "(QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold) AS Gold1, "
				. "(QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine) AS Xnine1, "
				. "(QuD5Score+QuD6Score+QuD7Score+QuD8Score) AS Score2, "
				. "(QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold) AS Gold2, "
				. "(QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine) AS Xnine2, ";
		}
		$Query .= "IF(EnDob!='0000-00-000',CONCAT(EnDob,'|',EnSex),EnCtrlCode) AS EnCtrlCode, IF(EnIndClEvent=1,QuClRank,999) AS ClRank,EnStatus ";

		for ($i=1;$i<=$NumDist;++$i)
		{
			$Query.=",QuD" . $i . "Score,QuD" . $i . "Gold,QuD" . $i . "Xnine ";
		}

		$Query.=", if(EnIocCode!='', EnIocCode, '$IocCode') as IocCode ";


		$Query
			.="FROM "
				. "Qualifications INNER JOIN Entries ON QuId=EnId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 "
				. "INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
			. "WHERE "
				. "EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnStatus<=1 AND QuScore>0 "
			. "ORDER BY "
				. "EnCode ASC, CONCAT(EnDivision,EnClass) ASC ";
		//print $Query;exit;
		$Rs=safe_r_sql($Query);

		if (safe_num_rows($Rs)>0)
		{
			while ($MyRow=safe_fetch($Rs))
			{
				$cols=array();
				foreach($MyRow as $key => $val)
				{
					$cols[]=stripslashes($val);
				}
				$StrData.=implode(';',$cols) . "\n";
			}
		}


	/*
	 * Tipo 2: Finale Individuale
	 * Matricola-Divisione-Classe-CognomeNome-Societa-Evento-PosPartenza|FaseIniziale-CodiceControllo-PosizioneClassificaAssoluta-Scores|SetPoints_arrostringDecodificata#tieArrowstringDecodificata
	 */

	// Carico le fasi in un array
		$myPhases=getPhaseArray();
		/*print '<pre>';
		print_r($myPhases);
		print '</pre>';
		exit;*/
	// Genero la query che mi ritorna tutti gli eventi individuali
		$MyQuery = "SELECT EvCode, EvFinalFirstPhase, EvEventName, EvFinalPrintHead,EvMatchMode,EvMatchArrowsNo  ";
		$MyQuery.= "FROM Events ";
		$MyQuery.= "WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 ";
		if (!is_null($Event) && preg_match("/^[0-9A-Z]{1,4}$/i",$Event))
			$MyQuery.= "AND EvCode LIKE '" . $Event . "' ";
		$MyQuery.= "ORDER BY  EvProgr ASC, EvCode ";
		$RsEv=safe_r_sql($MyQuery);

		if (safe_num_rows($RsEv)>0)
		{
			$RsEvCounter=0;
			while ($MyRowEv=safe_fetch($RsEv))
			{
				//$target=$GLOBALS{GetTargetType($MyRowEv->EvCode,0)};
				//print_r($target);

				$PhaseFields=array();
				reset($myPhases);

				//Genero la query che mi da i risultati per ogni evento
				$MyQuery = "SELECT FinAthlete, CONCAT_WS(' ',EnFirstName, EnName) as Atleta, CoCode, CoName, ";
				$Tmp="";
				$NumPhases=0;
				$NeedTitle=true;
				foreach($myPhases as $Key => $Value)
				{
					//print $Value.'<br><br>';
					if($Key<=valueFirstPhase($MyRowEv->EvFinalFirstPhase))
					{
						// mi servirà dopo nei calcoli dei campi!
						$PhaseFields[]=array(
							"X_Phase" => namePhase($MyRowEv->EvFinalFirstPhase,$Key)  . "_Phase",
							"X_SetPoints" => namePhase($MyRowEv->EvFinalFirstPhase,$Key)  . "_SetPoints",
							"X_Arrowstring" => namePhase($MyRowEv->EvFinalFirstPhase,$Key)  . "_Arrowstring",
							"X_TieArrowstring" => namePhase($MyRowEv->EvFinalFirstPhase,$Key)  . "_TieArrowstring",
							"X_Tie" => $Value . "Tie",
							"X_Live" => $Value . "Live",
							"X_Matchno" => $Value . "Matchno",
							"X_PhaseNo" => namePhase($MyRowEv->EvFinalFirstPhase,$Key)
							);
						//print_r($PhaseFields);
						/*if($Key!=0)
						{
							$MyQuery .= "SUM(IF(GrPhase=" . $Key . ",IF(FinScore=0 && FinTie=2,(QuScore*10),FinScore),0)) as `" . $Key  . "_Phase`, SUM(IF(GrPhase=" . $Key . ",FinTie,0)) as `" . $Value . "Tie`, SUM(IF(GrPhase=" . $Key . ",LENGTH(FinArrowstring),0)) as `" . $Value . "Live`, SUM(IF(GrPhase=" . $Key . ",FinMatchNo,0)) as `" . $Value . "Matchno`, ";
							$Tmp = ", `" . $Key . "_Phase` DESC " . $Tmp;
							$NumPhases++;
						}
						else
						{
							$MyQuery .= "SUM(IF((GrPhase=0 OR GrPhase=1),FinScore,0)) as `" . $Key . "_Phase`, SUM(IF((GrPhase=0 OR GrPhase=1),FinTie,0)) as `" . $Value . "Tie`, SUM(IF((GrPhase=0 OR GrPhase=1),LENGTH(FinArrowstring),0)) as `" . $Value . "Live`, SUM(IF((GrPhase=0 OR GrPhase=1),FinMatchNo,0)) as `" . $Value . "Matchno`, ";
							$Tmp = ", `" . $Key . "_Phase` DESC, `" . $Value . "Tie` DESC " . $Tmp;
							$NumPhases++;
						}*/

						if($Key!=0)
						{
							//$MyQuery .= "SUM(IF(GrPhase=" . $Key . ",IF(IF(EvMatchMode=0,FinScore,FinSetScore)=0 && FinTie=2,(QuScore*10),IF(EvMatchMode=0,FinScore,FinSetScore)),0)) as `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key)  . "_Phase`, SUM(IF(GrPhase=" . $Key . ",FinTie,0)) as `" . $Value . "Tie`, MAX(IF(GrPhase=" . $Key . ",/*FinTieBreak*/'','')) as `" . $Value . "TieBreak`, GROUP_CONCAT(IF(GrPhase=" . $Key . ",FinSetPoints,'') SEPARATOR '') AS `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_SetPoints`, SUM(IF(GrPhase=" . $Key . ",FinMatchNo,0)) as `" . $Value . "Matchno`, ";
							$MyQuery .= "SUM(IF(GrPhase=" . $Key . ",IF(IF(EvMatchMode=0,FinScore,FinSetScore)=0 && FinTie=2,(QuScore*10),IF(EvMatchMode=0,FinScore,FinSetScore)),0)) as `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key)  . "_Phase`, SUM(IF(GrPhase=" . $Key . ",FinTie,0)) as `" . $Value . "Tie`,GROUP_CONCAT(IF(GrPhase=" . $Key . ",FinArrowstring,'') SEPARATOR '') AS `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_Arrowstring`, GROUP_CONCAT(IF(GrPhase=" . $Key . ",FinTieBreak,'') SEPARATOR '') as `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_TieArrowstring`, GROUP_CONCAT(IF(GrPhase=" . $Key . ",FinSetPoints,'') SEPARATOR '') AS `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_SetPoints`, SUM(IF(GrPhase=" . $Key . ",FinMatchNo,0)) as `" . $Value . "Matchno`, ";
							$Tmp = ", `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_Phase` DESC " . $Tmp;
							if($Key==4 && $MyRowEv->EvMatchMode!=0)
							{
								$MyQuery .= "SUM(IF(GrPhase=" . $Key . ",FinSetScore,0)) as `QuarterWinner`, SUM(IF(GrPhase=" . $Key . ",FinScore,0)) as `QuarterScore`, ";
								$Tmp = ", `QuarterWinner` DESC, `QuarterScore` DESC " . $Tmp;
							}
							$NumPhases++;
						}
						else
						{
							$MyQuery .= "SUM(IF((GrPhase=0 OR GrPhase=1),IF(EvMatchMode=0,FinScore,FinSetScore),0)) as `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_Phase`, SUM(IF((GrPhase=0 OR GrPhase=1),FinTie,0)) as `" . $Value . "Tie`, GROUP_CONCAT(IF((GrPhase=0 OR GrPhase=1),FinTieBreak,'') SEPARATOR '') as `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_TieArrowstring`, GROUP_CONCAT(IF((GrPhase=0 OR GrPhase=1),FinSetPoints,'')  SEPARATOR '') AS `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key). "_SetPoints`, GROUP_CONCAT(IF((GrPhase=0 OR GrPhase=1),FinArrowstring,'')  SEPARATOR '') AS `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key). "_Arrowstring`,SUM(IF((GrPhase=0 OR GrPhase=1),FinMatchNo,0)) as `" . $Value . "Matchno`, ";
							$Tmp = ", `" . $Key . "_Phase` DESC, `" . $Value . "Tie` DESC " . $Tmp;
							$NumPhases++;
						}
					}
				}
				$MyQuery .= "MIN(GrPhase) as LastPhase, ifnull(CurrentPhase,128) as CurrentPhase, QuScore, IndRank,IndRankFinal,EnCode,EnDivision,EnClass,FinEvent, ";
				$MyQuery .= "IF(LENGTH(EnCtrlCode)=16,EnCtrlCode,CONCAT(EnDob,'|',EnSex)) AS EnCtrlCode,EnCode ";
				$MyQuery.=", if(EnIocCode!='', EnIocCode, '$IocCode') as IocCode ";
				$MyQuery .= "FROM Finals ";
				$MyQuery .= "INNER JOIN Events ON FinEvent=EvCode AND FinTournament=EvTournament AND EvTeamEvent=0 ";
				$MyQuery .= "INNER JOIN Grids ON FinMatchNo=GrMatchNo ";
				$MyQuery .= "INNER JOIN Entries ON FinAthlete=EnId AND FinTournament=EnTournament ";
				$MyQuery .= "INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament ";
				$MyQuery .= "INNER JOIN Qualifications ON EnId=QuId ";
				$MyQuery .= "INNER JOIN Individuals ON IndId=FinAthlete AND IndEvent=FinEvent AND IndTournament=FinTournament ";
				$MyQuery .= "LEFT JOIN (SELECT min(GrPhase) AS CurrentPhase, FinTournament AS SqyTournament, FinEvent AS SqyEvent "
					. "FROM Finals INNER JOIN Grids ON FinMatchNo=GrMatchNo inner join Individuals on IndId=FinAthlete "
					. "WHERE IndRankFinal>0 GROUP BY SqyTournament, SqyEvent) AS Sqy ON SqyTournament=FinTournament AND SqyEvent=FinEvent ";
				$MyQuery .= "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($MyRowEv->EvCode) . " ";
				$MyQuery .= "GROUP BY FinAthlete, CONCAT_WS(' ', EnFirstName, EnName), CoCode, CoName ";
				$MyQuery .= "ORDER BY FinEvent, LastPhase ASC " . $Tmp . ", IndRank ASC";
				///*Debug*/echo $MyQuery. "<br>&nbsp;<br>";
				//print '<br><br>'.$MyQuery.'<br><br>';
				$Rs=safe_r_sql($MyQuery);
				$MyPos=0;
//				$MyRank=0;

			//Se Esistono righe caricate....
				if(safe_num_rows($Rs)>0)
				{
//					$OldScore=-1;
//					$OldTie=-1;
//					$OldLastPhase=-1;
//					$OldRank=-1;
					$ActualScore=0;
					$ActualTie=-1;


					while($MyRow=safe_fetch($Rs))
					{
						$TmpScores=array();
						$cols=array();

						$MyPos++;
					// Se non ho parimerito il ranking � uguale alla posizione
						if($MyPos>$MyRow->CurrentPhase)
						{
							$TmpScores=array();
							$ActualScore=0;
							$ActualTie=-1;

							if($MyRowEv->EvMatchMode == 0)
							{
								foreach($PhaseFields as $i => $Val)
								{
									$ActualScore=($MyRow->{$Val['X_Phase']}!=0 ? $MyRow->{$Val['X_Phase']} : $ActualScore);
									$ActualTie=($MyRow->LastPhase<2 ? $MyRow->{$Val['X_Tie']} : -1);
									$TmpScores[]=($MyRow->{$Val["X_Phase"]}==($MyRow->QuScore*10) ? 'bye' : $MyRow->{$Val["X_Phase"]}) . '|';

									/*print '<pre>';
									print_r($Val);
									print '</pre>';*/
									list($tmpPhase,)=explode('_',$Val['X_Phase']);

									//$x=DecodeArrowstring($MyRowEv->EvMatchMode,$MyRowEv->EvMatchArrowsNo,0,0,$tmpPhase,$MyRow->{$Val["X_Arrowstring"]},$target);
									//$x=DecodeArrowstring($MyRowEv->EvMatchMode,$MyRowEv->EvMatchArrowsNo,0,0,$tmpPhase,$MyRow->{$Val["X_Arrowstring"]});

									$numRows=0;
									$numCols=0;
									$obj=getEventArrowsParams($MyRowEv->EvCode,$tmpPhase,0);
									$numRows=$obj->ends;
									$numCols=$obj->arrows;

									$x=DecodeArrowstring($numRows,$numCols,$MyRow->{$Val["X_Arrowstring"]});
									$TmpScores[count($TmpScores)-1].='_'.$x;

									//$x=DecodeTieArrowstring($MyRow->{$Val['X_TieArrowstring']},$target);
									$x=DecodeTieArrowstring($MyRow->{$Val['X_TieArrowstring']});
									$TmpScores[count($TmpScores)-1].='#'.$x;


									//print $TmpScores[count($TmpScores)-1] . '<br>';
								}
//								if ($OldScore!=$ActualScore || $OldTie!=$ActualTie || $OldLastPhase!=$MyRow->LastPhase)
//									$MyRank = $MyPos;
							}
							else
							{
								foreach($PhaseFields as $i => $Val)
								{
									/*print '<pre>';
									print_r($Val);
									print '</pre>';*/
									$TmpScores[]=($MyRow->{$Val["X_Phase"]}==($MyRow->QuScore*10) ? 'bye' : $MyRow->{$Val["X_Phase"]}) . '|' . ($MyRowEv->EvMatchMode==1 ?  str_replace('|',',',$MyRow->{$Val["X_SetPoints"]}): '');

									list($tmpPhase,)=explode('_',$Val['X_Phase']);
									//$x=DecodeArrowstring($MyRowEv->EvMatchMode,$MyRowEv->EvMatchArrowsNo,0,0,$tmpPhase,$MyRow->{$Val["X_Arrowstring"]},$target);


									$numRows=0;
									$numCols=0;
									$obj=getEventArrowsParams($MyRowEv->EvCode,$tmpPhase,0);
									$numRows=$obj->ends;
									$numCols=$obj->arrows;

									$x=DecodeArrowstring($numRows,$numCols,$MyRow->{$Val["X_Arrowstring"]});
									$TmpScores[count($TmpScores)-1].='_'.$x;

									//$x=DecodeTieArrowstring($MyRow->{$Val['X_TieArrowstring']},$target);
									$x=DecodeTieArrowstring($MyRow->{$Val['X_TieArrowstring']});
									$TmpScores[count($TmpScores)-1].='#'.$x;
									//print $TmpScores[count($TmpScores)-1] . '<br>';
								}

								if($MyRow->LastPhase>=8)
								{
									//$MyRank=$MyRow->LastPhase+1;
								}
								elseif($MyRow->LastPhase==4)
								{
									$ActualScore=$MyRow->QuarterWinner;
									$ActualTie=$MyRow->QuarterScore;
//									if ($OldScore!=$ActualScore || $OldTie!=$ActualTie || $OldLastPhase!=$MyRow->LastPhase)
//									{
//										$MyRank = $MyPos;
//									}

								}
							}
						}

						//Per i primi 4 NON vale la regola sopra
//						if($MyPos<=4)
//						{
//							if($MyRow->CurrentPhase==1 && $MyRank>2)
//								$MyRank = $MyPos;
//							elseif($MyRow->CurrentPhase==0)
//								$MyRank = $MyPos;
//						}

						//Tolgo tutti Quelli da non scrivere

						//Salvo i valori attuali e risistemo i colori
//						$OldScore=$ActualScore;
//						$OldTie=$ActualTie;
//						$OldLastPhase=$MyRow->LastPhase;
//						$OldRank=$MyRank;

						$cols[]=2;
						$cols[]=$MyRow->EnCode;
						$cols[]=$MyRow->EnDivision;
						$cols[]=$MyRow->EnClass;
						$cols[]=stripslashes($MyRow->Atleta);
						$cols[]=$MyRow->CoCode;
						$cols[]=$MyRow->FinEvent;
						$cols[]=$MyRow->IndRank . '|' . $MyRowEv->EvFinalFirstPhase;
						$cols[]=$MyRow->EnCtrlCode;
						//$cols[]=$MyRank;
						//print $MyRow->IndRankFinal.'<br>';
						$cols[]=$MyRow->IndRankFinal;
						$cols[]=$MyRow->IocCode;

						$StrData.=join(';',$cols) . ';' . join(';',$TmpScores) . "\n";
					}
				}
			}
		}

//exit;
	/*
	 * Tipo 3: Classifica di classe - Squadre
	 * CodiceSocieta-Divisione-Classe-Totale1-ori1-X1-Totale2-Ori2-X2-PosizioneClassifica-MatricolaPartecipanti(in lista)
	 */
		$MyQuery = "SELECT TcOrder,CoCode, TeEvent,Quanti,EnCode, EnClass, EnDivision,EnAgeClass, ";
		if ($ToType==8)
		{
			$MyQuery .= "(QuD1Score+QuD2Score) AS Score1, "
				. "(QuD1Gold+QuD2Gold) AS Gold1, "
				. "(QuD1Xnine+QuD2Xnine) AS Xnine1, "
				. "(QuD3Score+QuD4Score) AS Score2, "
				. "(QuD3Gold+QuD4Gold) AS Gold2, "
				. "(QuD3Xnine+QuD4Xnine) AS Xnine2, ";
		}
		else if ($ToType==10 || $ToType==12 || $ToType==13)
		{
			$MyQuery .= "(QuD1Score) AS Score1, "
				. "(QuD1Gold) AS Gold1, "
				. "(QuD1Xnine) AS Xnine1, "
				. "(QuD2Score) AS Score2, "
				. "(QuD2Gold) AS Gold2, "
				. "(QuD2Xnine) AS Xnine2, ";
		}
		else
		{
			$MyQuery .= "(QuD1Score+QuD2Score+QuD3Score+QuD4Score) AS Score1, "
				. "(QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold) AS Gold1, "
				. "(QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine) AS Xnine1, "
				. "(QuD5Score+QuD6Score+QuD7Score+QuD8Score) AS Score2, "
				. "(QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold) AS Gold2, "
				. "(QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine) AS Xnine2, ";
		}
		$MyQuery.= "QuScore, QuGold,QuXnine,TeScore, TeRank, TeGold, TeXnine, ToGolds AS TtGolds, ToXNine AS TtXNine ";
		$MyQuery.=", if(EnIocCode!='', EnIocCode, '$IocCode') as IocCode ";
		$MyQuery.= "FROM Tournament AS t ";
		$MyQuery.= "INNER JOIN Teams AS te ON t.ToId=te.TeTournament AND te.TeFinEvent=0 ";
		$MyQuery.= "INNER JOIN Countries AS c ON te.TeCoId=c.CoId AND te.TeTournament=c.CoTournament ";
		$MyQuery.= "INNER JOIN (SELECT TcCoId, TcSubTeam, TcEvent, TcFinEvent, COUNT(TcId) as Quanti FROM TeamComponent WHERE TcTournament=" . StrSafe_DB($_SESSION['TourId']) . " GROUP BY TcCoId, TcSubTeam, TcEvent, TcFinEvent ORDER BY TcOrder ASC) AS sq ON te.TeCoId=sq.TcCoId AND te.TeEvent=sq.TcEvent AND te.TeSubTeam=sq.TcSubTeam AND te.TeFinEvent=sq.TcFinEvent ";
		$MyQuery.= "INNER JOIN TeamComponent AS tc ON te.TeCoId=tc.TcCoId AND te.TeEvent=tc.TcEvent AND te.TeTournament=tc.TcTournament AND te.TeFinEvent=tc.TcFinEvent ";
		$MyQuery.= "INNER JOIN Entries AS en ON tc.TcId=en.EnId ";
		$MyQuery.= "INNER JOIN Qualifications AS q ON en.EnId=q.QuId ";

		$MyQuery.= "LEFT JOIN Classes AS cl ON en.EnClass=cl.ClId AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$MyQuery.= "LEFT JOIN Divisions AS d ON en.EnDivision=d.DivId AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";

		$MyQuery.= "WHERE ToId = " . StrSafe_DB($_SESSION['TourId']) . " ";
		$MyQuery.= "ORDER BY TeEvent, TeScore DESC, TeGold DESC, TeXnine DESC, CoCode,TcOrder";


	//print $MyQuery;exit;
		$Rs=safe_r_sql($MyQuery);

		if (safe_num_rows($Rs)>0)
		{
			$CurEvent = "";
			$CurTeam = "";
		// Variabili per la gestione del ranking
			$MyRank = 1;
			$MyPos = 0;
		// Variabili che contengono i punti del precedente atleta per la gestione del rank
			$MyScoreOld = 0;
			$MyGoldOld = 0;
			$MyXNineOld = 0;

			$Score1=0;
			$Score2=0;
			$Gold1=0;
			$Gold2=0;
			$Xnine1=0;
			$Xnine2=0;

			$TmpMatr='';

			while($MyRow=safe_fetch($Rs))
			{
				if ($CurEvent!=$MyRow->TeEvent)
				{
				// ultimo totale prima di cambiare evento
					if ($CurEvent!='')
					{
						$StrData
							.=$Score1 . ';'
							. $Gold1 . ';'
							. $Xnine1 . ';'
							. $Score2 . ';'
							. $Gold2 . ';'
							. $Xnine2 . ';'
							. $MyRank . ';'
							. substr($TmpMatr,0,-1) . "\n";
					}

					$TmpMatr='';

					$CurTeam = "";
					$MyRank = 1;
					$MyPos = 0;
					$MyScoreOld = 0;
					$MyGoldOld = 0;
					$MyXNineOld = 0;

					$Score1=0;
					$Score2=0;
					$Gold1=0;
					$Gold2=0;
					$Xnine1=0;
					$Xnine2=0;
				}

				if ($CurTeam!=$MyRow->CoCode)
				{
					if ($CurTeam!='')
					{
						$StrData
							.=$Score1 . ';'
							. $Gold1 . ';'
							. $Xnine1 . ';'
							. $Score2 . ';'
							. $Gold2 . ';'
							. $Xnine2 . ';'
							. $MyRank . ';'
							. substr($TmpMatr,0,-1) . "\n";

						$TmpMatr='';
						$Score1=0;
						$Score2=0;
						$Gold1=0;
						$Gold2=0;
						$Xnine1=0;
						$Xnine2=0;
					}

					$MyPos++;
					// Se non ho parimerito il ranking ? uguale alla posizione
					if (!($MyRow->TeScore==$MyScoreOld && $MyRow->TeGold==$MyGoldOld && $MyRow->TeXnine==$MyXNineOld))
						$MyRank = $MyPos;

					$StrData
						.='3;'
						. $MyRow->CoCode . ';'
						. substr($MyRow->TeEvent,0,2) . ';' . substr($MyRow->TeEvent,2,2) . ';';

				}

				$Score1+=$MyRow->Score1;
				$Gold1+=$MyRow->Gold1;
				$Xnine1+=$MyRow->Xnine1;
				$Score2+=$MyRow->Score2;
				$Gold2+=$MyRow->Gold2;
				$Xnine2+=$MyRow->Xnine2;

				$TmpMatr.=$MyRow->EnCode . ';';

				$CurEvent = $MyRow->TeEvent;
				$CurTeam = $MyRow->CoCode;

				$MyScoreOld = $MyRow->TeScore;
				$MyGoldOld = $MyRow->TeGold;
				$MyXNineOld = $MyRow->TeXnine;
			}

		// ultimissimo totale
			$StrData
				.=$Score1 . ';'
				. $Gold1 . ';'
				. $Xnine1 . ';'
				. $Score2 . ';'
				. $Gold2 . ';'
				. $Xnine2 . ';'
				. $MyRank . ';'
				. substr($TmpMatr,0,-1) . "\n";
		}
//print $StrData;Exit;
	/*
	 * Tipo 4: Finale a Squadre
	 * CodiceSocieta-Evento-PosPartenza|FaseIniziale-PosizioneClassifica-ScoresDelleFasi
	 */
	//Carico le fasi in un array
		$myPhases=getPhaseArray();

	//Genero la query che mi ritorna tutti gli eventi a squadre
		$MyQuery = "SELECT EvCode, EvFinalFirstPhase, EvEventName, EvFinalPrintHead,EvMatchMode,EvMatchArrowsNo,EvMixedTeam ";
		$MyQuery.= "FROM Events ";
		$MyQuery.= "WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 ";
		if (!is_null($Event) && preg_match("/^[0-9A-Z]{1,4}$/i",$Event))
			$MyQuery.= "AND EvCode LIKE '" . $Event . "' ";
		$MyQuery.= "ORDER BY  EvProgr ASC, EvCode ";
		$RsEv=safe_r_sql($MyQuery);

		if (safe_num_rows($RsEv)>0)
		{
			$RsEvCounter=0;
			while ($MyRowEv=safe_fetch($RsEv))
			{
				//$target=$GLOBALS{GetTargetType($MyRowEv->EvCode,1)};

				$PhaseFields=array();
				reset($myPhases);
				//Genero la query che mi da i risultati per ogni evento
				$MyQuery = "SELECT TfTeam, CoName, CoCode, TeRank,TeRankFinal, ";
				$Tmp="";
				$NumPhases=0;
				$NeedTitle=true;
				foreach($myPhases as $Key => $Value)
				{
					if($Key<=valueFirstPhase($MyRowEv->EvFinalFirstPhase))
					{
						// mi servirà dopo nei calcoli dei campi!
						$PhaseFields[]=array(
							"X_Phase" => namePhase($MyRowEv->EvFinalFirstPhase,$Key)  . "_Phase",
							"X_SetPoints" => namePhase($MyRowEv->EvFinalFirstPhase,$Key)  . "_SetPoints",
							"X_Arrowstring" => namePhase($MyRowEv->EvFinalFirstPhase,$Key)  . "_Arrowstring",
							"X_TieArrowstring" => namePhase($MyRowEv->EvFinalFirstPhase,$Key)  . "_TieArrowstring",
							"X_Tie" => $Value . "Tie",
							"X_Live" => $Value . "Live",
							"X_Matchno" => $Value . "Matchno",
							);
						/*if($Key!=0)
						{
							$MyQuery .= "SUM(IF(GrPhase=" . $Key . ",IF(TfScore=0 && TfTie=2,(TeScore*10),TfScore),0)) as `" . $Key  . "_Phase`, SUM(IF(GrPhase=" . $Key . ",TfTie,0)) as `" . $Value . "Tie`, SUM(IF(GrPhase=" . $Key . ",LENGTH(TfArrowstring),0)) as `" . $Value . "Live`, SUM(IF(GrPhase=" . $Key . ",TfMatchNo,0)) as `" . $Value . "Matchno`, ";
							$Tmp = ", `" . $Key . "_Phase` DESC " . $Tmp;
							$NumPhases++;
						}
						else
						{
							$MyQuery .= "SUM(IF((GrPhase=0 OR GrPhase=1),TfScore,0)) as `" . $Key . "_Phase`, SUM(IF((GrPhase=0 OR GrPhase=1),TfTie,0)) as `" . $Value . "Tie`, SUM(IF((GrPhase=0 OR GrPhase=1),LENGTH(TfArrowstring),0)) as `" . $Value . "Live`, SUM(IF((GrPhase=0 OR GrPhase=1),TfMatchNo,0)) as `" . $Value . "Matchno`, ";
							$Tmp = ", `" . $Key . "_Phase` DESC, `" . $Value . "Tie` DESC " . $Tmp;
							$NumPhases++;
						}*/
						if($Key!=0)
						{
							//$MyQuery .= "SUM(IF(GrPhase=" . $Key . ",IF(IF(EvMatchMode=0,FinScore,FinSetScore)=0 && FinTie=2,(QuScore*10),IF(EvMatchMode=0,FinScore,FinSetScore)),0)) as `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key)  . "_Phase`, SUM(IF(GrPhase=" . $Key . ",FinTie,0)) as `" . $Value . "Tie`,GROUP_CONCAT(IF(GrPhase=" . $Key . ",FinArrowstring,'') SEPARATOR '') AS `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_Arrowstring`, GROUP_CONCAT(IF(GrPhase=" . $Key . ",FinTieBreak,'') SEPARATOR '') as `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_TieArrowstring`, GROUP_CONCAT(IF(GrPhase=" . $Key . ",FinSetPoints,'') SEPARATOR '') AS `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_SetPoints`, SUM(IF(GrPhase=" . $Key . ",FinMatchNo,0)) as `" . $Value . "Matchno`, ";
							$MyQuery .= "SUM(IF(GrPhase=" . $Key . ",IF(TfScore=0 && TfTie=2,(TeScore*10),TfScore),0)) as `" . $Key  . "_Phase`, SUM(IF(GrPhase=" . $Key . ",TfTie,0)) as `" . $Value . "Tie`, GROUP_CONCAT(IF(GrPhase=" . $Key . ",TfTieBreak,'') SEPARATOR '') as `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_TieArrowstring`, GROUP_CONCAT(IF(GrPhase=" . $Key . ",TfSetPoints,'') SEPARATOR '') AS `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_SetPoints`, GROUP_CONCAT(IF(GrPhase=" . $Key . ",TfArrowstring,'') SEPARATOR '') AS `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_Arrowstring`, SUM(IF(GrPhase=" . $Key . ",TfMatchNo,0)) as `" . $Value . "Matchno`, ";
							$Tmp = ", `" . $Key . "_Phase` DESC " . $Tmp;
							if($Key==4)
							{
								$MyQuery .= "SUM(IF(GrPhase=" . $Key . ",TfScore,0)) as `QuarterScore`, ";
								$Tmp = ", `QuarterScore` DESC " . $Tmp;
							}
							$NumPhases++;
						}
						else
						{
							//$MyQuery .= "SUM(IF((GrPhase=0 OR GrPhase=1),IF(EvMatchMode=0,FinScore,FinSetScore),0)) as `" . $Key . "_Phase`, SUM(IF((GrPhase=0 OR GrPhase=1),FinTie,0)) as `" . $Value . "Tie`, GROUP_CONCAT(IF((GrPhase=0 OR GrPhase=1),FinTieBreak,'') SEPARATOR '') as `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_TieArrowstring`, GROUP_CONCAT(IF((GrPhase=0 OR GrPhase=1),FinSetPoints,'')  SEPARATOR '') AS `" . $Key. "_SetPoints`, GROUP_CONCAT(IF((GrPhase=0 OR GrPhase=1),FinArrowstring,'')  SEPARATOR '') AS `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key). "_Arrowstring`,SUM(IF((GrPhase=0 OR GrPhase=1),FinMatchNo,0)) as `" . $Value . "Matchno`, ";
							$MyQuery .= "SUM(IF((GrPhase=0 OR GrPhase=1),TfScore,0)) as `" . $Key . "_Phase`, SUM(IF((GrPhase=0 OR GrPhase=1),TfTie,0)) as `" . $Value . "Tie`, GROUP_CONCAT(IF((GrPhase=0 OR GrPhase=1),TfTieBreak,'')) as `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_TieArrowstring`,GROUP_CONCAT(IF((GrPhase=0 OR GrPhase=1),TfSetPoints,'') SEPARATOR '') AS `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_SetPoints`, GROUP_CONCAT(IF((GrPhase=0 OR GrPhase=1),TfArrowstring,'') SEPARATOR '') AS `" . namePhase($MyRowEv->EvFinalFirstPhase,$Key) . "_Arrowstring`,  SUM(IF((GrPhase=0 OR GrPhase=1),TfMatchNo,0)) as `" . $Value . "Matchno`, ";
							$Tmp = ", `" . $Key . "_Phase` DESC, `" . $Value . "Tie` DESC " . $Tmp;
							$NumPhases++;
						}
					}
				}
				$MyQuery .= "MIN(GrPhase) as LastPhase, ifnull(CurrentPhase,128) as CurrentPhase, TeScore ";
				$MyQuery .= "FROM TeamFinals ";
				$MyQuery .= "INNER JOIN Events ON TfEvent=EvCode AND TfTournament=EvTournament AND EvTeamEvent=1 ";
				$MyQuery .= "INNER JOIN Grids ON TfMatchNo=GrMatchNo ";
				$MyQuery .= "INNER JOIN Countries ON TfTeam=CoId AND TfTournament=CoTournament ";
				$MyQuery .= "INNER JOIN Teams ON TfTeam=TeCoId AND TfEvent=TeEvent AND TfTournament=TeTournament AND TeFinEvent=1 ";
				$MyQuery .= "LEFT JOIN (SELECT min(GrPhase) AS CurrentPhase, TfTournament AS SqyTournament, TfEvent AS SqyEvent "
					. "FROM TeamFinals INNER JOIN Grids ON TfMatchNo=GrMatchNo "
					. "WHERE TfScore<>0 GROUP BY SqyTournament, SqyEvent) AS Sqy ON SqyTournament=TfTournament AND SqyEvent=TfEvent ";
				$MyQuery .= "WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($MyRowEv->EvCode) . " ";
				$MyQuery .= "GROUP BY TfTeam, CoName, CoCode ";
				$MyQuery .= "ORDER BY TfEvent, LastPhase ASC " . $Tmp . ", TeRank ASC";

				//print $MyQuery . "<br>";

				$Rs=safe_r_sql($MyQuery);
				$MyPos=0;
			//	$MyRank=0;

				//Se Esistono righe caricate....
				if(safe_num_rows($Rs)>0)
				{
//					if($RsEvCounter++)
//						$pdf->AddPage();
//					$pdf->SetXY(10,$pdf->GetY()+5);
//					$OldScore=-1;
//					$OldTie=-1;
//					$OldLastPhase=-1;

					$ActualScore=0;
					$ActualTie=-1;
					$ActualMatch=0;

					while($MyRow=safe_fetch($Rs))
					{
						$TmpScores=array();
						$cols=array();

						$MyPos++;
					// Se non ho parimerito il ranking � uguale alla posizione
						if($MyPos>$MyRow->CurrentPhase)
						{
							$TmpScores=array();
							$ActualScore=0;
							$ActualMatch=0;
							$ActualTie=-1;
							foreach($PhaseFields as $Key => $Val) {
								$ActualScore=($MyRow->{$Val["X_Phase"]}!=0 ? $MyRow->{$Val["X_Phase"]} : $ActualScore);
								$ActualMatch=($MyRow->{$Val["X_Matchno"]}!=0 ? $MyRow->{$Val["X_Matchno"]} : $ActualMatch);
								$ActualTie=($MyRow->LastPhase<2 ? $MyRow->{$Val["X_Tie"]} : -1);

								$TmpScores[]=($MyRow->{$Val["X_Phase"]}==($MyRow->TeScore*10) ? 'bye' : $MyRow->{$Val["X_Phase"]}) . '|';

								list($tmpPhase,)=explode('_',$Val['X_Phase']);
								//$x=DecodeArrowstring($MyRowEv->EvMatchMode,$MyRowEv->EvMatchArrowsNo,1,$MyRowEv->EvMixedTeam,$tmpPhase,$MyRow->{$Val["X_Arrowstring"]},$target);
								//$x=DecodeArrowstring($MyRowEv->EvMatchMode,$MyRowEv->EvMatchArrowsNo,1,$MyRowEv->EvMixedTeam,$tmpPhase,$MyRow->{$Val["X_Arrowstring"]});

								$numRows=0;
								$numCols=0;
								$obj=getEventArrowsParams($MyRowEv->EvCode,$tmpPhase,1);
								$numRows=$obj->ends;
								$numCols=$obj->arrows;

								$x=DecodeArrowstring($numRows,$numCols,$MyRow->{$Val["X_Arrowstring"]});
								$TmpScores[count($TmpScores)-1].='_'.$x;


								//$x=DecodeTieArrowstring($MyRow->{$Val['X_TieArrowstring']},$target);
								$x=DecodeTieArrowstring($MyRow->{$Val['X_TieArrowstring']});
								$TmpScores[count($TmpScores)-1].='#'.$x;
							}
							if($MyRow->LastPhase>=8)
							{
							//	$MyRank=$MyRow->LastPhase+1;
							}
							elseif($MyRow->LastPhase==4)
							{
								$ActualScore=$MyRow->QuarterScore;
								$ActualTie=$MyRow->QuarterScore;
//								if ($OldScore!=$ActualScore || $OldTie!=$ActualTie || $OldLastPhase!=$MyRow->LastPhase)
//								{
//									$MyRank = $MyPos;
//								}
							}

						}
//						else
//							$MyRank = -1;

						//Per i primi 4 NON vale la regola sopra
//						if($MyPos<=4)
//						{
//							if($MyRow->CurrentPhase==1 && $MyRank>2)
//								$MyRank = $MyPos;
//							elseif($MyRow->CurrentPhase==0)
//								$MyRank = $MyPos;
//
//						}

						//Tolgo tutti Quelli da non scrivere


						//Salvo i valori attuali e risistemo i colori
//						$OldScore=$ActualScore;
//						$OldTie=$ActualTie;
//						$OldLastPhase=$MyRow->LastPhase;
//						$OldMatch=$ActualMatch;

						$cols[]='4';
						$cols[]=$MyRow->CoCode;
						$cols[]=$MyRowEv->EvCode;
						$cols[]=$MyRow->TeRank . '|' . $MyRowEv->EvFinalFirstPhase;
						//$cols[]=$MyRank;
						$cols[]=$MyRow->TeRankFinal;

						$StrData.=join(';',$cols) . ';' . join(';',$TmpScores) . "\n";
					}
				}
			}
		}

	/*
	 * Tipo 5/6: Classifica eliminatorie, fase 1 e fase 2
	 * Matricola-Divisione-Classe-CognomeNome-Societa-Evento-Totale-Ori-X-CodiceFiscale-PosizioneClassifica
	 *
	 */
		for ($tipo=5;$tipo<=6;++$tipo)
		{
			$phase=$tipo-5;

			$Query
				= "SELECT "
					. "'" . ($tipo) . "' AS RowType,EnCode as Bib, EnDivision, EnClass,"
					. "CONCAT(EnFirstName,' ',EnName) AS Name, CoCode,ElEventCode, "
					. "ElScore,ElGold,ElXnine,EnCtrlCode,ElRank "
				. "FROM "
					. "Entries "
					. "INNER JOIN "
						. "Countries "
					. "ON EnCountry=CoId AND EnTournament=CoTournament "
					. "INNER JOIN "
						. "Eliminations "
					. "ON EnId=ElId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ElElimPhase=" . $phase . " "
				. "ORDER BY "
					. "ElEventCode ASC, (ElScore*1.0) DESC, ElRank ASC, ElGold DESC, ElXnine DESC ";

			//print $Query . '<br><br/>';

			$Rs=safe_r_sql($Query);

			$CurEvent = "";
		// Variabili per la gestione del ranking
//			$MyRank = 1;
//			$MyPos = 0;

		// Variabili che contengono i punti del precedente atleta per la gestione del rank
//			$MyScoreOld = 0;
//			$MyGoldOld = 0;
//			$MyXNineOld = 0;

			if (safe_num_rows($Rs)>0)
			{
				while ($myRow=safe_fetch($Rs))
				{
//					if ($CurEvent!=$myRow->ElEventCode)
//					{
//
//						$MyRank = 1;
//						$MyPos = 0;
//						$MyScoreOld = 0;
//						$MyGoldOld = 0;
//						$MyXNineOld = 0;
//					}
//
//					++$MyPos;

//					if (!($myRow->ElScore==$MyScoreOld && $myRow->ElGold==$MyGoldOld && $myRow->ElXnine==$MyXNineOld))
//						$MyRank = $MyPos;

					$StrData
						.=$myRow->RowType . ';'
						. $myRow->Bib . ';'
						. $myRow->EnDivision . ';'
						. $myRow->EnClass . ';'
						. $myRow->Name . ';'
						. $myRow->CoCode . ';'
						. $myRow->ElEventCode . ';'
						. $myRow->ElScore . ';'
						. $myRow->ElGold . ';'
						. $myRow->ElXnine . ';'
						. $myRow->EnCtrlCode . ';'
						//. ($myRow->ElRank!=0 ? $myRow->ElRank : $MyRank)
						. $myRow->ElRank
						. "\n";

//					$MyScoreOld = $myRow->ElScore;
//					$MyGoldOld = $myRow->ElGold;
//					$MyXNineOld = $myRow->ElXnine;

					$CurEvent=$myRow->ElEventCode;
				}
			}

		}
	/*
	 * Tipo 7: assoluti individuali
	 * Matricola-Divisione-Classe-CognomeNome-Societa-CodiceEvento-Totale1-ori1-X1-Totale2-Ori2-X2-CodiceDiControllo-PosizioneClassificaIndividuale(999 se nn partecipa)-Status-Singole distanze-tiearrowstring
	 */

		$MyQuery = "SELECT EnCode as Bib, EnName AS Name, CONCAT(EnFirstName,' ',EnName) AS Name,SUBSTRING(QuTargetNo,1,1) AS Session, SUBSTRING(QuTargetNo,2) AS TargetNo, CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode, EnAgeClass as AgeClass, EnDivision AS DivCode, EvCode as EventCode, EvEventName as EventName, EvQualPrintHead,";
		$MyQuery.= "IF(EvElim1=0 && EvElim2=0,EvNumQualified,IF(EvElim1=0,EvElim2,EvElim1)) as QualifiedNo, ";
		$MyQuery.= "ToNumDist AS NumDist, Td1, Td2, Td3, Td4, Td5, Td6, Td7, Td8, ";
		for ($i=1;$i<=$NumDist;++$i)
		{
			$MyQuery.="QuD" . $i . "Score,QuD" . $i . "Gold,QuD" . $i . "Xnine,QuD" . $i . "Arrowstring AS Arrowstring" . $i . ", ";
		}
		$MyQuery.= "QuScore, IndRank, QuGold, QuXnine, ToGolds AS TtGolds, ToXNine AS TtXNine,  ";
		if ($ToType==8)
		{
			$MyQuery .= "(QuD1Score+QuD2Score) AS Score1, "
				. "(QuD1Gold+QuD2Gold) AS Gold1, "
				. "(QuD1Xnine+QuD2Xnine) AS Xnine1, "
				. "(QuD3Score+QuD4Score) AS Score2, "
				. "(QuD3Gold+QuD4Gold) AS Gold2, "
				. "(QuD3Xnine+QuD4Xnine) AS Xnine2, ";
		}
		else if ($ToType==10 || $ToType==12 || $ToType==13)
		{
			$MyQuery .= "(QuD1Score) AS Score1, "
				. "(QuD1Gold) AS Gold1, "
				. "(QuD1Xnine) AS Xnine1, "
				. "(QuD2Score) AS Score2, "
				. "(QuD2Gold) AS Gold2, "
				. "(QuD2Xnine) AS Xnine2, ";
		}
		else
		{
			$MyQuery .= "(QuD1Score+QuD2Score+QuD3Score+QuD4Score) AS Score1, "
				. "(QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold) AS Gold1, "
				. "(QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine) AS Xnine1, "
				. "(QuD5Score+QuD6Score+QuD7Score+QuD8Score) AS Score2, "
				. "(QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold) AS Gold2, "
				. "(QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine) AS Xnine2, ";
		}
		$MyQuery.="QuTieBreak AS ArrowstringTie, ";

		$MyQuery .= "IF(LENGTH(EnCtrlCode)=16,EnCtrlCode,CONCAT(EnDob,'|',EnSex)) AS EnCtrlCode, IndRank, EnStatus ";
		$MyQuery.= "FROM Tournament AS t ";
		$MyQuery.= "INNER JOIN Entries AS e ON t.ToId=e.EnTournament ";
		$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
		$MyQuery.= "INNER JOIN Qualifications AS q ON e.EnId=q.QuId ";
		$MyQuery.= "INNER JOIN Individuals i ON i.IndId=e.EnId AND i.IndTournament=e.EnTournament ";
		$MyQuery.= "INNER JOIN Events AS ev ON ev.EvCode=IndEvent AND ev.EvTeamEvent=0 AND ev.EvTournament=EnTournament ";
		$MyQuery.= "LEFT JOIN TournamentDistances AS td ON t.ToType=td.TdType and TdTournament=ToId AND CONCAT(TRIM(e.EnDivision),TRIM(e.EnClass)) LIKE TdClasses ";
		//Where Normale
		$MyQuery.= "WHERE EnAthlete=1 AND EnIndFEvent=1 AND EnStatus <= 1  AND QuScore<>'0' AND ToId = " . StrSafe_DB($_SESSION['TourId']) . " ";
		$MyQuery.= "ORDER BY EvProgr, EvCode, QuScore DESC, IndRank ASC, QuGold DESC, QuXnine DESC, IndRank, EnFirstName,EnName ";
		//print $MyQuery;exit;
		$Rs=safe_r_sql($MyQuery);

		//$target=$GLOBALS['TrgOutdoor'];

		if($Rs && safe_num_rows($Rs)>0)
		{
			$CurGroup = "....";
		// Variabili per la gestione del ranking
			$MyRank = 1;
			//$MyPos = 0;
			$EndQualified = false;
		// Variabili che contengono i punti del precedente atleta per la gestione del rank
//			$MyScoreOld = 0;
//			$MyGoldOld = 0;
//			$MyXNineOld = 0;
			$MyEndScore=-1;
			$MyGroupStartPos=0;
			$CurrentRow=-1;

			while($MyRow=safe_fetch($Rs))
			{
				$CurrentRow++;
				if ($CurGroup != $MyRow->EventCode)
				{
					$CurGroup = $MyRow->EventCode;

//					$MyRank = 1;
//					$MyPos = 0;
//					$MyScoreOld = 0;
//					$MyGoldOld = 0;
//					$MyXNineOld = 0;
					$EndQualified = false;
					$MyGroupStartPos = $CurrentRow;
				//Carico l'ultimo punteggio per entrare
				// Vado a brancare la riga con l'ultimo Score buono
					if(safe_num_rows($Rs) > ($MyGroupStartPos + $MyRow->QualifiedNo))
					{
						safe_data_seek($Rs,$MyGroupStartPos + $MyRow->QualifiedNo -1);
						$tmpMyRow = safe_fetch($Rs);
						if($CurGroup == $tmpMyRow->EventCode)
						{
							$MyEndScore = $tmpMyRow->QuScore;
							$tmpMyRow = safe_fetch($Rs);
							//Controllo se c'è parimerito per entrare
							if ($MyEndScore != $tmpMyRow->QuScore || $CurGroup != $tmpMyRow->EventCode) {
								$MyEndScore *= -1;
							}
						}
						else
							$MyEndScore = -1;
						$tmpMyRow = NULL;
					}
					else
					{
						safe_data_seek($Rs,safe_num_rows($Rs)-1);
						$tmpMyRow = safe_fetch($Rs);
						$MyEndScore = -1;
					}
					safe_data_seek($Rs,$MyGroupStartPos+1);
				}

				$MyRank=$MyRow->IndRank;

				//$MyPos++;
				// Se non ho parimerito il ranking è uguale alla posizione
//				if($MyEndScore == $MyRow->QuScore)  //Spareggio
//				{
//					if ($MyRow->QuScore!=$MyScoreOld)
//						$MyRank = $MyPos;
//				}
//				else
//				{
//					if (!($MyRow->QuScore==$MyScoreOld && $MyRow->QuGold==$MyGoldOld && $MyRow->QuXnine==$MyXNineOld))
//						$MyRank = $MyPos;
//				}


				if($MyRank > $MyRow->QualifiedNo && !$EndQualified)
				{
					$EndQualified = true;
				}

//				$MyScoreOld = $MyRow->QuScore;
//				$MyGoldOld = $MyRow->QuGold;
//				$MyXNineOld = $MyRow->QuXnine;

			// qui butto fuori la riga
				$cols=array();
				$cols[]=7;
				$cols[]=$MyRow->Bib;
				$cols[]=$MyRow->DivCode;
				$cols[]=$MyRow->ClassCode;
				$cols[]=$MyRow->Name;
				$cols[]=$MyRow->NationCode;
				$cols[]=$MyRow->EventCode;
				$cols[]=$MyRow->Score1;
				$cols[]=$MyRow->Gold1;
				$cols[]=$MyRow->Xnine1;
				$cols[]=$MyRow->Score2;
				$cols[]=$MyRow->Gold2;
				$cols[]=$MyRow->Xnine2;
				$cols[]=$MyRow->EnCtrlCode;
				//$cols[]=($MyRow->IndRank==0 ? $MyRank : $MyRow->IndRank);
				$cols[]=$MyRank;
				$cols[]=$MyRow->EnStatus;
				for ($i=1;$i<=$NumDist;++$i)
				{
					$cols[]=$MyRow->{'QuD' . $i . 'Score'};
					$cols[]=$MyRow->{'QuD' . $i . 'Gold'};
					$cols[]=$MyRow->{'QuD' . $i . 'Xnine'};

					$c=array();
					$v=$MyRow->{'Arrowstring' . $i};
					for ($k=0;$k<strlen($v);++$k)
					{
						$c[]=DecodeFromLetter($v[$k]);
					}
					$cols[]=implode(',',$c);

				}

				$c=array();
				$v=$MyRow->ArrowstringTie;
				for ($k=0;$k<strlen($v);++$k)
				{
					$c[]=DecodeFromLetter($v[$k]);
				}
				$cols[]=implode(',',$c);


				$StrData.=join(';',$cols) . "\n";
			}
		}

	/*
	 * Tipo 8: Assoluti a squadre
	 * CodiceSocieta-Evento-Totale1-ori1-X1-Totale2-Ori2-X2-PosizioneClassifica-MatricolaPartecipanti(in lista)
	 */

		/*$MyQuery = "SELECT EnCode,CoCode AS NationCode, TeSubTeam as SubTeam, CoName AS Nation, TeEvent, EvEventName, Quanti, EnFirstName as FirstName, EnName AS Name,  EnClass AS ClassCode, EnDivision AS DivCode,EnAgeClass as AgeClass,  EnSubClass as SubClass, sqY.QuantiPoss as NumGialli, (EvFinalFirstPhase*2) as QualifiedNo, EvQualPrintHead, ";
		$MyQuery.= "QuScore, TeScore, TeRank, TeGold, TeXnine, TtGolds, TtXNine, ";
		if ($ToType==8)
		{
			$MyQuery .= "(QuD1Score+QuD2Score) AS Score1, "
				. "(QuD1Gold+QuD2Gold) AS Gold1, "
				. "(QuD1Xnine+QuD2Xnine) AS Xnine1, "
				. "(QuD3Score+QuD4Score) AS Score2, "
				. "(QuD3Gold+QuD4Gold) AS Gold2, "
				. "(QuD3Xnine+QuD4Xnine) AS Xnine2 ";
		}
		else if ($ToType==10 || $ToType==12 || $ToType==13)
		{
			$MyQuery .= "(QuD1Score) AS Score1, "
				. "(QuD1Gold) AS Gold1, "
				. "(QuD1Xnine) AS Xnine1, "
				. "(QuD2Score) AS Score2, "
				. "(QuD2Gold) AS Gold2, "
				. "(QuD2Xnine) AS Xnine2 ";
		}
		else
		{
			$MyQuery .= "(QuD1Score+QuD2Score+QuD3Score+QuD4Score) AS Score1, "
				. "(QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold) AS Gold1, "
				. "(QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine) AS Xnine1, "
				. "(QuD5Score+QuD6Score+QuD7Score+QuD8Score) AS Score2, "
				. "(QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold) AS Gold2, "
				. "(QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine) AS Xnine2 ";
		}
		$MyQuery.= "FROM Tournament AS t ";
		$MyQuery.= "INNER JOIN Tournament*Type AS tt ON t.ToType=tt.TtId ";
		$MyQuery.= "INNER JOIN Teams AS te ON t.ToId=te.TeTournament AND te.TeFinEvent=1 ";
		$MyQuery.= "INNER JOIN Countries AS c ON te.TeCoId=c.CoId AND te.TeTournament=c.CoTournament ";
		$MyQuery.= "INNER JOIN Events AS ev ON te.TeEvent=ev.EvCode AND t.ToId=ev.EvTournament AND EvTeamEvent=1 ";
		$MyQuery.= "INNER JOIN (SELECT TcCoId, TcSubTeam, TcEvent, TcTournament, TcFinEvent, COUNT(TcId) as Quanti FROM TeamComponent GROUP BY TcCoId, TcSubTeam, TcEvent, TcTournament) AS sq ON te.TeCoId=sq.TcCoId AND te.TeSubTeam=sq.TcSubTeam AND te.TeEvent=sq.TcEvent AND te.TeTournament=sq.TcTournament AND te.TeFinEvent=sq.TcFinEvent ";
		$MyQuery.= "INNER JOIN TeamComponent AS tc ON te.TeCoId=tc.TcCoId AND te.TeSubTeam=tc.TcSubTeam AND  te.TeEvent=tc.TcEvent AND te.TeTournament=tc.TcTournament AND te.TeFinEvent=tc.TcFinEvent ";
		$MyQuery.= "INNER JOIN Entries AS en ON tc.TcId=en.EnId ";
		$MyQuery.= "INNER JOIN Qualifications AS q ON en.EnId=q.QuId ";
		//Contatori per Coin toss  & Spareggi
		$MyQuery .= "INNER JOIN (SELECT Count(*) as QuantiPoss, EvCode as SubCode, TeScore AS Score, TeGold AS Gold, TeXnine AS XNine "
			. "FROM  Teams "
			. "INNER JOIN Events ON TeEvent=EvCode AND TeTournament=EvTournament AND EvTeamEvent=1 "
			. "WHERE TeTournament = " . StrSafe_DB($_SESSION['TourId']) . " "
			. "GROUP BY TeScore, EvCode, TeGold, TeXnine) AS sqY ON sqY.Score=te.TeScore AND sqY.Gold=te.TeGold AND sqY.Xnine=te.TeXnine AND ev.EvCode=sqY.SubCode ";
		//Where Normale
		$MyQuery.= "WHERE ToId = " . StrSafe_DB($_SESSION['TourId']) . " ";
		//if(isset($_REQUEST["Definition"]))
		//	$MyQuery .= "AND te.TeEvent LIKE " . StrSafe_DB($_REQUEST["Definition"]) . " ";
		$MyQuery.= "ORDER BY EvProgr,TeEvent, TeScore DESC, TeGold DESC, TeXnine DESC, TeRank, NationCode, SubTeam, TcOrder ";*/

		$MyQuery = "SELECT EnCode,CoCode AS NationCode, TeSubTeam as SubTeam, CoName AS Nation, TeEvent, EvEventName, Quanti, EnFirstName as FirstName, EnName AS Name,  EnClass AS ClassCode, EnDivision AS DivCode,EnAgeClass as AgeClass,  EnSubClass as SubClass, sqY.QuantiPoss as NumGialli, (EvFinalFirstPhase*2) as QualifiedNo, EvQualPrintHead, ";
		$MyQuery.= "QuScore, TeScore, TeRank, TeGold, TeXnine, ToGolds AS TtGolds, ToXNine AS TtXNine, ";
		if ($ToType==8)
		{
			$MyQuery .= "(QuD1Score+QuD2Score) AS Score1, "
				. "(QuD1Gold+QuD2Gold) AS Gold1, "
				. "(QuD1Xnine+QuD2Xnine) AS Xnine1, "
				. "(QuD3Score+QuD4Score) AS Score2, "
				. "(QuD3Gold+QuD4Gold) AS Gold2, "
				. "(QuD3Xnine+QuD4Xnine) AS Xnine2 ";
		}
		else if ($ToType==10 || $ToType==12 || $ToType==13)
		{
			$MyQuery .= "(QuD1Score) AS Score1, "
				. "(QuD1Gold) AS Gold1, "
				. "(QuD1Xnine) AS Xnine1, "
				. "(QuD2Score) AS Score2, "
				. "(QuD2Gold) AS Gold2, "
				. "(QuD2Xnine) AS Xnine2 ";
		}
		else
		{
			$MyQuery .= "(QuD1Score+QuD2Score+QuD3Score+QuD4Score) AS Score1, "
				. "(QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold) AS Gold1, "
				. "(QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine) AS Xnine1, "
				. "(QuD5Score+QuD6Score+QuD7Score+QuD8Score) AS Score2, "
				. "(QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold) AS Gold2, "
				. "(QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine) AS Xnine2 ";
		}
		$MyQuery.= "FROM Tournament AS t ";
		$MyQuery.= "INNER JOIN Teams AS te ON t.ToId=te.TeTournament AND te.TeFinEvent=1 ";
		$MyQuery.= "INNER JOIN Countries AS c ON te.TeCoId=c.CoId AND te.TeTournament=c.CoTournament ";
		$MyQuery.= "INNER JOIN Events AS ev ON te.TeEvent=ev.EvCode AND t.ToId=ev.EvTournament AND EvTeamEvent=1 ";
		$MyQuery.= "INNER JOIN (SELECT TcCoId, TcSubTeam, TcEvent, TcTournament, TcFinEvent, COUNT(TcId) as Quanti FROM TeamComponent GROUP BY TcCoId, TcSubTeam, TcEvent, TcTournament) AS sq ON te.TeCoId=sq.TcCoId AND te.TeSubTeam=sq.TcSubTeam AND te.TeEvent=sq.TcEvent AND te.TeTournament=sq.TcTournament AND te.TeFinEvent=sq.TcFinEvent ";
		$MyQuery.= "INNER JOIN TeamComponent AS tc ON te.TeCoId=tc.TcCoId AND te.TeSubTeam=tc.TcSubTeam AND  te.TeEvent=tc.TcEvent AND te.TeTournament=tc.TcTournament AND te.TeFinEvent=tc.TcFinEvent ";
		$MyQuery.= "INNER JOIN Entries AS en ON tc.TcId=en.EnId ";
		$MyQuery.= "INNER JOIN Qualifications AS q ON en.EnId=q.QuId ";
		//Contatori per Coin toss  & Spareggi
		$MyQuery .= "INNER JOIN (SELECT Count(*) as QuantiPoss, EvCode as SubCode, TeScore AS Score, TeGold AS Gold, TeXnine AS XNine "
			. "FROM  Teams "
			. "INNER JOIN Events ON TeEvent=EvCode AND TeTournament=EvTournament AND EvTeamEvent=1 "
			. "WHERE TeTournament = " . StrSafe_DB($_SESSION['TourId']) . " "
			. "GROUP BY TeScore, EvCode, TeGold, TeXnine) AS sqY ON sqY.Score=te.TeScore AND sqY.Gold=te.TeGold AND sqY.Xnine=te.TeXnine AND ev.EvCode=sqY.SubCode ";
		//Where Normale
		$MyQuery.= "WHERE ToId = " . StrSafe_DB($_SESSION['TourId']) . " ";
		//if(isset($_REQUEST["Definition"]))
		//	$MyQuery .= "AND te.TeEvent LIKE " . StrSafe_DB($_REQUEST["Definition"]) . " ";
		$MyQuery.= "ORDER BY EvProgr,TeEvent, TeScore DESC, TeGold DESC, TeXnine DESC, TeRank, NationCode, SubTeam, TcOrder ";
//print $MyQuery;exit;
		$Rs=safe_r_sql($MyQuery);
		if($Rs && safe_num_rows($Rs)>0)
		{
			$CurGroup = "....";
			$CurTeam = "";
		// Variabili per la gestione del ranking
			//$MyRank = 1;
			//$MyPos = 0;
			$EndQualified = false;
		// Variabili che contengono i punti del precedente atleta per la gestione del rank
//			$MyScoreOld = 0;
//			$MyGoldOld = 0;
//			$MyXNineOld = 0;
			$MyEndScore=-1;
			$MyGroupStartPos=0;
			$CurrentRow=-1;

			$cols=array();
			$tmpMatr=array();
			$score1=0;
			$score2=0;
			$gold1=0;
			$gold2=0;
			$x1=0;
			$x2=0;

			while($MyRow=safe_fetch($Rs))
			{
				$CurrentRow++;

				if ($CurGroup != $MyRow->TeEvent)
				{
					$CurGroup = $MyRow->TeEvent;

//					$MyRank = 1;
//					$MyPos = 0;
//					$MyScoreOld = 0;
//					$MyGoldOld = 0;
//					$MyXNineOld = 0;
					$EndQualified = false;
					$MyGroupStartPos = $CurrentRow;

					if(safe_num_rows($Rs) > ($MyGroupStartPos + ($MyRow->QualifiedNo)*$MyRow->Quanti))
					{
						safe_data_seek($Rs,$MyGroupStartPos + ($MyRow->QualifiedNo-1)*$MyRow->Quanti);
						$tmpMyRow = safe_fetch($Rs);
						if($CurGroup == $tmpMyRow->TeEvent)
						{
							$MyEndScore = $tmpMyRow->TeScore;
							safe_data_seek($Rs,$MyGroupStartPos + ($MyRow->QualifiedNo)*$MyRow->Quanti);
							$tmpMyRow = safe_fetch($Rs);
							//Controllo se c'è parimerito per entrare
							if ($MyEndScore != $tmpMyRow->TeScore || $CurGroup != $tmpMyRow->TeEvent) {
								$MyEndScore *= -1;
							}
						}
						else
							$MyEndScore = -1;
						$tmpMyRow = NULL;
					}
					else
					{
						safe_data_seek($Rs,safe_num_rows($Rs)-1);
						$tmpMyRow = safe_fetch($Rs);
						$MyEndScore = -1;
					}
					safe_data_seek($Rs,$MyGroupStartPos+1);
					//$CurTeam = "";
				}


				if($CurTeam != $MyRow->NationCode."|".$MyRow->TeEvent )
				{
					if ($CurTeam!='')
					{
					// sostituisco i punti nelle colonne generate in (*) più sotto
						$cols[3]=$score1;
						$cols[4]=$gold1;
						$cols[5]=$x1;
						$cols[6]=$score2;
						$cols[7]=$gold2;
						$cols[8]=$x2;

						$StrData.=implode(';',$cols) . ';' . implode(';',$tmpMatr) . "\n";
						/*print '<pre>';
						print_r($cols);
						print_r($tmpMatr);
						print '</pre>';*/
						$cols=array();
						$tmpMatr=array();
						$score1=0;
						$score2=0;
						$gold1=0;
						$gold2=0;
						$x1=0;
						$x2=0;
					}

					$MyPos++;

					$MyRank=$MyRow->TeRank;

					// Se non ho parimerito il ranking è uguale alla posizione
//					if($MyEndScore == $MyRow->TeScore)  //Spareggio
//					{
//						if ($MyRow->QuScore!=$MyScoreOld)
//							$MyRank = $MyPos;
//					}
//					else
//					{
//						if (!($MyRow->TeScore==$MyScoreOld && $MyRow->TeGold==$MyGoldOld && $MyRow->TeXnine==$MyXNineOld))
//							$MyRank = $MyPos;
//					}

					if($MyRank > $MyRow->QualifiedNo && !$EndQualified)
					{
						$EndQualified = true;
					}

					$cols[]=8;
					$cols[]=$MyRow->NationCode;
					$cols[]=$MyRow->TeEvent;
				// colonne (*)
					$cols[]=0;
					$cols[]=0;
					$cols[]=0;
					$cols[]=0;
					$cols[]=0;
					$cols[]=0;
				// fine colonne (*)
					//$cols[]=($MyRow->TeRank==0 ? $MyRank : $MyRow->TeRank);
					$cols[]=$MyRank;
				}

				$tmpMatr[]=$MyRow->EnCode;
				$score1+=$MyRow->Score1;
				$score2+=$MyRow->Score2;
				$gold1+=$MyRow->Gold1;
				$gold2+=$MyRow->Gold2;
				$x1+=$MyRow->Xnine1;
				$x2+=$MyRow->Xnine2;

//				$MyScoreOld = $MyRow->TeScore;
//				$MyGoldOld = $MyRow->TeGold;
//				$MyXNineOld = $MyRow->TeXnine;
				$CurTeam = $MyRow->NationCode."|".$MyRow->TeEvent;
			}

		// ultima
			$cols[3]=$score1;
			$cols[4]=$gold1;
			$cols[5]=$x1;
			$cols[6]=$score2;
			$cols[7]=$gold2;
			$cols[8]=$x2;
			$StrData.=implode(';',$cols) . ';' . implode(';',$tmpMatr) ."\n";
			/*print '<pre>';
			print_r($cols);
			print_r($tmpMatr);
			print '</pre>';*/
		}

	/*
	 * Tipo 99: Verbale arbitri
	 * Domanda - Risposte
	 */
		/*$MyQuery = "SELECT '99' AS RowType, FraQuestion, REPLACE(FraAnswer,';','\";\"') AS Ans "
			. "FROM FinalReportQ "
			. "INNER JOIN Tournament ON ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "INNER JOIN Tournament*Type ON TtId=ToType "
			. "LEFT JOIN FinalReportA ON FrqId=FraQuestion AND FraTournament=ToId "
			. "WHERE (FrqStatus & TtCategory) > 0 AND FraQuestion IS NOT NULL "
			. "ORDER BY FrqId";*/

		$MyQuery = "SELECT '99' AS RowType, FraQuestion, REPLACE(FraAnswer,';','\";\"') AS Ans "
			. "FROM FinalReportQ "
			. "INNER JOIN Tournament ON ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "LEFT JOIN FinalReportA ON FrqId=FraQuestion AND FraTournament=ToId "
			. "WHERE (FrqStatus & ToCategory) > 0 AND FraQuestion IS NOT NULL "
			. "ORDER BY FrqId";
			//print $MyQuery;exit;

		$Rs=safe_r_sql($MyQuery);

		if (safe_num_rows($Rs)>0)
		{
			while ($MyRow=safe_fetch($Rs))
			{
				$cols=array();
				$cols[]=$MyRow->RowType;
				$cols[]=$MyRow->FraQuestion;
				$cols[]=$MyRow->Ans;

				$StrData.=join(';',$cols) . "\n";
			}
		}

		//exit;
		return (array($StrData,$ToCode));
	}
?>