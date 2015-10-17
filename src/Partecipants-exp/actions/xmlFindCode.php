<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');
	require_once 'Partecipants-exp/functions/rows.inc.php';
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');
	require_once('Common/Fun_FormatText.inc.php');

/****** Controller ******/
	$code=(isset($_REQUEST['code']) ? $_REQUEST['code'] : null);	// matricola del tizio
	$id=(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);	// id del tizio (mi serve per l'update di Entries)

	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : null);
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : null);

	if (!CheckTourSession() || is_null($code) || is_null($id) || is_null($row) || is_null($col))
	{
		print get_text('CrackError');
		exit;
	}

	$tourId=$_SESSION['TourId'];

	$error=0;

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
	//Codice Country di default
		$t=safe_r_sql("select NULLIF(ToIocCode,'') as nocCode from Tournament WHERE ToId={$_SESSION['TourId']}");
		$u=safe_fetch($t);
		$nocCode=$u->nocCode;

	// Dati che verranno salvati in Entries
		$entry=array
		(
			'EnCode'=>"''",
			'EnFirstName'=>"''",
			'EnName'=>"''",
			'EnSex'=>'0',
			'EnCtrlCode'=>"''",
			'EnDob'=>"'0000-00-00'",
			'EnIocCode'=>"'".$nocCode."'",
			'EnDivision'=>"'--'",
			'EnClass'=>"'--'",
			'EnAgeClass'=>"'--'",
			'EnSubClass'=>"'00'",
			'EnStatus'=>'0',
			'EnCountry'=>'0',
			'EnCountry2'=>'0',
			'EnCountry3'=>'0',
			'EnSubTeam'=>'0'
		);

	// Dati che verranno salvati in Countries
		$country=array
		(
			'CoTournament'=>$tourId,
			'CoCode'=>"''",
			'CoName'=>"''",
			'CoNameComplete'=>"''"
		);

	// Cerco in LookUpEntries
		$query
			= "SELECT * "
			. "FROM "
				. "LookUpEntries "
			. "LEFT JOIN "
				. "Divisions ON DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND LueDivision=DivId "
			. "LEFT JOIN "
				. "Classes ON ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND LueClass=ClId "
			. "WHERE "
				. "LueCode=" . StrSafe_DB($code) . " AND LueDefault='1' "
				. ($nocCode != '' ? ' AND LueIocCode=' . StrSafe_DB(stripslashes($nocCode)) : '' );
		$rs=safe_r_sql($query);
		//print $query;
		if ($rs)
		{
			if (safe_num_rows($rs)==1)
			{
				$myRow=safe_fetch($rs);

				$entry['EnCode']=StrSafe_DB($myRow->LueCode);
				$entry['EnFirstName']=StrSafe_DB(stripslashes(AdjustCaseTitle($myRow->LueFamilyName)));
				$entry['EnName']=StrSafe_DB(stripslashes(AdjustCaseTitle($myRow->LueName)));
				$entry['EnCtrlCode']=$myRow->LueCtrlCode;
				$entry['EnDob']=$myRow->LueCtrlCode;
				$entry['EnCtrlCode']=StrSafe_DB($entry['EnCtrlCode']);
				$entry['EnDob']=StrSafe_DB($entry['EnDob']);
				$entry['EnSex']=StrSafe_DB($myRow->LueSex);
				$entry['EnDivision']=StrSafe_DB($myRow->DivId);
				$entry['EnClass']=StrSafe_DB($myRow->ClId);
				$entry['EnAgeClass']=StrSafe_DB($myRow->ClId);
				$entry['EnSubClass']=StrSafe_DB(str_pad($myRow->LueSubClass,2,'0',STR_PAD_LEFT));
				$entry['EnStatus']=StrSafe_DB(($_SESSION['TourRealWhenFrom']>$myRow->LueStatusValidUntil && $myRow->LueStatusValidUntil!='0000-00-00' ? 5 : $myRow->LueStatus));

			/*
			 * Cerco se nel torneo aperto esiste già la nazione del tizio.
			 * Se non c'è la devo aggiungere
			 */
				$indices=array();
				if ($myRow->LueCountry2!='')
				{
					$indices=array('',2);
				}
				else
				{
					$indices=array('');
				}
				foreach ($indices as $i)
				{
					$cc=StrSafe_DB($myRow->{'LueCountry'.$i});

					$query
						= "SELECT "
							. "CoId,CoName,CoNameComplete "
						. "FROM "
							. "Countries "
						. "WHERE "
							. "CoCode=" . $cc . " AND CoTournament=" . $tourId . " ";

					$rs=safe_r_sql($query);

					if ($rs)
					{
						$count=safe_num_rows($rs);

						switch ($count)
						{
							case 0:			// La nazione non c'è quindi la aggiungo
								$country['CoCode']=mb_convert_case($cc, MB_CASE_UPPER, "UTF-8");
								$country['CoName']=StrSafe_DB(stripslashes(AdjustCaseTitle($myRow->{'LueCoShort'.$i})));
								$country['CoNameComplete']=StrSafe_DB(stripslashes(AdjustCaseTitle($myRow->{'LueCoDescr'.$i})));

								$fields=join(',',array_keys($country));
								$values=join(',',$country);

								$query
									= "INSERT INTO Countries (" . $fields . ") VALUES(" . $values . ") ";
								$rs=safe_w_sql($query);

								$entry['EnCountry'.$i]=safe_w_last_id();
								break;
							case 1:			// La nazione esiste quindi comanda lei ma se c'è ed è senza nome, sovrascrivo quest'ultimo
								$x=safe_fetch($rs);

								$entry['EnCountry'.$i]=StrSafe_DB($x->CoId);

								break;
							default:		// Errore
								$error=1;
								break;
						}
					}
					else
					{
						$error=1;
						break;
					}
				}
			}
			elseif (safe_num_rows($rs)==0)
			{
				$entry['EnCode']=StrSafe_DB($code);
			}
			else
				$error=1;
		}
		else
			$error=1;
	}
	else
		$error=1;

	$athletes=array();

	if ($error==0)
	{
		$upBody='';

		$tmp=array();
		foreach ($entry as $k=>$v)
			$tmp[]=$k . "=" . $v;

		$upBody=join(',',$tmp);

		$query
			= "UPDATE "
				. "Entries "
			. "SET "
				. $upBody . " "
			. "WHERE "
				. "EnId=" . StrSafe_DB($id) . " ";
		$rs=safe_w_sql($query);
		if(safe_w_affected_rows()) {
			safe_w_sql("update Entries set EnBadgePrinted=0 where EnId=$id");
			safe_w_sql("update Qualifications set QuBacknoPrinted=0 where QuId=$id");
		}

		$query
			= "DELETE FROM "
				. "Photos "
			. "WHERE "
				. "PhEnId=" . StrSafe_DB($id) . " ";
		$rs=safe_w_sql($query);



		//print $query;exit;
		$athletes=getAthletes($id);

		$affected=array();
		MakeIndividuals($affected);
	/*	print '<pre>';
		print_r($athletes);
		print '</pre>';*/
	}

/****** End Controller ******/

/****** Output ******/
	$xmlDoc=new DOMDocument('1.0',PageEncode);
		$xmlRoot=$xmlDoc->createElement('response');
		$xmlDoc->appendChild($xmlRoot);

		// Header
			$xmlHeader=$xmlDoc->createElement('header');
			$xmlRoot->appendChild($xmlHeader);

				$node=$xmlDoc->createElement('error',$error);
				$xmlHeader->appendChild($node);

				$node=$xmlDoc->createElement('row',intval($row));
				$xmlHeader->appendChild($node);

				$node=$xmlDoc->createElement('col',intval($col));
				$xmlHeader->appendChild($node);
		// Atleta
			if (count($athletes)==1)
			{
				$xmlAth=$xmlDoc->createElement('ath');
				$xmlRoot->appendChild($xmlAth);
					foreach ($athletes[0] as $k=>$v)
					{
						//print $k . ' --> ' . $v . '<br>';
						$node=$xmlDoc->createElement($k);
						$xmlAth->appendChild($node);
							$cdata=$xmlDoc->createCDATASection(($v!='' ? $v : '#'));
							$node->appendChild($cdata);
					}
			}
			//exit;
	header('Content-type: text/xml; charset=' . PageEncode);
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');

	print $xmlDoc->saveXML();
/****** End OUtput ******/
?>
