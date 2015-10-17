<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');

if(!isset($isCompleteResultBook))
	$pdf = new OrisPDF('C32B', 'Entries by Event');
else
	$pdf->setOrisCode('', 'Entries by Event');

$TmpWhere="";
if(isset($_REQUEST["CountryName"]) && preg_match("/^[-,0-9A-Z]*$/i",str_replace(" ","",$_REQUEST["CountryName"])))
{
	foreach(explode(",",$_REQUEST["CountryName"]) as $Value)
	{
		$Tmp=NULL;
		if(preg_match("/^([A-Z0-9]*)-([A-Z0-9]*)$/i",str_replace(" ","",$Value),$Tmp))
			$TmpWhere .= "(CoCode >= " . StrSafe_DB(stripslashes($Tmp[1]) ) . " AND CoCode <= " . StrSafe_DB(stripslashes($Tmp[2].chr(255))) . ") OR ";
		else
			$TmpWhere .= "CoCode LIKE " . StrSafe_DB(stripslashes(trim($Value)) . "%") . " OR ";
	}
	$TmpWhere = substr($TmpWhere,0,-3);
}

$MyQuery = "SELECT IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))) as EventCode, EnCode as Bib, EnName AS Name, upper(EnFirstName) AS FirstName, QuSession AS Session, SUBSTRING(QuTargetNo,2) AS TargetNo, CoCode AS NationCode, CoName AS Nation, IFNULL(EvEventName,CONCAT('|',DivDescription, '| |', ClDescription)) as EventName, cNumber ";
$MyQuery.= "FROM Entries AS e ";
$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
$MyQuery.= "INNER JOIN Qualifications AS q ON e.EnId=q.QuId ";
$MyQuery.= "INNER JOIN ( ";
$MyQuery.= "SELECT EnCountry AS cCode, COUNT(EnId) AS cNumber FROM `Entries` ";
$MyQuery.= "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " GROUP BY EnCountry ";
$MyQuery.= ") as sqy ON e.EnCountry=sqy.cCode ";
$MyQuery.= "LEFT JOIN Individuals AS i on e.EnId=i.IndId AND e.EnTournament=i.IndTournament ";
$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
$MyQuery.= "LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
$MyQuery.= "LEFT JOIN EventCategories AS ec ON ec.EcTeamEvent=0 AND i.IndTournament=ec.EcTournament AND i.IndEvent=ec.EvCode ";


$MyQuery.= "WHERE EnAthlete='1' AND EnTeamFEvent='1' AND QuSession!=0 AND EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
	$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
if($TmpWhere != "")
	$MyQuery .= "AND (" . $TmpWhere . ")";
$MyQuery.= "ORDER BY EvProgr, IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))), CoCode, FirstName, Name, TargetNo ";
//echo $MyQuery;exit;
$Rs=safe_r_sql($MyQuery);

$pdf->SetDataHeader(array("NOC","Country","Back No.","Name"), array(20,40,20,110));

if($Rs)
{
	$OldTeam='#@#@#';
	$OldEvent='#@#@#';
	while ($MyRow=safe_fetch($Rs))
	{
		if($OldEvent != $MyRow->EventCode)
		{
			$pdf->setEvent(get_text($MyRow->EventName,'','',true));
			$pdf->AddPage();
			$pdf->setOrisCode('C32B', 'Entries by Event');
			$OldTeam='#@#@#';
			$OldEvent = $MyRow->EventCode;
		}
		if($OldTeam != $MyRow->NationCode)
		{
			$pdf->SamePage($MyRow->cNumber + 1);
			$pdf->lastY += 3.5;
			$pdf->printDataRow(array(
				$MyRow->NationCode,
				$MyRow->Nation,
				$MyRow->TargetNo,
				$MyRow->FirstName . ' ' . $MyRow->Name
				));
			$OldTeam = $MyRow->NationCode;
		}
		else
		{
			$pdf->printDataRow(array(
				"",
				"",
				$MyRow->TargetNo,
				$MyRow->FirstName . ' ' . $MyRow->Name
				));
		}
	}
}
if(!isset($isCompleteResultBook))
{
	if(isset($_REQUEST['ToFitarco']))
	{
		$Dest='D';
		if (isset($_REQUEST['Dest']))
			$Dest=$_REQUEST['Dest'];
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
	}
	else
		$pdf->Output();
}

?>