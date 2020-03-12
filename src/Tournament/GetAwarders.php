<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACl(AclCompetition,AclReadWrite, false);

$xmlDoc=new DOMDocument('1.0','UTF-8');
$xmlRoot=$xmlDoc->createElement('response');
$xmlDoc->appendChild($xmlRoot);

$xmlRoot->setAttribute('error', '1');

// get the awarder for this award ceremony
$Awarders=array();
list($Event, $FinEvent, $Team)=explode('|', $_REQUEST['id']);
$q=safe_r_sql("select AwAwarderGrouping from Awards
		where AwTournament={$_SESSION['TourId']} and AwEvent=".StrSafe_DB($Event)." and AwFinEvent=".intval($FinEvent)." and AwTeam=".intval($Team));

if($r=safe_fetch($q)) {
	$Awarders=unserialize($r->AwAwarderGrouping);
}

// get all the awards
$Prizes='<option value="">---</option>';
$n=1;
$def='aaa';

while(($tmp=getModuleParameter('Awards', 'Aw-Award-1-'.$n, $def))!=$def) {
	$Prizes.='<option value="'.$n.'">'.$tmp.'</option>';
	$n++;
}

if($tmp=getModuleParameter('Awards', 'Aw-Special-1', '')) {
	$Prizes.='<option value="special">'.$tmp.'</option>';
}

// get all the awards
$Persons='<option value="">---</option>';
$n=1;
$def='aaa';
while(($tmp=getModuleParameter('Awards', 'Aw-Awarder-1-'.$n, $def))!=$def) {
	$Persons.='<option value="'.$n.'">'.$tmp.'</option>';
	$n++;
}

$ret='<form><input type="hidden" name="id" value="'.htmlspecialchars($_REQUEST['id']).'">';
IF(is_array($Awarders)) {
	foreach($Awarders as $k => $v) {
		// $k is the number of the award, $v is the number of the awarder!
		$ret.='<div>';
		// creates a select with all the awards
		$ret.='<select id="Prize[]" name="Prize[]">' . str_replace('value="'.$k.'"', 'value="'.$k.'" selected="selected"', $Prizes) . '</select>';
		$ret.='&nbsp;&nbsp;';
		// creates a select with all the people
		$ret.='<select id="Person[]" name="Person[]">' . str_replace('value="'.$v.'"', 'value="'.$v.'" selected="selected"', $Persons) . '</select>';
		$ret.='</div>';
	}

}
// prepares an empty select
$ret.='<div>';
// creates a select with all the awards
$ret.='<select id="Prize[]" name="Prize[]">' . $Prizes . '</select>';
$ret.='&nbsp;&nbsp;';
// creates a select with all the people
$ret.='<select id="Person[]" name="Person[]">' . $Persons . '</select>';
$ret.='</div>';
$ret.='<input type="submit"></form>';

$xmlRoot->setAttribute('error', '0');

$Data=$xmlDoc->createCDATASection($ret);
$html=$xmlDoc->createElement('html');
$html->appendChild($Data);

$xmlRoot->appendChild($html);

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

print $xmlDoc->saveXML();
