<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');;
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Fun_CONI.local.inc.php');

if(!isset($isCompleteResultBook))
	$pdf = new OrisPDF('', get_text('Rankings'));
else
	$pdf->setOrisCode('', get_text('Rankings'));
	
$MyQuery = "SELECT EvCode AS EventCode, EvEventName AS EventName "
	. "FROM Events "
	. "WHERE EvTeamEvent != 0 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
	. "ORDER BY EvProgr ASC, EvEventName ASC";
$RsEv=safe_r_sql($MyQuery);

if(safe_num_rows($RsEv)>0)
{
	while($MyRowEv=safe_fetch($RsEv))
	{		
		$tmp='';
		if (!isset($_REQUEST['noname']))
			$tmp="Componenti";
		$arrTitles=array("Rank", "Team",  $tmp, "Punti totali#", "Score#", " ");
		$arrSizes=array(15,80,55,20,20);
					
		$pdf->setEvent(get_text($MyRowEv->EventName,'','',true));
		$pdf->setPhase(get_text('Rankings'));
			
		$pdf->SetDataHeader(array(), array());
		$pdf->AddPage();
		$pdf->SetDataHeader($arrTitles, $arrSizes);
		$pdf->printHeader(OrisPDF::leftMargin, $pdf->lastY);
		
		$parts=array
		(
			'from1to4'=>  finalRankFirst4($MyRowEv->EventCode) ,		// i primi 4
			'from5to8'=> finalRank5_8($MyRowEv->EventCode),									// dal 5 all' 8
			'from9to16'=> rankPhase2($MyRowEv->EventCode,array(3,4),$MyRowEv->EventCode)		// dal 9 al 16
		);
		
		foreach ($parts as $k=> $rows)
		{
			if (count($rows))
			{
				foreach ($rows as $r)
				{
					$Tie='';
					if ($k=='from1to4')
					{
						if ($r->Tie==1)
						{
							$Tie='*';
						}
						elseif ($r->Tie==2)
						{
							$Tie='-bye';
						}
						//$Tie=($r->Tie==1 ? '*' : $r->Tie==2 ? '-bye-' : '');	
					}
					else
					{
						
						$Tie=$r->Tie;
					}
					
					$components=getTeamComponents($r->CountryId, $MyRowEv->EventCode);
					
					$pdf->printDataRow(array(
						$r->Rank,
						$r->Country,
						(!isset($_REQUEST['noname']) ?  $components[0]->Archer : ''),
						$r->Points,
						$r->Score,
						$Tie
					));
					$pdf->lastY += 2;
					
					if (!isset($_REQUEST['noname']))
					{
						for ($i=1;$i<count($components);++$i)
						{
							$pdf->printDataRow(array(
								"",
								"",
								$components[$i]->Archer,
								"",
								"",
								""
							));
							$pdf->lastY += 2;
						}
						$pdf->lastY += 5;
					}
					
				}
			}
		}
	}
}
	
if(!isset($isCompleteResultBook))
{
	if(isset($_REQUEST['ToFitarco']))
	{
		$Dest='D';
		if (isset($_REQUEST['Dest']))
			$Dest=$_REQUEST['Dest'];
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
	}
	else
		$pdf->Output();
}
?>