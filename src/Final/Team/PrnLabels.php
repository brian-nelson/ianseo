<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/pdf/LabelPDF.inc.php');
/*
 *
 * Page / Label Dimensions  	Report Builder Fields  	Avery 5160 (inches)
---Page Width 	Report – Page Width 	8.5
---Page Height 	Report – Page Height 	11
---Label Width 	* 	2.5935
---Label Height 	Detail – Height 	1
Top Margin 	Report – Top Margin 	0.5
Right Margin 	Report – Right Margin 	0.5
Bottom Margin 	Report – Bottom Margin 	0.1875
Left Margin 	Report – Left Margin 	0.1875
Columns 	Report – Column Count 	3
Horizontal Spacing 	Report – Column Spacing 	0.15625
 *
 */


$OpDetails = "Accreditation";
if(isset($_REQUEST["OperationType"]))
	$OpDetails = $_REQUEST["OperationType"];

if(CheckTourSession()) {
    checkACL(AclTeams, AclReadOnly);
	$pdf = new LabelPDF();
	//Predefinita per etichette A4
	$lblW= $pdf->GetPageWidth()/3;
	$lblH= $pdf->GetPageHeight()/8;
	$lblMarginH=2;
	$lblMarginV=2;
	$lblSpaceV=0;
	$lblSpaceH=0;
	$pageMarginT=0;
	$pageMarginL=0;
	$Label4Column=3;
	$Label4Page=24;
	$printBarcode=true;

	if(intval($pdf->GetPageWidth())==210 && intval($pdf->GetPageHeight())==297)	//Etichette A4
	{
		$lblMarginH=4;
		$lblMarginV=4;
	}
	else
	{
		$lblW= 2.5935*25.4;
		$lblH= 25.4;
		$lblMarginH=2;
		$lblMarginV=2;
		$lblSpaceH=0.15625*25.4;
		$lblSpaceV=0;
		$pageMarginT=0.5*25.4;
		$pageMarginL=0.1875*25.4;
		$Label4Page=30;
		$printBarcode=false;
	}

	$MyQuery = 'SELECT '
        . ' TeCoId, EvCode, EvEventName, EvMixedTeam, EvFinalFirstPhase, GrPosition, TeRank, TeScore QuScore, '
        . " CoCode, upper(CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),''))) AS FirstName, '' NationCode, '' Name, "
		. ' NULLIF(s8.FSTarget,\'\') s8, NULLIF(s4.FSTarget,\'\') s4, NULLIF(s2.FSTarget,\'\') s2, NULLIF(sb.FSTarget,\'\') sBr, NULLIF(sg.FSTarget,\'\') sGo '
        . ' FROM Events '
        . ' INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0 '
        . ' INNER JOIN TeamFinals ON EvCode=TfEvent AND EvTournament=TfTournament '
        . ' INNER JOIN Teams ON TeCoID=TfTeam AND TeEvent=TfEvent AND TeTournament=TfTournament '
        . ' INNER JOIN Grids ON TfMatchNo=GrMatchNo AND GrPhase=greatest(PhId, PhLevel) '
        . ' INNER JOIN Countries on TfTeam=CoId AND TfTournament=CoTournament '
        . ' LEFT JOIN FinSchedule s16 ON EvCode=s16.FSEvent AND EvTeamEvent=s16.FSTeamEvent AND EvTournament=s16.FSTournament AND IF(GrPhase=16,TfMatchNo,-256)=s16.FSMatchNo'
        . ' LEFT JOIN FinSchedule s8 ON EvCode=s8.FSEvent AND EvTeamEvent=s8.FSTeamEvent AND EvTournament=s8.FSTournament AND IF(GrPhase=8,TfMatchNo,FLOOR(s16.FSMatchNo/2))=s8.FSMatchNo'
		. ' LEFT JOIN FinSchedule s4 ON EvCode=s4.FSEvent AND EvTeamEvent=s4.FSTeamEvent AND EvTournament=s4.FSTournament AND IF(GrPhase=4,TfMatchNo,FLOOR(s8.FSMatchNo/2))=s4.FSMatchNo'
		. ' LEFT JOIN FinSchedule s2 ON EvCode=s2.FSEvent AND EvTeamEvent=s2.FSTeamEvent AND EvTournament=s2.FSTournament AND IF(GrPhase=2,TfMatchNo,FLOOR(s4.FSMatchNo/2))=s2.FSMatchNo'
		. ' LEFT JOIN FinSchedule sb ON EvCode=sb.FSEvent AND EvTeamEvent=sb.FSTeamEvent AND EvTournament=sb.FSTournament AND FLOOR(s2.FSMatchNo/2)=sb.FSMatchNo'
		. ' LEFT JOIN FinSchedule sg ON EvCode=sg.FSEvent AND EvTeamEvent=sg.FSTeamEvent AND EvTournament=sg.FSTournament AND FLOOR(s2.FSMatchNo/2)-2=sg.FSMatchNo'

		. ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=1 ';
		if (isset($_REQUEST['Event'])) {
			$events=array();
			if(!is_array($_REQUEST['Event'])) $_REQUEST['Event']= array($_REQUEST['Event']);

			foreach($_REQUEST['Event'] as $event) {
				if(preg_match("/^[0-9A-Z]{1,4}$/i",$event)) $events[]=strSafe_db($event);
			}

			if($events) {
				sort($events);
				$MyQuery.= "AND EvCode in (" . implode(',', $events) . ") ";
			}
		}
        $MyQuery .= ' ORDER BY EvCode, s8, s4, s2';

	$Rs=safe_r_sql($MyQuery);
	if($Rs)
	{
		$Etichetta=0;
		while($MyRow=safe_fetch($Rs)) {
			if($Etichetta==0)
				$pdf->AddPage();

			//Cerchia Etichetta
			//$pdf->Rect($pageMarginL+(($Etichetta % $Label4Column) * ($lblW+$lblSpaceH)),$pageMarginT+(intval($Etichetta / $Label4Column) * ($lblH+$lblSpaceV)),$lblW,$lblH,"D");

			$pdf->SetXY(0,0);
			$pdf->SetLeftMargin($pageMarginL+$lblMarginH+(($Etichetta % $Label4Column) * ($lblW+$lblSpaceH)));
			$pdf->SetTopMargin($pageMarginT+$lblMarginV+(intval($Etichetta / $Label4Column) * ($lblH+$lblSpaceV)));

			//Piazzola, Turno & Classe.Divisione
			$pdf->SetFont($pdf->FontStd,'B',20);
			$pdf->Cell(0.25*($lblW-2*($lblMarginH)),8,$MyRow->TeRank,0,0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->SetXY($pdf->GetX()-3,$pdf->GetY()+2);
			$pdf->Cell(0.45*($lblW-2*($lblMarginH)),7, get_text('Score','Tournament') . ': ' . $MyRow->QuScore,0,0,'C',0);
			$pdf->Cell(0.30*($lblW-2*($lblMarginH)),6,$MyRow->EvCode,0,1,'R',0);


			//Arciere & Società
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->Cell($lblW-2*($lblMarginH),6,$MyRow->FirstName . ' ' . $MyRow->Name,0,1,'L',0);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell(0.30*$lblW-2*($lblMarginH),5,$MyRow->NationCode,0,0,'L',0);
			// stampa l'elenco delle piazzole a cui dovrà andare...
			$tgts=array();
			$pos=array();
			$n=2;
			while($n<=$MyRow->EvFinalFirstPhase) {
				array_unshift($pos, $n);
				$n=$n*2;
			}
			foreach($pos as $n) {
			if($MyRow->{'s'.$n}) $tgts[]=$MyRow->{'s'.$n};
				else $tgts[]='bye';
			}
			$tgts[]=$MyRow->sBr.'/'.$MyRow->sGo;
			$pdf->SetFont($pdf->FontStd,'',8);
			$pdf->SetXY($pdf->GetX()-3,$pdf->GetY());
			$pdf->Cell($lblW-2*($lblMarginH),5, get_text('Targets','Tournament') . ': ' . implode('-', $tgts),0,1,'L',0);

			//Barcode
			if($printBarcode)
			{
				$pdf->SetFont('barcode','',28);
				$pdf->Cell($lblW-2*($lblMarginH),10, mb_convert_encoding('*$' . $MyRow->TeCoId,"UTF-8","cp1252") . "*",0,0,'C',0);
			}

/*
			//Status
			if($MyRow->Status>1)
				$pdf->Rect((($Etichetta % 3) * $lblW)+4,(intval($Etichetta / 3) * $lblH)+4,($lblW-8),($lblH-10*$lblSp),"FD");

			//Barcode
			$pdf->SetXY((($Etichetta % 3) * $lblW)+5,(intval($Etichetta / 3) * $lblH)+12*$lblSp);
			$pdf->SetFont('barcode','',28);
			$pdf->Cell($lblW-10,10, mb_convert_encoding('*$' . $MyRow->EnId,"UTF-8","cp1252") . "*",0,0,'C',0);

			//$pdf->Rect((($Etichetta % 3) * 70)+0,(intval($Etichetta / 3) * 37)+0,70,37);

*/
			$Etichetta = ++$Etichetta % $Label4Page;
		}
		safe_free_result($Rs);
	}
	$pdf->Output();
}
?>