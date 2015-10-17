<?php

/**
* MakeTeams per la generazione delle squadre nelle Regole UK - Campionati Junior
*/

$divisions = array('R');
if(!is_null($Category))
{
	$Category = substr($Category,0,1);
	switch($Category)
	{
		case 'B':
			$Category= 'R';
			break;
		default:
			$divisions = array();
	}
}

$startLevel = 2;
$endLevel = 2;

include("Common_MakeTeams.php");

?>