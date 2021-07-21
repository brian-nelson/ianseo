<?php
/*
													- CheckSession_Par.php -
	La pagina aggiorna controlla se è possibile settare la sessione scelta all'arciere
*/

define('debug',false);

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Fun_Various.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Partecipants/Fun_Partecipants.local.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

if (!CheckTourSession()) {
	print get_text('CrackError');
	exit;
}
checkACL(AclParticipants, AclReadWrite, false);

$Errore=IsBlocked(BIT_BLOCK_PARTICIPANT);

$field=(empty($_REQUEST['field']) ? '' : $_REQUEST['field']);
$value=(empty($_REQUEST['value']) ? '' : trim(htmlspecialchars_decode($_REQUEST['value'])));
$session=(empty($_REQUEST['session']) ? '' : $_REQUEST['session']);
$targetno=(empty($_REQUEST['targetno']) ? '' : $_REQUEST['targetno']);
$ID=(empty($_REQUEST['id']) ? '0' : $_REQUEST['id']);
$update=$ID;
$resetPrintBadge=false;
$UpdateTimestamp=true;

$SelectEnId='';
if($ID and $_SESSION['AccBooth']) {
	$q=safe_r_sql("select EnCode, EnIocCode, EnDivision, ToCode from Entries inner join Tournament on EnTournament=ToId where EnId=$ID");
	if($ENTRY=safe_fetch($q)) {
		$SelectEnId="select EnId from Entries where EnCode='$ENTRY->EnCode' and EnIocCode='$ENTRY->EnIocCode' and EnDivision='$ENTRY->EnDivision' and EnTournament=§TOCODETOID§";
	}
}

$Recalc=false;
if($field) {
	switch($field) {
		case 'subclass':
			$FIELD='EnSubclass';
			$Recalc=true;
			break;
		case 'division':
			$FIELD='EnDivision';
			$resetPrintBadge=true;
			$Recalc=true;
			break;
		case 'ageclass':
			$FIELD='EnAgeClass';
			$resetPrintBadge=true;
			$Recalc=true;
			break;
		case 'class':
			$FIELD='EnClass';
			$resetPrintBadge=true;
			$Recalc=true;
			break;
		case 'firstname':
			$FIELD='EnFirstname';
			$resetPrintBadge=true;
			$value=AdjustCaseTitle($value);
			break;
		case 'name':
			$FIELD='EnName';
			$resetPrintBadge=true;
			$value=AdjustCaseTitle($value);
			break;
		case 'tvname':
			$FIELD='EnOdfShortname';
			break;
		case 'email':
			$FIELD='EdEmail';
			$value=strtolower($value);
			break;
		case 'caption':
			$resetPrintBadge=true;
		case 'localCode':
			$FIELD='EdExtra';
			break;
		case 'EnOnlineId':
		case 'EnIocCode':
		case 'EnBadgePrinted':
		case 'EnWChair':
		case 'EnSitting':
		case 'EnDoubleSpace':
		case 'EnPays':
			$UpdateTimestamp=false;
			break;
		default:
			$Errore=1;
	}

	if($field=='email') {
		if($Errore) {
			$q=safe_r_sql("select $FIELD WhichField from Entries left join ExtraData on EdType='E' and EdId=EnId where EnId=$ID");
			if($r=safe_fetch($q)) $value=$r->WhichField;
			else $value='';
		} else {
			if($ID) {
				// update
				safe_w_sql("insert into ExtraData set $FIELD=".StrSafe_DB($value).  ", EdId=$ID, EdType='E' on duplicate key update $FIELD=".StrSafe_DB($value).  "");
				$up=safe_w_affected_rows();
				if($SelectEnId) {
					LogAccBoothQuerry("insert into ExtraData set $FIELD=".StrSafe_DB($value).  ", EdId=($SelectEnId), EdType='E' on duplicate key update $FIELD=".StrSafe_DB($value), $ENTRY->ToCode);
				}
				if($up) {
					// updates the entry timestamp as well
					safe_w_SQL("update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where EnId={$ID}");
					if($SelectEnId) {
						LogAccBoothQuerry("update Entries set EnTimestamp='" . date('Y-m-d H:i:s') . "' where EnId=($SelectEnId)");
					}
				}
			}
		}
	} elseif($field=='localCode') {
		if($Errore) {
			$q=safe_r_sql("select $FIELD WhichField from Entries left join ExtraData on EdType='Z' and EdId=EnId where EnId=$ID");
			if($r=safe_fetch($q)) $value=$r->WhichField;
			else $value='';
		} else {
			if($ID) {
				// update
				safe_w_sql("insert into ExtraData set $FIELD=".StrSafe_DB($value).  ", EdId=$ID, EdType='Z' on duplicate key update $FIELD=".StrSafe_DB($value).  "");
				if($SelectEnId) {
					LogAccBoothQuerry("insert into ExtraData set $FIELD=".StrSafe_DB($value).  ", EdId=($SelectEnId), EdType='Z' on duplicate key update $FIELD=".StrSafe_DB($value), $ENTRY->ToCode);
				}
			}
		}
	} elseif($field=='caption') {
		if($Errore) {
			$q=safe_r_sql("select $FIELD WhichField from Entries left join ExtraData on EdType='C' and EdId=EnId where EnId=$ID");
			if($r=safe_fetch($q)) $value=$r->WhichField;
			else $value='';
		} else {
			if($ID) {
				// update
				if($value) {
					safe_w_sql("insert into ExtraData set $FIELD=".StrSafe_DB($value).  ", EdId=$ID, EdType='C' on duplicate key update $FIELD=".StrSafe_DB($value).  "");
					$Affected=safe_w_affected_rows();
					if($SelectEnId) {
						LogAccBoothQuerry("insert into ExtraData set $FIELD=".StrSafe_DB($value).  ", EdId=($SelectEnId), EdType='C' on duplicate key update $FIELD=".StrSafe_DB($value), $ENTRY->ToCode);
					}
				} else {
					safe_w_sql("delete from ExtraData where EdId=$ID and EdType='C'");
					$Affected=safe_w_affected_rows();
					if($SelectEnId) {
						LogAccBoothQuerry("delete from ExtraData where EdId=($SelectEnId) and EdType='C'", $ENTRY->ToCode);
					}
				}
				if($Affected) {
					safe_w_sql("update Entries set EnBadgePrinted=0 where EnId=$ID");
					if($SelectEnId) {
						LogAccBoothQuerry("update Entries set EnBadgePrinted=0 where EnCode='$ENTRY->EnCode' and EnIocCode='$ENTRY->EnIocCode' and EnDivision='$ENTRY->EnDivision' and EnTournament=§TOCODETOID§", $ENTRY->ToCode);
					}
				}
			}
		}
	} else {
		if($Errore) {
			$q=safe_r_sql("select $FIELD WhichField from Entries where EnId=$ID");
			if($r=safe_fetch($q)) $value=$r->WhichField;
			else $value='';
		} else {
			if($ID) {
				// get old settings
				if($Recalc and $x=Params4Recalc($ID)) {
					list($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld,$zeroOld)=$x;
				}
				// update
				safe_w_sql("update Entries set $FIELD=".StrSafe_DB($value)
					.  ($resetPrintBadge ? ", EnBadgePrinted=0" : "")
					.  ($UpdateTimestamp ? '' : ", EnTimestamp=EnTimestamp")
					. " where EnId=$ID");
				if($SelectEnId) {
					LogAccBoothQuerry("update Entries set $FIELD=".StrSafe_DB($value)
						.  ($resetPrintBadge ? ", EnBadgePrinted=0" : "")
						.  ($UpdateTimestamp ? '' : ", EnTimestamp=EnTimestamp")
						. " where EnCode='$ENTRY->EnCode' and EnIocCode='$ENTRY->EnIocCode' and EnDivision='$ENTRY->EnDivision' and EnTournament=§TOCODETOID§", $ENTRY->ToCode);
				}

				switch($field) {
					case 'division':
					case 'ageclass':
					case 'class':
						// resets ageclass if not coherent with the new division
						$SQL="select EnDob+0 as HasAgeClass, AgeClass.ClId is not null as AgeCompatible, ShootClass.ClId is not null as ShootCompatible, DivAthlete and ShootClass.ClAthlete as IsAthlete 
							from Entries 
							inner join Tournament on ToId=EnTournament
							inner join Divisions on DivId=EnDivision and DivTournament=EnTournament
							left join Classes AgeClass on AgeClass.ClId=EnAgeClass 
								and AgeClass.ClTournament=EnTournament 
								and (AgeClass.ClDivisionsAllowed='' or find_in_set(DivId, AgeClass.ClDivisionsAllowed)) 
								and (AgeClass.ClSex=-1 or AgeClass.ClSex=EnSex)
								and (EnDob=0 or year(ToWhenTo)-year(EnDob) between AgeClass.ClAgeFrom and AgeClass.ClAgeTo)
							left join Classes ShootClass on ShootClass.ClId=EnClass 
								and ShootClass.ClTournament=EnTournament 
								and (ShootClass.ClDivisionsAllowed='' or find_in_set(DivId, ShootClass.ClDivisionsAllowed)) 
								and (ShootClass.ClSex=-1 or ShootClass.ClSex=EnSex)
								and (EnDob=0 or year(ToWhenTo)-year(EnDob) between ShootClass.ClAgeFrom and ShootClass.ClAgeTo)
							where EnId=$ID";
						$q=safe_r_sql($SQL);
						if($r=safe_fetch($q)) {
							$sql=array();
							$sql[]="EnAthlete=".intval($r->IsAthlete);
							if($r->HasAgeClass and !$r->AgeCompatible) {
								// needs to reset Age Class
								$sql[]="EnAgeClass=''";
							}
							if(!$r->ShootCompatible) {
								// reset Shooting Class
								$sql[]="EnClass=''";
							}
							safe_w_sql("update Entries set ".implode(',', $sql)." where EnId=$ID");

							if($SelectEnId) {
								LogAccBoothQuerry("update Entries set ".implode(',', $sql)." where EnCode='$ENTRY->EnCode' and EnIocCode='$ENTRY->EnIocCode' and EnDivision='$ENTRY->EnDivision' and EnTournament=§TOCODETOID§", $ENTRY->ToCode);
							}
						} else {
							$Errore=1;
						}
						break;
				}

				if($Recalc and $x=Params4Recalc($ID)) {
					list($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero)=$x;

					// ricalcolo il vecchio e il nuovo
					RecalculateShootoffAndTeams($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld,$zeroOld);
					RecalculateShootoffAndTeams($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero);

					// rank di classe x tutte le distanze
					$q="SELECT ToNumDist FROM Tournament WHERE ToId={$_SESSION['TourId']}";
					$r=safe_r_sql($q);
					$tmpRow=safe_fetch($r);
					for ($i=0; $i<$tmpRow->ToNumDist; $i++) {
						CalcQualRank($i,$divOld.$clOld);
						CalcQualRank($i,$div.$cl);
					}

					// individuale abs
					MakeIndAbs();

				}
			} else {
				// insert a new one and gives alert back to the page
				safe_w_sql("insert into Entries set EnTournament={$_SESSION['TourId']}, $FIELD=".StrSafe_DB($value)."");
				$ID=safe_w_last_id();
				safe_w_sql("insert into Qualifications set QuId=$ID".($session ? ", QuSession=$session" . ($targetno ? "QuTarget=".intval($targetno).", QuLetter='".substr($targetno, -1)."', QuTargetno=".StrSafe_DB($session.$targetno) : '') : ''));

				if($_SESSION['AccBooth']) {
					LogAccBoothQuerry("insert into Entries set EnTournament=§TOCODETOID§, $FIELD=".StrSafe_DB($value), $_SESSION['TourCode']);
					LogAccBoothQuerry("insert into Qualifications set QuId=($SelectEnId) ".($session ? ", QuSession=$session" . ($targetno ? "QuTarget=".intval($targetno).", QuLetter='".substr($targetno, -1)."', QuTargetno=".StrSafe_DB($session.$targetno) : '') : ''), $_SESSION['TourCode']);
				}
			}
			checkAgainstLUE($ID);
		}
	}
}

header('Content-Type: text/xml');

print '<response>';
print '<error>' . intval($Errore) . '</error>';
print '<update>' . $update . '</update>';
print '<id>'.$ID.'</id>';
print '<field>'.$field.'</field>';
print '<value><![CDATA['.stripslashes($value).']]></value>';
print '</response>';

