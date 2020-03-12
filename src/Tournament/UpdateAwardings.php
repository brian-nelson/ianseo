<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Modules.php');

if (!CheckTourSession()) {
	print get_text('CrackError');
	exit;
}
checkACl(AclCompetition,AclReadWrite, false);

$Errore=0;
$Answer='';
$AllowedFields=array('AwOrder', 'AwEventTrans', 'AwPositions','AwDescription-0','AwDescription-1','AwDescription-2','AwAwarders-1','AwAwarders-2');
$AllowedModuleFields=array(
	'SecondLanguageCode',
	'FirstLanguageCode',
	'Aw-Intro-1',
	'Aw-Medal-1',
	'Aw-Plaque-1',
	'Aw-Giver-1',
	'Aw-Giving-1',
	'Aw-Med1-1',
	'Aw-Med2-1',
	'Aw-Med3-1',
	'Aw-Med4-1',
	'Aw-representing-1',
	'Aw-Anthem-1',
	'Aw-Anthem-TPE-1',
	'Aw-Applause-1',
	'Aw-Special-1',
	'Aw-Intro-2',
	'Aw-Medal-2',
	'Aw-Plaque-2',
	'Aw-Giver-2',
	'Aw-Giving-2',
	'Aw-Med1-2',
	'Aw-Med2-2',
	'Aw-Med3-2',
	'Aw-Med4-2',
	'Aw-representing-2',
	'Aw-Anthem-2',
	'Aw-Anthem-TPE-2',
	'Aw-Applause-2',
	'Aw-Special-2',
    'PrintPositions',
);

$Field=$_REQUEST["field"];
$Value=$_REQUEST["value"];
$Response=$_REQUEST["value"];

if (!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
	if($_REQUEST["id"][0]=='f') $_REQUEST["id"]=substr($_REQUEST["id"], 1);
	if(in_array($_REQUEST["field"], $AllowedFields) and preg_match('/^[A-Z0-9-]+\|[0-1]{1}\|[0-1]{1}$/i',$_REQUEST["id"])) {
		list($Event,$isFinal,$isTeam) = explode('|',$_REQUEST["id"]);
		// get old values...
		$Sql="select * from Awards
			WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AwEvent=" . StrSafe_DB($Event) . " AND AwFinEvent=" . StrSafe_DB($isFinal). " AND AwTeam=" . StrSafe_DB($isTeam);
		$q=safe_r_sql($Sql);
		if($ORG=safe_fetch($q)) {
			$Description=explode('@@@', $ORG->AwDescription);
			if(empty($Description[1])) $Description[1]='';
			$Awarders=explode('@@@', $ORG->AwAwarders);
			if(empty($Awarders[1])) $Awarders[1]='';
		} else {
			$Description=array('','','');
			$Awarders=array('','');
		}

		switch($_REQUEST["field"]) {
			case 'AwDescription-0':
				$Description[0]=$_REQUEST["value"];
				$Field='AwDescription';
				$Value=implode('@@@', $Description);
				break;
			case 'AwDescription-1':
				$Description[1]=$_REQUEST["value"];
				$Field='AwDescription';
				$Value=implode('@@@', $Description);
				if($_REQUEST["value"]) $Response=getModuleParameter('Awards', $_REQUEST["value"]);
				break;
			case 'AwAwarders-1':
				$Awarders[0]=$_REQUEST["value"];
				$Field='AwAwarders';
				$Value=implode('@@@', $Awarders);
				if($_REQUEST["value"]) $Response=getModuleParameter('Awards', $_REQUEST["value"]);
				break;
			case 'AwAwarders-2':
				$Awarders[1]=$_REQUEST["value"];
				$Field='AwAwarders';
				$Value=implode('@@@', $Awarders);
				if($_REQUEST["value"]) $Response=getModuleParameter('Awards', $_REQUEST["value"]);
				break;
		}
		$Sql = "UPDATE Awards SET $Field=" . StrSafe_DB($Value) .
			"WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AwEvent=" . StrSafe_DB($Event) . " AND AwFinEvent=" . StrSafe_DB($isFinal). " AND AwTeam=" . StrSafe_DB($isTeam);
		$RsUp=safe_w_sql($Sql);
		if($Field=='AwPositions' and $Response=='1,2,4,3') {
			$Response='1,2,3-3';
		}
	} elseif(in_array($_REQUEST["field"], $AllowedModuleFields)
			or substr($_REQUEST["field"], 0, 11)=='Aw-Awarder-'
			or substr($_REQUEST["field"], 0, 9)=='Aw-Award-'
			or substr($_REQUEST["field"], 0, 9)=='Aw-Custom'
            or $_REQUEST["field"]=='PrintPositions'
			) {
		if($_REQUEST["field"]=='SecondLanguageCode') {
			$_REQUEST["value"]=strtolower($_REQUEST["value"]);
			// check if code already exists
			if(getModuleParameter('Awards', 'SecondLanguageCode')!=$_REQUEST["value"]) {
				// new value so implements the other pieces
				setModuleParameter('Awards', 'Aw-Intro-2', get_text('Award-Intro', 'IOC_Codes', '{$a}', '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Medal-2', get_text('Award-MedalGiver', 'IOC_Codes', '{$a}', '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Plaque-2', get_text('Award-PlaqueGiver', 'IOC_Codes', '{$a}', '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Giver-2', get_text('Award-PremiumGiver', 'IOC_Codes', array('{$a[0]}','{$a[1]}'), '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Giving-2', get_text('Award-PremiumGiving', 'IOC_Codes', array('{$a[0]}','{$a[1]}'), '', '', $_REQUEST["value"]));
				for($n=1; $n<5; $n++) {
					setModuleParameter('Awards', 'Aw-Med'.$n.'-2', get_text('Medal-'.$n, 'IOC_Codes', '', '', '', $_REQUEST["value"]));
				}
				setModuleParameter('Awards', 'Aw-representing-2', get_text('Award-representing', 'IOC_Codes', '{$a}', '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Anthem-2', get_text('Award-Anthem', 'IOC_Codes', '', '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Anthem-TPE-2', get_text('Award-Anthem-TPE', 'IOC_Codes', '', '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Applause-2', get_text('Award-Applause', 'IOC_Codes', '', '', '', $_REQUEST["value"]));
			}
		}
		if($_REQUEST["field"]=='FirstLanguageCode') {
			$_REQUEST["value"]=strtolower($_REQUEST["value"]);
			// check if code already exists
			if(getModuleParameter('Awards', 'FirstLanguageCode')!=$_REQUEST["value"]) {
				// new value so implements the other pieces
				setModuleParameter('Awards', 'Aw-Intro-1', get_text('Award-Intro', 'IOC_Codes', '{$a}', '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Medal-1', get_text('Award-MedalGiver', 'IOC_Codes', '{$a}', '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Plaque-1', get_text('Award-PlaqueGiver', 'IOC_Codes', '{$a}', '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Giver-1', get_text('Award-PremiumGiver', 'IOC_Codes', array('{$a[0]}','{$a[1]}'), '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Giving-1', get_text('Award-PremiumGiving', 'IOC_Codes', array('{$a[0]}','{$a[1]}'), '', '', $_REQUEST["value"]));
				for($n=1; $n<5; $n++) {
					setModuleParameter('Awards', 'Aw-Med'.$n.'-1', get_text('Medal-'.$n, 'IOC_Codes', '', '', '', $_REQUEST["value"]));
				}
				setModuleParameter('Awards', 'Aw-representing-1', get_text('Award-representing', 'IOC_Codes', '{$a}', '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Anthem-1', get_text('Award-Anthem', 'IOC_Codes', '', '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Anthem-TPE-1', get_text('Award-Anthem-TPE', 'IOC_Codes', '', '', '', $_REQUEST["value"]));
				setModuleParameter('Awards', 'Aw-Applause-1', get_text('Award-Applause', 'IOC_Codes', '', '', '', $_REQUEST["value"]));
			}
		}
		setModuleParameter('Awards', $_REQUEST["field"], $_REQUEST["value"]);

	} else {
		$Errore = 1;
	}

	$Answer = '<row>' . $_REQUEST["id"] . '</row>'
		. '<field><![CDATA[' . $_REQUEST["field"] . ']]></field>'
		. '<value><![CDATA[' . ManageHTML($Response) . ']]></value>';
} else {
	$Errore = 1;
}

header('Content-Type: text/xml');
echo '<response>';
echo '<error>' . $Errore . '</error>';
echo $Answer;
echo '</response>';
?>