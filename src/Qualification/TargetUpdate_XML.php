<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclQualification, AclReadOnly, false);

	$Errore=0;
	$XmlOut="";
	$MyQuery = "";

	if(isset($_REQUEST["Session"]) && isset($_REQUEST["Hour"]) && preg_match("/^[1-9]{1}$/i",$_REQUEST["Session"]) && preg_match("/^[0-9]{2}:[0-9]{2}$/i",$_REQUEST["Hour"]))
	{
		$MyQuery = "SELECT QuTargetNo, (QuTimeStamp > " . StrSafe_DB(date("Y-m-d") . " " . $_REQUEST["Hour"]. ":00") . ") as isUpdated"
        . " FROM Qualifications"
        . " INNER JOIN Entries ON QuId=EnId"
        . " WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuSession=" . StrSafe_DB($_REQUEST["Session"]) . " AND EnStatus <=1 "
        . " ORDER BY QuTargetNo";
		//echo $MyQuery; exit();
		$Rs=safe_r_sql($MyQuery);
		if(safe_num_rows($Rs)>0)
		{
			$OldTarget='';
			$cntOk=0;
			$cntKo=0;
			while($MyRow=safe_fetch($Rs))
			{
				if($OldTarget != substr($MyRow->QuTargetNo,0,-1) && $OldTarget!='')
				{
					$XmlOut .= "<target>";
					$XmlOut .= "<no>" . substr($OldTarget,1) . "</no>";
					$XmlOut .= "<status>" . ($cntKo==0 ? '0' : ($cntOk==0 ? '2' : '1')) . "</status>";
					$XmlOut .= "</target>";
					$cntOk=0;
					$cntKo=0;
				}
				if($MyRow->isUpdated==1)
					$cntOk++;
				else
					$cntKo++;
				$OldTarget = substr($MyRow->QuTargetNo,0,-1);
			}
			$XmlOut .= "<target>";
			$XmlOut .= "<no>" . substr($OldTarget,1) . "</no>";
			$XmlOut .= "<status>" . ($cntKo==0 ? '0' : ($cntOk==0 ? '2' : '1')) . "</status>";
			$XmlOut .= "</target>";

		}
		else
		{
			$Errore=1;
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
	print $XmlOut;
	print '</response>';


?>