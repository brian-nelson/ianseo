<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Various.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once dirname(dirname(__FILE__)) . '/Qualification/Fun_Qualification.local.inc.php';
	require_once('Fun_Partecipants.local.inc.php');

	if (!CheckTourSession()) printCrackerror('popup');

	$Code	= (!empty($_REQUEST['findCode']) ? $_REQUEST['findCode'] : '');
	$Ath	= (!empty($_REQUEST['findAth']) && strlen($_REQUEST['findAth'])>3 ? $_REQUEST['findAth'] : '');
	$Country= (!empty($_REQUEST['findCountry']) ? $_REQUEST['findCountry'] : '');
	$Div	= (empty($_REQUEST['findDiv']) ? '' : $_REQUEST['findDiv']);
	$Cl		= (empty($_REQUEST['findCl']) ? '' : $_REQUEST['findCl']);
	$SubCl	= (empty($_REQUEST['findSubCl']) ? '' : $_REQUEST['findSubCl']);
	$IocCode= (empty($_REQUEST['findIocCode']) ? '' : $_REQUEST['findIocCode']);

	$Filter = '';

	if ($IocCode)   $Filter.= " AND LueIocCode = " . StrSafe_DB($IocCode) . " ";
	if ($Code)		$Filter.= " AND LueCode = " . StrSafe_DB($Code) . " ";
	if ($Ath)		$Filter.= " AND CONCAT(LueFamilyName,' ',LueName) LIKE " . StrSafe_DB("%".stripslashes($Ath) . "%") . " ";
	if ($Country)	$Filter.= " AND (LueCountry = " . StrSafe_DB(stripslashes($Country)) . " OR LueCoShort LIKE '" . stripslashes($Country) ."%') ";
	if ($Code || $Ath || $Country) {
		if ($Div)	$Filter.= " AND LueDivision = " . StrSafe_DB($Div) . " ";
		if ($Cl)	$Filter.= " AND LueClass = " . StrSafe_DB($Cl) . " ";
		if ($SubCl)	$Filter.= " AND LueSubClass = " . StrSafe_DB($SubCl) . " ";
	}

	$Select
		= "SELECT * "
		. "FROM LookUpEntries "
		. "WHERE LueDefault='1' " . $Filter . " "
		. "ORDER BY LueFamilyName,LueName ";
	$Rs=safe_r_sql($Select);

	$html='';
	if (safe_num_rows($Rs)>0)
	{
		$html='<table class="Tabella">';

			while ($row=safe_fetch($Rs))
			{
				$row->LueFamilyName=stripslashes($row->LueFamilyName);
				$row->LueName=stripslashes($row->LueName);
				$row->LueCoShort=stripslashes($row->LueCoShort);
				$html.="
					<tr>
						<td style=\"width:10%;\"><a class=\"Link btn\" href=\"#\" id=\"{$row->LueCode}\" name=\"{$row->LueIocCode}\">{$row->LueCode}&nbsp;({$row->LueIocCode})</a></td>
						<td style=\"width:25%;\">{$row->LueFamilyName} {$row->LueName}</td>
						<td style=\"width:25%;\">{$row->LueCountry} - {$row->LueCoShort}</td>
						<td style=\"width:10%;\">{$row->LueDivision}</td>
						<td style=\"width:10%;\">{$row->LueClass}</td>
						<td style=\"width:10%;\">{$row->LueSubClass}</td>
						<td style=\"width:10%;\">
							&nbsp;
							<input type=\"hidden\" id=\"fdiv_{$row->LueCode}_{$row->LueIocCode}\" value=\"{$row->LueDivision}\"/>
							<input type=\"hidden\" id=\"fcl_{$row->LueCode}_{$row->LueIocCode}\" value=\"{$row->LueClass}\"/>
						</td>
					</tr>
				";
			}

		$html.='</table>';
	}

	print $html;


