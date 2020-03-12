<?php
/*
													- DeleteRow.php -
	Elimina un partecipante e ritorna il suo id
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');

	if (!isset($_REQUEST['id']) || !CheckTourSession())	{
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite);

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		$indFEvent=$teamFEvent=$country=$div=$cl=$subCl=$zero=null;
		$recalc=Params4Recalc($_REQUEST['id']);
		if ($recalc!==false)
		{
			$recalc=true;
			list($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero)=$recalc;
		}

		if($Id=intval($_REQUEST['id'])) {
			deleteArcher($Id, true, true);
		}

	// ricalcolo
		if ($recalc)
		{
			RecalculateShootoffAndTeams($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero);

		// rank di classe x tutte le distanze
			$q="SELECT ToNumDist FROM Tournament WHERE ToId={$_SESSION['TourId']}";
			$r=safe_r_sql($q);
			$tmpRow=safe_fetch($r);
			for ($i=0; $i<$tmpRow->ToNumDist;++$i)
			{
				CalcQualRank($i,$div.$cl);
			}

		// rifaccio gli assoluti
			$Errore=MakeIndAbs();
		}
	}

	header('Location: index.php?ord=' . $_REQUEST['ord'].'&dir='.$_REQUEST['dir'].'&AllTargets='.$_REQUEST['AllTargets']);
	exit;
?>