<?php
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_FormatText.inc.php');

$TourType=getTournamentType($TourId);

$TVsettings->EventFilter=MakeEventFilter($TVsettings->TVPEventInd);

$options=array('tournament' => $RULE->TVRTournament);

$options['subFamily'] = 'DivClass';

if(isset($TVsettings->TVPEventInd) && !empty($TVsettings->TVPEventInd))
	$options['events'] = explode('|',$TVsettings->TVPEventInd);
if(isset($TVsettings->TVPNumRows) && $TVsettings->TVPNumRows>0)
	$options['cutRank'] = $TVsettings->TVPNumRows;
if(isset($TVsettings->TVPSession) && $TVsettings->TVPSession>0)
	$options['session'] = $TVsettings->TVPSession;

$rank=Obj_RankFactory::create('Snapshot',$options);
$rank->read();
$rankData=$rank->getData();


if(count($rankData['sections'])==0) return '';

$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
$ViewTeams=((in_array('TEAM', $Columns) or in_array('ALL', $Columns)) and $TourType!=14 and $TourType != 32);
$ViewDists=((in_array('DIST', $Columns) or in_array('ALL', $Columns)) and $TVsettings->TVPViewPartials);
$ViewTOT=(in_array('TOT', $Columns) or in_array('ALL', $Columns));

$NumColBase=3 + $ViewTeams + $ViewTOT;

foreach($rankData['sections'] as $IdEvent => $section)
{
	// crea l'header della gara
	$tmp = '';

	$NumCol = $NumColBase + ($ViewDists && $TVsettings->TVPViewPartials ? $rankData['meta']['numDist'] : 0);

	$tmp.= '<tr><th class="Title" colspan="' . ($NumCol) . '">';
	$tmp.= $Arr_Pages[$TVsettings->TVPPage];
	$tmp.= '</th></tr>' . "\n";

	// Titolo della tabella
	$tmp.= '<tr><th class="Title" colspan="' . ($NumCol) . '">';
	$tmp.= $section['meta']['descr'];
	$tmp.= '</th></tr>' . "\n";

	// Header vero e proprio, incluse le larghezze delle colonne;
	$col=array();
	$tmp.= '<tr>';
	$tmp.= '<th>' . $section['meta']['fields']['rank'] . '</th>';
	$col[]=7;

	$tmp.= '<th>' . $section['meta']['fields']['athlete'] . '</th>';
	$col[]=33;

	if($ViewTeams) {
		$tmp.= '<th>' . $section['meta']['fields']['countryName'] . '</th>';
		$col[]=($TVsettings->TVPViewNationName?27:11.5);
	}

	if($ViewDists) {
		for ($i=1;$i<=$rankData['meta']['numDist'];++$i)
		{
			$tmp.= '<th>' . $section['meta']['fields']['dist_' . $i] . '</th>';
			$col[]=6.5;
		}
	}

	$tmp.= '<th>' . $section['meta']['printHeader'] . '</th>';
	$col[]=7.5;

	if($ViewTOT) {
		$tmp.= '<th>' . $section['meta']['fields']['score'] . '</th>';
		$col[]=7.5;
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

	foreach($section['items'] as $key => $item) {
		// Dati della tabella aperta in (1)
		// al 4° devo interrompere la tabella e aprire il resto in un div separato
		$tmp = '<tr' . ($key % 2 == 0 ? '': ' class="Next"') . '>'
			. '<th class="Title">' . $item['rank'] . '</th>'
			. '<td><span class="piccolo">' . $item['target'] . '</span> '
			. $item['familynameUpper']
			. ' '
			. ($TVsettings->TVPNameComplete==0 ? FirstLetters($item['givenname']) : $item['givenname'])
			. '</td>';
		if($ViewTeams) $tmp.= '<td style="font-size:80%">' . $item['countryCode'] . ' ' . ($TVsettings->TVPViewNationName==1 ? ($item['countryName']) : '') . '</td>';

		//NewDistanze
		if($ViewDists) {
			for ($i=1;$i<=$rankData['meta']['numDist'];++$i) {
				list($rank, $score, $gold, $xnine)=explode('|', $item['dist_'.$i]);
				if($section['meta']['snapDistance']==0)
					$tmp .= '<td class="NumberAlign">' . str_pad($score,3," ",STR_PAD_LEFT) . '<span class="piccolo">/' . str_pad((($TourType!=14 and $TourType != 32) ? $rank : $xnine),2," ",STR_PAD_LEFT) . '</span></td>';
				else if($i < $section['meta']['snapDistance'])
					$tmp .= '<td class="NumberAlign">' . str_pad($score,3," ",STR_PAD_LEFT) . '</td>';
				else if($i == $section['meta']['snapDistance'])
				{
					list($rank, $score)=explode('|', $item['dist_Snap']);
					$tmp .= '<td class="NumberAlign">' . str_pad($score,3," ",STR_PAD_LEFT) . '</td>';
				}
				else
					$tmp .= '<td class="NumberAlign">' . str_pad("0",3," ",STR_PAD_LEFT) . '</td>';
			}
		}
		$tmp.= '<td class="NumberAlign Grassetto">' . $item['scoreSnap'] . '</td>';
		if($ViewTOT) $tmp.= '<td class="NumberAlign Grassetto">' . ($item['scoreSnap'] != $item['score'] ?  $item['score'] : "&nbsp;") . '</td>';

		$tmp.= '</tr>' . "\n";

		if($item['rank']<=3)
		{
			if(isset($section['items'][7]) && $section['items'][7]['rank']<=3) {
				$ret[$IdEvent]['basso'].=$tmp;
			} else {
				$ret[$IdEvent]['fissi'].=$tmp;
			}
		}
		else
			$ret[$IdEvent]['basso'].=$tmp;
	}
}

?>