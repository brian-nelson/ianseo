<?php
/**
 * insertSession
 * Crea una nuova sessione.
 * @param int $SesTournament: torneo
 * @param int $SesOrder: ordine (numero sessione)
 * @param string $SesType: tipo
 * @param string $SesName: nome
 * @param int $SesTar4Session: numero di bersagli
 * @param int $SesAth4Target: numero di persone per bersaglio
 * @param int $SesFirstTarget: primo bersaglio della sessione
 * @param int $SesFollow: 0 no, 1 sì
 * @return mixed: true se ok; messaggio di errore altrimenti
 */
	function insertSession($SesTournament,$SesOrder,$SesType,$SesName,$SesTar4Session,$SesAth4Target,$SesFirstTarget,$SesFollow) {
		$ret=true;

		$q
			= "INSERT ignore INTO Session (SesTournament,SesOrder,SesType,SesName,SesTar4Session,SesAth4Target,SesFirstTarget,SesFollow) "
			. "VALUES( "
				. StrSafe_DB($SesTournament) . ", "
				. StrSafe_DB($SesOrder) . ", "
				. StrSafe_DB($SesType) . ", "
				. StrSafe_DB($SesName) . ", "
				. StrSafe_DB(($SesType=='Q' || $SesType=='E' ? $SesTar4Session : 0)) . ", "
				. StrSafe_DB(($SesType=='Q' || $SesType=='E' ? $SesAth4Target : 0)) . ", "
				. StrSafe_DB(($SesType=='Q' || $SesType=='E' ? $SesFirstTarget : 0)) . ", "
				. StrSafe_DB(($SesType=='F' ? $SesFollow : 0))
			. ") ";
			//	print $q;exit;
		$rs=safe_w_sql($q);

		if (!$rs)
		{
			$ret='_error_';
		}
		else
		{
		// IMPLICAZIONI
			switch($SesType)
			{
				case 'Q':
					// aggiorno il numero di sessioni di qualifica (Tournament.ToNumSession)
					$x=updateQualNumSession($SesTournament);

					if (!$x)
					{
						$ret='_error_';
					}
					else
					{
						// genero i paglioni per la sessione
						$x=regenerateQualTargetsForSession($SesTournament,$SesOrder);
						if (!$x)
						{
							$ret='_error_';
						}
					}

					// inserts a new DistanceInformation records set
					$SQL="select * from DistanceInformation where DiTournament=$SesTournament and DiType='Q' and DiSession=".($SesOrder-1);
					$q=safe_r_sql($SQL);
					if(safe_num_rows($q)) {
						while($r=safe_fetch($q)) {
							safe_w_sql("insert ignore into DistanceInformation set
								DiTournament=$r->DiTournament,
								DiSession=$SesOrder,
								DiDistance=$r->DiDistance,
								DiEnds=$r->DiEnds,
								DiArrows=$r->DiArrows,
								DiType='Q'");
						}
					} else {
						// inserts a standard guess based on the type
						$q=safe_r_sql("select ToType from Tournament where ToId=$SesTournament");
						$r=safe_fetch($q);
						foreach(getDistanceArrays($r->ToType) as $Dist=>$Info) {
							safe_w_sql("insert ignore into DistanceInformation set DiTournament=$SesTournament, DiSession=$SesOrder, DiDistance=$Dist+1, DiEnds={$Info[0]}, DiArrows={$Info[1]}, DiType='Q'");
						}
					}

					break;
				case 'E':
					break;
				case 'F':
					break;
				case 'T':
					break;
			}
		}

		return $ret;

	}

/**
 * updateSession().
 * Aggiorna una sessione
 * @param int $SesTournament: torneo (chiave)
 * @param int $SesOrder: ordine (numero sessione) (chiave)
 * @param int string $SesType: tipo (chiave)
 * @param string $SesName: nome
 * @param int $SesTar4Session: numero di bersagli
 * @param int $SesAth4Target: numero di persone per bersaglio
 * @param int $SesFirstTarget: primo bersaglio della sessione
 * @param int $SesFollow: 0 no, 1 sì
 * @param boll $forceRegenerateTargets: true per forzare la rigenerazione dei bersagli
 * @return mixed: true se ok; messaggio di errore altrimenti
 */
	function updateSession($SesTournament,$SesOrder,$SesType,$SesName,$SesTar4Session,$SesAth4Target,$SesFirstTarget,$SesFollow,$forceRegenerateTargets=false)
	{
		$ret=true;

		$oldSesTar4Session=0;
		$oldSesAth4Target=0;

	// vecchi parametri per la sessione Q (servono per l'eventuale rigenerazione dei bersagli)
		if ($SesType=='Q') {

			$x=getQualTargetsInfoForSession($SesTournament,$SesOrder);

			if ($x!==false) {
				list($oldSesTar4Session,$oldSesAth4Target)=$x;
			}
		}

		$q
			= "UPDATE "
				. "Session "
			. "SET "
				. "SesName=".StrSafe_DB($SesName). ","
				. "SesTar4Session=".StrSafe_DB(($SesType=='Q' || $SesType=='E' ? $SesTar4Session : 0)). ","
				. "SesAth4Target=".StrSafe_DB(($SesType=='Q' || $SesType=='E' ? $SesAth4Target : 0)). ","
				. "SesFirstTarget=".StrSafe_DB(($SesType=='Q' || $SesType=='E' ? $SesFirstTarget : 0)) . ","
				. "SesFollow=".StrSafe_DB(($SesType=='F' ? $SesFollow : 0)) . " "
			. "WHERE "
				. "SesOrder=" .StrSafe_DB($SesOrder) ." AND "
				. "SesType=" .StrSafe_DB($SesType) . " AND "
				. "SesTournament=" .StrSafe_DB($SesTournament) . " 	";
		//print $q.'<br><br>';
		$rs=safe_w_sql($q);

		if (!$rs) 	{
			$ret='_error_';
		} else {

		// IMPLICAZIONI
			switch($SesType) {
				case 'Q':
					/*
					 *  rigenero i paglioni per la sessione se i vecchi parametri son
					 *  cambiati quando $regenerateTargets==false; se vale true rigenero sempre
					 */
					$regenerate=($oldSesTar4Session!=$SesTar4Session || $oldSesAth4Target!=$SesAth4Target);

					if ($forceRegenerateTargets)
						$regenerate=true;

					if ($regenerate)
					{
						//print 'pp';exit;
						$x=regenerateQualTargetsForSession($SesTournament,$SesOrder);
						if (!$x)
						{
							$ret='_error_';
						}
						else
						{
						/*
						 * se si arriva qui bisogna mettere a posto l'assegnazione dei target alle persone
						 * che sono nella sessione SesOrder.
						 */

							//$x=correctQualTargetsForSession($SesTournament,$SesOrder,$oldSesTar4Session,$oldSesAth4Target);
						}
					}
					break;
				case 'E':
					break;
				case 'F':
					break;
				case 'T':
					break;
			}
		}
		//exit;
		return $ret;
	}

/**
 * deleteSession().
 * Cancella una sessione
 * @param int $SesTournament: torneo
 * @param int $SesOrder: numero sessione
 * @param string $SesType: tiop
 * @return mixed: true se ok; messaggio di errore altrimenti
 */
	function deleteSession($SesTournament,$SesOrder,$SesType, $TargetNo=0)
	{
		$ret=true;

		$lastOrder=getLastOrderForType($SesTournament,$SesType);

		$q
			= "DELETE FROM Session "
			. "WHERE "
				. "SesTournament=" . StrSafe_DB($SesTournament) . " AND "
				. "SesType=" . StrSafe_DB($SesType) . " AND "
				. "SesOrder=" . StrSafe_DB($SesOrder) . " ";

		$rs=safe_w_sql($q);

		if (!$rs)
		{
			$ret='_error_';
		}
		else
		{

		// IMPLICAZIONI
			switch($SesType)
			{
				case 'Q':
					// aggiorno il numero di sessioni di qualifica (Tournament.ToNumSession)
					$x=updateQualNumSession($SesTournament);
					if (!$x)
					{
						$ret='_error_';
					}
					else
					{
						// elimino i paglioni della sessione eliminata
						$x=destroyQualTargetsForSession($SesTournament, $SesOrder);
						if (!$x)
						{
							$ret='_error_';
						}
						else
						{
						// sgancio le quals
							$x=unlinkQualTargetsForSession($SesTournament,$SesOrder, $TargetNo);

							if (!$x)
							{
								$ret='_error_';
							}
							else
							{
							// tiro indietro le quals
								if ($SesOrder<$lastOrder)
								{
									//$x=moveQualOneBackFrom($SesTournament,$SesOrder+1);
									$x=moveOneBackFrom($SesTournament,$SesOrder+1,'Q');

									if (!$x)
									{
										$ret='_error_';
									}

									//exit;
								}
							}
						}
					}

					// Delete the DistanceInfo of that session
					safe_w_sql("delete from DistanceInformation where DiTournament=$SesTournament and DiSession=$SesOrder and DiType='Q'");
					break;
				case 'E':
					$x=moveOneBackFrom($SesTournament,$SesOrder+1,'E');

					if (!$x)
					{
						$ret='_error_';
					}
					break;
				case 'F':
					$x=moveOneBackFrom($SesTournament,$SesOrder+1,'F');

					if (!$x)
					{
						$ret='_error_';
					}
					break;
			}
		}

		return $ret;
	}

/**
 * updateQualNumSession().
 * Aggiorna il numero di sessioni
 * @param int $tour: torneo
 * @return bool: true se ok false altrimenti
 */
	function updateQualNumSession($tour)
	{
		$q
			= "UPDATE "
				. "Tournament "
			. "SET "
				. "ToNumSession=(SELECT count( * ) FROM Session WHERE SesType = 'Q' AND SesTournament =" . StrSafe_DB($tour) . ") "
			. "WHERE "
				. "ToId=". StrSafe_DB($tour) . " ";
		//print $q;exit;
		$rs=safe_w_sql($q);

		return ($rs!==false);
	}

/**
 * destroyQualTargetsForSession().
 * Distrugge i bersagli della sessione (Q)
 * @param int $tour: torneo
 * @param int session: numero sessione
 * @return bool: true se ok false altrimenti
 */
	function destroyQualTargetsForSession($tour,$session)
	{
		$q
			= "DELETE FROM AvailableTarget WHERE AtTournament=" . StrSafe_DB($tour) . " AND AtTargetNo LIKE '" . $session . "%' ";
		$rs=safe_w_sql($q);

		return true;
	}

/**
 * createQualTargetsForSession().
 * Crea i bersagli per la sessione (Q)
 * @param int $tour: torneo
 * @param int $session: numero sessione
 * @return bool: true se ok false altrimenti
 */
	function createQualTargetsForSession($tour,$session)
	{
	// parametri per la sessione
		$q="SELECT SesFirstTarget,SesTar4Session,SesAth4Target FROM Session WHERE SesTournament=" . StrSafe_DB($tour) . " AND SesOrder=" . StrSafe_DB($session) . " AND SesType='Q' ";
		$rs=safe_r_sql($q);

		if (!($rs && safe_num_rows($rs)==1))
			return false;

		$row=safe_fetch($rs);

		if ($row->SesTar4Session>0 && $row->SesAth4Target>0)
		{
			$q= "INSERT INTO AvailableTarget (AtTournament,AtTargetNo) VALUES";

			$tuple=array();


			for ($tt=$row->SesFirstTarget;$tt<($row->SesTar4Session+$row->SesFirstTarget);++$tt)
			{
				for ($aa=1;$aa<=$row->SesAth4Target;++$aa)
				{
					//$TargetNo = $ss . str_pad($tt,3,'0',STR_PAD_LEFT) . chr($aa+64);
					$TargetNo = $session . str_pad($tt,TargetNoPadding,'0',STR_PAD_LEFT) . chr($aa+64);
					$tuple[]="(" . StrSafe_DB($tour) . "," . StrSafe_DB($TargetNo) . ") ";
				}
			}

			$q.=implode(',',$tuple);

			$rs=safe_w_sql($q);

			return ($rs!==false);
		}

		return true;
	}

/**
 * regenerateQualTargetsForSession().
 * Rigenera i bersagli per la sessione (Q)
 * @param int $tour: torneo
 * @param int $session: numero sessione
 * @return bool: true se ok false altrimenti
 */
	function regenerateQualTargetsForSession($tour,$session)
	{
		$ret=false;

		destroyQualTargetsForSession($tour,$session);
		$ret=createQualTargetsForSession($tour,$session);

		return $ret;
	}

/**
 * unlinkQualTargetsForSession()
 * Sgancia i target delle qualifiche di una certa sessione
 * @param int $tour: torneo
 * @param int $session: numero sessione
 * @return bool: true se ok false altrimenti
 */
	function unlinkQualTargetsForSession($tour,$session,$Target='')
	{
		$q
			= "UPDATE "
				. "Qualifications INNER JOIN Entries ON QuId=EnId "
			. "SET "
				. "QuSession=0,QuTargetNo='',QuBacknoPrinted=0 "
			. "WHERE "
				. "EnTournament=" . StrSafe_DB($tour)
				. " AND QuSession=" . StrSafe_DB($session)
				. " AND QuTargetNo>" . StrSafe_DB($session.str_pad($Target+1, 3, '0', STR_PAD_LEFT));

		$rs=safe_w_sql($q);

		return ($rs!==false);
	}

/**
 * calcNewOrderForType()
 * Calcola il prossimo numero di sessione di un certo tipo
 * @param int $tour: torneo
 * @param string $type: tipo
 * @return int: nuovo numero di sessione
 */
	function calcNewOrderForType($tour,$type)
	{
		$newOrder=0;
		$q
			="SELECT "
				. "IFNULL((MAX(SesOrder)+1),1) AS newOrder "
			. "FROM "
				. "Session "
			. "WHERE "
				. "SesTournament=" . StrSafe_DB($tour). " AND SesType=" . StrSafe_DB($type) . " "
		;
	//	print $q;exit;
		$r=safe_r_sql($q);
		if ($r && safe_num_rows($r)==1)
			$newOrder=safe_fetch($r)->newOrder;

		return $newOrder;
	}

/**
 * getLastOrderForType().
 * Estrae l'ultimo numero di sessione di un certo tipo
 * @param int $tour: torneo
 * @param string $type: tipo
 * @return int: ultimo numero di sessione
 */
	function getLastOrderForType($tour,$type)
	{
		$lastOrder=0;
		$q
			="SELECT "
				. "IFNULL((MAX(SesOrder)),1) AS lastOrder "
			. "FROM "
				. "Session "
			. "WHERE "
				. "SesTournament=" . StrSafe_DB($tour). " AND SesType=" . StrSafe_DB($type) . " "
		;
	//	print $q;exit;
		$r=safe_r_sql($q);
		if ($r && safe_num_rows($r)==1)
			$lastOrder=safe_fetch($r)->lastOrder;

		return $lastOrder;
	}

/**
 * moveOneBackFrom().
 * Riscala di uno tutte le sessioni di un certo tipo a partire da $from
 *
 * L'idea è:
 * per ogni sessione a partire da $from
 * 1) rinumero la sessione (decremento di uno in pratica)
 * 2) aggiorno la sessione forzando la generazione dei target (se parlo di qualifiche; l'update non avrà effetto perchè la riga in tabella è uguale)
 * 3) le quals con la sessione vecchia verranno aggiornate.
 *
 *
 * @param int $tour: torneo
 * @param int $from: sessione di partenza
 * @param string $type: tipo di sessione
 * @return bool: true se ok false altrimenti
 */
	function moveOneBackFrom($tour,$from,$type)
	{
		$ret=true;

	// prendo le sessioni con SesOrder>=from
		$q
			= "SELECT * "
			. "FROM "
				. "Session "
			. "WHERE "
				. "SesTournament=" . StrSafe_DB($tour) . " AND SesOrder>=" . StrSafe_DB($from) . " AND SesType=" . StrSafe_DB($type). " "
		;
		//print $q.'<br><br>';
		$rs=safe_r_sql($q);

		if ($rs && safe_num_rows($rs)>0)
		{
		/*
		 * devo partire da $from-1 perchè
		 * se ho cancellato la 3 $from vale 4 quindi il 4 andrà in 3, il 5 in 4 etc...
		 */
			$newOrder=$from-1;

			while ($row=safe_fetch($rs))
			{
				$q
					= "UPDATE "
						. "Session "
					. "SET "
						. "SesOrder=" .StrSafe_DB($newOrder) . " "
					. "WHERE "
						. "SesOrder=" .StrSafe_DB($row->SesOrder) ." AND "
						. "SesType=" .StrSafe_DB($row->SesType) . " AND "
						. "SesTournament=" .StrSafe_DB($row->SesTournament) . " ";
				$r=safe_w_sql($q);

				if (!$r)
				{
					$ret=false;
					break;
				}
				else
				{
				/*
				 * Faccio l'up della sessione forzando la rigenerazione dei targets.
				 * la query di up non sortisce effetto perchè non ci sono cambiamenti
				 */
					$x=updateSession(
						$tour,
						$newOrder,
						$row->SesType,
						$row->SesName,
						$row->SesTar4Session,
						$row->SesAth4Target,
						$row->SesFirstTarget,
						$row->SesFollow,
						true
					);

					if (!$x)
					{
						$ret=false;
						break;
					}
					else
					{
					/*
					 * adesso le cose dipendono dal tipo di sessione
					 */
						switch($type)
						{
							case 'Q':
							/*
							 * Aggiorno Qualifications.
							 * Le persone che hanno sessione e paglione settato sulla sessione $row->SesOrder
							 * subiranno l'update
							 */
								$q
									= "UPDATE "
										. "Qualifications INNER JOIN Entries ON QuId=EnId "
									. "SET "
										. "QuSession=" . $newOrder . ", "
										. "QuTargetNo=CONCAT('{$newOrder}',SUBSTRING(QuTargetNo,2)), QuBacknoPrinted=0 "
									. "WHERE "
										. "QuSession=" . $row->SesOrder . " AND EnTournament=" . StrSafe_DB($tour) . " "
								;

								//print $q . '<br><br>';
								$r=safe_w_sql($q);

								if (!$r)
								{
									$ret=false;
									break;
								}

								break;
							case 'E':
								break;
							case 'F':
								break;
						}
					}
				}
				++$newOrder;
			}

			if ($ret)
			{
			/*
	 		 * Visto che restano i bersagli della vecchia ultima, li faccio sparire
	 		 */
				if ($type=='Q')
				{
				/*
				 * $newOrder qui contiene l'ultima sessione prima della rinumerazione
				 * perchè l'ultimo incremento lo ha portato lì.
				 */
					$x=destroyQualTargetsForSession($tour,$newOrder);

					if (!$x)
					{
						$ret=false;
					}
				}
			}
		}

		return $ret;
	}

/**
 * moveQualOneBackFrom().
 * Riscala di uno tutte le sessioni di qualifica a partire da $from
 *
 * L'idea è:
 * per ogni sessione a partire da $from
 * 1) rinumero la sessione (decremento di uno in pratica)
 * 2) aggiorno la sessione forzando la generazione dei target (l'update non avrà effetto perchè la riga in tabella è uguale)
 * 3) le quals con la sessione vecchia verranno aggiornate.
 *
 * Esempio:
 * 4 sessioni con 10 bersagli e 4 persone su ognuno.
 * Viene cancellata la sessione 2. Otteniamo =>
 * 1) Sparisce da Session la riga
 * 2) In Qualfications alle le persone nella sessione 2 vengono sganciati i bersagli
 * 3) La 3 diventa 2 e vengono rigenerati i bersagli della nuova 2 (uguali a quelli della 3)
 * 4) Le persone sulla 3 vengono spostate sulla 2
 * 5) La 4 diventa 3 e vengono rigenerati i bersagli della nuova 3 (uguali a quelli della 4)
 * 6) Le persone sulla 4 vengono spostate sulla 3
 * 7) I bersagli dell'ultima vegono rancati
 *
 * @param int $tour: torneo
 * @param int $from: sessione di partenza
 * @return bool: true se ok false altrimenti
 */
	function moveQualOneBackFrom($tour,$from)
	{
		$ret=true;

	// prendo le sessioni con SesOrder>=from
		$q
			= "SELECT * "
			. "FROM "
				. "Session "
			. "WHERE "
				. "SesTournament=" . StrSafe_DB($tour) . " AND SesOrder>=" . StrSafe_DB($from) . " AND SesType='Q' "
		;
		//print $q.'<br><br>';
		$rs=safe_r_sql($q);

		if ($rs && safe_num_rows($rs)>0)
		{
		/*
		 * devo partire da $from-1 perchè
		 * se ho cancellato la 3 $from vale 4 quindi il 4 andrà in 3, il 5 in 4 etc...
		 */
			$newOrder=$from-1;

			while ($row=safe_fetch($rs))
			{
				$q
					= "UPDATE "
						. "Session "
					. "SET "
						. "SesOrder=" .StrSafe_DB($newOrder) . " "
					. "WHERE "
						. "SesOrder=" .StrSafe_DB($row->SesOrder) ." AND "
						. "SesType=" .StrSafe_DB($row->SesType) . " AND "
						. "SesTournament=" .StrSafe_DB($row->SesTournament) . " ";
				$r=safe_w_sql($q);

				if (!$r)
				{
					$ret=false;
					break;
				}
				else
				{

				/*
				 * Faccio l'up della sessione forzando la rigenerazione dei targets.
				 * la query di up non sortisce effetto perchè non ci sono cambiamenti
				 */
					$x=updateSession(
						$tour,
						$newOrder,
						$row->SesType,
						$row->SesName,
						$row->SesTar4Session,
						$row->SesAth4Target,
						$row->SesFirstTarget,
						$row->SesFollow,
						true
					);

					if (!$x)
					{
						$ret=false;
						break;
					}
					else
					{
					/*
					 * Aggiorno Qualifications.
					 * Le persone che hanno sessione e paglione settato sulla sessione $row->SesOrder
					 * subiranno l'update
					 */
						$q
							= "UPDATE "
								. "Qualifications INNER JOIN Entries ON QuId=EnId "
							. "SET "
								. "QuSession=" . $newOrder . ", "
								. "QuTargetNo=CONCAT('{$newOrder}',SUBSTRING(QuTargetNo,2)), QuBacknoPrinted=0 "
							. "WHERE "
								. "QuSession=" . $row->SesOrder . " AND EnTournament=" . StrSafe_DB($tour) . " "
						;

						//print $q . '<br><br>';
						$r=safe_w_sql($q);

						if (!$r)
						{
							$ret=false;
							break;
						}
					}
				}

				++$newOrder;
			}

		/*
		 * Visto che restano i bersagli della vecchia ultima, li faccio sparire
		 */
			if ($ret)
			{
			/*
			 * $newOrder qui contiene l'ultima sessione prima della rinumerazione
			 * perchè l'ultimo incremento lo ha portato lì.
			 */
				$x=destroyQualTargetsForSession($tour,$newOrder);

				if (!$x)
				{
					$ret=false;
				}
			}
		}

		return $ret;
	}

	function correctQualTargetsForSession($SesTournament,$SesOrder,$oldSesTar4Session,$oldSesAth4Target)
	{
	// info attuali della sessione
		$SesTar4Session=0;
		$SesAth4Target=0;

		$x=getQualTargetsInfoForSession($SesTournament,$SesOrder);

		if ($x!==false)
		{
			list($SesTar4Session,$SesAth4Target)=$x;

			/*
			 * Il problema si presenta se $oldSesTar4Session>$SesTar4Session oppure $oldSesAth4Target>$SesAth4Target.
			 * Se i numeri aumentano non ci sono problemi perchè la "vecchia" sessione è contenuta nella "nuova".
			 *
			 * Se il numero di persone si riduce occorre sganciare tutti quelli che sono nelle lettere superiori
			 * all'ultima buona dopo l'update.
			 *
			 * Se il numero di paglioni si riduce occorre sganciare tutti i bersagli dalle persone nelle piazzole
			 * dopo l'ultima buona dopo l'update.
			 */

		// si riducono le persone
			if ($SesAth4Target<$oldSesAth4Target)
			{
			/*
			 *  in base al numero di persone posso ricavare l'ultima lettera buona
			 *  chr(64) = '@'
			 *  chr(65) = 'A'
			 */
				$lastLetter=chr($SesAth4Target+64);

			/*
			 * Quindi tutte le lettere oltre $lastLetter vanno resettate.
			 * In pratica si annullano le righe di Qualifications che hanno
			 * QuSession=$SesOrder e QuTargetNo<>'' e l'ultima lettera di QuTargetNo>$lastLetter
			 */
				$q
					= "UPDATE "
						. "Qualifications INNER JOIN Entries ON QuId=EnId "
					. "SET "
						. "QuSession=0,QuTargetNo='',QuBacknoPrinted=0 "
					. "WHERE "
						. "EnTournament=" . StrSafe_DB($SesTournament) . " AND "
						. "QuSession=" . StrSafe_DB($SesOrder) . " AND "
						. "QuTargetNo<>'' AND "
						. "SUBSTRING(QuTargetNo,-1)>'" . $lastLetter . "' ";
				;
				$r=safe_w_sql($q);
			}

		// si riducono i  bersagli
			if ($SesTar4Session<$oldSesTar4Session)
			{
			/*
			 * L'ultimo target è dato dal numero di bersagli
			 */
				$lastTarget=$SesTar4Session;

			/*
			 * Ora tutte le persone della sessione in bersagli più alti di $lastTarget
			 * vengono sganciate
			 */
				$q
					= "UPDATE "
						. "Qualifications INNER JOIN Entries ON QuId=EnId "
					. "SET "
						. "QuSession=0,QuTargetNo='',QuBacknoPrinted=0 "
					. "WHERE "
						. "EnTournament=" . StrSafe_DB($SesTournament) . " AND "
						. "QuSession=" . StrSafe_DB($SesOrder) . " AND "
						. "CAST(SUBSTRING(QuTargetNo,2, ".TargetNoPadding.") AS UNSIGNED)>" . $lastTarget . " ";
				;
				$r=safe_w_sql($q);

			}
		}
		else
		{
			return false;
		}
	}

/**
 * getQualTargetsInfoForSession().
 * Ritorna il numero di bersagli e le persone per bersaglio per una certa sessione
 * @param int $SesTournament: torneo
 * @param int $SesOrder: numero sessione
 * @return mixed: array[SesTar4Session,SesAth4Target] se ok altrimenti false
 */
	function getQualTargetsInfoForSession($SesTournament,$SesOrder)
	{
		$SesTar4Session=0;
		$SesAth4Target=0;

		$q
			= "SELECT "
				. "SesTar4Session,SesAth4Target "
			. "FROM "
				. "Session "
			. "WHERE "
				. "SesTournament=" . StrSafe_DB($SesTournament) . " AND SesOrder=" . StrSafe_DB($SesOrder) . ""
		;
		//print $q . '<br><br>';
		$rs=safe_r_sql($q);
		if (!($rs && safe_num_rows($rs)==1))
		{
			return false;
		}

		$row=safe_fetch($rs);

		$SesTar4Session=$row->SesTar4Session;
		$SesAth4Target=$row->SesAth4Target;

		return array($SesTar4Session,$SesAth4Target);
	}

	function getDistanceArrays($ToType) {
		$d=array();
		switch($ToType) {
			case '1': // Type_FITA				4
			case '18': // Type_FITA+50			0
			case '30': // Type_Bel_BFITA_Out	4
				$d=array(array( 6,6),array( 6,6),array(12,3), array(12,3)); break;
			case '2': // Type_2xFITA			8
				$d=array(array( 6,6),array( 6,6),array(12,3), array(12,3), array( 6,6),array( 6,6),array(12,3), array(12,3)); break;
			case '3': // Type_70m Round			2
				$d=array(array( 6,6),array( 6,6)); break;
			case '4': // Type_FITA 72			4
				$d=array(array( 3,6),array( 3,6),array( 6,3), array( 6,3)); break;
			case '5': // Type_900 Round			3
				$d=array(array( 5,6),array( 5,6), array(10,3)); break;
			case '6': // Type_Indoor 18			2
			case '7': // Type_Indoor 25			2
			case '21': // Type_Face2Face		0
				$d=array(array(10,3),array(10,3)); break;
			case '8': // Type_Indoor 25+18		4
				$d=array(array(10,3),array(10,3),array(10,3),array(10,3)); break;
			case '9': // Type_HF 12+12			1
				$d=array(array(24,3)); break;
			case '10': // Type_HF 24+24			2
				$d=array(array(24,3),array(24,3)); break;
			case '11': // 3D					1
				$d=array(array(20,2)); break;
			case '12': // Type_HF 12+12			2
			case '23': // Type_Bel_25m_Out		2
			case '24': // Type_Bel_50-30_Out	2
			case '25': // Type_Bel_50_Out		2
			case '26': // Type_Bel_B10_Out		2
			case '27': // Type_Bel_B15_Out		2
			case '28': // Type_Bel_B25_Out		2
			case '29': // Type_Bel_B50-30_Out	2
			case '31': // Type_ITA_Sperimental	2
				$d=array(array(12,3),array(12,3)); break;
			case '13': // 3D					2
				$d=array(array(20,2), array(20,2)); break;
			case '14': // Type_Las Vegas		4
				$d=array(array(10,3),array(10,3),array(10,3)); break;
			case '15': // Type_GiochiGioventu	2
			case '16': // Type_GiochiGioventuW	2
				$d=array(array(8,3), array(8,3)); break;
			case '17': // Type_NorField			0
			case '19': // Type_GiochiStudentes	1
				$d=array(array(12,3)); break;
			case '20': // Type_SweForestRound	0
				$d=array(array(15,1),array(15,1)); break;
			case '22': // Type_Indoor 18		1
				$d=array(array(10,3)); break;
		}
		return $d;
	}

