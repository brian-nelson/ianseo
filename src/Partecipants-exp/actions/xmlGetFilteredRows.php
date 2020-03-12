<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	
/****** Controller ******/
	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);
	
	$tourId=StrSafe_DB($_SESSION['TourId']);

	$error=0;
	
/****** End Controlloer ******/
	
/****** Output ******/
/****** End Output ******/	
?>