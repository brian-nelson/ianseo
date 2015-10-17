<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');

	CheckTourSession(true);

	if(!isset($_REQUEST["EventCode"]) || !preg_match("/^[A-Z0-9]{1,4}#[0,1,2]{1}$/i",$_REQUEST["EventCode"]))
	{
		header("Location: AbsIndividual1.php");
		exit();
	}

	$Error=false;

	$RequestedElim = 0;
	$RequestedEvent = '';
	$NumGironi=0;
	$ArrMaxRank=array('el1'=>0,'el2'=>0,'fin'=>0);

	if (!(isset($_REQUEST['EventCode']) && strlen($_REQUEST['EventCode'])>0))
		exit;

	$Event = explode('#',$_REQUEST['EventCode']);

	$RequestedEvent =  $Event[0];
	$RequestedElim = $Event[1];

// tiro fuori le info x calcolare il numero di gironi previsti per l'evento e la MaxRank in base a quello che sto spareggiando
	$q="SELECT IF(EvElim2<>0,IF(EvElim1=0,1,2),0) AS Gironi,EvElim1,EvElim2,EvFinalFirstPhase FROM Events WHERE EvTournament={$_SESSION['TourId']} AND EvTeamEvent=0 AND EvCode='{$RequestedEvent}'";
	$r=safe_r_sql($q);
	if ($r && safe_num_rows($r)==1)
	{
		$row=safe_fetch($r);
		$NumGironi=$row->Gironi;

		$ArrMaxRank['el1']=$row->EvElim1;
		$ArrMaxRank['el2']=$row->EvElim2;
		$ArrMaxRank['fin']=$row->EvFinalFirstPhase*2;
	// salva i primi 8
		if($row->EvFinalFirstPhase==24 or $row->EvFinalFirstPhase==48)
		{
			$ArrMaxRank['fin']+=8;
		}
	}
	else
		exit;
	// impossibile!
	if ($NumGironi==0 || ($NumGironi==1 && $RequestedElim==0))
		exit;

/*
 * Tabella per stabilire il tipo di spareggio che sto facendo
 *
 * #	$NumGironi	$RequestedElim		Tipo
 * (1)		0				*			Impossibile
 * (2)		1				0			Impossibile
 * (3)		1				1			Dagli assoluti all'ultimo girone (quello che dipende da EvElim2)
 * (4)		1				2			Dall'ultimo girone alle finali
 * (5)		2				0			Dagli assoluti al primo girone (quello che dipende da EvElim1)
 * (6)		2				1			Dal primo girone all'ultimo
 * (7)		2				2			Dall'ultimo girone alle finali
 *
 * In base a questi valori devo scegliere le classi corrette.
 *
 * --> (1),(2)
 * LA PAGINA SI FERMA!!!!!!
 *
 * --> (3)
 * 1) Con la classe Abs tiro fuori la rank
 * 2) Imposto le posizioni con la sua setRow per sistemare IndRank
 * 3) Calcolo con la classe FinalInd la RankFinal di chi si è fermato alle qualifiche (-3)
 *
 * --> (4)
 * 1) Con la classe ElimInd tiro fuori la rank del secondo girone (fase 1)
 * 2) Imposto le ElRank con la sua setRow
 * 3) Calcolo con la classe FinalInd la RankFinal di chi si è fermato al secondo girone (-2)
 *
 * --> (5)
 * 1) Con la classe Abs tiro fuori la rank
 * 2) Imposto le posizioni con la sua setRow per sistemare IndRank
 * 3) Calcolo con la classe FinalInd la RankFinal di chi si è fermato alle qualifiche (-3)
 *
 * --> (6)
 * 1) Con la classe ElimInd tiro fuori la rank del primo girone (fase 0)
 * 2) Imposto le ElRank con la sua setRow
 * 3) Calcolo con la classe FinalInd la RankFinal di chi si è fermato al primo girone (-1)
 *
 * --> (7)
 * 1) Con la classe ElimInd tiro fuori la rank del secondo girone (fase 1)
 * 2) Imposto le ElRank con la sua setRow
 * 3) Calcolo con la classe FinalInd la RankFinal di chi si è fermato al secondo girone (-2)
 */

	$MaxRank=0;  //(vedi ArrmaxRank in alto)

	$rank=null;
	$obj='';
	$opts=array();
	$optsFinal=array();		// lo userò quando occorrerà calcolare la RankFinal

	//print $NumGironi . $RequestedElim;exit;
	switch ($NumGironi . $RequestedElim)
	{
		case '11':				// (3) Assoluti -> Ultimo girone
			$MaxRank=$ArrMaxRank['el2'];

			$obj='Abs';

			$opts=array(
				'events'=>array($RequestedEvent),
				'dist'=>0
			);

			$optsFinal=array(
				'eventsC'=>array($RequestedEvent.'@-3')
			);
			break;

		case '12':				// (4) Ultimo girone -> Finali
			$MaxRank=$ArrMaxRank['fin'];

			$obj='ElimInd';

//			$opts=array(
//				'eventsC'=>array($RequestedEvent . '@2'),
//			);
			$opts=array(
				'eventsR'=>array($RequestedEvent . '@2'),
			);

			$optsFinal=array(
				'eventsC'=>array($RequestedEvent.'@-2')
			);
			break;

		case '20':				// (5) Assoluti -> Primo girone
			$MaxRank=$ArrMaxRank['el1'];

			$obj='Abs';

			$opts=array(
				'events'=>array($RequestedEvent),
				'dist'=>0
			);

			$optsFinal=array(
				'eventsC'=>array($RequestedEvent.'@-3')
			);
			break;

		case '21':				// (6) Primo girone -> Ultimo girone
			$MaxRank=$ArrMaxRank['el2'];

			$obj='ElimInd';

			$opts=array(
				'eventsR'=>array($RequestedEvent . '@1'),
			);

			$optsFinal=array(
				'eventsC'=>array($RequestedEvent . '@-1'),
			);
			break;

		case '22':				// (7) Ultimo girone -> Finali
			$MaxRank=$ArrMaxRank['fin'];

			$obj='ElimInd';

			$opts=array(
				'eventsR'=>array($RequestedEvent . '@2'),
			);

			$optsFinal=array(
				'eventsC'=>array($RequestedEvent.'@-2')
			);
			break;
	}
//print $obj . ' ' . $MaxRank;exit;
	$rank=Obj_RankFactory::create($obj,$opts);

	if (is_null($rank))
		exit;

	$IdAffected = array();
	$NotResolved=false;
	$NotResolvedMsg=array();

	if (isset($_REQUEST['Ok']) && $_REQUEST['Ok']=='OK' && !IsBlocked(BIT_BLOCK_ELIM))
	{
		$Ties=array();
		$EnIds=array();
		foreach ($_REQUEST as $Key => $Value)
		{

			if (strpos($Key,'R_')===0)
			{
				$EnIds[substr($Key,2)]=$Value;
			}
		}
//		print '<pre>';
//		print_r($EnIds);
//		print '</pre>';exit;

		asort($EnIds);

	// controlla che tutti gli spareggi siano stati fatti
		$TrueRank=1;
		foreach($EnIds as $EnId => $AssignedRank)
		{
//print $EnId . ' - ' . $AssignedRank . ' - ' . $TrueRank.'<br>';
			if($AssignedRank!=$TrueRank && $AssignedRank<=$MaxRank)
			{
				$NotResolved=true;
				break;
			}
			$TrueRank++;
		}

		if (!$NotResolved)
		{
			foreach($EnIds as $EnId => $AssignedRank)
			{

			/*
			 * Se provengo dagli assoluti il campo phase verrà ignorato.
			 * Se provengo dal primo girone il campo dist verrà ignorato.
			 * E' importante che nel secondo caso phase valga $RequestedElim-1 perchè
			 * se ho due gironi $RequestElim==1 vuol dire che sto per entrare nel II e quindi dovrò
			 * sistemare la rank della fase 0. Se Ho un girone ricado nell'abs quindi phase non verrà
			 * presa in considerazione.
			 */
				/*print '<pre>';
				print_r(array(
					array(
						'ath' => $EnId,
						'event' => $RequestedEvent,
						'dist'	=> 0,
						'phase' => $RequestedElim-1,
						'rank' => $AssignedRank
					)
				));
				print '</pre>';*/
				$x=$rank->setRow(array(
					array(
						'ath' => $EnId,
						'event' => $RequestedEvent,
						'dist'	=> 0,
						'phase' => $RequestedElim-1,
						'rank' => $AssignedRank
					)
				));

				if ($x==1)
				{
					$IdAffected[]= strsafe_db($EnId);
				}
			}
		}
		else
		{
			$NotResolvedMsg[]=$RequestedEvent;
		}

		foreach ($_REQUEST as $Key => $Value)
		{
			if (strpos($Key,'T_')===0)
			{
				//print $Key.'<br>';
				list(,$id,$index)=explode('_',$Key);

				if (!array_key_exists($RequestedEvent.'_'.$id,$Ties))
					$Ties[$RequestedEvent.'_'.$id]=str_pad('',3,' ');

				$v=GetLetterFromPrint($Value);

				$Ties[$RequestedEvent.'_'.$id]=substr_replace($Ties[$RequestedEvent.'_'.$id],$v,$index,1);
			}
		}
		//exit;

		if (count($Ties)>0)
		{
			/*print '<pre>';
			print_r($Ties);
			print '</pre>';Exit;*/
			foreach ($Ties as $Key=>$Value)
			{
				list($ev,$ath)=explode('_',$Key);

				$x=$rank->setRow(array(
					array(
						'ath' => $ath,
						'event' => $ev,
						'dist'	=> 0,
						'phase' => $RequestedElim-1,
						'tiebreak' => $Value
					)
				));
			}
		}

		if (count($IdAffected)>0)	//(se $NotResolved==true => count($IdAffected)==0)
		{

		// se non sto passando alle finali resetto i gironi successivi all'attuale
			if($RequestedElim<2)
			{
				for ($i=$RequestedElim;$i<=1;++$i)
					ResetElimRows($RequestedEvent,$i+1);
			}

		/* rifaccio le griglie */
			$Delete
				= "DELETE FROM Finals "
				. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent = " . StrSafe_DB($RequestedEvent);
			$Rs=safe_w_sql($Delete);
			// ricreo la griglia distrutta
			$Insert
				= "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) "
				. "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i')) . " "
				. "FROM Events INNER JOIN Grids ON GrPhase<=EvFinalFirstPhase AND EvTeamEvent='0' "
				. "AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "WHERE EvCode = " . StrSafe_DB($RequestedEvent);
			$RsIns=safe_w_sql($Insert);

		}

		if (!$NotResolved)
		{
		/*
		 *  Adesso metto a posto gli id delle Eliminations.
		 *  Per farlo rileggo la rank (la prima lettura avviene al carimento della pagina quando non si passa di qua)
		 *  e uso i dati per scrivere gli id nei posti giusti
		 */
			$rank->read();
			$data=$rank->getData();

			/*print '<pre>';
			print_r($data);
			print '</pre>';exit;*/

			$rr=-1;

		/*
		 * $data['sections] conterrà un solo elemento perchè per le eliminatorie non si possono spareggiare più eventi alla volta.
		 * Quindi per sapere il numero di persone qualificate piglio la prima sezione e guardo il qualifiedNo
		 */
			$section=current($data['sections']);

			/*print '<pre>';
			print_r($section);
			print '</pre>';exit;*/
			$qualifiedNo=$section['meta']['qualifiedNo'];

			foreach ($section['items'] as $item)
			{

				if ($item['rank']>$qualifiedNo)
					break;


				$q="";

				$date=date('Y-m-d H:i:s');

				if ($RequestedElim==0 || $RequestedElim==1)
				{
					$q="
						UPDATE
							Eliminations
						SET
							ElId={$item['id']},
							ElDateTime='{$date}'
						WHERE
							ElElimPhase={$RequestedElim} AND
							ElTournament={$_SESSION['TourId']} AND
							ElEventCode='{$RequestedEvent}' AND
							ElQualRank={$item['rank']}
					";
				}
				elseif ($RequestedElim==2)
				{
					$q="
						UPDATE
							Events
							INNER JOIN
								Finals
							ON EvCode=FinEvent AND EvTeamEvent=0 AND EvTournament=FinTournament
							INNER JOIN
								Grids
							ON FinMatchNo=GrMatchNo AND GrPhase=EvFinalFirstPhase AND FinTournament={$_SESSION['TourId']} AND FinEvent='{$RequestedEvent}'
						SET
							FinAthlete={$item['id']},
							FinDateTime='{$date}'
						WHERE
							FinEvent='{$RequestedEvent}' AND IF(EvFinalFirstPhase=48 || EvFinalFirstPhase=24,GrPosition2, GrPosition)={$item['rank']} AND FinTournament={$_SESSION['TourId']}

					";
				}
				//print $q.'<br><br>';
				$r=safe_w_sql($q);
			}

			//exit;

		// Adesso finalizzo chi si è fermato
			Obj_RankFactory::create('FinalInd',$optsFinal)->calculate();

		// setto a 1 i flags che dicono che ho fatto gli spareggi per gli eventi
			$Update
				= "UPDATE Events SET "
				. ($RequestedElim==2 ? 'EvShootOff' : ($RequestedElim==0 ? 'EvE1ShootOff' : 'EvE2ShootOff')) . "='1' "
				. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode='" . $RequestedEvent."'  AND EvTeamEvent='0' ";
			$RsUp=safe_w_sql($Update);
			//print $Update;exit;
			set_qual_session_flags();
		}
	}

	$JS_SCRIPT=array(
		'',
		);

	include('Common/Templates/head.php');

?>
<table class="Tabella">
<TR><TH class="Title"><?php print get_text('ShootOff4Elim');?></TH></TR>
<?php if (count($NotResolvedMsg)>0) { ?>
	<tr class="warning"><td><?php print get_text('NotAllShootoffResolved','Tournament',implode(', ',$NotResolvedMsg));?></td></tr>
<?php } ?>
</table>
<?php
	if (!$Error)
	{
		$rank->read();
		$data=$rank->getData();
		/*print '<pre>';
		print_r($data);
		print '</pre>';exit;*/
		$NumDist=0;
		if (array_key_exists('numDist',$data['meta']))
			$NumDist=$data['meta']['numDist'];

		$curEvent='';

		if(count($data['sections'])>0)
		{
			print '<form name="Frm" method="post" action="">' . "\n";
			if (isset($_REQUEST['EventCode']))
				print '<input type="hidden" name="EventCode" value="' . $_REQUEST['EventCode'] . '">' . "\n";

				foreach ($data['sections'] as $section)
				{
					$Colonne = 8 + $NumDist;
					$PercPunti = NumFormat(40/($NumDist+3));

					print '<table class="Tabella">' . "\n";
						print '<tr class="Divider"><td colspan="' . $Colonne . '"></td></tr>' . "\n";
						print '<tr><th class="Title" colspan="' . $Colonne . '">' . $section['meta']['descr']. ' (' . $section['meta']['event'] . ')</th></tr>';
						print '<tr>';
							print '<th width="5%">' . get_text('Rank') . '</th>';
							print '<th width="20%">' . get_text('Archer') . '</th>';
							print '<th width="20%" colspan="2">' . get_text('Country') . '</th>';
							for ($i=1;$i<=$NumDist;++$i)
								print '<th width="' . $PercPunti . '%">Score' . ($NumDist==0 ? '': ' ' . $i) . '</th>';
							print '<th width="' . $PercPunti . '%">' . get_text('Total') . '</th>';
							print '<th width="' . $PercPunti . '%">G</th>';
							print '<th width="' . $PercPunti .'%">X</th>';
							print '<th>' . get_text('TieArrows'). '</th>';
						print '</tr>' . "\n";

						foreach ($section['items'] as $item)
						{
						// fermo appena trovo una rank > di quelle che passano e una riga con so=0
							if ($item['rank']>$section['meta']['qualifiedNo'] && $item['so']==0)
								break;

							$style="";

							if ($item['so']==0)	// potrei avere un giallo
							{
								if ($item['ct']>1)		// ho un giallo
								{
									$style="warning";
								}
								else	// no pari
								{
									$style="";
								}
							}
							else	// rossi
							{
								$style="error";
							}

							print '<tr class="' . $style . '">';
								print '<th class="Title">';
									print $item['rank'] . '&nbsp;';

							/*
							 * Devo gestire la tendina.
							 * Partendo dalla rank so fino a dove devo arrivare perchè me lo
							 * dice il campo ct
							 */
									$endRank = $item['rankBeforeSO']+$item['ct']-1;

									print '<select name="R_' . $item['id'] . '">' . "\n";
										for ($i=$item['rankBeforeSO'];$i<=$endRank;++$i)
											print '<option value="' . $i . '"' . ($i==$item['rank'] ? ' selected' : '') . '>' . $i . '</option>' . "\n";
									print '</select>' . "\n";

								print '</th>';
								print '<td>' . $item['athlete'] . '</td>';
								print '<td width="5%" class="Center">' . $item['countryCode'] . '</td>';
								print '<td width="15%">' . ($item['countryName']!='' ? $item['countryName'] : '&nbsp') . '</td>';
								for ($i=1;$i<=$NumDist;++$i)
								{
									$tmp=explode('|',$item['dist_'.$i]);
									print '<td class="Center">' . $tmp[1] . '</td>';
								}
								print '<td class="Center">' . $item['score'] . '</td>';
								print '<td class="Center">' . $item['gold']  . '</td>';
								print '<td class="Center">' . $item['xnine']  . '</td>';
								print '<td>';
									for ($i=0;$i<3;++$i)
									{
										print '<input type="text" maxlength="2" size="1" name="T_' . $item['id']. '_' . $i . '" value="' . (strlen($item['tiebreak'])>$i ? DecodeFromLetter($item['tiebreak'][$i]) : ''). '">&nbsp;';
									}
								print '</td>';
							print '</tr>' . "\n";
						}

						print '<tr><td class="Center" colspan="' . $Colonne . '"><input type="hidden" name="Ok" value="OK"><input type="submit" value="' . get_text('CmdOk') . '"></td></tr>' . "\n";
					print '</table><br/>';
				}

			print '</form>';
		}
	}

	include('Common/Templates/tail.php');
?>