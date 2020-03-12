<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclAccreditation, AclReadWrite);
require_once('Common/Fun_Number.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once(dirname(__FILE__).'/Lib.php');

CheckTourSession(true);


if (!(isset($_SESSION['chk_Turni']) && is_array($_SESSION['chk_Turni']) && isset($_SESSION['AccOp']) && is_numeric($_SESSION['AccOp']))) {
	header('Location: index.php');
	exit;
}

$OpDescr = '';
$StrConto = '---';
if($_SESSION['AccOp']>=0) {
	$Select = "SELECT AOTDescr FROM AccOperationType WHERE AOTId=" . StrSafe_DB($_SESSION['AccOp']) ;
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)==1) {
		$Row=safe_fetch($Rs);
		$OpDescr=get_text($Row->AOTDescr, 'Tournament');
	}
} else if($_SESSION['AccOp'] == -1) {
	$OpDescr=get_text('TakePicture', 'Tournament') . ' - <a href="IdCard/Configuration.php" style="color:yellow">'.get_text('MenuLM_Setup').'</a>';
}

$SetRap=0;
if(!isset($_SESSION['SetRap'])) $_SESSION['SetRap']=0;

if (!IsBlocked(BIT_BLOCK_ACCREDITATION)) {
    if(isset($_REQUEST['AccreditateAll'])) {
        /// bulk accreditation of the visible ids
	    $Select = getAccrQuery();
        $q=safe_r_sql($Select);
        while($r=safe_fetch($q)) {
	        SetAccreditation($r->EnId, 0, 'RicaricaOpener', 0, $_SESSION['AccOp']);
        }
        CD_redirect('Accreditation.php'.go_get('AccreditateAll', '', true));
    }
    if(isset($_REQUEST['AccreditateNone'])) {
        /// bulk accreditation of the visible ids
	    $Select = getAccrQuery();
        $q=safe_r_sql($Select);
        while($r=safe_fetch($q)) {
	        safe_w_sql("DELETE FROM AccEntries WHERE AEId=$r->EnId AND AEOperation=" . StrSafe_DB($_SESSION['AccOp']) . " AND AETournament=" . StrSafe_DB($_SESSION['TourId']));
        }
        CD_redirect('Accreditation.php'.go_get('AccreditateNone', '', true));
    }
	if(isset($_REQUEST["Command"])) {
		if ($_REQUEST['Command']=='Del') {
			$Sql
				= "DELETE FROM AccEntries "
				. "WHERE AEId=" . StrSafe_DB($_REQUEST['Del']) . " AND AEOperation=" . StrSafe_DB($_SESSION['AccOp']) . " AND AETournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$Rs=safe_w_sql($Sql);
// 			print $Sql;exit;
//            CD_redirect('Accreditation.php'.go_get('Command', '', true));
			header('Location: Accreditation.php');
			exit;
		} elseif ($_REQUEST['Command']=='NoAcc') {
			$Sql = "";

			if ($_REQUEST['NoAcc']==1) {
				$Sql = "UPDATE Entries SET "
					. "EnStatus='7' "
					. "WHERE EnId=" . StrSafe_DB($_REQUEST['Id']). " "
					. "AND EnTournament=" . StrSafe_DB(($_SESSION['TourId'])) . " ";
			} else {
				$Sql = "UPDATE Entries AS e "
					. "LEFT JOIN LookUpEntries AS l ON e.EnCode=l.LueCode AND e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
					. "SET "
					. "e.EnStatus=IFNULL(l.LueStatus,0) "
					. "WHERE e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnId=" . StrSafe_DB($_REQUEST['Id']) . " ";
			}

			$Rs=safe_w_sql($Sql);

			header('Location: Accreditation.php');
			exit;
		} elseif ($_REQUEST['Command']=='CmdOpenBill') {
			$_SESSION['SetRap']=1;
		} elseif ($_REQUEST['Command']=='CmdCloseBill' || $_REQUEST['Command']=='CmdResetBill') {
			if ($_REQUEST['Command']=='CmdCloseBill')
				$_SESSION['SetRap']=0;

			$Update
				= "UPDATE AccEntries SET "
				. "AERapp='0' "
				. "WHERE AEOperation =" . StrSafe_DB($_SESSION['AccOp']) . " "
				. "AND AETournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "AND AEFromIp=INET_ATON(" . StrSafe_DB(($_SERVER['REMOTE_ADDR']=='::1' ? '127.0.0.1':$_SERVER['REMOTE_ADDR'])) .") ";
			$RsUp=safe_w_sql($Update);
			//print $Update;exit;
		}
	}

	$SetRap=$_SESSION['SetRap'];

	if ($SetRap==1) {
		// il conto vale solo per l'accredito (Operazione 1)
		$Select = "SELECT SUM(APPrice) AS Quanto, ToCurrency "
			. "FROM "
			. "Entries INNER JOIN AccEntries ON EnId=AEId AND EnPays=1 AND EnTournament=AETournament AND EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " "
			. "INNER JOIN AccPrice ON CONCAT(EnDivision,EnClass) LIKE APDivClass AND APTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND AERapp='1' "
			. "LEFT JOIN Tournament on EnTournament=ToID "
			. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "  AND AEOperation='1' AND AEFromIP=INET_ATON(" . StrSafe_DB(($_SERVER['REMOTE_ADDR']=='::1' ? '127.0.0.1':$_SERVER['REMOTE_ADDR'])) .") "
			. "GROUP BY ToId ";

		$Rs=safe_r_sql($Select);
		//print $Select;exit;
		if (safe_num_rows($Rs)==1) {
			$Euro = NumFormat(0,2);
			$row=safe_fetch($Rs);
			if (!is_null($row->Quanto)) {
				$Euro=NumFormat($row->Quanto,2);
			}
			$StrConto=get_text('Bill','Tournament') . ': ' . $Euro . '&nbsp;' . $row->ToCurrency;
		}
	}
}

$ONLOAD=' onLoad="javascript:document.Frm.bib.focus()"';
$JS_SCRIPT=array(
	'<script type="text/javascript" src="./Fun_JS.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
	'<style>.Full {width:100%;box-sizing:border-box;}</style>',
	);

include('Common/Templates/head.php');

$Counter = "SELECT
		SUM(IF(AEOperation IS NULL,0,1)) as Presenti,
		SUM(IF(EnStatus=7,0,IF(AEOperation IS NULL,1,0))) as Assenti,
		SUM(IF(EnStatus=7,1,0)) as NonAccreditati,
		COUNT(*) as Totale
		FROM Entries
		INNER JOIN Qualifications ON EnId=QuId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "
		LEFT JOIN AccEntries ON EnId=AEId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AEOperation=" . StrSafe_DB($_SESSION['AccOp']) . "
		LEFT JOIN Photos ON EnId=PhEnId WHERE QuSession IN (" . implode(',', StrSafe_DB($_SESSION['chk_Turni'])) . ")";
$Rs = safe_r_sql($Counter);
$MyRowCounter = safe_fetch($Rs);
?>
<form name="Frm" method="get" action="">
<table class="Tabella">
    <tr>
      <th colspan="5" class="Title"><?php print $OpDescr;?></th>
    </tr>
    <tr>
      <th width="15%"><?php print get_text('Code','Tournament');?></th>
      <th width="42%" colspan="3" class="Center"><?php print get_text('Search','Tournament');?></th>
      <th><?php print get_text('Bill','Tournament');?></th>
    </tr>
    <tr>
      <td class="Center" rowspan="3">
          <div>
            <input type="text" name="bib" id="bib" tabindex="1">&nbsp;<input type="submit" name="Vai" value="<?php print get_text('CmdGo','Tournament');?>" id="Vai" onClick="javascript:SendBib();">
          </div>
          <div>&nbsp;</div>
          <div>
              <input type="submit" name="AccreditateAll" value="<?php echo get_text('AccreditateAll', 'Tournament'); ?>" onclick="return confirm('Sure?')">
              <input type="submit" name="AccreditateNone" value="<?php echo get_text('AccreditateNone', 'Tournament'); ?>" onclick="return confirm('Sure?')">
          </div>
      </td>
      <th width="16%"><?php print get_text('FamilyName','Tournament');?></th>
      <th width="16%"><?php print get_text('Country');?></th>
      <td width="10%" class="Center" rowspan="3">
      <input type="button" name="Cerca" value="<?php print get_text('Search','Tournament');?>" onClick="javascript:Filtra();"><br><br>
      <input type="button" name="TogliFiltro" value="<?php print get_text('CmdRemoveFilter','Tournament');?>" onClick="javascript:ResetFilter();">
      </td>
      <td class="Center LetteraGrande" rowspan="2"><?php print $StrConto;?></td>
    </tr>
    <tr>
      <td class="Center"><input type="text" name="txt_Cognome" id="txt_Cognome" size="32" value="<?php print (isset($_REQUEST['txt_Cognome']) ? $_REQUEST['txt_Cognome'] : '');?>"></td>
      <td class="Center"><input type="text" name="txt_Societa" id="txt_Societa" size="32" value="<?php print (isset($_REQUEST['txt_Societa']) ? $_REQUEST['txt_Societa'] : '');?>"></td>
    </tr>
    <tr>
      <td class="Bold"><input type="checkbox" name="RemoveAcc" id="RemoveAcc" value="1"<?php print (isset($_REQUEST['RemoveAcc']) && $_REQUEST['RemoveAcc']==1 ? ' checked' : '');?>>&nbsp;<?php print get_text('HiddenCredited','Tournament');?></td>
      <td><?php
      	echo get_text('Credited', 'Tournament') . ": " . $MyRowCounter->Presenti . " (" . $MyRowCounter->NonAccreditati . ")<br/>" . get_text('NoAcc', 'Tournament') . ": " . $MyRowCounter->Assenti;
      	?></td>
      <td class="Center">
<?php
	if ($_SESSION['AccOp']==1)	// solo se sto facendo l'accredito posso gestire il conto
	{
		$cmd = ($SetRap==0 ? 'CmdOpenBill' : 'CmdCloseBill');
		$fld = '<input type="hidden" name="Command" value="">';
		$sub = '<input type="submit" value="' . get_text($cmd, 'Tournament') . '" onclick="document.Frm.Command.value=\''.$cmd.'\';">&nbsp;';
		if ($SetRap==1)
		{
			$sub.= '<input type="submit" value="' . get_text('CmdResetBill', 'Tournament') . '" onclick="document.Frm.Command.value=\'CmdResetBill\';">&nbsp;';
			$sub.= '<input type="button" value="' . get_text('CmdDetailsBill', 'Tournament') . '" onClick="document.Frm.Command.value=\'CmdDetailsBill\';OpenPopup(\'DetailsBill.php\',\'ResetBill\',800,600);">&nbsp;';
		}
		print $fld . $sub;
	}
	else
		print '&nbsp;';
?>
      </td>
    </tr>

</table>

</form>
<table class="Tabella">
<tr>
<th width="7%"><?php print get_text('Code','Tournament');?></th>
<th width="3%"><?php print get_text('Session');?></th>
<th width="5%"><?php print get_text('Target');?></th>
<th width="15%"><?php print get_text('Archer');?></th>
<th width="20%"><?php print get_text('Country');?></th>
<th width="5%"><?php print get_text('Division');?></th>
<th width="5%"><?php print get_text('Class');?></th>
<th width="5%"><?php print get_text('IndClEvent', 'Tournament');?></th>
<th width="5%"><?php print get_text('TeamClEvent', 'Tournament');?></th>
<th width="5%"><?php print get_text('IndFinEvent', 'Tournament');?></th>
<th width="5%"><?php print get_text('TeamFinEvent', 'Tournament');?></th>
<th width="5%"><?php print get_text('MixedTeamFinEvent', 'Tournament');?></th>
<th width="5%"><?php print get_text('Pay','Tournament');?></th>
<th width="10%"><?php print get_text('AccrNoShow','Tournament');?></th>
</tr>
<?php

	$Select = getAccrQuery();

	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0) {
		switch($_SESSION['AccOp']) {
			case '2':
				$DelAccr=get_text('CancelEquipment','Tournament');
				break;
			case '3':
				$DelAccr=get_text('CancelPayment','Tournament');
				break;
			default:
				$DelAccr=get_text('CancelAcc','Tournament');
		}

		while ($MyRow=safe_fetch($Rs)) {
			$Class="";
			if(($_SESSION['AccOp']>=0 && is_null($MyRow->AEOperation)) || ($_SESSION['AccOp']==-1 && is_null($MyRow->PhEnId))) {
				if($MyRow->EnStatus!=7) {
					$Class=' class="warning"';
				} else {
					$Class=' class="error"';
				}
			}
			if($_SESSION['chk_Photo'] and !$MyRow->HasPhoto) {
				$Class=' class="error"';
			}
			if($_SESSION['chk_Paid']==1 and !$MyRow->HasPaid) {
				$Class=' class="error"';
			}
			if($_SESSION['chk_Accredited']==1 and !$MyRow->IsAccredited) {
				$Class=' class="error"';
			}

			print '<tr' . $Class  . '>';

			print '<td>';
			if ($MyRow->EnStatus!=7) {
				print '<input class="Full" type="button" onclick="' . ($_SESSION['AccOp']>=0 ? "SendId" : "getImage"). '(' . $MyRow->EnId . ');" value="' . ($MyRow->EnCode!='' ? $MyRow->EnCode : "- " . get_text('Archer') . " -") . '">';
			} else {
				print ($MyRow->EnCode!='' ? $MyRow->EnCode : "- " . get_text('Archer') . " -");
			}
			print '</td>';
			print '<td class="Center">' . $MyRow->QuSession . '</td>';
			print '<td class="Center">' . $MyRow->TargetNo . '</td>';
			print '<td>' . $MyRow->EnFirstName . ' ' . $MyRow->EnName . (!is_null($MyRow->AEOperation) ? ' <a class="Link" href="' . $_SERVER['PHP_SELF'] . '?Command=Del&amp;Del=' . $MyRow->EnId . '">[' . $DelAccr . ']</a>':'') . '</td>';
			print '<td>' . $MyRow->CoCode . ' - ' . $MyRow->CoName . '</td>';
			print '<td class="Center">' . $MyRow->EnDivision . '</td>';
			print '<td class="Center">' . $MyRow->EnClass . '</td>';
			print '<td class="Center">' . ($MyRow->EnIndClEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
			print '<td class="Center">' . ($MyRow->EnTeamClEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
			print '<td class="Center">' . ($MyRow->EnIndFEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
			print '<td class="Center">' . ($MyRow->EnTeamFEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
			print '<td class="Center">' . ($MyRow->EnTeamMixEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
			print '<td class="Center">' . ($MyRow->EnPays=='1' ? get_text('Yes'): get_text('No')) . '</td>';
			print '<td class="Center">';
			if ($_SESSION['AccOp'] != -1 && is_null($MyRow->AEOperation))
				print '<b><a href="#" onClick="javascript:SetAcc(' . $MyRow->EnId . ',' . ($MyRow->EnStatus!=7 ? 1 : 0) . ');">' . ($MyRow->EnStatus!=7 ? get_text('AccrNoShow','Tournament') : get_text('Acc','Tournament')) . '</a></b>';
			else
				print '&nbsp;';
			print '</td>';
			print '</tr>' . "\n";
		}
	}
?>
</table>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>