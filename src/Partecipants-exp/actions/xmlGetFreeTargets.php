<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

/****** Controller ******/
	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$error = 0;

	$tourId=StrSafe_DB($_SESSION['TourId']);

	$x=(isset($_REQUEST['x']) ? $_REQUEST['x'] : null);
	$y=(isset($_REQUEST['y']) ? $_REQUEST['y'] : null);
	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : null);
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : null);
	$session=(isset($_REQUEST['session']) ? $_REQUEST['session'] : null);

	if (is_null($x) || is_null($y) || is_null($session) || is_null($row) || is_null($col))
	{
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	$query
		= "SELECT "
			. "SUBSTRING(AtTargetNo,2) AS Target "
		. "FROM "
			. "AvailableTarget "
		. "WHERE "
			. "AtTournament=" . $tourId . " "
			. "AND AtTargetNo LIKE '" . $session . "%' AND AtTargetNo NOT IN ("
				. "SELECT "
					. "QuTargetNo "
				. "FROM "
					. "Entries "
					. "INNER JOIN "
						. "Qualifications "
					. "ON EnId=QuId AND EnTournament=" . $tourId . " AND QuSession=" .  StrSafe_DB($session) . " AND QuTargetNo<>'' "
			. ") "
		. "ORDER BY "
			. "SUBSTRING(AtTargetNo,2) ASC ";
		//print $query;exit;
	$rs=safe_r_sql($query);
	$targets=array();
	if ($rs)
	{
		while ($myRow=safe_fetch($rs))
		{
			$targets[]=$myRow->Target;
		}
	}
	else
		$error=1;
/****** End Controller ******/

/****** Output ******/
	$xmlDoc=new DOMDocument('1.0',PageEncode);
		$xmlRoot=$xmlDoc->createElement('response');
		$xmlDoc->appendChild($xmlRoot);

	// Header
		$xmlHeader=$xmlDoc->createElement('header');
		$xmlRoot->appendChild($xmlHeader);

			$node=$xmlDoc->createElement('error',$error);
			$xmlHeader->appendChild($node);

			$node=$xmlDoc->createElement('x',$x);
			$xmlHeader->appendChild($node);

			$node=$xmlDoc->createElement('y',$y);
			$xmlHeader->appendChild($node);

			$node=$xmlDoc->createElement('row',$row);
			$xmlHeader->appendChild($node);
			
			$node=$xmlDoc->createElement('col',$col);
			$xmlHeader->appendChild($node);

		if (count($targets)>0)
		{
			foreach ($targets as $target)
			{
				$xmlTarget=$xmlDoc->createElement('target',$target);
				$xmlRoot->appendChild($xmlTarget);

			}
		}

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();
/****** end Output ******/
?>