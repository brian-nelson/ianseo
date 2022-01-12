<?php

if((isset($pdf->OrgEvent) and strlen($pdf->OrgEvent)==6) or  (isset($Events) and strlen($Events[0])==4)) {
	require_once('Common/pdf/chunks/BracketTeam.inc.php');
} else {
	$pdf->deletePage($pdf->getPage());
}
