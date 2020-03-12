<?php
require_once('./config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
//require_once('Common/Fun_Phases.inc.php');

$FeedItems=array();

$Grids=array();

$Ind=array();
$Team=array();
$AbsInd=array();
$AbsTeam=array();


$GridIndTemplate="select"
	. " concat_ws('/', concat_ws(' ', upper(e1.EnFirstName), e1.EnName), concat_ws(' ', upper(e2.EnFirstName), e2.EnName)) QryWho"
	. ", concat_ws('-', if(EvMatchMode,f1.FinSetScore,f1.FinScore), if(EvMatchMode,f2.FinSetScore,f2.FinScore)) QryScore"
	. ", '' Rank"
	. ", GrPhase"
	. ", f1.FinEvent"
	. ", EvProgr"
	. ", EvEventName"
	. ", EvTeamEvent "
	. "from"
	. " Finals f1 "
	. " inner join Finals f2 on f1.FinTournament=f2.FinTournament and f1.FinEvent=f2.FinEvent and f2.FinMatchNo=f1.FinMatchNo+1 "
	. " left join Entries e1 on f1.FinTournament=e1.EnTournament and f1.FinAthlete=e1.EnId "
	. " left join Entries e2 on f1.FinTournament=e2.EnTournament and f2.FinAthlete=e2.EnId "
	. " inner join Events on f1.FinEvent=EvCode and f1.FinTournament=EvTournament and EvTeamEvent=0 "
	. " inner join Grids on f1.FinMatchNo=GrMatchNo "

	. "where"
	. " f1.FinMatchNo mod 2=0 "
	. " and f1.FinTournament=$TourId"
	. " and f1.FinEvent='%s'"
	. " and GrPhase=%s";
$GridTeamTemplate="select"
	. " concat_ws('/', concat_ws(' ', c1.CoCode, c1.CoName), concat_ws(' ', c2.CoCode, c2.CoName)) QryWho"
	. ", concat_ws('-', if(EvMatchMode,f1.TfSetScore,f1.TfScore), if(EvMatchMode,f2.TfSetScore,f2.TfScore)) QryScore"
	. ", '' Rank"
	. ", GrPhase"
	. ", f1.TfEvent"
	. ", EvProgr"
	. ", EvEventName"
	. ", EvTeamEvent "
	. "from"
	. " TeamFinals f1 "
	. " inner join TeamFinals f2 on f1.TfTournament=f2.TfTournament and f1.TfEvent=f2.TfEvent and f2.TfMatchNo=f1.TfMatchNo+1 "
	. " left join Countries c1 on f1.TfTournament=c1.CoTournament and f1.TfTeam=c1.CoId "
	. " left join Countries c2 on f1.TfTournament=c2.CoTournament and f2.TfTeam=c2.CoId "
	. " inner join Events on f1.TfEvent=EvCode and f1.TfTournament=EvTournament and EvTeamEvent=1 "
	. " inner join Grids on f1.TfMatchNo=GrMatchNo "

	. "where"
	. " f1.TfMatchNo mod 2=0 "
	. " and f1.TfTournament=$TourId"
	. " and f1.TfEvent='%s'"
	. " and GrPhase=%s";

// finds out what Feeds are to be served!
$MyQuery="select * from BoinxSchedule where (BsType like 'Rss\\_%' or BsType like 'Fee\\_%') and BsTournament=$TourId "
	. "ORDER BY"
	. " BsType";

$Rs=safe_r_sql($MyQuery);
while($MyRow=safe_fetch($Rs) ) {
	$tmp= explode('_', $MyRow->BsType);
	switch($tmp[0]) {
		case 'Fee':
			// phases to show like Rss
			if($tmp[1]=='Ind') {
				$Grids[]=sprintf($GridIndTemplate, $tmp[2], $tmp[3]);
			} elseif($tmp[1]=='Team') {
				$Grids[]=sprintf($GridTeamTemplate, $tmp[2], $tmp[3]);
			}
			break;
		case 'Rss':
			if($tmp[1]=='Ind') {
				$Ind[$tmp[2].$tmp[3]]=$MyRow->BsExtra;
			} elseif($tmp[1]=='Team') {
				$Team[$tmp[2].$tmp[3]]=$MyRow->BsExtra;
			} elseif($tmp[1]=='Abs') {
				$AbsInd[$tmp[2]]=$MyRow->BsExtra;
			} elseif($tmp[1]=='AbsTeam') {
				$AbsTeam[$tmp[2]]=$MyRow->BsExtra;
			}
			// Feeds to show
			break;
	}
}

if($Ind) {
	require_once('Common/Lib/Obj_RankFactory.php');
//
	$options=array('dist'=>0);
	$family='DivClass';
	$options['arrNo'] = 0;
	$options['tournament']=$TourId;
	$options['events']=array_keys($Ind);
	/*
	//Genero la query che mi ritorna tutti al max 10 righe dei podi qualificati
	$options=array('dist'=>0);
	$options['cutRank'] = max(array_values($Ind));
	$options['subFamily'] = 'Abs';
	$family = 'Snapshot';
	$options['arrNo'] = 0;
	$options['tournament']=$TourId;
	$options['events']=array_keys($AbsInd);
*/
	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$rankData=$rank->getData();

	$Title=$rankData['meta']['title'];
	$OnOff="ON";
	foreach($rankData['sections'] as $Event => $data) {
		$FeedItems['AI-'.$Event]['title']=$data['meta']['descr'];
		$txt=array();
		foreach($data['items'] as $item) {
			$txt[]=sprintf("%s,%s,%s,%s,%s,%s,%s" , /*$item['rank']*/ $OnOff, $item['athlete'], "https://www.ianseo.net/TourData/2015/1362/img/" . $item["countryCode"]. ".gif", explode('|',$item['dist_1'])[1], explode('|',$item['dist_2'])[1],explode('|',$item['dist_3'])[1],$item['score']);
			$OnOff = "OFF";
		}
		$FeedItems['AI-'.$Event]['text']=implode("\n", $txt);

	}
}

$StartEvent='a';
$OldEvent='';
$OldPhase='';

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/txt; charset=' . PageEncode);

echo "PROGRAM,Txt Name,[img]Flag,Txt Score 1,Txt Score 2,Txt Score 3,Txt Total Score\n";
foreach($FeedItems as $Title=>$FeedData) {
	echo $FeedData['text']."\n";
}





?>