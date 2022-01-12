<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');

CheckTourSession(true);
checkACL(AclQualification, AclReadOnly);
$validWaDivs = array('R'=>'Recurve', 'C'=>'Compound', 'B'=>'Barebow',);

if(!empty($_REQUEST["updateDiv"])) {
    $JSON=array('error' => 1, 'div' => $_REQUEST["updateDiv"]);
    if(array_key_exists($_REQUEST["Value"],$validWaDivs)) {
        safe_w_SQL("UPDATE `Divisions` SET `DivWaDivision` = '".$_REQUEST["Value"]."' WHERE `DivId` = '".$_REQUEST["updateDiv"]."' AND `DivTournament` = ".$_SESSION['TourId']);
        $JSON['error']=0;
    } else {
        safe_w_SQL("UPDATE `Divisions` SET `DivWaDivision` = '' WHERE `DivId` = '".$_REQUEST["updateDiv"]."' AND `DivTournament` = ".$_SESSION['TourId']);
    }
    JsonOut($JSON);
} else if (empty($_REQUEST['DoExport'])) {
    $PAGE_TITLE=get_text('MenuLM_ExportIndoorWorldSeries', 'Common');
    $JS_SCRIPT = array(
        '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
        '<script type="text/javascript" src="./ExportIWS.js"></script>',
        '<link href="./ExportIWS.css" rel="stylesheet" type="text/css">',
    );
    include('Common/Templates/head.php');
    echo '<table class="Tabella">';
    echo '<tr><th class="Title" colspan="4">' . get_text('MenuLM_ExportIndoorWorldSeries') . '</th></tr>';
    echo '<tr><th>&nbsp;</th><th colspan="2">' . get_text('Division'). '</th><th>' . get_text('WADivision'). '</th></tr>';

    $q = safe_r_SQL("SELECT `DivId`,`DivDescription`,`DivWaDivision`,`DivAthlete` FROM `Divisions` WHERE `DivTournament`=".$_SESSION['TourId']." ORDER BY `DivAthlete` DESC, `DivViewOrder`,`DivId`");
    while($r=safe_fetch($q)) {
        echo '<tr>'.
            '<th class="smallContainer"><div id="div_'.$r->DivId.'" class="divStatus ' . ($r->DivAthlete=="1" ? (array_key_exists($r->DivWaDivision,$validWaDivs) ? 'isValid':'notValid') : '').'">&nbsp;</div></th>'.
            '<td class="divCodeContainer">'.$r->DivId.'</td>'.
            '<td class="divContainer">'.$r->DivDescription.'</td>'.
            '<td class="divContainer">';
        if($r->DivAthlete) {
            echo '<select id="sel_' . $r->DivId . '" onchange="saveWaDivision(\''.$r->DivId.'\')"><option value="">---</option>';
            foreach ($validWaDivs as $k => $v) {
                echo '<option value="' . $k . '" ' . ($r->DivWaDivision == $k ? 'selected="selected"' : '') . '>' . $k . ' - ' . $v . '</option>';
            }
            echo '</select>';
        } else {
            echo '&nbsp;';
        }
            echo '</td>'.
        '</tr>';
    }
    echo '<tr class="divider"><td colspan="4"></td></tr>';
    echo '<tr><td colspan="4" class="Center"><input class="exportButton" type="button" value="'.get_text('CmdExport', 'Tournament').'" onclick="window.open(\''.$_SERVER['PHP_SELF'].'?DoExport=1\',\'_blank\')"></td></tr>';
    echo '</table>';

    include('Common/Templates/tail.php');
} else {
    $DistQuery = '';

    $Sql = "SELECT EnCode as Bib, IFNULL(bib.EdExtra,'') as WaBib, EnFirstName AS FamilyName, EnName AS GivenName, EnDob as DoB, EnSex as Gender, CoCode AS NOC, CoMaCode as MaNoc, ToCountry as ToNoc, DivDescription, EnDivision, DivWaDivision, EnClass, QuScore, QuGold, QuXnine,
       TfT1, TfW1, TfT2, TfW2, TfT3, TfW3, TfT4, TfW4, TfT5, TfW5, TfT6, TfW6, TfT7, TfW7, TfT8, TfW8,
       TdDist1, TdDist2, TdDist3, TdDist4, TdDist5, TdDist6, TdDist7, TdDist8, IFNULL(NumArrows,0) as totArrows
	FROM Qualifications
	INNER JOIN Entries ON QuId=EnId
	INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
    INNER JOIN Divisions ON EnDivision=DivId and EnTournament=DivTournament
    INNER JOIN Tournament ON EnTournament=ToId
	LEFT JOIN ExtraData bib on bib.EdId=EnId and bib.EdType='Z'
    LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId 
    LEFT JOIN TournamentDistances ON TdType=ToType AND TdTournament=EnTournament AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses
    LEFT JOIN (SELECT DiSession, sum(DiEnds*DiArrows) as NumArrows FROM `DistanceInformation` where DiType='Q' and DiTournament=" . StrSafe_DB($_SESSION['TourId']) . " GROUP BY DiSession) as sqy ON QuSession=DiSession 
	WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1
	ORDER BY QuTargetNo, CoCode, EnName ";
    $q = safe_r_sql($Sql);
    //echo $MyQuery;exit;

    $header = array('National ID', 'World Archery ID', 'Family Name', 'Given Name', 'Date of Birth', 'Gender', 'NOC Code', 'Division', 'Score', '10s', '9s');
    $ClDivDetails = array();
    $DivDetails = array();
    $data = array();
    $invalidData = array();
    while ($r = safe_fetch($q)) {
        $isValid = true;
        $ClDivDetails[] = $r->EnDivision .':'. $r->EnClass . '|' . $r->totArrows . '|' .
            $r->TdDist1 . '-' . $r->TfT1 . '-' . $r->TfW1 . '|' . $r->TdDist2 . '-' . $r->TfT2 . '-' . $r->TfW2 . '|' . $r->TdDist3 . '-' . $r->TfT3 . '-' . $r->TfW3 . '|' . $r->TdDist4 . '-' . $r->TfT4 . '-' . $r->TfW4 . '|' .
            $r->TdDist5 . '-' . $r->TfT5 . '-' . $r->TfW5 . '|' . $r->TdDist6 . '-' . $r->TfT6 . '-' . $r->TfW6 . '|' . $r->TdDist7 . '-' . $r->TfT7 . '-' . $r->TfW7 . '|' . $r->TdDist8 . '-' . $r->TfT8 . '-' . $r->TfW8;
        $DivDetails[] = $r->EnDivision . '|' . $r->DivWaDivision . '|' . $r->DivDescription;

        for ($i = 1; $i <= 8; $i++) {
            $isValid = ($isValid and array_key_exists((empty($r->DivWaDivision) ? $r->EnDivision : $r->DivWaDivision),$validWaDivs) AND ($r->{"TdDist" . $i} == 0 or $r->{"TdDist" . $i} == 18) and ($r->{"TfW" . $i} == 0 or $r->{"TfW" . $i} == 40));
        }
        if (($r->totArrows == 0 or $r->totArrows == 60) and $isValid) {
            $data[] = array('"' . $r->Bib . '"', $r->WaBib,
                '"' . $r->FamilyName . '"', '"' . $r->GivenName . '"', $r->DoB, ($r->Gender ? 'W' : 'M'),
                '"' . (!empty($r->MaNoc) ? $r->MaNoc : (!empty($r->ToNoc) ? $r->ToNoc : $r->NOC)) . '"',
                '"' . (empty($r->DivWaDivision) ? $r->EnDivision : $r->DivWaDivision) . '"',
                $r->QuScore, $r->QuGold, $r->QuXnine);
        } else {
            $invalidData[] = array('"' . $r->Bib . '"', $r->WaBib,
                '"' . $r->FamilyName . '"', '"' . $r->GivenName . '"', $r->DoB, ($r->Gender ? 'W' : 'M'),
                '"' . $r->NOC . ':' . (!empty($r->MaNoc) ? $r->MaNoc : (!empty($r->ToNoc) ? $r->ToNoc : $r->NOC)) . '"',
                '"' . $r->EnDivision . ':' . $r->EnClass . '"',
                $r->QuScore, $r->QuGold, $r->QuXnine);
        }
    }
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Content-Disposition: attachment; filename=' . $_SESSION["TourCode"] . '.csv');
    header('Content-type: text/comma-separated-values; charset=' . PageEncode);
    echo '# ' . implode(';', $header) . "\n";
    foreach ($data as $row) {
        echo implode(';', $row) . "\n";
    }
    echo str_repeat('#', 40) . "\n";
    foreach ($invalidData as $row) {
        echo '#' . implode(';', $row) . "\n";
    }
    echo str_repeat('#', 40) . "\n";
    echo "# " . $_SESSION["TourCode"] . " - " . $_SESSION["TourName"] . "\n";
    echo "# " . $_SESSION["TourWhere"] . "\n";
    echo "# " . $_SESSION["TourRealWhenFrom"] . " - " . $_SESSION["TourRealWhenTo"] . "\n";
    echo str_repeat('#', 40) . "\n";
    $DivDetails = array_unique($DivDetails, SORT_REGULAR);
    sort($DivDetails, SORT_REGULAR);
    foreach ($DivDetails as $row) {
        echo '# ' . $row . "\n";
    }
    echo str_repeat('#', 40) . "\n";
    $ClDivDetails = array_unique($ClDivDetails, SORT_REGULAR);
    sort($ClDivDetails, SORT_REGULAR);
    foreach ($ClDivDetails as $row) {
        echo '# ' . $row . "\n";
    }
    echo str_repeat('#', 40) . "\n";
}