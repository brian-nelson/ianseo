<?php
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_DateTime.inc.php');

class Obj_Scheduler
{
	protected $tourId=null;

	protected $data=null;

/**
 * $opts
 * Array delle opzioni.
 * La forma è chiave/valore.
 *
 * Le opzioni sono:
 * sessionType:
 * 		* = tutte altrimenti il tipo (vedi la funzione GetSessions()).
 * 		Se non presente vale *
 *
 * qual: true per mettere le righe di Qualifications e false altrimenti. Se non presente vale true
 *
 * qualOrphans: true per mettere gli orfani delle qualifiche. Se non presente vale true
 *
 * elim: true per mettere le righe di Eliminations con sessione impostata e false altrimenti. Se non presente vale true
 *
 * elimOrphans: true per mettere gli orfani delle eliminatorie. Se non presente vale true
 *
 * final: true per mettere le righe di FinSchedule sotto ai gruppi F e false altrimenti. Se non presente vale true
 *
 * finalOrphans: true per mettere gli orfani del FinSchedule (le righe che non entrano sotto nessun gruppo F)
 * 		e false altrimenti.
 * 		Se non presente vale true
 *
 * @var mixed[]
 * @access protected
 */
	protected $opts=array();

	protected function sortDayItems($day)
	{
	/*
	 * Ordino sulla colonna dateStart un array multidimensionale con multisort.
	 * Bisognerebbe verificare la posizione in cui infilarlo ma visto che poi occorrerebbe tagliare, infilare e ricucire un vettore,
	 * faccio prima così
	 */
		$dates=array();
		foreach ($this->data['days'][$day]['items'] as $k=>$row)
		{
			$dates[$k]=$row['dateStart'];
		}
		array_multisort($dates, SORT_ASC,  $this->data['days'][$day]['items']);
	}

/**
 * base()
 * Inizializza Obj_Scheduler::data con le info di base
 *
 * @return void
 */
	protected function base()
	{
		$queries=array();

	/*
	 * *****************************************************************************
	 * Le sessioni Z e F (+ l'eventuale filtro)
	 * *****************************************************************************
	 */

		$filter="SchTournament={$this->tourId} AND SchSesType NOT IN ('Q','E') ";
		if ($this->opts['sessionType']!='*')
		{
			$filter.= " AND SchSesType='" . $this->opts['sessionType'] . "' ";
		}

		$queries[]="
			(
				SELECT
					SchOrder AS `schOrder`,SesOrder AS `sessionOrder`,SchDateStart AS `dateStart`, SchDateEnd AS `dateEnd`,
					SchSesType AS `sessionType`,
					IF(SchDescr<>'',SchDescr,IF(SesName<>'',SesName,'')) AS `descr`,
					IFNULL(SesFollow,0) AS `follow` 
				FROM
					Scheduler
					LEFT JOIN
						Session
					ON SchSesType=SesType AND SchSesOrder=SesOrder AND SchTournament=SesTournament
				WHERE
					{$filter} 
				ORDER BY
					SchDateStart ASC,SchOrder ASC
			)	
		";

	/*
	 * *****************************************************************************
	 * Le sessioni Q se richieste
	 * *****************************************************************************
	 */
		if (($this->opts['sessionType']=='*' || $this->opts['sessionType']=='Q') && $this->opts['qual'])
		{
			$qual=get_text('QualRound');

			$filter="SchTournament={$this->tourId} AND QuSession<>0 ";

			$queries[]="
				(
					SELECT
						SchOrder AS `schOrder`,
						QuSession AS `sessionOrder`,
						SchDateStart AS `dateStart`,
						IF(SchDateEnd<>'0000-00-00 00:00:00',SchDateEnd,CONCAT(DATE_FORMAT(SchDateStart,'%Y-%m-%d'),' 23:59:59')) AS `dateEnd`,
						SchSesType AS `sessionType`,
						/*SchDescr AS descr,*/
						CONCAT('{$qual}',' ',group_concat(DISTINCT CONCAT(EnDivision,EnClass) ORDER BY CONCAT(EnDivision,EnClass) SEPARATOR ' - ')) AS `descr`,
						0 AS `follow` 
					FROM
						Scheduler	
						INNER JOIN
							Qualifications
						ON SchSesType='Q' AND SchSesOrder=QuSession AND QuSession<>0
						INNER JOIN
							Entries
						ON QuId=EnId AND EnTournament=SchTournament
					WHERE
						{$filter}
					GROUP BY
						SchOrder,sessionOrder,sessionType,DATE_FORMAT(SchDateStart,'%Y-%-m-%d')
					ORDER BY
						SchDateStart ASC,SchOrder ASC
				)
			";
		}

	/*
	 * *****************************************************************************
	 * Le sessioni E se richieste
	 * *****************************************************************************
	 */
		if (($this->opts['sessionType']=='*' || $this->opts['sessionType']=='E') && $this->opts['elim'])
		{
			$elim=get_text('Elimination');

			$case="(CASE ElElimPhase ";

				$case.="WHEN 0 THEN '" . get_text('Eliminations_1'). "' ";
				$case.="WHEN 1 THEN '" . get_text('Eliminations_2'). "' ";

			$case.="END)";

			$filter="SchTournament={$this->tourId} AND ElSession<>0 ";

			$queries[]="
				(
					SELECT
						SchOrder AS `schOrder`,
						ElSession AS `sessionOrder`, 
						SchDateStart AS `dateStart`,
						IF(SchDateEnd<>'0000-00-00 00:00:00',SchDateEnd,CONCAT(DATE_FORMAT(SchDateStart,'%Y-%m-%d'),' 23:59:59')) AS `dateEnd`,
						SchSesType AS `sessionType`,
						CONCAT('{$elim}',' ',group_concat(DISTINCT CONCAT(EvCode, ' (',{$case},')') ORDER BY CONCAT(EvCode, ' (',{$case},')') SEPARATOR ' - ')) AS `descr`, 
						0 AS `follow`
					FROM
						Scheduler	
						INNER JOIN
							Eliminations 
						ON SchSesType='E' AND SchSesOrder=ElSession AND ElSession<>0 AND SchTournament=ElTournament
						INNER JOIN 
							Events 
						ON ElTournament=EvTournament AND ElEventCode=EvCode AND EvTeamEvent=0 
					WHERE
						{$filter}
					GROUP BY
						SchOrder,sessionOrder,sessionType,DATE_FORMAT(SchDateStart,'%Y-%-m-%d')
					ORDER BY
						SchDateStart ASC,SchOrder ASC
				)
			";
		}

		$q="";
		if (count($queries)>0)
		{
			$q=implode(' UNION ALL ',$queries) . " ORDER BY dateStart ASC,schOrder ASC ";
		}
		else
		{
		// per avere un recordset vuoto!
			$q="SELECT * FROM `Scheduler` WHERE 1=0";
		}
		//print $q;exit;
		$r=safe_r_sql($q);

		if ($r)
		{
			$this->data['meta']=array(
				'title'=>get_text('Scheduler')
			);

			$this->data['days']=array();

			while ($row=safe_fetch_assoc($r))
			{
				$row['timeLabel']=dateRenderer($row['dateStart'],'H:i');
				if ($row['dateEnd']!='0000-00-00 00:00:00')
				{
					$row['timeLabel'].=' - ' . dateRenderer($row['dateEnd'],'H:i');
				}

				$day=substr($row['dateStart'],0,10);

			// se nella struttura non c'è il giorno lo aggiungo
				if (!array_key_exists($day,$this->data['days']))
				{
					$this->data['days'][$day]=array(
						'dateLabel'=>dateRenderer($day,get_text('DateFmt')),
						'items'=>array()
					);
				}

				$row['children']=array();


			// ... e metto la riga nel giorno
				$this->data['days'][$day]['items'][]=$row;
			}
		}

//		print '<pre>';
//		print_r($this->data);
//		print '</pre>';exit;
	}

/**
 * qual()
 * Popola se richieste, le righe che riguardano le qualifiche.
 * Qui non ragioniamo con le date ma con QuSession che se impostato aggancia nel gruppo Q corretto.
 *
 * @return void
 */
	public function qual()
	{
	/*
	 * Se sono state richieste le righe di Qualifications e il filtro sul tipo di righe
	 * è Q oppure * allora parto altrimenti termino
	 */
		if (!(($this->opts['sessionType']=='*' || $this->opts['sessionType']=='Q') && $this->opts['qual']))
		{
			return;
		}

	/*
	 * Tiro fuori dallo scheduler le righe Q.
	 * Qui le righe dello scheduler sono i figli della query base
	 */

		$q="
			SELECT
				SchOrder AS `schOrder`,
				SchSesOrder AS `sessionOrder`,
				SchDateStart AS `dateStart`,
				/*IF(SchDateEnd<>'0000-00-00 00:00:00',SchDateEnd,CONCAT(DATE_FORMAT(SchDateStart,'%Y-%m-%d'),' 23:59:59')) AS `dateEnd`,*/
				SchDateEnd AS `dateEnd`,
				'Q' AS `sessionType`,
				IF(SchDescr<>'',SchDescr,IFNULL(SesName,'')) AS descr
			FROM
				Scheduler
				LEFT JOIN
					Session
				ON SchTournament=SesTournament AND SchSesOrder=SesOrder AND SchSesType=SesType
			WHERE
				SchTournament={$this->tourId} AND  SchSesType='Q'
			ORDER BY
				SchDateStart ASC,SchOrder ASC		
		";
		//print $q;exit;
		$r=safe_r_sql($q);

		if ($r)
		{
			while ($row=safe_fetch_assoc($r))
			{
				$found=false;

				$row['timeLabel']=dateRenderer($row['dateStart'],'H:i') . (dateRenderer($row['dateEnd'],'H:i')!='' ? ' - ' . dateRenderer($row['dateEnd'],'H:i') : '');

			// cerco dove infilare la riga (OCIO ALLE DUE &!!!!)
				foreach ($this->data['days'] as &$day)
				{
					foreach ($day['items'] as &$v)
					{
						if ($v['sessionType']=='Q')
						{
							if ($row['sessionOrder']==$v['sessionOrder'] && substr($row['dateStart'],0,10)==substr($v['dateStart'],0,10))
							{
								$found=true;
							// ... e metto la riga
								$v['children'][]=$row;
							}
						}
					}
				}

				$d=substr($row['dateStart'],0,10);
				if (!$found && $this->opts['qualOrphans'])	// orfano
				{
					if (!array_key_exists($d,$this->data['days']))
					{
						$this->data['days'][$d]=array(
							'dateLabel'=>dateRenderer($d,get_text('DateFmt')),
							'items'=>array()
						);
					}

					$this->data['days'][$d]['items'][]=$row;
				}

				$this->sortDayItems($d);
			}
		}
	}

/**
 * elim()
 * Popola se richieste, le righe che riguardano le eliminatorie.
 * Qui non ragioniamo con le date ma con ElSession che se impostato aggancia nel gruppo E corretto.
 *
 * @return void
 */
	public function elim()
	{
	/*
	 * Se sono state richieste le righe di Eliminations e il filtro sul tipo di righe
	 * è E oppure * allora parto altrimenti termino
	 */
		if (!(($this->opts['sessionType']=='*' || $this->opts['sessionType']=='E') && $this->opts['elim']))
		{
			return;
		}

	/*
	 * Tiro fuori dallo scheduler le righe Q.
	 * Qui le righe dello scheduler sono i figli della query base
	 * La procedura è in pratica la stessa di qual()
	 */

		$q="
			SELECT
				SchOrder AS `schOrder`,
				SchSesOrder AS `sessionOrder`,
				SchDateStart AS `dateStart`,
				/*IF(SchDateEnd<>'0000-00-00 00:00:00',SchDateEnd,CONCAT(DATE_FORMAT(SchDateStart,'%Y-%m-%d'),' 23:59:59')) AS `dateEnd`,*/
				SchDateEnd AS `dateEnd`,
				'E' AS `sessionType`,
				IF(SchDescr<>'',SchDescr,IFNULL(SesName,'')) AS descr
			FROM
				Scheduler
				LEFT JOIN
					Session
				ON SchTournament=SesTournament AND SchSesOrder=SesOrder AND SchSesType=SesType
			WHERE
				SchTournament={$this->tourId} AND  SchSesType='E'
			ORDER BY
				SchDateStart ASC,SchOrder ASC		
		";

		$r=safe_r_sql($q);

		if ($r)
		{
			while ($row=safe_fetch_assoc($r))
			{
				$found=false;
				$row['timeLabel']=dateRenderer($row['dateStart'],'H:i') . (dateRenderer($row['dateEnd'],'H:i')!='' ? ' - ' . dateRenderer($row['dateEnd'],'H:i') : '');

			// cerco dove infilare la riga (OCIO ALLE DUE &!!!!)
				foreach ($this->data['days'] as &$day)
				{
					foreach ($day['items'] as &$v)
					{
						if ($v['sessionType']=='E')
						{
							if ($row['sessionOrder']==$v['sessionOrder'] && substr($row['dateStart'],0,10)==substr($v['dateStart'],0,10))
							{
								$found=true;
							// ... e metto la riga
								$v['children'][]=$row;
							}
						}
					}
				}

				$d=substr($row['dateStart'],0,10);
				if (!$found && $this->opts['elimOrphans'])	// orfano
				{
					if (!array_key_exists($d,$this->data['days']))
					{
						$this->data['days'][$d]=array(
							'dateLabel'=>dateRenderer($d,get_text('DateFmt')),
							'items'=>array()
						);
					}

					$this->data['days'][$d]['items'][]=$row;
				}

				$this->sortDayItems($d);
			}
		}
	}

/**
 * trainingAndFinal()
 * Popola le righe che riguardano il warmup e le finali (se richieste)
 *
 * @return void
 */
	protected function trainingAndFinal()
	{
		$queries=array();

	/*
	 * Se sono state richieste le righe di FinSchedule e il filtro sul tipo di righe
	 * è F oppure * allora le metto
	 */
		if (($this->opts['sessionType']=='*' || $this->opts['sessionType']=='F') && $this->opts['final'])
		{
			$phases=getPhasesId(-1);

			$casePhase="(CASE GrPhase ";
				foreach ($phases as $p)
				{
					if (in_array($p,array(64,32)))
					{
						$txt1=get_text($p.'_Phase');
						$txt2='';
						if ($p==64)
							$txt2=get_text('48_Phase');
						elseif ($p==32)
							$txt2=get_text('24_Phase');

						if ($p==64)
						{
							$casePhase.=" WHEN {$p} THEN IF( EvFinalFirstPhase=64,'{$txt1}','{$txt2}' ) ";
						}
						elseif($p==32)
						{
							$casePhase.=" WHEN {$p} THEN IF( EvFinalFirstPhase=32,'{$txt1}','{$txt2}' ) ";
						}
					}
					else
					{
						$txt=get_text($p.'_Phase');
						$casePhase.=" WHEN {$p} THEN '{$txt}' ";
					}
				}
			$casePhase.="END) ";
			//print '<br>'.$casePhase.'<br><br>';
			$caseTeam="(CASE FSTeamEvent ";
				$caseTeam.="WHEN 0 THEN '" . get_text('Individual') ."' ";
				$caseTeam.="WHEN 1 THEN '" . get_text('Team') ."' ";
			$caseTeam.="END) ";

			$final=get_text('MenuLM_Final Rounds');

			$queries[]="
				(
					SELECT DISTINCT
						1 AS `schOrder`,CONCAT(FSScheduledDate,' ',FSScheduledTime) AS `dateStart`, ADDDATE(CONCAT(FSScheduledDate,' ',FSScheduledTime), INTERVAL FSScheduledLen MINUTE) AS `dateEnd`,
						'F' AS `sessionType`,
						CONCAT('{$final}',' ',group_concat(DISTINCT CONCAT({$caseTeam},' ',FSEvent,' (',{$casePhase},')' ) separator ' - ')) AS `descr`
					FROM
						FinSchedule
						INNER JOIN Events ON EvCode=FSEvent AND EvTeamEvent=FSTeamEvent AND EvTournament=FSTournament
						inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2, EvTeamEvent))>0
						INNER JOIN Grids ON FSMatchNo = GrMatchNo AND GrPhase<=greatest(PhId, PhLevel)
					WHERE
						FSTournament={$this->tourId} AND FSScheduledDate!='0000-00-00'
					GROUP BY
						schOrder,dateStart/*,FSMatchNo, GrPhase*/ 
				)
				
				/*ORDER BY
					FSScheduledDate ASC, FSScheduledTime ASC*/
			";
		}

	// se ho query parto se no termino
		if (count($queries)>0)
		{
			$q=implode(' UNION ALL ',$queries) . " ORDER BY dateStart ASC ";
			//print $q;exit;
			$r=safe_r_sql($q);

			if ($r)
			{
				while ($row=safe_fetch_assoc($r))
				{
					$row['timeLabel']=dateRenderer($row['dateStart'],'H:i');
					if ($row['dateEnd']!='0000-00-00 00:00:00' && $row['dateStart']!=$row['dateEnd'])
						$row['timeLabel'].= ' - ' . dateRenderer($row['dateEnd'],'H:i');



				// cerco dove infilare la riga
					$day=substr($row['dateStart'],0,10);
					$found=false;
//					print '<pre>';
//					print_r($this->data);
//					print '</pre>';
					if (array_key_exists($day,$this->data['days']))
					{
						foreach ($this->data['days'][$day]['items'] as &$v)
						{
							if ($v['sessionType']=='F')
							{
								if ($row['dateStart']>=$v['dateStart'] && $row['dateStart']<$v['dateEnd'])
								{
									$found=true;

								/*
								 *  ... e metto la riga nel giorno
								 *  Se il padre ha il Follow=0
								 *  		- la timeLabel è quella preparata prima
								 *  Se il padre ha il Follow=1
								 *  		- se il figlio precedente è di tipo diverso oppure non ci sono precedenti,
								 *  			la timeLabel è quella preparata prima
								 * 			- se il figlio precedente è dello stesso tipo
								 * 				la timeLabel cambia
								 */
									if ($v['follow']==1)
									{
										if (count($v['children'])!=0 && $v['children'][count($v['children'])-1]['sessionType']==$row['sessionType'])
										{
											$row['timeLabel']=get_text('ToFollow','Tournament');
										}
									}
									$v['children'][]=$row;
									break;
								}
							}
						}
					}
					if (!$found && ($this->opts['finalOrphans'] && $row['sessionType']=='F'))	// orfano
					{
						//$day=substr($row['dateStart'],0,10);
						if (!array_key_exists($day,$this->data['days']))
						{
							$this->data['days'][$day]=array(
								'dateLabel'=>dateRenderer($day,get_text('DateFmt')),
								'items'=>array()
							);
						}

						$this->data['days'][$day]['items'][]=$row;
					}

					$this->sortDayItems($day);

				}
			}
		}
	}

	public function __construct($opts=array(),$tourId=null)
	{
		if ($tourId===null)
		{
			$this->tourId=$_SESSION['TourId'];
		}
		else
		{
			$this->tourId=$tourId;
		}

	// default
		if (!array_key_exists('sessionType',$opts))
		{
			$opts['sessionType']='*';
		}

		if (!array_key_exists('qual',$opts))
		{
			$opts['qual']=true;
		}

		if (!array_key_exists('qualOrphans',$opts))
		{
			$opts['qualOrphans']=true;
		}

		if (!array_key_exists('elim',$opts))
		{
			$opts['elim']=true;
		}

		if (!array_key_exists('elimOrphans',$opts))
		{
			$opts['elimOrphans']=true;
		}

		if (!array_key_exists('final',$opts))
		{
			$opts['final']=true;
		}

		if (!array_key_exists('finalOrphans',$opts))
		{
			$opts['finalOrphans']=true;
		}

	// imposto le opzioni
		$this->opts=$opts;

	// parto con il calcolo
		$this->process(false);
	}

/**
 * process()
 * Da qui parte la popolazione di Obj_Scheduler::data.
 *
 * @param true $return: se true ritorna $this altrimenti popola Obj_Scheduler::data e basta
 *
 * @return mixed: $this (per il chainable-methods)
 */
	public function process($return=true)
	{
		$this->base();
		$this->qual();
		$this->elim();
		$this->trainingAndFinal();
		//$this->finalEvents();

	/*
	 * Questo mi assicura che i giorni siano ordinati giusti!
	 * Valgono le osservazioni del posizionamento di un orfano delle finali/warmup
	 */
		ksort($this->data['days']);

		if ($return)
			return $this;
	}
/**
 * getData()
 * Ritorna la struttura dello scheduler.
 *
 * @return mixed[]: scheduler
 */
	public function getData()
	{
		return $this->data;
	}
}
