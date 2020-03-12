<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');


	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly,false);

	$Errore=0;
	$xml='';

	$Matr = (isset($_REQUEST['d_e_EnCode']) ? $_REQUEST['d_e_EnCode'] : '');
	//$FirstName = (isset($_REQUEST['d_e_EnFirstName']) ? $_REQUEST['d_e_EnFirstName'] : '');
	//$Name = (isset($_REQUEST['d_e_EnName']) ? $_REQUEST['d_e_EnName'] : '');
	$Archer = (isset($_REQUEST['d_e_Archer']) ? $_REQUEST['d_e_Archer'] : '');
	$Country = (isset($_REQUEST['d_c_CoCode']) ? $_REQUEST['d_c_CoCode'] : '');
	$Div = (isset($_REQUEST['d_e_Div']) ? $_REQUEST['d_e_Div'] : '');
	$Cl = (isset($_REQUEST['d_e_Class']) ? $_REQUEST['d_e_Class'] : '');
	$SubCl = (isset($_REQUEST['d_e_SubCl']) ? $_REQUEST['d_e_SubCl'] : '');

	$Filter = '';

	if ($Matr!='')
		$Filter.= " AND LueCode = " . StrSafe_DB($Matr) . " ";

	if (strlen($Archer)>=3)
		$Filter.= " AND CONCAT(LueFamilyName,' ',LueName) LIKE " . StrSafe_DB(stripslashes($Archer) . "%") . " ";

	if ($Country!='')
		$Filter.= " AND LueCountry = " . StrSafe_DB(stripslashes($Country)) . " ";

	if ($Div!='' && ($Matr!='' || strlen($Archer)>=3 || $Country!=''))
		$Filter.= " AND LueDivision = " . StrSafe_DB($Div) . " ";

	if ($Cl!='' && ($Matr!='' || strlen($Archer)>=3 || $Country!=''))
		$Filter.= " AND LueClass = " . StrSafe_DB($Cl) . " ";

	if ($SubCl!='' && ($Matr!='' || strlen($Archer)>=3 || $Country!=''))
		$Filter.= " AND LueSubClass = " . StrSafe_DB($SubCl) . " ";

	$Select
		= "SELECT * "
		. "FROM LookUpEntries "
		. "WHERE " . ($Filter=='' ? "1=0 " : "1=1 ") . $Filter . " AND LueDefault='1' "
		. "ORDER BY LueFamilyName,LueName ";
	$Rs=safe_r_sql($Select);

	if (debug)
		print $Select . '<br><br>';

	if ($Rs)
	{
		if (safe_num_rows($Rs)>0)
		{
			while ($MyRow=safe_fetch($Rs))
			{
				$xml
					.= '<ath>'
					. '<code>' . (trim($MyRow->LueCode)!='' ? $MyRow->LueCode : '#') . '</code>'
					. '<firstname><![CDATA[' . (trim($MyRow->LueFamilyName)!='' ? $MyRow->LueFamilyName : '#') . ']]></firstname>'
					. '<name><![CDATA[' . (trim($MyRow->LueName)!='' ? $MyRow->LueName : '#') . ']]></name>'
					. '<country_code>' . (trim($MyRow->LueCountry)!='' ? $MyRow->LueCountry : '#') . '</country_code>'
					. '<country_name><![CDATA[' . (trim($MyRow->LueCoShort)!='' ? $MyRow->LueCoShort : '#') . ']]></country_name>'
					. '<division>' . (trim($MyRow->LueDivision)!='' ? $MyRow->LueDivision : '#') . '</division>'
					. '<ageclass>' . (trim($MyRow->LueClass)!='' ? $MyRow->LueClass : '') . '</ageclass>'
					. '<subclass>' . (trim($MyRow->LueSubClass)!='' ? str_pad($MyRow->LueSubClass,2,'0',STR_PAD_LEFT) : '0') . '</subclass>'
					. '<status>' . (trim($MyRow->LueStatus)!='' ? ($_SESSION['TourRealWhenFrom']>$MyRow->LueStatusValidUntil && $MyRow->LueStatusValidUntil!='0000-00-00' ? 5 : $MyRow->LueStatus) : '0') . '</status>'
					. '</ath>';
			}
		}
	}
	else
	{
		$Errore=1;
	}

	if (!debug)
	{
		header('Content-Type: text/xml');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		/*print '<?xml version="1.0" encoding="' . PageEncode . '"?>';*/
	}

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<title>' . get_text('SearchResults','Tournament') . '</title>';
	print '<head_code>' . get_text('Code','Tournament') . '</head_code>';
	print '<head_archer>' . get_text('Archer') . '</head_archer>';
	print '<head_country>' . get_text('Country') . '</head_country>';
	print '<head_div>' . get_text('Div') . '</head_div>';
	print '<head_agecl>' . get_text('AgeCl') . '</head_agecl>';
	print '<head_subcl>' . get_text('SubCl','Tournament') . '</head_subcl>';
	print $xml;
	print '</response>';
?>