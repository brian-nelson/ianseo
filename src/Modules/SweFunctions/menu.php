<?php
$version='2016-09-06';

if(!empty($on) && $_SESSION['TourLocRule'] == 'SE') {
	$ret['COMP']['EXPT'][] = '* Exportera svenska resultat|'.$CFG->ROOT_DIR.'Modules/SweFunctions/';
}
