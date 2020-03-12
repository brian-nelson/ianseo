<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclParticipants, AclReadOnly);
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF(get_text('StartlistCountry','Tournament'));

$TmpWhere="";
if(isset($_REQUEST["CountryName"]) && preg_match("/^[-,0-9A-Z]*$/i",str_replace(" ","",$_REQUEST["CountryName"])))
{
	foreach(explode(",",$_REQUEST["CountryName"]) as $Value)
	{
		$Tmp=NULL;
		if(preg_match("/^([A-Z0-9]*)\-([A-Z0-9]*)$/i",str_replace(" ","",$Value),$Tmp))
			$TmpWhere .= "(CoCode >= " . StrSafe_DB(strtoupper($Tmp[1]) ) . " AND CoCode <= " . StrSafe_DB(strtoupper($Tmp[2].chr(255))) . ") OR ";
		else
			$TmpWhere .= "CoCode LIKE " . StrSafe_DB(strtoupper(trim($Value)) . "%") . " OR ";
	}
	$TmpWhere = substr($TmpWhere,0,-3);
}

$MyQuery = "SELECT EnCode as Bib, EnName AS Name, upper(EnFirstName) AS FirstName, "
	. "QuSession AS Session, SUBSTRING(QuTargetNo,2) AS TargetNo, "
	. "CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode, "
	. "ClDescription, EnDivision AS DivCode, DivDescription, EnAgeClass as AgeClass, "
	. "EnSubClass as SubClass, EnStatus as Status, "
	. "EnIndClEvent AS `IC`, EnTeamClEvent AS `TC`, EnIndFEvent AS `IF`, EnTeamFEvent as `TF`, "
	. "PhPhoto ";
$MyQuery.= "FROM Entries AS e ";
$MyQuery.= "LEFT JOIN Photos AS ph ON e.EnId=ph.PhEnId ";
$MyQuery.= "LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
$MyQuery.= "LEFT JOIN Qualifications AS q ON e.EnId=q.QuId ";
$MyQuery.= "LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament ";
$MyQuery.= "INNER JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament ";
$MyQuery.= "WHERE EnAthlete=1 AND EnTournament = " . StrSafe_DB($_SESSION["TourId"]) . " ";
if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
	$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
if($TmpWhere != "")
	$MyQuery .= "AND (" . $TmpWhere . ")";
$MyQuery.= "ORDER BY CoCode, DivViewOrder, FirstName, Name";
//*DEBUG*/echo $MyQuery;exit;
$Rs=safe_r_sql($MyQuery);
if (safe_num_rows($Rs)>0)
{
	$FirstTime=true;
	$OldTeam='#@#@#';
	$OldDiv='#@#@#';
	$ColNo=0;
	$RowY=0;
	$RowX=0;
	$MaxHeight=0;
//	$pdf->SetAutoPageBreak(false);
	while($MyRow=safe_fetch($Rs))
	{
		if($OldDiv != $MyRow->DivCode && !$pdf->SamePage($MaxHeight+20))
			$OldTeam='#@#@#';

//Cambio di Squadra
		if($OldTeam != $MyRow->NationCode)
		{
			if(!$FirstTime)
				$pdf->AddPage();

			$pdf->SetXY(10,$pdf->GetY()+5);
			$pdf->SetFont($pdf->FontStd,'B',20);
			$pdf->Cell(190, 8,  $MyRow->Nation . ' (' .  $MyRow->NationCode . ')', 0, 0, 'L', 1);
			$FirstTime=false;
			$RowNo=0;
			$ColNo=0;
			$MaxHeight=0;
			$OldTeam = $MyRow->NationCode;
			$OldDiv = '#@#@#';
		}
//Cambio di divisione
		if($OldDiv != $MyRow->DivCode)
		{
			$RowX=15;
			$pdf->SetXY($RowX,$pdf->GetY()+10);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell(185, 7,  $MyRow->DivDescription, 0, 1, 'L', 0);
			$RowY=$pdf->GetY()+1;
			$MaxHeight=0;
			$OldDiv = $MyRow->DivCode;
		}

//Carico l'immagine
		$pdf->SetDrawColor(0x99,0x00,0x00);

		$height=0;
		if(!is_null($MyRow->PhPhoto))
		{
			$im = imagecreatefromstring(base64_decode($MyRow->PhPhoto));
			$height += ((imagesy($im) * 20 / imagesx($im)));
			imagedestroy($im);

			$pdf->Image('@'.base64_decode($MyRow->PhPhoto), $RowX+6, $RowY, 20, 0);
			$pdf->Rect($RowX+6, $RowY, 20, $height,'D');
		}
		$MaxHeight = ($MaxHeight > $height ? $MaxHeight : $height);

// Inizio dei dati degli atleti
		$pdf->SetDefaultColor();
		$pdf->SetFont($pdf->FontStd,'',8);
		$pdf->SetXY($RowX, $RowY+($height >0 ? $height : $MaxHeight)+2);
		$pdf->Cell(32, 4, $MyRow->FirstName . ' ' . $MyRow->Name, 0, 0, 'C', 0);

		$RowX += 37;
		if($RowX>=185)
		{
			$RowX = 15;
			$RowY += ($MaxHeight+6+5);
			if(!$pdf->SamePage($MaxHeight+6+5))
				$OldTeam='#@#@#';
		}
	}

	safe_free_result($Rs);
}
if(!isset($isCompleteResultBook))
	$pdf->Output();
?>