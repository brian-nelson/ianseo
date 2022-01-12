<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Phases.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$XmlDoc = new DOMDocument('1.0', 'UTF-8');

	$ToFit=(isset($_REQUEST['ToFitarco']) ? $_REQUEST['ToFitarco'] : null);

	$XmlDoc = new DOMDocument('1.0', 'UTF-8');

	$TmpNode = $XmlDoc->createProcessingInstruction ("xml-stylesheet", 'type="text/xsl" href="/Common/Styles/StyleBracket.xsl" ');
	$XmlDoc->appendChild($TmpNode);


	$XmlRoot = $XmlDoc->createElement('Results');
	$XmlRoot->setAttribute('IANSEO', ProgramVersion);
	$XmlRoot->setAttribute('TS', date('Y-m-d H:i:s'));
	$XmlDoc->appendChild($XmlRoot);

	$ListHeader = NULL;

	//Genero la query
	$MyQuery = "SELECT TfEvent AS Event, EvEventName AS EventDescr, TfMatchNo, EvFinalFirstPhase, "
		. "IF(GrPhase!=0,GrPhase,1) as Phase, (GrPhase=1) as finalina, "
		. "CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as Team, CoCode as Country, TfScore, TfTie,TfTiebreak, "
		. "GrPosition, EvFinalPrintHead, FSTarget ";
	$MyQuery .= "FROM TeamFinals ";
	$MyQuery .= "INNER JOIN Events ON TfEvent=EvCode AND TfTournament=EvTournament AND EvTeamEvent=1 ";
	$MyQuery .= "INNER JOIN Grids ON TfMatchNo=GrMatchNo ";
	$MyQuery .= "LEFT JOIN Countries ON TfTeam=CoId AND TfTournament=CoTournament ";
	$MyQuery .= "LEFT JOIN FinSchedule ON TfEvent=FSEvent AND TfMatchNo=FSMatchNo AND TfTournament=FSTournament AND FSTeamEvent='1' ";
	$MyQuery.= "WHERE TfTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
	if (isset($_REQUEST['Event'])) {
		if (is_array($_REQUEST["Event"])) {
			$tmp=array();
			foreach ($_REQUEST["Event"] as $ev) {
				if(preg_match("/^[0-9A-Z]+$/i",$ev)) {
					$tmp[]="TfEvent LIKE '" . $ev . "'";
				}
			}
			if ($tmp) $MyQuery.= "AND (" . implode(" OR ",$tmp) . ") ";
		} else {
			if( preg_match("/^[0-9A-Z]+$/i",$_REQUEST["Event"]))
				$MyQuery.= "AND TfEvent LIKE '" . $_REQUEST['Event'] . "' ";
		}
	}
	$MyQuery .= "ORDER BY EvProgr ASC, EvCode, Phase ASC, TfMatchNo ASC ";
	//print $MyQuery;Exit;
	$Rs=safe_r_sql($MyQuery);
	if($Rs)
	{
		$Event='**';
		$Phase='**';

		$XmlPhase=null;
		$XmlMatch=null;

		$PhaseFull=false;

		while ($MyRow=safe_fetch($Rs))
		{
		// al cambio evento devo creare il titolo
			if ($Event!=$MyRow->Event)
			{
				if($Phase!='**' and !$PhaseFull) $ListHeader->removeChild($XmlPhase);

				$ListHeader=$XmlDoc->createElement('List');
					$ListHeader->setAttribute('Title',get_text($MyRow->EventDescr,'','',true));
					$ListHeader->setAttribute('Columns','4');
				$XmlRoot->appendChild($ListHeader);

				$Event=$MyRow->Event;
				$PhaseFull=false;
				$Phase='**';
			}

			// Quando cambia la fase creo anche gli header
			if ($Phase!=$MyRow->Phase . $MyRow->finalina)
			{
				if($Phase!='**' and !$PhaseFull) $ListHeader->removeChild($XmlPhase);

				$title='';// get_text( $MyRow->Phase . '_Phase');
				if ($MyRow->finalina==1)
				{
					$title=get_text( '1_Phase');
				}
				else
				{
					if ($MyRow->Phase==1)
						$title=get_text( '0_Phase');
					else
						$title=get_text( $MyRow->Phase . '_Phase');
				}

				$XmlPhase=$XmlDoc->createElement('Phase');
					$XmlPhase->setAttribute('Title',$title);
					$XmlPhase->setAttribute('Columns','4');
				$ListHeader->appendChild($XmlPhase);

				$Phase=$MyRow->Phase . $MyRow->finalina;

			// creo le colonne
				$TmpNode = $XmlDoc->createElement('Caption',get_text('Team') . ' 1');
					$TmpNode->setAttribute('Name', 'Team1');
					$TmpNode->setAttribute('Columns', '1');
				$XmlPhase->appendChild($TmpNode);

				$TmpNode = $XmlDoc->createElement('Caption',get_text('Team') . ' 2');
					$TmpNode->setAttribute('Name', 'Team2');
					$TmpNode->setAttribute('Columns', '1');
				$XmlPhase->appendChild($TmpNode);

				$TmpNode = $XmlDoc->createElement('Caption',get_text('Total'));
					$TmpNode->setAttribute('Name', 'Score1');
					$TmpNode->setAttribute('Columns', '1');
				$XmlPhase->appendChild($TmpNode);

				$TmpNode = $XmlDoc->createElement('Caption',get_text('Total'));
					$TmpNode->setAttribute('Name', 'Score2');
					$TmpNode->setAttribute('Columns', '2');
				$XmlPhase->appendChild($TmpNode);
			}
		// creo il nuovo match e leggo il secondo atleta
			$XmlMatch=$XmlDoc->createElement('Match');
			$XmlPhase->appendChild($XmlMatch);

			$MyRow2=safe_fetch($Rs);

			$PhaseFull=($PhaseFull or $MyRow->TfScore or $MyRow2->TfScore or ($MyRow->Country and $MyRow2->Country));

			$Win=0;
			$Win2=0;
			if ($MyRow->TfTie>0)	// vince sicuramente 1
			{
				$Win=1;
				$Win2=0;
			}
			elseif ($MyRow2->TfTie>0)	// vince sicuramente 2
			{
				$Win=0;
				$Win2=1;
			}
			else	// guardo gli score
			{
				if ($MyRow->TfScore>$MyRow2->TfScore)
				{
					$Win=1;
					$Win2=0;
				}
				elseif($MyRow2->TfScore>$MyRow->TfScore)
				{
					$Win=0;
					$Win2=1;
				}
			}

			$Tiebreak=array();
			$Tiebreak2=array();

			if ($MyRow->TfTie==1 || $MyRow2->TfTie)
			{
				for ($i=0;$i<TieBreakArrows_Team;++$i)
				{
					$Tiebreak[]=DecodeFromLetter($MyRow->TfTiebreak[$i]);
					$Tiebreak2[]=DecodeFromLetter($MyRow2->TfTiebreak[$i]);
				}
			}

			$Tiebreak=trim(join(' ',$Tiebreak));
			if ($Tiebreak!='')
				$Tiebreak='(' . $Tiebreak . ')';

			$Tiebreak2=trim(join(' ',$Tiebreak2));
			if ($Tiebreak2!='')
				$Tiebreak2='(' . $Tiebreak2 . ')';

		// ath1
			$XmlAthlete=$XmlDoc->createElement('Athlete');
				$XmlAthlete->setAttribute('Win',$Win);
				$XmlAthlete->setAttribute('MatchNo',$MyRow->TfMatchNo);
			$XmlMatch->appendChild($XmlAthlete);

				$TmpNode=$XmlDoc->createElement('Country',(is_null($MyRow->Country) ? ' ' : $MyRow->Country . ' - ' . $MyRow->Team));
				$XmlAthlete->appendChild($TmpNode);

				$TmpNode=$XmlDoc->createElement('Score',(is_null($MyRow->Country) ? ' ' : ($MyRow2->TfTie==2 ? get_text('Bye') : (($MyRow->TfScore+$MyRow->TfScore>0)?$MyRow->TfScore:' '))));
				$XmlAthlete->appendChild($TmpNode);

				$TmpNode=$XmlDoc->createElement('Tiebreak',$Tiebreak);
				$XmlAthlete->appendChild($TmpNode);

		// ath2
			$XmlAthlete=$XmlDoc->createElement('Athlete');
				$XmlAthlete->setAttribute('Win',$Win2);
				$XmlAthlete->setAttribute('MatchNo',$MyRow2->TfMatchNo);
			$XmlMatch->appendChild($XmlAthlete);

				$TmpNode=$XmlDoc->createElement('Country',(is_null($MyRow2->Country) ? ' ' : $MyRow2->Country . ' - ' . $MyRow2->Team));
				$XmlAthlete->appendChild($TmpNode);

				$TmpNode=$XmlDoc->createElement('Score',(is_null($MyRow2->Country) ? ' ' : ($MyRow->TfTie==2 ? get_text('Bye') : (($MyRow->TfScore+$MyRow->TfScore>0)?$MyRow2->TfScore:' '))));
				$XmlAthlete->appendChild($TmpNode);

				$TmpNode=$XmlDoc->createElement('Tiebreak',$Tiebreak2);
				$XmlAthlete->appendChild($TmpNode);
		}
	}

	if($Phase!='**' and !$PhaseFull) $ListHeader->removeChild($XmlPhase);

	if (is_null($ToFit))
	{
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Content-type: text/xml; charset=' . PageEncode);
		echo $XmlDoc->SaveXML();
	}
	else
		$XmlDoc->save($ToFit);
?>