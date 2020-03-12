<?php
/**
 * Created by PhpStorm.
 * User: deligant
 * Date: 09/05/17
 * Time: 18.51
 */

require_once(dirname(dirname(__FILE__)).'/config.php');
checkACL(AclRoot, AclReadWrite);

require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/CommonLib.php');

$JSON=array(
	'error' => 1,
	'html' => '',
	);

if(empty($_REQUEST['toid']) or !($ToId=intval($_REQUEST['toid']))) {
	JsonOut($JSON);
}

$Options=GetParameter('AccessApp', '', array(), true);
$Sessions=$Options[$ToId];

if($tmp=getScheduledSessions('API', $ToId, !empty($_REQUEST['today']))) {
	$JSON['html'].= '<table><tr><th>Event</th><th>Byes In</th><th>Byes Out</th></tr>';
	foreach($tmp as $item) {
		$JSON['html'].= '<tr class="rowHover"><td>'.$item->Description.'</td>';
		$JSON['html'].= '<td align="center"><input type="checkbox" name="'.$item->keyValue.'-'.$ToId.'" value="'.$item->keyValue.'" tour="'.$ToId.'" onclick="setSession(this)"'.(in_array($item->keyValue, $Sessions) ? ' checked="checked"' : '').' ></td>';
		$JSON['html'].= '<td align="center"><input type="checkbox" name="'.$item->keyValue.'-'.$ToId.'" value="'.strtolower($item->keyValue).'" tour="'.$ToId.'" onclick="setSession(this)"'.(in_array(strtolower($item->keyValue), $Sessions) ? ' checked="checked"' : '').' ></td>';
		$JSON['html'].= '</tr>';
	}
	$JSON['html'].= '</table>';
}
//$tmp=getScheduledSessions('API', $ToId, !empty($_REQUEST['today']));
//foreach($tmp as $item) {
//	$JSON['html'].= '<div><input type="checkbox" value="'.$item->keyValue.'" tour="'.$ToId.'" onclick="setSession(this)"'.(in_array($item->keyValue, $Sessions) ? ' checked="checked"' : '').' >'.$item->Description.'</div>';
//}

$JSON['error']=0;

JsonOut($JSON);