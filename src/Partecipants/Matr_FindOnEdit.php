<?php
/*
													- Matr_FindOnEdit.php -
	La pagina riceve la 'Matr' che � la matricola da cercare.
	Il completamento della riga avviene solo se la query ritorna un risultato unico.
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Fun_Partecipants.local.inc.php');

	if (!isset($_REQUEST['Matr']) || !CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
	checkACL(AclParticipants, AclReadWrite, false);

	$nocCode='';
	if(isset($_REQUEST['Noc']))
		$nocCode = $_REQUEST['Noc'];

	$Errore = 0;
	$XML='';

	$Code2Save = ($_REQUEST['Matr']);
	$Name2Save = "";
	$FirstName2Save = "";
	$Sex2Save = "0";
	$CtrlCode2Save = "";
	$Dob2Save="";
	$Tournament2Save = ($_SESSION['TourId']);
	$Division2Save = "";
	$Class2Save = "";
	$AgeClass2Save = "";
	$SubClass2Save = "";
	$Status2Save = "0";

	$IdCountry2Save = "0";
	$Country2Save = "";
	$Nation2Save = "";
	$NationComplete2Save = "";

	$SecondIdCountry2Save = "0";
	$SecondCountry2Save = "";
	$SecondNation2Save = "";
	$SecondNationComplete2Save = "";

	if($nocCode) {
		$Select
			= "SELECT * "
			. "FROM LookUpEntries "
			. "LEFT JOIN Divisions ON DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND LueDivision=DivId "
			. "LEFT JOIN Classes ON ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND LueClass=ClId "
			. "WHERE LueCode=" . StrSafe_DB(stripslashes($_REQUEST['Matr'])) . " AND LueDefault='1' "
			. ' AND LueIocCode=' . StrSafe_DB(stripslashes($nocCode)) ;

		$Rs=safe_r_sql($Select);

		if (safe_num_rows($Rs)==1) {
			$MyRow=safe_fetch($Rs);

			// campi che non riguardano la nazione
			$Name2Save = stripslashes($MyRow->LueName);
			$FirstName2Save = stripslashes($MyRow->LueFamilyName);
			$CtrlCode2Save = $MyRow->LueCtrlCode;
			$Sex2Save = $MyRow->LueSex;
			$Division2Save = $MyRow->DivId;
			$Class2Save = $MyRow->ClId;
			$AgeClass2Save = $MyRow->ClId;
			$SubClass2Save = str_pad($MyRow->LueSubClass,2,'0',STR_PAD_LEFT);
			$Status2Save = ($_SESSION['TourRealWhenTo']>$MyRow->LueStatusValidUntil && $MyRow->LueStatusValidUntil!='0000-00-00' ? 5 : $MyRow->LueStatus);

			// dob
			$Dob2Save=$MyRow->LueCtrlCode;

			// ora creao il dob nel formato localizzato
			if ($Dob2Save!='') {
				$tmp=explode('-',$Dob2Save);
				//print_r($tmp);
				$mkDob=mktime(0,0,0,$tmp[1],$tmp[2],$tmp[0]);
				$Dob2Save=date(get_text('DateFmt'),$mkDob);
			}

			// campi nazione
			$Country2Save = (stripslashes($MyRow->LueCountry));
			$SecondCountry2Save = (stripslashes($MyRow->LueCountry2));

			/*
				Cerco il codice di nazione trovato nella tabella di lookup.
				Se non lo trovo, metto a -1 l'id altrimenti
				prendo i dati dalla tabella delle nazioni
			*/
			$indices=array('',2);
			foreach ($indices as $i) {
				$cc=StrSafe_DB(($i=='' ? $Country2Save : $SecondCountry2Save));

				$SelCountry
					= "SELECT CoId,CoName,CoNameComplete "
					. "FROM Countries "
					. "WHERE CoCode=" . ($cc) . " AND CoTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
				$RsC=safe_r_sql($SelCountry);

				/*
					Il codice non esiste quindi comanda la tabella di lookup.
					Aggiungo a Countries
				*/
				if (safe_num_rows($RsC)==0) {
					if ($i=='') {
						$Nation2Save = stripslashes($MyRow->{'LueCoShort'.$i});
						$NationComplete2Save = stripslashes($MyRow->{'LueCoDescr'.$i});
						$IdCountry2Save = -1;
					} else {
						$SecondNation2Save = stripslashes($MyRow->{'LueCoShort'.$i});
						$SecondNationComplete2Save = stripslashes($MyRow->{'LueCoDescr'});
						$SecondIdCountry2Save = -1;
					}
				} elseif (safe_num_rows($RsC)==1) {
					// la nazione esiste già per cui comanda lei
					$RowC=safe_fetch($RsC);
					if ($i=='') {
						$IdCountry2Save = ($RowC->CoId);
						$Nation2Save = stripslashes($RowC->CoName);
						$NationComplete2Save = stripslashes($RowC->CoNameComplete);
					} else {
						$SecondIdCountry2Save = ($RowC->CoId);
						$SecondNation2Save = stripslashes($RowC->CoName);
						$SecondNationComplete2Save = stripslashes($RowC->CoNameComplete);
					}
				} else 	{
					// l'indice unico è andato a puttane! Sono cazzi acidi
					$Errore = 1;
				}
			}


		}
	}

	$AllClasses='';
	if ($Errore==0) {
		// Ritorno l'elenco di tutte le classi
		$Select
			= "SELECT ClId FROM Classes WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ClViewOrder ASC ";
		$Rs=safe_r_sql($Select);
		while ($Row=safe_fetch($Rs)) {
			$AllClasses.= '<cl_id>' . $Row->ClId . '</cl_id>';
		}
	}

	if (!debug)
	{
		header('Content-Type: text/xml');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
	}
	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<code><![CDATA[' . (strlen(trim($Code2Save))!=0 ? ($Code2Save) : '') . ']]></code>';
	print '<name><![CDATA[' . (strlen(trim($Name2Save))!=0 ? ($Name2Save) : '') . ']]></name>';
	print '<firstname><![CDATA[' . (strlen(trim($FirstName2Save))!=0 ? ($FirstName2Save) : '') . ']]></firstname>';
	print '<ctrl_code><![CDATA[' . (strlen(trim($CtrlCode2Save))!=0 ? ($CtrlCode2Save) : '') . ']]></ctrl_code>';
	print '<dob><![CDATA[' . (strlen(trim($Dob2Save))!=0 ? ($Dob2Save) : '') . ']]></dob>';
	print '<sex><![CDATA[' . (strlen(trim($Sex2Save))!=0 ? ($Sex2Save) : '') . ']]></sex>';
	print '<division><![CDATA[' . (strlen(trim($Division2Save))!=0 ? ($Division2Save) : '') . ']]></division>';
	print '<class><![CDATA[' . (strlen(trim($Class2Save))!=0 ? ($Class2Save) : '') . ']]></class>';
	print '<ageclass><![CDATA[' . (strlen(trim($Class2Save))!=0 ? ($Class2Save) : '') . ']]></ageclass>';
	print '<subclass><![CDATA[' . (strlen(trim($SubClass2Save))!=0 ? ($SubClass2Save) : '') . ']]></subclass>';
	print '<country><![CDATA[' . (strlen(trim($Country2Save))!=0 ? ($Country2Save) : '') . ']]></country>';
	print '<idcountry><![CDATA[' . (strlen(trim($IdCountry2Save))!=0 ? ($IdCountry2Save) : '') . ']]></idcountry>';
	print '<nation><![CDATA[' . (strlen(trim($Nation2Save))!=0 ? ($Nation2Save) : '') . ']]></nation>';
	print '<country2><![CDATA[' . (strlen(trim($SecondCountry2Save))!=0 ? ($SecondCountry2Save) : '') . ']]></country2>';
	print '<idcountry2><![CDATA[' . (strlen(trim($SecondIdCountry2Save))!=0 ? ($SecondIdCountry2Save) : '') . ']]></idcountry2>';
	print '<nation2><![CDATA[' . (strlen(trim($SecondNation2Save))!=0 ? ($SecondNation2Save) : '') . ']]></nation2>';
	print '<status><![CDATA[' . (strlen(trim($Status2Save))!=0 ? ($Status2Save) : '') . ']]></status>';
	print '</response>';
?>
