<?php
// parametri
require_once('Common/OrisFunctions.php');

$PdfData=getMedalList();

$ret=array();
$col=array();
$CurPage=0;
$tmp='<tr><th class="Title" colspan="5">' . $PdfData->Description .'</th></tr>';
$tmp.= '<tr>';
$tmp.= '<th>'.$PdfData->EvName.'</th>';
$col[]=5;
$tmp.= '<th>'.$PdfData->TourWhen.'</th>';
$col[]=3;
$tmp.= '<th>'.$PdfData->Medal.'</th>';
$col[]=3;
$tmp.= '<th>'.$PdfData->Athlete.'</th>';
$col[]=6;
$tmp.= '<th>'.$PdfData->Country.'</th>';
$col[]=6;
$tmp.= '</tr>';

$SumCol=array_sum($col);
$cols='';
foreach($col as $w) $cols.='<col width="'.round(100*$w/$SumCol, 0).'%"></col>';

$CurPage='0';
$ret[$CurPage]['head']=$tmp;
$ret[$CurPage]['cols']=$cols;
$ret[$CurPage]['fissi']='';
$ret[$CurPage]['basso']='';
$ret[$CurPage]['type']='DB';
$ret[$CurPage]['style']=$ST;
$ret[$CurPage]['js']=$JS;
$ret[$CurPage]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId."';\n";

foreach($PdfData->rankData['events'] as $Event => $EventData) {
	// writes the "block" of medals
	// how many medals?
	$Rows=0;
	$Medals='';
	if(isset($EventData['gold'])) {
		foreach($EventData['gold'] as $Medal) {
			$num=count($Medal['athletes']);
			$Rows+=$num;
			$Medals.='<td rowspan="'.$num.'">'.$PdfData->Medal_1.'</td>
				<td>'.$Medal['athletes'][0]['athlete'].'</td>
				<td rowspan="'.$num.'">'.$Medal['countryName'].'</td>
				</tr><tr>';
			for($i=1; $i<$num; $i++) {
				$Medals.='<td>'.$Medal['athletes'][$i]['athlete'].'</td>
					</tr><tr>';
			}
		}
	}
	if(isset($EventData['silver'])) {
		foreach($EventData['silver'] as $Medal) {
			$num=count($Medal['athletes']);
			$Rows+=$num;
			$Medals.='<td rowspan="'.$num.'">'.$PdfData->Medal_2.'</td>
				<td>'.$Medal['athletes'][0]['athlete'].'</td>
				<td rowspan="'.$num.'">'.$Medal['countryName'].'</td>
				</tr><tr>';
			for($i=1; $i<$num; $i++) {
				$Medals.='<td>'.$Medal['athletes'][$i]['athlete'].'</td>
					</tr><tr>';
			}
		}
	}
	if(isset($EventData['bronze'])) {
		foreach($EventData['bronze'] as $Medal) {
			$num=count($Medal['athletes']);
			$Rows+=$num;
			$Medals.='<td rowspan="'.$num.'">'.$PdfData->Medal_3.'</td>
				<td>'.$Medal['athletes'][0]['athlete'].'</td>
				<td rowspan="'.$num.'">'.$Medal['countryName'].'</td>
				</tr><tr>';
			for($i=1; $i<$num; $i++) {
				$Medals.='<td>'.$Medal['athletes'][$i]['athlete'].'</td>
					</tr><tr>';
			}
		}
	}

	// skips to the next event if there are no medals!
	if(!$Rows) continue;

	$Medals=substr($Medals, 0, -4);

	$Day=date('w', strtotime($EventData['date']));

	$tmp='';
	$tmp.='<tr>
			<td rowspan="'.$Rows.'">'.$EventData['evName'].'</td>
			<td rowspan="'.$Rows.'">'.$PdfData->{'DayOfWeek_'.$Day}.' '.$PdfData->{'Month_'.(substr($EventData['date'], 5, 2)+1)}.' '.substr($EventData['date'], 0, 4).'</td>
			'.$Medals;

	$ret[$CurPage]['basso'].=$tmp;
}

