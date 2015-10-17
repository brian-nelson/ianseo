<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/ARF/ARFInput.class.php');

	$streamError=0;
	
	$out='<response>';
	
	if (!isset($_REQUEST['xml']))
	{
		$streamError=1;
	}
	else
	{
		$xml=stripslashes($_REQUEST['xml']);
		//print $xml;exit;
		$arf=new ARFInput($xml);

		list($tourCode,$importedEntries,$badEntries,$postError)=$arf->import();

		$out
			.='<imported_tour>' . $tourCode . '</imported_tour>' . "\n"
			. '<imported_entries>' . $importedEntries . '</imported_entries>' . "\n"
			. '<bad_entries>' . join(',',$badEntries) . '</bad_entries>' . "\n"
			. '<post_proc_error>' . ($postError ? 1 : 0) . '</post_proc_error>' . "\n";
		
	}
	
	$out.='<stream_error>' . $streamError . '</stream_error>' . "\n";
	$out.='</response>';
	
	header('Content-Type: text/xml');
	print $out;
?>