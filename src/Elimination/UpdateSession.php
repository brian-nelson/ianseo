<?php
/*
													- UpdateTargetNo.php -
	La pagina aggiorna il TargetNo del tizio in Qualifications se la sessione è settata
*/

define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}
	checkACL(AclEliminations,AclReadWrite, false);

	$Errore=0;
	$Id='#';
	$Ses='0';

	if (!IsBlocked(BIT_BLOCK_ELIM))
	{
		foreach ($_REQUEST as $Key => $Value)
		{
			if (substr($Key,0,2)=='d_')
			{
				$Fase='';
				$Evento='';
				$Torneo='';

				list( , , , $Fase, $Evento, $Torneo) = explode('_',$Key);
				$Id=$Fase.'_'.$Evento.'_'.$Torneo;
				$Ses=$Value;

				$sessions=GetSessions('E');
				$trovato=false;
				foreach ($sessions as $s)
				{
					if ($s->SesOrder==$Ses)
					{
						$trovato=true;
						break;
					}
				}

				if ($Ses==0)
					$trovato=true;

				if (!$trovato)
				{
					$Errore=1;
				}
				else
				{
					$Update
						= "UPDATE Eliminations SET "
						. "ElSession=" . StrSafe_DB($Ses) . " "
						. "WHERE "
							. "ElElimPhase=" . $Fase . " AND ElEventCode='".$Evento."' AND ElTournament=". $Torneo . " ";
					$RsUp=safe_w_sql($Update);
					if (!$RsUp)
						$Errore=1;

				}
			}
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<id><![CDATA[' . $Id . ']]></id>' . "\n";
	print '<ses>' . $Ses . '</ses>' . "\n";
	print '</response>' . "\n";
?>