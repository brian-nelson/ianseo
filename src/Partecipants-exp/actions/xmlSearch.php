<?php

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

/****** Controller ******/
	$code=(isset($_REQUEST['code']) ? $_REQUEST['code'] : null);
	$archer=(isset($_REQUEST['archer']) ? $_REQUEST['archer'] : null);
	$country=(isset($_REQUEST['country']) ? $_REQUEST['country'] : null);
	$division=(isset($_REQUEST['division']) ? $_REQUEST['division'] : null);
	$class=(isset($_REQUEST['class']) ? $_REQUEST['class'] : null);

	if (!CheckTourSession() || is_null($code) || is_null($archer) || is_null($country) || is_null($class) || is_null($division)) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	$error = 0;

	$tourId=StrSafe_DB($_SESSION['TourId']);

	$filter="";

	$go=false;

	if ($code!='')
	{
		$filter.=" AND LueCode=" . StrSafe_DB($code) . " ";
		$go=true;
	}

	if (strlen($archer)>=3)
	{
		$filter.=" AND CONCAT(LueFamilyName,' ',LueName) LIKE " . StrSafe_DB("%".stripslashes($archer) . '%') . " ";
		$go=true;
	}

	if ($country!='')
	{
		$filter.= " AND (LueCountry = " . StrSafe_DB(stripslashes($country)) . " || LueCoShort LIKE " . StrSafe_DB("%" . stripslashes($country) . "%") .  ") ";
		$go=true;
	}

	if ($division!='--')
		$filter.= " AND LueDivision = " . StrSafe_DB($division) . " ";

	if ($class!='--')
		$filter.= " AND LueClass = " . StrSafe_DB($class) . " ";

	$query
		= "SELECT * "
		. "FROM LookUpEntries "
		. "WHERE " . ($filter=='' ? "1=0 " : "1=1 ") . $filter . " AND LueDefault='1' "
		. "ORDER BY LueFamilyName,LueName ";

	$athletes=array();

	if ($go)
	{
		//print $query;
		$rs=safe_r_sql($query);


		if ($rs)
		{
			if (safe_num_rows($rs)>0)
			{
				while ($myRow=safe_fetch($rs))
				{
					$athletes[]=array
					(
						'code'=> trim($myRow->LueCode),
						'status'=> ($_SESSION['TourRealWhenFrom']>$myRow->LueStatusValidUntil && $myRow->LueStatusValidUntil!='0000-00-00' ? 5 : $myRow->LueStatus),
						'archer'=> stripslashes(trim($myRow->LueFamilyName . ' ' . $myRow->LueName)),
						'country'=>stripslashes(trim($myRow->LueCountry . ' - ' . $myRow->LueCoShort)),
						'division'=>trim($myRow->LueDivision),
						'class'=>trim($myRow->LueClass)
					);
				}
			}
		}
		else
		{
			$error=1;
		}
	}

/****** End Controller ******/

/****** Output ******/
	$xmlDoc=new DOMDocument('1.0','UTF-8');
		$xmlRoot=$xmlDoc->createElement('response');
		$xmlDoc->appendChild($xmlRoot);

	// Sezione header
			$xmlHeader=$xmlDoc->createElement('header');
			$xmlRoot->appendChild($xmlHeader);
				$node=$xmlDoc->createElement('error',$error);
				$xmlHeader->appendChild($node);

		// dati
			$xmlAthletes=$xmlDoc->createElement('athletes');
			$xmlRoot->appendChild($xmlAthletes);
				foreach ($athletes as $ath)
				{
					$xmlAth=$xmlDoc->createElement('ath');
					$xmlAthletes->appendChild($xmlAth);

						foreach ($ath as $k=>$v)
						{
							$node=$xmlDoc->createElement($k);
							$xmlAth->appendChild($node);
								$cdata=$xmlDoc->createCDATASection($v);
								$node->appendChild($cdata);
						}
				}

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();
/****** End OUtput ******/
?>