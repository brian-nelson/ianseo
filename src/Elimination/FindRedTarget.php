<?php
/*
													- FindRedTarget.php -
	Cerca i targetno doppi e ritorna l'elenco.
	La funzione ajax si preoccuperà di colorare i doppioni
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');

	$flags=array();

	function findClone()
	{
		global $flags;

		$Select="
			SELECT
				CONCAT(e1.ElElimPhase,'_',e1.ElEventCode,'_',e1.ElQualRank,'_',e1.ElTournament) AS `Id`,e1.ElSession,IFNULL(sq.q,0) AS `q`
			FROM
				Eliminations AS e1
				LEFT JOIN
					(
						SELECT
							ElElimPhase,ElTargetNo,ElEventCode,ElSession,COUNT(ElTargetNo) AS `q`
						FROM
							Eliminations
						WHERE
							ElTargetNo<>'' AND ElTournament={$_SESSION['TourId']}
						GROUP BY
							ElTargetNo,ElElimPhase,ElSession
					) AS sq
				ON e1.ElTargetNo=sq.ElTargetNo AND e1.ElElimPhase=sq.ElElimPhase  AND e1.ElSession=sq.ElSession
			WHERE
				e1.ElTournament={$_SESSION['TourId']}
			ORDER BY
				e1.ElElimPhase ASC,e1.ElQualRank ASC
		";
		//print $Select;
		$Rs=safe_r_sql($Select);

		if (safe_num_rows($Rs)>0)
		{
			while ($MyRow=safe_fetch($Rs))
			{
				$flags[$MyRow->Id]['quantity']=($MyRow->q>1 ? 0:1);
				$flags[$MyRow->Id]['out']=1;
				/*$xml
					.= '<target>' . "\n"
					 	. '<id>' . $MyRow->Id . '</id>' . "\n"
					 	. '<good>' . ($MyRow->q>1 ? 0:1). '</good>'
					 	. '<q>' . ($MyRow->q). '</q>'
					 . '</target>' . "\n";*/
			}
		}
	}

	function findOutOfRange()
	{
		global $flags;
	/*
	 * Per ogni sessione eliminatoria con i parametri settati calcolo il range corretto dei suoi bersagli.
	 * Poi tra le righe che agganciano una sessione controllo la bontà del target impostato
	 */
		$sessions=GetSessions('E');

		$ranges=array();

		foreach ($sessions as $s)
		{
		// se ogni parametro è >0
			if ($s->SesTar4Session*$s->SesAth4Target*$s->SesFirstTarget>0)
			{
				$range=array();

				for ($tt=$s->SesFirstTarget;$tt<($s->SesTar4Session+$s->SesFirstTarget);++$tt)
				{
					for ($aa=1;$aa<=$s->SesAth4Target;++$aa)
					{
						$range[]=str_pad($tt,TargetNoPadding,'0',STR_PAD_LEFT) . chr($aa+64);
					}
				}

				$ranges[$s->SesOrder]=$range;
			}
		}

	// adesso controllo i bersagli
		$q="
			SELECT CONCAT(ElElimPhase,'_',ElEventCode,'_',ElQualRank,'_',ElTournament) AS `Id`,ElSession,ElTargetNo
			FROM
				Eliminations
			WHERE
				ElTournament={$_SESSION['TourId']}
		";
		$r=safe_r_sql($q);

		while ($MyRow=safe_fetch($r))
		{
			if ($MyRow->ElSession!=0 && trim($MyRow->ElTargetNo)!='')
			{
				$flags[$MyRow->Id]['out']=(!in_array($MyRow->ElTargetNo,$ranges[$MyRow->ElSession]) ? 0:1);
			}
		}
	}

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$xml='';

	findClone();
	findOutOfRange();

	foreach ($flags as $k=>$v)
	{
		//print $k . ': ' . $v['quantity']*$v['out'].'<br>';
		$xml
			.= '<target>' . "\n"
			 	. '<id>' . $k . '</id>' . "\n"
			 // 0 male 1 buono
			 	. '<good>' . ($v['quantity']*$v['out']). '</good>'
			 . '</target>' . "\n";
	}


	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print $xml;
	print '</response>' . "\n";
?>