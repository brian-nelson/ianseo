<?php
/*****************
 *
 * Centralina METEO
 *
<meteo>
    <w_speed>26.56</w_speed>
    <w_direction>90</w_direction>
    <temperatura>24.00</temperatura>
    <umidity>30.3</umidity>
</meteo>


1 m/s = 1.94 nodi

Secondo me la formula calcola i nodi e non i m/s

//  float Speed = (float)(((((end_count - start_count)*1000.0)/(end_time - start_time)) /2.0)*2.453)*1862/3600;

 *
 * */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

$SQL="SELECT "
	. " * "
	. "FROM w1retap.readings "
	. "ORDER BY `date` desc "
	. "LIMIT 10 ";

$r1=new StdClass();
$r2=new StdClass();

$q=safe_r_sql($SQL);
while($r=safe_fetch($q)) {
	if(isset($r1->{$r->name})) {
		$r2->date=$r->date;
		$r2->{$r->name} = $r->value;
	} else {
		$r1->date=$r->date;
		$r1->{$r->name} = $r->value;
	}
}

$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRoot = $XmlDoc->createElement('meteo');
$XmlDoc->appendChild($XmlRoot);

$Header = $XmlDoc->createElement('umidity', $r1 ? round($r1->HUM, 1) : '-');
$XmlRoot->appendChild($Header);

$Games = $XmlDoc->createElement('temperatura', $r1 ? round($r1->TMP, 1) : '-');
$XmlRoot->appendChild($Games);

// calculate wind speed
$speed='-';
if($r2 and $r1) {
	$speed=number_format(0.634373 * ($r1->WSPD - $r2->WSPD) / ($r1->date - $r2->date), 1);
}
$Opp1 = $XmlDoc->createElement('w_speed', $r1 ? $speed : '-');
$XmlRoot->appendChild($Opp1);

// calculate wind direction
$Opp2 = $XmlDoc->createElement('w_direction', $r1 ? 22.5*$r1->WDIR : '-');
$XmlRoot->appendChild($Opp2);

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);
echo $XmlDoc->SaveXML();

die();

?>