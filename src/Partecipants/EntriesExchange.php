<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
checkACL(AclAccreditation, AclReadOnly);

$TourId=$_SESSION['TourId'];
if($_SESSION['AccreditationTourIds']) $TourId=$_SESSION['AccreditationTourIds'];

if(!empty($_REQUEST['Export'])) {
	$Gara=export_entries($TourId);

	$Name=array();
	foreach($Gara['Tournaments'] as $t) $Name[]=$t['ToCode'];

	// We'll be outputting a gzipped TExt File in UTF-8 pretending it's binary
	header('Content-type: application/octet-stream');
	// It will be called ToCode1[-ToCode2[...]].entries
	header("Content-Disposition: attachment; filename=\"".implode('-', $Name).".entries\"");

	echo gzcompress(serialize($Gara),9);

	exit();
}

if($_FILES and !empty($_FILES['Gara']['tmp_name']) and substr($_FILES['Gara']['name'], -8)=='.entries'){
	// if an ID is returned then everything is fine!
	if(!($ReturnStatus=import_Entries($_FILES['Gara']['tmp_name']))) {
		PrintCrackError(false, 'IncompatibleVersions', 'Tournament', '<a href="https://www.ianseo.net/">Ianseo.net</a>');
	}

}


$PAGE_TITLE=get_text('MenuLM_Export Entries');

include('Common/Templates/head.php');

$onclick='';
if(GetParameter('TourBusy')) $onclick=' onclick="return(confirm(\''.str_replace("\n",'\n',addslashes(get_text('TourBusy','Tournament'))).'\'))"';


?>



<div align="center">
<div class="medium">
<form method="POST" enctype="multipart/form-data">
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('MenuLM_Export Entries'); ?></th></tr>
<tr class="Spacer"><td colspan="2"></td></tr>
<tr><th class="SubTitle"><?php echo get_text('SelFile2Imp','HTT') ?></th>
	<td><input type="file" name="Gara">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="<?php echo get_text('CmdImport','HTT') ?>"<?php echo $onclick; ?>></td></tr>
<tr><th colspan="2" class="SubTitle"><input type="submit" name="Export" value="<?php echo get_text('ExportEntries','Tournament') ?>"></th></tr>
</table>
</form>
<?php
if(!empty($ReturnStatus)) {
	echo '<table class="Tabella">';
	foreach($ReturnStatus as $Title => $Results) {
		if(!$Results) continue;
		sort($Results);
		echo '<tr><th colspan="100">'.$Title.'</th></tr>'.implode('', $Results);
	}
	echo '</table>';
}

?>
</div>
</div>

<?php

include('Common/Templates/tail.php');


function export_entries($TourId) {

	$Gara=array();
	// Start fetching the ToCode <=> ToId translations
	$Rs=safe_r_sql("SELECT ToId, ToCode, ToDbVersion FROM Tournament WHERE ToId in ($TourId)");
	while($MyRow=safe_fetch_assoc($Rs)) {
		$Gara['Tournaments'][$MyRow['ToId']]=$MyRow;
	}

	// Gets only Countries from real Entries
	$Rs=safe_r_sql("SELECT distinct Countries.* FROM Countries inner join Entries on CoId in (EnCountry, EnCountry2, EnCountry3) WHERE CoTournament in ($TourId)");
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['Countries'][$MyRow['CoId']]=$MyRow;
	}

	// Gets Entries
	$Rs=safe_r_sql("SELECT Entries.*, EdExtra FROM Entries left join ExtraData on EnId=EdId and EdType='Z' WHERE EnTournament in ($TourId)");
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['Entries'][$MyRow['EnId']]=$MyRow;
	}

	// ExtraData
	$Gara['ExtraData']=array();
	$Rs=safe_r_sql("SELECT ExtraData.* FROM ExtraData inner join Entries on EnId=EdId WHERE EnTournament in ($TourId)");
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['ExtraData'][$MyRow['EdId']]=$MyRow;
	}

	// AccEntries
	$Gara['AccEntries']=array();
	$Rs=safe_r_sql("SELECT AccEntries.* FROM AccEntries inner join Entries on EnId=AEId WHERE EnTournament in ($TourId)");
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['AccEntries'][$MyRow['AEId']]=$MyRow;
	}

	// Flags from real Entries
	$Gara['Flags']=array();
	$Rs=safe_r_sql("SELECT distinct Flags.*, CoId FROM Entries
			inner join Countries on CoId in (EnCountry, EnCountry2, EnCountry3) and EnTournament=CoTournament
			inner join Flags on CoCode=FlCode and FlTournament = EnTournament
			WHERE EnTournament in ($TourId)");
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['Flags'][$MyRow['CoId']]=$MyRow;
	}

	// Photos
	$Gara['Photos']=array();
	$Rs=safe_r_sql("SELECT Photos.* FROM Photos inner join Entries on Entries.EnId=Photos.PhEnId WHERE Entries.EnTournament in ($TourId)");
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['Photos'][$MyRow['PhEnId']]=$MyRow;
	}

	return $Gara;
}

function import_Entries($filename) {
	require_once('Common/CheckPictures.php');

	// tables that have ToId
	$tabs_on_tour=array(
		'Countries' => 'Co',
		'Entries' => 'En',
		'AccEntries' => 'AE',
		'Flags' => 'Fl',
	);

	// Tables that have CoId
	$tab_to_country=array(
		'Entries' => array('EnCountry','EnCountry2','EnCountry3'),
	);

	$Refused=array();
	$Updated=array();
	$Inserts=array();
	$EnToDel=array();

	$Gara=unserialize(gzuncompress(implode('',file($filename))));

	$Tours=array();
	$Codes=array();
	// If is not compatible, exits
	// if ONLY ONE competition in the export, exchange data
	// 	 with the open competition, provided parameters are off

	if(!GetParameter('AccActive') and count($Gara['Tournaments'])==1) {
		foreach($Gara['Tournaments'] as $id => $data) {
			if($data['ToDbVersion'] != GetParameter('DBUpdate')) {
				return false;
			}
			$tmp=$_SESSION['TourId'];
			$Tours[$data['ToId']]=$tmp;
			$Codes[$tmp]=$data['ToCode'];
		}
	} else {
		foreach($Gara['Tournaments'] as $id => $data) {
			if($data['ToDbVersion'] != GetParameter('DBUpdate')) {
				return false;
			}
			if($tmp=getIdFromCode($data['ToCode'])) {
				$Tours[$data['ToId']]=$tmp;
				$Codes[$tmp]=$data['ToCode'];
			}
		}
	}

	// adjust ToId in all tables...
	foreach($tabs_on_tour as $tab => $code) {
		if(isset($Gara[$tab])) {
			foreach($Gara[$tab] as $key=>$val) {
				if(!empty($Tours[$val[$code.'Tournament']])) {
					$Gara[$tab][$key][$code.'Tournament']=$Tours[$val[$code.'Tournament']];
				} else {
					// if the competition is not there, unsets all its records
					unset($Gara[$tab][$key]);
				}
			}
		}
	}

	// Checks if a country has changed/created
	$Countries=array();
	$Parents1=array();
	$Parents2=array();
	if(isset($Gara['Countries'])) {
		foreach($Gara['Countries'] as $CoId => $Country) {
			unset($Country['CoId']);
			$query=array();

			if($Country['CoParent1']) {
				$Parents1[$CoId]=$Country['CoParent1'];
			}
			if($Country['CoParent2']) {
				$Parents2[$CoId]=$Country['CoParent2'];
			}

			$q=safe_r_sql("select * from Countries where CoCode=".StrSafe_DB($Country['CoCode'])." and CoTournament={$Country['CoTournament']}");
			if($r=safe_fetch_assoc($q)) {
				// nation exists... check differences
				$NewCoId=$r['CoId'];
				unset($r['CoId']);
				foreach($r as $k => $v) {
					if($v!=$Country[$k]) {
						$query[]="$k = ".StrSafe_DB($Country[$k]);
					}
				}
				if($query) {
					safe_w_sql("update Countries set ".implode(',', $query)." where CoId={$NewCoId}");
					$Updated[]="<tr><td>{$Codes[$Country['CoTournament']]}</td><td>Country</td><td>{$Country['CoCode']}</td><td>{$Country['CoName']}</td><td>{$Country['CoIocCode']}</td><td>{$Country['CoNameComplete']}</td></tr>";
				}
			} else {
				// nation does not exist... insert
				foreach($Country as $key=>$val) {
					$query[]="$key = " . strsafe_db($val) ;
				}
				safe_w_sql("insert into Countries set ". implode(', ', $query));
				$Inserts[]="<tr><td>{$Codes[$Country['CoTournament']]}</td><td>Country</td><td>{$Country['CoCode']}</td><td>{$Country['CoName']}</td><td>{$Country['CoIocCode']}</td><td>{$Country['CoNameComplete']}</td></tr>";
				$NewCoId=safe_w_last_id();
			}
			$Countries[$CoId]=$NewCoId;
		}
	}

	// updates CoParent1
	foreach($Parents1 as $OldId => $OldParent) {
		safe_w_sql("UPDATE Countries SET CoParent1=". $Countries[$OldParent] . " WHERE CoId=" . $Countries[$OldId]);
	}
	// updates CoParent2
	foreach($Parents2 as $OldId => $OldParent) {
		safe_w_sql("UPDATE Countries SET CoParent1=". $Countries[$OldParent] . " WHERE CoId=" . $Countries[$OldId]);
	}

	// update the countries in the tabs
	foreach($tab_to_country as $tab=>$field) {
		if(isset($Gara[$tab])) {
			foreach($Gara[$tab] as $key=>$record) {
				if(is_array($field)) {
					foreach($field as $ff) {
						if(array_key_exists($ff,$record) && $record[$ff]) {
							$Gara[$tab][$key][$ff]=$Countries[$record[$ff]];
						}
					}
				} else {
					if($record[$field] && array_key_exists($record[$field],$Countries)) {
						$Gara[$tab][$key][$field]=$Countries[$record[$field]];
					}
				}
			}
		}
	}

	// Check Entries
	$Entries=array();
	foreach($Gara['Entries'] as $EnId => $Entry) {
		$query=array();
		unset($Entry['EnId']);
		if($Entry['EdExtra']) {
			// original data... updates the entry
			$OrgCode=explode('-', $Entry['EdExtra']);
			$q=safe_r_sql("select * from Entries where EnCode=".StrSafe_DB($OrgCode['0'])." and EnTournament={$Entry['EnTournament']} and EnIocCode=".StrSafe_DB($OrgCode['1'])." and EnDivision=".StrSafe_DB($OrgCode['2']));
			if(safe_num_rows($q)==1 and $r=safe_fetch_assoc($q)) {
				$NewId=$r['EnId'];

				foreach($Entry as $key=>$val) {
					if($key!='EnTimestamp' and $key!='EdExtra' and $val!=$r[$key]) {
						$query[]="$key = " . strsafe_db($val) ;
					}
				}
				if($query) {
					safe_w_sql("update Entries set ". implode(', ', $query)." where EnId={$NewId}");
					$Updated[]="<tr><td>{$Codes[$Entry['EnTournament']]}</td><td>Entry</td><td>{$Entry['EnFirstName']}</td><td>{$Entry['EnName']}</td><td>{$Entry['EnIocCode']}</td><td>{$Entry['EnCode']}</td><td>{$Entry['EnDivision']}</td><td>{$Entry['EdExtra']}</td></tr>";
				}

				$Entries[$EnId]=$NewId;
			} else {
				$Refused[$EnId]="<tr><td>{$Codes[$Entry['EnTournament']]}</td><td>Entry</td><td>{$Entry['EnFirstName']}</td><td>{$Entry['EnName']}</td><td>{$Entry['EnIocCode']}</td><td>{$Entry['EnCode']}</td><td>{$Entry['EnDivision']}</td><td>{$Entry['EdExtra']}</td></tr>";
			}
		} else {
			// NEW ENTRY!!!
			foreach($Entry as $key=>$val) {
				if($key=='EdExtra') continue;
				$query[]="$key = " . strsafe_db($val) ;
			}
			safe_w_sql("insert into Entries set ". implode(', ', $query));
			$NewId=safe_w_last_id();
			safe_w_sql("insert into Qualifications set QuId=$NewId");
			$Inserts[]="<tr><td>{$Codes[$Entry['EnTournament']]}</td><td>Entry</td><td>{$Entry['EnFirstName']}</td><td>{$Entry['EnName']}</td><td>{$Entry['EnIocCode']}</td><td>{$Entry['EnCode']}</td><td>{$Entry['EnDivision']}</td><td>{$Entry['EdExtra']}</td></tr>";
			$Entries[$EnId]=$NewId;
		}
	}

	// check if there are some Entries to delete...
	$q=safe_r_sql("select Entries.*, ToCode from Entries inner join Tournament on EnTournament=ToId where EnTournament in (".implode(',', $Tours).") and EnId not in (".implode(',', $Entries).")");
	while($r=safe_fetch($q)) {
		safe_w_sql("update Entries set EnStatus=9, EnTimestamp=EnTimestamp where EnId=$r->EnId");
		$EnToDel[]="<tr><td>$r->ToCode</td><td>Entries</td><td>$r->EnFirstName</td><td>$r->EnName</td><td>$r->EnCode</td></tr>";
	}

	// updates the tables depending on EnId

	// AccEntries
	foreach($Gara['AccEntries'] as $AEId=>$record) {
		if(empty($Entries[$AEId])) {
			// no entries (refused!) so unset this entry
			unset($Gara['AccEntries'][$AEId]);
			$Refused[$AEId]=str_replace('Entries', 'Entries<br>AccEntries', $Refused[$AEId]);
		} else {
			$Gara['AccEntries'][$AEId]['AEId']=$Entries[$record[$field]];
			$record['AEId']=$Entries[$record[$field]];
			$query=array();
			foreach($record as $k=>$v) {
				$query[]="$k = ".StrSafe_DB($v);
			}
			safe_w_sql("insert into AccEntries set ".implode(',', $query)." on duplicate key update ".implode(',', $query));
			switch(safe_w_affected_rows()) {
				case 1:
					$Inserts[]="<tr><td>{$Codes[$Gara['Entries'][$AEId]['EnTournament']]}</td><td>AccEntries</td><td>{$Gara['Entries'][$AEId]['EnFirstName']}</td><td>{$Gara['Entries'][$AEId]['EnName']}</td><td>{$Gara['Entries'][$AEId]['EnIocCode']}</td><td>{$Gara['Entries'][$AEId]['EnDivision']}</td><td>{$Gara['Entries'][$AEId]['EnCode']}</td><td>{$Gara['Entries'][$AEId]['EdExtra']}</td></tr>";
					break;
				case 2:
					$Updated[]="<tr><td>{$Codes[$Gara['Entries'][$AEId]['EnTournament']]}</td><td>AccEntries</td><td>{$Gara['Entries'][$AEId]['EnFirstName']}</td><td>{$Gara['Entries'][$AEId]['EnName']}</td><td>{$Gara['Entries'][$AEId]['EnIocCode']}</td><td>{$Gara['Entries'][$AEId]['EnDivision']}</td><td>{$Gara['Entries'][$AEId]['EnCode']}</td><td>{$Gara['Entries'][$AEId]['EdExtra']}</td></tr>";
					break;
			}
		}
	}

	// ExtraData
	foreach($Gara['ExtraData'] as $key=>$record) {
		if($record['EdType']=='Z') continue;
		if(empty($Entries[$key])) {
			// no entries (refused!) so unset this entry
			unset($Gara['ExtraData'][$key]);
			if(!empty($Refused[$key])) $Refused[$key]=str_replace('Entries', 'Entries<br>ExtraData', $Refused[$key]);
		} else {
			$Gara['ExtraData'][$key]['EdId']=$Entries[$key];
			$record['EdId']=$Entries[$key];
			$query=array();
			foreach($record as $k=>$v) {
				$query[]="$k = ".StrSafe_DB($v);
			}
			safe_w_sql("insert into ExtraData set ".implode(',', $query)." on duplicate key update ".implode(',', $query));
			switch(safe_w_affected_rows()) {
				case 1:
					$Inserts[]="<tr><td>{$Codes[$Gara['Entries'][$key]['EnTournament']]}</td><td>ExtraData</td><td>{$Gara['Entries'][$key]['EnFirstName']}</td><td>{$Gara['Entries'][$key]['EnName']}</td><td>{$Gara['Entries'][$key]['EnIocCode']}</td><td>{$Gara['Entries'][$key]['EnDivision']}</td><td>{$Gara['Entries'][$key]['EnCode']}</td><td>{$Gara['Entries'][$key]['EdExtra']}</td></tr>";
					break;
				case 2:
					$Updated[]="<tr><td>{$Codes[$Gara['Entries'][$key]['EnTournament']]}</td><td>ExtraData</td><td>{$Gara['Entries'][$key]['EnFirstName']}</td><td>{$Gara['Entries'][$key]['EnName']}</td><td>{$Gara['Entries'][$key]['EnIocCode']}</td><td>{$Gara['Entries'][$key]['EnDivision']}</td><td>{$Gara['Entries'][$key]['EnCode']}</td><td>{$Gara['Entries'][$key]['EdExtra']}</td></tr>";
					break;
			}
		}
	}

	// Photos
	foreach($Gara['Photos'] as $key=>$record) {
		if(empty($Entries[$key])) {
			// no entries (refused!) so unset this entry
			unset($Gara['Photos'][$key]);
			if(!empty($Refused[$key])) $Refused[$key]=str_replace('Entries', 'Entries<br>Photos', $Refused[$key]);
		} else {
			$Gara['Photos'][$key]['PhEnId']=$Entries[$key];
			$record['PhEnId']=$Entries[$key];
			// look for the photo
			$query=array();
			$q=safe_r_sql("select * from Photos where PhEnId={$Entries[$key]}");
			if($r=safe_fetch($q)) {
				if($record['PhPhoto']==$r->PhPhoto) continue;
				$query[]='PhPhotoEntered=PhPhotoEntered';
				$query[]='PhPhoto='.StrSafe_DB($record['PhPhoto']);
				safe_w_SQL("update Photos set ".implode(',', $query)." where PhEnId={$r->PhEnId}");
				if(safe_w_affected_rows()) {
					$Updated[]="<tr><td>{$Codes[$Gara['Entries'][$key]['EnTournament']]}</td><td>Photos</td><td>{$Gara['Entries'][$key]['EnFirstName']}</td><td>{$Gara['Entries'][$key]['EnName']}</td><td>{$Gara['Entries'][$key]['EnIocCode']}</td><td>{$Gara['Entries'][$key]['EnDivision']}</td><td>{$Gara['Entries'][$key]['EnCode']}</td><td>{$Gara['Entries'][$key]['EdExtra']}</td></tr>";
				}
			} else {
				$query[]='PhPhoto='.StrSafe_DB($record['PhPhoto']);
				$query[]="PhEnId = {$Entries[$key]}";
				safe_w_sql("insert into Photos set ".implode(',', $query));
				$Inserts[]="<tr><td>{$Codes[$Gara['Entries'][$key]['EnTournament']]}</td><td>Photos</td><td>{$Gara['Entries'][$key]['EnFirstName']}</td><td>{$Gara['Entries'][$key]['EnName']}</td><td>{$Gara['Entries'][$key]['EnIocCode']}</td><td>{$Gara['Entries'][$key]['EnDivision']}</td><td>{$Gara['Entries'][$key]['EnCode']}</td><td>{$Gara['Entries'][$key]['EdExtra']}</td></tr>";
			}
		}
	}

	//Gestisce la tabella Flags
	foreach($Gara['Flags'] as $record) {
// 		if($record['FlTournament']!=-1) {
// 			$record['FlTournament']=$TourId;
// 		}
		unset($record['CoId']);
		$query=array();
		foreach($record as $key=>$val) {
			$query[]="$key = " . strsafe_db($val) ;
		}
		safe_w_sql("insert into Flags set ". implode(', ', $query). " on duplicate key update ". implode(', ', $query));
		switch(safe_w_affected_rows()) {
			case 1:
				$Inserts[]="<tr><td>{$Codes[$record['FlTournament']]}</td><td>Flags</td><td>{$record['FlCode']}</td></tr>";
				break;
			case 2:
				$Updated[]="<tr><td>{$Codes[$record['FlTournament']]}</td><td>Flags</td><td>{$record['FlCode']}</td></tr>";
				break;
		}
	}

	// RECREATES ALL MEDIA
	foreach($Gara['Tournaments'] as $k => $v) {
		CheckPictures($v['ToCode']);
	}
	return array(
		'Refused' => $Refused,
		'Updated' => $Updated,
		'Inserts' => $Inserts,
		'EnToDel' => $EnToDel,
		);
}