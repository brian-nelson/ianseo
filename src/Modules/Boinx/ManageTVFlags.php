<?php
require_once('../../config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/CommonLib.php');
checkACL(AclOutput,AclReadWrite);

// reset all "flag raising"
safe_w_sql("update BoinxSchedule set BsExtra='1' where BsTournament='{$_SESSION['TourId']}' and BsType like 'Awa\_%'");

if(!empty($_FILES['BackPhoto'])) {
	if(isset($_REQUEST['Delete'])) {
		safe_w_sql("delete from Images where
			ImTournament='{$_SESSION['TourId']}'
			and ImIocCode=''
			and ImSection='Award'
			and ImReference=''
			and ImType=''");
		unlink($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'] .'--Award--.jpg');
	} else {
		switch($_FILES['BackPhoto']['type']) {
			case 'image/jpeg':
			case 'image/png':
				$im = new Imagick();
				$im->setFormat('jpg');
				if($im->readImageBlob(file_get_contents($_FILES['BackPhoto']['tmp_name']))) {
					$imgtoSave=StrSafe_DB($im->getImageBlob());
					safe_w_sql("insert into Images set
						ImTournament='{$_SESSION['TourId']}',
						ImIocCode='',
						ImSection='Award',
						ImReference='',
						ImType='',
						ImContent={$imgtoSave} on duplicate key update ImContent={$imgtoSave}");
					$q=safe_r_sql("select * from Images where ImTournament = {$_SESSION['TourId']}");
					while($r=safe_fetch($q)) {
						if($r->ImContent and $im=@imagecreatefromstring($r->ImContent)) {
							imagejpeg($im, $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe']
							.'-'.$r->ImIocCode
							.'-'.$r->ImSection
							.'-'.$r->ImReference
							.'-'.$r->ImType.'.jpg', 90);
						}
					}
				}
				break;
		}
	}
	CD_redirect(basename(__FILE__));
}

$PAGE_TITLE=get_text('BoinxSchedule', 'Boinx');

$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
	'<script type="text/javascript" src="Fun_AJAX_Schedule.js"></script>',
	'<script type="text/javascript" >var StrConfirm="' . get_text('MsgAreYouSure') . '";</script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ColorPicker/302pop.js"></script>',
);

include('Common/Templates/head.php');
?>
<style>
.button_off, .button_on {
	margin:3px 0 0 0;
	padding:0 5px 1px 5px;
	border:2px outset #CC0000;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
	background-color:#ffd0d0;
	cursor: pointer;
	}
.button_on {
	border:2px inset blue;
	background-color:#d0d0ff;
	padding:1px 5px 0 5px;
	}

div.subtitle {
	margin-top:1em;
	font-size:small;
	font-weight:bold;
	}

div.Title {
	margin-top:1em;
	font-size:small;
	font-weight:bold;
	}

#RssDiv td {padding:0 2px;}


</style>

<div id="AwaDiv">
<div class="Title" style="cursor:pointer" onclick="this.nextElementSibling.style.display=(this.nextElementSibling.style.display=='none'?'table':'none')"><?php print get_text('ScheduleAwards','Boinx');?></div>
<table class="Tabella">
<?php
$Active=false;

// Awards
echo getAwards($_SESSION['TourId']);

// Custom Award...
$CustomAward=getModuleParameter('Flags', 'CustomAwards', array('Flags'=>array(), 'Direction'=>'up'));
echo '<tr><td colspan="2">';
foreach($CustomAward['Flags'] as $Flag) {
	echo '<div><img src="" align="absmiddle">'.$Flag.'<img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" align="absmiddle" onclick="document.href.location=\'?remove='.$Flag.'\'"></div>';
}
echo '<div><select name="" onchange=""><option value=""></option>';
$q=safe_r_sql("select * from Flags where FlTournament=-1 and FlIocCode='FITA' order by FlCode");
while($r=safe_fetch($q)) {
	echo '<option value="'.$r->FlCode.'">'.$r->FlCode.'</option>';
}
echo '</select></div>';
echo '</td><td colspan="2">';
echo '</td></tr>';

$BackColor=Get_Tournament_Option('AwardBackColor','#d0d0d0');
$BackPhoto= $CFG->ROOT_DIR . 'TV/Photos/' . $_SESSION['TourCodeSafe'] . '--Award--.jpg';
if(!file_exists($CFG->DOCUMENT_PATH . 'TV/Photos/' . $_SESSION['TourCodeSafe'] . '--Award--.jpg')) {
	$BackPhoto='';
}


?>
</table>
<div align="center"><input type="text" name="Page_BGColor" id="Page_BGColor" size="6" maxlength="7" value="<?php print $BackColor?>">
&nbsp;<input type="text" id="Ex_Page_BGColor" size="1" style="background-color:<?php print $BackColor?>;" readonly>
&nbsp;<img src="../../Common/Images/sel.gif" onclick="javascript:pickerPopup302('Page_BGColor','Ex_Page_BGColor');">
&nbsp;&nbsp;<input type="button" value="<?php echo get_text('CmdSet', 'Tournament');?>" onclick="SetBackColor()"></div>
<div align="center">
	<form method="POST" enctype="multipart/form-data">
		<img src="<?php echo $BackPhoto; ?>" height="50" align="absmiddle">
		<input type="file" name="BackPhoto">
		&nbsp;&nbsp;<input type="submit" value="<?php echo get_text('CmdUpload', 'Tournament') ;?>">
		&nbsp;&nbsp;<input type="submit" name="Delete" value="<?php echo get_text('CmdDelete', 'Tournament') ;?>" onclick="return(confirm('<?php echo get_text('MsgAreYouSure');?>'))">
	</form>
</div>
<div align="center"><input <?php echo ($Active ? 'style="background-color:yellow"' : ''); ?> type="button" id="RaiseFlag" onclick="DoRaiseFlag()" value="<?php echo get_text('RaiseFlag', 'Tournament');?>"></div>
</div>

<?php
include('Common/Templates/tail.php');

function getAwards($TourId) {
	global $Active;
	$ret='<tr><th>'.get_text('ResultIndClass','Tournament').'</th><th>'.get_text('ResultSqClass','Tournament').'</th><th>'.get_text('IndFinal').'</th><th>'.get_text('TeamFinal').'</th></tr>';
	$ret.='<tr valign="top">';

	// select all the possible awards...
	// Qualifications IND (Div and Class)
	$ret.="<td>\n";
	$q=safe_r_sql("select distinct DivId, ClId, BsType, BsExtra, DivDescription, ClDescription from Entries inner join (select DivId, ClId, DivTournament, DivDescription, ClDescription, DivViewOrder,ClViewOrder from Divisions inner join Classes on DivTournament=ClTournament where ClAthlete and DivAthlete) DivClass on EnTournament=DivTournament and EnDivision=DivId and EnClass=ClId left join BoinxSchedule on DivTournament=BsTournament and BsType=concat_ws('_', 'Awa', 'Ind', DivId, ClId) where DivTournament=$TourId order by DivViewOrder,ClViewOrder");
	while($r=safe_fetch($q)) {
		$ret.='<div name="Awa" class="button_'.($r->BsType ? 'on' : 'off').'" id="Awa_Ind_'.$r->DivId.'_'.$r->ClId.'" onclick="toggle(this)">'.get_text($r->DivDescription, '', '', true)." ".get_text($r->ClDescription, '', '', true)."</div>\n";
		if($r->BsExtra==2) $Active=true;
	}

	$ret.="</td><td>\n";
	// Qualifications Team (Div and Class)
	$q=safe_r_sql("select distinct DivId, ClId, BsType, BsExtra, DivDescription, ClDescription from Teams inner join (select concat(DivId, ClId) DivCl, DivId, ClId, DivTournament, DivDescription, ClDescription, DivViewOrder,ClViewOrder from Divisions inner join Classes on DivTournament=ClTournament where ClAthlete and DivAthlete) DivClass on TeTournament=DivTournament and TeEvent=DivCl left join BoinxSchedule on DivTournament=BsTournament and BsType=concat_ws('_', 'Awa', 'Team', DivId, ClId) where DivTournament=$TourId order by DivViewOrder,ClViewOrder");
	while($r=safe_fetch($q)) {
		$ret.='<div name="Awa" class="button_'.($r->BsType ? 'on' : 'off').'" id="Awa_Team_'.$r->DivId.'_'.$r->ClId.'" onclick="toggle(this)">'.get_text($r->DivDescription, '', '', true)." ".get_text($r->ClDescription, '', '', true)."</div>\n";
		if($r->BsExtra==2) $Active=true;
	}

	$ret.="</td><td>\n";
	// Finals Individual
	$q=safe_r_sql("Select distinct BsType, BsExtra, EvCode, EvEventName from Finals inner join Events on FinTournament=EvTournament and EvCode=FinEvent and EvTeamEvent=0 left join BoinxSchedule on FinTournament=BsTournament and BsType=concat_ws('_', 'Awa', 'Abs', EvCode) where FinTournament=$TourId order by EvProgr");
	while($r=safe_fetch($q)) {
		$ret.='<div name="Awa" class="button_'.($r->BsType ? 'on' : 'off').'" id="Awa_Abs_'.$r->EvCode.'" onclick="toggle(this)">'.get_text($r->EvEventName, '', '', true)."</div>\n";
		if($r->BsExtra==2) $Active=true;
	}

	$ret.="</td><td>\n";
	// Finals Teams
	$q=safe_r_sql("Select distinct BsType, BsExtra, EvCode, EvEventName from TeamFinals inner join Events on TfTournament=EvTournament and EvCode=TfEvent and EvTeamEvent=1 left join BoinxSchedule on TfTournament=BsTournament and BsType=concat_ws('_', 'Awa', 'AbsTeam', EvCode) where TfTournament=$TourId order by EvProgr");
	while($r=safe_fetch($q)) {
		$ret.='<div name="Awa" class="button_'.($r->BsType ? 'on' : 'off').'" id="Awa_AbsTeam_'.$r->EvCode.'" onclick="toggle(this)">'.get_text($r->EvEventName, '', '', true)."</div>\n";
		if($r->BsExtra==2) $Active=true;
	}

	$ret.="</td></tr>";
	return $ret;
}

