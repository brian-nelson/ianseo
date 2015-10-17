<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META Http-Equiv="Cache-Control" Content="no-cache">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<title><?php print ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '');;?></title>
<!-- <link href="TV_style.css" media="screen" rel="stylesheet" type="text/css"> -->
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo PageEncode ?>">
<?php
	include 'Tv_style.php';
?>
<script>
// variabile globale che serve per istanziare lo scrollatore
var scroller;
var altezza;
var StartingPoint=0;
var PixelScroll=2;
var ConHeight;
var ConWidth;
var TimeToWait=500;
var TimeCounter=TimeToWait;
var NextRule='<?php echo $NextRule; ?>';

</script>
<script type="text/javascript" src="<?php print $CFG->ROOT_DIR.'TV/TVscriptlight.js'; ?>"></script>
</head>
<body onload="init()" id="body">
<div id="Content">
