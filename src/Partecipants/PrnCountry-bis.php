<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclParticipants, AclReadOnly);
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');

define("HideCols", GetParameter("IntEvent"));

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF(get_text('StartlistCountry','Tournament'));

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

$MyQuery = "(SELECT EnCode as Bib, EnName AS Name, upper(EnFirstName) AS FirstName, QuSession AS Session, QuTargetNo, SUBSTRING(QuTargetNo,2) AS TargetNo, CoCode AS NationCode, CoName AS Nation, EnSubTeam, EnClass AS ClassCode, ClDescription, EnDivision AS DivCode, DivDescription, EnAgeClass as AgeClass, EnSubClass as SubClass, EnStatus as Status, EnIndClEvent AS `IC`, EnTeamClEvent AS `TC`, EnIndFEvent AS `IF`, EnTeamFEvent as `TF`, EnTeamMixEvent as `TM`, IF(EnCountry2=0,0,1) as secTeam ";
$MyQuery.= "FROM Entries AS e ";
$MyQuery.= "LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
$MyQuery.= "LEFT JOIN Qualifications AS q ON e.EnId=q.QuId ";
$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
$MyQuery.= "LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
$MyQuery.= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
$MyQuery .= " AND QuSession >0 ";
if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
	$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
if($TmpWhere != "")
	$MyQuery .= "AND (" . $TmpWhere . ")";
$MyQuery .= ") UNION ALL ";
$MyQuery .= "(SELECT EnCode as Bib, EnName AS Name, EnFirstName AS FirstName, QuSession AS Session, QuTargetNo, SUBSTRING(QuTargetNo,2) AS TargetNo, CoCode AS NationCode, CoName AS Nation, EnSubTeam, EnClass AS ClassCode, ClDescription, EnDivision AS DivCode, DivDescription, EnAgeClass as AgeClass, EnSubClass as SubClass, EnStatus as Status, EnIndClEvent AS `IC`, EnTeamClEvent AS `TC`, EnIndFEvent AS `IF`, EnTeamFEvent as `TF`, EnTeamMixEvent as `TM`, 2 as secTeam  ";
$MyQuery.= "FROM Entries AS e ";
$MyQuery.= "LEFT JOIN Countries AS c ON e.EnCountry2=c.CoId AND e.EnTournament=c.CoTournament ";
$MyQuery.= "LEFT JOIN Qualifications AS q ON e.EnId=q.QuId ";
$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
$MyQuery.= "LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
$MyQuery.= "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EnCountry2!=0 AND (EnTeamClEvent!=0 OR EnTeamFEvent!=0 OR EnTeamMixEvent!=0) ";
$MyQuery .= " AND QuSession >0 ";
if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
	$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
if($TmpWhere != "")
	$MyQuery .= "AND (" . $TmpWhere . ")";
$MyQuery.= ") ORDER BY divcode, NationCode, FirstName, Name, TargetNo ";
//echo $MyQuery;exit;
$Rs=safe_r_sql($MyQuery);
if($Rs)
{
	$ShowStatusLegend = false;
	$FirstTime=true;
	$OldTeam='#@#@#';
	$OldSession='';
	while($MyRow=safe_fetch($Rs))
	{

		if($OldSession!=$MyRow->DivCode and !$FirstTime) {
			$pdf->AddPage();
			$FirstTime=true;
			$OldTeam='#@#@#';
		}
		$OldSession=$MyRow->DivCode;
		if ($FirstTime || !$pdf->SamePage(4))
		{
		   	$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(54, 4,  (get_text('Country')), 1, 0, 'L', 1);
			$pdf->Cell(7, 4,  (get_text('SessionShort','Tournament')), 1, 0, 'C', 1);
			$pdf->Cell(11, 4,  (get_text('Target')), 1, 0, 'C', 1);
			$pdf->Cell(10, 4,  (get_text('Code','Tournament')), 1, 0, 'C', 1);
			$pdf->Cell(41, 4,  (get_text('Athlete')), 1, 0, 'L', 1);
			if(!HideCols)
			{
				$pdf->Cell(11, 4,  (get_text('AgeCl')), 1, 0, 'C', 1);
				$pdf->Cell(8, 4,  (get_text('SubCl','Tournament')), 1, 0, 'C', 1);
			}
			$pdf->Cell(12+ (HideCols==true ? 22 : 0), 4,  (get_text('Division')), 1, 0, 'C', 1);
			$pdf->Cell(12+ (HideCols==true ? 21 : 0), 4,  (HideCols==true ? get_text('Class') : get_text('Cl')), 1, 0, 'C', 1);
			//Disegna i Pallini
			if(!HideCols)
			{
				$pdf->DrawParticipantHeader();
			   	$pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->Cell(10, 4,  (get_text('Status','Tournament')), 1, 0, 'C', 1);
			}
			$pdf->Cell(1, 4,  '', 0, 1, 'C', 0);
			$OldTeam='';
			$FirstTime=false;
		}
		if($OldTeam != $MyRow->NationCode)
		{
		   	$pdf->SetFont($pdf->FontStd,'B',1);
			$pdf->Cell(190, 1,  '', 0, 1, 'C', 0);
			if(isset($_REQUEST["NewPage"]))
				$pdf->AddPage();
			$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(8, 4,  $MyRow->NationCode, 'LTB', 0, 'C', 0);
			$pdf->Cell(46, 4,  $MyRow->Nation, 'RTB', 0, 'L', 0);
			$OldTeam = $MyRow->NationCode;
		}
		else
		{
			$pdf->Cell(54, 4,  '', 0, 0, 'C', 0);
		}
	   	$pdf->SetFont($pdf->FontStd,'',7);
		$pdf->Cell(7, 4,  ($MyRow->Session), 1, 0, 'R', 0);
		$pdf->Cell(11, 4,  ($MyRow->TargetNo), 1, 0, 'R', 0);
		$pdf->Cell(10, 4,  ($MyRow->Bib), 1, 0, 'R', 0);
		$pdf->Cell(41, 4,  $MyRow->FirstName . ' ' . $MyRow->Name  . ($MyRow->EnSubTeam==0 ? "" : " (" . $MyRow->EnSubTeam . ")"), 1, 0, 'L', 0);
		if(!HideCols)
		{
			$pdf->Cell(11, 4,  ($MyRow->AgeClass), 1, 0, 'C', 0);
			$pdf->Cell(8, 4,  ($MyRow->SubClass), 1, 0, 'C', 0);
		}
		$pdf->Cell(12 + (HideCols==true ? 22 : 0), 4,  (HideCols==true ? get_text($MyRow->DivDescription,'','',true) : $MyRow->DivCode), 1, 0, 'C', 0);
		$pdf->Cell(12 + (HideCols==true ? 21 : 0), 4,  (HideCols==true ? get_text($MyRow->ClDescription,'','',true) : $MyRow->ClassCode), 1, 0, 'C', 0);
//Disegna i Pallini per la partecipazione
		if(!HideCols)
		{
			if($MyRow->secTeam==0)
				$pdf->DrawParticipantDetails($MyRow->IC, $MyRow->IF, $MyRow->TC, $MyRow->TF, $MyRow->TM);
			elseif($MyRow->secTeam==1)
				$pdf->DrawParticipantDetails($MyRow->IC, $MyRow->IF, null, null, null);
			else
				$pdf->DrawParticipantDetails(null, null, $MyRow->TC, $MyRow->TF, $MyRow->TM);
			$pdf->SetDefaultColor();
			$pdf->SetFont($pdf->FontStd,'',7);
			$ShowStatusLegend = ($ShowStatusLegend || ($MyRow->Status!=0));
			$pdf->Cell(10, 4,  ($MyRow->Status==0 ? '' : ($MyRow->Status)) , 1, 0, 'C', 0);
		}
		$pdf->Cell(1, 4,  '', 0, 1, 'C', 0);
	}

//Legenda per la partecipazione alle varie fasi
	if(!HideCols)
	{
		$pdf->DrawPartecipantLegend();
//Legenda per lo stato di ammisisone alle gare
		if($ShowStatusLegend)
			$pdf->DrawStatusLegend();
	}
	safe_free_result($Rs);
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