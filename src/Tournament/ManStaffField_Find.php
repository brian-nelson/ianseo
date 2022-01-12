<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error' => 0, 'rows' => array());

if(!CheckTourSession() or !hasACL(AclCompetition, AclReadWrite) or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

switch($_REQUEST['act']) {
	case 'find':
		if(empty($_REQUEST['Code'])) {
			JsonOut($JSON);
		}
		$q=safe_r_sql("("
			."select '1' as LUE, LueFamilyName as FamName, LueName as GivName, LueSex as Gender, LueCountry as CoCode, LueCoShort as CoName from LookUpEntries inner join Tournament on ToId={$_SESSION['TourId']} and ToIocCode=LueIocCode where LueCode=".StrSafe_DB($_REQUEST['Code'])
			.") union ("
			."select '0' as LUE, EnFirstName as FamName, EnName as GivName, EnSex as Gender, CoCode, CoName from Entries inner join Countries on CoId=EnCountry and CoTournament=EnTournament where EnTournament={$_SESSION['TourId']} and EnCode=".StrSafe_DB($_REQUEST['Code'])."
			)");

		while($r=safe_fetch($q)) {
			$JSON['rows'][]=$r;
		}

		if(!$JSON['rows']) {
			$JSON['rows'][]=array(
				'LUE'=>'1',
				'FamName'=>'',
				'GivName' =>'',
				'Gender'=>'',
				'CoCode'=>'',
				'CoName'=>''
			);
		}
		break;
	case 'search':
		$Where=array(array(),array());
		if(!empty($_REQUEST['Code'])) {
			$Where[1][]="LueCode = ".StrSafe_DB($_REQUEST['Code']);
			$Where[0][]="EnCode = ".StrSafe_DB($_REQUEST['Code']);
		}
		if(!empty($_REQUEST['FamilyName'])) {
			$Where[1][]="LueFamilyName like '%".StrSafe_DB($_REQUEST['FamilyName'], true)."%'";
			$Where[0][]="EnFirstName like '%".StrSafe_DB($_REQUEST['FamilyName'], true)."%'";
		}
		if(!empty($_REQUEST['GivenName'])) {
			$Where[1][]="LueName like '%".StrSafe_DB($_REQUEST['GivenName'], true)."%'";
			$Where[0][]="EnName like '%".StrSafe_DB($_REQUEST['GivenName'], true)."%'";
		}
		if(!empty($_REQUEST['Gender'])) {
			$Where[1][]="LueSex like '%".StrSafe_DB($_REQUEST['Gender'], true)."%'";
			$Where[0][]="EnSex like '%".StrSafe_DB($_REQUEST['Gender'], true)."%'";
		}
		if(!empty($_REQUEST['CoCode'])) {
			$Where[1][]="LueCountry like '%".StrSafe_DB($_REQUEST['CoCode'], true)."%'";
			$Where[0][]="CoCode like '%".StrSafe_DB($_REQUEST['CoCode'], true)."%'";
		}
		if(!empty($_REQUEST['CoName'])) {
			$Where[1][]="LueCoShort like '%".StrSafe_DB($_REQUEST['CoName'], true)."%'";
			$Where[0][]="CoName like '%".StrSafe_DB($_REQUEST['CoName'], true)."%'";
		}
		if(!$Where[0]) {
			JsonOut($JSON);
		}
		$q=safe_r_sql("("
			."select '1' as LUE, LueCode as Code, LueFamilyName as FamName, LueName as GivName, LueSex as Gender, LueCountry as CoCode, LueCoShort as CoName, if(LueCtrlCode=0,'',LueCtrlCode) as DOB 
				from LookUpEntries 
			    inner join Tournament on ToId={$_SESSION['TourId']} and ToIocCode=LueIocCode 
				where ".implode(' and ', $Where[1])
			.") union ("
			."select '0' as LUE, EnCode as Code, EnFirstName as FamName, EnName as GivName, EnSex as Gender, CoCode, CoName, if(EnDob=0,'',EnDob) as DOB 
				from Entries 
			    inner join Countries on CoId=EnCountry and CoTournament=EnTournament 
				where EnTournament={$_SESSION['TourId']} and ".implode(' and ', $Where[0])."
			)
			order by FamName, GivName, LUE");

		while($r=safe_fetch($q)) {
			$JSON['rows'][]=$r;
		}

		break;
}

JsonOut($JSON);
