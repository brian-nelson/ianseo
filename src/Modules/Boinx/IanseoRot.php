<?php
require_once('./config.php');
//require_once('Common/Fun_FormatText.inc.php');
//require_once('Common/Lib/ArrTargets.inc.php');
//require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');


$FeedItems=array();

$Ind=array();
$Team=array();
$AbsInd=array();
$AbsTeam=array();
$TargetList=array();
$MatchesInd=array();
$MatchesTeam=array();

// finds out what Feeds are to be served!
$MyQuery="SELECT *
	FROM BoinxSchedule
	WHERE (BsType like 'Rss\\_%' or BsType like 'Fee%') AND BsTournament={$TourId}
	ORDER BY BsType";

$Rs=safe_r_sql($MyQuery);
while($MyRow=safe_fetch($Rs) ) {
	$tmp= explode('_', $MyRow->BsType);
	switch($tmp[0]) {
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
				$TargetList[$tmp[2][0]][]=$tmp[2][1];
			} else {
			}
			break;
		case 'Fee':
			if($tmp[1]=='Ind') {
				$MatchesInd[]="{$tmp[2]}@{$tmp[3]}";
			} elseif($tmp[1]=='Team') {
				$MatchesTeam[]="{$tmp[2]}@{$tmp[3]}";
			} else {
			}
			break;
		default:
	}
}

$fotoHttpDir= 'http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-%s-%s.jpg';
$fotoFileDir= $CFG->DOCUMENT_PATH . 'TV/Photos/' . $TourCodeSafe . '-%s-%s.jpg';

$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRot = $XmlDoc->createElement('rot');
$XmlDoc->appendChild($XmlRot);

if($Ind) {
	//Genero la query che mi ritorna tutti al max 10 righe dei podi qualificati
	$options=array('dist'=>0);
	$family='DivClass';
	$options['arrNo'] = 0;
	$options['tournament']=$TourId;
	$options['events']=array_keys($Ind);

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$rankData=$rank->getData();

	foreach($rankData['sections'] as $Event => $data) {
		$XmlRot->appendChild($XmlEvent = $XmlDoc->createElement('event'));
		//title
		$XmlEvent->appendChild($tmp = $XmlDoc->createElement('title'));
		$tmp->appendChild($tmp=$XmlDoc->createCDATASection($data['meta']['descr']));
		//type
		$XmlEvent->appendChild($XmlDoc->createElement('type',0));

		//archers
		foreach($data['items'] as $item) {
			$XmlEvent->appendChild($XmlArcher = $XmlDoc->createElement('archer'));
			//name
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('name'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['athlete']));
			//rank
			$XmlArcher->appendChild($XmlDoc->createElement('rank', $item['rank']));
			//flag
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('flag'));
			if(file_exists(sprintf($fotoFileDir, 'Fl', $item['countryCode']))) {
				$tmp->appendChild($XmlDoc->createCDATASection(sprintf($fotoHttpDir, 'Fl', $item['countryCode'])));
			}
			//picture
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('picture'));
			if(file_exists(sprintf($fotoFileDir, 'En', $item['id']))) {
				$tmp->appendChild($XmlDoc->createCDATASection(sprintf($fotoHttpDir, 'En', $item['id'])));
			}
			//countrycode
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('countrycode'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['countryCode']));
			//total, gold, x9
			$XmlArcher->appendChild($XmlDoc->createElement('total', $item['score']));
			$XmlArcher->appendChild($XmlDoc->createElement('gold', $item['gold']));
			$XmlArcher->appendChild($XmlDoc->createElement('x-9', $item['xnine']));
			//in-out
			$XmlArcher->appendChild($XmlDoc->createElement('in-out',1));
			$XmlArcher->appendChild($XmlDoc->createElement('ct-so'));
			//shortname
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('shortname'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['familynameUpper']));
		}
	}
}

if($Team) {
	//Genero la query che mi ritorna tutti al max 10 righe dei podi qualificati
	$options=array('dist'=>0);
	$family='DivClassTeam';
	$options['tournament']=$TourId;
	$options['events']=array_keys($Team);

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$rankData=$rank->getData();

	foreach($rankData['sections'] as $Event => $data) {
		$XmlRot->appendChild($XmlEvent = $XmlDoc->createElement('event'));
		//title
		$XmlEvent->appendChild($tmp = $XmlDoc->createElement('title'));
		$tmp->appendChild($tmp=$XmlDoc->createCDATASection($data['meta']['descr']));
		//type
		$XmlEvent->appendChild($XmlDoc->createElement('type',1));

		//archers - in this case teams
		foreach($data['items'] as $item) {
			$XmlEvent->appendChild($XmlArcher = $XmlDoc->createElement('archer'));
			//name
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('name'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['countryName']));
			//rank
			$XmlArcher->appendChild($XmlDoc->createElement('rank', $item['rank']));
			//flag
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('flag'));
			if(file_exists(sprintf($fotoFileDir, 'Fl', $item['countryCode']))) {
				$tmp->appendChild($XmlDoc->createCDATASection(sprintf($fotoHttpDir, 'Fl', $item['countryCode'])));
			}
			//countrycode
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('countrycode'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['countryCode']));
			//Number and names of the archers
			$XmlArcher->appendChild($XmlDoc->createElement('a-num', count($item['athletes'])));
			foreach($item['athletes'] as $k => $v) {
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('a'.$k));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($v['athlete']));
			}

			//total, gold, x9
			$XmlArcher->appendChild($XmlDoc->createElement('total', $item['score']));
			$XmlArcher->appendChild($XmlDoc->createElement('gold', $item['gold']));
			$XmlArcher->appendChild($XmlDoc->createElement('x-9', $item['xnine']));
			//in-out
			$XmlArcher->appendChild($XmlDoc->createElement('in-out',1));
			$XmlArcher->appendChild($XmlDoc->createElement('ct-so'));
			//shortname
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('shortname'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['countryName']));
		}
	}
}

if($AbsInd) {
	//Genero la query che mi ritorna tutti al max 10 righe dei podi qualificati
	$options=array('dist'=>0);
	$family='Abs';
	$options['arrNo'] = 0;
	$options['tournament']=$TourId;
	$options['events']=array_keys($AbsInd);
//	$options['cutRank']=75;

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$rankData=$rank->getData();

	foreach($rankData['sections'] as $Event => $data) {
		$XmlRot->appendChild($XmlEvent = $XmlDoc->createElement('event'));
		//title
		$XmlEvent->appendChild($tmp = $XmlDoc->createElement('title'));
		$tmp->appendChild($tmp=$XmlDoc->createCDATASection($data['meta']['descr']));
		//type
		$XmlEvent->appendChild($XmlDoc->createElement('type',0));

		//archers
		foreach($data['items'] as $item) {
			$XmlEvent->appendChild($XmlArcher = $XmlDoc->createElement('archer'));
			//name
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('name'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['athlete']));
			//rank
			$XmlArcher->appendChild($XmlDoc->createElement('rank', $item['rank']));
			//flag
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('flag'));
			if(file_exists(sprintf($fotoFileDir, 'Fl', $item['countryCode']))) {
				$tmp->appendChild($XmlDoc->createCDATASection(sprintf($fotoHttpDir, 'Fl', $item['countryCode'])));
			}
			//picture
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('picture'));
			if(file_exists(sprintf($fotoFileDir, 'En', $item['id']))) {
				$tmp->appendChild($XmlDoc->createCDATASection(sprintf($fotoHttpDir, 'En', $item['id'])));
			}
			//countrycode
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('countrycode'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['countryCode']));
			//total, gold, x9
			$XmlArcher->appendChild($XmlDoc->createElement('total', $item['score']));
			$XmlArcher->appendChild($XmlDoc->createElement('gold', $item['gold']));
			$XmlArcher->appendChild($XmlDoc->createElement('x-9', $item['xnine']));
			//in-out
			$XmlArcher->appendChild($XmlDoc->createElement('in-out',($item['rank']<=$data['meta']['qualifiedNo'])));
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('ct-so'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection(($item['so'] >= 1 ? $data['meta']['fields']['so']: ($item['ct']>1 ? $data['meta']['fields']['ct'] : ''))));
			//shortname
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('shortname'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['familynameUpper']));
		}
	}
}

if($AbsTeam) {
	//Genero la query che mi ritorna tutti al max 10 righe dei podi qualificati
	$options=array('dist'=>0);
	$family='AbsTeam';
	$options['tournament']=$TourId;
	$options['events']=array_keys($AbsTeam);

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$rankData=$rank->getData();

	foreach($rankData['sections'] as $Event => $data) {
		$XmlRot->appendChild($XmlEvent = $XmlDoc->createElement('event'));
		//title
		$XmlEvent->appendChild($tmp = $XmlDoc->createElement('title'));
		$tmp->appendChild($tmp=$XmlDoc->createCDATASection($data['meta']['descr']));
		//type
		$XmlEvent->appendChild($XmlDoc->createElement('type',1));

		//archers - in this case teams
		foreach($data['items'] as $item) {
			$XmlEvent->appendChild($XmlArcher = $XmlDoc->createElement('archer'));
			//name
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('name'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['countryName']));
			//rank
			$XmlArcher->appendChild($XmlDoc->createElement('rank', $item['rank']));
			//flag
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('flag'));
			if(file_exists(sprintf($fotoFileDir, 'Fl', $item['countryCode']))) {
				$tmp->appendChild($XmlDoc->createCDATASection(sprintf($fotoHttpDir, 'Fl', $item['countryCode'])));
			}
			//countrycode
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('countrycode'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['countryCode']));
			//Number and names of the archers
			$XmlArcher->appendChild($XmlDoc->createElement('a-num', count($item['athletes'])));
			foreach($item['athletes'] as $k => $v) {
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('a'.$k));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($v['athlete']));
			}

			//total, gold, x9
			$XmlArcher->appendChild($XmlDoc->createElement('total', $item['score']));
			$XmlArcher->appendChild($XmlDoc->createElement('gold', $item['gold']));
			$XmlArcher->appendChild($XmlDoc->createElement('x-9', $item['xnine']));
			//in-out
			$XmlArcher->appendChild($XmlDoc->createElement('in-out',($item['rank']<=$data['meta']['qualifiedNo'])));
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('ct-so'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection(($item['so'] >= 1 ? $data['meta']['fields']['so']: ($item['ct']>1 ? $data['meta']['fields']['ct'] : ''))));
			//shortname
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('shortname'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['countryName']));
		}
	}
}

if($MatchesInd) {
	$options=array('dist'=>0);
	$family='GridInd';
	$options['tournament']=$TourId;
	$options['events']=$MatchesInd;

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$rankData=$rank->getData();
	foreach($rankData['sections'] as $Event => $phases) {
		foreach($phases['phases'] as $Phase => $data) {
			$XmlRot->appendChild($XmlEvent = $XmlDoc->createElement('event'));
			//title
			$XmlEvent->appendChild($tmp = $XmlDoc->createElement('title'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($phases['meta']['eventName'] . ' - ' . $data['meta']['phaseName']));
			//type
			$XmlEvent->appendChild($XmlDoc->createElement('type',2));

			// matches
			foreach($data['items'] as $item) {
				if($Phase>16 and ($phases['meta']['firstPhase']==48 or $phases['meta']['firstPhase']==24)) {
					if($item['saved'] or $item['oppSaved'] or (!$item['id'] and !$item['oppId'])) {
						continue;
					}
				}

				$XmlEvent->appendChild($XmlMatch = $XmlDoc->createElement('match'));
				// id = matchno/2
				$XmlMatch->appendChild($XmlDoc->createElement('id', $item['matchNo']/2));

				// Archer Left
				$XmlMatch->appendChild($XmlArcher = $XmlDoc->createElement('archer'));
				//name
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('name'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['athlete']));
				//flag
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('flag'));
				if(file_exists(sprintf($fotoFileDir, 'Fl', $item['countryCode']))) {
					$tmp->appendChild($XmlDoc->createCDATASection(sprintf($fotoHttpDir, 'Fl', $item['countryCode'])));
				}
				//picture
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('photo'));
				if(file_exists(sprintf($fotoFileDir, 'En', $item['id']))) {
					$tmp->appendChild($XmlDoc->createCDATASection(sprintf($fotoHttpDir, 'En', $item['id'])));
				}
				//countrycode
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('countrycode'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['countryCode']));

				//total, gold, x9
				$XmlArcher->appendChild($XmlDoc->createElement('total', $phases['meta']['matchMode'] ? $item['setScore'] : $item['score']));
				//in-out
				$XmlArcher->appendChild($XmlDoc->createElement('in-out', $item['winner']));
				// Target
				$XmlArcher->appendChild($XmlDoc->createElement('target', ltrim($item['target'],'0')));
				//shortname
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('shortname'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['familyNameUpper']));

				// Archer Right
				$XmlMatch->appendChild($XmlArcher = $XmlDoc->createElement('archer'));
				//name
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('name'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['oppAthlete']));
				//flag
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('flag'));
				if(file_exists(sprintf($fotoFileDir, 'Fl', $item['oppCountryCode']))) {
					$tmp->appendChild($XmlDoc->createCDATASection(sprintf($fotoHttpDir, 'Fl', $item['oppCountryCode'])));
				}
				//picture
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('photo'));
				if(file_exists(sprintf($fotoFileDir, 'En', $item['oppId']))) {
					$tmp->appendChild($XmlDoc->createCDATASection(sprintf($fotoHttpDir, 'En', $item['oppId'])));
				}
				//countrycode
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('countrycode'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['oppCountryCode']));

				//total, gold, x9
				$XmlArcher->appendChild($XmlDoc->createElement('total', $phases['meta']['matchMode'] ? $item['oppSetScore'] : $item['oppScore']));
				//in-out
				$XmlArcher->appendChild($XmlDoc->createElement('in-out', $item['oppWinner']));

				// Target
				$XmlArcher->appendChild($XmlDoc->createElement('target', ltrim($item['oppTarget'],'0')));
				//shortname
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('shortname'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['oppFamilyNameUpper']));
			}
		}
	}
}

if($MatchesTeam) {
	$options=array('dist'=>0);
	$family='GridTeam';
	$options['tournament']=$TourId;
	$options['events']=$MatchesTeam;

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$rankData=$rank->getData();
	foreach($rankData['sections'] as $Event => $phases) {
		foreach($phases['phases'] as $Phase => $data) {
			$XmlRot->appendChild($XmlEvent = $XmlDoc->createElement('event'));
			//title
			$XmlEvent->appendChild($tmp = $XmlDoc->createElement('title'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($phases['meta']['eventName'] . ' - ' . $data['meta']['phaseName']));
			//type
			$XmlEvent->appendChild($XmlDoc->createElement('type',3));

			// matches
			foreach($data['items'] as $item) {
				$XmlEvent->appendChild($XmlMatch = $XmlDoc->createElement('match'));
				// id = matchno/2
				$XmlMatch->appendChild($XmlDoc->createElement('id', $item['matchNo']/2));

				/** Archer Left **/
				$XmlMatch->appendChild($XmlArcher = $XmlDoc->createElement('archer'));
				//name
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('name'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['countryName']));
				//flag
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('flag'));
				if(file_exists(sprintf($fotoFileDir, 'Fl', $item['countryCode']))) {
					$tmp->appendChild($XmlDoc->createCDATASection(sprintf($fotoHttpDir, 'Fl', $item['countryCode'])));
				}
				//countrycode
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('countrycode'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['countryCode']));

				// Team Members
				if($item['teamId']) {
					foreach($phases['athletes'][$item['teamId']] as $rSubTeam => $rTeam) {
						foreach($rTeam as $k => $Athlete) {
							$XmlArcher->appendChild($tmp = $XmlDoc->createElement('a'.($k+1)));
							$tmp->appendChild($tmp=$XmlDoc->createCDATASection($Athlete['athlete']));

						}
					}
				}
				//total
				$XmlArcher->appendChild($XmlDoc->createElement('total', $phases['meta']['matchMode'] ? $item['setScore'] : $item['score']));
				//in-out
				$XmlArcher->appendChild($XmlDoc->createElement('in-out', $item['winner']));
				// Target
				$XmlArcher->appendChild($XmlDoc->createElement('target', ltrim($item['target'],'0')));
				//shortname
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('shortname'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['countryName']));

				/** Archer Right **/
				$XmlMatch->appendChild($XmlArcher = $XmlDoc->createElement('archer'));
				//name
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('name'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['oppCountryName']));
				//flag
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('flag'));
				if(file_exists(sprintf($fotoFileDir, 'Fl', $item['oppCountryCode']))) {
					$tmp->appendChild($XmlDoc->createCDATASection(sprintf($fotoHttpDir, 'Fl', $item['oppCountryCode'])));
				}
				//countrycode
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('countrycode'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['oppCountryCode']));

				// Team Members
				if($item['oppTeamId']) {
					foreach($phases['athletes'][$item['oppTeamId']] as $rSubTeam => $rTeam) {
						foreach($rTeam as $k => $Athlete) {
							$XmlArcher->appendChild($tmp = $XmlDoc->createElement('a'.($k+1)));
							$tmp->appendChild($tmp=$XmlDoc->createCDATASection($Athlete['athlete']));

						}
					}
				}
				//total
				$XmlArcher->appendChild($XmlDoc->createElement('total', $phases['meta']['matchMode'] ? $item['oppSetScore'] : $item['oppScore']));
				//in-out
				$XmlArcher->appendChild($XmlDoc->createElement('in-out', $item['oppWinner']));
				// Target
				$XmlArcher->appendChild($XmlDoc->createElement('target', ltrim($item['oppTarget'],'0')));
				//shortname
				$XmlArcher->appendChild($tmp = $XmlDoc->createElement('shortname'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($item['oppCountryName']));
			}
		}
	}
}

if($TargetList) {
	if(!empty($TargetList['Q'])) {
		$Select = "SELECT
				SesName,
				QuSession,
				upper(EnFirstName) ShortName,
				concat(upper(EnFirstName), ' ', EnName) as Athlete,
				substr(QuTargetNo, -4) TargetNo,
				EnId,
				CoCode,
				ClId, DivId, ClDescription, DivDescription,
				group_concat(EvEventName order by EvProgr separator ', ') EventName,
				EvCode
			FROM Entries
			INNER JOIN Qualifications ON QuId=EnId AND QuSession in (".implode(',', $TargetList['Q']).")
			INNER JOIN Countries ON EnCountry=CoId AND CoTournament='{$TourId}'
			INNER JOIN Session ON QuSession=SesOrder AND SesType='Q' AND SesTournament='{$TourId}'
			INNER JOIN Classes ON EnClass=ClId AND ClTournament='{$TourId}'
			INNER JOIN Divisions ON EnDivision=DivId AND DivTournament='{$TourId}'
			Inner join Individuals on IndId=EnId and IndTournament=EnTournament
			inner join Events on EvCode=IndEvent and EvTournament=EnTournament and EvTeamEvent=0
			WHERE EnTournament = '{$TourId}' and EnAthlete=1
			group by EnId
			ORDER BY QuTargetNo ";
		$q=safe_r_sql($Select);
		$Tit='';
		while($r=safe_fetch($q)) {
			$newTit=str_replace('&', 'and', $r->SesName ? $r->SesName : get_text('Session') . ' ' . $r->QuSession);
			if($Tit!=$newTit) {
				$XmlRot->appendChild($XmlEvent = $XmlDoc->createElement('event'));
				//title
				$XmlEvent->appendChild($tmp = $XmlDoc->createElement('title'));
				$tmp->appendChild($tmp=$XmlDoc->createCDATASection($Tit=$newTit));
				//type
				$XmlEvent->appendChild($XmlDoc->createElement('type',4));
			}

			//archers
			$XmlEvent->appendChild($XmlArcher = $XmlDoc->createElement('archer'));
			//name
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('name'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($r->Athlete));
			//rank
			$XmlArcher->appendChild($XmlDoc->createElement('rank', ltrim($r->TargetNo, '0')));
			//flag
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('flag'));
			if(file_exists($fl=sprintf($fotoFileDir, 'Fl', $r->CoCode))) {
				$tmp->appendChild($XmlDoc->createCDATASection($fl));
			}
			//picture
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('picture'));
			if(file_exists($ph=sprintf($fotoFileDir, 'En', $r->EnId))) {
				$tmp->appendChild($XmlDoc->createCDATASection($ph));
			}
			//countrycode
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('countrycode'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($r->CoCode));
			//Category
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('catshort'));
			$tmp->appendChild($XmlDoc->createCDATASection($r->DivId.$r->ClId));
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('catlong'));
			$tmp->appendChild($XmlDoc->createCDATASection($r->DivDescription.' '.$r->ClDescription));
			//Event
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('evshort'));
			$tmp->appendChild($XmlDoc->createCDATASection($r->EvCode));
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('evlong'));
			$tmp->appendChild($XmlDoc->createCDATASection($r->EventName));
			//shortname
			$XmlArcher->appendChild($tmp = $XmlDoc->createElement('shortname'));
			$tmp->appendChild($tmp=$XmlDoc->createCDATASection($r->ShortName));
// 			$XmlArcher->appendChild($XmlDoc->createElement('total', 0));
// 			$XmlArcher->appendChild($XmlDoc->createElement('gold', 0));
// 			$XmlArcher->appendChild($XmlDoc->createElement('x-9', 0));
			//in-out
// 			$XmlArcher->appendChild($XmlDoc->createElement('in-out',1));
// 			$XmlArcher->appendChild($XmlDoc->createElement('ct-so'));
		}
	}

}
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);
echo $XmlDoc->SaveXML();
