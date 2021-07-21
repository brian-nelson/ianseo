<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');

// check if it is a pro
define('ISK_PRO', getModuleParameter('ISK', 'Mode')=='pro');
