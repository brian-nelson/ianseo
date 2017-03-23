<?php

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

// trattasi di foglio stile!!
if(!empty($_GET['video'])) {
	$_SESSION['WINHEIGHT']=intval($_GET['video']);
	$_SESSION['WINWIDTH']=intval($_GET['videowidth']);
}

header('Content-type: text/css');

?>#Content {height:<?php echo max(180, $_SESSION['WINHEIGHT']-120); ?>px; }