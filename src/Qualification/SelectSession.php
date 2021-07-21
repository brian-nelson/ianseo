<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error'=>1, 'min'=>'', 'max'=>'','coalesce'=>false);
if (!CheckTourSession() or !hasACL(AclQualification, AclReadOnly)) {
	JsonOut($JSON);
}

if (isset($_REQUEST['Ses'])) {
	$Select = "SELECT MIN(AtTarget) AS Minimo, MAX(AtTarget) AS Massimo, SesAth4Target in (1,2) as Coalesce
		FROM AvailableTarget 
		inner join Session on SesTournament=AtTournament and SesOrder=AtSession and SesType='Q'
		WHERE AtTournament={$_SESSION['TourId']} AND AtSession = " . intval($_REQUEST['Ses']) . "
		group by AtTournament, AtSession";
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1) {
		$MyRow=safe_fetch($Rs);
		$JSON['min']=$MyRow->Minimo;
		$JSON['max']=$MyRow->Massimo;
		$JSON['error']=0;
		$JSON['coalesce']=($MyRow->Coalesce ? '<input id="x_Coalesce" name="x_Coalesce" type="checkbox" value="1">' . get_text('CoalesceScorecards', 'Tournament').'<div>' . get_text('CoalesceScorecardsTip', 'Tournament').'</div>' : '');
	}
}

JsonOut($JSON);
