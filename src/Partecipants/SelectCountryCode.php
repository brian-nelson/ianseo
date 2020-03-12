<?php
/*
													- SelectCountryCode.php -
	Compila il nome della nazione partendo dal codice della stessa
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!isset($_REQUEST['which']) && !isset($_REQUEST['Code'])||  !CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite,false);

	$Errore=0;
	$Id = 0;
	$Name='';

	if (strlen($_REQUEST['Code'])>0)
	{
	// cerco il codice nazione nel db
		$Select
			= "SELECT CoId,CoName,CoTournament "
			. "FROM Countries "
			. "WHERE CoCode=" . StrSafe_DB($_REQUEST['Code']) . " AND CoTournament in (-1,{$_SESSION['TourId']}) order by CoTournament desc";
		$Rs=safe_r_sql($Select);

		if ($Rs)
		{
			if (safe_num_rows($Rs)==1)
			{
				$MyRow=safe_fetch($Rs);
				$Id=($MyRow->CoTournament==-1 ? 0 : $MyRow->CoId);
				$Name=$MyRow->CoName;
			}
		}
		else
			$Errore=1;
	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<which><![CDATA[' . $_REQUEST['which'] . ']]></which>';
	print '<id><![CDATA[' . $Id . ']]></id>';
	print '<name><![CDATA[' . $Name . ']]></name>';
	print '</response>';
?>