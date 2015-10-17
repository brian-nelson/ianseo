<?php
require_once(dirname(dirname(__FILE__)).'/config.php');

CheckTourSession(true);

$Session='';
$SesType='Q';
$Phase=0;

$Group='';
foreach($_REQUEST['tgt'] as $SType => $Phases) {
	$SesType=$SType;
	foreach($Phases as $SPhase => $Targets) {
		$Phase=$SPhase;
		foreach($Targets as $tgt => $group) {
			$group=preg_replace('/[^0-9a-z_ -]/sim', '', $group);
			if(!$Session) $Session=substr($tgt,0,1);
			if(!$Group) $Group=$group;
			if(isset($_REQUEST['del'])) {
				safe_w_sql("delete from TargetGroups
					where TgTournament={$_SESSION['TourId']} and TgTargetNo='$tgt'");
			} else {
				safe_w_sql("insert into TargetGroups
					set TgTournament={$_SESSION['TourId']},
					TgSession=".substr($tgt,0,1).",
					TgSesType='".$SesType.($SPhase ? $SPhase : '')."',
					TgTargetNo='$tgt',
					TgGroup=".StrSafe_DB($group)."
					on duplicate key update
					TgGroup=".StrSafe_DB($group)."
							");
			}
		}
	}
}


header('Content-Type: text/xml');

print '<response>' . "\n";
print '<error>0</error>' . "\n";

if(isset($_REQUEST['new'])) {
	require_once('./lib.php');

	switch($SesType) {
		case 'Q':
			$q=safe_r_sql(getSesSQL('Q', $Session));
			$SesRow=safe_fetch($q);
			$SesRow->Range=Range($SesRow->SesFirstTarget, $SesRow->SesTar4Session+$SesRow->SesFirstTarget-1);
			break;
		case 'E':
			$q=safe_r_sql(getSesSQL('E', $Session, $Phase));
			$SesRow=safe_fetch($q);
			$SesRow->Range=explode(',', $SesRow->SesTar4Session);
			break;
	}

	$ret=BuildGroups($SesType, $Session, $Phase, $SesRow->Range, $Group);

	echo "<row><![CDATA[$ret]]></row>";
}

print '</response>' . "\n";

