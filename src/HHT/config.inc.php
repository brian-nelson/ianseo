<?php
	define("NUL", chr(0)); 
	define("STX", chr(2));
	define("ENQ", chr(5));
	define("ACK", chr(6));
	define("EOT", chr(4));
	define("AllModule", 32);
	define("ModuleName", chr(65));
	define("Alpha",chr(64));
	

/* obsoleti */
/*
	define(DefaultHost,"192.168.0.4");
	//define(DefaultHost,"127.0.0.1");
	define(DefaultPort,"9001");
*/	

	function CalculateChecksum($text)
	{
		$total=0;
		for($i=0; $i<strlen($text); $i++)
			$total += ord(substr($text,$i,1));
		$total &= 127;
		$total += 32;
		return chr($total); 
	}
	
	function CalculateDisplayChecksum($text)
	{
		$total=0;
		for($i=0; $i<strlen($text); $i++)
			$total += ord(substr($text,$i,1));
		$total &= 255;
		return chr($total); 
	}
	
	function OutText($text)
	{
		$return = "";
		$return .= "<pre>\n";
		$return .= "Text Lenght: " . strlen($text) . "\n";
		for($i=0; $i<strlen($text); $i++)
			$return .= "Char no. " . sprintf("%2s", $i) . " --- " . sprintf("%3s",ord(substr($text,$i,1))) . " (" . substr($text,$i,1) . ") . (0x" . base_convert(ord(substr($text,$i,1)), 10, 16) . ")\n";
		$return .= "</pre>\n";
		return $return; 
	}

?>