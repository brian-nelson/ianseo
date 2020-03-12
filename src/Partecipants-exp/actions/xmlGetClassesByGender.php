<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

	$sex=isset($_REQUEST['sex']) ? $_REQUEST['sex'] : null;
	$ath=isset($_REQUEST['ath']) ? $_REQUEST['ath'] : null;
	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : null);
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : null);
	$div=(isset($_REQUEST['div']) ? $_REQUEST['div'] : '');

	if (!CheckTourSession() || is_null($sex) || is_null($row) || is_null($col)) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);


	$error=0;

	$query
		= "SELECT ClId "
		. "FROM "
			. "Classes "
		. "WHERE "
			. "ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (ClSex=" . StrSafe_DB($sex) . " OR ClSex=-1) "
			. "AND ClAthlete=" . $ath . " "
		. "ORDER BY "
			. "ClId ASC,ClViewOrder ASC ";

	$query = "select ClId, ClValidClass, DivId from Classes"
		. " inner join Divisions on DivTournament=ClTournament and DivAthlete=ClAthlete"
		. ($div ? " AND DivId='$div'" : '')
		. " where ClTournament={$_SESSION['TourId']}"
		. " AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))"
		. " AND ClSex in (-1, {$sex})"
//		. ($Age ? " and (ClAthlete!='1' or (ClAgeFrom<=$Age and ClAgeTo>=$Age))" : '')
//		. ($Clas ? " AND ClId='$Clas'" : '')
		. " order by ClViewOrder, DivViewOrder ";

	$rs=safe_r_SQL($query);
//print $query;
	$classes=array();

	if (!$rs)
	{
		$error=1;
	}
	else
	{
		while ($myRow=safe_fetch($rs))
		{
			$classes[]=$myRow->ClId;
		}
	}

	$xmlDoc=new DOMDocument('1.0',PageEncode);
		$xmlRoot=$xmlDoc->createElement('response');
		$xmlDoc->appendChild($xmlRoot);

		// Header
			$xmlHeader=$xmlDoc->createElement('header');
			$xmlRoot->appendChild($xmlHeader);

				$node=$xmlDoc->createElement('error',$error);
				$xmlHeader->appendChild($node);

				$node=$xmlDoc->createElement('row',intval($row));
				$xmlHeader->appendChild($node);

				$node=$xmlDoc->createElement('col',intval($col));
				$xmlHeader->appendChild($node);

			$xmlClasses=$xmlDoc->createElement('classes');
			$xmlRoot->appendChild($xmlClasses);

				foreach ($classes as $c)
				{
					$xmlCl=$xmlDoc->createElement('class');
					$xmlClasses->appendChild($xmlCl);

						$cdata=$xmlDoc->createCDATASection($c);
						$xmlCl->appendChild($cdata);
				}

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();