<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	$sex=isset($_REQUEST['sex']) ? intval($_REQUEST['sex']) : '-1';
	$div=isset($_REQUEST['div']) ? preg_replace('/[^0-9A-Z]/sim', '', $_REQUEST['div']) : '';
	$age=isset($_REQUEST['age']) ? $_REQUEST['age'] : '';

	if (!CheckTourSession() || is_null($sex) ) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	if(!empty($age)) {
		preg_match('/(?P<age>[0-9]{4})/',$age,$tmp);
		if(!empty($tmp['age'])) {
			$age=$tmp['age'];
			$age=substr($_SESSION['TourRealWhenTo'],0,4)-$age;
		}
	}

	$error=0;
	$isAthlete=0;

	$query = "SELECT distinct ClId, DivAthlete FROM Divisions"
		. " inner join Classes on DivTournament=ClTournament and DivAthlete=ClAthlete"
		. " where DivTournament={$_SESSION['TourId']}"
		. " AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))"
		. " AND ClSex in (-1, $sex)"
		. " AND DivId='$div'"
		. ($age ? " AND (ClAthlete!='1' or (ClAgeFrom<=$age AND ClAgeTo>=$age ))" : '')
		. " ORDER BY ClViewOrder";
	$rs=safe_r_SQL($query);
	$classes=array();

	if (!$rs)
	{
		$error=1;
	}
	else
	{
		while ($myRow=safe_fetch($rs))
		{
			$classes[] = $myRow->ClId;
			$isAthlete = $myRow->DivAthlete;
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

			$xmlAthlete=$xmlDoc->createElement('athletes');
			$xmlRoot->appendChild($xmlAthlete);

				$node=$xmlDoc->createElement('athlete',($isAthlete ? '1' : '0') );
				$xmlAthlete->appendChild($node);


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