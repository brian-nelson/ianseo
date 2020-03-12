<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	if (!CheckTourSession() || !isset($_REQUEST['Event']) || !isset($_REQUEST['MatchNo'])) printcrackerror();
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');

	//Gestione Volee & Frecce
	$nEND=4;	// Numero di Volee
	$nARR=6;	// Numero di frecce
	$nSO=TieBreakArrows_Team; //Numero di frecce di SO

	$MyQuery = 'SELECT EvMixedTeam FROM Events WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=1 AND EvCode=' . StrSafe_DB($_REQUEST['Event']);
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)==1)
	{
		$MyRow=safe_fetch($Rs);
		if($MyRow->EvMixedTeam)
		{
			$nARR=4;
			$nSO=TieBreakArrows_MixedTeam;
		}
	}


	// Nomi delle textbox dei punti a sinistra e a destra
	$TxtPuntiSx = array("","","");
	$TxtPuntiDx = array("","","");
	$StrPuntiSx="";
	$StrPuntiDx="";

// Vettore dei punti (bersaglio)
	$MySym = ${GetTargetType($_REQUEST['Event'],1)};
	//print GetTargetType($_REQUEST['Event']);exit;
	$MyTargetComplete = TargetIsComplete(GetTargetType($_REQUEST['Event'],1));
	$MyTargetHitMiss = strstr(GetTargetType($_REQUEST['Event'],1), 'TrgHM');
	$MyTargetField = strstr(GetTargetType($_REQUEST['Event'],1), 'TrgField');
	//print $MyTargetComplete;exit;

	$MyTargetSize = 0;
	$MyTargetSize = ($MyTargetComplete ? 100 : 200);

	if (debug)
	{
		print GetTargetType($_REQUEST['Event'],1) . '<br>';
		print ($MyTargetComplete ? 'Completo' : 'Non completo') . '<br>';
		print 'Volee: ' . $nEND . '<br>';
		print 'Frecce: ' . $nARR . '<br>';
	}

/*
 * MatchNo dell'incontro
 *
 * Se $_REQUEST['MatchNo'] è pari la coppia è data da lui e dal successivo
 * altrimenti dal precedente e lui.
 */
	$MatchSx=-1;
	$MatchDx=-1;

	if ($_REQUEST['MatchNo']%2==0)
	{
		$MatchSx=$_REQUEST['MatchNo'];
		$MatchDx=$_REQUEST['MatchNo']+1;
	}
	else
	{
		$MatchSx=$_REQUEST['MatchNo']-1;
		$MatchDx=$_REQUEST['MatchNo'];
	}

	if (debug) print 'MatchNo ' . $MatchSx . ' - ' . $MatchDx . '<br>';

	if (!IsBlocked(BIT_BLOCK_TEAM))
	{
		// Scrittura nel db
		if ((isset($_REQUEST['Command']) && $_REQUEST['Command']=='OK') ||
			(isset($_REQUEST["TargetSx_x"]) && isset($_REQUEST["TargetSx_y"])) ||
			(isset($_REQUEST["TargetDx_x"]) && isset($_REQUEST["TargetDx_y"])))
		{
		// Estraggo le arrowstring coinvolte
			$Select
				= "SELECT TfMatchNo AS MatchNo,TfEvent AS Event,TfArrowString AS ArrowString,TfTieBreak AS TieBreak,TfArrowPosition AS ArrPos,TfTiePosition AS TiePos "
				. "FROM TeamFinals "
				. "WHERE TfMatchNo IN(" . StrSafe_DB($MatchSx) . "," . StrSafe_DB($MatchDx) . ") AND TfEvent=" . StrSafe_DB($_REQUEST['Event']) . " "
				. "AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "ORDER BY TfMatchNo ASC ";
			$Rs=safe_r_sql($Select);

			if (debug) print $Select . '<br>';	//exit;

			$MyRow=safe_fetch($Rs);
			$ArrSx=str_pad($MyRow->ArrowString,24);

			$TieSx=str_pad($MyRow->TieBreak,$nSO);
			$PosArrSx=explode('|',str_pad($MyRow->ArrPos,24-substr_count($MyRow->ArrPos, "|"),"|"));
			$PosTieSx=explode('|',str_pad($MyRow->TiePos,9-substr_count($MyRow->TiePos, "|"),"|"));
			$MyRow=safe_fetch($Rs);
			$ArrDx="";
			$TieDx="";

			if (!$MyRow)
			{
				if ($_REQUEST['MatchNo']%2==1) // ho solo il destro quindi il dato a sinistra va a destra
				{
					$ArrDx=$ArrSx;
					$TieDx=$TieSx;
					$PosArrDx=$PosArrSx;
					$PosTieDx=$TiePosSx;
				}
			}
			else
			{
				$ArrDx=str_pad($MyRow->ArrowString,24);
				$TieDx=str_pad($MyRow->TieBreak,TieBreakArrows_Ind);
				$PosArrDx=explode('|',str_pad($MyRow->ArrPos,24-substr_count($MyRow->ArrPos, "|"),"|"));
				$PosTieDx=explode('|',str_pad($MyRow->TiePos,9-substr_count($MyRow->TiePos, "|"),"|"));
			}

			if (debug)
			{
				print 'ArrowString Sx --> ...' . $ArrSx . '...<br>';
				print 'ArrowString Dx --> ...' . $ArrDx . '...<br>';
			}

			$MyUpQuery = "";
			$FirstEmpty=Array("Sx"=>-1,"Dx"=>-1);

			$m=-1;	// MatchNo
			$v=-1;	// Volee. E' la riga della matrice degli score. T significa TieBreak
			$f=-1;	// Freccia. E' la colonna della matrice degli score
			foreach($_REQUEST as $Key => $Value)
			{
				if (preg_match("/M_[0-9]+_([0-3]|t)_[0-8]/i",$Key))
				{
					$MyKey=false;
					$Trovato=0;
					foreach ($MySym as $kk => $vv)
					{
						$MyKey=array_search($Value,$MySym[$kk]);
						if ($MyKey==='P')
						{
							$Trovato=1;
							break;
						}
					}

					if ($Trovato==0)
					{
						$Value=" ";
					}
				// Estraggo le parti del punteggio
					list(,$m,$v,$f)=explode('_',$Key);

				//Verifico se è il primo vuoto per ogni parte
					if($Value==" ")
					{
						if ($m==$MatchSx && $FirstEmpty["Sx"]==-1)
						{
							$FirstEmpty["Sx"]=$f;
						}
						if ($m==$MatchDx && $FirstEmpty["Dx"]==-1)
						{
							$FirstEmpty["Dx"]=$f;
						}
					}

				// se v vale 't' o 'T' è un tiebreak altrimenti è uno score normale
					if ($v!='t' && $v!='T')
					{
						// se m vale MatchSx lavoro sui ArrSx altrimenti lavoro su ArrDx
						if ($m==$MatchSx)
						{
							$ArrSx[$v*$nARR+$f]=" ";

							foreach ($MySym as $kk => $vv)
							{
								if ($MySym[$kk]['P']==$Value)
								{
									$ArrSx[$v*$nARR+$f]=$kk;
									break;
								}
							}

							if ($ArrSx[$v*$nARR+$f]==" ")
								$PosArrSx[$v*$nARR+$f]="";
						}
						elseif ($m==$MatchDx)
						{
							$ArrDx[$v*$nARR+$f]=" ";
							foreach ($MySym as $kk => $vv)
							{
								if ($MySym[$kk]['P']==$Value)
								{
									$ArrDx[$v*$nARR+$f]=$kk;
									break;
								}
							}

							if ($ArrDx[$v*$nARR+$f]==" ")
								$PosArrDx[$v*$nARR+$f]="";
						}
					}
					else
					{
						// se m vale MatchSx lavoro sui TieSx altrimenti lavoro su TieDx
						if ($m==$MatchSx)
						{
							$TieSx[$f]=" ";
							foreach ($MySym as $kk => $vv)
							{
								if ($MySym[$kk]['P']==$Value)
								{
									$TieSx[$f]=$kk;
									break;
								}
							}

							if ($TieSx[$f]==" ")
								$PosTieSx[$f]="";
						}
						elseif ($m==$MatchDx)
						{
							$TieDx[$f]=" ";
							foreach ($MySym as $kk => $vv)
							{
								if ($MySym[$kk]['P']==$Value)
								{
									$TieDx[$f]=$kk;
									break;
								}
							}

							if ($TieDx[$v*$nARR+$f]==" ")
								$PosTieDx[$f]="";
						}
					}
				}
			}

		//Gestisco il clic dei bersagli - Inizio col bersaglio di sinistra, segue quello di Destra
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

			if ($Side!="")
			{
				$Coords="$TmpX,$TmpY";
				$Value=10-( (int) ((sqrt(($TmpX*$TmpX)+($TmpY*$TmpY)))/$MyTargetSize));

			// Distinguo il 10 dall'X
				if($Value==10 && sqrt(($TmpX*$TmpX)+($TmpY*$TmpY))<=($MyTargetSize/2))
				{
					$Value="X";
				}
				if($FirstEmpty[$Side]!=-1)
				{
					if ($v!='t' && $v!='T')
					{
						${"Arr".$Side}[$v*$nARR+$FirstEmpty[$Side]]=" ";
						foreach($MySym as $kk => $vv)
						{
							if ($MySym[$kk]['R']==$Value)
							{
								${"Arr".$Side}[$v*$nARR+$FirstEmpty[$Side]]=$kk;
								break;
							}
						}
						${"PosArr".$Side}[$v*$nARR+$FirstEmpty[$Side]]=$Coords;
					}
					else
					{
						${"Tie".$Side}[$FirstEmpty[$Side]]=" ";
						foreach($MySym as $kk => $vv)
						{
							if ($MySym[$kk]['R']==$Value)
							{
								${"Tie".$Side}[$v*$nARR+$FirstEmpty[$Side]]=$kk;
								break;
							}
						}
						${"PosTie".$Side}[$FirstEmpty[$Side]]=$Coords;
					}
				}

			}

		//Gestisco i totali
			$TotSx=ValutaArrowString($ArrSx);
			$TotDx=ValutaArrowString($ArrDx);

		// Preparo le query di update
			if (debug)
			{
				print 'ArrSx --> ' . $ArrSx . '<br>';
				print 'TieSx --> ' . $TieSx . '<br>';
				print 'TotSx --> ' . $TotSx . '<br>';
				print 'PosArrSx --> ' . $PosArrSx . '<br>';


			}

			$MyUpQuerySx
				= "UPDATE TeamFinals SET "
				. "TfArrowString='" . $ArrSx . "',"
				. "TfTieBreak=" . StrSafe_DB($TieSx) . ","
				. "TfScore=" . StrSafe_DB($TotSx) . ","
				. "TfArrowPosition='" . implode('|',$PosArrSx) . "',"
				. "TfTiePosition='" . implode('|',$PosTieSx) . "',"
				. "TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
				. "WHERE TfMatchNo=" . StrSafe_DB($MatchSx) . " AND TfEvent=" . StrSafe_DB($_REQUEST['Event']) . " "
				. "AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";

			$MyUpQueryDx
				= "UPDATE TeamFinals SET "
				. "TfArrowString=" . StrSafe_DB($ArrDx) . ","
				. "TfTieBreak=" . StrSafe_DB($TieDx) . ","
				. "TfScore=" . StrSafe_DB($TotDx) . ","
				. "TfArrowPosition='" . implode('|',$PosArrDx) . "',"
				. "TfTiePosition=" . StrSafe_DB(implode('|',$PosTieDx)) . ","
				. "TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
				. "WHERE TfMatchNo=" . StrSafe_DB($MatchDx) . " AND TfEvent=" . StrSafe_DB($_REQUEST['Event']) . " "
				. "AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";

			if (debug)
			{
				print $MyUpQuerySx . '<br>';
				print $MyUpQueryDx . '<br>';
				exit;
			}

			$RsSx=safe_w_sql($MyUpQuerySx);
			$RsDx=safe_w_sql($MyUpQueryDx);
		}
	}
// Contatori per la distribuzione di freccie
	$CountersSx=array_fill(1,10,0);
	$CountersSx["X"]=0;
	$CountersSx["M"]=0;
	//foreach ($Counters1 as $key => $value) print $key . ' --> ' . $value . "<br>";
	$CountersDx=array_fill(1,10,0);
	$CountersDx["X"]=0;
	$CountersDx["M"]=0;

// dati dello scontro
	$Select
		= "SELECT TfMatchNo AS MatchNo,TfEvent AS Event,TfTeam AS Athlete, "
		. "TfScore AS Score,TfArrowString AS ArrowString,TfArrowPosition AS ArrPos,"
		. "TfTieBreak AS TieBreak,TfTiePosition AS TiePos, "
		. "TfWinLose AS WinLose, "
		. "CoCode AS NationCode,CoName AS Nation "
		. "FROM TeamFinals INNER JOIN Countries ON TfTeam=CoId "
		. "WHERE TfMatchNo IN(" . StrSafe_DB($MatchSx) . "," . StrSafe_DB($MatchDx) . ") "
		. "AND TfEvent=" . StrSafe_DB($_REQUEST['Event']) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY TfMatchNo ASC ";
	//print $Select . '<br>';exit;
	if (debug) print $Select . '<br>';

	//exit;

	$Rs=safe_r_sql($Select);
	$MyRowSx=NULL;
	$MyRowDx=NULL;

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Team/Fun_JS.js"></script>',
		);

	//$PAGE_TITLE=get_text('IndFinal');

	include('Common/Templates/head.php');
?>
<form name="FrmVolee" method="post" action="<?php print $_SERVER['PHP_SELF']; ?>">
<?php
	if (safe_num_rows($Rs)==2 or safe_num_rows($Rs)==1)
	{
		$MyRowSx=safe_fetch($Rs);
		$MyRowSx->ArrowString=str_pad($MyRowSx->ArrowString,24);
		$MyRowSx->TieBreak=str_pad($MyRowSx->TieBreak,$nSO);

		if($MyRowDx=safe_fetch($Rs)) {
			// se $MyRowDx esiste normalizzo le cose
			$MyRowDx->ArrowString=str_pad($MyRowDx->ArrowString,24);
			$MyRowDx->TieBreak=str_pad($MyRowDx->TieBreak,$nSO);
		} elseif ($MyRowSx->MatchNo%2!=0) {
			// se non c'è e il match è dispari passo SX a DX e annullo SX
			$MyRowDx=$MyRowSx;
			$MyRowSx=false;
		}

		if (debug)
		{
			print $MyRowSx->TieBreak . '<br>';
			print $MyRowDx->TieBreak . '<br>';
		}

		$SemaforoSx = '&nbsp;';
		$SemaforoDx = '&nbsp;';
		$ScoreSx = '';
		$ScoreDx = '';

		$InsertSx = '';
		$InsertDx = '';

		if ($MyRowSx)
		{
			$ScoreSx
				= '<table class="Tabella">' . "\n"
				. '<tr>'
				. '<th rowspan="2"  style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('End (volee)') . '</th>'
				. '<th colspan="' . $nARR . '"   style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('Arrow') . '</th>'
				. '<th rowspan="2"   style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('EndScore') . '</th>'
				. '<th rowspan="2"   style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('TotaleScore') . '</th>'
				. '</tr>' . "\n";

			$ScoreSx.='<tr>';
			for ($j=0;$j<$nARR;++$j)
				$ScoreSx.='<th  style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . ($j+1) . '</th>';
				//$ScoreSx.='<th width="10%">' . ($j+1) . '</th>';
			$ScoreSx.='</tr>' . "\n";

			$Tot=0;
		// ArrowString
			for ($i=0;$i<$nEND;++$i)
			{
				$TotSerie=0;
				$ScoreSx.='<tr>';
				//$ScoreSx.='<th><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $_REQUEST['MatchNo'] . '&amp;Volee=' . ($i+1) . '&amp;Event=' . $_REQUEST['Event'] . '">' .  ($i+1) . '</a></th>';
				$ScoreSx.='<th style="font-size:180%; font-weight:bold; text-align:center;"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $_REQUEST['MatchNo'] . '&amp;Volee=' . ($i+1) . '&amp;Event=' . $_REQUEST['Event'] . '">' .  ($i+1) . '</a></th>';
				for ($j=0;$j<$nARR;++$j)
				{
					if(array_key_exists($MyRowSx->ArrowString[$i*$nARR+$j],$MySym))
					{
						$MyPValue=$MySym[$MyRowSx->ArrowString[$i*$nARR+$j]]["P"];
						$MyNValue=$MySym[$MyRowSx->ArrowString[$i*$nARR+$j]]["N"];
					}
					else
					{
						$MyPValue='';
						$MyNValue=0;
					}
					//$ScoreSx.='<td class="NumberAlign">' . $MyPValue  . '</td>';

					$ScoreSx.='<td style="font-size:180%; font-weight:bold; text-align:center;">' . $MyPValue  . '</td>';
					$TotSerie+=$MyNValue;
				}
				$Tot+=$TotSerie;
				//$ScoreSx.='<td class="NumberAlign">' . $TotSerie. '</td>';
				//$ScoreSx.='<td class="Grassetto NumberAlign">' . $Tot;
				$ScoreSx.='<td style="font-size:180%; font-weight:bold; text-align:right;">' . $TotSerie. '</td>';
				$ScoreSx.='<td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot;
				// campi nascosti
				$ScoreSx.='</td>';
				$ScoreSx.='</tr>' . "\n";
			}
		// Tiebreak
			$ScoreSx.='<tr>';
			//$ScoreSx.='<th><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $_REQUEST['MatchNo'] . '&amp;Volee=T&amp;Event=' . $_REQUEST['Event'] . '">T.B.</a></th>';
			$ScoreSx.='<th style="font-size:180%; font-weight:bold; text-align:center;"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $_REQUEST['MatchNo'] . '&amp;Volee=T&amp;Event=' . $_REQUEST['Event'] . '">T.B.</a></th>';
			$ScoreSx.='<td colspan="' . $nARR . '">';
			$ScoreSx.='<table class="Tabella">' . "\n";
			$ScoreSx.='<tr>';
			for ($i=0;$i<$nSO;++$i)
			{
				$MyNValue=-1;
				if(array_key_exists($MyRowSx->TieBreak[$i],$MySym))
				{
					$MyPValue=$MySym[$MyRowSx->TieBreak[$i]]["P"];
					$MyNValue=$MySym[$MyRowSx->TieBreak[$i]]["N"];
				}
				else
				{
					$MyPValue='';
					$MyNValue=-1;
				}

				//$ScoreSx.='<td class="NumberAlign Light">' . ($MyNValue!=-1 ? $MyPValue : '&nbsp;') . '</td>';
				$ScoreSx.='<td style="font-size:180%; font-weight:bold; text-align:center;">' . ($MyNValue!=-1 ? $MyPValue . '&nbsp;' : '&nbsp;') . '</td>';
			}
			$ScoreSx.='</tr>' . "\n";
			$ScoreSx.='</table>' . "\n";
			$ScoreSx.='</td>';

			//$ScoreSx.='<td colspan="' . (1+(TieBreakArrows_Ind-3)) . '"></td><td class="">' . $Tot . '</td>';
			//$ScoreSx.='<td colspan="' . (1+(TieBreakArrows_Team-6)) . '"></td><td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot . '</td>';
			$ScoreSx.='<td>&nbsp;</td><td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot . '</td>';
			$ScoreSx.='</tr>' . "\n";
			//$ScoreSx.='##InsertSx##';	// Andrà sostituita con $InsertSx
			$ScoreSx.='</table>' . "\n";
		}

		if ($MyRowDx)
		{
			$ScoreDx
				= '<table class="Tabella">' . "\n"
				. '<tr>'
				. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:10%;">' . get_text('End (volee)') . '</th>'
				. '<th colspan="' . $nARR . '" style="font-size:180%; font-weight:bold; text-align:center;">' . get_text('Arrow') . '</th>'
				. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('EndScore') . '</th>'
				. '<th rowspan="2" style="font-size:180%; font-weight:bold; text-align:center; width:15%;">' . get_text('TotaleScore') . '</th>'
				. '</tr>' . "\n";

			$ScoreDx.='<tr>';
			for ($j=0;$j<$nARR;++$j)
				$ScoreDx.='<th style="font-size:180%; font-weight:bold; text-align:center; width:15%; width:10%">' . ($j+1) . '</th>';
			$ScoreDx.='</tr>' . "\n";

			$Tot=0;
		// ArrowString
			for ($i=0;$i<$nEND;++$i)
			{
				$TotSerie=0;
				$ScoreDx.='<tr>';
				$ScoreDx.='<th style="font-size:180%; font-weight:bold; text-align:center;"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $_REQUEST['MatchNo'] . '&amp;Volee=' . ($i+1) . '&amp;Event=' . $_REQUEST['Event'] . '">' .  ($i+1) . '</a></th>';
				for ($j=0;$j<$nARR;++$j)
				{
					if(array_key_exists($MyRowDx->ArrowString[$i*$nARR+$j],$MySym))
					{
						$MyPValue=$MySym[$MyRowDx->ArrowString[$i*$nARR+$j]]["P"];
						$MyNValue=$MySym[$MyRowDx->ArrowString[$i*$nARR+$j]]["N"];
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
				$ScoreDx.='<td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot;
				// campi nascosti
				$ScoreDx.='</td>';
				$ScoreDx.='</tr>' . "\n";
			}
		// Tiebreak
			$ScoreDx.='<tr>';
			$ScoreDx.='<th style="font-size:180%; font-weight:bold; text-align:center;"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?MatchNo=' . $_REQUEST['MatchNo'] . '&amp;Volee=T&amp;Event=' . $_REQUEST['Event'] . '">T.B.</a></th>';
			$ScoreDx.='<td colspan="' . $nARR . '">';
			$ScoreDx.='<table class="Tabella">' . "\n";
			$ScoreDx.='<tr>';
			for ($i=0;$i<$nSO;++$i)
			{
				$MyNValue=-1;
				if(array_key_exists($MyRowDx->TieBreak[$i],$MySym))
				{
					$MyPValue=$MySym[$MyRowDx->TieBreak[$i]]["P"];
					$MyNValue=$MySym[$MyRowDx->TieBreak[$i]]["N"];
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
		// Cella vuota e poi il totale
			//$ScoreDx.='<td colspan="' . (1+(TieBreakArrows_Team-6)) . '"></td><td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot . '</td>';
			$ScoreDx.='<td>&nbsp;</td><td style="font-size:180%; font-weight:bold; text-align:right;">' . $Tot . '</td>';
			$ScoreDx.='</tr>' . "\n";
			//$ScoreDx.='##InsertDx##';	// Andrà sostituita con $InsertDx
			$ScoreDx.='</table>' . "\n";
		}

		if(isset($_REQUEST["Volee"]) && preg_match("/^[1-4]|t$/i",$_REQUEST["Volee"]))
		{
			// InsertSx
			if (!isset($_REQUEST['OnlyOne']) || $MyRowSx->MatchNo==$_REQUEST['MatchNo'])
			{
				/*$InsertSx
					.='<table class="Tabella">' . "\n"
					. '<tr>'
					. '<td>'
					. '</td>'
					. '<td>#</td><td>1</td><td>2</td><td>3</td><td></td></tr>' . "\n"
					. '<tr>'
					. '<td>&nbsp;</td><td>' . (is_numeric($_REQUEST['Volee']) ? $_REQUEST['Volee'] : 'T.B.') . '</td>';*/
				$InsertSx
					.='<table class="Tabella">' . "\n"
					. '<tr>'
					. '<td>'
					. '</td>'
					. '<td>#</td>';

				for ($i=1;$i<=($_REQUEST['Volee']!='T' && $_REQUEST['Volee']!='t' ? $nARR : $nSO);++$i)
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
						if(array_key_exists($MyRowSx->ArrowString[$i*$nARR+$j],$MySym))
						{
							$MyPValue=$MySym[$MyRowSx->ArrowString[$i*$nARR+$j]]["P"];
							$MyNValue=$MySym[$MyRowSx->ArrowString[$i*$nARR+$j]]["N"];
						}
						else
						{
							$MyPValue='';
							$MyNValue='';
						}
						//print '<input type="text" size="1" maxlength="1" name="M_' . $MatchSx . '_' . $i . '_' . $j . '" id="M_' . $MatchSx . '_' . $i . '_' . $j . '" value="' . ($MyPValue!='10' ? (preg_match("/[0-9]+|[AXM]/i",$MyPValue) ? $MyPValue : '') : 'A') . '">';
						$InsertSx.='<input type="text" size="1" maxlength="3" name="M_' . $MatchSx . '_' . $i . '_' . $j . '" id="M_' . $MatchSx . '_' . $i . '_' . $j . '" value="' . $MyPValue . '">';
						$TxtPuntiSx[$j]='M_' . $MatchSx . '_' . $i . '_' . $j;
						$StrPuntiSx.="'" . $TxtPuntiSx[$j] . "',";
						$InsertSx.='</td>';
					}
				}
				else	// ho il tiebreak
				{
					for ($j=0;$j<$nSO;++$j)
					{
						$InsertSx.='<td>';
						if(array_key_exists($MyRowSx->TieBreak[$j],$MySym))
						{
							$MyPValue=$MySym[$MyRowSx->TieBreak[$j]]["P"];
							$MyNValue=$MySym[$MyRowSx->TieBreak[$j]]["N"];
						}
						else
						{
							$MyPValue='';
							$MyNValue='';
						}
						//print '<input type="text" size="1" maxlength="1" name="M_' . $MatchSx . '_T_' . $j . '" id="M_' . $MatchSx . '_T_' . $j . '" value="' . ($MyPValue!='10' ? (preg_match("/[0-9]+|[AXM]/i",$MyPValue) ? $MyPValue : '') : 'A') . '">';
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
				$PosArrSx=explode('|',$MyRowSx->ArrPos);
				$PosTieSx=explode('|',$MyRowSx->TiePos);

				if ($_REQUEST['Volee']<>'t' && $_REQUEST['Volee']<>'T')
				{
					$i=$_REQUEST['Volee']-1;
					for ($j=0;$j<$nARR;++$j)
					{
						$InsertSx.='<td>';
						//print '<input type="hidden" value="' . $PosArrSx[$i*3+$j] . '" name="ASx' . ($j+1) . '" id="ASx' . ($j+1) . '">';
						//print '<button name="DubbioSx" value="' . $j . '">*</button>';
						$InsertSx.='<input type="button" value="*" onClick="GestisciDubbio(\'M_' . $MatchSx . '_' . $i . '_' . $j . '\')">';
						$InsertSx.='</td>';
					}
				}
				else  //tiebreak
				{
					for ($j=0;$j<$nSO;++$j)
					{
						$InsertSx.='<td>';
						//print '<input type="hidden" value="' . $PosTieSx[$j] . '" name="ASx' . ($j+1) . '" id="ASx' . ($j+1) . '">';
						//print '<button name="DubbioSx" value="' . $j . '">*</button>';
						$InsertSx.='<input type="button" value="*" onClick="GestisciDubbio(\'M_' . $MatchSx . '_T_' . $j . '\')">';
						$InsertSx.='</td>';
					}
				}
				$InsertSx.='</tr>' . "\n";
				$InsertSx.='</table>' . "\n";
			}

		// InsertDx
			if (!isset($_REQUEST['OnlyOne']) || $MyRowSx->MatchNo==$_REQUEST['MatchNo'])
			{
				/*$InsertDx
					.='<table class="Tabella">' . "\n"
					. '<tr>'
					. '<td>'
					. '</td>'
					. '<td>#</td><td>1</td><td>2</td><td>3</td><td></td></tr>' . "\n"
					. '<tr>'
					. '<td>&nbsp;</td><td>' . (is_numeric($_REQUEST['Volee']) ? $_REQUEST['Volee'] : 'T.B.') . '</td>';*/
				$InsertDx
					.='<table class="Tabella">' . "\n"
					. '<tr>'
					. '<td>'
					. '</td>'
					. '<td>#</td>';

				for ($i=1;$i<=($_REQUEST['Volee']!='T' && $_REQUEST['Volee']!='t' ? $nARR : $nSO);++$i)
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
						if(array_key_exists($MyRowDx->ArrowString[$i*$nARR+$j],$MySym))
						{
							$MyPValue=$MySym[$MyRowDx->ArrowString[$i*$nARR+$j]]["P"];
							$MyNValue=$MySym[$MyRowDx->ArrowString[$i*$nARR+$j]]["N"];
						}
						else
						{
							$MyPValue='';
							$MyNValue='';
						}

						//print '<input type="text" size="1" maxlength="1" name="M_' . $MatchSx . '_' . $i . '_' . $j . '" id="M_' . $MatchSx . '_' . $i . '_' . $j . '" value="' . ($MyPValue!='10' ? (preg_match("/[0-9]+|[AXM]/i",$MyPValue) ? $MyPValue : '') : 'A') . '">';
						$InsertDx.='<input type="text" size="1" maxlength="3" name="M_' . $MatchDx . '_' . $i . '_' . $j . '" id="M_' . $MatchDx . '_' . $i . '_' . $j . '" value="' . $MyPValue . '">';
						$TxtPuntiDx[$j]='M_' . $MatchDx . '_' . $i . '_' . $j;
						$StrPuntiDx.="'" . $TxtPuntiDx[$j] . "',";
						$InsertDx.='</td>';
					}
				}
				else	// ho il tiebreak
				{
					for ($j=0;$j<$nSO;++$j)
					{
						$InsertDx.='<td>';
						if(array_key_exists($MyRowDx->TieBreak[$j],$MySym))
						{
							$MyPValue=$MySym[$MyRowDx->TieBreak[$j]]["P"];
							$MyNValue=$MySym[$MyRowDx->TieBreak[$j]]["N"];
						}
						else
						{
							$MyPValue='';
							$MyNValue='';
						}
						//print '<input type="text" size="1" maxlength="1" name="M_' . $MatchSx . '_T_' . $j . '" id="M_' . $MatchSx . '_T_' . $j . '" value="' . ($MyPValue!='10' ? (preg_match("/[0-9]+|[AXM]/i",$MyPValue) ? $MyPValue : '') : 'A') . '">';
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
				$PosArrDx=explode('|',$MyRowDx->ArrPos);
				$PosTieDx=explode('|',$MyRowDx->TiePos);

				if ($_REQUEST['Volee']<>'t' && $_REQUEST['Volee']<>'T')
				{
					$i=$_REQUEST['Volee']-1;
					for ($j=0;$j<$nARR;++$j)
					{
						$InsertDx.='<td>';
						//print '<input type="hidden" value="' . $PosArrSx[$i*3+$j] . '" name="ASx' . ($j+1) . '" id="ASx' . ($j+1) . '">';
						//print '<button name="DubbioSx" value="' . $j . '">*</button>';
						$InsertDx.='<input type="button" value="*" onClick="GestisciDubbio(\'M_' . $MatchDx . '_' . $i . '_' . $j . '\')">';
						$InsertDx.='</td>';
					}
				}
				else  //tiebreak
				{
					for ($j=0;$j<$nSO;++$j)
					{
						$InsertDx.='<td>';
						//print '<input type="hidden" value="' . $PosTieSx[$j] . '" name="ASx' . ($j+1) . '" id="ASx' . ($j+1) . '">';
						//print '<button name="DubbioSx" value="' . $j . '">*</button>';
						$InsertDx.='<input type="button" value="*" onClick="GestisciDubbio(\'M_' . $MatchDx . '_T_' . $j . '\')">';
						$InsertDx.='</td>';
					}
				}
				$InsertDx.='</tr>' . "\n";
				$InsertDx.='</table>' . "\n";
			}
		// SemaforoSx
			$SemaforoSx= '<table class="Tabella">' . "\n";
			//$SemaforoSx.='<tr><td class="Title">' . (strtoupper($_REQUEST["Volee"])=='T' ? "Tie Break" : get_text('End (volee)') . ' ' . ($_REQUEST["Volee"]) ) . '</td></tr>';
			if ($_REQUEST['Volee']=='T')
				$SemaforoSx.='<tr><td class="Title">Tie Break</td></tr>';
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
						if(@array_key_exists (($i*$nARR+$j), $PosArrSx))
							$Tmp .= "&amp;Arrows[]=" .  $PosArrSx[$i*$nARR+$j];

					}

					if($MyTargetHitMiss)
						$Tmp.='&amp;HMOUT=1';
					else if($MyTargetField)
						$Tmp.='&amp;FIELD=1';

					//$SemaforoSx.='<tr><td class="FontMedium Bold">' . ($i+1) . '</td><td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Size=120' . $Tmp . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target" border="0"></td></tr>';
					$SemaforoSx.='<tr><td class="FontMedium Bold">' . ($i+1) . '</td><td class="Center"></td></tr>';
				}
			}
			else
			{
				/*for($j=0;$j<TieBreakArrows_Team;$j++)
				{
					$SemaforoSx.='<tr><td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Size=150&amp;Arrow=' . $PosTieSx[$j] . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target"></td></tr>';
				}*/
				for ($j=0;$j<$nSO;$j+=3)
				{
					$Tmp='';
					for($x=0;$x<3;++$x)
					{
						if (@array_key_exists($x+$j,$PosTieSx))
							$Tmp.= "&amp;Arrows[]=" . $PosTieSx[$j+$x];
					}

					if($MyTargetHitMiss)
						$Tmp.='&amp;HMOUT=1';
					else if($MyTargetField)
						$Tmp.='&amp;FIELD=1';
					//$SemaforoSx.='<tr><td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Size=150' . $Tmp . ($MyTargetComplete ? '&amp;complete' : '') . '" class="Target"></td></tr>' . "\n";
					$SemaforoSx.='<tr><td class="Center"></td></tr>' . "\n";
				}
			}
			$SemaforoSx.='</table>' . "\n";

		// SemaforoDx
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
					//$SemaforoDx.='<tr><td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Size=120' . $Tmp . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target" border="0"></td><td class="FontMedium Bold">' . ($i+1) . '</td></tr>';
					$SemaforoDx.='<tr><td class="Center"></td><td class="FontMedium Bold">' . ($i+1) . '</td></tr>';
				}
			}
			else
			{
				/*for($j=0;$j<TieBreakArrows_Team;$j++)
				{
					$SemaforoDx.='<tr><td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Size=150&amp;Arrow=' . $PosTieDx[$j] . ($MyTargetComplete ? '&amp;complete': '') .  '" class="Target"></td></tr>';
				}*/
				for ($j=0;$j<$nSO;$j+=3)
				{
					$Tmp='';
					for($x=0;$x<3;++$x)
					{
						if (@array_key_exists($x+$j,$PosTieDx))
							$Tmp.= "&amp;Arrows[]=" . $PosTieDx[$j+$x];

					//	print $Tmp.'<br>';

					}
					//print $Tmp;
					if($MyTargetHitMiss)
						$Tmp.='&amp;HMOUT=1';
					else if($MyTargetField)
						$Tmp.='&amp;FIELD=1';
					//$SemaforoDx.='<tr><td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/target.php?Size=150' . $Tmp . ($MyTargetComplete ? '&amp;complete' : '') . '" class="Target"></td></tr>' . "\n";
					$SemaforoDx.='<tr><td class="Center"></td></tr>' . "\n";
				}

			}
			$SemaforoDx.='</table>' . "\n";
		}

?>
<table class="Tabella">
<?php // Nomi e nazioni ?>
<tr>
<?php
/*<td width="17%" class="Center Grassetto"><?php print $MyRowSx->NationCode; ?><br><?php print $MyRowSx->Nation; ?></td>
<td width="32%" class="Center Grassetto"><?php print strtoupper($MyRowSx->Name) . ' ' . $MyRowSx->FirstName;?></td>
<!-- <td width="2%" rowspan="3"></td> -->
<td width="32%" class="Center Grassetto"><?php print strtoupper($MyRowDx->Name) . ' ' . $MyRowDx->FirstName;?></td>
<td width="17%" class="Center Grassetto"><?php print $MyRowDx->NationCode; ?><br><?php print $MyRowDx->Nation; ?></td>*/
?>

<td style="font-size:150%; font-weight:bold; text-align:center; width:17%;"><?php print $MyRowSx->NationCode; ?></td>
<td style="font-size:180%; font-weight:bold; text-align:center; width:32%;"><?php print strtoupper($MyRowSx->Nation) ;?></td>
<!-- <td width="2%" rowspan="3"></td> -->
<td style="font-size:180%; font-weight:bold; text-align:center; width:32%;"><?php print strtoupper($MyRowDx->Nation);?></td>
<td style="font-size:150%; font-weight:bold; text-align:center; width:17%;"><?php print $MyRowDx->NationCode; ?></td>
</tr>
<?php // Fine Nomi e nazioni ?>

<tr>
<td rowspan="3"><?php print $SemaforoSx; ?></td>
<td><?php print $ScoreSx; ?></td>
<td><?php print $ScoreDx; ?></td>
<td rowspan="3"><?php print $SemaforoDx; ?></td>
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
		for($j=0; $j<$nSO;++$j)
		{
			if(@array_key_exists($j,$PosTieSx))
				$Tmp .= "&amp;Arrows[]=" . $PosTieSx[$j];
		}
	}

	if($MyTargetHitMiss)
		$Tmp.='&amp;HMOUT=1';
	else if($MyTargetField)
		$Tmp.='&amp;FIELD=1';

	//echo '<input name="TargetSx" type="image" src="'.$CFG->ROOT_DIR.'Common/target.php?Size=300&amp;'.$Tmp.'&amp;noborder'.($MyTargetComplete ? '&amp;complete': '').'">';
?>

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
		for($j=0; $j<$nSO;++$j)
		{
			if(@array_key_exists($j,$PosTieDx))
				$Tmp .= "&amp;Arrows[]=" . $PosTieDx[$j];
		}
	}
	if($MyTargetHitMiss)
		$Tmp.='&amp;HMOUT=1';
	else if($MyTargetField)
		$Tmp.='&amp;FIELD=1';
//echo '<input name="TargetDSx" type="image" src="'.$CFG->ROOT_DIR.'Common/target.php?Size=300&amp;'.$Tmp.'&amp;noborder'.($MyTargetComplete ? '&amp;complete': '').'">';

?>
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