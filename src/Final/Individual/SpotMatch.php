<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	if (!CheckTourSession() || !isset($_REQUEST['Event']) || !isset($_REQUEST['MatchNo'])) printcrackerror();

	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_Phases.inc.php');
	require_once('Final/Fun_MatchTotal.inc.php');

//Gestione Volee & Frecce
	$nEND=5;	// Numero di Volee
	$nARR=3;	// Numero di frecce

// Nomi delle textbox dei punti a sinistra e a destra
	$TxtPuntiSx = array("","","");
	$TxtPuntiDx = array("","","");
	$StrPuntiSx="";
	$StrPuntiDx="";

// Vettore dei punti (bersaglio)
	$MySym = ${GetTargetType($_REQUEST['Event'],0)};

	//print GetTargetType($_REQUEST['Event']);exit;
	$MyTargetComplete = TargetIsComplete(GetTargetType($_REQUEST['Event']));
	$MyTargetHitMiss = strstr(GetTargetType($_REQUEST['Event'],0), 'TrgHM');
	$MyTargetField = strstr(GetTargetType($_REQUEST['Event'],0), 'TrgField');

	$MyTargetSize = 0;
	$MyTargetSize = ($MyTargetComplete ? 100 : 200);

/*
	if (debug)
	{
		print_r($_REQUEST);
		print "<br>";
		print GetTargetType($_REQUEST['Event']) . '<br>';
		print ($MyTargetComplete ? 'Completo' : 'Non completo') . '<br>';
	}
*/
/***********************
 MatchNo dell'incontro
************************/
	$MatchNo=(isset($_REQUEST['MatchNo']) ? $_REQUEST['MatchNo'] : NULL);
	$Event=(isset($_REQUEST['Event']) ? $_REQUEST['Event'] : NULL);
	$MatchSx = ($MatchNo % 2 == 0 ? $MatchNo : $MatchNo-1);
	$MatchDx = $MatchSx + 1;


/*********************************
  Gestione delle frecce in INPUT
*********************************/
	if (!IsBlocked(BIT_BLOCK_IND))
	{
	// Scrittura nel db
		if ((isset($_REQUEST['Command']) && $_REQUEST['Command']=='OK') ||
			(isset($_REQUEST["TargetSx_x"]) && isset($_REQUEST["TargetSx_y"])) ||
			(isset($_REQUEST["TargetDx_x"]) && isset($_REQUEST["TargetDx_y"])))
		{
		// Estraggo le arrowstring coinvolte
			$Select
				= "SELECT f.FinMatchNo AS MatchNo, f2.FinMatchNo AS OppMatchNo, "
				. "f.FinArrowPosition AS ArrPos, f.FinTiePosition AS TiePos, "
				. "f2.FinArrowPosition AS OppArrPos, f2.FinTiePosition AS OppTiePos, "
				. "EvCode as Event, GrPhase as Phase, EvMatchMode as MatchMode, EvMatchArrowsNo "
				. "FROM Events  "
				. "INNER JOIN Finals AS f ON EvTournament=f.FinTournament AND EvCode=f.FinEvent  "
				. "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament "
				. "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo "
				. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($_REQUEST['Event']) . " AND EvTeamEvent='0'  AND f.FinMatchNo=" . StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1));
			$Rs=safe_r_sql($Select);
			$MyRow=safe_fetch($Rs);

			$obj=getEventArrowsParams($MyRow->Event,$MyRow->Phase,0);


			$nEND=$obj->ends;
			$nARR=$obj->arrows;
			$maxArrows=$nEND*$nARR;
			$nTieBreak=$obj->so;

			$ArrStringSx=str_repeat(' ', ($_REQUEST["Volee"]=='T' ? $nTieBreak : $nARR));
			$ArrStringDx=str_repeat(' ', ($_REQUEST["Volee"]=='T' ? $nTieBreak : $nARR));
			$ArrowPositionSx = explode('|',str_pad($MyRow->{($_REQUEST["Volee"]=='T' ? "Tie" : "Arr") . "Pos"},($_REQUEST["Volee"]=='T' ? 6 : 18)-substr_count($MyRow->{($_REQUEST["Volee"]=='T' ? "Tie" : "Arr") . "Pos"}, "|"),"|"));
			$ArrowPositionDx = explode('|',str_pad($MyRow->{"Opp" . ($_REQUEST["Volee"]=='T' ? "Tie" : "Arr") . "Pos"},($_REQUEST["Volee"]=='T' ? 6 : 18)-substr_count($MyRow->{"Opp" . ($_REQUEST["Volee"]=='T' ? "Tie" : "Arr") . "Pos"}, "|"),"|"));
			$startArrowString=-1;
			$startArrowPos=($_REQUEST["Volee"]!='T' ? (($_REQUEST["Volee"]-1)*$nARR) : 0);


			foreach($_REQUEST as $Key => $Value)
			{
				//Scorro tutti i campi di punteggio
				if (preg_match("/M_[0-9]+_([0-4]|t)_[0-5]/i",$Key))
				{
					$m=-1;	// MatchNo
					$v=-1;	// Volee. E' la riga della matrice degli score. T significa TieBreak
					$f=-1;	// Freccia. E' la colonna della matrice degli score
					list(,$m,$v,$f)=explode('_',$Key);	// Estraggo le parti del punteggio
					//Cerco se il punteggio inserito è valido per il bersaglio scelto
					$arrKey = GetLetterFromPrint($Value);
					//Carico l'arrowstring
					if($m % 2 == 0)
						$ArrStringSx = substr_replace($ArrStringSx,$arrKey,$f,1);
					else
						$ArrStringDx = substr_replace($ArrStringDx,$arrKey,$f,1);


					//Vuoto l'array posizione per tutti i "Vuoti"
					if($arrKey==' ')
					{
						${"ArrowPosition". ($m % 2 == 0 ? "Sx":"Dx")}[$startArrowPos+$f] = "";
					}
					//Calcolo la prima freccia per l'update
					$startArrowString = (($v!='t' && $v!='T') ? ($v*$nARR) : $maxArrows)+1;
				}
			}
			//Valuto l'impatto della freccia sul bersaglio
			$TmpX=0;
			$TmpY=0;
			$Side="";
			if(isset($_REQUEST["TargetSx_x"]) && isset($_REQUEST["TargetSx_y"]) && ($_REQUEST["TargetSx_x"]+$_REQUEST["TargetSx_y"])!=0)
			{
				$TmpX=(int) (((150-$_REQUEST["TargetSx_x"])*1000/150));
				$TmpY=(int) (((150-$_REQUEST["TargetSx_y"])*1000/150));
				$Side="Sx";
			}
			if(isset($_REQUEST["TargetDx_x"]) && isset($_REQUEST["TargetDx_y"]) && ($_REQUEST["TargetDx_x"]+$_REQUEST["TargetDx_y"])!=0)
			{
				$TmpX=(int) (((150-$_REQUEST["TargetDx_x"])*1000/150));
				$TmpY=(int) (((150-$_REQUEST["TargetDx_y"])*1000/150));
				$Side="Dx";
			}
			if($Side!="")
			{
				$Coords="$TmpX,$TmpY";
				$Value=10-((int) ((sqrt(($TmpX*$TmpX)+($TmpY*$TmpY)))/$MyTargetSize));
				if($Value<=0)
					$Value='M';
			// Distinguo il 10 dall'X
				if($Value==10 && sqrt(($TmpX*$TmpX)+($TmpY*$TmpY))<=($MyTargetSize/2))
					$Value="X";
				$arrKey = GetLetterFromSearch($Value,$MySym);
			//Trovo il primo libero
				$firstFree=strpos(${"ArrString". $Side}," ");
				if($firstFree !== false)
				{
					${"ArrString". $Side} = substr_replace(${"ArrString". $Side},$arrKey,$firstFree,1);
					${"ArrowPosition". $Side}[$startArrowPos+$firstFree] = $Coords;
				}
				$MyUpQuery = "UPDATE Finals SET ";
				if($_REQUEST["Volee"]!='T')
					$MyUpQuery .= "FinArrowPosition='" . implode('|',${"ArrowPosition". $Side}) . "',";
				else
					$MyUpQuery .= "FinTiePosition='" . implode('|',${"ArrowPosition". $Side}) . "',";
				$MyUpQuery .=  "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
				$MyUpQuery .= "WHERE FinMatchNo=" . StrSafe_DB($Side=="Sx" ? $MyRow->MatchNo : $MyRow->OppMatchNo) . " AND FinEvent=" . StrSafe_DB($_REQUEST['Event']) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
				$RsUpdate=safe_w_sql($MyUpQuery);
			}
			UpdateArrowString($MyRow->MatchNo, $MyRow->Event, "0", $ArrStringSx, $startArrowString, $startArrowString+$nARR-1);
			UpdateArrowString($MyRow->OppMatchNo, $MyRow->Event, "0", $ArrStringDx, $startArrowString, $startArrowString+$nARR-1);
			//Aggiorno posizione frecce SX
			$MyUpQuery = "UPDATE Finals SET ";
			if($_REQUEST["Volee"]!='T')
				$MyUpQuery .= "FinArrowPosition='" . implode('|',$ArrowPositionSx) . "',";
			else
				$MyUpQuery .= "FinTiePosition='" . implode('|',ArrowPositionSx) . "',";
			$MyUpQuery .=  "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
			$MyUpQuery .= "WHERE FinMatchNo=" . $MyRow->MatchNo . " AND FinEvent=" . StrSafe_DB($_REQUEST['Event']) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
			$RsUpdate=safe_w_sql($MyUpQuery);
			//Aggiorno posizione frecce DX
			$MyUpQuery = "UPDATE Finals SET ";
			if($_REQUEST["Volee"]!='T')
				$MyUpQuery .= "FinArrowPosition='" . implode('|',$ArrowPositionDx) . "',";
			else
				$MyUpQuery .= "FinTiePosition='" . implode('|',ArrowPositionDx) . "',";
			$MyUpQuery .=  "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
			$MyUpQuery .= "WHERE FinMatchNo=" . $MyRow->OppMatchNo . " AND FinEvent=" . StrSafe_DB($_REQUEST['Event']) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']);
			$RsUpdate=safe_w_sql($MyUpQuery);
		}
	}

/*********************
  Dati dello Scontro
**********************/
	$Select
		= "SELECT "
		. "e.EnId AS Athlete, e.EnCode as Code, e.EnName AS Name, upper(e.EnFirstName) AS FirstName, c.CoCode AS NationCode, c.CoName AS Nation, "
		. "f.FinMatchNo AS MatchNo, IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS FinalScore, f.FinScore AS Score, f.FinSetScore as SetScore, f.FinSetPoints as SetPoints, f.FinTie as Tie, f.FinArrowString AS ArrowString, f.FinArrowPosition AS ArrPos, f.FinTieBreak AS TieBreak, f.FinTiePosition AS TiePos, "
		. "e2.EnId AS OppAthlete, e.EnCode as OppCode, e2.EnName AS OppName, upper(e2.EnFirstName) AS OppFirstName, c2.CoCode AS OppNationCode, c2.CoName AS OppNation, "
		. "f2.FinMatchNo AS OppMatchNo, IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) AS OppFinalScore, f2.FinScore AS OppScore, f2.FinSetScore as OppSetScore, f2.FinSetPoints as OppSetPoints, f2.FinTie as OppTie, f2.FinArrowString AS OppArrowString, f2.FinArrowPosition AS OppArrPos, f2.FinTieBreak AS OppTieBreak, f2.FinTiePosition AS OppTiePos, "
		. "EvCode AS Event, EvEventName AS EventName, GrPhase as Phase, EvMatchMode as MatchMode, EvMatchArrowsNo "
		. "FROM Events "
		. "INNER JOIN Finals AS f ON EvTournament=f.FinTournament AND EvCode=f.FinEvent AND EvTeamEvent='0' "
		. "LEFT JOIN Entries AS e ON f.FinAthlete=e.EnId AND f.FinTournament=e.EnTournament "
		. "LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
		. "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament "
		. "LEFT JOIN Entries AS e2 ON f2.FinAthlete=e2.EnId AND f2.FinTournament=e2.EnTournament "
		. "LEFT JOIN Countries AS c2 ON e2.EnCountry=c2.CoId AND e2.EnTournament=c2.CoTournament "

		. "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo "
		. "WHERE EvCode=" . StrSafe_DB($Event) . " AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "AND f.FinMatchNo =" . 	StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) . " ";

	//print $Select . '<br>';
	if (debug) print $Select . '<br>';

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Individual/Fun_JS.js"></script>',
		);

	$PAGE_TITLE=get_text('IndFinal');

	include('Common/Templates/head.php');
?>
<form name="FrmVolee" method="post" action="<?php print $_SERVER['PHP_SELF']; ?>">
<?php
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1)
	{
		$MyRow=safe_fetch($Rs);
		$obj=getEventArrowsParams($MyRow->Event,$MyRow->Phase,0);

		//Sistemiamo i numeri di  frecce
		$nEND=$obj->ends;
		$nARR=$obj->arrows;
		$maxArrows=$nEND*$nARR;
		$nTieBreak=$obj->so;

		$SetTieSx=0;
		$SetTieDx=0;
		/* Preparo le variabili per i due scontri */
		$MyRow->ArrowString=str_pad($MyRow->ArrowString,$maxArrows);
		$MyRow->TieBreak=str_pad($MyRow->TieBreak,$nTieBreak);
		$MyRow->OppArrowString=str_pad($MyRow->OppArrowString,$maxArrows);
		$MyRow->OppTieBreak=str_pad($MyRow->OppTieBreak,$nTieBreak);

		$SemaforoSx = '&nbsp;';
		$SemaforoDx = '&nbsp;';
		$ScoreSx = '';
		$ScoreDx = '';

		$InsertSx = '';
		$InsertDx = '';

/*************************
Conteggio i punti di Set
*************************/
		$SetPointSx=array();
		$SetPointDx=array();
		$SetTotSx=array();
		$SetTotDx=array();
		if($MyRow->MatchMode!=0)
		{
			for($i=0; $i<$maxArrows; $i=$i+$nARR)		//Cicla per tutte le volee dell'incontro
			{
				$SetPointSx[] = ValutaArrowString(substr($MyRow->ArrowString,$i,$nARR));
				$SetPointDx[] = ValutaArrowString(substr($MyRow->OppArrowString,$i,$nARR));
				if(strlen(trim(substr($MyRow->ArrowString,$i,$nARR)))==$nARR && strlen(trim(substr($MyRow->OppArrowString,$i,$nARR)))==$nARR && ctype_upper(substr($MyRow->ArrowString,$i,$nARR)) && ctype_upper(substr($MyRow->OppArrowString,$i,$nARR)))
				{
					if($SetPointSx[$i/$nARR]>$SetPointDx[$i/$nARR])
					{
						$SetTotSx[]= 2;
						$SetTotDx[]= 0;
					}
					else if($SetPointSx[$i/$nARR]<$SetPointDx[$i/$nARR])
					{
						$SetTotSx[]= 0;
						$SetTotDx[]= 2;
					}
					else
					{
						$SetTotSx[]= 1;
						$SetTotDx[]= 1;
					}
				}
			}
		}
/********************************
  Atleta di Sinistra --> Athlete
 ********************************/
		//if (!is_null($MyRow["Athlete"]))
		{
			$ScoreSx
				= '<table class="Tabella">' . "\n"
				. '<tr>'
					. '<th rowspan="2"  style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('End (volee)') . '</th>'
					. '<th colspan="' . $nARR . '"   style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('Arrow') . '</th>'
					. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text(($MyRow->MatchMode==0 ? 'TotalProg':'SetTotal'),'Tournament') . '</th>'
					. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('TotaleScore') . '</th>';
			if($MyRow->MatchMode!=0)
			{
				$ScoreSx .= '<th rowspan="2" colspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('SetPoints', 'Tournament') . '</th>';
			}
			$ScoreSx .= '</tr>' . "\n";

			$ScoreSx.='<tr>';
			for ($j=0;$j<$nARR;++$j)
				$ScoreSx.='<th  style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . ($j+1) . '</th>';
				//$ScoreSx.='<th width="10%">' . ($j+1) . '</th>';
			$ScoreSx.='</tr>' . "\n";

			$Tot=0;
			$TotSet=0;
		// ArrowString
			for ($i=0;$i<$nEND;++$i)
			{
				$TotSerie=0;
				$ScoreSx.='<tr>';
				//$ScoreSx.='<th><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $_REQUEST['MatchNo'] . '&amp;Volee=' . ($i+1) . '&amp;Event=' . $_REQUEST['Event'] . '">' .  ($i+1) . '</a></th>';
				$ScoreSx.='<th style="font-size:180%; font-weight:bold; text-align:center;"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $MatchNo . '&amp;Volee=' . ($i+1) . '&amp;Event=' . $Event . '">' .  ($i+1) . '</a></th>';
				for ($j=0;$j<$nARR;++$j)
				{
					if(array_key_exists($MyRow->ArrowString[$i*$nARR+$j],$MySym))
					{
						$MyPValue=$MySym[$MyRow->ArrowString[$i*$nARR+$j]]["P"];
						$MyNValue=$MySym[$MyRow->ArrowString[$i*$nARR+$j]]["N"];
					}
					else
					{
						$MyPValue='';
						$MyNValue=0;
					}
					$ScoreSx.='<td style="font-size:180%; font-weight:bold; text-align:center;">' . $MyPValue  . '</td>';
					$TotSerie+=$MyNValue;
				}
				$Tot+=$TotSerie;

				$ScoreSx.='<td style="font-size:180%; font-weight:bold; text-align:right;">' . $TotSerie. '</td>';
				$ScoreSx.='<td style="font-size:180%; ' . ($MyRow->MatchMode==0 ? 'font-weight:bold; ' : '') . 'text-align:right;">' . $Tot . '</td>';
				if($MyRow->MatchMode!=0)
				{
					$TotSet += 	(empty($SetTotSx[$i]) ? 0 : $SetTotSx[$i]);
					$ScoreSx .= '<td style="font-size:180%; font-weight:bold; text-align:right;">' . (empty($SetTotSx[$i]) ? '0' : $SetTotSx[$i]) . '</td>';
					$ScoreSx .= '<td style="font-size:180%; font-weight:bold; text-align:right;">' . $TotSet . '</td>';
				}
				$ScoreSx.='</td>';
				$ScoreSx.='</tr>' . "\n";
			}
		// Tiebreak
			$ScoreSx.='<tr>';
			$ScoreSx.='<th style="font-size:180%; font-weight:bold; text-align:center;"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $MatchNo . '&amp;Volee=T&amp;Event=' . $Event . '">T.B.</a></th>';
			$ScoreSx.='<td colspan="' . $nARR . '">';
			$ScoreSx.='<table class="Tabella">' . "\n";
			$ScoreSx.='<tr>';
			for ($i=0;$i<$nTieBreak;++$i)
			{
				$MyNValue=-1;
				if(array_key_exists($MyRow->TieBreak[$i],$MySym))
				{
					$MyPValue=$MySym[$MyRow->TieBreak[$i]]["P"];
					$MyNValue=$MySym[$MyRow->TieBreak[$i]]["N"];
				}
				else
				{
					$MyPValue='';
					$MyNValue=-1;
				}

				//$ScoreSx.='<td class="NumberAlign Light">' . ($MyNValue!=-1 ? $MyPValue : '&nbsp;') . '</td>';
				$ScoreSx.='<td style="font-size:180%; font-weight:bold; text-align:center;">' . ($MyNValue!=-1 ? $MyPValue . '&nbsp;': '&nbsp;') . '</td>';
			}
			$ScoreSx.='</tr>' . "\n";
			$ScoreSx.='</table>' . "\n";
			$ScoreSx.='</td>';
			// Cella vuota e poi il totale

			$ScoreSx.='<td>&nbsp;</td><td style="font-size:180%; ' . ($MyRow->MatchMode==0 ? 'font-weight:bold; ' : '') . 'text-align:right;">' . $Tot . '</td>';
			if($MyRow->MatchMode!=0)
			{
				$ScoreSx .= '<td colspan="2" style="font-size:180%; font-weight:bold; text-align:right;">' . $MyRow->FinalScore . '</td>';
			}

	//		$ScoreSx.='<td>&nbsp;</td><td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot . '</td>';
			$ScoreSx.='</tr>' . "\n";
			$ScoreSx.='</table>' . "\n";
		}

/**********************************
  Atleta di Destra --> OppAthlete
***********************************/
		//if (!is_null($MyRow["OppAthlete"]))
		{
			$ScoreDx
				= '<table class="Tabella">' . "\n"
				. '<tr>'
					. '<th rowspan="2"  style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('End (volee)') . '</th>'
					. '<th colspan="' . $nARR . '"   style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('Arrow') . '</th>'
					. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text(($MyRow->MatchMode==0 ? 'TotalProg':'SetTotal'),'Tournament') . '</th>'
					. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('TotaleScore') . '</th>';
			if($MyRow->MatchMode!=0)
			{
				$ScoreDx .= '<th rowspan="2" colspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('SetPoints', 'Tournament') . '</th>';
			}
			$ScoreDx .= '</tr>' . "\n";

			$ScoreDx.='<tr>';
			for ($j=0;$j<$nARR;++$j)
				$ScoreDx.='<th style="font-size:180%; font-weight:bold; text-align:center; width:15%; width:10%">' . ($j+1) . '</th>';
			$ScoreDx.='</tr>' . "\n";

			$Tot=0;
			$TotSet=0;
		// ArrowString
			for ($i=0;$i<$nEND;++$i)
			{
				$TotSerie=0;
				$ScoreDx.='<tr>';
				$ScoreDx.='<th style="font-size:180%; font-weight:bold; text-align:center;"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $MatchNo . '&amp;Volee=' . ($i+1) . '&amp;Event=' . $Event . '">' .  ($i+1) . '</a></th>';
				for ($j=0;$j<$nARR;++$j)
				{
					if(array_key_exists($MyRow->OppArrowString[$i*$nARR+$j],$MySym))
					{
						$MyPValue=$MySym[$MyRow->OppArrowString[$i*$nARR+$j]]["P"];
						$MyNValue=$MySym[$MyRow->OppArrowString[$i*$nARR+$j]]["N"];
					}
					else
					{
						$MyPValue='';
						$MyNValue=0;
					}

					$ScoreDx.='<td style="font-size:180%; font-weight:bold; text-align:center;">' . $MyPValue  . '</td>';
					$TotSerie+=$MyNValue;
				}
				$Tot+=$TotSerie;
				$ScoreDx.='<td style="font-size:180%; font-weight:bold; text-align:right;">' . $TotSerie. '</td>';
				$ScoreDx.='<td style="font-size:180%; ' . ($MyRow->MatchMode==0 ? 'font-weight:bold; ' : '') . 'text-align:right;">' . $Tot . '</td>';
				if($MyRow->MatchMode!=0)
				{
					$TotSet += 	(empty($SetTotDx[$i]) ? 0 : $SetTotDx[$i]);
					$ScoreDx .= '<td style="font-size:180%; font-weight:bold; text-align:right;">' . (empty($SetTotDx[$i]) ? '0' : $SetTotDx[$i]) . '</td>';
					$ScoreDx .= '<td style="font-size:180%; font-weight:bold; text-align:right;">' . $TotSet . '</td>';
				}
				$ScoreDx.='</tr>' . "\n";
			}
		// Tiebreak
			$ScoreDx.='<tr>';
			$ScoreDx.='<th style="font-size:180%; font-weight:bold; text-align:center;"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $MatchNo . '&amp;Volee=T&amp;Event=' . $Event . '">T.B.</a></th>';
			$ScoreDx.='<td colspan="' . $nARR . '">';
			$ScoreDx.='<table class="Tabella">' . "\n";
			$ScoreDx.='<tr>';
			for ($i=0;$i<$nTieBreak;++$i)
			{
				$MyNValue=-1;
				if(array_key_exists($MyRow->OppTieBreak[$i],$MySym))
				{
					$MyPValue=$MySym[$MyRow->OppTieBreak[$i]]["P"];
					$MyNValue=$MySym[$MyRow->OppTieBreak[$i]]["N"];
				}
				else
				{
					$MyPValue='';
					$MyNValue=-1;
				}

				$ScoreDx.='<td style="font-size:180%; font-weight:bold; text-align:center;">' . ($MyNValue!=-1 ? $MyPValue . '&nbsp;' : '&nbsp;') . '</td>';
			}
			$ScoreDx.='</tr>' . "\n";
			$ScoreDx.='</table>' . "\n";
			$ScoreDx.='</td>';
			$ScoreDx.='<td>&nbsp;</td><td style="font-size:180%; ' . ($MyRow->MatchMode==0 ? 'font-weight:bold; ' : '') . 'text-align:right;">' . $Tot . '</td>';
			if($MyRow->MatchMode!=0)
			{
				$ScoreDx .= '<td colspan="2" style="font-size:180%; font-weight:bold; text-align:right;">' . $MyRow->OppFinalScore . '</td>';
			}
			$ScoreDx.='</tr>' . "\n";
			$ScoreDx.='</table>' . "\n";
		}

/*************************
  Inserimenti & Semaforo
**************************/
		if(isset($_REQUEST["Volee"]) && preg_match("/^[1-5]|t$/i",$_REQUEST["Volee"]))
		{
/*****************
  Inserimento SX
******************/
			if (!isset($_REQUEST['OnlyOne']) || $MyRow->MatchNo==$MatchNo)
			{

				$InsertSx
					.='<table class="Tabella">' . "\n"
					. '<tr>'
					. '<td>'
					. '</td>'
					. '<td>#</td>';

				for ($i=1;$i<=($_REQUEST['Volee']!='T' && $_REQUEST['Volee']!='t' ? $nARR : $nTieBreak);++$i)
					$InsertSx.='<td>' . $i . '</td>';

				$InsertSx
					.='</tr>' . "\n"
					. '<tr>'
					. '<td>&nbsp;</td><td>' . (is_numeric($_REQUEST['Volee']) ? $_REQUEST['Volee'] : 'T.B.') . '</td>';


				if ($_REQUEST['Volee']<>'T' && $_REQUEST['Volee']<>'t')	// non ho tiebreak
				{
					$i=$_REQUEST['Volee']-1;

					for ($j=0;$j<$nARR;++$j)
					{
						$InsertSx.='<td>';
						if(array_key_exists($MyRow->ArrowString[$i*$nARR+$j],$MySym))
						{
							$MyPValue=$MySym[$MyRow->ArrowString[$i*$nARR+$j]]["P"];
							$MyNValue=$MySym[$MyRow->ArrowString[$i*$nARR+$j]]["N"];
						}
						else
						{
							$MyPValue='';
							$MyNValue='';
						}
						$InsertSx.='<input type="text" size="1" maxlength="3" name="M_' . $MatchSx . '_' . $i . '_' . $j . '" id="M_' . $MatchSx . '_' . $i . '_' . $j . '" value="' . $MyPValue . '">';
						$TxtPuntiSx[$j]='M_' . $MatchSx . '_' . $i . '_' . $j;
						$StrPuntiSx.="'" . $TxtPuntiSx[$j] . "',";
						$InsertSx.='</td>';
					}
				}
				else	// ho il tiebreak
				{
					for ($j=0;$j<$nTieBreak;++$j)
					{
						$InsertSx.='<td>';
						if(array_key_exists($MyRow->TieBreak[$j],$MySym))
						{
							$MyPValue=$MySym[$MyRow->TieBreak[$j]]["P"];
							$MyNValue=$MySym[$MyRow->TieBreak[$j]]["N"];
						}
						else
						{
							$MyPValue='';
							$MyNValue='';
						}
						$InsertSx.='<input type="text" size="1" maxlength="3" name="M_' . $MatchSx . '_T_' . $j . '" id="M_' . $MatchSx . '_T_' . $j . '" value="' . $MyPValue . '">';
						$TxtPuntiSx[$j]='M_' . $MatchSx . '_T_' . $j;
						$StrPuntiSx.="'" . $TxtPuntiSx[$j] . "',";
						// hidden delle coordinate
						$InsertSx.='</td>';
					}
				}
				$StrPuntiSx=substr($StrPuntiSx,0,-1);
			// hidden delle coordinate e Bottone per l'insert nel db
				$InsertSx.='<td><input type="button" value="'.get_text('CmdOk').'" onClick="EseguiSubmit()"></td>';
				$InsertSx.='</tr>' . "\n";
			// hidden per le coordinate e bottoni per i dubbi
				$InsertSx.='<tr>';
				$InsertSx.='<td></td><td></td>';
				$PosArrSx=explode('|',$MyRow->ArrPos);
				$PosTieSx=explode('|',$MyRow->TiePos);
				if ($_REQUEST['Volee']<>'t' && $_REQUEST['Volee']<>'T')
				{
					$i=$_REQUEST['Volee']-1;
					for ($j=0;$j<$nARR;++$j)
					{
						$InsertSx.='<td>';
						$InsertSx.='<input type="button" value="*" onClick="GestisciDubbio(\'M_' . $MatchSx . '_' . $i . '_' . $j . '\')">';
						$InsertSx.='</td>';
					}
				}
				else  //tiebreak
				{
					for ($j=0;$j<$nTieBreak;++$j)
					{
						$InsertSx.='<td>';
						$InsertSx.='<input type="button" value="*" onClick="GestisciDubbio(\'M_' . $MatchSx . '_T_' . $j . '\')">';
						$InsertSx.='</td>';
					}
				}
				$InsertSx.='</tr>' . "\n";
				$InsertSx.='</table>' . "\n";
			}

/*****************
  Inserimento DX
******************/
			if (!isset($_REQUEST['OnlyOne']) || $MyRow->OppMatchNo==$MatchNo)
			{
				$InsertDx
						.='<table class="Tabella">' . "\n"
						. '<tr>'
						. '<td>'
						. '</td>'
						. '<td>#</td>';

					for ($i=1;$i<=($_REQUEST['Volee']!='T' && $_REQUEST['Volee']!='t' ? $nARR : $nTieBreak);++$i)
						$InsertDx.='<td>' . $i . '</td>';

					$InsertDx
						.='</tr>' . "\n"
						. '<tr>'
						. '<td>&nbsp;</td><td>' . (is_numeric($_REQUEST['Volee']) ? $_REQUEST['Volee'] : 'T.B.') . '</td>';

				if ($_REQUEST['Volee']<>'T' && $_REQUEST['Volee']<>'t')	// non ho tiebreak
				{
					$i=$_REQUEST['Volee']-1;

					for ($j=0;$j<$nARR;++$j)
					{
						$InsertDx.='<td>';
						if(array_key_exists($MyRow->OppArrowString[$i*$nARR+$j],$MySym))
						{
							$MyPValue=$MySym[$MyRow->OppArrowString[$i*$nARR+$j]]["P"];
							$MyNValue=$MySym[$MyRow->OppArrowString[$i*$nARR+$j]]["N"];
						}
						else
						{
							$MyPValue='';
							$MyNValue='';
						}
						$InsertDx.='<input type="text" size="1" maxlength="3" name="M_' . $MatchDx . '_' . $i . '_' . $j . '" id="M_' . $MatchDx . '_' . $i . '_' . $j . '" value="' . $MyPValue . '">';
						$TxtPuntiDx[$j]='M_' . $MatchDx . '_' . $i . '_' . $j;
						$StrPuntiDx.="'" . $TxtPuntiDx[$j] . "',";
						$InsertDx.='</td>';
					}
				}
				else	// ho il tiebreak
				{
					for ($j=0;$j<$nTieBreak;++$j)
					{
						$InsertDx.='<td>';
						if(array_key_exists($MyRow->OppTieBreak[$j],$MySym))
						{
							$MyPValue=$MySym[$MyRow->OppTieBreak[$j]]["P"];
							$MyNValue=$MySym[$MyRow->OppTieBreak[$j]]["N"];
						}
						else
						{
							$MyPValue='';
							$MyNValue='';
						}
						$InsertDx.='<input type="text" size="1" maxlength="3" name="M_' . $MatchDx . '_T_' . $j . '" id="M_' . $MatchDx . '_T_' . $j . '" value="' . $MyPValue . '">';
						$TxtPuntiDx[$j]='M_' . $MatchDx . '_T_' . $j;
						$StrPuntiDx.="'" . $TxtPuntiDx[$j] . "',";
						// hidden delle coordinate
						$InsertDx.='</td>';
					}
				}
				$StrPuntiDx=substr($StrPuntiDx,0,-1);
			// hidden delle coordinate e Bottone per l'insert nel db
				$InsertDx.='<td><input type="button" value="'.get_text('CmdOk').'" onClick="EseguiSubmit()"></td>';
				$InsertDx.='</tr>' . "\n";
			// hidden per le coordinate e bottoni per i dubbi
				$InsertDx.='<tr>';
				$InsertDx.='<td></td><td></td>';
				$PosArrDx=explode('|',$MyRow->OppArrPos);
				$PosTieDx=explode('|',$MyRow->OppTiePos);
				if ($_REQUEST['Volee']<>'t' && $_REQUEST['Volee']<>'T')
				{
					$i=$_REQUEST['Volee']-1;
					for ($j=0;$j<$nARR;++$j)
					{
						$InsertDx.='<td>';
						$InsertDx.='<input type="button" value="*" onClick="GestisciDubbio(\'M_' . $MatchDx . '_' . $i . '_' . $j . '\')">';
						$InsertDx.='</td>';
					}
				}
				else  //tiebreak
				{
					for ($j=0;$j<$nTieBreak;++$j)
					{
						$InsertDx.='<td>';
						$InsertDx.='<input type="button" value="*" onClick="GestisciDubbio(\'M_' . $MatchDx . '_T_' . $j . '\')">';
						$InsertDx.='</td>';
					}
				}
				$InsertDx.='</tr>' . "\n";
				$InsertDx.='</table>' . "\n";
			}

/***************
  Semaforo SX
****************/
			$SemaforoSx= '<table class="Tabella">' . "\n";
			if ($_REQUEST['Volee']=='T')
				$SemaforoSx.='<tr><td class="Title">Tie Break</td></tr>';
			if(strtoupper($_REQUEST["Volee"])!='T')
			{
				for ($i=0;$i<$nEND;++$i)
				{
					$Tmp = "";
					/*print '<pre>';
					print_r($PosArrSx);
					print '</pre>';*/
					for($j=0; $j<$nARR; $j++)
					{
						if(@array_key_exists (($i*$nARR+$j), $PosArrSx))
							$Tmp .= "&amp;Arrows[]=" .  $PosArrSx[$i*$nARR+$j];

					}

					if($MyTargetHitMiss)
						$Tmp .='&amp;HMOUT=1';
					else if($MyTargetField)
						$Tmp .='&amp;FIELD=1';
					$SemaforoSx.='<tr><td class="FontMedium Bold">' . ($i+1) . '</td><td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Size=120' . $Tmp . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target" border="0"></td></tr>';
				}
			}
			else
			{
				for($j=0;$j<$nTieBreak;$j++)
				{
					$SemaforoSx .= '<tr><td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Size=150&amp;Arrow=' . $PosTieSx[$j];
					if($MyTargetHitMiss)
						$SemaforoSx .='&amp;HMOUT=1';
					else if($MyTargetField)
						$SemaforoSx .='&amp;FIELD=1';
					$SemaforoSx .= ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target"></td></tr>';
				}
			}
			$SemaforoSx.='</table>' . "\n";

/***************
  Semaforo DX
****************/
			$SemaforoDx= '<table class="Tabella">' . "\n";
			//$SemaforoDx.='<tr><td class="Title">' . (strtoupper($_REQUEST["Volee"])=='T' ? "Tie Break" : get_text('End (volee)') . ' ' . ($_REQUEST["Volee"]) ) . '</td></tr>';
			if ($_REQUEST['Volee']=='T')
				$SemaforoDx.='<tr><td class="Title">Tie Break</td></tr>';
			if(strtoupper($_REQUEST["Volee"])!='T')
			{
				//$i=$_REQUEST["Volee"]-1;

				for ($i=0;$i<$nEND;++$i)
				{
					$Tmp = "";
					/*print '<pre>';
					print_r($PosArrSx);
					print '</pre>';*/
					for($j=0; $j<$nARR; $j++)
					{
						if(@array_key_exists (($i*$nARR+$j), $PosArrDx))
							$Tmp .= "&amp;Arrows[]=" .  $PosArrDx[$i*$nARR+$j];

					}

					if($MyTargetHitMiss)
						$Tmp.='&amp;HMOUT=1';
					else if($MyTargetField)
						$Tmp.='&amp;FIELD=1';
					$SemaforoDx.='<tr><td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Size=120' . $Tmp . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target" border="0"></td><td class="FontMedium Bold">' . ($i+1) . '</td></tr>';
				}
			}
			else
			{
				for($j=0;$j<$nTieBreak;$j++)
				{
					$SemaforoDx .= '<tr><td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Size=150&amp;Arrow=' . $PosTieDx[$j];
					if($MyTargetHitMiss)
						$SemaforoDx .= '&amp;HMOUT=1';
					else if($MyTargetField)
						$SemaforoDx .= '&amp;FIELD=1';
					$SemaforoDx .= ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target"></td></tr>';
				}
			}

			$SemaforoDx.='</table>' . "\n";

		}

?>
<table class="Tabella">
<?php // Nomi e nazioni ?>
<tr>


<td style="font-size:150%; font-weight:bold; text-align:center; width:17%;"><?php print $MyRow->Nation; ?></td>
<td style="font-size:180%; font-weight:bold; text-align:center; width:32%;"><?php print $MyRow->FirstName . ' ' . $MyRow->Name; ?></td>
<td style="font-size:180%; font-weight:bold; text-align:center; width:32%;"><?php print $MyRow->OppFirstName . ' ' . $MyRow->OppName; ?></td>
<td style="font-size:150%; font-weight:bold; text-align:center; width:17%;"><?php print $MyRow->OppNation; ?></td>
</tr>
<?php // Fine Nomi e nazioni ?>

<tr>
<td rowspan="3" valign="top"><?php print $SemaforoSx; ?></td>
<td valign="top"><?php print $ScoreSx; ?></td>
<td valign="top"><?php print $ScoreDx; ?></td>
<td rowspan="3" valign="top"><?php print $SemaforoDx; ?></td>
</tr>

<?php // Target Sx e Dx sotto gli score ?>
<tr>
<td class="Center">
<?php
	$Tmp = "";
	/*print '<pre>';
	print_r($PosArrSx);
	print '</pre>';*/
	if (isset($_REQUEST['Volee']) && $_REQUEST['Volee']!='T')
	{
		$i=$_REQUEST['Volee']-1;
		for($j=0; $j<$nARR; $j++)
		{
			if(@array_key_exists (($i*$nARR+$j), $PosArrSx))
				$Tmp .= "&amp;Arrows[]=" .  $PosArrSx[$i*$nARR+$j];

		}

	}
	else
	{
		for($j=0; $j<$nTieBreak;++$j)
		{
			if(@array_key_exists($j,$PosTieSx))
				$Tmp .= "&amp;Arrows[]=" . $PosTieSx[$j];
		}
	}
	if($MyTargetHitMiss)
		$Tmp.='&amp;HMOUT=1';
	else if($MyTargetField)
		$Tmp.='&amp;FIELD=1';

?>
<input name="TargetSx" style="cursor:crosshair" type="image" src="../../Common/target.php?Size=300&amp;<?php print $Tmp;?>&amp;noborder<?php echo ($MyTargetComplete ? '&amp;complete': ''); ?>">
</td>
<td class="Center">
<?php
	$Tmp = "";
	/*print '<pre>';
	print_r($PosArrSx);
	print '</pre>';*/
	if (isset($_REQUEST['Volee']) && $_REQUEST['Volee']!='T')
	{
		$i=$_REQUEST['Volee']-1;
		for($j=0; $j<$nARR; $j++)
		{
			if(@array_key_exists (($i*$nARR+$j), $PosArrDx))
				$Tmp .= "&amp;Arrows[]=" .  $PosArrDx[$i*$nARR+$j];

		}

	}
	else
	{
		for($j=0; $j<$nTieBreak;++$j)
		{
			if(@array_key_exists($j,$PosTieDx))
				$Tmp .= "&amp;Arrows[]=" . $PosTieDx[$j];
		}
	}
	if($MyTargetHitMiss)
		$Tmp.='&amp;HMOUT=1';
	else if($MyTargetField)
		$Tmp.='&amp;FIELD=1';

?>
<input name="TargetDx" style="cursor:crosshair" type="image" src="../../Common/target.php?Size=300&amp;<?php print $Tmp;?>&amp;noborder<?php echo ($MyTargetComplete ? '&amp;complete': ''); ?>">
</td>
</tr>
<tr>
<td><br><br><br><?php print $InsertSx; ?></td>
<td><br><br><br><?php print $InsertDx; ?></td>
</tr>
<?php // Fine Target Sx e Dx sotto gli score ?>
</table>
<input type="hidden" name="Event" value="<?php print $_REQUEST['Event']; ?>">
<input type="hidden" name="Volee" value="<?php print $_REQUEST['Volee']; ?>">
<input type="hidden" name="MatchNo" value="<?php print $_REQUEST['MatchNo']; ?>">
<input type="hidden" name="Command" id="Command">
<?php
	}
?>
</form>
<?php
	include('Common/Templates/tail.php');
?>