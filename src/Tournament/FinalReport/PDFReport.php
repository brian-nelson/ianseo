<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/pdf/Report.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Various.inc.php');
require_once 'Tournament/Fun_Tournament.local.inc.php';
checkACL(AclCompetition, AclReadOnly);

if (!isset($_SESSION['TourId']) && isset($_REQUEST['TourId'])) {
	CreateTourSession($_REQUEST['TourId']);
}

$RowTournament = NULL;
$TournamentOptions = array();

$MySql = "SELECT"
	. " ToCode,"
	. " ToName,"
	. " ToCommitee,"
	. " ToComDescr,"
	. " ToWhere,"
	. " date_format(ToWhenFrom, '".get_text('DateFmtDB')."') AS DtFrom,"
	. " date_format(ToWhenTo, '".get_text('DateFmtDB')."') AS DtTo,"
	. " ToTypeName AS TtName, "
	. " ToLocRule, ToTypeName, ToTypeSubRule, ToOptions "
	. "FROM Tournament "
	. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$Rs=safe_r_sql($MySql);
if(safe_num_rows($Rs)==1) {
	$RowTournament = safe_fetch($Rs);
	if(!empty($RowTournament->ToOptions)) {
		$TournamentOptions = unserialize($RowTournament->ToOptions);
	}
	safe_free_result($Rs);
}

$TourRulesArray = array(
	'Divisions' => array('Div', 0, 0, get_text('Divisions', 'Tournament')),
	'Classes' => array('Cl', 0, 0, get_text('Classes', 'Tournament')),
	'TournamentDistances' => array('Td', 0, 0, get_text('MenuLM_Distances')),
	'DistanceInformation' => array('Di', 0, 0, get_text('ArrowsPerEnd', 'Tournament')),
	'Events' => array('Ev', 0, 0, get_text('MenuLM_Manage Events')),
	'EventClass' => array('Ec', 0, 0, get_text('EventClass')),
	'TargetFaces' => array('Tf', 0, 0 ,get_text('MenuLM_Targets')),
);
foreach($TourRulesArray as $k=>$v) {
	$q=safe_r_SQL("SELECT COUNT(*) as Quanti, 
		SUM(IF(".$v[0]."TourRules='". $RowTournament->ToLocRule . "|" . $RowTournament->ToTypeName . "|" . $RowTournament->ToTypeSubRule . "',1,0)) as Compliant
		FROM {$k} WHERE " . $v[0] . "Tournament=" . StrSafe_DB($_SESSION['TourId']) . " GROUP BY " . $v[0] . "Tournament");
	$r = safe_fetch($q);
	$TourRulesArray[$k][1] = intval($r->Quanti);
	$TourRulesArray[$k][2] = intval($r->Compliant);
}
$SyncLastUpdate=array();
$q=safe_r_sql("select LupIocCode, LupLastUpdate from LookUpPaths where LupIocCode in (select ToIocCode from Tournament where ToId={$_SESSION['TourId']} union select distinct EnIocCode from Entries where EnTournament={$_SESSION['TourId']})");
while($r=safe_fetch($q)) {
	$SyncLastUpdate[$r->LupIocCode]=$r->LupLastUpdate;
}
$Changes = getLUEChanges($_SESSION['TourId']);

$copy2 = array(
			get_text('ReportCopy1','Tournament'),
			get_text('ReportCopy2','Tournament')
			);

$pdf = new Report((get_text('FinalReportTitle','Tournament')));

list($StrData,$ToCode)=ExportASC(null,false);
$StrData = str_replace("\r","",$StrData);
$StrData = str_replace("\n","",$StrData);
$pdf -> setValidationCode(number_format(sprintf("%u",crc32($StrData)),0,'',get_text('NumberThousandsSeparator')));



for($i=0;$i<count($copy2);++$i)
{
	$pdf->setCopy2($copy2[$i]);

	//Intestazione
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(175, 7,  get_text('FinalReportTitle','Tournament'), 1, 1, 'C', 1);
	//Codice Gara & Tipo
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(40, 7,  get_text('TourCode','Tournament') . ": ", 'LT', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(50, 7,  $RowTournament->ToCode, 'T', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(30, 7,  get_text('TourType','Tournament') . ": ", 'T', 0, 'R', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(55, 7,  get_text($RowTournament->TtName, 'Tournament') .  ' (' . $RowTournament->ToLocRule . " - ". $RowTournament->ToTypeSubRule.')', 'TR', 1, 'L', 0);
	//Denominazione
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(40, 7,  get_text('TourName','Tournament') . ": ", 'L', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(135, 7,  $RowTournament->ToName, 'R', 1, 'L', 0);
	//Organizzazione
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(40, 7,  get_text('TourCommitee','Tournament') . ": ", 'L', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(135, 7,  $RowTournament->ToCommitee . " - " . $RowTournament->ToComDescr , 'R', 1, 'L', 0);
	//Luogo e data di Svolgimento
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(40, 7,  get_text('TourWhen','Tournament') . ": ", 'L', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(50, 7,  TournamentDate2String($RowTournament->DtFrom,$RowTournament->DtTo), 0, 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(30, 7,  get_text('TourWhere','Tournament') . ": ", 0, 0, 'R', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(55, 7,  $RowTournament->ToWhere, 'R', 1, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',1);
	$pdf->Cell(175, 0.5,  '', 1, 1, 'L', 1);
	//Report di caratteristiche IANSEO
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(40, 7,  get_text('IANSEOVersion','Tournament') . ": ", 'LT', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(135, 7,  ProgramName . ' ' . ProgramVersion . ' ' . ProgramRelease . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : ''), 'TR', 1, 'L', 0);
	if(!empty($TournamentOptions["TourRulesCount"])) {
		foreach ($TournamentOptions["TourRulesCount"] as $k => $v ){
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell(40, 7,  $TourRulesArray[$k][3] . ": ", 'L', 0, 'L', 0);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell(45, 7,  get_text('LocRuleValidNo','Tournament') . ": ", '', 0, 'R', 0);
			$pdf->SetFont($pdf->FontStd,($TourRulesArray[$k][2]!=$v ? 'B':''),10);
			$pdf->Cell(10, 7,  $TourRulesArray[$k][2]."/".$v, '', 0, 'R', 0);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell(40, 7,  get_text('LocRuleModifiedNo','Tournament') . ": ", '', 0, 'R', 0);
			$pdf->SetFont($pdf->FontStd,(($TourRulesArray[$k][1]-$TourRulesArray[$k][2])!=0 ? 'B':''),10);
			$pdf->Cell(5, 7,  ($TourRulesArray[$k][1]-$TourRulesArray[$k][2]), '', 0, 'R', 0);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell(25, 7,  get_text('LocRuleDefaultNo','Tournament') . ": ", '', 0, 'R', 0);
			$pdf->Cell(5, 7,  $v, '', 0, 'R', 0);
			$pdf->Cell(5, 7,  '', 'R', 1, 'R', 0);
		}
	}
	//Report Dati da DB
	$firstLoop=true;
	foreach($SyncLastUpdate as $k=>$v) {
		if($firstLoop) {
			$pdf->SetFont($pdf->FontStd,'B',1);
			$pdf->Cell(175, 0.5,  '', 1, 1, 'L', 1);
		}
		$pdf->SetFont($pdf->FontStd,'',10);
		$pdf->Cell(60, 7,  ($firstLoop ? get_text('MsgSyncronize', 'Tournament'). ": " : ""), 'L' . ($firstLoop ? 'T' : ''), 0, 'L', 0);
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->Cell(10, 7, $k , ($firstLoop ? 'T' : ''), 0, 'L', 0);
		$pdf->Cell(105, 7, $v, ($firstLoop ? 'T' : '').'R',1, 'L', 0);
		$firstLoop=false;
	}
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell(10, 5, get_text('Code', 'Tournament'), 1, 0, 'C', 0);
	$pdf->Cell(20, 5, get_text('FamilyName', 'Tournament'), 1, 0, 'C', 0);
	$pdf->Cell(20, 5, get_text('Name', 'Tournament'), 1, 0, 'C', 0);
	$pdf->Cell(20, 5, get_text('DOB', 'Tournament'), 1, 0, 'C', 0);
	$pdf->Cell(10, 5, get_text('CountryCode'), 1, 0, 'C', 0);
	$pdf->Cell(5, 5, get_text('Status', 'Tournament'), 1, 0, 'C', 0);
	$pdf->Cell(5, 5, '', '', 0, 'C', 0);
	$pdf->Cell(10, 5, get_text('Code', 'Tournament'), 1, 0, 'C', 0);
	$pdf->Cell(20, 5, get_text('FamilyName', 'Tournament'), 1, 0, 'C', 0);
	$pdf->Cell(20, 5, get_text('Name', 'Tournament'), 1, 0, 'C', 0);
	$pdf->Cell(20, 5, get_text('DOB', 'Tournament'), 1, 0, 'C', 0);
	$pdf->Cell(10, 5, get_text('CountryCode'), 1, 0, 'C', 0);
	$pdf->Cell(5, 5, get_text('Status', 'Tournament'), 1, 1, 'C', 0);
	$cnt=0;
	$pdf->SetFont($pdf->FontStd,'',8);
	foreach($Changes as $k => $v) {
		$pdf->Cell(10, 5, $k, 1, 0, 'L', 0);
		$pdf->Cell(20, 5, $v[1], 1, 0, 'L', 0);
		$pdf->Cell(20, 5, $v[2], 1, 0, 'L', 0);
		$pdf->Cell(20, 5, $v[4].":".$v[8], 1, 0, 'L', 0);
		$pdf->Cell(10, 5, $v[16], 1, 0, 'L', 0);
		$pdf->Cell(5, 5, $v[32], 1, ($cnt++)%2, 'L', 0);
		if($cnt%2) {
			$pdf->Cell(5, 5, '', '', 0, 'C', 0);
		}
	}
	if($cnt%2) {
		$pdf->Cell(85, 5, '', 'R', 1, 'C', 0);
	}
	
	$pdf->SetFont($pdf->FontStd,'B',1);
	$pdf->Cell(175, 0.5,  '', 1, 1, 'L', 1);
	//Personale sul Campo
//	$RowTournament = NULL;
	$Involved = array();
	$MySql = "SELECT TiName, TiCode, ItDescription  "
		. "FROM TournamentInvolved LEFT JOIN InvolvedType ON TiType=ItId "
		. "WHERE TiTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY ItOc, ItJury, ItDoS, ItJudge, TiName ";
	$Rs=safe_r_sql($MySql);
	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			if(!array_key_exists(get_text($MyRow->ItDescription,'Tournament'), $Involved))
				$Involved[get_text($MyRow->ItDescription,'Tournament')] = '';
			$Involved[get_text($MyRow->ItDescription,'Tournament')] .= (trim($MyRow->TiName) . (strlen($MyRow->TiCode)>0 ? '(' . $MyRow->TiCode . ')' : '') . ', ');
		}
		safe_free_result($Rs);
	}
	if(count($Involved)>0)
	{
		foreach($Involved as $InvType => $InvName)
		{
			$mcStartY = $pdf->GetY();
			$pdf->SetX($pdf->GetX()+40);
			$pdf->SetFont($pdf->FontStd,'B',10);
			//$pdf->Cell(150, 7,  substr($InvName,0,-2) , 'R', 1, 'L', 0);
			$pdf->MultiCell(135, 7,  substr($InvName,0,-2) , 'R', 'L',0,1);
			$mcEndY = $pdf->GetY();
			$pdf->SetY($mcStartY);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell(40, $mcEndY-$mcStartY,  $InvType . ": ", 'L', 1, 'L', 0);

		}
	}
	else
	{
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->Cell(175, 7,  get_text('NoStaffOnField','Tournament'), 'LR', 1, 'L', 0);
	}
	$pdf->SetFont($pdf->FontStd,'B',1);
	$pdf->Cell(175, 0.5,  '', 1, 1, 'L', 1);
	$pdf->SetXY($pdf->GetX(),$pdf->GetY()+5);
	//Parte di Report vera e propria
	/*$MySql = "SELECT FrqId, FrqStatus, FrqQuestion, FrqTip, FrqType, FrqOptions, FraAnswer "
		. "FROM FinalReportQ "
		. "INNER JOIN Tournament ON ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "INNER JOIN Tournament*Type ON TtId=ToType "
		. "LEFT JOIN FinalReportA ON FrqId=FraQuestion AND FraTournament=ToId "
		. "WHERE (FrqStatus & TtCategory) > 0 "
		. "ORDER BY FrqId";*/
	$MySql = "SELECT FrqId, FrqStatus, FrqQuestion, FrqTip, FrqType, FrqOptions, FraAnswer "
		. "FROM FinalReportQ "
		. "INNER JOIN Tournament ON ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "LEFT JOIN FinalReportA ON FrqId=FraQuestion AND FraTournament=ToId "
		. "WHERE (FrqStatus & ToCategory) > 0 "
		. "ORDER BY FrqId";

	$Rs=safe_r_sql($MySql);

	if(safe_num_rows($Rs)>0)
	{
		while($MyRow = safe_fetch($Rs))
		{
			if($MyRow->FrqType==-1)
			{
				$pdf->SetFont($pdf->FontStd,'B',10);
				$pdf->Cell(175, 7,  $MyRow->FrqId . ' - ' . $MyRow->FrqQuestion, 1, 1, 'L', 1);
			}
			else
			{
				switch($MyRow->FrqType)
				{
					case 0:
						$pdf->SetFont($pdf->FontStd,'',8);
						$pdf->Cell(10, 6,  $MyRow->FrqId . ".", 'LTB', 0, 'L', 0);
						$pdf->Cell(50, 6,  $MyRow->FrqQuestion . ": ", 'BT', 0, 'L', 0);
						$pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->Cell(115, 6,  $MyRow->FraAnswer , 'TRB', 1, 'L', 0);
						break;
					case 1:
						$mcStartY = $pdf->GetY();
						$pdf->SetX($pdf->GetX()+60);
						$pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->MultiCell(115, 7,  $MyRow->FraAnswer , 'RTB', 'L', 0, 1);
						$mcEndY = $pdf->GetY();
						if($mcStartY>$mcEndY)
						{
							$tmpMargin = $pdf->getMargins();
							$mcStartY = $tmpMargin['top'];
						}
						$pdf->SetY($mcStartY);
						$pdf->SetFont($pdf->FontStd,'',8);
						$pdf->Cell(10, $mcEndY-$mcStartY,  $MyRow->FrqId . ".", 'LTB' . (strlen($MyRow->FraAnswer)>0 ? '' : 'B'), 0, 'L', 0);
						$pdf->Cell(50, $mcEndY-$mcStartY,  $MyRow->FrqQuestion . ": ", 'TB', 1, 'L', 0);
						break;
					case 2:
						$pdf->SetFont($pdf->FontStd,'',8);
						$pdf->Cell(10, 6,  $MyRow->FrqId . ".", 'LTB', 0, 'L', 0);
						$pdf->Cell(50, 6,  $MyRow->FrqQuestion . ": ", 'BT', 0, 'L', 0);
						$pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->Cell(115, 6,  ($MyRow->FraAnswer=='0' ?  get_text('No') : ($MyRow->FraAnswer=='1' ?  get_text('Yes') : '--')) , 'TRB', 1, 'L', 0);
						break;
					case 3:
						$pdf->SetFont($pdf->FontStd,'',8);
						$pdf->Cell(10, 6,  $MyRow->FrqId . ".", 'LTB', 0, 'L', 0);
						$pdf->Cell(50, 6,  $MyRow->FrqQuestion . ": ", 'BT', 0, 'L', 0);
						$pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->Cell(115, 6,  $MyRow->FraAnswer , 'TRB', 1, 'L', 0);
						break;
					case 4:
						$pdf->SetFont($pdf->FontStd,'',8);
						$pdf->Cell(10, 6,  $MyRow->FrqId . ".", 'LTB', 0, 'L', 0);
						$pdf->Cell(50, 6,  $MyRow->FrqQuestion . ": ", 'BT', 0, 'L', 0);
						$pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->Cell(115, 6,  str_replace("|",", ",$MyRow->FraAnswer) , 'TRB', 1, 'L', 0);
						break;
				}
			}
		}
		safe_free_result($Rs);
	}

	if ($i!=count($copy2)-1)
	{
		$pdf->startPageGroup();
		$pdf->AddPage();
	}

}

if (isset($_REQUEST['TourId']))
{
	EraseTourSession();
}

if(isset($__ExportPDF))
{
	$__ExportPDF = $pdf->Output('','S');
}
elseif(isset($_REQUEST['ToFitarco']))
{
	$Dest='D';
	if (isset($_REQUEST['Dest']))
		$Dest=$_REQUEST['Dest'];

	if ($Dest=='S')
		print $pdf->Output($_REQUEST['ToFitarco'],$Dest);
	else
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
}
else
	$pdf->Output();


//$pdf->Output();

?>