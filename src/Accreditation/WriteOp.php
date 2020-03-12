<?php
//XXX Sarebbe il caso di avvisare nel caso non siano settate div o cl o clg della persona
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
checkACL(AclAccreditation, AclReadWrite);
require_once('Common/Fun_FormatText.inc.php');
require_once(dirname(__FILE__).'/Lib.php');

if (!isset($_REQUEST['Id']) && !isset($_REQUEST['bib'])) {
	printcrackerror();
}

if (!(isset($_SESSION['chk_Turni']) && is_array($_SESSION['chk_Turni']) &&
	isset($_SESSION['AccOp']) && is_numeric($_SESSION['AccOp'])))
{
	header('Location: index.php');
	exit;
}

$OpDescr = '';
$Select
	= "SELECT AOTDescr "
	. "FROM AccOperationType "
	. "WHERE AOTId=" . StrSafe_DB($_SESSION['AccOp']) . " ";
$Rs=safe_r_sql($Select);

if (safe_num_rows($Rs)==1)
{
	$Row=safe_fetch($Rs);
	$OpDescr=get_text($Row->AOTDescr,'Tournament');
}

$Turni = "";
/*
	Elenco dei turni per la query
*/
foreach ($_SESSION['chk_Turni'] as $Value) {
	$Turni .= StrSafe_DB($Value) . ",";
}

$Turni=substr($Turni,0,-1);

$Id = -1;
$bib = '';
$NoArcher=false;

if (isset($_REQUEST['Id'])) {
	$Id = $_REQUEST['Id'];
} else {
	$Id = CheckAccreditationCode($_REQUEST['bib'], array($_SESSION['TourId'] => $_SESSION['chk_Turni']));
}

// vale 1 se il conto è aperto
$SetRap = (isset($_SESSION['SetRap']) ? $_SESSION['SetRap'] : 0);

$RicaricaOpener=false;
if (!IsBlocked(BIT_BLOCK_ACCREDITATION)) {
    if (isset($_REQUEST['Command'])) {
        if ($_REQUEST['Command']=='EXEC') {
            if($_SESSION['chk_Paid']==2) {
                // 3 is the code for payment
                $RicaricaOpener=SetAccreditation($Id, 0, 'RicaricaOpener', 0, 3);
            }
            if($_SESSION['chk_Accredited']==2) {
                // 1 is the code for accreditation
                $RicaricaOpener=SetAccreditation($Id, 0, 'RicaricaOpener', 0, 1);
            }
            $RicaricaOpener=SetAccreditation($Id, $SetRap);
        }
    }
}

$Select = getAccrQuery($Id);
$Rs=safe_r_sql($Select);

$MyRow=NULL;
$NoAtleta = false;

$JS_SCRIPT=array(
	'<script type="text/javascript" src="Fun_JS.js"></script>',
	'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
	'<style>#PopupContent, #AccTable {height:100%; box-sizing:border-box;} .error {background-color:red;padding:5em 1em;}</style>',
	'',
	);

if (safe_num_rows($Rs)==1) {
	$MyRow=safe_fetch($Rs);

	// CHECK ALL THE ERROR CONDITIONS!!!
	$Class='';
	if($_SESSION['chk_Photo'] and !$MyRow->HasPhoto) {
		$Class='error';
	}
	if($_SESSION['chk_Paid']==1 and !$MyRow->HasPaid) {
		$Class='error';
	}
	if($_SESSION['chk_Accredited']==1 and !$MyRow->IsAccredited) {
		$Class='error';
	}

	// RowStyle definition
	$RowStyle='';
	switch ($MyRow->EnStatus) {
		case '0':
			$RowStyle = '';
			break;
		case '1':
			$RowStyle = 'CanShoot';
			break;
		case '8':
			$RowStyle = 'CouldShoot';
			break;
		case '9':
			$RowStyle = 'NoShoot';
			break;
	}

	$Button='';
	$SelectButton=false;

	if (is_null($MyRow->AEOperation) && $MyRow->EnStatus!=7) {
		if($Class) {
			$Button= '<tr><td colspan="12" class="Center"><input type="button" name="Submit" value="' . get_text('Close') . '" onclick="javascript:window.close();"></td></tr>' . "\n";
		} elseif ($MyRow->EnStatus<7) {
			$Button= '<tr><td colspan="12" class="Center"><input type="submit" name="Submit" value="' . get_text('CmdExec','Tournament') . '" onclick="document.Frm.Command.value=\'EXEC\'"></td></tr>' . "\n";
			$SelectButton=true;
		} else {
			$Button= '<tr><td colspan="12" class="Bold">' . get_text('Status_'.$MyRow->EnStatus) . '</td></tr>' . "\n";
			$Button.= '<tr><td colspan="12" class="Center"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?Command=EXEC&Id=' . $Id . '">' . get_text('CmdExec','Tournament') . '</a></td></tr>' . "\n";
		}
	} else {
		$Button= '<tr><td colspan="12" class="Center"><input type="button" name="Submit" value="' . get_text('Close') . '" onclick="javascript:window.close();"></td></tr>' . "\n";
        $SelectButton=true;
	}
}

$ONLOAD=($RicaricaOpener ? ' onLoad="javascript:ReloadOpener(true);"' : ($SelectButton ? ' onLoad="javascript:document.Frm.Submit.focus();"' : ''));
include('Common/Templates/head-popup.php');

if($MyRow) {

	echo '<div id="AccTable" class="'.$Class.'">';

	echo '<form name="Frm" method="POST" action="">
		<input type="hidden" name="Id" id="Id" value="'.$Id.'">
		<input type="hidden" name="Command" value="">';

	?>
	<table class="Tabella">
	<tr><th class="Title" colspan="12"><?php print $OpDescr;?></th></tr>
	<tr>
	<th width="7%"><?php print get_text('Code','Tournament');?></th>
	<th width="3%"><?php print get_text('Session');?></th>
	<th width="5%"><?php print get_text('Target');?></th>
	<th width="28%"><?php print get_text('Archer');?></th>
	<th width="22%"><?php print get_text('Country');?></th>
	<th width="5%"><?php print get_text('Division');?></th>
	<th width="5%"><?php print get_text('Class');?></th>
	<th width="5%"><?php print get_text('IndClEvent', 'Tournament');?></th>
	<th width="5%"><?php print get_text('TeamClEvent', 'Tournament');?></th>
	<th width="5%"><?php print get_text('IndFinEvent', 'Tournament');?></th>
	<th width="5%"><?php print get_text('TeamFinEvent', 'Tournament');?></th>
	<th width="5%"><?php print get_text('MixedTeamFinEvent', 'Tournament');?></th>
	<?php
	print '<tr class="' . $RowStyle . '">';
	print '<td>' . $MyRow->EnCode . '</td>';
	print '<td class="Center">' . $MyRow->QuSession . '</td>';
	print '<td class="Center">' . $MyRow->TargetNo . '</td>';
	print '<td>' . $MyRow->EnFirstName . ' ' . $MyRow->EnName . '</td>';
	print '<td>' . $MyRow->CoCode . ' - ' . $MyRow->CoName . '</td>';
	print '<td class="Center">' . $MyRow->EnDivision . '</td>';
	print '<td class="Center">' . $MyRow->EnClass . '</td>';
	print '<td class="Center">' . ($MyRow->EnIndClEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
	print '<td class="Center">' . ($MyRow->EnTeamClEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
	print '<td class="Center">' . ($MyRow->EnIndFEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
	print '<td class="Center">' . ($MyRow->EnTeamFEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
	print '<td class="Center">' . ($MyRow->EnTeamMixEvent=='1' ? get_text('Yes'): get_text('No')) . '</td>';
	print '</tr>' . "\n";
	print '<tr>';
	print '<td colspan="12" class="Center Bold">' . (!is_null($MyRow->AEOperation) ? get_text('Credited','Tournament') : ($MyRow->EnStatus!=7 ? '&nbsp;' : get_text('NoAcc','Tournament'))) . '</td>';
	print '</tr>' . "\n";

	if (is_null($MyRow->AEOperation) && $MyRow->EnStatus!=7) {
		if($Class) {
			print '<tr><td colspan="12" class="Center"><input type="button" name="Submit" value="' . get_text('Close') . '" onclick="javascript:window.close();"></td></tr>' . "\n";
		} elseif ($MyRow->EnStatus<7) {
			print '<tr><td colspan="12" class="Center"><input type="submit" name="Submit" value="' . get_text('CmdExec','Tournament') . '" onclick="document.Frm.Command.value=\'EXEC\'"></td></tr>' . "\n";
		} else {
			print '<tr><td colspan="12" class="Bold">' . get_text('Status_'.$MyRow->EnStatus) . '</td></tr>' . "\n";
			print '<tr><td colspan="12" class="Center"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?Command=EXEC&Id=' . $Id . '">' . get_text('CmdExec','Tournament') . '</a></td></tr>' . "\n";
		}
	} else {
		print '<tr><td colspan="12" class="Center"><input type="button" name="Submit" value="' . get_text('Close') . '" onclick="javascript:window.close();"></td></tr>' . "\n";
	}

	echo '</table>
		</form>';
	echo '</div>';


} else {
	print '<table class="Tabella">' . "\n";
	print '<tr>';
	print '<td colspan="12" class="Center Bold">' . get_text('ArcherNotFound','Tournament') . '</td>';
	print '</tr>' . "\n";
	print '<tr><td colspan="12" class="Center"><input type="button" name="Submit" value="' . get_text('Close') . '" onClick="javascript:window.close();"></td></tr>' . "\n";
	print '</table>' . "\n";
}


include('Common/Templates/tail-popup.php');
