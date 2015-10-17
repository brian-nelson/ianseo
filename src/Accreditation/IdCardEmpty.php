<?php
function emptyIdCard($sets='') {
	$ret=new StdClass();
	$ret->Settings=array();
	$ret->Background = '';
	$ret->ImgSize = 0;
	if($sets) {
		$ret->Settings = unserialize($sets->IcSettings);
		$ret->Background = $sets->IcBackground;
		if(!empty($sets->ImgSize)) $ret->ImgSize = $sets->ImgSize;
	}

	if(!isset($ret->Settings["Height"])) $ret->Settings["Height"] = $_SESSION['ToPaper'] ? 139 : 148;
	if(!isset($ret->Settings["OffsetY"])) $ret->Settings["OffsetY"] = '0;'.$ret->Settings["Height"];
	if(!isset($ret->Settings["PaperHeight"])) $ret->Settings["PaperHeight"] = 297;
	if(!isset($ret->Settings["Width"])) $ret->Settings["Width"] = $_SESSION['ToPaper'] ? 108 : 105;
	if(!isset($ret->Settings["OffsetX"])) $ret->Settings["OffsetX"] = '0;'.$ret->Settings["Width"];
	if(!isset($ret->Settings["PaperWidth"])) $ret->Settings["PaperWidth"] = 210;


	if(!isset($ret->Settings["IdBgX"])) $ret->Settings["IdBgX"] = 0;
	if(!isset($ret->Settings["IdBgY"])) $ret->Settings["IdBgY"] = 0;
	if(!isset($ret->Settings["IdBgH"])) $ret->Settings["IdBgH"] = 0;
	if(!isset($ret->Settings["IdBgW"])) $ret->Settings["IdBgW"] = 0;

	return $ret;
}
