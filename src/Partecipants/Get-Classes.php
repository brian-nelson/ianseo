<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	$error=0;
    $subclasses=array();

	if(!empty($_REQUEST['enid'])) {
		$EnId=intval($_REQUEST['enid']);
        $query = "SELECT distinct ClValidClass
			FROM Entries
			inner join Tournament on ToId=EnTournament
			inner JOIN Divisions on DivId=EnDivision and DivTournament=EnTournament
			inner join Classes on ClTournament=EnTournament 
				and (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) 
				and (ClSex=-1 or ClSex=EnSex)
				and (EnDob=0 or year(ToWhenTo)-year(EnDob) between ClAgeFrom and ClAgeTo)
				and (EnDob=0 or find_in_set(EnAgeClass, ClValidClass))
			where EnId=$EnId";
        $rs=safe_r_SQL($query);
        $tmpClasses = array();
        while ($myRow=safe_fetch($rs)) {
            $tmpClasses += explode(",",$myRow->ClValidClass);
        }

		$query = "SELECT distinct ClId, ClDescription 
			FROM Classes 
			where ClTournament={$_SESSION['TourId']} and ClId IN ('".implode("','",$tmpClasses)."')
			ORDER BY ClViewOrder";
        $rs=safe_r_SQL($query);

        while ($myRow=safe_fetch($rs)) {
            $subclasses[] = $myRow->ClId . ':::' . $myRow->ClDescription;
        }
	} else {
		$query = "SELECT ClId, ClDescription FROM Classes
			where ClTournament={$_SESSION['TourId']}
			ORDER BY ClViewOrder";
        $rs=safe_r_SQL($query);

        while ($myRow=safe_fetch($rs)) {
            $subclasses[] = $myRow->ClId . ':::' . $myRow->ClDescription;
        }
	}



	$xmlDoc=new DOMDocument('1.0',PageEncode);

	$xmlDoc->appendChild($xmlRoot=$xmlDoc->createElement('response'));

	$xmlRoot->appendChild($xmlDoc->createElement('error',$error));
	$xmlRoot->appendChild($xmlItems=$xmlDoc->createElement('items'));

	$xmlItems->appendChild($xmlDoc->createCDATASection(implode('---', $subclasses)));

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();