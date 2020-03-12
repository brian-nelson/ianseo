<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');

CheckTourSession(true);

checkACL(AclCompetition, AclReadWrite);

define('Series', 5);
