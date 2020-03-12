<?php
	set_time_limit(360);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Various.inc.php');
	require_once 'Fun_Tournament.local.inc.php';
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Final/Fun_Final.local.inc.php');

	include('Common/Fun_Export.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$ToCode = '';

	$Select
		= "SELECT ToCode "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)==1)
	{
		$row=safe_fetch($Rs);
		$ToCode=$row->ToCode;
	}

	if ($ToCode=='')
		exit;

// Cerco gli eventi delle finali
	$FinEventInd=0;
	$FinEventTeam=0;
	$Select
		= "SELECT COUNT(EvCode) AS Quanti,EvTeamEvent FROM Events "

		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . "   AND EvShootOff=1 "
		. "GROUP BY EvTeamEvent ";
	$Rs=safe_r_sql($Select);

	if ($Rs)
	{
		while ($RowEv=safe_fetch($Rs))
		{
			if ($RowEv->EvTeamEvent=='0')
				$FinEventInd=$RowEv->Quanti;
			elseif($RowEv->EvTeamEvent=='1')
				$FinEventTeam=$RowEv->Quanti;
		}
	}

	// Cerco gli eventi delle eliminatorie
	$ElimEvent=array(1=>0,2=>0);

	for ($i=1;$i<=2;++$i)
	{
		$Select
			= "SELECT EvCode FROM Events "
			. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvElim" . $i . ">0 AND EvE" . $i . "ShootOff=1 ";
		$Rs=safe_r_sql($Select);

		if ($Rs && safe_num_rows($Rs)>0)
		{
			$ElimEvent[$i]=safe_num_rows($Rs);
		}
	}

	list($asc,)=ExportASC();

/*
 * preparo il vettore con i file che non sono pdf
 * Mentre lo faccio inizializzo il vettore per ciclare tra i pdf
 */
	$pdfFiles=array
	(
		$ToCode . '_report.pdf'=>dirname(__FILE__) . '/FinalReport/PDFReport.php',
		$ToCode . '.pdf'=>dirname(dirname(__FILE__)) . '/Qualification/PrnIndividual.php',
		$ToCode . '_team.pdf'=>dirname(dirname(__FILE__)) . '/Qualification/PrnTeam.php'
	);

	$Tour = array();

	$Tour[$ToCode . '.ianseo'] = gzcompress(serialize(export_tournament($_SESSION['TourId'],false)));
	$Tour[$ToCode . '.asc'] = $asc;
	$Tour[$ToCode . '.lst'] = ExportLSTInd();
	$Tour[$ToCode . '_team.lst'] = ExportLSTTeam();


	if ($ElimEvent[1]>0 || $ElimEvent[2]>0)
	{
		//$Tour[$ToCode . '_elim.pdf'] = URLWrapper($CFG->ROOT_DIR .'Elimination/PrnIndividual.php?Lang=' . SelectLanguage(). '&TourId=' . $_SESSION['TourId'] . '&ToFitarco=ignored&Dest=S');
		$pdfFiles[$ToCode . '_elim.pdf']=dirname(dirname(__FILE__)) . '/Elimination/PrnIndividual.php';
	}

	if ($FinEventInd>0)
	{
		$Tour[$ToCode . '_rank.lst'] = ExportLSTFinInd();
		//$Tour[$ToCode . '_rank.pdf'] = URLWrapper( $CFG->ROOT_DIR . 'Final/Individual/PrnRanking.php?Lang=' . SelectLanguage(). '&TourId=' . $_SESSION['TourId'] . '&ToFitarco=ignored&Dest=S');
		//$Tour[$ToCode . '_grid.pdf'] = URLWrapper( $CFG->ROOT_DIR . 'Final/Individual/PrnBracket.php?Lang=' . SelectLanguage(). '&TourId=' . $_SESSION['TourId'] . '&ToFitarco=ignored&Dest=S');

		$pdfFiles[$ToCode . '_abs.pdf']=dirname(dirname(__FILE__)) . '/Qualification/PrnIndividualAbs.php';
		$pdfFiles[$ToCode . '_rank.pdf']=dirname(dirname(__FILE__)) . '/Final/Individual/PrnRanking.php';
		$pdfFiles[$ToCode . '_grid.pdf']=dirname(dirname(__FILE__)) . '/Final/Individual/PrnBracket.php';
	}

	if ($FinEventTeam>0)
	{
		$Tour[$ToCode . '_rank_team.lst'] = ExportLSTFinTeam();
		//$Tour[$ToCode . '_rank_team.pdf'] = URLWrapper( $CFG->ROOT_DIR . 'Final/Team/PrnRanking.php?Lang=' . SelectLanguage(). '&TourId=' . $_SESSION['TourId'] . '&ToFitarco=ignored&Dest=S');
		//$Tour[$ToCode . '_grid_team.pdf'] = URLWrapper( $CFG->ROOT_DIR . 'Final/Team/PrnBracket.php?Lang=' . SelectLanguage(). '&TourId=' . $_SESSION['TourId'] . '&ToFitarco=ignored&Dest=S');

		$pdfFiles[$ToCode . '_abs_team.pdf']=dirname(dirname(__FILE__)) . '/Qualification/PrnTeamAbs.php';
		$pdfFiles[$ToCode . '_rank_team.pdf']=dirname(dirname(__FILE__)) . '/Final/Team/PrnRanking.php';
		$pdfFiles[$ToCode . '_grid_team.pdf']=dirname(dirname(__FILE__)) . '/Final/Team/PrnBracket.php';
	}

// ora genero le stringhe dei pdf e accodo a $Tour
//	print '<pre>';
//	print_r($pdfFiles);
//	print '</pre>';
//	exit;
	if (count($pdfFiles)>0)
	{
	/*
	 * IMPORTANTE!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	 * Dentro ai vari pdf che vengono qui inclusi occorre evitare di utilizzare le variabili
	 * $pdfKey e $file perchè a causa dell'inclusione finirebbero per sovrascrivere i valori delle variabili
	 * omonime qui dentro.
	 * Ad esempio prima di questa nota al posto di $pdfKey c'era $k che però in PrnRanking.php veniva usata
	 * e quindi il suo valore alla riga
	 * $Tour[$k]=$__ExportPDF; (ora $Tour[$pdfKey]=$__ExportPDF;) non era quello della chiave di $pdfFiles ma
	 * zero (per come ciclava lo script incluso)
	 *
	 * Quindi per ora da NON usare dentro i vari pdf:
	 * $pdfFiles, $pdfKey, $file
	 */
		foreach ($pdfFiles as $pdfKey=>$file) {
			$Tour[$pdfKey]=getPDFforExp($file);
		}
		unset($__ExportPDF);
	}
//	print '<pre>';
//	print_r(array_keys($Tour));
//	print '</pre>';exit;
	//print 'qui';exit;

	header('Content-type: application/octet-stream');
	header("Content-Disposition: attachment; filename=\"" . $ToCode . '.exp' . "\"");

// serializzo e comprimo
	/*$fp=fopen($_SERVER['DOCUMENT_ROOT'] . '/Tournament/TmpDownload/x.exp','w');
	fputs($fp,gzcompress(serialize($Tour),9));
	fclose($fp);*/

	print gzcompress(serialize($Tour),9);

function getPDFforExp($file) {
	$__ExportPDF='';
	require_once($file);
	return $__ExportPDF;

}

?>