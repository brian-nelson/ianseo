<?php

/*

Questa funzione serve ad aggiornare le gare salvate con versioni precedenti di Ianseo al formato attuale

Viene richiamata quando la data di versione DB del torneo è diversa (inferiore) alla data attuale di DB

$TourVersion è la versione del torneo importato

*/

function UpdateTournament($Gara) {

	if($Gara['Tournament']['ToDbVersion']<'2010-04-13 14:00:00') {
		// rimedia all'errore introdotto in Arizona e spota il flag da DoubleSpace a WChair
		foreach($Gara['Entries'] as $ID => $Entry) {
			if(!empty($Entry['EnDoubleSpace'])) {
				$Gara['Entries'][$ID]['EnWChair']='1';
				$Gara['Entries'][$ID]['EnDoubleSpace']='0';
			}
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2010-04-17 15:34:01') {
		if(empty($Gara['TournamentDistances'])) {
			// recupera le differenti classi delle entries e le mette in una tabella temporanea
			safe_w_sql("create temporary table TempClasses (TcClass varchar(5) not null, primary key (TcClass) )");
			foreach($Gara['Entries'] as $E) safe_w_sql("insert ignore into TempClasses set TcClass='".$E['EnDivision'].$E['EnClass']."'");

			// riempie la tabella con le occorrenze
			$Gara['TournamentDistances']=array();
			$t=safe_r_sql("SELECT distinct td.* FROM TournamentDistances td inner join TempClasses e on TcClass LIKE TdClasses where TdType='{$Gara['Tournament']['ToType']}' and TdTournament=0");
			while($u=safe_fetch($t)) {
				$u->TdTournament=$Gara['Tournament']['ToId'];
				$qubits=array();
				foreach($u as $key=>$val) {
					$qubits[$key]=$val;
				}
				$Gara['TournamentDistances'][] = $qubits;
			}
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2010-04-25 11:34:00') {
		// procedura di aggiornamento della gara alla versione attuale del database
		foreach($Gara['Entries'] as $ID => $data) {
			if(array_key_exists('EnPhoto',$Gara['Entries'][$ID]))
			{
				$Gara['Entries'][$ID]['EnBadgePrinted']=$Gara['Entries'][$ID]['EnPhoto'];
				unset($Gara['Entries'][$ID]['EnPhoto']);
			}
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2010-09-01 10:15:00')
	{
		//print $Gara['Tournament']['ToType'];exit;
	// aggiungo le colonne nuove alla Tournament
		$Gara['Tournament']['ToLocRule']='';
		$Gara['Tournament']['ToUpNamesUrl']='';
		$Gara['Tournament']['ToUpPhotosUrl']='';
		$Gara['Tournament']['ToUpFlagsUrl']='';
//		$Gara['Tournament']['ToTypeName']='';
//		$Gara['Tournament']['ToNumDist']='0';
//		$Gara['Tournament']['ToNumEnds']='0';
//		$Gara['Tournament']['ToMaxDistScore']='0';
//		$Gara['Tournament']['ToMaxFinIndScore']='0';
//		$Gara['Tournament']['ToMaxFinTeamScore']='0';
//		$Gara['Tournament']['ToCategory']='0';
//		$Gara['Tournament']['ToElabTeam']='0';
//		$Gara['Tournament']['ToElimination']='0';
//		$Gara['Tournament']['ToGolds']='';
//		$Gara['Tournament']['ToXNine']='';
//		$Gara['Tournament']['ToDouble']='0';

	// ora dai tipi di torneo prendo i valori di default e li metto nel vettore
		$TourTypes=array(
			'ToTypeName'	=> 'Type_FITA',
			'ToNumDist'	=> '4',
			'ToNumEnds'	=> '12',
			'ToMaxDistScore'	=> '360',
			'ToMaxFinIndScore'	=> '120',
			'ToMaxFinTeamScore'	=> '240',
			'ToCategory'	=> '1',
			'ToElabTeam'	=> '0',
			'ToElimination'	=> '0',
			'ToGolds'	=> '10',
			'ToXNine'	=> 'X',
			'ToGoldsChars'	=> 'L',
			'ToXNineChars'	=> 'K',
			'ToDouble'	=> '0'
			);
		switch($Gara['Tournament']['ToType']) {
			case '2':
				$TourTypes['ToTypeName']='Type_2xFITA';
				$TourTypes['ToNumDist']='8';
				$TourTypes['ToDouble']='1';
				break;
			case '3':
				$TourTypes['ToTypeName']='Type_70m Round';
				$TourTypes['ToNumDist']='2';
				break;
			case '4':
				$TourTypes['ToTypeName']='Type_FITA 72';
				$TourTypes['ToNumEnds']='6';
				$TourTypes['ToMaxDistScore']='180';
				break;
			case '5':
				$TourTypes['ToTypeName']='Type_900 Round';
				$TourTypes['ToNumDist']='3';
				$TourTypes['ToNumEnds']='10';
				$TourTypes['ToMaxDistScore']='300';
				$TourTypes['ToMaxFinIndScore']='0';
				$TourTypes['ToMaxFinTeamScore']='0';
				break;
			case '6':
				$TourTypes['ToTypeName']='Type_Indoor 18';
				$TourTypes['ToNumDist']='2';
				$TourTypes['ToNumEnds']='10';
				$TourTypes['ToMaxDistScore']='300';
				$TourTypes['ToCategory']='2';
				$TourTypes['ToXNine']='9';
				$TourTypes['ToXNineChars']='J';
				break;
			case '7':
				$TourTypes['ToTypeName']='Type_Indoor 25';
				$TourTypes['ToNumDist']='2';
				$TourTypes['ToNumEnds']='10';
				$TourTypes['ToMaxDistScore']='300';
				$TourTypes['ToCategory']='2';
				$TourTypes['ToXNine']='9';
				$TourTypes['ToXNineChars']='J';
				break;
			case '8':
				$TourTypes['ToTypeName']='Type_Indoor 25+18';
				$TourTypes['ToNumEnds']='10';
				$TourTypes['ToMaxDistScore']='300';
				$TourTypes['ToCategory']='2';
				$TourTypes['ToXNine']='9';
				$TourTypes['ToXNineChars']='J';
				$TourTypes['ToDouble']='1';
				break;
			case '9':
			case '10':
			case '12':
				$TourTypes['ToTypeName']=($Gara['Tournament']['ToType']=='10'?'Type_HF 24+24':'Type_HF 12+12');
				$TourTypes['ToNumDist']=($Gara['Tournament']['ToType']=='9'?'1':'2');
				$TourTypes['ToNumEnds']=($Gara['Tournament']['ToType']=='12'?'12':'24');
				$TourTypes['ToMaxDistScore']=($Gara['Tournament']['ToType']=='12'?'216':'432');
				$TourTypes['ToMaxFinIndScore']='72';
				$TourTypes['ToMaxFinTeamScore']='144';
				$TourTypes['ToCategory']='4';
				$TourTypes['ToElabTeam']='1';
				$TourTypes['ToElimination']='1';
				$TourTypes['ToGolds']='6+5';
				$TourTypes['ToXNine']='6';
				$TourTypes['ToGoldsChars']='FG';
				$TourTypes['ToXNineChars']='G';
				break;
			case '11':
			case '13':
				$TourTypes['ToTypeName']='3D';
				$TourTypes['ToNumDist']=($Gara['Tournament']['ToType']=='11'?'1':'2');
				$TourTypes['ToNumEnds']='20';
				$TourTypes['ToMaxDistScore']='220';
				$TourTypes['ToMaxFinIndScore']='44';
				$TourTypes['ToMaxFinTeamScore']='132';
				$TourTypes['ToCategory']='8';
				$TourTypes['ToElabTeam']='2';
				$TourTypes['ToElimination']='1';
				$TourTypes['ToGolds']='11';
				$TourTypes['ToXNine']='10';
				$TourTypes['ToGoldsChars']='M';
				$TourTypes['ToXNineChars']='L';
				break;
			case '14':
				$TourTypes['ToTypeName']='Type_Las Vegas';
				$TourTypes['ToNumDist']='4';
				$TourTypes['ToNumEnds']='10';
				$TourTypes['ToMaxDistScore']='300';
				$TourTypes['ToMaxFinIndScore']='0';
				$TourTypes['ToMaxFinTeamScore']='0';
				break;
			case '15':
				$TourTypes['ToTypeName']='Type_GiochiGioventu';
				$TourTypes['ToNumDist']='2';
				$TourTypes['ToNumEnds']='8';
				$TourTypes['ToMaxDistScore']='240';
				$TourTypes['ToMaxFinIndScore']='0';
				$TourTypes['ToMaxFinTeamScore']='0';
				break;
			case '16':
				$TourTypes['ToTypeName']='Type_GiochiGioventuW';
				$TourTypes['ToNumDist']='2';
				$TourTypes['ToNumEnds']='8';
				$TourTypes['ToMaxDistScore']='240';
				$TourTypes['ToMaxFinIndScore']='0';
				$TourTypes['ToMaxFinTeamScore']='0';
				$TourTypes['ToCategory']='2';
				$TourTypes['ToXNine']='9';
				$TourTypes['ToXNineChars']='J';
				break;
		}

		$Gara['Tournament']=array_merge($Gara['Tournament'], $TourTypes);

	}

	if($Gara['Tournament']['ToDbVersion']<'2010-09-03 17:45:00')
	{
	// per prima cosa tiro fuori gli eventi delle finali e delle eliminatorie con i bersagli (id)
		$events=array();

		foreach ($Gara['Events'] as $g)
		{
			$events[$g['EvCode']]=$g['EvFinalTargetType'];
		}

		/*print '<pre>';
		print_r($events);*/


	/*
	 * Regole di trasformazione.
	 * la chiave è il tipo di target e ogni array contiene la serie di regole per
	 * le sostituzioni.
	 * Il giochino funziona così:
	 * nelle tabelle interessate c'è la colonna dell'evento.
	 * Da questo posso risalire, usando il vettore $events creato prima, al bersaglio di quell'evento.
	 * Quindi ciclando dentro al vettore dei records di Finals, TeamFinals, Eliminations posso prendere la regola
	 * giusta nel vettore qui sotto ed eseguire la serie di sostituzioni sui campi che a seconda saranno FinArrowstring,
	 * TfArrostring etc... in base alla tabella che sto ciclando.
	 */
		$rules=array
		(
			1 => array(),
			2 => array(),
			3 => array(),
			4 => array(),
			5 => array(),
			6 => array(),
			7 => array(),
			8 => array(),
			10 => array()
		);

	// TrgIndComplete
		$rules[1]=array
		(
			array('from'=>'K','to'=>'L'),
			array('from'=>'k','to'=>'l')
		);

	// TrgIndSmall
		$rules[2]=array
		(
			array('from'=>'B','to'=>'A'),
			array('from'=>'C','to'=>'A'),
			array('from'=>'D','to'=>'A'),
			array('from'=>'E','to'=>'A'),
			array('from'=>'F','to'=>'A'),
			array('from'=>'K','to'=>'L'),
			array('from'=>'b','to'=>'a'),
			array('from'=>'c','to'=>'a'),
			array('from'=>'d','to'=>'a'),
			array('from'=>'e','to'=>'a'),
			array('from'=>'f','to'=>'a'),
			array('from'=>'k','to'=>'l')
		);

	// TrgCOIndComplete
		$rules[3]=array
		(
			array('from'=>'B','to'=>'A'),
			array('from'=>'C','to'=>'A'),
			array('from'=>'D','to'=>'A'),
			array('from'=>'E','to'=>'A'),
			array('from'=>'F','to'=>'A'),
			array('from'=>'L','to'=>'J'),
			array('from'=>'K','to'=>'L'),
			array('from'=>'b','to'=>'a'),
			array('from'=>'c','to'=>'a'),
			array('from'=>'d','to'=>'a'),
			array('from'=>'e','to'=>'a'),
			array('from'=>'f','to'=>'a'),
			array('from'=>'l','to'=>'j'),
			array('from'=>'k','to'=>'l')
		);

	// TrgCOIndSmall
		$rules[4]=array
		(
			array('from'=>'B','to'=>'A'),
			array('from'=>'C','to'=>'A'),
			array('from'=>'D','to'=>'A'),
			array('from'=>'E','to'=>'A'),
			array('from'=>'F','to'=>'A'),
			array('from'=>'L','to'=>'J'),
			array('from'=>'K','to'=>'L'),
			array('from'=>'b','to'=>'a'),
			array('from'=>'c','to'=>'a'),
			array('from'=>'d','to'=>'a'),
			array('from'=>'e','to'=>'a'),
			array('from'=>'f','to'=>'a'),
			array('from'=>'l','to'=>'j'),
			array('from'=>'k','to'=>'l')
		);

	// TrgOutdoor (questo non va toccato!!!)
		$rules[5]=array();

	// TrgField
		$rules[6]=array
		(
			array('from'=>'B','to'=>'A'),
			array('from'=>'C','to'=>'A'),
			array('from'=>'D','to'=>'A'),
			array('from'=>'E','to'=>'A'),
			array('from'=>'F','to'=>'B'),
			array('from'=>'G','to'=>'C'),
			array('from'=>'H','to'=>'D'),
			array('from'=>'I','to'=>'E'),
			array('from'=>'J','to'=>'F'),
			array('from'=>'K','to'=>'G'),
			array('from'=>'L','to'=>'G'),
			array('from'=>'b','to'=>'a'),
			array('from'=>'c','to'=>'a'),
			array('from'=>'d','to'=>'a'),
			array('from'=>'e','to'=>'a'),
			array('from'=>'f','to'=>'b'),
			array('from'=>'g','to'=>'c'),
			array('from'=>'h','to'=>'d'),
			array('from'=>'i','to'=>'e'),
			array('from'=>'j','to'=>'f'),
			array('from'=>'k','to'=>'g'),
			array('from'=>'l','to'=>'g')
		);

	// TrgHMOutComplete
		$rules[7]=array
		(
			array('from'=>'B','to'=>'A'),
			array('from'=>'C','to'=>'A'),
			array('from'=>'D','to'=>'A'),
			array('from'=>'E','to'=>'A'),
			array('from'=>'F','to'=>'A'),
			array('from'=>'G','to'=>'A'),
			array('from'=>'H','to'=>'A'),
			array('from'=>'I','to'=>'A'),
			array('from'=>'J','to'=>'B'),
			array('from'=>'K','to'=>'B'),
			array('from'=>'L','to'=>'B'),
			array('from'=>'b','to'=>'a'),
			array('from'=>'c','to'=>'a'),
			array('from'=>'d','to'=>'a'),
			array('from'=>'e','to'=>'a'),
			array('from'=>'f','to'=>'a'),
			array('from'=>'g','to'=>'a'),
			array('from'=>'h','to'=>'a'),
			array('from'=>'i','to'=>'a'),
			array('from'=>'j','to'=>'b'),
			array('from'=>'k','to'=>'b'),
			array('from'=>'l','to'=>'b')
		);

	// Trg3DComplete
		$rules[8]=array
		(
			array('from'=>'B','to'=>'A'),
			array('from'=>'C','to'=>'A'),
			array('from'=>'D','to'=>'A'),
			array('from'=>'E','to'=>'A'),
			array('from'=>'G','to'=>'A'),
			array('from'=>'H','to'=>'A'),
			array('from'=>'J','to'=>'A'),
			array('from'=>'K','to'=>'M'),
			array('from'=>'b','to'=>'a'),
			array('from'=>'c','to'=>'a'),
			array('from'=>'d','to'=>'a'),
			array('from'=>'e','to'=>'a'),
			array('from'=>'f','to'=>'a'),
			array('from'=>'h','to'=>'a'),
			array('from'=>'j','to'=>'a'),
			array('from'=>'k','to'=>'m')
		);

	// nel db abbiamo due target Trg3DComplete
		$rules[10]=$rules[8];


	/*
	 * Adesso definisco le tabelle su cui ciclare e i campi da considerare.
	 * In chiave ho la tabella e ogni elemento ha la seguente struttura:
	 * array(eventField,fields)
	 * 		con eventField il campo da usare per scoprire l'evento per risalire al bersaglio
	 * 			fields un array con i campi della tabella a cui verranno applicate le regole
	 */
		$tables=array
		(
			'Finals'=>array('eventField'=>'FinEvent','fields'=>array('FinArrowstring','FinTiebreak')),
			'TeamFinals'=>array('eventField'=>'TfEvent','fields'=>array('TfArrowstring','TfTiebreak')),
			'Eliminations'=>array('eventField'=>'ElEventCode','fields'=>array('ElArrowString'))
		);

	// per ogni tabella definita qui sopra...
		foreach ($tables as $table => $infos)
		{
		// fondamentale la '&' prima di $row perchè nel ciclo più interno la variabile viene riassegnata!!!!
			foreach ($Gara[$table] as &$row)
			{
			/*
			 * per risalire al bersaglio della riga devo cercare in $events
			 * la chiave con l'evento corretto che si trova nella colonna  $infos['eventField']
			 * della riga attuale ($row).
			 */
				$target=$events[$row[$infos['eventField']]];

				/*print $target.'<br>';
				print_r($rules[$target]);*/
				/*print '<pre>';
				print_r($row);
				print '</pre>';*/

			/*
			 * adesso ad ogni campo definito in $infos['fields'] applico le regole
			 */
				//print 'target: ' . $target . '<br>';
				foreach ($infos['fields'] as $field)
				{
					//print 'prima: <b>' . $field . '</b> ' . $row[$field] . '<br>';
					foreach ($rules[$target] as $rule)
					{
						$row[$field]=str_replace($rule['from'],$rule['to'],$row[$field]);
					}
					//print 'dopo: <b>' . $field . '</b> ' . $row[$field] . '<br>';
				}
			}
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2010-09-15 09:35:00')
	{
	/*print 'prima<br>';
		print '<pre>';
		print_r($Gara['Events']);
		print '</pre>';*/

	/*
	 * Tabella dei valori.
	 * La chiave indica la terna Ind_Mode_Mixed
	 * con Ind 0 o 1 se individuale o team; Mode 0 o 1 se cumulativo o set; Mixed 0 o 1 se team normale o mixed
	 */

		$rules=array
		(
			'0_0_0' => array('EvElimEnds'=>2,'EvElimArrows'=>6,'EvElimSO'=>3,'EvFinEnds'=>4,'EvFinArrows'=>3,'EvFinSO'=>3),
			'0_1_0' => array('EvElimEnds'=>3,'EvElimArrows'=>6,'EvElimSO'=>1,'EvFinEnds'=>5,'EvFinArrows'=>3,'EvFinSO'=>1),
			'1_0_0' => array('EvElimEnds'=>4,'EvElimArrows'=>6,'EvElimSO'=>9,'EvFinEnds'=>4,'EvFinArrows'=>6,'EvFinSO'=>9),
			'1_0_1' => array('EvElimEnds'=>4,'EvElimArrows'=>4,'EvElimSO'=>6,'EvFinEnds'=>4,'EvFinArrows'=>4,'EvFinSO'=>6)
		);

		if(in_array($Gara['Tournament']['ToType'], array(9,10,12))) {
			// HF, one of 3 types
			$rules=array
			(
				'0_0_0' => array('EvElimEnds'=>12,'EvElimArrows'=>3,'EvElimSO'=>3,'EvFinEnds'=>4,'EvFinArrows'=>3,'EvFinSO'=>3),
				'1_0_0' => array('EvElimEnds'=>8,'EvElimArrows'=>3,'EvElimSO'=>3,'EvFinEnds'=>4,'EvFinArrows'=>3,'EvFinSO'=>3),
			);
		}

		if(in_array($Gara['Tournament']['ToType'], array(11,13))) {
			// 3D, one of 2 types
			$rules=array
			(
				'0_0_0' => array('EvElimEnds'=>12,'EvElimArrows'=>1,'EvElimSO'=>1,'EvFinEnds'=>4,'EvFinArrows'=>1,'EvFinSO'=>1),
				'1_0_0' => array('EvElimEnds'=>8,'EvElimArrows'=>3,'EvElimSO'=>3,'EvFinEnds'=>4,'EvFinArrows'=>3,'EvFinSO'=>3),
			);
		}

		foreach ($Gara['Events'] as $k=>&$v)
		{
			$r=$v['EvTeamEvent'] . '_' . $v['EvMatchMode'] . '_' . $v['EvMixedTeam'];

			foreach ($rules[$r] as $field=>$param)
			{
				$v[$field]=$param;
			}
		}

	/*print 'dopo<br>';
		print '<pre>';
		print_r($Gara['Events']);
		print '</pre>';
		exit;*/
	}

	if($Gara['Tournament']['ToDbVersion']<'2010-09-24 15:00:00') {
		// Aggiorna la FinSchedule inserendo la posizione a bersaglio
		$Events=array();
		foreach ($Gara['FinSchedule'] as $k=>&$v) {
			if($v['FSTarget']) {
				$v['FSLetter']=$v['FSTarget'].'A';
				if($v['FSMatchNo']%2==0) {
					$tmp=$v;
					$tmp['FSMatchNo']=$v['FSMatchNo']+1;
					$Events[]=$tmp;
				}
			}
		}
		foreach ($Gara['FinSchedule'] as $k=>&$v) {
			if($v['FSTarget'] and in_array($v, $Events)) {
				$v['FSLetter']=$v['FSTarget'].'B';
			}
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2010-10-12 15:44:00') {

	// aggiungo la tabella Session
		$Gara['Session']=array();

	// ora la popolo pigliando i dati dalle colonne di Tournament
		$ToNumSession=$Gara['Tournament']['ToNumSession'];

		for ($i=1;$i<=$ToNumSession;++$i)
		{
			$Gara['Session'][]=array
			(
				'SesTournament'=>$Gara['Tournament']['ToId'],
				'SesOrder'=>$i,
				'SesType'=>'Q',
				'SesName'=>'',
				'SesTar4Session'=>$Gara['Tournament']['ToTar4Session' . $i],
				'SesAth4Target'=>$Gara['Tournament']['ToAth4Target' . $i],
				'SesFirstTarget'=>1,
				'SesFollow'=>0
			);
		}

		/*print '<pre>';
		print_r($Gara['Session']);
		print '</pre>';exit;*/

	/*
	 * ATTENZIONE!!!
	 * Qui andrebbe la cancellazione dei vecchi campi
	 *
	 * ToTar4Session1,ToTar4Session2,ToTar4Session3,ToTar4Session4,ToTar4Session5,ToTar4Session6,ToTar4Session7,ToTar4Session8,ToTar4Session9,ToAth4Target1,ToAth4Target2,ToAth4Target3,ToAth4Target4,ToAth4Target5,ToAth4Target6,	ToAth4Target7,ToAth4Target8,ToAth4Target9
	 *
	 * da Tournament
	 */
	}

	if($Gara['Tournament']['ToDbVersion']<'2010-10-18 15:32:00') {
		// Aggiorna i targetFaces
		$Gara['TargetFaces']=array();

		$tmp=array(
			'TfTournament'	=> $Gara['Tournament']['ToId'],
			'TfId'			=> '0',
			'TfName'		=> '~Default',
			'TfClasses'		=> '%',
			'TfDefault'		=> '1',
			'TfT1'			=> '0',
			'TfW1'			=> '0',
			'TfT2'			=> '0',
			'TfW2'			=> '0',
			'TfT3'			=> '0',
			'TfW3'			=> '0',
			'TfT4'			=> '0',
			'TfW4'			=> '0',
			'TfT5'			=> '0',
			'TfW5'			=> '0',
			'TfT6'			=> '0',
			'TfW6'			=> '0',
			'TfT7'			=> '0',
			'TfW7'			=> '0',
			'TfT8'			=> '0',
			'TfW8'			=> '0',
			);

		switch($Gara['Tournament']['ToType']) {
			case 2: //	Type_2xFITA
				$Straight1=($Gara['Tournament']['Td1']>=$Gara['Tournament']['Td2']); // FITA 90-70-50-30 or 4*70m
				$Straight2=($Gara['Tournament']['Td5']>=$Gara['Tournament']['Td6']); // FITA 90-70-50-30 or 4*70m
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = '5';
				$tmp['TfW1'] = ($Straight1?122:80);
				$tmp['TfT2'] = '5';
				$tmp['TfW2'] = ($Straight1?122:80);
				$tmp['TfT3'] = '5';
				$tmp['TfW3'] = ($Straight1?80:122);
				$tmp['TfT4'] = '5';
				$tmp['TfW4'] = ($Straight1?80:122);
				$tmp['TfT5'] = '5';
				$tmp['TfW5'] = ($Straight2?122:80);
				$tmp['TfT6'] = '5';
				$tmp['TfW6'] = ($Straight2?122:80);
				$tmp['TfT7'] = '5';
				$tmp['TfW7'] = ($Straight2?80:122);
				$tmp['TfT8'] = '5';
				$tmp['TfW8'] = ($Straight2?80:122);
				$Gara['TargetFaces'][] = $tmp; // Catch All

				$tmp['TfName'] = '~DefaultVI';
				$tmp['TfClasses'] = 'VI%';
				$tmp['TfId'] = 2;
				$tmp['TfW1'] = ($Straight1?60:122);
				$tmp['TfW2'] = 80;
				$tmp['TfW3'] = 80;
				$tmp['TfW4'] = ($Straight1?122:60);
				$tmp['TfW5'] = ($Straight2?60:122);
				$tmp['TfW6'] = 80;
				$tmp['TfW7'] = 80;
				$tmp['TfW8'] = ($Straight2?122:60);
				$Gara['TargetFaces'][] = $tmp; // VI
				UpdateDivsClass($Gara, '2', array('VI')); // update VI
				break;
			case 1: // Type_FITA
			case 4: //	Type_FITA 72
				$Straight=($Gara['Tournament']['Td1']>=$Gara['Tournament']['Td2']); // FITA 90-70-50-30 or 4*70m
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = '5';
				$tmp['TfW1'] = ($Straight?122:80);
				$tmp['TfT2'] = '5';
				$tmp['TfW2'] = ($Straight?122:80);
				$tmp['TfT3'] = '5';
				$tmp['TfW3'] = ($Straight?80:122);
				$tmp['TfT4'] = '5';
				$tmp['TfW4'] = ($Straight?80:122);
				$Gara['TargetFaces'][] = $tmp; // Catch All

				$tmp['TfName'] = '~DefaultVI';
				$tmp['TfClasses'] = 'VI%';
				$tmp['TfId'] = 2;
				$tmp['TfW1'] = ($Straight?60:122);
				$tmp['TfW2'] = 80;
				$tmp['TfW3'] = 80;
				$tmp['TfW4'] = ($Straight?122:60);
				$Gara['TargetFaces'][] = $tmp; // VI
				UpdateDivsClass($Gara, '2', array('VI')); // update VI
				break;
			case 5: //	Type_900 Round
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = '5';
				$tmp['TfW1'] = 122;
				$tmp['TfT2'] = '5';
				$tmp['TfW2'] = 122;
				$tmp['TfT3'] = '5';
				$tmp['TfW3'] = 122;
				$Gara['TargetFaces'][] = $tmp; // Catch All
				break;
			case 3: //	Type_70m Round
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = '5';
				$tmp['TfW1'] = 122;
				$tmp['TfT2'] = '5';
				$tmp['TfW2'] = 122;
				$Gara['TargetFaces'][] = $tmp; // Catch All

				$tmp['TfName'] = '~DefaultVI';
				$tmp['TfClasses'] = 'VI%';
				$tmp['TfId'] = 2;
				$tmp['TfW1'] = 80;
				$tmp['TfW2'] = 80;
				$Gara['TargetFaces'][] = $tmp; // VI
				UpdateDivsClass($Gara, '2', array('VI')); // update VI
				break;
			case 8: //	Type_Indoor 25+18
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = 1;
				$tmp['TfW1'] = 60;
				$tmp['TfT2'] = 1;
				$tmp['TfW2'] = 60;
				$tmp['TfT3'] = 1;
				$tmp['TfW3'] = 40;
				$tmp['TfT4'] = 1;
				$tmp['TfW4'] = 40;
				$Gara['TargetFaces'][] = $tmp; // Catch All

				$tmp['TfName'] = 'Default COG';
				$tmp['TfClasses'] = 'COG_';
				$tmp['TfId'] = 4;
				$tmp['TfT1'] = 4;
				$tmp['TfW1'] = 80;
				$tmp['TfT2'] = 4;
				$tmp['TfW2'] = 80;
				$tmp['TfT3'] = 4;
				$tmp['TfW3'] = 60;
				$tmp['TfT4'] = 4;
				$tmp['TfW4'] = 60;
				$Gara['TargetFaces'][] = $tmp; // CoGio
				UpdateDivsClass($Gara, '4', array('C','CO','C1'), array('GM','GF')); // update GioCO

				$tmp['TfName'] = 'Default G';
				$tmp['TfClasses'] = '__G_';
				$tmp['TfId'] = 3;
				$tmp['TfT1'] = 1;
				$tmp['TfW1'] = 80;
				$tmp['TfT2'] = 1;
				$tmp['TfW2'] = 80;
				$tmp['TfT3'] = 1;
				$tmp['TfW3'] = 60;
				$tmp['TfT4'] = 1;
				$tmp['TfW4'] = 60;
				$Gara['TargetFaces'][] = $tmp; // Giovanissimi
				UpdateDivsClass($Gara, '3', array(), array('GM','GF')); // update other Gio

				$tmp['TfName'] = '~DefaultVI';
				$tmp['TfClasses'] = 'VI%';
				$tmp['TfId'] = 5;
				$Gara['TargetFaces'][] = $tmp; // VI
				UpdateDivsClass($Gara, '5', array('VI')); // update VI

				$tmp['TfName'] = '~DefaultCO';
				$tmp['TfClasses'] = 'C%';
				$tmp['TfId'] = 2;
				$tmp['TfT1'] = 4;
				$tmp['TfW1'] = 60;
				$tmp['TfT2'] = 4;
				$tmp['TfW2'] = 60;
				$tmp['TfT3'] = 4;
				$tmp['TfW3'] = 40;
				$tmp['TfT4'] = 4;
				$tmp['TfW4'] = 40;
				$Gara['TargetFaces'][] = $tmp; // Compounds
				UpdateDivsClass($Gara, '2', array('C','CO','C1')); // update Compounds
				break;
			case 7: //	Type_Indoor 25
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = 1;
				$tmp['TfW1'] = 60;
				$tmp['TfT2'] = 1;
				$tmp['TfW2'] = 60;
				$Gara['TargetFaces'][] = $tmp; // Catch All

				$tmp['TfName'] = 'Default COG';
				$tmp['TfClasses'] = 'COG_';
				$tmp['TfId'] = 4;
				$tmp['TfT1'] = 4;
				$tmp['TfW1'] = 80;
				$tmp['TfT2'] = 4;
				$tmp['TfW2'] = 80;
				$Gara['TargetFaces'][] = $tmp; // CoGio
				UpdateDivsClass($Gara, '4', array('C','CO','C1'), array('GM','GF')); // update GioCO

				$tmp['TfName'] = 'Default G';
				$tmp['TfClasses'] = '__G_';
				$tmp['TfId'] = 3;
				$tmp['TfT1'] = 1;
				$tmp['TfW1'] = 80;
				$tmp['TfT2'] = 1;
				$tmp['TfW2'] = 80;
				$Gara['TargetFaces'][] = $tmp; // Giovanissimi
				UpdateDivsClass($Gara, '3', array(), array('GM','GF')); // update other Gio

				$tmp['TfName'] = '~DefaultVI';
				$tmp['TfClasses'] = 'VI%';
				$tmp['TfId'] = 5;
				$Gara['TargetFaces'][] = $tmp; // VI
				UpdateDivsClass($Gara, '5', array('VI')); // update VI

				$tmp['TfName'] = '~DefaultCO';
				$tmp['TfClasses'] = 'C%';
				$tmp['TfId'] = 2;
				$tmp['TfT1'] = 4;
				$tmp['TfW1'] = 60;
				$tmp['TfT2'] = 4;
				$tmp['TfW2'] = 60;
				$Gara['TargetFaces'][] = $tmp; // Compounds
				UpdateDivsClass($Gara, '2', array('C','CO','C1')); // update Compounds

				break;
			case 6: //	Type_Indoor 18
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = 1;
				$tmp['TfW1'] = 40;
				$tmp['TfT2'] = 1;
				$tmp['TfW2'] = 40;
				$Gara['TargetFaces'][] = $tmp; // Catch All

				$tmp['TfName'] = 'Default COG';
				$tmp['TfClasses'] = 'COG_';
				$tmp['TfId'] = 4;
				$tmp['TfT1'] = 4;
				$tmp['TfW1'] = 60;
				$tmp['TfT2'] = 4;
				$tmp['TfW2'] = 60;
				$Gara['TargetFaces'][] = $tmp; // CoGio
				UpdateDivsClass($Gara, '4', array('C','CO','C1'), array('GM','GF')); // update GioCO

				$tmp['TfName'] = 'Default G';
				$tmp['TfClasses'] = '__G_';
				$tmp['TfId'] = 3;
				$tmp['TfT1'] = 1;
				$tmp['TfW1'] = 60;
				$tmp['TfT2'] = 1;
				$tmp['TfW2'] = 60;
				$Gara['TargetFaces'][] = $tmp; // Giovanissimi
				UpdateDivsClass($Gara, '3', array(), array('GM','GF')); // update other Gio

				$tmp['TfName'] = '~DefaultVI';
				$tmp['TfClasses'] = 'VI%';
				$tmp['TfId'] = 5;
				$Gara['TargetFaces'][] = $tmp; // VI
				UpdateDivsClass($Gara, '5', array('VI')); // update VI

				$tmp['TfName'] = '~DefaultCO';
				$tmp['TfClasses'] = 'C%';
				$tmp['TfId'] = 2;
				$tmp['TfT1'] = 4;
				$tmp['TfW1'] = 40;
				$tmp['TfT2'] = 4;
				$tmp['TfW2'] = 40;
				$Gara['TargetFaces'][] = $tmp; // Compounds
				UpdateDivsClass($Gara, '2', array('C','CO','C1')); // update Compounds

				break;
			case 10: //	Type_HF 24+24
			case 12: //	Type_HF 12+12
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = 6;
				$tmp['TfT2'] = 6;
				$Gara['TargetFaces'][] = $tmp; // Catch All
				break;
			case 9: //	Type_HF 12+12
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = 6;
				$Gara['TargetFaces'][] = $tmp; // Catch All
				break;
			case 13: //	3D
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = 8;
				$tmp['TfT2'] = 8;
				$Gara['TargetFaces'][] = $tmp; // Catch All
				break;
			case 11: //	3D
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = 8;
				$Gara['TargetFaces'][] = $tmp; // Catch All
				break;
			case 14: //	Type_Las Vegas: EVERYBODY shoots on large 10 ring, CO shoot on small target
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = 1;
				$tmp['TfT2'] = 1;
				$tmp['TfT3'] = 1;
				$tmp['TfT4'] = 1;
				$tmp['TfW1'] = 40;
				$tmp['TfW2'] = 40;
				$tmp['TfW3'] = 40;
				$tmp['TfW4'] = 40;
				$Gara['TargetFaces'][] = $tmp; // Catch All

				$tmp['TfName'] = '~DefaultCO';
				$tmp['TfClasses'] = 'C%';
				$tmp['TfId'] = 2;
				$tmp['TfT1'] = 2;
				$tmp['TfT2'] = 2;
				$tmp['TfT3'] = 2;
				$tmp['TfT4'] = 2;
				$Gara['TargetFaces'][] = $tmp; // Compound
				UpdateDivsClass($Gara, '2', array('C','CO')); // update Compounds

				break;
			case 15: //	Type_GiochiGioventu
			case 16: //	Type_GiochiGioventuWinter
				$tmp['TfId'] = 1;
				$tmp['TfT1'] = 5;
				$tmp['TfT2'] = 5;
				$tmp['TfW1'] = 80;
				$tmp['TfW2'] = 80;
				$Gara['TargetFaces'][] = $tmp; // Catch All
				break;
		}
		UpdateDivsClass($Gara); // catchall
	}

	if($Gara['Tournament']['ToDbVersion']<'2010-10-26 11:30:00') {
	// tolgo i campi ToAth4Target[i] e ToTar4Session[i] 1<=i<=9
		for ($i=1;$i<=9;++$i)
		{
			unset($Gara['Tournament']['ToTar4Session' . $i]);
			unset($Gara['Tournament']['ToAth4Target' . $i]);
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2010-11-08 11:48:00') {
	/*
	 * Devo aggiornare la gestione delle eliminatorie.
	 *
	 * Prima tiro fuori l'elenco degli eventi con le eliminatorie (in teoria tutti ma non si sa mai!)
	 */
		$events=array();
		foreach ($Gara['Events'] as $e)
		{
			if ($e['EvElim2']!=0)
				$events[$e['EvCode']]=$e;
		}

	// tiro fuori per ogni Qualifications l'id e la rank
		$qualifications=array();
		foreach ($Gara['Qualifications'] as $q)
		{
			$qualifications[$q['QuId']]=$q['QuRank'];
		}

	/*
	 *  per ogni Eliminations di fase 0 tiro fuori id_fase e ElRank
	 */
		$eliminations=array();
		foreach ($Gara['Eliminations'] as $g)
		{
			if ($g['ElElimPhase']==0)
				$eliminations[$g['ElId'].'_'.$g['ElElimPhase']]=$g['ElRank'];
		}

	// Aggiungo il riferimento al torneo alle righe di Eliminations
		foreach ($Gara['Eliminations'] as &$g)
		{
			if (array_key_exists($g['ElEventCode'],$events))
			{
				$g['ElTournament']=$Gara['Tournament']['ToId'];
			}
		}
		/*print '<pre>';
		print_r($events);
		print_r($qualifications);
		print_r($eliminations);
		print_r($Gara['Eliminations']);
		print '</pre>';		exit;*/
	/*
	 *  Adesso metto a posto la rank.
	 *  Prima le righe di Eliminations con l'evento che ha EvElim1=0
	 */
		foreach ($Gara['Eliminations'] as &$g)
		{
			if ($events[$g['ElEventCode']]['EvElim1']==0)
			{
				$g['ElQualRank']=$qualifications[$g['ElId']];
			}
		}

	/*
	 * Adesso le righe con EvElim!=0
	 */
		foreach ($Gara['Eliminations'] as &$g)
		{
		// due fasi
			if ($events[$g['ElEventCode']]['EvElim1']!=0)
			{
			// qui piglio da $qualifications perchè sto mettendo a posto la prima fase
				if ($g['ElElimPhase']==0)
				{
					$g['ElQualRank']=$qualifications[$g['ElId']];
				}
			// qui piglio da $eliminations perchè sto mettendo a posto la seconda fase
				else
				{
					$g['ElQualRank']=$eliminations[$g['ElId'].'_0'];
				}
			}
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2010-12-03 17:40:00') {
		//Creo il vettore di lookup della definizione eventi
		$lookupEvents=array();
		foreach($Gara['EventClass'] as $ecKey=>$ecValue)
		{
			if($ecValue['EcTeamEvent']==0)
			if(array_key_exists($ecValue['EcClass'].$ecValue['EcDivision'],$lookupEvents))
				$lookupEvents[$ecValue['EcClass'].$ecValue['EcDivision']][] = $ecValue['EcCode'];
			else
				$lookupEvents[$ecValue['EcClass'].$ecValue['EcDivision']] = array($ecValue['EcCode']);
		}
		foreach($Gara['Qualifications'] as $qKey=>$qValue)
		{
			if($Gara['Entries'][$qValue['QuId']]['EnAthlete']==1)
			{
				if(array_key_exists($Gara['Entries'][$qValue['QuId']]['EnClass'] . $Gara['Entries'][$qValue['QuId']]['EnDivision'],$lookupEvents))
				{
					for($i=0;$i<count($lookupEvents[$Gara['Entries'][$qValue['QuId']]['EnClass'] . $Gara['Entries'][$qValue['QuId']]['EnDivision']]);$i++)
					{
						if(!array_key_exists('Individuals',$Gara))
							$Gara['Individuals'] = array();
							$Gara['Individuals'][] = array(
							'IndId'=>$qValue['QuId'],
							'IndEvent'=>$lookupEvents[$Gara['Entries'][$qValue['QuId']]['EnClass'] . $Gara['Entries'][$qValue['QuId']]['EnDivision']][$i],
							'IndTournament'=>$Gara['Tournament']['ToId'],
							'IndRank'=>$qValue['QuRank'],
							'IndTieBreak'=>$qValue['QuTieBreak']
						);
					}
				}
			}
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2011-01-17 00:32:00') {
		// removes the 3 fields...
		unset($Gara['Tournament']['ToUpNamesUrl']);
		unset($Gara['Tournament']['ToUpPhotosUrl']);
		unset($Gara['Tournament']['ToUpFlagsUrl']);

		// updates the IocCode of the tournament
		if($Gara['Tournament']['ToLocRule']=='IT') {
			$Gara['Tournament']['ToIocCode']='ITA';

			foreach($Gara['Entries'] as $key => $val) {
				$Gara['Entries'][$key]['EnIocCode']='ITA';
			}
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2011-01-30 12:56:00') {
		// Aggiorna le bandiere!!!
		if(!empty($Gara['Flags'])) {
			foreach($Gara['Flags'] as $key=>$Flag) {
				if(!empty($Flag['FlPNG'])) {
					$tmpnam=tempnam('/tmp', 'img');
					$img=imagecreatefromstring(base64_decode($Flag['FlPNG']));
					imagejpeg($img, $tmpnam, 95);
					$Gara['Flags'][$key]['FlJPG'] = base64_encode(file_get_contents($tmpnam));
					unset($Gara['Flags'][$key]['FlPNG']);
				}
			}
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2011-02-09 21:02:00') {
		foreach($Gara['Qualifications'] as $key=>$val) {
			$t=substr($val['QuTargetNo'], 0, 1)
				. str_pad(substr($val['QuTargetNo'],1,-1), 3, '0', STR_PAD_LEFT)
				. substr($val['QuTargetNo'],-1);
			$Gara['Qualifications'][$key]['QuTargetNo']=$t;
		}
		foreach($Gara['AvailableTarget'] as $key=>$val) {
			$t=substr($val['AtTargetNo'], 0, 1)
				. str_pad(substr($val['AtTargetNo'],1,-1), 3, '0', STR_PAD_LEFT)
				. substr($val['AtTargetNo'],-1);
			$Gara['AvailableTarget'][$key]['AtTargetNo']=$t;
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2011-02-10 11:07:00') {
		foreach($Gara['Teams'] as $key=>$val) {
			unset($Gara['Teams'][$key]["TeFinalRank"]);
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2011-04-25 09:00:00') {
		if(array_key_exists('BroadCast',$Gara))
			unset($Gara['BroadCast']);
	}

	if($Gara['Tournament']['ToDbVersion']<'2011-08-17 18:13:00') {
		require_once('Common/Fun_FormatText.inc.php');

		// updates Countries
		foreach($Gara['Countries'] as $key => &$val) {
			$val['CoCode'] = mb_convert_case($val['CoCode'], MB_CASE_UPPER, "UTF-8");
			$val['CoName']=AdjustCaseTitle($val['CoName']);
			$val['CoNameComplete']=AdjustCaseTitle($val['CoNameComplete']);
		}

		// updates Entries
		foreach($Gara['Entries'] as $key => &$val) {
			$val['EnName']=AdjustCaseTitle($val['EnName']);
			$val['EnFirstName']=AdjustCaseTitle($val['EnFirstName']);
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2012-01-15 16:00:00') {
		foreach($Gara['TVParams'] as $key => &$val) {
			$val['TVPColumns']='ALL';
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2012-01-18 12:00:02') {
		if($Gara['Tournament']['ToBlock']==63) $Gara['Tournament']['ToBlock'] = 65535;
	}

	if($Gara['Tournament']['ToDbVersion']<'2014-01-31 11:35:00') {
		foreach($Gara['Qualifications'] as $key=>$val) {
			unset($Gara['Qualifications'][$key]["QuRank"]);
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2014-02-04 17:00:00') {
		foreach($Gara['Qualifications'] as $key=>$val) {
			unset($Gara['Qualifications'][$key]["QuClRankOld"]);
			unset($Gara['Qualifications'][$key]["QuSubClassRankOld"]);
			unset($Gara['Qualifications'][$key]["QuHitsCalcOld"]);
			unset($Gara['Qualifications'][$key]["QuHitsSubClassCalcOld"]);
		}

		foreach($Gara['Individuals'] as $key=>$val) {
			unset($Gara['Individuals'][$key]["IndRankOld"]);
			unset($Gara['Individuals'][$key]["IndRankCalcOld"]);
		}

		foreach($Gara['Teams'] as $key=>$val) {
			unset($Gara['Teams'][$key]["TeRankOld"]);
			unset($Gara['Teams'][$key]["TeRankCalcOld"]);
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2014-03-07 11:50:00') {
		if(!empty($Gara['DistanceInformation'])) {
			foreach($Gara['DistanceInformation'] as $key => $row) {
				if(isset($row['DiStart'])) {
					$Gara['DistanceInformation'][$key]['DiStartDay']=substr($row['DiStart'], 0, 10);
					$Gara['DistanceInformation'][$key]['DiStartTime']=substr($row['DiStart'], -8);
					$Gara['DistanceInformation'][$key]['DiEndDay']=substr($row['DiEnd'], 0, 10);
					$Gara['DistanceInformation'][$key]['DiEndTime']=substr($row['DiEnd'], -8);
					$Gara['DistanceInformation'][$key]['DiWarmDay']=substr($row['DiWarmup'], 0, 10);
					$Gara['DistanceInformation'][$key]['DiWarmTime']=substr($row['DiWarmup'], -8);
					unset($Gara['DistanceInformation'][$key]['DiStart']);
					unset($Gara['DistanceInformation'][$key]['DiEnd']);
					unset($Gara['DistanceInformation'][$key]['DiWarmup']);
				}
			}
		}
	}

	if($Gara['Tournament']['ToDbVersion']<'2014-05-18 19:00:01') {
		if(!empty($Gara['DistanceInformation'])) {
			foreach($Gara['DistanceInformation'] as $key => $row) {
				$Gara['DistanceInformation'][$key]['DiDay']=$row['DiStartDay'];
				$Gara['DistanceInformation'][$key]['DiWarmStart']=$row['DiWarmTime'];
				$Gara['DistanceInformation'][$key]['DiStart']=$row['DiStartTime'];
				$tmp=explode(':', $row['DiStartTime']);
				$timeStart=($tmp[0]*60)+$tmp[1];
				$tmp=explode(':', $row['DiWarmTime']);
				$timeWarm=($tmp[0]*60)+$tmp[1];
				$tmp=explode(':', $row['DiEndTime']);
				$timeEnd=($tmp[0]*60)+$tmp[1];
				$Gara['DistanceInformation'][$key]['DiWarmDuration']=$timeStart-$timeWarm;
				$Gara['DistanceInformation'][$key]['DiDuration']=$timeEnd-$timeStart;

				unset($Gara['DistanceInformation'][$key]['DiStartDay']);
				unset($Gara['DistanceInformation'][$key]['DiStartTime']);
				unset($Gara['DistanceInformation'][$key]['DiEndDay']);
				unset($Gara['DistanceInformation'][$key]['DiEndTime']);
				unset($Gara['DistanceInformation'][$key]['DiWarmDay']);
				unset($Gara['DistanceInformation'][$key]['DiWarmTime']);
			}
		}
	}

/*

	if($Gara['Tournament']['ToDbVersion']<'YYYY-MM-DD HH:MM:SS') {
		// procedura di aggiornamento della gara alla versione attuale del database
	}

*/
	return $Gara;
}

function UpdateDivsClass(&$Gara, $TfId='1', $Divs=array(), $Class=array()) {
	foreach($Gara['Entries'] as $key => $Entry) {
		if($Divs and !in_array($Entry['EnDivision'], $Divs)) continue;
		if($Class and !in_array($Entry['EnClass'], $Class)) continue;

		if(empty($Gara['Entries'][$key]['EnTargetFace'])) $Gara['Entries'][$key]['EnTargetFace'] = $TfId;
	}
}
?>