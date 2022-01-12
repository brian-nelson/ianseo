<?php
$version='2021-02-16';

if(!empty($on) && $_SESSION['TourLocRule'] == 'SE') {
	$ret['COMP']['EXPT'][] = '* Exportera svenska resultat|'.$CFG->ROOT_DIR.'Modules/SweFunctions/';
}
if(!empty($on) && $_SESSION['TourLocRule'] == 'NO') {
	$ret['COMP']['EXPT'][] = '* Exportere norske resultat|'.$CFG->ROOT_DIR.'Modules/SweFunctions/';
}
if(!empty($on) && $_SESSION['TourLocRule'] == 'IS') {
	$ret['COMP']['EXPT'][] = '* Senda íslensk úrslit|'.$CFG->ROOT_DIR.'Modules/SweFunctions/';
}
?>