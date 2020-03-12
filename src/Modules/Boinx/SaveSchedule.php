<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

	if (!CheckTourSession() || !isset($_REQUEST['type']) ) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclOutput,AclReadWrite, false);
	$Errore=0;

	$ret='';

	foreach($_REQUEST['type'] as $Type => $OnOff) {
		if(preg_match('/[^a-z0-9_]/i', $Type)) continue;

		if(substr($Type,0,7)=='Qua_Ind') {
			$q=safe_r_sql("select * from Session where SesTournament={$_SESSION['TourId']} and SesType='Q'");
			while($r=safe_fetch($q)) $ret.="<button>{$Type}_{$r->SesOrder}</button><onoff>0</onoff>";
			$ret.="<button>{$Type}_{$OnOff[0]}</button><onoff>$OnOff</onoff>";
		} elseif(substr($Type,0,7)=='Qua_Lst') {
			$q=safe_r_sql("select * from Session where SesTournament={$_SESSION['TourId']} and SesType='Q'");
			while($r=safe_fetch($q)) $ret.="<button>{$Type}_{$r->SesOrder}</button><onoff>0</onoff>";
			$ret.="<button>{$Type}_{$OnOff[0]}</button><onoff>$OnOff</onoff>";
		} else {
			$ret.="<button>{$Type}</button><onoff>$OnOff</onoff>";
		}

		if($OnOff) {
			// Inserts the schedule
			safe_w_sql("insert into BoinxSchedule set BsType='$Type', BsTournament={$_SESSION['TourId']}, BsExtra='".$OnOff."' on duplicate key update BsExtra='".$OnOff."'");
		} else {
			// removes from the schedule
			safe_w_sql("delete from BoinxSchedule where BsType='$Type' and BsTournament={$_SESSION['TourId']}");
		}

		if(substr($Type,0,3)=='Rss') {
			foreach(array('03', '10', '20', 'al') as $n) $ret.="<button>{$Type}_".sprintf('$%s$', $n)."</button><onoff>".($OnOff==$n?'1':'0')."</onoff>";
		}
	}

	header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print $ret;
	print '</response>' . "\n";

?>