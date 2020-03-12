<?php

/**
* MakeTeams per la generazione delle squadre nelle Regole UK - Campionati Junior
*/


$divisions = array('R','C');
if(!is_null($Category))
{
	$Category = substr($Category,0,1);
	switch($Category)
	{
		case 'B':
		case 'R':
			$divisions = array('R');
			$Category= 'R';
			break;
		case 'C':
			$divisions = array('C');
			break;
		default:
			$divisions = array();
	}
}


$startLevel = 0;
$endLevel = 2;

include("Common_MakeTeams.php");

?>