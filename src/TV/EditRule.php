<?php
if (!defined('IN_PHP')) CD_redirect();

require_once('Common/Fun_Sessions.inc.php');

$TourId = $RULE->TVRTournament;
$RuleId = $RULE->TVRId;
$ChainId=0;
$DBId=0;
$MMId=0;
$NewOrder=0;
$CHAIN=NULL;

if(!empty($_GET['contdel'])) {
	// item is deletable ony if not used in any rule
	$q=safe_r_sql("select * from TVSequence where TVSTournament=$TourId and TVSTable='MM' and TVSCntSameTour='1' and TVSContent=" . intval($_GET['contdel']));

	if(safe_num_rows($q) == 0) {
		safe_w_sql("delete from TVContents where TVCTournament=$TourId and TVCId=".intval($_GET['contdel']));
	}
	cd_redirect(go_get('contdel', '', true));
}

if(!empty($_GET['ChainId'])) {
	if(!empty($_GET['from']) and !empty($_GET['to']) and $from=intval($_GET['from']) and $to=intval($_GET['to'])) {

		// get the 2 records to swap
		$q=safe_r_sql("SELECT * FROM TVSequence WHERE TVSId=".intval($_GET['ChainId'])." AND TVSTournament=$TourId AND TVSOrder =$from");
		if($r=safe_fetch($q)) {
			safe_w_sql("UPDATE TVSequence SET TVSOrder=$to WHERE TVSId=$r->TVSId AND TVSTournament=$TourId");
			safe_w_sql("UPDATE TVSequence SET TVSOrder=$from WHERE TVSId!=$r->TVSId AND TVSTournament=$TourId AND TVSOrder=$to AND TVSRule=$r->TVSRule");
		}
		unset($_GET['from']);
		unset($_GET['to']);
		unset($_GET['ChainId']);
		cd_redirect(go_get());
	}
	// Get the sequence to know if it is a DB or Multimedia item
	$q=safe_r_sql("SELECT * FROM TVSequence WHERE TVSId=".intval($_GET['ChainId']) . " AND TVSTournament=$TourId");
	if($CHAIN=safe_fetch($q)) {
		$ChainId=$CHAIN->TVSId;
		if($CHAIN->TVSTable=='DB') {
			$DBId=$CHAIN->TVSContent; // it is a DB Item
		} elseif($CHAIN->TVSTable=='MM') {
			$MMId=$CHAIN->TVSContent; // it is a MultiMedia Item
		}
	}
} elseif(!empty($_GET['ChainDel'])) {
	$q=safe_r_sql("SELECT * FROM TVSequence WHERE TVSId=".intval($_GET['ChainDel']) . " AND TVSTournament=$TourId");
	if($r=safe_fetch($q)) {
		// decrease the order of the segment by 1
		safe_w_sql("UPDATE TVSequence SET TVSOrder=TVSOrder-1 WHERE TVSRule=$RuleId AND TVSTournament=$TourId AND TVSOrder>$r->TVSOrder");
		// delete params if content comes from DB
		if($r->TVSTable=='DB') safe_w_sql("delete from TVParams where TVPId=$r->TVSContent");
		// delete the sequence item
		safe_w_sql("DELETE FROM TVSequence WHERE TVSId=".intval($_GET['ChainDel']) . " AND TVSTournament=$TourId");
	}
	unset($_GET['ChainDel']);
	cd_redirect(go_get());
} else {
	$q=safe_r_sql("SELECT MAX(TVSOrder) AS NewOrder FROM TVSequence WHERE TVSRule=$RuleId AND TVSTournament=$TourId");
	if($r=safe_fetch($q)) {
		$NewOrder=$r->NewOrder + 1;
	}
}

if($_POST) {
	// Check a name change of the rule
	if(!empty($_POST['d_TVRuleName'])) {
		safe_w_sql("UPDATE TVRules SET TVRName=" . StrSafe_DB($_POST['d_TVRuleName']) . " WHERE TVRId=$IDrule AND TVRTournament=$TourId");
	}

	// Check a new DB sequence
	if(!empty($_POST['d_TVPage'])) {
		$params=array("TVPPage='{$_POST['d_TVPage']}'");

		if(!$RULE->TV_Carattere or !empty($_POST['d_TV_SetColDefault'])) {
			// no default setup, so the first segment becomes the default
			safe_w_sql("UPDATE TVRules SET ".
				"TV_Carattere=" . intval($_POST['d_TV_Carattere']).
				", TV_Content_BGColor=" . strsafe_DB($_POST['d_TV_Content_BGColor']).
				", TV_Page_BGColor=" . strsafe_DB($_POST['d_TV_Page_BGColor']).
				", TV_TR_BGColor=" . strsafe_DB($_POST['d_TV_TR_BGColor']).
				", TV_TR_Color=" . strsafe_DB($_POST['d_TV_TR_Color']).
				", TV_TRNext_BGColor=" . strsafe_DB($_POST['d_TV_TRNext_BGColor']).
				", TV_TRNext_Color=" . strsafe_DB($_POST['d_TV_TRNext_Color']).
				", TV_TH_BGColor=" . strsafe_DB($_POST['d_TV_TH_BGColor']).
				", TV_TH_Color=" . strsafe_DB($_POST['d_TV_TH_Color']).
				", TV_THTitle_BGColor=" . strsafe_DB($_POST['d_TV_THTitle_BGColor']).
				", TV_THTitle_Color=" . strsafe_DB($_POST['d_TV_THTitle_Color']).
				" WHERE TVRId=$RuleId AND TVRTournament=$TourId");
			$params[]="TVP_Carattere=0";
			$params[]="TVP_Content_BGColor=''";
			$params[]="TVP_Page_BGColor=''";
			$params[]="TVP_TR_BGColor=''";
			$params[]="TVP_TR_Color=''";
			$params[]="TVP_TRNext_BGColor=''";
			$params[]="TVP_TRNext_Color=''";
			$params[]="TVP_TH_BGColor=''";
			$params[]="TVP_TH_Color=''";
			$params[]="TVP_THTitle_BGColor=''";
			$params[]="TVP_THTitle_Color=''";
			$params[]="TVPDefault='1'";
		} elseif(empty($_POST['d_TV_ColDefault'])
			and ($RULE->TV_Carattere != intval($_POST['d_TV_Carattere'])
				OR $RULE->TV_Content_BGColor != $_POST['d_TV_Content_BGColor']
				OR $RULE->TV_Page_BGColor != $_POST['d_TV_Page_BGColor']
				OR $RULE->TV_TR_BGColor != $_POST['d_TV_TR_BGColor']
				OR $RULE->TV_TR_Color != $_POST['d_TV_TR_Color']
				OR $RULE->TV_TRNext_BGColor != $_POST['d_TV_TRNext_BGColor']
				OR $RULE->TV_TRNext_Color != $_POST['d_TV_TRNext_Color']
				OR $RULE->TV_TH_BGColor != $_POST['d_TV_TH_BGColor']
				OR $RULE->TV_TH_Color != $_POST['d_TV_TH_Color']
				OR $RULE->TV_THTitle_BGColor != $_POST['d_TV_THTitle_BGColor']
				OR $RULE->TV_THTitle_Color != $_POST['d_TV_THTitle_Color']
			)) {
			// sets this segment to different colors as the default
			$params[]="TVP_Carattere=" . intval($_POST['d_TV_Carattere']);
			$params[]="TVP_Content_BGColor=" . strsafe_DB($_POST['d_TV_Content_BGColor']);
			$params[]="TVP_Page_BGColor=" . strsafe_DB($_POST['d_TV_Page_BGColor']);
			$params[]="TVP_TR_BGColor=" . strsafe_DB($_POST['d_TV_TR_BGColor']);
			$params[]="TVP_TR_Color=" . strsafe_DB($_POST['d_TV_TR_Color']);
			$params[]="TVP_TRNext_BGColor=" . strsafe_DB($_POST['d_TV_TRNext_BGColor']);
			$params[]="TVP_TRNext_Color=" . strsafe_DB($_POST['d_TV_TRNext_Color']);
			$params[]="TVP_TH_BGColor=" . strsafe_DB($_POST['d_TV_TH_BGColor']);
			$params[]="TVP_TH_Color=" . strsafe_DB($_POST['d_TV_TH_Color']);
			$params[]="TVP_THTitle_BGColor=" . strsafe_DB($_POST['d_TV_THTitle_BGColor']);
			$params[]="TVP_THTitle_Color=" . strsafe_DB($_POST['d_TV_THTitle_Color']);
			$params[]="TVPDefault=''";
		} else {
			// revert to default
			$params[]="TVP_Carattere=0";
			$params[]="TVP_Content_BGColor=''";
			$params[]="TVP_Page_BGColor=''";
			$params[]="TVP_TR_BGColor=''";
			$params[]="TVP_TR_Color=''";
			$params[]="TVP_TRNext_BGColor=''";
			$params[]="TVP_TRNext_Color=''";
			$params[]="TVP_TH_BGColor=''";
			$params[]="TVP_TH_Color=''";
			$params[]="TVP_THTitle_BGColor=''";
			$params[]="TVP_THTitle_Color=''";
			$params[]="TVPDefault='1'";
		}
		$params[]="TVPTimeStop=".intval($_POST['d_TVTimeStop']);
		$params[]="TVPTimeScroll=".intval($_POST['d_TVTimeScroll']);
		$params[]="TVPNumRows=".intval($_POST['d_TVNumRows']);
		$params[]="TVPViewNationName=".strsafe_DB(!empty($_POST['d_TVViewNationName']));
		$params[]="TVPNameComplete=".strsafe_DB(!empty($_POST['d_TVNameComplete']));
		$params[]="TVPViewIdCard=".strsafe_DB(!empty($_POST['d_TVViewIdCard']));
		$params[]="TVPViewTeamComponents=".strsafe_DB(!empty($_POST['d_TVViewTeamComponents']));
		$params[]="TVPViewPartials=".strsafe_DB(!empty($_POST['d_TVViewPartials']));
		$params[]="TVPViewDetails=".strsafe_DB(!empty($_POST['d_TVViewDetails']));
		$tmpInd=array();
		if(!empty($_POST['d_TVPhaseInd'])) {
			foreach($_POST['d_TVPhaseInd'] as $k=>$v) {
				if(is_array($v)) $tmpInd[]=$k.'+'.implode('+',$v);
				else $tmpInd[]=$v;
			}
		}
		$tmpTeam=array();
		if(!empty($_POST['d_TVPhaseTeam'])) {
			foreach($_POST['d_TVPhaseTeam'] as $k=>$v) {
				if(is_array($v)) $tmpTeam[]=$k.'+'.implode('+',$v);
				else $tmpTeam[]=$v;
			}
		}

		$Columns=array();
		foreach($_POST['d_TVColumns'] as $k => $v) {
			if($k=='WIDTH') {
				$Columns[]="$k:$v";
			} else if($k=='COMP') {
				if(!empty($v))
					$Columns[]="$k:$v";
			} else {
				$Columns[]=$k;
			}
		}
		$params[]="TVPEventInd=".strsafe_DB(empty($_POST['d_TVEventInd']) || $tmpInd ? '' : implode('|',$_POST['d_TVEventInd']));
		$params[]="TVPEventTeam=".strsafe_DB(empty($_POST['d_TVEventTeam']) || $tmpTeam ? '' : implode('|',$_POST['d_TVEventTeam']));
		$params[]="TVPPhasesInd=".strsafe_DB(empty($_POST['d_TVPhaseInd'])?'':implode('|',$tmpInd));
		$params[]="TVPPhasesTeam=".strsafe_DB(empty($_POST['d_TVPhaseTeam'])?'':implode('|',$tmpTeam));
		$params[]="TVPColumns=".strsafe_DB(empty($_POST['d_TVColumns'])?'':implode('|',$Columns));
		$params[]="TVPSession=".strsafe_DB(empty($_POST['d_TVSession'])?'0':$_POST['d_TVSession']);

		// insert/update the TVparams table
		if($DBId) {
			safe_w_sql("UPDATE TVParams SET ".implode(',', $params)." WHERE TVPId=$DBId AND TVPTournament=$TourId");
		} else {
			$q=safe_r_sql("SELECT IFNULL(MAX(TVPId),0) AS CurID FROM TVParams WHERE TVPTournament=$TourId");
			$DBId = (safe_fetch($q)->CurID)+1;
			safe_w_sql("INSERT INTO TVParams SET TVPId=$DBId, TVPTournament=$TourId, ".implode(',', $params));
		}

		// update the TVSequence to the correct media
		if($ChainId) {
			safe_w_sql("UPDATE TVSequence SET TVSContent=$DBId, TVSTable='DB' WHERE TVSId=$ChainId AND TVSTournament=$TourId");
		} else {
			$q=safe_r_sql("SELECT IFNULL(MAX(TVSId),0) AS CurID FROM TVSequence WHERE TVSTournament=$TourId");
			$ChainId = (safe_fetch($q)->CurID)+1;
			safe_w_sql("INSERT INTO TVSequence SET TVSId=$ChainId, TVSTournament=$TourId, TVSRule=$RuleId, TVSContent=$DBId, TVSCntSameTour=1, TVSTable='DB', TVSOrder=$NewOrder");
		}

		unset($_GET['ChainId']);

		cd_redirect(go_get(array('NewDb'=>'', 'NewMm'=>''), true));
	}

	// Check if a new MM content has been Uploaded/Inserted
	if(!empty($_POST['d_TVContentName']) and (!empty($_POST['d_TVContentText']) or !empty($_FILES['d_TVContentUpload']['name']))) {
		$content=array();
		$content[]="TVCTournament=".$TourId;
		if(!empty($_POST['d_TVContentText'])) {
			$content[]="TVCContent=".strsafe_DB($_POST['d_TVContentText']);
			$content[]="TVCMimeType='text/html'";
		} else {
			$content[]="TVCContent=".strsafe_DB(file_get_contents($_FILES['d_TVContentUpload']['tmp_name']));
			$content[]="TVCMimeType=".strsafe_db($_FILES['d_TVContentUpload']['type']);
		}

		$content[]="TVCTime=". intval($_POST['d_TVDefaultTime']);
		$content[]="TVCName=".strsafe_db($_POST['d_TVContentName']);
		$content[]="TVCScroll=".intval($_POST['d_TVDefaultScroll']);

		$q=safe_r_sql("SELECT IFNULL(MAX(TVCId),0) AS CurID FROM TVContents WHERE TVCTournament=$TourId");
		$_POST['d_TVMultimedia'] = ((safe_fetch($q)->CurID)+1).'|1';
		safe_w_sql("INSERT INTO TVContents set TVCId=". $_POST['d_TVMultimedia'] . ", ".implode(',', $content));
		$_POST['d_TVMultimediaTime']=intval($_POST['d_TVDefaultTime']);
		$_POST['d_TVMultimediaScroll']=intval($_POST['d_TVDefaultScroll']);
		include_once('Common/CheckPictures.php');
		CheckPictures();
	} elseif(!empty($_POST['d_TVMultimedia']) and !empty($_POST['d_TVContentText'])) {
		// check if a Multimedia Text has been edited in a textual context (text/html)
		// and in the current Tournament
		list($mulId, $mulTour) = explode("|",$_POST['d_TVMultimedia']);
		$q=safe_r_sql("select * from TVContents where TVCId=$mulId AND TVCTournament=" . ($mulTour == 1 ? $TourId : "-1"));
		if($r=safe_fetch($q) and $r->TVCMimeType=='text/html' and $mulTour) {
			$content=array();
			$content[]="TVCContent=".strsafe_DB($_POST['d_TVContentText']);
			$content[]="TVCTime=". intval($_POST['d_TVDefaultTime']);
			$content[]="TVCScroll=".intval($_POST['d_TVDefaultScroll']);
			safe_w_sql("update TVContents set ".implode(',', $content). " where TVCTournament=$TourId and TVCId=$mulId and TVCTournament=" . ($mulTour == 1 ? $TourId : "-1"));
		}
	}


	// Check a new MM sequence
	if(!empty($_POST['d_TVMultimedia'])) {
		list($mulId, $mulTour) = explode("|",$_POST['d_TVMultimedia']);
		$q=safe_r_sql("select * from TVContents where TVCId=$mulId AND TVCTournament=" . ($mulTour == 1 ? $TourId : "-1"));
		if($r=safe_fetch($q)) {
			$multimedia=array();
			$multimedia[]="TVSContent=".$mulId;
			$multimedia[]="TVSCntSameTour=".$mulTour;
			$multimedia[]="TVSTime=".(strlen($_POST['d_TVMultimediaTime'])>0?intval($_POST['d_TVMultimediaTime']):$r->TVCTime);
			$multimedia[]="TVSScroll=".(strlen($_POST['d_TVMultimediaScroll'])>0?intval($_POST['d_TVMultimediaScroll']):$r->TVCScroll);
			$multimedia[]="TVSFullScreen=".StrSafe_DB(isset($_POST['d_TVMultimediaFullScreen']));

			// update the TVSequence to the correct media
			if($ChainId) {
				safe_w_sql("UPDATE TVSequence SET ".implode(',', $multimedia)." WHERE TVSId=$ChainId AND TVSTournament=$TourId");
			} else {
				$multimedia[]="TVSRule=$RuleId";
				$multimedia[]="TVSTable='MM'";
				$multimedia[]="TVSOrder=$NewOrder";
				$q=safe_r_sql("SELECT IFNULL(MAX(TVSId),0) AS CurID FROM TVSequence WHERE TVSTournament=$TourId");
				$ChainId = (safe_fetch($q)->CurID)+1;
				safe_w_sql("INSERT INTO TVSequence SET TVSId=$ChainId, TVSTournament=$TourId, ".implode(',', $multimedia));
			}
		}
	}
	unset($_GET['ChainId']);

	cd_redirect(go_get(array('NewDb'=>'', 'NewMm'=>''), true));
}

$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'TV/FuncRot.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ColorPicker/302pop.js"></script>',
	);

$PAGE_TITLE=get_text('TVManagingRule', 'Tournament', ManageHTML($RULE->TVRName));

if($DBId) {
	$Select
	= "SELECT * FROM TVParams "
	. "WHERE TVPId=$DBId "
	. "AND TVPTournament=$TourId";
	$Rs=safe_r_sql($Select);
	if($r=safe_fetch($Rs)) $ONLOAD=' onload="GetRuleSel(\''.$r->TVPPage.'\', ' . $DBId . ')"';
}

include('Common/Templates/head.php');

?>
<form name="Frm" method="post" action="" enctype="multipart/form-data" onsubmit="clearHiddenFields(this)">
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('TVManagingRule', 'Tournament', ManageHTML($RULE->TVRName));?></th></tr>

<?php

if($DBId or !empty($_REQUEST['NewDb'])) require_once('EditRule-Tour.php');
elseif($MMId or !empty($_REQUEST['NewMm'])) require_once('EditRule-Media.php');
else require_once('EditRule-List.php');
?>

</table>

<br/>

<table class="Tabella">
<tr><td class="Center"><input type="submit" value="<?php print get_text('CmdSave');?>">
	&nbsp;&nbsp;<input type="reset" value="<?php print get_text('CmdCancel');?>">
	&nbsp;&nbsp;<input type="button" value="<?php print get_text('Back');?>" onclick="window.location.href='./'">
	</td></tr>
</table>

</form>
<div id="colorpicker302" class="colorpicker302"></div>
<div id="idOutput">	</div>
<?php

	include('Common/Templates/tail.php');
?>