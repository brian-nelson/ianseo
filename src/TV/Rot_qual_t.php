<?php

require_once('Common/Lib/Obj_RankFactory.php');

$TVsettings->EventFilter=MakeEventFilter($TVsettings->TVPEventTeam);

$options=array('tournament' => $RULE->TVRTournament);
$options['dist'] = 0;

if(isset($TVsettings->TVPEventTeam) && !empty($TVsettings->TVPEventTeam))
	$options['events'] = explode('|',$TVsettings->TVPEventTeam);
if(isset($TVsettings->TVPNumRows) && $TVsettings->TVPNumRows>0)
	$options['cutRank'] = $TVsettings->TVPNumRows;
if(isset($TVsettings->TVPSession) && $TVsettings->TVPSession>0)
	$options['session'] = $TVsettings->TVPSession;

$rank=Obj_RankFactory::create('DivClassTeam',$options);
$rank->read();
$rankData=$rank->getData();

if(count($rankData['sections'])==0) return '';

$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
$ViewAths=(in_array('ATHL', $Columns) or in_array('ALL', $Columns));
$View10s=(in_array('10', $Columns) or in_array('ALL', $Columns));
$ViewX9s=(in_array('X9', $Columns) or in_array('ALL', $Columns));

$NumCol = 3 + $ViewAths + $View10s + $ViewX9s;

foreach($rankData['sections'] as $IdEvent => $data) {

	// Titolo della tabella
	$tmp = '<tr><th class="Title" colspan="' . ($NumCol) . '">';
	$tmp.= $Arr_Pages[$TVsettings->TVPPage];
	$tmp.= '</th></tr>' . "\n";

	$tmp.= '<tr><th class="Title" colspan="' . ($NumCol) . '">';
	$tmp.= $data['meta']['descr'];
	$tmp.= '</th></tr>' . "\n";

	// Header vero e proprio
	$col=array();
	$tmp.= '<tr>';
	$tmp.= '<th>' . $data['meta']['fields']['rank'] . '</th>';
	$col[]=7;
	$tmp.= '<th>' . $data['meta']['fields']['countryName'] . '</th>';
	$col[]=34;
	if($ViewAths) {
		$tmp.= '<th>' . get_text('Athlete') . '</th>';
		$col[]=41;
	}
	$tmp.= '<th>' . $data['meta']['fields']['score'] . '</th>';
	$col[]=8;
	if($View10s) {
		$tmp.= '<th>' . $data['meta']['fields']['gold'] . '</th>';
		$col[]=4;
	}
	if($ViewX9s) {
		$tmp.= '<th>' . $data['meta']['fields']['xnine'] . '</th>';
		$col[]=4;
	}
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
	$ret[$IdEvent]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId.'&Event='.$IdEvent."';\n";

	// Inserisci adesso le singole righe
	foreach($data['items'] as $key => $archer) {
		$NumNomi = count($archer['athletes']);

		// al 4° devo interrompere la tabella e aprire il resto in un div separato
		$tmp = '<tr' . ($key%2 == 0 ? '': ' class="Next"') . '>';
		$tmp.= '<th class="Title">' . $archer['rank'] . '</th>';
	    $tmp.= '<td>' . $archer['countryCode'] . ' ' . ($archer['countryName']) . '</td>';
	    if($ViewAths) {
		    $tmp.= '<td>' . $archer['athletes'][0]['athlete'];
		    for ($i=1; $i<$NumNomi;++$i)
		    	$tmp.= '<br/>' . $archer['athletes'][$i]['athlete'] ;
		    $tmp.= '</td>';
	    }
      $tmp.= '<td class="NumberAlign Grassetto">' . $archer['score'] . '</td>';
      if($View10s) $tmp.= '<td class="NumberAlign">' . $archer['gold'] . '</td>';
      if($ViewX9s) $tmp.= '<td class="NumberAlign">' . $archer['xnine'] . '</td>';
	  $tmp.= '</tr>' . "\n";

		if($archer['rank']<=3)
		{
			if(isset($data['items'][5]) && $data['items'][5]['rank']==1 && $key>0)
				$ret[$IdEvent]['basso'].=$tmp;
			if(isset($data['items'][6]) && $data['items'][6]['rank']==2 && $key>1)
				$ret[$IdEvent]['basso'].=$tmp;
			if(isset($data['items'][7]) && $data['items'][7]['rank']==3 && $key>2)
				$ret[$IdEvent]['basso'].=$tmp;
			else
				$ret[$IdEvent]['fissi'].=$tmp;
		}
		else
			$ret[$IdEvent]['basso'].=$tmp;

	}
}

?>