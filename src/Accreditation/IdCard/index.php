<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/pdf/LabelPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_DateTime.inc.php');

if (!CheckTourSession()) {
	print get_text('CrackError');
	exit;
}
checkACL(AclAccreditation, AclReadWrite);

$TmpWhere="";
if(isset($_REQUEST["ArcherName"]) && strlen($_REQUEST["ArcherName"])>0 && preg_match("/^[-,0-9A-Z]*$/i",str_replace(" ","",$_REQUEST["ArcherName"])))
{
	foreach(explode(",",$_REQUEST["ArcherName"]) as $Value)
	{
		$Tmp=NULL;
		if(preg_match("/^([0-9A-Z]*)\-([0-9A-Z]*)$/i",str_replace(" ","",$Value),$Tmp))
			$TmpWhere .= "(EnFirstName >= " . StrSafe_DB(strtoupper($Tmp[1]) ) . " AND EnFirstName <= " . StrSafe_DB(strtoupper($Tmp[2].chr(255))) . ") OR ";
		else
			$TmpWhere.= "EnFirstName LIKE " . StrSafe_DB(strtoupper(trim($Value)) . "%") . " OR ";
	}
}
if(isset($_REQUEST["CountryName"]) && strlen($_REQUEST["CountryName"])>0&& preg_match("/^[-,0-9A-Z]*$/i",str_replace(" ","",$_REQUEST["CountryName"])))
{
	foreach(explode(",",$_REQUEST["CountryName"]) as $Value)
	{
		$Tmp=NULL;
		if(preg_match("/^([0-9A-Z]*)\-([0-9A-Z]*)$/i",str_replace(" ","",$Value),$Tmp))
			$TmpWhere .= "(CoCode >= " . StrSafe_DB(strtoupper($Tmp[1]) ) . " AND CoCode <= " . StrSafe_DB(strtoupper($Tmp[2].chr(255))) . ") OR ";
		else
			$TmpWhere.= "CoCode LIKE " . StrSafe_DB(strtoupper(trim($Value)) . "%") . " OR ";
	}
}
if(strlen($TmpWhere)>3)
	$TmpWhere = substr($TmpWhere,0,-3);

$MyQuery
	= "SELECT ToCode, ToName,ToCommitee,ToComDescr,	ToWhere,date_format(ToWhenFrom, '".get_text('DateFmtDB')."') AS DtFrom,date_format(ToWhenTo, '".get_text('DateFmtDB')."') AS DtTo, "
	. "ToImgL,ToImgR "
	. "FROM Tournament "
	. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

$rsInfo=safe_r_sql($MyQuery);

$rowInfo=null;
if (safe_num_rows($rsInfo)==1)
	$rowInfo=safe_fetch($rsInfo);

$MyQuery = "SELECT EnCode as Bib, EnName AS Name, upper(EnFirstName) AS FirstName, QuSession AS Session, ";
$MyQuery.= "SUBSTRING(QuTargetNo,2) AS TargetNo, CoCode AS NationCode, CoName AS Nation, ";
$MyQuery.= "EnClass AS ClassCode, EnDivision AS DivCode, EnAgeClass as AgeClass, DivDescription,ClDescription,EnSubClass as SubClass, EnStatus as Status, EnIndClEvent AS `IC`, EnTeamClEvent AS `TC`, EnIndFEvent AS `IF`, EnTeamFEvent as `TF`, ";
$MyQuery.= "AcColor, AcIsAthlete, PhPhoto, EnId ";
$MyQuery.= "FROM Entries AS e ";
$MyQuery.= "INNER JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$MyQuery.= "INNER JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
$MyQuery.= "INNER JOIN Qualifications AS q ON e.EnId=q.QuId ";
$MyQuery.= "LEFT JOIN AccColors AS ac ON ac.AcTournament=e.EnTournament AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE ac.AcDivClass ";
$MyQuery.= "LEFT JOIN Photos AS ph ON e.EnId=ph.PhEnId ";
$MyQuery.= "WHERE EnAthlete=1 AND EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
	if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
if($TmpWhere != "")
	$MyQuery .= "AND (" . $TmpWhere . ")";
$MyQuery.= "ORDER BY QuSession, FirstName, Name, TargetNo ";
//print $MyQuery;exit;
//print '<br><br>'.$MyQuery;exit;

$PosX = array(6,111,6,111);
$PosY = array(6,6,155,155);
//echo $MyQuery;

$Rs=safe_r_sql($MyQuery);
if (safe_num_rows($Rs)>0)
{
	$pdf=new LabelPDF(); // 'P','mm','A4');
	$pdf->AliasNbPages();
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->SetMargins(6,6,6);
	$pdf->SetAutoPageBreak(false, 15);
	$pdf->SetTextColor(0x00, 0x00, 0x00);
	$i=0;
	while ($MyRow=safe_fetch($Rs))
	{
		$pdf->SetFont($pdf->FontStd,'B',18);
		if($i % 4 == 0)
		{
			$pdf->AddPage();
			//Crop Mark
			$pdf->Line(2,148.5,5,148.5);
			$pdf->Line(103,148.5,107,148.5);
			$pdf->Line(205,148.5,208,148.5);
			$pdf->Line(105,2,105,5);
			$pdf->Line(105,146.5,105,150.5);
			$pdf->Line(105,292,105,295);

		}
		$myPosX=$PosX[$i%4];
		$myPosY=$PosY[$i%4];
		//$pdf->SetFillColor($ColorArray[$MyRow->Session-1][0],$ColorArray[$MyRow->Session-1][1],$ColorArray[$MyRow->Session-1][2]);
	// solo per batumi
		if (!is_null($MyRow->AcColor))
			$pdf->SetFillColor(base_convert(substr($MyRow->AcColor,0,2), 16, 10),base_convert(substr($MyRow->AcColor,2,2), 16, 10),base_convert(substr($MyRow->AcColor,4,2), 16, 10));
		else
			$pdf->SetFillColor(255,255,255);

		$pdf->SetDrawColor(0x00,0x00,0x00);
	// riquadro colore
		$pdf->Rect($myPosX, $myPosY, 93, 93, 'F');
		//		$pdf->Rect($myPosX, $myPosY, 93, 20, 'F');
		//		$pdf->Rect($myPosX, $myPosY+20, 93, 10, 'F');
		//		$pdf->Rect($myPosX, $myPosY+65, 93, 10, 'F');
		//		$pdf->Rect($myPosX, $myPosY+85, 93, 10, 'F');
	// bordo
		$pdf->Rect($myPosX, $myPosY, 93, 136, 'D');
	//Header
		$pdf->SetXY($myPosX,$myPosY+7.5);
		$pdf->Cell(93 , 6, $rowInfo->ToName, 0, 1, 'C', 0);
		$pdf->SetFont($pdf->FontStd,'I',8);
		$pdf->SetX($myPosX);
		$pdf->Cell(93 , 6, $rowInfo->ToWhere .', ' . TournamentDate2String($rowInfo->DtFrom,$rowInfo->DtTo) , 0, 1, 'C', 2);

		$pdf->SetDrawColor(0x99,0x00,0x00);

		if($MyRow->PhPhoto and file_exists($img=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-En-'.$MyRow->EnId.'.jpg')) {
			list($w, $h, $d, $d)=getimagesize($img);
			$height = ($h * 20 / $w);
			$pdf->Image($img, $myPosX+36.5, $pdf->GetY()+5, 20, 0);
			$pdf->Rect($myPosX+36.5, $pdf->GetY()+5, 20, $height,'D');
		}

	//Matricola
		if(is_null($MyRow->PhPhoto) && $MyRow->AcIsAthlete==1)
			$pdf->SetXY($myPosX+5,$myPosY+30);//+$height);
		else
			$pdf->SetXY($myPosX+5,$myPosY+43);//+$height);
		$pdf->SetFont($pdf->FontStd,'I',12);
		$pdf->Cell(30 , 3, get_text('Code','Tournament'), 0, 1, 'L', 0);
		//$pdf->SetX($pdf->GetX()+5);
		//$pdf->Cell(30 , 3, 'Padiglione Fiera', 0, 1, 'L', 0);
		$pdf->SetX($myPosX+5);
		$pdf->SetFont($pdf->FontStd,'B',12);
		$pdf->Cell(30 , 4, $MyRow->Bib, 0, 1, 'L', 0);
		//$pdf->SetX($pdf->GetX()+5);
		//$pdf->Cell(30 , 4, $TurniArray[$MyRow->Session-1], 0, 1, 'L', 0);
	//Atleta
		if(is_null($MyRow->PhPhoto) && $MyRow->AcIsAthlete==1)
			$pdf->SetXY($myPosX+5,$myPosY+41);//+$height);

		$pdf->SetXY($myPosX+5,$pdf->GetY()+2);
		$pdf->SetFont($pdf->FontStd,'I',12);
		$pdf->Cell(83 , 4, ($MyRow->AcIsAthlete == 1 ? get_text('Athlete') : get_text('Name','Tournament')), 0, 1, 'L', 0);
		$pdf->SetX($myPosX+5);
		$pdf->SetFont($pdf->FontStd,'B',12);
		$pdf->Cell(83 , 4, $MyRow->FirstName . ' ' . $MyRow->Name, 0, 1, 'L', 1);
	//Società
		$pdf->SetXY($myPosX+5,$pdf->GetY()+2);
		$pdf->SetFont($pdf->FontStd,'I',12);
		$pdf->Cell(83 , 4, get_text('Country'), 0, 1, 'L', 0);
		$pdf->SetX($myPosX+5);
		$pdf->SetFont($pdf->FontStd,'B',12);
		$pdf->Cell(83 , 4,  $MyRow->NationCode . " - " .  $MyRow->Nation, 0, 1, 'L',1);

		if(is_null($MyRow->PhPhoto) && $MyRow->AcIsAthlete==1)
		{
	//Divisione
			$pdf->SetXY($myPosX+5,$pdf->GetY()+2);
			$pdf->SetFont($pdf->FontStd,'I',12);
			$pdf->Cell(83 , 4, get_text('Division'), 0, 1, 'L', 0);
	// valore divisione
			$pdf->SetX($myPosX+5);
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->Cell(83 , 4,  get_text($MyRow->DivDescription,'','',true), 0, 1, 'L', 0);
	// Classe
			$pdf->SetXY($myPosX+5,$pdf->GetY()+2);
			$pdf->SetFont($pdf->FontStd,'I',12);
			$pdf->Cell(83 , 4, get_text('Class'), 0, 1, 'L', 0);
	// valore classe
			$pdf->SetX($myPosX+5);
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->Cell(83 , 4,  get_text($MyRow->ClDescription,'','',true), 0, 1, 'L', 0);
		}
		else
		{
	//Divisione
			$pdf->SetXY($myPosX+5,$pdf->GetY()+2);
			$pdf->SetFont($pdf->FontStd,'I',12);
			$pdf->Cell(($MyRow->AcIsAthlete==1 ? 40 : 83), 4, ($MyRow->AcIsAthlete==1 ? get_text('Division') : get_text('Type','Tournament')), 0, ($MyRow->AcIsAthlete==1 ? 0 : 1), 'L', 0);
	// Classe
			if($MyRow->AcIsAthlete==1)
			{
				$pdf->SetXY($pdf->GetX()+3,$pdf->GetY());
				$pdf->SetFont($pdf->FontStd,'I',12);
				$pdf->Cell(40 , 4, get_text('Class'), 0, 1, 'L', 0);
			}
	// valore divisione
			$pdf->SetX($myPosX+5);
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->Cell(($MyRow->AcIsAthlete==1 ? 40 : 83) , 4, ($MyRow->AcIsAthlete==1 ? get_text($MyRow->DivDescription,'','',true) : get_text($MyRow->ClDescription,'','',true)), 0, ($MyRow->AcIsAthlete==1 ? 0 : 1), 'L', 0);
	// valore classe
			if($MyRow->AcIsAthlete==1)
			{
				$pdf->SetX($pdf->GetX()+3);
				$pdf->SetFont($pdf->FontStd,'B',12);
				$pdf->Cell(40 , 4,  get_text($MyRow->ClDescription,'','',true), 0, 1, 'L', 0);
			}

		}
		//Ind/team
		/*$pdf->SetXY($myPosX+5,$pdf->GetY()+2);
		$pdf->SetFont($pdf->FontStd,'I',8);
		$pdf->Cell(39 , 4, 'Partecipazione Individuale', 0, 0, 'L', 0);
		$pdf->SetX($pdf->GetX()+5);
		$pdf->Cell(39 , 4, 'Partecipazione a Squadre', 0, 1, 'L', 0);
		$pdf->SetX($myPosX+5);
		$pdf->SetFont($pdf->FontStd,'B',10);
		$Tmp="";
		$Tmp .= ($MyRow->IC==1 ?  'Classe' : '');
		$Tmp .= ($MyRow->IF==1 ?  (strlen($Tmp)>0 ? ' - ' : '') . 'Assoluto' : '');
		if($Tmp=='')
			$Tmp = str_repeat('-',17);
		$pdf->Cell(39 , 4, $Tmp, 0, 0, 'L', 0);
		$pdf->SetX($pdf->GetX()+5);
		$Tmp="";
		$Tmp .= ($MyRow->TC==1 ?  'Classe' : '');
		$Tmp .= ($MyRow->TF==1 ?  (strlen($Tmp)>0 ? ' - ' : '') . 'Assoluto' : '');
		if($Tmp=='')
			$Tmp = str_repeat('-',17);
		$pdf->Cell(39 , 4, $Tmp, 0, 1, 'L', 0);*/
	//Barcode
		//$pdf->SetXY($myPosX+5,$pdf->GetY()+8);
		$pdf->SetXY($myPosX+5,$pdf->GetY()+8);
		$pdf->SetFont('barcode','',40);
		$pdf->Cell(83 , 10, '*' . substr("00000" . $MyRow->Bib,-5) . '*', 0, 1, 'C', 0);
		$pdf->SetX($myPosX+15);
		$pdf->SetFont($pdf->FontStd,'',8);
		$pdf->Cell(63 , 3, substr("00000" . $MyRow->Bib,-5), 0, 1, 'C', 0);

		//Loghi
		//$pdf->Image($CFG->DOCUMENT_PATH . 'Accreditation/IdCard/CI2008.jpg', $myPosX+1, $myPosY+120, 0, 15);
		//$pdf->Image($CFG->DOCUMENT_PATH . 'Accreditation/IdCard/fitarco.jpg', $myPosX+77, $myPosY+120, 0, 15);
		//$pdf->Image($CFG->DOCUMENT_PATH . 'Accreditation/IdCard/Org.jpg', $myPosX+41, $myPosY+124, 0, 11);

		if(strlen($rowInfo->ToImgL) > 0)
		{
			$im = imagecreatefromstring($rowInfo->ToImgL);
			if ($im !== false)
			{
				$pdf->Image('@'.$rowInfo->ToImgL, $myPosX+1, $myPosY+115, 0, 20);
				imagedestroy($im);
			}
		}

		if(strlen($rowInfo->ToImgR) > 0)
		{
			$im = imagecreatefromstring($rowInfo->ToImgR);
			if ($im !== false)
			{
				$pdf->Image('@'.$rowInfo->ToImgR,$myPosX+72, $myPosY+115,0,20);
				imagedestroy($im);
			}
		}

		//$pdf->Image($CFG->DOCUMENT_PATH . 'Accreditation/IdCard/CI2008.jpg', $myPosX+1, $myPosY+110, 0, 15);
		//$pdf->Image($CFG->DOCUMENT_PATH . 'Accreditation/IdCard/fitarco.jpg', $myPosX+77, $myPosY+110, 0, 15);
		$i++;
	}
	$pdf->Output();
}
?>