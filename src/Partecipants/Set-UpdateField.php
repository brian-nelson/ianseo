<?php
/*
													- CheckSession_Par.php -
	La pagina aggiorna controlla se è possibile settare la sessione scelta all'arciere
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Fun_FormatText.inc.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}

	$Errore=IsBlocked(BIT_BLOCK_PARTICIPANT);

	$field=(empty($_REQUEST['field']) ? '' : $_REQUEST['field']);
	$value=(empty($_REQUEST['value']) ? '' : $_REQUEST['value']);
	$session=(empty($_REQUEST['session']) ? '' : $_REQUEST['session']);
	$targetno=(empty($_REQUEST['targetno']) ? '' : $_REQUEST['targetno']);
	$ID=(empty($_REQUEST['id']) ? '0' : $_REQUEST['id']);
	$update=$ID;
	$resetPrintBadge=false;

	if($field and $value) {
		switch($field) {
			case 'subclass':
				$FIELD='EnSubclass';
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
			case 'email':
				$FIELD='EdEmail';
				$value=strtolower($value);
				break;
			default:
				debug_svela($field);
		}

		if($field=='email') {
			if($Errore) {
				$q=safe_r_sql("select $FIELD WhichField from Entries left join ExtraData on EdType='E' and EdId=EnId where EnId=$ID");
				if($r=safe_fetch($q)) $value=$r->WhichField;
				else $value='';
			} else {
				if($ID) {
					// update
					safe_w_sql("insert into ExtraData set $FIELD=".StrSafe_DB($value).  ", EdId=$ID,EdType='E' on duplicate key update $FIELD=".StrSafe_DB($value).  "");
				}
			}
		} else {
			if($Errore) {
				$q=safe_r_sql("select $FIELD WhichField from Entries where EnId=$ID");
				if($r=safe_fetch($q)) $value=$r->WhichField;
				else $value='';
			} else {
				if($ID) {
					// update
					safe_w_sql("update Entries set $FIELD=".StrSafe_DB($value).  ($resetPrintBadge ? ", EnBadgePrinted='0000-00-00 00:00:00'" : "") . " where EnId=$ID");
				} else {
					// insert a new one and gives alert back to the page
					safe_w_sql("insert into Entries set EnTournament={$_SESSION['TourId']}, $FIELD=".StrSafe_DB($value)."");
					$ID=safe_w_last_id();
					safe_w_sql("insert into Qualifications set QuId=$ID".($session ? ", QuSession=$session" . ($targetno ? ", QuTargetno=".StrSafe_DB($session.$targetno) : '') : ''));
				}
			}
		}
	}

	header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . intval($Errore) . '</error>' . "\n";
	print '<update>' . $update . '</update>' . "\n";
	print '<id>'.$ID.'</id>' . "\n";
	print '<field>'.$field.'</field>' . "\n";
	print '<value><![CDATA['.stripslashes($value).']]></value>' . "\n";
	print '</response>' . "\n";
?>
