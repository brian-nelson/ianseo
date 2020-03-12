<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');

if(empty($_GET['T'])) die('<html><head><script type="text/javascript">window.close();</script></head></html>');

require_once('Common/pdf/ResultPDF.inc.php');

$Sess=$_GET['T'];
$Dist=empty($_GET['D']) ? '' : intval($_GET['D']);
$Type=empty($_GET['S']) ? '' : $_GET['S'];

switch($Type) {
	case 'Q':
		$Sess=intval($Sess);
		$MyQuery = "SELECT EnCode as Bib
				, EnName AS Name
				, upper(EnFirstName) AS FirstName
				, QuSession AS Session
				, SUBSTRING(QuTargetNo,2) AS TargetNo
				, CoCode AS NationCode, CoName AS Nation
				, EnClass AS ClassCode, ClDescription
				, EnDivision AS DivCode, DivDescription
				, EnSubClass as SubClass
				, SesName
			FROM Entries
			inner JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
			inner JOIN Qualifications ON EnId=QuId and QuSession=$Sess
			inner JOIN Divisions ON EnTournament=DivTournament AND EnDivision=DivId
			inner JOIN Classes ON EnTournament=ClTournament AND EnClass=ClId
			inner join Session on SesOrder=$Sess and SesTournament=EnTournament and SesType='Q'
			WHERE EnAthlete=1
				AND EnTournament = {$_SESSION['TourId']} AND EnStatus<=1
				AND QuConfirm & ".pow(2, $Dist)."=0
			ORDER BY TargetNo, FirstName, Name  ";

		$a=new StdClass();
		$a->distance=$Dist;
		$a->session=get_text('Session').' '.$Sess;

		$q=safe_r_sql("select SesName from Session where SesType='$Type' and SesTournament={$_SESSION['TourId']} and SesOrder=$Sess");
		if($r=safe_fetch($q) and $r->SesName) $a->session=$r->SesName;
		break;
	case 'E':
		$Sess=intval($Sess);
		$MyQuery = "SELECT EnCode as Bib
				, EnName AS Name
				, upper(EnFirstName) AS FirstName
				, ElElimPhase AS Session
				, ElTargetNo AS TargetNo
				, CoCode AS NationCode, CoName AS Nation
				, '' as ClDescription
				, EvEventName as DivDescription
				, EnSubClass as SubClass
				, SesName
			FROM Entries
			inner JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
			inner JOIN Eliminations ON EnId=ElId and ElElimPhase=".($Dist-1)." and ElTournament=EnTournament and ElSession={$_GET['T']}
			inner JOIN Events ON EvTournament=ElTournament AND EvTeamEvent=0 and EvCode=ElEventCode
			left join Session on SesOrder=ElSession and SesTournament=EnTournament and SesType='E'
			WHERE EnAthlete=1
				AND EnTournament = {$_SESSION['TourId']} AND EnStatus<=1
				AND ElConfirm=0
			ORDER BY ElTargetNo, FirstName, Name ";

		$a=new StdClass();
		$a->distance=$Dist;
		$a->session=get_text('Session').' '.$Sess;

		$q=safe_r_sql("select SesName from Session where SesType='$Type' and SesTournament={$_SESSION['TourId']} and SesOrder=$Sess");
		if($r=safe_fetch($q) and $r->SesName) $a->session=$r->SesName;
		break;
	case 'F':
		list($Date,$Time)=explode('|', $Sess);

		$MyQuery = "(SELECT e1.EnCode as Bib
				, concat(upper(e1.EnFirstName), ' ', e1.EnName) AS FirstName
				, concat(upper(e2.EnFirstName), ' ', e2.EnName) AS Name
				, GrPhase AS Session
				, FSTarget AS TargetNo
				, c1.CoCode AS NationCode, concat(c1.CoName, ' - ', c2.CoName) AS Nation
				, '' as ClDescription
				, EvEventName as DivDescription
				, e1.EnSubClass as SubClass
				, date_format(concat(FSScheduledDate,' ',FSScheduledTime), '%d-%m %H:%i') as SesName
			FROM FinSchedule
		    inner join Finals f1 on f1.FinTournament=FSTournament and f1.FinEvent=FSEvent and f1.FinMatchNo=FSMatchNo
		    inner join Finals f2 on f2.FinTournament=FSTournament and f2.FinEvent=FSEvent and f2.FinMatchNo=FSMatchNo+1
		    inner join Grids on GrMatchNo=f1.FinMatchNo
			inner join Entries e1 on e1.EnId=f1.FinAthlete and e1.EnTournament=FsTournament
			inner join Entries e2 on e2.EnId=f2.FinAthlete and e2.EnTournament=FsTournament
			inner JOIN Countries c1 ON c1.CoId=e1.EnCountry AND c1.CoTournament=FSTournament
			inner JOIN Countries c2 ON c2.CoId=e2.EnCountry AND c2.CoTournament=FSTournament
			inner JOIN Events ON EvTournament=FsTournament AND EvTeamEvent=0 and EvCode=FSEvent
			left join Session on SesTournament=FsTournament and SesType='F' and SesDtStart<=".StrSafe_DB($Date.' '.$Time)." and SesDtEnd>".StrSafe_DB($Date.' '.$Time)."
			WHERE FSScheduledDate=".StrSafe_DB($Date)." 
				and FSScheduledTime=".StrSafe_DB($Time)."
				AND FsTournament = {$_SESSION['TourId']} 
				AND f1.FinConfirmed=0 
				and FsTeamEvent=0
				and FsMatchNo%2=0)
			UNION
			(SELECT '' as Bib
				, '' AS FirstName
				, '' AS Name
				, GrPhase AS Session
				, FSTarget AS TargetNo
				, c1.CoCode AS NationCode, concat(c1.CoName, ' - ', c2.CoName) AS Nation
				, '' as ClDescription
				, EvEventName as DivDescription
				, '' as SubClass
				, date_format(concat(FSScheduledDate,' ',FSScheduledTime), '%d-%m %H:%i') as SesName
			FROM FinSchedule
		    inner join TeamFinals f1 on f1.TfTournament=FSTournament and f1.TfEvent=FSEvent and f1.TfMatchNo=FSMatchNo
		    inner join TeamFinals f2 on f2.TfTournament=FSTournament and f2.TfEvent=FSEvent and f2.TfMatchNo=FSMatchNo+1
		    inner join Grids on GrMatchNo=f1.TfMatchNo
			inner JOIN Countries c1 ON c1.CoId=f1.TfTeam AND c1.CoTournament=FSTournament
			inner JOIN Countries c2 ON c2.CoId=f2.TfTeam AND c2.CoTournament=FSTournament
			inner JOIN Events ON EvTournament=FsTournament AND EvTeamEvent=1 and EvCode=FSEvent
			left join Session on SesTournament=FsTournament and SesType='F' and SesDtStart<=".StrSafe_DB($Date.' '.$Time)." and SesDtEnd>".StrSafe_DB($Date.' '.$Time)."
			WHERE FSScheduledDate=".StrSafe_DB($Date)." 
				and FSScheduledTime=".StrSafe_DB($Time)."
				AND FsTournament = {$_SESSION['TourId']} 
				AND f1.TfConfirmed=0 
				and FsTeamEvent=1
				and FsMatchNo%2=0)
			ORDER BY TargetNo, FirstName, Name ";

		$a=(object) array('distance' => $Date.' '.substr($Time, 0, 5), 'session' => get_text('Matches', 'InfoSystem'));

	//debug_svela($MyQuery);

}


$PDF_TITLE=get_text('MissingScorecards', 'Tournament', $a);

$pdf = new ResultPDF($PDF_TITLE);

$pdf->SetFont('','b',30);
$pdf->Cell(0, 0, $PDF_TITLE, 0, 1, 'C');
$pdf->SetY($pdf->gety()+5);

$Order="FirstName, Name, TargetNo";



$Unit=($pdf->getPageWidth()-20)/150;

$AthCel=$Unit*40;
$NatCel=$Unit*35;
$TgtCel=$Unit*10;
$SesCel=$Unit*15;
$CatCel=$Unit*50;

$pdf->SetFont('','B',12);
$pdf->Cell($AthCel, 0, get_text('Athlete'), 1, 0, 'C', 1);
$pdf->Cell($NatCel, 0, get_text('Nation'), 1, 0, 'C', 1);
$pdf->Cell($TgtCel, 0, get_text('Target'), 1, 0, 'C', 1);
$pdf->Cell($SesCel, 0, get_text('Session'), 1, 0, 'C', 1);
$pdf->Cell($CatCel, 0, get_text('DivisionClass'), 1, 0, 'C', 1);
$pdf->ln();
$pdf->SetFont('','',8);

$q=safe_r_sql($MyQuery);
while($r=safe_fetch($q)) {
	if(!$pdf->SamePage(3.6)) {
		$pdf->AddPage();
		$pdf->SetFont('','B',12);
		$pdf->Cell(0, 0, $PDF_TITLE, 1, 0, 'C', 1);
		$pdf->ln();
		$pdf->Cell($AthCel, 0, get_text('Athlete'), 1, 0, 'C', 1);
		$pdf->Cell($NatCel, 0, get_text('Nation'), 1, 0, 'C', 1);
		$pdf->Cell($TgtCel, 0, get_text('Target'), 1, 0, 'C', 1);
		$pdf->Cell($SesCel, 0, get_text('Session'), 1, 0, 'C', 1);
		$pdf->Cell($CatCel, 0, get_text('DivisionClass'), 1, 0, 'C', 1);
		$pdf->ln();
		$pdf->SetFont('','',8);
	}
	$pdf->Cell($AthCel, 3.6, $r->FirstName.' '.$r->Name, 1, 0);
	$pdf->Cell($NatCel, 3.6, $r->Nation, 1, 0);
	$pdf->Cell($TgtCel, 3.6, ltrim($r->TargetNo, '0'), 1, 0, 'R');
	$pdf->Cell($SesCel, 3.6, $r->SesName ? $r->SesName : $r->Session, 1, 0);
	$pdf->Cell($CatCel, 3.6, $r->DivDescription . ' ' . $r->ClDescription, 1, 0);
	$pdf->ln();
}

$pdf->Output();