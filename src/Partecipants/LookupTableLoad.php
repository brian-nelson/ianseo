<?php
// @apache_setenv('no-gzip', 1);
// @ini_set('zlib.output_compression', 0);
// @ini_set('implicit_flush', 1);
// for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
// ob_implicit_flush(1);

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Various.inc.php');

CheckTourSession(true);
checkACL(AclParticipants, AclReadWrite);

if(!empty($_REQUEST['PrevPhoto']) and !empty($_REQUEST['EnId']) and !empty($_REQUEST['PhEnId'])) {
	require_once('Common/CheckPictures.php');
	safe_w_sql("insert into Photos (select ".intval($_REQUEST['EnId']).", PhPhoto, PhPhotoEntered, 1 from Photos where PhEnId=".intval($_REQUEST['PhEnId']).")");
	UpdatePhoto(intval($_REQUEST['EnId']));
}

if(!empty($_REQUEST['PrevWaId']) and !empty($_REQUEST['EnId']) and !empty($_REQUEST['WaId']) and $EnId=intval($_REQUEST['EnId']) and $WaId=intval($_REQUEST['WaId'])) {
	require_once('Common/CheckPictures.php');
	safe_w_sql("update Entries
			inner join Tournament on EnTournament=ToId and ToId={$_SESSION['TourId']}
			inner join LookUpEntries on LueIocCode=ToIocCode and LueCode=$WaId
			set EnCode=LueCode, EnName=LueName, EnFirstName=LueFamilyName, EnClassified=LueClassified
			where EnId=$EnId");
	$t=safe_r_sql("select EnFirstName, EnName, EnNameOrder from Entries where EnId={$EnId}");
	if($u=safe_fetch($t)) {
		$FamName=iconv('UTF-8', 'ASCII//TRANSLIT', $u->EnFirstName);
		$FamNameUpper=strtoupper($FamName);
		$GivName=iconv('UTF-8', 'ASCII//TRANSLIT', $u->EnName);
		$GivNamePointed=preg_replace('/[^.a-z]/sim', '', preg_replace('/([a-z])[a-z]*/sim', '$1.', $GivName));
		$TvName=$u->EnNameOrder ? $FamNameUpper.' '.$GivNamePointed : $GivNamePointed.' '.$FamNameUpper;
		safe_w_SQL("update Entries set EnOdfShortname=".StrSafe_DB($TvName)." where EnId={$EnId}");
	}
// 	safe_w_sql("update ExtraData set EdExtra=$WaId where EdId=$EnId and EdType='Z'");
	UpdatePhoto(intval($_REQUEST['EnId']));
}

$DataSource="";

$PAGE_TITLE=get_text('MsgSyncronize','Tournament');

ob_implicit_flush(true);

include('Common/Templates/head.php');

if(empty($_REQUEST["Download"])
	and empty($_FILES["UploadedFile"])
	and empty($_REQUEST["Photo"])
	and empty($_REQUEST["Flags"])
	and empty($_REQUEST["Rank"])
	and empty($_REQUEST["Clubs"])
	and empty($_REQUEST["Records"])
	and empty($_REQUEST["Check"])
		) {
	// Asks for what is to update/download
	echo '<form name="FrmDownload" id="FrmDownload" method="POST" action="" enctype="multipart/form-data">
	<table class="Tabella">
	<tr><th class="Title" colspan="8">'.get_text('MsgSyncronize','Tournament').'</th></tr>
	<tr>
		<th class="SubTitle" nowrap="nowrap">'.get_text('IOCcode','Tournament').'</th>
		<th class="SubTitle" nowrap="nowrap">'.get_text('Photo','Tournament').'</th>
		<th class="SubTitle" nowrap="nowrap">'.get_text('Flags','Tournament').'</th>
		<th class="SubTitle" nowrap="nowrap">'.get_text('Rank').'</th>
		<th class="SubTitle" nowrap="nowrap">'.get_text('CountryClubNames','Tournament').'</th>'
// 		<th class="SubTitle" nowrap="nowrap">'.get_text('Records','Tournament').'</th>
		.'<th class="SubTitle" nowrap="nowrap">'.get_text('MsgSyncNet','Tournament').'</th>
		<th class="SubTitle" colspan="2" nowrap="nowrap">'.get_text('LastUpdate','Tournament').'</th>
	</tr>';

	$t=safe_r_sql("select * from LookUpPaths where LupIocCode in (select ToIocCode from Tournament where ToId={$_SESSION['TourId']} union select distinct EnIocCode from Entries where EnTournament={$_SESSION['TourId']})");
	$firstRow=true;
	while($u=safe_fetch($t)) {
		$linkURL=$u->LupPath;
		$linkCHK=true;
		if($u->LupPath and $u->LupPath[0]=='%') {
			if(file_exists($CFG->DOCUMENT_PATH.substr($u->LupPath, 1))) {
				$linkURL="http://".$_SERVER['HTTP_HOST'].$CFG->ROOT_DIR.substr($u->LupPath, 1);
			} else {
				$linkURL='';
				$linkCHK=(file_exists($CFG->DOCUMENT_PATH.substr($u->LupPath, 1, -3).'txt'));
			}
		}
		$LupPath='';
		if($u->LupPath) {
            $LupPath		 =($linkCHK ? '<input name="Download['.$u->LupIocCode.']" type="checkbox">&nbsp;' : '').($linkURL ? '<a href="'.$linkURL.'" class="Link">'.$linkURL.'</a>' : '');
        }
		$LupPhotoPath='';
        if($u->LupPhotoPath && ($u->LupPhotoPath[0]!='%' or file_exists($CFG->DOCUMENT_PATH.substr($u->LupPhotoPath, 1)))) {
		    $LupPhotoPath = '<div><input name="Photo['.$u->LupIocCode.']" type="checkbox" onclick="document.getElementById(\'onlyMissing-'.$u->LupIocCode.'\').disabled=!this.checked;document.getElementById(\'force-'.$u->LupIocCode.'\').disabled=!this.checked"></div>
                <div><input id="onlyMissing-'.$u->LupIocCode.'" name="OnlyMissingPhoto['.$u->LupIocCode.']" type="checkbox" disabled="disabled" checked="checked">'.get_text('OnlyMissing', 'Tournament').'</div>
                <div><input id="force-'.$u->LupIocCode.'" name="ForceOldPhoto['.$u->LupIocCode.']" type="checkbox" disabled="disabled">'.get_text('ForceOldPhotos', 'Tournament').'</div>';
        }
		$LupFlagsPath    =$u->LupFlagsPath && ($u->LupFlagsPath[0]!='%' or file_exists($CFG->DOCUMENT_PATH.substr($u->LupFlagsPath, 1))) ? '<input name="Flags['.$u->LupIocCode.']" type="checkbox">'.($u->LupIocCode ? '(<input type="checkbox" name="fisu"> FISU)' : '') : '';
		$LupRankingPath  =$u->LupRankingPath && ($u->LupRankingPath[0]!='%' or file_exists($CFG->DOCUMENT_PATH.substr($u->LupRankingPath, 1))) ? '<input name="Rank['.$u->LupIocCode.']" type="checkbox">' : '';
		$LupClubNamesPath=$u->LupClubNamesPath && ($u->LupClubNamesPath[0]!='%' or file_exists($CFG->DOCUMENT_PATH.substr($u->LupClubNamesPath, 1))) ? '<input name="Clubs['.$u->LupIocCode.']" type="checkbox">' : '';
		$LupRecordsPath  =$u->LupRecordsPath && ($u->LupRecordsPath[0]!='%' or file_exists($CFG->DOCUMENT_PATH.substr($u->LupRecordsPath, 1))) ? '<input name="Records['.$u->LupIocCode.']" type="checkbox">' : '';


		if($LupPath or $LupPhotoPath or $LupFlagsPath) {
			echo '<tr>'
				. '<td class="Center" nowrap="nowrap">' . $u->LupIocCode . '</td>'
				. '<td class="Center" nowrap="nowrap">'.$LupPhotoPath.'</td>'
				. '<td class="Center" nowrap="nowrap">'.$LupFlagsPath.'</td>'
				. '<td class="Center" nowrap="nowrap">' . $LupRankingPath . '</td>'
				. '<td class="Center" nowrap="nowrap">'.$LupClubNamesPath.'</td>'
// 				. '<td class="Center" nowrap="nowrap">'.$LupRecordsPath.'</td>'
				. '<td nowrap="nowrap">'.$LupPath.'</td>'
				. '<td class="Center" nowrap="nowrap">'.$u->LupLastUpdate.'</td>'
				. ($firstRow ? '<td class="Center" rowspan="'.safe_num_rows($t).'" nowrap="nowrap"><input type="submit" name="Check" value="'.get_text('MsgCheckNet','Tournament').'"></td>':'')
				. '</tr>';
		}
		$firstRow=false;
	}
	?>
	<tr>
		<th class="SubTitle" colspan="8"><?php print get_text('MsgSyncFile','Tournament');?></th>
	</tr>
	<tr>
		<td class="Center" colspan="8"><input name="UploadedFile" type="file" size="20"></td>
	</tr>
	<tr>
	<td colspan="8" class="Center"><input type="submit" value="<?php echo get_text('MsgSyncronize','Tournament');?>"></td>
	</tr>
	<tr><th colspan="8"><?php echo '<input type="checkbox" name="PrevPhoto"'.(empty($_REQUEST['PrevPhoto'])? '' : ' checked="checked"').' onclick="window.location.href=\''.basename(__FILE__).(empty($_REQUEST['PrevPhoto'])? '?PrevPhoto=on' : '').'\'">'.get_text('PreviousPhotos','Tournament'); ?></th></tr>
	<?php
	// check if there are some photos in this tournament...
	if(!empty($_REQUEST['PrevPhoto'])) {
		$q=safe_r_SQL("select * from Entries
			inner join (select PhEnId OtPhEnId, EnCode OtEnCode, EnIocCode OtEnIocCode, EnTournament OtEnTournament, ToCode OtToCode, ToName OtToName, EnFirstName as OtFirstName, EnName as OtName
				from Entries
				inner join Photos on PhEnId=EnId
				inner join Tournament on EnTournament=ToId " . (!empty($_REQUEST['ToId']) ? ' AND ToId=' . $_REQUEST['ToId']. ' ' : '') . ") Ot on (EnCode=OtEnCode or (EnFirstName=OtFirstName and EnName=OtName))and EnIocCode=OtEnIocCode
			where EnId not in (select PhEnId from Photos inner join Entries on PhEnId=EnId and EnTournament={$_SESSION['TourId']})
		    and EnTournament={$_SESSION['TourId']}
			order by EnCode, OtPhEnId desc");
		while($r=safe_fetch($q)) {
			echo '<tr>';
			echo '<td colspan="1"><a href="'.go_get(array('EnId'=>$r->EnId, 'PhEnId'=>$r->OtPhEnId)).'">'.$r->EnFirstName.' '.$r->EnName.'</a></td>';
			echo '<td><img src="../Partecipants-exp/common/photo.php?id=' . $r->OtPhEnId . '&mode=y&val=50"></td>';
			echo '<td>'.$r->OtToCode.'</td>';
			echo '<td colspan="3"><a href="'.go_get(array('ToId'=>(!empty($_REQUEST['ToId']) ? '0' : $r->OtEnTournament))).'">'.$r->OtToName.'</a></td>';
			echo '</tr>';
		}
	}
	echo '<tr><th colspan="8">';
	echo '<input type="checkbox" name="PrevWaId"'.(empty($_REQUEST['PrevWaId'])? '' : ' checked="checked"').' onclick="window.location.href=\''.basename(__FILE__).'?CatWaId=\'+document.getElementById(\'CatWaId\').value+\''.(empty($_REQUEST['PrevWaId'])? '&PrevWaId=on' : '').'\'">'.get_text('CheckWaIds','Tournament');
	echo '&nbsp;-&nbsp;'.get_text('FilterOnDivCl', 'Tournament').':&nbsp;<input type="text" size="8" maxlength="4" value="'.(empty($_REQUEST['CatWaId'])? '' : $_REQUEST['CatWaId']).'" id="CatWaId" onchange="window.location.href=\''.basename(__FILE__).'?PrevWaId=on&CatWaId=\'+document.getElementById(\'CatWaId\').value">';
	echo '</th></tr>';
	// check if there are some matching WAIDs...
	if(!empty($_REQUEST['PrevWaId'])) {
		$q=safe_r_SQL("select EnId, EnCode, LueSex, EnSex, LueCode, EnFirstName, EnName, EnDob, LueCtrlCode, LueFamilyName, LueName, LueCountry, CoCode, (soundex(EnFirstName)=soundex(LueFamilyName) and soundex(EnName)=soundex(LueName)) or (soundex(EnFirstName)=soundex(LueName) and soundex(EnName)=soundex(LueFamilyName)) as BestMatch
			from Entries
			inner join Countries on EnCountry=CoId
			inner join Tournament on EnTournament=ToId
			inner join LookUpEntries on ToIocCode=LueIocCode " . (empty($_REQUEST['CatWaId']) ? "and CoCode=LueCountry " : "") ." and ( (soundex(EnFirstName)=soundex(LueFamilyName) or soundex(EnName)=soundex(LueName)) or (soundex(EnFirstName)=soundex(LueName) or soundex(EnName)=soundex(LueFamilyName)))
			where left(EnCode, 1)='_'
		    and EnTournament={$_SESSION['TourId']} " . (empty($_REQUEST['CatWaId']) ? "" : " and CONCAT(TRIM(EnDivision),TRIM(Enclass)) LIKE " . StrSafe_DB($_REQUEST['CatWaId'])) . "
			order by 
			    EnFirstName=LueFamilyName and EnName=LueName and (EnDob=LueCtrlCode or EnDob=0) desc,
			    EnDob>0 and EnDoB=LueCtrlCode desc,
			    BestMatch desc, right(LueCode,1)='O', EnCode, CoCode, EnName, EnFirstName");
		if(safe_num_rows($q)) {
			echo '<tr><td colspan="8"><table width="100%">';
			echo '<tr>';
			echo '<th colspan="2" rowspan="2"></th>';
			echo '<th colspan="2">'.get_text('MenuLM_Competition').'</th>';
			echo '<th colspan="2">'.get_text('LookupTable', 'Tournament').'</th>';
			echo '<th rowspan="2">'.get_text('Country').'</th>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>'.get_text('Name', 'Tournament').'</th>';
			echo '<th>'.get_text('DOB', 'Tournament').'</th>';
			echo '<th>'.get_text('DOB', 'Tournament').'</th>';
			echo '<th>'.get_text('Name', 'Tournament').'</th>';
			echo '</tr>';
			while($r=safe_fetch($q)) {
				$Style='';
				if($r->EnFirstName==$r->LueFamilyName and $r->EnName==$r->LueName and ($r->EnDob==$r->LueCtrlCode or $r->EnDob=='0000-00-00')) {
					$Style=' style="background-color:#80ff80"';
				} elseif($r->EnDob=='0000-00-00' or $r->LueCtrlCode=='0000-00-00') {
					$Style=' style="background-color:#ffff80"';
				} elseif($r->EnDob==$r->LueCtrlCode) {
					$Style=' style="background-color:#80ffff"';
				}
				echo '<tr'.$Style.'>';
				echo '<td>'.$r->EnCode.'</td>';
				echo '<td><a href="'.go_get(array('EnId'=>$r->EnId, 'WaId'=>$r->LueCode)).'">'.$r->LueCode.'</a></td>';
				echo '<td>'.$r->EnFirstName.' '.$r->EnName.' ('.($r->EnSex ? 'W' : 'M').')</td>';
				echo '<td>'.$r->EnDob.'</td>';
				echo '<td>'.$r->LueCtrlCode.'</td>';
				echo '<td>'.$r->LueFamilyName.' '.$r->LueName.' ('.($r->LueSex ? 'W' : 'M').')</td>';
				echo '<td>'.$r->CoCode.($r->LueCountry==$r->CoCode ? '' : ' / <b>' . $r->LueCountry . '</b>').'</td>';
				echo '</tr>';
			}
			echo '</table></td></tr>';
		}
	}
	echo '</table></form>';
} else if(!empty($_REQUEST["Check"])) {
	DoLookupEntriesCheck();
	ShowNotMatchingEntries();
    echo '<br>';
    echo get_text('MsgLookup5','Tournament');
} else {
	//ini_set('memory_limit', '512M');
	set_time_limit(0);
	$CFG->TRACE_QUERRIES=false;

	require_once('Common/Lib/Fun_DateTime.inc.php');
	$IocToUpdate=array();

	echo str_repeat(' ',4080);
	flush();

	// Check if a file has been uploaded
	if(!empty($_FILES["UploadedFile"]["name"]) and strlen($_FILES["UploadedFile"]["name"]) and $_FILES["UploadedFile"]["error"]==UPLOAD_ERR_OK) {
		$t=safe_r_sql("
			select IFNULL(LupIocCode,ToIocCode) AS LupIocCode,ifnull(LupOrigin,'') AS LupOrigin
			from
			Tournament
				LEFT JOIN LookUpPaths
			ON ToIocCode=LupIocCode AND ToId={$_SESSION['TourId']}
			where
				ToId={$_SESSION['TourId']}
		");
		$u=safe_fetch($t);

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
			DoLookupPhoto($u, isset($_REQUEST["OnlyMissingPhoto"][$u->LupIocCode]), isset($_REQUEST["ForceOldPhoto"][$u->LupIocCode]));
		}
		if($u->LupFlagsPath and !empty($_REQUEST["Flags"][$u->LupIocCode])) {
			echo $head;
			$head='';
			DoLookupFlags($u);
		}
		if($u->LupRankingPath and !empty($_REQUEST["Rank"][$u->LupIocCode])) {
			echo $head;
			$head='';
			DoLookupRank($u);
		}
		if($u->LupClubNamesPath and !empty($_REQUEST["Clubs"][$u->LupIocCode])) {
			echo $head;
			$head='';
			DoLookupClubs($u);
		}
// 		if($u->LupRankingPath and !empty($_REQUEST["Records"][$u->LupIocCode])) {
// 			echo $head;
// 			$head='';
// 			DoLookupRecords($u);
// 		}
		if(!$head) echo '</div>';
	}
	echo '<div>'.get_text('EndUpdate', 'Tournament').'</div>';
}

include('Common/Templates/tail.php');

function DoLookupEntries($u, $file='') {
	global $CFG;
	$ord=1;

	echo ($ord++).') '.get_text('MsgLookup1','Tournament').'<br/>';
	flush();
	//ob_flush();

	if(!$file and $u->LupPath['0']=='%') {
		$tmp=substr($u->LupPath, 1);
		if(file_exists($txt=$CFG->DOCUMENT_PATH.substr($tmp,0,-3).'txt')) {
			$file=trim(file_get_contents($txt));
		} else {
			$file="http://".$_SERVER['HTTP_HOST'].$CFG->ROOT_DIR.$tmp;
		}
	}

	$DataSource = file_get_contents($file ? $file : $u->LupPath);

	if (!$DataSource) {
    	echo "No Database<br>\n";
    	return;
	}

	// checks if it is gzipped
	if($unzipped=@gzuncompress($DataSource)) {
		$DataSource=$unzipped;
	}

	echo ($ord++).') '.get_text('MsgLookup2','Tournament').'<br/>';
	flush();
	//ob_flush();

	$NumRows=0;
	if($u->LupOrigin) {
		// Extranet
		if($Archers=json_decode($DataSource)) {
			safe_w_sql("delete from LookUpEntries where LueIocCode='$u->LupIocCode'");
			;
			echo ($ord++).') '.get_text('MsgLookup3','Tournament').'<br/>';
			@flush();
			@ob_flush();

			foreach($Archers as $r) {
				$Data="LueCode=".StrSafe_DB(isset($r->WaId) ? $r->WaId : $r->Id)."
					, LueIocCode=".StrSafe_DB(isset($u->LupIocCode) ? $u->LupIocCode : $u->Type)."
					, LueFamilyName=" . StrSafe_DB($r->FamilyName)."
					, LueName=" . StrSafe_DB($r->GivenName)."
					, LueSex=" . ($r->Gender=='M' ? 0 : 1)."
					, LueClassified=" . (empty($r->Para) ? 0 : 1)."
					, LueCtrlCode='".ConvertDateLoc($r->BirthDate)."'
					, LueCountry=".StrSafe_DB($r->CountryCode)."
					, LueCoDescr=".StrSafe_DB($r->CountryName)."
					, LueCoShort=".StrSafe_DB($r->ShortCountryName)."
					, LueNameOrder=".intval($r->NameOrder)."
					, LueStatus=".intval($r->Status)."
					, LueDefault=1";
				$Sql="insert into LookUpEntries set $Data
					on duplicate key update ".$Data;

				safe_w_sql($Sql);
				if(($NumRows++ % 100) == 0)
					echo "- ";
				if(($NumRows % 2000) == 0)
					echo "<br>";
				@flush();
				@ob_flush();
			}
			safe_w_sql("update LookUpPaths set LupLastUpdate='".date('Y-m-d H:i:s')."' where LupIocCode='$u->LupIocCode'");
		}
	} else {
	// dovrebbe essere il file tabulato
        if(!is_dir($CFG->DOCUMENT_PATH.'Tournament/TmpDownload')) {
            mkdir($CFG->DOCUMENT_PATH.'Tournament/TmpDownload', 0777);
            chmod($CFG->DOCUMENT_PATH.'Tournament/TmpDownload', 0777);
        }
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

		echo ($ord++).') '.get_text('MsgLookup3','Tournament').'<br/>';
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

	echo '<br/>'.($ord++).') '.get_text('MsgLookup4','Tournament').'<br/>';
	@flush();
	@ob_flush();



	// SE LA GARA NON E' BLOCCATA AGGIORNA GLI ARCIERI!
	if(!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		DoLookupEntriesCheck();
		echo ($ord++).') '.get_text('MsgLookup5','Tournament') . '<br/>';
	}
	@flush();
	@ob_flush();

//			$Rs=safe_w_sql("LOAD DATA LOCAL INFILE '" . $CFG->DOCUMENT_PATH . "Tournament/TmpDownload/ImportData' INTO TABLE LookUpEntries");
}

function DoLookupPhoto($u, $OnlyMissing=false, $ForceOld=false) {
	global $CFG;
	require_once('Common/PhotoResize.php');
	require_once('Common/CheckPictures.php');
	if($u->LupPhotoPath[0]=='%') {
		if(file_exists($CFG->DOCUMENT_PATH . substr($u->LupPhotoPath, 1))) {
			require_once($CFG->DOCUMENT_PATH . substr($u->LupPhotoPath, 1));
			$Function='LookupPhoto'.$u->LupIocCode;
			$q=safe_r_sql("select distinct EnId, EnCode, EnFirstName, ToWhenTo
                from Entries 
                inner join Tournament on ToId=EnTournament
                left join Photos ON PhEnId=EnId
					where EnTournament={$_SESSION['TourId']}
						and (EnIocCode='$u->LupIocCode' or (EnIocCode='' and (select ToIocCode from Tournament where ToId={$_SESSION['TourId']})='$u->LupIocCode')) and (PhEnId IS NULL or EnTimestamp>=PhPhotoEntered)
						".($OnlyMissing ? " and (PhEnId is null or PhPhoto = '') " : '')."
					order by EnFirstName");
			while($r=safe_fetch($q)) {
				echo '<br/>'.$r->EnCode.'-'.$r->EnFirstName. '... ';
				flush();
				echo $Function($r->EnCode, $r->EnId, $ForceOld ? '' : $r->ToWhenTo);
				//ob_flush();

				flush();
				//ob_flush();
			}
		}
	} else {
		$q=safe_r_sql("select distinct EnId, EnCode, EnFirstName from Entries left join Photos ON PhEnId=EnId where EnTournament={$_SESSION['TourId']} and (EnIocCode='$u->LupIocCode' or (EnIocCode='' and (select ToIocCode from Tournament where ToId={$_SESSION['TourId']})='$u->LupIocCode')) and (PhEnId IS NULL or EnTimestamp>=PhPhotoEntered)
			".($OnlyMissing ? " and (PhEnId is null or PhPhoto = '') " : '')."
			order by EnFirstName");
		while($r=safe_fetch($q)) {
			echo '<br/>'.$r->EnCode.'-'.$r->EnFirstName. '... ';
			flush();
			//ob_flush();


			// saves the image on disk...
			if($im=file_get_contents($u->LupPhotoPath."?id=".$r->EnCode)) {
				$Booth='';
				if($_SESSION['AccBooth']) {
					// pictures will be recorded in a Database!
					$q=safe_r_sql("select EnCode, EnIocCode, ToCode from Entries inner join Tournament on EnTournament=ToId where EnId={$r->EnId}");
					$Booth=safe_fetch($q);
				}

				if($image=photoresize($im, true, true) and InsertPhoto($r->EnId, $image, $Booth)) {
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
}

function DoLookupFlags($u) {
	require_once('Common/CheckPictures.php');
	$q=safe_r_sql("select distinct CoCode, CoName from Countries inner join Entries on CoId in (EnCountry, EnCountry2,EnCountry3) and CoTournament=EnTournament left join Flags ON FlTournament=CoTournament AND FlCode=CoCode where EnTournament={$_SESSION['TourId']} and EnIocCode='$u->LupIocCode' order by CoCode");

	$Opt='';
	if($_SESSION['TourLocRule']=='PAR') {
		$Opt='opt=IPC&';
	} elseif(!empty($_REQUEST['fisu'])) {
		$Opt='opt=FISU&';
	}
	while($r=safe_fetch($q)) {
		echo '<br/>'.$r->CoCode.'-'.$r->CoName. '... ';
		flush();
		//ob_flush();

		$imJPG=file_get_contents($u->LupFlagsPath."?{$Opt}jpg=".$r->CoCode);
		if($Opt and !$imJPG) {
			$imJPG=file_get_contents($u->LupFlagsPath."?jpg=".$r->CoCode);
		}
		if(!$imJPG) {
			echo $u->LupFlagsPath."?png=".$r->CoCode . ' ';
			$imgtmp=file_get_contents($u->LupFlagsPath."?{$Opt}png=".$r->CoCode);
			if($Opt and !$imgtmp) {
				$imgtmp=file_get_contents($u->LupFlagsPath."?png=".$r->CoCode);
			}
			if($imgtmp) {
				$tmpnam=tempnam('/tmp', 'img');
				$img=imagecreatefromstring($imgtmp);
				imagejpeg($img, $tmpnam, 95);
				$imJPG=file_get_contents($tmpnam);
			}
		}
		$imSVG=file_get_contents($u->LupFlagsPath."?{$Opt}svg=".$r->CoCode);
		if($Opt and !$imSVG) {
			$imSVG=file_get_contents($u->LupFlagsPath."?svg=".$r->CoCode);
		}

		if($imJPG or $imSVG) {
			$SQL="FlCode='$r->CoCode',
				FlIocCode='$u->LupIocCode',
				FlTournament={$_SESSION['TourId']},
				FlJPG=" . ($imJPG ? StrSafe_DB(base64_encode($imJPG)) : "''") .",
				FlSVG=" . ($imSVG ? "'".addslashes(gzdeflate($imSVG))."'" : "''");
			safe_w_sql("insert into Flags set $SQL on duplicate key update $SQL");

			echo 'OK';
		} else {
			echo 'none';
		}
		flush();
		//ob_flush();
	}
	// update flags
	$q=safe_r_sql("select FlCode, FlJPG, FlSVG, ToCode, ToId from Flags
		inner join Tournament on FlTournament=ToId
		where FlTournament={$_SESSION['TourId']} and FlIocCode='$u->LupIocCode'");

	updateFlag('', 'ALL', $q);
// 	die();
}

function DoLookupRank($u) {
	global $CFG;
	if($u->LupRankingPath['0']=='%') {
		if(file_exists($CFG->DOCUMENT_PATH . substr($u->LupRankingPath, 1))) {
			require_once($CFG->DOCUMENT_PATH . substr($u->LupRankingPath, 1));
			$Function='LookupRank'.$u->LupIocCode;
			echo $Function();
		}
	} else {
		echo 'NOT IMPLEMENTED YET, CONTACT <a href="mailto://info@ianseo.net">info@ianseo.net</a> IF YOU WANT IT DONE!';
	}
}

function DoLookupClubs($u) {
	global $CFG;
	if($u->LupClubNamesPath['0']=='%') {
		if(file_exists($CFG->DOCUMENT_PATH . substr($u->LupClubNamesPath, 1))) {
			require_once($CFG->DOCUMENT_PATH . substr($u->LupClubNamesPath, 1));
			$Function='LookupClubs'.$u->LupIocCode;
			echo $Function();
		}
	} else {
		echo 'NOT IMPLEMENTED YET, CONTACT <a href="mailto://info@ianseo.net">info@ianseo.net</a> IF YOU WANT IT DONE!';
	}
}

function DoLookupEntriesCheck() {
	/*
	 * Trasformazione degli stati:
	 * RIMOSSO: Le righe con EnStatus=1 e LueStatus=8 con dt buona e le righe con EnStatus=6 o =7 non vengono toccate.???
	 * CD: modificato che se status=1 tira e basta!
	 * Le altre invece prendono lo status della lookup se la dt gara è precedente alla scandenza della persona altrimenti pigliano 5
	 *
	 */
	$Sql="UPDATE Entries
		INNER JOIN Tournament ON EnTournament=ToId
		INNER JOIN LookUpEntries ON EnCode=LueCode and LueIocCode=IF(EnIocCode!='',EnIocCode,ToIocCode)
		SET EnTimestamp=if(EnStatus!=IF(ToWhenTo>LueStatusValidUntil AND LueStatusValidUntil<>'0000-00-00',5,LueStatus), '".date('Y-m-d H:i:s')."', EnTimestamp),
		EnStatus=IF(ToWhenTo>LueStatusValidUntil AND LueStatusValidUntil<>'0000-00-00',5,LueStatus),
		EnNameOrder=LueNameOrder,
		EnClassified=LueClassified
		WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']). "
		AND  NOT (EnStatus=6 OR EnStatus=7 OR EnStatus=1)";
	$Rs=safe_w_sql($Sql);

	$Sql = "UPDATE Entries
		INNER JOIN Tournament ON EnTournament=ToId
		INNER JOIN LookUpEntries ON EnCode=LueCode and LueIocCode=IF(EnIocCode!='',EnIocCode,ToIocCode) AND EnClass=LueClass and EnDivision=IF(ToIocCode='ITA_i',LueDivision,EnDivision)
		SET EnSubClass=LueSubClass, EnTimestamp=if(EnSubClass=LueSubClass, EnTimestamp, '".date('Y-m-d H:i:s')."')
		WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']);
	$Rs=safe_w_sql($Sql);

    $Sql = "UPDATE Entries
		INNER JOIN Tournament ON EnTournament=ToId
		LEFT JOIN LookUpEntries ON EnCode=LueCode and LueIocCode='ITA_i' AND EnClass=LueClass and EnDivision=LueDivision
		SET EnSubClass='04', EnTimestamp='".date('Y-m-d H:i:s')."'
		WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND ToIocCode='ITA_i' and EnDivision IN ('OL','CO','AN') AND (EnSubClass='00' OR LueCode IS NULL) AND LEFT(EnCode,1)!='_'";
    $Rs=safe_w_sql($Sql);


    $Sql = "UPDATE Entries
		INNER JOIN LookUpPaths ON EnIocCode=LupIocCode
		SET EnLueTimeStamp=LupLastUpdate
		WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']);
	$Rs=safe_w_sql($Sql);

	$Sql = "SELECT EnId FROM Entries WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']);
	$Rs=safe_r_SQL($Sql);
	while($r=safe_fetch($Rs)){
		checkAgainstLUE($r->EnId);
	}
}

function ShowNotMatchingEntries() {
    $Changes = getLUEChanges($_SESSION['TourId']);
    if(count($Changes)){
        echo '<table class="Tabella">';
	    echo '<tr><th class="Title" colspan="7">'.get_text('MsgSyncronize','Tournament').'</th></tr>';
        echo '<tr>';
        echo '<th>'.get_text('Code', 'Tournament').'</th>';
        echo '<th>'.get_text('FamilyName', 'Tournament').'</th>';
        echo '<th>'.get_text('Name', 'Tournament').'</th>';
        echo '<th>'.get_text('Sex', 'Tournament').'</th>';
        echo '<th>'.get_text('DOB', 'Tournament').'</th>';
        echo '<th>'.get_text('CountryCode').'</th>';
        echo '<th>'.get_text('Status', 'Tournament').'</th>';
        echo '</tr>';
        foreach ($Changes as $k=>$v) {
            echo '<tr>';
            echo '<td>'.$k.'</td>';
            foreach ($v as $txt) {
                echo '<td '.(strpos($txt,'(')!==false ? 'class="Bold"':'').'>'.$txt.'</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }

}


