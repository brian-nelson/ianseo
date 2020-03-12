<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	$TourCode=stripslashes($_REQUEST['Tour']);
	$TourId=getIdFromCode($TourCode);

// uguali ai valori di default nel db
	$TV_Carattere = 30;
	$TV_TR_BGColor = '#FFFFFF';
	$TV_TRNext_BGColor = '#FFFFCC';
	$TV_TR_Color = '#000000';
	$TV_TRNext_Color = '#000000';
	$TV_TH_BGColor = '#CCCCCC';
	$TV_TH_Color = '#000000';
	$TV_THTitle_BGColor = '#585858';
	$TV_THTitle_Color = '#F4F4F4';

	$TV_Content_BGColor = '#FEFEFE';
	$TV_Page_BGColor = '#FFFFFF';

	$Select
		= "SELECT TV_TR_BGColor,TV_TRNext_BGColor,TV_TR_Color,TV_TRNext_Color,TV_Content_BGColor,TV_Page_BGColor,TV_Carattere, "
		. "TV_TH_BGColor,TV_TH_Color,TV_THTitle_BGColor,TV_THTitle_Color "
		. "FROM TVRules "
		. "WHERE TVRId=" . StrSafe_DB($_REQUEST['Rule']) . " and TVRTournament=$TourId";
	$Rs=safe_r_sql($Select);
	//$RULE=safe_fetch($Rs);

	if ($MyRow=safe_fetch($Rs)) {
		$TV_Carattere=$MyRow->TV_Carattere;
		$TV_TR_BGColor=$MyRow->TV_TR_BGColor;
		$TV_TRNext_BGColor=$MyRow->TV_TRNext_BGColor;
		$TV_TR_Color=$MyRow->TV_TR_Color;
		$TV_TRNext_Color=$MyRow->TV_TRNext_Color;
		$TV_TH_BGColor = $MyRow->TV_TH_BGColor;
		$TV_TH_Color = $MyRow->TV_TH_Color;
		$TV_THTitle_BGColor = $MyRow->TV_THTitle_BGColor;
		$TV_THTitle_Color = $MyRow->TV_THTitle_Color;

		$TV_Content_BGColor=$MyRow->TV_Content_BGColor;
		$TV_Page_BGColor=$MyRow->TV_Page_BGColor;
	}
?>
<style id="TvStyles" type="text/css">
<!--
body {
	font-size: <?php print $TV_Carattere;?>px;
	font-family: <?php if(!empty($_SESSION['OlympicFont-use'])) echo '"'.$_SESSION['OlympicFont'].'", '; ?>  Verdana, Helvetica, Arial, sans-serif;
	color: #000000;
	margin: 0px;
	padding: 3px;
	background-color: <?php print $TV_Page_BGColor;?>;
	padding-right:20px;
}
#Header
{
	font-size: 10px;
	font-family: Verdana, Helvetica, Arial, sans-serif;
	color: #FFFFFF;
	font-weight: bold;
	width: 900px;
	height: 32px;
	margin-left: auto;
	margin-right: auto;
	background-color: #585858;
	background-attachment: fixed;
	text-align: left;
	letter-spacing: 0.5em;

}
#Content {
	/*width: 890px;*/
	/* width: 98%; */
	margin-left: auto;
	margin-right: auto;
/*	padding-left: 5px;
	padding-top: 5px;
	padding-right: 5px;
	padding-top: 5px;
	padding-bottom: 5px;*/
	background-color: <?php print $TV_Content_BGColor;?>;
}
#Footer {
	height: auto;
	width: 900px;
	margin-left: auto;
	margin-right: auto;
	background-color: #585858;
	font-size: 10px;
	font-family: Verdana, Helvetica, Arial, sans-serif;
	color: #FFFFFF;
	font-weight: bold;
	text-align: center;
}

table {table-layout:fixed;width:100%; font-size: <?php print $TV_Carattere;?>px; background-color:#dddddd; border: 2px; border-color:#dddddd; border-collapse:collapse; border-spacing:1px; empty-cells:hide;}
table tr { background-color:<?php print $TV_TR_BGColor;?>; color: <?php print $TV_TR_Color;?>; height:30px;}
table tr.Next { background-color:<?php print $TV_TRNext_BGColor;?>; color: <?php print $TV_TRNext_Color;?>; height:30px;}

table th {border:1px solid #dddddd; background-color:<?php print $TV_TH_BGColor;?>; text-align:center; padding-left:2px; padding-right:2px; font-weight:bold; color: <?php print $TV_TH_Color;?>; font-size:<?php print ($TV_Carattere ? max(12, $TV_Carattere*.8) : 12); ?>px;}
table th.Title {<?php if(!empty($_SESSION['OlympicFont-use'])) echo 'font-family: "'.$_SESSION['OlympicFont'].'",  Verdana, Helvetica, Arial, sans-serif; '; ?> background-color:<?php print $TV_THTitle_BGColor;?>;  overflow: hidden; white-space: nowrap;text-align:center; padding-left:2px; padding-right:2px; font-size:12px;  color: <?php print $TV_THTitle_Color;?>; font-size:120%}

table td {border:1px dotted #dddddd; padding:2px; overflow: hidden; white-space: normal;}
table td.NumberAlign { text-align:right; overflow: visible; white-space: nowrap;}
table td.Center { text-align:center; overflow: hidden; white-space: nowrap;}
table td.Grassetto { font-size:125%; overflow: hidden; white-space: nowrap;}

div.blocco {margin:0px 0px 15px;}
div.blocco#scrolltopEnd {margin:0}
.piccolo  { font-size:80%;}
.small  { font-size:15px;}
.Link {font-weight:bold; color: #666666; text-decoration:none;}
.Link:hover {text-decoration:underline;}

div#content {height:<?php echo $_SESSION['WINHEIGHT']-6 ?>px; overflow:hidden; }
div.MM, div.MM0, table.MM { height:<?php echo $_SESSION['WINHEIGHT']-6 ?>px; overflow:hidden; }
table.DB {}

table tr.bg, tr.bg td {background-color:#dddddd; height:10px; font-size:5px; }
table td.top {border-top:1px black solid;}
table td.right {border-right:1px black solid; }
table td.bottom {border-bottom:1px black solid; }
table td.left {border-left:1px black solid; }

table td, table th {overflow:hidden; white-space:nowrap;}

.flagphoto img {border:1px solid gray; }

<?php

$sql="select distinct TrRecType, TrRecCode, TrColor from TourRecords where TrTournament=$TourId  "; // for now we only do on totals
$q=safe_r_sql($sql);
while($r=safe_fetch($q)) {
	echo '.Record_'.$r->TrRecType.'_'. $r->TrRecCode.' th,
	 .Record_'.$r->TrRecType.'_'. $r->TrRecCode.' td {' . (!empty($_SESSION['OlympicFont-use']) && $r->TrRecType=='OR' ? 'font-family: "'.$_SESSION['OlympicFont'].'",  Verdana, Helvetica, Arial, sans-serif; ' : '') . ' background-color:#'.$r->TrColor.'; color:#ffffff; font-weight:bold;}';
	echo '.Record_'.$r->TrRecType.'_'. $r->TrRecCode.' th {text-align:right; font-size:125%; }';
	echo 'table td.Rec-'.$r->TrRecType.', table th.Rec-'.$r->TrRecType.' {color:#'.$r->TrColor.';font-weight:bold;}';
	echo 'table td.Rec-Bg-'.$r->TrRecType.', table th.Rec-Bg-'.$r->TrRecType.' {background-color:#'.$r->TrColor.';}';
}


foreach($Styles as $quad=>$style) {
	if(!empty($style['TV_Content_BGColor'])) echo "#scrolltop$quad {background-color: {$style['TV_Content_BGColor']}; }\n";
	if(!empty($style['TV_Carattere'])) {
		echo "#scrolltop$quad table {font-size: {$style['TV_Carattere']}px; }\n";
		echo "#scrolltop$quad table tr { background-color:{$style['TV_TR_BGColor']}; color: {$style['TV_TR_Color']}; }\n";
		echo "#scrolltop$quad table tr.Next { background-color:{$style['TV_TRNext_BGColor']}; color: {$style['TV_TRNext_Color']};}\n";
		echo "#scrolltop$quad table th { background-color:{$style['TV_TH_BGColor']}; color: {$style['TV_TH_Color']};}\n";
		echo "#scrolltop$quad table th.Title { background-color:{$style['TV_THTitle_BGColor']}; color: {$style['TV_THTitle_Color']};}";
	}
}
?>

-->
</style>