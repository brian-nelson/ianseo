<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=1;
	$xml='';

	$cl=(empty($_REQUEST['cl'])?'':$_REQUEST['cl']);
	$TfName=(empty($_REQUEST['TfName'])?'':$_REQUEST['TfName']);
	$RegExp=(empty($_REQUEST['RegExp'])?'':$_REQUEST['RegExp']);

	if (!IsBlocked(BIT_BLOCK_TOURDATA) and ($cl or $RegExp) and $TfName) {
		$ok=true;

		$targets=array();
		foreach($_REQUEST['tdface'] as $dist=>$face) {
			if(!$face or (empty($_REQUEST['tddiam'][$dist]) and !in_array($face, array(6,8,11)))) $ok=false;
			$targets[$face]='';
		}

		// check if the rule hits one or more div/cl
		$select
			= "SELECT "
				. "CONCAT(trim(DivId),trim(ClId)) as Ev "
			. "FROM "
				. "Divisions INNER JOIN Classes ON DivTournament=ClTournament "
			. "WHERE "
				. "CONCAT(trim(DivId),trim(ClId)) " . ($RegExp ? "RLIKE " . StrSafe_DB($RegExp) : "LIKE " . StrSafe_DB($cl))
				. " AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) .  " ";
		$rs=safe_r_sql($select);

		if (safe_num_rows($rs) and $ok) {
			$TfId=1;
			$q=safe_r_sql("select max(TfId) MaxId from TargetFaces where TfTournament={$_SESSION['TourId']}");
			if($r=safe_fetch($q)) $TfId=$r->MaxId+1;

			// get the name of the targets involved
			ksort($targets);
			$q=safe_r_sql("select TarId, TarDescr from Targets where TarId in (".implode(',', array_keys($targets)).")");
			while($r=safe_fetch($q)) $targets[$r->TarId]=get_text($r->TarDescr);


			$insert = "Insert ignore INTO TargetFaces set "
					. "TfTournament={$_SESSION['TourId']}"
					. ", TfId=$TfId"
					. ", TfDefault=" . ($_REQUEST['isDefault']?'1':'0')
					. ", TfClasses=" . StrSafe_DB($RegExp ? '' : $cl)
					. ", TfRegExp=" . StrSafe_DB($RegExp)
					. ", TfName=" . StrSafe_DB($_REQUEST['TfName'])
					;
			$xml.='<tfid>' . $TfId . '</tfid>';
			$xml.='<default>' . ($_REQUEST['isDefault'] ? get_text('Yes') : '<![CDATA[]]>') . '</default>';
			$xml.='<tfname>' . $_REQUEST['TfName'] . '</tfname>';
			foreach($_REQUEST['tdface'] as $dist => $face) {
				$insert.= ", TfT$dist = " . intval($face);
				$insert.= ", TfW$dist = " . intval($_REQUEST['tddiam'][$dist] );
				$xml.= '<face>'.$targets[$face].'</face>';
				$xml.= '<diam>'.intval($_REQUEST['tddiam'][$dist]).' cm</diam>';
			}

			$rs=safe_w_sql($insert);
			$Errore=0;
		}
	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<cl><![CDATA[' . $cl . ']]></cl>';
	print '<reg><![CDATA[' . $RegExp . ']]></reg>';
	echo $xml;

	print '</response>';
?>