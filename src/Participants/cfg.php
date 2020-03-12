<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_Various.inc.php');
CheckTourSession(true);
require_once('Common/Fun_FormatText.inc.php');
// require_once('Fun_Partecipants.local.inc.php');
require_once('Common/Fun_Sessions.inc.php');

$TourId=$_SESSION['TourId'];

if($_SESSION['AccreditationTourIds']) $TourId=$_SESSION['AccreditationTourIds'];

$AllTargets=(isset($_REQUEST['AllTargets']) ? intval($_REQUEST['AllTargets']) : 0);
$ShowTourCode=(isset($_REQUEST['ShowTourCode']) ? intval($_REQUEST['ShowTourCode']) : 0);
$ShowPicture=(isset($_REQUEST['ShowPicture']) ? intval($_REQUEST['ShowPicture']) : 1);
$ShowLocalBib=(isset($_REQUEST['ShowLocalBib']) ? intval($_REQUEST['ShowLocalBib']) : 0);
$ShowEmail=(isset($_REQUEST['ShowEmail']) ? intval($_REQUEST['ShowEmail']) : 1);
$ShowCaption=(isset($_REQUEST['ShowCaption']) ? intval($_REQUEST['ShowCaption']) : 1);
$ShowCountry2=(isset($_REQUEST['ShowCountry2']) ? intval($_REQUEST['ShowCountry2']) : 0);
$ShowCountry3=(isset($_REQUEST['ShowCountry3']) ? intval($_REQUEST['ShowCountry3']) : 0);
$ShowDisable=(isset($_REQUEST['ShowDisable']) ? intval($_REQUEST['ShowDisable']) : 1);
$ShowAgeClass=(isset($_REQUEST['ShowAgeClass']) ? intval($_REQUEST['ShowAgeClass']) : 1);
$ShowSubClass=(isset($_REQUEST['ShowSubClass']) ? intval($_REQUEST['ShowSubClass']) : 1);

$FilterRequest=(isset($_REQUEST['filter']) ? $_REQUEST['filter'] : array());

$Sort=(isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'sortTarget');
$SortOrder=(isset($_REQUEST['sortOrder']) ? ($_REQUEST['sortOrder']=='asc' ? 'asc' : 'desc') : 'asc');

$NegSort=array('asc' => 'desc', 'desc' => 'asc');

switch($Sort) {
	case 'sortPicture':
		$OrderBy="PhPhoto is not null $SortOrder, `Session` ASC, `TargetNo` ASC ";
		$RowKey='concat(PhPhoto is not null, "-", QuSession, "-", QuTarget) as RowKey';
		break;
	case 'sortTour':
		$OrderBy="ToCode $SortOrder, `Session` ASC, `TargetNo` ASC ";
		$RowKey='concat(ToCode, "-", QuSession, "-", QuTarget) as RowKey';
		break;
	case 'sortTarget':
		$OrderBy="ToCode, `Session` $SortOrder, `TargetNo` $SortOrder ";
		$RowKey='concat(ToCode, "-", QuSession, "-", QuTarget) as RowKey';
		break;
	case 'sortBib':
		$OrderBy="EnCode $SortOrder ";
		$RowKey='EnCode as RowKey';
		break;
	case 'sortLocalBib':
		$OrderBy="locBib $SortOrder ";
		$RowKey='zextra.EdExtra as RowKey';
		break;
	case 'sortFamName':
		$OrderBy="left(EnFirstName,1) $SortOrder, EnFirstName, EnName ";
		$RowKey='left(EnFirstName,1) as RowKey';
		break;
	case 'sortGivName':
		$OrderBy="left(EnName,1) $SortOrder, EnName, EnFirstName ";
		$RowKey='left(EnName,1) as RowKey';
		break;
	case 'sortEmail':
		$OrderBy="EdEmail $SortOrder, EnFirstName, EnName ";
		$RowKey='eextra.EdEmail as RowKey';
		break;
	case 'sortCaption':
		$OrderBy="AccrCaption $SortOrder, EnFirstName, EnName ";
		$RowKey='cextra.EdExtra as RowKey';
		break;
	case 'sortDob':
		$OrderBy="EnDob $SortOrder ";
		$RowKey='EnDob as RowKey';
		break;
	case 'sortSex':
		$OrderBy="EnSex $SortOrder ";
		$RowKey='EnSex as RowKey';
		break;
	case 'sortCoCode':
		$OrderBy="CoCode $SortOrder ";
		$RowKey='c.CoCode as RowKey';
		break;
	case 'sortCoName':
		$OrderBy="CoName $SortOrder ";
		$RowKey='c.CoName as RowKey';
		break;
	case 'sortCoCode2':
		$OrderBy="CoCode2 $SortOrder ";
		$RowKey='c2.CoCode as RowKey';
		break;
	case 'sortCoName2':
		$OrderBy="CoName2 $SortOrder ";
		$RowKey='c2.CoName as RowKey';
		break;
	case 'sortCoCode3':
		$OrderBy="CoCode3 $SortOrder ";
		$RowKey='c3.CoCode as RowKey';
		break;
	case 'sortCoName3':
		$OrderBy="CoName3 $SortOrder ";
		$RowKey='c3.CoName as RowKey';
		break;
	case 'sortDisable':
		$OrderBy="EnWChair $SortOrder ";
		$RowKey='EnWChair as RowKey';
		break;
	case 'sortDiv':
		$OrderBy="EnDivision $SortOrder, EnClass, EnFirstName, EnName ";
		$RowKey='concat(EnDivision, "-", EnClass) as RowKey';
		break;
	case 'sortAgeClass':
		$OrderBy="EnAgeClass $SortOrder, EnDivision, EnFirstName, EnName ";
		$RowKey='concat(EnAgeClass, "-", EnDivision) as RowKey';
		break;
	case 'sortClass':
		$OrderBy="EnClass $SortOrder, EnDivision, EnFirstName, EnName ";
		$RowKey='concat(EnClass, "-", EnDivision) as RowKey';
		break;
	case 'sortSubClass':
		$OrderBy="EnSubClass $SortOrder, EnDivision, EnClass, EnFirstName, EnName ";
		$RowKey='concat(EnSubClass, "-", EnDivision, "-", EnClass) as RowKey';
		break;
	default:
		$OrderBy="ToCode, `Session` ASC, `TargetNo` ASC ";
		$RowKey='concat(ToCode, "-", QuSession, "-", QuTarget) as RowKey';
}

