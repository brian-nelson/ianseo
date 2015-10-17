<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo PageEncode ?>">
<title><?php print ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '');;?></title>
<link href="<?php echo $CFG->ROOT_DIR ?>Common/Styles/Blue_screen.css" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo $CFG->ROOT_DIR ?>Common/Styles/Blue_screen-print.css" media="print" rel="stylesheet" type="text/css">
<?php

if(!empty($JS_SCRIPT)) {
	foreach($JS_SCRIPT as $script) echo "$script\n";
}
?>
</head>
<body<?php echo empty($ONLOAD)?' onload="window.focus()"':$ONLOAD ?>>
<div id="PopupContent">
