<?php
/*
													- AddPrice.php -
	Aggiunge il prezzo per una serie di categorie
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_DB.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}
    checkACL(AclCompetition, AclReadWrite,false);

	$Errore=0;

	$Tuple = array();
	$Rules = array();

	$Tuple_Index=0;
	$xml = '';
	if (!IsBlocked(BIT_BLOCK_ACCREDITATION))
	{
		foreach ($_REQUEST['New_Division'] as $DivKey => $DivValue)
		{
			foreach ($_REQUEST['New_Class'] as $ClKey => $ClValue)
			{
				$Tuple[$Tuple_Index]
					= "("
					. "'',"
					. StrSafe_DB($_SESSION['TourId']) . ", "
					. StrSafe_DB($DivValue . $ClValue) . ", "
					. StrSafe_DB(str_replace(",",".",$_REQUEST['New_Price'])) . ""
					. ")";
				$Rules[$Tuple_Index] = $DivValue . $ClValue;

				++$Tuple_Index;
			}
		}

		foreach ($Tuple as $Key => $Value)
		{
			$Insert
				= "INSERT INTO AccPrice (APId,APTournament,APDivClass,APPrice) "
				. "VALUES" . $Value;
			$RsIns=safe_w_sql($Insert);

			if (debug) print $Insert . '<br>';

			if (safe_w_affected_rows()==1)
				$xml.= '<new_rule>' . $Rules[$Key] . '</new_rule>' . "\n";
		}
	}
	if ($xml=='')
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<confirm_msg>' . get_text('MsgAreYouSure') . '</confirm_msg>' . "\n";
	print '<new_price>' . NumFormat(str_replace(",",".",$_REQUEST['New_Price']),2) . '</new_price>' . "\n";
	print $xml;
	print '</response>' . "\n";

?>