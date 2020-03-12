<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

CheckTourSession(true);
$Sql = "SELECT ToNumDist, ToCategory FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$q = safe_r_SQL($Sql);
$TourRow = safe_fetch($q);

if (!empty($_REQUEST["Session"]) AND !empty($_REQUEST["Distance"]) AND $ses = intval($_REQUEST["Session"]) AND $dist = intval($_REQUEST["Distance"])) {
    $Sql = "SELECT EnCode, QuTarget, QuLetter, CoCode, EnSex, EnClass, EnDivision, 
        QuD{$dist}Arrowstring as ArrowString, QuD{$dist}Score as Score, QuD{$dist}Gold as Gold, QuD{$dist}Xnine as Xnine 
        FROM Qualifications 
        INNER JOIN Entries ON QuId=EnId 
        INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
        WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND QuSession={$ses} 
        ORDER BY QuTargetNo";
    $q = safe_r_SQL($Sql);
    $fileData = 'Athlete,Target,Gender,Division,Class,Country,Distance,Score,X,Tens,Nines,A1,A2,A3,A4,A5,A6,A7,A8,A9,A10,A11,A12,A13,A14,A15,A16,A17,A18,A19,A20,A21,A22,A23,A24,A25,A26,A27,A28,A29,A30' . ($TourRow->ToCategory == 1 ? ",A31,A32,A33,A34,A35,A36\n" : "\n");
    while ($r = safe_fetch($q)) {
        $fileData .= $r->EnCode . ',' . $r->QuTarget . $r->QuLetter . ',' . ($r->EnSex ? 'F' : 'M') . ',' . $r->EnDivision . ',' . $r->EnClass . ',' . $r->CoCode . ',';
        $fileData .= $dist . ',' . $r->Score . ',' . ($TourRow->ToCategory == 1 ? $r->Xnine : 0) . ',' . $r->Gold . ',' . ($TourRow->ToCategory == 2 ? $r->Xnine : 0) . ',';
        $fileData .= implode(',', str_replace(' ', '', DecodeFromString(str_pad($r->ArrowString, ($TourRow->ToCategory == 1 ? 36 : 30), ' ')))) . "\n";

    }

    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Content-Disposition: attachment; filename=' . $_SESSION["TourCode"] . '_' . $ses . '_' . $dist . '.csv');
    header('Content-type: text/csv; charset=' . PageEncode);
    echo $fileData;
    die();

}

$PAGE_TITLE = get_text('MenuLM_ExportScoreUSCAA');
include('Common/Templates/head.php');

echo '<form>';
echo '<table class="Tabella">';
echo '<tr><th colspan="3" class="Title">' . get_text('MenuLM_ExportScoreUSCAA') . '</th></tr>';
$sessions = GetSessions('Q');
foreach ($sessions as $s) {
    echo '<tr><th width="5%">' . $s->SesOrder . '</th><td class="Center" width="5%"><input type="radio" name="Session" value="' . $s->SesOrder . '"></td><td>' . $s->Descr . '</td></tr>';
}
echo '<tr class="divider"><td colspan="3"></td></tr>';
echo '<tr><th colspan="2">' . get_text('Distance', 'Tournament') . '</th><td>';
for ($i = 1; $i <= $TourRow->ToNumDist; $i++) {
    echo '<input name="Distance" type="radio" value="' . $i . '" value="' . $i . '" >&nbsp;' . $i . '<br>';
}
echo '</td></tr>';
echo '<tr class="divider"><td colspan="3"></td></tr>';
echo '<tr><th colspan="3"><input type="submit" value="' . get_text('CmdExport', 'Tournament') . '"></th><td>';

echo '</table>';
echo '</form>';

include('Common/Templates/tail.php');