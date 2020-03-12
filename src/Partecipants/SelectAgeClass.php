<?php
/*
													- SelectAgeClass.php -
	Viene usata quando si cambia la classe
	Scatta al change e rigenera la tendina della classe gara
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	$Errore=0;
	$Classes='#';
	$isAthlete=0;

	if (!isset($_REQUEST['d_e_EnAgeClass']))
		$Errore=1;

	if ($Errore==0)
	{
	// verifico che l'ageclass esista (se è diversa da '')
		if (trim($_REQUEST['d_e_EnAgeClass'])!='--')
		{

			$Select
				= "SELECT * FROM Classes WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId'])
					. " AND ClId=" . StrSafe_DB($_REQUEST['d_e_EnAgeClass'])
					. (empty($_REQUEST['d_e_EnDivision']) ? '' : " AND (ClDivisionsAllowed='' or find_in_set(".StrSafe_DB($_REQUEST['d_e_EnDivision']).", ClDivisionsAllowed))" );
			$Rs=safe_r_sql($Select);
//print $Select;exit;
			if (debug)
				print $Select . '<br><br>';

			if (safe_num_rows($Rs)==1)
			{
				$Row = safe_fetch($Rs);
				$Classes = $Row->ClValidClass;
				$isAthlete = $Row->ClAthlete;
			}
			else
				$Errore=1;
		}
	}


//	if (!debug)
	header('Content-Type: text/xml');

	print '<response>' ;
	print '<error>' . $Errore . '</error>' ;
	print '<class><![CDATA[' . $_REQUEST['d_e_EnAgeClass'] . ']]></class>' ;
	print '<classes><![CDATA[' . $Classes . ']]></classes>' ;
	print '<athlete>' . ($isAthlete ? '1' : '0') . '</athlete>' ;
	print '</response>' ;
	flush();
?>