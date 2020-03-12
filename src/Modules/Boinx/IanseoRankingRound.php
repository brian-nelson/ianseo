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
$Lst=array();

$GridIndTemplate="select EvFinalFirstPhase, fs1.FsLetter Target, fs2.FsLetter OppTarget, "
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
	. " inner join Entries e1 on f1.FinTournament=e1.EnTournament and f1.FinAthlete=e1.EnId "
	. " inner join Entries e2 on f1.FinTournament=e2.EnTournament and f2.FinAthlete=e2.EnId "
	. " left join FinSchedule fs1 on f1.FinTournament=fs1.FsTournament and f1.FinEvent=fs1.FsEvent and fs1.FsTeamEvent=0 and fs1.FsMatchNo=f1.FinMatchNo "
	. " left join FinSchedule fs2 on f2.FinTournament=fs2.FsTournament and f2.FinEvent=fs2.FsEvent and fs2.FsTeamEvent=0 and fs2.FsMatchNo=f2.FinMatchNo "
	. " inner join Events on f1.FinEvent=EvCode and f1.FinTournament=EvTournament and EvTeamEvent=0 "
	. " inner join Grids on f1.FinMatchNo=GrMatchNo "

	. "where"
	. " f1.FinMatchNo mod 2=0 "
	. " and f1.FinTournament=$TourId"
	. " and f1.FinEvent='%s'"
	. " and GrPhase=%s";
$GridTeamTemplate="select EvFinalFirstPhase, fs1.FsLetter Target, fs2.FsLetter OppTarget, "
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
	. " inner join Countries c1 on f1.TfTournament=c1.CoTournament and f1.TfTeam=c1.CoId "
	. " inner join Countries c2 on f1.TfTournament=c2.CoTournament and f2.TfTeam=c2.CoId "
	. " left join FinSchedule fs1 on f1.TfTournament=fs1.FsTournament and f1.TfEvent=fs1.FsEvent and fs1.FsTeamEvent=1 and fs1.FsMatchNo=f1.TfMatchNo "
	. " left join FinSchedule fs2 on f2.TfTournament=fs2.FsTournament and f2.TfEvent=fs2.FsEvent and fs2.FsTeamEvent=1 and fs2.FsMatchNo=f2.TfMatchNo "
	. " inner join Events on f1.TfEvent=EvCode and f1.TfTournament=EvTournament and EvTeamEvent=1 "
	. " inner join Grids on f1.TfMatchNo=GrMatchNo "

	. "where"
	. " f1.TfMatchNo mod 2=0 "
	. " and f1.TfTournament=$TourId"
	. " and f1.TfEvent='%s'"
	. " and GrPhase=%s";

$LstTemplate="Select substr(QuTargetNo,2) Target, EnName, EnFirstName
	from Qualifications
	inner join Entries on EnId=QuId and EnTournament=$TourId
	Where QuSession='%s'
	order by QuTargetNo";

// finds out what Feeds are to be served!
$MyQuery="select * from BoinxSchedule
	where (BsType like 'Rss\\_%' or BsType like 'Fee\\_%')
	and BsTournament=$TourId "
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
			} elseif($tmp[1]=='Lst') {
				if(($Ses=substr($MyRow->BsType, -2, 1))=='Q') {
					$Lst[$MyRow->BsExtra[0]]=sprintf($LstTemplate, substr($MyRow->BsType, -1));
				}
			}
			break;
	}
}

if(false and $Lst) {
	foreach($Lst as $k => $SQL) {
		$q=safe_r_SQL($SQL);
		$txt=array();
		while($r=safe_fetch($q)) {
			$txt[]=sprintf("%s %s %s" , ltrim($r->Target,'0'), $r->EnName, $r->EnFirstName);
		}
		$FeedItems['Lst-'.$k]['title']=get_text('StartList', 'Tournament');
		$FeedItems['Lst-'.$k]['text']=implode(' - ', $txt);
	}

}

if($Ind) {
	require_once('Common/Lib/Obj_RankFactory.php');

	//Genero la query che mi ritorna tutti al max 10 righe dei podi qualificati
	$options=array('dist'=>0);
	$options['cutRank'] = max(array_values($Ind));
	$family='DivClass';
//	$options['subFamily'] = 'DivClass';
//	$family = 'Snapshot';
	$options['arrNo'] = 0;
	$options['tournament']=$TourId;
	$options['events']=array_keys($Ind);

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$rankData=$rank->getData();


	$Title=$rankData['meta']['title'];
	foreach($rankData['sections'] as $Event => $data) {
		$FeedItems['I-'.$Event]['title']=$data['meta']['descr'];
		$FeedItems['I-'.$Event]['text']=array();
		foreach(array_slice($data['items'], 0, $Ind[$Event]) as $item) {
			$FeedItems['I-'.$Event]['text'][]=array(
					'rank'=>$item['rank'],
					'name'=>$item['familyname'],
					'score'=>$item['score']);
		}
	}
}

if($Team) {
	if($TeamItems=getTeamItems($Team)) $FeedItems=array_merge_recursive($FeedItems, $TeamItems);
}

if($AbsInd) {
	require_once('Common/Lib/Obj_RankFactory.php');

	//Genero la query che mi ritorna tutti al max 10 righe dei podi qualificati
	$options=array('dist'=>0);
	$options['cutRank'] = max(array_values($AbsInd));
//	$options['subFamily'] = 'Abs';
	$family = 'Abs';
	$options['arrNo'] = 0;
	$options['tournament']=$TourId;
	$options['events']=array_keys($AbsInd);

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$rankData=$rank->getData();


	$Title=$rankData['meta']['title'];
	foreach($rankData['sections'] as $Event => $data) {
		$FeedItems['AI-'.$Event]['title']=$data['meta']['descr'];
		$FeedItems['AI-'.$Event]['text']=array();
		$txt=array();
		foreach(array_slice($data['items'], 0, $AbsInd[$Event]) as $item) {
			$FeedItems['AI-'.$Event]['text'][]=array(
					'rank'=>$item['rank'],
					'name'=>$item['familyname'],
					'score'=>$item['score']);
		}
	}
}

if($AbsTeam) {
	if($TeamItems=getAbsTeamItems($AbsTeam)) $FeedItems=array_merge_recursive($FeedItems, $TeamItems);
}

if($Grids) {
	$txt=array();
	$MyQuery="(".implode(') UNION (', $Grids).") order by EvTeamEvent, EvProgr, GrPhase desc, Target";

	$Rs=safe_r_sql($MyQuery);
	$OldEvent='';
	while($MyRow=safe_fetch($Rs)) {
		if($OldEvent!=$MyRow->EvEventName or $OldPhase!=$MyRow->GrPhase) {
			$Phase=namePhase($MyRow->EvFinalFirstPhase, $MyRow->GrPhase);
			$FeedItems['G-'.$MyRow->EvEventName.'-'.$MyRow->GrPhase]['title']=get_text($MyRow->EvEventName, null, null, true) .' - '. get_text($Phase.'_Phase');
			$FeedItems['G-'.$MyRow->EvEventName.'-'.$MyRow->GrPhase]['text']=array();
			$OldEvent=$MyRow->EvEventName;
			$OldPhase=$MyRow->GrPhase;
		}

		$tgt='';
		if($MyRow->Target) {
			$tgt=ltrim($MyRow->Target, '0');
			if($MyRow->OppTarget and $MyRow->OppTarget!=$MyRow->Target) {
				$tgt.='-';
			}
		}
		if($MyRow->OppTarget) {
			$tgt.=ltrim($MyRow->OppTarget, '0');
		}
		if($tgt) $tgt="# $tgt ";
		$FeedItems['G-'.$MyRow->EvEventName.'-'.$MyRow->GrPhase]['text'][]=array(
					'rank'=>$tgt,
					'name'=>$MyRow->QryWho,
					'score'=>$MyRow->QryScore);
	}
}

$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRss = $XmlDoc->createElement('rss');
$XmlDoc->appendChild($XmlRss);
$XmlRss->setAttributeNode(new DOMAttr('version', '2.0'));
$XmlRss->setAttributeNode(new DOMAttr('xmlns:content', 'http://purl.org/rss/1.0/modules/content/'));
$XmlRss->setAttributeNode(new DOMAttr('xmlns:wfw', 'http://wellformedweb.org/CommentAPI/'));
$XmlRss->setAttributeNode(new DOMAttr('xmlns:dc', 'http://purl.org/dc/elements/1.1/'));

$StartEvent='a';
$OldEvent='';
$OldPhase='';
$i=0;
foreach($FeedItems as $Title=>$FeedData) {
	$XmlRss->appendChild($Item = $XmlDoc->createElement('item'.$i++));
	$Item->appendChild($tit=$XmlDoc->createElement('title'));
	$tit->appendChild($XmlDoc->createCDATASection($FeedData['title']));
	foreach($FeedData['text'] as $row => $items) {
		$Item->appendChild($row=$XmlDoc->createElement('row'.$row));
		$row->appendChild($a=$XmlDoc->createElement('rank'));
		$a->appendChild($XmlDoc->createCDATASection($items['rank']));
		$row->appendChild($a=$XmlDoc->createElement('name'));
		$a->appendChild($XmlDoc->createCDATASection($items['name']));
		$row->appendChild($a=$XmlDoc->createElement('score'));
		$a->appendChild($XmlDoc->createCDATASection($items['score']));
	}
}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);
echo $XmlDoc->SaveXML();

function getTeamItems($Team) {
	global $TourId;
	$FeedItems=array();
	// TEAM EVENTS
	$MyQuery = "SELECT CoCode AS NationCode, CoName AS Nation, TeEvent, ClDescription, DivDescription";
	$MyQuery.= ", TeScore, TeRank, TeGold, TeXnine, ToGolds AS TtGolds, ToXNine AS TtXNine ";
	$MyQuery.= "FROM Tournament AS t ";
	$MyQuery.= "INNER JOIN Teams AS te ON t.ToId=te.TeTournament AND te.TeFinEvent=0 ";
	$MyQuery.= "INNER JOIN Countries AS c ON te.TeCoId=c.CoId AND te.TeTournament=c.CoTournament ";
	$MyQuery.= "INNER JOIN (select concat(DivId,ClId) DivClass, Divisions.*, Classes.* from Divisions inner join Classes on DivTournament=ClTournament where DivAthlete and ClAthlete) DivClas on TeEvent=DivClass and DivTournament=ToId ";
	$MyQuery.= "WHERE ToId = $TourId ";
	$MyQuery.= " AND TeEvent in ('".implode("','",array_keys($Team))."')";
	$MyQuery.= "ORDER BY DivViewOrder, ClViewOrder, TeScore DESC, TeGold DESC, TeXnine DESC, NationCode";

	//echo $MyQuery;exit();
	$Rs=safe_r_sql($MyQuery);

	$CurGroup = "....";
	$CurTeam = "";
	// Variabili per la gestione del ranking
	$MyRank = 1;
	$MyPos = 0;
	// Variabili che contengono i punti del precedente atleta per la gestione del rank
	$MyScoreOld = 0;
	$MyGoldOld = 0;
	$MyXNineOld = 0;

	while($MyRow=safe_fetch($Rs)) {
		//se cambia classifica rifaccio l'header
		if ($CurGroup != $MyRow->TeEvent ) {
			$TmpTitle = (get_text($MyRow->DivDescription,'','',true)) . " - " . (get_text($MyRow->ClDescription,'','',true));
			$FeedItems['T-'.$MyRow->TeEvent]['title'] = get_text('ResultSqClass', 'Tournament') . ': ' . $TmpTitle;
			$FeedItems['T-'.$MyRow->TeEvent]['text']=array();
			$MyRank = 1;
			$MyPos = 0;
			$MyScoreOld = 0;
			$MyGoldOld = 0;
			$MyXNineOld = 0;
			$CurTeam = "";
			$CurGroup = $MyRow->TeEvent;
		}


		// Sicuramente devo incrementare la posizione
		$MyPos++;
		// Se non ho parimerito il ranking è uguale alla posizione
		if (!($MyRow->TeScore==$MyScoreOld && $MyRow->TeGold==$MyGoldOld && $MyRow->TeXnine==$MyXNineOld)) $MyRank = $MyPos;

		if($MyRank<=$Team[$MyRow->TeEvent] and count($txt)<=$Team[$MyRow->TeEvent]) {
			$FeedItems['T-'.$MyRow->TeEvent]['text'][]=array(
					'rank'=>$MyRank,
					'name'=>$MyRow->NationCode . '-' . $MyRow->Nation,
					'score'=>$MyRow->TeScore);
		}
	}

	return $FeedItems;
}

function getAbsTeamItems($Team) {
	global $TourId;
	$FeedItems=array();
	// TEAM EVENTS
	$MyQuery = "SELECT CoCode AS NationCode, TeSubTeam as SubTeam, CoName AS Nation, TeEvent, EvEventName"
		. ", sqY.QuantiPoss as NumGialli, (EvFinalFirstPhase*2) as QualifiedNo, EvQualPrintHead, ";
	$MyQuery.= "TeScore, TeRank, TeGold, TeXnine, ToGolds AS TtGolds, ToXNine AS TtXNine ";
	$MyQuery.= "FROM Tournament AS t ";
	$MyQuery.= "INNER JOIN Teams AS te ON t.ToId=te.TeTournament AND te.TeFinEvent=1 ";
	$MyQuery.= "INNER JOIN Countries AS c ON te.TeCoId=c.CoId AND te.TeTournament=c.CoTournament ";
	$MyQuery.= "INNER JOIN Events AS ev ON te.TeEvent=ev.EvCode AND t.ToId=ev.EvTournament AND EvTeamEvent=1 ";
	//Contatori per Coin toss  & Spareggi
	$MyQuery .= "INNER JOIN (SELECT Count(*) as QuantiPoss, EvCode as SubCode, TeScore AS Score, TeGold AS Gold, TeXnine AS XNine "
		. "FROM  Teams "
		. "INNER JOIN Events ON TeEvent=EvCode AND TeTournament=EvTournament AND EvTeamEvent=TeFinEvent "
		. "WHERE TeTournament = $TourId AND EvTeamEvent=1 "
		. "GROUP BY TeScore, EvCode, TeGold, TeXnine) AS sqY ON sqY.Score=te.TeScore AND sqY.Gold=te.TeGold AND sqY.Xnine=te.TeXnine AND ev.EvCode=sqY.SubCode ";
	//Where Normale
	$MyQuery.= "WHERE ToId = $TourId ";
	$MyQuery .= "AND te.TeEvent in ('" . implode("','", array_keys($Team)) . "') ";
	$MyQuery.= "ORDER BY EvProgr, TeEvent, TeScore DESC, TeGold DESC, TeXnine DESC, TeRank, NationCode, SubTeam"
		//. ", TcOrder "
		;

	//echo $MyQuery;exit();
	$Rs=safe_r_sql($MyQuery);

	$CurGroup = "....";
	$CurTeam = "";
	// Variabili per la gestione del ranking
	$MyRank = 1;
	$MyPos = 0;
	// Variabili che contengono i punti del precedente atleta per la gestione del rank
	$MyScoreOld = 0;
	$MyGoldOld = 0;
	$MyXNineOld = 0;

	$txt=array();

	while($MyRow=safe_fetch($Rs)) {
		//se cambia classifica rifaccio l'header
		if ($CurGroup != $MyRow->TeEvent ) {
			$TmpTitle = get_text($MyRow->EvEventName,'','',true);
			$FeedItems['AT-'.$MyRow->TeEvent]['title'] = get_text('ResultSqAbs', 'Tournament') . ': ' . $TmpTitle;
			$FeedItems['AT-'.$MyRow->TeEvent]['text'] = array();
			$MyRank = 1;
			$MyPos = 0;
			$MyScoreOld = 0;
			$MyGoldOld = 0;
			$MyXNineOld = 0;
			$CurTeam = "";
			$CurGroup = $MyRow->TeEvent;
		}


		// Sicuramente devo incrementare la posizione
		$MyPos++;
		// Se non ho parimerito il ranking è uguale alla posizione
		if (!($MyRow->TeScore==$MyScoreOld && $MyRow->TeGold==$MyGoldOld && $MyRow->TeXnine==$MyXNineOld)) $MyRank = $MyPos;

		if($Team[$MyRow->TeEvent]=='al' or ($MyRank<=$Team[$MyRow->TeEvent] and count($txt)<=$Team[$MyRow->TeEvent])) {
			$FeedItems['AT-'.$MyRow->TeEvent]['text'][]=array(
					'rank'=>$MyRank,
					'name'=>$MyRow->NationCode . '-' . $MyRow->Nation,
					'score'=>$MyRow->TeScore);
		}
	}

	return $FeedItems;
}
/*

<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	>

<channel>
	<title>Ianseo feed</title> Titolo evento (in numero a piacere)

	<language>it</language>
  <item>
    <title>SM OL</title> Titolo evento (in numero a piacere)

    <description>1) Pinco Pallino 325 - 2) Pischio Pullo 288 - 3) Minno Lullo 286</description> Testo evento (lungo a piacere)
  </item>
  <item>
    <title>SF OL</title>
       <description>1) Pinco Pallino 325 - 2) Pischio Pullo 288 - 3) Minno Lullo 286</description>
  </item>
   <item>
    <title>MM OL</title>
    <description>1) Pinco Pallino 325 - 2) Pischio Pullo 288 - 3) Minno Lullo 286</description>

  </item>
</channel>
</rss>

*/
?>