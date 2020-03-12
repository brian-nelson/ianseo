<?php
$version='2013-03-24 14:13:00';

if($on) {
    if($acl[AclQualification] == AclReadWrite) {
        $ret['QUAL']['SCOR'][] = MENU_DIVIDER;
        $ret['QUAL']['SCOR'][] = get_text('MenuLM_GetScoreBarcode') . '|' . GetWebDirectory(__FILE__) . '/GetScoreBarCode.php';
        $ret['QUAL']['SCOR'][] = get_text('MenuLM_GetScoreBarcodeReport') . '|' . GetWebDirectory(__FILE__) . '/GetScoreBarCodeReport.php|||_blank';
    }

	if(!empty($ret['ELIM']) AND $acl[AclEliminations] == AclReadWrite) {
		$ret['ELIM']['SCOR'][] = MENU_DIVIDER;
		$ret['ELIM']['SCOR'][] = get_text('MenuLM_Input Score') .'|'.GetWebDirectory(__FILE__).'/GetElimScoreBarCode.php';
		$ret['ELIM']['SCOR'][] = get_text('MenuLM_GetScoreBarcode') .'|'.GetWebDirectory(__FILE__).'/GetElimScoreBarCode.php';
		$ret['ELIM']['SCOR'][] = get_text('MenuLM_GetScoreBarcodeReport') .'|'.GetWebDirectory(__FILE__).'/GetScoreBarCodeReport.php|||_blank';
	}

	if(!empty($ret['ELIMP']) AND $acl[AclEliminations] == AclReadWrite) {
		$ret['ELIMP']['SCOR'][] = get_text('MenuLM_GetScoreBarcode') .'|'.GetWebDirectory(__FILE__).'/GetFinScoreBarCode.php';
		$ret['ELIMP']['SCOR'][] = get_text('MenuLM_GetScoreBarcodeReport') .'|'.GetWebDirectory(__FILE__).'/GetScoreBarCodeReport.php|||_blank';
	}

	if(!empty($ret['FINI']) AND $acl[AclIndividuals] == AclReadWrite) {
		$ret['FINI'][] = MENU_DIVIDER;
		$ret['FINI']['SCOR'][] = get_text('MenuLM_Input Score') .'|'.GetWebDirectory(__FILE__).'/GetFinScoreBarCode.php';
		$ret['FINI']['SCOR'][] = get_text('MenuLM_GetScoreBarcode') .'|'.GetWebDirectory(__FILE__).'/GetFinScoreBarCode.php';
		$ret['FINI']['SCOR'][] = get_text('MenuLM_GetScoreBarcodeReport') .'|'.GetWebDirectory(__FILE__).'/GetScoreBarCodeReport.php|||_blank';
	}

	if(!empty($ret['FINT'])  AND $acl[AclTeams] == AclReadWrite) {
		$ret['FINT'][] = MENU_DIVIDER;
		$ret['FINT']['SCOR'][] = get_text('MenuLM_Input Score') .'|'.GetWebDirectory(__FILE__).'/GetFinScoreBarCode.php';
		$ret['FINT']['SCOR'][] = get_text('MenuLM_GetScoreBarcode') .'|'.GetWebDirectory(__FILE__).'/GetFinScoreBarCode.php';
		$ret['FINT']['SCOR'][] = get_text('MenuLM_GetScoreBarcodeReport') .'|'.GetWebDirectory(__FILE__).'/GetScoreBarCodeReport.php|||_blank';
	}

	if(!empty($ret['FINT'])  AND $acl[AclTeams] == AclReadWrite and !empty($_SESSION['TourLocSubRule']) and $_SESSION['TourLocSubRule']=='SetFRChampsD1DNAP') {
		$ret['SetFRChampsD1DNAP'][] = MENU_DIVIDER;
		$ret['SetFRChampsD1DNAP']['SCOR'][] = get_text('MenuLM_Input Score') .'|'.GetWebDirectory(__FILE__).'/GetFinScoreBarCode.php';
		$ret['SetFRChampsD1DNAP']['SCOR'][] = get_text('MenuLM_GetScoreBarcode') .'|'.GetWebDirectory(__FILE__).'/GetFinScoreBarCode.php';
		$ret['SetFRChampsD1DNAP']['SCOR'][] = get_text('MenuLM_GetScoreBarcodeReport') .'|'.GetWebDirectory(__FILE__).'/GetScoreBarCodeReport.php|||_blank';
	}
}
