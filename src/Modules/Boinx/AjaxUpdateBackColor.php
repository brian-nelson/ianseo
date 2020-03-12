<?php
require_once('../../config.php');

checkACL(AclOutput,AclReadWrite, false);
Set_Tournament_Option('AwardBackColor', $_REQUEST['Page_BGColor']);

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

echo '<response><error>0</error></response>';
