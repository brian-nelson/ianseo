<?php

function XML_Ranking_Header($XmlDoc, &$ListHeader, $TitElems) {

	foreach( $TitElems as $key=>$val) {
		$TmpNode = $XmlDoc->createElement('Caption', $val);
		$TmpNode->setAttribute('Name',$key);
		$ListHeader->appendChild($TmpNode);
	}
}

function XML_Ranking_Row($XmlDoc, &$ListHeader, $Elements) {
	$Athlete = $XmlDoc->createElement('Athlete');
	$ListHeader->appendChild($Athlete);

	foreach($Elements as $key => $val) {
		$Element = $XmlDoc->createElement('Item', $val);
		$Element->setAttribute('Name', $key);
		$Athlete->appendChild($Element);
	}
}

?>