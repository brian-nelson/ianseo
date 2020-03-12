<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once 'Partecipants-exp/functions/rows.inc.php';

/****** Controller ******/
	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	$error=0;

	$divisions=getDivisions();
	$classes=getClasses();

	if ($divisions===false || $classes===false)
	{
		$error=1;
		$divisions=array(''=>'');
		$classes=array(''=>array('val'=>'','valid'=>''));
	}
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

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();
/****** End Output ******/
?>