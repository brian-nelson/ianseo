<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo PageEncode ?>">
<title><?php

if(!empty($PAGE_TITLE)) echo $PAGE_TITLE . ' - ';
print ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '');?></title>
<link href="<?php echo $CFG->ROOT_DIR ?>Common/Styles/Blue_screen.css" media="screen" rel="stylesheet" type="text/css">
<?php

require_once('Common/Menu.php');

if($_SESSION['debug']) {
	echo '<link href="'.$CFG->ROOT_DIR.'Common/Styles/Blue_screen_debug.css" rel="stylesheet" type="text/css">';
}

if(!empty($JS_SCRIPT)) {
	foreach($JS_SCRIPT as $script) echo "$script\n";
}
?>

<?php if (false) { // true per attivare firebug lite (x ie, safari etc...)?>
	<script type="text/javascript" src="<?php print $CFG->ROOT_DIR.'Partecipants-exp/firebuglite/firebug-lite-compressed.js';?>"></script>
	<script type="text/javascript">firebug.env.css = "<?php print $CFG->ROOT_DIR.'Partecipants-exp/firebuglite/firebug-lite.css';?>"</script>
<?php }?>

</head>
<body<?php echo (!empty($ONLOAD)?$ONLOAD:'') ?>>
