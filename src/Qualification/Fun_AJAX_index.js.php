<?php
// TODO: THIS FILE IS NOT REFERENCED DIRECTLY: CAN BE DELETED???

header('Content-Type: application/javascript');
/*
													- Fun_AJAX_index.js.php -
		Contiene le funzioni ajax che riguardano le pagine:
	 	index.php
	 	index_all.php
	 	WriteArrows.php
	 	PrintBackNo.php
	 	PrintScore.php
*/

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/ajax/ObjXMLHttpRequest.js');
	require_once('Qualification/Fun_AJAX_index.js');

/*
	Nota Bene:
	Anche se fa schifo avere globale questa stringa, per comodità l'ho definita così.
*/
	print 'var MsgAreYouSure = "' . get_text('MsgAreYouSure') . '";';
?>