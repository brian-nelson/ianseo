<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once dirname(dirname(dirname(__FILE__))) . '/Qualification/Fun_Qualification.local.inc.php';
	require_once dirname(dirname(dirname(__FILE__))) . '/Partecipants/Fun_Partecipants.local.inc.php';
	require_once 'Partecipants-exp/common/config.inc.php';

/****** Controller ******/
	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$error=0;
	$EnAthlete=1;
	
	$tourId=StrSafe_DB($_SESSION['TourId']);
	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		$id=(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);
		$ageClass=(isset($_REQUEST['age_class']) ? $_REQUEST['age_class'] : null);
		$class=(isset($_REQUEST['class']) ? $_REQUEST['class'] : null);

		if (is_null($id) || is_null($ageClass) || is_null($class))
		{
			print get_text('CrackError');
			exit;
		}
		$rs=null;
	// verifico che l'ageclass esista (se è diversa da '--')
		if (($ageClass)!='--' && $ageClass!='')
		{
			$query
				= "SELECT * FROM Classes WHERE ClTournament=" . $tourId . " AND (ClId)=(" . StrSafe_DB($ageClass) . ") ";
			$rs=safe_r_sql($query);
			//print $query;exit;

			if (!$rs)
				$error=1;
		}

		if ($error==0)
		{
			if ($rs)
			{
				if (safe_num_rows($rs)==1)
				{
				// verifico che la classe sia tra quelle ammesse (se diversa da '')
					$row=safe_fetch($rs);
					//if (!(trim($class)=='--' || (trim($class)!='--' && strpos(trim($row->ClValidClass),trim($class))!==false)))
					if ( !(($class)=='--' || (($class)!='--' && in_array(($class),explode(',',$row->ClValidClass))) ) )
					{
						//print 'qui';
						$error=1;
					}
				}
				else
					$error=1;
			}
		}
		//print $class;exit;
		if ($error==0)
		{
			$recalc=false;
			$indFEventOld=$teamFEventOld=$countryOld=$divOld=$clOld=$zeroOld=null;
			$indFEvent=$teamFEvent=$country=$div=$cl=$zero=null;
		
		// se la vecchia classe è diversa ricalcolo spareggi,abs ind e squadre per la vecchia e la nuova
			$query= "SELECT EnClass FROM Entries WHERE EnId=" . StrSafe_DB($id) . " AND EnClass<>" . StrSafe_DB($class) . " ";
			//print $query;exit;
			$rs=safe_r_sql($query);
			if ($rs && safe_num_rows($rs)==1)
			{
				$recalc=true;
			
			// prendo le vecchie impostazioni
				$x=Params4Recalc($id);
				if ($x!==false)
				{
					list($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$zeroOld)=$x;
				}
			}
		
			
			$query
				= "UPDATE Entries SET "
				. "EnClass=" . StrSafe_DB($class) . ","
				. "EnAgeClass=" . StrSafe_DB($ageClass) . " "
				. "WHERE EnId=" . StrSafe_DB($id) . " ";
			$rs=safe_w_sql($query);
			//print $query;exit;
			if (!$rs)
			{
				$error=1;
			}
			else
			{	
				
			// devo capire se il tipo è atleta oppure no. fanculo!
				$EnAthlete=1;
				$query
					= "SELECT EnDivision,EnClass FROM Entries WHERE EnId=" . StrSafe_DB($id) . " ";
				$rs=safe_r_sql($query);
				$tmp=null;
				if ($rs && safe_num_rows($rs)==1)
				{
					$tmp=safe_fetch($rs);
					
					// recupero dell'indicazione se atleta in div e clas con la div e clas di gara
					$query
						= "SELECT"
						. " DivAthlete and ClAthlete as Athlete  "
						. "FROM "
						. " Divisions "
						. " INNER JOIN Classes on DivTournament=ClTournament "
						. "WHERE "
						. " DivTournament={$_SESSION['TourId']} "
						. " AND DivId=". StrSafe_DB($tmp->EnDivision)
						. " AND ClID=" . StrSafe_DB($tmp->EnClass);
						//print $query;exit;
						
					$t=safe_r_sql($query);
					$EnAthlete = intval(($u=safe_fetch($t) and $u->Athlete));
					
				}	
				
				$Update
					= "UPDATE Entries SET "
					. "EnAthlete=" . $EnAthlete . " "
					. "WHERE EnId=" . StrSafe_DB($id) . " ";
					//print $Update;Exit;
				$RsUp=safe_w_sql($Update);
				if (!$RsUp)
				{
					$Errore=1;
				}
				else
				{
					if ($recalc)
					{
						$x=Params4Recalc($id);
						if ($x!==false)
						{
							list($indFEvent,$teamFEvent,$country,$div,$cl,$zero)=$x;
						}
						
					// ricalcolo il vecchio e il nuovo
						RecalculateShootoffAndTeams($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$zeroOld);						
						RecalculateShootoffAndTeams($indFEvent,$teamFEvent,$country,$div,$cl,$zero);
						
					// rank di classe x tutte le distanze
						$q="SELECT ToNumDist FROM Tournament WHERE ToId={$_SESSION['TourId']}";
						$r=safe_r_sql($q);
						$tmpRow=safe_fetch($r);
						for ($i=0; $i<$tmpRow->ToNumDist;++$i)
						{
							CalcQualRank($i,$divOld.$clOld);
							CalcQualRank($i,$div.$cl);
						}
					// individuali abs	
						MakeIndAbs();
					}
				}
			}
		}
	}
	else
		$error=1;
	//exit;
/****** End Controlloer ******/

/****** Output ******/
	$xmlDoc=new DOMDocument('1.0',PageEncode);
		$xmlRoot=$xmlDoc->createElement('response');
		$xmlDoc->appendChild($xmlRoot);

	// Header
		$xmlHeader=$xmlDoc->createElement('header');
		$xmlRoot->appendChild($xmlHeader);

			$node=$xmlDoc->createElement('error',$error);
			$xmlHeader->appendChild($node);
			
			$node=$xmlDoc->createElement('row',$_REQUEST['row']);
			$xmlHeader->appendChild($node);
			
			$node=$xmlDoc->createElement('col',$_REQUEST['col']);
			$xmlHeader->appendChild($node);
			
		$xmlAth=$xmlDoc->createElement('ath',$EnAthlete);
			$xmlRoot->appendChild($xmlAth);
			

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();
/****** End Output ******/
?>