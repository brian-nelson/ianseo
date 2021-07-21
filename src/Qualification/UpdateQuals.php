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

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclQualification, AclReadWrite, false);

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

	foreach ($_REQUEST as $Key => $Value) {
		if (substr($Key,0,2)=='d_') {
			if (!IsBlocked(BIT_BLOCK_QUAL)) {
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

				if (safe_num_rows($RsSel)==1) {
					$rr=safe_fetch($RsSel);
					$OldValue=$rr->OldValue;
				}

			// Controllo errori
				if (strpos($Key,'Score')!==false) 	{
					$MaxScore=0;
					$Select
						= "SELECT ToMaxDistScore AS TtMaxDistScore "
						. "FROM Tournament "
						. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

					$RsMax=safe_r_sql($Select);

					if (safe_num_rows($RsMax)==1) {
						$rr=safe_fetch($RsMax);
						$MaxScore=$rr->TtMaxDistScore;
						if ($Value>$MaxScore) {
                            $Errore = 1;
                        }
					} else {
                        $Errore = 1;
                    }
				} else {
					if ($Value!=0) {
					// distanza trattata
						$Dist=substr($Cosa,3,1);
						$Select
							= "SELECT QuD" . $Dist . "Score AS Score,QuD" . $Dist . "Gold AS Gold,QuD" . $Dist . "Xnine AS Xnine,"
							. "ToGoldsChars AS TtGolds,ToXNineChars AS TtXNine "
							. "FROM Qualifications "
							. "INNER JOIN Entries ON QuId=EnId  "
							. "INNER JOIN Tournament ON EnTournament=ToId "
							. "WHERE QuId=" . StrSafe_DB($Atleta) . " AND ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

						$RsGX=safe_r_sql($Select);

						if (safe_num_rows($RsGX)==1) {
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
							for($i=0;$i<count($arrG);++$i) {
								$tmp=ValutaArrowString($arrG[$i]);
								if ($tmp<$minG)
									$minG=$tmp;
							}

						// minimo delle x
							for($i=0;$i<count($arrX);++$i) {
								$tmp=ValutaArrowString($arrX[$i]);
								if ($tmp<$minX)
									$minX=$tmp;
							}

							if (strpos($Key,'Gold')!==false) {
								if (($Row->Score - ($Value*$minG))<0)
									$Errore=1;
							} elseif(strpos($Key,'Xnine')!==false) {
							/*
							 * Se l'intersezione delle colonne P delle x e degli ori non è vuota oppure non lo è
							 * quella delle colonne N, allora le x sono incluse negli ori
							 *
							 */
								$arrG_P=array();
								$arrG_N=array();
								for ($i=0;$i<count($arrG);++$i) {
									$arrG_P[]=DecodeFromLetter($arrG[$i]);
									$arrG_N[]=ValutaArrowString($arrG[$i]);
								}

								$arrX_P=array();
								$arrX_N=array();
								for ($i=0;$i<count($arrX);++$i) {
									$arrX_P[]=DecodeFromLetter($arrX[$i]);
									//$arrX_N[]=$LetterPoint[$arrX[$i]]['N'];
									$arrX_N[]=ValutaArrowString($arrX[$i]);
								}

							// inclusione
								if (array_intersect($arrX_P,$arrG_P)!==array() || array_intersect($arrX_N,$arrG_N)!==array()) {
									if ($Value>$Row->Gold) {
                                        $Errore = 1;
                                    }
								} else {
									if (($Row->Score - ($Row->Gold*$minG) - ($Value*$minX))<0) {
                                        $Errore = 1;
                                    }
								}
							}
						} else {
                            $Errore = 1;
                        }
					}

				}
			} else {
                $Errore = 1;
            }


			if ($Errore==0) {
			    if($OldValue!=$Value) {
                    // scrivo il dato e aggiorno i totali
                    $Update
                        = "UPDATE Qualifications SET "
                        . $Cosa . "=" . StrSafe_DB($Value) . ", "
                        . "QuConfirm = QuConfirm & (255-" . pow(2, intval(substr($Cosa, 3, 1))) . "), "
                        . "QuScore=QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score+QuD8Score,"
                        . "QuGold=QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold,"
                        . "QuXnine=QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine, "
                        . "QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits, "
                        . "QuTimestamp=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
                        . "WHERE QuId=" . StrSafe_DB($Atleta);
                    $RsUp = safe_w_sql($Update);
                    //print $Update;exit;
                    if (safe_w_affected_rows() == 1 AND $OldValue != $Value) {

                        // distruggo e ricreo le eliminatorie
                        // scopro in che evento elim si trova la divcl del tipo
                        $q = "SELECT EvCode
                            FROM Individuals 
                            INNER JOIN Events on EvCode=IndEvent and EvTournament=IndTournament and EvTeamEvent=0 AND (EvElim1+EvElim2)>0
                            WHERE IndId={$Atleta}";
                        $r = safe_r_sql($q);
                        //print $q;exit;
                        if ($r && safe_num_rows($r) > 0) {
                            while ($row = safe_fetch($r)) {
                                $ev = $row->EvCode;
                                for ($j = 1; $j <= 2; ++$j) {
                                    ResetElimRows($ev, $j);
                                }
                            }
                        }

                        // azzero gli shootoff
                        $q = " SELECT DISTINCT EvCode,EvTeamEvent
                            FROM Events
                            INNER JOIN EventClass ON EvCode=EcCode AND if(EvTeamEvent=0, EcTeamEvent=0, EcTeamEvent>0) AND EcTournament={$_SESSION['TourId']}
                            INNER JOIN Entries ON EcDivision=EnDivision AND EcClass=EnClass and if(EcSubClass='', true, EcSubClass=EnSubClass) AND EnId={$Atleta}
                            WHERE (EvTeamEvent='0' AND EnIndFEvent='1') OR (EvTeamEvent='1' AND EnTeamFEvent+EnTeamMixEvent>0) AND EvTournament={$_SESSION['TourId']}";
                        //print $q;
                        $Rs = safe_r_sql($q);
                        if ($Rs && safe_num_rows($Rs) > 0) {
                            while ($row = safe_fetch($Rs)) {
                                ResetShootoff($row->EvCode, $row->EvTeamEvent, 0);
                            }
                        }
                    }
                }

			// estraggo i totali
				$Select = "SELECT QuId,QuScore,QuGold,QuXnine, {$Cosa} 
					FROM Qualifications WHERE QuId=" . StrSafe_DB($Atleta);
				$Rs=safe_r_sql($Select);
				//print $Select;exit;
				$Errore = 0;	// no error
				$MyRow=NULL;
				if (safe_num_rows($Rs)==1) {
					$MyRow=safe_fetch($Rs);

				// se il valore del campo passato � uguale a quello in db ok, altrimenti errore
					if ($Value!=$MyRow->{$Cosa}) {
						$Errore=1;
					}
				} else {
					$Errore=1;
				}
			}

			if ($OldValue!=$Value AND !isset($_REQUEST["NoRecalc"])) {
				if ($Errore==0) {

				// Se non ho errori, calcolo la rank della distanza per l'evento
					$Distanza = array();
					preg_match("/[1-8]/", $Cosa, $Distanza);

					$Select = "SELECT CONCAT(EnDivision,EnClass) AS MyEvent, EnCountry as MyTeam, EnDivision, EnClass "
						. "FROM Entries "
						. "WHERE EnId=" . StrSafe_DB($Atleta) . " AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$Rs=safe_r_sql($Select);

					if (safe_num_rows($Rs)==1) {
						$rr=safe_fetch($Rs);
						$Evento=$rr->MyEvent;
						$Category = $rr->MyEvent;
						$Societa = $rr->MyTeam;
						$Div = $rr->EnDivision;
						$Cl = $rr->EnClass;

						if (CalcQualRank($Distanza[0],$Evento)) {
							$Errore=1;
							//print 'errore CalcQualRank distanza';exit;
						}
					} else {
                        $Errore = 1;
                    }
				}

				if ($Errore==0) {
				// se non ho errori calcolo la rank globale per l'evento
					if (CalcQualRank(0,$Evento)) {
						$Errore=1;
					}
				}

			// eventi di cui calcolare le rank assolute
				$events4abs=array();
				$q="SELECT distinct IndEvent from Individuals where IndId='$Atleta' and IndTournament={$_SESSION['TourId']}";

				$r=safe_r_sql($q);

				if ($r) {
					while ($tmp=safe_fetch($r)) {
						$events4abs[]=$tmp->IndEvent;
					}
				} else {
                    $Errore = 1;
                }


			// rank abs di distanza
				if ($Errore==0) {
					if (count($events4abs)>0) {
						if (!Obj_RankFactory::create('Abs',array('events'=>$events4abs,'dist'=>$Distanza[0]))->calculate()) {
							$Errore=1;
						}
					}
				}

				if ($Errore==0) {
					if (count($events4abs)>0) {
						if (!Obj_RankFactory::create('Abs',array('events'=>$events4abs,'dist'=>0))->calculate()) {
							$Errore=1;
						}
					}
				}

				if ($Errore==0) {
				// se non ho errori calcolo la rank globale per l'evento
					if (MakeTeams($Societa, $Evento)) {
                        $Errore = 1;
                    }
				}

				if ($Errore==0) {
				// se non ho errori calcolo la rank globale per l'evento
					if (MakeTeamsAbs($Societa,$Div,$Cl)) {
                        $Errore = 1;
                    }
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
