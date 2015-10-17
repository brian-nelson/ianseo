<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants/Fun_AJAX_SetTarget_default.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants/Fun_JS.js"></script>',
		);

	$PAGE_TITLE=get_text('ManualTargetAssignment','Tournament');

	include('Common/Templates/head.php');
?>
<form name="Frm" method="GET" action="">
<?php
	$QueryString = $_SERVER['QUERY_STRING'];
	$Arr = explode('&',$QueryString);
	foreach ($Arr as $Key => $Value)
	{
		list($ff,$vv)=explode('=',$Value);
		print '<input type="hidden" name="' . $ff . '" value="' . $vv . '">';
	}
?>
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('ManualTargetAssignment','Tournament');?></th></tr>
<tr class="Divider"><TD  colspan="2"></TD></tr>
<TR><TD width="70%">
<?php
	$MaxSession = 0;
	print get_text('SelectSession','Tournament') . ': ';

	$sessions=GetSessions('Q');

	$MaxSession=count($sessions);

	$ComboSes = array(0 => '--');

	if ($MaxSession>0)
	{
		foreach ($sessions as $s)
		{
			print '<a class="Link" href="' . $_SERVER['PHP_SELF'] . '?' . (isset($_REQUEST['Event']) ? 'Event=' . $_REQUEST['Event'] . '&amp;' : '') . 'Ses=' . $s->SesOrder . '">' . ($_REQUEST['Ses']==$s->SesOrder ? '[' : '') . $s->SesOrder . ($_REQUEST['Ses']==$s->SesOrder ? ']' : '') . '</a> ';
			$ComboSes[$s->SesOrder]=$s->Descr;
		}

		print '<a class="Link" href="' . $_SERVER['PHP_SELF'] . '?' . (isset($_REQUEST['Event']) ? 'Event=' . $_REQUEST['Event'] . '&amp;' : '') . 'Ses=*">' . ($_REQUEST['Ses']=='*' ? '[' : '')  . get_text('AllsF','Tournament') . ($_REQUEST['Ses']=='*' ? ']' : '') . '</a> ';
	}
	else
	{
		exit;
	}
?>
</TD>
<td><?php print get_text('FilterOnDivCl','Tournament');?>:&nbsp;
<input type="text" name="Event" id="Event" value="<?php print (isset($_REQUEST['Event']) ? $_REQUEST['Event'] : '');?>">&nbsp;
<input type="submit" value="<?php print get_text('CmdOk');?>">
</td>
</TR>
<tr class="Divider"><td colspan="2"></td></tr>
<tr><td class="Bold" colspan="2"><input type="checkbox" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1"><?php echo get_text('CmdBlocAutoSave') ?></td></tr>
</table>
</form>
<br>
<?php
	if (isset($_REQUEST['Ses']) && ((is_numeric($_REQUEST['Ses']) && $_REQUEST['Ses']>0 && $_REQUEST['Ses']<=$MaxSession) || (!is_numeric($_REQUEST['Ses']) && $_REQUEST['Ses']=='*')))
	{

		print '<table class="Tabella">' . "\n";
		print '<tr>';
		print '<th class="Title" width="14%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?' . (isset($_REQUEST['Event']) ? 'Event=' . $_REQUEST['Event'] . '&amp;' : '') . 'Ses=' . $_REQUEST['Ses'] . '&amp;ordTarget=' . (isset($_REQUEST['ordTarget']) ? ( $_REQUEST['ordTarget']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Session') . '</a></th>';
		print '<th class="Title" width="6%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?' . (isset($_REQUEST['Event']) ? 'Event=' . $_REQUEST['Event'] . '&amp;' : '') . 'Ses=' . $_REQUEST['Ses'] . '&amp;ordTarget=' . (isset($_REQUEST['ordTarget']) ? ( $_REQUEST['ordTarget']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Target') . '</a></th>';
		print '<th class="Title" width="6%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?' . (isset($_REQUEST['Event']) ? 'Event=' . $_REQUEST['Event'] . '&amp;' : '') . 'Ses=' . $_REQUEST['Ses'] . '&amp;ordCode=' . (isset($_REQUEST['ordCode']) ? ( $_REQUEST['ordCode']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Code','Tournament') . '</a></th>';
		print '<th class="Title" width="24%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?' . (isset($_REQUEST['Event']) ? 'Event=' . $_REQUEST['Event'] . '&amp;' : '') . 'Ses=' . $_REQUEST['Ses'] . '&amp;ordName=' . (isset($_REQUEST['ordName']) ? ( $_REQUEST['ordName']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Athlete') . '</a></th>';
		print '<th class="Title" colspan="2" width="30%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?' . (isset($_REQUEST['Event']) ? 'Event=' . $_REQUEST['Event'] . '&amp;' : '') . 'Ses=' . $_REQUEST['Ses'] . '&amp;ordCountry=' . (isset($_REQUEST['ordCountry']) ? ($_REQUEST['ordCountry']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Country') . '</a></th>';
		print '<th class="Title" width="10%">' . get_text('WheelChair', 'Tournament') . '</th>';
		print '<th class="Title" width="10%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?' . (isset($_REQUEST['Event']) ? 'Event=' . $_REQUEST['Event'] . '&amp;' : '') . 'Ses=' . $_REQUEST['Ses'] . '&amp;ordDiv=' . (isset($_REQUEST['ordDiv']) ? ($_REQUEST['ordDiv']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Division') . '</a></th>';
		print '<th class="Title" width="10%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?' . (isset($_REQUEST['Event']) ? 'Event=' . $_REQUEST['Event'] . '&amp;' : '') . 'Ses=' . $_REQUEST['Ses'] . '&amp;ordCl=' . (isset($_REQUEST['ordCl']) ? ($_REQUEST['ordCl']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Class') . '</a></th>';
		print '</tr>' . "\n";

		$OrderBy = "QuSession ASC,QuTargetNo ASC ";

		if (isset($_REQUEST['ordTarget']) && ($_REQUEST['ordTarget']=='ASC' || $_REQUEST['ordTarget']=='DESC'))
			$OrderBy = "QuSession " . $_REQUEST['ordTarget'] . ",QuTargetNo " . $_REQUEST['ordTarget'] . " ";
		elseif (isset($_REQUEST['ordCode']) && ($_REQUEST['ordCode']=='ASC' || $_REQUEST['ordCode']=='DESC'))
			$OrderBy = "EnCode " . $_REQUEST['ordCode'] . " ";
		elseif (isset($_REQUEST['ordName']) && ($_REQUEST['ordName']=='ASC' || $_REQUEST['ordName']=='DESC'))
			$OrderBy = "EnFirstName " . $_REQUEST['ordName'] . ",EnName " . $_REQUEST['ordName'] . " ";
		elseif (isset($_REQUEST['ordCountry']) && ($_REQUEST['ordCountry']=='ASC' || $_REQUEST['ordCountry']=='DESC'))
			$OrderBy = "EnCountry " . $_REQUEST['ordCountry'] . " ";
		elseif (isset($_REQUEST['ordDiv']) && ($_REQUEST['ordDiv']=='ASC' || $_REQUEST['ordDiv']=='DESC'))
			$OrderBy = "EnDivision " . $_REQUEST['ordDiv'] . " ";
		elseif (isset($_REQUEST['ordCl']) && ($_REQUEST['ordCl']=='ASC' || $_REQUEST['ordCl']=='DESC'))
			$OrderBy = "EnClass " . $_REQUEST['ordCl'] . " ";


		$Select
			= "SELECT EnId,EnCode,EnName,EnFirstName,EnSex,EnId,EnTournament,EnDivision,EnClass,EnCountry,EnStatus, EnWChair, "
			. "CoCode,CoName,QuSession, SUBSTRING(QuTargetNo,2) AS TargetNo "
			. "FROM Entries INNER JOIN Qualifications ON EnId=QuId "
			. "INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
			. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1  "
			. ($_REQUEST['Ses']!='*' ? "AND ((QuSession=0 AND QuTargetNo='') OR QuSession=" . StrSafe_DB($_REQUEST['Ses']) . ") " : '') . " ";
			if(isset($_REQUEST["Event"]) && preg_match("/^[0-9A-Z_]{1,4}$/i",$_REQUEST["Event"]))
				$Select.= " AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE " . StrSafe_DB($_REQUEST["Event"]). " ";
			//. "ORDER BY QuSession ASC,QuTargetNo ASC ";
			$Select.= "ORDER BY " . $OrderBy;
		$Rs=safe_r_sql($Select);
	//	print $Select;

		if (safe_num_rows($Rs)>0)
		{
			while ($MyRow=safe_fetch($Rs))
			{
				$RowStyle='';
				switch ($MyRow->EnStatus)
				{
					case 0:
						$RowStyle = '';
						break;
					case 1:
						$RowStyle = 'CanShoot';
						break;
					case 5:
						$RowStyle = 'UnknownShoot';
						break;
					case 8:
						$RowStyle = 'CouldShoot';
						break;
					case 9:
						$RowStyle = 'NoShoot';
						break;
				}
				print '<tr id="Row_' . $MyRow->EnId . '" class="' . $RowStyle . '">';

				print '<td class="Center">';
				print '<select ' . ($MyRow->EnStatus>8 ? ' disabled ' : '') . 'name="d_q_QuSession_' . $MyRow->EnId . '" id="d_q_QuSession_' . $MyRow->EnId . '" onBlur="javascript:UpdateSession(\'d_q_QuSession_' . $MyRow->EnId . '\');	">' . "\n";
				foreach ($ComboSes as $Key => $Value)
					print '<option value="' . $Key . '"' . ($MyRow->QuSession==$Key ? ' selected'  : '') . '>' . $Value . '</option>' . "\n";
				print '</select>' . "\n";
				print '</td>';

				print '<td class="Center">';
				print '<input type="text" size="4" maxlength="4" name="d_q_QuTargetNo_' . $MyRow->EnId . '" id="d_q_QuTargetNo_' . $MyRow->EnId . '" value="' . $MyRow->TargetNo . '"' . ($MyRow->QuSession==0 || $MyRow->EnStatus>8 ? ' readonly' : '') . ' onBlur="javascript:UpdateTargetNo(\'d_q_QuTargetNo_' . $MyRow->EnId . '\',\'' . $_REQUEST['Ses'] . '\');">';
				print isset($MyRow->QuTargetNo) ? $MyRow->QuTargetNo : '';
				print '</td>';

				print '<td class="Center">';
				print ($MyRow->EnCode!='' ? $MyRow->EnCode : '&nbsp;');
				print '</td>';

				print '<td>';
				print ($MyRow->EnFirstName . ' ' . $MyRow->EnName!=' ' ? $MyRow->EnFirstName . ' ' . $MyRow->EnName : '&nbsp;');
				print '</td>';

				print '<td class="Center" width="4%">';
				print ($MyRow->CoCode!='' ? $MyRow->CoCode : '&nbsp;');
				print '</td>';

				print '<td width="16%">';
				print ($MyRow->CoName!='' ? $MyRow->CoName : '&nbsp;');
				print '</td>';

				print '<td class="Center">';
				print ($MyRow->EnWChair ? 'X' : '&nbsp;');
				print '</td>';

				print '<td class="Center">';
				print (trim($MyRow->EnDivision)!='' ? $MyRow->EnDivision : '&nbsp;');
				print '</td>';

				print '<td class="Center">';
				print (trim($MyRow->EnClass)!='' ? $MyRow->EnClass : '&nbsp;');
				print '</td>';

				print '</tr>';
			}
		}
		print '</table>' . "\n";
	}
?>
<div id="idOutput"></div>
<script type="text/javascript">FindRedTarget('<?php print $_REQUEST['Ses'];?>');</script>
<?php
	include('Common/Templates/tail.php');
?>