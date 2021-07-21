<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
checkACL(AclCompetition, AclReadOnly);

define("CellH",10);

CheckTourSession(true);

$PrintNames=isset($_REQUEST['teamcomponents']);

$pdf = new OrisPDF('C58', 'DETAILED COMPETITION SCHEDULE');

$pdf->SetTopMargin(OrisPDF::topStart);



$Sql = "SELECT SesName, SesDtStart, SesDtEnd FROM Session WHERE SesTournament=".$_SESSION['TourId'] . " AND SesType != 'Q' ";
if(!empty($_REQUEST['FromDayDay'])) {
    if(strtolower(substr($_REQUEST['FromDayDay'], 0, 1))=='d') {
        $Date=date('Y-m-d', strtotime(sprintf('%+d days', substr($_REQUEST['FromDayDay'], 1) -1), $_SESSION['ToWhenFromUTS']));
    } else {
        $Date=CleanDate($_REQUEST['FromDayDay']);
    }
    if($Date) {
        $Sql .= " AND SesDtStart >= '{$Date} 00:00:00' AND SesDtStart <= '{$Date} 23:59:59'";
    }
}
$Sql .= " ORDER BY SesDtStart, SesDtEnd";
$q=safe_r_SQL($Sql);
$Sessions = array();
$whereCond=array();
$cnt=1;
while($r=safe_fetch($q)) {
	$Sessions[] = array("Name"=>$r->SesName, "Start"=>$r->SesDtStart, "End"=>$r->SesDtEnd);
	$whereCond[$cnt++] = "(CONCAT(FsScheduledDate, ' ', FsScheduledTime) >= '{$r->SesDtStart}' AND CONCAT(FsScheduledDate, ' ', FsScheduledTime) < '{$r->SesDtEnd}')";
}

$Sql = "SELECT CONCAT(FsEvent, '|', FsTeamEvent, '|', FsMatchNo) as SesKey, ";
if($whereCond) {
	$tmp=array();
	foreach($whereCond as $kWhere=>$vWhere) {
		$tmp[] = "IF({$vWhere},$kWhere,0)";
	}
	$Sql.='('.implode('+',$tmp).')';
} else {
	$Sql.=' 0 ';
}
$Sql .= " as SesNumber
	FROM FinSchedule
	WHERE FsTournament=".$_SESSION['TourId'] ." AND (FsMatchNo%2=0)".(empty($_REQUEST['OnlyMedals']) ? '' : ' and FsMatchno in (0,2) ').($whereCond ? " AND (" . implode(' OR ', $whereCond). ")" : '')."
	ORDER BY FsScheduledDate, FsScheduledTime, FsOdfMatchName";

$q=safe_r_SQL($Sql);
$SessionMatches = array();
while($r=safe_fetch($q)) {
	$SessionMatches[$r->SesNumber][] = $r->SesKey;
}
$lastSes=0;
$evInSession=0;
$runningDay='';
$sesInDay=0;
$sesCnt=-1;
$pdf->SetFont('','');
$FirstPage=true;
foreach($SessionMatches as $vSes => $items) {
	$NumItems=count($items);
	foreach($items as $i => $kSes) {
		list($eventCode,$isTeam,$matchNo) = explode('|',$kSes);
		$opts=array('matchno'=>$matchNo, 'events'=>$eventCode);
		$rank=Obj_RankFactory::create(($isTeam ? 'GridTeam':'GridInd'), $opts);
		$rank->read();
		$rankData=$rank->getData();

		$ChangePage=false;
		$Continue='';

		$item=$rankData["sections"][$eventCode]["phases"][key($rankData["sections"][$eventCode]["phases"])]["items"][0];
		if($item['tie']!=2 AND $item['oppTie']!=2) {

            $ExtraLineHeight = 0;
            $AthlBorder = 1;
            if ($isTeam and $PrintNames) {
                $ExtraLineHeight = 3 * $rankData["sections"][$eventCode]['meta']['maxTeamPerson'];
                $AthlBorder = 'LTR';
            }

            if (!$i) {
                if (!$pdf->samePage(3, CellH, '', false)
                    or (!$pdf->samePage($NumItems, CellH, '', false))) {
                    // first item in a block... needs at least 3 rows to print the sessions data
                    // not able to split in 3+3
                    $ChangePage = true;
                    if ($runningDay == $item["scheduledDate"]) $Continue = ' (Cont.)';
                }
            } elseif (($NumItems - $i == 4 and !$pdf->samePage(3, CellH, '', false))
                or !$pdf->samePage(($isTeam and $PrintNames) ? $rankData["sections"][$eventCode]['meta']['maxTeamPerson'] : 1, CellH, '', false)) {
                // needs to have room for printing the last 3 rows
                $ChangePage = true;
                $Continue = ' (Cont.)';
            }

            if ($runningDay != $item["scheduledDate"]
                or $ChangePage) {
                // close the cell...
                if (!$FirstPage) $pdf->Line(OrisPDF::leftMargin, $y1 = $pdf->GetY(), OrisPDF::leftMargin + 25, $y1);

                $pdf->AddPage();

                $pdf->SetXY(OrisPDF::leftMargin, OrisPDF::topStart);
                $pdf->SetFont('', 'B');
                $pdf->Cell(25, CellH, "Date/Session", 1, 0, 'L', 0);
                $pdf->Cell(7, CellH, "Match", 1, 0, 'C', 0);
                $pdf->Cell(9, CellH / 2, "Start", 'TLR', 0, 'C', 0);
                $pdf->SetXY($pdf->GetX() - 9, $pdf->GetY() + CellH / 2);
                $pdf->Cell(9, CellH / 2, "Time", 'BLR', 0, 'C', 0);
                $pdf->SetXY($pdf->GetX(), $pdf->GetY() - CellH / 2);
                $pdf->Cell(30, CellH, "Event", 1, 0, 'L', 0);
                $pdf->Cell(9, CellH, "Round", 1, 0, 'L', 0);

                $pdf->Cell(10, CellH / 2, "R.R.", 'TLR', 0, 'C', 0);
                $pdf->SetXY($pdf->GetX() - 10, $pdf->GetY() + CellH / 2);
                $pdf->Cell(10, CellH / 2, "Rank", 'BLR', 0, 'C', 0);
                $pdf->SetXY($pdf->GetX(), $pdf->GetY() - CellH / 2);
                $pdf->Cell(45, CellH, "Participant 1", 1, 0, 'L', 0);

                $pdf->Cell(10, CellH / 2, "R.R.", 'TLR', 0, 'C', 0);
                $pdf->SetXY($pdf->GetX() - 10, $pdf->GetY() + CellH / 2);
                $pdf->Cell(10, CellH / 2, "Rank", 'BLR', 0, 'C', 0);
                $pdf->SetXY($pdf->GetX(), $pdf->GetY() - CellH / 2);
                $pdf->Cell(45, CellH, "Participant 2", 1, 1, 'L', 0);
                $pdf->SetFont('', '');
                if ($runningDay != $item["scheduledDate"]) {
                    $sesInDay = 0;
                } else {
                    $evInSession = -1;
                }

                $runningDay = $item["scheduledDate"];
            }
            $FirstPage = false;
            if ($lastSes != $vSes) {
                $evInSession = 0;
                $sesInDay++;
                $sesCnt++;
                $pdf->Line(OrisPDF::leftMargin, $y1 = $pdf->GetY(), OrisPDF::leftMargin + 25, $y1);
                $pdf->dy(1);
            } else {
                $evInSession++;
            }

            $OrgY = $pdf->getY();

            $SessionText = '<b>'.(new DateTime($runningDay))->format('D j M') .'</b>' . $Continue . "<br>".
                "<b>Session " . $sesInDay . "</b><br>".
                (!empty($Sessions[$sesCnt]) ? $Sessions[$sesCnt]['Name'] : '');
            if($evInSession == 0) {
                $pdf->MultiCell(25, CellH + $ExtraLineHeight, $SessionText, 'TLR', 'L', 0, 0, '', '', true, 0, true, true, 0);
            } else {
                $pdf->Cell(25, CellH + $ExtraLineHeight,'','LR' . ($evInSession == 0 ? 'T' : ''), 0, 'L', 0);
            }
            $pdf->Cell(7, CellH + $ExtraLineHeight, $item['odfMatchName'], 1, 0, 'C', 0);
            $pdf->Cell(9, CellH + $ExtraLineHeight, (new DateTime($item["scheduledTime"]))->format('H:i'), 1, 0, 'C', 0);
            $pdf->Cell(30, CellH + $ExtraLineHeight, $rankData["sections"][$eventCode]["meta"]["eventName"], 1, 0, 'L', 0);
            $pdf->Cell(9, CellH + $ExtraLineHeight, $rankData["sections"][$eventCode]["phases"][key($rankData["sections"][$eventCode]["phases"])]["meta"]["phaseName"], 1, 0, 'L', 0);

            $Name = (empty($item['odfPath']) or $item[$isTeam ? "countryName" : "athlete"]) ? $item[$isTeam ? "countryName" : "athlete"] : $item['odfPath'];
            $pdf->Cell(10, CellH + $ExtraLineHeight, $item["qualRank"], 1, 0, 'R', 0);
            $pdf->Cell(37, CellH, $Name, $AthlBorder, 0, 'L', 0);
            $pdf->Cell(8, CellH + $ExtraLineHeight, $item["countryCode"], 1, 0, 'L', 0);

            $Name = (empty($item['oppOdfPath']) or $item[$isTeam ? "oppCountryName" : "oppAthlete"]) ? $item[$isTeam ? "oppCountryName" : "oppAthlete"] : $item['oppOdfPath'];
            $pdf->Cell(10, CellH + $ExtraLineHeight, $item["oppQualRank"], 1, 0, 'R', 0);
            $pdf->Cell(37, CellH, $Name, $AthlBorder, 0, 'L', 0);
            $pdf->Cell(8, CellH + $ExtraLineHeight, $item["oppCountryCode"], 1, 1, 'L', 0);

            if ($isTeam and $PrintNames) {
                $OrgX = $pdf->getX() + 93;
                $Font = $pdf->getFontSizePt();
                $pdf->SetFontSize(8);
                if (!empty($rankData["sections"][$eventCode]['athletes'][$item['teamId']][0])) {
                    foreach ($rankData["sections"][$eventCode]['athletes'][$item['teamId']][0] as $k => $Component) {
                        $pdf->setxy($OrgX, 3 * $k + $OrgY + 8);
                        $pdf->Cell(34, 3, $Component['athlete'], '', 0, 'L', 0);
                    }
                }
                $pdf->Line($OrgX - 3, $OrgY + CellH + $ExtraLineHeight, $OrgX + 34, $OrgY + CellH + $ExtraLineHeight);
                $OrgX += 55;
                if (!empty($rankData["sections"][$eventCode]['athletes'][$item['oppTeamId']][0])) {
                    foreach ($rankData["sections"][$eventCode]['athletes'][$item['oppTeamId']][0] as $k => $Component) {
                        $pdf->setxy($OrgX, 3 * $k + $OrgY + 8);
                        $pdf->Cell(34, 3, $Component['athlete'], '', 0, 'L', 0);
                    }
                }
                $pdf->Line($OrgX - 3, $OrgY + CellH + $ExtraLineHeight, $OrgX + 34, $OrgY + CellH + $ExtraLineHeight);
                $pdf->SetY($OrgY + CellH + $ExtraLineHeight);
                $pdf->SetFontSize($Font);
            }
            $lastSes = $vSes;
        }
	}
}
$pdf->Cell(25, CellH, "", 'T', 0, 'L', 0);



$pdf->Output();