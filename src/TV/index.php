<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('TV/Fun_HTML.local.inc.php');

if(!empty($_REQUEST['export'])) {
	$JSON=array('rule' => '', 'sequence' => array(), 'param' => array(), 'content' => array());
	$ToId=getIdFromCode($_REQUEST['Tour']);
	$RuleId=intval($_REQUEST['export']);
	$q=safe_r_sql("select * from TVRules where TVRTournament={$ToId} and TVRId=$RuleId");
	if($r=safe_fetch($q)) {
		$JSON['rule']=$r;
		$RuleName=$r->TVRName;
		$q=safe_r_sql("select * from TVSequence where TVSTournament={$ToId} and TVSRule=$RuleId order by TVSId");
		$SeqIds=array();
		while($r=safe_fetch($q)) {
			$SeqIds[]=$r->TVSContent;
			$JSON['sequence'][]=$r;
		}
		$q=safe_r_sql("select * from TVParams where TVPTournament={$ToId} and TVPId in (".implode(',', $SeqIds).")");
		while($r=safe_fetch($q)) {
			$JSON['param'][]=$r;
		}
		$q=safe_r_sql("select * from TVContents where TVCTournament={$ToId} and TVCId in (".implode(',', $SeqIds).")");
		while($r=safe_fetch($q)) {
			$JSON['content'][]=$r;
		}
		if($JSON) {
			JsonOut($JSON, false, 'Content-disposition: attachment; filename=TVRules-'.$_REQUEST['Tour'].'-'.$RuleName.'.json');
		}
	} else {
		redirect();
	}

}

$TourId=0;
if(!empty($_SESSION['TourId'])) $TourId=$_SESSION['TourId'];
if(!empty($_REQUEST['TourId'])) $TourId=$_REQUEST['TourId'];
$lvl = 0;
if($TourId) {
    $lvl = checkACL(AclOutput, AclReadOnly, true, $TourId);
}

if(!IsBlocked(BIT_BLOCK_MEDIA)) {
	if(!empty($_SESSION['TourId']) and !empty($_FILES['TvRule']['size']) and $_FILES['TvRule']['error']==0) {
		$TourId=$_SESSION['TourId'];
		$Rule=1;
		if($JSON=@json_decode(@file_get_contents($_FILES['TvRule']['tmp_name']))) {
			$q=safe_r_sql("select max(TVRId) as MaxId from TVRules where TVRTournament=$TourId");
			$r=safe_fetch($q);
			if($r->MaxId) $Rule=$r->MaxId+1;
			$JSON->rule->TVRTournament=$TourId;
			$JSON->rule->TVRId=$Rule;
			$SQL=array();
			foreach($JSON->rule as $k=>$v) {
				$SQL[]="$k=".StrSafe_DB($v);
			}
			safe_w_sql("insert into TVRules set ".implode(',', $SQL));

			// inserts all the params first
			$ContentIds=array();
			$StartParam=1;
			// check the sequence ID
			$q=safe_r_sql("select max(TVPId) as MaxId from TVParams where TVPTournament=$TourId");
			$r=safe_fetch($q);
			if($r->MaxId) $StartParam=$r->MaxId+1;
			foreach($JSON->param as $r) {
				$r->TVPTournament=$TourId;
				if(empty($ContentIds['DB'][$r->TVPId])) {
					$ContentIds['DB'][$r->TVPId]=$StartParam++;
				}
				$r->TVPId=$ContentIds['DB'][$r->TVPId];
				$SQL=array();
				foreach($r as $k=>$v) {
					$SQL[]="$k=".StrSafe_DB($v);
				}
				safe_w_sql("insert into TVParams set ".implode(',', $SQL));
			}

			// inserts all the Multimedia Content
			$StartParam=1;
			// check the sequence ID
			$q=safe_r_sql("select max(TVCId) as MaxId from TVContents where TVCTournament=$TourId");
			$r=safe_fetch($q);
			if($r->MaxId) $StartParam=$r->MaxId+1;
			foreach($JSON->content as $r) {
				$r->TVCTournament=$TourId;
				if(empty($ContentIds['MM'][$r->TVCId])) {
					$ContentIds['MM'][$r->TVCId]=$StartParam++;
				}
				$r->TVCId=$ContentIds['MM'][$r->TVCId];
				$SQL=array();
				foreach($r as $k=>$v) {
					$SQL[]="$k=".StrSafe_DB($v);
				}
				safe_w_sql("insert into TVContents set ".implode(',', $SQL));
			}

			$StartSeq=1;
			// check the sequence ID
			$q=safe_r_sql("select max(TVSId) as MaxId from TVSequence where TVSTournament=$TourId");
			$r=safe_fetch($q);
			if($r->MaxId) $StartSeq=$r->MaxId+1;
			$SeqIds=array();
			foreach($JSON->sequence as $Seq) {
				$Seq->TVSTournament=$TourId;
				$Seq->TVSRule=$Rule;
				if(empty($SeqIds[$Seq->TVSId])) {
					$SeqIds[$Seq->TVSId]=$StartSeq++;
				}
				$Seq->TVSId=$SeqIds[$Seq->TVSId];
				$Seq->TVSContent=$ContentIds[$Seq->TVSTable][$Seq->TVSContent];
				$SQL=array();
				foreach($Seq as $k=>$v) {
					$SQL[]="$k=".StrSafe_DB($v);
				}
				safe_w_sql("insert into TVSequence set ".implode(',', $SQL));
			}

			cd_redirect();
		}
	}
	if($TourId and !empty($_POST['NewRule']) and $lvl==AclReadWrite) {
		$q=safe_r_sql("SELECT IFNULL(MAX(TVRId),0) AS CurID FROM TVRules WHERE TVRTournament=$TourId");
		$newID = (safe_fetch($q)->CurID)+1;
		safe_w_sql("INSERT INTO TVRules set TVRId=$newID, TVRTournament=$TourId, TVRName=".strsafe_DB($_POST['NewRule']));
		$_GET=array();
		cd_redirect(go_get('edit',$newID));
	}

	// cancella la regola e le sue associazioni
	if(!empty($_GET['delete']) and $lvl==AclReadWrite) {
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
		safe_w_sql("DELETE FROM TVContents WHERE TVCTournament=$TourId AND TVCId not in (select distinct TVSContent FROM TVSequence WHERE TVSTournament=$TourId)");
		cd_redirect();
	}

	if(!empty($_GET['edit']) and $lvl==AclReadWrite) {
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
<form method="POST" enctype="multipart/form-data">
<table class="Tabella">
<tbody id="tbody">
<tr><th class="Title" colspan="10"><?php print get_text('TVOutRulesWithStart','Tournament');?></th></tr>
<tr>
	<th class="Title"><?php print get_text('TourCode','Tournament');?></th>
	<th class="Title"><?php print get_text('TourName','Tournament');?></th>
	<th class="Title"><?php print get_text('TourWhere','Tournament');?></th>
	<th class="Title"><?php print get_text('TourWhen','Tournament');?></th>
	<th class="Title" colspan="6"><?php print get_text('TVOutRules','Tournament');?></th>
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
			print '<td class="Left">[&nbsp;<a target="_blank"  class="Link" href="Rot/?Rule=' . $MyRow->TVRId . '&Tour=' . $MyRow->ToCode . '">' . get_text('TvCss3Page', 'Tournament') . '</a>&nbsp;]</td>';

			if($TourId and $lvl==AclReadWrite) {
				print '<td class="Left"><a class="Link" href="?edit=' . $MyRow->TVRId . '">' . get_text('Edit','Languages') . '</a></td>';
				print '<td class="Left"><a class="Link" href="?delete=' . $MyRow->TVRId . '" onclick="return(confirm(\''.get_text('MsgAreYouSure').'\'))">' . get_text('CmdDelete','Tournament') . '</a></td>';
			}
			print '<td class="Left"><input type="button" onclick="document.location.href=\'?export=' . $MyRow->TVRId . '&Tour=' . $MyRow->ToCode . '\'" value="' . get_text('CmdExport','Tournament') . '"></td>';
			print '</tr>' . "\n";
		}
	}
	else
	{
		print '<tr><td class="Bold Center">' . get_text('TVOutNoRulesWithStart','Tournament') . '</td></tr>' . "\n";
	}

	if($TourId and $lvl==AclReadWrite) {
		// new rules only in an open tournament
		$riga=(1-$riga);
		print '<tr'.($riga?' class="alt"':'').'>';
		print "<td>&nbsp;</td>";
		print "<td>&nbsp;</td>";
		print "<td>&nbsp;</td>";
		print "<td>&nbsp;</td>";
		print '<td class="Left" colspan="3"><input type="text" name="NewRule" />&nbsp;<input type="submit" value="'.get_text('TVNewRule','Tournament').'" /></td>';
		print '<td class="Left" colspan="3"><input type="file" name="TvRule"></td>';
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