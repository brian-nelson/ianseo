<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');

checkACL(AclTeams, AclReadOnly);

class TMDeclarationPDF extends OrisPDF {
    function Footer() {
        $this->SetFont($this->FontStd,'',9);
        $this->setY($this->h - (15 + $this->extraBottomMargin) - 8);
        $this->cell(40, 4,  get_text('TeamManagerSignature','Tournament'), 0, 0, 'R');
        $this->cell(60, 4, '', 'B', 0);
        $this->cell(10, 4, '', 0, 0, 'C');
        $this->cell(25, 4, get_text('DateTimeSignature','Tournament'), 0, 0, 'R');
        $this->cell($this->w-(2*$this::leftMargin)-135, 4, '', 'B', 1);
        $this->SetFont($this->FontStd,'',10);
        parent::Footer();
    }
}

$pdf = new TMDeclarationPDF('C48', get_text('TeamComponentForm', 'Tournament'));

$pdf->setEvent(get_text('TeamComponentForm', 'Tournament'));

$EvCode = (!empty($_REQUEST["EvCode"]) ? filter_var($_REQUEST["EvCode"], FILTER_SANITIZE_STRING) : '' );
$CoId = (!empty($_REQUEST["CoId"]) ? intval($_REQUEST["CoId"]) : 0 );

$Sql = "SELECT DISTINCT EvCode, TeCoId, TeSubTeam " .
    "FROM Entries " .
    "INNER JOIN EventClass ON  EcTeamEvent!=0 AND EcTournament=EnTournament AND EcClass=EnClass AND EcDivision=EnDivision " .
    "INNER JOIN Events ON  EvCode=EcCode and EvTeamEvent=1 and EvTournament=EnTournament " .
    "INNER JOIN Teams ON EvTournament=TeTournament AND TeEvent=EvCode AND TeFinEvent=1 AND TeSO!=0 " .
    "WHERE EnTournament={$_SESSION['TourId']} AND EnAthlete=1 AND EnStatus<=1 " .
    "AND IF(EvTeamCreationMode=3, EnCountry3, if(EvTeamCreationMode=2, EnCountry2, if((EvTeamCreationMode=0 and EnCountry2=0) or EvTeamCreationMode=1, EnCountry, EnCountry2)))=TeCoId " .
    "AND IF(EvMixedTeam=0, EnTeamFEvent, EnTeamMixEvent) = 1 ";
if(!empty($CoId)) {
    $Sql .= "AND TeCoId='{$CoId}' ";
}
if(!empty($EvCode)) {
    $Sql .= "AND TeEvent='{$EvCode}' ";
}
$Sql .= "GROUP BY EvCode, TeCoId, TeSubTeam, EcTeamEvent, EcNumber HAVING EcNumber<COUNT(EnId)";

$optsAvailable=array();
$q=safe_r_SQL($Sql);
while($r = safe_fetch($q)) {
    if(!array_key_exists($r->EvCode,$optsAvailable)) {
        $optsAvailable[$r->EvCode] = array();
    }
    $optsAvailable[$r->EvCode][] = $r->TeCoId . '|' . $r->TeSubTeam;
}

$Sql = "SELECT EnId, EnDivision, EnClass, EnCode, EnSubClass, CONCAT(UPPER(EnFirstName), ' ', EnName) as Ath, QuScore, EvCode, EvEventName, TeCoId, TeSubTeam, CoCode, IF(CoNameComplete='',CoName,CoNameComplete) as Name, EcTeamEvent, EcNumber, EcSubClass, TfcId, TcId " .
    "FROM Entries " .
    "INNER JOIN Qualifications ON QuId=EnId " .
    "INNER JOIN EventClass ON  EcTeamEvent!=0 AND EcTournament=EnTournament AND EcClass=EnClass AND EcDivision=EnDivision " .
    "INNER JOIN Events ON  EvCode=EcCode and EvTeamEvent=1 and EvTournament=EnTournament " .
    "INNER JOIN Teams ON EvTournament=TeTournament AND TeEvent=EvCode AND TeFinEvent=1 AND TeSO!=0 " .
    "INNER JOIN Countries on CoId=TeCoId AND CoTournament=TeTournament " .
    "LEFT JOIN TeamFinComponent ON TfcCoId=TeCoId AND TfcSubTeam=TeSubTeam AND TfcTournament=EvTournament AND TfcEvent=EvCode AND TfcId=EnId " .
    "LEFT JOIN TeamComponent ON TcCoId=TeCoId  AND TcSubTeam=TeSubTeam AND TcTournament=EvTournament AND TcEvent=EvCode AND TcFinEvent=1 AND TcId=EnId " .
    "WHERE EnTournament={$_SESSION['TourId']} AND EnAthlete=1 AND EnStatus<=1 " .
    "AND IF(EvTeamCreationMode=3, EnCountry3, if(EvTeamCreationMode=2, EnCountry2, if((EvTeamCreationMode=0 and EnCountry2=0) or EvTeamCreationMode=1, EnCountry, EnCountry2)))=TeCoId " .
    "AND IF(EvMixedTeam=0, EnTeamFEvent, EnTeamMixEvent) = 1 ";
if(!empty($CoId)) {
    $Sql .= "AND TeCoId='{$CoId}' ";
}
if(!empty($EvCode)) {
    $Sql .= "AND TeEvent='{$EvCode}' ";
}
$Sql .= "ORDER BY CoCode, TeSubTeam, EvProgr, EvCode, EcTeamEvent, TfcId IS NOT NULL DESC, TcId IS NOT NULL DESC, Ath";

$q=safe_r_SQL($Sql);
$oldCoId = 0;
$oldSubCoId = 0;
$oldEvent = '';
$oldGroup = 0;
$yTop=-1;
$yBottom=-1;
$isOptions=false;

while($r=safe_fetch($q)){
    if(array_key_exists($r->EvCode, $optsAvailable) AND in_array($r->TeCoId . '|' . $r->TeSubTeam,$optsAvailable[$r->EvCode])) {
        if ($oldCoId != $r->TeCoId or $oldSubCoId != $r->TeSubTeam) {
            $pdf->setOrisCode('', $r->CoCode . ' - ' . $r->Name . ($r->TeSubTeam <= 1 ? '' : ' (' . $r->TeSubTeam . ')'));
            $pdf->setPhase($r->Name . ($r->TeSubTeam <= 1 ? '' : ' (' . $r->TeSubTeam . ')'));
            $pdf->addPage();
            $oldCoId = $r->TeCoId;
            $oldSubCoId = $r->TeSubTeam;
            $oldEvent = '';
            $oldGroup = 0;
            $pdf->setY($pdf->lastY - 5);
            $pdf->SetTopMargin($pdf->getY());
            $yBottom = $pdf->getY();
        }

        if ($oldEvent != $r->EvCode) {
            if (!$pdf->samePage(5 + $r->EcNumber, 5)) {
                $yBottom = $pdf->getY();
                $oldEvent = '';
            }
            $pdf->setY($yBottom + ($oldEvent != '' ? 4 : 0));
            $pdf->SetFont($pdf->FontStd, 'B', 12);
            $pdf->Cell(0, 7, $r->EvCode . ' - ' . $r->EvEventName, 'TLR', 1, 'L', 0);
            $pdf->SetDrawColor(0xE0, 0xE0, 0xE0);
            $pdf->Line(10, $pdf->getY(), $pdf->getPageWidth() - 10, $pdf->getY());
            $pdf->SetDefaultColor();
            $pdf->SetFont($pdf->FontStd, 'I', 8);
            $pdf->Cell(90, 4, 'Team Components', 'BL', 0, 'L', 0);
            $pdf->Cell(10, 4, '', 'B', 0, 'L', 0);
            $pdf->Cell(0, 4, 'Eligible Options', 'BR', 1, 'L', 0);
            $pdf->SetFont($pdf->FontStd, '', 10);
            $pdf->ln(2);
            $oldEvent = $r->EvCode;
            $oldGroup = 0;
            $yBottom = $pdf->getY();
        }

        if ($oldGroup != $r->EcTeamEvent) {
            $pdf->SetLineWidth(0.1);
            if ($oldGroup != 0) {
                $pdf->SetDrawColor(0xC0, 0xC0, 0xC0);
                $pdf->Line(10, $pdf->getY() + 1.5, $pdf->getPageWidth() - 10, $pdf->getY() + 1.5);
                $pdf->SetDefaultColor();
                $pdf->setY($pdf->getY() + 3);
            }

            $yTop = $pdf->getY();
            $yBottom = $yTop;
            $isOptions = false;
            $oldGroup = $r->EcTeamEvent;
        }

        if (empty($r->EcSubClass) or $r->EcSubClass == $r->EnSubClass) {
            if (is_null($r->TfcId)) {
                $pdf->setXY($pdf->getX() + 100, ($isOptions ? $pdf->gety() : $yTop));
                $isOptions = true;
            }
            $pdf->SetFont($pdf->FontFix, '', 8);
            $pdf->SetDrawColor(0xC0, 0xC0, 0xC0);
            $pdf->Cell(5.5, 5, '', 0, 0, '', 0);
            $pdf->Cell(4.5, 4.5, '', 1, 0, '', 0);
            $pdf->SetDefaultColor();
            $pdf->SetFont($pdf->FontStd, '', 10);
            $pdf->Cell(15, 5, $r->EnCode, 0, 0, 'R', 0);
            $pdf->Cell(55, 5, $r->Ath . (!is_null($r->TcId) ? ' (Q)' : ' '), 0, 0, 'L', 0);
            $pdf->Cell(10, 5, number_format($r->QuScore,0,'','.'), 0, 1, 'R', 0);
            $yBottom = max($yBottom, $pdf->getY());
        }
    }
}


$pdf->Output();

