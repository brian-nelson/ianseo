<?php

require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_FormatText.inc.php');

$TourType=getTournamentType($TourId);

$TVsettings->EventFilter=MakeEventFilter($TVsettings->TVPEventInd);

$options=array('tournament' => $RULE->TVRTournament);
$options['dist'] = 0;

if(isset($TVsettings->TVPEventInd) && !empty($TVsettings->TVPEventInd))
	$options['events'] = explode('|',$TVsettings->TVPEventInd);
if(isset($TVsettings->TVPNumRows) && $TVsettings->TVPNumRows>0)
	$options['cutRank'] = $TVsettings->TVPNumRows;
if(isset($TVsettings->TVPSession) && $TVsettings->TVPSession>0)
	$options['session'] = $TVsettings->TVPSession;

$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
$ViewTeams=((in_array('TEAM', $Columns) or in_array('ALL', $Columns)) and $TourType != 14 and $TourType != 32);
$ViewDists=((in_array('DIST', $Columns) or in_array('ALL', $Columns)) and $TVsettings->TVPViewPartials);
$View10s=((in_array('10', $Columns) or in_array('ALL', $Columns)) and $TourType != 14 and $TourType != 32);
$ViewX9s=(in_array('X9', $Columns) or in_array('ALL', $Columns));
$comparedTo=preg_grep('/^COMP:/', $Columns);
if(!empty($comparedTo))
	list(,$comparedTo) = explode(":",reset($comparedTo));
$options['comparedTo'] = $comparedTo;

$rank=Obj_RankFactory::create('DivClass',$options);
$rank->read();
$rankData=$rank->getData();

if(count($rankData['sections'])==0) return '';

if(defined('PNLG-ROTS')) {
	$ret=$rankData['sections'];
	return;
}

$NumColBase=(empty($comparedTo) ? 3 : 4) + $ViewTeams + $View10s + $ViewX9s;

foreach($rankData['sections'] as $IdEvent => $data) {
	// crea l'header della gara
	$tmp = '';

	$NumCols=$rankData['meta']['numDist'];
	if($ViewDists) {
		for ($i=1; $i<=$rankData['meta']['numDist']; ++$i) {
			if($data['meta']['fields']['dist_'.$i]=='-') $NumCols--;
		}
	}
	$NumCol = $NumColBase + ($ViewDists && $TVsettings->TVPViewPartials ? $NumCols : 0);

	$tmp.= '<tr><th class="Title" colspan="' . ($NumCol) . '">';
	$tmp.= $Arr_Pages[$TVsettings->TVPPage];
	$tmp.= '</th></tr>' . "\n";

	// Titolo della tabella
	$tmp.= '<tr><th class="Title" colspan="' . ($NumCol) . '">';
	$tmp.= $data['meta']['descr'];
	$tmp.= '</th></tr>' . "\n";

	// Header vero e proprio, incluse le larghezze delle colonne;
	$col=array();
	$tmp.= '<tr>';

	$tmp.= '<th colspan="' . (empty($comparedTo) ? 1 : 2) . '">' . $data['meta']['fields']['rank'] . '</th>';
	if(empty($comparedTo)) {
		$col[]=5;
	} else {
		$col[]=3;
		$col[]=2;
	}


	$tmp.= '<th>' . $data['meta']['fields']['athlete'] . '</th>';
	$col[]=33;

	if($ViewTeams) {
		$tmp.= '<th>' . $data['meta']['fields']['countryName'] . '</th>';
		$col[]=($TVsettings->TVPViewNationName?20:11.5);
	}

	if($ViewDists) {
		for ($i=1; $i<=$rankData['meta']['numDist']; ++$i) {
			if($data['meta']['fields']['dist_'.$i]=='-') continue;
			$tmp.= '<th>' . $data['meta']['fields']['dist_'.$i] . '</th>';
			$col[]=6.5;
		}
	}

	$tmp.= '<th>' . $data['meta']['fields']['score'] . '</th>';
	$col[]=7.5;

	if($View10s) {
		$tmp.= '<th>' . $data['meta']['fields']['gold'] . '</th>';
		$col[]=4;
	}

	if($ViewX9s) {
		$tmp.= '<th>' . $data['meta']['fields']['xnine'] . '</th>';
		$col[]=4;
	}
	$tmp.= '</tr>' . "\n";
	$ret[$IdEvent]['head']=$tmp;

	$SumCol=array_sum($col);
	$cols='';
	foreach($col as $w) $cols.='<col width="'.round(100*$w/$SumCol, 0).'%"></col>';

	$ret[$IdEvent]['cols']=$cols;
	$ret[$IdEvent]['fissi']='';
	$ret[$IdEvent]['basso']='';
	$ret[$IdEvent]['type']='DB';
	$ret[$IdEvent]['style']=$ST;
	$ret[$IdEvent]['js']=$JS;
	$ret[$IdEvent]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId.'&Event='.$IdEvent."';\n";

	// Inserisci adesso le singole righe
	foreach($data['items'] as $key => $archer) {
		// al 4° devo interrompere la tabella e aprire il resto in un div separato
		$tmp = '<tr' . ($key%2 == 0 ? '': ' class="Next"') . '>'
			. '<th class="Title">' . $archer['rank'] . '</th>'
			. (empty($comparedTo) ? '' : '<td class="Center" style="' . ($archer['oldRank'] ? 'background: url(\'' . $CFG->ROOT_DIR . 'Common/Images/' . ($archer['rank']==$archer['oldRank'] ? 'Minus' : ($archer['rank']<$archer['oldRank'] ? 'Up' : 'Down')) . '.png\'); background-repeat:no-repeat; background-size: contain; background-position:center;' :'') . 'color:#FFFFFF; font-weight:bold; font-size:60%; ">' . ($archer['oldRank']&& $archer['oldRank']!=$archer['rank'] ? $archer['oldRank']:'&nbsp;'). '</td>')
			. '<td><span class="piccolo">' . $archer['target'] . '</span> '
			. $archer['familynameUpper']
			. ' '
			. ($TVsettings->TVPNameComplete==0 ? FirstLetters($archer['givenname']) : $archer['givenname'])
			. '</td>';

		if($ViewTeams) $tmp.= '<td style="font-size:80%">' . $archer['countryCode'] . ' ' . ($TVsettings->TVPViewNationName==1 ? $archer['countryName'] : '') . '</td>';

		if($ViewDists) {
			for ($i=1; $i<=$rankData['meta']['numDist']; ++$i) {
				if($data['meta']['fields']['dist_'.$i]=='-') continue;
				$bits=explode('|', $archer['dist_'.$i]);
				$tmp.= '<td class="NumberAlign">' . str_pad($bits[1],3," ",STR_PAD_LEFT) . '<span class="piccolo">/' . str_pad((($TourType != 14 and $TourType != 32) ? $bits[0] : $bits[3]),2," ",STR_PAD_LEFT) . '</span></td>';
			}
		}

		$tmp.= '<td class="NumberAlign Grassetto">' . $archer['score'] . '</td>';

		if($View10s) $tmp.= '<td class="NumberAlign">' . $archer['gold'] . '</td>';

		if($ViewX9s) $tmp.= '<td class="NumberAlign">' . $archer['xnine'] . '</td>';
		$tmp.= '</tr>' . "\n";

		// COMMENTATO PER NFAA
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