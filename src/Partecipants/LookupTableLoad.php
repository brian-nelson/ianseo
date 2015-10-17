<?php
// @apache_setenv('no-gzip', 1);
// @ini_set('zlib.output_compression', 0);
// @ini_set('implicit_flush', 1);
// for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
// ob_implicit_flush(1);

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');

CheckTourSession(true);

if(!empty($_REQUEST['PrevPhoto']) and !empty($_REQUEST['EnId']) and !empty($_REQUEST['PhEnId'])) {
	safe_w_sql("insert into Photos (select ".intval($_REQUEST['EnId']).",PhPhoto, PhPhotoEntered from Photos where PhEnId=".intval($_REQUEST['PhEnId']).")");
}

$DataSource="";

$PAGE_TITLE=get_text('MsgSyncronize','Tournament');

include('Common/Templates/head.php');

if(empty($_REQUEST["Download"]) && empty($_FILES["UploadedFile"])) {
	// Asks for what is to update/download
?>
	<form name="FrmDownload" method="POST" action="" enctype="multipart/form-data">
	<table class="Tabella" style="width:600px">
	<tr><th class="Title" colspan="4"><?php print get_text('MsgSyncronize','Tournament');?></th></tr>
	<tr>
		<th class="SubTitle" nowrap="nowrap"><?php print get_text('IOCcode','Tournament');?></th>
		<th class="SubTitle" nowrap="nowrap"><?php print get_text('Photo','Tournament');?></th>
		<th class="SubTitle" nowrap="nowrap"><?php print get_text('Flags','Tournament');?></th>
		<th class="SubTitle" nowrap="nowrap"><?php print get_text('MsgSyncNet','Tournament');?></th>
	</tr>
	<?php
	$t=safe_r_sql("select * from LookUpPaths where LupIocCode in (select ToIocCode from Tournament where ToId={$_SESSION['TourId']} union select distinct EnIocCode from Entries where EnTournament={$_SESSION['TourId']})");
	while($u=safe_fetch($t)) {
		if($u->LupIocCode=='FITA' and !file_exists($CFG->DOCUMENT_PATH.'/Modules/IanseoTeam/LookupFitaId.php')) continue;
		echo '<tr>'
			. '<td class="Center" nowrap="nowrap">' . $u->LupIocCode . '</td>'
			. '<td class="Center" nowrap="nowrap">'.($u->LupPhotoPath?'<input name="Photo['.$u->LupIocCode.']" type="checkbox">':'&nbsp;').'</td>'
			. '<td class="Center" nowrap="nowrap">'.($u->LupFlagsPath?'<input name="Flags['.$u->LupIocCode.']" type="checkbox">':'&nbsp;').'</td>'
			. '<td nowrap="nowrap">'.($u->LupPath?'<input name="Download['.$u->LupIocCode.']" type="checkbox">&nbsp;<a href="'.$u->LupPath.'" class="Link">'.$u->LupPath.'</a>':'&nbsp;').'</td>'
			. '</tr>';
	}
	?>
	<tr>
		<th class="SubTitle" colspan="4"><?php print get_text('MsgSyncFile','Tournament');?></th>
	</tr>
	<tr>
		<td class="Center" colspan="4"><input name="UploadedFile" type="file" size="20"></td>
	</tr>
	<tr>
	<td colspan="4" class="Center"><input type="submit" value="<?php echo get_text('MsgSyncronize','Tournament');?>"></td>
	</tr>
	<tr><th colspan="4"><?php echo '<input type="checkbox" name="PrevPhoto"'.(empty($_REQUEST['PrevPhoto'])? '' : ' checked="checked"').' onclick="window.location.href=\''.basename(__FILE__).(empty($_REQUEST['PrevPhoto'])? '?PrevPhoto=on' : '').'\'">'.get_text('PreviousPhotos','Tournament'); ?></th></tr>
	<?php
	// check if there are some photos in this tournament...
	if(!empty($_REQUEST['PrevPhoto'])) {
		$q=safe_r_SQL("select * from Entries
			inner join (select PhEnId OtPhEnId, EnCode OtEnCode, EnIocCode OtEnIocCode, EnTournament OtEnTournament, ToCode OtToCode, ToName OtToName
				from Entries
				inner join Photos on PhEnId=EnId
				inner join Tournament on EnTournament=ToId) Ot on EnCode=OtEnCode and EnIocCode=OtEnIocCode
			where EnId not in (select PhEnId from Photos inner join Entries on PhEnId=EnId and EnTournament={$_SESSION['TourId']})
		    and EnTournament={$_SESSION['TourId']}
			order by EnCode, OtPhEnId desc");
		while($r=safe_fetch($q)) {
			echo '<tr>';
			echo '<td><a href="'.go_get(array('EnId'=>$r->EnId, 'PhEnId'=>$r->OtPhEnId)).'">'.$r->EnFirstName.' '.$r->EnName.'</a></td>';
			echo '<td><img src="../Partecipants-exp/common/photo.php?id='.$r->OtPhEnId.'&mode=y&val=50"></td>';
			echo '<td>'.$r->OtToCode.'</td>';
			echo '<td>'.$r->OtToName.'</td>';
			echo '</tr>';
		}
	}
	?>
	</table>
	</form>
<?php
} else {

	//ini_set('memory_limit', '512M');
	set_time_limit(0);
	$CFG->TRACE_QUERRIES=false;

	require_once('Common/Lib/Fun_DateTime.inc.php');
	$IocToUpdate=array();

	// Check if a file has been uploaded
	if(!empty($_FILES["UploadedFile"]["name"]) and strlen($_FILES["UploadedFile"]["name"]) and $_FILES["UploadedFile"]["error"]==UPLOAD_ERR_OK) {
		$t=safe_r_sql("
			select IFNULL(LupIocCode,ToIocCode) AS LupIocCode,ifnull(LupFors,'') AS LupFors
			from
			Tournament
				LEFT JOIN LookUpPaths
			ON ToIocCode=LupIocCode AND ToId={$_SESSION['TourId']}
			where
				ToId={$_SESSION['TourId']}
		");
		$u=safe_fetch($t);
		@ob_end_flush();

		echo str_repeat(' ',1500);
		flush();

		echo '<div style="margin-top:1em;"><b>'.$_FILES["UploadedFile"]["name"].'</b><br/>';
		DoLookupEntries($u, $_FILES["UploadedFile"]["tmp_name"]);
		unlink($_FILES["UploadedFile"]["tmp_name"]);
		echo '</div>';
	}

	// Check IOC Codes to download!!!
	$t=safe_r_sql("select * from LookUpPaths");
	while($u=safe_fetch($t)) {
		$head='<div style="margin-top:1em;"><b>'.$u->LupIocCode.'</b><br/>';
		if($u->LupPath and !empty($_REQUEST["Download"][$u->LupIocCode])) {
			echo $head;
			$head='';
			DoLookupEntries($u);
		}
		if($u->LupPhotoPath and !empty($_REQUEST["Photo"][$u->LupIocCode])) {
			echo $head;
			$head='';
			DoLookupPhoto($u);
		}
		if($u->LupFlagsPath and !empty($_REQUEST["Flags"][$u->LupIocCode])) {
			echo $head;
			$head='';
			DoLookupFlags($u);
		}
		if(!$head) echo '</div>';
	}
	echo '<div>'.get_text('EndUpdate', 'Tournament').'</div>';
}

include('Common/Templates/tail.php');

function DoLookupEntries($u, $file='') {
	global $CFG;

	echo get_text('MsgLookup1','Tournament').'<br/>';
	flush();
	//ob_flush();

	$DataSource = file_get_contents($file ? $file : $u->LupPath);
	if (!$DataSource) {
    	echo "No Database<br>\n";
    	return;
	}

	// checks if it is gzipped
	if($unzipped=@gzuncompress($DataSource)) {
		$DataSource=$unzipped;
	}

	echo get_text('MsgLookup2','Tournament').'<br/>';
	flush();
// 	debug_svela($DataSource);
	//ob_flush();

	$NumRows=0;
	if($u->LupFors) {
		// FORS Entry
		$XML=New DOMDocument();
		$XML->preserveWhiteSpace = false;
		$XML->loadXML(trim($DataSource));
		//$XML->normalizeDocument();

		$Entries=$XML->getElementsByTagName('Table1');
		if($Entries and $Entries->length) {
			safe_w_sql("delete from LookUpEntries where LueIocCode='$u->LupIocCode'");
		}

		echo get_text('MsgLookup3','Tournament').'<br/>';
		@flush();
		@ob_flush();

		for($n=0; $n<$Entries->length; $n++) {
			$Entry=simplexml_import_dom($Entries->item($n));

			$Entry->NATIONCODE=mb_convert_case($Entry->NATIONCODE, MB_CASE_UPPER, "UTF-8");
			$Entry->SURNAME=AdjustCaseTitle($Entry->SURNAME);
			$Entry->NAME =AdjustCaseTitle($Entry->NAME);
			$Entry->NATIONDESC =AdjustCaseTitle($Entry->NATIONDESC);

			$Data="LueFamilyName=". StrSafe_DB($Entry->SURNAME)."
				, LueName=" . StrSafe_DB($Entry->NAME)."
				, LueSex=" . ($Entry->GENDER=='M' ? 0 : 1)."
				, LueCtrlCode='".ConvertDateLoc($Entry->BIRTHDATE)."'
				, LueCountry=".StrSafe_DB($Entry->NATIONCODE)."
				, LueCoDescr=".StrSafe_DB($Entry->NATIONDESC)."
				, LueDefault=1
				, LueCoShort=".StrSafe_DB($Entry->NATIONDESC);

			$Sql="insert into LookUpEntries set
				LueCode=".StrSafe_DB($Entry->ID.($Entry->TYPE=='ATHLETE' ? '' : 'O'))."
				, LueIocCode=".StrSafe_DB($u->LupIocCode)."
				, ". $Data."
				on duplicate key update ".$Data;

			safe_w_sql($Sql);
			if(($NumRows++ % 100) == 0)
				echo "- ";
			if(($NumRows % 2000) == 0)
				echo "<br>";
			@flush();
			@ob_flush();
		}
		safe_w_sql("update LookUpPaths set LupLastUpdate=now() where LupIocCode='$u->LupIocCode'");
	}
	else	// dovrebbe essere il file tabulato
	{
		$file=$CFG->DOCUMENT_PATH.'Tournament/TmpDownload/archers.dat';

		@file_put_contents($file,$DataSource);

		$fp=@fopen($file,'r');

		if ($fp===false) die('Cannot read downloaded data!');

	// la prima riga deve essere nella forma "VERSION: #.#"
		$buffer=fgets($fp);
		if (!preg_match('/VERSION: [0-9]+\.[0-9]+/',$buffer)) die('Bad file format!');

	// se la versione non è 2.0 mi fermo
		list(,$ver)=explode(':',$buffer);
		if (trim($ver)!='2.0') die('Incompatible Version!');

	// la seconda riga deve essere nella forma "DATE: ##############"
		$buffer=fgets($fp);
		if (!preg_match('/DATE: [0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',$buffer)) die('Bad file format!');
		$date=str_replace('DATE: ','',$buffer);

	// la terza riga deve essere nella forma "IOC: [a-zA-Z0-9]*" e se c'è il codice lo uso come ioc
		$buffer=fgets($fp);
		$buffer=substr($buffer,0,-1);	// per togliere il newline finale

		if (substr($buffer,0,4)!='IOC:') die('Bad file format!');

		$ioc=trim(str_replace('IOC:','',$buffer));
		if (empty($ioc))
		{
			$ioc=$u->LupIocCode;
		}

	// la quarta riga deve essere "CLUBS": se sì allora inizio a leggere fino a che non trovo "ENTRIES" o al più fino alla fine
		$buffer=fgets($fp);
		//print 'clubs<br>';
		if (!preg_match('/CLUBS/',$buffer)) die('Bad file format!');

		safe_w_sql("delete from LookUpEntries where LueIocCode='$ioc'");

		echo get_text('MsgLookup3','Tournament').'<br/>';
		@flush();
		@ob_flush();

		$clubs=array();
	// la riga è: code nome nomebreve
		while (($buffer=fgets($fp))!==false)
		{
			$buffer=substr($buffer,0,-1);	// per togliere il newline finale

			if ($buffer=='ENTRIES')
				break;

			$row=explode("\t",$buffer);

			$clubs[$row[0]]=array($row[1],$row[2]);
		}

	/*
	 *  qui se ho letto "ENTRIES" ho il puntatore del file già a posto. Se sono arrivato alla fine (no nomi e/o no "ENTRIES")
	 *  il ciclo terminerebbe subito
	 */

	// la riga è: code ioc familyname name sex dob div status validuntil soc1 soc2 terne per la classe,subcl e default
		while (($buffer=fgets($fp))!==false) {
			$buffer=rtrim($buffer);	// per togliere il newline finale

			$row=explode("\t",$buffer);

		// fino all'indice 10 ho dati che non riguardano la classe e dall'11 ho le terne delle classi

			$Sql="REPLACE into LookUpEntries set "
				. "LueCode=".StrSafe_DB($row[0])
				. ", LueIocCode=".StrSafe_DB($ioc)
				. ", LueFamilyName=". StrSafe_DB($row[2])
				. ", LueName=" . StrSafe_DB($row[3])
				. ", LueSex=" . $row[4]
				. ", LueCtrlCode='".$row[5]."'"
				. ", LueCountry=".StrSafe_DB($row[9])
				. ", LueCoDescr=".StrSafe_DB($clubs[$row[9]][0])
				. ", LueCoShort=".StrSafe_DB((!empty($clubs[$row[9]][1]) ? $clubs[$row[9]][1] : $clubs[$row[9]][0]))
				. ", LueCountry2=".StrSafe_DB($row[10])
				. ", LueCoDescr2=".StrSafe_DB((!empty($clubs[$row[10]][0]) ? $clubs[$row[10]][0] : ''))
				. ", LueCoShort2=".StrSafe_DB((!empty($clubs[$row[10]][0]) ? (!empty($clubs[$row[10]][1]) ? $clubs[$row[10]][1] : $clubs[$row[10]][0]) : ''))
				. ", LueDivision=".StrSafe_DB($row[6])
				. ", LueStatus=".$row[7]
				. ", LueStatusValidUntil=".StrSafe_DB($row[8])
				. ", LueClass=%1\$s"
				. ", LueSubClass=%2\$s"
				. ", LueDefault=%3\$s"
				;

			for ($i=11;$i<count($row);$i+=3)
			{
//				print $row[$i] . ' - ' . $row[$i+1] . ' - ' .$row[$i+2].'<br>';
//				$q=sprintf($Sql, StrSafe_DB($row[$i]), StrSafe_DB($row[$i+1]), StrSafe_DB($row[$i+2]));
//				print $q.'<br><br>';
				safe_w_sql(sprintf($Sql, StrSafe_DB($row[$i]), StrSafe_DB($row[$i+1]), StrSafe_DB($row[$i+2])));
			}

			if(($NumRows++ % 100) == 0)
				echo "- ";
			if(($NumRows % 2000) == 0)
				echo "<br>";
			@flush();
			@ob_flush();
		}

		fclose($fp);
		@unlink($file);

		safe_w_sql("insert into LookUpPaths set LupIocCode='$ioc', LupLastUpdate='$date' on duplicate key update LupLastUpdate='$date'");
	}

	echo '<br/>'.get_text('MsgLookup4','Tournament').'<br/>';
	@flush();
	@ob_flush();


	// SE LA GARA NON E' BLOCCATA AGGIORNA GLI ARCIERI!
	if(!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		/*
		 * Trasformazione degli stati:
		 * Le righe con EnStatus=1 e LueStatus=8 con dt buona e le righe con EnStatus=6 o =7 non vengono toccate.
		 * Le altre invece prendono lo status della lookup se la dt gara è precedente alla scandenza della persona altrimenti pigliano 5
		 *
		 */
			$Sql="
				UPDATE
					Entries
					INNER JOIN
						Tournament
					ON EnTournament=ToId
					INNER JOIN
						LookUpEntries
					ON EnCode=LueCode and LueIocCode=IF(EnIocCode!='',EnIocCode,ToIocCode)
				SET
					EnStatus=IF(ToWhenFrom>LueStatusValidUntil AND LueStatusValidUntil<>'0000-00-00',5,LueStatus)
				WHERE
					EnTournament=" . StrSafe_DB($_SESSION['TourId']). "
					AND  NOT (EnStatus=6 OR EnStatus=7 OR (EnStatus<=1 AND LueStatus=8 AND LueStatusValidUntil>=ToWhenTo AND LueStatusValidUntil<>'0000-00-00'))
			";
			$Rs=safe_w_sql($Sql);

			$Sql = "UPDATE Entries "
				. "INNER JOIN Tournament ON EnTournament=ToId "
				. "INNER JOIN LookUpEntries ON EnCode=LueCode and LueIocCode=IF(EnIocCode!='',EnIocCode,ToIocCode) AND EnClass=LueClass "
				. "SET EnSubClass=LueSubClass "
				. "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']);
			$Rs=safe_w_sql($Sql);
		echo get_text('MsgLookup5','Tournament') . '<br/>';
	}


	@flush();
	@ob_flush();

//			$Rs=safe_w_sql("LOAD DATA LOCAL INFILE '" . $CFG->DOCUMENT_PATH . "Tournament/TmpDownload/ImportData' INTO TABLE LookUpEntries");
}

function DoLookupPhoto($u) {
	$q=safe_r_sql("select distinct EnId, EnCode, EnFirstName from Entries left join Photos ON PhEnId=EnId where EnTournament={$_SESSION['TourId']} and (EnIocCode='$u->LupIocCode' or (EnIocCode='' and (select ToIocCode from Tournament where ToId={$_SESSION['TourId']})='$u->LupIocCode')) and (PhEnId IS NULL or EnTimestamp>=PhPhotoEntered) order by EnFirstName");
	while($r=safe_fetch($q)) {
		echo '<br/>'.$r->EnCode.'-'.$r->EnFirstName. '... ';
		flush();
		//ob_flush();


		// saves the image on disk...
		if($im=file_get_contents($u->LupPhotoPath."?id=".$r->EnCode)) {
//			die($u->LupPhotoPath."?id=".$r->EnCode);
			if(imagecreatefromstring($im)) {
				safe_w_sql("replace into Photos set PhEnId=$r->EnId, PhPhoto='".addslashes(base64_encode($im))."'");
				echo 'OK';
			} else {
				echo 'ERROR IMPORTING FILE: ' . $im;
			}

		} else {
			echo 'none';
		}
		flush();
		//ob_flush();
	}
}

function DoLookupFlags($u) {
	$q=safe_r_sql("select distinct CoCode, CoName from Countries inner join Entries on CoId in (EnCountry, EnCountry2,EnCountry3) and CoTournament=EnTournament left join Flags ON FlTournament=CoTournament AND FlCode=CoCode where EnTournament={$_SESSION['TourId']} and EnIocCode='$u->LupIocCode' and (FlCode IS NULL) order by CoCode");
	while($r=safe_fetch($q)) {
		echo '<br/>'.$r->CoCode.'-'.$r->CoName. '... ';
		flush();
		//ob_flush();

		$imJPG=file_get_contents($u->LupFlagsPath."?jpg=".$r->CoCode);
		if(!$imJPG) {
			if($imgtmp=file_get_contents($u->LupFlagsPath."?png=".$r->CoCode)) {
				$tmpnam=tempnam('/tmp', 'img');
				$img=imagecreatefromstring($imgtmp);
				imagejpeg($img, $tmpnam, 95);
				$imJPG=file_get_contents($tmpnam);
			}
		}
		$imSVG=file_get_contents($u->LupFlagsPath."?svg=".$r->CoCode);

		if($imJPG or $imSVG) {
			safe_w_sql("replace into Flags set"
				. " FlCode='$r->CoCode'"
				. ", FlIocCode='$u->LupIocCode'"
				. ", FlTournament={$_SESSION['TourId']}"
				. ($imJPG?", FlJPG='".addslashes(base64_encode($imJPG))."'":'')
				. ($imSVG?", FlSVG='".addslashes(gzdeflate($imSVG))."'":'')
				. "");
			echo 'OK';
		} else {
			echo 'none';
		}
		flush();
		//ob_flush();
	}
}
?>