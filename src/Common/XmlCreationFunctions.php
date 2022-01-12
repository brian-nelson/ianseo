<?php

require_once('Common/StartListQueries.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Lib/ArrTargets.inc.php');

function XmlCreateSessions() {
	$XmlDoc = new DOMDocument('1.0', 'UTF-8');

	$TmpNode = $XmlDoc->createProcessingInstruction ("xml-stylesheet", 'type="text/xsl" href="/Common/Styles/StyleStartList.xsl" ');
	$XmlDoc->appendChild($TmpNode);


	$XmlRoot = $XmlDoc->createElement('Results');
	$XmlRoot->setAttribute('IANSEO', ProgramVersion);
	$XmlRoot->setAttribute('TS', date('Y-m-d H:i:s'));
	$XmlDoc->appendChild($XmlRoot);

	$ListHeader = NULL;

	$MyQuery = getStartListQuery();

	$Rs=safe_r_sql($MyQuery);

	$CurSession=-1;
	$CurTarget='xx';

	$EmptySession=true;
	while($MyRow=safe_fetch($Rs)) {
		// faccio il titolo
		if ($CurSession != $MyRow->Session) {
			if($ListHeader and $EmptySession) $XmlRoot->removechild($ListHeader);
			$EmptySession=true;
			$CurSession=$MyRow->Session;

			$txt='';
			if ($MyRow->SesName!='')
				$txt=$MyRow->SesName . ' (' . get_text('Session') . ' ' . $CurSession . ')';
			else
				$txt=get_text('Session') . ' ' . $CurSession;

			$ListHeader = $XmlDoc->createElement('List');
				//$ListHeader->setAttribute('Title', get_text('Session') . ' ' . $CurSession);
				$ListHeader->setAttribute('Title', $txt);
				$ListHeader->setAttribute('Columns', 10);
			$XmlRoot->appendChild($ListHeader);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Target'));
				$TmpNode->setAttribute('Name', 'Target');
				$TmpNode->setAttribute('Columns', '2');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Code','Tournament'));
				$TmpNode->setAttribute('Name', 'Code');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Athlete'));
				$TmpNode->setAttribute('Name', 'Athlete');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Country'));
				$TmpNode->setAttribute('Name', 'Country');
				$TmpNode->setAttribute('Columns', '2');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('AgeCl'));
				$TmpNode->setAttribute('Name', 'AgeClass');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('SubCl','Tournament'));
				$TmpNode->setAttribute('Name', 'SubCl');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Div'));
				$TmpNode->setAttribute('Name', 'Div');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Cl'));
				$TmpNode->setAttribute('Name', 'Class');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);
		}

		$XmlAthlete=$XmlDoc->createElement('Athlete');
		$ListHeader->appendChild($XmlAthlete);

		$Target='';
		if ($CurTarget!=substr($MyRow->TargetNo,0,-1)) {
			$Target=substr($MyRow->TargetNo,0,-1);
			$CurTarget=substr($MyRow->TargetNo,0,-1);
		}

		$Element = $XmlDoc->createElement('Item',$Target);
		$Element->setAttribute('Name', 'TargetNo');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', substr($MyRow->TargetNo,-1));
		$Element->setAttribute('Name', 'TargetLetter');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->Bib);
		$Element->setAttribute('Name', 'Bib');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->FirstName . ' ' . $MyRow->Name);
		$Element->setAttribute('Name', 'Athlete');
		$XmlAthlete->appendChild($Element);
		if(trim($MyRow->FirstName . ' ' . $MyRow->Name)) $EmptySession=false;

		$Element = $XmlDoc->createElement('Item', $MyRow->NationCode);
		$Element->setAttribute('Name', 'CountryCode');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->Nation . ($MyRow->NationCode2 ? ' (' . $MyRow->NationCode2 . ' ' . $MyRow->Nation2 . ')' : ''));
		$Element->setAttribute('Name', 'Country');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->AgeClass);
		$Element->setAttribute('Name', 'AgeClass');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->SubClass);
		$Element->setAttribute('Name', 'SubClass');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->DivCode);
		$Element->setAttribute('Name', 'Div');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->ClassCode);
		$Element->setAttribute('Name', 'Class');
		$XmlAthlete->appendChild($Element);
	}
	return $XmlDoc;
}

function XmlCreateSessionsCountries() {
	$XmlDoc = new DOMDocument('1.0', 'UTF-8');

	$TmpNode = $XmlDoc->createProcessingInstruction ("xml-stylesheet", 'type="text/xsl" href="/Common/Styles/StyleStartList.xsl" ');
	$XmlDoc->appendChild($TmpNode);


	$XmlRoot = $XmlDoc->createElement('Results');
	$XmlRoot->setAttribute('IANSEO', ProgramVersion);
	$XmlRoot->setAttribute('TS', date('Y-m-d H:i:s'));
	$XmlDoc->appendChild($XmlRoot);

	$MyQuery = getStartListCountryQuery();
	$Rs=safe_r_sql($MyQuery);

	$CurTeam=-1;

	$ListHeader = $XmlDoc->createElement('List');
		$ListHeader->setAttribute('Columns', '10');
	$XmlRoot->appendChild($ListHeader);

	$TmpNode = $XmlDoc->createElement('Caption',get_text('Country'));
		$TmpNode->setAttribute('Name', 'Country');
		$TmpNode->setAttribute('Columns', '1');
	$ListHeader->appendChild($TmpNode);

	$TmpNode = $XmlDoc->createElement('Caption',get_text('Session'));
		$TmpNode->setAttribute('Name', 'Session');
		$TmpNode->setAttribute('Columns', '1');
	$ListHeader->appendChild($TmpNode);

	$TmpNode = $XmlDoc->createElement('Caption',get_text('Target'));
		$TmpNode->setAttribute('Name', 'TargetNo');
		$TmpNode->setAttribute('Columns', '1');
	$ListHeader->appendChild($TmpNode);

	$TmpNode = $XmlDoc->createElement('Caption',get_text('Code','Tournament'));
		$TmpNode->setAttribute('Name', 'Code');
		$TmpNode->setAttribute('Columns', '1');
	$ListHeader->appendChild($TmpNode);

	$TmpNode = $XmlDoc->createElement('Caption',get_text('Athlete'));
		$TmpNode->setAttribute('Name', 'Athlete');
		$TmpNode->setAttribute('Columns', '1');
	$ListHeader->appendChild($TmpNode);

	$TmpNode = $XmlDoc->createElement('Caption',get_text('AgeCl'));
		$TmpNode->setAttribute('Name', 'AgeClass');
		$TmpNode->setAttribute('Columns', '1');
	$ListHeader->appendChild($TmpNode);

	$TmpNode = $XmlDoc->createElement('Caption',get_text('SubCl','Tournament'));
		$TmpNode->setAttribute('Name', 'SubCl');
		$TmpNode->setAttribute('Columns', '1');
	$ListHeader->appendChild($TmpNode);

	$TmpNode = $XmlDoc->createElement('Caption',get_text('Div'));
		$TmpNode->setAttribute('Name', 'Div');
		$TmpNode->setAttribute('Columns', '1');
	$ListHeader->appendChild($TmpNode);

	$TmpNode = $XmlDoc->createElement('Caption',get_text('Cl'));
		$TmpNode->setAttribute('Name', 'Class');
		$TmpNode->setAttribute('Columns', '1');
	$ListHeader->appendChild($TmpNode);

	while($MyRow=safe_fetch($Rs)) {
		$XmlAthlete=$XmlDoc->createElement('Athlete');
		$ListHeader->appendChild($XmlAthlete);

		$Team='';
		if ($CurTeam!=$MyRow->NationCode) {
			$Team=$MyRow->NationCode . ' - ' . $MyRow->Nation;
			$CurTeam=$MyRow->NationCode;
		}

		$Element = $XmlDoc->createElement('Item', $Team);
		$Element->setAttribute('Name', 'Country');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item',$MyRow->Session);
		$Element->setAttribute('Name', 'Session');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item',$MyRow->TargetNo);
		$Element->setAttribute('Name', 'TargetNo');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item',$MyRow->Bib);
		$Element->setAttribute('Name', 'Bib');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item',$MyRow->FirstName . ' ' . $MyRow->Name);
		$Element->setAttribute('Name', 'Bib');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->AgeClass);
		$Element->setAttribute('Name', 'AgeClass');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->SubClass);
		$Element->setAttribute('Name', 'SubClass');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->DivCode);
		$Element->setAttribute('Name', 'Div');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->ClassCode);
		$Element->setAttribute('Name', 'Class');
		$XmlAthlete->appendChild($Element);
	}

	return $XmlDoc;
}

function XmlCreateSessionsAlpha() {
	$XmlDoc = new DOMDocument('1.0', 'UTF-8');

	$TmpNode = $XmlDoc->createProcessingInstruction ("xml-stylesheet", 'type="text/xsl" href="/Common/Styles/StyleStartList.xsl" ');
	$XmlDoc->appendChild($TmpNode);

	$XmlRoot = $XmlDoc->createElement('Results');
	$XmlRoot->setAttribute('IANSEO', ProgramVersion);
	$XmlRoot->setAttribute('TS', date('Y-m-d H:i:s'));
	$XmlDoc->appendChild($XmlRoot);

	$ListHeader = NULL;

	$MyQuery = getStartListAlphaQuery();

	$Rs=safe_r_sql($MyQuery);

	$StartLetter = ".";

	while($MyRow=safe_fetch($Rs)) {
		if ($StartLetter != strtoupper(substr($MyRow->FirstName,0,1))) {
			$StartLetter = strtoupper(substr($MyRow->FirstName,0,1));

			$ListHeader = $XmlDoc->createElement('List');
				$ListHeader->setAttribute('Title', $StartLetter);
				$ListHeader->setAttribute('Columns', 10);
			$XmlRoot->appendChild($ListHeader);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Code','Tournament'));
				$TmpNode->setAttribute('Name', 'Code');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Athlete'));
				$TmpNode->setAttribute('Name', 'Athlete');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Country'));
				$TmpNode->setAttribute('Name', 'Country');
				$TmpNode->setAttribute('Columns', '2');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Session'));
				$TmpNode->setAttribute('Name', 'Session');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Target'));
				$TmpNode->setAttribute('Name', 'Target');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('AgeCl'));
				$TmpNode->setAttribute('Name', 'AgeClass');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('SubCl','Tournament'));
				$TmpNode->setAttribute('Name', 'SubCl');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Div'));
				$TmpNode->setAttribute('Name', 'Div');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Cl'));
				$TmpNode->setAttribute('Name', 'Class');
				$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);
		}

		$XmlAthlete=$XmlDoc->createElement('Athlete');
		$ListHeader->appendChild($XmlAthlete);

		$Element = $XmlDoc->createElement('Item', $MyRow->Bib);
		$Element->setAttribute('Name', 'Bib');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->FirstName . ' ' . $MyRow->Name);
		$Element->setAttribute('Name', 'Athlete');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->NationCode);
		$Element->setAttribute('Name', 'CountryCode');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->Nation);
		$Element->setAttribute('Name', 'Country');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item',$MyRow->Session);
		$Element->setAttribute('Name', 'Session');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->TargetNo);
		$Element->setAttribute('Name', 'TargetNo');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->AgeClass);
		$Element->setAttribute('Name', 'AgeClass');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->SubClass);
		$Element->setAttribute('Name', 'SubClass');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->DivCode);
		$Element->setAttribute('Name', 'Div');
		$XmlAthlete->appendChild($Element);

		$Element = $XmlDoc->createElement('Item', $MyRow->ClassCode);
		$Element->setAttribute('Name', 'Class');
		$XmlAthlete->appendChild($Element);
	}

	return $XmlDoc;
}

function XmlCreateQualIndividual($EventRequested='') {
	$XmlDoc = new DOMDocument('1.0', 'UTF-8');

	$TmpNode = $XmlDoc->createProcessingInstruction ("xml-stylesheet", 'type="text/xsl" href="/Common/Styles/StyleIndividual.xsl" ');
	$XmlDoc->appendChild($TmpNode);

	$XmlRoot = $XmlDoc->createElement('Results');
	$XmlRoot->setAttribute('IANSEO', ProgramVersion);
	$XmlRoot->setAttribute('TS', date('Y-m-d H:i:s'));
	$XmlDoc->appendChild($XmlRoot);

	$ListHeader = NULL;

	$options=array('dist'=>0);
	if(isset($_REQUEST["Event"]) && preg_match("/^[0-9A-Z]+$/i",$_REQUEST["Event"]))
		$options['events'] = $_REQUEST["Event"];
	if($EventRequested) $options['events']=$EventRequested;
	if(isset($_REQUEST["MaxNum"]) && is_numeric($_REQUEST["MaxNum"]))
		$options['cutRank'] = $_REQUEST["MaxNum"];
	$family='Abs';

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$rankData=$rank->getData();

	if(count($rankData['sections'])) {
		foreach($rankData['sections'] as $section) {
			$ListHeader = $XmlDoc->createElement('List');
			$ListHeader->setAttribute('Title', get_text($section['meta']['descr'],'','',true));
			$ListHeader->setAttribute('Columns', '11' + $section['meta']['numDist']);
			$XmlRoot->appendChild($ListHeader);

			$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['rank']);
			$TmpNode->setAttribute('Name', 'Rank');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['athlete']);
			$TmpNode->setAttribute('Name', 'Athlete Name');
			$TmpNode->setAttribute('Columns', '2');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['class']);
			$TmpNode->setAttribute('Name', 'Class');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['countryName']);
			$TmpNode->setAttribute('Name', 'Nation');
			$TmpNode->setAttribute('Columns', '2');
			$ListHeader->appendChild($TmpNode);

			for ($i=1;$i<=$section['meta']['numDist'];++$i)
			{
				$TmpNode=$XmlDoc->createElement('Caption',$section['meta']['fields']['dist_'. $i]);
				$TmpNode->setAttribute('Name','Distance' . $i);
				$TmpNode->setAttribute('Columns',1);
				$ListHeader->appendChild($TmpNode);
			}

			$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['score']);
			$TmpNode->setAttribute('Name', 'Totale');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['gold']);
			$TmpNode->setAttribute('Name', 'Gold');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['xnine']);
			$TmpNode->setAttribute('Name', 'XNine');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption','');
			$TmpNode->setAttribute('Name', 'SO');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$EndQualified = false;
			foreach($section['items'] as $item)
			{
				if($item['rank'] > $section['meta']['qualifiedNo'] && !$EndQualified)
				{
					$Athlete = $XmlDoc->createElement('Athlete');
			   		$ListHeader->appendChild($Athlete);

			   			$Element = $XmlDoc->createElement('Item');
			   				$cdata=$XmlDoc->createCDATASection(' ');
			   				$Element->appendChild($cdata);
			   			$Element->setAttribute('Name', 'Separator');
			   			$Element->setAttribute('Columns', 13);
			   			$Athlete->appendChild($Element);

					$EndQualified = true;
				}

				//Stampo la singola riga
				$Athlete = $XmlDoc->createElement('Athlete');
				$ListHeader->appendChild($Athlete);

				$Element = $XmlDoc->createElement('Item', $item['rank']);
				$Element->setAttribute('Name', 'Rank');
				$Athlete->appendChild($Element);

				$Element = $XmlDoc->createElement('Item', $item['target']);
				$Element->setAttribute('Name', 'Target No');
				$Athlete->appendChild($Element);

				$Element = $XmlDoc->createElement('Item', $item['athlete']);
				$Element->setAttribute('Name', 'Athlete Name');
				$Athlete->appendChild($Element);

				$Element = $XmlDoc->createElement('Item', $item['class'] . ($item['class']!=$item['ageclass'] ?  ' ' . ($item['ageclass']) : ''));
				$Element->setAttribute('Name', 'Class');
				$Athlete->appendChild($Element);

				$Element = $XmlDoc->createElement('Item',$item['countryCode']);
				$Element->setAttribute('Name', 'Nation Code');
				$Athlete->appendChild($Element);
				$Element = $XmlDoc->createElement('Item',$item['countryName']);
				$Element->setAttribute('Name', 'Nation');
				$Athlete->appendChild($Element);


				for($i=1; $i<=$section['meta']['numDist'];$i++)
				{
					list($rank,$score)=explode('|',$item['dist_' . $i]);
					$Element = $XmlDoc->createElement('Item', $score . "/" . str_pad($rank,2,"0",STR_PAD_LEFT));
					$Element->setAttribute('Name', 'Score'.$i);
					$Athlete->appendChild($Element);
				}

				$Element = $XmlDoc->createElement('Item', $item['score']);
				$Element->setAttribute('Name', 'Total');
				$Athlete->appendChild($Element);

				$Element = $XmlDoc->createElement('Item', $item['gold']);
				$Element->setAttribute('Name', 'Gold');
				$Athlete->appendChild($Element);

				$Element = $XmlDoc->createElement('Item', $item['xnine']);
				$Element->setAttribute('Name', 'Xnine');
				$Athlete->appendChild($Element);

				//Definizione dello spareggio/Sorteggio
				$so='';
				if($item['so']>0)  //Spareggio
				{
					$tmpArr="";
					if(strlen(trim($item['tiebreak'])))
					{
						$tmpArr="-T.";
						for($countArr=0; $countArr<strlen(trim($item['tiebreak'])); $countArr++)
							$tmpArr .= DecodeFromLetter(substr(trim($item['tiebreak']),$countArr,1)) . ",";
						$tmpArr = substr($tmpArr,0,-1);
					}
					$so = get_text('ShotOffShort','Tournament') . $tmpArr;
				}
				elseif($item['ct']>1)
					$so = get_text('CoinTossShort','Tournament');

				$Element = $XmlDoc->createElement('Item', $so);
				$Element->setAttribute('Name', 'SO');
				$Athlete->appendChild($Element);

				if($item['rank']>$section['meta']['qualifiedNo'])
					$EndQualified = true;
			}
		}
	}

	return $XmlDoc;
}

function XmlCreateQualTeam($EventRequested='') {
	$XmlDoc = new DOMDocument('1.0', 'UTF-8');

	$TmpNode = $XmlDoc->createProcessingInstruction ("xml-stylesheet", 'type="text/xsl" href="/Common/Styles/StyleTeam.xsl" ');
	$XmlDoc->appendChild($TmpNode);

	$XmlRoot = $XmlDoc->createElement('Results');
	$XmlRoot->setAttribute('IANSEO', ProgramVersion);
	$XmlRoot->setAttribute('TS', date('Y-m-d H:i:s'));
	$XmlDoc->appendChild($XmlRoot);

	$ListHeader = NULL;

	$options=array();
	if(isset($_REQUEST["Event"]))
		$options['events'] = $_REQUEST["Event"];
	if($EventRequested) $options['events']=$EventRequested;
	if(isset($_REQUEST["MaxNum"]) && is_numeric($_REQUEST["MaxNum"]))
		$options['cutRank'] = $_REQUEST["MaxNum"];

	$family='AbsTeam';

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$rankData=$rank->getData();

//	print '<pre>';
//	print_r($rankData);
//	print '</pre>';

	if(count($rankData['sections']))
	{
		foreach($rankData['sections'] as $section)
		{
			$ListHeader = $XmlDoc->createElement('List');
			$ListHeader->setAttribute('Title', get_text($section['meta']['descr'],'','',true));
			$ListHeader->setAttribute('Columns', '13');
			$XmlRoot->appendChild($ListHeader);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['rank'],'','',true));
			$TmpNode->setAttribute('Name', 'Rank');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['countryName'],'','',true));
			$TmpNode->setAttribute('Name', 'Nation');
			$TmpNode->setAttribute('Columns', '2');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['athletes']['fields']['athlete'],'','',true));
			$TmpNode->setAttribute('Name', 'Athlete Name');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['athletes']['fields']['div'],'','',true));
			$TmpNode->setAttribute('Name', 'Division');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['athletes']['fields']['ageclass'],'','',true));
			$TmpNode->setAttribute('Name', 'Age Class');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['athletes']['fields']['class'],'','',true));
			$TmpNode->setAttribute('Name', 'Class');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['athletes']['fields']['subclass'],'','',true));
			$TmpNode->setAttribute('Name', 'Sub Class');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['score'],'','',true));
			$TmpNode->setAttribute('Name', 'Total');
			$TmpNode->setAttribute('Columns', '2');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['gold'],'','',true));
			$TmpNode->setAttribute('Name', 'Gold');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['xnine'],'','',true));
			$TmpNode->setAttribute('Name', 'XNine');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption','');
			$TmpNode->setAttribute('Name', 'SO');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$endQualified=false;

			if (count($section['items'])>0)
			{
				foreach($section['items'] as $item)
				{
				// separatore
					if ($item['rank']>$section['meta']['qualifiedNo'] && !$endQualified)
					{
						$endQualified=true;

						$XmlTeam=$XmlDoc->createElement('Team');
							$XmlTeam->setAttribute('Rank','');
							$XmlTeam->setAttribute('NationCode','');
							$XmlTeam->setAttribute('Nation','');
							$XmlTeam->setAttribute('Quanti','1');
							$XmlTeam->setAttribute('Total','');
							$XmlTeam->setAttribute('Gold','');
							$XmlTeam->setAttribute('Xnine','');
							$XmlTeam->setAttribute('SO','');
						$ListHeader->appendChild($XmlTeam);

						$XmlAthlete=$XmlDoc->createElement('Athlete');
							$XmlAthlete->setAttribute('Name', '');
							$XmlAthlete->setAttribute('Division','');
							$XmlAthlete->setAttribute('AgeClass','');
							$XmlAthlete->setAttribute('Class','');
							$XmlAthlete->setAttribute('SubClass','');
							$XmlAthlete->setAttribute('QuScore','');
						$XmlTeam->appendChild($XmlAthlete);
					}

					$XmlTeam=$XmlDoc->createElement('Team');
						$XmlTeam->setAttribute('Rank',$item['rank']);
						$XmlTeam->setAttribute('NationCode',$item['countryCode']);
						$XmlTeam->setAttribute('Nation',mb_convert_case($item['countryName'], MB_CASE_UPPER, "UTF-8") . ($item['subteam']!=0 ? ' (' . $item['subteam'] . ')' :''));
						$XmlTeam->setAttribute('Quanti',count($item['athletes']));
						$XmlTeam->setAttribute('Total',$item['score']);
						$XmlTeam->setAttribute('Gold',$item['gold']);
						$XmlTeam->setAttribute('Xnine',$item['xnine']);
						$so='';
						if($item['so']>0)  //Spareggio
						{
							$so=get_text($section['meta']['fields']['so'],'','',true);
						}
						elseif ($item['ct']>1)
						{
							$so=get_text($section['meta']['fields']['ct'],'','',true);
						}

						$XmlTeam->setAttribute('SO',$so);

					$ListHeader->appendChild($XmlTeam);

					if (count($item['athletes'])>0)
					{
						foreach ($item['athletes'] as $ath)
						{
							$XmlAthlete=$XmlDoc->createElement('Athlete');
								$XmlAthlete->setAttribute('Name', $ath['athlete']);
								$XmlAthlete->setAttribute('Division',$ath['div']);
								$XmlAthlete->setAttribute('AgeClass',$ath['ageclass']);
								$XmlAthlete->setAttribute('Class',$ath['class']);
								$XmlAthlete->setAttribute('SubClass',$ath['subclass']);
								$XmlAthlete->setAttribute('QuScore',$ath['quscore']);
							$XmlTeam->appendChild($XmlAthlete);
						}
					}
				}
			}
		}
	}

	return $XmlDoc;
}

function XmlCreateBracketIndividual($EventRequested='') {
	$XmlDoc = new DOMDocument('1.0', 'UTF-8');

	$TmpNode = $XmlDoc->createProcessingInstruction ("xml-stylesheet", 'type="text/xsl" href="/Common/Styles/StyleBracket.xsl" ');
	$XmlDoc->appendChild($TmpNode);

	$XmlRoot = $XmlDoc->createElement('Results');
	$XmlRoot->setAttribute('IANSEO', ProgramVersion);
	$XmlRoot->setAttribute('TS', date('Y-m-d H:i:s'));
	$XmlDoc->appendChild($XmlRoot);

	$ListHeader = NULL;

	$options=array();
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".") {
		$options['events'] = $_REQUEST["Event"];
	}
	if($EventRequested) $options['events']=$EventRequested;

	$rank=Obj_RankFactory::create('GridInd',$options);
	$rank->read();
	$rankData=$rank->getData();

	foreach($rankData['sections'] as $Event => $Data) {
		// New Event
		$ListHeader=$XmlDoc->createElement('List');
			$ListHeader->setAttribute('Title',$Data['meta']['eventName']);
			$ListHeader->setAttribute('Columns','4');
		$XmlRoot->appendChild($ListHeader);

		foreach($Data['phases'] as $Phase => $items) {
			// new Phase
			$XmlPhase=$XmlDoc->createElement('Phase');
				$XmlPhase->setAttribute('Title', $items['meta']['phaseName']);
				$XmlPhase->setAttribute('Columns','4');
			$ListHeader->appendChild($XmlPhase);

			// creo le colonne
			$TmpNode = $XmlDoc->createElement('Caption',get_text('Athlete') . ' 1');
				$TmpNode->setAttribute('Name', 'Athlete1');
				$TmpNode->setAttribute('Columns', '1');
			$XmlPhase->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Athlete') . ' 2');
				$TmpNode->setAttribute('Name', 'Athlete2');
				$TmpNode->setAttribute('Columns', '1');
			$XmlPhase->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Total') . ' 1');
				$TmpNode->setAttribute('Name', 'Score1');
				$TmpNode->setAttribute('Columns', '1');
			$XmlPhase->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text('Total') . ' 2');
				$TmpNode->setAttribute('Name', 'Score2');
				$TmpNode->setAttribute('Columns', '2');
			$XmlPhase->appendChild($TmpNode);

			foreach($items['items'] as $match) {
				// creo il nuovo match e leggo il secondo atleta
				$XmlMatch=$XmlDoc->createElement('Match');
				$XmlPhase->appendChild($XmlMatch);

				$Win1=($match['tie'] or ($Data['meta']['matchMode'] ? $match['setScore']>$match['oppSetScore'] : $match['score']>$match['oppScore']));
				$Win2=($match['oppTie'] or ($Data['meta']['matchMode'] ? $match['setScore']<$match['oppSetScore'] : $match['score']<$match['oppScore']));

				$Tiebreak1=array();
				$Tiebreak2=array();

				if ($match['tie']==1 || $match['oppTie']==1) {
					for ($i=0; $i<max(strlen($match['tiebreak']), strlen($match['oppTiebreak'])); ++$i) {
						$Tiebreak1[]=DecodeFromLetter($match['tiebreak'][$i]);
						$Tiebreak2[]=DecodeFromLetter($match['oppTiebreak'][$i]);
					}
				}

				$Tiebreak1=trim(join(' ',$Tiebreak1));
				if ($Tiebreak1!='') $Tiebreak1='(' . $Tiebreak1 . ')';

				$Tiebreak2=trim(join(' ',$Tiebreak2));
				if ($Tiebreak2!='') $Tiebreak2='(' . $Tiebreak2 . ')';

				// ath1
				$XmlAthlete=$XmlDoc->createElement('Athlete');
					$XmlAthlete->setAttribute('Win',$Win1);
					$XmlAthlete->setAttribute('MatchNo', $match['matchNo']);
				$XmlMatch->appendChild($XmlAthlete);

				$TmpNode=$XmlDoc->createElement('Name', $match['athlete']);
				$XmlAthlete->appendChild($TmpNode);

				$TmpNode=$XmlDoc->createElement('Country', $match['countryCode'] ? '('.$match['countryCode'] . ' - ' . $match['countryName'] . ')' : ' ');
				$XmlAthlete->appendChild($TmpNode);

				$score=' ';
				if($match['athlete']) {
					if($match['tie']==2) {
						$score=get_text('Bye');
					} elseif($Data['meta']['matchMode']) {
						$score=($match['setScore']+$match['oppSetScore'] ? $match['setScore'] : ' ' );
					} else {
						$score=($match['score']+$match['oppScore'] ? $match['score'] : ' ' );
					}
				}
				$TmpNode=$XmlDoc->createElement('Score', $score);
				$XmlAthlete->appendChild($TmpNode);

				$TmpNode=$XmlDoc->createElement('Tiebreak',$Tiebreak1);
				$XmlAthlete->appendChild($TmpNode);

				// ath2
				$XmlAthlete=$XmlDoc->createElement('Athlete');
					$XmlAthlete->setAttribute('Win',$Win2);
					$XmlAthlete->setAttribute('MatchNo',$match['oppMatchNo']);
				$XmlMatch->appendChild($XmlAthlete);

				$TmpNode=$XmlDoc->createElement('Name',$match['oppAthlete']);
				$XmlAthlete->appendChild($TmpNode);

				$TmpNode=$XmlDoc->createElement('Country', $match['oppCountryCode'] ? '('.$match['oppCountryCode'] . ' - ' . $match['oppCountryName'] . ')' : ' ');
				$XmlAthlete->appendChild($TmpNode);

				$score=' ';
				if($match['oppAthlete']) {
					if($match['oppTie']==2) {
						$score=get_text('Bye');
					} elseif($Data['meta']['matchMode']) {
						$score=($match['setScore']+$match['oppSetScore'] ? $match['oppSetScore'] : ' ' );
					} else {
						$score=($match['score']+$match['oppScore'] ? $match['oppScore'] : ' ' );
					}
				}
				$TmpNode=$XmlDoc->createElement('Score', $score);
				$XmlAthlete->appendChild($TmpNode);

				$TmpNode=$XmlDoc->createElement('Tiebreak',$Tiebreak2);
				$XmlAthlete->appendChild($TmpNode);
			}
		}
	}

	return $XmlDoc;
}

?>