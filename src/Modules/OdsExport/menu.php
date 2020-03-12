<?php
$version='2011-08-21 10:54:00';

if(!empty($on) AND isset($ret['COMP']['EXPT'])) {
	$ret['COMP']['EXPT'][] = get_text('MenuLM_OdsExport').'|'.$CFG->ROOT_DIR.'Modules/'.basename(dirname(__FILE__)).'/|||_blank';
}

?>