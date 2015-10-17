<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);

require_once('./LibScheduler.php');

if(empty($_REQUEST['Fld'])) out();

$HasDay=false;
$q=array("SchTournament={$_SESSION['TourId']}");

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
			}
			$q[] = "SchStart='$Value'";
			break;
		case 'Duration':
			$Value=intval($Value);
			$q[] = "SchDuration='$Value'";
			break;
		case 'Title':
		case 'SubTitle':
		case 'Text':
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
	safe_w_SQL("insert into Scheduler set ".implode(',', $q)." on duplicate key update SchOrder=SchOrder+1, ".implode(',', $q));
}

$Schedule=new Scheduler();
$Schedule->ROOT_DIR=$CFG->ROOT_DIR;
$ret=array('error' => 0, 'txt' => getScheduleTexts(), 'sch' => $Schedule->getScheduleHTML('SET'));
out($ret);
