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

	if (!CheckTourSession() || !isset($_REQUEST['Id']) || !is_numeric($_REQUEST['Id']) || !isset($_REQUEST['Index']) || !is_numeric($_REQUEST['Index']) || !isset($_REQUEST['Dist']) || !is_numeric($_REQUEST['Dist']) || !isset($_REQUEST['Point'])) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclQualification, AclReadWrite, false);

	$Errore=0;
	$MakeTeams=1;

	$MaxArrows=0;	// num massimo di frecce
	$ArrowString = '';	// arrowstring da scrivere
	$G = '';			// stringa che rappresenta i Gold
	$X = '';			// stringa che rappresenta le X
	$ScoreForX=10; // default value for an X

	$OldValue = 0;	// Vecchio valore $Cur...

	$CurScore=0;
	$CurGold=0;
	$CurXNine=0;
	$Score=0;
	$Gold=0;
	$Xnine=0;
	$CurEndScore=0;

	$Updated=0;

// Vars per rank e teams
	$Evento = '';
	$Category='';
	$Societa='';
	$Div="";
	$Cl="";

	if(empty($PageOutput)) $PageOutput='XML';

	if (!IsBlocked(BIT_BLOCK_QUAL))	{
	// Estraggo il num max di frecce e gli altri parametri
		/*$Select
			= "SELECT TtGolds,TtXNine,(TtMaxDistScore/TtGolds) AS MaxArrows "
			. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
			. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/

		$Select	= "SELECT ToType, ToGoldsChars,ToXNineChars,(ToMaxDistScore/ToGolds) AS MaxArrows FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

		$Rs=safe_r_sql($Select);

		if (safe_num_rows($Rs)!=1) {
            $Errore = 1;
        } else {
			$MyRow=safe_fetch($Rs);
			$MakeTeams=($MyRow->ToType!=14); // Vegas style does not have teams!!!
			$MaxArrows=$MyRow->MaxArrows;
			$G=$MyRow->ToGoldsChars;
			$X=$MyRow->ToXNineChars;
		// Estraggo l'arrowstring
			$Select = "SELECT QuD" . $_REQUEST['Dist'] . "ArrowString AS ArrowString, DiEnds*DiArrows as MaxArrows, DiArrows 
				FROM Qualifications
				LEFT JOIN DistanceInformation on DiTournament={$_SESSION['TourId']} and QuSession=DiSession and DiDistance={$_REQUEST['Dist']} and DiType='Q'
				WHERE QuId=" . StrSafe_DB($_REQUEST['Id']) . " ";
			$Rs=safe_r_sql($Select);

			if (safe_num_rows($Rs)!=1) {
                $Errore = 1;
            } else {
				$MyRow=safe_fetch($Rs);
				if($MyRow->MaxArrows) {
					$MaxArrows=$MyRow->MaxArrows;
				}
				$ArrowString=str_pad($MyRow->ArrowString,$MaxArrows,' ',STR_PAD_RIGHT);
				$xx=GetLetterFromPrint($_REQUEST['Point'],$_REQUEST['Id'],$_REQUEST['Dist']);

				// establish the score value for an X
				$tmp=GetLetterFromPrint('X', $_REQUEST['Id'], $_REQUEST['Dist']);
				$ScoreForX=ValutaArrowString($tmp);

				$Value2Write = ($_REQUEST['Point']!='' ? ($xx!=' ' ? $xx : '') : ' ');

				if ($Value2Write=='') {
                    $Errore = 1;
                } else {
					$MustUpdateZeroValue=($Value2Write=='A' and $ArrowString[$_REQUEST['Index']]!=$Value2Write);
					$ArrowString[$_REQUEST['Index']]=$Value2Write;
					$CurEndScore=ValutaArrowString(substr($ArrowString, intval($_REQUEST['Index']/$MyRow->DiArrows)*$MyRow->DiArrows, $MyRow->DiArrows));

				// Ricalcolo i totali della distanza usando $ArrowString
                    list($CurScore,$CurGold,$CurXNine) = ValutaArrowStringGX($ArrowString,$G,$X);

				// Estraggo il vecchio valore
					$Select
						= "SELECT QuD" . $_REQUEST['Dist'] . "Score AS OldScore, QuD" . $_REQUEST['Dist'] . "Gold AS OldGold, QuD" . $_REQUEST['Dist'] . "Xnine AS OldXnine, QuD" . $_REQUEST['Dist'] . "Hits AS OldHits  "
						. "FROM Qualifications "
						. "WHERE QuId=" . StrSafe_DB($_REQUEST['Id']) . " ";
					$Rs=safe_r_sql($Select);

					if (safe_num_rows($Rs)!=1) {
                        $Errore = 1;
                    } else {
						$MyRow=safe_fetch($Rs);
						$OldValue=$MyRow->OldScore;
                        $OldGold=$MyRow->OldGold;
                        $OldXNine=$MyRow->OldXnine;
                        $OldHits=$MyRow->OldHits;

                        if($MustUpdateZeroValue or $OldValue != $CurScore OR $OldGold != $CurGold OR $OldXNine != $CurXNine OR $OldHits != strlen(rtrim($ArrowString))) {
					// Aggiorno i totali della distanza
                            $Update
                                = "UPDATE Qualifications SET "
                                . "QuD" . $_REQUEST['Dist'] . "ArrowString=" . StrSafe_DB($ArrowString) . ","
                                . "QuD" . $_REQUEST['Dist'] . "Score=" . StrSafe_DB($CurScore) . ", "
                                . "QuD" . $_REQUEST['Dist'] . "Gold=" . StrSafe_DB($CurGold) . ", "
                                . "QuD" . $_REQUEST['Dist'] . "Xnine=" . StrSafe_DB($CurXNine) . ", "
                                . "QuD" . $_REQUEST['Dist'] . "Hits=" . StrSafe_DB(strlen(rtrim($ArrowString))) . ", "
                                . "QuConfirm = QuConfirm & (255-".pow(2, intval($_REQUEST['Dist'])) ."), "
                                . "QuScore=QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score+QuD8Score,"
                                . "QuGold=QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold,"
                                . "QuXnine=QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine, "
                                . "QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits, "
                                . "QuTimestamp=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
                                . "WHERE QuId=" . StrSafe_DB($_REQUEST['Id']) . " ";
                            $RsUp=safe_w_sql($Update);
                            $Updated=safe_w_affected_rows();

                            if(!$MustUpdateZeroValue) {
		                        runJack("QualArrUpdate", $_SESSION['TourId'], array("Dist"=>$_REQUEST['Dist'] ,"Index"=>$_REQUEST['Index'] ,"Id"=>$_REQUEST['Id'] ,"Point"=>$_REQUEST['Point'] ,"TourId"=>$_SESSION['TourId']));

		                        if($PageOutput!='JSON' or !$BlockApi) {
	                                if (safe_w_affected_rows() == 1 && $OldValue != $CurScore) {

	                                    $q = "SELECT DISTINCT EvCode,EvTeamEvent
	                                        FROM Events
	                                        INNER JOIN EventClass ON EvCode=EcCode AND if(EvTeamEvent=0, EcTeamEvent=0, EcTeamEvent>0) AND EcTournament={$_SESSION['TourId']}
	                                        INNER JOIN Entries ON EcDivision=EnDivision AND EcClass=EnClass and if(EcSubClass='', true, EcSubClass=EnSubClass) AND EnId={$_REQUEST['Id']}
	                                        WHERE (EvTeamEvent='0' AND EnIndFEvent='1') OR (EvTeamEvent='1' AND EnTeamFEvent+EnTeamMixEvent>0) AND EvTournament={$_SESSION['TourId']}
	                                    ";
	                                    //print $q;exit;
	                                    $Rs = safe_r_sql($q);

	                                    if (safe_num_rows($Rs) > 0) {
	                                        while ($row = safe_fetch($Rs)) {
	                                            ResetShootoff($row->EvCode, $row->EvTeamEvent, 0);
	                                        }
	                                    }
	                                }

	                                if (!isset($_REQUEST["NoRecalc"])) {
	                                    // Calcolo la rank della distanza per l'evento
	                                    $Evento = '*#*#';

	                                    $Select
	                                        = "SELECT CONCAT(EnDivision,EnClass) AS MyEvent, EnCountry as MyTeam,EnDivision,EnClass "
	                                        . "FROM Entries "
	                                        . "WHERE EnId=" . StrSafe_DB($_REQUEST['Id']) . " AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
	                                    $Rs = safe_r_sql($Select);

	                                    if (safe_num_rows($Rs) == 1) {
	                                        $rr = safe_fetch($Rs);
	                                        $Evento = $rr->MyEvent;
	                                        $Category = $rr->MyEvent;
	                                        $Societa = $rr->MyTeam;
	                                        $Div = $rr->EnDivision;
	                                        $Cl = $rr->EnClass;

	                                        if (CalcQualRank($_REQUEST['Dist'], $Evento))
	                                            $Errore = 1;
	                                    } else
	                                        $Errore = 1;


	                                    if ($Errore == 0) {
	                                        if (debug) print $Evento . '<br>';
	                                        // se non ho errori calcolo la rank globale per l'evento
	                                        if (CalcQualRank(0, $Evento))
	                                            $Errore = 1;
	                                    }

	                                    // eventi di cui calcolare le rank assolute
	                                    $events4abs = array();
	                                    $q = "SELECT distinct IndEvent FROM Individuals WHERE IndTournament={$_SESSION['TourId']} AND IndId=" . StrSafe_DB($_REQUEST['Id']);
	                                    $r = safe_r_sql($q);

	                                    if ($r) {
	                                        while ($tmp = safe_fetch($r)) {
	                                            $events4abs[] = $tmp->IndEvent;
	                                        }
	                                    } else {
	                                        $Errore = 1;
	                                    }

	                                    // nuovo by simo
	                                    // rank abs di distanza
	                                    if ($Errore == 0 and $events4abs) {
	                                        if (!Obj_RankFactory::create('Abs', array('events' => $events4abs, 'dist' => $_REQUEST['Dist']))->calculate()) {
	                                            $Errore = 1;
	                                        }
	                                    }

	                                    // nuovo by simo
	                                    // rank abs totale
	                                    if ($Errore == 0 and $events4abs) {
	                                        if (!Obj_RankFactory::create('Abs', array('events' => $events4abs, 'dist' => 0))->calculate()) {
	                                            $Errore = 1;
	                                        }
	                                    }

	                                    if($MakeTeams) {

		                                    if ($Errore == 0) {
		                                        // se non ho errori calcolo le squadre
		                                        if (MakeTeams($Societa, $Category))
		                                            $Errore = 1;
		                                    }

		                                    if ($Errore == 0) {
		                                        // se non ho errori calcolo le squadre assolute
		                                        if (MakeTeamsAbs($Societa, $Div, $Cl))
		                                            $Errore = 1;
		                                    }
	                                    }

	                                }
	                            }
                            }
						}
                        // tiro fuori lo score totale
                        $Select
                            = "SELECT QuScore, QuGold, QuXnine "
                            . "FROM Qualifications "
                            . "WHERE QuId=" . StrSafe_DB($_REQUEST['Id']) . " ";

                        $Rs = safe_r_sql($Select);

                        if (safe_num_rows($Rs) == 1) {
                            $MyRow = safe_fetch($Rs);
                            $Score = $MyRow->QuScore;
                            $Gold = $MyRow->QuGold;
                            $Xnine = $MyRow->QuXnine;
                        }
					}
				}
			}
		}
	} else {
        $Errore = 1;
    }

	switch($PageOutput) {
		case 'XML':
		// produco l'xml di ritorno
			if (!debug)
				header('Content-Type: text/xml');

			print '<response>';
			print '<error>' . $Errore . '</error>';
			print '<id>' . $_REQUEST['Id'] . '</id>';
			print '<dist>' . $_REQUEST['Dist'] . '</dist>';
			print '<index>' . $_REQUEST['Index'] . '</index>';
			print '<curendscore>' . $CurEndScore . '</curendscore>';
			print '<curscore>' . $CurScore . '</curscore>';
			print '<curgold>' . $CurGold . '</curgold>';
			print '<curxnine>' . $CurXNine . '</curxnine>';
			print '<score>' . $Score . '</score>';
			print '<gold>' . $Gold . '</gold>';
			print '<xnine>' . $Xnine . '</xnine>';
			print '<xvalue>' . $ScoreForX . '</xvalue>';
			print '<updated>' . $Updated . '</updated>';
			print '</response>';
			break;
		case 'JSON':
			$JsonResult=array();
			$JsonResult['error']      = $Errore;
			$JsonResult['qutarget']   = $_REQUEST['qutarget'];
			$JsonResult['distnum']    = $_REQUEST['distnum'] ;
			$JsonResult['arrowindex'] = $_REQUEST['arrowindex'] ;
			$JsonResult['arrowsymbol']= $Value2Write ? strtoupper($_REQUEST['Point']) : '';
			$JsonResult['curendscore']= $CurEndScore;
			$JsonResult['curscore']   = $CurScore ;
			$JsonResult['curgold']    = $CurGold ;
			$JsonResult['curxnine']   = $CurXNine;
			$JsonResult['score']      = $Score ;
			$JsonResult['gold']       = $Gold ;
			$JsonResult['xnine']      = $Xnine;
			$JsonResult['xvalue']     = $ScoreForX;
			$JsonResult['updated']     = $Updated;
			break;
	}

