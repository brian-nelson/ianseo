<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META Http-Equiv="Cache-Control" Content="no-cache">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<title><?php print ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '');;?></title>
<!-- <link href="TV_style.css" media="screen" rel="stylesheet" type="text/css"> -->
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo PageEncode ?>">
<script type="text/javascript" src="<?php print $CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js';?>"></script>
<?php
	include 'Tv_style.php';
?>
<style type="text/css">
@font-face {
    font-family: "London2012";
    src: url("<?php echo $CFG->ROOT_DIR; ?>Common/tcpdf/fonts/londo___.ttf");
}
</style>
<script>
// variabile globale che serve per istanziare lo scrollatore
var scroller_interno;
var scroller_esterno;
var altezza;
var elemento;
var next_element;
var tabella;
var tempo_attesa=100;
var aspetta=tempo_attesa; // = scrolltime*100= 4 secondi
var scrolltime=10 // 40 = 40 millisecondi
var scroll_pagina=0;
var i=0; // il div corrente da scrollare!
var j=1;
var alt_tab;
var reload=false;
var loaded=true;
var PixelScroll=4;
var ConHeight;
var ConWidth;
var RotMatches=<?php echo ($RotMatches ? 'true' : 'false'); ?>;

var Quadro=<?php echo $quadro; ?>;
// each box has its own styles/Javascripts

var timeStop=new Array();
var timeScroll=new Array();
var FreshDBContent=new Array();
var TourId=<?php echo intval($_GET['Tour']); ?>;
var RuleId=<?php echo intval($_GET['Rule']); ?>;

var d1 = 0;
var d2 = 0;
var cicli = 0;

function post_init() {
	<?php echo implode('', $JavaScript); ?>
}

</script>
<script type="text/javascript" src="<?php print $CFG->ROOT_DIR.'TV/TVscript.js'; ?>"></script>
</head><body onload="init()" id="body">
<div id="Content">
