<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo PageEncode ?>">
<title><?php

if(!empty($PAGE_TITLE)) echo $PAGE_TITLE . ' - ';
print ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '');;?></title>
<link href="<?php echo $CFG->ROOT_DIR ?>Common/Styles/Blue_screen.css" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo $CFG->ROOT_DIR ?>Common/Styles/Blue_screen-print.css" media="print" rel="stylesheet" type="text/css">
<link href="<?php echo $CFG->ROOT_DIR ?>Common/Styles/Menu.css" rel="stylesheet" type="text/css">
<?php

if(!empty($IncludeFA)) {
    echo '<link href="'.$CFG->ROOT_DIR.'Common/css/font-awesome.css" rel="stylesheet" type="text/css">';
}

require_once('Common/Menu.php');

if($_SESSION['debug']) {
	echo '<link href="'.$CFG->ROOT_DIR.'Common/Styles/Blue_screen_debug.css" rel="stylesheet" type="text/css">';
}

if(SelectLanguage()=='tlh'){
    echo '<link href="'.$CFG->ROOT_DIR.'Common/Styles/klingon.css" rel="stylesheet" type="text/css">';
}

if(!empty($JS_SCRIPT)) {
	foreach($JS_SCRIPT as $script) echo "$script\n";
}
?>

</head>
<body<?php echo (!empty($ONLOAD)?$ONLOAD:'') ?>>
<div id="TourInfo">
<?php

InfoTournament();

?>
</div>
<div id="navigation">
<?php

// $mid->printMenu('hormenu1');

PrintMenu();

?>
</div>
<div id="Content">
