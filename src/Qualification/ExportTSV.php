<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');

CheckTourSession(true);
checkACL(AclQualification, AclReadOnly);

$Sql = "SELECT ToCode, ToNumDist, ToGolds, ToXNine FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$q = safe_r_sql($Sql);
$tourData = null;
if (safe_num_rows($q)==1) {
    $tourData=safe_fetch($q);
} else {
    exit;
}

$DistQuery='';
for ($i=1;$i<=$tourData->ToNumDist;++$i) {
	$DistQuery .= "QuD{$i}Score as D{$i}Score, QuD{$i}Gold as D{$i}Golds, QuD{$i}Xnine as D{$i}Xnine, QuD{$i}Arrowstring, ";
}

$Sql = "SELECT EnCode as Bib, ifnull(bib.EdExtra,'') as LocalBib, EnFirstName AS FamilyName, EnName AS GivenName, EnDob as DoB, EnSex as Gender, QuSession AS Session, CONCAT(QuTarget,QuLetter) AS TargetNo,
	CoCode AS NOC, CoName AS Country, EnDivision AS Division, EnClass as Class, EnSubClass as SubClass,  
	$DistQuery
   	QuScore,QuHits,QuGold,QuXnine,
	if(QuClRank=0,'',QuClRank) as QuClRank, IndEvent, if(IndRank=0,'',IndRank) as IndRank, if(IndRankFinal=0,'',IndRankFinal) as IndRankFinal, ifnull(mail.EdEmail,'') as Email
	FROM Qualifications
	INNER JOIN Entries ON QuId=EnId
	INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
	LEFT JOIN Individuals on IndId=EnId and IndTournament=EnTournament
	left join ExtraData bib on bib.EdId=EnId and bib.EdType='Z'
	left join ExtraData mail on mail.EdId=EnId and mail.EdType='E'
	WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1
	ORDER BY QuTargetNo, CoCode, EnName ";
$q=safe_r_sql($Sql);
	//echo $MyQuery;exit;

$header = array('WaID','Local Bib','FamilyName','GivenName','DoB','Gender','Session','Target','Noc','Country', 'Division', 'Class', 'SubClass');
for ($i=1;$i<=$tourData->ToNumDist;++$i) {
    $header =array_merge($header, array("D{$i} Score","D{$i} ".$tourData->ToGolds,"D{$i} ". $tourData->ToXNine, "D{$i} Arrows"));
}
$header =array_merge($header, array("Score", "Hits", $tourData->ToGolds, $tourData->ToXNine, 'Category Rank','Event', 'Qual Rank','Final Rank','Email'));
$data = array();
$data[] = $header;
while ($r=safe_fetch_assoc($q)) {
    for ($i=1;$i<=$tourData->ToNumDist;++$i) {
        $r["QuD{$i}Arrowstring"] = implode(',', DecodeFromString(rtrim($r["QuD{$i}Arrowstring"]), false, true));
    }
    $data[] = $r;
}
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-Disposition: attachment; filename=' . $tourData->ToCode . '.txt');
header('Content-type: text/tab-separated-values; charset=' . PageEncode);
foreach ($data as $row) {
    echo implode(';',$row) . "\r\n";
}
?>
