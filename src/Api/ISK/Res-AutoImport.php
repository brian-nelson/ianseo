<?php

require_once(dirname(dirname(__FILE__)).'/config.php');
$Error=1;

if(!CheckTourSession()) {
	header('Content-Type: text/xml');
	die('<response error="'.$Error.'"/>');
}
checkACL(AclISKServer, AclReadWrite,false);

require_once('Common/Lib/Fun_Modules.php');

if(empty($_REQUEST['stop'])) {
	delModuleParameter('ISK', 'StopAutoImport');
} else {
	setModuleParameter('ISK', 'StopAutoImport', 1);
}
$Error=0;

header('Content-Type: text/xml');
echo '<response error="'.$Error.'">';
echo '</response>';
