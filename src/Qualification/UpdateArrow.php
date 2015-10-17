<?php
/*
													- UpdateArrow.php -
	Aggiorna la freccia di una data arrowstring in Qualifications
*/

	define('debug',false);

	if(!isset($BlockApi)) $BlockApi=false;

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['Id']) || !is_numeric($_REQUEST['Id']) ||
		!isset($_REQUEST['Index']) || !is_numeric($_REQUEST['Index']) ||
		!isset($_REQUEST['Dist']) || !is_numeric($_REQUEST['Dist']) ||
		!isset($_REQUEST['Point']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	$MaxArrows=0;	// num massimo di frecce
	$ArrowString = '';	// arrowstring da scrivere
	$G = '';			// stringa che rappresenta i Gold
	$X = '';			// stringa che rappresenta le X

	$OldValue = 0;	// Vecchio valore $Cur...

	$CurScore=0;
	$CurGold=0;
	$CurXNine=0;
	$Score=0;
	$Gold=0;
	$Xnine=0;

// Vars per rank e teams
	$Evento = '';
	$Category='';
	$Societa='';
	$Div="";
	$Cl="";

	if(empty($PageOutput)) $PageOutput='XML';

	if (!IsBlocked(BIT_BLOCK_QUAL))
	{
	// Estraggo il num max di frecce e gli altri parametri
		/*$Select
			= "SELECT TtGolds,TtXNine,(TtMaxDistScore/TtGolds) AS MaxArrows "
			. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
			. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/

		$Select
			= "SELECT ToGoldsChars,ToXNineChars,(ToMaxDistScore/ToGolds) AS MaxArrows "
			. "FROM Tournament "
			. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

		$Rs=safe_r_sql($Select);

		if (safe_num_rows($Rs)==1)
		{
			$MyRow=safe_fetch($Rs);
			$MaxArrows=$MyRow->MaxArrows;
			$G=$MyRow->ToGoldsChars;
			$X=$MyRow->ToXNineChars;
		}
		else
			$Errore=1;

		if ($Errore==0)
		{
		// Estraggo l'arrowstring

			$Select
				= "SELECT QuD" . $_REQUEST['Dist'] . "ArrowString AS ArrowString, DiEnds*DiArrows as MaxArrows "
				. "FROM Qualifications
					left join DistanceInformation on DiTournament={$_SESSION['TourId']} and QuSession=DiSession and DiDistance={$_REQUEST['Dist']} and DiType='Q'"
				. " WHERE QuId=" . StrSafe_DB($_REQUEST['Id']) . " ";
			$Rs=safe_r_sql($Select);

			if (!$Rs || safe_num_rows($Rs)!=1)
				$Errore=1;

			if ($Errore==0)
			{
				$MyRow=safe_fetch($Rs);
				if($MyRow->MaxArrows) {
					$MaxArrows=$MyRow->MaxArrows;
				}

				$ArrowString=str_pad($MyRow->ArrowString,$MaxArrows,' ',STR_PAD_RIGHT);

				if (debug) print '<pre>...' .  $ArrowString . '...</pre><br>';

				//$Value2Write = ($_REQUEST['Point']!='' ? GetLetterFromPrint($_REQUEST['Point']) : ' ');
				$xx=GetLetterFromPrint($_REQUEST['Point'],$_REQUEST['Id'],$_REQUEST['Dist']);
				$Value2Write = ($_REQUEST['Point']!='' ? ($xx!=' ' ? $xx : '') : ' ');

				if ($Value2Write!='')
				{
					$ArrowString[$_REQUEST['Index']]=$Value2Write;
				/*
				 *  se la lettera non è tra quelle buone x il bersaglio in uso ho un errore (ma se ho lo spazio no
				 *  perchè spazio vuol dire casella vuota).
				 *  Adesso questa roba la fa GetLetterFromPrint usando gli ultimi due parametri
				 */
					//print $Value2Write.'<br>';
//					if ($Value2Write==' ' || in_array($Value2Write,GetGoodLettersFromDist($_REQUEST['Id'],$_REQUEST['Dist'])))
//					{
//						$ArrowString[$_REQUEST['Index']]=$Value2Write;
//					}
//					else
//					{
//						$Errore=1;
//					}
				}
				else
					$Errore=1;

				if (debug) print '<pre>...' . $ArrowString . '...</pre><br>';

				if ($Errore==0)
				{
				// Ricalcolo i totali della distanza usando $ArrowString
					list($CurScore,$CurGold,$CurXNine) = ValutaArrowStringGX($ArrowString,$G,$X);

					if (debug) print $Score . '<br>';

				// Estraggo il vecchio valore
					$Select
						= "SELECT QuD" . $_REQUEST['Dist'] . "Score AS OldScore "
						. "FROM Qualifications "
						. "WHERE QuId=" . StrSafe_DB($_REQUEST['Id']) . " ";
					$Rs=safe_r_sql($Select);

					if (safe_num_rows($Rs)==1)
					{
						$MyRow=safe_fetch($Rs);
						$OldValue=$MyRow->OldScore;
					}
					else
						$Errore=1;

					if ($Errore==0)
					{
					// Aggiorno i totali della distanza
						$Update
							= "UPDATE Qualifications SET "
							. "QuD" . $_REQUEST['Dist'] . "ArrowString=" . StrSafe_DB($ArrowString) . ","
							. "QuD" . $_REQUEST['Dist'] . "Score=" . StrSafe_DB($CurScore) . ", "
							. "QuD" . $_REQUEST['Dist'] . "Gold=" . StrSafe_DB($CurGold) . ", "
							. "QuD" . $_REQUEST['Dist'] . "Xnine=" . StrSafe_DB($CurXNine) . ", "
							. "QuD" . $_REQUEST['Dist'] . "Hits=" . StrSafe_DB(strlen(rtrim($ArrowString))) . ", "
							. "QuScore=QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score+QuD8Score,"
							. "QuGold=QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold,"
							. "QuXnine=QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine, "
							. "QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits, "
							. "QuTimestamp=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
							. "WHERE QuId=" . StrSafe_DB($_REQUEST['Id']) . " ";
						$RsUp=safe_w_sql($Update);
						if (debug)
							print $Update . '<br>';

						if($PageOutput!='JSON' or !$BlockApi) {
							if (safe_w_affected_rows()==1 && $OldValue!=$CurScore) {
							// Resetto il flag per gli spareggi
								/*$Update
									= "UPDATE Tournament SET "
									. "ToMadeIndShootOff='0' "
									. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
								$Rs=safe_w_sql($Update);*/

							// azzero gli shootoff
								/*$Update
									= "UPDATE Events INNER JOIN EventClass ON EvCode=EcCode AND (EvTeamEvent='0' OR EvTeamEvent='1') AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
									. "INNER JOIN Entries ON EcDivision=EnDivision AND EcClass=EnClass  AND EnId=" . StrSafe_DB($_REQUEST['Id']) . " "
									. "SET EvShootOff='0' "
									. "WHERE (EvTeamEvent='0' AND EnIndFEvent='1') OR (EvTeamEvent='1' AND EnTeamFEvent='1') AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
								$Rs=safe_w_sql($Update);
								set_qual_session_flags();*/

								$q="
									SELECT DISTINCT EvCode,EvTeamEvent
									FROM
										Events
										INNER JOIN
											EventClass
										ON EvCode=EcCode AND (EvTeamEvent='0' OR EvTeamEvent='1') AND EcTournament={$_SESSION['TourId']}
										INNER JOIN
											Entries
										ON TRIM(EcDivision)=TRIM(EnDivision) AND TRIM(EcClass)=TRIM(EnClass)  AND EnId={$_REQUEST['Id']}
									WHERE
										 (EvTeamEvent='0' AND EnIndFEvent='1') OR (EvTeamEvent='1' AND EnTeamFEvent='1') AND EvTournament={$_SESSION['TourId']}
								";
								//print $q;exit;
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


							if ($RsUp) {
							// tiro fuori lo score totale
								$Select
									= "SELECT QuScore, QuGold, QuXnine "
									. "FROM Qualifications "
									. "WHERE QuId=" . StrSafe_DB($_REQUEST['Id']) . " ";

								if (debug) print $Select . '<br>';
								$Rs=safe_r_sql($Select);

								if (safe_num_rows($Rs)==1)
								{
									$MyRow = safe_fetch($Rs);
									$Score = $MyRow->QuScore;
									$Gold = $MyRow->QuGold;
									$Xnine = $MyRow->QuXnine;
								}
								if(!isset($_REQUEST["NoRecalc"]))
								{
								// Calcolo la rank della distanza per l'evento
									$Evento = '*#*#';

									$Select
										= "SELECT CONCAT(EnDivision,EnClass) AS MyEvent, EnCountry as MyTeam,EnDivision,EnClass "
										. "FROM Entries "
										. "WHERE EnId=" . StrSafe_DB($_REQUEST['Id']) . " AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
									$Rs=safe_r_sql($Select);

									if (safe_num_rows($Rs)==1)
									{
										$rr=safe_fetch($Rs);
										$Evento=$rr->MyEvent;
										$Category = $rr->MyEvent;
										$Societa = $rr->MyTeam;
										$Div = $rr->EnDivision;
										$Cl = $rr->EnClass;

										if (CalcQualRank($_REQUEST['Dist'],$Evento))
											$Errore=1;
									}
									else
										$Errore=1;


									if ($Errore==0)
									{
										if (debug) print $Evento . '<br>';
									// se non ho errori calcolo la rank globale per l'evento
										if (CalcQualRank(0,$Evento))
											$Errore=1;
									}

								// eventi di cui calcolare le rank assolute
									$events4abs=array();
									$q="SELECT EcCode FROM EventClass WHERE EcTournament={$_SESSION['TourId']} AND EcTeamEvent=0 AND EcDivision='" . $Div . "' AND EcClass='" . $Cl. "' ";
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

								// nuovo by simo
								// rank abs di distanza
									if ($Errore==0)
									{
										if (debug)
											print 'Faccio la rank abs di distanza<br>';

										if (count($events4abs)>0)
										{
											if (!Obj_RankFactory::create('Abs',array('events'=>$events4abs,'dist'=>$_REQUEST['Dist']))->calculate())
												$Errore=1;
										}
									}

								// nuovo by simo
								// rank abs totale
									if ($Errore==0)
									{
										if (debug)
											print 'Faccio la rank abs totale<br>';

										if (count($events4abs)>0)
										{
											if (!Obj_RankFactory::create('Abs',array('events'=>$events4abs,'dist'=>0))->calculate())
												$Errore=1;
										}
									}

									if ($Errore==0)
									{
									// se non ho errori calcolo le squadre
										if (MakeTeams($Societa, $Category))
											$Errore=1;
									}

									if ($Errore==0)
									{
									// se non ho errori calcolo le squadre assolute
										if (MakeTeamsAbs($Societa,$Div,$Cl))
											$Errore=1;
									}

								}
							} else {
								$Errore=1;
							}
						}
					}
				}
			}
		}
	}
	else
		$Errore=1;

	switch($PageOutput) {
		case 'XML':
		// produco l'xml di ritorno
			if (!debug)
				header('Content-Type: text/xml');

			print '<response>' . "\n";
			print '<error>' . $Errore . '</error>' . "\n";
			print '<id>' . $_REQUEST['Id'] . '</id>' . "\n";
			print '<dist>' . $_REQUEST['Dist'] . '</dist>' . "\n";
			print '<index>' . $_REQUEST['Index'] . '</index>' . "\n";
			print '<curscore>' . $CurScore . '</curscore>' . "\n";
			print '<curgold>' . $CurGold . '</curgold>' . "\n";
			print '<curxnine>' . $CurXNine . '</curxnine>' . "\n";
			print '<score>' . $Score . '</score>' . "\n";
			print '<gold>' . $Gold . '</gold>' . "\n";
			print '<xnine>' . $Xnine . '</xnine>' . "\n";
			print '</response>' . "\n";
			break;
		case 'JSON':
			$JsonResult=array();
			$JsonResult['error']      = $Errore;
			$JsonResult['qutarget']   = $_REQUEST['qutarget'];
			$JsonResult['distnum']    = $_REQUEST['distnum'] ;
			$JsonResult['arrowindex'] = $_REQUEST['arrowindex'] ;
			$JsonResult['arrowsymbol']= $Value2Write ? strtoupper($_REQUEST['Point']) : '';
			$JsonResult['curscore']   = $CurScore ;
			$JsonResult['curgold']    = $CurGold ;
			$JsonResult['curxnine']   = $CurXNine;
			$JsonResult['score']      = $Score ;
			$JsonResult['gold']       = $Gold ;
			$JsonResult['xnine']      = $Xnine;
			break;
	}

