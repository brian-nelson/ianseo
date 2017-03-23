<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo PageEncode ?>">
<title><?php print ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '');;?></title>
<?php
if(empty($NOSTYLE)) {
	echo '<link href="'.$CFG->ROOT_DIR.'Modules/Caspar/caspar.css" media="screen" rel="stylesheet" type="text/css">';
}

if(empty($JS_SCRIPT)) {
	$JS_SCRIPT=array();
}

array_unshift($JS_SCRIPT,
	'<script src="'.$CFG->ROOT_DIR.'Common/jQuery/jquery-2.1.4.min.js"></script>',
	'<script src="'.$CFG->ROOT_DIR.'Common/jQuery/jquery-ui.min.js"></script>',
	'<script src="'.$CFG->ROOT_DIR.'Common/jQuery/velocity.min.js"></script>',
	'<script src="'.$CFG->ROOT_DIR.'Common/jQuery/jquery.marquee.min.js"></script>'
	);

if(!empty($JS_SCRIPT)) {
	foreach($JS_SCRIPT as $script) echo "$script\n";
}
?>
</head>
<body id="body"<?php echo (!empty($ONLOAD)?$ONLOAD:'') ?>>
