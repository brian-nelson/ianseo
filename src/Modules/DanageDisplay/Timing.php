<?php
define("NUL", chr(0));
define("STX", chr(2));
define("ENQ", chr(5));
define("ACK", chr(6));
define("EOT", chr(4));

$colArray = array("R"=>array(1.00,0.00,0.00), "Y"=> array(1.00,1.00,0.00), "G"=>array(0.50,1.00,0.00));

$parsed=false;
$fp = @fsockopen ('192.168.69.202', '9001', $errno, $errstr, 1);
if ($fp)
{
	stream_set_blocking($fp,0);
	$TimerTo=getmicrotime();
	$answer="";
	$startfound=false;
	while (!feof($fp))  {
		$char = fgetc($fp);
		if($char != null)
			$TimerTo=getmicrotime();
		if($char == STX)
			$startfound=true;
		
		if((getmicrotime()-$TimerTo) > 5)
			break;
		
		if($startfound)
			$answer .= $char;
		
		if($char == EOT && $startfound)
			break;
	}
	//echo OutText($answer);
	$parsed = parseTiming($answer);
}
//print_r($parsed);exit;
$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRoot = $XmlDoc->createElement('DanageTiming');
$XmlDoc->appendChild($XmlRoot);

if($parsed !== false) {
	$XmlRoot->appendChild($XmlDoc->createElement('validData', 1));

	$XmlRoot->appendChild($XmlDoc->createElement('lShooting', ($parsed["lLight"]!='R' ? 1:0)));
	$XmlRoot->appendChild($XmlDoc->createElement('lTime', $parsed["lTime"]));
	$XmlRoot->appendChild($XmlDoc->createElement('lLightR', $colArray[$parsed["lLight"]][0]));
	$XmlRoot->appendChild($XmlDoc->createElement('lLightG', $colArray[$parsed["lLight"]][1]));
	$XmlRoot->appendChild($XmlDoc->createElement('lLightB', $colArray[$parsed["lLight"]][2]));

	$XmlRoot->appendChild($XmlDoc->createElement('rShooting', ($parsed["rLight"]!='R' ? 1:0)));
	$XmlRoot->appendChild($XmlDoc->createElement('rTime', $parsed["rTime"]));
	$XmlRoot->appendChild($XmlDoc->createElement('rLightR', $colArray[$parsed["rLight"]][0]));
	$XmlRoot->appendChild($XmlDoc->createElement('rLightG', $colArray[$parsed["rLight"]][1]));
	$XmlRoot->appendChild($XmlDoc->createElement('rLightB', $colArray[$parsed["rLight"]][2]));
/*
	$XmlRoot->appendChild($XmlDoc->createElement('rShooting', ($parsed["lLight"]!='R' ? 1:0)));
	$XmlRoot->appendChild($XmlDoc->createElement('rTime', $parsed["lTime"]));
	$XmlRoot->appendChild($XmlDoc->createElement('rLightR', $colArray[$parsed["lLight"]][0]));
	$XmlRoot->appendChild($XmlDoc->createElement('rLightG', $colArray[$parsed["lLight"]][1]));
	$XmlRoot->appendChild($XmlDoc->createElement('rLightB', $colArray[$parsed["lLight"]][2]));
	
	$XmlRoot->appendChild($XmlDoc->createElement('lShooting', ($parsed["rLight"]!='R' ? 1:0)));
	$XmlRoot->appendChild($XmlDoc->createElement('lTime', $parsed["rTime"]));
	$XmlRoot->appendChild($XmlDoc->createElement('lLightR', $colArray[$parsed["rLight"]][0]));
	$XmlRoot->appendChild($XmlDoc->createElement('lLightG', $colArray[$parsed["rLight"]][1]));
	$XmlRoot->appendChild($XmlDoc->createElement('lLightB', $colArray[$parsed["rLight"]][2]));
*/
} else {
	$XmlRoot->appendChild($XmlDoc->createElement('validData', 0));
}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=UTF-8');
echo $XmlDoc->SaveXML();

function OutText($text)
{
	$return = "";
	$return .= "<pre>\n";
	$return .= "Text Length: " . strlen($text) . "\n";
	for($i=0; $i<strlen($text); $i++)
	$return .= "Char no. " . sprintf("%2s", $i) . " --- " . sprintf("%3s",ord(substr($text,$i,1))) . " (" . substr($text,$i,1) . ") . (0x" . base_convert(ord(substr($text,$i,1)), 10, 16) . ")\n";
	$return .= "</pre>\n";
	return $return;
}

function getmicrotime() {
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

function parseTiming($packet) {
	$value=array("lLight"=>"","rLight"=>"","lTime"=>"","rTime"=>"","Seq"=>"");
	if(strlen($packet)!=39 || $packet[0]!=STX || $packet[38]!=EOT)
		return false;
	$value["lLight"] = substr($packet,5,1);
	if($value["lLight"] == " ")
		$value["lLight"]  ="R";
	
	$value["lTime"] = intval("0" . trim(substr($packet,6,5)));
//	if(substr($packet,11,1)!=" ")
//		$value["lTime"] += (intval(ord(substr($packet,11,1)))-0xA0)/10;

	$value["rLight"] = substr($packet,13,1);
	if($value["rLight"] == " ")
		$value["rLight"]  ="R";
	$value["rTime"] = intval("0" . trim(substr($packet,14,5)));
//	if(substr($packet,19,1)!=" ")
//		$value["rTime"] += (intval(ord(substr($packet,19,1)))-0xA0)/10;
	
	$value["Seq"] = substr($packet,29,4);
	
	return $value;
}

?>