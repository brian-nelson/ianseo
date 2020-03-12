<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession()) {
        exit;
    }
    checkACL(AclISKServer, AclReadWrite,false);

	$error=0;
	$order=0;
	$xml='';

	$OrderBy='IskDvTarget+0, IskDvTargetReq, IskDvCode';
	$ord=($_REQUEST['ord']=='orddesc' ? 'DESC' : '');
	if(!empty($_REQUEST['field'])) {
		switch($_REQUEST['field']) {
			case 'tgtOrder':
				$OrderBy="IskDvTarget+0 $ord, IskDvTargetReq, IskDvCode";
				break;
			case 'idOrder':
				$OrderBy="IskDvDevice $ord, IskDvTarget+0 , IskDvTargetReq, IskDvCode";
				break;
			case 'codeOrder':
				$OrderBy="IskDvVersion $ord, IskDvCode $ord, IskDvTarget+0 , IskDvTargetReq, IskDvCode";
				break;
			case 'batteryOrder':
				$OrderBy="abs(IskDvBattery) $ord, IskDvTarget+0 , IskDvTargetReq, IskDvCode";
				break;
			default:
				$OrderBy='IskDvTarget+0, IskDvTargetReq, IskDvCode';
		}
	}

	$Colors=array('#FFFFFF', '#FFCCCC', '#FF7777', '#FF0000');

	$Select
		= "SELECT IskDevices.*, if(IskDvState=1, least(3, round((time_to_sec(utc_timestamp())-time_to_sec(IskDvLastSeen))/65)), 0) as Difference, time_to_sec(utc_timestamp())-time_to_sec(IskDvLastSeen) as Seconds
			FROM IskDevices ORDER BY IskDvTournament={$_SESSION['TourId']} desc, $OrderBy";
	$Rs=safe_r_sql($Select);
	if ($Rs && safe_num_rows($Rs)>0) {
		while ($myRow=safe_fetch($Rs)) {
			$xml.='<tablet '.
				'order="'.$order++.'" '.
				'device="' . $myRow->IskDvDevice . '" ' .
				'tournament="' . $myRow->IskDvTournament . '" ' .
				'code="' . $myRow->IskDvCode . '" ' .
				'target="' . $myRow->IskDvTarget . '" ' .
				'reqtarget="' . $myRow->IskDvTargetReq . '" ' .
				'state="' . $myRow->IskDvState . '" ' .
				'appversion="' . $myRow->IskDvVersion . '" ' .
				'appdevversion="' . $myRow->IskDvAppVersion . '" ' .
				'battery="' . abs($myRow->IskDvBattery) . ($myRow->IskDvBattery < 0 ? '+' : '') . '" ' .
				'authrequest="' . $myRow->IskDvAuthRequest . '" ' .
				'ip="' . $myRow->IskDvIpAddress . '" ' .
				'online="' . $Colors[$myRow->Difference] . '" ' .
				'seconds="' . ($myRow->IskDvTournament==$_SESSION['TourId'] ? $myRow->Seconds : '') . '" ' .
				'lastseen="' . $myRow->IskDvLastSeen . '"/>';
		}
	}


	header('Content-Type: text/xml');
	print '<response error="' . $error . '">';
	print $xml;
	print '</response>';
