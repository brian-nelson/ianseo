<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once 'Partecipants-exp/functions/rows.inc.php';

/****** Controller ******/
	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	$tourId=StrSafe_DB($_SESSION['TourId']);

	$error=0;

	$flags=array
	(
		array('','',''),
		array(0,'','x-icon-ok'),
		array(1,'','x-icon-canshoot'),
		array(5,'','x-icon-unknownshoot'),
		array(6,'','x-icon-gohome'),
		array(7,'','x-icon-notaccredited'),
		array(8,'','x-icon-couldshoot'),
		array(9,'','x-icon-noshoot')
	);


	$sessions=array('0'=>'--');
	$divisions=array(''=>'');
	$classes=array(''=>array('val'=>'','valid'=>''));
	$subClasses=array(''=>'');
	$archers=array(''=>'','0'=>get_text('No'),'1'=>get_text('Yes'));
	$genders=array();
	$athletes=array();


	$sessions=getSessions_();
	$divisions=getDivisions();
	$subClasses=getSubClasses();
	$classes=getClasses();
	$genders=getGenders();
	$athletes=getAthletes();


/****** End Controlloer ******/

/****** Output ******/
	$xmlDoc=new DOMDocument('1.0','UTF-8');
		$xmlRoot=$xmlDoc->createElement('response');
		$xmlDoc->appendChild($xmlRoot);

	// Sezione header
			$xmlHeader=$xmlDoc->createElement('header');
			$xmlRoot->appendChild($xmlHeader);
				$node=$xmlDoc->createElement('error',$error);
				$xmlHeader->appendChild($node);
	// Sezione combos
			$xmlCombos=$xmlDoc->createElement('combos');
			$xmlRoot->appendChild($xmlCombos);
			// flags
				$xmlFlags=$xmlDoc->createElement('flags');
				$xmlCombos->appendChild($xmlFlags);
					foreach ($flags as $k=>$v)
					{
						$xmlFlag=$xmlDoc->createElement('flag');
						$xmlFlags->appendChild($xmlFlag);

							$node=$xmlDoc->createElement('id',$v[0]);
							$xmlFlag->appendChild($node);

							$node=$xmlDoc->createElement('val',$v[1]);
							$xmlFlag->appendChild($node);

							$node=$xmlDoc->createElement('icon',$v[2]);
							$xmlFlag->appendChild($node);
					}

			// sessions
				$xmlSessions=$xmlDoc->createElement('sessions');
				$xmlCombos->appendChild($xmlSessions);
					foreach ($sessions as $k => $v)
					{
						$xmlSes=$xmlDoc->createElement('session');
						$xmlSessions->appendChild($xmlSes);

							$node=$xmlDoc->createElement('id',$k);
							$xmlSes->appendChild($node);

							$node=$xmlDoc->createElement('val',$v);
							$xmlSes->appendChild($node);
					}
			// divisions
				$xmlDivs=$xmlDoc->createElement('divisions');
				$xmlCombos->appendChild($xmlDivs);
					foreach ($divisions as $k => $v)
					{
						$xmlDiv=$xmlDoc->createElement('division');
						$xmlDivs->appendChild($xmlDiv);

							$node=$xmlDoc->createElement('id',$k);
							$xmlDiv->appendChild($node);

							$node=$xmlDoc->createElement('val',$v);
							$xmlDiv->appendChild($node);
					}
			// subclasses
				$xmlSubClasses=$xmlDoc->createElement('sub_classes');
				$xmlCombos->appendChild($xmlSubClasses);
					foreach ($subClasses as $k => $v)
					{
						$xmlSubCl=$xmlDoc->createElement('sub_class');
						$xmlSubClasses->appendChild($xmlSubCl);

							$node=$xmlDoc->createElement('id',$k);
							$xmlSubCl->appendChild($node);

							$node=$xmlDoc->createElement('val',$v);
							$xmlSubCl->appendChild($node);
					}
			// classes
				$xmlClasses=$xmlDoc->createElement('classes');
				$xmlCombos->appendChild($xmlClasses);
					foreach ($classes as $k => $cl)
					{
						$xmlCl=$xmlDoc->createElement('class');
						$xmlClasses->appendChild($xmlCl);

							$node=$xmlDoc->createElement('id',$k);
							$xmlCl->appendChild($node);

							$node=$xmlDoc->createElement('val',$cl['val']);
							$xmlCl->appendChild($node);

							$node=$xmlDoc->createElement('valid',$cl['valid']);
							$xmlCl->appendChild($node);
					}
			// arciere si/no
				$xmlArchers=$xmlDoc->createElement('archers');
				$xmlCombos->appendChild($xmlArchers);

					foreach ($archers as $k =>$v)
					{
						$xmlAr=$xmlDoc->createElement('archer');
						$xmlArchers->appendChild($xmlAr);

							$node=$xmlDoc->createElement('id',$k);
							$xmlAr->appendChild($node);

							$node=$xmlDoc->createElement('val',$v);
							$xmlAr->appendChild($node);
					}
			// sesso
				$xmlGenders=$xmlDoc->createElement('genders');
				$xmlCombos->appendChild($xmlGenders);
					foreach ($genders as $k =>$v)
					{
						$xmlAr=$xmlDoc->createElement('gender');
						$xmlGenders->appendChild($xmlAr);

							$node=$xmlDoc->createElement('id',$k);
							$xmlAr->appendChild($node);

							$node=$xmlDoc->createElement('val',$v);
							$xmlAr->appendChild($node);
					}

	// sezione athletes
		$xmlAthletes=$xmlDoc->createElement('athletes');
		$xmlRoot->appendChild($xmlAthletes);
			foreach ($athletes as $ath)
			{
				$xmlAth=$xmlDoc->createElement('ath');
				$xmlAthletes->appendChild($xmlAth);

				// campo errors fittizio
					$node=$xmlDoc->createElement('errors','');
					$xmlAth->appendChild($node);

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

/****** End Output ******/
?>