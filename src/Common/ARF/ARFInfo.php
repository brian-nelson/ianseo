<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/MySql2XML.class.php');
	
// documento xml
	$xmlDoc=new DOMDocument('1.0','UTF-8');			
	$xmlRoot=$xmlDoc->createElement('response');
	$xmlDoc->appendChild($xmlRoot);
	
	$xmlVers=$xmlDoc->createElement('versions');
	$xmlRoot->appendChild($xmlVers);
	
	$xmlVerInfo=$xmlDoc->createElement('arf_info','20081119.01');
	$xmlVers->appendChild($xmlVerInfo);
	
	$xmlVerOut=$xmlDoc->createElement('arf_output','20081119.01');
	$xmlVers->appendChild($xmlVerOut);
	
	$xmlVerIn=$xmlDoc->createElement('arf_info','20081119.01');
	$xmlVers->appendChild($xmlVerIn);
	
// Estraggo l'id dato il codice gara
	$ToId=getIdFromCode($_REQUEST['Code']);
	
	if ($ToId!=0)
	{
	/*
	 * - info gara
	 * - numero di sessioni nella qualificazione e numero di distanze nella qualificazione
	 */ 
		/*$query
			= "SELECT "
				. "'Output' AS `header->direction`,"
				. " ToCode AS `header->tour_code`,"
				. "ToName AS `header->tour_name`,"
				. "IF(TtElabTeam=1 || TtElabTeam=2,'1',IF(INSTR(TtName,'Indoor')=0,'0','2' ))  AS `header->type`, "
				. "ToNumSession AS `info->qualification->number_sessions`,"
				. "TtNumDist AS `info->qualification->number_distances` "
			. "FROM "
				. "Tournament INNER JOIN Tournament*Type ON ToType=TtId "
			. "WHERE "
				. "ToId=" . StrSafe_DB($ToId) . " ";*/
		$query
			= "SELECT "
				. "'Output' AS `header->direction`,"
				. " ToCode AS `header->tour_code`,"
				. "ToName AS `header->tour_name`,"
				. "IF(ToElabTeam=1 || ToElabTeam=2,'1',IF(INSTR(ToTypeName,'Indoor')=0,'0','2' ))  AS `header->type`, "
				. "ToNumSession AS `info->qualification->number_sessions`,"
				. "ToNumDist AS `info->qualification->number_distances` "
			. "FROM "
				. "Tournament  "
			. "WHERE "
				. "ToId=" . StrSafe_DB($ToId) . " ";
		//print $query;exit;		
		$rs=safe_r_sql($query);
		
		if (safe_num_rows($rs)==1)
		{
			$mysql2xml=new MySql2XML($rs,$xmlDoc,$xmlRoot);
			$xmlDoc=$mysql2xml->getXmlDoc();
			
			/*
			 * gironi delle eliminatorie
			 */
			
		// cerco eventi con 2 gironi
			$rounds=0;
			
			$query
				= "SELECT "
					. "EvCode "
				. "FROM "
					. "Events "
				. "WHERE "
					. "EvTournament=" . StrSafe_DB($ToId) . " "
					. "AND EvElim1!=0 AND EvElim2!=0 ";
			$rs=safe_r_sql($query);
			
			if (safe_num_rows($rs)>0)
			{
				$rounds=2;
			}	
		// cerco eventi con un girone solo
			else	
			{
				$query
					= "SELECT "
						. "EvCode "
					. "FROM "
						. "Events "
					. "WHERE "
						. "EvTournament=" . StrSafe_DB($ToId) . " "
						. "AND EvElim1=0 AND EvElim2!=0 ";
				$rs=safe_r_sql($query);
				
				if (safe_num_rows($rs)>0)
				{
					$rounds=1;
				}	
			}
			
			$infos=$xmlDoc->getElementsByTagName('info');
			
			$xmlElim=$xmlDoc->createElement('elimination');
			$infos->item(0)->appendChild($xmlElim);
			
			$tmp=$xmlDoc->createElement('number_rounds',$rounds);
			$xmlElim->appendChild($tmp);
			
		// eventi delle finali ind
			$query
				= "SELECT "
					. "EvCode AS `event->code`,"
					. "EvEventName AS `event->descr`,"
					. "EvFinalFirstPhase AS `event->start_phase` "
				. "FROM "
					. "Events "
				. "WHERE "
					. "EvTournament=" . StrSafe_DB($ToId) .  " AND EvTeamEvent=0 "
				. "ORDER BY "
					. "EvCode ASC ";
			$rs=safe_r_sql($query);

			$xmlInd=null;
			
			/* CHE SENSO HA QUESTO IF??? UNA QUERY HA SEMPRE SUCCESSO ANCHE SE E' VUOTA!!! */
			if ($rs)
			{
				$xmlInd=$xmlDoc->createElement('individual_final');
				$infos->item(0)->appendChild($xmlInd);
				
				$xmlEvents=$xmlDoc->createElement('events');
				$xmlInd->appendChild($xmlEvents);
				
				$mysql2xml=new MySql2XML($rs,$xmlDoc,$xmlEvents);
				
			// Scheduling delle finali ind
				$query
					= "SELECT DISTINCT "
						. "CONCAT(FSScheduledDate,' ',FSScheduledTime) AS `schedule` "
					. "FROM "
						. "FinSchedule "
					. "WHERE "
						. "FSTournament=" . StrSafe_DB($ToId) . " AND FSTeamEvent='0' "
					. "ORDER BY "
						. "CONCAT(FSScheduledDate,' ',FSScheduledTime) ASC ";
				$rs=safe_r_sql($query);
				
				/* IDEM COME PRIMA: UNA QUERY HA SEMPRE SUCESSO ANCHE SE VUOTA */
				if ($rs)
				{
					$xmlScheduling=$xmlDoc->createElement('scheduling');
					$xmlInd->appendChild($xmlScheduling);
					
					$mysql2xml=new MySql2XML($rs,$xmlDoc,$xmlScheduling);
					//$xmlDoc=$mysql2xml->getXmlDoc();
				}
			}
			
		// eventi delle finali team
			$query
				= "SELECT "
					. "EvCode AS `event->code`,"
					. "EvEventName AS `event->descr`,"
					. "EvFinalFirstPhase AS `event->start_phase` "
				. "FROM "
					. "Events "
				. "WHERE "
					. "EvTournament=" . StrSafe_DB($ToId) .  " AND EvTeamEvent=1 "
				. "ORDER BY "
					. "EvCode ASC ";
			$rs=safe_r_sql($query);

			$xmlTeam=null;
			
			/* ANCHE QUI COME PRIMA */
			if ($rs)
			{
				$xmlTeam=$xmlDoc->createElement('team_final');
				$infos->item(0)->appendChild($xmlTeam);
				
				$xmlEvents=$xmlDoc->createElement('events');
				$xmlTeam->appendChild($xmlEvents);
				
				$mysql2xml=new MySql2XML($rs,$xmlDoc,$xmlEvents);
				
			// Scheduling delle finali team
				$query
					= "SELECT DISTINCT "
						. "CONCAT(FSScheduledDate,' ',FSScheduledTime) AS `schedule` "
					. "FROM "
						. "FinSchedule "
					. "WHERE "
						. "FSTournament=" . StrSafe_DB($ToId) . " AND FSTeamEvent='1' "
					. "ORDER BY "
						. "CONCAT(FSScheduledDate,' ',FSScheduledTime) ASC ";
				$rs=safe_r_sql($query);
				
				/* DI NUOVO... */
				if ($rs)
				{
					$xmlScheduling=$xmlDoc->createElement('scheduling');
					$xmlTeam->appendChild($xmlScheduling);
					
					$mysql2xml=new MySql2XML($rs,$xmlDoc,$xmlScheduling);
					//$xmlDoc=$mysql2xml->getXmlDoc();
				}
			}		
		
		}	 
	}
	
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);
	
	print $xmlDoc->SaveXML();
?>