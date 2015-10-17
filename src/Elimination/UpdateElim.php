<?php
/*
													- UpdateQuals.php -
	Aggiorna la tabella Qualifications
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$Cosa = "";
	$Atleta = "";
	$Fase = "";

	foreach ($_REQUEST as $Key => $Value)
	{
		if (substr($Key,0,2)=='d_')
		{

			if (!IsBlocked(BIT_BLOCK_ELIM))
			{
				list(,$Cosa,$Atleta,$Fase)=explode('_',$Key);
			/*
				Devo estrarre il vecchio valore dal db perchè solo se cambia qualcosa devo azzerare
				il flag di shootoff. Dato che in ogni caso ho un update del timestamp, l'affected_rows ritornerebbe sempre 1.

			*/
				$Sel
					= "SELECT " . $Cosa . " AS OldValue "
					. "FROM Eliminations "
					. "WHERE ElId=" . StrSafe_DB($Atleta) . " AND ElElimPhase=" . StrSafe_DB($Fase) ;
				$RsSel =safe_r_sql($Sel);

				$OldValue='';

				if (safe_num_rows($RsSel)==1)
				{
					$rr=safe_fetch($RsSel);
					$OldValue=$rr->OldValue;
				}

			// seleziono il max punteggio possibile se devo scrivere lo score
				if (strpos($Key,'Score')!==false)
				{
					$MaxScore=0;
					/*$Select
						= "SELECT TtMaxDistScore "
						. "FROM Tournament LEFT JOIN Tournament*Type ON ToType=TtId "
						. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/
					$Select
						= "SELECT ToMaxDistScore AS TtMaxDistScore "
						. "FROM Tournament "
						. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$RsMax=safe_r_sql($Select);

					if (safe_num_rows($RsMax)==1)
					{
						$rr=safe_fetch($RsMax);
						$MaxScore=$rr->TtMaxDistScore;
						if ($Value>$MaxScore)
							$Errore=1;
					}
					else
						$Errore=1;
				}
				else	// gold o xnine
				{
					if ($Value!=0)
					{
						/*$Select
							= "SELECT ElScore AS Score,ElGold AS Gold,ElXnine AS Xnine,"
							. "TtGolds,TtXNine "
							. "FROM Eliminations INNER JOIN Entries ON ElId=EnId AND ElId=" . StrSafe_DB($Atleta) . " AND ElElimPhase=" . StrSafe_DB($Fase) . " "
							. "INNER JOIN Tournament ON EnTournament=ToId "
							. "INNER JOIN Tournament*Type ON ToType=TtId "
							. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/
						$Select
							= "SELECT ElScore AS Score,ElGold AS Gold,ElXnine AS Xnine,"
							. "ToGolds AS TtGolds,ToXNine AS TtXNine "
							. "FROM Eliminations INNER JOIN Entries ON ElId=EnId AND ElId=" . StrSafe_DB($Atleta) . " AND ElElimPhase=" . StrSafe_DB($Fase) . " "
							. "INNER JOIN Tournament ON EnTournament=ToId "
							. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

						if (debug)
							print $Select . '<br><br>';

						$RsGX=safe_r_sql($Select);

						if (debug)
							print $Select . '<br><br>';

						if (safe_num_rows($RsGX)==1)
						{
							$Row=safe_fetch($RsGX);

						// controllo i gold
							if (strpos($Key,'Gold')!==false)
							{
								switch($Row->TtGolds)
								{
									case '11':
										if ($Value>floor($Row->Score/11))
											$Errore=1;
										break;
									case '10':
										if ($Value>floor($Row->Score/10))
											$Errore=1;
										break;
									case '6+5':
										if ($Value>floor($Row->Score/5))
											$Errore=1;
										break;
								}
							}
						// Controllo gli Xnine
							elseif(strpos($Key,'Xnine')!==false)
							{
								switch($Row->TtXNine)
								{
									case 'X':
									case '6':
										if ($Value>$Row->Gold)
											$Errore=1;
										break;
									case '10':
										if ($Value>floor((($Row->Score-11*$Row->Gold)/10)))
											$Errore=1;
										break;
									case '9':
										if ($Value>floor((($Row->Score-10*$Row->Gold)/9)))
											$Errore=1;
										break;
								}
							}
						}
						else
							$Errore=1;
					}

				}
			}
			else
				$Errore=1;

			if (debug)
				print 'Errore prima di aggiornare: ' . $Errore . '<br>';

			if ($Errore==0)
			{
			// scrivo il dato e aggiorno i totali
				$Update
					= "UPDATE Eliminations SET "
					. $Cosa . "=" . StrSafe_DB($Value) . ", "
					. "ElDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
					. "WHERE ElId=" . StrSafe_DB($Atleta) . " AND ElElimPhase=" . StrSafe_DB($Fase) ;
				$RsUp=safe_w_sql($Update);
				if (debug)
					print $Update  .' <br><br>';

				//print '..' . safe_w_affected_rows() . '<br>';
				if (safe_w_affected_rows()==1 && $OldValue!=$Value)
				{
				/*
				 * QUI il reset della fase successiva delle elim (se c'è)
				 * e il calcolo della rank elim della fase attuale.
				 *
				 * Se fase è 0 sicuramente ho anche la 1 perchè in caso di solo un girone l'unica fase buona è la 1
				 */
					$q="SELECT ElEventCode FROM Eliminations WHERE ElId={$Atleta} AND ElElimPhase={$Fase}";
					$r=safe_r_sql($q);

					$ev='';
					$row=safe_fetch($r);
					$ev=$row->ElEventCode;

					if ($ev!='')
					{
						if ($Fase==0)
						{
							ResetElimRows($ev,2);
						}

						Obj_RankFactory::create('ElimInd',array('eventsC'=>array($ev.'@'.($Fase+1))))->calculate();
					}

				// azzero gli shootoff
					/*$Update
						= "UPDATE Events INNER JOIN EventClass ON EvCode=EcCode AND EvTeamEvent='0' AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
						. "INNER JOIN Entries ON EcDivision=EnDivision AND EcClass=EnClass  AND EnId=" . StrSafe_DB($Atleta) . " "
						. "SET EvShootOff='0' " . ($Fase==0 ? ", EvE2ShootOff='0' " : "")
						. "WHERE EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$Rs=safe_w_sql($Update);
					set_qual_session_flags();*/

					$q="
						SELECT EvCode
						FROM
							Events
							INNER JOIN
								EventClass
							ON EvCode=EcCode AND EvTeamEvent='0' AND EcTournament={$_SESSION['TourId']}
							INNER JOIN
								Entries
							ON EcDivision=EnDivision AND EcClass=EnClass  AND EnId={$Atleta}
						WHERE
							 EvTeamEvent='0' AND EvTournament={$_SESSION['TourId']}
					";
					$Rs=safe_w_sql($q);
					if ($Rs && safe_num_rows($Rs)>0)
					{
						while ($row=safe_fetch($Rs))
						{
							//print $Fase.'<br>';
							ResetShootoff($row->EvCode,0,($Fase==0 ? 1 : 2));
						}
					}
					if (debug)
						print $Update . '<br>';
				}

			// estraggo i totali
				$Select
					= "SELECT ElId, ElElimPhase, "
					. $Cosa . " "
					. "FROM Eliminations WHERE ElId=" . StrSafe_DB($Atleta) . " AND ElElimPhase=" . StrSafe_DB($Fase) ;
				$Rs=safe_r_sql($Select);
				//print $Select;
				$Errore = 0;	// no error
				$MyRow=NULL;
				if (safe_num_rows($Rs)==1)
				{
					$MyRow=safe_fetch($Rs);

				// se il valore del campo passato � uguale a quello in db ok, altrimenti errore
					if ($Value!=$MyRow->{$Cosa})
						$Errore=1;
				}
				else
					$Errore=1;
			}

		// produco l'xml di ritorno
			if (!debug)
				header('Content-Type: text/xml');

			print '<response>' . "\n";
			print '<error>' . $Errore . '</error>' . "\n";
			print '<id>' . ($MyRow!=NULL ? $MyRow->ElId : 'null') . '</id>' . "\n";
			print '<phase>' .  ($MyRow!=NULL ? $MyRow->ElElimPhase : 'null') . '</phase>' . "\n";
			print '<which>' .  $Key . '</which>' . "\n";
			print '</response>' . "\n";
		}
	}
?>