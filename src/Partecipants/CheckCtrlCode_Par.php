<?php
/*
													- UpdateCtrlCode.php -
	Controlla il codice fiscale dei tizio in Partecipants.php
	Decide anche come gestire le tendine delle classi
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');

	if (!CheckTourSession() || !isset($_REQUEST['d_e_EnCtrlCode']) || !isset($_REQUEST['d_e_EnSex'])) {
		print get_text('CrackError');
		exit;
	}
	checkACL(AclParticipants, AclReadOnly, false);

	$Errore=0;
	$AgeClass = '';
	$Classes= '';

	$ctrlCode=ConvertDateLoc($_REQUEST['d_e_EnCtrlCode']);

	if ($ctrlCode!==false)
	{
		$dob='0000-00-00';
		$AgeClass = '';
		$Classes= '';

		if (!empty($ctrlCode))
		{
			list($__yy,$mm,$dd)=explode('-',$ctrlCode);


		// Ultime 2 cifre dell'anno
			$__yy = substr($__yy,-2);

		// Prime 2 cifre dell'anno
			$yy__ = '19';

		/*
			Pivot per discriminare 19xx e 20xx
		*/
			if ($__yy >= '00' && $__yy<='20')
				$yy__='20';

			$yy=intval($yy__ . $__yy);

			//$year = date('Y') - $yy;
			$year=date('Y',$_SESSION['ToWhenFromUTS']) - $yy;

			// Estraggo l'ageclass dalla tabella
			$Select
				= "SELECT ClId,ClValidClass "
				. "FROM Classes "
				. "WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClAgeFrom<=" . StrSafe_DB($year) . " AND ClAgeTo>=" . StrSafe_DB($year) . " ";
				//. "AND ClSex = " . StrSafe_DB($Sex) . " ";
			$cond="AND ClSex = " . StrSafe_DB($_REQUEST['d_e_EnSex']) . " ";
			$condUnisex="AND ClSex = -1 ";
			$RsCl = safe_r_sql($Select.$cond);

			//print $Select.$cond;exit;
			if (safe_num_rows($RsCl)==1)
			{
				$MyRow=safe_fetch($RsCl);
				$AgeClass=$MyRow->ClId;
				$Classes=$MyRow->ClValidClass;
			}
			elseif (safe_num_rows($RsCl)==0)
			{
				$RsCl = safe_r_sql($Select.$condUnisex);
				if (safe_num_rows($RsCl)==1)
				{
					$Row=safe_fetch($RsCl);
					$AgeClass = $Row->ClId;
					$Classes=$MyRow->ClValidClass;
				}
			}
		}
	}
	else
	{
		$Errore=1;
	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<ageclass><![CDATA[' . $AgeClass . ']]></ageclass>';
	print '<classes><![CDATA[' . $Classes . ']]></classes>';
	print '</response>';
?>

