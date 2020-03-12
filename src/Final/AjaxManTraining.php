<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
checkACL(AclCompetition, AclReadWrite, false);

$Value=array('error' => 1);

if(!empty($_GET['Targets']) and is_array($_GET['Targets'])) {
	foreach($_GET['Targets'] as $Day => $Times) {
		if(is_array($Times)) {
			foreach($Times as $Time => $Teams) {
				if(is_array($Teams)) {
					foreach($Teams as $Team => $Events) {
						if(is_array($Events)) {
							foreach($Events as $Event => $Val) {
								safe_w_SQL("update FinWarmup set
									FwTargets='$Val'
									where FwTournament={$_SESSION['TourId']}
										AND FwEvent='$Event'
										AND FwTeamEvent='$Team'
										AND FwDay='$Day'
										AND FwTime='$Time'
										");
								$Value['error']=0;
								$Value['val']=$Val;
							}
						}
					}
				}
			}
		}
	}
}

header('Content-Type: text/xml');

echo '<response>';
foreach($Value as $fld => $data) {
	echo "<$fld><![CDATA[$data]]></$fld>";
}
echo '</response>';
