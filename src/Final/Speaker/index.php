<?php
	require_once( dirname(dirname(dirname(__FILE__))) . '/config.php'  );
	require_once('Common/Fun_Various.inc.php');

	if (defined('hideSpeaker'))
	{
		header('location: /index.php');
		exit;
	}

	function isMobile()
	{
		$regex_match="/(nokia|ipad|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
		$regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
		$regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
		$regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
		$regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
		$regex_match.=")/i";
//		echo '<pre>';
//		print_r(preg_split($regex_match, strtolower($_SERVER['HTTP_USER_AGENT'])));
//		echo '<br />';
//		print_r($_SERVER);
//		echo '</pre><br />WAP '.$_SERVER['HTTP_X_WAP_PROFILE'].'<br />Profile '.$_SERVER['HTTP_PROFILE'].'<br />Agent '.$_SERVER['HTTP_USER_AGENT'].'<br />';
//		exit;
		return isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']) || preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
	}

	CheckTourSession(true);

// tiro fuori i parametri dei timer
	$param=GetParameter('SpkTimer');

	$tmp=explode(';',$param);

	$timers=array();
	$colors=array();

	for ($i=0;$i<count($tmp);++$i)
	{
		list($t,$c)=explode('|',$tmp[$i]);
		if ($t!='#')
			$timers[$i]=$t;
		if ($c!='#')
			$colors[$i]=$c;
	}

	/*print '<pre>';
	print_r($timers);
	print_r($colors);
	print '</pre>';exit;*/

	$isMobile=isMobile() ? 1 : 0;
//print $mobile;exit;
	$array = array(
		'StrOk'=>get_text('CmdOk'),
		'StrEvent'=>get_text('Event'),
		'StrTarget'=>get_text('Target'),
		'StrSetPoints'=>get_text('SetPoints','Tournament'),
		'StrScore'=>get_text('TotalShort','Tournament'),
		'StrStatus'=>get_text('Status','Tournament'),
		'timers'=>$timers,
		'colors'=>$colors,
		'WebDir' => $CFG->ROOT_DIR,
		'StrSchedule'=>get_text('Schedule','Tournament'),
		'StrStopRefresh'=>get_text('StopRefresh','Tournament'),
		'isMobile'=>$isMobile
	);

	$JS_SCRIPT=array(
		'<link href="'.$CFG->ROOT_DIR.'Common/Styles/ext-2.2/css/ext-all.css" media="screen" rel="stylesheet" type="text/css">',
		'<link href="'.$CFG->ROOT_DIR.'Final/Speaker/css/speaker.css" media="screen" rel="stylesheet" type="text/css">',
//		'<link href="'.$CFG->ROOT_DIR.'Common/ext-2.2/ext.ux/css/Ext.ux.ColorField.css" media="screen" rel="stylesheet" type="text/css">',
		phpVars2js($array),
//		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants-exp/firebuglite/firebug-lite-compressed.js"></script>',
//		'<script type="text/javascript">firebug.env.css = "'.$CFG->ROOT_DIR.'Partecipants-exp/firebuglite/firebug-lite.css";</script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/adapter/ext/ext-base.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/ext-all-debug.js"></script>',
		'<script type="text/javascript">Ext.BLANK_IMAGE_URL=\''.$CFG->ROOT_DIR.'Common/Styles/ext-2.2/images/default/s.gif\';</script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/ext.ux/ext.ux.js"></script> ',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/ext.util/ext.util.js"></script>',
//		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/Ext.form/Ext.form.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Speaker/js/speaker.ViewStore.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Speaker/js/speaker.TaskCheck.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Speaker/js/speaker.Filter.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Speaker/js/speaker.View.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Speaker/js/speaker.ViewBBar.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Speaker/js/main.js"></script>',

	);

	//$PAGE_TITLE=get_text('PrintBackNo','BackNumbers');

	include('Common/Templates/head-min.php');

?>
<div id="panel"></div>
<?php
	include('Common/Templates/tail-min.php');
?>