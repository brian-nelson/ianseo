<?php
/**
 * PdfChunkLoader
 * Serve a gestire il caricamento dei chunk pdf.
 *
 *
 */
function PdfChunkLoader($file, $localRule='', $Type='', $SubRule='') {
	if(!$localRule) {
		$q="SELECT ToLocRule, ToType, ToTypeSubRule FROM Tournament WHERE ToId={$_SESSION['TourId']}";
		$r=safe_r_sql($q);
		if ($row=safe_fetch($r)) {
			$localRule=$row->ToLocRule;
			$Type=$row->ToType;
			$SubRule=$row->ToTypeSubRule;
		}
	}

	// paths have to be relative to THIS file
	$Path=dirname(dirname(dirname(__FILE__))).'/';

	if($localRule) {
		// check if the localised file exists
		if(file_exists($f=$Path.'Modules/Sets/'.$localRule.'/pdf/chunks/'.str_replace('.inc.php', '_'.$Type.'_'.$SubRule.'.inc.php', $file))) {
			return $f;
		}
		if(file_exists($f=$Path.'Modules/Sets/'.$localRule.'/pdf/chunks/'.str_replace('.inc.php', '_'.$Type.'.inc.php', $file))) {
			return $f;
		}
		if(file_exists($f=$Path.'Modules/Sets/'.$localRule.'/pdf/chunks/'.$file)) {
			return $f;
		}
	}

	// check if the localised file exists
	if(file_exists($f=$Path.'Common/pdf/chunks/'.str_replace('.inc.php', '_'.$Type.'.inc.php', $file))) {
		return $f;
	}

	// checks if the standard file exists
	if(file_exists($f=$Path.'Common/pdf/chunks/'.$file)) {
		return $f;
	}

	return false;
}