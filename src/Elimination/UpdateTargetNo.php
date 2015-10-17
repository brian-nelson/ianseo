<?php
/*
													- UpdateTargetNo.php -
	La pagina aggiorna il TargetNo del tizio in Qualifications se la sessione è settata
*/

function checkOnlyPattern($v)
{
	if (trim($v)=='')
		return 0;

	if(preg_match('/^[0-9]{1,' . TargetNoPadding . '}[A-Z]{1}$/i',strtoupper($v)))
	{
		$num=intval(substr($v,0,-1));

		if ($num>0)
			return 0;
		else
			return 1;
	}
	else
		return 1;
}

define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$xml='';

	$CommandSymbols=array('+');
	$Command='';
	$num='';		// numero del bersaglio per la piazzola usata
	$letter='';		// lettera della piazzola
	$which='';		// campo da cui è partito il tutto

	$toWrite=array();

	if (!IsBlocked(BIT_BLOCK_ELIM))
	{
		foreach ($_REQUEST as $Key => $Value)
		{
			if (substr($Key,0,2)=='d_')
			{
				$Fase='';
				$Evento='';
				$Rank='';
				$Torneo='';

				list( , , , $Fase, $Evento, $Rank,$Torneo) = explode('_',$Key);
				$Id=$Fase.'_'.$Evento.'_'.$Rank.'_'.$Torneo;
				$which=$Id;

				$session=null;
				$q="
					SELECT
						ElSession ,Session.*
					FROM
						Eliminations
						LEFT JOIN
							Session
						ON ElSession=SesOrder AND ElTournament=SesTournament AND SesType='E'
					WHERE
						ElTournament={$Torneo} AND ElEventCode='{$Evento}' AND ElElimPhase={$Fase} AND ElQualRank={$Rank}
				";

				$r=safe_r_sql($q);

				if ($r && safe_num_rows($r)==1)
				{
					$session=safe_fetch($r);
				}

			/* check della stringa */

			// vuoto => ok
				if (trim($Value)!='')
				{
					if (in_array(substr($Value,-1),$CommandSymbols))
					{
						$Command=substr($Value,-1);
						$Value=substr($Value,0,-1);
					}
				}

			// indipendentemente dalla sessione controllo solo la forma del valore
				$Errore=checkOnlyPattern($Value);

			/* preparo le scritture*/
				if ($Errore==0)
				{
					if (trim($Value)=='')
						$Value='';
					else
						$Value=str_pad(strtoupper($Value),(TargetNoPadding+1),'0',STR_PAD_LEFT);

					$num=intval(substr($Value,0,-1));
					$letter=substr($Value,-1);

				// senza Command scrivo secco
					if ($Command=='')
					{
						$toWrite[]=array('value'=>$Value,'key'=>$Id);
					}
					else	// un comando che si riperquote su più piazzole (stesso evento e stessa fase cambia la rank)
					{
					/*
					 * Mi interessano le righe del girone e dell'evento con rank>= a quella del campo
					 */

						$q="
							SELECT ElElimPhase,ElEventCode,ElTournament,ElQualRank
							FROM
								Eliminations
							WHERE
								ElElimPhase={$Fase} AND ElEventCode='{$Evento}' AND ElTournament={$Torneo} AND ElQualRank>={$Rank}
						";
						$rs=safe_r_sql($q);

						if ($rs && safe_num_rows($rs)>0)
						{
						// se non è impostato il numero di persone per i bersagli della sessione (ovviamente se diversa da 0) ipotizzo 4 persone (D)
							$lastLetter='D';
							if (!is_null($session) && $session->ElSession!=0)
							{
								$lastLetter=chr(64+$session->SesAth4Target);
							}

							$letters=range('A',$lastLetter);

						/*	print '<pre>';
							print_r($letters);
							print '</pre>';*/

							$curNum=$num;
							$curLetter=$letter;

							while ($row=safe_fetch($rs))
							{
								$toWrite[]=array(
									'value'=>str_pad(strtoupper($curNum.$curLetter),(TargetNoPadding+1),'0',STR_PAD_LEFT),
									'key'=>$row->ElElimPhase.'_'.$row->ElEventCode.'_'.$row->ElQualRank.'_'.$row->ElTournament
								);

							// incremento la lettera
								++$curLetter;

							// se esco dal range (non c'è in $letters) riporto su A e incremento il numero
								if (!in_array($curLetter,$letters))
								{
									++$curNum;
									$curLetter='A';
								}
							}
						}
						else
						{
							$Errore=1;
						}
					}

					/*print '<pre>';
					print_r($toWrite);
					print '</pre>';*/

				// scrivo e preparo l'xml
					if (count($toWrite)>0)
					{
						foreach ($toWrite as $tw)
						{
							list($Fase, $Evento, $Rank,$Torneo) = explode('_',$tw['key']);

							$q="
								UPDATE Eliminations
								SET
									ElTargetNo='{$tw['value']}'
								WHERE
									ElElimPhase={$Fase} AND ElEventCode='{$Evento}' AND ElQualRank={$Rank} AND ElTournament={$Torneo}
							";
							$r=safe_w_sql($q);

							$xml.='
								<field>
									<key>'.$tw['key'].'</key>
									<value>' . $tw['value'] .'</value>
									<fieldError>' . (!$r ? 1 : 0). '</fieldError>
								</field>
							';
						}
					}
				}

				/*if (trim($Value)=='' || preg_match('/^[0-9]{1,' . TargetNoPadding . '}[a-z]{1}$/i',$Value))
				{
					if (trim($Value)=='')
						$PadValue='';
					else
						$PadValue=str_pad(strtoupper($Value),(TargetNoPadding+1),'0',STR_PAD_LEFT);

					$Update
						= "UPDATE Eliminations SET "
						. "ElTargetNo=" . StrSafe_DB($PadValue) . " "
						. "WHERE "
							. "ElElimPhase=" . $Fase . " AND ElEventCode='".$Evento."' AND ElTournament=". $Torneo . " AND ElQualRank=".$Rank . " ";
					$RsUp=safe_w_sql($Update);
					if (debug)
						print $Update . '<br>';
				}
				else
					$Errore=1;*/

			}
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<which>' . $which . '</which>' . "\n";
	print $xml;
	print '</response>' . "\n";
?>