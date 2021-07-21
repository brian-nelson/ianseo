<?php
require_once(dirname(dirname(__FILE__)).'/config.php');
// Include the main TCPDF library (search for installation path).
require_once('Common/pdf/ResultPDF.inc.php');

CheckTourSession(true);
checkACL(AclISKServer, AclReadWrite);

$Results=array();

$Session=-1;
if(!empty($_REQUEST['ses'])) {
	$tmp=explode('|',$_REQUEST['ses']);
	if($tmp[0]!='Q') {
		die('<html><script>window.close()</script></html>');
	}

	$Session=intval($tmp[1]);
}

$SubTitle='';
if($Session==-1) {
	// general PDF, so all numbers are per session
	$TotSql="select QuSession, count(*) as Total, ifnull(SesName, concat('Session ',QuSession)) as Session
		from Qualifications 
	    inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} 
		left join Session on SesType='Q' and SesTournament=EnTournament and SesOrder=QuSession
		group by QuSession
		order by QuSession";

	$SQL="select QuSession, count(*) as Total, greatest(QuIrmType, IndIrmType) as QualStatus, IrmType, QuHits as Arrows
		from Qualifications
		inner join Entries on QuId=EnId
		left join Individuals on IndId=QuId
		left join IrmTypes on IrmId=greatest(QuIrmType, IndIrmType)
		where EnTournament={$_SESSION['TourId']}
		group by QuSession, Arrows, IrmType
		order by QualStatus=0, QualStatus, Arrows, QuSession";
} else {
	// Session PDF, all numbers are per event
	$TotSql="select concat(EnDivision,EnClass) as QuSession, count(*) as Total, concat(ifnull(DivDescription, ''), ' ', ifnull(ClDescription, '')) as Session
		from Qualifications 
	    inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']}
		left join Divisions on DivTournament=EnTournament and DivId=EnDivision
		left join Classes on ClTournament=EnTournament and ClId=EnClass
		where QuSession=$Session
		group by concat(EnDivision,EnClass)
		order by DivViewOrder,ClViewOrder";

	$SQL="select concat(EnDivision,EnClass) as QuSession, count(*) as Total, greatest(QuIrmType, IndIrmType) as QualStatus, IrmType, QuHits as Arrows
		from Qualifications
	    inner join Entries on EnId=QuId  
		left join Individuals on IndId=QuId
		left join Divisions on DivTournament=EnTournament and DivId=EnDivision
		left join Classes on ClTournament=EnTournament and ClId=EnClass
		left join IrmTypes on IrmId=greatest(QuIrmType, IndIrmType)
		where EnTournament={$_SESSION['TourId']} and QuSession=$Session
		group by concat(EnDivision,EnClass), Arrows, IrmType
		order by QualStatus=0, QualStatus, Arrows, concat(EnDivision,EnClass)";

	$q=safe_r_sql("select SesName from Session where SesOrder=$Session and SesType='Q' and SesTournament={$_SESSION['TourId']}");
	if($r=safe_fetch($q)) {
		$SubTitle=$r->SesName;
	} else {
		$SubTitle=get_text('PopupStatusSession', 'Api', $Session);
	}
}

// FIRST STEP getting gross totals
$q=safe_r_sql($TotSql);
while($r=safe_fetch($q)) {
	$Results[$r->QuSession]=array(
		'Total' => $r->Total,
		'Name'=>$r->Session,
		'Arrows'=>array(),
		'IrmTypes'=>array(),
	);
}

$q=safe_r_sql($SQL);
$Arrows=array();
$IrmTypes=array();
while($r=safe_fetch($q)) {
	if($r->IrmType) {
		$IrmTypes[$r->QualStatus]=$r->IrmType;
		if(empty($Results[$r->QuSession]['IrmTypes'][$r->QualStatus])) {
			$Results[$r->QuSession]['IrmTypes'][$r->QualStatus]=0;
		}
		$Results[$r->QuSession]['IrmTypes'][$r->QualStatus]+=$r->Total;
	} else {
		$Arrows[$r->Arrows]=1;
		if(empty($Results[$r->QuSession]['Arrows'][$r->Arrows])) {
			$Results[$r->QuSession]['Arrows'][$r->Arrows]=0;
		}
		$Results[$r->QuSession]['Arrows'][$r->Arrows]+=$r->Total;
	}
}

$Totals=array();
// create new PDF document
$pdf = new ResultPDF('Control Page', false);//TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetFont('', 'B', 18);
$pdf->Cell(0, 0, 'Competition Arrow Status', 0, 1, 'C');
if($SubTitle) {
	$pdf->ln(4);
	$pdf->SetFont('', 'B', 14);
	$pdf->Cell(0, 0, $SubTitle, 0, 1, 'C');
}

$wCell=min(11, ($pdf->getPageWidth()-80)/(count($Arrows)+count($IrmTypes)+3));
$hCell=7;
$FontSize=9;

// HEADER
$pdf->dy(10);
$pdf->SetFont('', 'B', 10);
$pdf->Cell(60, $hCell+2,'', 1, 0, 'L', 1);
$pdf->Cell($wCell,$hCell+2, 'Entries',1,0,'C',1);
$Totals['g']=0;
$Totals['c']=0;
foreach($Arrows as $Num => $dummy) {
	$pdf->Cell($wCell,$hCell+2,$Num,1,0,'C',1);
	$Totals['a'.$Num]=0;
}
foreach($IrmTypes as $Num => $dummy) {
	$pdf->Cell($wCell,$hCell+2,$dummy,1,0,'C',1);
	$Totals['i'.$Num]=0;
}
$pdf->Cell($wCell,$hCell+2, 'Total',1,0,'C',1);
$pdf->Ln();

foreach($Results as $Id => $Session) {
	$pdf->SetFont('', 'B', $FontSize);
	$pdf->Cell(10, $hCell, $Id, 'TBL',0,'C');
	$pdf->Cell(50, $hCell, $Session['Name'], 'TBR');
	$pdf->SetFont('', '', $FontSize);

	$pdf->Cell($wCell,$hCell, number_format($Session['Total'], 0,'.','.'),1,0,'R');
	$Totals['g']+=$Session['Total'];
	foreach($Arrows as $Num => $dummy) {
		if(isset($Session['Arrows'][$Num])) {
			$pdf->Cell($wCell,$hCell,number_format($Session['Arrows'][$Num], 0,'.','.'),1,0,'R');
			$Totals['a'.$Num]+=$Session['Arrows'][$Num];
		} else {
			$pdf->Cell($wCell,$hCell,'',1,0,'R');
		}
	}
	foreach($IrmTypes as $Num => $dummy) {
		if(isset($Session['IrmTypes'][$Num])) {
			$pdf->Cell($wCell,$hCell,number_format($Session['IrmTypes'][$Num], 0,'.','.'),1,0,'R');
			$Totals['i'.$Num]+=$Session['IrmTypes'][$Num];
		} else {
			$pdf->Cell($wCell,$hCell,'',1,0,'R');
		}
	}

	$Tot=array_sum($Session['Arrows'])+array_sum($Session['IrmTypes']);
	$Totals['c']+=$Session['Total'];
	$pdf->SetFont('', 'B', $FontSize);
	$pdf->Cell($wCell,$hCell,number_format($Tot, 0,'.','.'),1,0,'R');
	if($Tot!=$Session['Total']) {
		$pdf->Cell($wCell,$hCell,'*',1,0,'R');
	}
	$pdf->Ln();
	$pdf->SetFont('', '', $FontSize);
}

$pdf->SetFont('', 'B', $FontSize);
$pdf->Cell(60, $hCell+2,'', 0, 0, 'L');
$pdf->Cell($wCell,$hCell+2, number_format($Totals['g'], 0,'.','.'),1,0,'R');
foreach($Arrows as $Num => $dummy) {
	$pdf->Cell($wCell,$hCell+2,number_format($Totals['a'.$Num], 0,'.','.'),1,0,'R');
}
foreach($IrmTypes as $Num => $dummy) {
	$pdf->Cell($wCell,$hCell+2,number_format($Totals['i'.$Num], 0,'.','.'),1,0,'R');
}
$pdf->Cell($wCell,$hCell+2, number_format($Totals['c'], 0,'.','.'),1,0,'R');

// -------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('ControlPage.pdf', 'I');

