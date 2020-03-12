<?php

function emptyBackNumber($Id=0) {
	global $CFG;
	if(!$Id) $Id=$_SESSION['TourId'];
	// defaults values for a BackNumber
	// codes are as follow
	//  1: printed

	//  0: Arial
	//  2: Times
	//  4: Courier

	//  8: Bold
	// 16: Italic

	// 32: right
	// 64: left
	// 96: center

	// so default for everything is 1+0+8+96=105 (printed, Arial, Bold, Centered)

	$RowBn=new StdClass();
	$RowBn->BnHeight=297;
	$RowBn->BnWidth=210;
	$RowBn->BnOffsetX=0;
	$RowBn->BnOffsetY=148;
	$RowBn->BnBgX = 5;
	$RowBn->BnBgY = 5;

	$RowBn->BnTargetNo = 105;
	$RowBn->BnTnoColor = '707070';
	$RowBn->BnTnoSize = 150;

	$RowBn->BnAthlete = 105;
	$RowBn->BnAthColor = 'D00000';
	$RowBn->BnAthSize = 70;

	$RowBn->BnCountry = 105;
	$RowBn->BnCoColor = '336633';
	$RowBn->BnCoSize = 40;

	$RowBn->ImgSize = 0;
	$RowBn->Customized = 1;

	$q=safe_r_sql("select ToPrintPaper from Tournament where ToId={$Id}");
	$r=safe_fetch($q);
	if($r->ToPrintPaper=='1') {
		// Letter Paper
		$RowBn->BnHeight=279;
		$RowBn->BnWidth=216;
		$RowBn->BnOffsetX=0;
		$RowBn->BnOffsetY=139;
	}

	// width and height of printable area
	$RowBn->BnBgW = $RowBn->BnWidth - 2*$RowBn->BnBgX;
	$RowBn->BnBgH = $RowBn->BnOffsetY - 2*$RowBn->BnBgY;

	// Target position
	$RowBn->BnTnoW = 125;
	$RowBn->BnTnoH = 65;
	$RowBn->BnTnoX = 80;
	$RowBn->BnTnoY = 5;

	// Athlete position
	$RowBn->BnAthW = 200;
	$RowBn->BnAthH = 30;
	$RowBn->BnAthX = 5;
	$RowBn->BnAthY = 75;

	// Country Position
	$RowBn->BnCoW = 64;
	$RowBn->BnCoH = 34;
	$RowBn->BnCoX = 140;
	$RowBn->BnCoY = 109;

	// other
	$RowBn->BnIncludeSession=0;
	$RowBn->BnCapitalFirstName=0;
	$RowBn->BnGivenNameInitial=0;
	$RowBn->BnCountryCodeOnly=4;

	return $RowBn;
}
?>