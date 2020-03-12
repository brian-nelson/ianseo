<?php
/*
															- MakeSnapshot.php -
	Genera lo snapshot
*/

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
checkACL(AclQualification, AclReadWrite, false);

$Errore=1;
$xmlReturn='';
$JSON=array('Error' => 1, 'msg' => '');
if (!IsBlocked(BIT_BLOCK_QUAL)) {
	$Session = (isset($_REQUEST["Session"]) ? intval($_REQUEST["Session"]) : 0);
	$Distance = (isset($_REQUEST["Distance"]) ? intval($_REQUEST["Distance"]) : 0);
	$FromTarget = ($_REQUEST["fromTarget"] ? intval($_REQUEST["fromTarget"]) : 0);
	$ToTarget = ($_REQUEST["toTarget"] ? intval($_REQUEST["toTarget"]) : 0);

	if ($Session and $Distance and $FromTarget and $ToTarget) {
		$Errore=0;
		$JSON['Error']=0;
		$num=array();
		if(isset($_REQUEST["numArrows"]) && preg_match("/^[0-9]{1,2}$/",$_REQUEST["numArrows"])) {
			if($_REQUEST["numArrows"]==0) {
				// get num of ends for that session and distance
				$obj=getArrowEnds($Session, $Distance);
				for($i=$obj[$Distance]['arrows']; $i<=$obj[$Distance]['arrows']*$obj[$Distance]['ends']; $i+=$obj[$Distance]['arrows'] ) {
					$tmp=useArrowsSnapshot($Session, $Distance, $FromTarget, $ToTarget,$i);
					$xmlReturn .= '<numArrows>'  . $tmp . '</numArrows>' . "\n";
					$num[]=$tmp;
				}
			} else {
				$tmp=useArrowsSnapshot($Session, $Distance, $FromTarget, $ToTarget, $_REQUEST["numArrows"]);
				$xmlReturn .= '<numArrows>'  . $tmp . '</numArrows>' . "\n";
				$num[]=$tmp;
			}
		} else {
			$xmlReturn .= '<numArrows>'  . recalSnapshot($Session, $Distance, $FromTarget, $ToTarget) . '</numArrows>' . "\n";
			$num[]='current';
		}
		$JSON['msg']=get_text('SnapshotRecalculated', 'Tournament', implode(', ', $num));
	}
}

if(isset($_REQUEST['json'])) {
	JsonOut($JSON);
}

// produco l'xml di ritorno
header('Content-Type: text/xml');

print '<response>' . "\n";
print '<error>' . $Errore . '</error>' . "\n";
print '<msg><![CDATA[' . ($Errore==1 ? get_text('MakeSnapshotError','Tournament') : get_text('MakeSnapshotOk','Tournament')) . ']]></msg>';
print $xmlReturn;
print '</response>' . "\n";


