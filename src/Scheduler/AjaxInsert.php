<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclCompetition, AclReadWrite,false);
CheckTourSession(true);

require_once('./LibScheduler.php');

if(empty($_REQUEST['Fld'])) out();

$HasDay=false;
$q=array("SchTournament={$_SESSION['TourId']}");
$Order=1;
$day='';
$start='00:00:00';


foreach($_REQUEST['Fld'] as $Field => $Value) {
	switch($Field) {
		case 'Day':
			if(!$Value or $Value=='-') {
				$Value='';
			} elseif(strtolower(substr($Value, 0, 1))=='d') {
				$Value=date('Y-m-d', strtotime(sprintf('%+d days', substr($Value, 1) -1), $_SESSION['ToWhenFromUTS']));
			} else {
				$Value=CleanDate($Value);
			}

			if($Value) {
				$HasDay=true;
				$q[]="SchDay='$Value'";
				$day=$Value;
			}
			break;
		case 'Start':
			if(!$Value or $Value=='-') {
				$Value='';
			} else {
				$t=explode(':', $Value);
				if(count($t)==1) {
					$t[1]=$t[0]%60;
					$t[0]= intval($t[0]/60);
				}
				$Value=sprintf('%02d:%02d:00', $t[0], $t[1]);
				$start=$Value;
			}
			$q[] = "SchStart='$Value'";
			break;
		case 'Order':
			$Order=intval($Value);
			break;
		case 'Duration':
			$Value=intval($Value);
			$q[] = "SchDuration='$Value'";
			break;
		case 'Title':
		case 'SubTitle':
		case 'Text':
		case 'Targets':
			$q[]= "Sch{$Field}=".StrSafe_DB($Value);
			break;
		case 'Shift':
			if(strlen($Value)) {
				$Value=StrSafe_DB(intval($Value));
			} else {
				$Value='null';
			}
			$q[]= "SchShift=".$Value;
			break;
	}
}

if($HasDay) {
	if(!$Order) {
		$rs=safe_r_sql("select max(SchOrder) as MaxOrder from Scheduler where SchTournament={$_SESSION['TourId']} and SchDay='$day' and SchStart='$start'");
		if($r=safe_fetch($rs)) {
			$Order=$r->MaxOrder+1;
		}
	}
	$q[] = "SchOrder=$Order";
	safe_w_SQL("insert into Scheduler set ".implode(',', $q)." on duplicate key update SchOrder=SchOrder+1, ".implode(',', $q));
}

$Schedule=new Scheduler();
$Schedule->ROOT_DIR=$CFG->ROOT_DIR;
$ret=array('error' => 0, 'txt' => getScheduleTexts(), 'sch' => $Schedule->getScheduleHTML('SET'));
out($ret);
