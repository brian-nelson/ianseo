<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/BackNoPDF.php');
require_once('Common/Fun_FormatText.inc.php');

$BisTarget = 0;
$NumEnd = 0;
/*$Select
	= "SELECT (TtElabTeam!=0) as BisTarget, TtNumEnds "
	. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
	. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/

$Select
	= "SELECT (ToElabTeam!=0) as BisTarget, ToNumEnds AS TtNumEnds "
	. "FROM Tournament "
	. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
$RsTour=safe_r_sql($Select);
if (safe_num_rows($RsTour)==1)
{
	$r=safe_fetch($RsTour);
	$BisTarget = $r->BisTarget;
	$NumEnd = $r->TtNumEnds;
	safe_free_result($RsTour);
}

$pdf = new BackNoPDF(0);
$max4Page = $pdf->AthletesPerPage();

if(isset($_REQUEST["BackNoDraw"]) && $_REQUEST['BackNoDraw']=="Test")
{
	$tmp=new stdClass();
	$tmp->EnFirstName=get_text('Athlete');
	$tmp->EnFirstNameUpper=mb_convert_case(get_text('Athlete'), MB_CASE_UPPER, "UTF-8");
	$tmp->EnName=get_text('Athlete');
	$tmp->CoCode='ABC';
	$tmp->CoName=get_text('Country');
	$pdf->DrawElements("1a", $tmp, 0);
	$pdf->DrawElements("99z", $tmp, 1);
}
else
{
	$From=str_pad(intval($_REQUEST['x_From']), 3, '0', STR_PAD_LEFT);
	$To=str_pad(intval($_REQUEST['x_To']), 3, '0', STR_PAD_LEFT);

	$MyQuery = 'SELECT SUBSTRING(at.AtTargetNo,2) as tNo, SUBSTRING(at.AtTargetNo,1,1) as Session, SesName, QuBacknoPrinted, EnFirstName, upper(EnFirstName) EnFirstNameUpper, EnName, CoCode, CoName '
		. ' FROM AvailableTarget as at '
		. ' LEFT JOIN Session ON at.AtTournament=SesTournament AND SUBSTRING(at.AtTargetNo,1,1)=SesOrder AND SesType="Q" '
		. ' ' . (isset($_REQUEST["PrintEmpty"]) && $_REQUEST['PrintEmpty']==1 ? 'LEFT' : 'INNER') . ' JOIN '
		. ' (SELECT QuTargetNo, QuBacknoPrinted, EnTournament, EnFirstName, EnName, CoCode, CoName '
		. ' FROM Qualifications as q '
		. ' INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnAthlete=1 '
		. ' INNER JOIN Countries as c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament) as Sqy on at.AtTargetNo=Sqy.QuTargetNo AND at.AtTournament=Sqy.EnTournament '
		. " WHERE AtTournament =  " . StrSafe_DB($_SESSION['TourId']) . ' '
		. (!empty($_REQUEST['SkipPrinted']) ? ' AND QuBacknoPrinted=0' : '')
		. " AND at.AtTargetNo>='" . $_REQUEST['x_Session'] . $From . "A' AND at.AtTargetNo<='" . $_REQUEST['x_Session'] . $To . "Z' "
		. ' ORDER BY at.AtTargetNo ASC, EnFirstName, EnName, CoCode';
//print $MyQuery;
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)>0)
	{
		$CntBackNo=0;
		while($MyRow=safe_fetch($Rs))
		{
			$Targetno = intval(substr($MyRow->tNo,0,-1));
			$BisValue='';
			if($BisTarget && ($Targetno > $NumEnd))
			{
				$Targetno -= $NumEnd;
				$BisValue='bis';

				if($Targetno > $NumEnd) {
					$Targetno -= $NumEnd;
					$BisValue='ter';
				}
			}
			$Targetno.=substr($MyRow->tNo,-1);
			$pdf->DrawElements(
				(empty($Targetno) ? '' : $Targetno),
				(empty($MyRow) ? '' : $MyRow),
				$CntBackNo,
				$BisValue
				);
			$CntBackNo = ++$CntBackNo % $max4Page;
		}
	}
}

//echo 'Memoria allocata: '.memory_get_peak_usage (true);

if($pdf->BackGroundFile) unlink($pdf->BackGroundFile);

$pdf->Output();
?>