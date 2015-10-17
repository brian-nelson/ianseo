<?php
/*
													- UpdateQuals.php -
	Aggiorna la tabella Qualifications
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');	// nuovo by simo

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$Cosa = "";
	$Atleta = "";
	$Societa = "";
	$Category = "";

	$OldValue=null;

	$Evento = '*#*#';
	$Div="";
	$Cl="";

	$MyRow=NULL;

	foreach ($_REQUEST as $Key => $Value)
	{
		if (substr($Key,0,2)=='d_')
		{
			if (!IsBlocked(BIT_BLOCK_QUAL))
			{
				list(,$Cosa,$Atleta)=explode('_',$Key);

			/*
				Devo estrarre il vecchio valore dal db perchè solo se cambia qualcosa devo azzerare
				il flag di shootoff. Dato che in ogni caso ho un update del timestamp, l'affected_rows ritornerebbe sempre 1.

			*/
				$Sel
					= "SELECT " . $Cosa . " AS OldValue "
					. "FROM Qualifications "
					. "WHERE QuId=" . StrSafe_DB($Atleta) . " ";
				$RsSel =safe_r_sql($Sel);

				$OldValue='';

				if (safe_num_rows($RsSel)==1)
				{
					$rr=safe_fetch($RsSel);
					$OldValue=$rr->OldValue;
				}

			// Controllo errori
				if (strpos($Key,'Score')!==false)
				{
					$MaxScore=0;
					/*$Select
						= "SELECT TtMaxDistScore "
						. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
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
					// distanza trattata
						$Dist=substr($Cosa,3,1);


						$Select
							= "SELECT QuD" . $Dist . "Score AS Score,QuD" . $Dist . "Gold AS Gold,QuD" . $Dist . "Xnine AS Xnine,"
							//. "ToGolds AS TtGolds,ToXNine AS TtXNine "
							. "ToGoldsChars AS TtGolds,ToXNineChars AS TtXNine "
							. "FROM Qualifications "
							. "INNER JOIN Entries ON QuId=EnId  "
							. "INNER JOIN Tournament ON EnTournament=ToId "
							. "WHERE QuId=" . StrSafe_DB($Atleta) . " AND ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

						$RsGX=safe_r_sql($Select);

						if (debug)
							print '..'. $Select . '<br><br>';

						if (safe_num_rows($RsGX)==1)
						{
							$Row=safe_fetch($RsGX);

						/*
						 * Il giochino funziona così:
						 * siano #G il numero di ori inserito nella casella, min($G) il valore del simbolo con meno valore negli ori,
						 * #X il numero di X inserito nella casella e min($X) il valore del simbolo con meno valore negli X allora
						 *
						 * il numero di ori è buono se
						 * 			[score - (#G x min($G))] > =0
						 *
						 * il numero delle X è buono se
						 * 			#X < #G,
						 * 			nel caso in cui i simboli delle X sono inclusi in quelli degli ori
						 *
						 * 			[score - (#G x min($G)) - (#X x min($X))] >= 0
						 * 			nel caso in cui i simboli non siano inclusi
						 *
						 */

							$arrG=str_split($Row->TtGolds);
							$arrX=str_split($Row->TtXNine);

							$minG=100;
							$minX=100;

						// minimo dei gold
							for($i=0;$i<count($arrG);++$i)
							{
								$tmp=ValutaArrowString($arrG[$i]);
								if ($tmp<$minG)
									$minG=$tmp;
							}

						// minimo delle x
							for($i=0;$i<count($arrX);++$i)
							{
								$tmp=ValutaArrowString($arrX[$i]);
								if ($tmp<$minX)
									$minX=$tmp;
							}

						// controllo i gold
							if (strpos($Key,'Gold')!==false)
							{
								if (($Row->Score - ($Value*$minG))<0)
									$Errore=1;
							}
						// controllo gli xnine
							elseif(strpos($Key,'Xnine')!==false)
							{
							/*
							 * Se l'intersezione delle colonne P delle x e degli ori non è vuota oppure non lo è
							 * quella delle colonne N, allora le x sono incluse negli ori
							 *
							 */
								$arrG_P=array();
								$arrG_N=array();
								for ($i=0;$i<count($arrG);++$i)
								{
									//print $arrG[$i].'<br>';
									$arrG_P[]=DecodeFromLetter($arrG[$i]);
									//$arrG_N[]=$LetterPoint[$arrG[$i]]['N'];
									$arrG_N[]=ValutaArrowString($arrG[$i]);
								}

//								print '<pre>';
//								print_r($arrG_P);
//								print_r($arrG_N);
//								print '</pre>';
//								exit;

								$arrX_P=array();
								$arrX_N=array();
								for ($i=0;$i<count($arrX);++$i)
								{
									$arrX_P[]=DecodeFromLetter($arrX[$i]);
									//$arrX_N[]=$LetterPoint[$arrX[$i]]['N'];
									$arrX_N[]=ValutaArrowString($arrX[$i]);
								}

							// inclusione
								if (array_intersect($arrX_P,$arrG_P)!==array() || array_intersect($arrX_N,$arrG_N)!==array())
								{
									if ($Value>$Row->Gold)
										$Errore=1;
								}
								else	// no inclusione
								{
									if (($Row->Score - ($Row->Gold*$minG) - ($Value*$minX))<0)
										$Errore=1;
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
					= "UPDATE Qualifications SET "
					. $Cosa . "=" . StrSafe_DB($Value) . ", "
					. "QuScore=QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score+QuD8Score,"
					. "QuGold=QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold,"
					. "QuXnine=QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine, "
					. "QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits, "
					. "QuTimestamp=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
					. "WHERE QuId=" . StrSafe_DB($Atleta) . " ";
				$RsUp=safe_w_sql($Update);
				//print $Update;exit;
				if (debug)
					print $Update  .' <br><br>';

				//print '..' . safe_w_affected_rows() . '<br>';
				if (safe_w_affected_rows()==1 && $OldValue!=$Value)
				{

				// distruggo e ricreo le eliminatorie
				// scopro in che evento elim si trova la divcl del tipo
					$q="
						SELECT EvCode
						FROM
							Entries
							INNER JOIN
								EventClass
							ON EnTournament=EcTournament AND EcTeamEvent=0 AND EnDivision=EcDivision AND EnClass=EcClass
							INNER JOIN
								Events
							ON EcCode=EvCode AND EcTournament=EvTournament AND EvTeamEvent=EcTeamEvent AND EcTeamEvent=0 AND (EvElim1+EvElim2)>0
						WHERE
							EnId={$Atleta}
					";
					$r=safe_r_sql($q);
					//print $q;exit;
					if ($r && safe_num_rows($r)>0)
					{
						while ($row=safe_fetch($r))
						{
							$ev=$row->EvCode;

							for ($j=1;$j<=2;++$j)
							{
								//print 'pp';
								ResetElimRows($ev,$j);
							}
						}
					}

				// azzero gli shootoff
					$q="
						SELECT DISTINCT EvCode,EvTeamEvent
						FROM
							Events
							INNER JOIN
								EventClass
							ON EvCode=EcCode AND (EvTeamEvent='0' OR EvTeamEvent='1') AND EcTournament={$_SESSION['TourId']}
							INNER JOIN
								Entries
							ON TRIM(EcDivision)=TRIM(EnDivision) AND TRIM(EcClass)=TRIM(EnClass)  AND EnId={$Atleta}
						WHERE
							 (EvTeamEvent='0' AND EnIndFEvent='1') OR (EvTeamEvent='1' AND EnTeamFEvent='1') AND EvTournament={$_SESSION['TourId']}
					";
					//print $q;
					$Rs=safe_r_sql($q);
					if ($Rs && safe_num_rows($Rs)>0)
					{
						while ($row=safe_fetch($Rs))
						{
							ResetShootoff($row->EvCode,$row->EvTeamEvent,0);
						}
					}

					if (debug)
						print $Update . '<br>';
				}

			// estraggo i totali
				$Select
					= "SELECT QuId,QuScore,QuGold,QuXnine, "
					. $Cosa . " "
					. "FROM Qualifications WHERE QuId=" . StrSafe_DB($Atleta) . " ";
				$Rs=safe_r_sql($Select);
				//print $Select;exit;
				$Errore = 0;	// no error
				$MyRow=NULL;
				if (safe_num_rows($Rs)==1)
				{
					$MyRow=safe_fetch($Rs);

				// se il valore del campo passato � uguale a quello in db ok, altrimenti errore
					if ($Value!=$MyRow->{$Cosa})
					{
						$Errore=1;
						//print 'oo';exit;
					}
				}
				else
				{
					$Errore=1;
					//print 'pp';exit;
				}
			}

			if ($OldValue!=$Value && !isset($_REQUEST["NoRecalc"]))
			{
				if ($Errore==0)
				{
					if (debug)
						print 'Faccio la rank di distanza<br>';

				// Se non ho errori, calcolo la rank della distanza per l'evento
					$Distanza = array();
					preg_match("/[1-8]/", $Cosa, $Distanza);

					$Select
						= "SELECT CONCAT(EnDivision,EnClass) AS MyEvent, EnCountry as MyTeam, EnDivision, EnClass "
						. "FROM Entries "
						. "WHERE EnId=" . StrSafe_DB($Atleta) . " AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$Rs=safe_r_sql($Select);

					if (safe_num_rows($Rs)==1)
					{
						$rr=safe_fetch($Rs);
						$Evento=$rr->MyEvent;
						$Category = $rr->MyEvent;
						$Societa = $rr->MyTeam;
						$Div = $rr->EnDivision;
						$Cl = $rr->EnClass;

						if (CalcQualRank($Distanza[0],$Evento))
						{
							$Errore=1;
							//print 'errore CalcQualRank distanza';exit;
						}
					}
					else
						$Errore=1;
				}

				if ($Errore==0)
				{
					if (debug) print $Evento . '<br>';

				// se non ho errori calcolo la rank globale per l'evento

					if (CalcQualRank(0,$Evento))
					{
						//print 'errore CalcQualRank 0';exit;
						$Errore=1;
					}
				}

			// eventi di cui calcolare le rank assolute
				$events4abs=array();
				$q="SELECT EcCode FROM EventClass WHERE EcTournament={$_SESSION['TourId']} AND EcTeamEvent=0 AND EcDivision='" . $Div . "' AND EcClass='" . $Cl . "' ";

				$r=safe_r_sql($q);

				if ($r)
				{
					while ($tmp=safe_fetch($r))
					{
						$events4abs[]=$tmp->EcCode;
					}
				}
				else
					$Errore=1;


			// rank abs di distanza
				if ($Errore==0)
				{
					if (debug)
						print 'Faccio la rank abs di distanza<br>';

					if (count($events4abs)>0)
					{
						if (!Obj_RankFactory::create('Abs',array('events'=>$events4abs,'dist'=>$Distanza[0]))->calculate())
						{
							$Errore=1;
							//print 'errore abs distanza';exit;
						}
					}
				}

				if ($Errore==0)
				{
					if (debug)
						print 'Faccio la rank abs totale<br>';

					if (count($events4abs)>0)
					{
						if (!Obj_RankFactory::create('Abs',array('events'=>$events4abs,'dist'=>0))->calculate())
						{
							$Errore=1;
							//print 'errore abs 0';exit;
						}
					}
				}

				if ($Errore==0)
				{
					if (debug)
						print 'Faccio la classifica a squadre di classe <br>';
				// se non ho errori calcolo la rank globale per l'evento
					if (MakeTeams($Societa, $Evento))
						$Errore=1;
				}

				if ($Errore==0)
				{
					if (debug)
						print 'Faccio la classifica a squadre assoluta <br>';
				// se non ho errori calcolo la rank globale per l'evento
					if (MakeTeamsAbs($Societa,$Div,$Cl))
						$Errore=1;
				}
			}



		// produco l'xml di ritorno
			if (!debug)
				header('Content-Type: text/xml');

			print '<response>' . "\n";
			print '<error>' . $Errore . '</error>' . "\n";
			print '<id>' . ($MyRow!=NULL ? $MyRow->QuId : 'null') . '</id>' . "\n";
			print '<score>' . ($MyRow!=NULL ? $MyRow->QuScore : 'null') . '</score>' . "\n";
			print '<gold>' . ($MyRow!=NULL ? $MyRow->QuGold : 'null') . '</gold>' . "\n";
			print '<xnine>' .  ($MyRow!=NULL ? $MyRow->QuXnine : 'null') . '</xnine>' . "\n";
			print '<which>' .  $Key . '</which>' . "\n";
			print '</response>' . "\n";
		}
	}
?>