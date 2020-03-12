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
// 	include 'Tv_style.php';
?>
<style type="text/css">
@font-face {
    font-family: "London2012";
    src: url("<?php echo $CFG->ROOT_DIR; ?>Common/tcpdf/fonts/londo___.ttf");
}

canvas {border:1px solid red;width:100%;height:100%}
</style>
<script>
// variabile globale che serve per istanziare lo scrollatore
var TourId=<?php echo getIdFromCode($_GET['Tour']); ?>;
var RuleId=<?php echo intval($_GET['Rule']); ?>;
</script>
<script type="text/javascript" src="<?php print $CFG->ROOT_DIR.'TV/TVscript5.js'; ?>"></script>
</head><body onload="startShow()" id="body">
<div id="DivDebug"></div>
<canvas id="canvas">
