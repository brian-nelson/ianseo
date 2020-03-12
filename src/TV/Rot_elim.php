<?php
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_FormatText.inc.php');

$options=array('tournament' => $RULE->TVRTournament);
$Arr_Ev = array();
$Arr_Ph = array();
if($TVsettings->TVPEventInd) $Arr_Ev = explode('|', $TVsettings->TVPEventInd);
if(strlen($TVsettings->TVPPhasesInd)) $Arr_Ph = explode('|', $TVsettings->TVPPhasesInd);

if($Arr_Ev and count($Arr_Ph)) {
	$options['eventsR']=array();
	foreach($Arr_Ph as $p) {
		foreach($Arr_Ev as $e) $options['eventsR'][] = $e . '@' . $p;
	}
} elseif($Arr_Ph) {
	$options['eventsR']=array();
	foreach($Arr_Ph as $p) {
		$options['eventsR'][] = '@' . $p;
	}
} elseif($Arr_Ev) {
	$options['events'] = $Arr_Ev;
}

$family='ElimInd';


$rank=Obj_RankFactory::create($family,$options);
$rank->read();
$rankData=$rank->getData();

/*
echo "<pre>";
print_r($options);
print_r($rankData);
echo "</pre>";
exit;
*/
if(count($rankData['sections'])==0) return '';

$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
$ViewTeams=(in_array('TEAM', $Columns) or in_array('ALL', $Columns)) ;
$ViewTOT=(in_array('TOT', $Columns) or in_array('ALL', $Columns));
$View10s=(in_array('10', $Columns) or in_array('ALL', $Columns));
$ViewX9s=(in_array('X9', $Columns) or in_array('ALL', $Columns));

$NumCol=3 + $ViewTeams + $View10s + $ViewX9s;

foreach($rankData['sections'] as $IdEvent => $section) {
	if(!$section['items']) continue;
	if(!$section['items'][0]['completeScore']) continue;

	// Titolo della tabella
	$tmp = '<tr><th class="Title" colspan="' . ($NumCol) . '">';
	$tmp.= $section['meta']['descr'] . " - " . $section['meta']['round'];
	$tmp.= '</th></tr>' . "\n";

	// Header vero e proprio
	$col=array();
	$tmp.= '<tr>';
	$tmp.= '<th>' . $section['meta']['fields']['rank'] . '</th>';
	$col[]=7;
	$tmp.= '<th>' . $section['meta']['fields']['athlete']. '</th>';
	$col[]=33;
	if($ViewTeams) {
		$tmp.= '<th>' . $section['meta']['fields']['countryName'] . '</th>';
		$col[]=($TVsettings->TVPViewNationName?27:11.5);
	}
	$tmp.= '<th>' . $section['meta']['fields']['completeScore'] . '</th>';
	$col[]=7.5;

	if($section['meta']['running']) {
		$Field10='score';
		$FieldX9='hits';
		$Class10=' Grassetto';
		if($View10s) {
			$tmp.= '<th>' . $section['meta']['fields']['score'] . '</th>';
			$col[]=7.5;
		}
		if($ViewX9s) {
			$tmp.= '<th>' . $section['meta']['fields']['hits'] . '</th>';
			$col[]=4.5;
		}
	} else {
		$Field10='gold';
		$FieldX9='xnine';
		$Class10='';
		if($View10s) {
			$tmp.= '<th>' . $section['meta']['fields']['gold'] . '</th>';
			$col[]=4;
		}
		if($ViewX9s) {
			$tmp.= '<th>' . $section['meta']['fields']['xnine']. '</th>';
			$col[]=4;
		}
	}

	/*
	if($View10s) {
		$tmp.= '<th>' . $section['meta']['fields']['gold'] . '</th>';
		$col[]=4;
	}
	if($ViewX9s) {
		$tmp.= '<th>' . $section['meta']['fields']['xnine'] . '</th>';
		$col[]=4;
	}*/
	$tmp.= '</tr>' . "\n";

	$SumCol=array_sum($col);
	$cols='';
	foreach($col as $w) $cols.='<col width="'.round(100*$w/$SumCol, 0).'%"></col>';

	$ret[$IdEvent]['head']=$tmp;
	$ret[$IdEvent]['cols']=$cols;
	$ret[$IdEvent]['fissi']='';
	$ret[$IdEvent]['basso']='';
	$ret[$IdEvent]['type']='DB';
	$ret[$IdEvent]['style']=$ST;
	$ret[$IdEvent]['js']=$JS;
	$ret[$IdEvent]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId.'&Event='.substr($IdEvent, 0, -1).'&Phase='.(substr($IdEvent, -1) + 1)."';\n";

	foreach($section['items'] as $key => $item)
	{
		// Dati della tabella aperta in (1)
		$tmp = '<tr' . ($key % 2 == 0 ? '': ' class="Next"') . '>';
		$tmp.= '<th class="Title">' .  $item['rank'] . '</th>';
		$tmp.= '<td><span class="piccolo">' .  $item['target'] . '</span> ' . $item['familynameUpper'] . ' ' . ($TVsettings->TVPNameComplete==0 ? FirstLetters($item['givenname']) : $item['givenname']) . '</td>';
		if($ViewTeams) {
			$tmp.= '<td>' . $item['countryCode'] . ' ' . ($TVsettings->TVPViewNationName==1 ? ($item['countryName']) : '') . '</td>';
		}
		$tmp.= '<td class="NumberAlign Grassetto">' . $item['completeScore'] . '</td>';
		if($View10s) {
			$tmp.= '<td class="NumberAlign'.$Class10.'">' . $item[$Field10] . '</td>';
		}
		if($ViewX9s) {
			$tmp.= '<td class="NumberAlign">' . $item[$FieldX9] . '</td>';
		}
		$tmp.= '</tr>' . "\n";

		if($item['rank']<=3)
		{
			if(isset($section['items'][5]) && $section['items'][5]['rank']==1 && $key>0)
				$ret[$IdEvent]['basso'].=$tmp;
			if(isset($section['items'][6]) && $section['items'][6]['rank']==2 && $key>1)
				$ret[$IdEvent]['basso'].=$tmp;
			if(isset($section['items'][7]) && $section['items'][7]['rank']==3 && $key>2)
				$ret[$IdEvent]['basso'].=$tmp;
			else
				$ret[$IdEvent]['fissi'].=$tmp;
		}
		else
			$ret[$IdEvent]['basso'].=$tmp;
	}
}

?>