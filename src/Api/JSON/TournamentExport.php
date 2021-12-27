<?php
require_once(dirname(__FILE__) . '/config.php');

$ToCode = $_GET['ToCode'];
$ToId ='';
$JsonResponse = array();
$sql = "SELECT
	ToId              ,
	ToOnlineId        ,
	ToType            ,
	ToCode            ,
	ToIocCode         ,
	ToTimeZone        ,
	ToName            ,
	ToNameShort       ,
	ToCommitee        ,
	ToComDescr        ,
	ToWhere           ,
	ToVenue           ,
	ToCountry         ,
	ToWhenFrom        ,
	ToWhenTo          ,
	ToIntEvent        ,
	ToCurrency        ,
	ToPrintLang       ,
	ToPrintChars      ,
	ToPrintPaper      ,
	ToImpFin          ,
	ToImgL            ,
	ToImgR            ,
	ToImgB            ,
	ToImgB2           ,
	ToNumSession      ,
	ToIndFinVxA       ,
	ToTeamFinVxA      ,
	ToDbVersion       ,
	ToBlock           ,
	ToUseHHT          ,
	ToLocRule         ,
	ToTypeName        ,
	ToTypeSubRule     ,
	ToNumDist         ,
	ToNumEnds         ,
	ToMaxDistScore    ,
	ToMaxFinIndScore  ,
	ToMaxFinTeamScore ,
	ToCategory        ,
	ToElabTeam        ,
	ToElimination     ,
	ToGolds           ,
	ToXNine           ,
	ToGoldsChars      ,
	ToXNineChars      ,
	ToDouble          ,
	ToCollation       ,
	ToIsORIS          ,
	ToOptions         ,
	ToRecCode         ,
	CURDATE() as Today
FROM Tournament
WHERE ToCode='" . $ToCode ."'";
$rs = safe_r_sql($sql);
while($row = safe_fetch($rs)) {
	$JsonResponse[] = array(
		"tournament" => $row,
		"unserialized" => unserialize($row->ToOptions)
	);
}
$howmany = count($JsonResponse);

if ($howmany < 1)
	SendResult("Not found");

if ($howmany == 1)
	$ToId = $JsonResponse[0]["tournament"]->ToId;
	$sql = "SELECT * from Classes WHERE ClTournament=".$ToId.";";
	$rs = safe_r_sql($sql);
	while($row = safe_fetch($rs)) {
		$JsonResponse[0]["classes"][] = $row;
	}
	$sql = "SELECT * from Divisions WHERE DivTournament=".$ToId.";";
	$rs = safe_r_sql($sql);
	while($row = safe_fetch($rs)) {
		$JsonResponse[0]["divisions"][] = $row;
	}
	SendResult($JsonResponse[0]);

if ($howmany > 1)
	SendResult("Too many coincidences found! That is weird, check with the I@anseon admin.");
