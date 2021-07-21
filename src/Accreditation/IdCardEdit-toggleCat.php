<?php

$JSON=array('error' => 1);

// ACL and other checks are made in the config
require_once('./IdCardEdit-config.php');

$JSON['error']=0;

if(!empty($_REQUEST['match'])) {
	setModuleParameter('Accreditation', 'Matches-'.$CardType.'-'.$CardNumber, implode(',', $_REQUEST['match']));
} else {
	delModuleParameter('Accreditation', 'Matches-'.$CardType.'-'.$CardNumber);
}

JsonOut($JSON);


