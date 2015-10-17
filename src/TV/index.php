<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('TV/Fun_HTML.local.inc.php');

$TourId=0;
if(!empty($_SESSION['TourId'])) $TourId=$_SESSION['TourId'];
if(!empty($_REQUEST['TourId'])) $TourId=$_REQUEST['TourId'];

if(!IsBlocked(BIT_BLOCK_MEDIA)) {
	if($TourId and !empty($_POST['NewRule'])) {
		$q=safe_r_sql("SELECT IFNULL(MAX(TVRId),0) AS CurID FROM TVRules WHERE TVRTournament=$TourId");
		$newID = (safe_fetch($q)->CurID)+1;
		safe_w_sql("INSERT INTO TVRules set TVRId=$newID, TVRTournament=$TourId, TVRName=".strsafe_DB($_POST['NewRule']));
		$_GET=array();
		cd_redirect(go_get('edit',$newID));
	}

	// cancella la regola e le sue associazioni
	if(!empty($_GET['delete'])) {
		$IDrule=intval($_GET['delete']);

		// controlla che non sia una regola predefinita (IDtour = -1)
		$q=safe_r_sql("SELECT * FROM TVRules WHERE TVRId=$IDrule AND TVRTournament=$TourId");
		if(!($r=safe_fetch($q))) cd_redirect(); // non esiste la regola o

		$q=safe_r_sql("SELECT * FROM TVSequence WHERE TVSTable='DB' AND TVSRule=$IDrule AND TVSTournament=$TourId");
		while($r=safe_fetch($q)) {
			safe_w_sql("DELETE FROM TVParams WHERE TVPId=$r->TVSContent AND TVPTournament=$TourId");
		}
		safe_w_sql("DELETE FROM TVRules WHERE TVRId=$IDrule AND TVRTournament=$TourId");
		safe_w_sql("DELETE FROM TVSequence WHERE TVSRule=$IDrule AND TVSTournament=$TourId");
		cd_redirect();
	}

	if(!empty($_GET['edit'])) {
		$IDrule=intval($_GET['edit']);

		// controlla che non sia una regola predefinita (IDtour = -1)
		$q=safe_r_sql("SELECT * FROM TVRules WHERE TVRId=$IDrule AND TVRTournament=$TourId");
		if(!($RULE=safe_fetch($q))) cd_redirect(); // non esiste la regola

		define('IN_PHP', true);
		include('EditRule.php');
		exit;
	}
}

$JS_SCRIPT=array("<style>\n#Content .Tabella tr.alt {\n\tbackground-color:rgb(200,200,200)\n\t}\n</style>\n");

$PAGE_TITLE=get_text('TVOutRulesWithStart','Tournament');

include('Common/Templates/head.php');

?>
<div align="center">
<div class="medium">
<form method="POST">
<table class="Tabella">
<tbody id="tbody">
<tr><th class="Title" colspan="8"><?php print get_text('TVOutRulesWithStart','Tournament');?></th></tr>
<tr>
	<th class="Title"><?php print get_text('TourCode','Tournament');?></th>
	<th class="Title"><?php print get_text('TourName','Tournament');?></th>
	<th class="Title"><?php print get_text('TourWhere','Tournament');?></th>
	<th class="Title"><?php print get_text('TourWhen','Tournament');?></th>
	<th class="Title" colspan="4"><?php print get_text('TVOutRules','Tournament');?></th>
</tr>
<?php

	$Select = "SELECT TVRId, TVRTournament, TVRName, ToCode, ToName, ToWhere, ";
	$Select .= "date_format(ToWhenFrom, '".get_text('DateFmtDB')."') as ToFrom, ";
	$Select .= "date_format(ToWhenTo, '".get_text('DateFmtDB')."') as ToTo ";
	$Select .= "FROM TVRules inner join Tournament on ToId=TVRTournament ";
	if($TourId) $Select.= "where TVRTournament=$TourId ";
	$Select .= "order by ToWhenFrom desc, ToCode";

	$riga=1;

	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0)
	{
		$old_code='';
		while ($MyRow=safe_fetch($Rs))
		{
			if($MyRow->ToCode!=$old_code) {
				$riga=(1-$riga);
				$old_code=$MyRow->ToCode;
			}
			print '<tr'.($riga?' class="alt"':'').' style="height:3em">';
			print "<td>$MyRow->ToCode</td>";
			print "<td>$MyRow->ToName</td>";
			print "<td>$MyRow->ToWhere</td>";
			if($MyRow->ToFrom!=$MyRow->ToTo) {
				print "<td>". get_text('DateFmtMoreDays','',array($MyRow->ToFrom,$MyRow->ToTo))."</td>";
			} else {
				print "<td>$MyRow->ToFrom</td>";
			}

			print '<td class="Left"><a target="_blank"  class="Link" href="Rotation.php?Rule=' . $MyRow->TVRId . '&Tour=' . $MyRow->ToCode . '">' . ManageHTML($MyRow->TVRName) . '</a></td>';
			print '<td class="Left">[&nbsp;<a target="_blank"  class="Link" href="LightRot.php?Rule=' . $MyRow->TVRId . '&Tour=' . $MyRow->ToCode . '">' . get_text('TvLightPage', 'Tournament') . '</a>&nbsp;]</td>';

			if($TourId) {
				print '<td class="Left"><a class="Link" href="?edit=' . $MyRow->TVRId . '">' . get_text('Edit','Languages') . '</a></td>';
				print '<td class="Left"><a class="Link" href="?delete=' . $MyRow->TVRId . '" onclick="return(confirm(\''.get_text('MsgAreYouSure').'\'))">' . get_text('CmdDelete','Tournament') . '</a></td>';
			}
			print '</tr>' . "\n";
		}
	}
	else
	{
		print '<tr><td class="Bold Center">' . get_text('TVOutNoRulesWithStart','Tournament') . '</td></tr>' . "\n";
	}

	if($TourId) {
		// new rules only in an open tournament
		$riga=(1-$riga);
		print '<tr'.($riga?' class="alt"':'').'>';
		print "<td>&nbsp;</td>";
		print "<td>&nbsp;</td>";
		print "<td>&nbsp;</td>";
		print "<td>&nbsp;</td>";
		print '<td class="Left" colspan="2"><input type="text" name="NewRule" /></td>';
		print '<td class="Left" colspan="2"><input type="submit" value="'.get_text('TVNewRule','Tournament').'" /></td>';
		print '</tr>' . "\n";
	}
?>
</tbody>
</table>
</form>
</div>
</div>
<div id="idOutput">	</div>
<?php include('Common/Templates/tail.php'); ?>